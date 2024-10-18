<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class paymentmethod
{
    public $Identifier;
    public $Title;
    public $InternalName;
    public $Directory;
    public $Availability;
    public $Image;
    public $Extra;
    public $FeeType;
    public $FeeAmount;
    public $FeeDesc;
    public $PaymentType;
    public $MerchantID;
    public $Password;
    public $Description;
    public $CountRows;
    public $Error;
    public $Success;
    public $Warning;
    public $Variables = ["Identifier", "Title", "InternalName", "Directory", "Availability", "Image", "Extra", "FeeType", "FeeAmount", "FeeDesc", "PaymentType", "MerchantID", "Password", "Description"];
    const AVAILABILITY_OFF = 0;
    const AVAILABILITY_ORDERFORM = 1;
    const AVAILABILITY_CLIENTAREA = 2;
    const AVAILABILITY_ALWAYS = 3;
    public function __construct()
    {
        $this->Error = [];
        $this->Warning = [];
        $this->Success = [];
        $this->Availability = 3;
        $this->AvailabilityArray = [__("payment availability off"), __("payment availability only orderform"), __("payment availability only customerpanel"), __("payment availability always")];
    }
    public function show()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for paymentmethod");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_PaymentMethods", ["*"])->where("id", $this->Identifier)->execute();
        if(!$result || empty($result->id)) {
            $this->Error[] = __("invalid identifier for paymentmethod");
            return false;
        }
        foreach ($result as $key => $value) {
            $this->{$key} = htmlspecialchars($value);
        }
        return true;
    }
    public function add()
    {
        if(!$this->validate()) {
            return false;
        }
        $result = Database_Model::getInstance()->insert("HostFact_PaymentMethods", ["Title" => $this->Title, "InternalName" => $this->InternalName, "Directory" => $this->Directory, "Availability" => $this->Availability, "Image" => $this->Image, "Extra" => $this->Extra, "FeeType" => $this->FeeType, "FeeAmount" => $this->FeeAmount, "FeeDesc" => $this->FeeDesc, "PaymentType" => $this->PaymentType, "MerchantID" => $this->MerchantID, "Password" => $this->Password, "Description" => $this->Description])->execute();
        if($result) {
            $this->Identifier = $result;
            $this->Success[] = __("payment method created");
            return true;
        }
        return false;
    }
    public function edit()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for paymentmethod");
            return false;
        }
        if(!$this->validate()) {
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_PaymentMethods", ["Title" => $this->Title, "InternalName" => $this->InternalName, "Directory" => $this->Directory, "Availability" => $this->Availability, "Image" => $this->Image, "Extra" => $this->Extra, "FeeType" => $this->FeeType, "FeeAmount" => $this->FeeAmount, "FeeDesc" => $this->FeeDesc, "PaymentType" => $this->PaymentType, "MerchantID" => $this->MerchantID, "Password" => $this->Password, "Description" => $this->Description])->where("id", $this->Identifier)->execute();
        if($result) {
            $this->Success[] = __("payment method adjusted");
            return true;
        }
        return false;
    }
    public function delete()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for paymentmethod");
            return false;
        }
        $result = Database_Model::getInstance()->delete("HostFact_PaymentMethods")->where("id", $this->Identifier)->execute();
        if($result) {
            $this->Success[] = __("payment method removed");
            return true;
        }
        return false;
    }
    public function reorder($order)
    {
        $order = explode("&", $order);
        $ordering = 1;
        foreach ($order as $item) {
            $exp_item = explode("=", $item);
            Database_Model::getInstance()->update("HostFact_PaymentMethods", ["Ordering" => $ordering])->where("id", $exp_item[1])->execute();
            $ordering++;
        }
        return true;
    }
    public function validate()
    {
        if(!trim($this->Title)) {
            $this->Error[] = __("payment method title missing");
        }
        if($this->PaymentType == "auth") {
            $this->Directory = "payment.auth";
        } elseif($this->PaymentType == "wire") {
            $this->Directory = "";
        } elseif($this->Directory == "") {
            $this->Error[] = __("payment method directory must not be empty");
        }
        if(empty($this->Error)) {
            return true;
        }
        return false;
    }
    public function get_types($availability = [])
    {
        $result = Database_Model::getInstance()->get("HostFact_PaymentMethods", ["*"])->where("Availability", ["IN" => $availability])->orderBy("Ordering", "ASC")->execute();
        $payment_types = [];
        if($result && is_array($result)) {
            foreach ($result as $var) {
                $payment_types[] = $var->PaymentType;
            }
        }
        return $payment_types;
    }
    public function all()
    {
        $result = Database_Model::getInstance()->get("HostFact_PaymentMethods", ["*"])->orderBy("Ordering", "ASC")->execute();
        $payment_methods = [];
        if($result && is_array($result)) {
            foreach ($result as $var) {
                $key = $var->PaymentType . "" . $var->id;
                $payment_methods[$key]["id"] = $var->id;
                $payment_methods[$key]["TITLE"] = $var->Title;
                $payment_methods[$key]["DIRECTORY"] = $var->Directory;
                $payment_methods[$key]["AVAILABLE"] = $var->Availability;
                $payment_methods[$key]["IMAGE"] = $var->Image;
                $payment_methods[$key]["IMAGEALT"] = "";
                $payment_methods[$key]["EXTRA"] = $var->Extra;
                $payment_methods[$key]["FEETYPE"] = $var->FeeType;
                $payment_methods[$key]["FEEAMOUNT"] = $var->FeeAmount;
                $payment_methods[$key]["FEEDESC"] = $var->FeeDesc;
            }
        }
        return $payment_methods;
    }
}

?>