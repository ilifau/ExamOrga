<?php
// fau: videoPortal - entry script for video portal REST service

chdir('../../../../../../../');

// we need access handling
require_once('./Services/Context/classes/class.ilContext.php');
ilContext::init(ilContext::CONTEXT_RSS);

require_once("./Services/Init/classes/class.ilInitialisation.php");
ilInitialisation::initILIAS();

require_once(__DIR__ . '/classes/class.ilExamOrgaServer.php');
$server = new ilExamOrgaServer(
    [
        'settings' => [
            'displayErrorDetails' => DEVMODE ? true : false
        ]
    ]
);
$server->init();
$server->run();
