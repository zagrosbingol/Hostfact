<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class UpgradeGroup_Model
{
    public $Identifier;
    public $Name;
    public $Status;
    public $Products;
    public $ServiceType;
    public $Error;
    public $Success;
    public $Warning;
    public $Variables = ["id", "Name", "Products", "ServiceType"];
    public function __construct($service_type)
    {
        $this->ServiceType = $service_type;
        $this->Status = "1";
        $this->Error = [];
        $this->Success = [];
        $this->Warning = [];
    }
    public function getProductsInUpgradegroups()
    {
        switch ($this->ServiceType) {
            case "hosting":
                $product_type = "hosting";
                break;
            default:
                $result = Database_Model::getInstance()->getOne("HostFact_UpgradeGroups", ["GROUP_CONCAT(`Products`) AS Products"])->where("ServiceType", $product_type)->where("Status", ["!=" => 9])->orderBy("id", "ASC")->asArray()->execute();
                return $result;
        }
    }
    public function add()
    {
        if(!$this->validate()) {
            return false;
        }
        $result = Database_Model::getInstance()->insert("HostFact_UpgradeGroups", ["Name" => $this->Name, "Status" => $this->Status, "Products" => $this->Products, "ServiceType" => $this->ServiceType])->execute();
        if($result) {
            $this->Success[] = sprintf(__("upgradegroup is created"), $this->Name);
            return true;
        }
        return false;
    }
    public function edit()
    {
        if(!$this->validate()) {
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_UpgradeGroups", ["Name" => $this->Name, "Status" => $this->Status, "Products" => $this->Products, "ServiceType" => $this->ServiceType])->where("id", $this->Identifier)->execute();
        if($result) {
            $this->Success[] = sprintf(__("upgradegroup is adjusted"), $this->Name);
            return true;
        }
        return false;
    }
    public function show($extra_product_info = false)
    {
        $result = Database_Model::getInstance()->getOne("HostFact_UpgradeGroups", ["Name", "Products", "ServiceType"])->where("id", $this->Identifier)->execute();
        foreach ($result as $key => $value) {
            $this->{$key} = htmlspecialchars($value);
        }
        if(!$result) {
            return false;
        }
        if($extra_product_info === true) {
            $pdo_statement = Database_Model::getInstance()->rawQuery("SELECT id, ProductCode, ProductName, PriceExcl, TaxPercentage, PricePeriod, PackageID, ProductKeyPhrase, " . (VAT_CALC_METHOD == "incl" ? "(`PriceExcl` * ROUND((1+`TaxPercentage`),2))" : "(ROUND(`PriceExcl`,2) * ROUND((1+`TaxPercentage`),2))") . " as PriceIncl \n                                            FROM `HostFact_Products` \n                                            WHERE FIND_IN_SET(id, :product_ids) AND `Status` != '9' \n                                            ORDER BY FIELD(id," . $result->Products . ")", ["product_ids" => $result->Products]);
            if($products = $pdo_statement->fetchAll(PDO::FETCH_ASSOC)) {
                $this->ProductsInfo = $products;
            }
        }
        return true;
    }
    public function all($fields, $sort = "id", $order = "ASC", $extra_product_info = false)
    {
        if(!is_array($fields) || empty($fields)) {
            $this->Error[] = __("no fields given for retrieving data");
            return false;
        }
        $select = [];
        foreach ($fields as $column) {
            if(in_array($column, $this->Variables)) {
                $select[] = $column;
            }
        }
        Database_Model::getInstance()->get("HostFact_UpgradeGroups", $select);
        Database_Model::getInstance()->where("ServiceType", $this->ServiceType)->where("Status", ["!=" => 9]);
        Database_Model::getInstance()->orderBy($sort, $order);
        $result = Database_Model::getInstance()->asArray()->execute();
        if(!$result) {
            return false;
        }
        if($extra_product_info === true) {
            foreach ($result as $key => $value) {
                if(!$value["Products"]) {
                } else {
                    $pdo_statement = Database_Model::getInstance()->rawQuery("SELECT id, ProductName FROM `HostFact_Products` \n                                            WHERE FIND_IN_SET(id, :product_ids) AND `Status` !=  '9' \n                                            ORDER BY FIELD(id, " . $value["Products"] . ")", ["product_ids" => $value["Products"]]);
                    $products = $pdo_statement->fetchAll(PDO::FETCH_ASSOC);
                    if($products) {
                        $result[$key]["ProductsInfo"] = $products;
                    }
                }
            }
        }
        return $result;
    }
    public function delete()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for group");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_UpgradeGroups", ["Status" => 9])->where("ServiceType", $this->ServiceType)->where("id", $this->Identifier)->execute();
        if($result) {
            $this->Success[] = sprintf(__("upgradegroup is deleted"), $this->Name);
            return true;
        }
        return false;
    }
    public function validate()
    {
        if(!is_numeric($this->Status)) {
            $this->Error[] = sprintf(__("invalid status for upgradegroup"), $this->Name);
        }
        if($this->ServiceType != "hosting") {
            $this->Error[] = sprintf(__("invalid type for upgradegroup"), $this->Name);
        }
        if(!(is_string($this->Name) && strlen(trim($this->Name)) <= 100 && 0 < strlen(trim($this->Name)))) {
            $this->Error[] = sprintf(__("invalid name for upgradegroup"), $this->Name);
        } elseif(!$this->is_free($this->Name)) {
            $this->Error[] = sprintf(__("upgradegroup name already in use"), $this->Name);
        }
        if(0 < count($this->Error)) {
            return false;
        }
        return true;
    }
    public function is_free($name)
    {
        if($name) {
            $result = Database_Model::getInstance()->getOne("HostFact_UpgradeGroups", ["id"])->where("Name", $name)->where("ServiceType", $this->ServiceType)->where("Status", ["!=" => 9])->execute();
            if(!$result || $result->id == $this->Identifier) {
                return true;
            }
            return false;
        }
        return false;
    }
    public function showByProduct($product_id)
    {
        $result = Database_Model::getInstance()->getOne("HostFact_UpgradeGroups", ["id", "Name", "Products", "ServiceType"])->where("FIND_IN_SET(:product, `Products`)")->where("ServiceType", $this->ServiceType)->where("Status", ["!=" => 9])->bindValue("product", $product_id)->execute();
        if(!$result) {
            return false;
        }
        $this->Identifier = $result->id;
        if(!$this->show(true)) {
            return false;
        }
        return true;
    }
}

?>