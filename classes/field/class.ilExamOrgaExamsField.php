<?php
// Copyright (c) 2020 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

require_once(__DIR__ . '/../form/class.ilExamOrgaExamsInputGUI.php');

//function split($delim, $string) {
//    return explode($delim, $string);
//}

class ilExamOrgaExamsField extends ilExamOrgaField
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
        $item = new ilExamOrgaExamsInputGUI($this->title, $this->getPostvar());

        $item->setRequired($this->required);
        $item->setDisabled(!$this->object->canEditField($this));

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

    /**
     * Get the exam data
     */
    protected function getExams() {

        $xml='<SOAPDataService active="y">
<general>
<object>getExaminations</object>
</general>
</SOAPDataService>
';

//        $client = new SoapClient($this->plugin->getConfig()->get('campus_soap_url') . '?wsdl');
//        //$params = array('in0'=> '124','in1'=>'1');
//        $result = $client->getDa( [] );
//        print_r($result);
//        exit;
//
        include_once("./webservice/soap/lib/nusoap.php");
        $soap_client = new nusoap_client($this->plugin->getConfig()->get('campus_soap_url'));
        $soap_client->setHTTPProxy('proxy.uni-erlangen.de', '80');
        $result = $soap_client->call('getDataXML');
        var_dump($result);
        exit;


        $client = new ilSoapClient($this->plugin->getConfig()->get('campus_soap_url'). '?wsdl');
        $client->init();
        $result = $client->call('getDataXML', ['xmlParams' => $xml]);

        var_dump($result);
        exit;
    }
}