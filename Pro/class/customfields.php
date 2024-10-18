<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class customfields
{
    public $FieldCode;
    public $LabelTitle;
    public $LabelType;
    public $LabelOptions;
    public $LabelDefault;
    public $OrderID;
    public $ShowDebtor;
    public $ShowHandle;
    public $ShowOrderform;
    public $ShowInvoice;
    public $ShowPriceQuote;
    public $Regex;
    public $Success;
    public $Warning;
    public $Error;
    public $LabelTypes;
    public function __construct()
    {
        $this->Success = $this->Warning = $this->Error = [];
        $this->Variables = ["FieldCode", "LabelTitle", "LabelType", "LabelOptions", "LabelDefault", "OrderID", "ShowDebtor", "ShowHandle", "ShowOrderform", "ShowInvoice", "ShowPriceQuote", "Regex"];
        $this->LabelTypes = ["input" => __("ccf labeltype input"), "textarea" => __("ccf labeltype textarea"), "select" => __("ccf labeltype select"), "date" => __("ccf labeltype date"), "checkbox" => __("ccf labeltype checkbox"), "radio" => __("ccf labeltype radio")];
    }
    public function show()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("ccf - invalid identifier");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_Debtor_Custom_Fields")->where("id", $this->Identifier)->execute();
        if(!$result) {
            $this->Error[] = __("ccf - invalid identifier");
            return false;
        }
        foreach ($result as $key => $value) {
            $this->{$key} = $value;
        }
        if(in_array($this->LabelType, ["select", "radio", "checkbox"])) {
            $this->LabelOptions = (array) json_decode($this->LabelOptions);
            if($this->LabelType == "checkbox") {
                $this->LabelDefault = (array) json_decode($this->LabelDefault);
            }
        }
        return true;
    }
    public function add()
    {
        if(!$this->validate()) {
            return false;
        }
        $order_id = Database_Model::getInstance()->getOne("HostFact_Debtor_Custom_Fields", ["MAX(`OrderID`) as `OrderID`"])->execute();
        $result = Database_Model::getInstance()->insert("HostFact_Debtor_Custom_Fields", ["FieldCode" => $this->FieldCode, "LabelTitle" => $this->LabelTitle, "LabelType" => $this->LabelType, "LabelOptions" => json_encode($this->LabelOptions), "LabelDefault" => $this->LabelType == "checkbox" ? json_encode($this->LabelDefault) : $this->LabelDefault, "OrderID" => isset($order_id) ? $order_id->OrderID + 1 : 1, "ShowDebtor" => $this->ShowDebtor, "ShowHandle" => $this->ShowHandle, "ShowOrderform" => $this->ShowDebtor == "yes" || $this->ShowHandle == "yes" ? $this->ShowOrderform : "no", "ShowInvoice" => $this->ShowInvoice, "ShowPriceQuote" => $this->ShowPriceQuote, "Regex" => $this->Regex])->execute();
        if($result) {
            $this->Success[] = sprintf(__("ccf - field added"), htmlspecialchars($this->LabelTitle));
            unset($_SESSION["custom_fields"]);
            return true;
        }
        return false;
    }
    public function edit()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("ccf - invalid identifier");
            return false;
        }
        if(!$this->validate()) {
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Debtor_Custom_Fields", ["FieldCode" => $this->FieldCode, "LabelTitle" => $this->LabelTitle, "LabelType" => $this->LabelType, "LabelOptions" => json_encode($this->LabelOptions), "LabelDefault" => $this->LabelType == "checkbox" ? json_encode($this->LabelDefault) : $this->LabelDefault, "ShowDebtor" => $this->ShowDebtor, "ShowHandle" => $this->ShowHandle, "ShowOrderform" => $this->ShowDebtor == "yes" || $this->ShowHandle == "yes" ? $this->ShowOrderform : "no", "ShowInvoice" => $this->ShowInvoice, "ShowPriceQuote" => $this->ShowPriceQuote, "Regex" => $this->Regex])->where("id", $this->Identifier)->execute();
        if($result) {
            $this->Success[] = sprintf(__("ccf - field edited"), htmlspecialchars($this->LabelTitle));
            unset($_SESSION["custom_fields"]);
            return true;
        }
        return false;
    }
    public function delete()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("ccf - invalid identifier");
            return false;
        }
        $result = Database_Model::getInstance()->delete("HostFact_Debtor_Custom_Values")->where("FieldID", $this->Identifier)->execute();
        $result = Database_Model::getInstance()->delete("HostFact_Debtor_Custom_Fields")->where("id", $this->Identifier)->execute();
        if($result) {
            $this->Success[] = sprintf(__("ccf - field deleted"), htmlspecialchars($this->LabelTitle));
            unset($_SESSION["custom_fields"]);
            return true;
        }
        return false;
    }
    private function validate()
    {
        if(!$this->FieldCode || preg_match("/^[a-z0-9]+\$/i", $this->FieldCode) == 0) {
            $this->Error[] = __("ccf - invalid fieldcode");
        } elseif($this->FieldCode) {
            $result = Database_Model::getInstance()->getOne("HostFact_Debtor_Custom_Fields", "id")->where("FieldCode", $this->FieldCode)->where("id", ["!=" => $this->Identifier])->execute();
            if($result !== false) {
                $this->Error[] = __("ccf - fieldcode already in use");
            }
        }
        if(!$this->LabelTitle) {
            $this->Error[] = __("ccf - invalid labeltitle");
        }
        if($this->Regex) {
            if(substr($this->Regex, 0, 1) != "/") {
                $this->Regex = "/" . $this->Regex;
            }
            if(substr($this->Regex, -1, 1) != "/") {
                $this->Regex = $this->Regex . "/";
            }
            if(@preg_match($this->Regex, "") === false) {
                $this->Error[] = __("ccf - invalid regex");
            }
        }
        return empty($this->Error) ? true : false;
    }
    public function reorder($order)
    {
        $order = explode("&", $order);
        $ordering = 1;
        foreach ($order as $item) {
            $exp_item = explode("=", $item);
            Database_Model::getInstance()->update("HostFact_Debtor_Custom_Fields", ["OrderID" => $ordering])->where("id", $exp_item[1])->execute();
            $ordering++;
        }
        unset($_SESSION["custom_fields"]);
        return true;
    }
    public function all()
    {
        $result = Database_Model::getInstance()->get("HostFact_Debtor_Custom_Fields")->orderBy("OrderID", "ASC")->asArray()->execute();
        return $result;
    }
    public function getCustomDebtorFields()
    {
        return $this->__getCustomFields("debtor");
    }
    public function getCustomDebtorFieldsValues($reference_id)
    {
        return $this->__getCustomFieldsValues("debtor", $reference_id);
    }
    public function setCustomDebtorFieldsValues($debtor_id, $values)
    {
        return $this->__setCustomFieldsValues("debtor", $debtor_id, $values);
    }
    public function getCustomNewCustomerFields()
    {
        return $this->__getCustomFields("newcustomer");
    }
    public function getCustomNewCustomerFieldsValues($reference_id)
    {
        return $this->__getCustomFieldsValues("newcustomer", $reference_id);
    }
    public function setCustomNewCustomerFieldsValues($customer_id, $values)
    {
        return $this->__setCustomFieldsValues("newcustomer", $customer_id, $values);
    }
    public function getCustomHandleFields()
    {
        return $this->__getCustomFields("handle");
    }
    public function getCustomHandleFieldsValues($reference_id)
    {
        return $this->__getCustomFieldsValues("handle", $reference_id);
    }
    public function setCustomHandleFieldsValues($handle_id, $values)
    {
        return $this->__setCustomFieldsValues("handle", $handle_id, $values);
    }
    public function getCustomInvoiceFields()
    {
        return $this->__getCustomFields("invoice");
    }
    public function getCustomInvoiceFieldsValues($reference_id)
    {
        return $this->__getCustomFieldsValues("invoice", $reference_id);
    }
    public function setCustomInvoiceFieldsValues($reference_id, $values)
    {
        return $this->__setCustomFieldsValues("invoice", $reference_id, $values);
    }
    public function getCustomPriceQuoteFields()
    {
        return $this->__getCustomFields("pricequote");
    }
    public function getCustomPriceQuoteFieldsValues($reference_id)
    {
        return $this->__getCustomFieldsValues("pricequote", $reference_id);
    }
    public function setCustomPriceQuoteFieldsValues($reference_id, $values)
    {
        return $this->__setCustomFieldsValues("pricequote", $reference_id, $values);
    }
    public function syncCustomFields($from_type, $from_id, $to_type, $to_id_array)
    {
        if(!($from = $this->__getCustomFieldsValues($from_type, $from_id))) {
            return NULL;
        }
        $to_custom_fields = $this->__getCustomFields($to_type);
        $to_fieldname_to_id = [];
        foreach ($to_custom_fields as $to_custom_field) {
            $to_fieldname_to_id[$to_custom_field["FieldCode"]] = $to_custom_field["id"];
        }
        foreach ($to_id_array as $to_id) {
            foreach ($from as $from_field => $from_value) {
                if(isset($to_fieldname_to_id[$from_field])) {
                    $this->__setCustomFieldsValue($to_type, $to_id, $to_fieldname_to_id[$from_field], $from_value["Value"]);
                }
            }
        }
    }
    public function preFillCustomFields($from_type, $from_id, $to_type)
    {
        $from = $this->__getCustomFieldsValues($from_type, $from_id);
        $to_custom_fields = $this->__getCustomFields($to_type);
        $field_values = [];
        foreach ($to_custom_fields as $to_custom_field) {
            if(isset($from[$to_custom_field["FieldCode"]])) {
                $field_values[$to_custom_field["FieldCode"]] = $from[$to_custom_field["FieldCode"]]["Value"];
            }
        }
        return $field_values;
    }
    public function searchCustomFieldsByValue($search_value, $reference_type)
    {
        Database_Model::getInstance()->get("HostFact_Debtor_Custom_Fields", ["id", "LabelOptions", "LabelType"]);
        switch ($reference_type) {
            case "debtor":
                Database_Model::getInstance()->where("ShowDebtor", "yes");
                break;
            case "invoice":
                Database_Model::getInstance()->where("ShowInvoice", "yes");
                break;
            case "pricequote":
                Database_Model::getInstance()->where("ShowPriceQuote", "yes");
                break;
            default:
                $fields = Database_Model::getInstance()->where("LabelType", ["IN" => ["select", "checkbox", "radio"]])->where("LabelOptions", ["LIKE" => "%" . $search_value . "%"])->execute();
                $keys = ["select" => [], "checkbox" => [], "radio" => []];
                if(!empty($fields)) {
                    foreach ($fields as $_field) {
                        $_options = json_decode($_field->LabelOptions, true);
                        if(!empty($_options)) {
                            foreach ($_options as $label_key => $_label_value) {
                                if(stripos($_label_value, $search_value) !== false) {
                                    $keys[$_field->LabelType][] = strpos($label_key, "opt-") !== false ? substr($label_key, 4) : $label_key;
                                }
                            }
                        }
                    }
                }
                $or_clausule = [];
                $or_clausule[] = ["AND" => [["HostFact_Debtor_Custom_Values.`Value`", ["LIKE" => "%" . $search_value . "%"]], ["HostFact_Debtor_Custom_Fields.`LabelType`", ["IN" => ["input", "textarea"]]]]];
                if(!empty($keys["radio"])) {
                    $or_clausule[] = ["AND" => [["HostFact_Debtor_Custom_Values.`Value`", ["IN" => $keys["radio"]]], ["HostFact_Debtor_Custom_Fields.`LabelType`", "radio"]]];
                }
                if(!empty($keys["select"])) {
                    $or_clausule[] = ["AND" => [["HostFact_Debtor_Custom_Values.`Value`", ["IN" => $keys["select"]]], ["HostFact_Debtor_Custom_Fields.`LabelType`", "select"]]];
                }
                if(!empty($keys["checkbox"])) {
                    foreach ($keys["checkbox"] as $_key) {
                        $or_clausule[] = ["AND" => [["HostFact_Debtor_Custom_Values.`Value`", ["LIKE" => "%\"" . $_key . "\"%"]], ["HostFact_Debtor_Custom_Fields.`LabelType`", "checkbox"]]];
                    }
                }
                $items = Database_Model::getInstance()->getOne("HostFact_Debtor_Custom_Values", ["GROUP_CONCAT(DISTINCT `ReferenceID`) AS item_ids"])->join("HostFact_Debtor_Custom_Fields", "HostFact_Debtor_Custom_Fields.`id` = HostFact_Debtor_Custom_Values.`FieldID`")->where("HostFact_Debtor_Custom_Values.`ReferenceType`", $reference_type)->orWhere($or_clausule)->asArray()->execute();
                $item_ids = "";
                if($items && isset($items["item_ids"]) && $items["item_ids"]) {
                    $item_ids = $items["item_ids"];
                }
                if($item_ids) {
                    $item_ids = array_unique(explode(",", $item_ids));
                }
                if(!empty($items)) {
                    return $item_ids;
                }
                return false;
        }
    }
    private function __getCustomFields($reference_type)
    {
        $custom_fields = [];
        if(isset($_SESSION["custom_fields"][$reference_type]) && $_SESSION["custom_fields"][$reference_type]) {
            $custom_fields = $_SESSION["custom_fields"][$reference_type];
        } else {
            switch ($reference_type) {
                case "handle":
                    $show_columm = "ShowHandle";
                    break;
                case "invoice":
                    $show_columm = "ShowInvoice";
                    break;
                case "pricequote":
                    $show_columm = "ShowPriceQuote";
                    break;
                default:
                    $show_columm = "ShowDebtor";
                    Database_Model::getInstance()->get("HostFact_Debtor_Custom_Fields")->where($show_columm, "yes");
                    if($reference_type == "newcustomer") {
                        Database_Model::getInstance()->where("ShowOrderform", "yes");
                    }
                    $result = Database_Model::getInstance()->orderBy("OrderID", "ASC")->asArray()->execute();
                    if($result === false) {
                        $_SESSION["custom_fields"][$reference_type] = false;
                        return $custom_fields;
                    }
                    foreach ($result as $tmp_field) {
                        unset($tmp_field["ShowDebtor"]);
                        unset($tmp_field["ShowHandle"]);
                        unset($tmp_field["ShowOrderform"]);
                        unset($tmp_field["ShowInvoice"]);
                        unset($tmp_field["ShowPriceQuote"]);
                        unset($tmp_field["OrderID"]);
                        $custom_fields[] = $tmp_field;
                    }
                    $_SESSION["custom_fields"][$reference_type] = $custom_fields;
            }
        }
        return $custom_fields;
    }
    private function __getCustomFieldsValues($reference_type, $reference_id)
    {
        switch ($reference_type) {
            case "handle":
                $show_columm = "ShowHandle";
                break;
            case "invoice":
                $show_columm = "ShowInvoice";
                break;
            case "pricequote":
                $show_columm = "ShowPriceQuote";
                break;
            default:
                $show_columm = "ShowDebtor";
                $custom_values = [];
                if(0 < $reference_id) {
                    Database_Model::getInstance()->get("HostFact_Debtor_Custom_Fields", ["HostFact_Debtor_Custom_Values.*", "HostFact_Debtor_Custom_Fields.`FieldCode`", "HostFact_Debtor_Custom_Fields.`LabelType`", "HostFact_Debtor_Custom_Fields.`LabelOptions`", "HostFact_Debtor_Custom_Fields.`LabelDefault`"])->join("HostFact_Debtor_Custom_Values", "HostFact_Debtor_Custom_Values.`ReferenceType`=:reference_type AND HostFact_Debtor_Custom_Values.`ReferenceID`=:reference_id AND HostFact_Debtor_Custom_Values.`FieldID`=HostFact_Debtor_Custom_Fields.`id`")->bindValue("reference_type", $reference_type)->bindValue("reference_id", $reference_id)->where($show_columm, "yes");
                    if($reference_type == "newcustomer") {
                        Database_Model::getInstance()->where("HostFact_Debtor_Custom_Fields.`ShowOrderform`", "yes");
                    }
                    $result = Database_Model::getInstance()->execute();
                } elseif($reference_id === false) {
                    Database_Model::getInstance()->get("HostFact_Debtor_Custom_Fields", ["NULL as `Value`", "HostFact_Debtor_Custom_Fields.`FieldCode`", "HostFact_Debtor_Custom_Fields.`LabelType`", "HostFact_Debtor_Custom_Fields.`LabelOptions`", "HostFact_Debtor_Custom_Fields.`LabelDefault`"])->where($show_columm, "yes");
                    if($reference_type == "newcustomer") {
                        Database_Model::getInstance()->where("HostFact_Debtor_Custom_Fields.`ShowOrderform`", "yes");
                    }
                    $result = Database_Model::getInstance()->execute();
                }
                if($result) {
                    foreach ($result as $tmp_value) {
                        $tmp_value->Value = is_null($tmp_value->Value) ? $tmp_value->LabelDefault : $tmp_value->Value;
                        $custom_values[$tmp_value->FieldCode]["Value"] = $tmp_value->Value;
                        switch ($tmp_value->LabelType) {
                            case "checkbox":
                                $options = (array) json_decode($tmp_value->LabelOptions);
                                $checked = (array) json_decode($tmp_value->Value);
                                $custom_values[$tmp_value->FieldCode]["Value"] = $checked;
                                $tmp_array = [];
                                foreach ($checked as $tmp_checked) {
                                    if(isset($options["opt-" . $tmp_checked])) {
                                        $tmp_array[] = $options["opt-" . $tmp_checked];
                                    }
                                }
                                $custom_values[$tmp_value->FieldCode]["ValueFormatted"] = implode(", ", $tmp_array);
                                break;
                            case "select":
                            case "radio":
                                $options = (array) json_decode($tmp_value->LabelOptions);
                                $custom_values[$tmp_value->FieldCode]["ValueFormatted"] = isset($options["opt-" . $tmp_value->Value]) ? $options["opt-" . $tmp_value->Value] : "";
                                break;
                            case "input":
                            case "textarea":
                                $custom_values[$tmp_value->FieldCode]["ValueFormatted"] = $tmp_value->Value;
                                break;
                            case "date":
                                $custom_values[$tmp_value->FieldCode]["ValueFormatted"] = $tmp_value->Value ? rewrite_date_db2site($tmp_value->Value) : "";
                                break;
                        }
                    }
                    return $custom_values;
                } else {
                    return false;
                }
        }
    }
    private function __setCustomFieldsValue($reference_type, $reference_id, $field_id, $value)
    {
        Database_Model::getInstance()->insert("HostFact_Debtor_Custom_Values", ["ReferenceType" => $reference_type, "ReferenceID" => $reference_id, "FieldID" => $field_id, "Value" => $value])->onDuplicate(["Value" => $value])->execute();
    }
    private function __setCustomFieldsValues($reference_type, $reference_id, $values)
    {
        $custom_fields = $this->__getCustomFields($reference_type);
        foreach ($custom_fields as $custom_field) {
            Database_Model::getInstance()->insert("HostFact_Debtor_Custom_Values", ["ReferenceType" => $reference_type, "ReferenceID" => $reference_id, "FieldID" => $custom_field["id"], "Value" => isset($values[$custom_field["FieldCode"]]) ? $values[$custom_field["FieldCode"]] : ""])->onDuplicate(["Value" => isset($values[$custom_field["FieldCode"]]) ? $values[$custom_field["FieldCode"]] : ""])->execute();
        }
    }
}
function show_custom_input_field($field, $value = NULL, $prefix = "")
{
    $html = "";
    if($value !== NULL) {
        $field["Value"] = $value;
    }
    switch ($field["LabelType"]) {
        case "select":
            $options = (array) json_decode($field["LabelOptions"]);
            $selected = isset($field["Value"]) && $field["Value"] && array_key_exists("opt-" . $field["Value"], $options) ? $field["Value"] : $field["LabelDefault"];
            $html .= "<select name=\"" . $prefix . "custom[" . $field["FieldCode"] . "]\" class=\"text1 size4 customfield\">";
            foreach ($options as $k => $v) {
                $html .= "<option value=\"" . htmlspecialchars(substr($k, 4)) . "\" " . ($selected == substr($k, 4) ? "selected=\"selected\"" : "") . ">" . htmlspecialchars($v) . "</option>";
            }
            $html .= "</select>";
            break;
        case "input":
            $html .= "<input type=\"text\" class=\"text1 size1 customfield\" name=\"" . $prefix . "custom[" . $field["FieldCode"] . "]\" value=\"" . (isset($field["Value"]) && $field["Value"] ? htmlspecialchars($field["Value"]) : "") . "\"/>";
            break;
        case "checkbox":
            $options = (array) json_decode($field["LabelOptions"]);
            $checked = isset($field["Value"]) && is_array($field["Value"]) ? $field["Value"] : (array) json_decode($field["LabelDefault"]);
            foreach ($options as $k => $v) {
                $html .= "<label><input type=\"checkbox\" name=\"" . $prefix . "custom[" . $field["FieldCode"] . "][]\" class=\"customfield\" value=\"" . htmlspecialchars(substr($k, 4)) . "\" " . (in_array(substr($k, 4), $checked) ? "checked=\"checked\"" : "") . ">" . htmlspecialchars($v) . "</label><br />";
            }
            $html = substr($html, 0, -6);
            break;
        case "radio":
            $options = (array) json_decode($field["LabelOptions"]);
            $checked = isset($field["Value"]) && $field["Value"] && array_key_exists("opt-" . $field["Value"], $options) ? $field["Value"] : $field["LabelDefault"];
            foreach ($options as $k => $v) {
                $html .= "<label><input type=\"radio\" name=\"" . $prefix . "custom[" . $field["FieldCode"] . "]\" class=\"customfield\" value=\"" . htmlspecialchars(substr($k, 4)) . "\" " . ($checked == substr($k, 4) ? "checked=\"checked\"" : "") . ">" . htmlspecialchars($v) . "</label><br />";
            }
            $html = substr($html, 0, -6);
            break;
        case "date":
            $html .= "<input type=\"text\" class=\"text1 size6 datepicker customfield\" name=\"" . $prefix . "custom[" . $field["FieldCode"] . "]\" value=\"" . (isset($field["Value"]) && $field["Value"] ? htmlspecialchars(rewrite_date_db2site($field["Value"])) : "") . "\"  />";
            break;
        case "textarea":
            $html .= "<textarea class=\"text1 size11 customfield autogrow\" rows=\"4\" name=\"" . $prefix . "custom[" . $field["FieldCode"] . "]\">" . (isset($field["Value"]) && $field["Value"] ? htmlspecialchars($field["Value"]) : "") . "</textarea>";
            break;
        default:
            return $html;
    }
}
function show_custom_field($field, $value)
{
    $html = "<strong class=\"title2\">" . htmlspecialchars($field["LabelTitle"]) . "</strong>";
    switch ($field["LabelType"]) {
        case "textarea":
            $html .= "<span class=\"title2_value\" style=\"float:left;\">";
            $html .= nl2br(htmlspecialchars($value));
            $html .= "</span><br clear=\"both\" />";
            break;
        default:
            $html .= "<span class=\"title2_value\">";
            $html .= htmlspecialchars($value);
            $html .= "</span>";
            return $html;
    }
}
function show_custom_field_handlepage($handle_type, $field, $value)
{
    $value = $value ? htmlspecialchars($value) : "-";
    $html = "<div class=\"title2_sub align_right\">" . htmlspecialchars($field["LabelTitle"]) . "</div>";
    switch ($field["LabelType"]) {
        case "textarea":
            $html .= "<div id=\"label_" . $handle_type . "_" . $field["FieldCode"] . "\" class=\"title2_sub_value\" style=\"float:left;\">";
            $html .= nl2br($value);
            $html .= "</div><br clear=\"both\" />";
            break;
        default:
            $html .= "<div id=\"label_" . $handle_type . "_" . $field["FieldCode"] . "\" class=\"title2_sub_value\">";
            $html .= $value;
            $html .= "</div>";
            return $html;
    }
}
function show_custom_field_invoice($field, $value)
{
    $html = "<div class=\"field_row\">";
    $html .= "<span class=\"field_label\">" . htmlspecialchars($field["LabelTitle"]) . ":</span>";
    switch ($field["LabelType"]) {
        case "textarea":
            $html .= "<span class=\"field_value\">";
            $html .= nl2br(htmlspecialchars($value));
            $html .= "</span>";
            break;
        default:
            $html .= "<span class=\"field_value\">";
            $html .= htmlspecialchars($value);
            $html .= "</span>";
            $html .= "</div>";
            return $html;
    }
}

?>