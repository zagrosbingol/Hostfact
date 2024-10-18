<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "class/whois.php";
class registrar
{
    public $Identifier;
    public $Name;
    public $Class;
    public $License;
    public $User;
    public $Password;
    public $DNS1;
    public $DNS2;
    public $DNS3;
    public $DefaultDNSTemplate;
    public $Testmode;
    public $Status;
    public $AdminCustomer;
    public $AdminHandle;
    public $TechCustomer;
    public $TechHandle;
    public $Setting1;
    public $Setting2;
    public $Setting3;
    public $DomainEnabled;
    public $SSLEnabled;
    public $api;
    public $CountRows;
    public $Error;
    public $Warning;
    public $Success;
    public $Variables;
    public function __construct()
    {
        $this->Status = 1;
        $this->Testmode = 0;
        $this->License = $_SERVER["SERVER_ADDR"];
        $this->Variables = ["Identifier", "Name", "Class", "License", "User", "Password", "DNS1", "DNS2", "DNS3", "Testmode", "Status", "AdminCustomer", "AdminHandle", "TechCustomer", "TechHandle", "Setting1", "Setting2", "Setting3", "DomainEnabled", "SSLEnabled", "DefaultDNSTemplate"];
        $this->AdminCustomer = "yes";
        $this->AdminHandle = "0";
        $this->TechCustomer = "yes";
        $this->TechHandle = "0";
        $this->domain_admin_customer = "1";
        $this->domain_admin_handle = 0;
        $this->domain_tech_customer = "1";
        $this->domain_tech_handle = 0;
        $this->Setting1 = $this->Setting2 = $this->Setting3 = "";
        $this->DomainEnabled = "yes";
        $this->SSLEnabled = "no";
        $this->Error = $this->Warning = $this->Success = [];
    }
    public function __destruct()
    {
    }
    public function show($id = NULL)
    {
        $this->Identifier = 0 < $id ? $id : $this->Identifier;
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for registrar");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_Registrar", ["*"])->where("id", $this->Identifier)->execute();
        if(!$result || empty($result->id)) {
            $this->Error[] = __("invalid identifier for registrar");
            return false;
        }
        foreach ($result as $key => $value) {
            $this->{$key} = htmlspecialchars($value);
        }
        $this->OldPassword = $this->Password;
        $this->domain_admin_customer = $this->AdminCustomer == "yes" ? "1" : "0";
        $this->domain_admin_handle = $this->AdminHandle;
        $this->domain_tech_customer = $this->TechCustomer == "yes" ? "1" : "0";
        $this->domain_tech_handle = $this->TechHandle;
        return true;
    }
    public function search($tld = NULL)
    {
        if(!$tld) {
            $this->Error[] = __("invalid identifier for registrar");
            return false;
        }
        $result = Database_Model::getInstance()->getOne(["HostFact_Registrar", "HostFact_TopLevelDomain"], ["HostFact_Registrar.*"])->where("HostFact_TopLevelDomain.Tld", $tld)->where("HostFact_TopLevelDomain.Registrar = HostFact_Registrar.id")->execute();
        if(!$result || empty($result->id)) {
            $this->Error[] = __("invalid identifier for registrar");
            return false;
        }
        foreach ($result as $key => $value) {
            $this->{$key} = htmlspecialchars($value);
        }
        $this->Identifier = $result->id;
        $this->OldPassword = $this->Password;
        $this->domain_admin_customer = $this->AdminCustomer == "yes" ? "1" : "0";
        $this->domain_admin_handle = $this->AdminHandle;
        $this->domain_tech_customer = $this->TechCustomer == "yes" ? "1" : "0";
        $this->domain_tech_handle = $this->TechHandle;
        return true;
    }
    public function checkCredentials()
    {
        if($this->getAPI() && isset($this->api) && method_exists($this->api, "checkLogin")) {
            if(!$this->api->checkLogin()) {
                if(!empty($this->api->Error)) {
                    $this->Warning[] = sprintf(__("third party login check failed with error"), implode("<br />", $this->api->Error));
                } else {
                    $this->Warning[] = sprintf(__("third party login check failed without error"));
                }
            } else {
                $this->Success[] = sprintf(__("third party login check success"));
            }
        }
    }
    public function add()
    {
        if(!$this->validate()) {
            return false;
        }
        if($this->Password) {
            $this->Password = passcrypt($this->Password);
        }
        $this->checkCredentials();
        $result = Database_Model::getInstance()->insert("HostFact_Registrar", ["Name" => $this->Name, "Class" => $this->Class, "License" => $this->License, "User" => $this->User, "Password" => $this->Password, "DNS1" => $this->DNS1, "DNS2" => $this->DNS2, "DNS3" => $this->DNS3, "Testmode" => $this->Testmode, "Status" => $this->Status, "AdminCustomer" => $this->AdminCustomer, "TechCustomer" => $this->TechCustomer, "Setting1" => $this->Setting1, "Setting2" => $this->Setting2, "Setting3" => $this->Setting3, "DomainEnabled" => $this->DomainEnabled, "SSLEnabled" => $this->SSLEnabled])->execute();
        if($result) {
            $this->Identifier = $result;
            if(is_module_active("dnsmanagement")) {
                global $_module_instances;
                $dnsmanagement = $_module_instances["dnsmanagement"];
                $dnsmanagement->integration_is_added($this, "registrar");
                $this->Error = array_merge($this->Error, $dnsmanagement->Error);
                $this->Success = array_merge($this->Success, $dnsmanagement->Success);
            }
            if($this->DomainEnabled == "yes" && $this->getAPI() && isset($this->api->AvailableExtraFields) && is_array($this->api->AvailableExtraFields)) {
                foreach ($this->api->AvailableExtraFields as $tld => $fields) {
                    foreach ($fields as $field) {
                        Database_Model::getInstance()->insert("HostFact_Domain_Extra_Fields", ["Registrar" => $this->Identifier, "Tld" => $tld, "RegistrarField" => $field["Field"], "LabelTitle" => $field["Title"], "LabelType" => $field["Type"], "LabelOptions" => $field["Options"], "LabelDefault" => $field["Default"]])->execute();
                    }
                }
            }
            array_unshift($this->Success, sprintf(__("registrar is created"), $this->Name));
            return true;
        } else {
            return false;
        }
    }
    public function edit($id)
    {
        if(!is_numeric($id)) {
            $this->Error[] = __("invalid identifier for registrar");
            return false;
        }
        if(!$this->validate()) {
            return false;
        }
        if(!$this->Password) {
            $this->Password = htmlspecialchars_decode($this->OldPassword);
        } else {
            $this->Password = passcrypt($this->Password);
        }
        $this->checkCredentials();
        if($this->Class == "") {
            $this->User = $this->Password = $this->Setting1 = $this->Setting2 = $this->Setting3 = "";
        }
        $result = Database_Model::getInstance()->update("HostFact_Registrar", ["Name" => $this->Name, "Class" => $this->Class, "License" => $this->License, "User" => $this->User, "Password" => $this->Password, "DNS1" => $this->DNS1, "DNS2" => $this->DNS2, "DNS3" => $this->DNS3, "Testmode" => $this->Testmode, "Status" => $this->Status, "AdminCustomer" => $this->AdminCustomer, "AdminHandle" => $this->AdminHandle, "TechCustomer" => $this->TechCustomer, "TechHandle" => $this->TechHandle, "Setting1" => $this->Setting1, "Setting2" => $this->Setting2, "Setting3" => $this->Setting3, "DomainEnabled" => $this->DomainEnabled, "SSLEnabled" => $this->SSLEnabled])->where("id", $id)->execute();
        if($result) {
            $this->Identifier = $id;
            if(is_module_active("dnsmanagement")) {
                global $_module_instances;
                $dnsmanagement = $_module_instances["dnsmanagement"];
                $dnsmanagement->integration_is_added($this, "registrar");
                $this->Error = array_merge($this->Error, $dnsmanagement->Error);
                $this->Success = array_merge($this->Success, $dnsmanagement->Success);
            }
            if($this->DomainEnabled == "yes" && $this->getAPI() && isset($this->api->AvailableExtraFields) && is_array($this->api->AvailableExtraFields)) {
                $current_fields = [];
                $result_fields = Database_Model::getInstance()->get("HostFact_Domain_Extra_Fields", ["Tld", "RegistrarField"])->where("Registrar", $this->Identifier)->execute();
                if($result_fields && is_array($result_fields)) {
                    foreach ($result_fields as $var) {
                        $current_fields[$var->Tld][] = $var->RegistrarField;
                    }
                }
                foreach ($this->api->AvailableExtraFields as $tld => $fields) {
                    foreach ($fields as $field) {
                        if(is_array($current_fields["all"]) && in_array($field["Field"], $current_fields["all"]) || is_array($current_fields[$tld]) && in_array($field["Field"], $current_fields[$tld])) {
                        } else {
                            Database_Model::getInstance()->insert("HostFact_Domain_Extra_Fields", ["Registrar" => $this->Identifier, "Tld" => $tld, "RegistrarField" => $field["Field"], "LabelTitle" => $field["Title"], "LabelType" => $field["Type"], "LabelOptions" => $field["Options"], "LabelDefault" => $field["Default"]])->execute();
                        }
                    }
                }
            }
            $this->Success[] = sprintf(__("registrar is adjusted"), $this->Name);
            return true;
        } else {
            return false;
        }
    }
    public function delete($id)
    {
        if(!is_numeric($id)) {
            $this->Error[] = __("invalid identifier for registrar");
            return false;
        }
        $result_st = Database_Model::getInstance()->getOne("HostFact_Registrar", ["Name"])->where("id", $id)->execute();
        if(!$result_st) {
            $this->Error[] = __("invalid identifier for registrar");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_Domains", ["COUNT(`id`) AS CountPresent"])->where("Registrar", $id)->where("Status", ["!=" => "-1"])->where("Status", ["!=" => "9"])->execute();
        if($result) {
            if($result->CountPresent == 1) {
                $this->Error[] = __("cannot delete registrar connected to domains");
                return false;
            }
            $result = Database_Model::getInstance()->update("HostFact_Registrar", ["Status" => "9"])->where("id", $id)->execute();
            if(!$result) {
                return false;
            }
            $result = Database_Model::getInstance()->update("HostFact_TopLevelDomain", ["Registrar" => "0"])->where("Registrar", $id)->execute();
            if(!$result) {
                return false;
            }
            $this->Success[] = sprintf(__("registrar is removed"), $result_st->Name);
            return true;
        }
        return false;
    }
    public function validate()
    {
        if(!trim($this->Name)) {
            $this->Error[] = __("please enter a registrar name");
        }
        if(!$this->is_free($this->Name)) {
            $this->Error[] = __("registrar name already in use");
        }
        global $array_registrarstatus;
        if(!array_key_exists($this->Status, $array_registrarstatus)) {
            $this->Error[] = __("unknown status selected for registrar");
        }
        if(!empty($this->Class) && !@file_exists("3rdparty/domain/" . $this->Class . "/" . $this->Class . ".php")) {
            $this->Error[] = __("files for selected registrar not found");
        }
        return empty($this->Error) ? true : false;
    }
    public function all($fields, $sort = "Name", $order = "ASC", $limit = "-1", $searchat = false, $searchfor = false, $group = false, $show_results = MAX_RESULTS_LIST)
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
        $search_at = [];
        if($searchat && $searchfor) {
            $search_at = explode("|", $searchat);
        }
        $select = ["id"];
        foreach ($fields as $column) {
            $select[] = $column;
        }
        Database_Model::getInstance()->get("HostFact_Registrar", $select);
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
            Database_Model::getInstance()->orderBy("Name", $order);
        }
        if(0 <= $limit) {
            Database_Model::getInstance()->limit((max(1, $limit) - 1) * $show_results, $show_results);
        }
        if(is_string($group) && 1 < count(explode("|", $group))) {
            $group = explode("|", $group);
            Database_Model::getInstance()->where("Status", ["IN" => $group]);
        } elseif(is_numeric($group)) {
            Database_Model::getInstance()->where("Status", $group);
        } else {
            Database_Model::getInstance()->where("Status", ["<" => "9"]);
        }
        $list = [];
        $list["CountRows"] = 0;
        if($server_list = Database_Model::getInstance()->execute()) {
            $list["CountRows"] = Database_Model::getInstance()->rowCount("HostFact_Registrar", "id");
            foreach ($server_list as $result) {
                $list[$result->id] = ["id" => $result->id];
                foreach ($fields as $column) {
                    $list[$result->id][$column] = htmlspecialchars($result->{$column});
                }
            }
        }
        return $list;
    }
    public function getAPIs($only_in_use = false)
    {
        $list = scandir("3rdparty/domain/");
        $api_list = [];
        foreach ($list as $dir) {
            if($dir != "." && $dir != ".." && is_dir("3rdparty/domain/" . $dir) && strpos($dir, "-outdated") === false) {
                $file_list = scandir("3rdparty/domain/" . $dir . "/");
                if(in_array("version.php", $file_list)) {
                    $version = [];
                    include "3rdparty/domain/" . $dir . "/version.php";
                    if(!isset($version["version"])) {
                        $version["version"] = $version["wefact_version"];
                    }
                    $api_list[$dir] = $version;
                }
            }
        }
        if($only_in_use === true) {
            $result = Database_Model::getInstance()->get("HostFact_Registrar", "Class")->where("Class", ["!=" => ""])->where("User", ["!=" => ""])->where("Password", ["!=" => ""])->where("Status", ["!=" => "9"])->groupBy("Class")->execute();
            $registrars_in_use = [];
            if(isset($result) && !empty($result)) {
                foreach ($result as $_registrar) {
                    $registrars_in_use[$_registrar->Class] = $_registrar->Class;
                }
            }
            $api_list = array_intersect_key($api_list, $registrars_in_use);
        }
        return $api_list;
    }
    public function getAPI()
    {
        $registrarName = $this->Class;
        if($registrarName && @file_exists("3rdparty/domain/" . $registrarName . "/" . $registrarName . ".php")) {
            require_once "3rdparty/domain/" . $registrarName . "/" . $registrarName . ".php";
            $this->api = new $registrarName();
            $this->api->User = $this->User;
            $this->api->Password = passcrypt($this->Password);
            $this->api->Testmode = $this->Testmode;
            $this->api->License = $this->License;
            $this->api->Setting1 = $this->Setting1;
            $this->api->Setting2 = $this->Setting2;
            $this->api->Setting3 = $this->Setting3;
            return true;
        }
        if($registrarName) {
            $this->Error[] = sprintf(__("registrar module not found"), $this->Name);
            return false;
        }
    }
    public function getVersionInfo()
    {
        $this->VersionInfo = NULL;
        if(@file_exists("3rdparty/domain/" . $this->Class . "/version.php")) {
            include "3rdparty/domain/" . $this->Class . "/version.php";
            $this->VersionInfo = $version;
            return true;
        }
        return false;
    }
    public function importDomainList()
    {
        $result2 = [];
        if(!$this->api && !$this->getAPI()) {
            return [];
        }
        $result = $this->api->getDomainList();
        if(isset($this->api->Error) && 0 < count($this->api->Error)) {
            $this->Error = array_merge($this->Error, $this->api->Error);
            return [];
        }
        return $result;
    }
    public function importDomainInformation($domain)
    {
        if(!$this->api && !$this->getAPI()) {
            return [];
        }
        $result = $this->api->getDomainInformation($domain);
        if(!empty($this->api->Error)) {
            $this->Error = array_merge($this->Error, $this->api->Error);
            return $result;
        }
        return $result;
    }
    public function importHandleInformation($handle)
    {
        if(!$this->api && !$this->getAPI()) {
            return [];
        }
        $result2 = $this->api->getContact($handle);
        $tmp = [];
        $tmp["Handle"] = $handle;
        $tmp["Initials"] = $result2->ownerInitials;
        $tmp["SurName"] = $result2->ownerSurName;
        $tmp["Address"] = isset($result2->ownerStreetName) ? $result2->ownerStreetName . " " . $result2->ownerStreetNumber : $result2->ownerAddress;
        $tmp["ZipCode"] = $result2->ownerZipCode;
        $tmp["City"] = $result2->ownerCity;
        $tmp["Country"] = $result2->ownerCountry;
        $tmp["PhoneNumber"] = $result2->ownerPhoneNumber;
        $tmp["FaxNumber"] = $result2->ownerFaxNumber;
        $tmp["EmailAddress"] = $result2->ownerEmailAddress;
        $tmp["Sex"] = "";
        $tmp["CompanyName"] = $result2->ownerCompanyName;
        $tmp["TaxNumber"] = "";
        $tmp["HandleType"] = "NONSIDN";
        return $tmp;
    }
    public function importHandleList()
    {
        $result2 = [];
        if(!$this->api && !$this->getAPI()) {
            return [];
        }
        $version = $this->api->getVersionInformation();
        if(!isset($version["handle_support"]) || !$version["handle_support"]) {
            $this->Error[] = __("handles not supported by registrar");
            return [];
        }
        $result = $this->api->getContactList();
        if(!empty($this->api->Error)) {
            $this->Error = array_merge($this->Error, $this->api->Error);
            return [];
        }
        $array_list = [];
        foreach ($result as $handle) {
            if(in_array($this->Class, ["inforbusiness", "opendomainregistry", "autodns", "oxxa", "bnamed", "internetx", "xxlwebhosting"])) {
                $tmp = [];
                $tmp["Handle"] = $handle["Handle"];
                $tmp["Initials"] = $handle["Initials"];
                $tmp["SurName"] = $handle["SurName"];
                $tmp["CompanyName"] = $handle["CompanyName"];
                $tmp["HandleType"] = "NONSIDN";
            } elseif(isset($handle["EmailAddress"])) {
                $tmp = $handle;
                $tmp["HandleType"] = "NONSIDN";
            } else {
                $result2 = $this->api->getContact($handle["Handle"]);
                $tmp = [];
                $tmp["Handle"] = $handle["Handle"];
                $tmp["Initials"] = $result2->ownerInitials;
                $tmp["SurName"] = $result2->ownerSurName;
                $tmp["Address"] = $result2->ownerAddress ? $result2->ownerAddress : $result2->ownerStreetName . " " . $result2->ownerStreetNumber;
                $tmp["ZipCode"] = $result2->ownerZipCode;
                $tmp["City"] = $result2->ownerCity;
                $tmp["Country"] = $result2->ownerCountry;
                $tmp["PhoneNumber"] = $result2->ownerPhoneNumber;
                $tmp["FaxNumber"] = $result2->ownerFaxNumber;
                $tmp["EmailAddress"] = $result2->ownerEmailAddress;
                $tmp["Sex"] = "";
                $tmp["CompanyName"] = $result2->ownerCompanyName;
                $tmp["TaxNumber"] = "";
                $tmp["HandleType"] = "NONSIDN";
            }
            $array_list[] = $tmp;
        }
        return $array_list;
    }
    public function countNumberOfDomains()
    {
        $count_list = [];
        $result = Database_Model::getInstance()->get("HostFact_Domains", ["COUNT(`id`) as `Count`", "Registrar"])->where("Status", ["!=" => "9"])->groupBy("Registrar")->execute();
        if($result && is_array($result)) {
            foreach ($result as $var) {
                $count_list[$var->Registrar] = $var->Count;
            }
        }
        return $count_list;
    }
    public function updateDefaultHandle($id, $role, $handle_id)
    {
        if(!is_numeric($id)) {
            $this->Error[] = __("invalid identifier for registrar");
            return false;
        }
        if($role != "admin" && $role != "tech") {
            $this->Error[] = __("invalid role for default handle");
            return false;
        }
        if($role == "admin") {
            $result = Database_Model::getInstance()->update("HostFact_Registrar", ["AdminCustomer" => "no", "AdminHandle" => $handle_id])->where("id", $id)->execute();
        } elseif($role == "tech") {
            $result = Database_Model::getInstance()->update("HostFact_Registrar", ["TechCustomer" => "no", "TechHandle" => $handle_id])->where("id", $id)->execute();
        }
        if($result) {
            return true;
        }
        return false;
    }
    public function is_free($registrarname)
    {
        $result = Database_Model::getInstance()->getOne("HostFact_Registrar", "id")->where("Name", $registrarname)->where("Status", ["!=" => 9])->execute();
        if(0 < $result->id) {
            if($result->id == $this->Identifier) {
                return true;
            }
            return false;
        }
        return true;
    }
}

?>