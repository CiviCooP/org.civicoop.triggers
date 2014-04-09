<?php

/*
 * This is the BAO for processing triggers
 * 
 */

class CRM_Triggers_BAO_ProcessedTrigger extends CRM_Triggers_DAO_ProcessedTrigger {

  private static $processed_activity_id = false;
  
  /**
   * Process the processed entities and contacts
   * 
   * This function will create an entry in the civicrm_processed_trigger table
   * And if contacts contain contacts it will create a processed trigger activity for every contact
   * 
   * @param CRM_Core_DAO $entity
   * @param CRM_Triggers_BAO_TriggerRule $trigger_rule
   * @param CRM_Triggers_BAO_TriggerAction $triger_action
   * @param type $contacts
   */
  public static function processTrigger(CRM_Core_DAO $entity, CRM_Triggers_BAO_TriggerRule $trigger_rule, CRM_Triggers_BAO_TriggerAction $triger_action, CRM_Triggers_BAO_ActionRule $action_rule, $contacts) {
    $processed = new CRM_Triggers_DAO_ProcessedTrigger();
    $processed->date_processed = date('YmdHis');
    $processed->entity = $trigger_rule->entity;
    $processed->entity_id = $entity->id;
    $processed->trigger_action_id = $triger_action->id;
    $processed->save();
    
    $contactIds = array();
    foreach($contacts as $contact) {
      $contactIds[] = $contact->id;
    }
    
    //create an activity if contactId is set
    if (count($contactIds)) {
      $params['activity_type_id'] = self::getActivityTypeId();
      $params['source_record_id'] = $processed->id;
      $params['subject'] = ts('Processed trigger "'.$trigger_rule->label.'" with action "'.$action_rule->label.'"');
      $params['status_id'] = 2; //completed
      //$params['target_contact_id'] = implode(",", $contactIds);
      $params['target_contact_id'] = $contactIds;
      
      civicrm_api3('Activity', 'create', $params);
    }
  }
  
  protected static function getActivityTypeId() {
    if (self::$processed_activity_id === false) {
      $option_group = civicrm_api3('OptionGroup', 'getsingle', array('name' => 'activity_type'));
      $params['option_group_id'] = $option_group['id'];
      $params['name'] = 'TriggerProcessed';
      $activity_type = civicrm_api3('OptionValue', 'getsingle', $params);
      self::$processed_activity_id = $activity_type['value'];
    }
    return self::$processed_activity_id;
  }

}
