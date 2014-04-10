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
  
  /**
   * Return a field from a dao, given by the fieldName
   * 
   * @param CRM_Core_DAO $dao
   * @param String $fieldName
   * @return boolean/array
   */
  public static function getFieldFromDao(CRM_Core_DAO $dao, $fieldName) {
    $daoClass = get_class($dao);
    $fields = $daoClass::fields();
    foreach($fields as $key => $field) {
      if ($key == $fieldName) {
        return $field;
      } elseif (isset($field['name']) && $field['name'] == $fieldName) {
        return $field;
      }
    }
    
    $entity = CRM_Core_DAO_AllCoreTables::getBriefName($daoClass);
    if (strtolower($entity) == 'contact' && isset($dao->contact_type)) {
      // Use sub-type if available, otherwise stick with 'Contact'
      $entity = $dao->contact_type;
    }
    $subtype = false;
    if (isset($dao->contact_sub_type)) {
      $subtype = $dao->contact_sub_type;
    }
    
    $customfields = CRM_Core_BAO_CustomField::getFields($entity, FALSE, FALSE, $subtype);
    foreach($customfields as $key => $field) {
      if ('custom_'.$key == $fieldName) {
        return $field;
      }
    }
    
    return false;
  }
  
  /**
   * Returns the contacts belonging to an entity
   * 
   * @param CRM_Core_DAO $objRef
   * @return array
   */
  public static function getContactsFromEntity(CRM_Core_DAO $objRef) {
    if ($objRef instanceof CRM_Contact_DAO_Contact) {
      return array($objRef); //return the current object
    }
    
    $class = get_class($objRef);
    $fields = $class::fields();
    $keyFields = $class::fieldKeys();
    
    $contact_ids = array();
    if ($objRef instanceof CRM_Activity_DAO_Activity) {
      //retrieve the targets of this activity
      $contact_ids = CRM_Activity_BAO_ActivityTarget::retrieveTargetIdsByActivityId($objRef->id);
    } elseif (isset($fields['contact_id']) || isset($keyFields['contact_id'])) {
      $contact_ids = array($objRef->contact_id);
    }
    
    $contacts = array();
    foreach($contact_ids as $contact_id) {
      $contact = new CRM_Contact_BAO_Contact();
      $contact->id = $contact_id;
      if ($contact->find(TRUE)) {
         $contacts[$contact_id] = $contact;
      }
    }
    
    return $contacts;
  }
  
}
