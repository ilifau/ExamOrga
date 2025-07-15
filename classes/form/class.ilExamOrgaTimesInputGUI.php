<?php
// Copyright (c) 2020 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

require_once(__DIR__ . '/class.ilExamOrgaDayTimeInputGUI.php');

/**
 * Class ilExamOrgaTimesInputGUI
 * extended to support form confirmation
 */
class ilExamOrgaTimesInputGUI extends ilDclGenericMultiInputGUI
{
    /**
     * ilDclTimesInputGUI constructor.
     * @param string $a_title
     * @param string $a_postvar
     */
    public function __construct($a_title = "", $a_postvar = "")
    {
        parent::__construct($a_title, $a_postvar);

        $timeInput = new ilExamOrgaDayTimeInputGUI('', 'daytime');
        $timeInput->setRequired($this->required);

        //$this->setMulti(true);
        $this->setInput($timeInput);
    }


    /**
     *
     * @return bool|void
     */
    public function checkInput(): bool
    {
        global $DIC;

        // fault tolerance (field is multi, see constructor)
        if (!is_array($_POST[$this->getPostVar()])) {
            $_POST[$this->getPostVar()] = [];
        }


        if ($this->required) {
            $found = false;
            foreach ($_POST[$this->getPostVar()] as $entry) {
                if (is_array($entry)) {
                    if (!empty(ilExamOrgaDayTimeInputGUI::_getString($entry))) {
                        $found = true;
                    }
                }
            }
            if (!$found) {
                $this->setAlert(sprintf($DIC->language()->txt("msg_input_is_required")));
                return false;
            }
        }



       // return parent::checkInput();
       return true;
    }


    /**
     * Get the array representation from a string value
     *
     * @param string $value
     * @return array
     */
    public static function _getArray($value)
    {
        // ilDclGenericMultiInputGUI starts counting of its inputs with 2
        $i = 2;
        $times = [];
        
        if(!isset($value))
            return $times;
        
        foreach (explode(',', (string) $value) as $time) {
            $times[$i++] = $time;
        }
        return $times;
    }


    /**
     * Get the string representation from an array
     *
     * @param $value
     * @return string
     */
    public static function _getString($array)
    {
        $times = [];
        foreach ((array) $array as $entry) {
            $time = ilExamOrgaDayTimeInputGUI::_getString($entry);
            if (isset($time)) {
                $times[] = $time;
            }
        }
        sort($times);
        return implode(', ', $times);
    }

    /**
     * Get the line values
     *
     * @return array|null
     */    
    public function getLineValues(): ?array
    {
        return $this->line_values;
    }
}