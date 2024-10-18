<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class group_api_controller extends api_controller
{
    public function __construct()
    {
        parent::__construct();
        HostFact_API::setObjectNames("groups", "group");
        $this->addParameter("Identifier", "int");
        $this->addParameter("Type", "string");
        $this->addParameter("GroupName", "string");
        $this->addParameter("Items", "array_raw");
        $this->addFilter("type", "string", "");
    }
    public function list_api_action()
    {
        $filters = $this->getFilterValues();
        $group = new group($filters["type"]);
        $item_field = $this->getReferencesArrayName($filters["type"]);
        $fields = ["GroupName", "Type", $item_field];
        $sort = $filters["sort"] ? $filters["sort"] : "GroupName";
        $page_offset = $filters["offset"] / $filters["limit"] + 1;
        $searchat = $filters["searchat"] ? $filters["searchat"] : "GroupName";
        $limit = $filters["limit"];
        $field_filters = array_diff_key($filters, array_flip(["offset", "limit", "sort", "order", "searchat", "searchfor"]));
        $group_list = $group->all($fields, $sort, $filters["order"], $page_offset, $searchat, $filters["searchfor"], $field_filters, $limit);
        $array = [];
        foreach ($group_list as $key => $value) {
            if($key != "CountRows") {
                $item_ids = array_column($value[$item_field], "id");
                $array[] = ["Identifier" => $value["id"], "GroupName" => htmlspecialchars_decode($value["GroupName"]), "Type" => htmlspecialchars_decode($value["Type"]), "Items" => $item_ids];
            }
        }
        HostFact_API::setMetaData("totalresults", $group_list["CountRows"]);
        HostFact_API::setMetaData("currentresults", count($array));
        HostFact_API::setMetaData("offset", $filters["offset"]);
        if(!empty($this->_valid_filter_input)) {
            HostFact_API::setMetaData("filters", $this->_valid_filter_input);
        }
        HostFact_API::parseResponse($array, true);
    }
    public function show_api_action()
    {
        $group_id = HostFact_API::getRequestParameter("Identifier");
        return $this->_show_group($group_id);
    }
    public function add_api_action()
    {
        $parse_array = $this->getValidParameters();
        if(!isset($parse_array["Type"]) || !in_array($parse_array["Type"], ["debtor", "creditor", "product"])) {
            HostFact_API::parseError(__("please enter a valid group type"), true);
        }
        $group = new group($parse_array["Type"]);
        $group->GroupName = isset($parse_array["GroupName"]) ? $parse_array["GroupName"] : "";
        $group->Type = $parse_array["Type"];
        $type_full_list = $this->getFullListForType($group->Type);
        $newItems = isset($parse_array["Items"]) && count($parse_array["Items"]) ? $parse_array["Items"] : [];
        foreach ($newItems as $key) {
            if(!isset($type_full_list[$key])) {
                HostFact_API::parseError([sprintf(__("api " . $group->Type . " group item not found"), esc($key))], true);
            }
        }
        $group->Products = !isset($parse_array["Items"]) ? [] : $newItems;
        if($group->add()) {
            HostFact_API::parseSuccess($group->Success);
            return $this->_show_group($group->Identifier);
        }
        HostFact_API::parseError($group->Error, true);
    }
    public function edit_api_action()
    {
        $group_id = HostFact_API::getRequestParameter("Identifier");
        $group = new group();
        $group->Identifier = $group_id;
        if(!$group->show()) {
            HostFact_API::parseError($group->Error, true);
        }
        $item_field = $this->getReferencesArrayName($group->Type);
        $parse_array = $this->getValidParameters();
        $group->GroupName = isset($parse_array["GroupName"]) ? $parse_array["GroupName"] : htmlspecialchars_decode($group->GroupName);
        $type_full_list = $this->getFullListForType($group->Type);
        $newItems = !empty($parse_array["Items"]) ? $parse_array["Items"] : [];
        foreach ($newItems as $key) {
            if(!isset($type_full_list[$key])) {
                HostFact_API::parseError([sprintf(__("api " . $group->Type . " group item not found"), esc($key))], true);
            }
        }
        $group->Products = !isset($parse_array["Items"]) ? $group->{$item_field} : $newItems;
        if($group->edit()) {
            HostFact_API::parseSuccess($group->Success);
            return $this->_show_group($group->Identifier);
        }
        HostFact_API::parseError($group->Error, true);
    }
    public function delete_api_action()
    {
        $group_id = HostFact_API::getRequestParameter("Identifier");
        $group = new group();
        $group->Identifier = $group_id;
        if(!$group->show()) {
            HostFact_API::parseError($group->Error, true);
        }
        if($group->delete()) {
            HostFact_API::parseSuccess($group->Success, true);
        } else {
            HostFact_API::parseError($group->Error, true);
        }
    }
    private function _show_group($group_id)
    {
        $group = new group();
        $group->Identifier = $group_id;
        if(!$group->show()) {
            HostFact_API::parseError($group->Error, true);
        }
        $item_field = $this->getReferencesArrayName($group->Type);
        $type_full_list = $this->getFullListForType($group->Type);
        $items = [];
        switch ($group->Type) {
            case "debtor":
                foreach ($group->{$item_field} as $_id) {
                    $items[$_id] = ["Identifier" => $_id];
                    if(isset($type_full_list[$_id])) {
                        $items[$_id]["DebtorCode"] = htmlspecialchars_decode($type_full_list[$_id]["DebtorCode"]);
                        $items[$_id]["CompanyName"] = htmlspecialchars_decode($type_full_list[$_id]["CompanyName"]);
                        $items[$_id]["Initials"] = htmlspecialchars_decode($type_full_list[$_id]["Initials"]);
                        $items[$_id]["SurName"] = htmlspecialchars_decode($type_full_list[$_id]["SurName"]);
                    }
                }
                break;
            case "creditor":
                foreach ($group->{$item_field} as $_id) {
                    $items[$_id] = ["Identifier" => $_id];
                    if(isset($type_full_list[$_id])) {
                        $items[$_id]["CreditorCode"] = htmlspecialchars_decode($type_full_list[$_id]["CreditorCode"]);
                        $items[$_id]["CompanyName"] = htmlspecialchars_decode($type_full_list[$_id]["CompanyName"]);
                        $items[$_id]["Initials"] = htmlspecialchars_decode($type_full_list[$_id]["Initials"]);
                        $items[$_id]["SurName"] = htmlspecialchars_decode($type_full_list[$_id]["SurName"]);
                    }
                }
                break;
            case "product":
                foreach ($group->{$item_field} as $_id) {
                    $items[$_id] = ["Identifier" => $_id];
                    if(isset($type_full_list[$_id])) {
                        $items[$_id]["ProductCode"] = htmlspecialchars_decode($type_full_list[$_id]["ProductCode"]);
                        $items[$_id]["ProductName"] = htmlspecialchars_decode($type_full_list[$_id]["ProductName"]);
                    }
                }
                break;
            default:
                $result = ["Identifier" => $group->Identifier, "GroupName" => htmlspecialchars_decode($group->GroupName), "Type" => htmlspecialchars_decode($group->Type), "Items" => $items];
                return HostFact_API::parseResponse($result);
        }
    }
    protected function getFilterValues()
    {
        $filters = parent::getFilterValues();
        if(!isset($filters["type"]) || !in_array($filters["type"], ["debtor", "creditor", "product"])) {
            HostFact_API::parseError(__("please enter a valid group type"), true);
        }
        return $filters;
    }
    protected function getReferencesArrayName($type)
    {
        switch ($type) {
            case "debtor":
                $item_field = "Debtors";
                break;
            case "creditor":
                $item_field = "Creditors";
                break;
            case "product":
                $item_field = "Products";
                break;
            default:
                return $item_field;
        }
    }
    protected function getFullListForType($type)
    {
        switch ($type) {
            case "debtor":
                $debtor = new debtor();
                $debtor_list = $debtor->all(["DebtorCode", "CompanyName", "Initials", "SurName"]);
                return $debtor_list;
                break;
            case "creditor":
                $creditor = new creditor();
                $creditor_list = $creditor->all(["CreditorCode", "CompanyName", "Initials", "SurName"]);
                return $creditor_list;
                break;
            case "product":
                $product = new product();
                $product_list = $product->all(["ProductCode", "ProductName"]);
                return $product_list;
                break;
            default:
                return false;
        }
    }
}
if(!function_exists("array_column")) {
    function array_column($array, $column_name)
    {
        return array_map(function ($element) {
            return $element[$column_name];
        }, $array);
    }
}

?>