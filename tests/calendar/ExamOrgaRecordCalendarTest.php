<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class ExamOrgaRecordCalendarTest extends TestCase
{
    public function testCanCreateIcsStringEasy(): void
    {
        $eorCalendarObj = new ilExamOrgaRecordCalendar();
        $examOrgaObj = null;
        $examOrgaPlugin = null;
        $records = array();
        $ical = '';

        $records[0] = $this->getMockBuilder(ilExamOrgaRecord::class);

        $records[0]->exam_format = 'open';
        $records[0]->exam_date = "2022-07-01";
        $records[0]->exam_runs = "07:20";
        $records[0]->run_minutes = 30;
        $records[0]->fau_unit = "Med / Klinische Einrichtungen";
        $records[0]->exam_title = "Kursus der XXX und YYY II Klausur 2";
        $records[0]->id = 2645;
        $records[0]->room = "";
        $records[0]->fau_lecturer = "Prof. Dr. M. ZZZ";
        $records[0]->mail_address = "xxx@yyyy.yyy-yyyyyy";
        $records[0]->team_students = "";
        $records[0]->num_participants = 60;
        $records[0]->reg_code = "yyyyy";
        $records[0]->course_link = "https://test.fau.de/meinkurslink";
        
        $eorCalendarObj->exportEventToIcs($records[0]);

        // remove DTSTAMP line before asserting that strings are equal
        $correctICS = file_get_contents("tests/calendar/calendartest1.ics");
        $correctICS = preg_replace('^DTSTAMP(.)*\\r\\n^', '', $correctICS);
        $actualICS = $eorCalendarObj->getExportString();
        $actualICS = preg_replace('^DTSTAMP(.)*\\r\\n^', '', $actualICS);
        //var_dump($actualICS);
        $this->assertEquals($correctICS, $actualICS);  
    }
}
