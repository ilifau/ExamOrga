<?php

/**
 * Base Representation of an Exam note
 */
class ilExamOrgaNote extends ActiveRecord
{
    /**
     * @return string
     * @description Return the Name of your Database Table
     */
    public static function returnDbTableName()
    {
        return 'xamo_note';
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
    public $record_id;


    /**
     * @var integer
     * @con_has_field        true
     * @con_is_notnull       false
     * @con_fieldtype        integer
     * @con_length           4
     */
    public $code;


    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           4000
     */
    public $note;


    /**
     * @var string
     * @con_has_field        true
     * @con_is_notnull       true
     * @con_fieldtype        integer
     * @con_length           4
     */
    public $created_at;


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
     * Check if record is not yet saved
     * @return bool
     */
    public function isNew()
    {
        return empty($this->id);
    }


    /**
     * Set creation info and create record
     */
    public function create()
    {
        $time = time();
        if (empty($this->created_at)) {
            $this->created_at = $time;
        }

        parent::create();
    }
}
