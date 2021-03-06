<?php

/* 
 * class to add a check on automated handling
 * 
 */

class CRM_Triggers_Utils_AutomatedHandling {
  
  /**
   * We only need one instance of this object. So we use the singleton
   * pattern and cache the instance in this variable
   *
   * @var object
   * @access private
   * @static
   */
  static private $_singleton = NULL;
  
  private $tag_id = false;
  
  private function __construct() {
    $params['name'] = 'Automated Handling';
    $params['version'] = 3;
    $tag = civicrm_api('Tag', 'getsingle', $params);
    if (isset($tag['is_error']) && $tag['is_error']) {
      throw new API_Exception('API Error: Tag.getsingle');
    }
    $this->tag_id = $tag['id'];
  }
  
  /**
   * Constructor and getter for the singleton instance
   *
   * @return instance of $config->userHookClass
   */
  static function singleton($fresh = FALSE) {
    if (self::$_singleton == NULL || $fresh) {
      self::$_singleton = new CRM_Triggers_Utils_AutomatedHandling();
    }
    return self::$_singleton;
  }
  
  /**
   * Add a condition for checking on automated tag is set
   * 
   * @param array $objects
   */
  public function checkActionExecution($objects, $params, CRM_Triggers_BAO_ActionRule $action_rule) {
    if (isset($objects['Contact'])) {
      $tag_params['tag_id'] = $this->tag_id;
      $tag_params['entity_id'] = $objects['Contact']->id;
      $class = get_class($objects['Contact']);
      $tag_params['entity_table'] = $class::getTableName();
      $tag_params['version'] = 3;
      $result = civicrm_api3('EntityTag', 'get', $tag_params);
      if (isset($result['is_error']) && $result['is_error']) {
        return false;
      }
      foreach($result['values'] as $value) {
        if (isset($value['tag_id']) && $value['tag_id'] == $this->tag_id) {
          return true; //tag exist
        }
      }
      return false; //tag doesn't exist
    }
    return true;    
  }
  
}

