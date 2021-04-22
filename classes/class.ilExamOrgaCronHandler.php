<?php

require_once (__DIR__ . '/record/class.ilExamOrgaRecord.php');

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
        if (!empty($this->config->get('campus_soap_url'))) {
            require_once (__DIR__ . '/campus/class.ilExamOrgaCampusExam.php');
            ilExamOrgaCampusExam::updateExams($this->plugin);
            return true;
        }
        else {
            return false;
        }
    }

}