<?php

class HpsExceptionMapper{
    public  $exceptions = null;

    public function __construct(){
        $path = realpath(dirname(__FILE__));
        $fileName = $path .'/Exceptions.json';
        $fh = fopen($fileName,'r');
        $jsonString = fread($fh, filesize($fileName));
        $this->exceptions = json_decode($jsonString);
    }

    public function version_number(){
        return $this->exceptions->version;
    }

    public function map_issuer_exception($transaction_id, $response_code, $response_text, $result_text = null){
        $mapping = $this->exception_for_category_and_code('issuer', $response_code);

        if(isset($mapping)){
            $message = $this->message_for_mapping($mapping, $response_text);
            $code = $mapping->mapping_code;
            return new CardException($transaction_id, $code, $message, $result_text);
        }else{
            return new CardException($transaction_id, 'unknown_card_exception', $response_text, $result_text);
        }
    }

    public function map_gateway_exception($transaction_id, $response_code, $response_text){
        $mapping = $this->exception_for_category_and_code('gateway',$response_code);

        if(isset($mapping)){
            $message = $this->message_for_mapping($mapping, $response_text);
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

    public function map_sdk_exception($error_code, $inner_exception = null){
        $mapping = $this->exception_for_category_and_code('sdk', $error_code);
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
            $message = $this->message_for_mapping($mapping, $response_text);
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

    private function exception_for_category_and_code($category, $code){
        foreach($this->exceptions->exception_mappings as $key=>$mapping){
            if($mapping->category == $category && in_array($code,$mapping->exception_codes)){
                return $mapping;
            }
        }
    }

    private function message_for_mapping($mapping, $original_message){
        if(isset($mapping) && $mapping != null && $mapping != ""){
            if(isset($mapping->mapping_message)){
                $message = $mapping->mapping_message;
                if(isset($message)){
                    foreach($this->exceptions->exception_messages as $key=>$exception_mapping){
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
