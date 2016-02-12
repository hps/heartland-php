<?php

class HpsAltPaymentSessionInfo extends HpsAltPaymentResponse
{
    /** @var string|null */
    public $status    = null;

    /** @var HpsBuyerData|null */
    public $buyer     = null;

    /** @var HpsPaymentData|null */
    public $payment   = null;

    /** @var HpsShippingInfo|null */
    public $shipping  = null;

    /** @var array(HpsLineItem)|null */
    public $lineItems = null;

    public static function fromDict($rsp, $txnType, $returnType = 'HpsAltPaymentSessionInfo')
    {
        $sessionInfo = $rsp->Transaction->$txnType;
        $buyer     = self::nvpToArray($sessionInfo->Buyer);
        $payment   = self::nvpToArray($sessionInfo->Payment);
        $shipping  = self::nvpToArray($sessionInfo->Shipping->Address);
        $lineItems = self::nvpToArray($sessionInfo->LineItem->Detail);

        $session = parent::fromDict($rsp, $txnType, $returnType);
        $session->status = isset($sessionInfo->Status) ? (string)$sessionInfo->Status : null;

        $session->buyer = new HpsBuyerData();
        $session->buyer->emailAddress = isset($buyer['EmailAddress']) ? $buyer['EmailAddress'] : null;
        $session->buyer->payerId = isset($buyer['BuyerId']) ? $buyer['BuyerId'] : null;
        $session->buyer->status = isset($buyer['Status']) ? $buyer['Status'] : null;
        $session->buyer->countryCode = isset($buyer['CountryCode']) ? $buyer['CountryCode'] : null;
        $session->buyer->firstName = isset($buyer['FirstName']) ? $buyer['FirstName'] : null;
        $session->buyer->lastName = isset($buyer['LastName']) ? $buyer['LastName'] : null;

        $session->shipping = new HpsShippingInfo();
        $session->shipping->name = isset($shipping['ShipName']) ? $shipping['ShipName'] : null;
        $session->shipping->address = new HpsAddress();
        $session->shipping->address->address = isset($shipping['ShipAddress']) ? $shipping['ShipAddress'] : null;
        $session->shipping->address->city = isset($shipping['ShipCity']) ? $shipping['ShipCity'] : null;
        $session->shipping->address->state = isset($shipping['ShipState']) ? $shipping['ShipState'] : null;
        $session->shipping->address->zip = isset($shipping['ShipZip']) ? $shipping['ShipZip'] : null;
        $session->shipping->address->country = isset($shipping['ShipCountryCode']) ? $shipping['ShipCountryCode'] : null;

        $session->payment = new HpsPaymentData();
        $session->payment->subtotal = isset($payment['ItemAmount']) ? $payment['ItemAmount'] : null;
        $session->payment->shippingAmount = isset($payment['ShippingAmount']) ? $payment['ShippingAmount'] : null;
        $session->payment->taxAmount = isset($payment['TaxAmount']) ? $payment['TaxAmount'] : null;

        $session->lineItems = array();
        $lineItem = new HpsLineitem();
        $lineItem->name = isset($lineItems['Name']) ? $lineItems['Name'] : null;
        $lineItem->amount = isset($lineItems['Amount']) ? $lineItems['Amount'] : null;
        $lineItem->number = isset($lineItems['Number']) ? $lineItems['Number'] : null;
        $lineItem->quantity = isset($lineItems['Quantity']) ? $lineItems['Quantity'] : null;
        $lineItem->taxAmount = isset($lineItems['TaxAmount']) ? $lineItems['TaxAmount'] : null;

        return $session;
    }
}
