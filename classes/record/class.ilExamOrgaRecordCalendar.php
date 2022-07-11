<?php

require_once (__DIR__ . '/class.ilExamOrgaRecord.php');

/**
 * Class ilExamOrgaRecordCalendar
 */
class ilExamOrgaRecordCalendar extends ilCalendarExport
{
    /** @var ilObjExamOrga */
    protected $object;

    /** @var ilExamOrgaPlugin */
    protected $plugin;

    /** @var ilExamOrgaField[] */
    protected $fields;

    /** @var ilExamOrgaRecord[] */
    protected $records = [];

    /** @var string */
    protected $error;
    
    /**
     * Initialize the data
     * @param ilObjExamOrga $object
     */
    public function init( $object)
    {
        $this->object = $object;
        $this->plugin = $object->plugin;

        $fields = $object->getAvailableFields();

        // put id at the beginning
        $this->fields['id'] = $fields['id'];
        unset($fields['id']);

        foreach ($fields as $field) {
            if ($field->isForCalendar()) {
               $this->fields[$field->name] = $field;
            }
       }
    }

    /**
     * Init the data of the exercise members
     */
    protected function initRecords()
    {
        global $DIC;

        /** @var ilExamOrgaRecord $record */
        $recordList = ilExamOrgaRecord::getCollection();
        $recordList->where(['obj_id' => $this->object->getId()]);

        // limit to owned records
        if (!$this->object->canViewAllRecords()) {
            $recordList->where(['owner_id' => $DIC->user()->getId()]);
        }

        $recordList->orderBy('id');
        $this->records = $recordList->get();

        // preload field data
        foreach ($this->fields as $field) {
            $field->preload($this->records);
        }
    }

    /**
     * Export to ics file
     * @param ilObjExamOrga $object
     */
    public function exportToIcs($object) {
        $this->init($object);
        $this->initRecords();
        $fields = $this->fields;
        $records = $this->records;

        $this->writer->addLine('BEGIN:VCALENDAR');
        $this->writer->addLine('VERSION:2.0');
        $this->writer->addLine('METHOD:PUBLISH');
        $this->writer->addLine('PRODID:-//ilias.de/NONSGML ILIAS Calendar V4.4//EN');
        $this->addTimezone();
        
        $i = 0;
        foreach($records as $record)
        {
            // calculate start and end time; TODO: implement more than 1 runs
            $start = new DateTime($record->exam_date);
            $runStart = explode(':', $record->exam_runs);
            $start->modify('+'.$runStart[0]*60+$runStart[1].' minutes');
            $end = new DateTime($record->exam_date);
            $end->modify('+'.$runStart[0]*60+$runStart[1]+$record->run_minutes.' minutes'); 
            $examStart =  $start->format('Ymd\THis');
            $examEnd =  $end->format('Ymd\THis');
            $now = new DateTime();
            $updatedTime = $now->format('Ymd\THis');

            // TODO: calculate category 

            // example event; TODO: write records from Exam Orga
            $this->writer->addLine('BEGIN:VEVENT');
            $this->writer->addLine('SUMMARY:'.$record->fau_unit.'-'.$record->exam_title);
            $this->writer->addLine('UID:studon-c7614cff-3549-4a00-9152-d25cc1fe077d'.$i); // TODO UID generieren
            $this->writer->addLine('SEQUENCE:0'); // TODO: implement more than 1 runs
            $this->writer->addLine('LOCATION:'.$record->room);
            $this->writer->addLine('DTSTART;Europe/Berlin:'.$examStart);
            $this->writer->addLine('DTEND;Europe/Berlin:'.$examEnd);
            $this->writer->addLine('DTSTAMP;;Europe/Berlin:'.$updatedTime);
            $this->writer->addLine('CATEGORIES:Gelbe Kategorie');
            $this->writer->addLine('DESCRIPTION:Dozierender: Max Mustermann2\nE-Mail: Max.Mustermann@fautest.de\nHiwis & Azubis:\nTeilnehmende: 10\nSelbstregistrieungscode: fgRtz%69\nLink: https://studontest.fautest.de');
            $this->writer->addLine('END:VEVENT');
            $i++;
        }

        $this->writer->addLine('END:VCALENDAR');
    }
}