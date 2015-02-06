<?php


class HpsReportTransactionDetails extends HpsAuthorization {
    public  $originalTransactionId  = null,
            $maskedCardNumber       = null,
            $transactionType        = null,
            $transactionDate        = null,
            $exceptions             = null;

    public function __construct($header){
        parent::__construct($header);
    }
} 