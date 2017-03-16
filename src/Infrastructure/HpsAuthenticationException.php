<?php

/**
 * Class HpsAuthenticationException
 */
class HpsAuthenticationException extends HpsException
{
    /**
     * HpsAuthenticationException constructor.
     *
     * @param string $code
     * @param null   $message
     */
    public function __construct($code, $message)
    {
        $this->code = $code;
        $this->message = (string) $message;
    }
}
