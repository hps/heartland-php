<?php

/**
 * Exception to be thrown when a property that
 * doesn't exist attempts to be set using
 * HpsBuilderAbstract::__call magic method.
 */
class HpsUnknownPropertyException extends HpsException
{
    /**
     * Instantiates new HpsUnknownPropertyException.
     *
     * @param object       $obj
     * @param string       $property
     * @param int          $code
     * @param HpsException $inner
     *
     * @return HpsUnknownPropertyException
     */
    public function __construct($obj, $property, $code = 0, HpsException $inner = null)
    {
        $className = get_class($obj);
        $message = 'Failed to set non-existent property "' . $property
                 . '" on class "' . $className . '"';
        parent::__construct($message, $code, $inner);
    }
}
