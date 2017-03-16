<?php

/**
 * Class HpsApiConnectionException
 */
class HpsApiConnectionException extends HpsException
{
    /**
     * HpsApiConnectionException constructor.
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
