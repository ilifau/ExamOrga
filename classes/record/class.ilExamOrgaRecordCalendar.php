<?php

require_once (__DIR__ . '/class.ilExamOrgaRecord.php');
include_once './Services/Calendar/classes/iCal/class.ilICalWriter.php';

/**
 * Class ilExamOrgaRecordCalendar
 */
class ilExamOrgaRecordCalendar
{
    const LINEBREAK = "\r\n";
    #const LINEBREAK = '<br />';
    // minus one to fix multi line breaks.
    const LINE_SIZE = 74;
    const BEGIN_LINE_WHITESPACE = ' ';

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

    /** @var string */
    private $ical = '';
    
    /**
     * Initialize the data
     * @param ilObjExamOrga $object
     */
    public function init( $object)
    {
        $this->object = $object;
        $this->plugin = $object->plugin;
        $this->ical = '';

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
    /*    if (!$this->object->canViewAllRecords()) {
            $recordList->where(['owner_id' => $DIC->user()->getId()]);
        }*/

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

        $this->addLine('BEGIN:VCALENDAR');
        $this->addLine('VERSION:2.0');
        $this->addLine('METHOD:PUBLISH');
        $this->addLine('PRODID:-//ilias.de/NONSGML ILIAS Calendar V4.4//EN');
      //  $this->addTimezone(); TODO
        
        $i = 0;
        foreach($records as $record)
        {
            // calculate start and end time; TODO: implement more than 1 runs
            $start = new DateTime($record->exam_date);
            $runStart = explode(':', $record->exam_runs);
            $start->modify('+'.($runStart[0]*60+$runStart[1]).' minutes');
            $end = new DateTime($record->exam_date);
            $end->modify('+'.($runStart[0]*60+$runStart[1]+$record->run_minutes).' minutes'); 
            $examStart =  $start->format('Ymd\THis');
            $examEnd =  $end->format('Ymd\THis');
            $now = new DateTime();
            $updatedTime = $now->format('Ymd\THis');

            // TODO: calculate category 

            // example event; TODO: write records from Exam Orga
            $this->addLine('BEGIN:VEVENT');
            $this->addLine('SUMMARY:'.$record->fau_unit.'-'.$record->exam_title);
            $this->addLine('UID:studon-c7614cff-3549-4a00-9152-d25cc1fe077d'.$i); // TODO UID generieren
            $this->addLine('SEQUENCE:0'); // TODO: implement more than 1 runs
            $this->addLine('LOCATION:'.$record->room);
            $this->addLine('DTSTART;Europe/Berlin:'.$examStart);
            $this->addLine('DTEND;Europe/Berlin:'.$examEnd);
            $this->addLine('DTSTAMP;;Europe/Berlin:'.$updatedTime);
            $this->addLine('CATEGORIES:Gelbe Kategorie');
            $this->addLine('DESCRIPTION:Dozierender: Max Mustermann3\nE-Mail: Max.Mustermann@fautest.de\nHiwis & Azubis:\nTeilnehmende: 10\nSelbstregistrieungscode: fgRtz%69\nLink: https://studontest.fautest.de');
            $this->addLine('END:VEVENT');
            $i++;
        }

        $this->addLine('END:VCALENDAR');
    }

    public function getExportString()
    {
        return $this->ical;
    }

  
    protected static function escapeText($a_text)
    {
        $a_text = str_replace("\r\n", '\\n', $a_text);

        return preg_replace(
            array(
                '/\\\/',
                '/;/',
                '/,/',
                ),
            array(
                '\\',
                '\;',
                '\,',
                ),
            $a_text
            );
    }
    
    /**
     * Add a line to the ical string
     * @return
     * @param object $a_line
     */
    protected function addLine($a_line)
    {
        //$chunks = str_split($a_line, self::LINE_SIZE);

        include_once './Services/Utilities/classes/class.ilStr.php';

        // use multibyte split
        $chunks = array();
        $len = ilStr::strLen($a_line);
        while ($len) {
            $chunks[] = ilStr::subStr($a_line, 0, self::LINE_SIZE);
            $a_line = ilStr::subStr($a_line, self::LINE_SIZE, $len);
            $len = ilStr::strLen($a_line);
        }

        for ($i = 0; $i < count($chunks); $i++) {
            $this->ical .= $chunks[$i];
            if (isset($chunks[$i + 1])) {
                $this->ical .= self::LINEBREAK;
                $this->ical .= self::BEGIN_LINE_WHITESPACE;
            }
        }
        $this->ical .= self::LINEBREAK;
    }
}