<?php 

class CRM_Triggers_QueryBuilder {
  
  protected $select = array('*');
  
  protected $from;
  
  protected $joins = array();
  
  /**
   *
   * @var subcondition (CRM_Triggers_QueryBuilder_Subcondition)
   */
  protected $where;
  
  /**
   *
   * @var subcondition (CRM_Triggers_QueryBuilder_Subcondition)
   */
  protected $having = array();
  
  protected $groupBys = array();
  
  public function __construct($from) {
    $this->from = $from;
    $this->where = new CRM_Triggers_QueryBuilder_Subcondition();
    $this->having = new CRM_Triggers_QueryBuilder_Subcondition();
  }
  
  public function addWhere(CRM_Triggers_QueryBuilder_ConditionInterface $cond) {
    $this->where->addCond($cond);
  }
  
  public function addHaving(CRM_Triggers_QueryBuilder_ConditionInterface $cond) {
    $this->having->addCond($cond);
  }
  
  public function addGroupBy($groupBy) {
    $this->groupBys = $groupBy;
  }
  
  public function addJoin($join, $key) {
    $this->joins[$key] = $join;
  }
  
  public function addSelect($select) {
    $this->select[] = $select;
  }
  
  public function toSql() {
    $select = implode(", ", $this->select);
    $joins = implode(" ", $this->joins);
    $groupBy = implode($this->groupBys);
    $where = $this->where->toSqlCondition();
    $having = $this->having->toSqlCondition();
    
    $sql = "SELECT ".$select." FROM ".$this->from;
    if (strlen($joins)) {
      $sql .= " ".$joins;
    }
    if (strlen($where)) {
      $sql .= " WHERE ".$where;
    }
    if (strlen($groupBy)) {
      $sql .= " GROUP BY ".$groupBy;
    }
    if (strlen($having)) {
      $sql .= " HAVING ".$having;
    }
    return $sql;
  }
}