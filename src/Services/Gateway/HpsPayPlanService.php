<?php

class HpsPayPlanService
{
    protected $customer      = null;
    protected $paymentMethod = null;
    protected $schedule      = null;

    public function __construct(HpsServicesConfig $config = null)
    {
        $this->customer = new HpsPayPlanCustomerService($config);
        $this->paymentMethod = new HpsPayPlanPaymentMethodService($config);
        $this->schedule = new HpsPayPlanScheduleService($config);
    }

    public function setServicesConfig(HpsServicesConfig $config)
    {
        $this->customer->setServicesConfig($config);
        $this->paymentMethod->setServicesConfig($config);
        $this->schedule->setServicesConfig($config);
    }

    /// Customer methods

    public function addCustomer(HpsPayPlanCustomer $customer)
    {
        return $this->customer->add($customer);
    }

    public function editCustomer(HpsPayPlanCustomer $customer)
    {
        return $this->customer->edit($customer);
    }

    public function findAllCustomers($searchFields = array())
    {
        return $this->customer->findAll($searchFields);
    }

    public function getCustomer($customer)
    {
        return $this->customer->get($customer);
    }

    public function deleteCustomer($customer, $forceDelete = false)
    {
        return $this->customer->delete($customer, $forceDelete);
    }

    /// PaymentMethod methods

    public function addPaymentMethod(HpsPayPlanPaymentMethod $paymentMethod)
    {
        return $this->paymentMethod->add($paymentMethod);
    }

    public function editPaymentMethod(HpsPayPlanPaymentMethod $paymentMethod)
    {
        return $this->paymentMethod->edit($paymentMethod);
    }

    public function findAllPaymentMethods($searchFields = array())
    {
        return $this->paymentMethod->findAll($searchFields);
    }

    public function getPaymentMethod($paymentMethod)
    {
        return $this->paymentMethod->get($paymentMethod);
    }

    public function deletePaymentMethod($paymentMethod, $forceDelete = false)
    {
        return $this->paymentMethod->delete($paymentMethod, $forceDelete);
    }

    /// Schedule methods

    public function addSchedule(HpsPayPlanSchedule $schedule)
    {
        return $this->schedule->add($schedule);
    }

    public function editSchedule(HpsPayPlanSchedule $schedule)
    {
        return $this->schedule->edit($schedule);
    }

    public function findAllSchedules($searchFields = array())
    {
        return $this->schedule->findAll($searchFields);
    }

    public function getSchedule($schedule)
    {
        return $this->schedule->get($schedule);
    }

    public function deleteSchedule($schedule, $forceDelete = false)
    {
        return $this->schedule->delete($schedule, $forceDelete);
    }
}
