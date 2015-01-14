<?php

use Heartland\Entities\HpsAddress;
use Heartland\Entities\Check\HpsCheckHolder;
use Heartland\Entities\Check\HpsCheck;

class TestCheck {
    static public function approve(){
        $check = new HpsCheck();
        $check->accountNumber = '24413815';
        $check->routingNumber = '490000018';
        $check->checkType = 'PERSONAL';
        $check->secCode = 'PPD';
        $check->accountType = 'CHECKING';

        $check->checkHolder = new HpsCheckHolder();
        $check->checkHolder->address = new HpsAddress();
        $check->checkHolder->address->address = '6860 Dallas Parkway';
        $check->checkHolder->address->city = 'Plano';
        $check->checkHolder->address->state = 'TX';
        $check->checkHolder->address->zip = '75024';
        $check->checkHolder->dlNumber = '1234567';
        $check->checkHolder->dlState = 'TX';
        $check->checkHolder->firstName = 'John';
        $check->checkHolder->lastName = 'Doe';
        $check->checkHolder->phone = '1234567890';

        return $check;
    }

    static public function invalidCheckHolder(){
        $check = new HpsCheck();
        $check->accountNumber = '24413815';
        $check->routingNumber = '490000018';
        $check->checkType = 'PERSONAL';
        $check->secCode = 'PPD';
        $check->accountType = 'CHECKING';

        $check->checkHolder = new HpsCheckHolder();
        $check->checkHolder->address = new HpsAddress();
        $check->checkHolder->address->address = '6860 Dallas Parkway';
        $check->checkHolder->address->city = 'Plano';
        $check->checkHolder->address->state = 'TX';
        $check->checkHolder->address->zip = '75024';
        $check->checkHolder->dlNumber = '';
        $check->checkHolder->dlState = '';
        $check->checkHolder->firstName = 'John';
        $check->checkHolder->lastName = 'Doe';
        $check->checkHolder->phone = '1234567890';

        return $check;
    }

    static public function decline(){
        $check = new HpsCheck();
        $check->accountNumber = '24413815';
        $check->routingNumber = '490000034';
        $check->checkType = 'PERSONAL';
        $check->secCode = 'PPD';
        $check->accountType = 'CHECKING';

        $check->checkHolder = new HpsCheckHolder();
        $check->checkHolder->address = new HpsAddress();
        $check->checkHolder->address->address = '6860 Dallas Parkway';
        $check->checkHolder->address->city = 'Plano';
        $check->checkHolder->address->state = 'TX';
        $check->checkHolder->address->zip = '75024';
        $check->checkHolder->dlNumber = '1234567';
        $check->checkHolder->dlState = 'TX';
        $check->checkHolder->firstName = 'John';
        $check->checkHolder->lastName = 'Doe';
        $check->checkHolder->phone = '1234567890';

        return $check;
    }

    static public function certification(){
        $check = new HpsCheck();
        $check->accountNumber = '24413815';
        $check->routingNumber = '490000018';
        $check->checkType = 'PERSONAL';
        $check->secCode = 'PPD';
        $check->accountType = 'CHECKING';

        $check->checkHolder = new HpsCheckHolder();
        $check->checkHolder->address = new HpsAddress();
        $check->checkHolder->address->address = '123 Main St.';
        $check->checkHolder->address->city = 'Downtown';
        $check->checkHolder->address->state = 'NJ';
        $check->checkHolder->address->zip = '12345';
        $check->checkHolder->dlNumber = '09876543210';
        $check->checkHolder->dlState = 'TX';
        $check->checkHolder->firstName = 'John';
        $check->checkHolder->lastName = 'Doe';
        $check->checkHolder->phone = '8003214567';

        return $check;
    }
} 