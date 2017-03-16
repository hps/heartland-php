<?php

/**
 * Class HpsLogger
 */
class HpsLogger
{
    private static $_instance = null;
    private $_logger = null;
    /**
     * HpsLogger constructor.
     */
    public function __construct()
    {
        $this->_logger = new HpsEmptyLogger();
    }
    /**
     * @return \HpsLogger|null
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new HpsLogger();
        }
        return self::$_instance;
    }
    /**
     * @param \HpsLoggerInterface $logger
     */
    public function useLogger(HpsLoggerInterface $logger)
    {
        $this->_logger = $logger;
    }
    /**
     * @param      $message
     * @param null $object
     */
    public function log($message, $object = null)
    {
        $this->_logger->log($message, $object);
    }
}
