<?php
// Copyright (c) 2020 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

class ilExamOrgaCheckboxField extends ilExamOrgaField
{
    /**
     * @inheritdoc
     */
    public function getListHTML($record) {
        if ($this->getValue($record)) {
            $icon = ilUtil::getImagePath('icon_ok.svg');
            $alt = $this->plugin->txt('yes');
        }
        else {
            $icon = ilUtil::getImagePath('icon_not_ok.svg');
            $alt = $this->plugin->txt('no');
        }
        return '<img src="'. $icon . '" alt="' . $alt . '" />';
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
        $item = new ilCheckboxInputGUI($this->title, $this->getPostvar());
        $item->setRequired($this->required);
        $item->setDisabled(!$this->object->canEditField($this));

        if (isset($this->info)) {
            $item->setInfo($this->info);
        }

        $item->setChecked((bool) $this->getValue($record));
        return $item;

    }

    /**
     * @inheritdoc
     */
    public function setByForm($record, $form) {
        /** @var ilCheckboxInputGUI $item */
        $item = $form->getItemByPostVar($this->getPostvar());
        $this->setValue($record, (bool) $item->getChecked());
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