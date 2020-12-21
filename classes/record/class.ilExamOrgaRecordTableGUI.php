<?php
// Copyright (c) 2017 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

/**
 * Class ilExteStatQuestionsOverviewTableGUI
 */
class ilExamOrgaRecordTableGUI extends ilTable2GUI
{
    /** @var ilExamOrgaRecordGUI */
    protected $parent_obj;

    /** @var string $parent_cmd */
    protected $parent_cmd;

    /** @var ilObjExamOrga */
    protected $object;

    /** @var ilExamOrgaPlugin */
    protected $plugin;

    /** @var ilExamOrgaField[] */
    protected $fields;

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
        $this->fields = $this->object->getAvailableFields();

        $this->setId('ilExamOrgaRecordTableGUI');
        $this->setPrefix('ilExamOrgaRecordTableGUI');

        parent::__construct($a_parent_obj, $a_parent_cmd);

        $this->setFormName('exams');
        $this->setTitle($this->plugin->txt('exams'));
        $this->setStyle('table', 'fullwidth');

        // todo: show the checkbox column

        // selected columns
        foreach ($this->getSelectableColumns() as $name => $settings) {
            if ($this->isColumnSelected($name)) {
                $this->addColumn(
                    $settings['txt'],
                    $settings['sortable'] ? $name : '',
                    '',
                    false,
                    '',
                    $settings['tooltip']
                );
            }
        }
        // action column
        $this->addColumn('');

        $this->setTitle($this->plugin->txt('exams'));
        $this->setFormName('exams');
        $this->setFormAction($this->ctrl->getFormAction($a_parent_obj, $a_parent_cmd));

        $this->setStyle('table', 'fullwidth');
        $this->setRowTemplate("tpl.il_xamo_record_row.html", $this->plugin->getDirectory());

        $this->setExternalSorting(true);
        $this->setExternalSegmentation(true);

        $this->setDefaultOrderField("title");
        $this->setDefaultOrderDirection("asc");
        $this->setDisableFilterHiding(true);

        $this->enable('sort');
        $this->enable('header');
        $this->initFilter();
    }

    /**
     * Get selectable columns
     */
    public function getSelectableColumns()
    {
        $columns = [];
        foreach($this->fields as $name => $field) {
            $columns[$name] = [
                'txt' => $field->title,
                'tooltip' => $field->info,
                'default' => $field->default,
                'sortable' => true
            ];
        }
        return $columns;
    }

    /**
     * Initialize the filter controls
     */
    public function initFilter()
    {
        foreach ($this->fields as $name => $field) {
            if ($field->filter) {
                $item = $field->getFilterItem();
                if (isset($item)) {
                    $this->addFilterItem($item, true);
                    $item->readFromSession();
                }
            }
        }
    }


    /**
     * Query for the data to be shown
     * @throws Exception
     */
    public function loadData()
    {
        global $DIC;

        /** @var ilExamOrgaRecord $record */
        $recordList = ilExamOrgaRecord::getCollection();
        $recordList->where(['obj_id' => $this->object->getId()]);

        // limit to owned records
        if (!$this->object->canViewAllRecords()) {
            $recordList->where(['owner_id' => $DIC->user()->getId()]);
        }

        // apply the filter
        foreach ($this->fields as $field) {
            if ($field->filter) {
                $field->setFilterCondition($recordList, $this);
            }
        }

        // paging
        $this->determineOffsetAndOrder();
        $this->determineLimit();
        $this->setMaxCount($recordList->count());
        $recordList->orderBy($this->getOrderField(), $this->getOrderDirection());
        $recordList->limit($this->getOffset(), $this->getLimit());

        // prepare row data (fillRow expects array)
       $data = [];
        foreach ($recordList->get() as $record) {
            $row = [];
            $row['id'] = $record->getValue('id');
            $row['record'] = $record;
            $data[] = $row;
       }
       $this->setData($data);
    }


    /**
	 * Define ordering mode for a field (not neeed, if externally sorted)
     * @param string $a_field
	 * @return boolean  numeric ordering; default is false
	 */
	function numericOrdering($a_field)
	{
	    if (isset($this->fields[$a_field]) && $this->fields[$a_field]->type == ilExamOrgaField::TYPE_INTEGER) {
	        return true;
        }
	    return false;
	}

	/**
	 * fill row
	 * @param array $data
	 */
	public function fillRow($data)
	{
		$id = $data['id'];
		$record = $data['record'];

		// todo: show the checkbox column

		// show the columns
        foreach ($this->getSelectedColumns() as $name)
        {
            $content = '';
            $field = $this->fields[$name];
            if (isset($field)) {
                $content = $field->getListHTML($record);
            }

            $this->tpl->setCurrentBlock('column');
            $this->tpl->setVariable('CONTENT', $content);
            $this->tpl->parseCurrentBlock();
        }

        // show action column
        $list = new ilAdvancedSelectionListGUI();
        $list->setSelectionHeaderClass('small');
        $list->setItemLinkClass('small');
        $list->setId('actl_'.$id.'_'.$this->getId());
        $list->setListTitle($this->lng->txt('actions'));

        // add actions
        $this->ctrl->setParameter($this->parent_obj, 'id', $id);
        $list->addItem($this->plugin->txt('view_details'), '', $this->ctrl->getLinkTarget($this->parent_obj,'viewDetails'));
        if ($this->object->canEditRecord($record)) {
            $list->addItem($this->plugin->txt('edit_record'), '', $this->ctrl->getLinkTarget($this->parent_obj,'editRecord'));
        }

        $this->tpl->setCurrentBlock('column');
        $this->tpl->setVariable('CONTENT', $list->getHtml());
        $this->tpl->parseCurrentBlock();
    }
}