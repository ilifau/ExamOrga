<?php
require_once (__DIR__ . '/classes/field/class.ilExamOrgaField.php');

/**
 * Definition of the record fields
 */
$fields = [
  [
      'name' => 'fau_unit',
      'type' => ilExamOrgaField::TYPE_SELECT,
      'title' => 'Bereich',
      'info' => 'Wählen Sie hier den Bereich, in dem Ihre Prüfung auf der (Fern-)Prüfungsplattform angelegt werden soll. Die Bereiche orientieren sich an den Fakultäten und Ihrer Untergliederung. Damit sollen zu lange Listen von Prüfungen auf einer Ebene vermieden werden.',
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
          'Tech / Artificial Intelligence in Biomedical Engineering (AIBE)',
          'Tech / Chemie- und Bioingenieurwesen (CBI)',
          'Tech / Elektrotechnik-Elektronik-Informationstechnik (EEI)',
          'Tech / Informatik (INF)',
          'Tech / Maschinenbau (MB)',
          'Tech / Werkstoffwissenschaften (WW)',
          'Zentrale / Spachenzentrum'
      ],
      'default' => true
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
  [
      'name' => 'mail_title',
      'type' => ilExamOrgaField::TYPE_TEXT,
      'title' => 'Mail-Betreff',
      'info' => 'Dieser Betreff wird für automatisch generierte E-Mails verwendet.',
      'limit' => 200
  ],
  [
      'name' => 'exam_format',
      'type' => ilExamOrgaField::TYPE_RADIO,
      'title' => 'Prüfungsformat',
      'info' => 'Geben Sie hier an, welches Format Sie durchführen möchten. Wenn Sie unsicher sind kontaktieren Sie uns gerne.',
      'options' => [
        'presence' => 'E-Prüfung in Präsenz',
        'open' => 'Open-Book-Prüfung mit Zeitbegrenzung',
        'monitored' => 'Fernklausur mit Videoaufsicht',
        'oral' => 'Mündliche Prüfung per Videokonferenz'
      ],
      'required' => true,
      'default' => true,
      'filter' => true
  ],
  [
      'name' => 'exam_method',
      'type' => ilExamOrgaField::TYPE_RADIO,
      'title' => 'Prüfungsmethode',
      'info' => '[Nur bei Fernprüfungen] Geben Sie bitte an, ob Sie Ihre Prüfung mit dem Objekt Übung (Vorrangig für Essayfragen I Studierende laden von der Prüfungsplattform eine Aufgabenstellung herunter, bearbeiten diese offline und laden sie anschließend wieder in das Übungsobjekt auf der Prüfungsplattform hoch) oder mit dem Objekt Test (Vorrangig für Antwort-Wahl-Verfahren I Studierende bearbeiten alle Aufgaben im Test auf der Prüfungsplattform) umsetzen möchten.',
      'options' => [
          'test' => 'Test (kontinuierliche Bearbeitung auf der Prüfungsplattform)',
          'exercise' => 'Übung (Offline-Bearbeitung mit anschließendem Upload)'
      ],
      'default' => true,
      'filter' => true,
  ],
  [
      'name' => 'exam_type',
      'type' => ilExamOrgaField::TYPE_RADIO,
      'title' => 'Typ',
      'info' => 'Bitte immer korrekt auswählen!',
      'options' => [
          'exam' => 'Klausur',
          'review' => 'Einsichtnahme',
          'retry' => 'Nachholklausur'
      ],
      'default' => true,
      'filter' => true,
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
      'name' => 'exam_ids',
      'type' => ilExamOrgaField::TYPE_EXAMS,
      'title' => 'Prüfungen in mein campus',
      'info' => 'Geben Sie hier die "ID für Organisation elektronischer Prüfungen" die Ihnen in <em>mein campus</em> im Bereich "Prüfungsorganisation" angezeigt wird an. Mit diese Angabe können wir die Prüfungs-Teilnehmer automatisch aus <em>mein campus</em> abfragen und die richtigen Dateien zur Ergebnisverbuchung generieren.',
      'status' => ilExamOrgaField::STATUS_HIDDEN
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
      'info' => 'Startzeitpunkt der Prüfung bzw. Startzeitpunkte aller Durchgänge (wenn mehrere veranschlagt sind). Bei Fernprüfungen rechnen Sie bitte zwischen den Durchgängen eine Pause von 20 Minuten ein. Bei Präsenzprüfungen rechnen Sie bitte zwischen den Durchgängen eine Pause von 60 Minuten ein.',
      'required' => true
  ],
  [
      'name' => 'run_minutes',
      'type' => ilExamOrgaField::TYPE_INTEGER,
      'title' => 'Prüfungsdauer',
      'info' => 'Bitte geben Sie die reine Prüfungsdauer pro Durchgang in Minuten an. Bei Fernprüfungen Angabe bitte inkl. Pufferzeit von 20 Minuten für Upload und technische Verzögerungen.',
      'size' => 4,
      'required' => true
  ],
  [
      'name' => 'num_participants',
      'type' => ilExamOrgaField::TYPE_INTEGER,
      'title' => 'Teilnehmerzahl',
      'info' => 'Bitte geben Sie die reine Prüfungsdauer pro Durchgang in Minuten an. Bei Fernprüfungen Angabe bitte inkl. Pufferzeit von 20 Minuten für Upload und technische Verzögerungen.',
      'size' => 4,
      'required' => true,
      'default' => true,
      'filter' => true,
  ],
  [
      'name' => 'admins',
      'type' => ilExamOrgaField::TYPE_LOGINS,
      'title' => 'Korektoren-Accounts',
      'info' => 'Wählen Sie hier die StudOn-Accounts der Korrektoren aus. Sie erhalten automatisch Zugiff auf den Kurs in der Prüfungsplattform.',
  ],
  [
      'name' => 'monitors',
      'type' => ilExamOrgaField::TYPE_LOGINS,
      'title' => 'Aufsichten',
      'info' => 'Wählen Sie hier die StudOn-Accounts der Korrektoren aus. Sie erhalten automatisch Zugiff auf die ZOOM-Meetings zur Prüfung',
  ],
  [
      'name' => 'remarks',
      'type' => ilExamOrgaField::TYPE_TEXTAREA,
      'title' => 'Anmerkungen',
      'info' => 'Beschreiben Sie hier kurz eventuelle Besonderheiten zur Klausur (z.B. Einsatz von Video, Audio, Taschenrechner, Zufalls-Test, verschiedene Tests, Weiterleitung zur Umfrage etc.) ',
  ],
  /////////////////////////////////////////////////////////
  [
      'name' => 'head_presence',
      'type' => ilExamOrgaField::TYPE_HEADLINE,
      'title' => 'Präsenzprüfung',
      'info' => 'Die folgenden Angaben sind nur für E-Prüfungen in Präsenz relevant',
  ],
  [
      'name' => 'test_ref_id',
      'type' => ilExamOrgaField::TYPE_REFERENCE,
      'title' => 'Testobjekt in StudOn',
      'info' => 'Nur für E-Prüfungen in Präsenz relevant. Hier können Sie Ihren Test aus StudOn verlinken, sobald er fertiggestellt ist.',
  ],
  [
      'name' => 'room',
      'type' => ilExamOrgaField::TYPE_TEXTAREA,
      'title' => 'Raum',
      'info' => 'Nur für E-Prüfungen in Präsenz relevant: Hier dürfen Sie eine Raum-Präferenz angeben und finden nach Abschluss des Buchungsprozesses die für Sie verbindlich gebuchten Räume. Vermerken Sie bitte in Klammern, wenn Sie den Raum bereits selbst gebucht haben. ',
  ],
  [
      'name' => 'room_approved',
      'type' => ilExamOrgaField::TYPE_CHECKBOX,
      'title' => 'Raumbuchung bestätigt',
      'info' => 'Für Exam-Team',
      'status' => ilExamOrgaField::STATUS_HIDDEN
  ],
  [
      'name' => 'room_in_univis',
      'type' => ilExamOrgaField::TYPE_CHECKBOX,
      'title' => 'UnivIS Eintrag',
      'info' => 'Für Exam-Team',
      'status' => ilExamOrgaField::STATUS_HIDDEN
  ],
  //////////////////////////////////////////////////////////
  [
      'name' => 'head_process',
      'type' => ilExamOrgaField::TYPE_HEADLINE,
      'title' => 'Status',
      'info' => 'Die folgenden Angaben werden vom Exam-Team ausgefüllt',
  ],
  [
      'name' => 'booking_in_process',
      'type' => ilExamOrgaField::TYPE_CHECKBOX,
      'title' => 'In Bearbeitung',
      'info' => 'Wird vom Exam-Team ausgefüllt. Ihre Anfrage ist bei uns eingegangen und wird von uns bearbeitet.',
      'status' => ilExamOrgaField::STATUS_LOCKED
  ],
  [
      'name' => 'booking_approved',
      'type' => ilExamOrgaField::TYPE_CHECKBOX,
      'title' => 'Buchung abgeschlossen',
      'info' => 'Wird vom Exam-Team ausgefüllt. Sie können die Daten Ihres verbindlich gebuchten Prüfungstermins einsehen.',
      'status' => ilExamOrgaField::STATUS_LOCKED
  ],
  [
      'name' => 'finally_approved',
      'type' => ilExamOrgaField::TYPE_CHECKBOX,
      'title' => 'Finale Abnahme',
      'info' => 'Hiermit bestätigen Sie, dass Sie Ihre Prüfung auf die Prüfungsplattform übertragen haben, die Teilnehmer importiert sind, final alle Einstellungen überprüft wurden und keine Änderungen mehr vorgenommen werden. Die finale Abnahme bezieht sich auf den letzten Check ihrer Prüfung auf der Prüfungsplattform und ist erst wenige Tage vor Prüfungstermin relevant.',
      'status' => ilExamOrgaField::STATUS_LOCKED
  ],
  [
      'name' => 'reg_code',
      'type' => ilExamOrgaField::TYPE_TEXT,
      'title' => 'Registrierungscode',
      'info' => 'Wird vom StudOn-Exam Team eingetragen, sobald dieser bereit steht',
      'status' => ilExamOrgaField::STATUS_LOCKED
  ],

  //////////////////////////////////////////////////////////
  [
      'name' => 'head_internal',
      'type' => ilExamOrgaField::TYPE_HEADLINE,
      'title' => 'Intern',
      'info' => 'Die folgenden Angaben sind nur für das Exam-Team sichtbar',
      'status' => ilExamOrgaField::STATUS_HIDDEN
  ],
  [
      'name' => 'team_agent',
      'type' => ilExamOrgaField::TYPE_SELECT,
      'title' => 'Agentin',
      'options' => [
        'Silvana',
        'Mona',
        'Steffi',
        'Inke'
      ],
      'status' => ilExamOrgaField::STATUS_HIDDEN
  ],
  [
      'name' => 'quality_checked',
      'type' => ilExamOrgaField::TYPE_CHECKBOX,
      'title' => 'Qualitätscheck',
      'status' => ilExamOrgaField::STATUS_HIDDEN
  ],
  [
      'name' => 'team_students',
      'type' => ilExamOrgaField::TYPE_TEXTAREA,
      'title' => 'ILI-Hiwi',
      'status' => ilExamOrgaField::STATUS_HIDDEN
  ],
  [
      'name' => 'team_standby',
      'type' => ilExamOrgaField::TYPE_MULTISELECT,
      'title' => 'Bereitschaft',
      'options' => [
        'Mona (D)',
        'Silvana (D)',
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
      'title' => 'IP Freigabe',
      'status' => ilExamOrgaField::STATUS_HIDDEN
  ],
  [
      'name' => 'tech_details',
      'type' => ilExamOrgaField::TYPE_TEXTAREA,
      'title' => 'Techn. Details',
      'status' => ilExamOrgaField::STATUS_HIDDEN
  ],
  [
      'name' => 'settings_checked',
      'type' => ilExamOrgaField::TYPE_CHECKBOX,
      'title' => 'Exam-Check 1 Einstellungen',
      'status' => ilExamOrgaField::STATUS_HIDDEN
  ],
  [
      'name' => 'seb_checked',
      'type' => ilExamOrgaField::TYPE_CHECKBOX,
      'title' => 'Exam-Check 2 SEB',
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