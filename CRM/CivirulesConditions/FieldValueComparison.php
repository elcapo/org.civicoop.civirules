<?php

class CRM_CivirulesConditions_FieldValueComparison extends CRM_CivirulesConditions_Generic_ValueComparison {

  /**
   * Returns the value of the field for the condition
   * For example: I want to check if age > 50, this function would return the 50
   *
   * @param object CRM_Civirules_TriggerData_TriggerData $triggerData
   * @return
   * @access protected
   * @abstract
   */
  protected function getFieldValue(CRM_Civirules_TriggerData_TriggerData $triggerData) {
    $entity = $this->conditionParams['entity'];
    $field = $this->conditionParams['field'];

    $data = $triggerData->getEntityData($entity);
    if (isset($data[$field])) {
      return $this->normalizeValue($data[$field]);
    }
    
    if ($this->isRelativeDate($field)) {
      $relativeDate = $this->parseRelativeDate($field);
      $field = $relativeDate['field'];
      $interval = $relativeDate['interval'];
      if (isset($data[$field])) {
        $date = new DateTime($data[$field]);
        $today = new DateTime("now");
        $diff = $date->diff($today);
        return $this->normalizeValue($diff->format('%'.$interval));
      }
    }

    if (strpos($field, 'custom_')===0) {
      $custom_field_id = str_replace("custom_", "", $field);
      try {
        $params['entityID'] = $data['id'];
        $params[$field] = 1;
        $values = CRM_Core_BAO_CustomValueTable::getValues($params);

        $value = null;
        if (!empty($values[$field])) {
          $value = $this->normalizeValue($values[$field]);
        } elseif (!empty($values['error_message'])) {
          $value = $triggerData->getCustomFieldValue($custom_field_id);
        }

        if ($value !== null) {
          $value = $this->convertMultiselectCustomfieldToArray($custom_field_id, $value);
          return $this->normalizeValue($value);
        }
      } catch (Exception $e) {
        //do nothing
      }
    }

    return null;
  }

  protected function isRelativeDate($field) {
    $result = strpos($field, '_days2today');
    
    if ( !$result ) {
      $result = strpos($field, '_months2today');
    }
    
    if ( !$result ) {
      $result = strpos($field, '_years2today');
    }
    
    return $result;
  }
  
  protected function parseRelativeDate($field) {
    $result = array();
    
    if(strpos($field, '_days2today') !== false) {
      $result['field'] = substr($field, 0,
        strlen($field) - strlen('_days2today'));
      $result['interval'] = 'd';
    } else if(strpos($field, '_months2today') !== false) {
      $result['field'] = substr($field, 0,
        strlen($field) - strlen('_months2today'));
      $result['interval'] = 'm';
    } else if(strpos($field, '_years2today') !== false) {
      $result['field'] = substr($field, 0,
        strlen($field) - strlen('_years2today'));
      $result['interval'] = 'y';
    }
    
    return $result;
  }
  
  /**
   * Returns an array of value when the custom field is a multi select
   * otherwise just return the value
   *
   * @param $custom_field_id
   * @param $value
   * @return mixed
   */
  protected function convertMultiselectCustomfieldToArray($custom_field_id, $value) {
    if (CRM_Civirules_Utils_CustomField::isCustomFieldMultiselect($custom_field_id)) {
      $value = trim($value, CRM_Core_DAO::VALUE_SEPARATOR);
      $value = explode(CRM_Core_DAO::VALUE_SEPARATOR, $value);
    }
    return $value;
  }

  /**
   * Returns the value for the data comparison
   *
   * @return mixed
   * @access protected
   */
  protected function getComparisonValue() {
    $value = parent::getComparisonValue();
    if (!empty($value)) {
      return $this->normalizeValue($value);
    } else {
      return null;
    }
  }

  protected function normalizeValue($value) {
    if ($value === null) {
      return null;
    }

    //@todo normalize value based on the field
    return $value;
  }

  /**
   * Returns a redirect url to extra data input from the user after adding a condition
   *
   * Return false if you do not need extra data input
   *
   * @param int $ruleConditionId
   * @return bool|string
   * @access public
   */
  public function getExtraDataInputUrl($ruleConditionId) {
    return CRM_Utils_System::url('civicrm/civirule/form/condition/fieldvaluecomparison/', 'rule_condition_id='.$ruleConditionId);
  }

  /**
   * Returns a user friendly text explaining the condition params
   * e.g. 'Older than 65'
   *
   * @return string
   * @access public
   */
  public function userFriendlyConditionParams() {
    $value = $this->getComparisonValue();
    if (is_array($value)) {
      $value = implode(", ", $value);
    }
    return htmlentities($this->conditionParams['entity'].'.'.$this->conditionParams['field'].' '.($this->getOperator())).' '.htmlentities($value);
  }

}