<?php
/**
 * Class RuleSchedule for form processing of CiviCRM Rule Schedule
 * 
 * MAF Norge funded sprint
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 10 Apr 
 * 
 * Copyright (C) 2014 Coöperatieve CiviCooP U.A. <http://www.civicoop.org>
 * Licensed to MAF Norge <http://www.maf.no> and CiviCRM under the Academic Free License version 3.0.
 */

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Triggers_Form_RuleSchedule extends CRM_Core_Form {
  /*
   * class attribute to hold possible date type, logic operators, action rules and trigger rules
   */
  protected $_logicOperators = array();
  protected $_actionRules = array();
  protected $_triggerRules = array();
  protected $_hasTriggers = FALSE;
  
  function buildQuickForm() {
    /*
     * add elements to the form
     */
    $this->setFormElements();
    /*
     * set default values
     */
    //$this->setDefaultValues();
    parent::buildQuickForm();
  }

  function postProcess() {
    $values = $this->exportValues();
    if ($this->_action == CRM_Core_Action::UPDATE) {
      $saveScheduleParams['id'] = $this->_id;
    }
    $saveScheduleParams['label'] = $values['label'];
    $saveScheduleParams['action_rule_id'] = $values['action_rule'];
    $saveScheduleParams['schedule'] = $values['schedule'];
    $saveScheduleParams['start_date'] = CRM_Utils_Date::processDate($values['start_date'].$values['start_date_time']);
    $saveScheduleParams['end_date'] = CRM_Utils_Date::processDate($values['end_date'].$values['end_date_time']);
    if (isset($values['is_active'])) {
      $saveScheduleParams['is_active'] = $values['is_active'];
    }
    $savedRuleSchedule = CRM_Triggers_BAO_RuleSchedule::add($saveScheduleParams);
    $session = CRM_Core_Session::singleton();
    if ($this->_action == CRM_Core_Action::ADD) {
      $session->setStatus('Rule Schedule Saved', 'Saved', 'success');
      $session->pushUserContext(CRM_Utils_System::url('civicrm/ruleschedule', 'action=update&rsid='.$savedRuleSchedule['id'], true));
    } else {
      if ($values['_qf_RuleSchedule_next'] == 'Schedule Trigger') {
        $session->setStatus('Trigger Scheduled', 'Saved', 'success');
        $saveTriggerParams['rule_schedule_id'] = $savedRuleSchedule['id'];
        $saveTriggerParams['trigger_rule_id'] = $values['rule_schedule_trigger'];
        if (isset($values['logic_operator'])) {
          if ($values['logic_operator'] == 1) {
            $saveTriggerParams['logic_operator'] = 'OR';
          } else {
            $saveTriggerParams['logic_operator'] = 'AND';
          }
        }
        CRM_Triggers_BAO_RuleScheduleTrigger::add($saveTriggerParams);
        $session->pushUserContext(CRM_Utils_System::url('civicrm/ruleschedule', 'action=update&rsid='.$savedRuleSchedule['id'], true));            
      } else {
          $session->setStatus('Rule Schedule and Triggers Saved', 'Saved', 'success');
          $session->pushUserContext(CRM_Utils_System::url('civicrm/ruleschedulelist', '', true));
      }
    }
    parent::postProcess();
  }
  /*
   * set all form elements
   */
  function setFormElements() {
    if ($this->_action == CRM_Core_Action::VIEW) {
      $this->setViewElements();
    }
    if ($this->_action == CRM_Core_Action::ADD) {
      $this->setAddElements();
    }
    if ($this->_action == CRM_Core_Action::UPDATE) {
      $this->setUpdateElements();
    }
  }
  /*
   * set form elements for view action
   */
  function setViewElements() {
    $this->add('text', 'label', ts('Label'), array(), false);
    $this->add('text', 'action_rule', ts('Action Rule'), array(), false);
    $this->add('text', 'schedule', ts('Schedule'), array('readonly'  => 'readonly'), false);
    $this->addDate('start_date', ts('Start Date'), false);
    $this->addDate('end_date', ts('End Date'), false);
    $this->addDate('last_run', ts('Date Last Run'), false);
    $this->addDate('next_run', ts('Date Next Run'), false);
    $this->add('text', 'is_active', ts('Enabled?', array('readonly' => 'readonly', false)));
    $this->addButtons(array(array('type' => 'cancel', 'name' => ts('Done'), 'isDefault' => true)));
  }
  /*
   * set form elements for add action
   */
  function setAddElements() {
    $this->add('text', 'label', ts('Label'), array(
      'size'      =>  CRM_Utils_Type::HUGE,
      'maxlength' =>  255), true);
    $this->add('select', 'action_rule', ts('Action Rule'), $this->_actionRules, true);
    $this->add('text', 'schedule', ts('Schedule'), array(
      'size'      =>  CRM_Utils_Type::HUGE,
      'maxlength' =>  255), true);
    $this->addDateTime('start_date', ts('Start Date'), false);
    $this->addDateTime('end_date', ts('End Date'), false);
    $this->add('checkbox', 'is_active', ts('Enabled'));
    $this->addButtons(array(
      array('type' => 'next', 'name' => ts('Save'), 'isDefault' => true,),
      array('type' => 'cancel', 'name' => ts('Cancel'))));
  }
  /*
   * set form elements for update action
   */
  function setUpdateElements() {
    $this->add('text', 'label', ts('Label'), array(
      'size'      =>  CRM_Utils_Type::HUGE,
      'maxlength' =>  255), true);
    $this->add('select', 'action_rule', ts('Action Rule'), $this->_actionRules, true);
    $this->add('text', 'schedule', ts('Schedule'), array(
      'size'      =>  CRM_Utils_Type::HUGE,
      'maxlength' =>  255), true);
    $this->addDateTime('start_date', ts('Start Date'), false);
    $this->addDateTime('end_date', ts('End Date'), false);
    $this->addDateTime('last_run', ts('Date Last Run'), false);
    $this->addDateTime('next_run', ts('Date Next Run'), false);
    /*
     * set elements for new rule schedule trigger
     */
    if ($this->_hasTriggers == true) {
      $this->add('select', 'logic_operator', ts('Logic Operator'), array('AND', 'OR'), true);
    }
    $this->add('select', 'rule_schedule_trigger', ts('Trigger'), $this->_triggerRules, true);
    $this->addButtons(array(
      array('type' => 'next', 'name' => ts('Save'), 'isDefault' => true,),
      array('type' => 'cancel', 'name' => ts('Cancel'))));
  }
  /*
   * perform pre form building actions
   */
  function preProcess() {
    /*
     * set user context to return to rule schedule list
     */
    $session = CRM_Core_Session::singleton();
    $session->pushUserContext(CRM_Utils_System::url('civicrm/ruleschedulelist'));
    $this->_hasTriggers = false;
    /*
     * if action is not add, store action_id in $this->_id
     */
    if ($this->_action != CRM_Core_Action::ADD) {
      $this->_id = CRM_Utils_Request::retrieve('rsid', 'Integer', $this);
      /*
       * retrieve all scheduled triggers related to this rule schedule
       */
      $scheduledTriggerRows = CRM_Triggers_BAO_RuleScheduleTrigger::getValues(array('rule_schedule_id' => $this->_id));
      foreach ($scheduledTriggerRows as &$scheduledTriggerRow) {
        $scheduledTriggerRow = $this->setScheduledTriggerRow($scheduledTriggerRow);
        if ($this->_hasTriggers == false) {
          $this->_hasTriggers = true;
        }
      }
      $this->assign('scheduledTriggerRows', $scheduledTriggerRows);
      $triggerHeaders = array('', 'Trigger', 'Trigger Entity', 'Trigger Conditions');
      $this->assign('triggerHeaders', $triggerHeaders);
    }
    /*
     * if action = delete, execute delete immediately
     */
    if ($this->_action == CRM_Core_Action::DELETE) {
      CRM_Triggers_BAO_RuleSchedule::deleteById($this->_id);
      $session->setStatus('Rule Schedule deleted', 'Delete', 'success');
      CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/ruleschedulelist'));
    }
    /*
     * retrieve all valid action rules
     */
    $actionRules = CRM_Triggers_BAO_ActionRule::getValues(array());
    foreach ($actionRules as $actionRule) {
      $actionLabel = $actionRule['label'].' {entity:'.$actionRule['entity'].'-action:'.$actionRule['action'].'}';
      $this->_actionRules[$actionRule['id']] = $actionLabel;
    }
    /*
     * retrieve all valid trigger rules
     */
    $triggerRules = CRM_Triggers_BAO_TriggerRule::getValues(array());
    foreach ($triggerRules as $triggerRule) {
      $triggerLabel = $triggerRule['label'].' {entity: '.$triggerRule['entity'].'}';
      $this->_triggerRules[$triggerRule['id']] = $triggerLabel;
    }
    /*
     * set page title based on action
     */
    switch($this->_action) {
      case CRM_Core_Action::ADD:
        $pageTitle = "New Schedule Rule";
        break;
      case CRM_Core_Action::VIEW:
        $pageTitle = "View Schedule Rule";
        break;
      case CRM_Core_Action::UPDATE:
        $pageTitle = "Update Schedule Rule";
        break;
      default:
        $pageTitle = "Schedule Rule";
        break;
    }
    CRM_Utils_System::setTitle(ts($pageTitle));
  }
  /*
   * function to set default values
   */
  function setDefaultValues() {
    $defaults = array();
    if (isset($this->_id)) {
      $scheduleRule = CRM_Triggers_BAO_RuleSchedule::getByRuleScheduleId($this->_id);
      
      if (isset($scheduleRule['id'])) {
        $defaults['id'] = $scheduleRule['id'];
      }
      if (isset($scheduleRule['label'])) {
        $defaults['label'] = $scheduleRule['label'];
      }
      if (isset($scheduleRule['action_rule_id'])) {
        if ($this->_action == CRM_Core_Action::UPDATE) {
          $ruleAction = $scheduleRule['action_rule_id'];
        } else {
          $ruleAction = CRM_Utils_Array::value($scheduleRule['action_rule_id'], $this->_actionRules);
        }
        $defaults['action_rule'] = $ruleAction;
      }
      if (isset($scheduleRule['schedule'])) {
        $defaults['schedule'] = $scheduleRule['schedule'];
      }
      if (isset($scheduleRule['last_run'])) {
        $defaults['last_run'] = $scheduleRule['last_run'];
      }
      if (isset($scheduleRule['next_run'])) {
        $defaults['next_run'] = $scheduleRule['next_run'];
      }
      if (isset($scheduleRule['start_date'])) {
        if ($this->_action == CRM_Core_Action::VIEW) {
          if (empty($scheduleRule['start_date'])) {
            $defaults['start_date'] = "";
          } else {
            $defaults['start_date'] = $scheduleRule['start_date'];
          }
        } else {
          list($defaults['start_date'], $defaults['start_date_time']) = CRM_Utils_Date::setDateDefaults($scheduleRule['start_date']);
        }
      }
      if (isset($scheduleRule['end_date'])) {
        if ($this->_action == CRM_Core_Action::VIEW) {
          if (empty($scheduleRule['end_date'])) {
            $defaults['end_date'] = "";
          } else {
            $defaults['end_date'] = $scheduleRule['end_date'];
          }
        } else {
          list($defaults['end_date'], $defaults['end_date_time']) = CRM_Utils_Date::setDateDefaults($scheduleRule['end_date']);
        }
      }
      if (isset($scheduleRule['is_active'])) {
        $defaults['is_active'] = $scheduleRule['is_active'];
      }    
    } else {
      $defaults['is_active'] = 1;
    }
    return $defaults;
  }
  function setScheduledTriggerRow($scheduledTriggerRow) {
    /*
     * set delete function for row
     */
    $scheduledTriggerRow['delete'] = CRM_Utils_System::url('civicrm/rulescheduletriggerdelete', 
      'rsid='.$this->_id.'&rstid='.$scheduledTriggerRow['id'], true);
    if (!isset($scheduledTriggerRow['logic_operator'])) {
      $scheduledTriggerRow['logic_operator'] = null;
    }
    /*
     * retrieve data for trigger rule
     */
    $scheduledTrigger = CRM_Triggers_BAO_TriggerRule::getByTriggerRuleId($scheduledTriggerRow['trigger_rule_id']);
    if (!empty($scheduledTrigger)) {
      $scheduledTriggerRow['trigger_label'] = $scheduledTrigger['label'];
      $scheduledTriggerRow['trigger_entity'] = $scheduledTrigger['entity'];
      $scheduledTriggerConditions = CRM_Triggers_BAO_TriggerRuleCondition::getValues(
          array('trigger_rule_id' => $scheduledTriggerRow['trigger_rule_id']));
      $conditionRows = array();
      foreach($scheduledTriggerConditions as $conditionId => $condition) {
        $conditionRow = $condition['field_name'].' '.$condition['operation'].' '.$condition['value'];
        if (isset($condition['aggregate_function']) && !empty($condition['aggregate_function'])) {
          $conditionRow .= ' AGGREGATE FUNCTION '.$condition['aggregate_function'];
        }
        if (isset($condition['grouping_field']) && !empty($condition['grouping_field'])) {
          $conditionRow .= ' GROUP BY '.$condition['grouping_field'];
        }
        $conditionRows[] = $conditionRow;
      }
      $scheduledTriggerRow['trigger_conditions'] = $conditionRows;
    }
    return $scheduledTriggerRow;
  }
  /**
   * Function to add validation rules
   */
  function addRules() {
    if ($this->_action == CRM_Core_Action::ADD) {
      $this->addFormRule(array('CRM_Triggers_Form_RuleSchedule', 'validateLabel'));
    }
    if ($this->_action == CRM_Core_Action::ADD || $this->_action == CRM_Core_Action::UPDATE) {
      $this->addFormRule(array('CRM_Triggers_Form_RuleSchedule', 'validateDates'));
    }
  }
  /**
   * Function to validate label
   */
  static function validateLabel($fields) {
    if (CRM_Triggers_BAO_RuleSchedule::checkLabelExists($fields['label']) == TRUE) {
      $errors['label'] = ts('There is already a Schedule Rule with label '.$fields['label']);
      return $errors;
    } else {
      return TRUE;
    }
  }
  /**
   * Function to validate dates
   */
  static function validateDates($fields) {
    if (!empty($fields['end_date'])) {
      $compEndDate = date('Ymd', strtotime($fields['end_date']));
      $compStartDate = date('Ymd', strtotime($fields['start_date']));
      if ($compEndDate <= $compStartDate) {
          $errors['end_date'] = ts('End date has to be later than start date');
          return $errors;
      }      
    }
  }
}