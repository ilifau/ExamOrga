<?php

class ilExamOrgaField
{
    const TYPE_TEXT = 'text';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_LINK = 'link';
    const TYPE_INTEGER = 'integer';
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_SELECT = 'select';
    const TYPE_MULTISELECT = 'multiselect';
    const TYPE_RADIO = 'radio';
    const TYPE_DATE = 'date';
    const TYPE_TIMESTAMP = 'timestamp';
    const TYPE_HEADLINE = 'headline';
    const TYPE_USER_ID = 'user_id';
    const TYPE_REFERENCE = 'reference';
    const TYPE_TIMES = 'times';
    const TYPE_LOGINS = 'logins';
    const TYPE_EXAMS = 'exams';
    const TYPE_RUN_LINKS = 'run_links';

    const STATUS_PUBLIC = 'public';     // visible to all users, editable for owner and admins
    const STATUS_LOCKED  = 'locked';    // visible to all users, read-only for owner, editable for admins
    const STATUS_FIXED = 'fixed';       // visible to all users, not editable
    const STATUS_HIDDEN = 'hidden';     // hidden for all users, editable for admins


    /** @var ilObjExamOrga */
    public $object;

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
    public $default;

    /** @var string */
    public $status;

    /**
     * Get a new field object according to the definition
     * @param ilObjExamOrga $object
     * @param array $definition
     * @return self
     */
    public static function factory($object, $definition) {
        switch ($definition['type']) {

            case self::TYPE_TEXT:
                require_once (__DIR__ . '/class.ilExamOrgaTextField.php');
                return new ilExamOrgaTextField($object, $definition);

            case self::TYPE_TEXTAREA:
                require_once (__DIR__ . '/class.ilExamOrgaTextareaField.php');
                return new ilExamOrgaTextareaField($object, $definition);

            case self::TYPE_LINK:
                require_once (__DIR__ . '/class.ilExamOrgaLinkField.php');
                return new ilExamOrgaLinkField($object, $definition);

            case self::TYPE_INTEGER:
                require_once (__DIR__ . '/class.ilExamOrgaIntegerField.php');
                return new ilExamOrgaIntegerField($object, $definition);

            case self::TYPE_CHECKBOX:
                require_once (__DIR__ . '/class.ilExamOrgaCheckboxField.php');
                return new ilExamOrgaCheckboxField($object, $definition);

            case self::TYPE_SELECT:
                require_once (__DIR__ . '/class.ilExamOrgaSelectField.php');
                return new ilExamOrgaSelectField($object, $definition);

            case self::TYPE_MULTISELECT:
                require_once (__DIR__ . '/class.ilExamOrgaMultiselectField.php');
                return new ilExamOrgaMultiselectField($object, $definition);

            case self::TYPE_RADIO:
                require_once (__DIR__ . '/class.ilExamOrgaRadioField.php');
                return new ilExamOrgaRadioField($object, $definition);

            case self::TYPE_DATE:
                require_once (__DIR__ . '/class.ilExamOrgaDateField.php');
                return new ilExamOrgaDateField($object, $definition);

            case self::TYPE_TIMESTAMP:
                require_once (__DIR__ . '/class.ilExamOrgaTimestampField.php');
                return new ilExamOrgaTimestampField($object, $definition);

            case self::TYPE_HEADLINE:
                require_once (__DIR__ . '/class.ilExamOrgaHeadlineField.php');
                return new ilExamOrgaHeadlineField($object, $definition);

            case self::TYPE_REFERENCE:
                require_once (__DIR__ . '/class.ilExamOrgaReferenceField.php');
                return new ilExamOrgaReferenceField($object, $definition);

            case self::TYPE_TIMES:
                require_once (__DIR__ . '/class.ilExamOrgaTimesField.php');
                return new ilExamOrgaTimesField($object, $definition);

            case self::TYPE_LOGINS:
                require_once (__DIR__ . '/class.ilExamOrgaLoginsField.php');
                return new ilExamOrgaLoginsField($object, $definition);

            case self::TYPE_USER_ID:
                require_once (__DIR__ . '/class.ilExamOrgaUserIdField.php');
                return new ilExamOrgaUserIdField($object, $definition);

            case self::TYPE_EXAMS:
                require_once (__DIR__ . '/class.ilExamOrgaExamsField.php');
                return new ilExamOrgaExamsField($object, $definition);

            case self::TYPE_RUN_LINKS:
                require_once (__DIR__ . '/class.ilExamOrgaRunLinksField.php');
                return new ilExamOrgaRunLinksField($object, $definition);

            default:
                return new self($object, $definition);
        }
    }

    /**
     * ilExamOrgaField constructor.
     * @param ilObjExamOrga $object
     * @param array $definition
     */
    public function __construct($object, $definition)
    {
        $this->object = $object;
        $this->plugin = $object->plugin;

        $this->name = (string) $definition['name'];
        $this->type =  (string) $definition['type'];
        $this->size = (int) $definition['size'];
        $this->limit = (int) $definition['limit'];
        $this->required = (bool) $definition['required'];
        $this->multi = (bool) $definition['multi'];
        $this->filter = (bool) $definition['filter'];
        $this->default = (bool) $definition['default'];

        // title
        if (isset($definition['title'])) {
            $this->title = $definition['title'];
        }
        else {
            $this->title =  $this->plugin->txt('field_' . $this->name);
        }

        // optional info line
        if (isset($definition['info'])) {
            $this->info =  $definition['info'];
        }
        elseif ($this->plugin->txt('field_' . $this->name . '_info') != 'field_' . $this->name . '_info') {
            $this->info = $this->plugin->txt('field_' . $this->name . '_info');
        }

        // select or radio options
        foreach ((array) $definition['options'] as $key => $option) {
            if (is_int($key)) {
                $key = (string) $option;
            }
            $this->options[$key] = $this->plugin->txt($option);
        }

        // status
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
      * Build the form item with the value from a record
      * @param ilExamOrgaRecord $record
      * @return ilFormPropertyGUI
      */
    public function getFormItem($record)
    {
        $item = new ilTextInputGUI($this->title, $this->getPostvar());
        $item->setRequired($this->required);
        $item->setDisabled(!$this->object->canEditField($this));

        if (isset($this->info)) {
            $item->setInfo($this->info);
        }
        if (isset($this->size)) {
            $item->setSize($this->size);
        }
        if (isset($this->limit)) {
            $item->setMaxLength($this->limit);
        }

        $item->setValue($this->getValue($record));
        return $item;
    }

    /**
     * Set the value of the record by form input
     * @param ilExamOrgaRecord $record
     * @param ilPropertyFormGUI $form
     */
    public function setByForm($record, $form)
    {
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
            $list->where([$this->name => $item->getValue()]);
        }
    }


    /**
     * Get the value for an excel sheet
     * @param ilExamOrgaRecord $record
     * @param ilExcel $excel
     * @return mixed
     */
    public function getExcelValue($record, $excel) {
        return $this->getValue($record);
    }

    /**
     * Set the value from an excel sheet
     * @param ilExamOrgaRecord $record
     * @param ilExcel $excel
     * @param mixed $value
     */
    public function setExcelValue($record, $excel, $value) {
        if ($this->required && !isset($value)) {
            return false;
        }
        $this->setValue($record, $value);
        return true;
    }


    /**
     * Get the post variable
     * @return string
     */
    final protected function getPostvar() {
        return 'field_' . $this->name;
    }

    /**
     * Check if the field can be used in a list of records
     * @return bool
     */
    public function isForList() {
        return true;
    }

    /**
     * Check if the field can be used in a details view
     * @return bool
     */
    public function isForDetails() {
        return true;
    }

    /**
     * Check if the field can be used in a form
     * @return bool
     */
    public function isForForm() {
        return true;
    }

    /**
     * Check if the field can be used in excel export and import
     * @return bool
     */
    public function isForExcel() {
        return true;
    }
}
