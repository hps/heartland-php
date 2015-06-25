<?php

abstract class HpsPayPlanResourceAbstract implements HpsPayPlanResourceInterface
{
    /** @var string|null */
    public $statusSetDate = null;

    /** @var string|null */
    public $creationDate = null;

    /** @var string|null */
    public $lastChangeDate = null;

    protected function isNotNull($value)
    {
        return $value !== null;
    }

    public function getEditableFieldsWithValues($class, $params = array())
    {
        $array = array_intersect_key(
            get_object_vars($this),
            array_flip(call_user_func_array($class.'::getEditableFields', $params))
        );
        return array_filter($array, array($this, 'isNotNull'));
    }
}
