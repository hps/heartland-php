<?php
/**
 * MasterPass using Cardinal Commerce
 *
 * PHP Version 5.2+
 *
 * @category PHP
 * @package  HPS
 * @author   Heartland Payment Systems <EntApp_DevPortal@e-hps.com>
 * @license  https://github.com/hps/heartland-php/blob/master/LICENSE.txt Custom
 * @link     https://github.com/hps/heartland-php
 */

/**
 * MasterPass using Cardinal Commerce
 *
 * @category PHP
 * @package  HPS
 * @author   Heartland Payment Systems <EntApp_DevPortal@e-hps.com>
 * @license  https://github.com/hps/heartland-php/blob/master/LICENSE.txt Custom
 * @link     https://github.com/hps/heartland-php
 */
class HpsMasterPassService
    extends HpsCentinelGatewayService
    implements HpsAltPaymentServiceInterface
{
    protected static $currencyCodes = array(
        'usd' => '840',
    );

    /**
     * Updates the Merchant’s front-end order number with their back-end order
     * number in the Centinel system. The Merchant’s original order number is
     * persisted and searchable. The transaction expects the OrderId and
     * OrderNumber values to correspond to the original Lookup response message.
     *
     * @param string       $orderId   order id from Cardinal
     * @param HpsOrderData $orderData Cardinal/MasterPass specific data
     *
     * @return object
     */
    public function addOrderNumber(
        $orderId,
        HpsOrderData $orderData
    ) {
        $payload = array(
            'OrderId' => $orderId,
            'OrderNumber' => $orderData->orderNumber,
            'TransactionType' => 'WT',
        );
        return $this->submitTransaction($payload, 'cmpi_add_order_number');
    }

    /**
     * Responsible for returning the status of the MasterPass transaction to the
     * Merchant. The message will return the status of the transaction, enabling
     * the Merchant to handle the order according to the outcome. In the event
     * that the ErrorNo element is 0 (zero) then the PAResStatus value will define
     * how the transaction should be processed. Based on the transaction outcome
     * the Merchant's order management system should be updated and the appropriate
     * message should be displayed to the consumer. In the event that a nonzero
     * ErrorNo value is returned or PAResStatus value is not Y, the consumer should
     * be prompted for an alternate form of payment.
     *
     * @param string       $orderId       order id from Cardinal
     * @param string       $oauthToken    oauth token from MasterPass
     * @param string       $oauthVerifier oauth verifier from MasterPass
     * @param string       $payload       payload data from Cardinal
     * @param string       $resourceUrl   resource URL from MasterPass
     * @param HpsOrderData $orderData     Cardinal/MasterPass specific data
     *
     * @return object
     */
    public function authenticate(
        $orderId,
        $oauthToken,
        $oauthVerifier,
        $payload,
        $resourceUrl,
        HpsOrderData $orderData = null
    ) {
        $data = array(
            'OrderId' => $orderId,
            'PAResPayload' => $payload,
            'Status' => $orderData->transactionStatus,
            'TransactionType' => 'WT',
        );

        if ($orderData->checkoutType === HpsCentinelCheckoutType::PAIRING
            || $orderData->checkoutType === HpsCentinelCheckoutType::PAIRING_CHECKOUT
        ) {
            $data['PairingToken'] = $orderData->pairingToken;
            $data['PairingVerifier'] = $orderData->pairingVerifier;
        }

        if ($orderData->checkoutType === null
            || $orderData->checkoutType === HpsCentinelCheckoutType::LIGHTBOX
            || $orderData->checkoutType === HpsCentinelCheckoutType::PAIRING_CHECKOUT
        ) {
            $data['CheckoutResourceUrl'] = $resourceUrl;
            $data['OAuthToken'] = $oauthToken;
            $data['OAuthVerifier'] = $oauthVerifier;
        }

        return $this->submitTransaction($data, 'cmpi_authenticate');
    }

    /**
     * Responsible for authorizing the transaction. Once authorized, the
     * transaction amount can be captured at a later point in time. Once the
     * Merchant is ready to perform the actual Authorization of funds the
     * Authorize  message should be processes referencing the original OrderId
     * returned in the Lookup message. This authorization request checks the
     * availability of the Customer’s funds to obtain an honor period for
     * capture/settlement.
     *
     * @param string                  $orderId         order id from Cardinal
     * @param mixed                   $amount          amount to be authorized
     * @param string                  $currency        currency code
     * @param HpsBuyerData            $buyer           buyer information
     * @param HpsPaymentData          $payment         payment information
     * @param HpsShippingInfo         $shippingAddress shipping information
     * @param array<int, HpsLineItem> $lineItems       line items from order
     * @param HpsOrderData            $orderData       Cardinal/MasterPass specific
     *                                                 data
     *
     * @return object
     */
    public function authorize(
        $orderId,
        $amount,
        $currency,
        HpsBuyerData $buyer = null,
        HpsPaymentData $payment = null,
        HpsShippingInfo $shippingAddress = null,
        $lineItems = null,
        HpsOrderData $orderData = null
    ) {
        $payload = array(
            'TransactionType' => 'WT',
            'Amount' => $this->formatAmount($amount),
            'OrderId' => $orderId,
            'CurrencyCode' => $this->currencyStringToNumeric($currency),
        );
        if ($buyer !== null) {
            $payload = array_merge($payload, $this->hydrateBuyerData($buyer));
        }
        if ($payment !== null) {
            $payload = array_merge($payload, $this->hydratePaymentData($payment));
        }
        if ($shippingAddress !== null) {
            $payload = array_merge(
                $payload,
                $this->hydrateShippingInfo($shippingAddress)
            );
        }
        if ($lineItems !== null) {
            $payload = array_merge($payload, $this->hydrateLineItems($lineItems));
        }
        return $this->submitTransaction($payload, 'cmpi_authorize');
    }

    /**
     * Responsible for settling funds from previous authorization transaction.
     * Payment for the complete or partial amount of the authorization is available.
     * Multiple captures can be processed against a single Authorization up to 100%
     * of the authorization.
     *
     * @param string       $orderId   order id from Cardinal
     * @param mixed        $amount    amount to be authorized
     * @param HpsOrderData $orderData Cardinal/MasterPass specific data
     *
     * @return object
     */
    public function capture(
        $orderId,
        $amount,
        HpsOrderData $orderData = null
    ) {
        $payload = array(
            'Amount' => $this->formatAmount($amount),
            'CurrencyCode' => $this->currencyStringToNumeric(
                $orderData->currencyCode
            ),
            'OrderId' => $orderId,
            'OrderNumber' => $orderData->orderNumber,
            'TransactionType' => 'WT',
        );
        return $this->submitTransaction($payload, 'cmpi_capture');
    }

    /**
     * Responsible for initiating the MasterPass transaction. The Lookup Message
     * is constructed and sent to the Centinel platform for processing. The Lookup
     * Message requires transaction specific data elements to be formatted on the
     * request message. Please refer to the Message API section for the complete
     * list of required message elements.
     *
     * The Centinel platform will then redirect the consumer back to the TermUrl
     * representing a web page  on the merchant's website. At that point, the
     * merchant will process the Authenticate message to retrieve the status from
     * the MasterPass processing.
     *
     * @param mixed                   $amount          amount to be authorized
     * @param string                  $currency        currency code
     * @param HpsBuyerData            $buyer           buyer information
     * @param HpsPaymentData          $payment         payment information
     * @param HpsShippingInfo         $shippingAddress shipping information
     * @param array<int, HpsLineItem> $lineItems       line items from order
     * @param HpsOrderData            $orderData       Cardinal/MasterPass specific
     *                                                 data
     *
     * @return object
     */
    public function createSession(
        $amount,
        $currency,
        HpsBuyerData $buyer = null,
        HpsPaymentData $payment = null,
        HpsShippingInfo $shippingAddress = null,
        $lineItems = null,
        HpsOrderData $orderData = null
    ) {
        $payload = array(
            'TransactionType' => 'WT',
            'OverridePaymentMethod' => 'MPPWLT',
            'Amount' => $this->formatAmount($amount),
            'CurrencyCode' => $this->currencyStringToNumeric($currency),
            'OverrideCheckoutType' => $this->getCheckoutType($orderData),
            'ConnectTimeout' => '10000',
            'TransactionMode' => 'S',
            'OrderNumber' => $orderData->orderNumber,
            'IPAddress' => $orderData->ipAddress,
            'BrowserHeader' => $orderData->browserHeader,
            'UserAgent' => $orderData->userAgent,
            'OriginUrl' => $orderData->originUrl,
            'TermUrl' => $orderData->termUrl,
        );
        if ($orderData->orderId !== null) {
            $payload['OrderId'] = $orderData->orderId;
        }
        if ($buyer !== null) {
            $payload = array_merge($payload, $this->hydrateBuyerData($buyer));
        }
        if ($payment !== null) {
            $payload = array_merge($payload, $this->hydratePaymentData($payment));
        }
        if ($shippingAddress !== null) {
            $payload = array_merge(
                $payload,
                $this->hydrateShippingInfo($shippingAddress)
            );
        }
        if ($lineItems !== null) {
            $payload = array_merge($payload, $this->hydrateLineItems($lineItems));
        }
        return $this->submitTransaction($payload, 'cmpi_lookup');
    }

    /**
     * Gives Merchants the ability to provide the consumer the opportunity to
     * pre-select their checkout options before completing checkout.
     *
     * @param string       $longAccessToken Access token from Cardinal/MasterPass
     * @param HpsOrderData $orderData       Cardinal/MasterPass specific data
     *
     * @return object
     */
    public function preApproval(
        $longAccessToken,
        HpsOrderData $orderData = null
    ) {
        $payload = array(
            'LongAccessToken' => $longAccessToken,
            'SubMsgType' => 'cmpi_preapproval',
            'TransactionType' => 'WT',
        );
        return $this->submitTransaction($payload, 'cmpi_baseserver_api');
    }

    /**
     * Responsible for crediting the consumer some portion or all of the original
     * settlement amount. Multiple refunds can be processed against the original
     * capture transaction.
     *
     * @param string       $orderId   order id from Cardinal
     * @param boolean      $isPartial flag for partial refund
     * @param string       $amount    amount to be refunded
     * @param HpsOrderData $orderData Cardinal/MasterPass specific data
     *
     * @return object
     */
    public function refund(
        $orderId,
        $isPartial = false,
        $amount = null,
        HpsOrderData $orderData = null
    ) {
        $payload = array(
            'Amount' => $this->formatAmount($amount),
            'CurrencyCode' => $this->currencyStringToNumeric(
                $orderData->currencyCode
            ),
            'OrderId' => $orderId,
            'TransactionType' => 'WT',
        );
        return $this->submitTransaction($payload, 'cmpi_refund');
    }

    /**
     * Responsible for authorizing the transaction. Once authorized, the
     * transaction amount can be captured at a later point in time. Once the
     * Merchant is ready to perform the actual Authorization of funds the
     * Authorize  message should be processes referencing the original OrderId
     * returned in the Lookup message. This authorization request checks the
     * availability of the Customer’s funds to obtain an honor period for
     * capture/settlement.
     *
     * @param string                  $orderId         order id from Cardinal
     * @param mixed                   $amount          amount to be authorized
     * @param string                  $currency        currency code
     * @param HpsBuyerData            $buyer           buyer information
     * @param HpsPaymentData          $payment         payment information
     * @param HpsShippingInfo         $shippingAddress shipping information
     * @param array<int, HpsLineItem> $lineItems       line items from order
     * @param HpsOrderData            $orderData       Cardinal/MasterPass specific
     *                                                 data
     *
     * @return object
     */
    public function sale(
        $orderId,
        $amount,
        $currency,
        HpsBuyerData $buyer = null,
        HpsPaymentData $payment = null,
        HpsShippingInfo $shippingAddress = null,
        $lineItems = null,
        HpsOrderData $orderData = null
    ) {
        $authorization = $this->authorize(
            $orderId,
            $amount,
            $currency,
            $buyer,
            $payment,
            $shippingAddress,
            $lineItems,
            $orderData
        );
        if (null === $orderData) {
            $orderData = new HpsOrderData();
        }
        $orderData->currencyCode = $currency;
        $capture = $this->capture(
            $orderId,
            $this->formatAmount($amount),
            $orderData
        );
        return (object)array(
            'authorization' => $authorization,
            'capture'       => $capture,
        );
    }

    /**
     * Cancels an authorized transaction with MasterPass. Any hold on consumer
     * funds will be removed when the transaction is voided.
     *
     * @param string       $orderId   order id from Cardinal
     * @param HpsOrderData $orderData Cardinal/MasterPass specific data
     *
     * @return object
     */
    public function void(
        $orderId,
        HpsOrderData $orderData = null
    ) {
        $payload = array(
            'OrderId' => $orderId,
        );
        return $this->submitTransaction($payload, 'cmpi_void');
    }

    /**
     * Gets information about a MasterPass session
     *
     * @param string       $orderId   order id from Cardinal
     * @param HpsOrderData $orderData Cardinal/MasterPass specific data
     *
     * @return object
     */
    public function sessionInfo(
        $orderId,
        HpsOrderData $orderData = null
    ) {
    }

    /**
     * Gets checkout type from `$orderData` or `lightbox` if not set
     *
     * @param HpsOrderData $orderData the order data
    *
     * @return string
     */
    protected function getCheckoutType(HpsOrderData $orderData)
    {
        return isset($orderData->checkoutType)
            ? $orderData->checkoutType
            : 'lightbox';
    }
    /**
     * Converts a 3-letter currency code to 3-digit ISO 4217 version
     *
     * @param string $currency currency code
     *
     * @return string
     * @throws \HpsArgumentException
     * @raises HpsArgumentException
     */
    protected function currencyStringToNumeric($currency)
    {
        if (!in_array(strtolower($currency), array_keys(self::$currencyCodes))) {
            throw new HpsArgumentException(
                'Currency is not supported',
                HpsExceptionCodes::INVALID_CURRENCY
            );
        }
        return self::$currencyCodes[strtolower($currency)];
    }

    /**
     * Converts HpsBuyerData into expected format for Cardinal
     *
     * @param HpsBuyerData $buyer buyer information
     *
     * @return array<string, string>
     */
    protected function hydrateBuyerData(HpsBuyerData $buyer)
    {
        return array(
            'BillingAddress1'    => $buyer->address->address,
            'BillingCity'        => $buyer->address->city,
            'BillingCountryCode' => $buyer->countryCode,
            'BillingFirstName'   => $buyer->firstName,
            'BillingLastName'    => $buyer->lastName,
            'BillingMiddleName'  => $buyer->middleName,
            'BillingPhone'       => $buyer->phoneNumber,
            'BillingPostalCode'  => $buyer->address->zip,
            'BillingState'       => $buyer->address->state,
        );
    }

    /**
     * Converts HpsLineItem's into expected format for Cardinal
     *
     * @param array<int, HpsLineItem> $items line items from order
     *
     * @return array<string, string>
     */
    protected function hydrateLineItems($items)
    {
        $result = array();
        if ($items == null) {
            return $result;
        }

        foreach ($items as $i => $item) {
            $result = array_merge(
                $result,
                array(
                    'Item_Name_'     . $i => $item->name,
                    'Item_Desc_'     . $i => $item->description,
                    'Item_Price_'    . $i => $this->formatAmount($item->amount),
                    'Item_Quantity_' . $i => $item->quantity,
                    'Item_SKU_'      . $i => $item->number,
                )
            );
        }

        return $result;
    }

    /**
     * Converts HpsShippingInfo into expected format for Cardinal
     *
     * @param HpsPaymentData $payment payment information
     *
     * @return array<string, string>
     */
    protected function hydratePaymentData(HpsPaymentData $payment)
    {
        return array(
            'TaxAmount'      => $this->formatAmount($payment->taxAmount),
            'ShippingAmount' => $this->formatAmount($payment->shippingAmount),
        );
    }

    /**
     * Converts HpsShippingInfo into expected format for Cardinal
     *
     * @param HpsShippingInfo $shipping shipping information
     *
     * @return array<string, string>
     */
    protected function hydrateShippingInfo(HpsShippingInfo $shipping)
    {
        return array(
            'ShippingAddress1'    => $shipping->address->address,
            'ShippingCity'        => $shipping->address->city,
            'ShippingCountryCode' => $shipping->countryCode,
            'ShippingFirstName'   => $shipping->firstName,
            'ShippingLastName'    => $shipping->lastName,
            'ShippingMiddleName'  => $shipping->middleName,
            'ShippingPhone'       => $shipping->phoneNumber,
            'ShippingPostalCode'  => $shipping->address->zip,
            'ShippingState'       => $shipping->address->state,
        );
    }

    /**
     * Formats the amount in form of cents
     *
     * @param mixed $amount amount to be formatted
     *
     * @return string
     */
    protected function formatAmount($amount)
    {
        return sprintf('%s', ceil(intval($amount) * 100));
        // return $amount;
    }
    /**
     * Processes the response from Cardinal
     *
     * @param object $response response from Cardinal
     *
     * @return null
     * @throws \HpsException
     */
    protected function processGatewayResponse($response)
    {
        $gatewayRspCode = isset($response->ErrorNo)
                        ? (string)$response->ErrorNo
                        : null;

        if ($gatewayRspCode == '0') {
            return;
        }

        throw new HpsException((string)$response->ErrorDesc);
    }
    /**
     * Processes the response from MasterPass
     *
     * @param object $response response from Cardinal
     *
     * @return null
     * @throws \HpsException
     */
    protected function processProcessorResponse($response)
    {
        $statusCode = isset($response->StatusCode)
                    ? (string)$response->StatusCode
                    : null;

        if ($statusCode == null || $statusCode == 'Y') {
            return;
        }

        throw new HpsException((string)$response->ErrorDesc);
    }

    /**
     * Submits a transaction to the gateway
     *
     * @param array<string, string> $request request payload
     * @param string                $txnType type of transaction to be ran
     *
     * @return object
     */
    protected function submitTransaction(
        $request,
        $txnType
    ) {
        $request = array_merge($request, array('MsgType' => $txnType));
        $response = $this->doRequest($request);

        $this->processGatewayResponse($response);
        $this->processProcessorResponse($response);

        $result = null;

        switch ($txnType) {
        case 'cmpi_lookup':
            $result = HpsCardinalMPILookupResponse::fromObject($response);
            break;
        case 'cmpi_authenticate':
            $result = HpsCardinalMPIAuthenticateResponse::fromObject($response);
            break;
        case 'cmpi_baseserver_api':
            $result = HpsCardinalMPIPreapprovalResponse::fromObject($response);
            break;
        case 'cmpi_authorize':
            $result = HpsCardinalMPIAuthorizeResponse::fromObject($response);
            break;
        case 'cmpi_capture':
            $result = HpsCardinalMPICaptureResponse::fromObject($response);
            break;
        case 'cmpi_refund':
            $result = HpsCardinalMPIRefundResponse::fromObject($response);
            break;
        case 'cmpi_void':
            $result = HpsCardinalMPIVoidResponse::fromObject($response);
            break;
        case 'cmpi_add_order_number':
            $result = HpsCardinalMPIAddOrderNumberResponse::fromObject($response);
            break;
        }
        return $result;
    }
}
