<?php

/*
 * This is the BAO file for the trigger action table
 */

class CRM_Triggers_BAO_TriggerRule extends CRM_Triggers_DAO_TriggerRule {

  private $entity_dao_class_name = false;
  private $entity_dao = false;
  private static $trigger_rules = array();

  /**
   * Function to get values
   * 
   * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
   * @date 8 Apr 2014
   * @param array $params name/value pairs with field names/values
   * @return array $result found rows with data
   * @access public
   * @static
   */
  public static function getValues($params) {
    $result = array();
    $triggerRule = new CRM_Triggers_BAO_TriggerRule();
    if (!empty($params)) {
      $fields = self::fields();
      foreach ($params as $paramKey => $paramValue) {
        if (isset($fields[$paramKey])) {
          $triggerRule->$paramKey = $paramValue;
        }
      }
    }
    $triggerRule->find();
    while ($triggerRule->fetch()) {
      self::storeValues($triggerRule, $row);
      $result[$row['id']] = $row;
    }
    return $result;
  }

  /**
   * Function to get single trigger with trigger_rule_id
   * 
   * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
   * @date 8 Apr 2014
   * @param int $triggerRuleId
   * @return array $result found row with data
   * @access public
   * @static
   */
  public static function getByTriggerRuleId($triggerRuleId) {
    $result = array();
    if (empty($triggerRuleId)) {
      return $result;
    }
    $triggerRule = self::getDaoByTriggerRuleId($triggerRuleId);
    self::storeValues($triggerRule, $result);
    return $result;
  }

  /**
   * Returns a trigger rule BAO by its id
   * 
   * @param type $triggerRuleId
   * @return CRM_Triggers_BAO_TriggerRule
   */
  public static function getDaoByTriggerRuleId($triggerRuleId) {
    if (!isset(self::$trigger_rules[$triggerRuleId])) {
      $result = array();
      if (empty($triggerRuleId)) {
        return $result;
      }
      $triggerRule = new CRM_Triggers_BAO_TriggerRule();
      $triggerRule->id = $triggerRuleId;
      $triggerRule->find(true);
      self::$trigger_rules[$triggerRuleId] = $triggerRule;
    }
    return self::$trigger_rules[$triggerRuleId];
  }

  /**
   * Function to add or update trigger rule
   * 
   * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
   * @date 8 Apr 2014
   * @param array $params 
   * @return array $result
   * @access public
   * @static
   */
  public static function add($params) {
    $result = array();
    if (empty($params)) {
      CRM_Core_Error::fatal('Params can not be empty when adding or updating a TriggerRule');
    }
    $triggerRule = new CRM_Triggers_BAO_TriggerRule();
    $fields = self::fields();
    foreach ($params as $paramKey => $paramValue) {
      if (isset($fields[$paramKey])) {
        $triggerRule->$paramKey = $paramValue;
      }
    }
    $triggerRule->save();
    self::storeValues($triggerRule, $result);
    return $result;
  }

  /**
   * Function to delete trigger rule
   * 
   * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
   * @date 9 Apr 2014
   * @param int $triggerRuleId 
   * @return boolean
   * @access public
   * @static
   */
  public static function deleteById($triggerRuleId) {
    if (empty($triggerRuleId)) {
      throw new Exception('TriggerRuleId can not be empty when attempting to delete one');
    }
    $triggerRule = new CRM_Triggers_BAO_TriggerRule();
    $triggerRule->id = $triggerRuleId;
    $triggerRule->delete();
    return TRUE;
  }

  /**
   * 
   * @return CRM_Core_DAO the dao belonging to the entity
   */
  public function getEntityDAO() {
    if ($this->entity_dao === false) {
      $class = $this->getEntityDAOClass();
      $this->entity_dao = new $class();
    }
    return $this->entity_dao;
  }

  public function getEntityDAOClass() {
    if ($this->entity_dao_class_name === false) {
      $dao = CRM_Core_DAO_AllCoreTables::getFullName($this->entity);
      if ($dao == NULL) {
        throw new CRM_Triggers_Exception_DAO_Not_Found("Entity " . $this->entity . " has no DAO");
      }
      $this->entity_dao_class_name = $dao;
    }
    return $this->entity_dao_class_name;
  }

}
