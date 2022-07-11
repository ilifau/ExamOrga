<?php
require_once (__DIR__ . '/classes/class.ilExamOrgaCalendarRemoteAccessHandler.php');

$cal_remote = new ilExamOrgaCalendarRemoteAccessHandler();

$cal_remote->parseRequest();
$cal_remote->handleRequest();

// Aufruf; Kalender in DB Tabelle cal_auth_token eingetragen. TODO: Mit DB Update Step eintragen?
//http://localhost/studon7/Customizing/global/plugins/Services/Repository/RepositoryObject/ExamOrga/examcalendar.php?client_id=myilias&token=0e9104816adcbbfa57ce10d49ea52fd9
