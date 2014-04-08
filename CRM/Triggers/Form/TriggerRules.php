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
    protected $_id = 0;
    function buildQuickForm() {
        /*
         * if action is not add, store trigger_rule_id in $this->_id
         * and retrieve conditions for trigger
         */
        if ($this->_action != CRM_Core_Action::ADD) {
            $this->_id = CRM_Utils_Request::retrieve('tid', 'Integer', $this);
            $conditionParams = array('trigger_rule_id', $this->_id);
            $triggerConditions = CRM_Triggers_BAO_TriggerRuleCondition::get($conditionParams);
            $this->assign('conditionRows', $triggerConditions);
            $conditionFields = CRM_Triggers_BAO_TriggerRuleCondition::fields();
            $conditionHeaders = array('Field Name', 'Operation', 'Value', 'Aggregate Function', 'Grouping Field');
            $this->assign('conditionHeaders', $conditionHeaders);
        }
        /*
         * add form elements
         */
        $this->add('text', 'label', ts('Label'), 
            array(
                'maxlength' => 255,
                'size' => CRM_Utils_Type::HUGE,
            ), true);
        $validEntities = array('Activity', 'Contribution', 'GroupContact');
        $this->add('select', 'entity', ts('Entity'), $validEntities, true);
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
        CRM_Utils_System::setTitle(ts('Trigger'));
        $this->assign('elementNames', $this->getRenderableElementNames());
        parent::buildQuickForm();
    }
    function postProcess() {
        $values = $this->exportValues();
        $options = $this->getColorOptions();
        CRM_Core_Session::setStatus(ts('You picked color "%1"', array(
          1 => $options[$values['favorite_color']]
        )));
        parent::postProcess();
    }

    function getColorOptions() {
        $options = array(
            '' => ts('- select -'),
            '#f00' => ts('Red'),
            '#0f0' => ts('Green'),
            '#00f' => ts('Blue'),
            '#f0f' => ts('Purple'),
        );
        foreach (array('1','2','3','4','5','6','7','8','9','a','b','c','d','e') as $f) {
            $options["#{$f}{$f}{$f}"] = ts('Grey (%1)', array(1 => $f));
        }
        return $options;
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
}
