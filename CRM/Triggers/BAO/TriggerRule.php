<?php

/* 
 * This is the BAO file for the trigger action table
 */

class CRM_Triggers_BAO_TriggerRule extends CRM_Triggers_DAO_TriggerRule {
  
  /**
   * Returns the BAO/DAO for a given entity
   * 
   * @param String $entity
   * @return BAO/DAO
   */
  public static function getEntityDAO($entity) {
    $dao = CRM_Core_DAO_AllCoreTables::getFullName($entity);
    if ($dao == NULL) {
      throw new CRM_Triggers_Exception_DAO_Not_Found("Entity ".$entity." has no DAO");
    }
    return new $dao();
  }
  
}

