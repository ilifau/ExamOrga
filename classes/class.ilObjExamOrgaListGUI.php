<?php

/**
 * handles the presentation in container items (categories, courses, ...)
 * together with the corresponding ...Access class.
 *
 * PLEASE do not create instances of larger classes here. Use the
 * ...Access class to get DB data and keep it small.
 */
class ilObjExamOrgaListGUI extends ilObjectPluginListGUI
{

	/**
	 * Init type
	 */
	function initType() {
		$this->setType(ilExamOrgaPlugin::ID);
	}

	/**
	 * Get name of gui class handling the commands
	 */
	function getGuiClass()
	{
		return "ilObjExamOrgaGUI";
	}

	/**
	 * Get commands
	 */
	function initCommands()
	{
		return array
		(
			array(
				"permission" => "read",
				"cmd" => "showContent",
				"default" => true),
			array(
				"permission" => "write",
				"cmd" => "editProperties",
				"txt" => $this->txt("edit"),
				"default" => false)
		);
	}

	/**
	 * Get item properties
	 *
	 * @return        array           array of property arrays:
	 *                                "alert" (boolean) => display as an alert property (usually in red)
	 *                                "property" (string) => property name
	 *                                "value" (string) => property value
	 */
	function getProperties()
	{
		$props = array();

		require_once(__DIR__ . '/class.ilObjExamOrgaAccess.php');
		if (!ilObjExamOrgaAccess::checkOnline($this->obj_id))
		{
			$props[] = array("alert" => true, "property" => $this->txt("status"),
				"value" => $this->txt("offline"));
		}

		return $props;
	}
}
?>