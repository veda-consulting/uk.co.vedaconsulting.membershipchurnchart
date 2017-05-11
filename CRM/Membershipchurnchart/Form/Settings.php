<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Membershipchurnchart_Form_Settings extends CRM_Core_Form {
  
  function buildQuickForm() {

    $settingsStr = CRM_Core_BAO_Setting::getItem('CiviCRM Membershipchurnchart Settings', 'membershipchurnchart_settings');

    $settingsArray = unserialize($settingsStr);

    // Chart start year
    $this->add(
      'text',
      'start_year',
      ts('Start Year'),
      array('size' => 6),
      TRUE
    );

    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Submit'),
        'isDefault' => TRUE,
      ),
    ));

    $this->setDefaults($settingsArray);

    $this->addFormRule( array( 'CRM_Membershipchurnchart_Form_Settings', 'formRule' ) );

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  static function formRule( $values ){
    $errors = array();

    // Make sure the start year is not in future
    if ($values['start_year'] > date('Y')) {
      $errors['start_year'] = ts( "Start year should be current year or in past." );
    }

    return $errors;
  }

  function postProcess() {
    $values = $this->exportValues();

    $settingsArray = array();
    $settingsArray['start_year'] = $values['start_year'];
    $settingsStr = serialize($settingsArray);

    CRM_Core_BAO_Setting::setItem($settingsStr,
      'CiviCRM Membershipchurnchart Settings',
      'membershipchurnchart_settings'
    );

    // Call API to refresh churn data table
    CRM_Membershipchurnchart_Utils::CiviCRMAPIWrapper('Membershipchurnchart', 'preparechurntable', array(
      'sequential' => 1,
    ));

    $message = "Settings saved.";
    CRM_Core_Session::setStatus($message, 'Membership CHurn Chart Settings', 'success');

    CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/membership/membershipchurnchart', "reset=1"));
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }
}
