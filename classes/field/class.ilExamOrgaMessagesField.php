<?php
// Copyright (c) 2020 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

class ilExamOrgaMessagesField extends ilExamOrgaField
{
    /** @var ilExamOrgaMessageSent[][] */
    protected $sent_messages;

    /**
     * Require the neccessary classes
     */
    protected function requireMessages()
    {
        require_once (__DIR__ . '/../message/class.ilExamOrgaMessage.php');
        require_once (__DIR__ . '/../message/class.ilExamOrgaMessageSent.php');

    }

    /**
     * Preload the data for the list view
     * @param ilExamOrgaFieldValues[] $records
     */
    public function preload($records)
    {
        global $DIC;

        $ids = [];
        foreach ($records as $record) {
            $ids[] = $record->getId();
        }

        $this->requireMessages();
        $result = ilExamOrgaMessageSent::where($DIC->database()->in('record_id', $ids, false, 'integer'))->orderBy('sent_at')->get();

        /** @var ilExamOrgaMessage $message */
        foreach($result as $sent) {
            $this->sent_messages[$sent->record_id][$sent->id] = $sent;
        }
    }


    /**
     * @inheritdoc
     */
    public function getListHTML($record)
    {
        return nl2br(ilExamOrgaMessageSent::getRecordMessagesText($record->getId(), $this->sent_messages[$record->getId()]));
    }

    /**
     * @inheritdoc
     */
    public function getDetailsHTML($record)
    {
        $this->requireMessages();
        return nl2br(ilExamOrgaMessageSent::getRecordMessagesText($record->getId()));
    }

    /**
     * @inheritdoc
     */
    public function getFormItem($record)
    {
        $this->requireMessages();
        $text = ilExamOrgaMessageSent::getRecordMessagesText($record->getId());

        $item = new ilTextAreaInputGUI($this->title, $this->getPostvar());
        $item->setDisabled(!$this->object->canEditField($this));

        if (isset($this->info)) {
            $item->setInfo($this->info);
        }
        if (isset($this->size)) {
            $item->setRows($this->size);
        }

        $item->setValue($text);
        return $item;
    }

    /**
     * @inheritdoc
     */
    public function setByForm($record, $form) {
        // not supported - messages have their own table
        return;
    }

    /**
     * @inheritdoc
     */
    public function getFilterItem()
    {
        $options = [
            'without_confirmation' => $this->plugin->txt('without_confirmation'),
            'without_reminder1' => $this->plugin->txt('without_reminder1'),
            'without_reminder2' => $this->plugin->txt('without_reminder2')
        ];

        $item = new ilSelectInputGUI($this->title, $this->getPostvar());
        $options = array_merge(['' => $this->plugin->txt('filter_all')], $options);
        $item->setOptions($options);
        return $item;
    }

    /**
     * @inheritdoc
     */
    public function setFilterCondition($list, $table)
    {
        global $DIC;

        /** @var ilTextInputGUI $item */
        $item = $table->getFilterItemByPostVar($this->getPostvar());

        $cond = '';
        if (isset($item)) {
            switch ($item->getValue()) {
                case 'without_confirmation':
                    $cond = 'message_type IN ('
                        . $DIC->database()->quote(ilExamOrgaMessage::TYPE_CONFIRM_PRESENCE) . ', '
                        . $DIC->database()->quote(ilExamOrgaMessage::TYPE_CONFIRM_REMOTE) . ')';
                    break;
                case 'without_reminder1':
                    $cond = 'message_type IN ('
                        . $DIC->database()->quote(ilExamOrgaMessage::TYPE_REMINDER1_PRESENCE) . ', '
                        . $DIC->database()->quote(ilExamOrgaMessage::TYPE_REMINDER1_REMOTE) . ')';
                    break;
                case 'without_reminder2':
                    $cond = 'message_type IN ('
                        . $DIC->database()->quote(ilExamOrgaMessage::TYPE_REMINDER2_PRESENCE) . ', '
                        . $DIC->database()->quote(ilExamOrgaMessage::TYPE_REMINDER2_REMOTE) . ')';
                    break;
            }

            if ($cond != '') {
                $cond = 'id NOT IN (SELECT record_id FROM xamo_message_sent WHERE ' . $cond . ')';
                $list->where($cond);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function getExcelValue($record, $excel) {
        $this->requireMessages();
        return ilExamOrgaMessageSent::getRecordMessagesText($record->getId(), $this->sent_messages[$record->getId()]);
    }

    /**
     * @inheritdoc
     */
    public function setExcelValue($record, $excel, $value) {
        // nothing to set
        return true;
    }


    /**
     * Check if the field can be used in a form
     * @return bool
     */
    public function isForForm() {
        return true;
    }

    /**
     * Check if the field can be used in a details view
     * @return bool
     */
    public function isForDetails() {
        // as long as the details view is provided as a form
        return true;
    }
}