<?php

interface HpsGatewayServiceInterface
{
    public function doRequest($data, $options);
    public function processResponse($curlResponse, $curlInfo, $curlError);
}
