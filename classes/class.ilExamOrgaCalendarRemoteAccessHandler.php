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
     * @see \ilCalendarRemoteAccessHandler::initIlias
     */
    protected function initIlias()
    {
        include_once "Services/Context/classes/class.ilContext.php";
        ilContext::init(ilContext::CONTEXT_ICAL);

        include_once './Services/Authentication/classes/class.ilAuthFactory.php';
        ilAuthFactory::setContext(ilAuthFactory::CONTEXT_CALENDAR_TOKEN);

        require_once("Services/Init/classes/class.ilInitialisation.php");
        ilInitialisation::initILIAS();

        $GLOBALS['DIC']['lng']->loadLanguageModule('dateplaner');
    }

    /**
     * Handle remote calendar request
     * @see \ilCalendarRemoteAccessHandler::handleRequest
     */
    public function handleRequest()
    {
        session_name('ILCALSESSID');
        $this->initIlias();

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
