<?php
// Copyright (c) 2021 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

/**
 * Record table
 */
class ilExamOrgaMessageTableGUI extends ilTable2GUI
{
    /** @var ilExamOrgaMessageGUI */
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
     * @param ilExamOrgaMessageGUI $a_parent_obj
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
        $this->fields = $this->object->getMessageFields();

        $this->setId('ilExamOrgaMessageTableGUI');
        $this->setPrefix('ilExamOrgaMessageTableGUI');

        parent::__construct($a_parent_obj, $a_parent_cmd);

        $this->addColumn('');

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

        $this->setTitle($this->plugin->txt('messages'));
        $this->setDescription(nl2br($this->plugin->txt('messages_description')));
        $this->setFormName('messages');
        $this->setFormAction($this->ctrl->getFormAction($a_parent_obj, $a_parent_cmd));

        $this->setStyle('table', 'fullwidth');
        $this->setRowTemplate("tpl.il_xamo_message_row.html", $this->plugin->getDirectory());

        $this->disable('sort');
        $this->enable('header');
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
        $messages = ilExamOrgaMessage::getForObject($this->object->getId());

        // prepare row data (fillRow expects array)
       $data = [];
       foreach ($messages as $type => $message) {
            $row = [];
            $row['message_object'] = $message;
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
        /** @var ilExamOrgaMessage $message */
		$message = $data['message_object'];
        $type = $message->message_type;
        $this->ctrl->setParameter($this->parent_obj, 'type', $type);

        $button = ilLinkButton::getInstance();
        $button->setUrl($this->ctrl->getLinkTarget($this->parent_obj,'editMessage'));
        $button->setCaption('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>', false);
        $this->tpl->setVariable('MAIN_BUTTON', $button->render());


        // show the columns
        foreach ($this->getColumns() as $name => $column)
        {
            $content = '';
            $field = $this->fields[$name];
            if (isset($field)) {
                $content = (string) $field->getListHTML($message);
            }

            $this->tpl->setCurrentBlock('column');
            $this->tpl->setVariable('CONTENT', $content);
            $this->tpl->parseCurrentBlock();
        }
    }
}