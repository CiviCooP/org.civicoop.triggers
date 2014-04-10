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
    
    function buildQuickForm() {
        
        $this->preProcess();
        /*
         * add form elements
         */
        if ($this->_action == CRM_Core_Action::VIEW) {
            $this->add('text', 'label', ts('Label'), 
                array(
                    'readonly'  => 'readonly',
                    'size' => CRM_Utils_Type::HUGE,
                ), true);
        } else {
            $this->add('text', 'label', ts('Label'), 
                array(
                    'maxlength' => 255,
                    'size' => CRM_Utils_Type::HUGE,
                ), true);            
        }
        if ($this->_action == CRM_Core_Action::ADD) {
            $this->add('select', 'entity', ts('Entity'), $this->_entities, true);
        } else {
            $this->add('text', 'entity', ts('Entity'),
                array(
                    'readonly'  =>  'readonly'
                ), true);
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
            $this->add('text', 'operation');
            $this->add('text', 'value');
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
        /*
         * if action = delete, execute delete immediately
         */
        if ($this->_action == CRM_Core_Action::DELETE) {
            $this->_id = CRM_Utils_Request::retrieve('tid', 'Integer', $this);
            CRM_Triggers_BAO_TriggerRule::deleteById($this->_id);
            $session->setStatus('Trigger deleted', 'Delete', 'success');
            CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/triggerruleslist'));
        }
        $this->_entities = array('Activity', 'Contribution', 'GroupContact', 'Contact');

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
            $conditionHeaders = array('Field Name', 'Operation', 'Value', 'Aggregate Function', 'Grouping Field');
            $this->assign('conditionHeaders', $conditionHeaders);
        }
        /*
         * set page title based on action
         */
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
    }
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
                $saveConditionParams['operation'] = $values['operation'];
                $saveConditionParams['value'] = $values['value'];
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

    private function _listEntityFields() {
        if (isset($this->_entity) && !empty($this->_entity)) {
            $daoEntity = CRM_Triggers_BAO_TriggerRule::getEntityDAO($this->_entity);
            $className = get_class($daoEntity);
            $fields = $className::fields();
            foreach ($fields as $field) {
                $result[] = $field['name'];
            }
            return $result;
        }
    }
}
