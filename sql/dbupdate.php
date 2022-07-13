<#1>
<?php
    /**
     * Copyright (c) 2020 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg
     * GPLv3, see docs/LICENSE
     */

    /**
     * ExamOrga plugin: database update script
     *
     * @author Fred Neumann <fred.neumann@fau.de>
     */

    /** @var ilDBInterface */
    global $ilDB;
?>
<#2>
<?php
if (!$ilDB->tableExists('xamo_config'))
{
    $fields = array(
        'param_name' => array(
            'type' => 'text',
            'length' => 255,
            'notnull' => true,
        ),
        'param_value' => array(
            'type' => 'text',
            'length' => 255,
            'notnull' => false,
            'default' => null
        )
    );
    $ilDB->createTable("xamo_config", $fields);
    $ilDB->addPrimaryKey("xamo_config", array("param_name"));
}
?>
<#3>
<?php
if (!$ilDB->tableExists('xamo_data'))
{
    $fields = array(
        'obj_id' => array(
            'type' => 'integer',
            'length' => 4,
            'notnull' => true,
        ),
        'param_name' => array(
            'type' => 'text',
            'length' => 255,
            'notnull' => true,
        ),
        'param_value' => array(
            'type' => 'text',
            'length' => 4000,
            'notnull' => false,
            'default' => null
        )
    );
    $ilDB->createTable("xamo_data", $fields);
    $ilDB->addPrimaryKey("xamo_data", array("obj_id", "param_name"));
}
?>
<#4>
<?php
    require_once('Services/Migration/DBUpdate_3560/classes/class.ilDBUpdateNewObjectType.php');
    $type_id = ilDBUpdateNewObjectType::addNewType('xamo', 'Exam Orga');
    $ops_id = ilDBUpdateNewObjectType::addCustomRBACOperation('add_entry', 'Add Entry', 'object', 3200);
    ilDBUpdateNewObjectType::addRBACOperation($type_id, $ops_id);
    $ops_id = ilDBUpdateNewObjectType::addCustomRBACOperation('view_entries', 'View All Entries', 'object', 3210);
    ilDBUpdateNewObjectType::addRBACOperation($type_id, $ops_id);
?>
<#5>
<?php
    $fields = array(
        'id' => array(
            'notnull' => '1',
            'type' => 'integer',
            'length' => '4',

        ),
        'obj_id' => array(
            'notnull' => '1',
            'type' => 'integer',
            'length' => '4',

        ),
        'fau_unit' => array(
            'type' => 'text',
            'length' => '200',

        ),
        'fau_chair' => array(
            'type' => 'text',
            'length' => '200',

        ),
        'fau_lecturer' => array(
            'notnull' => '1',
            'type' => 'text',
            'length' => '200',

        ),
        'mail_address' => array(
            'type' => 'text',
            'length' => '200',

        ),
        'mail_title' => array(
            'type' => 'text',
            'length' => '200',

        ),
        'exam_format' => array(
            'notnull' => '1',
            'type' => 'text',
            'length' => '20',

        ),
        'force_presence' => array(
            'type' => 'integer',
            'length' => '4',

        ),
        'exam_method' => array(
            'type' => 'text',
            'length' => '20',

        ),
        'exam_type' => array(
            'type' => 'text',
            'length' => '20',

        ),
        'exam_title' => array(
            'notnull' => '1',
            'type' => 'text',
            'length' => '200',

        ),
        'exam_date' => array(
            'notnull' => '1',
            'type' => 'date',

        ),
        'exam_ids' => array(
            'type' => 'text',
            'length' => '1000',

        ),
        'alternative_dates' => array(
            'type' => 'text',
            'length' => '200',

        ),
        'exam_runs' => array(
            'type' => 'text',
            'length' => '200',

        ),
        'run_minutes' => array(
            'type' => 'integer',
            'length' => '4',

        ),
        'num_participants' => array(
            'type' => 'integer',
            'length' => '4',

        ),
        'test_ref_id' => array(
            'type' => 'integer',
            'length' => '4',

        ),
        'admins_text' => array(
            'type' => 'text',
            'length' => '2000',

        ),
        'admins' => array(
            'type' => 'text',
            'length' => '1000',

        ),
        'correctors' => array(
            'type' => 'text',
            'length' => '1000',

        ),
        'monitors' => array(
            'type' => 'text',
            'length' => '1000',

        ),
        'room' => array(
            'type' => 'text',
            'length' => '200',

        ),
        'remarks' => array(
            'type' => 'text',
            'length' => '2000',

        ),
        'booking_in_process' => array(
            'type' => 'integer',
            'length' => '4',

        ),
        'booking_approved' => array(
            'type' => 'integer',
            'length' => '4',

        ),
        'finally_approved' => array(
            'type' => 'integer',
            'length' => '4',

        ),
        'team_agent' => array(
            'type' => 'text',
            'length' => '20',

        ),
        'room_approved' => array(
            'type' => 'integer',
            'length' => '4',

        ),
        'room_in_univis' => array(
            'type' => 'integer',
            'length' => '4',

        ),
        'quality_checked' => array(
            'type' => 'integer',
            'length' => '4',

        ),
        'reg_code' => array(
            'type' => 'text',
            'length' => '20',

        ),
        'course_link' => array(
            'type' => 'text',
            'length' => '100',
        ),
        'team_students' => array(
            'type' => 'text',
            'length' => '200',

        ),
        'team_standby' => array(
            'type' => 'text',
            'length' => '200',

        ),
        'ips_active' => array(
            'type' => 'integer',
            'length' => '4',

        ),
        'tech_details' => array(
            'type' => 'text',
            'length' => '2000',

        ),
        'settings_checked' => array(
            'type' => 'integer',
            'length' => '4',

        ),
        'seb_checked' => array(
            'type' => 'integer',
            'length' => '4',

        ),
        'owner_id' => array(
            'notnull' => '1',
            'type' => 'integer',
            'length' => '4',

        ),
        'created_at' => array(
            'notnull' => '1',
            'type' => 'integer',
            'length' => '4',

        ),
        'created_by' => array(
            'notnull' => '1',
            'type' => 'integer',
            'length' => '4',

        ),
        'modified_at' => array(
            'notnull' => '1',
            'type' => 'integer',
            'length' => '4',

        ),
        'modified_by' => array(
            'notnull' => '1',
            'type' => 'integer',
            'length' => '4',

        ),

    );
    if (! $ilDB->tableExists('xamo_record')) {
        $ilDB->createTable('xamo_record', $fields);
        $ilDB->addPrimaryKey('xamo_record', array( 'id' ));

        if (! $ilDB->sequenceExists('xamo_record')) {
            $ilDB->createSequence('xamo_record');
        }

}
?>
<#6>
<?php
    /** @var ilDBInterface $ilDB */
    if (!$ilDB->indexExistsByFields('xamo_record', ['obj_id'])) {
        $ilDB->addIndex('xamo_record', ['obj_id'], 'i1');
    }
    if (!$ilDB->indexExistsByFields('xamo_record', ['fau_chair'])) {
        $ilDB->addIndex('xamo_record', ['fau_chair'], 'i2');
    }
    if (!$ilDB->indexExistsByFields('xamo_record', ['fau_lecturer'])) {
        $ilDB->addIndex('xamo_record', ['fau_lecturer'], 'i3');
    }
    if (!$ilDB->indexExistsByFields('xamo_record', ['exam_format'])) {
        $ilDB->addIndex('xamo_record', ['exam_format'], 'i4');
    }
    if (!$ilDB->indexExistsByFields('xamo_record', ['exam_method'])) {
        $ilDB->addIndex('xamo_record', ['exam_method'], 'i5');
    }
    if (!$ilDB->indexExistsByFields('xamo_record', ['exam_type'])) {
        $ilDB->addIndex('xamo_record', ['exam_type'], 'i6');
    }
    if (!$ilDB->indexExistsByFields('xamo_record', ['exam_type'])) {
        $ilDB->addIndex('xamo_record', ['exam_type'], 'i7');
    }
    if (!$ilDB->indexExistsByFields('xamo_record', ['exam_title'])) {
        $ilDB->addIndex('xamo_record', ['exam_title'], 'i8');
    }
    if (!$ilDB->indexExistsByFields('xamo_record', ['exam_date'])) {
        $ilDB->addIndex('xamo_record', ['exam_date'], 'i9');
    }
    if (!$ilDB->indexExistsByFields('xamo_record', ['num_participants'])) {
        $ilDB->addIndex('xamo_record', ['num_participants'], 'i10');
    }
?>
<#7>
<?php
$fields = array(
    'porgnr' => array(
        'notnull' => '1',
        'type' => 'integer',
        'length' => '4',

    ),
    'pnr' => array(
        'notnull' => '1',
        'type' => 'integer',
        'length' => '4',

    ),
    'psem' => array(
        'notnull' => '1',
        'type' => 'text',
        'length' => '10',

    ),
    'ptermin' => array(
        'type' => 'text',
        'length' => '10',

    ),
    'pdatum' => array(
        'type' => 'text',
        'length' => '10',

    ),
    'ppruefer' => array(
        'type' => 'text',
        'length' => '10',

    ),
    'vorname' => array(
        'type' => 'text',
        'length' => '50',

    ),
    'nachname' => array(
        'type' => 'text',
        'length' => '50',

    ),
    'titel' => array(
        'type' => 'text',
        'length' => '500',

    ),
    'veranstaltung' => array(
        'type' => 'text',
        'length' => '500',

    ),

);
if (! $ilDB->tableExists('xamo_campus')) {
    $ilDB->createTable('xamo_campus', $fields);
    $ilDB->addPrimaryKey('xamo_campus', array( 'porgnr' ));

    if (! $ilDB->sequenceExists('xamo_campus')) {
        $ilDB->createSequence('xamo_campus');
    }
}

if (!$ilDB->indexExistsByFields('xamo_campus', ['pnr'])) {
    $ilDB->addIndex('xamo_campus', ['pnr'], 'i2');
}
if (!$ilDB->indexExistsByFields('xamo_campus', ['psem'])) {
    $ilDB->addIndex('xamo_campus', ['psem'], 'i3');
}
if (!$ilDB->indexExistsByFields('xamo_campus', ['nachname'])) {
    $ilDB->addIndex('xamo_campus', ['nachname'], 'i4');
}
?>
<#8>
<?php
$fields = array(
    'id' => array(
        'notnull' => '1',
        'type' => 'integer',
        'length' => '4',

    ),
    'record_id' => array(
        'notnull' => '1',
        'type' => 'integer',
        'length' => '4',

    ),
    'exam_run' => array(
        'type' => 'text',
        'length' => '10',

    ),
    'link' => array(
        'type' => 'text',
        'length' => '100',

    ),
    'created_at' => array(
        'notnull' => '1',
        'type' => 'integer',
        'length' => '4',
    ),

);
if (! $ilDB->tableExists('xamo_link')) {
    $ilDB->createTable('xamo_link', $fields);
    $ilDB->addPrimaryKey('xamo_link', array( 'id' ));

    if (! $ilDB->sequenceExists('xamo_link')) {
        $ilDB->createSequence('xamo_link');
    }
}

if (!$ilDB->indexExistsByFields('xamo_link', ['record_id'])) {
    $ilDB->addIndex('xamo_link', ['record_id'], 'i1');
}
?>
<#9>
<?php
$fields = array(
    'id' => array(
        'notnull' => '1',
        'type' => 'integer',
        'length' => '4',

    ),
    'record_id' => array(
        'notnull' => '1',
        'type' => 'integer',
        'length' => '4',

    ),
    'code' => array(
        'type' => 'integer',
        'length' => '4',

    ),
    'note' => array(
        'type' => 'text',
        'length' => '4000',

    ),
    'created_at' => array(
        'notnull' => '1',
        'type' => 'integer',
        'length' => '4',

    ),

);
if (! $ilDB->tableExists('xamo_note')) {
    $ilDB->createTable('xamo_note', $fields);
    $ilDB->addPrimaryKey('xamo_note', array( 'id' ));

    if (! $ilDB->sequenceExists('xamo_note')) {
        $ilDB->createSequence('xamo_note');
    }

    if (!$ilDB->indexExistsByFields('xamo_note', ['record_id'])) {
        $ilDB->addIndex('xamo_note', ['record_id'], 'i1');
    }

}
?>
<#10>
<?php
$fields = array(
    'id' => array(
        'notnull' => '1',
        'type' => 'integer',
        'length' => '4',

    ),
    'obj_id' => array(
        'notnull' => '1',
        'type' => 'integer',
        'length' => '4',

    ),
    'level' => array(
        'notnull' => '1',
        'type' => 'text',
        'length' => '10',
    ),
    'cond_type' => array(
        'notnull' => '1',
        'type' => 'text',
        'length' => '10',
        'default'=> 'require'
    ),
    'reg_min_date' => array(
        'type' => 'timestamp',

    ),
    'reg_max_date' => array(
        'type' => 'timestamp',

    ),
    'exam_from_date' => array(
        'type' => 'date',

    ),
    'exam_to_date' => array(
        'type' => 'date',

    ),
    'reg_min_days_before' => array(
        'type' => 'integer',
        'length' => '4',

    ),
    'exam_formats' => array(
        'type' => 'text',
        'length' => '200',

    ),
    'exam_types' => array(
        'type' => 'text',
        'length' => '200',

    ),
    'exam_min_date' => array(
        'type' => 'date',

    ),
    'exam_max_date' => array(
        'type' => 'date',

    ),
    'max_exams_per_day' => array(
        'type' => 'integer',
        'length' => '4',

    ),
    'weekdays' => array(
        'type' => 'text',
        'length' => '10',

    ),
    'min_daytime' => array(
        'type' => 'text',
        'length' => '10',

    ),
    'max_daytime' => array(
        'type' => 'text',
        'length' => '10',

    ),
    'max_exams_per_week' => array(
        'type' => 'integer',
        'length' => '4',

    ),
    'max_exams_per_month' => array(
        'type' => 'integer',
        'length' => '4',

    ),
    'failure_message' => array(
        'notnull' => '1',
        'type' => 'text',
        'length' => '2000',

    ),

);
if (! $ilDB->tableExists('xamo_cond')) {
    $ilDB->createTable('xamo_cond', $fields);
    $ilDB->addPrimaryKey('xamo_cond', array( 'id' ));

    $ilDB->addIndex('xamo_cond', ['obj_id'], 'i1');

    if (! $ilDB->sequenceExists('xamo_cond')) {
        $ilDB->createSequence('xamo_cond');
    }
}
?>
<#11>
<?php
if (!$ilDB->tableColumnExists('xamo_record', 'booking_status')) {
    $ilDB->addTableColumn('xamo_record', 'booking_status', array(
        'type' => 'text',
        'length' => '20',
        'notnull' => '1',
        'default' => 'requested'
    ));

    $ilDB->manipulate("UPDATE xamo_record SET booking_status = 'approved' WHERE booking_status = 'requested' AND booking_approved > 0");
    $ilDB->manipulate("UPDATE xamo_record SET booking_status = 'in_process' WHERE booking_status = 'requested' AND booking_in_process > 0");
}
?>
<#12>
<?php
if ($ilDB->tableColumnExists('xamo_record', 'booking_approved')) {
    $ilDB->dropTableColumn('xamo_record', 'booking_approved');
}
if ($ilDB->tableColumnExists('xamo_record', 'booking_in_process')) {
    $ilDB->dropTableColumn('xamo_record', 'booking_in_process');
}
?>
<#13>
<?php
if (!$ilDB->tableColumnExists('xamo_note', 'note_type')) {
    $ilDB->addTableColumn('xamo_note', 'note_type', array(
        'type' => 'text',
        'length' => '20',
        'notnull' => '0',
        'default' => null
    ));

    $ilDB->manipulate("UPDATE xamo_note SET note_type = 'zoom' WHERE note_type IS NULL");
}
?>
<#14>
<?php
$fields = array(
    'id' => array(
        'notnull' => '1',
        'type' => 'integer',
        'length' => '4',

    ),
    'obj_id' => array(
        'notnull' => '1',
        'type' => 'integer',
        'length' => '4',

    ),
    'message_type' => array(
        'type' => 'text',
        'length' => '20',

    ),
    'subject' => array(
        'type' => 'text',
        'length' => '250',

    ),
    'content' => array(
        'type' => 'text',
        'length' => '4000',
    ),
    'active' => array(
        'notnull' => '1',
        'type' => 'integer',
        'length' => '4',
        'default' => 0
    )

);
if (! $ilDB->tableExists('xamo_message')) {
    $ilDB->createTable('xamo_message', $fields);
    $ilDB->addPrimaryKey('xamo_message', array( 'id' ));

    if (! $ilDB->sequenceExists('xamo_message')) {
        $ilDB->createSequence('xamo_message');
    }

    if (!$ilDB->indexExistsByFields('xamo_message', ['obj_id', 'message_type'])) {
        $ilDB->addIndex('xamo_message', ['obj_id', 'message_type'], 'i1');
    }
}
?>
<#15>
<?php
$fields = array(
    'id' => array(
        'notnull' => '1',
        'type' => 'integer',
        'length' => '4',

    ),
    'record_id' => array(
        'notnull' => '1',
        'type' => 'integer',
        'length' => '4',

    ),
    'message_type' => array(
        'type' => 'text',
        'length' => '20',
        'notnull' => '1',

    ),
    'sent_at' => array(
        'notnull' => '1',
        'type' => 'integer',
        'length' => '4',

    ),

);
if (! $ilDB->tableExists('xamo_message_sent')) {
    $ilDB->createTable('xamo_message_sent', $fields);
    $ilDB->addPrimaryKey('xamo_message_sent', array( 'id' ));

    if (! $ilDB->sequenceExists('xamo_message_sent')) {
        $ilDB->createSequence('xamo_message_sent');
    }

    if (!$ilDB->indexExistsByFields('xamo_message_sent', ['record_id', 'message_type'])) {
        $ilDB->addIndex('xamo_message_sent', ['record_id', 'message_type'], 'i1');
    }
}
?>
<#16>
<?php
if (!$ilDB->indexExistsByFields('xamo_campus', ['titel'])) {
    $ilDB->addIndex('xamo_campus', ['titel'], 'i5');
}
if (!$ilDB->indexExistsByFields('xamo_campus', ['veranstaltung'])) {
    $ilDB->addIndex('xamo_campus', ['veranstaltung'], 'i6');
}
?>
