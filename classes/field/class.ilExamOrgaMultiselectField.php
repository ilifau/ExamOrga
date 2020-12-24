<?php
// Copyright (c) 2020 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

class ilExamOrgaMultiselectField extends ilExamOrgaField
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
        $item = new ilMultiSelectInputGUI($this->title, $this->getPostvar());
        $item->setRequired($this->required);
        $item->setDisabled(!$this->object->canEditField($this));

        if (isset($this->info)) {
            $item->setInfo($this->info);
        }
        $item->setOptions($this->options);

        $values = [];
        foreach (explode(',', $this->getValue($record)) as $value) {
            $values[] = trim($value);
        }
        $item->setValue($values);
        return $item;
    }

    /**
     * @inheritdoc
     */
    public function setByForm($record, $form)
    {
        /** @var ilMultiSelectInputGUI $item */
        $item = $form->getItemByPostVar($this->getPostvar());
        $value = $item->getValue();
        if (is_array($value)) {
            $value = implode(', ', $value);
        }
        $this->setValue($record, $value);
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