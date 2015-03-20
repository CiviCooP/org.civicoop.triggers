<?php

require_once 'triggers.civix.php';

/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function triggers_civicrm_config(&$config) {
  _triggers_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function triggers_civicrm_xmlMenu(&$files) {
  _triggers_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function triggers_civicrm_install() {
  return _triggers_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function triggers_civicrm_uninstall() {
  return _triggers_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function triggers_civicrm_enable() {
  return _triggers_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function triggers_civicrm_disable() {
  return _triggers_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function triggers_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _triggers_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function triggers_civicrm_managed(&$entities) {
  return _triggers_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function triggers_civicrm_caseTypes(&$caseTypes) {
  _triggers_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function triggers_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _triggers_civix_civicrm_alterSettingsFolders($metaDataFolders);
}
/**
 * Implementation of hook civicrm_navigationMenu
 * to create a trigger_action menu
 * 
 * @author Erik Hommel (erik.hommel@civicoop.org http://www.civicoop.org)
 * @date 7 Apr 2014
 */
function triggers_civicrm_navigationMenu( &$params ) {
    $itemMain = array (
        'name'          =>  ts('Trigger Action Rules'),
        'permission'    => 'administer CiviCRM',
    );
    _triggers_civix_insert_navigation_menu($params, 'Administer', $itemMain);
    
    $listTrigger = array(
        'name'          =>  ts('List Trigger Rules'),
        'url'           =>  CRM_Utils_System::url('civicrm/triggerruleslist', '', true),
        'permission'    =>  'administer CiviCRM'
    );
    _triggers_civix_insert_navigation_menu($params, 'Administer/Trigger Action Rules', $listTrigger);
    
    $addTrigger = array(
        'name'          =>  ts('New Trigger Rule'),
        'url'           =>  CRM_Utils_System::url('civicrm/triggerrules', 'action=add&reset=1', true),
        'permission'    =>  'administer CiviCRM'
    );
    _triggers_civix_insert_navigation_menu($params, 'Administer/Trigger Action Rules', $addTrigger);
    
    $listAction = array(
        'name'          =>  ts('List Action Rules'),
        'url'           =>  CRM_Utils_System::url('civicrm/actionruleslist', '', true),
        'permission'    =>  'administer CiviCRM'
    );
    _triggers_civix_insert_navigation_menu($params, 'Administer/Trigger Action Rules', $listAction);
    
    $addAction = array(
        'name'          =>  ts('New Action Rule'),
        'url'           =>  CRM_Utils_System::url('civicrm/actionrule', 'action=add&reset=1', true),
        'permission'    =>  'administer CiviCRM'
    );
    _triggers_civix_insert_navigation_menu($params, 'Administer/Trigger Action Rules', $addAction);
    
    $listTriggerAction = array(
        'name'          =>  ts('List Schedule Rules'),
        'url'           =>  CRM_Utils_System::url('civicrm/ruleschedulelist', '', true),
        'permission'    =>  'administer CiviCRM'
    );
    _triggers_civix_insert_navigation_menu($params, 'Administer/Trigger Action Rules', $listTriggerAction);
    
    $addTriggerAction = array(
        'name'          =>  ts('New Schedule Rule'),
        'url'           =>  CRM_Utils_System::url('civicrm/ruleschedule', 'action=add&reset=1', true),
        'permission'    =>  'administer CiviCRM'
    );
    _triggers_civix_insert_navigation_menu($params, 'Administer/Trigger Action Rules', $addTriggerAction);
    
}

/**
 * Implement hook_civicrm_trigger_check_action_execution
 * 
 * So we can check if a conact has the tag automated handling
 * 
 * @param type $doProcessing
 * @param type $objects
 * @param type $params
 * @param CRM_Triggers_BAO_ActionRule $action_rule
 */
function triggers_civicrm_trigger_check_action_execution(&$doProcessing, $objects, $params, CRM_Triggers_BAO_ActionRule $action_rule) {
  $automated_handling = CRM_Triggers_Utils_AutomatedHandling::singleton();
  //per 23 okt no handling of tags
  //$doProcessing = $automated_handling->checkActionExecution($objects, $params, $action_rule);
}
