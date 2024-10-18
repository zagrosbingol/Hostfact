<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class export
{
    protected $package;
    protected $LicenseStatus;
    protected $apiMethod;
    protected $apiVersion;
    protected $hasSettings;
    protected $hasLoginCredentials;
    protected $filename;
    protected $temp_name;
    protected $type;
    protected $filters = [];
    public $config = [];
    public $language = [];
    public $supported = [];
    public function __construct()
    {
        $this->Error = $this->Warning = $this->Success = [];
        $this->LicenseStatus = "blocked";
        global $array_invoicestatus;
        $this->filters = ["invoice" => ["code" => ["name" => "Factuurnummer", "field" => "InvoiceCode", "type" => "min-max"], "date" => ["name" => "Factuurdatum", "field" => "Date", "type" => "min-max-date"], "paymentdate" => ["name" => "Betaaldatum", "field" => "PayDate", "type" => "min-max-date"], "country" => ["name" => "Land", "field" => "HostFact_Invoice.`Country`", "type" => "val", "options" => ["nl" => "Nederland", "eu" => "Overige EU landen", "non-eu" => "Alle landen buiten Europa"]], "status" => ["name" => "Status", "field" => "HostFact_Invoice.`Status`", "type" => "val", "options" => $array_invoicestatus]], "creditinvoice" => ["code" => ["name" => "Factuurnummer", "field" => "CreditInvoiceCode", "type" => "min-max"], "date" => ["name" => "Factuurdatum", "field" => "Date", "type" => "min-max-date"], "paymentdate" => ["name" => "Betaaldatum", "field" => "PayDate", "type" => "min-max-date"], "country" => ["name" => "Land", "field" => "HostFact_CreditInvoice.`Country`", "type" => "val", "options" => ["nl" => "Nederland", "eu" => "Overige EU landen", "non-eu" => "Alle landen buiten Europa"]]], "debtor" => ["code" => ["name" => "Debiteurnummer", "field" => "DebtorCode", "type" => "min-max"]], "creditor" => ["code" => ["name" => "Crediteurnummer", "field" => "CreditorCode", "type" => "min-max"]], "product" => ["code" => ["name" => "Productnummer", "field" => "ProductCode", "type" => "min-max"]], "sddbatches" => ["batch" => ["name" => __("sdd batch"), "field" => "BatchID", "type" => "min-max"]]];
        $this->config["taxRates"] = ["Zero", "0.06" => "Low", "0.09" => "Low", "0.19" => "High", "0.21" => "High", "0.23" => "High"];
        $this->supported = ["debtors" => false, "creditors" => false, "products" => false, "invoices" => false, "creditinvoices" => false];
        $this->hasSettings = false;
        $this->hasLoginCredentials = false;
        $this->base_url = BACKOFFICE_URL . "exportaccounting.php?module=" . $this->package;
        $this->debtor_url = BACKOFFICE_URL . "debtors.php?page=show&id=";
        $this->creditor_url = BACKOFFICE_URL . "creditors.php?page=show&id=";
        $this->product_url = BACKOFFICE_URL . "products.php?page=show&id=";
        $this->invoice_url = BACKOFFICE_URL . "invoices.php?page=show&id=";
        $this->creditinvoice_url = BACKOFFICE_URL . "creditors.php?page=show_invoice&id=";
        $this->directdebit_url = BACKOFFICE_URL . "directdebit.php?page=show&id=";
        $this->product_group_add_url = BACKOFFICE_URL . "products.php?page=add_group";
        $this->creditor_group_add_url = BACKOFFICE_URL . "creditors.php?page=add_group";
    }
    public function getPath($type)
    {
        if(isset($this->{$type . "_url"}) && $this->{$type . "_url"}) {
            return $this->{$type . "_url"};
        }
        return false;
    }
    protected function addFilter($for, $identifier, $field, $type, $name)
    {
        $this->filters[$for]["custom_" . $identifier] = ["name" => $name, "field" => $field, "type" => $type];
    }
    public function getFilter($for, $identifier)
    {
        return isset($this->filters[$for][$identifier]) ? $this->filters[$for][$identifier] : NULL;
    }
    public function getFilters()
    {
        return $this->filters;
    }
    protected function connect()
    {
        if($this->apiMethod == "csv" || $this->apiMethod == "xml") {
            $this->temp_name = @tempnam(@sys_get_temp_dir(), "wf_");
            if(!$this->temp_name) {
                $this->temp_name = "temp/wf_38d39_" . md5(time());
            }
            return true;
        }
    }
    protected function disconnect()
    {
        if($this->apiMethod == "csv") {
            @unlink($this->temp_name);
            @unlink($this->filename);
            return true;
        }
    }
    protected function download()
    {
        @unlink("./temp/" . $this->filename);
        if($this->checkLicense()) {
            @copy($this->temp_name, "./temp/" . $this->filename);
            $_SESSION["force_download"] = $this->filename;
        }
    }
    protected function _toString($string)
    {
        return str_replace("\"", "\"\"", iconv("UTF-8", "CP1252", trim($string)));
    }
    protected function _toInt($string)
    {
        return (int) preg_replace("/[^0-9]/", "", $string);
    }
    protected function _translateFilters($filters)
    {
        $where = [];
        foreach ($filters as $name => $filter) {
            if(array_key_exists($name, $this->filters)) {
                foreach ($filter as $type => $f) {
                    if($this->filters[$name][$type]["type"] == "min-max-date") {
                        if($f["min"] != NULL) {
                            Database_Model::getInstance()->where($this->filters[$name][$type]["field"], [">=" => date("Y-m-d", strtotime(rewrite_date_site2db($f["min"])))]);
                        }
                        if($f["max"] != NULL) {
                            Database_Model::getInstance()->where($this->filters[$name][$type]["field"], ["<=" => date("Y-m-d", strtotime(rewrite_date_site2db($f["max"])))]);
                        }
                    }
                    if($this->filters[$name][$type]["type"] == "min-max") {
                        if($f["min"] != NULL) {
                            Database_Model::getInstance()->where($this->filters[$name][$type]["field"], [">=" => $f["min"]])->where("LENGTH(`" . $this->filters[$name][$type]["field"] . "`)", [">=" => strlen($f["min"])]);
                        }
                        if($f["max"] != NULL) {
                            Database_Model::getInstance()->where($this->filters[$name][$type]["field"], ["<=" => $f["max"]])->where("LENGTH(`" . $this->filters[$name][$type]["field"] . "`)", ["<=" => strlen($f["max"])]);
                        }
                    }
                    if($this->filters[$name][$type]["type"] == "val") {
                        if($type == "country") {
                            global $array_country_EU;
                            $eu_countries = array_keys($array_country_EU);
                            if($f["val"] == "nl") {
                                Database_Model::getInstance()->where($this->filters[$name][$type]["field"], "NL");
                            }
                            if($f["val"] == "eu") {
                                Database_Model::getInstance()->where($this->filters[$name][$type]["field"], ["IN" => $eu_countries])->where($this->filters[$name][$type]["field"], ["!=" => "NL"]);
                            }
                            if($f["val"] == "non-eu") {
                                Database_Model::getInstance()->where($this->filters[$name][$type]["field"], ["NOT IN" => $eu_countries]);
                            }
                        } elseif($type == "debtortype" || $type == "creditortype") {
                            if($f["val"] == "individuals") {
                                Database_Model::getInstance()->where($this->filters[$name][$type]["field"], "");
                            } else {
                                Database_Model::getInstance()->where($this->filters[$name][$type]["field"], ["<>" => ""]);
                            }
                        } elseif($f["val"] != NULL) {
                            Database_Model::getInstance()->where($this->filters[$name][$type]["field"], $f["val"]);
                        }
                    }
                }
            }
        }
        return true;
    }
    protected function _getDebtors($filters = [])
    {
        Database_Model::getInstance()->get("HostFact_Debtors")->where("Status", ["!=" => 9]);
        $this->_translateFilters($filters);
        $result = Database_Model::getInstance()->orderBy("id", "ASC")->asArray()->execute();
        return $result;
    }
    protected function _getCreditors($filters = [])
    {
        Database_Model::getInstance()->get("HostFact_Creditors")->where("Status", ["!=" => 9]);
        $this->_translateFilters($filters);
        $result = Database_Model::getInstance()->orderBy("id", "ASC")->asArray()->execute();
        return $result;
    }
    protected function _getProducts($filters = [])
    {
        Database_Model::getInstance()->get("HostFact_Products")->where("Status", ["!=" => 9]);
        $this->_translateFilters($filters);
        $result = Database_Model::getInstance()->orderBy("id", "ASC")->asArray()->execute();
        return $result;
    }
    protected function _getInvoices($filters = [])
    {
        Database_Model::getInstance()->get("HostFact_Invoice", ["HostFact_Invoice.*", "HostFact_Debtors.DebtorCode", "HostFact_Debtors.TaxNumber"])->where("HostFact_Invoice.Status", [">" => 1])->join("HostFact_Debtors", "HostFact_Debtors.`id` = HostFact_Invoice.`Debtor`");
        $this->_translateFilters($filters);
        $result = Database_Model::getInstance()->orderBy("HostFact_Invoice.InvoiceCode", "ASC")->asArray()->execute();
        return $result;
    }
    protected function _getInvoiceElements($invoiceCode, $vatcalcmethod = "excl")
    {
        if($vatcalcmethod == "incl") {
            $element_list = Database_Model::getInstance()->get("HostFact_InvoiceElements", ["HostFact_InvoiceElements.*", "`LineAmountExcl` as AmountExcl", "`LineAmountIncl` as AmountIncl"])->where("InvoiceCode", $invoiceCode)->orderBy("HostFact_InvoiceElements.Ordering", "ASC")->orderBy("HostFact_InvoiceElements.id", "ASC")->asArray()->execute();
        } else {
            $element_list = Database_Model::getInstance()->get("HostFact_InvoiceElements", ["HostFact_InvoiceElements.*", "`LineAmountExcl` as AmountExcl", "`LineAmountIncl` as AmountIncl"])->where("InvoiceCode", $invoiceCode)->orderBy("HostFact_InvoiceElements.Ordering", "ASC")->orderBy("HostFact_InvoiceElements.id", "ASC")->asArray()->execute();
        }
        return $element_list;
    }
    protected function _getCreditInvoices($filters = [])
    {
        Database_Model::getInstance()->get("HostFact_CreditInvoice", ["HostFact_CreditInvoice.*", "HostFact_Creditors.CreditorCode", "HostFact_Creditors.CompanyName", "HostFact_Creditors.Country"])->where("HostFact_CreditInvoice.Status", [">=" => 1])->join("HostFact_Creditors", "HostFact_Creditors.`id` = HostFact_CreditInvoice.`Creditor`");
        $this->_translateFilters($filters);
        $result = Database_Model::getInstance()->orderBy("HostFact_CreditInvoice.CreditInvoiceCode", "ASC")->asArray()->execute();
        return $result;
    }
    protected function _getCreditInvoiceElements($invoiceCode)
    {
        $element_list = Database_Model::getInstance()->get("HostFact_CreditInvoiceElements", ["HostFact_CreditInvoiceElements.*", "ROUND(`PriceExcl` * `Number`,2) as AmountExcl"])->where("CreditInvoiceCode", $invoiceCode)->asArray()->execute();
        return $element_list;
    }
    public function getGroups()
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
        foreach ($result as $row) {
            $list[$row["id"]] = $row;
        }
        return $list;
    }
    public function getConfig($name = NULL)
    {
        if($name != NULL) {
            return isset($this->config[$name]) ? $this->config[$name] : NULL;
        }
        return $this->config;
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
    public function getPackageName()
    {
        if(file_exists("3rdparty/export/" . $this->package . "/version.php")) {
            include "3rdparty/export/" . $this->package . "/version.php";
            if(isset($version["name"])) {
                return $version["name"];
            }
        }
        return $this->package;
    }
    public function getLicenseStatus()
    {
        return $this->LicenseStatus;
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
    public function getAvailablePackages($packages = [])
    {
        $folder = "3rdparty/export";
        $file_list = @scandir($folder);
        if(!$file_list) {
            return $packages;
        }
        foreach ($file_list as $item) {
            if($item == "." || $item == ".." || !is_dir($folder . "/" . $item) || !file_exists($folder . "/" . $item . "/version.php")) {
            } else {
                $item = strtolower($item);
                $version = [];
                include "3rdparty/export/" . $item . "/version.php";
                if(!isset($version["version"])) {
                    $version["version"] = $version["wefact_version"];
                }
                $packages[$item] = $version;
            }
        }
        return $packages;
    }
    public function hasSettings()
    {
        return $this->hasSettings;
    }
    public function hasLoginCredentials()
    {
        return $this->hasLoginCredentials;
    }
    public function getSettings($name)
    {
        if(strlen($this->package) === 0) {
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_ExportSettings", ["modified", "value"])->where("package", strtolower($this->package))->where("name", $name)->execute();
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
        Database_Model::getInstance()->insert("HostFact_ExportSettings", ["modified" => ["RAW" => "NOW()"], "value" => $value, "package" => strtolower($this->package), "name" => $name])->onDuplicate(["modified" => ["RAW" => "NOW()"], "value" => $value])->execute();
    }
    public function showLastExport($type, $single_type)
    {
        $stats = $this->getSettings("statistics");
        echo "<span class=\"fontsmall c4 export_history\">";
        if(isset($stats["value"][$type]["lastExport"]) && 0 < $stats["value"][$type]["lastExport"]) {
            echo sprintf(__("export accounting - last export on"), date("d-m-Y", $stats["value"][$type]["lastExport"]) . " " . __("at") . " " . date("H:i", $stats["value"][$type]["lastExport"]));
            if(!empty($stats["value"][$type]["filters"][$single_type])) {
                echo "<br />";
                foreach ($stats["value"][$type]["filters"][$single_type] as $id => $val) {
                    $filter = $this->getFilter($single_type, $id);
                    echo "&nbsp;&nbsp; &bull; ";
                    echo $filter["name"];
                    echo " (";
                    if($filter["type"] == "min-max-date" || $filter["type"] == "min-max") {
                        echo $val["min"] . " - " . $val["max"];
                    } elseif($filter["type"] == "val") {
                        echo $filter["options"][$val["val"]];
                    }
                    echo ")";
                }
            }
        } else {
            echo __("export accounting - not yet exported");
        }
        echo "</span>";
    }
    public function startTrial()
    {
        global $server_addr;
        $url = "https://www.hostfact.nl/hosting/infomodule.php?action=trial_module";
        $url .= "&module=" . urlencode(strtolower($this->package));
        $url .= "&licensemd5=" . md5(trim(LICENSE));
        $url .= "&ip=" . urlencode($server_addr);
        $result = getContent($url);
        $result = explode("|", $result);
        if($result[1] == "OK") {
            return true;
        }
        if($result[1] == "EXPIRED") {
            $this->Error[] = "U heeft al eerder een proefperiode voor " . $this->package . " aangevraagd. Neem daarom contact met ons op via info@hostfact.nl";
            return false;
        }
        $this->Error[] = "Er is een fout opgetreden tijdens het aanmaken van uw proefperiode voor " . $this->package . ". Neem daarom contact met ons op via info@hostfact.nl";
        return false;
    }
    public function endSubscription($message = "")
    {
        checkRight(U_EXPORT_EDIT);
        $url = "https://www.hostfact.nl/hosting/infomodule.php?action=end_module";
        $url .= "&module=" . urlencode(strtolower($this->package));
        $url .= "&licensemd5=" . md5(trim(LICENSE));
        $url .= "&message=" . urlencode(base64_encode($message));
        $result = getContent($url);
        $result = explode("|", $result);
        if($result[1] == "OK") {
            Database_Model::getInstance()->delete("HostFact_ExportSettings")->where("package", strtolower($this->package))->execute();
            unset($_SESSION[md5(strtolower($this->package))]);
            $this->Success[] = "Uw " . $this->package . " module is beëindigd.";
            return true;
        }
        $this->Error[] = "Uw " . $this->package . " module kan niet automatisch worden beëindigd. Neem daarom contact met ons op via info@hostfact.nl";
        return false;
    }
    public function orderSubscription()
    {
        $url = "https://www.hostfact.nl/hosting/infomodule.php?action=order_module";
        $url .= "&module=" . urlencode(strtolower($this->package));
        $url .= "&licensemd5=" . md5(trim(LICENSE));
        $result = getContent($url);
        $result = explode("|", $result);
        if($result[1] == "OK") {
            return true;
        }
        $this->Error[] = "Er is een fout opgetreden tijdens het bestellen van de " . $this->package . " module. Neem daarom contact met ons op via info@hostfact.nl";
        return false;
    }
    public function checkLicense()
    {
        global $server_addr;
        if(isset($_SESSION[md5(strtolower($this->package))]) && ($_SESSION[md5(strtolower($this->package))] == "OK" || $_SESSION[md5(strtolower($this->package))] == "TRIAL")) {
            $this->LicenseStatus = $_SESSION[md5(strtolower($this->package))];
            return true;
        }
        $url = "https://www.hostfact.nl/hosting/infomodule.php?action=validate_module";
        $url .= "&module=" . urlencode(strtolower($this->package));
        $url .= "&licensemd5=" . md5(trim(LICENSE));
        $url .= "&ip=" . urlencode($server_addr);
        $result = getContent($url);
        if(substr($result, 0, 8) != "HOSTFACT") {
            return true;
        }
        $result = explode("|", $result);
        if($result[1] == "OK") {
            $_SESSION[md5(strtolower($this->package))] = "OK";
            $this->LicenseStatus = "OK";
            return true;
        }
        if($result[1] == "TRIAL") {
            $_SESSION[strtolower($this->package) . "-trialdate"] = rewrite_date_db2site($result[2]);
            $_SESSION[md5(strtolower($this->package))] = "TRIAL";
            $this->LicenseStatus = "TRIAL";
            return true;
        }
        if($result[1] == "EXPIRED") {
            $_SESSION[md5(strtolower($this->package))] = "EXPIRED";
            $this->LicenseStatus = "EXPIRED";
            list($this->ExpiredDaysTillSubscription, $this->ExpiredMessage, $this->ExpiredSubscriptionStart) = $result;
            return false;
        }
        $this->Error[] = "Uw licentie voor " . $this->package . " is geblokkeerd. Neem daarom contact met ons op via info@hostfact.nl";
        return false;
    }
    protected function _getFirstMailAddress($emailAddress)
    {
        if(strpos($emailAddress, ";")) {
            $arrayEmailAddress = explode(";", $emailAddress);
            return $arrayEmailAddress[0];
        }
        return $emailAddress;
    }
    public function showCustomGroups()
    {
        global $company;
        global $array_country;
        $groups = $this->getGroups();
        $creditor_groups = $this->getCreditorGroups();
        $accounts = $this->getSettings("ledgerAccounts");
        $otherSettings = $this->getSettings("otherSettings");
        echo "\t\t<div class=\"split2\">\n\t\t\t<div class=\"left\">\n\n\t\t\t\t<div class=\"box3\">\n\t\t\t\t\t<h3>Afwijkende grootboekrekeningen verkoop</h3>\n\t\t\t\t\t<div class=\"content lineheight2\">\n\t\t\t\t\t\t<label><input type=\"checkbox\" name=\"toggleCustomLedgerTable\" value=\"1\" /> Ik wil afwijkende grootboekrekeningen opgeven per productgroep</label><br />\n\n\t\t\t\t\t\t<div id=\"customLedgerTable\">\n\t\t\t\t\t\t\t";
        if(empty($groups)) {
            echo "\t\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t\tOm gebruik te kunnen maken van afwijkende grootboekrekeningen per productgroep, dient eerst een productgroep aangemaakt te worden. <a href=\"";
            echo $this->getPath("product_group_add");
            echo "\" class=\"a1 c1\">Klik hier om een eerste productgroep aan te maken.</a>";
        } else {
            echo "\t\t\t\t\t\t\t\t<label><input type=\"checkbox\" name=\"custom_group_all_countries\" value=\"yes\" ";
            if(isset($otherSettings["value"]["custom_group_all_countries"]) && $otherSettings["value"]["custom_group_all_countries"] == "yes") {
                echo "checked=\"checked\";";
            }
            echo "/> Ik wil deze afwijkende grootboekrekeningen ook laten gelden voor verkoop buiten ";
            echo $array_country[$company->Country];
            echo "</label>\n\t\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t\t<table border=\"0\" cellpadding=\"0\" cellspacing=\"3\" style=\"width: 100%\">\n\t\t\t\t\t\t\t\t\t<thead>\n\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t<td style=\"width: 60%;\">&nbsp;</td>\n\t\t\t\t\t\t\t\t\t\t<td style=\"text-align: center;\"><strong>";
            echo $this->getPackageName();
            echo " kenmerk</strong></td>\n\t\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t\t</thead>\n\t\t\t\t\t\t\t\t\t<tbody>\n\t\t\t\t\t\t\t\t\t";
            $customAccounts = $accounts["value"]["custom"];
            foreach ($groups as $tmp_group) {
                echo "\t\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t\t<td><strong>";
                echo htmlspecialchars($tmp_group["GroupName"]);
                echo "</strong></td>\n\t\t\t\t\t\t\t\t\t\t\t<td style=\"text-align: center;\"><input type=\"text\" class=\"text1 size6\" name=\"group[";
                echo $tmp_group["id"];
                echo "]\" value=\"";
                echo isset($customAccounts[$tmp_group["id"]]["id"]) && 0 < $customAccounts[$tmp_group["id"]]["id"] ? $customAccounts[$tmp_group["id"]]["id"] : "";
                echo "\" style=\"text-align: center;\" /></td>\n\t\t\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t\t\t";
            }
            echo "\t\t\t\t\t\t\t\t\t</tbody>\n\t\t\t\t\t\t\t\t</table>\n\t\t\t\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\t\t\t\t</div>\n\n\t\t\t</div>\n\t\t\t<div class=\"right\">\n\t\t\t\t";
        if(isset($this->config["ledgerAccounts"]["purchase_groups"])) {
            echo "\t\t\t\t\t<div class=\"box3\">\n\t\t\t\t\t\t<h3>Afwijkende grootboekrekeningen inkoop</h3>\n\t\t\t\t\t\t<div class=\"content lineheight2\">\n\t\t\t\t\t\t\t<label><input type=\"checkbox\" name=\"togglePurchaseLedgerTable\" value=\"1\" /> Ik wil afwijkende grootboekrekeningen opgeven per crediteurgroep</label><br />\n\n\t\t\t\t\t\t\t<div id=\"purchaseLedgerTable\">\n\t\t\t\t\t\t\t\t";
            if(empty($creditor_groups)) {
                echo "\t\t\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t\t\tOm gebruik te kunnen maken van afwijkende grootboekrekeningen per crediteurgroep, dient eerst een crediteurgroep aangemaakt te worden. <a href=\"";
                echo $this->getPath("creditor_group_add");
                echo "\" class=\"a1 c1\">Klik hier om een eerste crediteurgroep aan te maken.</a>";
            } else {
                echo "\t\t\t\t\t\t\t\t\t<label><input type=\"checkbox\" name=\"custom_purchasegroup_all_countries\" value=\"yes\" ";
                if(isset($otherSettings["value"]["custom_purchasegroup_all_countries"]) && $otherSettings["value"]["custom_purchasegroup_all_countries"] == "yes") {
                    echo "checked=\"checked\";";
                }
                echo "/> Ik wil deze afwijkende grootboekrekeningen ook laten gelden voor inkoop buiten ";
                echo $array_country[$company->Country];
                echo "</label>\n\t\t\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t\t\t<table border=\"0\" cellpadding=\"0\" cellspacing=\"3\" style=\"width: 100%\">\n\t\t\t\t\t\t\t\t\t\t<thead>\n\t\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t\t<td style=\"width: 60%;\">&nbsp;</td>\n\t\t\t\t\t\t\t\t\t\t\t<td style=\"text-align: center;\"><strong>";
                echo $this->getPackageName();
                echo " kenmerk</strong></td>\n\t\t\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t\t\t</thead>\n\t\t\t\t\t\t\t\t\t\t<tbody>\n\t\t\t\t\t\t\t\t\t\t";
                $customAccounts = $accounts["value"]["purchase_groups"];
                foreach ($creditor_groups as $tmp_group) {
                    echo "\t\t\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t\t\t<td><strong>";
                    echo htmlspecialchars($tmp_group["GroupName"]);
                    echo "</strong></td>\n\t\t\t\t\t\t\t\t\t\t\t\t<td style=\"text-align: center;\"><input type=\"text\" class=\"text1 size6\" name=\"purchase_group[";
                    echo $tmp_group["id"];
                    echo "]\" value=\"";
                    echo isset($customAccounts[$tmp_group["id"]]["id"]) && 0 < $customAccounts[$tmp_group["id"]]["id"] ? $customAccounts[$tmp_group["id"]]["id"] : "";
                    echo "\" style=\"text-align: center;\" /></td>\n\t\t\t\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t\t\t\t";
                }
                echo "\t\t\t\t\t\t\t\t\t\t</tbody>\n\t\t\t\t\t\t\t\t\t</table>\n\t\t\t\t\t\t\t\t\t";
            }
            echo "\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\t\t\t\t\t";
        }
        echo "\t\t\t</div>\n\t\t</div>\n\t\t";
    }
    public function getSDDBatches()
    {
        $status = "downloaded|processed";
        require_once "class/directdebit.php";
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
            $result = Database_Model::getInstance()->get("HostFact_ExportHistory", ["*"])->where("Package", $this->package)->where("Type", "sddbatch")->where("ReferenceID", ["IN" => array_keys($batches_array)])->execute();
            if($result) {
                foreach ($result as $_batch) {
                    $batches_array[$_batch->ReferenceID]["ExportedAt"] = $_batch->ExportedAt;
                    $batches_array[$_batch->ReferenceID]["PackageReference"] = $_batch->PackageReference;
                }
            }
        }
        return $batches_array;
    }
    protected function _getSDDBatches($filters = [])
    {
        $batches_array = $this->getSDDBatches();
        if(isset($filters["sddbatches"]["batch"]) && $filters["sddbatches"]["batch"]["min"] && $filters["sddbatches"]["batch"]["max"]) {
            foreach ($batches_array as $_key => $_batch) {
                if("SDD" . $_key < $filters["sddbatches"]["batch"]["min"] || $filters["sddbatches"]["batch"]["max"] < "SDD" . $_key) {
                    unset($batches_array[$_key]);
                }
            }
            require_once "class/directdebit.php";
            $directdebit = new directdebit();
            foreach ($batches_array as $_key => $_batch) {
                $transactions = $directdebit->getBatchTransactions($_batch["BatchID"], false);
                foreach ($transactions as $_transaction) {
                    if(!isset($batches_array[$_key]["Transactions"][$_transaction["MandateType"]])) {
                        $batches_array[$_key]["Transactions"][$_transaction["MandateType"]] = ["Amount" => 0, "Lines" => []];
                    }
                    $batches_array[$_key]["Transactions"][$_transaction["MandateType"]]["Amount"] += $_transaction["Amount"];
                    $batches_array[$_key]["Transactions"][$_transaction["MandateType"]]["Lines"][] = $_transaction;
                }
            }
            return $batches_array;
        } else {
            $this->Error[] = __("export no data available");
            return false;
        }
    }
    protected function logExportedItem($type, $reference_id, $package_reference = "")
    {
        Database_Model::getInstance()->insert("HostFact_ExportHistory", ["ExportedAt" => ["RAW" => "NOW()"], "Package" => $this->package, "Type" => $type, "ReferenceID" => $reference_id, "PackageReference" => $package_reference])->execute();
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
        global $array_country;
        global $array_taxpercentages;
        $taxrules = $this->getTaxRules();
        if(!empty($taxrules)) {
            echo "\t\t\t<br />\n\t\t\t<table border=\"0\" cellpadding=\"0\" cellspacing=\"3\">\n\t\t\t\t<thead>\n\t\t\t\t<tr class=\"trtitle\">\n\t\t\t\t\t<td scope=\"col\" width=\"200\">&nbsp;</strong></td>\n\t\t\t\t\t<td scope=\"col\" width=\"310\"><strong>";
            echo __("taxrules apply to");
            echo "</strong></td>\n\t\t\t\t\t<td width=\"310\"><strong>";
            echo __("export accounting - revenue account");
            echo "</strong></td>\n\n\t\t\t\t\t";
            if(!empty($vatcode_options)) {
                echo "<td width=\"310\"><strong>";
                echo __("export accounting - vat code");
                echo "</strong></td>";
            }
            if(!empty($balance_options)) {
                echo "<td><strong>";
                echo __("export accounting - vat to pay");
                echo "</strong></td>";
            }
            echo "\n\t\t\t\t</tr>\n\t\t\t\t</thead>\n\t\t\t\t<tbody>\n\t\t\t\t";
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
                                echo "\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td><strong>";
                                echo $countryname;
                                echo "</strong></td>\n\t\t\t\t\t\t<td>";
                                echo $restriction_name;
                                echo "</td>\n\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t<select name=\"taxrules[";
                                echo $_taxhash;
                                echo "][revenue]\" class=\"text1 size4f\">\n\t\t\t\t\t\t\t\t<option value=\"\">";
                                echo __("please choose");
                                echo "</option>\n\t\t\t\t\t\t\t\t";
                                if(!$taxRules_info["value"][$_taxhash]["revenue"] && $help_me_fill) {
                                    $taxRules_info["value"][$_taxhash]["revenue"] = $accounts["value"]["default"]["revenue" . $help_me_fill]["id"];
                                }
                                foreach ($account_options as $_key => $_value) {
                                    echo "\t\t\t\t\t\t\t\t\t<option value=\"";
                                    echo $_key;
                                    echo "\" ";
                                    if((string) $_key == (string) $taxRules_info["value"][$_taxhash]["revenue"]) {
                                        echo " selected=\"selected\"";
                                    }
                                    echo ">";
                                    echo $_key . " - " . $_value["title"];
                                    echo "</option>\n\t\t\t\t\t\t\t\t\t";
                                }
                                echo "\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t</td>\n\t\t\t\t\t";
                                if(!empty($vatcode_options)) {
                                    echo "\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t<select name=\"taxrules[";
                                    echo $_taxhash;
                                    echo "][taxcode]\" class=\"text1 size4f\">\n\t\t\t\t\t\t\t\t<option value=\"\">";
                                    echo __("please choose");
                                    echo "</option>\n\t\t\t\t\t\t\t\t";
                                    $selected_vat_code = "";
                                    if(!$taxRules_info["value"][$_taxhash]["taxcode"] && $help_me_fill) {
                                        $taxRules_info["value"][$_taxhash]["taxcode"] = $accounts["value"]["default"]["taxcode" . $help_me_fill]["id"];
                                    }
                                    foreach ($vatcode_options as $_key => $_value) {
                                        if((string) $_key == (string) $taxRules_info["value"][$_taxhash]["taxcode"]) {
                                            $selected_vat_code = $_key;
                                        }
                                        echo "\t\t\t\t\t\t\t\t\t<option value=\"";
                                        echo $_key;
                                        echo "\" ";
                                        if((string) $_key == (string) $selected_vat_code) {
                                            echo " selected=\"selected\"";
                                        }
                                        echo ">";
                                        echo $_key . " - " . $_value["title"];
                                        echo "</option>\n\t\t\t\t\t\t\t\t\t";
                                    }
                                    echo "\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t</td>\n\t\t\t\t\t\t";
                                }
                                if(!empty($balance_options)) {
                                    echo "\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t<select name=\"taxrules[";
                                    echo $_taxhash;
                                    echo "][tax]\" class=\"text1 size4f\">\n\t\t\t\t\t\t\t\t\t<option value=\"\">";
                                    echo __("please choose");
                                    echo "</option>\n\t\t\t\t\t\t\t\t\t";
                                    if(!$taxRules_info["value"][$_taxhash]["tax"] && $help_me_fill) {
                                        $taxRules_info["value"][$_taxhash]["tax"] = $accounts["value"]["default"]["tax" . $help_me_fill]["id"];
                                    }
                                    foreach ($balance_options as $_key => $_value) {
                                        if(!$taxRules_info["value"][$_taxhash]["tax"] && $selected_vat_code && isset($vatcode_options[$selected_vat_code]["account"])) {
                                            $taxRules_info["value"][$_taxhash]["tax"] = (string) $vatcode_options[$selected_vat_code]["account"];
                                        }
                                        echo "\t\t\t\t\t\t\t\t\t\t<option value=\"";
                                        echo $_key;
                                        echo "\" ";
                                        if((string) $_key == (string) $taxRules_info["value"][$_taxhash]["tax"]) {
                                            echo " selected=\"selected\"";
                                        }
                                        echo ">";
                                        echo $_key . " - " . $_value["title"];
                                        echo "</option>\n\t\t\t\t\t\t\t\t\t\t";
                                    }
                                    echo "\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t";
                                }
                                echo "\t\t\t\t\t</tr>\n\t\t\t\t\t";
                        }
                }
            }
            echo "\t\t\t\t</tbody>\n\t\t\t</table>\n\t\t\t";
        }
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
            global $array_country_EU;
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
            $eu_countries = [];
            if($array_country_EU && is_array($array_country_EU)) {
                foreach ($array_country_EU as $Code => $name) {
                    if(!is_numeric($Code)) {
                        $eu_countries[] = $Code;
                    }
                }
            }
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
    public function getVersionInformation()
    {
        $version = [];
        if(file_exists("3rdparty/export/" . $this->package . "/version.php")) {
            include "3rdparty/export/" . $this->package . "/version.php";
        }
        return $version;
    }
}

?>