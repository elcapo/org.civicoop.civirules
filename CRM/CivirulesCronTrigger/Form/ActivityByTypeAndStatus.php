<?php
/**
 * Class for CiviRules Activity By Type and Status Trigger
 *
 * @author Carlos Capote (Ixiam) <carlos@librecool.es>
 * @license AGPL-3.0
 */

class CRM_CivirulesCronTrigger_Form_ActivityByTypeAndStatus extends CRM_CivirulesTrigger_Form_Form {

  /**
   * Method to get activity types
   *
   * @return array
   * @access protected
   */
  protected function getActivityTypes() {
    return CRM_Core_OptionGroup::values('activity_type');
  }

  /**
   * Method to get activity statuses
   *
   * @return array
   * @access protected
   */
  protected function getActivityStatuses() {
    return CRM_Core_OptionGroup::values('activity_status');
  }

  /**
   * Overridden parent method to build form
   *
   * @access public
   */
  public function buildQuickForm() {
    $this->add('hidden', 'rule_id');
    $this->add('select', 'activity_type_id', ts('Activity Type'), $this->getActivityTypes(), true);
    $this->add('select', 'status_id', ts('Activity Status'), $this->getActivityStatuses(), true);

    $this->addButtons(array(
      array('type' => 'next', 'name' => ts('Save'), 'isDefault' => TRUE,),
      array('type' => 'cancel', 'name' => ts('Cancel'))));
  }

  /**
   * Overridden parent method to set default values
   *
   * @return array $defaultValues
   * @access public
   */
  public function setDefaultValues() {
    $defaultValues = parent::setDefaultValues();
    $data = unserialize($this->rule->trigger_params);
    
    if (!empty($data['activity_type_id'])) {
      $defaultValues['activity_type_id'] = $data['activity_type_id'];
    }
    
    if (!empty($data['status_id'])) {
      $defaultValues['status_id'] = $data['status_id'];
    }
    
    return $defaultValues;
  }

  /**
   * Overridden parent method to process form data after submission
   *
   * @throws Exception when rule condition not found
   * @access public
   */
  public function postProcess() {
    $data['activity_type_id'] = $this->_submitValues['activity_type_id'];
    $data['status_id'] = $this->_submitValues['status_id'];
    $this->rule->trigger_params = serialize($data);
    $this->rule->save();

    parent::postProcess();
  }
}