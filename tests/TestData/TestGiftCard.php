<?php

class TestGiftCard
{
    public static function validGiftCardNotEncrypted()
    {
        $card = new HpsGiftCard();
        $card->number = "5022440000000000098";
        $card->expMonth = '12';
        $card->expYear = '39';

        return $card;
    }

    public static function validGiftCardNotEncrypted2()
    {
        $card = new HpsGiftCard();
        $card->number = "5022440000000000007";
        $card->expMonth = '12';
        $card->expYear = '39';

        return $card;
    }

    public static function invalidGiftCardNotEncrypted()
    {
        $card = new HpsGiftCard();
        $card->number = "123";
        $card->expMonth = '12';
        $card->expYear = '39';

        return $card;
    }
}
