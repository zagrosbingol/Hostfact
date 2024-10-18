<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class creditinvoice
{
    public $Identifier;
    public $CreditInvoiceCode;
    public $InvoiceCode;
    public $Creditor;
    public $Date;
    public $Term;
    public $Authorisation;
    public $Private;
    public $PrivatePercentage;
    public $ReferenceNumber;
    public $PayDate;
    public $Status;
    public $AmountExcl;
    public $AmountIncl;
    public $AmountTax;
    public $AmountPaid;
    public $Location;
    public $Free1;
    public $Free2;
    public $Free3;
    public $Free4;
    public $Free5;
    public $Elements;
    public $ExtraElement;
    public $CountRows;
    public $Error;
    public $Success;
    public $Variables = ["Identifier", "CreditInvoiceCode", "InvoiceCode", "Creditor", "Date", "Term", "Authorisation", "PayDate", "Status", "Location", "Private", "PrivatePercentage", "ReferenceNumber", "AmountPaid"];
    public function __construct()
    {
        $this->Date = rewrite_date_db2site(date("Y-m-d H:i:s"));
        $this->Status = "1";
        $this->Term = "0";
        $this->Private = 0;
        $this->Elements = [];
        $this->ExtraElement = false;
        $this->Error = [];
        $this->Success = [];
        $this->Warning = [];
        $this->AmountExcl = money(0, false);
        $this->AmountIncl = money(0, false);
        $this->AmountPaid = 0;
        $this->Location = "";
        $this->ReferenceNumber = "";
        $this->Authorisation = "no";
    }
    public function show()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for creditinvoice");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_CreditInvoice", ["*", "DATE_ADD(`Date`,INTERVAL `Term` DAY) as `PayBefore`"])->where("id", $this->Identifier)->execute();
        if(!$result || empty($result->id)) {
            $this->Error[] = __("invalid identifier for creditinvoice");
            return false;
        }
        foreach ($result as $key => $value) {
            if($key != "id") {
                $this->{$key} = htmlspecialchars($value);
            }
        }
        $this->PartPayment = $this->AmountIncl - $this->AmountPaid;
        $elements = new creditinvoiceelement();
        $this->Elements = $elements->all($this->CreditInvoiceCode);
        $this->Date = rewrite_date_db2site($this->Date);
        $this->PayDate = rewrite_date_db2site($this->PayDate);
        $this->PrivatePercentage = $this->PrivatePercentage * 100;
        return true;
    }
    public function format()
    {
        $this->CompanyExcl = $this->AmountExcl * (1 - $this->PrivatePercentage / 100) - $this->Private;
        $this->CompanyIncl = $this->AmountExcl != 0 ? $this->AmountIncl / $this->AmountExcl * ($this->AmountExcl * (1 - $this->PrivatePercentage / 100) - $this->Private) : 0;
        $this->PrivateIncl = $this->AmountIncl / 100 * $this->PrivatePercentage + $this->Private;
        $this->AmountTax = $this->AmountIncl - $this->AmountExcl;
        $this->CompanyExcl = money($this->CompanyExcl);
        $this->CompanyIncl = money($this->CompanyIncl);
        $this->PrivateIncl = money($this->PrivateIncl);
        $this->AmountTax = money($this->AmountTax);
        $this->AmountExcl = money($this->AmountExcl);
        $this->AmountIncl = money($this->AmountIncl);
    }
    public function download()
    {
        $filename = DIR_CREDIT_INVOICES . $this->Location;
        if(file_exists($filename)) {
            $f = fopen($filename, "r");
            $buffer = fread($f, filesize($filename));
            fclose($f);
            $filename = basename($filename);
            header("Content-type: application/octet-stream");
            header("Content-Disposition: download; filename=\"" . $filename . "\"");
            header("Content-Transfer-Encoding: binary");
            echo $buffer;
            exit;
        }
        $this->Error[] = sprintf(__("cannot find credit invoice file"), $this->Location);
    }
    public function getID($method, $value)
    {
        switch ($method) {
            case "identifier":
                $creditinvoice_id = Database_Model::getInstance()->getOne("HostFact_CreditInvoice", ["id"])->where("id", intval($value))->execute();
                return $creditinvoice_id !== false ? $creditinvoice_id->id : false;
                break;
            case "creditinvoicecode":
                $creditinvoice_id = Database_Model::getInstance()->getOne("HostFact_CreditInvoice", ["id"])->where("CreditInvoiceCode", $value)->execute();
                return $creditinvoice_id !== false ? $creditinvoice_id->id : false;
                break;
        }
    }
    public function changeCreditInvoiceCode($Identifier, $newCreditInvoiceCode)
    {
        $invoiceData = Database_Model::getInstance()->getOne("HostFact_CreditInvoice", ["CreditInvoiceCode", "Status"])->where("id", $Identifier)->execute();
        if($invoiceData === false) {
            $this->Error[] = __("invalid identifier for credit invoice");
            return false;
        }
        if($invoiceData->CreditInvoiceCode == $newCreditInvoiceCode) {
            return true;
        }
        $result = Database_Model::getInstance()->update("HostFact_CreditInvoiceElements", ["CreditInvoiceCode" => $newCreditInvoiceCode])->where("CreditInvoiceCode", $invoiceData->CreditInvoiceCode)->execute();
        if($result === true) {
            return true;
        }
        return false;
    }
    public function add()
    {
        $elements = new creditinvoiceelement();
        $this->Elements = $elements->all($this->CreditInvoiceCode);
        $this->AmountIncl = deformat_money($this->AmountIncl);
        $this->Private = deformat_money($this->Private);
        $this->PrivatePercentage = deformat_money($this->PrivatePercentage) / 100;
        $this->Date = rewrite_date_site2db($this->Date);
        $this->PayDate = rewrite_date_site2db($this->PayDate);
        $this->AmountExcl = isset($this->Elements["AmountExcl"]) ? $this->Elements["AmountExcl"] : 0;
        if(isEmptyFloat($this->AmountIncl)) {
            $this->AmountIncl = isset($this->Elements["AmountIncl"]) ? $this->Elements["AmountIncl"] : 0;
        }
        $this->AmountPaid = $this->Status == 2 ? deformat_money($this->AmountPaid) : 0;
        if(0 < $this->AmountIncl) {
            $this->AmountIncl = number_format($this->AmountIncl + 0, 2, ".", "");
        } elseif($this->AmountIncl < 0) {
            $this->AmountIncl = number_format($this->AmountIncl - 0, 2, ".", "");
        }
        if($this->validate() === false) {
            $elements = new creditinvoiceelement();
            $this->Elements = $elements->all($this->CreditInvoiceCode);
            $this->Date = rewrite_date_db2site($this->Date);
            $this->PayDate = rewrite_date_db2site($this->PayDate);
            $this->PrivatePercentage = $this->PrivatePercentage * 100;
            return false;
        }
        if($this->Status == 2 && $this->AmountPaid == $this->AmountIncl) {
            $this->Status = 3;
            $this->PayDate = date("Ymd");
        }
        $result = Database_Model::getInstance()->insert("HostFact_CreditInvoice", ["CreditInvoiceCode" => $this->CreditInvoiceCode, "InvoiceCode" => $this->InvoiceCode, "Creditor" => $this->Creditor, "Date" => $this->Date, "Term" => $this->Term, "Private" => $this->Private, "PrivatePercentage" => $this->PrivatePercentage, "PayDate" => $this->PayDate, "Status" => $this->Status, "AmountExcl" => $this->AmountExcl, "AmountIncl" => $this->AmountIncl, "AmountPaid" => $this->AmountPaid, "Location" => $this->Location, "Authorisation" => $this->Authorisation, "ReferenceNumber" => $this->ReferenceNumber])->execute();
        if($result) {
            $this->Identifier = $result;
            createLog("creditinvoice", $this->Identifier, "purchase invoice created");
            $this->Success[] = sprintf(__("creditinvoice succesfully added"), $this->CreditInvoiceCode);
            return true;
        }
        $elements = new creditinvoiceelement();
        $this->Elements = $elements->all($this->CreditInvoiceCode);
        $this->Date = rewrite_date_db2site($this->Date);
        $this->PayDate = rewrite_date_db2site($this->PayDate);
        $this->PrivatePercentage = $this->PrivatePercentage * 100;
        return false;
    }
    public function edit()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for creditinvoice");
            return false;
        }
        $elements = new creditinvoiceelement();
        $this->Elements = $elements->all($this->CreditInvoiceCode);
        $this->AmountIncl = deformat_money($this->AmountIncl);
        $this->Private = deformat_money($this->Private);
        $this->PrivatePercentage = deformat_money($this->PrivatePercentage) / 100;
        $this->Date = rewrite_date_site2db($this->Date);
        $this->PayDate = rewrite_date_site2db($this->PayDate);
        $this->AmountPaid = $this->Status == 2 ? deformat_money($this->AmountPaid) : 0;
        $this->AmountExcl = isset($this->Elements["AmountExcl"]) ? $this->Elements["AmountExcl"] : 0;
        if(isEmptyFloat($this->AmountIncl)) {
            $this->AmountIncl = isset($this->Elements["AmountIncl"]) ? $this->Elements["AmountIncl"] : 0;
        }
        if(0 < $this->AmountIncl) {
            $this->AmountIncl = number_format($this->AmountIncl + 0, 2, ".", "");
        } elseif($this->AmountIncl < 0) {
            $this->AmountIncl = number_format($this->AmountIncl - 0, 2, ".", "");
        }
        if($this->validate() === false) {
            $elements = new creditinvoiceelement();
            $this->Elements = $elements->all($this->CreditInvoiceCode);
            $this->Date = rewrite_date_db2site($this->Date);
            $this->PayDate = rewrite_date_db2site($this->PayDate);
            $this->PrivatePercentage = $this->PrivatePercentage * 100;
            return false;
        }
        if($this->Status == 2 && $this->AmountPaid == $this->AmountIncl) {
            $this->Status = 3;
            $this->PayDate = date("Ymd");
        }
        $result = Database_Model::getInstance()->update("HostFact_CreditInvoice", ["CreditInvoiceCode" => $this->CreditInvoiceCode, "InvoiceCode" => $this->InvoiceCode, "Creditor" => $this->Creditor, "Date" => $this->Date, "Term" => $this->Term, "Private" => $this->Private, "PrivatePercentage" => $this->PrivatePercentage, "PayDate" => $this->PayDate, "Status" => $this->Status, "AmountExcl" => $this->AmountExcl, "AmountIncl" => $this->AmountIncl, "AmountPaid" => $this->AmountPaid, "Location" => $this->Location, "Authorisation" => $this->Authorisation, "ReferenceNumber" => $this->ReferenceNumber])->where("id", $this->Identifier)->execute();
        if($result) {
            createLog("creditinvoice", $this->Identifier, "purchase invoice adjusted");
            $this->Success[] = sprintf(__("creditinvoice succesfully edited"), $this->CreditInvoiceCode);
            return true;
        }
        $elements = new creditinvoiceelement();
        $this->Elements = $elements->all($this->CreditInvoiceCode);
        $this->Date = rewrite_date_db2site($this->Date);
        $this->PayDate = rewrite_date_db2site($this->PayDate);
        $this->PrivatePercentage = $this->PrivatePercentage * 100;
        return false;
    }
    public function delete()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for creditinvoice");
            return false;
        }
        $result = Database_Model::getInstance()->delete("HostFact_CreditInvoiceElements")->where("CreditInvoiceCode", ["IN" => ["RAW" => "SELECT `CreditInvoiceCode` FROM `HostFact_CreditInvoice` WHERE `id`=:InvoiceID"]])->bindValue("InvoiceID", $this->Identifier)->execute();
        $result = Database_Model::getInstance()->delete("HostFact_CreditInvoice")->where("id", $this->Identifier)->execute();
        Database_Model::getInstance()->delete("HostFact_ExportHistory")->where("Type", ["IN" => ["creditinvoice", "payment_purchase"]])->where("ReferenceID", $this->Identifier)->execute();
        if($result) {
            if($this->Location) {
                @unlink(DIR_CREDIT_INVOICES . "/" . $this->Location);
            }
            require_once "attachment.php";
            $attachments = new attachment();
            $attachments->deleteAllAttachments($this->Identifier, "creditinvoice");
            $this->Success[] = sprintf(__("creditinvoice succesfully deleted"), $this->CreditInvoiceCode);
            return true;
        }
        return false;
    }
    public function newCreditInvoiceCode($prefix = CREDITINVOICECODE_PREFIX, $number = CREDITINVOICECODE_NUMBER)
    {
        $prefix = parsePrefixVariables($prefix);
        $length = strlen($prefix . $number);
        $result = Database_Model::getInstance()->getOne("HostFact_CreditInvoice", ["CreditInvoiceCode"])->where("CreditInvoiceCode", ["LIKE" => $prefix . "%"])->where("LENGTH(`CreditInvoiceCode`)", [">=" => $length])->where("(SUBSTR(`CreditInvoiceCode`,:PrefixLength)*1)", [">" => 0])->where("Status", ["!=" => 9])->where("SUBSTR(`CreditInvoiceCode`,:PrefixLength)", ["REGEXP" => "^[0-9]+\$"])->orderBy("(SUBSTR(`CreditInvoiceCode`,:PrefixLength)*1)", "DESC")->bindValue("PrefixLength", strlen($prefix) + 1)->execute();
        if(isset($result->CreditInvoiceCode) && $result->CreditInvoiceCode && is_numeric(substr($result->CreditInvoiceCode, strlen($prefix)))) {
            $CreditInvoiceCode = substr($result->CreditInvoiceCode, strlen($prefix));
            $CreditInvoiceCode = $prefix . @str_repeat("0", @max(@strlen($number) - @strlen(@max($CreditInvoiceCode + 1, $number)), 0)) . max($CreditInvoiceCode + 1, $number);
        } else {
            $CreditInvoiceCode = $prefix . $number;
        }
        if(!$this->is_free($CreditInvoiceCode)) {
            $this->Error[] = __("invalid identifier");
        }
        return !empty($this->Error) ? false : $CreditInvoiceCode;
    }
    public function validate()
    {
        if(100 < strlen($this->InvoiceCode)) {
            $this->Error[] = __("external invoicecode too long");
            return false;
        }
        if(!$this->is_freeExt($this->InvoiceCode)) {
            $this->Warning[] = sprintf(__("external invoicecode in use"), $this->InvoiceCode);
        }
        if(!is_numeric($this->Creditor)) {
            $this->Error[] = __("invalid creditor");
        }
        if(!$this->is_free($this->CreditInvoiceCode)) {
            $this->Error[] = __("invalid creditinvoicecode");
        }
        global $array_creditinvoicestatus;
        if(!is_numeric($this->Status) || !array_key_exists($this->Status, $array_creditinvoicestatus)) {
            $this->Error[] = __("invalid status creditinvoice");
        }
        if($this->Status != 3) {
            $this->PayDate = "0000-00-00";
        } elseif(strlen($this->PayDate) && !is_date(rewrite_date_site2db($this->PayDate))) {
            $this->Error[] = __("invalid paydate");
        }
        if(!is_numeric($this->AmountPaid)) {
            $this->Error[] = __("invalid amount paid");
        }
        if(!is_date(rewrite_date_site2db($this->Date))) {
            $this->Error[] = __("invalid creditinvoice date");
        }
        if(!isset($this->Elements["CountRows"]) || (int) $this->Elements["CountRows"] === 0) {
            unset($_POST["inv"]);
            if(0 < $this->Identifier) {
                $this->Warning[] = __("no creditinvoicelements");
                $this->AmountExcl = $this->AmountIncl = 0;
            } else {
                $this->Error[] = __("no creditinvoicelements");
                return empty($this->Error);
            }
        }
        if(!is_numeric($this->AmountExcl)) {
            $this->Error[] = __("invalid btw excl");
        }
        if(!is_numeric($this->AmountIncl)) {
            $this->Error[] = __("invalid btw incl");
        }
        if($this->Private && !is_numeric($this->Private)) {
            $this->Error[] = __("invalid private amount");
        }
        if(!is_numeric($this->PrivatePercentage) || $this->PrivatePercentage < 0 || 1 < $this->PrivatePercentage) {
            $this->Error[] = __("invalid privatepercentage");
        }
        if(!(is_string($this->ReferenceNumber) && strlen($this->ReferenceNumber) <= 255 || strlen($this->ReferenceNumber) === 0)) {
            $this->Error[] = __("invalid referencenumber");
        }
        if(!in_array($this->Authorisation, ["yes", "no"])) {
            $this->Error[] = __("invalid creditor authorisation");
        }
        if(!(is_numeric($this->Term) && 0 <= $this->Term)) {
            if($this->Authorisation == "yes") {
                $this->Term = 0;
            } else {
                $this->Error[] = __("invalid creditinvoice term");
            }
        }
        return empty($this->Error);
    }
    public function is_free($CreditInvoiceCode)
    {
        if($CreditInvoiceCode) {
            $result = Database_Model::getInstance()->getOne("HostFact_CreditInvoice", ["id"])->where("CreditInvoiceCode", $CreditInvoiceCode)->where("Status", ["!=" => 9])->execute();
            if(!$result || $result->id == $this->Identifier) {
                return true;
            }
            return false;
        }
        return false;
    }
    public function is_freeExt($InvoiceCode)
    {
        if($InvoiceCode) {
            $result = Database_Model::getInstance()->getOne("HostFact_CreditInvoice", ["id"])->where("InvoiceCode", $InvoiceCode)->where("Status", ["!=" => 9])->execute();
            if(!$result || $result->id == $this->Identifier) {
                return true;
            }
            return false;
        }
        return true;
    }
    public function markaspaid($date = "")
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for creditinvoice");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_CreditInvoice", ["Status" => 3, "AmountPaid" => 0, "PayDate" => $date && is_date($date) ? $date : ["RAW" => "CURDATE()"]])->where("id", $this->Identifier)->execute();
        if($result) {
            createLog("creditinvoice", $this->Identifier, "purchase invoice paid");
            $this->Success[] = __("invoice paid", ["invoicecode" => $this->CreditInvoiceCode]);
            return true;
        }
        return false;
    }
    public function updateOpenAmountViaPackage($new_open_amount, $package)
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for creditinvoice");
            return false;
        }
        if(2 < $this->Status) {
            $this->Error[] = __("invoice already paid", ["invoicecode" => $this->CreditInvoiceCode]);
            return false;
        }
        $data = ["Status" => 2, "AmountPaid" => round($this->AmountIncl - $new_open_amount, 2), "PayDate" => ""];
        if(isEmptyFloat($new_open_amount)) {
            $data["Status"] = 3;
            $data["PayDate"] = date("Y-m-d");
        } elseif($new_open_amount == $this->AmountIncl) {
            $data["Status"] = 1;
        }
        $result = Database_Model::getInstance()->update("HostFact_CreditInvoice", $data)->where("id", $this->Identifier)->execute();
        if(!$result) {
            $this->Error[] = __("invoice not paid partly", ["invoicecode" => $this->CreditInvoiceCode]);
            return false;
        }
        if($data["Status"] == 3) {
            createLog("creditinvoice", $this->Identifier, "invoice paid via package", [$package]);
            $this->Success[] = __("invoice paid", ["invoicecode" => $this->CreditInvoiceCode]);
        } else {
            createLog("creditinvoice", $this->Identifier, "invoice paid partly via package", [money($this->AmountIncl - $this->AmountPaid), money($new_open_amount), $package]);
            $this->Success[] = __("invoice paid partly via package", ["openamount_old" => money($this->AmountIncl - $this->AmountPaid), "openamount_new" => money($new_open_amount), "invoicecode" => $this->CreditInvoiceCode]);
        }
        return true;
    }
    public function partpayment($amountpaid, $date = "")
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for creditinvoice");
            return false;
        }
        $amountpaid = deformat_money($amountpaid);
        $this->Status = 2;
        $this->AmountPaid = $this->AmountPaid + $amountpaid;
        $this->PartPayment = round((double) $this->PartPayment, 2) - $amountpaid;
        $this->PayDate = "";
        if(isEmptyFloat($this->PartPayment)) {
            $this->Status = 3;
            $this->PayDate = $date && is_date($date) ? $date : date("Ymd");
        } elseif($this->PartPayment < 0) {
            $this->Warning[] = sprintf(__("partpayment higher"), $this->CreditInvoiceCode);
        }
        if(isEmptyFloat($this->AmountPaid)) {
            $this->Status = 1;
        }
        $result = Database_Model::getInstance()->update("HostFact_CreditInvoice", ["Status" => $this->Status, "AmountPaid" => $this->AmountPaid, "PayDate" => $this->PayDate])->where("id", $this->Identifier)->execute();
        if($result) {
            createLog("creditinvoice", $this->Identifier, "purchase invoice part payment done", [money($amountpaid)]);
            $this->Success[] = sprintf(__("invoice paid partly"), $this->CreditInvoiceCode);
            return true;
        }
        return false;
    }
    public function markasnotpaid()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for creditinvoice");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_CreditInvoice", ["Status" => 1, "PayDate" => "0000-00-00"])->where("id", $this->Identifier)->execute();
        if($result) {
            createLog("creditinvoice", $this->Identifier, "purchase invoice payment undone");
            $this->Success[] = sprintf(__("creditinvoice not paid"), $this->CreditInvoiceCode);
            return true;
        }
        return false;
    }
    public function receivedInvoice()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for creditinvoice");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_CreditInvoice", ["Status" => 1])->where("id", $this->Identifier)->execute();
        if($result) {
            $this->Success[] = sprintf(__("creditinvoice received"), $this->CreditInvoiceCode);
            return true;
        }
        return false;
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
        if($group !== false && is_array($group) && 0 < count($group)) {
            $filters = $group;
            if(array_key_exists("status", $group)) {
                $group = $group["status"];
                unset($filters["status"]);
            } else {
                $group = false;
            }
        }
        $CreditorArray = ["CreditorCode", "CompanyName", "SurName", "Initials"];
        $CreditorFields = 0 < count(array_intersect($CreditorArray, $fields)) ? true : false;
        $ElementArray = ["Description"];
        $ElementFields = 0 < count(array_intersect($ElementArray, $fields)) ? true : false;
        $search_at = [];
        $CreditorSearch = $ElementSearch = false;
        if($searchat && $searchfor) {
            $search_at = explode("|", $searchat);
            $CreditorSearch = 0 < count(array_intersect($CreditorArray, $search_at)) ? true : false;
            $ElementSearch = 0 < count(array_intersect($ElementArray, $search_at)) ? true : false;
        }
        $select = ["HostFact_CreditInvoice.id", "DATE_ADD(HostFact_CreditInvoice.`Date`,INTERVAL HostFact_CreditInvoice.`Term` DAY) as `PayBefore`"];
        foreach ($fields as $column) {
            if(in_array($column, $CreditorArray)) {
                $select[] = "HostFact_Creditors.`" . $column . "`";
            } elseif(in_array($column, $ElementArray)) {
                $select[] = "HostFact_CreditInvoiceElements.`" . $column . "`";
            } else {
                $select[] = "HostFact_CreditInvoice.`" . $column . "`";
            }
        }
        Database_Model::getInstance()->get("HostFact_CreditInvoice", $select);
        if($CreditorFields || $CreditorSearch) {
            Database_Model::getInstance()->join("HostFact_Creditors", "HostFact_Creditors.`id`=HostFact_CreditInvoice.`Creditor`");
        }
        if($ElementFields || $ElementSearch) {
            Database_Model::getInstance()->join("HostFact_CreditInvoiceElements", "HostFact_CreditInvoice.`CreditInvoiceCode`=HostFact_CreditInvoiceElements.`CreditInvoiceCode`");
        }
        if(!empty($search_at)) {
            $or_clausule = [];
            foreach ($search_at as $searchColumn) {
                if(in_array($searchColumn, ["Creditor"])) {
                    $or_clausule[] = ["HostFact_CreditInvoice.`" . $searchColumn . "`", $searchfor];
                } elseif(in_array($searchColumn, $CreditorArray)) {
                    $or_clausule[] = ["HostFact_Creditors.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                } elseif(in_array($searchColumn, $ElementArray)) {
                    $or_clausule[] = ["HostFact_CreditInvoiceElements.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                } else {
                    $or_clausule[] = ["HostFact_CreditInvoice.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                }
            }
            Database_Model::getInstance()->orWhere($or_clausule);
        }
        $order = $sort ? $order ? $order : "DESC" : "";
        if($sort == "Creditor") {
            Database_Model::getInstance()->orderBy("CONCAT(HostFact_Creditors.`CompanyName`, HostFact_Creditors.`SurName`)", $order);
        } elseif(in_array($sort, $CreditorArray)) {
            Database_Model::getInstance()->orderBy("HostFact_Creditors.`" . $sort . "`", $order);
        } elseif($sort == "PayBefore") {
            Database_Model::getInstance()->orderBy("PayBefore", $order);
        } elseif($sort == "CreditInvoiceCode") {
            $order = $order ? $order : "DESC";
            Database_Model::getInstance()->orderBy("IF(SUBSTRING(HostFact_CreditInvoice.`CreditInvoiceCode`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(HostFact_CreditInvoice.`CreditInvoiceCode`,1,1))", $order)->orderBy("LENGTH(HostFact_CreditInvoice.`CreditInvoiceCode`)", $order)->orderBy("HostFact_CreditInvoice.`CreditInvoiceCode`", $order);
        } elseif($sort == "Date` ASC, `CreditInvoiceCode") {
            Database_Model::getInstance()->orderBy("HostFact_CreditInvoice.`Date`", "ASC")->orderBy("IF(SUBSTRING(HostFact_CreditInvoice.`CreditInvoiceCode`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(HostFact_CreditInvoice.`CreditInvoiceCode`,1,1))", "ASC")->orderBy("LENGTH(HostFact_CreditInvoice.`CreditInvoiceCode`)", "ASC")->orderBy("HostFact_CreditInvoice.`CreditInvoiceCode`", "ASC");
        } elseif($sort == "Date` DESC, `CreditInvoiceCode") {
            Database_Model::getInstance()->orderBy("HostFact_CreditInvoice.`Date`", "DESC")->orderBy("IF(SUBSTRING(HostFact_CreditInvoice.`CreditInvoiceCode`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(HostFact_CreditInvoice.`CreditInvoiceCode`,1,1))", "DESC")->orderBy("LENGTH(HostFact_CreditInvoice.`CreditInvoiceCode`)", "DESC")->orderBy("HostFact_CreditInvoice.`CreditInvoiceCode`", "DESC");
        } elseif($sort) {
            Database_Model::getInstance()->orderBy("HostFact_CreditInvoice." . $sort, $order);
        } else {
            $order = $order ? $order : "DESC";
            Database_Model::getInstance()->orderBy("IF(SUBSTRING(HostFact_CreditInvoice.`CreditInvoiceCode`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(HostFact_CreditInvoice.`CreditInvoiceCode`,1,1))", $order)->orderBy("LENGTH(HostFact_CreditInvoice.`CreditInvoiceCode`)", $order)->orderBy("HostFact_CreditInvoice.`CreditInvoiceCode`", $order);
        }
        if(0 <= $limit) {
            Database_Model::getInstance()->limit((max(1, $limit) - 1) * $show_results, $show_results);
        }
        if(is_string($group) && 1 < count(explode("|", $group))) {
            $group = explode("|", $group);
            $or_clausule = [];
            foreach ($group as $status) {
                $or_clausule[] = ["HostFact_CreditInvoice.`Status`", $status];
            }
            Database_Model::getInstance()->orWhere($or_clausule);
        } elseif(is_numeric($group)) {
            Database_Model::getInstance()->where("HostFact_CreditInvoice.`Status`", $group);
        } else {
            Database_Model::getInstance()->where("HostFact_CreditInvoice.`Status`", ["<=" => 9]);
        }
        if(!empty($filters)) {
            foreach ($filters as $_db_field => $_db_value) {
                switch ($_db_field) {
                    case "created":
                        if(isset($_db_value["from"]) && $_db_value["from"]) {
                            Database_Model::getInstance()->where("HostFact_CreditInvoice.Created", [">=" => $_db_value["from"]]);
                        }
                        if(isset($_db_value["to"]) && $_db_value["to"]) {
                            Database_Model::getInstance()->where("HostFact_CreditInvoice.Created", ["<=" => $_db_value["to"]]);
                        }
                        break;
                    case "modified":
                        if(isset($_db_value["from"]) && $_db_value["from"]) {
                            Database_Model::getInstance()->where("HostFact_CreditInvoice.Modified", [">=" => $_db_value["from"]]);
                        }
                        if(isset($_db_value["to"]) && $_db_value["to"]) {
                            Database_Model::getInstance()->where("HostFact_CreditInvoice.Modified", ["<=" => $_db_value["to"]]);
                        }
                        break;
                    case "date":
                        if(isset($_db_value["from"]) && $_db_value["from"]) {
                            Database_Model::getInstance()->where("HostFact_CreditInvoice.Date", [">=" => $_db_value["from"]]);
                        }
                        if(isset($_db_value["to"]) && $_db_value["to"]) {
                            Database_Model::getInstance()->where("HostFact_CreditInvoice.Date", ["<=" => $_db_value["to"]]);
                        }
                        break;
                }
            }
        }
        $list = [];
        $list["TotalAmountIncl"] = 0;
        $list["TotalAmountExcl"] = 0;
        $list["CountRows"] = 0;
        if($invoice_list = Database_Model::getInstance()->execute()) {
            $list["CountRows"] = Database_Model::getInstance()->rowCount("HostFact_CreditInvoice", "HostFact_CreditInvoice.id");
            foreach ($invoice_list as $result) {
                $list[$result->id] = ["id" => $result->id];
                foreach ($fields as $column) {
                    $list[$result->id][$column] = htmlspecialchars($result->{$column});
                }
                $list[$result->id]["PayBefore"] = $result->PayBefore;
                if(isset($result->AmountIncl)) {
                    if(isset($result->AmountPaid)) {
                        $list[$result->id]["PartPayment"] = number_format($result->AmountIncl - $result->AmountPaid, 2, ".", "");
                    } else {
                        $list[$result->id]["PartPayment"] = 0;
                    }
                    $list["TotalAmountIncl"] += number_format($result->AmountIncl, 2, ".", "");
                }
                if(isset($result->AmountExcl)) {
                    $list["TotalAmountExcl"] += number_format($result->AmountExcl, 2, ".", "");
                }
            }
            if(isset($this->page_total_method) && $this->page_total_method == "all_results_open_amount") {
                $grouped_result = Database_Model::getInstance()->getGroupedData("HostFact_CreditInvoice", ["SUM(`HostFact_CreditInvoice`.`AmountIncl` - `HostFact_CreditInvoice`.`AmountPaid`) as OpenAmountIncl"]);
                $list["OpenAmountIncl"] = isset($grouped_result->OpenAmountIncl) ? $grouped_result->OpenAmountIncl : 0;
            }
        }
        return $list;
    }
    public function getCreditInvoiceBeginEndCode($firstLast = "first")
    {
        $orderBy = $firstLast == "first" ? "ASC" : "DESC";
        $result = Database_Model::getInstance()->getOne("HostFact_CreditInvoice", ["CreditInvoiceCode"])->orderBy("IF(SUBSTRING(`CreditInvoiceCode`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(`CreditInvoiceCode`,1,1))", $orderBy)->orderBy("LENGTH(`CreditInvoiceCode`)", $orderBy)->orderBy("CreditInvoiceCode", $orderBy)->execute();
        return $result->CreditInvoiceCode;
    }
}
class creditinvoiceelement
{
    public $Identifier;
    public $CreditInvoiceCode;
    public $Creditor;
    public $Number;
    public $Description;
    public $PriceExcl;
    public $TaxPercentage;
    public $Free1;
    public $Free2;
    public $Free3;
    public $Free4;
    public $Free5;
    public $CountRows;
    public $Error;
    public $Warning;
    public $success;
    public $Variables = ["Identifier", "CreditInvoiceCode", "Creditor", "Number", "Description", "PriceExcl", "TaxPercentage", "Free1", "Free2", "Free3", "Free4", "Free5"];
    public function __construct()
    {
        $this->TaxPercentage = STANDARD_TAX;
        $this->Date = rewrite_date_db2site(date("Y-m-d H:i:s"));
        $this->Error = [];
        $this->Warning = [];
        $this->Success = [];
    }
    public function show()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for creditinvoice element");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_CreditInvoiceElements")->where("id", $this->Identifier)->execute();
        if(!$result || empty($result->id)) {
            $this->Error[] = __("invalid identifier for creditinvoice element");
            return false;
        }
        foreach ($result as $key => $value) {
            if($key != "id") {
                $this->{$key} = htmlspecialchars($value);
            }
        }
        return true;
    }
    public function add()
    {
        $this->OldNumber = $this->Number;
        $this->PriceExcl = deformat_money($this->PriceExcl);
        $this->Number = deformat_money($this->Number);
        $this->TaxPercentage = deformat_money($this->TaxPercentage);
        if(!$this->validate()) {
            return false;
        }
        $this->Date = rewrite_date_site2db($this->Date);
        $result = Database_Model::getInstance()->insert("HostFact_CreditInvoiceElements", ["CreditInvoiceCode" => $this->CreditInvoiceCode, "Creditor" => $this->Creditor, "Number" => $this->Number, "Description" => $this->Description, "PriceExcl" => $this->PriceExcl, "TaxPercentage" => floatval($this->TaxPercentage)])->execute();
        if($result) {
            $this->Identifier = $result;
            return true;
        }
        return false;
    }
    public function edit()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for creditinvoice element");
            return false;
        }
        $this->OldNumber = $this->Number;
        $this->PriceExcl = deformat_money($this->PriceExcl);
        $this->Number = deformat_money($this->Number);
        $this->TaxPercentage = deformat_money($this->TaxPercentage);
        if(!$this->validate()) {
            return false;
        }
        $this->Date = rewrite_date_site2db($this->Date);
        if(is_numeric($this->Number) && isEmptyFloat($this->Number) || !$this->Number) {
            Database_Model::getInstance()->delete("HostFact_CreditInvoiceElements")->where("id", $this->Identifier)->execute();
            return true;
        }
        $result = Database_Model::getInstance()->update("HostFact_CreditInvoiceElements", ["CreditInvoiceCode" => $this->CreditInvoiceCode, "Creditor" => $this->Creditor, "Number" => $this->Number, "Description" => $this->Description, "PriceExcl" => $this->PriceExcl, "TaxPercentage" => floatval($this->TaxPercentage)])->where("id", $this->Identifier)->execute();
        if($result) {
            return true;
        }
        return false;
    }
    public function delete()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for creditinvoice element");
            return false;
        }
        $result = Database_Model::getInstance()->delete("HostFact_CreditInvoiceElements")->where("id", $this->Identifier)->execute();
        if($result) {
            return true;
        }
        return false;
    }
    public function validate()
    {
        if(!$this->CreditInvoiceCode) {
            $this->Error[] = __("invalid creditinvoicecode");
        }
        if(!is_numeric($this->Number) || strlen($this->Number) === 0) {
            $this->Error[] = sprintf(__("invalid number creditinvoice element"), $this->OldNumber);
        }
        if(!is_numeric(deformat_money($this->TaxPercentage)) || 1 <= $this->TaxPercentage || $this->TaxPercentage < 0) {
            $this->Error[] = __("invalid vat percentage creditinvoice element");
        }
        if($this->TaxPercentage && 2 < strlen(substr(strrchr($this->TaxPercentage * 100, "."), 1))) {
            $this->Error[] = __("invalid vat percentage digits creditinvoice element");
        }
        return empty($this->Error) ? true : false;
    }
    public function all($CreditInvoiceCode)
    {
        $result = Database_Model::getInstance()->get("HostFact_CreditInvoiceElements", ["COUNT(`id`) as CountRows", "SUM(ROUND(`PriceExcl` * `Number` + 0.000001,2)) as AmountExcl", "SUM(ROUND(ROUND(`PriceExcl` * `Number` + 0.000001,2)*ROUND((1+`TaxPercentage`),4),4)) as AmountIncl"])->where("CreditInvoiceCode", $CreditInvoiceCode)->groupBy("CreditInvoiceCode")->asArray()->execute();
        $list = $result[0];
        $element_list = Database_Model::getInstance()->get("HostFact_CreditInvoiceElements", ["*", "ROUND(`PriceExcl` * `Number` + 0.000001,2) as AmountExcl"])->where("CreditInvoiceCode", $CreditInvoiceCode)->orderBy("id", "ASC")->execute();
        foreach ($element_list as $result) {
            $list[$result->id] = ["id" => $result->id];
            foreach ($result as $key => $value) {
                if(in_array($key, $this->Variables) || $key == "AmountExcl") {
                    $list[$result->id][$key] = htmlspecialchars($result->{$key});
                }
            }
        }
        return 0 < $list["CountRows"] ? $list : [];
    }
}

?>