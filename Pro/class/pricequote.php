<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class pricequote
{
    public $Identifier;
    public $PriceQuoteCode;
    public $Debtor;
    public $Date;
    public $Term;
    public $Discount;
    public $IgnoreDiscount;
    public $Coupon;
    public $ReferenceNumber;
    public $UsePriceQuoteAsReferenceNumber = false;
    public $CompanyName;
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
    public $PriceQuoteMethod;
    public $Template;
    public $SentDate;
    public $Sent;
    public $Status;
    public $TaxRate;
    public $Compound;
    public $AmountExcl;
    public $AmountIncl;
    public $VatCalcMethod;
    public $Comment;
    public $Reason;
    public $VatShift;
    public $AcceptName;
    public $AcceptEmailAddress;
    public $AcceptComment;
    public $AcceptSignatureBase64;
    public $AcceptDate;
    public $AcceptIPAddress;
    public $AcceptUserAgent;
    public $AcceptPDF;
    public $Description;
    public $Attachment;
    public $Elements;
    public $ExtraElement;
    public $CountRows;
    public $Table;
    public $Error;
    public $Warning;
    public $Success;
    public $Variables = ["Identifier", "PriceQuoteCode", "Debtor", "Date", "Term", "Discount", "IgnoreDiscount", "Coupon", "ReferenceNumber", "CompanyName", "Sex", "Initials", "SurName", "Address", "Address2", "ZipCode", "City", "State", "Country", "EmailAddress", "Authorisation", "PriceQuoteMethod", "Template", "Status", "TaxRate", "Compound", "Sent", "Description", "VatCalcMethod", "Comment", "Reason", "VatShift", "AcceptName", "AcceptEmailAddress", "AcceptComment", "AcceptSignatureBase64", "AcceptDate", "AcceptIPAddress", "AcceptUserAgent", "AcceptPDF"];
    public function __construct()
    {
        global $company;
        $this->Date = rewrite_date_db2site(date("Y-m-d H:i:s"));
        $this->Discount = "0";
        $this->Status = "0";
        $this->Authorisation = "no";
        $this->Country = isset($company->Country) && $company->Country ? $company->Country : "NL";
        $this->PriceQuoteMethod = STANDARD_INVOICEMETHOD;
        $this->Term = PRICEQUOTE_TERM;
        require_once "class/template.php";
        $template = new template();
        $this->Template = $template->getStandard("pricequote");
        if(!defined("PRICEQUOTE_STD_TEMPLATE")) {
            define("PRICEQUOTE_STD_TEMPLATE", $this->Template);
        }
        $this->Error = [];
        $this->Warning = [];
        $this->Success = [];
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
        if(370 <= str_replace(".", "", SOFTWARE_VERSION) && (!isset($_SESSION["custom_fields"]["pricequote"]) || $_SESSION["custom_fields"]["pricequote"])) {
            require_once "class/customfields.php";
            $customfields = new customfields();
            $this->customfields_list = $customfields->getCustomPriceQuoteFields();
        }
    }
    public function show()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for pricequote");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_PriceQuote", ["*", "DATE_ADD(`Date`,INTERVAL `Term` DAY) as ExpirationDate", "`Description` as `PriceQuoteDescription`", "MD5(CONCAT(id, PricequoteCode, Debtor, AmountIncl, `Created`)) as `AcceptURLKey`"])->where("id", $this->Identifier)->execute();
        if(!$result || empty($result->id)) {
            $this->Error[] = __("invalid identifier for pricequote");
            return false;
        }
        $this->AcceptURLRaw = CLIENTAREA_URL . "accept/" . htmlspecialchars("?pricequote=" . urlencode($result->PriceQuoteCode) . "&key=" . urlencode($result->AcceptURLKey));
        $this->AcceptURL = "<a href={&quot;}" . $this->AcceptURLRaw . "{&quot;}>" . $this->AcceptURLRaw . "</a>";
        foreach ($result as $key => $value) {
            if($key != "id") {
                $this->{$key} = htmlspecialchars($value);
            }
        }
        $elements = new pricequoteelement();
        $this->Elements = $elements->all($this->PriceQuoteCode);
        $this->Discount = $this->Discount * 100;
        $this->Name = $this->Initials . " " . $this->SurName;
        global $array_states;
        $this->CountryLong = countryCodeToLong($this->Country);
        $this->StateName = isset($array_states[$this->Country][$this->State]) ? $array_states[$this->Country][$this->State] : $this->State;
        $this->OldDebtor = $this->Debtor;
        if(!empty($this->customfields_list)) {
            $customfields = new customfields();
            $custom_values = $customfields->getCustomPriceQuoteFieldsValues($this->Identifier);
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
    public function format($euro = true)
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
        ksort($used_rates);
        $this->used_taxrates = $used_rates;
        $this->Date = rewrite_date_db2site($this->Date);
        $this->ExpirationDate = rewrite_date_db2site($this->ExpirationDate);
        $this->Description = nl2br($this->Description);
        return true;
    }
    public function add($discountcheck = true)
    {
        if($this->PriceQuoteMethod === false || $this->PriceQuoteMethod == "") {
            require_once "class/debtor.php";
            $debtor = new debtor();
            $debtor->Identifier = $this->Debtor;
            $debtor->show();
            $this->PriceQuoteMethod = $debtor->InvoiceMethod;
        }
        $elements = new pricequoteelement();
        $elements->VatCalcMethod = $this->VatCalcMethod;
        $this->Elements = $elements->all($this->PriceQuoteCode);
        foreach ($this->Elements as $k => $v) {
            if(is_numeric($k) && $v["Debtor"] != $this->Debtor) {
                $this->Error[] = sprintf(__("mixed debtor for pricequote"), $this->PriceQuoteCode);
                $this->recover_mixed_pricequotes();
                return false;
            }
        }
        if($discountcheck === true && (int) $this->IgnoreDiscount === 0) {
            require_once "class/discount.php";
            $discount = new discount();
            $discount->Type = "pricequote";
            $result = $discount->check($this->Debtor, $this->Elements, $this->Coupon, $this->Authorisation, $this->VatCalcMethod);
            if($result) {
                $totaal = isset($this->Elements["AmountExcl"]) ? $this->Elements["AmountExcl"] : 0;
                $discount_percentage = $this->Discount;
                foreach ($result as $value) {
                    $discount->Identifier = $value;
                    $discount->show();
                    $discount_percentage = $discount_percentage < $discount->DiscountPercentage ? $discount->DiscountPercentage : $discount_percentage;
                    if(!isEmptyFloat($discount->Discount)) {
                        $elements->PriceQuoteCode = $this->PriceQuoteCode;
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
            $this->Elements = $elements->all($this->PriceQuoteCode);
        }
        $this->Discount = round((double) number2db($this->Discount), 2) / 100;
        $this->Date = rewrite_date_site2db($this->Date);
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
            return false;
        }
        $result = Database_Model::getInstance()->insert("HostFact_PriceQuote", ["PriceQuoteCode" => $this->PriceQuoteCode, "Debtor" => $this->Debtor, "Date" => $this->Date, "Term" => $this->Term, "Discount" => $this->Discount, "IgnoreDiscount" => $this->IgnoreDiscount, "Coupon" => $this->Coupon, "ReferenceNumber" => $this->ReferenceNumber, "CompanyName" => $this->CompanyName, "Sex" => $this->Sex, "Initials" => $this->Initials, "SurName" => $this->SurName, "Address" => $this->Address, "Address2" => $this->Address2, "ZipCode" => $this->ZipCode, "City" => $this->City, "State" => $this->State, "Country" => $this->Country, "EmailAddress" => check_email_address($this->EmailAddress, "convert"), "Authorisation" => $this->Authorisation, "PriceQuoteMethod" => $this->PriceQuoteMethod, "Template" => $this->Template, "Status" => $this->Status, "Sent" => $this->Sent, "SentDate" => $this->SentDate, "TaxRate" => $this->TaxRate, "Compound" => $this->Compound, "AmountExcl" => $this->AmountExcl, "AmountIncl" => $this->AmountIncl, "Description" => $this->Description, "VatCalcMethod" => $this->VatCalcMethod, "Comment" => $this->Comment, "VatShift" => $this->VatShift])->execute();
        if($result) {
            $this->Identifier = $result;
            createLog("pricequote", $this->Identifier, "pricequote created");
            if(!empty($this->customfields_list)) {
                $customfields = new customfields();
                $customfields->setCustomPriceQuoteFieldsValues($this->Identifier, $this->customvalues);
            }
            $this->Success[] = sprintf(__("pricequote created"), $this->PriceQuoteCode);
            return true;
        }
        $elements = new pricequoteelement();
        $this->Elements = $elements->all($this->PriceQuoteCode);
        $this->Discount = $this->Discount * 100;
        $this->Date = rewrite_date_db2site($this->Date);
        return false;
    }
    public function changePriceQuoteCode($Identifier, $newPriceQuoteCode)
    {
        $priceQuoteData = Database_Model::getInstance()->getOne("HostFact_PriceQuote", ["PriceQuoteCode", "Status"])->where("id", $Identifier)->execute();
        if($priceQuoteData === false) {
            $this->Error[] = __("invalid identifier for pricequote");
            return false;
        }
        if($priceQuoteData->PriceQuoteCode == $newPriceQuoteCode) {
            return true;
        }
        Database_Model::getInstance()->update("HostFact_PriceQuote", ["PriceQuoteCode" => $newPriceQuoteCode])->where("id", $Identifier)->execute();
        $result = Database_Model::getInstance()->update("HostFact_PriceQuoteElements", ["PriceQuoteCode" => $newPriceQuoteCode])->where("PriceQuoteCode", $priceQuoteData->PriceQuoteCode)->execute();
        if($result) {
            return true;
        }
        return false;
    }
    public function changeDebtor($pricequoteId, $newDebtorId)
    {
        $priceQuoteCode = $this->getID("id", $pricequoteId);
        if($priceQuoteCode === false) {
            $this->Error[] = __("invalid identifier for price quote");
            return false;
        }
        require_once "class/debtor.php";
        $debtor = new debtor();
        $debtor->Identifier = $newDebtorId;
        if($debtor->show()) {
            $InvoiceDataForPriceQuote = $debtor->InvoiceDataForPriceQuote == "yes" ? true : false;
            Database_Model::getInstance()->update("HostFact_PriceQuote", ["Debtor" => $newDebtorId, "CompanyName" => $InvoiceDataForPriceQuote ? htmlspecialchars_decode($debtor->InvoiceCompanyName) : htmlspecialchars_decode($debtor->CompanyName), "Sex" => $InvoiceDataForPriceQuote ? htmlspecialchars_decode($debtor->InvoiceSex) : htmlspecialchars_decode($debtor->Sex), "Initials" => $InvoiceDataForPriceQuote ? htmlspecialchars_decode($debtor->InvoiceInitials) : htmlspecialchars_decode($debtor->Initials), "SurName" => $InvoiceDataForPriceQuote ? htmlspecialchars_decode($debtor->InvoiceSurName) : htmlspecialchars_decode($debtor->SurName), "Address" => $InvoiceDataForPriceQuote ? htmlspecialchars_decode($debtor->InvoiceAddress) : htmlspecialchars_decode($debtor->Address), "Address2" => $InvoiceDataForPriceQuote ? htmlspecialchars_decode($debtor->InvoiceAddress2) : htmlspecialchars_decode($debtor->Address2), "ZipCode" => $InvoiceDataForPriceQuote ? htmlspecialchars_decode($debtor->InvoiceZipCode) : htmlspecialchars_decode($debtor->ZipCode), "City" => $InvoiceDataForPriceQuote ? htmlspecialchars_decode($debtor->InvoiceCity) : htmlspecialchars_decode($debtor->City), "State" => $InvoiceDataForPriceQuote ? htmlspecialchars_decode($debtor->InvoiceState) : htmlspecialchars_decode($debtor->State), "Country" => $debtor->InvoiceCountry && $debtor->InvoiceAddress && $InvoiceDataForPriceQuote ? htmlspecialchars_decode($debtor->InvoiceCountry) : htmlspecialchars_decode($debtor->Country), "EmailAddress" => htmlspecialchars_decode(check_email_address($InvoiceDataForPriceQuote ? $debtor->InvoiceEmailAddress : $debtor->EmailAddress, "convert")), "Authorisation" => $debtor->InvoiceAuthorisation])->where("id", $pricequoteId)->execute();
            Database_Model::getInstance()->update("HostFact_PriceQuoteElements", ["Debtor" => $newDebtorId])->where("PriceQuoteCode", $priceQuoteCode)->execute();
        } else {
            $this->Error[] = __("invalid identifier for debtor");
        }
    }
    public function getID($method, $value, $debtor_id = false)
    {
        switch ($method) {
            case "pricequotecode":
                $invoice_id = Database_Model::getInstance()->getOne("HostFact_PriceQuote", ["id"])->where("PriceQuoteCode", $value)->execute();
                return $invoice_id !== false && 0 < $invoice_id->id ? $invoice_id->id : false;
                break;
            case "id":
                $priceQuoteCode = Database_Model::getInstance()->getOne("HostFact_PriceQuote", ["PriceQuoteCode"])->where("id", intval($value))->execute();
                return $priceQuoteCode !== false ? $priceQuoteCode->InvoiceCode : false;
                break;
            case "identifier":
                $invoice_id = Database_Model::getInstance()->getOne("HostFact_PriceQuote", ["id"])->where("id", intval($value))->execute();
                return $invoice_id !== false && 0 < $invoice_id->id ? $invoice_id->id : false;
                break;
            case "clientarea":
                $invoice_id = Database_Model::getInstance()->getOne("HostFact_PriceQuote", ["id"])->where("id", intval($value))->where("Debtor", $debtor_id)->where("Status", ["IN" => [2, 3, 4, 8]])->execute();
                return $invoice_id !== false && 0 < $debtor_id ? $invoice_id->id : false;
                break;
        }
    }
    public function edit()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for pricequote");
            return false;
        }
        if($this->PriceQuoteMethod === false || $this->PriceQuoteMethod == "") {
            require_once "class/debtor.php";
            $debtor = new debtor();
            $debtor->Identifier = $this->Debtor;
            $debtor->show();
            $this->PriceQuoteMethod = $debtor->InvoiceMethod;
        }
        $elements = new pricequoteelement();
        $elements->VatCalcMethod = $this->VatCalcMethod;
        $this->Elements = $elements->all($this->PriceQuoteCode);
        $discountcheck = true;
        if($discountcheck === true && (int) $this->IgnoreDiscount === 0) {
            require_once "class/discount.php";
            $discount = new discount();
            $discount->Type = "pricequote";
            $result = $discount->check($this->Debtor, $this->Elements, $this->Coupon, $this->Authorisation, $this->VatCalcMethod);
            if($result) {
                $totaal = $this->Elements["AmountExcl"];
                $discount_percentage = $this->Discount;
                foreach ($result as $value) {
                    $discount->Identifier = $value;
                    $discount->show();
                    $discount_percentage = $discount_percentage < $discount->DiscountPercentage ? $discount->DiscountPercentage : $discount_percentage;
                    if(!empty($discount->Discount)) {
                        $already_on_invoice = false;
                        foreach ($this->Elements as $k => $v) {
                            if(is_numeric($k) && $v["Description"] == $discount->Description) {
                                $already_on_invoice = true;
                            }
                        }
                        if($already_on_invoice !== true) {
                            $elements->PriceQuoteCode = $this->PriceQuoteCode;
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
            $this->Elements = $elements->all($this->PriceQuoteCode);
        }
        $this->Discount = round((double) number2db($this->Discount), 2) / 100;
        $this->Date = rewrite_date_site2db($this->Date);
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
        if($this->validate() === false) {
            $this->Discount = $this->Discount * 100;
            $this->Date = rewrite_date_db2site($this->Date);
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_PriceQuote", ["PriceQuoteCode" => $this->PriceQuoteCode, "Debtor" => $this->Debtor, "Date" => $this->Date, "Term" => $this->Term, "Discount" => $this->Discount, "IgnoreDiscount" => $this->IgnoreDiscount, "Coupon" => $this->Coupon, "ReferenceNumber" => $this->ReferenceNumber, "CompanyName" => $this->CompanyName, "Sex" => $this->Sex, "Initials" => $this->Initials, "SurName" => $this->SurName, "Address" => $this->Address, "Address2" => $this->Address2, "ZipCode" => $this->ZipCode, "City" => $this->City, "State" => $this->State, "Country" => $this->Country, "EmailAddress" => check_email_address($this->EmailAddress, "convert"), "Authorisation" => $this->Authorisation, "PriceQuoteMethod" => $this->PriceQuoteMethod, "Template" => $this->Template, "Status" => $this->Status, "Sent" => $this->Sent, "SentDate" => $this->SentDate, "TaxRate" => $this->TaxRate, "Compound" => $this->Compound, "AmountExcl" => $this->AmountExcl, "AmountIncl" => $this->AmountIncl, "Description" => $this->Description, "VatCalcMethod" => $this->VatCalcMethod, "Comment" => $this->Comment, "VatShift" => $this->VatShift])->where("id", $this->Identifier)->execute();
        if($result) {
            if($this->AcceptName && in_array($this->CurrentStatus, [3, 4]) && !in_array($this->Status, [3, 4])) {
                $attachment = new attachment();
                if(0 < $this->AcceptPDF) {
                    $attachment->deleteAttachment($this->AcceptPDF);
                }
                Database_Model::getInstance()->update("HostFact_PriceQuote", ["AcceptName" => "", "AcceptEmailAddress" => "", "AcceptComment" => "", "AcceptSignatureBase64" => "", "AcceptDate" => "", "AcceptIPAddress" => "", "AcceptUserAgent" => "", "AcceptPDF" => 0])->where("id", $this->Identifier)->execute();
            }
            createLog("pricequote", $this->Identifier, "pricequote adjusted");
            if(!empty($this->customfields_list)) {
                $customfields = new customfields();
                $customfields->setCustomPriceQuoteFieldsValues($this->Identifier, $this->customvalues);
            }
            $this->Success[] = sprintf(__("pricequote updated success"), $this->PriceQuoteCode);
            return true;
        }
        $elements = new pricequoteelement();
        $this->Elements = $elements->all($this->PriceQuoteCode);
        $this->Discount = $this->Discount * 100;
        $this->Date = rewrite_date_db2site($this->Date);
        return false;
    }
    public function changeStatus($log_action = NULL, $reason = "", $ip = "")
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for pricequote");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_PriceQuote", ["Status" => $this->Status, "Reason" => $reason, "IPAddress" => $ip])->where("id", $this->Identifier)->execute();
        if($result) {
            if($log_action) {
                $log_suffix = defined("API_CALL_FROM_CLIENTAREA") && API_CALL_FROM_CLIENTAREA === true ? " from clientarea" : "";
                switch ($log_action) {
                    case "accept":
                        createLog("pricequote", $this->Identifier, "pricequote accepted" . $log_suffix);
                        break;
                    case "decline":
                        createLog("pricequote", $this->Identifier, "pricequote declined" . $log_suffix);
                        break;
                }
            }
            if($this->Status == 3) {
                $action_info = ["id" => $this->Identifier, "Debtor" => $this->Debtor, "PriceQuoteCode" => $this->PriceQuoteCode];
                do_action("pricequote_is_accepted", $action_info);
            } elseif($this->Status == 8) {
                $action_info = ["id" => $this->Identifier, "Debtor" => $this->Debtor, "PriceQuoteCode" => $this->PriceQuoteCode];
                do_action("pricequote_is_declined", $action_info);
            }
            $this->Success[] = sprintf(__("pricequote updated success"), $this->PriceQuoteCode);
            return true;
        }
        return false;
    }
    public function acceptedWithSignature()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for pricequote");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_PriceQuote", ["AcceptName" => $this->AcceptName, "AcceptEmailAddress" => $this->AcceptEmailAddress, "AcceptComment" => $this->AcceptComment, "AcceptSignatureBase64" => $this->AcceptSignatureBase64, "AcceptDate" => ["RAW" => "NOW()"], "AcceptIPAddress" => $this->AcceptIPAddress, "AcceptUserAgent" => $this->AcceptUserAgent, "Status" => 3])->where("id", $this->Identifier)->execute();
        if($result) {
            if($this->printPriceQuote(false, true, true)) {
                $attachment_model = new attachment();
                $attachment_model->FileDir = $attachment_model->fileDir($this->Identifier, "pricequote_accepted");
                $attachment_model->FileType = "pricequote_accepted";
                $attachment_model->FilenameOriginal = preg_replace("/[^a-zA-Z0-9_\\-()\\.]/", "", $this->PriceQuoteCode) . "-" . __("signed") . ".pdf";
                $attachment_model->FileBase64 = base64_encode(file_get_contents("temp/" . $_SESSION["force_download"]));
                $attachment_model->Reference = $this->Identifier;
                $result_pdf = $attachment_model->saveBase64(true);
                if($result_pdf) {
                    $result_pdf = $attachment_model->getAttachmentInfoByFilename($attachment_model->FilenameOriginal, "pricequote_accepted", $this->Identifier);
                }
                if($result_pdf === false) {
                    Database_Model::getInstance()->update("HostFact_PriceQuote", ["AcceptName" => "", "AcceptEmailAddress" => "", "AcceptComment" => "", "AcceptSignature" => 0, "AcceptDate" => "", "AcceptIPAddress" => "", "AcceptUserAgent" => "", "Status" => 2])->where("id", $this->Identifier)->execute();
                    $this->Error[] = __("could not generate pricequote");
                    return false;
                }
                Database_Model::getInstance()->update("HostFact_PriceQuote", ["AcceptPDF" => $result_pdf])->where("id", $this->Identifier)->execute();
            }
            createLog("pricequote", $this->Identifier, "pricequote accepted online", [$this->AcceptName]);
            $action_info = ["id" => $this->Identifier, "Debtor" => $this->Debtor, "PriceQuoteCode" => $this->PriceQuoteCode];
            do_action("pricequote_is_accepted", $action_info);
            $this->Success[] = sprintf(__("pricequote updated success"), $this->PriceQuoteCode);
            return true;
        }
        return false;
    }
    public function changeComment($id, $comment = "")
    {
        if(!is_numeric($id)) {
            $this->Error[] = __("invalid identifier for pricequote");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_PriceQuote", ["Comment" => $comment])->where("id", $id)->execute();
        if($result) {
            createLog("pricequote", $id, "comment adjusted");
            $this->Success[] = __("comment adjusted");
            return true;
        }
        return false;
    }
    public function delete($id, $deleteType)
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for pricequote");
            return false;
        }
        if($deleteType == "remove") {
            $result1 = Database_Model::getInstance()->delete("HostFact_PriceQuoteElements")->where("PriceQuoteCode", ["IN" => ["RAW" => "SELECT `PriceQuoteCode` FROM `HostFact_PriceQuote` WHERE `id`=:pricequote_id"]])->bindValue("pricequote_id", $this->Identifier)->execute();
            $result2 = Database_Model::getInstance()->delete("HostFact_PriceQuote")->where("id", $this->Identifier)->execute();
            $result3 = Database_Model::getInstance()->delete("HostFact_Log")->where("Type", "pricequote")->where("Reference", $this->Identifier)->execute();
            Database_Model::getInstance()->delete("HostFact_Debtor_Custom_Values")->where("ReferenceType", "pricequote")->where("ReferenceID", $this->Identifier)->execute();
            if($result1 && $result2 && $result3) {
                $this->Success[] = sprintf(__("pricequote is removed"), $this->PriceQuoteCode);
                return true;
            }
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_PriceQuote", ["Status" => "9"])->where("id", $this->Identifier)->execute();
        if($result) {
            $this->Success[] = sprintf(__("pricequote is archived"), $this->PriceQuoteCode);
            return true;
        }
        return false;
    }
    public function sent($printnow = true, $download_instead = true)
    {
        require_once "class/debtor.php";
        $debtor = new debtor();
        $debtor->Identifier = $this->Debtor;
        if(!$debtor->show() || $debtor->Status == 9) {
            $this->Error[] = sprintf(__("debtor from pricequote cannot be found"), $this->PriceQuoteCode);
            return false;
        }
        $viaMail = (int) $this->PriceQuoteMethod === 0 || $this->PriceQuoteMethod == 3 || $this->PriceQuoteMethod == 4 ? true : false;
        $viaPost = $this->PriceQuoteMethod == 1 || $this->PriceQuoteMethod == 2 || $this->PriceQuoteMethod == 3 || $this->PriceQuoteMethod == 4 ? true : false;
        $dbAdjusted = false;
        if($viaMail === true) {
            require_once "class/email.php";
            $email = new email("pricequote", $this);
            $email->Recipient = $this->EmailAddress;
            if(!$email->Subject) {
                $email->Subject = sprintf(__("email subject send pricequote"), $this->PriceQuoteCode);
            }
            $email->Debtor = $this->Debtor;
            $objects = ["pricequote" => $this, "debtor" => $debtor];
            $email->add($objects);
            $email_sent = $email->sent("pricequote", $this->Identifier, false, $objects);
            if($email_sent) {
                $this->SentDate = rewrite_date_db2site($this->SentDate) != "-" && rewrite_date_db2site($this->SentDate) != "" ? $this->SentDate : date("YmdHis");
                $this->Status = $this->Status <= 2 ? 2 : $this->Status;
                Database_Model::getInstance()->update("HostFact_PriceQuote", ["Sent" => ["RAW" => "`Sent` + 1"], "SentDate" => $this->SentDate, "Status" => $this->Status])->where("id", $this->Identifier)->execute();
                $dbAdjusted = true;
                $logEmailAddress = check_email_address($this->EmailAddress, "convert", ", ");
                createLog("pricequote", $this->Identifier, "pricequote sent per email", $logEmailAddress);
                $this->Success[] = sprintf(__("pricequote sent success"), $this->PriceQuoteCode, $logEmailAddress);
            } elseif(0 < count($email->Error)) {
                $this->Error[] = sprintf(__("pricequote sent failed"), [$this->PriceQuoteCode, $logEmailAddress], implode("", $email->Error));
            }
        }
        if($viaPost === true) {
            if(!$dbAdjusted) {
                $this->SentDate = rewrite_date_db2site($this->SentDate) != "-" && rewrite_date_db2site($this->SentDate) != "" ? $this->SentDate : date("YmdHis");
                $this->Status = $this->Status <= 2 ? 2 : $this->Status;
                Database_Model::getInstance()->update("HostFact_PriceQuote", ["Sent" => ["RAW" => "`Sent` + 1"], "SentDate" => $this->SentDate, "Status" => $this->Status])->where("id", $this->Identifier)->execute();
            }
            createLog("pricequote", $this->Identifier, "pricequote sent per post");
            $this->Warning[] = sprintf(__("pricequote sent manual"), $this->PriceQuoteCode);
            if($viaMail === true) {
                $this->show();
            }
            if($this->PartOfBatch === false) {
                $this->printPriceQuote(false, $download_instead);
            } else {
                $this->BatchName = sprintf(__("pdf filename sent in batch pricequote"), rewrite_date_db2site(date("Y-m-d"))) . ".pdf";
                $this->printBatch(false, $download_instead);
            }
        }
        return true;
    }
    public function printPriceQuote($log = true, $download_instead = true, $show_accepted_data = false)
    {
        require_once "class/debtor.php";
        $debtor = new debtor();
        $debtor->Identifier = $this->Debtor;
        $debtor->show();
        $OutputType = "D";
        $type = "pricequote";
        require_once "class/pdf.php";
        $template = $this->Template;
        $pdf = new pdfCreator($template, ["pricequote" => $this, "debtor" => $debtor], "pricequote", "D", $download_instead, false, $show_accepted_data);
        $pdf->setOutputType($OutputType);
        if(!$pdf->generatePDF("F")) {
            $this->Error = array_merge($this->Error, $pdf->Error);
            return false;
        }
        if($log) {
            createLog("pricequote", $this->Identifier, "pricequote printed");
        }
        $_SESSION["force_download"] = $pdf->Name;
        return true;
    }
    public function printPriceQuoteFromClientArea($pricequote_id, $key)
    {
        error_reporting(E_NONE);
        $this->Identifier = $pricequote_id;
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
        $pdf = new pdfCreator($template, ["pricequote" => $this, "debtor" => $debtor], "pricequote", "D", true);
        $pdf->setOutputType($OutputType);
        if(!$pdf->generatePDF("F")) {
            $this->Error = array_merge($this->Error, $pdf->Error);
            return false;
        }
        createLog("pricequote", $this->Identifier, "pricequote printed from clientarea");
        if($pdf_file = file_get_contents("temp/" . $pdf->Name)) {
            echo json_encode(["filename" => $pdf->Name, "file" => base64_encode($pdf_file), "filesize" => filesize("temp/" . $pdf->Name)]);
            @unlink("temp/" . $pdf->Name);
            exit;
        }
        return false;
    }
    public function downloadBatchPDF($OutputType = "D")
    {
        if(!isset($this->pdf)) {
            return false;
        }
        $this->pdf->Name = isset($this->BatchName) && $this->BatchName ? $this->BatchName : __("pricequotes printed at") . " " . date("d-m-Y") . ".pdf";
        if(!$this->pdf->generatePDF($OutputType)) {
            $this->Error = array_merge($this->Error, $this->pdf->Error);
            return false;
        }
    }
    public function printBatch($logging = true, $download_instead = true)
    {
        require_once "class/debtor.php";
        $debtor = new debtor();
        $debtor->Identifier = $this->Debtor;
        $debtor->show();
        $OutputType = "D";
        require_once "class/pdf.php";
        if($logging) {
            createLog("pricequote", $this->Identifier, "pricequote printed");
        }
        if(!isset($this->pdf)) {
            $this->pdf = new pdfCreator($this->Template, ["pricequote" => $this, "debtor" => $debtor], "pricequote", "D", $download_instead);
        } else {
            $this->pdf->loadTemplate($this->Template);
            $this->pdf->createPages(["pricequote" => $this, "debtor" => $debtor]);
        }
        $this->pdf->setOutputType($OutputType);
        if($this->LastOfBatch) {
            $this->hasPrintQueue = false;
            $this->pdf->Name = isset($this->BatchName) && $this->BatchName ? $this->BatchName : __("pricequotes printed at") . " " . date("d-m-Y") . ".pdf";
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
    public function makeInvoice()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for pricequote");
            return false;
        }
        if(!$this->show()) {
            return false;
        }
        if($this->UsePriceQuoteAsReferenceNumber) {
            $this->ReferenceNumber = $this->PriceQuoteCode;
        }
        if($this->Status == 4) {
            $this->Error[] = __("invoice alreay created");
            return false;
        }
        foreach ($this->Variables as $k) {
            if(is_string($this->{$k})) {
                $this->{$k} = htmlspecialchars_decode($this->{$k});
            }
        }
        require_once "class/invoice.php";
        $invoice = new invoice();
        foreach ($invoice->Variables as $value2) {
            if(isset($this->{$value2}) && $value2 != "Identifier") {
                $invoice->{$value2} = $this->{$value2};
            }
        }
        $invoice->InvoiceCode = $invoice->newConceptCode();
        $invoice->Status = 0;
        require_once "class/debtor.php";
        $debtor = new debtor();
        $debtor->Identifier = $this->Debtor;
        $debtor->show();
        $invoice->CompanyName = $debtor->InvoiceCompanyName ? htmlspecialchars_decode($debtor->InvoiceCompanyName) : htmlspecialchars_decode($debtor->CompanyName);
        $invoice->TaxNumber = htmlspecialchars_decode($debtor->TaxNumber);
        $invoice->Sex = $debtor->InvoiceSurName ? htmlspecialchars_decode($debtor->InvoiceSex) : htmlspecialchars_decode($debtor->Sex);
        $invoice->Initials = $debtor->InvoiceInitials ? htmlspecialchars_decode($debtor->InvoiceInitials) : htmlspecialchars_decode($debtor->Initials);
        $invoice->SurName = $debtor->InvoiceSurName ? htmlspecialchars_decode($debtor->InvoiceSurName) : htmlspecialchars_decode($debtor->SurName);
        $invoice->Address = $debtor->InvoiceAddress ? htmlspecialchars_decode($debtor->InvoiceAddress) : htmlspecialchars_decode($debtor->Address);
        $invoice->ZipCode = $debtor->InvoiceZipCode ? htmlspecialchars_decode($debtor->InvoiceZipCode) : htmlspecialchars_decode($debtor->ZipCode);
        $invoice->City = $debtor->InvoiceCity ? htmlspecialchars_decode($debtor->InvoiceCity) : htmlspecialchars_decode($debtor->City);
        $invoice->Country = $debtor->InvoiceCountry && $debtor->InvoiceAddress ? htmlspecialchars_decode($debtor->InvoiceCountry) : htmlspecialchars_decode($debtor->Country);
        $invoice->EmailAddress = htmlspecialchars_decode(check_email_address($debtor->InvoiceEmailAddress ? $debtor->InvoiceEmailAddress : $debtor->EmailAddress, "convert"));
        if(0 < $debtor->InvoiceTemplate) {
            $invoice->Template = $debtor->InvoiceTemplate;
        } else {
            require_once "class/template.php";
            $template = new template();
            $invoice->Template = $template->getStandard("invoice");
        }
        $invoice->Authorisation = $debtor->InvoiceAuthorisation;
        $h_domain = "";
        $h_domainID = 0;
        $h_hosting = 0;
        $result_invoice = true;
        foreach ($this->Elements as $key => $value2) {
            if(is_numeric($key)) {
                $invoiceelement = new invoiceelement();
                $invoiceelement->VatCalcMethod = $invoice->VatCalcMethod;
                foreach ($invoiceelement->Variables as $key3 => $value3) {
                    if(isset($this->Elements[$key][$value3])) {
                        $invoiceelement->{$value3} = htmlspecialchars_decode($this->Elements[$key][$value3]);
                    }
                }
                $invoiceelement->InvoiceCode = $invoice->InvoiceCode;
                $invoiceelement->DiscountPercentage = $invoiceelement->DiscountPercentage * 100;
                if($invoiceelement->Date != "") {
                    $invoiceelement->Date = rewrite_date_db2site(date("Ymd"));
                }
                if(isset($invoiceelement->StartPeriod) && $invoiceelement->StartPeriod) {
                    $invoiceelement->StartPeriod = rewrite_date_db2site(date("Ymd"));
                    $invoiceelement->EndPeriod = "";
                }
                if(!$invoiceelement->add()) {
                    $invoice->Error = array_merge($invoiceelement->Error, $invoice->Error);
                    $invoice->Warning = array_merge($invoiceelement->Warning, $invoice->Warning);
                    $invoice->Success = array_merge($invoiceelement->Success, $invoice->Success);
                    $result_invoice = false;
                }
                $h_domain = $invoiceelement->Domain ? $invoiceelement->Domain : $h_domain;
                $h_domainID = $invoiceelement->Domain ? $invoiceelement->DomainID : $h_domainID;
                $h_hosting = $invoiceelement->Hosting ? $invoiceelement->Hosting : $h_hosting;
            }
        }
        if($result_invoice) {
            $invoice->Sent = 0;
            $invoice->SentDate = "";
            $invoice->Term = 0 < $debtor->InvoiceTerm ? $debtor->InvoiceTerm : INVOICE_TERM;
            $invoice->Date = rewrite_date_db2site(date("Ymd"));
            $invoice->InvoiceMethod = $this->PriceQuoteMethod;
            if(!empty($invoice->customfields_list) && !empty($this->customfields_list)) {
                $invoice->getDefaultCustomValuesWithDebtorSync();
                foreach ($this->customvalues as $_field => $_value) {
                    if(isset($invoice->customvalues[$_field])) {
                        $invoice->customvalues[$_field] = $_value;
                    }
                }
            }
            $result_invoice = $invoice->add(false, $this->PriceQuoteCode);
            if($result_invoice) {
                $invoice->Success = [];
                $this->Success[] = __("pricequote converted");
            }
        }
        $this->Error = array_merge($invoice->Error, $this->Error);
        $this->Warning = array_merge($invoice->Warning, $this->Warning);
        $this->Success = array_merge($invoice->Success, $this->Success);
        if(!$result_invoice) {
            if(0 < $invoice->Identifier) {
                Database_Model::getInstance()->delete("HostFact_Invoice")->where("id", $invoice->Identifier)->execute();
            }
            $invoiceelement = new invoiceelement();
            $invoiceelements = $invoiceelement->all($invoice->InvoiceCode);
            foreach ($invoiceelements as $k => $v) {
                if(is_numeric($k)) {
                    $invoiceelement->Identifier = $k;
                    $invoiceelement->delete();
                    if(0 < $v["PeriodicID"]) {
                        require_once "class/periodic.php";
                        $periodic = new periodic();
                        $periodic->delete($v["PeriodicID"]);
                    }
                }
            }
            return false;
        } else {
            $this->InvoiceID = $invoice->Identifier;
            $this->Status = 4;
            $result = Database_Model::getInstance()->update("HostFact_PriceQuote", ["Status" => $this->Status])->where("id", $this->Identifier)->execute();
            if($result) {
                createLog("pricequote", $this->Identifier, "invoice x created", [$invoice->InvoiceCode]);
                return true;
            }
            return false;
        }
    }
    public function newPriceQuoteCode($prefix = PRICEQUOTECODE_PREFIX, $number = PRICEQUOTECODE_NUMBER)
    {
        $prefix = parsePrefixVariables($prefix);
        $length = strlen($prefix . $number);
        $result = Database_Model::getInstance()->getOne("HostFact_PriceQuote", ["PriceQuoteCode"])->where("PriceQuoteCode", ["LIKE" => $prefix . "%"])->where("LENGTH(`PriceQuoteCode`)", [">=" => $length])->where("(SUBSTR(`PriceQuoteCode`,:PrefixLength)*1)", [">" => 0])->where("SUBSTR(`PriceQuoteCode`,:PrefixLength)", ["REGEXP" => "^[0-9]+\$"])->orderBy("(SUBSTR(`PriceQuoteCode`,:PrefixLength)*1)", "DESC")->bindValue("PrefixLength", strlen($prefix) + 1)->execute();
        $result2 = Database_Model::getInstance()->getOne("HostFact_PriceQuoteElements", ["PriceQuoteCode"])->where("PriceQuoteCode", ["LIKE" => $prefix . "%"])->where("LENGTH(`PriceQuoteCode`)", [">=" => $length])->where("(SUBSTR(`PriceQuoteCode`,:PrefixLength)*1)", [">" => 0])->where("SUBSTR(`PriceQuoteCode`,:PrefixLength)", ["REGEXP" => "^[0-9]+\$"])->orderBy("(SUBSTR(`PriceQuoteCode`,:PrefixLength)*1)", "DESC")->bindValue("PrefixLength", strlen($prefix) + 1)->execute();
        if(!$result || isset($result2->PriceQuoteCode) && substr($result->PriceQuoteCode, strlen($prefix)) < substr($result2->PriceQuoteCode, strlen($prefix))) {
            $result = $result2;
        }
        if(isset($result->PriceQuoteCode) && $result->PriceQuoteCode && is_numeric(substr($result->PriceQuoteCode, strlen($prefix)))) {
            $Code = substr($result->PriceQuoteCode, strlen($prefix));
            $Code = $prefix . @str_repeat("0", @max(@strlen($number) - @strlen(@max($Code + 1, $number)), 0)) . max($Code + 1, $number);
        } else {
            $Code = $prefix . $number;
        }
        if(!$this->is_free($Code)) {
            $this->Error[] = sprintf(__("pricequotecode generation failed"), $Code);
        }
        return !empty($this->Error) ? false : $Code;
    }
    public function recover_mixed_pricequotes()
    {
        if($this->is_free($this->PriceQuoteCode)) {
            foreach ($this->Elements as $k => $v) {
                if(is_numeric($k) && $v["Debtor"] == $this->Debtor) {
                    Database_Model::getInstance()->delete("HostFact_PriceQuoteElements")->where("id", $v["id"])->where("Debtor", $this->Debtor)->execute();
                }
            }
        }
    }
    public function validate()
    {
        global $array_pricequotestatus;
        if(!$this->is_free($this->PriceQuoteCode)) {
            $this->Error[] = __("invalid pricequote");
        }
        if(!array_key_exists($this->Status, $array_pricequotestatus)) {
            $this->Error[] = __("invalid status");
        }
        if(!is_numeric($this->Debtor) || empty($this->Debtor)) {
            $this->Error[] = __("invalid debtor");
            return false;
        }
        if(!trim($this->CompanyName) && !trim($this->SurName)) {
            $this->Error[] = __("no companyname and no surname are given");
        }
        if(!is_date(rewrite_date_site2db($this->Date))) {
            $this->Error[] = __("invalid price quote date2");
        }
        if(!is_numeric($this->Term)) {
            $this->Error[] = __("invalid term");
        }
        if(!is_numeric($this->Discount) || $this->Discount < 0 || 1 < $this->Discount) {
            $this->Error[] = __("invalid discount");
        } elseif($this->Discount && 2 < strlen(substr(strrchr($this->Discount * 100, "."), 1))) {
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
            $this->Error[] = __("invalid emailaddress");
        }
        global $array_pricequotemethod;
        if(!array_key_exists($this->PriceQuoteMethod, $array_pricequotemethod)) {
            $this->Error[] = __("invalid pricequotemethod");
        }
        if(!is_numeric($this->Template)) {
            $this->Error[] = __("invalid template");
        }
        if(!is_numeric($this->AmountExcl)) {
            $this->Error[] = __("invalid amountexcl");
        }
        if(!is_numeric($this->AmountIncl)) {
            $this->Error[] = __("invalid amountincl");
        }
        if((!isset($this->Elements["CountRows"]) || (int) $this->Elements["CountRows"] === 0) && empty($this->Error)) {
            $this->Error[] = sprintf(__("no pricequote elements"), $this->PriceQuoteCode);
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
        if(strlen($this->SentDate) && substr($this->SentDate, 0, 4) != "0000" && !is_date(rewrite_date_site2db($this->SentDate))) {
            $this->Error[] = __("invalid sentdate");
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
        if(($this->PriceQuoteMethod == "0" || $this->PriceQuoteMethod == "3") && !$this->EmailAddress) {
            global $array_pricequotemethod;
            $this->Warning[] = sprintf(__("pricequotemethod changed because mailaddress is unknown"), $array_pricequotemethod[$this->PriceQuoteMethod], $array_pricequotemethod[1]);
            $this->PriceQuoteMethod = 1;
        }
        return true;
    }
    public function is_free($PriceQuoteCode)
    {
        if($PriceQuoteCode) {
            $result = Database_Model::getInstance()->getOne("HostFact_PriceQuote", ["id"])->where("PriceQuoteCode", $PriceQuoteCode)->execute();
            if(!$result || $result->id == $this->Identifier) {
                return true;
            }
            return false;
        }
        return false;
    }
    public function changesendmethod($new_emailaddress = false)
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for pricequote");
            return false;
        }
        if($new_emailaddress !== false && (check_email_address($new_emailaddress) === false || !$new_emailaddress)) {
            $this->Error[] = __("invalid emailaddress");
            return false;
        }
        if($new_emailaddress !== false) {
            Database_Model::getInstance()->update("HostFact_PriceQuote", ["EmailAddress" => check_email_address($new_emailaddress, "convert")])->where("id", $this->Identifier)->execute();
        }
        if($this->PriceQuoteMethod == "0" || $this->PriceQuoteMethod == "3") {
            $result = Database_Model::getInstance()->getOne("HostFact_PriceQuote", ["EmailAddress", "Debtor"])->where("id", $this->Identifier)->execute();
            if(!$result->EmailAddress) {
                $result = Database_Model::getInstance()->getOne("HostFact_Debtors", ["EmailAddress", "InvoiceEmailAddress"])->where("id", $result->Debtor)->execute();
                if(!$result->EmailAddress && !$result->InvoiceEmailAddress) {
                    $this->Error[] = sprintf(__("pricequotemethod pricequote not changed, no valid mailaddress"), $this->InvoiceCode);
                    return false;
                }
                Database_Model::getInstance()->update("HostFact_PriceQuote", ["EmailAddress" => $result->InvoiceEmailAddress ? $result->InvoiceEmailAddress : $result->EmailAddress])->where("id", $this->Identifier)->execute();
            }
        }
        $result = Database_Model::getInstance()->update("HostFact_PriceQuote", ["PriceQuoteMethod" => $this->PriceQuoteMethod])->where("id", $this->Identifier)->execute();
        if($result) {
            global $array_invoicemethod;
            if($this->PriceQuoteMethod == "0" || $this->PriceQuoteMethod == "3") {
                createLog("pricequote", $this->Identifier, "pricequotemethod and emailaddress changed", [strtolower($array_invoicemethod[$this->PriceQuoteMethod]), check_email_address($new_emailaddress, "convert", ", ")]);
            } else {
                createLog("pricequote", $this->Identifier, "pricequotemethod changed", $array_invoicemethod[$this->PriceQuoteMethod]);
            }
            $this->Success[] = sprintf(__("pricequotemethod pricequote changed"), $this->PriceQuoteCode);
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
            $item_ids = $this->CustomFields->searchCustomFieldsByValue($searchfor, "pricequote");
        }
        $select = ["HostFact_PriceQuote.id", "DATE_ADD(HostFact_PriceQuote.`Date`,INTERVAL HostFact_PriceQuote.`Term` DAY) as `ExpirationDate`"];
        foreach ($fields as $column) {
            if($column == "ExpirationDate") {
            } elseif(in_array($column, $DebtorArray)) {
                $select[] = "HostFact_Debtors.`" . $column . "`";
            } elseif(in_array($column, $ElementArray)) {
                $select[] = "HostFact_PriceQuoteElements.`" . $column . "`";
            } else {
                $select[] = "HostFact_PriceQuote.`" . $column . "`";
            }
        }
        if($ElementFields || $ElementSearch) {
            Database_Model::getInstance()->get(["HostFact_PriceQuote", "HostFact_PriceQuoteElements"], $select)->where("HostFact_PriceQuoteElements.`PriceQuoteCode`=HostFact_PriceQuote.`PriceQuoteCode`")->groupBy("HostFact_PriceQuote.`PriceQuoteCode`");
        } else {
            Database_Model::getInstance()->get("HostFact_PriceQuote", $select);
        }
        if($DebtorFields || $DebtorSearch) {
            Database_Model::getInstance()->join("HostFact_Debtors", "HostFact_Debtors.`id` = HostFact_PriceQuote.`Debtor`");
        }
        if(!empty($search_at)) {
            $or_clausule = [];
            foreach ($search_at as $searchColumn) {
                if(in_array($searchColumn, ["Debtor"])) {
                    $or_clausule[] = ["HostFact_PriceQuote.`" . $searchColumn . "`", $searchfor];
                } elseif(in_array($searchColumn, $DebtorArray)) {
                    $or_clausule[] = ["HostFact_Debtors.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                } elseif(in_array($searchColumn, ["PeriodicID"])) {
                    $or_clausule[] = ["HostFact_PriceQuoteElements.`" . $searchColumn . "`", $searchfor];
                } elseif(in_array($searchColumn, $ElementArray)) {
                    $or_clausule[] = ["HostFact_PriceQuoteElements.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                } elseif($searchColumn == "CustomFieldValue") {
                    if($item_ids && is_array($item_ids) && 0 < count($item_ids)) {
                        $or_clausule[] = ["HostFact_PriceQuote.`id`", ["IN" => $item_ids]];
                    }
                } else {
                    $or_clausule[] = ["HostFact_PriceQuote.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
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
            Database_Model::getInstance()->orderBy("HostFact_PriceQuoteElements.`" . $sort . "`", $order);
        } elseif($sort == "PriceQuoteCode") {
            $order = $order ? $order : "DESC";
            Database_Model::getInstance()->orderBy("IF(SUBSTRING(HostFact_PriceQuote.`PriceQuoteCode`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(HostFact_PriceQuote.`PriceQuoteCode`,1,1))", $order)->orderBy("LENGTH(HostFact_PriceQuote.`PriceQuoteCode`)", $order)->orderBy("HostFact_PriceQuote.`PriceQuoteCode`", $order);
        } elseif($sort == "Date` ASC, `PriceQuoteCode") {
            Database_Model::getInstance()->orderBy("HostFact_PriceQuote.`Date`", "ASC")->orderBy("IF(SUBSTRING(HostFact_PriceQuote.`PriceQuoteCode`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(HostFact_PriceQuote.`PriceQuoteCode`,1,1))", "ASC")->orderBy("LENGTH(HostFact_PriceQuote.`PriceQuoteCode`)", "ASC")->orderBy("HostFact_PriceQuote.`PriceQuoteCode`", "ASC");
        } elseif($sort == "Date` DESC, `PriceQuoteCode") {
            Database_Model::getInstance()->orderBy("HostFact_PriceQuote.`Date`", "DESC")->orderBy("IF(SUBSTRING(HostFact_PriceQuote.`PriceQuoteCode`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(HostFact_PriceQuote.`PriceQuoteCode`,1,1))", "DESC")->orderBy("LENGTH(HostFact_PriceQuote.`PriceQuoteCode`)", "DESC")->orderBy("HostFact_PriceQuote.`PriceQuoteCode`", "DESC");
        } elseif($sort) {
            Database_Model::getInstance()->orderBy("HostFact_PriceQuote." . $sort, $order);
        } else {
            $order = $order ? $order : "DESC";
            Database_Model::getInstance()->orderBy("IF(SUBSTRING(HostFact_PriceQuote.`PriceQuoteCode`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(HostFact_PriceQuote.`PriceQuoteCode`,1,1))", $order)->orderBy("LENGTH(HostFact_PriceQuote.`PriceQuoteCode`)", $order)->orderBy("HostFact_PriceQuote.`PriceQuoteCode`", $order);
        }
        if(0 <= $limit) {
            Database_Model::getInstance()->limit((max(1, $limit) - 1) * $show_results, $show_results);
        }
        if(is_string($group) && 1 < count(explode("|", $group))) {
            $group = explode("|", $group);
            $or_clausule = [];
            foreach ($group as $status) {
                $or_clausule[] = ["HostFact_PriceQuote.`Status`", $status];
            }
            Database_Model::getInstance()->orWhere($or_clausule);
        } elseif(is_numeric($group)) {
            Database_Model::getInstance()->where("HostFact_PriceQuote.`Status`", $group);
        } else {
            Database_Model::getInstance()->where("HostFact_PriceQuote.`Status`", ["!=" => 9]);
        }
        if(isset($this->BeginDate) && isset($this->EndDate)) {
            Database_Model::getInstance()->where("HostFact_PriceQuote.Date", ["BETWEEN" => [$this->BeginDate, $this->EndDate]]);
        }
        if(!empty($filters)) {
            foreach ($filters as $_db_field => $_db_value) {
                switch ($_db_field) {
                    case "debtor":
                        if(is_numeric($_db_value) && 0 < $_db_value) {
                            Database_Model::getInstance()->where("HostFact_PriceQuote.Debtor", $_db_value);
                        }
                        break;
                    case "created":
                        if(isset($_db_value["from"]) && $_db_value["from"]) {
                            Database_Model::getInstance()->where("HostFact_PriceQuote.Created", [">=" => $_db_value["from"]]);
                        }
                        if(isset($_db_value["to"]) && $_db_value["to"]) {
                            Database_Model::getInstance()->where("HostFact_PriceQuote.Created", ["<=" => $_db_value["to"]]);
                        }
                        break;
                    case "modified":
                        if(isset($_db_value["from"]) && $_db_value["from"]) {
                            Database_Model::getInstance()->where("HostFact_PriceQuote.Modified", [">=" => $_db_value["from"]]);
                        }
                        if(isset($_db_value["to"]) && $_db_value["to"]) {
                            Database_Model::getInstance()->where("HostFact_PriceQuote.Modified", ["<=" => $_db_value["to"]]);
                        }
                        break;
                    case "date":
                        if(isset($_db_value["from"]) && $_db_value["from"]) {
                            Database_Model::getInstance()->where("HostFact_PriceQuote.Date", [">=" => $_db_value["from"]]);
                        }
                        if(isset($_db_value["to"]) && $_db_value["to"]) {
                            Database_Model::getInstance()->where("HostFact_PriceQuote.Date", ["<=" => $_db_value["to"]]);
                        }
                        break;
                    case "expirationdate":
                        if(isset($_db_value["from"]) && $_db_value["from"]) {
                            Database_Model::getInstance()->where("DATE_ADD(HostFact_PriceQuote.`Date`,INTERVAL HostFact_PriceQuote.`Term` DAY)", [">=" => $_db_value["from"]]);
                        }
                        if(isset($_db_value["to"]) && $_db_value["to"]) {
                            Database_Model::getInstance()->where("DATE_ADD(HostFact_PriceQuote.`Date`,INTERVAL HostFact_PriceQuote.`Term` DAY)", ["<=" => $_db_value["to"]]);
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
            $list["CountRows"] = Database_Model::getInstance()->rowCountWithIDOptimalisation(["HostFact_PriceQuote", "HostFact_PriceQuoteElements"], "HostFact_PriceQuote.id", "HostFact_PriceQuote.id");
            $this->CountRows = $list["CountRows"];
        } else {
            $list["CountRows"] = Database_Model::getInstance()->rowCountWithIDOptimalisation("HostFact_PriceQuote", "HostFact_PriceQuote.id", "HostFact_PriceQuote.id");
            $this->CountRows = $list["CountRows"];
        }
        if($invoice_list = Database_Model::getInstance()->execute()) {
            foreach ($invoice_list as $result) {
                $list[$result->id] = ["id" => $result->id];
                foreach ($fields as $column) {
                    $list[$result->id][$column] = htmlspecialchars($result->{$column});
                }
                if(isset($result->AmountIncl)) {
                    $list["TotalAmountIncl"] += number_format($result->AmountIncl, 2, ".", "");
                }
                if(isset($result->AmountExcl)) {
                    $list["TotalAmountExcl"] += number_format($result->AmountExcl, 2, ".", "");
                }
            }
        }
        return $list;
    }
    public function getDefaultCustomValuesWithDebtorSync()
    {
        $customfields = new customfields();
        if($customValues = $customfields->getCustomPriceQuoteFieldsValues(false)) {
            foreach ($customValues as $field => $valueArray) {
                $this->customvalues[$field] = $valueArray["Value"];
            }
        }
        $debtor_custom_values = $customfields->preFillCustomFields("debtor", $this->Debtor, "pricequote");
        $this->customvalues = is_array($this->customvalues) ? array_merge($this->customvalues, $debtor_custom_values) : $debtor_custom_values;
    }
}
class pricequoteelement
{
    public $Identifier;
    public $PriceQuoteCode;
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
    public $AmountIncl;
    public $AmountTax;
    public $AmountExcl;
    public $PeriodPriceIncl;
    public $PeriodPriceTax;
    public $PeriodPriceExcl;
    public $PriceIncl;
    public $PriceTax;
    public $Ordering;
    public $CountRows;
    public $Error;
    public $Warning;
    public $Success;
    public $Variables = ["Identifier", "PriceQuoteCode", "Debtor", "Date", "Number", "NumberSuffix", "ProductCode", "Description", "PriceExcl", "TaxPercentage", "DiscountPercentage", "DiscountPercentageType", "Periods", "Periodic", "PeriodicID", "StartPeriod", "EndPeriod", "Ordering"];
    public function __construct()
    {
        $this->Number = 1;
        $this->Periods = 1;
        $this->Ordering = 0;
        $this->Date = rewrite_date_db2site(date("Y-m-d H:i:s"));
        $this->Error = [];
        $this->Warning = [];
        $this->Success = [];
        $this->DiscountPercentage = "";
        $this->DiscountPercentageType = "line";
        $this->NumberSuffix = "";
        $this->TaxPercentage = STANDARD_TAX;
    }
    public function show()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for pricequote element");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_PriceQuoteElements", ["HostFact_PriceQuoteElements.*", "HostFact_Products.ProductName", "HostFact_Products.ProductKeyPhrase", "HostFact_Products.ProductDescription"])->join("HostFact_Products", "HostFact_Products.ProductCode = HostFact_PriceQuoteElements.ProductCode")->where("HostFact_PriceQuoteElements.id", $this->Identifier)->execute();
        if(!$result || empty($result->id)) {
            $this->Error[] = __("invalid identifier for pricequote element");
            return false;
        }
        foreach ($result as $key => $value) {
            if($key != "id") {
                $this->{$key} = htmlspecialchars($value);
            }
        }
        if(!isset($this->VatCalcMethod)) {
            $vat_calc_method = Database_Model::getInstance()->getOne("HostFact_PriceQuote", ["VatCalcMethod"])->where("PriceQuoteCode", $this->PriceQuoteCode)->execute();
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
        $this->TaxPercentage = btwcheck($this->Debtor, $this->TaxPercentage);
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
        $line_amount = getLineAmount($this->VatCalcMethod, $this->PriceExcl, $this->Periods, $this->Number, $this->TaxPercentage, $this->DiscountPercentage);
        $result = Database_Model::getInstance()->insert("HostFact_PriceQuoteElements", ["PriceQuoteCode" => $this->PriceQuoteCode, "Debtor" => $this->Debtor, "Date" => $this->Date, "Number" => $this->Number, "NumberSuffix" => $this->NumberSuffix, "ProductCode" => $this->ProductCode, "Description" => $this->Description, "PriceExcl" => $this->PriceExcl, "TaxPercentage" => $this->TaxPercentage, "DiscountPercentage" => $this->DiscountPercentage, "DiscountPercentageType" => $this->DiscountPercentageType, "Periods" => $this->Periods, "Periodic" => $this->Periodic, "PeriodicID" => $this->PeriodicID, "StartPeriod" => substr($this->StartPeriod, 0, 4) != "DATE" ? $this->StartPeriod : ["RAW" => $this->StartPeriod], "EndPeriod" => substr($this->EndPeriod, 0, 4) != "DATE" ? $this->EndPeriod : ["RAW" => $this->EndPeriod], "Ordering" => $this->Ordering, "LineAmountExcl" => $line_amount["excl"], "LineAmountIncl" => $line_amount["incl"]])->execute();
        if($result) {
            $this->Identifier = $result;
            return true;
        }
        return false;
    }
    public function edit()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for pricequote element");
            return false;
        }
        $this->OldNumber = $this->Number;
        $this->Number = deformat_money($this->Number);
        if(is_numeric($this->Number) && isEmptyFloat($this->Number) || !$this->Number) {
            Database_Model::getInstance()->delete("HostFact_PriceQuoteElements")->where("id", $this->Identifier)->execute();
            return true;
        }
        $this->TaxPercentage = btwcheck($this->Debtor, $this->TaxPercentage);
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
        $line_amount = getLineAmount($this->VatCalcMethod, $this->PriceExcl, $this->Periods, $this->Number, $this->TaxPercentage, $this->DiscountPercentage);
        $result = Database_Model::getInstance()->update("HostFact_PriceQuoteElements", ["PriceQuoteCode" => $this->PriceQuoteCode, "Debtor" => $this->Debtor, "Date" => $this->Date, "Number" => $this->Number, "NumberSuffix" => $this->NumberSuffix, "ProductCode" => $this->ProductCode, "Description" => $this->Description, "PriceExcl" => $this->PriceExcl, "TaxPercentage" => $this->TaxPercentage, "DiscountPercentage" => $this->DiscountPercentage, "DiscountPercentageType" => $this->DiscountPercentageType, "Periods" => $this->Periods, "Periodic" => $this->Periodic, "PeriodicID" => $this->PeriodicID, "StartPeriod" => substr($this->StartPeriod, 0, 4) != "DATE" ? $this->StartPeriod : ["RAW" => $this->StartPeriod], "EndPeriod" => substr($this->EndPeriod, 0, 4) != "DATE" ? $this->EndPeriod : ["RAW" => $this->EndPeriod], "Ordering" => $this->Ordering, "LineAmountExcl" => $line_amount["excl"], "LineAmountIncl" => $line_amount["incl"]])->where("id", $this->Identifier)->execute();
        if($result) {
            return true;
        }
        return false;
    }
    public function updatePrice($VatCalcMethod)
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for pricequote element");
            return false;
        }
        $this->PriceExcl = deformat_money($this->PriceExcl);
        $line_amount = getLineAmount($VatCalcMethod, $this->PriceExcl, $this->Periods, $this->Number, $this->TaxPercentage, $this->DiscountPercentage);
        $result = Database_Model::getInstance()->update("HostFact_PriceQuoteElements", ["PriceExcl" => $this->PriceExcl, "LineAmountExcl" => $line_amount["excl"], "LineAmountIncl" => $line_amount["incl"]])->where("id", $this->Identifier)->execute();
        if($result) {
            return true;
        }
        return false;
    }
    public function delete()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for pricequote element");
            return false;
        }
        $result = Database_Model::getInstance()->delete("HostFact_PriceQuoteElements")->where("id", $this->Identifier)->execute();
        if($result) {
            return true;
        }
        return false;
    }
    public function is_free($PriceQuoteCode)
    {
        if($PriceQuoteCode) {
            $result = Database_Model::getInstance()->getOne("HostFact_PriceQuoteElements", ["Debtor"])->where("PriceQuoteCode", $PriceQuoteCode)->execute();
            if(!$result || $result->Debtor == $this->Debtor || isset($this->OldDebtor) && 0 < $this->OldDebtor && $this->OldDebtor == $result->Debtor) {
                return true;
            }
            return false;
        }
        return false;
    }
    public function validate()
    {
        if(!$this->is_free($this->PriceQuoteCode)) {
            $this->Error[] = __("element conflict with pricequotecode");
            return false;
        }
        if(!is_numeric($this->Debtor) || empty($this->Debtor)) {
            $this->Error[] = __("invalid pricequote element debtor");
        }
        if(strlen($this->Date) && !is_date(rewrite_date_site2db($this->Date))) {
            $this->Error[] = __("invalid price quote date");
        }
        if(strlen($this->Number) && (!is_numeric($this->Number) || 20 < strlen($this->NumberSuffix))) {
            $this->Error[] = sprintf(__("invalid price quote element number"), $this->OldNumber);
        }
        if(!(is_string($this->ProductCode) && strlen($this->ProductCode) <= 50 || strlen($this->ProductCode) === 0)) {
            $this->Error[] = __("invalid price quote element productcode");
        }
        if(!(is_string($this->Description) || strlen($this->Description) === 0)) {
            $this->Error[] = __("invalid pricequote element description");
        }
        if(!is_numeric($this->PriceExcl)) {
            $this->PriceExcl = 0;
        }
        if(!is_numeric($this->DiscountPercentage) || $this->DiscountPercentage < 0 || 1 < $this->DiscountPercentage) {
            $this->Error[] = __("invalid pricequote element discountpercentage");
        } elseif($this->DiscountPercentage && 2 < strlen(substr(strrchr($this->DiscountPercentage * 100, "."), 1))) {
            $this->Error[] = __("invalid price quote element discountpercentage digit");
        }
        if(!in_array($this->DiscountPercentageType, ["line", "subscription"])) {
            $this->DiscountPercentageType = "line";
        }
        if(is_null($this->TaxPercentage) || $this->TaxPercentage == "") {
            $this->TaxPercentage = 0;
        } elseif(!is_numeric($this->TaxPercentage) || $this->TaxPercentage < 0 || 1 < $this->TaxPercentage) {
            $this->Error[] = __("invalid price quote element taxpercentage");
        } elseif($this->TaxPercentage && 2 < strlen(substr(strrchr($this->TaxPercentage * 100, "."), 1))) {
            $this->Error[] = __("invalid price quote element taxpercentage digits");
        }
        if($this->Periodic && 0 < $this->Periods && !$this->PeriodicID && !isset($this->StartPeriod)) {
            $this->Error[] = __("invalid price quote element startdate");
        }
        if($this->PeriodicID == "") {
            $this->PeriodicID = 0;
        } elseif(!is_numeric($this->PeriodicID)) {
            $this->Error[] = __("invalid periodicID");
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
            $this->Error[] = __("invalid price quote element ordering");
        }
        if(!empty($this->Error)) {
            return false;
        }
        return true;
    }
    public function all($PriceQuoteCode)
    {
        $PriceQuoteCode = htmlspecialchars_decode($PriceQuoteCode);
        if(!isset($this->VatCalcMethod)) {
            $vat_calc_method = Database_Model::getInstance()->getOne("HostFact_PriceQuote", ["VatCalcMethod"])->where("PriceQuoteCode", $PriceQuoteCode)->execute();
            $this->VatCalcMethod = isset($vat_calc_method->VatCalcMethod) ? $vat_calc_method->VatCalcMethod : "excl";
        }
        if($this->VatCalcMethod == "incl") {
            $result = Database_Model::getInstance()->getOne("HostFact_PriceQuoteElements", ["COUNT(`id`) as CountRows", "SUM(`LineAmountIncl` / (1+`TaxPercentage`)) as AmountExcl", "SUM(`LineAmountIncl`) as AmountIncl"])->where("PriceQuoteCode", $PriceQuoteCode)->groupBy("PriceQuoteCode")->execute();
            $sql_amount_excl = "HostFact_PriceQuoteElements.`LineAmountExcl` as AmountExcl";
            $sql_amount_incl = "HostFact_PriceQuoteElements.`LineAmountIncl` as AmountIncl";
        } else {
            $result = Database_Model::getInstance()->getOne("HostFact_PriceQuoteElements", ["COUNT(`id`) as CountRows", "SUM(`LineAmountExcl`) as AmountExcl", "SUM(`LineAmountExcl` * (1+`TaxPercentage`)) as AmountIncl"])->where("PriceQuoteCode", $PriceQuoteCode)->groupBy("PriceQuoteCode")->execute();
            $sql_amount_excl = "HostFact_PriceQuoteElements.`LineAmountExcl` as AmountExcl";
            $sql_amount_incl = "HostFact_PriceQuoteElements.`LineAmountIncl` as AmountIncl";
        }
        $list["AmountIncl"] = $result ? $result->AmountIncl : 0;
        $list["AmountExcl"] = $result ? $result->AmountExcl : 0;
        $list["CountRows"] = $result ? $result->CountRows : 0;
        $element_list = Database_Model::getInstance()->get("HostFact_PriceQuoteElements", ["HostFact_PriceQuoteElements.*", "HostFact_Products.ProductName", "(HostFact_PriceQuoteElements.`PriceExcl` * (1+HostFact_PriceQuoteElements.`TaxPercentage`)) as `PriceIncl` ", $sql_amount_excl, $sql_amount_incl])->join("HostFact_Products", "HostFact_Products.ProductCode = HostFact_PriceQuoteElements.ProductCode")->where("PriceQuoteCode", $PriceQuoteCode)->orderBy("HostFact_PriceQuoteElements.Ordering", "ASC")->orderBy("HostFact_PriceQuoteElements.id", "ASC")->execute();
        if($element_list) {
            foreach ($element_list as $result) {
                $list[$result->id] = ["id" => $result->id];
                foreach ($result as $key => $value) {
                    if(in_array($key, $this->Variables) || $key == "ProductName" || $key == "PriceIncl" || $key == "AmountExcl" || $key == "AmountIncl") {
                        $list[$result->id][$key] = htmlspecialchars($result->{$key});
                    }
                }
                $list[$result->id]["Date"] = $list[$result->id]["Date"] != "0000-00-00" ? rewrite_date_db2site($list[$result->id]["Date"]) : "";
                $list[$result->id]["StartPeriod"] = $list[$result->id]["StartPeriod"] != "0000-00-00" ? rewrite_date_db2site($list[$result->id]["StartPeriod"]) : "";
                $list[$result->id]["EndPeriod"] = $list[$result->id]["EndPeriod"] != "0000-00-00" ? rewrite_date_db2site($list[$result->id]["EndPeriod"]) : "";
                $list[$result->id]["PriceIncl"] = $this->VatCalcMethod == "incl" ? round((double) $list[$result->id]["PriceIncl"], 5) : $list[$result->id]["PriceIncl"];
            }
        }
        return 0 < $list["CountRows"] ? $list : [];
    }
}

?>