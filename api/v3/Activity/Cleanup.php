<?php
use CRM_Ricmcustom_ExtensionUtil as E;

/**
 * Activity.Cleanup API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_activity_Cleanup_spec(&$spec) {
  $spec['limit']['api.required'] = 1;
}

/**
 * Activity.Cleanup API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_activity_Cleanup($params) {

  // 49 is the webform activity (I do not expect this cleanup to be used in another instance
  $sql = 'select id from civicrm_activity where activity_type_id = %1 and details is not null limit %2';
  $count = 0;
  $dao = CRM_Core_DAO::executeQuery($sql,[
    1 => [49,'Integer'],
    2 => [$params['limit'],'Integer']
    ]);

  while($dao->fetch()){
    $count++;
    $result = civicrm_api3('Activity','create',
      ['id' => $dao->id,'details'=>'']);
  }
  $returnValues['count'] = $count;

  return civicrm_api3_create_success($returnValues, $params, 'Participant', 'cleanup');
}
