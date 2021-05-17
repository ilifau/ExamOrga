<?php
require_once (__DIR__ . '/classes/field/class.ilExamOrgaField.php');
$plugin = ilExamOrgaPlugin::getInstance();

/**
 * Definition of the record fields
 */
$fields = [
    [
        'name' => 'fau_unit',
        'type' => ilExamOrgaField::TYPE_SELECT,
        'title' => $plugin->txt('record_oral_fau_unit'),
        'info' => '',
        'options' => [
            'Phil',
            'RW',
            'Med',
            'Nat',
            'Tech',
            'Zentral'
        ],
        'default' => true,
        'required' => true,
        'filter' => true,
    ],
  [
      'name' => 'fau_chair',
      'type' => ilExamOrgaField::TYPE_TEXT,
      'limit' => 200,
      'default' => true,
      'filter' => true,
  ],
  [
      'name' => 'fau_lecturer',
      'type' => ilExamOrgaField::TYPE_TEXT,
      'limit' => 200,
      'required' => true,
      'default' => true,
      'filter' => true
  ],
  [
      'name' => 'mail_address',
      'type' => ilExamOrgaField::TYPE_TEXT,
      'limit' => 200,
      'required' => true,
  ],
  [
      'name' => 'exam_format',
      'type' => ilExamOrgaField::TYPE_SELECT,
      'info' => '',
      'options' => [
        'oral' => $plugin->txt('exam_formats_oral')
      ],
      'required' => true,
      'default' => true,
  ],
  [
      'name' => 'exam_title',
      'type' => ilExamOrgaField::TYPE_TEXT,
      'default' => true,
      'filter' => true,
      'required' => true,
  ],
  [
      'name' => 'exam_date',
      'type' => ilExamOrgaField::TYPE_DATE,
      'required' => true,
      'default' => true,
      'filter' => true
  ],
  [
      'name' => 'alternative_dates',
      'type' => ilExamOrgaField::TYPE_TEXT,
  ],
    [
        'name' => 'head_organisation',
        'type' => ilExamOrgaField::TYPE_HEADLINE,
    ],
    [
      'name' => 'exam_runs',
      'type' => ilExamOrgaField::TYPE_TIMES,
      'required' => true
  ],
  [
      'name' => 'run_minutes',
      'type' => ilExamOrgaField::TYPE_INTEGER,
      'size' => 4,
      'required' => true
  ],
  [
      'name' => 'num_participants',
      'type' => ilExamOrgaField::TYPE_INTEGER,
      'size' => 4,
      'required' => true,
      'default' => true,
      'filter' => true,
  ],
    [
        'name' => 'monitors',
        'type' => ilExamOrgaField::TYPE_LOGINS,
        'check_idm' => true
    ],
    [
        'name' => 'head_process',
        'type' => ilExamOrgaField::TYPE_HEADLINE,
        'info' => $plugin->txt('record_oral_head_process_info'),
    ],
  [
      'name' => 'run_links',
      'type' => ilExamOrgaField::TYPE_RUN_LINKS,
      'size' => 10,
      'status' => ilExamOrgaField::STATUS_FIXED
   ],

    /////////////////////////////////////////////////////////////
  [
      'name' => 'head_record',
      'type' => ilExamOrgaField::TYPE_HEADLINE,
      'status' => ilExamOrgaField::STATUS_FIXED
  ],
  [
      'name' => 'id',
      'type' => ilExamOrgaField::TYPE_INTEGER,
      'status' => ilExamOrgaField::STATUS_FIXED
  ],
  [
      'name' => 'created_at',
      'type' => ilExamOrgaField::TYPE_TIMESTAMP,
      'status' => ilExamOrgaField::STATUS_FIXED
  ],
  [
      'name' => 'created_by',
      'type' => ilExamOrgaField::TYPE_USER_ID,
      'status' => ilExamOrgaField::STATUS_FIXED
  ],
  [
      'name' => 'modified_at',
      'type' => ilExamOrgaField::TYPE_TIMESTAMP,
      'status' => ilExamOrgaField::STATUS_FIXED
  ],
  [
      'name' => 'modified_by',
      'type' => ilExamOrgaField::TYPE_USER_ID,
      'status' => ilExamOrgaField::STATUS_FIXED
  ],
  [
      'name' => 'owner_id',
      'type' => ilExamOrgaField::TYPE_USER_ID,
      'status' => ilExamOrgaField::STATUS_LOCKED
  ],
];

return $fields;