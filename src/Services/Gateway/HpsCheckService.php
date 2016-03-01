<?php

class HpsCheckService extends HpsSoapGatewayService
{
    public function __construct(HpsServicesConfig $config)
    {
        parent::__construct($config);
    }

    /**
     * A Sale transaction is used to process transactions using bank account information as the payment method.
     * The transaction service can be used to perform a Sale or Return transaction by indicating the Check Action.
     *
     * <b>NOTE:</b> The Portico Gateway supports both GETI and HPS Colonnade for processing check transactions. While
     * the available services are the same regardless of the check processor, the services may have different behaviors.
     * For example, GETI-processed Check Sale transactions support the ability to override a Check Sale transaction
     * already presented as well as the ability to verify a check.
     * @param string $action Type of Check Action (Sale, Return, Override)
     * @param string $check The Check information.
     * @param string $amount The amount of the sale.
     *
     * @returns HpsCheckSale
     */
    public function sale(HpsCheck $check, $amount, $clientTransactionId = null)
    {
        return $this->_buildTransaction('SALE', $check, $amount, $clientTransactionId);
    }

    public function returnCheck(HpsCheck $check, $amount, $clientTransactionId = null)
    {
        throw new HpsException('Check action RETURN not currently supported');
        return $this->_buildTransaction('RETURN', $check, $amount, $clientTransactionId);
    }

    public function override(HpsCheck $check, $amount, $clientTransactionId = null)
    {
        throw new HpsException('Check action OVERRIDE not currently supported');
        return $this->_buildTransaction('OVERRIDE', $check, $amount, $clientTransactionId);
    }

    /**
     * A <b>Void</b> transaction is used to cancel a previously successful sale transaction. The original sale transaction
     * can be identified by the GatewayTxnid of the original or by the ClientTxnId of the original if provided on the
     * original Sale Transaction.
     *
     * @param null $transactionId
     * @param null $clientTransactionId
     */
    public function void($transactionId = null, $clientTransactionId = null)
    {
        if (($transactionId == null && $clientTransactionId == null) || ($transactionId != null && $clientTransactionId != null)) {
            throw new HpsException('Please provide either a transaction id or a client transaction id');
        }

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsCheckVoid = $xml->createElement('hps:CheckVoid');
        $hpsBlock1 = $xml->createElement('hps:Block1');

        if ($transactionId != null) {
            $hpsBlock1->appendChild($xml->createElement('hps:GatewayTxnId', $transactionId));
        } else {
            $hpsBlock1->appendChild($xml->createElement('hps:ClientTxnId', $clientTransactionId));
        }

        $hpsCheckVoid->appendChild($hpsBlock1);
        $hpsTransaction->appendChild($hpsCheckVoid);
        return $this->_submitTransaction($hpsTransaction, 'CheckVoid');
    }

    private function _buildTransaction($action, HpsCheck $check, $amount, $clientTransactionId = null)
    {
        $amount = HpsInputValidation::checkAmount($amount);

        if ($check->secCode == HpsSECCode::CCD &&
            ($check->checkHolder == null || $check->checkHolder->checkName == null)) {
            throw new HpsInvalidRequestException(
                HpsExceptionCodes::MISSING_CHECK_NAME,
                'For SEC code CCD, the check name is required',
                'check_name'
            );
        }

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsCheckSale = $xml->createElement('hps:CheckSale');
        $hpsBlock1 = $xml->createElement('hps:Block1');

        $hpsBlock1->appendChild($xml->createElement('hps:Amt', sprintf("%0.2f", round($amount, 3))));
        $hpsBlock1->appendChild($this->_hydrateCheckData($check, $xml));
        $hpsBlock1->appendChild($xml->createElement('hps:CheckAction', $action));
        $hpsBlock1->appendChild($xml->createElement('hps:SECCode', $check->secCode));
        if ($check->checkType != null) {
            $hpsBlock1->appendChild($xml->createElement('hps:CheckType', $check->checkType));
        }
        $hpsBlock1->appendChild($xml->createElement('hps:DataEntryMode', $check->dataEntryMode));
        if ($check->checkHolder != null) {
            $hpsBlock1->appendChild($this->_hydrateConsumerInfo($check, $xml));
        }

        $hpsCheckSale->appendChild($hpsBlock1);
        $hpsTransaction->appendChild($hpsCheckSale);

        return $this->_submitTransaction($hpsTransaction, 'CheckSale', $clientTransactionId);
    }

    private function _submitTransaction($transaction, $txnType, $clientTransactionId = null)
    {
        $options = array();
        if ($clientTransactionId !== null) {
            $options['clientTransactionId'] = $clientTransactionId;
        }
        $rsp = $this->doRequest($transaction, $options);
        HpsGatewayResponseValidation::checkResponse($rsp, $txnType);
        $response = HpsCheckResponse::fromDict($rsp, $txnType);

        if ($response->responseCode != 0) {
            throw new HpsCheckException(
                $rsp->Header->GatewayTxnId,
                $response->details,
                $response->responseCode,
                $response->responseText
            );
        }

        return $response;
    }
}
