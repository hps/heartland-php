<?php

/**
 * Class TestCardHolder
 */
class TestCardHolder
{
    /**
     * @return \HpsCardHolder
     */
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
    /**
     * @return \HpsCardHolder
     */
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
    /**
     * @return \HpsCardHolder
     */
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
    /**
     * @return \HpsCardHolder
     */
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
    /**
     * @return \HpsCardHolder
     */
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
    /**
     * @return \HpsCardHolder
     */
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
    /**
     * @return \HpsCardHolder
     */
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
    
    //Test data for sanitize user input
    /**
     * @return \HpsCardHolder
     */
    public static function certCardHolderLongFirstName()
    {
        $address = new HpsAddress();
        $address->address = "6860 Dallas Pkwy";
        $address->city = "Irvine";
        $address->state = "Tx";
        $address->zip = "75024";
        $address->country = "United States";

        $validCardHolder = new HpsCardHolder();
        $validCardHolder->firstName = "Bill Johnson Bill Johnson Bill Johnson Bill Johnson";
        $validCardHolder->lastName = "Johnson";
        $validCardHolder->address = $address;
        return $validCardHolder;
    }
    /**
     * @return \HpsCardHolder
     */
    public static function certCardHolderLongLastName()
    {
        $address = new HpsAddress();
        $address->address = "6860 Dallas Pkwy";
        $address->city = "Irvine";
        $address->state = "Tx";
        $address->zip = "75024";
        $address->country = "United States";

        $validCardHolder = new HpsCardHolder();
        $validCardHolder->firstName = "Bill";
        $validCardHolder->lastName = "Bill Johnson Bill Johnson Bill Johnson Bill Johnson";
        $validCardHolder->address = $address;
        return $validCardHolder;
    }
    /**
     * @return \HpsCardHolder
     */
    public static function certCardHolderLongCityName()
    {
        $address = new HpsAddress();
        $address->address = "6860 Dallas Pkwy";
        $address->city = "Irvine Irvine Irvine Irvine Irvine";
        $address->state = "Tx";
        $address->zip = "75024";
        $address->country = "United States";

        $validCardHolder = new HpsCardHolder();
        $validCardHolder->firstName = "Bill";
        $validCardHolder->lastName = "Johnson";
        $validCardHolder->address = $address;
        return $validCardHolder;
    }
    /**
     * @return \HpsCardHolder
     */
    public static function certCardHolderLongStateName()
    {
        $address = new HpsAddress();
        $address->address = "6860 Dallas Pkwy";
        $address->city = "Irvine";
        $address->state = "Tx Irvine Irvine Irvine Irvine";
        $address->zip = "75024";
        $address->country = "United States";

        $validCardHolder = new HpsCardHolder();
        $validCardHolder->firstName = "Bill";
        $validCardHolder->lastName = "Johnson";
        $validCardHolder->address = $address;
        return $validCardHolder;
    }
    /**
     * @return \HpsCardHolder
     */
    public static function certCardHolderLongEmailAddress()
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
        $validCardHolder->email = 'Loremipsumdonsecthisisadum.mysLoremipsum+donsectetur@textthisisadummytextLoremipsuconsecteturthisisadummy.com';
        
        return $validCardHolder;
    }
    /**
     * @return \HpsCardHolder
     */
    public static function certCardHolderInvalidEmailAddress()
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
        $validCardHolder->email = 'www.invalidmail.com';
        
        return $validCardHolder;
    }
    /**
     * @return \HpsCardHolder
     */
    public static function certCardHolderLongPhoneNumber()
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
        $validCardHolder->phone = '555-555-555-555-555-555-555-555-555555-555-555-555-555-555-555-555-555';
        
        return $validCardHolder;
    }
    /**
     * @return \HpsCardHolder
     */
    public static function certCardHolderLongZipCode()
    {
        $address = new HpsAddress();
        $address->address = "6860 Dallas Pkwy";
        $address->city = "Irvine";
        $address->state = "Tx";
        $address->zip = "CAD 123 CAD 123 CAD 123";
        $address->country = "United States";        

        $validCardHolder = new HpsCardHolder();
        $validCardHolder->firstName = "Bill";
        $validCardHolder->lastName = "Johnson";
        $validCardHolder->address = $address;
        
        return $validCardHolder;
    }
    /**
     * @return \HpsCardHolder
     */
    public static function certCardHolderCanadianZipCode()
    {
        $address = new HpsAddress();
        $address->address = "6860 Dallas Pkwy";
        $address->city = "Irvine";
        $address->state = "Tx";
        $address->zip = "CAD 123";
        $address->country = "United States";        

        $validCardHolder = new HpsCardHolder();
        $validCardHolder->firstName = "Bill";
        $validCardHolder->lastName = "Johnson";
        $validCardHolder->address = $address;
        
        return $validCardHolder;
    }
}
