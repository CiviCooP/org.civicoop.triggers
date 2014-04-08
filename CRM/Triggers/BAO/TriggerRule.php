<?php

/* 
 * This is the BAO file for the trigger action table
 */

class CRM_Triggers_BAO_TriggerRule extends CRM_Triggers_DAO_TriggerRule {
  
  public function findEntities($fetchFirst = false) {
    $dao = self::getEntityDAO($this->entity);
    //build condition for this dao
    $dao->selectAdd();
    $dao->selectAdd('*');
    
    $conditions = CRM_Triggers_BAO_TriggerRuleCondition::findByTriggerRuleId($this->id);
    $conditionsCount = 0;
    while($conditions->fetch()) {
      $conditions->parseCondition($dao);
      $conditionsCount ++;
    }
    
    if ($conditionsCount <= 0) {
      throw new CRM_Triggers_Exception_NoConditions('No active conditions found for this rule, stop processing');
    }
    
    return $dao->find($fetchFirst);
  }
  
  /**
   * Returns the BAO/DAO for a given entity
   * 
   * @param String $entity
   * @return BAO/DAO
   */
  protected static function getEntityDAO($entity) {
    $dao = CRM_Core_DAO_AllCoreTables::getFullName($entity);
    if ($dao == NULL) {
      throw new CRM_Triggers_Exception_DAO_Not_Found("Entity ".$entity." has no DAO");
    }
    return $dao;
  }
  
  /**
   * Throws a new exception when this trigger rule is not valid
   */
  protected function validate() {
    if (empty($this->entity)) {
      throw new CRM_Triggers_Exception_InvalidTriggerRule("Entity is not set");
    }
  }
  
}
