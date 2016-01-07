<?php
/**
 * Class for CiviRules Activity By Type and Status Trigger
 *
 * @author Carlos Capote (Ixiam) <carlos@librecool.es>
 * @license AGPL-3.0
 */

class CRM_CivirulesCronTrigger_ActivityByTypeAndStatus extends CRM_Civirules_Trigger_Cron {

  private $dao = false;

  /**
   * This function returns a CRM_Civirules_TriggerData_TriggerData this entity is used for triggering the rule
   *
   * Return false when no next entity is available
   *
   * @return CRM_Civirules_TriggerData_TriggerData|false
   */
  protected function getNextEntityTriggerData() {
    if (!$this->dao) {
      if (!$this->queryForTriggerEntities()) {
        return false;
      }
    }
    if ($this->dao->fetch()) {
      $data = array();
      CRM_Core_DAO::storeValues($this->dao, $data);
      $triggerData = new CRM_Civirules_TriggerData_Cron($this->dao->contact_id, 'ActivityContact', $data);
      return $triggerData;
    }
    return false;
  }

  /**
   * Returns an array of entities on which the trigger reacts
   *
   * @return CRM_Civirules_TriggerData_EntityDefinition
   */
  protected function reactOnEntity() {
    return new CRM_Civirules_TriggerData_EntityDefinition('Activity', 'Activity', 'CRM_Activity_DAO_Activity', 'Activity');
  }

  /**
   * Method to query trigger entities
   *
   * @access private
   */
  private function queryForTriggerEntities() {

    if (empty($this->triggerParams['activity_type_id']) ||
            empty($this->triggerParams['status_id'])) {
      return false;
    }

    $sql = "SELECT *
            FROM civicrm_activity AS a
            JOIN civicrm_activity_contact AS ac ON ac.activity_id = a.id
            WHERE a.activity_type_id = %1 AND a.status_id = %2";
    $params[1] = array($this->triggerParams['activity_type_id'], 'Integer');
    $params[2] = array($this->triggerParams['status_id'], 'Integer');
    $this->dao = CRM_Core_DAO::executeQuery($sql, $params, true, 'CRM_Activity_DAO_Activity');

    return true;
  }

  /**
   * Returns a redirect url to extra data input from the user after adding a condition
   *
   * Return false if you do not need extra data input
   *
   * @param int $ruleId
   * @return bool|string
   * @access public
   * @abstract
   */
  public function getExtraDataInputUrl($ruleId) {
    return CRM_Utils_System::url('civicrm/civirule/form/trigger/activity_by_type_and_status/', 'rule_id='.$ruleId);
  }

  public function setTriggerParams($triggerParams) {
    $this->triggerParams = unserialize($triggerParams);
  }

  /**
   * Returns a description of this trigger
   *
   * @return string
   * @access public
   * @abstract
   */
  public function getTriggerDescription() {
    $activity_type_id = $this->triggerParams['activity_type_id'];
    $activity_type = civicrm_api3('OptionValue', 'getsingle', array(
      'option_group_id' => $this->getOptionGroupId('activity_type'),
      'value' => $activity_type_id
    ));
    
    $status_id = $this->triggerParams['status_id'];
    $status = civicrm_api3('OptionValue', 'getsingle', array(
      'option_group_id' => $this->getOptionGroupId('activity_status'),
      'value' => $status_id
    )); 
    
    return ts('Activities of type %1 with status %2', array( $activity_type['label'], $status['label'] ));
  }
  
  /**
   * Returns the id of an option group
   * 
   * @return integer
   * @access public
   */
  public function getOptionGroupId($option_group_name) {
    $result = civicrm_api3('OptionGroup', 'get', array(
      'name' => $option_group_name
    ));
    
    if($result) {
        return $result['id'];
    } else {
        return 0;
    }
  }
}
