<?php

abstract class HpsPayPlanPaymentMethodStatus extends HpsPayPlanCustomerStatus
{
    const INVALID     = 'Invalid';
    const REVOKED     = 'Revoked';
    const EXPIRED     = 'Expired';
    const LOST_STOLEN = 'Lost/Stolen';
}
