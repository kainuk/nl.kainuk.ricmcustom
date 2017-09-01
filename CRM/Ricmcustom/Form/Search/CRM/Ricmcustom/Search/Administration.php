<?php

/**
 * A custom contact search
 */
class CRM_Ricmcustom_Form_Search_CRM_Ricmcustom_Search_Administration extends CRM_Contact_Form_Search_Custom_Base implements CRM_Contact_Form_Search_Interface {

  protected $_aclFrom = NULL;
  protected $_aclWhere = NULL;
  /**
   * @param $formValues
   */
  public function __construct(&$formValues) {
    parent::__construct($formValues);

    $this->_columns = array(
      ts('Contact ID') => 'contact_id',
      ts('Name') => 'sort_name',
      ts('Status') => 'status_id',
      ts('Remarks') => 'opmerkingen',
      ts('Language') => 'language',
      ts('Applied for Team') => 'aanmelding_team',
    );
  }

  /**
   * @param CRM_Core_Form $form
   */
  public function buildForm(&$form) {

    $events = CRM_Event_BAO_Event::getEvents(1);
    $roles  = CRM_Event_PseudoConstant::participantRole();
    $status = CRM_Event_PseudoConstant::participantStatus(null,"name in ('Registered','Awaiting approval','Partially paid','Pending refund','Pending from pay later')");
    $form->add('text', 'contact_id', ts('RICM-2017 identifier'));
    $form->add('select', 'event_id', ts('Event Name'), array('' => ts('- select -')) + $events);
    $form->add('select', 'role_id', ts('Participant Role'), array('' => ts('- select -')) + $roles);
    $form->add('select', 'status_id', ts('Status'), array('' => ts('- select -')) + $status);
    $form->add('select', 'language', ts('Language'), array('' => ts('- select -'),'nl'=>'Dutch','en'=>'English'));
    $form->add('select', 'team', ts('Applied for Team'), array('' => ts('- select -'),'1'=>'Yes','0'=>'No'));

    /**
     * You can define a custom title for the search form
     */
    $this->setTitle('RICM Administratie');

    /**
     * if you are using the standard template, this array tells the template what elements
     * are part of the search criteria
     */
    $form->assign('elements', array('contact_id','event_id','role_id','status_id','language','team'));
  }

  /**
   * @return array
   */
  public function summary() {
    $summary = array(
    );
    return $summary;
  }

  /**
   * @param int $offset
   * @param int $rowcount
   * @param null $sort
   * @param bool $returnSQL
   *
   * @return string
   */
  public function contactIDs($offset = 0, $rowcount = 0, $sort = NULL, $returnSQL = FALSE) {
    return $this->all($offset, $rowcount, $sort, FALSE, TRUE);
  }

  /**
   * @param int $offset
   * @param int $rowcount
   * @param null $sort
   * @param bool $includeContactIDs
   * @param bool $justIDs
   *
   * @return string
   */
  public function all($offset = 0, $rowcount = 0, $sort = NULL, $includeContactIDs = FALSE, $justIDs = FALSE) {
    if ($justIDs) {
      $selectClause = "contact_a.id as contact_id";
      $sort = 'contact_a.id';
    }
    else {
      $selectClause = "
contact_a.id      as contact_id,
display_name      as display_name,
sort_name         as sort_name,
language_5        as language,
participant.status_id         as status_id,
participant.id    as participant_id,
aanmelding_team_7 as aanmelding_team,
opmerkingen_8     as opmerkingen
";
    }

    return $this->sql($selectClause,
      $offset, $rowcount, $sort,
      $includeContactIDs, NULL
    );
  }

  /**
   * @return string
   */
  public function from() {
    $this->buildACLClause('contact_a');
    $from = "
FROM        civicrm_contact contact_a
INNER JOIN  civicrm_participant participant on (contact_a.id = participant.contact_id)
LEFT  JOIN  civicrm_value_ricm_participant_2 ricm on (participant.id = ricm.entity_id)
{$this->_aclFrom}
";
    return $from;
  }

  /**
   * @param bool $includeContactIDs
   *
   * @return string
   */
  public function where($includeContactIDs = FALSE) {
    $params = array();
    $where = "(1=1)";

    $contact_id = CRM_Utils_Array::value('contact_id',
      $this->_formValues
    );

    if ($contact_id) {
      $where = "contact_a.id = $contact_id";
    }

    $event_id = CRM_Utils_Array::value('event_id',
      $this->_formValues
    );

    if ($event_id) {
      $where .= " AND participant.event_id = $event_id";
    }

    $role_id = CRM_Utils_Array::value('role_id',
      $this->_formValues
    );

    if ($role_id) {
      $where .= " AND participant.role_id = $role_id";
    }

    $status_id = CRM_Utils_Array::value('status_id',
      $this->_formValues
    );

    if ($status_id) {
      $where .= " AND participant.status_id = $status_id";
    }

    $language =  CRM_Utils_Array::value('language',
      $this->_formValues
    );

    if ($language) {
      $where .= " AND ricm.language_5 = '$language'";
    }

    $team =  CRM_Utils_Array::value('team',
      $this->_formValues
    );

    if ($team) {
      $where .= " AND aanmelding_team_7 = '$team'";
    }

    return $this->whereClause($where, $params);
  }

  /**
   * @return string
   */
  public function templateFile() {
    return 'CRM/Contact/Form/Search/Custom.tpl';
  }

  /**
   * @return array
   */
  public function setDefaultValues() {
    return array(
      'event_id' => variable_get('ricm_event_id', '2'),
    );
  }

  /**
   * @param $row
   */
  public function alterRow(&$row) {
    if($row['aanmelding_team']=='1'){
      $row['aanmelding_team']='Yes';
    }  else {
      $row['aanmelding_team']='No';
    }
    $status = CRM_Event_PseudoConstant::participantStatus();
    $status_id = $row['status_id'];
    $row['status_id'] = $status[$status_id];
  }

  /**
   * @param $title
   */
  public function setTitle($title) {
    if ($title) {
      CRM_Utils_System::setTitle($title);
    }
    else {
      CRM_Utils_System::setTitle(ts('Search'));
    }
  }

  /**
   * @param string $tableAlias
   */
  public function buildACLClause($tableAlias = 'contact') {
    list($this->_aclFrom, $this->_aclWhere) = CRM_Contact_BAO_Contact_Permission::cacheClause($tableAlias);
  }

}
