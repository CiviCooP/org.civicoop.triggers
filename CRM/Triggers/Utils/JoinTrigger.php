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
  public static function createJoinCondition($dao_classes, $trigger_dao_class) {
    $references = CRM_Triggers_Utils_JoinTrigger::getDaoReferenceColumns($trigger_dao_class);    
    foreach($references as $ref) {
      foreach($dao_classes as $dao_class) {
        if ($ref->getTargetTable() == $dao_class::getTableName()) {
          return new CRM_Triggers_QueryBuilder_Condition(
            "`".$ref->getTargetTable()."`.`".$ref->getTargetKey() . 
            "` = `".$ref->getReferenceTable()."`.`".$ref->getReferenceKey()."`"
          );
        }
      }
    }
    
    foreach($dao_classes as $dao_class) {
      $references = CRM_Triggers_Utils_JoinTrigger::getDaoReferenceColumns($dao_class);
      foreach($references as $ref) {
        if ($ref->getTargetTable() == $trigger_dao_class::getTableName()) {
          return new CRM_Triggers_QueryBuilder_Condition(
            "`".$ref->getTargetTable()."`.`".$ref->getTargetKey() . 
            "` = `".$ref->getReferenceTable()."`.`".$ref->getReferenceKey()."`"
          );
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
    } else {
      //not in every version of civicrm the reference columns are defined.
      //so we have to add them manually.
      //first check if a contact_id field exist. If so add a ref to the contact object
      $fields = $dao_class::fields();
      $keyFields = array();
      if (method_exists($dao_class, 'fieldKeys')) {
        $keyFields = $dao_class::fieldKeys();
      }
      if (isset($fields['contribution_contact_id'])) {
        $references = array(new CRM_Triggers_Utils_EntityReference($dao_class::getTableName() , 'contact_id', 'civicrm_contact', 'id'));
      } elseif (isset($fields['contact_id']) || isset($keyFields['contact_id'])) {
        $references = array(new CRM_Triggers_Utils_EntityReference($dao_class::getTableName() , 'contact_id', 'civicrm_contact', 'id'));
      }
    }
    return $references;
  }
  
}

