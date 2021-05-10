<?php
// Copyright (c) 2020 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

class ilExamOrgaNotesField extends ilExamOrgaField
{
    /** @var ilExamOrgaNote[][] */
    protected $notes;

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

        require_once (__DIR__ . '/../notes/class.ilExamOrgaNote.php');
        $notes = ilExamOrgaNote::where($DIC->database()->in('record_id', $ids, false, 'integer'))->orderBy('created_at')->get();

        /** @var ilExamOrgaNote $note */
        foreach($notes as $note) {
            $this->notes[$note->record_id][$note->id] = $note;
        }
    }


    /**
     * @inheritdoc
     */
    public function getListHTML($record) {
        require_once (__DIR__ . '/../notes/class.ilExamOrgaNote.php');
        return nl2br(ilExamOrgaNote::getRecordNotesText($record->getId(), $this->notes[$record->getId()]));
    }

    /**
     * @inheritdoc
     */
    public function getDetailsHTML($record) {
        require_once (__DIR__ . '/../notes/class.ilExamOrgaNote.php');
        return nl2br(ilExamOrgaNote::getRecordNotesText($record->getId()));
    }

    /**
     * @inheritdoc
     */
    public function getFormItem($record) {
        // not supported - notes have their own table
        return null;
    }

    /**
     * @inheritdoc
     */
    public function setByForm($record, $form) {
        // not supported - notes have their own table
        return;
    }

    /**
     * @inheritdoc
     */
    public function getFilterItem() {
        $options = [
            'zoom_any' => $this->plugin->txt('notes_filter_zoom_any'),
            'zoom_meeting' => $this->plugin->txt('notes_filter_zoom_meeting'),
            'zoom_monitor' => $this->plugin->txt('notes_filter_zoom_monitor')
        ];

        $item = new ilSelectInputGUI($this->title, $this->getPostvar());
        $options = array_merge(['' => $this->plugin->txt('filter_all')], $options);
        $item->setOptions($options);
        return $item;
    }

    /**
     * @inheritdoc
     */
    public function setFilterCondition($list, $table) {
        /** @var ilTextInputGUI $item */
        $item = $table->getFilterItemByPostVar($this->getPostvar());

        $cond = '';
        if (isset($item)) {
            switch ($item->getValue()) {
                case 'zoom_any':
                    $cond = 'code BETWEEN 100 AND 199';
                    break;
                case 'zoom_meeting':
                    $cond = 'code = 140 OR code = 150';
                    break;
                case 'zoom_monitor':
                    $cond = 'code = 141 OR code = 151';
                    break;
            }

            if ($cond != '') {
                $cond = 'id IN (SELECT record_id FROM xamo_note WHERE ' . $cond . ')';
                $list->where($cond);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function getExcelValue($record, $excel) {
        require_once (__DIR__ . '/../notes/class.ilExamOrgaNote.php');
        return ilExamOrgaNote::getRecordNotesText($record->getId(), $this->notes[$record->getId()]);
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
        return false;
    }

    /**
     * Check if the field can be used in a details view
     * @return bool
     */
    public function isForDetails() {
        // as long as the details view is provided as a form
        return false;
    }
}