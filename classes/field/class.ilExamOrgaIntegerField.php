<?php
// Copyright (c) 2020 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

class ilExamOrgaIntegerField extends ilExamOrgaField
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
        $item = new ilCombinationInputGUI($this->title, $this->getPostvar());
        $from = new ilNumberInputGUI("", $this->getPostvar() . "_from");
        $from->allowDecimals(false);
        $item->addCombinationItem("from", $from, $this->plugin->txt("from"));
        $to = new ilNumberInputGUI("", $this->getPostvar() . "_to");
        $to->allowDecimals(false);
        $item->addCombinationItem("to", $to, $this->plugin->txt("to"));
        $item->setComparisonMode(ilCombinationInputGUI::COMPARISON_ASCENDING);
        $item->setMaxLength(7);
        $item->setSize(20);
        return $item;
    }

    /**
     * @inheritdoc
     */
    public function setFilterCondition($list, $table) {
        /** @var ilCombinationInputGUI $item */
        $item = $table->getFilterItemByPostVar($this->getPostvar());
        if (isset($item)) {
            /** @var ilNumberInputGUI $from */
            $from = $item->getCombinationItem('from');
            /** @var ilNumberInputGUI $to */
            $to = $item->getCombinationItem('to');

            if(isset($from) && !empty($from->getValue())) {
                $list->where([$this->name => $from->getValue()], '>=');
            }
            if (isset($to) && !empty($to->getValue())) {
                $list->where([$this->name => $to->getValue()], '<=');
            }
        }
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