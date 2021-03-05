<?php
// Copyright (c) 2020 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

/**
 * ExamOrga configuration user interface class
 *
 * @ilCtrl_Calls: ilExamOrgaConfigGUI: ilPropertyFormGUI
 *
 * @author Fred Neumann <fred.neumann@fau.de>
 */
class ilExamOrgaConfigGUI extends ilPluginConfigGUI
{
	/** @var ilExamOrgaPlugin $plugin */
	protected $plugin;

	/** @var ilExamOrgaConfig $config */
	protected $config;

	/** @var ilTabsGUI $tabs */
    protected $tabs;

    /** @var ilCtrl $ctrl */
    protected $ctrl;

    /** @var ilLanguage $lng */
	protected $lng;

    /** @var ilTemplate $lng */
	protected $tpl;

    /** @var  ilToolbarGUI $toolbar */
    protected $toolbar;

    /**
	 * Handles all commands, default is "configure"
     * @throws Exception
	 */
	public function performCommand($cmd)
	{
        global $DIC;

        // this can't be in constructor
        $this->plugin = $this->getPluginObject();
        $this->config = $this->plugin->getConfig();
        $this->lng = $DIC->language();
        $this->tabs = $DIC->tabs();
        $this->ctrl = $DIC->ctrl();
        $this->tpl = $DIC->ui()->mainTemplate();
        $this->toolbar = $DIC->toolbar();


        $this->tabs->addTab('basic', $this->plugin->txt('basic_configuration'), $this->ctrl->getLinkTarget($this, 'configure'));
        $this->setToolbar();

        switch ($DIC->ctrl()->getNextClass())
        {
            case 'ilpropertyformgui':
                switch ($_GET['config'])
                {
                    case 'basic':
                        $DIC->ctrl()->forwardCommand($this->initBasicConfigurationForm());
                        break;
                }

                break;

            default:
                switch ($cmd)
                {
                    case "configure":
                    case "saveBasicSettings":
                    case "updateLanguages":
                    case "loadCampusExams":
                    case "generateDBUpdate":
                        $this->tabs->activateTab('basic');
                        $this->$cmd();
                        break;
                }
        }
	}

    /**
     * Set the toolbar
     */
    protected function setToolbar()
    {
        $this->toolbar->setFormAction($this->ctrl->getFormAction($this));

        $button = ilLinkButton::getInstance();
        $button->setUrl($this->ctrl->getLinkTarget($this, 'updateLanguages'));
        $button->setCaption($this->plugin->txt('update_languages'), false);
        $this->toolbar->addButtonInstance($button);

        $button = ilLinkButton::getInstance();
        $button->setUrl($this->ctrl->getLinkTarget($this, 'loadCampusExams'));
        $button->setCaption($this->plugin->txt('load_campus_exams'), false);
        $this->toolbar->addButtonInstance($button);

        $button = ilLinkButton::getInstance();
        $button->setUrl($this->ctrl->getLinkTarget($this, 'generateDBUpdate'));
        $button->setCaption($this->plugin->txt('generate_db_update'), false);
        $this->toolbar->addButtonInstance($button);

    }

    /**
	 * Show base configuration screen
	 */
	protected function configure()
	{
		$form = $this->initBasicConfigurationForm();
		$this->tpl->setContent($form->getHTML());
	}

    /**
     * Update Languages
     */
    protected function updateLanguages()
    {
        $this->plugin->updateLanguages();
        $this->ctrl->redirect($this, 'configure');
    }

    /**
     * Generate the db update steps for active record
     */
    protected function loadCampusExams()
    {
        require_once (__DIR__ . '/campus/class.ilExamOrgaCampusExam.php');
        ilExamOrgaCampusExam::updateExams($this->plugin);
        $this->ctrl->redirect($this, 'configure');
    }


    /**
     * Generate the db update steps for active record
     */
	protected function generateDBUpdate()
    {
        require_once (__DIR__ . '/condition/class.ilExamOrgaCondition.php');
        $arBuilder = new arBuilder(new ilExamOrgaCondition());
        $arBuilder->generateDBUpdateForInstallation();
    }

    /**
	 * Initialize the configuration form
	 * @return ilPropertyFormGUI form object
	 */
	protected function initBasicConfigurationForm()
	{
		$form = new ilPropertyFormGUI();
        $form->setTitle($this->plugin->txt('config_base'));
        $form->setDescription($this->plugin->txt('config_base_info'));
		$form->setFormAction($this->ctrl->getFormAction($this));

        foreach($this->config->getParams() as $param)
        {
            $param->setValue($this->config->get($param->name));
            $form->addItem($param->getFormItem());
        }

		$form->addCommandButton("saveBasicSettings", $this->lng->txt("save"));
		return $form;
	}

	/**
	 * Save the basic settings
	 */
	protected function saveBasicSettings()
	{
		$form = $this->initBasicConfigurationForm();
		if ($form->checkInput())
		{
		    foreach ($this->config->getParams() as $param)
            {
                $this->config->set($param->name, $form->getInput($param->getPostvar()));
            }
            $this->config->write();

			ilUtil::sendSuccess($this->lng->txt("settings_saved"), true);
			$this->ctrl->redirect($this, 'configure');
		}
		else
		{
			$form->setValuesByPost();
			$this->tpl->setContent($form->getHtml());
		}
	}
}