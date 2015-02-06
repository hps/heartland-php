<?php

class HpsReversal extends HpsTransaction{
    public  $avsResultCode  = null,
            $avsResultText  = null,
            $cvvResultCode  = null,
            $cvvResultText  = null,
            $cpcIndicator   = null;

    public function __construct($header){
        parent::__construct($header);
    }
} 