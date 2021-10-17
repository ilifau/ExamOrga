<?php
require_once (__DIR__ . '/classes/field/class.ilExamOrgaField.php');
$plugin = ilExamOrgaPlugin::getInstance();
/**
 * Definition of the record fields
 */
$fields = [
    [
        'name' => 'cond_type',
        'type' => ilExamOrgaField::TYPE_RADIO,
        'options' => [
            'require' => $plugin->txt('condition_cond_type_require'),
            'exclude' => $plugin->txt('condition_cond_type_exclude'),
        ],
        'required' => true,
        'default' => true
    ],
    [
        'name' => 'level',
        'type' => ilExamOrgaField::TYPE_RADIO,
        'options' => [
            'hard' => $plugin->txt('condition_level_option_hard'),
            'soft' => $plugin->txt('condition_level_option_soft'),
        ],
        'required' => true,
        'default' => true
    ],
    [
        'name' => 'failure_message',
        'type' => ilExamOrgaField::TYPE_TEXTAREA,
        'default' => true,
    ],
    [
        'name' => 'head_filter',
        'type' => ilExamOrgaField::TYPE_HEADLINE,
    ],
    [
        'name' => 'reg_min_date',
        'type' => ilExamOrgaField::TYPE_DATE,
        'default' => true,
    ],
    [
        'name' => 'reg_max_date',
        'type' => ilExamOrgaField::TYPE_DATE,
        'default' => true,
    ],
    [
        'name' => 'exam_formats',
        'type' => ilExamOrgaField::TYPE_MULTISELECT,
        'options' => [
            'presence' => $plugin->txt('exam_formats_presence'),
            'open' => $plugin->txt('exam_formats_open'),
            'monitored' => $plugin->txt('exam_formats_monitored'),
        ],
        'default' => true
    ],
    [
        'name' => 'exam_from_date',
        'type' => ilExamOrgaField::TYPE_DATE,
        'default' => true,
    ],
    [
        'name' => 'exam_to_date',
        'type' => ilExamOrgaField::TYPE_DATE,
        'default' => true,
    ],
    [
        'name' => 'head_conditions',
        'type' => ilExamOrgaField::TYPE_HEADLINE,
    ],
    [
        'name' => 'reg_min_days_before',
        'type' => ilExamOrgaField::TYPE_INTEGER,
        'default' => true,
    ],
    [
      'name' => 'exam_types',
      'type' => ilExamOrgaField::TYPE_MULTISELECT,
      'options' => [
          'exam' => $plugin->txt('exam_types_exam'),
          'review' => $plugin->txt('exam_types_review'),
          'retry' => $plugin->txt('exam_types_retry'),
          'sample' => $plugin->txt('exam_types_sample'),
      ],
      'default' => true,
    ],
    [
      'name' => 'exam_min_date',
      'type' => ilExamOrgaField::TYPE_DATE,
      'default' => true,
    ],
    [
      'name' => 'exam_max_date',
      'type' => ilExamOrgaField::TYPE_DATE,
      'default' => true,
    ],
    [
        'name' => 'weekdays',
        'type' => ilExamOrgaField::TYPE_RADIO,
        'options' => [
            '' => $plugin->txt('condition_weekdays_any'),
            'Mo-Fr' => $plugin->txt('condition_weekdays_mo_fr'),
            'Mo-Sa' => $plugin->txt('condition_weekdays_mo_sa'),
        ],
        'default' => true
    ],
    [
        'name' => 'min_daytime',
        'type' => ilExamOrgaField::TYPE_SELECT,
        'options' => ['','06:00','06:30','07:00','07:30','08:00','08:30','09:00','09:30','10:00','10:30','11:00', '11:30', '12:00'],
        'default' => true
    ],
    [
        'name' => 'max_daytime',
        'type' => ilExamOrgaField::TYPE_SELECT,
        'options' => ['','15:00','15:30','16:00','16:30','17:00','17:30','18:00','18:30','19:00','19:30','20:00', '20:30', '21:00'],
        'default' => true
    ],

    //    [
//        'name' => 'max_exams_per_day',
//        'type' => ilExamOrgaField::TYPE_INTEGER,
//        'title' => 'Pro Tag',
//        'info' => 'Maximale Anzahl an Prüfungen pro Tag',
//        'default' => true,
//    ],
//    [
//        'name' => 'max_exams_per_week',
//        'type' => ilExamOrgaField::TYPE_INTEGER,
//        'title' => 'Pro Woche',
//        'info' => 'Maximale Anzahl an Prüfungen pro Woche',
//        'default' => true,
//    ],
//    [
//        'name' => 'max_exams_per_month',
//        'type' => ilExamOrgaField::TYPE_INTEGER,
//        'title' => 'Pro Monat',
//        'info' => 'Maximale Anzahl an Prüfungen pro Monat',
//        'default' => true,
//    ],
];

return $fields;