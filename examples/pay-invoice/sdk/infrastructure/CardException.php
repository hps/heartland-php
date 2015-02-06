<?php

class CardException extends HpsException{
    public  $TransactionId = null;
    public  $ResultText = null;

    public function __construct($transactionId, $code, $message, $resultText = null) {
        $this->TransactionId = $transactionId;
        $this->ResultText = $resultText;
        parent::__construct($message, $code);
    }

}
