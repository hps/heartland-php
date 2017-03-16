<?php

/**
 * Interface HpsGatewayServiceInterface
 */
interface HpsGatewayServiceInterface
{
    /**
     * @param       $data
     * @param array $options
     *
     * @return mixed
     */
    public function doRequest($data, $options = array());
    /**
     * @param $curlResponse
     * @param $curlInfo
     * @param $curlError
     *
     * @return mixed
     */
    public function processResponse($curlResponse, $curlInfo, $curlError);
}
