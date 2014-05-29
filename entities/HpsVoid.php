<?php

class HpsVoid extends HpsTransaction{
    public function __construct($header){
        parent::__construct($header);
    }

    static public function fromDict($rsp,$txnType){
        $void = parent::fromDict($rsp,$txnType);
        $void->responseCode = '00';
        $void->responseText = '';
        return $void;
    }
} 