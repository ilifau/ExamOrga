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

        $this->setMulti(true);
        $this->setAllowEmptyFields(true);
        $this->addInput($timeInput);
    }

    /**
     *
     * @return bool|void
     */
    public function checkInput()
    {
        // fault tolerance
        if (!is_array($_POST[$this->getPostVar()])) {
            $_POST[$this->getPostVar()] = [];
        }
        return parent::checkInput();
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
        foreach (explode(',', (string) $value) as $time) {
            $times[$i++] = [
                'daytime' => ilExamOrgaDayTimeInputGUI::_getArray($time)
            ];
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
            $time = ilExamOrgaDayTimeInputGUI::_getString($entry['daytime']);
            if (isset($times)) {
                $times[] = $time;
            }
        }
        sort($times);
        return implode(', ', $times);
    }
}