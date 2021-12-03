<?php

/**
 * Base Representation of an Exam note
 */
class ilExamOrgaNote extends ActiveRecord
{
    CONST TYPE_ZOOM = 'zoom';               // notes from the zoom service
    CONST TYPE_CAMPUS = 'campus';           // notes regarding campus connection
    CONST TYPE_ROLES = 'roles';             // notes regarding the roles
    CONST TYPE_CONDITION = 'condition';     // warnings from the conditions

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
     * @var string
     * @con_has_field        true
     * @con_is_notnull       false
     * @con_fieldtype        text
     * @con_length           20
     */
    public $note_type;

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
     * Get the default codes for the note types
     * specific codes may be in the same 100 range
     * @return array type => code
     * @see ilExamOrgaRecordChecker::updateNotes()
     * @see ilExamOrgaNotesField::setFilterCondition()
     */
    public static function getDefaultCodes()
    {
        return [
            self::TYPE_ZOOM => 100,
            self::TYPE_CAMPUS => 200,
            self::TYPE_ROLES => 300,
            self::TYPE_CONDITION => 400
        ];
    }

    /**
     * Get a textual representation of the notes
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

    /**
     * Get the notes for a record of a certain type
     *
     * @param int $record_id
     * @return self[]
     */
    public static function getRecordNotesForType($record_id, $type)
    {
        return ilExamOrgaNote::where(['record_id' => $record_id, 'note_type' => $type])->orderBy('created_at')->get();
    }

    /**
     * Get the notes for a record of a certain type by their texts
     * Will keep existing notes with the same texts, add new notes for new texts and delete obsoloete notes
     *
     * @param int $record_id
     * @param string $type
     * @param array $data [ ['note' => string, 'code' => int], ...]
     */
    public static function setRecordNotesByData($record_id, $type, $data)
    {
        // get the existing notes, clustered by their texts
        /** @var ilExamOrgaNote[] $existing */
        $existing = [];
        foreach (ilExamOrgaNote::where(['record_id' => $record_id, 'note_type' => $type])->orderBy('created_at')->get() as $note) {
            $existing[$note->note][] = $note;
        }

        foreach ($data as $row) {

            if (is_array($existing[$row['note']])) {
                // first note with the same text should not be deleted
                array_shift($existing[$row['note']]);
            } else {
                // add new note
                $note = new ilExamOrgaNote();
                $note->record_id = $record_id;
                $note->note_type = $type;
                $note->note = $row['note'];
                $note->code = $row['code'];
                $note->create();
            }
        }
        // delete the not found notes and double notes
        foreach ($existing as $notes) {
            foreach ($notes as $note) {
                $note->delete();
            }
        }
    }
}
