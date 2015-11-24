<?php

class HpsGiftCard
{
    public $number         = null;
    public $trackData      = null;
    public $alias          = null;
    public $tokenValue     = null;
    public $encryptionData = null;
    public $pin            = null;

    public function __construct($number = null)
    {
        $this->number = $number;
    }
}
