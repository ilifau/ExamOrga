<?php
// Copyright (c) 2020 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

require_once(__DIR__ . '/../form/class.ilExamOrgaRepositorySelectInputGUI.php');

class ilExamOrgaReferenceField extends ilExamOrgaField
{
    /**
     * @inheritdoc
     */
    public function getListHTML($record) {
        $ref_id = (int) $this->getValue($record);
        if ($ref_id && !ilObject::_isInTrash($ref_id)) {
            $href = ilLink::_getStaticLink($ref_id, 'tst');
            $title = ilObject::_lookupTitle(ilObject::_lookupObjectId($ref_id));
            return '<a href = "' . $href . '" target="_blank">' . $title .'</a>';
        }
       return '';
    }

    /**
     * @inheritdoc
     */
    public function getDetailsHTML($record) {
        return parent::getListHTML($record);
    }

    /**
     * @inheritdoc
     */
    public function getFormItem($record) {
        $item = new ilExamOrgaRepositorySelectInputGUI($this->title, $this->getPostvar());
        $item->setSelectableTypes(['tst']);

        $item->setRequired($this->required);
        $item->setDisabled(!$this->object->canEditField($this));

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
        parent::setFilterCondition($list, $table);
    }

    /**
     * @inheritdoc
     */
    public function getExcelValue($record, $excel) {
        if (empty($this->getValue($record))) {
            return null;
        }
        return $this->getValue($record);
    }

    /**
     * @inheritdoc
     */
    public function setExcelValue($record, $excel, $value) {
        return parent::setExcelValue($record, $excel, $value);
    }
}