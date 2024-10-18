<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "subscription_api_controller.php";
class service_api_controller extends subscription_api_controller
{
    public function __construct()
    {
        parent::__construct();
        HostFact_API::setObjectNames("services", "service");
        $this->addParameter("Identifier", "int");
        $this->addParameter("Debtor", "int");
        $this->addParameter("DebtorCode", "string");
        $this->addParameter("Status", "string");
        $this->addFilter("status", "string", "active");
    }
    public function list_api_action()
    {
        $this->_extra_search_filter = ["Debtor", "DebtorCode", "ProductCode", "Description"];
        $filters = $this->getFilterValues();
        $fields = ["Description", "TaxPercentage", "ProductCode", "Number", "NumberSuffix", "PriceExcl", "DiscountPercentage", "NextDate", "Debtor", "DebtorCode", "CompanyName", "SurName", "Initials", "Periods", "Periodic", "StartPeriod", "EndPeriod", "Status", "TerminationDate", "PeriodicType", "Reference", "InvoiceAuthorisation", "DebtorInvoiceAuthorisation", "AmountExcl", "AmountIncl", "Modified"];
        $sort = $filters["sort"] ? $filters["sort"] : "NextDate";
        $page_offset = $filters["offset"] / $filters["limit"] + 1;
        $selectgroup = $filters["status"] ? $filters["status"] : "";
        $searchat = $filters["searchat"] ? $filters["searchat"] : "DebtorCode|ProductCode|Description";
        $limit = $filters["limit"];
        $field_filters = array_diff_key($filters, array_flip(["offset", "limit", "sort", "order", "searchat", "searchfor"]));
        $this->object->Subscription->OtherServicesOnly = true;
        $subscription_list = $this->object->Subscription->all($fields, $sort, $filters["order"], $page_offset, $searchat, $filters["searchfor"], $field_filters, false, $limit);
        $array = [];
        foreach ($subscription_list as $key => $value) {
            if(!in_array($key, ["TotalAmountIncl", "TotalAmountExcl", "CountRows"])) {
                $array[] = ["Identifier" => $value["id"], "Debtor" => $value["Debtor"], "DebtorCode" => htmlspecialchars_decode($value["DebtorCode"]), "CompanyName" => htmlspecialchars_decode($value["CompanyName"]), "Initials" => htmlspecialchars_decode($value["Initials"]), "SurName" => htmlspecialchars_decode($value["SurName"]), "Status" => $value["Status"] == 1 && (!$this->_filter_date_site2api($value["TerminationDate"]) || $this->_filter_date_site2api($value["StartPeriod"]) < $this->_filter_date_site2api($value["TerminationDate"])) ? "active" : "terminated", "Subscription" => ["ProductCode" => $value["ProductCode"], "Description" => htmlspecialchars_decode($value["Description"]), "Number" => htmlspecialchars_decode($value["Number"]), "NumberSuffix" => htmlspecialchars_decode($value["NumberSuffix"]), "PriceExcl" => $value["PriceExcl"], "TaxPercentage" => $value["TaxPercentage"] * 100, "DiscountPercentage" => $value["DiscountPercentage"] * 100, "Periods" => $value["Periods"], "Periodic" => htmlspecialchars_decode($value["Periodic"]), "StartPeriod" => $this->_filter_date_site2api($value["StartPeriod"]), "EndPeriod" => $this->_filter_date_site2api($value["EndPeriod"]), "NextDate" => $this->_filter_date_site2api($value["NextDate"]), "TerminationDate" => $this->_filter_date_site2api($value["TerminationDate"]), "AmountExcl" => $value["AmountExcl"], "AmountIncl" => $value["AmountIncl"]], "Modified" => $this->_filter_date_db2api($value["Modified"], false)];
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
    public function show_api_action()
    {
        $service_id = $this->_get_service_id();
        $this->_show_service($service_id);
    }
    public function add_api_action()
    {
        $this->parseOutput = true;
        if($identifier = parent::add_api_action()) {
            $this->_show_service($identifier);
        }
    }
    public function edit_api_action()
    {
        $this->parseOutput = true;
        if(parent::edit_api_action() === true) {
            $this->_show_service($this->_get_service_id());
        }
    }
    public function terminate_api_action()
    {
        if(!($service_id = $this->_get_service_id())) {
            HostFact_API::parseError(__("invalid identifier for subscription"), true);
        }
        if($this->_terminate_subscription("other", $service_id)) {
            $this->_show_service($service_id);
        }
    }
    private function _get_service_id()
    {
        $service_id = HostFact_API::getRequestParameter("Identifier");
        if(defined("API_CALL_FROM_CLIENTAREA") && API_CALL_FROM_CLIENTAREA === true && !empty(debtor_id::$ClientArea)) {
            $service_id = $this->object->Subscription->getID("clientarea", $service_id, ClientArea::$debtor_id);
        }
        return $service_id;
    }
    private function _show_service($service_id)
    {
        require_once "class/periodic.php";
        $periodic = new periodic();
        if($periodic->show($service_id)) {
            require_once "class/debtor.php";
            $debtor = new debtor();
            $debtor->Identifier = $periodic->Debtor;
            $debtor->show();
            $result = [];
            foreach ($this->_object_parameters as $field => $field_info) {
                if(isset($periodic->{$field})) {
                    $result[$field] = is_string($periodic->{$field}) ? htmlspecialchars_decode($periodic->{$field}) : $periodic->{$field};
                } else {
                    switch ($field) {
                        case "DebtorCode":
                            $result[$field] = htmlspecialchars_decode($debtor->DebtorCode);
                            break;
                    }
                }
            }
            $result["Status"] = $result["Status"] == 1 && (!$this->_filter_date_site2api($result["TerminationDate"]) || $this->_filter_date_site2api($result["StartPeriod"]) < $this->_filter_date_site2api($result["TerminationDate"])) ? "active" : "terminated";
            foreach ($this->_object_parameters["Subscription"]["children"] as $elementKey => $elementValue) {
                $result["Subscription"][$elementKey] = is_string($periodic->{$elementKey}) ? htmlspecialchars_decode($periodic->{$elementKey}) : $periodic->{$elementKey};
            }
            $result["Subscription"] = $this->_show_subscription($result["Subscription"]);
            $result = $this->_show_termination($result, "other", $service_id);
            $result["Created"] = $this->_filter_date_db2api($periodic->Created, false);
            $result["Modified"] = $this->_filter_date_db2api($periodic->Modified, false);
            return HostFact_API::parseResponse($result);
        } else {
            return HostFact_API::parseError($periodic->Error, true);
        }
    }
}

?>