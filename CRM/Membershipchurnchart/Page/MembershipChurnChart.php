<?php

require_once 'CRM/Core/Page.php';

class CRM_Membershipchurnchart_Page_MembershipChurnChart extends CRM_Core_Page {
  function run() {

  	$chartData = array();

    // Get churn chart data
    $sql = "SELECT * FROM membership_churn_monthly_table ORDER BY year, month";
    $sqlRes = CRM_Core_DAO::executeQuery($sql);

    $totalStats = $years = array();
    while($sqlRes->fetch()) {

      $years[$sqlRes->year] = $sqlRes->year;

      $data = array();
      $data['date'] = $sqlRes->month_year;
      $data['year'] = $sqlRes->year;
      $data['month'] = $sqlRes->month;
      $data['membership_type_id'] = $sqlRes->membership_type_id;
      $data['Current'] = $sqlRes->current;
      $data['Joined'] = $sqlRes->joined;
      $data['Resigned'] = $sqlRes->resigned;
      $data['Rejoined'] = $sqlRes->rejoined;
      $data['Brought_Forward'] = $data['Brought Forward'] = $sqlRes->brought_forward;
      $data['Churn'] = $sqlRes->churn;

      $chartData[$sqlRes->year][$sqlRes->membership_type_id][] = $data;
    }

    // Get membership churn chart settings
    $settingsStr = CRM_Core_BAO_Setting::getItem('CiviCRM Membershipchurnchart Settings', 'membershipchurnchart_settings');
    $settingsArray = unserialize($settingsStr);
    $startYear = $currentYear = date('Y'); // Current
    // Check if start date is set in settings page
    if (!empty($settingsArray['start_year'])) {
      $startYear = $settingsArray['start_year'];
    }

    // Start year filters
    $startYearOptions = $endYearOptions = NULL;
    foreach($years as $year) {
      if ($year == $startYear) {
        $startYearOptions .= "<option value=\"{$year}\" selected='selected'>{$year}</option>";
      } else {
        $startYearOptions .= "<option value=\"{$year}\">{$year}</option>";
      }
    }
    // End year filters
    foreach($years as $year) {
      if ($year == $currentYear) {
        $endYearOptions .= "<option value=\"{$year}\" selected='selected'>{$year}</option>";
      } else {
        $endYearOptions .= "<option value=\"{$year}\">{$year}</option>";
      }
    }
    $this->assign('startYearRange', $startYearOptions);
    $this->assign('endYearRange', $endYearOptions);

    // Membership types filter
    $memTypes = CRM_Membershipchurnchart_Utils::getAllmembershipTypes();
    $this->assign('memTypes', $memTypes);

    // All status to be displayed as legends
    $allStatuses = CRM_Membershipchurnchart_Utils::getAllMemberStatusesForChart();
    $allStatuses = json_encode(array_values($allStatuses));
    $this->assign('allStatuses', $allStatuses);

    // Chart data
    $chartData = json_encode($chartData);
  	$this->assign('chartData', $chartData);
    $this->assign('currentYear', $currentYear);

    // Minimum churn data
    $minChurn = CRM_Membershipchurnchart_Utils::getMinChurnValuesForYaxis($chartData);
    $jMinChurn = json_encode($minChurn, JSON_HEX_QUOT);
    $this->assign('minChurn', $jMinChurn);

    parent::run();
  }
}
