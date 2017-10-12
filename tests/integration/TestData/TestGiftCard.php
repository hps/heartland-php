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
    /**
     * @return \HpsTokenData
     */
    public static function validGiftCardToken()
    {
        $token = self::getSuToken(self::validGiftCardNotEncrypted());
        return $token;
    }
    
    /**
     * @param \HpsGiftCard $card
     * @param null $key
     *
     * @return \HpsTokenData|mixed
     */
    private static function getSuToken(HpsGiftCard $card, $key = null)
    {
        if (empty($key)) {
            $key = TestServicesConfig::validMultiUsePublicKey();
        }

        $tokenService = new HpsTokenService($key);
        $tokenResponse = $tokenService->getGiftCardToken($card);
        if (isset($tokenResponse->token_value)) {
            $token = new HpsTokenData();
            $token->tokenValue = $tokenResponse->token_value;
            return $token;
        } else {
            return $tokenResponse;
        }
    }
}
