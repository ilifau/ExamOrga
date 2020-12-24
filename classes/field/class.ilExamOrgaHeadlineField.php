<?php
// Copyright (c) 2020 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

class ilExamOrgaHeadLineField extends ilExamOrgaField
{
    /**
     * @inheritdoc
     */
    public function getValue($record) {
        return $this->title;
    }

    /**
     * @inheritdoc
     */
    public function setValue($record, $value) {
        // nothing to do
    }


    /**
     * @inheritdoc
     */
    public function getDetailsHTML($record) {
        return $this->title;
    }

    /**
     * @inheritdoc
     */
    public function getFormItem($record) {
        $item = new ilFormSectionHeaderGUI();
        $item->setTitle($this->title);
        if (isset($this->info)) {
            $item->setInfo($this->info);
        }
        return $item;
    }

    /**
     * @inheritdoc
     */
    public function setByForm($record, $form) {
        //nothing to do
    }


    /**
     * Check if the field can be used in a list of records
     * @return bool
     */
    public function isForList() {
        return false;
    }

    /**
     * Check if the field can be used in excel export and import
     * @return bool
     */
    public function isForExcel() {
        return false;
    }

}