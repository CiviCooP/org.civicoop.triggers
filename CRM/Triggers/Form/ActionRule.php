<?php
/**
 * Class ActionRule for form processing of CiviCRM Action Rule
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
class CRM_Triggers_Form_ActionRule extends CRM_Core_Form {
    
    protected $_actionRuleEntities = array();
    protected $_actionRuleActions = array();
    
  function buildQuickForm() {
    $this->preProcess();
    /*
     * add elements to the form
     */
    $this->setFormElements();
    $this->setDefaultValues();
    /*
     * export form elements
     */
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  function postProcess() {
    $values = $this->exportValues();
    if ($this->_action == CRM_Core_Action::UPDATE) {
      $saveActionParams['id'] = $this->_id;
    }
    $saveActionParams['label'] = $values['label'];
    $saveActionParams['entity'] = CRM_Utils_Array::value($values['entity'], $this->_actionRuleEntities);
    $saveActionParams['action'] = CRM_Utils_Array::value($values['rule_action'], $this->_actionRuleActions);
    $saveActionParams['params'] = $values['params'];

    CRM_Triggers_BAO_ActionRule::add($saveActionParams);
    $session = CRM_Core_Session::singleton();
    $session->setStatus('Action Rule Saved', 'Saved', 'success');
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
   * perform processing before the form is built
   */
  function preProcess() {
    /*
     * set user context to return to action list
     */
    $session = CRM_Core_Session::singleton();
    $session->pushUserContext(CRM_Utils_System::url('civicrm/actionruleslist'));
    /*
     * if action = delete, execute delete immediately
     */
    if ($this->_action == CRM_Core_Action::DELETE) {
      $this->_id = CRM_Utils_Request::retrieve('aid', 'Integer', $this);
      CRM_Triggers_BAO_ActionRule::deleteById($this->_id);
      $session->setStatus('Action Rule deleted', 'Delete', 'success');
      CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/actionruleslist'));
    }
    $apiEntities = civicrm_api3('Entity', 'Get', array());
    $this->_actionRuleEntities = $apiEntities['values'];
    $this->_actionRuleActions = array('Create', 'Read', 'Update', 'Delete');

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
        $pageTitle = "New Action Rule";
        break;
      case CRM_Core_Action::VIEW:
        $pageTitle = "View Action Rule";
        break;
      case CRM_Core_Action::UPDATE:
        $pageTitle = "Update Action Rule";
        break;
      default:
        $pageTitle = "Action Rule";
        break;
    }
    CRM_Utils_System::setTitle(ts($pageTitle));
  }
  function setDefaultValues() {
    $defaults = array();
    if (isset($this->_id)) {
      $actionRule = CRM_Triggers_BAO_ActionRule::getByActionRuleId($this->_id);
      if (isset($actionRule['id'])) {
        $defaults['id'] = $actionRule['id'];
      }
      if (isset($actionRule['label'])) {
        $defaults['label'] = $actionRule['label'];
      }
      if (isset($actionRule['entity'])) {
        if ($this->_action == CRM_Core_Action::UPDATE) {
          $entityValue = CRM_Utils_Array::key($actionRule['entity'], $this->_actionRuleEntities);
        } else {
          $entityValue = $actionRule['entity'];
        }
        $defaults['entity'] = $entityValue;
      }
      if (isset($actionRule['action'])) {
        if ($this->_action == CRM_Core_Action::UPDATE) {
          $actionValue = CRM_Utils_Array::key($actionRule['action'], $this->_actionRuleActions);
        } else {
          $actionValue = $actionRule['action'];
        }
        $defaults['rule_action'] = $actionValue;
      }
      if (isset($actionRule['params'])) {
        $defaults['params'] = $actionRule['params'];
      }
    }
    return $defaults;

  }
  function setFormElements() {
    if ($this->_action == CRM_Core_Action::VIEW) {
      $this->setViewElements();
    } else {
      $this->setUpdateAddElements();
    }
  }
  function setViewElements() {
      $this->add(
        'text',
        'label',
        ts('Label'),
        array(
          'size'      => CRM_Utils_Type::HUGE,
          'readonly'  => 'readonly',
          ),
        true
      );
      $this->add(
        'text',
        'entity',
        ts('Entity'),
        array(
          'readonly'  => 'readonly',
          ),
        true
      );
      $this->add(
        'text',
        'rule_action',
        ts('Action'),
        array(
          'readonly'  => 'readonly',
          ),
        true
      );
      $this->add(
        'textarea',
        'params',
        ts('Parameters'),
        array(
          'rows'  =>  4,
          'readonly'  =>  'readonly',
          'cols'  =>  80
        ),
        false
      );
      $this->addButtons(array(
        array(
          'type' => 'cancel',
          'name' => ts('Done'),
          'isDefault' => true
      )));
  }
  function setUpdateAddElements() {
      $this->add(
        'text',
        'label',
        ts('Label'),
        array(
          'size'      => CRM_Utils_Type::HUGE,
          'maxlength' => 255
          ),
        true
      );
      $this->add(
        'select',
        'entity',
        ts('Entity'),
        $this->_actionRuleEntities,
        true
      );
      $this->add(
        'select',
        'rule_action',
        ts('Action'),
        $this->_actionRuleActions,
        true
      );
      $this->add(
        'textarea',
        'params',
        ts('Parameters'),
        array(
          'rows'  =>  4,
          'cols'  =>  80
        ),
        false
      );
      $this->addButtons(array(
        array(
            'type' => 'next',
            'name' => ts('Save'),
            'isDefault' => true,
        ),
        array(
          'type' => 'cancel',
          'name' => ts('Cancel'),
        ),
        )
    );
  }
}
