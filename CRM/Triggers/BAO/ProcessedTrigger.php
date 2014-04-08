<?php

/*
 * This is the BAO for processing triggers
 * 
 */

class CRM_Triggers_BAO_ProcessedTrigger extends CRM_Triggers_DAO_ProcessedTrigger {

  private static $processed_activity_id = false;
  
  public static function processTrigger(CRM_Core_DAO $entity, CRM_Triggers_BAO_TriggerRule $trigger_rule, CRM_Triggers_BAO_TriggerAction $triger_action) {
    $processed = new CRM_Triggers_DAO_ProcessedTrigger();
    $processed->date_processed = date('YmdHis');
    $processed->entity = $trigger_rule->entity;
    $processed->entity_id = $entity->id;
    $processed->trigger_action_id = $triger_action->id;
    $processed->save();
    
    $contactId = CRM_Triggers_Utils::getContactIdFromEntity($entity);
    //create an activity if contactId is set
    if ($contactId) {
      $params['activity_type_id'] = self::getActivityTypeId();
      $params['source_record_id'] = $processed->id;
      $params['subject'] = ts('Processed trigger "'.$trigger_rule->label.'"');
      $params['status_id'] = 2; //completed
      $params['target_contact_id'] = $contactId;
      
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
