<?php

/**
 * Class HpsProcessorException
 */
class HpsProcessorException extends HpsException
{
    public $transactionId = null;
    public $code          = null;
    public $details       = null;
    /**
     * HpsProcessorException constructor.
     *
     * @param string          $transactionId
     * @param null            $code
     * @param \Exception|null $message
     * @param null            $processorCode
     * @param null            $processorMessage
     * @param null            $innerException
     */
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
