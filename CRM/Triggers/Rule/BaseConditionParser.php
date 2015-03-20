<?php

/* 
 * Base class for a condition parser
 * 
 */

abstract class CRM_Triggers_Rule_BaseConditionParser {
  
  /**
   *
   * @var array of custom groups instances
   */
  private static $custom_groups = array();

  /**
   *
   * @var array of custom field instances 
   */
  private static $custom_fields = array();
  
  protected $trigger_rule_condition;
  
  public function __construct(CRM_Triggers_BAO_TriggerRuleCondition $trigger_rule_condition) {
    $this->trigger_rule_condition = $trigger_rule_condition;
  }
  
  protected function getSqlFieldName($field_name, CRM_Triggers_QueryBuilder $builder) {
    $trigger = CRM_Triggers_BAO_TriggerRule::getDaoByTriggerRuleId($this->trigger_rule_condition->trigger_rule_id);
    $entityDAO = $trigger->getEntityDAO();
    $entityDAOClass = $trigger->getEntityDAOClass();
    $table_alias = $trigger->getTableAlias();
    
    //check if field exist in DAO    
    $sqlFieldName = false;
    $field = CRM_Triggers_Utils::getFieldFromDao($entityDAO, $field_name);
    if ($field !== false) {
      $sqlFieldName = $this->parseField($field, $table_alias, $builder);
    }
    
    return $sqlFieldName;
  }
  
  protected function getSqlFieldAlias($field_name, CRM_Triggers_QueryBuilder $builder) {
    $trigger = CRM_Triggers_BAO_TriggerRule::getDaoByTriggerRuleId($this->trigger_rule_condition->trigger_rule_id);
    $entityDAO = $trigger->getEntityDAO();
    $entityDAOClass = $trigger->getEntityDAOClass();
    $table_alias = $trigger->getTableAlias();
    
    //check if field exist in DAO    
    $sqlFieldName = false;
    $field = CRM_Triggers_Utils::getFieldFromDao($entityDAO, $field_name);
    if ($field !== false) {
      $sqlFieldName = $this->parseField($field, $table_alias, $builder);
      if ($sqlFieldName) {
        //replace the dors in underscore and remove the `
        $sqlFieldName = str_replace(".", "_", $sqlFieldName);
        $sqlFieldName = str_replace("`", "", $sqlFieldName);
      }
    }
    
    return $sqlFieldName;
  }
  
  /**
   * Escapes the value based on the special processing value
   * 
   * @param type $value
   */
  protected function escapeValue($value, $alwaysEscape = false) {
    if (!strlen($value)) {
      return '';
    }
    if ($this->trigger_rule_condition->special_processing && $alwaysEscape === false) {
      return $value;
    }
    return "'".CRM_Core_DAO::escapeString($value)."'";
  }
  
  /**
   * returns the alias field to use in the query and optionally add joins for custom field/tables
   * 
   * @param type $field
   * @param CRM_Core_DAO $dao
   * @return type
   */
  private function parseField($field, $table_name, CRM_Triggers_QueryBuilder $builder) {
    $fieldName = "`" . $table_name . "`.`" . $field['name'] . "`";

    //if the field is a custom field add the column and table as a join
    if (isset($field['custom_group_id'])) {
      $gid = $field['custom_group_id'];

      if (!isset(self::$custom_groups[$gid])) {
        $api_result = civicrm_api('CustomGroup', 'getsingle', array('id' => $gid, 'version' => 3));
        if (isset($api_result['is_error']) && $api_result['is_error']) {
          throw new API_Exception('API Error: CustomGroup.getsingle');
        }
        self::$custom_groups[$gid] = $api_result;
      }
      if (!isset(self::$custom_fields[$gid])) {
        $fields = civicrm_api('CustomField', 'get', array('custom_group_id' => $gid, 'version' => 3));
        if (isset($fields['is_error']) && $fields['is_error']) {
          throw new API_Exception('API Error: CustomField.get');
        }
        foreach ($fields['values'] as $f) {
          self::$custom_fields[$gid]['custom_' . $f['id']] = $f;
        }
      }
      $cgroup = self::$custom_groups[$gid];
      $cfield = self::$custom_fields[$gid][$field['name']];

      $join = " LEFT JOIN `" . $cgroup['table_name'] . "` AS `group_" . $gid . "` ON `" . $table_name . "`.`id` = `group_" . $gid . "`.`entity_id`";
      $builder->addJoin($join, "group_" . $gid);
      $fieldName = "`group_" . $gid . "`.`" . $cfield['column_name'] . "`";
      $builder->addSelect("`group_" . $gid . "`.`" . $cfield['column_name'] . "` AS `" . $table_name . "_" . $field['name'] . "`");
    }
    return $fieldName;
  }
}
