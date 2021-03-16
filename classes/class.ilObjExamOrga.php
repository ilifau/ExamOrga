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
     * Fields defined for an Exam Records
     * @var ilExamOrgaField[] $record_fields (indexed by name)
     */
    protected $record_fields;

    /**
     * Fields defined for a record Condition
     * @var ilExamOrgaField[] $condition_fields (indexed by name)
     */
    protected $condition_fields;

    /**
     * Active conditions
     * @var ilExamOrgaCondition[] $active_conditions
     */
    protected $active_conditions;

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
	    $this->initRecordFields();
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
     * Init the list of available record fields
     */
    protected function initRecordFields() {

        $names = [
            'fields_record_' . $this->data->get(ilExamOrgaData::PARAM_PURPOSE) . '_'  . $this->data->get(ilExamOrgaData::PARAM_SEMESTER) . '.php',
            'fields_record_' . $this->data->get(ilExamOrgaData::PARAM_PURPOSE). '.php',
            'fields_record_written.php'
            ];

        $fields = [];
        foreach ($names as $name) {
            if (file_exists(__DIR__ . '/../' . $name)) {
                $fields = include_once(__DIR__ . '/../' . $name);
                break;
            }
        }

        foreach ($fields as $definition) {
            $name = (string) $definition['name'];
            $this->record_fields[$name] = ilExamOrgaField::factory($this, $definition);
        }
    }

    /**
     * Get the fields that are available for a user
     * @return ilExamOrgaField[] (indexed by name)
     */
    public function getAvailableFields() {
        $available = [];

        foreach ($this->record_fields as $field) {
            if ($this->canViewField($field)) {
                $available[$field->name] = $field;
            }
        }
        return $available;
    }

    /**
     * Get the gui fields for a condition
     */
    public function getConditionFields()
    {
        if (!isset($this->condition_fields)) {
            $fields = include_once(__DIR__ . '/../fields_condition.php');
            foreach ($fields as $definition) {
                $name = (string) $definition['name'];
                $this->condition_fields[$name] = ilExamOrgaField::factory($this, $definition);
            }
        }
        return $this->condition_fields;
    }


    /**
     * Get the active record conditions
     */
    public function getActiveConditions() {

        if (!isset($this->active_conditions)) {
            require_once (__DIR__ . '/condition/class.ilExamOrgaCondition.php');
            $this->active_conditions = ilExamOrgaCondition::getActiveConditions($this->getId());
        }
        return $this->active_conditions;
    }

    /**
     * Check a record against the active conditions
     *
     * @param ilExamOrgaRecord $record
     * @param ilExamOrgaRecord $original
     * @return array ['failures' => string[], 'warnings' => string[] ]
     */
    public function checkConditions($record, $original = null) {

        $failures = [];
        $warnings = [];
        foreach ($this->getActiveConditions() as $cond) {
            if (!$cond->checkRecord($record)) {

                switch ($cond->level) {
                    case ilExamOrgaCondition::LEVEL_HARD:
                        $failures[] = $cond->failure_message;
                        break;

                    case ilExamOrgaCondition::LEVEL_SOFT:
                        if ((!isset($original) || $cond->checkRecord($original)) && !$this->canEditAllRecords()) {
                            $failures[] = $cond->failure_message;
                        }
                        else {
                            $warnings[] = $cond->failure_message;
                        }
                        break;

                    case ilExamOrgaCondition::LEVEL_WARN:
                        $warnings[] = $cond->failure_message;
                        break;
                }
            }
        }

        return ['failures' => $failures, 'warnings' => $warnings];
    }
}
