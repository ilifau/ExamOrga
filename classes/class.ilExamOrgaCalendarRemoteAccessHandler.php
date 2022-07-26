<?php

/**
 * Remote access handler for ExamOrgaCalendar 
 *
 * @author Christina Fuchs <christina.fuchs@ili.fau.de>
 */
class ilExamOrgaCalendarRemoteAccessHandler{
    /** @var string */
    private $token;
    /** @var string */
    private $ref_id;  

    /**
     * Handle remote calendar request
     * @return
     */
    public function handleRequest()
    {    
        $this->ref_id = $_GET["ref_id"];
        $this->token = $_GET["token"];
        $object = new ilObjExamOrga($this->ref_id);
        if ($this->token != $object->plugin->getConfig()->get('calendar_api_token')) 
        {
            throw new ExamCalendarException("Unknown API Token");
        }

        require_once(__DIR__ . '/record/class.ilExamOrgaRecordCalendar.php');
        $export = new ilExamOrgaRecordCalendar();
        $export->exportToIcs($object);

        ilUtil::deliverData($export->getExportString(), 'calendar.ics', 'text/calendar', 'utf-8');
        exit;
    }
}
