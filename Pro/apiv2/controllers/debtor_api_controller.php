<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class debtor_api_controller extends api_controller
{
    public function __construct()
    {
        parent::__construct();
        HostFact_API::setObjectNames("debtors", "debtor");
        require_once "class/debtor.php";
        $this->addParameter("Identifier", "int");
        $this->addParameter("DebtorCode", "string");
        if(defined("IS_INTERNATIONAL") && IS_INTERNATIONAL === true) {
            $this->addParameter("Status", "int");
        }
        $this->addParameter("CompanyName", "string");
        $this->addParameter("CompanyNumber", "string");
        $this->addParameter("LegalForm", "string");
        $this->addParameter("TaxNumber", "string");
        $this->addParameter("Sex", "string");
        $this->addParameter("Initials", "string");
        $this->addParameter("SurName", "string");
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
        $this->addParameter("Comment", "string");
        $this->addParameter("InvoiceMethod", "int");
        $this->addParameter("InvoiceCompanyName", "string");
        $this->addParameter("InvoiceSex", "string");
        $this->addParameter("InvoiceInitials", "string");
        $this->addParameter("InvoiceSurName", "string");
        $this->addParameter("InvoiceAddress", "string");
        if(defined("IS_INTERNATIONAL") && IS_INTERNATIONAL === true) {
            $this->addParameter("InvoiceAddress2", "string");
        }
        $this->addParameter("InvoiceZipCode", "string");
        $this->addParameter("InvoiceCity", "string");
        if(defined("IS_INTERNATIONAL") && IS_INTERNATIONAL === true) {
            $this->addParameter("InvoiceState", "string");
        }
        $this->addParameter("InvoiceCountry", "string");
        $this->addParameter("InvoiceEmailAddress", "string");
        $this->addParameter("ReminderEmailAddress", "string");
        $this->addParameter("InvoiceAuthorisation", "string");
        $this->addParameter("MandateDate", "readonly");
        $this->addParameter("MandateID", "readonly");
        $this->addParameter("MandateType", "readonly");
        $this->addParameter("InvoiceDataForPriceQuote", "string");
        $this->addParameter("AccountNumber", "string");
        $this->addParameter("AccountBIC", "string");
        $this->addParameter("AccountName", "string");
        $this->addParameter("AccountBank", "string");
        $this->addParameter("AccountCity", "string");
        $this->addParameter("ActiveLogin", "string");
        $this->addParameter("Username", "string");
        $this->addParameter("NewUsername", "string");
        $this->addParameter("Password", "string");
        $this->addParameter("SecurePassword", "string");
        $this->addParameter("SendPasswordForgotEmail", "string");
        $this->addParameter("Mailing", "string");
        $this->addParameter("Taxable", "string");
        $this->addParameter("InvoiceTerm", "default_int");
        $this->addParameter("PeriodicInvoiceDays", "default_int");
        $this->addParameter("InvoiceTemplate", "int");
        $this->addParameter("PriceQuoteTemplate", "int");
        $this->addParameter("ReminderTemplate", "int");
        $this->addParameter("SecondReminderTemplate", "default_int");
        $this->addParameter("SummationTemplate", "int");
        $this->addParameter("PaymentMail", "string");
        $this->addParameter("PaymentMailTemplate", "int");
        $this->addParameter("InvoiceCollect", "default_int");
        $this->addParameter("DefaultLanguage", "string");
        $this->addParameter("ClientareaProfile", "int");
        $this->addParameter("Server", "int");
        $this->addParameter("DNS1", "string");
        $this->addParameter("DNS2", "string");
        $this->addParameter("DNS3", "string");
        $this->addParameter("Groups", "array_raw");
        $this->object = new debtor();
        if(!empty($this->object->customfields_list)) {
            $this->addParameter("CustomFields", "array_with_keys");
            foreach ($this->object->customfields_list as $_custom_field) {
                if($_custom_field["LabelType"] == "checkbox") {
                    $this->addSubParameter("CustomFields", $_custom_field["FieldCode"], "array_raw");
                } else {
                    $this->addSubParameter("CustomFields", $_custom_field["FieldCode"], "string");
                }
            }
        }
        $this->addParameter("Created", "readonly");
        $this->addParameter("Modified", "readonly");
        $this->addFilter("group", "int", "");
        $this->addFilter("created", "filter_datetime");
        $this->addFilter("modified", "filter_datetime");
    }
    public function list_api_action()
    {
        if(strpos(HostFact_API::getRequestParameter("searchat"), "SecondEmailAddress") !== false) {
            $search_at = explode("|", HostFact_API::getRequestParameter("searchat"));
            unset($search_at[array_search("SecondEmailAddress", $search_at)]);
            if(!in_array("EmailAddress", $search_at)) {
                $search_at[] = "EmailAddress";
            }
            HostFact_API::$_request_data[HostFact_API::$_request_method]["searchat"] = implode("|", $search_at);
        }
        $filters = $this->getFilterValues();
        $fields = ["DebtorCode", "CompanyName", "Sex", "Initials", "SurName", "EmailAddress", "Modified"];
        $sort = $filters["sort"] ? $filters["sort"] : "DebtorCode";
        $page_offset = $filters["offset"] / $filters["limit"] + 1;
        $searchat = $filters["searchat"] ? $filters["searchat"] : "DebtorCode|CompanyName|SurName";
        $limit = $filters["limit"];
        $field_filters = array_diff_key($filters, array_flip(["offset", "limit", "sort", "order", "searchat", "searchfor"]));
        $debtor_list = $this->object->all($fields, $sort, $filters["order"], $page_offset, $searchat, $filters["searchfor"], $field_filters, $limit);
        $array = [];
        foreach ($debtor_list as $key => $value) {
            if($key != "CountRows") {
                $array[] = ["Identifier" => $value["id"], "DebtorCode" => htmlspecialchars_decode($value["DebtorCode"]), "CompanyName" => htmlspecialchars_decode($value["CompanyName"]), "Sex" => $value["Sex"], "Initials" => htmlspecialchars_decode($value["Initials"]), "SurName" => htmlspecialchars_decode($value["SurName"]), "EmailAddress" => htmlspecialchars_decode($value["EmailAddress"]), "Modified" => $this->_filter_date_db2api($value["Modified"], false)];
            }
        }
        HostFact_API::setMetaData("totalresults", $debtor_list["CountRows"]);
        HostFact_API::setMetaData("currentresults", count($array));
        HostFact_API::setMetaData("offset", $filters["offset"]);
        if(!empty($this->_valid_filter_input)) {
            HostFact_API::setMetaData("filters", $this->_valid_filter_input);
        }
        HostFact_API::parseResponse($array, true);
    }
    public function show_api_action()
    {
        $debtor_id = $this->_get_debtor_id(HostFact_API::getRequestParameter("Identifier"));
        return $this->_show_debtor($debtor_id);
    }
    public function add_api_action()
    {
        $parse_array = $this->getValidParameters();
        $send_welcome = HostFact_API::getRequestParameter("SendWelcome");
        $send_welcome = $send_welcome == "yes" ? "yes" : "no";
        $debtor = $this->object;
        foreach ($parse_array as $key => $value) {
            if(in_array($key, $debtor->Variables)) {
                $debtor->{$key} = $value;
            }
        }
        $debtor->DebtorCode = !$debtor->DebtorCode ? $debtor->newDebtorCode() : $debtor->DebtorCode;
        if($debtor->ActiveLogin == "yes") {
            $debtor->Username = !$debtor->Username ? $debtor->DebtorCode : $debtor->Username;
            $debtor->Password = !$debtor->Password ? generatePassword() : $debtor->Password;
            $debtor->Password = passcrypt($debtor->Password);
            $debtor->OneTimePasswordValidTill = debtor::setOneTimePasswordValidTill();
        }
        $debtor->Country = str_replace("EU-", "", strtoupper($debtor->Country));
        $debtor->InvoiceCountry = isset($parse_array["InvoiceCountry"]) && $parse_array["InvoiceCountry"] ? str_replace("EU-", "", strtoupper($parse_array["InvoiceCountry"])) : $debtor->Country;
        $newGroups = $this->_checkDebtorGroups($parse_array);
        $debtor->Groups = !isset($parse_array["Groups"]) ? $debtor->Groups : $newGroups;
        if(isset($this->_object_parameters["CustomFields"])) {
            $customfields = new customfields();
            if($customValues = $customfields->getCustomDebtorFieldsValues(false)) {
                foreach ($customValues as $field => $valueArray) {
                    $debtor->customvalues[$field] = $valueArray["Value"];
                }
            }
            if(isset($parse_array["CustomFields"])) {
                foreach ($parse_array["CustomFields"] as $_custom_field => $_custom_value) {
                    $debtor->customvalues[$_custom_field] = $_custom_value;
                }
            }
        }
        if($debtor->add()) {
            if(isset($send_welcome) && $send_welcome == "yes") {
                $debtor->sentWelcome();
            }
            HostFact_API::parseSuccess($debtor->Success);
            return $this->_show_debtor($debtor->Identifier);
        }
        HostFact_API::parseError($debtor->Error, true);
    }
    public function edit_api_action()
    {
        $this->object->Identifier = $this->_get_debtor_id(HostFact_API::getRequestParameter("Identifier"));
        if(!$this->object->show()) {
            HostFact_API::parseError($this->object->Error, true);
        }
        $oldDebtor = clone $this->object;
        $parse_array = $this->getValidParameters();
        $helpers = ["SynchronizeEmail", "SynchronizeAuth", "SynchronizeAddress", "SynchronizeHandles"];
        foreach ($helpers as $helper) {
            $input = HostFact_API::getRequestParameter($helper);
            $this->object->{$helper} = $input == "yes" ? "yes" : "no";
        }
        $send_welcome = HostFact_API::getRequestParameter("SendWelcome");
        $send_welcome = $send_welcome == "yes" ? "yes" : "no";
        foreach ($this->object as $key => $value) {
            if(is_string($value)) {
                $this->object->{$key} = htmlspecialchars_decode($value);
            }
        }
        foreach ($parse_array as $key => $value) {
            if(in_array($key, $this->object->Variables)) {
                $this->object->{$key} = $value;
            }
        }
        if(isset($parse_array["Password"])) {
            if($parse_array["Password"] == "") {
                $this->object->OldPassword = "";
                $this->object->OneTimePasswordValidTill = "";
            } else {
                $this->object->Password = passcrypt($this->object->Password);
                $this->object->OneTimePasswordValidTill = debtor::setOneTimePasswordValidTill();
            }
        }
        $this->object->Country = str_replace("EU-", "", strtoupper($this->object->Country));
        if(isset($parse_array["InvoiceCountry"]) && $parse_array["InvoiceCountry"]) {
            $this->object->InvoiceCountry = str_replace("EU-", "", strtoupper($parse_array["InvoiceCountry"]));
        } elseif(isset($parse_array["Country"]) && $parse_array["Country"] && !$this->object->InvoiceAddress) {
            $this->object->InvoiceCountry = $this->object->Country;
        }
        $newGroups = $this->_checkDebtorGroups($parse_array);
        $this->object->Groups = !isset($parse_array["Groups"]) ? $this->object->Groups : $newGroups;
        if(isset($parse_array["SynchronizeHandles"]) && $parse_array["SynchronizeHandles"] == "yes") {
            require_once "class/handle.php";
            $handle = new handle();
            $matched_handles = $handle->lookupDebtorHandle($this->object->Identifier);
        }
        if(isset($parse_array["CustomFields"])) {
            foreach ($parse_array["CustomFields"] as $_custom_field => $_custom_value) {
                $this->object->customvalues[$_custom_field] = $_custom_value;
            }
        }
        if(defined("API_CALL_FROM_CLIENTAREA") && API_CALL_FROM_CLIENTAREA === true) {
            foreach (["TwoFactorAuthentication", "TokenData"] as $field) {
                $value = HostFact_API::getRequestParameter($field);
                if($value !== false) {
                    $this->object->{$field} = $value;
                }
            }
        }
        if(defined("API_CALL_FROM_CLIENTAREA") && API_CALL_FROM_CLIENTAREA === true && (isset($parse_array["CompanyName"]) || isset($parse_array["InvoiceCompanyName"]) || isset($parse_array["AccountNumber"]))) {
            require_once "class/clientareaprofiles.php";
            $ClientareaProfiles = new ClientareaProfiles_Model();
            if($this->object->ClientareaProfile && 0 < $this->object->ClientareaProfile) {
                $ClientareaProfiles->id = $this->object->ClientareaProfile;
                $ClientareaProfiles->show();
            } else {
                $ClientareaProfiles->showDefault();
            }
            require_once "class/clientareachange.php";
            $ClientareaChanges = new ClientareaChange_Model();
            if(isset($parse_array["CompanyName"])) {
                $action = "editgeneral";
            } elseif(isset($parse_array["InvoiceCompanyName"])) {
                $action = "editbilling";
            } elseif(isset($parse_array["AccountNumber"])) {
                $action = "editpayment";
            }
            $ip = HostFact_API::getRequestParameter("IPAddress") ? HostFact_API::getRequestParameter("IPAddress") : "";
            $options = [];
            $options["filters"]["reference_type"] = "debtor";
            $options["filters"]["reference_id"] = $this->object->Identifier;
            $options["filters"]["action"] = $action;
            $options["filters"]["debtor"] = $this->object->Identifier;
            $options["filter"] = "pending";
            $changes_result = $ClientareaChanges->listChanges($options);
            if($changes_result && 0 < count($changes_result)) {
                $changes_result = end($changes_result);
                $ClientareaChanges->id = $changes_result->id;
            }
            $approval = "notused";
            if($action == "editgeneral" || $action == "editbilling") {
                $approval = $ClientareaProfiles->Rights["CLIENTAREA_DEBTOR_DATA_CHANGE"] == "approve" ? "pending" : "notused";
            } elseif($action == "editpayment") {
                $approval = $ClientareaProfiles->Rights["CLIENTAREA_DEBTOR_PAYMENTDATA_CHANGE"] == "approve" ? "pending" : "notused";
            }
            $ClientareaChanges->ReferenceType = "debtor";
            $ClientareaChanges->ReferenceID = $this->object->Identifier;
            $ClientareaChanges->Action = $action;
            $ClientareaChanges->Data = $parse_array;
            $ClientareaChanges->Debtor = $this->object->Identifier;
            $ClientareaChanges->Approval = $approval;
            $ClientareaChanges->Status = "pending";
            $ClientareaChanges->CreatorType = "debtor";
            $ClientareaChanges->CreatorID = $this->object->Identifier;
            $ClientareaChanges->IP = $ip;
            $result = isset($ClientareaChanges->id) && $ClientareaChanges->id ? $ClientareaChanges->edit() : $ClientareaChanges->add();
            if($result) {
                if($ClientareaChanges->Approval == "notused") {
                    $ClientareaChanges->execute($ClientareaChanges->id);
                }
                HostFact_API::parseSuccess($ClientareaChanges->Success);
            } else {
                HostFact_API::parseError($ClientareaChanges->Error, true);
            }
        } elseif($this->object->edit()) {
            if(isset($parse_array["SynchronizeHandles"]) && $parse_array["SynchronizeHandles"] == "yes") {
                $handle->syncDebtorToHandle($this->object->Identifier, $matched_handles);
            }
            if(isset($send_welcome) && $send_welcome == "yes") {
                $this->object->sentWelcome();
            }
            HostFact_API::parseSuccess($this->object->Success);
            $show_debtor = true;
        } else {
            HostFact_API::parseError($this->object->Error, true);
        }
        if(HostFact_API::getRequestParameter("SendNotification") == "yes") {
            if(!isset($ClientareaProfiles)) {
                require_once "class/clientareaprofiles.php";
                $ClientareaProfiles = new ClientareaProfiles_Model();
                if($this->object->ClientareaProfile && 0 < $this->object->ClientareaProfile) {
                    $ClientareaProfiles->id = $this->object->ClientareaProfile;
                    $ClientareaProfiles->show();
                } else {
                    $ClientareaProfiles->showDefault();
                }
            }
            $this->object->ClientareaProfileObject = $ClientareaProfiles;
            require_once "class/email.php";
            $email = new email();
            $email->Sender = getFirstMailAddress(CLIENTAREA_NOTIFICATION_EMAILADDRESS);
            $email->Recipient = CLIENTAREA_NOTIFICATION_EMAILADDRESS;
            $email->Subject = __("subject debtor " . $action . " changed");
            $email->Message = file_get_contents("includes/language/" . LANGUAGE . "/mail.debtor." . $action . ".changed.phtml");
            $email->Debtor = $this->object->Identifier;
            $email->add(["debtorOld" => $oldDebtor, "debtorChanged" => normalize($this->object)]);
            $email->Debtor = 0;
            $email->sent();
        }
        if(isset($show_debtor) && $show_debtor === true) {
            return $this->_show_debtor($this->object->Identifier);
        }
        HostFact_API::parseResponse();
    }
    public function checklogin_api_action()
    {
        if(defined("API_CALL_FROM_CLIENTAREA") && API_CALL_FROM_CLIENTAREA === true && HostFact_API::getRequestParameter("DebtorKey") && HostFact_API::getRequestParameter("DebtorID") && HostFact_API::getRequestParameter("CustomerPanelKey")) {
            $DebtorKey = HostFact_API::getRequestParameter("DebtorKey");
            $DebtorID = HostFact_API::getRequestParameter("DebtorID");
            $this->object->CustomerPanelKey = HostFact_API::getRequestParameter("CustomerPanelKey");
            if($debtor_id = $this->object->getByCustomerPanelKey($DebtorID, $DebtorKey)) {
                $this->object->Identifier = $debtor_id;
                $this->object->setCustomerPanelKey("");
                return $this->_show_debtor($debtor_id);
            }
            $this->object->Identifier = $DebtorID;
            $this->object->setCustomerPanelKey("");
            HostFact_API::parseError([__("Debtor not found")], true);
        } else {
            $Username = HostFact_API::getRequestParameter("Username");
            $Password = HostFact_API::getRequestParameter("Password");
            $debtor = $this->object;
            if(0 < strlen($Username) || 0 < strlen($Password)) {
                $debtor->Username = esc($Username);
                $debtor->Password = esc($Password);
                $debtor->Identifier = $debtor->get_id();
                if(0 < $debtor->Identifier) {
                    return $this->_show_debtor($debtor->Identifier);
                }
            }
            HostFact_API::parseError([sprintf(__("Debtor not found"), $Username)], true);
        }
    }
    public function updatelogincredentials_api_action()
    {
        $Username = HostFact_API::getRequestParameter("Username");
        $EmailAddress = HostFact_API::getRequestParameter("EmailAddress");
        $IPAddress = HostFact_API::getRequestParameter("IPAddress");
        if(strlen($Username) === 0 || strlen($EmailAddress) === 0) {
            HostFact_API::parseError([sprintf(__("Debtor not found"), $Username)], true);
        }
        $debtor = $this->object;
        $debtor->Identifier = $debtor->getID("username-email", ["Username" => esc($Username), "EmailAddress" => esc($EmailAddress)]);
        if(!$debtor->show()) {
            if(defined("API_CALL_FROM_CLIENTAREA") && API_CALL_FROM_CLIENTAREA) {
                logFailedLoginAttempt("clientarea", $IPAddress, $Username);
            }
            HostFact_API::parseError([sprintf(__("Debtor not found"), $Username)], true);
        }
        if(defined("API_CALL_FROM_CLIENTAREA") && API_CALL_FROM_CLIENTAREA) {
            clearFailedLoginAttempts($IPAddress, false, "clientarea");
        }
        $NewUsername = HostFact_API::getRequestParameter("NewUsername");
        if($NewUsername === false) {
            $NewUsername = $debtor->Username;
        }
        $Password = HostFact_API::getRequestParameter("Password");
        if($Password === false) {
            $Password = $debtor->Password;
        }
        $SendEmail = HostFact_API::getRequestParameter("SendPasswordForgotEmail");
        $SendEmail = $SendEmail !== false && $SendEmail == "yes" ? true : false;
        if($debtor->updateLoginDetails($NewUsername, $Password, $SendEmail)) {
            HostFact_API::parseSuccess($debtor->Success);
            return $this->_show_debtor($debtor->Identifier);
        }
        HostFact_API::parseError($debtor->Error, true);
    }
    public function generatepdf_api_action()
    {
        $this->object->Identifier = $this->_get_debtor_id(HostFact_API::getRequestParameter("Identifier"));
        $_template_id = HostFact_API::getRequestParameter("TemplateID");
        $_service_id = HostFact_API::getRequestParameter("ServiceID");
        $_service_type = HostFact_API::getRequestParameter("ServiceType");
        $parse_array = $this->getValidParameters();
        if(!isset($_template_id)) {
            HostFact_API::parseError([__("invalid template")], true);
        }
        require_once "class/template.php";
        $template = new template();
        $template->Identifier = $_template_id;
        if(!$template->show()) {
            HostFact_API::parseError([__("invalid template")], true);
        }
        if($template->Type != "other") {
            HostFact_API::parseError([__("template is not of type other")], true);
        }
        if(!$this->object->show()) {
            HostFact_API::parseError($this->object->Error, true);
        }
        $OutputType = "D";
        $type = "";
        require_once "class/pdf.php";
        $objects = [];
        $objects["debtor"] = $this->object;
        if(isset($_service_id) && 0 < $_service_id) {
            switch ($_service_type) {
                case "domain":
                    require_once "class/domain.php";
                    $domain = new domain();
                    $domain->Identifier = $_service_id;
                    if(!$domain->show()) {
                        HostFact_API::parseError([__("invalid identifier for domain")], true);
                    }
                    if($domain->Debtor != $this->object->Identifier) {
                        HostFact_API::parseError([__("debtor does not match service debtor")], true);
                    }
                    $objects["domain"] = $domain;
                    if(0 < $domain->HostingID) {
                        require_once "class/hosting.php";
                        $hosting = new hosting();
                        $hosting->Identifier = $domain->HostingID;
                        if($hosting->show()) {
                            $objects["hosting"] = $hosting;
                        }
                    }
                    break;
                case "hosting":
                    require_once "class/hosting.php";
                    $hosting = new hosting();
                    $hosting->Identifier = $_service_id;
                    if(!$hosting->show()) {
                        HostFact_API::parseError([__("invalid identifier for hosting")], true);
                    }
                    if($hosting->Debtor != $this->object->Identifier) {
                        HostFact_API::parseError([__("debtor does not match service debtor")], true);
                    }
                    $objects["hosting"] = $hosting;
                    if(0 < $hosting->Server) {
                        require_once "class/server.php";
                        $server = new server();
                        $server->Identifier = $hosting->Server;
                        if($server->show()) {
                            $objects["server"] = $server;
                        }
                    }
                    if($hosting->Domain) {
                        $sld = substr($hosting->Domain, 0, strpos($hosting->Domain, "."));
                        $tld = substr(stristr($hosting->Domain, "."), 1);
                        require_once "class/domain.php";
                        $domain = new domain();
                        $domain->Identifier = $domain->getID("domain", $sld, $tld);
                        if($domain->show()) {
                            $objects["domain"] = $domain;
                        }
                    }
                    break;
            }
        }
        error_reporting(0);
        $OutputType = "F";
        require_once "class/pdf.php";
        $pdf = new pdfCreator($_template_id, $objects, "other", "D", true);
        $pdf->setOutputType($OutputType);
        if(!$pdf->generatePDF("F")) {
            HostFact_API::parseError($pdf->Error, true);
        }
        $handle = fopen("temp/" . $pdf->Name, "r");
        $filedata = fread($handle, filesize("temp/" . $pdf->Name));
        fclose($handle);
        @unlink("temp/" . $pdf->Name);
        $result = [];
        $result["Filename"] = $pdf->Name;
        $result["Base64"] = base64_encode($filedata);
        return HostFact_API::parseResponse($result);
    }
    public function sendemail_api_action()
    {
        $this->object->Identifier = $this->_get_debtor_id(HostFact_API::getRequestParameter("Identifier"));
        if(!$this->object->show()) {
            HostFact_API::parseError($this->object->Error, true);
        }
        $objects = ["debtor" => $this->object];
        global $additional_product_types;
        $references = HostFact_API::getRequestParameter("References");
        if(is_array($references)) {
            foreach ($references as $_type => $_id) {
                $_id = intval($_id);
                if($_type == "invoice") {
                    require_once "class/invoice.php";
                    $invoice = new invoice();
                    $invoice->Identifier = $_id;
                    if($invoice->show() && $invoice->Debtor == $this->object->Identifier) {
                        $objects["invoice"] = $invoice;
                    }
                } elseif($_type == "pricequote") {
                    require_once "class/pricequote.php";
                    $pricequote = new pricequote();
                    $pricequote->Identifier = $_id;
                    if($pricequote->show() && $pricequote->Debtor == $this->object->Identifier) {
                        $objects["pricequote"] = $pricequote;
                    }
                } elseif(in_array($_type, ["domain", "hosting"]) || isset($additional_product_types[$_type])) {
                    require_once "class/service.php";
                    $service = new service();
                    if($service->show($_id, $_type) && $service->Debtor == $this->object->Identifier) {
                        if(isset($service->{$_type})) {
                            $objects[$_type] = $service->{$_type};
                        }
                    }
                }
                $this->Error[] = sprintf(__("api debtor sendemail - reference not found"), $_type, $_id, $this->object->DebtorCode);
            }
        }
        if(isset($objects["invoice"]) && isset($objects["pricequote"])) {
            $this->Error[] = __("api debtor sendemail - not possible to add invoice and pricequote");
        }
        if(!empty($this->Error)) {
            HostFact_API::parseError($this->Error, true);
        }
        require_once "class/email.php";
        $email = new email();
        $template_id = intval(HostFact_API::getRequestParameter("TemplateID"));
        if($template_id && 0 < $template_id) {
            require_once "class/template.php";
            $emailtemplate = new emailtemplate();
            $emailtemplate->Identifier = HostFact_API::getRequestParameter("TemplateID");
            $emailtemplate->show();
            foreach ($emailtemplate->Variables as $v) {
                if(is_string($emailtemplate->{$v})) {
                    $email->{$v} = htmlspecialchars_decode($emailtemplate->{$v});
                } else {
                    $email->{$v} = $emailtemplate->{$v};
                }
            }
        }
        $email->Recipient = $this->object->EmailAddress;
        if((HostFact_API::getRequestParameter("SenderName") || HostFact_API::getRequestParameter("SenderEmail")) && preg_match("/(.*)<(.*)>/i", $email->Sender, $matches)) {
            $email->SenderName = HostFact_API::getRequestParameter("SenderName") ? HostFact_API::getRequestParameter("SenderName") : $matches[1];
            $email->SenderEmail = HostFact_API::getRequestParameter("SenderEmail") ? HostFact_API::getRequestParameter("SenderEmail") : $matches[2];
            $email->Sender = $email->SenderName . "<" . $email->SenderEmail . ">";
        }
        foreach (["Recipient", "Subject", "Message"] as $_field) {
            if(HostFact_API::getRequestParameter($_field) !== false) {
                $email->{$_field} = HostFact_API::getRequestParameter($_field);
            }
        }
        if(HostFact_API::getRequestParameter("CC") !== false) {
            $email->CarbonCopy = HostFact_API::getRequestParameter("CC");
        }
        if(HostFact_API::getRequestParameter("BCC") !== false) {
            $email->BlindCarbonCopy = HostFact_API::getRequestParameter("BCC");
        }
        if(HostFact_API::getRequestParameter("SkipDefaultBCC") == "yes") {
            $email->Sent_bcc = false;
        }
        $email->Debtor = $this->object->Identifier;
        Database_Model::getInstance()->beginTransaction();
        $email->add($objects);
        $email_sent = $email->sent(false, false, true, $objects);
        Database_Model::getInstance()->commit();
        if($email_sent) {
            HostFact_API::parseResponse($email->Success, true);
        } else {
            HostFact_API::parseError($email->Error, true);
        }
    }
    protected function getFilterValues()
    {
        $filters = parent::getFilterValues();
        if(isset($filters["searchat"]) && $filters["searchat"] == "CustomFields") {
            HostFact_API::parseError("Invalid filter 'searchat'. Please enter valid columns or remove filter", true);
        }
        if(isset($filters["group"])) {
            if(0 < $filters["group"]) {
                $result = Database_Model::getInstance()->getOne("HostFact_Group", "id")->where("Type", "debtor")->where("Status", "1")->where("id", $filters["group"])->execute();
                if(!$result) {
                    HostFact_API::parseError("Invalid filter 'debtorgroup'. The debtor group does not exist", true);
                }
            } else {
                $filters["group"] = "";
                unset($this->_valid_filter_input["group"]);
            }
        }
        return $filters;
    }
    private function _checkDebtorGroups($parse_array)
    {
        $debtorGroups = [];
        if(!empty($parse_array["Groups"])) {
            $newGroups = $parse_array["Groups"];
            $debtor = $this->object;
            foreach ($newGroups as $key) {
                $id = $debtor->_checkGroup($key);
                if($id !== false) {
                    $debtorGroups[] = $id;
                } else {
                    HostFact_API::parseError([sprintf(__("api debtor group not found"), esc($key))], true);
                }
            }
        }
        return $debtorGroups;
    }
    private function _show_debtor($debtor_id)
    {
        $debtor = $this->object;
        $debtor->Identifier = $debtor_id;
        if(!$debtor->show()) {
            HostFact_API::parseError($debtor->Error, true);
        }
        $debtor->Password = passcrypt($debtor->Password);
        $debtor->Taxable = $debtor->TaxableSetting;
        $result = [];
        foreach ($this->_object_parameters as $field => $field_info) {
            if(!isset($debtor->{$field})) {
            } elseif(!(empty($debtor->Server) && in_array($field, ["Server", "DNS1", "DNS2", "DNS3"]))) {
                $result[$field] = is_string($debtor->{$field}) ? htmlspecialchars_decode($debtor->{$field}) : $debtor->{$field};
            }
        }
        if(isset($this->_object_parameters["CustomFields"])) {
            $result["CustomFields"] = $debtor->customvalues;
        }
        $get_password = HostFact_API::getRequestParameter("GetPassword");
        if($get_password != "yes") {
            unset($result["Password"]);
        }
        global $array_legaltype;
        global $array_country;
        global $array_invoicemethod;
        global $array_taxable;
        global $array_customer_languages;
        $result["Translations"] = ["LegalForm" => isset($array_legaltype[$debtor->LegalForm]) ? $array_legaltype[$debtor->LegalForm] : "", "State" => isset($debtor->StateName) ? $debtor->StateName : "", "Country" => isset($array_country[$debtor->Country]) ? $array_country[$debtor->Country] : "", "InvoiceMethod" => isset($array_invoicemethod[$debtor->InvoiceMethod]) ? $array_invoicemethod[$debtor->InvoiceMethod] : "", "InvoiceState" => isset($debtor->InvoiceStateName) ? $debtor->InvoiceStateName : "", "InvoiceCountry" => isset($array_country[$debtor->InvoiceCountry]) ? $array_country[$debtor->InvoiceCountry] : "", "Taxable" => isset($array_taxable[$debtor->Taxable]) ? $array_taxable[$debtor->Taxable] : "", "DefaultLanguage" => isset($array_customer_languages[$debtor->DefaultLanguage]) ? $array_customer_languages[$debtor->DefaultLanguage] : ""];
        if(!defined("IS_INTERNATIONAL") || IS_INTERNATIONAL !== true) {
            unset($result["Translations"]["State"]);
            unset($result["Translations"]["InvoiceState"]);
        }
        $result = $this->_show_modifications($result, "debtor", $debtor->Identifier, $debtor->Identifier);
        if(defined("API_CALL_FROM_CLIENTAREA") && API_CALL_FROM_CLIENTAREA) {
            $result = $this->_clientarea_settings($result, $debtor->ClientareaProfile);
            $result["TwoFactorAuthentication"] = $debtor->TwoFactorAuthentication;
            $result["TokenData"] = $debtor->TokenData;
        }
        $result["Created"] = $this->_filter_date_db2api($debtor->Created, false);
        $result["Modified"] = $this->_filter_date_db2api($debtor->Modified, false);
        return HostFact_API::parseResponse($result);
    }
    private function _clientarea_settings($result, $clientarea_profile)
    {
        require_once "class/clientareaprofiles.php";
        $ClientareaProfiles_Model = new ClientareaProfiles_Model();
        if($clientarea_profile && 0 < $clientarea_profile) {
            $ClientareaProfiles_Model->id = $clientarea_profile;
            $profile_result = $ClientareaProfiles_Model->show();
        }
        if((int) $clientarea_profile === 0 || $profile_result === false || $ClientareaProfiles_Model->Status != "active") {
            $ClientareaProfiles_Model->showDefault();
        }
        if($ClientareaProfiles_Model) {
            $result["ClientareaSettings"]["WelcomeTitle"] = $ClientareaProfiles_Model->WelcomeTitle;
            $result["ClientareaSettings"]["WelcomeMessage"] = $ClientareaProfiles_Model->WelcomeMessage;
            $result["ClientareaSettings"]["Rights"] = $ClientareaProfiles_Model->Rights;
            $result["ClientareaSettings"]["Orderforms"] = $ClientareaProfiles_Model->Orderforms;
            $result["ClientareaSettings"]["TwoFactorAuthentication"] = $ClientareaProfiles_Model->TwoFactorAuthentication;
        }
        return $result;
    }
}

?>