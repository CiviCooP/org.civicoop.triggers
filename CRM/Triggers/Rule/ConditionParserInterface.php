<?php

/* 
 * Interface which a class should implement if it is a condition parser
 * A condition parsers parses a condition and adds it to the query builder
 * 
 */

interface CRM_Triggers_Rule_ConditionParserInterface {
  
  /**
   * Parse the condition resulting in new where, new having conditions
   */
  public function parseCondition(CRM_Triggers_QueryBuilder_Subcondition $where, CRM_Triggers_QueryBuilder_Subcondition $having, CRM_Triggers_QueryBuilder $builder);
  
  /**
   * add a condition if needed to the query builder to determine if the entity is already processed
   * 
   * @param CRM_Triggers_QueryBuilder_Subcondition $alreadyProcessedConditions 
   */
  public function addAlreadyProcessedCondition(CRM_Triggers_QueryBuilder_Subcondition $alreadyProcessedConditions, CRM_Triggers_BAO_RuleScheduleTrigger $rule_schedule_trigger);
  
}

