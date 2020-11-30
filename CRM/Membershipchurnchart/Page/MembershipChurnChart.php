<?php

require_once 'CRM/Core/Page.php';

class CRM_Membershipchurnchart_Page_MembershipChurnChart extends CRM_Core_Page {

  function run() {
    CRM_Core_Session::singleton()->replaceUserContext(CRM_Utils_System::url('civicrm/membership/membershipchurnchart', "reset=1"));

    $chartData = [];

    // Get churn chart data
    $sql = "SELECT * FROM civicrm_membership_churn_monthly_table ORDER BY year, month";
    $sqlRes = CRM_Core_DAO::executeQuery($sql);

    $years = [];
    while($sqlRes->fetch()) {
      $years[$sqlRes->year] = $sqlRes->year;

      $data = [];
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
    $startYear = (int) \Civi::settings()->get('membershipchurnchart_startyear');
    $currentYear = date('Y');
    if (empty($startYear)) {
      $startYear = $currentYear;
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
    $memTypes = CRM_Member_PseudoConstant::membershipType();
    $this->assign('memTypes', $memTypes);

    // All status to be displayed as legends
    $allStatuses = CRM_Membershipchurnchart_Utils::getAllMemberStatusesForChart();
    $allStatuses = json_encode(array_values($allStatuses));
    $this->assign('allStatuses', $allStatuses);

    // Minimum churn data
    $minChurn = CRM_Membershipchurnchart_Utils::getMinChurnValuesForYaxis($chartData);
    $jMinChurn = json_encode($minChurn, JSON_HEX_QUOT);
    $this->assign('minChurn', $jMinChurn);

    // Chart data
    $chartData = json_encode($chartData);
    $this->assign('chartData', $chartData);
    $this->assign('currentYear', $currentYear);

    parent::run();
  }
}
