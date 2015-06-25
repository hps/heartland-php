<?php

class HpsTrackData
{
    /** @var HpsTrackDataMethod **/
    public $method         = HpsTrackDataMethod::SWIPE;

    /** @var string|null **/
    public $value          = null;

    /** @var HpsEncryptionData|null **/
    public $encryptionData = null;
}
