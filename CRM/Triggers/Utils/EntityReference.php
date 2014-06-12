<?php

/**
 * This class exist because the references from dao to other objects
 * does not always exist in civicrm
 * 
 */

class CRM_Triggers_Utils_EntityReference {
  
  protected $refTable;
  protected $refKey;
  protected $refTypeColumn;
  protected $targetTable;
  protected $targetKey;

  public function __construct($refTable, $refKey, $targetTable = NULL, $targetKey = 'id', $refTypeColumn = NULL) {
    $this->refTable = $refTable;
    $this->refKey = $refKey;
    $this->targetTable = $targetTable;
    $this->targetKey = $targetKey;
    $this->refTypeColumn = $refTypeColumn;
  }
  
  public static function convertReferences($references) {
    $return = array();
    foreach($references as $ref) {
      $return[] = new CRM_Triggers_Utils_EntityReference($ref->getReferenceTable(), $ref->getReferenceKey(), $ref->getTargetTable(), $ref->getTargetKey(), $ref->getTypeColumn());
    }
    return $return;
  }

  public function getReferenceTable() {
    return $this->refTable;
  }

  public function getReferenceKey() {
    return $this->refKey;
  }

  public function getTypeColumn() {
    return $this->refTypeColumn;
  }

  public function getTargetTable() {
    return $this->targetTable;
  }

  public function getTargetKey() {
    return $this->targetKey;
  }

  /**
   * @return true if the reference can point to more than one type
   */
  public function isGeneric() {
    return ($this->refTypeColumn !== NULL);
  }
  
}