<?php


class AVSResponseCodeHandler {
    private $avsResultCode;
    private $config;
    private $response;
    private $transaction;
    private $transactionId;
    private $ver;

    function __construct($response, $hpsChargeService=null, $config=null)
    {
        $this->config = $config;
        if(count($this->config->avsResponseErrors) == 0){
            return;
        }

        $this->transaction = $response->Transaction;
        $this->transactionId = $response->Header->GatewayTxnId;

        if(isset($this->transaction->CreditSale) && is_object($this->transaction->CreditSale)){
            $this->avsResultCode = $this->transaction->CreditSale->AVSRsltCode;
            $this->evaluate($hpsChargeService,'sale');
        }else if(isset($this->transaction->CreditAuth) && is_object($this->transaction->CreditAuth)){
            $this->avsResultCode = $this->transaction->CreditAuth->AVSRsltCode;
            $this->evaluate($hpsChargeService,'auth');
        }
    }

    function evaluate($hpsChargeService,$type){
        $exceptionFound = false;
        $code = "";
        $message = "";

        foreach ($this->config->avsResponseErrors as $c=>$m) {
            if($this->avsResultCode == $c){
                $code = $c;
                $message = $m;
                $exceptionFound = true;
            }
        }

        if($exceptionFound){
            $hpsChargeService->void($this->transactionId);
            throw new HpsException($message,$code);
        }
    }
} 