<?php

/**
 * Message text template
 */
class ilExamOrgaMessage extends ActiveRecord implements ilExamOrgaFieldValues
{
    CONST TYPE_CONFIRM_PRESENCE = 'confirm_presence';
    CONST TYPE_CONFIRM_REMOTE = 'confirm_remote';
    CONST TYPE_REMINDER1_PRESENCE = 'reminder1_presence';
    CONST TYPE_REMINDER1_REMOTE = 'reminder1_remote';
    CONST TYPE_REMINDER2_PRESENCE = 'reminder2_presence';
    CONST TYPE_REMINDER2_REMOTE = 'reminder2_remote';
    CONST TYPE_WARNING_ZOOM = 'warning_zoom';
    CONST TYPE_WARNING_CAMPUS = 'warning_campus';
    CONST TYPE_WARNING_ROLES = 'warning_roles';
    CONST TYPE_WARNING_SCHEDULE = 'warning_schedule';

    /**
     * Get the defined types
     * @return string[]
     */
    public static function getTypes() {
        return [
            self::TYPE_CONFIRM_PRESENCE,
            self::TYPE_CONFIRM_REMOTE,
            self::TYPE_REMINDER1_PRESENCE,
            self::TYPE_REMINDER1_REMOTE,
            self::TYPE_REMINDER2_PRESENCE,
            self::TYPE_REMINDER2_REMOTE,
            self::TYPE_WARNING_CAMPUS,
            self::TYPE_WARNING_ROLES,
            self::TYPE_WARNING_SCHEDULE,
            self::TYPE_WARNING_ZOOM
        ];
    }


    /**
     * @return string
     * @description Return the Name of your Database Table
     */
    public static function returnDbTableName()
    {
        return 'xamo_message';
    }


    /**
     * @var integer
     * @con_has_field        true
     * @con_is_primary       true
     * @con_sequence         true
     * @con_is_notnull       true
     * @con_fieldtype        integer
     * @con_length           4
     */
    public $id;


    /**
     * @var integer
     * @con_has_field        true
     * @con_is_notnull       true
     * @con_fieldtype        integer
     * @con_length           4
     */
    public $obj_id;

    /**
     * @var string
     * @con_has_field        true
     * @con_is_notnull       false
     * @con_fieldtype        text
     * @con_length           20
     */
    public $message_type;


    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           250
     */
    public $subject;

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           4000
     */
    public $content;


    /**
     * @var bool
     * @con_has_field        true
     * @con_is_notnull       true
     * @con_fieldtype        integer
     * @con_length           4
     */
    public $active;

    /**
     * Get the id if the record
     */
    public function getId() {
        return $this->id;
    }


    /**
     * Get the value of a property
     * @param string $name
     * @return mixed
     */
    public function getValue($name) {
        if (property_exists($this, $name)) {
            return $this->{$name};
        }
    }

    /**
     * Set the value of a property
     * @param $name
     * @param $value
     */
    public function setValue($name, $value) {
        if (property_exists($this, $name)) {
            $this->{$name} = $value;
        }
    }

    /**
     * Get an existing or new message for an object and type
     * @param int $obj_id
     * @param string $type
     * @return self
     */
    public static function getByType($obj_id, $type)
    {
        $message = self::where(['obj_id' => $obj_id, 'message_type' => $type])->first();
        if (!isset($message)) {
            $message = new self();
            $message->obj_id = (int) $obj_id;
            $message->message_type = (string) $type;
        }
        return $message;
    }

    /**
     * Get all messages for an object
     * @param $obj_id
     * @return self[] (indexed by type)
     */
    public static function getForObject($obj_id)
    {
        $messages = [];

        // default objects
        foreach(self::getTypes() as $type) {
            $message = new self;
            $message->obj_id = $obj_id;
            $message->message_type = $type;
            $messages[$type] = $message;
        }

        // find the stored messages
        /** @var self $message */
        foreach (self::where(['obj_id' => $obj_id])->get() as $message) {
            if (in_array($message->message_type, self::getTypes())) {
            $messages[$message->message_type] = $message;
            }
        }

        return $messages;
    }
}
