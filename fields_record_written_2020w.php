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
      'options' => [
          'Phil / Alte Welt und Asiatische Kulturen',
          'Phil / Anglistik/Amerikanistik und Romanistik',
          'Phil / Fachdidaktiken',
          'Phil / Germanistik und Komparatistik',
          'Phil / Geschichte',
          'Phil / Islamisch-Religiöse Studien (DIRS)',
          'Phil / Medienwissenschaften und Kunstgeschichte',
          'Phil / Pädagogik',
          'Phil / Psychologie',
          'Phil / Sozialwissenschaften und Philosophie',
          'Phil / Sportwissenschaft und Sport',
          'Phil / Theologie',
          'RW / Rechtsgeschichte',
          'RW / Strafrecht, Strafprozessrecht, Kriminologie',
          'RW / Deutsches und Internationales Privatrecht und Zivilverfahrensrecht',
          'RW / Wirtschafts- und Arbeitsrecht',
          'RW / Deutsches, Europäisches und Internationales Öffentliches Recht',
          'RW / Rechtsphilosophie und Allgemeine Staatslehre',
          'RW / Kirchenrecht',
          'RW / Recht und Technik',
          'RW / Anwaltsrecht und Anwaltspraxis',
          'RW / Arbeitsmarkt und Sozialökonomik (IAS)',
          'RW / Finance, Auditing, Controlling, Taxation (FACT)',
          'RW / Globalisierung und Internationale Unternehmensführung (IBUG)',
          'RW / Management (IFM)',
          'RW / Marketing (IFMA)',
          'RW / Wirtschaftsforschung (IWF)',
          'RW / Wirtschaftsinformatik (WIN)',
          'RW / Wirtschaftspädagogik (IWP)',
          'Med / Vorklinische Institute',
          'Med / klinisch-theoretische Institute',
          'Med / Klinische Einrichtungen',
          'Nat / Biologie',
          'Nat / Chemie und Pharmazie',
          'Nat / Geographie und Geowissenschaften',
          'Nat / Mathematik',
          'Nat / Physik',
          'Nat / Data Science (DDS)',
          'Tech / Artificial Intelligence in Biomedical Engineering (AIBE)',
          'Tech / Chemie- und Bioingenieurwesen (CBI)',
          'Tech / Elektrotechnik-Elektronik-Informationstechnik (EEI)',
          'Tech / Informatik (INF)',
          'Tech / Maschinenbau (MB)',
          'Tech / Werkstoffwissenschaften (WW)',
          'Zentrale / Sprachenzentrum'
      ],
      'default' => true,
      'filter' => true,
      'required' => true,
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
      'type' => ilExamOrgaField::TYPE_RADIO,
      'options' => [
          'presence' => $plugin->txt('exam_formats_presence'),
          'open' => $plugin->txt('exam_formats_open'),
          'monitored' => $plugin->txt('exam_formats_monitored'),
      ],
      'required' => true,
      'default' => true,
      'filter' => true
  ],

  [
      'name' => 'force_presence',
      'type' => ilExamOrgaField::TYPE_CHECKBOX,
      'status' => ilExamOrgaField::STATUS_LOCKED
  ],

  [
      'name' => 'exam_method',
      'type' => ilExamOrgaField::TYPE_RADIO,
      'options' => [
          'test' => $plugin->txt('exam_methods_test'),
          'exercise' => $plugin->txt('exam_methods_exercise')
      ],
      'default' => true,
      'filter' => true,
  ],
  [
      'name' => 'exam_type',
      'type' => ilExamOrgaField::TYPE_RADIO,
      'info' => '',
      'options' => [
          'exam' => $plugin->txt('exam_types_exam'),
          'review' => $plugin->txt('exam_types_review'),
          'retry' => $plugin->txt('exam_types_retry'),
          'sample' => $plugin->txt('exam_types_sample'),
      ],
      'default' => true,
      'filter' => true,
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
      'required' => false
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
      'name' => 'exam_ids',
      'type' => ilExamOrgaField::TYPE_EXAMS,
      'status' => ilExamOrgaField::STATUS_PUBLIC
  ],
  [
      'name' => 'admins',
      'type' => ilExamOrgaField::TYPE_LOGINS,
  ],
  [
      'name' => 'correctors',
      'type' => ilExamOrgaField::TYPE_LOGINS,
  ],
  [
      'name' => 'monitors',
      'type' => ilExamOrgaField::TYPE_LOGINS,
      'check_idm' => true
  ],
  [
      'name' => 'remarks',
      'type' => ilExamOrgaField::TYPE_TEXTAREA,
  ],
  [
      'name' => 'finally_approved',
      'type' => ilExamOrgaField::TYPE_CHECKBOX,
      'status' => ilExamOrgaField::STATUS_PUBLIC
  ],
  /////////////////////////////////////////////////////////
  [
      'name' => 'head_presence',
      'type' => ilExamOrgaField::TYPE_HEADLINE,
  ],
  [
      'name' => 'test_ref_id',
      'type' => ilExamOrgaField::TYPE_REFERENCE,
  ],
  [
      'name' => 'room',
      'type' => ilExamOrgaField::TYPE_TEXTAREA,
  ],
  [
      'name' => 'room_approved',
      'type' => ilExamOrgaField::TYPE_CHECKBOX,
      'status' => ilExamOrgaField::STATUS_HIDDEN
  ],
  [
      'name' => 'room_in_univis',
      'type' => ilExamOrgaField::TYPE_CHECKBOX,
      'status' => ilExamOrgaField::STATUS_HIDDEN
  ],
  //////////////////////////////////////////////////////////
  [
      'name' => 'head_process',
      'type' => ilExamOrgaField::TYPE_HEADLINE,
  ],
  [
      'name' => 'booking_in_process',
      'type' => ilExamOrgaField::TYPE_CHECKBOX,
      'status' => ilExamOrgaField::STATUS_LOCKED
  ],
  [
      'name' => 'booking_approved',
      'type' => ilExamOrgaField::TYPE_CHECKBOX,
      'status' => ilExamOrgaField::STATUS_LOCKED
  ],
  [
      'name' => 'reg_code',
      'type' => ilExamOrgaField::TYPE_TEXT,
      'status' => ilExamOrgaField::STATUS_LOCKED
  ],
  [
      'name' => 'course_link',
      'type' => ilExamOrgaField::TYPE_LINK,
      'status' => ilExamOrgaField::STATUS_LOCKED
  ],
  [
      'name' => 'run_links',
      'type' => ilExamOrgaField::TYPE_RUN_LINKS,
      'size' => 10,
      'status' => ilExamOrgaField::STATUS_FIXED
  ],
  [
      'name' => 'notes',
      'type' => ilExamOrgaField::TYPE_NOTES,
      'size' => 10,
      'status' => ilExamOrgaField::STATUS_FIXED,
      'filter' => true,
  ],

  //////////////////////////////////////////////////////////
  [
      'name' => 'head_internal',
      'type' => ilExamOrgaField::TYPE_HEADLINE,
      'status' => ilExamOrgaField::STATUS_HIDDEN
  ],
  [
      'name' => 'team_agent',
      'type' => ilExamOrgaField::TYPE_SELECT,
      'options' => [
          ' ' => 'Nicht gewählt',
        'Silvana',
        'Mona',
        'Steffi',
        'Stefie',
        'Inke'
      ],
      'status' => ilExamOrgaField::STATUS_HIDDEN
  ],
  [
      'name' => 'quality_checked',
      'type' => ilExamOrgaField::TYPE_CHECKBOX,
      'status' => ilExamOrgaField::STATUS_HIDDEN
  ],
  [
      'name' => 'team_students',
      'type' => ilExamOrgaField::TYPE_TEXTAREA,
      'status' => ilExamOrgaField::STATUS_HIDDEN
  ],
  [
      'name' => 'team_standby',
      'type' => ilExamOrgaField::TYPE_MULTISELECT,
      'options' => [
        'Inke (D)',
        'Mona (D)',
        'Silvana (D)',
        'Steffi (D)',
        'Stefie (D)',
        'Gerd (T)',
        'Fred (T)',
        'Silvana (T)'
      ],
      'status' => ilExamOrgaField::STATUS_HIDDEN
  ],
  [
      'name' => 'ips_active',
      'type' => ilExamOrgaField::TYPE_CHECKBOX,
      'status' => ilExamOrgaField::STATUS_HIDDEN
  ],
  [
      'name' => 'tech_details',
      'type' => ilExamOrgaField::TYPE_TEXTAREA,
      'status' => ilExamOrgaField::STATUS_HIDDEN
  ],
  [
      'name' => 'settings_checked',
      'type' => ilExamOrgaField::TYPE_CHECKBOX,
      'status' => ilExamOrgaField::STATUS_HIDDEN
  ],
  [
      'name' => 'seb_checked',
      'type' => ilExamOrgaField::TYPE_CHECKBOX,
      'status' => ilExamOrgaField::STATUS_HIDDEN
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