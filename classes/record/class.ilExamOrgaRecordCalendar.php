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
        try{
                $this->init($object);
                $records = $this->records;

                $this->addLine('BEGIN:VCALENDAR');
                $this->addLine('VERSION:2.0');
                $this->addLine('METHOD:PUBLISH');
                $this->addLine('PRODID:-//ilias.de/NONSGML ILIAS Calendar V4.4//EN');
                //  $this->addTimezone(); TODO
            
                foreach($records as $record)
                {
                    try
                    {
                        // calculate start and end time; TODO: implement more than 1 runs
                        $start = new DateTime($record->exam_date);
                        $end = new DateTime($record->exam_date);
                        if(!isset($record->exam_runs)||empty($record->exam_runs)) 
                            throw new ExamCalendarException();
                        $runStart = explode(',', $record->exam_runs);
                        if(sizeof($runStart) != 1) 
                            throw new ExamCalendarException();
                        else $runStart = explode(':', $record->exam_runs);

                        $start->modify('+'.($runStart[0]*60+$runStart[1]).' minutes');
                        $end->modify('+'.($runStart[0]*60+$runStart[1]+$record->run_minutes).' minutes'); 
                        $examStart =  $start->format('Ymd\THis');
                        $examEnd =  $end->format('Ymd\THis');
                        $now = new DateTime();
                        $updatedTime = $now->format('Ymd\THis');
                    }catch(ExamCalendarException $e){
                        // daily event if runs not correct
                        $examStart =  $start->format('Ymd\THis');
                        $start->modify("+24 hours");
                        $examEnd =  $start->format('Ymd\THis');
                    }
                    $now = new DateTime();
                    $updatedTime = $now->format('Ymd\THis');
                    // TODO: implement category 
                    // TODO: implement description
                   
                    $this->addLine('BEGIN:VEVENT');
                    $this->addLine('SUMMARY:'.$record->fau_unit.'-'.$record->exam_title);
                    $this->addLine('UID:studon-'.$record->obj_id);
                    $this->addLine('SEQUENCE:0'); // TODO: implement more than 1 runs
                    $this->addLine('LOCATION:'.$record->room);
                    $this->addLine('DTSTART;Europe/Berlin:'.$examStart);
                    $this->addLine('DTEND;Europe/Berlin:'.$examEnd);
                    $this->addLine('DTSTAMP;Europe/Berlin:'.$updatedTime);
                    $this->addLine('CATEGORIES:Gelbe Kategorie');
                    $this->addLine('DESCRIPTION:Dozierender: Max Mustermann2\nE-Mail: Max.Mustermann@fautest.de\nHiwis & Azubis:\nTeilnehmende: 10\nSelbstregistrieungscode: fgRtz%69\nLink: https://studontest.fautest.de');
                    $this->addLine('END:VEVENT');
            }

            $this->addLine('END:VCALENDAR');
    }catch(Exception $e)
    {
      echo $e->getMessage();  
    }
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

class ExamCalendarException extends Exception{}