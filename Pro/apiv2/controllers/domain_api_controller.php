<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "subscription_api_controller.php";
class domain_api_controller extends subscription_api_controller
{
    public $show_hide_auth_key;
    public function __construct()
    {
        parent::__construct();
        HostFact_API::setObjectNames("domains", "domain");
        $this->show_hide_auth_key = "hide";
        require_once "class/domain.php";
        $this->addParameter("Identifier", "int");
        $this->addParameter("Domain", "string");
        $this->addParameter("Tld", "string");
        $this->addParameter("Debtor", "int");
        $this->addParameter("DebtorCode", "string");
        $this->addParameter("HostingID", "int");
        $this->addParameter("DirectServerCreation", "string");
        $this->addParameter("Status", "default_int");
        $this->addParameter("RegistrationDate", "date");
        $this->addParameter("ExpirationDate", "date");
        $this->addParameter("AuthKey", "string");
        $this->addParameter("Registrar", "int");
        $this->addParameter("DNS1", "string");
        $this->addParameter("DNS2", "string");
        $this->addParameter("DNS3", "string");
        $this->addParameter("DNS1IP", "string");
        $this->addParameter("DNS2IP", "string");
        $this->addParameter("DNS3IP", "string");
        $this->addParameter("DNSTemplate", "int");
        $this->addParameter("DNSZone", "array_raw");
        $this->addParameter("OwnerHandle", "int");
        $this->addParameter("AdminHandle", "int");
        $this->addParameter("TechHandle", "int");
        $this->addParameter("Owner", "array_with_keys");
        $this->addSubParameter("Owner", "CompanyName", "string");
        $this->addSubParameter("Owner", "CompanyNumber", "string");
        $this->addSubParameter("Owner", "LegalForm", "string");
        $this->addSubParameter("Owner", "TaxNumber", "string");
        $this->addSubParameter("Owner", "Sex", "string");
        $this->addSubParameter("Owner", "Initials", "string");
        $this->addSubParameter("Owner", "SurName", "string");
        $this->addSubParameter("Owner", "Address", "string");
        if(defined("IS_INTERNATIONAL") && IS_INTERNATIONAL === true) {
            $this->addSubParameter("Owner", "Address2", "string");
        }
        $this->addSubParameter("Owner", "ZipCode", "string");
        $this->addSubParameter("Owner", "City", "string");
        if(defined("IS_INTERNATIONAL") && IS_INTERNATIONAL === true) {
            $this->addSubParameter("Owner", "State", "string");
        }
        $this->addSubParameter("Owner", "Country", "string");
        $this->addSubParameter("Owner", "EmailAddress", "string");
        $this->addSubParameter("Owner", "PhoneNumber", "string");
        $this->addSubParameter("Owner", "FaxNumber", "string");
        $this->addParameter("Admin", "array_with_keys");
        $this->addSubParameter("Admin", "CompanyName", "string");
        $this->addSubParameter("Admin", "CompanyNumber", "string");
        $this->addSubParameter("Admin", "LegalForm", "string");
        $this->addSubParameter("Admin", "TaxNumber", "string");
        $this->addSubParameter("Admin", "Sex", "string");
        $this->addSubParameter("Admin", "Initials", "string");
        $this->addSubParameter("Admin", "SurName", "string");
        $this->addSubParameter("Admin", "Address", "string");
        if(defined("IS_INTERNATIONAL") && IS_INTERNATIONAL === true) {
            $this->addSubParameter("Admin", "Address2", "string");
        }
        $this->addSubParameter("Admin", "ZipCode", "string");
        $this->addSubParameter("Admin", "City", "string");
        if(defined("IS_INTERNATIONAL") && IS_INTERNATIONAL === true) {
            $this->addSubParameter("Admin", "State", "string");
        }
        $this->addSubParameter("Admin", "Country", "string");
        $this->addSubParameter("Admin", "EmailAddress", "string");
        $this->addSubParameter("Admin", "PhoneNumber", "string");
        $this->addSubParameter("Admin", "FaxNumber", "string");
        $this->addParameter("Tech", "array_with_keys");
        $this->addSubParameter("Tech", "CompanyName", "string");
        $this->addSubParameter("Tech", "CompanyNumber", "string");
        $this->addSubParameter("Tech", "LegalForm", "string");
        $this->addSubParameter("Tech", "TaxNumber", "string");
        $this->addSubParameter("Tech", "Sex", "string");
        $this->addSubParameter("Tech", "Initials", "string");
        $this->addSubParameter("Tech", "SurName", "string");
        $this->addSubParameter("Tech", "Address", "string");
        if(defined("IS_INTERNATIONAL") && IS_INTERNATIONAL === true) {
            $this->addSubParameter("Tech", "Address2", "string");
        }
        $this->addSubParameter("Tech", "ZipCode", "string");
        $this->addSubParameter("Tech", "City", "string");
        if(defined("IS_INTERNATIONAL") && IS_INTERNATIONAL === true) {
            $this->addSubParameter("Tech", "State", "string");
        }
        $this->addSubParameter("Tech", "Country", "string");
        $this->addSubParameter("Tech", "EmailAddress", "string");
        $this->addSubParameter("Tech", "PhoneNumber", "string");
        $this->addSubParameter("Tech", "FaxNumber", "string");
        $this->addParameter("DomainAutoRenew", "readonly");
        $this->addParameter("Comment", "text");
        $this->addParameter("Created", "readonly");
        $this->addParameter("Modified", "readonly");
        $this->addFilter("status", "string", "");
        $this->addFilter("created", "filter_datetime");
        $this->addFilter("modified", "filter_datetime");
        $this->object = new domain();
    }
    public function list_api_action()
    {
        $this->_extra_search_filter = ["ProductCode", "Description"];
        $filters = $this->getFilterValues();
        $fields = ["Domain", "Tld", "Debtor", "DebtorCode", "CompanyName", "SurName", "Initials", "RegistrationDate", "ExpirationDate", "Status", "Registrar", "Name", "HostingID", "PeriodicID", "ProductCode", "Description", "Number", "PriceExcl", "TaxPercentage", "DiscountPercentage", "Periods", "Periodic", "NextDate", "StartPeriod", "EndPeriod", "TerminationDate", "Modified"];
        $sort = $filters["sort"] ? $filters["sort"] : "Domain";
        $page_offset = $filters["offset"] / $filters["limit"] + 1;
        $searchat = $filters["searchat"] ? $filters["searchat"] : "Domain";
        $limit = $filters["limit"];
        if(defined("API_CALL_FROM_CLIENTAREA") && API_CALL_FROM_CLIENTAREA === true) {
            if(!isset($filters["status"]) || $filters["status"] == "" || strpos($filters["status"], "9") !== false) {
                HostFact_API::parseError("Unauthorized request", true);
            }
            $fields[] = "Termination.id";
            $fields[] = "Termination.Date";
            $fields[] = "Termination.Created";
            $fields[] = "Termination.Status";
        }
        $field_filters = array_diff_key($filters, array_flip(["offset", "limit", "sort", "order", "searchat", "searchfor"]));
        $domain_list = $this->object->all($fields, $sort, $filters["order"], $page_offset, $searchat, $filters["searchfor"], $field_filters, $limit);
        $array = [];
        foreach ($domain_list as $key => $value) {
            if($key != "CountRows" && is_numeric($key)) {
                $line_amount = getLineAmount(VAT_CALC_METHOD, $value["PriceExcl"], $value["Periods"], $value["Number"], $value["TaxPercentage"], $value["DiscountPercentage"]);
                $value["AmountIncl"] = $line_amount["incl"];
                $value["AmountExcl"] = $line_amount["excl"];
                $param = ["Identifier" => $value["id"], "Debtor" => $value["Debtor"], "DebtorCode" => htmlspecialchars_decode($value["DebtorCode"]), "CompanyName" => htmlspecialchars_decode($value["CompanyName"]), "Initials" => htmlspecialchars_decode($value["Initials"]), "SurName" => htmlspecialchars_decode($value["SurName"]), "Domain" => htmlspecialchars_decode($value["Domain"]), "Tld" => htmlspecialchars_decode($value["Tld"]), "RegistrationDate" => $this->_filter_date_db2api($value["RegistrationDate"]), "ExpirationDate" => $this->_filter_date_db2api($value["ExpirationDate"]), "Status" => $value["Status"], "Registrar" => $value["Registrar"], "RegistrarName" => htmlspecialchars_decode($value["Name"]), "HostingID" => $value["HostingID"], "Subscription" => ["ProductCode" => $value["ProductCode"], "Description" => htmlspecialchars_decode($value["Description"]), "Number" => htmlspecialchars_decode($value["Number"]), "PriceExcl" => $value["PriceExcl"], "TaxPercentage" => $value["TaxPercentage"] * 100, "DiscountPercentage" => $value["DiscountPercentage"] * 100, "Periods" => $value["Periods"], "Periodic" => htmlspecialchars_decode($value["Periodic"]), "StartPeriod" => $this->_filter_date_db2api($value["StartPeriod"]), "EndPeriod" => $this->_filter_date_db2api($value["EndPeriod"]), "NextDate" => $this->_filter_date_db2api($value["NextDate"]), "TerminationDate" => $this->_filter_date_db2api($value["TerminationDate"]), "AmountExcl" => $value["AmountExcl"], "AmountIncl" => $value["AmountIncl"]], "Modified" => $this->_filter_date_db2api($value["Modified"], false)];
                if(empty($value["PeriodicID"])) {
                    unset($param["Subscription"]);
                }
                if(isset($value["Termination.id"]) && 0 < $value["Termination.id"]) {
                    $param["Termination"] = ["Date" => $value["Termination.Date"], "Created" => $value["Termination.Created"], "Status" => $value["Termination.Status"]];
                }
                $array[] = $param;
            }
        }
        HostFact_API::setMetaData("totalresults", $domain_list["CountRows"]);
        HostFact_API::setMetaData("currentresults", count($array));
        HostFact_API::setMetaData("offset", $filters["offset"]);
        if(!empty($this->_valid_filter_input)) {
            HostFact_API::setMetaData("filters", $this->_valid_filter_input);
        }
        HostFact_API::parseResponse($array, true);
    }
    public function show_api_action()
    {
        $domain_id = $this->_get_domain_id();
        $this->_show_domain($domain_id);
    }
    public function add_api_action()
    {
        $parse_array = $this->getValidParameters();
        if(array_key_exists("DNSTemplate", $parse_array) && is_module_active("dnsmanagement") === false) {
            HostFact_API::parseError(sprintf(__("parameters not available, module not active"), "DNSTemplate", __("module type dnsmanagement")), true);
        }
        HostFact_API::beginTransaction();
        $domain = $this->object;
        require_once "class/service.php";
        $service = new service();
        $parse_array["Debtor"] = $this->_get_debtor_id();
        $inputSubscription = $this->_updateSubscriptionParameters($parse_array, $service);
        $inputSubscription = $this->_add_domain_to_description($inputSubscription, $parse_array, $parse_array["Domain"], $parse_array["Tld"]);
        if(!isset($parse_array["Registrar"])) {
            require_once "class/topleveldomain.php";
            $topleveldomain = new topleveldomain();
            if($topleveldomain->showbyTLD($parse_array["Tld"])) {
                if(empty($topleveldomain->Registrar)) {
                    HostFact_API::parseError(__("invalid identifier for registrar"), true);
                } else {
                    $parse_array["Registrar"] = $topleveldomain->Registrar;
                }
            } else {
                HostFact_API::parseError($topleveldomain->Error, true);
            }
        }
        $input = ["PeriodicType" => "domain", "Debtor" => $parse_array["Debtor"], "domain" => ["Domain" => isset($parse_array["Domain"]) ? $parse_array["Domain"] : "", "Tld" => isset($parse_array["Tld"]) ? $parse_array["Tld"] : "", "Status" => isset($parse_array["Status"]) ? $parse_array["Status"] : 1, "HostingID" => isset($parse_array["HostingID"]) ? $parse_array["HostingID"] : 0, "RegistrationDate" => isset($parse_array["RegistrationDate"]) ? rewrite_date_db2site($parse_array["RegistrationDate"]) : "", "ExpirationDate" => isset($parse_array["ExpirationDate"]) ? rewrite_date_db2site($parse_array["ExpirationDate"]) : "", "AuthKey" => isset($parse_array["AuthKey"]) ? $parse_array["AuthKey"] : "", "Registrar" => isset($parse_array["Registrar"]) ? $parse_array["Registrar"] : 0, "Comment" => isset($parse_array["Comment"]) ? $parse_array["Comment"] : "", "DirectServerCreation" => isset($parse_array["DirectServerCreation"]) && $parse_array["DirectServerCreation"] == "yes" ? "yes" : false], "subscription_invoice" => isset($parse_array["HasSubscription"]) && $parse_array["HasSubscription"] == "yes" ? "yes" : "", "SubscriptionType" => "new"];
        $input = $this->_setDNSAndHandles($input, $parse_array, $service);
        if(array_key_exists("DNSTemplate", $parse_array)) {
            $input["domain"]["DNSTemplate"] = $parse_array["DNSTemplate"];
            $_POST["domain"]["DNSTemplate"] = $parse_array["DNSTemplate"];
        }
        if($service->add(array_merge($input, $inputSubscription))) {
            HostFact_API::commit();
            if(count($service->domain->Warning)) {
                HostFact_API::parseWarning($service->domain->Warning);
            }
            if(isset($parse_array["AuthKey"])) {
                $this->show_hide_auth_key = "show";
            }
            $this->_show_domain($service->domain->Identifier);
            HostFact_API::parseSuccess($domain->Success, true);
        } else {
            $Errors = array_unique(array_merge($service->Error, $service->domain->Error, $service->Subscription->Error));
            HostFact_API::parseError($Errors, true);
        }
    }
    public function _setDNSAndHandles($input, $parse_array, $service)
    {
        $parse_array["OwnerHandle"] = isset($parse_array["OwnerHandle"]) ? $parse_array["OwnerHandle"] : $service->domain->ownerHandle;
        $parse_array["AdminHandle"] = isset($parse_array["AdminHandle"]) ? $parse_array["AdminHandle"] : $service->domain->adminHandle;
        $parse_array["TechHandle"] = isset($parse_array["TechHandle"]) ? $parse_array["TechHandle"] : $service->domain->techHandle;
        require_once "class/registrar.php";
        $registrar = new registrar();
        $registrar->Identifier = $input["domain"]["Registrar"];
        if(!$registrar->show()) {
            HostFact_API::parseError(__("invalid identifier for registrar"), true);
        }
        $input["domain"]["ownerc_id"] = 0;
        if(isset($parse_array["OwnerHandle"]) && 0 < $parse_array["OwnerHandle"]) {
            $input["domain"]["ownerc"] = "custom";
            $input["domain"]["ownerHandle"] = $parse_array["OwnerHandle"];
        } else {
            $input["domain"]["ownerc"] = "debtor";
        }
        $input["domain"]["adminc_id"] = 0;
        if(isset($parse_array["AdminHandle"]) && 0 < $parse_array["AdminHandle"]) {
            $input["domain"]["adminc"] = "custom";
            $input["domain"]["adminHandle"] = $parse_array["AdminHandle"];
        } elseif(0 < $registrar->AdminHandle) {
            $input["domain"]["adminc"] = "handle";
            $input["domain"]["adminc_id"] = $registrar->AdminHandle;
        } else {
            $input["domain"]["adminc"] = "owner";
        }
        $input["domain"]["techc_id"] = 0;
        if(isset($parse_array["TechHandle"]) && 0 < $parse_array["TechHandle"]) {
            $input["domain"]["techc"] = "custom";
            $input["domain"]["techHandle"] = $parse_array["TechHandle"];
        } elseif(0 < $registrar->TechHandle) {
            $input["domain"]["techc"] = "handle";
            $input["domain"]["techc_id"] = $registrar->TechHandle;
        } else {
            $input["domain"]["techc"] = "owner";
        }
        $ownerHandleId = isset($parse_array["OwnerHandle"]) && 0 < $parse_array["OwnerHandle"] ? $parse_array["OwnerHandle"] : $input["domain"]["ownerc_id"];
        $adminHandleId = isset($parse_array["AdminHandle"]) && 0 < $parse_array["AdminHandle"] ? $parse_array["AdminHandle"] : $input["domain"]["adminc_id"];
        $techHandleId = isset($parse_array["TechHandle"]) && 0 < $parse_array["TechHandle"] ? $parse_array["TechHandle"] : $input["domain"]["techc_id"];
        $this->_validateHandleIDs(array_unique([$ownerHandleId, $adminHandleId, $techHandleId]));
        $parse_array["DNS1"] = isset($parse_array["DNS1"]) && $parse_array["DNS1"] != "" ? $parse_array["DNS1"] : htmlspecialchars_decode($service->domain->DNS1);
        $parse_array["DNS2"] = isset($parse_array["DNS2"]) ? $parse_array["DNS2"] : htmlspecialchars_decode($service->domain->DNS2);
        $parse_array["DNS3"] = isset($parse_array["DNS3"]) ? $parse_array["DNS3"] : htmlspecialchars_decode($service->domain->DNS3);
        if(isset($parse_array["DNS1"]) && $parse_array["DNS1"] != "") {
            $input["domain"]["DNS1"] = $parse_array["DNS1"];
            $input["domain"]["DNS2"] = isset($parse_array["DNS2"]) ? $parse_array["DNS2"] : "";
            $input["domain"]["DNS3"] = isset($parse_array["DNS3"]) ? $parse_array["DNS3"] : "";
        } else {
            require_once "class/debtor.php";
            $debtor = new debtor();
            $debtor->Identifier = isset($parse_array["Debtor"]) ? $parse_array["Debtor"] : (isset($input["Debtor"]) ? $input["Debtor"] : "");
            if(!$debtor->show()) {
                HostFact_API::parseError(__("invalid identifier for debtor"), true);
            }
            if($debtor->DNS1 != "") {
                $input["domain"]["DNS1"] = $debtor->DNS1;
                $input["domain"]["DNS2"] = $debtor->DNS2;
                $input["domain"]["DNS3"] = $debtor->DNS3;
            } else {
                if(0 < $input["domain"]["HostingID"]) {
                    require_once "class/hosting.php";
                    $hosting = new hosting();
                    if(!$hosting->show($input["domain"]["HostingID"], false) || $hosting->Debtor != $input["Debtor"]) {
                        HostFact_API::parseError(__("invalid identifier for hosting"), true);
                    } else {
                        require_once "class/server.php";
                        $server = new server();
                        if($server->show($hosting->Server) && $server->DNS1 != "") {
                            $input["domain"]["DNS1"] = $server->DNS1;
                            $input["domain"]["DNS2"] = $server->DNS2;
                            $input["domain"]["DNS3"] = $server->DNS3;
                            return $input;
                        }
                    }
                }
                if($registrar->DNS1 != "") {
                    $input["domain"]["DNS1"] = $registrar->DNS1;
                    $input["domain"]["DNS2"] = $registrar->DNS2;
                    $input["domain"]["DNS3"] = $registrar->DNS3;
                } else {
                    HostFact_API::parseError(__("nameservers required"), true);
                }
            }
        }
        return $input;
    }
    private function _validateHandleIDs($arrayHandleIds)
    {
        require_once "class/handle.php";
        $handle = new handle();
        foreach ($arrayHandleIds as $id) {
            if((int) $id !== 0 && $handle->GetID("id", $id) === false) {
                HostFact_API::parseError(sprintf(__("invalid identifier x for contact"), $id), true);
            }
        }
    }
    public function edit_api_action()
    {
        $parse_array = $this->getValidParameters();
        if(array_key_exists("DNSTemplate", $parse_array) && is_module_active("dnsmanagement") === false) {
            HostFact_API::parseError(sprintf(__("parameters not available, module not active"), "DNSTemplate", __("module type dnsmanagement")), true);
        }
        HostFact_API::beginTransaction();
        $domain = $this->object;
        require_once "class/service.php";
        $service = new service();
        if(!$service->show($this->_get_domain_id(), "domain")) {
            HostFact_API::parseError($service->domain->Error, true);
        }
        if(isset($parse_array["Debtor"]) || isset($parse_array["DebtorCode"])) {
            $newDebtor = $this->_get_debtor_id();
            if($newDebtor === false) {
                HostFact_API::parseError(__("invalid debtor"), true);
            } else {
                $parse_array["Debtor"] = $newDebtor;
            }
        } else {
            $parse_array["Debtor"] = $service->domain->Debtor;
        }
        if(isset($parse_array["Registrar"]) && (!isset($parse_array["OwnerHandle"]) || !isset($parse_array["AdminHandle"]) || !isset($parse_array["TechHandle"]))) {
            HostFact_API::parseError(__("you need to change the handle"), true);
        }
        $inputSubscription = $this->_updateSubscriptionParameters($parse_array, $service, $service->domain->PeriodicID);
        $inputSubscription = $this->_add_domain_to_description($inputSubscription, $parse_array, isset($parse_array["Domain"]) ? $parse_array["Domain"] : htmlspecialchars_decode($service->domain->Domain), isset($parse_array["Tld"]) ? $parse_array["Tld"] : htmlspecialchars_decode($service->domain->Tld));
        $input = ["PeriodicType" => "domain", "Debtor" => $parse_array["Debtor"], "domain_id" => $this->_get_domain_id(), "domain" => ["Domain" => isset($parse_array["Domain"]) ? $parse_array["Domain"] : htmlspecialchars_decode($service->domain->Domain), "Tld" => isset($parse_array["Tld"]) ? $parse_array["Tld"] : htmlspecialchars_decode($service->domain->Tld), "Status" => isset($parse_array["Status"]) ? $parse_array["Status"] : $service->domain->Status, "HostingID" => isset($parse_array["HostingID"]) ? $parse_array["HostingID"] : $service->domain->HostingID, "RegistrationDate" => isset($parse_array["RegistrationDate"]) ? rewrite_date_db2site($parse_array["RegistrationDate"]) : $service->domain->RegistrationDate, "ExpirationDate" => isset($parse_array["ExpirationDate"]) ? rewrite_date_db2site($parse_array["ExpirationDate"]) : $service->domain->ExpirationDate, "AuthKey" => isset($parse_array["AuthKey"]) ? $parse_array["AuthKey"] : htmlspecialchars_decode($service->domain->AuthKey), "Registrar" => isset($parse_array["Registrar"]) ? $parse_array["Registrar"] : $service->domain->Registrar, "Comment" => isset($parse_array["Comment"]) ? $parse_array["Comment"] : htmlspecialchars_decode($service->domain->Comment), "DirectServerCreation" => isset($parse_array["DirectServerCreation"]) && $parse_array["DirectServerCreation"] == "yes" ? "yes" : false], "subscription_invoice" => isset($parse_array["HasSubscription"]) && $parse_array["HasSubscription"] == "yes" ? "yes" : (isset($parse_array["HasSubscription"]) && $parse_array["HasSubscription"] == "no" ? "no" : (0 < $service->domain->PeriodicID ? "yes" : "no")), "SubscriptionType" => isset($parse_array["HasSubscription"]) && $parse_array["HasSubscription"] == "yes" && $inputSubscription["subscription"]["Identifier"] == "" ? "new" : "current"];
        $input = $this->_setDNSAndHandles($input, $parse_array, $service);
        if(array_key_exists("DNSTemplate", $parse_array)) {
            $input["domain"]["DNSTemplate"] = $parse_array["DNSTemplate"];
            $_POST["domain"]["DNSTemplate"] = $parse_array["DNSTemplate"];
        }
        if($service->edit(array_merge($input, $inputSubscription))) {
            HostFact_API::commit();
            if(!empty($service->domain->Warning)) {
                HostFact_API::parseWarning($service->domain->Warning);
            }
            if(isset($parse_array["AuthKey"])) {
                $this->show_hide_auth_key = "show";
            }
            $this->_show_domain($service->domain->Identifier);
            HostFact_API::parseSuccess($service->Success, true);
        } else {
            $Errors = array_unique(array_merge($service->Error, $service->domain->Error, $service->Subscription->Error));
            HostFact_API::parseError($Errors, true);
        }
    }
    public function terminate_api_action()
    {
        require_once "class/service.php";
        $service = new service();
        if(!$service->show($this->_get_domain_id(), "domain")) {
            HostFact_API::parseError($service->domain->Error, true);
        }
        $expiration_date = rewrite_date_site2db($service->domain->ExpirationDate) ? date("Y-m-d", strtotime(rewrite_date_site2db($service->domain->ExpirationDate))) : false;
        if($this->_terminate_subscription("domain", $this->_get_domain_id(), $expiration_date)) {
            $this->_show_domain($this->_get_domain_id());
        }
    }
    public function unlock_api_action()
    {
        $domain = $this->object;
        $domain->Identifier = $this->_get_domain_id();
        if($domain->show()) {
            if($domain->Status == 4) {
                if($domain->lock(false) && empty($domain->Error) && empty($domain->Warning)) {
                    HostFact_API::parseSuccess($domain->Success, true);
                } else {
                    $domain->Error = array_merge($domain->Error, $domain->Warning);
                    HostFact_API::parseError($domain->Error, true);
                }
            } else {
                HostFact_API::parseError(sprintf(__("domain unlocking failed"), $domain->Domain . "." . $domain->Tld), true);
            }
        }
    }
    public function lock_api_action()
    {
        $domain = $this->object;
        $domain->Identifier = $this->_get_domain_id();
        if($domain->show()) {
            if($domain->Status == 4) {
                if($domain->lock(true) && empty($domain->Error) && empty($domain->Warning)) {
                    HostFact_API::parseSuccess($domain->Success, true);
                } else {
                    $domain->Error = array_merge($domain->Error, $domain->Warning);
                    HostFact_API::parseError($domain->Error, true);
                }
            } else {
                HostFact_API::parseError(sprintf(__("domain locking failed"), $domain->Domain . "." . $domain->Tld), true);
            }
        }
    }
    public function autorenew_api_action()
    {
        $domain = $this->object;
        $domain->Identifier = $this->_get_domain_id();
        if($domain->show()) {
            $autorenewValue = HostFact_API::getRequestParameter("DomainAutoRenew");
            if(!in_array($autorenewValue, ["on", "off"])) {
                $domain->Error[] = __("invalid value for domain autorenew");
                HostFact_API::parseError($domain->Error, true);
            }
            if($domain->changeAutoRenew($autorenewValue) && empty($domain->Error) && empty($domain->Warning)) {
                HostFact_API::parseSuccess($domain->Success, true);
            } else {
                $domain->Error = array_merge($domain->Error, $domain->Warning);
                HostFact_API::parseError($domain->Error, true);
            }
        }
    }
    public function gettoken_api_action()
    {
        $domain = $this->object;
        $domain->Identifier = $this->_get_domain_id();
        if($domain->show()) {
            if($domain->Status == 4 || $domain->Status == 8) {
                if(($token = $domain->gettoken(true)) && empty($domain->Error) && empty($domain->Warning)) {
                    if($token === true && defined("API_CALL_FROM_CLIENTAREA") && API_CALL_FROM_CLIENTAREA === true) {
                        HostFact_API::parseSuccess(__("token sent to the domain owner"));
                    } else {
                        HostFact_API::parseSuccess($domain->Success);
                    }
                    $domain->show();
                    $result["Identifier"] = $domain->Identifier;
                    $result["Domain"] = $domain->Domain;
                    $result["Tld"] = $domain->Tld;
                    $result["AuthKey"] = htmlspecialchars_decode($domain->AuthKey);
                    HostFact_API::parseResponse($result);
                } else {
                    $domain->Error = array_merge($domain->Error, $domain->Warning);
                    HostFact_API::parseError($domain->Error, true);
                }
            } else {
                HostFact_API::parseError(__("token could not be retrieved"), true);
            }
        } else {
            HostFact_API::parseError($domain->Error, true);
        }
    }
    public function delete_api_action()
    {
        $domain = $this->object;
        $domain->Identifier = $this->_get_domain_id();
        if($domain->show()) {
            $confirmRegistrar = HostFact_API::getRequestParameter("ConfirmRegistrar");
            if($domain->delete($domain->Identifier, $confirmRegistrar)) {
                if(count($domain->Error)) {
                    HostFact_API::parseWarning($domain->Error);
                }
                HostFact_API::parseSuccess($domain->Success, true);
            } else {
                HostFact_API::parseError($domain->Error, true);
            }
        } else {
            HostFact_API::parseError($domain->Error, true);
        }
    }
    public function register_api_action()
    {
        $domain = $this->object;
        $domain->Identifier = $this->_get_domain_id();
        if($domain->show()) {
            if($domain->register()) {
                HostFact_API::parseSuccess($domain->Success, true);
            } else {
                HostFact_API::parseError($domain->Error, true);
            }
        } else {
            HostFact_API::parseError($domain->Error, true);
        }
    }
    public function transfer_api_action()
    {
        $domain = $this->object;
        $domain->Identifier = $this->_get_domain_id();
        if($domain->show()) {
            if(($domain_authkey = HostFact_API::getRequestParameter("AuthKey")) && !$domain->setToken($domain_authkey)) {
                HostFact_API::parseError($domain->Error, true);
            }
            if($domain->transfer()) {
                HostFact_API::parseSuccess($domain->Success, true);
            } else {
                HostFact_API::parseError($domain->Error, true);
            }
        } else {
            HostFact_API::parseError($domain->Error, true);
        }
    }
    public function changenameserver_api_action()
    {
        $domain = $this->object;
        $domain->Identifier = $this->_get_domain_id();
        if($domain->show()) {
            $oldDomain = clone $domain;
            $parse_array = $this->getValidParameters();
            if(array_key_exists("DNSTemplate", $parse_array) && is_module_active("dnsmanagement") === false) {
                HostFact_API::parseError(sprintf(__("parameters not available, module not active"), "DNSTemplate", __("module type dnsmanagement")), true);
            }
            $domain->DNS1 = isset($parse_array["DNS1"]) ? $parse_array["DNS1"] : $domain->DNS1;
            $domain->DNS2 = isset($parse_array["DNS2"]) ? $parse_array["DNS2"] : $domain->DNS2;
            $domain->DNS3 = isset($parse_array["DNS3"]) ? $parse_array["DNS3"] : $domain->DNS3;
            $domain->DNS1IP = isset($parse_array["DNS1IP"]) ? $parse_array["DNS1IP"] : $domain->DNS1IP;
            $domain->DNS2IP = isset($parse_array["DNS2IP"]) ? $parse_array["DNS2IP"] : $domain->DNS2IP;
            $domain->DNS3IP = isset($parse_array["DNS3IP"]) ? $parse_array["DNS3IP"] : $domain->DNS3IP;
            $domain->DNSTemplate = isset($parse_array["DNSTemplate"]) ? $parse_array["DNSTemplate"] : 0;
            if(defined("API_CALL_FROM_CLIENTAREA") && API_CALL_FROM_CLIENTAREA === true) {
                require_once "class/debtor.php";
                $debtor = new debtor();
                $debtor->Identifier = $domain->Debtor;
                $debtor->show();
                require_once "class/clientareaprofiles.php";
                $ClientareaProfiles = new ClientareaProfiles_Model();
                if($debtor->ClientareaProfile && 0 < $debtor->ClientareaProfile) {
                    $ClientareaProfiles->id = $debtor->ClientareaProfile;
                    $ClientareaProfiles->show();
                } else {
                    $ClientareaProfiles->showDefault();
                }
                require_once "class/clientareachange.php";
                $ClientareaChanges = new ClientareaChange_Model();
                $ip = HostFact_API::getRequestParameter("IPAddress") ? HostFact_API::getRequestParameter("IPAddress") : "";
                $options = [];
                $options["filters"]["reference_type"] = "domain";
                $options["filters"]["reference_id"] = $domain->Identifier;
                $options["filters"]["action"] = "changenameserver";
                $options["filters"]["debtor"] = $domain->Debtor;
                $options["filter"] = "pending";
                $changes_result = $ClientareaChanges->listChanges($options);
                if(!empty($changes_result)) {
                    $changes_result = end($changes_result);
                    $ClientareaChanges->id = $changes_result->id;
                }
                $nameservers_update = ["DNS1" => $domain->DNS1, "DNS2" => $domain->DNS2, "DNS3" => $domain->DNS3];
                if(isset($parse_array["DNS1IP"])) {
                    $nameservers_update["DNS1IP"] = $domain->DNS1IP;
                }
                if(isset($parse_array["DNS2IP"])) {
                    $nameservers_update["DNS2IP"] = $domain->DNS2IP;
                }
                if(isset($parse_array["DNS3IP"])) {
                    $nameservers_update["DNS3IP"] = $domain->DNS3IP;
                }
                $ClientareaChanges->ReferenceType = "domain";
                $ClientareaChanges->ReferenceID = $domain->Identifier;
                $ClientareaChanges->Action = "changenameserver";
                $ClientareaChanges->Data = $nameservers_update;
                $ClientareaChanges->Debtor = $domain->Debtor;
                $ClientareaChanges->Approval = $ClientareaProfiles->Rights["CLIENTAREA_DOMAIN_NAMESERVER_CHANGE"] == "approve" ? "pending" : "notused";
                $ClientareaChanges->Status = "pending";
                $ClientareaChanges->CreatorType = "debtor";
                $ClientareaChanges->CreatorID = $domain->Debtor;
                $ClientareaChanges->IP = $ip;
                $result = isset($ClientareaChanges->id) && $ClientareaChanges->id ? $ClientareaChanges->edit() : $ClientareaChanges->add();
                if($result) {
                    HostFact_API::parseSuccess($ClientareaChanges->Success);
                } else {
                    HostFact_API::parseError($ClientareaChanges->Error, true);
                }
            } elseif($domain->changenameserver()) {
                if(!empty($domain->Error)) {
                    HostFact_API::parseError($domain->Error);
                }
                if(!empty($domain->Warning)) {
                    HostFact_API::parseWarning($domain->Warning);
                }
                HostFact_API::parseSuccess($domain->Success);
            } else {
                HostFact_API::parseError($domain->Error, true);
            }
        } else {
            HostFact_API::parseError($domain->Error, true);
        }
        if(HostFact_API::getRequestParameter("SendNotification") == "yes") {
            if(!isset($ClientareaProfiles)) {
                require_once "class/debtor.php";
                $debtor = new debtor();
                $debtor->Identifier = $domain->Debtor;
                $debtor->show();
                require_once "class/clientareaprofiles.php";
                $ClientareaProfiles = new ClientareaProfiles_Model();
                if($debtor->ClientareaProfile && 0 < $debtor->ClientareaProfile) {
                    $ClientareaProfiles->id = $debtor->ClientareaProfile;
                    $ClientareaProfiles->show();
                } else {
                    $ClientareaProfiles->showDefault();
                }
            }
            $domain->ClientareaProfileObject = $ClientareaProfiles;
            require_once "class/email.php";
            $email = new email();
            $email->Sender = getFirstMailAddress(CLIENTAREA_NOTIFICATION_EMAILADDRESS);
            $email->Recipient = CLIENTAREA_NOTIFICATION_EMAILADDRESS;
            $email->Subject = __("subject domain nameservers changed");
            $email->Message = file_get_contents("includes/language/" . LANGUAGE . "/mail.domain.nameservers.changed.phtml");
            $email->Debtor = $domain->Debtor;
            $email->add(["domain" => $oldDomain, "domainChanged" => $domain]);
            $email->Debtor = 0;
            $email->sent();
        }
        HostFact_API::parseResponse();
    }
    public function syncwhois_api_action()
    {
        $domain = $this->object;
        $domain->Identifier = $this->_get_domain_id();
        if(HostFact_API::getRequestParameter("ChargeCosts") != "no") {
            $domain->ChargeCosts = true;
        }
        if($domain->show()) {
            if($domain->updateWhoisDataToRegistrar()) {
                HostFact_API::parseSuccess($domain->Success, true);
            } else {
                HostFact_API::parseError($domain->Error, true);
            }
        } else {
            HostFact_API::parseError($domain->Error, true);
        }
    }
    public function check_api_action()
    {
        $parse_array = $this->getValidParameters();
        $available = "";
        $domain = $this->object;
        $domain->Registrar = isset($parse_array["Registrar"]) ? $parse_array["Registrar"] : "";
        $domain->Domain = $parse_array["Domain"];
        $domain->Tld = $parse_array["Tld"];
        $check = $domain->check(true);
        if($check === false) {
            $check = $domain->publicCheck();
        }
        if($check === false) {
            HostFact_API::parseError(__("domain check failed"));
        }
        if($domain->Type == "register") {
            $available = "yes";
        } elseif($domain->Type == "transfer") {
            $available = "no";
        }
        $result[] = ["Domain" => $domain->Domain, "Tld" => $domain->Tld, "Available" => $available];
        HostFact_API::parseResponse($result, true);
    }
    public function listdnstemplates_api_action()
    {
        if(is_module_active("dnsmanagement") === false) {
            HostFact_API::parseError(sprintf(__("function not available, module not active"), "listdnstemplates", __("module type dnsmanagement")), true);
        }
        HostFact_API::setObjectNames("dnstemplates", "dnstemplate");
        $this->module_path = "3rdparty/modules/dns/dnsmanagement/";
        require_once $this->module_path . "models/dns_template_model.php";
        $DNSobject = new modules\dns\dnsmanagement\DNS_Template_Model();
        $options = ["fields" => ["id", "Name", "DNSRecords", "TemplateID", "TemplateName"]];
        $list_dns_templates = $DNSobject->listDNSTemplates($options);
        if(!empty($list_dns_templates)) {
            foreach ($list_dns_templates as $_key => $_dns_template) {
                if($_dns_template->DNSRecords != "null" && $_dns_template->DNSRecords != "") {
                    $list_dns_templates[$_key]->DNSRecords = json_decode($_dns_template->DNSRecords, true);
                } else {
                    $list_dns_templates[$_key]->DNSRecords = [];
                }
            }
        }
        HostFact_API::parseResponse($list_dns_templates, true);
    }
    public function getdnszone_api_action()
    {
        if(is_module_active("dnsmanagement") === false) {
            HostFact_API::parseError(sprintf(__("function not available, module not active"), "getdnszone", __("module type dnsmanagement")), true);
        }
        $domain_id = $this->_get_domain_id();
        HostFact_API::setObjectNames("domain", "domain");
        $this->module_path = "3rdparty/modules/dns/dnsmanagement/";
        require_once $this->module_path . "dnsmanagement.php";
        $DNSobject = new modules\dns\dnsmanagement\dnsmanagement();
        $dns_zone = [];
        $dns_zone["dns_zone"] = $DNSobject->getOrSaveDNSZone($domain_id);
        $dns_zone["dns_zone"]["SettingSingleTTL"] = isset($DNSobject->dns_integration_obj->SettingSingleTTL) && $DNSobject->dns_integration_obj->SettingSingleTTL === true ? true : false;
        $dns_zone["dns_zone"]["SettingAvailableTypes"] = $DNSobject->RecordTypes;
        HostFact_API::parseResponse($dns_zone, true);
    }
    public function editdnszone_api_action()
    {
        $domain = $this->object;
        $domain->Identifier = $this->_get_domain_id();
        if(!$domain->show()) {
            HostFact_API::parseError($domain->Error, true);
        }
        if(is_module_active("dnsmanagement") === false) {
            HostFact_API::parseError(sprintf(__("function not available, module not active"), "getdnszone", __("module type dnsmanagement")), true);
        }
        HostFact_API::setObjectNames("domain", "domain");
        $parse_array = $this->getValidParameters();
        $dns_records = [];
        if(!empty($parse_array["DNSZone"]["records"])) {
            $index = 0;
            foreach ($parse_array["DNSZone"]["records"] as $_key => $_record) {
                $dns_records["id"][$index] = isset($_record["id"]) && $_record["id"] ? $_record["id"] : "";
                $dns_records["Name"][$index] = isset($_record["name"]) ? $_record["name"] : "";
                $dns_records["Type"][$index] = isset($_record["type"]) ? $_record["type"] : "";
                $dns_records["Value"][$index] = isset($_record["value"]) ? $_record["value"] : "";
                $dns_records["Priority"][$index] = isset($_record["priority"]) ? $_record["priority"] : "";
                $dns_records["TTL"][$index] = isset($_record["ttl"]) ? $_record["ttl"] : "";
                $index++;
            }
        }
        $this->module_path = "3rdparty/modules/dns/dnsmanagement/";
        require_once $this->module_path . "dnsmanagement.php";
        $DNSobject = new modules\dns\dnsmanagement\dnsmanagement();
        if(defined("API_CALL_FROM_CLIENTAREA") && API_CALL_FROM_CLIENTAREA === true) {
            $dns_zone = $DNSobject->getOrSaveDNSZone($domain->Identifier);
            require_once "class/debtor.php";
            $debtor = new debtor();
            $debtor->Identifier = $domain->Debtor;
            $debtor->show();
            require_once "class/clientareaprofiles.php";
            $ClientareaProfiles = new ClientareaProfiles_Model();
            if($debtor->ClientareaProfile && 0 < $debtor->ClientareaProfile) {
                $ClientareaProfiles->id = $debtor->ClientareaProfile;
                $ClientareaProfiles->show();
            } else {
                $ClientareaProfiles->showDefault();
            }
            require_once "class/clientareachange.php";
            $ClientareaChanges = new ClientareaChange_Model();
            $ip = HostFact_API::getRequestParameter("IPAddress") ? HostFact_API::getRequestParameter("IPAddress") : "";
            $options = [];
            $options["filters"]["reference_type"] = "domain";
            $options["filters"]["reference_id"] = $domain->Identifier;
            $options["filters"]["action"] = "editdnszone";
            $options["filters"]["debtor"] = $domain->Debtor;
            $options["filter"] = "pending";
            $changes_result = $ClientareaChanges->listChanges($options);
            if(!empty($changes_result)) {
                if(isset($changes_result[0]->Action) && $changes_result[0]->Action == "editdnszone") {
                    $modified_dnszone_data = json_decode($changes_result[0]->Data, true);
                    if(!empty($modified_dnszone_data)) {
                        $dns_zone["records"] = [];
                        foreach ($modified_dnszone_data["Records"]["Name"] as $_record_key => $_value) {
                            $dns_zone["records"][$_record_key]["id"] = isset($modified_dnszone_data["Records"]["id"][$_record_key]) ? $modified_dnszone_data["Records"]["id"][$_record_key] : 0;
                            $dns_zone["records"][$_record_key]["name"] = $modified_dnszone_data["Records"]["Name"][$_record_key];
                            $dns_zone["records"][$_record_key]["type"] = $modified_dnszone_data["Records"]["Type"][$_record_key];
                            $dns_zone["records"][$_record_key]["value"] = $modified_dnszone_data["Records"]["Value"][$_record_key];
                            $dns_zone["records"][$_record_key]["priority"] = $modified_dnszone_data["Records"]["Priority"][$_record_key];
                            $dns_zone["records"][$_record_key]["ttl"] = $modified_dnszone_data["Records"]["TTL"][$_record_key];
                        }
                    }
                }
                $changes_result = end($changes_result);
                $ClientareaChanges->id = $changes_result->id;
            }
            $ClientareaChanges->ReferenceType = "domain";
            $ClientareaChanges->ReferenceID = $domain->Identifier;
            $ClientareaChanges->Action = "editdnszone";
            $ClientareaChanges->Data = ["Records" => $dns_records];
            $ClientareaChanges->Debtor = $domain->Debtor;
            $ClientareaChanges->Approval = $ClientareaProfiles->Rights["CLIENTAREA_DOMAIN_DNSZONE_CHANGE"] == "approve" ? "pending" : "notused";
            $ClientareaChanges->Status = "pending";
            $ClientareaChanges->CreatorType = "debtor";
            $ClientareaChanges->CreatorID = $domain->Debtor;
            $ClientareaChanges->IP = $ip;
            $result = isset($ClientareaChanges->id) && $ClientareaChanges->id ? $ClientareaChanges->edit() : $ClientareaChanges->add();
            if($result) {
                HostFact_API::parseSuccess($ClientareaChanges->Success);
            } else {
                HostFact_API::parseError($ClientareaChanges->Error, true);
            }
        } else {
            $result = $DNSobject->getOrSaveDNSZone($domain->Identifier, $dns_records);
        }
        if(HostFact_API::getRequestParameter("SendNotification") == "yes") {
            if(!isset($ClientareaProfiles)) {
                require_once "class/debtor.php";
                $debtor = new debtor();
                $debtor->Identifier = $domain->Debtor;
                $debtor->show();
                require_once "class/clientareaprofiles.php";
                $ClientareaProfiles = new ClientareaProfiles_Model();
                if($debtor->ClientareaProfile && 0 < $debtor->ClientareaProfile) {
                    $ClientareaProfiles->id = $debtor->ClientareaProfile;
                    $ClientareaProfiles->show();
                } else {
                    $ClientareaProfiles->showDefault();
                }
            }
            $domain->ClientareaProfileObject = $ClientareaProfiles;
            if(defined("API_CALL_FROM_CLIENTAREA") && API_CALL_FROM_CLIENTAREA === true) {
                $hashTableOldRecords = array_flip(array_map(function ($record) {
                    return md5($record["name"] . $record["type"] . $record["value"]);
                }, $dns_zone["records"]));
                $hashTableNewRecords = array_flip(array_map(function ($record) {
                    return $record["checksum"];
                }, $parse_array["DNSZone"]["records"]));
                $addedHashes = array_diff(array_keys($hashTableNewRecords), array_keys($hashTableOldRecords));
                $removedHashes = array_diff(array_keys($hashTableOldRecords), array_keys($hashTableNewRecords));
                $recordTableContent = [];
                $recordProperties = ["name", "type", "value", "ttl"];
                foreach (array_unique(array_merge(array_keys($hashTableOldRecords), array_keys($hashTableNewRecords))) as $recordHash) {
                    if(in_array($recordHash, $addedHashes)) {
                        $styleClass = "added";
                        $record = $parse_array["DNSZone"]["records"][$hashTableNewRecords[$recordHash]];
                    } elseif(in_array($recordHash, $removedHashes)) {
                        $styleClass = "removed";
                        $record = array_change_key_case($dns_zone["records"][$hashTableOldRecords[$recordHash]], CASE_LOWER);
                    } else {
                        $recordNew = $parse_array["DNSZone"]["records"][$hashTableNewRecords[$recordHash]];
                        $recordOld = array_change_key_case($dns_zone["records"][$hashTableOldRecords[$recordHash]], CASE_LOWER);
                        $record = [];
                        $recordChanged = false;
                        foreach ($recordProperties as $field) {
                            if($recordNew[$field] == $recordOld[$field]) {
                                $record[$field] = $recordNew[$field];
                            } else {
                                $recordChanged = true;
                                $record[$field] = "<s>" . $recordOld[$field] . "</s><br />" . $recordNew[$field];
                            }
                        }
                        $styleClass = $recordChanged === true ? "changed" : "";
                    }
                    $recordContent = ["class" => $styleClass, "columns" => []];
                    foreach ($recordProperties as $field) {
                        $recordContent["columns"][] = $record[$field];
                    }
                    $recordTableContent[] = $recordContent;
                }
            } else {
                $recordTableContent = NULL;
            }
            require_once "class/email.php";
            $email = new email();
            $email->Sender = getFirstMailAddress(CLIENTAREA_NOTIFICATION_EMAILADDRESS);
            $email->Recipient = CLIENTAREA_NOTIFICATION_EMAILADDRESS;
            $email->Subject = sprintf(__("subject domain dnszone changed"), $domain->Domain . "." . $domain->Tld);
            $email->Message = file_get_contents("includes/language/" . LANGUAGE . "/mail.domain.dnszone.changed.phtml");
            $email->Debtor = $domain->Debtor;
            $email->add(["domain" => $domain, "recordTableContent" => $recordTableContent]);
            $email->Debtor = 0;
            $email->sent();
        }
        if($result === false || !empty($DNSobject->Error)) {
            HostFact_API::parseError($DNSobject->Error, true);
        } else {
            HostFact_API::parseResponse($DNSobject->Success, true);
        }
    }
    public function editwhois_api_action()
    {
        $domain = $this->object;
        $domain->Identifier = $this->_get_domain_id();
        $parse_array = $this->getValidParameters();
        $whois_data = [];
        foreach ($parse_array as $_key => $_value) {
            if(in_array($_key, ["Owner", "Admin", "Tech"])) {
                $whois_data[$_key] = $_value;
            }
        }
        if($domain->show()) {
            $oldDomain = clone $domain;
            if(HostFact_API::getRequestParameter("ChargeCosts") != "no" && isset($whois_data["Owner"])) {
                $domain->ChargeCosts = true;
            }
            if(defined("API_CALL_FROM_CLIENTAREA") && API_CALL_FROM_CLIENTAREA === true) {
                require_once "class/debtor.php";
                $debtor = new debtor();
                $debtor->Identifier = $domain->Debtor;
                $debtor->show();
                require_once "class/clientareaprofiles.php";
                $ClientareaProfiles = new ClientareaProfiles_Model();
                if($debtor->ClientareaProfile && 0 < $debtor->ClientareaProfile) {
                    $ClientareaProfiles->id = $debtor->ClientareaProfile;
                    $ClientareaProfiles->show();
                } else {
                    $ClientareaProfiles->showDefault();
                }
                require_once "class/clientareachange.php";
                $ClientareaChanges = new ClientareaChange_Model();
                $ip = HostFact_API::getRequestParameter("IPAddress") ? HostFact_API::getRequestParameter("IPAddress") : "";
                $options = [];
                $options["filters"]["reference_type"] = "domain";
                $options["filters"]["reference_id"] = $domain->Identifier;
                $options["filters"]["action"] = "editwhois";
                $options["filters"]["debtor"] = $domain->Debtor;
                $options["filter"] = "pending";
                $changes_result = $ClientareaChanges->listChanges($options);
                if(!empty($changes_result)) {
                    $changes_result = end($changes_result);
                    $ClientareaChanges->id = $changes_result->id;
                }
                $ClientareaChanges->ReferenceType = "domain";
                $ClientareaChanges->ReferenceID = $domain->Identifier;
                $ClientareaChanges->Action = "editwhois";
                $ClientareaChanges->Data = $whois_data;
                $ClientareaChanges->Debtor = $domain->Debtor;
                $ClientareaChanges->Approval = $ClientareaProfiles->Rights["CLIENTAREA_DOMAIN_WHOIS_CHANGE"] == "approve" ? "pending" : "notused";
                $ClientareaChanges->Status = "pending";
                $ClientareaChanges->CreatorType = "debtor";
                $ClientareaChanges->CreatorID = $domain->Debtor;
                $ClientareaChanges->IP = $ip;
                $result = isset($ClientareaChanges->id) && $ClientareaChanges->id ? $ClientareaChanges->edit() : $ClientareaChanges->add();
                if($result) {
                    HostFact_API::parseSuccess($ClientareaChanges->Success);
                } else {
                    HostFact_API::parseError($ClientareaChanges->Error, true);
                }
            } elseif($domain->editWhois($whois_data)) {
                HostFact_API::parseWarning($domain->Warning);
                HostFact_API::parseSuccess($domain->Success);
            } else {
                HostFact_API::parseError($domain->Error, true);
            }
        } else {
            HostFact_API::parseError($domain->Error, true);
        }
        if(HostFact_API::getRequestParameter("SendNotification") == "yes") {
            if(!isset($ClientareaProfiles)) {
                require_once "class/debtor.php";
                $debtor = new debtor();
                $debtor->Identifier = $domain->Debtor;
                $debtor->show();
                require_once "class/clientareaprofiles.php";
                $ClientareaProfiles = new ClientareaProfiles_Model();
                if($debtor->ClientareaProfile && 0 < $debtor->ClientareaProfile) {
                    $ClientareaProfiles->id = $debtor->ClientareaProfile;
                    $ClientareaProfiles->show();
                } else {
                    $ClientareaProfiles->showDefault();
                }
            }
            $domain->ClientareaProfileObject = $ClientareaProfiles;
            require_once "class/email.php";
            $email = new email();
            $email->Sender = getFirstMailAddress(CLIENTAREA_NOTIFICATION_EMAILADDRESS);
            $email->Recipient = CLIENTAREA_NOTIFICATION_EMAILADDRESS;
            $email->Subject = __("subject domain whois changed");
            $email->Message = file_get_contents("includes/language/" . LANGUAGE . "/mail.domain.whois.changed.phtml");
            $email->Debtor = $domain->Debtor;
            $email->add(["domain" => $oldDomain, "domainChanged" => $domain]);
            $email->Debtor = 0;
            $email->sent();
        }
        HostFact_API::parseResponse();
    }
    public function cancelmodification_api_action()
    {
        $parse_array = $this->getValidParameters();
        require_once "class/service.php";
        $service = new service();
        $domain_id = $this->_get_domain_id();
        if(!$service->show($domain_id, "domain")) {
            HostFact_API::parseError($service->domain->Error, true);
        }
        if($this->_cancel_modification("domain", $domain_id, $service->domain->Debtor)) {
            $this->_show_domain($domain_id);
        }
    }
    protected function _get_domain_id()
    {
        require_once "class/domain.php";
        $domain = new domain();
        if($domain_id = HostFact_API::getRequestParameter("Identifier")) {
            if(defined("API_CALL_FROM_CLIENTAREA") && API_CALL_FROM_CLIENTAREA === true && !empty(debtor_id::$ClientArea)) {
                $domain_id = $domain->getID("clientarea", $domain_id, ClientArea::$debtor_id);
            } else {
                $domain_id = $domain->getID("identifier", $domain_id);
            }
            return $domain_id;
        }
        if(($domainName = HostFact_API::getRequestParameter("Domain")) && ($tld = HostFact_API::getRequestParameter("Tld"))) {
            $domain_id = $domain->getID("domain", $domainName, $tld);
            if(!$domain_id) {
                HostFact_API::parseError(__("invalid domain"), true);
            }
            if(defined("API_CALL_FROM_CLIENTAREA") && API_CALL_FROM_CLIENTAREA === true && !empty(debtor_id::$ClientArea)) {
                $domain_id = $domain->getID("clientarea", $domain_id, ClientArea::$debtor_id);
            }
            return $domain_id;
        }
        return false;
    }
    protected function _show_domain($domain_id)
    {
        $domain = $this->object;
        $domain->Identifier = $domain_id;
        if(!$domain->show()) {
            HostFact_API::parseError($domain->Error, true);
        }
        require_once "class/debtor.php";
        $debtor = new debtor();
        $debtor->Identifier = $domain->Debtor;
        $debtor->show();
        $result = [];
        foreach ($this->_object_parameters as $field => $field_info) {
            if(isset($domain->{$field})) {
                $result[$field] = is_string($domain->{$field}) ? htmlspecialchars_decode($domain->{$field}) : $domain->{$field};
            } else {
                switch ($field) {
                    case "OwnerHandle":
                        $result["OwnerHandle"] = htmlspecialchars_decode($domain->ownerHandle);
                        break;
                    case "AdminHandle":
                        $result["AdminHandle"] = htmlspecialchars_decode($domain->adminHandle);
                        break;
                    case "TechHandle":
                        $result["TechHandle"] = htmlspecialchars_decode($domain->techHandle);
                        break;
                    case "DebtorCode":
                        $result[$field] = htmlspecialchars_decode($debtor->DebtorCode);
                        break;
                }
            }
        }
        $result["ExpirationDate"] = $this->_filter_date_site2api($result["ExpirationDate"]);
        $result["RegistrationDate"] = $this->_filter_date_site2api($result["RegistrationDate"]);
        if(HostFact_API::getRequestParameter("ShowHandleInfo") == "yes") {
            require_once "class/handle.php";
            foreach (["OwnerHandle", "AdminHandle", "TechHandle"] as $type) {
                $handle = new handle();
                $handle->Identifier = $result[$type];
                $handle->show();
                foreach ($handle->Variables as $key) {
                    $result["HandleInfo"][$type][$key] = htmlspecialchars_decode($handle->{$key});
                }
                if(!defined("IS_INTERNATIONAL") || IS_INTERNATIONAL === false) {
                    unset($result["HandleInfo"][$type]["Address2"]);
                    unset($result["HandleInfo"][$type]["State"]);
                }
            }
        }
        if(0 < $domain->Registrar) {
            require_once "class/registrar.php";
            $registrar_obj = new registrar();
            if($registrar_obj->show($domain->Registrar)) {
                $result["RegistrarInfo"]["Identifier"] = $registrar_obj->Identifier;
                $result["RegistrarInfo"]["Class"] = $registrar_obj->Class;
                $result["RegistrarInfo"]["Name"] = $registrar_obj->Name;
                $result["RegistrarInfo"]["Testmode"] = $registrar_obj->Testmode;
                $result["RegistrarInfo"]["DefaultDNSTemplate"] = $registrar_obj->DefaultDNSTemplate;
                $result["RegistrarInfo"]["AdminHandle"] = $registrar_obj->AdminHandle;
                $result["RegistrarInfo"]["TechHandle"] = $registrar_obj->TechHandle;
            }
        }
        if(0 < $domain->PeriodicID) {
            require_once "class/periodic.php";
            $periodic = new periodic();
            if($periodic->show($domain->PeriodicID)) {
                foreach ($this->_object_parameters["Subscription"]["children"] as $elementKey => $elementValue) {
                    $result["Subscription"][$elementKey] = $periodic->{$elementKey};
                }
                $result["Subscription"] = $this->_show_subscription($result["Subscription"]);
            }
        }
        $result = $this->_show_termination($result, "domain", $domain->Identifier);
        if($this->show_hide_auth_key == "hide") {
            unset($result["AuthKey"]);
        }
        global $array_domainstatus;
        $result["Translations"] = ["RegistrarName" => $domain->Name, "Status" => isset($array_domainstatus[$domain->Status]) ? $array_domainstatus[$domain->Status] : ""];
        $result = $this->_show_modifications($result, "domain", $domain->Identifier, $domain->Debtor);
        if(is_module_active("dnsmanagement") && ($domain->Status == 4 || $domain->Status == 8)) {
            global $_module_instances;
            $dnsmanagement = $_module_instances["dnsmanagement"];
            if($nameservers_manager = $dnsmanagement->get_nameservers_manager($domain)) {
                $result["NameserversManager"]["Type"] = isset($nameservers_manager["Type"]) ? $nameservers_manager["Type"] : "";
                $result["NameserversManager"]["IntegrationID"] = isset($nameservers_manager["IntegrationID"]) ? $nameservers_manager["IntegrationID"] : "";
            }
        }
        $result["Created"] = $this->_filter_date_db2api($domain->Created, false);
        $result["Modified"] = $this->_filter_date_db2api($domain->Modified, false);
        return HostFact_API::parseResponse($result);
    }
    protected function getFilterValues()
    {
        $filters = parent::getFilterValues();
        if(isset($filters["status"]) && $filters["status"] && !in_array($filters["status"], ["client_visible"])) {
            global $array_domainstatus;
            $tmp_status = explode("|", $filters["status"]);
            foreach ($tmp_status as $tmp_status_key) {
                if(!array_key_exists($tmp_status_key, $array_domainstatus)) {
                    HostFact_API::parseError("Invalid filter 'status'. The status '" . $tmp_status_key . "' does not exist", true);
                }
            }
        }
        return $filters;
    }
    protected function _add_domain_to_description($inputSubscription, $parse_array, $sld, $tld)
    {
        if(!isset($parse_array["Subscription"]["Description"]) && $inputSubscription["subscription"]["Identifier"] == "") {
            if(strrpos($inputSubscription["subscription"]["Description"], " ." . $tld) !== false) {
                $inputSubscription["subscription"]["Description"] = substr_replace($inputSubscription["subscription"]["Description"], " " . $sld . "." . $tld, strrpos($inputSubscription["subscription"]["Description"], " ." . $tld), strlen(" ." . $tld));
            } elseif(strrpos($inputSubscription["subscription"]["Description"], "-") !== false) {
                $inputSubscription["subscription"]["Description"] = substr_replace($inputSubscription["subscription"]["Description"], "- " . $sld . "." . $tld, strrpos($inputSubscription["subscription"]["Description"], "-"), 1);
            } else {
                $inputSubscription["subscription"]["Description"] .= " - " . $sld . "." . $tld;
            }
        }
        return $inputSubscription;
    }
}

?>