<?php

/**
 * Class HpsInvalidRequestException
 */
class HpsInvalidRequestException extends HpsException
{
    public $param = null;
    public $code  = null;
    /**
     * HpsInvalidRequestException constructor.
     *
     * @param string $code
     * @param null   $message
     * @param null   $paramName
     */
    public function __construct($code, $message, $paramName = null)
    {
        $this->param = $paramName;
        $this->message = (string) $message;
        $this->code = $code;
        parent::__construct($message);
    }
}
