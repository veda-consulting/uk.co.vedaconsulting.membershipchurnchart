<?php

require_once 'CRM/Core/Page.php';

class CRM_Membershipchurnchart_Utils {
  /**
   * CiviCRM API wrapper
   *
   * @param string $entity
   * @param string $action
   * @param string $params
   *
   * @return array of API results
   */
  public static function CiviCRMAPIWrapper($entity, $action, $params) {

    if (empty($entity) || empty($action) || empty($params)) {
      return;
    }

    try {
      $result = civicrm_api3($entity, $action, $params);
    }
    catch (Exception $e) {
      CRM_Core_Error::debug_log_message('CiviCRM API Call Failed');
      CRM_Core_Error::debug_var('CiviCRM API Call Error', $e);
      return;
    }

    return $result;
  }

  /**
   * Function to prepare churn table data
   */
  public static function prepareChurnTable() {

    // Truncate membership churn data table
    CRM_Core_DAO::executeQuery("TRUNCATE TABLE `membership_churn_table`");

    // Get membership churn chart settings
    $settingsStr = CRM_Core_BAO_Setting::getItem('CiviCRM Membershipchurnchart Settings', 'membershipchurnchart_settings');
    $settingsArray = unserialize($settingsStr);
    // Check if start date is set in settings page
    if (!empty($settingsArray['start_year'])) {
      $start_year = $settingsArray['start_year'];
    } else {
      // Start year not set
      // So get min and max year from civicrm_membership table
      $sql = "SELECT YEAR(MIN(join_date)) as min_join_date FROM civicrm_membership";
      $sqlRes = CRM_Core_DAO::executeQuery($sql);
      $sqlRes->fetch();
      $start_year = $sqlRes->min_join_date;
    }
    
    $end_year = date('Y'); // Current year
    $end_month = date('n'); // Current month

    // Get data for one month before the start year, to get the brought forward value
    $startMonthYear = "{$start_year}-01";
    $previous_month = date("m", strtotime($startMonthYear. " -1 months"));
    $previous_year = date("Y", strtotime($startMonthYear. " -1 months"));
    self::insertDataIntoChurnTable($previous_year, $previous_month);

    for ($i = $start_year; $i <= $end_year; $i++) {
      for ($j = 1; $j <= 12; $j++) { 
        self::insertDataIntoChurnTable($i, $j);
      }
    }

    // Delete all stats which are greater than and equal to current year/month
    $deleteSql = "DELETE FROM membership_churn_table WHERE year >= %1 AND month >= %2";
    $deleteParams = array(
      1 => array($end_year, 'Integer'), 
      2 => array($end_month, 'Integer'),
    );
    CRM_Core_DAO::executeQuery($deleteSql, $deleteParams);

    // Truncate membership churn monthly data table
    CRM_Core_DAO::executeQuery("TRUNCATE TABLE `membership_churn_monthly_table`");

    $monthlyDataSql = "INSERT INTO membership_churn_monthly_table (year, month, membership_type_id, 
    current, joined, resigned, rejoined)
    SELECT year, month, membership_type_id, 
    count(current) as current, 
    count(joined) as joined, 
    count(resigned) as resigned,
    count(rejoined) as rejoined
    FROM membership_churn_table GROUP BY membership_type_id, year, month ORDER BY year, month
    ";
    CRM_Core_DAO::executeQuery($monthlyDataSql);
  
    $allMonthlyData = "SELECT * FROM membership_churn_monthly_table";
    $allMonthlyDataRes = CRM_Core_DAO::executeQuery($allMonthlyData);
    while($allMonthlyDataRes->fetch()) {

      // Get month name and year
      $monthName = date('M', mktime(0, 0, 0, $allMonthlyDataRes->month, 10));
      $month_year = $monthName.'/'.$allMonthlyDataRes->year;

      $month = $allMonthlyDataRes->month;
      $year = $allMonthlyDataRes->year;
      $month = sprintf('%02d', $month);
      $current_month = "{$year}-{$month}";
      $previous_month = date("m", strtotime($current_month. " -1 months"));
      $previous_year = date("Y", strtotime($current_month. " -1 months"));

      // Get data for previous period
      $broughtForwardSql = "SELECT * FROM membership_churn_monthly_table WHERE year = %1 AND month = %2 AND membership_type_id = %3";
      $broughtForwardParams = array(
        1 => array($previous_year, 'Integer'), 
        2 => array($previous_month, 'Integer'),
        3 => array($allMonthlyDataRes->membership_type_id, 'Integer'),
      );
      $broughtForwardRes = CRM_Core_DAO::executeQuery($broughtForwardSql, $broughtForwardParams);
      $broughtForward = $churn = 0;
      if ($broughtForwardRes->fetch()) {
        // Calculate BF
        // BF = (Joined + Rejoined + Current) - Resigned
        $broughtForward = ($broughtForwardRes->joined + $broughtForwardRes->rejoined + $broughtForwardRes->current) - $broughtForwardRes->resigned;
      }

      // Calculate Churn
      // Churn = (Joined + Rejoined - Resigned) / BroughtForward
      //$churn = ($allMonthlyDataRes->joined + $allMonthlyDataRes->rejoined - $allMonthlyDataRes->resigned) / $broughtForward;

      //$churn = number_format($churn, 2);

      //to View result in reversal process, multiplying value with -1
      //{calc} * -1
      //so churn chart should reverse
      //negatives should become positive
      //$churn = $churn * -1;

      $monthDataUpdateSql = "UPDATE membership_churn_monthly_table SET 
      brought_forward = %1, churn = %2 ,month_year = %3
      WHERE id = %4";
      $monthDataUpdateParams = array(
        1 => array($broughtForward, 'Integer'), 
        2 => array($churn, 'String'), 
        3 => array($month_year, 'String'),
        4 => array($allMonthlyDataRes->id, 'Integer'),
      );
      CRM_Core_DAO::executeQuery($monthDataUpdateSql, $monthDataUpdateParams); 
    }

    // Delete all stats which are less than start year
    // we need to delete the data we got for previous month 
    // to get brought forward value
    $deleteSql = "DELETE FROM membership_churn_monthly_table WHERE year < %1";
    $deleteParams = array(
      1 => array($start_year, 'Integer'), 
    );
    CRM_Core_DAO::executeQuery($deleteSql, $deleteParams);
  }

  public static function insertDataIntoChurnTable($year, $month) {

    if (empty($year) || empty($month)) {
      return;
    }

    $month = sprintf('%02d', $month);
    $startDate = "{$year}-{$month}-01";
    $endDate = date('Y-m-t', strtotime($startDate));

    // Carry forward / Current
    $carryForwardSql = "
    INSERT INTO membership_churn_table (year, month, membership_id, membership_type_id, current)
    SELECT {$year}, {$month}, m.id, m.membership_type_id, 1 FROM civicrm_membership m 
    INNER JOIN civicrm_contact c ON m.contact_id = c.id
    WHERE c.is_deleted = 0 
    AND join_date < '{$startDate}' AND end_date > '{$endDate}'";
    CRM_Core_DAO::executeQuery($carryForwardSql);
    
    // Joined
    $joinedSql = "
    INSERT INTO membership_churn_table (year, month, membership_id, membership_type_id, joined)
    SELECT {$year}, {$month}, m.id, m.membership_type_id, 1 FROM civicrm_membership m 
    INNER JOIN civicrm_contact c ON m.contact_id = c.id
    WHERE c.is_deleted = 0 
    AND join_date >= '{$startDate}' AND join_date <='{$endDate}'
    AND NOT EXISTS
     (
      SELECT 1
      FROM civicrm_membership cm
      WHERE cm.contact_id = m.contact_id
      AND cm.membership_type_id = m.membership_type_id
      AND cm.join_date < '{$startDate}'
      )
    ";
    CRM_Core_DAO::executeQuery($joinedSql);

    // Resigned
    $resignedSql = "
    INSERT INTO membership_churn_table (year, month, membership_id, membership_type_id, resigned)
    SELECT {$year}, {$month}, m.id, m.membership_type_id, 1 FROM civicrm_membership m 
    INNER JOIN civicrm_contact c ON m.contact_id = c.id
    WHERE c.is_deleted = 0 
    AND end_date >= '{$startDate}' AND end_date <='{$endDate}'";
    CRM_Core_DAO::executeQuery($resignedSql);

    // Rejoin
    $rejoinedSql = "
    INSERT INTO membership_churn_table (year, month, membership_id, membership_type_id, rejoined)
    SELECT {$year}, {$month}, m.id, m.membership_type_id, 1 FROM civicrm_membership m 
    INNER JOIN civicrm_contact c ON m.contact_id = c.id
    WHERE c.is_deleted = 0 
    AND join_date >= '{$startDate}' AND join_date <='{$endDate}'
    AND EXISTS
     (
      SELECT 1
      FROM civicrm_membership cm
      WHERE cm.contact_id = m.contact_id
      AND cm.membership_type_id = m.membership_type_id
      AND cm.join_date < '{$startDate}'
      )
    ";
    CRM_Core_DAO::executeQuery($rejoinedSql);
  }

  /**
   * Function to create scheduled job for preparing membership churn table data
   *
   * @params boolean $is_active (is the schedule job active or not)
   */
  public static function createScheduledJob($is_active = 1) {

    // Chekc if the schedule job exists
    $selectSql = "SELECT * FROM civicrm_job WHERE api_entity = %1 AND api_action = %2";
    $selectParams = array(
      '1' => array( 'membershipchurnchart', 'String' ),
      '2' => array( 'preparechurntable', 'String' ),
    );
    $selectDao = CRM_Core_DAO::executeQuery($selectSql, $selectParams);
    if (!$selectDao->fetch()) {
      // Create schedule job, if not exists
      $domainId = CRM_Core_Config::domainID();
      $query = "INSERT INTO civicrm_job SET domain_id = %1, run_frequency = %2, last_run = NULL, name = %3, description = %4,
      api_entity = %5, api_action = %6, parameters = NULL, is_active = %7";
      $params = array(
        '1' => array($domainId, 'Integer' ),
        '2' => array('Daily', 'String' ),
        '3' => array('Membership Churn Chart - Prepare Data', 'String' ),
        '4' => array('To prepare data for membership churn chart', 'String' ),
        '5' => array('membershipchurnchart', 'String' ),
        '6' => array('preparechurntable', 'String' ),
        '7' => array(1, 'Integer' ),
      );
      CRM_Core_DAO::executeQuery($query, $params);
    } else {
      // Enabled/Disable based on settings
      $updateSql = "UPDATE civicrm_job SET is_active = %3 WHERE api_entity = %1 AND api_action = %2";
      $updateParams = array(
        '1' => array('membershipchurnchart', 'String' ),
        '2' => array('preparechurntable', 'String' ),
        '3' => array($is_active , 'Integer' ),
      );
      $updateDao = CRM_Core_DAO::executeQuery($updateSql, $updateParams);
    }
  }

  public static function getAllMemberStatusesForChart(){
    return array(/*'Brought Forward',*/'Current', 'Joined', 'Resigned', 'Rejoined');
  }

  public static function getMinChurnValuesForYaxis($row){
    $allChurns = $chruns = array();
    foreach ($row as $year => $monthlyData) {
      $chruns = array();
      foreach ($monthlyData as $memType => $memTypeData) {
        foreach ($memTypeData as $months => $data) {
          $chruns[] = $data['Churn'];
        }
      }
      $allChurns[$year] = MIN($chruns) ? MIN($chruns) : 0;
    }
    return $allChurns;
  }

  public static function getAllmembershipTypes(){
    return CRM_Member_PseudoConstant::membershipType();
  }
}
