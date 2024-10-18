<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class product
{
    public $Identifier;
    public $ProductCode;
    public $Status;
    public $ParentIdentifier;
    public $ProductType;
    public $ProductTld;
    public $ProductName;
    public $ProductKeyPhrase;
    public $ProductDescription;
    public $NumberSuffix;
    public $HasCustomPrice;
    public $PriceExcl;
    public $PricePeriod;
    public $TaxPercentage;
    public $Cost;
    public $Ordered;
    public $Sold;
    public $Sales;
    public $Profit;
    public $Groups;
    public $Error;
    public $Warning;
    public $Success;
    public $Variables = ["Identifier", "ProductCode", "Status", "ParentIdentifier", "ProductType", "ProductTld", "PackageID", "ProductName", "ProductKeyPhrase", "ProductDescription", "NumberSuffix", "HasCustomPrice", "PriceExcl", "PricePeriod", "TaxPercentage", "Cost", "Ordered", "Sold"];
    public function __construct()
    {
        $this->TaxPercentage = STANDARD_TAX;
        $this->Status = "1";
        $this->ProductType = "other";
        $this->ProductDescription = "";
        $this->HasCustomPrice = "no";
        $this->Sales = 0;
        $this->Error = [];
        $this->Warning = [];
        $this->Success = [];
        $this->Groups = [];
    }
    public function show($id = NULL)
    {
        $id = 0 < $id ? $id : $this->Identifier;
        if(!is_numeric($id) && !$this->ProductCode) {
            $this->Error[] = __("invalid identifier for product");
            return false;
        }
        if(is_numeric($id) && 0 < $id) {
            $this->ProductCode = "HostFact_Search_ID";
        } else {
            $id = 0;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_Products", ["*"])->orWhere([["id", $id], ["ProductCode", $this->ProductCode]])->execute();
        if(!$result || empty($result->id)) {
            $this->Error[] = __("invalid identifier for product");
            return false;
        }
        $this->OldProductCode = $result->ProductCode;
        foreach ($result as $key => $value) {
            $this->{$key} = htmlspecialchars($value);
        }
        $this->Identifier = $this->id;
        $result = Database_Model::getInstance()->getOne("HostFact_InvoiceElements", ["SUM(ROUND(`PriceExcl` * `Periods` * `Number` * ROUND((1-DiscountPercentage),2),2)) AS Sales", "SUM(ROUND(`Periods` * `Number`*IF(`PriceExcl`>=0,1,-1),2)) AS Sold"])->where("ProductCode", $this->ProductCode)->execute();
        if(isset($result->Sales)) {
            $this->Sales = $result->Sales;
        }
        $this->Profit = $this->Sales - $result->Sold * $this->Cost;
        $this->PriceIncl = defined("VAT_CALC_METHOD") && VAT_CALC_METHOD == "incl" ? round($this->PriceExcl * (1 + $this->TaxPercentage), 5) : round(round((double) $this->PriceExcl, 2) * (1 + $this->TaxPercentage), 2);
        $this->Groups = [];
        $result = Database_Model::getInstance()->get(["HostFact_Group", "HostFact_GroupRelations"], ["HostFact_Group.id", "HostFact_Group.GroupName"])->where("HostFact_GroupRelations.Reference", $this->Identifier)->where("HostFact_GroupRelations.Type", "product")->where("HostFact_GroupRelations.Group = HostFact_Group.id")->where("HostFact_Group.Type", "product")->where("HostFact_Group.Status", ["!=" => "product"])->execute();
        if($result && is_array($result)) {
            foreach ($result as $_group) {
                $this->Groups[$_group->id] = ["id" => $_group->id, "GroupName" => htmlspecialchars($_group->GroupName)];
            }
        }
        return true;
    }
    public function add()
    {
        if(!$this->validate()) {
            return false;
        }
        $result = Database_Model::getInstance()->insert("HostFact_Products", ["ProductCode" => $this->ProductCode, "Status" => $this->Status, "ParentIdentifier" => $this->ParentIdentifier, "ProductType" => $this->ProductType, "ProductTld" => $this->ProductTld, "ProductName" => $this->ProductName, "ProductKeyPhrase" => $this->ProductKeyPhrase, "ProductDescription" => $this->ProductDescription, "NumberSuffix" => $this->NumberSuffix, "HasCustomPrice" => $this->HasCustomPrice, "PriceExcl" => $this->PriceExcl, "PricePeriod" => $this->PricePeriod, "TaxPercentage" => $this->TaxPercentage, "Cost" => $this->Cost, "Ordered" => $this->Ordered, "Sold" => $this->Sold])->execute();
        if($result) {
            $this->Identifier = $result;
            foreach ($this->Groups as $key => $value) {
                $input = is_numeric($value) ? $value : $key;
                Database_Model::getInstance()->insert("HostFact_GroupRelations", ["Group" => $input, "Type" => "product", "Reference" => $this->Identifier])->execute();
            }
            $this->Success[] = sprintf(__("product is created"), $this->ProductName);
            $product_info = ["id" => $this->Identifier, "ProductCode" => $this->ProductCode, "ProductType" => $this->ProductType];
            do_action("product_is_created", $product_info);
            return true;
        } else {
            return false;
        }
    }
    public function edit($id = "")
    {
        $id = 0 < $id ? $id : $this->Identifier;
        if(!is_numeric($id)) {
            $this->Error[] = __("invalid identifier for product");
            return false;
        }
        if(!$this->validate()) {
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Products", ["ProductCode" => $this->ProductCode, "Status" => $this->Status, "ParentIdentifier" => $this->ParentIdentifier, "ProductType" => $this->ProductType, "ProductTld" => $this->ProductTld, "ProductName" => $this->ProductName, "ProductKeyPhrase" => $this->ProductKeyPhrase, "ProductDescription" => $this->ProductDescription, "NumberSuffix" => $this->NumberSuffix, "HasCustomPrice" => $this->HasCustomPrice, "PriceExcl" => $this->PriceExcl, "PricePeriod" => $this->PricePeriod, "TaxPercentage" => $this->TaxPercentage, "Cost" => $this->Cost, "Ordered" => $this->Ordered, "Sold" => $this->Sold])->where("id", $id)->execute();
        if($result) {
            Database_Model::getInstance()->delete("HostFact_GroupRelations")->where("Type", "product")->where("Reference", $this->Identifier)->execute();
            foreach ($this->Groups as $key => $value) {
                $input = is_numeric($value) ? $value : $key;
                Database_Model::getInstance()->insert("HostFact_GroupRelations", ["Group" => $input, "Type" => "product", "Reference" => $this->Identifier])->execute();
            }
            if(isset($this->OldProductCode) && $this->OldProductCode && $this->OldProductCode != $this->ProductCode) {
                Database_Model::getInstance()->update("HostFact_InvoiceElements", ["ProductCode" => $this->ProductCode])->where("ProductCode", $this->OldProductCode)->execute();
                Database_Model::getInstance()->update("HostFact_PeriodicElements", ["ProductCode" => $this->ProductCode])->where("ProductCode", $this->OldProductCode)->execute();
                Database_Model::getInstance()->update("HostFact_PriceQuoteElements", ["ProductCode" => $this->ProductCode])->where("ProductCode", $this->OldProductCode)->execute();
            }
            $this->Success[] = sprintf(__("product is adjusted"), $this->ProductName);
            $product_info = ["id" => $this->Identifier, "ProductCode" => $this->ProductCode, "ProductType" => $this->ProductType];
            do_action("product_is_edited", $product_info);
            return true;
        } else {
            return false;
        }
    }
    public function delete($id)
    {
        $id = 0 < $id ? $id : $this->Identifier;
        if(!is_numeric($id)) {
            $this->Error[] = __("invalid identifier for product");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_Products", ["Status", "ProductCode"])->where("id", $id)->execute();
        if(!$result) {
            $this->Error[] = __("invalid identifier for product");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Products", ["Status" => "9"])->where("id", $id)->execute();
        if(!$result) {
            return false;
        }
        $result = Database_Model::getInstance()->delete("HostFact_GroupRelations")->where("Reference", $id)->where("Type", "product")->execute();
        $this->Success[] = sprintf(__("product is removed"), $this->ProductName);
        $result = Database_Model::getInstance()->update("HostFact_PeriodicElements", ["ProductCode" => ""])->where("ProductCode", $this->ProductCode)->execute();
        if(!$result) {
            return false;
        }
        return true;
    }
    public function remove($id)
    {
        if(!is_numeric($id)) {
            $this->Error[] = __("invalid identifier for product");
            return false;
        }
        $result = Database_Model::getInstance()->delete("HostFact_Products")->where("id", $id)->execute();
        if(!$result) {
            return false;
        }
        if($this->HasCustomPrice == "period") {
            $this->removeCustomProductPrices();
        }
        return true;
    }
    public function newProductCode($prefix = PRODUCTCODE_PREFIX, $number = PRODUCTCODE_NUMBER)
    {
        $prefix = parsePrefixVariables($prefix);
        $length = strlen($prefix . $number);
        $result = Database_Model::getInstance()->getOne("HostFact_Products", ["ProductCode"])->where("ProductCode", ["LIKE" => $prefix . "%"])->where("LENGTH(`ProductCode`)", [">=" => $length])->where("(SUBSTR(`ProductCode`,:PrefixLength)*1)", [">" => 0])->where("SUBSTR(`ProductCode`,:PrefixLength)", ["REGEXP" => "^[0-9]+\$"])->orderBy("(SUBSTR(`ProductCode`,:PrefixLength)*1)", "DESC")->bindValue("PrefixLength", strlen($prefix) + 1)->execute();
        if(isset($result->ProductCode) && $result->ProductCode && is_numeric(substr($result->ProductCode, strlen($prefix)))) {
            $Code = substr($result->ProductCode, strlen($prefix));
            $Code = $prefix . @str_repeat("0", @max(@strlen($number) - @strlen(@max($Code + 1, $number)), 0)) . max($Code + 1, $number);
        } else {
            $Code = $prefix . $number;
        }
        if(!$this->is_free($Code)) {
            $this->Error[] = sprintf(__("generated productcode is already in use"), $Code);
            return false;
        }
        return $Code;
    }
    public function validate()
    {
        $this->PriceExcl = deformat_money($this->PriceExcl);
        $this->Cost = deformat_money($this->Cost);
        if(!$this->is_free($this->ProductCode)) {
            $this->Error[] = __("productcode already exists");
        }
        if(!array_key_exists($this->Status, [0, 1, 9])) {
            $this->Error[] = __("unknown status selected for product");
        }
        global $array_producttypes;
        if(!array_key_exists($this->ProductType, $array_producttypes)) {
            $this->Error[] = __("unknown type selected for product");
        }
        if(($this->ProductType == "domain" || $this->ProductType == "hosting") && $this->PricePeriod == "") {
            $this->Error[] = __("invalid period for hosting or domain product");
        }
        if(!(is_string($this->ProductName) && strlen($this->ProductName) <= 100 && 0 < strlen($this->ProductName))) {
            $this->Error[] = __("invalid productname");
        }
        if(!(is_string($this->ProductKeyPhrase) && strlen($this->ProductKeyPhrase) <= 21845 && 0 < strlen($this->ProductKeyPhrase))) {
            $this->Error[] = __("invalid productkeyphrase");
        }
        if(!is_string($this->ProductDescription)) {
            $this->Error[] = __("invalid productdescription");
        }
        if($this->NumberSuffix && 19 < mb_strlen($this->NumberSuffix)) {
            $this->Error[] = __("product numbersuffix is too long");
        } elseif($this->NumberSuffix && preg_match("/^[\\s.,\\d]/", $this->NumberSuffix)) {
            $this->Error[] = __("product numbersuffix invalid start character");
        }
        if(!is_numeric($this->PriceExcl)) {
            $this->Error[] = __("invalid priceexcl product");
        }
        global $array_periodes;
        if($this->PricePeriod != "" && !array_key_exists($this->PricePeriod, $array_periodes)) {
            $this->Error[] = __("unknown priceperiod");
        }
        if(!((is_float($this->TaxPercentage * 1) || ($this->TaxPercentage = "0.00")) && 0 <= $this->TaxPercentage && $this->TaxPercentage <= 1)) {
            $this->Error[] = __("invalid taxpercentage");
        }
        if($this->Cost != "" && !is_numeric($this->Cost)) {
            $this->Error[] = __("invalid costprice");
        }
        if($this->ProductType == "domain" && $this->ProductTld && substr($this->ProductTld, 0, 1) == ".") {
            $this->ProductTld = substr($this->ProductTld, 1);
        }
        return empty($this->Error) ? true : false;
    }
    public function getID($method, $value)
    {
        switch ($method) {
            case "identifier":
                $product_id = Database_Model::getInstance()->getOne("HostFact_Products", "id")->where("id", intval($value))->execute();
                return $product_id !== false ? $product_id->id : false;
                break;
            case "productcode":
                $product_id = Database_Model::getInstance()->getOne("HostFact_Products", "id")->where("ProductCode", $value)->execute();
                return $product_id !== false ? $product_id->id : false;
                break;
        }
    }
    public function _checkGroup($productGroupId)
    {
        if(!is_numeric($productGroupId)) {
            return false;
        }
        $productGroup_id = Database_Model::getInstance()->getOne("HostFact_Group", ["id"])->where("Type", "product")->where("id", $productGroupId)->execute();
        return $productGroup_id !== false && 0 < $productGroup_id->id ? $productGroup_id->id : false;
    }
    public function is_free($ProductCode)
    {
        if($ProductCode) {
            $result = Database_Model::getInstance()->getOne("HostFact_Products", ["id"])->where("ProductCode", $ProductCode)->execute();
            if(!$result || $result->id == $this->Identifier) {
                return true;
            }
            return false;
        }
        return false;
    }
    public function recalculate()
    {
        $this->Sold = 0;
        $result = Database_Model::getInstance()->getOne("HostFact_InvoiceElements", ["COUNT(`id`) as Number", "SUM(`Number`*`Periods`*IF(`PriceExcl` >= 0,1,-1)) as Sold"])->where("ProductCode", $this->ProductCode)->groupBy("ProductCode")->execute();
        $this->Sold = $result ? $result->Sold : 0;
        $this->Ordered = 0;
        $result = Database_Model::getInstance()->getOne("HostFact_NewOrderElements", ["COUNT(`id`) as Number", "SUM(`Number`*`Periods`) as Ordered"])->where("ProductCode", $this->ProductCode)->groupBy("ProductCode")->execute();
        $this->Ordered = $result ? $result->Ordered : 0;
        Database_Model::getInstance()->update("HostFact_Products", ["Sold" => $this->Sold, "Ordered" => $this->Ordered])->where("ProductCode", $this->ProductCode)->execute();
    }
    public function all($fields, $sort = "ProductCode", $order = false, $limit = "-1", $searchat = false, $searchfor = false, $group = false, $show_results = MAX_RESULTS_LIST)
    {
        $limit = !is_numeric($show_results) ? -1 : $limit;
        if(!is_array($fields) || empty($fields)) {
            $this->Error[] = __("no fields given for retrieving data");
            return false;
        }
        if(!is_numeric($show_results)) {
            $this->Error[] = __("invalid number for displaying results");
            return false;
        }
        if($group !== false && is_array($group) && 0 < count($group)) {
            $filters = $group;
            if(array_key_exists("group", $group)) {
                $group = $group["group"];
                unset($filters["group"]);
            }
        }
        $search_at = [];
        if($searchat && $searchfor) {
            $search_at = explode("|", $searchat);
        }
        $select = ["HostFact_Products.id", (VAT_CALC_METHOD == "incl" ? "(HostFact_Products.`PriceExcl` * ROUND((1+HostFact_Products.`TaxPercentage`),2))" : "(ROUND(HostFact_Products.`PriceExcl`,2) * ROUND((1+HostFact_Products.`TaxPercentage`),2))") . " as PriceIncl"];
        foreach ($fields as $column) {
            if($column == "Groups") {
            } else {
                $select[] = "HostFact_Products.`" . $column . "`";
            }
        }
        if(is_numeric($group)) {
            Database_Model::getInstance()->get(["HostFact_Products", "HostFact_GroupRelations"], $select)->where("HostFact_GroupRelations.`Group`", $group)->where("HostFact_GroupRelations.`Reference` = HostFact_Products.`id`");
        } elseif($group) {
            Database_Model::getInstance()->get(["HostFact_Products", "HostFact_GroupRelations", "HostFact_Group"], $select)->where("HostFact_Group.GroupName", $group)->where("HostFact_Group.`id` = HostFact_GroupRelations.`Group`")->where("HostFact_GroupRelations.`Type`", "product")->where("HostFact_GroupRelations.`Reference` = HostFact_Products.`id`");
        } else {
            Database_Model::getInstance()->get("HostFact_Products", $select)->where("HostFact_Products.Status", ["!=" => "9"]);
        }
        if(isset($this->OnlyProductType) && is_array($this->OnlyProductType)) {
            Database_Model::getInstance()->where("HostFact_Products.ProductType", ["IN" => $this->OnlyProductType]);
        }
        if(!empty($search_at)) {
            $or_clausule = [];
            foreach ($search_at as $searchColumn) {
                if($searchColumn == "concatProductCodeProductName") {
                    $or_clausule[] = ["CONCAT(HostFact_Products.`ProductCode`,' ',HostFact_Products.`ProductName`)", ["LIKE" => "%" . $searchfor . "%"]];
                } elseif(in_array($searchColumn, ["ProductTld"])) {
                    $or_clausule[] = ["HostFact_Products.`" . $searchColumn . "`", $searchfor];
                } else {
                    $or_clausule[] = ["HostFact_Products.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                }
            }
            Database_Model::getInstance()->orWhere($or_clausule);
        }
        $order = $sort ? $order ? $order : "ASC" : "";
        if($sort == "AmountIncl" || $sort == "PriceIncl") {
            Database_Model::getInstance()->orderBy("ROUND(HostFact_Products.`PriceExcl` * (1+HostFact_Products.`TaxPercentage`),2)", $order);
        } elseif($sort == "ProductCode") {
            $order = $order ? $order : "ASC";
            Database_Model::getInstance()->orderBy("IF(SUBSTRING(HostFact_Products.`ProductCode`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(HostFact_Products.`ProductCode`,1,1))", $order)->orderBy("LENGTH(HostFact_Products.`ProductCode`)", $order)->orderBy("HostFact_Products.`ProductCode`", $order);
        } elseif($sort) {
            Database_Model::getInstance()->orderBy("HostFact_Products." . $sort, $order);
        }
        if(0 <= $limit) {
            Database_Model::getInstance()->limit((max(1, $limit) - 1) * $show_results, $show_results);
        }
        if(!empty($filters)) {
            foreach ($filters as $_db_field => $_db_value) {
                switch ($_db_field) {
                    case "created":
                        if(isset($_db_value["from"]) && $_db_value["from"]) {
                            Database_Model::getInstance()->where("HostFact_Products.Created", [">=" => $_db_value["from"]]);
                        }
                        if(isset($_db_value["to"]) && $_db_value["to"]) {
                            Database_Model::getInstance()->where("HostFact_Products.Created", ["<=" => $_db_value["to"]]);
                        }
                        break;
                    case "modified":
                        if(isset($_db_value["from"]) && $_db_value["from"]) {
                            Database_Model::getInstance()->where("HostFact_Products.Modified", [">=" => $_db_value["from"]]);
                        }
                        if(isset($_db_value["to"]) && $_db_value["to"]) {
                            Database_Model::getInstance()->where("HostFact_Products.Modified", ["<=" => $_db_value["to"]]);
                        }
                        break;
                }
            }
        }
        $list = [];
        $list["CountRows"] = 0;
        if($product_list = Database_Model::getInstance()->execute()) {
            if($group) {
                $list["CountRows"] = Database_Model::getInstance()->rowCount(["HostFact_Products", "HostFact_GroupRelations"], "HostFact_Products.id");
            } else {
                $list["CountRows"] = Database_Model::getInstance()->rowCount("HostFact_Products", "HostFact_Products.id");
            }
            foreach ($product_list as $result) {
                $list[$result->id] = ["id" => $result->id, "PriceIncl" => $result->PriceIncl];
                foreach ($fields as $column) {
                    if(in_array($column, $this->Variables) || in_array($column, ["Created", "Modified"])) {
                        $list[$result->id][$column] = htmlspecialchars($result->{$column});
                    } elseif($column == "Groups") {
                        $list[$result->id][$column] = [];
                        $result_db = Database_Model::getInstance()->get(["HostFact_Group", "HostFact_GroupRelations"], ["HostFact_Group.id", "HostFact_Group.GroupName"])->where("HostFact_GroupRelations.Reference", $result->id)->where("HostFact_GroupRelations.Type", "product")->where("HostFact_GroupRelations.`Group` = HostFact_Group.`id`")->where("HostFact_Group.Type", "product")->where("HostFact_Group.Status", ["!=" => "9"])->execute();
                        if($result_db && is_array($result_db)) {
                            foreach ($result_db as $result_group) {
                                $list[$result->id][$column][$result_group->id] = ["id" => $result_group->id, "GroupName" => htmlspecialchars($result_group->GroupName)];
                            }
                        }
                    }
                }
            }
        }
        return $list;
    }
    public function deconnectPeriodic($periodic_list)
    {
        if(!is_array($periodic_list) || count($periodic_list) === 0) {
            $this->Error[] = __("no subscriptions to unconnect from product");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_PeriodicElements", ["ProductCode" => ""])->where("id", ["IN" => $periodic_list])->execute();
        if($result) {
            $this->Success[] = sprintf(__("subscriptions unconnected from product"), count($periodic_list));
            return true;
        }
        return false;
    }
    public function getStatistics()
    {
        $years = [];
        $years[date("Y") - 1] = ["Sold" => 0, "Sales" => 0];
        $years[date("Y")] = ["Sold" => 0, "Sales" => 0];
        for ($i = 1; $i <= 12; $i++) {
            $years[date("Y") - 1][$i] = ["Sold" => 0, "Sales" => 0];
            $years[date("Y")][$i] = ["Sold" => 0, "Sales" => 0];
        }
        $result_db = Database_Model::getInstance()->get(["HostFact_InvoiceElements", "HostFact_Invoice"], ["SUM(HostFact_InvoiceElements.`Number`*HostFact_InvoiceElements.`Periods`*IF(HostFact_InvoiceElements.`PriceExcl`>=0,1,-1)) as `Sold`", "SUM(HostFact_InvoiceElements.`Number`*HostFact_InvoiceElements.`Periods`*HostFact_InvoiceElements.`PriceExcl`) as `Sales`", "DATE_FORMAT(HostFact_Invoice.`Date`, '%Y-%m') as Date"])->where("HostFact_InvoiceElements.InvoiceCode = HostFact_Invoice.InvoiceCode")->where("HostFact_InvoiceElements.ProductCode", $this->ProductCode)->where("HostFact_Invoice.Date", ["<=" => date("Y-12-31")])->where("HostFact_Invoice.Date", [">=" => date("Y") - 1 . "-01-01"])->groupBy(["RAW" => "DATE_FORMAT(HostFact_Invoice.`Date`, '%Y-%m')"])->orderBy("HostFact_Invoice.Date", "DESC")->asArray()->execute();
        $result = [];
        if($result_db && is_array($result_db)) {
            foreach ($result_db as $var) {
                $result[] = $var;
            }
        }
        foreach ($result as $k => $v) {
            if(isset($years[substr($v["Date"], 0, 4)])) {
                $years[substr($v["Date"], 0, 4)][(int) substr($v["Date"], 5)]["Sold"] = $v["Sold"];
                $years[substr($v["Date"], 0, 4)][(int) substr($v["Date"], 5)]["Sales"] = $v["Sales"];
                $years[substr($v["Date"], 0, 4)]["Sold"] += $v["Sold"];
                $years[substr($v["Date"], 0, 4)]["Sales"] += $v["Sales"];
            }
        }
        return $years;
    }
    public function resetProductType($id)
    {
        if(!is_numeric($id)) {
            $this->Error[] = __("invalid identifier for product");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Products", ["ProductType" => "other", "ProductTld" => "", "PackageID" => "0"])->where("id", $id)->execute();
        if($result) {
            return true;
        }
        return false;
    }
    public function show_by_tld($tld)
    {
        if(!$tld) {
            $this->Error[] = __("invalid identifier for product");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_Products", ["*"])->where("ProductTld", $tld)->where("Status", "1")->execute();
        if(!$result || empty($result->id)) {
            $this->Error[] = __("invalid identifier for product");
            return false;
        }
        foreach ($result as $key => $value) {
            $this->{$key} = htmlspecialchars($value);
        }
        $this->Identifier = $this->id;
        return true;
    }
    public function updatePackageID($product_id, $package_id, $product_type = "hosting")
    {
        if(!is_numeric($product_id)) {
            $this->Error[] = __("invalid identifier for product");
            return false;
        }
        if(0 < $package_id) {
            $result = Database_Model::getInstance()->update("HostFact_Products", ["PackageID" => $package_id, "ProductType" => $product_type])->where("id", $product_id)->execute();
        } else {
            $result = Database_Model::getInstance()->update("HostFact_Products", ["PackageID" => "0", "ProductType" => "other"])->where("id", $product_id)->execute();
        }
        if(!$result) {
            return false;
        }
        return true;
    }
    public function getPackageIDAndType($product_id)
    {
        $result = Database_Model::getInstance()->getOne("HostFact_Products", ["ProductType", "PackageID"])->where("id", $product_id)->execute();
        return $result;
    }
    public function disconnectPackageID($package_id, $product_type)
    {
        Database_Model::getInstance()->update("HostFact_Products", ["ProductType" => "other", "PackageID" => "0"])->where("PackageID", $package_id)->where("ProductType", $product_type)->execute();
    }
    public function listCustomProductPrices()
    {
        $result = Database_Model::getInstance()->get("HostFact_Product_Custom_Prices", ["*"])->where("ProductID", $this->Identifier)->execute();
        if(!$result) {
            return false;
        }
        $price_array = [];
        foreach ($result as $tmp_price) {
            $price_array[$tmp_price->PriceType][$tmp_price->Periods . "-" . $tmp_price->Periodic]["PriceExcl"] = $tmp_price->PriceExcl;
            $price_array[$tmp_price->PriceType][$tmp_price->Periods . "-" . $tmp_price->Periodic]["PriceIncl"] = defined("VAT_CALC_METHOD") && VAT_CALC_METHOD == "incl" ? round($tmp_price->PriceExcl * (1 + $this->TaxPercentage), 5) : round(round((double) $tmp_price->PriceExcl, 2) * (1 + $this->TaxPercentage), 2);
        }
        if(isset($price_array["period"])) {
            $price_array["period"]["default"]["PriceExcl"] = $this->PriceExcl;
            $price_array["period"]["default"]["PriceIncl"] = $this->PriceIncl;
        }
        return $price_array;
    }
    public function removeCustomProductPrices()
    {
        $result = Database_Model::getInstance()->delete("HostFact_Product_Custom_Prices")->where("ProductID", $this->Identifier)->execute();
        return $result ? true : false;
    }
    public function updateCustomProductPrices($extra_prices_array)
    {
        $update_list = [];
        foreach ($extra_prices_array as $_tmp) {
            if($_tmp["Periods"] == 1 && $_tmp["Periodic"] == $this->PricePeriod) {
                $this->Error[] = __("it is not possible to give a custom price for the same period as default price");
                return false;
            }
            if(isset($update_list[$_tmp["Periods"] . "-" . $_tmp["Periodic"]])) {
                $this->Error[] = __("it is not possible to enter multiple prices for the same period");
                return false;
            }
            $update_list[$_tmp["Periods"] . "-" . $_tmp["Periodic"]]["Periods"] = $_tmp["Periods"];
            $update_list[$_tmp["Periods"] . "-" . $_tmp["Periodic"]]["Periodic"] = $_tmp["Periodic"];
            if(defined("VAT_CALC_METHOD") && VAT_CALC_METHOD == "incl" && isset($_tmp["PriceIncl"])) {
                $update_list[$_tmp["Periods"] . "-" . $_tmp["Periodic"]]["PriceExcl"] = deformat_money($_tmp["PriceIncl"]) / (1 + $this->TaxPercentage);
            } else {
                $update_list[$_tmp["Periods"] . "-" . $_tmp["Periodic"]]["PriceExcl"] = deformat_money($_tmp["PriceExcl"]);
            }
        }
        $this->removeCustomProductPrices();
        foreach ($update_list as $_tmp) {
            Database_Model::getInstance()->insert("HostFact_Product_Custom_Prices", ["ProductID" => $this->Identifier, "PriceType" => "period", "Periods" => $_tmp["Periods"], "Periodic" => $_tmp["Periodic"], "PriceExcl" => $_tmp["PriceExcl"]])->execute();
        }
        return true;
    }
    public function fixCustomPriceSetting()
    {
        $result = Database_Model::getInstance()->get("HostFact_Product_Custom_Prices", ["*"])->where("ProductID", $this->Identifier)->execute();
        $has_custom_price = $result ? "period" : "no";
        if($this->HasCustomPrice !== $has_custom_price) {
            $this->HasCustomPrice = $has_custom_price;
            Database_Model::getInstance()->update("HostFact_Products", ["HasCustomPrice" => $this->HasCustomPrice])->where("id", $this->Identifier)->execute();
        }
    }
}

?>