<?php

/**
 * Interface HpsLoggerInterface
 */
interface HpsLoggerInterface
{
    /**
     * @param      $message
     * @param null $object
     *
     * @return mixed
     */
    public function log($message, $object = null);
}
