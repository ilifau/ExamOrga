<?php
// Copyright (c) 2020 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

class ilExamOrgaDateField extends ilExamOrgaField
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
        if (!empty($this->getValue($record))) {
            $date = new ilDate($this->getValue($record), IL_CAL_DATE);
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
        $item->setShowTime(false);
        $item->setRequired($this->required);
        $item->setDisabled(!$this->object->canEditField($this));

        if (isset($this->info)) {
            $item->setInfo($this->info);
        }

        $value = (int) $this->getValue($record);
        if (!empty($value)) {
            $date = new ilDateTime($value, IL_CAL_DATE);
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
            $this->setValue($record, $date->get(IL_CAL_DATE));
        }
    }

    /**
     * @inheritdoc
     */
    public function getFilterItem() {
        $item = new ilCombinationInputGUI($this->title, $this->getPostvar());
        $from = new ilDateTimeInputGUI("", $this->getPostvar() . "_from");
        $item->addCombinationItem("from", $from, $this->plugin->txt("from"));
        $to = new ilDateTimeInputGUI("", $this->getPostvar() . "_to");
        $item->addCombinationItem("to", $to, $this->plugin->txt("to"));
        $item->setComparisonMode(ilCombinationInputGUI::COMPARISON_ASCENDING);
        return $item;
    }

    /**
     * @inheritdoc
     */
    public function setFilterCondition($list, $table) {
        /** @var ilCombinationInputGUI $item */
        $item = $table->getFilterItemByPostVar($this->getPostvar());
        if (isset($item)) {
            /** @var ilDateTimeInputGUI $from */
            $from = $item->getCombinationItem('from');
            /** @var ilDateTimeInputGUI $to */
            $to = $item->getCombinationItem('to');

            if(isset($from)) {
                /** @var ilDate $date */
                $date = $from->getDate();
                if (isset($date)) {
                    $list->where([$this->name => $date->get(IL_CAL_DATE)], '>=');
                }
            }
            if(isset($to)) {
                /** @var ilDate $date */
                $date = $to->getDate();
                if (isset($date)) {
                    $list->where([$this->name => $date->get(IL_CAL_DATE)], '<=');
                }
            }
        }

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