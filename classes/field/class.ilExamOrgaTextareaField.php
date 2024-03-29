<?php
// Copyright (c) 2020 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

class ilExamOrgaTextareaField extends ilExamOrgaField
{
    /**
     * @inheritdoc
     */
    public function getListHTML($record)
    {
       $text = parent::getListHTML($record);
       return ilStr::shortenText($text, 0, 250);
    }

    /**
     * @inheritdoc
     */
    public function getDetailsHTML($record)
    {
        return parent::getDetailsHTML($record);
    }

    /**
     * @inheritdoc
     */
    public function getFormItem($record)
    {
        $item = new ilTextAreaInputGUI($this->title, $this->getPostvar());
        $item->setRequired($this->required);
        $item->setDisabled(!$this->object->canEditField($this));

        if (isset($this->info)) {
            $item->setInfo($this->info);
        }
        if (isset($this->size)) {
            $item->setRows($this->size);
        }

        $item->setValue($this->getValue($record));
        return $item;
    }

    /**
     * @inheritdoc
     */
    public function setByForm($record, $form)
    {
        parent::setByForm($record, $form);
    }

    /**
     * @inheritdoc
     */
    public function getFilterItem()
    {
        return parent::getFilterItem();
    }

    /**
     * @inheritdoc
     */
    public function setFilterCondition($list, $table)
    {
        /** @var ilTextInputGUI $item */
        $item = $table->getFilterItemByPostVar($this->getPostvar());

        if (isset($item) && !empty($item->getValue())) {
            $list->where([$this->name => '%' . $item->getValue() . '%'], 'LIKE');
        }
    }

    /**
     * @inheritdoc
     */
    public function getExcelValue($record, $excel)
    {
        return parent::getExcelValue($record, $excel);
    }

    /**
     * @inheritdoc
     */
    public function setExcelValue($record, $excel, $value)
    {
        return parent::setExcelValue($record, $excel, $value);
    }
}