<?php

/* 
 * Aggrgate condition parser
 * 
 */

class CRM_Triggers_Rule_AggregateConditionParser extends CRM_Triggers_Rule_BaseConditionParser implements CRM_Triggers_Rule_ConditionParserInterface {
  
  /**
   * 
   * @param CRM_Triggers_QueryBuilder_Subcondition $where
   * @param CRM_Triggers_QueryBuilder_Subcondition $having
   * @param CRM_Triggers_QueryBuilder $builder
   */
  public function parseCondition(CRM_Triggers_QueryBuilder_Subcondition $where, CRM_Triggers_QueryBuilder_Subcondition $having, CRM_Triggers_QueryBuilder $builder) {
      $sqlFieldName = $this->getSqlFieldName($this->trigger_rule_condition->field_name, $builder);
      $sqlFieldAlias = $this->getSqlFieldAlias($this->trigger_rule_condition->field_name, $builder);
      
      if ($sqlFieldName === false || $sqlFieldAlias === false) {
        throw new CRM_Triggers_Exception_InvalidCondition("Invalid field '".$this->trigger_rule_condition->field_name."'");
      }
    
      $strCond = $this->trigger_rule_condition->aggregate_function ."(".$sqlFieldName.")";
      $builder->addSelect($strCond . " AS ` ".$sqlFieldAlias."`");
      
      $strCond .= " ".$this->trigger_rule_condition->operation." ";
      $strCond .= $this->escapeValue($this->trigger_rule_condition->value);
      
      $cond = new CRM_Triggers_QueryBuilder_Condition($strCond);
      $having->addCond($cond);
      
      $sqlGroupingFieldName = $this->getSqlFieldName($this->trigger_rule_condition->grouping_field, $builder);
      
      if ($sqlGroupingFieldName === false) {
        throw new CRM_Triggers_Exception_InvalidCondition("Invalid field '".$this->trigger_rule_condition->grouping_field."'");
      }
      $builder->addGroupBy($sqlGroupingFieldName);
  }
  
  public function addAlreadyProcessedCondition(CRM_Triggers_QueryBuilder_Subcondition $alreadyProcessedConditions, CRM_Triggers_BAO_RuleScheduleTrigger $rule_schedule_trigger) {
    //do not add a condition to check if entity is already processed
  }
  
}

