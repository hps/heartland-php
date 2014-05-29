<?php

class Validation {
    static private $_defaultAllowedCurrencies = array('usd');

    static public function checkAmount($amount){
        if ($amount <= 0 || $amount == null){
            throw HpsExceptionMapper::map_sdk_exception(HpsSdkCodes::$invalidAmount);
        }
        return sprintf("%0.2f",round($amount,3));
    }

    static public function checkCurrency($currency){
        if ($currency == null or $currency == ""){
            throw HpsExceptionMapper::map_sdk_exception(HpsSdkCodes::$missingCurrency);
        }
        if(!in_array(strtolower($currency), self::$_defaultAllowedCurrencies)){
            throw HpsExceptionMapper::map_sdk_exception(HpsSdkCodes::$invalidCurrency);
        }
    }

    static public function cleanPhoneNumber($number){
        return preg_replace('/\D+/', '', $number);
    }

    static public function cleanZipCode($zip){
        return preg_replace('/\D+/', '', $zip);
    }

    static public function checkGatewayResponse($response, $expectedType, $reversalMethod = null, $args = array()){
        $gatewayRspCode = (isset($response->Header->GatewayRspCode) ? $response->Header->GatewayRspCode : null);
        $gatewayRspText = (isset($response->Header->GatewayRspMsg) ? $response->Header->GatewayRspMsg : null);
        $transactionId = (isset($response->Header->GatewayTxnId) ? $response->Header->GatewayTxnId : null);
        $object = (isset($args['object']) ? $args['object'] : null);

        if($gatewayRspCode != '0'){
            // check if we need to submit a reversal
            if($gatewayRspCode == '30' && $reversalMethod != null){
                try{
                    $object->$reversalMethod($transactionId, $args['amount'], $args['currency']);
                } catch(Exception $e){
                    throw HpsExceptionMapper::map_sdk_exception(HpsSdkCodes::$reversalErrorAfterGatewayTimeout,$e);
                }
            }else{
                throw HpsExceptionMapper::map_gateway_exception($transactionId,$gatewayRspCode,$gatewayRspText);
            }

            if(!isset($response->Transaction) || !isset($response->Transaction->$expectedType)){
                throw HpsExceptionMapper::map_sdk_exception(HpsSdkCodes::$unableToProcessTransaction, null);
            }

        }
        return $transactionId;
    }

    static public function checkTransactionResponse($response, $expectedType, $args = array()){
        $transactionId = (isset($response->Header->GatewayTxnId) ? $response->Header->GatewayTxnId : null);
        $reversalMethod = (isset($args['reversalMethod']) ? $args['reversalMethod'] : null);
        $object = (isset($args['object']) ? $args['object'] : null);

        $item = $response->Transaction->$expectedType;
        if($item != null){
            $issuerRspCode = (isset($item->RspCode) ? $item->RspCode : null);
            $issuerRspText = (isset($item->RspText) ? $item->RspText : null);

            if( $issuerRspCode != null){
                // check if we need to do a reversal
                if( $issuerRspCode == '91'){
                    if( $reversalMethod != null ){
                        try{
                                $object->$reversalMethod($transactionId, $args['amount'], $args['currency']);
                        }catch (Exception $e){
                            throw HpsExceptionMapper::map_sdk_exception(HpsSdkCodes::$reversalErrorAfterIssuerTimeout, $e);
                        }
                    }
                }else if( $expectedType == 'CreditAccountVerify'){
                    if ($issuerRspCode != '85' && $issuerRspCode != '00' ){
                        throw HpsExceptionMapper::map_issuer_exception($transactionId,$issuerRspCode,$issuerRspText);
                    }
                }else if($issuerRspCode != '00' && $issuerRspCode != '0'){
                    throw HpsExceptionMapper::map_issuer_exception($transactionId,$issuerRspCode,$issuerRspText);
                }
            }
        }
    }
} 