<?php
// This file declares a managed database record of type "Activity Type".
// The record will be automatically inserted, updated, or deleted from the
// database as appropriate. For more details, see "hook_civicrm_managed" at:
// http://wiki.civicrm.org/confluence/display/CRMDOC42/Hook+Reference
return array (
  0 => 
  array (
    'name' => 'Trigger Processed activity',
    'entity' => 'ActivityType',
    'params' => 
    array (
      'version' => 3,
      'name' => 'TriggerProcessed',
      'label' => 'Trigger processed',
      'weight' => 1,
      'is_active' => 1,
      //'is_reserved' => 1,
    ),
  ),
);
