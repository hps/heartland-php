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
            ->doRequest('POST', 'searchPaymentMethods', $data);

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
        $result = $this->doRequest('GET', 'paymentMethods/'.$id);
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
        return $this->doRequest('DELETE', 'paymentMethods/'.$id, $data);
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
        $result = $this->doRequest('POST', 'paymentMethodsCreditCard', $data);
        return $this->hydrateObject($result, 'HpsPayPlanPaymentMethod');
    }

    private function editCreditCard(HpsPayPlanPaymentMethod $paymentMethod)
    {
        $data = $paymentMethod->getEditableFieldsWithValues();
        $result = $this->doRequest('PUT', 'paymentMethodsCreditCard/'.$paymentMethod->paymentMethodKey, $data);
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
        $result = $this->doRequest('POST', 'paymentMethodsACH', $data);
        return $this->hydrateObject($result, 'HpsPayPlanPaymentMethod');
    }

    private function editACH(HpsPayPlanPaymentMethod $paymentMethod)
    {
        $data = $paymentMethod->getEditableFieldsWithValues();
        $result = $this->doRequest('PUT', 'paymentMethodsACH/'.$paymentMethod->paymentMethodKey, $data);
        return $this->hydrateObject($result, 'HpsPayPlanPaymentMethod');
    }
}
