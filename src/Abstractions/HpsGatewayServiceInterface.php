<?php

interface HpsGatewayServiceInterface
{
    public function doRequest($data, $options = array());
    public function processResponse($curlResponse, $curlInfo, $curlError);
}
