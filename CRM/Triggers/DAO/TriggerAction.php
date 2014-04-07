<?php

/* 
 * This class holds DAO data for a trigger action
 * 
 * This is used to retrieve trigger and actions upon processing
 */

class CRM_Triggers_DAO_TriggerAction extends CRM_Core_DAO {
  
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
    return 'civicrm_trigger_action';
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
        'action_rule_id' => array(
          'name' => 'action_rule_id',
          'type' => CRM_Utils_Type::T_INT,
          'required' => true,
        ) ,
        'schedule' => array(
          'name' => 'schedule',
          'type' => CRM_Utils_Type::T_STRING,
          'required' => true,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
        ) ,
        'last_run' => array(
          'name' => 'last_run',
          'type' => CRM_Utils_Type::T_DATE + CRM_Utils_Type::T_TIME,
        ) ,
        'next_run' => array(
          'name' => 'next_run',
          'type' => CRM_Utils_Type::T_DATE + CRM_Utils_Type::T_TIME,
        ) ,
        'is_active' => array(
          'name' => 'is_active',
          'type' => CRM_Utils_Type::T_BOOLEAN,
          'default' => '1',
        ) ,
        'start_date' => array(
          'name' => 'start_date',
          'type' => CRM_Utils_Type::T_DATE + CRM_Utils_Type::T_TIME,
        ) ,
        'end_date' => array(
          'name' => 'end_date',
          'type' => CRM_Utils_Type::T_DATE + CRM_Utils_Type::T_TIME,
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
        'action_rule_id' => 'action_rule_id',
        'schedule' => 'schedule',
        'last_run' => 'last_run',
        'next' => 'next_run',
        'is_active' => 'is_active',
        'start_date' => 'start_date',
        'end_date' => 'end_date',
      );
    }
    return self::$_fieldKeys;
  }
  
  
}

