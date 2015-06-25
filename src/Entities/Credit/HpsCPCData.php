<?php

class HpsCPCData
{
    public $cardHolderPONbr = null;
    public $taxType         = null;
    public $taxAmt          = null;

    public function __construct($poNbr = null, $taxType = null, $taxAmt = null)
    {
        if ($poNbr != null) {
            if (strlen($poNbr) > 17) {
                throw new HpsArgumentException('Card holder PO number must be less than 17 characters');
            }
            $this->cardHolderPONbr = $poNbr;
        }

        if ($taxType != null) {
            $this->taxType = $taxType;
        }

        if ($taxAmt != null) {
            $this->taxAmt = $taxAmt;
        }
    }
}
