<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class order_api_controller extends api_controller
{
    public function __construct()
    {
        parent::__construct();
        HostFact_API::setObjectNames("orders", "order");
        require_once "class/neworders.php";
        $this->addParameter("Identifier", "int");
        $this->addParameter("OrderCode", "string");
        $this->addParameter("Debtor", "int");
        $this->addParameter("DebtorCode", "string");
        $this->addParameter("Date", "datetime");
        $this->addParameter("Term", "int");
        $this->addParameter("AmountExcl", "readonly");
        $this->addParameter("AmountTax", "readonly");
        $this->addParameter("AmountIncl", "readonly");
        if(defined("INT_SUPPORT_TAX_OVER_TOTAL") && INT_SUPPORT_TAX_OVER_TOTAL === true) {
            $this->addParameter("TaxRate", "float");
            $this->addParameter("Compound", "string");
        }
        $this->addParameter("Discount", "float");
        $this->addParameter("VatCalcMethod", "string");
        $this->addParameter("IgnoreDiscount", "int");
        $this->addParameter("Coupon", "string");
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
        $this->addParameter("InvoiceMethod", "int");
        $this->addParameter("Template", "int");
        $this->addParameter("Authorisation", "string");
        $this->addParameter("PaymentMethod", "string");
        $this->addParameter("Paid", "int");
        $this->addParameter("TransactionID", "string");
        $this->addParameter("IPAddress", "string");
        $this->addParameter("Comment", "text");
        $this->addParameter("Status", "int");
        $this->addParameter("OrderLines", "array");
        $this->addSubParameter("OrderLines", "Identifier", "int");
        $this->addSubParameter("OrderLines", "Date", "date");
        $this->addSubParameter("OrderLines", "Number", "float");
        $this->addSubParameter("OrderLines", "ProductCode", "string");
        $this->addSubParameter("OrderLines", "Description", "text");
        $this->addSubParameter("OrderLines", "PriceExcl", "double");
        $this->addSubParameter("OrderLines", "TaxPercentage", "float");
        $this->addSubParameter("OrderLines", "DiscountPercentage", "float");
        $this->addSubParameter("OrderLines", "DiscountPercentageType", "string");
        $this->addSubParameter("OrderLines", "PeriodicType", "string");
        $this->addSubParameter("OrderLines", "Periods", "int");
        $this->addSubParameter("OrderLines", "Periodic", "string");
        $this->addSubParameter("OrderLines", "ProductType", "string");
        $this->addSubParameter("OrderLines", "Reference", "int");
        $this->addParameter("Created", "readonly");
        $this->addParameter("Modified", "readonly");
        $this->addFilter("created", "filter_datetime");
        $this->addFilter("modified", "filter_datetime");
        $this->addFilter("date", "filter_datetime");
        $this->addFilter("status", "string", "0|1|2");
        $this->addFilter("order", "string", "DESC");
        $this->object = new neworder();
    }
    public function list_api_action()
    {
        $this->_extra_search_filter = ["Description"];
        $filters = $this->getFilterValues();
        $fields = ["OrderCode", "Debtor", "CompanyName", "Initials", "Type", "SurName", "AmountExcl", "AmountIncl", "Date", "Status", "Modified"];
        $sort = $filters["sort"] ? $filters["sort"] : "Date` DESC, `OrderCode";
        $page_offset = $filters["offset"] / $filters["limit"] + 1;
        $searchat = $filters["searchat"] ? $filters["searchat"] : "OrderCode|CompanyName|SurName";
        $limit = $filters["limit"];
        if(defined("API_CALL_FROM_CLIENTAREA") && API_CALL_FROM_CLIENTAREA === true && (!isset($filters["status"]) || $filters["status"] == "" || strpos($filters["status"], "9") !== false)) {
            HostFact_API::parseError("Unauthorized request", true);
        }
        $field_filters = array_diff_key($filters, array_flip(["offset", "limit", "sort", "order", "searchat", "searchfor"]));
        $order_list = $this->object->all($fields, $sort, $filters["order"], 1, $searchat, $filters["searchfor"], $field_filters, $limit);
        $array = [];
        foreach ($order_list as $key => $value) {
            if($key != "CountRows" && is_numeric($key)) {
                $array[] = ["Identifier" => $value["id"], "OrderCode" => htmlspecialchars_decode($value["OrderCode"]), "Debtor" => $value["Type"] == "new" ? "0" : $value["Debtor"], "CompanyName" => htmlspecialchars_decode($value["CompanyName"]), "Initials" => htmlspecialchars_decode($value["Initials"]), "SurName" => htmlspecialchars_decode($value["SurName"]), "AmountExcl" => $value["AmountExcl"], "AmountIncl" => $value["AmountIncl"], "Date" => $this->_filter_date_db2api($value["Date"], false), "Status" => $value["Status"], "Modified" => $this->_filter_date_db2api($value["Modified"], false)];
            }
        }
        HostFact_API::setMetaData("totalresults", $order_list["CountRows"]);
        HostFact_API::setMetaData("currentresults", count($array));
        HostFact_API::setMetaData("offset", $filters["offset"]);
        if(!empty($this->_valid_filter_input)) {
            HostFact_API::setMetaData("filters", $this->_valid_filter_input);
        }
        HostFact_API::parseResponse($array, true);
    }
    public function show_api_action()
    {
        $order_id = $this->_get_order_id();
        $this->_show_order($order_id);
    }
    public function add_api_action()
    {
        HostFact_API::beginTransaction();
        $parse_array = $this->getValidParameters();
        $order = $this->object;
        $aAddOrderLines = [];
        foreach ($parse_array as $key => $value) {
            if(in_array($key, $order->Variables)) {
                $order->{$key} = $value;
            }
        }
        $order->Type = "debtor";
        $this->_checkOrderDebtorData($parse_array);
        if(isset($parse_array["Paid"]) && $parse_array["Paid"] == "1" && (!isset($parse_array["TransactionID"]) || strlen($parse_array["TransactionID"]) === 0)) {
            HostFact_API::parseError([__("api transactionid required")], true);
        }
        if($order->OrderCode == "") {
            $order->OrderCode = $order->newOrderCode();
        } elseif(!$order->is_free($order->OrderCode)) {
            HostFact_API::parseError([__("ordercode genaration failed")], true);
        }
        $aAddOrderLines = $this->_updateOrderLines($parse_array);
        if(!empty($this->object->Error)) {
            HostFact_API::parseError($this->object->Error, true);
        }
        if(isset($aAddOrderLines) && 0 < count($aAddOrderLines)) {
            $order->IgnoreDiscount = in_array($order->IgnoreDiscount, ["yes", "no"]) ? $order->IgnoreDiscount == "yes" ? 1 : 0 : $order->IgnoreDiscount;
            $order->Date = isset($parse_array["Date"]) ? rewrite_date_db2site($parse_array["Date"], DATE_FORMAT . " %H:%i:%s") : $order->Date;
            $order->TaxRate = $this->_check_total_taxpercentages(isset($parse_array["TaxRate"]) ? floatval($parse_array["TaxRate"]) / 100 : $order->TaxRate);
            if($order->add()) {
                HostFact_API::commit();
                $order_info = ["id" => $order->Identifier, "Debtor" => $order->Debtor, "OrderCode" => $order->OrderCode];
                do_action("order_is_created", $order_info);
                return $this->_show_order($order->Identifier);
            }
            HostFact_API::parseError($order->Error, true);
        } else {
            HostFact_API::parseError(sprintf(__("no order elements"), $order->OrderCode), true);
        }
    }
    protected function _deleteOrderLines($parse_array = [])
    {
        $orderLines = 0;
        if(!empty($parse_array["OrderLines"])) {
            foreach ($parse_array["OrderLines"] as $key => $elementData) {
                if(!isset($elementData["Identifier"])) {
                } else {
                    $orderelement = new neworderelement();
                    $orderelement->Identifier = $elementData["Identifier"];
                    if(!$orderelement->show()) {
                        HostFact_API::parseError(sprintf(__("there is no orderline with id x"), $elementData["Identifier"]));
                    } elseif($this->object->OrderCode != $orderelement->OrderCode) {
                        HostFact_API::parseError(__("cannot remove orderlines from another order"));
                    } elseif($orderelement->delete()) {
                        $orderLines++;
                    }
                }
            }
            if(count($parse_array["OrderLines"]) == $this->object->Elements["CountRows"] && !HostFact_API::hasErrors()) {
                HostFact_API::parseError(__("cannot remove all the orderlines"));
            }
        }
        return $orderLines;
    }
    public function edit_api_action()
    {
        $order = $this->object;
        $order->Identifier = $this->_get_order_id();
        if(!$order->show()) {
            HostFact_API::parseError($order->Error, true);
        }
        HostFact_API::beginTransaction();
        $parse_array = $this->getValidParameters();
        if(isset($parse_array["Paid"]) && $parse_array["Paid"] == "1" && (!isset($parse_array["TransactionID"]) || strlen($parse_array["TransactionID"]) === 0)) {
            HostFact_API::parseError([__("api transactionid required")], true);
        }
        if($debtor_id = $this->_get_debtor_id()) {
            $order->changeDebtor($order->Identifier, $debtor_id);
            $order->show();
        }
        if(isset($parse_array["OrderCode"]) && !$order->is_free($parse_array["OrderCode"])) {
            HostFact_API::parseError([sprintf(__("ordercode not available"), $parse_array["OrderCode"])], true);
        } elseif(isset($parse_array["OrderCode"]) && $order->changeOrderCode($order->Identifier, $parse_array["OrderCode"]) === false) {
            HostFact_API::parseError($order->Error, true);
        }
        foreach ($order as $key => $value) {
            if((is_string($value) || is_numeric($value)) && !in_array($key, ["Debtor"])) {
                $order->{$key} = isset($parse_array[$key]) ? $parse_array[$key] : htmlspecialchars_decode($value);
            }
        }
        $aUpdatedOrderLines = $this->_updateOrderLines($parse_array);
        $order->IgnoreDiscount = in_array($order->IgnoreDiscount, ["yes", "no"]) ? $order->IgnoreDiscount == "yes" ? 1 : 0 : $order->IgnoreDiscount;
        $order->Date = isset($parse_array["Date"]) ? rewrite_date_db2site($parse_array["Date"], DATE_FORMAT . " %H:%i:%s") : $order->Date;
        $order->TaxRate = $this->_check_total_taxpercentages(isset($parse_array["TaxRate"]) ? floatval($parse_array["TaxRate"]) / 100 : $order->TaxRate);
        if(empty($order->Error) && $order->edit()) {
            HostFact_API::commit();
            return $this->_show_order($order->Identifier);
        }
        HostFact_API::parseError($order->Error, true);
    }
    protected function _updateOrderLines($parse_array)
    {
        $this->object->Debtor = 0 < $this->object->Debtor ? $this->object->Debtor : $this->_get_debtor_id();
        $linesUpdated = [];
        $linesAdded = 0;
        if(!empty($parse_array["OrderLines"])) {
            $addOrderlines = false;
            foreach ($parse_array["OrderLines"] as $key => $elementData) {
                $check_price_period = !isset($elementData["PriceExcl"]) ? true : false;
                $orderelement = new neworderelement();
                $orderelement->VatCalcMethod = $this->object->VatCalcMethod;
                $orderelement->Type = $this->object->Type;
                if(!isset($elementData["Identifier"])) {
                    $add_or_edit = "add";
                    if(isset($elementData["ProductCode"]) && 0 < strlen($elementData["ProductCode"])) {
                        $elementData = $this->_checkProductCode($elementData);
                        if($elementData === false) {
                        }
                    }
                } else {
                    $add_or_edit = "edit";
                    $orderelement->Identifier = $elementData["Identifier"];
                    if(!$orderelement->show()) {
                        HostFact_API::parseError($orderelement->Error);
                    } else {
                        if(isset($elementData["ProductCode"]) && 0 < strlen($elementData["ProductCode"])) {
                            $elementData = $this->_checkProductCode($elementData);
                            if($elementData === false) {
                            }
                        } else {
                            $elementData["PeriodicType"] = isset($elementData["PeriodicType"]) ? $elementData["PeriodicType"] : ($orderelement->Periodic != "" ? "period" : "once");
                        }
                        if($orderelement->OrderCode != $this->object->OrderCode || $orderelement->Debtor != $this->object->Debtor) {
                            HostFact_API::parseError(sprintf(__("cannot edit order element"), $orderelement->Identifier, $orderelement->OrderCode));
                        }
                    }
                }
                $orderelement->OrderCode = $this->object->OrderCode;
                $orderelement->Debtor = $this->object->Debtor;
                $orderelement->Date = isset($elementData["Date"]) && strlen($elementData["Date"]) ? rewrite_date_db2site($elementData["Date"]) : $orderelement->Date;
                $orderelement->Number = isset($elementData["Number"]) ? $elementData["Number"] : ($orderelement->Number ? $orderelement->Number : 1);
                $orderelement->ProductCode = isset($elementData["ProductCode"]) ? $elementData["ProductCode"] : htmlspecialchars_decode($orderelement->ProductCode);
                $orderelement->Description = isset($elementData["Description"]) ? $elementData["Description"] : htmlspecialchars_decode($orderelement->Description);
                $orderelement->PriceExcl = isset($elementData["PriceExcl"]) ? $elementData["PriceExcl"] : $orderelement->PriceExcl;
                $orderelement->DiscountPercentage = isset($elementData["DiscountPercentage"]) ? $elementData["DiscountPercentage"] : floatval($orderelement->DiscountPercentage) * 100;
                $orderelement->DiscountPercentageType = isset($elementData["DiscountPercentageType"]) ? $elementData["DiscountPercentageType"] : $orderelement->DiscountPercentageType;
                $orderelement->TaxPercentage = $this->_check_taxpercentage(isset($elementData["TaxPercentage"]) ? floatval($elementData["TaxPercentage"]) / 100 : $orderelement->TaxPercentage);
                $orderelement->ProductType = isset($elementData["ProductType"]) ? $elementData["ProductType"] : $orderelement->ProductType;
                $orderelement->Reference = $this->_checkProductTypeReference($orderelement->ProductType, isset($elementData["Reference"]) ? $elementData["Reference"] : $orderelement->Reference);
                if(isset($elementData["PeriodicType"]) && $elementData["PeriodicType"] == "period") {
                    $orderelement->Periods = isset($elementData["Periods"]) ? $elementData["Periods"] : $orderelement->Periods;
                    $orderelement->Periodic = isset($elementData["Periodic"]) ? $elementData["Periodic"] : $orderelement->Periodic;
                    $orderelement->StartPeriod = "";
                    $orderelement->EndPeriod = "";
                    if($check_price_period === true) {
                        $orderelement->PriceExcl = $this->_checkPricePeriod($orderelement->ProductCode, $orderelement->Periods, $orderelement->Periodic, $orderelement->PriceExcl);
                    }
                } else {
                    $orderelement->Periods = "1";
                    $orderelement->Periodic = "";
                    $orderelement->StartPeriod = "";
                    $orderelement->EndPeriod = "";
                }
                if($add_or_edit == "edit" && !($result_elements = $orderelement->edit())) {
                    HostFact_API::parseError($orderelement->Error);
                } else {
                    if($add_or_edit == "add") {
                        $orderelement->Ordering = $this->object->Elements["CountRows"] + $linesAdded;
                        if(!($result_elements = $orderelement->add())) {
                            $this->object->Error = array_merge($this->object->Error, $orderelement->Error);
                        } else {
                            $linesAdded++;
                        }
                    }
                    $linesUpdated[] = $orderelement->Identifier;
                }
            }
        }
        return $linesUpdated;
    }
    public function delete_api_action()
    {
        $order = $this->object;
        $order->Identifier = $this->_get_order_id();
        if($order->show()) {
            if($order->Status == 8 || $order->Status == 9) {
                HostFact_API::parseError(sprintf(__("order cant removed"), $order->OrderCode), true);
            }
            HostFact_API::beginTransaction();
            if($order->delete()) {
                HostFact_API::commit();
                HostFact_API::parseSuccess(sprintf(__("order x removed"), $order->OrderCode), true);
            } else {
                HostFact_API::parseError($order->Error, true);
            }
        } else {
            HostFact_API::parseError($order->Error, true);
        }
    }
    public function process_api_action()
    {
        $order = $this->object;
        $order->Identifier = $this->_get_order_id();
        if($order->show()) {
            HostFact_API::beginTransaction();
            $order->makeinvoice();
            if(!empty($order->Success)) {
                HostFact_API::commit();
                HostFact_API::parseSuccess($order->Success, true);
            } else {
                HostFact_API::parseError(array_merge($order->Error, $order->Warning), true);
            }
        } else {
            HostFact_API::parseError($order->Error, true);
        }
    }
    private function _checkOrderDebtorData($param = [])
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
        $this->object->Sex = isset($param["Sex"]) ? $param["Sex"] : ($debtor->InvoiceSurName ? htmlspecialchars_decode($debtor->InvoiceSex) : htmlspecialchars_decode($debtor->Sex));
        $this->object->Initials = isset($param["Initials"]) ? $param["Initials"] : ($debtor->InvoiceInitials ? htmlspecialchars_decode($debtor->InvoiceInitials) : htmlspecialchars_decode($debtor->Initials));
        $this->object->SurName = isset($param["SurName"]) ? $param["SurName"] : ($debtor->InvoiceSurName ? htmlspecialchars_decode($debtor->InvoiceSurName) : htmlspecialchars_decode($debtor->SurName));
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
        if(!isset($param["Term"]) && !is_null($debtor->InvoiceTerm)) {
            $this->object->Term = $debtor->InvoiceTerm;
        }
        $this->object->Template = !isset($param["Template"]) || $param["Template"] == "" ? $debtor->InvoiceTemplate : $param["Template"];
    }
    protected function _get_order_id()
    {
        $order_id = HostFact_API::getRequestParameter("Identifier");
        if(0 < $order_id) {
            if(defined("API_CALL_FROM_CLIENTAREA") && API_CALL_FROM_CLIENTAREA === true && !empty(debtor_id::$ClientArea)) {
                $order_id = $this->object->getID("clientarea", $order_id, ClientArea::$debtor_id);
            } else {
                $order_id = $this->object->getID("identifier", $order_id);
            }
            return $order_id;
        }
        if($OrderCode = HostFact_API::getRequestParameter("OrderCode")) {
            $order_id = $this->object->getID("ordercode", $OrderCode);
            if(0 < $order_id && defined("API_CALL_FROM_CLIENTAREA") && API_CALL_FROM_CLIENTAREA === true && !empty(debtor_id::$ClientArea)) {
                $order_id = $this->object->getID("clientarea", $order_id, ClientArea::$debtor_id);
            }
            return $order_id;
        }
        return false;
    }
    protected function _show_order($order_id)
    {
        $order = $this->object;
        $order->Identifier = $order_id;
        if(!$order->show()) {
            HostFact_API::parseError($order->Error, true);
        }
        $result = [];
        foreach ($this->_object_parameters as $field => $field_info) {
            if($field == "OrderLines" && 0 < $order->Elements["CountRows"]) {
                foreach ($order->Elements as $key => $value) {
                    if(is_numeric($key) && !empty($order->Elements[$key])) {
                        $line_data = [];
                        $line_data["Identifier"] = $key;
                        foreach ($this->_object_parameters[$field]["children"] as $elementKey => $elementValue) {
                            if(isset($order->Elements[$key][$elementKey])) {
                                if(in_array($elementKey, ["DiscountPercentage", "TaxPercentage"])) {
                                    $line_data[$elementKey] = $order->Elements[$key][$elementKey] * 100;
                                } else {
                                    $line_data[$elementKey] = is_string($order->Elements[$key][$elementKey]) ? htmlspecialchars_decode($order->Elements[$key][$elementKey]) : $order->Elements[$key][$elementKey];
                                }
                            }
                        }
                        $line_data["Date"] = $this->_filter_date_site2api($line_data["Date"]);
                        unset($line_data["OrderCode"]);
                        unset($line_data["Debtor"]);
                        $order_element = new neworderelement();
                        $order_element->Identifier = $line_data["Identifier"];
                        $order_element->show();
                        $order_element->format();
                        $line_data["NoDiscountAmountIncl"] = deformat_money($order_element->NoDiscountAmountIncl);
                        $line_data["NoDiscountAmountExcl"] = deformat_money($order_element->NoDiscountAmountExcl);
                        $line_data["DiscountAmountIncl"] = deformat_money($order_element->DiscountAmountIncl);
                        $line_data["DiscountAmountExcl"] = deformat_money($order_element->DiscountAmountExcl);
                        $result["OrderLines"][] = $line_data;
                    }
                }
            } elseif(isset($order->{$field})) {
                $result[$field] = is_string($order->{$field}) ? htmlspecialchars_decode($order->{$field}) : $order->{$field};
            } else {
                switch ($field) {
                    case "AmountTax":
                        $result[$field] = number_format(round($order->AmountIncl - $order->AmountExcl, 2), 2, ".", "");
                        break;
                }
            }
        }
        if($order->Type == "new") {
            $result["Debtor"] = 0;
        }
        $result["Date"] = $this->_filter_date_site2api($result["Date"], false);
        $result["IgnoreDiscount"] = $order->IgnoreDiscount == 1 ? "yes" : "no";
        $result["TaxRate"] = $result["TaxRate"] * 100;
        global $array_country;
        global $array_orderstatus;
        global $array_paymentmethod;
        global $array_invoicemethod;
        $templateName = "";
        if(0 < $order->Template) {
            require_once "class/template.php";
            $template = new template();
            $template->Identifier = $order->Template;
            $template->show();
            $templateName = $template->Name;
        }
        $result["Translations"] = ["State" => isset($order->StateName) ? $order->StateName : "", "Country" => isset($array_country[$order->Country]) ? $array_country[$order->Country] : "", "InvoiceMethod" => isset($array_invoicemethod[$order->InvoiceMethod]) ? $array_invoicemethod[$order->InvoiceMethod] : "", "Template" => $templateName, "PaymentMethod" => isset($array_paymentmethod[$order->PaymentMethod]) ? $array_paymentmethod[$order->PaymentMethod] : "", "Status" => isset($array_orderstatus[$order->Status]) ? $array_orderstatus[$order->Status] : ""];
        if(!defined("IS_INTERNATIONAL") || IS_INTERNATIONAL !== true) {
            unset($result["Translations"]["State"]);
            unset($result["TaxRate"]);
            unset($result["Compound"]);
        }
        $order->format(false);
        $result["AmountDiscount"] = deformat_money($order->AmountDiscount);
        $result["AmountDiscountIncl"] = deformat_money($order->AmountDiscountIncl);
        $taxrates = $order->used_taxrates;
        foreach ($taxrates as $_rate => $_amounts) {
            $taxrates[$_rate]["AmountExcl"] = deformat_money($taxrates[$_rate]["AmountExcl"]);
            $taxrates[$_rate]["AmountTax"] = deformat_money($taxrates[$_rate]["AmountTax"]);
            $taxrates[$_rate]["AmountIncl"] = deformat_money($taxrates[$_rate]["AmountIncl"]);
        }
        $result["UsedTaxrates"] = $taxrates;
        $result["Created"] = $this->_filter_date_db2api($order->Created, false);
        $result["Modified"] = $this->_filter_date_db2api($order->Modified, false);
        return HostFact_API::parseResponse($result);
    }
    protected function getFilterValues()
    {
        $filters = parent::getFilterValues();
        if(isset($filters["status"]) && $filters["status"]) {
            global $array_orderstatus;
            $tmp_status = explode("|", $filters["status"]);
            foreach ($tmp_status as $tmp_status_key) {
                if(!array_key_exists($tmp_status_key, $array_orderstatus)) {
                    HostFact_API::parseError("Invalid filter 'status'. The status '" . $tmp_status_key . "' does not exist", true);
                }
            }
        }
        return $filters;
    }
}

?>