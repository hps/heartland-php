<?php

class TestCreditCard
{
    public static function validVisaCreditCard()
    {
        $validVisaCreditCard = new HpsCreditCard();
        $validVisaCreditCard->cvv = "123";
        $validVisaCreditCard->expMonth = "12";
        $validVisaCreditCard->expYear = "2016";
        $validVisaCreditCard->number = "4012002000060016";
        return $validVisaCreditCard;
    }

    public static function validMastercardCreditCard()
    {
        $creditCard = new HpsCreditCard();
        $creditCard->cvv = "123";
        $creditCard->expMonth = "12";
        $creditCard->expYear = "2016";
        $creditCard->number = "5473500000000014";
        return $creditCard;
    }

    public static function validDiscoverCreditCard()
    {
        $creditCard = new HpsCreditCard();
        $creditCard->cvv = "123";
        $creditCard->expMonth = "12";
        $creditCard->expYear = "2016";
        $creditCard->number = "6011000990156527";
        return $creditCard;
    }

    public static function validAmexCreditCard()
    {
        $creditCard = new HpsCreditCard();
        $creditCard->cvv = "1234";
        $creditCard->expMonth = "12";
        $creditCard->expYear = "2016";
        $creditCard->number = "372700699251018";
        return $creditCard;
    }

    public static function validJCBCreditCard()
    {
        $creditCard = new HpsCreditCard();
        $creditCard->cvv = "123";
        $creditCard->expMonth = "12";
        $creditCard->expYear = "2016";
        $creditCard->number = "3566007770007321";
        return $creditCard;
    }

    public static function invalidCreditCard()
    {
        $creditCard = new HpsCreditCard();
        $creditCard->cvv = "123";
        $creditCard->expMonth = "12";
        $creditCard->expYear = "2016";
        $creditCard->number = "12345";
        return $creditCard;
    }

    public static function validCreditCardWithBlankExpMonth()
    {
        $validVisaCreditCard = new HpsCreditCard();
        $validVisaCreditCard->cvv = "123";
        $validVisaCreditCard->expMonth = "";
        $validVisaCreditCard->expYear = "2016";
        $validVisaCreditCard->number = "4012002000060016";
        return $validVisaCreditCard;
    }

    public static function validGsbCardEcommerce()
    {
        $card = new HpsCreditCard();
        $card->number = '6277220572999800';
        $card->expMonth = "12";
        $card->expYear = "2049";

        return $card;
    }
}
