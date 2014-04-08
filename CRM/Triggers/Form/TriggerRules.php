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
    function buildQuickForm() {
        
        $this->preProcess();
        /*
         * add form elements
         */
        $this->add('text', 'label', ts('Label'), 
            array(
                'maxlength' => 255,
                'size' => CRM_Utils_Type::HUGE,
            ), true);
        $this->add('select', 'entity', ts('Entity'), $this->_entities, true);
        $validActions = array('Create', 'Delete', 'Read', 'Update');
        /**
         * EH 8 Apr 2014: not required as long as we do cron processing only
         * @todo bring back when using post hook
         */
        //$this->add('select', 'operation', ts('Operation'), $validActions, true);
        
    
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
         * set user context to return to trigger list
         */
        $session = CRM_Core_Session::singleton();
        $session->pushUserContext(CRM_Utils_System::url('civicrm/triggerruleslist'));
        $this->_entities = array('Activity', 'Contribution', 'GroupContact');

        /*
         * if action is not add, store trigger_rule_id in $this->_id
         * and retrieve conditions for trigger
         */
        if ($this->_action == CRM_Core_Action::UPDATE || $this->_action == CRM_Core_Action::VIEW) {
            $this->_id = CRM_Utils_Request::retrieve('tid', 'Integer', $this);
            $conditionParams = array('trigger_rule_id', $this->_id);
            $triggerConditions = CRM_Triggers_BAO_TriggerRuleCondition::getValues($conditionParams);
            $this->assign('conditionRows', $triggerConditions);
            $conditionHeaders = array('Field Name', 'Operation', 'Value', 'Aggregate Function', 'Grouping Field');
            $this->assign('conditionHeaders', $conditionHeaders);
        }
        switch($this->_action) {
            case CRM_Core_Action::ADD:
                $pageTitle = "New Trigger";
                break;
            case CRM_Core_Action::VIEW:
                $pageTitle = "View Trigger";
                break;
            case CRM_Core_Action::UPDATE:
                $pageTitle = "Update Trigger";
                break;
            default:
                $pageTitle = "Trigger";
                break;
        }
        CRM_Utils_System::setTitle(ts($pageTitle));

        $this->setDefaultValues();
    }
    function postProcess() {
        $values = $this->exportValues();
        CRM_Core_Error::debug("values", $values);
        CRM_Core_Error::debug("this", $this);
        exit();
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
                $entityValue = CRM_Utils_Array::key($triggerRule['entity'], $this->_entities);
                $defaults['entity'] = $entityValue;
            }
        }
        return $defaults;
    }
}
