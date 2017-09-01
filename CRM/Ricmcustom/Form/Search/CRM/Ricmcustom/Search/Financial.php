<?php

/**
 * A custom contact search
 */
class CRM_Ricmcustom_Form_Search_CRM_Ricmcustom_Search_Financial extends CRM_Contact_Form_Search_Custom_Base implements CRM_Contact_Form_Search_Interface {

  protected $_query;

  protected $_aclFrom = NULL;

  protected $_aclWhere = NULL;

  /**
   * @param $formValues
   */
  /**
   * @param $formValues
   */
  public function __construct(&$formValues) {
    parent::__construct($formValues);

    $this->normalize();
    $this->_columns = [
      '' => 'contact_type',
      ts('Name') => 'sort_name',
      ts('Address') => 'street_address',
      ts('City') => 'city',
      ts('State') => 'state_province',
      ts('Postal') => 'postal_code',
      ts('Country') => 'country',
      ts('Email') => 'email',
      ts('Phone') => 'phone',
    ];

    $params = CRM_Contact_BAO_Query::convertFormValues($this->_formValues);
    $returnProperties = [];
    $returnProperties['contact_sub_type'] = 1;

    $addressOptions = CRM_Core_BAO_Setting::valueOptions(CRM_Core_BAO_Setting::SYSTEM_PREFERENCES_NAME, 'address_options', TRUE, NULL, TRUE);

    foreach ($this->_columns as $name => $field) {
      if (in_array($field, [
          'street_address',
          'city',
          'state_province',
          'postal_code',
          'country',
        ]) && empty($addressOptions[$field])) {
        unset($this->_columns[$name]);
        continue;
      }
      $returnProperties[$field] = 1;
    }

    $this->_query = new CRM_Contact_BAO_Query($params, $returnProperties, NULL, FALSE, FALSE, 1, FALSE, FALSE);
  }

  /**
   * Normalize the form values to make it look similar to the advanced form
   * values this prevents a ton of work downstream and allows us to use the
   * same code for multiple purposes (queries, save/edit etc)
   *
   * @return void
   */
  public function normalize() {
    $contactType = CRM_Utils_Array::value('contact_type', $this->_formValues);
    if ($contactType && !is_array($contactType)) {
      unset($this->_formValues['contact_type']);
      $this->_formValues['contact_type'][$contactType] = 1;
    }

    $group = CRM_Utils_Array::value('group', $this->_formValues);
    if ($group && !is_array($group)) {
      unset($this->_formValues['group']);
      $this->_formValues['group'][$group] = 1;
    }

    $tag = CRM_Utils_Array::value('tag', $this->_formValues);
    if ($tag && !is_array($tag)) {
      unset($this->_formValues['tag']);
      $this->_formValues['tag'][$tag] = 1;
    }

    return NULL;
  }

  /**
   * @param CRM_Core_Form $form
   */
  public function buildForm(&$form) {

    $this->setTitle(ts('Ricm Financial Search'));

    $form->add('text', 'contact_id', ts('RICM-2016 identifier'));
    $form->add('text', 'sort_name', ts('Name'));

    $form->assign('elements', ['contact_id', 'sort_name']);
  }

  /**
   * @return CRM_Contact_DAO_Contact
   */
  public function count() {
    return $this->_query->searchQuery(0, 0, NULL, TRUE);
  }

  /**
   * @param int $offset
   * @param int $rowCount
   * @param null $sort
   * @param bool $includeContactIDs
   * @param bool $justIDs
   *
   * @return CRM_Contact_DAO_Contact
   */
  public function all($offset = 0, $rowCount = 0, $sort = NULL, $includeContactIDs = FALSE, $justIDs = FALSE) {
    return $this->_query->searchQuery($offset, $rowCount, $sort, FALSE, $includeContactIDs, FALSE, $justIDs, TRUE);
  }

  /**
   * @return string
   */
  public function from() {
    $this->buildACLClause('contact_a');
    $from = $this->_query->_fromClause;
    $from .= "{$this->_aclFrom}";
    return $from;
  }

  /**
   * @param bool $includeContactIDs
   *
   * @return string|void
   */
  public function where($includeContactIDs = FALSE) {
    if ($whereClause = $this->_query->whereClause()) {
      if ($this->_aclWhere) {
        $whereClause .= " AND {$this->_aclWhere}";
      }
      return $whereClause;
    }
    return ' (1) ';
  }

  /**
   * @return string
   */
  public function templateFile() {
    return 'CRM/Contact/Form/Search/Custom.tpl';
  }

  /**
   * @return CRM_Contact_BAO_Query
   */
  public function getQueryObj() {
    return $this->_query;
  }

  /**
   * @param string $tableAlias
   */
  public function buildACLClause($tableAlias = 'contact') {
    list($this->_aclFrom, $this->_aclWhere) = CRM_Contact_BAO_Contact_Permission::cacheClause($tableAlias);
  }

}