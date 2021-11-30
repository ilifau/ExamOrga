<?php

/**
 * Message sending status
 */
class ilExamOrgaMessageSent extends ActiveRecord
{
    /**
     * @return string
     * @description Return the Name of your Database Table
     */
    public static function returnDbTableName()
    {
        return 'xamo_message_sent';
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
     * @con_is_notnull       true
     * @con_fieldtype        text
     * @con_length           20
     */
    public $message_type;



    /**
     * @var int
     * @con_has_field        true
     * @con_is_notnull       true
     * @con_fieldtype        integer
     * @con_length           4
     */
    public $sent_at;


    /**
     * Check if a message is already sent for a record
     * @param int $record_id
     * @param string $message_type
     */
    public static function isSent($record_id, $message_type)
    {
        return self::where(['record_id' => $record_id, 'message_type' => $message_type])->hasSets();
    }

    /**
     * Set a message sent for a record
     * @param int $record_id
     * @param string $message_type
     */
    public static function setSent($record_id, $message_type)
    {
        $sent = self::where(['record_id' => $record_id, 'message_type' => $message_type])->first();

        if (empty($sent)) {
            $sent = new self;
            $sent->record_id = $record_id;
            $sent->message_type = $message_type;
        }

        $sent->sent_at = time();
        $sent->save();
    }

    /**
     * Set a message unsent for a record
     * @param int $record_id
     * @param string $message_type
     */
    public static function setUnsent($record_id, $message_type)
    {
        foreach(self::where(['record_id' => $record_id, 'message_type' => $message_type])->get() as $sent) {
            $sent->delete();
        }
    }
}
