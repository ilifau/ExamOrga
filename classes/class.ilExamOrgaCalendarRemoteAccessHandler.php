<?php
chdir('../../../../../../../');
include_once("./Services/Calendar/classes/class.ilCalendarRemoteAccessHandler.php");

/**
 * Remote access handler for ExamOrgaCalendar 
 *
 * @author Christina Fuchs <christina.fuchs@ili.fau.de>
 */
class ilExamOrgaCalendarRemoteAccessHandler extends ilCalendarRemoteAccessHandler
{
    /**
     * Handle Request
     * @return
     */
    public function handleRequest()
    {
        session_name('ILCALSESSID');
        $this->initIlias();
        $logger = $GLOBALS['DIC']->logger()->cal();
        $this->initTokenHandler();

        if (!$this->initUser()) {
            $logger->warning('Calendar token is invalid. Authentication failed.');
            return false;
        }

        if ($this->getTokenHandler()->getIcal() and !$this->getTokenHandler()->isIcalExpired()) {
            $GLOBALS['DIC']['ilAuthSession']->logout();
            ilUtil::deliverData($this->getTokenHandler(), 'calendar.ics', 'text/calendar', 'utf-8');
            exit;
        }

        include_once './Services/Calendar/classes/Export/class.ilCalendarExport.php';
        include_once './Services/Calendar/classes/class.ilCalendarCategories.php';
        /*       if ($this->getTokenHandler()->getSelectionType() == ilCalendarAuthenticationToken::SELECTION_CALENDAR) {
            #$export = new ilCalendarExport(array($this->getTokenHandler()->getCalendar()));
            $cats = ilCalendarCategories::_getInstance();
            $cats->initialize(ilCalendarCategories::MODE_REMOTE_SELECTED, $this->getTokenHandler()->getCalendar());
            $export = new ilCalendarExport($cats->getCategories(true));
        } else {
            $cats = ilCalendarCategories::_getInstance();
            $cats->initialize(ilCalendarCategories::MODE_REMOTE_ACCESS);
            $export = new ilCalendarExport($cats->getCategories(true));
        }
 */

        require_once(__DIR__ . '/record/class.ilExamOrgaRecordCalendar.php');
        $export = new ilExamOrgaRecordCalendar();
        $object = new ilObjExamOrga(145); // TODO - get ref_id of Exam Orga Object
        $export->exportToIcs($object);

        $this->getTokenHandler()->setIcal($export->getExportString());
        $this->getTokenHandler()->storeIcal();

        $GLOBALS['DIC']['ilAuthSession']->logout();

        ilUtil::deliverData($export->getExportString(), 'calendar.ics', 'text/calendar', 'utf-8');
        exit;
    }
}
