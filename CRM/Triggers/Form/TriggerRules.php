<?php
/**
 * Class TriggerRules for form processing of CiviCRM Trigger Rules
 * 
 * MAF Norge funded sprint
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 7 Apr 
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
class CRM_Triggers_Form_TriggerRules extends CRM_Core_Form {
    
  protected $_entities = array();
  protected $_entity = null;
  protected $_entityFields = array();
  protected $_conditionOperations = array();
  /**
   * Function to build form
   */
   
  function buildQuickForm() {
    $this->preProcess();
    /*
     * add form elements
     */
    if ($this->_action == CRM_Core_Action::VIEW) {
      $this->add('text', 'label', ts('Label'), 
      array('readonly'  => 'readonly', 'size' => CRM_Utils_Type::HUGE), true);
    } else {
      $this->add('text', 'label', ts('Label'), 
        array('maxlength' => 255, 'size' => CRM_Utils_Type::HUGE), true);            
      }
    if ($this->_action == CRM_Core_Action::ADD) {
      $this->add('select', 'entity', ts('Entity'), $this->_entities, true);
    } else {
      $this->add('text', 'entity', ts('Entity'), array('readonly' => 'readonly'), true);
    }
    /*
     * set new Condition elements if action is update
     */
    if ($this->_action == CRM_Core_Action::UPDATE) {
      $triggerRule = CRM_Triggers_BAO_TriggerRule::getByTriggerRuleId($this->_id);
      if (isset($triggerRule['entity'])) {
        $this->_entity = $triggerRule['entity'];
      }
      $this->_entityFields = $this->_listEntityFields();
      /*
       * add element for condition without label, so they do not
       * occur in the rendereableElements on the main form
       */
      $this->add('select', 'field_name', '', $this->_entityFields, true);
      $this->add('select', 'operation', '', $this->_conditionOperations, true);
      $this->add('text', 'value');
      $this->add('checkbox', 'special_processing');
      $this->add('text', 'aggregate_function');
      $this->add('text', 'grouping_field');
    }
    $this->setDefaultValues();
    /**
     * EH 8 Apr 2014: not required as long as we do cron processing only
     * @todo bring back when using post hook
     */
    //$validActions = array('Create', 'Delete', 'Read', 'Update');
    //$this->add('select', 'operation', ts('Operation'), $validActions, true);

    if ($this->_action == CRM_Core_Action::VIEW) {
      $this->addButtons(array(array('type' => 'cancel', 'name' => ts('Done'), 'isDefault' => true)));
    } else {
      $this->addButtons(array(
        array('type' => 'next', 'name' => ts('Save'), 'isDefault' => TRUE),
        array('type' => 'cancel', 'name' => ts('Cancel'))));
    }
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }
  /**
   * Function to do processing before form stuff
   */
  function preProcess() {
    /*
     * set user context to return to trigger list
     */
    $session = CRM_Core_Session::singleton();
    $session->pushUserContext(CRM_Utils_System::url('civicrm/triggerruleslist'));
    /*
     * if action = delete, execute delete immediately
     */
    if ($this->_action == CRM_Core_Action::DELETE) {
      $this->_id = CRM_Utils_Request::retrieve('tid', 'Integer', $this);
      CRM_Triggers_BAO_TriggerRule::deleteById($this->_id);
      $session->setStatus('Trigger Rule deleted', 'Delete', 'success');
      CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/triggerruleslist'));
    }
    $validEntities = array('Activity', 'ActivityContact', 'ActivityTarget', 'ActivityAssignment',  'Contribution', 'GroupContact', 'Contact', 
      'Email', 'Phone', 'Address');
    sort($validEntities);
    $this->_entities = $validEntities;
    $this->_conditionOperations = array('=', '!=', '>', '>=', '<', '<=', 'LIKE', 
      'IN', 'IS NULL', 'IS NOT NULL', 'IS EMPTY', 'IS NOT EMPTY' );
    /*
     * if action is not add, store trigger_rule_id in $this->_id
     * and retrieve conditions for trigger
     */
    if ($this->_action != CRM_Core_Action::ADD) {
      $this->_id = CRM_Utils_Request::retrieve('tid', 'Integer', $this);
      $conditionParams = array('trigger_rule_id' => $this->_id);
      $triggerConditions = CRM_Triggers_BAO_TriggerRuleCondition::getValues($conditionParams);
      foreach ($triggerConditions as &$triggerCondition) {
          $triggerCondition['delete'] = CRM_Utils_System::url('civicrm/conditiondelete', 
              'tid='.$this->_id.'&trcid='.$triggerCondition['id'], true);
      }
      $this->assign('conditionRows', $triggerConditions);
      $conditionHeaders = array('Field Name', 'Operation', 'Value', 'Function', 'Aggregate Function', 'Grouping Field');
      $this->assign('conditionHeaders', $conditionHeaders);
    }
    /*
     * set page title based on action
     */
    switch($this->_action) {
      case CRM_Core_Action::ADD:
        $pageTitle = "New Trigger Rule";
        break;
      case CRM_Core_Action::VIEW:
        $pageTitle = "View Trigger Rule";
        break;
      case CRM_Core_Action::UPDATE:
        $pageTitle = "Update Trigger Rule";
        break;
      default:
        $pageTitle = "Trigger Rule";
        break;
    }
    CRM_Utils_System::setTitle(ts($pageTitle));
  }
  /**
   * Function to process form results
   */
  function postProcess() {
    $values = $this->exportValues();
    if ($this->_action == CRM_Core_Action::UPDATE) {
      $saveTriggerParams['id'] = $this->_id;
    }
    $saveTriggerParams['label'] = $values['label'];
    $saveTriggerParams['entity'] = CRM_Utils_Array::value($values['entity'], $this->_entities);
    $savedTriggerRule = CRM_Triggers_BAO_TriggerRule::add($saveTriggerParams);
    $session = CRM_Core_Session::singleton();
    if ($this->_action == CRM_Core_Action::ADD) {
      $session->setStatus('Trigger Saved', 'Saved', 'success');
      $session->pushUserContext(CRM_Utils_System::url('civicrm/triggerrules', 'action=update&tid='.$savedTriggerRule['id'], true));
    }
    if ($this->_action == CRM_Core_Action::UPDATE) {
      if ($values['_qf_TriggerRules_next'] == 'Add Condition') {
        $session->setStatus('Condition Added', 'Saved', 'success');
        $saveConditionParams['trigger_rule_id'] = $savedTriggerRule['id'];
        $saveConditionParams['field_name'] = CRM_Utils_Array::value($values['field_name'], $this->_entityFields);
        $saveConditionParams['operation'] = CRM_Utils_Array::value($values['operation'], $this->_conditionOperations);
        $saveConditionParams['value'] = $values['value'];
        $saveConditionParams['special_processing'] = isset($values['special_processing']) && $values['special_processing'] ? '1' : '0';
        $saveConditionParams['aggregate_function'] = $values['aggregate_function'];
        $saveConditionParams['grouping_field'] = $values['grouping_field'];                
        CRM_Triggers_BAO_TriggerRuleCondition::add($saveConditionParams);
        $session->pushUserContext(CRM_Utils_System::url('civicrm/triggerrules', 'action=update&tid='.$savedTriggerRule['id'], true));            
      } else {
        $session->setStatus('Trigger and Conditions Saved', 'Saved', 'success');
        $session->pushUserContext(CRM_Utils_System::url('civicrm/triggerruleslist', '', true));
      }
    }
    parent::postProcess();
  }
  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }
  /**
   * Function to set default values
   * 
   */
  function setDefaultValues() {
    $defaults = array();
    if (isset($this->_id)) {
      $triggerRule = CRM_Triggers_BAO_TriggerRule::getByTriggerRuleId($this->_id);
      if (isset($triggerRule['id'])) {
        $defaults['id'] = $triggerRule['id'];
      }
      if (isset($triggerRule['label'])) {
        $defaults['label'] = $triggerRule['label'];
      }
      if (isset($triggerRule['entity'])) {
        if ($this->_action == CRM_Core_Action::ADD) {
          $entityValue = CRM_Utils_Array::key($triggerRule['entity'], $this->_entities);
        } else {
          $entityValue = $triggerRule['entity'];
        }
        $defaults['entity'] = $entityValue;
      }
    }
    return $defaults;
  }
  /**
   * Function to retrieve entity list
   * 
   */
  private function _listEntityFields() {
    if (isset($this->_entity) && !empty($this->_entity)) {
      $daoEntity = $this->getDaoClassByFullName($this->_entity);
      $fields = $daoEntity::fields();
      foreach ($fields as $field) {
        $result[] = $field['name'];
      }
      return $result;
    }
  }
  
  private function getDaoClassByFullName($fullName) {
    $daoEntity = CRM_Core_DAO_AllCoreTables::getFullName($fullName);
    if (empty($daoEntity)) {
      switch($fullName) {
        case 'ActivityTarget':
          $daoEntity = 'CRM_Activity_DAO_ActivityTarget';
          break;
        case 'ActivityAssignment':
          $daoEntity = 'CRM_Activity_DAO_ActivityAssignment';
          break;
      }
    }
    return $daoEntity;
  }
  /**
   * Function to add validation rules
   */
  function addRules() {
    if ($this->_action == CRM_Core_Action::ADD) {
      $this->addFormRule(array('CRM_Triggers_Form_TriggerRules', 'validateLabel'));
    }
    if ($this->_action == CRM_Core_Action::UPDATE) {
      $values = $this->exportValues();
      if (isset($values['_qf_TriggerRules_next']) && $values['_qf_TriggerRules_next'] == 'Add Condition') {
        $this->addFormRule(array('CRM_Triggers_Form_TriggerRules', 'validateCondition'), $this->_conditionOperations);
      }
    }
  }
  /**
   * Function to validate label
   */
  static function validateLabel($fields) {
    if (CRM_Triggers_BAO_TriggerRule::checkLabelExists($fields['label']) == TRUE) {
      $errors['label'] = ts('There is already a Trigger Rule with label '.$fields['label']);
      return $errors;
    } else {
      return TRUE;
    }
  }
  /**
   * Function to validate new condition
   */
  static function validateCondition($fields, $files, $operations) {
    $errors = array();
    $nonValueOperations = array('IS NULL', 'IS NOT NULL', 'IS EMPTY', 'IS NOT EMPTY');
    $operationLabel = CRM_Utils_Array::value($fields['operation'], $operations);
    
    if (in_array($operationLabel, $nonValueOperations)) {
      if (!empty($fields['value'])) {
        $errors['value'] = ts('Value has to be empty when operation is '.$operationLabel);
      }
      if (!empty($fields['aggregate_function'])) {
        $errors['aggregate_function'] = ts('Aggregate function has to be empty when operation is '.$operationLabel);
      }
    } else {
      if (empty($fields['value'])) {
        $errors['value'] = ts('Value can not be empty when operation is '.$operationLabel);
      }
    }
    if (!empty($errors)) {
      return $errors;
    } else {
      return TRUE;
    }
  }
}
