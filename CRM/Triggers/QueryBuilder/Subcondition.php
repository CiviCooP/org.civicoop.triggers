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
    foreach($this->subconditions as $c) {
      if ($c == $cond) {
        return; //condition is already added
      }
    }
    $this->subconditions[] = $cond;
  }
  
  public function getLinkToPrevious() {
    return $this->linkToPrevious;
  }
  
  public function toSqlCondition() {
    $s = "";
    if (count($this->subconditions) > 0) {
      foreach($this->subconditions as $c) {
        $sc = $c->toSqlCondition();
        if (strlen($s) && strlen($sc)) {
          $s .= " ".$c->linkToPrevious;
        }
        if (strlen($sc)) {
          $s .= " ".$sc;
        }
      }
    }
    $s = trim($s);
  
    if (strlen($s)) {
      return "(".$s.")"; 
    }
    return "";
  }
}

