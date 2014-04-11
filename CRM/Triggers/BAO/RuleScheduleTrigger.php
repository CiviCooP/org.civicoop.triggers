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
    $builder = new CRM_Triggers_QueryBuilder("`" . $daoClass::getTableName() . "`");
    return $builder;
  }
  
  public function addSelectToQueryBuilder(CRM_Triggers_QueryBuilder $builder) {
    $trigger = $this->getTriggerRule();
    $daoClass = $trigger->getEntityDAOClass();
    $fields = $daoClass::fields();
    $table = $daoClass::getTableName();
    foreach($fields as $field) {
      if (isset($field['name'])) {
        $builder->addSelect("`".$table."`.`".$field['name']."` AS `".$table."_".$field['name']."`");
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
  
}

