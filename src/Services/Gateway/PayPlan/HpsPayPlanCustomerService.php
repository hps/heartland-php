<?php

class HpsPayPlanCustomerService extends HpsRestGatewayService
{
    public function add(HpsPayPlanCustomer $customer)
    {
        $data = $customer->getEditableFieldsWithValues();
        $result = $this->doRequest($data, array(
            'verb'     => 'POST',
            'endpoint' => 'customers',
        ));
        return $this->hydrateObject($result, 'HpsPayPlanCustomer');
    }

    public function edit(HpsPayPlanCustomer $customer)
    {
        $data = $customer->getEditableFieldsWithValues();
        $result = $this->doRequest($data, array(
            'verb'     => 'PUT',
            'endpoint' => 'customers/'.$customer->customerKey,
        ));
        return $this->hydrateObject($result, 'HpsPayPlanCustomer');
    }

    public function findAll($searchFields = array())
    {
        // Cannot have an array as the root object
        // in a JSON document
        $data = $searchFields === array() ? (object)array() : $searchFields;
        $results = $this
            ->doRequest($data, array(
                'verb'     => 'POST',
                'endpoint' => 'searchCustomers',
            ));

        return $this->hydrateSearchResults($results, 'HpsPayPlanCustomer');
    }

    public function get($customer)
    {
        $id = null;
        if ($customer instanceof HpsPayPlanCustomer) {
            $id = $customer->customerKey;
        } else {
            $id = $customer;
        }
        $result = $this->doRequest(null, array(
            'verb'     => 'GET',
            'endpoint' => 'customers/'.$id,
        ));
        return $this->hydrateObject($result, 'HpsPayPlanCustomer');
    }

    public function delete($customer, $forceDelete = false)
    {
        $id = null;
        if ($customer instanceof HpsPayPlanCustomer) {
            $id = $customer->customerKey;
        } else {
            $id = $customer;
        }

        $data = array(
            'forceDelete' => $forceDelete,
        );
        return $this->doRequest($data, array(
            'verb'     => 'DELETE',
            'endpoint' => 'customers/'.$id,
        ));
    }
}
