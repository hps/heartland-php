<?php

class HpsAltPaymentResponse extends HpsAuthorization
{
    public $error = null;

    public static function fromDict($rsp, $txnType, $returnType = 'HpsAltPaymentResponse')
    {
        $response = $rsp->Transaction->$txnType;

        $altPayment = parent::fromDict($rsp, $txnType, $returnType);

        if ($response->RspCode != 0) {
            $error = self::nvpToArray($response->Processor->Response);
            $altPayment->error = new HpsProcessorError();
            $altPayment->error->code = isset($error['Code']) ? $error['Code'] : null;
            $altPayment->error->message = isset($error['Message']) ? $error['Message'] : null;
            $altPayment->error->type = isset($error['Type']) ? $error['Type'] : null;
        }

        return $altPayment;
    }

    public static function nvpToArray($pairs)
    {
        $array = array();
        foreach ($pairs->NameValuePair as $pair) {
            $array[(string)$pair->Name] = (string)$pair->Value;
        }
        return $array;
    }
}
