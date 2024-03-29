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
class ilExamOrgaExamsInputGUI extends ilTextInputGUI
{
    /**
     * @var string
     */
    protected $semester;


    /**
     * ilExamOrgaExamsInputGUI constructor.
     * @param string $a_title
     * @param string $a_postvar
     */
    public function __construct($a_title = "", $a_postvar = "")
    {
        parent::__construct($a_title, $a_postvar);
        $this->setMulti(true);
        $this->setInlineStyle('width: 90%;');
    }

    /**
     * Set the semester for autocomplete restriction
     * @param string $semester
     */
    public function setAutocomplete($semester = null)
    {
        if (!empty($semester)) {
            $this->semester = $semester;
            $this->ctrl->setParameterByClass('ilexamorgaexamsinputgui', 'semester', $semester);
        }

        $ajax_url = $this->ctrl->getLinkTargetByClass(
            ['iluipluginroutergui', 'ilexamorgaexamsinputgui'],
            'doAutoComplete',
            '',
            true,
            false
        );
        $this->setDataSource($ajax_url);
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
        global $DIC;
        $db = $DIC->database();

        $term = $_REQUEST['term'];
        $semester = $_REQUEST['semester'];
        $fetchall = $_REQUEST['fetchall'];

        require_once (__DIR__ . '/../campus/class.ilExamOrgaCampusExam.php');
        $exams = ilExamOrgaCampusExam::getCollection()
            ->where(
                '(nachname LIKE ' . $db->quote($term . '%', 'text')
                . ' OR titel LIKE ' . $db->quote($term . '%', 'text')
                . ' OR veranstaltung LIKE ' . $db->quote($term . '%', 'text')
                . ')'
            );

        if (!empty($semester)) {
            $exams->where($db->in('psem', ilExamOrgaCampusExam::getNearSemesters($semester), false, 'text'));
        }

        $exams->orderBy('nachname, titel, psem, ptermin, veranstaltung')->limit(0, $fetchall ? 1000 : 10);

        $items = [];

        /** @var  ilExamOrgaCampusExam $exam */
        foreach($exams->get() as $exam) {
            $items[] = [
                'value'=> $exam->getLabel(),
                'label' => $exam->getLabel(),
                'id' => 'xamo_campus_id_' . $exam->id
            ];
        }

        $result_json['items'] = $items;
        $result_json['hasMoreResults'] = !$fetchall;

        echo json_encode($result_json);
        exit;
    }

     /**
     *
     * @return bool|void
     */
    public function checkInput()
    {
        // fault tolerance (field is multi, see constructor)
        if (!is_array($_POST[$this->getPostVar()])) {
            $_POST[$this->getPostVar()] = [];
        }

        require_once (__DIR__ . '/../campus/class.ilExamOrgaCampusExam.php');
        foreach ($_POST[$this->getPostVar()] as $value) {

            if (empty(trim($value))) {
                continue;
            }

            $exams = ilExamOrgaCampusExam::where(['porgnr' => (int) $value]);
            if (!$exams->hasSets()) {
                $this->setAlert(sprintf(ilExamOrgaPlugin::getInstance()->txt('exam_not_found'), $value));
                return false;
            }
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
        $exams = [];
        foreach (explode(',', (string) $value) as $porgnr) {
            if (!empty(trim($porgnr))) {
                /** @var ilExamOrgaCampusExam $examRecord */
                foreach(ilExamOrgaCampusExam::where(['porgnr' => $porgnr])->get() as $examRecord)
                $exams[] = $examRecord->getLabel();
            }
        }
        return array_unique($exams);
    }


    /**
     * Get the string representation from an array
     *
     * @param $value
     * @return string
     */
    public static function _getString($labels)
    {
        $keys = [];
        foreach ((array) $labels as $label) {
            $keys[] = ilExamOrgaCampusExam::getKeyFromLabel($label);
        }
        return implode(', ', array_unique($keys));
    }
}