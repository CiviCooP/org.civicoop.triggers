<?php

/* 
 * This class holds DAO data for a trigger action
 * 
 * This is used to retrieve trigger and actions upon processing
 */

class CRM_Triggers_DAO_RuleScheduleTrigger extends CRM_Core_DAO {
  
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
    return 'civicrm_rule_schedule_trigger';
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
        'rule_schedule_id' => array(
          'name' => 'rule_schedule_id',
          'type' => CRM_Utils_Type::T_INT,
          'required' => true,
        ) ,
        'trigger_rule_id' => array(
          'name' => 'trigger_rule_id',
          'type' => CRM_Utils_Type::T_INT,
          'required' => true,
        ) ,
        'logic_operator' => array(
          'name' => 'logic_operator',
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
        'rule_schedule_id' => 'rule_schedule_id',
        'trigger_rule_id' => 'trigger_rule_id',
        'logic_operator' => 'logic_operator',
      );
    }
    return self::$_fieldKeys;
  }
  
  
}

