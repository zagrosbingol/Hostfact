<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class exportTemplate
{
    public $Identifier;
    public $ExportData;
    public $Name;
    public $Type;
    public $Data;
    public $Template;
    public $Dataset;
    public $Table;
    public $Tables;
    public $Filter;
    public $Elements;
    public $Error;
    public $Warning;
    public $Success;
    public function __construct()
    {
        $this->Date = rewrite_date_db2site(date("Y-m-d H:i:s"));
        $this->Error = [];
        $this->Warning = [];
        $this->Success = [];
    }
    public function get_templates($exportdata_table)
    {
        $result = Database_Model::getInstance()->get("HostFact_ExportTemplates", ["id", "Name"])->where("ExportData", $exportdata_table)->where("Status", ["!=" => "9"])->execute();
        if($result && is_array($result)) {
            foreach ($result as $_template) {
                $exportTemplates[] = ["id" => $_template->id, "name" => $_template->Name];
            }
        }
        if(is_array($exportTemplates)) {
            return $exportTemplates;
        }
        $this->Error[] = __("no export templates found");
        return [];
    }
    public function show()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for export");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_ExportTemplates", ["*"])->where("id", $this->Identifier)->execute();
        if(!$result || empty($result->id)) {
            $this->Error[] = __("invalid identifier for export");
            return false;
        }
        foreach ($result as $key => $value) {
            $this->{$key} = htmlspecialchars($value);
        }
        $this->Elements = [];
        $result = Database_Model::getInstance()->get("HostFact_ExportTemplateElements", ["CONCAT(`Table`,'.',`Field`) AS `Element`"])->where("Template", $this->Identifier)->orderBy("Order", "ASC")->execute();
        if($result && is_array($result)) {
            foreach ($result as $_element) {
                $this->Elements[] = $_element->Element;
            }
        }
        return true;
    }
    public function save_template()
    {
        if(!$this->validate()) {
            return false;
        }
        if(!empty($this->Identifier)) {
            Database_Model::getInstance()->delete("HostFact_ExportTemplateElements")->where("Template", $this->Identifier)->execute();
            Database_Model::getInstance()->update("HostFact_ExportTemplates", ["Name" => $this->Template])->where("id", $this->Identifier)->execute();
            $this->Success[] = sprintf(__("exporttemplate is adjusted"), $this->Name);
        } else {
            $result = Database_Model::getInstance()->insert("HostFact_ExportTemplates", ["ExportData" => $this->ExportData, "Name" => $this->Template, "Type" => "intern", "Date" => ["RAW" => "CURDATE()"]])->execute();
            $this->Identifier = $result;
            $this->Success[] = sprintf(__("exporttemplate is created"), $this->Name);
        }
        $elements = new exporttemplateelement();
        foreach ($this->Data as $key => $value) {
            $elements->Template = $this->Identifier;
            $elements->Table = substr($value, 0, strpos($value, "."));
            $elements->Field = substr($value, strpos($value, ".") + 1);
            $elements->Order = $key;
            $elements->add();
        }
        return true;
    }
    public function get_tabledata()
    {
        $this->show();
        $select = [];
        $tables = [$this->ExportData];
        $join_tables = [];
        $exclude_elements = ["CustomFields"];
        $eu_countries = [];
        if(is_array($this->Filter)) {
            foreach ($this->Filter as $_filter) {
                if($_filter["type"] == "country_invoice") {
                    $result = Database_Model::getInstance()->get("HostFact_Settings_Countries", ["*"])->where("EUCountry", "yes")->execute();
                    if($result) {
                        foreach ($result as $v) {
                            $eu_countries[] = $v->CountryCode;
                        }
                    }
                }
            }
        }
        foreach ($this->Elements as $key => $value) {
            $arr_value = explode(".", $value);
            $tables[] = $arr_value[0];
            if(in_array($arr_value[1], $exclude_elements)) {
                if($arr_value[1] == "CustomFields") {
                    switch ($arr_value[0]) {
                        case "HostFact_Debtors":
                            $select[] = "HostFact_Debtors.`id` AS 'HostFact_Debtors.DebtorId'";
                            break;
                        case "HostFact_Invoice":
                            $select[] = "HostFact_Invoice.`id` AS 'HostFact_Invoice.InvoiceId'";
                            break;
                        case "HostFact_PriceQuote":
                            $select[] = "HostFact_PriceQuote.`id` AS 'HostFact_PriceQuote.PriceQuoteId'";
                            break;
                    }
                }
            } else {
                if($arr_value[0] == "HostFact_Domains") {
                    $arr_value[1] = str_replace("CompanyLegalForm", "LegalForm", $arr_value[1]);
                }
                if(in_array($value, ["HostFact_Creditors.CreditorGroup", "HostFact_Debtors.DebtorGroup"])) {
                    $select[] = $arr_value[0] . ".`id` AS '" . $value . "'";
                } elseif($arr_value[0] == "HostFact_Domains" && strpos($arr_value[1], "owner") !== false) {
                    $select[] = "owner.`" . str_replace("owner", "", $arr_value[1]) . "` AS '" . $value . "'";
                    $join_tables[] = "ownerhandle";
                } elseif($arr_value[0] == "HostFact_Domains" && strpos($arr_value[1], "admin") !== false) {
                    $select[] = "admin.`" . str_replace("admin", "", $arr_value[1]) . "` AS '" . $value . "'";
                    $join_tables[] = "adminhandle";
                } elseif($arr_value[0] == "HostFact_Domains" && strpos($arr_value[1], "tech") !== false) {
                    $select[] = "tech.`" . str_replace("tech", "", $arr_value[1]) . "` AS '" . $value . "'";
                    $join_tables[] = "techhandle";
                } elseif($value == "HostFact_Invoice.AmountPaid") {
                    $select[] = "IF(`" . $arr_value[0] . "`.`Status` = '3',`" . $arr_value[0] . "`.`" . $arr_value[1] . "`,0.00) AS '" . $value . "'";
                } else {
                    $select[] = "`" . $arr_value[0] . "`.`" . $arr_value[1] . "` AS '" . $value . "'";
                }
            }
        }
        if($this->ExportData == "HostFact_PeriodicElements") {
            $tables[] = "HostFact_Debtors";
        }
        $tables = array_unique($tables);
        Database_Model::getInstance()->get($this->ExportData, $select);
        $x = 1;
        foreach ($tables as $table) {
            if($x != 1) {
                switch ($table) {
                    case "HostFact_Debtors":
                        Database_Model::getInstance()->join("HostFact_Debtors", $this->ExportData . ".Debtor = HostFact_Debtors.id");
                        break;
                    case "HostFact_Creditors":
                        Database_Model::getInstance()->join("HostFact_Creditors", $this->ExportData . ".Creditor = HostFact_Creditors.id");
                        break;
                    case "HostFact_PeriodicElements":
                        Database_Model::getInstance()->join("HostFact_PeriodicElements", $this->ExportData . ".PeriodicID = HostFact_PeriodicElements.id");
                        break;
                    case "HostFact_Products":
                        Database_Model::getInstance()->join("HostFact_Products", $this->ExportData . ".Product = HostFact_Products.id");
                        break;
                    case "HostFact_Registrar":
                        Database_Model::getInstance()->join("HostFact_Registrar", $this->ExportData . ".Registrar = HostFact_Registrar.id");
                        break;
                    case "HostFact_Hosting":
                        Database_Model::getInstance()->join("HostFact_Hosting", $this->ExportData . ".HostingID = HostFact_Hosting.id");
                        break;
                    case "HostFact_Servers":
                        Database_Model::getInstance()->join("HostFact_Servers", $this->ExportData . ".Server = HostFact_Servers.id");
                        break;
                    case "HostFact_Packages":
                        Database_Model::getInstance()->join("HostFact_Packages", $this->ExportData . ".Package = HostFact_Packages.id");
                        break;
                }
            }
            $x++;
        }
        switch ($this->ExportData) {
            case "HostFact_Debtors":
                Database_Model::getInstance()->where("HostFact_Debtors.Status", ["!=" => "9"]);
                break;
            case "HostFact_Creditors":
                Database_Model::getInstance()->where("HostFact_Creditors.Status", ["!=" => "9"]);
                break;
            case "HostFact_PeriodicElements":
                Database_Model::getInstance()->where("HostFact_PeriodicElements.Status", ["NOT IN" => ["8", "9"]]);
                break;
            case "HostFact_Domains":
                Database_Model::getInstance()->where("HostFact_Domains.Status", ["!=" => "9"]);
                if(in_array("ownerhandle", $join_tables)) {
                    Database_Model::getInstance()->join("HostFact_Handles as `owner`", $this->ExportData . ".ownerHandle = owner.id");
                }
                if(in_array("ownerhandle", $join_tables)) {
                    Database_Model::getInstance()->join("HostFact_Handles as `admin`", $this->ExportData . ".adminHandle = admin.id");
                }
                if(in_array("ownerhandle", $join_tables)) {
                    Database_Model::getInstance()->join("HostFact_Handles as `tech`", $this->ExportData . ".techHandle = tech.id");
                }
                break;
            case "HostFact_Hosting":
                Database_Model::getInstance()->where("HostFact_Hosting.Status", ["!=" => "9"]);
                break;
            case "HostFact_Invoice":
                Database_Model::getInstance()->orderBy("HostFact_Invoice.Date")->orderBy("HostFact_Invoice.InvoiceCode");
                break;
            default:
                if(is_array($this->Filter)) {
                    foreach ($this->Filter as $key => $value) {
                        if(isset($value["value"]) && $value["value"] != "" || isset($value["value1"]) && $value["value1"] != "" && isset($value["value2"]) && $value["value2"] != "" || is_array($value["checked"]) && count($value["checked"])) {
                            switch ($value["type"]) {
                                case "date":
                                    Database_Model::getInstance()->where($this->ExportData . "." . $value["field"], ["BETWEEN" => [rewrite_date_site2db($value["value1"]), rewrite_date_site2db($value["value2"])]]);
                                    break;
                                case "id":
                                    Database_Model::getInstance()->where($this->ExportData . "." . $value["field"], [">=" => $value["value1"]])->where($this->ExportData . "." . $value["field"], ["<=" => $value["value2"]]);
                                    break;
                                case "country_invoice":
                                    global $array_country;
                                    global $company;
                                    if($value["value"] == "eu") {
                                        Database_Model::getInstance()->where($this->ExportData . "." . $value["field"], ["IN" => $eu_countries]);
                                    } elseif($value["value"] == "rest") {
                                        $rest_countries = [];
                                        foreach ($array_country as $country_key => $country_value) {
                                            if($country_key != $company->Country && strlen($country_key) == 2 && !in_array($country_key, $eu_countries)) {
                                                $rest_countries[] = $country_key;
                                            }
                                        }
                                        Database_Model::getInstance()->where($this->ExportData . "." . $value["field"], ["IN" => $rest_countries]);
                                    } elseif($value["value"] == "all") {
                                    } else {
                                        Database_Model::getInstance()->where($this->ExportData . "." . $value["field"], $value["value"]);
                                    }
                                    break;
                                case "checkbox":
                                    if(in_array($value["field"], ["CreditorGroup", "DebtorGroup"]) && is_array($value["checked"])) {
                                        $aChecked = [];
                                        foreach ($value["checked"] as $iKey => $iGroupId) {
                                            if(is_numeric($iGroupId)) {
                                                $aChecked[] = intval($iGroupId);
                                            }
                                        }
                                        Database_Model::getInstance()->where($this->ExportData . ".id", ["IN" => ["RAW" => "SELECT `Reference` FROM `HostFact_GroupRelations` WHERE `Group` IN (" . implode(",", $aChecked) . ")"]]);
                                    } elseif(is_array($value["checked"])) {
                                        $aChecked = [];
                                        foreach ($value["checked"] as $iKey => $sValue) {
                                            $aChecked[] = $sValue;
                                        }
                                        Database_Model::getInstance()->where($this->ExportData . "." . $value["field"], ["IN" => $aChecked]);
                                    }
                                    break;
                                default:
                                    Database_Model::getInstance()->where($this->ExportData . "." . $value["field"], $value["value"]);
                            }
                        }
                    }
                }
                switch ($this->ExportData) {
                    case "HostFact_Debtors":
                        Database_Model::getInstance()->orderBy("IF(SUBSTRING(HostFact_Debtors.`DebtorCode`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(HostFact_Debtors.`DebtorCode`,1,1))", "ASC")->orderBy("LENGTH(HostFact_Debtors.`DebtorCode`)", "ASC")->orderBy("HostFact_Debtors.`DebtorCode`", "ASC");
                        break;
                    case "HostFact_Creditors":
                        Database_Model::getInstance()->orderBy("IF(SUBSTRING(HostFact_Creditors.`CreditorCode`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(HostFact_Creditors.`CreditorCode`,1,1))", "ASC")->orderBy("LENGTH(HostFact_Creditors.`CreditorCode`)", "ASC")->orderBy("HostFact_Creditors.`CreditorCode`", "ASC");
                        break;
                    case "HostFact_PeriodicElements":
                        Database_Model::getInstance()->orderBy("(SELECT CASE HostFact_Debtors.InvoiceCollect \n    \t\t\t\t\t\t\t\tWHEN '2' THEN CONCAT(DATE_FORMAT(HostFact_PeriodicElements.`NextDate`, '%Y-%m-'),IF(DAYOFMONTH(HostFact_PeriodicElements.`NextDate`) >= 15, '15', '01')) \n    \t\t\t\t\t\t\t\tWHEN '1' THEN CONCAT(DATE_FORMAT(HostFact_PeriodicElements.`NextDate`, '%Y-%m-'),'01') \n    \t\t\t\t\t\t\t\tWHEN '-1' THEN " . (INVOICE_COLLECT_ENABLED == "yes" ? "CONCAT(DATE_FORMAT(HostFact_PeriodicElements.`NextDate`, '%Y-%m-'),IF(DAYOFMONTH(HostFact_PeriodicElements.`NextDate`) >= 15, '" . (INVOICE_COLLECT_TPM == 1 ? "01" : "15") . "', '01'))" : "HostFact_PeriodicElements.`NextDate`") . " ELSE HostFact_PeriodicElements.`NextDate` END)", "DESC")->orderBy("IF(HostFact_PeriodicElements.`AutoRenew`='no',1,0)", "ASC")->orderBy("HostFact_PeriodicElements.Debtor", "ASC")->orderBy("HostFact_PeriodicElements.id", "ASC");
                        break;
                    case "HostFact_Products":
                        Database_Model::getInstance()->orderBy("IF(SUBSTRING(HostFact_Products.`ProductCode`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(HostFact_Products.`ProductCode`,1,1))", "ASC")->orderBy("LENGTH(HostFact_Products.`ProductCode`)", "ASC")->orderBy("HostFact_Products.`ProductCode`", "ASC");
                        break;
                    case "HostFact_Hosting":
                        Database_Model::getInstance()->orderBy("HostFact_Hosting.Username", "ASC");
                        break;
                    case "HostFact_Domains":
                        Database_Model::getInstance()->orderBy("HostFact_Domains.Domain", "ASC");
                        break;
                    case "HostFact_Invoice":
                        Database_Model::getInstance()->orderBy("HostFact_Invoice.`Date`", "DESC")->orderBy("IF(SUBSTRING(HostFact_Invoice.`InvoiceCode`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(HostFact_Invoice.`InvoiceCode`,1,1))", "DESC")->orderBy("LENGTH(HostFact_Invoice.`InvoiceCode`)", "DESC")->orderBy("HostFact_Invoice.`InvoiceCode`", "DESC");
                        break;
                    case "HostFact_CreditInvoice":
                        Database_Model::getInstance()->orderBy("IF(SUBSTRING(HostFact_CreditInvoice.`CreditInvoiceCode`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(HostFact_CreditInvoice.`CreditInvoiceCode`,1,1))", "DESC")->orderBy("LENGTH(HostFact_CreditInvoice.`CreditInvoiceCode`)", "DESC")->orderBy("HostFact_CreditInvoice.`CreditInvoiceCode`", "DESC");
                        break;
                    case "HostFact_PriceQuote":
                        Database_Model::getInstance()->orderBy("HostFact_PriceQuote.`Date`", "DESC")->orderBy("IF(SUBSTRING(HostFact_PriceQuote.`PriceQuoteCode`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(HostFact_PriceQuote.`PriceQuoteCode`,1,1))", "DESC")->orderBy("LENGTH(HostFact_PriceQuote.`PriceQuoteCode`)", "DESC")->orderBy("HostFact_PriceQuote.`PriceQuoteCode`", "DESC");
                        break;
                    default:
                        $result_db = Database_Model::getInstance()->asArray()->execute();
                        $aCustomFields = [];
                        require_once "class/customfields.php";
                        $custom_fields_obj = new customfields();
                        if(in_array("HostFact_Debtors", $tables)) {
                            $aCustomDebtorFields = $custom_fields_obj->getCustomDebtorFields();
                        }
                        if(in_array("HostFact_Invoice", $tables)) {
                            $aCustomInvoiceFields = $custom_fields_obj->getCustomInvoiceFields();
                        }
                        if(in_array("HostFact_PriceQuote", $tables)) {
                            $aCustomPriceQuoteFields = $custom_fields_obj->getCustomPriceQuoteFields();
                        }
                        if($result_db) {
                            $counter = 0;
                            foreach ($result_db as $result) {
                                foreach ($this->Elements as $key => $value) {
                                    $arr_value = explode(".", $value);
                                    if(in_array($arr_value[1], $exclude_elements)) {
                                        switch ($arr_value[1]) {
                                            case "CustomFields":
                                                $customFieldValues = $aCustomFields = [];
                                                switch ($arr_value[0]) {
                                                    case "HostFact_Debtors":
                                                        $aCustomFields = $aCustomDebtorFields;
                                                        $customFieldValues = $custom_fields_obj->getCustomDebtorFieldsValues($result["HostFact_Debtors.DebtorId"]);
                                                        break;
                                                    case "HostFact_Invoice":
                                                        $aCustomFields = $aCustomInvoiceFields;
                                                        $customFieldValues = $custom_fields_obj->getCustomInvoiceFieldsValues($result["HostFact_Invoice.InvoiceId"]);
                                                        break;
                                                    case "HostFact_PriceQuote":
                                                        $aCustomFields = $aCustomPriceQuoteFields;
                                                        $customFieldValues = $custom_fields_obj->getCustomPriceQuoteFieldsValues($result["HostFact_PriceQuote.PriceQuoteId"]);
                                                        break;
                                                    default:
                                                        if(!empty($aCustomFields)) {
                                                            foreach ($aCustomFields as $key => $aValues) {
                                                                $tabledata[$counter][$arr_value[0] . "." . $aValues["LabelTitle"]] = $customFieldValues[$aValues["FieldCode"]]["ValueFormatted"];
                                                            }
                                                        }
                                                }
                                                break;
                                        }
                                    } else {
                                        $tabledata[$counter][$value] = $result[$value];
                                    }
                                }
                                if(isset($tabledata[$counter]["HostFact_Debtors.DebtorId"])) {
                                    unset($tabledata[$counter]["HostFact_Debtors.DebtorId"]);
                                }
                                $counter++;
                            }
                        }
                        if(isset($tabledata) && is_array($tabledata)) {
                            return $tabledata;
                        }
                        $this->Error[] = __("no export results found for selected filters");
                        return [];
                }
        }
    }
    public function get_csv_content()
    {
        $csv_tabledata = $this->get_tabledata();
        $csv_rows = [];
        foreach ($csv_tabledata as $csv_key => $csv_column) {
            foreach ($csv_column as $csv_key2 => $csv_value) {
                $csv_value = str_replace(["\r\n", "\n", "\""], [" ", " ", "\"\""], $csv_value);
                if(!empty($this->config[$this->ExportData]["translations"][$csv_key2])) {
                    if($this->config[$this->ExportData]["translations"][$csv_key2] == "rewrite_date_db2site") {
                        $csv_rows[$csv_key] .= "\"" . rewrite_date_db2site($csv_value) . "\",";
                    } else {
                        ${$this->config[$this->ExportData]["translations"][$csv_key2]} =& ${$this->config[$this->ExportData]["translations"][$csv_key2]};
                        if(isset(${$this->config[$this->ExportData]["translations"][$csv_key2]}[$csv_value])) {
                            $csv_rows[$csv_key] .= "\"" . html_entity_decode(${$this->config[$this->ExportData]["translations"][$csv_key2]}[$csv_value]) . "\",";
                            $csv_rows[$csv_key] = str_replace("--- Selecteer uw land ---", "", $csv_rows[$csv_key]);
                        } else {
                            $csv_rows[$csv_key] .= "\"" . $csv_value . "\",";
                        }
                    }
                } elseif(strstr($csv_key2, "CreditorGroup")) {
                    $aCreditorGroup = [];
                    $result = Database_Model::getInstance()->get(["HostFact_Group", "HostFact_GroupRelations"], ["HostFact_Group.GroupName"])->where("HostFact_GroupRelations.Reference", $csv_value)->where("HostFact_GroupRelations.Type", "creditor")->where("HostFact_GroupRelations.`Group` = HostFact_Group.id")->where("HostFact_Group.Type", "creditor")->where("HostFact_Group.Status", ["!=" => "9"])->execute();
                    if($result && is_array($result)) {
                        foreach ($result as $_group) {
                            $aCreditorGroup[] = htmlspecialchars($_group->GroupName);
                        }
                    }
                    $csv_rows[$csv_key] .= "\"" . @implode(", ", $aCreditorGroup) . "\",";
                } elseif(strstr($csv_key2, "DebtorGroup")) {
                    $aDebtorGroup = [];
                    $result = Database_Model::getInstance()->get(["HostFact_Group", "HostFact_GroupRelations"], ["HostFact_Group.GroupName"])->where("HostFact_GroupRelations.Reference", $csv_value)->where("HostFact_GroupRelations.Type", "debtor")->where("HostFact_GroupRelations.`Group` = HostFact_Group.id")->where("HostFact_Group.Type", "debtor")->where("HostFact_Group.Status", ["!=" => "9"])->execute();
                    if($result && is_array($result)) {
                        foreach ($result as $_group) {
                            $aDebtorGroup[] = htmlspecialchars($_group->GroupName);
                        }
                    }
                    $csv_rows[$csv_key] .= "\"" . @implode(", ", $aDebtorGroup) . "\",";
                } elseif(strstr($csv_key2, "Discount")) {
                    $csv_rows[$csv_key] .= "\"" . $csv_value * 100 . "\",";
                } elseif(strstr($csv_key2, "Password")) {
                    $csv_rows[$csv_key] .= "\"" . @passcrypt($csv_value) . "\",";
                } else {
                    $csv_rows[$csv_key] .= "\"" . $csv_value . "\",";
                }
            }
            $csv_rows[$csv_key] = substr($csv_rows[$csv_key], 0, -1);
        }
        return $csv_rows;
    }
    public function delete($remove = false)
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for export");
            return false;
        }
        Database_Model::getInstance()->update("HostFact_ExportTemplateElements", ["Status" => "9"])->where("Template", $this->Identifier)->execute();
        $result = Database_Model::getInstance()->update("HostFact_ExportTemplates", ["Status" => "9"])->where("id", $this->Identifier)->execute();
        if($result) {
            $this->Success[] = sprintf(__("export template is removed"), $this->Identifier);
            return true;
        }
        return false;
    }
    public function all($fields, $sort = false, $order = false, $limit = "-1", $searchat = false, $searchfor = false, $show_results = MAX_RESULTS_LIST)
    {
        $list = [];
        if($limit == "") {
            $limit = -1;
        }
        if(!is_array($fields) || empty($fields)) {
            $this->Error[] = parse_error(__FILE__, "exportTemplate", "all", 775, "no fields");
            return [];
        }
        $search_at = [];
        if($searchat && $searchfor) {
            $search_at = explode("|", $searchat);
        }
        $select = ["id"];
        foreach ($fields as $column) {
            $select[] = $column;
        }
        Database_Model::getInstance()->get("HostFact_ExportTemplates", $select)->where("Status", ["!=" => "9"])->where("Type", isset($this->TemplateType) && $this->TemplateType == "extern" ? "extern" : "intern");
        if(!empty($search_at)) {
            $or_clausule = [];
            foreach ($search_at as $searchColumn) {
                $or_clausule[] = [$searchColumn, ["LIKE" => "%" . $searchfor . "%"]];
            }
            Database_Model::getInstance()->orWhere($or_clausule);
        }
        $order = $sort ? $order ? $order : "DESC" : "";
        if($sort) {
            Database_Model::getInstance()->orderBy($sort, $order);
        } else {
            Database_Model::getInstance()->orderBy("id", $order);
        }
        if(0 <= $limit) {
            Database_Model::getInstance()->limit((max(1, $limit) - 1) * $show_results, $show_results);
        }
        $list = [];
        $list["CountRows"] = 0;
        if($template_list = Database_Model::getInstance()->execute()) {
            $list["CountRows"] = Database_Model::getInstance()->rowCount("HostFact_ExportTemplates", "id");
            foreach ($template_list as $result) {
                $list[$result->id] = ["id" => $result->id];
                foreach ($fields as $column) {
                    $list[$result->id][$column] = htmlspecialchars($result->{$column});
                }
            }
        }
        return $list;
    }
    public function get_external_template($filename)
    {
        $template = Database_Model::getInstance()->getOne("HostFact_ExportTemplates", ["*"])->where("Type", "extern")->where("Filename", $filename)->execute();
        if($template->Filename != "") {
            return true;
        }
        return false;
    }
    public function validate()
    {
        if(!isset($this->Template) || !strlen($this->Template)) {
            $this->Error[] = __("no template name given");
        }
        if((!isset($this->Template) || !strlen($this->Template)) && empty($this->ExportData)) {
            $this->Error[] = __("no export data selected");
        }
        if(!isset($this->Data) || !count($this->Data)) {
            $this->Error[] = __("no export columns selected");
        }
        return empty($this->Error) ? true : false;
    }
}
class exporttemplateelement
{
    public $Identifier;
    public $Template;
    public $Table;
    public $Field;
    public $Order;
    public $Error;
    public $Warning;
    public $Success;
    public function __construct()
    {
        $this->Error = [];
        $this->Warning = [];
        $this->Success = [];
    }
    public function add()
    {
        $result = Database_Model::getInstance()->insert("HostFact_ExportTemplateElements", ["Template" => $this->Template, "Table" => $this->Table, "Field" => $this->Field, "Order" => $this->Order])->execute();
        if($result) {
            return true;
        }
        return false;
    }
}

?>