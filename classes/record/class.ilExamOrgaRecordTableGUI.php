<?php
// Copyright (c) 2017 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

/**
 * Record table
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

    /** @var int[] */
    protected $ids_with_notes = [];

    /** @var string */
    protected $icon_alert;

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
        $this->icon_alert = ilUtil::getImagePath('icon_alert.svg');

        $this->setId('ilExamOrgaRecordTableGUI');
        $this->setPrefix('ilExamOrgaRecordTableGUI');

        parent::__construct($a_parent_obj, $a_parent_cmd);

        $this->addColumn('', '', '1%', true);
        $this->addColumn('');

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
        $this->addColumn($this->lng->txt('actions'));

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

        $this->setSelectAllCheckbox('ids');
        $this->addMultiCommand('confirmDeleteRecords', $this->plugin->txt('delete_records'));
        $this->initFilter();
    }

    /**
     * Get selectable columns
     */
    public function getSelectableColumns()
    {
        $columns = [];
        foreach($this->fields as $name => $field) {
            if ($field->isForList()) {
                $columns[$name] = [
                    'txt' => $field->title,
                    'tooltip' => $field->info,
                    'default' => $field->default,
                    'sortable' => true
                ];
            }
        }
        return $columns;
    }

    /**
     * Initialize the filter controls
     */
    public function initFilter()
    {
        // needed for filter reset with select fields
        $this->filters = [];
        $this->optional_filters = [];

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
            $cond = '(' .$DIC->database()->equals('owner_id', $DIC->user()->getId(), 'integer')
                . ' OR ' .  $DIC->database()->like('admins', 'text', '%'.$DIC->user()->getLogin().'%', false)
                .')';
            $recordList->where($cond);
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
        if (isset($this->fields[$this->getOrderField()])) {
            $recordList->orderBy($this->getOrderField(), $this->getOrderDirection());
        }
        $recordList->limit($this->getOffset(), $this->getLimit());

        // prepare row data (fillRow expects array)
       $data = [];
       $records = $recordList->get();
       foreach ($records as $record) {
            $row = [];
            $row['id'] = $record->id;
            $row['record'] = $record;
            $data[] = $row;
       }
       $this->setData($data);

       // allow fields with external date a bulk load
       foreach ($this->fields as $field) {
           $field->preload($records);
       }

       // get info for alert sign
       require_once (__DIR__ . '/../notes/class.ilExamOrgaNote.php');
       $this->ids_with_notes = ilExamOrgaNote::getRecordIdsWithNotes($records);
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
	    global $DIC;

		$id = $data['id'];
		$record = $data['record'];
        $this->ctrl->setParameter($this->parent_obj, 'id', $id);

		// checkbox
        if ($this->object->canDeleteRecord($record)) {
            $this->tpl->setVariable('ID', $id);
        }

        if ($this->object->canEditRecord($record)) {
            $button = ilLinkButton::getInstance();
            $button->setUrl($this->ctrl->getLinkTarget($this->parent_obj,'editRecord'));
            $button->setCaption('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>', false);
            $this->tpl->setVariable('MAIN_BUTTON', $button->render());
        }
        elseif ($this->object->canViewRecord($record)) {
            $button = ilLinkButton::getInstance();
            $button->setUrl($this->ctrl->getLinkTarget($this->parent_obj,'viewDetails'));
            $button->setCaption('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>', false);
            $this->tpl->setVariable('MAIN_BUTTON', $button->render());
        }

        if (in_array($id, $this->ids_with_notes)) {
            $this->tpl->setVariable('SRC_ALERT', $this->icon_alert);
            $this->tpl->setVariable('ALT_ALERT', $this->plugin->txt('alert_note'));
        }

        // show the columns
        foreach ($this->getSelectedColumns() as $name)
        {
            $content = '';
            $field = $this->fields[$name];
            if (isset($field)) {
                $content = (string) $field->getListHTML($record);
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
        if ($this->object->canEditRecord($record)) {
            $list->addItem($this->plugin->txt('edit_record'), '', $this->ctrl->getLinkTarget($this->parent_obj,'editRecord'));
        }
        elseif ($this->object->canViewRecord($record)) {
            $list->addItem($this->plugin->txt('view_details'), '', $this->ctrl->getLinkTarget($this->parent_obj,'viewDetails'));
        }

        $this->tpl->setCurrentBlock('column');
        $this->tpl->setVariable('CONTENT', $list->getHtml());
        $this->tpl->parseCurrentBlock();
    }
}