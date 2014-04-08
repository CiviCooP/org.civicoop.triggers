<?php

/* 
 * This is the BAO file for the trigger action table
 */

class CRM_Triggers_BAO_TriggerRuleCondition extends CRM_Triggers_DAO_TriggerRuleCondition {
 
  /**
   * Parse the condition and adds it to to the DAO of the entity
   * 
   * @param CRM_Core_DAO $dao
   */
  public function parseCondition(CRM_Core_DAO $dao) {
    
  }
  
  public static function findByTriggerRuleId($trigger_rule_id, $fetchFirst=FALSE) {
    $conditions = new CRM_Triggers_BAO_TriggerRuleCondition();
    $conditions->selectAdd();
    $conditions->selectAdd('*');
    $conditions->whereAdd("trigger_rule_id = '".$trigger_rule_id."'");
    $conditions->find($fetchFirst);
    return $conditions;
  }
  
}