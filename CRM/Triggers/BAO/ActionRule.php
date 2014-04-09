<?php

/* 
 * This is the BAO file for the action rule table
 */
class CRM_Triggers_BAO_ActionRule extends CRM_Triggers_DAO_ActionRule {

  /**
   * Process the action for the given entity ($objRef)
   * @param CRM_Core_DAO $objRef
   */
  public function processEntity(CRM_Core_DAO $objRef, CRM_Triggers_BAO_TriggerRule $trigger_rule, CRM_Triggers_BAO_TriggerAction $trigger_action) {
    //set the objects array for processing
    $objects[strtolower($trigger_rule->entity)] = $objRef;
    
    //retrieve contacts from the entity
    $contacts = CRM_Triggers_Utils::getContactsFromEntity($objRef);

    $processCount = 0;
    if ($this->process_contacts && count($contacts)) {
      foreach($contacts as $contact) {
        $objects['Contact'] = $contact;
        $params = $this->parseParams($objects, $trigger_rule);
        civicrm_api3($this->entity, $this->action, $params);
        $processCount++;
      }
    } else {
      $params = $this->parseParams($objects, $trigger_rule);
      civicrm_api3($this->entity, $this->action, $params);
      $processCount++;
    }
    
    //add an activity type and add this entity to the processed table        
      //we do that through the processed trigger BAO
    CRM_Triggers_BAO_ProcessedTrigger::processTrigger($objRef, $trigger_rule, $trigger_action, $this, $contacts);
    
    return $processCount;
  }
  
  /**
   * returns an array to be used with the api calls
   * 
   * @param array $objects array of objects identified by lower case entity name 'e.g. contribution'.
   * @return array
   */
  protected function parseParams($objects, CRM_Triggers_BAO_TriggerRule $trigger_rule) {
    $return = array();
    
    $p = explode("&", $this->params);
    $params = array();
    foreach ($p as $str) {
      $strArr = explode("=", $str);
      if (isset($strArr[0]) && isset($strArr[1]));
      $params[$strArr[0]] = $strArr[1];
    }
    
    $fields = array();
    foreach($objects as $entity => $obj) {
      $class = get_class($obj);
      $fields[$entity] = $class::fields();
    }
    
    $hooks = CRM_Utils_Hook::singleton();
    $hooks->invoke(5,
      $params, $return, $objects, $trigger_rule, $this,
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
        if (isset($objects[$entityName]) && $objects[$entityName] instanceof $entityType) {      
          if (isset($fields[$entityName]) && isset($fields[$entityName][$fieldName])) {
            $return[$key] = $objects[$entityName]->$fieldName;
          }
        }
      } 
      
      $matches = array();
      if (!isset($return[$key]) && preg_match("/{(.*)}/", $val, $matches)) {
        //value looks like {field}
        //check if field exist on objRef.
        foreach($fields as $entityName => $entityFields) {
          foreach($entityFields as $fieldName => $field) {
            if ($fieldName == $matches[1]) {
              if (isset($objects[$entityName])) {
                $return[$key] = $objects[$entityName]->$fieldName;
                break 2;
              }
            }
          }
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
    /**
     * Function to get values
     * 
     * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
     * @date 9 Apr 2014
     * @param array $params name/value pairs with field names/values
     * @return array $result found rows with data
     * @access public
     * @static
     */
    public static function getValues($params) {
        $result = array();
        $actionRule = new CRM_Triggers_BAO_ActionRule();
        if (!empty($params)) {
            $fields = self::fields();
            foreach ($params as $paramKey => $paramValue) {
                if (isset($fields[$paramKey])) {
                    $actionRule->$paramKey = $paramValue;
                }
            }
        }
        $actionRule->find();
        while ($actionRule->fetch()) {
            self::storeValues($actionRule, $row);
            $result[$row['id']] = $row;
        }
        return $result;
    }
    /**
     * Function to get single action with action_rule_id
     * 
     * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
     * @date 9 Apr 2014
     * @param int $actionRuleId
     * @return array $result found row with data
     * @access public
     * @static
     */
    public static function getByActionRuleId($actionRuleId) {
        $result = array();
        if (empty($actionRuleId)) {
            return $result;
        }
        $actionRule = new CRM_Triggers_BAO_ActionRule();
        $actionRule->id = $actionRuleId;
        $actionRule->find(true);
        self::storeValues($actionRule, $result);
        return $result;
    }
    /**
     * Function to add or update action rule
     * 
     * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
     * @date 9 Apr 2014
     * @param array $params 
     * @return array $result
     * @access public
     * @static
     */
    public static function add($params) {
        $result = array();
        CRM_Core_Error::debug('params', $params);
        if (empty($params)) {
            CRM_Core_Error::fatal('Params can not be empty when adding or updating an ActionRule');
        }
        $actionRule = new CRM_Triggers_BAO_ActionRule();
        $fields = self::fields();
        CRM_Core_Error::debug("fields", $fields);
        exit();
        foreach ($params as $paramKey => $paramValue) {
            if (isset($fields[$paramKey])) {
                $actionRule->$paramKey = $paramValue;
            }
        }
        $actionRule->save();
        self::storeValues($actionRule, $result);
        return $result;
    }
    /**
     * Function to delete action rule
     * 
     * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
     * @date 9 Apr 2014
     * @param int $actionRuleId 
     * @return boolean
     * @access public
     * @static
     */
    public static function deleteById($actionRuleId) {
        if (empty($actionRuleId)) {
            throw new Exception('ActionRuleId can not be empty when attempting to delete one');
        }
        $actionRule = new CRM_Triggers_BAO_ActionRule();
        $actionRule->id = $actionRuleId;
        $actionRule->delete();
        return TRUE;
    }
  
}



