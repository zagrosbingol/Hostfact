<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class package
{
    public $Identifier;
    public $PackageName;
    public $PackageType;
    public $Product;
    public $Server;
    public $Template;
    public $TemplateName;
    public $Status;
    public $BandWidth;
    public $uBandWidth;
    public $DiscSpace;
    public $uDiscSpace;
    public $Domains;
    public $uDomains;
    public $SubDomains;
    public $uSubDomains;
    public $IPs;
    public $uIPs;
    public $EmailAccounts;
    public $uEmailAccounts;
    public $EmailForwarders;
    public $uEmailForwarders;
    public $MailingLists;
    public $uMailingLists;
    public $Autoresponders;
    public $uAutoresponders;
    public $MySQLDatabases;
    public $uMySQLDatabases;
    public $Domainpointers;
    public $uDomainpointers;
    public $FTPAccounts;
    public $uFTPAccounts;
    public $AnonFTP;
    public $CGIAccess;
    public $PHPAccess;
    public $SpamAssasin;
    public $SSLAccess;
    public $SSHAccess;
    public $SSHUserAccess;
    public $Cronjobs;
    public $Sysinfo;
    public $DNSControl;
    public $PersonalDNS;
    public $SharedIP;
    public $EmailTemplate;
    public $EmailAuto;
    public $PdfTemplate;
    public $CountRows;
    public $Error;
    public $Warning;
    public $Succes;
    public $Variables;
    public function __construct()
    {
        $this->PackageType = "normal";
        $this->Template = "yes";
        $this->Status = 1;
        $this->EmailAuto = "yes";
        $this->Variables = ["Identifier", "PackageName", "PackageType", "Product", "Server", "Template", "TemplateName", "Status", "BandWidth", "uBandWidth", "DiscSpace", "uDiscSpace", "Domains", "uDomains", "SubDomains", "uSubDomains", "IPs", "uIPs", "EmailAccounts", "uEmailAccounts", "EmailForwarders", "uEmailForwarders", "MailingLists", "uMailingLists", "Autoresponders", "uAutoresponders", "MySQLDatabases", "uMySQLDatabases", "Domainpointers", "uDomainpointers", "FTPAccounts", "uFTPAccounts", "AnonFTP", "CGIAccess", "PHPAccess", "SpamAssasin", "SSLAccess", "SSHAccess", "SSHUserAccess", "Cronjobs", "Sysinfo", "DNSControl", "PersonalDNS", "SharedIP", "EmailTemplate", "EmailAuto", "PdfTemplate"];
        $this->Error = [];
        $this->Warning = [];
        $this->Succes = [];
    }
    public function __destruct()
    {
    }
    public function show($id = NULL)
    {
        $id = 0 < $id ? $id : $this->Identifier;
        if(!is_numeric($id)) {
            $this->Error[] = __("invalid identifier for package");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_Packages", ["HostFact_Packages.*", "HostFact_Products.ProductCode"])->join("HostFact_Products", "HostFact_Products.id = HostFact_Packages.Product")->where("HostFact_Packages.id", $id)->execute();
        if(!$result || empty($result->id)) {
            $this->Error[] = __("invalid identifier for package");
            return false;
        }
        foreach ($result as $key => $value) {
            $this->{$key} = htmlspecialchars($value);
        }
        return true;
    }
    public function search()
    {
        if(!$this->Product) {
            $this->Error[] = __("invalid identifier for package");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_Packages", ["id"])->where("Product", $this->Product)->execute();
        if(!$result || empty($result->id)) {
            $this->Error[] = __("invalid identifier for package");
            return false;
        }
        return $result->id;
    }
    public function checkbox($value)
    {
        return $value == "ON" ? 1 : 0;
    }
    public function add()
    {
        if(!$this->validate()) {
            return false;
        }
        $result = Database_Model::getInstance()->insert("HostFact_Packages", ["PackageName" => $this->PackageName, "PackageType" => $this->PackageType, "Product" => $this->Product, "Server" => $this->Server, "Template" => $this->Template, "TemplateName" => $this->TemplateName, "BandWidth" => $this->BandWidth, "uBandWidth" => $this->checkbox($this->uBandWidth), "DiscSpace" => $this->DiscSpace, "uDiscSpace" => $this->checkbox($this->uDiscSpace), "Domains" => $this->Domains, "uDomains" => $this->checkbox($this->uDomains), "SubDomains" => $this->SubDomains, "uSubDomains" => $this->checkbox($this->uSubDomains), "IPs" => $this->IPs, "uIPs" => $this->checkbox($this->uIPs), "EmailAccounts" => $this->EmailAccounts, "uEmailAccounts" => $this->checkbox($this->uEmailAccounts), "EmailForwarders" => $this->EmailForwarders, "uEmailForwarders" => $this->checkbox($this->uEmailForwarders), "MailingLists" => $this->MailingLists, "uMailingLists" => $this->checkbox($this->uMailingLists), "Autoresponders" => $this->Autoresponders, "uAutoresponders" => $this->checkbox($this->uAutoresponders), "MySQLDatabases" => $this->MySQLDatabases, "uMySQLDatabases" => $this->checkbox($this->uMySQLDatabases), "Domainpointers" => $this->Domainpointers, "uDomainpointers" => $this->checkbox($this->uDomainpointers), "FTPAccounts" => $this->FTPAccounts, "uFTPAccounts" => $this->checkbox($this->uFTPAccounts), "AnonFTP" => $this->checkbox($this->AnonFTP), "CGIAccess" => $this->checkbox($this->CGIAccess), "PHPAccess" => $this->checkbox($this->PHPAccess), "SpamAssasin" => $this->checkbox($this->SpamAssasin), "SSLAccess" => $this->checkbox($this->SSLAccess), "SSHAccess" => $this->checkbox($this->SSHAccess), "SSHUserAccess" => $this->checkbox($this->SSHUserAccess), "Cronjobs" => $this->checkbox($this->Cronjobs), "Sysinfo" => $this->checkbox($this->Sysinfo), "DNSControl" => $this->checkbox($this->DNSControl), "PersonalDNS" => $this->checkbox($this->PersonalDNS), "SharedIP" => $this->checkbox($this->SharedIP), "EmailTemplate" => $this->EmailTemplate, "EmailAuto" => $this->EmailAuto, "PdfTemplate" => $this->PdfTemplate])->execute();
        if($result) {
            $this->Identifier = $result;
            $this->Success[] = sprintf(__("package created"), $this->PackageName);
            return true;
        }
        return false;
    }
    public function edit($id)
    {
        $id = 0 < $id ? $id : $this->Identifier;
        if(!is_numeric($id)) {
            $this->Error[] = __("invalid identifier for package");
            return false;
        }
        if(!$this->validate()) {
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Packages", ["PackageName" => $this->PackageName, "PackageType" => $this->PackageType, "Product" => $this->Product, "Server" => $this->Server, "Template" => $this->Template, "TemplateName" => $this->TemplateName, "BandWidth" => $this->BandWidth, "uBandWidth" => $this->checkbox($this->uBandWidth), "DiscSpace" => $this->DiscSpace, "uDiscSpace" => $this->checkbox($this->uDiscSpace), "Domains" => $this->Domains, "uDomains" => $this->checkbox($this->uDomains), "SubDomains" => $this->SubDomains, "uSubDomains" => $this->checkbox($this->uSubDomains), "IPs" => $this->IPs, "uIPs" => $this->checkbox($this->uIPs), "EmailAccounts" => $this->EmailAccounts, "uEmailAccounts" => $this->checkbox($this->uEmailAccounts), "EmailForwarders" => $this->EmailForwarders, "uEmailForwarders" => $this->checkbox($this->uEmailForwarders), "MailingLists" => $this->MailingLists, "uMailingLists" => $this->checkbox($this->uMailingLists), "Autoresponders" => $this->Autoresponders, "uAutoresponders" => $this->checkbox($this->uAutoresponders), "MySQLDatabases" => $this->MySQLDatabases, "uMySQLDatabases" => $this->checkbox($this->uMySQLDatabases), "Domainpointers" => $this->Domainpointers, "uDomainpointers" => $this->checkbox($this->uDomainpointers), "FTPAccounts" => $this->FTPAccounts, "uFTPAccounts" => $this->checkbox($this->uFTPAccounts), "AnonFTP" => $this->checkbox($this->AnonFTP), "CGIAccess" => $this->checkbox($this->CGIAccess), "PHPAccess" => $this->checkbox($this->PHPAccess), "SpamAssasin" => $this->checkbox($this->SpamAssasin), "SSLAccess" => $this->checkbox($this->SSLAccess), "SSHAccess" => $this->checkbox($this->SSHAccess), "SSHUserAccess" => $this->checkbox($this->SSHUserAccess), "Cronjobs" => $this->checkbox($this->Cronjobs), "Sysinfo" => $this->checkbox($this->Sysinfo), "DNSControl" => $this->checkbox($this->DNSControl), "PersonalDNS" => $this->checkbox($this->PersonalDNS), "SharedIP" => $this->checkbox($this->SharedIP), "EmailTemplate" => $this->EmailTemplate, "EmailAuto" => $this->EmailAuto, "PdfTemplate" => $this->PdfTemplate])->where("id", $id)->execute();
        if($result) {
            $this->Identifier = $id;
            $this->Success[] = sprintf(__("package adjusted"), $this->PackageName);
            return true;
        }
        return false;
    }
    public function delete($id)
    {
        $id = 0 < $id ? $id : $this->Identifier;
        if(!is_numeric($id)) {
            $this->Error[] = __("invalid identifier for package");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Packages", ["Status" => "9"])->where("id", $id)->execute();
        if($result) {
            $this->Success[] = sprintf(__("package removed"), $this->PackageName);
            Database_Model::getInstance()->update("HostFact_Products", ["PackageID" => "0", "ProductType" => "other"])->where("PackageID", $id)->where("ProductType", "hosting")->execute();
            return true;
        }
        return false;
    }
    public function validate()
    {
        if(!trim($this->PackageName)) {
            $this->Error[] = __("please enter a package name");
        }
        global $array_packagetypes;
        if(!in_array($this->PackageType, array_keys($array_packagetypes))) {
            $this->Error[] = __("invalid package type");
        }
        if($this->Server <= 0) {
            $this->Error[] = __("please select a server");
        }
        if($this->Template == "yes") {
            require_once "class/server.php";
            $server = new server();
            $server->show($this->Server);
            if(!trim($this->TemplateName) && $server->Panel) {
                $this->Error[] = __("please select a package template");
            }
            if(0 < $this->Server && $server->Panel) {
                $list_packages = $server->getListPackages($this->Server);
                if((!is_array($list_packages["user"]) || !in_array($this->TemplateName, $list_packages["user"])) && $this->PackageType == "normal" || (!is_array($list_packages["reseller"]) || !in_array($this->TemplateName, $list_packages["reseller"])) && $this->PackageType == "reseller") {
                    $this->Error[] = __("package template does not exist on server");
                }
            }
        }
        return empty($this->Error) ? true : false;
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
        $ProductArray = ["ProductCode", "ProductName"];
        $ProductFields = 0 < count(array_intersect($ProductArray, $fields)) ? true : false;
        $search_at = [];
        $ProductSearch = false;
        if($searchat && (0 < strlen($searchfor) || $searchfor)) {
            $search_at = explode("|", $searchat);
            $ProductSearch = 0 < count(array_intersect($ProductArray, $search_at)) ? true : false;
        }
        $select = ["HostFact_Packages.id"];
        foreach ($fields as $column) {
            if(in_array($column, $ProductArray)) {
                $select[] = "HostFact_Products.`" . $column . "`";
            } else {
                $select[] = "HostFact_Packages.`" . $column . "`";
            }
        }
        Database_Model::getInstance()->get("HostFact_Packages", $select);
        if($ProductFields || $ProductSearch) {
            Database_Model::getInstance()->join("HostFact_Products", "HostFact_Products.`id` = HostFact_Packages.`Product`");
        }
        if(!empty($search_at)) {
            $or_clausule = [];
            foreach ($search_at as $searchColumn) {
                if(in_array($searchColumn, ["Debtor", "Server"])) {
                    $or_clausule[] = ["HostFact_Packages.`" . $searchColumn . "`", $searchfor];
                } elseif(in_array($searchColumn, $ProductArray)) {
                    $or_clausule[] = ["HostFact_Products.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                } else {
                    $or_clausule[] = ["HostFact_Packages.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                }
            }
            Database_Model::getInstance()->orWhere($or_clausule);
        }
        $order = $sort ? $order ? $order : "ASC" : "";
        if(in_array($sort, $ProductArray)) {
            Database_Model::getInstance()->orderBy("HostFact_Products.`" . $sort . "`", $order);
        } elseif($sort) {
            Database_Model::getInstance()->orderBy("HostFact_Packages." . $sort, $order);
        }
        if(0 <= $limit) {
            Database_Model::getInstance()->limit((max(1, $limit) - 1) * $show_results, $show_results);
        }
        if(is_string($group) && 1 < count(explode("|", $group))) {
            $group = explode("|", $group);
            $or_clausule = [];
            foreach ($group as $status) {
                $or_clausule[] = ["HostFact_Packages.`Status`", $status];
            }
            Database_Model::getInstance()->orWhere($or_clausule);
        } elseif(is_numeric($group)) {
            Database_Model::getInstance()->where("HostFact_Packages.`Status`", $group);
        } else {
            Database_Model::getInstance()->where("HostFact_Packages.`Status`", ["!=" => "9"]);
        }
        $list = [];
        $this->CountRows = 0;
        $list["CountRows"] = 0;
        if($package_list = Database_Model::getInstance()->execute()) {
            $list["CountRows"] = $this->CountRows = Database_Model::getInstance()->rowCount("HostFact_Packages", "HostFact_Packages.id");
            foreach ($package_list as $result) {
                $list[$result->id] = ["id" => $result->id];
                foreach ($fields as $column) {
                    $list[$result->id][$column] = htmlspecialchars($result->{$column});
                }
            }
        }
        return $list;
    }
    public function updatePackageInfo($api_object, $id)
    {
        if(!is_object($api_object) || !is_numeric($id)) {
            $this->Error[] = __("package info could not be updated");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Packages", ["BandWidth" => $api_object->Package_BandWidth, "uBandWidth" => isset($api_object->Package_uBandWidth) ? $api_object->Package_uBandWidth : 0, "DiscSpace" => $api_object->Package_DiscSpace, "uDiscSpace" => isset($api_object->Package_uDiscSpace) ? $api_object->Package_uDiscSpace : 0, "Domains" => $api_object->Package_Domains, "uDomains" => isset($api_object->Package_uDomains) ? $api_object->Package_uDomains : 0, "SubDomains" => $api_object->Package_SubDomains, "uSubDomains" => isset($api_object->Package_uSubDomains) ? $api_object->Package_uSubDomains : 0, "EmailAccounts" => $api_object->Package_EmailAccounts, "uEmailAccounts" => isset($api_object->Package_uEmailAccounts) ? $api_object->Package_uEmailAccounts : 0, "MySQLDatabases" => $api_object->Package_MySQLDatabases, "uMySQLDatabases" => isset($api_object->Package_uMySQLDatabases) ? $api_object->Package_uMySQLDatabases : 0, "Domainpointers" => $api_object->Package_Domainpointers, "uDomainpointers" => isset($api_object->Package_uDomainpointers) ? $api_object->Package_uDomainpointers : 0])->where("id", $id)->execute();
        if(!$result) {
            $this->Error[] = __("package info could not be updated");
            return false;
        }
        foreach ($api_object as $key => $value) {
            if(substr($key, 0, 8) == "Package_") {
                $this->{substr($key, 8)} = $value;
            }
        }
        return true;
    }
    public function updatePackageName($id, $packagename)
    {
        if(!is_numeric($id)) {
            $this->Error[] = __("invalid identifier for package");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Packages", ["PackageName" => $packagename])->where("id", $id)->execute();
        if(!$result) {
            return false;
        }
        return true;
    }
    public function updateProductReference($id, $product_id)
    {
        if(!is_numeric($id)) {
            $this->Error[] = __("invalid identifier for package");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Packages", ["Product" => $product_id])->where("id", $id)->execute();
        if(!$result) {
            return false;
        }
        return true;
    }
}

?>