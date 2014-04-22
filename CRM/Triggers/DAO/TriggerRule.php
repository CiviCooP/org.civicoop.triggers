<?php

/* 
 * This class holds DAO data for a trigger action
 * 
 * This is used to retrieve trigger and actions upon processing
 */

class CRM_Triggers_DAO_TriggerRule extends CRM_Core_DAO {
  
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
    return 'civicrm_trigger_rule';
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
        'label' => array(
          'name' => 'label',
          'type' => CRM_Utils_Type::T_STRING,
          'required' => true,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
        ) ,
        'name' => array(
          'name' => 'name',
          'type' => CRM_Utils_Type::T_STRING,
          'required' => true,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
        ) ,
        'entity' => array(
          'name' => 'entity',
          'type' => CRM_Utils_Type::T_STRING,
          'required' => true,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
        ) ,
        'operation' => array(
          'name' => 'operation',
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
        'label' => 'label',
        'name' => 'name',
        'entity' => 'entity',
        'operation' => 'operation',
      );
    }
    return self::$_fieldKeys;
  }
  
  
}

