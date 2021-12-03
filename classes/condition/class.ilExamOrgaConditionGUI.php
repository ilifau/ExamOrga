<?php

require_once (__DIR__ . '/../class.ilExamOrgaBaseGUI.php');
require_once (__DIR__ . '/class.ilExamOrgaConditionTableGUI.php');
require_once (__DIR__ . '/class.ilExamOrgaCondition.php');
require_once(__DIR__ . '/../notes/class.ilExamOrgaNote.php');
require_once (__DIR__ . '/../notes/class.ilExamOrgaNotesTableGUI.php');

/**
 * Class ilExamOrgaConditionGUI
 *
 * @ilCtrl_Calls: ilExamOrgaConditionGUI: ilPropertyFormGUI
 */
class ilExamOrgaConditionGUI extends ilExamOrgaBaseGUI
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
                    $this->ctrl->forwardCommand($this->initConditionForm(new ilExamOrgaCondition($_GET['id'])));
                    break;
            }
        }
        else {
            $cmd = $this->ctrl->getCmd('listConditions');
            switch ($cmd)
            {
                case 'listConditions':
                case 'addCondition':
                case 'createCondition':
                case 'editCondition':
                case 'updateCondition':
                case 'confirmDeleteConditions':
                case 'deleteConditions':
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
     * Show the list of conditions
     */
    protected function listConditions()
    {
        $this->setListToolbar();

        $table = new ilExamOrgaConditionTableGUI($this, 'listConditions');
        $table->loadData();
        
        $this->tpl->setContent($table->getHTML());
    }
    
    /**
     * Show form to add a new condition
     */
    protected function addCondition()
    {
        $condition = new ilExamOrgaCondition();
        $form = $this->initConditionForm($condition);
        $this->tpl->setContent($form->getHTML());
    }

    /**
     * Save a new condition
     */
    protected function createCondition()
    {
        $condition = new ilExamOrgaCondition();
        $condition->obj_id = $this->object->getId();

        $this->setConditionToolbar();
        $form = $this->initConditionForm($condition);
        $form->setValuesByPost();
        if ($form->checkInput()) {
            foreach ($this->object->getConditionFields() as $field) {
                $field->setByForm($condition, $form);
            }
            $condition->create();

            ilUtil::sendSuccess($this->plugin->txt("condition_created"), true);
            $this->ctrl->setParameter($this, 'id', $condition->id);
            $this->ctrl->redirect($this, "editCondition");
        }
        $this->tpl->setContent($form->getHTML());
    }

    /**
     * Show form to edit a condition
     */
    protected function editCondition()
    {
        $this->ctrl->saveParameter($this, 'id');
        $this->setConditionToolbar();

        /** @var ilExamOrgaCondition $condition */
        $condition = ilExamOrgaCondition::find((int) $_GET['id']);

        $form = $this->initConditionForm($condition);
        $this->tpl->setContent( $form->getHTML());
    }

    /**
     * Update an edited condition
     */
    protected function updateCondition()
    {
        $this->ctrl->saveParameter($this, 'id');

        /** @var ilExamOrgaCondition $condition */
        $condition = ilExamOrgaCondition::find((int) $_GET['id']);

        $form = $this->initConditionForm($condition);
        $form->setValuesByPost();
        if ($form->checkInput()) {
            foreach ($this->object->getConditionFields() as $field) {
                $field->setByForm($condition, $form);
            }

            $condition->update();

            ilUtil::sendSuccess($this->plugin->txt("condition_updated"), true);
            $this->ctrl->redirect($this, "editCondition");
        }

        $this->setConditionToolbar();
        $this->tpl->setContent($form->getHTML());
    }

    /**
     * Init the form to  create or update a condition
     * @param ilExamOrgaCondition $condition
     * @return ilPropertyFormGUI
     */
    protected function initConditionForm($condition)
    {
        $form = new ilPropertyFormGUI();
        $form->setTitle($this->plugin->txt($condition->isNew() ? 'add_condition' : 'edit_condition'));
        $form->setDescription(nl2br($this->plugin->txt('conditions_description')));
        $form->setFormAction($this->ctrl->getFormAction($this));

        foreach ($this->object->getConditionFields() as $field) {
            if ($field->isForForm()) {
                $form->addItem($field->getFormItem($condition));
            }
        }

        if ($condition->isNew()) {
            $form->addCommandButton('createCondition', $this->plugin->txt('create_condition'));
        }
        else {
            $form->addCommandButton('updateCondition', $this->plugin->txt('update_condition'));
        }

        return $form;
    }


    /**
     * Set the toolbar for the condition list
     */
    protected function setListToolbar() {
        $button = ilLinkButton::getInstance();
        $button->setCaption($this->plugin->txt('add_condition'), false);
        $button->setUrl($this->ctrl->getLinkTarget($this, 'addCondition'));
        $this->toolbar->addButtonInstance($button);
        $this->toolbar->addSeparator();
    }

    /**
     * Set the toolbar for a condition view
     */
    protected function setConditionToolbar()
    {
        $button = ilLinkButton::getInstance();
        $button->setCaption('« ' . $this->plugin->txt('back_to_list'), false);
        $button->setUrl($this->ctrl->getLinkTarget($this, 'listConditions'));
        $this->toolbar->addButtonInstance($button);
    }


    /**
     * Confirm the deletion of conditions
     */
    protected function confirmDeleteConditions()
    {
        if (empty($_POST['ids'])) {
            ilUtil::sendFailure($this->lng->txt('select_at_least_one_object'), true);
            $this->ctrl->redirect($this,'listConditions');
        }

        $conf_gui = new ilConfirmationGUI();
        $conf_gui->setFormAction($this->ctrl->getFormAction($this));
        $conf_gui->setHeaderText($this->plugin->txt('confirm_delete_conditions'));
        $conf_gui->setConfirm($this->lng->txt('delete'),'deleteConditions');
        $conf_gui->setCancel($this->lng->txt('cancel'), 'listConditions');

        /** @var ilExamOrgaCondition[] $conditions */
        $conditions = ilExamOrgaCondition::where(['id' => $_POST['ids']])->get();

        foreach($conditions as $condition) {
            $conf_gui->addItem('ids[]', $condition->id, $condition->failure_message);
        }

        $this->tpl->setContent($conf_gui->getHTML());
    }

    /**
     * Delete confirmed items
     */
    protected function deleteConditions()
    {
        /** @var ilExamOrgaCondition[] $conditions */
        $conditions = ilExamOrgaCondition::where(['id' => $_POST['ids']])->get();

        foreach($conditions as $condition) {
            $condition->delete();
        }

        ilUtil::sendSuccess($this->plugin->txt('conditions_deleted'), true);
        $this->ctrl->redirect($this, 'listConditions');
    }
}