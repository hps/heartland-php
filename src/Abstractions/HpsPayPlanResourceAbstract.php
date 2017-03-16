<?php

/**
 * Class HpsPayPlanResourceAbstract
 */
abstract class HpsPayPlanResourceAbstract implements HpsPayPlanResourceInterface
{
    /** @var string|null */
    public $statusSetDate = null;

    /** @var string|null */
    public $creationDate = null;

    /** @var string|null */
    public $lastChangeDate = null;
    /**
     * @param $value
     *
     * @return bool
     */
    protected function isNotNullOrEmpty($value)
    {
        return $value !== null && !empty($value);
    }
    /**
     * @param       $class
     * @param array $params
     *
     * @return array
     */
    public function getEditableFieldsWithValues($class, $params = array())
    {
        $array = array_intersect_key(
            get_object_vars($this),
            array_flip(call_user_func($class.'::getEditableFields', $params))
        );
        return array_filter($array, array($this, 'isNotNullOrEmpty'));
    }
}
