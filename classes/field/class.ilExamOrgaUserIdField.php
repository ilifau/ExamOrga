<?php
// Copyright (c) 2020 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

require_once(__DIR__ . '/../form/class.ilExamOrgaLoginsInputGUI.php');

class ilExamOrgaUserIdField extends ilExamOrgaField
{
    /**
     * @inheritdoc
     */
    public function getListHTML($record) {
        return ilObjUser::_lookupLogin($this->getValue($record));
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
        $item = new ilExamOrgaLoginsInputGUI($this->title, $this->getPostvar());
        $item->setMulti(false);
        $item->setRequired($this->required);
        $item->setDisabled(!$this->object->canEditField($this));

        if (isset($this->info)) {
            $item->setInfo($this->info);
        }

        $item->setValue(ilObjUser::_lookupLogin($this->getValue($record)));
        return $item;
    }

    /**
     * @inheritdoc
     */
    public function setByForm($record, $form) {
        /** @var  ilExamOrgaLoginsInputGUI $item */
        $item = $form->getItemByPostVar($this->getPostvar());

        $user_id = ilObjUser::_lookupId($item->getValue());
        $this->setValue($record, $user_id);
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
        if (!empty($this->getValue($record))) {
            return ilObjUser::_lookupLogin($this->getValue($record));
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function setExcelValue($record, $excel, $value)
    {
        $user_id = ilObjUser::_lookupId($value);
        if (!empty($user_id)) {
            $this->setValue($record, $user_id);
            return true;
        }
        return !$this->required;
    }
}