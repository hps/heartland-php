<?php

use Heartland\Entities\HpsCreditCard;

class TestCreditCard
{
    static public function validVisaCreditCard(){
        $validVisaCreditCard = new HpsCreditCard();
        $validVisaCreditCard->cvv = "123";
        $validVisaCreditCard->expMonth = "12";
        $validVisaCreditCard->expYear = "2016";
        $validVisaCreditCard->number = "4012002000060016";
        return $validVisaCreditCard;
    }

    static public function validMasterCreditCard(){
        $creditCard = new HpsCreditCard();
        $creditCard->cvv = "123";
        $creditCard->expMonth = "12";
        $creditCard->expYear = "2016";
        $creditCard->number = "5473500000000014";
        return $creditCard;
    }

    static public function validDiscoverCreditCard(){
        $creditCard = new HpsCreditCard();
        $creditCard->cvv = "123";
        $creditCard->expMonth = "12";
        $creditCard->expYear = "2016";
        $creditCard->number = "6011000990156527";
        return $creditCard;
    }

    static public function validAmexCreditCard(){
        $creditCard = new HpsCreditCard();
        $creditCard->cvv = "123";
        $creditCard->expMonth = "12";
        $creditCard->expYear = "2016";
        $creditCard->number = "372700699251018";
        return $creditCard;
    }

    static public function validJBCCreditCard(){
        $creditCard = new HpsCreditCard();
        $creditCard->cvv = "123";
        $creditCard->expMonth = "12";
        $creditCard->expYear = "2016";
        $creditCard->number = "3566007770007321";
        return $creditCard;
    }

    static public function invalidCreditCard(){
        $creditCard = new HpsCreditCard();
        $creditCard->cvv = "123";
        $creditCard->expMonth = "12";
        $creditCard->expYear = "2016";
        $creditCard->number = "12345";
        return $creditCard;
    }
}