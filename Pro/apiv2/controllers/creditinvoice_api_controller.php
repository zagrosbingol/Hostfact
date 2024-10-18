<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class creditinvoice_api_controller extends api_controller
{
    public function __construct()
    {
        parent::__construct();
        HostFact_API::setObjectNames("creditinvoices", "creditinvoice");
        require_once "class/creditinvoice.php";
        $this->addParameter("Identifier", "int");
        $this->addParameter("CreditInvoiceCode", "string");
        $this->addParameter("InvoiceCode", "string");
        $this->addParameter("Creditor", "int");
        $this->addParameter("CreditorCode", "string");
        $this->addParameter("Status", "int");
        $this->addParameter("Date", "date");
        $this->addParameter("Term", "int");
        $this->addParameter("AmountExcl", "readonly");
        $this->addParameter("AmountTax", "readonly");
        $this->addParameter("AmountIncl", "readonly");
        $this->addParameter("AmountPaid", "float");
        $this->addParameter("Authorisation", "string");
        $this->addParameter("Private", "float");
        $this->addParameter("PrivatePercentage", "int");
        $this->addParameter("ReferenceNumber", "string");
        $this->addParameter("PayDate", "date");
        $this->addParameter("InvoiceLines", "array");
        $this->addSubParameter("InvoiceLines", "Identifier", "int");
        $this->addSubParameter("InvoiceLines", "Number", "float");
        $this->addSubParameter("InvoiceLines", "Description", "string");
        $this->addSubParameter("InvoiceLines", "PriceExcl", "double");
        $this->addSubParameter("InvoiceLines", "TaxPercentage", "float");
        $this->addParameter("Created", "readonly");
        $this->addParameter("Modified", "readonly");
        $this->addFilter("date", "filter_date");
        $this->addFilter("created", "filter_datetime");
        $this->addFilter("modified", "filter_datetime");
        $this->addFilter("status", "string", "");
        $this->addFilter("order", "string", "DESC");
        $this->object = new creditinvoice();
    }
    public function list_api_action()
    {
        $filters = $this->getFilterValues();
        $fields = ["CreditInvoiceCode", "InvoiceCode", "CompanyName", "Initials", "SurName", "Creditor", "Date", "Term", "Authorisation", "PayDate", "Status", "AmountExcl", "AmountIncl", "AmountPaid", "Modified"];
        $sort = $filters["sort"] ? $filters["sort"] : "CreditInvoiceCode";
        $searchat = $filters["searchat"] ? $filters["searchat"] : "CreditInvoiceCode|InvoiceCode|CompanyName|SurName|Description";
        $searchfor = isset($filters["searchfor"]) ? $filters["searchfor"] : "";
        $page_offset = $filters["offset"] / $filters["limit"] + 1;
        $limit = $filters["limit"];
        $field_filters = array_diff_key($filters, array_flip(["offset", "limit", "sort", "order", "searchat", "searchfor"]));
        $invoice_list = $this->object->all($fields, $sort, $filters["order"], $page_offset, $searchat, $filters["searchfor"], $field_filters, $limit);
        $array = [];
        foreach ($invoice_list as $key => $value) {
            if($key != "CountRows" && is_numeric($key)) {
                $array[] = ["Identifier" => $value["id"], "CreditInvoiceCode" => htmlspecialchars_decode($value["CreditInvoiceCode"]), "InvoiceCode" => htmlspecialchars_decode($value["InvoiceCode"]), "Creditor" => $value["Creditor"], "CompanyName" => htmlspecialchars_decode($value["CompanyName"]), "Initials" => htmlspecialchars_decode($value["Initials"]), "SurName" => htmlspecialchars_decode($value["SurName"]), "Date" => $this->_filter_date_db2api($value["Date"]), "AmountExcl" => $value["AmountExcl"], "AmountIncl" => $value["AmountIncl"], "PartPayment" => $value["PartPayment"], "Term" => $value["Term"], "Authorisation" => $value["Authorisation"], "PayDate" => $this->_filter_date_db2api($value["PayDate"]), "PayBefore" => $this->_filter_date_db2api($value["PayBefore"]), "Status" => $value["Status"], "Modified" => $this->_filter_date_db2api($value["Modified"], false)];
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
        $creditinvoice_id = $this->_get_creditinvoice_id();
        $this->_show_creditinvoice($creditinvoice_id);
    }
    public function add_api_action()
    {
        HostFact_API::beginTransaction();
        $parse_array = $this->getValidParameters();
        if(isset($parse_array["Creditor"]) || isset($parse_array["CreditorCode"])) {
            $parse_array["Creditor"] = $this->_get_creditor_id();
        }
        $creditinvoice = $this->object;
        foreach ($parse_array as $key => $value) {
            if(in_array($key, $creditinvoice->Variables)) {
                $creditinvoice->{$key} = $value;
            }
        }
        $creditor = new creditor();
        $creditor->Identifier = $parse_array["Creditor"];
        if(!$creditor->show()) {
            HostFact_API::parseError($creditor->Error, true);
        }
        if(!isset($param["Term"])) {
            $creditinvoice->Term = $creditor->Term;
        }
        $creditinvoice->Authorisation = isset($parse_array["Authorisation"]) ? $parse_array["Authorisation"] : $creditor->Authorisation;
        if($creditinvoice->CreditInvoiceCode == "") {
            $creditinvoice->CreditInvoiceCode = $creditinvoice->newCreditInvoiceCode();
        } elseif(!$creditinvoice->is_free($creditinvoice->CreditInvoiceCode)) {
            HostFact_API::parseError(sprintf(__("creditinvoicecode is already in use"), $creditinvoice->CreditInvoiceCode), true);
        }
        $aAddCreditInvoiceLines = [];
        $aAddCreditInvoiceLines = $this->_updateCreditInvoiceLines($parse_array);
        if(!empty($this->object->Error)) {
            HostFact_API::parseError($this->object->Error, true);
        }
        if(isset($aAddCreditInvoiceLines) && 0 < count($aAddCreditInvoiceLines)) {
            $creditinvoice->Date = isset($parse_array["Date"]) ? rewrite_date_db2site($parse_array["Date"]) : $creditinvoice->Date;
            $creditinvoice->PayDate = isset($parse_array["PayDate"]) ? rewrite_date_db2site($parse_array["PayDate"]) : $creditinvoice->PayDate;
            if($creditinvoice->add()) {
                HostFact_API::commit();
                return $this->_show_creditinvoice($creditinvoice->Identifier);
            }
            HostFact_API::parseError($creditinvoice->Error, true);
        } else {
            HostFact_API::parseError(sprintf(__("no invoice elements"), $creditinvoice->InvoiceCode), true);
        }
    }
    protected function _deleteCreditInvoiceLines($parse_array = [])
    {
        $invoiceLines = 0;
        if(!empty($parse_array["InvoiceLines"])) {
            foreach ($parse_array["InvoiceLines"] as $key => $elementData) {
                if(!isset($elementData["Identifier"])) {
                } else {
                    $creditinvoiceelement = new creditinvoiceelement();
                    $creditinvoiceelement->Identifier = $elementData["Identifier"];
                    if(!$creditinvoiceelement->show()) {
                        HostFact_API::parseError(sprintf(__("there is no invoiceline with id x"), $elementData["Identifier"]));
                    } elseif($this->object->CreditInvoiceCode != $creditinvoiceelement->CreditInvoiceCode) {
                        HostFact_API::parseError(__("cannot remove invoicelines from another creditinvoice"));
                    } elseif($creditinvoiceelement->delete()) {
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
        $creditInvoice = $this->object;
        $creditInvoice->Identifier = $this->_get_creditinvoice_id();
        if(!$creditInvoice->show()) {
            HostFact_API::parseError($creditInvoice->Error, true);
        }
        HostFact_API::beginTransaction();
        $parse_array = $this->getValidParameters();
        if(isset($parse_array["CreditInvoiceCode"]) && strlen($parse_array["CreditInvoiceCode"]) === 0) {
            HostFact_API::parseError(__("invalid creditinvoicecode"), true);
        }
        if(isset($parse_array["CreditInvoiceCode"]) && $parse_array["CreditInvoiceCode"] != $creditInvoice->CreditInvoiceCode) {
            if($creditInvoice->is_free($parse_array["CreditInvoiceCode"])) {
                $creditInvoice->changeCreditInvoiceCode($creditInvoice->Identifier, $parse_array["CreditInvoiceCode"]);
            } else {
                HostFact_API::parseError(sprintf(__("creditinvoicecode is already in use"), $parse_array["CreditInvoiceCode"]), true);
            }
        }
        foreach ($creditInvoice as $key => $value) {
            if(is_string($value) || is_numeric($value)) {
                $creditInvoice->{$key} = isset($parse_array[$key]) ? $parse_array[$key] : htmlspecialchars_decode($value);
            }
        }
        $this->_updateCreditInvoiceLines($parse_array);
        $creditInvoice->Date = isset($parse_array["Date"]) ? rewrite_date_db2site($parse_array["Date"]) : $creditInvoice->Date;
        $creditInvoice->PayDate = isset($parse_array["PayDate"]) ? rewrite_date_db2site($parse_array["PayDate"]) : $creditInvoice->PayDate;
        $creditInvoice->AmountIncl = 0;
        if(empty($creditInvoice->Error) && $creditInvoice->edit()) {
            HostFact_API::commit();
            return $this->_show_creditinvoice($creditInvoice->Identifier);
        }
        HostFact_API::parseError($creditInvoice->Error, true);
    }
    protected function _updateCreditInvoiceLines($parse_array)
    {
        $this->object->Creditor = 0 < $this->object->Creditor ? $this->object->Creditor : $this->_get_creditor_id();
        $linesUpdated = [];
        $linesAdded = 0;
        if(!empty($parse_array["InvoiceLines"])) {
            $addInvoicelines = false;
            foreach ($parse_array["InvoiceLines"] as $key => $elementData) {
                $creditinvoiceelement = new creditinvoiceelement();
                $add_or_edit = "add";
                if(isset($elementData["Identifier"])) {
                    $add_or_edit = "edit";
                    $creditinvoiceelement->Identifier = $elementData["Identifier"];
                    if(!$creditinvoiceelement->show()) {
                        HostFact_API::parseError($creditinvoiceelement->Error);
                    } elseif($creditinvoiceelement->CreditInvoiceCode != $this->object->CreditInvoiceCode || $creditinvoiceelement->Creditor != $this->object->Creditor) {
                        HostFact_API::parseError(sprintf(__("cannot edit credit invoice element"), $creditinvoiceelement->Identifier, $creditinvoiceelement->CreditInvoiceCode));
                    }
                }
                $creditinvoiceelement->CreditInvoiceCode = $this->object->CreditInvoiceCode;
                $creditinvoiceelement->Creditor = $this->object->Creditor;
                $creditinvoiceelement->Number = isset($elementData["Number"]) ? $elementData["Number"] : (0 < $creditinvoiceelement->Number ? $creditinvoiceelement->Number : 1);
                $creditinvoiceelement->Description = isset($elementData["Description"]) ? $elementData["Description"] : htmlspecialchars_decode($creditinvoiceelement->Description);
                $creditinvoiceelement->PriceExcl = isset($elementData["PriceExcl"]) ? $elementData["PriceExcl"] : $creditinvoiceelement->PriceExcl;
                $creditinvoiceelement->TaxPercentage = isset($elementData["TaxPercentage"]) ? floatval($elementData["TaxPercentage"]) / 100 : $creditinvoiceelement->TaxPercentage;
                if($add_or_edit == "edit" && !($result_elements = $creditinvoiceelement->edit())) {
                    HostFact_API::parseError($creditinvoiceelement->Error);
                } else {
                    if($add_or_edit == "add") {
                        if(!($result_elements = $creditinvoiceelement->add())) {
                            $this->object->Error = array_merge($this->object->Error, $creditinvoiceelement->Error);
                        } else {
                            $linesAdded++;
                        }
                    }
                    $linesUpdated[] = $creditinvoiceelement->Identifier;
                }
            }
        }
        return $linesUpdated;
    }
    public function delete_api_action()
    {
        $creditinvoice = $this->object;
        $creditinvoice->Identifier = $this->_get_creditinvoice_id();
        if($creditinvoice->show()) {
            HostFact_API::beginTransaction();
            if($creditinvoice->delete()) {
                HostFact_API::commit();
                HostFact_API::parseSuccess($creditinvoice->Success, true);
            } else {
                HostFact_API::parseError($creditinvoice->Error, true);
            }
        } else {
            HostFact_API::parseError($creditinvoice->Error, true);
        }
    }
    public function markaspaid_api_action()
    {
        $creditinvoice = $this->object;
        $creditinvoice->Identifier = $this->_get_creditinvoice_id();
        if($creditinvoice->show()) {
            if($creditinvoice->Status == 3) {
                HostFact_API::parseError(__("api credit invoice already paid"), true);
            } elseif($creditinvoice->markaspaid()) {
                HostFact_API::parseSuccess($creditinvoice->Success, true);
            } else {
                HostFact_API::parseError($creditinvoice->Error, true);
            }
        } else {
            HostFact_API::parseError($creditinvoice->Error, true);
        }
    }
    public function partpayment_api_action()
    {
        $creditinvoice = $this->object;
        $creditinvoice->Identifier = $this->_get_creditinvoice_id();
        if($creditinvoice->show()) {
            if($creditinvoice->Status == 3) {
                HostFact_API::parseError(__("api credit invoice already paid"), true);
            } else {
                $parse_array = $this->getValidParameters();
                if($creditinvoice->partpayment($parse_array["AmountPaid"])) {
                    if(!empty($creditinvoice->Warning)) {
                        HostFact_API::parseWarning($creditinvoice->Warning);
                    }
                    HostFact_API::parseSuccess($creditinvoice->Success);
                    $this->_show_creditinvoice($creditinvoice->Identifier);
                } else {
                    HostFact_API::parseError($creditinvoice->Error, true);
                }
            }
        } else {
            HostFact_API::parseError($creditinvoice->Error, true);
        }
    }
    protected function _get_creditinvoice_id()
    {
        require_once "class/creditinvoice.php";
        $creditinvoice = new creditinvoice();
        if($creditInvoice_id = HostFact_API::getRequestParameter("Identifier")) {
            $creditinvoice_id = $creditinvoice->getID("identifier", $creditInvoice_id);
            if($creditInvoice_id === false) {
                HostFact_API::parseError(__("invalid identifier for creditinvoice"), true);
            }
            return $creditInvoice_id;
        }
        if($creditInvoiceCode = HostFact_API::getRequestParameter("CreditInvoiceCode")) {
            if($creditInvoice_id = $creditinvoice->getID("creditinvoicecode", $creditInvoiceCode)) {
                return $creditInvoice_id;
            }
            HostFact_API::parseError(__("invalid creditinvoicecode"), true);
        } else {
            return false;
        }
    }
    protected function _get_creditor_id()
    {
        require_once "class/creditor.php";
        $creditor = new creditor();
        if($creditor_id = HostFact_API::getRequestParameter("Creditor")) {
            $creditor_id = $creditor->getID("identifier", $creditor_id);
            if($creditor_id === false) {
                HostFact_API::parseError(__("invalid identifier for creditor"), true);
            }
            return $creditor_id;
        }
        if($creditorCode = HostFact_API::getRequestParameter("CreditorCode")) {
            if($creditor_id = $creditor->getID("creditorcode", $creditorCode)) {
                return $creditor_id;
            }
            HostFact_API::parseError(__("invalid creditorcode"), true);
        } else {
            return false;
        }
    }
    protected function _show_creditinvoice($creditinvoice_id)
    {
        $creditinvoice = $this->object;
        $creditinvoice->Identifier = $creditinvoice_id;
        if(!$creditinvoice->show()) {
            HostFact_API::parseError($creditinvoice->Error, true);
        }
        require_once "class/creditor.php";
        $creditor = new creditor();
        $creditor->Identifier = $creditinvoice->Creditor;
        $creditor->show();
        $result = [];
        foreach ($this->_object_parameters as $field => $field_info) {
            if($field == "InvoiceLines" && isset($creditinvoice->Elements["CountRows"]) && 0 < $creditinvoice->Elements["CountRows"]) {
                foreach ($creditinvoice->Elements as $key => $value) {
                    if(is_numeric($key) && !empty($creditinvoice->Elements[$key])) {
                        $line_data = [];
                        $line_data["Identifier"] = $key;
                        foreach ($this->_object_parameters[$field]["children"] as $elementKey => $elementValue) {
                            if(isset($creditinvoice->Elements[$key][$elementKey])) {
                                if(in_array($elementKey, ["TaxPercentage"])) {
                                    $line_data[$elementKey] = $creditinvoice->Elements[$key][$elementKey] * 100;
                                } else {
                                    $line_data[$elementKey] = is_string($creditinvoice->Elements[$key][$elementKey]) ? htmlspecialchars_decode($creditinvoice->Elements[$key][$elementKey]) : $creditinvoice->Elements[$key][$elementKey];
                                }
                            }
                        }
                        $result["InvoiceLines"][] = $line_data;
                    }
                }
            } elseif(isset($creditinvoice->{$field})) {
                $result[$field] = is_string($creditinvoice->{$field}) ? htmlspecialchars_decode($creditinvoice->{$field}) : $creditinvoice->{$field};
            } else {
                switch ($field) {
                    case "CreditorCode":
                        $result[$field] = htmlspecialchars_decode($creditor->CreditorCode);
                        break;
                }
            }
        }
        $result["Date"] = $this->_filter_date_site2api($result["Date"]);
        $result["PayDate"] = $this->_filter_date_site2api($result["PayDate"]);
        $result["AmountPaid"] = number_format(round((double) $creditinvoice->AmountPaid, 2), 2, ".", "");
        $result["Private"] = number_format(round((double) $creditinvoice->Private, 2), 2, ".", "");
        require_once "class/attachment.php";
        $attachment = new attachment();
        $Attachments = $attachment->getAttachments($creditinvoice->Identifier, "creditinvoice", true);
        if(is_array($Attachments)) {
            foreach ($Attachments as $attachment) {
                $result["Attachments"][] = ["Identifier" => $attachment->id, "Filename" => $attachment->Filename];
            }
        }
        global $array_creditinvoicestatus;
        $result["Translations"] = ["Status" => isset($array_creditinvoicestatus[$creditinvoice->Status]) ? $array_creditinvoicestatus[$creditinvoice->Status] : ""];
        $result["Created"] = $this->_filter_date_db2api($creditinvoice->Created, false);
        $result["Modified"] = $this->_filter_date_db2api($creditinvoice->Modified, false);
        return HostFact_API::parseResponse($result);
    }
    protected function getFilterValues()
    {
        $filters = parent::getFilterValues();
        if(isset($filters["status"]) && $filters["status"]) {
            global $array_creditinvoicestatus;
            $tmp_status = explode("|", $filters["status"]);
            foreach ($tmp_status as $tmp_status_key) {
                if(!array_key_exists($tmp_status_key, $array_creditinvoicestatus)) {
                    HostFact_API::parseError("Invalid filter 'status'. The status '" . $tmp_status_key . "' does not exist", true);
                }
            }
        }
        return $filters;
    }
}

?>