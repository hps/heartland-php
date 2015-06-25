<?php

class HpsException extends Exception
{
    public $code           = null;
    public $innerException = null;

    public function __construct($message, $code = null, $innerException = null)
    {
        $this->message = $message;
        if ($code != null) {
            $this->code = $code;
        }
        if ($innerException != null) {
            $this->innerException = $innerException;
        }
    }
}
