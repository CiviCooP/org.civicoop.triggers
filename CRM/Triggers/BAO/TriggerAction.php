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
    $trigger_action = new CRM_Triggers_DAO_TriggerAction();
    
    $trigger_action->selectAdd();
    $trigger_action->selectAdd("*");
    $trigger_action->whereAdd('is_active = 1');
    $trigger_action->whereAdd('(start_date IS NULL OR start_date <= CURDATE())');
    $trigger_action->whereAdd('(end_date IS NULL OR end_date >= CURDATE())');
    $trigger_action->whereAdd('(next_run <= CURDATE())');
    
    $trigger_action->find($fetchFirst);
    
    return $trigger_action;
  }
  
}

