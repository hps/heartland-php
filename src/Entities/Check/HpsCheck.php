<?php

class HpsCheck
{
    public $routingNumber = null;
    public $accountNumber = null;
    public $checkNumber   = null;
    public $checkType     = null;
    public $checkHolder   = null;
    public $micrNumber    = null;

     /**
     * Account Type: Checking, Savings.
     *
     * <b>NOTE:</b> If processing with Colonnade, Account Type must be specified.
     *
     * @var null
     */
    public $accountType   = null;

    /**
     * Data Entry Mode indicating whether the check data was manually entered or obtained from a check reader.
     * Default value is HpsDataEntryMode::MANUAL.
     *
     * @var string|null
     */
    public $dataEntryMode = HpsDataEntryMode::MANUAL;

    /**
     * Indicates Check Verify. Requires processor setup to utilise. Contact your HPS representative for more information
     * on the GETI eBronze program.
     *
     * @var null
     */
    public $checkVerify   = null;

    /**
     * NACHA Standard Entry Class Code.
     *
     * <b>NOTE:</b> If processing with Colonnade, SECCode is required for CHeck Sale transactions.
     *
     * @var null
     */
    public $secCode       = null;
}
