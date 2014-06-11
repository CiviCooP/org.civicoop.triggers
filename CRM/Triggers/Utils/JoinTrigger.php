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
    $references = array();
    if (method_exists($dao_classes, 'getReferenceColumns')) {
      $references = $trigger_dao_class::getReferenceColumns();
    } else {
      $fields = $trigger_dao_class::fields();
      $keyFields = $trigger_dao_class::fieldKeys();
      if (isset($fields['contact_id']) || isset($keyFields['contact_id'])) {
        $references = array(new CRM_Core_EntityReference($trigger_dao_class::getTableName() , 'contact_id', 'civicrm_contact', 'id'));
      }
    }    
    
    foreach($references as $ref) {
      foreach($dao_classes as $dao_class) {
        if ($ref->getTargetTable() == $dao_class::getTableName()) {
          return new CRM_Triggers_QueryBuilder_Condition(
            "`".$ref->getTargetTable()."`.`".$ref->getTargetKey() . 
            "` = `".$ref->getRefernceTable()."`.`".$ref->getReferenceKey()."`"
          );
        }
      }
    }
    
    foreach($dao_classes as $dao_class) {
      $references = $dao_class::getReferenceColumns();
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
  
  
}

