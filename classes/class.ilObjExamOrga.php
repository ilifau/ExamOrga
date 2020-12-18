<?php

/**
 * Exam Organization objects
 */
class ilObjExamOrga extends ilObjectPlugin
{
    /** @var ilExamOrgaPlugin */
    public $plugin;

    /**
     * @var ilExamOrgaData	$data;
     */
    public $data;

    /**
	 * Constructor
	 *
	 * @access        public
	 * @param int $a_ref_id
	 */
	function __construct($a_ref_id = 0)
	{
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
	function doCreate()
	{
		$this->data->write();
	}

	/**
	 * Read data from db
	 */
	function doRead()
	{
	    $this->data->read();
	}

	/**
	 * Update data
	 */
	function doUpdate()
	{
        $this->data->write();
	}

	/**
	 * Delete data from db
	 */
	function doDelete()
	{
		$this->data->delete();
	}

	/**
	 * Do Cloning
     * @param self $new_obj
     * @param int $a_target_id
     * @param int $a_copy_id
	 */
	function doCloneObject($new_obj, $a_target_id, $a_copy_id = null)
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
	function setOnline($a_val)
	{
		$this->data->set('online', (bool)  $a_val);
	}

	/**
	 * Get online
	 *
	 * @return        boolean                online
	 */
	function isOnline()
	{
		return (bool) $this->data->get('online');
	}
}
