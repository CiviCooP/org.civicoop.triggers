<?php

/* 
 * This is the BAO file for the action rule table
 */
class CRM_Triggers_BAO_ActionRule extends CRM_Triggers_DAO_ActionRule {

  /**
   * Process the action for the given entity ($objRef)
   * @param CRM_Core_DAO $objRef
   */
  public function processEntity(CRM_Core_DAO $objRef) {
    $params = $this->parseParms($objRef);
    
    civicrm_api3($this->entity, $this->action, $params);
  }
  
  /**
   * returns an array to be used with the api calls
   * 
   * @param CRM_Core_DAO $objRef
   * @return array
   */
  protected function parseParams(CRM_Core_DAO $objRef) {
    return array();
  }
  
  public static function findByActionId($action_id) {
    $action = new CRM_Triggers_BAO_ActionRule();
    $action->id = $action_id;
    $action->find(TRUE);
    return $action;
  }
  
}



