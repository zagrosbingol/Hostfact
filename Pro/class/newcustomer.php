<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class newcustomer
{
    public $Identifier;
    public $Status;
    public $Username;
    public $Password;
    public $CompanyName;
    public $CompanyNumber;
    public $TaxNumber;
    public $LegalForm;
    public $Sex;
    public $Initials;
    public $SurName;
    public $Address;
    public $Address2;
    public $ZipCode;
    public $City;
    public $State;
    public $Country;
    public $BirthDate;
    public $EmailAddress;
    public $PhoneNumber;
    public $MobileNumber;
    public $FaxNumber;
    public $Comment;
    public $InvoiceMethod;
    public $InvoiceCompanyName;
    public $InvoiceSex;
    public $InvoiceInitials;
    public $InvoiceSurName;
    public $InvoiceAddress;
    public $InvoiceAddress2;
    public $InvoiceZipCode;
    public $InvoiceCity;
    public $InvoiceState;
    public $InvoiceCountry;
    public $InvoiceEmailAddress;
    public $InvoiceAuthorisation;
    public $InvoiceTemplate;
    public $PriceQuoteTemplate;
    public $AccountNumber;
    public $AccountBIC;
    public $AccountName;
    public $AccountBank;
    public $AccountCity;
    public $DefaultLanguage;
    public $Free1;
    public $Free2;
    public $Free3;
    public $Free4;
    public $Free5;
    public $CountRows;
    public $Table;
    public $Error;
    public $Succes;
    public $Variables = ["Identifier", "Status", "Username", "Password", "CompanyName", "CompanyNumber", "TaxNumber", "LegalForm", "Sex", "Initials", "SurName", "Address", "Address2", "ZipCode", "City", "State", "Country", "BirthDate", "EmailAddress", "PhoneNumber", "MobileNumber", "FaxNumber", "Comment", "InvoiceMethod", "InvoiceCompanyName", "InvoiceSex", "InvoiceInitials", "InvoiceSurName", "InvoiceAddress", "InvoiceAddress2", "InvoiceZipCode", "InvoiceCity", "InvoiceState", "InvoiceCountry", "InvoiceEmailAddress", "InvoiceAuthorisation", "InvoiceTemplate", "PriceQuoteTemplate", "AccountNumber", "AccountBIC", "AccountName", "AccountBank", "AccountCity", "DefaultLanguage", "Free1", "Free2", "Free3", "Free4", "Free5"];
    public function __construct()
    {
        $this->Country = "NL";
        $this->InvoiceAuthorisation = "no";
        $this->InvoiceTemplate = "";
        $this->PriceQuoteTemplate = "0";
        $this->InvoiceMethod = "0";
        $this->Status = "0";
        $this->Sex = "m";
        $this->LegalForm = "ANDERS";
        $this->Error = [];
        $this->Warning = [];
        $this->Success = [];
        $this->StateName = "";
        $this->InvoiceStateName = "";
        $this->DefaultLanguage = "";
        $this->customfields_list = [];
        if(!isset($_SESSION["custom_fields"]["newcustomer"]) || $_SESSION["custom_fields"]["newcustomer"]) {
            require_once "class/customfields.php";
            $customfields = new customfields();
            $this->customfields_list = $customfields->getCustomNewCustomerFields();
        }
    }
    public function __destruct()
    {
    }
    public function show()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for newcustomer");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_NewCustomers", ["*"])->where("id", $this->Identifier)->execute();
        if(!$result || empty($result->id)) {
            $this->Error[] = __("invalid identifier for newcustomer");
            return false;
        }
        foreach ($result as $key => $value) {
            if($key != "id") {
                $this->{$key} = htmlspecialchars($value);
            }
        }
        global $array_states;
        $this->BirthDate = rewrite_date_db2site($this->BirthDate);
        $this->StateName = isset($array_states[$this->Country][$this->State]) ? $array_states[$this->Country][$this->State] : $this->State;
        $this->InvoiceStateName = isset($array_states[$this->InvoiceCountry][$this->InvoiceState]) ? $array_states[$this->InvoiceCountry][$this->InvoiceState] : $this->InvoiceState;
        $this->Taxable = true;
        $this->TaxRate1 = $this->TaxRate2 = NULL;
        global $company;
        $debtor_country = $this->InvoiceCountry && $this->InvoiceAddress ? $this->InvoiceCountry : $this->Country;
        $result_rules = Database_Model::getInstance()->get("HostFact_Settings_TaxRules")->where("CountryCode", $debtor_country)->asArray()->execute();
        if($result_rules && is_array($result_rules)) {
            foreach ($result_rules as $v) {
                if(($v["StateCode"] == "all" || $v["StateCode"] == "same" && $this->State == $company->State || $v["StateCode"] == "other" && $this->State != $company->State) && ($v["Restriction"] == "all" || $v["Restriction"] == "company" && $this->CompanyName || $v["Restriction"] == "company_vat" && $this->CompanyName && $this->TaxNumber || $v["Restriction"] == "individual" && (!$this->CompanyName || !$this->TaxNumber))) {
                    $this->Taxable = is_null($v["TaxLevel1"]) ? true : false;
                    $this->TaxRate1 = $v["TaxLevel1"];
                    $this->TaxRate2 = $v["TaxLevel2"];
                }
            }
        }
        $result_eu_countries = Database_Model::getInstance()->get("HostFact_Settings_Countries")->where("EUCountry", "yes")->execute();
        $eu_countries = [];
        if($result_eu_countries && is_array($result_eu_countries)) {
            foreach ($result_eu_countries as $eu_country) {
                $eu_countries[] = $eu_country->CountryCode;
            }
        }
        $result_rules = Database_Model::getInstance()->get("HostFact_Settings_TaxRules")->orWhere([["CountryCode", "all"], ["CountryCode", "other"], ["CountryCode", "otherEU"], ["CountryCode", "nonEU"]])->asArray()->execute();
        if($result_rules && is_array($result_rules)) {
            foreach ($result_rules as $v) {
                if(($v["CountryCode"] == "all" || $v["CountryCode"] == "other" && $company->Country != $debtor_country || $v["CountryCode"] == "otherEU" && in_array($debtor_country, $eu_countries) && $company->Country != $debtor_country || $v["CountryCode"] == "nonEU" && !in_array($debtor_country, $eu_countries)) && ($v["StateCode"] == "all" || $v["StateCode"] == "same" && $this->State == $company->State || $v["StateCode"] == "other" && $this->State != $company->State) && ($v["Restriction"] == "all" || $v["Restriction"] == "company" && $this->CompanyName || $v["Restriction"] == "company_vat" && $this->CompanyName && $this->TaxNumber || $v["Restriction"] == "individual" && (!$this->CompanyName || !$this->TaxNumber))) {
                    $this->Taxable = false;
                    $this->TaxRate1 = $v["TaxLevel1"];
                    $this->TaxRate2 = $v["TaxLevel2"];
                }
            }
        }
        if(!empty($this->customfields_list)) {
            $customfields = new customfields();
            $custom_values = $customfields->getCustomNewCustomerFieldsValues($this->Identifier);
            if($custom_values && is_array($custom_values)) {
                $this->custom = new stdClass();
                $this->customvalues = [];
                foreach ($custom_values as $field_name => $custom_value) {
                    $this->custom->{$field_name} = $custom_value["ValueFormatted"];
                    $this->customvalues[$field_name] = $custom_value["Value"];
                }
            }
        }
        return true;
    }
    public function add()
    {
        $this->BirthDate = rewrite_date_site2db($this->BirthDate);
        if(!$this->validate()) {
            return false;
        }
        $result = Database_Model::getInstance()->insert("HostFact_NewCustomers", ["Status" => $this->Status, "Username" => $this->Username, "Password" => $this->Password, "CompanyName" => $this->CompanyName, "CompanyNumber" => $this->CompanyNumber, "TaxNumber" => $this->TaxNumber, "LegalForm" => $this->LegalForm, "Sex" => $this->Sex, "Initials" => $this->Initials, "SurName" => $this->SurName, "Address" => $this->Address, "Address2" => $this->Address2, "ZipCode" => $this->ZipCode, "City" => $this->City, "State" => $this->State, "Country" => $this->Country, "BirthDate" => $this->BirthDate, "EmailAddress" => check_email_address($this->EmailAddress, "convert"), "PhoneNumber" => $this->PhoneNumber, "MobileNumber" => $this->MobileNumber, "FaxNumber" => $this->FaxNumber, "Comment" => $this->Comment, "InvoiceMethod" => $this->InvoiceMethod, "InvoiceCompanyName" => $this->InvoiceCompanyName, "InvoiceSex" => $this->InvoiceSex, "InvoiceInitials" => $this->InvoiceInitials, "InvoiceSurName" => $this->InvoiceSurName, "InvoiceAddress" => $this->InvoiceAddress, "InvoiceAddress2" => $this->InvoiceAddress2, "InvoiceZipCode" => $this->InvoiceZipCode, "InvoiceCity" => $this->InvoiceCity, "InvoiceState" => $this->InvoiceState, "InvoiceCountry" => $this->InvoiceCountry, "InvoiceEmailAddress" => check_email_address($this->InvoiceEmailAddress, "convert"), "InvoiceAuthorisation" => $this->InvoiceAuthorisation, "InvoiceTemplate" => $this->InvoiceTemplate, "PriceQuoteTemplate" => $this->PriceQuoteTemplate, "AccountNumber" => $this->AccountNumber, "AccountBIC" => $this->AccountBIC, "AccountName" => $this->AccountName, "AccountBank" => $this->AccountBank, "AccountCity" => $this->AccountCity, "DefaultLanguage" => $this->DefaultLanguage])->execute();
        if($result) {
            $this->Identifier = $result;
            if(!empty($this->customfields_list)) {
                $customfields = new customfields();
                $customfields->setCustomNewCustomerFieldsValues($this->Identifier, $this->customvalues);
            }
            return true;
        }
        return false;
    }
    public function edit()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for newcustomer");
            return false;
        }
        $this->BirthDate = rewrite_date_site2db($this->BirthDate);
        if(!$this->validate()) {
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_NewCustomers", ["Status" => $this->Status, "Username" => $this->Username, "Password" => $this->Password, "CompanyName" => $this->CompanyName, "CompanyNumber" => $this->CompanyNumber, "TaxNumber" => $this->TaxNumber, "LegalForm" => $this->LegalForm, "Sex" => $this->Sex, "Initials" => $this->Initials, "SurName" => $this->SurName, "Address" => $this->Address, "Address2" => $this->Address2, "ZipCode" => $this->ZipCode, "City" => $this->City, "State" => $this->State, "Country" => $this->Country, "BirthDate" => $this->BirthDate, "EmailAddress" => check_email_address($this->EmailAddress, "convert"), "PhoneNumber" => $this->PhoneNumber, "MobileNumber" => $this->MobileNumber, "FaxNumber" => $this->FaxNumber, "Comment" => $this->Comment, "InvoiceMethod" => $this->InvoiceMethod, "InvoiceCompanyName" => $this->InvoiceCompanyName, "InvoiceSex" => $this->InvoiceSex, "InvoiceInitials" => $this->InvoiceInitials, "InvoiceSurName" => $this->InvoiceSurName, "InvoiceAddress" => $this->InvoiceAddress, "InvoiceAddress2" => $this->InvoiceAddress2, "InvoiceZipCode" => $this->InvoiceZipCode, "InvoiceCity" => $this->InvoiceCity, "InvoiceState" => $this->InvoiceState, "InvoiceCountry" => $this->InvoiceCountry, "InvoiceEmailAddress" => check_email_address($this->InvoiceEmailAddress, "convert"), "InvoiceAuthorisation" => $this->InvoiceAuthorisation, "InvoiceTemplate" => $this->InvoiceTemplate, "PriceQuoteTemplate" => $this->PriceQuoteTemplate, "AccountNumber" => $this->AccountNumber, "AccountBIC" => $this->AccountBIC, "AccountName" => $this->AccountName, "AccountBank" => $this->AccountBank, "AccountCity" => $this->AccountCity, "DefaultLanguage" => $this->DefaultLanguage])->where("id", $this->Identifier)->execute();
        if($result) {
            if(!empty($this->customfields_list)) {
                $customfields = new customfields();
                $customfields->setCustomNewCustomerFieldsValues($this->Identifier, $this->customvalues);
            }
            return true;
        }
        return false;
    }
    public function delete()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for newcustomer");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_NewCustomers", ["Status" => "9"])->where("id", $this->Identifier)->execute();
        if($result) {
            return true;
        }
        return false;
    }
    public function validate()
    {
        if(!is_numeric($this->Status)) {
            $this->Error[] = __("invalid status for newcustomer");
        }
        if(!(is_string($this->Username) && strlen($this->Username) <= 100 || strlen($this->Username) === 0)) {
            $this->Error[] = __("invalid username for newcustomer");
        }
        if(!(is_string($this->Password) && strlen($this->Password) <= 32 || strlen($this->Password) === 0)) {
            $this->Error[] = __("invalid password for newcustomer");
        }
        if(!trim($this->CompanyName) && !trim($this->SurName)) {
            $this->Error[] = __("no companyname and no surname are given");
        }
        if(!(check_email_address($this->EmailAddress) || strlen($this->EmailAddress) === 0)) {
            $this->Error[] = sprintf(__("invalid email"), $this->EmailAddress);
        }
        if(!(check_email_address($this->InvoiceEmailAddress) || strlen($this->InvoiceEmailAddress) === 0)) {
            $this->Error[] = sprintf(__("invalid invoicemail"), $this->InvoiceEmailAddress);
        }
        if(!(is_string($this->CompanyName) && strlen($this->CompanyName) <= 100 || strlen($this->CompanyName) === 0)) {
            $this->Error[] = __("invalid companyname");
        }
        if(!(is_string($this->CompanyNumber) && strlen($this->CompanyNumber) <= 20 || strlen($this->CompanyNumber) === 0)) {
            $this->Error[] = __("invalid companynumber");
        }
        if(!(is_string($this->TaxNumber) && strlen($this->TaxNumber) <= 20 || strlen($this->TaxNumber) === 0)) {
            $this->Error[] = __("invalid taxnumber");
        }
        if(!(is_string($this->Initials) && strlen($this->Initials) <= 25 || strlen($this->Initials) === 0)) {
            $this->Error[] = __("invalid initials");
        }
        if(!(is_string($this->SurName) && strlen($this->SurName) <= 100 || strlen($this->SurName) === 0)) {
            $this->Error[] = __("invalid surname");
        }
        if(!(is_string($this->Address) && strlen($this->Address) <= 100 || strlen($this->Address) === 0)) {
            $this->Error[] = __("invalid address");
        }
        if(!(is_string($this->Address2) && strlen($this->Address2) <= 100 || strlen($this->Address2) === 0)) {
            $this->Error[] = __("invalid address");
        }
        if(!(is_string($this->ZipCode) && strlen($this->ZipCode) <= 10 || strlen($this->ZipCode) === 0)) {
            $this->Error[] = __("invalid zipcode");
        }
        if(!(is_string($this->City) && strlen($this->City) <= 100 || strlen($this->City) === 0)) {
            $this->Error[] = __("invalid city");
        }
        if(!(is_string($this->State) && strlen($this->State) <= 100 || strlen($this->State) === 0)) {
            $this->Error[] = __("invalid state");
        }
        if(!(is_string($this->Country) && strlen($this->Country) <= 10 || strlen($this->Country) === 0)) {
            $this->Error[] = __("invalid country");
        }
        if(!(is_string($this->PhoneNumber) && strlen($this->PhoneNumber) <= 25 || strlen($this->PhoneNumber) === 0)) {
            $this->Error[] = __("invalid phonenumber");
        }
        if(!(is_string($this->MobileNumber) && strlen($this->MobileNumber) <= 25 || strlen($this->MobileNumber) === 0)) {
            $this->Error[] = __("invalid mobilenumber");
        }
        if(!(is_string($this->FaxNumber) && strlen($this->FaxNumber) <= 25 || strlen($this->FaxNumber) === 0)) {
            $this->Error[] = __("invalid faxnumber");
        }
        if(!(is_string($this->AccountNumber) && strlen($this->AccountNumber) <= 50 || strlen($this->AccountNumber) === 0)) {
            $this->Error[] = __("invalid accountnumber");
        }
        if(!(is_string($this->AccountBIC) && strlen($this->AccountBIC) <= 50 || strlen($this->AccountBIC) === 0)) {
            $this->Error[] = __("invalid accountbic");
        }
        if(!(is_string($this->AccountName) && strlen($this->AccountName) <= 100 || strlen($this->AccountName) === 0)) {
            $this->Error[] = __("invalid accountname");
        }
        if(!(is_string($this->AccountBank) && strlen($this->AccountBank) <= 100 || strlen($this->AccountBank) === 0)) {
            $this->Error[] = __("invalid accountbank");
        }
        if(!(is_string($this->AccountCity) && strlen($this->AccountCity) <= 100 || strlen($this->AccountCity) === 0)) {
            $this->Error[] = __("invalid accountcity");
        }
        if($this->InvoiceSex && !in_array($this->InvoiceSex, settings::GENDER_AVAILABLE_OPTIONS)) {
            $this->Error[] = __("invalid invoicesex");
        } elseif(!$this->InvoiceSex) {
            $this->InvoiceSex = "m";
        }
        if(!(is_string($this->InvoiceInitials) && strlen($this->InvoiceInitials) <= 25 || strlen($this->InvoiceInitials) === 0)) {
            $this->Error[] = __("invalid invoiceinitials");
        }
        if(!(is_string($this->InvoiceSurName) && strlen($this->InvoiceSurName) <= 100 || strlen($this->InvoiceSurName) === 0)) {
            $this->Error[] = __("invalid invoicesurname");
        }
        if(!(is_string($this->InvoiceAddress) && strlen($this->InvoiceAddress) <= 100 || strlen($this->InvoiceAddress) === 0)) {
            $this->Error[] = __("invalid invoiceaddress");
        }
        if(!(is_string($this->InvoiceAddress2) && strlen($this->InvoiceAddress2) <= 100 || strlen($this->InvoiceAddress2) === 0)) {
            $this->Error[] = __("invalid invoiceaddress");
        }
        if(!(is_string($this->InvoiceZipCode) && strlen($this->InvoiceZipCode) <= 10 || strlen($this->InvoiceZipCode) === 0)) {
            $this->Error[] = __("invalid invoicezipcode");
        }
        if(!(is_string($this->InvoiceCity) && strlen($this->InvoiceCity) <= 100 || strlen($this->InvoiceCity) === 0)) {
            $this->Error[] = __("invalid invoicecity");
        }
        if(!(is_string($this->InvoiceState) && strlen($this->InvoiceState) <= 100 || strlen($this->InvoiceState) === 0)) {
            $this->Error[] = __("invalid invoicestate");
        }
        if(!(is_string($this->InvoiceCountry) && strlen($this->InvoiceCountry) <= 10 || strlen($this->InvoiceCountry) === 0)) {
            $this->Error[] = __("invalid invoicecountry");
        }
        if(($this->InvoiceMethod == "0" || $this->InvoiceMethod == "3") && !$this->EmailAddress && !$this->InvoiceEmailAddress) {
            global $array_invoicemethod;
            $this->Error[] = sprintf(__("invoicemethod via mail not possible without mailaddress for newcustomer"), $array_invoicemethod[$this->InvoiceMethod]);
        }
        if(!empty($this->customfields_list)) {
            foreach ($this->customfields_list as $custom_field) {
                switch ($custom_field["LabelType"]) {
                    case "date":
                        $this->customvalues[$custom_field["FieldCode"]] = $this->customvalues[$custom_field["FieldCode"]] ? date("Y-m-d", strtotime(rewrite_date_site2db($this->customvalues[$custom_field["FieldCode"]]))) : "";
                        break;
                    case "checkbox":
                        $this->customvalues[$custom_field["FieldCode"]] = json_encode($this->customvalues[$custom_field["FieldCode"]]);
                        break;
                }
            }
        }
        return empty($this->Error) ? true : false;
    }
}

?>