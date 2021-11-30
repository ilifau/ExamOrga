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
        'options' => [
            ilExamOrgaMessage::TYPE_CONFIRM_PRESENCE => $plugin->txt('message_type_confirm_presence'),
            ilExamOrgaMessage::TYPE_CONFIRM_REMOTE => $plugin->txt('message_type_confirm_remote'),
            ilExamOrgaMessage::TYPE_REMINDER1_PRESENCE => $plugin->txt('message_type_reminder1_presence'),
            ilExamOrgaMessage::TYPE_REMINDER1_REMOTE => $plugin->txt('message_type_reminder1_remote'),
            ilExamOrgaMessage::TYPE_REMINDER2_PRESENCE => $plugin->txt('message_type_reminder2_presence'),
            ilExamOrgaMessage::TYPE_REMINDER2_REMOTE => $plugin->txt('message_type_reminder2_remote'),
            ilExamOrgaMessage::TYPE_WARNING_ZOOM => $plugin->txt('message_type_warning_zoom'),
            ilExamOrgaMessage::TYPE_WARNING_CAMPUS => $plugin->txt('message_type_warning_campus'),
            ilExamOrgaMessage::TYPE_WARNING_ROLES => $plugin->txt('message_type_warning_roles'),
            ilExamOrgaMessage::TYPE_WARNING_SCHEDULE => $plugin->txt('message_type_warning_schedule'),
        ],
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
