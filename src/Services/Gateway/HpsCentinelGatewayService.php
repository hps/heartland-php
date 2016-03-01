<?php

class HpsCentinelGatewayService
    extends HpsGatewayServiceAbstract
    implements HpsGatewayServiceInterface
{
    public function doRequest($request, $options = null)
    {
        $request = array_merge($request, array(
            'Version'        => $this->_config->version,
            'ProcessorId'    => $this->_config->processorId,
            'MerchantId'     => $this->_config->merchantId,
            'TransactionPwd' => $this->_config->transactionPwd,
        ));

        $xml = new DOMDocument('1.0', 'utf-8');
        $envelope = $xml->createElement('CardinalMPI');
        foreach ($request as $k => $v) {
            $envelope->appendChild($xml->createElement($k, $v));
        }
        $xml->appendChild($envelope);

        $url = $this->_config->serviceUri();
        $xmlData = $xml->saveXML();
        $data = 'cmpi_msg=' . urlencode($xmlData);
        $header = array(
            'Content-type: application/x-www-form-urlencoded;charset="utf-8"',
            'Accept: text/xml',
            'Content-length: '.strlen($data),
        );
        // error_log($xmlData);

        return $this->submitRequest($url, $header, $data);
    }

    public function processResponse($curlResponse, $curlInfo, $curlError)
    {
        // error_log($curlResponse);
        switch ($curlInfo['http_code']) {
            case '200':
                return simplexml_load_string($curlResponse);
                break;
            case '500':
                $faultString = '';
                throw new HpsException($faultString);
                break;
            default:
                throw new HpsException('Unexpected response');
                break;
        }
    }
}
