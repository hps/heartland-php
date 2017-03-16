<?php

/**
 * Interface HpsConfigInterface
 */
interface HpsConfigInterface
{
    const KEY_TYPE_SECRET  = 'secret';
    const KEY_TYPE_PUBLIC  = 'public';
    const KEY_TYPE_UNKNOWN = 'unknown';
    public function serviceUri();
    /**
     * @param $value
     *
     * @return mixed
     */
    public function setServiceUri($value);
    /**
     * @param $keyType
     *
     * @return mixed
     */
    public function validate($keyType);
}
