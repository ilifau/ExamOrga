<?php

require_once(__DIR__ . '/../field/interface.ilExamOrgaFieldValues.php');

/**
 * Base Representation of an Exam
 */
class ilExamOrgaRecord extends ActiveRecord implements ilExamOrgaFieldValues
{

    /**
     * @return string
     * @description Return the Name of your Database Table
     */
    public static function returnDbTableName()
    {
        return 'xamo_record';
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
     * @con_fieldtype        text
     * @con_length           200
     */
    public $fau_unit;


    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           200
     */
    public $fau_chair;

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_is_notnull       true
     * @con_length           200
     */
    public $fau_lecturer;


    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           200
     */
    public $mail_address;


    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           200
     */
    public $mail_title;

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_is_notnull       true
     * @con_length           20
     */
    public $exam_format;

    /**
     * @var integer
     * @con_has_field        true
     * @con_fieldtype        integer
     * @con_length           4
     */
    public $force_presence;

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           20
     */
    public $exam_method;

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           20
     */
    public $exam_type;

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_is_notnull       true
     * @con_length           200
     */
    public $exam_title;

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        date
     * @con_is_notnull       true
     */
    public $exam_date;

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           1000
     */
    public $exam_ids;

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           200
     */
    public $alternative_dates;

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           200
     */
    public $exam_runs;

    /**
     * @var integer
     * @con_has_field        true
     * @con_fieldtype        integer
     * @con_length           4
     */
    public $run_minutes;

    /**
     * @var integer
     * @con_has_field        true
     * @con_fieldtype        integer
     * @con_length           4
     */
    public $num_participants;

    /**
     * @var integer
     * @con_has_field        true
     * @con_fieldtype        integer
     * @con_length           4
     */
    public $test_ref_id;

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           2000
     */
    public $admins_text;

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           1000
     */
    public $admins;

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           1000
     */
    public $correctors;

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           1000
     */
    public $monitors;

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           200
     */
    public $room;


    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           2000
     */
    public $remarks;

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           20
     */
    public $booking_status = 'requested';

    /**
     * @var integer
     * @con_has_field        true
     * @con_fieldtype        integer
     * @con_length           4
     */
    public $finally_approved;

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           20
     */
    public $team_agent;

    /**
     * @var integer
     * @con_has_field        true
     * @con_fieldtype        integer
     * @con_length           4
     */
    public $room_approved;

    /**
     * @var integer
     * @con_has_field        true
     * @con_fieldtype        integer
     * @con_length           4
     */
    public $room_in_univis;


    /**
     * @var integer
     * @con_has_field        true
     * @con_fieldtype        integer
     * @con_length           4
     */
    public $quality_checked;

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           20
     */
    public $reg_code;

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           100
     */
    public $course_link;

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           200
     */
    public $team_students;


    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           200
     */
    public $team_standby;

    /**
     * @var integer
     * @con_has_field        true
     * @con_fieldtype        integer
     * @con_length           4
     */
    public $ips_active;

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           2000
     */
    public $tech_details;

    /**
     * @var integer
     * @con_has_field        true
     * @con_fieldtype        integer
     * @con_length           4
     */
    public $settings_checked;

    /**
     * @var integer
     * @con_has_field        true
     * @con_fieldtype        integer
     * @con_length           4
     */
    public $seb_checked;

    /**
     * @var integer
     * @con_has_field        true
     * @con_is_notnull       true
     * @con_fieldtype        integer
     * @con_length           4
     */
    public $owner_id;

    /**
     * @var int
     * @con_has_field        true
     * @con_is_notnull       true
     * @con_fieldtype        integer
     * @con_length           4
     */
    public $created_at;

    /**
     * @var integer
     * @con_has_field        true
     * @con_is_notnull       true
     * @con_fieldtype        integer
     * @con_length           4
     */
    public $created_by;

    /**
     * @var int
     * @con_has_field        true
     * @con_is_notnull       true
     * @con_fieldtype        integer
     * @con_length           4
     */
    public $modified_at;

    /**
     * @var integer
     * @con_has_field        true
     * @con_is_notnull       true
     * @con_fieldtype        integer
     * @con_length           4
     */
    public $modified_by;

    /**
     * Find a record with a given id that belongs to an orga object
     * @param int $id
     * @param int $obj_id
     * @return ilExamOrgaRecord|null
     */
    public static function findInObject($id, $obj_id)
    {
        /** @var ilExamOrgaRecord[] $records */
        $recordList = ilExamOrgaRecord::getCollection();
        $records = $recordList->where(['id' => (int) $id, 'obj_id' => (int) $obj_id])->get();
        if (empty($records)) {
            return null;
        }
        return array_pop($records);
    }

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
     * Check if the current user is the owner of the record
     */
    public function isOwner()
    {
        global $DIC;
        return ($this->owner_id == $DIC->user()->getId());
    }

    public function isAdmin()
    {
        global $DIC;
        return (in_array($DIC->user()->getLogin(), $this->getAdminLogins()));
    }

    /**
     * Set creation info and create record
     */
    public function create()
    {
        global $DIC;

        $time = time();
        if (empty($this->created_at)) {
            $this->created_at = $time;
        }
        if (empty($this->created_by)) {
            $this->created_by = $DIC->user()->getId();
        }
        if (empty( $this->modified_at)) {
            $this->modified_at = $time;
        }
        if (empty($this->modified_by)) {
            $this->modified_by = $DIC->user()->getId();
        }

        if (empty($this->owner_id)) {
            $this->owner_id = $DIC->user()->getId();
        }

        parent::create();
    }

    /**
     * Set modification info and update record
     */
    public function update()
    {
        global $DIC;

        $time = time();
        $this->modified_at = $time;
        $this->modified_by = $DIC->user()->getId();

        parent::update();
    }

    /**
     * Get a title for the record
     * @return string
     */
    public function getTitle() {

        $date = new ilDate($this->exam_date, IL_CAL_DATE);

        ilDatePresentation::setUseRelativeDates(false);
        return $this->fau_lecturer . ' / ' . (!empty($this->exam_title) ? $this->exam_title . ' / ' : ''). ilDatePresentation::formatDate($date);
    }

    /**
     * Get the logins of admins as an array
     * @return string[]
     */
    public function getAdminLogins()
    {
        $logins = [];
        foreach (explode(',', (string) $this->admins) as $login) {
            if (!empty(trim($login))) {
                $logins[] = trim($login);
            }
        }
        return $logins;
    }

    /**
     * Get the logins of correctors as an array
     * @return string[]
     */
    public function getCorrectorLogins()
    {
        $logins = [];
        foreach (explode(',', (string) $this->correctors) as $login) {
            if (!empty(trim($login))) {
                $logins[] = trim($login);
            }
        }
        return $logins;
    }

    /**
     * Get the weekday of the exam date (1 for Monday, 7 for Sunday)
     */
    public function getWeekday()
    {
        $date = new DateTime($this->exam_date);
        return $date->format('N');
    }

    /**
     * Get the time of the earliest run start on the exam day
     */
    public function getEarliestStart()
    {
        $times = self::_toArray($this->exam_runs);
        if (empty($times)) {
            return '00:00';
        }
        sort($times);
        return $times[0];
    }


    /**
     * Get the time of the latest run end on the exam day
     */
    public function getLatestEnd()
    {
        $times = self::_toArray($this->exam_runs);
        if (empty($times)) {
            return '23:59';
        }
        sort($times);
        $times = array_reverse($times);

        $time = explode(':', $times[0]);
        $hour = $time[0];
        $minute = $time[1] + (int) $this->run_minutes;

        $hour = $hour + intdiv($minute, 60);
        $minute = $minute % 60;

        if ($hour > 23) {
            return '23:59';
        }
        return sprintf("%02d:%02d", $hour, $minute);
    }

    /**
     * Check if the exam format is presence
     * @return bool
     */
    public function isPresence()
    {
        return ($this->exam_format == 'presence');
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
