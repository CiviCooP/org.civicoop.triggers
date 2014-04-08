# Hooks

## civicrm_trigger_condition_parse

This hook is invoked on the moment the condition is parsed to a sql condition on a dao.

### Parameters

1. *CRM_Triggers_BAO_TriggerRuleCondition* $condition
2. *CRM_Core_DAO* $dao 

### Example

Below an example of usage of hook which will set the is_active condition on the dao when the field_name is contact_id. This example is probably useless :-)

    function hook_civicrm_trigger_condition_parse(CRM_Triggers_BAO_TriggerRuleCondition $condition, CRM_Core_DAO $dao) {
        if ($condition->field_name == 'contact_id') {
            $dao->addWhere("is_active = '1'"); //only active contacts
        }
    }