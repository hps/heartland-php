<?php

interface HpsConfigInterface
{
    const KEY_TYPE_SECRET  = 'secret';
    const KEY_TYPE_PUBLIC  = 'public';
    const KEY_TYPE_UNKNOWN = 'unknown';
    public function serviceUri();
    public function setServiceUri($value);
    public function validate($keyType);
}
