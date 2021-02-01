<?php
// Copyright (c) 2020 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

require_once(__DIR__ . '/../form/class.ilExamOrgaLoginsInputGUI.php');

class ilExamOrgaLoginsField extends ilExamOrgaField
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
        $item = new ilExamOrgaLoginsInputGUI($this->title, $this->getPostvar());

        $item->setRequired($this->required);
        $item->setDisabled(!$this->object->canEditField($this));
        $item->requireIdmAccount($this->require_idm);

        if (isset($this->info)) {
            $item->setInfo($this->info);
        }

        $item->setValueByArray([$this->getPostvar() =>
            ilExamOrgaLoginsInputGUI::_addNames(ilExamOrgaLoginsInputGUI::_getArray($this->getValue($record)))]);
        return $item;
    }

    /**
     * @inheritdoc
     */
    public function setByForm($record, $form) {
        /** @var  ilExamOrgaLoginsInputGUI $item */
        $item = $form->getItemByPostVar($this->getPostvar());

        $this->setValue($record,
            ilExamOrgaLoginsInputGUI::_getString(ilExamOrgaLoginsInputGUI::_removeNames($form->getInput($this->getPostvar()))));
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