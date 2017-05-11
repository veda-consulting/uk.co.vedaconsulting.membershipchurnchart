<?php

/**
 * Membershipchurnchart prepare churn table API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */ 
function civicrm_api3_membershipchurnchart_preparechurntable($params) {

  // Prepare renewal dates
  CRM_Membershipchurnchart_Utils::prepareChurnTable();

  $returnValues = array();
  // Return success
  return civicrm_api3_create_success($returnValues, $params, 'Membershipchurnchart', 'Preparechurntable');
}