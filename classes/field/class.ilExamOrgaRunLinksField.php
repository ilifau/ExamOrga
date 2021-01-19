<?php
// Copyright (c) 2020 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

class ilExamOrgaRunLinksField extends ilExamOrgaField
{
    /**
     * @inheritdoc
     */
    public function getListHTML($record) {
        // may be a too long list
        // query record by record is slow
        // so isForlist() is yet set to false
        return $this->getDetailsHTML($record);
    }

    /**
     * @inheritdoc
     */
    public function getDetailsHTML($record) {
        require_once (__DIR__ . '/../links/class.ilExamOrgaLink.php');
        return nl2br(ilExamOrgaLink::getRecordLinksText($record->id));
    }

    /**
     * @inheritdoc
     */
    public function getFormItem($record) {
        require_once (__DIR__ . '/../links/class.ilExamOrgaLink.php');
        $text = ilExamOrgaLink::getRecordLinksText($record->id);

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
        return ilExamOrgaLink::getRecordLinksText($record->id);

    }

    /**
     * @inheritdoc
     */
    public function setExcelValue($record, $excel, $value) {
        // nothing to set
    }

    /**
     * Check if the field can be used in a list of records
     * @return bool
     */
    public function isForList() {
        return false;
    }
}