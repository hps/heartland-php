<?php
/*
 * https://posgateway.cert.secureexchange.net/Gateway/PorticoSOAPSchema/build/Default/webframe.html#Portico%20Schema_xsd~e-PosRequest~e-Ver1.0~e-Transaction~e-AddAttachment.html
 * Valid if the origional transaction ID is from
CreditAuth
CreditOfflineAuth
CreditSale
CreditOfflineSale
CreditReturn
DebitSale
GiftCardSale
GiftCardAddValue
PrePaidAddValue
CheckSale
OverrideFraudDecline
 */


/**
 * Class HpsAttachmentService
 */
class HpsAttachmentService extends HpsSoapGatewayService
{


    /**
     * HpsAttachmentService constructor.
     * @param HpsServicesConfig $config
     */
    public function __construct($config = null)
    {
        parent::__construct($config);
    }

    /*
    public function AddAttachment(){
        throw new Exception('Not implemented');
        return null; // not implemented stub
    }
    */

    /**
     * @param float                     $GatewayTxnId   this is actually a long but php handles long with float
     * @param \HpsAttachmentType|string $AttachmentType SIGNATURE_IMAGE|RECEIPT_IMAGE|CUSTOMER_IMAGE|PRODUCT_IMAGE|DOCUMENT
     * @param bool                      $ReturnAttachmentTypesOnly
     * @param null|int                  $AttachmentDataId
     *
     * @return \HpsAttachment
     * @throws \HpsArgumentException
     * @throws \HpsException
     * @throws \HpsGatewayException
     */
    public function getAttachments($GatewayTxnId, $AttachmentType = 'all', $ReturnAttachmentTypesOnly = false, $AttachmentDataId = null)
    {
        $GatewayTxnId = filter_var($GatewayTxnId, FILTER_SANITIZE_NUMBER_FLOAT);
        if (!$GatewayTxnId) {
            throw new HpsArgumentException('Gateway Transaction ID required', HpsExceptionCodes::INVALID_NUMBER);
        }

        $AttachmentTypeProvided = preg_match(HpsAttachmentType::VALID_ATTACHMENT_TYPE, $AttachmentType) === 1;
        $AttachmentDataId = filter_var($AttachmentDataId, FILTER_SANITIZE_NUMBER_INT);
        // this is a limitation of the gateway and we want to inform the user
        if ($AttachmentTypeProvided && $AttachmentDataId) {
            throw new HpsGatewayException(HpsExceptionCodes::GATEWAY_ERROR, "Since the AttachmentDataId was provided the AttachmentType was ignored by the server");
            //trigger_error("Since the AttachmentDataId was provided the AttachmentType was ignored by the server", E_USER_NOTICE);
        }
        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');

        $hpsGetAttachments = $hpsTransaction
            ->appendChild($xml->createElement('hps:GetAttachments'));

        $hpsGetAttachments->appendChild($xml->createElement('hps:GatewayTxnId', $GatewayTxnId));

        if ($AttachmentTypeProvided) {
            $hpsGetAttachments->appendChild($xml->createElement('hps:AttachmentType', $AttachmentType));
        }

        if ($ReturnAttachmentTypesOnly === true) {
            $hpsGetAttachments->appendChild($xml->createElement('hps:ReturnAttachmentTypesOnly', 'true'));
        }

        if ($AttachmentDataId) {
            $hpsGetAttachments->appendChild($xml->createElement('hps:AttachmentDataId', $AttachmentDataId));
        }

        return $this->_submitTransaction($hpsTransaction, 'GetAttachments');

    }

    /**
     * @param \DOMElement $transaction
     * @param string $txnType
     *
     * @return array|null
     * @throws \HpsException
     * @throws \HpsGatewayException
     */
    private function _submitTransaction($transaction, $txnType)
    {

        try {
            $response = $this->doRequest($transaction);
        } catch (HpsException $e) {
            if ($e->innerException != null && $e->innerException->getMessage() == 'gateway_time-out') {
                throw new HpsException('An error occurred and the gateway has timed out', 'gateway_timeout', $e, 'gateway_timeout');
            }
            throw $e;
        }

        $this->_processGatewayResponse($response, $txnType);

        $rvalue = null;
        switch ($txnType) {
            case 'GetAttachments':
                $rvalue = HpsAttachment::fromDict($response, $txnType);
                break;
            default:
                break;
        }

        return $rvalue;
    }

    /**
     * @param SimpleXMLElement $response raw XML response
     * @param string $expectedType
     * @throws HpsAuthenticationException
     * @throws HpsGatewayException
     * @throws null
     */
    private function _processGatewayResponse($response, $expectedType)
    {
        $gatewayRspCode = (isset($response->Header->GatewayRspCode) ? $response->Header->GatewayRspCode : null);
        $transactionId = (isset($response->Header->GatewayTxnId) ? (float)$response->Header->GatewayTxnId : null);

        if ($gatewayRspCode == '0') {
            return;
        }

        if ($gatewayRspCode == '3') {
            throw new HpsGatewayException(
                HpsExceptionCodes::GATEWAY_ERROR,
                'Image could not be retrieved for ' . $transactionId,
                null,
                null,
                null,
                $transactionId
            );
        }

        HpsGatewayResponseValidation::checkResponse($response, $expectedType);
    }
}