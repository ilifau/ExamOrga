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
        'admins' => array(
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

