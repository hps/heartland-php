<?php

class HpsPayPlanCustomerService extends HpsRestGatewayService
{
    public function add(HpsPayPlanCustomer $customer)
    {
        $result = $this->doRequest('POST', 'customers', $customer->getEditableFieldsWithValues());
        return $this->hydrateObject($result, 'HpsPayPlanCustomer');
    }

    public function edit(HpsPayPlanCustomer $customer)
    {
        $data = $customer->getEditableFieldsWithValues();
        $result = $this->doRequest('PUT', 'customers/'.$customer->customerKey, $data);
        return $this->hydrateObject($result, 'HpsPayPlanCustomer');
    }

    public function findAll($searchFields = array())
    {
        // Cannot have an array as the root object
        // in a JSON document
        $data = $searchFields === array() ? (object)array() : $searchFields;
        $results = $this
            ->doRequest('POST', 'searchCustomers', $data);

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
        $result = $this->doRequest('GET', 'customers/'.$id);
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
        return $this->doRequest('DELETE', 'customers/'.$id, $data);
    }
}
