<?php
// Copyright (c) 2020 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

class ilExamOrgaLinkField extends ilExamOrgaField
{
    /**
     * @inheritdoc
     */
    public function getListHTML($record) {
        $link = ilUtil::stripSlashes((string) $this->getValue($record));
        if (!empty($link)) {
            return '<a href="' . $link . '" target="_blank">' . $link . '</a>';
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
        return parent::getFormItem($record);
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
        return parent::getFilterItem();
    }

    /**
     * @inheritdoc
     */
    public function setFilterCondition($list, $table) {
        /** @var ilTextInputGUI $item */
        $item = $table->getFilterItemByPostVar($this->getPostvar());

        if (isset($item) && !empty($item->getValue())) {
            $list->where(['%' . $this->name => $item->getValue() . '%'], 'LIKE');
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