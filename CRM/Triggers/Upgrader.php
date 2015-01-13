<?php

/**
 * Collection of upgrade steps
 */
class CRM_Triggers_Upgrader extends CRM_Triggers_Upgrader_Base {

  // By convention, functions that look like "function upgrade_NNNN()" are
  // upgrade tasks. They are executed in order (like Drupal's hook_update_N).

  /**
   * Example: Run an external SQL script when the module is installed
   */
  public function install() {
    $this->executeSqlFile('sql/install.sql');
    $this->addProcessedActivityType();
    $this->addAutomatedHandlingTag();
  }

  /**
   * Example: Run an external SQL script when the module is uninstalled
   */
  public function uninstall() {
   $this->executeSqlFile('sql/uninstall.sql');
  }
  
  /**
   * Upgrade 1001
   *
   * @return TRUE on success
   * @throws Exception
   */ 
  public function upgrade_1001() {
    $this->ctx->log->info('Applying update 1001');
    // this path is relative to the extension base dir
    $this->executeSqlFile('sql/upgrade_1001.sql');
    return TRUE;
  }
  
  /**
   * Upgrade 1002
   *
   * @return TRUE on success
   * @throws Exception
   */ 
  public function upgrade_1002() {
    $this->ctx->log->info('Applying update 1002');
    // this path is relative to the extension base dir
    $this->executeSqlFile('sql/upgrade_1002.sql');
    return TRUE;
  }
  
  /**
   * Upgrade 1003
   *
   * @return TRUE on success
   * @throws Exception
   */ 
  public function upgrade_1003() {
    $this->ctx->log->info('Applying update 1003');
    // this path is relative to the extension base dir
    $this->executeSqlFile('sql/upgrade_1003.sql');
    return TRUE;
  }
  
  /**
   * Upgrade 1005
   *
   * @return TRUE on success
   * @throws Exception
   */ 
  public function upgrade_1005() {
    $this->ctx->log->info('Applying update 1005');
    // this path is relative to the extension base dir
    $this->executeSqlFile('sql/upgrade_1005.sql');
    return TRUE;
  }
  
  /**
   * Upgrade 1006
   *
   * @return TRUE on success
   * @throws Exception
   */ 
  public function upgrade_1006() {
    $this->ctx->log->info('Applying update 1006');
    // this path is relative to the extension base dir
    $this->executeSqlFile('sql/upgrade_1006.sql');
    return TRUE;
  }
  
  /**
   * Upgrade 1007
   *
   * @return TRUE on success
   * @throws Exception
   */ 
  public function upgrade_1007() {
    $this->ctx->log->info('Applying update 1007');
    // this path is relative to the extension base dir
    $this->executeSqlFile('sql/upgrade_1007.sql');
    return TRUE;
  }
  
  /**
   * Upgrade 1008
   *
   * @return TRUE on success
   * @throws Exception
   */ 
  public function upgrade_1008() {
    $this->ctx->log->info('Applying update 1008');
    // this path is relative to the extension base dir
    $this->addAutomatedHandlingTag();
    return TRUE;
  }
  
  protected function addProcessedActivityType() {
    $params = array (
      'name' => 'TriggerProcessed',
      'label' => 'Trigger processed',
      'weight' => 1,
      'is_active' => 1,
    );
    $this->createActivityType($params);
  }
  
  protected function addAutomatedHandlingTag() {
    $params = array (
      'name' => 'Automated Handling',
      'description' => 'Contact could be processed with the triggers',
      'is_selectable' => 1,
      'is_reserved' => 1,
      'user_for' => 'civicrm_contact',
    );
    $this->createTag($params);
  }
  
  protected function createTag($params) {
    if (!isset($params['name'])) {
      return;
    }
    
    $tag_id = $this->getTagId($params['name']);
    
    if ($tag_id === false) {
      $params['version'] = 3;
      civicrm_api('Tag', 'create', $params);
    }
  }
  
  protected function createActivityType($params) {
    if (!isset($params['name'])) {
      return;
    }
    
    $activity_id = $this->getActivityTypeId($params['name']);
    if ($activity_id === false) {
      $option_group = civicrm_api('OptionGroup', 'getsingle', array('name' => 'activity_type', 'version' => 3));
      if (isset($option_group['is_error']) && $option_group['is_error']) {
        return;
      }
      $params['option_group_id'] = $option_group['id'];
      $params['version'] = 3;
      civicrm_api('OptionValue', 'Create', $params); 
    }          
  }
  
  protected function getTagId($name) {
    $params['name'] = $name;
    $params['version'] = 3;
    $tag = civicrm_api('Tag', 'getsingle', $params);
    if (isset($tag['is_error']) && $tag['is_error']) {
      return false;
    }
    return $tag['id'];
  }
  
  protected function getActivityTypeId($name) {
    $option_group = civicrm_api('OptionGroup', 'getsingle', array('name' => 'activity_type', 'version' => 3));
    if (isset($option_group['is_error']) && $option_group['is_error']) {
      return false;
    }
    $params['option_group_id'] = $option_group['id'];
    $params['name'] = $name;
    $params['version'] = 3;
    $activity_type = civicrm_api('OptionValue', 'getsingle', $params);
    if (isset($activity_type['is_error']) && $activity_type['is_error']) {
      return false;
    }
    return $activity_type['id'];
  }

  /**
   * Example: Run a simple query when a module is enabled
   *
  public function enable() {
    CRM_Core_DAO::executeQuery('UPDATE foo SET is_active = 1 WHERE bar = "whiz"');
  }

  /**
   * Example: Run a simple query when a module is disabled
   *
  public function disable() {
    CRM_Core_DAO::executeQuery('UPDATE foo SET is_active = 0 WHERE bar = "whiz"');
  }

  /**
   * Example: Run a couple simple queries
   *
   * @return TRUE on success
   * @throws Exception
   *
  public function upgrade_4200() {
    $this->ctx->log->info('Applying update 4200');
    CRM_Core_DAO::executeQuery('UPDATE foo SET bar = "whiz"');
    CRM_Core_DAO::executeQuery('DELETE FROM bang WHERE willy = wonka(2)');
    return TRUE;
  } // */


  /**
   * Example: Run an external SQL script
   *
   * @return TRUE on success
   * @throws Exception
  public function upgrade_4201() {
    $this->ctx->log->info('Applying update 4201');
    // this path is relative to the extension base dir
    $this->executeSqlFile('sql/upgrade_4201.sql');
    return TRUE;
  } // */


  /**
   * Example: Run a slow upgrade process by breaking it up into smaller chunk
   *
   * @return TRUE on success
   * @throws Exception
  public function upgrade_4202() {
    $this->ctx->log->info('Planning update 4202'); // PEAR Log interface

    $this->addTask(ts('Process first step'), 'processPart1', $arg1, $arg2);
    $this->addTask(ts('Process second step'), 'processPart2', $arg3, $arg4);
    $this->addTask(ts('Process second step'), 'processPart3', $arg5);
    return TRUE;
  }
  public function processPart1($arg1, $arg2) { sleep(10); return TRUE; }
  public function processPart2($arg3, $arg4) { sleep(10); return TRUE; }
  public function processPart3($arg5) { sleep(10); return TRUE; }
  // */


  /**
   * Example: Run an upgrade with a query that touches many (potentially
   * millions) of records by breaking it up into smaller chunks.
   *
   * @return TRUE on success
   * @throws Exception
  public function upgrade_4203() {
    $this->ctx->log->info('Planning update 4203'); // PEAR Log interface

    $minId = CRM_Core_DAO::singleValueQuery('SELECT coalesce(min(id),0) FROM civicrm_contribution');
    $maxId = CRM_Core_DAO::singleValueQuery('SELECT coalesce(max(id),0) FROM civicrm_contribution');
    for ($startId = $minId; $startId <= $maxId; $startId += self::BATCH_SIZE) {
      $endId = $startId + self::BATCH_SIZE - 1;
      $title = ts('Upgrade Batch (%1 => %2)', array(
        1 => $startId,
        2 => $endId,
      ));
      $sql = '
        UPDATE civicrm_contribution SET foobar = whiz(wonky()+wanker)
        WHERE id BETWEEN %1 and %2
      ';
      $params = array(
        1 => array($startId, 'Integer'),
        2 => array($endId, 'Integer'),
      );
      $this->addTask($title, 'executeSql', $sql, $params);
    }
    return TRUE;
  } // */

}
