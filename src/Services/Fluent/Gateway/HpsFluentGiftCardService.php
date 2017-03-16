<?php

/**
 * Class HpsFluentGiftCardService
 */
class HpsFluentGiftCardService extends HpsSoapGatewayService
{
    /**
     * HpsFluentGiftCardService constructor.
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
     * @return \HpsGiftCardServiceActivateBuilder
     */
    public function activate($amount = null)
    {
        $builder = new HpsGiftCardServiceActivateBuilder($this);
        return $builder
            ->withAmount($amount)
            ->withCurrency('usd');
    }
    /**
     * @param null $amount
     *
     * @return \HpsGiftCardServiceAddValueBuilder
     */
    public function addValue($amount = null)
    {
        $builder = new HpsGiftCardServiceAddValueBuilder($this);
        return $builder
            ->withAmount($amount);
    }
    /**
     * @return \HpsGiftCardServiceAliasBuilder
     */
    public function alias()
    {
        return new HpsGiftCardServiceAliasBuilder($this);
    }
    /**
     * @return \HpsGiftCardServiceBalanceBuilder
     */
    public function balance()
    {
        return new HpsGiftCardServiceBalanceBuilder($this);
    }
    /**
     * @return \HpsGiftCardServiceDeactivateBuilder
     */
    public function deactivate()
    {
        return new HpsGiftCardServiceDeactivateBuilder($this);
    }
    /**
     * @return \HpsGiftCardServiceReplaceBuilder
     */
    public function replace()
    {
        return new HpsGiftCardServiceReplaceBuilder($this);
    }
    /**
     * @param null $amount
     *
     * @return \HpsGiftCardServiceReverseBuilder
     */
    public function reverse($amount = null)
    {
        $builder = new HpsGiftCardServiceReverseBuilder($this);
        return $builder
            ->withAmount($amount)
            ->withCurrency('usd');
    }
    /**
     * @param null $amount
     *
     * @return \HpsGiftCardServiceRewardBuilder
     */
    public function reward($amount = null)
    {
        $builder = new HpsGiftCardServiceRewardBuilder($this);
        return $builder
            ->withAmount($amount)
            ->withCurrency('usd');
    }
    /**
     * @param null $amount
     *
     * @return \HpsGiftCardServiceSaleBuilder
     */
    public function sale($amount = null)
    {
        $builder = new HpsGiftCardServiceSaleBuilder($this);
        return $builder
            ->withAmount($amount)
            ->withCurrency('usd');
    }
    /**
     * @param null $transactionId
     *
     * @return \HpsGiftCardServiceVoidBuilder
     */
    public function void($transactionId = null)
    {
        $builder = new HpsGiftCardServiceVoidBuilder($this);
        return $builder
            ->withTransactionId($transactionId);
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
