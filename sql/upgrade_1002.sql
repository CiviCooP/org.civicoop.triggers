ALTER TABLE `civicrm_trigger_rule_condition`
  DROP `old_value`,
  DROP `old_op`;

ALTER TABLE  `civicrm_trigger_rule_condition` 
CHANGE  `new_value`  `value` TEXT NOT NULL ,
CHANGE  `new_op`  `operation` varchar( 255 ) NOT NULL ;