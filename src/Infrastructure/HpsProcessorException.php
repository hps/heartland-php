<?php

class HpsProcessorException extends HpsException
{
    public $transactionId = null;
    public $code          = null;
    public $details       = null;

    public function __construct($transactionId, $code, $message, $processorCode = null, $processorMessage = null, $innerException = null)
    {
        $this->transactionId = $transactionId;
        $this->code = $code;

        if ($processorCode != null || $processorMessage != null) {
            $this->details = new HpsProcessorExceptionDetails();
            $this->details->processorResponseCode = $processorCode;
            $this->details->processorResponseText = $processorMessage;
            parent::__construct($message, $code, $innerException);
        }
    }
}
