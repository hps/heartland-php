<?php

class HpsAltPaymentService extends HpsSoapGatewayService
{
    /** @var string|null */
    protected $_transactionType = null;

    public function authorize($sessionId, $amount, $currency, HpsBuyerData $buyer = null, HpsPaymentData $payment = null, HpsShippingInfo $shippingAddress = null, $lineItems = null)
    {
        HpsInputValidation::checkAmount($amount);
        HpsInputValidation::checkCurrency($currency);

        $xml = new DOMDocument();
        $transaction = $xml->createElement('hps:Transaction');
        $auth = $xml->createElement('hps:AltPaymentAuth');

        $auth->appendChild($xml->createElement('hps:TransactionType', $this->_transactionType));
        $auth->appendChild($xml->createElement('hps:SessionId', $sessionId));
        $auth->appendChild($this->hydrateBuyerData($buyer, $xml));
        $auth->appendChild($xml->createElement('hps:Amt', $amount));
        $auth->appendChild($this->hydratePaymentData($payment, $xml));
        if ($shippingAddress != null) {
            $auth->appendChild($this->hydrateShippingData($shippingAddress, $xml));
        }
        if ($lineItems != null) {
            $auth->appendChild($this->hydrateLineItems($lineItems, $xml));
        }

        $transaction->appendChild($auth);
        return $this->_submitTransaction($transaction, 'AltPaymentAuth');
    }

    public function capture($transactionId, $amount)
    {
        HpsInputValidation::checkAmount($amount);

        $xml = new DOMDocument();
        $transaction = $xml->createElement('hps:Transaction');
        $capture = $xml->createElement('hps:AltPaymentCapture');

        $capture->appendChild($xml->createElement('hps:TransactionType', $this->_transactionType));
        $capture->appendChild($xml->createElement('hps:GatewayTxnId', $transactionId));
        $capture->appendChild($xml->createElement('hps:Amt', $amount));

        $payment = $xml->createElement('hps:Payment');
        $nvp = $xml->createElement('hps:NameValuePair');
        $nvp->appendChild($xml->createElement('hps:Name', 'FullyCapturedFlag'));
        $nvp->appendChild($xml->createElement('hps:Value', 'true'));
        $payment->appendChild($nvp);

        $capture->appendChild($payment);
        $transaction->appendChild($capture);
        return $this->_submitTransaction($transaction, 'AltPaymentCapture');
    }

    public function createSession($amount, $currency, HpsBuyerData $buyer = null, HpsPaymentData $payment = null, HpsShippingInfo $shippingAddress = null, $lineItems = null)
    {
        HpsInputValidation::checkAmount($amount);
        HpsInputValidation::checkCurrency($currency);

        $xml = new DOMDocument();
        $transaction = $xml->createElement('hps:Transaction');
        $createSession = $xml->createElement('hps:AltPaymentCreateSession');

        $createSession->appendChild($xml->createElement('hps:TransactionType', $this->_transactionType));
        $createSession->appendChild($this->hydrateBuyerData($buyer, $xml));
        $createSession->appendChild($xml->createElement('hps:Amt', $amount));
        $createSession->appendChild($this->hydratePaymentData($payment, $xml));
        if ($shippingAddress != null) {
            $createSession->appendChild($this->hydrateShippingData($shippingAddress, $xml));
        }
        if ($lineItems != null) {
            $createSession->appendChild($this->hydrateLineItems($lineItems, $xml));
        }

        $transaction->appendChild($createSession);
        return $this->_submitTransaction($transaction, 'AltPaymentCreateSession');
    }

    public function refund($transactionId, $isPartial = false, $partialAmount = null)
    {
        $xml = new DOMDocument();
        $transaction = $xml->createElement('hps:Transaction');
        $return = $xml->createElement('hps:AltPaymentReturn');

        $return->appendChild($xml->createElement('hps:TransactionType', $this->_transactionType));
        $return->appendChild($xml->createElement('hps:GatewayTxnId', $transactionId));

        if ($isPartial) {
            $return->appendChild($xml->createElement('hps:Amt', $partialAmount));
        }

        $payment = $xml->createElement('hps:Return');
        $nvp = $xml->createElement('hps:NameValuePair');
        $nvp->appendChild($xml->createElement('hps:Name', 'ReturnType'));
        $nvp->appendChild($xml->createElement('hps:Value', $isPartial ? 'partial' : 'full'));
        $payment->appendChild($nvp);

        $return->appendChild($payment);
        $transaction->appendChild($return);
        return $this->_submitTransaction($transaction, 'AltPaymentReturn');
    }

    public function sale($sessionId, $amount, $currency, HpsBuyerData $buyer = null, HpsPaymentData $payment = null, HpsShippingInfo $shippingAddress = null, $lineItems = null)
    {
        HpsInputValidation::checkAmount($amount);
        HpsInputValidation::checkCurrency($currency);

        $xml = new DOMDocument();
        $transaction = $xml->createElement('hps:Transaction');
        $sale = $xml->createElement('hps:AltPaymentSale');

        $sale->appendChild($xml->createElement('hps:TransactionType', $this->_transactionType));
        $sale->appendChild($xml->createElement('hps:SessionId', $sessionId));
        $sale->appendChild($this->hydrateBuyerData($buyer, $xml));
        $sale->appendChild($xml->createElement('hps:Amt', $amount));
        $sale->appendChild($this->hydratePaymentData($payment, $xml));
        if ($shippingAddress != null) {
            $sale->appendChild($this->hydrateShippingData($shippingAddress, $xml));
        }
        if ($lineItems != null) {
            $sale->appendChild($this->hydrateLineItems($lineItems, $xml));
        }

        $transaction->appendChild($sale);
        return $this->_submitTransaction($transaction, 'AltPaymentSale');
    }

    public function void($transactionId)
    {
        $xml = new DOMDocument();
        $transaction = $xml->createElement('hps:Transaction');
        $void = $xml->createElement('hps:AltPaymentVoid');

        $void->appendChild($xml->createElement('hps:TransactionType', $this->_transactionType));
        $void->appendChild($xml->createElement('hps:GatewayTxnId', $transactionId));

        $transaction->appendChild($void);
        return $this->_submitTransaction($transaction, 'AltPaymentVoid');
    }

    public function sessionInfo($sessionId)
    {
        $xml = new DOMDocument();
        $transaction = $xml->createElement('hps:Transaction');
        $info = $xml->createElement('hps:AltPaymentSessionInfo');

        $info->appendChild($xml->createElement('hps:TransactionType', $this->_transactionType));
        $info->appendChild($xml->createElement('hps:SessionId', $sessionId));

        $transaction->appendChild($info);
        return $this->_submitTransaction($transaction, 'AltPaymentSessionInfo');
    }

    public function setTransactionType($type)
    {
        $this->_transactionType = $type;
    }

    public function status($transactionId)
    {
        $xml = new DOMDocument();
        $transaction = $xml->createElement('hps:Transaction');
        $status = $xml->createElement('hps:GetTransactionStatus');

        $status->appendChild($xml->createElement('hps:GatewayTxnId', $transactionId));

        $transaction->appendChild($status);
        return $this->_submitTransaction($transaction, 'GetTransactionStatus');
    }

    protected function hydrateBuyerData(HpsBuyerData $buyer, DOMDocument $xml)
    {
        $data = $xml->createElement('hps:Buyer');
        if (isset($buyer->returnUrl)) {
            $data->appendChild($this->hydrateNameValuePair('ReturnUrl', $buyer->returnUrl, $xml));
        }
        if (isset($buyer->cancelUrl)) {
            $data->appendChild($this->hydrateNameValuePair('CancelUrl', $buyer->cancelUrl, $xml));
        }
        if (isset($buyer->emailAddress)) {
            $data->appendChild($this->hydrateNameValuePair('EmailAddress', $buyer->emailAddress, $xml));
        }
        if (isset($buyer->payerId)) {
            $data->appendChild($this->hydrateNameValuePair('BuyerId', $buyer->payerId, $xml));
        }
        if (isset($buyer->credit) && $buyer->credit != false) {
            $data->appendChild($this->hydrateNameValuePair('FundingSource', 'credit', $xml));
        }
        return $data;
    }

    protected function hydrateLineItems($items, DOMDocument $xml)
    {
        $lineItems = $xml->createElement('hps:LineItem');

        foreach ($items as $item) {
            if (!$item instanceof HpsLineItem) {
                continue;
            }
            $detail = $xml->createElement('hps:Detail');
            if (isset($item->name)) {
                $detail->appendChild($this->hydrateNameValuePair('Name', $item->name, $xml));
            }
            if (isset($item->description)) {
                $detail->appendChild($this->hydrateNameValuePair('Description', $item->description, $xml));
            }
            if (isset($item->number)) {
                $detail->appendChild($this->hydrateNameValuePair('Number', $item->number, $xml));
            }
            if (isset($item->amount)) {
                $detail->appendChild($this->hydrateNameValuePair('Amount', $item->amount, $xml));
            }
            if (isset($item->quantity)) {
                $detail->appendChild($this->hydrateNameValuePair('Quantity', $item->quantity, $xml));
            }
            if (isset($item->taxAmount)) {
                $detail->appendChild($this->hydrateNameValuePair('TaxAmount', $item->taxAmount, $xml));
            }
            $lineItems->appendChild($detail);
        }
        return $lineItems;
    }

    protected function hydrateNameValuePair($name, $value, DOMDocument $xml)
    {
        $nvp = $xml->createElement('hps:NameValuePair');
        $nvp->appendChild($xml->createElement('hps:Name', $name));
        $nvp->appendChild($xml->createElement('hps:Value', $value));
        return $nvp;
    }

    protected function hydratePaymentData(HpsPaymentData $payment, DOMDocument $xml)
    {
        $data = $xml->createElement('hps:Payment');
        $data->appendChild($this->hydrateNameValuePair('ItemAmount', $payment->subtotal, $xml));
        if (isset($payment->shippingAmount)) {
            $data->appendChild($this->hydrateNameValuePair('ShippingAmount', $payment->shippingAmount, $xml));
        }
        if (isset($payment->taxAmount)) {
            $data->appendChild($this->hydrateNameValuePair('TaxAmount', $payment->taxAmount, $xml));
        }
        if (isset($payment->paymentType)) {
            $data->appendChild($this->hydrateNameValuePair('PaymentType', $payment->paymentType, $xml));
        }
        if (isset($payment->invoiceNumber)) {
            $data->appendChild($this->hydrateNameValuePair('InvoiceNbr', $payment->invoiceNumber, $xml));
        }
        return $data;
    }

    protected function hydrateShippingData(HpsShippingInfo $info, DOMDocument $xml)
    {
        $shipping = $xml->createElement('hps:Shipping');
        $address = $xml->createElement('hps:Address');
        $address->appendChild($this->hydrateNameValuePair('AllowAddressOverride', 'false', $xml));
        $address->appendChild($this->hydrateNameValuePair('ShipName', $info->name, $xml));
        $address->appendChild($this->hydrateNameValuePair('ShipAddress', $info->address->address, $xml));
        $address->appendChild($this->hydrateNameValuePair('ShipCity', $info->address->city, $xml));
        $address->appendChild($this->hydrateNameValuePair('ShipState', $info->address->state, $xml));
        $address->appendChild($this->hydrateNameValuePair('ShipZip', $info->address->zip, $xml));
        $address->appendChild($this->hydrateNameValuePair('ShipCountryCode', $info->address->country, $xml));
        $shipping->appendChild($address);
        return $shipping;
    }

    private function _processGatewayResponse($response, $expectedType)
    {
        $gatewayRspCode = (isset($response->Header->GatewayRspCode) ? $response->Header->GatewayRspCode : null);
        $transactionId = (isset($response->Header->GatewayTxnId) ? $response->Header->GatewayTxnId : null);

        if ($gatewayRspCode == '0') {
            return;
        }

        if ($gatewayRspCode == '30') {
            try {
                $this->void($transactionId);
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

    private function _processProcessorResponse($response, $expectedType)
    {
        $transactionId = (isset($response->Header->GatewayTxnId) ? $response->Header->GatewayTxnId : null);
        $item = $response->Transaction->$expectedType;

        if ($item != null) {
            $responseCode = (isset($item->RspCode) ? $item->RspCode : null);
            $responseMessage = (isset($item->RspMessage) ? $item->RspMessage : null);

            if ($responseCode == null && isset($item->TransactionStatus->RspCode)) {
                $responseCode = $item->TransactionStatus->RspCode;
            }
            if ($responseMessage == null && isset($item->TransactionStatus->RspText)) {
                $responseMessage = $item->TransactionStatus->RspText;
            }

            HpsProcessorResponseValidation::checkResponse($transactionId, $responseCode, $responseMessage, $item);
        }
    }

    private function _submitTransaction($transaction, $txnType, $clientTxnId = null, $cardData = null)
    {
        try {
            $response = $this->doRequest($transaction, $clientTxnId);
        } catch (HpsException $e) {
            if ($e->innerException != null && $e->innerException->getMessage() == 'gateway_time-out') {
                // if (in_array($txnType, array('CreditSale', 'CreditAuth'))) {
                //     try {
                //         $this->reverse($cardData, $this->_amount, $this->_currency);
                //     } catch (Exception $e) {
                //         throw new HpsGatewayException('0', HpsExceptionCodes::GATEWAY_TIMEOUT_REVERSAL_ERROR);
                //     }
                // }
                throw new HpsException('An error occurred and the gateway has timed out', 'gateway_timeout', $e, 'gateway_timeout');
            }
            throw $e;
        }

        $this->_processGatewayResponse($response, $txnType);
        $this->_processProcessorResponse($response, $txnType);

        $rvalue = null;
        switch ($txnType) {
            case 'AltPaymentCreateSession':
                $rvalue = HpsAltPaymentCreateSession::fromDict($response, $txnType);
                break;
            case 'AltPaymentSessionInfo':
                $rvalue = HpsAltPaymentSessionInfo::fromDict($response, $txnType);
                break;
            case 'AltPaymentSale':
                $rvalue = HpsAltPaymentSale::fromDict($response, $txnType);
                break;
            case 'AltPaymentAuth':
                $rvalue = HpsAltPaymentAuth::fromDict($response, $txnType);
                break;
            case 'AltPaymentCapture':
                $rvalue = HpsAltPaymentCapture::fromDict($response, $txnType);
                break;
            case 'AltPaymentReturn':
                $rvalue = HpsAltPaymentReturn::fromDict($response, $txnType);
                break;
            case 'AltPaymentVoid':
                $rvalue = HpsAltPaymentVoid::fromDict($response, $txnType);
                break;
            case 'GetTransactionStatus':
                $rvalue = HpsTransactionStatus::fromDict($response, $txnType);
            default:
                break;
        }

        return $rvalue;
    }
}
