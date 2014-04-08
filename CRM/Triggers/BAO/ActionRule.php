<?php

/* 
 * This is the BAO file for the action rule table
 */
class CRM_Triggers_BAO_ActionRule extends CRM_Triggers_DAO_ActionRule {

  /**
   * Process the action for the given entity ($objRef)
   * @param CRM_Core_DAO $objRef
   */
  public function processEntity(CRM_Core_DAO $objRef, CRM_Triggers_BAO_TriggerRule $trigger_rule) {
    $params = $this->parseParams($objRef, $trigger_rule);
    
    civicrm_api3($this->entity, $this->action, $params);
    
    return true;
  }
  
  /**
   * returns an array to be used with the api calls
   * 
   * @param CRM_Core_DAO $objRef
   * @return array
   */
  protected function parseParams(CRM_Core_DAO $objRef, CRM_Triggers_BAO_TriggerRule $trigger_rule) {
    $return = array();
    
    $p = explode("&", $this->params);
    $params = array();
    foreach ($p as $str) {
      $strArr = explode("=", $str);
      if (isset($strArr[0]) && isset($strArr[1]));
      $params[$strArr[0]] = $strArr[1];
    }
    $fields = $objRef->fields();
    
    $hooks = CRM_Utils_Hook::singleton();
    $hooks->invoke(5,
      $params, $return, $objRef, $trigger_rule, $this,
      'civicrm_trigger_action_parse_params'
      );
    
    //loop trhough all parameters and replace their value if needed
    foreach($params as $key => $val) {  
      //check if the val is encapsulated into brackets { }      
      //e.g. {contribution.status_id}      
         
      $matches = array();      
      if (!isset($return[$key]) && preg_match("/{(.*)\.(.*)}/", $val, $matches)) {
        //value looks like {entity.field}
        //so we split this and we check if objRef is of entty and the field exist on objectref
        $entityName = CRM_Triggers_Utils::camelCaseEntity($matches[1]);
        $fieldName = $matches[2];
        
        $entityType = CRM_Core_DAO_AllCoreTables::getFullName($entityName);
        if ($entityType == NULL) {
          throw new CRM_Triggers_Exception_DAO_Not_Found("Entity ".$entityName." has no DAO");
        }
        //check if objRef is an instanceof $entityType
        if ($objRef instanceof $entityType) {      
          if (isset($fields[$fieldName])) {
            $return[$key] = $objRef->$fieldName;
          }
        }
      } 
      
      $matches = array();
      if (!isset($return[$key]) && preg_match("/{(.*)}/", $val, $matches)) {
        //value looks like {field}
        //check if field exist on objRef.
        $fieldName = $matches[1];
        if (isset($fields[$fieldName])) {
          $return[$key] = $objRef->$fieldName;
        }
      }
      
      if (!isset($return[$key])) {
        $return[$key] = $val;
      }
    }
    return $return;
  }
  
  public static function findByActionId($action_id) {
    $action = new CRM_Triggers_BAO_ActionRule();
    $action->id = $action_id;
    $action->find(TRUE);
    return $action;
  }
  
}



