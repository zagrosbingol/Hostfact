<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class handle_api_controller extends api_controller
{
    public function __construct()
    {
        parent::__construct();
        HostFact_API::setObjectNames("handles", "handle");
        require_once "class/handle.php";
        $this->addParameter("Identifier", "int");
        $this->addParameter("Handle", "string");
        $this->addParameter("Registrar", "int");
        $this->addParameter("RegistrarHandle", "string");
        $this->addParameter("Debtor", "int");
        $this->addParameter("DebtorCode", "string");
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
        $this->addParameter("FaxNumber", "string");
        $this->addParameter("Created", "readonly");
        $this->addParameter("Modified", "readonly");
        $this->addFilter("status", "string", "");
        $this->addFilter("created", "filter_datetime");
        $this->addFilter("modified", "filter_datetime");
        $this->object = new handle();
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
    }
    public function list_api_action()
    {
        $filters = $this->getFilterValues();
        $fields = ["Handle", "Name", "Registrar", "RegistrarHandle", "Debtor", "CompanyName", "Initials", "SurName", "EmailAddress", "Status", "Modified"];
        $sort = $filters["sort"] ? $filters["sort"] : "Handle";
        $page_offset = $filters["offset"] / $filters["limit"] + 1;
        $searchat = $filters["searchat"] ? $filters["searchat"] : "DebtorCode|Handle|RegistrarHandle";
        $searchfor = $filters["searchfor"] ? $filters["searchfor"] : "";
        $limit = $filters["limit"];
        $field_filters = array_diff_key($filters, array_flip(["offset", "limit", "sort", "order", "searchat", "searchfor"]));
        $handele_list = $this->object->all($fields, $sort, $filters["order"], $page_offset, $searchat, $searchfor, $field_filters, $limit);
        $array = [];
        foreach ($handele_list as $key => $value) {
            if($key != "CountRows") {
                $array[] = ["Identifier" => $value["id"], "Handle" => htmlspecialchars_decode($value["Handle"]), "Registrar" => htmlspecialchars_decode($value["Registrar"]), "RegistrarHandle" => htmlspecialchars_decode($value["RegistrarHandle"]), "Debtor" => htmlspecialchars_decode($value["Debtor"]), "CompanyName" => htmlspecialchars_decode($value["CompanyName"]), "Initials" => htmlspecialchars_decode($value["Initials"]), "SurName" => htmlspecialchars_decode($value["SurName"]), "EmailAddress" => htmlspecialchars_decode($value["EmailAddress"]), "Modified" => $this->_filter_date_db2api($value["Modified"], false)];
            }
        }
        HostFact_API::setMetaData("totalresults", $handele_list["CountRows"]);
        HostFact_API::setMetaData("currentresults", count($array));
        HostFact_API::setMetaData("offset", $filters["offset"]);
        if(!empty($this->_valid_filter_input)) {
            HostFact_API::setMetaData("filters", $this->_valid_filter_input);
        }
        HostFact_API::parseResponse($array, true);
    }
    public function listdomain_api_action()
    {
        $filters = $this->getFilterValues();
        require_once "class/domain.php";
        $domain = new domain();
        $handle_id = $this->_get_handle_id();
        if($handle_id === false) {
            HostFact_API::parseError(__("invalid identifier for handle"), true);
        }
        $fields = ["id", "Domain", "Tld", "Debtor", "RegistrationDate", "ExpirationDate", "TerminationDate", "Registrar", "Status"];
        $sort = $filters["sort"] ? $filters["sort"] : "Domain";
        $order = $filters["order"] ? $filters["order"] : "ASC";
        $page_offset = $filters["offset"] / $filters["limit"] + 1;
        $searchat = "ownerHandle|adminHandle|techHandle";
        $searchfor = $handle_id;
        $limit = $filters["limit"];
        global $array_domainstatus;
        $tmp_array_domainstatus = $array_domainstatus;
        unset($tmp_array_domainstatus[9]);
        $group_id = implode("|", array_keys($tmp_array_domainstatus));
        $list_handle_domains = $domain->all($fields, $sort, $order, $page_offset, $searchat, $searchfor, $group_id, $limit);
        $array = [];
        foreach ($list_handle_domains as $key => $value) {
            if($key != "CountRows") {
                $array[] = ["Identifier" => $value["id"], "Domain" => htmlspecialchars_decode($value["Domain"]), "Tld" => htmlspecialchars_decode($value["Tld"]), "Debtor" => $value["Debtor"], "RegistrationDate" => $this->_filter_date_db2api($value["RegistrationDate"]), "ExpirationDate" => $this->_filter_date_db2api($value["ExpirationDate"]), "TerminationDate" => $this->_filter_date_db2api($value["TerminationDate"]), "Registrar" => $value["Registrar"], "Status" => $value["Status"]];
            }
        }
        HostFact_API::setMetaData("totalresults", $list_handle_domains["CountRows"]);
        HostFact_API::setMetaData("currentresults", count($array));
        HostFact_API::setMetaData("offset", $filters["offset"]);
        if(!empty($this->_valid_filter_input)) {
            HostFact_API::setMetaData("filters", $this->_valid_filter_input);
        }
        HostFact_API::parseResponse($array, true);
    }
    public function show_api_action()
    {
        $handle_id = $this->_get_handle_id();
        return $this->_show_handle($handle_id);
    }
    public function add_api_action()
    {
        $parse_array = $this->getValidParameters();
        $parse_array["Debtor"] = $this->_get_debtor_id();
        $handle = $this->object;
        if($handle->Registrar == "") {
            $handle->RegistrarHandle = "";
        }
        if(0 < HostFact_API::getRequestParameter("copyDataFromHandle")) {
            $handle->Identifier = intval(HostFact_API::getRequestParameter("copyDataFromHandle"));
            if(!$handle->show()) {
                HostFact_API::parseError($handle->Error, true);
            }
            foreach ($handle as $key => $value) {
                if(@is_string($value)) {
                    $handle->{$key} = htmlspecialchars_decode($value);
                }
            }
            $resetHandleFields = ["Identifier", "Handle", "RegistrarHandle"];
            foreach ($resetHandleFields as $key) {
                $handle->{$key} = "";
            }
            $handle->Status = "1";
        }
        if($parse_array["Debtor"] === false) {
            $handle->Handle = $handle->nextInternalHandle("general");
        } elseif(0 < $parse_array["Debtor"]) {
            $handle->Handle = $handle->nextInternalHandle("debtor", $parse_array["Debtor"]);
            if(HostFact_API::getRequestParameter("copyDataFromDebtor") == "yes") {
                require_once "class/debtor.php";
                $debtor = new debtor();
                $debtor->Identifier = $parse_array["Debtor"];
                if(!$debtor->show()) {
                    HostFact_API::parseError($debtor->Error, true);
                }
                foreach ($debtor as $key => $value) {
                    if(in_array($key, $handle->Variables) && !in_array($key, ["Status"])) {
                        $handle->{$key} = is_string($value) ? htmlspecialchars_decode($value) : $value;
                    }
                }
            }
        }
        foreach ($parse_array as $key => $value) {
            if(in_array($key, $handle->Variables)) {
                $handle->{$key} = $value;
            }
        }
        if(isset($parse_array["CustomFields"])) {
            foreach ($parse_array["CustomFields"] as $_custom_field => $_custom_value) {
                $handle->customvalues[$_custom_field] = $_custom_value;
            }
        }
        if(HostFact_API::getRequestParameter("createAtRegistrar") == "yes" && $handle->RegistrarHandle != "") {
            HostFact_API::parseError(__("handle exists at registrar"), true);
        } elseif(HostFact_API::getRequestParameter("createAtRegistrar") == "yes" && $handle->RegistrarHandle == "") {
            $handle->CreateAtRegistrar = true;
        }
        if($handle->add()) {
            HostFact_API::parseSuccess($handle->Success);
            return $this->_show_handle($handle->Identifier);
        }
        HostFact_API::parseError($handle->Error, true);
    }
    public function edit_api_action()
    {
        $parse_array = $this->getValidParameters(true);
        $handle = $this->object;
        $handle->Identifier = $this->_get_handle_id();
        if(!$handle->show()) {
            HostFact_API::parseError($handle->Error, true);
        }
        foreach ($handle as $key => $value) {
            if(is_string($value)) {
                $handle->{$key} = isset($parse_array[$key]) ? $parse_array[$key] : htmlspecialchars_decode($value);
            }
        }
        if(isset($parse_array["CustomFields"])) {
            foreach ($parse_array["CustomFields"] as $_custom_field => $_custom_value) {
                $handle->customvalues[$_custom_field] = $_custom_value;
            }
        }
        if($handle->edit($handle->Identifier)) {
            if(HostFact_API::getRequestParameter("createAtRegistrar") == "yes" && $handle->RegistrarHandle != "") {
                HostFact_API::parseError(__("handle exists at registrar"), true);
            } elseif(HostFact_API::getRequestParameter("createAtRegistrar") == "yes" && $handle->RegistrarHandle == "") {
                $handle->createHandleAtRegistrar();
                if(count($handle->Error)) {
                    $handle->Warning = array_merge($handle->Warning, $handle->Error);
                }
            }
            if($handle->RegistrarHandle && HostFact_API::getRequestParameter("updateAtRegistrar") == "yes") {
                $handle->updateWhoisDataToRegistrar();
                if(!empty($handle->Error)) {
                    $handle->Warning = array_merge($handle->Warning, $handle->Error);
                }
            } elseif($handle->Registrar && $handle->RegistrarHandle) {
                $handle->Warning[] = __("api if you want to update handle at registrar, use action sync handles");
            }
            if(!empty($handle->Warning)) {
                HostFact_API::parseWarning($handle->Warning);
            }
            return $this->_show_handle($handle->Identifier);
        }
        HostFact_API::parseError($handle->Error, true);
    }
    public function delete_api_action()
    {
        $handle = $this->object;
        $handle->Identifier = $this->_get_handle_id();
        if(!$handle->show()) {
            HostFact_API::parseError(__("invalid identifier for handle"), true);
        } elseif($handle->Status == 9) {
            HostFact_API::parseError([sprintf(__("handle is already removed"), $handle->Handle)], true);
        } elseif($handle->delete($handle->Identifier) === true) {
            HostFact_API::parseSuccess([sprintf(__("handle is removed"), $handle->Handle)], true);
        }
        HostFact_API::parseError($handle->Error, true);
    }
    private function _get_handle_id()
    {
        $handle_id = HostFact_API::getRequestParameter("Identifier");
        if(0 < $handle_id) {
            return $handle_id;
        }
        if($handle = HostFact_API::getRequestParameter("Handle")) {
            return $this->object->getID("handle", $handle);
        }
        return false;
    }
    private function _show_handle($handle_id)
    {
        $handle = $this->object;
        $handle->Identifier = $handle_id;
        if(!$handle->show()) {
            HostFact_API::parseError($handle->Error, true);
        }
        $result = [];
        foreach ($this->_object_parameters as $field => $field_info) {
            if($field == "DebtorCode") {
                if(0 < $handle->Debtor) {
                    require_once "class/debtor.php";
                    $debtor = new debtor();
                    $result["DebtorCode"] = $debtor->getDebtorCode($handle->Debtor);
                } else {
                    $result["DebtorCode"] = "";
                }
            } elseif($field != "CustomFields") {
                $result[$field] = is_string($handle->{$field}) ? htmlspecialchars_decode($handle->{$field}) : $handle->{$field};
            }
        }
        if(isset($this->_object_parameters["CustomFields"])) {
            $result["CustomFields"] = $handle->customvalues;
        }
        global $array_legaltype;
        global $array_country;
        $result["Translations"] = ["LegalForm" => isset($array_legaltype[$handle->LegalForm]) ? $array_legaltype[$handle->LegalForm] : "", "State" => isset($handle->StateName) ? $handle->StateName : "", "Country" => isset($array_country[$handle->Country]) ? $array_country[$handle->Country] : "", "RegistrarName" => $handle->Name];
        if(!defined("IS_INTERNATIONAL") || IS_INTERNATIONAL !== true) {
            unset($result["Translations"]["State"]);
        }
        $result["Created"] = $this->_filter_date_db2api($handle->Created, false);
        $result["Modified"] = $this->_filter_date_db2api($handle->Modified, false);
        return HostFact_API::parseResponse($result);
    }
}

?>