<?php

/* 
 * This is the BAO file for the trigger action table
 */

class CRM_Triggers_BAO_TriggerRuleCondition extends CRM_Triggers_DAO_TriggerRuleCondition {
  
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
  
  /**
     * Function to get conditions
     * 
     * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
     * @date 8 Apr 2014
     * @param array $params name/value pairs with field names/values
     * @return array $result found rows with data
     * @access public
     * @static
     */
    public static function getValues($params) {
        $result = array();
        $condition = new CRM_Triggers_BAO_TriggerRuleCondition();
        if (!empty($params)) {
            $fields = self::fields();
            foreach ($params as $paramKey => $paramValue) {
                if (isset($fields[$paramKey])) {
                    $condition->$paramKey = $paramValue;
                }
            }
        }
        $condition->find();
        while ($condition->fetch()) {
            $row = array();
            self::storeValues($condition, $row);
            $result[$row['id']] = $row;
        }
        return $result;
    }
  
  /**
   * Parse the condition and adds it to to the qb of the entity
   * 
   * @param CRM_Triggers_QueryBuilder_Subcondition $where
   * @param CRM_Triggers_QueryBuilder_Subcondition $having
   * @param CRM_Triggers_QueryBuilder $builder
   * @throws CRM_Triggers_Exception_InvalidCondition
   */
  public function parseCondition(CRM_Triggers_QueryBuilder_Subcondition $where, CRM_Triggers_QueryBuilder_Subcondition $having, CRM_Triggers_QueryBuilder $builder) {
    $trigger = CRM_Triggers_BAO_TriggerRule::getDaoByTriggerRuleId($this->trigger_rule_id);
    $entityDAO = $trigger->getEntityDAO();
    $entityDAOClass = $trigger->getEntityDAOClass();
    
    //check if field exist in DAO    
    $sqlFieldName = false;
    $field = CRM_Triggers_Utils::getFieldFromDao($entityDAO, $this->field_name);
    if ($field !== false) {
      $sqlFieldName = $this->parseField($field, $entityDAOClass::getTableName(), $builder);
    }
    
    if ($sqlFieldName === false) {
      throw new CRM_Triggers_Exception_InvalidCondition("Invalid field '".$this->field_name."'");
    }

    //determine if this condition is an aggregation
    $is_aggregate = false;
    if (!empty($this->aggregate_function) && !empty($this->grouping_field)) {
      $is_aggregate = true;
    }
    
    if ($is_aggregate) {
      $strCond = $this->aggregate_function ."(".$sqlFieldName.")";
      $builder->addSelect($strCond . " AS `".$sqlFieldName."`");
      
      $strCond .= " ".$this->operation." ";
      $strCond .= " '".$entityDAO->escape($this->value)."'";
      
      $cond = new CRM_Triggers_QueryBuilder_Condition($strCond);
      $having->addCond($cond);
           
      $sqlGroupingFieldName = false;
      $groupField = CRM_Triggers_Utils::getFieldFromDao($entityDAO, $this->field_name);    
      if ($groupField !== false) {
        $sqlGroupingFieldName = $this->parseField($groupField, $entityDAOClass::getTableName(), $builder);
      }
      
      if ($sqlGroupingFieldName === false) {
        throw new CRM_Triggers_Exception_InvalidCondition("Invalid field '".$this->grouping_field."'");
      }
      $builder->addGroupBy($sqlGroupingFieldName);
      
    } else {
      $strCond = "".$sqlFieldName."";
      $strCond .= " ".$this->operation." ";
      $strCond .= " '".$entityDAO->escape($this->value)."'";
      
      $cond = new CRM_Triggers_QueryBuilder_Condition($strCond);
      $where->addCond($cond);  
    }
    
    $hooks = CRM_Utils_Hook::singleton();
    $hooks->invoke(2,
      $this, $builder, $where, $having, $trigger,
      'civicrm_trigger_condition_parse'
      );
    
  }
  
  /**
   * returns the alias field to use in the query and optionally add joins for custom field/tables
   * 
   * @param type $field
   * @param CRM_Core_DAO $dao
   * @return type
   */
  private function parseField($field, $table_name, CRM_Triggers_QueryBuilder $builder) {
    $fieldName = "`".$table_name."`.`".$field['name']."`";
    
    //if the field is a custom field add the column and table as a join
    if (isset($field['custom_group_id'])) {
      $gid = $field['custom_group_id'];
      
      if (!isset(self::$custom_groups[$gid])) {
        self::$custom_groups[$gid] = civicrm_api3('CustomGroup', 'getsingle', array('id' => $gid));
      }
      if (!isset(self::$custom_fields[$gid])) {
        $fields = civicrm_api3('CustomField', 'get', array('custom_group_id' => $gid));
        foreach($fields['values'] as $f) {
          self::$custom_fields[$gid]['custom_'.$f['id']] = $f;
        }
      }
      $cgroup = self::$custom_groups[$gid];      
      $cfield = self::$custom_fields[$gid][$field['name']];
      
      $join = " LEFT JOIN `".$cgroup['table_name']."` AS `group_".$gid."` ON `".$table_name."`.`id` = `group_".$gid."`.`entity_id`";
      $builder->addJoin($join, "group_".$gid);
      $fieldName = "`group_".$gid."`.`".$cfield['column_name']."`";
      $builder->addSelect("`group_".$gid."`.`".$cfield['column_name']."` AS `".$field['name']."`");
    }
    return $fieldName;
  }
  
  public static function findByTriggerRuleId($trigger_rule_id, $fetchFirst=FALSE) {
    $conditions = new CRM_Triggers_BAO_TriggerRuleCondition();
    $conditions->selectAdd();
    $conditions->selectAdd('*');
    $conditions->whereAdd("trigger_rule_id = '".$trigger_rule_id."'");
    $conditions->find($fetchFirst);
    return $conditions;
  }
    /**
     * Function to add or update trigger rule condition
     * 
     * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
     * @date 9 Apr 2014
     * @param array $params 
     * @return array $result
     * @access public
     * @static
     */
    public static function add($params) {
        $result = array();
        if (empty($params)) {
            throw new Exception('Params can not be empty when adding or updating a TriggerRule Condition');
        }
        $triggerRuleCondition = new CRM_Triggers_BAO_TriggerRuleCondition();
        $fields = self::fields();
        foreach ($params as $paramKey => $paramValue) {
            if (isset($fields[$paramKey])) {
                $triggerRuleCondition->$paramKey = $paramValue;
            }
        }
        $triggerRuleCondition->save();
        self::storeValues($triggerRuleCondition, $result);
        return $result;
    }
    /**
     * Function to delete trigger rule condition
     * 
     * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
     * @date 9 Apr 2014
     * @param int $triggerRuleConditionId 
     * @return boolean
     * @access public
     * @static
     */
    public static function deleteById($triggerRuleConditionId) {
        if (empty($triggerRuleConditionId)) {
            throw new Exception('TriggerRuleConditionId can not be empty when attempting to delete one');
        }
        $condition = new CRM_Triggers_BAO_TriggerRuleCondition();
        $condition->id = $triggerRuleConditionId;
        $condition->delete();
        return TRUE;
    }
}