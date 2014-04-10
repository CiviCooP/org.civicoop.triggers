<?php
/**
 * Class ActionRules for page processing of CiviCRM Trigger/Action Rules list
 * 
 * MAF Norge funded sprint
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 10 Apr 
 * 
 * Copyright (C) 2014 Coöperatieve CiviCooP U.A. <http://www.civicoop.org>
 * Licensed to MAF Norge <http://www.maf.no> and CiviCRM under the Academic Free License version 3.0.
 */

require_once 'CRM/Core/Page.php';

class CRM_Triggers_Page_RuleSchedule extends CRM_Core_Page {
    function run() {
        CRM_Utils_System::setTitle(ts('List of Trigger/Action Rules'));
        $this->assign('addUrl', CRM_Utils_System::url('civicrm/ruleschedule', 'action=add&reset=1', true));
        $rowHeaders = array('Label', 'Triggers', 'Action', 'Schedule', 'Start Date', 'End Date', 'Enabled');
        $this->assign('rowHeaders', $rowHeaders);
        
        $rows = array();
        /*
         * get all rule schedule
         */
        $scheduleRows = CRM_Triggers_BAO_RuleSchedule::getValues(array());
        foreach ($scheduleRows as $scheduleRow) {
            $row = array();
            $row['id'] = $scheduleRow['id'];
            $row['label'] = $scheduleRow['label'];
            /*
             * get related Triggers
             */
            $triggerRows = CRM_Triggers_BAO_RuleScheduleTrigger::getValues(array('rule_schedule_id' => $scheduleRow['id']));
            foreach ($triggerRows as $triggerRow) {
                $rowChild = array();
                $rowChild['rule_schedule_trigger_id'] = $triggerRow['id'];
                $rowChild['trigger_rule_id'] = $triggerRow['trigger_rule_id'];
                $rowChild['andor'] = $triggerRow['andor'];
                $row['triggers'][] = $rowChild;
            }
            $row['action'] = $scheduleRow['action'];
            $row['schedule'] = $scheduleRow['schedule'];
            $row['start_date'] = $scheduleRow['start_date'];
            $row['end_date'] = $scheduleRow['end_date'];
            $row['is_active'] = $scheduleRow['is_active'];
            $rowActions = array();
            $viewUrl = CRM_Utils_System::url('civicrm/ruleschedule', 'action=view&reset=1&rsid='.$scheduleRow['id'], true);
            $editUrl = CRM_Utils_System::url('civicrm/ruleschedule', 'action=update&reset=1&rsid='.$scheduleRow['id'], true);
            $deleteUrl = CRM_Utils_System::url('civicrm/ruleschedule', 'action=delete&reset=1&rsid='.$scheduleRow['id'], true);
            $rowActions[] = '<a class="action-item" title="View action details" href="'.$viewUrl.'">View</a>';
            $rowActions[] = '<a class="action-item" title="Edit action" href="'.$editUrl. '">Edit</a>';
            $rowActions[] = '<a class="action-item" title="Delete action" href="'.$deleteUrl.'">Delete</a>';
            $row['actions'] = $rowActions;
            $rows[] = $row;
        }
        $this->assign('rows', $rows);
        parent::run();
    }
}
