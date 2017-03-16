<?php

/**
 * Class HpsArgumentException
 */
class HpsArgumentException extends HpsException
{
    /**
     * HpsArgumentException constructor.
     *
     * @param string $message
     * @param null   $code
     * @param null   $innerException
     */
    public function __construct($message, $code, $innerException = null)
    {
        parent::__construct($message, $code, $innerException);
    }
}
