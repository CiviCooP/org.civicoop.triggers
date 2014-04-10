<?php

/**
 * TriggerAction.Process API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
function _civicrm_api3_trigger_action_process_spec(&$spec) {
  //no parameters for this cron job
}

/**
 * TriggerAction.Process API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_trigger_action_process($params) {
  $actions = CRM_Triggers_BAO_RuleSchedule::findForProcessing(FALSE);
  $messages = array();
  $count = 0;
  while ($actions->fetch()) {
    //process this trigger action
    //here comes all the logic etc....
    
    //this consists of the following steps
    //1. retrieve the entities which match the condition of the trigger
    //For every entity
    //   2. prepare the api action with the entity
    //   3. create activity of type: TriggerAction for this trigger action and entity
    //   
    //4. set next run day
    
    
    //Retrieve the entities
    $entities = $actions->findEntities();
    
    $trigger = new CRM_Triggers_BAO_TriggerRule();
    $trigger->id = $actions->trigger_rule_id;
    $trigger->find(TRUE);
    
    $action = CRM_Triggers_BAO_ActionRule::findByActionId($actions->action_rule_id);
    $processedEntityCount = 0;
    while ($entities->fetch()) {
      //process the entity
      $processCount = $action->processEntity($entities, $trigger, $actions);
      
      if ($processCount) {
        $processedEntityCount = $processedEntityCount + $processCount;
      }
    }
    
    //reschedule the trigger-action
    $actions->reschedule();
    
    $count ++;
    $messages[] = 'Trigger "'.$trigger->label.'" processed '.$processedEntityCount.' entities';
  }
  
  $params['message'] = 'Processed '.$count.' triggers. '.implode(' ', $messages);
  return civicrm_api3_create_success($params);
  
}

