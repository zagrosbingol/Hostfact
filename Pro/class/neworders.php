<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class neworder
{
    public $Identifier;
    public $OrderCode;
    public $Debtor;
    public $Customer;
    public $Type;
    public $Date;
    public $Term;
    public $Discount;
    public $IgnoreDiscount;
    public $Coupon;
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
    public $InvoiceMethod;
    public $Status;
    public $TaxRate;
    public $Compound;
    public $AmountExcl;
    public $AmountIncl;
    public $VatCalcMethod;
    public $VatShift;
    public $PaymentMethod;
    public $PaymentMethodID;
    public $Paid;
    public $TransactionID;
    public $Comment;
    public $IPAddress;
    public $Employee;
    public $Free1;
    public $Free2;
    public $Free3;
    public $Free4;
    public $Free5;
    public $Elements;
    public $ExtraElement;
    public $CountRows;
    public $Table;
    public $Error;
    public $Warning;
    public $Success;
    public $Variables = ["Identifier", "OrderCode", "Debtor", "Customer", "Type", "Date", "Term", "Discount", "IgnoreDiscount", "Coupon", "CompanyName", "Sex", "Initials", "SurName", "Address", "Address2", "ZipCode", "City", "State", "Country", "EmailAddress", "Authorisation", "InvoiceMethod", "Template", "Status", "TaxRate", "Compound", "PaymentMethod", "PaymentMethodID", "Paid", "TransactionID", "Comment", "IPAddress", "Employee", "VatCalcMethod", "VatShift"];
    public function __construct()
    {
        $this->Date = rewrite_date_db2site(date("Y-m-d H:i:s"), DATE_FORMAT . " %H:%i:%s");
        $this->Discount = "0";
        $this->Authorisation = "no";
        $this->Status = "0";
        $this->PaymentMethodID = 0;
        $this->Paid = 0;
        $this->InvoiceMethod = "0";
        $this->Term = INVOICE_TERM;
        $this->ExtraElement = false;
        $this->Error = [];
        $this->Warning = [];
        $this->Success = [];
        $this->StateName = "";
        $this->TaxRate = STANDARD_TOTAL_TAX;
        $this->Compound = "no";
        $this->VatCalcMethod = VAT_CALC_METHOD;
        $this->VatShift = "";
    }
    public function show()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for neworder");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_NewOrder", ["*"])->where("id", $this->Identifier)->execute();
        if(!$result || empty($result->id)) {
            $this->Error[] = __("invalid identifier for neworder");
            return false;
        }
        foreach ($result as $key => $value) {
            if($key != "id") {
                $this->{$key} = htmlspecialchars($value);
            }
        }
        $elements = new neworderelement();
        $this->Elements = $elements->all($this->OrderCode);
        $this->Discount = $this->Discount * 100;
        $this->Name = $this->Initials . " " . $this->SurName;
        $tmpDate = $this->Date;
        $this->Date = rewrite_date_db2site($tmpDate, DATE_FORMAT . " %H:%i:%s");
        $this->ShowDate = rewrite_date_db2site($tmpDate, DATE_FORMAT . " " . __("at") . " %H:%i:%s");
        $this->Date = 0 < intval(str_replace("-", "", $this->Date)) ? $this->Date : "";
        global $array_states;
        $this->CountryLong = countryCodeToLong($this->Country);
        $this->StateName = isset($array_states[$this->Country][$this->State]) ? $array_states[$this->Country][$this->State] : $this->State;
        $this->OldDebtor = $this->Debtor;
        return true;
    }
    public function format($euro = true)
    {
        if(is_array($this->Elements)) {
            $disc_percentage = $this->Discount / 100;
            $financial_totals = calculateFinancialTotals($this->VatCalcMethod, $this->Elements, $disc_percentage);
            if(0 < $this->AmountExcl && isEmptyFloat($financial_totals["totals"]["AmountExcl"])) {
                if($this->VatCalcMethod == "incl") {
                    Database_Model::getInstance()->rawQuery("UPDATE `HostFact_NewOrderElements` SET `LineAmountExcl`=(`PriceExcl`*`Periods`*`Number`*ROUND((1-`DiscountPercentage`),4)+(IF(`PriceExcl`*`Periods`*`Number`*ROUND((1-`DiscountPercentage`),4) > 0, 1, -1) * 0.000001)), `LineAmountIncl`=(ROUND(`PriceExcl` * ROUND((1+`TaxPercentage`),4)*`Periods`*`Number`*ROUND((1-`DiscountPercentage`),4)+(IF(`PriceExcl`*`Periods`*`Number`*ROUND((1-`DiscountPercentage`),4) > 0, 1, -1) * 0.000001),2)) WHERE `OrderCode`='" . $this->OrderCode . "'");
                } else {
                    Database_Model::getInstance()->rawQuery("UPDATE `HostFact_NewOrderElements` SET `LineAmountExcl`=(ROUND(`PriceExcl`*`Periods`*`Number`*ROUND((1-`DiscountPercentage`),4)+(IF(`PriceExcl`*`Periods`*`Number`*ROUND((1-`DiscountPercentage`),4) > 0, 1, -1) * 0.000001),2)), `LineAmountIncl`=(ROUND(ROUND(`PriceExcl`*`Periods`*`Number`*ROUND((1-`DiscountPercentage`),4)+(IF(`PriceExcl`*`Periods`*`Number`*ROUND((1-`DiscountPercentage`),4) > 0, 1, -1) * 0.000001),2)*ROUND((1+`TaxPercentage`),4),4)) WHERE `OrderCode`='" . $this->OrderCode . "'");
                }
                $financial_totals = calculateFinancialTotals($this->VatCalcMethod, $this->Elements, $disc_percentage);
                $elements = new neworderelement();
                $this->Elements = $elements->all($this->OrderCode);
            }
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
        return true;
    }
    public function add($discountcheck = true)
    {
        if($this->InvoiceMethod === false || $this->InvoiceMethod == "") {
            require_once "class/debtor.php";
            $debtor = new debtor();
            $debtor->Identifier = $this->Debtor;
            $debtor->show();
            $this->InvoiceMethod = $debtor->InvoiceMethod;
        }
        $elements = new neworderelement();
        $this->Elements = $elements->all($this->OrderCode);
        foreach ($this->Elements as $k => $v) {
            if(is_numeric($k) && $v["Debtor"] != $this->Debtor) {
                $this->Error[] = sprintf(__("mixed debtor for order"), $this->OrderCode);
                return false;
            }
        }
        if($discountcheck === true && (int) $this->IgnoreDiscount === 0) {
            require_once "class/discount.php";
            $discount = new discount();
            $discount->Type = "order";
            $result = $discount->check($this->Debtor, $this->Elements, $this->Coupon, $this->Authorisation, $this->VatCalcMethod);
            if($result) {
                $totaal = isset($this->Elements["AmountExcl"]) ? $this->Elements["AmountExcl"] : 0;
                $discount_percentage = 0;
                foreach ($result as $value) {
                    $discount->Identifier = $value;
                    $discount->show();
                    $discount_percentage = $discount_percentage < $discount->DiscountPercentage ? $discount->DiscountPercentage : $discount_percentage;
                    if(!isEmptyFloat($discount->Discount)) {
                        $elements->OrderCode = $this->OrderCode;
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
                $this->Discount = $this->Discount + $discount_percentage;
            }
            $this->Elements = $elements->all($this->OrderCode);
        }
        $this->Discount = round((double) number2db($this->Discount), 2) / 100;
        $this->Date = rewrite_date_site2db($this->Date, DATE_FORMAT . " %H:%i:%s");
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
            $this->Date = rewrite_date_db2site($this->Date, DATE_FORMAT . " %H:%i:%s");
            return false;
        }
        $result = Database_Model::getInstance()->insert("HostFact_NewOrder", ["OrderCode" => $this->OrderCode, "Debtor" => $this->Debtor, "Customer" => $this->Customer, "Type" => $this->Type, "Date" => $this->Date, "Term" => $this->Term, "Discount" => $this->Discount, "IgnoreDiscount" => $this->IgnoreDiscount, "Coupon" => $this->Coupon, "CompanyName" => $this->CompanyName, "Sex" => $this->Sex, "Initials" => $this->Initials, "SurName" => $this->SurName, "Address" => $this->Address, "Address2" => $this->Address2, "ZipCode" => $this->ZipCode, "City" => $this->City, "State" => $this->State, "Country" => $this->Country, "EmailAddress" => check_email_address($this->EmailAddress, "convert"), "Authorisation" => $this->Authorisation, "InvoiceMethod" => $this->InvoiceMethod, "Template" => $this->Template, "Status" => $this->Status, "TaxRate" => $this->TaxRate, "Compound" => $this->Compound, "PaymentMethod" => $this->PaymentMethod, "PaymentMethodID" => $this->PaymentMethodID, "Paid" => $this->Paid, "TransactionID" => $this->TransactionID, "Comment" => $this->Comment, "IPAddress" => $this->IPAddress, "Employee" => $this->Employee, "VatCalcMethod" => $this->VatCalcMethod, "AmountExcl" => $this->AmountExcl, "AmountIncl" => $this->AmountIncl, "VatShift" => $this->VatShift])->execute();
        if($result) {
            $this->Identifier = $result;
            createLog("order", $this->Identifier, "order created");
            $this->Success[] = sprintf(__("order add success"), $this->OrderCode);
            return true;
        }
        $elements = new neworderelement();
        $this->Elements = $elements->all($this->OrderCode);
        $this->Discount = $this->Discount * 100;
        $this->Date = rewrite_date_db2site($this->Date, DATE_FORMAT . " %H:%i:%s");
        return false;
    }
    public function edit($log = true)
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for neworder");
            return false;
        }
        $elements = new neworderelement();
        $elements->VatCalcMethod = $this->VatCalcMethod;
        $this->Elements = $elements->all($this->OrderCode);
        $discountcheck = true;
        if($discountcheck === true && (int) $this->IgnoreDiscount === 0) {
            require_once "class/discount.php";
            $discount = new discount();
            $discount->Type = "order";
            $result = $discount->check($this->Type == "debtor" ? $this->Debtor : 0, $this->Elements, $this->Coupon, $this->Authorisation, $this->VatCalcMethod);
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
                            $elements->OrderCode = $this->OrderCode;
                            $elements->Debtor = $this->Debtor;
                            $elements->Number = 1;
                            $elements->Description = htmlspecialchars_decode($discount->Description);
                            $elements->PriceExcl = $totaal < $discount->Discount ? -1 * $totaal : -1 * $discount->Discount;
                            $totaal = $totaal < $discount->Discount ? 0 : $totaal - $discount->Discount;
                            $elements->TaxPercentage = btwcheck($this->Debtor, STANDARD_TAX, "line", $this->Type == "debtor" ? false : true);
                            $elements->Ordering = isset($this->Elements["CountRows"]) ? $this->Elements["CountRows"] : 0;
                            $elements->add();
                        }
                    }
                }
                $this->Discount = $discount_percentage;
            }
            $this->Elements = $elements->all($this->OrderCode);
        }
        $this->Discount = round((double) $this->Discount, 2) / 100;
        $financial_totals = calculateFinancialTotals($this->VatCalcMethod, $this->Elements, $this->Discount);
        $this->AmountExcl = $financial_totals["totals"]["AmountExcl"];
        $this->AmountIncl = $financial_totals["totals"]["AmountIncl"];
        $this->TaxRate = btwcheck($this->Debtor, $this->TaxRate, "total", $this->Type == "debtor" ? false : true);
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
            return false;
        }
        if(!is_array($this->Date)) {
            $this->Date = rewrite_date_site2db($this->Date, DATE_FORMAT . " %H:%i:%s");
            $result = Database_Model::getInstance()->update("HostFact_NewOrder", ["Date" => $this->Date])->where("id", $this->Identifier)->execute();
        }
        $result = Database_Model::getInstance()->update("HostFact_NewOrder", ["OrderCode" => $this->OrderCode, "Debtor" => $this->Debtor, "Customer" => $this->Customer, "Type" => $this->Type, "Date" => $this->Date, "Term" => $this->Term, "Discount" => $this->Discount, "IgnoreDiscount" => $this->IgnoreDiscount, "Coupon" => $this->Coupon, "CompanyName" => $this->CompanyName, "Sex" => $this->Sex, "Initials" => $this->Initials, "SurName" => $this->SurName, "Address" => $this->Address, "Address2" => $this->Address2, "ZipCode" => $this->ZipCode, "City" => $this->City, "State" => $this->State, "Country" => $this->Country, "EmailAddress" => check_email_address($this->EmailAddress, "convert"), "Authorisation" => $this->Authorisation, "InvoiceMethod" => $this->InvoiceMethod, "Template" => $this->Template, "Status" => $this->Status, "TaxRate" => $this->TaxRate, "Compound" => $this->Compound, "PaymentMethod" => $this->PaymentMethod, "Paid" => $this->Paid, "TransactionID" => $this->TransactionID, "Comment" => $this->Comment, "IPAddress" => $this->IPAddress, "VatCalcMethod" => $this->VatCalcMethod, "AmountExcl" => $this->AmountExcl, "AmountIncl" => $this->AmountIncl, "VatShift" => $this->VatShift])->where("id", $this->Identifier)->execute();
        if($result) {
            if($log) {
                $this->Success[] = sprintf(__("neworder edit success"), $this->OrderCode);
            }
            return true;
        }
        $elements = new neworderelement();
        $this->Elements = $elements->all($this->OrderCode);
        $this->Discount = $this->Discount * 100;
        return false;
    }
    public function delete()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for neworder");
            return false;
        }
        $this->show();
        global $_module_instances;
        foreach ($this->Elements as $k => $v) {
            if(is_numeric($k) && 0 < $v["Reference"] && $v["ProductType"] && isset($_module_instances[$v["ProductType"]])) {
                $_module_instances[$v["ProductType"]]->order_deleted($v["Reference"]);
            } elseif(is_numeric($k) && 0 < $v["Reference"] && $v["ProductType"] == "domain") {
                $domain_result = Database_Model::getInstance()->getOne("HostFact_Domains", ["id"])->where("id", $v["Reference"])->where("Status", "-1")->orWhere([["Debtor", "-1"], ["Debtor", $this->Debtor]])->execute();
                if(0 < $domain_result->id) {
                    Database_Model::getInstance()->delete("HostFact_Domains")->where("id", $domain_result->id)->where("Status", "-1")->execute();
                    Database_Model::getInstance()->delete("HostFact_Domain_Extra_Values")->where("DomainID", $domain_result->id)->execute();
                }
            } elseif(is_numeric($k) && 0 < $v["Reference"] && $v["ProductType"] == "hosting") {
                Database_Model::getInstance()->delete("HostFact_Hosting")->where("id", $v["Reference"])->where("Status", "-1")->orWhere([["Debtor", "-1"], ["Debtor", $this->Debtor]])->execute();
            } elseif(is_numeric($k) && $v["ProductCode"]) {
                require_once "class/periodic.php";
                $periodic = new periodic();
                $result = $periodic->checkRecognition($v["ProductCode"], $v["Description"]);
                if($result["PeriodicType"] == "domain") {
                    $domain_match = $result["Match"];
                    if($domain_match) {
                        $domain_match = explode(".", $domain_match, 2);
                        $domain_result = Database_Model::getInstance()->getOne("HostFact_Domains", ["id"])->where("Domain", $domain_match[0])->where("Tld", isset($domain_match[1]) ? $domain_match[1] : "")->where("Status", "-1")->orWhere([["Debtor", "-1"], ["Debtor", $this->Debtor]])->execute();
                        if(0 < $domain_result->id) {
                            Database_Model::getInstance()->delete("HostFact_Domains")->where("id", $domain_result->id)->where("Status", "-1")->execute();
                            Database_Model::getInstance()->delete("HostFact_Domain_Extra_Values")->where("DomainID", $domain_result->id)->execute();
                        }
                    }
                } elseif($result["PeriodicType"] == "hosting") {
                    $accountname_match = $result["Match"] != "unknown" ? $result["Match"] : false;
                    if($accountname_match) {
                        Database_Model::getInstance()->delete("HostFact_Hosting")->where("Username", $accountname_match)->where("Status", "-1")->orWhere([["Debtor", "-1"], ["Debtor", $this->Debtor]])->execute();
                    }
                }
            }
        }
        $result = Database_Model::getInstance()->update("HostFact_NewOrder", ["Status" => "9"])->where("id", $this->Identifier)->execute();
        if($result) {
            delete_stats_summary();
            return true;
        }
        return false;
    }
    public function newOrderCode($prefix = ORDERCODE_PREFIX, $number = ORDERCODE_NUMBER)
    {
        $prefix = parsePrefixVariables($prefix);
        $length = strlen($prefix . $number);
        $result = Database_Model::getInstance()->getOne("HostFact_NewOrder", ["OrderCode"])->where("OrderCode", ["LIKE" => $prefix . "%"])->where("LENGTH(`OrderCode`)", [">=" => $length])->where("(SUBSTR(`OrderCode`,:PrefixLength)*1)", [">" => 0])->where("SUBSTR(`OrderCode`,:PrefixLength)", ["REGEXP" => "^[0-9]+\$"])->orderBy("(SUBSTR(`OrderCode`,:PrefixLength)*1)", "DESC")->bindValue("PrefixLength", strlen($prefix) + 1)->execute();
        if(isset($result->OrderCode) && $result->OrderCode && is_numeric(substr($result->OrderCode, strlen($prefix)))) {
            $Code = substr($result->OrderCode, strlen($prefix));
            $Code = $prefix . @str_repeat("0", @max(@strlen($number) - @strlen(@max($Code + 1, $number)), 0)) . max($Code + 1, $number);
        } else {
            $Code = $prefix . $number;
        }
        if(!$this->is_free($Code)) {
            $this->Error[] = sprintf(__("ordercode generation failed"), $Code);
        }
        return !empty($this->Error) ? false : $Code;
    }
    public function validate()
    {
        if(!$this->is_free($this->OrderCode)) {
            $this->Error[] = __("ordercode in use");
        }
        if(!is_numeric($this->Status)) {
            $this->Error[] = __("invalid status");
        }
        if(!is_numeric($this->Debtor)) {
            $this->Error[] = __("invalid debtor");
        }
        if(!is_numeric($this->Term)) {
            $this->Error[] = __("invalid term");
        }
        if(!(is_numeric($this->Discount * 1) && 0 <= $this->Discount && $this->Discount <= 1)) {
            $this->Error[] = __("invalid discount");
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
        if(!(check_email_address($this->EmailAddress) || strlen($this->EmailAddress) === 0)) {
            $this->Error[] = __("invalid email");
        }
        if(!is_numeric($this->InvoiceMethod)) {
            $this->Error[] = __("invalid invoicemethod");
        }
        if(!($this->Authorisation == "yes" || $this->Authorisation == "no")) {
            $this->Error[] = __("invalid authorisation");
        }
        if(!is_numeric($this->AmountExcl)) {
            $this->Error[] = __("invalid amountexcl");
        }
        if(!is_numeric($this->AmountIncl)) {
            $this->Error[] = __("invalid amountincl");
        }
        if(!in_array($this->VatCalcMethod, ["incl", "excl"])) {
            $this->Error[] = __("invalid vatcalcmethod");
        }
        if(!isset($this->Elements["CountRows"]) || $this->Elements["CountRows"] == 0) {
            $this->Error[] = __("no elements in order");
        }
        if(!empty($this->Error)) {
            return false;
        }
        return true;
    }
    public function is_free($OrderCode)
    {
        if($OrderCode) {
            $result = Database_Model::getInstance()->getOne("HostFact_NewOrder", ["id"])->where("OrderCode", $OrderCode)->execute();
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
        $ElementArray = ["Description"];
        $ElementFields = 0 < count(array_intersect($ElementArray, $fields)) ? true : false;
        $search_at = [];
        $DebtorSearch = $ElementSearch = false;
        if($searchat && (0 < strlen($searchfor) || $searchfor)) {
            $search_at = explode("|", $searchat);
            $DebtorSearch = 0 < count(array_intersect($DebtorArray, $search_at)) ? true : false;
            $ElementSearch = 0 < count(array_intersect($ElementArray, $search_at)) ? true : false;
        }
        $select = ["HostFact_NewOrder.id"];
        foreach ($fields as $column) {
            if(in_array($column, $DebtorArray)) {
                $select[] = "HostFact_NewCustomers.`" . $column . "`";
                $select[] = "HostFact_Debtors.`" . $column . "` as `DEB" . $column . "`";
            } elseif(in_array($column, $ElementArray)) {
                $select[] = "HostFact_NewOrderElements.`" . $column . "`";
            } else {
                $select[] = "HostFact_NewOrder.`" . $column . "`";
            }
        }
        if($ElementFields || $ElementSearch) {
            Database_Model::getInstance()->get(["HostFact_NewOrder", "HostFact_NewOrderElements"], $select)->where("HostFact_NewOrderElements.`OrderCode`=HostFact_NewOrder.`OrderCode`")->groupBy("HostFact_NewOrder.`OrderCode`");
        } else {
            Database_Model::getInstance()->get("HostFact_NewOrder", $select);
        }
        if($DebtorFields || $DebtorSearch) {
            Database_Model::getInstance()->join("HostFact_NewCustomers", "HostFact_NewCustomers.`id` = HostFact_NewOrder.`Debtor` AND HostFact_NewOrder.`Type`='new' AND HostFact_NewOrder.`Customer`='0'")->join("HostFact_Debtors", "HostFact_Debtors.`id` = HostFact_NewOrder.`Debtor` AND (HostFact_NewOrder.`Type`='debtor' OR HostFact_NewOrder.`Customer` > 0)");
        }
        if(!empty($search_at)) {
            $or_clausule = [];
            foreach ($search_at as $searchColumn) {
                if(in_array($searchColumn, ["Debtor"])) {
                    $or_clausule[] = ["HostFact_NewOrder.`" . $searchColumn . "`", $searchfor];
                } elseif(in_array($searchColumn, $DebtorArray)) {
                    if($searchColumn == "DebtorCode") {
                        $or_clausule[] = ["HostFact_Debtors.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                    } else {
                        $or_clausule[] = ["HostFact_Debtors.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                        $or_clausule[] = ["HostFact_NewCustomers.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                    }
                } elseif(in_array($searchColumn, $ElementArray)) {
                    $or_clausule[] = ["HostFact_NewOrderElements.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                } else {
                    $or_clausule[] = ["HostFact_NewOrder.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
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
            Database_Model::getInstance()->orderBy("HostFact_NewOrderElements.`" . $sort . "`", $order);
        } elseif($sort == "OrderCode") {
            $order = $order ? $order : "DESC";
            Database_Model::getInstance()->orderBy("IF(SUBSTRING(HostFact_NewOrder.`OrderCode`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(HostFact_NewOrder.`OrderCode`,1,1))", $order)->orderBy("LENGTH(HostFact_NewOrder.`OrderCode`)", $order)->orderBy("HostFact_NewOrder.`OrderCode`", $order);
        } elseif($sort == "Date` ASC, `OrderCode") {
            Database_Model::getInstance()->orderBy("HostFact_NewOrder.`Date`", "ASC")->orderBy("IF(SUBSTRING(HostFact_NewOrder.`OrderCode`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(HostFact_NewOrder.`OrderCode`,1,1))", "ASC")->orderBy("LENGTH(HostFact_NewOrder.`OrderCode`)", "ASC")->orderBy("HostFact_NewOrder.`OrderCode`", "ASC");
        } elseif($sort == "Date` DESC, `OrderCode") {
            Database_Model::getInstance()->orderBy("HostFact_NewOrder.`Date`", "DESC")->orderBy("IF(SUBSTRING(HostFact_NewOrder.`OrderCode`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(HostFact_NewOrder.`OrderCode`,1,1))", "DESC")->orderBy("LENGTH(HostFact_NewOrder.`OrderCode`)", "DESC")->orderBy("HostFact_NewOrder.`OrderCode`", "DESC");
        } elseif($sort) {
            Database_Model::getInstance()->orderBy("HostFact_NewOrder." . $sort, $order);
        } else {
            $order = $order ? $order : "DESC";
            Database_Model::getInstance()->orderBy("IF(SUBSTRING(HostFact_NewOrder.`OrderCode`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(HostFact_NewOrder.`OrderCode`,1,1))", $order)->orderBy("LENGTH(HostFact_NewOrder.`OrderCode`)", $order)->orderBy("HostFact_NewOrder.`OrderCode`", $order);
        }
        if(0 <= $limit) {
            Database_Model::getInstance()->limit((max(1, $limit) - 1) * $show_results, $show_results);
        }
        if(is_string($group) && 1 < count(explode("|", $group))) {
            $group = explode("|", $group);
            $or_clausule = [];
            foreach ($group as $status) {
                $or_clausule[] = ["HostFact_NewOrder.`Status`", $status];
            }
            Database_Model::getInstance()->orWhere($or_clausule);
        } elseif(is_numeric($group)) {
            Database_Model::getInstance()->where("HostFact_NewOrder.`Status`", $group);
        } else {
            Database_Model::getInstance()->where("HostFact_NewOrder.`Status`", ["<=" => 9]);
        }
        if(!empty($filters)) {
            foreach ($filters as $_db_field => $_db_value) {
                switch ($_db_field) {
                    case "debtor":
                        if(is_numeric($_db_value) && 0 < $_db_value) {
                            Database_Model::getInstance()->where("HostFact_NewOrder.Debtor", $_db_value)->where("HostFact_NewOrder.Type", "debtor");
                        }
                        break;
                    case "created":
                        if(isset($_db_value["from"]) && $_db_value["from"]) {
                            Database_Model::getInstance()->where("HostFact_NewOrder.Created", [">=" => $_db_value["from"]]);
                        }
                        if(isset($_db_value["to"]) && $_db_value["to"]) {
                            Database_Model::getInstance()->where("HostFact_NewOrder.Created", ["<=" => $_db_value["to"]]);
                        }
                        break;
                    case "modified":
                        if(isset($_db_value["from"]) && $_db_value["from"]) {
                            Database_Model::getInstance()->where("HostFact_NewOrder.Modified", [">=" => $_db_value["from"]]);
                        }
                        if(isset($_db_value["to"]) && $_db_value["to"]) {
                            Database_Model::getInstance()->where("HostFact_NewOrder.Modified", ["<=" => $_db_value["to"]]);
                        }
                        break;
                    case "date":
                        if(isset($_db_value["from"]) && $_db_value["from"]) {
                            Database_Model::getInstance()->where("HostFact_NewOrder.Date", [">=" => $_db_value["from"]]);
                        }
                        if(isset($_db_value["to"]) && $_db_value["to"]) {
                            Database_Model::getInstance()->where("HostFact_NewOrder.Date", ["<=" => $_db_value["to"]]);
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
            $list["CountRows"] = Database_Model::getInstance()->rowCountWithIDOptimalisation(["HostFact_NewOrder", "HostFact_NewOrderElements"], "HostFact_NewOrder.id", "HostFact_NewOrder.id");
            $this->CountRows = $list["CountRows"];
        } else {
            $list["CountRows"] = Database_Model::getInstance()->rowCountWithIDOptimalisation("HostFact_NewOrder", "HostFact_NewOrder.id", "HostFact_NewOrder.id");
            $this->CountRows = $list["CountRows"];
        }
        if($invoice_list = Database_Model::getInstance()->execute()) {
            foreach ($invoice_list as $result) {
                $list[$result->id] = ["id" => $result->id];
                foreach ($fields as $column) {
                    $list[$result->id][$column] = $result->{$column} ? htmlspecialchars($result->{$column}) : (isset($result->{"DEB" . $column}) && $result->{"DEB" . $column} ? htmlspecialchars($result->{"DEB" . $column}) : htmlspecialchars($result->{$column}));
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
    public function makeInvoice()
    {
        $this->idealpaypalissue = false;
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for neworder");
            return false;
        }
        $this->show();
        if(!in_array($this->PaymentMethod, ["ideal", "paypal", "other"]) || $this->Paid == 1 || rewrite_date_site2db($this->Date, DATE_FORMAT . " %H:%i") < date("YmdHis", time() - 3600)) {
            if($this->Status < 7) {
                if(!$this->Country || is_numeric($this->Country)) {
                    global $company;
                    $this->Country = $company->Country;
                }
                if($this->Type == "new") {
                    require_once "class/newcustomer.php";
                    $newcustomer = new newcustomer();
                    $newcustomer->Identifier = $this->Debtor;
                    $newcustomer->show();
                    require_once "class/debtor.php";
                    $debtor = new debtor();
                    foreach ($debtor->Variables as $value3) {
                        if(isset($newcustomer->{$value3})) {
                            $debtor->{$value3} = htmlspecialchars_decode($newcustomer->{$value3});
                        }
                    }
                    $debtor->Identifier = 0;
                    global $array_customer_languages;
                    if($debtor->DefaultLanguage && !isset($array_customer_languages[$debtor->DefaultLanguage])) {
                        $debtor->DefaultLanguage = "";
                    }
                    if(!empty($newcustomer->customfields_list) && !empty($debtor->customfields_list)) {
                        $debtor->customvalues = [];
                        foreach ($newcustomer->customfields_list as $k => $custom_field) {
                            $debtor->customvalues[$custom_field["FieldCode"]] = isset($newcustomer->customvalues[$custom_field["FieldCode"]]) ? $newcustomer->customvalues[$custom_field["FieldCode"]] : "";
                            if($custom_field["LabelType"] == "checkbox") {
                                $debtor->customvalues[$custom_field["FieldCode"]] = json_decode($debtor->customvalues[$custom_field["FieldCode"]]);
                            }
                        }
                    }
                    $newcustomer->__destruct();
                    unset($newcustomer);
                    if(!$debtor->Country || is_numeric($debtor->Country)) {
                        global $company;
                        $debtor->Country = $company->Country;
                    }
                    if(!$debtor->InvoiceCountry || is_numeric($debtor->InvoiceCountry)) {
                        $debtor->InvoiceCountry = $debtor->Country;
                    }
                    $debtor->Comment = $this->Comment;
                    $debtor->DebtorCode = $debtor->newDebtorCode();
                    if(!$debtor->is_free_username($debtor->Username) || $debtor->Username == "[DebtorCode]" && $debtor->is_free_username($debtor->DebtorCode)) {
                        $debtor->Username = $debtor->DebtorCode;
                    } elseif($debtor->Username == "[DebtorCode]" && !$debtor->is_free_username($debtor->DebtorCode)) {
                        $this->Error[] = __("manual set debtorcode for creating debtor");
                    }
                    $debtor->SentWelcome = "";
                    if(!$debtor->add()) {
                        $this->Error = array_merge($this->Error, $debtor->Error);
                    } else {
                        $this->OldDebtor = $this->Debtor;
                        $this->Debtor = $debtor->Identifier;
                    }
                } else {
                    $this->OldDebtor = $this->Debtor;
                    require_once "class/debtor.php";
                    $debtor = new debtor();
                    $debtor->Identifier = $this->Debtor;
                    $debtor->show();
                    if(trim($this->Comment)) {
                        if(trim($debtor->Comment)) {
                            $debtor->Comment = $debtor->Comment . "\n\n" . $this->Comment;
                        } else {
                            $debtor->Comment = $this->Comment;
                        }
                        Database_Model::getInstance()->update("HostFact_Debtors", ["Comment" => $debtor->Comment])->where("id", $this->Debtor)->execute();
                    }
                }
                $debtor->__destruct();
                unset($debtor);
                require_once "class/debtor.php";
                $debtor = new debtor();
                if(empty($this->Error)) {
                    $debtor->Identifier = $this->Debtor;
                    $debtor->show();
                    $this->Error = array_merge($this->Error, $debtor->Error);
                }
                if(empty($this->Error)) {
                    require_once "class/invoice.php";
                    $invoice = new invoice();
                    foreach ($invoice->Variables as $value2) {
                        if(isset($this->{$value2}) && $value2 != "Template" && $value2 != "Identifier") {
                            $invoice->{$value2} = !is_array($this->{$value2}) ? htmlspecialchars_decode($this->{$value2}) : $this->{$value2};
                        }
                    }
                    $invoice->TaxNumber = htmlspecialchars_decode($debtor->TaxNumber);
                    $invoice->Status = ORDERACCEPT_STATUS;
                    if(INVOICECODE_CONCEPT == "no" || 0 < ORDERACCEPT_STATUS) {
                        $invoice->InvoiceCode = $invoice->newInvoiceCode();
                    } else {
                        $invoice->InvoiceCode = $invoice->newConceptCode();
                    }
                    if($this->Type == "new") {
                        $invoice->Debtor = $debtor->Identifier;
                    }
                    if(0 < $debtor->InvoiceTemplate) {
                        $invoice->Template = $debtor->InvoiceTemplate;
                    }
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
                            $invoiceelement->Date = rewrite_date_db2site(date("Ymd"));
                            if($invoiceelement->Periodic && 0 < $invoiceelement->Periods) {
                                $invoiceelement->StartPeriod = $invoiceelement->Date;
                            }
                            $invoiceelement->DiscountPercentage = $invoiceelement->DiscountPercentage * 100;
                            if($this->Type == "new") {
                                $invoiceelement->Debtor = $debtor->Identifier;
                            }
                            $invoiceelement->add();
                            global $h_domain;
                            global $h_domainID;
                            global $h_hosting;
                            $h_domain = $invoiceelement->Domain ? $invoiceelement->Domain : $h_domain;
                            $h_domainID = $invoiceelement->Domain ? $invoiceelement->DomainID : $h_domainID;
                            $h_hosting = $invoiceelement->Hosting ? $invoiceelement->Hosting : $h_hosting;
                            if(0 < $h_domainID) {
                                $domains_in_order = isset($domains_in_order) ? $domains_in_order : [];
                                $domains_in_order[] = $h_domainID;
                            }
                            if(!empty($invoiceelement->Error)) {
                                $invoice->Error = array_merge($invoiceelement->Error, $invoice->Error);
                            }
                            $invoiceelement->__destruct();
                            unset($invoiceelement);
                        }
                    }
                    $invoice->Date = rewrite_date_db2site(date("Ymd"));
                    if($this->Paid == 1) {
                        $invoice->PayDate = rewrite_date_site2db($this->Date);
                    }
                    if(empty($invoice->Error)) {
                        $invoice->add(false);
                    }
                    $this->Error = array_merge($invoice->Error, $this->Error);
                    if(empty($this->Error)) {
                        require_once "class/automation.php";
                        $automation = new automation();
                        $automation->show();
                        if($h_domain && $h_hosting && empty($this->Error)) {
                            require_once "class/hosting.php";
                            $hosting = new hosting();
                            $hosting->Identifier = $h_hosting;
                            $hosting->show();
                            if($hosting->Domain == "") {
                                $hosting->Domain = $h_domain;
                            }
                            $hosting->getPanel();
                            if($automation->makeaccount_value == 1 && ($automation->makeaccount_run == "create" || $this->Paid == 1)) {
                                $hosting->Status = "3";
                                Database_Model::getInstance()->update("HostFact_Hosting", ["Status" => $hosting->Status, "Domain" => $hosting->Domain, "Server" => $hosting->Server])->where("id", $hosting->Identifier)->execute();
                            } else {
                                Database_Model::getInstance()->update("HostFact_Hosting", ["Domain" => $hosting->Domain, "Server" => $hosting->Server])->where("id", $hosting->Identifier)->execute();
                            }
                            $this->Error = array_merge($hosting->Error, $this->Error);
                            $_SESSION["hosting"] = $hosting;
                            $hosting->show();
                            require_once "class/server.php";
                            $server = new server();
                            $server->Identifier = $hosting->Server;
                            $server->show();
                            if($server->DNS1 && isset($domains_in_order) && is_array($domains_in_order)) {
                                require_once "class/domain.php";
                                foreach ($domains_in_order as $tmp_domain_id) {
                                    $domain = new domain();
                                    $domain->Identifier = $tmp_domain_id;
                                    $domain->show();
                                    require_once "class/registrar.php";
                                    $registrar = new registrar();
                                    $registrar->Identifier = $domain->Registrar;
                                    $registrar->show();
                                    if($registrar->DNS1 == $domain->DNS1 && $registrar->DNS2 == $domain->DNS2 && $registrar->DNS3 == $domain->DNS3) {
                                        $domain->DNS1 = $server->DNS1;
                                        $domain->DNS2 = $server->DNS2 ? $server->DNS2 : "";
                                        $domain->DNS3 = $server->DNS3 ? $server->DNS3 : "";
                                    }
                                    Database_Model::getInstance()->update("HostFact_Domains", ["DNS1" => $domain->DNS1, "DNS2" => $domain->DNS2, "DNS3" => $domain->DNS3])->where("id", $domain->Identifier)->execute();
                                }
                                $domain->__destruct();
                                unset($domain);
                            }
                            $hosting->__destruct();
                            unset($hosting);
                            unset($server);
                        }
                        if($h_domain && $h_domainID && empty($this->Error)) {
                            require_once "class/domain.php";
                            $domain = new domain();
                            $domain->Identifier = $h_domainID;
                            $domain->show();
                            if(0 < $h_hosting && isset($domains_in_order) && is_array($domains_in_order)) {
                                Database_Model::getInstance()->update("HostFact_Domains", ["HostingID" => $h_hosting])->where("id", ["IN" => $domains_in_order])->where("HostingID", "0")->execute();
                            } elseif(0 < $domain->HostingID) {
                                require_once "class/hosting.php";
                                $hosting = new hosting();
                                $hosting->Identifier = $domain->HostingID;
                                $hosting->show(NULL, false);
                                require_once "class/server.php";
                                $server = new server();
                                $server->Identifier = $hosting->Server;
                                $server->show();
                                if($server->DNS1) {
                                    require_once "class/registrar.php";
                                    $registrar = new registrar();
                                    $registrar->Identifier = $domain->Registrar;
                                    $registrar->show();
                                    if($registrar->DNS1 == $domain->DNS1 && $registrar->DNS2 == $domain->DNS2 && $registrar->DNS3 == $domain->DNS3) {
                                        $domain->DNS1 = $server->DNS1;
                                        $domain->DNS2 = $server->DNS2 ? $server->DNS2 : "";
                                        $domain->DNS3 = $server->DNS3 ? $server->DNS3 : "";
                                    }
                                    Database_Model::getInstance()->update("HostFact_Domains", ["DNS1" => $domain->DNS1, "DNS2" => $domain->DNS2, "DNS3" => $domain->DNS3])->where("id", $domain->Identifier)->execute();
                                }
                                $hosting->__destruct();
                                unset($hosting);
                            }
                            if(isset($domains_in_order) && is_array($domains_in_order)) {
                                foreach ($domains_in_order as $tmp_domain_id) {
                                    $domain = new domain();
                                    $domain->Identifier = $tmp_domain_id;
                                    $domain->show();
                                    if($h_hosting <= 0 && 0 < $domain->HostingID && $domain->Status < 4) {
                                        $domains_to_be_added_to_server[$domain->Domain . "." . $domain->Tld] = $domain->HostingID;
                                    }
                                    require_once "class/handle.php";
                                    $handle = new handle();
                                    Database_Model::getInstance()->update("HostFact_Handles", ["Debtor" => $domain->Debtor, "Handle" => $handle->nextInternalHandle("debtor", $domain->Debtor)])->where("id", $domain->ownerHandle)->where("Debtor", "-1")->execute();
                                    Database_Model::getInstance()->update("HostFact_Handles", ["Debtor" => $domain->Debtor, "Handle" => $handle->nextInternalHandle("debtor", $domain->Debtor)])->where("id", $domain->adminHandle)->where("Debtor", "-1")->execute();
                                    Database_Model::getInstance()->update("HostFact_Handles", ["Debtor" => $domain->Debtor, "Handle" => $handle->nextInternalHandle("debtor", $domain->Debtor)])->where("id", $domain->techHandle)->where("Debtor", "-1")->execute();
                                    if($automation->registerdomain_value == 1 && ($automation->registerdomain_run == "create" || $this->Paid == 1) && $domain->Status < 4) {
                                        $domain->Status = 3;
                                        $domain->check();
                                        if($automation->registerdomain_exception == "transfer" && $domain->Type == "transfer") {
                                            $domain->Status = 1;
                                        }
                                        Database_Model::getInstance()->update("HostFact_Domains", ["Type" => $domain->Type, "Status" => $domain->Status])->where("id", $domain->Identifier)->execute();
                                    }
                                }
                            }
                            $_SESSION["domain"] = $domain;
                            $domain->__destruct();
                            unset($domain);
                        }
                        unset($automation);
                    }
                    if(!empty($this->Error)) {
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
                                    $periodic->Identifier = $v["PeriodicID"];
                                    $periodic->delete($v["PeriodicID"]);
                                    $periodic->__destruct();
                                    unset($periodic);
                                }
                            }
                        }
                        if($this->Type == "new") {
                            $debtor = new debtor();
                            $debtor->Identifier = $this->Debtor;
                            $debtor->delete(true);
                            $debtor->__destruct();
                            unset($debtor);
                        }
                        if(isset($domains_in_order) && is_array($domains_in_order)) {
                            foreach ($domains_in_order as $tmp_domain_id) {
                                Database_Model::getInstance()->update("HostFact_Domains", ["Debtor" => $this->Type == "new" ? "-1" : $this->Debtor, "Status" => "-1"])->where("id", $tmp_domain_id)->execute();
                            }
                        }
                        if(isset($h_hosting) && $h_hosting) {
                            require_once "class/hosting.php";
                            $hosting = new hosting();
                            $hosting->delete($h_hosting, "remove", "none", "none");
                            $hosting->__destruct();
                            unset($hosting);
                        }
                    } elseif(empty($this->Error)) {
                        if($this->Type == "new") {
                            $debtor = new debtor();
                            $debtor->Identifier = $this->Debtor;
                            $debtor->show();
                            $debtor->WelcomeMail = defined("ORDER_ACCEPT_WELCOME_MAIL") && ORDER_ACCEPT_WELCOME_MAIL == 1 ? WELCOME_MAIL : 0;
                            foreach ($debtor->Variables as $k) {
                                $debtor->{$k} = htmlspecialchars_decode($debtor->{$k});
                            }
                            $debtor->sentWelcome($debtor->WelcomeMail);
                            $debtor->__destruct();
                            unset($debtor);
                        }
                        $this->Customer = $this->OldDebtor;
                        $this->Status = 8;
                        $this->Type = "debtor";
                        foreach ($this->Variables as $k) {
                            $this->{$k} = htmlspecialchars_decode($this->{$k});
                        }
                        $this->edit(false);
                        global $account;
                        Database_Model::getInstance()->update("HostFact_NewOrder", ["Employee" => isset($account->Identifier) ? $account->Identifier : 0])->where("id", $this->Identifier)->execute();
                        global $_module_instances;
                        $invoiceelement = new invoiceelement();
                        $invoiceelements = $invoiceelement->all($invoice->InvoiceCode);
                        foreach ($invoiceelements as $k => $v) {
                            if(is_numeric($k) && 0 < $v["Reference"] && $v["ProductType"] && isset($_module_instances[$v["ProductType"]])) {
                                $_module_instances[$v["ProductType"]]->order_processed($v["Reference"], $this->Debtor);
                            }
                            if(is_numeric($k) && 0 < $v["Reference"] && $v["ProductType"]) {
                                $service_info = ["Type" => $v["ProductType"], "id" => $v["Reference"], "Debtor" => $this->Debtor];
                                do_action("service_order_is_processed", $service_info);
                            }
                        }
                        if(!empty($domains_to_be_added_to_server) && $h_hosting <= 0) {
                            foreach ($domains_to_be_added_to_server as $tmp_domain => $tmp_hosting_id) {
                                require_once "class/hosting.php";
                                $hosting = new hosting();
                                $hosting->Identifier = $tmp_hosting_id;
                                $hosting->show(NULL, false);
                                if($hosting->Status == 4) {
                                    $hosting->addDomainToServer($tmp_domain, $hosting->Identifier);
                                    $this->Error = array_merge($this->Error, $hosting->Error);
                                }
                            }
                        }
                        if(isset($h_hosting) && $h_hosting) {
                            $service_info = ["Type" => "hosting", "id" => $h_hosting, "Debtor" => $this->Debtor];
                            do_action("service_order_is_processed", $service_info);
                        }
                        if(isset($domains_in_order) && is_array($domains_in_order)) {
                            foreach ($domains_in_order as $tmp_domain_id) {
                                $service_info = ["Type" => "domain", "id" => $tmp_domain_id, "Debtor" => $this->Debtor];
                                do_action("service_order_is_processed", $service_info);
                            }
                        }
                    }
                } else {
                    $this->Error = array_merge($debtor->Error, $this->Error);
                }
                $h_domain = $h_domainID = $h_hosting = $domains_in_order = NULL;
                unset($h_domain);
                unset($h_domainID);
                unset($h_hosting);
                unset($domains_in_order);
                if(empty($this->Error)) {
                    $this->Success[] = sprintf(__("order processed"), $this->OrderCode);
                    createMessageLog("success", "order processed", $this->OrderCode, "order", $this->Identifier);
                    delete_stats_summary();
                    $order_info = ["id" => $this->Identifier, "Debtor" => $this->Debtor, "OrderCode" => $this->OrderCode];
                    do_action("order_is_processed", $order_info);
                } else {
                    createMessageLog("error", "error while processing order", $this->OrderCode, "order", $this->Identifier);
                    foreach ($this->Error as $e) {
                        createMessageLog("error", $e, false, "order", $this->Identifier);
                    }
                    global $account;
                    global $company;
                    if(CRONJOB_NOTIFY_ORDER == "yes" && CRONJOB_NOTIFY_MAILADDRESS && (!isset($account->Identifier) || empty($account->Identifier))) {
                        require_once "class/email.php";
                        $email = new email();
                        $email->Recipient = CRONJOB_NOTIFY_MAILADDRESS;
                        $email->Sender = $company->CompanyName . " <" . $company->EmailAddress . ">";
                        $email->Subject = sprintf(__("email subject order process error"), $this->OrderCode);
                        $email->Message = file_get_contents("includes/language/" . LANGUAGE_CODE . "/mail.order.process.error.html");
                        $email->Message = str_replace("[ordercode]", $this->OrderCode, $email->Message);
                        $email->Message = str_replace("[error]", "&bull; " . implode("<br />&bull; ", $this->Error), $email->Message);
                        $email->Message = str_replace("[orderurl]", BACKOFFICE_URL . "orders.php?page=show&id=" . $this->Identifier, $email->Message);
                        $email->AutoSubmitted = true;
                        $email_sent = $email->sent();
                    }
                }
            } else {
                $this->Error[] = sprintf(__("order already handled"), $this->OrderCode);
            }
        } else {
            $this->Error[] = sprintf(__("order not handled ideal/paypal"), $this->OrderCode);
            $this->idealpaypalissue = true;
        }
        $hosting = "";
        $domain = "";
        $debtor = "";
        $_SESSION["domain"] = "";
        $_SESSION["hosting"] = "";
        $_SESSION["debtor"] = "";
        unset($_SESSION["domain"]);
        unset($_SESSION["hosting"]);
        unset($_SESSION["debtor"]);
    }
    public function getID($method, $value, $debtor_id = false)
    {
        switch ($method) {
            case "identifier":
                $order_id = Database_Model::getInstance()->getOne("HostFact_NewOrder", ["id"])->where("id", intval($value))->execute();
                return $order_id !== false ? $order_id->id : false;
                break;
            case "ordercode":
                $order_id = Database_Model::getInstance()->getOne("HostFact_NewOrder", ["id"])->where("OrderCode", $value)->execute();
                return $order_id !== false && 0 < $order_id->id ? $order_id->id : false;
                break;
            case "clientarea":
                $order_id = Database_Model::getInstance()->getOne("HostFact_NewOrder", ["id"])->where("id", intval($value))->where("Debtor", $debtor_id)->where("Type", "debtor")->where("Status", ["IN" => [0, 1, 2, 8]])->execute();
                return $order_id !== false && 0 < $debtor_id ? $order_id->id : false;
                break;
        }
    }
    public function changeOrderCode($Identifier, $newOrderCode)
    {
        $orderData = Database_Model::getInstance()->getOne("HostFact_NewOrder", ["OrderCode", "Status"])->where("id", $Identifier)->execute();
        if($orderData === false) {
            $this->Error[] = __("invalid identifier for order");
            return false;
        }
        if($orderData->OrderCode == $newOrderCode) {
            return true;
        }
        Database_Model::getInstance()->update("HostFact_NewOrder", ["OrderCode" => $newOrderCode])->where("id", $Identifier)->execute();
        $result = Database_Model::getInstance()->update("HostFact_NewOrderElements", ["OrderCode" => $newOrderCode])->where("OrderCode", $orderData->OrderCode)->execute();
        if($result) {
            return true;
        }
        return false;
    }
    public function changeDebtor($orderId, $newDebtorId)
    {
        $orderCode = $this->getID("id", $orderId);
        if($orderCode === false) {
            $this->Error[] = __("invalid identifier for order");
            return false;
        }
        require_once "class/debtor.php";
        $debtor = new debtor();
        $debtor->Identifier = $newDebtorId;
        if($debtor->show()) {
            Database_Model::getInstance()->update("HostFact_NewOrder", ["Debtor" => $debtor->Identifier, "CompanyName" => $debtor->InvoiceCompanyName ? htmlspecialchars_decode($debtor->InvoiceCompanyName) : htmlspecialchars_decode($debtor->CompanyName), "Sex" => $debtor->InvoiceSurName ? htmlspecialchars_decode($debtor->InvoiceSex) : htmlspecialchars_decode($debtor->Sex), "Initials" => $debtor->InvoiceInitials ? htmlspecialchars_decode($debtor->InvoiceInitials) : htmlspecialchars_decode($debtor->Initials), "SurName" => $debtor->InvoiceSurName ? htmlspecialchars_decode($debtor->InvoiceSurName) : htmlspecialchars_decode($debtor->SurName), "Address" => $debtor->InvoiceAddress ? htmlspecialchars_decode($debtor->InvoiceAddress) : htmlspecialchars_decode($debtor->Address), "Address2" => $debtor->InvoiceAddress2 ? htmlspecialchars_decode($debtor->InvoiceAddress2) : htmlspecialchars_decode($debtor->Address2), "ZipCode" => $debtor->InvoiceZipCode ? htmlspecialchars_decode($debtor->InvoiceZipCode) : htmlspecialchars_decode($debtor->ZipCode), "City" => $debtor->InvoiceCity ? htmlspecialchars_decode($debtor->InvoiceCity) : htmlspecialchars_decode($debtor->City), "State" => $debtor->InvoiceState ? htmlspecialchars_decode($debtor->InvoiceState) : htmlspecialchars_decode($debtor->State), "Country" => $debtor->InvoiceCountry && $debtor->InvoiceAddress ? htmlspecialchars_decode($debtor->InvoiceCountry) : htmlspecialchars_decode($debtor->Country), "EmailAddress" => $debtor->InvoiceEmailAddress ? htmlspecialchars_decode($debtor->InvoiceEmailAddress) : htmlspecialchars_decode($debtor->EmailAddress), "Authorisation" => $debtor->InvoiceAuthorisation])->where("id", $orderId)->execute();
            Database_Model::getInstance()->update("HostFact_NewOrderElements", ["Debtor" => $debtor->Identifier])->where("OrderCode", $orderCode)->execute();
        } else {
            $this->Error[] = __("invalid identifier for debtor");
        }
    }
    public function acceptorders()
    {
        global $automation;
        $_exceptions = $automation->acceptorder_exception ? json_decode($automation->acceptorder_exception, true) : [];
        $fields = ["OrderCode", "Status", "Comment", "Paid", "Type", "PaymentMethod", "Date", "Debtor"];
        $orders = $this->all($fields, "Date", "ASC", "-1", false, false, "0|1");
        foreach ($orders as $key => $value) {
            $acceptorder = false;
            if(is_numeric($key) && $value["Status"] < 2) {
                if(isset($_exceptions["Comment"]) && $_exceptions["Comment"] == "yes" && !empty($value["Comment"])) {
                    $acceptorder = false;
                } else {
                    $_debtor_exception = "paid";
                    if($value["Type"] == "new") {
                        $_debtor_exception = $_exceptions["Debtor"]["new"];
                    } elseif($value["Type"] == "debtor") {
                        $debtor_profile = Database_Model::getInstance()->getOne("HostFact_Debtors", "ClientareaProfile")->where("id", $value["Debtor"])->execute();
                        if(isset($debtor_profile->ClientareaProfile) && isset($_exceptions["Debtor"][$debtor_profile->ClientareaProfile])) {
                            $_debtor_exception = $_exceptions["Debtor"][$debtor_profile->ClientareaProfile];
                        } else {
                            $_debtor_exception = $_exceptions["Debtor"]["existing"];
                        }
                    }
                    if($_debtor_exception == "always") {
                        $acceptorder = true;
                    } elseif($_debtor_exception == "paid" && 0 < $value["Paid"]) {
                        $acceptorder = true;
                    } elseif($_debtor_exception == "never") {
                        $acceptorder = false;
                    }
                }
                if(in_array($value["PaymentMethod"], ["ideal", "paypal", "other"]) && empty($value["Paid"]) && time() - 3600 <= strtotime($value["Date"])) {
                    $acceptorder = false;
                }
                if($acceptorder === true) {
                    $this->Identifier = $key;
                    Database_Model::getInstance()->update("HostFact_NewOrder", ["Status" => "2"])->where("id", $this->Identifier)->execute();
                    $this->makeInvoice();
                    $this->Error = [];
                }
            }
        }
    }
    public function changesendmethod($new_emailaddress = false)
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = sprintf(__("invalid identifier"));
            return false;
        }
        if($new_emailaddress !== false && (check_email_address($new_emailaddress) === false || !$new_emailaddress)) {
            $this->Error[] = __("invalid emailaddress");
            return false;
        }
        if($new_emailaddress !== false) {
            Database_Model::getInstance()->update("HostFact_NewOrder", ["EmailAddress" => check_email_address($new_emailaddress, "convert")])->where("id", $this->Identifier)->execute();
        }
        $result = Database_Model::getInstance()->update("HostFact_NewOrder", ["InvoiceMethod" => $this->InvoiceMethod])->where("id", $this->Identifier)->execute();
        if($result) {
            $this->Success[] = sprintf(__("invoicemethod changed"), $this->OrderCode);
            return true;
        }
        $this->Error[] = sprintf(__("invoicemethod not changed"), $this->OrderCode);
        return false;
    }
    public function removeTransactionID()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for order");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_NewOrder", ["PaymentMethod" => "", "PaymentMethodID" => "", "TransactionID" => ""])->where("id", $this->Identifier)->execute();
        if($result) {
            $this->Success[] = sprintf(__("order transactionid removed"), $this->OrderCode);
            return true;
        }
        return false;
    }
    public function markaspaid($transactionID)
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for order");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_NewOrder", ["TransactionID" => $transactionID, "Paid" => "1"])->where("id", $this->Identifier)->execute();
        if($result) {
            $this->Success[] = sprintf(__("order marked as paid"), $this->OrderCode);
            return true;
        }
        return false;
    }
}
class neworderelement
{
    public $Identifier;
    public $OrderCode;
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
    public $StartPeriod;
    public $EndPeriod;
    public $ProductType;
    public $Reference;
    public $Free1;
    public $Free2;
    public $Free3;
    public $Free4;
    public $Free5;
    public $CountRows;
    public $Table;
    public $Error;
    public $Success;
    public $Warning;
    public $Variables = ["Identifier", "OrderCode", "Debtor", "Date", "Number", "NumberSuffix", "ProductCode", "Description", "PriceExcl", "TaxPercentage", "DiscountPercentage", "DiscountPercentageType", "Periods", "Periodic", "StartPeriod", "EndPeriod", "Free1", "Free2", "Free3", "Free4", "Free5", "Ordering", "ProductType", "Reference"];
    public function __construct()
    {
        $this->Periods = 1;
        $this->Number = 1;
        $this->Date = rewrite_date_db2site(date("Y-m-d H:i:s"));
        $this->ProductType = "";
        $this->Reference = 0;
        $this->NumberSuffix = "";
        $this->Error = [];
        $this->Warning = [];
        $this->Success = [];
        $this->DiscountPercentage = "";
        $this->DiscountPercentageType = "line";
        $this->TaxPercentage = STANDARD_TAX;
    }
    public function show()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for neworder element");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_NewOrderElements", ["HostFact_NewOrderElements.*", "HostFact_Products.ProductName", "HostFact_Products.ProductKeyPhrase", "HostFact_Products.ProductDescription"])->join("HostFact_Products", "HostFact_Products.ProductCode = HostFact_NewOrderElements.ProductCode")->where("HostFact_NewOrderElements.id", $this->Identifier)->execute();
        if(!$result || empty($result->id)) {
            $this->Error[] = __("invalid identifier for neworder element");
            return false;
        }
        foreach ($result as $key => $value) {
            if($key != "id") {
                $this->{$key} = htmlspecialchars($value);
            }
        }
        if(!isset($this->VatCalcMethod)) {
            $vat_calc_method = Database_Model::getInstance()->getOne("HostFact_NewOrder", ["VatCalcMethod"])->where("OrderCode", $this->OrderCode)->execute();
            $this->VatCalcMethod = isset($vat_calc_method->VatCalcMethod) ? $vat_calc_method->VatCalcMethod : "excl";
        }
        $this->PriceIncl = $this->VatCalcMethod == "incl" ? round($this->PriceExcl * (1 + $this->TaxPercentage), 5) : round($this->PriceExcl, 5) * (1 + $this->TaxPercentage);
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
        $this->TaxPercentage = btwcheck($this->Debtor, $this->TaxPercentage, "line", $this->Type == "debtor" ? false : true);
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
        $result = Database_Model::getInstance()->insert("HostFact_NewOrderElements", ["OrderCode" => $this->OrderCode, "Debtor" => $this->Debtor, "Date" => $this->Date, "Number" => $this->Number, "NumberSuffix" => $this->NumberSuffix, "ProductCode" => $this->ProductCode, "Description" => $this->Description, "PriceExcl" => $this->PriceExcl, "TaxPercentage" => $this->TaxPercentage, "DiscountPercentage" => $this->DiscountPercentage, "DiscountPercentageType" => $this->DiscountPercentageType, "Periods" => $this->Periods, "Periodic" => $this->Periodic, "StartPeriod" => substr($this->StartPeriod, 0, 4) != "DATE" ? $this->StartPeriod : ["RAW" => $this->StartPeriod], "EndPeriod" => substr($this->EndPeriod, 0, 4) != "DATE" ? $this->EndPeriod : ["RAW" => $this->EndPeriod], "Ordering" => $this->Ordering, "LineAmountExcl" => $line_amount["excl"], "LineAmountIncl" => $line_amount["incl"], "ProductType" => $this->ProductType, "Reference" => $this->Reference])->execute();
        if($result) {
            $this->Identifier = $result;
            if($this->ProductCode && 0 <= $this->PriceExcl) {
                Database_Model::getInstance()->update("HostFact_Products", ["Ordered" => ["RAW" => "`Ordered` + :ordered"]])->bindValue("ordered", $this->Number * $this->Periods)->where("ProductCode", $this->ProductCode)->execute();
            } elseif($this->ProductCode && $this->PriceExcl < 0) {
                Database_Model::getInstance()->update("HostFact_Products", ["Ordered" => ["RAW" => "`Ordered` - :ordered"]])->bindValue("ordered", $this->Number * $this->Periods)->where("ProductCode", $this->ProductCode)->execute();
            }
            return true;
        }
        return false;
    }
    public function edit()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for neworder element");
            return false;
        }
        $this->OldNumber = $this->Number;
        $this->Number = deformat_money($this->Number);
        if(is_numeric($this->Number) && isEmptyFloat($this->Number) || !$this->Number) {
            Database_Model::getInstance()->delete("HostFact_NewOrderElements")->where("id", $this->Identifier)->execute();
            return true;
        }
        $this->TaxPercentage = btwcheck($this->Debtor, $this->TaxPercentage, "line", $this->Type == "debtor" ? false : true);
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
        $result = Database_Model::getInstance()->update("HostFact_NewOrderElements", ["OrderCode" => $this->OrderCode, "Debtor" => $this->Debtor, "Date" => $this->Date, "Number" => $this->Number, "NumberSuffix" => $this->NumberSuffix, "ProductCode" => $this->ProductCode, "Description" => $this->Description, "PriceExcl" => $this->PriceExcl, "TaxPercentage" => $this->TaxPercentage, "DiscountPercentage" => $this->DiscountPercentage, "DiscountPercentageType" => $this->DiscountPercentageType, "Periods" => $this->Periods, "Periodic" => $this->Periodic, "StartPeriod" => substr($this->StartPeriod, 0, 4) != "DATE" ? $this->StartPeriod : ["RAW" => $this->StartPeriod], "EndPeriod" => substr($this->EndPeriod, 0, 4) != "DATE" ? $this->EndPeriod : ["RAW" => $this->EndPeriod], "Ordering" => $this->Ordering, "LineAmountExcl" => $line_amount["excl"], "LineAmountIncl" => $line_amount["incl"], "ProductType" => $this->ProductType, "Reference" => $this->Reference])->where("id", $this->Identifier)->execute();
        if($result) {
            return true;
        }
        return false;
    }
    public function updatePrice($VatCalcMethod)
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for neworder element");
            return false;
        }
        $this->PriceExcl = deformat_money($this->PriceExcl);
        $line_amount = getLineAmount($VatCalcMethod, $this->PriceExcl, $this->Periods, $this->Number, $this->TaxPercentage, $this->DiscountPercentage);
        $result = Database_Model::getInstance()->update("HostFact_NewOrderElements", ["PriceExcl" => $this->PriceExcl, "LineAmountExcl" => $line_amount["excl"], "LineAmountIncl" => $line_amount["incl"]])->where("id", $this->Identifier)->execute();
        if($result) {
            return true;
        }
        return false;
    }
    public function delete()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for neworder element");
            return false;
        }
        $result = Database_Model::getInstance()->delete("HostFact_NewOrderElements")->where("id", $this->Identifier)->execute();
        if($result) {
            return true;
        }
        return false;
    }
    public function validate()
    {
        if(!is_numeric($this->Debtor)) {
            $this->Error[] = __("invalid debtor neworder element");
        }
        if(!(is_numeric($this->TaxPercentage * 1) && 0 <= $this->TaxPercentage && $this->TaxPercentage <= 1)) {
            $this->Error[] = __("invalid taxpercentage");
        }
        if(!is_numeric($this->Periods) || (int) $this->Periods === 0) {
            $this->Error[] = __("invalid periodic");
        }
        if(strlen($this->Number) && (!is_numeric($this->Number) || 20 < strlen($this->NumberSuffix))) {
            $this->Error[] = sprintf(__("invalid orderline number"), $this->OldNumber);
        }
        if(!is_numeric($this->DiscountPercentage) || $this->DiscountPercentage < 0 || 1 < $this->DiscountPercentage) {
            $this->Error[] = __("invalid orderline discountpercentage");
        } elseif($this->DiscountPercentage && 2 < strlen(substr(strrchr(number2db($this->DiscountPercentage) * 100, "."), 1))) {
            $this->Error[] = __("invalid orderline discountpercentage digit");
        }
        if(!in_array($this->DiscountPercentageType, ["line", "subscription"])) {
            $this->DiscountPercentageType = "line";
        }
        if($this->PriceExcl != "" && !is_numeric($this->PriceExcl)) {
            $this->Error[] = sprintf(__("invalid price for order element"), $this->PriceExcl);
        }
        if(!empty($this->Error)) {
            return false;
        }
        return true;
    }
    public function all($OrderCode)
    {
        $OrderCode = htmlspecialchars_decode($OrderCode);
        if(!isset($this->VatCalcMethod)) {
            $vat_calc_method = Database_Model::getInstance()->getOne("HostFact_NewOrder", ["VatCalcMethod"])->where("OrderCode", $OrderCode)->execute();
            $this->VatCalcMethod = isset($vat_calc_method->VatCalcMethod) ? $vat_calc_method->VatCalcMethod : "excl";
        }
        if($this->VatCalcMethod == "incl") {
            $result = Database_Model::getInstance()->getOne("HostFact_NewOrderElements", ["COUNT(`id`) as CountRows", "SUM(`LineAmountIncl` / (1+`TaxPercentage`)) as AmountExcl", "SUM(`LineAmountIncl`) as AmountIncl"])->where("OrderCode", $OrderCode)->groupBy("OrderCode")->execute();
            $sql_amount_excl = "HostFact_NewOrderElements.`LineAmountExcl` as AmountExcl";
            $sql_amount_incl = "HostFact_NewOrderElements.`LineAmountIncl` as AmountIncl";
        } else {
            $result = Database_Model::getInstance()->getOne("HostFact_NewOrderElements", ["COUNT(`id`) as CountRows", "SUM(`LineAmountExcl`) as AmountExcl", "SUM(`LineAmountExcl` * (1+`TaxPercentage`)) as AmountIncl"])->where("OrderCode", $OrderCode)->groupBy("OrderCode")->execute();
            $sql_amount_excl = "HostFact_NewOrderElements.`LineAmountExcl` as AmountExcl";
            $sql_amount_incl = "HostFact_NewOrderElements.`LineAmountIncl` as AmountIncl";
        }
        $list["AmountIncl"] = $result ? $result->AmountIncl : 0;
        $list["AmountExcl"] = $result ? $result->AmountExcl : 0;
        $list["CountRows"] = $result ? $result->CountRows : 0;
        $element_list = Database_Model::getInstance()->get("HostFact_NewOrderElements", ["HostFact_NewOrderElements.*", "HostFact_Products.ProductName", "(HostFact_NewOrderElements.`PriceExcl` * (1+HostFact_NewOrderElements.`TaxPercentage`)) as `PriceIncl` ", $sql_amount_excl, $sql_amount_incl])->join("HostFact_Products", "HostFact_Products.ProductCode = HostFact_NewOrderElements.ProductCode")->where("OrderCode", $OrderCode)->orderBy("HostFact_NewOrderElements.Ordering", "ASC")->orderBy("HostFact_NewOrderElements.id", "ASC")->execute();
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