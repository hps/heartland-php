<?php

class HpsRefund extends HpsTransaction{
    public function __construct($header){
        parent::__construct($header);
    }

    static public function fromDict($rsp,$txnType){
        $refund = parent::fromDict($rsp,$txnType);
        $refund->responseCode = '00';
        $refund->responseText = '';
        return $refund;
    }
} 