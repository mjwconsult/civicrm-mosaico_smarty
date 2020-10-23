<?php
namespace Civi\Token;

use Civi\Token\Event\TokenRenderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use \Civi\FlexMailer\FlexMailer;

/**
 * Class SmartyTokenSubscriber
 * @package Civi\Token
 *
 */
class SmartyTokenSubscriber implements EventSubscriberInterface {

  /**
   * @inheritDoc
   */
  public static function getSubscribedEvents() {
    return [
      'civi.token.render' => ['onRender', FlexMailer::WEIGHT_ALTER],
    ];
  }

  /**
   * Apply the various CRM_Utils_Token helpers.
   *
   * @param TokenRenderEvent $e
   */
  public function onRender(TokenRenderEvent $e) {
    $smarty = \CRM_Core_Smarty::singleton();
    $smarty->assign_by_ref('contact', $e->row->context['contact']);
    $e->string = $smarty->fetch("string:" . $e->string);
  }

}
