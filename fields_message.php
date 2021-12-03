<?php
require_once (__DIR__ . '/classes/field/class.ilExamOrgaField.php');
$plugin = ilExamOrgaPlugin::getInstance();
/**
 * Definition of the record fields
 */
$fields = [
    [
        'name' => 'message_type',
        'type' => ilExamOrgaField::TYPE_SELECT,
        'options' => ilExamOrgaMessage::getOptions(),
        'required' => true,
        'default' => true,
        'status' => ilExamOrgaField::STATUS_FIXED
    ],
    [
        'name' => 'active',
        'type' => ilExamOrgaField::TYPE_CHECKBOX,
        'default' => true,
    ],
    [
        'name' => 'subject',
        'type' => ilExamOrgaField::TYPE_TEXT,
        'required' => true,
        'default' => true,
    ],
    [
        'name' => 'content',
        'type' => ilExamOrgaField::TYPE_TEXTAREA,
        'size' => 10,
        'required' => true,
        'default' => true,
    ],
];

return $fields;
