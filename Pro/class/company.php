<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class company
{
    public $Identifier;
    public $CompanyName;
    public $CompanyNumber;
    public $TaxNumber;
    public $AccountNumber;
    public $AccountName;
    public $AccountBank;
    public $AccountCity;
    public $AccountBIC;
    public $Address;
    public $Address2;
    public $ZipCode;
    public $City;
    public $State;
    public $Country;
    public $PhoneNumber;
    public $FaxNumber;
    public $MobileNumber;
    public $EmailAddress;
    public $Website;
    public $Error = [];
    public $Warning = [];
    public $Success = [];
    public $Variables;
    public function __construct()
    {
        $this->Country = "NL";
        $this->StateName = "";
        $this->Variables = ["Identifier", "CompanyName", "CompanyNumber", "TaxNumber", "AccountNumber", "AccountName", "AccountBank", "AccountCity", "AccountBIC", "Address", "Address2", "ZipCode", "City", "State", "Country", "PhoneNumber", "FaxNumber", "MobileNumber", "EmailAddress", "Website"];
    }
    public function show()
    {
        $result = Database_Model::getInstance()->getOne("HostFact_Company")->execute();
        if(!$result || empty($result->id)) {
            $this->Error[] = __("company not found");
            return false;
        }
        foreach ($result as $key => $value) {
            $this->{$key} = htmlspecialchars($value);
        }
        global $array_country;
        global $array_states;
        $this->StateName = isset($array_states[$this->Country][$this->State]) ? $array_states[$this->Country][$this->State] : $this->State;
        $this->CountryLong = $array_country[$this->Country] ?? "";
        return true;
    }
    public function edit()
    {
        if(!$this->validate()) {
            return false;
        }
        if(isset($this->SynchronizeEmail)) {
            $this->syncCompanyEmailAddress($this->currentSender, $this->Sender, $this->SynchronizeEmail);
        }
        $result = Database_Model::getInstance()->update("HostFact_Company", ["CompanyName" => $this->CompanyName, "CompanyNumber" => $this->CompanyNumber, "TaxNumber" => $this->TaxNumber, "AccountNumber" => $this->AccountNumber, "AccountName" => $this->AccountName, "AccountBank" => $this->AccountBank, "AccountCity" => $this->AccountCity, "AccountBIC" => $this->AccountBIC, "Address" => $this->Address, "Address2" => $this->Address2, "ZipCode" => $this->ZipCode, "City" => $this->City, "State" => $this->State, "Country" => $this->Country, "PhoneNumber" => $this->PhoneNumber, "FaxNumber" => $this->FaxNumber, "MobileNumber" => $this->MobileNumber, "EmailAddress" => $this->EmailAddress, "Website" => $this->Website])->noWhere()->execute();
        if($result) {
            $this->Success[] = __("company adjusted");
            return true;
        }
        return false;
    }
    public function syncCompanyEmailAddress($currentSender, $Sender, $syncAction)
    {
        switch ($syncAction) {
            case "replace":
                Database_Model::getInstance()->update("HostFact_EmailTemplates", ["Sender" => $Sender])->where("Sender", $currentSender)->execute();
                break;
            case "all":
                Database_Model::getInstance()->update("HostFact_EmailTemplates", ["Sender" => $Sender])->noWhere()->execute();
                break;
            case "none":
        }
    }
    public function validate()
    {
        global $array_country;
        if(!array_key_exists($this->Country, $array_country) || is_numeric($this->Country)) {
            $this->Error[] = __("invalid country for company");
        }
        if($this->EmailAddress && !check_email_address($this->EmailAddress, "single")) {
            $this->Error[] = __("invalid company mailaddress given");
        }
        return empty($this->Error) ? true : false;
    }
}

?>