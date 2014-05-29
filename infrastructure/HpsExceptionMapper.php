<?php

class HpsExceptionMapper{

    private static function _getExceptions(){
        $path = realpath(dirname(__FILE__));
        $fileName = $path .'/Exceptions.json';
        $fh = fopen($fileName,'r');
        $jsonString = fread($fh, filesize($fileName));
        return json_decode($jsonString);
    }

    public static function map_issuer_exception($transaction_id, $response_code, $response_text, $result_text = null){
        $mapping = self::exception_for_category_and_code('issuer', $response_code);

        if(isset($mapping)){
            $message = self::message_for_mapping($mapping, $response_text);
            $code = $mapping->mapping_code;
            return new CardException($transaction_id, $code, $message, $result_text);
        }else{
            return new CardException($transaction_id, 'unknown_card_exception', $response_text, $result_text);
        }
    }

    public static function map_gateway_exception($transaction_id, $response_code, $response_text){
        $mapping = self::exception_for_category_and_code('gateway',$response_code);

        if(isset($mapping)){
            $message = self::message_for_mapping($mapping, $response_text);
            $code = $mapping->exception_codes[0];
            $exception_type = $mapping->mapping_type;

            if($exception_type == 'AuthenticationException'){
                return new AuthenticationException($message);
            }else if($exception_type == "CardException"){
                return new CardException($transaction_id, $code, $message);
            }else if($exception_type == "InvalidRequestException"){
                return new InvalidRequestException($message, $mapping->param, $code);
            }else if(isset($code)){
                return new HpsException($response_text,$code);
            }

        }
        return new HpsException($response_text,"unknown");
    }

    public static function map_sdk_exception($error_code, $inner_exception = null){
        $mapping = self::exception_for_category_and_code('sdk', $error_code);
        $sdk_codes = get_class_vars('HpsSdkCodes');
        foreach($sdk_codes as $code_name=>$code_value){
            if($code_value == $error_code){
                $sdk_code_name = $code_name;
                break;
            }
        }

        if(isset($sdk_code_name)){
            $response_text = $sdk_code_name;
        }else{
            $response_text = 'unknown';
        }

        if(isset($mapping)){
            $message = self::message_for_mapping($mapping, $response_text);
            $code = $mapping->mapping_code;
            $exception_type = $mapping->mapping_type;

            if($exception_type == "InvalidRequestException"){
                return new InvalidRequestException($message, $mapping->param, $code, $inner_exception);
            }else if($exception_type == "ApiConnectionException"){
                return new ApiConnectionException($message, $code, $inner_exception);
            }else if(isset($code)){
                return new HpsException($message, $code, $inner_exception);
            }
        }

        return new HpsException('unknown', 'unknown', $inner_exception);
    }

    private static function exception_for_category_and_code($category, $code){
        $exceptions = self::_getExceptions();
        foreach($exceptions->exception_mappings as $key=>$mapping){
            if($mapping->category == $category && in_array($code,$mapping->exception_codes)){
                return $mapping;
            }
        }
    }

    private static function message_for_mapping($mapping, $original_message){
        $exceptions = self::_getExceptions();
        if(isset($mapping) && $mapping != null && $mapping != ""){
            if(isset($mapping->mapping_message)){
                $message = $mapping->mapping_message;
                if(isset($message)){
                    foreach($exceptions->exception_messages as $key=>$exception_mapping){
                        if($exception_mapping->code == $message){
                            return $exception_mapping->message;
                        }
                    }
                }
            }
        }
        return $original_message;
    }

}
