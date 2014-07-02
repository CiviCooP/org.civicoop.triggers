<?php

/* 
 * This class holds DAO data for a trigger rule condition
 * 
 * This is used to retrieve trigger and actions upon processing
 */

class CRM_Triggers_DAO_TriggerRuleCondition extends CRM_Core_DAO {
  
  /**
   * static instance to hold the field values
   *
   * @var array
   * @static
   */
  static $_fields = null;
  
  /**
   * empty definition for virtual function
   */
  static function getTableName() {
    return 'civicrm_trigger_rule_condition';
  }
  
  /**
   * returns all the column names of this table
   *
   * @access public
   * @return array
   */
  static function &fields()
  {
    if (!(self::$_fields)) {
      self::$_fields = array(
        'id' => array(
          'name' => 'id',
          'type' => CRM_Utils_Type::T_INT,
          'required' => true,
        ) ,
        'trigger_rule_id' => array(
          'name' => 'trigger_rule_id',
          'type' => CRM_Utils_Type::T_INT,
          'required' => true,
        ) ,
        'field_name' => array(
          'name' => 'field_name',
          'type' => CRM_Utils_Type::T_STRING,
          'required' => true,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
        ) ,
        'value' => array(
          'name' => 'value',
          'type' => CRM_Utils_Type::T_STRING,
          'required' => true,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
        ) ,
        'special_processing' => array(
          'name' => 'special_processing',
          'type' => CRM_Utils_Type::T_BOOLEAN,
        ),
        'operation' => array(
          'name' => 'operation',
          'type' => CRM_Utils_Type::T_STRING,
          'required' => true,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
        ) ,
        'aggregate_function' => array(
          'name' => 'aggregate_function',
          'type' => CRM_Utils_Type::T_STRING,
          'required' => true,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
        ) ,
        'grouping_field' => array(
          'name' => 'grouping_field',
          'type' => CRM_Utils_Type::T_STRING,
          'required' => true,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
        ) ,
      );
    }
    return self::$_fields;
  }
  /**
   * Returns an array containing, for each field, the arary key used for that
   * field in self::$_fields.
   *
   * @access public
   * @return array
   */
  static function &fieldKeys()
  {
    if (!(self::$_fieldKeys)) {
      self::$_fieldKeys = array(
        'id' => 'id',
        'trigger_rule_id' => 'trigger_rule_id',
        'field_name' => 'field_name',
        'operation' => 'operation',
        'special_processing' => 'special_processing',
        'value' => 'value',
        'aggregate_function' => 'aggregate_function',
        'grouping_field' => 'grouping_field',
      );
    }
    return self::$_fieldKeys;
  }
  
  
}

