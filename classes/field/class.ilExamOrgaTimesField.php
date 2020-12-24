<?php
// Copyright (c) 2020 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

require_once(__DIR__ . '/../form/class.ilExamOrgaTimesInputGUI.php');

class ilExamOrgaTimesField extends ilExamOrgaField
{
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
        $item = new ilExamOrgaTimesInputGUI($this->title, $this->getPostvar());

        $item->setRequired($this->required);
        $item->setDisabled(!$this->object->canEditField($this));

        if (isset($this->info)) {
            $item->setInfo($this->info);
        }

        $item->setValueByArray([$this->getPostvar() => ilExamOrgaTimesInputGUI::_getArray($this->getValue($record))]);
        return $item;
    }

    /**
     * @inheritdoc
     */
    public function setByForm($record, $form) {
        /** @var  ilExamOrgaTimesInputGUI $item */
        $item = $form->getItemByPostVar($this->getPostvar());

        $this->setValue($record, ilExamOrgaTimesInputGUI::_getString($form->getInput($this->getPostvar())));
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
    public function getExcelValue($record, $excel) {
        return parent::getExcelValue($record, $excel);
    }

    /**
     * @inheritdoc
     */
    public function setExcelValue($record, $excel, $value) {
        return parent::setExcelValue($record, $excel, $value);
    }
}