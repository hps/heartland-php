<?php

class HpsAltPaymentCreateSession extends HpsAltPaymentResponse
{
    public $sessionId   = null;
    public $redirectUrl = null;

    public static function fromDict($rsp, $txnType, $returnType = 'HpsAltPaymentCreateSession')
    {
        $createSession = $rsp->Transaction->$txnType;

        $session = parent::fromDict($rsp, $txnType, $returnType);
        if (isset($createSession->Session)) {
            $pairs = self::nvpToArray($createSession->Session);
        }

        $session->sessionId = isset($pairs['SessionId']) ? $pairs['SessionId'] : null;
        $session->redirectUrl = isset($pairs['RedirectUrl']) ? $pairs['RedirectUrl'] : null;

        return $session;
    }
}
