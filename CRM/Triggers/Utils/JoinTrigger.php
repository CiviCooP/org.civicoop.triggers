<?php

/* 
 * Util class to help joining triggers together
 * 
 */

class CRM_Triggers_Utils_JoinTrigger {
  
  public function __construct() {
    
  }  
  
  
  /**
   * Create a join condition. 
   * 
   * Returns the CRM_Triggers_QueryBuilder_ConditionInterface when a join is possible. 
   * Returns false when no join conditions are possible
   * 
   * @param array $dao_classes array of class names to check for link
   * @param string $trigger_dao_class the class name of the current dao
   * @return boolean|\CRM_Triggers_QueryBuilder_Condition
   */
  public static function createJoinCondition($dao_classes, $trigger_dao_class, $table_alias) {
    $references = CRM_Triggers_Utils_JoinTrigger::getDaoReferenceColumns($trigger_dao_class);    
    foreach($references as $ref) {
      foreach($dao_classes as $alias => $dao_class) {
        if ($ref->getTargetTable() == $dao_class::getTableName()) {
          $condition = new CRM_Triggers_QueryBuilder_Subcondition();
          $condition->addCond(new CRM_Triggers_QueryBuilder_Condition(
            "`".$alias."`.`".$ref->getTargetKey() . 
            "` = `".$table_alias."`.`".$ref->getReferenceKey()."`"
          ));
          if ($ref->getExtraCondition()) {
            $condition->addCond($ref->getExtraCondition());
          }
          return $condition;
        }
      }
    }
    
    foreach($dao_classes as $alias => $dao_class) {
      $references = CRM_Triggers_Utils_JoinTrigger::getDaoReferenceColumns($dao_class);
      foreach($references as $ref) {
        if ($ref->getTargetTable() == $trigger_dao_class::getTableName()) {
          $condition = new CRM_Triggers_QueryBuilder_Subcondition();
          $condition->addCond(new CRM_Triggers_QueryBuilder_Condition(
            "`".$table_alias."`.`".$ref->getTargetKey() . 
            "` = `".$alias."`.`".$ref->getReferenceKey()."`"
          ));
          if ($ref->getExtraCondition()) {
            $condition->addCond($ref->getExtraCondition());
          }
          return $condition;
        }
      }
    }
    
    //check for linking entity e.g. group_contact.contact_id = activity_target.target_contact_id
    foreach($dao_classes as $alias => $dao_class) {
      $references = CRM_Triggers_Utils_JoinTrigger::getDaoReferenceColumns($dao_class);
      $trigger_references = CRM_Triggers_Utils_JoinTrigger::getDaoReferenceColumns($trigger_dao_class);
      foreach($trigger_references as $trigger_ref) {
        foreach($references as $ref) {
          if ($ref->getTargetTable() == $trigger_ref->getTargetTable() && $ref->getTargetKey() == $trigger_ref->getTargetKey()) {
            $condition = new CRM_Triggers_QueryBuilder_Subcondition();
            $condition->addCond(new CRM_Triggers_QueryBuilder_Condition(
              "`".$table_alias."`.`".$trigger_ref->getReferenceKey() . 
              "` = `".$alias."`.`".$ref->getReferenceKey()."`"
            ));
            if ($ref->getExtraCondition()) {
              $condition->addCond($ref->getExtraCondition());
            }
            if ($trigger_ref->getExtraCondition()) {
              $condition->addCond($trigger_ref->getExtraCondition());
            }
            return $condition;
          }
        }
      }
    }

		//create a join on contact_id if both classes have a link to contact
		if (CRM_Triggers_Utils_JoinTrigger::hasDaoALinkToContact($trigger_dao_class)) {
			foreach($dao_classes as $alias => $dao_class) {
				if (CRM_Triggers_Utils_JoinTrigger::hasDaoALinkToContact($dao_class)) {
						return new CRM_Triggers_QueryBuilder_Condition("`".$table_alias."`.`contact_id` = `".$alias."`.`contact_id`");
				}
			}
		}

    return false;
  }
  
  /**
   * Get refernces to other objects from a DAO 
   * 
   * @param type $dao_class
   * @return \CRM_Triggers_Utils_EntityReference
   */
  protected static function getDaoReferenceColumns($dao_class) {
    $references = array();
    if (method_exists($dao_class, 'getReferenceColumns')) {
      $references = CRM_Triggers_Utils_EntityReference::convertReferences($dao_class::getReferenceColumns());
    } elseif ($dao_class == 'CRM_Activity_DAO_ActivityTarget') {
      $contact_ref = new CRM_Triggers_Utils_EntityReference('civicrm_activity_contact' , 'contact_id', 'civicrm_contact', 'id');
      $contact_ref->addExtraCondition(new CRM_Triggers_QueryBuilder_Condition('record_type_id = 3'));
      $references[] = $contact_ref;
      $activity_ref = new CRM_Triggers_Utils_EntityReference('civicrm_activity_contact' , 'activity_id', 'civicrm_activity', 'id');
      $activity_ref->addExtraCondition(new CRM_Triggers_QueryBuilder_Condition('record_type_id = 3'));
      $references[] = $activity_ref;
    } elseif ($dao_class == 'CRM_Activity_DAO_ActivityAssignment') {
      $contact_ref = new CRM_Triggers_Utils_EntityReference('civicrm_activity_contact' , 'contact_id', 'civicrm_contact', 'id');
      $contact_ref->addExtraCondition(new CRM_Triggers_QueryBuilder_Condition('record_type_id = 1'));
      $references[] = $contact_ref;
      $activity_ref = new CRM_Triggers_Utils_EntityReference('civicrm_activity_contact' , 'activity_id', 'civicrm_activity', 'id');
      $activity_ref->addExtraCondition(new CRM_Triggers_QueryBuilder_Condition('record_type_id = 1'));
      $references[] = $activity_ref;
    } else {
      //not in every version of civicrm the reference columns are defined.
      //so we have to add them manually.
      //first check if a contact_id field exist. If so add a ref to the contact object
			if (CRM_Triggers_Utils_JoinTrigger::hasDaoALinkToContact($dao_class)) {
        $references = array(new CRM_Triggers_Utils_EntityReference($dao_class::getTableName() , 'contact_id', 'civicrm_contact', 'id'));
      }
    }
    return $references;
  }

	protected static function hasDaoALinkToContact($dao_class) {
		if ($dao_class::getTableName() == 'civicrm_contact') {
			return false;
		}
		
		//not in every version of civicrm the reference columns are defined.
    //so we have to add them manually.
    //first check if a contact_id field exist. If so add a ref to the contact object
    $fields = $dao_class::fields();
    $keyFields = array();
    if (method_exists($dao_class, 'fieldKeys')) {
      $keyFields = $dao_class::fieldKeys();
    }
    if (isset($fields['contribution_contact_id'])) {
      return true;
    } elseif (isset($fields['contact_id']) || isset($keyFields['contact_id'])) {
      return true;
    }
		return false;
	}
  
}
