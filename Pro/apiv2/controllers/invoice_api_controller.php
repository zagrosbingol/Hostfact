<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class invoice_api_controller extends api_controller
{
    public function __construct()
    {
        parent::__construct();
        HostFact_API::setObjectNames("invoices", "invoice");
        require_once "class/invoice.php";
        $this->addParameter("Identifier", "int");
        $this->addParameter("InvoiceCode", "string");
        $this->addParameter("Debtor", "int");
        $this->addParameter("DebtorCode", "string");
        $this->addParameter("Status", "int");
        $this->addParameter("SubStatus", "string");
        $this->addParameter("Date", "date");
        $this->addParameter("Term", "int");
        $this->addParameter("PayBefore", "readonly");
        $this->addParameter("PaymentURL", "readonly");
        $this->addParameter("AmountExcl", "readonly");
        $this->addParameter("AmountTax", "readonly");
        $this->addParameter("AmountIncl", "readonly");
        if(defined("INT_SUPPORT_TAX_OVER_TOTAL") && INT_SUPPORT_TAX_OVER_TOTAL === true) {
            $this->addParameter("TaxRate", "float");
            $this->addParameter("Compound", "string");
        }
        $this->addParameter("AmountPaid", "float");
        $this->addParameter("Discount", "float");
        $this->addParameter("VatCalcMethod", "string");
        $this->addParameter("IgnoreDiscount", "string");
        $this->addParameter("Coupon", "string");
        $this->addParameter("ReferenceNumber", "string");
        $this->addParameter("CompanyName", "string");
        $this->addParameter("TaxNumber", "string");
        $this->addParameter("Sex", "string");
        $this->addParameter("Initials", "string");
        $this->addParameter("SurName", "string");
        $this->addParameter("Address", "string");
        if(defined("IS_INTERNATIONAL") && IS_INTERNATIONAL === true) {
            $this->addParameter("Address2", "string");
        }
        $this->addParameter("ZipCode", "string");
        $this->addParameter("City", "string");
        if(defined("IS_INTERNATIONAL") && IS_INTERNATIONAL === true) {
            $this->addParameter("State", "string");
        }
        $this->addParameter("Country", "string");
        $this->addParameter("EmailAddress", "string");
        $this->addParameter("InvoiceMethod", "int");
        $this->addParameter("Template", "int");
        $this->addParameter("SentDate", "date");
        $this->addParameter("Sent", "int");
        $this->addParameter("Reminders", "int");
        $this->addParameter("ReminderDate", "date");
        if(defined("INT_SUPPORT_SUMMATIONS") && INT_SUPPORT_SUMMATIONS === true) {
            $this->addParameter("Summations", "int");
            $this->addParameter("SummationDate", "date");
        }
        $this->addParameter("Authorisation", "string");
        $this->addParameter("PaymentMethod", "string");
        $this->addParameter("PayDate", "date");
        $this->addParameter("TransactionID", "string");
        $this->addParameter("Description", "text");
        $this->addParameter("Comment", "text");
        $this->addParameter("InvoiceLines", "array");
        $this->addSubParameter("InvoiceLines", "Identifier", "int");
        $this->addSubParameter("InvoiceLines", "Date", "date");
        $this->addSubParameter("InvoiceLines", "Number", "float");
        $this->addSubParameter("InvoiceLines", "NumberSuffix", "string");
        $this->addSubParameter("InvoiceLines", "ProductCode", "string");
        $this->addSubParameter("InvoiceLines", "Description", "text");
        $this->addSubParameter("InvoiceLines", "PriceExcl", "double");
        $this->addSubParameter("InvoiceLines", "DiscountPercentage", "float");
        $this->addSubParameter("InvoiceLines", "DiscountPercentageType", "string");
        $this->addSubParameter("InvoiceLines", "TaxPercentage", "float");
        $this->addSubParameter("InvoiceLines", "PeriodicID", "readonly");
        $this->addSubParameter("InvoiceLines", "PeriodicType", "string");
        $this->addSubParameter("InvoiceLines", "Periods", "int");
        $this->addSubParameter("InvoiceLines", "Periodic", "string");
        $this->addSubParameter("InvoiceLines", "StartPeriod", "date");
        $this->addSubParameter("InvoiceLines", "EndPeriod", "readonly");
        $this->addSubParameter("InvoiceLines", "ProductType", "string");
        $this->addSubParameter("InvoiceLines", "Reference", "int");
        $this->addParameter("Created", "readonly");
        $this->addParameter("Modified", "readonly");
        $this->addFilter("created", "filter_datetime");
        $this->addFilter("modified", "filter_datetime");
        $this->addFilter("date", "filter_date");
        $this->addFilter("paybefore", "filter_date");
        $this->addFilter("paydate", "filter_date");
        $this->addFilter("status", "string", "");
        $this->addFilter("order", "string", "DESC");
        $this->object = new invoice();
        if(!empty($this->object->customfields_list)) {
            $this->addParameter("CustomFields", "array_with_keys");
            foreach ($this->object->customfields_list as $_custom_field) {
                if($_custom_field["LabelType"] == "checkbox") {
                    $this->addSubParameter("CustomFields", $_custom_field["FieldCode"], "array_raw");
                } else {
                    $this->addSubParameter("CustomFields", $_custom_field["FieldCode"], "string");
                }
            }
        }
    }
    public function list_api_action()
    {
        $filters = $this->getFilterValues();
        $fields = ["InvoiceCode", "Debtor", "DebtorCode", "CompanyName", "Initials", "SurName", "AmountExcl", "AmountIncl", "AmountPaid", "Date", "Status", "SubStatus", "Authorisation", "Modified"];
        $sort = $filters["sort"] ? $filters["sort"] : "Date` DESC, `InvoiceCode";
        $page_offset = $filters["offset"] / $filters["limit"] + 1;
        $searchat = $filters["searchat"] ? $filters["searchat"] : "InvoiceCode|CompanyName|SurName";
        $limit = $filters["limit"];
        if(defined("API_CALL_FROM_CLIENTAREA") && API_CALL_FROM_CLIENTAREA === true && (!isset($filters["status"]) || $filters["status"] == "" || strpos($filters["status"], "0") !== false || strpos($filters["status"], "1") !== false)) {
            HostFact_API::parseError("Unauthorized request", true);
        }
        $field_filters = array_diff_key($filters, array_flip(["offset", "limit", "sort", "order", "searchat", "searchfor"]));
        $invoice_list = $this->object->all($fields, $sort, $filters["order"], $page_offset, $searchat, $filters["searchfor"], $field_filters, $limit);
        $array = [];
        foreach ($invoice_list as $key => $value) {
            if($key != "CountRows" && is_numeric($key)) {
                $amountOpen = $value["Status"] == "2" ? $value["AmountIncl"] : ($value["Status"] == "3" ? $value["AmountIncl"] - $value["AmountPaid"] : 0);
                $array[] = ["Identifier" => $value["id"], "InvoiceCode" => htmlspecialchars_decode($value["InvoiceCode"]), "Debtor" => $value["Debtor"], "DebtorCode" => htmlspecialchars_decode($value["DebtorCode"]), "CompanyName" => htmlspecialchars_decode($value["CompanyName"]), "Initials" => htmlspecialchars_decode($value["Initials"]), "SurName" => htmlspecialchars_decode($value["SurName"]), "AmountExcl" => $value["AmountExcl"], "AmountIncl" => $value["AmountIncl"], "AmountOpen" => $amountOpen, "Date" => $this->_filter_date_db2api($value["Date"]), "Status" => $value["Status"], "SubStatus" => $value["SubStatus"], "Authorisation" => $value["Authorisation"], "Modified" => $this->_filter_date_db2api($value["Modified"], false)];
            }
        }
        HostFact_API::setMetaData("totalresults", $invoice_list["CountRows"]);
        HostFact_API::setMetaData("currentresults", count($array));
        HostFact_API::setMetaData("offset", $filters["offset"]);
        if(!empty($this->_valid_filter_input)) {
            HostFact_API::setMetaData("filters", $this->_valid_filter_input);
        }
        HostFact_API::parseResponse($array, true);
    }
    public function show_api_action()
    {
        $invoice_id = $this->_get_invoice_id();
        $this->_show_invoice($invoice_id);
    }
    public function sendbyemail_api_action()
    {
        $invoice = $this->object;
        $invoice->Identifier = $this->_get_invoice_id();
        if($invoice->show()) {
            HostFact_API::beginTransaction();
            $invoice->InvoiceMethod = 0;
            if($invoice->sent(true, false) && empty($invoice->Error) && empty($invoice->Warning)) {
                HostFact_API::parseSuccess($invoice->Success);
                HostFact_API::commit();
                $this->_show_invoice($invoice->Identifier);
            } else {
                $invoice->Error = array_merge($invoice->Error, $invoice->Warning);
                HostFact_API::parseError($invoice->Error, true);
            }
        } else {
            HostFact_API::parseError($invoice->Error, true);
        }
    }
    public function sendreminderbyemail_api_action()
    {
        $invoice = $this->object;
        $invoice->Identifier = $this->_get_invoice_id();
        if($invoice->show()) {
            $invoice->InvoiceMethod = 0;
            if($invoice->sentReminder() && empty($invoice->Error) && empty($invoice->Warning)) {
                HostFact_API::commit();
                HostFact_API::parseSuccess($invoice->Success, true);
            } else {
                $invoice->Error = array_merge($invoice->Error, $invoice->Warning);
                HostFact_API::parseError($invoice->Error, true);
            }
        } else {
            HostFact_API::parseError($invoice->Error, true);
        }
    }
    public function sendsummationbyemail_api_action()
    {
        $invoice = $this->object;
        $invoice->Identifier = $this->_get_invoice_id();
        if($invoice->show()) {
            HostFact_API::beginTransaction();
            $invoice->InvoiceMethod = 0;
            if($invoice->sentSummation(true) && empty($invoice->Error) && empty($invoice->Warning)) {
                HostFact_API::commit();
                HostFact_API::parseSuccess($invoice->Success, true);
            } else {
                $invoice->Error = array_merge($invoice->Error, $invoice->Warning);
                HostFact_API::parseError($invoice->Error, true);
            }
        } else {
            HostFact_API::parseError($invoice->Error, true);
        }
    }
    public function add_api_action()
    {
        HostFact_API::beginTransaction();
        $parse_array = $this->getValidParameters();
        $invoice = $this->object;
        $aAddInvoiceLines = [];
        foreach ($parse_array as $key => $value) {
            if(in_array($key, $invoice->Variables)) {
                $invoice->{$key} = $value;
            }
        }
        $this->_checkInvoiceDebtorData($parse_array);
        if(!isset($parse_array["Status"]) || $parse_array["Status"] == "0") {
            if(!isset($parse_array["InvoiceCode"]) || substr($parse_array["InvoiceCode"], 0, 9) != "[concept]" || !$invoice->is_free($parse_array["InvoiceCode"])) {
                $invoice->InvoiceCode = $invoice->newConceptCode();
            }
            $invoice->SentDate = "0000-00-00 00:00:00";
        } else {
            $invoice->InvoiceCode = $parse_array["InvoiceCode"];
        }
        if($invoice->InvoiceCode == "") {
            $invoice->InvoiceCode = $invoice->newInvoiceCode();
        } elseif(!$invoice->is_free($invoice->InvoiceCode)) {
            HostFact_API::parseError([__("invalid invoicecode")], true);
        }
        $aAddInvoiceLines = $this->_updateInvoiceLines($parse_array);
        if(!empty($this->object->Error)) {
            HostFact_API::parseError($this->object->Error, true);
        }
        if(isset($aAddInvoiceLines) && 0 < count($aAddInvoiceLines)) {
            $invoice->IgnoreDiscount = in_array($invoice->IgnoreDiscount, ["yes", "no"]) ? $invoice->IgnoreDiscount == "yes" ? 1 : 0 : $invoice->IgnoreDiscount;
            $invoice->Date = isset($parse_array["Date"]) ? rewrite_date_db2site($parse_array["Date"]) : $invoice->Date;
            $invoice->SentDate = isset($parse_array["SentDate"]) ? rewrite_date_db2site($parse_array["SentDate"]) : $invoice->SentDate;
            $invoice->ReminderDate = isset($parse_array["ReminderDate"]) ? rewrite_date_db2site($parse_array["ReminderDate"]) : $invoice->ReminderDate;
            $invoice->SummationDate = isset($parse_array["SummationDate"]) ? rewrite_date_db2site($parse_array["SummationDate"]) : $invoice->SummationDate;
            $invoice->PayDate = isset($parse_array["PayDate"]) ? rewrite_date_db2site($parse_array["PayDate"]) : $invoice->PayDate;
            $invoice->TaxRate = $this->_check_total_taxpercentages(isset($parse_array["TaxRate"]) ? floatval($parse_array["TaxRate"]) / 100 : $invoice->TaxRate);
            if(isset($this->_object_parameters["CustomFields"])) {
                $invoice->getDefaultCustomValuesWithDebtorSync();
                if(isset($parse_array["CustomFields"])) {
                    foreach ($parse_array["CustomFields"] as $_custom_field => $_custom_value) {
                        $invoice->customvalues[$_custom_field] = $_custom_value;
                    }
                }
            }
            if($invoice->add()) {
                HostFact_API::commit();
                return $this->_show_invoice($invoice->Identifier);
            }
            HostFact_API::parseError($invoice->Error, true);
        } else {
            HostFact_API::parseError(sprintf(__("no invoice elements"), $invoice->InvoiceCode), true);
        }
    }
    protected function _deleteInvoiceLines($parse_array = [])
    {
        $invoiceLines = 0;
        if(!empty($parse_array["InvoiceLines"])) {
            foreach ($parse_array["InvoiceLines"] as $key => $elementData) {
                if(!isset($elementData["Identifier"])) {
                } else {
                    $invoiceelement = new invoiceelement();
                    $invoiceelement->Identifier = $elementData["Identifier"];
                    if(!$invoiceelement->show()) {
                        HostFact_API::parseError(sprintf(__("there is no invoiceline with id x"), $elementData["Identifier"]));
                    } elseif($this->object->InvoiceCode != $invoiceelement->InvoiceCode) {
                        HostFact_API::parseError(__("cannot remove invoicelines from another invoice"));
                    } elseif($invoiceelement->delete()) {
                        $invoiceLines++;
                    }
                }
            }
            if(count($parse_array["InvoiceLines"]) == $this->object->Elements["CountRows"] && !HostFact_API::hasErrors()) {
                HostFact_API::parseError(__("cannot remove all the invoice lines"));
            }
        }
        return $invoiceLines;
    }
    public function edit_api_action()
    {
        $invoice = $this->object;
        $invoice->Identifier = $this->_get_invoice_id();
        if(!$invoice->show()) {
            HostFact_API::parseError($invoice->Error, true);
        }
        HostFact_API::beginTransaction();
        $parse_array = $this->getValidParameters();
        if($debtor_id = $this->_get_debtor_id()) {
            $invoice->changeDebtor($invoice->Identifier, $debtor_id);
            $invoice->show();
        }
        $old_invoiceStatus = $invoice->Status;
        $old_invoiceCode = $invoice->InvoiceCode;
        if(isset($parse_array["Status"]) && $parse_array["Status"] == "0" || (int) $invoice->Status === 0 && !isset($parse_array["Status"])) {
            if(substr($invoice->InvoiceCode, 0, 9) != "[concept]" && (!isset($parse_array["InvoiceCode"]) || substr($parse_array["InvoiceCode"], 0, 9) != "[concept]" || !$invoice->is_free($parse_array["InvoiceCode"]))) {
                $parse_array["InvoiceCode"] = $invoice->newConceptCode();
            }
            unset($parse_array["SentDate"]);
        } elseif((isset($parse_array["Status"]) && $parse_array["Status"] != "0" || (int) $invoice->Status !== 0) && (isset($parse_array["InvoiceCode"]) && substr($parse_array["InvoiceCode"], 0, 9) == "[concept]" || substr($invoice->InvoiceCode, 0, 9) == "[concept]" && !isset($parse_array["InvoiceCode"]))) {
            $parse_array["InvoiceCode"] = $invoice->newInvoiceCode();
        }
        if(isset($parse_array["InvoiceCode"]) && !$invoice->is_free($parse_array["InvoiceCode"])) {
            HostFact_API::parseError([sprintf(__("invoicecode not available"), $parse_array["InvoiceCode"])], true);
        } elseif(isset($parse_array["InvoiceCode"]) && $invoice->changeInvoiceCode($invoice->Identifier, $parse_array["InvoiceCode"]) === false) {
            HostFact_API::parseError($invoice->Error, true);
        }
        foreach ($invoice as $key => $value) {
            if((is_string($value) || is_numeric($value)) && !in_array($key, ["Debtor"])) {
                $invoice->{$key} = isset($parse_array[$key]) ? $parse_array[$key] : htmlspecialchars_decode($value);
            }
        }
        $aUpdatedInvoiceLines = $this->_updateInvoiceLines($parse_array);
        $invoice->IgnoreDiscount = in_array($invoice->IgnoreDiscount, ["yes", "no"]) ? $invoice->IgnoreDiscount == "yes" ? 1 : 0 : $invoice->IgnoreDiscount;
        $invoice->Date = isset($parse_array["Date"]) ? rewrite_date_db2site($parse_array["Date"]) : $invoice->Date;
        $invoice->SentDate = isset($parse_array["SentDate"]) ? rewrite_date_db2site($parse_array["SentDate"]) : $invoice->SentDate;
        $invoice->ReminderDate = isset($parse_array["ReminderDate"]) ? rewrite_date_db2site($parse_array["ReminderDate"]) : $invoice->ReminderDate;
        $invoice->SummationDate = isset($parse_array["SummationDate"]) ? rewrite_date_db2site($parse_array["SummationDate"]) : $invoice->SummationDate;
        $invoice->PayDate = isset($parse_array["PayDate"]) ? rewrite_date_db2site($parse_array["PayDate"]) : $invoice->PayDate;
        $invoice->TaxRate = $this->_check_total_taxpercentages(isset($parse_array["TaxRate"]) ? floatval($parse_array["TaxRate"]) / 100 : $invoice->TaxRate);
        if(isset($parse_array["CustomFields"])) {
            foreach ($parse_array["CustomFields"] as $_custom_field => $_custom_value) {
                $invoice->customvalues[$_custom_field] = $_custom_value;
            }
        }
        if(empty($invoice->Error) && $invoice->edit()) {
            if(isset($parse_array["Status"]) && (int) $parse_array["Status"] === 0 && 0 < $old_invoiceStatus && strpos($invoice->InvoiceCode, "[concept]") !== false && $invoice->newInvoiceCode() != $old_invoiceCode) {
                HostFact_API::parseWarning(sprintf(__("invoice status change caused a gap"), $old_invoiceCode));
            }
            HostFact_API::commit();
            return $this->_show_invoice($invoice->Identifier);
        }
        HostFact_API::parseError($invoice->Error, true);
    }
    protected function _updateInvoiceLines($parse_array)
    {
        $this->object->Debtor = 0 < $this->object->Debtor ? $this->object->Debtor : $this->_get_debtor_id();
        $linesUpdated = [];
        $linesAdded = 0;
        if(isset($parse_array["InvoiceLines"]) && is_array($parse_array["InvoiceLines"]) && 0 < count($parse_array["InvoiceLines"])) {
            $addInvoicelines = false;
            foreach ($parse_array["InvoiceLines"] as $key => $elementData) {
                $check_price_period = !isset($elementData["PriceExcl"]) ? true : false;
                $invoiceelement = new invoiceelement();
                $invoiceelement->VatCalcMethod = $this->object->VatCalcMethod;
                if(!isset($elementData["Identifier"])) {
                    $add_or_edit = "add";
                    if(isset($elementData["ProductCode"]) && 0 < strlen($elementData["ProductCode"])) {
                        $elementData = $this->_checkProductCode($elementData);
                        if($elementData === false) {
                        }
                    }
                } else {
                    $add_or_edit = "edit";
                    $invoiceelement->Identifier = $elementData["Identifier"];
                    if(!$invoiceelement->show()) {
                        HostFact_API::parseError($invoiceelement->Error);
                    } else {
                        if(isset($elementData["ProductCode"]) && 0 < strlen($elementData["ProductCode"])) {
                            $elementData = $this->_checkProductCode($elementData);
                            if($elementData === false) {
                            }
                        } else {
                            $elementData["PeriodicType"] = isset($elementData["PeriodicType"]) ? $elementData["PeriodicType"] : (0 < $invoiceelement->PeriodicID ? "period" : "once");
                        }
                        if($invoiceelement->InvoiceCode != $this->object->InvoiceCode || $invoiceelement->Debtor != $this->object->Debtor) {
                            HostFact_API::parseError(sprintf(__("cannot edit invoice element"), $invoiceelement->Identifier, $invoiceelement->InvoiceCode));
                        }
                    }
                }
                $invoiceelement->InvoiceCode = $this->object->InvoiceCode;
                $invoiceelement->Debtor = $this->object->Debtor;
                $invoiceelement->Date = isset($elementData["Date"]) && strlen($elementData["Date"]) ? rewrite_date_db2site($elementData["Date"]) : $invoiceelement->Date;
                $invoiceelement->Number = isset($elementData["Number"]) ? $elementData["Number"] : ($invoiceelement->Number ? $invoiceelement->Number : 1);
                $invoiceelement->NumberSuffix = isset($elementData["NumberSuffix"]) ? $elementData["NumberSuffix"] : htmlspecialchars_decode($invoiceelement->NumberSuffix);
                $invoiceelement->ProductCode = isset($elementData["ProductCode"]) ? $elementData["ProductCode"] : htmlspecialchars_decode($invoiceelement->ProductCode);
                $invoiceelement->Description = isset($elementData["Description"]) ? $elementData["Description"] : htmlspecialchars_decode($invoiceelement->Description);
                $invoiceelement->PriceExcl = isset($elementData["PriceExcl"]) ? $elementData["PriceExcl"] : $invoiceelement->PriceExcl;
                $invoiceelement->DiscountPercentage = isset($elementData["DiscountPercentage"]) ? $elementData["DiscountPercentage"] : floatval($invoiceelement->DiscountPercentage) * 100;
                $invoiceelement->DiscountPercentageType = isset($elementData["DiscountPercentageType"]) ? $elementData["DiscountPercentageType"] : $invoiceelement->DiscountPercentageType;
                $invoiceelement->TaxPercentage = $this->_check_taxpercentage(isset($elementData["TaxPercentage"]) ? floatval($elementData["TaxPercentage"]) / 100 : $invoiceelement->TaxPercentage);
                if(isset($elementData["PeriodicType"]) && $elementData["PeriodicType"] == "period") {
                    $invoiceelement->Periods = isset($elementData["Periods"]) ? $elementData["Periods"] : $invoiceelement->Periods;
                    $invoiceelement->Periodic = isset($elementData["Periodic"]) ? $elementData["Periodic"] : $invoiceelement->Periodic;
                    $invoiceelement->StartPeriod = isset($elementData["StartPeriod"]) && $elementData["StartPeriod"] ? rewrite_date_db2site($elementData["StartPeriod"]) : (0 < strlen($invoiceelement->StartPeriod) && $invoiceelement->StartPeriod != "0000-00-00" ? rewrite_date_db2site($invoiceelement->StartPeriod) : rewrite_date_db2site(date("Y-m-d")));
                    $invoiceelement->EndPeriod = "";
                    if($check_price_period === true) {
                        $invoiceelement->PriceExcl = $this->_checkPricePeriod($invoiceelement->ProductCode, $invoiceelement->Periods, $invoiceelement->Periodic, $invoiceelement->PriceExcl);
                    }
                    if(isset($elementData["ProductType"]) && $elementData["ProductType"] || isset($elementData["Reference"]) && 0 < $elementData["Reference"]) {
                        $invoiceelement->ProductType = isset($elementData["ProductType"]) ? $elementData["ProductType"] : $invoiceelement->ProductType;
                        $invoiceelement->Reference = $this->_checkProductTypeReference($invoiceelement->ProductType, isset($elementData["Reference"]) ? $elementData["Reference"] : $invoiceelement->Reference);
                        if($invoiceelement->Reference === false) {
                        } elseif($this->_getPeriodicID($invoiceelement->ProductType, $invoiceelement->Reference, $elementData) === false) {
                        } else {
                            $this->object->Error[] = sprintf(__("service already has a periodic"), $invoiceelement->ProductType, $invoiceelement->Reference);
                        }
                    }
                } else {
                    $invoiceelement->Periods = "1";
                    $invoiceelement->Periodic = "";
                    $invoiceelement->StartPeriod = "";
                    $invoiceelement->EndPeriod = "";
                }
                if($add_or_edit == "edit" && !($result_elements = $invoiceelement->edit())) {
                    HostFact_API::parseError($invoiceelement->Error);
                } else {
                    if($add_or_edit == "add") {
                        $invoiceelement->Ordering = $this->object->Elements["CountRows"] + $linesAdded;
                        if(!($result_elements = $invoiceelement->add())) {
                            $this->object->Error = array_merge($this->object->Error, $invoiceelement->Error);
                        } else {
                            $linesAdded++;
                        }
                    }
                    $linesUpdated[] = $invoiceelement->Identifier;
                }
            }
        }
        return $linesUpdated;
    }
    public function delete_api_action()
    {
        $invoice = $this->object;
        $invoice->Identifier = $this->_get_invoice_id();
        if($invoice->show()) {
            if((int) $invoice->Status === 0) {
                HostFact_API::beginTransaction();
                if($invoice->delete(true)) {
                    HostFact_API::commit();
                    HostFact_API::parseSuccess($invoice->Success, true);
                } else {
                    HostFact_API::parseError($invoice->Error, true);
                }
            } else {
                HostFact_API::parseError([__("only remove concept invoices")], true);
            }
        } else {
            HostFact_API::parseError($invoice->Error, true);
        }
    }
    public function credit_api_action()
    {
        $invoice = $this->object;
        $invoice->Identifier = $this->_get_invoice_id();
        if($invoice->show()) {
            HostFact_API::beginTransaction();
            if($invoice->Status == 9) {
                HostFact_API::parseError(sprintf(__("api cannot create creditinvoice"), $invoice->InvoiceCode), true);
            } elseif((int) $invoice->Status === 0 || substr($invoice->InvoiceCode, 0, 9) == "[concept]") {
                HostFact_API::parseError(sprintf(__("api cannot create creditinvoice from concept"), $invoice->InvoiceCode), true);
            } elseif(!$invoice->delete(false) && !empty($invoice->Error)) {
                HostFact_API::parseError($invoice->Error, true);
            } else {
                HostFact_API::commit();
                HostFact_API::parseSuccess($invoice->Success);
                return $this->_show_invoice($invoice->Identifier);
            }
        } else {
            HostFact_API::parseError($invoice->Error, true);
        }
    }
    public function download_api_action()
    {
        $filetype = HostFact_API::getRequestParameter("FileType");
        if($filetype && !in_array($filetype, ["ubl", "pdf", "ublwithpdf"])) {
            $this->object->Error[] = __("api invoice download invalid filetype");
            HostFact_API::parseError($this->object->Error, true);
        }
        if(!$filetype) {
            $filetype = "pdf";
        }
        $invoice = $this->object;
        $invoice->Identifier = $this->_get_invoice_id();
        $invoice->Debtor = 0 < $invoice->Debtor ? $invoice->Debtor : $this->_get_debtor_id();
        if($invoice->show()) {
            require_once "class/debtor.php";
            $debtor = new debtor();
            $debtor->Identifier = $invoice->Debtor;
            $debtor->show();
            if($filetype == "pdf" || $filetype == "ublwithpdf") {
                error_reporting(0);
                $OutputType = "F";
                require_once "class/pdf.php";
                $template = $invoice->Template;
                $pdf = new pdfCreator($template, ["invoice" => $invoice, "debtor" => $debtor], "invoice", "D", true);
                $pdf->setOutputType("F");
                if(!$pdf->generatePDF("F")) {
                    HostFact_API::parseError($pdf->Error, true);
                }
                $handle = fopen("temp/" . $pdf->Name, "r");
                $filedata = fread($handle, filesize("temp/" . $pdf->Name));
                fclose($handle);
                $invoice_filedata = $filedata;
                $invoice_filename = $pdf->Name;
            }
            if($filetype == "ublwithpdf") {
                $pdf_object = ["path" => "temp/", "filename" => $pdf->Name];
            }
            if($filetype == "ublwithpdf" || $filetype == "ubl") {
                $invoice->show();
                $invoice->format(false);
                require_once "class/ubl.php";
                $ubl = new UBL();
                $ubl_objects = ["invoice" => $invoice, "debtor" => $debtor];
                if(isset($pdf_object)) {
                    $ubl_objects["pdf"] = $pdf_object;
                }
                $ubl->layout_template = $invoice->Template;
                $invoice_filedata = $ubl->generateInvoiceUBL($ubl_objects);
                $invoice_filename = $ubl->UBLFilename;
            }
            if(isset($pdf->Name)) {
                @unlink("temp/" . $pdf->Name);
            }
            if(defined("API_CALL_FROM_CLIENTAREA") && API_CALL_FROM_CLIENTAREA === true) {
                createLog("invoice", $invoice->Identifier, "invoice downloaded via clientarea");
            } else {
                createLog("invoice", $invoice->Identifier, "invoice downloaded via api");
            }
            $result = [];
            $result["Filename"] = $invoice_filename;
            $result["Base64"] = base64_encode($invoice_filedata);
            return HostFact_API::parseResponse($result);
        }
        HostFact_API::parseError($invoice->Error, true);
    }
    public function paymentprocesspause_api_action()
    {
        $invoice = $this->object;
        $invoice->Identifier = $this->_get_invoice_id();
        if($invoice->show()) {
            if($invoice->changePaymentProcessStatus("pause")) {
                HostFact_API::parseSuccess($invoice->Success, true);
            } else {
                HostFact_API::parseError($invoice->Error, true);
            }
        } else {
            HostFact_API::parseError($invoice->Error, true);
        }
    }
    public function paymentprocessreactivate_api_action()
    {
        $invoice = $this->object;
        $invoice->Identifier = $this->_get_invoice_id();
        if($invoice->show()) {
            if($invoice->changePaymentProcessStatus("reactivate")) {
                HostFact_API::parseSuccess($invoice->Success, true);
            } else {
                HostFact_API::parseError($invoice->Error, true);
            }
        } else {
            HostFact_API::parseError($invoice->Error, true);
        }
    }
    public function block_api_action()
    {
        $invoice = $this->object;
        $invoice->Identifier = $this->_get_invoice_id();
        if($invoice->show()) {
            if($invoice->changeBlockStatus("block")) {
                HostFact_API::parseSuccess($invoice->Success, true);
            } else {
                HostFact_API::parseError($invoice->Error, true);
            }
        } else {
            HostFact_API::parseError($invoice->Error, true);
        }
    }
    public function schedule_api_action()
    {
        $invoice = $this->object;
        $invoice->Identifier = $this->_get_invoice_id();
        if($invoice->show()) {
            if($invoice->scheduleDraftInvoice(HostFact_API::getRequestParameter("ScheduledAt"))) {
                HostFact_API::parseSuccess($invoice->Success, true);
            } else {
                HostFact_API::parseError($invoice->Error, true);
            }
        } else {
            HostFact_API::parseError($invoice->Error, true);
        }
    }
    public function cancelschedule_api_action()
    {
        $invoice = $this->object;
        $invoice->Identifier = $this->_get_invoice_id();
        if($invoice->show()) {
            if($invoice->undoScheduleDraftInvoice()) {
                HostFact_API::parseSuccess($invoice->Success, true);
            } else {
                HostFact_API::parseError($invoice->Error, true);
            }
        } else {
            HostFact_API::parseError($invoice->Error, true);
        }
    }
    public function unblock_api_action()
    {
        $invoice = $this->object;
        $invoice->Identifier = $this->_get_invoice_id();
        if($invoice->show()) {
            if($invoice->changeBlockStatus("unblock")) {
                HostFact_API::parseSuccess($invoice->Success, true);
            } else {
                HostFact_API::parseError($invoice->Error, true);
            }
        } else {
            HostFact_API::parseError($invoice->Error, true);
        }
    }
    public function markaspaid_api_action()
    {
        $parse_array = $this->getValidParameters();
        $invoice = $this->object;
        $invoice->Identifier = $this->_get_invoice_id();
        if($invoice->show()) {
            if($invoice->Status == 4) {
                HostFact_API::parseError(__("api invoice already paid"), true);
            } elseif($invoice->Status <= 3 && substr($invoice->InvoiceCode, 0, 8) != "[concept") {
                if(isset($parse_array["PayDate"])) {
                    $result = $invoice->markaspaid($parse_array["PayDate"]);
                } else {
                    $result = $invoice->markaspaid();
                }
                if($result) {
                    $invoice->checkAuto();
                    HostFact_API::parseSuccess($invoice->Success, true);
                }
            } elseif($invoice->Status <= 3 && substr($invoice->InvoiceCode, 0, 8) == "[concept") {
                HostFact_API::parseError(__("invoice not marked as paid, because status or conceptcode"), $invoice->InvoiceCode);
            }
        } else {
            HostFact_API::parseError($invoice->Error, true);
        }
    }
    public function markasunpaid_api_action()
    {
        $invoice = $this->object;
        $invoice->Identifier = $this->_get_invoice_id();
        if($invoice->show()) {
            if($invoice->Status == 4) {
                if($invoice->markasunpaid()) {
                    HostFact_API::parseSuccess($invoice->Success, true);
                }
            } else {
                HostFact_API::parseError(__("api invoice not marked as paid"), true);
            }
        }
        HostFact_API::parseError($invoice->Error, true);
    }
    public function partpayment_api_action()
    {
        $parse_array = $this->getValidParameters();
        $invoice = $this->object;
        $invoice->Identifier = $this->_get_invoice_id();
        if($invoice->show()) {
            if($invoice->Status <= 3 && substr($invoice->InvoiceCode, 0, 8) != "[concept") {
                $payDate = isset($parse_array["PayDate"]) && $parse_array["PayDate"] ? $parse_array["PayDate"] : "";
                $invoice->partpayment($parse_array["AmountPaid"], $payDate);
                if($invoice->Status == 4) {
                    $invoice->checkAuto();
                }
                HostFact_API::parseSuccess($invoice->Success);
                return $this->_show_invoice($invoice->Identifier);
            }
            if((int) $invoice->Status === 0 && substr($invoice->InvoiceCode, 0, 8) == "[concept") {
                HostFact_API::parseError(sprintf(__("invoice is still concept invoice, cannot do part payment"), $invoice->InvoiceCode), true);
            } elseif(4 <= $invoice->Status) {
                HostFact_API::parseError(__("api invoice already paid"), true);
            }
        } else {
            HostFact_API::parseError($invoice->Error, true);
        }
    }
    private function _checkInvoiceDebtorData($param = [])
    {
        require_once "class/debtor.php";
        $debtor = new debtor();
        $debtor->Identifier = $this->_get_debtor_id();
        if(!$debtor->show()) {
            HostFact_API::parseError($debtor->Error, true);
        }
        $this->object->Debtor = 0 < $this->object->Debtor ? $this->object->Debtor : $this->_get_debtor_id();
        $this->object->InvoiceMethod = isset($param["InvoiceMethod"]) ? $param["InvoiceMethod"] : $debtor->InvoiceMethod;
        $this->object->CompanyName = isset($param["CompanyName"]) ? $param["CompanyName"] : ($debtor->InvoiceCompanyName ? htmlspecialchars_decode($debtor->InvoiceCompanyName) : htmlspecialchars_decode($debtor->CompanyName));
        $this->object->TaxNumber = isset($param["TaxNumber"]) ? $param["TaxNumber"] : htmlspecialchars_decode($debtor->TaxNumber);
        $this->object->Initials = isset($param["Initials"]) ? $param["Initials"] : ($debtor->InvoiceInitials ? htmlspecialchars_decode($debtor->InvoiceInitials) : htmlspecialchars_decode($debtor->Initials));
        $this->object->SurName = isset($param["SurName"]) ? $param["SurName"] : ($debtor->InvoiceSurName ? htmlspecialchars_decode($debtor->InvoiceSurName) : htmlspecialchars_decode($debtor->SurName));
        $this->object->Sex = isset($param["Sex"]) ? $param["Sex"] : ($debtor->InvoiceSurName && $debtor->InvoiceSex ? htmlspecialchars_decode($debtor->InvoiceSex) : htmlspecialchars_decode($debtor->Sex));
        $this->object->Address = isset($param["Address"]) ? $param["Address"] : ($debtor->InvoiceAddress ? htmlspecialchars_decode($debtor->InvoiceAddress) : htmlspecialchars_decode($debtor->Address));
        if(defined("IS_INTERNATIONAL") && IS_INTERNATIONAL === true) {
            $this->object->Address2 = isset($param["Address2"]) ? $param["Address2"] : ($debtor->InvoiceAddress2 ? htmlspecialchars_decode($debtor->InvoiceAddress2) : htmlspecialchars_decode($debtor->Address2));
        }
        $this->object->ZipCode = isset($param["ZipCode"]) ? $param["ZipCode"] : ($debtor->InvoiceZipCode ? htmlspecialchars_decode($debtor->InvoiceZipCode) : htmlspecialchars_decode($debtor->ZipCode));
        $this->object->City = isset($param["City"]) ? $param["City"] : ($debtor->InvoiceCity ? htmlspecialchars_decode($debtor->InvoiceCity) : htmlspecialchars_decode($debtor->City));
        if(defined("IS_INTERNATIONAL") && IS_INTERNATIONAL === true) {
            $this->object->State = isset($param["State"]) ? $param["State"] : ($debtor->InvoiceState ? htmlspecialchars_decode($debtor->InvoiceState) : htmlspecialchars_decode($debtor->State));
        }
        $this->object->Country = isset($param["Country"]) ? $param["Country"] : ($debtor->InvoiceCountry && $debtor->InvoiceAddress ? $debtor->InvoiceCountry : $debtor->Country);
        $this->object->EmailAddress = isset($param["EmailAddress"]) ? $param["EmailAddress"] : ($debtor->InvoiceEmailAddress ? $debtor->InvoiceEmailAddress : $debtor->EmailAddress);
        $this->object->Authorisation = isset($param["Authorisation"]) ? $param["Authorisation"] : $debtor->InvoiceAuthorisation;
        if(!isset($param["Template"]) || $param["Template"] == "") {
            $this->object->Template = 0 < $debtor->InvoiceTemplate ? $debtor->InvoiceTemplate : $this->object->Template;
        } else {
            $this->object->Template = $param["Template"];
        }
        if(!isset($param["Term"]) && !is_null($debtor->InvoiceTerm)) {
            $this->object->Term = $debtor->InvoiceTerm;
        }
    }
    protected function _get_invoice_id()
    {
        $invoice_id = HostFact_API::getRequestParameter("Identifier");
        if(0 < $invoice_id) {
            if(defined("API_CALL_FROM_CLIENTAREA") && API_CALL_FROM_CLIENTAREA === true && !empty(debtor_id::$ClientArea)) {
                $invoice_id = $this->object->getID("clientarea", $invoice_id, ClientArea::$debtor_id);
            } else {
                $invoice_id = $this->object->getID("identifier", $invoice_id);
            }
            return $invoice_id;
        }
        if($invoiceCode = HostFact_API::getRequestParameter("InvoiceCode")) {
            $invoice_id = $this->object->getID("invoicecode", $invoiceCode);
            if(0 < $invoice_id && defined("API_CALL_FROM_CLIENTAREA") && API_CALL_FROM_CLIENTAREA === true && !empty(debtor_id::$ClientArea)) {
                $invoice_id = $this->object->getID("clientarea", $invoice_id, ClientArea::$debtor_id);
            }
            return $invoice_id;
        }
        return false;
    }
    protected function _show_invoice($invoice_id)
    {
        $invoice = $this->object;
        $invoice->Identifier = $invoice_id;
        if(!$invoice->show()) {
            HostFact_API::parseError($invoice->Error, true);
        }
        require_once "class/debtor.php";
        $debtor = new debtor();
        $debtor->Identifier = $invoice->Debtor;
        $debtor->show();
        $result = [];
        foreach ($this->_object_parameters as $field => $field_info) {
            if($field == "InvoiceLines" && 0 < $invoice->Elements["CountRows"]) {
                foreach ($invoice->Elements as $key => $value) {
                    if(is_numeric($key) && !empty($invoice->Elements[$key])) {
                        $line_data = [];
                        $line_data["Identifier"] = $key;
                        foreach ($this->_object_parameters[$field]["children"] as $elementKey => $elementValue) {
                            if(isset($invoice->Elements[$key][$elementKey])) {
                                if(in_array($elementKey, ["DiscountPercentage", "TaxPercentage"])) {
                                    $line_data[$elementKey] = $invoice->Elements[$key][$elementKey] * 100;
                                } else {
                                    $line_data[$elementKey] = is_string($invoice->Elements[$key][$elementKey]) ? htmlspecialchars_decode($invoice->Elements[$key][$elementKey]) : $invoice->Elements[$key][$elementKey];
                                }
                            }
                        }
                        $line_data["Date"] = $this->_filter_date_site2api($line_data["Date"]);
                        $line_data["StartPeriod"] = $this->_filter_date_site2api($line_data["StartPeriod"]);
                        $line_data["EndPeriod"] = $this->_filter_date_site2api($line_data["EndPeriod"]);
                        unset($line_data["InvoiceCode"]);
                        unset($line_data["Debtor"]);
                        $inv_element = new invoiceelement();
                        $inv_element->Identifier = $line_data["Identifier"];
                        $inv_element->show();
                        $inv_element->format();
                        $line_data["NoDiscountAmountIncl"] = deformat_money($inv_element->NoDiscountAmountIncl);
                        $line_data["NoDiscountAmountExcl"] = deformat_money($inv_element->NoDiscountAmountExcl);
                        $line_data["DiscountAmountIncl"] = deformat_money($inv_element->DiscountAmountIncl);
                        $line_data["DiscountAmountExcl"] = deformat_money($inv_element->DiscountAmountExcl);
                        $result["InvoiceLines"][] = $line_data;
                    }
                }
            } elseif(isset($invoice->{$field})) {
                $result[$field] = is_string($invoice->{$field}) ? htmlspecialchars_decode($invoice->{$field}) : $invoice->{$field};
            } else {
                switch ($field) {
                    case "AmountTax":
                        $result[$field] = number_format(round($invoice->AmountIncl - $invoice->AmountExcl, 2), 2, ".", "");
                        break;
                    case "DebtorCode":
                        $result[$field] = htmlspecialchars_decode($debtor->DebtorCode);
                        break;
                }
            }
        }
        $result["Date"] = $this->_filter_date_db2api($result["Date"]);
        if((int) $invoice->Status === 0) {
            $first_array = array_splice($result, 0, array_search("SentDate", array_keys($result)));
            $result = array_merge($first_array, ["ScheduledAt" => $this->_filter_date_db2api($result["SentDate"], false)], $result);
            $result["SentDate"] = $this->_filter_date_db2api("0000-00-00 00:00:00");
        } else {
            $result["SentDate"] = $this->_filter_date_db2api($result["SentDate"]);
        }
        $result["ReminderDate"] = $this->_filter_date_db2api($result["ReminderDate"]);
        $result["SummationDate"] = $this->_filter_date_db2api($result["SummationDate"]);
        $result["PayDate"] = $this->_filter_date_db2api($result["PayDate"]);
        $result["PayBefore"] = $this->_filter_date_db2api($result["PayBefore"]);
        $result["PaymentURL"] = $invoice->Status == 2 || $invoice->Status == 3 ? htmlspecialchars_decode($invoice->PaymentURLRaw) : "";
        $result["IgnoreDiscount"] = $invoice->IgnoreDiscount == 1 ? "yes" : "no";
        $result["TaxRate"] = $result["TaxRate"] * 100;
        global $array_country;
        global $array_invoicemethod;
        global $array_invoicestatus;
        global $array_paymentmethod;
        $template = new template();
        $template->Identifier = $invoice->Template;
        $template->show();
        require_once "class/attachment.php";
        $attachment = new attachment();
        $Attachments = $attachment->getAttachments($invoice->Identifier, "invoice", true);
        if(is_array($Attachments)) {
            foreach ($Attachments as $attachment) {
                $result["Attachments"][] = ["Identifier" => $attachment->id, "Filename" => $attachment->Filename];
            }
        }
        if(isset($this->_object_parameters["CustomFields"])) {
            $result["CustomFields"] = $invoice->customvalues;
        }
        $result["Translations"] = ["Status" => isset($array_invoicestatus[$invoice->Status]) ? $array_invoicestatus[$invoice->Status] : "", "State" => isset($invoice->StateName) ? $invoice->StateName : "", "Country" => isset($array_country[$invoice->Country]) ? $array_country[$invoice->Country] : "", "InvoiceMethod" => isset($array_invoicemethod[$invoice->InvoiceMethod]) ? $array_invoicemethod[$invoice->InvoiceMethod] : "", "Template" => $template->Name, "PaymentMethod" => isset($array_paymentmethod[$invoice->PaymentMethod]) ? $array_paymentmethod[$invoice->PaymentMethod] : ""];
        if(!defined("IS_INTERNATIONAL") || IS_INTERNATIONAL !== true) {
            unset($result["Translations"]["State"]);
        }
        $invoice->format(false);
        $result["AmountDiscount"] = deformat_money($invoice->AmountDiscount);
        $result["AmountDiscountIncl"] = deformat_money($invoice->AmountDiscountIncl);
        $taxrates = $invoice->used_taxrates;
        foreach ($taxrates as $_rate => $_amounts) {
            $taxrates[$_rate]["AmountExcl"] = deformat_money($taxrates[$_rate]["AmountExcl"]);
            $taxrates[$_rate]["AmountTax"] = deformat_money($taxrates[$_rate]["AmountTax"]);
            $taxrates[$_rate]["AmountIncl"] = deformat_money($taxrates[$_rate]["AmountIncl"]);
        }
        $result["UsedTaxrates"] = $taxrates;
        $result["Created"] = $this->_filter_date_db2api($invoice->Created, false);
        $result["Modified"] = $this->_filter_date_db2api($invoice->Modified, false);
        return HostFact_API::parseResponse($result);
    }
    protected function getFilterValues()
    {
        $filters = parent::getFilterValues();
        if(isset($filters["searchat"]) && $filters["searchat"] == "CustomFields") {
            HostFact_API::parseError("Invalid filter 'searchat'. Please enter valid columns or remove filter", true);
        }
        if(isset($filters["status"]) && $filters["status"] && !in_array($filters["status"], ["reminders", "summations"])) {
            global $array_invoicestatus;
            $tmp_status = explode("|", $filters["status"]);
            foreach ($tmp_status as $tmp_status_key) {
                if(!array_key_exists($tmp_status_key, $array_invoicestatus)) {
                    HostFact_API::parseError("Invalid filter 'status'. The status '" . $tmp_status_key . "' does not exist", true);
                }
            }
        }
        return $filters;
    }
}

?>