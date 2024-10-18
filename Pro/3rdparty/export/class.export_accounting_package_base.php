<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class export_accounting_package_base
{
    public $package;
    public $eu_countries;
    public $hasOAuth;
    public $configuredOAuth;
    public $Error;
    public $Warning;
    public $Success;
    public $ErrorType;
    protected $exportLog;
    public $errorsReported;
    public $itemsExported;
    public $importableData;
    public function __construct($package)
    {
        global $array_country_EU;
        $this->package = $package;
        $this->eu_countries = array_keys($array_country_EU);
        $this->hasOAuth = $this->configuredOAuth = false;
        $this->Error = $this->Warning = $this->Success = $this->exportErrors = [];
        $this->ErrorType = "";
        $this->errorsReported = 0;
        $this->itemsExported = 0;
        $this->base_url = BACKOFFICE_URL . "exportaccounting.php?module=" . $this->package;
        $this->debtor_url = BACKOFFICE_URL . "debtors.php?page=show&id=";
        $this->creditor_url = BACKOFFICE_URL . "creditors.php?page=show&id=";
        $this->product_url = BACKOFFICE_URL . "products.php?page=show&id=";
        $this->invoice_url = BACKOFFICE_URL . "invoices.php?page=show&id=";
        $this->creditinvoice_url = BACKOFFICE_URL . "creditors.php?page=show_invoice&id=";
        $this->directdebit_url = BACKOFFICE_URL . "directdebit.php?page=show&id=";
        $this->product_group_add_url = BACKOFFICE_URL . "products.php?page=add_group";
        $this->creditor_group_add_url = BACKOFFICE_URL . "creditors.php?page=add_group";
        $settingsValue = $this->getSettingsValue("statistics");
        if($settingsValue === false) {
            $this->saveSettings("statistics", json_encode(["value" => ""]));
        } elseif(isset($settingsValue["debtors"]) || isset($settingsValue["invoices"])) {
            $type_translator = ["debtors" => "debtor", "invoices" => "invoice", "sddbatchess" => "sddbatch", "creditors" => "creditor", "products" => "product", "creditinvoices" => "creditinvoice"];
            $new_statistics = [];
            foreach ($settingsValue as $_key => $_value) {
                $filters = [];
                if(isset($_value["filters"][substr($_key, 0, -1)])) {
                    foreach ($_value["filters"][substr($_key, 0, -1)] as $_sub_key => $_filter_item) {
                        if(in_array($_sub_key, ["code", "batch"]) && isset($_filter_item["min"])) {
                            $filters["Filter"] = "manual";
                            $filters["FilterBy"] = "code";
                            $filters["Min"] = $_filter_item["min"];
                            $filters["Max"] = $_filter_item["max"];
                        } elseif(in_array($_sub_key, ["date"]) && isset($_filter_item["min"])) {
                            $filters["Filter"] = "manual";
                            $filters["FilterBy"] = "date";
                            $filters["Min"] = date("Y-m-d", strtotime(rewrite_date_site2db($_filter_item["min"])));
                            $filters["Max"] = date("Y-m-d", strtotime(rewrite_date_site2db($_filter_item["max"])));
                        }
                    }
                }
                $new_statistics[$type_translator[$_key]] = ["export_lastdate" => date("Y-m-d H:i:s", $_value["lastExport"]), "filters" => $filters];
            }
            $this->saveSettings("statistics", json_encode($new_statistics));
        }
        $this->config["taxRates"] = ["Zero", "0.06" => "Low", "0.09" => "Low", "0.19" => "High", "0.21" => "High", "0.23" => "High"];
        $this->importableData = [];
        $this->versionInfo = $this->getVersionInformation();
    }
    public function getPath($type)
    {
        if(isset($this->{$type . "_url"}) && $this->{$type . "_url"}) {
            return $this->{$type . "_url"};
        }
        return false;
    }
    public function getDashboardGroups()
    {
        return [["debtor", "invoice", "sddbatch"], ["creditor", "creditinvoice"], ["product"]];
    }
    public function getAvailableFilter($export_type)
    {
        $filters = [];
        switch ($export_type) {
            case "debtor":
            case "creditor":
            case "product":
            case "sddbatch":
                $filters["code"] = true;
                break;
            case "invoice":
            case "creditinvoice":
                $filters["code"] = true;
                $filters["date"] = true;
                break;
            default:
                return $filters;
        }
    }
    public function getSettingsValue($name)
    {
        $setting = $this->getSettings($name);
        return isset($setting["value"]) ? $setting["value"] : false;
    }
    public function getSettings($name)
    {
        if(strlen($this->package) === 0) {
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_ExportSettings", ["modified", "value"])->where("package", $this->package)->where("name", $name)->execute();
        if($result) {
            return ["modified" => $result->modified, "value" => json_decode($result->value, true)];
        }
        return false;
    }
    public function saveSettings($name, $value = "")
    {
        if(strlen($this->package) === 0) {
            return false;
        }
        Database_Model::getInstance()->insert("HostFact_ExportSettings", ["modified" => ["RAW" => "NOW()"], "value" => $value, "package" => $this->package, "name" => $name])->onDuplicate(["modified" => ["RAW" => "NOW()"], "value" => $value])->execute();
        return true;
    }
    public function deleteSetting($name)
    {
        Database_Model::getInstance()->delete("HostFact_ExportSettings")->where("package", $this->package)->where("name", $name)->execute();
    }
    public function saveAutomation($post_data)
    {
        global $administration;
        $data_types = [];
        if(isset($post_data["data"])) {
            foreach ($post_data["data"] as $_export_type) {
                $data_types[] = $_export_type;
            }
            $automation_settings = ["data" => $data_types];
            if(isset($post_data["time"])) {
                foreach ($post_data["time"] as $_type => $_time) {
                    if(in_array($_type, $data_types) && preg_match("/([01]?[0-9]|2[0-3]):[0-5][0-9]/", $_time)) {
                        $automation_settings["time"][$_type] = $_time;
                    }
                }
            }
            $this->saveSettings("automationSettings", json_encode($automation_settings));
        } else {
            $this->saveSettings("automationSettings", "");
        }
        if(1 <= count(array_diff($data_types, ["payment_invoice"]))) {
        }
        if(in_array("payment_invoice", $data_types)) {
        }
    }
    public function showLastExportDate($date)
    {
        $date_response = "";
        if(date("Y-m-d") == date("Y-m-d", strtotime($date))) {
            $date_response .= __("export accounting - date today");
        } elseif(date("Y-m-d", strtotime("-1 day")) == date("Y-m-d", strtotime($date))) {
            $date_response .= __("export accounting - date yesterday");
        } else {
            $date_response .= rewrite_date_db2site($date);
        }
        $date_response .= " " . __("at") . " " . rewrite_date_db2site($date, "%H:%i");
        return $date_response;
    }
    protected function _saveCache($request, $response)
    {
        $hash_key = md5($request);
        $_SESSION["export_accounting_package"][$this->package][$hash_key][time()] = serialize($response);
    }
    protected function _getCache($request)
    {
        $hash_key = md5($request);
        if(isset($_SESSION["export_accounting_package"][$this->package][$hash_key])) {
            $cached_data = $_SESSION["export_accounting_package"][$this->package][$hash_key];
            if(time() - key($cached_data) <= 3600) {
                return unserialize(current($cached_data));
            }
            unset($_SESSION["export_accounting_package"][$this->package][$hash_key]);
            return false;
        }
        return false;
    }
    protected function _resetCache($request)
    {
        $hash_key = md5($request);
        if(isset($_SESSION["export_accounting_package"][$this->package][$hash_key])) {
            unset($_SESSION["export_accounting_package"][$this->package][$hash_key]);
        }
    }
    public function importDebtors()
    {
        $list_debtors = [];
        if(!method_exists($this, "listDebtors")) {
            return false;
        }
        $list_debtors = $this->listDebtors();
        $counter = 0;
        if($list_debtors && is_array($list_debtors) && !empty($list_debtors)) {
            if(method_exists($this, "getDebtorMask")) {
                $mask = $this->getDebtorMask();
            } else {
                $ledger_accounts = $this->getSettingsValue("ledgerAccounts");
                $mask = $ledger_accounts["default"]["debtor_prefix"]["id"];
            }
            if(strpos($mask, "*") !== false) {
            } elseif(strpos($mask, "#") !== false || strpos($mask, "[0-9]") !== false) {
                $mask = str_replace("[0-9]", "#", $mask);
                $prefix = str_replace("#", "", $mask);
                $code = str_pad(1, substr_count($mask, "#"), "0", STR_PAD_LEFT);
                if(preg_match("/[0-9]+\$/i", $prefix, $matches)) {
                    $prefix = substr($prefix, 0, -1 * strlen($matches[0]));
                    $code = $matches[0] . $code;
                }
                $settings = new settings();
                $settings->Variable = "DEBTORCODE_PREFIX";
                $settings->Value = $prefix;
                $settings->edit();
                $settings->Variable = "DEBTORCODE_NUMBER";
                $settings->Value = $code;
                $settings->edit();
            }
            foreach ($list_debtors as $_debtor) {
                $debtor = new debtor();
                foreach ($_debtor as $_key => $_value) {
                    $debtor->{$_key} = $_value;
                }
                $debtor->Username = $debtor->DebtorCode;
                if(!$debtor->add()) {
                    foreach ($debtor->Error as $k => $v) {
                        $debtor->Error[$k] = "[" . $_debtor["PackageReference"] . "]" . $v;
                    }
                    generate_flash_message($debtor);
                } else {
                    $counter++;
                    $this->logExportedItem("debtor", $debtor->Identifier, "success", $_debtor["PackageReference"]);
                }
            }
        }
        if(0 < $counter) {
            $this->Success[] = sprintf(__("import accounting - x debtors imported"), $counter);
        }
        generate_flash_message($this);
    }
    public function filterItemsByMinMax($export_type, $filter_type, $between_from, $between_to)
    {
        switch ($export_type) {
            case "debtor":
                $code_column = "DebtorCode";
                $date_column = false;
                $export_object = new debtor();
                break;
            case "invoice":
                $code_column = "InvoiceCode";
                $date_column = "Date";
                $export_object = new invoice();
                break;
            case "creditor":
                $code_column = "CreditorCode";
                $date_column = false;
                $export_object = new creditor();
                break;
            case "creditinvoice":
                $code_column = "CreditInvoiceCode";
                $date_column = "Date";
                $export_object = new creditinvoice();
                break;
            case "product":
                $code_column = "ProductCode";
                $date_column = false;
                $export_object = new product();
                break;
            case "sddbatch":
                $code_column = "BatchID";
                $date_column = false;
                $export_object = new directdebit();
                $id_list = [];
                if($filter_type == "date") {
                    if($between_from == "" || $between_from == "1970-01-01") {
                        $this->Error[] = __("invalid date min");
                    }
                    if($between_to == "" || $between_to == "1970-01-01") {
                        $this->Error[] = __("invalid date max");
                    }
                } elseif($filter_type == "code") {
                    if($between_from == "") {
                        $this->Error[] = __("invalid " . strtolower($code_column) . " min");
                    } elseif($export_object->is_free($between_from)) {
                        $this->Error[] = __("invalid " . strtolower($code_column) . " min");
                    }
                    if($between_to == "") {
                        $this->Error[] = __("invalid " . strtolower($code_column) . " max");
                    } elseif($export_object->is_free($between_to)) {
                        $this->Error[] = __("invalid " . strtolower($code_column) . " max");
                    }
                } else {
                    return false;
                }
                if(!empty($this->Error)) {
                    return false;
                }
                switch ($export_type) {
                    case "debtor":
                        Database_Model::getInstance()->get("HostFact_Debtors", "id")->where("Status", ["!=" => 9]);
                        break;
                    case "invoice":
                        Database_Model::getInstance()->get("HostFact_Invoice", "id")->where("Status", [">" => 0]);
                        break;
                    case "creditor":
                        Database_Model::getInstance()->get("HostFact_Creditors", "id")->where("Status", ["!=" => 9]);
                        break;
                    case "creditinvoice":
                        Database_Model::getInstance()->get("HostFact_CreditInvoice", "id")->where("Status", [">" => 0]);
                        break;
                    case "product":
                        Database_Model::getInstance()->get("HostFact_Products", "id")->where("Status", ["!=" => 9]);
                        break;
                    case "sddbatch":
                        Database_Model::getInstance()->get("HostFact_SDD_Batches", "BatchID as `id`")->where("Status", ["IN" => ["downloaded", "processed"]]);
                        if($filter_type == "date" && $date_column) {
                            Database_Model::getInstance()->where($date_column, ["BETWEEN" => [$between_from, $between_to]])->orderBy($date_column, "ASC");
                        } elseif($filter_type == "code") {
                            $first_char_sql = "IF(SUBSTRING(`" . $code_column . "`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(`" . $code_column . "`,1,1))";
                            $first_char_start = is_numeric(substr($between_from, 0, 1)) ? "0" : substr($between_from, 0, 1);
                            $first_char_end = is_numeric(substr($between_to, 0, 1)) ? "0" : substr($between_to, 0, 1);
                            Database_Model::getInstance()->orWhere([[$first_char_sql, [">" => $first_char_start]], ["AND" => [[$first_char_sql, $first_char_start], ["LENGTH(`" . $code_column . "`)", [">" => strlen($between_from)]]]], ["AND" => [[$first_char_sql, $first_char_start], ["LENGTH(`" . $code_column . "`)", strlen($between_from)], [$code_column, [">=" => $between_from]]]]])->orWhere([[$first_char_sql, ["<" => $first_char_end]], ["AND" => [[$first_char_sql, $first_char_end], ["LENGTH(`" . $code_column . "`)", ["<" => strlen($between_to)]]]], ["AND" => [[$first_char_sql, $first_char_end], ["LENGTH(`" . $code_column . "`)", strlen($between_to)], [$code_column, ["<=" => $between_to]]]]]);
                            Database_Model::getInstance()->orderBy("IF(SUBSTRING(`" . $code_column . "`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(`" . $code_column . "`,1,1))", "ASC")->orderBy("LENGTH(`" . $code_column . "`)", "ASC")->orderBy("" . $code_column . "", "ASC");
                        } else {
                            return false;
                        }
                        $result = Database_Model::getInstance()->execute();
                        foreach ($result as $_item) {
                            $id_list[] = $_item->id;
                        }
                        if(!empty($id_list)) {
                            $this->saveStatistics($export_type, "manual", $filter_type, $between_from, $between_to);
                        }
                        return $id_list;
                        break;
                    default:
                        return false;
                }
                break;
            default:
                return false;
        }
    }
    protected function _toInt($string)
    {
        return (int) preg_replace("/[^0-9]/", "", $string);
    }
    protected function toAZ09($string)
    {
        return preg_replace("/[^A-Z0-9]/i", "", $string);
    }
    protected function toPhoneNumber($val)
    {
        $pattern = "/[(+]?[0-9]([0-9()+\\- ,]*[0-9)])?/";
        if(preg_match($pattern, trim($val), $matches) && $matches[0] == trim($val)) {
            return trim($val);
        }
        return false;
    }
    protected function toEmailAddress($val)
    {
        $val = trim($val);
        if(strpos($val, "@") === false || strpos($val, ".") === false) {
            return false;
        }
        if(strlen($val) < 3) {
            return false;
        }
        return $val;
    }
    protected function _getFirstMailAddress($emailAddress)
    {
        if(strpos($emailAddress, ";")) {
            $arrayEmailAddress = explode(";", $emailAddress);
            return $arrayEmailAddress[0];
        }
        return $emailAddress;
    }
    protected function storeError($item_type, $item_id, $error_message)
    {
        $this->exportLog[$item_type][$item_id]["error"][] = $error_message;
        $this->errorsReported++;
    }
    protected function storeSuccess($item_type, $item_id, $package_reference)
    {
        $this->exportLog[$item_type][$item_id]["reference"] = $package_reference;
        $this->itemsExported++;
    }
    protected function logPaymentItem($type, $reference_id, $status, $open_amount, $package_reference = "", $messages = [], $log_allowed = true)
    {
        return $this->logExportedItem($type, $reference_id, $status, $package_reference, $messages, $log_allowed, $open_amount);
    }
    protected function logExportedItem($type, $reference_id, $status, $package_reference = "", $messages = [], $log_allowed = true, $open_amount = NULL)
    {
        $reference_id = str_replace("SDD", "", $reference_id);
        if($status === "success" && !$package_reference && in_array($type, ["sddbatch", "debtor", "creditor", "invoice", "creditinvoice", "product"], true)) {
            $status = "error";
            $messages = [__("export accounting - exported item has no package reference")];
        }
        $params = ["ExportedAt" => ["RAW" => "NOW()"], "Package" => $this->package, "Type" => $type, "ReferenceID" => $reference_id, "PackageReference" => $package_reference, "Status" => $status, "Message" => !empty($messages) ? json_encode($messages) : "", "LastOpenAmount" => is_null($open_amount) ? ["RAW" => "NULL"] : $open_amount];
        if($package_reference !== false && $package_reference == "") {
            unset($params["PackageReference"]);
        }
        Database_Model::getInstance()->insert("HostFact_ExportHistory", $params)->onDuplicate($params)->execute();
        if($log_allowed === true && $type == "invoice" && $status == "success") {
            createLog($type, $reference_id, sprintf(__("export accounting - log exported to"), $this->versionInfo["name"]), [], false);
        }
        unset($_SESSION["export_menu_notification"]);
    }
    protected function truncateExportedItems()
    {
        Database_Model::getInstance()->delete("HostFact_ExportHistory")->where("Package", $this->package)->execute();
        unset($_SESSION["export_menu_notification"]);
    }
    public function saveStatistics($export_type, $filter, $filter_by = "", $min = false, $max = false)
    {
        $filters = [];
        $filters["Filter"] = $filter;
        if($filter_by) {
            $filters["FilterBy"] = $filter_by;
            $filters["Min"] = $min;
            $filters["Max"] = $max;
        }
        $stats = $this->getSettingsValue("statistics");
        $stats[$export_type]["export_lastdate"] = date("Y-m-d H:i:s");
        $stats[$export_type]["filters"] = $filters;
        $this->saveSettings("statistics", json_encode($stats));
    }
    public function getStatistics($include_errors = true)
    {
        $activated_since_date = $this->getSettingsValue("activated_since_date");
        $stats_dates_filters = $this->getSettingsValue("statistics");
        $result_array = [];
        $or_clausule_is_exported = [];
        if($include_errors === true) {
            $or_clausule_is_exported[] = ["HostFact_ExportHistory.Status", "error"];
        }
        $or_clausule_is_exported = array_merge($or_clausule_is_exported, [["HostFact_ExportHistory.ExportedAt", "0000-00-00 00:00:00"], ["HostFact_ExportHistory.ExportedAt", ["IS" => ["RAW" => "NULL"]]]]);
        $or_clausule_is_modifed = [["HostFact_ExportHistory.ExportedAt < `Modified`"]];
        foreach ($this->supported as $_key => $_is_supported) {
            if(!$_is_supported) {
            } else {
                $code_column = "";
                $selectArray = ["HostFact_ExportHistory.ExportedAt", "HostFact_ExportHistory.Status"];
                $result_array[$_key] = ["export_lastdate" => isset($stats_dates_filters[$_key]["export_lastdate"]) ? $stats_dates_filters[$_key]["export_lastdate"] : "", "export_needed" => []];
                if(isset($stats_dates_filters[$_key]["filters"])) {
                    $result_array[$_key]["filters"] = $stats_dates_filters[$_key]["filters"];
                }
                switch ($_key) {
                    case "debtor":
                        $selectArray[] = "HostFact_Debtors.id";
                        $selectArray[] = "HostFact_Debtors.DebtorCode";
                        $selectArray[] = "HostFact_Debtors.CompanyName";
                        $selectArray[] = "HostFact_Debtors.Initials";
                        $selectArray[] = "HostFact_Debtors.SurName";
                        $selectArray[] = "HostFact_Debtors.Modified";
                        $orWhere = array_merge($or_clausule_is_exported, $or_clausule_is_modifed);
                        $code_column = "DebtorCode";
                        Database_Model::getInstance()->get("HostFact_Debtors", $selectArray)->join("HostFact_ExportHistory", "HostFact_ExportHistory.`Type` = 'debtor' AND HostFact_ExportHistory.`ReferenceID` = HostFact_Debtors.`id` AND HostFact_ExportHistory.Package=:package")->bindValue(":package", $this->package)->where("HostFact_Debtors.Status", ["!=" => 9]);
                        break;
                    case "creditor":
                        $selectArray[] = "HostFact_Creditors.id";
                        $selectArray[] = "HostFact_Creditors.CreditorCode";
                        $selectArray[] = "HostFact_Creditors.CompanyName";
                        $selectArray[] = "HostFact_Creditors.Initials";
                        $selectArray[] = "HostFact_Creditors.SurName";
                        $selectArray[] = "HostFact_Creditors.Modified";
                        $orWhere = array_merge($or_clausule_is_exported, $or_clausule_is_modifed);
                        $code_column = "CreditorCode";
                        Database_Model::getInstance()->get("HostFact_Creditors", $selectArray)->join("HostFact_ExportHistory", "HostFact_ExportHistory.`Type` = 'creditor' AND HostFact_ExportHistory.`ReferenceID` = HostFact_Creditors.`id` AND HostFact_ExportHistory.Package=:package")->bindValue(":package", $this->package)->where("HostFact_Creditors.Status", ["!=" => 9]);
                        break;
                    case "invoice":
                        $selectArray[] = "HostFact_Invoice.id";
                        $selectArray[] = "HostFact_Invoice.InvoiceCode";
                        $selectArray[] = "HostFact_Invoice.Modified";
                        $selectArray[] = "HostFact_Invoice.Date";
                        $orWhere = $or_clausule_is_exported;
                        $code_column = "InvoiceCode";
                        Database_Model::getInstance()->get("HostFact_Invoice", $selectArray)->join("HostFact_ExportHistory", "HostFact_ExportHistory.`Type` = 'invoice' AND HostFact_ExportHistory.`ReferenceID` = HostFact_Invoice.`id` AND HostFact_ExportHistory.Package=:package")->bindValue(":package", $this->package)->where("HostFact_Invoice.Status", [">" => 1]);
                        break;
                    case "creditinvoice":
                        $selectArray[] = "HostFact_CreditInvoice.id";
                        $selectArray[] = "HostFact_CreditInvoice.CreditInvoiceCode";
                        $selectArray[] = "HostFact_CreditInvoice.Modified";
                        $selectArray[] = "HostFact_CreditInvoice.Date";
                        $orWhere = $or_clausule_is_exported;
                        $code_column = "CreditInvoiceCode";
                        Database_Model::getInstance()->get("HostFact_CreditInvoice", $selectArray)->join("HostFact_ExportHistory", "HostFact_ExportHistory.`Type` = 'creditinvoice' AND HostFact_ExportHistory.`ReferenceID` = HostFact_CreditInvoice.`id` AND HostFact_ExportHistory.Package=:package")->bindValue(":package", $this->package);
                        break;
                    case "product":
                        $selectArray[] = "HostFact_Products.id";
                        $selectArray[] = "HostFact_Products.ProductCode";
                        $selectArray[] = "HostFact_Products.ProductName";
                        $selectArray[] = "HostFact_Products.Modified";
                        $orWhere = array_merge($or_clausule_is_exported, $or_clausule_is_modifed);
                        $code_column = "ProductCode";
                        Database_Model::getInstance()->get("HostFact_Products", $selectArray)->join("HostFact_ExportHistory", "HostFact_ExportHistory.`Type` = 'product' AND HostFact_ExportHistory.`ReferenceID` = HostFact_Products.`id` AND HostFact_ExportHistory.Package=:package")->bindValue(":package", $this->package)->where("HostFact_Products.Status", ["!=" => 9]);
                        if($code_column) {
                            Database_Model::getInstance()->orderBy("IF(SUBSTRING(`" . $code_column . "`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(`" . $code_column . "`,1,1))", "ASC")->orderBy("LENGTH(`" . $code_column . "`)", "ASC")->orderBy("" . $code_column . "", "ASC");
                        }
                        $result = Database_Model::getInstance()->orWhere($orWhere)->execute();
                        if($result) {
                            foreach ($result as $_result) {
                                if(!isset($_result->Date) || $activated_since_date["start_date"] <= $_result->Date) {
                                    $result_array[$_key]["export_needed"][$_result->id] = $_result;
                                } else {
                                    $result_array[$_key]["export_older_items"][$_result->id] = $_result;
                                }
                            }
                        }
                        break;
                }
            }
        }
        if(isset($this->supported["sddbatch"]) && $this->supported["sddbatch"]) {
            $status = "downloaded";
            $directdebit = new directdebit();
            $list_batches = $directdebit->listBatches($status, "BatchID", "ASC");
            $batches_array = [];
            foreach ($list_batches["processing"] as $_batch) {
                $batches_array[str_replace("SDD", "", $_batch["BatchID"])] = $_batch;
            }
            foreach ($list_batches["archive"] as $_batch) {
                $batches_array[str_replace("SDD", "", $_batch["BatchID"])] = $_batch;
            }
            if(!empty($batches_array)) {
                Database_Model::getInstance()->get("HostFact_ExportHistory", ["*"])->where("Package", $this->package)->where("Type", "sddbatch")->where("ReferenceID", ["IN" => array_keys($batches_array)]);
                if($include_errors === true) {
                    Database_Model::getInstance()->where("Status", ["IN" => ["success", "ignore"]]);
                } else {
                    Database_Model::getInstance()->where("Status", ["IN" => ["success", "ignore", "error"]]);
                }
                $result = Database_Model::getInstance()->execute();
                if($result) {
                    foreach ($result as $_batch) {
                        $batches_array[$_batch->ReferenceID]["ExportedAt"] = $_batch->ExportedAt;
                        $batches_array[$_batch->ReferenceID]["PackageReference"] = $_batch->PackageReference;
                    }
                }
                foreach ($batches_array as $_key => $_item) {
                    if(!isset($_item["ExportedAt"]) && $activated_since_date["activation_date"] <= $_item["DownloadDate"]) {
                        $result_array["sddbatch"]["export_needed"][$_key] = (object) $_item;
                    } elseif(isset($_item["ExportedAt"])) {
                        $result_array["sddbatch"]["export_older_items"][$_key] = (object) $_item;
                    }
                }
            }
        }
        return $result_array;
    }
    public function hasAnyItems($export_type)
    {
        switch ($export_type) {
            case "debtor":
                $result = Database_Model::getInstance()->getOne("HostFact_Debtors", "id")->execute();
                break;
            case "invoice":
                $result = Database_Model::getInstance()->getOne("HostFact_Invoice", "id")->where("Status", [">" => "1"])->execute();
                break;
            case "creditor":
                $result = Database_Model::getInstance()->getOne("HostFact_Creditors", "id")->execute();
                break;
            case "creditinvoice":
                $result = Database_Model::getInstance()->getOne("HostFact_CreditInvoice", "id")->execute();
                break;
            case "product":
                $result = Database_Model::getInstance()->getOne("HostFact_Products", "id")->execute();
                break;
            case "sddbatch":
                $result = Database_Model::getInstance()->getOne("HostFact_SDD_Batches", "BatchID")->where("Status", ["IN" => ["downloaded", "processed"]])->execute();
                return $result === false ? false : true;
                break;
            default:
                return false;
        }
    }
    public function getPackageReference($export_type, $reference)
    {
        $reference = str_replace("SDD", "", $reference);
        $result = Database_Model::getInstance()->getOne("HostFact_ExportHistory", ["PackageReference"])->where("Package", $this->package)->where("Type", $export_type)->where("ReferenceID", $reference)->execute();
        if($result && $result->PackageReference) {
            return $result->PackageReference;
        }
        return false;
    }
    public function setGlobalErrors($errors_export_types)
    {
        $globalErrors = $this->getSettingsValue("globalErrors");
        $globalErrors["date"] = date("Y-m-d H:i:s");
        $globalErrors["errors"] = isset($globalErrors["errors"]) && is_array($globalErrors["errors"]) ? array_merge($globalErrors["errors"], $errors_export_types) : $errors_export_types;
        $this->saveSettings("globalErrors", json_encode($globalErrors));
        unset($_SESSION["export_menu_notification"]);
    }
    public function removeGlobalErrors($type = "")
    {
        $globalErrors = $this->getSettingsValue("globalErrors");
        if($type && isset($globalErrors["errors"])) {
            unset($globalErrors["errors"]["connection"]);
            unset($globalErrors["errors"]["authentication"]);
            unset($globalErrors["errors"][$type]);
            if(empty($globalErrors["errors"])) {
                $globalErrors = "";
            }
        } else {
            $globalErrors = "";
        }
        $this->saveSettings("globalErrors", is_array($globalErrors) ? json_encode($globalErrors) : "");
        unset($_SESSION["export_menu_notification"]);
    }
    public function setAdministrationCounter()
    {
        $accounting_errors = 0;
        $result = Database_Model::getInstance()->getOne("HostFact_ExportHistory", "COUNT(`id`) as Count")->where("Status", "error")->where("Package", $this->package)->execute();
        if($result && 0 < $result->Count) {
            $accounting_errors += $result->Count;
        }
        $globalErrors = $this->getSettingsValue("globalErrors");
        if(isset($globalErrors["errors"])) {
            $accounting_errors += count($globalErrors["errors"]);
        }
        return $accounting_errors;
    }
    public function getManualActions($type = "", $since = false)
    {
        Database_Model::getInstance()->get("HostFact_ExportHistory")->where("Status", "error")->where("Package", $this->package);
        if($type) {
            Database_Model::getInstance()->where("Type", $type);
        } else {
            $supported_types = [];
            foreach ($this->supported as $_k => $_v) {
                if($_v) {
                    $supported_types[] = $_k;
                }
            }
            Database_Model::getInstance()->where("Type", ["IN" => $supported_types]);
        }
        if($since !== false) {
            Database_Model::getInstance()->where("ExportedAt", [">=" => $since]);
        }
        $result = Database_Model::getInstance()->orderBy("ExportedAt", "DESC")->execute();
        $manual_actions = [];
        foreach ($result as $_key => $_item) {
            $_item->Message = json_decode($_item->Message, true);
            $_item->Suggestion = $this->getSuggestion($_item->Type, $_item->ReferenceID, $_item->Message);
            if(!$_item->Suggestion) {
                $this->storeSuggestion($_item->Type, $_item->Message);
            }
            $manual_actions[] = $_item;
        }
        return $manual_actions;
    }
    public function getPaidInHostFactDiffs()
    {
        Database_Model::getInstance()->get("HostFact_ExportHistory")->where("Status", "paid_diff")->where("Package", $this->package);
        $result = Database_Model::getInstance()->orderBy("ExportedAt", "DESC")->execute();
        $diff_items = [];
        foreach ($result as $_key => $_item) {
            $_item->Message = json_decode($_item->Message, true);
            $diff_items[] = $_item;
        }
        return $diff_items;
    }
    public function getManualAction($export_type, $reference_id)
    {
        $result = Database_Model::getInstance()->getOne("HostFact_ExportHistory")->where("Status", ["IN" => ["error", "paid_diff"]])->where("Package", $this->package)->where("Type", $export_type)->where("ReferenceID", $reference_id)->execute();
        if($result) {
            $result->Message = json_decode($result->Message, true);
            $result->Suggestion = $this->getSuggestion($result->Type, $result->ReferenceID, $result->Message);
            if(!$result->Suggestion) {
                $this->storeSuggestion($result->Type, $result->Message);
            }
        }
        return $result;
    }
    public function ignoreManualAction($export_type, $reference_id)
    {
        Database_Model::getInstance()->update("HostFact_ExportHistory", ["Status" => "ignore"])->where("Status", ["IN" => ["error", "paid_diff"]])->where("Package", $this->package)->where("Type", $export_type)->where("ReferenceID", $reference_id)->execute();
        unset($_SESSION["export_menu_notification"]);
    }
    protected function getSuggestion($item_type, $item_id, $message_array)
    {
        return "";
    }
    protected function getProductGroupForProduct()
    {
        $result = Database_Model::getInstance()->get("HostFact_Products", ["HostFact_Products.ProductCode", "HostFact_GroupRelations.Group"])->join("HostFact_GroupRelations", "HostFact_GroupRelations.`Reference` = HostFact_Products.`id`")->where("HostFact_GroupRelations.Type", "product")->where("HostFact_Products.Status", ["!=" => 9])->execute();
        $product_x_groups = [];
        foreach ($result as $row) {
            $product_x_groups[$row->ProductCode][] = $row->Group;
        }
        return $product_x_groups;
    }
    protected function getCreditorGroupForCreditor()
    {
        $result = Database_Model::getInstance()->get("HostFact_Creditors", ["HostFact_Creditors.CreditorCode", "HostFact_GroupRelations.Group"])->join("HostFact_GroupRelations", "HostFact_GroupRelations.`Reference` = HostFact_Creditors.`id`")->where("HostFact_GroupRelations.Type", "creditor")->where("HostFact_Creditors.Status", ["!=" => 9])->execute();
        $creditor_x_groups = [];
        foreach ($result as $row) {
            $creditor_x_groups[$row->CreditorCode][] = $row->Group;
        }
        return $creditor_x_groups;
    }
    public function getProductGroups()
    {
        $result = Database_Model::getInstance()->get("HostFact_Group")->where("Type", "product")->where("Status", ["!=" => "9"])->orderBy("GroupName", "ASC")->asArray()->execute();
        $list = [];
        foreach ($result as $row) {
            $list[$row["id"]] = $row;
        }
        return $list;
    }
    public function getCreditorGroups()
    {
        $result = Database_Model::getInstance()->get("HostFact_Group")->where("Type", "creditor")->where("Status", ["!=" => "9"])->orderBy("GroupName", "ASC")->asArray()->execute();
        $list = [];
        foreach ($result as $row) {
            $list[$row["id"]] = $row;
        }
        return $list;
    }
    protected function getTaxRuleHash($invoice_id, $taxtype = "line")
    {
        if(0 < $invoice_id) {
            $invoice = new invoice();
            $debtor = new debtor();
            $invoice->Identifier = $invoice_id;
            if(!$invoice->show()) {
                return false;
            }
            $debtor->Identifier = $invoice->Debtor;
            if(!$debtor->show()) {
                return false;
            }
            if($debtor->TaxableSetting == "yes") {
                return false;
            }
            global $company;
            global $array_country;
            $debtor_country = $invoice->Country;
            $result_rules = Database_Model::getInstance()->get("HostFact_Settings_TaxRules", ["CountryCode", "StateCode", "TaxLevel1", "TaxLevel2", "Compound", "Restriction"])->where("CountryCode", $debtor_country)->execute();
            if($result_rules && is_array($result_rules)) {
                foreach ($result_rules as $v) {
                    $v = (array) $v;
                    if(($v["StateCode"] == "all" || $v["StateCode"] == "same" && $invoice->State == $company->State || $v["StateCode"] == "other" && $invoice->State != $company->State) && ($v["Restriction"] == "all" || $v["Restriction"] == "company" && $invoice->CompanyName || $v["Restriction"] == "company_vat" && $invoice->CompanyName && $invoice->TaxNumber || $v["Restriction"] == "individual" && (!$invoice->CompanyName || !$invoice->TaxNumber)) && $taxtype == "line" && !is_null($v["TaxLevel1"])) {
                        if($debtor->TaxableSetting == "no" && 0 < $v["TaxLevel1"]) {
                        } else {
                            $v["TaxLevel1"] = number_format($v["TaxLevel1"], 6, ".", "");
                            return md5(json_encode($v));
                        }
                    }
                }
            }
            $eu_countries = $this->eu_countries;
            $result_rules = Database_Model::getInstance()->get("HostFact_Settings_TaxRules", ["CountryCode", "StateCode", "TaxLevel1", "TaxLevel2", "Compound", "Restriction"])->where("CountryCode", ["IN" => ["all", "other", "otherEU", "nonEU"]])->execute();
            if($result_rules && is_array($result_rules)) {
                foreach ($result_rules as $v) {
                    $v = (array) $v;
                    if(($v["CountryCode"] == "all" || $v["CountryCode"] == "other" && $invoice->Country != $debtor_country || $v["CountryCode"] == "otherEU" && in_array($debtor_country, $eu_countries) && $company->Country != $debtor_country || $v["CountryCode"] == "nonEU" && !in_array($debtor_country, $eu_countries)) && ($v["StateCode"] == "all" || $v["StateCode"] == "same" && $invoice->State == $company->State || $v["StateCode"] == "other" && $invoice->State != $company->State) && ($v["Restriction"] == "all" || $v["Restriction"] == "company" && $invoice->CompanyName || $v["Restriction"] == "company_vat" && $invoice->CompanyName && $invoice->TaxNumber || $v["Restriction"] == "individual" && (!$invoice->CompanyName || !$invoice->TaxNumber)) && $taxtype == "line" && !is_null($v["TaxLevel1"])) {
                        if($debtor->TaxableSetting == "no" && 0 < $v["TaxLevel1"]) {
                        } else {
                            $v["TaxLevel1"] = number_format($v["TaxLevel1"], 6, ".", "");
                            return md5(json_encode($v));
                        }
                    }
                }
            }
            return false;
        } else {
            return false;
        }
    }
    protected function getTaxRules()
    {
        $taxrules = [];
        $result = Database_Model::getInstance()->get("HostFact_Settings_TaxRules", ["CountryCode", "StateCode", "TaxLevel1", "TaxLevel2", "Compound", "Restriction"])->execute();
        if($result && is_array($result)) {
            foreach ($result as $_taxrule) {
                $_taxrule->TaxLevel1 = $_taxrule->TaxLevel1 !== NULL ? number_format($_taxrule->TaxLevel1, 6, ".", "") : NULL;
                $hash = md5(json_encode($_taxrule));
                $taxrules[$hash] = $_taxrule;
            }
        }
        return $taxrules;
    }
    protected function getTaxRulesSettings($taxRules_info, $accounts, $account_options, $vatcode_options, $balance_options)
    {
        if(!isset($account_options["profit"])) {
            $account_options = ["profit" => $account_options];
        }
        global $array_country;
        global $array_taxpercentages;
        $taxrules = $this->getTaxRules();
        if(!empty($taxrules)) {
            foreach ($taxrules as $_taxhash => $_taxrule) {
                switch ($_taxrule->CountryCode) {
                    case "all":
                        $countryname = __("taxrules all countries");
                        break;
                    case "other":
                        $countryname = __("taxrules other countries");
                        break;
                    case "otherEU":
                        $countryname = __("taxrules other EU countries");
                        break;
                    case "nonEU":
                        $countryname = __("taxrules non EU countries");
                        break;
                    default:
                        $countryname = isset($array_country[$_taxrule->CountryCode]) ? $array_country[$_taxrule->CountryCode] : "";
                        switch ($_taxrule->Restriction) {
                            case "all":
                                $restriction_name = __("taxrules all debtors");
                                break;
                            case "company":
                                $restriction_name = __("taxrules all companies");
                                break;
                            case "company_vat":
                                $restriction_name = __("taxrules companies with taxnumber");
                                break;
                            case "individual":
                                $restriction_name = __("taxrules all individuals");
                                break;
                            default:
                                $help_me_fill = "";
                                if($_taxrule->CountryCode == "otherEU" && $_taxrule->Restriction == "company_vat") {
                                    $help_me_fill = "EU";
                                } elseif($_taxrule->CountryCode == "nonEU" && $_taxrule->Restriction == "all") {
                                    $help_me_fill = "NonEU";
                                }
                                echo "\t\t\t\t<div class=\"width_230px ellipsis\" style=\"clear:both;float: left;line-height:16px;margin-top:2px\">\n\t\t\t\t\t<strong class=\"strong\">";
                                echo $countryname;
                                echo "</strong><br />\n\t\t\t\t\t<span class=\"c_gray\">";
                                echo $restriction_name;
                                echo "</span>\n\t\t\t\t</div>\n\n\t\t\t\t<div class=\"input_column\" style=\"float:left;\">\n\t\t\t\t\t<select name=\"taxrules[";
                                echo $_taxhash;
                                echo "][revenue]\" class=\"width_310px margin_right_20px\">\n\t\t\t\t\t\t<option value=\"\">";
                                echo __("please choose");
                                echo "</option>\n\t\t\t\t\t\t";
                                if(!isset($taxRules_info["value"][$_taxhash]["revenue"]) && $help_me_fill) {
                                    $taxRules_info["value"][$_taxhash]["revenue"] = $accounts["value"]["default"]["revenue" . $help_me_fill]["id"];
                                }
                                foreach ($account_options as $_group_type => $account_options_items) {
                                    if(!empty($account_options_items)) {
                                        if(1 < count($account_options)) {
                                            echo "<optgroup label=\"";
                                            echo __("export accounting - ledger account group " . $_group_type);
                                            echo "\">";
                                        }
                                        foreach ($account_options_items as $_key => $_value) {
                                            echo "\t\t\t\t\t\t\t\t\t<option value=\"";
                                            echo $_key;
                                            echo "\" ";
                                            if(isset($taxRules_info["value"][$_taxhash]["revenue"]) && (string) $_key === (string) $taxRules_info["value"][$_taxhash]["revenue"]) {
                                                echo " selected=\"selected\"";
                                            }
                                            echo ">";
                                            echo $_key . " - " . $_value["title"];
                                            echo "</option>\n\t\t\t\t\t\t\t\t\t";
                                        }
                                        if(1 < count($account_options)) {
                                            echo "</optgroup>";
                                        }
                                    }
                                }
                                echo "\t\t\t\t\t</select>";
                                if(!empty($vatcode_options)) {
                                    echo "<select name=\"taxrules[";
                                    echo $_taxhash;
                                    echo "][taxcode]\" class=\"width_310px margin_right_20px\">\n\t\t\t\t\t\t<option value=\"\">";
                                    echo __("please choose");
                                    echo "</option>\n\t\t\t\t\t\t";
                                    $selected_vat_code = "";
                                    if(!isset($taxRules_info["value"][$_taxhash]["taxcode"]) && $help_me_fill) {
                                        $taxRules_info["value"][$_taxhash]["taxcode"] = $accounts["value"]["default"]["taxcode" . $help_me_fill]["id"];
                                    }
                                    foreach ($vatcode_options as $_key => $_value) {
                                        if((string) $_key === (string) $taxRules_info["value"][$_taxhash]["taxcode"]) {
                                            $selected_vat_code = $_key;
                                        }
                                        echo "\t\t\t\t\t\t\t<option value=\"";
                                        echo $_key;
                                        echo "\" ";
                                        if((string) $_key === (string) $selected_vat_code) {
                                            echo " selected=\"selected\"";
                                        }
                                        echo ">";
                                        echo isset($_value["select_title"]) ? $_value["select_title"] : $_key . " - " . $_value["title"];
                                        echo "</option>\n\t\t\t\t\t\t\t";
                                    }
                                    echo "\t\t\t\t\t</select>";
                                }
                                if(!empty($balance_options)) {
                                    echo "<select name=\"taxrules[";
                                    echo $_taxhash;
                                    echo "][tax]\" class=\"width_310px\">\n\t\t\t\t\t\t<option value=\"\">";
                                    echo __("please choose");
                                    echo "</option>\n\t\t\t\t\t\t";
                                    if(!isset($taxRules_info["value"][$_taxhash]["tax"]) && $help_me_fill) {
                                        $taxRules_info["value"][$_taxhash]["tax"] = $accounts["value"]["default"]["tax" . $help_me_fill]["id"];
                                    }
                                    foreach ($balance_options as $_key => $_value) {
                                        if(!$taxRules_info["value"][$_taxhash]["tax"] && $selected_vat_code && isset($vatcode_options[$selected_vat_code]["account"])) {
                                            $taxRules_info["value"][$_taxhash]["tax"] = (string) $vatcode_options[$selected_vat_code]["account"];
                                        }
                                        echo "\t\t\t\t\t\t\t<option value=\"";
                                        echo $_key;
                                        echo "\" ";
                                        if((string) $_key === (string) $taxRules_info["value"][$_taxhash]["tax"]) {
                                            echo " selected=\"selected\"";
                                        }
                                        echo ">";
                                        echo $_key . " - " . $_value["title"];
                                        echo "</option>\n\t\t\t\t\t\t\t";
                                    }
                                    echo "\t\t\t\t\t\t</select>\n\t\t\t\t\t\t";
                                }
                                echo "\t\t\t\t</div>\n\t\t\t\t<br clear=\"both\" />\n\t\t\t\t";
                        }
                }
            }
        }
    }
    public function getVersionInformation()
    {
        $version = [];
        if(file_exists("3rdparty/export/" . $this->package . "/version.php")) {
            include "3rdparty/export/" . $this->package . "/version.php";
        }
        return $version;
    }
    public function startTrial()
    {
        global $server_addr;
        $url = "https://www.hostfact.nl/hosting/infomodule.php?action=trial_module";
        $url .= "&module=" . urlencode($this->package);
        $url .= "&license=" . urlencode(encrypt(LICENSE));
        $url .= "&licensemd5=" . md5(trim(LICENSE));
        $url .= "&ip=" . urlencode($server_addr);
        $result = getContent($url);
        $result = explode("|", $result);
        if($result[1] == "OK") {
            return true;
        }
        if($result[1] == "EXPIRED") {
            $this->Error[] = "U heeft al eerder een proefperiode voor " . $this->versionInfo["name"] . " aangevraagd. Neem daarom contact met ons op via info@hostfact.nl";
            return false;
        }
        $this->Error[] = "Er is een fout opgetreden tijdens het aanmaken van uw proefperiode voor " . $this->versionInfo["name"] . ". Neem daarom contact met ons op via info@hostfact.nl";
        return false;
    }
    public function endSubscription($message = "")
    {
        checkRight(U_EXPORT_EDIT);
        $url = "https://www.hostfact.nl/hosting/infomodule.php?action=end_module";
        $url .= "&module=" . urlencode($this->package);
        $url .= "&license=" . urlencode(encrypt(LICENSE));
        $url .= "&licensemd5=" . md5(trim(LICENSE));
        $url .= "&message=" . urlencode(base64_encode($message));
        $result = getContent($url);
        $result = explode("|", $result);
        if($result[1] == "OK") {
            Database_Model::getInstance()->delete("HostFact_ExportSettings")->where("package", $this->package)->execute();
            Database_Model::getInstance()->delete("HostFact_ExportHistory")->where("package", strtolower($this->package))->execute();
            unset($_SESSION[md5($this->package)]);
            $this->Success[] = "Uw " . $this->versionInfo["name"] . " module is beëindigd.";
            return true;
        }
        $this->Error[] = "Uw " . $this->versionInfo["name"] . " module kan niet automatisch worden beëindigd. Neem daarom contact met ons op via info@hostfact.nl";
        return false;
    }
    public function orderSubscription()
    {
        $url = "https://www.hostfact.nl/hosting/infomodule.php?action=order_module";
        $url .= "&module=" . urlencode($this->package);
        $url .= "&license=" . urlencode(encrypt(LICENSE));
        $url .= "&licensemd5=" . md5(trim(LICENSE));
        $result = getContent($url);
        $result = explode("|", $result);
        if($result[1] == "OK") {
            return true;
        }
        $this->Error[] = "Er is een fout opgetreden tijdens het bestellen van de " . $this->versionInfo["name"] . " module. Neem daarom contact met ons op via info@hostfact.nl";
        return false;
    }
    public function checkLicense()
    {
        global $server_addr;
        if(isset($_SESSION[md5($this->package)]) && ($_SESSION[md5($this->package)] == "OK" || $_SESSION[md5($this->package)] == "TRIAL")) {
            $this->LicenseStatus = $_SESSION[md5($this->package)];
            return true;
        }
        $url = "https://www.hostfact.nl/hosting/infomodule.php?action=validate_module";
        $url .= "&module=" . urlencode($this->package);
        $url .= "&license=" . urlencode(encrypt(LICENSE));
        $url .= "&licensemd5=" . md5(trim(LICENSE));
        $url .= "&ip=" . urlencode($server_addr);
        $result = getContent($url);
        if(substr($result, 0, 8) != "HOSTFACT") {
            return true;
        }
        $result = explode("|", $result);
        if($result[1] == "OK") {
            $_SESSION[md5($this->package)] = "OK";
            $this->LicenseStatus = "OK";
            return true;
        }
        if($result[1] == "TRIAL") {
            $_SESSION[$this->package . "-trialdate"] = rewrite_date_db2site($result[2]);
            $_SESSION[md5($this->package)] = "TRIAL";
            $this->LicenseStatus = "TRIAL";
            return true;
        }
        if($result[1] == "EXPIRED") {
            $_SESSION[md5($this->package)] = "EXPIRED";
            $this->LicenseStatus = "EXPIRED";
            list($_SESSION[$this->package . "-expired"]["ExpiredDaysTillSubscription"], $_SESSION[$this->package . "-expired"]["ExpiredMessage"], $_SESSION[$this->package . "-expired"]["ExpiredSubscriptionStart"]) = $result;
            return false;
        }
        $this->Error[] = "Uw licentie voor " . $this->versionInfo["name"] . " is geblokkeerd. Neem daarom contact met ons op via info@hostfact.nl";
        return false;
    }
    public function getLicenseStatus()
    {
        return $this->LicenseStatus;
    }
    protected function generalErrorLogging($reference2, $reference3, $error_msg, $request = "", $response = "")
    {
    }
    protected function storeSuggestion($type, $message_array)
    {
    }
    protected function setErrorAndType($error_type, $error_message)
    {
        $this->Error[] = $error_message;
        $known_error_types = ["none", "connection", "authentication", "export"];
        if(!in_array($error_type, $known_error_types)) {
            exit("Invalid error type " . $error_type);
        }
        if(!$this->ErrorType || array_search($error_type, $known_error_types) < array_search($this->ErrorType, $known_error_types)) {
            $this->ErrorType = $error_type;
        }
    }
    protected function setSupported($array)
    {
        $this->supported = $array;
        if(!defined("SCRIPT_IS_CRONJOB")) {
            if(!checkRight("U_CREDITOR_INVOICE_SHOW", false)) {
                $this->supported["creditor"] = false;
                $this->supported["creditinvoice"] = false;
            }
            if(!checkRight("U_PRODUCT_SHOW", false)) {
                $this->supported["product"] = false;
            }
            if(!checkRight("U_DEBTOR_SHOW", false)) {
                $this->supported["debtor"] = false;
            }
            if(!checkRight("U_INVOICE_SHOW", false)) {
                $this->supported["invoice"] = false;
            }
        }
    }
    public function getPackages($active_only = false)
    {
        if($active_only === true) {
            $status_per_package = [];
            $package_status = Database_Model::getInstance()->get("HostFact_ExportSettings", ["package", "value"])->where("name", "status")->groupBy("package")->execute();
            if($package_status) {
                foreach ($package_status as $_status) {
                    $status_per_package[$_status->package] = json_decode($_status->value);
                }
            }
        }
        $packages = [];
        $result = Database_Model::getInstance()->get("HostFact_ExportSettings", ["package"])->where("name", "statistics")->groupBy("package")->execute();
        $today = new DateTime("-1 week");
        if(!empty($result)) {
            foreach ($result as $row) {
                if($active_only !== true || !isset($status_per_package[$row->package])) {
                    $packages[] = $row->package;
                } elseif($status_per_package[$row->package]->status == "active") {
                    $packages[] = $row->package;
                } elseif(in_array($status_per_package[$row->package]->status, ["trial", "trial_expired"])) {
                    $expire_date = new DateTime($status_per_package[$row->package]->trial_end_date);
                    if($today < $expire_date) {
                        $packages[] = $row->package;
                    }
                }
            }
            return $packages;
        } else {
            return false;
        }
    }
    public function importPaymentsNew($remaining_ids = false)
    {
        $prev_failed_ids = [];
        if($remaining_ids === false) {
            $result = $this->getManualActions("payment_invoice");
            if($result) {
                foreach ($result as $_item) {
                    $prev_failed_ids[] = $_item->ReferenceID;
                }
            }
        }
        $checked_transactions_for_invoice = [];
        if(!method_exists($this, "listOpenTransactions")) {
            return false;
        }
        try {
            $open_transactions = $this->listOpenTransactions();
            if($open_transactions === false) {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
        foreach ($open_transactions as $k => $v) {
            if((string) $v["PackageReference"] === "") {
                $this->Error[] = __("retrieving payments went wrong, incorrect data");
                return false;
            }
            if(isEmptyFloat(round($v["AmountOpen"], 2))) {
                unset($open_transactions[$k]);
            }
        }
        Database_Model::getInstance()->get("HostFact_Invoice", ["HostFact_Invoice.id", "HostFact_ExportHistory.PackageReference"])->join("HostFact_ExportHistory", "HostFact_ExportHistory.`Type` = 'invoice' AND HostFact_ExportHistory.`ReferenceID` = HostFact_Invoice.`id` AND HostFact_ExportHistory.Package=:package")->bindValue(":package", $this->package);
        if($remaining_ids && 0 < count($remaining_ids)) {
            Database_Model::getInstance()->where("HostFact_Invoice.id", ["IN" => $remaining_ids]);
        } elseif($prev_failed_ids && 0 < count($prev_failed_ids)) {
            Database_Model::getInstance()->orWhere([["HostFact_Invoice.Status", ["IN" => [2, 3]]], ["HostFact_Invoice.id", ["IN" => $prev_failed_ids]]]);
        } else {
            Database_Model::getInstance()->where("HostFact_Invoice.Status", ["IN" => [2, 3]]);
        }
        $open_invoices = Database_Model::getInstance()->execute();
        foreach ($open_invoices as $_invoice) {
            if(!$_invoice->PackageReference) {
            } else {
                $invoice_object = new invoice();
                $invoice_object->Identifier = $_invoice->id;
                $invoice_object->show();
                $payable_amount = round($invoice_object->AmountIncl - $invoice_object->AmountPaid, 2);
                switch ($invoice_object->Status) {
                    case 2:
                        $payable_amount = round($invoice_object->AmountIncl, 2);
                        break;
                    case 4:
                        $payable_amount = round(0, 2);
                        break;
                    default:
                        $mark_as_paid = false;
                        $payment_correction = false;
                        $has_open_amount_difference = false;
                        $payment_exporthistory_log = Database_Model::getInstance()->getOne("HostFact_ExportHistory", ["Status", "LastOpenAmount"])->where("Type", "payment_invoice")->where("Package", $this->package)->where("ReferenceID", $invoice_object->Identifier)->execute();
                        $previous_open_amount = $payment_exporthistory_log && !is_null($payment_exporthistory_log->LastOpenAmount) ? round($payment_exporthistory_log->LastOpenAmount, 2) : NULL;
                        $new_open_amount = 0;
                        if(isset($open_transactions[$_invoice->PackageReference])) {
                            $transaction_open_amount = round($open_transactions[$_invoice->PackageReference]["AmountOpen"], 2);
                            $new_open_amount = $transaction_open_amount;
                            unset($open_transactions[$_invoice->PackageReference]);
                            if($payable_amount == $transaction_open_amount) {
                                $this->logPaymentItem("payment_invoice", $invoice_object->Identifier, "success", $new_open_amount);
                            } elseif($invoice_object->Authorisation == "yes" && $invoice_object->SDDBatchID) {
                                $has_open_amount_difference = true;
                            } else {
                                $credit_invoices = Database_Model::getInstance()->get("HostFact_Invoice", ["InvoiceCode", "AmountIncl"])->where("CorrespondingInvoice", $invoice_object->Identifier)->execute();
                                if(!empty($credit_invoices)) {
                                    $total_credit_invoices_amount = array_sum(array_column($credit_invoices, "AmountIncl"));
                                    if($payable_amount + -1 * $total_credit_invoices_amount == $transaction_open_amount) {
                                    } elseif(0 <= $invoice_object->AmountIncl && $transaction_open_amount < $payable_amount || $invoice_object->AmountIncl < 0 && $payable_amount < $transaction_open_amount) {
                                        $payment_correction = true;
                                    } else {
                                        $has_open_amount_difference = true;
                                    }
                                } elseif(0 <= $invoice_object->AmountIncl && $transaction_open_amount < $payable_amount || $invoice_object->AmountIncl < 0 && $payable_amount < $transaction_open_amount) {
                                    $payment_correction = true;
                                } else {
                                    $has_open_amount_difference = true;
                                }
                            }
                        } elseif($invoice_object->Status == 4) {
                            $this->logPaymentItem("payment_invoice", $invoice_object->Identifier, "success", 0);
                        } elseif($invoice_object->Authorisation == "yes" && $invoice_object->SDDBatchID) {
                            if(0 < $payable_amount) {
                                $directdebit = new directdebit();
                                $batch_info = $directdebit->getBatchInfo($invoice_object->SDDBatchID);
                                if(in_array($batch_info->Status, ["draft", "downloadable"])) {
                                    $directdebit = new directdebit();
                                    $directdebit->removeDirectDebitFromInvoiceByBatchAndInvoiceID($invoice_object->SDDBatchID, $invoice_object->Identifier);
                                    $mark_as_paid = true;
                                } elseif(date("Y-m-d") < $batch_info->Date) {
                                } else {
                                    $invoice_object->markaspaid($batch_info->Date);
                                    $invoice_object->checkAuto();
                                }
                            }
                        } else {
                            $mark_as_paid = true;
                        }
                        $msg = [];
                        $log_status = "success";
                        if(is_null($previous_open_amount) || $previous_open_amount != $new_open_amount) {
                            if($mark_as_paid === true) {
                                try {
                                    if($this->invoiceExists($_invoice->PackageReference)) {
                                        $invoice_object->updateOpenAmountViaPackage($new_open_amount, $this->versionInfo["name"]);
                                    } else {
                                        $msg[] = __("export accounting - payment transactions - not found in accounting package");
                                        $log_status = "error";
                                    }
                                } catch (Exception $e) {
                                    return false;
                                }
                            } elseif($payment_correction === true) {
                                $invoice_object->updateOpenAmountViaPackage($new_open_amount, $this->versionInfo["name"]);
                            } elseif($has_open_amount_difference === true) {
                                $msg[] = __("export accounting - payment transactions - mismatch paid amounts - hostfact x - accounting package y", ["amount_hf" => money($payable_amount), "amount_acc" => money($new_open_amount)]);
                                $log_status = "error";
                                $this->errorsReported++;
                            }
                            $this->Error = array_merge($this->Error, $invoice_object->Error);
                            $this->Warning = array_merge($this->Warning, $invoice_object->Warning);
                            $this->Success = array_merge($this->Success, $invoice_object->Success);
                            $this->logPaymentItem("payment_invoice", $invoice_object->Identifier, $log_status, $new_open_amount, "", $msg);
                        } elseif(!is_null($previous_open_amount) && $previous_open_amount == $new_open_amount) {
                            if(!empty($payment_exporthistory_log->Status) && $payment_exporthistory_log->Status == "ignore") {
                            } elseif($mark_as_paid === true) {
                                try {
                                    if($this->invoiceExists($_invoice->PackageReference)) {
                                        $msg[] = __("export accounting - payment transactions - unpaid in software, paid in accounting package");
                                    } else {
                                        $msg[] = __("export accounting - payment transactions - not found in accounting package");
                                    }
                                } catch (Exception $e) {
                                    return false;
                                }
                                $log_status = "error";
                            } elseif($has_open_amount_difference === true) {
                                $msg[] = __("export accounting - payment transactions - mismatch paid amounts - hostfact x - accounting package y", ["amount_hf" => money($payable_amount), "amount_acc" => money($new_open_amount)]);
                                $log_status = "error";
                            }
                            if($log_status == "error") {
                                $this->logPaymentItem("payment_invoice", $invoice_object->Identifier, $log_status, $new_open_amount, "", $msg);
                                $this->errorsReported++;
                            }
                        }
                        $checked_transactions_for_invoice[] = $invoice_object->Identifier;
                }
            }
        }
        $invoice_by_package_reference = [];
        if(0 < count($open_transactions)) {
            $_invoice_list = Database_Model::getInstance()->get(["HostFact_Invoice", "HostFact_ExportHistory"], ["HostFact_ExportHistory.PackageReference", "HostFact_Invoice.id", "HostFact_Invoice.PaymentMethod", "HostFact_Invoice.TransactionID", "HostFact_Invoice.Paid", "HostFact_Invoice.Status"])->where("HostFact_ExportHistory.Type", "invoice")->where("HostFact_ExportHistory.`ReferenceID` = HostFact_Invoice.`id`")->where("HostFact_ExportHistory.Package", $this->package)->where("HostFact_ExportHistory.PackageReference", ["!=" => ""])->asArray()->execute();
            foreach ($_invoice_list as $_invoice) {
                $invoice_by_package_reference[$_invoice["PackageReference"]] = $_invoice;
            }
            foreach ($open_transactions as $_transaction) {
                if(!$_transaction["PackageReference"]) {
                } elseif(!isset($invoice_by_package_reference[$_transaction["PackageReference"]])) {
                } else {
                    $_invoice = $invoice_by_package_reference[$_transaction["PackageReference"]];
                    if($remaining_ids && 0 < count($remaining_ids) && !in_array($_invoice["id"], $remaining_ids)) {
                    } elseif(in_array($_invoice["PaymentMethod"], ["ideal", "paypal", "other"]) && $_invoice["TransactionID"] && 0 < $_invoice["Paid"]) {
                    } elseif(in_array($_invoice["Status"], [8, 9])) {
                    } else {
                        $invoice_object = new invoice();
                        $invoice_object->Identifier = $_invoice["id"];
                        $invoice_object->show();
                        $transaction_open_amount = round($_transaction["AmountOpen"], 2);
                        $payment_exporthistory_log = Database_Model::getInstance()->getOne("HostFact_ExportHistory", ["Status", "LastOpenAmount"])->where("Type", "payment_invoice")->where("Package", $this->package)->where("ReferenceID", $invoice_object->Identifier)->execute();
                        $previous_open_amount = $payment_exporthistory_log && !is_null($payment_exporthistory_log->LastOpenAmount) ? round($payment_exporthistory_log->LastOpenAmount, 2) : NULL;
                        $new_open_amount = $transaction_open_amount;
                        $checked_transactions_for_invoice[] = $invoice_object->Identifier;
                        if($previous_open_amount != $new_open_amount && $invoice_object->Authorisation == "yes") {
                            $directdebit = new directdebit();
                            $batch_info = $directdebit->getBatchInfo($invoice_object->SDDBatchID);
                            if($batch_info->Date <= date("Y-m-d")) {
                                if(!is_null($previous_open_amount) && isEmptyFloat($previous_open_amount)) {
                                    $msg = __("export accounting - payment transactions - sdd reversal choose option");
                                    $this->logPaymentItem("payment_invoice", $invoice_object->Identifier, "error", $new_open_amount, "sdd_reversal", [$msg]);
                                    $this->errorsReported++;
                                } else {
                                    $msg = __("export accounting - payment transactions - sdd not exported");
                                    $this->logPaymentItem("payment_invoice", $invoice_object->Identifier, "error", $new_open_amount, "", [$msg]);
                                    $this->errorsReported++;
                                }
                            }
                        }
                        if($previous_open_amount != $new_open_amount) {
                            $msg = __("export accounting - payment transactions - paid in software open in accounting package");
                            $this->logPaymentItem("payment_invoice", $invoice_object->Identifier, "paid_diff", $new_open_amount, "", [$msg]);
                            $this->errorsReported++;
                        }
                    }
                }
            }
        }
        $this->Error = array_unique($this->Error);
        $this->Warning = array_unique($this->Warning);
        $this->Success = array_unique($this->Success);
        $paid_in_hostfact_check_in_accounting_software = Database_Model::getInstance()->getOne("HostFact_ExportHistory", "GROUP_CONCAT(`ReferenceID`) as ids")->where("Status", "paid_diff")->where("Package", $this->package)->where("Type", "payment_invoice")->execute();
        if($paid_in_hostfact_check_in_accounting_software) {
            $paid_in_hostfact_check_in_accounting_software = explode(",", $paid_in_hostfact_check_in_accounting_software->ids);
            $remaining_ids = array_diff($paid_in_hostfact_check_in_accounting_software, $checked_transactions_for_invoice);
            if(0 < count($remaining_ids)) {
                $open_invoices = Database_Model::getInstance()->get("HostFact_Invoice", ["HostFact_Invoice.id", "HostFact_ExportHistory.PackageReference"])->join("HostFact_ExportHistory", "HostFact_ExportHistory.`Type` = 'invoice' AND HostFact_ExportHistory.`ReferenceID` = HostFact_Invoice.`id` AND HostFact_ExportHistory.Package=:package")->bindValue(":package", $this->package)->where("HostFact_Invoice.id", ["IN" => $remaining_ids])->execute();
                foreach ($open_invoices as $_invoice) {
                    if(!isset($open_transactions[$_invoice->PackageReference])) {
                        $this->logPaymentItem("payment_invoice", $_invoice->id, "success", 0);
                    }
                }
            }
        }
        return [];
    }
    public function importPayments($remaining_ids = false)
    {
        if(method_exists($this, "invoiceExists")) {
            return $this->importPaymentsNew($remaining_ids);
        }
        $prev_failed_ids = [];
        if($remaining_ids === false) {
            $result = $this->getManualActions("payment_invoice");
            if($result) {
                foreach ($result as $_item) {
                    $prev_failed_ids[] = $_item->ReferenceID;
                }
            }
        }
        $open_transactions = [];
        $checked_transactions_for_invoice = [];
        if(!method_exists($this, "listOpenTransactions")) {
            return false;
        }
        $open_transactions = $this->listOpenTransactions();
        if($open_transactions === false) {
            return false;
        }
        Database_Model::getInstance()->get("HostFact_Invoice", ["HostFact_Invoice.id", "HostFact_Invoice.InvoiceCode", "HostFact_Invoice.AmountExcl", "HostFact_Invoice.AmountIncl", "HostFact_Invoice.AmountPaid", "HostFact_Invoice.Date", "HostFact_Invoice.Status", "HostFact_ExportHistory.PackageReference", "HostFact_Invoice.SDDBatchID"])->join("HostFact_ExportHistory", "HostFact_ExportHistory.`Type` = 'invoice' AND HostFact_ExportHistory.`ReferenceID` = HostFact_Invoice.`id` AND HostFact_ExportHistory.Package=:package")->bindValue(":package", $this->package);
        if(!empty($remaining_ids)) {
            Database_Model::getInstance()->where("HostFact_Invoice.id", ["IN" => $remaining_ids]);
        } elseif(!empty($prev_failed_ids)) {
            Database_Model::getInstance()->orWhere([["HostFact_Invoice.Status", ["IN" => ["2", "3"]]], ["HostFact_Invoice.id", ["IN" => $prev_failed_ids]]]);
        } else {
            Database_Model::getInstance()->where("HostFact_Invoice.Status", ["IN" => ["2", "3"]]);
        }
        $open_invoices = Database_Model::getInstance()->execute();
        foreach ($open_invoices as $_invoice) {
            $transaction_open_amount = 0;
            if(!$_invoice->PackageReference) {
                if(method_exists($this, "getPackageReferenceFromInvoiceNumber")) {
                    $_invoice->PackageReference = $this->getPackageReferenceFromInvoiceNumber($_invoice->InvoiceCode);
                    if($_invoice->PackageReference) {
                        $this->logExportedItem("invoice", $_invoice->id, "success", $_invoice->PackageReference);
                    }
                }
                if(!$_invoice->PackageReference) {
                }
            }
            if(isset($open_transactions[$_invoice->PackageReference])) {
                $transaction_open_amount = $open_transactions[$_invoice->PackageReference]["AmountOpen"];
                if($_invoice->Status < 4 || 0 == $transaction_open_amount) {
                    unset($open_transactions[$_invoice->PackageReference]);
                }
                if($_invoice->AmountIncl - $_invoice->AmountPaid == $transaction_open_amount && $_invoice->Status < 4 || 0 == $transaction_open_amount && $_invoice->Status == 4) {
                    $this->logExportedItem("payment_invoice", $_invoice->id, "success");
                }
            } elseif($_invoice->Status == 4) {
                $this->logExportedItem("payment_invoice", $_invoice->id, "success");
            }
            $checked_transactions_for_invoice[] = $_invoice->id;
            $info = $this->saveTransactions($this->listAllTransactionsForInvoiceNumber($_invoice->InvoiceCode), $_invoice);
            if(0 < $_invoice->AmountPaid && 0 < count(array_column($info, "CorrespondingInvoice")) && (round($_invoice->AmountPaid, 2) == round($this->_total_known_transactions, 2) || round($_invoice->AmountIncl, 2) == round($this->_total_known_transactions + array_sum(array_column($info, "AmountIncl")), 2)) && 0 < count($info)) {
                if(0 < count(array_column($info, "CorrespondingInvoice"))) {
                    foreach ($info as $_k => $_trans) {
                        if(isset($_trans["CorrespondingInvoice"]) && $_trans["CorrespondingInvoice"] == $_invoice->id) {
                            unset($info[$_k]);
                        }
                    }
                }
            } elseif(0 < $_invoice->AmountPaid && round($_invoice->AmountPaid, 2) != round($this->_total_known_transactions, 2) && !empty($info)) {
                $msg = __("export accounting - payment transactions - already partly paid");
                $this->logExportedItem("payment_invoice", $_invoice->id, "error", "", [$msg]);
                $this->errorsReported++;
            } elseif(round($transaction_open_amount, 2) != round($_invoice->AmountIncl - $_invoice->AmountPaid, 2) && $_invoice->Status < 4 && empty($info)) {
                if($_invoice->SDDBatchID) {
                } else {
                    $result = Database_Model::getInstance()->getOne("HostFact_ExportHistory", ["Status"])->where("Type", "payment_invoice")->where("ReferenceID", $_invoice->id)->execute();
                    if($result && $result->Status == "ignore") {
                    } else {
                        if(isEmptyFloat($transaction_open_amount)) {
                            $msg = __("export accounting - payment transactions - unpaid in software, paid in accounting package");
                        } else {
                            $msg = __("export accounting - payment transactions - mismatch paid amounts");
                        }
                        $this->logExportedItem("payment_invoice", $_invoice->id, "error", "", [$msg]);
                        $this->errorsReported++;
                    }
                }
            }
            if(is_array($info)) {
                foreach ($info as $_transaction_line) {
                    $look_for_action = false;
                    $logged_transaction_id = intval($_transaction_line["logged_transaction_id"]);
                    $transaction_action = "";
                    $invoice_object = new invoice();
                    $invoice_object->Identifier = $_invoice->id;
                    $invoice_object->show();
                    $payable_amount = round($invoice_object->AmountIncl - $invoice_object->AmountPaid, 2);
                    if($invoice_object->Authorisation == "yes" && $invoice_object->SDDBatchID && $payable_amount == round($invoice_object->AmountIncl, 2) && $payable_amount == $_transaction_line["AmountIncl"]) {
                        $directdebit = new directdebit();
                        $batch_info = $directdebit->getBatchInfo($invoice_object->SDDBatchID);
                        if(date("Y-m-d") < $batch_info->Date) {
                            $transaction_action = "sdd_planned_markaspaid";
                        } elseif($invoice_object->Status != 4) {
                            $transaction_action = "sdd_markaspaid";
                            $invoice_object->markaspaid($batch_info->Date);
                            $invoice_object->checkAuto();
                        }
                    } elseif($invoice_object->Status != 4 && $payable_amount == round($invoice_object->AmountIncl, 2) && $payable_amount == $_transaction_line["AmountIncl"]) {
                        $invoice_object->markaspaid($_transaction_line["Date"]);
                        $invoice_object->checkAuto();
                        $transaction_action = "markaspaid";
                    } elseif($invoice_object->Status != 4 && $payable_amount <= $_transaction_line["AmountIncl"]) {
                        $invoice_object->partpayment($payable_amount, $_transaction_line["Date"]);
                        if($invoice_object->Status == 4) {
                            $invoice_object->checkAuto();
                        }
                        $transaction_action = "markaspaid";
                    } elseif($invoice_object->Status != 4 && $_transaction_line["AmountIncl"] < $payable_amount) {
                        $invoice_object->partpayment($_transaction_line["AmountIncl"], $_transaction_line["Date"]);
                        if($invoice_object->Status == 4) {
                            $invoice_object->checkAuto();
                        }
                        $transaction_action = "partpayment";
                    }
                    $this->Error = array_merge($this->Error, $invoice_object->Error);
                    $this->Warning = array_merge($this->Warning, $invoice_object->Warning);
                    $this->Success = array_merge($this->Success, $invoice_object->Success);
                    Database_Model::getInstance()->update("HostFact_ExportPaymentTransactions", ["Action" => $transaction_action])->where("id", $logged_transaction_id)->execute();
                }
            }
            $this->logExportedItem("payment_invoice", $_invoice->id, "success");
        }
        foreach ($open_transactions as $_transaction) {
            if(round($_transaction["AmountOpen"], 2) == round(0, 2) || !$_transaction["PackageReference"]) {
            } else {
                $_invoice = Database_Model::getInstance()->getOne(["HostFact_Invoice", "HostFact_ExportHistory"], ["HostFact_Invoice.*"])->where("HostFact_ExportHistory.Type", "invoice")->where("HostFact_ExportHistory.`ReferenceID` = HostFact_Invoice.`id`")->where("HostFact_ExportHistory.Package", $this->package)->where("HostFact_ExportHistory.PackageReference", $_transaction["PackageReference"])->execute();
                if(!$_invoice) {
                    $inv_number = $this->getInvoiceNumberFromPackageReference($_transaction["PackageReference"]);
                    if(!$inv_number) {
                    } else {
                        $_invoice = Database_Model::getInstance()->getOne("HostFact_Invoice")->where("InvoiceCode", $inv_number)->where("Status", [">" => 0])->execute();
                        if(!$_invoice) {
                        } else {
                            $this->logExportedItem("invoice", $_invoice->id, "success", $_transaction["PackageReference"]);
                        }
                    }
                } else {
                    $inv_number = $_invoice->InvoiceCode;
                }
                if($remaining_ids && !empty($remaining_ids) && !in_array($_invoice->id, $remaining_ids)) {
                } elseif(in_array($_invoice->PaymentMethod, ["ideal", "paypal", "other"]) && $_invoice->TransactionID && 0 < $_invoice->Paid) {
                } elseif(in_array($_invoice->Status, [8, 9])) {
                } else {
                    $checked_transactions_for_invoice[] = $_invoice->id;
                    $info = $this->saveTransactions($this->listAllTransactionsForInvoiceNumber($inv_number), $_invoice);
                    $give_error = !empty($info) ? true : false;
                    if(!$give_error) {
                        $_log_status = Database_Model::getInstance()->getOne("HostFact_ExportHistory", "Status")->where("Type", "payment_invoice")->where("Package", $this->package)->where("ReferenceID", $_invoice->id)->where("Status", "ignore")->execute();
                        $give_error = !$_log_status ? true : false;
                    }
                    if($give_error && $_invoice->Authorisation == "yes") {
                        $sdd_transactions = Database_Model::getInstance()->get("HostFact_ExportPaymentTransactions", "id")->where("InvoiceID", $_invoice->id)->execute();
                        if($sdd_transactions && !empty($sdd_transactions)) {
                            $msg = __("export accounting - payment transactions - sdd reversal choose option");
                            $this->logExportedItem("payment_invoice", $_invoice->id, "error", "sdd_reversal", [$msg]);
                            $this->errorsReported++;
                        } else {
                            $msg = __("export accounting - payment transactions - sdd not exported");
                            $this->logExportedItem("payment_invoice", $_invoice->id, "error", "", [$msg]);
                            $this->errorsReported++;
                        }
                    } elseif($give_error) {
                        $msg = __("export accounting - payment transactions - paid in software open in accounting package");
                        $this->logExportedItem("payment_invoice", $_invoice->id, "paid_diff", "", [$msg]);
                        $this->errorsReported++;
                    }
                }
            }
        }
        $this->Error = array_unique($this->Error);
        $this->Warning = array_unique($this->Warning);
        $this->Success = array_unique($this->Success);
        if(!$remaining_ids) {
            $this->syncDraftTransactions($checked_transactions_for_invoice);
        }
        $paid_in_hostfact_check_in_accounting_software = Database_Model::getInstance()->getOne("HostFact_ExportHistory", "GROUP_CONCAT(`ReferenceID`) as ids")->where("Status", "paid_diff")->where("Type", "payment_invoice")->execute();
        if($paid_in_hostfact_check_in_accounting_software) {
            $paid_in_hostfact_check_in_accounting_software = explode(",", $paid_in_hostfact_check_in_accounting_software->ids);
            $remaining_ids = array_diff($paid_in_hostfact_check_in_accounting_software, $checked_transactions_for_invoice);
            if(!empty($remaining_ids)) {
                $open_invoices = Database_Model::getInstance()->get("HostFact_Invoice", ["HostFact_Invoice.id", "HostFact_Invoice.InvoiceCode", "HostFact_Invoice.AmountExcl", "HostFact_Invoice.AmountIncl", "HostFact_Invoice.AmountPaid", "HostFact_Invoice.Date", "HostFact_Invoice.Status", "HostFact_ExportHistory.PackageReference", "HostFact_Invoice.SDDBatchID"])->join("HostFact_ExportHistory", "HostFact_ExportHistory.`Type` = 'invoice' AND HostFact_ExportHistory.`ReferenceID` = HostFact_Invoice.`id` AND HostFact_ExportHistory.Package=:package")->bindValue(":package", $this->package)->where("HostFact_Invoice.id", ["IN" => $remaining_ids])->execute();
                foreach ($open_invoices as $_invoice) {
                    if(!isset($open_transactions[$_invoice->PackageReference])) {
                        $this->saveTransactions($this->listAllTransactionsForInvoiceNumber($_invoice->InvoiceCode), $_invoice);
                        $this->logExportedItem("payment_invoice", $_invoice->id, "success");
                    }
                }
            }
        }
        return [];
    }
    public function importPurchasePayments($remaining_ids = false)
    {
        $prev_failed_ids = [];
        if($remaining_ids === false) {
            $has_credit_invoices = Database_Model::getInstance()->getOne("HostFact_CreditInvoice", "id")->where("Date", [">=" => date("Y") - 1 . "-01-01"])->execute();
            if(!$has_credit_invoices) {
                return false;
            }
            $result = $this->getManualActions("payment_purchase");
            if($result) {
                foreach ($result as $_item) {
                    $prev_failed_ids[] = $_item->ReferenceID;
                }
            }
        }
        $checked_transactions_for_invoice = [];
        if(!method_exists($this, "listOpenPurchaseTransactions")) {
            return false;
        }
        try {
            $open_transactions = $this->listOpenPurchaseTransactions();
            if($open_transactions === false) {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
        foreach ($open_transactions as $k => $v) {
            if((string) $v["PackageReference"] === "") {
                $this->Error[] = __("retrieving payments went wrong, incorrect data");
                return false;
            }
            if(isEmptyFloat(round($v["AmountOpen"], 2))) {
                unset($open_transactions[$k]);
            }
        }
        Database_Model::getInstance()->get("HostFact_CreditInvoice", ["HostFact_CreditInvoice.id", "HostFact_ExportHistory.PackageReference"])->join("HostFact_ExportHistory", "HostFact_ExportHistory.`Type` = 'creditinvoice' AND HostFact_ExportHistory.`ReferenceID` = HostFact_CreditInvoice.`id` AND HostFact_ExportHistory.Package=:package")->bindValue(":package", $this->package);
        if($remaining_ids && 0 < count($remaining_ids)) {
            Database_Model::getInstance()->where("HostFact_CreditInvoice.id", ["IN" => $remaining_ids]);
        } elseif($prev_failed_ids && 0 < count($prev_failed_ids)) {
            Database_Model::getInstance()->orWhere([["HostFact_CreditInvoice.Status", ["IN" => [1, 2]]], ["HostFact_CreditInvoice.id", ["IN" => $prev_failed_ids]]]);
        } else {
            Database_Model::getInstance()->where("HostFact_CreditInvoice.Status", ["IN" => [1, 2]]);
        }
        $open_purchase_invoices = Database_Model::getInstance()->execute();
        foreach ($open_purchase_invoices as $_invoice) {
            if(!$_invoice->PackageReference) {
            } else {
                $invoice_object = new creditinvoice();
                $invoice_object->Identifier = $_invoice->id;
                $invoice_object->show();
                $payable_amount = round($invoice_object->AmountIncl - $invoice_object->AmountPaid, 2);
                $mark_as_paid = false;
                $payment_correction = false;
                $has_open_amount_difference = false;
                $payment_exporthistory_log = Database_Model::getInstance()->getOne("HostFact_ExportHistory", ["Status", "LastOpenAmount"])->where("Type", "payment_purchase")->where("Package", $this->package)->where("ReferenceID", $invoice_object->Identifier)->execute();
                $previous_open_amount = $payment_exporthistory_log && !is_null($payment_exporthistory_log->LastOpenAmount) ? round($payment_exporthistory_log->LastOpenAmount, 2) : NULL;
                $new_open_amount = 0;
                if(isset($open_transactions[$_invoice->PackageReference])) {
                    $transaction_open_amount = round($open_transactions[$_invoice->PackageReference]["AmountOpen"], 2);
                    $new_open_amount = $transaction_open_amount;
                    unset($open_transactions[$_invoice->PackageReference]);
                    if($payable_amount == $transaction_open_amount) {
                        $this->logPaymentItem("payment_purchase", $invoice_object->Identifier, "success", $new_open_amount);
                    } elseif(0 <= $invoice_object->AmountIncl && $transaction_open_amount < $payable_amount || $invoice_object->AmountIncl < 0 && $payable_amount < $transaction_open_amount) {
                        $payment_correction = true;
                    } else {
                        $has_open_amount_difference = true;
                    }
                } elseif($invoice_object->Status == 3) {
                    $this->logPaymentItem("payment_purchase", $invoice_object->Identifier, "success", 0);
                } else {
                    $mark_as_paid = true;
                }
                $msg = [];
                $log_status = "success";
                if(is_null($previous_open_amount) || $previous_open_amount != $new_open_amount) {
                    if($mark_as_paid === true) {
                        try {
                            if($this->creditInvoiceExists($_invoice->PackageReference)) {
                                $invoice_object->updateOpenAmountViaPackage($new_open_amount, $this->versionInfo["name"]);
                            } else {
                                $msg[] = __("export accounting - payment transactions - not found in accounting package");
                                $log_status = "error";
                            }
                        } catch (Exception $e) {
                            return false;
                        }
                    } elseif($payment_correction === true) {
                        $invoice_object->updateOpenAmountViaPackage($new_open_amount, $this->versionInfo["name"]);
                    } elseif($has_open_amount_difference === true) {
                        $msg[] = __("export accounting - payment transactions - mismatch paid amounts - hostfact x - accounting package y", ["amount_hf" => money($payable_amount), "amount_acc" => money($new_open_amount)]);
                        $log_status = "error";
                        $this->errorsReported++;
                    }
                    $this->Error = array_merge($this->Error, $invoice_object->Error);
                    $this->Warning = array_merge($this->Warning, $invoice_object->Warning);
                    $this->Success = array_merge($this->Success, $invoice_object->Success);
                    $this->logPaymentItem("payment_purchase", $invoice_object->Identifier, $log_status, $new_open_amount, "", $msg);
                } elseif(!is_null($previous_open_amount) && $previous_open_amount == $new_open_amount) {
                    if(!empty($payment_exporthistory_log->Status) && $payment_exporthistory_log->Status == "ignore") {
                    } elseif($mark_as_paid === true) {
                        try {
                            if($this->creditInvoiceExists($_invoice->PackageReference)) {
                                $msg[] = __("export accounting - payment transactions - unpaid in software, paid in accounting package");
                            } else {
                                $msg[] = __("export accounting - payment transactions - not found in accounting package");
                            }
                        } catch (Exception $e) {
                            return false;
                        }
                        $log_status = "error";
                    } elseif($has_open_amount_difference === true) {
                        $msg[] = __("export accounting - payment transactions - mismatch paid amounts - hostfact x - accounting package y", ["amount_hf" => money($payable_amount), "amount_acc" => money($new_open_amount)]);
                        $log_status = "error";
                    }
                    if($log_status == "error") {
                        $this->logPaymentItem("payment_purchase", $invoice_object->Identifier, $log_status, $new_open_amount, "", $msg);
                        $this->errorsReported++;
                    }
                }
                $checked_transactions_for_invoice[] = $invoice_object->Identifier;
            }
        }
        foreach ($open_transactions as $_transaction) {
            if(!$_transaction["PackageReference"]) {
            } else {
                $_invoice = Database_Model::getInstance()->getOne(["HostFact_CreditInvoice", "HostFact_ExportHistory"], ["HostFact_CreditInvoice.id"])->where("HostFact_ExportHistory.Type", "creditinvoice")->where("HostFact_ExportHistory.`ReferenceID` = HostFact_CreditInvoice.`id`")->where("HostFact_ExportHistory.Package", $this->package)->where("HostFact_ExportHistory.PackageReference", $_transaction["PackageReference"])->execute();
                if(!$_invoice) {
                } elseif($remaining_ids && 0 < count($remaining_ids) && !in_array($_invoice->id, $remaining_ids)) {
                } else {
                    $invoice_object = new creditinvoice();
                    $invoice_object->Identifier = $_invoice->id;
                    $invoice_object->show();
                    if(in_array($invoice_object->Status, [8])) {
                    } else {
                        $transaction_open_amount = $_transaction["AmountOpen"];
                        $payment_exporthistory_log = Database_Model::getInstance()->getOne("HostFact_ExportHistory", ["Status", "LastOpenAmount"])->where("Type", "payment_purchase")->where("Package", $this->package)->where("ReferenceID", $invoice_object->Identifier)->execute();
                        $previous_open_amount = $payment_exporthistory_log && !is_null($payment_exporthistory_log->LastOpenAmount) ? round($payment_exporthistory_log->LastOpenAmount, 2) : NULL;
                        $new_open_amount = $transaction_open_amount;
                        $checked_transactions_for_invoice[] = $invoice_object->Identifier;
                        if($previous_open_amount != $new_open_amount) {
                            $msg = __("export accounting - payment transactions - paid in software open in accounting package");
                            $this->logPaymentItem("payment_purchase", $invoice_object->Identifier, "paid_diff", $new_open_amount, "", [$msg]);
                            $this->errorsReported++;
                        }
                    }
                }
            }
        }
        $this->Error = array_unique($this->Error);
        $this->Warning = array_unique($this->Warning);
        $this->Success = array_unique($this->Success);
        $paid_in_hostfact_check_in_accounting_software = Database_Model::getInstance()->getOne("HostFact_ExportHistory", "GROUP_CONCAT(`ReferenceID`) as ids")->where("Status", "paid_diff")->where("Package", $this->package)->where("Type", "payment_purchase")->execute();
        if($paid_in_hostfact_check_in_accounting_software) {
            $paid_in_hostfact_check_in_accounting_software = explode(",", $paid_in_hostfact_check_in_accounting_software->ids);
            $remaining_ids = array_diff($paid_in_hostfact_check_in_accounting_software, $checked_transactions_for_invoice);
            if(0 < count($remaining_ids)) {
                $open_invoices = Database_Model::getInstance()->get("HostFact_CreditInvoice", ["HostFact_CreditInvoice.id", "HostFact_ExportHistory.PackageReference"])->join("HostFact_ExportHistory", "HostFact_ExportHistory.`Type` = 'creditinvoice' AND HostFact_ExportHistory.`ReferenceID` = HostFact_CreditInvoice.`id` AND HostFact_ExportHistory.Package=:package")->bindValue(":package", $this->package)->where("HostFact_CreditInvoice.id", ["IN" => $remaining_ids])->execute();
                foreach ($open_invoices as $_invoice) {
                    if(!isset($open_transactions[$_invoice->PackageReference])) {
                        $this->logPaymentItem("payment_purchase", $_invoice->id, "success", 0);
                    }
                }
            }
        }
        return [];
    }
    protected function syncDraftTransactions($checked_transactions_for_invoice)
    {
    }
    protected function saveTransactions($info, $_invoice)
    {
        $this->_total_known_transactions = 0;
        $logged_transactions = Database_Model::getInstance()->getOne("HostFact_ExportPaymentTransactions", "GROUP_CONCAT(`id`) as ids")->where("Package", $this->package)->where("InvoiceID", $_invoice->id)->where("PackageStatus", ["!=" => "removed"])->execute();
        $logged_ids = [];
        if($logged_transactions && $logged_transactions->ids) {
            $logged_ids = explode(",", $logged_transactions->ids);
        }
        if(is_array($info)) {
            foreach ($info as $_key => $_transaction_line) {
                $logged_transaction = Database_Model::getInstance()->get("HostFact_ExportPaymentTransactions")->where("Package", $this->package)->where("Journal", $_transaction_line["Journal"])->where("PackageReference", $_transaction_line["PackageReference"])->where("Date", $_transaction_line["Date"])->where("Description", $_transaction_line["Description"])->execute();
                if(!empty($logged_transaction)) {
                    if(1 < count($logged_transaction)) {
                        foreach ($logged_transaction as $_found_key => $_found_transaction) {
                            if($_found_transaction->Date == $_transaction_line["Date"] && $_found_transaction->Description == $_transaction_line["Description"] && $_found_transaction->Amount == $_transaction_line["AmountIncl"] && $_found_transaction->InvoiceID == $_invoice->id) {
                                $logged_transaction = $logged_transaction[$_found_key];
                                if(!empty($logged_transaction)) {
                                    foreach ($logged_transaction as $_found_key => $_found_transaction) {
                                        if($_found_transaction->Description == $_transaction_line["Description"] && $_found_transaction->InvoiceID == $_invoice->id) {
                                            $logged_transaction = $logged_transaction[$_found_key];
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        $logged_transaction = $logged_transaction[0];
                    }
                    if($logged_transaction->Date == $_transaction_line["Date"] && $logged_transaction->Description == $_transaction_line["Description"] && $logged_transaction->Amount == $_transaction_line["AmountIncl"] && $logged_transaction->InvoiceID == $_invoice->id) {
                        unset($info[$_key]);
                        unset($logged_ids[array_search($logged_transaction->id, $logged_ids)]);
                        $this->_total_known_transactions += $_transaction_line["AmountIncl"];
                        Database_Model::getInstance()->update("HostFact_ExportPaymentTransactions", ["PackageStatus" => $_transaction_line["Status"]])->where("id", $logged_transaction->id)->execute();
                    } else {
                        $logged_transaction = false;
                    }
                }
                if(empty($logged_transaction)) {
                    $result = Database_Model::getInstance()->insert("HostFact_ExportPaymentTransactions", ["Package" => $this->package, "Journal" => $_transaction_line["Journal"], "PackageReference" => $_transaction_line["PackageReference"], "Date" => $_transaction_line["Date"], "Description" => $_transaction_line["Description"], "Amount" => $_transaction_line["AmountIncl"], "PackageStatus" => $_transaction_line["Status"], "InvoiceID" => $_invoice->id, "Action" => ""])->execute();
                    if(0 < $result) {
                        $info[$_key]["logged_transaction_id"] = $result;
                    }
                }
            }
        }
        if(!empty($logged_ids)) {
            Database_Model::getInstance()->update("HostFact_ExportPaymentTransactions", ["PackageStatus" => "removed"])->where("id", ["IN" => $logged_ids])->execute();
        }
        return $info;
    }
    public function hasRelationCodeCollision($table, $column, $code, $mask, $filter = false)
    {
        if(!in_array($table, ["HostFact_Debtors", "HostFact_Creditors"]) || !in_array($column, ["DebtorCode", "CreditorCode"])) {
            return false;
        }
        if($mask == "*") {
            if($filter == "int") {
                $int_code = $this->_toInt($code);
                if(!$int_code) {
                    $relation_list = Database_Model::getInstance()->get($table, $column)->where($column, ["REGEXP" => "^([^1-9]*)\$"])->where("Status", ["!=" => 9])->execute();
                } else {
                    $relation_list = Database_Model::getInstance()->get($table, $column)->where($column, ["REGEXP" => "^([^1-9]*)(" . $int_code . ")([^0-9]*)\$"])->where("Status", ["!=" => 9])->execute();
                }
            } else {
                $relation_list = Database_Model::getInstance()->get($table, $column)->where("TRIM(`" . $column . "`)", trim($code))->where("Status", ["!=" => 9])->execute();
            }
        } else {
            $mask = str_replace("[0-9]", "#", $mask);
            $nr_of_digits = substr_count($mask, "#");
            if(!$nr_of_digits) {
                return false;
            }
            $temp = preg_replace("/[^0-9]/i", "", substr($code, -1 * $nr_of_digits));
            $temp = ltrim($temp, "0");
            if(!$temp) {
                return false;
            }
            $relation_list = Database_Model::getInstance()->get($table, $column)->where("RIGHT(`" . $column . "`," . $nr_of_digits . ")", ["REGEXP" => "^([^1-9]{0," . max(0, $nr_of_digits - strlen($temp)) . "}" . $temp . ")\$"])->where("Status", ["!=" => 9])->execute();
        }
        if($relation_list && (1 < count($relation_list) || $relation_list[0]->{$column} != $code)) {
            $array_codes = [];
            foreach ($relation_list as $_code) {
                $array_codes[] = $_code->{$column};
            }
            return $array_codes;
        } else {
            return false;
        }
    }
    public function hasDebtorCodeCollision($code, $mask, $filter = false)
    {
        return $this->hasRelationCodeCollision("HostFact_Debtors", "DebtorCode", $code, $mask, $filter);
    }
    public function hasCreditorCodeCollision($code, $mask, $filter = false)
    {
        return $this->hasRelationCodeCollision("HostFact_Creditors", "CreditorCode", $code, $mask, $filter);
    }
    protected function checkSepaDirectDebitInvoicesExport(array $batch_transactions)
    {
        foreach ($batch_transactions as $_batch_line) {
            if($this->getPackageReference("invoice", $_batch_line["InvoiceID"]) === false) {
                $this->storeError("sddbatch", $_batch_line["BatchID"], __("export of sdd batches is only possible after exporting invoices"));
                return false;
            }
        }
        return true;
    }
}

?>