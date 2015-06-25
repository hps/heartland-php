<?php

class HpsCreditException extends HpsException
{
    public $transactionId = null;
    public $code          = null;
    public $details       = null;

    public function __construct($transactionId, $code, $message, $issuerCode = null, $issuerMessage = null, $innerException = null)
    {
        $this->transactionId = $transactionId;
        $this->code = $code;

        if ($issuerCode != null || $issuerMessage != null) {
            $this->details = new HpsCreditExceptionDetails();
            $this->details->issuerResponseCode = $issuerCode;
            $this->details->issuerResponseText = $issuerMessage;
            parent::__construct($message, $code, $innerException);
        }
    }
}
