<?php

/**
 * Class HpsDebitService
 */
class HpsDebitService extends HpsSoapGatewayService
{
    /**
     * HpsDebitService constructor.
     *
     * @param null $config
     */
    public function __construct($config = null)
    {
        parent::__construct($config);
    }

    /**
     * The Debit Add Value transaction adds value to a stored value card. The transaction is placed in the current
     * open batch. If a batch is not open, this transaction creates an open batch.
     *
     * @param double                $amount              Authorization amount.
     * @param string                $currency            Currency ("usd")
     * @param string                $trackData           Track data read from the card by the card reader.
     * @param string                $pinBlock            PIN block.
     * @param HpsEncryptionData     $encryptionData      E3 encryption data group.
     * @param bool                  $allowDuplicates     Indicates whether to allow duplicate transactions.
     * @param HpsCardHolder         $cardHolder          Card holder information.
     * @param HpsTransactionDetails $details             Group containing additional transaction fields to be included in detail reporting.
     * @param string                $clientTransactionId Optional client transaction ID.
     *
     * @return HpsDebitAddValue The AddValue (Authorization) response.
     */
    public function addValue($amount, $currency, $trackData, $pinBlock, HpsEncryptionData $encryptionData = null, $allowDuplicates = false, HpsCardHolder $cardHolder = null, HpsTransactionDetails $details = null, $clientTransactionId = null)
    {
        HpsInputValidation::checkAmount($amount);
        HpsInputValidation::checkCurrency($currency);

        /* Build the transaction request. */
        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsGiftCard = $xml->createElement('hps:DebitAddValue');
        $hpsBlock1 = $xml->createElement('hps:Block1');

        $hpsBlock1->appendChild($xml->createElement('hps:TrackData', $trackData));
        $hpsBlock1->appendChild($xml->createElement('hps:AllowDup', ($allowDuplicates ? 'Y' : 'N')));
        if ($cardHolder != null) {
            $hpsBlock1->appendChild($this->_hydrateCardHolderData($cardHolder, $xml));
        }
        $hpsBlock1->appendChild($xml->createElement('hps:Amt', $amount));
        $hpsBlock1->appendChild($xml->createElement('hps:PinBlock', $pinBlock));
        if ($encryptionData != null) {
            $hpsBlock1->appendChild($this->_hydrateEncryptionData($encryptionData, $xml));
        }
        if ($details != null) {
            $hpsBlock1->appendChild($this->_hydrateAdditionalTxnFields($details, $xml));
        }

        $hpsGiftCard->appendChild($hpsBlock1);
        $hpsTransaction->appendChild($hpsGiftCard);

        return $this->_submitTransaction($hpsTransaction, 'DebitAddValue', $clientTransactionId);
    }

    /**
     * A Debit Return transaction returns funds to the cardholder. The transaction is generally used as a
     * counterpart to a Debit Charge transaction that needs to be reversed. The Debit Return transaction is
     * placed in the current open batch. If a batch is not open, this transaction create an open batch.
     *
     * @param string                $transactionId       The gateway transaciton ID of the charge to be returned.
     * @param double                $amount              Authorization amount.
     * @param string                $trackData           Track data read from the card by the card reader.
     * @param string                $pinBlock            PIN block.
     * @param bool                  $allowDuplicates     Indicates whether to allow duplicate transactions.
     * @param HpsCardHolder         $cardHolder          Card holder information.
     * @param HpsEncryptionData     $encryptionData      E3 encryption data group.
     * @param HpsTransactionDetails $details             Group containing additional transaction fields to be included in detail reporting.
     * @param string                $clientTransactionId Optional client transaction ID.
     *
     * @return HpsDebitReturn The Return (Authorization) results.
     */
    public function returnDebit($transactionId, $amount, $trackData, $pinBlock, $allowDuplicates = false, HpsCardHolder $cardHolder = null, HpsEncryptionData $encryptionData = null, HpsTransactionDetails $details = null, $clientTransactionId = null)
    {
        HpsInputValidation::checkAmount($amount);

        /* Build the transaction request. */
        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsGiftCard = $xml->createElement('hps:DebitReturn');
        $hpsBlock1 = $xml->createElement('hps:Block1');

        $hpsBlock1->appendChild($xml->createElement('hps:TrackData', $trackData));
        $hpsBlock1->appendChild($xml->createElement('hps:AllowDup', ($allowDuplicates ? 'Y' : 'N')));
        if ($cardHolder != null) {
            $hpsBlock1->appendChild($this->_hydrateCardHolderData($cardHolder, $xml));
        }
        $hpsBlock1->appendChild($xml->createElement('hps:Amt', $amount));
        $hpsBlock1->appendChild($xml->createElement('hps:PinBlock', $pinBlock));
        if ($encryptionData != null) {
            $hpsBlock1->appendChild($this->_hydrateEncryptionData($encryptionData, $xml));
        }
        if ($details != null) {
            $hpsBlock1->appendChild($this->_hydrateAdditionalTxnFields($details, $xml));
        }

        $hpsGiftCard->appendChild($hpsBlock1);
        $hpsTransaction->appendChild($hpsGiftCard);

        return $this->_submitTransaction($hpsTransaction, 'DebitReturn', $clientTransactionId);
    }

    /**
     * A Debit Reversal transaction reverses a Debit Charge or Debit Return transaction.
     *
     * @param string                $transactionId       The gateway transaciton ID of the charge to be reversed.
     * @param double                $amount              Authorization amount.
     * @param string                $trackData           Track data read from the card by the card reader.
     * @param double                $authorizedAmount    Settlement amount or New Authorization amount after reversal occures.
     * @param HpsEncryptionData     $encryptionData      E3 encryption data group.
     * @param HpsTransactionDetails $details             Group containing additional transaction fields to be included in detail reporting.
     * @param string                $clientTransactionId Optional client transaction ID.
     *
     * @return HpsDebitReversal The reversal result.
     */
    public function reverse($transactionId, $amount, $trackData, $authorizedAmount = null, HpsEncryptionData $encryptionData = null, HpsTransactionDetails $details = null, $clientTransactionId = null)
    {
        HpsInputValidation::checkAmount($amount);

        /* Build the transaction request. */
        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsGiftCard = $xml->createElement('hps:DebitReversal');
        $hpsBlock1 = $xml->createElement('hps:Block1');

        $hpsBlock1->appendChild($xml->createElement('hps:GatewayTxnId', $transactionId));
        $hpsBlock1->appendChild($xml->createElement('hps:TrackData', $trackData));
        $hpsBlock1->appendChild($xml->createElement('hps:Amt', $amount));
        if ($encryptionData != null) {
            $hpsBlock1->appendChild($this->_hydrateEncryptionData($encryptionData, $xml));
        }
        if ($details != null) {
            $hpsBlock1->appendChild($this->_hydrateAdditionalTxnFields($details, $xml));
        }


        if (isset($authorizedAmount)) {
            $hpsBlock1->appendChild($xml->createElement('hps:authAmt', $authorizedAmount));
            $hpsBlock1->appendChild($xml->createElement('hps:authAmtSpecified', true));
        }

        $hpsGiftCard->appendChild($hpsBlock1);
        $hpsTransaction->appendChild($hpsGiftCard);

        $rsp = $this->_submitTransaction($hpsTransaction, 'DebitReversal', $clientTransactionId);
        $rsp->responseCode = '00';
        $rsp->responseText = '';

        return $rsp;
    }

    /**
     * A Debit Charge transaction performs a sale purchased with a Debit Card. The Debit Charge is placed
     * in the current open batch. If a batch is not open, this transaction creates an open batch.
     *
     * @param double                $amount              Authorization amount.
     * @param string                $currency            Currency ("usd")
     * @param string                $trackData           Track data read from the card by the card reader.
     * @param string                $pinBlock            PIN block.
     * @param HpsEncryptionData     $encryptionData      E3 encryption data group.
     * @param bool                  $allowDuplicates     Indicates whether to allow duplicate transactions.
     * @param double                $cashBackAmount      Contains the portion of the amount that is cash back.
     * @param bool                  $allowPartialAuth    Indicate whether to allow partial authorization.
     * @param HpsCardHolder         $cardHolder          Card holder information.
     * @param HpsTransactionDetails $details             Group containing additional transaction fields to be included in detail reporting.
     * @param string                $clientTransactionId Optional client transaction ID.
     *
     * @return HpsDebitSale The Debit Charge result.
     */
    public function charge($amount, $currency, $trackData, $pinBlock, HpsEncryptionData $encryptionData = null, $allowDuplicates = false, $cashBackAmount = null, $allowPartialAuth = false, HpsCardHolder $cardHolder = null, HpsTransactionDetails $details = null, $clientTransactionId = null)
    {
        HpsInputValidation::checkAmount($amount);
        HpsInputValidation::checkCurrency($currency);

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsGiftCard = $xml->createElement('hps:DebitSale');
        $hpsBlock1 = $xml->createElement('hps:Block1');

        $hpsBlock1->appendChild($xml->createElement('hps:TrackData', $trackData));
        $hpsBlock1->appendChild($xml->createElement('hps:AllowDup', ($allowDuplicates ? 'Y' : 'N')));
        if ($cardHolder != null) {
            $hpsBlock1->appendChild($this->_hydrateCardHolderData($cardHolder, $xml));
        }
        $hpsBlock1->appendChild($xml->createElement('hps:Amt', $amount));
        $hpsBlock1->appendChild($xml->createElement('hps:CashbackAmtInfo', isset($cashBackAmount) ? $cashBackAmount : 0));
        $hpsBlock1->appendChild($xml->createElement('hps:AllowPartialAuth', ($allowPartialAuth ? 'Y' : 'N')));
        $hpsBlock1->appendChild($xml->createElement('hps:PinBlock', $pinBlock));
        if ($encryptionData != null) {
            $hpsBlock1->appendChild($this->_hydrateEncryptionData($encryptionData, $xml));
        }
        if ($details != null) {
            $hpsBlock1->appendChild($this->_hydrateAdditionalTxnFields($details, $xml));
        }

        $hpsGiftCard->appendChild($hpsBlock1);
        $hpsTransaction->appendChild($hpsGiftCard);

        return $this->_submitTransaction($hpsTransaction, 'DebitSale', $clientTransactionId);
    }
    /**
     * @param $response
     * @param $expectedType
     *
     * @throws \HpsAuthenticationException
     * @throws \HpsGatewayException
     * @throws null
     */
    private function _processChargeGatewayResponse($response, $expectedType)
    {
        $gatewayRspCode = (isset($response->Header->GatewayRspCode) ? $response->Header->GatewayRspCode : null);
        $transactionId = (isset($response->Header->GatewayTxnId) ? $response->Header->GatewayTxnId : null);

        if ($gatewayRspCode == '0') {
            return;
        }

        if ($gatewayRspCode == '30') {
            try {
                $this->reverse($transactionId, $this->_amount, $this->_currency);
            } catch (Exception $e) {
                throw new HpsGatewayException(
                    HpsExceptionCodes::GATEWAY_TIMEOUT_REVERSAL_ERROR,
                    'Error occurred while reversing a charge due to HPS gateway timeout',
                    $e,
                    null,
                    null,
                    $transactionId
                );
            }
        }

        HpsGatewayResponseValidation::checkResponse($response, $expectedType);
    }
    /**
     * @param $response
     * @param $expectedType
     *
     * @throws \HpsCreditException
     * @throws null
     */
    private function _processChargeIssuerResponse($response, $expectedType)
    {
        $transactionId = (isset($response->Header->GatewayTxnId) ? $response->Header->GatewayTxnId : null);
        $item = $response->Transaction->$expectedType;

        if ($item != null) {
            $responseCode = (isset($item->RspCode) ? $item->RspCode : null);
            $responseText = (isset($item->RspText) ? $item->RspText : null);

            if ($responseCode != null) {
                // check if we need to do a reversal
                if ($responseCode == '91') {
                    try {
                        $this->reverse($transactionId, $this->_amount, $this->_currency);
                    } catch (HpsGatewayException $e) {
                        // if the transaction wasn't found; throw the original timeout exception
                        if ($e->details->gatewayResponseCode == 3) {
                            HpsIssuerResponseValidation::checkResponse($transactionId, $responseCode, $responseText);
                        }
                        throw new HpsCreditException(
                            $transactionId,
                            HpsExceptionCodes::ISSUER_TIMEOUT_REVERSAL_ERROR,
                            'Error occurred while reversing a charge due to HPS issuer timeout',
                            $e
                        );
                    } catch (HpsException $e) {
                        throw new HpsCreditException(
                            $transactionId,
                            HpsExceptionCodes::ISSUER_TIMEOUT_REVERSAL_ERROR,
                            'Error occurred while reversing a charge due to HPS issuer timeout',
                            $e
                        );
                    }
                }
                HpsIssuerResponseValidation::checkResponse($transactionId, $responseCode, $responseText);
            }
        }
    }
    /**
     * @param      $transaction
     * @param      $txnType
     * @param null $clientTxnId
     * @param null $cardData
     *
     * @return null
     * @throws \HpsCreditException
     * @throws \HpsException
     * @throws \HpsGatewayException
     */
    private function _submitTransaction($transaction, $txnType, $clientTxnId = null, $cardData = null)
    {
        $options = array();
        if ($clientTxnId !== null) {
            $options['clientTransactionId'] = $clientTxnId;
        }

        try {
            $response = $this->doRequest($transaction, $options);
        } catch (HpsException $e) {
            if ($e->innerException != null && $e->innerException->getMessage() == 'gateway_time-out') {
                if ($txnType == 'DebitSale') {
                    try {
                        $this->reverse($cardData, $this->_amount, $this->_currency);
                    } catch (Exception $e) {
                        throw new HpsGatewayException('0', HpsExceptionCodes::GATEWAY_TIMEOUT_REVERSAL_ERROR);
                    }
                }
                throw new HpsException('An error occurred and the gateway has timed out', 'gateway_timeout', $e, 'gateway_timeout');
            }
            throw $e;
        }

        $this->_processChargeGatewayResponse($response, $txnType);
        $this->_processChargeIssuerResponse($response, $txnType);

        $rvalue = null;
        switch ($txnType) {
            case 'DebitSale':
                $rvalue = HpsDebitSale::fromDict($response, $txnType);
                break;
            case 'DebitAddValue':
                $rvalue = HpsDebitAddValue::fromDict($response, $txnType);
                break;
            case 'DebitReturn':
                $rvalue = HpsDebitReturn::fromDict($response, $txnType);
                break;
            case 'DebitReversal':
                $rvalue = HpsDebitReversal::fromDict($response, $txnType);
                break;
            default:
                break;
        }

        return $rvalue;
    }
}
