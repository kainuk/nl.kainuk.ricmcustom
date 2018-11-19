<?php
use CRM_Ricmcustom_ExtensionUtil as E;

/**
 * Participant.Cleanup API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_participant_Cleanup_spec(&$spec) {
  $spec['event_id']['api.required'] = 1;
}

/**
 * Participant.Cleanup API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_participant_Cleanup($params) {

  $sql = 'select id from civicrm_participant where event_id = %1';
  $count=0;

  $dao = CRM_Core_DAO::executeQuery($sql,[
    1 => [$params['event_id'],'Integer']
  ]);

  while($dao->fetch()){

    $count++;

    $result = civicrm_api3('Participant','create',[
      'id' => $dao->id,
      'custom_6' => '', //dieet
      'custom_8' => '', //opmerkingen
      'custom_10' => '' //kinderen details
    ]);

  }

  $returnValues['count'] = $count++;
  $returnValues['event_id']='eventid';
  return civicrm_api3_create_success($returnValues, $params, 'Participant', 'cleanup');
}
