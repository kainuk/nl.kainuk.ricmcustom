<?php

require_once 'ricmcustom.civix.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function ricmcustom_civicrm_config(&$config) {
  _ricmcustom_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function ricmcustom_civicrm_xmlMenu(&$files) {
  _ricmcustom_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function ricmcustom_civicrm_install() {
  _ricmcustom_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function ricmcustom_civicrm_postInstall() {
  _ricmcustom_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function ricmcustom_civicrm_uninstall() {
  _ricmcustom_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function ricmcustom_civicrm_enable() {
  _ricmcustom_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function ricmcustom_civicrm_disable() {
  _ricmcustom_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function ricmcustom_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _ricmcustom_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function ricmcustom_civicrm_managed(&$entities) {
  _ricmcustom_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function ricmcustom_civicrm_caseTypes(&$caseTypes) {
  _ricmcustom_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function ricmcustom_civicrm_angularModules(&$angularModules) {
  _ricmcustom_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function ricmcustom_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _ricmcustom_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function ricmcustom_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function ricmcustom_civicrm_navigationMenu(&$menu) {
  _ricmcustom_civix_insert_navigation_menu($menu, NULL, array(
    'label' => ts('The Page', array('domain' => 'nl.kainuk.ricmcustom')),
    'name' => 'the_page',
    'url' => 'civicrm/the-page',
    'permission' => 'access CiviReport,access CiviContribute',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _ricmcustom_civix_navigationMenu($menu);
} // */

function ricmcustom_civicrm_tokens(&$tokens) {
  $tokens['ricm'] = array(
    'ricm.fee'  => 'Deelname kosten',
    'ricm.paid' => 'Deelname reeds betaald',
    'ricm.due'  => 'Deelname nog te betalen',
    'ricm.roompreference' => 'Deelname kamervoorkeur',
    'ricm.extras' => 'Deelname extras',
    'ricm.children' => 'Deelname - kinderen',
    'ricm.childrendetails' => 'Deelname - kinderen - details',
    'ricm.team' => 'Deelname - aanmelding bij het team',
    'ricm.language' => 'Deelname - taal kleine groep',
    'ricm.diet' => 'Deelname - Dieet',
    'ricm.remarks' => 'Deelname - opmerkingen',
  );
}

function ricmcustom_civicrm_tokenValues(&$values, $cids, $job = NULL, $tokens = [], $context = NULL) {
  // Date tokens
  if (!empty($tokens['ricm'])) {
    foreach ($cids as $cid) {
      $tokens = new CRM_Ricmcustom_ParticipantTokens($cid);
      $values[$cid] = array_merge( $values[$cid],$tokens->tokenValues());
    }
  }
}

/*
    CiviCrm hook, zorgt ervoor dat de event_offline_receipt email naar een
    extra email adres wordt gestuurd.
*/

function ricmcustom_civicrm_alterMailParams(&$params, $context) {
  if (isset($params['valueName']) && $params['valueName'] == 'event_offline_receipt') {
    $params['bcc'] = variable_get('ricm_bcc_email', 'info@kainuk.nl');
  }
}