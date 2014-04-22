<?php

class CRM_Triggers_QueryBuilder_Condition implements CRM_Triggers_QueryBuilder_ConditionInterface {
  
  /**
   *
   * @var String 
   */
  public $condition;
  
  /**
   *
   * @var link to previous condition (e.g. AND/OR) 
   */
  public $linkToPrevious = 'AND';
  
  public function __construct($cond) {
    $this->condition = $cond;
  }
  
  public function getLinkToPrevious() {
    return $this->linkToPrevious;
  }

  
  public function toSqlCondition() {
    return $this->condition;
  }
}

