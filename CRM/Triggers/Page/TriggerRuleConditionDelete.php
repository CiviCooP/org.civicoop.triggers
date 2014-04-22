<?php
/**
 * Class TriggerRuleConditionDelete to execute the delete action item on the
 * TriggerRule form
 * 
 * MAF Norge funded sprint
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 9 Apr 
 * 
 * Copyright (C) 2014 Coöperatieve CiviCooP U.A. <http://www.civicoop.org>
 * Licensed to MAF Norge <http://www.maf.no> and CiviCRM under the Academic Free License version 3.0.
 */
require_once 'CRM/Core/Page.php';
class CRM_Triggers_Page_TriggerRuleConditionDelete extends CRM_Core_Page {
    function run() {
        $triggerRuleConditionId = CRM_Utils_Request::retrieve('trcid', 'Integer');
        $triggerRuleId = CRM_Utils_Request::retrieve('tid', 'Integer');
        if (!empty($triggerRuleConditionId)) {
            CRM_Triggers_BAO_TriggerRuleCondition::deleteById($triggerRuleConditionId);
            $session = CRM_Core_Session::singleton();
            $session->setStatus('Condition deleted', 'Deleted', 'success');
        }
        CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/triggerrules', 'action=update&tid='.$triggerRuleId, true));
    }
}
