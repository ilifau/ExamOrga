<?php
// Copyright (c) 2020 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

class ilExamOrgaRadioField extends ilExamOrgaField
{
    /**
     * @inheritdoc
     */
    public function getListHTML($record) {
        if (isset($this->options[$this->getValue($record)])) {
            return $this->options[$this->getValue($record)];
        }
        else {
            return $this->getValue($record);
        }

    }

    /**
     * @inheritdoc
     */
    public function getDetailsHTML($record) {
        return $this->getListHTML($record);
    }

    /**
     * @inheritdoc
     */
    public function getFormItem($record) {
        $item = new ilRadioGroupInputGUI($this->title, $this->getPostvar());
        $item->setRequired($this->required);
        $item->setDisabled(!$this->object->canEditField($this));

        foreach ($this->options as $value => $title) {
            $option = new ilRadioOption($title, $value);
            $item->addOption($option);
        }

        if (isset($this->info)) {
            $item->setInfo($this->info);
        }
        $item->setValue($this->getValue($record));
        return $item;
    }

    /**
     * @inheritdoc
     */
    public function setByForm($record, $form) {
        $value = $form->getInput($this->getPostvar());
        $this->setValue($record, $value);
    }

    /**
     * @inheritdoc
     */
    public function getFilterItem() {
        $item = new ilSelectInputGUI($this->title, $this->getPostvar());
        $options = array_merge(['' => $this->plugin->txt('filter_all')], $this->options);
        $item->setOptions($options);
        return $item;
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
        if (!empty($this->getValue($record))) {
            return $this->options[$this->getValue($record)];
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function setExcelValue($record, $excel, $value) {
        return parent::setExcelValue($record, $excel, $value);
    }
}