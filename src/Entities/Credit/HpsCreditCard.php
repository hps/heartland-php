<?php

/**
 * Class HpsCreditCard
 */
class HpsCreditCard
{
    public $number         = null;
    public $cvv            = null;
    public $expMonth       = null;
    public $expYear        = null;
    public $encryptionData = null;
    /**
     * @return int|string
     */
    public function cardType()
    {
        $regexMap = array(
            'Amex'       => '/^3[47][0-9]{13}$/',
            'MasterCard' => '/^5[1-5][0-9]{14}$/',
            'Visa'       => '/^4[0-9]{12}(?:[0-9]{3})?$/',
            'DinersClub' => '/^3(?:0[0-5]|[68][0-9])[0-9]{11}$/',
            'EnRoute'    => '/^(2014|2149)/',
            'Discover'   => '/^6(?:011|5[0-9]{2})[0-9]{12}$/',
            'Jcb'        => '/^(?:2131|1800|35\d{3})\d{11}$/',
        );
        foreach ($regexMap as $card => $rx) {
            if (preg_match($rx, $this->number)) {
                return $card;
            }
        }
        return "Unknown";
    }
}
