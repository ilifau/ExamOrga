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

    /**
     * Get a textual representation of the links
     * @param self[]|null $notes
     * @return string
     */
    public static function getRecordNotesText($record_id, $notes = null)
    {
        if (!isset($notes)) {
            $notes = self::where(['record_id' => $record_id])->orderBy('created_at')->get();
        }

        $entries = [];
        foreach ($notes as $note) {
            $entries[] = $note->code . "\t" . $note->note;
        }

        return implode("\n", $entries);
    }

    /**
     * Get the ids if records with notes
     * @param ilExamOrgaRecord[] $records
     * @return array
     */
    public static function getRecordIdsWithNotes($records)
    {
        global $DIC;
        $db = $DIC->database();

        $ids = [];
        foreach ($records as $record) {
            $ids[] = $record->id;
        }
        $query = "SELECT DISTINCT record_id FROM xamo_note WHERE "
            . $db->in('record_id', $ids, false, 'integer');
        $result = $db->query($query);

        $record_ids = [];
        while($row = $db->fetchAssoc($result)) {
            $record_ids[] = $row['record_id'];
        }
        return $record_ids;
    }

}
