<?php

/**
 * Class HpsEmptyLogger
 */
class HpsEmptyLogger implements HpsLoggerInterface
{
    /**
     * @param      $message
     * @param null $object
     *
     * @return mixed|void
     */
    public function log($message, $object = null)
    {
        return;
    }
}
