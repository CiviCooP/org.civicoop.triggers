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
        $rowHeaders = array('Label', 'Entity', 'Action', '');
        $this->assign('rowHeaders', $rowHeaders);
        
        $dataRows = CRM_Triggers_BAO_Trigger::get();
        parent::run();
    }
}
