<?php

interface CRM_Triggers_QueryBuilder_ConditionInterface {
  
  public function toSqlCondition();
  
  public function getLinkToPrevious();
}

