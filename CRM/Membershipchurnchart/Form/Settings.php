<?php

require_once 'CRM/Core/Form.php';
use CRM_Membershipchurnchart_ExtensionUtil as E;

class CRM_Membershipchurnchart_Form_Settings extends CRM_Admin_Form_Generic {

  public function buildQuickForm() {
    parent::buildQuickForm();
    $this->addFormRule(['CRM_Membershipchurnchart_Form_Settings', 'formRule']);
  }

  public static function formRule($values) {
    $errors = [];

    // Make sure the start year is not in future
    if ($values['membershipchurnchart_startyear'] > date('Y')) {
      $errors['membershipchurnchart_startyear'] = E::ts('Start year should be current year or in past.');
    }

    return $errors;
  }

  public function postProcess() {
    parent::postProcess();
    CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/membership/membershipchurnchart', "reset=1"));
  }
}
