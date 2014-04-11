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
   * class attribute to hold possible logic operators, action rules and trigger rules
   */
  protected $_logicOperators = array();
  protected $_actionRules = array();
  protected $_triggerRules = array();
  
  function buildQuickForm() {
    $this->preProcess();
    /*
     * add elements to the form
     */
    $this->setFormElements();
    /*
     * set default values
     */
    $this->setDefaultValues();
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
    $saveScheduleParams['is_active'] = $values['is_active'];

    CRM_Triggers_BAO_RuleSchedule::add($saveScheduleParams);
    $session = CRM_Core_Session::singleton();
    $session->setStatus('Rule Schedule (with related Triggers) Saved', 'Saved', 'success');
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
    $this->add('text', 'label', ts('Label'), array(
      'size'      => CRM_Utils_Type::HUGE,
      'readonly'  => 'readonly'), true);

    $this->add('text', 'action_rule', ts('Action Rule'), array('readonly'  => 'readonly'), false);
    $this->add('text', 'schedule', ts('Schedule'), array('readonly'  => 'readonly'), false);
    $this->addDate('start_date', ts('Start Date'), false);
    $this->addDate('end_date', ts('End Date'), false);
    $this->addDate('last_run', ts('Date Last Run'), false);
    $this->addDate('next_run', ts('Date Next Run'), false);
    $this->add('text', 'is_active', ts('Enabled?'), array('readonly' => 'readonly'), false);
    $this->addButtons(array(array(
      'type' => 'cancel',
      'name' => ts('Done'),
      'isDefault' => true)));
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
    /*
     * if action = delete, execute delete immediately
     */
    if ($this->_action == CRM_Core_Action::DELETE) {
      $this->_id = CRM_Utils_Request::retrieve('rsid', 'Integer', $this);
      CRM_Triggers_BAO_RuleSchedule::deleteById($this->_id);
      $session->setStatus('Rule Schedule deleted', 'Delete', 'success');
      CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/ruleschedulelist'));
    }
    /*
     * retrieve all valid action rules
     */
    $actionRules = CRM_Triggers_BAO_ActionRule::getValues(array());
    foreach ($actionRules as $actionRule) {
      $actionLabel = $actionRule['label'].'{entity:'.$actionRule['entity'].'-action:'.$actionRule['action'].'}';
      $this->_actionRules[$actionRule['id']] = $actionLabel;
    }
    /*
     * if action is not add, store action_id in $this->_id
     */
    if ($this->_action != CRM_Core_Action::ADD) {
      $this->_id = CRM_Utils_Request::retrieve('aid', 'Integer', $this);
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
          $ruleAction = CRM_Utils_Array::value($scheduleRule['action_rule_id'], $this->_actionRules);
        } else {
          $ruleAction = $scheduleRule['entity'];
        }
        $defaults['entity'] = $ruleAction;
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
        $defaults['start_date'] = $scheduleRule['start_date'];
      }
      if (isset($scheduleRule['next_run'])) {
        $defaults['end_date'] = $scheduleRule['end_date'];
      }
      if (isset($scheduleRule['is_active'])) {
        $defaults['is_active'] = $scheduleRule['is_active'];
      }    
    } else {
      $defaults['is_active'] = 1;
      //list($defaults['start_date']) = CRM_Utils_Date::setDateDefaults(CRM_Utils_Date::getToday());
      //$defaults('end_date') = date('Ymd', strtotime('+ 1 year'));
    }
    return $defaults;
  }
}


