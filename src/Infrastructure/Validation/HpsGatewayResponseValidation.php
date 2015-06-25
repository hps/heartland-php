<?php

class HpsGatewayResponseValidation
{
    public static function checkResponse($response, $expectedType)
    {
        $rspCode = $response->Header->GatewayRspCode;
        $rspText = $response->Header->GatewayRspMsg;
        $e = HpsGatewayResponseValidation::getException($rspCode, $rspText, $response);

        if ($e != null) {
            throw $e;
        }

        if (!isset($response->Transaction) || !isset($response->Transaction->$expectedType)) {
            throw new HpsGatewayException(
                HpsExceptionCodes::UNEXPECTED_GATEWAY_ERROR,
                'Unexpected response from HPS gateway'
            );
        }
    }

    public static function getException($responseCode, $responseText, $response)
    {
        $e = null;
        
        switch ($responseCode) {
            case '0':
                break;
            case '-2':
                $e = new HpsAuthenticationException(
                    HpsExceptionCodes::AUTHENTICATION_ERROR,
                    'Authentication Error. Please double check your service configuration'
                );
                break;
            case '3':
                $e = new HpsGatewayException(
                    HpsExceptionCodes::INVALID_ORIGINAL_TRANSACTION,
                    $responseText,
                    $responseCode,
                    $responseText
                );
                break;
            case '5':
                $e = new HpsGatewayException(
                    HpsExceptionCodes::NO_OPEN_BATCH,
                    $responseText,
                    $responseCode,
                    $responseText
                );
                break;
            case '12':
                $e = new HpsGatewayException(
                    HpsExceptionCodes::INVALID_CPC_DATA,
                    'Invalid CPC data',
                    $responseCode,
                    $responseText
                );
                break;
            case '13':
                $e = new HpsGatewayException(
                    HpsExceptionCodes::INVALID_CARD_DATA,
                    'Invalid card data',
                    $responseCode,
                    $responseText
                );
                break;
            case '14':
                $e = new HpsGatewayException(
                    HpsExceptionCodes::INVALID_NUMBER,
                    'The card number is not valid',
                    $responseCode,
                    $responseText
                );
                break;
            case '30':
                $e = new HpsGatewayException(
                    HpsExceptionCodes::GATEWAY_ERROR,
                    'Gateway timed out',
                    $responseCode,
                    $responseText
                );
                break;
            case '1':
            default:
                $e = new HpsGatewayException(
                    HpsExceptionCodes::UNKNOWN_GATEWAY_ERROR,
                    $responseText,
                    $responseCode,
                    $responseText
                );
        }

        return $e;
    }
}
