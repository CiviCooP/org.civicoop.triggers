<?php

/* 
 * Class with helper functions
 * 
 */

class CRM_Triggers_Utils {
  
  /**
   * Converts a lower entity name to an uper case one
   * e.g. activity_type becomes ActivityType
   * 
   * @param string $entity
   * @return string
   */
  public static function camelCaseEntity($entity) {
    //we could do
    //
    //_civicrm_api_get_camel_name($entity); 
    //
    //but that is not wise because the function name start with
    //an underscore which indicates it is a 'private' function
    //so we have copied the contents fo that function to our own function
    
    $fragments = explode('_', $entity);
    foreach ($fragments as & $fragment) {
      $fragment = ucfirst($fragment);
    }
    // Special case: UFGroup, UFJoin, UFMatch, UFField
    if ($fragments[0] === 'Uf') {
      $fragments[0] = 'UF';
    }
    return implode('', $fragments);
  }
  
}
