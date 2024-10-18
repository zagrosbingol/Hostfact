<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class discount
{
    public $Identifier;
    public $Name;
    public $Description;
    public $Coupon;
    public $Discount;
    public $DiscountPercentage;
    public $DiscountPercentageType;
    public $StartDate;
    public $EndDate;
    public $Counter;
    public $Max;
    public $MaxPerInvoice;
    public $MinAmount;
    public $DocumentType;
    public $Debtor;
    public $DebtorGroup;
    public $Product1;
    public $ProductGroup1;
    public $Price1;
    public $Product2;
    public $ProductGroup2;
    public $Price2;
    public $Product3;
    public $ProductGroup3;
    public $Price3;
    public $Status;
    public $CountRows;
    public $Error;
    public $Warning;
    public $Success;
    public $Variables = ["Identifier", "Name", "Description", "Coupon", "Discount", "DiscountPercentage", "DiscountPart", "DiscountPartRestriction", "DiscountType", "DiscountPercentageType", "DiscountPeriod", "StartDate", "EndDate", "Counter", "Max", "MaxPerInvoice", "MinAmount", "DocumentType", "Debtor", "DebtorGroup", "Product1", "ProductGroup1", "Price1", "Product2", "ProductGroup2", "Price2", "Product3", "ProductGroup3", "Price3", "Status"];
    public function __construct()
    {
        $this->CountRows = 0;
        $this->Status = "1";
        $this->Error = [];
        $this->Warning = [];
        $this->Success = [];
        $this->Type = "invoice";
        $this->product_restriction = "no";
        $this->DebtorRestriction = "none";
        $this->DiscountType = "TotalAmount";
        $this->DebtorGroup = [];
        $this->DiscountPartRestriction = 0;
        $this->DiscountPart = 0;
        $this->DiscountPercentageType = "line";
        $this->DiscountPeriod = "once";
        for ($i = 1; $i <= 3; $i++) {
            $this->{"Restriction" . $i . "Type"} = "none";
            $this->{"Restriction" . $i . "Product"} = 0;
            $this->{"Restriction" . $i . "Group"} = 0;
        }
    }
    public function show()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for discount");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_Discount")->where("id", $this->Identifier)->execute();
        if(!$result || empty($result->id)) {
            $this->Error[] = __("invalid identifier for discount");
            return false;
        }
        foreach ($result as $key => $value) {
            $this->{$key} = htmlspecialchars($value);
        }
        $this->StartDate = rewrite_date_db2site($this->StartDate);
        $this->EndDate = rewrite_date_db2site($this->EndDate);
        $this->MinAmount = !isEmptyFloat($this->MinAmount) ? $this->MinAmount : "";
        $this->MaxPerInvoice = 0 < $this->MaxPerInvoice ? $this->MaxPerInvoice : "";
        $this->Max = 0 < $this->Max ? $this->Max : "";
        if(0 < $this->Debtor) {
            $this->DebtorRestriction = "debtor";
            $this->DebtorGroup = [];
        } elseif($this->DebtorGroup < 0 && is_numeric($this->DebtorGroup)) {
            $this->DebtorRestriction = $this->DebtorGroup;
            $this->DebtorGroup = [];
        } elseif(0 < strlen($this->DebtorGroup)) {
            $this->DebtorRestriction = "group";
            $this->DebtorGroup = explode(",", $this->DebtorGroup);
        }
        if(0 < $this->Product1 || 0 < $this->ProductGroup1 || 0 < $this->Product2 || 0 < $this->ProductGroup2 || 0 < $this->Product3 || 0 < $this->ProductGroup3) {
            $this->product_restriction = "yes";
            for ($i = 1; $i <= 3; $i++) {
                if(0 < $this->{"Product" . $i}) {
                    $this->{"Restriction" . $i . "Type"} = "product";
                    $this->{"Restriction" . $i . "Product"} = $this->{"Product" . $i};
                } elseif(0 < $this->{"ProductGroup" . $i}) {
                    $this->{"Restriction" . $i . "Type"} = "group";
                    $this->{"Restriction" . $i . "Group"} = $this->{"ProductGroup" . $i};
                }
            }
        }
        switch ($this->DiscountType) {
            case "PartialRestrictedPercentage":
                $this->PartialRestrictedDiscountPercentage = $this->DiscountPart;
                break;
            case "PartialPercentage":
                $this->PartialDiscountPercentage = $this->DiscountPart;
                $this->PartialDiscountPercentageRestriction = $this->DiscountPartRestriction;
                break;
            case "PartialAmount":
                $this->PartialDiscount = $this->DiscountPart;
                $this->PartialDiscountRestriction = $this->DiscountPartRestriction;
                break;
            default:
                return true;
        }
    }
    public function add()
    {
        $this->SyncDiscount();
        if($this->validate() === false) {
            $this->StartDate = rewrite_date_db2site($this->StartDate);
            $this->EndDate = rewrite_date_db2site($this->EndDate);
            if($this->Discount == "NULL") {
                $this->Discount = "";
            }
            if($this->DiscountPercentage == "NULL") {
                $this->DiscountPercentage = "";
            }
            if($this->Max == "0") {
                $this->Max = "";
            }
            return false;
        }
        $result = Database_Model::getInstance()->insert("HostFact_Discount", ["Name" => $this->Name, "Description" => $this->Description, "Coupon" => $this->Coupon, "Discount" => $this->Discount, "DiscountPercentage" => $this->DiscountPercentage, "DiscountPart" => $this->DiscountPart, "DiscountPartRestriction" => $this->DiscountPartRestriction, "DiscountType" => $this->DiscountType, "DiscountPercentageType" => $this->DiscountPercentageType, "DiscountPeriod" => $this->DiscountPeriod, "StartDate" => $this->StartDate, "EndDate" => $this->EndDate, "Counter" => $this->Counter, "Max" => $this->Max, "MaxPerInvoice" => $this->MaxPerInvoice, "MinAmount" => $this->MinAmount, "DocumentType" => $this->DocumentType, "Debtor" => $this->Debtor, "DebtorGroup" => $this->DebtorGroup, "Product1" => $this->Product1, "ProductGroup1" => $this->ProductGroup1, "Price1" => $this->Price1 == "NULL" ? ["RAW" => NULL] : $this->Price1, "Product2" => $this->Product2, "ProductGroup2" => $this->ProductGroup2, "Price2" => $this->Price2 == "NULL" ? ["RAW" => NULL] : $this->Price2, "Product3" => $this->Product3, "ProductGroup3" => $this->ProductGroup3, "Price3" => $this->Price3 == "NULL" ? ["RAW" => NULL] : $this->Price3, "Status" => $this->Status])->execute();
        if($result) {
            $this->Identifier = $result;
            $this->Success[] = sprintf(__("discount is created"), $this->Name);
            return true;
        }
        $this->StartDate = rewrite_date_db2site($this->StartDate);
        $this->EndDate = rewrite_date_db2site($this->EndDate);
        if($this->Discount == "NULL") {
            $this->Discount = "";
        }
        if($this->DiscountPercentage == "NULL") {
            $this->DiscountPercentage = "";
        }
        if($this->Max == "0") {
            $this->Max = "";
        }
        return false;
    }
    public function edit()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for discount");
            return false;
        }
        $this->SyncDiscount();
        if($this->validate() === false) {
            $this->StartDate = rewrite_date_db2site($this->StartDate);
            $this->EndDate = rewrite_date_db2site($this->EndDate);
            if($this->Discount == "NULL") {
                $this->Discount = "";
            }
            if($this->DiscountPercentage == "NULL") {
                $this->DiscountPercentage = "";
            }
            if($this->Max == "0") {
                $this->Max = "";
            }
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Discount", ["Name" => $this->Name, "Description" => $this->Description, "Coupon" => $this->Coupon, "Discount" => $this->Discount, "DiscountPercentage" => $this->DiscountPercentage, "DiscountPart" => $this->DiscountPart, "DiscountPartRestriction" => $this->DiscountPartRestriction, "DiscountType" => $this->DiscountType, "DiscountPercentageType" => $this->DiscountPercentageType, "DiscountPeriod" => $this->DiscountPeriod, "StartDate" => $this->StartDate, "EndDate" => $this->EndDate, "Counter" => $this->Counter, "Max" => $this->Max, "MaxPerInvoice" => $this->MaxPerInvoice, "MinAmount" => $this->MinAmount, "DocumentType" => $this->DocumentType, "Debtor" => $this->Debtor, "DebtorGroup" => $this->DebtorGroup, "Product1" => $this->Product1, "ProductGroup1" => $this->ProductGroup1, "Price1" => $this->Price1 == "NULL" ? ["RAW" => NULL] : $this->Price1, "Product2" => $this->Product2, "ProductGroup2" => $this->ProductGroup2, "Price2" => $this->Price2 == "NULL" ? ["RAW" => NULL] : $this->Price2, "Product3" => $this->Product3, "ProductGroup3" => $this->ProductGroup3, "Price3" => $this->Price3 == "NULL" ? ["RAW" => NULL] : $this->Price3, "Status" => $this->Status])->where("id", $this->Identifier)->execute();
        if($result) {
            $this->Success[] = sprintf(__("discount is adjusted"), $this->Name);
            return true;
        }
        $this->StartDate = rewrite_date_db2site($this->StartDate);
        $this->EndDate = rewrite_date_db2site($this->EndDate);
        if($this->Discount == "NULL") {
            $this->Discount = "";
        }
        if($this->DiscountPercentage == "NULL") {
            $this->DiscountPercentage = "";
        }
        if($this->Max == "0") {
            $this->Max = "";
        }
        return false;
    }
    public function SyncDiscount()
    {
        switch ($this->DebtorRestriction) {
            case "group":
                $this->Debtor = 0;
                $this->DebtorGroup = is_array($this->DebtorGroup) ? implode(",", $this->DebtorGroup) . "," : $this->DebtorGroup;
                break;
            case "debtor":
                $this->DebtorGroup = "";
                break;
            case "-1":
                $this->Debtor = 0;
                $this->DebtorGroup = "-1";
                break;
            case "-2":
                $this->Debtor = 0;
                $this->DebtorGroup = "-2";
                break;
            case "-3":
                $this->Debtor = 0;
                $this->DebtorGroup = "-3";
                break;
            default:
                $this->Debtor = 0;
                $this->DebtorGroup = "";
                switch ($this->product_restriction) {
                    case "no":
                        $this->Product1 = $this->ProductGroup1 = $this->Price1 = NULL;
                        $this->Product2 = $this->ProductGroup2 = $this->Price2 = NULL;
                        $this->Product3 = $this->ProductGroup3 = $this->Price3 = NULL;
                        break;
                    case "yes":
                        $this->Product1 = $this->ProductGroup1 = $this->Price1 = NULL;
                        $this->Product2 = $this->ProductGroup2 = $this->Price2 = NULL;
                        $this->Product3 = $this->ProductGroup3 = $this->Price3 = NULL;
                        $i = 1;
                        while ($i <= 3) {
                            switch ($this->{"Restriction" . $i . "Type"}) {
                                case "product":
                                    $this->{"Product" . $i} = $this->{"Restriction" . $i . "Product"};
                                    $this->{"ProductGroup" . $i} = $this->{"Price" . $i} = NULL;
                                    break;
                                case "group":
                                    $this->{"ProductGroup" . $i} = $this->{"Restriction" . $i . "Group"};
                                    $this->{"Product" . $i} = $this->{"Price" . $i} = NULL;
                                    break;
                                default:
                                    $this->{"Product" . $i} = $this->{"ProductGroup" . $i} = $this->{"Price" . $i} = NULL;
                                    $i++;
                            }
                        }
                        break;
                    default:
                        switch ($this->DiscountType) {
                            case "TotalAmount":
                                $this->DiscountPercentage = 0;
                                $this->DiscountPercentageType = "line";
                                break;
                            case "TotalPercentage":
                                $this->Discount = 0;
                                $this->DiscountPercentageType = "line";
                                break;
                            case "PartialRestrictedPercentage":
                                $this->DiscountPart = $this->PartialRestrictedDiscountPercentage;
                                $this->DiscountPercentage = $this->Discount = 0;
                                $this->DiscountPercentageType = $this->PartialRestrictedDiscountPercentageType;
                                break;
                            case "PartialPercentage":
                                $this->DiscountPart = $this->PartialDiscountPercentage;
                                $this->DiscountPartRestriction = $this->PartialDiscountPercentageRestriction;
                                $this->DiscountPercentage = $this->Discount = 0;
                                $this->DiscountPercentageType = $this->PartialDiscountPercentageType;
                                break;
                            case "PartialAmount":
                                $this->DiscountPart = $this->PartialDiscount;
                                $this->DiscountPartRestriction = $this->PartialDiscountRestriction;
                                $this->DiscountPercentage = $this->Discount = 0;
                                $this->DiscountPercentageType = "line";
                                break;
                            default:
                                switch ($this->Period) {
                                    case "always":
                                        $this->StartDate = $this->EndDate = "";
                                        break;
                                    case "till":
                                        $this->StartDate = "";
                                        break;
                                    default:
                                        return true;
                                }
                        }
                }
        }
    }
    public function delete()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for discount");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Discount", ["Status" => 9])->where("id", $this->Identifier)->execute();
        if($result) {
            $this->Success[] = __("discount is removed");
            return true;
        }
        return false;
    }
    public function validate()
    {
        $this->StartDate = $this->StartDate ? rewrite_date_site2db($this->StartDate) : "";
        $this->EndDate = $this->EndDate ? rewrite_date_site2db($this->EndDate) : "";
        $this->Price1 = deformat_money($this->Price1);
        $this->Price2 = deformat_money($this->Price2);
        $this->Price3 = deformat_money($this->Price3);
        $this->Discount = deformat_money($this->Discount);
        $this->DiscountPercentage = deformat_money($this->DiscountPercentage);
        $this->MinAmount = deformat_money($this->MinAmount);
        $this->DiscountPart = deformat_money($this->DiscountPart);
        if($this->Discount == "") {
            $this->Discount = "NULL";
        }
        if($this->DiscountPercentage == "") {
            $this->DiscountPercentage = "NULL";
        }
        if($this->Max == "") {
            $this->Max = "0";
        }
        if($this->Price1 == "") {
            $this->Price1 = "NULL";
        }
        if($this->Price2 == "") {
            $this->Price2 = "NULL";
        }
        if($this->Price3 == "") {
            $this->Price3 = "NULL";
        }
        if(!(is_string($this->Name) && strlen($this->Name) <= 100 && 0 < strlen($this->Name))) {
            $this->Error[] = __("invalid discount name");
        }
        if($this->DiscountType == "TotalAmount" && $this->Discount && !is_numeric($this->Discount)) {
            $this->Error[] = __("invalid discount");
        }
        if($this->DiscountType == "TotalPercentage") {
            if($this->DiscountPercentage && !is_numeric($this->DiscountPercentage) || $this->DiscountPercentage < 0 || 100 < $this->DiscountPercentage) {
                $this->Error[] = __("invalid discountpercentage");
            }
            if($this->DiscountPercentage && 2 < strlen(substr(strrchr($this->DiscountPercentage, "."), 1))) {
                $this->Error[] = __("invalid discountpercentage digits");
            }
        } elseif($this->DiscountType == "PartialRestrictedPercentage") {
            if($this->DiscountPart && !is_numeric($this->DiscountPart) || $this->DiscountPart < 0 || 100 < $this->DiscountPart) {
                $this->Error[] = __("invalid discountpercentage");
            }
            if($this->DiscountPart && 2 < strlen(substr(strrchr($this->DiscountPart, "."), 1))) {
                $this->Error[] = __("invalid discountpercentage digits");
            }
        } elseif($this->DiscountType == "PartialPercentage") {
            if($this->DiscountPart && !is_numeric($this->DiscountPart) || $this->DiscountPart < 0 || 100 < $this->DiscountPart) {
                $this->Error[] = __("invalid discountpercentage");
            }
            if($this->DiscountPart && 2 < strlen(substr(strrchr($this->DiscountPart, "."), 1))) {
                $this->Error[] = __("invalid discountpercentage digits");
            }
        } elseif($this->DiscountType == "PartialAmount" && $this->DocumentType == "order") {
            $this->Error[] = __("invalid combination discounttype and documenttype");
        }
        if(!in_array($this->DiscountPercentageType, ["line", "subscription"])) {
            $this->DiscountPercentageType = "line";
        }
        if(isset($this->DebtorRestriction) && $this->DebtorRestriction == "group" && strlen($this->DebtorGroup) <= 1) {
            $this->Error[] = __("discount no debtor groups selected");
        }
        if($this->StartDate == "-") {
            $this->Error[] = __("invalid startdate for discount");
        }
        if($this->StartDate && $this->EndDate && $this->EndDate < $this->StartDate) {
            $this->Error[] = __("startdate discount cannot be after enddate");
        }
        if(!is_numeric($this->Status)) {
            $this->Error[] = __("invalid discount status");
        }
        if($this->DiscountType == "PartialAmount" && defined("ORDERFORM_ENABLED") && ORDERFORM_ENABLED == "yes" && 0 < $this->MaxPerInvoice) {
            $this->Warning[] = __("invalid discount combination maxperinvoice partialamount and old orderform");
        }
        return empty($this->Error) ? true : false;
    }
    public function check($debtor, $elements_ori, $coupon = "", $incasso = "no", $VatCalcMethod = "excl")
    {
        $debtor_incasso = false;
        if(0 < $debtor && $incasso == "yes") {
            $debtor_incasso = true;
        }
        $array = [];
        $array1 = [];
        $array2 = [];
        $or_clausule = [];
        $or_clausule[] = ["AND" => [["Debtor", $debtor], ["Debtor", ["!=" => ""]]]];
        $or_clausule[] = ["(SELECT COUNT(HostFact_GroupRelations.`Group`) FROM `HostFact_GroupRelations` WHERE FIND_IN_SET( HostFact_GroupRelations.`Group` , HostFact_Discount.`DebtorGroup` ) AND HostFact_GroupRelations.`Reference`=:Debtor AND HostFact_GroupRelations.`Type`='debtor') > 0"];
        $or_clausule[] = ["AND" => [["Debtor", ""], ["DebtorGroup", ""]]];
        $or_clausule[] = ["AND" => [["DebtorGroup", "-2"], [0 < $debtor ? "`id` = `id`" : "`id` != `id`"]]];
        $or_clausule[] = ["AND" => [["DebtorGroup", "-1"], [(int) $debtor === 0 ? "`id` = `id`" : "`id` != `id`"]]];
        $or_clausule[] = ["AND" => [["DebtorGroup", "-3"], [$debtor_incasso === true ? "`id` = `id`" : "`id` != `id`"]]];
        Database_Model::getInstance()->get("HostFact_Discount")->where("Status", ["!=" => 9]);
        if($this->Type != "order") {
            Database_Model::getInstance()->where("DocumentType", ["!=" => "order"]);
        }
        $discount_list = Database_Model::getInstance()->orWhere($or_clausule)->orWhere([["Counter < Max"], ["Max", 0]])->orWhere([["AND" => [["StartDate", ["<=" => ["RAW" => "CURDATE()"]]], ["EndDate", [">=" => ["RAW" => "CURDATE()"]]]]], ["AND" => [["StartDate", ["<=" => ["RAW" => "CURDATE()"]]], ["EndDate", "0000-00-00 00:00:00"]]], ["AND" => [["StartDate", "0000-00-00 00:00:00"], ["EndDate", "0000-00-00 00:00:00"]]]])->orWhere([["MinAmount", 0], ["MinAmount", ["<=" => $elements_ori["AmountExcl"]]]])->where("Coupon", ["IN" => ["", $coupon]])->orderBy("DiscountPercentage", "ASC")->orderBy("Debtor", "ASC")->bindValue("Debtor", $debtor)->asArray()->execute();
        if($discount_list && is_array($discount_list)) {
            foreach ($discount_list as $var) {
                $array1[$var["id"]] = $var;
            }
        }
        foreach ($array1 as $key => $value) {
            $array2[$key] = [];
            $result = Database_Model::getInstance()->get("HostFact_Products", ["id", "ProductCode"])->where("id", ["IN" => [$value["Product1"], $value["Product2"], $value["Product3"]]])->execute();
            if($result && is_array($result)) {
                foreach ($result as $var) {
                    if(!in_array($var->ProductCode, $array2[$key])) {
                        $array2[$key][$var->id] = $var->ProductCode;
                    }
                }
            }
            $array21[$key] = [];
            $array22[$key] = [];
            $array23[$key] = [];
            for ($i = 1; $i <= 3; $i++) {
                $result = Database_Model::getInstance()->get(["HostFact_Products", "HostFact_GroupRelations"], ["HostFact_Products.id", "HostFact_Products.ProductCode"])->where("HostFact_GroupRelations.Type", "product")->where("HostFact_GroupRelations.Reference = HostFact_Products.id")->where("HostFact_GroupRelations.Group", $value["ProductGroup" . $i])->execute();
                if($result && is_array($result)) {
                    foreach ($result as $var) {
                        if(!in_array($var->ProductCode, ${"array2" . $i}[$key])) {
                            ${"array2" . $i}[$key][$var->id] = $var->ProductCode;
                        }
                    }
                }
            }
            $array3 = [];
            foreach ($elements_ori as $key2 => $value2) {
                if(is_numeric($key2)) {
                    $array3[$key2] = $value2["ProductCode"];
                }
            }
            $x = 0;
            $x1 = 0;
            $x2 = 0;
            $x3 = 0;
            foreach ($array2[$key] as $value3) {
                if(in_array($value3, $array3)) {
                    $x = $x + 1;
                }
            }
            foreach ($array21[$key] as $value3) {
                if(in_array($value3, $array3)) {
                    $x1 = $x1 + 1;
                }
            }
            foreach ($array22[$key] as $value3) {
                if(in_array($value3, $array3)) {
                    $x2 = $x2 + 1;
                }
            }
            foreach ($array23[$key] as $value3) {
                if(in_array($value3, $array3)) {
                    $x3 = $x3 + 1;
                }
            }
            if($x == count($array2[$key]) && (0 < $x1 || (int) $value["ProductGroup1"] === 0) && (0 < $x2 || (int) $value["ProductGroup2"] === 0) && (0 < $x3 || (int) $value["ProductGroup3"] === 0) && !in_array($key, $array)) {
                $array[] = $key;
                switch ($value["DiscountType"]) {
                    case "TotalAmount":
                    case "TotalPercentage":
                    case "PartialRestrictedPercentage":
                        $discount_counter_local = 0;
                        foreach ($array3 as $elementkey => $productcode) {
                            for ($i = 1; $i <= 3; $i++) {
                                if(0 < $value["Product" . $i] && $array2[$key][$value["Product" . $i]] == $productcode) {
                                    $discount_counter_local++;
                                    $this->updateElement($value["DiscountPart"], $elementkey, $value["DiscountPercentageType"], $VatCalcMethod);
                                } elseif(0 < $value["ProductGroup" . $i] && in_array($productcode, ${"array2" . $i}[$key])) {
                                    $discount_counter_local++;
                                    $this->updateElement($value["DiscountPart"], $elementkey, $value["DiscountPercentageType"], $VatCalcMethod);
                                }
                            }
                            if(0 < $value["MaxPerInvoice"] && $value["MaxPerInvoice"] <= $discount_counter_local) {
                            }
                        }
                        break;
                    case "PartialPercentage":
                        foreach ($array3 as $elementkey => $productcode) {
                            $i = $value["DiscountPartRestriction"];
                            $discount_counter_local = 0;
                            if(0 < $value["Product" . $i] && $array2[$key][$value["Product" . $i]] == $productcode) {
                                $this->updateElement($value["DiscountPart"], $elementkey, $value["DiscountPercentageType"], $VatCalcMethod);
                                $discount_counter_local++;
                            } elseif(0 < $value["ProductGroup" . $i] && in_array($productcode, ${"array2" . $i}[$key])) {
                                $this->updateElement($value["DiscountPart"], $elementkey, $value["DiscountPercentageType"], $VatCalcMethod);
                                $discount_counter_local++;
                            }
                            if(0 < $value["MaxPerInvoice"] && $value["MaxPerInvoice"] <= $discount_counter_local) {
                            }
                        }
                        break;
                    case "PartialAmount":
                        $discount_counter_local = 0;
                        foreach ($array3 as $elementkey => $productcode) {
                            $i = $value["DiscountPartRestriction"];
                            if(0 < $value["Product" . $i] && $array2[$key][$value["Product" . $i]] == $productcode) {
                                $product_info = Database_Model::getInstance()->getOne("HostFact_Products", ["PriceExcl", "PricePeriod"])->where("ProductCode", $productcode)->execute();
                                if(isset($product_info->PriceExcl)) {
                                    $value["Price" . $i] = $product_info->PriceExcl - $value["DiscountPart"];
                                    $value["PricePeriod" . $i] = $product_info->PricePeriod;
                                    $discount_counter_local++;
                                }
                            } elseif(0 < $value["ProductGroup" . $i] && in_array($productcode, ${"array2" . $i}[$key])) {
                                $product_info = Database_Model::getInstance()->getOne("HostFact_Products", ["PriceExcl", "PricePeriod"])->where("ProductCode", $productcode)->execute();
                                if(isset($product_info->PriceExcl)) {
                                    $key2 = array_search($productcode, ${"array2" . $i}[$key]);
                                    $array2[$key][$key2] = $productcode;
                                    $value["Product" . $i] = $key2;
                                    $value["Price" . $i] = $product_info->PriceExcl - $value["DiscountPart"];
                                    if(array_key_exists($value["Product" . $i], $array2[$key]) && is_numeric($value["Price" . $i])) {
                                        foreach ($array3 as $elementkey => $productcode) {
                                            if($array2[$key][$value["Product" . $i]] == $productcode) {
                                                $elements = $this->Type == "invoice" ? new invoiceelement() : ($this->Type == "order" ? new neworderelement() : new pricequoteelement());
                                                $elements->Identifier = $elementkey;
                                                $elements->show();
                                                $elements->PriceExcl = $this->getPriceForPeriod($elements->Periodic, $product_info->PricePeriod, $value["Price" . $i]);
                                                $elements->updatePrice($VatCalcMethod);
                                                $discount_counter_local++;
                                                if(0 < $value["MaxPerInvoice"] && $value["MaxPerInvoice"] <= $discount_counter_local) {
                                                    unset($value["Price" . $i]);
                                                }
                                            }
                                        }
                                    }
                                    unset($value["Price" . $i]);
                                }
                            }
                            if(0 < $value["MaxPerInvoice"] && $value["MaxPerInvoice"] <= $discount_counter_local) {
                            }
                        }
                        break;
                    default:
                        if(array_key_exists($value["Product1"], $array2[$key]) && is_numeric($value["Price1"])) {
                            foreach ($array3 as $elementkey => $productcode) {
                                if($array2[$key][$value["Product1"]] == $productcode) {
                                    $elements = $this->Type == "invoice" ? new invoiceelement() : ($this->Type == "order" ? new neworderelement() : new pricequoteelement());
                                    $elements->Identifier = $elementkey;
                                    $elements->show();
                                    if($this->getPriceForPeriod($elements->Periodic, $value["PricePeriod1"], $value["Price1"]) < $elements->PriceExcl) {
                                        $elements->PriceExcl = $this->getPriceForPeriod($elements->Periodic, $value["PricePeriod1"], $value["Price1"]);
                                        $elements->updatePrice($VatCalcMethod);
                                    }
                                }
                            }
                        }
                        if(array_key_exists($value["Product2"], $array2[$key]) && is_numeric($value["Price2"])) {
                            foreach ($array3 as $elementkey => $productcode) {
                                if($array2[$key][$value["Product2"]] == $productcode) {
                                    $elements = $this->Type == "invoice" ? new invoiceelement() : ($this->Type == "order" ? new neworderelement() : new pricequoteelement());
                                    $elements->Identifier = $elementkey;
                                    $elements->show();
                                    if($this->getPriceForPeriod($elements->Periodic, $value["PricePeriod2"], $value["Price2"]) < $elements->PriceExcl) {
                                        $elements->PriceExcl = $this->getPriceForPeriod($elements->Periodic, $value["PricePeriod2"], $value["Price2"]);
                                        $elements->updatePrice($VatCalcMethod);
                                    }
                                }
                            }
                        }
                        if(array_key_exists($value["Product3"], $array2[$key]) && is_numeric($value["Price3"])) {
                            foreach ($array3 as $elementkey => $productcode) {
                                if($array2[$key][$value["Product3"]] == $productcode) {
                                    $elements = $this->Type == "invoice" ? new invoiceelement() : ($this->Type == "order" ? new neworderelement() : new pricequoteelement());
                                    $elements->Identifier = $elementkey;
                                    $elements->show();
                                    if($this->getPriceForPeriod($elements->Periodic, $value["PricePeriod3"], $value["Price3"]) < $elements->PriceExcl) {
                                        $elements->PriceExcl = $this->getPriceForPeriod($elements->Periodic, $value["PricePeriod3"], $value["Price3"]);
                                        $elements->updatePrice($VatCalcMethod);
                                    }
                                }
                            }
                        }
                        Database_Model::getInstance()->update("HostFact_Discount", ["Counter" => ["RAW" => "Counter + 1"]])->where("id", $key)->execute();
                }
            }
        }
        return !empty($array) ? $array : false;
    }
    public function updateElement($discount, $key, $discount_percentage_type = "line", $VatCalcMethod = "excl")
    {
        if($this->Type == "invoice") {
            Database_Model::getInstance()->update("HostFact_InvoiceElements", ["DiscountPercentage" => $discount / 100, "DiscountPercentageType" => ["RAW" => "IF(`PeriodicID` > 0,:DiscountPercentageType,'line')"]])->where("id", $key)->where("DiscountPercentage", ["<" => $discount / 100])->bindValue("DiscountPercentageType", $discount_percentage_type)->execute();
            $line = Database_Model::getInstance()->getOne("HostFact_InvoiceElements")->where("id", $key)->execute();
            $line_amount = getLineAmount($VatCalcMethod, $line->PriceExcl, $line->Periods, $line->Number, $line->TaxPercentage, $line->DiscountPercentage);
            Database_Model::getInstance()->update("HostFact_InvoiceElements", ["LineAmountExcl" => $line_amount["excl"], "LineAmountIncl" => $line_amount["incl"]])->where("id", $key)->execute();
        } elseif($this->Type == "order") {
            Database_Model::getInstance()->update("HostFact_NewOrderElements", ["DiscountPercentage" => $discount / 100, "DiscountPercentageType" => ["RAW" => "IF(`Periodic` !='',:DiscountPercentageType,'line')"]])->where("id", $key)->where("DiscountPercentage", ["<" => $discount / 100])->bindValue("DiscountPercentageType", $discount_percentage_type)->execute();
            $line = Database_Model::getInstance()->getOne("HostFact_NewOrderElements")->where("id", $key)->execute();
            $line_amount = getLineAmount($VatCalcMethod, $line->PriceExcl, $line->Periods, $line->Number, $line->TaxPercentage, $line->DiscountPercentage);
            Database_Model::getInstance()->update("HostFact_NewOrderElements", ["LineAmountExcl" => $line_amount["excl"], "LineAmountIncl" => $line_amount["incl"]])->where("id", $key)->execute();
        } else {
            Database_Model::getInstance()->update("HostFact_PriceQuoteElements", ["DiscountPercentage" => $discount / 100, "DiscountPercentageType" => ["RAW" => "IF(`Periodic` !='',:DiscountPercentageType,'line')"]])->where("id", $key)->where("DiscountPercentage", ["<" => $discount / 100])->bindValue("DiscountPercentageType", $discount_percentage_type)->execute();
            $line = Database_Model::getInstance()->getOne("HostFact_PriceQuoteElements")->where("id", $key)->execute();
            $line_amount = getLineAmount($VatCalcMethod, $line->PriceExcl, $line->Periods, $line->Number, $line->TaxPercentage, $line->DiscountPercentage);
            Database_Model::getInstance()->update("HostFact_PriceQuoteElements", ["LineAmountExcl" => $line_amount["excl"], "LineAmountIncl" => $line_amount["incl"]])->where("id", $key)->execute();
        }
    }
    public function getPriceForPeriod($current_period, $compare_period, $compare_price)
    {
        if($current_period == $compare_period) {
            return $compare_price;
        }
        switch ($current_period) {
            case "t":
                if($compare_period == "j") {
                    $compare_price = $compare_price * 2;
                } elseif($compare_period == "h") {
                    $compare_price = $compare_price * 4;
                } elseif($compare_period == "k") {
                    $compare_price = $compare_price * 8;
                } elseif($compare_period == "m") {
                    $compare_price = $compare_price * 24;
                } elseif($compare_period == "w") {
                    $compare_price = $compare_price * 104;
                } elseif($compare_period == "d") {
                    $compare_price = $compare_price * 365 * 2;
                }
                break;
            case "j":
                if($compare_period == "t") {
                    $compare_price = $compare_price / 2;
                } elseif($compare_period == "h") {
                    $compare_price = $compare_price * 2;
                } elseif($compare_period == "k") {
                    $compare_price = $compare_price * 4;
                } elseif($compare_period == "m") {
                    $compare_price = $compare_price * 12;
                } elseif($compare_period == "w") {
                    $compare_price = $compare_price * 52;
                } elseif($compare_period == "d") {
                    $compare_price = $compare_price * 365;
                }
                break;
            case "h":
                if($compare_period == "t") {
                    $compare_price = $compare_price / 4;
                } elseif($compare_period == "j") {
                    $compare_price = $compare_price / 2;
                } elseif($compare_period == "k") {
                    $compare_price = $compare_price * 4 / 2;
                } elseif($compare_period == "m") {
                    $compare_price = $compare_price * 12 / 2;
                } elseif($compare_period == "w") {
                    $compare_price = $compare_price * 52 / 2;
                } elseif($compare_period == "d") {
                    $compare_price = $compare_price * 365 / 2;
                }
                break;
            case "k":
                if($compare_period == "t") {
                    $compare_price = $compare_price / 8;
                } elseif($compare_period == "j") {
                    $compare_price = $compare_price / 4;
                } elseif($compare_period == "h") {
                    $compare_price = $compare_price / 2;
                } elseif($compare_period == "m") {
                    $compare_price = $compare_price * 12 / 4;
                } elseif($compare_period == "w") {
                    $compare_price = $compare_price * 52 / 4;
                } elseif($compare_period == "d") {
                    $compare_price = $compare_price * 365 / 4;
                }
                break;
            case "m":
                if($compare_period == "t") {
                    $compare_price = $compare_price / 24;
                } elseif($compare_period == "j") {
                    $compare_price = $compare_price / 12;
                } elseif($compare_period == "h") {
                    $compare_price = $compare_price / 6;
                } elseif($compare_period == "k") {
                    $compare_price = $compare_price / 3;
                } elseif($compare_period == "w") {
                    $compare_price = $compare_price * 52 / 12;
                } elseif($compare_period == "d") {
                    $compare_price = $compare_price * 365 / 12;
                }
                break;
            case "w":
                if($compare_period == "t") {
                    $compare_price = $compare_price / 104;
                } elseif($compare_period == "j") {
                    $compare_price = $compare_price / 52;
                } elseif($compare_period == "h") {
                    $compare_price = $compare_price / 26;
                } elseif($compare_period == "k") {
                    $compare_price = $compare_price * 4 / 52;
                } elseif($compare_period == "m") {
                    $compare_price = $compare_price * 12 / 52;
                } elseif($compare_period == "d") {
                    $compare_price = $compare_price * 365 / 52;
                }
                break;
            case "d":
                if($compare_period == "t") {
                    $compare_price = $compare_price / 712;
                } elseif($compare_period == "j") {
                    $compare_price = $compare_price / 356;
                } elseif($compare_period == "h") {
                    $compare_price = $compare_price * 2 / 356;
                } elseif($compare_period == "k") {
                    $compare_price = $compare_price * 4 / 356;
                } elseif($compare_period == "m") {
                    $compare_price = $compare_price * 12 / 356;
                } elseif($compare_period == "w") {
                    $compare_price = $compare_price / 7;
                }
                break;
            default:
                return $compare_price;
        }
    }
    public function all($fields, $sort = false, $order = false, $limit = "-1", $searchat = false, $searchfor = false, $group = false, $show_results = MAX_RESULTS_LIST)
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
        $select = ["id"];
        foreach ($fields as $column) {
            $select[] = $column;
        }
        Database_Model::getInstance()->get("HostFact_Discount", $select)->where("Status", ["!=" => 9]);
        $search_at = [];
        if($searchat && $searchfor) {
            $search_at = explode("|", $searchat);
        }
        if(!empty($search_at)) {
            $or_clausule = [];
            foreach ($search_at as $searchColumn) {
                $or_clausule[] = [$searchColumn, ["LIKE" => "%" . $searchfor . "%"]];
            }
            Database_Model::getInstance()->orWhere($or_clausule);
        }
        $order = $sort ? $order ? $order : "ASC" : "";
        if($sort) {
            Database_Model::getInstance()->orderBy($sort, $order);
        }
        if(0 <= $limit) {
            Database_Model::getInstance()->limit((max(1, $limit) - 1) * $show_results, $show_results);
        }
        $list = [];
        $list["CountRows"] = 0;
        if($discount_list = Database_Model::getInstance()->execute()) {
            $list["CountRows"] = Database_Model::getInstance()->rowCount("HostFact_Discount", "id");
            foreach ($discount_list as $result) {
                $list[$result->id] = ["id" => $result->id];
                foreach ($fields as $column) {
                    $list[$result->id][$column] = htmlspecialchars($result->{$column});
                }
            }
        }
        return $list;
    }
}

?>