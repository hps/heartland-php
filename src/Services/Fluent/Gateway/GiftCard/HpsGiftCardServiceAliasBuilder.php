<?php

/**
 * A fluent interface for creating and executing an alias
 * transaction through the HpsGiftCardService.
 *
 * @method HpsGiftCardServiceAliasBuilder withCard(HpsGiftCard $card)
 * @method HpsGiftCardServiceAliasBuilder withToken(HpsTokenData $token)
 * @method HpsGiftCardServiceAliasBuilder withAlias(string $alias)
 * @method HpsGiftCardServiceAliasBuilder withAction(string $action)
 */
class HpsGiftCardServiceAliasBuilder extends HpsBuilderAbstract
{
    /** @var HpsGiftCard|null */
    protected $card   = null;

    /** @var HpsTokenData|null */
    protected $token    = null;

    /** @var string|null */
    protected $alias  = null;

    /** @var string|null */
    protected $action = null;

    /**
     * Instatiates a new HpsGiftCardServiceAliasBuilder
     *
     * @param HpsSoapGatewayService $service
     */
    public function __construct(HpsSoapGatewayService $service)
    {
        parent::__construct($service);
        $this->setUpValidations();
    }

    /**
     * Creates an alias transaction through the HpsGiftCardService
     */
    public function execute()
    {
        parent::execute();

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsGiftAlias = $xml->createElement('hps:GiftCardAlias');
        $hpsBlock1 = $xml->createElement('hps:Block1');

        $hpsBlock1->appendChild($xml->createElement('hps:Action', $this->action));
        $hpsBlock1->appendChild($xml->createElement('hps:Alias', $this->alias));

        if ($this->token != null && ($this->token instanceof HpsTokenData)) {
            if ($this->card == null) {
                $this->card = new HpsGiftCard();
            }
            $this->card->tokenValue = $this->token->tokenValue;
        }
        $cardData = $this->service->_hydrateGiftCardData($this->card, $xml);
        $hpsBlock1->appendChild($cardData);

        $hpsGiftAlias->appendChild($hpsBlock1);
        $hpsTransaction->appendChild($hpsGiftAlias);

        return $this->service->_submitTransaction($hpsTransaction, 'GiftCardAlias');
    }

    /**
     * Setups up validations for building aliases.
     *
     * @return null
     */
    private function setUpValidations()
    {
        $this
            ->addValidation(array($this, 'onlyOnePaymentMethod'), 'HpsArgumentException', 'Alias can only use one payment method')
            ->addValidation(array($this, 'aliasNotNull'), 'HpsArgumentException', 'Alias needs an alias')
            ->addValidation(array($this, 'actionNotNull'), 'HpsArgumentException', 'Alias needs an action');
    }

    /**
     * Ensures there is only one payment method, and checks that
     * there is only one card or one token in use. Both cannot be
     * used.
     *
     * @param array $actionCounts
     *
     * @return bool
     */
    public function onlyOnePaymentMethod($actionCounts)
    {
        $methods = 0;
        if (isset($actionCounts['card']) && $actionCounts['card'] == 1) {
            $methods++;
        }
        if (isset($actionCounts['token']) && $actionCounts['token'] == 1) {
            $methods++;
        }
        return $methods == 1;
    }

    /**
     * Ensures a card has been set.
     *
     * @param array $actionCounts
     *
     * @return bool
     */
    protected function cardNotNull($actionCounts)
    {
        if ($this->action == 'CREATE') {
            return $this->card == null;
        }

        return isset($actionCounts['card']);
    }

    /**
     * Ensures an alias has been set.
     *
     * @param array $actionCounts
     *
     * @return bool
     */
    protected function aliasNotNull($actionCounts)
    {
        return isset($actionCounts['alias']);
    }

    /**
     * Ensures a action has been set.
     *
     * @param array $actionCounts
     *
     * @return bool
     */
    protected function actionNotNull($actionCounts)
    {
        return isset($actionCounts['action']);
    }
}
