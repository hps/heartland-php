<?php

class HpsTokenData {
    public  $tokenValue         = null,
            $responseCode       = null,
            $responseMessage    = null;

    public function __construct($responseMessage = null){
        $this->responseMessage = $responseMessage;
    }
} 