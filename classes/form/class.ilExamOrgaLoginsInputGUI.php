<?php
// Copyright (c) 2020 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

/**
 * Class ilExamOrgaLoginsInputGUI
 * extended to support form confirmation
 *
 * @ilCtrl_IsCalledBy ilExamOrgaLoginsInputGUI: ilUIPluginRouterGUI
 * @ilCtrl_Calls ilExamOrgaLoginsInputGUI: ilRepositorySearchGUI
 *
 */
class ilExamOrgaLoginsInputGUI extends ilDclTextInputGUI
{
    /**
     * ilExamOrgaLoginsInputGUI constructor.
     * @param string $a_title
     * @param string $a_postvar
     */
    public function __construct($a_title = "", $a_postvar = "")
    {
        parent::__construct($a_title, $a_postvar);

        $ajax_url =$this->ctrl->getLinkTargetByClass(
            ['iluipluginroutergui', 'ilExamOrgaLoginsinputgui','ilrepositorysearchgui'],
            'doUserAutoComplete',
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
        $next_class = $this->ctrl->getNextClass();

        switch ($next_class) {
            case 'ilrepositorysearchgui':
                $rep_search = new ilRepositorySearchGUI();
                $rep_search->addUserAccessFilterCallable([$this, 'filterUserIdsByRbacOrPositionOfCurrentUser']);
                $this->ctrl->forwardCommand($rep_search);
                break;
        }
    }

    /**
     * Filter user ids by access
     * @param int[] $a_usr_ids
     * @return int[]
     */
    public function filterUserIdsByRbacOrPositionOfCurrentUser($a_user_ids)
    {
        return $a_user_ids;
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