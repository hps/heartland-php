<?php

class HpsGatewayException extends HpsException
{
    public $code    = null;
    public $details = null;

    public function __construct($code, $message, $gatewayResponseCode = null, $gatewayResponseMessage = null, $innerException = null)
    {
        $this->code = $code;
        $this->message = (string) $message;

        if ($gatewayResponseCode != null || $gatewayResponseMessage != null) {
            $this->details = new HpsGatewayExceptionDetails();
            $this->details->gatewayResponseCode = $gatewayResponseCode;
            $this->details->gatewayResponseMessage = $gatewayResponseMessage;

            $this->innerException = $innerException;
        }
    }
}
