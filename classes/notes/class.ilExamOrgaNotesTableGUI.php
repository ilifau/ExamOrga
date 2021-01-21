<?php
// Copyright (c) 2017 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

/**
 * Notes Table
 */
class ilExamOrgaNotesTableGUI extends ilTable2GUI
{
    /** @var ilExamOrgaRecordGUI */
    protected $parent_obj;

    /** @var string $parent_cmd */
    protected $parent_cmd;

    /** @var ilObjExamOrga */
    protected $object;

    /** @var ilExamOrgaPlugin */
    protected $plugin;

    /** ilExamOrgaRecord */
    protected $record;

    /**
     * Constructor
     * @param ilExamOrgaRecordGUI $a_parent_obj
     * @param string $a_parent_cmd
     */
    public function __construct($a_parent_obj, $a_parent_cmd)
    {
        global $DIC;

        $this->lng = $DIC->language();
        $this->ctrl = $DIC->ctrl();
        $this->parent_obj = $a_parent_obj;
        $this->parent_cmd = $a_parent_cmd;
        $this->object = $a_parent_obj->object;
        $this->plugin = $a_parent_obj->plugin;

        $this->setId('ilExamOrgaNotesTableGUI');
        $this->setPrefix('ilExamOrgaNotesTableGUI');

        parent::__construct($a_parent_obj, $a_parent_cmd);

        $this->addColumn($this->plugin->txt('created_at'), 'created_at');
        $this->addColumn($this->plugin->txt('code'), 'code');
        $this->addColumn($this->plugin->txt('note'), 'note');
        $this->addColumn($this->lng->txt('actions'));

        $this->setTitle($this->plugin->txt('notes'));
        $this->setFormName('notes');
        $this->setFormAction($this->ctrl->getFormAction($a_parent_obj, $a_parent_cmd));

        $this->setStyle('table', 'fullwidth');
        $this->setRowTemplate("tpl.il_xamo_note_row.html", $this->plugin->getDirectory());

        $this->setDefaultOrderField("created_at");
        $this->setDefaultOrderDirection("asc");

        $this->disable('sort');
        //$this->disable('header');
    }


    /**
     * Query for the data to be shown
     * @param ilExamOrgaRecord $record
     * @throws Exception
     */
    public function loadData($record)
    {
       $this->record = $record;

        /** @var ilExamOrgaNote[] $notes */
        $notes = ilExamOrgaNote::where(['record_id' => $record->id])->get();

        // prepare row data (fillRow expects array)
       $data = [];
        foreach ($notes as $note) {
            $row = [];
            $row['id'] = $note->id;
            $row['note'] = $note;
            $data[] = $row;
       }
       $this->setData($data);
    }


	/**
	 * fill row
	 * @param array $data
	 */
	public function fillRow($data)
	{
		$id = $data['id'];

		/** @var ilExamOrgaNote $note */
		$note = $data['note'];
		$this->tpl->setVariable('CREATED_AT', ilDatePresentation::formatDate(new ilDateTime($note->created_at, IL_CAL_UNIX)));
        $this->tpl->setVariable('CODE', $note->code);
        $this->tpl->setVariable('NOTE', $note->note);

        // show action column
        $list = new ilAdvancedSelectionListGUI();
        $list->setSelectionHeaderClass('small');
        $list->setItemLinkClass('small');
        $list->setId('actl_'.$id.'_'.$this->getId());
        $list->setListTitle($this->lng->txt('actions'));

        // add actions
        if ($this->object->canEditRecord($this->record)) {
            $this->ctrl->setParameter($this->parent_obj, 'note_id', $note->id);
            $list->addItem($this->plugin->txt('delete_note'), '', $this->ctrl->getLinkTarget($this->parent_obj,'deleteNote'));
        }
        $this->tpl->setVariable('ACTIONS', $list->getHtml());
    }
}