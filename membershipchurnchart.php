<?php

require_once 'membershipchurnchart.civix.php';

use CRM_Membershipchurnchart_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function membershipchurnchart_civicrm_config(&$config) {
  _membershipchurnchart_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function membershipchurnchart_civicrm_install() {
  _membershipchurnchart_civix_civicrm_install();
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
 * Implements hook_civicrm_alterLogTables().
 *
 * Exclude tables from logging tables since they hold data that can be regenerated automatically.
 */
function membershipchurnchart_civicrm_alterLogTables(&$logTableSpec) {
  unset($logTableSpec['civicrm_membership_churn_table']);
  unset($logTableSpec['civicrm_membership_churn_monthly_table']);
}

/**
 * Adds a navigation menu item under report.
 */
function membershipchurnchart_civicrm_navigationMenu(&$menu) {
  _membershipchurnchart_civix_insert_navigation_menu($menu, 'Memberships', [
    'label' => E::ts('Membership Churn Chart'),
    'name' => 'membershipchurnchart',
    'url' => 'civicrm/membership/membershipchurnchart',
    'permission' => 'access CiviReport',
    'operator' => NULL,
    'separator' => FALSE,
  ]);
  _membershipchurnchart_civix_navigationMenu($menu);
}

function membershipchurnchart_civicrm_pageRun(&$page) {
  $sPageName = $page->getVar('_name');
  if ($sPageName == "CRM_Membershipchurnchart_Page_MembershipChurnChart") {
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
