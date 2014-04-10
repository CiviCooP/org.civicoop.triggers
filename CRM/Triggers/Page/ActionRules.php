<?php
/**
 * Class ActionRules for page processing of CiviCRM Action Rules list
 * 
 * MAF Norge funded sprint
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 9 Apr 
 * 
 * Copyright (C) 2014 Coöperatieve CiviCooP U.A. <http://www.civicoop.org>
 * Licensed to MAF Norge <http://www.maf.no> and CiviCRM under the Academic Free License version 3.0.
 */

require_once 'CRM/Core/Page.php';

class CRM_Triggers_Page_ActionRules extends CRM_Core_Page {
    function run() {
        CRM_Utils_System::setTitle(ts('List of Action Rules'));
        $this->assign('addUrl', CRM_Utils_System::url('civicrm/actionrules', 'action=add&reset=1', true));
        $rowHeaders = array('Label', 'Entity', 'Action', 'Parameters');
        $this->assign('rowHeaders', $rowHeaders);
        
        $rows = array();
        $dataRows = CRM_Triggers_BAO_ActionRule::getValues(array());
        foreach($dataRows as $dataRow) {
            $row = array();
            $row['id'] = $dataRow['id'];
            $row['label'] = $dataRow['label'];
            $row['entity'] = $dataRow['entity'];
            $row['action'] = $dataRow['action'];
            $row['params'] = $dataRow['params'];
            $rowActions = array();
            $viewUrl = CRM_Utils_System::url('civicrm/actionrules', 'action=view&reset=1&aid='.$dataRow['id'], true);
            $editUrl = CRM_Utils_System::url('civicrm/actionrules', 'action=update&reset=1&aid='.$dataRow['id'], true);
            $deleteUrl = CRM_Utils_System::url('civicrm/actionrules', 'action=delete&reset=1&aid='.$dataRow['id'], true);
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
