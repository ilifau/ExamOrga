<?php
// Copyright (c) 2020 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

class ilExamOrgaDayTimeInputGUI extends ilFormPropertyGUI
{
    /** @var ilExamOrgaPlugin  */
    protected $plugin;

    protected $hours = null;
    protected $minutes = null;

    /**
     * Constructor
     * @param string $a_title   Title
     * @param string $a_postvar Post Variable
     */
    public function __construct($a_title = "", $a_postvar = "")
    {
        require_once(__DIR__ . '/../class.ilExamOrgaPlugin.php');
        $this->plugin = ilExamOrgaPlugin::getInstance();

        parent::__construct($a_title, $a_postvar);
        $this->setType("daytime");
    }

    /**
     * Insert property html
     *
     */
    public function render()
    {
        $tpl = $this->plugin->getTemplate("tpl.daytime.html", true, true);

        $tpl->setVariable("TXT_DELIM", $this->plugin->txt('time_delim'));
        $tpl->setVariable("TXT_SUFFIX", $this->plugin->txt('time_suffix'));

        $val =  ['  ' => '--'];
        for ($i = 0; $i <= 23; $i++) {
            $val[sprintf("%02d", $i)] = sprintf("%02d", $i);
        }
        $tpl->setVariable(
            "SELECT_HOURS",
            ilUtil::formSelect(
                $this->hours,
                $this->getPostVar() . "[hh]",
                $val,
                false,
                true,
                0,
                '',
                '',
                $this->getDisabled()
            )
        );

        $val =  ['  ' => '--'];
        for ($i = 0; $i <= 59; $i = $i + 5) {
            $val[sprintf("%02d", $i)] = sprintf("%02d", $i);
        }
        $tpl->setVariable(
            "SELECT_MINUTES",
            ilUtil::formSelect(
                $this->minutes,
                $this->getPostVar() . "[mm]",
                $val,
                false,
                true,
                0,
                '',
                '',
                $this->getDisabled()
            )
        );

        return $tpl->get();
    }

    /**
     * Check input, strip slashes etc. set alert, if input is not ok.
     *
     * @return	boolean		Input ok, true/false
     */
    public function checkInput()
    {
        $_POST[$this->getPostVar()]["hh"] = ilUtil::stripSlashes($_POST[$this->getPostVar()]["hh"]);
        $_POST[$this->getPostVar()]["mm"] = ilUtil::stripSlashes($_POST[$this->getPostVar()]["mm"]);
        return true;
    }

    /**
     * Get the value as array ['hh' => hour, 'mm' => minute]
     * @return array
     */
    public function getValue()
    {
        return [
            'hh' => isset($this->hours) ? sprintf("%02d", $this->hours) : null,
            'mm' => isset($this->minutes) ? sprintf("%02d", $this->minutes) : null,
        ];
    }

    /**
     * Set the value as array ['hh' => hour, 'mm' => minute]
     * (Must be an array because ilDclGenericMultiInputGUI calls this with post data)
     * @var array $a_value
     */
    public function setValue($a_value)
    {
        $hours = trim($a_value['hh']);
        $minutes = trim($a_value['mm']);

        $this->hours = (($hours != '') ? (int) $hours : null);
        $this->minutes = (($minutes != '') ? (int) $minutes : null);
    }

    /**
     * Set value from part of a posted array
     *
     * @param	array	$a_values	value array
     */
    public function setValueByArray($a_values)
    {
        $this->setValue($a_values[$this->getPostVar()]);
    }


    /**
     * Get the value as string (format 08:23)
     * @return string|null
     */
    public function getStringValue()
    {
        return self::_getString($this->getValue());
    }

    /**
     * Set the value as string (format 08:23)
     * @var string|null
     */
    public function setStringValue($a_value)
    {
        $this->setValue(self::_getArray($a_value));
    }


    /**
     * Get the string value from an array
     * @param array $a_array ['hh' => hour, 'mm' => minute]
     * @return string 'hh:mm'
     */
    public static function _getString($a_array)
    {
        $hours = trim($a_array['hh']);
        $minutes = trim($a_array['mm']);

        if ($hours != '') {
            return sprintf("%02d:%02d", (int) $hours, (int) $minutes);
        }
        else {
            return null;
        }
    }

    /**
     * Get the string value from an array
     * @param string $a_string
     * @return array ['hh' => hour, 'mm' => minute]
     */
    public static function _getArray($a_string)
    {
        $parts = explode(':', trim($a_string));
        return [
            'hh' => $parts[0],
            'mm' => $parts[1]
        ];
    }
}