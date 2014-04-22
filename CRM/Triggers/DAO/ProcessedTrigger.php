<?php

/* 
 * This class holds DAO data for a rule action
 * 
 * This is used to retrieve trigger and actions upon processing
 */

class CRM_Triggers_DAO_ProcessedTrigger extends CRM_Core_DAO {
  
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
    return 'civicrm_processed_trigger';
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
        'entity' => array(
          'name' => 'entity',
          'type' => CRM_Utils_Type::T_STRING,
          'required' => true,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
        ) ,
       'entity_id' => array(
          'name' => 'entity_id',
          'type' => CRM_Utils_Type::T_INT,
          'required' => true,
        ) ,
        'date_processed' => array(
          'name' => 'date_processed',
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
        'rule_schedule_id' => 'rule_schedule_id',
        'entity' => 'entity',
        'entity_id' => 'entity_id',
        'date_processed' => 'date_processed',
      );
    }
    return self::$_fieldKeys;
  }
  
  
}

