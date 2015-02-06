<?php


class HpsAuthorization extends HpsTransaction {
    public  $avsResultCode      = null,
            $avsResultText      = null,
            $cvvResultCode      = null,
            $cvvResultText      = null,
            $cpcIndicator       = null,
            $authorizationCode  = null,
            $authorizedAmount   = null,
            $cardType           = null,
            $tokenData          = null;

    public function __construct($header){
        parent::__construct($header);
    }
} 