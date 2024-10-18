<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class creditor_api_controller extends api_controller
{
    public function __construct()
    {
        parent::__construct();
        HostFact_API::setObjectNames("creditors", "creditor");
        require_once "class/creditor.php";
        $this->addParameter("Identifier", "int");
        $this->addParameter("CreditorCode", "string");
        $this->addParameter("MyCustomerCode", "string");
        $this->addParameter("CompanyName", "string");
        $this->addParameter("CompanyNumber", "string");
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
        $this->addParameter("Comment", "text");
        $this->addParameter("Authorisation", "string");
        $this->addParameter("AccountNumber", "string");
        $this->addParameter("AccountBIC", "string");
        $this->addParameter("AccountName", "string");
        $this->addParameter("AccountBank", "string");
        $this->addParameter("AccountCity", "string");
        $this->addParameter("Term", "int");
        $this->addParameter("Groups", "array_raw");
        $this->addParameter("Created", "readonly");
        $this->addParameter("Modified", "readonly");
        $this->addFilter("group", "int", 0);
        $this->addFilter("created", "filter_datetime");
        $this->addFilter("modified", "filter_datetime");
        $this->object = new creditor();
    }
    public function list_api_action()
    {
        $filters = $this->getFilterValues();
        $fields = ["CreditorCode", "CompanyName", "Sex", "Initials", "SurName", "EmailAddress", "Modified"];
        $sort = $filters["sort"] ? $filters["sort"] : "CreditorCode";
        $page_offset = $filters["offset"] / $filters["limit"] + 1;
        $searchat = $filters["searchat"] ? $filters["searchat"] : "CreditorCode|CompanyName|SurName";
        $limit = $filters["limit"];
        $field_filters = array_diff_key($filters, array_flip(["offset", "limit", "sort", "order", "searchat", "searchfor"]));
        $creditor_list = $this->object->all($fields, $sort, $filters["order"], $page_offset, $searchat, $filters["searchfor"], $field_filters, $limit);
        $array = [];
        foreach ($creditor_list as $key => $value) {
            if($key != "CountRows") {
                $array[] = ["Identifier" => $value["id"], "CreditorCode" => htmlspecialchars_decode($value["CreditorCode"]), "CompanyName" => htmlspecialchars_decode($value["CompanyName"]), "Sex" => $value["Sex"], "Initials" => htmlspecialchars_decode($value["Initials"]), "SurName" => htmlspecialchars_decode($value["SurName"]), "EmailAddress" => htmlspecialchars_decode($value["EmailAddress"]), "Modified" => $this->_filter_date_db2api($value["Modified"], false)];
            }
        }
        HostFact_API::setMetaData("totalresults", $creditor_list["CountRows"]);
        HostFact_API::setMetaData("currentresults", count($array));
        HostFact_API::setMetaData("offset", $filters["offset"]);
        if(!empty($this->_valid_filter_input)) {
            HostFact_API::setMetaData("filters", $this->_valid_filter_input);
        }
        HostFact_API::parseResponse($array, true);
    }
    public function show_api_action()
    {
        $creditor_id = $this->_get_creditor_id();
        return $this->_show_creditor($creditor_id);
    }
    public function add_api_action()
    {
        $parse_array = $this->getValidParameters();
        $creditor = $this->object;
        foreach ($parse_array as $key => $value) {
            if(in_array($key, $creditor->Variables)) {
                $creditor->{$key} = $value;
            }
        }
        $creditor->CreditorCode = !$creditor->CreditorCode ? $creditor->newCreditorCode() : $creditor->CreditorCode;
        $newGroups = $this->_checkCreditorGroups($parse_array);
        $creditor->Groups = !isset($parse_array["Groups"]) ? $creditor->Groups : $newGroups;
        $creditor->Country = str_replace("EU-", "", strtoupper($creditor->Country));
        if($creditor->add()) {
            return $this->_show_creditor($creditor->Identifier);
        }
        HostFact_API::parseError($creditor->Error, true);
    }
    public function edit_api_action()
    {
        $parse_array = $this->getValidParameters();
        $creditor = $this->object;
        $creditor->Identifier = $this->_get_creditor_id();
        if(!$creditor->show()) {
            HostFact_API::parseError($creditor->Error, true);
        }
        foreach ($creditor as $key => $value) {
            if(is_string($value)) {
                $creditor->{$key} = isset($parse_array[$key]) ? $parse_array[$key] : htmlspecialchars_decode($value);
            }
        }
        $newGroups = $this->_checkCreditorGroups($parse_array);
        $creditor->Groups = !isset($parse_array["Groups"]) ? $creditor->Groups : $newGroups;
        if($creditor->edit()) {
            return $this->_show_creditor($creditor->Identifier);
        }
        HostFact_API::parseError($creditor->Error, true);
    }
    public function delete_api_action()
    {
        $creditor = $this->object;
        $creditor->Identifier = $this->_get_creditor_id();
        if(!$creditor->show()) {
            HostFact_API::parseError($creditor->Error, true);
        } elseif($creditor->Status == 9) {
            HostFact_API::parseError(__("api creditor already removed"), true);
        }
        $withcreditinvoice = HostFact_API::getRequestParameter("withcreditinvoice");
        require_once "class/creditinvoice.php";
        $creditInvoice = new creditinvoice();
        $listCreditInvoice = $creditInvoice->all(["id"], false, false, -1, "Creditor", $creditor->Identifier, false);
        if(0 < $listCreditInvoice["CountRows"] && $withcreditinvoice != "yes") {
            HostFact_API::parseError(__("api there are still credit invoices, give withcreditinvoice"), true);
        }
        if($withcreditinvoice == "yes") {
            foreach ($listCreditInvoice as $CreditInvoiceId => $creditInvoice) {
                if(is_numeric($CreditInvoiceId)) {
                    $deleteCreditInvoice = new creditinvoice();
                    $deleteCreditInvoice->Identifier = $CreditInvoiceId;
                    $deleteCreditInvoice->delete();
                }
            }
        }
        if(!$creditor->delete()) {
            HostFact_API::parseError($creditor->Error, true);
        }
        return HostFact_API::parseSuccess($creditor->Success, true);
    }
    protected function getFilterValues()
    {
        $filters = parent::getFilterValues();
        if(isset($filters["group"])) {
            if(0 < $filters["group"]) {
                $result = Database_Model::getInstance()->getOne("HostFact_Group", "id")->where("Type", "creditor")->where("Status", "1")->where("id", $filters["group"])->execute();
                if(!$result) {
                    HostFact_API::parseError("Invalid filter 'creditorgroup'. The creditor group does not exist", true);
                }
            } else {
                $filters["group"] = "";
                unset($this->_valid_filter_input["group"]);
            }
        }
        return $filters;
    }
    private function _checkCreditorGroups($parse_array)
    {
        $creditorGroups = [];
        if(isset($parse_array["Groups"]) && count($parse_array["Groups"])) {
            $newGroups = $parse_array["Groups"];
            $creditor = $this->object;
            foreach ($newGroups as $key) {
                $id = $creditor->_checkGroup($key);
                if($id !== false) {
                    $creditorGroups[] = $id;
                } else {
                    HostFact_API::parseError([sprintf(__("api supplier group not found"), esc($key))], true);
                }
            }
        }
        return $creditorGroups;
    }
    private function _get_creditor_id()
    {
        $creditor_id = HostFact_API::getRequestParameter("Identifier");
        if(0 < $creditor_id) {
            return $creditor_id;
        }
        return $this->object->getID("creditorcode", HostFact_API::getRequestParameter("CreditorCode"));
    }
    private function _show_creditor($creditor_id)
    {
        $creditor = $this->object;
        $creditor->Identifier = $creditor_id;
        if(!$creditor->show()) {
            HostFact_API::parseError($creditor->Error, true);
        }
        $result = [];
        foreach ($this->_object_parameters as $field => $field_info) {
            $result[$field] = is_string($creditor->{$field}) ? htmlspecialchars_decode($creditor->{$field}) : $creditor->{$field};
        }
        global $array_country;
        global $array_states;
        $result["Translations"] = ["State" => isset($creditor->StateName) ? $creditor->StateName : "", "Country" => isset($array_country[$creditor->Country]) ? $array_country[$creditor->Country] : ""];
        if(!defined("IS_INTERNATIONAL") || IS_INTERNATIONAL !== true) {
            unset($result["Translations"]["State"]);
        }
        $result["Created"] = $this->_filter_date_db2api($creditor->Created, false);
        $result["Modified"] = $this->_filter_date_db2api($creditor->Modified, false);
        return HostFact_API::parseResponse($result);
    }
}

?>