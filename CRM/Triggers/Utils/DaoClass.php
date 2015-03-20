<?php

class CRM_Triggers_Utils_DaoClass {
  
  public static function getDaoClassByFullName($fullName) {
    $daoEntity = CRM_Core_DAO_AllCoreTables::getFullName($fullName);
    if (empty($daoEntity)) {
      switch($fullName) {
        case 'ActivityTarget':
          $daoEntity = 'CRM_Activity_DAO_ActivityContact';
          break;
        case 'ActivityAssignment':
          $daoEntity = 'CRM_Activity_DAO_ActivityContact';
          break;
      }
    }
    return $daoEntity;
  }
  
}

