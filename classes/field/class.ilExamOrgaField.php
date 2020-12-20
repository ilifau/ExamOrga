<?php

class ilExamOrgaField
{
    const TYPE_INFO = 'info';
    const TYPE_TEXT = 'text';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_INTEGER = 'integer';
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_SELECT = 'select';
    const TYPE_RADIO = 'radio';
    const TYPE_DATE = 'date';
    const TYPE_DATETIME = 'datetime';
    const TYPE_TIMES = 'times';
    const TYPE_USERS = 'users';
    const TYPE_EXAMS = 'exams';
    const TYPE_LINKS = 'links';
    const TYPE_REFERENCE = 'reference';
    const TYPE_USER_ID = 'user_id';
    const TYPE_HEADLINE = 'headline';

    const STATUS_PUBLIC = 'public';     // visible to all users, editable for owner and admins
    const STATUS_HIDDEN = 'hidden';     // hidden for all users
    const STATUS_LOCKED  = 'locked';    // visible to all users, read-only for owner, editable for admins
    const STATUS_FIXED = 'fixed';       // visible to all users, not editable


    /** @var ilExamOrgaPlugin */
    public $plugin;

    /** @var string */
    public $name;

    /** @var string */
    public $type;

    /** @var string */
    public $title;

    /** @var string */
    public $info;

    /** @var array */
    public $options;

    /** @var int */
    public $size;

    /** @var int */
    public $limit;

    /** @var bool */
    public $multi;

    /** @var bool */
    public $required;

    /** @var bool */
    public $filter;

    /** @var bool */
    public $efault;

    /** @var string */
    public $status;

    /**
     * Get a new field object according to the definition
     * @param ilExamOrgaPlugin $plugin
     * @param array $definition
     * @return self
     */
    public static function factory($plugin, $definition) {
        switch ($definition['type']) {


            default:
                return new self($plugin, $definition);
        }
    }

    /**
     * ilExamOrgaField constructor.
     * @param ilExamOrgaPlugin $plugin
     * @param array $definition
     */
    public function __construct($plugin, $definition)
    {
        $this->plugin = $plugin;

        $this->name = (string) $definition['name'];
        $this->type =  (string) $definition['type'];
        $this->size = (int) $definition['size'];
        $this->limit = (int) $definition['limit'];
        $this->required = (bool) $definition['required'];
        $this->multi = (bool) $definition['required'];
        $this->filter = (bool) $definition['filter'];
        $this->default = (bool) $definition['default'];

        if (isset($definition['title'])) {
            $this->title =  $definition['title'];
        }
        else {
            $this->title =  $this->plugin->txt('field_' . $this->name);
        }

        if (isset($definition['info'])) {
            $this->info =  $definition['info'];
        }
        elseif ($this->plugin->txt('field_' . $this->name . '_info') != 'field_' . $this->name . '_info') {
            $this->info = $this->plugin->txt('field_' . $this->name);
        }

        foreach ((array) $definition['options'] as $key => $option) {
            if (is_int($key)) {
                $key = (string) $option;
            }
            $this->options[$key] = $this->plugin->txt($option);
        }

        if (isset($definition['status'])) {
            $this->status = $definition['status'];
        }
        else {
            $this->status = self::STATUS_PUBLIC;
        }
    }

    /**
     * Get the raw value from the record
     * @param ilExamOrgaRecord $record
     * @return mixed
     */
    public function getValue($record) {
        return $record->getValue($this->name);
    }

    /**
     * Get the raw value from the record
     * @param ilExamOrgaRecord $record
     * @return mixed
     */
    public function setValue($record, $value) {
       $record->setValue($this->name, $value);
    }


    /**
     * Get the HTML code for display in a table
     * @param ilExamOrgaRecord $record
     * @return string
     */
    public function getListHTML($record) {
        return ilUtil::stripSlashes((string) $this->getValue($record));
    }

    /**
     * Get the HTML code for display in a table
     * @param ilExamOrgaRecord $record
     * @return string
     */
    public function getDetailsHTML($record) {
        return ilUtil::stripSlashes((string) $this->getValue($record));
    }


    /**
     * Get the form item with the value from a record
     * @param ilExamOrgaRecord $record
     * @param ilPropertyFormGUI $form
     */
    public function getFormItem($record, $form) {
        $item = new ilTextInputGUI($this->title, $this->getPostvar());
        $item->setRequired($this->required);
        if ($this->status == self::STATUS_FIXED || $this->status == self::STATUS_LOCKED) {
            $item->setDisabled(true);
        }
        if (isset($this->size)) {
            $item->setSuffix($this->size);
        }
        if (isset($this->limit)) {
            $item->setMaxLength($this->limit);
        }
        $item->setValue($this->getValue());

        return $item;
    }


    /**
     * Set the value of the record by form input
     * @param ilExamOrgaRecord $record
     * @param ilPropertyFormGUI $form
     */
    public function setByForm($record, $form) {
        $value = $form->getInput($this->getPostvar());
        $this->setValue($record, $value);
    }

    /**
     * Get the item for a record filter
     * @return ilFormPropertyGUI|null
     * @see \ilTable2GUI::addFilterItemByMetaType
     */
    public function getFilterItem() {
        $item = new ilTextInputGUI($this->title, $this->getPostvar());
        $item->setMaxLength(64);
        $item->setSize(20);
        return $item;
    }

    /**
     * Set the query condition for a table filter
     * @param ActiveRecordList $list
     * @param ilTable2GUI      $table
     * @throws Exception
     */
    public function setFilterCondition($list, $table) {
        /** @var ilTextInputGUI $item */
        $item = $table->getFilterItemByPostVar($this->getPostvar());

        if (isset($item) && !empty($item->getValue())) {
            $list->where([$this->name => $item->getValue() . '%'], 'LIKE');
        }
    }


    /**
     * Get the data for the external REST API
     */
    public function getApiData($record) {
        return ilUtil::stripSlashes($this->getValue($record));
    }

    /**
     * Write the value to an excel sheet
     * @param ilExamOrgaRecord $record
     * @param ilExcel $excel
     * @param integer $row
     * @param integer $col
     */
    public function WriteToExcel($record, $excel, $row, $com) {
    }

    /**
     * ReadFrom the value from an excel sheet
     * @param ilExamOrgaRecord $record
     * @param ilExcel $excel
     * @param integer $row
     * @param integer $col
     */
    public function ReadFromExcel($record, $excel, $row, $com) {
    }


    /**
     * Get the post variable
     * @return string
     */
    protected function getPostvar() {
        return 'field_' . $this->name;
    }


}
