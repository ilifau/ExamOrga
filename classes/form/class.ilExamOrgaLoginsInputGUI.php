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
class ilExamOrgaLoginsInputGUI extends ilTextInputGUI
{
    protected $require_idm_account;


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
        $this->setInlineStyle('width: 30em;');
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
        global $DIC;

        // will do standard checks and prepare array for multi
        if (!parent::checkInput()) {
            return false;
        }

        foreach ((array) $_POST[$this->getPostVar()] as $entry) {
            if (!empty(trim($entry))) {
                $login = self::_removeNames([$entry])[0];
                $usr_id = ilObjUser::_loginExists($login);
                if (!$usr_id) {
                    $this->setAlert(sprintf(ilExamOrgaPlugin::getInstance()->txt('login_not_found'), $entry));
                    return false;
                }
                elseif ($this->isIdmAccountRequired()) {
                    $ext_account = ilObjUser::_lookupExternalAccount($usr_id);
                    if (empty($DIC->fau()->staging()->repo()->getIdentity($ext_account))) {
                        $this->setAlert(sprintf(ilExamOrgaPlugin::getInstance()->txt('idm_account_not_found'), $login));
                        return false;
                    }
                }
            }
        }
        return parent::checkInput();
    }

    /**
     * Require an external account when input is checked
     * @param bool $require
     */
    public function requireIdmAccount($require = true)
    {
        $this->require_idm_account = (bool) $require;
    }

    /**
     * Is an external account required?
     * @return bool
     */
    public function isIdmAccountRequired()
    {
        return (bool) $this->require_idm_account;
    }

    /**
     * Get the array representation from a string value
     *
     * @param string $value
     * @return array
     */
    public static function _getArray($value)
    {
        $users = [];
        foreach (explode(',', (string) $value) as $user) {
            if (!empty(trim($user))) {
                $users[] = trim($user);
            }
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

    /**
     * Add the names to a list of logins
     * Login will be put in braces
     * @param array $logins
     * @return array
     */
    public static function _addNames($logins = [])
    {
        global $DIC;
        $db = $DIC->database();

        $logins = (array) $logins;

        $logins2 = [];
        foreach($logins as $login) {
            $logins2[$login] = $login;
        }

        $query = "SELECT firstname, lastname, login, ext_account FROM usr_data WHERE "
            . $db->in('login', $logins, false, 'text');
        $result = $db->query($query);

        while ($row = $db->fetchAssoc($result)) {
            if (isset($logins2[$row['login']])) {
                $logins2[$row['login']] = $row['lastname'] . ', ' . $row['firstname'] . ' [' . $row['login'] . ']';
            }
        }

        return array_values($logins2);
    }

    /**
     * Remove the names from a list of logins
     * Login will be taken out of braces
     * @param array $logins
     * @return array
     */
    public static function _removeNames($logins = [])
    {
        $logins = (array) $logins;

        $logins2 = [];
        foreach ($logins as $login) {
            $matches = [];
            if (preg_match('/.*\[(.+)\].*/', $login, $matches)) {
                $logins2[] = $matches[1];
            }
            else {
                $logins2[] = $login;
            }
        }

        return $logins2;
    }
}