<?php
// Copyright (c) 2020 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

/**
 * Class ilExamOrgaExamsInputGUI
 * extended to support form confirmation
 *
 * @ilCtrl_IsCalledBy ilExamOrgaExamsInputGUI: ilUIPluginRouterGUI
 * @ilCtrl_Calls ilExamOrgaExamsInputGUI: ilRepositorySearchGUI
 *
 */
class ilExamOrgaExamsInputGUI extends ilDclTextInputGUI
{
    /**
     * ilExamOrgaExamsInputGUI constructor.
     * @param string $a_title
     * @param string $a_postvar
     */
    public function __construct($a_title = "", $a_postvar = "")
    {
        parent::__construct($a_title, $a_postvar);

        $ajax_url =$this->ctrl->getLinkTargetByClass(
            ['iluipluginroutergui', 'ilexamorgaexamsinputgui'],
            'doAutoComplete',
            '',
            true,
            false
        );

        $this->setMulti(true);
        $this->setDataSource( $ajax_url);
    }

    /**
     * Execute command
     */
    public function executeCommand()
    {
        $cmd = $this->ctrl->getCmd();

        switch ($cmd) {
            case 'doAutoComplete':
                $this->$cmd();
                break;
        }
    }

    /**
     * Auto-Complete the text inout
     * @see \ilRepositorySearchGUI::doUserAutoComplete
     */
    public function doAutoComplete()
    {
        $field = $_GET['autoCompleteField'];
        $term = $_REQUEST['term'];
        $fetchall = $_REQUEST['fetchall'];

        $cnt = 0;

        $result[$cnt]['value'] = 'value';
        $result[$cnt]['label'] = 'label';
        $result[$cnt]['id'] = 'id';

        $result_json['items'] = $result;
        $result_json['hasMoreResults'] = false;

        echo ilJsonUtil::encode($result_json);
        exit;
    }

     /**
     *
     * @return bool|void
     */
    public function checkInput()
    {
        // fault tolerance
        if ($this->getMulti() && !is_array($_POST[$this->getPostVar()])) {
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
        $users = [];
        foreach (explode(',', (string) $value) as $user) {
            $users[$i++] = $user;
        }
        return $users;
    }


    /**
     * Get the string representation from an array
     *
     * @param $value
     * @return string
     */
    public static function _getString($array)
    {
        return implode(', ', $array);
    }
}