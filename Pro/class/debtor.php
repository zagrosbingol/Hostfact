<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
if(!class_exists("SoapFault", false)) {
    class SoapFault
    {
    }
}
class debtor
{
    public $Identifier;
    public $DebtorCode;
    public $Status;
    public $ActiveLogin;
    public $Username;
    public $Password;
    public $OneTimePasswordValidTill;
    public $TwoFactorAuthentication = "off";
    public $TokenData;
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
    public $Website;
    public $PhoneNumber;
    public $MobileNumber;
    public $FaxNumber;
    public $Comment;
    public $PeriodicInvoiceDays;
    public $InvoiceMethod;
    public $InvoiceTerm;
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
    public $InvoiceDataForPriceQuote;
    public $InvoiceAuthorisation;
    public $InvoiceTemplate;
    public $PriceQuoteTemplate;
    public $ReminderTemplate;
    public $ReminderEmailAddress;
    public $SecondReminderTemplate;
    public $SummationTemplate;
    public $PaymentMail;
    public $PaymentMailTemplate;
    public $InvoiceCollect;
    public $DefaultLanguage;
    public $ClientareaProfile;
    public $Server;
    public $DNS1;
    public $DNS2;
    public $DNS3;
    public $AccountNumber;
    public $AccountBIC;
    public $AccountName;
    public $AccountBank;
    public $AccountCity;
    public $Mailing;
    public $Taxable;
    public $InvoiceEmailAttachments;
    public $Groups;
    public $Attachment;
    public $CountRows;
    public $Error;
    public $Warning;
    public $Success;
    public $Variables = ["Identifier", "DebtorCode", "Status", "ActiveLogin", "Username", "Password", "SecurePassword", "TwoFactorAuthentication", "TokenData", "CompanyName", "CompanyNumber", "TaxNumber", "LegalForm", "Sex", "Initials", "SurName", "Address", "Address2", "ZipCode", "City", "State", "Country", "BirthDate", "EmailAddress", "Website", "PhoneNumber", "MobileNumber", "FaxNumber", "Comment", "PeriodicInvoiceDays", "InvoiceMethod", "InvoiceTerm", "InvoiceCompanyName", "InvoiceSex", "InvoiceInitials", "InvoiceSurName", "InvoiceAddress", "InvoiceAddress2", "InvoiceZipCode", "InvoiceCity", "InvoiceState", "InvoiceCountry", "InvoiceEmailAddress", "InvoiceDataForPriceQuote", "InvoiceAuthorisation", "InvoiceTemplate", "PriceQuoteTemplate", "ReminderEmailAddress", "ReminderTemplate", "SecondReminderTemplate", "SummationTemplate", "Server", "AccountNumber", "AccountBIC", "AccountName", "AccountBank", "AccountCity", "PaymentMail", "PaymentMailTemplate", "InvoiceCollect", "DefaultLanguage", "Mailing", "Taxable", "DNS1", "DNS2", "DNS3", "MandateID", "MandateDate", "MandateType", "InvoiceEmailAttachments", "ClientareaProfile"];
    public $MandateID;
    public $MandateDate;
    public $MandateType;
    public function __construct()
    {
        global $company;
        $this->ActiveLogin = "yes";
        $this->PeriodicInvoiceDays = "-1";
        $this->Sex = "m";
        $this->InvoiceAuthorisation = "no";
        $this->InvoiceMethod = STANDARD_INVOICEMETHOD;
        $this->Country = isset($company->Country) && $company->Country ? $company->Country : "NL";
        $this->InvoiceCountry = $this->Country;
        $this->Status = "0";
        $this->Server = 0;
        $this->Groups = [];
        $this->SentWelcome = ORDER_ACCEPT_WELCOME_MAIL == 1 && isset($_GET["page"]) && $_GET["page"] == "add" ? "yes" : "no";
        $this->SynchronizeEmail = "no";
        $this->SynchronizeAuth = "no";
        $this->SynchronizeAddress = "no";
        $this->SynchronizeHandles = "no";
        $this->PaymentMail = -1;
        $this->PaymentMailTemplate = 0;
        $this->SecondReminderTemplate = -1;
        $this->InvoiceCollect = -1;
        $this->StateName = "";
        $this->InvoiceStateName = "";
        $this->Mailing = "yes";
        $this->Taxable = "auto";
        $this->InvoiceEmailAttachments = "";
        $this->InvoiceDataForPriceQuote = "no";
        $this->Attachment = [];
        $this->Error = [];
        $this->Warning = [];
        $this->Success = [];
        $this->MandateID = "";
        $this->customfields_list = [];
        if(322 <= str_replace(".", "", SOFTWARE_VERSION) && (!isset($_SESSION["custom_fields"]["debtor"]) || $_SESSION["custom_fields"]["debtor"])) {
            require_once "class/customfields.php";
            $customfields = new customfields();
            $this->customfields_list = $customfields->getCustomDebtorFields();
        }
    }
    public function __destruct()
    {
    }
    public function show($id = NULL, $extrainfo = false)
    {
        $this->Identifier = 0 < $id ? $id : $this->Identifier;
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for debtor");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_Debtors")->where("id", $this->Identifier)->execute();
        if(!$result || empty($result->id)) {
            $this->Error[] = __("invalid identifier for debtor");
            return false;
        }
        foreach ($result as $key => $value) {
            $this->{$key} = htmlspecialchars($value);
        }
        global $array_states;
        $this->CountryLong = countryCodeToLong($this->Country);
        $this->InvoiceCountryLong = countryCodeToLong($this->InvoiceCountry);
        $this->OldPassword = htmlspecialchars_decode($this->Password);
        $this->PeriodicInvoiceDaysOld = $this->PeriodicInvoiceDays;
        $this->BirthDate = rewrite_date_db2site($this->BirthDate);
        $this->StateName = isset($array_states[$this->Country][$this->State]) ? $array_states[$this->Country][$this->State] : $this->State;
        $this->InvoiceStateName = isset($array_states[$this->InvoiceCountry][$this->InvoiceState]) ? $array_states[$this->InvoiceCountry][$this->InvoiceState] : $this->InvoiceState;
        $this->Groups = [];
        $group_list = Database_Model::getInstance()->get(["HostFact_Group", "HostFact_GroupRelations"], ["HostFact_Group.id", "HostFact_Group.GroupName", "HostFact_Group.Status"])->where("HostFact_GroupRelations.Reference", $this->Identifier)->where("HostFact_GroupRelations.Type", "debtor")->where("HostFact_GroupRelations.Group = HostFact_Group.id")->where("HostFact_Group.Type", "debtor")->where("HostFact_Group.Status", ["!=" => 9])->execute();
        if($group_list && is_array($group_list)) {
            foreach ($group_list as $result_group) {
                $this->Groups[$result_group->id] = ["id" => $result_group->id, "GroupName" => htmlspecialchars($result_group->GroupName)];
            }
        }
        $this->TaxableSetting = $this->Taxable;
        $this->TaxRate1 = $this->TaxRate2 = NULL;
        global $company;
        $debtor_country = $this->InvoiceCountry && $this->InvoiceAddress ? $this->InvoiceCountry : $this->Country;
        if($this->Taxable == "auto" || $this->Taxable == "") {
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
        } elseif($this->Taxable == "yes") {
            $this->Taxable = true;
        } elseif($this->Taxable == "no") {
            $this->Taxable = false;
        }
        if($this->InvoiceTerm == -1) {
            $this->InvoiceTerm = NULL;
        }
        $this->EmailAddress = trim($this->EmailAddress);
        $this->InvoiceEmailAddress = trim($this->InvoiceEmailAddress);
        $this->ReminderEmailAddress = trim($this->ReminderEmailAddress);
        if(SDD_ID) {
            $this->getDirectDebitMandate();
        }
        if(!empty($this->customfields_list)) {
            $customfields = new customfields();
            $custom_values = $customfields->getCustomDebtorFieldsValues($this->Identifier);
            if($custom_values && is_array($custom_values)) {
                $this->custom = new stdClass();
                $this->customvalues = [];
                foreach ($custom_values as $field_name => $custom_value) {
                    $this->custom->{$field_name} = $custom_value["ValueFormatted"];
                    $this->customvalues[$field_name] = $custom_value["Value"];
                }
            }
        }
        if(!empty($this->Password) && $result->OneTimePasswordValidTill < date("Y-m-d H:i:s")) {
            $this->Password = "";
        }
        return true;
    }
    public function get_id()
    {
        if(strlen($this->Username) === 0 || strlen($this->Password) === 0) {
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_Debtors", ["id", "Password", "OneTimePasswordValidTill", "SecurePassword"])->where("Username", $this->Username)->where("Status", ["!=" => 9])->execute();
        if($result && 0 <= $result->id) {
            if($result->SecurePassword && wf_password_verify($this->Password, $result->SecurePassword)) {
                return $result->id;
            }
            if($result->Password && $result->Password == passcrypt($this->Password) && date("Y-m-d H:i:s") < $result->OneTimePasswordValidTill) {
                return $result->id;
            }
        }
        return false;
    }
    public function getDebtorCode($debtor_id)
    {
        $debtor_code = Database_Model::getInstance()->getOne("HostFact_Debtors", "DebtorCode")->where("id", $debtor_id)->execute();
        return $debtor_code !== false ? $debtor_code->DebtorCode : false;
    }
    public function getID($method, $value)
    {
        switch ($method) {
            case "identifier":
                $debtor_id = Database_Model::getInstance()->getOne("HostFact_Debtors", "id")->where("id", intval($value))->execute();
                return $debtor_id !== false ? $debtor_id->id : false;
                break;
            case "debtorcode":
                $debtor_id = Database_Model::getInstance()->getOne("HostFact_Debtors", "id")->where("DebtorCode", $value)->execute();
                return $debtor_id !== false ? $debtor_id->id : false;
                break;
            case "username-email":
                $debtor_id = Database_Model::getInstance()->getOne("HostFact_Debtors", ["id", "Emailaddress"])->where("Username", $value["Username"])->where("Status", ["!=" => 9])->execute();
                if(!$debtor_id) {
                    return false;
                }
                return in_array(strtolower($value["EmailAddress"]), explode(";", strtolower($debtor_id->Emailaddress))) ? $debtor_id->id : false;
                break;
        }
    }
    public function _checkGroup($DebtorGroupId)
    {
        if(!is_numeric($DebtorGroupId)) {
            return false;
        }
        $DebtorGroup_id = Database_Model::getInstance()->getOne("HostFact_Group", "id")->where("Type", "debtor")->where("id", $DebtorGroupId)->execute();
        return $DebtorGroup_id !== false && 0 < $DebtorGroup_id->id ? $DebtorGroup_id->id : false;
    }
    public function add()
    {
        $this->BirthDate = rewrite_date_site2db($this->BirthDate);
        if(!$this->__checkClientLimit() || !$this->validate()) {
            return false;
        }
        if($this->InvoiceTerm == "" || $this->InvoiceTerm === NULL) {
            $this->InvoiceTerm = -1;
        }
        if(trim($this->Password) != $this->Password || rtrim($this->Password) != $this->Password) {
            $this->Warning[] = __("debtor password isnt coded correctly");
        }
        $this->__prefixTaxnumber();
        $result = Database_Model::getInstance()->insert("HostFact_Debtors", ["DebtorCode" => $this->DebtorCode, "Status" => $this->Status, "ActiveLogin" => $this->ActiveLogin, "Username" => $this->Username, "Password" => $this->Password, "OneTimePasswordValidTill" => !empty($this->OneTimePasswordValidTill) ? $this->OneTimePasswordValidTill : ["RAW" => "NULL"], "TwoFactorAuthentication" => $this->TwoFactorAuthentication, "TokenData" => $this->TokenData, "CompanyName" => $this->CompanyName, "CompanyNumber" => $this->CompanyNumber, "TaxNumber" => $this->TaxNumber, "LegalForm" => $this->LegalForm, "Sex" => $this->Sex, "Initials" => $this->Initials, "SurName" => $this->SurName, "Address" => $this->Address, "Address2" => $this->Address2, "ZipCode" => $this->ZipCode, "City" => $this->City, "State" => $this->State, "Country" => $this->Country, "BirthDate" => $this->BirthDate, "EmailAddress" => check_email_address($this->EmailAddress, "convert"), "PhoneNumber" => $this->PhoneNumber, "MobileNumber" => $this->MobileNumber, "FaxNumber" => $this->FaxNumber, "Comment" => $this->Comment, "PeriodicInvoiceDays" => $this->PeriodicInvoiceDays, "InvoiceMethod" => $this->InvoiceMethod, "InvoiceTerm" => $this->InvoiceTerm, "InvoiceCompanyName" => $this->InvoiceCompanyName, "InvoiceSex" => $this->InvoiceSex, "InvoiceInitials" => $this->InvoiceInitials, "InvoiceSurName" => $this->InvoiceSurName, "InvoiceAddress" => $this->InvoiceAddress, "InvoiceAddress2" => $this->InvoiceAddress2, "InvoiceZipCode" => $this->InvoiceZipCode, "InvoiceCity" => $this->InvoiceCity, "InvoiceState" => $this->InvoiceState, "InvoiceCountry" => $this->InvoiceCountry, "InvoiceEmailAddress" => check_email_address($this->InvoiceEmailAddress, "convert"), "InvoiceAuthorisation" => $this->InvoiceAuthorisation, "InvoiceTemplate" => $this->InvoiceTemplate, "PriceQuoteTemplate" => $this->PriceQuoteTemplate, "ReminderTemplate" => $this->ReminderTemplate, "ReminderEmailAddress" => check_email_address($this->ReminderEmailAddress, "convert"), "SecondReminderTemplate" => $this->SecondReminderTemplate, "SummationTemplate" => $this->SummationTemplate, "Server" => $this->Server, "AccountNumber" => $this->AccountNumber, "AccountBIC" => $this->AccountBIC, "AccountName" => $this->AccountName, "AccountBank" => $this->AccountBank, "AccountCity" => $this->AccountCity, "PaymentMail" => $this->PaymentMail, "PaymentMailTemplate" => $this->PaymentMailTemplate, "InvoiceCollect" => $this->InvoiceCollect, "Website" => $this->Website, "DefaultLanguage" => $this->DefaultLanguage, "Mailing" => $this->Mailing, "Taxable" => $this->Taxable, "InvoiceEmailAttachments" => $this->InvoiceEmailAttachments, "DNS1" => $this->DNS1, "DNS2" => $this->DNS2, "DNS3" => $this->DNS3, "ClientareaProfile" => $this->ClientareaProfile, "InvoiceDataForPriceQuote" => $this->InvoiceDataForPriceQuote])->execute();
        if($result) {
            $this->Identifier = $result;
            foreach ($this->Groups as $key => $value) {
                $input = is_numeric($value) ? $value : $key;
                Database_Model::getInstance()->insert("HostFact_GroupRelations", ["Group" => $input, "Type" => "debtor", "Reference" => $this->Identifier])->execute();
            }
            if($this->InvoiceAuthorisation == "yes" && SDD_ID) {
                $this->createDirectDebitMandate();
            }
            if(!empty($this->customfields_list)) {
                $customfields = new customfields();
                $customfields->setCustomDebtorFieldsValues($this->Identifier, $this->customvalues);
            }
            if(isset($this->WelcomeMail) && 0 < $this->WelcomeMail) {
                $this->sentWelcome($this->WelcomeMail);
            }
            $this->Success[] = sprintf(__("debtor is created"), $this->DebtorCode);
            if(isset($_SESSION["active_clients"])) {
                unset($_SESSION["active_clients"]);
            }
            $debtor_info = ["id" => $this->Identifier, "DebtorCode" => $this->DebtorCode];
            do_action("debtor_is_created", $debtor_info);
            return true;
        } else {
            return false;
        }
    }
    public function edit($id = NULL)
    {
        $this->Identifier = 0 < $id ? $id : $this->Identifier;
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for debtor");
            return false;
        }
        $this->BirthDate = rewrite_date_site2db($this->BirthDate);
        if(!$this->validate()) {
            return false;
        }
        if($this->InvoiceTerm == "" || $this->InvoiceTerm === NULL) {
            $this->InvoiceTerm = -1;
        }
        if(!$this->Password) {
            $this->Password = $this->OldPassword;
        } elseif(trim($this->Password) != $this->Password || rtrim($this->Password) != $this->Password) {
            $this->Warning[] = __("debtor password isnt coded correctly");
        }
        if(!empty($this->Password) && $this->Password != $this->OldPassword) {
            $this->OneTimePasswordValidTill = static::setOneTimePasswordValidTill();
        }
        $this->__prefixTaxnumber();
        $result = Database_Model::getInstance()->update("HostFact_Debtors", ["DebtorCode" => $this->DebtorCode, "Status" => $this->Status, "ActiveLogin" => $this->ActiveLogin, "Username" => $this->Username, "Password" => $this->Password, "OneTimePasswordValidTill" => !empty($this->OneTimePasswordValidTill) ? $this->OneTimePasswordValidTill : ["RAW" => "NULL"], "SecurePassword" => $this->SecurePassword, "TwoFactorAuthentication" => $this->TwoFactorAuthentication, "TokenData" => $this->TokenData, "CompanyName" => $this->CompanyName, "CompanyNumber" => $this->CompanyNumber, "TaxNumber" => $this->TaxNumber, "LegalForm" => $this->LegalForm, "Sex" => $this->Sex, "Initials" => $this->Initials, "SurName" => $this->SurName, "Address" => $this->Address, "Address2" => $this->Address2, "ZipCode" => $this->ZipCode, "City" => $this->City, "State" => $this->State, "Country" => $this->Country, "BirthDate" => $this->BirthDate, "EmailAddress" => check_email_address($this->EmailAddress, "convert"), "PhoneNumber" => $this->PhoneNumber, "MobileNumber" => $this->MobileNumber, "FaxNumber" => $this->FaxNumber, "Comment" => $this->Comment, "PeriodicInvoiceDays" => $this->PeriodicInvoiceDays, "InvoiceMethod" => $this->InvoiceMethod, "InvoiceTerm" => $this->InvoiceTerm, "InvoiceCompanyName" => $this->InvoiceCompanyName, "InvoiceSex" => $this->InvoiceSex, "InvoiceInitials" => $this->InvoiceInitials, "InvoiceSurName" => $this->InvoiceSurName, "InvoiceAddress" => $this->InvoiceAddress, "InvoiceAddress2" => $this->InvoiceAddress2, "InvoiceZipCode" => $this->InvoiceZipCode, "InvoiceCity" => $this->InvoiceCity, "InvoiceState" => $this->InvoiceState, "InvoiceCountry" => $this->InvoiceCountry, "InvoiceEmailAddress" => check_email_address($this->InvoiceEmailAddress, "convert"), "InvoiceAuthorisation" => $this->InvoiceAuthorisation, "InvoiceTemplate" => $this->InvoiceTemplate, "PriceQuoteTemplate" => $this->PriceQuoteTemplate, "ReminderTemplate" => $this->ReminderTemplate, "ReminderEmailAddress" => check_email_address($this->ReminderEmailAddress, "convert"), "SecondReminderTemplate" => $this->SecondReminderTemplate, "SummationTemplate" => $this->SummationTemplate, "Server" => $this->Server, "AccountNumber" => $this->AccountNumber, "AccountBIC" => $this->AccountBIC, "AccountName" => $this->AccountName, "AccountBank" => $this->AccountBank, "AccountCity" => $this->AccountCity, "PaymentMail" => $this->PaymentMail, "PaymentMailTemplate" => $this->PaymentMailTemplate, "InvoiceCollect" => $this->InvoiceCollect, "Website" => $this->Website, "DefaultLanguage" => $this->DefaultLanguage, "Mailing" => $this->Mailing, "Taxable" => $this->Taxable, "InvoiceEmailAttachments" => $this->InvoiceEmailAttachments, "DNS1" => $this->DNS1, "DNS2" => $this->DNS2, "DNS3" => $this->DNS3, "ClientareaProfile" => $this->ClientareaProfile, "InvoiceDataForPriceQuote" => $this->InvoiceDataForPriceQuote])->where("id", $this->Identifier)->execute();
        if(!$result) {
            return false;
        }
        if($this->PeriodicInvoiceDaysOld != $this->PeriodicInvoiceDays) {
            Database_Model::getInstance()->update("HostFact_PeriodicElements", ["ReminderDate" => ["RAW" => "DATE_ADD(`StartPeriod`, INTERVAL -:day_diff DAY)"]])->bindValue("day_diff", $this->PeriodicInvoiceDays != -1 ? $this->PeriodicInvoiceDays : PERIODIC_INVOICE_DAYS)->where("`NextDate` = `ReminderDate`")->where("DATEDIFF(`StartPeriod`,`NextDate`)", $this->PeriodicInvoiceDaysOld != -1 ? $this->PeriodicInvoiceDaysOld : PERIODIC_INVOICE_DAYS)->where("Debtor", $this->Identifier)->execute();
            Database_Model::getInstance()->update("HostFact_PeriodicElements", ["NextDate" => ["RAW" => "DATE_ADD(`StartPeriod`, INTERVAL -:day_diff DAY)"]])->bindValue("day_diff", $this->PeriodicInvoiceDays != -1 ? $this->PeriodicInvoiceDays : PERIODIC_INVOICE_DAYS)->where("DATEDIFF(`StartPeriod`,`NextDate`)", $this->PeriodicInvoiceDaysOld != -1 ? $this->PeriodicInvoiceDaysOld : PERIODIC_INVOICE_DAYS)->where("Debtor", $this->Identifier)->execute();
        }
        Database_Model::getInstance()->delete("HostFact_GroupRelations")->where("Type", "debtor")->where("Reference", $this->Identifier)->execute();
        foreach ($this->Groups as $key => $value) {
            $input = is_numeric($value) ? $value : $key;
            Database_Model::getInstance()->insert("HostFact_GroupRelations", ["Group" => $input, "Type" => "debtor", "Reference" => $this->Identifier])->execute();
        }
        if($this->InvoiceAuthorisation == "yes" && SDD_ID) {
            if(!$this->getDirectDebitMandate(false)) {
                $this->createDirectDebitMandate();
            } else {
                $this->saveDirectDebitMandate();
            }
        } else {
            $this->closeDirectDebitMandate();
        }
        if(isset($this->SynchronizeEmail) && $this->SynchronizeEmail == "yes") {
            $email = check_email_address($this->InvoiceEmailAddress && check_email_address($this->InvoiceEmailAddress) ? $this->InvoiceEmailAddress : $this->EmailAddress, "convert");
            Database_Model::getInstance()->update("HostFact_Invoice", ["EmailAddress" => $email])->where("Debtor", $this->Identifier)->where("Status", ["<" => 4])->execute();
            Database_Model::getInstance()->update("HostFact_PriceQuote", ["EmailAddress" => $email])->where("Debtor", $this->Identifier)->where("Status", ["<" => 3])->execute();
        }
        if(isset($this->SynchronizeAuth) && $this->SynchronizeAuth == "yes") {
            if($this->InvoiceAuthorisation == "yes") {
                Database_Model::getInstance()->update("HostFact_Invoice", ["Authorisation" => "yes", "PaymentMethod" => "", "TransactionID" => ""])->where("Debtor", $this->Identifier)->where("Status", ["<" => 4])->execute();
                if(SDD_ID) {
                    require_once "class/directdebit.php";
                    $directdebit = new directdebit();
                    $directdebit->cronDirectDebit();
                }
            } else {
                if(SDD_ID) {
                    require_once "class/directdebit.php";
                    $result = Database_Model::getInstance()->get("HostFact_Invoice", ["id", "SDDBatchID"])->where("Authorisation", "yes")->where("SDDBatchID", ["!=" => ""])->where("Debtor", $this->Identifier)->where("Status", ["<" => 4])->execute();
                    if($result && is_array($result)) {
                        foreach ($result as $var) {
                            $directdebit = new directdebit();
                            $directdebit->removeDirectDebitFromInvoiceByBatchAndInvoiceID($var->SDDBatchID, $var->id);
                        }
                    }
                }
                Database_Model::getInstance()->update("HostFact_Invoice", ["Authorisation" => "no", "PaymentMethod" => "", "TransactionID" => "", "SDDBatchID" => ""])->where("Debtor", $this->Identifier)->where("Status", ["<" => 4])->execute();
            }
        }
        if(isset($this->SynchronizeAddress) && $this->SynchronizeAddress == "yes") {
            if($this->InvoiceCompanyName == "") {
                $this->InvoiceCompanyName = $this->CompanyName;
            }
            if($this->InvoiceInitials == "") {
                $this->InvoiceInitials = $this->Initials;
            }
            if($this->InvoiceSurName == "") {
                $this->InvoiceSurName = $this->SurName;
            }
            if($this->InvoiceAddress == "") {
                $this->InvoiceAddress = $this->Address;
            }
            if($this->InvoiceAddress2 == "") {
                $this->InvoiceAddress2 = $this->Address2;
            }
            if($this->InvoiceZipCode == "") {
                $this->InvoiceZipCode = $this->ZipCode;
            }
            if($this->InvoiceCity == "") {
                $this->InvoiceCity = $this->City;
            }
            if($this->InvoiceState == "") {
                $this->InvoiceState = $this->State;
            }
            if($this->InvoiceEmailAddress == "") {
                $this->InvoiceEmailAddress = $this->EmailAddress;
            }
            Database_Model::getInstance()->update("HostFact_Invoice", ["CompanyName" => $this->InvoiceCompanyName, "TaxNumber" => $this->TaxNumber, "Sex" => $this->InvoiceSex, "Initials" => $this->InvoiceInitials, "SurName" => $this->InvoiceSurName, "Address" => $this->InvoiceAddress, "Address2" => $this->InvoiceAddress2, "ZipCode" => $this->InvoiceZipCode, "City" => $this->InvoiceCity, "State" => $this->InvoiceState, "Country" => $this->InvoiceCountry, "EmailAddress" => check_email_address($this->InvoiceEmailAddress, "convert")])->where("Debtor", $this->Identifier)->where("Status", ["<" => 4])->execute();
            Database_Model::getInstance()->update("HostFact_PriceQuote", ["CompanyName" => $this->InvoiceDataForPriceQuote == "yes" ? $this->InvoiceCompanyName : $this->CompanyName, "Sex" => $this->InvoiceDataForPriceQuote == "yes" ? $this->InvoiceSex : $this->Sex, "Initials" => $this->InvoiceDataForPriceQuote == "yes" ? $this->InvoiceInitials : $this->Initials, "SurName" => $this->InvoiceDataForPriceQuote == "yes" ? $this->InvoiceSurName : $this->SurName, "Address" => $this->InvoiceDataForPriceQuote == "yes" ? $this->InvoiceAddress : $this->Address, "Address2" => $this->InvoiceDataForPriceQuote == "yes" ? $this->InvoiceAddress2 : $this->Address2, "ZipCode" => $this->InvoiceDataForPriceQuote == "yes" ? $this->InvoiceZipCode : $this->ZipCode, "City" => $this->InvoiceDataForPriceQuote == "yes" ? $this->InvoiceCity : $this->City, "State" => $this->InvoiceDataForPriceQuote == "yes" ? $this->InvoiceState : $this->State, "Country" => $this->InvoiceDataForPriceQuote == "yes" ? $this->InvoiceCountry : $this->Country, "EmailAddress" => check_email_address($this->InvoiceDataForPriceQuote == "yes" ? $this->InvoiceEmailAddress : $this->EmailAddress, "convert")])->where("Debtor", $this->Identifier)->where("Status", ["<" => 3])->execute();
        }
        if(!empty($this->customfields_list)) {
            $customfields = new customfields();
            $customfields->setCustomDebtorFieldsValues($this->Identifier, $this->customvalues);
        }
        if(isset($this->WelcomeMail) && 0 < $this->WelcomeMail) {
            $this->sentWelcome($this->WelcomeMail);
        }
        $this->Success[] = sprintf(__("debtor is adjusted"), $this->DebtorCode);
        $debtor_info = ["id" => $this->Identifier, "DebtorCode" => $this->DebtorCode];
        do_action("debtor_is_edited", $debtor_info);
        return true;
    }
    public function delete($remove = false)
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for debtor");
            return false;
        }
        if($remove) {
            $result = Database_Model::getInstance()->delete("HostFact_Debtors")->where("id", $this->Identifier)->execute();
            if($result) {
                Database_Model::getInstance()->delete("HostFact_Debtor_Custom_Values")->where("ReferenceType", "debtor")->where("ReferenceID", $this->Identifier)->execute();
            }
        } else {
            $result = Database_Model::getInstance()->update("HostFact_Debtors", ["Status" => 9])->where("id", $this->Identifier)->execute();
        }
        if($result) {
            Database_Model::getInstance()->update("HostFact_PeriodicElements", ["Status" => 8, "TerminationDate" => date("Y-m-d")])->where("Debtor", $this->Identifier)->where("Status", ["<" => 8])->execute();
            Database_Model::getInstance()->update("HostFact_Domains", ["PeriodicID" => 0])->where("Debtor", $this->Identifier)->execute();
            Database_Model::getInstance()->update("HostFact_Hosting", ["PeriodicID" => 0])->where("Debtor", $this->Identifier)->execute();
            require_once "class/clientareachange.php";
            $ClientareaChanges = new ClientareaChange_Model();
            $ClientareaChanges->cancelPendingChanges("debtor", $this->Identifier, $this->Identifier);
            $this->Success[] = sprintf(__("debtor is removed"), $this->Identifier);
            if(isset($_SESSION["active_clients"])) {
                unset($_SESSION["active_clients"]);
            }
            if($remove === false) {
                $debtor_info = ["id" => $this->Identifier, "DebtorCode" => $this->DebtorCode];
                do_action("debtor_is_archived", $debtor_info);
            }
            return true;
        }
        $this->Error[] = sprintf(__("debtor cannot be removed"), $this->Identifier);
        return false;
    }
    public function recover()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for debtor");
            return false;
        }
        if(!$this->__checkClientLimit()) {
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Debtors", ["Status" => 0])->where("id", $this->Identifier)->where("Status", 9)->execute();
        if($result) {
            $this->Success[] = sprintf(__("debtor is recovered"), $this->Identifier);
            $this->Warning[] = sprintf(__("periodics from this debtor are not recovered"));
            return true;
        }
        $this->Error[] = sprintf(__("debtor cannot be recovered"), $this->Identifier);
        return false;
    }
    public function getOpenAmount($currency_sign = true)
    {
        $financial_var = Database_Model::getInstance()->getOne("HostFact_Invoice", ["ROUND(SUM(`AmountExcl`-(`AmountPaid`/(`AmountIncl`/`AmountExcl`))),2) as OpenAmountExcl", "ROUND(SUM(`AmountIncl`-`AmountPaid`),2) as OpenAmountIncl"])->where("Debtor", $this->Identifier)->orWhere([["Status", 2], ["Status", 3]])->groupBy("Debtor")->execute();
        if($financial_var && !isEmptyFloat($financial_var->OpenAmountExcl)) {
            $this->OpenAmountExcl = money($financial_var->OpenAmountExcl, $currency_sign);
            $this->OpenAmountIncl = money($financial_var->OpenAmountIncl, $currency_sign);
        } else {
            $this->OpenAmountExcl = money(0, $currency_sign);
            $this->OpenAmountIncl = money(0, $currency_sign);
        }
        return true;
    }
    public function getFinancialInfo($info = "")
    {
        if($info == "total") {
            $financial_var = Database_Model::getInstance()->getOne("HostFact_Invoice", ["ROUND(SUM(`AmountExcl`),2) as TurnoverThisYear"])->where("Debtor", $this->Identifier)->where("Status", ["IN" => [2, 3, 4, 8, 9]])->where("YEAR(`Date`)", date("Y"))->groupBy("Debtor")->execute();
            $this->TurnoverThisYear = $financial_var->TurnoverThisYear;
            $financial_var = Database_Model::getInstance()->getOne("HostFact_Invoice", ["ROUND(SUM(`AmountExcl`),2) as TurnoverLastYear"])->where("Debtor", $this->Identifier)->where("Status", ["IN" => [2, 3, 4, 8, 9]])->where("YEAR(`Date`)", date("Y", strtotime("-1 YEAR")))->groupBy("Debtor")->execute();
            $this->TurnoverLastYear = $financial_var->TurnoverLastYear;
        }
        if($info != "TotalAmountExcl" && $info != "total") {
            $financial_var = Database_Model::getInstance()->getOne("HostFact_Invoice", ["AVG(DATEDIFF(IF(`PayDate`>0, `PayDate`, NOW()), `Date`)) as AverageOutstandingDays"])->where("Debtor", $this->Identifier)->where("Status", ["IN" => [2, 3, 4]])->orWhere([["PayDate", ["!=" => "0000-00-00"]], ["Status", ["<" => 4]]])->groupBy("Debtor")->execute();
            $this->AverageOutstandingDays = isset($financial_var->AverageOutstandingDays) ? number_format($financial_var->AverageOutstandingDays, 0) : 0;
        }
        if($info != "AverageOutstandingDays") {
            $financial_var = Database_Model::getInstance()->getOne("HostFact_Invoice", ["ROUND(SUM(`AmountExcl`),2) as TotalAmount"])->where("Debtor", $this->Identifier)->where("Status", ["IN" => [2, 3, 4, 8, 9]])->groupBy("Debtor")->execute();
            $this->TotalAmountExcl = isset($financial_var->TotalAmount) ? money($financial_var->TotalAmount, false) : money(0, false);
        }
        return true;
    }
    public function newDebtorCode($prefix = DEBTORCODE_PREFIX, $number = DEBTORCODE_NUMBER)
    {
        $prefix = parsePrefixVariables($prefix);
        $length = strlen($prefix . $number);
        $result = Database_Model::getInstance()->getOne("HostFact_Debtors", ["DebtorCode"])->where("DebtorCode", ["LIKE" => $prefix . "%"])->where("LENGTH(`DebtorCode`)", [">=" => $length])->where("(SUBSTR(`DebtorCode`,:PrefixLength)*1)", [">" => 0])->where("SUBSTR(`DebtorCode`,:PrefixLength)", ["REGEXP" => "^[0-9]+\$"])->orderBy("(SUBSTR(`DebtorCode`,:PrefixLength)*1)", "DESC")->bindValue("PrefixLength", strlen($prefix) + 1)->execute();
        if(isset($result->DebtorCode) && $result->DebtorCode && is_numeric(substr($result->DebtorCode, strlen($prefix)))) {
            $DebtorCode = substr($result->DebtorCode, strlen($prefix));
            $DebtorCode = $prefix . @str_repeat("0", @max(@strlen($number) - @strlen(@max($DebtorCode + 1, $number)), 0)) . max($DebtorCode + 1, $number);
        } else {
            $DebtorCode = $prefix . $number;
        }
        if(!$this->is_free($DebtorCode)) {
            $this->Error[] = sprintf(__("generated debtorcode is already in use"), $DebtorCode);
        }
        return !empty($this->Error) ? false : $DebtorCode;
    }
    public function is_removed($debtor_code)
    {
        if($debtor_code) {
            $result = Database_Model::getInstance()->getOne("HostFact_Debtors", ["id", "CompanyName", "Initials", "SurName", "Status"])->where("DebtorCode", $debtor_code)->execute();
            if($result->Status == 9) {
                return $result;
            }
            return false;
        }
        return false;
    }
    public function validate()
    {
        if($this->Status == 9 && $this->Anonymous == "yes") {
            $this->Error[] = __("this debtor has been made anonymous and cannot be used again");
            return false;
        }
        if(!$this->is_free($this->DebtorCode)) {
            $is_removed = $this->is_removed($this->DebtorCode);
            if($is_removed === false) {
                $this->Error[] = sprintf(__("debtorcode is already in use"), $this->DebtorCode);
            } else {
                $recoverurl = "debtors.php?page=show&id=" . $is_removed->id . "&action=recover";
                $removed_debtor = $this->DebtorCode . " " . ($is_removed->CompanyName ? $is_removed->CompanyName : $is_removed->Initials . " " . $is_removed->SurName);
                $this->Error[] = sprintf(__("debtorcode not available, debtor removed"), $this->DebtorCode, $recoverurl, $removed_debtor);
                return false;
            }
        }
        global $array_debtorstatus;
        global $array_country;
        global $array_invoicemethod;
        global $array_legaltype;
        global $array_customer_languages;
        if(!array_key_exists($this->Status, $array_debtorstatus)) {
            $this->Error[] = __("invalid status for debtor");
        }
        if(!trim($this->CompanyName) && !trim($this->SurName)) {
            $this->Error[] = __("no companyname and no surname are given");
        }
        if(!in_array($this->Taxable, ["no", "yes", "auto"])) {
            if($this->TaxableSetting) {
                $this->Taxable = $this->TaxableSetting;
            } else {
                $this->Taxable = "auto";
            }
        }
        if(!in_array($this->Taxable, ["no", "yes", "auto"])) {
            $this->Error[] = __("no valid value for taxable");
        }
        if(!(check_email_address($this->EmailAddress) || strlen($this->EmailAddress) === 0 || 255 < strlen($this->EmailAddress))) {
            $this->Error[] = sprintf(__("invalid email"), $this->EmailAddress);
        }
        if(!(check_email_address($this->InvoiceEmailAddress) || strlen($this->InvoiceEmailAddress) === 0 || 255 < strlen($this->InvoiceEmailAddress))) {
            $this->Error[] = sprintf(__("invalid invoicemail"), $this->InvoiceEmailAddress);
        }
        if(!(check_email_address($this->ReminderEmailAddress) || strlen($this->ReminderEmailAddress) === 0 || 255 < strlen($this->ReminderEmailAddress))) {
            $this->Error[] = sprintf(__("invalid remindermail"), $this->ReminderEmailAddress);
        }
        if($this->ActiveLogin && !in_array($this->ActiveLogin, ["yes", "no"])) {
            $this->Error[] = __("invalid value for activelogin");
        } elseif(!$this->ActiveLogin) {
            $this->ActiveLogin = "yes";
        }
        if(!$this->is_free_username($this->Username) || !(is_string($this->Username) && strlen($this->Username) <= 100 || strlen($this->Username) === 0)) {
            $this->Error[] = sprintf(__("invalid username or already in use"), $this->Username);
        }
        if(!empty($this->Password) && empty($this->OneTimePasswordValidTill)) {
            $this->OneTimePasswordValidTill = static::setOneTimePasswordValidTill();
        } elseif(empty($this->Password)) {
            $this->OneTimePasswordValidTill = "";
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
        if($this->LegalForm && $this->LegalForm != "ANDERS" && !array_key_exists($this->LegalForm, $array_legaltype)) {
            $this->Error[] = __("invalid legalform");
        }
        if($this->Sex && !in_array($this->Sex, settings::GENDER_AVAILABLE_OPTIONS)) {
            $this->Error[] = __("invalid sex");
        } elseif(!$this->Sex) {
            $this->Sex = "m";
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
        } elseif(!array_key_exists($this->Country, $array_country)) {
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
        if(!(is_string($this->AccountName) && strlen($this->AccountName) <= 100 || strlen($this->AccountName) === 0)) {
            $this->Error[] = __("invalid accountname");
        }
        if(!(is_string($this->AccountBank) && strlen($this->AccountBank) <= 100 || strlen($this->AccountBank) === 0)) {
            $this->Error[] = __("invalid accountbank");
        }
        if(!(is_string($this->AccountCity) && strlen($this->AccountCity) <= 100 || strlen($this->AccountCity) === 0)) {
            $this->Error[] = __("invalid accountcity");
        }
        if($this->InvoiceAuthorisation == "yes" && SDD_ID) {
            if(!$this->MandateID && !$this->MandateDate && (!$this->Identifier || !$this->getDirectDebitMandate())) {
                $this->MandateID = $this->getDirectDebitMandateID();
                $this->MandateDate = rewrite_date_db2site(date("Y-m-d"));
                $this->MandateType = "RCUR";
            }
            if(!$this->MandateID || 35 < strlen($this->MandateID)) {
                $this->Error[] = __("sdd mandateid invalid");
            } elseif(!$this->checkDirectDebitMandateID($this->MandateID)) {
                $this->Error[] = __("sdd mandateid already used");
            }
            if(!$this->MandateDate || !is_date(rewrite_date_site2db($this->MandateDate))) {
                $this->Error[] = __("sdd mandate sign date invalid");
            }
            if(!$this->AccountNumber) {
                $this->Error[] = __("sdd accountnumber required");
            }
        } elseif($this->InvoiceAuthorisation == "yes" && !($this->AccountNumber || $this->AccountCity)) {
            $this->Warning[] = __("no bankaccount while authorisation");
        }
        if($this->InvoiceAuthorisation && !in_array($this->InvoiceAuthorisation, ["yes", "no"])) {
            $this->Error[] = __("invalid invoiceauthorisation");
        } elseif(!$this->InvoiceAuthorisation) {
            $this->InvoiceAuthorisation = "no";
        }
        if(!is_numeric($this->InvoiceTerm) && strlen($this->InvoiceTerm) !== 0) {
            $this->Error[] = __("invalid invoiceterm");
        }
        if(0 < strlen($this->InvoiceMethod) && !in_array($this->InvoiceMethod, array_keys($array_invoicemethod))) {
            $this->Error[] = __("invalid invoicemethod");
        } elseif(strlen($this->InvoiceMethod) === 0) {
            $this->InvoiceMethod = STANDARD_INVOICEMETHOD;
        }
        if(!is_numeric($this->InvoiceTerm) && strlen($this->InvoiceTerm) !== 0) {
            $this->Error[] = __("invalid invoiceterm");
        }
        if(!in_array($this->InvoiceMethod, array_keys($array_invoicemethod))) {
            $this->Error[] = __("invalid invoicemethod");
        }
        if(!(is_string($this->InvoiceCompanyName) && strlen($this->InvoiceCompanyName) <= 100 || strlen($this->InvoiceCompanyName) === 0)) {
            $this->Error[] = __("invalid invoicecompanyname");
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
        } elseif($this->InvoiceCountry && !array_key_exists($this->InvoiceCountry, $array_country)) {
            $this->Error[] = __("invalid invoicecountry");
        }
        if($this->Mailing && !in_array($this->Mailing, ["yes", "no"])) {
            $this->Error[] = __("invalid value for mailing option");
        } elseif(!$this->Mailing) {
            $this->Mailing = "yes";
        }
        if(0 < strlen($this->InvoiceCollect) && !in_array($this->InvoiceCollect, [-1, 0, 1, 2])) {
            $this->Error[] = __("invalid value for invoicecollect option");
        }
        if($this->InvoiceDataForPriceQuote && !in_array($this->InvoiceDataForPriceQuote, ["yes", "no"])) {
            $this->Error[] = __("invalid value for invoicedataforpricequote option");
        } elseif(!$this->InvoiceDataForPriceQuote) {
            $this->InvoiceDataForPriceQuote = "no";
        }
        if(!is_numeric($this->PeriodicInvoiceDays)) {
            $this->Error[] = __("invalid value for custom periodic invoice days");
        }
        require_once "class/template.php";
        $template = new template();
        $fields = ["Name"];
        $templates = $template->all($fields, "", "", "", "Type", "invoice");
        $pricequotetemplates = $template->all($fields, "", "", "", "Type", "pricequote");
        $emailtemplate = new emailtemplate();
        $emailtemplates = $emailtemplate->all($fields);
        if(0 < $this->InvoiceTemplate && !isset($templates[$this->InvoiceTemplate])) {
            $this->Error[] = __("invalid ID for invoice template");
        }
        if(0 < $this->PriceQuoteTemplate && !isset($pricequotetemplates[$this->PriceQuoteTemplate])) {
            $this->Error[] = __("invalid ID for pricequote template");
        }
        if(0 < $this->ReminderTemplate && !isset($emailtemplates[$this->ReminderTemplate])) {
            $this->Error[] = __("invalid ID for reminder template");
        }
        if(0 < $this->SecondReminderTemplate && !isset($emailtemplates[$this->SecondReminderTemplate])) {
            $this->Error[] = __("invalid ID for second reminder template");
        }
        if(0 < $this->SummationTemplate && !isset($emailtemplates[$this->SummationTemplate])) {
            $this->Error[] = __("invalid ID for summation template");
        }
        if((string) $this->PaymentMail === "-1" || $this->PaymentMail === "") {
        } else {
            $paymentMailOptions = explode("|", $this->PaymentMail);
            $paymentMailOptionsNotInAcceptedList = array_diff($paymentMailOptions, ["auth", "wire", "order", "paid"]);
            if(0 < count($paymentMailOptionsNotInAcceptedList) || count($paymentMailOptions) != count(array_unique($paymentMailOptions))) {
                $this->Error[] = __("invalid ID for paymentmail");
            }
        }
        if(0 < $this->PaymentMailTemplate && !isset($emailtemplates[$this->PaymentMailTemplate])) {
            $this->Error[] = __("invalid ID for paymentmail template");
        }
        if($this->DefaultLanguage && !array_key_exists($this->DefaultLanguage, $array_customer_languages)) {
            $this->Error[] = __("invalid default language for client");
        }
        if(0 < $this->Server) {
            require_once "class/server.php";
            $server = new server();
            if(!$server->show($this->Server)) {
                $this->Error[] = __("invalid ID for default server");
            }
        }
        if(0 < $this->ClientareaProfile) {
            require_once "class/clientareaprofiles.php";
            $clientareaprofile = new ClientareaProfiles_Model();
            $clientareaprofile->id = $this->ClientareaProfile;
            if(!$clientareaprofile->show()) {
                $this->Error[] = __("invalid identifier for clientarea profile");
            }
        }
        if(!empty($this->customfields_list)) {
            foreach ($this->customfields_list as $custom_field) {
                switch ($custom_field["LabelType"]) {
                    case "date":
                        $this->customvalues[$custom_field["FieldCode"]] = isset($this->customvalues[$custom_field["FieldCode"]]) && rewrite_date_site2db($this->customvalues[$custom_field["FieldCode"]]) ? date("Y-m-d", strtotime(rewrite_date_site2db($this->customvalues[$custom_field["FieldCode"]]))) : "";
                        break;
                    case "checkbox":
                        $this->customvalues[$custom_field["FieldCode"]] = is_array($this->customvalues[$custom_field["FieldCode"]]) ? json_encode($this->customvalues[$custom_field["FieldCode"]]) : "";
                        break;
                    default:
                        if($custom_field["Regex"] && !@preg_match($custom_field["Regex"], $this->customvalues[$custom_field["FieldCode"]])) {
                            $this->Error[] = sprintf(__("custom client fields regex"), $custom_field["LabelTitle"]);
                        }
                }
            }
        }
        if(empty($this->Error) && ($this->InvoiceMethod == "0" || $this->InvoiceMethod == "3") && !$this->EmailAddress && !$this->InvoiceEmailAddress) {
            global $array_invoicemethod;
            $this->Warning[] = sprintf(__("invoicemethod via mail not possible without mailaddress"), $array_invoicemethod[$this->InvoiceMethod]);
            $this->InvoiceMethod = 1;
        }
        return empty($this->Error) ? true : false;
    }
    public function is_free($DebtorCode)
    {
        if($DebtorCode) {
            $result = Database_Model::getInstance()->getOne("HostFact_Debtors", "id")->where("DebtorCode", $DebtorCode)->execute();
            if(!$result || $result->id == $this->Identifier) {
                return true;
            }
            return false;
        }
        return false;
    }
    public function is_free_username($username)
    {
        if(trim($username)) {
            $result = Database_Model::getInstance()->getOne("HostFact_Debtors", "id")->where("Username", $username)->execute();
            if(!$result || $result->id == $this->Identifier) {
                return true;
            }
            return false;
        }
        return true;
    }
    public function all($fields, $sort = "Debtor", $order = false, $limit = "-1", $searchat = false, $searchfor = false, $group = false, $show_results = MAX_RESULTS_LIST)
    {
        $limit = !is_numeric($show_results) ? -1 : $limit;
        if(!is_array($fields) || empty($fields)) {
            $this->Error[] = __("no fields given for retrieving data");
            return false;
        }
        if(!is_numeric($show_results)) {
            $this->Error[] = __("invalid number for displaying results");
            return false;
        }
        if($group !== false && is_array($group) && 0 < count($group)) {
            $filters = $group;
            if(array_key_exists("group", $group)) {
                $group = $group["group"];
                unset($filters["group"]);
            }
        }
        $DomainArray = ["Domain"];
        $DomainSearch = false;
        $DomainFields = 0 < count(array_intersect($DomainArray, $fields)) ? true : false;
        $search_at = [];
        if($searchat && $searchfor) {
            $search_at = explode("|", $searchat);
            $DomainSearch = 0 < count(array_intersect($DomainArray, $search_at)) ? true : false;
        }
        if(in_array("CustomFieldValue", $search_at)) {
            $this->CustomFields = new customfields();
            $debtors_ids = $this->CustomFields->searchCustomFieldsByValue($searchfor, "debtor");
        }
        $select = ["HostFact_Debtors.id"];
        foreach ($fields as $column) {
            if(in_array($column, $DomainArray)) {
                $select[] = "HostFact_Domains.`" . $column . "`";
            }
            if($column != "Groups" && $column != "OpenAmountIncl") {
                $select[] = "HostFact_Debtors.`" . $column . "`";
            }
        }
        if($group && !in_array($group, ["auth", "archived"])) {
            Database_Model::getInstance()->get(["HostFact_Debtors", "HostFact_GroupRelations"], $select)->where("HostFact_GroupRelations.`Group`", $group)->where("HostFact_GroupRelations.`Reference` = HostFact_Debtors.`id`")->where("HostFact_Debtors.Status", ["!=" => "9"]);
        } else {
            Database_Model::getInstance()->get("HostFact_Debtors", $select);
            if($group && $group == "auth") {
                Database_Model::getInstance()->where("HostFact_Debtors.InvoiceAuthorisation", "yes");
            } elseif($group && $group == "archived") {
                Database_Model::getInstance()->where("HostFact_Debtors.Status", "9")->where("HostFact_Debtors.Anonymous", "no");
            }
            if(!$group || $group != "archived") {
                Database_Model::getInstance()->where("HostFact_Debtors.Status", ["!=" => "9"]);
            }
        }
        if(isset($this->restrictedAll) && $this->restrictedAll == "mailing:yes") {
            Database_Model::getInstance()->where("HostFact_Debtors.Mailing", "yes");
        }
        if($DomainFields || $DomainSearch) {
            Database_Model::getInstance()->join("HostFact_Domains", "HostFact_Domains.`Debtor`=HostFact_Debtors.`id`");
        }
        if(!empty($search_at)) {
            $or_clausule = [];
            foreach ($search_at as $searchColumn) {
                if($searchColumn == "Domain") {
                    $or_clausule[] = ["CONCAT(HostFact_Domains.`Domain`, HostFact_Domains.`Tld`)", ["LIKE" => "%" . $searchfor . "%"]];
                } elseif(in_array($searchColumn, $DomainArray)) {
                    $or_clausule[] = ["HostFact_Domains.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                } elseif($searchColumn == "PhoneNumber" || $searchColumn == "MobileNumber" || $searchColumn == "FaxNumber") {
                    $searchfor_regexed = preg_replace("/[^0-9a-z]/i", "", $searchfor);
                    if($searchfor_regexed && is_numeric($searchfor_regexed)) {
                        $regex_string = ".*";
                        for ($regex_i = 0; $regex_i < strlen($searchfor_regexed); $regex_i++) {
                            $regex_string .= $searchfor_regexed[$regex_i] . ".*";
                        }
                        $or_clausule[] = ["HostFact_Debtors.`" . $searchColumn . "`", ["REGEXP" => "^" . $regex_string . "\$"]];
                    } else {
                        $or_clausule[] = ["HostFact_Debtors.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                    }
                } elseif($searchColumn == "AccountNumber") {
                    $searchfor_regexed = preg_replace("/[^0-9A-Za-z]/i", "", $searchfor);
                    $regex_string = "^.*";
                    for ($regex_i = 0; $regex_i < strlen($searchfor_regexed); $regex_i++) {
                        $regex_string .= $searchfor_regexed[$regex_i] . ".*";
                    }
                    $or_clausule[] = ["HostFact_Debtors.`" . $searchColumn . "`", ["REGEXP" => "^" . $regex_string . "\$"]];
                } elseif($searchColumn == "CustomFieldValue") {
                    if($debtors_ids && is_array($debtors_ids) && 0 < count($debtors_ids)) {
                        $or_clausule[] = ["HostFact_Debtors.`id`", ["IN" => $debtors_ids]];
                    }
                } else {
                    $or_clausule[] = ["HostFact_Debtors.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                }
            }
            if(!empty($or_clausule)) {
                Database_Model::getInstance()->orWhere($or_clausule);
            }
        }
        $order = $sort ? $order ? $order : "ASC" : "";
        if($sort == "Debtor") {
            Database_Model::getInstance()->orderBy("CONCAT(HostFact_Debtors.`CompanyName`, HostFact_Debtors.`SurName`)", $order);
        } elseif(in_array($sort, $DomainArray)) {
            Database_Model::getInstance()->orderBy("HostFact_Domains.`" . $sort . "`", $order);
        } elseif($sort == "DebtorCode") {
            $order = $order ? $order : "ASC";
            Database_Model::getInstance()->orderBy("IF(SUBSTRING(HostFact_Debtors.`DebtorCode`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(HostFact_Debtors.`DebtorCode`,1,1))", $order)->orderBy("LENGTH(HostFact_Debtors.`DebtorCode`)", $order)->orderBy("HostFact_Debtors.`DebtorCode`", $order);
        } elseif($sort) {
            Database_Model::getInstance()->orderBy("HostFact_Debtors.`" . $sort . "`", $order);
        }
        if(0 <= $limit) {
            Database_Model::getInstance()->limit((max(1, $limit) - 1) * $show_results, $show_results);
        }
        if(!empty($filters)) {
            foreach ($filters as $_db_field => $_db_value) {
                switch ($_db_field) {
                    case "created":
                        if(isset($_db_value["from"]) && $_db_value["from"]) {
                            Database_Model::getInstance()->where("HostFact_Debtors.Created", [">=" => $_db_value["from"]]);
                        }
                        if(isset($_db_value["to"]) && $_db_value["to"]) {
                            Database_Model::getInstance()->where("HostFact_Debtors.Created", ["<=" => $_db_value["to"]]);
                        }
                        break;
                    case "modified":
                        if(isset($_db_value["from"]) && $_db_value["from"]) {
                            Database_Model::getInstance()->where("HostFact_Debtors.Modified", [">=" => $_db_value["from"]]);
                        }
                        if(isset($_db_value["to"]) && $_db_value["to"]) {
                            Database_Model::getInstance()->where("HostFact_Debtors.Modified", ["<=" => $_db_value["to"]]);
                        }
                        break;
                }
            }
        }
        $list = [];
        $list["CountRows"] = 0;
        if($debtor_list = Database_Model::getInstance()->execute()) {
            if($group && !in_array($group, ["auth", "archived"])) {
                $list["CountRows"] = Database_Model::getInstance()->rowCount(["HostFact_Debtors", "HostFact_GroupRelations"], "HostFact_Debtors.id");
            } else {
                $list["CountRows"] = Database_Model::getInstance()->rowCount("HostFact_Debtors", "HostFact_Debtors.id");
            }
            foreach ($debtor_list as $result) {
                $list[$result->id] = ["id" => $result->id];
                foreach ($fields as $column) {
                    if(in_array($column, $this->Variables) || in_array($column, ["Created", "Modified"])) {
                        $list[$result->id][$column] = htmlspecialchars($result->{$column});
                    } elseif($column == "Groups") {
                        $list[$result->id][$column] = [];
                        $group_list = Database_Model::getInstance()->get(["HostFact_Group", "HostFact_GroupRelations"], ["HostFact_Group.id", "HostFact_Group.GroupName"])->where("HostFact_GroupRelations.Reference", $result->id)->where("HostFact_GroupRelations.Type", "debtor")->where("HostFact_GroupRelations.Group = HostFact_Group.id")->where("HostFact_Group.Type", "debtor")->where("HostFact_Group.Status", ["!=" => 9])->execute();
                        if($group_list && is_array($group_list)) {
                            foreach ($group_list as $result2) {
                                $list[$result->id][$column][$result2->id] = ["id" => $result2->id, "GroupName" => htmlspecialchars($result2->GroupName)];
                            }
                        }
                    } elseif($column == "OpenAmountIncl") {
                        $open_amount = Database_Model::getInstance()->getOne("HostFact_Invoice", ["ROUND(SUM(`AmountIncl`-`AmountPaid`),2) as OpenAmountIncl"])->where("Debtor", $result->id)->where("Status", ["IN" => [2, 3]])->groupBy("Debtor")->execute();
                        $list[$result->id][$column] = $open_amount ? round((double) $open_amount->OpenAmountIncl, 2) : 0;
                    }
                }
            }
        }
        return $list;
    }
    public function getActiveClientsCount()
    {
        $result = Database_Model::getInstance()->get("HostFact_Debtors", "HostFact_Debtors.`id`")->join("HostFact_PeriodicElements", "HostFact_Debtors.`id` = HostFact_PeriodicElements.`Debtor` AND HostFact_PeriodicElements.`Status`=1", "INNER")->where("HostFact_Debtors.Status", ["!=" => 9])->groupBy("HostFact_Debtors.`id`");
        $result = Database_Model::getInstance()->rowCount("HostFact_Debtors", "HostFact_Debtors.id");
        if($result) {
            return $result;
        }
        return 0;
    }
    public function getPushData()
    {
        $result = Database_Model::getInstance()->getOne("HostFact_Invoice", "COUNT(`id`) as `Count`")->where("Debtor", $this->Identifier)->execute();
        $data["Invoices"] = $result && 0 < $result->Count ? $result->Count : 0;
        $result = Database_Model::getInstance()->getOne("HostFact_NewOrder", "COUNT(`id`) as `Count`")->where("Debtor", $this->Identifier)->where("Type", "debtor")->execute();
        $data["Orders"] = $result && 0 < $result->Count ? $result->Count : 0;
        $result = Database_Model::getInstance()->getOne("HostFact_PriceQuote", "COUNT(`id`) as `Count`")->where("Debtor", $this->Identifier)->execute();
        $data["PriceQuotes"] = $result && 0 < $result->Count ? $result->Count : 0;
        $tmp_count = 0;
        $result = Database_Model::getInstance()->getOne("HostFact_Domains", "COUNT(`id`) as `Count`")->where("Debtor", $this->Identifier)->execute();
        $tmp_count += $result && 0 < $result->Count ? $result->Count : 0;
        $result = Database_Model::getInstance()->getOne("HostFact_Hosting", "COUNT(`id`) as `Count`")->where("Debtor", $this->Identifier)->execute();
        $tmp_count += $result && 0 < $result->Count ? $result->Count : 0;
        global $additional_product_types;
        global $_module_instances;
        foreach ($additional_product_types as $product_type => $product_type_title) {
            if(isset($_module_instances[$product_type]) && method_exists($_module_instances[$product_type], "service_get_termination_parameters")) {
                $_params = $_module_instances[$product_type]->service_get_termination_parameters();
                $result = Database_Model::getInstance()->getOne($_params["table_name"], "COUNT(`id`) as `Count`")->where("Debtor", $this->Identifier)->execute();
                $tmp_count += $result && 0 < $result->Count ? $result->Count : 0;
            }
        }
        $result = Database_Model::getInstance()->getOne("HostFact_PeriodicElements", "COUNT(`id`) as `Count`")->where("Debtor", $this->Identifier)->where("PeriodicType", "other")->execute();
        $tmp_count += $result && 0 < $result->Count ? $result->Count : 0;
        $result = Database_Model::getInstance()->getOne("HostFact_Handles", "COUNT(`id`) as `Count`")->where("Debtor", $this->Identifier)->execute();
        $tmp_count += $result && 0 < $result->Count ? $result->Count : 0;
        $data["Services"] = $tmp_count;
        unset($tmp_count);
        $result = Database_Model::getInstance()->getOne("HostFact_Interactions", "COUNT(`id`) as `Count`")->where("Debtor", $this->Identifier)->execute();
        $data["Interactions"] = $result && 0 < $result->Count ? $result->Count : 0;
        $result = Database_Model::getInstance()->getOne("HostFact_Tickets", "COUNT(`id`) as `Count`")->where("Debtor", $this->Identifier)->execute();
        $data["Tickets"] = $result && 0 < $result->Count ? $result->Count : 0;
        $data["Comment"] = $this->Comment ? 1 : 0;
        return $data;
    }
    public function pushDebtor($checkboxes, $pushdata)
    {
        if($this->FromDebtor == $this->ToDebtor || !is_numeric($this->FromDebtor) || !is_numeric($this->ToDebtor)) {
            $this->Error[] = sprintf(__("invalid push combination"));
            return false;
        }
        $should_delete_debtor = true;
        if(isset($checkboxes["Invoices"]) && $checkboxes["Invoices"] == "yes") {
            Database_Model::getInstance()->update("HostFact_Invoice", ["Debtor" => $this->ToDebtor])->where("Debtor", $this->FromDebtor)->execute();
            Database_Model::getInstance()->update("HostFact_InvoiceElements", ["Debtor" => $this->ToDebtor])->where("Debtor", $this->FromDebtor)->execute();
            Database_Model::getInstance()->update("HostFact_SDD_BatchElements", ["Debtor" => $this->ToDebtor])->where("Debtor", $this->FromDebtor)->execute();
        } elseif(isset($pushdata["Invoices"]) && 0 < $pushdata["Invoices"]) {
            $should_delete_debtor = false;
        }
        if(isset($checkboxes["Orders"]) && $checkboxes["Orders"] == "yes") {
            $result = Database_Model::getInstance()->get("HostFact_NewOrder", "OrderCode")->where("Debtor", $this->FromDebtor)->where("Type", "debtor")->execute();
            $tmp_ordercodes_array = [];
            if($result && is_array($result)) {
                foreach ($result as $tmp_ordercode) {
                    $tmp_ordercodes_array[] = $tmp_ordercode->OrderCode;
                }
                unset($result);
                unset($tmp_ordercode);
            }
            Database_Model::getInstance()->update("HostFact_NewOrderElements", ["Debtor" => $this->ToDebtor])->where("OrderCode", ["IN" => $tmp_ordercodes_array])->execute();
            Database_Model::getInstance()->update("HostFact_NewOrder", ["Debtor" => $this->ToDebtor])->where("OrderCode", ["IN" => $tmp_ordercodes_array])->execute();
        } elseif(isset($pushdata["Orders"]) && 0 < $pushdata["Orders"]) {
            $should_delete_debtor = false;
        }
        if(isset($checkboxes["PriceQuotes"]) && $checkboxes["PriceQuotes"] == "yes") {
            Database_Model::getInstance()->update("HostFact_PriceQuote", ["Debtor" => $this->ToDebtor])->where("Debtor", $this->FromDebtor)->execute();
            Database_Model::getInstance()->update("HostFact_PriceQuoteElements", ["Debtor" => $this->ToDebtor])->where("Debtor", $this->FromDebtor)->execute();
        } elseif(isset($pushdata["PriceQuotes"]) && 0 < $pushdata["PriceQuotes"]) {
            $should_delete_debtor = false;
        }
        if(isset($checkboxes["Services"]) && $checkboxes["Services"] == "yes") {
            Database_Model::getInstance()->update("HostFact_PeriodicElements", ["Debtor" => $this->ToDebtor])->where("Debtor", $this->FromDebtor)->execute();
            Database_Model::getInstance()->update("HostFact_Hosting", ["Debtor" => $this->ToDebtor])->where("Debtor", $this->FromDebtor)->execute();
            Database_Model::getInstance()->update("HostFact_Domains", ["Debtor" => $this->ToDebtor])->where("Debtor", $this->FromDebtor)->execute();
            global $additional_product_types;
            global $_module_instances;
            foreach ($additional_product_types as $product_type => $product_type_title) {
                if(isset($_module_instances[$product_type]) && method_exists($_module_instances[$product_type], "service_get_termination_parameters")) {
                    $_params = $_module_instances[$product_type]->service_get_termination_parameters();
                    Database_Model::getInstance()->update($_params["table_name"], ["Debtor" => $this->ToDebtor])->where("Debtor", $this->FromDebtor)->execute();
                }
            }
            Database_Model::getInstance()->update("HostFact_Handles", ["Debtor" => $this->ToDebtor])->where("Debtor", $this->FromDebtor)->execute();
        } elseif(isset($pushdata["Services"]) && 0 < $pushdata["Services"]) {
            $should_delete_debtor = false;
        }
        if(isset($checkboxes["Interactions"]) && $checkboxes["Interactions"] == "yes") {
            Database_Model::getInstance()->update("HostFact_Interactions", ["Debtor" => $this->ToDebtor])->where("Debtor", $this->FromDebtor)->execute();
        } elseif(isset($pushdata["Interactions"]) && 0 < $pushdata["Interactions"]) {
            $should_delete_debtor = false;
        }
        if(isset($checkboxes["Tickets"]) && $checkboxes["Tickets"] == "yes") {
            Database_Model::getInstance()->update("HostFact_Tickets", ["Debtor" => $this->ToDebtor])->where("Debtor", $this->FromDebtor)->execute();
        } elseif(isset($pushdata["Tickets"]) && 0 < $pushdata["Tickets"]) {
            $should_delete_debtor = false;
        }
        if($should_delete_debtor) {
            if($this->Comment && isset($checkboxes["Comment"]) && $checkboxes["Comment"] == "yes") {
                $result = Database_Model::getInstance()->getOne("HostFact_Debtors", "Comment")->where("id", $this->ToDebtor)->execute();
                $comment = $result && $result->Comment ? $result->Comment . "\n\n" : "";
                $comment .= __("debtor") . " " . $this->DebtorCode . ":\n" . $this->Comment;
                Database_Model::getInstance()->update("HostFact_Debtors", ["Comment" => $comment])->where("id", $this->ToDebtor)->execute();
                Database_Model::getInstance()->delete("HostFact_GroupRelations")->where("Type", "debtor")->where("Reference", $this->FromDebtor)->execute();
            }
            Database_Model::getInstance()->delete("HostFact_Debtors")->where("id", $this->FromDebtor)->execute();
            Database_Model::getInstance()->delete("HostFact_Debtor_Custom_Values")->where("ReferenceType", "debtor")->where("ReferenceID", $this->FromDebtor)->execute();
            $this->Success[] = sprintf(__("push and delete debtor success"), $this->DebtorCode);
            $this->Identifier = $this->ToDebtor;
            return true;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_Debtors", "DebtorCode")->where("id", $this->ToDebtor)->execute();
        $this->Success[] = sprintf(__("selected data is pushed but debtor not deleted"), $result->DebtorCode);
        $this->Identifier = $this->FromDebtor;
        return true;
    }
    public function all_small()
    {
        $result = Database_Model::getInstance()->get("HostFact_Debtors", ["id", "DebtorCode", "CompanyName", "SurName", "Initials", "Server"])->where("Status", ["!=" => 9])->orderBy("CONCAT(`CompanyName`,`SurName`,`DebtorCode`)", "ASC")->execute();
        $list = [];
        if($result) {
            foreach ($result as $var) {
                $list[$var->id] = [];
                foreach ($var as $column => $value) {
                    $list[$var->id][$column] = htmlspecialchars($value);
                }
            }
        }
        return $list;
    }
    public function sentWelcome($template_id = WELCOME_MAIL)
    {
        if(!check_email_address($this->EmailAddress) || strlen($this->EmailAddress) === 0) {
            $this->Error[] = sprintf(__("invalid email, cannot send welcome-mail"), $this->EmailAddress);
            return false;
        }
        require_once "class/email.php";
        $email = new email();
        $email->Debtor = $this->Identifier;
        $email->Recipient = $this->EmailAddress;
        if(0 < $template_id) {
            require_once "class/template.php";
            $emailtemplate = new emailtemplate();
            $emailtemplate->Identifier = $template_id;
            $emailtemplate->show();
            $emailtemplate->Attachment = implode("|", $emailtemplate->Attachment);
            foreach ($emailtemplate->Variables as $v) {
                if(is_string($emailtemplate->{$v})) {
                    $email->{$v} = htmlspecialchars_decode($emailtemplate->{$v});
                } else {
                    $email->{$v} = $emailtemplate->{$v};
                }
            }
            $email->add(["debtor" => $this]);
            if($email->sent("", "", false, ["debtor" => $this])) {
                $logEmailAddress = check_email_address($this->EmailAddress, "convert", ", ");
                $this->Success[] = sprintf(__("welcome-mail success"), $logEmailAddress);
                return true;
            }
            $this->Error[] = sprintf(__("welcome-mail failed"), isset($email->MailerError) ? $email->MailerError : "");
            return false;
        } else {
            $this->Error[] = __("welcome-mail no template");
            return false;
        }
    }
    public function getPleskClientIDs()
    {
        $id_list = Database_Model::getInstance()->get("HostFact_ServersPleskClients", ["ClientID", "ServerID"])->where("DebtorID", $this->Identifier)->execute();
        $list = [];
        if($id_list && is_array($id_list)) {
            foreach ($id_list as $result) {
                $list[$result->ServerID] = $result->ClientID;
            }
        }
        $list["CountRows"] = count($list);
        return $list;
    }
    public function updatePleskClientIDs($client_ids)
    {
        Database_Model::getInstance()->delete("HostFact_ServersPleskClients")->where("DebtorID", $this->Identifier)->execute();
        foreach ($client_ids as $cid) {
            $tmp = explode("=", $cid);
            if(isset($tmp[1]) && $tmp[1]) {
                $result = Database_Model::getInstance()->insert("HostFact_ServersPleskClients", ["ServerID" => $tmp[0], "DebtorID" => $this->Identifier, "ClientID" => $tmp[1]])->execute();
                if($result) {
                    return true;
                }
            }
        }
        return false;
    }
    public function updateLoginDetails($username, $password, $resend = false)
    {
        $password = passcrypt($password);
        $result = Database_Model::getInstance()->update("HostFact_Debtors", ["Username" => $username, "Password" => $password, "OneTimePasswordValidTill" => static::setOneTimePasswordValidTill()])->where("id", $this->Identifier)->execute();
        if($result) {
            $passwordforgot_email_template = CLIENTAREA_PASSWORDFORGOT_EMAIL;
            if($resend === true && 0 < $passwordforgot_email_template) {
                if(!check_email_address($this->EmailAddress) || strlen($this->EmailAddress) === 0) {
                    $this->Error[] = sprintf(__("invalid email, cannot send welcome-mail"), $this->EmailAddress);
                } else {
                    require_once "class/email.php";
                    $email = new email();
                    $email->Debtor = $this->Identifier;
                    $email->Recipient = $this->EmailAddress;
                    require_once "class/template.php";
                    $emailtemplate = new emailtemplate();
                    $emailtemplate->Identifier = $passwordforgot_email_template;
                    $emailtemplate->show();
                    $emailtemplate->Attachment = implode("|", $emailtemplate->Attachment);
                    foreach ($emailtemplate->Variables as $v) {
                        if(is_string($emailtemplate->{$v})) {
                            $email->{$v} = htmlspecialchars_decode($emailtemplate->{$v});
                        } else {
                            $email->{$v} = $emailtemplate->{$v};
                        }
                    }
                    $this->show();
                    $email->add(["debtor" => $this]);
                    if($email->sent("", "", false, ["debtor" => $this])) {
                        $logEmailAddress = check_email_address($this->EmailAddress, "convert", ", ");
                        $this->Success[] = sprintf(__("debtor logindetails mail sent"), $logEmailAddress);
                    } else {
                        $this->Error[] = sprintf(__("debtor logindetails mail failed"), isset($email->MailerError) ? $email->MailerError : "");
                    }
                }
            }
            $this->Success[] = sprintf(__("debtor logindetails changed"), $this->DebtorCode);
            return true;
        }
        return false;
    }
    public function syncEmailAddressNeeded()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for debtor");
            return false;
        }
        $result_inv = Database_Model::getInstance()->getOne("HostFact_Invoice", "COUNT(`id`) as Count")->where("Debtor", $this->Identifier)->where("Status", ["<" => 4])->execute();
        $result_prq = Database_Model::getInstance()->getOne("HostFact_PriceQuote", "COUNT(`id`) as Count")->where("Debtor", $this->Identifier)->where("Status", ["<" => 3])->execute();
        $counter = $result_inv && 0 < $result_inv->Count ? $result_inv->Count : 0;
        $counter += $result_prq && 0 < $result_prq->Count ? $result_prq->Count : 0;
        if(0 < $counter) {
            return true;
        }
        return false;
    }
    public function syncAuthorizationNeeded()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for debtor");
            return false;
        }
        $result_inv = Database_Model::getInstance()->getOne("HostFact_Invoice", "COUNT(`id`) as Count")->where("Debtor", $this->Identifier)->where("Status", ["<" => 4])->execute();
        $counter = $result_inv && 0 < $result_inv->Count ? $result_inv->Count : 0;
        if(0 < $counter) {
            return true;
        }
        return false;
    }
    public function syncAddressNeeded()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("debtor not found", ["id" => $this->Identifier]);
            return false;
        }
        $result_inv = Database_Model::getInstance()->getOne("HostFact_Invoice", "COUNT(`id`) as Count")->where("Debtor", $this->Identifier)->where("Status", ["<" => 4])->execute();
        $result_prq = Database_Model::getInstance()->getOne("HostFact_PriceQuote", "COUNT(`id`) as Count")->where("Debtor", $this->Identifier)->where("Status", ["<" => 3])->execute();
        $counter = $result_inv && 0 < $result_inv->Count ? $result_inv->Count : 0;
        $counter += $result_prq && 0 < $result_prq->Count ? $result_prq->Count : 0;
        if(0 < $counter) {
            return true;
        }
        return false;
    }
    public function getPDF($template, $options = [], $download_instead = false)
    {
        $OutputType = "D";
        $type = "";
        require_once "class/pdf.php";
        $objects = [];
        $objects["debtor"] = $this;
        if(isset($options["domain"]) && 0 < $options["domain"]) {
            require_once "class/domain.php";
            $domain = new domain();
            $domain->Identifier = $options["domain"];
            $domain->show();
            $objects["domain"] = $domain;
        }
        if(isset($options["hosting"]) && 0 < $options["hosting"]) {
            require_once "class/hosting.php";
            $hosting = new hosting();
            $hosting->Identifier = $options["hosting"];
            $hosting->show();
            $objects["hosting"] = $hosting;
            require_once "class/server.php";
            $server = new server();
            $server->Identifier = $hosting->Server;
            $server->show();
            $objects["server"] = $server;
        }
        $pdf = new pdfCreator($template, $objects, "other", "D", $download_instead);
        $pdf->setOutputType($OutputType);
        if(!$pdf->generatePDF("F")) {
            $this->Error = array_merge($this->Error, $pdf->Error);
            return false;
        }
        $_SESSION["force_download"] = $pdf->Name;
    }
    public function getDirectDebitMandate($set_values = true)
    {
        $mandate_var = Database_Model::getInstance()->getOne("HostFact_SDD_Mandates")->where("Debtor", $this->Identifier)->where("Status", "active")->execute();
        if($mandate_var && 0 < $mandate_var->id) {
            if($set_values) {
                $this->MandateID = $mandate_var->MandateID;
                $this->MandateDate = rewrite_date_db2site($mandate_var->MandateDate);
                $this->MandateType = $mandate_var->MandateType;
            }
            return true;
        }
        return false;
    }
    public function createDirectDebitMandate()
    {
        $result = Database_Model::getInstance()->insert("HostFact_SDD_Mandates", ["Debtor" => $this->Identifier, "MandateID" => $this->MandateID, "MandateDate" => rewrite_date_site2db($this->MandateDate), "MandateType" => $this->MandateType, "Status" => "active"])->execute();
        if($result) {
            return true;
        }
        return false;
    }
    public function saveDirectDebitMandate()
    {
        $result = Database_Model::getInstance()->update("HostFact_SDD_Mandates", ["MandateID" => $this->MandateID, "MandateDate" => rewrite_date_site2db($this->MandateDate), "MandateType" => $this->MandateType, "Status" => "active"])->where("Debtor", $this->Identifier)->where("Status", "active")->execute();
        if($result) {
            return true;
        }
        return false;
    }
    public function closeDirectDebitMandate()
    {
        $result = Database_Model::getInstance()->update("HostFact_SDD_Mandates", ["Status" => "suspended"])->where("Debtor", $this->Identifier)->where("Status", "active")->execute();
        if($result) {
            return true;
        }
        return false;
    }
    public function getDirectDebitMandateID()
    {
        global $company;
        $prefix = preg_replace("/[^a-z0-9]/i", "", $this->DebtorCode) . "-";
        $prefix = strtoupper($prefix . substr(preg_replace("/[^a-z0-9]/i", "", htmlspecialchars_decode($company->CompanyName)), 0, 35 - strlen($prefix) - 3) . "-");
        $mandate_var = Database_Model::getInstance()->getOne("HostFact_SDD_Mandates", "MandateID")->where("MandateID", ["LIKE" => $prefix . "%"])->orderBy("MandateID", "DESC")->execute();
        if($mandate_var && $mandate_var->MandateID) {
            $tmp_mandate_id = intval(substr($mandate_var->MandateID, -2, 2));
            return $prefix . str_pad($tmp_mandate_id + 1, 2, "0", STR_PAD_LEFT);
        }
        return $prefix . "01";
    }
    public function checkDirectDebitMandateID($mandateID)
    {
        $mandate_var = Database_Model::getInstance()->getOne("HostFact_SDD_Mandates", ["id", "Debtor", "Status"])->where("MandateID", $mandateID)->execute();
        if(!$mandate_var || $mandate_var->id <= 0) {
            return true;
        }
        if($mandate_var->Debtor == $this->Identifier && $mandate_var->Status == "active") {
            return true;
        }
        return false;
    }
    private function __checkClientLimit()
    {
        if(isset($_SESSION["active_clients"]) && defined("INT_WF_ACTIVE_DEBTOR_LIMIT") && (INT_WF_ACTIVE_DEBTOR_LIMIT <= $_SESSION["active_clients"] || $_SESSION["wf_cache_licensehash"] != md5(LICENSE . $_SESSION["active_clients"]))) {
            $this->Error[] = __("you are not able to add extra clients, debtor validate");
            return false;
        }
        if(!isset($_SESSION["active_clients"])) {
            $active_debtors_counter = $this->getActiveClientsCount();
            if(isset($active_debtors_counter) && defined("INT_WF_ACTIVE_DEBTOR_LIMIT") && INT_WF_ACTIVE_DEBTOR_LIMIT <= $active_debtors_counter - 20) {
                $this->Error[] = __("you are not able to add extra clients, debtor validate");
                return false;
            }
        }
        return true;
    }
    public function getDebtorBeginEndCode($firstLast = "first")
    {
        $orderBy = $firstLast == "first" ? "ASC" : "DESC";
        $result = Database_Model::getInstance()->getOne("HostFact_Debtors", "DebtorCode")->orderBy("IF(SUBSTRING(`DebtorCode`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(`DebtorCode`,1,1))", $orderBy)->orderBy("LENGTH(`DebtorCode`)", $orderBy)->orderBy("DebtorCode", $orderBy)->execute();
        return $result ? $result->DebtorCode : "";
    }
    public function updateToSecurePasswords()
    {
        $debtors = Database_Model::getInstance()->get("HostFact_Debtors", ["id", "Password"])->execute();
        foreach ($debtors as $debtor) {
            if(isset($debtor->Password) && $debtor->Password != "") {
                Database_Model::getInstance()->update("HostFact_Debtors", ["SecurePassword" => wf_password_hash(passcrypt($debtor->Password)), "Password" => "", "OneTimePasswordValidTill" => ["RAW" => "NULL"]])->where("id", $debtor->id)->execute();
            }
        }
    }
    public function setCustomerPanelKey($key)
    {
        if($key != "" && $this->SecurePassword == "") {
            Database_Model::getInstance()->update("HostFact_Debtors", ["SecurePassword" => wf_password_hash("temp" . time() . rand(0, 1000) . "temp")])->where("id", $this->Identifier)->execute();
        }
        $result = Database_Model::getInstance()->update("HostFact_Debtors", ["CustomerPanelKey" => $key])->where("id", $this->Identifier)->execute();
        return $result;
    }
    private function __prefixTaxnumber()
    {
        if($this->TaxNumber && is_numeric(substr($this->TaxNumber, 0, 1))) {
            $eu_countries = [];
            $result = Database_Model::getInstance()->getOne("HostFact_Settings_Countries", ["GROUP_CONCAT(`CountryCode`) AS CountryCodes"])->where("EUCountry", "yes")->execute();
            if($result && $result->CountryCodes) {
                $eu_countries = explode(",", $result->CountryCodes);
            }
            if(in_array($this->Country, $eu_countries)) {
                switch ($this->Country) {
                    case "GR":
                        $country = "EL";
                        break;
                    default:
                        $country = $this->Country;
                        $this->TaxNumber = $country . $this->TaxNumber;
                }
            }
        }
    }
    public function getByCustomerPanelKey($debtor_id, $debtor_code_encrypted)
    {
        if(!$this->CustomerPanelKey) {
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_Debtors", ["id", "DebtorCode"])->where("id", $debtor_id)->where("CustomerPanelKey", $this->CustomerPanelKey)->where("Status", ["!=" => 9])->execute();
        if($result && 0 <= $result->id && sha1($result->DebtorCode) == $debtor_code_encrypted) {
            return $result->id;
        }
        return false;
    }
    public function anonimize()
    {
        if(!$this->Identifier || !$this->show($this->Identifier) || $this->Status != 9) {
            $this->Error[] = __("only archived debtors can be anonimized");
            return false;
        }
        $params = [];
        $params["ActiveLogin"] = "no";
        $params["Username"] = "";
        $params["CompanyName"] = "";
        $params["CompanyNumber"] = "";
        $params["TaxNumber"] = "";
        $params["Initials"] = "";
        $params["SurName"] = "";
        $params["Address"] = "";
        $params["Address2"] = "";
        $params["ZipCode"] = "";
        $params["City"] = "";
        $params["State"] = "";
        $params["EmailAddress"] = "";
        $params["Website"] = "";
        $params["PhoneNumber"] = "";
        $params["MobileNumber"] = "";
        $params["FaxNumber"] = "";
        $params["InvoiceCompanyName"] = "";
        $params["InvoiceInitials"] = "";
        $params["InvoiceSurName"] = "";
        $params["InvoiceAddress"] = "";
        $params["InvoiceAddress2"] = "";
        $params["InvoiceZipCode"] = "";
        $params["InvoiceCity"] = "";
        $params["InvoiceState"] = "";
        $params["InvoiceEmailAddress"] = "";
        $params["ReminderEmailAddress"] = "";
        $params["AccountNumber"] = "";
        $params["AccountName"] = "";
        $params["Anonymous"] = "yes";
        Database_Model::getInstance()->update("HostFact_Debtors", $params)->where("id", $this->Identifier)->execute();
        global $account;
        $today = new DateTime();
        $audit = ["Who" => isset($account->Identifier) ? $account->Identifier : 0, "IP" => $_SERVER["REMOTE_ADDR"], "DateTime" => $today->format("Y-m-d H:i:s")];
        createLog("debtor", $this->Identifier, "debtor made anonymous", json_encode($audit));
        createMessageLog("success", "debtor made anonymous", [$this->DebtorCode, $account->Name, $_SERVER["REMOTE_ADDR"]], "debtor", $this->Identifier, true);
        $this->Success[] = sprintf(__("debtor made anonymous success"), $this->DebtorCode);
        return true;
    }
    public static function setOneTimePasswordValidTill()
    {
        return date("Y-m-d H:i:s", strtotime("+24 hours"));
    }
}
class VATvalidator
{
    protected static $_soapClient;
    const WSDL = "http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl";
    public static function _getSoapClient()
    {
        if(!class_exists("SoapClient", false)) {
            self::$_soapClient = new ownVat();
        }
        if(is_null(self::$_soapClient)) {
            try {
                self::$_soapClient = new SoapClient("http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl");
                $classMap = ["Check" => "checkVat"];
                $options = ["classmap" => $classMap, "encoding" => "utf-8", "features" => SOAP_SINGLE_ELEMENT_ARRAYS, "trace" => false, "exceptions" => false, "connection_timeout" => 10];
                self::$_soapClient = new SoapClient("http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl", $options);
            } catch (SoapFault $e) {
                self::$_soapClient = new ownVat2();
            }
        }
        return self::$_soapClient;
    }
}
class ownVat
{
    public function checkVat($a)
    {
        return false;
    }
}
class ownVat2
{
    public function checkVat($a)
    {
        return "servicedown";
    }
}

?>