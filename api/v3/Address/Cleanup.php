<?php
use CRM_Ricmcustom_ExtensionUtil as E;

/**
 * Address.Cleanup API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_address_Cleanup_spec(&$spec) {
}

/**
 * Address.Cleanup API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_address_Cleanup($params) {

  if(isset($params['limit'])){
    $limit = $params['limit'];
  } else {
    $limit = 10;
  }

  $sql = 'select id,contact_id,city from civicrm_address limit %1';
  $dao = CRM_Core_DAO::executeQuery($sql,[
    1 => [$limit,'Integer']
  ]);

  while($dao->fetch()){

    $contactId = $dao->contact_id;
    if(isset($contactId)){
      $contact = civicrm_api3('Contact','getsingle',[
        'id' => $contactId,
      ]);
      if(isset($dao->city)){
        civicrm_api3('Contact','create',[
          'id' => $contactId,
          'custom_11' => $dao->city
        ]);
      }
      if($contact['contact_type']=='Individual'){
        civicrm_api3('Address','delete',[
          'id'=> $dao->id,
        ]);
      }
    }

  }



}
