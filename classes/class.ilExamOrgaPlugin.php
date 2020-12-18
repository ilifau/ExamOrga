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

        $ilDB->dropTable('xamo_config');
        $ilDB->dropTable('xamo_data');
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
}