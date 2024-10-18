<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class company_api_controller extends api_controller
{
    public function __construct()
    {
        parent::__construct();
        HostFact_API::setObjectNames("company", "company");
        require_once "class/company.php";
        $this->addParameter("CompanyName", "string");
        $this->addParameter("CompanyNumber", "string");
        $this->addParameter("LegalForm", "string");
        $this->addParameter("TaxNumber", "string");
        $this->addParameter("Address", "string");
        if(defined("IS_INTERNATIONAL") && IS_INTERNATIONAL === true) {
            $this->addParameter("Address2", "string");
        }
        $this->addParameter("ZipCode", "string");
        $this->addParameter("City", "string");
        if(defined("IS_INTERNATIONAL") && IS_INTERNATIONAL === true) {
            $this->addParameter("State", "string");
        }
        $this->addParameter("Country", "string");
        $this->addParameter("EmailAddress", "string");
        $this->addParameter("PhoneNumber", "string");
        $this->addParameter("MobileNumber", "string");
        $this->addParameter("FaxNumber", "string");
        $this->addParameter("Website", "string");
        $this->addParameter("AccountNumber", "string");
        $this->addParameter("AccountBIC", "string");
        $this->addParameter("AccountName", "string");
        $this->addParameter("AccountBank", "string");
        $this->addParameter("AccountCity", "string");
    }
    public function show_api_action()
    {
        $company = new company();
        if(!$company->show()) {
            HostFact_API::parseError($company->Error, true);
        }
        $result = [];
        foreach ($this->_object_parameters as $field => $field_info) {
            if(!isset($company->{$field})) {
            } else {
                $result[$field] = is_string($company->{$field}) ? htmlspecialchars_decode($company->{$field}) : $company->{$field};
            }
        }
        global $array_legaltype;
        global $array_country;
        $result["Translations"] = ["State" => isset($company->StateName) ? $company->StateName : "", "Country" => isset($array_country[$company->Country]) ? $array_country[$company->Country] : ""];
        if(!defined("IS_INTERNATIONAL") || IS_INTERNATIONAL !== true) {
            unset($result["Translations"]["State"]);
            unset($result["Translations"]["InvoiceState"]);
        }
        return HostFact_API::parseResponse($result);
    }
}

?>