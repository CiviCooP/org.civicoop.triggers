<?php

class CRM_Triggers_QueryBuilder_Subcondition implements CRM_Triggers_QueryBuilder_ConditionInterface {
  
  /**
   *
   * @var link to previous condition (e.g. AND/OR) 
   */
  public $linkToPrevious = 'AND';
  
  /**
   *
   * @var array of sub condition (CRM_Triggers_QueryBuilder_Condition)
   */
  protected $subconditions = array();
  
  public function addCond(CRM_Triggers_QueryBuilder_ConditionInterface $cond) {
    $this->subconditions[] = $cond;
  }
  
  public function getLinkToPrevious() {
    return $this->linkToPrevious;
  }
  
  public function toSqlCondition() {
    $s = "";
    foreach($this->subconditions as $c) {
      if (strlen($s)) {
        $s .= " ".$c->linkToPrevious;
      }
      $s .= " ".$c->toSqlCondition();
    }
  
    if (strlen($s)) {
      return "(".$s.")"; 
    }
    return "";
  }
}

