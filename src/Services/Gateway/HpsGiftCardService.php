<?php

/**
 * Class HpsGiftCardService
 */
class HpsGiftCardService extends HpsSoapGatewayService
{
    /**
     * HpsGiftCardService constructor.
     *
     * @param \HpsServicesConfig $config
     */
    public function __construct(HpsServicesConfig $config)
    {
        parent::__construct($config);
    }
    /**
     * @param $amount
     * @param $currency
     * @param $giftCard
     *
     * @return \HpsGiftCardAlias|string
     * @throws \HpsInvalidRequestException
     */
    public function activate($amount, $currency, $giftCard)
    {
        HpsInputValidation::checkCurrency($currency);
        $amount = HpsInputValidation::checkAmount($amount);
        $txnType = 'GiftCardActivate';

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsGiftCard = $xml->createElement('hps:'.$txnType);
        $hpsBlock1 = $xml->createElement('hps:Block1');

        $hpsBlock1->appendChild($xml->createElement('hps:Amt', $amount));
        $hpsBlock1->appendChild($this->_hydrateGiftCardData($giftCard, $xml));

        $hpsGiftCard->appendChild($hpsBlock1);
        $hpsTransaction->appendChild($hpsGiftCard);

        return $this->_submitTransaction($hpsTransaction, $txnType);
    }
    /**
     * @param $amount
     * @param $currency
     * @param $giftCard
     *
     * @return \HpsGiftCardAlias|string
     * @throws \HpsInvalidRequestException
     */
    public function addValue($amount, $currency, $giftCard)
    {
        HpsInputValidation::checkCurrency($currency);
        $amount = HpsInputValidation::checkAmount($amount);
        $txnType = 'GiftCardAddValue';

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsGiftCard = $xml->createElement('hps:'.$txnType);
        $hpsBlock1 = $xml->createElement('hps:Block1');

        $hpsBlock1->appendChild($xml->createElement('hps:Amt', $amount));
        $hpsBlock1->appendChild($this->_hydrateGiftCardData($giftCard, $xml));

        $hpsGiftCard->appendChild($hpsBlock1);
        $hpsTransaction->appendChild($hpsGiftCard);

        return $this->_submitTransaction($hpsTransaction, $txnType);
    }
    /**
     * @param $action
     * @param $giftCard
     * @param $aliasStr
     *
     * @return \HpsGiftCardAlias|string
     */
    public function alias($action, $giftCard, $aliasStr)
    {
        $txnType = 'GiftCardAlias';

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsGiftCardAlias = $xml->createElement('hps:'. $txnType);
        $hpsBlock1 = $xml->createElement('hps:Block1');

        $hpsBlock1->appendChild($xml->createElement('hps:Action', strtoupper($action)));
        $hpsBlock1->appendChild($xml->createElement('hps:Alias', $aliasStr));
        $hpsBlock1->appendChild($this->_hydrateGiftCardData($giftCard, $xml));

        $hpsGiftCardAlias->appendChild($hpsBlock1);
        $hpsTransaction->appendChild($hpsGiftCardAlias);

        return $this->_submitTransaction($hpsTransaction, $txnType);
    }
    /**
     * @param $giftCard
     *
     * @return \HpsGiftCardAlias|string
     */
    public function balance($giftCard)
    {
        $txnType = 'GiftCardBalance';

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsGiftCard = $xml->createElement('hps:'. $txnType);
        $hpsBlock1 = $xml->createElement('hps:Block1');

        $hpsBlock1->appendChild($this->_hydrateGiftCardData($giftCard, $xml));

        $hpsGiftCard->appendChild($hpsBlock1);
        $hpsTransaction->appendChild($hpsGiftCard);

        return $this->_submitTransaction($hpsTransaction, $txnType);
    }
    /**
     * @param $giftCard
     *
     * @return \HpsGiftCardAlias|string
     */
    public function deactivate($giftCard)
    {
        $txnType = 'GiftCardDeactivate';

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsGiftCard = $xml->createElement('hps:'. $txnType);
        $hpsBlock1 = $xml->createElement('hps:Block1');

        $hpsBlock1->appendChild($this->_hydrateGiftCardData($giftCard, $xml));

        $hpsGiftCard->appendChild($hpsBlock1);
        $hpsTransaction->appendChild($hpsGiftCard);

        return $this->_submitTransaction($hpsTransaction, $txnType);
    }
    /**
     * @param $oldGiftCard
     * @param $newGiftCard
     *
     * @return \HpsGiftCardAlias|string
     */
    public function replace($oldGiftCard, $newGiftCard)
    {
        $txnType = 'GiftCardReplace';

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsGiftCard = $xml->createElement('hps:'. $txnType);
        $hpsBlock1 = $xml->createElement('hps:Block1');

        $hpsBlock1->appendChild($this->_hydrateGiftCardData($oldGiftCard, $xml, 'OldCardData'));
        $hpsBlock1->appendChild($this->_hydrateGiftCardData($newGiftCard, $xml, 'NewCardData'));

        $hpsGiftCard->appendChild($hpsBlock1);
        $hpsTransaction->appendChild($hpsGiftCard);

        return $this->_submitTransaction($hpsTransaction, $txnType);
    }
    /**
     * @param        $cardData
     * @param        $amount
     * @param string $currency
     *
     * @return \HpsGiftCardAlias|string
     * @throws \HpsInvalidRequestException
     */
    public function reverse($cardData, $amount, $currency = 'usd')
    {
        $txnType = 'GiftCardReversal';

        HpsInputValidation::checkCurrency($currency);
        $amount = HpsInputValidation::checkAmount($amount);

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsGiftCard = $xml->createElement('hps:'. $txnType);
        $hpsBlock1 = $xml->createElement('hps:Block1');

        $hpsBlock1->appendChild($xml->createElement('hps:Amt', $amount));
        if ($cardData instanceof HpsGiftCard) {
            $hpsBlock1->appendChild($this->_hydrateGiftCardData($cardData, $xml));
        } else {
            $hpsBlock1->appendChild($xml->createElement('hps:GatewayTxnId', $cardData));
        }

        $hpsGiftCard->appendChild($hpsBlock1);
        $hpsTransaction->appendChild($hpsGiftCard);

        return $this->_submitTransaction($hpsTransaction, $txnType);
    }
    /**
     * @param        $giftCard
     * @param        $amount
     * @param string $currency
     * @param null   $gratuity
     * @param null   $tax
     *
     * @return \HpsGiftCardAlias|string
     * @throws \HpsInvalidRequestException
     */
    public function reward($giftCard, $amount, $currency = 'usd', $gratuity = null, $tax = null)
    {
        $txnType = 'GiftCardReward';

        HpsInputValidation::checkCurrency($currency);
        $amount = HpsInputValidation::checkAmount($amount);

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsGiftCard = $xml->createElement('hps:'. $txnType);
        $hpsBlock1 = $xml->createElement('hps:Block1');

        $hpsBlock1->appendChild($xml->createElement('hps:Amt', $amount));
        $hpsBlock1->appendChild($this->_hydrateGiftCardData($giftCard, $xml));
        if (strtolower($currency) == 'usd' || $currency == 'points') {
            $hpsBlock1->appendChild($xml->createElement('hps:Currency', (strtolower($currency) == 'usd' ? 'USD' : 'POINTS')));
        }
        if ($gratuity != null) {
            $hpsBlock1->appendChild($xml->createElement('hps:GratuityAmtInfo', $gratuity));
        }
        if ($tax != null) {
            $hpsBlock1->appendChild($xml->createElement('hps:TaxAmtInfo', $tax));
        }

        $hpsGiftCard->appendChild($hpsBlock1);
        $hpsTransaction->appendChild($hpsGiftCard);

        return $this->_submitTransaction($hpsTransaction, $txnType);
    }
    /**
     * @param        $giftCard
     * @param        $amount
     * @param string $currency
     * @param null   $gratuity
     * @param null   $tax
     *
     * @return \HpsGiftCardAlias|string
     * @throws \HpsInvalidRequestException
     */
    public function sale($giftCard, $amount, $currency = 'usd', $gratuity = null, $tax = null)
    {
        $txnType = 'GiftCardSale';

        HpsInputValidation::checkCurrency($currency);
        $amount = HpsInputValidation::checkAmount($amount);

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsGiftCard = $xml->createElement('hps:'. $txnType);
        $hpsBlock1 = $xml->createElement('hps:Block1');

        $hpsBlock1->appendChild($xml->createElement('hps:Amt', $amount));
        $hpsBlock1->appendChild($this->_hydrateGiftCardData($giftCard, $xml));
        if (strtolower($currency) == 'usd' || $currency == 'points') {
            $hpsBlock1->appendChild($xml->createElement('hps:Currency', (strtolower($currency) == 'usd' ? 'USD' : 'POINTS')));
        }
        if ($gratuity != null) {
            $hpsBlock1->appendChild($xml->createElement('hps:GratuityAmtInfo', $gratuity));
        }
        if ($tax != null) {
            $hpsBlock1->appendChild($xml->createElement('hps:TaxAmtInfo', $tax));
        }

        $hpsGiftCard->appendChild($hpsBlock1);
        $hpsTransaction->appendChild($hpsGiftCard);

        return $this->_submitTransaction($hpsTransaction, $txnType);
    }
    /**
     * @param $transactionId
     *
     * @return \HpsGiftCardAlias|string
     */
    public function void($transactionId)
    {
        $txnType = 'GiftCardVoid';

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsGiftCard = $xml->createElement('hps:'. $txnType);
        $hpsBlock1 = $xml->createElement('hps:Block1');

        $hpsBlock1->appendChild($xml->createElement('hps:GatewayTxnId', $transactionId));

        $hpsGiftCard->appendChild($hpsBlock1);
        $hpsTransaction->appendChild($hpsGiftCard);

        return $this->_submitTransaction($hpsTransaction, $txnType);
    }
    /**
     * @param      $transaction
     * @param      $txnType
     * @param null $clientTxnId
     *
     * @return \HpsGiftCardAlias|string
     * @throws \HpsAuthenticationException
     * @throws \HpsCreditException
     * @throws \HpsGatewayException
     * @throws null
     */
    private function _submitTransaction($transaction, $txnType, $clientTxnId = null)
    {
        $options = array();
        if ($clientTxnId != null) {
            $options['clientTransactionId'] = $clientTxnId;
        }
        $response = $this->doRequest($transaction, $options);

        HpsGatewayResponseValidation::checkResponse($response, $txnType);
        HpsIssuerResponseValidation::checkResponse(
            $response->Header->GatewayTxnId,
            $response->Transaction->$txnType->RspCode,
            $response->Transaction->$txnType->RspText,
            'gift'
        );

        $rvalue = '';
        switch ($txnType) {
            case 'GiftCardActivate':
                $rvalue = HpsGiftCardActivate::fromDict($response, $txnType, 'HpsGiftCardActivate');
                break;
            case 'GiftCardAddValue':
                $rvalue = HpsGiftCardAddValue::fromDict($response, $txnType, 'HpsGiftCardAddValue');
                break;
            case 'GiftCardAlias':
                $rvalue = HpsGiftCardAlias::fromDict($response, $txnType, 'HpsGiftCardAlias');
                break;
            case 'GiftCardBalance':
                $rvalue = HpsGiftCardBalance::fromDict($response, $txnType, 'HpsGiftCardBalance');
                break;
            case 'GiftCardDeactivate':
                $rvalue = HpsGiftCardDeactivate::fromDict($response, $txnType, 'HpsGiftCardDeactivate');
                break;
            case 'GiftCardReplace':
                $rvalue = HpsGiftCardReplace::fromDict($response, $txnType, 'HpsGiftCardReplace');
                break;
            case 'GiftCardReward':
                $rvalue = HpsGiftCardReward::fromDict($response, $txnType, 'HpsGiftCardReward');
                break;
            case 'GiftCardSale':
                $rvalue = HpsGiftCardSale::fromDict($response, $txnType, 'HpsGiftCardSale');
                break;
            case 'GiftCardVoid':
                $rvalue = HpsGiftCardVoid::fromDict($response, $txnType, 'HpsGiftCardVoid');
                break;
            case 'GiftCardReversal':
                $rvalue = HpsGiftCardReversal::fromDict($response, $txnType, 'HpsGiftCardReversal');
                break;
            default:
                break;
        }

        return $rvalue;
    }
}
