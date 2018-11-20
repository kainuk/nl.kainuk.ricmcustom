<?php
/**
 *  @author Klaas Eikelboom (CiviCooP) <klaas.eikelboom@civicoop.org>
 *  @date 11/19/18 9:39 PM
 *  @license AGPL-3.0
 */
class CRM_Ricmcustom_ParticipantTokens {

  var $participantId;

  /**
   * CRM_Ricmcustom_ParticipantTokens constructor.
   *
   * @param $contactId
   */
  public function __construct($contactId) {
    $this->participantId = CRM_Core_DAO::singleValueQuery('select id from civicrm_participant where event_id=%1 and contact_id=%2',[
        //1 => [variable_get('ricm_event_id', '2'),'Integer'],
        1 => [4,'Integer'],
        2 => [$contactId,'Integer']
      ]);
  }

  private function totalPaid() {

    $query = "
     select ifnull(sum(ft.total_amount),0) total_amount
     from   civicrm_contribution cnt
     join   civicrm_participant_payment pap on (pap.contribution_id = cnt.id)
     join   civicrm_entity_financial_trxn et on (et.entity_id=cnt.id and et.entity_table = 'civicrm_contribution')
     join   civicrm_financial_trxn ft on (ft.id = et.financial_trxn_id and ft.to_financial_account_id=6)
     where  pap.participant_id = %1";

    return CRM_Core_DAO::singleValueQuery($query, [
      1 => [$this->participantId,'Integer']
    ]);
  }

  private function roomPreference() {

    $query = "select li.label, li.unit_price from civicrm_line_item li
              join civicrm_price_field cpf on (li.price_field_id = cpf.id and cpf.name=%2)
              where entity_id=%1 and entity_table = 'civicrm_participant'";
    $dao = CRM_Core_DAO::executeQuery($query,[
      1 => [$this->participantId,'Integer'],
      2 => ['Kamer','String']
    ]);
    if($dao->fetch()){
      return "{$dao->label} ({$dao->unit_price} EURO)";
    } else {
      return false;
    }

  }

  private function extras() {

    $result = [];

    $query = "select li.label, li.unit_price from civicrm_line_item li
              join civicrm_price_field cpf on (li.price_field_id = cpf.id and cpf.name=%2)
              where entity_id=%1 and entity_table = 'civicrm_participant'";
    $dao = CRM_Core_DAO::executeQuery($query,[
      1 => [$this->participantId,'Integer'],
      2 => ['Extra','String']
    ]);
    while($dao->fetch()){
      $result[] =  "{$dao->label} ({$dao->unit_price} EURO)";
    }
    return implode('<br/>',$result);

  }

  private function fee() {
    $query = "
     select fee_amount
     from   civicrm_participant
     where id = %1";
    return CRM_Core_DAO::singleValueQuery($query, [
      1 => [$this->participantId,'Integer'],
    ]);
  }

  public function tokenValues() {
    if($this->participantId) {

      $participant = civicrm_api3('Participant','getsingle',[
        'id' => $this->participantId,
      ]);

      return [
        'ricm.fee' => $this->fee(),
        'ricm.paid' => $this->totalPaid(),
        'ricm.due' => $this->fee()-$this->totalPaid(),

        'ricm.roompreference' => $this->roomPreference(),
        'ricm.extras' => $this->extras(),
        'ricm.children' => $participant['custom_9'],
        'ricm.childrendetails' => $participant['custom_10'],
        'ricm.team' => $participant['custom_7'],
        'ricm.language' => $participant['custom_5'],
        'ricm.diet' => $participant['custom_6'],
        'ricm.remarks' => $participant['custom_8'],
      ];
    } else {
      return [
        'ricm.fee' => 'Onbekend',
        'ricm.paid' => 'Onbekend',
        'ricm.due' => 'Onbekend',
        'ricm.roompreference' => 'Onbekend',
        'ricm.extras' => 'Onbekend',
        'ricm.children' => 'Onbekend',
        'ricm.childrendetails' => 'Onbekend',
        'ricm.team' => 'Onbekend',
        'ricm.language' => 'Onbekend',
        'ricm.diet' => 'Onbekend',
        'ricm.remarks' => 'Onbekend',
      ];
    }
  }

}