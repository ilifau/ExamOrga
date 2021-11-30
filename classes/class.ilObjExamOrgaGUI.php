<?php

require_once(__DIR__ . "/class.ilExamOrgaPlugin.php");
require_once(__DIR__ . "/class.ilObjExamOrga.php");
require_once(__DIR__ . "/record/class.ilExamOrgaRecord.php");

/**
 * @ilCtrl_isCalledBy ilObjExamOrgaGUI: ilRepositoryGUI, ilAdministrationGUI, ilObjPluginDispatchGUI
 * @ilCtrl_Calls ilObjExamOrgaGUI: ilPermissionGUI, ilInfoScreenGUI, ilObjectCopyGUI, ilCommonActionDispatcherGUI, ilExportGUI
 * @ilCtrl_Calls ilObjExamOrgaGUI: ilExamOrgaRecordGUI, ilExamOrgaConditionGUI, ilExamOrgaMessageGUI
 */
class ilObjExamOrgaGUI extends ilObjectPluginGUI
{
    /** @var ilObjExamOrga */
	public $object;

	/** @var ilExamOrgaPlugin */
	public $plugin;

    /**
     * Extended to go to a specific record
     * @param $a_target
     */
    public static function _goto($a_target)
    {
        global $DIC;

        $ilCtrl = $DIC->ctrl();

        $t = explode("_", $a_target[0]);
        $ref_id = (int) $t[0];
        $record_id = (int) $t[1];

        if (empty($record_id)) {
            parent::_goto($a_target);
        }

        /** @var ilExamOrgaRecord $record */
        $record = ilExamOrgaRecord::findOrGetInstance($record_id);
        $object = new ilObjExamOrga($ref_id);

        $ilCtrl->initBaseClass("ilObjPluginDispatchGUI");
        $ilCtrl->getCallStructure('ilObjPluginDispatchGUI');

        $ilCtrl->setParameterByClass('ilexamorgarecordgui', "ref_id", $ref_id);
        $ilCtrl->setParameterByClass('ilexamorgarecordgui', "id", $record_id);

        if ($object->canEditRecord($record)) {
            $ilCtrl->redirectByClass(["ilobjplugindispatchgui", 'ilobjexamorgagui', 'ilexamorgarecordgui'], "editRecord");
        }
        elseif ($object->canViewRecord($record)) {
            $ilCtrl->redirectByClass(["ilobjplugindispatchgui", 'ilobjexamorgagui', 'ilexamorgarecordgui'], "viewDetails");
        }

        parent::_goto($a_target);
    }

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

            switch ($next_class) {
                case 'ilexamorgaconditiongui':
                    $this->checkPermission('write');
                    $this->tabs->activateTab("conditions");
                    require_once(__DIR__ . '/condition/class.ilExamOrgaConditionGUI.php');
                    $this->ctrl->forwardCommand(new ilExamOrgaConditionGUI($this));
                    break;
            }

            switch ($next_class) {
                case 'ilexamorgamessagegui':
                    $this->checkPermission('write');
                    $this->tabs->activateTab("messages");
                    require_once(__DIR__ . '/message/class.ilExamOrgaMessageGUI.php');
                    $this->ctrl->forwardCommand(new ilExamOrgaMessageGUI($this));
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
            $this->tabs->addTab("conditions", $this->txt("conditions"), $this->ctrl->getLinkTargetByClass('ilexamorgaconditiongui'));
            $this->tabs->addTab("messages", $this->txt("messages"), $this->ctrl->getLinkTargetByClass('ilexamorgamessagegui'));

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
        $this->ctrl->redirectByClass('ilexamorgarecordgui');
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