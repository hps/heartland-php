<?php

/**
 * Class HpsPayPlanService
 */
class HpsPayPlanService
{
    protected $customer      = null;
    protected $paymentMethod = null;
    protected $schedule      = null;
    /**
     * HpsPayPlanService constructor.
     *
     * @param \HpsServicesConfig|null $config
     */
    public function __construct(HpsServicesConfig $config = null)
    {
        $this->customer = new HpsPayPlanCustomerService($config);
        $this->paymentMethod = new HpsPayPlanPaymentMethodService($config);
        $this->schedule = new HpsPayPlanScheduleService($config);
    }
    /**
     * @param \HpsServicesConfig $config
     */
    public function setServicesConfig(HpsServicesConfig $config)
    {
        $this->customer->setServicesConfig($config);
        $this->paymentMethod->setServicesConfig($config);
        $this->schedule->setServicesConfig($config);
    }

    /// Customer methods

    /**
     * @param \HpsPayPlanCustomer $customer
     *
     * @return mixed
     */
    public function addCustomer(HpsPayPlanCustomer $customer)
    {
        return $this->customer->add($customer);
    }
    /**
     * @param \HpsPayPlanCustomer $customer
     *
     * @return mixed
     */
    public function editCustomer(HpsPayPlanCustomer $customer)
    {
        return $this->customer->edit($customer);
    }
    /**
     * @param array $searchFields
     *
     * @return object
     */
    public function findAllCustomers($searchFields = array())
    {
        return $this->customer->findAll($searchFields);
    }
    /**
     * @param $customer
     *
     * @return mixed
     */
    public function getCustomer($customer)
    {
        return $this->customer->get($customer);
    }
    /**
     * @param      $customer
     * @param bool $forceDelete
     *
     * @return mixed
     */
    public function deleteCustomer($customer, $forceDelete = false)
    {
        return $this->customer->delete($customer, $forceDelete);
    }

    /// PaymentMethod methods

    /**
     * @param \HpsPayPlanPaymentMethod $paymentMethod
     *
     * @return mixed
     */
    public function addPaymentMethod(HpsPayPlanPaymentMethod $paymentMethod)
    {
        return $this->paymentMethod->add($paymentMethod);
    }
    /**
     * @param \HpsPayPlanPaymentMethod $paymentMethod
     *
     * @return mixed
     */
    public function editPaymentMethod(HpsPayPlanPaymentMethod $paymentMethod)
    {
        return $this->paymentMethod->edit($paymentMethod);
    }
    /**
     * @param array $searchFields
     *
     * @return object
     */
    public function findAllPaymentMethods($searchFields = array())
    {
        return $this->paymentMethod->findAll($searchFields);
    }
    /**
     * @param $paymentMethod
     *
     * @return mixed
     */
    public function getPaymentMethod($paymentMethod)
    {
        return $this->paymentMethod->get($paymentMethod);
    }
    /**
     * @param      $paymentMethod
     * @param bool $forceDelete
     *
     * @return mixed
     */
    public function deletePaymentMethod($paymentMethod, $forceDelete = false)
    {
        return $this->paymentMethod->delete($paymentMethod, $forceDelete);
    }

    /// Schedule methods

    /**
     * @param \HpsPayPlanSchedule $schedule
     *
     * @return mixed
     */
    public function addSchedule(HpsPayPlanSchedule $schedule)
    {
        return $this->schedule->add($schedule);
    }
    /**
     * @param \HpsPayPlanSchedule $schedule
     *
     * @return mixed
     */
    public function editSchedule(HpsPayPlanSchedule $schedule)
    {
        return $this->schedule->edit($schedule);
    }
    /**
     * @param array $searchFields
     *
     * @return object
     */
    public function findAllSchedules($searchFields = array())
    {
        return $this->schedule->findAll($searchFields);
    }
    /**
     * @param $schedule
     *
     * @return mixed
     */
    public function getSchedule($schedule)
    {
        return $this->schedule->get($schedule);
    }
    /**
     * @param      $schedule
     * @param bool $forceDelete
     *
     * @return mixed
     */
    public function deleteSchedule($schedule, $forceDelete = false)
    {
        return $this->schedule->delete($schedule, $forceDelete);
    }
}
