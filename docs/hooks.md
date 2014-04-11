# Hooks

## civicrm_trigger_condition_parse

This hook is invoked on the moment the condition is parsed to a sql condition on the query builder

### Parameters

1. `CRM_Triggers_BAO_TriggerRuleSchedule $rule_schedule`
2. `CRM_Triggers_QueryBuilder $builder`
3. `CRM_Triggers_QueryBuilder_Subcondition $where`
4. `CRM_Triggers_QueryBuilder_Subcondition $having`
5. `CRM_Triggers_BAO_TriggerRule $trigger_rule`

### Example

Below an example of usage of hook which will set the is_active condition on the dao when the field_name is contact_id. This example is probably useless :-)

    function hook_civicrm_trigger_condition_parse(CRM_Triggers_BAO_TriggerRuleSchedule $rule_schedule, CRM_Triggers_QueryBuilder $builder CRM_Triggers_QueryBuilder_Subcondition $where, CRM_Triggers_QueryBuilder_Subcondition $having, CRM_Triggers_BAO_TriggerRule $trigger_rule) {
        if ($rule_schedule->name == 'start_donor_journey') {
            $cond = new CRM_Triggers_QueryBuilder_Condition("is_active = 1");
            $where->addCondition($cond); //only active contacts
        }
    }

## civicrm_trigger_action_parse_params

This hook is invoked on the moment the parameters for the action execution are parsed.

The definition of this hook looks like
    
    function hook_civicrm_trigger_action_parse_params(&$return, $params, $objects, CRM_Triggers_BAO_TriggerRule $trigger_rule, CRM_Triggers_BAO_ActionRule $action);

You can set parameters in the variable `$return`.

### Parameters

1. `$return` This is an array you can set which is used the execution of the action
2. `$params` this is an array with the source parameters
3. `array $objects` this is the entity which is processed
4. `CRM_Triggers_BAO_RuleSchedule $rule_schedule` this is actual trigger
5. `CRM_Triggers_BAO_ActionRule $action` this is the actual action

### Example

    function hook_civicrm_trigger_action_parse_params(&$return, $params, $objects, CRM_Triggers_BAO_RuleSchedule $rule_schedule, CRM_Triggers_BAO_ActionRule $action) {
        if ($action->name == 'GroupMovement' and $action->entity == 'GroupContact') {
            $return['group_id'] = 21;//use group 21 for the action
        }
    }

## civicrm_trigger_pre_execute_entity_query

Todo

## civicrm_trigger_check_action_execution

Todo