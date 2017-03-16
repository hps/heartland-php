<?php

/**
 * Class HpsProcessorResponseValidation
 */
class HpsProcessorResponseValidation
{
    /**
     * @param $transactionId
     * @param $responseCode
     * @param $responseText
     * @param $item
     *
     * @throws \HpsProcessorException
     * @throws null
     */
    public static function checkResponse($transactionId, $responseCode, $responseText, $item)
    {
        $e = self::getException($transactionId, $responseCode, $responseText, $item);

        if ($e != null) {
            throw $e;
        }
    }
    /**
     * @param $transactionId
     * @param $responseCode
     * @param $responseText
     * @param $item
     *
     * @return \HpsProcessorException|null
     */
    public static function getException($transactionId, $responseCode, $responseText, $item)
    {
        $responseCode = (string)$responseCode;
        $code = null;
        $message = null;

        if ($responseCode == '00') {
            return null;
        }

        if (isset($item->Processor) && isset($item->Processor->Response)) {
            $error = self::nvpToArray($item->Processor->Response);
            $code = $error['Code'];
            $message = $error['Message'];
        }

        return new HpsProcessorException($transactionId, $code, $message, $responseCode, $responseText);
    }
    /**
     * @param $pairs
     *
     * @return array
     */
    protected static function nvpToArray($pairs)
    {
        $array = array();
        foreach ($pairs->NameValuePair as $pair) {
            $array[(string)$pair->Name] = (string)$pair->Value;
        }
        return $array;
    }
}
