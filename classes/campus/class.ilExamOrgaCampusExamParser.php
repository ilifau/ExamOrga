<?php

require_once (__DIR__ ."/class.ilExamOrgaCampusExam.php");

class ilExamOrgaCampusExamParser extends ilSaxParser
{
    /** @var string */
    protected $cdata;

    /** @var ilExamOrgaCampusExam */
    protected $exam;

    /**
     *
     * @return
     * @throws	ilSaxParserException	if invalid xml structure is given
     * @throws	ilWebLinkXMLParserException	missing elements
     */

    public function start()
    {
         $this->startParsing();
    }

    /**
     * set event handlers
     *
     * @param	resource	reference to the xml parser
     * @access	private
     */
    public function setHandlers($a_xml_parser)
    {
        xml_set_object($a_xml_parser, $this);
        xml_set_element_handler($a_xml_parser, 'handlerBeginTag', 'handlerEndTag');
        xml_set_character_data_handler($a_xml_parser, 'handlerCharacterData');
    }

    /**
     * handler for begin of element
     *
     * @param	resource	$a_xml_parser		xml parser
     * @param	string		$a_name				element name
     * @param	array		$a_attribs			element attributes array
     */
    public function handlerBeginTag($a_xml_parser, $a_name, $a_attribs)
    {
        switch ($a_name) {

            case 'Examination':
                $this->exam = new ilExamOrgaCampusExam();
                break;
        }
    }

    /**
     * handler for end of element
     *
     * @param	resource	$a_xml_parser		xml parser
     * @param	string		$a_name				element name
     * @throws	ilSaxParserException	if invalid xml structure is given
     * @throws	ilWebLinkXMLParserException	missing elements
     */
    public function handlerEndTag($a_xml_parser, $a_name)
    {
        switch ($a_name) {

            case 'porg.porgnr':
                $this->exam->porgnr = (int) $this->cdata;
                break;
            case 'porg.pnr':
                $this->exam->pnr = (int) $this->cdata;
                break;
            case 'porg.psem':
                $this->exam->psem = $this->cdata;
                break;
            case 'porg.ptermin':
                $this->exam->ptermin = $this->cdata;
                break;
            case 'porg.pdatum':
                $this->exam->pdatum = $this->cdata;
                break;
            case 'porg.ppruefer':
                $this->exam->ppruefer = $this->cdata;
                break;
            case 'nachname':
                $this->exam->nachname = $this->cdata;
                break;
            case 'vorname':
                $this->exam->vorname = $this->cdata;
                break;
            case 'titel':
                $this->exam->titel = $this->cdata;
                break;
            case 'veranstaltung':
                $this->exam->veranstaltung = $this->cdata;
                break;

            case 'Examination':
                $this->exam->save();
                break;
        }

        // Reset cdata
        $this->cdata = '';
    }



    /**
     * handler for character data
     *
     * @param	resource	$a_xml_parser		xml parser
     * @param	string		$a_data				character data
     */
    public function handlerCharacterData($a_xml_parser, $a_data)
    {
        $this->cdata .= trim($a_data);
    }
}