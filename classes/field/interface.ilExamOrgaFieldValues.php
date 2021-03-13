<?php

/**
 * Public Interface fpr a record of field values
 * Defines only the functions needed by the field types
 */
interface ilExamOrgaFieldValues
{
    /**
     * Get the id of the record
     * @return mixed
     */
    public function getId();

    /**
     * Get the value of a property
     * @param string $name
     * @return mixed
     */
    public function getValue($name);

    /**
     * Set the value of a property
     * @param $name
     * @param $value
     */
    public function setValue($name, $value);
}
