<?php
// Copyright (c) 2020 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

require_once(__DIR__ . '/../form/class.ilExamOrgaExamsInputGUI.php');

class ilExamOrgaExamsField extends ilExamOrgaField
{
    /**
     * @inheritdoc
     */
    public function getListHTML($record) {

        $labels = [];
        foreach ((array) explode(',', $this->getValue($record)) as $porgnr) {
            if (!empty($porgnr)) {
                /** @var ilExamOrgaCampusExam $exam */
                foreach(ilExamOrgaCampusExam::where(['porgnr' => trim($porgnr)])->get() as $exam) {
                    $labels[] = $exam->getLabel();
                }
            }
        }
        return implode('<br />', array_unique($labels));
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
        $item = new ilExamOrgaExamsInputGUI($this->title, $this->getPostvar());

        $item->setRequired($this->required);
        $item->setDisabled(!$this->object->canEditField($this));
        $item->setAutocomplete($this->object->data->getCampusSemester());

        if (isset($this->info)) {
            $item->setInfo($this->info);
        }

        $item->setValueByArray([$this->getPostvar() => ilExamOrgaExamsInputGUI::_getArray($this->getValue($record))]);
        return $item;
    }

    /**
     * @inheritdoc
     */
    public function setByForm($record, $form) {
        /** @var  ilExamOrgaExamsInputGUI $item */
        $item = $form->getItemByPostVar($this->getPostvar());

        $this->setValue($record, ilExamOrgaExamsInputGUI::_getString($form->getInput($this->getPostvar())));
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