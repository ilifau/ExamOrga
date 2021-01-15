<?php

class ilExamOrgaCampusExam extends ActiveRecord
{
    /**
     * @return string
     * @description Return the Name of your Database Table
     */
    public static function returnDbTableName()
    {
        return 'xamo_campus';
    }

    /**
     * @var integer
     * @con_has_field        true
     * @con_is_primary       true
     * @con_sequence         false
     * @con_is_notnull       true
     * @con_fieldtype        integer
     * @con_length           4
     */
    public $porgnr;

    /**
     * @var integer
     * @con_has_field        true
     * @con_is_notnull       true
     * @con_fieldtype        integer
     * @con_length           4
     */
    public $pnr;

    /**
     * @var string
     * @con_has_field        true
     * @con_is_notnull       true
     * @con_fieldtype        text
     * @con_length           10
     */
    public $psem;

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           10
     */
    public $ptermin;

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           10
     */
    public $pdatum;

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           10
     */
    public $ppruefer;

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           50
     */
    public $vorname;

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           50
     */
    public $nachname;


    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           500
     */
    public $titel;

    /**
     * @var string
     * @con_has_field        true
     * @con_fieldtype        text
     * @con_length           500
     */
    public $veranstaltung;


    /**
     * Get the exam data
     * @param ilExamOrgaPlugin
     */
    public static function updateExams($plugin)
    {
        $xml= '
<SOAPDataService active="y">
<general>
<object>getExaminations</object>
</general>
</SOAPDataService>
';
        $client = new SoapClient($plugin->getConfig()->get('campus_soap_url') . '?wsdl');
        $result = $client->__call('getDataXML', ['xmlParams' => $xml]);

        require_once (__DIR__ . '/class.ilExamOrgaCampusExamParser.php');
        $parser = new ilExamOrgaCampusExamParser();
        $parser->setXMLContent($result);
        $parser->startParsing();
    }

    /**
     * Get the label of the exam
     * @return string
     */
    public function getLabel()
    {
        return $this->porgnr . " - " . $this->nachname . ', ' . $this->vorname . ': ' . $this->titel . ' (PNR ' . $this->pnr . ')';
    }

}
