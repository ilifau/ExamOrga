<?php

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

    /** @var ilExamOrgaRecord[] */
    protected $records = [];

    /** @var string */
    private $ical = '';
    
    /**
     * Initialize object data
     * @param ilObjExamOrga $object
     */
    public function init( $object)
    {
        $this->object = $object;
        $this->plugin = $object->plugin;
        $this->ical = '';
        $this->initRecords();
    }

    /**
     * Init exam records
     */
    protected function initRecords()
    {
        /** @var ilExamOrgaRecord $record */
        $recordList = ilExamOrgaRecord::getCollection();
        $recordList->where(['obj_id' => $this->object->getId()]);

        $recordList->orderBy('id');
        $this->records = $recordList->get();
    }

    /**
     * Export to ics file
     * @param ilObjExamOrga $object
     */
    public function exportToIcs($object) {
        try
        {
            $this->init($object);
            $records = $this->records;

            $this->addLine('BEGIN:VCALENDAR');
            $this->addLine('VERSION:2.0');
            $this->addLine('METHOD:PUBLISH');
            $this->addLine('PRODID:-//ilias.de/NONSGML ILIAS Calendar V4.4//EN');
            $this->addTimezone(); 
        
            foreach($records as $record)
            {
                $this->exportEventToIcs($record);
            }
            $this->addLine('END:VCALENDAR');
        }catch(Exception $e)
        {
        echo $e->getMessage();  
        }
    }

    /**
     * Export one event to ics file
     * @param ilObjExamOrgaRecord $object
     * @param int $sequence
     */
    protected function exportEventToIcs($record, $sequence=0)
    {
        try
        {
            $examCategory = "Fehlerhafter Termin";

            // calculate category
            switch($record->exam_format){
                case "presence":
                    $examCategory = "E-Prüfung in Präsenz";
                    break;
                case "open":
                    $examCategory = "Open-Book-Prüfung mit Zeitbegrenzung";
                    break;
                case "monitored";
                    $examCategory = "Fernklausur mit Videoaufsicht";
                    break;
                default: 
                    $examCategory = "Fehlerhafter Termin";
                    break;
            }

            // calculate start and end time; 
            $start = new DateTime($record->exam_date);
            $end = new DateTime($record->exam_date);
            $numRuns = 1;
            // no runs set
            if(!isset($record->exam_runs)||empty($record->exam_runs)) 
                $numRuns = 0;
            else 
            {
                $runStart = explode(',', $record->exam_runs);
                $numRuns = sizeof($runStart);
            }
            if($numRuns > 1) 
            {
                $sequence = 0;
                foreach($runStart as $s)
                {
                    // create record
                    $record2 = clone $record;
                    $record2->exam_runs = $s;
                    // export record
                    $this->exportEventToIcs($record2, $sequence);
                    $sequence++;

                }            
            }
            else
            {
                try
                {
                    if(!isset($record->exam_runs)||empty($record->exam_runs)) 
                       throw new ExamCalendarException();
                    $runStart = explode(':', $record->exam_runs);
                    $start->modify('+'.($runStart[0]*60+$runStart[1]).' minutes');
                    $end->modify('+'.($runStart[0]*60+$runStart[1]+$record->run_minutes).' minutes'); 
                    $examStart =  $start->format('Ymd\THis');
                    $examEnd =  $end->format('Ymd\THis');
                    $now = new DateTime();
                    $updatedTime = $now->format('Ymd\THis');
            
                
                }catch(ExamCalendarException $e){
                        // daily event if run not correct
                        $examStart =  $start->format('Ymd\THis');
                        $start->modify("+24 hours");
                        $examEnd =  $start->format('Ymd\THis');
                        $examCategory = "Fehlerhafter Termin";
                }
                $now = new DateTime();
                $updatedTime = $now->format('Ymd\THis');
            
                $this->addLine('BEGIN:VEVENT');
                $this->addLine('SUMMARY:'.$record->fau_unit.'-'.$record->exam_title);
                $this->addLine('UID:studon-'.$record->id.'seq'.$sequence);
                $this->addLine('SEQUENCE:0'); 
                $this->addLine('LOCATION:'.preg_replace("/\r|\n/", " ", $record->room)); // location needs to be 1 line
                $this->addLine('DTSTART;TZID="Europe/Berlin":'.$examStart);
                $this->addLine('DTEND;TZID="Europe/Berlin":'.$examEnd);
                $this->addLine('DTSTAMP;TZID="Europe/Berlin":'.$updatedTime);
                $this->addLine('CATEGORIES:'.$examCategory);
                $this->addLine('DESCRIPTION:Dozierender: '.$record->fau_lecturer.'\nE-Mail: '.$record->mail_address.'\nHiwis & Azubis: '.$record->team_students.'\nTeilnehmende: '.$record->num_participants.'\nSelbstregistrierungscode: '.$record->reg_code.'\nLink: '.$record->course_link);
                $this->addLine('END:VEVENT');
            }
        }catch(Exception $e)
        {}
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

    /**
     * Add timezone info to ics
     * @return
     */
    protected function addTimezone()
    {
        
        //  $this->writer->addLine('X-WR-TIMEZONE:' . $GLOBALS['DIC']['ilUser']->getTimeZone());
    
        include_once './Services/Calendar/classes/class.ilCalendarUtil.php';
        $tzid_file = ilCalendarUtil::getZoneInfoFile('Europe/Berlin');
        $reader = fopen($tzid_file, 'r');
        while ($line = fgets($reader)) {
            $line = str_replace("\r", '', $line);
            $line = str_replace("\n", '', $line);
            $this->addLine($line);
        }
    }
    
}

class ExamCalendarException extends Exception{}