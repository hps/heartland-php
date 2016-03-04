<?php

class HpsEmptyLogger implements HpsLoggerInterface
{
    public function log($message, $object = null)
    {
        return;
    }
}
