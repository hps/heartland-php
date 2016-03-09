<?php

/**
 * Class HpsException
 */
class HpsException extends Exception
{
    /**
     * @var int|null
     */
    public $code           = null;
    /**
     * @var null
     */
    public $innerException = null;

    /**
     * HpsException constructor.
     *
     * @param string $message [optional] The Exception message to throw.
     * @param $code [optional] The Exception code.
     * @param Exception $innerException [optional] The previous exception used for the exception chaining. Since 5.3.0
     */
    public function __construct($message = "", $code = null, $innerException = null)
    {
        $this->message = (string) $message;
        if ($code != null) {
            $this->code = $code;
        }
        if ($innerException != null) {
            $this->innerException =  $innerException;
        }
    }
}
