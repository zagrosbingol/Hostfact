<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class api_controller
{
    protected $_object_parameters;
    protected $_objet_filters;
    protected $_valid_filter_input;
    public function __construct()
    {
        $this->_object_parameters = [];
        $this->_object_filters = [];
        $this->_valid_filter_input = [];
        $this->_extra_sort_parameter = [];
        $this->_extra_search_filter = [];
        $this->addFilter("offset", "int", 0);
        $this->addFilter("limit", "int", defined("API_CALL_FROM_CLIENTAREA") && API_CALL_FROM_CLIENTAREA ? 50000 : 1000);
        $this->addFilter("sort", "string", "");
        $this->addFilter("order", "string", "ASC");
        $this->addFilter("searchat", "string", "");
        $this->addFilter("searchfor", "string", "");
        $this->addFilter("debtor", "int", 0);
    }
    protected function addParameter($key, $type = "string", $options = [])
    {
        $this->_object_parameters[$key] = [];
        $this->_object_parameters[$key]["type"] = $type;
        return true;
    }
    protected function addSubParameter($parent_key, $key, $type = "string", $options = [])
    {
        $this->_object_parameters[$parent_key]["children"][$key] = [];
        $this->_object_parameters[$parent_key]["children"][$key]["type"] = $type;
        return true;
    }
    protected function validateParameter($key, $input_value, $parent_key = false, $array_index = 0)
    {
        $_object_parameter = $parent_key ? $this->_object_parameters[$parent_key]["children"][$key] : $this->_object_parameters[$key];
        $_object_parameter_name = $parent_key ? $parent_key . "[" . $array_index . "]." . $key : $key;
        if($input_value !== false) {
            switch ($_object_parameter["type"]) {
                case "array":
                    if(is_array($input_value)) {
                        $valid = true;
                        $valid_array = [];
                        foreach ($input_value as $k => $child_array) {
                            foreach ($_object_parameter["children"] as $child_key => $child_value) {
                                if($this->validateParameter($child_key, isset($child_array[$child_key]) ? $child_array[$child_key] : false, $_object_parameter_name, $k)) {
                                    if(isset($child_array[$child_key])) {
                                        $valid_array[$k][$child_key] = $child_array[$child_key];
                                    }
                                } else {
                                    $valid = false;
                                }
                            }
                        }
                        if($valid === false || empty($valid_array)) {
                            return false;
                        }
                        return $valid_array;
                    }
                    break;
                case "array_with_keys":
                    if(is_array($input_value)) {
                        $valid = true;
                        $valid_array = [];
                        foreach ($_object_parameter["children"] as $child_key => $child_value) {
                            $result_from_child = $this->validateParameter($child_key, isset($input_value[$child_key]) ? $input_value[$child_key] : false, $_object_parameter_name);
                            if($result_from_child !== false) {
                                if(strpos($this->_object_parameters[$_object_parameter_name]["children"][$child_key]["type"], "array") !== false) {
                                    if(is_array($result_from_child)) {
                                        $valid_array[$child_key] = $result_from_child;
                                    }
                                } elseif(isset($input_value[$child_key])) {
                                    $valid_array[$child_key] = $input_value[$child_key];
                                }
                            } else {
                                $valid = false;
                            }
                        }
                        if($valid === false || empty($valid_array)) {
                            return false;
                        }
                        return $valid_array;
                    }
                    break;
                case "array_raw":
                    if(is_array($input_value)) {
                        $valid = true;
                        $valid_array = $input_value;
                        return $valid_array;
                    }
                    return [];
                    break;
                case "text":
                case "string":
                    if(is_string($input_value)) {
                        return true;
                    }
                    break;
                case "int":
                    if(0 < strlen($input_value) && is_numeric($input_value) && intval($input_value) == $input_value && 0 <= $input_value) {
                        return true;
                    }
                    break;
                case "default_int":
                    if(is_numeric($input_value) && intval($input_value) == $input_value && -1 <= $input_value) {
                        return true;
                    }
                    break;
                case "double":
                case "float":
                    if(filter_var($input_value, FILTER_VALIDATE_FLOAT) !== false) {
                        return true;
                    }
                    break;
                case "datetime":
                    if(date("Y-m-d H:i:s", strtotime($input_value)) == $input_value) {
                        return true;
                    }
                    break;
                case "date":
                    if(is_date($input_value) || $input_value == "") {
                        return true;
                    }
                    break;
                case "readonly":
                    if($parent_key) {
                        HostFact_API::parseError($_object_parameter_name . " is readonly");
                    }
                    return false;
                    break;
                default:
                    HostFact_API::parseError("Invalid type for '" . $_object_parameter_name . "'");
                    return false;
            }
        } else {
            return true;
        }
    }
    protected function getValidParameters()
    {
        $parameters = [];
        foreach ($this->_object_parameters as $field => $field_info) {
            $input_value = HostFact_API::getRequestParameter($field);
            $valid = $this->validateParameter($field, $input_value);
            if($input_value !== false && $valid === true) {
                $parameters[$field] = esc($input_value);
            } elseif($input_value !== false && is_array($valid)) {
                $parameters[$field] = $valid;
            }
        }
        if(HostFact_API::hasErrors() === true) {
            HostFact_API::parseResponse();
        }
        return $parameters;
    }
    protected function addFilter($key, $type = "string", $default = "")
    {
        $this->_object_filters[$key] = [];
        $this->_object_filters[$key]["type"] = $type;
        $this->_object_filters[$key]["default"] = $default;
    }
    protected function _get_product_id($product_id = false, $productCode = false)
    {
        require_once "class/product.php";
        $product = new product();
        if($product_id !== false || ($product_id = HostFact_API::getRequestParameter("Product"))) {
            return $product->getID("identifier", $product_id);
        }
        if($productCode !== false || ($productCode = HostFact_API::getRequestParameter("ProductCode"))) {
            if($product_id = $product->getID("productcode", $productCode)) {
                return $product_id;
            }
            HostFact_API::parseError(__("invalid productcode"), true);
        } else {
            return false;
        }
    }
    protected function _check_total_taxpercentages($taxTotalPercentage)
    {
        global $array_total_taxpercentages;
        if(!array_key_exists("" . $taxTotalPercentage, $array_total_taxpercentages) && !isEmptyFloat($taxTotalPercentage)) {
            HostFact_API::parseError(sprintf(__("total tax percentage dont exists"), $taxTotalPercentage * 100), false);
        }
        return $taxTotalPercentage;
    }
    protected function _check_taxpercentage($taxPercentage)
    {
        global $array_taxpercentages;
        if(!array_key_exists("" . $taxPercentage, $array_taxpercentages) && !isEmptyFloat($taxPercentage)) {
            HostFact_API::parseError(sprintf(__("tax percentage dont exists"), $taxPercentage * 100), false);
        }
        return $taxPercentage;
    }
    protected function _get_debtor_id($debtor_id = false)
    {
        require_once "class/debtor.php";
        $debtor = new debtor();
        if($debtor_id !== false || ($debtor_id = HostFact_API::getRequestParameter("Debtor"))) {
            return $debtor->getID("identifier", intval($debtor_id));
        }
        if($debtorCode = HostFact_API::getRequestParameter("DebtorCode")) {
            if($debtor_id = $debtor->getID("debtorcode", $debtorCode)) {
                return $debtor_id;
            }
            HostFact_API::parseError(__("invalid debtorcode"), true);
        } else {
            return false;
        }
    }
    protected function _filter_date_site2api($date, $dateonly = true)
    {
        if($date == "") {
            return "";
        }
        if($dateonly === true) {
            $date = new DateTime(rewrite_date_site2db($date));
            $date = $date->format("Y-m-d");
        } elseif($dateonly === false) {
            $date = new DateTime(rewrite_date_site2db($date, DATE_FORMAT . " %H:%i:%s"));
            $date = $date->format("Y-m-d H:i:s");
        }
        return $this->_filter_date_db2api($date, $dateonly);
    }
    protected function _filter_date_db2api($date, $dateonly = true)
    {
        if($date != "" && in_array(substr($date, 0, 10), ["0000-00-00", "1970-01-01"])) {
            return "";
        }
        if($dateonly === true) {
            return substr($date, 0, 10);
        }
        return $date;
    }
    protected function _checkProductTypeReference($type, $id)
    {
        if($type == "") {
            return 0;
        }
        global $array_producttypes;
        if(!array_key_exists($type, $array_producttypes)) {
            $this->object->Error[] = sprintf(__("producttype not available"), $type);
            return false;
        }
        require_once "class/service.php";
        $service = new service();
        if(!$service->show($id, $type)) {
            $this->object->Error[] = sprintf(__("producttype reference not available"), $type, $id);
            return false;
        }
        return $id;
    }
    protected function _checkProductCode($elementData)
    {
        require_once "class/product.php";
        $product = new product();
        $product->ProductCode = $elementData["ProductCode"];
        if($product->show() && $product->Status != 9) {
            $elementData["Periodic"] = isset($elementData["Periodic"]) ? $elementData["Periodic"] : $product->PricePeriod;
            $elementData["PeriodicType"] = isset($elementData["PeriodicType"]) ? $elementData["PeriodicType"] : (!isset($product->PricePeriod) || $product->PricePeriod == "" ? "once" : "period");
            $elementData["Description"] = isset($elementData["Description"]) ? $elementData["Description"] : htmlspecialchars_decode($product->ProductKeyPhrase);
            $elementData["NumberSuffix"] = isset($elementData["NumberSuffix"]) ? $elementData["NumberSuffix"] : " " . htmlspecialchars_decode($product->NumberSuffix);
            $elementData["TaxPercentage"] = isset($elementData["TaxPercentage"]) ? $elementData["TaxPercentage"] : $product->TaxPercentage * 100;
            $elementData["PriceExcl"] = isset($elementData["PriceExcl"]) ? $elementData["PriceExcl"] : $product->PriceExcl;
            return $elementData;
        }
        if($product->Status == 9) {
            $this->object->Error[] = sprintf(__("product not available"), $elementData["ProductCode"]);
            return false;
        }
        $this->object->Error = array_merge($this->object->Error, $product->Error);
        return false;
    }
    protected function _checkPricePeriod($productcode, $periods, $periodic_unit, $price)
    {
        require_once "class/periodic.php";
        $periodic = new periodic();
        $periodic->ProductCode = $productcode;
        $periodic->Periods = $periods;
        $periodic->Periodic = $periodic_unit;
        $periodic->PriceExcl = $price;
        $periodic->checkPricePeriod(true);
        return $periodic->PriceExcl;
    }
    protected function _getPeriodicID($product_type, $reference)
    {
        require_once "class/periodic.php";
        $periodic = new periodic();
        if($periodic_id = $periodic->lookupSubscription($product_type, $reference)) {
            return $periodic_id;
        }
        return false;
    }
    protected function getFilterValues()
    {
        $filters = [];
        foreach ($this->_object_filters as $key => $filter) {
            $input_value = HostFact_API::getRequestParameter($key);
            if($input_value !== false) {
                switch ($filter["type"]) {
                    case "string":
                        if(is_string($input_value)) {
                            $filters[$key] = $input_value;
                        } elseif(is_numeric($input_value) && intval($input_value) == $input_value) {
                            $filters[$key] = $input_value;
                        }
                        break;
                    case "int":
                        if(is_numeric($input_value) && intval($input_value) == $input_value && 0 <= $input_value) {
                            $filters[$key] = $input_value;
                        }
                        break;
                    case "filter_date":
                        $filters[$key] = ["from" => isset($input_value["from"]) ? $input_value["from"] : "", "to" => isset($input_value["to"]) ? $input_value["to"] : ""];
                        if($filters[$key]["from"] && !is_date($filters[$key]["from"]) || $input_value["to"] && !is_date($filters[$key]["to"])) {
                            HostFact_API::parseError("Invalid date(s) for '" . $key . "'. Please enter valid dates with format yyyy-mm-dd", true);
                        }
                        break;
                    case "filter_datetime":
                        $filters[$key] = ["from" => isset($input_value["from"]) ? $input_value["from"] : "", "to" => isset($input_value["to"]) ? $input_value["to"] : ""];
                        $valid_datetimes = true;
                        if($filters[$key]["from"] && !is_date($filters[$key]["from"]) || $input_value["to"] && !is_date($filters[$key]["to"])) {
                            $valid_datetimes = false;
                        } else {
                            if($filters[$key]["from"]) {
                                $dt = new DateTime($filters[$key]["from"]);
                                if($dt === false || array_sum($dt->getLastErrors())) {
                                    $valid_datetimes = false;
                                } else {
                                    $filters[$key]["from"] = $dt->format("Y-m-d H:i:s");
                                }
                            }
                            if($filters[$key]["to"]) {
                                $dt = new DateTime($filters[$key]["to"]);
                                if($dt === false || array_sum($dt->getLastErrors())) {
                                    $valid_datetimes = false;
                                } elseif(strlen($filters[$key]["to"]) < 19) {
                                    $last_time_of_the_day = $dt->format("Y-m-d") . " 23:59:59";
                                    $filters[$key]["to"] = $filters[$key]["to"] . substr($last_time_of_the_day, strlen($filters[$key]["to"]));
                                }
                            }
                        }
                        if($valid_datetimes === false) {
                            HostFact_API::parseError("Invalid datetime(s) for '" . $key . "'. Please enter valid dates with format 'yyyy-mm-dd' or 'yyyy-mm-dd hh:ii:ss'", true);
                        }
                        break;
                    default:
                        if(isset($filters[$key])) {
                            $is_filter_input_valid = false;
                            switch ($key) {
                                case "sort":
                                    if(!in_array($input_value, array_keys($this->_object_parameters)) && !in_array($input_value, $this->_extra_sort_parameter)) {
                                        HostFact_API::parseError("Invalid filter 'sort'. Please enter valid columns or remove filter", true);
                                    }
                                    $is_filter_input_valid = true;
                                    break;
                                case "order":
                                    if(in_array($input_value, ["ASC", "DESC"])) {
                                        $is_filter_input_valid = true;
                                    }
                                    break;
                                case "searchat":
                                    $searchat_array = explode("|", $input_value);
                                    foreach ($searchat_array as $searchat) {
                                        if(!in_array($searchat, array_keys($this->_object_parameters)) && !in_array($searchat, $this->_extra_search_filter)) {
                                            HostFact_API::parseError("Invalid filter 'searchat'. Please enter valid columns or remove filter", true);
                                        }
                                    }
                                    $is_filter_input_valid = true;
                                    break;
                                case "offset":
                                case "limit":
                                case "searchfor":
                                case "debtor":
                                default:
                                    $is_filter_input_valid = true;
                                    if($is_filter_input_valid === true) {
                                        $this->_valid_filter_input[$key] = $input_value;
                                    }
                            }
                        }
                }
            }
            $filters[$key] = $filter["default"];
        }
        return $filters;
    }
    protected function _show_modifications($result, $service_type, $service_id, $service_debtor)
    {
        require_once "class/clientareachange.php";
        $ClientareaChange_Model = new ClientareaChange_Model();
        $options = [];
        $options["filters"]["reference_type"] = $service_type;
        $options["filters"]["reference_id"] = $service_id;
        $options["filters"]["debtor"] = $service_debtor;
        $options["filters"]["approval"] = "pending|approved|notused";
        $options["filter"] = "pending|error";
        $changes_result = $ClientareaChange_Model->listChanges($options);
        if(!empty($changes_result)) {
            foreach ($changes_result as $_change) {
                $result["ClientareaModifications"][$_change->Action] = ["Created" => $_change->Created, "Modified" => $_change->Modified, "Status" => $_change->Status, "Data" => $_change->Data, "Approval" => $_change->Approval];
            }
        }
        return $result;
    }
    protected function _cancel_modification($service_type, $service_id, $service_debtor)
    {
        $modification_type = HostFact_API::getRequestParameter("ModificationType");
        require_once "class/clientareachange.php";
        $ClientareaChange_Model = new ClientareaChange_Model();
        if($ClientareaChange_Model->cancelPendingChange($service_type, $service_id, $service_debtor, $modification_type)) {
            HostFact_API::parseSuccess($ClientareaChange_Model->Success, true);
        }
        HostFact_API::parseError($ClientareaChange_Model->Error, true);
    }
}

?>