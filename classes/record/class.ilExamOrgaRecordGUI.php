<?php

require_once (__DIR__ . '/../class.ilExamOrgaBaseGUI.php');

/**
 * Class ilExamOrgaRecordGUI
 */
class ilExamOrgaRecordGUI extends ilExamOrgaBaseGUI
{
    /**
     * Execute a command
     * This should be overridden in the child classes
     */
    public function executeCommand()
    {
        $cmd = $this->ctrl->getCmd('listRecords');
        switch ($cmd)
        {
            case 'listRecords':
                $this->$cmd();
                return;

            default:
                // show unknown command
                $this->tpl->setContent('unknown command: ' . $cmd);
                return;
        }
    }

    /**
     * Show the list of records
     */
    protected function listRecords()
    {
        if (!$this->object->canViewAllRecords() && ! $this->object->canAddRecord()) {
            ilUtil::sendFailure($this->plugin->txt('no_record_access'));
            return;
        }



    }


}