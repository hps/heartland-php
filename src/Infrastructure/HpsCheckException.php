<?php

/**
 * Class HpsCheckException
 */
class HpsCheckException extends HpsException
{
    public $transactionId = null;
    public $details       = null;
    public $code          = null;
    /**
     * HpsCheckException constructor.
     *
     * @param string          $transactionId
     * @param null            $details
     * @param \Exception|null $code
     * @param null            $message
     */
    public function __construct($transactionId, $details, $code, $message = null)
    {
        $this->transactionId = $transactionId;
        $this->details = $details;
        $this->code = $code;
        $this->message = (string) $message;
    }
}
