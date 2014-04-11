<?php

/*
 * This is the BAO file for the trigger action table
 */

class CRM_Triggers_BAO_TriggerRuleCondition extends CRM_Triggers_DAO_TriggerRuleCondition {

  /**
   * Function to get conditions
   * 
   * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
   * @date 8 Apr 2014
   * @param array $params name/value pairs with field names/values
   * @return array $result found rows with data
   * @access public
   * @static
   */
  public static function getValues($params) {
    $result = array();
    $condition = new CRM_Triggers_BAO_TriggerRuleCondition();
    if (!empty($params)) {
      $fields = self::fields();
      foreach ($params as $paramKey => $paramValue) {
        if (isset($fields[$paramKey])) {
          $condition->$paramKey = $paramValue;
        }
      }
    }
    $condition->find();
    while ($condition->fetch()) {
      $row = array();
      self::storeValues($condition, $row);
      $result[$row['id']] = $row;
    }
    return $result;
  }
  
  public function getParser() {
    $parser = false;
    //determine if this condition is an aggregation
    $is_aggregate = false;
    if (!empty($this->aggregate_function) && !empty($this->grouping_field)) {
      $is_aggregate = true;
    }

    if ($is_aggregate) {
      $parser = new CRM_Triggers_Rule_AggregateConditionParser($this);
    } else {
      $parser = new CRM_Triggers_Rule_PlainConditionParser($this);
    }
    
    return $parser;
  }
  
  public static function findByTriggerRuleId($trigger_rule_id, $fetchFirst = FALSE) {
    $conditions = new CRM_Triggers_BAO_TriggerRuleCondition();
    $conditions->selectAdd();
    $conditions->selectAdd('*');
    $conditions->whereAdd("trigger_rule_id = '" . $trigger_rule_id . "'");
    $conditions->find($fetchFirst);
    return $conditions;
  }

  /**
   * Function to add or update trigger rule condition
   * 
   * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
   * @date 9 Apr 2014
   * @param array $params 
   * @return array $result
   * @access public
   * @static
   */
  public static function add($params) {
    $result = array();
    if (empty($params)) {
      throw new Exception('Params can not be empty when adding or updating a TriggerRule Condition');
    }
    $triggerRuleCondition = new CRM_Triggers_BAO_TriggerRuleCondition();
    $fields = self::fields();
    foreach ($params as $paramKey => $paramValue) {
      if (isset($fields[$paramKey])) {
        $triggerRuleCondition->$paramKey = $paramValue;
      }
    }
    $triggerRuleCondition->save();
    self::storeValues($triggerRuleCondition, $result);
    return $result;
  }

  /**
   * Function to delete trigger rule condition
   * 
   * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
   * @date 9 Apr 2014
   * @param int $triggerRuleConditionId 
   * @return boolean
   * @access public
   * @static
   */
  public static function deleteById($triggerRuleConditionId) {
    if (empty($triggerRuleConditionId)) {
      throw new Exception('TriggerRuleConditionId can not be empty when attempting to delete one');
    }
    $condition = new CRM_Triggers_BAO_TriggerRuleCondition();
    $condition->id = $triggerRuleConditionId;
    $condition->delete();
    return TRUE;
  }

}
