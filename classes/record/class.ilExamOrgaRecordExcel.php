<?php

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;

require_once (__DIR__ . '/class.ilExamOrgaRecord.php');

/**
 * Class ilExAssignmentStatusFile
 */
class ilExamOrgaRecordExcel extends ilExcel
{
    /** @var string */
    protected $format = self::FORMAT_XML;

    /** @var ilObjExamOrga */
    protected $object;

    /** @var ilExamOrgaPlugin */
    protected $plugin;

    /** @var ilExamOrgaField[] */
    protected $fields;

    /** @var ilExamOrgaRecord[] */
    protected $records = [];

    /** @var string */
    protected $error;

    /**
     * Initialize the data
     * @param ilObjExamOrga $object
     */
    public function init( $object)
    {
        $this->object = $object;
        $this->plugin = $object->plugin;

        $fields = $object->getAvailableFields();

        // put id at the beginning
        $this->fields['id'] = $fields['id'];
        unset($fields['id']);

        foreach ($fields as $field) {
            if ($field->isForExcel()) {
               $this->fields[$field->name] = $field;
            }
       }
    }

    /**
     * Init the data of the exercise members
     */
    protected function initRecords()
    {
        global $DIC;

        /** @var ilExamOrgaRecord $record */
        $recordList = ilExamOrgaRecord::getCollection();
        $recordList->where(['obj_id' => $this->object->getId()]);

        // limit to owned records
        if (!$this->object->canViewAllRecords()) {
            $recordList->where(['owner_id' => $DIC->user()->getId()]);
        }

        $recordList->orderBy('id');
        $this->records = $recordList->get();
    }


    /**
     * Get valid file formats
     *
     * @return array
     */
    public function getValidFormats() {
        return array(self::FORMAT_XML, self::FORMAT_BIFF);
    }


    /**
     * Get the filename that should be used
     * @return string
     */
    public function getFilename() {
        switch($this->format) {
            case self::FORMAT_BIFF:
                return "exams.xls";
            case self::FORMAT_XML:
            default:
                return "exams.xlsx";
        }
    }


    /**
     * Write the status of the users/teams to the status file
     * @param  $a_file
     * @return boolean
     */
    public function writeToFile($a_file) {
        try {
            $this->initRecords();
            $this->writeSheet();

            /** @var  PhpOffice\PhpSpreadsheet\Writer\Xlsx  $writer */
            $writer = IOFactory::createWriter($this->workbook, $this->format);
            $writer->save($a_file);
            return true;
        }
        catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    /**
     * Add the sheet for exercise members
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    protected function writeSheet() {
        $this->addSheet('exams');

        // write the title line
        $col = 0;
        foreach ($this->fields as $field) {
            $this->setCell(1, $col++, $field->name);
        }

        // write the record line
        $row = 2;
        foreach ($this->records as $record) {
            $col = 0;
            foreach ($this->fields as $field) {
                $this->setCell($row, $col++, $field->getExcelValue($record, $this));
            }
            $row++;
        }
    }

    /**
     * load the status of the users/teams from the status file
     * @param  $filename
     * @return bool
     */
    public function loadFromFile($filename) {
        $this->error = false;
        $this->records = [];
        try {
            if (file_exists($filename)) {
                $this->workbook = IOFactory::load($filename);
                $this->loadSheet();
                foreach ($this->records as $record) {
                    $record->save();
                }
                return true;
            }
            return false;
        }
        catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    /**
     * Load the sheet data for members
     * @throws ilExerciseException
     */
    protected function loadSheet() {
        $sheet = $this->getSheetAsArray();

        // get the column index
        $index = [];
        $titles = array_shift($sheet);
        foreach ($titles as $col => $title) {
            if (isset($this->fields[$title])) {
                $index[$title] = $col;
            }
            else {
                throw new Exception(sprintf($this->plugin->txt('excel_wrong_column_title'), $title));
            }
        }

        // read the records
        foreach ($sheet as $row => $rowdata) {

            /** @var ilExamOrgaRecord $record */
            if (isset($index['id']) && isset($rowdata[$index['id']])) {

                $id = $rowdata[$index['id']];
                $record = ilExamOrgaRecord::find($id);
                if ($record == null) {
                    throw new Exception(sprintf($this->plugin->txt('excel_record_not_found'), $id));
                }
                elseif (!$this->object->canEditRecord($record)) {
                    throw new Exception(sprintf($this->plugin->txt('excel_record_not_writeable'), $id));
                }
            }
            elseif (!$this->object->canAddRecord())  {
                throw new Exception($this->plugin->txt('excel_no_add_permission'), $title);
            }
            else {
                $record = new ilExamOrgaRecord();
                $record->obj_id = $this->object->getId();
            }

            foreach ($this->fields as $field) {
                if (!$field->setExcelValue($record, $this, $rowdata[$index[$field->name]])) {
                    throw new Exception(sprintf($this->plugin->txt('excel_wrong_value'), $field->name, $row + 2));
                }
            }
            $this->records[] = $record;
        }
    }


    /**
     * Check if an error was detected
     * @return bool
     */
    public function hasError() {
        return !empty($this->error);
    }

    /**
     * Check if updates are read from the file
     * @return bool
     */
    public function hasRecords() {
        return !empty($this->records);
    }

    /**
     * Get the info message for updates
     */
    public function getInfo() {
        if ($this->hasError()) {
            return sprintf($this->plugin->txt('excel_records_read_error'), $this->error);
        }
        elseif (!$this->hasRecords()) {
            return $this->plugin->txt('excel_no_records_read');
        }
        else {
            $list = [];
            foreach ($this->records as $record) {
                $list[] = $record->getTitle();
                return sprintf($this->plugin->txt('excel_records_read'), implode('<br /> ', $list));
            }
        }
    }
}