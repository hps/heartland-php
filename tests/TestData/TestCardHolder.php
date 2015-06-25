<?php

class TestCardHolder
{
    public static function validCardHolder()
    {
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

    public static function certCardHolderShortZip()
    {
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

    public static function certCardHolderLongZip()
    {
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

    public static function certCardHolderLongZipStreet()
    {
        $address = new HpsAddress();
        $address->address = '6860';
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

    public static function certCardHolderLongZipNoStreet()
    {
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

    public static function certCardHolderShortZipNoStreet()
    {
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

    public static function certCardHolderShortZipStreet()
    {
        $address = new HpsAddress();
        $address->address = '6860';
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
