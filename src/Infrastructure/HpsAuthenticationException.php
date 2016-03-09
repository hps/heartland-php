<?php

class HpsAuthenticationException extends HpsException
{
    public function __construct($code, $message)
    {
        $this->code = $code;
        $this->message = (string) $message;
    }
}
