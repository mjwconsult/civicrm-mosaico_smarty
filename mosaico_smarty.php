<?php

require_once 'mosaico_smarty.civix.php';
use CRM_MosaicoSmarty_ExtensionUtil as E;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function mosaico_smarty_civicrm_config(&$config) {
  if (isset(Civi::$statics[__FUNCTION__])) { return; }
  Civi::$statics[__FUNCTION__] = 1;

  _mosaico_smarty_civix_civicrm_config($config);

  // Add listeners for CiviCRM hooks that might need altering by other scripts
  Civi::dispatcher()->addListener('civi.flexmailer.run', 'mosaico_smarty_symfony_civicrm_flexmailer_run');
  Civi::dispatcher()->addListener('civi.flexmailer.compose', 'mosaico_smarty_symfony_civicrm_flexmailer_compose', \Civi\FlexMailer\FlexMailer::WEIGHT_PREPARE);
}

/**
 * Implements hook_civicrm_container().
 */
function mosaico_smarty_civicrm_container(ContainerBuilder $container) {
  $container->removeDefinition('civi_flexmailer_default_composer');
  $container->removeDefinition('mosaico_flexmail_composer');
  $container->setDefinition('civi_flexmailer_default_composer', new Definition('Civi\FlexMailer\Listener\SmartyComposer'))->setPublic(TRUE);
  $container->setDefinition('mosaico_flexmail_composer', new Definition('Civi\FlexMailer\Listener\SmartyComposer'))->setPublic(TRUE);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function mosaico_smarty_civicrm_xmlMenu(&$files) {
  _mosaico_smarty_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function mosaico_smarty_civicrm_install() {
  _mosaico_smarty_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function mosaico_smarty_civicrm_postInstall() {
  _mosaico_smarty_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function mosaico_smarty_civicrm_uninstall() {
  _mosaico_smarty_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function mosaico_smarty_civicrm_enable() {
  _mosaico_smarty_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function mosaico_smarty_civicrm_disable() {
  _mosaico_smarty_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function mosaico_smarty_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _mosaico_smarty_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function mosaico_smarty_civicrm_managed(&$entities) {
  _mosaico_smarty_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterSettingsFolders
 */
function mosaico_smarty_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _mosaico_smarty_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function mosaico_smarty_civicrm_entityTypes(&$entityTypes) {
  _mosaico_smarty_civix_civicrm_entityTypes($entityTypes);
}
