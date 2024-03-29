<?php

require_once(__DIR__ . '/../field/interface.ilExamOrgaFieldValues.php');


/**
 * Base Representation of an Exam
 *
 * Create new condition
 * INSERT INTO xamo_cond_seq(sequence) VALUES (NULL)
 * Then create a new row with the value of sequence as id
 */
class ilExamOrgaCondition extends ActiveRecord implements ilExamOrgaFieldValues
{

    /**
     * Prevent a saving of the record at all
     */
    const LEVEL_HARD = 'hard';

    /**
     * Prevent an creation of the record if not done by admin (edit all records)
     * Allow an update if the condition is already broken (was saved by admin)
     * Show a warning and add it to the notes.
     */
    const LEVEL_SOFT = 'soft';

    /**
     * Allow saving for all users but show a warning and add it to the notes.
     */
    const LEVEL_WARN = 'warn';

    /**
     * All partial conditions must fit
     */
    const TYPE_REQUIRE = 'require';

    /**
     * The combination of the partial conditions mur not fit
     */
    const TYPE_EXCLUDE = 'exclude';


    /**
     * @return string
     * @description Return the Name of your Database Table
     */
    public static function returnDbTableName()
    {
        return 'xamo_cond';
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
     * @con_is_notnull       true
     * @con_fieldtype        text
     * @con_length           10
     */
    public $level = 'hard';


    /**
     * @var string
     * @con_has_field        true
     * @con_is_notnull       true
     * @con_fieldtype        text
     * @con_length           10
     */
    public $cond_type = 'require';


    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        timestamp
     */
    public $reg_min_date;

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        timestamp
     */
    public $reg_max_date;

    /**
     * @var integer
     * @con_has_field        true
     * @con_fieldtype        integer
     * @con_length           4
     */
    public $reg_min_days_before;

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        date
     */
    public $exam_from_date;

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        date
     */
    public $exam_to_date;

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           200
     */
    public $exam_formats;

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           200
     */
    public $exam_types;

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        date
     */
    public $exam_min_date;

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        date
     */
    public $exam_max_date;

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           10
     */
    public $weekdays;

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           10
     */
    public $min_daytime;

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           10
     */
    public $max_daytime;

    /**
     * @var integer
     * @con_has_field        true
     * @con_fieldtype        integer
     * @con_length           4
     */
    public $max_exams_per_day;

    /**
     * @var integer
     * @con_has_field        true
     * @con_fieldtype        integer
     * @con_length           4
     */
    public $max_exams_per_week;

    /**
     * @var integer
     * @con_has_field        true
     * @con_fieldtype        integer
     * @con_length           4
     */
    public $max_exams_per_month;


    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           2000
     * @con_is_notnull       true
     */
    public $failure_message;


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
     * Check if record is not yet saved
     * @return bool
     */
    public function isNew()
    {
        return empty($this->id);
    }

    /**
     * Get the conditions that are active for an object
     * @param int $obj_id
     * @return self[]
     */
    public static function getActiveConditions($obj_id)
    {
        global $DIC;
        $db = $DIC->database();

        $cond = "obj_id = " . $db->quote($obj_id, 'integer')
            . " AND (reg_min_date IS NULL OR reg_min_date <= FROM_UNIXTIME(" . time() . "))"
            . " AND (reg_max_date IS NULL OR reg_max_date >= FROM_UNIXTIME(" . time() . "))";

        return self::where($cond)->get();
    }


    /**
     * Check a record if the condition is satisfied
     * @param ilExamOrgaRecord $record
     * @return bool
     */
    public function checkRecord(ilExamOrgaRecord $record)
    {
        // FILTER: not matching => condition will not be checked => return true

        if (!empty($this->exam_formats) && !in_array($record->exam_format, self::_toArray($this->exam_formats))) {
            return true;
        }

        if (!empty($this->exam_from_date) & $record->exam_date < $this->exam_from_date) {
            return true;
        }

        if (!empty($this->exam_to_date) & $record->exam_date > $this->exam_to_date) {
            return true;
        }


         // Set the return value for mismatching or matching condition

        switch ($this->cond_type) {
            case self::TYPE_EXCLUDE:
                $mismatch = true;
                $match = false;
                break;

            case self::TYPE_REQUIRE:
            default:
                $mismatch = false;
                $match = true;
                break;
        }


        // CONDITIONS: not matching => condition failed => return mismatch value

        if (!empty($this->exam_types) && !in_array($record->exam_type, self::_toArray($this->exam_types))) {
            return $mismatch;
        }

        if (!empty($this->exam_min_date && $record->exam_date < $this->exam_min_date)) {
            return $mismatch;
        }

        if (!empty($this->exam_max_date && $record->exam_date > $this->exam_max_date)) {
            return $mismatch;
        }

        if (!empty($this->reg_min_days_before)) {
            $time = ( empty($record->created_at) ? time() : $record->created_at);

            $day = new ilDate($time, IL_CAL_UNIX);
            $day->increment(ilDate::DAY, $this->reg_min_days_before);
            $compare = $day->get(IL_CAL_DATE);

            if ($record->exam_date < $compare) {
                return $mismatch;
            }
        }

        switch ($this->weekdays) {
            case 'Mo-Fr':
                if ($record->getWeekday() > 5) {
                    return $mismatch;
                }
                break;
            case 'Mo-Sa':
                if ($record->getWeekday() > 6) {
                    return $mismatch;
                }
                break;
        }

        if (!empty($this->min_daytime)) {
            if ($record->getEarliestStart() < $this->min_daytime) {
                return $mismatch;
            }
        }

        if (!empty($this->max_daytime)) {
            if ($record->getLatestEnd() > $this->max_daytime) {
                return $mismatch;
            }
        }


        // DEFAULT: all checks passed => conditions satisfied => return match value
        return $match;
    }


    /**
     * Get an array of a comma separated string
     * @return string[]
     */
    protected static function _toArray($list)
    {
        $array = [];
        foreach (explode(',', (string) $list) as $value) {
            if (!empty(trim($value))) {
                $array[] = trim($value);
            }
        }
        return $array;
    }
}
