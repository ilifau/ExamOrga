<?php

/**
 * Base Representation of an Exam
 */
class ilExamRecord extends ActiveRecord
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
    protected $id = 0;

    /**
     * @var integer
     * @con_has_field        true
     * @con_is_notnull       true
     * @con_fieldtype        integer
     * @con_length           4
     */
    protected $obj_id = 0;


    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           200
     */
    protected $fau_unit = '';


    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           200
     */
    protected $fau_chair = '';

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_is_notnull       true
     * @con_length           200
     */
    protected $fau_lecturer = '';


    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           200
     */
    protected $mail_address = '';


    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           200
     */
    protected $mail_title = '';

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_is_notnull       true
     * @con_length           20
     */
    protected $exam_format = '';

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           20
     */
    protected $exam_method = '';

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           20
     */
    protected $exam_type = '';

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_is_notnull       true
     * @con_length           200
     */
    protected $exam_title = '';

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        date
     * @con_is_notnull       true
     */
    protected $exam_date = '';

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           200
     */
    protected $alternative_dates = '';

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           200
     */
    protected $exam_runs = '';

    /**
     * @var integer
     * @con_has_field        true
     * @con_fieldtype        integer
     * @con_length           4
     */
    protected $run_minutes = '';

    /**
     * @var integer
     * @con_has_field        true
     * @con_fieldtype        integer
     * @con_length           4
     */
    protected $num_participants = '';

    /**
     * @var integer
     * @con_has_field        true
     * @con_fieldtype        integer
     * @con_length           4
     */
    protected $test_ref_id = '';

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           1000
     */
    protected $admins = '';

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           1000
     */
    protected $monitors = '';

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           200
     */
    protected $room = '';


    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           2000
     */
    protected $remarks = '';

    /**
     * @var integer
     * @con_has_field        true
     * @con_fieldtype        integer
     * @con_length           4
     */
    protected $booking_in_process = '';

    /**
     * @var integer
     * @con_has_field        true
     * @con_fieldtype        integer
     * @con_length           4
     */
    protected $booking_approved = '';

    /**
     * @var integer
     * @con_has_field        true
     * @con_fieldtype        integer
     * @con_length           4
     */
    protected $finally_approved = '';

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           20
     */
    protected $team_agent = '';

    /**
     * @var integer
     * @con_has_field        true
     * @con_fieldtype        integer
     * @con_length           4
     */
    protected $room_approved = '';

    /**
     * @var integer
     * @con_has_field        true
     * @con_fieldtype        integer
     * @con_length           4
     */
    protected $room_in_univis = '';


    /**
     * @var integer
     * @con_has_field        true
     * @con_fieldtype        integer
     * @con_length           4
     */
    protected $quality_checked = '';

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           20
     */
    protected $reg_code = '';

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           200
     */
    protected $team_students = '';


    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           200
     */
    protected $team_standby = '';

    /**
     * @var integer
     * @con_has_field        true
     * @con_fieldtype        integer
     * @con_length           4
     */
    protected $ips_active = '';

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           2000
     */
    protected $tech_details = '';

    /**
     * @var integer
     * @con_has_field        true
     * @con_fieldtype        integer
     * @con_length           4
     */
    protected $settings_checked = '';

    /**
     * @var integer
     * @con_has_field        true
     * @con_fieldtype        integer
     * @con_length           4
     */
    protected $seb_checked = '';

    /**
     * @var integer
     * @con_has_field        true
     * @con_is_notnull       true
     * @con_fieldtype        integer
     * @con_length           4
     */
    protected $owner_id = 0;

    /**
     * @var integer
     * @con_has_field        true
     * @con_is_notnull       true
     * @con_fieldtype        integer
     * @con_length           4
     */
    protected $created_at = 0;

    /**
     * @var integer
     * @con_has_field        true
     * @con_is_notnull       true
     * @con_fieldtype        integer
     * @con_length           4
     */
    protected $created_by = 0;

    /**
     * @var integer
     * @con_has_field        true
     * @con_is_notnull       true
     * @con_fieldtype        integer
     * @con_length           4
     */
    protected $modified_at = 0;

    /**
     * @var integer
     * @con_has_field        true
     * @con_is_notnull       true
     * @con_fieldtype        integer
     * @con_length           4
     */
    protected $modified_by = 0;
}
