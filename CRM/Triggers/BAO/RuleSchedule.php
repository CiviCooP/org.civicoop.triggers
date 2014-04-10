<?php

/*
 * This is the BAO file for the trigger action table
 */

class CRM_Triggers_BAO_RuleSchedule extends CRM_Triggers_DAO_RuleSchedule {

  protected static $processedDaoClasses = array();
  
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
   * Returns the found entities which should be processed by the trigger
   */
  public function findEntities() {
    $rule_schedule_trigger = CRM_Triggers_BAO_RuleScheduleTrigger::findByRuleScheduleId($this->id, false);
    $builder = false;
    $daoClass = false;
    self::$processedDaoClasses[$this->id] = array(); //reset the processed dao classes
    while ($rule_schedule_trigger->fetch()) {
      if ($builder === false) {
        $builder = $rule_schedule_trigger->createQueryBuilder();
        $daoClass = $rule_schedule_trigger->getTriggerRule()->getEntityDAOClass();
      } elseif (!isset(self::$processedDaoClasses[$this->id][$rule_schedule_trigger->id])) {
        //build a join for the trigger table
        $this->addJoinedTriggerToQueryBuilder($rule_schedule_trigger, $builder);
      }
      
      if (!isset(self::$processedDaoClasses[$this->id][$rule_schedule_trigger->id])) {
        $rule_schedule_trigger->addTriggerConditionsToQueryBuilder($builder);
        self::$processedDaoClasses[$this->id][$rule_schedule_trigger->id] = $rule_schedule_trigger->getTriggerRule()->getEntityDAOClass();
      }
    }
echo $builder->toSql(); exit();
    $entityDao = CRM_Core_DAO::executeQuery($builder->toSql(), array(), TRUE, $daoClass);
    return $entityDao;
  }
  
  protected function addJoinedTriggerToQueryBuilder(CRM_Triggers_BAO_RuleScheduleTrigger $rule_schedule_trigger, CRM_Triggers_QueryBuilder $builder) {
    $daoClass = $rule_schedule_trigger->getTriggerRule()->getEntityDAOClass();
    $joinCondition = CRM_Triggers_Utils_JoinTrigger::createJoinCondition(self::$processedDaoClasses[$this->id], $daoClass);
    
    $table = $daoClass::getTableName();
    $joinStatement = " LEFT JOIN `".$table."` ON (".$joinCondition->toSqlCondition().")";
    $builder->addJoin($joinStatement, $table);    
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
    $rule_schedule = new CRM_Triggers_BAO_RuleSchedule();
    if (!empty($params)) {
      $fields = self::fields();
      foreach ($params as $paramKey => $paramValue) {
        if (isset($fields[$paramKey])) {
          $rule_schedule->$paramKey = $paramValue;
        }
      }
    }
    $rule_schedule->find();
    while ($rule_schedule->fetch()) {
      self::storeValues($rule_schedule, $row);
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

}
