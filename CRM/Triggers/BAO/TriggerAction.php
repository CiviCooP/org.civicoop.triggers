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
  public function findEntities() {
    $this->validate();
    
    $trigger = new CRM_Core_BAO_TriggerRule();
    $trigger->selectAdd();
    $trigger->selectAdd('*');
    $trigger->whereAdd("id = '".$this->trigger_rule_id."'");
    $trigger->find(TRUE);
    
    //now build condition query
  }
  
  /**
   * Checks if current object is valid to use
   * Throws an error if not
   */
  protected function validate() {
    if (empty($this->trigger_rule_id)) {
      throw new CRM_Triggers_Exception_InvalidTriggerActionEntity("Trigger rule ID is not set");
    }
  }
  
}

