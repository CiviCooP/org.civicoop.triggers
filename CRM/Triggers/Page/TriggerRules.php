<?php
/**
 * Class TriggerRules for page processing of CiviCRM Trigger Rules list
 * 
 * MAF Norge funded sprint
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 7 Apr 
 * 
 * Copyright (C) 2014 Coöperatieve CiviCooP U.A. <http://www.civicoop.org>
 * Licensed to MAF Norge <http://www.maf.no> and CiviCRM under the Academic Free License version 3.0.
 */

require_once 'CRM/Core/Page.php';

class CRM_Triggers_Page_TriggerRules extends CRM_Core_Page {
    function run() {
        CRM_Utils_System::setTitle(ts('List of Triggers'));
        $this->assign('addUrl', CRM_Utils_System::url('civicrm/triggerrules', 'action=add&reset=1', true));
        $rowHeaders = array('Label', 'Entity', 'Conditions?');
        $this->assign('rowHeaders', $rowHeaders);
        
        //$dataRows = CRM_Triggers_BAO_Trigger::get();
        $rows = array();
        $daoTriggers = CRM_Core_DAO::executeQuery("SELECT * FROM civicrm_trigger_rule");
        while ($daoTriggers->fetch()) {
            $row = array();
            $row['id'] = $daoTriggers->id;
            $row['label'] = $daoTriggers->label;
            $row['entity'] = $daoTriggers->entity;
            /**
             * EH 8 Apr 2014: not required at the moment as we do cron processing only.
             * @todo: put operation back to active when we enable the post hook
             */
            //$row['operation'] = $daoTriggers->operation;
            $queryCondition = '
                SELECT COUNT(*) AS countCondition FROM civicrm_trigger_rule_condition 
                WHERE trigger_rule_id = '.$daoTriggers->id;
            $daoCondition = CRM_Core_DAO::executeQuery($queryCondition);
            if ($daoCondition->fetch() && $daoCondition->countCondition > 0) {
                $row['condition'] = "Y";
            } else {
                $row['condition'] = "N";
            }            
            $rowActions = array();
            $viewUrl = CRM_Utils_System::url('civicrm/triggerrules', 'action=view&reset=1&tid='.$daoTriggers->id, true);
            $editUrl = CRM_Utils_System::url('civicrm/triggerrules', 'action=edit&reset=1&tid='.$daoTriggers->id, true);
            $deleteUrl = CRM_Utils_System::url('civicrm/triggerrules', 'action=delete&reset=1&tid='.$daoTriggers->id, true);
            $rowActions[] = '<a class="action-item" title="View trigger details" href="'.$viewUrl.'">View</a>';
            $rowActions[] = '<a class="action-item" title="Edit trigger" href="'.$editUrl. '">Edit</a>';
            $rowActions[] = '<a class="action-item" title="Delete trigger" href="'.$deleteUrl.'">Delete</a>';
            $row['actions'] = $rowActions;
            $rows[] = $row;
        }
        $this->assign('rows', $rows);
        parent::run();
    }
}
