<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "subscription_api_controller.php";
class hosting_api_controller extends subscription_api_controller
{
    private $show_hosting_usage = false;
    public function __construct()
    {
        parent::__construct();
        HostFact_API::setObjectNames("hosting", "hosting");
        require_once "class/hosting.php";
        $this->addParameter("Identifier", "int");
        $this->addParameter("Username", "string");
        $this->addParameter("Password", "string");
        $this->addParameter("Debtor", "int");
        $this->addParameter("DebtorCode", "string");
        $this->addParameter("Domain", "string");
        $this->addParameter("Server", "int");
        $this->addParameter("Package", "int");
        $this->addParameter("PackageName", "readonly");
        $this->addParameter("Comment", "text");
        $this->addParameter("DirectCreation", "string");
        $this->addParameter("Status", "default_int");
        $this->addParameter("Created", "readonly");
        $this->addParameter("Modified", "readonly");
        $this->addFilter("status", "string", "");
        $this->addFilter("created", "filter_datetime");
        $this->addFilter("modified", "filter_datetime");
        $this->object = new hosting();
    }
    public function list_api_action()
    {
        $this->_extra_search_filter = ["ProductCode", "Description"];
        $filters = $this->getFilterValues();
        $fields = ["Username", "Debtor", "DebtorCode", "CompanyName", "SurName", "Initials", "Domain", "Server", "Name", "Status", "Package", "PackageName", "PeriodicID", "ProductCode", "Number", "Description", "PriceExcl", "TaxPercentage", "DiscountPercentage", "Periods", "Periodic", "NextDate", "StartPeriod", "EndPeriod", "TerminationDate", "Modified"];
        $sort = $filters["sort"] ? $filters["sort"] : "Username";
        $page_offset = $filters["offset"] / $filters["limit"] + 1;
        $searchat = $filters["searchat"] ? $filters["searchat"] : "Username|Domain";
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
        $hosting_list = $this->object->all($fields, $sort, $filters["order"], $page_offset, $searchat, $filters["searchfor"], $field_filters, $limit);
        $array = [];
        foreach ($hosting_list as $key => $value) {
            if($key != "CountRows" && is_numeric($key)) {
                $line_amount = getLineAmount(VAT_CALC_METHOD, $value["PriceExcl"], $value["Periods"], $value["Number"], $value["TaxPercentage"], $value["DiscountPercentage"]);
                $value["AmountIncl"] = $line_amount["incl"];
                $value["AmountExcl"] = $line_amount["excl"];
                $param = ["Identifier" => $value["id"], "Username" => htmlspecialchars_decode($value["Username"]), "Debtor" => $value["Debtor"], "DebtorCode" => htmlspecialchars_decode($value["DebtorCode"]), "CompanyName" => htmlspecialchars_decode($value["CompanyName"]), "Initials" => htmlspecialchars_decode($value["Initials"]), "SurName" => htmlspecialchars_decode($value["SurName"]), "Domain" => htmlspecialchars_decode($value["Domain"]), "Server" => $value["Server"], "Name" => htmlspecialchars_decode($value["Name"]), "Package" => $value["Package"], "PackageName" => htmlspecialchars_decode($value["PackageName"]), "Status" => $value["Status"], "Subscription" => ["ProductCode" => $value["ProductCode"], "Description" => htmlspecialchars_decode($value["Description"]), "Number" => htmlspecialchars_decode($value["Number"]), "PriceExcl" => $value["PriceExcl"], "TaxPercentage" => $value["TaxPercentage"] * 100, "DiscountPercentage" => $value["DiscountPercentage"] * 100, "Periods" => $value["Periods"], "Periodic" => htmlspecialchars_decode($value["Periodic"]), "StartPeriod" => $this->_filter_date_db2api($value["StartPeriod"]), "EndPeriod" => $this->_filter_date_db2api($value["EndPeriod"]), "NextDate" => $this->_filter_date_db2api($value["NextDate"]), "TerminationDate" => $this->_filter_date_db2api($value["TerminationDate"]), "AmountExcl" => $value["AmountExcl"], "AmountIncl" => $value["AmountIncl"]], "Modified" => $this->_filter_date_db2api($value["Modified"], false)];
                if(empty($value["PeriodicID"])) {
                    unset($param["Subscription"]);
                }
                if(isset($value["Termination.id"]) && 0 < $value["Termination.id"]) {
                    $param["Termination"] = ["Date" => $value["Termination.Date"], "Created" => $value["Termination.Created"], "Status" => $value["Termination.Status"]];
                }
                $array[] = $param;
            }
        }
        HostFact_API::setMetaData("totalresults", $hosting_list["CountRows"]);
        HostFact_API::setMetaData("currentresults", count($array));
        HostFact_API::setMetaData("offset", $filters["offset"]);
        if(!empty($this->_valid_filter_input)) {
            HostFact_API::setMetaData("filters", $this->_valid_filter_input);
        }
        HostFact_API::parseResponse($array, true);
    }
    public function show_api_action()
    {
        $hosting_id = $this->_get_hosting_id();
        if(defined("API_CALL_FROM_CLIENTAREA") && API_CALL_FROM_CLIENTAREA === true) {
            $this->show_hosting_usage = true;
        }
        $this->_show_hosting($hosting_id);
    }
    public function sendaccountinfobyemail_api_action()
    {
        $hosting = $this->object;
        $hosting->Identifier = $this->_get_hosting_id();
        if($hosting->show(0, false)) {
            if($hosting->emailPDF($hosting->Identifier, $hosting->EmailTemplate) && empty($hosting->Error) && empty($hosting->Warning)) {
                HostFact_API::parseSuccess($hosting->Success, true);
            } else {
                $hosting->Error = array_merge($hosting->Error, $hosting->Warning);
                HostFact_API::parseError($hosting->Error, true);
            }
        } else {
            HostFact_API::parseError($hosting->Error, true);
        }
    }
    public function unsuspend_api_action()
    {
        $hosting = $this->object;
        $hosting->Identifier = $this->_get_hosting_id();
        if($hosting->show(0, false)) {
            if($hosting->Status == 5) {
                if($hosting->suspend($hosting->Identifier) && empty($hosting->Error) && empty($hosting->Warning)) {
                    HostFact_API::parseSuccess($hosting->Success, true);
                } else {
                    $hosting->Error = array_merge($hosting->Error, $hosting->Warning);
                    HostFact_API::parseError($hosting->Error, true);
                }
            } else {
                HostFact_API::parseError(__("account not suspended"), true);
            }
        }
    }
    public function suspend_api_action()
    {
        $hosting = $this->object;
        $hosting->Identifier = $this->_get_hosting_id();
        if($hosting->show(0, false)) {
            if($hosting->Status == 5) {
                HostFact_API::parseError(__("account already suspend"), true);
            } elseif($hosting->Status != 4) {
                HostFact_API::parseError(__("hosting not active"), true);
            } elseif($hosting->suspend($hosting->Identifier) && empty($hosting->Error) && empty($hosting->Warning)) {
                HostFact_API::parseSuccess($hosting->Success, true);
            } else {
                $hosting->Error = array_merge($hosting->Error, $hosting->Warning);
                HostFact_API::parseError($hosting->Error, true);
            }
        }
    }
    public function add_api_action()
    {
        HostFact_API::beginTransaction();
        $parse_array = $this->getValidParameters();
        $hosting = $this->object;
        require_once "class/service.php";
        $service = new service();
        $inputSubscription = $this->_updateSubscriptionParameters($parse_array, $service);
        $debtor_id = $this->_get_debtor_id();
        require_once "class/debtor.php";
        $debtor = new debtor();
        if(0 < $debtor_id) {
            $debtor->Identifier = $debtor_id;
            if(!$debtor->show()) {
                HostFact_API::parseError($debtor->Error, true);
            }
        }
        $input = ["PeriodicType" => "hosting", "Debtor" => $this->_get_debtor_id(), "hosting" => ["Domain" => isset($parse_array["Domain"]) ? $parse_array["Domain"] : "", "Username" => isset($parse_array["Username"]) && $parse_array["Username"] != "" ? $parse_array["Username"] : $hosting->generateNewAccountname(ACCOUNT_GENERATION, $debtor->CompanyName, $debtor->SurName, $debtor->Initials, isset($parse_array["Domain"]) ? $parse_array["Domain"] : ""), "Password" => isset($parse_array["Password"]) && $parse_array["Password"] != "" ? $parse_array["Password"] : generatePassword(), "Status" => isset($parse_array["Status"]) ? $parse_array["Status"] : 1, "Server" => isset($parse_array["Server"]) ? $parse_array["Server"] : "", "Package" => isset($parse_array["Package"]) ? $parse_array["Package"] : "", "Comment" => isset($parse_array["Comment"]) ? $parse_array["Comment"] : ""], "subscription_invoice" => isset($parse_array["HasSubscription"]) && $parse_array["HasSubscription"] == "yes" ? "yes" : "", "SubscriptionType" => "new"];
        if($input["hosting"]["Package"] == "" && $input["subscription_invoice"] == "yes" && isset($inputSubscription["subscription"]["Product"]) && 0 < $inputSubscription["subscription"]["Product"]) {
            require_once "class/product.php";
            $product = new product();
            $product->Identifier = $inputSubscription["subscription"]["Product"];
            $product->show();
            if($product->ProductType == "hosting" && 0 < $product->PackageID) {
                $input["hosting"]["Package"] = $product->PackageID;
            }
        }
        if($service->add(array_merge($input, $inputSubscription))) {
            HostFact_API::commit();
            if(isset($parse_array["DirectCreation"]) && $parse_array["DirectCreation"] == "yes") {
                if(!$service->hosting->show(0, false) || !$service->hosting->create()) {
                    HostFact_API::parseWarning($service->hosting->Error, true);
                } else {
                    HostFact_API::parseSuccess($service->hosting->Success);
                }
            }
            $this->_show_hosting($service->hosting->Identifier);
            HostFact_API::parseSuccess($hosting->Success, true);
        } else {
            $Errors = array_unique(array_merge($service->Error, $service->hosting->Error, $service->Subscription->Error));
            HostFact_API::parseError($Errors, true);
        }
    }
    public function edit_api_action()
    {
        HostFact_API::beginTransaction();
        $parse_array = $this->getValidParameters();
        $helpers = ["ChangePasswordOnServer"];
        foreach ($helpers as $helper) {
            $helper_value = HostFact_API::getRequestParameter($helper);
            $parse_array[$helper] = $helper_value == "yes" ? "yes" : "no";
        }
        $hosting = $this->object;
        require_once "class/service.php";
        $service = new service();
        if(!$service->show($this->_get_hosting_id(), "hosting")) {
            HostFact_API::parseError($service->hosting->Error, true);
        }
        $inputSubscription = $this->_updateSubscriptionParameters($parse_array, $service, $service->hosting->PeriodicID);
        $debtor = $this->_get_debtor_id();
        $input = ["PeriodicType" => "hosting", "Debtor" => $debtor === false ? $service->hosting->Debtor : $debtor, "hosting_id" => $this->_get_hosting_id(), "subscription_invoice" => isset($parse_array["HasSubscription"]) && $parse_array["HasSubscription"] == "yes" ? "yes" : (isset($parse_array["HasSubscription"]) && $parse_array["HasSubscription"] == "no" ? "no" : (0 < $service->hosting->PeriodicID ? "yes" : "no")), "hosting" => ["Domain" => isset($parse_array["Domain"]) ? $parse_array["Domain"] : htmlspecialchars_decode($service->hosting->Domain), "Username" => isset($parse_array["Username"]) && $parse_array["Username"] != "" ? $parse_array["Username"] : htmlspecialchars_decode($service->hosting->Username), "Password" => isset($parse_array["Password"]) && $parse_array["Password"] != "" ? $parse_array["Password"] : htmlspecialchars_decode($service->hosting->Password), "Status" => isset($parse_array["Status"]) ? $parse_array["Status"] : $service->hosting->Status, "Server" => isset($parse_array["Server"]) ? $parse_array["Server"] : $service->hosting->Server, "Package" => isset($parse_array["Package"]) ? $parse_array["Package"] : $service->hosting->Package, "Comment" => isset($parse_array["Comment"]) ? $parse_array["Comment"] : htmlspecialchars_decode($service->hosting->Comment)], "SubscriptionType" => isset($parse_array["HasSubscription"]) && $parse_array["HasSubscription"] == "yes" && $inputSubscription["subscription"]["Identifier"] == "" ? "new" : "current"];
        if($service->edit(array_merge($input, $inputSubscription))) {
            HostFact_API::commit();
            if(isset($parse_array["DirectCreation"]) && $parse_array["DirectCreation"] == "yes") {
                if(!$service->hosting->show(0, false) || !$service->hosting->create()) {
                    HostFact_API::parseWarning($service->hosting->Error);
                } else {
                    HostFact_API::parseSuccess($service->hosting->Success);
                }
            } elseif(isset($parse_array["Password"]) && isset($parse_array["ChangePasswordOnServer"]) && $parse_array["ChangePasswordOnServer"] == "yes") {
                $service->hosting->Password = passcrypt($service->hosting->Password);
                if(!$service->hosting->changeAccountPassword()) {
                    HostFact_API::parseWarning($service->hosting->Error);
                }
            }
            $this->_show_hosting($service->hosting->Identifier);
            HostFact_API::parseSuccess($service->Success, true);
        } else {
            $Errors = array_unique(array_merge($service->Error, $service->hosting->Error, $service->Subscription->Error));
            HostFact_API::parseError($Errors, true);
        }
    }
    public function terminate_api_action()
    {
        if(!($hosting_id = $this->_get_hosting_id())) {
            HostFact_API::parseError(__("invalid identifier for hosting"), true);
        }
        if($this->_terminate_subscription("hosting", $hosting_id)) {
            $this->_show_hosting($hosting_id);
        }
    }
    public function create_api_action()
    {
        $hosting = $this->object;
        $hosting->Identifier = $this->_get_hosting_id();
        if($hosting->show(0, false)) {
            if(!in_array($hosting->Status, [1, 3])) {
                HostFact_API::parseError(sprintf(__("hosting is already active"), $hosting->Username), true);
            }
            HostFact_API::beginTransaction();
            if($hosting->create()) {
                HostFact_API::commit();
                if(count($hosting->Warning)) {
                    HostFact_API::parseWarning($hosting->Warning);
                }
                HostFact_API::parseSuccess($hosting->Success, true);
            } else {
                HostFact_API::parseError($hosting->Error, true);
            }
        } else {
            HostFact_API::parseError($hosting->Error, true);
        }
    }
    public function getdomainlist_api_action()
    {
        $hosting = $this->object;
        $hosting->Identifier = $this->_get_hosting_id();
        if($hosting->show()) {
            $array = [];
            foreach ($hosting->DomainList as $key => $value) {
                $array["domainlist"][] = ["Identifier" => isset($value["id"]) ? $value["id"] : "", "Domain" => isset($value["Domain"]) ? htmlspecialchars_decode($value["Domain"]) : "", "BandWidth" => isset($value["BandWidth"]) ? htmlspecialchars_decode($value["BandWidth"]) : "", "DiscSpace" => isset($value["DiscSpace"]) ? htmlspecialchars_decode($value["DiscSpace"]) : "", "Parent" => isset($value["Parent"]) ? htmlspecialchars_decode($value["Parent"]) : "", "Type" => isset($value["Type"]) ? htmlspecialchars_decode($value["Type"]) : "", "Active" => isset($value["Active"]) ? $value["Active"] : ""];
            }
            HostFact_API::setMetaData("currentresults", count($array));
            HostFact_API::parseResponse($array, true);
        } else {
            HostFact_API::parseError($hosting->Error, true);
        }
    }
    public function removefromserver_api_action()
    {
        $hosting = $this->object;
        $hosting->Identifier = $this->_get_hosting_id();
        if($hosting->show(0, false)) {
            if(!in_array($hosting->Status, [4, 5])) {
                HostFact_API::parseError(__("hosting not active"), true);
            }
            if($hosting->remove($hosting->Identifier)) {
                $hosting->changeStatus(1);
                if(!empty($hosting->api->Warning)) {
                    HostFact_API::parseWarning($hosting->Warning);
                }
                HostFact_API::parseSuccess($hosting->Success, true);
            } else {
                if(!empty($hosting->Error)) {
                    HostFact_API::parseError($hosting->Error, true);
                }
                HostFact_API::parseError(sprintf(__("account removing must be done manually"), $hosting->Username), true);
            }
        } else {
            HostFact_API::parseError($hosting->Error, true);
        }
    }
    public function delete_api_action()
    {
        $hosting = $this->object;
        $hosting->Identifier = $this->_get_hosting_id();
        if($hosting->show(0, false)) {
            HostFact_API::beginTransaction();
            $confirmServer = HostFact_API::getRequestParameter("ConfirmServer");
            $confirmDomains = HostFact_API::getRequestParameter("ConfirmDomains");
            if($hosting->delete($hosting->Identifier, "remove", $confirmServer, $confirmDomains)) {
                HostFact_API::commit();
                HostFact_API::parseSuccess($hosting->Success, true);
            } else {
                HostFact_API::parseError($hosting->Error, true);
            }
        } else {
            HostFact_API::parseError($hosting->Error, true);
        }
    }
    public function updowngrade_api_action()
    {
        $hosting = $this->object;
        $hosting->Identifier = $this->_get_hosting_id();
        $new_product_id = $this->_get_product_id();
        $periodic_details = [];
        if($periods = HostFact_API::getRequestParameter("Periods")) {
            $periodic_details["Periods"] = $periods;
        }
        if($periodic = HostFact_API::getRequestParameter("Periodic")) {
            $periodic_details["Periodic"] = $periodic;
        }
        if($invoice_cycle = HostFact_API::getRequestParameter("InvoiceCycle")) {
            $periodic_details["invoice_cycle"] = $invoice_cycle;
        }
        if($create_invoice = HostFact_API::getRequestParameter("CreateInvoice")) {
            $periodic_details["create_invoice"] = $create_invoice;
        }
        if($hosting->upDowngrade($hosting->Identifier, $new_product_id, $periodic_details)) {
            if(!empty($hosting->Warning)) {
                HostFact_API::parseWarning($hosting->Warning);
            }
            HostFact_API::parseSuccess($hosting->Success);
            $this->_show_hosting($hosting->Identifier);
            HostFact_API::parseSuccess($hosting->Success, true);
        } else {
            if(!empty($hosting->Warning)) {
                HostFact_API::parseWarning($hosting->Warning);
            }
            HostFact_API::parseError($hosting->Error, true);
        }
    }
    public function singlesignon_api_action()
    {
        $hosting = $this->object;
        $hosting->Identifier = $this->_get_hosting_id();
        if($hosting->show($hosting->Identifier, false)) {
            if(!in_array($hosting->Status, [4])) {
                HostFact_API::parseError(__("hosting not active"), true);
            } else {
                $IPAddress = HostFact_API::getRequestParameter("IPAddresses") ? HostFact_API::getRequestParameter("IPAddresses") : [];
                $result = $hosting->singleSignOn($IPAddress);
                if($result && is_array($result) && (isset($result["form_action"]) || isset($result["url"]))) {
                    return HostFact_API::parseResponse($result);
                }
                HostFact_API::parseError($hosting->Error, true);
            }
        }
    }
    protected function _get_hosting_id()
    {
        require_once "class/hosting.php";
        $hosting = new hosting();
        if($hosting_id = HostFact_API::getRequestParameter("Identifier")) {
            if(defined("API_CALL_FROM_CLIENTAREA") && API_CALL_FROM_CLIENTAREA === true && !empty(debtor_id::$ClientArea)) {
                $hosting_id = $hosting->getID("clientarea", $hosting_id, ClientArea::$debtor_id);
            } else {
                $hosting_id = $hosting->getID("identifier", $hosting_id);
            }
            return $hosting_id;
        }
        if($username = HostFact_API::getRequestParameter("Username")) {
            $hosting_id = $hosting->getID("username", $username);
            if(!$hosting_id) {
                HostFact_API::parseError(__("invalid username"), true);
            }
            if(defined("API_CALL_FROM_CLIENTAREA") && API_CALL_FROM_CLIENTAREA === true && !empty(debtor_id::$ClientArea)) {
                $hosting_id = $hosting->getID("clientarea", $hosting_id, ClientArea::$debtor_id);
            }
            return $hosting_id;
        }
        return false;
    }
    protected function _show_hosting($hosting_id)
    {
        $hosting = $this->object;
        $hosting->Identifier = $hosting_id;
        if(!$hosting->show(0, false)) {
            HostFact_API::parseError($hosting->Error, true);
        }
        require_once "class/debtor.php";
        $debtor = new debtor();
        $debtor->Identifier = $hosting->Debtor;
        $debtor->show();
        $result = [];
        foreach ($this->_object_parameters as $field => $field_info) {
            if(isset($hosting->{$field})) {
                $result[$field] = is_string($hosting->{$field}) ? htmlspecialchars_decode($hosting->{$field}) : $hosting->{$field};
            } else {
                switch ($field) {
                    case "DebtorCode":
                        $result[$field] = htmlspecialchars_decode($debtor->DebtorCode);
                        break;
                }
            }
        }
        if($this->show_hosting_usage) {
            $account_exists_on_server = $hosting->checkIfAccountExistsOnServer();
            if(($hosting->Status == 4 || $hosting->Status == 5) && $account_exists_on_server) {
                $hosting->getStats();
            }
            $result["LimitInfo"]["BandWidth"] = $hosting->BandWidth || $hosting->BandWidth == "0" && 0 < strlen($hosting->BandWidth) ? $hosting->BandWidth : 0;
            $result["LimitInfo"]["DiscSpace"] = $hosting->DiscSpace || $hosting->DiscSpace == "0" && 0 < strlen($hosting->DiscSpace) ? $hosting->DiscSpace : 0;
            $result["UsageInfo"]["BandWidth"] = $hosting->UsedBandWidth || $hosting->UsedBandWidth == "0" && 0 < strlen($hosting->UsedBandWidth) ? $hosting->UsedBandWidth : 0;
            $result["UsageInfo"]["DiscSpace"] = $hosting->UsedDiscSpace || $hosting->UsedDiscSpace == "0" && 0 < strlen($hosting->UsedDiscSpace) ? $hosting->UsedDiscSpace : 0;
        }
        $get_password = HostFact_API::getRequestParameter("GetPassword");
        if($get_password != "yes") {
            unset($result["Password"]);
        }
        if(0 < $hosting->PeriodicID) {
            require_once "class/periodic.php";
            $periodic = new periodic();
            if($periodic->show($hosting->PeriodicID)) {
                foreach ($this->_object_parameters["Subscription"]["children"] as $elementKey => $elementValue) {
                    $result["Subscription"][$elementKey] = $periodic->{$elementKey};
                }
                $result["Subscription"] = $this->_show_subscription($result["Subscription"]);
            }
        }
        $result = $this->_show_termination($result, "hosting", $hosting->Identifier);
        if(0 < $hosting->Package) {
            require_once "class/package.php";
            $package = new package();
            $package->Identifier = $hosting->Package;
            if($package->show()) {
                $result["PackageInfo"]["Identifier"] = $package->Identifier;
                $result["PackageInfo"]["Name"] = $package->PackageName;
                $result["PackageInfo"]["Type"] = $package->PackageType;
                $result["PackageInfo"]["Template"] = $package->Template;
                $result["PackageInfo"]["TemplateName"] = $package->TemplateName;
                $result["PackageInfo"]["BandWidth"] = $package->uBandWidth == 1 ? "unlimited" : $package->BandWidth;
                $result["PackageInfo"]["DiscSpace"] = $package->uDiscSpace == 1 ? "unlimited" : $package->DiscSpace;
                $result["PackageInfo"]["Domains"] = $package->uDomains == 1 ? "unlimited" : $package->Domains;
                $result["PackageInfo"]["SubDomains"] = $package->uSubDomains == 1 ? "unlimited" : $package->SubDomains;
                $result["PackageInfo"]["Domainpointers"] = $package->uDomainpointers == 1 ? "unlimited" : $package->Domainpointers;
                $result["PackageInfo"]["MySQLDatabases"] = $package->uMySQLDatabases == 1 ? "unlimited" : $package->MySQLDatabases;
                $result["PackageInfo"]["EmailAccounts"] = $package->uEmailAccounts == 1 ? "unlimited" : $package->EmailAccounts;
            }
        }
        if(0 < $hosting->Server) {
            require_once "class/server.php";
            $server = new server();
            $server->Identifier = $hosting->Server;
            if($server->show()) {
                $result["ServerInfo"]["Identifier"] = $server->Identifier;
                $result["ServerInfo"]["Name"] = $server->Name;
                $result["ServerInfo"]["Panel"] = $server->Panel;
                $result["ServerInfo"]["Location"] = $server->Location;
                $result["ServerInfo"]["Port"] = $server->Port;
                $result["ServerInfo"]["IP"] = $server->IP;
                $result["ServerInfo"]["AdditionalIP"] = $server->AdditionalIP;
                $result["ServerInfo"]["Settings"] = $server->Settings;
                $result["ServerInfo"]["DefaultDNSTemplate"] = $server->DefaultDNSTemplate;
                if(isset($server->VersionInfo["sso_support"])) {
                    $result["ServerInfo"]["SSOSupport"] = $server->VersionInfo["sso_support"];
                }
            }
        }
        global $array_hostingstatus;
        $result["Translations"] = ["Status" => isset($array_hostingstatus[$hosting->Status]) ? $array_hostingstatus[$hosting->Status] : ""];
        $result["Created"] = $this->_filter_date_db2api($hosting->Created, false);
        $result["Modified"] = $this->_filter_date_db2api($hosting->Modified, false);
        return HostFact_API::parseResponse($result);
    }
    protected function getFilterValues()
    {
        $filters = parent::getFilterValues();
        if(isset($filters["status"]) && $filters["status"]) {
            global $array_hostingstatus;
            $tmp_status = explode("|", $filters["status"]);
            foreach ($tmp_status as $tmp_status_key) {
                if(!array_key_exists($tmp_status_key, $array_hostingstatus)) {
                    HostFact_API::parseError("Invalid filter 'status'. The status '" . $tmp_status_key . "' does not exist", true);
                }
            }
        }
        return $filters;
    }
}

?>