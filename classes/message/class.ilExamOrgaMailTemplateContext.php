<?php
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

require_once(__DIR__ . '/../class.ilExamOrgaPlugin.php');
require_once (__DIR__ . '/../class.ilObjExamOrga.php');
require_once(__DIR__ . '/../record/class.ilExamOrgaRecord.php');
/**
 * Handles course mail placeholders
 */
class ilExamOrgaMailTemplateContext extends ilMailTemplateContext
{
    const ID = 'xamo_mail_template_context';

    /** @var ilExamOrgaPlugin */
    protected $plugin;


    public function __construct()
    {
        parent::__construct();
        $this->plugin = ilExamOrgaPlugin::getInstance();
    }

    /**
     * @return string
     */
    public function getId() : string
    {
        return self::ID;
    }

    /**
     * @return string
     */
    public function getTitle() : string
    {
        return $this->plugin->txt('obj_xamo');
    }

    /**
     * @return string
     */
    public function getDescription() : string
    {
        return $this->plugin->txt('obj_xamo');
    }

    /**
     * Return an array of placeholders
     * @return array
     */
    public function getSpecificPlaceholders() : array
    {

        $placeholders = [
            'exam_title' => [
              'placeholder' => 'EXAM_TITLE',
              'label' => $this->plugin->txt('record_exam_title')
            ],
            'exam_date' => [
              'placeholder' => 'EXAM_DATE',
              'label' => $this->plugin->txt('record_exam_date')
            ],
            'exam_link' => [
              'placeholder' => 'EXAM_LINK',
              'label' => $this->plugin->txt('record_course_link')
            ],
            'record_link' => [
                'placeholder' => 'RECORD_LINK',
                'label' => $this->plugin->txt('record_link')
            ]
        ];


        return $placeholders;
    }

    /**
     * @inheritdoc
     * @param array $context_parameters ['ref_id' => int, 'record' => ilExamOrgaRecord]
     */
    public function resolveSpecificPlaceholder(string $placeholder_id, array $context_parameters, ilObjUser $recipient = null, bool $html_markup = false) : string
    {
        /** @var ilExamOrgaRecord $record */
        if (isset($context_parameters['record'])) {
            $record = $context_parameters['record'];
        }
        else {
            $record = $this->getExampleRecord();
        }


        switch ($placeholder_id) {

            case 'exam_title':
                return $record->exam_title;

            case 'exam_date':
                $date = new ilDate($record->exam_date, IL_CAL_DATE);
                ilDatePresentation::setUseRelativeDates(false);
                return ilDatePresentation::formatDate($date);

            case 'exam_link':
                return $record->course_link;

            case 'record_link':
                if (isset($context_parameters['ref_id'])) {
                    return ilObjExamOrga::_getRecordLink($context_parameters['ref_id'], $record->getId());
                }
        }

        return '';
    }

    /**
     * Get an example record for the preview
     * @return ilExamOrgaRecord
     */
    public function getExampleRecord()
    {
        global $DIC;

        $record = new ilExamOrgaRecord();
        $record->id = 9999;
        $record->exam_title = $this->plugin->txt('example_title');
        $record->exam_date = '2021-01-01 00:00:00';
        $record->course_link = 'www.studon-exam.fau.de/winter21/goto.php?target=crs_9999';
        $record->owner_id = $DIC->user()->getId();

        return $record;
    }
}
