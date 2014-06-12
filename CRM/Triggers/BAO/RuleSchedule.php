<?php

/*
 * This is the BAO file for the trigger action table
 */

class CRM_Triggers_BAO_RuleSchedule extends CRM_Triggers_DAO_RuleSchedule {

  protected static $processedTriggers = array();
  
  /**
   * Find trigger actions which are ready for processing
   * 
   * @param bool $fetchFirst fetch the first record
   */
  public static function findForProcessing($fetchFirst = FALSE) {
    $rule_schedule = new CRM_Triggers_BAO_RuleSchedule();

    $rule_schedule->selectAdd();
    $rule_schedule->selectAdd("*");
    $rule_schedule->whereAdd('is_active = 1');
    $rule_schedule->whereAdd('(start_date IS NULL OR start_date <= NOW())');
    $rule_schedule->whereAdd('(end_date IS NULL OR end_date >= NOW())');
    $rule_schedule->whereAdd('(next_run <= NOW())');

    $rule_schedule->find($fetchFirst);

    return $rule_schedule;
  }
  
  /**
   * Process this rule schedule
   * 
   * Returns the number of actions executed
   */
  public function process() {
    $action = CRM_Triggers_BAO_ActionRule::findByActionId($this->action_rule_id);
    $entityQuery = $this->executeEntityQuery();
    $count = 0;
    while ($entityQuery->fetch()) {
      $entities = $this->convertQueryToEntities($entityQuery);
      $processCount = $action->processEntities($entities, $this);
      $count = $count + $processCount;
    }
    return $count;
  }
  
  /**
   * Converts a result of query to an array containting the entities used in the query
   * @param CRM_Core_DAO $entityQuery
   */
  protected function convertQueryToEntities(CRM_Core_DAO $entityQuery) {
    if (!isset(self::$processedTriggers[$this->id])) {
      return;
    }
    $entities = array();
    $fields = $entityQuery->toArray();
    foreach(self::$processedTriggers[$this->id] as $trigger) {
      $dao_class = $trigger->getEntityDAOClass();
      $dao = new $dao_class();
      $table = $dao_class::getTableName();
      foreach($fields as $key => $val) {
        if (strpos($key, $table."_")===0) {
          $fieldName = str_replace($table."_", "", $key);
          $dao->$fieldName = $val;
        }
      }
      $entities[$trigger->entity] = $dao;
    }
    return $entities;
  }

  /**
   * Returns the found entities which should be processed by the trigger
   */
  protected function executeEntityQuery() {
    $rule_schedule_trigger = CRM_Triggers_BAO_RuleScheduleTrigger::findByRuleScheduleId($this->id, false);
    $builder = false;
    $daoClass = false;
    self::$processedTriggers[$this->id] = array(); //reset the processed dao classes
    while ($rule_schedule_trigger->fetch()) {
      if ($builder === false) {
        $builder = $rule_schedule_trigger->createQueryBuilder();
        $daoClass = $rule_schedule_trigger->getTriggerRule()->getEntityDAOClass();
      } elseif (!isset(self::$processedTriggers[$this->id][$rule_schedule_trigger->id])) {
        //build a join for the trigger table
        $this->addJoinedTriggerToQueryBuilder($rule_schedule_trigger, $builder);
      }
      
      if (!isset(self::$processedTriggers[$this->id][$rule_schedule_trigger->id])) {
        $rule_schedule_trigger->addTriggerConditionsToQueryBuilder($builder);
        $rule_schedule_trigger->addSelectToQueryBuilder($builder);
        self::$processedTriggers[$this->id][$rule_schedule_trigger->id] = $rule_schedule_trigger->getTriggerRule();
      }
    }
    
    $hooks = CRM_Utils_Hook::singleton();
    $hooks->invoke(3,
      $this, $builder, self::$processedTriggers[$this->id], CRM_Utils_Hook::$_nullObject, CRM_Utils_Hook::$_nullObject,
      'civicrm_trigger_pre_execute_entity_query'
      );

    $entityDao = CRM_Core_DAO::executeQuery($builder->toSql(), array(), TRUE, $daoClass);
    
    return $entityDao;
  }
  
  /**
   * Add trigger to the query builder with a join (this is useful for combining multiple triggers)
   * 
   * @param CRM_Triggers_BAO_RuleScheduleTrigger $rule_schedule_trigger
   * @param CRM_Triggers_QueryBuilder $builder
   */
  protected function addJoinedTriggerToQueryBuilder(CRM_Triggers_BAO_RuleScheduleTrigger $rule_schedule_trigger, CRM_Triggers_QueryBuilder $builder) {
    $daoClass = $rule_schedule_trigger->getTriggerRule()->getEntityDAOClass();
    $joinCondition = CRM_Triggers_Utils_JoinTrigger::createJoinCondition($this->getProcessedDAOClasses(), $daoClass);
    
    if ($joinCondition) {
      $table = $daoClass::getTableName();
      $joinStatement = " LEFT JOIN `".$table."` ON (".$joinCondition->toSqlCondition().")";
      $builder->addJoin($joinStatement, $table);    
    }
  }
  
  /** 
   * Returns an array with the class names of the dao's belonging to the processed triggers
   */
  protected function getProcessedDAOClasses() {
    $return = array();
    if (isset(self::$processedTriggers[$this->id])) {
      foreach(self::$processedTriggers[$this->id] as $trigger) {
        $return[] = $trigger->getEntityDAOClass();
      }
    }
    return $return;
  }

  /**
   * Function to get conditions
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
    $ruleSchedule = new CRM_Triggers_BAO_RuleSchedule();
    if (!empty($params)) {
      $fields = self::fields();
      foreach ($params as $paramKey => $paramValue) {
        if (isset($fields[$paramKey])) {
          $ruleSchedule->$paramKey = $paramValue;
        }
      }
    }
    $ruleSchedule->find();
    while ($ruleSchedule->fetch()) {
      $row = array();
      self::storeValues($ruleSchedule, $row);
      $result[$row['id']] = $row;
    }
    return $result;
  }

  /**
   * Reschedule this trigger/action combination and set last run date
   */
  public function reschedule() {
    $this->last_run = date('YmdHis');

    if (strlen($this->schedule)) {
      $date = new DateTime();
      $date->modify($this->schedule);
      $this->next_run = $date->format('YmdHis');
    }

    $this->save();
  }
  /**
   * Function to add or update rule schedule
   * 
   * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
   * @date 11 Apr 2014
   * @param array $params 
   * @return array $result
   * @access public
   * @static
   */
  public static function add($params) {
    $result = array();
    if (empty($params)) {
      CRM_Core_Error::fatal('Params can not be empty when adding or updating a RuleSchedule');
    }
    $ruleSchedule = new CRM_Triggers_BAO_RuleSchedule();
    $fields = self::fields();
    foreach ($params as $paramKey => $paramValue) {
      if (isset($fields[$paramKey])) {
        $ruleSchedule->$paramKey = $paramValue;
      }
    }
    $ruleSchedule->reschedule();
    $ruleSchedule->save();
    self::storeValues($ruleSchedule, $result);
    unset($ruleSchedule);
    return $result;
  }
  /**
   * Function to delete rule schedule
   * 
   * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
   * @date 11 Apr 2014
   * @param int $ruleScheduleId 
   * @return boolean
   * @access public
   * @static
   */
  public static function deleteById($ruleScheduleId) {
    if (empty($ruleScheduleId)) {
        throw new Exception('ruleScheduleId can not be empty when attempting to delete one');
    }
    $ruleSchedule = new CRM_Triggers_BAO_RuleSchedule();
    $ruleSchedule->id = $ruleScheduleId;
    $ruleSchedule->delete();
    return TRUE;
  }
  /**
   * Function to get single rule schedule with ruleScheduleId
   * 
   * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
   * @date 14 Apr 2014
   * @param int $ruleScheduleId
   * @return array $result found row with data
   * @access public
   * @static
   */
  public static function getByRuleScheduleId($ruleScheduleId) {
    $result = array();
    if (empty($ruleScheduleId)) {
      return $result;
    }
    $ruleSchedule = new CRM_Triggers_BAO_RuleSchedule();
    $ruleSchedule->id = $ruleScheduleId;
    $ruleSchedule->find(true);
    self::storeValues($ruleSchedule, $result);
    return $result;
  }
  /**
   * Function to check if there is a ScheduleRule with label
   * 
   * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
   * @date 6 May 2014
   * @param string $label
   * @return boolean
   * @access public
   * @static
   */
  public static function checkLabelExists($label) {
    $ruleSchedule = self::getValues(array('label' => $label));
    if (empty($ruleSchedule)) {
      return FALSE;
    } else {
      return TRUE;
    }
  }  
}
