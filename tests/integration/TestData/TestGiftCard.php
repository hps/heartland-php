<?php

/**
 * Class TestGiftCard
 */
class TestGiftCard
{
    /**
     * @return \HpsGiftCard
     */
    public static function validGiftCardNotEncrypted()
    {
        $card = new HpsGiftCard();
        $card->number = "5022440000000000098";

        return $card;
    }
    /**
     * @return \HpsGiftCard
     */
    public static function validGiftCardNotEncrypted2()
    {
        $card = new HpsGiftCard();
        $card->number = "5022440000000000007";

        return $card;
    }
    /**
     * @return \HpsGiftCard
     */
    public static function invalidGiftCardNotEncrypted()
    {
        $card = new HpsGiftCard();
        $card->number = "123";

        return $card;
    }
}
