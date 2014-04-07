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
  $actions = CRM_Triggers_BAO_TriggerAction::findForProcessing(FALSE);
  while ($actions->fetch()) {
    //process this trigger action
    //here comes all the logic etc....
    
    //this consists of the following steps
    //1. retrieve the entities which match the condition of the trigger
    //2. prepare the api action with the entity
  }
}

