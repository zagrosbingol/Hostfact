<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class product_api_controller extends api_controller
{
    public function __construct()
    {
        parent::__construct();
        HostFact_API::setObjectNames("products", "product");
        require_once "class/product.php";
        $this->addParameter("Identifier", "int");
        $this->addParameter("ProductCode", "string");
        $this->addParameter("ProductName", "string");
        $this->addParameter("ProductKeyPhrase", "text");
        $this->addParameter("ProductDescription", "text");
        $this->addParameter("NumberSuffix", "string");
        $this->addParameter("PriceExcl", "float");
        $this->addParameter("PricePeriod", "string");
        $this->addParameter("TaxPercentage", "float");
        $this->addParameter("Cost", "float");
        $this->addParameter("ProductType", "string");
        $this->addParameter("ProductTld", "string");
        $this->addParameter("PackageID", "int");
        $this->addParameter("HasCustomPrice", "string");
        $this->addParameter("CustomPrices", "array");
        $this->addSubParameter("CustomPrices", "Periods", "int");
        $this->addSubParameter("CustomPrices", "Periodic", "string");
        $this->addSubParameter("CustomPrices", "PriceExcl", "float");
        $this->addSubParameter("CustomPrices", "PriceIncl", "float");
        $this->addParameter("Groups", "array_raw");
        $this->addParameter("Created", "readonly");
        $this->addParameter("Modified", "readonly");
        $this->addFilter("group", "int", "");
        $this->addFilter("created", "filter_datetime");
        $this->addFilter("modified", "filter_datetime");
        $this->object = new product();
    }
    public function list_api_action()
    {
        $filters = $this->getFilterValues();
        $fields = ["ProductCode", "ProductName", "ProductType", "ProductTld", "PackageID", "PriceExcl", "TaxPercentage", "PricePeriod", "ProductKeyPhrase", "ProductDescription", "NumberSuffix", "Modified"];
        $sort = $filters["sort"] ? $filters["sort"] : "ProductCode";
        $page_offset = $filters["offset"] / $filters["limit"] + 1;
        $searchat = $filters["searchat"] ? $filters["searchat"] : "ProductCode|ProductName|ProductKeyPhrase";
        $limit = $filters["limit"];
        $field_filters = array_diff_key($filters, array_flip(["offset", "limit", "sort", "order", "searchat", "searchfor"]));
        $product_list = $this->object->all($fields, $sort, $filters["order"], $page_offset, $searchat, $filters["searchfor"], $field_filters, $limit);
        $array = [];
        foreach ($product_list as $key => $value) {
            if($key != "CountRows") {
                $array[] = ["Identifier" => $value["id"], "ProductCode" => htmlspecialchars_decode($value["ProductCode"]), "ProductName" => htmlspecialchars_decode($value["ProductName"]), "ProductKeyPhrase" => htmlspecialchars_decode($value["ProductKeyPhrase"]), "ProductDescription" => htmlspecialchars_decode($value["ProductDescription"]), "ProductType" => htmlspecialchars_decode($value["ProductType"]), "ProductTld" => htmlspecialchars_decode($value["ProductTld"]), "PackageID" => 0 < $value["PackageID"] ? $value["PackageID"] : "", "NumberSuffix" => htmlspecialchars_decode($value["NumberSuffix"]), "PriceExcl" => $value["PriceExcl"], "TaxPercentage" => $value["TaxPercentage"] * 100, "PricePeriod" => htmlspecialchars_decode($value["PricePeriod"]), "Modified" => $this->_filter_date_db2api($value["Modified"], false)];
            }
        }
        HostFact_API::setMetaData("totalresults", $product_list["CountRows"]);
        HostFact_API::setMetaData("currentresults", count($array));
        HostFact_API::setMetaData("offset", $filters["offset"]);
        if(!empty($this->_valid_filter_input)) {
            HostFact_API::setMetaData("filters", $this->_valid_filter_input);
        }
        HostFact_API::parseResponse($array, true);
    }
    public function show_api_action()
    {
        $product_id = $this->_get_product_id(HostFact_API::getRequestParameter("Identifier"));
        return $this->_show_product($product_id);
    }
    public function add_api_action()
    {
        HostFact_API::beginTransaction();
        $parse_array = $this->getValidParameters();
        $product = $this->object;
        foreach ($parse_array as $key => $value) {
            if(in_array($key, $product->Variables)) {
                $product->{$key} = $value;
            }
        }
        if(isset($parse_array["TaxPercentage"]) && $parse_array["TaxPercentage"] != "") {
            $product->TaxPercentage = $this->_check_taxpercentage($parse_array["TaxPercentage"] / 100);
        }
        $newGroups = $this->_checkProductGroups($parse_array);
        $product->Groups = !isset($parse_array["Groups"]) ? $product->Groups : $newGroups;
        if(!isset($product->ProductCode) || $product->ProductCode == "") {
            $product->ProductCode = $product->newProductCode();
        }
        if($product->add()) {
            $product_id = $product->Identifier;
            if($product->HasCustomPrice == "period" && $product->PricePeriod != "") {
                if($product->updateCustomProductPrices($this->_checkInclPrice($parse_array["CustomPrices"], $product->TaxPercentage)) === false) {
                    HostFact_API::parseError($product->Error, true);
                }
            } elseif($product->HasCustomPrice == "period" && $product->PricePeriod == "") {
                HostFact_API::parseError(__("cant add custom prices when period is ones"), true);
            }
            HostFact_API::commit();
            return $this->_show_product($product_id);
        }
        HostFact_API::parseError($product->Error, true);
    }
    private function _checkInclPrice($CustomPrices, $TaxPercentage)
    {
        foreach ($CustomPrices as $key => $_tmp) {
            if(isset($_tmp["PriceIncl"])) {
                $CustomPrices[$key]["PriceExcl"] = deformat_money($_tmp["PriceIncl"]) / (1 + $TaxPercentage);
                unset($CustomPrices[$key]["PriceIncl"]);
            }
        }
        return $CustomPrices;
    }
    public function edit_api_action()
    {
        $parse_array = $this->getValidParameters(true);
        $product = $this->object;
        $product->Identifier = $this->_get_product_id(HostFact_API::getRequestParameter("Identifier"));
        if(!$product->show()) {
            HostFact_API::parseError($product->Error, true);
        }
        foreach ($this->object as $key => $value) {
            if(is_string($value) || is_numeric($value)) {
                if($key == "TaxPercentage" && isset($parse_array["TaxPercentage"])) {
                    $product->{$key} = $this->_check_taxpercentage($parse_array[$key] / 100);
                } else {
                    $product->{$key} = isset($parse_array[$key]) ? $parse_array[$key] : htmlspecialchars_decode($value);
                }
            }
        }
        $newGroups = $this->_checkProductGroups($parse_array);
        $product->Groups = !isset($parse_array["Groups"]) ? $product->Groups : $newGroups;
        if($product->HasCustomPrice == "period" && $product->PricePeriod != "" && isset($parse_array["CustomPrices"])) {
            if($product->updateCustomProductPrices($this->_checkInclPrice($parse_array["CustomPrices"], $product->TaxPercentage)) === false) {
                HostFact_API::parseError($product->Error, true);
            }
        } elseif($product->HasCustomPrice == "no" || $product->PricePeriod == "") {
            $product->removeCustomProductPrices();
        }
        if($product->edit()) {
            $product_id = $product->Identifier;
            return $this->_show_product($product_id);
        }
        HostFact_API::parseError($product->Error, true);
    }
    private function _checkProductGroups($parse_array)
    {
        $productGroups = [];
        if(isset($parse_array["Groups"]) && is_array($parse_array["Groups"])) {
            $newGroups = $parse_array["Groups"];
            $product = $this->object;
            foreach ($newGroups as $key) {
                $id = $product->_checkGroup($key);
                if($id !== false) {
                    $productGroups[] = $id;
                } else {
                    HostFact_API::parseError([sprintf(__("invalid product group"), esc($key))], true);
                }
            }
        }
        return $productGroups;
    }
    public function delete_api_action()
    {
        $product = $this->object;
        $product->Identifier = $this->_get_product_id(HostFact_API::getRequestParameter("Identifier"));
        if($product->show()) {
            if($product->delete(0, "none")) {
                HostFact_API::parseSuccess([sprintf(__("product is removed"), $product->ProductCode)], true);
            } else {
                HostFact_API::parseError($product->Error, true);
            }
        } else {
            HostFact_API::parseError($product->Error, true);
        }
    }
    protected function getFilterValues()
    {
        $filters = parent::getFilterValues();
        if(isset($filters["group"])) {
            if(0 < $filters["group"]) {
                $result = Database_Model::getInstance()->getOne("HostFact_Group", "id")->where("Type", "product")->where("Status", "1")->where("id", $filters["group"])->execute();
                if(!$result) {
                    HostFact_API::parseError("Invalid filter 'productgroup'. The product group does not exist", true);
                }
            } else {
                $filters["group"] = "";
                unset($this->_valid_filter_input["group"]);
            }
        }
        return $filters;
    }
    private function _show_product($product_id)
    {
        $product = $this->object;
        $product->Identifier = $product_id;
        if(!$product->show()) {
            HostFact_API::parseError($product->Error, true);
        }
        $result = [];
        foreach ($this->_object_parameters as $field => $field_info) {
            if(in_array($field, ["Groups"])) {
                $result[$field] = $product->{$field};
            } else {
                $result[$field] = is_string($product->{$field}) ? htmlspecialchars_decode($product->{$field}) : $product->{$field};
            }
        }
        if($product->HasCustomPrice == "period") {
            $listCustomProductPrices = $product->listCustomProductPrices();
            if($listCustomProductPrices !== false) {
                foreach ($listCustomProductPrices["period"] as $PeriodsPeriodic => $aPrice) {
                    if($PeriodsPeriodic != "default") {
                        $aPeriodsPeriodic = explode("-", $PeriodsPeriodic);
                        $result["CustomPrices"][] = ["Periods" => $aPeriodsPeriodic[0], "Periodic" => $aPeriodsPeriodic[1], "PriceExcl" => $aPrice["PriceExcl"], "PriceIncl" => $aPrice["PriceIncl"]];
                    }
                }
            }
        }
        $result["TaxPercentage"] = $result["TaxPercentage"] * 100;
        global $array_producttypes;
        global $array_periodes;
        $result["Translations"] = ["ProductType" => isset($array_producttypes[$product->ProductType]) ? $array_producttypes[$product->ProductType] : "", "PricePeriod" => isset($array_periodes[$product->PricePeriod]) ? $array_periodes[$product->PricePeriod] : ""];
        $result["Created"] = $this->_filter_date_db2api($product->Created, false);
        $result["Modified"] = $this->_filter_date_db2api($product->Modified, false);
        return HostFact_API::parseResponse($result);
    }
}

?>