<?php

require_once (__DIR__ . '/class.ilExamOrgaMessage.php');
require_once (__DIR__ . '/../class.ilExamOrgaBaseGUI.php');
require_once (__DIR__ . '/class.ilExamOrgaMessageTableGUI.php');
require_once (__DIR__ . '/class.ilExamOrgaMailTemplateContext.php');
require_once (__DIR__ . '/class.ilExamOrgaMessenger.php');

/**
 * Class ilExamOrgaMessageGUI
 *
 * @ilCtrl_Calls: ilExamOrgaMessageGUI: ilPropertyFormGUI
 */
class ilExamOrgaMessageGUI extends ilExamOrgaBaseGUI
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
                    $this->ctrl->forwardCommand($this->initMessageForm(new ilExamOrgaMessage($_GET['id'])));
                    break;
            }
        }
        else {
            $cmd = $this->ctrl->getCmd('listMessages');
            switch ($cmd)
            {
                case 'listMessages':
                case 'editMessage':
                case 'updateMessage':
                case 'sendTestMessage':
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
     * Show the list of messages
     */
    protected function listMessages()
    {
        $table = new ilExamOrgaMessageTableGUI($this, 'listMessages');
        $table->loadData();
        
        $this->tpl->setContent($table->getHTML());
    }

    /**
     * Show form to edit a message
     */
    protected function editMessage()
    {
        $this->ctrl->saveParameter($this, 'type');
        $this->setMessageToolbar();

        /** @var ilExamOrgaMessage $message */
        $message = ilExamOrgaMessage::getByType($this->object->getId(), $_GET['type']);

        $form = $this->initMessageForm($message);
        $this->tpl->setContent( $form->getHTML());
    }

    /**
     * Update an edited message
     */
    protected function updateMessage()
    {
        $this->ctrl->saveParameter($this, 'type');

        /** @var ilExamOrgaMessage $message */
        $message = ilExamOrgaMessage::getByType($this->object->getId(), $_GET['type']);

        $form = $this->initMessageForm($message);
        $form->setValuesByPost();
        if ($form->checkInput()) {
            foreach ($this->object->getMessageFields() as $field) {
                $field->setByForm($message, $form);
            }

            // create or update the message
            $message->save();

            ilUtil::sendSuccess($this->plugin->txt("message_updated"), true);
            $this->ctrl->redirect($this, "editMessage");
        }

        $this->setMessageToolbar();
        $this->tpl->setContent($form->getHTML());
    }

    /**
     * Init the form to  create or update a message
     * @param ilExamOrgaMessage $message
     * @return ilPropertyFormGUI
     */
    protected function initMessageForm($message)
    {
        global $DIC;

        $user = $DIC->user();
        $context = new ilExamOrgaMailTemplateContext();
        $record = $context->getExampleRecord();
        $params = ['ref_id' => $this->object->getRefId(), 'record' => $record];

        $form = new ilPropertyFormGUI();
        $form->setTitle($this->plugin->txt('edit_message'));
        $form->setFormAction($this->ctrl->getFormAction($this));

        foreach ($this->object->getMessageFields() as $field) {
            if ($field->isForForm()) {
                $form->addItem($field->getFormItem($message));
            }
        }

        $DIC->language()->loadLanguageModule('mail');

        $placeholders = new ilManualPlaceholderInputGUI('m_message');
        $placeholders->setInstructionText('x');
        foreach ($context->getPlaceholders() as $key => $value) {
            $placeholders->addPlaceholder($value['placeholder'], $value['label']);
        }
        $form->addItem($placeholders);

        $header = new ilFormSectionHeaderGUI();
        $header->setTitle($this->lng->txt('preview'));
        $form->addItem($header);

        $item = new ilNonEditableValueGUI($this->plugin->txt('message_subject'), '', true);
        $resolver = new ilMailTemplatePlaceholderResolver($context, strip_tags($message->subject));
        $item->setValue('<div class="small">'. nl2br($resolver->resolve($user, $params)).'</div>');
        $form->addItem($item);

        $item = new ilNonEditableValueGUI($this->plugin->txt('message_message'), '', true);
        $resolver = new ilMailTemplatePlaceholderResolver($context, strip_tags($message->content));
        $item->setValue('<div class="small">'. nl2br($resolver->resolve($user, $params)).'</div>');
        $form->addItem($item);

        $form->addCommandButton('updateMessage', $this->plugin->txt('update_message'));

        return $form;
    }

    /**
     * Send a test mail to the current user
     */
    protected function sendTestMessage()
    {
        $this->ctrl->saveParameter($this, 'type');

        $context = new ilExamOrgaMailTemplateContext();
        $record = $context->getExampleRecord();

        $messenger = new ilExamOrgaMessenger($this->object->getRefId());
        if ($messenger->send($record, $_GET['type'], true, false)) {
            ilUtil::sendSuccess($this->plugin->txt('message_sent'), true);
        } else
        {
            ilUtil::sendFailure($this->plugin->txt('message_not_sent'), true);
        }

        $this->ctrl->redirect($this, "editMessage");
    }


    /**
     * Set the toolbar for a message view
     */
    protected function setMessageToolbar()
    {
        $button = ilLinkButton::getInstance();
        $button->setCaption('« ' . $this->plugin->txt('back_to_list'), false);
        $button->setUrl($this->ctrl->getLinkTarget($this, 'listMessages'));
        $this->toolbar->addButtonInstance($button);

        $button = ilLinkButton::getInstance();
        $button->setCaption($this->plugin->txt('send_test_message'), false);
        $button->setUrl($this->ctrl->getLinkTarget($this, 'sendTestMessage'));
        $this->toolbar->addButtonInstance($button);

    }
}