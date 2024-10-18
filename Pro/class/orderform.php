<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class orderform
{
    public $Identifier;
    public $Title;
    public $Type;
    public $Language;
    public $Available;
    public $ProductGroups;
    public $ShowPrices;
    public $ShowDiscountCoupon;
    public $VatCalcMethod;
    public $PeriodChoice;
    public $PeriodDefaultPeriods;
    public $PeriodDefaultPeriodic;
    public $PeriodChoiceOptions;
    public $OtherSettings;
    public $CountRows;
    public $Error;
    public $Success;
    public $Warning;
    public $Variables = ["Identifier", "Title", "Type", "Language", "Available", "ProductGroups", "ShowPrices", "ShowDiscountCoupon", "VatCalcMethod", "PeriodChoice", "PeriodDefaultPeriods", "PeriodDefaultPeriodic", "OtherSettings"];
    public function __construct()
    {
        $this->Type = "other";
        $this->Language = LANGUAGE;
        $this->Available = "yes";
        $this->ProductGroups = [];
        $this->ShowPrices = "yes";
        $this->ShowDiscountCoupon = "yes";
        $this->VatCalcMethod = "default";
        $this->PeriodChoice = "no";
        $this->PeriodDefaultPeriods = 1;
        $this->PeriodDefaultPeriodic = "j";
        $this->PeriodChoiceOptions = [];
        $this->OtherSettings = [];
        $this->Error = [];
        $this->Warning = [];
        $this->Success = [];
    }
    public function show()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for orderform");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_OrderForms", ["*"])->where("id", $this->Identifier)->execute();
        if(!$result || empty($result->id)) {
            $this->Error[] = __("invalid identifier for orderform");
            return false;
        }
        foreach ($result as $key => $value) {
            $this->{$key} = $value;
        }
        $this->ProductGroups = json_decode($this->ProductGroups);
        $this->OtherSettings = json_decode($this->OtherSettings);
        $this->PeriodChoiceOptions = json_decode($this->PeriodChoiceOptions);
        return true;
    }
    public function add()
    {
        if(!$this->validate()) {
            return false;
        }
        $result = Database_Model::getInstance()->insert("HostFact_OrderForms", ["Title" => $this->Title, "Type" => $this->Type, "Language" => $this->Language, "Available" => $this->Available, "ProductGroups" => json_encode($this->ProductGroups), "ShowPrices" => $this->ShowPrices, "ShowDiscountCoupon" => $this->ShowDiscountCoupon, "VatCalcMethod" => $this->VatCalcMethod, "PeriodChoice" => $this->PeriodChoice, "PeriodDefaultPeriods" => $this->PeriodDefaultPeriods, "PeriodDefaultPeriodic" => $this->PeriodDefaultPeriodic, "PeriodChoiceOptions" => json_encode($this->PeriodChoiceOptions), "OtherSettings" => json_encode($this->OtherSettings)])->execute();
        if($result) {
            $this->Identifier = $result;
            $this->Success[] = sprintf(__("orderform created"), $this->Title);
            return true;
        }
        return false;
    }
    public function edit()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for orderform");
            return false;
        }
        if(!$this->validate()) {
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_OrderForms", ["Title" => $this->Title, "Type" => $this->Type, "Language" => $this->Language, "Available" => $this->Available, "ProductGroups" => json_encode($this->ProductGroups), "ShowPrices" => $this->ShowPrices, "ShowDiscountCoupon" => $this->ShowDiscountCoupon, "VatCalcMethod" => $this->VatCalcMethod, "PeriodChoice" => $this->PeriodChoice, "PeriodDefaultPeriods" => $this->PeriodDefaultPeriods, "PeriodDefaultPeriodic" => $this->PeriodDefaultPeriodic, "PeriodChoiceOptions" => json_encode($this->PeriodChoiceOptions), "OtherSettings" => json_encode($this->OtherSettings)])->where("id", $this->Identifier)->execute();
        if($result) {
            $this->Success[] = sprintf(__("orderform adjusted"), $this->Title);
            return true;
        }
        return false;
    }
    public function delete()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for orderform");
            return false;
        }
        if(DEFAULT_ORDERFORM == $this->Identifier) {
            $this->Error[] = __("cannot delete default orderform");
            return false;
        }
        $clientarea_profiles = Database_Model::getInstance()->get("HostFact_Clientarea_Profiles")->where("Orderforms", ["LIKE" => "%\"" . $this->Identifier . "\"%"])->execute();
        if($clientarea_profiles) {
            foreach ($clientarea_profiles as $_profile) {
                $_update_profile = false;
                $_orderforms = json_decode($_profile->Orderforms, true);
                foreach ($_orderforms as $_orderform_key => $_orderform_id) {
                    if($_orderform_id == $this->Identifier) {
                        $_orderforms[$_orderform_key] = "";
                        $_update_profile = true;
                    }
                }
                if($_update_profile) {
                    Database_Model::getInstance()->update("HostFact_Clientarea_Profiles", ["Orderforms" => json_encode($_orderforms)])->where("id", $_profile->id)->execute();
                }
            }
        }
        $result = Database_Model::getInstance()->delete("HostFact_OrderForms")->where("id", $this->Identifier)->execute();
        if($result) {
            $this->Success[] = sprintf(__("orderform removed"), $this->Title);
            return true;
        }
        return false;
    }
    public function validate()
    {
        if(!trim($this->Title)) {
            $this->Error[] = __("orderform title missing");
        }
        if($this->PeriodChoice == "default" || $this->PeriodChoice == "yes") {
            if(intval($this->PeriodDefaultPeriods) != $this->PeriodDefaultPeriods || $this->PeriodDefaultPeriods <= 0) {
                $this->Error[] = __("orderform default periods invalid");
            }
            if($this->PeriodChoice == "yes") {
                $has_default = false;
                foreach ($this->PeriodChoiceOptions as $tmp) {
                    if(intval($tmp["Periods"]) != $tmp["Periods"] || $tmp["Periods"] <= 0) {
                        $this->Error[] = __("orderform periods invalid");
                    }
                    if($tmp["Periods"] == $this->PeriodDefaultPeriods && $tmp["Periodic"] == $this->PeriodDefaultPeriodic) {
                        $has_default = true;
                    }
                }
                if(!$has_default) {
                    $this->PeriodChoiceOptions = array_merge([["Periods" => $this->PeriodDefaultPeriods, "Periodic" => $this->PeriodDefaultPeriodic]], $this->PeriodChoiceOptions);
                }
            }
        }
        if($this->Type == "other" && !$this->ProductGroups["main"]) {
            $this->Error[] = __("no productgroup selected for other products");
        }
        if($this->Type == "domain" || isset($this->OtherSettings["domain"]["Available"]) && $this->OtherSettings["domain"]["Available"] == "yes") {
            if(!$this->ProductGroups["domain"]) {
                $this->Error[] = __("no productgroup selected for domain products");
            }
            if(isset($this->OtherSettings["domain"]["ShowTLDs"]) && $this->OtherSettings["domain"]["ShowTLDs"] == "popular" && empty($this->OtherSettings["domain"]["Popular"])) {
                $this->Error[] = __("popular tlds for domain check, but no popular domains specified");
            }
        }
        if($this->Type == "hosting" && !$this->ProductGroups["hosting"]) {
            $this->Error[] = __("no productgroup selected for hosting products");
        }
        if($this->Type == "custom" && (!isset($this->OtherSettings["custom"]["ControllerName"]) || !$this->OtherSettings["custom"]["ControllerName"])) {
            $this->Error[] = __("no controller name for custom orderform");
        }
        if(0 < $this->Identifier && DEFAULT_ORDERFORM == $this->Identifier && $this->Available == "no") {
            $this->Error[] = __("cannot make default orderform inactive");
            $this->Available = "yes";
        }
        if(empty($this->Error)) {
            return true;
        }
        return false;
    }
    public function all()
    {
        $result = Database_Model::getInstance()->get("HostFact_OrderForms", ["id", "Title", "Type", "Available"])->orderBy("Available", "DESC")->orderBy("Title", "ASC")->asArray()->execute();
        $orderforms = ["Available" => [], "Unavailable" => []];
        if($result && is_array($result)) {
            foreach ($result as $var) {
                $tmp_orderform = [];
                foreach ($var as $k => $v) {
                    $tmp_orderform[$k] = htmlspecialchars($v);
                }
                $orderforms[$var["Available"] == "yes" ? "Available" : "Unavailable"][] = $tmp_orderform;
            }
        }
        return $orderforms;
    }
    public function getLanguages()
    {
        $json_languages = getContent(ORDERFORM_URL . "?list_hostfact=languages");
        $language_list = json_decode($json_languages);
        $language_array = (array) $language_list;
        return $language_array;
    }
}

?>