<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class settings
{
    public $Identifier;
    public $Variable;
    public $Value;
    public $Table;
    public $Success;
    public $Warning;
    public $Error;
    public $Variables = ["Identifier", "Variable", "Value"];
    const ANONYMOUS_FEEDBACK_DAYS_INTERVAL = 7;
    const GENDER_AVAILABLE_OPTIONS = ["m", "f", "d", "u"];
    const GENDER_SHOW_IF_IN_ARRAY = ["m", "f"];
    const AUTH_TYPE_OAUTH2_MS = "OAUTH2_MS";
    const SECURITY_HTTP_HEADER_CONTENT_TYPE = "x-content-type-options";
    const SECURITY_HTTP_HEADER_XSS_PROTECTION = "x-xss-protection";
    const SECURITY_HTTP_HEADER_FRAME_OPTIONS = "x-frame-options";
    public function __construct()
    {
        $this->Success = [];
        $this->Warning = [];
        $this->Error = [];
    }
    public function show()
    {
        $forbidden_stripslashes = ["SMTP_PASSWORD", "TICKET_PASSWORD", "SIDN_POP3_PASS"];
        $result = Database_Model::getInstance()->get("HostFact_Settings")->execute();
        if(!$result && defined("InStAlLHosTFacT")) {
            $db_last_error = Database_Model::getInstance()->getLastError();
            if($db_last_error && stripos($db_last_error, "HostFact_Settings") !== false) {
                $result = Database_Model::getInstance()->get("WeFact_Settings")->execute();
            }
        }
        if(is_array($result)) {
            foreach ($result as $tmp_setting) {
                if(!defined($tmp_setting->Variable)) {
                    if($tmp_setting->Variable == "IS_INTERNATIONAL") {
                        define("IS_INTERNATIONAL", $tmp_setting->Value ? true : false);
                    } else {
                        define($tmp_setting->Variable, htmlspecialchars($tmp_setting->Value));
                    }
                }
            }
        }
        if(defined("BACKOFFICE_URL") && BACKOFFICE_URL == "") {
            if(isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] && $_SERVER["HTTPS"] != "off") {
                $script_url = "https://";
            } else {
                $script_url = "http://";
            }
            $script_url .= $_SERVER["SERVER_NAME"];
            $script_url .= substr($_SERVER["REQUEST_URI"], 0, strrpos($_SERVER["REQUEST_URI"], "/") + 1);
            $script_url = str_replace("install/", "", $script_url);
            Database_Model::getInstance()->update("HostFact_Settings", ["Value" => $script_url])->where("Variable", "BACKOFFICE_URL")->execute();
        }
        return !empty($this->Error) ? $this->Error : true;
    }
    public function edit()
    {
        if(!$this->validate()) {
            return false;
        }
        if($this->Variable != "TICKET_PASSWORD" && $this->Variable != "CONTROLPANEL_PASS" && $this->Variable != "SIDN_POP3_PASS" && $this->Variable != "SMTP_PASSWORD" || $this->Value) {
            if($this->Variable == "TICKET_PASSWORD" || $this->Variable == "CONTROLPANEL_PASS" || $this->Variable == "SIDN_POP3_PASS" || $this->Variable == "SMTP_PASSWORD") {
                $this->OldValue = $this->Value;
                $this->Value = passcrypt($this->Value);
                if(trim($this->Value) != $this->Value || rtrim($this->Value) != $this->Value) {
                    $this->Error[] = __("pass encrypt error");
                }
            }
            if($this->Variable == "ACCOUNTCODE_PREFIX" && !ctype_lower($this->Value)) {
                $this->Value = strtolower($this->Value);
            }
            if(in_array($this->Variable, ["DEBTORCODE_NUMBER", "CREDITORCODE_NUMBER", "PRODUCTCODE_NUMBER", "INVOICECODE_NUMBER", "PRICEQUOTECODE_NUMBER", "CREDITINVOICECODE_NUMBER", "ORDERCODE_NUMBER", "TICKETID_NUMBER", "ACCOUNTCODE_NUMBER"]) && !is_numeric($this->Value)) {
                $this->Error[] = __("numbering should only contain numbers");
            }
            if(in_array($this->Variable, ["CUSTOMER_WIRE_FEEAMOUNT", "CUSTOMER_IDEAL_FEEAMOUNT", "CUSTOMER_PAYPAL_FEEAMOUNT", "CUSTOMER_AUTH_FEEAMOUNT"])) {
                $this->Value = deformat_money($this->Value);
            }
            if($this->Variable == "API_ACCESS") {
                $this->Value = array_filter($this->Value, "strlen");
                $this->Value = implode(";", $this->Value);
            } elseif($this->Variable == "SMTP_HOST" && $_POST["SMTP_HOST_PORT"]) {
                $this->Value = $this->Value . ":" . esc($_POST["SMTP_HOST_PORT"]);
            } else {
                if($this->Variable == "SMTP_HOST_PORT") {
                    return true;
                }
                if($this->Variable == "CUSTOMER_SHOW_CREDITINVOICE") {
                    Database_Model::getInstance()->update("HostFact_Settings", ["Value" => $this->Value])->where("Variable", "CUSTOMER_SHOW_DELETEDINVOICE")->execute();
                }
            }
            Database_Model::getInstance()->update("HostFact_Settings", ["Value" => $this->Value])->where("Variable", $this->Variable)->execute();
            if($this->Variable == "LICENSE" && $this->Value != LICENSE) {
                $this->Variable = "LICENSE_DATE";
                $this->Value = date("Ymd");
                $this->edit();
            } elseif($this->Variable == "LICENSE_UPDATE" && $this->Value != LICENSE_UPDATE) {
                $this->Variable = "LICENSE_UPDATE_DATE";
                $this->Value = date("Ymd");
                $this->edit();
            } elseif($this->Variable == "PERIODIC_INVOICE_DAYS" && $this->Value != PERIODIC_INVOICE_DAYS) {
                Database_Model::getInstance()->update("HostFact_PeriodicElements", ["ReminderDate" => ["RAW" => "DATE_ADD(`StartPeriod`, INTERVAL -:Value DAY)"]])->bindValue("Value", $this->Value)->where("Debtor", ["IN" => ["RAW" => "SELECT `id` FROM `HostFact_Debtors` WHERE `PeriodicInvoiceDays` = -1"]])->where("NextDate = ReminderDate")->where("DATEDIFF(`StartPeriod`,`NextDate`)", PERIODIC_INVOICE_DAYS)->execute();
                Database_Model::getInstance()->update("HostFact_PeriodicElements", ["NextDate" => ["RAW" => "DATE_ADD(`StartPeriod`, INTERVAL -:Value DAY)"]])->bindValue("Value", $this->Value)->where("Debtor", ["IN" => ["RAW" => "SELECT `id` FROM `HostFact_Debtors` WHERE `PeriodicInvoiceDays` = -1"]])->where("DATEDIFF(`StartPeriod`,`NextDate`)", PERIODIC_INVOICE_DAYS)->execute();
            } elseif($this->Variable == "INVOICE_COLLECT_TPM") {
                Database_Model::getInstance()->update("HostFact_Settings", ["Value" => 0 < $this->Value ? "yes" : "no"])->where("Variable", "INVOICE_COLLECT_ENABLED")->execute();
            } elseif($this->Variable == "DIR_EMAIL_ATTACHMENTS" && $this->Value != DIR_EMAIL_ATTACHMENTS) {
                Database_Model::getInstance()->update("HostFact_EmailTemplates", ["Attachment" => ["RAW" => "REPLACE(`Attachment`, :DIR_EMAIL_ATTACHMENTS, :Value)"]])->bindValue("DIR_EMAIL_ATTACHMENTS", DIR_EMAIL_ATTACHMENTS)->bindValue("Value", $this->Value)->noWhere()->execute();
                Database_Model::getInstance()->update("HostFact_Emails", ["Attachment" => ["RAW" => "REPLACE(`Attachment`, :DIR_EMAIL_ATTACHMENTS, :Value)"]])->bindValue("DIR_EMAIL_ATTACHMENTS", DIR_EMAIL_ATTACHMENTS)->bindValue("Value", $this->Value)->noWhere()->execute();
            } elseif($this->Variable == "DIR_PDF_FILES" && $this->Value != DIR_PDF_FILES) {
                Database_Model::getInstance()->update("HostFact_Templates", ["Location" => ["RAW" => "REPLACE(`Location`, :DIR_PDF_FILES, :Value)"], "PostLocation" => ["RAW" => "REPLACE(`Location`, :DIR_PDF_FILES, :Value)"]])->bindValue("DIR_PDF_FILES", DIR_PDF_FILES)->bindValue("Value", $this->Value)->noWhere()->execute();
            } elseif($this->Variable == "DIR_TICKET_ATTACHMENTS" && $this->Value != DIR_TICKET_ATTACHMENTS) {
                Database_Model::getInstance()->update("HostFact_TicketMessage", ["Attachments" => ["RAW" => "REPLACE(`Attachments`, :DIR_TICKET_ATTACHMENTS, :Value)"]])->bindValue("DIR_TICKET_ATTACHMENTS", DIR_TICKET_ATTACHMENTS)->bindValue("Value", $this->Value)->noWhere()->execute();
            }
        }
        return !empty($this->Error) ? $this->Error : true;
    }
    public function validate()
    {
        if($this->Variable == "CLIENTAREA_NOTIFICATION_EMAILADDRESS" && 0 < strlen($this->Value)) {
            if(!check_email_address($this->Value)) {
                $this->Error[] = __("invalid emailaddress entered, setting not saved");
            } else {
                $this->Value = check_email_address($this->Value, "convert");
            }
        }
        if(substr($this->Variable, 0, 12) == "INVOICECODE_" && (strpos($this->Value, "\\") !== false || strpos($this->Value, "/") !== false)) {
            $this->Error[] = __("invoice number cannot contain chars");
            return false;
        }
        if(substr($this->Variable, 0, 15) == "PRICEQUOTECODE_" && (strpos($this->Value, "\\") !== false || strpos($this->Value, "/") !== false)) {
            $this->Error[] = __("pricequote number cannot contain chars");
            return false;
        }
        if($this->Variable == "BACKUP_EMAILADDRESS" && 0 < strlen($this->Value)) {
            if(check_email_address($this->Value)) {
                $this->Value = check_email_address($this->Value, "convert");
            } else {
                $this->Error[] = __("Invalid emailaddress");
            }
        }
        if($this->Variable == "ORDERFORM_TO_PAYMENTDIR" && (stripos($this->Value, "http://") !== false || stripos($this->Value, "https://") !== false)) {
            $this->Error[] = __("setting error - relative path to payment directory");
        }
        return !empty($this->Error) ? false : true;
    }
    public function _validateTax($TaxRate)
    {
        $TaxArray = [];
        if(is_array($TaxRate)) {
            foreach ($TaxRate as $key => $tax) {
                $tax = number2db(vat(number2db($tax)));
                if(is_numeric($tax) && !in_array($tax, $TaxArray)) {
                    $TaxArray[] = $tax;
                } else {
                    $this->Error[] = sprintf(__("invalid tax"), htmlspecialchars($tax));
                    return false;
                }
            }
            return true;
        } else {
            $this->Error[] = __("no tax found");
            return false;
        }
    }
    public function updateDefaultTax($tax)
    {
        Database_Model::getInstance()->update("HostFact_Settings_Taxrates", ["Default" => "no"])->noWhere()->execute();
        Database_Model::getInstance()->update("HostFact_Settings_Taxrates", ["Default" => "yes"])->where("ROUND(`Rate`,3)", number_format($tax, 3, ".", ""))->execute();
        if(isset($_SESSION["wf_cache_array_tax"])) {
            unset($_SESSION["wf_cache_array_tax"]);
        }
    }
    public function updateTaxes($type, $rates = [], $labels = [], $default = 0, $compound = "no")
    {
        Database_Model::getInstance()->delete("HostFact_Settings_Taxrates")->where("TaxType", $type)->execute();
        foreach ($rates as $index => $value) {
            if(isset($labels[$index])) {
                $value = number2db(vat(deformat_money($value)));
                if(is_numeric($value)) {
                    Database_Model::getInstance()->insert("HostFact_Settings_Taxrates", ["Rate" => (double) ($value / 100), "Default" => (double) ($value / 100) == $default ? "yes" : "no", "Label" => esc($labels[$index]), "TaxType" => $type, "Compound" => $compound])->execute();
                }
            }
        }
    }
    public function getTaxChanger()
    {
        $count_array = [];
        global $array_taxpercentages;
        global $array_total_taxpercentages;
        if(!empty($array_taxpercentages)) {
            $result = Database_Model::getInstance()->get("HostFact_Products", ["COUNT(`id`) as Count"])->where("ROUND(`TaxPercentage`,6)", round((double) STANDARD_TAX, 6))->execute();
            $count_array["Products"] = $result[0]->Count;
            $result = Database_Model::getInstance()->get("HostFact_PeriodicElements", ["COUNT(`id`) as Count"])->where("ROUND(`TaxPercentage`,6)", round((double) STANDARD_TAX, 6))->execute();
            $count_array["Subscriptions"] = $result[0]->Count;
            $result = Database_Model::getInstance()->get(["HostFact_InvoiceElements", "HostFact_Invoice"], ["COUNT(HostFact_InvoiceElements.`id`) as Count"])->where("HostFact_Invoice.InvoiceCode = HostFact_InvoiceElements.InvoiceCode")->orWhere([["HostFact_Invoice.Status", 0], ["HostFact_Invoice.Status", 1]])->where("ROUND(HostFact_InvoiceElements.`TaxPercentage`,6)", round((double) STANDARD_TAX, 6))->execute();
            $count_array["InvoiceElements"] = $result[0]->Count;
            $result = Database_Model::getInstance()->get(["HostFact_PriceQuoteElements", "HostFact_PriceQuote"], ["COUNT(HostFact_PriceQuoteElements.`id`) as Count"])->where("HostFact_PriceQuote.PriceQuoteCode = HostFact_PriceQuoteElements.PriceQuoteCode")->orWhere([["HostFact_PriceQuote.Status", 0], ["HostFact_PriceQuote.Status", 1]])->where("ROUND(HostFact_PriceQuoteElements.`TaxPercentage`,6)", round((double) STANDARD_TAX, 6))->execute();
            $count_array["PriceQuoteElements"] = $result[0]->Count;
            if(!defined("PDF_MODULE") || PDF_MODULE == "fpdf") {
                $array_taxpercentages = $_SESSION["wf_cache_array_tax"];
                $result = Database_Model::getInstance()->get(["HostFact_TemplateElements", "HostFact_Templates"], ["COUNT(HostFact_TemplateElements.`id`) as Count"])->where("HostFact_Templates.id = HostFact_TemplateElements.Template")->orWhere([["HostFact_Templates.Type", "invoice"], ["HostFact_Templates.Type", "pricequote"]])->orWhere([["HostFact_TemplateElements.Value", ["LIKE" => "%AmountTax_" . $array_taxpercentages["" . STANDARD_TAX] . "]%"]], ["HostFact_TemplateElements.Value", ["LIKE" => "%" . $array_taxpercentages["" . STANDARD_TAX] . "\\%%"]], ["HostFact_TemplateElements.Value", ["LIKE" => "%" . $array_taxpercentages["" . STANDARD_TAX] . " \\%%"]]])->execute();
                $count_array["TemplateElements"] = $result[0]->Count;
            } else {
                $array_taxpercentages = $_SESSION["wf_cache_array_tax"];
                $result = Database_Model::getInstance()->get(["HostFact_TemplateBlocks", "HostFact_Templates"], ["COUNT(HostFact_TemplateBlocks.`id`) as Count"])->where("HostFact_Templates.id = HostFact_TemplateBlocks.template_id")->orWhere([["HostFact_Templates.Type", "invoice"], ["HostFact_Templates.Type", "pricequote"]])->orWhere([["HostFact_TemplateBlocks.value", ["LIKE" => "%AmountTax_" . $array_taxpercentages["" . STANDARD_TAX] . "]%"]], ["HostFact_TemplateBlocks.value", ["LIKE" => "%" . $array_taxpercentages["" . STANDARD_TAX] . "\\%%"]], ["HostFact_TemplateBlocks.value", ["LIKE" => "%" . $array_taxpercentages["" . STANDARD_TAX] . " \\%%"]]])->execute();
                $count_array["TemplateElements"] = $result[0]->Count;
            }
        }
        if(!empty($array_total_taxpercentages)) {
            $result = Database_Model::getInstance()->get("HostFact_Invoice", ["COUNT(`id`) as Count"])->orWhere([["Status", 0], ["Status", 1]])->where("ROUND(`TaxRate`,6)", round((double) STANDARD_TOTAL_TAX, 6))->execute();
            $count_array["Invoices"] = $result[0]->Count;
            $result = Database_Model::getInstance()->get("HostFact_PriceQuote", ["COUNT(`id`) as Count"])->orWhere([["Status", 0], ["Status", 1]])->where("ROUND(`TaxRate`,6)", round((double) STANDARD_TOTAL_TAX, 6))->execute();
            $count_array["PriceQuotes"] = $result[0]->Count;
        }
        return $count_array;
    }
    public function updateTaxChanger($what_to_update, $new_tax, $new_tax_lvl2, $compound = "no")
    {
        if(STANDARD_TAX != $new_tax) {
            if(isset($what_to_update["Products"]) && $what_to_update["Products"] == "yes") {
                Database_Model::getInstance()->update("HostFact_Products", ["TaxPercentage" => $new_tax])->where("ROUND(`TaxPercentage`,6)", round((double) STANDARD_TAX, 6))->execute();
            }
            if(isset($what_to_update["Subscriptions"]) && $what_to_update["Subscriptions"] == "yes") {
                Database_Model::getInstance()->update("HostFact_PeriodicElements", ["TaxPercentage" => $new_tax])->where("ROUND(`TaxPercentage`,6)", round((double) STANDARD_TAX, 6))->execute();
            }
            if(isset($what_to_update["InvoiceElements"]) && $what_to_update["InvoiceElements"] == "yes") {
                $invoices_to_update = [];
                $result = Database_Model::getInstance()->get(["HostFact_InvoiceElements", "HostFact_Invoice"], ["HostFact_InvoiceElements.`id`", "HostFact_Invoice.id as InvoiceID"])->where("HostFact_Invoice.InvoiceCode = HostFact_InvoiceElements.InvoiceCode")->orWhere([["HostFact_Invoice.Status", "0"], ["HostFact_Invoice.Status", "1"]])->where("ROUND(`TaxPercentage`,6)", round((double) STANDARD_TAX, 6))->execute();
                foreach ($result as $var) {
                    $invoices_to_update[] = $var->InvoiceID;
                    Database_Model::getInstance()->update("HostFact_InvoiceElements", ["TaxPercentage" => $new_tax])->where("id", $var->id)->execute();
                }
                $invoices_to_update = array_unique($invoices_to_update);
                require_once "class/invoice.php";
                foreach ($invoices_to_update as $inv_id) {
                    $invoice = new invoice();
                    $invoice->Identifier = $inv_id;
                    $invoice->show();
                    $invoice->Discount = round((double) $invoice->Discount, 2) / 100;
                    $invoice->AmountExcl = isset($invoice->Elements["AmountExcl"]) ? $invoice->Elements["AmountExcl"] - round($invoice->Elements["AmountExcl"] * $invoice->Discount, 3) : 0;
                    $invoice->AmountIncl = isset($invoice->Elements["AmountIncl"]) ? $invoice->Elements["AmountIncl"] - round($invoice->Elements["AmountIncl"] * $invoice->Discount, 3) : 0;
                    if(0 < $invoice->TaxRate) {
                        global $array_total_taxpercentages_info;
                        $invoice->Compound = isset($array_total_taxpercentages_info[(string) (double) $invoice->TaxRate]["compound"]) ? $array_total_taxpercentages_info[(string) (double) $invoice->TaxRate]["compound"] : "no";
                        if($invoice->Compound == "yes") {
                            $invoice->TaxRate_Amount = round($invoice->AmountIncl * $invoice->TaxRate, 2);
                            $invoice->AmountIncl = $invoice->AmountIncl + $invoice->TaxRate_Amount;
                        } else {
                            $invoice->TaxRate_Amount = round($invoice->AmountExcl * $invoice->TaxRate, 2);
                            $invoice->AmountIncl = $invoice->AmountIncl + $invoice->TaxRate_Amount;
                        }
                    }
                    if(0 < $invoice->AmountIncl) {
                        $invoice->AmountIncl = number_format($invoice->AmountIncl + 0, 2, ".", "");
                    } elseif($invoice->AmountIncl < 0) {
                        $invoice->AmountIncl = number_format($invoice->AmountIncl - 0, 2, ".", "");
                    }
                    Database_Model::getInstance()->update("HostFact_Invoice", ["AmountExcl" => $invoice->AmountExcl, "AmountIncl" => $invoice->AmountIncl])->where("id", $inv_id)->execute();
                }
            }
            if(isset($what_to_update["PriceQuoteElements"]) && $what_to_update["PriceQuoteElements"] == "yes") {
                $pricequotes_to_update = [];
                $result = Database_Model::getInstance()->get(["HostFact_PriceQuoteElements", "HostFact_PriceQuote"], ["HostFact_PriceQuoteElements.`id`", "HostFact_PriceQuote.id as PriceQuoteID"])->where("HostFact_PriceQuote.PriceQuoteCode = HostFact_PriceQuoteElements.PriceQuoteCode")->orWhere([["HostFact_PriceQuote.Status", "0"], ["HostFact_PriceQuote.Status", "1"]])->where("ROUND(`TaxPercentage`,6)", round((double) STANDARD_TAX, 6))->execute();
                foreach ($result as $var) {
                    $pricequotes_to_update[] = $var->PriceQuoteID;
                    Database_Model::getInstance()->update("HostFact_PriceQuoteElements", ["TaxPercentage" => $new_tax])->where("id", $var->id)->execute();
                }
                $pricequotes_to_update = array_unique($pricequotes_to_update);
                require_once "class/pricequote.php";
                foreach ($pricequotes_to_update as $inv_id) {
                    $pricequote = new pricequote();
                    $pricequote->Identifier = $inv_id;
                    $pricequote->show();
                    $pricequote->Discount = round((double) $pricequote->Discount, 2) / 100;
                    $pricequote->AmountExcl = isset($pricequote->Elements["AmountExcl"]) ? $pricequote->Elements["AmountExcl"] - round($pricequote->Elements["AmountExcl"] * $pricequote->Discount, 3) : 0;
                    $pricequote->AmountIncl = isset($pricequote->Elements["AmountIncl"]) ? $pricequote->Elements["AmountIncl"] - round($pricequote->Elements["AmountIncl"] * $pricequote->Discount, 3) : 0;
                    if(0 < $pricequote->TaxRate) {
                        global $array_total_taxpercentages_info;
                        $pricequote->Compound = isset($array_total_taxpercentages_info[(string) (double) $pricequote->TaxRate]["compound"]) ? $array_total_taxpercentages_info[(string) (double) $pricequote->TaxRate]["compound"] : "no";
                        if($pricequote->Compound == "yes") {
                            $pricequote->TaxRate_Amount = round($pricequote->AmountIncl * $pricequote->TaxRate, 2);
                            $pricequote->AmountIncl = $pricequote->AmountIncl + $pricequote->TaxRate_Amount;
                        } else {
                            $pricequote->TaxRate_Amount = round($pricequote->AmountExcl * $pricequote->TaxRate, 2);
                            $pricequote->AmountIncl = $pricequote->AmountIncl + $pricequote->TaxRate_Amount;
                        }
                    }
                    if(0 < $pricequote->AmountIncl) {
                        $pricequote->AmountIncl = number_format($pricequote->AmountIncl + 0, 2, ".", "");
                    } elseif($pricequote->AmountIncl < 0) {
                        $pricequote->AmountIncl = number_format($pricequote->AmountIncl - 0, 2, ".", "");
                    }
                    Database_Model::getInstance()->update("HostFact_PriceQuote", ["AmountExcl" => $pricequote->AmountExcl, "AmountIncl" => $pricequote->AmountIncl])->where("id", $inv_id)->execute();
                }
            }
            if(isset($what_to_update["TemplateElements"]) && $what_to_update["TemplateElements"] == "yes") {
                if(!defined("PDF_MODULE") || PDF_MODULE == "fpdf") {
                    $array_taxpercentages = $_SESSION["wf_cache_array_tax"];
                    $result = Database_Model::getInstance()->get(["HostFact_TemplateElements", "HostFact_Templates"], ["HostFact_TemplateElements.*"])->where("HostFact_Templates.`id` = HostFact_TemplateElements.`Template`")->orWhere([["HostFact_Templates.Type", "invoice"], ["HostFact_Templates.Type", "pricequote"]])->orWhere([["HostFact_TemplateElements.`Value`", ["LIKE" => "%AmountTax_" . $array_taxpercentages["" . STANDARD_TAX] . "]%"]], ["HostFact_TemplateElements.`Value`", ["LIKE" => "%" . $array_taxpercentages["" . STANDARD_TAX] . "\\%%"]], ["HostFact_TemplateElements.`Value`", ["LIKE" => "%" . $array_taxpercentages["" . STANDARD_TAX] . " \\%%"]]])->execute();
                    foreach ($result as $var) {
                        $find = ["AmountTax_" . $array_taxpercentages["" . STANDARD_TAX], $array_taxpercentages["" . STANDARD_TAX] . "%", $array_taxpercentages["" . STANDARD_TAX] . " %"];
                        $replace = ["AmountTax_" . $array_taxpercentages["" . $new_tax], $array_taxpercentages["" . $new_tax] . "%", $array_taxpercentages["" . $new_tax] . " %"];
                        Database_Model::getInstance()->update("HostFact_TemplateElements", ["Value" => str_replace($find, $replace, $var->Value)])->where("id", $var->id)->execute();
                    }
                } else {
                    $array_taxpercentages = $_SESSION["wf_cache_array_tax"];
                    $result = Database_Model::getInstance()->get(["HostFact_TemplateBlocks", "HostFact_Templates"], ["HostFact_TemplateBlocks.*"])->where("HostFact_Templates.id = HostFact_TemplateBlocks.template_id")->orWhere([["HostFact_Templates.Type", "invoice"], ["HostFact_Templates.Type", "pricequote"]])->orWhere([["HostFact_TemplateBlocks.value", ["LIKE" => "%AmountTax_" . $array_taxpercentages["" . STANDARD_TAX] . "]%"]], ["HostFact_TemplateBlocks.value", ["LIKE" => "%" . $array_taxpercentages["" . STANDARD_TAX] . "\\%%"]], ["HostFact_TemplateBlocks.value", ["LIKE" => "%" . $array_taxpercentages["" . STANDARD_TAX] . " \\%%"]]])->execute();
                    foreach ($result as $var) {
                        $find = ["AmountTax_" . $array_taxpercentages["" . STANDARD_TAX], $array_taxpercentages["" . STANDARD_TAX] . "%", $array_taxpercentages["" . STANDARD_TAX] . " %"];
                        $replace = ["AmountTax_" . $array_taxpercentages["" . $new_tax], $array_taxpercentages["" . $new_tax] . "%", $array_taxpercentages["" . $new_tax] . " %"];
                        Database_Model::getInstance()->update("HostFact_TemplateBlocks", ["value" => str_replace($find, $replace, $var->value)])->where("id", $var->id)->execute();
                    }
                }
            }
        }
        if(STANDARD_TOTAL_TAX != $new_tax_lvl2) {
            if(isset($what_to_update["Invoices"]) && $what_to_update["Invoices"] == "yes") {
                $invoices_to_update = [];
                $result = Database_Model::getInstance()->get("HostFact_Invoice", ["id", "Debtor"])->where("Status", ["IN" => [0, 1]])->where("ROUND(`TaxRate`,6)", round((double) STANDARD_TOTAL_TAX, 6))->execute();
                foreach ($result as $tmp_element) {
                    $invoices_to_update[] = $tmp_element->id;
                    $tmp_new_tax_lvl2 = btwcheck($tmp_element->Debtor, $new_tax_lvl2, "total");
                    Database_Model::getInstance()->update("HostFact_Invoice", ["TaxRate" => $tmp_new_tax_lvl2, "Compound" => $compound])->where("id", $tmp_element->id)->execute();
                }
                $invoices_to_update = array_unique($invoices_to_update);
                require_once "class/invoice.php";
                foreach ($invoices_to_update as $inv_id) {
                    $invoice = new invoice();
                    $invoice->Identifier = $inv_id;
                    $invoice->show();
                    $invoice->Discount = round((double) $invoice->Discount, 2) / 100;
                    $invoice->AmountExcl = isset($invoice->Elements["AmountExcl"]) ? $invoice->Elements["AmountExcl"] - round($invoice->Elements["AmountExcl"] * $invoice->Discount, 3) : 0;
                    $invoice->AmountIncl = isset($invoice->Elements["AmountIncl"]) ? $invoice->Elements["AmountIncl"] - round($invoice->Elements["AmountIncl"] * $invoice->Discount, 3) : 0;
                    $invoice->TaxRate = btwcheck($invoice->Debtor, $invoice->TaxRate, "total");
                    if(0 < $invoice->TaxRate) {
                        global $array_total_taxpercentages_info;
                        $invoice->Compound = isset($array_total_taxpercentages_info[(string) (double) $invoice->TaxRate]["compound"]) ? $array_total_taxpercentages_info[(string) (double) $invoice->TaxRate]["compound"] : "no";
                        if($invoice->Compound == "yes") {
                            $invoice->TaxRate_Amount = round($invoice->AmountIncl * $invoice->TaxRate, 2);
                            $invoice->AmountIncl = $invoice->AmountIncl + $invoice->TaxRate_Amount;
                        } else {
                            $invoice->TaxRate_Amount = round($invoice->AmountExcl * $invoice->TaxRate, 2);
                            $invoice->AmountIncl = $invoice->AmountIncl + $invoice->TaxRate_Amount;
                        }
                    }
                    if(0 < $invoice->AmountIncl) {
                        $invoice->AmountIncl = number_format($invoice->AmountIncl + 0, 2, ".", "");
                    } elseif($invoice->AmountIncl < 0) {
                        $invoice->AmountIncl = number_format($invoice->AmountIncl - 0, 2, ".", "");
                    }
                    Database_Model::getInstance()->update("HostFact_Invoice", ["AmountExcl" => $invoice->AmountExcl, "AmountIncl" => $invoice->AmountIncl])->where("id", $inv_id)->execute();
                }
            }
            if(isset($what_to_update["PriceQuoteElements"]) && $what_to_update["PriceQuoteElements"] == "yes") {
                $pricequotes_to_update = [];
                $result = Database_Model::getInstance()->get("HostFact_PriceQuote", ["id", "Debtor"])->where("Status", ["IN" => [0, 1]])->where("ROUND(`TaxRate`,6)", round((double) STANDARD_TOTAL_TAX, 6))->execute();
                foreach ($result as $tmp_element) {
                    $pricequotes_to_update[] = $tmp_element->id;
                    $tmp_new_tax_lvl2 = btwcheck($tmp_element->Debtor, $new_tax_lvl2, "total");
                    Database_Model::getInstance()->update("HostFact_PriceQuote", ["TaxRate" => $tmp_new_tax_lvl2, "Compound" => $compound])->where("id", $tmp_element->id)->execute();
                }
                $pricequotes_to_update = array_unique($pricequotes_to_update);
                require_once "class/pricequote.php";
                foreach ($pricequotes_to_update as $inv_id) {
                    $pricequote = new pricequote();
                    $pricequote->Identifier = $inv_id;
                    $pricequote->show();
                    $pricequote->Discount = round((double) $pricequote->Discount, 2) / 100;
                    $pricequote->AmountExcl = isset($pricequote->Elements["AmountExcl"]) ? $pricequote->Elements["AmountExcl"] - round($pricequote->Elements["AmountExcl"] * $pricequote->Discount, 3) : 0;
                    $pricequote->AmountIncl = isset($pricequote->Elements["AmountIncl"]) ? $pricequote->Elements["AmountIncl"] - round($pricequote->Elements["AmountIncl"] * $pricequote->Discount, 3) : 0;
                    if(0 < $pricequote->TaxRate) {
                        global $array_total_taxpercentages_info;
                        $pricequote->Compound = isset($array_total_taxpercentages_info[(string) (double) $pricequote->TaxRate]["compound"]) ? $array_total_taxpercentages_info[(string) (double) $pricequote->TaxRate]["compound"] : "no";
                        if($pricequote->Compound == "yes") {
                            $pricequote->TaxRate_Amount = round($pricequote->AmountIncl * $pricequote->TaxRate, 2);
                            $pricequote->AmountIncl = $pricequote->AmountIncl + $pricequote->TaxRate_Amount;
                        } else {
                            $pricequote->TaxRate_Amount = round($pricequote->AmountExcl * $pricequote->TaxRate, 2);
                            $pricequote->AmountIncl = $pricequote->AmountIncl + $pricequote->TaxRate_Amount;
                        }
                    }
                    if(0 < $pricequote->AmountIncl) {
                        $pricequote->AmountIncl = number_format($pricequote->AmountIncl + 0, 2, ".", "");
                    } elseif($pricequote->AmountIncl < 0) {
                        $pricequote->AmountIncl = number_format($pricequote->AmountIncl - 0, 2, ".", "");
                    }
                    Database_Model::getInstance()->update("HostFact_PriceQuote", ["AmountExcl" => $pricequote->AmountExcl, "AmountIncl" => $pricequote->AmountIncl])->where("id", $inv_id)->execute();
                }
            }
        }
    }
    public function removeTaxRules()
    {
        Database_Model::getInstance()->delete("HostFact_Settings_TaxRules")->noWhere()->execute();
    }
    public function addTaxRule($rule)
    {
        Database_Model::getInstance()->insert("HostFact_Settings_TaxRules", ["CountryCode" => $rule["CountryCode"], "StateCode" => $rule["StateCode"], "TaxLevel1" => 0 < strlen($rule["TaxLevel1"]) && is_numeric($rule["TaxLevel1"]) ? (double) $rule["TaxLevel1"] : ["RAW" => NULL], "TaxLevel2" => 0 < strlen($rule["TaxLevel2"]) && is_numeric($rule["TaxLevel2"]) ? (double) $rule["TaxLevel2"] : ["RAW" => NULL], "Restriction" => $rule["Restriction"]])->execute();
    }
    public function getTaxRules()
    {
        $taxrules = Database_Model::getInstance()->get("HostFact_Settings_TaxRules")->asArray()->execute();
        return $taxrules ? $taxrules : [];
    }
    public function setDKIM($dkim_domains = [])
    {
        if(!function_exists("openssl_pkey_new")) {
            $this->Error[] = __("error while creating DKIM credentials, please check openssl configuration");
            return false;
        }
        $current_dkim = json_decode(htmlspecialchars_decode(DKIM_DOMAINS), true);
        $new_dkim = [];
        $new_dkim_added = false;
        foreach ($dkim_domains as $dkim_domain) {
            if(isset($current_dkim[$dkim_domain])) {
                $new_dkim[$dkim_domain] = $current_dkim[$dkim_domain];
            } else {
                $pkGenerate = openssl_pkey_new(["private_key_bits" => 2048, "private_key_type" => OPENSSL_KEYTYPE_RSA]);
                if(!$pkGenerate) {
                    $this->Error[] = __("error while creating DKIM credentials, please check openssl configuration");
                    return false;
                }
                openssl_pkey_export($pkGenerate, $pkGeneratePrivate);
                $selector = "hf" . rand(1000, 9999);
                $new_dkim[$dkim_domain] = ["selector" => $selector, "private" => $pkGeneratePrivate];
                $new_dkim_added = true;
            }
        }
        Database_Model::getInstance()->update("HostFact_Settings", ["Value" => json_encode($new_dkim)])->where("Variable", "DKIM_DOMAINS")->execute();
        if($new_dkim_added) {
            $this->Success[] = __("DKIM activated, find instructions below");
        }
        return true;
    }
    public function getDKIMInstructions()
    {
        $current_dkim = json_decode(htmlspecialchars_decode(DKIM_DOMAINS), true);
        $info = [];
        if(is_array($current_dkim)) {
            foreach ($current_dkim as $dkim_domain => $dkim_info) {
                $pkImport = openssl_pkey_get_private($dkim_info["private"]);
                $pkImportDetails = openssl_pkey_get_details($pkImport);
                $pkImportPublic = $pkImportDetails["key"];
                openssl_pkey_free($pkImport);
                $info[$dkim_domain] = ["Name" => $dkim_info["selector"] . "._domainkey", "Value" => "v=DKIM1; k=rsa; p=" . str_replace(["\n", "-----BEGIN PUBLIC KEY-----", "-----END PUBLIC KEY-----"], "", $pkImportPublic)];
            }
        }
        return $info;
    }
    public function checkClientAreaUrl($clientarea_url = CLIENTAREA_URL)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $clientarea_url);
        settings::disableSSLVerificationIfNeeded($ch);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, "10");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $content = curl_exec($ch);
        $curl_info = curl_getinfo($ch);
        curl_close($ch);
        if(in_array($curl_info["http_code"], ["200"])) {
            return true;
        }
        $this->Error[] = sprintf(__("clientarea url check returns invalid http code"), CLIENTAREA_URL, $curl_info["http_code"], $curl_info["url"]);
        return false;
    }
    public static function sendAnonymousStatistics()
    {
        if(ANONYMOUS_FEEDBACK != "yes" || !isset($_SESSION["hostfact_module_version_has_updates"])) {
            return NULL;
        }
        $last_month = new DateTime("last month");
        $date_start = $last_month->format("Y-m-01");
        $this_month = new DateTime();
        $date_end = $this_month->format("Y-m-01");
        if(ANONYMOUS_FEEDBACK_LASTDATE) {
            $last_date = new DateTime(ANONYMOUS_FEEDBACK_LASTDATE);
            $interval = $last_date->diff($this_month);
            if($interval->format("%r%a") < 7) {
                return NULL;
            }
        }
        $data = [];
        if(!ANONYMOUS_FEEDBACK_HASH) {
            $setting = new settings();
            $setting->Variable = "ANONYMOUS_FEEDBACK_HASH";
            if(function_exists("random_int")) {
                $setting->Value = wf_password_hash(LICENSE . random_int(100, 999));
            } else {
                $setting->Value = wf_password_hash(LICENSE . rand(100, 999));
            }
            $setting->edit();
            $data["id"] = $setting->Value;
        } else {
            $data["id"] = ANONYMOUS_FEEDBACK_HASH;
        }
        $data["month"] = $last_month->format("Y-m");
        $data["hostfact"] = SOFTWARE_VERSION;
        $result = Database_Model::getInstance()->getOne("HostFact_Log", "COUNT(`id`) as `Count`")->where("Action", "debtor made anonymous")->where("Date", [">=" => $date_start])->where("Date", ["<" => $date_end])->execute();
        $data["debtors"]["anonimized"] = self::categorizeCounter(max(0, $result->Count));
        $result = Database_Model::getInstance()->getOne("HostFact_PriceQuote", "COUNT(`id`) as `Count`")->where("Created", [">=" => $date_start])->where("Created", ["<" => $date_end])->execute();
        $data["estimates"]["created"] = self::categorizeCounter(max(0, $result->Count));
        $result = Database_Model::getInstance()->getOne("HostFact_NewOrder", "COUNT(`id`) as `Count`")->where("Created", [">=" => $date_start])->where("Created", ["<" => $date_end])->execute();
        $data["orders"]["created"] = self::categorizeCounter(max(0, $result->Count));
        $result = Database_Model::getInstance()->getOne("HostFact_Invoice", "COUNT(`id`) as `Count`")->where("Created", [">=" => $date_start])->where("Created", ["<" => $date_end])->execute();
        $data["invoices"]["created"] = self::categorizeCounter(max(0, $result->Count));
        $result = Database_Model::getInstance()->getOne("HostFact_Interactions", "COUNT(`id`) as `Count`")->where("Date", [">=" => $date_start])->where("Date", ["<" => $date_end])->execute();
        $data["interactions"]["created"] = self::categorizeCounter(max(0, $result->Count));
        $result = Database_Model::getInstance()->get("HostFact_CreditInvoice", ["SUBSTRING(HostFact_Documents.`FilenameServer`,-4) as `AttachmentType`", "COUNT(HostFact_CreditInvoice.`id`) as `Count`"])->join("HostFact_Documents", "HostFact_Documents.`Type`='creditinvoice' AND HostFact_Documents.`Reference`=HostFact_CreditInvoice.id")->where("HostFact_CreditInvoice.Created", [">=" => $date_start])->where("HostFact_CreditInvoice.Created", ["<" => $date_end])->groupBy(["RAW" => "SUBSTRING(HostFact_Documents.`FilenameServer`,-4)"])->execute();
        $purchase_counter = ["total" => 0, "pdf" => 0, "ubl" => 0, "image" => 0, "other" => 0];
        foreach ($result as $_attachment_type) {
            if($_attachment_type->AttachmentType == ".pdf") {
                $purchase_counter["pdf"] += $_attachment_type->Count;
            } elseif($_attachment_type->AttachmentType == ".xml") {
                $purchase_counter["ubl"] += $_attachment_type->Count;
            } elseif(in_array($_attachment_type->AttachmentType, [".jpg", ".png", "jpeg", "gif"])) {
                $purchase_counter["image"] += $_attachment_type->Count;
            } elseif($_attachment_type->AttachmentType) {
                $purchase_counter["other"] += $_attachment_type->Count;
            }
            $purchase_counter["total"] += $_attachment_type->Count;
        }
        $data["purchase_invoices"]["created"] = self::categorizeCounter($purchase_counter["total"]);
        $data["purchase_invoices"]["pdf_percentage"] = 0 < $purchase_counter["total"] ? round($purchase_counter["pdf"] / $purchase_counter["total"], 2) : 0;
        $data["purchase_invoices"]["ubl_percentage"] = 0 < $purchase_counter["total"] ? round($purchase_counter["ubl"] / $purchase_counter["total"], 2) : 0;
        $data["purchase_invoices"]["image_percentage"] = 0 < $purchase_counter["total"] ? round($purchase_counter["image"] / $purchase_counter["total"], 2) : 0;
        $data["purchase_invoices"]["other_percentage"] = 0 < $purchase_counter["total"] ? round($purchase_counter["other"] / $purchase_counter["total"], 2) : 0;
        $data["ticketsystem"]["enabled"] = false;
        if(TICKET_USE == 1) {
            $data["ticketsystem"]["enabled"] = true;
            $data["ticketsystem"]["pop3"] = TICKET_USE_MAIL == 1 ? true : false;
            $result = Database_Model::getInstance()->getOne("HostFact_Tickets", "COUNT(`id`) as `Count`")->where("Date", [">=" => $date_start])->where("Date", ["<" => $date_end])->execute();
            $ticketCountCreated = (int) max(0, $result->Count);
            $data["ticketsystem"]["created"] = self::categorizeCounter($ticketCountCreated);
            $result = Database_Model::getInstance()->getOne("HostFact_Tickets", "COUNT(`id`) as `Count`")->where("Date", [">=" => $date_start])->where("Date", ["<" => $date_end])->where("Number", [">" => 1])->execute();
            $data["ticketsystem"]["replied_percentage"] = 0 < $ticketCountCreated ? round((int) $result->Count / $ticketCountCreated, 2) : 0;
        }
        require_once __DIR__ . "/../3rdparty/modules/hostfact_module.php";
        $module_version_information = hostfact_module::checkModuleVersions();
        $zero_category = self::categorizeCounter(0);
        $registrar = new registrar();
        foreach ($registrar->getAPIs() as $_module => $_module_info) {
            if(!isset($module_version_information["registrars"][$_module])) {
            } else {
                $data["registrars"][$_module] = ["domains" => $zero_category, "ssl" => $zero_category];
            }
        }
        $result = Database_Model::getInstance()->get("HostFact_Domains", ["HostFact_Registrar.Class", "COUNT(HostFact_Domains.`id`) as `Count`"])->join("HostFact_Registrar", "HostFact_Registrar.`id` = HostFact_Domains.`Registrar`")->where("HostFact_Domains.Status", ["IN" => ["4", "5", "6", "7"]])->where("HostFact_Registrar.Class", ["!=" => ""])->groupBy("HostFact_Registrar.Class")->execute();
        if($result) {
            foreach ($result as $_registrar) {
                if(!isset($data["registrars"][$_registrar->Class])) {
                } else {
                    $data["registrars"][$_registrar->Class]["domains"] = self::categorizeCounter($_registrar->Count);
                }
            }
        }
        $result = Database_Model::getInstance()->get("HostFact_SSL_Certificates", ["HostFact_Registrar.Class", "COUNT(HostFact_SSL_Certificates.`id`) as `Count`"])->join("HostFact_Registrar", "HostFact_Registrar.`id` = HostFact_SSL_Certificates.`Registrar`")->where("HostFact_SSL_Certificates.Status", ["IN" => ["inrequest", "install", "active"]])->where("HostFact_Registrar.Class", ["!=" => ""])->groupBy("HostFact_Registrar.Class")->execute();
        if($result) {
            foreach ($result as $_registrar) {
                if(!isset($data["registrars"][$_registrar->Class])) {
                } else {
                    $data["registrars"][$_registrar->Class]["ssl"] = self::categorizeCounter($_registrar->Count);
                }
            }
            if(is_module_active("dnsmanagement")) {
                $result = Database_Model::getInstance()->get("HostFact_Registrar", ["HostFact_Registrar.Class", "COUNT(HostFact_DNS_Integrations.`id`) as `Count`"])->join("HostFact_DNS_Integrations", "HostFact_DNS_Integrations.`Status`='active' AND HostFact_DNS_Integrations.`Type`='registrar' AND HostFact_DNS_Integrations.IntegrationID=HostFact_Registrar.id")->where("HostFact_Registrar.Class", ["!=" => ""])->groupBy("HostFact_Registrar.Class")->execute();
                foreach ($result as $_registrar) {
                    if(!isset($data["registrars"][$_registrar->Class])) {
                    } else {
                        $data["registrars"][$_registrar->Class]["dns"] = 0 < $_registrar->Count ? true : false;
                    }
                }
            }
        }
        $server = new server();
        foreach ($server->getControlPanels() as $_module => $_module_info) {
            if(!isset($module_version_information["controlpanels"][$_module])) {
            } else {
                $data["controlpanels"][$_module] = ["accounts" => $zero_category];
            }
        }
        $result = Database_Model::getInstance()->get("HostFact_Hosting", ["HostFact_Servers.Panel", "COUNT(HostFact_Hosting.`id`) as `Count`"])->join("HostFact_Servers", "HostFact_Servers.`id` = HostFact_Hosting.`Server`")->where("HostFact_Hosting.Status", ["IN" => ["4", "5", "7"]])->where("HostFact_Servers.Panel", ["!=" => ""])->groupBy("HostFact_Servers.Panel")->execute();
        if($result) {
            foreach ($result as $_server) {
                if(!isset($data["controlpanels"][$_server->Panel])) {
                } else {
                    $data["controlpanels"][$_server->Panel]["accounts"] = self::categorizeCounter($_server->Count);
                }
            }
            if(is_module_active("dnsmanagement")) {
                $result = Database_Model::getInstance()->get("HostFact_Servers", ["HostFact_Servers.Panel", "COUNT(HostFact_DNS_Integrations.`id`) as `Count`"])->join("HostFact_DNS_Integrations", "HostFact_DNS_Integrations.`Status`='active' AND HostFact_DNS_Integrations.`Type`='server' AND HostFact_DNS_Integrations.IntegrationID=HostFact_Servers.id")->where("HostFact_Servers.Panel", ["!=" => ""])->groupBy("HostFact_Servers.Panel")->execute();
                foreach ($result as $_server) {
                    if(!isset($data["controlpanels"][$_server->Panel])) {
                    } else {
                        $data["controlpanels"][$_server->Panel]["dns"] = 0 < $_server->Count ? true : false;
                    }
                }
            }
        }
        require_once "3rdparty/export/class.export.php";
        $export = new export();
        foreach ($export->getAvailablePackages() as $_module => $_module_info) {
            if(!isset($module_version_information["accounting_export"][$_module])) {
            } else {
                $data["accounting_export"][$_module] = false;
            }
        }
        if(!empty($data["accounting_export"])) {
            $result = Database_Model::getInstance()->get("HostFact_ExportSettings", ["package", "value"])->where("package", ["IN" => array_keys($data["accounting_export"])])->where("name", "statistics")->groupBy("package")->execute();
            if($result) {
                foreach ($result as $_package) {
                    if(!isset($data["accounting_export"][$_package->package]) || $_package->value == "") {
                    } elseif(strpos($_package->value, "lastExport")) {
                        $_lastdates = json_decode($_package->value);
                        foreach ($_lastdates as $_type => $_stats) {
                            if(strtotime($date_start) <= $_stats->lastExport && $_stats->lastExport < strtotime($date_end)) {
                                $data["accounting_export"][$_package->package] = true;
                            }
                        }
                    } elseif(strpos($_package->value, "export_lastdate")) {
                        $_lastdates = json_decode($_package->value);
                        foreach ($_lastdates as $_type => $_stats) {
                            if($date_start <= $_stats->export_lastdate && $_stats->export_lastdate < $date_end) {
                                $data["accounting_export"][$_package->package] = true;
                            }
                        }
                    }
                }
            }
        }
        global $additional_product_types;
        $product_module_integrations = [];
        $product_module_integrations = do_filter("module_get_integrations", $product_module_integrations);
        if(isset($product_module_integrations["services"]["dnsmanagement"])) {
            foreach ($product_module_integrations["services"]["dnsmanagement"] as $_module => $_module_info) {
                if(!isset($module_version_information["dnsmanagement"][$_module])) {
                } else {
                    $data["dnsmanagement"][$_module] = false;
                }
            }
            $result = Database_Model::getInstance()->get("HostFact_DNS_Platform", ["HostFact_DNS_Platform.Platform", "COUNT(HostFact_DNS_Integrations.`id`) as `Count`"])->join("HostFact_DNS_Integrations", "HostFact_DNS_Integrations.`Status`='active' AND HostFact_DNS_Integrations.`Type`='other' AND HostFact_DNS_Integrations.IntegrationID=HostFact_DNS_Platform.id")->where("HostFact_DNS_Platform.Status", "active")->where("HostFact_DNS_Platform.Platform", ["!=" => ""])->groupBy("HostFact_DNS_Platform.Platform")->execute();
            if($result) {
                foreach ($result as $_platform) {
                    if(!isset($data["dnsmanagement"][$_platform->Platform])) {
                    } else {
                        $data["dnsmanagement"][$_platform->Platform] = 0 < $_platform->Count ? true : false;
                    }
                }
            }
        }
        if(isset($product_module_integrations["services"]["vps"]) && isset($additional_product_types["vps"])) {
            foreach ($product_module_integrations["services"]["vps"] as $_module => $_module_info) {
                if(!isset($module_version_information["vps"][$_module])) {
                } else {
                    $data["vps"][$_module] = ["accounts" => $zero_category];
                }
            }
            $result = Database_Model::getInstance()->get("HostFact_VPS_Services", ["HostFact_VPS_Nodes.Platform", "COUNT(HostFact_VPS_Services.`id`) as `Count`"])->join("HostFact_VPS_Nodes", "HostFact_VPS_Nodes.`id` = HostFact_VPS_Services.`Node`")->where("HostFact_VPS_Services.Status", "active")->where("HostFact_VPS_Nodes.Platform", ["!=" => ""])->groupBy("HostFact_VPS_Nodes.Platform")->execute();
            if($result) {
                foreach ($result as $_node) {
                    if(!isset($data["vps"][$_node->Platform])) {
                    } else {
                        $data["vps"][$_node->Platform]["accounts"] = self::categorizeCounter($_node->Count);
                    }
                }
            }
        }
        $result = Database_Model::getInstance()->get("HostFact_PaymentMethods", ["id", "Directory"])->where("Availability", [">" => 0])->where("Directory", ["NOT IN" => ["", "payment.auth"]])->where("MerchantID", ["!=" => ""])->execute();
        if($result) {
            $id_to_platform = [];
            foreach ($result as $_provider) {
                if(!in_array($_provider->Directory, ["ideal.abn.easy", "ideal.abn.idealonly", "ideal.ing.advanced.v3", "ideal.mollie", "mollie", "multisafepay", "paypal"])) {
                } else {
                    $id_to_platform[$_provider->id] = $_provider->Directory;
                    $data["payment_provider"][$_provider->Directory] = ["payments" => $zero_category];
                }
            }
            $result = Database_Model::getInstance()->get("HostFact_Invoice", ["PaymentMethodID", "COUNT(HostFact_Invoice.`id`) as `Count`"])->where("Status", "4")->where("PayDate", [">=" => $date_start])->where("PayDate", ["<" => $date_end])->where("PaymentMethodID", [">" => "0"])->groupBy("PaymentMethodID")->execute();
            if($result) {
                foreach ($result as $_count_per_method) {
                    if(!isset($data["payment_provider"][$id_to_platform[$_count_per_method->PaymentMethodID]])) {
                    } else {
                        $data["payment_provider"][$id_to_platform[$_count_per_method->PaymentMethodID]]["payments"] = self::categorizeCounter($_count_per_method->Count);
                    }
                }
            }
        }
        $url = INTERFACE_URL . "/hosting/infofile.php?action=anonymous_feedback";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        settings::disableSSLVerificationIfNeeded($ch);
        curl_setopt($ch, CURLOPT_TIMEOUT, "10");
        curl_setopt($ch, CURLOPT_POSTFIELDS, ["data" => json_encode($data)]);
        curl_exec($ch);
        curl_close($ch);
        $setting = new settings();
        $setting->Variable = "ANONYMOUS_FEEDBACK_LASTDATE";
        $setting->Value = $this_month->format("Y-m-d");
        $setting->edit();
        return NULL;
    }
    private static function categorizeCounter($counter)
    {
        if($counter === 0) {
            return "A";
        }
        if($counter <= 9) {
            return "B";
        }
        if($counter <= 49) {
            return "C";
        }
        if($counter <= 249) {
            return "D";
        }
        if($counter <= 999) {
            return "E";
        }
        if($counter <= 4999) {
            return "F";
        }
        return "G";
    }
    public static function getSecurityHeaders()
    {
        $all_headers = [static::SECURITY_HTTP_HEADER_CONTENT_TYPE => false, static::SECURITY_HTTP_HEADER_XSS_PROTECTION => false, static::SECURITY_HTTP_HEADER_FRAME_OPTIONS => false];
        $headers = defined("SECURITY_HEADERS") ? json_decode(htmlspecialchars_decode(SECURITY_HEADERS), true) : false;
        if($headers === false || !is_array($headers)) {
            return $all_headers;
        }
        foreach ($headers as $key => $value) {
            if(is_bool($all_headers[$key])) {
                $all_headers[$key] = (bool) $value;
            } else {
                $all_headers[$key] = $value;
            }
        }
        return $all_headers;
    }
    public static function setSecurityHeaders()
    {
        $headers = static::getSecurityHeaders();
        if($headers[static::SECURITY_HTTP_HEADER_XSS_PROTECTION]) {
            header("X-XSS-Protection: 1; mode=block");
        }
        if($headers[static::SECURITY_HTTP_HEADER_CONTENT_TYPE]) {
            header("X-Content-Type-Options: nosniff");
        }
        if($headers[static::SECURITY_HTTP_HEADER_FRAME_OPTIONS]) {
            header("X-Frame-Options: SAMEORIGIN");
        }
    }
    public static function disableSSLVerificationIfNeeded($ch)
    {
        if(defined("DISABLE_CURLOPT_SSL_VERIFICATION") && DISABLE_CURLOPT_SSL_VERIFICATION == "1") {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }
    }
    public static function getGenderTranslation($gender = false, $always)
    {
        if($always === true || in_array($gender, ["m", "f"])) {
            global $array_sex;
            return $array_sex[$gender];
        }
        return "";
    }
}

?>