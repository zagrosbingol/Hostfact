<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class subscription_api_controller extends api_controller
{
    public function __construct()
    {
        parent::__construct();
        HostFact_API::setObjectNames("subscriptions", "subscription");
        require_once "class/service.php";
        $this->addParameter("HasSubscription", "string");
        $this->addParameter("Subscription", "array_with_keys");
        $this->addSubParameter("Subscription", "Debtor", "readonly");
        $this->addSubParameter("Subscription", "DebtorCode", "readonly");
        $this->addSubParameter("Subscription", "Number", "float");
        $this->addSubParameter("Subscription", "NumberSuffix", "string");
        $this->addSubParameter("Subscription", "ProductCode", "string");
        $this->addSubParameter("Subscription", "Description", "text");
        $this->addSubParameter("Subscription", "PriceExcl", "float");
        $this->addSubParameter("Subscription", "PriceIncl", "float");
        $this->addSubParameter("Subscription", "TaxPercentage", "float");
        $this->addSubParameter("Subscription", "DiscountPercentage", "float");
        $this->addSubParameter("Subscription", "Periods", "int");
        $this->addSubParameter("Subscription", "Periodic", "string");
        $this->addSubParameter("Subscription", "StartPeriod", "date");
        $this->addSubParameter("Subscription", "EndPeriod", "readonly");
        $this->addSubParameter("Subscription", "NextDate", "date");
        $this->addSubParameter("Subscription", "ContractPeriods", "int");
        $this->addSubParameter("Subscription", "ContractPeriodic", "string");
        $this->addSubParameter("Subscription", "StartContract", "date");
        $this->addSubParameter("Subscription", "EndContract", "date");
        $this->addSubParameter("Subscription", "TerminationDate", "date");
        $this->addSubParameter("Subscription", "Reminder", "string");
        $this->addSubParameter("Subscription", "InvoiceAuthorisation", "string");
        $this->addSubParameter("Subscription", "AmountExcl", "readonly");
        $this->addSubParameter("Subscription", "AmountIncl", "readonly");
        $this->addFilter("status", "string", "active");
        $this->addFilter("created", "filter_datetime");
        $this->addFilter("modified", "filter_datetime");
        $this->object = new service();
    }
    public function list_api_action()
    {
        $this->_extra_search_filter = ["Debtor", "DebtorCode", "ProductCode", "Description"];
        $this->addParameter("Created", "readonly");
        $this->addParameter("Modified", "readonly");
        $filters = $this->getFilterValues();
        $fields = ["Description", "TaxPercentage", "ProductCode", "Number", "NumberSuffix", "PriceExcl", "DiscountPercentage", "NextDate", "Debtor", "DebtorCode", "CompanyName", "SurName", "Initials", "Periods", "Periodic", "StartPeriod", "EndPeriod", "Status", "TerminationDate", "PeriodicType", "Reference", "InvoiceAuthorisation", "DebtorInvoiceAuthorisation", "AmountExcl", "AmountIncl", "Modified"];
        $sort = $filters["sort"] ? $filters["sort"] : "NextDate";
        $page_offset = $filters["offset"] / $filters["limit"] + 1;
        $selectgroup = $filters["status"] ? $filters["status"] : "";
        $searchat = $filters["searchat"] ? $filters["searchat"] : "DebtorCode|ProductCode|Description";
        $limit = $filters["limit"];
        $field_filters = array_diff_key($filters, array_flip(["offset", "limit", "sort", "order", "searchat", "searchfor"]));
        $subscription_list = $this->object->Subscription->all($fields, $sort, $filters["order"], $page_offset, $searchat, $filters["searchfor"], $field_filters, false, $limit);
        $array = [];
        foreach ($subscription_list as $key => $value) {
            if(!in_array($key, ["TotalAmountIncl", "TotalAmountExcl", "CountRows"])) {
                $array[] = ["Identifier" => $value["id"], "Debtor" => $value["Debtor"], "DebtorCode" => htmlspecialchars_decode($value["DebtorCode"]), "CompanyName" => htmlspecialchars_decode($value["CompanyName"]), "Initials" => htmlspecialchars_decode($value["Initials"]), "SurName" => htmlspecialchars_decode($value["SurName"]), "ProductCode" => htmlspecialchars_decode($value["ProductCode"]), "Description" => htmlspecialchars_decode($value["Description"]), "Number" => $value["Number"], "NumberSuffix" => $value["NumberSuffix"], "PriceExcl" => $value["PriceExcl"], "TaxPercentage" => $value["TaxPercentage"] * 100, "DiscountPercentage" => $value["DiscountPercentage"] * 100, "Periods" => $value["Periods"], "Periodic" => htmlspecialchars_decode($value["Periodic"]), "StartPeriod" => $this->_filter_date_site2api($value["StartPeriod"]), "EndPeriod" => $this->_filter_date_site2api($value["EndPeriod"]), "NextDate" => $this->_filter_date_site2api($value["NextDate"]), "TerminationDate" => $this->_filter_date_site2api($value["TerminationDate"]), "PeriodicType" => htmlspecialchars_decode($value["PeriodicType"]), "Reference" => $value["Reference"], "Status" => $value["Status"] == 1 && (!$this->_filter_date_site2api($value["TerminationDate"]) || $this->_filter_date_site2api($value["StartPeriod"]) < $this->_filter_date_site2api($value["TerminationDate"])) ? "active" : "terminated", "AmountExcl" => $value["AmountExcl"], "AmountIncl" => $value["AmountIncl"], "Modified" => $this->_filter_date_db2api($value["Modified"], false)];
            }
        }
        HostFact_API::setMetaData("totalresults", $subscription_list["CountRows"]);
        HostFact_API::setMetaData("currentresults", count($array));
        HostFact_API::setMetaData("offset", $filters["offset"]);
        if(!empty($this->_valid_filter_input)) {
            HostFact_API::setMetaData("filters", $this->_valid_filter_input);
        }
        HostFact_API::parseResponse($array, true);
    }
    public function add_api_action()
    {
        HostFact_API::beginTransaction();
        $parse_array = $this->getValidParameters();
        $service = $this->object;
        $parse_array["Debtor"] = $this->_get_debtor_id();
        $parse_array["HasSubscription"] = "yes";
        $inputSubscription = $this->_updateSubscriptionParameters($parse_array, $service);
        $input = ["PeriodicType" => "other", "Debtor" => $parse_array["Debtor"], "subscription_invoice" => "yes", "SubscriptionType" => "new"];
        if($service->add(array_merge($input, $inputSubscription))) {
            HostFact_API::commit();
            if(count($service->Warning)) {
                HostFact_API::parseWarning($service->Warning);
            }
            HostFact_API::parseSuccess($service->Success);
            if(isset($this->parseOutput) && $this->parseOutput === true) {
                return $service->Subscription->Identifier;
            }
            $this->_return_subscription($service->Subscription->Identifier);
        } else {
            $Errors = array_unique(array_merge($service->Error, $service->Subscription->Error));
            HostFact_API::parseError($Errors, true);
        }
    }
    public function edit_api_action()
    {
        HostFact_API::beginTransaction();
        $parse_array = $this->getValidParameters();
        $service = $this->object;
        if(!$service->show($this->_get_subscription_id(), "other")) {
            HostFact_API::parseError($service->Subscription->Error, true);
        }
        $parse_array["Debtor"] = $this->_get_debtor_id();
        if(!$parse_array["Debtor"]) {
            $parse_array["Debtor"] = $service->Subscription->Debtor;
        }
        $parse_array["HasSubscription"] = "yes";
        $inputSubscription = $this->_updateSubscriptionParameters($parse_array, $service, $service->Subscription->Identifier);
        $input = ["PeriodicType" => "other", "Debtor" => $parse_array["Debtor"], "Other" => [], "subscription_invoice" => "yes", "SubscriptionType" => "current"];
        if($service->edit(array_merge($input, $inputSubscription))) {
            HostFact_API::commit();
            if(!empty($service->Subscription->Warning)) {
                HostFact_API::parseWarning($service->Subscription->Warning);
            }
            if(isset($this->parseOutput) && $this->parseOutput === true) {
                return true;
            }
            $this->_return_subscription($service->Subscription->Identifier);
            HostFact_API::parseSuccess($service->Subscription->Success, true);
        } else {
            $Errors = array_unique(array_merge($service->Error, $service->Subscription->Error));
            HostFact_API::parseError($Errors, true);
        }
    }
    public function terminate_api_action()
    {
        if(!($service_id = $this->_get_subscription_id())) {
            HostFact_API::parseError(__("invalid identifier for subscription"), true);
        }
        if($this->_terminate_subscription("other", $service_id)) {
            $this->_return_subscription($service_id);
        }
    }
    public function show_api_action()
    {
        $subscription_id = $this->_get_subscription_id();
        $this->_return_subscription($subscription_id);
    }
    private function _get_subscription_id()
    {
        return HostFact_API::getRequestParameter("Identifier");
    }
    protected function _updateSubscriptionParameters($parse_array, $service, $PeriodicID = 0)
    {
        $input = [];
        $check_price_period = !isset($parse_array["Subscription"]["PriceExcl"]) ? true : false;
        if(isset($parse_array["Subscription"]["ProductCode"]) && $parse_array["Subscription"]["ProductCode"] != $service->Subscription->ProductCode) {
            if(empty($PeriodicID) && (!isset($parse_array["HasSubscription"]) || $parse_array["HasSubscription"] == "no")) {
                HostFact_API::parseError(__("no active or new subscription"), true);
            }
            require_once "class/product.php";
            $product = new product();
            $product->Identifier = $this->_get_product_id(false, $parse_array["Subscription"]["ProductCode"]);
            if($product->show()) {
                if($product->Status == 9) {
                    HostFact_API::parseError(__("invalid identifier for product"), true);
                }
                $parse_array["Subscription"]["Product"] = $product->Identifier;
                $parse_array["Subscription"]["Description"] = isset($parse_array["Subscription"]["Description"]) ? $parse_array["Subscription"]["Description"] : htmlspecialchars_decode($product->ProductKeyPhrase);
                $parse_array["Subscription"]["TaxPercentage"] = isset($parse_array["Subscription"]["TaxPercentage"]) ? $parse_array["Subscription"]["TaxPercentage"] : htmlspecialchars_decode($product->TaxPercentage * 100);
                $parse_array["Subscription"]["Periodic"] = isset($parse_array["Subscription"]["Periodic"]) ? $parse_array["Subscription"]["Periodic"] : htmlspecialchars_decode($product->PricePeriod);
                $parse_array["Subscription"]["PriceExcl"] = isset($parse_array["Subscription"]["PriceExcl"]) ? $parse_array["Subscription"]["PriceExcl"] : $product->PriceExcl;
            } elseif($parse_array["Subscription"]["ProductCode"] == "") {
                $parse_array["Subscription"]["Product"] = 0;
            }
        }
        $_startperiod = isset($parse_array["Subscription"]["StartPeriod"]) ? rewrite_date_db2site($parse_array["Subscription"]["StartPeriod"]) : ($service->Subscription->StartPeriod ? $service->Subscription->StartPeriod : date("Ymd"));
        $input["subscription"] = ["Identifier" => 0 < $PeriodicID ? $PeriodicID : "", "Product" => isset($parse_array["Subscription"]["Product"]) ? $parse_array["Subscription"]["Product"] : ($service->Subscription->ProductCode ? $this->_get_product_id(false, $service->Subscription->ProductCode) : ""), "Number" => isset($parse_array["Subscription"]["Number"]) ? $parse_array["Subscription"]["Number"] : $service->Subscription->Number, "NumberSuffix" => isset($parse_array["Subscription"]["NumberSuffix"]) ? $parse_array["Subscription"]["NumberSuffix"] : $service->subscription->NumberSuffix, "Description" => isset($parse_array["Subscription"]["Description"]) ? $parse_array["Subscription"]["Description"] : htmlspecialchars_decode($service->Subscription->Description), "PriceExcl" => isset($parse_array["Subscription"]["PriceExcl"]) ? $parse_array["Subscription"]["PriceExcl"] : $service->Subscription->PriceExcl, "TaxPercentage" => $this->_check_taxpercentage(isset($parse_array["Subscription"]["TaxPercentage"]) ? (double) $parse_array["Subscription"]["TaxPercentage"] / 100 : $service->Subscription->TaxPercentage), "DiscountPercentage" => isset($parse_array["Subscription"]["DiscountPercentage"]) ? $parse_array["Subscription"]["DiscountPercentage"] : (double) $service->Subscription->DiscountPercentage * 100, "Periods" => isset($parse_array["Subscription"]["Periods"]) ? $parse_array["Subscription"]["Periods"] : $service->Subscription->Periods, "Periodic" => isset($parse_array["Subscription"]["Periodic"]) ? $parse_array["Subscription"]["Periodic"] : htmlspecialchars_decode($service->Subscription->Periodic), "StartPeriod" => $_startperiod, "EndPeriod" => "", "NextDate" => isset($parse_array["Subscription"]["NextDate"]) ? rewrite_date_db2site($parse_array["Subscription"]["NextDate"]) : $service->Subscription->NextDate, "ContractPeriods" => isset($parse_array["Subscription"]["ContractPeriods"]) ? $parse_array["Subscription"]["ContractPeriods"] : $service->Subscription->ContractPeriods, "ContractPeriodic" => isset($parse_array["Subscription"]["ContractPeriodic"]) ? $parse_array["Subscription"]["ContractPeriodic"] : htmlspecialchars_decode($service->Subscription->ContractPeriodic), "StartContract" => isset($parse_array["Subscription"]["StartContract"]) ? rewrite_date_db2site($parse_array["Subscription"]["StartContract"]) : ($service->Subscription->StartContract ? $service->Subscription->StartContract : (0 < $PeriodicID ? "" : $_startperiod)), "EndContract" => isset($parse_array["Subscription"]["EndContract"]) ? rewrite_date_db2site($parse_array["Subscription"]["EndContract"]) : $service->Subscription->EndContract, "TerminationDate" => isset($parse_array["Subscription"]["TerminationDate"]) ? rewrite_date_db2site($parse_array["Subscription"]["TerminationDate"]) : ($service->Subscription->TerminationDate ? $service->Subscription->TerminationDate : ""), "Reminder" => isset($parse_array["Subscription"]["Reminder"]) ? $parse_array["Subscription"]["Reminder"] : htmlspecialchars_decode($service->Subscription->Reminder), "InvoiceAuthorisation" => isset($parse_array["Subscription"]["InvoiceAuthorisation"]) ? $parse_array["Subscription"]["InvoiceAuthorisation"] : htmlspecialchars_decode($service->Subscription->InvoiceAuthorisation)];
        if(isset($parse_array["Subscription"]["ContractPeriods"]) || isset($parse_array["Subscription"]["ContractPeriodic"]) || isset($parse_array["Subscription"]["StartContract"]) || isset($parse_array["Subscription"]["EndContract"])) {
            if($input["subscription"]["Periods"] == $input["subscription"]["ContractPeriods"] && $input["subscription"]["Periodic"] == $input["subscription"]["ContractPeriodic"] && !isset($parse_array["Subscription"]["StartContract"]) && !isset($parse_array["Subscription"]["EndContract"])) {
                $input["ContractPeriod"] = "billing";
            } else {
                $input["ContractPeriod"] = "custom";
            }
        } else {
            $input["ContractPeriod"] = $service->Subscription->Periods == $service->Subscription->ContractPeriods && $service->Subscription->Periodic == $service->Subscription->ContractPeriodic ? "billing" : "custom";
        }
        if($check_price_period === true) {
            $productcode = isset($parse_array["Subscription"]["ProductCode"]) ? $parse_array["Subscription"]["ProductCode"] : $service->Subscription->ProductCode;
            $input["subscription"]["PriceExcl"] = $this->_checkPricePeriod($productcode, $input["subscription"]["Periods"], $input["subscription"]["Periodic"], $input["subscription"]["PriceExcl"]);
        }
        if(isset($parse_array["Subscription"]["AutoRenew"]) && in_array($parse_array["Subscription"]["AutoRenew"], ["yes", "once", "no"])) {
            $input["subscription"]["AutoRenew"] = $parse_array["Subscription"]["AutoRenew"];
        }
        return $input;
    }
    private function _return_subscription($subscriptionID)
    {
        $result = [];
        $result["Identifier"] = $subscriptionID;
        require_once "class/periodic.php";
        $periodic = new periodic();
        if($periodic->show($subscriptionID)) {
            require_once "class/debtor.php";
            $debtor = new debtor();
            $debtor->Identifier = $periodic->Debtor;
            $debtor->show();
            foreach ($this->_object_parameters["Subscription"]["children"] as $elementKey => $elementValue) {
                $result[$elementKey] = $periodic->{$elementKey};
            }
            $result["DebtorCode"] = $debtor->DebtorCode;
        } else {
            HostFact_API::parseError($periodic->Error, true);
        }
        $result = $this->_show_subscription($result, true);
        $result["Created"] = $this->_filter_date_db2api($periodic->Created, false);
        $result["Modified"] = $this->_filter_date_db2api($periodic->Modified, false);
        return HostFact_API::parseResponse($result);
    }
    protected function _show_subscription($subscription, $with_debtor_data = false)
    {
        isset($subscription["TaxPercentage"]);
        isset($subscription["TaxPercentage"]) ? $subscription["TaxPercentage"] : "";
        isset($subscription["DiscountPercentage"]);
        isset($subscription["DiscountPercentage"]) ? $subscription["DiscountPercentage"] : "";
        is_date(rewrite_date_site2db($subscription["StartPeriod"]));
        isset($subscription["StartPeriod"]) && is_date(rewrite_date_site2db($subscription["StartPeriod"])) ? $subscription["StartPeriod"] : "";
        is_date(rewrite_date_site2db($subscription["EndPeriod"]));
        isset($subscription["EndPeriod"]) && is_date(rewrite_date_site2db($subscription["EndPeriod"])) ? $subscription["EndPeriod"] : "";
        is_date(rewrite_date_site2db($subscription["NextDate"]));
        isset($subscription["NextDate"]) && is_date(rewrite_date_site2db($subscription["NextDate"])) ? $subscription["NextDate"] : "";
        is_date(rewrite_date_site2db($subscription["StartContract"]));
        isset($subscription["StartContract"]) && is_date(rewrite_date_site2db($subscription["StartContract"])) ? $subscription["StartContract"] : "";
        is_date(rewrite_date_site2db($subscription["EndContract"]));
        isset($subscription["EndContract"]) && is_date(rewrite_date_site2db($subscription["EndContract"])) ? $subscription["EndContract"] : "";
        is_date(rewrite_date_site2db($subscription["TerminationDate"]));
        isset($subscription["TerminationDate"]) && strlen($subscription["TerminationDate"]) && is_date(rewrite_date_site2db($subscription["TerminationDate"])) ? $subscription["TerminationDate"] : "";
        if($with_debtor_data === false) {
            unset($subscription["Debtor"]);
            unset($subscription["DebtorCode"]);
        } else {
            $subscription = $this->_show_termination($subscription, "other", $subscription["Identifier"]);
        }
        return $subscription;
    }
    protected function _show_termination($result, $service_type, $service_id)
    {
        require_once "class/terminationprocedure.php";
        $termination = new Termination_Model();
        if($termination->show($service_type, $service_id)) {
            $result["Termination"] = ["Date" => $termination->Date, "Created" => $termination->Created, "Status" => $termination->Status];
        }
        return $result;
    }
    protected function _terminate_subscription($service_type, $service_id, $service_has_expiration_date = false)
    {
        $parse_array = $this->getValidParameters();
        $end_date = false;
        if(HostFact_API::getRequestParameter("Date")) {
            $end_date = HostFact_API::getRequestParameter("Date");
        } elseif(isset($parse_array["Subscription"]["TerminationDate"]) && $parse_array["Subscription"]["TerminationDate"]) {
            $end_date = $parse_array["Subscription"]["TerminationDate"];
        }
        require_once "class/terminationprocedure.php";
        $termination = new Termination_Model();
        if(HostFact_API::getRequestParameter("Reason")) {
            $termination->Reason = HostFact_API::getRequestParameter("Reason");
        }
        if(defined("API_CALL_FROM_CLIENTAREA") && API_CALL_FROM_CLIENTAREA === true) {
            $termination->Who = "clientarea";
            $termination->IP = HostFact_API::getRequestParameter("IP");
        }
        $from_customerpanel = HostFact_API::getRequestParameter("ApproveTermination") == "yes" ? true : false;
        if(0 < $service_id && $termination->terminateService($service_type, $service_id, $end_date, $service_has_expiration_date, $from_customerpanel)) {
            HostFact_API::parseSuccess(sprintf(__("termination successfully created via api"), rewrite_date_db2site($termination->Date)));
            if(HostFact_API::getRequestParameter("ShowInCalendar") == "yes") {
                require_once "class/service.php";
                $service = new service();
                if($service->show($service_id, $service_type) && 0 < $service->Subscription->Identifier) {
                    require_once "class/agenda.php";
                    $agenda = new agenda();
                    $agenda->Description = sprintf(__("default description in agenda for subscription termination"), $service->Subscription->Description, rewrite_date_db2site($termination->Date));
                    $agenda->Date = $termination->Date;
                    $agenda->WholeDay = 1;
                    $agenda->ItemType = "periodic";
                    $agenda->ItemID = $service->Subscription->Identifier;
                    $agenda->EmailNotify = 0;
                    $agenda->add();
                }
            }
            if(HostFact_API::getRequestParameter("SendNotification") == "yes") {
                $termination_result = $termination->listTerminations(["termination_ids" => [$termination->id]]);
                $termination_info = $termination_result[0];
                $service_url = "";
                switch ($service_type) {
                    case "domain":
                        $service_url = "domains.php?page=show&id=" . $service_id;
                        break;
                    case "hosting":
                        $service_url = "hosting.php?page=show&id=" . $service_id;
                        break;
                    default:
                        global $_module_instances;
                        if(isset($_module_instances[$service_type])) {
                            $service_url = "modules.php?module=" . $service_type . "&page=show&id= " . $service_id;
                        } else {
                            $service_url = "services.php?page=show&id=" . $service_id;
                        }
                        $termination_info->ServiceURL = $service_url;
                        require_once "class/clientareaprofiles.php";
                        $ClientareaProfiles = new ClientareaProfiles_Model();
                        if($debtor->ClientareaProfile && 0 < $debtor->ClientareaProfile) {
                            $ClientareaProfiles->id = $debtor->ClientareaProfile;
                            $ClientareaProfiles->show();
                        } else {
                            $ClientareaProfiles->showDefault();
                        }
                        $termination_info->ClientareaProfileObject = $ClientareaProfiles;
                        require_once "class/email.php";
                        $email = new email();
                        $email->Sender = getFirstMailAddress(CLIENTAREA_NOTIFICATION_EMAILADDRESS);
                        $email->Recipient = CLIENTAREA_NOTIFICATION_EMAILADDRESS;
                        $email->Subject = __("subject service terminated");
                        $email->Message = file_get_contents("includes/language/" . LANGUAGE . "/mail.service.terminated.phtml");
                        $email->Debtor = $termination_info->Debtor;
                        $email->add(["termination" => $termination_info, "debtor" => $debtor]);
                        $email->Debtor = 0;
                        $email->sent();
                }
            }
            return true;
        }
        HostFact_API::parseError($termination->Error, true);
    }
    protected function getFilterValues()
    {
        $this->_extra_sort_parameter = array_keys($this->_object_parameters["Subscription"]["children"]);
        $filters = parent::getFilterValues();
        return $filters;
    }
}

?>