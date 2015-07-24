<?php

class GatewayPayPlanCreditRecurringTest extends PHPUnit_Framework_TestCase
{
    protected $service;
    protected $schedule;

    protected function setup()
    {
        $config = new HpsServicesConfig();
        $config->secretApiKey = 'skapi_cert_MTyMAQBiHVEAewvIzXVFcmUd2UcyBge_eCpaASUp0A';
        $config->developerId = '002914';
        $config->versionNumber = '1510';
        $scheduleService = new HpsPayPlanScheduleService($config);
        $results = $scheduleService
            ->page(1, 0)
            ->findAll(array(
                'scheduleStatus'     => HpsPayPlanScheduleStatus::ACTIVE,
                'scheduleIdentifier' => 'SecureSubmit',
            ));
        $this->schedule = $results->results[0];

        $this->service = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
    }

    public function testOneTimeWithCardData()
    {
        $response = $this->service->recurring($this->schedule, 10, TestCreditCard::validVisaCreditCard(), TestCardHolder::ValidCardHolder(), true);

        $this->assertNotNull($response);
        $this->assertEquals('00', $response->responseCode);
    }

    public function testOneTimeWithTokenData()
    {
        $token = $this->getToken(TestCreditCard::validVisaCreditCard());
        $response = $this->service->recurring($this->schedule, 10, $token, null, true);

        $this->assertNotNull($response);
        $this->assertEquals('00', $response->responseCode);
    }

    public function testOneTimeWithPaymentMethodKey()
    {
        $paymentMethodKey = $this->getPaymentMethodKey();
        $response = $this->service->recurring($this->schedule, 10, $paymentMethodKey, null, true);

        $this->assertNotNull($response);
        $this->assertEquals('00', $response->responseCode);
    }

    public function testWithCardData()
    {
        $response = $this->service->recurring($this->schedule, 10, TestCreditCard::validVisaCreditCard(), TestCardHolder::ValidCardHolder());

        $this->assertNotNull($response);
        $this->assertEquals('00', $response->responseCode);
    }

    public function testWithTokenData()
    {
        $token = $this->getToken(TestCreditCard::validVisaCreditCard());
        $response = $this->service->recurring($this->schedule, 10, $token);

        $this->assertNotNull($response);
        $this->assertEquals('00', $response->responseCode);
    }

    public function testWithPaymentMethodKey()
    {
        $paymentMethodKey = $this->getPaymentMethodKey();
        $response = $this->service->recurring($this->schedule, 10, $paymentMethodKey);

        $this->assertNotNull($response);
        $this->assertEquals('00', $response->responseCode);
    }

    private function getToken($card, $key = null)
    {
        if ($key != null and $key != "") {
            $this->publicKey = $key;
        } else {
            $this->publicKey = TestServicesConfig::validMultiUsePublicKey();
        }

        $this->tokenService = new HpsTokenService($this->publicKey);
        $tokenResponse = $this->tokenService->getToken($card);
        if (isset($tokenResponse->token_value)) {
            $token = new HpsTokenData();
            $token->tokenValue = $tokenResponse->token_value;
            return $token;
        } else {
            return $tokenResponse;
        }
    }

    private function getPaymentMethodKey()
    {
        return $this->schedule->paymentMethodKey;
    }
}
