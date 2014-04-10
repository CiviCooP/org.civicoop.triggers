<?php
/**
 * Class ActionRules for form processing of CiviCRM Action Rules
 * 
 * MAF Norge funded sprint
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 9 Apr 
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
class CRM_Triggers_Form_ActionRules extends CRM_Core_Form {
    
    protected $_entities = array();
    protected $_entityActions = array();
    
    function buildQuickForm() {
        
        $this->preProcess();
        /*
         * add form elements
         */
        switch ($this->_action) {
            case CRM_Core_Action::VIEW:
                $this->add('text', 'label', ts('Label'), 
                    array(
                        'readonly'  => 'readonly',
                        'size' => CRM_Utils_Type::HUGE,
                    ), true);
                $this->add('text', 'entity', ts('Entity'), 
                    array(
                        'readonly'  => 'readonly',
                        'size' => CRM_Utils_Type::HUGE,
                    ), true);
                $this->add('text', 'action', ts('Action'), 
                    array(
                        'readonly'  => 'readonly',
                        'size' => CRM_Utils_Type::HUGE,
                    ), true);
                $this->add('textarea', 'params', ts('Parameters'), 
                    array(
                        'readonly'  => 'readonly',
                    ), true);
                break;
            case CRM_Core_Action::ADD:
                $this->add('text', 'label', ts('Label'), 
                    array(
                        'maxlength'  => 255,
                        'size' => CRM_Utils_Type::HUGE,
                    ), true);
                $this->add('select', 'entity', ts('Entity'), $this->_entities, true);
                $this->add('select', 'action', ts('Action'), $this->_entityActions, true);
                $this->add('textarea', 'params', ts('Parameters'),'', false);
                break;
            case CRM_Core_Action::UPDATE:
                $this->add('text', 'label', ts('Label'), 
                    array(
                        'maxlength'  => 255,
                        'size' => CRM_Utils_Type::HUGE,
                    ), true);
                $this->add('text', 'entity', ts('Entity'), 
                    array(
                        'readonly'  => 'readonly',
                        'size' => CRM_Utils_Type::HUGE,
                    ), true);
                $this->add('text', 'action', ts('Action'), 
                    array(
                        'readonly'  => 'readonly',
                        'size' => CRM_Utils_Type::HUGE,
                    ), true);
                $this->add('textarea', 'params', ts('Parameters'),true);
                break;
        }
        $this->setDefaultValues();
        $this->addButtons(array(
            array(
                'type' => 'next',
                'name' => ts('Save'),
                'isDefault' => TRUE,
            ),
            array(
              'type' => 'cancel',
              'name' => ts('Cancel'),
            ),
            )
        );
        $this->assign('elementNames', $this->getRenderableElementNames());
        parent::buildQuickForm();
    }
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
            $session->setStatus('Action deleted', 'Delete', 'success');
            CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/actionruleslist'));
        }
        $apiEntities = civicrm_api3('Entity', 'Get', array());
        $this->_entities = $apiEntities['values'];
        $this->_entityActions = array('Create', 'Read', 'Update', 'Delete');

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
                $pageTitle = "New Action";
                break;
            case CRM_Core_Action::VIEW:
                $pageTitle = "View Action";
                break;
            case CRM_Core_Action::UPDATE:
                $pageTitle = "Update Action";
                break;
            default:
                $pageTitle = "Action";
                break;
        }
        CRM_Utils_System::setTitle(ts($pageTitle));
    }
    function postProcess() {
        /**
         * @todo find out why the heck exportValues does not give result
         */
        $values = $this->_submitValues;
        CRM_Core_Error::debug("values", $values);
        CRM_Core_Error::debug("action", $this->_action);
        exit();
        if ($this->_action == CRM_Core_Action::UPDATE) {
            $saveActionParams['id'] = $this->_id;
        }
        $saveActionParams['label'] = $values['label'];
        $saveActionParams['entity'] = CRM_Utils_Array::value($values['entity'], $this->_entities);
        $saveActionParams['action'] = CRM_Utils_Array::value($values['action'], $this->_entityActions);
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
     * Function to set default values
     * 
     */
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
                if ($this->_action == CRM_Core_Action::ADD) {
                    $entityValue = CRM_Utils_Array::key($actionRule['entity'], $this->_entities);
                } else {
                    $entityValue = $actionRule['entity'];
                }
                $defaults['entity'] = $entityValue;
            }
            if (isset($actionRule['action'])) {
                if ($this->_action == CRM_Core_Action::ADD) {
                    $actionValue = CRM_Utils_Array::key($actionRule['action'], $this->_entityActions);
                } else {
                    $actionValue = $actionRule['action'];
                }
                $defaults['action'] = $actionValue;
            }
            if (isset($actionRule['params'])) {
                $defaults['params'] = $actionRule['params'];
            }
        }
        return $defaults;
    }
}
