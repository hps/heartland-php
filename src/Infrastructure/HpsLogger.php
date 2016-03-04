<?php

class HpsLogger
{
    private static $_instance = null;
    private $_logger = null;

    public function __construct()
    {
        $this->_logger = new HpsEmptyLogger();
    }

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new HpsLogger();
        }
        return self::$_instance;
    }

    public function useLogger(HpsLoggerInterface $logger)
    {
        $this->_logger = $logger;
    }

    public function log($message, $object = null)
    {
        $this->_logger->log($message, $object);
    }
}
