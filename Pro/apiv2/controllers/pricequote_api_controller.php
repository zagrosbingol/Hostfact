<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class pricequote_api_controller extends api_controller
{
    public function __construct()
    {
        parent::__construct();
        HostFact_API::setObjectNames("pricequotes", "pricequote");
        require_once "class/pricequote.php";
        $this->addParameter("Identifier", "int");
        $this->addParameter("PriceQuoteCode", "string");
        $this->addParameter("Debtor", "int");
        $this->addParameter("DebtorCode", "string");
        $this->addParameter("Status", "int");
        $this->addParameter("Date", "date");
        $this->addParameter("Term", "int");
        $this->addParameter("ExpirationDate", "readonly");
        $this->addParameter("AmountExcl", "readonly");
        $this->addParameter("AmountTax", "readonly");
        $this->addParameter("AmountIncl", "readonly");
        if(defined("INT_SUPPORT_TAX_OVER_TOTAL") && INT_SUPPORT_TAX_OVER_TOTAL === true) {
            $this->addParameter("TaxRate", "float");
            $this->addParameter("Compound", "string");
        }
        $this->addParameter("Discount", "float");
        $this->addParameter("VatCalcMethod", "string");
        $this->addParameter("IgnoreDiscount", "string");
        $this->addParameter("Coupon", "string");
        $this->addParameter("ReferenceNumber", "string");
        $this->addParameter("CompanyName", "string");
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
        $this->addParameter("PriceQuoteMethod", "int");
        $this->addParameter("Template", "int");
        $this->addParameter("SentDate", "date");
        $this->addParameter("Sent", "int");
        $this->addParameter("Description", "text");
        $this->addParameter("Comment", "text");
        $this->addParameter("PriceQuoteLines", "array");
        $this->addSubParameter("PriceQuoteLines", "Identifier", "int");
        $this->addSubParameter("PriceQuoteLines", "Date", "date");
        $this->addSubParameter("PriceQuoteLines", "Number", "float");
        $this->addSubParameter("PriceQuoteLines", "NumberSuffix", "string");
        $this->addSubParameter("PriceQuoteLines", "ProductCode", "string");
        $this->addSubParameter("PriceQuoteLines", "Description", "text");
        $this->addSubParameter("PriceQuoteLines", "PriceExcl", "double");
        $this->addSubParameter("PriceQuoteLines", "DiscountPercentage", "float");
        $this->addSubParameter("PriceQuoteLines", "DiscountPercentageType", "string");
        $this->addSubParameter("PriceQuoteLines", "TaxPercentage", "float");
        $this->addSubParameter("PriceQuoteLines", "PeriodicType", "string");
        $this->addSubParameter("PriceQuoteLines", "Periods", "int");
        $this->addSubParameter("PriceQuoteLines", "Periodic", "string");
        $this->addSubParameter("PriceQuoteLines", "StartPeriod", "date");
        $this->addSubParameter("PriceQuoteLines", "EndPeriod", "readonly");
        $this->addParameter("Created", "readonly");
        $this->addParameter("Modified", "readonly");
        $this->addFilter("created", "filter_datetime");
        $this->addFilter("modified", "filter_datetime");
        $this->addFilter("date", "filter_date");
        $this->addFilter("expirationdate", "filter_date");
        $this->addFilter("status", "string", "");
        $this->addFilter("order", "string", "DESC");
        $this->object = new pricequote();
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
        $fields = ["PriceQuoteCode", "Debtor", "DebtorCode", "CompanyName", "Initials", "SurName", "AmountExcl", "AmountIncl", "Date", "ExpirationDate", "Status", "Modified"];
        $sort = $filters["sort"] ? $filters["sort"] : "Date` DESC, `PriceQuoteCode";
        $page_offset = $filters["offset"] / $filters["limit"] + 1;
        $searchat = $filters["searchat"] ? $filters["searchat"] : "PriceQuoteCode|CompanyName|SurName";
        $limit = $filters["limit"];
        if(defined("API_CALL_FROM_CLIENTAREA") && API_CALL_FROM_CLIENTAREA === true && (!isset($filters["status"]) || $filters["status"] == "" || strpos($filters["status"], "0") !== false || strpos($filters["status"], "1") !== false)) {
            HostFact_API::parseError("Unauthorized request", true);
        }
        $field_filters = array_diff_key($filters, array_flip(["offset", "limit", "sort", "order", "searchat", "searchfor"]));
        $pricequote_list = $this->object->all($fields, $sort, $filters["order"], $page_offset, $searchat, $filters["searchfor"], $field_filters, $limit);
        $array = [];
        foreach ($pricequote_list as $key => $value) {
            if($key != "CountRows" && is_numeric($key)) {
                $array[] = ["Identifier" => $value["id"], "PriceQuoteCode" => htmlspecialchars_decode($value["PriceQuoteCode"]), "Debtor" => $value["Debtor"], "DebtorCode" => htmlspecialchars_decode($value["DebtorCode"]), "CompanyName" => htmlspecialchars_decode($value["CompanyName"]), "Initials" => htmlspecialchars_decode($value["Initials"]), "SurName" => htmlspecialchars_decode($value["SurName"]), "AmountExcl" => $value["AmountExcl"], "AmountIncl" => $value["AmountIncl"], "Date" => $this->_filter_date_db2api($value["Date"]), "ExpirationDate" => $this->_filter_date_db2api($value["ExpirationDate"]), "Status" => $value["Status"], "Modified" => $this->_filter_date_db2api($value["Modified"], false)];
            }
        }
        HostFact_API::setMetaData("totalresults", $pricequote_list["CountRows"]);
        HostFact_API::setMetaData("currentresults", count($array));
        HostFact_API::setMetaData("offset", $filters["offset"]);
        if(!empty($this->_valid_filter_input)) {
            HostFact_API::setMetaData("filters", $this->_valid_filter_input);
        }
        HostFact_API::parseResponse($array, true);
    }
    public function show_api_action()
    {
        $pricequote_id = $this->_get_pricequote_id();
        $this->_show_pricequote($pricequote_id);
    }
    public function showbyhash_api_action()
    {
        $pricequote_id = $this->_get_pricequote_id();
        $pricequote = $this->object;
        $pricequote->Identifier = $pricequote_id;
        $remote_ip = HostFact_API::getRequestParameter("IPAddress");
        if(!$pricequote->show() || HostFact_API::getRequestParameter("Hash") != md5($pricequote->Identifier . $pricequote->PriceQuoteCode . $pricequote->Debtor . $pricequote->AmountIncl . $pricequote->Created)) {
            logFailedLoginAttempt("clientarea", $remote_ip, "");
            HostFact_API::parseError(__("invalid identifier for pricequote"), true);
        } elseif($pricequote->Status == 8) {
            HostFact_API::parseError(__("invalid identifier for pricequote"), true);
        }
        clearFailedLoginAttempts($remote_ip, false, "clientarea");
        $this->_show_pricequote($pricequote_id);
    }
    public function sendbyemail_api_action()
    {
        $pricequote = $this->object;
        $pricequote->Identifier = $this->_get_pricequote_id();
        if($pricequote->show()) {
            HostFact_API::beginTransaction();
            $pricequote->PriceQuoteMethod = 0;
            if($pricequote->sent(true, false) && count($pricequote->Error) === 0 && count($pricequote->Warning) === 0) {
                HostFact_API::parseSuccess($pricequote->Success);
                HostFact_API::commit();
                $this->_show_pricequote($pricequote->Identifier);
            } else {
                $pricequote->Error = array_merge($pricequote->Error, $pricequote->Warning);
                HostFact_API::parseError($pricequote->Error, true);
            }
        } else {
            HostFact_API::parseError($pricequote->Error, true);
        }
    }
    public function add_api_action()
    {
        HostFact_API::beginTransaction();
        $parse_array = $this->getValidParameters();
        $pricequote = $this->object;
        $aAddPriceQuoteLines = [];
        foreach ($parse_array as $key => $value) {
            if(in_array($key, $pricequote->Variables)) {
                $pricequote->{$key} = $value;
            }
        }
        $this->_checkPriceQuoteDebtorData($parse_array);
        if($pricequote->PriceQuoteCode == "") {
            $pricequote->PriceQuoteCode = $pricequote->newPriceQuoteCode();
        } elseif(!$pricequote->is_free($pricequote->PriceQuoteCode)) {
            HostFact_API::parseError([__("invalid pricequotecode")], true);
        }
        $aAddPriceQuoteLines = $this->_updatePriceQuoteLines($parse_array);
        if(!empty($this->object->Error)) {
            HostFact_API::parseError($this->object->Error, true);
        }
        if(isset($aAddPriceQuoteLines) && 0 < count($aAddPriceQuoteLines)) {
            $pricequote->IgnoreDiscount = in_array($pricequote->IgnoreDiscount, ["yes", "no"]) ? $pricequote->IgnoreDiscount == "yes" ? 1 : 0 : $pricequote->IgnoreDiscount;
            $pricequote->Date = isset($parse_array["Date"]) ? rewrite_date_db2site($parse_array["Date"]) : $pricequote->Date;
            $pricequote->SentDate = isset($parse_array["SentDate"]) ? rewrite_date_db2site($parse_array["SentDate"]) : $pricequote->SentDate;
            $pricequote->TaxRate = $this->_check_total_taxpercentages(isset($parse_array["TaxRate"]) ? floatval($parse_array["TaxRate"]) / 100 : $pricequote->TaxRate);
            if(isset($this->_object_parameters["CustomFields"])) {
                $pricequote->getDefaultCustomValuesWithDebtorSync();
                if(isset($parse_array["CustomFields"])) {
                    foreach ($parse_array["CustomFields"] as $_custom_field => $_custom_value) {
                        $pricequote->customvalues[$_custom_field] = $_custom_value;
                    }
                }
            }
            if($pricequote->add()) {
                HostFact_API::commit();
                return $this->_show_pricequote($pricequote->Identifier);
            }
            HostFact_API::parseError($pricequote->Error, true);
        } else {
            HostFact_API::parseError(sprintf(__("no price quote elements"), $pricequote->PriceQuoteCode), true);
        }
    }
    protected function _deletePriceQuoteLines($parse_array = [])
    {
        $PriceQuoteLines = 0;
        if(!empty($parse_array["PriceQuoteLines"])) {
            foreach ($parse_array["PriceQuoteLines"] as $key => $elementData) {
                if(!isset($elementData["Identifier"])) {
                } else {
                    $pricequoteelement = new pricequoteelement();
                    $pricequoteelement->Identifier = $elementData["Identifier"];
                    if(!$pricequoteelement->show()) {
                        HostFact_API::parseError(sprintf(__("there is no price quote line with id x"), $elementData["Identifier"]));
                    } elseif($this->object->PriceQuoteCode != $pricequoteelement->PriceQuoteCode) {
                        HostFact_API::parseError(__("cannot remove price quote lines from another price quote"));
                    } elseif($pricequoteelement->delete()) {
                        $PriceQuoteLines++;
                    }
                }
            }
            if(count($parse_array["PriceQuoteLines"]) == $this->object->Elements["CountRows"] && !HostFact_API::hasErrors()) {
                HostFact_API::parseError(__("cannot remove all the price quote lines"));
            }
        }
        return $PriceQuoteLines;
    }
    public function edit_api_action()
    {
        $pricequote = $this->object;
        $pricequote->Identifier = $this->_get_pricequote_id();
        if(!$pricequote->show()) {
            HostFact_API::parseError($pricequote->Error, true);
        }
        $parse_array = $this->getValidParameters();
        if($debtor_id = $this->_get_debtor_id()) {
            $pricequote->changeDebtor($pricequote->Identifier, $debtor_id);
            $pricequote->show();
        }
        $old_pricequoteStatus = $pricequote->Status;
        $old_pricequoteCode = $pricequote->PriceQuoteCode;
        if(isset($parse_array["PriceQuoteCode"]) && !$pricequote->is_free($parse_array["PriceQuoteCode"])) {
            HostFact_API::parseError([sprintf(__("price quote code not available"), $parse_array["PriceQuoteCode"])], true);
        } elseif(isset($parse_array["PriceQuoteCode"]) && $pricequote->changePriceQuoteCode($pricequote->Identifier, $parse_array["PriceQuoteCode"]) === false) {
            HostFact_API::parseError($pricequote->Error, true);
        }
        foreach ($pricequote as $key => $value) {
            if(is_string($value) || is_numeric($value)) {
                $pricequote->{$key} = isset($parse_array[$key]) ? $parse_array[$key] : htmlspecialchars_decode($value);
            }
        }
        $aUpdatedPriceQuoteLines = $this->_updatePriceQuoteLines($parse_array);
        $pricequote->IgnoreDiscount = in_array($pricequote->IgnoreDiscount, ["yes", "no"]) ? $pricequote->IgnoreDiscount == "yes" ? 1 : 0 : $pricequote->IgnoreDiscount;
        $pricequote->Date = isset($parse_array["Date"]) ? rewrite_date_db2site($parse_array["Date"]) : $pricequote->Date;
        $pricequote->SentDate = isset($parse_array["SentDate"]) ? rewrite_date_db2site($parse_array["SentDate"]) : $pricequote->SentDate;
        $pricequote->TaxRate = $this->_check_total_taxpercentages(isset($parse_array["TaxRate"]) ? floatval($parse_array["TaxRate"]) / 100 : $pricequote->TaxRate);
        if(isset($parse_array["CustomFields"])) {
            foreach ($parse_array["CustomFields"] as $_custom_field => $_custom_value) {
                $pricequote->customvalues[$_custom_field] = $_custom_value;
            }
        }
        if(empty($pricequote->Error) && $pricequote->edit()) {
            return $this->_show_pricequote($pricequote->Identifier);
        }
        HostFact_API::parseError($pricequote->Error, true);
    }
    protected function _updatePriceQuoteLines($parse_array)
    {
        $this->object->Debtor = 0 < $this->object->Debtor ? $this->object->Debtor : $this->_get_debtor_id();
        $linesUpdated = [];
        $linesAdded = 0;
        if(!empty($parse_array["PriceQuoteLines"])) {
            $addPriceQuoteLines = false;
            foreach ($parse_array["PriceQuoteLines"] as $key => $elementData) {
                $check_price_period = !isset($elementData["PriceExcl"]) ? true : false;
                $pricequoteelement = new pricequoteelement();
                $pricequoteelement->VatCalcMethod = $this->object->VatCalcMethod;
                if(!isset($elementData["Identifier"])) {
                    $add_or_edit = "add";
                    if(isset($elementData["ProductCode"]) && 0 < strlen($elementData["ProductCode"])) {
                        $elementData = $this->_checkProductCode($elementData);
                        if($elementData === false) {
                        }
                    }
                } else {
                    $add_or_edit = "edit";
                    $pricequoteelement->Identifier = $elementData["Identifier"];
                    if(!$pricequoteelement->show()) {
                        HostFact_API::parseError($pricequoteelement->Error);
                    } else {
                        if(isset($elementData["ProductCode"]) && 0 < strlen($elementData["ProductCode"])) {
                            $elementData = $this->_checkProductCode($elementData);
                            if($elementData === false) {
                            }
                        } else {
                            $elementData["PeriodicType"] = isset($elementData["PeriodicType"]) ? $elementData["PeriodicType"] : ($pricequoteelement->Periodic != "" ? "period" : "once");
                        }
                        if($pricequoteelement->PriceQuoteCode != $this->object->PriceQuoteCode || $pricequoteelement->Debtor != $this->object->Debtor) {
                            HostFact_API::parseError(sprintf(__("cannot edit price quote element"), $pricequoteelement->Identifier, $pricequoteelement->PriceQuoteCode));
                        }
                    }
                }
                $pricequoteelement->PriceQuoteCode = $this->object->PriceQuoteCode;
                $pricequoteelement->Debtor = $this->object->Debtor;
                $pricequoteelement->Date = isset($elementData["Date"]) && strlen($elementData["Date"]) ? rewrite_date_db2site($elementData["Date"]) : $pricequoteelement->Date;
                $pricequoteelement->Number = isset($elementData["Number"]) ? $elementData["Number"] : ($pricequoteelement->Number ? $pricequoteelement->Number : 1);
                $pricequoteelement->NumberSuffix = isset($elementData["NumberSuffix"]) ? $elementData["NumberSuffix"] : htmlspecialchars_decode($pricequoteelement->NumberSuffix);
                $pricequoteelement->ProductCode = isset($elementData["ProductCode"]) ? $elementData["ProductCode"] : htmlspecialchars_decode($pricequoteelement->ProductCode);
                $pricequoteelement->Description = isset($elementData["Description"]) ? $elementData["Description"] : htmlspecialchars_decode($pricequoteelement->Description);
                $pricequoteelement->PriceExcl = isset($elementData["PriceExcl"]) ? $elementData["PriceExcl"] : $pricequoteelement->PriceExcl;
                $pricequoteelement->DiscountPercentage = isset($elementData["DiscountPercentage"]) ? $elementData["DiscountPercentage"] : floatval($pricequoteelement->DiscountPercentage) * 100;
                $pricequoteelement->DiscountPercentageType = isset($elementData["DiscountPercentageType"]) ? $elementData["DiscountPercentageType"] : $pricequoteelement->DiscountPercentageType;
                $pricequoteelement->TaxPercentage = $this->_check_taxpercentage(isset($elementData["TaxPercentage"]) ? floatval($elementData["TaxPercentage"]) / 100 : $pricequoteelement->TaxPercentage);
                if(isset($elementData["PeriodicType"]) && strtolower($elementData["PeriodicType"]) == "period") {
                    $pricequoteelement->Periods = isset($elementData["Periods"]) ? $elementData["Periods"] : $pricequoteelement->Periods;
                    $pricequoteelement->Periodic = isset($elementData["Periodic"]) ? $elementData["Periodic"] : $pricequoteelement->Periodic;
                    $pricequoteelement->StartPeriod = isset($elementData["StartPeriod"]) && $elementData["StartPeriod"] ? rewrite_date_db2site($elementData["StartPeriod"]) : (0 < strlen($pricequoteelement->StartPeriod) && $pricequoteelement->StartPeriod != "0000-00-00" ? rewrite_date_db2site($pricequoteelement->StartPeriod) : rewrite_date_db2site(date("Y-m-d")));
                    $pricequoteelement->EndPeriod = "";
                    if($check_price_period === true) {
                        $pricequoteelement->PriceExcl = $this->_checkPricePeriod($pricequoteelement->ProductCode, $pricequoteelement->Periods, $pricequoteelement->Periodic, $pricequoteelement->PriceExcl);
                    }
                } else {
                    $pricequoteelement->Periods = "1";
                    $pricequoteelement->Periodic = "";
                    $pricequoteelement->StartPeriod = "";
                    $pricequoteelement->EndPeriod = "";
                }
                if($add_or_edit == "edit" && !($result_elements = $pricequoteelement->edit())) {
                    HostFact_API::parseError($pricequoteelement->Error);
                } else {
                    if($add_or_edit == "add") {
                        $pricequoteelement->Ordering = $this->object->Elements["CountRows"] + $linesAdded;
                        if(!($result_elements = $pricequoteelement->add())) {
                            $this->object->Error = array_merge($this->object->Error, $pricequoteelement->Error);
                        } else {
                            $linesAdded++;
                        }
                    }
                    $linesUpdated[] = $pricequoteelement->Identifier;
                }
            }
        }
        return $linesUpdated;
    }
    public function delete_api_action()
    {
        $pricequote = $this->object;
        $pricequote->Identifier = $this->_get_pricequote_id();
        if($pricequote->show()) {
            HostFact_API::beginTransaction();
            $deleteType = HostFact_API::getRequestParameter("DeleteType") == "remove" ? "remove" : "hide";
            if($pricequote->delete($pricequote->Identifier, $deleteType)) {
                HostFact_API::commit();
                HostFact_API::parseSuccess($pricequote->Success, true);
            } else {
                HostFact_API::parseError($pricequote->Error, true);
            }
        } else {
            HostFact_API::parseError($pricequote->Error, true);
        }
    }
    public function download_api_action()
    {
        $pricequote = $this->object;
        $pricequote->Identifier = $this->_get_pricequote_id();
        if($pricequote->show()) {
            require_once "class/debtor.php";
            $debtor = new debtor();
            $debtor->Identifier = $pricequote->Debtor;
            $debtor->show();
            if(HostFact_API::getRequestParameter("ShowAcceptedDetails") == "yes" && $pricequote->AcceptPDF) {
                require_once "class/attachment.php";
                $attachment_model = new attachment();
                $accepted_pricequote_info = $attachment_model->getAttachmentInfo($pricequote->AcceptPDF);
                $filename = $attachment_model->fileDir($pricequote->Identifier, "pricequote_accepted") . $accepted_pricequote_info->FilenameServer;
                if(@is_file($filename)) {
                    $result = [];
                    $result["Filename"] = $accepted_pricequote_info->Filename;
                    $result["Base64"] = base64_encode(file_get_contents($filename));
                }
            } else {
                error_reporting(0);
                $OutputType = "F";
                require_once "class/pdf.php";
                $template = $pricequote->Template;
                $pdf = new pdfCreator($template, ["pricequote" => $pricequote, "debtor" => $debtor], "pricequote", "D", true);
                $pdf->setOutputType("F");
                if(!$pdf->generatePDF("F")) {
                    HostFact_API::parseError($pdf->Error, true);
                }
                $handle = fopen("temp/" . $pdf->Name, "r");
                $filedata = fread($handle, filesize("temp/" . $pdf->Name));
                fclose($handle);
                @unlink("temp/" . $pdf->Name);
                if(defined("API_CALL_FROM_CLIENTAREA") && API_CALL_FROM_CLIENTAREA === true) {
                    createLog("pricequote", $pricequote->Identifier, "pricequote downloaded via clientarea");
                } else {
                    createLog("pricequote", $pricequote->Identifier, "pricequote downloaded via api");
                }
                $result = [];
                $result["Filename"] = $pdf->Name;
                $result["Base64"] = base64_encode($filedata);
            }
            return HostFact_API::parseResponse($result);
        }
        HostFact_API::parseError($pricequote->Error, true);
    }
    public function accept_api_action()
    {
        $pricequote = $this->object;
        $pricequote->Identifier = $this->_get_pricequote_id();
        if($pricequote->show()) {
            $usepricequoteasinvoiceref = HostFact_API::getRequestParameter("UsePriceQuoteCodeASInvoiceReference");
            $pricequote->UsePriceQuoteAsReferenceNumber = isset($usepricequoteasinvoiceref) && $usepricequoteasinvoiceref == "yes" ? true : false;
            if($pricequote->Status == 3) {
                if(HostFact_API::getRequestParameter("CreateInvoice") == "yes") {
                    if(!$pricequote->makeInvoice()) {
                        HostFact_API::parseError($pricequote->Error, true);
                    }
                } else {
                    HostFact_API::parseError(__("price quote already accepted"), true);
                }
            } elseif($pricequote->Status < 3) {
                $pricequote->Status = 3;
                $reason = HostFact_API::getRequestParameter("Reason") ? HostFact_API::getRequestParameter("Reason") : "";
                $ip = HostFact_API::getRequestParameter("IPAddress") ? HostFact_API::getRequestParameter("IPAddress") : "";
                $result = $pricequote->changeStatus("accept", $reason, $ip);
                if($result && HostFact_API::getRequestParameter("CreateInvoice") == "yes" && !$pricequote->makeInvoice()) {
                    HostFact_API::parseError($pricequote->Error, true);
                }
                if($result && HostFact_API::getRequestParameter("SendNotification") == "yes") {
                    $pricequote->show();
                    require_once "class/debtor.php";
                    $debtor = new debtor();
                    $debtor->Identifier = $pricequote->Debtor;
                    $debtor->show();
                    require_once "class/email.php";
                    $email = new email();
                    $email->Sender = getFirstMailAddress(CLIENTAREA_NOTIFICATION_EMAILADDRESS);
                    $email->Recipient = CLIENTAREA_NOTIFICATION_EMAILADDRESS;
                    $email->Subject = __("subject pricequote accepted");
                    $email->Message = file_get_contents("includes/language/" . LANGUAGE . "/mail.pricequote.accepted.phtml");
                    $email->add(["pricequote" => $pricequote, "debtor" => $debtor]);
                    $email->sent();
                }
            }
            if(!empty($pricequote->Success)) {
                HostFact_API::parseSuccess($pricequote->Success);
            }
            $this->_show_pricequote($pricequote->Identifier);
        } else {
            HostFact_API::parseError($pricequote->Error, true);
        }
    }
    public function accept_signature_api_action()
    {
        $pricequote = $this->object;
        $pricequote->Identifier = $this->_get_pricequote_id();
        if($pricequote->show()) {
            if($pricequote->Status < 3) {
                $pricequote->Status = 3;
                $pricequote->AcceptName = HostFact_API::getRequestParameter("AcceptName") ? HostFact_API::getRequestParameter("AcceptName") : "";
                $pricequote->AcceptEmailAddress = HostFact_API::getRequestParameter("AcceptEmailAddress") ? HostFact_API::getRequestParameter("AcceptEmailAddress") : "";
                $pricequote->AcceptComment = HostFact_API::getRequestParameter("AcceptComment") ? HostFact_API::getRequestParameter("AcceptComment") : "";
                $pricequote->AcceptSignatureBase64 = HostFact_API::getRequestParameter("AcceptSignatureBase64") ? HostFact_API::getRequestParameter("AcceptSignatureBase64") : "";
                $pricequote->AcceptDate = date("Y-m-d H:i:s");
                $pricequote->AcceptIPAddress = HostFact_API::getRequestParameter("AcceptIPAddress") ? HostFact_API::getRequestParameter("AcceptIPAddress") : "";
                $pricequote->AcceptUserAgent = HostFact_API::getRequestParameter("AcceptUserAgent") ? HostFact_API::getRequestParameter("AcceptUserAgent") : "";
                Database_Model::getInstance()->beginTransaction();
                if($result = $pricequote->acceptedWithSignature()) {
                    Database_Model::getInstance()->commit();
                } else {
                    Database_Model::getInstance()->rollBack();
                }
                if($result && HostFact_API::getRequestParameter("SendNotification") == "yes") {
                    $pricequote->show();
                    require_once "class/debtor.php";
                    $debtor = new debtor();
                    $debtor->Identifier = $pricequote->Debtor;
                    $debtor->show();
                    require_once "class/email.php";
                    $email = new email();
                    $email->Sender = getFirstMailAddress(CLIENTAREA_NOTIFICATION_EMAILADDRESS);
                    $email->Recipient = CLIENTAREA_NOTIFICATION_EMAILADDRESS;
                    $email->Subject = __("subject pricequote accepted");
                    $email->Message = file_get_contents("includes/language/" . LANGUAGE . "/mail.pricequote.accepted.phtml");
                    $pricequote->Reason = $pricequote->AcceptComment;
                    $email->add(["pricequote" => $pricequote, "debtor" => $debtor]);
                    $email->sent();
                }
            }
            if(!empty($pricequote->Success)) {
                HostFact_API::parseSuccess($pricequote->Success);
            }
            $this->_show_pricequote($pricequote->Identifier);
        } else {
            HostFact_API::parseError($pricequote->Error, true);
        }
    }
    public function decline_api_action()
    {
        $pricequote = $this->object;
        $pricequote->Identifier = $this->_get_pricequote_id();
        if($pricequote->show()) {
            if($pricequote->Status != 4) {
                $pricequote->Status = 8;
                $reason = HostFact_API::getRequestParameter("Reason") ? HostFact_API::getRequestParameter("Reason") : "";
                $ip = HostFact_API::getRequestParameter("IPAddress") ? HostFact_API::getRequestParameter("IPAddress") : "";
                if($pricequote->changeStatus("decline", $reason, $ip)) {
                    if(HostFact_API::getRequestParameter("SendNotification") == "yes") {
                        $pricequote->show();
                        require_once "class/debtor.php";
                        $debtor = new debtor();
                        $debtor->Identifier = $pricequote->Debtor;
                        $debtor->show();
                        require_once "class/email.php";
                        $email = new email();
                        $email->Sender = getFirstMailAddress(CLIENTAREA_NOTIFICATION_EMAILADDRESS);
                        $email->Recipient = CLIENTAREA_NOTIFICATION_EMAILADDRESS;
                        $email->Subject = __("subject pricequote declined");
                        $email->Message = file_get_contents("includes/language/" . LANGUAGE . "/mail.pricequote.declined.phtml");
                        $email->add(["pricequote" => $pricequote, "debtor" => $debtor]);
                        $email->sent();
                    }
                    HostFact_API::parseSuccess($pricequote->Success);
                    return $this->_show_pricequote($pricequote->Identifier);
                }
            } else {
                HostFact_API::parseError(sprintf(__("invoice already created"), $pricequote->PriceQuoteCode), true);
            }
        } else {
            HostFact_API::parseError($pricequote->Error, true);
        }
    }
    private function _checkPriceQuoteDebtorData($param = [])
    {
        require_once "class/debtor.php";
        $debtor = new debtor();
        $debtor->Identifier = $this->_get_debtor_id();
        if(!$debtor->show()) {
            HostFact_API::parseError($debtor->Error, true);
        }
        $this->object->Debtor = 0 < $this->object->Debtor ? $this->object->Debtor : $this->_get_debtor_id();
        $InvoiceDataForPriceQuote = $debtor->InvoiceDataForPriceQuote == "yes" ? true : false;
        $this->object->PriceQuoteMethod = isset($param["PriceQuoteMethod"]) ? $param["PriceQuoteMethod"] : $debtor->InvoiceMethod;
        $this->object->CompanyName = isset($param["CompanyName"]) ? $param["CompanyName"] : ($InvoiceDataForPriceQuote ? htmlspecialchars_decode($debtor->InvoiceCompanyName) : htmlspecialchars_decode($debtor->CompanyName));
        $this->object->Sex = isset($param["Sex"]) ? $param["Sex"] : ($InvoiceDataForPriceQuote && $debtor->InvoiceSex && $debtor->InvoiceAddress ? htmlspecialchars_decode($debtor->InvoiceSex) : htmlspecialchars_decode($debtor->Sex));
        $this->object->Initials = isset($param["Initials"]) ? $param["Initials"] : ($InvoiceDataForPriceQuote ? htmlspecialchars_decode($debtor->InvoiceInitials) : htmlspecialchars_decode($debtor->Initials));
        $this->object->SurName = isset($param["SurName"]) ? $param["SurName"] : ($InvoiceDataForPriceQuote ? htmlspecialchars_decode($debtor->InvoiceSurName) : htmlspecialchars_decode($debtor->SurName));
        $this->object->Address = isset($param["Address"]) ? $param["Address"] : ($InvoiceDataForPriceQuote ? htmlspecialchars_decode($debtor->InvoiceAddress) : htmlspecialchars_decode($debtor->Address));
        if(defined("IS_INTERNATIONAL") && IS_INTERNATIONAL === true) {
            $this->object->Address2 = isset($param["Address2"]) ? $param["Address2"] : ($InvoiceDataForPriceQuote ? htmlspecialchars_decode($debtor->InvoiceAddress2) : htmlspecialchars_decode($debtor->Address2));
        }
        $this->object->ZipCode = isset($param["ZipCode"]) ? $param["ZipCode"] : ($InvoiceDataForPriceQuote ? htmlspecialchars_decode($debtor->InvoiceZipCode) : htmlspecialchars_decode($debtor->ZipCode));
        $this->object->City = isset($param["City"]) ? $param["City"] : ($InvoiceDataForPriceQuote ? htmlspecialchars_decode($debtor->InvoiceCity) : htmlspecialchars_decode($debtor->City));
        if(defined("IS_INTERNATIONAL") && IS_INTERNATIONAL === true) {
            $this->object->State = isset($param["State"]) ? $param["State"] : ($InvoiceDataForPriceQuote ? htmlspecialchars_decode($debtor->InvoiceState) : htmlspecialchars_decode($debtor->State));
        }
        $this->object->Country = isset($param["Country"]) ? $param["Country"] : ($InvoiceDataForPriceQuote && $debtor->InvoiceCountry && $debtor->InvoiceAddress ? $debtor->InvoiceCountry : $debtor->Country);
        $this->object->EmailAddress = isset($param["EmailAddress"]) ? $param["EmailAddress"] : ($InvoiceDataForPriceQuote ? $debtor->InvoiceEmailAddress : $debtor->EmailAddress);
        if(!isset($param["Template"]) || $param["Template"] == "") {
            $this->object->Template = 0 < $debtor->PriceQuoteTemplate ? $debtor->PriceQuoteTemplate : $this->object->Template;
        } else {
            $this->object->Template = $param["Template"];
        }
    }
    protected function _get_pricequote_id()
    {
        $pricequote_id = HostFact_API::getRequestParameter("Identifier");
        if(0 < $pricequote_id) {
            if(defined("API_CALL_FROM_CLIENTAREA") && API_CALL_FROM_CLIENTAREA === true && !empty(debtor_id::$ClientArea)) {
                $pricequote_id = $this->object->getID("clientarea", $pricequote_id, ClientArea::$debtor_id);
            } else {
                $pricequote_id = $this->object->getID("identifier", $pricequote_id);
            }
            return $pricequote_id;
        }
        if($priceQuoteCode = HostFact_API::getRequestParameter("PriceQuoteCode")) {
            $pricequote_id = $this->object->getID("pricequotecode", $priceQuoteCode);
            if(0 < $pricequote_id && defined("API_CALL_FROM_CLIENTAREA") && API_CALL_FROM_CLIENTAREA === true && !empty(debtor_id::$ClientArea)) {
                $pricequote_id = $this->object->getID("clientarea", $pricequote_id, ClientArea::$debtor_id);
            }
            return $pricequote_id;
        }
        return false;
    }
    protected function _show_pricequote($pricequote_id)
    {
        $pricequote = $this->object;
        $pricequote->Identifier = $pricequote_id;
        if(!$pricequote->show()) {
            HostFact_API::parseError($pricequote->Error, true);
        }
        require_once "class/debtor.php";
        $debtor = new debtor();
        $debtor->Identifier = $pricequote->Debtor;
        $debtor->show();
        $result = [];
        foreach ($this->_object_parameters as $field => $field_info) {
            if($field == "PriceQuoteLines" && isset($pricequote->Elements["CountRows"]) && 0 < $pricequote->Elements["CountRows"]) {
                foreach ($pricequote->Elements as $key => $value) {
                    if(is_numeric($key) && !empty($pricequote->Elements[$key])) {
                        $line_data = [];
                        $line_data["Identifier"] = $key;
                        foreach ($this->_object_parameters[$field]["children"] as $elementKey => $elementValue) {
                            if(isset($pricequote->Elements[$key][$elementKey])) {
                                if(in_array($elementKey, ["DiscountPercentage", "TaxPercentage"])) {
                                    $line_data[$elementKey] = $pricequote->Elements[$key][$elementKey] * 100;
                                } else {
                                    $line_data[$elementKey] = is_string($pricequote->Elements[$key][$elementKey]) ? htmlspecialchars_decode($pricequote->Elements[$key][$elementKey]) : $pricequote->Elements[$key][$elementKey];
                                }
                            }
                        }
                        $line_data["Date"] = $this->_filter_date_site2api($line_data["Date"]);
                        $line_data["StartPeriod"] = $this->_filter_date_site2api($line_data["StartPeriod"]);
                        $line_data["EndPeriod"] = $this->_filter_date_site2api($line_data["EndPeriod"]);
                        unset($line_data["PriceQuoteCode"]);
                        unset($line_data["Debtor"]);
                        $pc_element = new pricequoteelement();
                        $pc_element->Identifier = $line_data["Identifier"];
                        $pc_element->show();
                        $pc_element->format();
                        $line_data["NoDiscountAmountIncl"] = deformat_money($pc_element->NoDiscountAmountIncl);
                        $line_data["NoDiscountAmountExcl"] = deformat_money($pc_element->NoDiscountAmountExcl);
                        $line_data["DiscountAmountIncl"] = deformat_money($pc_element->DiscountAmountIncl);
                        $line_data["DiscountAmountExcl"] = deformat_money($pc_element->DiscountAmountExcl);
                        $result["PriceQuoteLines"][] = $line_data;
                    }
                }
            } elseif(isset($pricequote->{$field})) {
                $result[$field] = is_string($pricequote->{$field}) ? htmlspecialchars_decode($pricequote->{$field}) : $pricequote->{$field};
            } else {
                switch ($field) {
                    case "AmountTax":
                        $result[$field] = number_format(round($pricequote->AmountIncl - $pricequote->AmountExcl, 2), 2, ".", "");
                        break;
                    case "DebtorCode":
                        $result[$field] = htmlspecialchars_decode($debtor->DebtorCode);
                        break;
                }
            }
        }
        $result["Date"] = $this->_filter_date_db2api($result["Date"]);
        $result["SentDate"] = $this->_filter_date_db2api($result["SentDate"]);
        $result["AcceptURL"] = $pricequote->Status == 2 ? htmlspecialchars_decode($pricequote->AcceptURL) : "";
        $result["IgnoreDiscount"] = $pricequote->IgnoreDiscount == 1 ? "yes" : "no";
        $result["TaxRate"] = $result["TaxRate"] * 100;
        global $array_country;
        global $array_pricequotemethod;
        global $array_pricequotestatus;
        $template = new template();
        $template->Identifier = $pricequote->Template;
        $template->show();
        require_once "class/attachment.php";
        $attachment = new attachment();
        $Attachments = $attachment->getAttachments($pricequote->Identifier, "pricequote", true);
        if(is_array($Attachments)) {
            foreach ($Attachments as $attachment) {
                $result["Attachments"][] = ["Identifier" => $attachment->id, "Filename" => $attachment->Filename];
            }
        }
        if(isset($this->_object_parameters["CustomFields"])) {
            $result["CustomFields"] = $pricequote->customvalues;
        }
        if(HostFact_API::getRequestParameter("ShowAcceptedDetails") == "yes") {
            foreach (["AcceptName", "AcceptEmailAddress", "AcceptComment", "AcceptDate", "AcceptIPAddress", "AcceptUserAgent", "AcceptSignatureBase64", "AcceptPDF"] as $field) {
                $result[$field] = is_string($pricequote->{$field}) ? htmlspecialchars_decode($pricequote->{$field}) : $pricequote->{$field};
            }
        }
        $result["Translations"] = ["Status" => isset($array_pricequotestatus[$pricequote->Status]) ? $array_pricequotestatus[$pricequote->Status] : "", "State" => isset($pricequote->StateName) ? $pricequote->StateName : "", "Country" => isset($array_country[$pricequote->Country]) ? $array_country[$pricequote->Country] : "", "PriceQuoteMethod" => isset($array_pricequotemethod[$pricequote->PriceQuoteMethod]) ? $array_pricequotemethod[$pricequote->PriceQuoteMethod] : "", "Template" => $template->Name];
        if(!defined("IS_INTERNATIONAL") || IS_INTERNATIONAL !== true) {
            unset($result["Translations"]["State"]);
        }
        $pricequote->format(false);
        $result["AmountDiscount"] = deformat_money($pricequote->AmountDiscount);
        $result["AmountDiscountIncl"] = deformat_money($pricequote->AmountDiscountIncl);
        $taxrates = $pricequote->used_taxrates;
        foreach ($taxrates as $_rate => $_amounts) {
            $taxrates[$_rate]["AmountExcl"] = deformat_money($taxrates[$_rate]["AmountExcl"]);
            $taxrates[$_rate]["AmountTax"] = deformat_money($taxrates[$_rate]["AmountTax"]);
            $taxrates[$_rate]["AmountIncl"] = deformat_money($taxrates[$_rate]["AmountIncl"]);
        }
        $result["UsedTaxrates"] = $taxrates;
        $result["Created"] = $this->_filter_date_db2api($pricequote->Created, false);
        $result["Modified"] = $this->_filter_date_db2api($pricequote->Modified, false);
        return HostFact_API::parseResponse($result);
    }
    protected function getFilterValues()
    {
        $filters = parent::getFilterValues();
        if(isset($filters["searchat"]) && $filters["searchat"] == "CustomFields") {
            HostFact_API::parseError("Invalid filter 'searchat'. Please enter valid columns or remove filter", true);
        }
        if(isset($filters["status"]) && $filters["status"]) {
            global $array_pricequotestatus;
            $tmp_status = explode("|", $filters["status"]);
            foreach ($tmp_status as $tmp_status_key) {
                if(!array_key_exists($tmp_status_key, $array_pricequotestatus)) {
                    HostFact_API::parseError("Invalid filter 'status'. The status '" . $tmp_status_key . "' does not exist", true);
                }
            }
        }
        return $filters;
    }
}

?>