<?php

require_once (__DIR__ . '/class.ilObjExamOrga.php');
require_once (__DIR__ . '/record/class.ilExamOrgaRecord.php');
require_once (__DIR__ . '/campus/class.ilExamOrgaCampusExam.php');

class ilExamOrgaCronHandler
{
    private $parent_obj;
    /** @var  ilAccessHandler $access */
    protected $access;

    /** @var  ilLanguage $lng */
    protected $lng;

    /** @var ilDBInterface */
    protected $db;

    /** @var ilExamOrgaPlugin $plugin */
    protected $plugin;

    /** @var ilExamOrgaConfig */
    protected $config;


    /** @var string */
    protected $logfile;

    /**
     * constructor
     * @param ilExamOrgaPlugin $plugin
     */
    public function __construct($plugin)
    {
        global $DIC;

        $this->access = $DIC->access();
        $this->lng = $DIC->language();
        $this->db = $DIC->database();

        $this->plugin = $plugin;
        $this->config = $plugin->getConfig();

        $this->logfile = ILIAS_DATA_DIR . '/ExamOrga.log';
    }

    /**
     * Update the list of exams
     */
    public function updateExams()
    {
        ilExamOrgaCampusExam::updateExams($this->plugin);
        return true;
    }

    /**
     * Check the records for confirmations, warnings, reminders
     * @return int  number of checked records
     */
    public function checkRecords()
    {
        require_once(__DIR__ . '/record/class.ilExamOrgaRecordChecker.php');

        $checked = 0;
        foreach($this->plugin->getActiveObjects() as $obj_id => $title) {

            $object = null;
            foreach(ilObject::_getAllReferences($obj_id) as $ref_id) {
                if (!ilObject::_isInTrash($ref_id)) {
                    $object = new ilObjExamOrga($ref_id);
                    break;
                }
            }
            if (!isset($object)) {
                continue;
            }

            if (!ilContext::usesHTTP()) {
                echo "Checking records of object " . $object->getTitle() . "...\n";
            }

            foreach (ilExamOrgaRecord::getForObject($object->getId()) as $record) {

                // don' check records after the exam day
                if (isset($record->exam_date)) {
                    $exam = new ilDate($record->exam_date, IL_CAL_DATE);
                    $today = new ilDate(time(), IL_CAL_UNIX);
                    if (ilDate::_after($today, $exam, IL_CAL_DAY))
                    {
                        continue;
                    }
                }

                if (!ilContext::usesHTTP()) {
                    echo "Check record " .$record->getTitle() . "...\n";
                }

                $checker = new ilExamOrgaRecordChecker(ilExamOrgaRecordChecker::PURPOSE_CRON, $object, $record);
                $checker->doChecks();
                $checker->handleCheckResult();
                $checked++;
            }
        }

        return $checked;
    }
}