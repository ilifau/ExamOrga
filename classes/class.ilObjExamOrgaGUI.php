<?php

require_once(__DIR__ . "/class.ilExamOrgaPlugin.php");

/**
 * @ilCtrl_isCalledBy ilObjExamOrgaGUI: ilRepositoryGUI, ilAdministrationGUI, ilObjPluginDispatchGUI
 * @ilCtrl_Calls ilObjExamOrgaGUI: ilPermissionGUI, ilInfoScreenGUI, ilObjectCopyGUI, ilCommonActionDispatcherGUI, ilExportGUI
 * @ilCtrl_Calls ilObjExamOrgaGUI: ilExamOrgaRecordGUI
 */
class ilObjExamOrgaGUI extends ilObjectPluginGUI
{
    /** @var ilObjExamOrga */
	public $object;

	/** @var ilExamOrgaPlugin */
	public $plugin;

	/**
	 * Initialisation
	 */
	protected function afterConstructor()
	{
        // Description is not shown by ilObjectPluginGUI
        if (isset($this->object))
        {
            $this->tpl->setDescription($this->object->getDescription());
            $alerts = array();
            if (!$this->object->isOnline())
            {
                array_push($alerts, array(
                        'property' => $this->object->plugin->txt('status'),
                        'value' => $this->object->plugin->txt('offline'))
                );
            }
            $this->tpl->setAlertProperties($alerts);
        }
    }

	/**
	 * Get type.
	 */
	final function getType()
	{
		return ilExamOrgaPlugin::ID;
	}


	/**
	 * Handles all commands of this class, centralizes permission checks
	 */
	function performCommand($cmd)
	{
        $next_class = $this->ctrl->getNextClass();
        if (!empty($next_class)) {

            switch ($next_class) {
                case 'ilexamorgarecordgui':
                    $this->checkPermission('read');
                    $this->tabs->activateTab("content");
                    require_once(__DIR__ . '/record/class.ilExamOrgaRecordGUI.php');
                    $this->ctrl->forwardCommand(new ilExamOrgaRecordGUI($this));
                    break;
            }
        }
        else {
            switch ($cmd)
            {
                // list all commands that need write permission here
                case "editProperties":
                case "updateProperties":
                case "saveProperties":
                    $this->checkPermission("write");
                    $this->$cmd();
                    break;

                // list all commands that need read permission here
                case "showContent":
                default:
                    $this->checkPermission("read");
                    $this->$cmd();
                    break;
            }
        }
	}

	/**
	 * After object has been created -> jump to this command
	 */
	function getAfterCreationCmd()
	{
		return "editProperties";
	}

	/**
	 * Get standard command
	 */
	function getStandardCmd()
	{
		return "showContent";
	}


	/**
	 * Set tabs
	 */
	function setTabs()
	{
		// tab for the "show content" command
		if ($this->access->checkAccess("read", "", $this->object->getRefId()))
		{
			$this->tabs->addTab("content", $this->txt("content"), $this->ctrl->getLinkTarget($this, "showContent"));
		}

		// standard info screen tab
		$this->addInfoTab();

		// a "properties" tab
		if ($this->access->checkAccess("write", "", $this->object->getRefId()))
		{
			$this->tabs->addTab("properties", $this->txt("properties"), $this->ctrl->getLinkTarget($this, "editProperties"));
		}

		// standard export tab
		// $this->addExportTab();

		// standard permission tab
		$this->addPermissionTab();
		$this->activateTab();
	}

	/**
	 * Edit Properties. This commands uses the form class to display an input form.
	 */
	protected function editProperties()
	{
		$this->tabs->activateTab("properties");
		$form = $this->initPropertiesForm();
		$this->tpl->setContent($form->getHTML());
	}

	/**
	 * @return ilPropertyFormGUI
	 */
	protected function initPropertiesForm() {
		$form = new ilPropertyFormGUI();
		$form->setTitle($this->lng->txt("settings"));

		$title = new ilTextInputGUI($this->plugin->txt("title"), "title");
		$title->setRequired(true);
		$title->setValue($this->object->getTitle());
		$form->addItem($title);

		$description = new ilTextInputGUI($this->plugin->txt("description"), "description");
		$description->setValue($this->object->getDescription());
		$form->addItem($description);

        // items will already have the param values
		$this->object->data->addFormItems($form);

		$form->setFormAction($this->ctrl->getFormAction($this, "saveProperties"));
		$form->addCommandButton("saveProperties", $this->lng->txt("update"));

		return $form;
	}

	/**
	 * Save the Object Properties
	 */
	protected function saveProperties()
    {
		$form = $this->initPropertiesForm();
		$form->setValuesByPost();
		if ($form->checkInput()) {

            $this->object->setTitle($form->getInput('title'));
            $this->object->setDescription($form->getInput('description'));
            $this->object->data->setByForm($form);
			$this->object->update();

			ilUtil::sendSuccess($this->lng->txt("settings_saved"), true);
			$this->ctrl->redirect($this, "editProperties");
		}
		$this->tpl->setContent($form->getHTML());
	}

	protected function showContent()
    {
        //$this->ctrl->redirectByClass('ilexamorgarecordgui');
	}


	/**
	 * We need this method if we can't access the tabs otherwise...
	 */
	private function activateTab() {
		$next_class = $this->ctrl->getCmdClass();

		switch($next_class) {
			case 'ilexportgui':
				$this->tabs->activateTab("export");
				break;
		}

		return;
	}



}