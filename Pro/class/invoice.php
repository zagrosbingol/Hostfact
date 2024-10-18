<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class invoice
{
    public $Identifier;
    public $InvoiceCode;
    public $Debtor;
    public $Date;
    public $Term;
    public $Discount;
    public $IgnoreDiscount;
    public $Coupon;
    public $ReferenceNumber;
    public $CompanyName;
    public $TaxNumber;
    public $Sex;
    public $Initials;
    public $SurName;
    public $Address;
    public $Address2;
    public $ZipCode;
    public $City;
    public $State;
    public $Country;
    public $EmailAddress;
    public $Authorisation;
    public $InvoiceMethod;
    public $Template;
    public $SentDate;
    public $Sent;
    public $Status;
    public $Reminders;
    public $ReminderDate;
    public $Summations;
    public $SummationDate;
    public $TaxRate;
    public $Compound;
    public $AmountExcl;
    public $AmountIncl;
    public $AmountPaid;
    public $PaymentMethod;
    public $PaymentMethodID;
    public $Paid;
    public $PayDate;
    public $TransactionID;
    public $SDDBatchID;
    public $AuthTrials;
    public $VatCalcMethod;
    public $VatShift;
    public $Description;
    public $InvoiceDescription;
    public $Comment;
    public $SubStatus;
    public $Attachment;
    public $CorrespondingInvoice;
    public $Elements;
    public $ExtraElement;
    public $CountRows;
    public $Table;
    public $Error;
    public $Warning;
    public $Success;
    public $Variables = ["Identifier", "InvoiceCode", "Debtor", "Date", "Term", "Discount", "IgnoreDiscount", "Coupon", "ReferenceNumber", "CompanyName", "TaxNumber", "Sex", "Initials", "SurName", "Address", "Address2", "ZipCode", "City", "State", "Country", "EmailAddress", "Authorisation", "InvoiceMethod", "Template", "Status", "Sent", "SentDate", "Reminders", "ReminderDate", "Summations", "SummationDate", "TaxRate", "Compound", "PaymentMethod", "PaymentMethodID", "Paid", "PayDate", "TransactionID", "SDDBatchID", "AuthTrials", "Description", "Comment", "AmountPaid", "VatCalcMethod", "SubStatus", "VatShift"];
    public function __construct()
    {
        global $company;
        $this->Date = rewrite_date_db2site(date("Y-m-d H:i:s"));
        $this->Discount = "0";
        $this->Authorisation = "no";
        $this->Status = "0";
        $this->AmountPaid = 0;
        $this->InvoiceMethod = STANDARD_INVOICEMETHOD;
        $this->Sent = 0;
        $this->Reminders = 0;
        $this->Summations = 0;
        $this->Country = $company->Country ?? "NL";
        $this->PaymentMethodID = 0;
        $this->Paid = 0;
        $this->AuthTrials = 0;
        $this->Term = INVOICE_TERM;
        require_once "class/template.php";
        $template = new template();
        $this->Template = $template->getStandard("invoice");
        if(!defined("INVOICE_STD_TEMPLATE")) {
            define("INVOICE_STD_TEMPLATE", $this->Template);
        }
        $this->Error = [];
        $this->Warning = [];
        $this->Success = [];
        $this->PeriodicInvoice = false;
        $this->LastOfBatch = false;
        $this->hasPrintQueue = false;
        $this->PartOfBatch = false;
        $this->StateName = "";
        $this->TaxRate = STANDARD_TOTAL_TAX;
        $this->Compound = "no";
        $this->Attachment = [];
        $this->VatCalcMethod = VAT_CALC_METHOD;
        $this->VatShift = "";
        $this->customfields_list = [];
        if(370 <= str_replace(".", "", SOFTWARE_VERSION) && (!isset($_SESSION["custom_fields"]["invoice"]) || $_SESSION["custom_fields"]["invoice"])) {
            require_once "class/customfields.php";
            $customfields = new customfields();
            $this->customfields_list = $customfields->getCustomInvoiceFields();
        }
    }
    public function show()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for invoice");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_Invoice", ["*", "DATE_ADD(`Date`,INTERVAL `Term` DAY) as PayBefore", "MD5(CONCAT(`id`, `InvoiceCode`, `Date`, `AmountIncl`)) as `PaymentURL`", "`Description` as `InvoiceDescription`"])->where("id", $this->Identifier)->execute();
        if(!$result || empty($result->id)) {
            $this->Error[] = __("invalid identifier for invoice");
            return false;
        }
        foreach ($result as $key => $value) {
            if($key != "id" && ($key != "Template" || $value != "0")) {
                $this->{$key} = htmlspecialchars($value);
            }
        }
        $elements = new invoiceelement();
        $this->Elements = $elements->all($this->InvoiceCode);
        $this->Discount = $this->Discount * 100;
        $this->PartPayment = round($this->AmountIncl - $this->AmountPaid, 2);
        $this->Name = $this->Initials . " " . $this->SurName;
        global $array_states;
        $this->CountryLong = countryCodeToLong($this->Country);
        $this->StateName = isset($array_states[$this->Country][$this->State]) ? $array_states[$this->Country][$this->State] : $this->State;
        $this->PaymentURLRaw = IDEAL_EMAIL . "?payment=" . $this->InvoiceCode . "&amp;key=" . $this->PaymentURL;
        $this->IDEALlinkRaw = $this->PaymentURLRaw;
        $this->PaymentURL = "<a href={&quot;}" . IDEAL_EMAIL . "?payment=" . $this->InvoiceCode . "&amp;key=" . $this->PaymentURL . "{&quot;}>" . IDEAL_EMAIL . "?payment=" . $this->InvoiceCode . "&amp;key=" . $this->PaymentURL . "</a>";
        $this->IDEALlink = $this->PaymentURL;
        $this->OldDebtor = $this->Debtor;
        $this->CurrentStatus = $this->Status;
        if(!empty($this->customfields_list)) {
            $customfields = new customfields();
            $custom_values = $customfields->getCustomInvoiceFieldsValues($this->Identifier);
            if($custom_values && is_array($custom_values)) {
                $this->custom = new stdClass();
                $this->customvalues = [];
                foreach ($custom_values as $field_name => $custom_value) {
                    $this->custom->{$field_name} = $custom_value["ValueFormatted"];
                    $this->customvalues[$field_name] = $custom_value["Value"];
                }
            }
        }
        return true;
    }
    public function format($euro = true, $override_amountpaid = true)
    {
        if(is_array($this->Elements)) {
            $disc_percentage = $this->Discount / 100;
            $financial_totals = calculateFinancialTotals($this->VatCalcMethod, $this->Elements, $disc_percentage);
            $this->AmountExcl = $financial_totals["totals"]["AmountExcl"];
            $this->AmountIncl = $financial_totals["totals"]["AmountIncl"];
            $used_rates = $financial_totals["used_rates"];
        }
        global $array_taxpercentages;
        foreach ($array_taxpercentages as $k => $v) {
            $key = str_replace(".", "_", $k * 100);
            $this->{"AmountExcl_" . $key} = money(isset($used_rates[$k]["AmountExcl"]) ? $used_rates[$k]["AmountExcl"] : 0, false);
            $this->{"AmountTax_" . $key} = money(isset($used_rates[$k]["AmountTax"]) ? $used_rates[$k]["AmountTax"] : 0, false);
        }
        foreach ($used_rates as $k => $v) {
            $used_rates[$k]["AmountExcl"] = money($v["AmountExcl"], false);
            $used_rates[$k]["AmountTax"] = money($v["AmountTax"], false);
            $used_rates[$k]["AmountIncl"] = money($v["AmountIncl"], false);
        }
        $this->AmountDiscount = money(0, $euro);
        $this->AmountDiscountIncl = money(0, $euro);
        if(0 < $this->Discount) {
            $financial_totals = calculateFinancialTotals($this->VatCalcMethod, $this->Elements, 0);
            $this->AmountDiscount = money(-1 * ($financial_totals["totals"]["AmountExcl"] - $this->AmountExcl), $euro);
            $this->AmountDiscountIncl = money(-1 * ($financial_totals["totals"]["AmountIncl"] - $this->AmountIncl), $euro);
        }
        $this->TaxRate_Amount = 0;
        $this->TaxRate_Label = "";
        if(0 < $this->TaxRate) {
            global $array_total_taxpercentages_info;
            if($this->Compound == "yes") {
                $this->TaxRate_Amount = round($this->AmountIncl - round($this->AmountIncl / (1 + $this->TaxRate), 2), 2);
            } else {
                $this->TaxRate_Amount = round($this->AmountExcl * $this->TaxRate, 2);
            }
            $this->TaxRate_Label = isset($array_total_taxpercentages_info[(string) (double) $this->TaxRate]["label"]) ? $array_total_taxpercentages_info[(string) (double) $this->TaxRate]["label"] : "";
            $this->AmountIncl += $this->TaxRate_Amount;
            $this->TaxRate_Amount = money($this->TaxRate_Amount, $euro);
        }
        $this->AmountTax = $this->AmountIncl - $this->AmountExcl;
        $this->AmountTax = money($this->AmountTax, $euro);
        $this->AmountExcl = money($this->AmountExcl, $euro);
        $this->AmountIncl = money($this->AmountIncl, $euro);
        if($override_amountpaid === true && $this->Status == 4) {
            $this->AmountPaid = money($this->AmountIncl, $euro);
            $this->PartPayment = money(0, $euro);
        } else {
            $this->AmountPaid = money($this->AmountPaid, $euro);
            $this->PartPayment = money($this->PartPayment, $euro);
        }
        ksort($used_rates);
        $this->used_taxrates = $used_rates;
        $this->Date = rewrite_date_db2site($this->Date);
        $this->ReminderDate = rewrite_date_db2site($this->ReminderDate);
        $this->SummationDate = rewrite_date_db2site($this->SummationDate);
        $this->PayBefore = isset($this->PayBefore) ? rewrite_date_db2site($this->PayBefore) : "";
        $this->PayDate = rewrite_date_db2site($this->PayDate);
        $this->PaymentMethodName = "";
        if(0 < $this->PaymentMethodID) {
            $result = Database_Model::getInstance()->getOne("HostFact_PaymentMethods", "InternalName")->where("id", $this->PaymentMethodID)->execute();
            if(isset($result->InternalName) && $result->InternalName) {
                $this->PaymentMethodName = htmlspecialchars($result->InternalName);
            }
        }
        $this->DirectDebitDate = $this->Authorisation == "yes" && $this->SDDBatchID ? rewrite_date_db2site(str_replace("SDD", "", $this->SDDBatchID)) : "";
        $this->Description = nl2br($this->Description);
        return true;
    }
    public function changePaymentProcessStatus($status)
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for invoice");
            return false;
        }
        if(!in_array($status, ["pause", "reactivate"])) {
            $this->Error[] = sprintf(__("invalid collection status"), $status);
            return false;
        }
        switch ($status) {
            case "pause":
                if($this->SubStatus != "") {
                    $this->Error[] = __("collection already started");
                    return false;
                }
                $CollectionStatus = "PAUSED";
                break;
            case "reactivate":
                if($this->SubStatus != "PAUSED") {
                    $this->Error[] = __("collection not started");
                    return false;
                }
                $CollectionStatus = "";
                break;
            default:
                $result = Database_Model::getInstance()->update("HostFact_Invoice", ["SubStatus" => $CollectionStatus])->where("id", $this->Identifier)->execute();
                if($result) {
                    createLog("invoice", $this->Identifier, "payment process " . $status);
                    $this->Success[] = sprintf(__("success payment process " . $status), $this->InvoiceCode);
                    delete_stats_summary();
                    return true;
                }
                return false;
        }
    }
    public function getID($method, $value, $debtor_id = false)
    {
        switch ($method) {
            case "invoicecode":
                $invoice_id = Database_Model::getInstance()->getOne("HostFact_Invoice", ["id"])->where("InvoiceCode", $value)->execute();
                return $invoice_id !== false && 0 < $invoice_id->id ? $invoice_id->id : false;
                break;
            case "id":
                $invoiceCode = Database_Model::getInstance()->getOne("HostFact_Invoice", ["InvoiceCode"])->where("id", intval($value))->execute();
                return $invoiceCode !== false ? $invoiceCode->InvoiceCode : false;
                break;
            case "identifier":
                $invoice_id = Database_Model::getInstance()->getOne("HostFact_Invoice", ["id"])->where("id", intval($value))->execute();
                return $invoice_id !== false && 0 < $invoice_id->id ? $invoice_id->id : false;
                break;
            case "clientarea":
                $invoice_id = Database_Model::getInstance()->getOne("HostFact_Invoice", ["id"])->where("id", intval($value))->where("Debtor", $debtor_id)->where("Status", ["IN" => [2, 3, 4, 8, 9]])->execute();
                return $invoice_id !== false && 0 < $debtor_id ? $invoice_id->id : false;
                break;
        }
    }
    public function getInvoiceCode($firstLast = "first")
    {
        $orderBy = $firstLast == "first" ? "ASC" : "DESC";
        $result = Database_Model::getInstance()->getOne("HostFact_Invoice", ["InvoiceCode"])->where("Status", ["!=" => 0])->orderBy("IF(SUBSTRING(`InvoiceCode`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(`InvoiceCode`,1,1))", $orderBy)->orderBy("LENGTH(`InvoiceCode`)", $orderBy)->orderBy("InvoiceCode", $orderBy)->execute();
        return $result->InvoiceCode;
    }
    public function changeInvoiceCode($Identifier, $newInvoiceCode)
    {
        $invoiceData = Database_Model::getInstance()->getOne("HostFact_Invoice", ["InvoiceCode", "Status"])->where("id", $Identifier)->execute();
        if($invoiceData === false) {
            $this->Error[] = __("invalid identifier for invoice");
            return false;
        }
        if($invoiceData->InvoiceCode == $newInvoiceCode) {
            return true;
        }
        Database_Model::getInstance()->update("HostFact_Invoice", ["InvoiceCode" => $newInvoiceCode])->where("id", $Identifier)->execute();
        $result = Database_Model::getInstance()->update("HostFact_InvoiceElements", ["InvoiceCode" => $newInvoiceCode])->where("InvoiceCode", $invoiceData->InvoiceCode)->execute();
        if($result) {
            return true;
        }
        return false;
    }
    public function changeDebtor($invoiceId, $newDebtorId)
    {
        $invoiceCode = $this->getID("id", $invoiceId);
        if($invoiceCode === false) {
            $this->Error[] = __("invalid identifier for invoice");
            return false;
        }
        require_once "class/debtor.php";
        $debtor = new debtor();
        $debtor->Identifier = $newDebtorId;
        if($debtor->show()) {
            Database_Model::getInstance()->update("HostFact_Invoice", ["Debtor" => $debtor->Identifier, "CompanyName" => $debtor->InvoiceCompanyName ? htmlspecialchars_decode($debtor->InvoiceCompanyName) : htmlspecialchars_decode($debtor->CompanyName), "TaxNumber" => htmlspecialchars_decode($debtor->TaxNumber), "Sex" => $debtor->InvoiceSurName ? htmlspecialchars_decode($debtor->InvoiceSex) : htmlspecialchars_decode($debtor->Sex), "Initials" => $debtor->InvoiceInitials ? htmlspecialchars_decode($debtor->InvoiceInitials) : htmlspecialchars_decode($debtor->Initials), "SurName" => $debtor->InvoiceSurName ? htmlspecialchars_decode($debtor->InvoiceSurName) : htmlspecialchars_decode($debtor->SurName), "Address" => $debtor->InvoiceAddress ? htmlspecialchars_decode($debtor->InvoiceAddress) : htmlspecialchars_decode($debtor->Address), "Address2" => $debtor->InvoiceAddress2 ? htmlspecialchars_decode($debtor->InvoiceAddress2) : htmlspecialchars_decode($debtor->Address2), "ZipCode" => $debtor->InvoiceZipCode ? htmlspecialchars_decode($debtor->InvoiceZipCode) : htmlspecialchars_decode($debtor->ZipCode), "City" => $debtor->InvoiceCity ? htmlspecialchars_decode($debtor->InvoiceCity) : htmlspecialchars_decode($debtor->City), "State" => $debtor->InvoiceState ? htmlspecialchars_decode($debtor->InvoiceState) : htmlspecialchars_decode($debtor->State), "Country" => $debtor->InvoiceCountry && $debtor->InvoiceAddress ? htmlspecialchars_decode($debtor->InvoiceCountry) : htmlspecialchars_decode($debtor->Country), "EmailAddress" => htmlspecialchars_decode(check_email_address($debtor->InvoiceEmailAddress ? $debtor->InvoiceEmailAddress : $debtor->EmailAddress, "convert")), "Authorisation" => $debtor->InvoiceAuthorisation])->where("id", $invoiceId)->execute();
            Database_Model::getInstance()->update("HostFact_InvoiceElements", ["Debtor" => $debtor->Identifier])->where("InvoiceCode", $invoiceCode)->execute();
        } else {
            $this->Error[] = __("invalid identifier for debtor");
        }
    }
    public function add($discountcheck = true, $createforpricequote = false)
    {
        if($this->InvoiceMethod === false || $this->InvoiceMethod == "") {
            require_once "class/debtor.php";
            $debtor = new debtor();
            $debtor->Identifier = $this->Debtor;
            $debtor->show();
            $this->InvoiceMethod = $debtor->InvoiceMethod;
        }
        $elements = new invoiceelement();
        $elements->VatCalcMethod = $this->VatCalcMethod;
        $this->Elements = $elements->all($this->InvoiceCode);
        foreach ($this->Elements as $k => $v) {
            if(is_numeric($k) && $v["Debtor"] != $this->Debtor) {
                $this->Error[] = sprintf(__("mixed debtor for invoice"), $this->InvoiceCode);
                $this->recover_mixed_invoices();
                return false;
            }
        }
        if($discountcheck === true && (int) $this->IgnoreDiscount === 0) {
            require_once "class/discount.php";
            $discount = new discount();
            $result = $discount->check($this->Debtor, $this->Elements, $this->Coupon, $this->Authorisation, $this->VatCalcMethod);
            if($result) {
                $totaal = isset($this->Elements["AmountExcl"]) ? $this->Elements["AmountExcl"] : 0;
                $discount_percentage = $this->Discount;
                foreach ($result as $value) {
                    $discount->Identifier = $value;
                    $discount->show();
                    $discount_percentage = $discount_percentage < $discount->DiscountPercentage ? $discount->DiscountPercentage : $discount_percentage;
                    if(!isEmptyFloat($discount->Discount)) {
                        $elements->InvoiceCode = $this->InvoiceCode;
                        $elements->Debtor = $this->Debtor;
                        $elements->Number = 1;
                        $elements->Description = htmlspecialchars_decode($discount->Description);
                        $elements->PriceExcl = $totaal < $discount->Discount ? -1 * $totaal : -1 * $discount->Discount;
                        $totaal = $totaal < $discount->Discount ? 0 : $totaal - $discount->Discount;
                        $elements->TaxPercentage = btwcheck($this->Debtor, STANDARD_TAX);
                        $elements->Ordering = isset($this->Elements["CountRows"]) ? $this->Elements["CountRows"] : 0;
                        $elements->add();
                    }
                }
                $this->Discount = $discount_percentage;
            }
            $this->Elements = $elements->all($this->InvoiceCode);
        }
        $this->Discount = round((double) number2db($this->Discount), 2) / 100;
        $this->Date = rewrite_date_site2db($this->Date);
        $this->SentDate = rewrite_date_site2db($this->SentDate);
        $this->PayDate = rewrite_date_site2db($this->PayDate);
        $financial_totals = calculateFinancialTotals($this->VatCalcMethod, $this->Elements, $this->Discount);
        $this->AmountExcl = $financial_totals["totals"]["AmountExcl"];
        $this->AmountIncl = $financial_totals["totals"]["AmountIncl"];
        $this->TaxRate = btwcheck($this->Debtor, $this->TaxRate, "total");
        if(0 < $this->TaxRate) {
            global $array_total_taxpercentages_info;
            $this->Compound = isset($array_total_taxpercentages_info[(string) (double) $this->TaxRate]["compound"]) ? $array_total_taxpercentages_info[(string) (double) $this->TaxRate]["compound"] : "no";
            if($this->Compound == "yes") {
                $this->TaxRate_Amount = round($this->AmountIncl * $this->TaxRate, 2);
                $this->AmountIncl = $this->AmountIncl + $this->TaxRate_Amount;
            } else {
                $this->TaxRate_Amount = round($this->AmountExcl * $this->TaxRate, 2);
                $this->AmountIncl = $this->AmountIncl + $this->TaxRate_Amount;
            }
        }
        if(0 < $this->AmountIncl) {
            $this->AmountIncl = number_format($this->AmountIncl + 0, 2, ".", "");
        } elseif($this->AmountIncl < 0) {
            $this->AmountIncl = number_format($this->AmountIncl - 0, 2, ".", "");
        }
        if(isset($this->customfields_list) && 0 < count($this->customfields_list) && !isset($this->customvalues)) {
            $this->getDefaultCustomValuesWithDebtorSync();
        }
        if($this->validate() === false) {
            $this->Discount = $this->Discount * 100;
            $this->Date = rewrite_date_db2site($this->Date);
            $this->SentDate = rewrite_date_db2site($this->SentDate);
            $this->PayDate = rewrite_date_db2site($this->PayDate);
            return false;
        }
        $result = Database_Model::getInstance()->insert("HostFact_Invoice", ["InvoiceCode" => $this->InvoiceCode, "Debtor" => $this->Debtor, "Date" => $this->Date, "Term" => $this->Term, "Discount" => $this->Discount, "IgnoreDiscount" => $this->IgnoreDiscount, "Coupon" => $this->Coupon, "ReferenceNumber" => $this->ReferenceNumber, "CompanyName" => $this->CompanyName, "TaxNumber" => $this->TaxNumber, "Sex" => $this->Sex, "Initials" => $this->Initials, "SurName" => $this->SurName, "Address" => $this->Address, "Address2" => $this->Address2, "ZipCode" => $this->ZipCode, "City" => $this->City, "State" => $this->State, "Country" => $this->Country, "EmailAddress" => check_email_address($this->EmailAddress, "convert"), "Authorisation" => $this->Authorisation, "InvoiceMethod" => $this->InvoiceMethod, "Template" => $this->Template, "Status" => $this->Status, "Sent" => $this->Sent, "SentDate" => $this->SentDate, "Reminders" => $this->Reminders, "ReminderDate" => $this->ReminderDate, "Summations" => $this->Summations, "SummationDate" => $this->SummationDate, "TaxRate" => $this->TaxRate, "Compound" => $this->Compound, "PaymentMethod" => $this->PaymentMethod, "PaymentMethodID" => $this->PaymentMethodID, "Paid" => $this->Paid, "PayDate" => $this->PayDate, "TransactionID" => $this->TransactionID, "SDDBatchID" => $this->SDDBatchID, "AuthTrials" => $this->AuthTrials, "Description" => $this->Description, "Comment" => $this->Comment, "AmountPaid" => $this->AmountPaid, "VatCalcMethod" => $this->VatCalcMethod, "SubStatus" => $this->SubStatus, "AmountExcl" => $this->AmountExcl, "AmountIncl" => $this->AmountIncl, "VatShift" => $this->VatShift, "CorrespondingInvoice" => $this->CorrespondingInvoice])->execute();
        if($result) {
            $this->Identifier = $result;
            if($createforpricequote) {
                createLog("invoice", $this->Identifier, "invoice created from pricequote x", [$createforpricequote]);
            } elseif(isset($this->logSuccessMessage) && $this->logSuccessMessage) {
                createLog("invoice", $this->Identifier, $this->logSuccessMessage["message"], $this->logSuccessMessage["values"]);
            } else {
                createLog("invoice", $this->Identifier, "invoice created");
            }
            if(SDD_ID && $this->Authorisation == "yes" && ($this->Status == "2" || $this->Status == "3")) {
                $this->addInvoiceToSDDBatch($this->Identifier);
            }
            if(!empty($this->customfields_list)) {
                $customfields = new customfields();
                $customfields->setCustomInvoiceFieldsValues($this->Identifier, $this->customvalues);
            }
            $invoice_link = "[hyperlink_1]invoices.php?page=show&id=" . $this->Identifier . "[hyperlink_2]" . $this->InvoiceCode . "[hyperlink_3]";
            $this->Success[] = sprintf(__("invoice add success"), $invoice_link);
            $invoice_info = ["id" => $this->Identifier, "Debtor" => $this->Debtor, "InvoiceCode" => $this->InvoiceCode];
            do_action("invoice_is_created", $invoice_info);
            return true;
        }
        $elements = new invoiceelement();
        $this->Elements = $elements->all($this->InvoiceCode);
        $this->Discount = $this->Discount * 100;
        $this->Date = rewrite_date_db2site($this->Date);
        $this->SentDate = rewrite_date_db2site($this->SentDate);
        $this->PayDate = rewrite_date_db2site($this->PayDate);
        return false;
    }
    public function edit()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for invoice");
            return false;
        }
        if($this->InvoiceMethod === false || $this->InvoiceMethod == "") {
            require_once "class/debtor.php";
            $debtor = new debtor();
            $debtor->Identifier = $this->Debtor;
            $debtor->show();
            $this->InvoiceMethod = $debtor->InvoiceMethod;
        }
        $elements = new invoiceelement();
        $elements->VatCalcMethod = $this->VatCalcMethod;
        $this->Elements = $elements->all($this->InvoiceCode);
        $discountcheck = true;
        if($discountcheck === true && (int) $this->IgnoreDiscount === 0) {
            require_once "class/discount.php";
            $discount = new discount();
            $result = $discount->check($this->Debtor, $this->Elements, $this->Coupon, $this->Authorisation, $this->VatCalcMethod);
            if($result) {
                $totaal = $this->Elements["AmountExcl"];
                $discount_percentage = $this->Discount;
                foreach ($result as $value) {
                    $discount->Identifier = $value;
                    $discount->show();
                    $discount_percentage = $discount_percentage < $discount->DiscountPercentage ? $discount->DiscountPercentage : $discount_percentage;
                    if(!isEmptyFloat($discount->Discount)) {
                        $already_on_invoice = false;
                        foreach ($this->Elements as $k => $v) {
                            if(is_numeric($k) && $v["Description"] == $discount->Description) {
                                $already_on_invoice = true;
                            }
                        }
                        if($already_on_invoice !== true) {
                            $elements->InvoiceCode = $this->InvoiceCode;
                            $elements->Debtor = $this->Debtor;
                            $elements->Number = 1;
                            $elements->Description = htmlspecialchars_decode($discount->Description);
                            $elements->PriceExcl = $totaal < $discount->Discount ? -1 * $totaal : -1 * $discount->Discount;
                            $totaal = $totaal < $discount->Discount ? 0 : $totaal - $discount->Discount;
                            $elements->TaxPercentage = btwcheck($this->Debtor, STANDARD_TAX);
                            $elements->Ordering = isset($this->Elements["CountRows"]) ? $this->Elements["CountRows"] : 0;
                            $elements->add();
                        }
                    }
                }
                $this->Discount = $discount_percentage;
            }
            $this->Elements = $elements->all($this->InvoiceCode);
        }
        $this->Discount = round((double) number2db($this->Discount), 2) / 100;
        $this->Date = rewrite_date_site2db($this->Date);
        $this->SentDate = rewrite_date_site2db($this->SentDate);
        $this->ReminderDate = rewrite_date_site2db($this->ReminderDate);
        $this->SummationDate = rewrite_date_site2db($this->SummationDate);
        $this->PayDate = rewrite_date_site2db($this->PayDate);
        $financial_totals = calculateFinancialTotals($this->VatCalcMethod, $this->Elements, $this->Discount);
        $this->AmountExcl = $financial_totals["totals"]["AmountExcl"];
        $this->AmountIncl = $financial_totals["totals"]["AmountIncl"];
        $this->TaxRate = btwcheck($this->Debtor, $this->TaxRate, "total");
        if(0 < $this->TaxRate) {
            global $array_total_taxpercentages_info;
            $this->Compound = isset($array_total_taxpercentages_info[(string) (double) $this->TaxRate]["compound"]) ? $array_total_taxpercentages_info[(string) (double) $this->TaxRate]["compound"] : "no";
            if($this->Compound == "yes") {
                $this->TaxRate_Amount = round($this->AmountIncl * $this->TaxRate, 2);
                $this->AmountIncl = $this->AmountIncl + $this->TaxRate_Amount;
            } else {
                $this->TaxRate_Amount = round($this->AmountExcl * $this->TaxRate, 2);
                $this->AmountIncl = $this->AmountIncl + $this->TaxRate_Amount;
            }
        }
        if(0 < $this->AmountIncl) {
            $this->AmountIncl = number_format($this->AmountIncl + 0, 2, ".", "");
        } elseif($this->AmountIncl < 0) {
            $this->AmountIncl = number_format($this->AmountIncl - 0, 2, ".", "");
        }
        if((int) $this->Status !== 0 && $this->SubStatus == "BLOCKED") {
            $this->SubStatus = "";
        }
        if($this->validate() === false) {
            $this->Discount = $this->Discount * 100;
            $this->Date = rewrite_date_db2site($this->Date);
            $this->SentDate = rewrite_date_db2site($this->SentDate);
            $this->ReminderDate = rewrite_date_db2site($this->ReminderDate);
            $this->SummationDate = rewrite_date_db2site($this->SummationDate);
            $this->PayDate = rewrite_date_db2site($this->PayDate);
            return false;
        }
        if(0 < $this->CurrentStatus && (int) $this->Status === 0) {
            $this->SentDate = "0000-00-00 00:00:00";
        }
        $result = Database_Model::getInstance()->update("HostFact_Invoice", ["InvoiceCode" => $this->InvoiceCode, "Debtor" => $this->Debtor, "Date" => $this->Date, "Term" => $this->Term, "Discount" => $this->Discount, "IgnoreDiscount" => $this->IgnoreDiscount, "Coupon" => $this->Coupon, "ReferenceNumber" => $this->ReferenceNumber, "CompanyName" => $this->CompanyName, "TaxNumber" => $this->TaxNumber, "Sex" => $this->Sex, "Initials" => $this->Initials, "SurName" => $this->SurName, "Address" => $this->Address, "Address2" => $this->Address2, "ZipCode" => $this->ZipCode, "City" => $this->City, "State" => $this->State, "Country" => $this->Country, "EmailAddress" => check_email_address($this->EmailAddress, "convert"), "Authorisation" => $this->Authorisation, "InvoiceMethod" => $this->InvoiceMethod, "Template" => $this->Template, "Status" => $this->Status, "Sent" => $this->Sent, "SentDate" => $this->SentDate, "Reminders" => $this->Reminders, "ReminderDate" => $this->ReminderDate, "Summations" => $this->Summations, "SummationDate" => $this->SummationDate, "TaxRate" => $this->TaxRate, "Compound" => $this->Compound, "PaymentMethod" => $this->PaymentMethod, "PaymentMethodID" => $this->PaymentMethodID, "Paid" => $this->Paid, "PayDate" => $this->PayDate, "TransactionID" => $this->TransactionID, "SDDBatchID" => $this->SDDBatchID, "AuthTrials" => $this->AuthTrials, "Description" => $this->Description, "Comment" => $this->Comment, "AmountPaid" => $this->AmountPaid, "VatCalcMethod" => $this->VatCalcMethod, "SubStatus" => $this->SubStatus, "AmountExcl" => $this->AmountExcl, "AmountIncl" => $this->AmountIncl, "VatShift" => $this->VatShift])->where("id", $this->Identifier)->execute();
        if($result) {
            createLog("invoice", $this->Identifier, "invoice adjusted");
            if(SDD_ID && $this->Authorisation == "yes" && $this->SDDBatchID == "" && ($this->Status == "2" || $this->Status == "3")) {
                $this->addInvoiceToSDDBatch($this->Identifier);
            } elseif($this->SDDBatchID != "" && ($this->Authorisation == "no" || $this->Status < 2)) {
                require_once "class/directdebit.php";
                $directdebit = new directdebit();
                $directdebit->removeDirectDebitFromInvoiceByBatchAndInvoiceID($this->SDDBatchID, $this->Identifier);
            }
            if(!empty($this->customfields_list)) {
                $customfields = new customfields();
                $customfields->setCustomInvoiceFieldsValues($this->Identifier, $this->customvalues);
            }
            $invoice_link = "[hyperlink_1]invoices.php?page=show&id=" . $this->Identifier . "[hyperlink_2]" . $this->InvoiceCode . "[hyperlink_3]";
            $this->Success[] = sprintf(__("invoice edit success"), $invoice_link);
            $invoice_info = ["id" => $this->Identifier, "Debtor" => $this->Debtor, "InvoiceCode" => $this->InvoiceCode];
            do_action("invoice_is_edited", $invoice_info);
            return true;
        }
        $elements = new invoiceelement();
        $this->Elements = $elements->all($this->InvoiceCode);
        $this->Discount = $this->Discount * 100;
        $this->Date = rewrite_date_db2site($this->Date);
        $this->SentDate = rewrite_date_db2site($this->SentDate);
        $this->ReminderDate = rewrite_date_db2site($this->ReminderDate);
        $this->SummationDate = rewrite_date_db2site($this->SummationDate);
        $this->PayDate = rewrite_date_db2site($this->PayDate);
        return false;
    }
    public function delete($remove = false)
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for invoice");
            return false;
        }
        if($remove === false) {
            $type = isset($_POST["ids"]) ? "partly" : "full";
            $line_ids = isset($_POST["ids"]) ? $_POST["ids"] : [];
            $line_numbers = isset($_POST["Number"]) ? $_POST["Number"] : [];
            return $this->createCreditInvoice($type, $line_ids, $line_numbers);
        }
        $result1 = Database_Model::getInstance()->delete("HostFact_InvoiceElements")->where("InvoiceCode", ["IN" => ["RAW" => "SELECT `InvoiceCode` FROM `HostFact_Invoice` WHERE `id`=:invoice_id"]])->bindValue("invoice_id", $this->Identifier)->execute();
        $result2 = Database_Model::getInstance()->delete("HostFact_Invoice")->where("id", $this->Identifier)->execute();
        $result3 = Database_Model::getInstance()->delete("HostFact_Log")->where("Type", "invoice")->where("Reference", $this->Identifier)->execute();
        Database_Model::getInstance()->delete("HostFact_ExportHistory")->where("Type", ["IN" => ["invoice", "payment_invoice"]])->where("ReferenceID", $this->Identifier)->execute();
        Database_Model::getInstance()->delete("HostFact_Debtor_Custom_Values")->where("ReferenceType", "invoice")->where("ReferenceID", $this->Identifier)->execute();
        if($result1 && $result2 && $result3) {
            $this->Success[] = sprintf(__("invoice is removed"), $this->InvoiceCode);
            return true;
        }
        return false;
    }
    public function changeComment($id, $comment = "")
    {
        if(!is_numeric($id)) {
            $this->Error[] = __("invalid identifier for invoice");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Invoice", ["Comment" => $comment])->where("id", $id)->execute();
        if($result) {
            createLog("invoice", $id, "comment adjusted");
            $this->Success[] = __("comment adjusted");
            return true;
        }
        return false;
    }
    public function markaspaid($date = "")
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for invoice");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Invoice", ["Status" => 4, "PayDate" => $date ? $date : ["RAW" => "CURDATE()"]])->where("id", $this->Identifier)->execute();
        if($result) {
            $this->Status = 4;
            if(isset($date)) {
                createLog("invoice", $this->Identifier, "invoice paid on", rewrite_date_db2site($date));
            } else {
                createLog("invoice", $this->Identifier, "invoice paid");
            }
            if($this->SDDBatchID && (!isset($this->preventDirectDebitAction) || $this->preventDirectDebitAction !== true)) {
                require_once "class/directdebit.php";
                $directdebit = new directdebit();
                $directdebit->markInvoiceAsPaidByInvoiceID($this->Identifier);
            }
            if($this->SubStatus == "PAUSED") {
                Database_Model::getInstance()->update("HostFact_Invoice", ["SubStatus" => ""])->where("id", $this->Identifier)->execute();
                delete_stats_summary();
            }
            $this->Success[] = __("invoice paid", ["invoicecode" => $this->InvoiceCode]);
            return true;
        }
        return false;
    }
    public function updateOpenAmountViaPackage($new_open_amount, $package)
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for invoice");
            return false;
        }
        if(3 < $this->Status) {
            $this->Error[] = __("invoice already paid", ["invoicecode" => $this->InvoiceCode]);
            return false;
        }
        $data = ["Status" => 3, "AmountPaid" => round($this->AmountIncl - $new_open_amount, 2), "PaymentMethod" => "other"];
        if(isEmptyFloat($new_open_amount)) {
            $data["Status"] = 4;
            $data["PayDate"] = date("Y-m-d");
            if($this->SubStatus == "PAUSED") {
                $data["SubStatus"] = "";
                delete_stats_summary();
            }
        } elseif($new_open_amount == $this->AmountIncl) {
            $data["Status"] = 2;
            $data["PaymentMethod"] = "";
            $data["PayDate"] = "0000-00-00";
            $data["PaymentMethodID"] = "";
            $data["TransactionID"] = "";
            $data["Paid"] = 0;
            $data["AmountPaid"] = 0;
        }
        $result = Database_Model::getInstance()->update("HostFact_Invoice", $data)->where("id", $this->Identifier)->execute();
        if(!$result) {
            $this->Error[] = __("invoice not paid partly", ["invoicecode" => $this->InvoiceCode]);
            return false;
        }
        if($data["Status"] == 4) {
            createLog("invoice", $this->Identifier, "invoice paid via package", [$package]);
            $this->Success[] = __("invoice paid", ["invoicecode" => $this->InvoiceCode]);
            $this->checkAuto();
        } else {
            createLog("invoice", $this->Identifier, "invoice paid partly via package", [money($this->AmountIncl - $this->AmountPaid), money($new_open_amount), $package]);
            $this->Success[] = __("invoice paid partly via package", ["openamount_old" => money($this->AmountIncl - $this->AmountPaid), "openamount_new" => money($new_open_amount), "invoicecode" => $this->InvoiceCode]);
            if($data["Status"] == 2) {
                $invoice_info = ["id" => $this->Identifier, "Debtor" => $this->Debtor, "InvoiceCode" => $this->InvoiceCode];
                do_action("invoice_is_unpaid", $invoice_info);
            } else {
                $invoice_info = ["id" => $this->Identifier, "Debtor" => $this->Debtor, "InvoiceCode" => $this->InvoiceCode, "AmountPaidNow" => $data["AmountPaid"] - $this->AmountPaid, "AmountPaidTotal" => $data["AmountPaid"], "AmountOpen" => $this->AmountIncl - $data["AmountPaid"]];
                do_action("invoice_is_partly_paid", $invoice_info);
            }
        }
        return true;
    }
    public function markasunpaid()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for invoice");
            return false;
        }
        if($this->AmountPaid == $this->AmountIncl) {
            $result = Database_Model::getInstance()->update("HostFact_Invoice", ["Status" => 2, "PayDate" => "0000-00-00", "PaymentMethod" => "", "PaymentMethodID" => "", "TransactionID" => "", "Paid" => 0, "AmountPaid" => 0])->where("id", $this->Identifier)->execute();
        } elseif(!isEmptyFloat($this->AmountPaid)) {
            $result = Database_Model::getInstance()->update("HostFact_Invoice", ["Status" => 3, "PayDate" => "0000-00-00", "PaymentMethod" => "", "PaymentMethodID" => "", "TransactionID" => "", "Paid" => 0])->where("id", $this->Identifier)->execute();
        } else {
            $result = Database_Model::getInstance()->update("HostFact_Invoice", ["Status" => 2, "PayDate" => "0000-00-00", "PaymentMethod" => "", "PaymentMethodID" => "", "TransactionID" => "", "Paid" => 0])->where("id", $this->Identifier)->execute();
        }
        if($result) {
            createLog("invoice", $this->Identifier, "invoice unpaid");
            if($this->Authorisation == "yes" && $this->SDDBatchID && (!isset($this->preventDirectDebitAction) || $this->preventDirectDebitAction !== true)) {
                require_once "class/directdebit.php";
                $directdebit = new directdebit();
                $directdebit->failedDirectDebitByInvoiceID($this->Identifier, "", "move_next");
            }
            Database_Model::getInstance()->update("HostFact_ExportHistory", ["Status" => "success", "Message" => ""])->where("Type", "payment_invoice")->where("ReferenceID", $this->Identifier)->where("Status", "paid_diff")->execute();
            $this->Success[] = sprintf(__("invoice not paid"), $this->InvoiceCode);
            $invoice_info = ["id" => $this->Identifier, "Debtor" => $this->Debtor, "InvoiceCode" => $this->InvoiceCode];
            do_action("invoice_is_unpaid", $invoice_info);
            return true;
        }
        return false;
    }
    public function partpayment($amountpaid, $date = "")
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for invoice");
            return false;
        }
        $amountpaid = deformat_money($amountpaid);
        $this->Status = 3;
        $this->AmountPaid = $this->AmountPaid + $amountpaid;
        $this->PartPayment = $this->PartPayment - $amountpaid;
        $this->PayDate = "";
        if(isEmptyFloat($this->PartPayment)) {
            $this->Status = 4;
            $this->PayDate = $date && is_date($date) ? $date : date("Y-m-d");
        } elseif($this->PartPayment < 0) {
            $this->Warning[] = sprintf(__("partpayment higher"), $this->InvoiceCode);
        }
        if(isEmptyFloat($this->AmountPaid)) {
            $this->Status = 2;
        }
        $result = Database_Model::getInstance()->update("HostFact_Invoice", ["Status" => $this->Status, "AmountPaid" => $this->AmountPaid, "PayDate" => $this->PayDate])->where("id", $this->Identifier)->execute();
        if($result) {
            if($date) {
                createLog("invoice", $this->Identifier, "invoice paid partly on", [money($amountpaid, true), rewrite_date_db2site($date)]);
            } else {
                createLog("invoice", $this->Identifier, "invoice paid partly", money($amountpaid, true));
            }
            if($this->Status == 4 && $this->SubStatus == "PAUSED") {
                Database_Model::getInstance()->update("HostFact_Invoice", ["SubStatus" => ""])->where("id", $this->Identifier)->execute();
                delete_stats_summary();
            }
            if($this->Status != 4) {
                $invoice_info = ["id" => $this->Identifier, "Debtor" => $this->Debtor, "InvoiceCode" => $this->InvoiceCode, "AmountPaidNow" => $amountpaid, "AmountPaidTotal" => $this->AmountPaid, "AmountOpen" => $this->AmountIncl - $this->AmountPaid];
                do_action("invoice_is_partly_paid", $invoice_info);
                $this->Success[] = sprintf(__("invoice paid partly"), $this->InvoiceCode);
            } else {
                $this->Success[] = sprintf(__("invoice paid by part payment"), $this->InvoiceCode, money($amountpaid, true));
            }
            return true;
        }
        return false;
    }
    public function removeTransactionID()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for invoice");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Invoice", ["PaymentMethod" => "", "PaymentMethodID" => "", "TransactionID" => ""])->where("id", $this->Identifier)->execute();
        if($result) {
            $this->Success[] = sprintf(__("invoice transactionid removed"), $this->InvoiceCode);
            return true;
        }
        return false;
    }
    public function removeAuthorization()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for invoice");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Invoice", ["PaymentMethod" => "", "TransactionID" => "", "Authorisation" => "no"])->where("id", $this->Identifier)->execute();
        if($result) {
            if($this->SDDBatchID) {
                require_once "class/directdebit.php";
                $directdebit = new directdebit();
                $directdebit->removeDirectDebitFromInvoiceByBatchAndInvoiceID($this->SDDBatchID, $this->Identifier);
            }
            $this->Success[] = sprintf(__("invoice authorization is removed"), $this->InvoiceCode);
            return true;
        }
        return false;
    }
    public function sent($always = true, $printnow = true, $download_instead = true)
    {
        $concept_changed = false;
        if(!empty($this->Error)) {
            return false;
        }
        require_once "class/debtor.php";
        $debtor = new debtor();
        $debtor->Identifier = $this->Debtor;
        if(!$debtor->show() || $debtor->Status == 9) {
            $this->Error[] = sprintf(__("debtor from invoice cannot be found"), $this->InvoiceCode);
            return false;
        }
        if(date("Ymd") < str_replace("-", "", substr($this->Date, 0, 10))) {
            $this->Error[] = sprintf(__("invoice has future date"), $this->InvoiceCode);
            return false;
        }
        if((int) $this->Status === 0 && $this->SubStatus == "BLOCKED") {
            $invoice_link = "[hyperlink_1]invoices.php?page=show&id=" . $this->Identifier . "[hyperlink_2]" . $this->InvoiceCode . "[hyperlink_3]";
            $unblock_link = "[hyperlink_1]invoices.php?page=show&action=unblock&id=" . $this->Identifier . "[hyperlink_2]" . __("unblock invoice link") . "[hyperlink_3]";
            $this->Warning[] = sprintf(__("invoice cannot be send, blocked"), $invoice_link, $unblock_link);
            return false;
        }
        $firsttime = $this->Status <= 2 ? true : false;
        Database_Model::getInstance()->beginTransaction();
        if($firsttime && (int) $this->Status === 0 && substr($this->InvoiceCode, 0, 9) == "[concept]") {
            $this->ConceptCode = $this->InvoiceCode;
            $this->InvoiceCode = $this->newInvoiceCode();
            if(!$this->InvoiceCode) {
                Database_Model::getInstance()->rollBack();
                return false;
            }
            $this->Date = date("Ymd");
            Database_Model::getInstance()->update("HostFact_InvoiceElements", ["InvoiceCode" => $this->InvoiceCode])->where("InvoiceCode", $this->ConceptCode)->execute();
            $result_change_invoicecode = Database_Model::getInstance()->update("HostFact_Invoice", ["InvoiceCode" => $this->InvoiceCode, "Date" => $this->Date])->where("InvoiceCode", $this->ConceptCode)->execute();
            if($result_change_invoicecode === false) {
                Database_Model::getInstance()->rollBack();
                return false;
            }
            $this->show();
            $concept_changed = true;
        } elseif($always === true && $this->Status == 1 && ($this->InvoiceMethod == 3 || $this->InvoiceMethod == 4)) {
            $always = false;
        }
        $invoice_info = ["id" => $this->Identifier, "Debtor" => $this->Debtor, "InvoiceCode" => $this->InvoiceCode];
        if(do_action("before_send_invoice", $invoice_info)) {
            $this->show();
        }
        if(SDD_ID && $this->Authorisation == "yes" && $this->SDDBatchID == "" && $this->Status < 4) {
            $first_send = $this->Status < 2;
            $this->addInvoiceToSDDBatch($this->Identifier, $first_send);
        }
        $viaMail = (int) $this->InvoiceMethod === 0 || $this->InvoiceMethod == 3 && ($always || (int) $this->Sent === 0) || $this->InvoiceMethod == 4 && ($always || (int) $this->Sent === 0) && substr($this->InvoiceCode, 0, 9) != "[concept]" ? true : false;
        $viaPost = $printnow === true && ($this->InvoiceMethod == 1 || $this->InvoiceMethod == 2 || $this->InvoiceMethod == 3 || $this->InvoiceMethod == 4) ? true : false;
        $viaMailSuccess = $viaPostSuccess = false;
        $dbAdjusted = false;
        $currentStatus = $this->Status;
        if($viaMail === true) {
            require_once "class/email.php";
            $email = new email("invoice", $this);
            $email->Recipient = $this->EmailAddress;
            if(!$email->Subject) {
                $email->Subject = sprintf(__("email subject send invoice"), $this->InvoiceCode);
            }
            $email->Debtor = $this->Debtor;
            $objects = ["invoice" => $this, "debtor" => $debtor];
            $email->add($objects);
            if($currentStatus <= 2) {
                $this->Status = $this->Paid == "1" ? 4 : 2;
            }
            $email_sent = $email->sent("invoice", $this->Identifier, false, $objects);
            if($email_sent) {
                if($currentStatus <= 2 && $this->Status == 4) {
                    Database_Model::getInstance()->update("HostFact_Invoice", ["PayDate" => ["RAW" => "CURDATE()"]])->where("id", $this->Identifier)->execute();
                    if($firsttime === true) {
                        $this->checkAuto();
                        $this->Status = 4;
                    }
                }
                $this->SentDate = rewrite_date_db2site($this->SentDate) != "-" && rewrite_date_db2site($this->SentDate) != "" && $concept_changed !== true ? $this->SentDate : date("YmdHis");
                Database_Model::getInstance()->update("HostFact_Invoice", ["Sent" => ["RAW" => "`Sent` + 1"], "SentDate" => $this->SentDate, "Status" => $this->Status])->where("id", $this->Identifier)->execute();
                $dbAdjusted = true;
                $sLogEmailAddress = check_email_address($this->EmailAddress, "convert", ", ");
                createLog("invoice", $this->Identifier, "invoice sent per email", $sLogEmailAddress);
                createMessageLog("success", "invoice sent success", [$this->InvoiceCode, $sLogEmailAddress], "invoice", $this->Identifier);
                $this->Success[] = sprintf(__("invoice sent success"), $this->InvoiceCode, $sLogEmailAddress);
                $viaMailSuccess = true;
            } elseif(0 < count($email->Error)) {
                Database_Model::getInstance()->rollBack();
                if($concept_changed === true) {
                    $this->InvoiceCode = $this->ConceptCode;
                }
                if($currentStatus <= 2) {
                    $this->Status = $currentStatus;
                }
                if(isset($email->EmailSpecificError) && $email->EmailSpecificError === true) {
                    flashmessage($email);
                } else {
                    $this->Error[] = sprintf(__("invoice sent failed"), $this->InvoiceCode, implode("", $email->Error));
                }
                createMessageLog("error", "invoice sent failed", [$this->InvoiceCode, implode("", $email->Error)], "invoice", $this->Identifier);
                return false;
            }
        }
        if($viaPost === true) {
            if(!$dbAdjusted) {
                if($this->Status <= 2) {
                    $this->Status = $this->Paid == "1" ? 4 : 2;
                    if($this->Status == 4) {
                        Database_Model::getInstance()->update("HostFact_Invoice", ["PayDate" => ["RAW" => "CURDATE()"]])->where("id", $this->Identifier)->execute();
                        if($firsttime === true) {
                            $this->checkAuto();
                            $this->Status = 4;
                        }
                    }
                }
                $this->SentDate = rewrite_date_db2site($this->SentDate) != "-" && rewrite_date_db2site($this->SentDate) != "" && $concept_changed !== true ? $this->SentDate : date("YmdHis");
                Database_Model::getInstance()->update("HostFact_Invoice", ["Sent" => ["RAW" => "`Sent` + 1"], "SentDate" => $this->SentDate, "Status" => $this->Status])->where("id", $this->Identifier)->execute();
            }
            createLog("invoice", $this->Identifier, "invoice sent per post");
            $this->Warning[] = sprintf(__("invoice sent manual"), $this->InvoiceCode);
            createMessageLog("warning", "invoice need to be sent manual", $this->InvoiceCode, "invoice", $this->Identifier);
            if($viaMail === true) {
                $this->show();
            }
            if($this->PartOfBatch === false) {
                $this->printInvoice(false, $download_instead);
            } else {
                $this->BatchName = sprintf(__("pdf filename sent in batch invoice"), rewrite_date_db2site(date("Y-m-d"))) . ".pdf";
                $this->printBatch(false, $download_instead);
            }
            $viaPostSuccess = true;
        }
        Database_Model::getInstance()->commit();
        if($viaMailSuccess || $viaPostSuccess) {
            $invoice_info = ["id" => $this->Identifier, "Debtor" => $this->Debtor, "InvoiceCode" => $this->InvoiceCode, "viaMail" => $viaMailSuccess, "viaPost" => $viaPostSuccess, "Sent" => $this->Sent + 1, "Reminders" => $this->Reminders, "Summations" => $this->Summations];
            do_action("invoice_is_sent", $invoice_info);
        }
        if(isset($concept_changed) && $concept_changed) {
            delete_stats_summary();
        }
        return true;
    }
    public function sentReminder($download_instead = true)
    {
        if($this->SubStatus != "PAUSED" && ($this->Status == 2 || $this->Status == 3) && substr(rewrite_date_site2db($this->PayBefore), 0, 8) < date("Ymd") && 0 < $this->PartPayment) {
            require_once "class/debtor.php";
            $debtor = new debtor();
            $debtor->Identifier = $this->Debtor;
            $debtor->show();
            if(isset($this->useInvoiceMethod) && $this->useInvoiceMethod == "email") {
                $this->InvoiceMethod = 0;
            } elseif(isset($this->useInvoiceMethod) && $this->useInvoiceMethod == "post") {
                $this->InvoiceMethod = 1;
            } elseif(isset($this->useInvoiceMethod) && $this->useInvoiceMethod == "both") {
                $this->InvoiceMethod = 3;
            }
            $viaMail = (int) $this->InvoiceMethod === 0 || $this->InvoiceMethod == 3 || $this->InvoiceMethod == 4 ? true : false;
            $viaPost = $this->InvoiceMethod == 1 || $this->InvoiceMethod == 2 || $this->InvoiceMethod == 3 || $this->InvoiceMethod == 4 ? true : false;
            $viaMailSuccess = $viaPostSuccess = false;
            $dbAdjusted = false;
            if($viaMail === true) {
                if(1 <= $this->Reminders && $debtor->SecondReminderTemplate == -1) {
                    $emailtemplate_id = (int) REMINDER_MAIL_SECOND === 0 ? REMINDER_MAIL : REMINDER_MAIL_SECOND;
                } elseif(1 <= $this->Reminders && 0 <= $debtor->SecondReminderTemplate) {
                    $emailtemplate_id = (int) $debtor->SecondReminderTemplate === 0 ? $debtor->ReminderTemplate : $debtor->SecondReminderTemplate;
                } else {
                    $emailtemplate_id = 0 < $debtor->ReminderTemplate ? $debtor->ReminderTemplate : REMINDER_MAIL;
                }
                require_once "class/template.php";
                $emailtemplate = new emailtemplate();
                $emailtemplate->Identifier = $emailtemplate_id;
                $emailtemplate->show();
                if(!$emailtemplate->Message) {
                    $emailtemplate->Identifier = (int) REMINDER_MAIL_SECOND === 0 ? REMINDER_MAIL : REMINDER_MAIL_SECOND;
                    $emailtemplate->show();
                }
                require_once "class/email.php";
                $email = new email("invoice", $this);
                foreach ($emailtemplate->Variables as $v) {
                    if(is_string($emailtemplate->{$v})) {
                        $email->{$v} = htmlspecialchars_decode($emailtemplate->{$v});
                    } else {
                        $email->{$v} = $emailtemplate->{$v};
                    }
                }
                $email->Recipient = !empty($debtor->ReminderEmailAddress) ? $debtor->ReminderEmailAddress : $this->EmailAddress;
                if(!$email->Subject) {
                    $email->Subject = sprintf(__("email subject reminder invoice"), $this->InvoiceCode);
                }
                $email->Debtor = $this->Debtor;
                Database_Model::getInstance()->beginTransaction();
                $objects = ["invoice" => $this, "debtor" => $debtor];
                $email->add($objects);
                if(INVOICE_REMINDER_SENT_PDF == "1") {
                    $email_sent = $email->sent("invoice", $this->Identifier, false, $objects);
                } else {
                    $email_sent = $email->sent(false, false, false, $objects);
                }
                Database_Model::getInstance()->commit();
                if($email_sent) {
                    Database_Model::getInstance()->update("HostFact_Invoice", ["Reminders" => ["RAW" => "`Reminders` + 1"], "ReminderDate" => ["RAW" => "NOW()"]])->where("id", $this->Identifier)->execute();
                    $dbAdjusted = true;
                    $sLogEmailAddress = check_email_address($email->Recipient, "convert", ", ");
                    createLog("invoice", $this->Identifier, "reminder sent per email", $sLogEmailAddress);
                    $this->Success[] = sprintf(__("reminder sent success"), $this->InvoiceCode, $sLogEmailAddress);
                    createMessageLog("success", "reminder sent success", [$this->InvoiceCode, $sLogEmailAddress], "invoice", $this->Identifier);
                    $viaMailSuccess = true;
                } elseif(!empty($email->Error)) {
                    $this->Warning[] = sprintf(__("reminder sent failed"), $this->InvoiceCode, implode("", $email->Error));
                    createMessageLog("error", "reminder sent failed", [$this->InvoiceCode, implode("", $email->Error)], "invoice", $this->Identifier);
                }
            }
            if($viaPost === true) {
                if(!$dbAdjusted) {
                    Database_Model::getInstance()->update("HostFact_Invoice", ["Reminders" => ["RAW" => "`Reminders` + 1"], "ReminderDate" => ["RAW" => "NOW()"]])->where("id", $this->Identifier)->execute();
                }
                createLog("invoice", $this->Identifier, "reminder sent per post");
                $this->Warning[] = sprintf(__("reminder sent manual"), $this->InvoiceCode);
                createMessageLog("warning", "reminder sent manual", $this->InvoiceCode, "invoice", $this->Identifier);
                if($viaMail === true) {
                    $this->show();
                }
                if($this->PartOfBatch === false) {
                    $this->PrintReminderDocument = true;
                    $this->LastOfBatch = true;
                    $this->BatchName = sprintf(__("pdf filename reminder invoice"), $this->InvoiceCode) . ".pdf";
                    $this->printBatch(false, $download_instead);
                } else {
                    $this->PrintReminderDocument = true;
                    $this->BatchName = sprintf(__("pdf filename reminders in batch invoice"), rewrite_date_db2site(date("Y-m-d"))) . ".pdf";
                    $this->printBatch(false, $download_instead);
                }
                $viaPostSuccess = true;
            }
            if($viaMailSuccess || $viaPostSuccess) {
                $invoice_info = ["id" => $this->Identifier, "Debtor" => $this->Debtor, "InvoiceCode" => $this->InvoiceCode, "viaMail" => $viaMailSuccess, "viaPost" => $viaPostSuccess, "Sent" => $this->Sent + 1, "Reminders" => $this->Reminders + 1, "Summations" => $this->Summations];
                do_action("invoice_reminder_is_sent", $invoice_info);
            }
            delete_stats_summary();
            return true;
        } else {
            if($this->SubStatus == "PAUSED") {
                $this->Error[] = sprintf(__("reminder not possible status collection"), $this->InvoiceCode);
                return false;
            }
            $this->Error[] = sprintf(__("reminder not possible"), $this->InvoiceCode);
            return false;
        }
    }
    public function sentSummation($download_instead = true)
    {
        if(INT_SUPPORT_SUMMATIONS === false) {
            $this->Error[] = __("summations not supported in this version");
            return false;
        }
        if($this->SubStatus != "PAUSED" && ($this->Status == 2 || $this->Status == 3) && INVOICE_REMINDER_NUMBER <= $this->Reminders && substr(rewrite_date_site2db($this->PayBefore), 0, 8) < date("Ymd") && 0 < $this->PartPayment) {
            require_once "class/debtor.php";
            $debtor = new debtor();
            $debtor->Identifier = $this->Debtor;
            $debtor->show();
            if(isset($this->useInvoiceMethod) && $this->useInvoiceMethod == "email") {
                $this->InvoiceMethod = 0;
            } elseif(isset($this->useInvoiceMethod) && $this->useInvoiceMethod == "post") {
                $this->InvoiceMethod = 1;
            } elseif(isset($this->useInvoiceMethod) && $this->useInvoiceMethod == "both") {
                $this->InvoiceMethod = 3;
            }
            $viaMail = (int) $this->InvoiceMethod === 0 || $this->InvoiceMethod == 3 || $this->InvoiceMethod == 4 ? true : false;
            $viaPost = $this->InvoiceMethod == 1 || $this->InvoiceMethod == 2 || $this->InvoiceMethod == 3 || $this->InvoiceMethod == 4 ? true : false;
            $viaMailSuccess = $viaPostSuccess = false;
            $dbAdjusted = false;
            if($viaMail === true) {
                $emailtemplate_id = 0 < $debtor->SummationTemplate ? $debtor->SummationTemplate : SUMMATION_MAIL;
                require_once "class/template.php";
                $emailtemplate = new emailtemplate();
                $emailtemplate->Identifier = $emailtemplate_id;
                $emailtemplate->show();
                if(!$emailtemplate->Message) {
                    $emailtemplate->Identifier = SUMMATION_MAIL;
                    $emailtemplate->show();
                }
                require_once "class/email.php";
                $email = new email("invoice", $this);
                foreach ($emailtemplate->Variables as $v) {
                    if(is_string($emailtemplate->{$v})) {
                        $email->{$v} = htmlspecialchars_decode($emailtemplate->{$v});
                    } else {
                        $email->{$v} = $emailtemplate->{$v};
                    }
                }
                $email->Recipient = !empty($debtor->ReminderEmailAddress) ? $debtor->ReminderEmailAddress : $this->EmailAddress;
                if(!$email->Subject) {
                    $email->Subject = sprintf(__("email subject summation invoice"), $this->InvoiceCode);
                }
                $email->Debtor = $this->Debtor;
                Database_Model::getInstance()->beginTransaction();
                $objects = ["invoice" => $this, "debtor" => $debtor];
                $email->add($objects);
                if(INVOICE_SUMMATION_SENT_PDF == "1") {
                    $email_sent = $email->sent("invoice", $this->Identifier, false, $objects);
                } else {
                    $email_sent = $email->sent(false, false, false, $objects);
                }
                Database_Model::getInstance()->commit();
                if($email_sent) {
                    Database_Model::getInstance()->update("HostFact_Invoice", ["Summations" => ["RAW" => "`Summations` + 1"], "SummationDate" => ["RAW" => "NOW()"]])->where("id", $this->Identifier)->execute();
                    $dbAdjusted = true;
                    $sLogEmailAddress = check_email_address($email->Recipient, "convert", ", ");
                    createLog("invoice", $this->Identifier, "summation sent per email", $sLogEmailAddress);
                    $this->Success[] = sprintf(__("summation sent success"), $this->InvoiceCode, $sLogEmailAddress);
                    createMessageLog("success", "summation sent success", [$this->InvoiceCode, $sLogEmailAddress], "invoice", $this->Identifier);
                    $viaMailSuccess = true;
                } elseif(!empty($email->Error)) {
                    $this->Warning[] = sprintf(__("summation sent failed"), $this->InvoiceCode, implode("", $email->Error));
                    createMessageLog("error", "summation sent failed", [$this->InvoiceCode, implode("", $email->Error)], "invoice", $this->Identifier);
                }
            }
            if($viaPost === true) {
                if(!$dbAdjusted) {
                    Database_Model::getInstance()->update("HostFact_Invoice", ["Summations" => ["RAW" => "`Summations` + 1"], "SummationDate" => ["RAW" => "NOW()"]])->where("id", $this->Identifier)->execute();
                }
                createLog("invoice", $this->Identifier, "summation sent per post");
                $this->Warning[] = sprintf(__("summation sent manual"), $this->InvoiceCode);
                createMessageLog("warning", "summation sent manual", $this->InvoiceCode, "invoice", $this->Identifier);
                if($viaMail === true) {
                    $this->show();
                }
                if($this->PartOfBatch === false) {
                    $this->PrintSummationDocument = true;
                    $this->LastOfBatch = true;
                    $this->BatchName = sprintf(__("pdf filename summation invoice"), $this->InvoiceCode) . ".pdf";
                    $this->printBatch(false, $download_instead);
                } else {
                    $this->PrintSummationDocument = true;
                    $this->BatchName = sprintf(__("pdf filename summations in batch invoice"), rewrite_date_db2site(date("Y-m-d"))) . ".pdf";
                    $this->printBatch(false, $download_instead);
                }
                $viaPostSuccess = true;
            }
            if($viaMailSuccess || $viaPostSuccess) {
                $invoice_info = ["id" => $this->Identifier, "Debtor" => $this->Debtor, "InvoiceCode" => $this->InvoiceCode, "viaMail" => $viaMailSuccess, "viaPost" => $viaPostSuccess, "Sent" => $this->Sent + 1, "Reminders" => $this->Reminders, "Summations" => $this->Summations + 1];
                do_action("invoice_summation_is_sent", $invoice_info);
            }
            delete_stats_summary();
            return true;
        } else {
            if($this->SubStatus == "PAUSED") {
                $this->Error[] = sprintf(__("summation not possible status collection"), $this->InvoiceCode);
                return false;
            }
            $this->Error[] = sprintf(__("summation not possible"), $this->InvoiceCode);
            return false;
        }
    }
    public function printInvoice($log = true, $download_instead = true)
    {
        require_once "class/debtor.php";
        $debtor = new debtor();
        $debtor->Identifier = $this->Debtor;
        $debtor->show();
        if($log) {
            createLog("invoice", $this->Identifier, "invoice printed");
        }
        $OutputType = "D";
        require_once "class/pdf.php";
        $template = $this->Template;
        $pdf = new pdfCreator($template, ["invoice" => $this, "debtor" => $debtor], "invoice", "D", $download_instead);
        $pdf->setOutputType($OutputType);
        if(!$pdf->generatePDF("F")) {
            $this->Error = array_merge($this->Error, $pdf->Error);
            return false;
        }
        $_SESSION["force_download"] = $pdf->Name;
        return true;
    }
    public function printInvoiceFromClientArea($invoice_id, $key)
    {
        error_reporting(E_NONE);
        $this->Identifier = $invoice_id;
        if(!$this->show()) {
            return false;
        }
        require_once "class/debtor.php";
        $debtor = new debtor();
        $debtor->Identifier = $this->Debtor;
        $debtor->show();
        $key_generated = sha1(date("d-m-Y H:i") . "a7y3hg51" . $this->Identifier . "-" . $debtor->DebtorCode . "-" . $debtor->Username);
        if($key != $key_generated) {
            return false;
        }
        $OutputType = "D";
        require_once "class/pdf.php";
        $template = $this->Template;
        $pdf = new pdfCreator($template, ["invoice" => $this, "debtor" => $debtor], "invoice", "D", true);
        $pdf->setOutputType($OutputType);
        if(!$pdf->generatePDF("F")) {
            $this->Error = array_merge($this->Error, $pdf->Error);
            return false;
        }
        createLog("invoice", $this->Identifier, "invoice printed from clientarea");
        if($pdf_file = file_get_contents("temp/" . $pdf->Name)) {
            echo json_encode(["filename" => $pdf->Name, "file" => base64_encode($pdf_file), "filesize" => filesize("temp/" . $pdf->Name)]);
            @unlink("temp/" . $pdf->Name);
            exit;
        }
        return false;
    }
    public function printBatch($logging = true, $download_instead = true)
    {
        require_once "class/debtor.php";
        $debtor = new debtor();
        $debtor->Identifier = $this->Debtor;
        $debtor->show();
        $OutputType = "D";
        require_once "class/pdf.php";
        if(isset($this->PrintReminderDocument) && $this->PrintReminderDocument) {
            if(INVOICE_REMINDER_SENT_PDF == "1" && 0 < INVOICE_REMINDER_LETTER) {
                if(!isset($this->pdf)) {
                    $this->pdf = new pdfCreator(INVOICE_REMINDER_LETTER, ["invoice" => $this, "debtor" => $debtor], "invoice", "D", $download_instead);
                } else {
                    $this->pdf->loadTemplate(INVOICE_REMINDER_LETTER);
                    $this->pdf->createPages(["invoice" => $this, "debtor" => $debtor]);
                }
                $this->show();
                $this->pdf->loadTemplate($this->Template);
                $this->pdf->createPages(["invoice" => $this, "debtor" => $debtor]);
            } elseif(0 < INVOICE_REMINDER_LETTER) {
                if(!isset($this->pdf)) {
                    $this->pdf = new pdfCreator(INVOICE_REMINDER_LETTER, ["invoice" => $this, "debtor" => $debtor], "invoice", "D", $download_instead);
                } else {
                    $this->pdf->loadTemplate(INVOICE_REMINDER_LETTER);
                    $this->pdf->createPages(["invoice" => $this, "debtor" => $debtor]);
                }
            } elseif(INVOICE_REMINDER_SENT_PDF == "1") {
                if(!isset($this->pdf)) {
                    $this->pdf = new pdfCreator($this->Template, ["invoice" => $this, "debtor" => $debtor], "invoice", "D", $download_instead);
                } else {
                    $this->pdf->loadTemplate($this->Template);
                    $this->pdf->createPages(["invoice" => $this, "debtor" => $debtor]);
                }
            }
        } elseif(isset($this->PrintSummationDocument) && $this->PrintSummationDocument) {
            if(INVOICE_SUMMATION_SENT_PDF == "1" && 0 < INVOICE_SUMMATION_LETTER) {
                if(!isset($this->pdf)) {
                    $this->pdf = new pdfCreator(INVOICE_SUMMATION_LETTER, ["invoice" => $this, "debtor" => $debtor], "invoice", "D", $download_instead);
                } else {
                    $this->pdf->loadTemplate(INVOICE_SUMMATION_LETTER);
                    $this->pdf->createPages(["invoice" => $this, "debtor" => $debtor]);
                }
                $this->show();
                $this->pdf->loadTemplate($this->Template);
                $this->pdf->createPages(["invoice" => $this, "debtor" => $debtor]);
            } elseif(0 < INVOICE_SUMMATION_LETTER) {
                if(!isset($this->pdf)) {
                    $this->pdf = new pdfCreator(INVOICE_SUMMATION_LETTER, ["invoice" => $this, "debtor" => $debtor], "invoice", "D", $download_instead);
                } else {
                    $this->pdf->loadTemplate(INVOICE_SUMMATION_LETTER);
                    $this->pdf->createPages(["invoice" => $this, "debtor" => $debtor]);
                }
            } elseif(INVOICE_SUMMATION_SENT_PDF == "1") {
                if(!isset($this->pdf)) {
                    $this->pdf = new pdfCreator($this->Template, ["invoice" => $this, "debtor" => $debtor], "invoice", "D", $download_instead);
                } else {
                    $this->pdf->loadTemplate($this->Template);
                    $this->pdf->createPages(["invoice" => $this, "debtor" => $debtor]);
                }
            }
        } else {
            if($logging) {
                createLog("invoice", $this->Identifier, "invoice printed");
            }
            if(!isset($this->pdf)) {
                $this->pdf = new pdfCreator($this->Template, ["invoice" => $this, "debtor" => $debtor], "invoice", "D", $download_instead);
            } else {
                $this->pdf->loadTemplate($this->Template);
                $this->pdf->createPages(["invoice" => $this, "debtor" => $debtor]);
            }
        }
        $this->pdf->setOutputType($OutputType);
        if($this->LastOfBatch) {
            $this->hasPrintQueue = false;
            $this->pdf->Name = isset($this->BatchName) && $this->BatchName ? $this->BatchName : __("invoices printed at") . " " . date("d-m-Y") . ".pdf";
            if(!$this->pdf->generatePDF("F")) {
                $this->Error = array_merge($this->Error, $this->pdf->Error);
                return false;
            }
            $_SESSION["force_download"] = $this->pdf->Name;
        } else {
            $this->hasPrintQueue = true;
        }
        return true;
    }
    public function downloadBatchPDF($OutputType = "D")
    {
        if(!isset($this->pdf)) {
            return false;
        }
        $this->pdf->Name = isset($this->BatchName) && $this->BatchName ? $this->BatchName : __("invoices printed at") . " " . date("d-m-Y") . ".pdf";
        if(!$this->pdf->generatePDF($OutputType)) {
            $this->Error = array_merge($this->Error, $this->pdf->Error);
            return false;
        }
    }
    public function newInvoiceCode($prefix = INVOICECODE_PREFIX, $number = INVOICECODE_NUMBER)
    {
        $prefix = parsePrefixVariables($prefix);
        $length = strlen($prefix . $number);
        $result = Database_Model::getInstance()->getOne("HostFact_Invoice", ["InvoiceCode"])->where("InvoiceCode", ["LIKE" => $prefix . "%"])->where("LENGTH(`InvoiceCode`)", [">=" => $length])->where("(SUBSTR(`InvoiceCode`,:PrefixLength)*1)", [">" => 0])->where("SUBSTR(`InvoiceCode`,:PrefixLength)", ["REGEXP" => "^[0-9]+\$"])->orderBy("(SUBSTR(`InvoiceCode`,:PrefixLength)*1)", "DESC")->bindValue("PrefixLength", strlen($prefix) + 1)->execute();
        $result2 = Database_Model::getInstance()->getOne("HostFact_InvoiceElements", ["InvoiceCode"])->where("InvoiceCode", ["LIKE" => $prefix . "%"])->where("LENGTH(`InvoiceCode`)", [">=" => $length])->where("(SUBSTR(`InvoiceCode`,:PrefixLength)*1)", [">" => 0])->where("SUBSTR(`InvoiceCode`,:PrefixLength)", ["REGEXP" => "^[0-9]+\$"])->orderBy("(SUBSTR(`InvoiceCode`,:PrefixLength)*1)", "DESC")->bindValue("PrefixLength", strlen($prefix) + 1)->execute();
        if(!$result || isset($result2->InvoiceCode) && substr($result->InvoiceCode, strlen($prefix)) < substr($result2->InvoiceCode, strlen($prefix))) {
            $result = $result2;
        }
        if(isset($result->InvoiceCode) && $result->InvoiceCode && is_numeric(substr($result->InvoiceCode, strlen($prefix)))) {
            $Code = substr($result->InvoiceCode, strlen($prefix));
            $Code = $prefix . @str_repeat("0", @max(@strlen($number) - @strlen(@max($Code + 1, $number)), 0)) . max($Code + 1, $number);
        } else {
            $Code = $prefix . $number;
        }
        if(!$this->is_free($Code)) {
            $this->Error[] = sprintf(__("invoicecode generation failed"), $Code);
        }
        return !empty($this->Error) ? false : $Code;
    }
    public function mergeConceptInvoices($aInvoiceIds = [])
    {
        $aMergeInvoices = [];
        $aInvoiceCodes = [];
        $rollbackData = [];
        if(!is_array($aInvoiceIds) || count($aInvoiceIds) <= 1) {
            return false;
        }
        foreach ($aInvoiceIds as $invoiceId) {
            if(is_numeric($invoiceId)) {
                $aMergeInvoices[] = esc($invoiceId);
            } else {
                $this->Error[] = __("invalid identifier for invoice");
                return false;
            }
        }
        $aInvoiceData = Database_Model::getInstance()->get("HostFact_Invoice", ["InvoiceCode", "Debtor", "Status", "SubStatus", "Comment", "Description"])->where("id", ["IN" => $aMergeInvoices])->execute();
        $aComment = $aDescription = [];
        foreach ($aInvoiceData as $invoiceData) {
            if(!isset($tmpdebtor)) {
                $tmpdebtor = $invoiceData->Debtor;
            } elseif($tmpdebtor != $invoiceData->Debtor) {
                $this->Error[] = __("only merge invoices with same debtor");
                return false;
            }
            if((int) $invoiceData->Status !== 0) {
                $this->Error[] = __("only merge concept invoices");
                return false;
            }
            $aInvoiceCodes[] = $invoiceData->InvoiceCode;
            if($invoiceData->SubStatus == "BLOCKED") {
                $this->SubStatus = "BLOCKED";
            }
            if($invoiceData->Comment) {
                $aComment[] = $invoiceData->Comment;
            }
            if($invoiceData->Description) {
                $aDescription[] = $invoiceData->Description;
            }
        }
        require_once "class/debtor.php";
        $debtor = new debtor();
        $debtor->Identifier = $tmpdebtor;
        $debtor->show();
        $this->InvoiceMethod = $debtor->InvoiceMethod;
        $this->Term = $debtor->InvoiceTerm !== NULL ? $debtor->InvoiceTerm : $this->Term;
        $this->Template = 0 < $debtor->InvoiceTemplate ? $debtor->InvoiceTemplate : $this->Template;
        $this->CompanyName = $debtor->InvoiceCompanyName ? htmlspecialchars_decode($debtor->InvoiceCompanyName) : htmlspecialchars_decode($debtor->CompanyName);
        $this->TaxNumber = htmlspecialchars_decode($debtor->TaxNumber);
        $this->Sex = $debtor->InvoiceSurName ? htmlspecialchars_decode($debtor->InvoiceSex) : htmlspecialchars_decode($debtor->Sex);
        $this->Initials = $debtor->InvoiceInitials ? htmlspecialchars_decode($debtor->InvoiceInitials) : htmlspecialchars_decode($debtor->Initials);
        $this->SurName = $debtor->InvoiceSurName ? htmlspecialchars_decode($debtor->InvoiceSurName) : htmlspecialchars_decode($debtor->SurName);
        $this->Address = $debtor->InvoiceAddress ? htmlspecialchars_decode($debtor->InvoiceAddress) : htmlspecialchars_decode($debtor->Address);
        $this->Address2 = $debtor->InvoiceAddress2 ? htmlspecialchars_decode($debtor->InvoiceAddress2) : htmlspecialchars_decode($debtor->Address2);
        $this->ZipCode = $debtor->InvoiceZipCode ? htmlspecialchars_decode($debtor->InvoiceZipCode) : htmlspecialchars_decode($debtor->ZipCode);
        $this->City = $debtor->InvoiceCity ? htmlspecialchars_decode($debtor->InvoiceCity) : htmlspecialchars_decode($debtor->City);
        $this->State = $debtor->InvoiceState ? htmlspecialchars_decode($debtor->InvoiceState) : htmlspecialchars_decode($debtor->State);
        $this->StateName = $debtor->InvoiceStateName ? htmlspecialchars_decode($debtor->InvoiceStateName) : htmlspecialchars_decode($debtor->StateName);
        $this->Country = $debtor->InvoiceCountry && $debtor->InvoiceAddress ? htmlspecialchars_decode($debtor->InvoiceCountry) : htmlspecialchars_decode($debtor->Country);
        $this->EmailAddress = htmlspecialchars_decode(check_email_address($debtor->InvoiceEmailAddress ? $debtor->InvoiceEmailAddress : $debtor->EmailAddress, "convert"));
        $this->Authorisation = $debtor->InvoiceAuthorisation;
        $newConceptCode = $this->newConceptCode();
        $this->InvoiceCode = $newConceptCode;
        $this->Debtor = $tmpdebtor;
        $this->Status = 0;
        if(!empty($aComment)) {
            $this->Comment = implode("\n\n", $aComment);
        }
        if(!empty($aDescription)) {
            $this->Description = implode("\n\n", $aDescription);
        }
        $aInvoiceElementData = Database_Model::getInstance()->get("HostFact_InvoiceElements", ["id", "InvoiceCode"])->where("InvoiceCode", ["IN" => $aInvoiceCodes])->where("Debtor", $tmpdebtor)->orderBy("IF(SUBSTRING(`InvoiceCode`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(`InvoiceCode`,1,1))", "ASC")->orderBy("LENGTH(`InvoiceCode`)", "ASC")->orderBy("InvoiceCode", "ASC")->orderBy("Ordering", "ASC")->orderBy("id", "ASC")->execute();
        foreach ($aInvoiceElementData as $invoiceElementData) {
            $rollbackData[$invoiceElementData->id] = $invoiceElementData->InvoiceCode;
        }
        Database_Model::getInstance()->update("HostFact_InvoiceElements", ["InvoiceCode" => $newConceptCode])->where("Debtor", $tmpdebtor)->where("InvoiceCode", ["IN" => $rollbackData])->execute();
        if($this->add()) {
            $new_invoice_id = $this->Identifier;
            foreach ($aMergeInvoices as $key) {
                $this->Identifier = esc($key);
                $this->show();
                require_once "class/attachment.php";
                $attachments = new attachment();
                $attachments->changeAttachmentReference("invoice", $this->Identifier, $new_invoice_id);
                $this->delete(true);
            }
            $rollbackData = array_keys($rollbackData);
            foreach ($rollbackData as $order_id => $element_id) {
                Database_Model::getInstance()->update("HostFact_InvoiceElements", ["Ordering" => $order_id])->where("id", $element_id)->execute();
            }
            if(empty($this->Error)) {
                $this->Success = [];
                $this->Success[] = sprintf(__("invoices successfully merged"), $newConceptCode);
            }
        } else {
            foreach ($rollbackData as $id => $invoiceCode) {
                Database_Model::getInstance()->update("HostFact_InvoiceElements", ["InvoiceCode" => $invoiceCode])->where("id", $id)->execute();
            }
        }
    }
    public function newConceptCode()
    {
        if(INVOICECODE_CONCEPT == "no") {
            return $this->newInvoiceCode();
        }
        $prefix = "[concept]";
        $number = "0001";
        $length = strlen($prefix . $number);
        $result = Database_Model::getInstance()->getOne("HostFact_Invoice", ["InvoiceCode"])->where("InvoiceCode", ["LIKE" => $prefix . "%"])->where("LENGTH(`InvoiceCode`)", [">=" => $length])->where("(SUBSTR(`InvoiceCode`,:PrefixLength)*1)", [">" => 0])->orderBy("(SUBSTR(`InvoiceCode`,:PrefixLength)*1)", "DESC")->bindValue("PrefixLength", strlen($prefix) + 1)->execute();
        $result2 = Database_Model::getInstance()->getOne("HostFact_InvoiceElements", ["InvoiceCode"])->where("InvoiceCode", ["LIKE" => $prefix . "%"])->where("LENGTH(`InvoiceCode`)", [">=" => $length])->where("(SUBSTR(`InvoiceCode`,:PrefixLength)*1)", [">" => 0])->orderBy("(SUBSTR(`InvoiceCode`,:PrefixLength)*1)", "DESC")->bindValue("PrefixLength", strlen($prefix) + 1)->execute();
        if(!$result || isset($result2->InvoiceCode) && substr($result->InvoiceCode, strlen($prefix)) < substr($result2->InvoiceCode, strlen($prefix))) {
            $result = $result2;
        }
        if(isset($result->InvoiceCode) && $result->InvoiceCode && is_numeric(substr($result->InvoiceCode, strlen($prefix)))) {
            $Code = substr($result->InvoiceCode, strlen($prefix));
            $Code = $prefix . @str_repeat("0", @max(@strlen($number) - @strlen(@max($Code + 1, $number)), 0)) . max($Code + 1, $number);
        } else {
            $Code = $prefix . $number;
        }
        if(!$this->is_free($Code)) {
            $this->Error[] = sprintf(__("invoicecode generation failed"), $Code);
        }
        return !empty($this->Error) ? false : $Code;
    }
    public function validate()
    {
        if(!$this->is_free($this->InvoiceCode)) {
            $this->Error[] = __("invalid invoicecode");
            return false;
        }
        if(!trim($this->CompanyName) && !trim($this->SurName)) {
            $this->Error[] = __("no companyname and no surname are given");
        }
        if(0 < $this->Status && substr($this->InvoiceCode, 0, 9) == "[concept]") {
            $this->Error[] = __("conceptcode status");
        }
        if(!is_numeric($this->Status) || !in_array($this->Status, [0, 1, 2, 3, 4, 8, 9])) {
            $this->Error[] = __("invalid status");
        }
        if($this->SubStatus) {
            if(strtoupper($this->SubStatus) == "BLOCKED" && (int) $this->Status === 0) {
                $this->SubStatus = "BLOCKED";
            } elseif(strtoupper($this->SubStatus) == "PAUSED" && in_array($this->Status, [2, 3])) {
                $this->SubStatus = "PAUSED";
            } else {
                $this->SubStatus = "";
            }
        }
        if(!is_numeric($this->Debtor) || empty($this->Debtor)) {
            $this->Error[] = __("invalid debtor");
            return false;
        }
        if(!is_date(rewrite_date_site2db($this->Date))) {
            $this->Error[] = __("invalid date");
        }
        if(!is_numeric($this->Term)) {
            $this->Error[] = __("invalid term");
        }
        if(!is_numeric($this->Discount) || $this->Discount < 0 || 1 < $this->Discount) {
            $this->Error[] = __("invalid discount");
        }
        if($this->Discount && 2 < strlen(substr(strrchr($this->Discount * 100, "."), 1))) {
            $this->Error[] = __("invalid discount digits");
        }
        if(!in_array($this->IgnoreDiscount, [0, 1])) {
            $this->IgnoreDiscount = 0;
        }
        if(!(is_string($this->CompanyName) && strlen($this->CompanyName) <= 100 || strlen($this->CompanyName) === 0)) {
            $this->Error[] = __("invalid companyname");
        }
        if($this->Sex && !in_array($this->Sex, settings::GENDER_AVAILABLE_OPTIONS)) {
            $this->Error[] = __("invalid sex");
        } elseif(!$this->Sex) {
            $this->Sex = "m";
        }
        if(!(is_string($this->Initials) && strlen($this->Initials) <= 25 || strlen($this->Initials) === 0)) {
            $this->Error[] = __("invalid initials");
        }
        if(!(is_string($this->SurName) && strlen($this->SurName) <= 111 || strlen($this->SurName) === 0)) {
            $this->Error[] = __("invalid surname");
        }
        if(!(is_string($this->Address) && strlen($this->Address) <= 100 || strlen($this->Address) === 0)) {
            $this->Error[] = __("invalid address");
        }
        if(!(is_string($this->Address2) && strlen($this->Address2) <= 100 || strlen($this->Address2) === 0)) {
            $this->Error[] = __("invalid address");
        }
        if(!(is_string($this->ZipCode) && strlen($this->ZipCode) <= 10 || strlen($this->ZipCode) === 0)) {
            $this->Error[] = __("invalid zipcode");
        }
        if(!(is_string($this->City) && strlen($this->City) <= 100 || strlen($this->City) === 0)) {
            $this->Error[] = __("invalid city");
        }
        if(!(is_string($this->State) && strlen($this->State) <= 100 || strlen($this->State) === 0)) {
            $this->Error[] = __("invalid state");
        }
        if(!(is_string($this->Country) && strlen($this->Country) <= 10 || strlen($this->Country) === 0)) {
            $this->Error[] = __("invalid country");
        }
        if(!(check_email_address($this->EmailAddress) || strlen($this->EmailAddress) === 0 || 255 < strlen($this->EmailAddress))) {
            $this->Warning[] = __("invalid emailaddress");
        }
        global $array_invoicemethod;
        if(!in_array($this->InvoiceMethod, array_keys($array_invoicemethod))) {
            $this->Error[] = __("invalid invoicemethod");
        }
        if(!in_array($this->Authorisation, ["yes", "no"])) {
            $this->Error[] = __("invalid authentication");
        } elseif($this->Authorisation == "yes" && $this->Status == 8) {
            $this->Authorisation = "no";
        }
        if(!is_numeric($this->Template)) {
            $this->Error[] = __("invalid template");
        } else {
            require_once "class/template.php";
            $template = new template();
            $fields = ["Name"];
            $templates = $template->all($fields, "", "", "", "Type", "invoice");
            if(!isset($templates[$this->Template])) {
                $this->Template = $template->getStandard("invoice");
            }
        }
        if(!is_numeric($this->Reminders)) {
            $this->Error[] = __("invalid reminders");
        }
        if(!is_numeric($this->Summations)) {
            $this->Error[] = __("invalid summations");
        }
        if(!is_numeric($this->AmountExcl)) {
            $this->Error[] = __("invalid amountexcl");
        }
        if(!is_numeric($this->AmountIncl)) {
            $this->Error[] = __("invalid amountincl");
        }
        if(count($this->Elements) === 0 && empty($this->Error)) {
            $this->Error[] = sprintf(__("no invoice elements"), $this->InvoiceCode);
        }
        if(!is_numeric($this->TaxRate) || $this->TaxRate < 0 || 1 < $this->TaxRate) {
            $this->Error[] = __("invalid taxrate");
        }
        if(strlen($this->Compound) && !in_array($this->Compound, ["yes", "no"])) {
            $this->Error[] = __("invalid compound");
        } elseif(!strlen($this->Compound)) {
            $this->Compound = "no";
        }
        if(!in_array($this->VatCalcMethod, ["incl", "excl"])) {
            $this->Error[] = __("invalid vatcalcmethod");
        }
        if(!(is_string($this->Coupon) && strlen($this->Coupon) <= 50 || strlen($this->Coupon) === 0)) {
            $this->Error[] = __("invalid coupon");
        }
        if(!(is_string($this->ReferenceNumber) && strlen($this->ReferenceNumber) <= 255 || strlen($this->ReferenceNumber) === 0)) {
            $this->Error[] = __("invalid referencenumber");
        }
        if(strlen($this->SentDate) && !is_date(rewrite_date_site2db($this->SentDate))) {
            $this->Error[] = __("invalid sentdate");
        }
        if(strlen($this->ReminderDate) && $this->ReminderDate != "0000-00-00" && !is_date(rewrite_date_site2db($this->ReminderDate))) {
            $this->Error[] = __("invalid reminderdate");
        }
        if(strlen($this->SummationDate) && $this->SummationDate != "0000-00-00" && !is_date(rewrite_date_site2db($this->SummationDate))) {
            $this->Error[] = __("invalid summationdate");
        }
        if(strlen($this->AmountPaid) && !is_numeric($this->AmountPaid)) {
            $this->Error[] = __("invalid amount paid");
        }
        global $array_paymentmethod;
        if($this->PaymentMethod && !array_key_exists($this->PaymentMethod, $array_paymentmethod)) {
            $this->Error[] = __("invalid PaymentMethod");
        }
        if(strlen($this->PaymentMethodID) && !is_numeric($this->PaymentMethodID)) {
            $this->Error[] = __("invalid PaymentMethodID");
        }
        if(strlen($this->Paid) && !is_numeric($this->Paid)) {
            $this->Error[] = __("invalid paid");
        }
        if(strlen($this->PayDate) && !is_date(rewrite_date_site2db($this->PayDate))) {
            $this->Error[] = __("invalid paydate");
        }
        if(!(is_string($this->TransactionID) && strlen($this->TransactionID) <= 50 || strlen($this->TransactionID) === 0)) {
            $this->Error[] = __("invalid transactionID");
        }
        if(!(is_string($this->Comment) && strlen($this->Comment) <= 21845 || strlen($this->Comment) === 0)) {
            $this->Error[] = __("invalid Comment");
        }
        if(!(is_string($this->Description) && strlen($this->Description) <= 21845 || strlen($this->Description) === 0)) {
            $this->Error[] = __("invalid description");
        }
        if(!empty($this->customfields_list)) {
            foreach ($this->customfields_list as $custom_field) {
                switch ($custom_field["LabelType"]) {
                    case "date":
                        $this->customvalues[$custom_field["FieldCode"]] = isset($this->customvalues[$custom_field["FieldCode"]]) && rewrite_date_site2db($this->customvalues[$custom_field["FieldCode"]]) ? date("Y-m-d", strtotime(rewrite_date_site2db($this->customvalues[$custom_field["FieldCode"]]))) : "";
                        break;
                    case "checkbox":
                        $this->customvalues[$custom_field["FieldCode"]] = is_array($this->customvalues[$custom_field["FieldCode"]]) ? json_encode($this->customvalues[$custom_field["FieldCode"]]) : "";
                        break;
                    default:
                        if($custom_field["Regex"] && !@preg_match($custom_field["Regex"], $this->customvalues[$custom_field["FieldCode"]])) {
                            $this->Error[] = sprintf(__("custom client fields regex"), $custom_field["LabelTitle"]);
                        }
                }
            }
        }
        if(!empty($this->Error)) {
            return false;
        }
        if(($this->InvoiceMethod == "0" || $this->InvoiceMethod == "3") && !$this->EmailAddress) {
            global $array_invoicemethod;
            $this->Warning[] = sprintf(__("invoicemethod changed because mailaddress is unknown"), $array_invoicemethod[$this->InvoiceMethod], $array_invoicemethod[1]);
            $this->InvoiceMethod = 1;
        }
        return true;
    }
    public function is_free($InvoiceCode)
    {
        if($InvoiceCode) {
            $result = Database_Model::getInstance()->getOne("HostFact_Invoice", ["id"])->where("InvoiceCode", $InvoiceCode)->execute();
            if(!$result || $result->id == $this->Identifier) {
                return true;
            }
            return false;
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
        $DebtorArray = ["DebtorCode", "AccountNumber", "AccountCity", "PhoneNumber", "MobileNumber"];
        $DebtorFields = 0 < count(array_intersect($DebtorArray, $fields)) ? true : false;
        $ElementArray = ["Description", "PeriodicID"];
        $ElementFields = 0 < count(array_intersect($ElementArray, $fields)) ? true : false;
        $search_at = [];
        $DebtorSearch = $ElementSearch = false;
        if($searchat && (0 < strlen($searchfor) || $searchfor)) {
            $search_at = explode("|", $searchat);
            $DebtorSearch = 0 < count(array_intersect($DebtorArray, $search_at)) ? true : false;
            $ElementSearch = 0 < count(array_intersect($ElementArray, $search_at)) ? true : false;
        }
        if(in_array("CustomFieldValue", $search_at)) {
            $this->CustomFields = new customfields();
            $item_ids = $this->CustomFields->searchCustomFieldsByValue($searchfor, "invoice");
        }
        $select = ["HostFact_Invoice.id", "DATE_ADD(HostFact_Invoice.`Date`,INTERVAL HostFact_Invoice.`Term` DAY) as `PayBefore`", "DATEDIFF(CURDATE(), DATE_ADD(HostFact_Invoice.`Date`,INTERVAL HostFact_Invoice.`Term` DAY)) AS `DaysDue`"];
        foreach ($fields as $column) {
            if(in_array($column, $DebtorArray)) {
                $select[] = "HostFact_Debtors.`" . $column . "`";
            } elseif(in_array($column, $ElementArray)) {
                $select[] = "HostFact_InvoiceElements.`" . $column . "`";
            } elseif($column == "PaymentURL") {
                $select[] = "MD5(CONCAT(HostFact_Invoice.`id`, HostFact_Invoice.`InvoiceCode`, HostFact_Invoice.`Date`, HostFact_Invoice.`AmountIncl`)) as `PaymentURL`";
            } else {
                $select[] = "HostFact_Invoice.`" . $column . "`";
            }
        }
        if($ElementFields || $ElementSearch) {
            Database_Model::getInstance()->get(["HostFact_Invoice", "HostFact_InvoiceElements"], $select)->where("HostFact_InvoiceElements.`InvoiceCode`=HostFact_Invoice.`InvoiceCode`")->groupBy("HostFact_Invoice.`InvoiceCode`");
        } else {
            Database_Model::getInstance()->get("HostFact_Invoice", $select);
        }
        if($DebtorFields || $DebtorSearch) {
            Database_Model::getInstance()->join("HostFact_Debtors", "HostFact_Debtors.`id` = HostFact_Invoice.`Debtor`");
        }
        if(!empty($search_at)) {
            $or_clausule = [];
            foreach ($search_at as $searchColumn) {
                if(in_array($searchColumn, ["Debtor"])) {
                    $or_clausule[] = ["HostFact_Invoice.`" . $searchColumn . "`", $searchfor];
                } elseif(in_array($searchColumn, ["Sent"])) {
                    $or_clausule[] = ["AND" => [["HostFact_Invoice.Sent", $searchfor], ["OR" => [["HostFact_Invoice.Status", ["!=" => 8]], ["DATE_ADD(HostFact_Invoice.`Date`, INTERVAL :credit_show_not_sent DAY)", [">=" => date("Y-m-d 00:00:00")]]]]]];
                    Database_Model::getInstance()->bindValue("credit_show_not_sent", CREDIT_SHOW_NOT_SENT);
                } elseif(in_array($searchColumn, $DebtorArray)) {
                    $or_clausule[] = ["HostFact_Debtors.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                } elseif(in_array($searchColumn, ["PeriodicID"])) {
                    $or_clausule[] = ["HostFact_InvoiceElements.`" . $searchColumn . "`", $searchfor];
                } elseif(in_array($searchColumn, $ElementArray)) {
                    $or_clausule[] = ["HostFact_InvoiceElements.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                } elseif($searchColumn == "CustomFieldValue") {
                    if($item_ids && is_array($item_ids) && 0 < count($item_ids)) {
                        $or_clausule[] = ["HostFact_Invoice.`id`", ["IN" => $item_ids]];
                    }
                } else {
                    $or_clausule[] = ["HostFact_Invoice.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                }
            }
            Database_Model::getInstance()->orWhere($or_clausule);
        }
        $order = $sort ? $order ? $order : "DESC" : "";
        if($sort == "Debtor") {
            Database_Model::getInstance()->orderBy("CONCAT(HostFact_Debtors.`CompanyName`, HostFact_Debtors.`SurName`)", $order);
        } elseif(in_array($sort, $DebtorArray)) {
            Database_Model::getInstance()->orderBy("HostFact_Debtors.`" . $sort . "`", $order);
        } elseif(in_array($sort, $ElementArray)) {
            Database_Model::getInstance()->orderBy("HostFact_InvoiceElements.`" . $sort . "`", $order);
        } elseif($sort == "PayBefore") {
            Database_Model::getInstance()->orderBy("PayBefore", $order);
        } elseif($sort == "InvoiceCode") {
            $order = $order ? $order : "DESC";
            Database_Model::getInstance()->orderBy("IF(SUBSTRING(HostFact_Invoice.`InvoiceCode`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(HostFact_Invoice.`InvoiceCode`,1,1))", $order)->orderBy("LENGTH(HostFact_Invoice.`InvoiceCode`)", $order)->orderBy("HostFact_Invoice.`InvoiceCode`", $order);
        } elseif($sort == "Date` ASC, `InvoiceCode") {
            Database_Model::getInstance()->orderBy("HostFact_Invoice.`Date`", "ASC")->orderBy("IF(SUBSTRING(HostFact_Invoice.`InvoiceCode`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(HostFact_Invoice.`InvoiceCode`,1,1))", "ASC")->orderBy("LENGTH(HostFact_Invoice.`InvoiceCode`)", "ASC")->orderBy("HostFact_Invoice.`InvoiceCode`", "ASC");
        } elseif($sort == "Date` DESC, `InvoiceCode") {
            Database_Model::getInstance()->orderBy("HostFact_Invoice.`Date`", "DESC")->orderBy("IF(SUBSTRING(HostFact_Invoice.`InvoiceCode`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(HostFact_Invoice.`InvoiceCode`,1,1))", "DESC")->orderBy("LENGTH(HostFact_Invoice.`InvoiceCode`)", "DESC")->orderBy("HostFact_Invoice.`InvoiceCode`", "DESC");
        } elseif($sort == "Date") {
            Database_Model::getInstance()->orderBy("CASE HostFact_Invoice.`Status` WHEN '0' THEN (CASE HostFact_Invoice.`SentDate` WHEN '0000-00-00 00:00:00' THEN CURDATE() ELSE HostFact_Invoice.`SentDate` END) ELSE HostFact_Invoice.`Date` END", $order);
        } elseif($sort) {
            Database_Model::getInstance()->orderBy("HostFact_Invoice." . $sort, $order);
        } else {
            $order = $order ? $order : "DESC";
            Database_Model::getInstance()->orderBy("IF(SUBSTRING(HostFact_Invoice.`InvoiceCode`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(HostFact_Invoice.`InvoiceCode`,1,1))", $order)->orderBy("LENGTH(HostFact_Invoice.`InvoiceCode`)", $order)->orderBy("HostFact_Invoice.`InvoiceCode`", $order);
        }
        if(0 <= $limit) {
            Database_Model::getInstance()->limit((max(1, $limit) - 1) * $show_results, $show_results);
        }
        if($group == "substatus_paused") {
            Database_Model::getInstance()->where("HostFact_Invoice.SubStatus", "PAUSED");
        } elseif($group == "reminders") {
            Database_Model::getInstance()->where("HostFact_Invoice.Status", ["IN" => [2, 3]])->where("HostFact_Invoice.SubStatus", ["!=" => "PAUSED"])->orWhere([["AND" => [["HostFact_Invoice.Authorisation", ["!=" => "yes"]], ["HostFact_Invoice.Reminders", 0], ["DATE_ADD(HostFact_Invoice.`Date`, INTERVAL HostFact_Invoice.`Term` DAY)", ["<" => ["RAW" => "CURDATE()"]]]]], ["AND" => [["HostFact_Invoice.Authorisation", ["!=" => "yes"]], ["HostFact_Invoice.Reminders", [">" => 0]], ["DATE_ADD(HostFact_Invoice.`Date`, INTERVAL HostFact_Invoice.`Term` DAY)", ["<" => ["RAW" => "CURDATE()"]]], ["HostFact_Invoice.Reminders", ["<" => INVOICE_REMINDER_NUMBER]], ["DATE_ADD(HostFact_Invoice.`ReminderDate`, INTERVAL :invoice_reminder_term DAY)", ["<" => ["RAW" => "CURDATE()"]]]]]])->bindValue("invoice_reminder_term", INVOICE_REMINDER_TERM);
        } elseif($group == "summations") {
            Database_Model::getInstance()->where("HostFact_Invoice.Status", ["IN" => [2, 3]])->where("HostFact_Invoice.SubStatus", ["!=" => "PAUSED"])->orWhere([["AND" => [["HostFact_Invoice.Authorisation", ["!=" => "yes"]], ["HostFact_Invoice.Summations", 0], ["HostFact_Invoice.Reminders", [">=" => INVOICE_REMINDER_NUMBER]], ["DATE_ADD(HostFact_Invoice.`Date`, INTERVAL HostFact_Invoice.`Term` DAY)", ["<" => ["RAW" => "CURDATE()"]]], ["HostFact_Invoice.Summations", ["<" => INVOICE_SUMMATION_NUMBER]], ["DATE_ADD(HostFact_Invoice.`ReminderDate`, INTERVAL :invoice_reminder_term DAY)", ["<" => ["RAW" => "CURDATE()"]]]]], ["AND" => [["HostFact_Invoice.Authorisation", ["!=" => "yes"]], ["HostFact_Invoice.Summations", [">" => 0]], ["HostFact_Invoice.Reminders", [">=" => INVOICE_REMINDER_NUMBER]], ["DATE_ADD(HostFact_Invoice.`Date`, INTERVAL HostFact_Invoice.`Term` DAY)", ["<" => ["RAW" => "CURDATE()"]]], ["HostFact_Invoice.Summations", ["<" => INVOICE_SUMMATION_NUMBER]], ["DATE_ADD(HostFact_Invoice.`SummationDate`, INTERVAL :invoice_summation_term DAY)", ["<" => ["RAW" => "CURDATE()"]]]]]])->bindValue("invoice_reminder_term", INVOICE_REMINDER_TERM)->bindValue("invoice_summation_term", INVOICE_SUMMATION_TERM);
        } elseif($group == "draft_scheduled") {
            Database_Model::getInstance()->where("HostFact_Invoice.Status", "0")->where("HostFact_Invoice.SentDate", ["!=" => "0000-00-00 00:00:00"]);
        } elseif($group == "draft_scheduled_sendable") {
            Database_Model::getInstance()->where("HostFact_Invoice.Status", "0")->where("HostFact_Invoice.SubStatus", ["!=" => "PAUSED"])->where("HostFact_Invoice.SentDate", ["!=" => "0000-00-00 00:00:00"])->where("HostFact_Invoice.SentDate", ["<=" => date("Y-m-d H:i:s")]);
        } elseif(is_string($group) && 1 < count(explode("|", $group))) {
            $group = explode("|", $group);
            $or_clausule = [];
            foreach ($group as $status) {
                $or_clausule[] = ["HostFact_Invoice.`Status`", $status];
            }
            Database_Model::getInstance()->orWhere($or_clausule);
        } elseif(is_numeric($group)) {
            Database_Model::getInstance()->where("HostFact_Invoice.`Status`", $group);
        } else {
            Database_Model::getInstance()->where("HostFact_Invoice.`Status`", ["<=" => 9]);
        }
        if(isset($this->BeginDate) && isset($this->EndDate)) {
            Database_Model::getInstance()->where("HostFact_Invoice.Date", ["BETWEEN" => [$this->BeginDate, $this->EndDate]]);
        }
        if(!empty($filters)) {
            foreach ($filters as $_db_field => $_db_value) {
                switch ($_db_field) {
                    case "debtor":
                        if(is_numeric($_db_value) && 0 < $_db_value) {
                            Database_Model::getInstance()->where("HostFact_Invoice.Debtor", $_db_value);
                        }
                        break;
                    case "created":
                        if(isset($_db_value["from"]) && $_db_value["from"]) {
                            Database_Model::getInstance()->where("HostFact_Invoice.Created", [">=" => $_db_value["from"]]);
                        }
                        if(isset($_db_value["to"]) && $_db_value["to"]) {
                            Database_Model::getInstance()->where("HostFact_Invoice.Created", ["<=" => $_db_value["to"]]);
                        }
                        break;
                    case "modified":
                        if(isset($_db_value["from"]) && $_db_value["from"]) {
                            Database_Model::getInstance()->where("HostFact_Invoice.Modified", [">=" => $_db_value["from"]]);
                        }
                        if(isset($_db_value["to"]) && $_db_value["to"]) {
                            Database_Model::getInstance()->where("HostFact_Invoice.Modified", ["<=" => $_db_value["to"]]);
                        }
                        break;
                    case "date":
                        if(isset($_db_value["from"]) && $_db_value["from"]) {
                            Database_Model::getInstance()->where("HostFact_Invoice.Date", [">=" => $_db_value["from"]]);
                        }
                        if(isset($_db_value["to"]) && $_db_value["to"]) {
                            Database_Model::getInstance()->where("HostFact_Invoice.Date", ["<=" => $_db_value["to"]]);
                        }
                        break;
                    case "paybefore":
                        if(isset($_db_value["from"]) && $_db_value["from"]) {
                            Database_Model::getInstance()->where("DATE_ADD(HostFact_Invoice.`Date`,INTERVAL HostFact_Invoice.`Term` DAY)", [">=" => $_db_value["from"]]);
                        }
                        if(isset($_db_value["to"]) && $_db_value["to"]) {
                            Database_Model::getInstance()->where("DATE_ADD(HostFact_Invoice.`Date`,INTERVAL HostFact_Invoice.`Term` DAY)", ["<=" => $_db_value["to"]]);
                        }
                        break;
                    case "paydate":
                        if(isset($_db_value["from"]) && $_db_value["from"]) {
                            Database_Model::getInstance()->where("HostFact_Invoice.PayDate", [">=" => $_db_value["from"]]);
                        }
                        if(isset($_db_value["to"]) && $_db_value["to"]) {
                            Database_Model::getInstance()->where("HostFact_Invoice.PayDate", ["<=" => $_db_value["to"]]);
                        }
                        break;
                }
            }
        }
        $list = [];
        $list["TotalAmountIncl"] = 0;
        $list["TotalAmountExcl"] = 0;
        $list["CountRows"] = 0;
        $this->CountRows = 0;
        if($ElementFields || $ElementSearch) {
            $list["CountRows"] = Database_Model::getInstance()->rowCountWithIDOptimalisation(["HostFact_Invoice", "HostFact_InvoiceElements"], "HostFact_Invoice.id", "HostFact_Invoice.id");
            $this->CountRows = $list["CountRows"];
        } else {
            $list["CountRows"] = Database_Model::getInstance()->rowCountWithIDOptimalisation("HostFact_Invoice", "HostFact_Invoice.id", "HostFact_Invoice.id");
            $this->CountRows = $list["CountRows"];
        }
        if($invoice_list = Database_Model::getInstance()->execute()) {
            foreach ($invoice_list as $result) {
                $list[$result->id] = ["id" => $result->id];
                foreach ($fields as $column) {
                    $list[$result->id][$column] = htmlspecialchars($result->{$column});
                }
                $list[$result->id]["PayBefore"] = htmlspecialchars($result->PayBefore);
                $list[$result->id]["DaysDue"] = htmlspecialchars($result->DaysDue);
                $list[$result->id]["ReminderDays"] = isset($result->ReminderDays) ? htmlspecialchars($result->ReminderDays) : "";
                $list[$result->id]["LastReminderDays"] = isset($result->LastReminderDays) ? htmlspecialchars($result->LastReminderDays) : "";
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
        }
        if(isset($this->page_total_method) && $this->page_total_method == "all_results") {
            $grouped_result = Database_Model::getInstance()->getGroupedData("HostFact_Invoice", ["SUM(`HostFact_Invoice`.`AmountExcl`) as TotalAmountExcl", "SUM(`HostFact_Invoice`.`AmountIncl`) as TotalAmountIncl"]);
            $list["TotalAmountExcl"] = $grouped_result->TotalAmountExcl;
            $list["TotalAmountIncl"] = $grouped_result->TotalAmountIncl;
        } elseif(isset($this->page_total_method) && $this->page_total_method == "all_results_open_amount") {
            $grouped_result = Database_Model::getInstance()->getGroupedData("HostFact_Invoice", ["SUM(`HostFact_Invoice`.`AmountIncl` - `HostFact_Invoice`.`AmountPaid`) as OpenAmountIncl"]);
            $list["OpenAmountIncl"] = isset($grouped_result->OpenAmountIncl) ? $grouped_result->OpenAmountIncl : 0;
        }
        return $list;
    }
    public function ReceivedPaymentMail()
    {
        require_once "class/debtor.php";
        $debtor = new debtor();
        $debtor->Identifier = $this->Debtor;
        $debtor->show();
        $send_payment_email_notification = false;
        $Payment_Mail = (int) $debtor->PaymentMailTemplate === 0 ? PAYMENT_MAIL : $debtor->PaymentMailTemplate;
        $Payment_Mail_When = explode("|", $debtor->PaymentMail == -1 ? PAYMENT_MAIL_WHEN : $debtor->PaymentMail);
        foreach ($Payment_Mail_When as $condition) {
            switch ($condition) {
                case "auth":
                    if($this->Authorisation == "yes") {
                        $send_payment_email_notification = true;
                    }
                    break;
                case "wire":
                    if($this->Paid == "0" && $this->Authorisation != "yes") {
                        $send_payment_email_notification = true;
                    }
                    break;
                case "order":
                    if($this->Paid == "1" && $this->Authorisation != "yes") {
                        $send_payment_email_notification = true;
                    }
                    break;
                case "paid":
                    if($this->Paid == "2" && $this->Authorisation != "yes") {
                        $send_payment_email_notification = true;
                    }
                    break;
                default:
                    if($send_payment_email_notification === true) {
                        if(0 < $Payment_Mail && $send_payment_email_notification === true) {
                            require_once "class/template.php";
                            $emailtemplate = new emailtemplate();
                            $emailtemplate->Identifier = $Payment_Mail;
                            $emailtemplate->show();
                            $this->show();
                            if(!$this->EmailAddress) {
                                return false;
                            }
                            require_once "class/email.php";
                            $email = new email("invoice", $this);
                            foreach ($emailtemplate->Variables as $v) {
                                if(is_string($emailtemplate->{$v})) {
                                    $email->{$v} = htmlspecialchars_decode($emailtemplate->{$v});
                                } else {
                                    $email->{$v} = $emailtemplate->{$v};
                                }
                            }
                            $email->Recipient = $this->EmailAddress;
                            if(!$email->Subject) {
                                $email->Subject = sprintf(__("subject payment received mail"), $this->InvoiceCode);
                            }
                            $email->Debtor = $this->Debtor;
                            $email->Attachment = implode("|", $email->Attachment);
                            $email->add(["debtor" => $debtor, "invoice" => $this]);
                            $id = $this->Identifier;
                            $this->Identifier = 0;
                            if(!$email->sent("", false, false, ["debtor" => $debtor, "invoice" => $this])) {
                                $this->Error[] = sprintf(__("invoice sent payment mail failed"), $this->InvoiceCode, implode("", $email->Error));
                                createMessageLog("error", "invoice sent payment mail failed", [$this->InvoiceCode, implode("", $email->Error)], "invoice", $this->Identifier);
                                $this->Identifier = $id;
                                return false;
                            }
                            $this->Identifier = $id;
                            createLog("invoice", $this->Identifier, "invoice payment notification");
                            $this->Success[] = sprintf(__("payment notification sent by email"), $this->InvoiceCode, $this->EmailAddress);
                            return true;
                        }
                    }
            }
        }
    }
    public function sentinvoices($printnow = false)
    {
        $fields = ["InvoiceCode", "Status", "InvoiceMethod", "Sent", "Date", "DebtorCode"];
        $invoices = $this->all($fields, "", "", -1, "Sent", "0", "1|8");
        $inv_counter = 0;
        foreach ($invoices as $key => $value) {
            if(is_numeric($key)) {
                if(100 <= $inv_counter) {
                    if(isset($_SESSION["flashMessage"]["Error"]) && is_array($_SESSION["flashMessage"]["Error"])) {
                        $this->Error = array_merge($this->Error, $_SESSION["flashMessage"]["Error"]);
                        unset($_SESSION["flashMessage"]["Error"]);
                    }
                } elseif($value["Status"] == 1 && (int) $value["Sent"] === 0 && ((int) $value["InvoiceMethod"] === 0 || $value["InvoiceMethod"] == "3" || $value["InvoiceMethod"] == "4")) {
                    $this->Identifier = $key;
                    $this->show();
                    $this->sent(true, $printnow);
                    $inv_counter++;
                    if($value["InvoiceMethod"] == "3" || $value["InvoiceMethod"] == "4") {
                        $result = Database_Model::getInstance()->update("HostFact_Invoice", ["Status" => "1"])->where("id", $this->Identifier)->execute();
                    }
                }
            }
        }
    }
    public function checkAuto()
    {
        $this->ReceivedPaymentMail();
        if(is_array($this->Elements)) {
            require_once "class/automation.php";
            $automation = new automation();
            $automation->show();
            foreach ($this->Elements as $k => $v) {
                if(is_numeric($k) && 0 < $v["PeriodicID"]) {
                    $pID = $v["PeriodicID"];
                    $result = Database_Model::getInstance()->getOne("HostFact_PeriodicElements", ["PeriodicType", "Reference"])->where("id", $pID)->execute();
                    switch ($result->PeriodicType) {
                        case "domain":
                            if($automation->registerdomain_value == 1 && $automation->registerdomain_run == "receive") {
                                require_once "class/domain.php";
                                $domain = new domain();
                                $domain->Identifier = $result->Reference;
                                $domain->show();
                                if($domain->PeriodicID == $pID && $domain->Status < 4) {
                                    $domain->check();
                                    $domain->Status = 3;
                                    Database_Model::getInstance()->update("HostFact_Domains", ["Type" => $domain->Type, "Status" => $domain->Status])->where("id", $domain->Identifier)->execute();
                                    $this->Success = array_merge($this->Success, $domain->Success);
                                    $this->Warning = array_merge($this->Warning, $domain->Warning);
                                    $this->Error = array_merge($this->Error, $domain->Error);
                                }
                            }
                            break;
                        case "hosting":
                            if($automation->makeaccount_value == 1 && $automation->makeaccount_run == "receive") {
                                require_once "class/hosting.php";
                                $hosting = new hosting();
                                $hosting->Identifier = $result->Reference;
                                $hosting->show();
                                if($hosting->PeriodicID == $pID && $hosting->Status < 4 && $hosting->Domain) {
                                    $hosting->Status = "3";
                                    Database_Model::getInstance()->update("HostFact_Hosting", ["Status" => $hosting->Status])->where("id", $hosting->Identifier)->execute();
                                }
                            }
                            break;
                    }
                }
            }
            global $_module_instances;
            foreach ($this->Elements as $k => $v) {
                if(is_numeric($k) && 0 < $v["Reference"] && $v["ProductType"] && isset($_module_instances[$v["ProductType"]]) && method_exists($_module_instances[$v["ProductType"]], "service_is_paid")) {
                    $_module_instances[$v["ProductType"]]->service_is_paid($v["Reference"]);
                }
            }
        }
        $invoice_info = ["id" => $this->Identifier, "Debtor" => $this->Debtor, "InvoiceCode" => $this->InvoiceCode];
        do_action("invoice_is_paid", $invoice_info);
    }
    public function changeBlockStatus($status)
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for invoice");
            return false;
        }
        if(!in_array($status, ["unblock", "block"])) {
            $this->Error[] = sprintf(__("invalid block status"), $status);
            return false;
        }
        if((int) $this->Status !== 0) {
            $this->Error[] = __("invoice not blocked, because not concept");
            return false;
        }
        switch ($status) {
            case "unblock":
                $block_status = "";
                $succes_status = "unblocked";
                break;
            case "block":
                $block_status = "BLOCKED";
                $succes_status = "blocked";
                break;
            default:
                $result = Database_Model::getInstance()->update("HostFact_Invoice", ["SubStatus" => $block_status])->where("id", $this->Identifier)->execute();
                if($result) {
                    createLog("invoice", $this->Identifier, "invoice " . $succes_status);
                    $this->Success[] = sprintf(__("success invoice " . $succes_status), $this->InvoiceCode);
                    delete_stats_summary();
                    return true;
                }
                return false;
        }
    }
    public function createCreditInvoice($credit_type = "full", $line_ids = [], $line_numbers = [])
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for invoice");
            return false;
        }
        if(!$this->show()) {
            return false;
        }
        if($this->Status == 9) {
            $this->Error[] = sprintf(__("cannot create creditinvoice"), $this->InvoiceCode);
            return false;
        }
        if((int) $this->Status === 0 && substr($this->InvoiceCode, 0, 9) == "[concept]") {
            $this->Error[] = sprintf(__("cannot create creditinvoice from concept"), $this->InvoiceCode);
            return false;
        }
        if($credit_type == "partly") {
            foreach ($line_numbers as $line_id => $_value) {
                $NumberSuffix = extractNumberAndSuffix($_value);
                if($NumberSuffix[1] !== false) {
                    $_value = $NumberSuffix[0];
                }
                $line_numbers[$line_id] = number2db($_value);
            }
        }
        if(!in_array($credit_type, ["full", "partly"])) {
            return false;
        }
        if($credit_type == "partly" && (count($line_ids) === 0 || isEmptyFloat(array_sum($line_numbers)))) {
            $this->Error[] = sprintf(__("for credit invoice, at least 1 line must be selected"));
            return false;
        }
        if($credit_type == "partly" && count($line_ids) === (int) $this->Elements["CountRows"]) {
            $hasSameNumbers = true;
            foreach ($this->Elements as $k => $v) {
                if(is_numeric($k) && isset($line_numbers[$k]) && $v["Number"] != $line_numbers[$k]) {
                    $hasSameNumbers = false;
                }
            }
            if($hasSameNumbers) {
                $credit_type = "full";
            }
        }
        Database_Model::getInstance()->beginTransaction();
        if($credit_type == "full") {
            if($this->SDDBatchID != "") {
                require_once "class/directdebit.php";
                $directdebit = new directdebit();
                $directdebit->removeDirectDebitFromInvoiceByBatchAndInvoiceID($this->SDDBatchID, $this->Identifier);
                $this->show();
                $this->AuthTrials = 0;
            }
            $result = Database_Model::getInstance()->update("HostFact_Invoice", ["Status" => 9, "AmountPaid" => 0])->where("id", $this->Identifier)->execute();
            if($this->SubStatus == "PAUSED") {
                Database_Model::getInstance()->update("HostFact_Invoice", ["SubStatus" => ""])->where("id", $this->Identifier)->execute();
                delete_stats_summary();
            }
            if($result) {
                $this->Success[] = sprintf(__("expired success"), $this->InvoiceCode);
            } else {
                return false;
            }
        }
        $oldinvoice = $this->InvoiceCode;
        $oldinvoiceID = $this->Identifier;
        $old_amountpaid = $this->AmountPaid;
        $this->CorrespondingInvoice = $oldinvoiceID;
        $this->InvoiceCode = $this->newInvoiceCode();
        $this->Date = rewrite_date_db2site(date("Ymd"));
        $this->Status = 8;
        $this->Sent = 0;
        $this->Reminders = 0;
        $this->Summations = 0;
        $this->Template = CREDIT_TEMPLATE ? CREDIT_TEMPLATE : $this->Template;
        $this->AmountPaid = 0;
        $this->SubStatus = "";
        $this->Authorisation = "no";
        foreach ($this->Variables as $k) {
            $this->{$k} = htmlspecialchars_decode($this->{$k});
        }
        foreach ($this->Elements as $k => $v) {
            if(is_numeric($k) && ($credit_type == "full" || $credit_type == "partly" && in_array($k, $line_ids))) {
                $element = new invoiceelement();
                $element->VatCalcMethod = $this->VatCalcMethod;
                $element->Identifier = $k;
                $element->show();
                foreach ($element->Variables as $k2) {
                    $element->{$k2} = htmlspecialchars_decode($element->{$k2});
                }
                if($credit_type == "partly" && isset($line_numbers[$k])) {
                    $element->Number = $line_numbers[$k];
                }
                $element->InvoiceCode = $this->InvoiceCode;
                $element->PriceExcl = 0 - $element->PriceExcl;
                $element->DiscountPercentage = $element->DiscountPercentage * 100;
                $element->StartPeriod = rewrite_date_db2site($element->StartPeriod);
                $element->EndPeriod = rewrite_date_db2site($element->EndPeriod);
                $element->Date = rewrite_date_db2site($element->Date);
                $element->helper_runBTWChecker = false;
                $element->add();
            }
        }
        if(CREDIT_TEXT) {
            $element = new invoiceelement();
            $element->VatCalcMethod = $this->VatCalcMethod;
            $element->Debtor = $this->Debtor;
            $element->InvoiceCode = $this->InvoiceCode;
            $element->Description = str_replace("[invoice-&gt;InvoiceCode]", $oldinvoice, str_replace("[invoice->InvoiceCode]", $oldinvoice, CREDIT_TEXT));
            $element->PriceExcl = 0;
            $element->Number = 1;
            $element->Periodic = "";
            $element->Ordering = isset($this->Elements["CountRows"]) ? $this->Elements["CountRows"] : 0;
            $element->add();
        }
        $this->PaymentMethod = "";
        $this->PaymentMethodID = 0;
        $this->TransactionID = "";
        $this->logSuccessMessage = ["message" => $credit_type == "full" ? "invoice created - from credit" : "invoice created - from partly credit", "values" => ["invoicecode" => $oldinvoice]];
        $result = $this->add(false);
        unset($this->logSuccessMessage);
        if($result) {
            Database_Model::getInstance()->commit();
            if($credit_type == "full") {
                createLog("invoice", $oldinvoiceID, "invoice expired");
                $invoice_info = ["id" => $oldinvoiceID, "Debtor" => $this->Debtor, "InvoiceCode" => $oldinvoice, "CreditInvoiceID" => $this->Identifier, "CreditInvoiceCode" => $this->InvoiceCode];
                do_action("invoice_is_credited", $invoice_info);
            } elseif($credit_type == "partly") {
                $this->show();
                $old_invoice = new invoice();
                $old_invoice->Identifier = $oldinvoiceID;
                $old_invoice->show();
                if(in_array($old_invoice->Status, ["2", "3"])) {
                    Database_Model::getInstance()->update("HostFact_Invoice", ["Status" => 3, "AmountPaid" => $old_amountpaid + -1 * $this->AmountIncl])->where("id", $oldinvoiceID)->execute();
                }
                createLog("invoice", $oldinvoiceID, "invoice partly credited", ["amountcredit" => money($this->AmountIncl), "invoicecode" => $this->InvoiceCode]);
                $this->Success[] = sprintf(__("invoice partly credited"), $oldinvoice, money($this->AmountIncl));
                $this->Warning[] = sprintf(__("invoice credited, not sent"), $this->InvoiceCode);
                if(in_array($old_invoice->Status, ["2", "3"]) && $old_invoice->PartPayment == -1 * $this->AmountIncl) {
                    Database_Model::getInstance()->update("HostFact_Invoice", ["Status" => 4, "PayDate" => ["RAW" => "CURDATE()"]])->where("id", $oldinvoiceID)->execute();
                    $old_invoice->Success[] = __("invoice paid", ["invoicecode" => $old_invoice->InvoiceCode]);
                    $invoice_info = ["id" => $old_invoice->Identifier, "Debtor" => $old_invoice->Debtor, "InvoiceCode" => $old_invoice->InvoiceCode];
                    do_action("invoice_is_paid", $invoice_info);
                    $this->Error = array_merge($this->Error, $old_invoice->Error);
                    $this->Warning = array_merge($this->Warning, $old_invoice->Warning);
                    $this->Success = array_merge($this->Success, $old_invoice->Success);
                }
            }
            return true;
        }
        Database_Model::getInstance()->rollBack();
        $this->Success = [];
        return false;
    }
    public function recover_mixed_invoices()
    {
        if($this->is_free($this->InvoiceCode)) {
            foreach ($this->Elements as $k => $v) {
                if(is_numeric($k) && $v["Debtor"] == $this->Debtor) {
                    if(!$this->PeriodicInvoice) {
                        Database_Model::getInstance()->delete("HostFact_PeriodicElements")->where("id", $v["PeriodicID"])->where("Debtor", $this->Debtor)->execute();
                    }
                    Database_Model::getInstance()->delete("HostFact_InvoiceElements")->where("id", $v["id"])->where("Debtor", $this->Debtor)->execute();
                }
            }
        }
    }
    public function changesendmethod($new_emailaddress = false)
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for invoice");
            return false;
        }
        if($new_emailaddress !== false && (check_email_address($new_emailaddress) === false || !$new_emailaddress)) {
            $this->Error[] = __("invalid emailaddress");
            return false;
        }
        if($new_emailaddress !== false) {
            Database_Model::getInstance()->update("HostFact_Invoice", ["EmailAddress" => check_email_address($new_emailaddress, "convert")])->where("id", $this->Identifier)->execute();
        }
        if($this->InvoiceMethod == "0" || $this->InvoiceMethod == "3") {
            $result = Database_Model::getInstance()->getOne("HostFact_Invoice", ["EmailAddress", "Debtor"])->where("id", $this->Identifier)->execute();
            if(!$result->EmailAddress) {
                $result = Database_Model::getInstance()->getOne("HostFact_Debtors", ["EmailAddress", "InvoiceEmailAddress"])->where("id", $result->Debtor)->execute();
                if(!$result->EmailAddress && !$result->InvoiceEmailAddress) {
                    $this->Error[] = sprintf(__("invoicemethod invoice not changed, no valid mailaddress"), $this->InvoiceCode);
                    return false;
                }
                Database_Model::getInstance()->update("HostFact_Invoice", ["EmailAddress" => $result->InvoiceEmailAddress ? $result->InvoiceEmailAddress : $result->EmailAddress])->where("id", $this->Identifier)->execute();
            }
        }
        $result = Database_Model::getInstance()->update("HostFact_Invoice", ["InvoiceMethod" => $this->InvoiceMethod])->where("id", $this->Identifier)->execute();
        if($result) {
            global $array_invoicemethod;
            if($this->InvoiceMethod == "0" || $this->InvoiceMethod == "3") {
                createLog("invoice", $this->Identifier, "invoicemethod and emailaddress changed", [strtolower($array_invoicemethod[$this->InvoiceMethod]), check_email_address($new_emailaddress, "convert", ", ")]);
            } else {
                createLog("invoice", $this->Identifier, "invoicemethod changed", $array_invoicemethod[$this->InvoiceMethod]);
            }
            $this->Success[] = sprintf(__("invoicemethod invoice changed"), $this->InvoiceCode);
            return true;
        }
        return false;
    }
    public function addInvoiceToSDDBatch($invoice_id, $first_send = false)
    {
        $invoice_result = Database_Model::getInstance()->getOne("HostFact_Invoice", ["AmountIncl", "AmountPaid"])->where("id", $invoice_id)->execute();
        if(!$invoice_result || $invoice_result->AmountIncl - $invoice_result->AmountPaid <= 0) {
            Database_Model::getInstance()->update("HostFact_Invoice", ["Authorisation" => "no", "SDDBatchID" => ""])->where("id", $invoice_id)->execute();
            $this->SDDBatchID = "";
            $this->DirectDebitDate = "";
            return false;
        }
        require_once "class/directdebit.php";
        $directdebit = new directdebit();
        if($directdebit->putInvoiceInBatch($invoice_id, false, false, $first_send)) {
            $this->SDDBatchID = $directdebit->BatchID;
            $this->DirectDebitDate = rewrite_date_db2site(str_replace("SDD", "", $directdebit->BatchID));
            Database_Model::getInstance()->update("HostFact_Invoice", ["PaymentMethod" => "auth", "SDDBatchID" => $this->SDDBatchID])->where("id", $invoice_id)->execute();
            return true;
        }
        $this->SDDBatchID = "";
        $this->DirectDebitDate = "";
        return false;
    }
    public function getInvoiceBeginEndCode($firstLast = "first")
    {
        $orderBy = $firstLast == "first" ? "ASC" : "DESC";
        $result = Database_Model::getInstance()->getOne("HostFact_Invoice", ["InvoiceCode"])->where("Status", ["!=" => 0])->orderBy("IF(SUBSTRING(`InvoiceCode`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(`InvoiceCode`,1,1))", $orderBy)->orderBy("LENGTH(`InvoiceCode`)", $orderBy)->orderBy("`InvoiceCode`", $orderBy)->execute();
        return $result->InvoiceCode;
    }
    public function quickAdd($debtor_id, $invoice_lines, $invoice_values = false)
    {
        $this->Debtor = $debtor_id;
        $new_invoice = true;
        $fields = ["InvoiceCode", "Authorisation", "Paid", "SubStatus"];
        $invoicelist = $this->all($fields, "id", "DESC", "", "Debtor", $debtor_id, "0");
        foreach ($invoicelist as $invoiceId => $value) {
            if(is_numeric($invoiceId) && isEmptyFloat($value["Paid"]) && $value["SubStatus"] != "BLOCKED") {
                $this->InvoiceCode = $value["InvoiceCode"];
                $existing_invoice_id = $value["id"];
                $new_invoice = false;
                $this->Identifier = $value["id"];
                $this->show();
                if($new_invoice === true) {
                    $this->InvoiceCode = $this->newConceptCode();
                }
                require_once "class/product.php";
                Database_Model::getInstance()->beginTransaction();
                foreach ($invoice_lines as $line_i => $invoice_line) {
                    $product = new product();
                    if($invoice_line["ProductCode"]) {
                        $product->ProductCode = $invoice_line["ProductCode"];
                        $product->show();
                    }
                    $invoice_element = new invoiceelement();
                    $invoice_element->VatCalcMethod = $this->VatCalcMethod;
                    $invoice_element->InvoiceCode = $this->InvoiceCode;
                    $invoice_element->Debtor = $debtor_id;
                    $invoice_element->ProductCode = $invoice_line["ProductCode"];
                    $invoice_element->Description = isset($invoice_line["Description"]) ? $invoice_line["Description"] : $product->ProductKeyPhrase;
                    $invoice_element->PriceExcl = isset($invoice_line["PriceExcl"]) ? $invoice_line["PriceExcl"] : $product->PriceExcl;
                    $invoice_element->TaxPercentage = isset($invoice_line["TaxPercentage"]) ? $invoice_line["TaxPercentage"] : $product->TaxPercentage;
                    $invoice_element->DiscountPercentage = $invoice_line["DiscountPercentage"];
                    $invoice_element->Ordering = $new_invoice === true ? $line_i : $this->Elements["CountRows"] + $line_i;
                    $invoice_element->ProductType = isset($invoice_line["ProductType"]) ? $invoice_line["ProductType"] : "";
                    $invoice_element->Reference = isset($invoice_line["Reference"]) && 0 < $invoice_line["Reference"] ? $invoice_line["Reference"] : 0;
                    if(isset($invoice_line["Periodic"]) && $invoice_line["Periodic"] != "") {
                        $invoice_element->PeriodicID = isset($invoice_line["PeriodicID"]) && 0 < $invoice_line["PeriodicID"] ? $invoice_line["PeriodicID"] : 0;
                        $invoice_element->Periodic = $invoice_line["Periodic"];
                        $invoice_element->Periods = $invoice_line["Periods"] ? $invoice_line["Periods"] : 1;
                        if($invoice_line["StartPeriod"]) {
                            $invoice_element->StartPeriod = $invoice_line["StartPeriod"];
                        }
                        if($invoice_line["EndPeriod"]) {
                            $invoice_element->EndPeriod = $invoice_line["EndPeriod"];
                        }
                    } else {
                        $invoice_element->Periodic = "";
                    }
                    $invoice_element->add();
                    unset($product);
                }
                require_once "class/debtor.php";
                $debtor = new debtor();
                $debtor->show($debtor_id);
                foreach ($debtor as $k => $v) {
                    if(is_string($v) && in_array($k, $debtor->Variables)) {
                        $debtor->{$k} = htmlspecialchars_decode($debtor->{$k});
                    }
                }
                if($new_invoice === false) {
                    $this->Identifier = $existing_invoice_id;
                    $this->show();
                    foreach ($this as $k => $v) {
                        if(is_string($v) && in_array($k, $this->Variables)) {
                            $this->{$k} = htmlspecialchars_decode($this->{$k});
                        }
                    }
                }
                $this->CompanyName = $debtor->InvoiceCompanyName ? $debtor->InvoiceCompanyName : $debtor->CompanyName;
                $this->TaxNumber = $debtor->TaxNumber;
                $this->Sex = $debtor->InvoiceSurName ? $debtor->InvoiceSex : $debtor->Sex;
                $this->Initials = $debtor->InvoiceInitials ? $debtor->InvoiceInitials : $debtor->Initials;
                $this->SurName = $debtor->InvoiceSurName ? $debtor->InvoiceSurName : $debtor->SurName;
                $this->Address = $debtor->InvoiceAddress ? $debtor->InvoiceAddress : $debtor->Address;
                $this->ZipCode = $debtor->InvoiceZipCode ? $debtor->InvoiceZipCode : $debtor->ZipCode;
                $this->City = $debtor->InvoiceCity ? $debtor->InvoiceCity : $debtor->City;
                $this->Country = $debtor->InvoiceCountry && $debtor->InvoiceAddress ? $debtor->InvoiceCountry : $debtor->Country;
                $this->EmailAddress = $debtor->InvoiceEmailAddress ? $debtor->InvoiceEmailAddress : $debtor->EmailAddress;
                $this->InvoiceMethod = $debtor->InvoiceMethod;
                $this->Authorisation = $debtor->InvoiceAuthorisation;
                if(0 < $debtor->InvoiceTemplate) {
                    $this->Template = $debtor->InvoiceTemplate;
                }
                if((is_string($debtor->InvoiceTerm) && $debtor->InvoiceTerm != "-1" || is_int($debtor->InvoiceTerm) && $debtor->InvoiceTerm != -1) && is_numeric($debtor->InvoiceTerm) && 0 <= intval($debtor->InvoiceTerm)) {
                    $this->Term = $debtor->InvoiceTerm;
                }
                if($invoice_values) {
                    foreach ($invoice_values as $field => $value) {
                        if(in_array($field, $this->Variables)) {
                            $this->{$field} = $value;
                        }
                    }
                }
                if($new_invoice === true) {
                    if(!$this->add()) {
                        Database_Model::getInstance()->rollBack();
                        return false;
                    }
                } elseif(!$this->edit()) {
                    Database_Model::getInstance()->rollBack();
                    return false;
                }
                Database_Model::getInstance()->commit();
                return true;
            }
        }
    }
    public function getConfigInvoiceTable($filter_options = [])
    {
        $options = [];
        $options["cols"] = [["key" => "InvoiceCode", "title" => __("invoice no"), "sortable" => "InvoiceCode", "width" => 150], ["key" => "Debtor", "title" => __("debtor"), "sortable" => "Debtor"], ["key" => "AmountExcl", "title" => __("amountexcl"), "sortable" => "AmountExcl", "colspan" => 3, "special_type" => "amount", "width" => 100, "td_class" => "show_col_widescreen_medium", "class" => "show_col_widescreen_medium"], ["key" => "AmountIncl", "title" => __("amountincl"), "sortable" => "AmountIncl", "colspan" => 3, "special_type" => "amount", "width" => 100], ["key" => "Date", "title" => __("invoice date"), "sortable" => "Date", "width" => 100, "class" => "table_date_th", "td_class" => "table_date_td"], ["key" => "ReferenceNumber", "title" => __("reference no"), "sortable" => "ReferenceNumber", "width" => 150, "class" => "show_col_widescreen_large table_reference_th", "td_class" => "show_col_widescreen_large nowrap table_reference_td"], ["key" => "Status", "title" => __("status"), "width" => 180], ["key" => "subtr", "title" => "&nbsp;", "width" => 30], "subtr" => [[], [], ["colspan" => 7, "td_class" => "show_col_widescreen_medium hide_col_widescreen_large"], ["colspan" => 4, "td_class" => "show_col_no_widescreen"], ["colspan" => 8, "td_class" => "show_col_widescreen_large"], ["colspan" => 2, "td_class" => "lineheight1"]]];
        $options["data"] = ["class/invoice.php", "invoice", "getDataInvoiceTable"];
        $options["form_action"] = "invoices.php?page=view";
        $options["page_total_placeholder"] = "page_total_placeholder_invoices";
        $options["table_class"] = "list_invoice";
        $options["ajax_callback_function"] = "createFlexibleReferenceAndDateColumn";
        $options["actions"][] = ["action" => "invoicesent", "title" => __("send invoice"), "dialog" => ["title" => __("send invoice"), "content" => "<strong>" . __("confirm action") . "</strong><br />" . __("batchdialog invoice send") . "<div id=\"dialog_send_invoice_print\" class=\"hide\"><br />" . "<strong>" . __("dialog template design title for post method") . "</strong><br />" . "<label><input type=\"radio\" name=\"printtype\" value=\"download\" checked=\"checked\"/> " . __("dialog template design option1") . "</label><br />" . "<label><input type=\"radio\" name=\"printtype\" value=\"print\"/> " . __("dialog template design option2") . "</label><br />" . "</div>"]];
        $options["actions"][] = ["action" => "invoiceprint", "title" => __("print invoice"), "dialog" => ["title" => __("print invoice"), "content" => "<strong>" . __("dialog template design title") . "</strong><br />" . "<label><input type=\"radio\" id=\"print_printtype_radio_download\" name=\"printtype\" value=\"download\" checked=\"checked\"/> " . __("dialog template design option1") . "</label><br />" . "<label><input type=\"radio\" id=\"print_printtype_radio_print\" name=\"printtype\" value=\"print\"/> " . __("dialog template design option2") . "</label><br />"]];
        if(isset($filter_options["filter"]) && in_array($filter_options["filter"], ["0", "concept", "draft_scheduled"])) {
            if(U_INVOICE_EDIT) {
                $default_date = new DateTime("+1 day");
                $hours_options = $minutes_options = [];
                $current_automatic_sending_time = explode(":", "09:00", 2);
                for ($i = 0; $i <= 23; $i++) {
                    $hours = str_pad($i, 2, "0", STR_PAD_LEFT);
                    $selected = isset($current_automatic_sending_time[0]) && $current_automatic_sending_time[0] == $hours ? " selected=\"selected\"" : "";
                    $hours_options[] = "<option value=\"" . $hours . "\"" . $selected . ">" . $hours . "</option>";
                }
                foreach (["00", "15", "30", "45"] as $minutes) {
                    $selected = isset($current_automatic_sending_time[1]) && $current_automatic_sending_time[1] == $minutes ? " selected=\"selected\"" : "";
                    $minutes_options[] = "<option value=\"" . $minutes . "\"" . $selected . ">" . $minutes . "</option>";
                }
                $options["actions"] = array_merge($options["actions"], [["action" => "schedule_invoices", "title" => __("schedule draft sending"), "dialog" => ["title" => __("schedule draft sending"), "content" => "<strong>" . __("confirm your action") . "</strong><br />" . __("confirm dialog schedule x invoices") . "<br /><br />" . "<span class=\"schedule_draft_invoices_manual hide c_gray\">" . __("confirm dialog schedule x invoices manual") . "<br /><br /></span>" . "<strong>" . __("schedule send dialog datetime label") . "</strong><br/>" . "<span class=\"title2 lineheight_input\">" . __("date") . "</span>" . "<span class=\"title2_value\"><input type=\"text\" class=\"text1 initDatepicker\" name=\"Date\" value=\"" . rewrite_date_db2site($default_date->format("Y-m-d")) . "\"  data-dp-mindate=\"" . rewrite_date_db2site(date("Y-m-d")) . "\" style=\"width:108px;\" tabindex=\"-1\" ></span>" . "<span class=\"title2 lineheight_input\">" . __("time") . "</span>" . "<span class=\"title2_value\"><select class=\"text1\" name=\"Hours\">" . implode("", $hours_options) . "</select> : <select class=\"text1\" name=\"Minutes\">" . implode("", $minutes_options) . "</select></span>", "before_open" => "batch_schedule_invoice_draft"]]]);
                if($filter_options["filter"] == "draft_scheduled") {
                    $options["actions"] = array_merge($options["actions"], [["action" => "undo_schedule_invoices", "title" => __("undo schedule draft sending"), "dialog" => ["title" => __("undo schedule draft sending"), "content" => "<strong>" . __("confirm your action") . "</strong><br />" . __("confirm dialog undo schedule x invoices")]]]);
                }
            }
            if(U_INVOICE_DELETE) {
                $options["actions"][] = ["action" => "delete_invoices", "title" => __("delete draft invoice"), "dialog" => ["title" => __("delete draft invoice"), "content" => "<strong>" . __("confirm your action") . "</strong><br />" . __("confirm dialog delete x invoices"), "button" => "red"]];
            }
        } else {
            if(!isset($filter_options["filter"]) || $filter_options["filter"] != "substatus_paused") {
                $options["actions"][] = ["action" => "remindersent", "title" => __("send reminder"), "dialog" => ["title" => __("send reminder"), "content" => "<strong>" . __("confirm action") . "</strong><br />" . __("batchdialog invoice send reminder") . "<br />" . "<label><input type=\"radio\" name=\"radio_send_invoicemethod\" value=\"setting\" checked=\"checked\" /> " . __("batchdialog invoice send reminder option1") . "</label><br />" . "<label><input type=\"radio\" name=\"radio_send_invoicemethod\" value=\"email\"/> " . __("batchdialog invoice send reminder option2") . "</label><br />" . "<label><input type=\"radio\" name=\"radio_send_invoicemethod\" value=\"post\" class=\"design_div_toggle\" /> " . __("batchdialog invoice send reminder option3") . "</label><br />" . "<label><input type=\"radio\" name=\"radio_send_invoicemethod\" value=\"both\" class=\"design_div_toggle\" /> " . __("batchdialog invoice send reminder option4") . "</label>" . "<div class=\"design_div\"><br /><strong>" . __("dialog template design title") . "</strong><br />" . "<label><input type=\"radio\" name=\"printtype\" value=\"download\" checked=\"checked\"/> " . __("dialog template design option1") . "</label><br />" . "<label><input type=\"radio\" name=\"printtype\" value=\"print\"/> " . __("dialog template design option2") . "</label>" . "</div>"]];
                if(INT_SUPPORT_SUMMATIONS) {
                    $options["actions"][] = ["action" => "summationsent", "title" => __("send summation"), "dialog" => ["title" => __("send summation"), "content" => "<strong>" . __("confirm action") . "</strong><br />" . __("batchdialog invoice send summation") . "<br />" . "<label><input type=\"radio\" name=\"radio_send_invoicemethod\" value=\"setting\" checked=\"checked\" /> " . __("batchdialog invoice send summation option1") . "</label><br />" . "<label><input type=\"radio\" name=\"radio_send_invoicemethod\" value=\"email\"/> " . __("batchdialog invoice send summation option2") . "</label><br />" . "<label><input type=\"radio\" name=\"radio_send_invoicemethod\" value=\"post\" class=\"design_div_toggle\" /> " . __("batchdialog invoice send summation option3") . "</label><br />" . "<label><input type=\"radio\" name=\"radio_send_invoicemethod\" value=\"both\" class=\"design_div_toggle\" /> " . __("batchdialog invoice send summation option4") . "</label>" . "<div class=\"design_div\"><br />" . "<strong>" . __("dialog template design title") . "</strong><br />" . "<label><input type=\"radio\" name=\"printtype\" value=\"download\" checked=\"checked\"/> " . __("dialog template design option1") . "</label><br />" . "<label><input type=\"radio\" name=\"printtype\" value=\"print\"/> " . __("dialog template design option2") . "</label>" . "</div>"]];
                }
            }
            $options["actions"][] = ["action" => "markaspaid", "title" => __("received payment"), "dialog" => ["title" => __("received payment"), "content" => "<strong>" . __("confirm action") . "</strong><br />" . __("batchdialog invoice markaspaid")]];
            $options["actions"][] = ["action" => "createcreditinvoice", "title" => __("make credit invoice"), "dialog" => ["title" => __("make credit invoice"), "content" => "<strong>" . __("confirm action") . "</strong><br />" . __("batchdialog invoice createcreditinvoice")]];
        }
        if(U_INVOICE_EDIT && isset($filter_options["page"]) && $filter_options["page"] == "debtor") {
            $ccf_text = "";
            if(!empty($this->customfields_list)) {
                $ccf_text = "<br /><br />" . __("batchdialog merge concept invoices ccf warning");
            }
            $options["actions"][] = ["action" => "mergeconceptinvoices", "title" => __("merge invoices"), "dialog" => ["title" => __("merge invoices"), "content" => "<strong>" . __("confirm action") . "</strong><br />" . __("batchdialog merge concept invoices") . $ccf_text]];
        }
        return $options;
    }
    public function listInvoices($options = [])
    {
        $fields = isset($options["fields"]) && is_array($options["fields"]) ? $options["fields"] : [];
        $sort_by = isset($options["sort_by"]) && $options["sort_by"] ? $options["sort_by"] : "Date` DESC, `InvoiceCode";
        $sort_order = isset($options["sort_order"]) && $options["sort_order"] ? $options["sort_order"] : "DESC";
        $results_per_page = isset($options["results_per_page"]) && $options["results_per_page"] == "all" ? -1 : (isset($options["results_per_page"]) && $options["results_per_page"] ? $options["results_per_page"] : MAX_RESULTS_LIST);
        $offset = $results_per_page == -1 || $results_per_page == "all" ? -1 : $options["offset"] / $results_per_page + 1;
        $filter = isset($options["filter"]) && $options["filter"] != "" ? $options["filter"] : "0|1|2|3|4|8|9";
        $searchat = isset($options["searchat"]) ? $options["searchat"] : "InvoiceCode|CompanyName|SurName";
        $searchfor = isset($options["searchfor"]) ? $options["searchfor"] : "";
        if(!is_array($fields) || empty($fields)) {
            $fields = ["InvoiceCode", "CompanyName", "Sex", "Initials", "SurName", "Address", "ZipCode", "City", "Country", "EmailAddress", "PhoneNumber", "MobileNumber", "Debtor", "Date", "Authorisation", "InvoiceMethod", "SentDate", "Status", "SubStatus", "AmountExcl", "AmountIncl", "AmountPaid", "Reminders", "Summations", "ReminderDate", "SummationDate", "Term", "TransactionID", "PaymentMethod", "AuthTrials", "PayDate", "Comment", "ReferenceNumber", "Paid"];
        }
        $this->page_total_method = isset($options["page_total_method"]) && $options["page_total_method"] ? $options["page_total_method"] : false;
        $invoices = $this->all($fields, $sort_by, $sort_order, $offset, $searchat, $searchfor, $filter, $results_per_page);
        return $invoices;
    }
    public function getDataInvoiceTable($offset, $results_per_page, $sort_by = "", $sort_order = "DESC", $parameters = [])
    {
        global $array_periodic;
        global $array_invoicemethod;
        global $array_sex;
        global $array_country;
        global $array_invoicestatus;
        $options = !empty($parameters) ? $parameters : [];
        $options["offset"] = $offset;
        $options["results_per_page"] = $results_per_page;
        $options["sort_by"] = $sort_by != "" ? $sort_by : "Date` DESC, `InvoiceCode";
        $options["sort_order"] = $sort_order;
        $invoice_list = $this->listInvoices($options);
        $data = ["TotalResults" => $invoice_list["CountRows"], "TotalText" => isset($options["page_total_method"]) && $options["page_total_method"] == "all_results_open_amount" ? money($invoice_list["OpenAmountIncl"]) . " " . __("incl vat") : money($invoice_list["TotalAmountExcl"]) . " " . __("excl vat") . " / " . money($invoice_list["TotalAmountIncl"]) . " " . __("incl vat")];
        foreach ($invoice_list as $key => $_invoice) {
            if(!is_numeric($key)) {
            } else {
                $print_method = $_scheduled_date = "";
                if($_invoice["InvoiceMethod"] != STANDARD_INVOICEMETHOD) {
                    $print_method = "<br /><span class=\"fontsmall c4\" style=\"margin-left:24px;\">" . strtolower($array_invoicemethod[$_invoice["InvoiceMethod"]]) . "</span>";
                }
                $comment = "";
                if($_invoice["Comment"] != "") {
                    $comment = "\n\t\t\t\t<span class=\"ico inline comment infopopuptop\" style=\"float:none;\">\n\t\t\t\t\t&nbsp;\n\t\t\t\t\t<span class=\"popup\">\n\t\t\t\t\t\t<strong>" . __("internal note") . "</strong><br />\n\t\t\t\t\t\t" . substr(str_replace("\r\n", " ", $_invoice["Comment"]), 0, 100) . (100 < strlen($_invoice["Comment"]) ? "..." : "") . "\n\t\t\t\t\t\t<b></b>\n\t\t\t\t\t</span>\n\t\t\t\t</span>";
                }
                $invoice_status = "";
                if($_invoice["Status"] == "4") {
                    $invoice_status = "<span class=\"ico inline check\">" . __("invoice status paid") . "</span>";
                    if($_invoice["PayDate"] != "0000-00-00") {
                        $invoice_status .= "<span> (" . rewrite_date_db2site($_invoice["PayDate"]) . ")</span>";
                    }
                } elseif($_invoice["Status"] == "2" || $_invoice["Status"] == "3") {
                    if($_invoice["SubStatus"] == "PAUSED") {
                        $pause_filter = __("invoice status collection");
                        $invoice_status = do_filter("invoice_overview_pause_status", $pause_filter, ["Identifier" => $_invoice["id"]]);
                    } elseif($_invoice["Authorisation"] == "yes") {
                        if($_invoice["TransactionID"]) {
                            $invoice_status = 1 < $_invoice["AuthTrials"] ? sprintf(__("invoice status waiting incasso trials"), $_invoice["AuthTrials"]) : __("invoice status waiting incasso");
                        } else {
                            $invoice_status = 1 <= $_invoice["AuthTrials"] ? sprintf(__("invoice status open for incasso trials"), $_invoice["AuthTrials"] + 1) : __("invoice status open for incasso");
                        }
                    } elseif(date("Y-m-d") <= substr($_invoice["PayBefore"], 0, 10)) {
                        $invoice_status = __("invoice status unpaid");
                    } elseif((int) $_invoice["Summations"] === 0 && ((int) $_invoice["Reminders"] === 0 || $_invoice["Reminders"] < INVOICE_REMINDER_NUMBER && date("Y-m-d", strtotime("+ " . INVOICE_REMINDER_TERM . " day", strtotime($_invoice["ReminderDate"]))) < date("Y-m-d"))) {
                        $invoice_status = "<span class=\"infopopupinvoicestatus delaypopup\">\n                                            <a href=\"invoices.php?page=show&id=" . $_invoice["id"] . "&open=reminder\" class=\"ico inline sendemail\">\n                                                <em>" . str_replace("{count}", $_invoice["Reminders"] + 1, __("invoice status send reminder")) . "</em>\n                                            </a>\n                    \t\t\t\t\t\t&nbsp;";
                        if(1 <= $_invoice["Reminders"]) {
                            $invoice_status .= "<span class=\"popup\">" . __("last reminder was sent on") . ": " . rewrite_date_db2site($_invoice["ReminderDate"]) . "\n                \t\t\t\t\t\t\t\t<b></b>\n                \t\t\t\t\t\t\t</span>";
                        }
                        $invoice_status .= "</span>";
                    } elseif((int) $_invoice["Summations"] === 0 && date("Y-m-d") <= date("Y-m-d", strtotime("+ " . INVOICE_REMINDER_TERM . " day", strtotime($_invoice["ReminderDate"])))) {
                        $invoice_status = "<span class=\"infopopupinvoicestatus delaypopup\">" . str_replace("{count}", $_invoice["Reminders"], __("invoice status reminder sent")) . "\n                    \t\t\t\t\t\t&nbsp;\n                    \t\t\t\t\t\t<span class=\"popup\"> " . __("last reminder was sent on") . ": " . rewrite_date_db2site($_invoice["ReminderDate"]) . "\n                    \t\t\t\t\t\t\t<b></b>\n                    \t\t\t\t\t\t</span>\n                    \t\t\t\t\t</span>";
                    } elseif(INT_SUPPORT_SUMMATIONS && ((int) $_invoice["Summations"] === 0 || date("Y-m-d", strtotime("+ " . INVOICE_SUMMATION_TERM . " day", strtotime($_invoice["SummationDate"]))) < date("Y-m-d"))) {
                        $invoice_status = "<span class=\"infopopupinvoicestatus delaypopup\">\n                                            <a href=\"invoices.php?page=show&id=" . $_invoice["id"] . "&open=summation\" class=\"ico inline sendemail\">\n                                                <em>" . str_replace("{count}", $_invoice["Summations"] + 1, __("invoice status send summation")) . "</em>\n                                            </a>\n                    \t\t\t\t\t\t&nbsp;\n                    \t\t\t\t\t\t<span class=\"popup\">";
                        if(1 <= $_invoice["Summations"]) {
                            $invoice_status .= __("last summation was sent on") . ": " . rewrite_date_db2site($_invoice["SummationDate"]);
                        } else {
                            $invoice_status .= __("last reminder was sent on") . ": " . rewrite_date_db2site($_invoice["ReminderDate"]);
                        }
                        $invoice_status .= "<b></b></span></span>";
                    } elseif(INT_SUPPORT_SUMMATIONS && date("Y-m-d") <= date("Y-m-d", strtotime("+ " . INVOICE_SUMMATION_TERM . " day", strtotime($_invoice["SummationDate"])))) {
                        $invoice_status = "<span class=\"infopopupinvoicestatus delaypopup\">" . str_replace("{count}", $_invoice["Summations"], __("invoice status summation sent")) . "\n                    \t\t\t\t\t\t&nbsp;\n                    \t\t\t\t\t\t<span class=\"popup\">" . __("last summation was sent on") . ": " . rewrite_date_db2site($_invoice["SummationDate"]) . "\n                    \t\t\t\t\t\t\t<b></b>\n                    \t\t\t\t\t\t</span>\n                    \t\t\t\t\t</span>";
                    } else {
                        $invoice_status = __("invoice status unpaid");
                    }
                } elseif($_invoice["Status"] == "0") {
                    $invoice_status = $array_invoicestatus[$_invoice["Status"]];
                    if($_invoice["SubStatus"] == "BLOCKED") {
                        $invoice_status .= " (" . __("blocked") . ")";
                    } elseif($_invoice["SentDate"] != "0000-00-00 00:00:00") {
                        $_scheduled_date = "<span class=\"infopopuptop inline delaypopup\" style=\"width:auto;\">" . rewrite_date_db2site($_invoice["SentDate"]) . "<span data-role=\"time\" class=\"hide\"> " . rewrite_date_db2site($_invoice["SentDate"], "%H:%i") . "</span><span class=\"popup\">" . sprintf(__("tooltip invoice scheduled"), rewrite_date_db2site($_invoice["SentDate"]) . " " . __("at") . " " . rewrite_date_db2site($_invoice["SentDate"], "%H:%i")) . "<b></b></span></span>";
                        $invoice_status = __("invoice status draft planned");
                    }
                } else {
                    $invoice_status = $array_invoicestatus[$_invoice["Status"]];
                }
                $acol1 = $acol2 = $acol3 = [];
                $acol1[] = $_invoice["Initials"] . $_invoice["SurName"] ? (isset($_invoice["Sex"]) && in_array($_invoice["Sex"], settings::GENDER_SHOW_IF_IN_ARRAY) ? $array_sex[$_invoice["Sex"]] : "") . " " . $_invoice["Initials"] . " " . $_invoice["SurName"] : "";
                $acol1[] = $_invoice["Address"];
                $acol1[] = $_invoice["ZipCode"] . "&nbsp;&nbsp;" . $_invoice["City"];
                $acol1[] = $array_country[$_invoice["Country"]];
                if($_invoice["AmountIncl"] != $_invoice["PartPayment"] && ($_invoice["Status"] == 2 || $_invoice["Status"] == 3)) {
                    $acol2[] = "<span class=\"fontsmall\">" . __("open sum") . ": " . money($_invoice["PartPayment"]) . "</span><br />";
                }
                if(isset($_invoice["PhoneNumber"]) && $_invoice["PhoneNumber"] != "") {
                    $acol2[] = "<span class=\"ei_title\">" . __("abbr_phonenumber") . ":</span><span class=\"ei_value\">" . $_invoice["PhoneNumber"] . "</span>";
                }
                if(isset($_invoice["MobileNumber"]) && $_invoice["MobileNumber"] != "") {
                    $acol2[] = "<span class=\"ei_title\">" . __("abbr_mobilenumber") . ":</span><span class=\"ei_value\">" . $_invoice["MobileNumber"] . "</span>";
                }
                if(isset($_invoice["EmailAddress"]) && $_invoice["EmailAddress"] != "") {
                    $acol2[] = "<span class=\"ei_title\">" . __("abbr_emailaddress") . ":</span><span class=\"ei_value\">" . $_invoice["EmailAddress"] . "</span>";
                }
                $acol3[] = "<a class=\"ico inline arrowrightwhite a1\" href=\"invoices.php?page=show&id=" . $_invoice["id"] . "\">" . __("view invoice") . "</a>";
                $acol3[] = "<a class=\"ico inline arrowrightwhite a1 printQuestion\" href=\"invoices.php?page=show&action=print&id=" . $_invoice["id"] . "\">" . __("print invoice") . "</a>";
                if(U_INVOICE_EDIT && 2 <= $_invoice["Status"] && $_invoice["Status"] <= 3) {
                    $acol3[] = "<a class=\"ico inline arrowrightwhite a1\" href=\"invoices.php?page=show&action=markaspaid&id=" . $_invoice["id"] . "\">" . __("payment received") . "</a>";
                } elseif(U_INVOICE_EDIT && $_invoice["Status"] < 2) {
                    $acol3[] = "<a class=\"ico inline arrowrightwhite a1 " . (0 < $_invoice["InvoiceMethod"] ? " printQuestion" : "") . "\" href=\"invoices.php?page=show&action=sentinvoice&id=" . $_invoice["id"] . "\">" . __("send invoice") . "</a>";
                }
                if(substr($_invoice["PayBefore"], 0, 10) < date("Y-m-d") && ($_invoice["Status"] == 2 || $_invoice["Status"] == 3)) {
                    $invoice_code_color = "c3";
                } else {
                    $invoice_code_color = "c1";
                }
                $print_method_class = "";
                if(0 < $_invoice["InvoiceMethod"]) {
                    $print_method_class = " printmethod";
                }
                $paid_status = "";
                if(($_invoice["Paid"] == 1 || $_invoice["Paid"] == 2) && ($_invoice["Status"] == 1 || (int) $_invoice["Status"] === 0)) {
                    $paid_status = "<span class=\"fontsmall c4\"> " . strtolower(__("invoicestatus paid")) . "</span>";
                }
                $partpayment = "";
                if($_invoice["AmountIncl"] != $_invoice["PartPayment"] && ($_invoice["Status"] == 2 || $_invoice["Status"] == 3)) {
                    $partpayment = "<span class=\"ico inline money infopopuptop\">\n        \t\t\t\t\t\t\t&nbsp;\n        \t\t\t\t\t\t\t<span class=\"popup\">\n        \t\t\t\t\t\t\t\t" . __("open sum") . ": " . money($_invoice["PartPayment"]) . "\n        \t\t\t\t\t\t\t\t<b></b>\n        \t\t\t\t\t\t\t</span>\n        \t\t\t\t\t\t</span>";
                }
                $data[] = ["id" => $_invoice["id"], "<a href=\"invoices.php?page=show&id=" . $_invoice["id"] . "\" class=\"" . $invoice_code_color . $print_method_class . " a1\" data-inv-method=\"" . $_invoice["InvoiceMethod"] . "\">" . $_invoice["InvoiceCode"] . "</a>" . $paid_status . ($_invoice["Authorisation"] == "yes" ? " <span class=\"fontsmall c4\">" . __("inc") . "</span>" : "") . $comment . $print_method, "<a href=\"debtors.php?page=show&id=" . $_invoice["Debtor"] . "\" class=\"a1\">" . ($_invoice["CompanyName"] ? $_invoice["CompanyName"] : $_invoice["SurName"] . ", " . $_invoice["Initials"]) . "</a>", [currency_sign_td(CURRENCY_SIGN_LEFT), money($_invoice["AmountExcl"], false), currency_sign_td(CURRENCY_SIGN_RIGHT)], [currency_sign_td(CURRENCY_SIGN_LEFT), money($_invoice["AmountIncl"], false), $partpayment . currency_sign_td(CURRENCY_SIGN_RIGHT)], $_invoice["Status"] == "0" ? $_scheduled_date : rewrite_date_db2site($_invoice["Date"]), $_invoice["ReferenceNumber"] != "" ? trim(substr($_invoice["ReferenceNumber"], 0, 20)) . (20 < strlen($_invoice["ReferenceNumber"]) ? "..." : "") : "&nbsp;", $invoice_status, "&nbsp;<span class=\"ico actionblock tag nm hover_extra_info_span\">" . __("more information") . "</span>", "subtr" => ["&nbsp", implode("<br />", $acol1), implode("", $acol2), implode("", $acol2), implode("", $acol2), implode("<br />", $acol3)]];
            }
        }
        return $data;
    }
    public function getDefaultCustomValuesWithDebtorSync()
    {
        $customfields = new customfields();
        if($customValues = $customfields->getCustomInvoiceFieldsValues(false)) {
            foreach ($customValues as $field => $valueArray) {
                $this->customvalues[$field] = $valueArray["Value"];
            }
        }
        $debtor_custom_values = $customfields->preFillCustomFields("debtor", $this->Debtor, "invoice");
        $this->customvalues = is_array($this->customvalues) ? array_merge($this->customvalues, $debtor_custom_values) : $debtor_custom_values;
    }
    public function isExportedToAccountingPackage()
    {
        $is_exported = Database_Model::getInstance()->getOne("HostFact_ExportHistory", ["id"])->where("Type", "invoice")->where("ReferenceID", $this->Identifier)->where("Status", "success")->execute();
        return $is_exported && 0 < $is_exported->id ? true : false;
    }
    public function hasAccountingTransactions()
    {
        return [];
    }
    public function cronAccountingTransactions()
    {
        $invoices = Database_Model::getInstance()->get(["HostFact_ExportPaymentTransactions", "HostFact_Invoice"], ["HostFact_Invoice.id", "HostFact_SDD_Batches.Date", "HostFact_ExportPaymentTransactions.`id` as PaymentTransactionID"])->join("HostFact_SDD_Batches", "HostFact_SDD_Batches.`BatchID` = HostFact_Invoice.`SDDBatchID`")->where("HostFact_ExportPaymentTransactions.`InvoiceID` = HostFact_Invoice.`id`")->where("HostFact_ExportPaymentTransactions.PackageStatus", ["!=" => "removed"])->where("HostFact_ExportPaymentTransactions.Action", "sdd_planned_markaspaid")->where("HostFact_Invoice.Authorisation", "yes")->where("HostFact_Invoice.Status", ["IN" => [2, 3]])->where("HostFact_Invoice.SDDBatchID", ["!=" => ""])->where("HostFact_SDD_Batches.Date", ["<=" => date("Y-m-d")])->execute();
        if(!empty($invoices)) {
            foreach ($invoices as $_key => $_invoice) {
                $invoice_object = new invoice();
                $invoice_object->Identifier = $_invoice->id;
                $invoice_object->show();
                $invoice_object->markaspaid($_invoice->Date, "auth");
                Database_Model::getInstance()->update("HostFact_ExportPaymentTransactions", ["Action" => "sdd_markaspaid"])->where("id", $_invoice->PaymentTransactionID)->execute();
                $invoice_object->checkAuto();
            }
        }
    }
    public function getAccountingTransactionsTableConfig($_package)
    {
        $options = [];
        $options["cols"] = [["key" => "date", "title" => __("date"), "sortable" => "Date", "width" => 70], ["key" => "journal", "title" => __("export accounting - journal")], ["key" => "packagereference", "title" => __("export accounting - entry number")], ["key" => "description", "title" => __("bank transaction description")], ["key" => "amount", "title" => __("bank transaction amount"), "sortable" => "Amount", "width" => 100, "colspan" => 3, "special_type" => "amount"], ["key" => "packagestatus", "title" => __("status")], ["key" => "action", "title" => __("historydialog action")]];
        $options["filter"] = "";
        $options["results_per_page"] = "all";
        $options["parameters"]["reference_id"] = $this->Identifier;
        $options["parameters"]["package"] = $_package;
        $options["page_total_placeholder"] = "page_total_placeholder_accounting_transactions_" . $_package;
        $options["data"] = ["class/invoice.php", "invoice", "getAccountingTransactionsTableData"];
        $options["form_action"] = "";
        return $options;
    }
    public function getAccountingTransactionsTableData($offset, $results_per_page, $sort_by = "", $sort_order = "ASC", $parameters = [])
    {
        $options = !empty($parameters) ? $parameters : [];
        Database_Model::getInstance()->get("HostFact_ExportPaymentTransactions")->where("InvoiceID", $options["reference_id"])->where("Package", $options["package"]);
        if($sort_by) {
            Database_Model::getInstance()->orderBy($sort_by, $sort_order);
        }
        $transaction_list = Database_Model::getInstance()->execute();
        $data = ["TotalResults" => count($transaction_list)];
        foreach ($transaction_list as $_transaction) {
            $data[] = [rewrite_date_db2site($_transaction->Date), htmlspecialchars($_transaction->Journal), htmlspecialchars($_transaction->PackageReference), htmlspecialchars($_transaction->Description), [currency_sign_td(CURRENCY_SIGN_LEFT), money($_transaction->Amount, false), currency_sign_td(CURRENCY_SIGN_RIGHT)], __("invoice show accounting transactions - status " . $_transaction->PackageStatus), $_transaction->Action ? __("invoice show accounting transactions - action " . $_transaction->Action) : "-"];
        }
        return $data;
    }
    public function scheduleDraftInvoice($datetime)
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for invoice");
            return false;
        }
        if((int) $this->Status !== 0) {
            $this->Error[] = sprintf(__("invoice not scheduled, because not concept"), $this->InvoiceCode);
            return false;
        }
        if($this->SubStatus != "") {
            $this->Error[] = sprintf(__("invoice not scheduled, because blocked"), $this->InvoiceCode);
            return false;
        }
        try {
            $date = new DateTime($datetime);
            if(!preg_match("/(0[0-9]|1[0-9]|2[0-3]):(00|15|30|45)/", $date->format("H:i"))) {
                throw new Exception("invalid time");
            }
            if($date->format("Y-m-d H:i:s") != $datetime) {
                throw new Exception("invalid datetime");
            }
        } catch (Exception $e) {
            $this->Error[] = __("invalid datetime for schedule sending of draft invoice");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Invoice", ["SentDate" => $datetime])->where("id", $this->Identifier)->execute();
        if($result) {
            createLog("invoice", $this->Identifier, "draft invoice scheduled", ["scheduled_at" => rewrite_date_db2site($datetime) . " " . __("at") . " " . rewrite_date_db2site($datetime, "%H:%i")]);
            $this->Success[] = sprintf(__("draft invoice scheduled"), $this->InvoiceCode, rewrite_date_db2site($datetime) . " " . __("at") . " " . rewrite_date_db2site($datetime, "%H:%i"));
            return true;
        }
        return false;
    }
    public function undoScheduleDraftInvoice($from = false)
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for invoice");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Invoice", ["SentDate" => "0000-00-00 00:00:00"])->where("id", $this->Identifier)->where("Status", "0")->execute();
        if($result) {
            if($from == "edit") {
                createLog("invoice", $this->Identifier, "draft invoice scheduled undone edit");
                $this->Success[] = sprintf(__("draft invoice scheduled undone"), $this->InvoiceCode);
            } else {
                createLog("invoice", $this->Identifier, "draft invoice scheduled undone");
                $this->Success[] = sprintf(__("draft invoice scheduled undone"), $this->InvoiceCode);
            }
            return true;
        }
        return false;
    }
    public function canGenerateQRCode($includeDraftStatus)
    {
        if(!in_array($this->Status, [2, 3]) && ($includeDraftStatus === false || !in_array($this->Status, [0, 1]))) {
            return false;
        }
        if($this->Authorisation !== "no") {
            return false;
        }
        $openAmount = deformat_money($this->PartPayment);
        if($openAmount <= 0) {
            return false;
        }
        return true;
    }
    public function canGenerateExampleQRCode()
    {
        return $this->canGenerateQRCode(true);
    }
}
class invoiceelement
{
    public $Identifier;
    public $InvoiceCode;
    public $Debtor;
    public $Date;
    public $Number;
    public $NumberSuffix;
    public $ProductCode;
    public $Description;
    public $PriceExcl;
    public $TaxPercentage;
    public $DiscountPercentage;
    public $DiscountPercentageType;
    public $Periods;
    public $Periodic;
    public $PeriodicID;
    public $StartPeriod;
    public $EndPeriod;
    public $Free1;
    public $Free2;
    public $Free3;
    public $Free4;
    public $Free5;
    public $AmountIncl;
    public $AmountTax;
    public $AmountExcl;
    public $PeriodPriceIncl;
    public $PeriodPriceTax;
    public $PeriodPriceExcl;
    public $PriceIncl;
    public $PriceTax;
    public $Ordering;
    public $Domain;
    public $Hosting;
    public $ProductType;
    public $Reference;
    public $CountRows;
    public $Table;
    public $Error;
    public $Warning;
    public $Success;
    public $helper_runBTWChecker;
    public $Variables = ["Identifier", "InvoiceCode", "Debtor", "Date", "Number", "NumberSuffix", "ProductCode", "Description", "PriceExcl", "TaxPercentage", "DiscountPercentage", "DiscountPercentageType", "Periods", "Periodic", "PeriodicID", "StartPeriod", "EndPeriod", "Free1", "Free2", "Free3", "Free4", "Free5", "Ordering", "ProductType", "Reference"];
    public function __construct()
    {
        $this->Number = 1;
        $this->Periods = 1;
        $this->Ordering = 0;
        $this->Date = rewrite_date_db2site(date("Y-m-d"));
        $this->Error = [];
        $this->Warning = [];
        $this->Success = [];
        $this->DiscountPercentage = "";
        $this->DiscountPercentageType = "line";
        $this->TaxPercentage = STANDARD_TAX;
        $this->ProductType = "";
        $this->Reference = 0;
        $this->NumberSuffix = "";
        $this->helper_runBTWChecker = true;
    }
    public function __destruct()
    {
    }
    public function show()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for invoice element");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_InvoiceElements", ["HostFact_InvoiceElements.*", "HostFact_Products.ProductName", "HostFact_Products.ProductKeyPhrase", "HostFact_Products.ProductDescription"])->join("HostFact_Products", "HostFact_Products.ProductCode = HostFact_InvoiceElements.ProductCode")->where("HostFact_InvoiceElements.id", $this->Identifier)->execute();
        if(!$result || empty($result->id)) {
            $this->Error[] = __("invalid identifier for invoice element");
            return false;
        }
        foreach ($result as $key => $value) {
            if($key != "id") {
                $this->{$key} = htmlspecialchars($value);
            }
        }
        if(!isset($this->VatCalcMethod)) {
            $vat_calc_method = Database_Model::getInstance()->getOne("HostFact_Invoice", ["VatCalcMethod"])->where("InvoiceCode", $this->InvoiceCode)->execute();
            $this->VatCalcMethod = isset($vat_calc_method->VatCalcMethod) ? $vat_calc_method->VatCalcMethod : "excl";
        }
        $this->PriceIncl = $this->VatCalcMethod == "incl" ? round($this->PriceExcl * (1 + $this->TaxPercentage), 5) : round((double) $this->PriceExcl, 5) * (1 + $this->TaxPercentage);
        return true;
    }
    public function format()
    {
        global $array_periodes;
        global $array_periodesMV;
        $offset = 0 <= $this->PriceExcl * $this->Periods * $this->Number ? 0 : 0;
        if($this->VatCalcMethod == "incl") {
            $this->NoDiscountAmountIncl = money($this->PriceExcl * $this->Periods * $this->Number * (1 + $this->TaxPercentage) + $offset, false);
            $this->NoDiscountAmountTax = money($this->PriceExcl * $this->Periods * $this->Number * $this->TaxPercentage + $offset, false);
            $this->NoDiscountAmountExcl = money($this->PriceExcl * $this->Periods * $this->Number + $offset, false);
            $this->AmountIncl = money(round(round(1 - $this->DiscountPercentage, 8) * $this->PriceIncl * $this->Periods * $this->Number, 2) + $offset, false);
            $this->AmountTax = money(round(1 - $this->DiscountPercentage, 8) * $this->PriceExcl * $this->Periods * $this->Number * $this->TaxPercentage + $offset, false);
            $this->AmountExcl = money(round(1 - $this->DiscountPercentage, 8) * $this->PriceExcl * $this->Periods * $this->Number + $offset, false);
            $this->PeriodPriceIncl = money($this->PriceExcl * $this->Periods * (1 + $this->TaxPercentage) + $offset, false);
            $this->PeriodPriceTax = money($this->PriceExcl * $this->Periods * $this->TaxPercentage + $offset, false);
            $this->PeriodPriceExcl = money($this->PriceExcl * $this->Periods + $offset, false);
            $this->DiscountAmountIncl = round((double) $this->DiscountPercentage, 8) * $this->PriceExcl * $this->Periods * $this->Number * (1 + $this->TaxPercentage) + $offset;
            $this->DiscountAmountIncl = $offset < $this->DiscountAmountIncl ? "-" . money(abs($this->DiscountAmountIncl), false) : money(abs($this->DiscountAmountIncl), false);
            $this->DiscountAmountTax = money(round((double) $this->DiscountPercentage, 8) * $this->PriceExcl * $this->Periods * $this->Number * $this->TaxPercentage + $offset, false);
            $this->DiscountAmountExcl = round((double) $this->DiscountPercentage, 8) * $this->PriceExcl * $this->Periods * $this->Number + $offset;
            $this->DiscountAmountExcl = $offset < $this->DiscountAmountExcl ? "-" . money(abs($this->DiscountAmountExcl), false) : money(abs($this->DiscountAmountExcl), false);
            $this->PriceTax = money($this->PriceIncl - round((double) $this->PriceExcl, 2), false);
            $this->PriceIncl = money($this->PriceIncl, false);
            $this->PriceExcl = money($this->PriceExcl, false);
        } else {
            $this->NoDiscountAmountIncl = money(round($this->PriceExcl * $this->Periods * $this->Number, 2) * (1 + $this->TaxPercentage) + $offset, false);
            $this->NoDiscountAmountTax = money(round($this->PriceExcl * $this->Periods * $this->Number, 2) * $this->TaxPercentage + $offset, false);
            $this->NoDiscountAmountExcl = money($this->PriceExcl * $this->Periods * $this->Number + $offset, false);
            $this->AmountIncl = money(round(round(1 - $this->DiscountPercentage, 8) * $this->PriceExcl * $this->Periods * $this->Number, 2) * (1 + $this->TaxPercentage) + $offset, false);
            $this->AmountTax = money(round(round(1 - $this->DiscountPercentage, 8) * $this->PriceExcl * $this->Periods * $this->Number, 2) * $this->TaxPercentage + $offset, false);
            $this->AmountExcl = money(round(1 - $this->DiscountPercentage, 8) * $this->PriceExcl * $this->Periods * $this->Number + $offset, false);
            $this->PeriodPriceIncl = money(round($this->PriceExcl * $this->Periods, 2) * (1 + $this->TaxPercentage) + $offset, false);
            $this->PeriodPriceTax = money(round($this->PriceExcl * $this->Periods, 2) * $this->TaxPercentage + $offset, false);
            $this->PeriodPriceExcl = money($this->PriceExcl * $this->Periods + $offset, false);
            $this->DiscountAmountIncl = round(round((double) $this->DiscountPercentage, 8) * $this->PriceExcl * $this->Periods * $this->Number, 2) * (1 + $this->TaxPercentage) + $offset;
            $this->DiscountAmountIncl = $offset < $this->DiscountAmountIncl ? "-" . money(abs($this->DiscountAmountIncl), false) : money(abs($this->DiscountAmountIncl), false);
            $this->DiscountAmountTax = money(round(round((double) $this->DiscountPercentage, 8) * $this->PriceExcl * $this->Periods * $this->Number, 2) * $this->TaxPercentage + $offset, false);
            $this->DiscountAmountExcl = round((double) $this->DiscountPercentage, 8) * $this->PriceExcl * $this->Periods * $this->Number + $offset;
            $this->DiscountAmountExcl = $offset < $this->DiscountAmountExcl ? "-" . money(abs($this->DiscountAmountExcl), false) : money(abs($this->DiscountAmountExcl), false);
            $this->PriceIncl = money(round((double) $this->PriceExcl, 2) * (1 + $this->TaxPercentage) + $offset, false);
            $this->PriceTax = money(round((double) $this->PriceExcl, 2) * $this->TaxPercentage + $offset, false);
            $this->PriceExcl = money($this->PriceExcl + $offset, false);
        }
        $this->FullDiscountPercentage = round($this->DiscountPercentage * 100, 2);
        $pattern = "/\\[[A-Z]\\:(.*?)\\]/is";
        $replacements = "";
        $this->Description = preg_replace($pattern, $replacements, $this->Description);
        $this->Date = rewrite_date_db2site($this->Date);
        $this->Number = number_format($this->Number, strpos($this->Number, ".") === false ? 0 : strlen($this->Number) - strpos($this->Number, ".") - 1, AMOUNT_DEC_SEPERATOR, AMOUNT_THOU_SEPERATOR);
        $this->StartPeriod = rewrite_date_db2site($this->StartPeriod);
        $this->EndPeriod = rewrite_date_db2site($this->EndPeriod);
        $this->Periodic = $this->Periodic ? 1 < $this->Periods ? $array_periodesMV[$this->Periodic] : $array_periodes[$this->Periodic] : "";
        $this->FullTaxPercentage = showNumber($this->TaxPercentage * 100);
    }
    public function add()
    {
        $this->OldNumber = $this->Number;
        $this->Number = deformat_money($this->Number);
        if(is_numeric($this->Number) && isEmptyFloat($this->Number) || !$this->Number) {
            return true;
        }
        if($this->helper_runBTWChecker) {
            $this->TaxPercentage = btwcheck($this->Debtor, $this->TaxPercentage);
        }
        $this->PriceExcl = deformat_money($this->PriceExcl);
        $this->DiscountPercentage = floatval(round(deformat_money($this->DiscountPercentage), 2)) / 100;
        if(!$this->validate()) {
            return false;
        }
        $this->Date = rewrite_date_site2db($this->Date);
        $this->StartPeriod = rewrite_date_site2db($this->StartPeriod);
        $this->EndPeriod = rewrite_date_site2db($this->EndPeriod);
        $this->Periods = (int) $this->Periods === 0 ? 1 : $this->Periods;
        if($this->Periodic && 0 < $this->Periods && !$this->PeriodicID) {
            require_once "class/periodic.php";
            $periodic = new periodic();
            $periodic->IsInvoice = true;
            foreach ($periodic->Variables as $value) {
                if(isset($this->{$value}) && !in_array($value, ["Reference", "DiscountPercentage"])) {
                    $periodic->{$value} = $this->{$value};
                }
            }
            $periodic->isProductTypeKnown = $this->ProductType ? true : false;
            $periodic->PeriodicType = $this->ProductType ? $this->ProductType : "";
            $periodic->Reference = $this->ProductType ? $this->Reference : 0;
            if($this->DiscountPercentageType == "subscription") {
                $periodic->DiscountPercentage = $this->DiscountPercentage * 100;
            }
            $periodic->Date = $this->EndPeriod ? $this->Date : $this->StartPeriod;
            $periodic->StartPeriod = rewrite_date_db2site($this->EndPeriod);
            $periodic->EndPeriod = "";
            $periodic->LastDate = rewrite_date_db2site($this->Date);
            $periodic->NextDate = "";
            if(isset($this->InvoiceStatus)) {
                $periodic->InvoiceStatus = $this->InvoiceStatus;
            }
            if($periodic->add()) {
                $this->PeriodicID = $periodic->Identifier;
                $this->PriceExcl = $periodic->PriceExcl;
                $this->Domain = isset($periodic->Domain) && $periodic->Domain ? $periodic->Domain : $this->Domain;
                $this->DomainID = isset($periodic->DomainID) && $periodic->DomainID ? $periodic->DomainID : "";
                $this->Hosting = isset($periodic->Hosting) && $periodic->Hosting ? $periodic->Hosting : $this->Hosting;
                global $additional_product_types;
                if(!$this->ProductType && isset($periodic->PeriodicType) && $periodic->PeriodicType && isset($additional_product_types[$periodic->PeriodicType])) {
                    $this->ProductType = isset($periodic->Reference) && 0 < $periodic->Reference ? $periodic->PeriodicType : "";
                    $this->Reference = isset($periodic->Reference) && 0 < $periodic->Reference ? $periodic->Reference : 0;
                }
                $this->Warning = array_merge($periodic->Warning, $this->Warning);
            } else {
                $this->Error = array_merge($periodic->Error, $this->Error);
            }
            $this->StartPeriod = 0 < intval($this->StartPeriod) ? $this->StartPeriod : $this->Date;
            $this->EndPeriod = 0 < intval($this->EndPeriod) ? $this->EndPeriod : $periodic->StartPeriod;
            $periodic->__destruct();
            unset($periodic);
        }
        if(empty($this->Error)) {
            $line_amount = getLineAmount($this->VatCalcMethod, $this->PriceExcl, $this->Periods, $this->Number, $this->TaxPercentage, $this->DiscountPercentage);
            $result = Database_Model::getInstance()->insert("HostFact_InvoiceElements", ["InvoiceCode" => $this->InvoiceCode, "Debtor" => $this->Debtor, "Date" => $this->Date, "Number" => $this->Number, "NumberSuffix" => $this->NumberSuffix, "ProductCode" => $this->ProductCode, "Description" => $this->Description, "PriceExcl" => $this->PriceExcl, "TaxPercentage" => $this->TaxPercentage, "DiscountPercentage" => $this->DiscountPercentage, "DiscountPercentageType" => $this->DiscountPercentageType, "Periods" => $this->Periods, "Periodic" => $this->Periodic, "PeriodicID" => $this->PeriodicID, "StartPeriod" => substr($this->StartPeriod, 0, 4) != "DATE" ? $this->StartPeriod : ["RAW" => $this->StartPeriod], "EndPeriod" => substr($this->EndPeriod, 0, 4) != "DATE" ? $this->EndPeriod : ["RAW" => $this->EndPeriod], "Ordering" => $this->Ordering, "LineAmountExcl" => $line_amount["excl"], "LineAmountIncl" => $line_amount["incl"], "ProductType" => $this->ProductType, "Reference" => $this->Reference])->execute();
            if($result) {
                $this->Identifier = $result;
                if($this->ProductCode && 0 <= $this->PriceExcl) {
                    Database_Model::getInstance()->update("HostFact_Products", ["Sold" => ["RAW" => "`Sold` + :sold"]])->bindValue("sold", $this->Number * $this->Periods)->where("ProductCode", $this->ProductCode)->execute();
                } elseif($this->ProductCode && $this->PriceExcl < 0) {
                    Database_Model::getInstance()->update("HostFact_Products", ["Sold" => ["RAW" => "`Sold` - :sold"]])->bindValue("sold", $this->Number * $this->Periods)->where("ProductCode", $this->ProductCode)->execute();
                }
                return true;
            }
            return false;
        }
        return false;
    }
    public function edit()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for invoice element");
            return false;
        }
        $this->OldNumber = $this->Number;
        $this->Number = deformat_money($this->Number);
        if(is_numeric($this->Number) && isEmptyFloat($this->Number) || !$this->Number) {
            Database_Model::getInstance()->delete("HostFact_InvoiceElements")->where("id", $this->Identifier)->execute();
            return true;
        }
        if($this->helper_runBTWChecker) {
            $this->TaxPercentage = btwcheck($this->Debtor, $this->TaxPercentage);
        }
        $this->PriceExcl = deformat_money($this->PriceExcl);
        $this->DiscountPercentage = floatval(round(deformat_money($this->DiscountPercentage), 2)) / 100;
        if(!$this->validate()) {
            return false;
        }
        $this->Date = rewrite_date_site2db($this->Date);
        $this->StartPeriod = rewrite_date_site2db($this->StartPeriod);
        $this->Periods = (int) $this->Periods === 0 ? 1 : $this->Periods;
        if($this->Periodic && 0 < $this->Periods) {
            $this->EndPeriod = rewrite_date_site2db($this->EndPeriod) ? rewrite_date_site2db($this->EndPeriod) : calculate_date($this->StartPeriod, $this->Periods, $this->Periodic);
        } else {
            $this->EndPeriod = rewrite_date_site2db($this->EndPeriod);
        }
        if($this->Periodic && 0 < $this->Periods) {
            require_once "class/periodic.php";
            $periodic = new periodic();
            $periodic->IsInvoice = true;
            if($this->PeriodicID) {
                $periodic->Identifier = $this->PeriodicID;
                $periodic->show();
                $periodic->DiscountPercentage = $periodic->DiscountPercentage * 100;
            }
            foreach ($periodic->Variables as $value) {
                if(isset($this->{$value}) && !in_array($value, ["Reference", "DiscountPercentage"])) {
                    $periodic->{$value} = $this->{$value};
                }
            }
            $periodic->Identifier = $this->PeriodicID;
            if($this->DiscountPercentageType == "subscription") {
                $periodic->DiscountPercentage = $this->DiscountPercentage * 100;
            }
            $periodic->StartPeriod = rewrite_date_db2site($this->EndPeriod);
            $periodic->EndPeriod = "";
            $periodic->LastDate = rewrite_date_db2site($this->Date);
            $periodic->NextDate = "";
            if(isset($this->InvoiceStatus)) {
                $periodic->InvoiceStatus = $this->InvoiceStatus;
            }
            if($this->PeriodicID) {
                $result = Database_Model::getInstance()->getOne("HostFact_InvoiceElements", ["id"])->where("PeriodicID", $this->PeriodicID)->orderBy("StartPeriod", "DESC")->orderBy("id", "ASC")->execute();
                $periodic->LastInvoiceElementId = $result->id;
                if($periodic->LastInvoiceElementId == $this->Identifier) {
                    if($periodic->edit()) {
                        $this->PriceExcl = $periodic->PriceExcl;
                    } else {
                        $this->Error = array_merge($periodic->Error, $this->Error);
                    }
                }
            } else {
                $periodic->isProductTypeKnown = $this->ProductType ? true : false;
                $periodic->PeriodicType = $this->ProductType ? $this->ProductType : $periodic->PeriodicType;
                $periodic->Reference = $this->ProductType ? $this->Reference : $periodic->Reference;
                if($periodic->add()) {
                    $this->PeriodicID = $periodic->Identifier;
                    $this->PriceExcl = $periodic->PriceExcl;
                    $this->Domain = isset($periodic->Domain) && $periodic->Domain ? $periodic->Domain : $this->Domain;
                    $this->DomainID = isset($periodic->DomainID) && $periodic->DomainID ? $periodic->DomainID : "";
                    $this->Hosting = isset($periodic->Hosting) && $periodic->Hosting ? $periodic->Hosting : $this->Hosting;
                    global $additional_product_types;
                    if(!$this->ProductType && isset($periodic->PeriodicType) && $periodic->PeriodicType && isset($additional_product_types[$periodic->PeriodicType])) {
                        $this->ProductType = isset($periodic->Reference) && 0 < $periodic->Reference ? $periodic->PeriodicType : "";
                        $this->Reference = isset($periodic->Reference) && 0 < $periodic->Reference ? $periodic->Reference : 0;
                    }
                    $this->Warning = array_merge($periodic->Warning, $this->Warning);
                } else {
                    $this->Error = array_merge($periodic->Error, $this->Error);
                }
            }
            $this->StartPeriod = 0 < intval($this->StartPeriod) ? $this->StartPeriod : $this->Date;
            $this->EndPeriod = 0 < intval($this->EndPeriod) ? $this->EndPeriod : $periodic->StartPeriod;
            $periodic->__destruct();
            unset($periodic);
        }
        if(empty($this->Error)) {
            $line_amount = getLineAmount($this->VatCalcMethod, $this->PriceExcl, $this->Periods, $this->Number, $this->TaxPercentage, $this->DiscountPercentage);
            $result = Database_Model::getInstance()->update("HostFact_InvoiceElements", ["InvoiceCode" => $this->InvoiceCode, "Debtor" => $this->Debtor, "Date" => $this->Date, "Number" => $this->Number, "NumberSuffix" => $this->NumberSuffix, "ProductCode" => $this->ProductCode, "Description" => $this->Description, "PriceExcl" => $this->PriceExcl, "TaxPercentage" => $this->TaxPercentage, "DiscountPercentage" => $this->DiscountPercentage, "DiscountPercentageType" => $this->DiscountPercentageType, "Periods" => $this->Periods, "Periodic" => $this->Periodic, "PeriodicID" => $this->PeriodicID, "StartPeriod" => substr($this->StartPeriod, 0, 4) != "DATE" ? $this->StartPeriod : ["RAW" => $this->StartPeriod], "EndPeriod" => substr($this->EndPeriod, 0, 4) != "DATE" ? $this->EndPeriod : ["RAW" => $this->EndPeriod], "Ordering" => $this->Ordering, "LineAmountExcl" => $line_amount["excl"], "LineAmountIncl" => $line_amount["incl"], "ProductType" => $this->ProductType, "Reference" => $this->Reference])->where("id", $this->Identifier)->execute();
            if($result) {
                return true;
            }
            return false;
        }
        return false;
    }
    public function updatePrice($VatCalcMethod)
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for invoice element");
            return false;
        }
        $this->PriceExcl = deformat_money($this->PriceExcl);
        $line_amount = getLineAmount($VatCalcMethod, $this->PriceExcl, $this->Periods, $this->Number, $this->TaxPercentage, $this->DiscountPercentage);
        $result = Database_Model::getInstance()->update("HostFact_InvoiceElements", ["PriceExcl" => $this->PriceExcl, "LineAmountExcl" => $line_amount["excl"], "LineAmountIncl" => $line_amount["incl"]])->where("id", $this->Identifier)->execute();
        if($result) {
            return true;
        }
        return false;
    }
    public function delete()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for invoice element");
            return false;
        }
        $result = Database_Model::getInstance()->delete("HostFact_InvoiceElements")->where("id", $this->Identifier)->execute();
        if($result) {
            return true;
        }
        return false;
    }
    public function validate()
    {
        if(!$this->is_free($this->InvoiceCode)) {
            $this->Error[] = __("element conflict with invoicecode");
            return false;
        }
        if(!is_numeric($this->Debtor) || empty($this->Debtor)) {
            $this->Error[] = __("invalid invoice element debtor");
        }
        if(strlen($this->Date) && !is_date(rewrite_date_site2db($this->Date))) {
            $this->Error[] = __("invalid invoice element date");
        }
        if(strlen($this->Number) && (!is_numeric($this->Number) || 20 < strlen($this->NumberSuffix))) {
            $this->Error[] = sprintf(__("invalid invoice element number"), $this->OldNumber);
        }
        if(!(is_string($this->ProductCode) && strlen($this->ProductCode) <= 50 || strlen($this->ProductCode) === 0)) {
            $this->Error[] = __("invalid productcode");
        }
        if(!(is_string($this->Description) && strlen($this->Description) <= 21845 || strlen($this->Description) === 0)) {
            $this->Error[] = __("invalid invoice element description");
        }
        if(!is_numeric($this->PriceExcl)) {
            $this->PriceExcl = 0;
        }
        if(!is_numeric($this->DiscountPercentage) || $this->DiscountPercentage < 0 || 1 < $this->DiscountPercentage) {
            $this->Error[] = __("invalid invoice element discountpercentage");
        }
        if(is_null($this->TaxPercentage) || $this->TaxPercentage == "") {
            $this->TaxPercentage = 0;
        } elseif(!is_numeric($this->TaxPercentage) || $this->TaxPercentage < 0 || 1 < $this->TaxPercentage) {
            $this->Error[] = __("invalid invoice element taxpercentage");
        }
        if($this->Periodic && 0 < $this->Periods && !$this->PeriodicID && !isset($this->StartPeriod)) {
            $this->Error[] = __("invalid invoice element startdate");
        }
        if($this->PeriodicID == "") {
            $this->PeriodicID = 0;
        } elseif(!is_numeric($this->PeriodicID)) {
            $this->Error[] = __("invalid periodicID");
        }
        if($this->TaxPercentage && 2 < strlen(substr(strrchr($this->TaxPercentage * 100, "."), 1))) {
            $this->Error[] = __("invalid invoice element taxpercentage digits");
        }
        if($this->DiscountPercentage && 2 < strlen(substr(strrchr(number2db($this->DiscountPercentage) * 100, "."), 1))) {
            $this->Error[] = __("invalid invoice element discountpercentage digits");
        }
        if(!in_array($this->DiscountPercentageType, ["line", "subscription"])) {
            $this->DiscountPercentageType = "line";
        }
        if(strlen($this->EndPeriod) && $this->EndPeriod != "0000-00-00" && !is_date(rewrite_date_site2db($this->EndPeriod))) {
            $this->Error[] = __("invalid endperiod");
        } elseif(!strlen($this->EndPeriod)) {
            $this->EndPeriod = "";
        }
        if(strlen($this->StartPeriod) && $this->StartPeriod != "0000-00-00" && !is_date(rewrite_date_site2db($this->StartPeriod))) {
            $this->Error[] = __("invalid startperiod");
        } elseif(!strlen($this->StartPeriod)) {
            $this->StartPeriod = "";
        }
        if(strlen($this->Ordering) && !is_numeric($this->Ordering)) {
            $this->Error[] = __("invalid ordering");
        }
        if(!empty($this->Error)) {
            return false;
        }
        return true;
    }
    public function is_free($InvoiceCode)
    {
        if($InvoiceCode) {
            $result = Database_Model::getInstance()->getOne("HostFact_InvoiceElements", ["Debtor"])->where("InvoiceCode", $InvoiceCode)->execute();
            if(!$result || $result->Debtor == $this->Debtor || isset($this->OldDebtor) && 0 < $this->OldDebtor && $this->OldDebtor == $result->Debtor) {
                return true;
            }
            return false;
        }
        return false;
    }
    public function all($InvoiceCode)
    {
        $InvoiceCode = htmlspecialchars_decode($InvoiceCode);
        if(!isset($this->VatCalcMethod)) {
            $vat_calc_method = Database_Model::getInstance()->getOne("HostFact_Invoice", ["VatCalcMethod"])->where("InvoiceCode", $InvoiceCode)->execute();
            $this->VatCalcMethod = isset($vat_calc_method->VatCalcMethod) ? $vat_calc_method->VatCalcMethod : "excl";
        }
        $list = [];
        if($this->VatCalcMethod == "incl") {
            $result = Database_Model::getInstance()->getOne("HostFact_InvoiceElements", ["COUNT(`id`) as CountRows", "SUM(`LineAmountIncl` / (1+`TaxPercentage`)) as AmountExcl", "SUM(`LineAmountIncl`) as AmountIncl"])->where("InvoiceCode", $InvoiceCode)->groupBy("InvoiceCode")->execute();
            $sql_amount_excl = "HostFact_InvoiceElements.`LineAmountExcl` as AmountExcl";
            $sql_amount_incl = "HostFact_InvoiceElements.`LineAmountIncl` as AmountIncl";
        } else {
            $result = Database_Model::getInstance()->getOne("HostFact_InvoiceElements", ["COUNT(`id`) as CountRows", "SUM(`LineAmountExcl`) as AmountExcl", "SUM(`LineAmountExcl` * (1+`TaxPercentage`)) as AmountIncl"])->where("InvoiceCode", $InvoiceCode)->groupBy("InvoiceCode")->execute();
            $sql_amount_excl = "HostFact_InvoiceElements.`LineAmountExcl` as AmountExcl";
            $sql_amount_incl = "HostFact_InvoiceElements.`LineAmountIncl` as AmountIncl";
        }
        $list["AmountIncl"] = $result ? $result->AmountIncl : 0;
        $list["AmountExcl"] = $result ? $result->AmountExcl : 0;
        $list["CountRows"] = $result ? $result->CountRows : 0;
        $element_list = Database_Model::getInstance()->get("HostFact_InvoiceElements", ["HostFact_InvoiceElements.*", "HostFact_Products.ProductName", "(HostFact_InvoiceElements.`PriceExcl` * (1+ROUND(HostFact_InvoiceElements.`TaxPercentage`,4))) as `PriceIncl` ", $sql_amount_excl, $sql_amount_incl])->join("HostFact_Products", "HostFact_Products.ProductCode = HostFact_InvoiceElements.ProductCode")->where("InvoiceCode", $InvoiceCode)->orderBy("HostFact_InvoiceElements.Ordering", "ASC")->orderBy("HostFact_InvoiceElements.id", "ASC")->execute();
        if($element_list) {
            foreach ($element_list as $result) {
                $list[$result->id] = ["id" => $result->id];
                foreach ($result as $key => $value) {
                    if(in_array($key, $this->Variables) || $key == "ProductName" || $key == "PriceIncl" || $key == "AmountExcl" || $key == "AmountIncl") {
                        $list[$result->id][$key] = htmlspecialchars($result->{$key} ?? "");
                    }
                }
                if(isset($list[$result->id]["Description"])) {
                    $pattern = "/\\[[A-Z]\\:(.*?)\\]/is";
                    $replacements = "";
                    $list[$result->id]["Description"] = preg_replace($pattern, $replacements, $list[$result->id]["Description"]);
                }
                $list[$result->id]["Date"] = $list[$result->id]["Date"] != "0000-00-00" ? rewrite_date_db2site($list[$result->id]["Date"]) : "";
                $list[$result->id]["StartPeriod"] = $list[$result->id]["StartPeriod"] != "0000-00-00" ? rewrite_date_db2site($list[$result->id]["StartPeriod"]) : "";
                $list[$result->id]["EndPeriod"] = $list[$result->id]["EndPeriod"] != "0000-00-00" ? rewrite_date_db2site($list[$result->id]["EndPeriod"]) : "";
                $number_of_decimals = min(5, max(2, strlen(substr(strrchr($list[$result->id]["PriceExcl"], "."), 1))));
                $list[$result->id]["PriceIncl"] = $this->VatCalcMethod == "incl" ? round(round((double) $list[$result->id]["PriceIncl"], 6), $number_of_decimals) : $list[$result->id]["PriceIncl"];
            }
        }
        return 0 < $list["CountRows"] ? $list : [];
    }
}

?>