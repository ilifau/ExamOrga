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
    /** @var string */
    private $client;   

    /**
     * Handle Request
     * @return
     */
    public function handleRequest()
    {    
        $this->client = $_GET["client_id"]; // TODO: wird der Client benötigt?
        $this->ref_id = $_GET["ref_id"];
        $this->token = $_GET["token"];
        $object = new ilObjExamOrga($this->ref_id);
        if ($this->token != $object->plugin->getConfig()->get('calendar_api_token')) 
        {
            return false;
        }

        require_once(__DIR__ . '/record/class.ilExamOrgaRecordCalendar.php');
        $export = new ilExamOrgaRecordCalendar();
        $export->exportToIcs($object);

        ilUtil::deliverData($export->getExportString(), 'calendar.ics', 'text/calendar', 'utf-8');
        exit;
    }
}
