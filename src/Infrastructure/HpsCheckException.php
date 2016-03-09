<?php

class HpsCheckException extends HpsException
{
    public $transactionId = null;
    public $details       = null;
    public $code          = null;

    public function __construct($transactionId, $details, $code, $message = null)
    {
        $this->transactionId = $transactionId;
        $this->details = $details;
        $this->code = $code;
        $this->message = (string) $message;
    }
}
