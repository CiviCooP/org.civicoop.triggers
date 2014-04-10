<?php

/* 
 * This is the BAO file for the trigger action table
 */

class CRM_Triggers_BAO_TriggerAction extends CRM_Triggers_DAO_TriggerAction {

  /**
   * Find trigger actions which are ready for processing
   * 
   * @param bool $fetchFirst fetch the first record
   */
  public static function findForProcessing($fetchFirst = FALSE) {
    $trigger_action = new CRM_Triggers_BAO_TriggerAction();
    
    $trigger_action->selectAdd();
    $trigger_action->selectAdd("*");
    $trigger_action->whereAdd('is_active = 1');
    $trigger_action->whereAdd('(start_date IS NULL OR start_date <= NOW())');
    $trigger_action->whereAdd('(end_date IS NULL OR end_date >= NOW())');
    $trigger_action->whereAdd('(next_run <= NOW())');
    
    $trigger_action->find($fetchFirst);
    
    return $trigger_action;
  }
  
  /**
   * Returns the found entities which should be processed by the trigger
   */
  public function findEntities($fetchFirst=false) {
    //check if this object is valid for finding entities
    if (empty($this->trigger_rule_id)) {
      throw new CRM_Triggers_Exception_InvalidTriggerAction("Trigger rule ID is not set");
    }
    
    $trigger = new CRM_Triggers_BAO_TriggerRule();
    $trigger->selectAdd();
    $trigger->selectAdd('*');
    $trigger->whereAdd("id = '".$this->trigger_rule_id."'");
    $trigger->find(TRUE);
           
    $dao = CRM_Triggers_BAO_TriggerRule::getEntityDAO($trigger->entity);
    //build condition for this dao
    
    $qb = new CRM_Triggers_QueryBuilder($dao->tableName());
    
    $where = new CRM_Triggers_QueryBuilder_Subcondition();
    $having = new CRM_Triggers_QueryBuilder_Subcondition();
    $conditions = CRM_Triggers_BAO_TriggerRuleCondition::findByTriggerRuleId($trigger->id);
    $conditionsCount = 0;
    while($conditions->fetch()) {
      $conditions->parseCondition($where, $having, $qb);
      $conditionsCount ++;
    }
    
    if ($conditionsCount <= 0) {
      throw new CRM_Triggers_Exception_NoConditions('No active conditions found for this rule, stop processing');
    }
    
    //add the conditions to the query builder
    $qb->addWhere($where);
    $qb->addHaving($having);
    
    //add a join on civicrm_processed_trigger
    $alreadyProcessedCond = "`id` NOT IN ("
        . "SELECT `entity_id` FROM `civicrm_processed_trigger` "
        . "WHERE `entity` = '".$dao->escape($trigger->entity)."' "
        . "AND `trigger_action_id` = '".$dao->escape($this->id)."')";
    $qb->addWhere($alreadyProcessedCond);
    
    $entityDao = CRM_Core_DAO::executeQuery($qb->toSql());
    
    
    if ($entityDao->find($fetchFirst)!==false) {
      return $entityDao;
    }
    
    throw new CRM_Triggers_Exception_QueryError("Query error on finding entities");
  }
    /**
     * Function to get conditions
     * 
     * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
     * @date 8 Apr 2014
     * @param array $params name/value pairs with field names/values
     * @return array $result found rows with data
     * @access public
     * @static
     */
    public static function getValues($params) {
        $result = array();
        $triggerAction = new CRM_Triggers_BAO_TriggerAction();
        if (!empty($params)) {
            $fields = self::fields();
            foreach ($params as $paramKey => $paramValue) {
                if (isset($fields[$paramKey])) {
                    $triggerAction->$paramKey = $paramValue;
                }
            }
        }
        $triggerAction->find();
        while ($triggerAction->fetch()) {
            self::storeValues($triggerAction, $row);
            $result[$row['id']] = $row;
        }
        return $result;
    }
    
    /**
     * Reschedule this trigger/action combination and set last run date
     */
    public function reschedule() {
      $this->last_run = date('YmdHis');

      if (strlen($this->schedule)) {
        $date = new DateTime();
        $date->modify($this->schedule);
        $this->next_run = $date->format('YmdHis');
      }
      
      $this->save();
    }
  
}

