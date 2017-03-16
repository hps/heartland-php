<?php

/**
 * Class HpsGiftCardAlias
 */
class HpsGiftCardAlias extends HpsTransaction
{
    /**
     * The Hps gift card alias response.
     */

    public $giftCard = null;
    /**
     * @param        $rsp
     * @param        $txnType
     * @param string $returnType
     *
     * @return \HpsGiftCardAlias
     */
    public static function fromDict($rsp, $txnType, $returnType = 'HpsGiftCardAlias')
    {
        $item = $rsp->Transaction->$txnType;

        $alias = new HpsGiftCardAlias();
        $alias->transactionId = (string)$rsp->Header->GatewayTxnId;
        $alias->giftCard = new HpsGiftCard($item->CardData);
        $alias->responseCode = (isset($item->RspCode) ? (string)$item->RspCode : null);
        $alias->responseText = (isset($item->RspText) ? (string)$item->RspText : null);

        return $alias;
    }
}
