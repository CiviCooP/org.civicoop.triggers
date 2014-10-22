<?php

/* 
 * This is the BAO file for the action rule table
 */
class CRM_Triggers_BAO_ActionRule extends CRM_Triggers_DAO_ActionRule {

  /**
   * Process the action for the given entities
   * 
   * @param $entities array of CRM_Core_DAO entities
   */
  public function processEntities($entities, CRM_Triggers_BAO_RuleSchedule $rule_schedule) {
    //retrieve contacts from the entity
    $contacts = array();
    if (!isset($entities['Contact'])) {
      //only retrieve contacts if no contact is present in the set
      $contacts = CRM_Triggers_Utils::getContactsFromEntities($entities);
    } else {
      $contact = $entities['Contact'];
      $contacts[$contact->id] = $contact;
    }

    $processCount = 0;
    $processedContacts = array();
    if ($this->process_contacts && count($contacts)) {
      foreach($contacts as $contact) {
        $objects = $entities;
        $objects['Contact'] = $contact;
        $params = $this->parseParams($objects, $rule_schedule);
        
        if ($this->checkForProcessing($objects, $params)) {        
          civicrm_api3($this->entity, $this->action, $params);
          $processedContacts[$contact->id] = $contact;
          $processCount++;
        }
      }
    } else {
      $params = $this->parseParams($objects, $rule_schedule);
      if ($this->checkForProcessing($objects, $params)) {        
        civicrm_api3($this->entity, $this->action, $params);
        $processCount++;
      }
    }
    
    //add an activity type and add this entity to the processed table        
      //we do that through the processed trigger BAO
    if ($processCount > 0) {
      CRM_Triggers_BAO_ProcessedTrigger::processTrigger($entities, $rule_schedule, $this, $processedContacts);
    }
    
    return $processCount;
  }
  
  protected function checkForProcessing($objects, $params) {
    $return = true;
    
    $hooks = CRM_Utils_Hook::singleton();
    $hooks->invoke(4,
      $return, $objects, $params, $this, CRM_Utils_Hook::$_nullObject,
      'civicrm_trigger_check_action_execution'
      );

    return $return;
  }
  
  /**
   * returns an array to be used with the api calls
   * 
   * @param array $objects array of objects identified by lower case entity name 'e.g. contribution'.
   * @return array
   */
  protected function parseParams($objects, CRM_Triggers_BAO_RuleSchedule $rule_schedule) {
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
      $params, $return, $objects, $rule_schedule, $this,
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
        $entity = $matches[1];
        $fieldName = $matches[2];
        
        if (isset($objects[$entity]) && isset($objects[$entity]->$fieldName)) {
          $return[$key] = $objects[$entity]->$fieldName;
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
    if (empty($params)) {
      CRM_Core_Error::fatal('Params can not be empty when adding or updating an ActionRule');
    }
    $actionRule = new CRM_Triggers_BAO_ActionRule();
    $fields = self::fields();
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
  /**
   * Function to check if there is an ActionRule with label
   * 
   * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
   * @date 6 May 2014
   * @param string $label
   * @return boolean
   * @access public
   * @static
   */
  public static function checkLabelExists($label) {
    $actionRules = self::getValues(array('label' => $label));
    if (empty($actionRules)) {
      return FALSE;
    } else {
      return TRUE;
    }
  }  
}



