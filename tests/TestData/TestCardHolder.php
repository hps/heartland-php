<?php

class TestCardHolder
{

    static public function ValidCardHolder(){
        $address = new HpsAddress();
        $address->address = "One Heartland Way";
        $address->city = "Jeffersonville";
        $address->state = "IN";
        $address->zip = "47130";
        $address->country = "United States";

        $validCardHolder = new HpsCardHolder();
        $validCardHolder->firstName = "Bill";
        $validCardHolder->lastName = "Johnson";
        $validCardHolder->address = $address;
        return $validCardHolder;
    }

    static public function certCardHolderShortZip(){
        $address = new HpsAddress();
        $address->address = "6860 Dallas Pkwy";
        $address->city = "Irvine";
        $address->state = "Tx";
        $address->zip = "75024";
        $address->country = "United States";

        $validCardHolder = new HpsCardHolder();
        $validCardHolder->firstName = "Bill";
        $validCardHolder->lastName = "Johnson";
        $validCardHolder->address = $address;
        return $validCardHolder;
    }

    static public function certCardHolderLongZip(){
        $address = new HpsAddress();
        $address->address = "6860 Dallas Pkwy";
        $address->city = "Irvine";
        $address->state = "Tx";
        $address->zip = "750241234";
        $address->country = "United States";

        $validCardHolder = new HpsCardHolder();
        $validCardHolder->firstName = "Bill";
        $validCardHolder->lastName = "Johnson";
        $validCardHolder->address = $address;
        return $validCardHolder;
    }

    static public function certCardHolderLongZipNoStreet(){
        $address = new HpsAddress();
        $address->address = null;
        $address->city = "Irvine";
        $address->state = "Tx";
        $address->zip = "750241234";
        $address->country = "United States";

        $validCardHolder = new HpsCardHolder();
        $validCardHolder->firstName = "Bill";
        $validCardHolder->lastName = "Johnson";
        $validCardHolder->address = $address;
        return $validCardHolder;
    }

    static public function certCardHolderShortZipNoStreet(){
        $address = new HpsAddress();
        $address->address = null;
        $address->city = "Irvine";
        $address->state = "Tx";
        $address->zip = "75024";
        $address->country = "United States";

        $validCardHolder = new HpsCardHolder();
        $validCardHolder->firstName = "Bill";
        $validCardHolder->lastName = "Johnson";
        $validCardHolder->address = $address;
        return $validCardHolder;
    }

}
