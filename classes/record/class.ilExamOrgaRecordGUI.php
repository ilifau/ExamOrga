<?php

require_once (__DIR__ . '/../class.ilExamOrgaBaseGUI.php');
require_once (__DIR__ . '/class.ilExamOrgaRecordTableGUI.php');
require_once (__DIR__ . '/class.ilExamOrgaRecord.php');
require_once(__DIR__ . '/../notes/class.ilExamOrgaNote.php');
require_once (__DIR__ . '/../notes/class.ilExamOrgaNotesTableGUI.php');

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
                case 'excelExport':
                case 'excelImportForm':
                case 'excelImport':
                case 'deleteNote':
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

            $intro = $this->object->data->get('intro');
            $this->tpl->setContent($intro . $table->getHTML());
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

            $this->ctrl->redirect($this, 'listRecords');
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

            $this->ctrl->redirect($this, 'listRecords');
        }
    }

    /**
     * View the details of a record
     */
    protected function viewDetails()
    {
        $this->setRecordToolbar();

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

        $notesTable = new ilExamOrgaNotesTableGUI($this, 'viewDetails');
        $notesTable->loadData($record);

        if ($notesTable->dataExists()) {
            $this->tpl->setContent( $notesTable->getHTML() . $form->getHTML());
        }
        else {
            $this->tpl->setContent( $form->getHTML());
        }
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

            foreach ($this->object->getActiveConditions() as $cond) {
                if (!$cond->checkRecord($record)) {
                    ilUtil::sendFailure($cond->failure_message);
                    $this->tpl->setContent($form->getHTML());
                    return;
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
        $this->setRecordToolbar();

        /** @var ilExamOrgaRecord $record */
        $record = ilExamOrgaRecord::find((int) $_GET['id']);
        $this->checkEditRecord($record);

        $form = $this->initRecordForm($record);

        $notesTable = new ilExamOrgaNotesTableGUI($this, 'editRecord');
        $notesTable->loadData($record);

        if ($notesTable->dataExists()) {
            $this->tpl->setContent( $notesTable->getHTML() . $form->getHTML());
        }
        else {
            $this->tpl->setContent( $form->getHTML());
        }
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

        $original = clone $record;

        $form = $this->initRecordForm($record);
        $form->setValuesByPost();
        if ($form->checkInput()) {
            foreach ($this->object->getAvailableFields() as $field) {
                if ($this->object->canEditField($field)) {
                    $field->setByForm($record, $form);
                }
            }

            foreach ($this->object->getActiveConditions() as $cond) {
                if (!$cond->checkRecord($record) && $cond->checkRecord($original)) {
                    ilUtil::sendFailure($cond->failure_message);
                    $this->tpl->setContent($form->getHTML());
                    return;
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

        return $form;
    }

    /**
     * Delete a note
     */
    protected function deleteNote()
    {
        $this->ctrl->saveParameter($this, 'id');

        /** @var ilExamOrgaRecord $record */
        $record = ilExamOrgaRecord::find((int) $_GET['id']);
        $this->checkEditRecord($record);

        /** @var ilExamOrgaNote $note */
        $note = ilExamOrgaNote::find((int) $_GET['note_id']);
        if (isset($note)) {
            $note->delete();
            ilUtil::sendSuccess($this->plugin->txt("note_deleted"), true);
        }
        $this->ctrl->redirect($this, "editRecord");
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
            $this->toolbar->addSeparator();
        }

        $button = ilLinkButton::getInstance();
        $button->setCaption($this->plugin->txt('excel_export'), false);
        $button->setUrl($this->ctrl->getLinkTarget($this, 'excelExport'));
        $this->toolbar->addButtonInstance($button);

        if ($this->object->canEditAllRecords()) {
            $button = ilLinkButton::getInstance();
            $button->setCaption($this->plugin->txt('excel_import'), false);
            $button->setUrl($this->ctrl->getLinkTarget($this, 'excelImportForm'));
            $this->toolbar->addButtonInstance($button);
        }
    }

    /**
     * Set the toolbar for a record view
     */
    protected function setRecordToolbar()
    {
        $button = ilLinkButton::getInstance();
        $button->setCaption('« ' . $this->plugin->txt('back_to_list'), false);
        $button->setUrl($this->ctrl->getLinkTarget($this, 'listRecords'));
        $this->toolbar->addButtonInstance($button);
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
     * Export an excel file
     */
    protected function excelExport()
    {
        require_once (__DIR__ . '/class.ilExamOrgaRecordExcel.php');
        $file = ilUtil::ilTempnam();
        $excel = new ilExamOrgaRecordExcel();
        $excel->init($this->object);
        $excel->writeToFile($file);
        ilUtil::deliverFile($file, $excel->getFilename(), '', false, true, true);
    }

    /**
     * Show form to import an excel file
     */
    protected function excelImportForm()
    {
        $form = $this->initImportForm();
        $this->tpl->setContent($form->getHTML());
    }

    /**
     * Show form to import an excel file
     */
    protected function excelImport()
    {
        require_once (__DIR__ . '/class.ilExamOrgaRecordExcel.php');
        $excel = new ilExamOrgaRecordExcel();
        $excel->init($this->object);

        $form = $this->initImportForm();
        $form->setValuesByPost();
        if (!$form->checkInput()) {
            $this->tpl->setContent($form->getHTML());
            return;
        }

        $temp = $_FILES["excel_file"]["tmp_name"];

        if ($excel->loadFromFile($temp)) {
            ilUtil::sendSuccess($excel->getInfo(), true);
        }
        else {
            ilUtil::sendFailure($excel->getInfo(), true);
        }
        $this->ctrl->redirect($this, 'listRecords');
    }

    /**
     * Init the excel import form
     */
    protected function initImportForm()
    {
        $form = new ilPropertyFormGUI();
        $form->setMultipart(true);
        $form->setFormAction($this->ctrl->getFormAction($this));
        $form->setTitle($this->plugin->txt('excel_import'));

        $file = new ilFileInputGUI($this->plugin->txt('excel_file'), 'excel_file');
        $file->setSuffixes(['xls', 'xlsx']);
        $file->setRequired(true);
        $form->addItem($file);

        $form->addCommandButton('excelImport', $this->lng->txt('import'));
        $form->addCommandButton('listRecords', $this->lng->txt('cancel'));

        return $form;
    }


    /**
     * Check if records can be listed
     * @return bool
     */
    protected function checkListRecords()
    {
//        if (!$this->object->canViewAllRecords() && !$this->object->canAddRecord()) {
//            // don't redirect because listRecords is default command
//            ilUtil::sendFailure($this->plugin->txt('message_no_list_records'));
//            return false;
//        }
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