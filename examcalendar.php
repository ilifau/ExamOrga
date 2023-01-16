<?php
chdir('../../../../../../../');

require_once (__DIR__ . '/classes/class.ilExamOrgaCalendarRemoteAccessHandler.php');
$cal_remote = new ilExamOrgaCalendarRemoteAccessHandler();
$cal_remote->handleRequest();

// Aufruf Beispiel
//http://localhost/studon7/Customizing/global/plugins/Services/Repository/RepositoryObject/ExamOrga/examcalendar.php?ref_id=145&token=lafjal873045803478jh

// Verkürzt per Rewrite
// http://www.studon.fau.de/xamo/4717544/TZghUI78GKLCTbnIKT.ics