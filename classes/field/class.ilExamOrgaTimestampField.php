<?php
// Copyright (c) 2020 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

class ilExamOrgaTimestampField extends ilExamOrgaField
{
    /**
     * @inheritdoc
     */
    public function getListHTML($record) {
        if (!empty($this->getValue($record))) {
            $date = new ilDateTime($this->getValue($record), IL_CAL_UNIX);
            return ilDatePresentation::formatDate($date);
        }
        return '';
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
        $item = new ilDateTimeInputGUI($this->title, $this->getPostvar());
        $item->setShowTime(true);
        $item->setRequired($this->required);
        $item->setDisabled(!$this->object->canEditField($this));

        if (isset($this->info)) {
            $item->setInfo($this->info);
        }

        $value = (int) $this->getValue($record);
        if (!empty($value)) {
            $date = new ilDateTime($value, IL_CAL_UNIX);
            $item->setDate($date);
        }

        return $item;

    }

    /**
     * @inheritdoc
     */
    public function setByForm($record, $form) {
        /** @var ilDateTimeInputGUI $item */
        $item = $form->getItemByPostVar($this->getPostvar());
        /** @var ilDateTime $date */
        $date = $item->getDate();
        if (isset($date)) {
            $this->setValue($record, $date->get(IL_CAL_UNIX));
        }
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
        if(!empty($this->getValue($record))) {
            return new ilDateTime($this->getValue($record), IL_CAL_UNIX);
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function setExcelValue($record, $excel, $value) {
        if (!empty($value)) {
            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
            $this->setValue($record, $date->getTimestamp());
        }
        return true;
    }
}