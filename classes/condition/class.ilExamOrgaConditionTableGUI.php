<?php
// Copyright (c) 2021 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

/**
 * Record table
 */
class ilExamOrgaConditionTableGUI extends ilTable2GUI
{
    /** @var ilExamOrgaConditionGUI */
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
     * @param ilExamOrgaConditionGUI $a_parent_obj
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
        $this->fields = $this->object->getConditionFields();

        $this->setId('ilExamOrgaConditionTableGUI');
        $this->setPrefix('ilExamOrgaConditionTableGUI');

        parent::__construct($a_parent_obj, $a_parent_cmd);

        $this->addColumn('', '', '1%', true);

        // selected columns
        foreach ($this->getColumns() as $name => $settings) {
            $this->addColumn(
                $settings['txt'],
                $settings['sortable'] ? $name : '',
                '',
                false,
                '',
                $settings['tooltip']
            );
        }
        // action column
        $this->addColumn($this->lng->txt('actions'));

        $this->setTitle($this->plugin->txt('conditions'));
        $this->setFormName('conditions');
        $this->setFormAction($this->ctrl->getFormAction($a_parent_obj, $a_parent_cmd));

        $this->setStyle('table', 'fullwidth');
        $this->setRowTemplate("tpl.il_xamo_condition_row.html", $this->plugin->getDirectory());

        $this->setExternalSorting(true);
        $this->setExternalSegmentation(true);

        $this->disable('sort');
        $this->enable('header');

        $this->setSelectAllCheckbox('ids');
        $this->addMultiCommand('confirmDeleteConditions', $this->plugin->txt('delete_conditions'));
    }

    /**
     * Get the columns
     */
    public function getColumns()
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
     * Query for the data to be shown
     * @throws Exception
     */
    public function loadData()
    {
        /** @var ilExamOrgaCondition $condition */
        $conditionsList = ilExamOrgaCondition::getCollection();
        $conditionsList->where(['obj_id' => $this->object->getId()]);

        // paging
        $this->determineOffsetAndOrder();
        $this->determineLimit();
        $this->setMaxCount($conditionsList->count());
        if (isset($this->fields[$this->getOrderField()])) {
            $conditionsList->orderBy($this->getOrderField(), $this->getOrderDirection());
        }
        $conditionsList->limit($this->getOffset(), $this->getLimit());

        // prepare row data (fillRow expects array)
       $data = [];
       $conditions = $conditionsList->get();
       foreach ($conditions as $condition) {
            $row = [];
            $row['id'] = $condition->id;
            $row['condition'] = $condition;
            $data[] = $row;
       }
       $this->setData($data);
    }


    /**
	 * Define ordering mode for a field (not needed, if externally sorted)
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
		$condition = $data['condition'];
        $this->ctrl->setParameter($this->parent_obj, 'id', $id);

        // checkbox
        $this->tpl->setVariable('ID', $id);

        // show the columns
        foreach ($this->getColumns() as $name => $column)
        {
            $content = '';
            $field = $this->fields[$name];
            if (isset($field)) {
                $content = (string) $field->getListHTML($condition);
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
        $list->addItem($this->plugin->txt('edit_condition'), '', $this->ctrl->getLinkTarget($this->parent_obj,'editCondition'));

        $this->tpl->setCurrentBlock('column');
        $this->tpl->setVariable('CONTENT', $list->getHtml());
        $this->tpl->parseCurrentBlock();
    }
}