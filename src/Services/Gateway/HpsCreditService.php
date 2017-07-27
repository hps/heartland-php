<?php

/**
 * Class HpsCreditService
 */
class HpsCreditService extends HpsSoapGatewayService
{
    /**
     * HpsCreditService constructor.
     *
     * @param \HpsServicesConfig|null $config
     */
    public function __construct(HpsServicesConfig $config = null)
    {
        parent::__construct($config);
    }
    /**
     * @param      $amount
     * @param      $currency
     * @param      $cardOrToken
     * @param null $cardHolder
     * @param bool $requestMultiUseToken
     * @param null $details
     * @param null $txnDescriptor
     * @param bool $allowPartialAuth
     * @param bool $cpcReq
     * @param null $convenienceAmtInfo
     * @param null $shippingAmtInfo
     *
     * @return array|null
     * @throws \HpsException
     * @throws \HpsGatewayException
     * @throws \HpsInvalidRequestException
     */
    public function authorize($amount, $currency, $cardOrToken, $cardHolder = null, $requestMultiUseToken = false, $details = null, $txnDescriptor = null, $allowPartialAuth = false, $cpcReq = false, $convenienceAmtInfo = null, $shippingAmtInfo = null)
    {
        HpsInputValidation::checkCurrency($currency);
        $this->_currency = $currency;
        $this->_amount = HpsInputValidation::checkAmount($amount);

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsCreditAuth = $xml->createElement('hps:CreditAuth');
        $hpsBlock1 = $xml->createElement('hps:Block1');

        $hpsBlock1->appendChild($xml->createElement('hps:AllowDup', 'Y'));
        $hpsBlock1->appendChild($xml->createElement('hps:AllowPartialAuth', ($allowPartialAuth ? 'Y' : 'N')));
        $hpsBlock1->appendChild($xml->createElement('hps:Amt', $amount));
        //update convenienceAmtInfo if passed
        if ($convenienceAmtInfo != null && $convenienceAmtInfo != '') {
            $hpsBlock1->appendChild($xml->createElement('hps:ConvenienceAmtInfo', $convenienceAmtInfo));
        }

         //update shippingAmtInfo if passed
        if ($shippingAmtInfo != null && $shippingAmtInfo != '') {
            $hpsBlock1->appendChild($xml->createElement('hps:ShippingAmtInfo', $shippingAmtInfo));
        }

        if ($cardHolder != null) {
            $hpsBlock1->appendChild($this->_hydrateCardHolderData($cardHolder, $xml));
        }
        if ($details != null) {
            $hpsBlock1->appendChild($this->_hydrateAdditionalTxnFields($details, $xml));
        }
        if ($txnDescriptor != null && $txnDescriptor != '') {
            $hpsBlock1->appendChild($xml->createElement('hps:TxnDescriptor', $txnDescriptor));
        }

        $cardData = $xml->createElement('hps:CardData');
        if ($cardOrToken instanceof HpsCreditCard) {
            $cardData->appendChild($this->_hydrateManualEntry($cardOrToken, $xml));
        } else {
            $cardData->appendChild($this->_hydrateTokenData($cardOrToken, $xml));
        }
        $cardData->appendChild($xml->createElement('hps:TokenRequest', ($requestMultiUseToken) ? 'Y' : 'N'));
        if ($cpcReq) {
            $hpsBlock1->appendChild($xml->createElement('hps:CPCReq', 'Y'));
        }

        $hpsBlock1->appendChild($cardData);
        $hpsCreditAuth->appendChild($hpsBlock1);
        $hpsTransaction->appendChild($hpsCreditAuth);

        return $this->_submitTransaction($hpsTransaction, 'CreditAuth', (isset($details->clientTransactionId) ? $details->clientTransactionId : null), $cardOrToken);
    }
    /**
     * @param      $transactionId
     * @param null $amount
     * @param null $gratuity
     * @param null $clientTransactionId
     * @param null $directMarketData
     *
     * @return array|null
     * @throws \HpsArgumentException
     * @throws \HpsGatewayException
     */
    public function capture($transactionId, $amount = null, $gratuity = null, $clientTransactionId = null, $directMarketData = null)
    {
        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsCreditAddToBatch = $xml->createElement('hps:CreditAddToBatch');

        $hpsCreditAddToBatch->appendChild($xml->createElement('hps:GatewayTxnId', $transactionId));
        if ($amount != null) {
            $amount = sprintf("%0.2f", round($amount, 3));
            $hpsCreditAddToBatch->appendChild($xml->createElement('hps:Amt', $amount));
        }
        if ($gratuity != null) {
            $hpsCreditAddToBatch->appendChild($xml->createElement('hps:GratuityAmtInfo', $gratuity));
        }

        if ($directMarketData != null && $directMarketData->invoiceNumber != null) {
            $hpsCreditAddToBatch->appendChild($this->_hydrateDirectMarketData($directMarketData, $xml));
        }

        $hpsTransaction->appendChild($hpsCreditAddToBatch);
        $options = array();
        if ($clientTransactionId != null) {
            $options['clientTransactionId'] = $clientTransactionId;
        }
        $response = $this->doRequest($hpsTransaction, $options);
        $this->_processChargeGatewayResponse($response, 'CreditAddToBatch');

        return $this->get($transactionId);
    }
    /**
     * @param      $amount
     * @param      $currency
     * @param      $cardOrToken
     * @param null $cardHolder
     * @param bool $requestMultiUseToken
     * @param null $details
     * @param null $txnDescriptor
     * @param bool $allowPartialAuth
     * @param bool $cpcReq
     * @param null $directMarketData
     * @param null $convenienceAmtInfo
     * @param null $shippingAmtInfo
     *
     * @return array|null
     * @throws \HpsException
     * @throws \HpsGatewayException
     * @throws \HpsInvalidRequestException
     */
    public function charge($amount, $currency, $cardOrToken, $cardHolder = null, $requestMultiUseToken = false, $details = null, $txnDescriptor = null, $allowPartialAuth = false, $cpcReq = false, $directMarketData = null, $convenienceAmtInfo = null, $shippingAmtInfo = null)
    {
        HpsInputValidation::checkCurrency($currency);
        $this->_currency = $currency;
        $this->_amount = HpsInputValidation::checkAmount($amount);

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsCreditSale = $xml->createElement('hps:CreditSale');
        $hpsBlock1 = $xml->createElement('hps:Block1');

        $hpsBlock1->appendChild($xml->createElement('hps:AllowDup', 'Y'));
        $hpsBlock1->appendChild($xml->createElement('hps:AllowPartialAuth', ($allowPartialAuth ? 'Y' : 'N')));
        $hpsBlock1->appendChild($xml->createElement('hps:Amt', $amount));
        //update convenienceAmtInfo if passed
        if ($convenienceAmtInfo != null && $convenienceAmtInfo != '') {
            $hpsBlock1->appendChild($xml->createElement('hps:ConvenienceAmtInfo', $convenienceAmtInfo));
        }

         //update shippingAmtInfo if passed
        if ($shippingAmtInfo != null && $shippingAmtInfo != '') {
            $hpsBlock1->appendChild($xml->createElement('hps:ShippingAmtInfo', $shippingAmtInfo));
        }
        if ($cardHolder != null) {
            $hpsBlock1->appendChild($this->_hydrateCardHolderData($cardHolder, $xml));
        }
        if ($details != null) {
            $hpsBlock1->appendChild($this->_hydrateAdditionalTxnFields($details, $xml));
        }
        if ($txnDescriptor != null && $txnDescriptor != '') {
            $hpsBlock1->appendChild($xml->createElement('hps:TxnDescriptor', $txnDescriptor));
        }

        $cardData = $xml->createElement('hps:CardData');
        if ($cardOrToken instanceof HpsCreditCard) {
            $cardData->appendChild($this->_hydrateManualEntry($cardOrToken, $xml));
        } else {
            $cardData->appendChild($this->_hydrateTokenData($cardOrToken, $xml));
        }
        if ($cpcReq) {
            $hpsBlock1->appendChild($xml->createElement('hps:CPCReq', 'Y'));
        }
        $cardData->appendChild($xml->createElement('hps:TokenRequest', ($requestMultiUseToken) ? 'Y' : 'N'));

        if ($directMarketData != null && $directMarketData->invoiceNumber != null) {
            $hpsBlock1->appendChild($this->_hydrateDirectMarketData($directMarketData, $xml));
        }

        $hpsBlock1->appendChild($cardData);
        $hpsCreditSale->appendChild($hpsBlock1);
        $hpsTransaction->appendChild($hpsCreditSale);

        return $this->_submitTransaction($hpsTransaction, 'CreditSale', (isset($details->clientTransactionId) ? $details->clientTransactionId : null), $cardOrToken);
    }
    /**
     * @param      $schedule
     * @param      $amount
     * @param      $cardOrTokenOrPMKey
     * @param null $cardHolder
     * @param bool $oneTime
     * @param null $details
     *
     * @return array|null
     * @throws \HpsException
     * @throws \HpsGatewayException
     * @throws \HpsInvalidRequestException
     */
    public function recurring($schedule, $amount, $cardOrTokenOrPMKey, $cardHolder = null, $oneTime = false, $details = null)
    {
        $this->_amount = HpsInputValidation::checkAmount($amount);

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsRecurringBilling = $xml->createElement('hps:RecurringBilling');
        $hpsBlock1 = $xml->createElement('hps:Block1');

        $hpsBlock1->appendChild($xml->createElement('hps:AllowDup', 'Y'));
        $hpsBlock1->appendChild($xml->createElement('hps:Amt', $amount));
        if ($cardHolder != null) {
            $hpsBlock1->appendChild($this->_hydrateCardHolderData($cardHolder, $xml));
        }
        if ($details != null) {
            $hpsBlock1->appendChild($this->_hydrateAdditionalTxnFields($details, $xml));
        }

        if ($cardOrTokenOrPMKey instanceof HpsCreditCard) {
            $cardData = $xml->createElement('hps:CardData');
            $cardData->appendChild($this->_hydrateManualEntry($cardOrTokenOrPMKey, $xml));
            $hpsBlock1->appendChild($cardData);
        } else if ($cardOrTokenOrPMKey instanceof HpsTokenData) {
            $cardData = $xml->createElement('hps:CardData');
            $cardData->appendChild($this->_hydrateTokenData($cardOrTokenOrPMKey, $xml));
            $hpsBlock1->appendChild($cardData);
        } else {
            $hpsBlock1->appendChild($xml->createElement('hps:PaymentMethodKey', $cardOrTokenOrPMKey));
        }

        $id = $schedule;
        if ($schedule instanceof HpsPayPlanSchedule) {
            $id = $schedule->scheduleIdentifier;
        }
        $recurringData = $xml->createElement('hps:RecurringData');
        $recurringData->appendChild($xml->createElement('hps:ScheduleID', $id));
        $recurringData->appendChild($xml->createElement('hps:OneTime', ($oneTime ? 'Y' : 'N')));

        $hpsBlock1->appendChild($recurringData);
        $hpsRecurringBilling->appendChild($hpsBlock1);
        $hpsTransaction->appendChild($hpsRecurringBilling);

        return $this->_submitTransaction($hpsTransaction, 'RecurringBilling', (isset($details->clientTransactionId) ? $details->clientTransactionId : null), $cardOrTokenOrPMKey);
    }
    /**
     * @param $transactionId
     * @param $cpcData
     *
     * @return array|null
     * @throws \HpsException
     * @throws \HpsGatewayException
     */
    public function cpcEdit($transactionId, $cpcData)
    {
        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsPosCreditCPCEdit = $xml->createElement('hps:CreditCPCEdit');
        $hpsPosCreditCPCEdit->appendChild($xml->createElement('hps:GatewayTxnId', $transactionId));
        $hpsPosCreditCPCEdit->appendChild($this->_hydrateCPCData($cpcData, $xml));
        $hpsTransaction->appendChild($hpsPosCreditCPCEdit);

        return $this->_submitTransaction($hpsTransaction, 'CreditCPCEdit');
    }
    /**
     * @param      $transactionId
     * @param null $amount
     * @param null $gratuity
     * @param null $clientTransactionId
     *
     * @return array|null
     * @throws \HpsException
     * @throws \HpsGatewayException
     */
    public function edit($transactionId, $amount = null, $gratuity = null, $clientTransactionId = null)
    {
        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsCreditTxnEdit = $xml->createElement('hps:CreditTxnEdit');

        $hpsCreditTxnEdit->appendChild($xml->createElement('hps:GatewayTxnId', $transactionId));
        if ($amount != null) {
            $amount = sprintf('%0.2f', round($amount, 3));
            $hpsCreditTxnEdit->appendChild($xml->createElement('hps:Amt', $amount));
        }
        if ($gratuity != null) {
            $hpsCreditTxnEdit->appendChild($xml->createElement('hps:GratuityAmtInfo', $gratuity));
        }

        $hpsTransaction->appendChild($hpsCreditTxnEdit);
        $trans = $this->_submitTransaction($hpsTransaction, 'CreditTxnEdit', $clientTransactionId);

        $trans->responseCode = '00';
        $trans->responseText = '';

        return $trans;
    }

    /** builds soap transaction for Portico so that  expiration dates can be updated for expired cards with a new current issuance
     * @param string $tokenValue
     * @param int    $expMonth 1-12 padding will be handled automatically
     * @param int    $expYear  must be 4 digits.
     *
     * @return \HpsManageTokensResponse
     * @throws \HpsException
     * @throws \HpsGatewayException
     */
    public function updateTokenExpiration($tokenValue, $expMonth, $expYear)  {
        // new DOM
        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsManageTokens = $xml->createElement('hps:ManageTokens');

        $hpsManageTokens->appendChild($xml->createElement('hps:TokenValue', trim((string)$tokenValue)));

        $hpsTokenActions = $xml->createElement('hps:TokenActions');
        $hpsSet = $xml->createElement('hps:Set');
        $hpsAttribute = $xml->createElement('hps:Attribute');

            $hpsAttribute->appendChild($xml->createElement('hps:Name', 'ExpMonth'));
            $hpsAttribute->appendChild($xml->createElement('hps:Value', (string)sprintf("%'.02d", (int)$expMonth)));

        $hpsSet->appendChild($hpsAttribute);

        $hpsAttribute = $xml->createElement('hps:Attribute');

            $hpsAttribute->appendChild($xml->createElement('hps:Name', 'ExpYear'));
            $hpsAttribute->appendChild($xml->createElement('hps:Value', (string)$expYear));

        $hpsSet->appendChild($hpsAttribute);

        $hpsTokenActions->appendChild($hpsSet);

        $hpsManageTokens->appendChild($hpsTokenActions);

        $hpsTransaction->appendChild($hpsManageTokens);

        return $this->_submitTransaction($hpsTransaction, 'ManageTokens');
    }
    /**
     * @param $transactionId
     *
     * @return array|null
     * @throws \HpsArgumentException
     * @throws \HpsException
     * @throws \HpsGatewayException
     */
    public function get($transactionId)
    {
        if ($transactionId <= 0) {
            throw new HpsArgumentException('Invalid Transaction Id',HpsExceptionCodes::INVALID_ORIGINAL_TRANSACTION);
        }

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsReportTxnDetail = $xml->createElement('hps:ReportTxnDetail');
        $hpsReportTxnDetail->appendChild($xml->createElement('hps:TxnId', $transactionId));
        $hpsTransaction->appendChild($hpsReportTxnDetail);

        return $this->_submitTransaction($hpsTransaction, 'ReportTxnDetail');
    }
    /**
     * @param      $startDate
     * @param      $endDate
     * @param null $filterBy
     *
     * @return array|null
     * @throws \HpsException
     * @throws \HpsGatewayException
     * @throws \HpsInvalidRequestException
     */
    public function listTransactions($startDate, $endDate, $filterBy = null)
    {
        $this->_filterBy = $filterBy;
        date_default_timezone_set("UTC");
        $dateFormat = 'Y-m-d\TH:i:s.00\Z';
        $current = new DateTime();
        $currentTime = $current->format($dateFormat);

        HpsInputValidation::checkDateNotFuture($startDate);
        HpsInputValidation::checkDateNotFuture($endDate);

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsReportActivity = $xml->createElement('hps:ReportActivity');
        $hpsReportActivity->appendChild($xml->createElement('hps:RptStartUtcDT', $startDate));
        $hpsReportActivity->appendChild($xml->createElement('hps:RptEndUtcDT', $endDate));
        $hpsTransaction->appendChild($hpsReportActivity);

        return $this->_submitTransaction($hpsTransaction, 'ReportActivity');
    }
    /**
     * @param      $amount
     * @param      $currency
     * @param      $cardData
     * @param null $cardHolder
     * @param null $details
     *
     * @return array|null
     * @throws \HpsException
     * @throws \HpsGatewayException
     * @throws \HpsInvalidRequestException
     */
    public function refund($amount, $currency, $cardData, $cardHolder = null, $details = null)
    {
        HpsInputValidation::checkCurrency($currency);
        $this->_currency = $currency;
        $this->_amount = HpsInputValidation::checkAmount($amount);

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsCreditReturn = $xml->createElement('hps:CreditReturn');
        $hpsBlock1 = $xml->createElement('hps:Block1');

        $hpsBlock1->appendChild($xml->createElement('hps:AllowDup', 'Y'));
        $hpsBlock1->appendChild($xml->createElement('hps:Amt', $amount));
        if ($cardData instanceof HpsCreditCard) {
            $cardDataElement = $xml->createElement('hps:CardData');
            $cardDataElement->appendChild($this->_hydrateManualEntry($cardData, $xml));
            $hpsBlock1->appendChild($cardDataElement);
        } else if ($cardData instanceof HpsTokenData) {
            $cardDataElement = $xml->createElement('hps:CardData');
            $cardDataElement->appendChild($this->_hydrateTokenData($cardData, $xml));
            $hpsBlock1->appendChild($cardDataElement);
        } else {
            $hpsBlock1->appendChild($xml->createElement('hps:GatewayTxnId', $cardData));
        }
        if ($cardHolder != null) {
            $hpsBlock1->appendChild($this->_hydrateCardHolderData($cardHolder, $xml));
        }
        if ($details != null) {
            $hpsBlock1->appendChild($this->_hydrateAdditionalTxnFields($details, $xml));
        }

        $hpsCreditReturn->appendChild($hpsBlock1);
        $hpsTransaction->appendChild($hpsCreditReturn);

        return $this->_submitTransaction($hpsTransaction, 'CreditReturn', (isset($details->clientTransactionId) ? $details->clientTransationId : null));
    }
     /**
     * @param HpsCreditCard|HpsTokenData|int                $cardData GatewayTxnId
     * @param float                                         $amount
     * @param string                                           $currency
     * @param null|HpsTransactionDetails                    $details
     * @param null|float                                    $authAmount
     * @return HpsReversal
     * @throws HpsException
     * @throws HpsGatewayException
     * @throws HpsInvalidRequestException
     */
    public function reverse($cardData, $amount, $currency, $details = null, $authAmount = null)
    {
        HpsInputValidation::checkCurrency($currency);
        $this->_currency = $currency;
        $this->_amount = HpsInputValidation::checkAmount($amount);

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsCreditReversal = $xml->createElement('hps:CreditReversal');
        $hpsBlock1 = $xml->createElement('hps:Block1');

        $hpsBlock1->appendChild($xml->createElement('hps:Amt', $amount));
        if ($authAmount !== null){
            $hpsBlock1->appendChild($xml->createElement('hps:AuthAmt', HpsInputValidation::checkAmount($authAmount)));
        }
        $cardDataElement = null;
        if ($cardData instanceof HpsCreditCard) {
            $cardDataElement = $xml->createElement('hps:CardData');
            $cardDataElement->appendChild($this->_hydrateManualEntry($cardData, $xml));
        } else if ($cardData instanceof HpsTokenData) {
            $cardDataElement = $xml->createElement('hps:CardData');
            $cardDataElement->appendChild($this->_hydrateTokenData($cardData, $xml));
        } else {
            $cardDataElement = $xml->createElement('hps:GatewayTxnId', $cardData);
        }
        $hpsBlock1->appendChild($cardDataElement);
        if ($details != null) {
            $hpsBlock1->appendChild($this->_hydrateAdditionalTxnFields($details, $xml));
        }

        $hpsCreditReversal->appendChild($hpsBlock1);
        $hpsTransaction->appendChild($hpsCreditReversal);

        return $this->_submitTransaction($hpsTransaction, 'CreditReversal', (isset($details->clientTransactionId) ? $details->clientTransactionId : null));
    }
    /**
     * @param      $cardOrToken
     * @param null $cardHolder
     * @param bool $requestMultiUseToken
     * @param null $clientTransactionId
     *
     * @return array|null
     * @throws \HpsException
     * @throws \HpsGatewayException
     */
    public function verify($cardOrToken, $cardHolder = null, $requestMultiUseToken = false, $clientTransactionId = null)
    {
        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsCreditAccountVerify = $xml->createElement('hps:CreditAccountVerify');
        $hpsBlock1 = $xml->createElement('hps:Block1');

        if ($cardHolder != null) {
            $hpsBlock1->appendChild($this->_hydrateCardHolderData($cardHolder, $xml));
        }

        $cardData = $xml->createElement('hps:CardData');
        if ($cardOrToken instanceof HpsCreditCard) {
            $cardData->appendChild($this->_hydrateManualEntry($cardOrToken, $xml));
        } else {
            $cardData->appendChild($this->_hydrateTokenData($cardOrToken, $xml));
        }
        $cardData->appendChild($xml->createElement('hps:TokenRequest', ($requestMultiUseToken) ? 'Y' : 'N'));

        $hpsBlock1->appendChild($cardData);
        $hpsCreditAccountVerify->appendChild($hpsBlock1);
        $hpsTransaction->appendChild($hpsCreditAccountVerify);

        return $this->_submitTransaction($hpsTransaction, 'CreditAccountVerify', $clientTransactionId);
    }
    /**
     * @param      $transactionId
     * @param null $clientTransactionId
     *
     * @return array|null
     * @throws \HpsException
     * @throws \HpsGatewayException
     */
    public function void($transactionId, $clientTransactionId = null)
    {
        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsCreditVoid = $xml->createElement('hps:CreditVoid');
        $hpsCreditVoid->appendChild($xml->createElement('hps:GatewayTxnId', $transactionId));
        $hpsTransaction->appendChild($hpsCreditVoid);

        return $this->_submitTransaction($hpsTransaction, 'CreditVoid', $clientTransactionId);
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
     * @return array|null
     * @throws \HpsCreditException
     * @throws \HpsException
     * @throws \HpsGatewayException
     */
    private function _submitTransaction($transaction, $txnType, $clientTxnId = null, $cardData = null)
    {
        $options = array();
        if ($clientTxnId != null) {
            $options['clientTransactionId'] = $clientTxnId;
        }

        try {
            $response = $this->doRequest($transaction, $options);
        } catch (HpsException $e) {
            if ($e->innerException != null && $e->innerException->getMessage() == 'gateway_time-out') {
                if (in_array($txnType, array('CreditSale', 'CreditAuth'))) {
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
            case 'ReportTxnDetail':
                $rvalue = HpsReportTransactionDetails::fromDict($response, $txnType);
                break;
            case 'ReportActivity':
                $rvalue = HpsReportTransactionSummary::fromDict($response, $txnType, $this->_filterBy);
                break;
            case 'CreditSale':
                $rvalue = HpsCharge::fromDict($response, $txnType);
                break;
            case 'CreditAccountVerify':
                $rvalue = HpsAccountVerify::fromDict($response, $txnType);
                break;
            case 'CreditAuth':
                $rvalue = HpsAuthorization::fromDict($response, $txnType);
                break;
            case 'CreditReturn':
                $rvalue = HpsRefund::fromDict($response, $txnType);
                break;
            case 'CreditReversal':
                $rvalue = HpsReversal::fromDict($response, $txnType);
                break;
            case 'CreditVoid':
                $rvalue = HpsVoid::fromDict($response, $txnType);
                break;
            case 'CreditCPCEdit':
                $rvalue = HpsCPCEdit::fromDict($response, $txnType);
                break;
            case 'CreditTxnEdit':
                $rvalue = HpsTransaction::fromDict($response, $txnType);
                break;
            case 'RecurringBilling':
                $rvalue = HpsRecurringBilling::fromDict($response, $txnType);
                break;
            case 'ManageTokens':
                $rvalue = HpsManageTokensResponse::fromDict($response);
                break;
            default:
                break;
        }

        return $rvalue;
    }
}
