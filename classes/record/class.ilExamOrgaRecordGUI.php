<?php

require_once (__DIR__ . '/../class.ilExamOrgaBaseGUI.php');
require_once (__DIR__ . '/class.ilExamOrgaRecordTableGUI.php');
require_once (__DIR__ . '/class.ilExamOrgaRecord.php');

/**
 * Class ilExamOrgaRecordGUI
 *
 * @ilCtrl_Calls: ilExamOrgaRecordGUI: ilPropertyFormGUI
 */
class ilExamOrgaRecordGUI extends ilExamOrgaBaseGUI
{
    /**
     * Execute a command
     * This should be overridden in the child classes
     */
    public function executeCommand()
    {
        $next_class = $this->ctrl->getNextClass();
        if (!empty($next_class)) {

            switch ($next_class) {
                case 'ilpropertyformgui':
                    $this->ctrl->forwardCommand($this->initRecordForm(new ilExamOrgaRecord($_GET['id'])));
                    break;
            }
        }
        else {
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
                case 'confirmDeleteRecords':
                case 'deleteRecords':
                    $this->$cmd();
                    break;

                default:
                    // show unknown command
                    $this->tpl->setContent('Unknown command: ' . $cmd);
                    return;
            }
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
            $table->initFilter();
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
            if ($field->isForDetails()) {
                $item = $field->getFormItem($record);
                if (method_exists($item, 'setDisabled')) {
                    $item->setDisabled(true);
                }
                $form->addItem($item);
            }
        }

        $form->addCommandButton('listRecords', $this->lng->txt('close'));
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
        $this->tpl->setContent($form->getHTML());
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

            ilUtil::sendSuccess($this->plugin->txt("record_created"), true);
            $this->ctrl->setParameter($this, 'id', $record->id);
            $this->ctrl->redirect($this, "editRecord");
        }
        $this->tpl->setContent($form->getHTML());
    }

    /**
     * Show form to edit a record
     */
    protected function editRecord()
    {
        $this->ctrl->saveParameter($this, 'id');

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
        $this->ctrl->saveParameter($this, 'id');

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

            ilUtil::sendSuccess($this->plugin->txt("record_updated"), true);
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
            if ($field->isForForm()) {
                $form->addItem($field->getFormItem($record));
            }
        }

        if ($record->isNew()) {
            $form->addCommandButton('createRecord', $this->plugin->txt('create_record'));
        }
        else {
            $form->addCommandButton('updateRecord', $this->plugin->txt('update_record'));
        }
        $form->addCommandButton('listRecords', $this->lng->txt('close'));

        return $form;
    }


    /**
     * Set the toolbar for the record list
     */
    protected function setListToolbar() {
        if ($this->object->canAddRecord()) {
            $button = ilLinkButton::getInstance();
            $button->setCaption($this->plugin->txt('add_record'), false);
            $button->setUrl($this->ctrl->getLinkTarget($this, 'addRecord'));
            $this->toolbar->addButtonInstance($button);
        }
    }


    /**
     * Confirm the deletion of records
     */
    protected function confirmDeleteRecords()
    {
        if (empty($_POST['ids'])) {
            ilUtil::sendFailure($this->lng->txt('select_at_least_one_object'), true);
            $this->ctrl->redirect($this,'listRecords');
        }

        $conf_gui = new ilConfirmationGUI();
        $conf_gui->setFormAction($this->ctrl->getFormAction($this));
        $conf_gui->setHeaderText($this->plugin->txt('confirm_delete_records'));
        $conf_gui->setConfirm($this->lng->txt('delete'),'deleteRecords');
        $conf_gui->setCancel($this->lng->txt('cancel'), 'listRecords');

        /** @var ilExamOrgaRecord[] $records */
        $records = ilExamOrgaRecord::where(['id' => $_POST['ids']])->get();

        foreach($records as $record) {
            if ($this->object->canDeleteRecord($record)) {
                $conf_gui->addItem('ids[]', $record->id, $record->getTitle());
            }
        }

        $this->tpl->setContent($conf_gui->getHTML());
    }

    /**
     * Delete confirmed items
     */
    protected function deleteRecords()
    {
        /** @var ilExamOrgaRecord[] $records */
        $records = ilExamOrgaRecord::where(['id' => $_POST['ids']])->get();

        foreach($records as $record) {
            if ($this->object->canDeleteRecord($record)) {
                $record->delete();
            }
        }

        ilUtil::sendSuccess($this->plugin->txt('records_deleted'), true);
        $this->ctrl->redirect($this, 'listRecords');
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