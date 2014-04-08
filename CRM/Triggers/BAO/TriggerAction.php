<?php

/* 
 * This is the BAO file for the trigger action table
 */

class CRM_Triggers_BAO_TriggerAction extends CRM_Triggers_DAO_TriggerAction {

  /**
   * Find trigger actions which are ready for processing
   * 
   * @param bool $fetchFirst fetch the first record
   */
  public static function findForProcessing($fetchFirst = FALSE) {
    $trigger_action = new CRM_Triggers_BAO_TriggerAction();
    
    $trigger_action->selectAdd();
    $trigger_action->selectAdd("*");
    $trigger_action->whereAdd('is_active = 1');
    $trigger_action->whereAdd('(start_date IS NULL OR start_date <= CURDATE())');
    $trigger_action->whereAdd('(end_date IS NULL OR end_date >= CURDATE())');
    $trigger_action->whereAdd('(next_run <= CURDATE())');
    
    $trigger_action->find($fetchFirst);
    
    return $trigger_action;
  }
  
  /**
   * Returns the found entities which should be processed by the trigger
   */
  public function findEntities($fetchFirst=false) {
    //check if this object is valid for finding entities
    if (empty($this->trigger_rule_id)) {
      throw new CRM_Triggers_Exception_InvalidTriggerAction("Trigger rule ID is not set");
    }
    
    $trigger = new CRM_Triggers_BAO_TriggerRule();
    $trigger->selectAdd();
    $trigger->selectAdd('*');
    $trigger->whereAdd("id = '".$this->trigger_rule_id."'");
    $trigger->find(TRUE);
    
    $dao = CRM_Triggers_BAO_TriggerRule::getEntityDAO($trigger->entity);
    //build condition for this dao
    $dao->selectAdd();
    $dao->selectAdd('*');
    
    $conditions = CRM_Triggers_BAO_TriggerRuleCondition::findByTriggerRuleId($trigger->id);
    $conditionsCount = 0;
    while($conditions->fetch()) {
      $conditions->parseCondition($dao);
      $conditionsCount ++;
    }
    
    if ($conditionsCount <= 0) {
      throw new CRM_Triggers_Exception_NoConditions('No active conditions found for this rule, stop processing');
    }
    
    //add a join on civicrm_processed_trigger
    $dao->whereAdd("`id` NOT IN ("
        . "SELECT `entity_id` FROM `civicrm_processed_trigger` "
        . "WHERE `entity` = '".$dao->escape($trigger->entity)."' "
        . "AND `trigger_action_id` = '".$dao->escape($this->id)."')");
    
    if ($dao->find($fetchFirst)!==false) {
      return $dao;
    }
    
    throw new CRM_Triggers_Exception_QueryError("Query error on finding entities");
  }
  
}

