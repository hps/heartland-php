<?php

/**
 * Class HpsAltPaymentSale
 */
class HpsAltPaymentSale extends HpsAltPaymentResponse
{
    public $status = null;
    public $statusMessage = null;
    /**
     * @param        $rsp
     * @param        $txnType
     * @param string $returnType
     *
     * @return mixed
     */
    public static function fromDict($rsp, $txnType, $returnType = 'HpsAltPaymentSale')
    {
        $charge = $rsp->Transaction->$txnType;

        $sale = parent::fromDict($rsp, $txnType, $returnType);

        $sale->status = isset($charge->Status) ? (string)$charge->Status : null;
        $sale->statusMessage = isset($charge->StatusMessage) ? (string)$charge->StatusMessage : null;

        return $sale;
    }
}
