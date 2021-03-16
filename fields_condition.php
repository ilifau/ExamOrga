<?php
require_once (__DIR__ . '/classes/field/class.ilExamOrgaField.php');

/**
 * Definition of the record fields
 */
$fields = [
    [
        'name' => 'level',
        'type' => ilExamOrgaField::TYPE_RADIO,
        'title' => 'Level',
        'info' => 'Erlaubte Prüfungsformate',
        'options' => [
            'hard' => 'Verhindert Speicherung für alle',
            'soft' => 'Verhindert Erstellung für Nutzer, erlaubt Erstellung für Admins, danach Bearbeitung durch Nutzer mit Warnung',
        ],
        'required' => true,
        'default' => true
    ],
    [
        'name' => 'failure_message',
        'type' => ilExamOrgaField::TYPE_TEXTAREA,
        'title' => 'Meldung',
        'info' => 'Meldung, welche die Bedingung verständlich wiedergibt.',
        'default' => true,
    ],
    [
        'name' => 'head_filter',
        'type' => ilExamOrgaField::TYPE_HEADLINE,
        'title' => 'Anwendung',
        'info' => 'Stellen Sie hier ein, für welche Einträge diese Bedingung geprüft wird.',
    ],
    [
        'name' => 'reg_min_date',
        'type' => ilExamOrgaField::TYPE_DATE,
        'title' => 'Gültig ab',
        'info' => 'Erstellungs- oder Bearbeitungsdatum, ab dem die Bedingung gültig ist',
        'default' => true,
    ],
    [
        'name' => 'reg_max_date',
        'type' => ilExamOrgaField::TYPE_DATE,
        'title' => 'Gültig bis',
        'info' => 'Erstellungs- oder Bearbeitungsdatum, bis zu dem dem die Bedingung gültig ist',
        'default' => true,
    ],
    [
        'name' => 'exam_formats',
        'type' => ilExamOrgaField::TYPE_MULTISELECT,
        'title' => 'Für Formate',
        'info' => 'Prüfungsformate mit dieser Bedingung',
        'options' => [
            'presence' => 'E-Prüfung in Präsenz',
            'open' => 'Open-Book-Prüfung mit Zeitbegrenzung',
            'monitored' => 'Fernklausur mit Videoaufsicht',
        ],
        'default' => true
    ],
    [
        'name' => 'head_condition',
        'type' => ilExamOrgaField::TYPE_HEADLINE,
        'title' => 'Anforderung',
        'info' => 'Stellen Sie hier ein, welche Anforderungen ein Eintrag erfüllen muss.',
    ],
    [
        'name' => 'reg_min_days_before',
        'type' => ilExamOrgaField::TYPE_INTEGER,
        'title' => 'Vorlauf',
        'info' => 'Minumum an Tagen zwischen Bearbeitung des Eintrags und dem Prüfungsdatum',
        'default' => true,
    ],
    [
      'name' => 'exam_types',
      'type' => ilExamOrgaField::TYPE_MULTISELECT,
      'title' => 'Typen',
      'info' => 'Erlaubte Prüfungstypen',
      'options' => [
          'exam' => 'Klausur',
          'review' => 'Einsichtnahme',
          'retry' => 'Nachholklausur',
          'sample' => 'Probeklausur'
      ],
      'default' => true,
    ],
    [
      'name' => 'exam_min_date',
      'type' => ilExamOrgaField::TYPE_DATE,
      'title' => 'Früheste Prüfung',
      'default' => true,
    ],
    [
      'name' => 'exam_max_date',
      'type' => ilExamOrgaField::TYPE_DATE,
      'title' => 'Späteste Prüfung',
      'default' => true,
    ],
    [
        'name' => 'weekdays',
        'type' => ilExamOrgaField::TYPE_RADIO,
        'title' => 'Wochentage',
        'info' => 'Erlaubte Wochentage als Prüfungstermin',
        'options' => [
            '' => 'beliebig',
            'Mo-Fr' => 'Montag bis Freitag',
            'Mo-Sa' => 'Montag bis Samstag',
        ],
        'default' => true
    ],
    [
        'name' => 'min_daytime',
        'type' => ilExamOrgaField::TYPE_SELECT,
        'title' => 'Frühester Start',
        'info' => 'Frühester Durchgangs-Start am Prüfungstag',
        'options' => ['','06:00','06:30','07:00','07:30','08:00','08:30','09:00','09:30','10:00','10:30','11:00', '11:30', '12:00'],
        'default' => true
    ],
    [
        'name' => 'max_daytime',
        'type' => ilExamOrgaField::TYPE_SELECT,
        'title' => 'Spätestes Ende',
        'info' => 'Spätestes Durchgang-Ende am Prüfungstag',
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