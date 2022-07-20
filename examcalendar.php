<?php
chdir('../../../../../../../');

//require_once('./Services/Context/classes/class.ilContext.php');
//ilContext::init(ilContext::CONTEXT_SOAP_NO_AUTH);

require_once("./Services/Init/classes/class.ilInitialisation.php");
ilInitialisation::initILIAS();

require_once (__DIR__ . '/classes/class.ilExamOrgaCalendarRemoteAccessHandler.php');
$cal_remote = new ilExamOrgaCalendarRemoteAccessHandler();

$cal_remote->handleRequest();

// Aufruf Beispiel
//http://localhost/studon7/Customizing/global/plugins/Services/Repository/RepositoryObject/ExamOrga/examcalendar.php?ref_id=145&token=lafjal873045803478jh
