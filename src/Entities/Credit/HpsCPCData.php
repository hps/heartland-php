<?php

/**
 * Class HpsCPCData
 */
class HpsCPCData
{
    public $cardHolderPONbr = null;
    public $taxType         = null;
    public $taxAmt          = null;
    /**
     * HpsCPCData constructor.
     *
     * @param null $poNbr
     * @param null $taxType
     * @param null $taxAmt
     *
     * @throws \HpsArgumentException
     */
    public function __construct($poNbr = null, $taxType = null, $taxAmt = null)
    {
        if ($poNbr != null) {
            if (strlen($poNbr) > 17) {
                throw new HpsArgumentException('Card holder PO number must be less than 17 characters',HpsExceptionCodes::INVALID_CPC_DATA);
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
