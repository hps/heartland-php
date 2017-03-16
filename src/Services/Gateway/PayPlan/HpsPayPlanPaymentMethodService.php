<?php

/**
 * Class HpsPayPlanPaymentMethodService
 */
class HpsPayPlanPaymentMethodService extends HpsRestGatewayService
{
    /**
     * @param \HpsPayPlanPaymentMethod $paymentMethod
     *
     * @return mixed
     */
    public function add(HpsPayPlanPaymentMethod $paymentMethod)
    {
        $result = null;
        if ($paymentMethod->paymentMethodType == HpsPayPlanPaymentMethodType::ACH) {
            $result = $this->addACH($paymentMethod);
        } else {
            $result = $this->addCreditCard($paymentMethod);
        }
        return $this->hydrateObject($result, 'HpsPayPlanPaymentMethod');
    }
    /**
     * @param \HpsPayPlanPaymentMethod $paymentMethod
     *
     * @return mixed
     */
    public function edit(HpsPayPlanPaymentMethod $paymentMethod)
    {
        $result = null;
        if ($paymentMethod->paymentMethodType == HpsPayPlanPaymentMethodType::ACH) {
            $result = $this->editACH($paymentMethod);
        } else {
            $result = $this->editCreditCard($paymentMethod);
        }
        return $this->hydrateObject($result, 'HpsPayPlanPaymentMethod');
    }
    /**
     * @param array $searchFields
     *
     * @return object
     */
    public function findAll($searchFields = array())
    {
        // Cannot have an array as the root object
        // in a JSON document
        $data = $searchFields === array() ? (object)array() : $searchFields;
        $results = $this
            ->doRequest($data, array(
                'verb'     => 'POST',
                'endpoint' => 'searchPaymentMethods',
            ));

        return $this->hydrateSearchResults($results, 'HpsPayPlanPaymentMethod');
    }
    /**
     * @param $paymentMethod
     *
     * @return mixed
     */
    public function get($paymentMethod)
    {
        $id = null;
        if ($paymentMethod instanceof HpsPayPlanPaymentMethod) {
            $id = $paymentMethod->paymentMethodKey;
        } else {
            $id = $paymentMethod;
        }
        $result = $this->doRequest(null, array(
            'verb'     => 'GET',
            'endpoint' => 'paymentMethods/'.$id
        ));
        return $this->hydrateObject($result, 'HpsPayPlanPaymentMethod');
    }
    /**
     * @param      $paymentMethod
     * @param bool $forceDelete
     *
     * @return mixed
     */
    public function delete($paymentMethod, $forceDelete = false)
    {
        $id = null;
        if ($paymentMethod instanceof HpsPayPlanPaymentMethod) {
            $id = $paymentMethod->paymentMethodKey;
        } else {
            $id = $paymentMethod;
        }

        $data = array(
            'forceDelete' => $forceDelete,
        );
        return $this->doRequest($data, array(
            'verb'     => 'DELETE',
            'endpoint' => 'paymentMethods/'.$id,
        ));
    }
    /**
     * @param \HpsPayPlanPaymentMethod $paymentMethod
     *
     * @return mixed
     */
    private function addCreditCard(HpsPayPlanPaymentMethod $paymentMethod)
    {
        $data = $paymentMethod->getEditableFieldsWithValues();
        $data['customerKey'] = $paymentMethod->customerKey;
        if (isset($paymentMethod->accountNumber)) {
            $data['accountNumber'] = $paymentMethod->accountNumber;
        } else if (isset($paymentMethod->paymentToken)) {
            $data['paymentToken'] = $paymentMethod->paymentToken;
        }
        $result = $this->doRequest($data, array(
            'verb'     => 'POST',
            'endpoint' => 'paymentMethodsCreditCard',
        ));
        return $this->hydrateObject($result, 'HpsPayPlanPaymentMethod');
    }
    /**
     * @param \HpsPayPlanPaymentMethod $paymentMethod
     *
     * @return mixed
     */
    private function editCreditCard(HpsPayPlanPaymentMethod $paymentMethod)
    {
        $data = $paymentMethod->getEditableFieldsWithValues();
        $result = $this->doRequest($data, array(
            'verb'     => 'PUT',
            'endpoint' => 'paymentMethodsCreditCard/'.$paymentMethod->paymentMethodKey,
        ));
        return $this->hydrateObject($result, 'HpsPayPlanPaymentMethod');
    }
    /**
     * @param \HpsPayPlanPaymentMethod $paymentMethod
     *
     * @return mixed
     */
    private function addACH(HpsPayPlanPaymentMethod $paymentMethod)
    {
        $data = $paymentMethod->getEditableFieldsWithValues();
        $data['customerKey'] = $paymentMethod->customerKey;
        $data['accountNumber'] = $paymentMethod->accountNumber;
        $data['accountType'] = $paymentMethod->accountType;
        $data['achType'] = $paymentMethod->achType;
        $data['routingNumber'] = $paymentMethod->routingNumber;
        $result = $this->doRequest($data, array(
            'verb'     => 'POST',
            'endpoint' => 'paymentMethodsACH',
        ));
        return $this->hydrateObject($result, 'HpsPayPlanPaymentMethod');
    }
    /**
     * @param \HpsPayPlanPaymentMethod $paymentMethod
     *
     * @return mixed
     */
    private function editACH(HpsPayPlanPaymentMethod $paymentMethod)
    {
        $data = $paymentMethod->getEditableFieldsWithValues();
        $result = $this->doRequest($data, array(
            'verb'     => 'PUT',
            'endpoint' => 'paymentMethodsACH/'.$paymentMethod->paymentMethodKey,
        ));
        return $this->hydrateObject($result, 'HpsPayPlanPaymentMethod');
    }
}
