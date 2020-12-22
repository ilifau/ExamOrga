<?php
// Copyright (c) 2020 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

class ilExamOrgaIntegerField extends ilExamOrgaField
{
    /**
     * @inheritdoc
     */
    public function getValue($record) {
        return parent::getValue($record);
    }

    /**
     * @inheritdoc
     */
    public function setValue($record, $value) {
        parent::setValue($record, $value);
    }

    /**
     * @inheritdoc
     */
    public function getListHTML($record) {
        return parent::getListHTML($record);
    }

    /**
     * @inheritdoc
     */
    public function getDetailsHTML($record) {
        return parent::getDetailsHTML($record);
    }

    /**
     * @inheritdoc
     */
    public function getFormItem($record) {
        $item = new ilNumberInputGUI($this->title, $this->getPostvar());
        $item->setRequired($this->required);
        $item->setDisabled(!$this->object->canEditField($this));
        $item->allowDecimals(false);

        if (isset($this->info)) {
            $item->setInfo($this->info);
        }
        if (isset($this->size)) {
            $item->setSize($this->size);
        }
        if (!empty($this->limit)) {
            $item->setMaxValue($this->limit);
        }

        $item->setValue($this->getValue($record));
        return $item;

    }

    /**
     * @inheritdoc
     */
    public function setByForm($record, $form) {
        parent::setByForm($record, $form);
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
        parent::setFilterCondition($list, $table);
    }

    /**
     * @inheritdoc
     */
    public function getApiData($record) {
        return parent::getApiData($record);
    }

    /**
     * @inheritdoc
     */
    public function writeToExcel($record, $excel, $row, $com) {
        parent::writeToExcel($record, $excel, $row, $com);
    }

    /**
     * @inheritdoc
     */
    public function readFromExcel($record, $excel, $row, $com) {
        parent::readFromExcel($record, $excel, $row, $com);
    }
}