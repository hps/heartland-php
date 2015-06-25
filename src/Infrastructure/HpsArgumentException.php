<?php

class HpsArgumentException extends HpsException
{
    public function __construct($message, $code, $innerException = null)
    {
        parent::__construct($message, $code, $innerException);
    }
}
