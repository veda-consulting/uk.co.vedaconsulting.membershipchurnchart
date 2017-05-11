<?php

require_once 'membershipchurnchart.civix.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function membershipchurnchart_civicrm_config(&$config) {
  _membershipchurnchart_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function membershipchurnchart_civicrm_xmlMenu(&$files) {
  _membershipchurnchart_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function membershipchurnchart_civicrm_install() {
  _membershipchurnchart_civix_civicrm_install();

  // Create scheduled job whuch prepares churn data
  CRM_Membershipchurnchart_Utils::createScheduledJob(1);
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function membershipchurnchart_civicrm_uninstall() {
  _membershipchurnchart_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function membershipchurnchart_civicrm_enable() {
  _membershipchurnchart_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function membershipchurnchart_civicrm_disable() {
  _membershipchurnchart_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed
 *   Based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function membershipchurnchart_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _membershipchurnchart_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function membershipchurnchart_civicrm_managed(&$entities) {
  _membershipchurnchart_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function membershipchurnchart_civicrm_caseTypes(&$caseTypes) {
  _membershipchurnchart_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function membershipchurnchart_civicrm_angularModules(&$angularModules) {
  _membershipchurnchart_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function membershipchurnchart_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _membershipchurnchart_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Adds a navigation menu item under report.
 */
function membershipchurnchart_civicrm_navigationMenu(&$params ) {
  // get the id of Membership Menu
  $membershipMenuId = CRM_Core_DAO::getFieldValue('CRM_Core_BAO_Navigation', 'Memberships', 'id', 'name');

  // skip adding menu if there is no administer menu
  if ($membershipMenuId) {
    // get the maximum key under adminster menu
    $maxKey = max( array_keys($params[$membershipMenuId]['child']));
    $params[$membershipMenuId]['child'][$maxKey+1] =  array (
       'attributes' => array (
        'label'      => 'Membership Churn Chart',
        'name'       => 'Membershipchurnchart',
        'url'        => 'civicrm/membership/membershipchurnchart',
        'permission' => 'access CiviReport',
        'operator'   => NULL,
        'separator'  => TRUE,
        'parentID'   => $membershipMenuId,
        'navID'      => $maxKey+1,
        'active'     => 1
      )
    );
  }
}

function membershipchurnchart_civicrm_pageRun( &$page ){
  $sPageName = $page->getVar('_name') ;
  if($sPageName == "CRM_Membershipchurnchart_Page_MembershipChurnChart"){
    CRM_Core_Resources::singleton()
    ->addScriptFile('uk.co.vedaconsulting.membershipchurnchart', 'js/d3.v3.js', 110, 'html-header', FALSE)   
    ->addScriptFile('uk.co.vedaconsulting.membershipchurnchart', 'js/dc/dc.js', 110, 'html-header', FALSE)
    ->addScriptFile('uk.co.vedaconsulting.membershipchurnchart', 'js/dc/crossfilter.js', 110, 'html-header', FALSE)
    ->addScriptFile('uk.co.vedaconsulting.membershipchurnchart', 'js/bootstrap.min.js', 110, 'html-header', FALSE)
    ->addScriptFile('uk.co.vedaconsulting.membershipchurnchart', 'js/bootstrap-dialog.min.js', 110, 'html-header', FALSE)
    ->addStyleFile('uk.co.vedaconsulting.membershipchurnchart', 'js/dc/dc.css')
    ->addStyleFile('uk.co.vedaconsulting.membershipchurnchart', 'css/ChurnCharts.css', 110, 'page-header')
    ->addStyleFile('uk.co.vedaconsulting.membershipchurnchart', 'css/bootstrap.css', 110, 'page-header')
    ->addStyleFile('uk.co.vedaconsulting.membershipchurnchart', 'css/sb-admin.css', 110, 'page-header')
    ->addStyleFile('uk.co.vedaconsulting.membershipchurnchart', 'css/font-awesome/css/font-awesome.css', 110, 'page-header');
  }
}
