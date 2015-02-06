<?php

class AuthenticationException extends HpsException{

    public function __construct($message){
        parent::__construct($message, null);
    }

}