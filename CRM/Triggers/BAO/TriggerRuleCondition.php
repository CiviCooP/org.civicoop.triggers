<?php

/* 
 * This is the BAO file for the trigger action table
 */

class CRM_Triggers_BAO_TriggerRuleCondition extends CRM_Triggers_DAO_TriggerRuleCondition {

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
        $condition = new CRM_Triggers_BAO_TriggerRuleCondition();
        if (!empty($params)) {
            $fields = self::fields();
            foreach ($params as $paramKey => $paramValue) {
                if (isset($fields[$paramKey])) {
                    $condition->$paramKey = $paramValue;
                }
            }
        }
        $condition->find();
        while ($condition->fetch()) {
            self::storeValues($condition, $row);
            $result[$row['id']] = $row;
        }
        return $result;
    }
}