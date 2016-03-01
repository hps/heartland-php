<?php

class HpsFluentGiftCardService extends HpsSoapGatewayService
{
    public function __construct($config = null)
    {
        parent::__construct($config);
    }

    public function withConfig($config)
    {
        $this->_config = $config;
        return $this;
    }

    public function activate($amount = null)
    {
        $builder = new HpsGiftCardServiceActivateBuilder($this);
        return $builder
            ->withAmount($amount)
            ->withCurrency('usd');
    }

    public function addValue($amount = null)
    {
        $builder = new HpsGiftCardServiceAddValueBuilder($this);
        return $builder
            ->withAmount($amount);
    }

    public function alias()
    {
        return new HpsGiftCardServiceAliasBuilder($this);
    }

    public function balance()
    {
        return new HpsGiftCardServiceBalanceBuilder($this);
    }

    public function deactivate()
    {
        return new HpsGiftCardServiceDeactivateBuilder($this);
    }

    public function replace()
    {
        return new HpsGiftCardServiceReplaceBuilder($this);
    }

    public function reverse($amount = null)
    {
        $builder = new HpsGiftCardServiceReverseBuilder($this);
        return $builder
            ->withAmount($amount)
            ->withCurrency('usd');
    }

    public function reward($amount = null)
    {
        $builder = new HpsGiftCardServiceRewardBuilder($this);
        return $builder
            ->withAmount($amount)
            ->withCurrency('usd');
    }

    public function sale($amount = null)
    {
        $builder = new HpsGiftCardServiceSaleBuilder($this);
        return $builder
            ->withAmount($amount)
            ->withCurrency('usd');
    }

    public function void($transactionId = null)
    {
        $builder = new HpsGiftCardServiceVoidBuilder($this);
        return $builder
            ->withTransactionId($transactionId);
    }

    public function _submitTransaction($transaction, $txnType, $clientTxnId = null)
    {
        $response = $this->doRequest($transaction, $clientTxnId);

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
