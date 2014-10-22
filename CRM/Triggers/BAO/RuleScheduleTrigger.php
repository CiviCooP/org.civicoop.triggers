<?php

class CRM_Triggers_BAO_RuleScheduleTrigger extends CRM_Triggers_DAO_RuleScheduleTrigger {
  
  public static function findByRuleScheduleId($rule_schedule_id, $fetchFirst=FALSE) {
    $dao = new CRM_Triggers_BAO_RuleScheduleTrigger();
    $dao->rule_schedule_id = $rule_schedule_id;
    $dao->find($fetchFirst);
    return $dao;
  }
  
  public function getTriggerRule() {
    $trigger = CRM_Triggers_BAO_TriggerRule::getDaoByTriggerRuleId($this->trigger_rule_id);
    return $trigger;
  }
  
  /**
   * Returns a new QueryBuilder object for this trigger
   * 
   * @return CRM_Triggers_QueryBuilder
   */
  public function createQueryBuilder() {
    $trigger = $this->getTriggerRule();
    $daoClass = $trigger->getEntityDAOClass();
    $builder = new CRM_Triggers_QueryBuilder("`" . $daoClass::getTableName() . "` `".$trigger->getTableAlias()."`");
    return $builder;
  }
  
  public function addSelectToQueryBuilder(CRM_Triggers_QueryBuilder $builder) {
    $trigger = $this->getTriggerRule();
    $daoClass = $trigger->getEntityDAOClass();
    $fields = $daoClass::fields();
    $table_alias = $trigger->getTableAlias();
    
    foreach($fields as $field) {
      if (isset($field['name'])) {
        $builder->addSelect("`".$table_alias."`.`".$field['name']."` AS `".$table_alias."_".$field['name']."`");
      }
    }
  }
  
  /**
   * Adds the trigger conditions to the query builder
   * 
   * @param CRM_Triggers_QueryBuilder $builder
   */
  public function addTriggerConditionsToQueryBuilder(CRM_Triggers_QueryBuilder $builder) {
    //build condition for this dao
    $trigger = $this->getTriggerRule();
    $wheres = new CRM_Triggers_QueryBuilder_Subcondition();
    $where = new CRM_Triggers_QueryBuilder_Subcondition();
    $having = new CRM_Triggers_QueryBuilder_Subcondition();
    if (strlen($this->logic_operator)) {
      $wheres->linkToPrevious = $this->logic_operator;
      $having->linkToPrevious = $this->logic_operator;
    }
    $conditions = CRM_Triggers_BAO_TriggerRuleCondition::findByTriggerRuleId($trigger->id);
    $alreadyProcessedConditions = new CRM_Triggers_QueryBuilder_Subcondition();
    $alreadyProcessedConditions->linkToPrevious = 'AND';
    while($conditions->fetch()) {
      $parser = $conditions->getParser();
      $parser->parseCondition($where, $having, $builder, $trigger);
      $parser->addAlreadyProcessedCondition($alreadyProcessedConditions, $this);
    }
    
    $hooks = CRM_Utils_Hook::singleton();
    $hooks->invoke(5,
      $this, $builder, $where, $having, $trigger,
      'civicrm_trigger_condition_parse'
      );
    
    $wheres->addCond($where);    
    $wheres->addCond($alreadyProcessedConditions);
    
    //add the conditions to the query builder
    $builder->addWhere($wheres);
    $builder->addHaving($having);
  }
  /**
   * Function to get schedule triggers
   * 
   * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
   * @date 14 Apr 2014
   * @param array $params name/value pairs with field names/values
   * @return array $result found rows with data
   * @access public
   * @static
   */
  public static function getValues($params) {
    $result = array();
    $ruleScheduleTrigger = new CRM_Triggers_BAO_RuleScheduleTrigger();
    if (!empty($params)) {
      $fields = self::fields();
      foreach ($params as $paramKey => $paramValue) {
        if (isset($fields[$paramKey])) {
          $ruleScheduleTrigger->$paramKey = $paramValue;
        }
      }
    }
    $ruleScheduleTrigger->find();
    while ($ruleScheduleTrigger->fetch()) {
      self::storeValues($ruleScheduleTrigger, $row);
      $result[$row['id']] = $row;
    }
    return $result;
  }
  /**
   * Function to add or update rule schedule trigger
   * 
   * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
   * @date 15 Apr 2014
   * @param array $params 
   * @return array $result
   * @access public
   * @static
   */
  public static function add($params) {
    $result = array();
    if (empty($params)) {
      CRM_Core_Error::fatal('Params can not be empty when adding or updating a RuleScheduleTrigger');
    }
    $ruleScheduleTrigger = new CRM_Triggers_BAO_RuleScheduleTrigger();
    $fields = self::fields();
    foreach ($params as $paramKey => $paramValue) {
      if (isset($fields[$paramKey])) {
        $ruleScheduleTrigger->$paramKey = $paramValue;
      }
    }
    $ruleScheduleTrigger->save();
    self::storeValues($ruleScheduleTrigger, $result);
    unset($ruleScheduleTrigger);
    return $result;
  }
  
}

