# Data structure

The data structure consist of four tables. 

1. **civicrm_trigger_rule** the actual trigger
2. **civicrm_trigger_rule_condition** the condition's for a trigger, multiple conditions are combined as an and, e.g. condition 1 and condition 2
3. **civicrm_action_rule** the action to be executed
4. **civicrm_trigger_action** matching a trigger to an action

In short a trigger rule is something like 'when a contribution is updated'. A trigger condition is something like 'when status is completed and total amount is larger then 25 000'.
An action is something like 'add contact to a group' and the trigger action is this all combined.