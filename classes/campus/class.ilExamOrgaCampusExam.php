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
     * @con_sequence         true
     * @con_is_notnull       true
     * @con_fieldtype        integer
     * @con_length           4
     */
    public $id;


    /**
     * @var integer
     * @con_has_field        true
     * @con_is_primary       false
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
     * Update the exam data
     */
    public static function updateExams()
    {
        global $DIC;
        $db = $DIC->fau()->staging()->database();

        $existing = [];
        /** @var self $exam */
        foreach (self::get() as $exam) {
            $existing[$exam->getDataHash()] = $exam;
        }

        $query = "
            SELECT e.*, i.fau_campo_person_id, i.sn AS nachname, i.given_name AS vorname
            FROM campo_exam e
            LEFT JOIN campo_exam_examiner p ON e.porgnr = p.porgnr
            LEFT JOIN identities i ON p.person_id = i.fau_campo_person_id
            ORDER BY porgnr            
            ";
        $result = $db->query($query);

        while ($row = $db->fetchAssoc($result)) {

            $exam = new self;
            $exam->porgnr = $row['porgnr'];
            $exam->pnr = $row['pnr'];
            $exam->psem = $row['psem'];
            $exam->ptermin = sprintf("%02d", $row['ptermin']);
            $exam->pdatum = $row['pdatum'];
            $exam->titel = $row['titel'];
            $exam->veranstaltung = $row['veranstaltung'];
            if (isset($row['fau_campo_person_id'])) {
                $exam->nachname = $row['nachname'];
                $exam->vorname = $row['vorname'];
            }
            if (isset($existing[$exam->getDataHash()])) {
                // existing record matches the data from campo
                unset($existing[$exam->getDataHash()]);
            }
            else {
                // save a non-existing record
                $exam->save();
            }
        }

        // delete the remaining existing records that no longer match with campo
        foreach ($existing as $exam) {
            $exam->delete();
        }
    }

    /**
     * Get a hash over all data except the id
     */
    public function getDataHash() {
        return md5(serialize([
            $this->porgnr,
            $this->pnr,
            $this->psem,
            $this->ptermin,
            $this->pdatum,
            $this->titel,
            $this->veranstaltung,
            $this->vorname,
            $this->nachname
        ]));
    }

    /**
     * Get the label of the exam
     * @return string
     */
    public function getLabel()
    {
        $semester = $this->psem;
        $year = (int)  substr($semester, 0, 4);
        $num = (int) substr($semester, 4, 1);

        switch ($num) {
            case 1:
                $semester = 'SoSe ' . $year;
                break;
            case 2:
                $semester = 'WiSe ' . $year;
                break;
        }

        return $this->porgnr . ' - '
            . (empty($this->nachname) ? '' : $this->nachname . ', ' . $this->vorname .  ': ')
            . $this->titel . ' ( ' . $this->pnr . ', '. $semester . ', Termin ' . $this->ptermin
            . (empty($this->pdatum) ? '' : ', ' . $this->pdatum)
            . ')'
            . (empty($this->veranstaltung) ? '' : ': ' . $this->veranstaltung);
    }

    /**
     * Extract the key (porgnr) from a generated label
     */
    public static function getKeyFromLabel($label)
    {
        $dashpos = strpos($label, ' - ');
        return (int) substr($label, 0, $dashpos);
    }

    /**
     * get a list of semesters that are near a given semester
     * @param $semester
     * @return array
     */
    public static function getNearSemesters($semester)
    {
        $year = (int)  substr($semester, 0, 4);
        $num = (int) substr($semester, 4, 1);

        switch ($num) {
            case 1:
                // current semester is summer
                return [
                    ($year - 1) . '2',
                    $semester,
                    $year . '2'
                ];
            case 2:
                // current semester is winter
                return [
                    $year . '1',
                    $semester,
                    ($year + 1) . '1',
                ];
            default:
                return [$semester];
        }
    }
}
