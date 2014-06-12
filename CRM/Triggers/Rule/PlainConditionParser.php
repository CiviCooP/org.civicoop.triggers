<?php

/* 
 * Plain condition parser
 * 
 */

class CRM_Triggers_Rule_PlainConditionParser extends CRM_Triggers_Rule_BaseConditionParser implements CRM_Triggers_Rule_ConditionParserInterface {
  
  /**
   * 
   * @param CRM_Triggers_QueryBuilder_Subcondition $where
   * @param CRM_Triggers_QueryBuilder_Subcondition $having
   * @param CRM_Triggers_QueryBuilder $builder
   */
  public function parseCondition(CRM_Triggers_QueryBuilder_Subcondition $where, CRM_Triggers_QueryBuilder_Subcondition $having, CRM_Triggers_QueryBuilder $builder) {
      $sqlFieldName = $this->getSqlFieldName($this->trigger_rule_condition->field_name, $builder);
      
      if ($sqlFieldName === false) {
        throw new CRM_Triggers_Exception_InvalidCondition("Invalid field '".$this->trigger_rule_condition->field_name."'");
      }
    
      if ($this->trigger_rule_condition->operation == "IS NOT EMPTY") {
        $strCond = "(".$sqlFieldName." IS NOT NULL OR ".$sqlFieldName ." != '')";
      } elseif ($this->trigger_rule_condition->operation == "IS EMPTY") {
        $strCond = "(".$sqlFieldName." IS NULL OR ".$sqlFieldName ." = '')";
      } else {
        $strCond = "".$sqlFieldName."";
        $strCond .= " ".$this->trigger_rule_condition->operation." ";
        $strCond .= $this->escapeValue($this->trigger_rule_condition->value);
      }
      
      $cond = new CRM_Triggers_QueryBuilder_Condition($strCond);
      $where->addCond($cond);  
  }
  
  public function addAlreadyProcessedCondition(CRM_Triggers_QueryBuilder_Subcondition $alreadyProcessedConditions, CRM_Triggers_BAO_RuleScheduleTrigger $rule_schedule_trigger) {
    $trigger = CRM_Triggers_BAO_TriggerRule::getDaoByTriggerRuleId($this->trigger_rule_condition->trigger_rule_id);
    $entityDAOClass = $trigger->getEntityDAOClass();
    
    //add a join on civicrm_processed_trigger
    $alreadyProcessedCond = new CRM_Triggers_QueryBuilder_Condition("`".$entityDAOClass::getTableName() ."`.`id` NOT IN ("
        . "SELECT `entity_id` FROM `civicrm_processed_trigger` "
        . "WHERE `entity` = ".$this->escapeValue($trigger->entity, true)." "
        . "AND `rule_schedule_id` = ".$this->escapeValue($rule_schedule_trigger->rule_schedule_id, true).")");
    
    $alreadyProcessedConditions->addCond($alreadyProcessedCond);
  }
  
}

