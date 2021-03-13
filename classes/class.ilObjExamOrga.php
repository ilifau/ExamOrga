<?php

/**
 * Exam Organization objects
 */
class ilObjExamOrga extends ilObjectPlugin
{
    /** @var ilAccess */
    public $access;

    /** @var ilExamOrgaPlugin */
    public $plugin;

    /**
     * Properties of the Object
     * @var ilExamOrgaData	$data;
     */
    public $data;

    /**
     * Records of the organized Exams
     * @var ilExamOrgaRecord[] $records (indexed by id)
     */
    public $records;

    /**
     * Fields defined for an Exam Records
     * @var ilExamOrgaField[] $fields (indexed by name)
     */
    public $fields;

    /**
	 * Constructor
	 *
	 * @access        public
	 * @param int $a_ref_id
	 */
	function __construct($a_ref_id = 0)
	{
	    global $DIC;
        $this->access = $DIC->access();

		parent::__construct($a_ref_id);
	}


	/**
	 * Get type.
	 */
	final function initType()
	{
		$this->setType(ilExamOrgaPlugin::ID);
	}

	/**
	 * Create object
	 */
	protected function doCreate()
	{
        $this->data = $this->plugin->getData($this->getId());
		$this->data->write();
	}

	/**
	 * Read data from db
	 */
    protected function doRead()
	{
        $this->data = $this->plugin->getData($this->getId());
	    $this->data->read();
	    $this->initFields();
	}

	/**
	 * Update data
	 */
    protected function doUpdate()
	{
        $this->data->write();
	}

	/**
	 * Delete data from db
	 */
    protected function doDelete()
	{
		$this->data->delete();
	}

	/**
	 * Do Cloning
     * @param self $new_obj
     * @param int $a_target_id
     * @param int $a_copy_id
	 */
    protected function doCloneObject($new_obj, $a_target_id, $a_copy_id = null)
	{
		$new_obj->data = clone $this->data;
		$new_obj->data->setObjId($new_obj->getId());
		$new_obj->update();
	}

	/**
	 * Set online
	 *
	 * @param        boolean                online
	 */
	public function setOnline($a_val)
	{
		$this->data->set('online', (bool)  $a_val);
	}

	/**
	 * Get online
	 * @return bool
	 */
    public function isOnline()
	{
		return (bool) $this->data->get('online');
	}


    /**
     * Check if the current user can view all records
     * @return bool
     */
    public function canViewAllRecords() {
        return $this->access->checkAccess('view_entries', '', $this->getRefId());
    }

    /**
     * Check if the current user can view all records
     * @return bool
     */
    public function canEditAllRecords() {
        return $this->access->checkAccess('write', '', $this->getRefId());
    }

    /**
     * Check if the current user can add a record
     * @return bool
     */
    public function canAddRecord() {
        return $this->access->checkAccess('add_entry', '', $this->getRefId());
    }

    /**
     * Check if the current user can view a certain record
     * @param ilExamOrgaRecord $record
     * @return bool
     */
    public function canViewRecord($record) {
        return ($this->canViewAllRecords() || $record->isOwner() || $record->isAdmin());
    }

    /**
     * Check if the current user can edit a certain record
     * @param ilExamOrgaRecord $record
     * @return bool
     */
    public function canEditRecord($record) {
        return ($this->canEditAllRecords() || $record->isOwner() || $record->isAdmin());
    }

    /**
     * Check if the current user can edit a certain record
     * @param ilExamOrgaRecord $record
     * @return bool
     */
    public function canDeleteRecord($record) {
        return ($this->canEditAllRecords() || $record->isOwner() || $record->isAdmin());
    }


    /**
     * Check if a field can be viewed
     * @var ilExamOrgaField $field
     * @return bool
     */
    public function canViewField($field) {
        switch ($field->status) {
            case ilExamOrgaField::STATUS_PUBLIC:
            case ilExamOrgaField::STATUS_FIXED:
            case ilExamOrgaField::STATUS_LOCKED:
                return true;
            case ilExamOrgaField::STATUS_HIDDEN:
                return $this->canEditAllRecords();
        }
        return false;
    }

    /**
     * Check if a field can be edited
     * @var ilExamOrgaField $field
     * @return bool
     */
    public function canEditField($field) {
        switch ($field->status) {
            case ilExamOrgaField::STATUS_PUBLIC:
                return true;
            case ilExamOrgaField::STATUS_FIXED:
                return false;
            case ilExamOrgaField::STATUS_LOCKED:
            case ilExamOrgaField::STATUS_HIDDEN:
               return $this->canEditAllRecords();
        }
        return false;
    }

    /**
     * Init the list of available fields
     */
    protected function initFields() {

        switch ($this->data->get('purpose')) {
            case 'oral':
                $fields = include_once(__DIR__ . '/../fields_record_oral.php');
                break;
            case 'written':
            default:
                $fields = include_once(__DIR__ . '/../fields_record_written.php');
                break;
        }


        foreach ($fields as $definition) {
            $name = (string) $definition['name'];
            $this->fields[$name] = ilExamOrgaField::factory($this, $definition);
        }
    }

    /**
     * Get the fields that are available for a user
     * @return ilExamOrgaField[] (indexed by name)
     */
    public function getAvailableFields() {
        $available = [];

        foreach ($this->fields as $field) {
            if ($this->canViewField($field)) {
                $available[$field->name] = $field;
            }
        }
        return $available;
    }

    /**
     * Get the active record conditions
     */
    public function getActiveConditions() {
        require_once (__DIR__ . '/condition/class.ilExamOrgaCondition.php');
        return ilExamOrgaCondition::getActiveConditions($this->getId());
    }
}
