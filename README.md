# SecureSubmit PHP SDK

This PHP SDK makes it easy to process payments against the Heartland Payment Systems Portico Gateway.

## Installation

Add this SDK to your PHP project and require it once.

```php
<?php
require_once 'Hps.php';
?>
```

Using Composer? Require this library in your `composer.json`:

```json
{
    "require": {
        "hps/heartland-php": "*"
    }
}
```

and run `composer update` to pull down the dependency and update your autoloader.

## Usage

Supported Gateway Calls

Credit:

- [CreditAccountVerify](#verify-a-card) (4.1)
- [CreditAddToBatch](#capturing-an-authorization) (4.2)
- [CreditAuth](#create-an-authorization) (4.3)
- CreditCPCEdit (4.4)
- [CreditReturn](#refund-a-credit-transaction) (4.7)
- [CreditReversal](#reverse-a-credit-transaction) (4.8)
- [CreditSale](#create-a-charge) (4.9)
- [CreditTxnEdit](#edit-a-credit-transaction) (4.10)
- [CreditVoid](#void-a-credit-transaction) (4.11)
- [ReportActivity](#list-credit-transactions) (10.4)
- [ReportTxnDetail](#get-a-credit-transaction) (10.8)
- BatchClose (10.3)

Check:

- [CheckSale](#run-a-check) (6.1)
- [CheckVoid](#void-a-check-transaction) (6.2)

Gift Card:

- [GiftCardActivate](#activate-a-gift-card) (8.1)
- [GiftCardAddValue](#add-value-to-a-gift-card) (8.2)
- GiftCardAlias (8.3)
- [GiftCardBalance](#check-a-gift-cards-balance) (8.4)
- [GiftCardDeactivate](#deactivate-a-gift-card) (8.6)
- [GiftCardReplace](#replace-a-gift-card) (8.8)
- [GiftCardReversal](#reverse-a-gift-card-transaction) (8.12)
- GiftCardReward (8.9)
- [GiftCardSale](#charge-a-gift-card) (8.10)
- [GiftCardVoid](#void-a-gift-card-transaction) (8.11)

### Configuration / Authentication

Authentication with SecureSubmit is simple: you will pass your Secret API Key (found on your Account Settings screen) to the SDK via your configuration. Once you have done this, you can start making calls to the API immediately. Examples of authentication are as follows:

```php
<?php

$config = new HpsServicesConfig();
$config->secretApiKey =  "secret api key";
$config->versionNumber = '0000'; // this is provided to you during the certification process
$config->developerId = '000000';  // this is provided to you during the certification process

// Use this config when creating gateway service instances
$creditService = new HpsCreditService($config);
```

### Credit Transactions

#### Create a Card Holder

```php
<?php

$cardHolder =  new HpsCardHolder();
$address = new HpsAddress();
$address->zip = "47130"
$cardHolder->address = $address;
$cardHolder->firstName = 'Richard';
$cardHolder->lastName = 'Smith';
```

#### Create a Payment Method

More often than not, you will want to send calls to the gateway using a single-use token obtained via one of our Javascript libraries:

```php
<?php

$token = new HpsTokenData();
$token->tokenValue = $_POST['securesubmit_token'];
```

Other times, you'll want to create a credit card object:

```php
<?php

$card = new HpsCreditCard();
$card->number = "4111111111111111";
$card->expYear = 2015;
$card->expMonth = 12;
$card->cvv = 123;
```

#### Create a Charge

The credit sale transaction authorizes a sale purchased with a credit card. The authorization is placed in the current open batch (should auto-close for e-commerce transactions). If a batch is not open, this transaction will create an open batch.

##### Parameters

- Amount: The amount (in dollars)
- Currency: The currency (3-letter ISO code for currency).
- Card or Token: The payment method information.
- CardHolder (optional): The card holder information (used for AVS).

##### Returns: `HpsCharge`

```php
<?php

$creditService = new HpsCreditService($config);

// Charge a token
$creditService->charge(10, 'usd', $token, $cardHolder);

// Charge a card
$creditService->charge(10, 'usd', $card, $cardHolder);
```

#### Create an Authorization

A credit authorization transaction authorizes a credit card transaction. The authorization is NOT placed in the batch. The credit authorization transaction can be committed by using the capture method.

##### Parameters

- Amount: The amount (in dollars)
- Currency: The currency (3-letter ISO code for currency).
- Card: The credit card information.
- CardHolder (optional): The card holder information (used for AVS).

##### Returns: `HpsAuthorization`

- Authorization Code: If authorized, authorization code returned by the Issuer
- Avs Result Code: If address verification requested, address verification result code returned by the Issuer
- Avs Result Text: Description of AVS result code
- Cvv Result Code: If card verification was provided in the request, card verification result code provided by the Issuer
- Cvv Result Text: Description of CVV result code
- Cpc Indicator: If the commercial card was specified in the request, the commercial card response indicator returned by the Issuer
- Authorized Amount: If supplied from the Issuer on a partial authorization, the authorized amount (less than the original or requested amount).
- Card Type: Card brand name
- Descriptor: Generated by concatenating the TxnDescriptor string from the transaction request to a configurable merchant DBA name. This string is sent to the card issuer for the Merchant Name.
- Token Data
    - Token Rsp Code: The response code associated with the token Look-up or generation
    - Token Rsp Message: The response text associated with the token Look-up or generation
    - Token Value: The token used to replace swiped or manually entered card data for this transaction.

##### Important

In order to complete the transaction and recieve funds, credit authorizations must be "captured". Please refer to the next section entitled "Capturing an Authorization" for additional details.

```php
<?php

$creditService = new HpsCreditService($config);

// Authorize a token
$creditService->authorize(10, 'usd', $token, $cardHolder);

// Authorize a card
$creditService->authorize(10, 'usd', $card, $cardHolder);
```

#### Capturing an Authorization

A Capture transaction adds a previous authorization transaction to the current open batch. If a batch is not open, this transaction will create one.

##### Parameters

- Transaction Id: The authorization transaction Id.
- Amount (optional): An amount to charge (optional). Used if different from original authorization.

##### Returns: `HpsReportTransactionDetails` (See `HpsAuthorization`)

- Original Transaction Id: If the transaction performed an action on a previous transaction, this field records the transaction that was acted upon.
- Masked Card Number: Card number (masked)
- Settlement Amount: Settlement amount
- Transaction Type: The transaction type (i.e. Authorize, Capture, Charge, Refund, etc...)
- Transaction Utc Date: Date of the transaction in universal time.
- Exceptions: Any exceptions which may have occured during the transaction.
- Memo: a free-form field (for Merchant reporting/record-keeping purposes only).
- Invoice Number: This will not be used at settlement. (for Merchant reporting/record-keeping purposes only).
- Customer Id: free-form field for Merchant use. This is intended to be the customer identification. (for Merchant reporting/record-keeping purposes only).

```php
<?php

$creditService = new HpsCreditService($config);

// Authorize the token
$authorizeResponse = $creditService->authorize(10, 'usd', $token, $cardHolder);

// Record the transactionId from the authorization
$transactionId = $response->transactionId;

// Later, capture the authorization using the transactionId
$captureResponse = $creditService->capture($transactionid);
```

#### Verify a Card

A credit account verify transaction is used to verify that the account is in good standing with the issuer. This is a zero dollar transaction with no associated authorization. Since VISA and other issuers have started assessing penalties for one dollar authorizations, this provides a way for merchants to accomplish the same task while avoiding these penalties.

##### Parameters

- Card: The credit card information.
- CardHolder (optional): The card holder information (used for AVS).

##### Returns: `HpsAccountVerify` (See `HpsAuthorization`)

##### Important

American Express requires AVS data to be sent for card verification.

```php
<?php

$creditService = new HpsCreditService($config);

$creditService->verify($card, $cardHolder);
```

#### Refund a Credit Transaction

The credit return transaction returns funds to the cardholder. The transaction is generally used as a counterpart to a credit card transaction that needs to be reversed, and the batch containing the original transaction has already been closed. The credit return transaction is placed in the current open batch. If a batch is not open, this transaction will create an open batch.

##### Parameters

- Amount: The amount (in specified currency)
- Currency: The currency (3-letter ISO code for currency).
- Transaction Id: The Id of the Transaction to be refunded.

##### Returns: `HpsRefund`

```php
<?php

$creditService = new HpsCreditService($config);

// Create the charge
$chargeResponse = $creditService->charge(10, 'usd', $token, $cardHolder);

// Record the transactionId from the charge
$transactionId = $chargeResponse->transactionId;

// Later, refund the charge
$refundResponse = $creditService->refund(10, 'usd', $transactionId);
```

#### Reverse a Credit Transaction

A reverse transaction reverses a Charge or Authorize transaction from the active open authorizations or current open batch.

##### Parameters

- Transaction ID: The transaction ID of charge to reverse.
- Amount: The amount (in specified currency).
- Currency: The currency (3-letter ISO code for currency).

##### Returns: `HpsReversal`

```php
<?php

$creditService = new HpsCreditService($config);

// Create the charge
$chargeResponse = $creditService->charge(10, 'usd', $token, $cardHolder);

// Record the transactionId from the charge
$transactionId = $chargeResponse->transactionId;

// Later, reverse the charge
$reverseResponse = $creditService->reverse($transactionId, 10, 'usd');
```

#### Void a Credit Transaction

A credit void transaction is used to inactivate a transaction. The transaction must be an Authorize, Charge or Return. The transaction must be active in order to be voided. Authorize transactions do not have to be associated with a batch to be voided. Transactions may be voided after they are associated with a batch as long as the batch is not closed.

> _Note_: If the batch containing the original transaction has been closed, a Return transaction may be used to credit the cardholder.

> _Note_: If a transaction has been returned, it cannot be voided.

##### Parameters

- Transaction ID - The transaction ID of charge to void.

##### Returns: `HpsVoid`

```php
<?php

// Create the charge
$chargeResponse = $creditService->charge(10, 'usd', $token, $cardHolder);

// Record the transactionId from the charge
$transactionId = $chargeResponse->transactionId;

// Later, void the charge
$voidResponse = $creditService->void($transactionId);
```

#### Edit a Credit Transaction

An edit transaction changes the data on a previously approved Charge or Authorize transaction.

> _Note_: When the settlement amount of a transaction is altered with this service, the Portico Gateway does not send an update to the Issuer. For example, if the settlement amount of a transaction is reduced, a reversal for the difference is not sent. Likewise, if the amount is increased, an additional authorization is not sent. These additional operations are the responsibility of the POS. Additional features like this are being considered for future releases of the Portico Gateway.

##### Parameters

- Transaction ID - The transaction ID of charge to void.
- Amount - If not null, revises (replaces) the authorized amount of the original auth. If null, does not affect the authorized amount of the original auth.
- Gratuity - If not null, revises (replaces) the gratuity amount information of the original auth. If null, does not affect the gratuity amount information, if any, of the original auth. This element is for informational purposes only and does not affect the authorized amount.

##### Returns: `HpsTransaction`

```php
<?php

$creditService = new HpsCreditService($config);

// Create an authorization
$authorizeResponse = $creditService->authorize(10, 'usd', $token, $cardHolder);

// Record the authorization's transactionId
$transactionId = $authorizeResponse->transactionId;

// Edit the authorization
$creditService->edit($transactionId, 15, 5);
```

### Reporting

#### List Credit Transactions

Gets a list of transaction summaries between a set of dates and filtered if specified.

##### Parameters

- Start: Start date.
- End: End date.
- Filter By (optional): filter the result set by transaction type.

##### Returns: `array(HpsReportTransactionSummary)`

```php
<?php

$creditService = new HpsCreditService($config);

$dateFormat = 'Y-m-d\TH:i:s.00\Z';
$dateMinus10 = new DateTime();
$dateMinus10->sub(new DateInterval('P10D'));
$dateMinus10Utc = gmdate($dateFormat, $dateMinus10->Format('U'));
$nowUtc = gmdate($dateFormat);

$transactions = $creditService->listTransactions($dateMinus10Utc, $nowUtc, HpsTransactionType::CHARGE);
```

#### Get a Credit Transaction

Gets an HPS transaction given a Transaction ID.

##### Parameters

- Transaction ID: The transaction ID for the transaction.

##### Returns: `HpsReportTransactionDetails`

```php
<?php

$creditService = new HpsCreditService($config);

$dateFormat = 'Y-m-d\TH:i:s.00\Z';
$dateMinus10 = new DateTime();
$dateMinus10->sub(new DateInterval('P10D'));
$dateMinus10Utc = gmdate($dateFormat, $dateMinus10->Format('U'));
$nowUtc = gmdate($dateFormat);

$transactions = $creditService->listTransactions($dateMinus10Utc, $nowUtc, HpsTransactionType::CAPTURE);

$charge = $creditService->get($transactios[0]->transactionId);
```

### Check Transactions

#### Create a Check Holder

```php
<?php

$checkHolder = new HpsCheckHolder();
$checkHolder->address = new HpsAddress();
$checkHolder->address->address = '6860 Dallas Parkway';
$checkHolder->address->city = 'Plano';
$checkHolder->address->state = 'TX';
$checkHolder->address->zip = '75024';
$checkHolder->dlNumber = '1234567';
$checkHolder->dlState = 'TX';
$checkHolder->firstName = 'John';
$checkHolder->lastName = 'Doe';
$checkHolder->phone = '1234567890';
```

#### Create a Check

```php
<?php

$check = new HpsCheck();
$check->accountNumber = '24413815';
$check->routingNumber = '490000018';
$check->checkType = HpsCheckType::PERSONAL;
$check->secCode = HpsSECCode::PPD;
$check->accountType = HpsAccountType::CHECKING;
$check->checkHolder = $checkHolder;
```

#### Run a Check

##### Parameters

- Check: The check.
- Amount: The amount (in dollars)

##### Returns: `HpsCheckResponse`

```php
<?php

$checkService = new HpsCheckService($config);

$checkService->sale($check, 10);
```

#### Void a Check Transaction

##### Parameters

- Transaction ID: the transaction ID.

##### Returns: `HpsCheckResponse`

```php
<?php

$checkService = new HpsCheckService($config);

// Run a Check
$saleResponse = $checkService->sale($check, 10);

// Record the sale's transactionId
$transactionId = $saleResponse->transactionId;

// Later, void the transaction with the transactionId
$voidResponse = $checkService->void($transactionId);
```

### Gift Card Transactions

#### Create a Gfit Card

```php
<?php

$giftCard = new HpsGiftCard();
$giftCard->number = "5022440000000000098";
$giftCard->expMonth = '12';
$giftCard->expYear = '39';
```

#### Charge a Gift Card

##### Parameters

- Amount: The amount (in dollars)
- Currency: The currency (3-letter ISO code for currency).
- Card or Token: The payment method information.
- CardHolder (optional): The card holder information (used for AVS).

##### Returns: `HpsGiftCardSale`

```php
<?php

$giftService = new HpsGiftCardService($config);

$giftService->sale($giftCard, 10);
```

#### Activate a Gift Card

##### Parameters

- Amount: The amount (in dollars)
- Currency: The currency (3-letter ISO code for currency).
- Gift Card: The gift card.

##### Returns: `HpsGiftCardActivate`

```php
<?php

$giftService = new HpsGiftCardService($config);

$giftService->activate(100, 'usd', $giftCard);
```

#### Add Value to a Gift Card

##### Parameters

- Amount: The amount (in dollars)
- Currency: The currency (3-letter ISO code for currency).
- Gift Card: The gift card.

##### Returns: `HpsGiftCardAddValue`

```php
<?php

$giftService = new HpsGiftCardService($config);

$giftService->activate(100, 'usd', $giftCard);
```

#### Check a Gift Card's Balance

##### Parameters

- Gift Card: The gift card.

##### Returns: `HpsGiftCardBalance`

```php
<?php

$giftService = new HpsGiftCardService($config);

$balanceResponse = $giftService->balance($giftCard);

// Record the balance
$balance = $balanceResponse->balanceAmount;
```

#### Deactivate a Gift Card

##### Parameters

- Gift Card: The gift card.

##### Returns: `HpsGiftCardDeactivate`

```php
<?php

$giftService = new HpsGiftCardService($config);

$giftService->deactivate($giftCard);
```

#### Replace a Gift Card

##### Parameters

- Old Gift Card: The gift card to be replaced.
- New Gift Card: The gift card to replace the old.

##### Returns: `HpsGiftCardReplace`

```php
<?php

$giftService = new HpsGiftCardService($config);

$giftService->replace($oldGiftCard, $newGiftCard);
```

#### Reverse a Gift Card Transaction

##### Parameters

- Transaction ID: The transaction ID.

##### Returns: `HpsGiftCardReversal`

```php
<?php

$giftService = new HpsGiftCardService($config);

// Charge the Gift Card
$saleResponse = $giftService->sale($giftCard, 10);

// Record the transactionId
$transactionId = $saleResponse->transactionId;

// Later, reverse the transaction
$reverseResponse = $giftService->reverse($transactionId);
```

#### Void a Gift Card Transaction

##### Parameters

- Transaction ID: The transaction ID.

##### Returns: `HpsGiftCardVoid`

```php
<?php

$giftService = new HpsGiftCardService($config);

// Charge the Gift Card
$saleResponse = $giftService->sale($giftCard, 10);

// Record the transactionId
$transactionId = $saleResponse->transactionId;

// Later, void the transaction
$voidResponse = $giftService->void($transactionId);
```

## Testing

Clone this repository locally, install dependencies with Composer, and run PHPUnit against the provided tests.

```
$ git clone https://github.com/SecureSubmit/heartland-php.git
$ cd heartland-php
$ composer install
$ php vendor/bin/phpunit -c tests/phpunit.xml
```

This will run through all of our test suites by default. To run a single test suite, pass the `--testsuite`
option to `php vendor/bin/phpunit` with one of the following values:

- `fluent`
- `gateway-check`
- `gateway-credit`
- `gateway-debit`
- `gateway-giftcard`
- `gateway-token`
- `general`
- `certification`

Example:

```
$ php vendor/bin/phpunit -c tests/phpunit.xml --testsuite certification
```

## Contributing

1. Fork it
2. Create your feature branch (`git checkout -b my-new-feature`)
3. Commit your changes (`git commit -am 'Add some feature'`)
4. Push to the branch (`git push origin my-new-feature`)
5. Create new Pull Request
