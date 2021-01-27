<?php

/**
 * Base Representation of an Exam link
 */
class ilExamOrgaLink extends ActiveRecord
{
    /**
     * @return string
     * @description Return the Name of your Database Table
     */
    public static function returnDbTableName()
    {
        return 'xamo_link';
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
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           10
     */
    public $exam_run;


    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           100
     */
    public $link;


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

    /**
     * Get a textual representation of the links
     */
    public static function getRecordLinksText($record_id)
    {
        /** @var self[] $links */
        $links = self::where(['record_id' => $record_id])->orderBy('exam_run')->get();

        $entries = [];
        foreach ($links as $link) {
            $entries[] = $link->exam_run . "\t" . $link->link;
        }

        return implode("\n", $entries);
    }

    /**
     * Get a textual representation of the links
     */
    public static function getRecordLinksHtml($record_id)
    {
        /** @var self[] $links */
        $links = self::where(['record_id' => $record_id])->orderBy('exam_run')->get();

        $entries = [];
        foreach ($links as $link) {
            $entries[] = $link->exam_run . ' &nbsp; <a target="_blank" href="' . $link->link . '">' . $link->link . '</a>';
        }

        return implode("<br />", $entries);
    }

}
