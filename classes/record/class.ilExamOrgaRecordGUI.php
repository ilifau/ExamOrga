<?php

require_once (__DIR__ . '/../class.ilExamOrgaBaseGUI.php');
require_once (__DIR__ . '/class.ilExamOrgaRecordTableGUI.php');
require_once (__DIR__ . '/class.ilExamOrgaRecord.php');

/**
 * Class ilExamOrgaRecordGUI
 */
class ilExamOrgaRecordGUI extends ilExamOrgaBaseGUI
{
    /**
     * Execute a command
     * This should be overridden in the child classes
     */
    public function executeCommand()
    {
        $cmd = $this->ctrl->getCmd('listRecords');
        switch ($cmd)
        {
            case 'listRecords':
            case 'applyFilter':
            case 'resetFilter':
            case 'viewDetails':
            case 'addRecord':
            case 'createRecord':
            case 'editRecord':
            case 'updateRecord':
                $this->$cmd();
                break;

            default:
                // show unknown command
                $this->tpl->setContent('unknown command: ' . $cmd);
                return;
        }
    }

    /**
     * Show the list of records
     */
    protected function listRecords()
    {
        if ($this->checkListRecords()) {
            $this->setListToolbar();

            $table = new ilExamOrgaRecordTableGUI($this, 'listRecords');
            $table->loadData();
            $this->tpl->setContent($table->getHTML());
        }
    }

    /**
     * Apply filter
     */
    protected function applyFilter()
    {
        if ($this->checkListRecords()) {
            $this->setListToolbar();
            $table = new ilExamOrgaRecordTableGUI($this, 'listRecords');
            $table->writeFilterToSession();
            $table->resetOffset();
            $table->loadData();
            $this->tpl->setContent($table->getHTML());
        }
    }

    /**
     * Reset filter
     */
    protected function resetFilter()
    {
        if ($this->checkListRecords()) {
            $this->setListToolbar();
            $table = new ilExamOrgaRecordTableGUI($this, 'listRecords');
            $table->resetOffset();
            $table->resetFilter();
            $table->loadData();
            $this->tpl->setContent($table->getHTML());
        }
    }

    /**
     * View the details of a record
     */
    protected function viewDetails()
    {
        /** @var ilExamOrgaRecord $record */
        $record = ilExamOrgaRecord::find((int) $_GET['id']);
        $this->checkViewRecord($record);

        $form = new ilPropertyFormGUI();
        $form->setTitle($this->plugin->txt('view_details'));
        $form->setFormAction($this->ctrl->getFormAction($this));

        foreach ($this->object->getAvailableFields() as $field) {
            $item = $field->getFormItem($record);
            $item->setDisabled(true);
            $form->addItem($item);
        }

        $form->addCommandButton('listRecords', $this->lng->txt('cancel'));
        $this->tpl->setContent($form->getHTML());
    }

    /**
     * Show form to add a new record
     */
    protected function addRecord()
    {
        $this->checkAddRecord();
        $record = new ilExamOrgaRecord();
        $form = $this->initRecordForm($record);
        $this->tpl->show($form->getHTML());
    }

    /**
     * Save a new record
     */
    protected function createRecord()
    {
        $this->checkAddRecord();
        $record = new ilExamOrgaRecord();
        $record->obj_id = $this->object->getId();

        $form = $this->initRecordForm($record);
        $form->setValuesByPost();
        if ($form->checkInput()) {
            foreach ($this->object->getAvailableFields() as $field) {
                if ($this->object->canEditField($field)) {
                    $field->setByForm($record, $form);
                }
            }
            $record->create();

            ilUtil::sendSuccess($this->lng->txt("record_created"), true);
            $this->ctrl->redirect($this, "editRecord");
        }
        $this->tpl->setContent($form->getHTML());
    }

    /**
     * Show form to edit a record
     */
    protected function editRecord()
    {
        /** @var ilExamOrgaRecord $record */
        $record = ilExamOrgaRecord::find((int) $_GET['id']);
        $this->checkEditRecord($record);

        $form = $this->initRecordForm($record);
        $this->tpl->setContent($form->getHTML());
    }

    /**
     * Update an edited record
     */
    protected function updateRecord()
    {
        /** @var ilExamOrgaRecord $record */
        $record = ilExamOrgaRecord::find((int) $_GET['id']);
        $this->checkEditRecord($record);

        $form = $this->initRecordForm($record);
        $form->setValuesByPost();
        if ($form->checkInput()) {
            foreach ($this->object->getAvailableFields() as $field) {
                if ($this->object->canEditField($field)) {
                    $field->setByForm($record, $form);
                }
            }
            $record->update();

            ilUtil::sendSuccess($this->lng->txt("record_created"), true);
            $this->ctrl->redirect($this, "editRecord");
        }
        $this->tpl->setContent($form->getHTML());
    }

    /**
     * Init the form to  create or update a record
     * @param ilExamOrgaRecord $record
     * @return ilPropertyFormGUI
     */
    protected function initRecordForm($record)
    {
        $form = new ilPropertyFormGUI();
        $form->setTitle($this->plugin->txt($record->isNew() ? 'add_record' : 'edit_record'));
        $form->setFormAction($this->ctrl->getFormAction($this));

        foreach ($this->object->getAvailableFields() as $field) {
            $form->addItem($field->getFormItem($record));
        }

        if ($record->isNew()) {
            $form->addCommandButton('saveRecord', $this->plugin->txt('save_record'));
        }
        else {
            $form->addCommandButton('updateRecord', $this->plugin->txt('update_record'));
        }
        $form->addCommandButton('listRecords', $this->lng->txt('cancel'));

        return $form;
    }


    /**
     * Set the toolbar for the record list
     */
    protected function setListToolbar() {
        if ($this->object->canAddRecord()) {
            $button = ilLinkButton::getInstance();
            $button->setCaption($this->plugin->txt('add_record'));
            $button->setUrl($this->ctrl->getLinkTarget($this, 'addRecord'));
            $this->toolbar->addButtonInstance($button);
        }
    }


    /**
     * Check if records can be listed
     * @return bool
     */
    protected function checkListRecords()
    {
        if (!$this->object->canViewAllRecords() && !$this->object->canAddRecord()) {
            // don't redirect because listRecords is default command
            ilUtil::sendFailure($this->plugin->txt('message_no_list_records'));
            return false;
        }
        return true;
    }


    /**
     * Check if a record can be added
     */
    protected function checkAddRecord()
    {
        if (!$this->object->canAddRecord()) {
            ilUtil::sendFailure($this->plugin->txt('message_no_add_record'), true);
            $this->ctrl->redirect($this, 'listRecords');
        }
        return true;
    }

    /**
     * Check if a record can be added
     * @var ilExamOrgaRecord $record
     * @return bool
     */
    protected function checkViewRecord($record)
    {
        if (!isset($record)) {
            ilUtil::sendFailure($this->lng->txt("message_record_not_found"), true);
            $this->ctrl->redirect($this, 'listRecords');
        }
        if (!$this->object->canViewRecord($record)) {
            ilUtil::sendFailure($this->plugin->txt('message_no_view_record'), true);
            $this->ctrl->redirect($this, 'listRecords');
        }
        return true;
    }


    /**
     * Check if record can be edited
     * @var ilExamOrgaRecord $record
     * @return bool
     */
    protected function checkEditRecord($record)
    {
        if (!isset($record)) {
            ilUtil::sendFailure($this->lng->txt("message_record_not_found"), true);
            $this->ctrl->redirect($this, 'listRecords');
        }
        if (!$this->object->canEditRecord($record)) {
            ilUtil::sendFailure($this->lng->txt("message_no_edit_record"), true);
            $this->ctrl->redirect($this, 'listRecords');
        }
        return true;
    }
}