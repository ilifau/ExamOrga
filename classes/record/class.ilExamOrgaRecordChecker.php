<?php

require_once (__DIR__ . '/../message/class.ilExamOrgaMessage.php');
require_once (__DIR__ . '/../notes/class.ilExamOrgaNote.php');

/**
 * Check records for problems
 * create or delete notes
 * send messages
 */
class ilExamOrgaRecordChecker
{
    const PURPOSE_SAVE = 'save';
    const PURPOSE_CRON = 'cron';

    const SHORT_DAYS = 3;
    const LONG_DAYS = 7;

    const EARLIEST_CONFIRMATION = '2022-01-01 00:00:00';

    /**
     * @var ilExamOrgaPlugin
     */
    protected $plugin;

    /**
     * @var ilObjExamOrga
     */
    protected $object;

    /**
     * @var ilExamOrgaRecord
     */
    protected $record;

    /**
     * @var ilExamOrgaRecord
     */
    protected $original;

    /**
     * @var ilExamOrgaMessenger
     */
    protected $messenger;

    /** @var string */
    protected $purpose;


    /** @var string[] */
    protected $failures = [];

    /** @var array */
    protected $warnings = [
        ilExamOrgaMessage::TYPE_WARNING_ZOOM => [],
        ilExamOrgaMessage::TYPE_WARNING_CAMPUS => [],
        ilExamOrgaMessage::TYPE_WARNING_ROLES =>[],
        ilExamOrgaMessage::TYPE_WARNING_CONDITION =>[]
    ];

    /**
     * @var array
     */
    protected $notes_messages_types = [
        ilExamOrgaNote::TYPE_ZOOM => ilExamOrgaMessage::TYPE_WARNING_ZOOM,
        ilExamOrgaNote::TYPE_CAMPUS => ilExamOrgaMessage::TYPE_WARNING_CAMPUS,
        ilExamOrgaNote::TYPE_ROLES => ilExamOrgaMessage::TYPE_WARNING_ROLES,
        ilExamOrgaNote::TYPE_CONDITION => ilExamOrgaMessage::TYPE_WARNING_CONDITION,
    ];


    /** @var bool */
    protected $confirm_booking = false;

    /** @var bool */
    protected $reminder1 = false;

    /** @var bool */
    protected $reminder2 = false;

    /** @var bool */
    protected $confirmation_sent = false;

    /**
     * Constructor
     * @param string $purpose
     * @param ilObjExamOrga $object
     * @param ilExamOrgaRecord $record
     * @param ilExamOrgaRecord $original
     */
    public function __construct($purpose, $object, $record, $original = null)
    {
        $this->purpose = $purpose;
        $this->object = $object;
        $this->record = $record;
        $this->original = $original;
        $this->messenger = $object->getMessenger();
        $this->plugin = ilExamOrgaPlugin::getInstance();
    }

    /**
     * Get the failures detected in doChecks()
     * These should prevent a record saving
     * @return string[]
     */
    public function getFailures()
    {
        return $this->failures;
    }

    /**
     * Get the warnings detected in doChecks()
     * These should be saved as notes after a record is updated or checked by cron
     * @return string[]
     */
    public function getWarnings()
    {
        $all = [];
        foreach ($this->warnings as $type => $warnings) {
            $all = array_merge($all, $warnings);
        }
        return $all;
    }

    /**
     * Get if the confirmation was sent by this check
     */
    public function isConfirmationSent()
    {
        return $this->confirmation_sent;
    }


    /**
     * Do the checks before a record is updated or when a record is checked by cron
     */
    public function doChecks()
    {
        $this->loadWarnings();

        // checks that create notes should only be done when a record is interactively created or updated
        // this will replace the loaded notes of the certain type
        if ($this->purpose == self::PURPOSE_SAVE) {

            if($this->object->data->getPurpose() == ilExamOrgaData::PURPOSE_WRITTEN) {
                $this->checkCampus();
                $this->checkRoles();
            }
            $this->checkConditions();
        }

        $this->checkConfirmation();
        $this->checkReminder();
    }

    /**
     * Update the notes and send messages after the record is updated or checked by cron
     */
    public function handleCheckResult()
    {
        // notes should only be updated when a record is interactively  created or updated
        if ($this->purpose == self::PURPOSE_SAVE) {
            $this->updateNotes(ilExamOrgaNote::TYPE_CAMPUS, $this->warnings[ilExamOrgaMessage::TYPE_WARNING_CAMPUS]);
            $this->updateNotes(ilExamOrgaNote::TYPE_ROLES, $this->warnings[ilExamOrgaMessage::TYPE_WARNING_ROLES]);
            $this->updateNotes(ilExamOrgaNote::TYPE_CONDITION, $this->warnings[ilExamOrgaMessage::TYPE_WARNING_CONDITION]);
        }

        // don't send any message if exam date is over
        if (isset($this->record->exam_date)) {
            $exam = new ilDate($this->record->exam_date, IL_CAL_DATE);
            $today = new ilDate(time(), IL_CAL_UNIX);
            if (ilDate::_before($exam, $today, IL_CAL_DAY)) {
                return;
            }
        }

        // confirm bookins when record is save or or by cron
        if ($this->confirm_booking) {
            $this->confirmation_sent = $this->messenger->send($this->record, $this->record->isPresence() ?
                ilExamOrgaMessage::TYPE_CONFIRM_PRESENCE : ilExamOrgaMessage::TYPE_CONFIRM_REMOTE);
        }

        // send warnings and reminders only by cron
        if ($this->purpose == self::PURPOSE_CRON)
        {
            foreach ($this->warnings as $type => $warnings) {

                // send warnings for missing campus ids or roles earliest one month before the exam
                if (isset($this->record->exam_date) && ($type == ilExamOrgaMessage::TYPE_WARNING_CAMPUS || $type ==ilExamOrgaMessage::TYPE_WARNING_ROLES))
                {
                    $exam = new ilDate($this->record->exam_date, IL_CAL_DATE);
                    $month = new ilDate(time(), IL_CAL_UNIX);
                    $month->increment(IL_CAL_MONTH, 1);

                    if (!ilDate::_before($exam, $month, IL_CAL_DAY)) {
                       continue;
                    }
                }

                if (!empty($warnings)) {
                    $this->messenger->send($this->record, $type);
                }
                else {
                    $this->messenger->reset($this->record, $type);
                }
            }

            if ($this->reminder1) {
                $this->messenger->send($this->record, $this->record->isPresence() ?
                    ilExamOrgaMessage::TYPE_REMINDER1_PRESENCE : ilExamOrgaMessage::TYPE_REMINDER1_REMOTE);
            }
            if ($this->reminder2) {
                $this->messenger->send($this->record, $this->record->isPresence() ?
                    ilExamOrgaMessage::TYPE_REMINDER2_PRESENCE : ilExamOrgaMessage::TYPE_REMINDER2_REMOTE);
            }
        }
    }

    /**
     * Check if a confirmation message should be sent
     */
    protected function checkConfirmation()
    {
        if ($this->record->booking_status == 'approved' && !empty($this->record->course_link) && !empty($this->record->exam_date)) {
            $exam = new ilDate($this->record->exam_date, IL_CAL_DATE);
            $earliest = new ilDate(self::EARLIEST_CONFIRMATION, IL_CAL_DATE);

            if (ilDate::_before($exam, $earliest, IL_CAL_DAY)) {
                return;
            }

            // will only be sent once
            $this->confirm_booking = true;
        }
    }

    /**
     * Check if a reminder should be sent
     */
    public function checkReminder()
    {
        if (isset($this->record->exam_date)) {
            $exam = new ilDate($this->record->exam_date, IL_CAL_DATE);
            $today = new ilDate(time(), IL_CAL_UNIX);

            $short = new ilDate(time(), IL_CAL_UNIX);
            $short->increment(IL_CAL_DAY, self::SHORT_DAYS);

            $long = new ilDate(time(), IL_CAL_UNIX);
            $long->increment(IL_CAL_DAY, self::LONG_DAYS);

            // too late
            if (ilDate::_before($exam, $today, IL_CAL_DAY)) {
                return;
            }
            // short reminder is due
            if (ilDate::_before($exam, $short, IL_CAL_DAY) || ilDate::_equals($exam, $short, IL_CAL_DAY)) {
                $this->reminder2 = true;
            }
            // long reminder is due
            elseif (ilDate::_before($exam, $long, IL_CAL_DAY) || ilDate::_equals($exam, $long, IL_CAL_DAY)) {
                $this->reminder1 = true;
            }
        }
    }

    /**
     * Check if users are selected for additional roles
     */
    protected function checkRoles()
    {
        $this->warnings[ilExamOrgaMessage::TYPE_WARNING_ROLES] = [];

        if (empty($this->record->admins) && empty($this->record->correctors)) {
            $this->warnings[ilExamOrgaMessage::TYPE_WARNING_ROLES][] = $this->plugin->txt('warning_roles');
        }
    }

    /**
     * Check if a campus id is selected
     */
    protected function checkCampus()
    {
        $this->warnings[ilExamOrgaMessage::TYPE_WARNING_CAMPUS] = [];

        if (empty($this->record->exam_ids)) {
            $this->warnings[ilExamOrgaMessage::TYPE_WARNING_CAMPUS][] = $this->plugin->txt('warning_campus');
        }
    }


    /**
     * Check a record against the active conditions (called wen a record is saved interactively)
     * Failures should prevent a saving of the record
     */
    protected function checkConditions()
    {
        $this->warnings[ilExamOrgaMessage::TYPE_WARNING_CONDITION] = [];

        foreach ($this->object->getActiveConditions() as $cond) {
            if (!$cond->checkRecord($this->record)) {

                switch ($cond->level) {

                    // HARD level: saving is prevented for all
                    case ilExamOrgaCondition::LEVEL_HARD:
                        $this->failures[] = $cond->failure_message;
                        break;

                    // SOFT level: admins can create with warning, users can afterwards edit without warning
                    case ilExamOrgaCondition::LEVEL_SOFT:
                        if (isset($this->original) && !$cond->checkRecord($this->original)) {
                            // no failure/warning if problem existed before
                        }
                        elseif ($this->object->canEditAllRecords()) {
                            // admins get a warning when they hurt the condition the first time
                            $this->warnings[ilExamOrgaMessage::TYPE_WARNING_CONDITION][] = $cond->failure_message;
                        }
                        else {
                            // users can't save a record when they they hurt the condition the first time
                            $this->failures[] = $cond->failure_message;
                        }
                        break;

                    // WARN level: all can create and edit with warning
                    case ilExamOrgaCondition::LEVEL_WARN:
                        $this->warnings[ilExamOrgaMessage::TYPE_WARNING_CONDITION][] = $cond->failure_message;
                        break;
                }
            }
        }
    }

    /**
     * Load the existing warnings
     */
    protected function loadWarnings()
    {
        foreach ($this->notes_messages_types as $note_type => $message_type) {
            foreach (ilExamOrgaNote::getRecordNotesForType($this->record->id, $note_type) as $note) {
                $this->warnings[$message_type][] = $note->note;
            }
        }
    }


    /**
     * Update the notes of a certain type for a record
     * @param string $type
     * @param string [] $texts
     */
    protected function updateNotes($type, $texts)
    {
        $codes = ilExamOrgaNote::getDefaultCodes();
        $code = (isset($codes[$type]) ? $codes[$type] : null);

        $data = [];
        foreach ($texts as $text) {
            $data[] = ['code' => $code, 'note' => $text];
        }
        ilExamOrgaNote::setRecordNotesByData($this->record->id, $type, $data);
    }
}