<?php
/*
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC. All rights reserved.                        |
 |                                                                    |
 | This work is published under the GNU AGPLv3 license with some      |
 | permitted exceptions and without any warranty. For full license    |
 | and copyright information, see https://civicrm.org/licensing       |
 +--------------------------------------------------------------------+
 */
namespace Civi\FlexMailer\Listener;

use Civi\FlexMailer\Event\ComposeBatchEvent;
use Civi\FlexMailer\Event\RunEvent;
use Civi\FlexMailer\FlexMailerTask;
use Civi\Token\TokenProcessor;
use Civi\Token\TokenRow;

/**
 * Class DefaultComposer
 * @package Civi\FlexMailer\Listener
 *
 * The DefaultComposer uses a TokenProcessor to generate all messages as
 * a batch.
 */
class SmartyComposer extends DefaultComposer {

  public function onRun(RunEvent $e) {
    \CRM_Core_Smarty::registerStringResource();
  }

  /**
   * Determine whether this composer knows how to handle this mailing.
   *
   * @param \CRM_Mailing_DAO_Mailing $mailing
   * @return bool
   */
  public function isSupported(\CRM_Mailing_DAO_Mailing $mailing) {
    return TRUE;
  }

  /**
   * Given a mailing and a batch of recipients, prepare
   * the individual messages (headers and body) for each.
   *
   * @param \Civi\FlexMailer\Event\ComposeBatchEvent $e
   */
  public function onCompose(ComposeBatchEvent $e) {
    if (!$this->isActive() || !$this->isSupported($e->getMailing())) {
      return;
    }

    $tpls = $this->createMessageTemplates($e);
    $literals = [
      '<style type="text/css">' => '<style type="text/css">{literal}',
      '</style>' => '{/literal}</style>',
    ];
    $tpls['html'] = str_ireplace(array_keys($literals), array_values($literals), $tpls['html']);

    $tp = new TokenProcessor(\Civi::service('dispatcher'),
      $this->createTokenProcessorContext($e));

    $tp->addMessage('subject', $tpls['subject'], 'text/plain');
    $tp->addMessage('body_text', isset($tpls['text']) ? $tpls['text'] : '',
      'text/plain');
    $tp->addMessage('body_html', isset($tpls['html']) ? $tpls['html'] : '',
      'text/html');

    $hasContent = FALSE;
    foreach ($e->getTasks() as $key => $task) {
      /** @var \Civi\FlexMailer\FlexMailerTask $task */
      if (!$task->hasContent()) {
        $tp->addRow()->context($this->createTokenRowContext($e, $task));
        $hasContent = TRUE;
      }
    }

    if (!$hasContent) {
      return;
    }

    foreach ($tp->getRows() as $row) {
      /** @var \Civi\Token\TokenRow $row */
      /** @var \Civi\FlexMailer\FlexMailerTask $task */
      $task = $row->context['flexMailerTask'];

      // Configure Smarty and the token context with the contact information
      $smarty = \CRM_Core_Smarty::singleton();
      $smarty->assign_by_ref('contact', $row->context['contact']);

      $task->setMailParams(array_merge(
        $this->createMailParams($e, $task, $row),
        $task->getMailParams()
      ));
    }

    $tp->evaluate();
  }

  /**
   * Define the contextual parameters for the token-processor.
   *
   * @param \Civi\FlexMailer\Event\ComposeBatchEvent $e
   * @return array
   */
  public function createTokenProcessorContext(ComposeBatchEvent $e) {
    $context = [
      'controller' => get_class($this),
      'mailing' => $e->getMailing(),
      'mailingId' => $e->getMailing()->id,
      'smarty' => TRUE,
    ];
    return $context;
  }

  /**
   * Create contextual data for a message recipient.
   *
   * @param \Civi\FlexMailer\Event\ComposeBatchEvent $e
   * @param \Civi\FlexMailer\FlexMailerTask $task
   * @return array
   *   Contextual data describing the recipient.
   *   Typical values are `contactId` or `mailingJobId`.
   */
  public function createTokenRowContext(
    ComposeBatchEvent $e,
    FlexMailerTask $task
  ) {
    $contact = \Civi\Api4\Contact::get(FALSE)->addWhere('id', '=', $task->getContactId())->execute()->first();
    // @fixme: This is required because TokenCompatSubscriber::onRender expects $context['contact']['contact_id'] to exist
    //   and in API4 it uses ArrayAccess which will error if the offset does not exist.
    //   See https://github.com/civicrm/civicrm-core/pull/18845
    $contact['contact_id'] = $contact['id'];
    return array(
      'contactId' => $task->getContactId(),
      'contact' => $contact,
      'mailingJobId' => $e->getJob()->id,
      'mailingActionTarget' => array(
        'id' => $task->getEventQueueId(),
        'hash' => $task->getHash(),
        'email' => $task->getAddress(),
      ),
      'flexMailerTask' => $task,
    );
  }

  /**
   * For a given task, prepare the mailing.
   *
   * @param \Civi\FlexMailer\Event\ComposeBatchEvent $e
   * @param \Civi\FlexMailer\FlexMailerTask $task
   * @param \Civi\Token\TokenRow $row
   * @return array
   *   A list of email parameters, such as "Subject", "text", and/or "html".
   * @see \CRM_Utils_Hook::alterMailParams
   */
  public function createMailParams(
    \Civi\FlexMailer\Event\ComposeBatchEvent $e,
    \Civi\FlexMailer\FlexMailerTask $task,
    \Civi\Token\TokenRow $row
  ) {
    $mailParams = parent::createMailParams($e, $task, $row);
    $mailParams['X-CiviMail-Mosaico'] = 'Yes';
    return $mailParams;
  }

  /**
   * Generate the message templates for use with token-processor.
   *
   * @param \Civi\FlexMailer\Event\ComposeBatchEvent $e
   * @return array
   *   A list of templates. Some combination of:
   *     - subject: string
   *     - html: string
   *     - text: string
   */
  public function createMessageTemplates(
    \Civi\FlexMailer\Event\ComposeBatchEvent $e
  ) {
    // Currently building on the BAO's behavior for reconciling
    // HTML/text and header/body/footer.
    $templates = $e->getMailing()->getTemplates();
    \_mosaico_civicrm_alterMailContent($templates);
    if ($this->isClickTracking($e)) {
      $templates = $this->applyClickTracking($e, $templates);
    }
    return $templates;
  }

}
