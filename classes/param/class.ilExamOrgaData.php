<?php
// Copyright (c) 2020 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

/**
 * ExamOrga plugin data class
 *
 * @author Fred Neumann <fred.neumann@ili.fau.de>
 *
 */
class ilExamOrgaData
{
    /** @var int obj_id */
    protected $obj_id;
	/**
	 * @var ilExamOrgaParam[]	$params		parameters: 	name => ilExamOrgaParam
	 */
	protected $params = array();

    /**
     * @var ilExamOrgaPlugin
     */
	protected $plugin;


	/**
	 * Constructor.
	 * @param ilPlugin
     * @param int obj_id;
	 */
	public function __construct($a_plugin_object, $a_obj_id)
	{
		$this->plugin = $a_plugin_object;
		$this->obj_id = $a_obj_id;

        $this->plugin->includeClass('param/class.ilExamOrgaParam.php');

        /** @var ilExamOrgaParam[] $params */
        $params = [];

        // purpose
        $params[] = ilExamOrgaParam::_create(
            'purpose', $this->plugin->txt('purpose'), $this->plugin->txt('purpose_info'), ilExamOrgaParam::TYPE_SELECT, 'written',
            [
                'written' => $this->plugin->txt('purpose_written'),
                'oral' => $this->plugin->txt('purpose_oral')
            ]
        );

        // semester
        for ($y = 2020; $y <= 2030; $y++) {
            $options[$y . 's'] = $this->plugin->txt('summer_term') . ' ' . $y;
            $options[$y . 'w'] = $this->plugin->txt('winter_term') . ' ' . $y . '/' . ($y + 1);
        }
        $params[] = ilExamOrgaParam::_create(
            'semester', $this->plugin->txt('semester'), '', ilExamOrgaParam::TYPE_SELECT, '2020w', $options
        );

        // online status
        $params[] = ilExamOrgaParam::_create(
            'online', $this->plugin->txt('online'), $this->plugin->txt('online_info'), ilExamOrgaParam::TYPE_BOOLEAN, 0
        );
        // test data
        $params[] = ilExamOrgaParam::_create(
            'testdata', $this->plugin->txt('testdata'), $this->plugin->txt('testdata_info'), ilExamOrgaParam::TYPE_BOOLEAN, 0
        );
        // introduction
        $params[] = ilExamOrgaParam::_create(
            'intro', $this->plugin->txt('introduction'), $this->plugin->txt('introduction_info'), ilExamOrgaParam::TYPE_RICHTEXT, null
        );



        foreach ($params as $param)
        {
            $this->params[$param->name] = $param;
        }
	}

    /**
     * Set the object Id
     * @param int $a_obj_id
     */
	public function setObjId($a_obj_id)
    {
	    $this->obj_id  = $a_obj_id;
    }

    /**
     * Get the array of all parameters
     * @return ilExamOrgaParam[]
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Add the items to a property form
     * @param ilPropertyFormGUI $form
     */
    public function addFormItems($form)
    {
        foreach ($this->getParams() as $param) {
            $form->addItem($param->getFormItem());
        }
    }

    /**
     * Set the data from a posted property form
     * @param ilPropertyFormGUI $form
     */
    public function setByForm($form)
    {
        foreach ($this->getParams() as $param) {
            $param->setByForm($form);
        }
    }


    /**
     * Get the value of a named parameter
     * @param $name
     * @return  mixed
     */
	public function get($name)
    {
        if (!isset($this->params[$name]))
        {
            return null;
        }
        else
        {
            return $this->params[$name]->value;
        }
    }

    /**
     * Set the value of the named parameter
     * @param string $name
     * @param mixed $value
     *
     */
    public function set($name, $value = null)
    {
       $param = $this->params[$name];

       if (isset($param))
       {
           $param->setValue($value);
       }
    }


    /**
     * Read the configuration from the database
     */
	public function read()
    {
        global $DIC;
        $ilDB = $DIC->database();

        $query = "SELECT * FROM xamo_data WHERE obj_id = ". $ilDB->quote($this->obj_id, 'integer');
        $res = $ilDB->query($query);
        while($row = $ilDB->fetchAssoc($res))
        {
            $this->set($row['param_name'], $row['param_value']);
        }
    }

    /**
     * Write the configuration to the database
     */
    public function write()
    {
        global $DIC;
        $ilDB = $DIC->database();

        foreach ($this->params as $param)
        {
            $ilDB->replace('xamo_data',
                array(
                    'obj_id' =>  array('integer', $this->obj_id),
                    'param_name' => array('text', $param->name)
                ),
                array('param_value' => array('text', (string) $param->value))
            );
        }
    }

    /**
     * Delete the data of an object
     */
    public function delete()
    {
        global $DIC;
        $ilDB = $DIC->database();

        $query = "DELETE FROM xamo_data WHERE obj_id = ". $ilDB->quote($this->obj_id, 'integer');
        $ilDB->query($query);
    }

    /**
     * Lookup a single value
     * @param int $obj_id
     * @param string $name
     * @return string
     */
    public static function _lookup($obj_id, $name)
    {
        global $DIC;
        $ilDB = $DIC->database();


        $query = "SELECT param_value FROM xamo_data WHERE obj_id = ". $ilDB->quote($obj_id, 'integer')
            . " AND param_name = " . $ilDB->quote($name, 'text');
        $ilDB->query($query);
        $res = $ilDB->query($query);
        if ($row = $ilDB->fetchAssoc($res))
        {
            return $row['param_value'];
        }
        return '';
    }

    /**
     * Get the semester as it is stored in mein campus
     * @return string (format: '20201' (summer term) or '20202' (winter term)
     */
    public function getCampusSemester()
    {
        $semster = $this->get('semester');
        $semester = str_replace('s', '1', $semster);
        $semester = str_replace('w', '2', $semster);
        return $semester;
    }


    /**
     * Get the object ids for a certain mode
     * @param string $mode  test|prod|any
     * @return int[]
     */
    public static function getObjectIdsForMode($mode)
    {
        global $DIC;
        $ilDB = $DIC->database();

        $query = "
            SELECT o.obj_id, d1.param_value AS `online`, d2.param_value AS `testdata` FROM object_data o
            INNER JOIN object_reference r ON r.obj_id = o.obj_id AND r.deleted IS NULL
            LEFT JOIN xamo_data d1 ON d1.obj_id = o.obj_id AND d1.param_name = 'online'
            LEFT JOIN xamo_data d2 ON d2.obj_id = o.obj_id AND d2.param_name = 'testdata'
            WHERE o.`type` = 'xamo'";

        $result = $ilDB->query($query);

        $obj_ids = [];
        while ($row = $ilDB->fetchAssoc($result)) {
            if ($row['online']) {
                if ($row['testdata'] && ($mode == 'test' || $mode == 'any')) {
                    $obj_ids[] = $row['obj_id'];
                }
                if (!$row['testdata'] && ($mode == 'prod' || $mode == 'any')) {
                    $obj_ids[] = $row['obj_id'];
                }
            }
        }
        return $obj_ids;
    }
}