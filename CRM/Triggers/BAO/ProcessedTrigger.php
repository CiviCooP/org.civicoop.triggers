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
   * @param CRM_Triggers_BAO_RuleSchedule $rule_schedule
   * @param type $contacts
   */
  public static function processTrigger($entities, CRM_Triggers_BAO_RuleSchedule $rule_schedule, CRM_Triggers_BAO_ActionRule $action_rule, $contacts) {
    foreach($entities as $entityName => $entity) {
      $processed = new CRM_Triggers_DAO_ProcessedTrigger();
      $processed->date_processed = date('YmdHis');
      $processed->entity = $entityName;
      $processed->entity_id = $entity->id;
      $processed->rule_schedule_id = $rule_schedule->id;
      $processed->save();
    }
    
    $contactIds = array();
    foreach($contacts as $contact) {
      $contactIds[] = $contact->id;
    }
    
    //create an activity if contactId is set
    if (count($contactIds)) {
      $params['activity_type_id'] = self::getActivityTypeId();
      $params['source_record_id'] = $processed->id;
      $params['subject'] = ts('Processed "'.$rule_schedule->label.'" with action "'.$action_rule->label.'"');
      $params['status_id'] = 2; //completed
      //$params['target_contact_id'] = implode(",", $contactIds);
      $params['target_contact_id'] = $contactIds;
      
      $params['version'] = 3;
      $r = civicrm_api('Activity', 'create', $params);
      if (isset($r['is_error']) && $r['is_error']) {
        throw new API_Exception('API Error Activity.create');
      }
    }
  }
  
  protected static function getActivityTypeId() {
    if (self::$processed_activity_id === false) {
      $option_group = civicrm_api('OptionGroup', 'getsingle', array('name' => 'activity_type', 'version' => 3));
      if (isset($option_group['is_error']) && $option_group['is_error']) {
        throw new API_Exception('API Error: OptionGroup.getsingle');
      }
      $params['option_group_id'] = $option_group['id'];
      $params['name'] = 'TriggerProcessed';
      $params['version'] = 3;
      $activity_type = civicrm_api('OptionValue', 'getsingle', $params);
      if (isset($activity_type['is_error']) && $activity_type['is_error']) {
        throw new API_Exception('API Error: OptionValue.getsingle');
      }
      self::$processed_activity_id = $activity_type['value'];
    }
    return self::$processed_activity_id;
  }

}
