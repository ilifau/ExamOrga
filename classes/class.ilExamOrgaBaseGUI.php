<?php
/**
 * Base class for combined subscription GUI classes (except tables)
 */
abstract class ilExamOrgaBaseGUI
{
	/** @var ilObjExamOrgaGUI */
	public $parent;

	/** @var  ilObjExamOrga */
	public $object;

	/** @var  ilExamOrgaPlugin */
	public $plugin;

	/** @var  ilCtrl */
	public $ctrl;

	/** @var  ilTabsGUI */
	public $tabs;

	/** @var ilTemplate */
	public $tpl;

	/** @var ilLanguage */
	public $lng;

	/** @var ilToolbarGUI */
	protected $toolbar;

	/**
	 * Constructor
	 * @param ilObjExamOrgaGUI     $a_parent_gui
	 */
	public function __construct($a_parent_gui)
	{
		global $DIC;

		$this->parent = $a_parent_gui;
		$this->object = $this->parent->object;
		$this->plugin = $this->parent->plugin;
		$this->ctrl = $DIC->ctrl();
		$this->tabs = $DIC->tabs();
		$this->toolbar = $DIC->toolbar();
        $this->lng = $DIC->language();
		$this->tpl = $DIC->ui()->mainTemplate();
	}

	/**
	 * Execute a command
	 * This should be overridden in the child classes
	 * note: permissions are already checked in parent gui
	 *
	 */
	public function executeCommand()
	{
		$cmd = $this->ctrl->getCmd('xxx');
		switch ($cmd)
		{
			case 'yyy':
			case 'zzz':
				$this->$cmd();
				return;

			default:
				// show unknown command
                $this->tpl->setContent('unknown command: ' . $cmd);
				return;
		}
	}

	/**
	 * Design a text as page info below toolbar
	 * @param $text
	 * @return string
	 */
	public function pageInfo($text)
	{
		return '<p class="small">'.$text.'</p><br />';
	}

	/**
	 * render a text as messageDetails
	 * @param $text
	 * @return string
	 */
	public function messageDetails($text)
	{
		return '<p class="small">'.$text.'</p>';
	}




}