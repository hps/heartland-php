<?php

class HpsReportTransactionSummary extends HpsTransaction{
    public  $amount                 = null,
            $originalTransactionId  = null,
            $maskedCardNumber       = null,
            $transactionType        = null,
            $transactionDate        = null,
            $exceptions             = null;
} 