<?php
// Copyright (c) 2020 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE
 
/**
 * Basic plugin file
 *
 * @author Fred Neumann <fred.neumann@fau.de>
 */
class ilExamOrgaPlugin extends ilRepositoryObjectPlugin
{
     const ID = "xamo";

    /** @var ilExamOrgaConfig */
    protected $config;

    /** @var self */
    protected static $instance;

    /**
     * Get the Plugin name
     * must correspond to the plugin subdirectory
     * @return string
     */
    public function getPluginName()
	{
		return "ExamOrga";
	}

    /**
     * @inheritdoc
     */
    public function getParentTypes()
    {
        return array("cat", "crs", "grp", "fold");
    }

    /**
     * @inheritdoc
     */
    public function allowCopy()
    {
        return false;
    }

    /**
     * Uninstall custom data of this plugin
     */
    protected function uninstallCustom()
    {
        global $DIC;
        $ilDB = $DIC->database();

        $ilDB->dropTable('xamo_campus');
        $ilDB->dropTable('xamo_cond');
        $ilDB->dropTable('xamo_config');
        $ilDB->dropTable('xamo_data');
        $ilDB->dropTable('xamo_link');
        $ilDB->dropTable('xamo_note');
        $ilDB->dropTable('xamo_record');
    }

    /**
     * Get the plugin instance
     * @return ilExamOrgaPlugin
     */
    public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get the data set for an object
     * @param $obj_id
     * @return ilExamOrgaData
     */
	public function getData($obj_id)
    {
        require_once (__DIR__ . '/param/class.ilExamOrgaData.php');
        return new ilExamOrgaData($this, $obj_id);
    }


    /**
     * Get the plugin configuration
     * @return ilExamOrgaConfig
     */
    public function getConfig()
    {
        if (!isset($this->config))
        {
            require_once (__DIR__ . '/param/class.ilExamOrgaConfig.php');
            $this->config = new ilExamOrgaConfig($this);
        }
        return $this->config;
    }

    /**
     * Check if the user has administrative access
     * @return bool
     */
    public function hasAdminAccess()
    {
        global $DIC;
        return $DIC->rbac()->system()->checkAccess("visible", SYSTEM_FOLDER_ID);
    }


    /**
	 * Get a user preference
	 * @param string	$name
	 * @param mixed		$default
	 * @return mixed
	 */
	public function getUserPreference($name, $default = false)
	{
		global $ilUser;
		$value = $ilUser->getPref($this->getId().'_'.$name);
		if ($value !== false)
		{
			return $value;
		}
		else
		{
			return $default;
		}
	}


	/**
	 * Set a user preference
	 * @param string	$name
	 * @param mixed		$value
	 */
	public function setUserPreference($name, $value)
	{
		global $ilUser;
		$ilUser->writePref($this->getId().'_'.$name, $value);
	}


    /**
     * Get a plugin text and use the variable, if not translated
     *
     * @param string $a_var
     * @return string
     */
    public function txt(string $a_var) : string
    {
        $txt = parent::txt($a_var);
        if (substr($txt, 0, 5) == '-rep_') {
            return $a_var;
        }
        return $txt;
    }


    /**
     * Handle a call by the cron job plugin
     * @return	array [success, message]
     * @throws	Exception
     */
    public function handleCronJob()
    {
        if (!ilContext::usesHTTP()) {
            echo "ExamOrga: handle cron job...\n";
        }

        $messages = [];

        require_once (__DIR__ . '/class.ilExamOrgaCronHandler.php');
        $handler = new ilExamOrgaCronHandler($this);

        // update the list of exams
        if ($handler->updateExams()) {
            $messages[] = $this->txt('campus_exams_loaded');
            if (!ilContext::usesHTTP()) {
                echo $this->txt('campus_exams_loaded') . "\n";
            }
        }
        else {
            $messages[] = $this->txt('campus_exams_not_loaded');
        }

        $checked = $handler->checkRecords();
        $messages[] = sprintf($this->txt('x_records_checked'), $checked);
        if (!ilContext::usesHTTP()) {
            echo sprintf($this->txt('x_records_checked'), $checked) . "\n";
        }

        if (!ilContext::usesHTTP()) {
            echo "ExamOrga: finished.\n";
        }

        return [true, implode(' | ', $messages)];
    }

    /**
     * Get the active objects
     * @return array    obj_id => title
     */
    public function getActiveObjects()
    {
        $query = "
            SELECT o.obj_id, o.title, 
            d1.param_value AS `online` 
            FROM object_data o
            INNER JOIN object_reference r ON r.obj_id = o.obj_id AND r.deleted IS NULL
            LEFT JOIN xamo_data d1 ON d1.obj_id = o.obj_id AND d1.param_name = 'online'
            WHERE o.`type` = 'xamo'
            ";

        $result = $this->db->query($query);

        $objects = [];
        while ($row = $this->db->fetchAssoc($result)) {
            if (!isset($row['online']) || !$row['online']) {
                continue;
            }
            $objects[$row['obj_id']] = $row['title'];

        }
        return $objects;
    }
}