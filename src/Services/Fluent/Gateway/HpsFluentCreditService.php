<?php

/**
 * Class HpsFluentCreditService
 */
class HpsFluentCreditService extends HpsSoapGatewayService
{
    /**
     * HpsFluentCreditService constructor.
     *
     * @param null $config
     */
    public function __construct($config = null)
    {
        parent::__construct($config);
    }
    /**
     * @param $config
     *
     * @return $this
     */
    public function withConfig($config)
    {
        $this->_config = $config;
        return $this;
    }
    /**
     * @param null $amount
     *
     * @return \HpsCreditServiceAuthorizeBuilder
     */
    public function authorize($amount = null)
    {
        $builder = new HpsCreditServiceAuthorizeBuilder($this);
        return $builder
            ->withAmount($amount)
            ->withCurrency('usd');
    }
    /**
     * @param null $transactionId
     *
     * @return \HpsCreditServiceCaptureBuilder
     */
    public function capture($transactionId = null)
    {
        $builder = new HpsCreditServiceCaptureBuilder($this);
        return $builder
            ->withTransactionId($transactionId);
    }
    /**
     * @param null $amount
     *
     * @return \HpsCreditServiceChargeBuilder
     */
    public function charge($amount = null)
    {
        $builder = new HpsCreditServiceChargeBuilder($this);
        return $builder
            ->withAmount($amount)
            ->withCurrency('usd');
    }
    /**
     * @param null $transactionId
     *
     * @return \HpsCreditServiceCpcEditBuilder
     */
    public function cpcEdit($transactionId = null)
    {
        $builder = new HpsCreditServiceCpcEditBuilder($this);
        return $builder
            ->withTransactionId($transactionId);
    }
    /**
     * @return \HpsCreditServiceEditBuilder
     */
    public function edit()
    {
        return new HpsCreditServiceEditBuilder($this);
    }
    /**
     * @return \HpsCreditServiceUpdateTokenExpirationBuilder
     */
    public function updateTokenExpiration()
    {
        $builder = new HpsCreditServiceUpdateTokenExpirationBuilder($this);
        //print_r($builder);
        return $builder;
    }
    /**
     * @param null $transactionId
     *
     * @return \HpsCreditServiceGetBuilder
     */
    public function get($transactionId = null)
    {
        $builder = new HpsCreditServiceGetBuilder($this);
        return $builder
            ->withTransactionId($transactionId);
    }
    /**
     * @return \HpsCreditServiceListTransactionsBuilder
     */
    public function listTransactions()
    {
        return new HpsCreditServiceListTransactionsBuilder($this);
    }
    /**
     * @param null $amount
     *
     * @return \HpsCreditServiceOfflineAuthBuilder
     */
    public function offlineAuth($amount = null)
    {
        $builder = new HpsCreditServiceOfflineAuthBuilder($this);
        return $builder
            ->withAmount($amount)
            ->withCurrency('usd');
    }
    /**
     * @param null $amount
     *
     * @return \HpsCreditServiceOfflineChargeBuilder
     */
    public function offlineCharge($amount = null)
    {
        $builder = new HpsCreditServiceOfflineChargeBuilder($this);
        return $builder
            ->withAmount($amount)
            ->withCurrency('usd');
    }
    /**
     * @return \HpsCreditServicePrepaidBalanceInquiryBuilder
     */
    public function prepaidBalanceInquiry()
    {
        return new HpsCreditServicePrepaidBalanceInquiryBuilder($this);
    }
    /**
     * @param null $amount
     *
     * @return \HpsCreditServicePrepaidAddValueBuilder
     */
    public function prepaidAddValue($amount = null)
    {
        $builder = new HpsCreditServicePrepaidAddValueBuilder($this);
        return $builder
            ->withAmount($amount)
            ->withCurrency('usd');
    }
    /**
     * @param null $amount
     *
     * @return \HpsCreditServiceRecurringBuilder
     */
    public function recurring($amount = null)
    {
        $builder = new HpsCreditServiceRecurringBuilder($this);
        return $builder->withAmount($amount);
    }
    /**
     * @param null $amount
     *
     * @return \HpsCreditServiceRefundBuilder
     */
    public function refund($amount = null)
    {
        $builder = new HpsCreditServiceRefundBuilder($this);
        return $builder
            ->withAmount($amount)
            ->withCurrency('usd');
    }
    /**
     * @param null $amount
     *
     * @return \HpsCreditServiceReverseBuilder
     */
    public function reverse($amount = null)
    {
        $builder = new HpsCreditServiceReverseBuilder($this);
        return $builder
            ->withAmount($amount)
            ->withCurrency('usd');
    }
    /**
     * @return \HpsAccountVerify
     */
    public function verify()
    {
        return new HpsCreditServiceVerifyBuilder($this);
    }
    /**
     * @param null $transactionId
     *
     * @return \HpsCreditServiceVoidBuilder
     */
    public function void($transactionId = null)
    {
        $builder = new HpsCreditServiceVoidBuilder($this);
        return $builder
            ->withTransactionId($transactionId);
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
                $this->reverse($this->_amount)->withTransactionId($transactionId)->execute();
            } catch (Exception $e) {
                throw new HpsGatewayException(
                    HpsExceptionCodes::GATEWAY_TIMEOUT_REVERSAL_ERROR,
                    'Error occurred while reversing a charge due to HPS gateway timeout',
                    $e
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
                        $this->reverse($this->_amount)->withTransactionId($transactionId)->execute();
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
    public function _submitTransaction($transaction, $txnType, $clientTxnId = null, $cardData = null)
    {
        try {
            $response = $this->doRequest($transaction, $clientTxnId);
        } catch (HpsException $e) {
            if ($e->innerException != null && $e->innerException->getMessage() == 'gateway_time-out') {
                if (in_array($txnType, array('CreditSale', 'CreditAuth'))) {
                    try {
                        $this->reverse($this->_amount)->withCard($cardData)->execute();
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
            case 'CreditAdditionalAuth':
                $rvalue = HpsAuthorization::fromDict($response, $txnType);
                break;
            case 'PrePaidBalanceInquiry':
                $rvalue = HpsAuthorization::fromDict($response, $txnType);
                break;
            case 'PrePaidAddValue':
                $rvalue = HpsAuthorization::fromDict($response, $txnType);
                break;
            case 'CreditOfflineAuth':
                $rvalue = HpsOfflineAuthorization::fromDict($response, $txnType);
                break;
            case 'ManageTokens':
                $rvalue = HpsManageTokensResponse::fromDict($response, $txnType);
                break;
            default:
                break;
        }

        return $rvalue;
    }
}
