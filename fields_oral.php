<?php
require_once (__DIR__ . '/classes/field/class.ilExamOrgaField.php');

/**
 * Definition of the record fields
 */
$fields = [
    [
        'name' => 'fau_unit',
        'type' => ilExamOrgaField::TYPE_SELECT,
        'title' => 'Fakultät',
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
      'title' => 'Lehrstuhl',
      'info' => 'Bitte die volle Lehrstuhl-Bezeichnung angeben',
      'limit' => 200,
      'default' => true,
      'filter' => true,
  ],
  [
      'name' => 'fau_lecturer',
      'type' => ilExamOrgaField::TYPE_TEXT,
      'title' => 'Dozent',
      'limit' => 200,
      'required' => true,
      'default' => true,
      'filter' => true
  ],
  [
      'name' => 'mail_address',
      'type' => ilExamOrgaField::TYPE_TEXT,
      'title' => 'Mail-Adresse',
      'info' => 'Bitte die E-Mail-Adresse des Hauptansprechpartners eintragen.',
      'limit' => 200
  ],
//  [
//      'name' => 'mail_title',
//      'type' => ilExamOrgaField::TYPE_TEXT,
//      'title' => 'Mail-Betreff',
//      'info' => 'Dieser Betreff wird für automatisch generierte E-Mails verwendet.',
//      'limit' => 200
//  ],
  [
      'name' => 'exam_format',
      'type' => ilExamOrgaField::TYPE_SELECT,
      'title' => 'Prüfungsformat',
      'options' => [
        'oral' => 'Mündliche Prüfung per Videokonferenz'
      ],
      'required' => true,
      'default' => true,
  ],
  [
      'name' => 'exam_title',
      'type' => ilExamOrgaField::TYPE_TEXT,
      'title' => 'Prüfungstitel',
      'default' => true,
      'filter' => true,
      'required' => true,
  ],
  [
      'name' => 'exam_date',
      'type' => ilExamOrgaField::TYPE_DATE,
      'title' => 'Datum',
      'required' => true,
      'default' => true,
      'filter' => true
  ],
  [
      'name' => 'alternative_dates',
      'type' => ilExamOrgaField::TYPE_TEXT,
      'title' => 'Alternativtermin',
  ],
  [
      'name' => 'exam_runs',
      'type' => ilExamOrgaField::TYPE_TIMES,
      'title' => 'Durchgänge',
      'info' => 'Startzeitpunkt der Prüfung bzw. Startzeitpunkte aller Durchgänge (wenn mehrere veranschlagt sind).',
      'required' => true
  ],
  [
      'name' => 'run_minutes',
      'type' => ilExamOrgaField::TYPE_INTEGER,
      'title' => 'Prüfungsdauer',
      'info' => 'Bitte geben Sie die reine Prüfungsdauer pro Durchgang in Minuten an.',
      'size' => 4,
      'required' => true
  ],
  [
      'name' => 'num_participants',
      'type' => ilExamOrgaField::TYPE_INTEGER,
      'title' => 'Teilnehmerzahl',
      'info' => 'Bitte geben Sie die Gesamtzahl der Teilnehmer/innen an. Zur Lastberechnung nimmt das System eine Gleichverteilung auf die Durchgänge an.',
      'size' => 4,
      'required' => true,
      'default' => true,
      'filter' => true,
  ],
  [
      'name' => 'monitors',
      'type' => ilExamOrgaField::TYPE_LOGINS,
      'title' => 'Aufsichten',
      'info' => 'Wählen Sie hier die StudOn-Accounts der Aufsichten aus. Sie erhalten automatisch Zugiff auf die ZOOM-Meetings zur Prüfung',
  ],
  [
      'name' => 'booking_approved',
      'type' => ilExamOrgaField::TYPE_CHECKBOX,
      'title' => 'Buchung abgeschlossen',
      'info' => 'Wird vom RRZE ausgefüllt',
      'status' => ilExamOrgaField::STATUS_HIDDEN
  ],
  /////////////////////////////////////////////////////////////
  [
      'name' => 'head_record',
      'type' => ilExamOrgaField::TYPE_HEADLINE,
      'title' => 'Angaben zu diesem Eintrag',
      'status' => ilExamOrgaField::STATUS_HIDDEN
  ],
  [
      'name' => 'id',
      'type' => ilExamOrgaField::TYPE_INTEGER,
      'title' => 'ID',
      'info' => 'Interne ID dieses Datensatzes. Hat keine Beziehung zur Prüfungsnummer.',
      'status' => ilExamOrgaField::STATUS_FIXED
  ],
  [
      'name' => 'created_at',
      'type' => ilExamOrgaField::TYPE_TIMESTAMP,
      'title' => 'Erstellt am',
      'status' => ilExamOrgaField::STATUS_FIXED
  ],
  [
      'name' => 'created_by',
      'type' => ilExamOrgaField::TYPE_USER_ID,
      'title' => 'Erstellt von',
      'status' => ilExamOrgaField::STATUS_FIXED
  ],
  [
      'name' => 'modified_at',
      'type' => ilExamOrgaField::TYPE_TIMESTAMP,
      'title' => 'Bearbeitet am',
      'status' => ilExamOrgaField::STATUS_FIXED
  ],
  [
      'name' => 'modified_by',
      'type' => ilExamOrgaField::TYPE_USER_ID,
      'title' => 'Bearbeitet von',
      'status' => ilExamOrgaField::STATUS_FIXED
  ],
  [
      'name' => 'owner_id',
      'type' => ilExamOrgaField::TYPE_USER_ID,
      'title' => 'Besitzer',
      'info' => 'Der Besitzer kann diesen Eintrag bearbeiten',
      'status' => ilExamOrgaField::STATUS_HIDDEN
  ],
];

return $fields;