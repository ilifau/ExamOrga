<?php
// Copyright (c) 2020 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

class ilExamOrgaRunLinksField extends ilExamOrgaField
{
    /** @var ilExamOrgaLink[][] */
    protected $links;

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

        require_once (__DIR__ . '/../links/class.ilExamOrgaLink.php');
        $links = ilExamOrgaLink::where($DIC->database()->in('record_id', $ids, false, 'integer'))->orderBy('created_at')->get();

        /** @var ilExamOrgaLink $link */
        foreach($links as $link) {
            $this->links[$link->record_id][$link->id] = $link;
        }
    }
    /**
     * @inheritdoc
     */
    public function getListHTML($record) {
        require_once (__DIR__ . '/../links/class.ilExamOrgaLink.php');
        return ilExamOrgaLink::getRecordLinksHtml($record->getId(), $this->links[$record->getId()]);
    }

    /**
     * @inheritdoc
     */
    public function getDetailsHTML($record) {
        require_once (__DIR__ . '/../links/class.ilExamOrgaLink.php');
        return nl2br(ilExamOrgaLink::getRecordLinksText($record->getId()));
    }

    /**
     * @inheritdoc
     */
    public function getFormItem($record) {
        require_once (__DIR__ . '/../links/class.ilExamOrgaLink.php');
        $text = ilExamOrgaLink::getRecordLinksText($record->getId());

        $item = new ilTextAreaInputGUI($this->title, $this->getPostvar());
        $item->setRequired($this->required);
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
        // list is read-only
        return;
    }

    /**
     * @inheritdoc
     */
    public function getFilterItem() {
        return parent::getFilterItem();
    }

    /**
     * @inheritdoc
     */
    public function setFilterCondition($list, $table) {
        // would be sub query on links table
    }

    /**
     * @inheritdoc
     */
    public function getExcelValue($record, $excel) {
        require_once (__DIR__ . '/../links/class.ilExamOrgaLink.php');
        return ilExamOrgaLink::getRecordLinksText($record->getId(), $this->links[$record->getId()]);
    }

    /**
     * @inheritdoc
     */
    public function setExcelValue($record, $excel, $value) {
        // nothing to set
    }
}