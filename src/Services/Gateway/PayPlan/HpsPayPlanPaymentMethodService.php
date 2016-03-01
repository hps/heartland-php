<?php

class HpsPayPlanPaymentMethodService extends HpsRestGatewayService
{
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

    private function editCreditCard(HpsPayPlanPaymentMethod $paymentMethod)
    {
        $data = $paymentMethod->getEditableFieldsWithValues();
        $result = $this->doRequest($data, array(
            'verb'     => 'PUT',
            'endpoint' => 'paymentMethodsCreditCard/'.$paymentMethod->paymentMethodKey,
        ));
        return $this->hydrateObject($result, 'HpsPayPlanPaymentMethod');
    }

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
