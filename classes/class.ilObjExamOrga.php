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

		// data will be read by doRead
		$this->data = $this->plugin->getData(($this->getId()));
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
		$this->data->write();
	}

	/**
	 * Read data from db
	 */
    protected function doRead()
	{
	    $this->data->read();
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
	 *
	 * @return        boolean                online
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
        return $this->access->checkAccess('write', '', $this->getRefId());
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
     */
    public function canAddRecord() {
        return $this->access->checkAccess('write', '', $this->getRefId());
    }

    /**
     * Check if the current user can view a certain record
     * @param ilExamOrgaRecord $record
     */
    public function canViewRecord($record) {
        return $this->access->checkAccess('read', '', $this->getRefId());
    }

    /**
     * Check if the current user can edit a certain record
     * @param ilExamOrgaRecord $record
     */
    public function canEditRecord($record) {
        return $this->access->checkAccess('write', '', $this->getRefId());
    }

    /**
     * Check if the current user can edit a certain record
     * @param ilExamOrgaRecord $record
     */
    public function canDeleteRecord($record) {
        return $this->access->checkAccess('write', '', $this->getRefId());
    }

    /**
     * Init the list of available fields
     */
    protected function initFields() {
        $fields = (array) include_once(__DIR__ . '../fields.php');

        foreach ($fields as $definition) {
            $name = (string) $definition['name'];
            $this->fields[$name] = ilExamOrgaField::factory($this->plugin, $definition);
        }
    }

    /**
     * Get the fields that are available for a user
     * @return ilExamOrgaField[] (indexed by name)
     */
    public function getAvailableFields() {
        $available = [];

        foreach ($this->fields as $field) {
            if ($this->canEditAllRecords() || $field->status !== ilExamOrgaField::STATUS_HIDDEN) {
                $available[$field->name] = $field;
            }
        }
        return $available;
    }
}
