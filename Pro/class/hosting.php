<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class hosting
{
    public $Identifier;
    public $Debtor;
    public $Product;
    public $PeriodicID;
    public $Package;
    public $Username;
    public $Password;
    public $Domain;
    public $Status;
    public $Server;
    public $Comment;
    public $api;
    public $CountRows;
    public $Error = [];
    public $Warning = [];
    public $Success = [];
    public $Variables;
    public function __construct()
    {
        global $array_hostingstatus;
        $this->StatusList = $array_hostingstatus;
        $this->Status = 1;
        $this->Product = 0;
        $this->Variables = ["Identifier", "Debtor", "Product", "PeriodicID", "Package", "Username", "Password", "Domain", "Status", "Server", "Comment"];
        $this->Periodic_TaxPercentage = STANDARD_TAX;
        $this->Server = 0;
    }
    public function __destruct()
    {
        if(isset($this->Periodic) && is_object($this->Periodic)) {
            $this->Periodic->__destruct();
            unset($this->Periodic);
        }
        if(isset($this->api) && is_object($this->api)) {
            unset($this->api);
        }
    }
    public function show($id = NULL, $extended = true)
    {
        $id = 0 < $id ? $id : $this->Identifier;
        if(!is_numeric($id)) {
            $this->Error[] = __("invalid identifier for hosting");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_Hosting")->where("id", $id)->execute();
        if(!$result || empty($result->id)) {
            $this->Error[] = __("invalid identifier for hosting");
            return false;
        }
        $result->Password = passcrypt($result->Password);
        foreach ($result as $key => $value) {
            $this->{$key} = htmlspecialchars($value);
        }
        $this->UsedBandWidth = false;
        $this->UsedDiscSpace = false;
        require_once "class/package.php";
        $package = new package();
        $package->show($this->Package);
        foreach ($package as $key => $value) {
            if(!in_array($key, $this->Variables) && !isset($this->{$key}) && $key != "Variables") {
                $this->{$key} = $value;
            }
        }
        $package->__destruct();
        unset($package);
        if($extended === true) {
            $this->showExtended($id);
        }
        if(0 < $this->PeriodicID) {
            require_once "class/periodic.php";
            $this->Periodic = new periodic();
            $this->Periodic->show($this->PeriodicID);
            if($this->Periodic->Status == 9 || $this->Periodic->Status == 8 && $this->Periodic->TerminationDate == "") {
                $this->Periodic->delete($this->PeriodicID);
                unset($this->Periodic);
                $this->PeriodicID = 0;
            } else {
                $this->Periodic_Type = "existing";
            }
        }
        $this->Periodic_TaxPercentage = btwcheck($this->Debtor, STANDARD_TAX);
        $this->Identifier = $id;
        return true;
    }
    public function showExtended($id)
    {
        require_once "class/server.php";
        $server = new server();
        $server->show($this->Server);
        if($this->Template == "yes") {
            if($server->Panel != "") {
                $package_info = $server->getPackageInformation($this->TemplateName, $this->PackageType == "reseller" ? true : false);
                if(!$package_info) {
                    $this->Warning[] = sprintf(__("the package from the hostingaccount cannot be found on the server"), $this->TemplateName);
                    $this->BandWidth = 0;
                    $this->uBandWidth = 0;
                    $this->DiscSpace = 0;
                    $this->uDiscSpace = 0;
                }
                if($package_info->Package_BandWidth == "unlimited") {
                    $this->BandWidth = 0;
                    $this->uBandWidth = 1;
                } else {
                    $this->BandWidth = $package_info->Package_BandWidth;
                    $this->uBandWidth = 0;
                }
                if($package_info->Package_DiscSpace == "unlimited") {
                    $this->DiscSpace = 0;
                    $this->uDiscSpace = 1;
                } else {
                    $this->DiscSpace = $package_info->Package_DiscSpace;
                    $this->uDiscSpace = 0;
                }
            } else {
                $this->Warning[] = sprintf(__("the server from hostingaccount is currently down"), $this->Username);
                $this->BandWidth = 0;
                $this->uBandWidth = 0;
                $this->DiscSpace = 0;
                $this->uDiscSpace = 0;
            }
        }
        $this->Domain = trim($this->Domain);
        $this->DomainList[strtolower($this->Domain)] = ["Domain" => $this->Domain, "BandWidth" => __("unknown"), "DiscSpace" => __("unknown")];
        require_once "class/domain.php";
        $domain = new domain();
        $domain_debtor = $domain->all(["Domain", "Tld", "Debtor"], false, false, -1, "HostingID", $id, "-1|1|3|4|5|6|7|8");
        $all_domains_debtor_tmp = $domain->all(["Domain", "Tld", "HostingID"], false, false, -1, "Debtor", $this->Debtor, "-1|1|3|4|5|6|7|8");
        $all_domains_debtor = [];
        foreach ($all_domains_debtor_tmp as $k => $val) {
            if(is_numeric($k) && (int) $val["HostingID"] === 0) {
                $all_domains_debtor[$val["id"]] = $val["Domain"] . "." . $val["Tld"];
            }
        }
        unset($all_domains_debtor_tmp);
        $list_domains_debtor = [];
        foreach ($domain_debtor as $k => $val) {
            if(is_numeric($k) && $val["Debtor"] == $this->Debtor) {
                unset($all_domains_debtor[$k]);
                $list_domains_debtor[strtolower($val["Domain"] . "." . $val["Tld"])] = ["id" => $k, "Domain" => $val["Domain"] . "." . $val["Tld"], "BandWidth" => __("unknown"), "DiscSpace" => __("unknown")];
            }
        }
        unset($domain_debtor);
        if(isset($list_domains_debtor[strtolower($this->Domain)])) {
            $this->DomainList[strtolower($this->Domain)]["id"] = $list_domains_debtor[strtolower($this->Domain)]["id"];
            unset($list_domains_debtor[strtolower($this->Domain)]);
        } else {
            $domain_id = $this->connectDomainToHosting($id, $this->Debtor, $this->Domain);
            if($domain_id !== false) {
                $this->DomainList[strtolower($this->Domain)]["id"] = $domain_id;
            }
            unset($domain_id);
        }
        $this->DomainList = array_merge($this->DomainList, $list_domains_debtor);
        if(0 < $this->Server && isset($server) && is_object($server) && 4 <= $this->Status && $this->Status <= 5 && $this->getPanel()) {
            $this->api->Domain = strtolower($this->Domain);
            $domain_result = $this->api->listDomains($this->Username);
            if(!empty($domain_result)) {
                foreach ($domain_result as $dom) {
                    $dom["Domain"] = strtolower($dom["Domain"]);
                    $dom["Parent"] = isset($dom["Parent"]) ? strtolower($dom["Parent"]) : "";
                    if(isset($this->DomainList[$dom["Domain"]]) && is_array($this->DomainList[$dom["Domain"]])) {
                        $this->DomainList[$dom["Domain"]] = array_merge($this->DomainList[$dom["Domain"]], $dom);
                    } else {
                        if(array_search($dom["Domain"], $all_domains_debtor) !== false) {
                            $dom["id"] = array_search($dom["Domain"], $all_domains_debtor);
                            $this->connectDomainToHosting($id, $this->Debtor, $dom["Domain"]);
                            unset($all_domains_debtor[$dom["id"]]);
                        }
                        $this->DomainList[$dom["Domain"]] = $dom;
                    }
                }
            }
            $this->Error = array_merge($this->Error, $this->api->Error);
        }
    }
    public function add()
    {
        require_once "class/package.php";
        $package = new package();
        $package->show($this->Package);
        if(0 < $this->Server) {
        } else {
            require_once "class/debtor.php";
            $debtor = new debtor();
            $debtor->show($this->Debtor);
            if(0 < $debtor->Server) {
                $result = Database_Model::getInstance()->getOne("HostFact_Packages", ["id"])->where("Product", $this->Product)->where("Server", $debtor->Server)->where("Status", ["!=" => 9])->execute();
                if($result && 0 < $result->id) {
                    $this->Package = $result->id;
                    $package = new package();
                    $package->show($this->Package);
                }
            }
            $this->Server = $package->Server;
        }
        if(empty($this->Product)) {
            $this->Product = $package->Product;
        }
        if(!$this->validate()) {
            return false;
        }
        $result = Database_Model::getInstance()->insert("HostFact_Hosting", ["Debtor" => $this->Debtor, "Product" => $this->Product, "PeriodicID" => $this->PeriodicID, "Package" => $this->Package, "Username" => $this->Username, "Password" => $this->Password, "Domain" => trim($this->Domain), "Status" => $this->Status, "Server" => $this->Server, "Comment" => $this->Comment])->execute();
        if($result) {
            $this->Identifier = $result;
            $tmp_domain = explode(".", $this->Domain, 2);
            $sld = $tmp_domain[0];
            $tld = isset($tmp_domain[1]) ? $tmp_domain[1] : "";
            Database_Model::getInstance()->update("HostFact_Domains", ["HostingID" => $this->Identifier])->where("Domain", $sld)->where("Tld", $tld)->execute();
            createLog("hosting", $this->Identifier, "account created");
            $this->Success[] = sprintf(__("hosting account created"), $this->Username);
            return true;
        }
        return false;
    }
    public function edit($id)
    {
        $id = 0 < $id ? $id : $this->Identifier;
        if(!is_numeric($id)) {
            $this->Error[] = __("invalid identifier for hosting");
            return false;
        }
        require_once "class/package.php";
        $package = new package();
        $package->show($this->Package);
        if(0 < $this->Server) {
        } else {
            require_once "class/debtor.php";
            $debtor = new debtor();
            $debtor->show($this->Debtor);
            if(0 < $debtor->Server) {
                $result = Database_Model::getInstance()->getOne("HostFact_Packages", ["id"])->where("Product", $this->Product)->where("Server", $debtor->Server)->execute();
                if($result && 0 < $result->id) {
                    $this->Package = $result->id;
                    $package = new package();
                    $package->show($this->Package);
                }
            }
            $this->Server = $package->Server;
        }
        if(empty($this->Product)) {
            $this->Product = $package->Product;
        }
        if(!$this->validate()) {
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Hosting", ["Debtor" => $this->Debtor, "Product" => $this->Product, "PeriodicID" => $this->PeriodicID, "Package" => $this->Package, "Username" => $this->Username, "Password" => $this->Password, "Domain" => trim($this->Domain), "Status" => $this->Status, "Server" => $this->Server, "Comment" => $this->Comment])->where("id", $id)->execute();
        if($result) {
            $this->Identifier = $id;
            createLog("hosting", $this->Identifier, "account adjusted");
            $this->Success[] = sprintf(__("hosting account adjusted"), $this->Username);
            return true;
        }
        return false;
    }
    public function changeStatus($status)
    {
        global $array_hostingstatus;
        if(0 < $this->Identifier && array_key_exists($status, $array_hostingstatus)) {
            $result = Database_Model::getInstance()->update("HostFact_Hosting", ["Status" => $status])->where("id", $this->Identifier)->execute();
            if($result) {
                return true;
            }
            return false;
        }
    }
    public function getID($method, $value, $debtor_id = false)
    {
        switch ($method) {
            case "identifier":
                $hosting_id = Database_Model::getInstance()->getOne("HostFact_Hosting", "id")->where("id", intval($value))->execute();
                return $hosting_id !== false ? $hosting_id->id : false;
                break;
            case "username":
                $hosting_id = Database_Model::getInstance()->getOne("HostFact_Hosting", "id")->where("Username", $value)->execute();
                return $hosting_id !== false ? $hosting_id->id : false;
                break;
            case "clientarea":
                $hosting_id = Database_Model::getInstance()->getOne("HostFact_Hosting", ["id"])->where("id", intval($value))->where("Debtor", $debtor_id)->where("Status", ["IN" => [-1, 1, 3, 4, 5, 7]])->execute();
                return $hosting_id !== false && 0 < $debtor_id ? $hosting_id->id : false;
                break;
        }
    }
    public function changeDebtor($new_debtor_id)
    {
        if(!$this->show($this->Identifier, false)) {
            return false;
        }
        $old_debtor_id = $this->Debtor;
        $result = Database_Model::getInstance()->update("HostFact_Hosting", ["Debtor" => $new_debtor_id])->where("id", $this->Identifier)->execute();
        if($result) {
            if(0 < $this->PeriodicID) {
                require_once "class/periodic.php";
                $periodic = new periodic();
                $periodic->Identifier = $this->PeriodicID;
                if(!$periodic->changeDebtor($new_debtor_id)) {
                    return false;
                }
            }
            require_once "class/debtor.php";
            $old_debtor = new debtor();
            $old_debtor->Identifier = $old_debtor_id;
            $old_debtor->show();
            $old_debtor_name = $old_debtor->DebtorCode . " " . ($old_debtor->CompanyName != "" ? $old_debtor->CompanyName : $old_debtor->SurName . ", " . $old_debtor->Initials);
            $debtor = new debtor();
            $debtor->Identifier = $new_debtor_id;
            $debtor->show();
            $debtor_name = $debtor->DebtorCode . " " . ($debtor->CompanyName != "" ? $debtor->CompanyName : $debtor->SurName . ", " . $debtor->Initials);
            createLog("hosting", $this->Identifier, "hosting debtor changed", [$old_debtor_name, $debtor_name]);
            $debtor_link = "[hyperlink_1]debtors.php?page=show&id=" . $new_debtor_id . "[hyperlink_2]" . $debtor_name . "[hyperlink_3]";
            $hosting_link = "[hyperlink_1]hosting.php?page=show&id=" . $this->Identifier . "[hyperlink_2]" . $this->Username . "[hyperlink_3]";
            $this->Success[] = sprintf(__("service transfered to new debtor"), $hosting_link, $debtor_link);
            return true;
        }
        return false;
    }
    public function changeComment($id, $comment = "")
    {
        if(!is_numeric($id)) {
            $this->Error[] = __("invalid identifier for hosting");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Hosting", ["Comment" => $comment])->where("id", $id)->execute();
        if($result) {
            createLog("hosting", $this->Identifier, "comment adjusted");
            $this->Success[] = sprintf(__("hosting comment adjusted"), $this->Domain . "." . $this->Tld);
            return true;
        }
        return false;
    }
    public function delete($id, $confirmPeriodic, $confirmServer, $current_status = NULL)
    {
        if(!is_numeric($id)) {
            $this->Error[] = __("invalid identifier for hosting");
            return false;
        }
        if(!$this->show($id, false)) {
            $this->Error[] = __("invalid identifier for hosting");
            return false;
        }
        if($confirmServer == "remove") {
            if(!$this->remove($id) && is_object($this->api)) {
                return false;
            }
        } elseif($confirmServer == "suspend" && !$this->suspend($id) && is_object($this->api)) {
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Hosting", ["Status" => "9"])->where("id", $id)->execute();
        if(!$result) {
            return false;
        }
        createLog("hosting", $id, "account deleted");
        $this->Success[] = sprintf(__("hosting deleted"), $this->Username);
        $result = Database_Model::getInstance()->update("HostFact_PeriodicElements", ["Status" => "9"])->where("PeriodicType", "hosting")->where("Reference", $id)->execute();
        if(!$result) {
            $this->Error[] = sprintf(__("cannot remove subscription connected to hosting account"), $this->Username);
        }
        $result = Database_Model::getInstance()->update("HostFact_Domains", ["HostingID" => "0"])->where("HostingID", $id)->execute();
        if(!$result) {
            $this->Error[] = sprintf(__("cannot unconnect domains of hosting"), $this->Username);
        }
        $service_info = ["Type" => "hosting", "id" => $this->Identifier, "Debtor" => $this->Debtor];
        do_action("service_is_removed", $service_info);
        return true;
    }
    public function deleteFromDatabase($id)
    {
        if(!is_numeric($id)) {
            $this->Error[] = __("invalid identifier for hosting");
            return false;
        }
        $result = Database_Model::getInstance()->delete("HostFact_Hosting")->where("id", $id)->execute();
        if(!$result) {
            return false;
        }
        Database_Model::getInstance()->delete("HostFact_Log")->where("Reference", $id)->where("Type", "hosting")->execute();
        Database_Model::getInstance()->update("HostFact_PeriodicElements", ["PeriodicType" => "other", "Reference" => "0"])->where("PeriodicType", "hosting")->where("Reference", $id)->execute();
        return true;
    }
    public function validate()
    {
        if($this->Debtor && is_numeric($this->Debtor) && 0 < $this->Debtor) {
            $result = Database_Model::getInstance()->getOne("HostFact_Debtors", ["id"])->where("id", $this->Debtor)->execute();
            if(!$result || $result->id != $this->Debtor) {
                $this->Error[] = __("debtor does not exist");
            }
        } elseif($this->Debtor == "-1" && $this->Status == "-1") {
        } else {
            $this->Error[] = __("no debtor selected");
        }
        if($this->Product && is_numeric($this->Product) && 0 < $this->Product) {
            $result = Database_Model::getInstance()->getOne("HostFact_Products", ["id"])->where("id", $this->Product)->execute();
            if(!$result || $result->id != $this->Product) {
                $this->Error[] = __("product does not exist");
            }
        }
        if(isset($this->PeriodicID) && $this->PeriodicID && is_numeric($this->PeriodicID)) {
            $result = Database_Model::getInstance()->getOne("HostFact_PeriodicElements", ["id"])->where("id", $this->PeriodicID)->where("Debtor", $this->Debtor)->execute();
            if(!$result || $result->id != $this->PeriodicID) {
                $this->Error[] = __("subscription does not exist");
            }
        }
        if(!isset($this->Package) || !$this->Package) {
            $this->Error[] = __("no package selected");
        }
        if(isset($this->Package) && $this->Package && is_numeric($this->Package)) {
            $result = Database_Model::getInstance()->getOne("HostFact_Packages", ["id", "PackageName", "Server"])->where("id", $this->Package)->where("Status", ["!=" => 9])->execute();
            if(!$result || $result->id != $this->Package) {
                $this->Error[] = __("package does not exist");
                return false;
            }
            if($result->Server != $this->Server) {
                $this->Error[] = sprintf(__("package does not exist on server"), htmlspecialchars($result->PackageName));
                return false;
            }
        }
        if(!isset($this->Server) || !$this->Server) {
            $this->Error[] = __("no server selected yet");
        }
        if(isset($this->Server) && $this->Server && is_numeric($this->Server)) {
            $result = Database_Model::getInstance()->getOne("HostFact_Servers", ["id"])->where("id", $this->Server)->where("Status", ["!=" => 9])->execute();
            if(!$result || $result->id != $this->Server) {
                $this->Error[] = __("server does not exist");
            }
        }
        global $array_hostingstatus;
        if(!array_key_exists($this->Status, $array_hostingstatus)) {
            $this->Error[] = __("invalid hosting status");
        }
        if(!trim($this->Username)) {
            $this->Error[] = __("please enter an accountname");
        }
        if(!$this->is_free($this->Username)) {
            $this->Error[] = __("accountname already in use");
        }
        if(!trim($this->Password)) {
            $this->Error[] = __("please enter a password");
        } else {
            $this->Password = passcrypt($this->Password);
        }
        if(0 < strlen(trim($this->Domain)) && !is_domain($this->Domain, true, 6)) {
            $this->Error[] = __("account domain is not a valid domain");
        }
        return empty($this->Error) ? true : false;
    }
    public function is_free($username, $id = NULL)
    {
        if($username) {
            $result = Database_Model::getInstance()->getOne("HostFact_Hosting", ["id"])->where("Username", $username)->where("Status", ["!=" => 9])->orderBy("Debtor", "DESC")->execute();
            if($result && 0 < $result->id) {
                if(0 < $id && $id !== true && $id == $result->id || $result->id == $this->Identifier && $id == NULL) {
                    return true;
                }
                if($id === true) {
                    return $result->id;
                }
                return false;
            }
            return $id === true ? 0 : true;
        }
        return false;
    }
    public function generateNewAccountname($method = ACCOUNT_GENERATION, $companyname = "", $surname = "", $initials = "", $domain = "")
    {
        $maxlength = 8;
        $maxtries = 50;
        switch ($method) {
            case "2":
                $name = $companyname ? $companyname : $surname . $initials;
                $name = preg_replace("/[^a-z0-9]/i", "", htmlspecialchars_decode($name));
                if(preg_match("/([a-z][a-z0-9]*)/i", $name, $matches)) {
                    $name = $matches[1];
                }
                $cur_length = $maxlength;
                $cur_try = 0;
                for ($account = NULL; $cur_try < $maxtries; $cur_try++) {
                    $check_name = substr($name, 0, $cur_length);
                    if(0 < $cur_try) {
                        $check_name .= $cur_try;
                    }
                    if($this->is_free($check_name)) {
                        $account = $check_name;
                        break;
                    }
                    if($maxlength <= $cur_length) {
                        $cur_length--;
                    }
                }
                if($account == NULL) {
                    $this->Error[] = __("could not generate new accountname based on debtor name");
                    return false;
                }
                $account = strtolower($account);
                break;
            case "3":
                if($domain) {
                    $domain = preg_replace("/[^a-z0-9]/i", "", $domain);
                    if(preg_match("/([a-z][a-z0-9]*)/i", $domain, $matches)) {
                        $domain = $matches[1];
                    }
                    $cur_length = $maxlength;
                    $cur_try = 0;
                    for ($account = NULL; $cur_try < $maxtries; $cur_try++) {
                        $check_name = substr($domain, 0, $cur_length);
                        if(0 < $cur_try) {
                            $check_name .= $cur_try;
                        }
                        if($this->is_free($check_name)) {
                            $account = $check_name;
                            break;
                        }
                        if($maxlength <= $cur_length) {
                            $cur_length--;
                        }
                    }
                    if($account == NULL) {
                        $this->Error[] = __("could not generate new accountname based on domain, maximum attempts reached");
                        return false;
                    }
                    $account = strtolower($account);
                } else {
                    return $this->generateNewAccountname("1", $companyname, $surname, $initials, $domain);
                }
                break;
            default:
                $prefix = ACCOUNTCODE_PREFIX;
                $number = ACCOUNTCODE_NUMBER;
                $prefix = parsePrefixVariables($prefix);
                $length = strlen($prefix . $number);
                $result = Database_Model::getInstance()->getOne("HostFact_Hosting", ["Username"])->where("Username", ["LIKE" => $prefix . "%"])->where("LENGTH(`Username`)", [">=" => $length])->where("(SUBSTR(`Username`,:PrefixLength)*1)", [">" => 0])->where("SUBSTR(`Username`,:PrefixLength)", ["REGEXP" => "^[0-9]+\$"])->orderBy("(SUBSTR(`Username`,:PrefixLength)*1)", "DESC")->bindValue("PrefixLength", strlen($prefix) + 1)->execute();
                if($result->Username && is_numeric(substr($result->Username, strlen($prefix)))) {
                    $Code = substr($result->Username, strlen($prefix));
                    $Code = $prefix . @str_repeat("0", @max(@strlen($number) - @strlen(@max($Code + 1, $number)), 0)) . max($Code + 1, $number);
                } else {
                    $Code = $prefix . $number;
                }
                $account = $Code;
                if(!$this->is_free($account)) {
                    $this->Error[] = __("could not generate new free accountname");
                }
                return !empty($this->Error) ? false : $account;
        }
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
        if($group !== false && is_array($group) && 0 < count($group)) {
            $filters = $group;
            if(array_key_exists("status", $group)) {
                $group = $group["status"];
                unset($filters["status"]);
            } else {
                $group = false;
            }
        }
        $DebtorArray = ["DebtorCode", "CompanyName", "SurName", "Initials"];
        $DebtorFields = 0 < count(array_intersect($DebtorArray, $fields)) ? true : false;
        $PackageArray = ["PackageName"];
        $PackageFields = 0 < count(array_intersect($PackageArray, $fields)) ? true : false;
        $ServerArray = ["Name"];
        $ServerFields = 0 < count(array_intersect($ServerArray, $fields)) ? true : false;
        $SubscriptionArray = ["PriceExcl", "Periods", "Periodic", "TerminationDate", "NextDate", "StartPeriod", "EndPeriod", "ProductCode", "Description", "Number", "TaxPercentage", "AutoRenew", "DiscountPercentage"];
        $SubscriptionFields = 0 < count(array_intersect($SubscriptionArray, $fields)) ? true : false;
        $TerminationArray = ["TerminationID", "TerminatedDate", "Termination.id", "Termination.Date", "Termination.Created", "Termination.Status"];
        $TerminationFields = 0 < count(array_intersect($TerminationArray, $fields)) ? true : false;
        $search_at = [];
        $DebtorSearch = $PackageSearch = $ServerSearch = $SubscriptionSearch = false;
        if($searchat && (0 < strlen($searchfor) || $searchfor)) {
            $search_at = explode("|", $searchat);
            $DebtorSearch = 0 < count(array_intersect($DebtorArray, $search_at)) ? true : false;
            $PackageSearch = 0 < count(array_intersect($PackageArray, $search_at)) ? true : false;
            $ServerSearch = 0 < count(array_intersect($ServerArray, $search_at)) ? true : false;
            $SubscriptionSearch = 0 < count(array_intersect($SubscriptionArray, $search_at)) ? true : false;
        }
        $select = ["HostFact_Hosting.id"];
        foreach ($fields as $column) {
            if(in_array($column, $DebtorArray)) {
                $select[] = "HostFact_Debtors.`" . $column . "`";
            } elseif(in_array($column, $PackageArray)) {
                $select[] = "HostFact_Packages.`" . $column . "`";
            } elseif(in_array($column, $ServerArray)) {
                $select[] = "HostFact_Servers.`" . $column . "`";
            } elseif(in_array($column, $SubscriptionArray)) {
                if($column == "NextDate") {
                    $select[] = "(SELECT CASE HostFact_Debtors.`InvoiceCollect`\n\t\t\t\t\t\t\t\tWHEN '2' THEN CONCAT(DATE_FORMAT(HostFact_PeriodicElements.`NextDate`, '%Y-%m-'),IF(DAYOFMONTH(HostFact_PeriodicElements.`NextDate`) >= 15, '15', '01'))\n\t\t\t\t\t\t\t\tWHEN '1' THEN CONCAT(DATE_FORMAT(HostFact_PeriodicElements.`NextDate`, '%Y-%m-'),'01')\n\t\t\t\t\t\t\t\tWHEN '-1' THEN " . (INVOICE_COLLECT_ENABLED == "yes" ? "CONCAT(DATE_FORMAT(HostFact_PeriodicElements.`NextDate`, '%Y-%m-'),IF(DAYOFMONTH(HostFact_PeriodicElements.`NextDate`) >= 15, '" . (INVOICE_COLLECT_TPM == 1 ? "01" : "15") . "', '01'))" : "HostFact_PeriodicElements.`NextDate`") . "\n\t\t\t\t\t\t\t\tELSE HostFact_PeriodicElements.`NextDate`\n\t\t\t\t\t\t\t  END) as NextDate";
                } else {
                    $select[] = "HostFact_PeriodicElements.`" . $column . "`";
                }
            } elseif(in_array($column, $TerminationArray)) {
                if($column == "TerminationID") {
                    $select[] = "HostFact_Terminations.`id` as `TerminationID`";
                } elseif($column == "TerminatedDate") {
                    $select[] = "HostFact_Terminations.`Date` as `TerminatedDate`";
                } elseif(strpos($column, "Termination.") !== false) {
                    $select[] = "HostFact_Terminations.`" . str_replace("Termination.", "", $column) . "` as `" . $column . "`";
                }
            } else {
                $select[] = "HostFact_Hosting.`" . $column . "`";
            }
        }
        if(0 < count(array_intersect($SubscriptionArray, $fields))) {
            $fields[] = "PeriodicStatus";
            $select[] = "HostFact_PeriodicElements.`Status` AS `PeriodicStatus`";
        }
        if(in_array("PriceExcl", $fields) && !in_array("TaxPercentage", $fields)) {
            $fields[] = "TaxPercentage";
            $select[] = "HostFact_PeriodicElements.`TaxPercentage`";
        }
        if(in_array("PriceExcl", $fields) && !in_array("DiscountPercentage", $fields)) {
            $fields[] = "DiscountPercentage";
            $select[] = "HostFact_PeriodicElements.`DiscountPercentage`";
        }
        Database_Model::getInstance()->get("HostFact_Hosting", $select);
        if($DebtorFields || $DebtorSearch || in_array("NextDate", $fields)) {
            Database_Model::getInstance()->join("HostFact_Debtors", "HostFact_Debtors.`id` = HostFact_Hosting.`Debtor`");
        }
        if($PackageFields || $PackageSearch) {
            Database_Model::getInstance()->join("HostFact_Packages", "HostFact_Packages.`id` = HostFact_Hosting.`Package`");
        }
        if($ServerFields || $ServerSearch) {
            Database_Model::getInstance()->join("HostFact_Servers", "HostFact_Servers.`id` = HostFact_Hosting.`Server`");
        }
        if($SubscriptionFields || $SubscriptionSearch) {
            Database_Model::getInstance()->join("HostFact_PeriodicElements", "HostFact_PeriodicElements.`id` = HostFact_Hosting.`PeriodicID`");
        }
        if($TerminationFields) {
            Database_Model::getInstance()->join("HostFact_Terminations", "HostFact_Terminations.`ServiceType`='hosting' AND HostFact_Terminations.`ServiceID`=HostFact_Hosting.`id` AND HostFact_Terminations.`Status` IN ('pending', 'processed')");
        }
        if(!empty($search_at)) {
            $or_clausule = [];
            foreach ($search_at as $searchColumn) {
                if(in_array($searchColumn, ["Debtor", "Package", "Server"])) {
                    $or_clausule[] = ["HostFact_Hosting.`" . $searchColumn . "`", $searchfor];
                } elseif(in_array($searchColumn, $DebtorArray)) {
                    $or_clausule[] = ["HostFact_Debtors.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                } elseif(in_array($searchColumn, $PackageArray)) {
                    $or_clausule[] = ["HostFact_Packages.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                } elseif(in_array($searchColumn, $ServerArray)) {
                    $or_clausule[] = ["HostFact_Servers.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                } elseif(in_array($searchColumn, $SubscriptionArray)) {
                    $or_clausule[] = ["HostFact_PeriodicElements.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                } else {
                    $or_clausule[] = ["HostFact_Hosting.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                }
            }
            Database_Model::getInstance()->orWhere($or_clausule);
        }
        $order = $sort ? $order ? $order : "ASC" : "";
        if($sort == "Debtor") {
            Database_Model::getInstance()->orderBy("CONCAT(HostFact_Debtors.`CompanyName`, HostFact_Debtors.`SurName`)", $order);
        } elseif(in_array($sort, $DebtorArray)) {
            Database_Model::getInstance()->orderBy("HostFact_Debtors.`" . $sort . "`", $order);
        } elseif(in_array($sort, $PackageArray)) {
            Database_Model::getInstance()->orderBy("HostFact_Packages.`" . $sort . "`", $order);
        } elseif(in_array($sort, $ServerArray)) {
            Database_Model::getInstance()->orderBy("HostFact_Servers.`" . $sort . "`", $order);
        } elseif(in_array($sort, $SubscriptionArray)) {
            Database_Model::getInstance()->orderBy("HostFact_PeriodicElements.`" . $sort . "`", $order);
        } elseif($sort) {
            Database_Model::getInstance()->orderBy("HostFact_Hosting." . $sort, $order);
        }
        if(0 <= $limit) {
            Database_Model::getInstance()->limit((max(1, $limit) - 1) * $show_results, $show_results);
        }
        if(is_string($group) && 1 < count(explode("|", $group))) {
            $group = explode("|", $group);
            $or_clausule = [];
            foreach ($group as $status) {
                $or_clausule[] = ["HostFact_Hosting.`Status`", $status];
            }
            Database_Model::getInstance()->orWhere($or_clausule);
        } elseif(is_numeric($group)) {
            Database_Model::getInstance()->where("HostFact_Hosting.`Status`", $group);
        } else {
            Database_Model::getInstance()->where("HostFact_Hosting.`Status`", ["NOT IN" => [8, 9]]);
        }
        if(!empty($filters)) {
            foreach ($filters as $_db_field => $_db_value) {
                switch ($_db_field) {
                    case "debtor":
                        if(is_numeric($_db_value) && 0 < $_db_value) {
                            Database_Model::getInstance()->where("HostFact_Hosting.Debtor", $_db_value);
                        }
                        break;
                    case "created":
                        if(isset($_db_value["from"]) && $_db_value["from"]) {
                            Database_Model::getInstance()->where("HostFact_Hosting.Created", [">=" => $_db_value["from"]]);
                        }
                        if(isset($_db_value["to"]) && $_db_value["to"]) {
                            Database_Model::getInstance()->where("HostFact_Hosting.Created", ["<=" => $_db_value["to"]]);
                        }
                        break;
                    case "modified":
                        if(isset($_db_value["from"]) && $_db_value["from"]) {
                            Database_Model::getInstance()->where("HostFact_Hosting.Modified", [">=" => $_db_value["from"]]);
                        }
                        if(isset($_db_value["to"]) && $_db_value["to"]) {
                            Database_Model::getInstance()->where("HostFact_Hosting.Modified", ["<=" => $_db_value["to"]]);
                        }
                        break;
                }
            }
        }
        $list = [];
        $list["CountRows"] = 0;
        if($hosting_list = Database_Model::getInstance()->execute()) {
            $list["CountRows"] = Database_Model::getInstance()->rowCount("HostFact_Hosting", "HostFact_Hosting.id");
            foreach ($hosting_list as $result) {
                $list[$result->id] = ["id" => $result->id];
                foreach (["PriceExcl", "TaxPercentage"] as $_cast_column) {
                    if(isset($result->{$_cast_column})) {
                        $result->{$_cast_column} = (double) $result->{$_cast_column};
                    }
                }
                foreach ($fields as $column) {
                    $list[$result->id][$column] = is_string($result->{$column}) ? htmlspecialchars($result->{$column}) : $result->{$column};
                }
                if(isset($result->PriceExcl)) {
                    $list[$result->id]["PriceIncl"] = VAT_CALC_METHOD == "incl" ? round($result->PriceExcl * (1 + $result->TaxPercentage), 2) : round(round((double) $result->PriceExcl, 2) * (1 + $result->TaxPercentage), 2);
                }
            }
        }
        return $list;
    }
    public function createPDF($hosting_id, $template_id)
    {
        if(!$this->show($hosting_id, false)) {
            return false;
        }
        require_once "class/server.php";
        $server = new server();
        $server->show($this->Server);
        if(strpos($server->Panel, "plesk") !== false && $this->getPanel() && is_object($this->api)) {
            $result2 = $this->api->getAccountInfo();
            if(isset($result2["Username"])) {
                $this->PleskUsername = $result2["Username"];
                $this->PleskPassword = $result2["Password"];
            }
        }
        require_once "class/periodic.php";
        $periodic = new periodic();
        $periodic->show($this->PeriodicID);
        require_once "class/domain.php";
        $domain = new domain();
        $dom_array = explode(".", $this->Domain, 2);
        $result = Database_Model::getInstance()->getOne("HostFact_Domains", ["id"])->where("Domain", $dom_array[0])->where("Tld", isset($dom_array[1]) ? $dom_array[1] : "")->where("HostingID", $hosting_id)->execute();
        if($result && 0 < $result->id) {
            $domain->show($result->id);
        }
        $debtor_id = $this->Debtor;
        require_once "class/debtor.php";
        $debtor = new debtor();
        $debtor->show($debtor_id);
        $this->BandWidth = formatMB($this->BandWidth);
        $this->DiscSpace = formatMB($this->DiscSpace);
        $OutputType = "D";
        require_once "class/pdf.php";
        $template = $template_id;
        $pdf = new pdfCreator($template, ["hosting" => $this, "server" => $server, "periodic" => $periodic, "debtor" => $debtor, "domain" => $domain], "other", $OutputType, true);
        if(!$pdf->generatePDF("F")) {
            $this->Error = array_merge($this->Error, $pdf->Error);
            return false;
        }
        $_SESSION["force_download"] = $pdf->Name;
        return true;
    }
    public function emailPDF($hosting_id, $template_id, $emailaddresses = false)
    {
        if(!$this->show($hosting_id, false)) {
            return false;
        }
        require_once "class/server.php";
        $server = new server();
        $server->show($this->Server);
        if(strpos($server->Panel, "plesk") !== false && $this->getPanel() && is_object($this->api)) {
            $result2 = $this->api->getAccountInfo();
            if(isset($result2["Username"])) {
                $this->PleskUsername = $result2["Username"];
                $this->PleskPassword = $result2["Password"];
            }
        }
        require_once "class/periodic.php";
        $periodic = new periodic();
        $periodic->show($this->PeriodicID);
        require_once "class/domain.php";
        $domain = new domain();
        $dom_array = explode(".", $this->Domain, 2);
        $result = Database_Model::getInstance()->getOne("HostFact_Domains", ["id"])->where("Domain", $dom_array[0])->where("Tld", isset($dom_array[1]) ? $dom_array[1] : "")->where("HostingID", $hosting_id)->execute();
        if($result && 0 < $result->id) {
            $domain->show($result->id);
        }
        $debtor_id = $this->Debtor;
        require_once "class/debtor.php";
        $debtor = new debtor();
        $debtor->show($debtor_id);
        require_once "class/template.php";
        $emailtemplate = new emailtemplate();
        $emailtemplate->Identifier = $template_id;
        $emailtemplate->show();
        require_once "class/email.php";
        $email = new email();
        foreach ($emailtemplate->Variables as $v) {
            if(is_string($emailtemplate->{$v})) {
                $email->{$v} = htmlspecialchars_decode($emailtemplate->{$v});
            } else {
                $email->{$v} = $emailtemplate->{$v};
            }
        }
        $this->BandWidth = formatMB($this->BandWidth);
        $this->DiscSpace = formatMB($this->DiscSpace);
        $objects = ["hosting" => $this, "server" => $server, "periodic" => $periodic, "debtor" => $debtor, "domain" => $domain];
        $email->Recipient = $emailaddresses ? $emailaddresses : $debtor->EmailAddress;
        $email->Debtor = $debtor_id;
        $email->add($objects);
        if($email->sent(false, false, false, $objects)) {
            $logEmailAddress = check_email_address($email->Recipient, "convert", ", ");
            createLog("hosting", $this->Identifier, "pdf account data sent", $logEmailAddress);
            $this->Success[] = sprintf(__("mail with account data sent"), $this->Username, $logEmailAddress);
            createMessageLog("success", "mail with account data sent", [$this->Username, $logEmailAddress], "hosting", $this->Identifier);
            return true;
        }
        $this->Error[] = sprintf(__("error while sending mail with account data"), isset($email->MailerError) ? $email->MailerError : "");
        createMessageLog("error", "error while sending mail with account data", isset($email->MailerError) ? $email->MailerError : "", "hosting", $this->Identifier);
        return false;
    }
    public function create($supressMessage = false)
    {
        if($this->Status == 4) {
            $this->Error[] = sprintf(__("hosting is already active"), $this->Username);
            return false;
        }
        $this->connectDomainToHosting($this->Identifier, $this->Debtor, $this->Domain);
        if(!$this->getPanel()) {
            $this->Status = 4;
            $result = Database_Model::getInstance()->update("HostFact_Hosting", ["Status" => $this->Status])->where("id", $this->Identifier)->execute();
            if(!$result) {
                $this->Error[] = sprintf(__("cannot update hosting status to active"), $this->Username);
                return false;
            }
            $this->Warning[] = sprintf(__("account creation must be done manually"), $this->Username);
            createMessageLog("warning", "account creation must be done manually", $this->Username, "hosting", $this->Identifier);
            $account_info = ["id" => $this->Identifier, "Debtor" => $this->Debtor, "Username" => $this->Username, "Domain" => $this->Domain];
            do_action("hosting_account_is_created", $account_info);
            if(function_exists("account_is_created")) {
                $account_info = ["id" => $this->Identifier, "debtor_id" => $this->Debtor, "username" => $this->Username, "domain" => $this->Domain];
                @account_is_created($account_info);
            }
            delete_stats_summary();
            createLog("hosting", $this->Identifier, "hosting created");
            return true;
        }
        if($this->Package <= 0) {
            $this->Error[] = sprintf(__("hosting doesnt have a package"), $this->Username);
            $update_status_to_error = true;
        } else {
            $this->api->Username = strtolower($this->Username);
            $this->api->Password = $this->Password;
            $this->api->Domain = strtolower($this->Domain);
            $this->api->Package = $this->Package;
            require_once "class/debtor.php";
            $debtor = new debtor();
            $debtor->Identifier = $this->Debtor;
            $debtor->show();
            $this->api->EmailAddress = getFirstMailAddress($debtor->EmailAddress);
            require_once "class/package.php";
            $package = new package();
            $package->Identifier = $this->Package;
            $package->show();
            foreach ($package->Variables as $v) {
                $this->api->{$v} = $package->{$v};
            }
            if($package->PackageType == "reseller") {
                $result = $this->api->createResellerAccount();
            } else {
                $result = $this->api->createAccount();
            }
            if($result === true) {
                $this->Status = 4;
                $result = Database_Model::getInstance()->update("HostFact_Hosting", ["Status" => $this->Status])->where("id", $this->Identifier)->execute();
                if(!$result) {
                    $this->Error[] = sprintf(__("cannot update hosting status to active"), $this->Username);
                    return false;
                }
                $this->Success[] = sprintf(__("account creation completed"), $this->Username);
                createLog("hosting", $this->Identifier, "hosting created");
                createMessageLog("success", "account creation completed", $this->Username, "hosting", $this->Identifier);
                if(0 < $this->EmailTemplate && $this->EmailAuto == "yes") {
                    $this->emailPDF($this->Identifier, $this->EmailTemplate);
                }
                $result = Database_Model::getInstance()->get(["HostFact_Domains", "HostFact_Hosting"], ["HostFact_Domains.*"])->where("HostFact_Hosting.id", $this->Identifier)->where("HostFact_Domains.HostingID = HostFact_Hosting.id")->where("HostFact_Domains.Status", [">=" => "0"])->where("HostFact_Domains.Status", ["<" => "8"])->execute();
                if($result && is_array($result)) {
                    foreach ($result as $_domain) {
                        $dom = $_domain->Domain . "." . $_domain->Tld;
                        if($dom != $this->Domain) {
                            $this->CurrentDomain = $this->Domain;
                            $this->api->CurrentDomain = strtolower($this->Domain);
                            $this->api->Domain = strtolower($dom);
                            $this->api->addDomain();
                            $this->Domain = $this->CurrentDomain;
                        }
                    }
                }
                $this->Error = array_merge($this->Error, $this->api->Error);
                $account_info = ["id" => $this->Identifier, "Debtor" => $this->Debtor, "Username" => $this->Username, "Domain" => $this->Domain];
                do_action("hosting_account_is_created", $account_info);
                if(function_exists("account_is_created")) {
                    $account_info = ["id" => $this->Identifier, "debtor_id" => $this->Debtor, "username" => $this->Username, "domain" => $this->Domain];
                    @account_is_created($account_info);
                }
                delete_stats_summary();
                return true;
            } else {
                $this->Error[] = sprintf(__("account creation failed"), $this->Username);
                $this->Error = array_merge($this->Error, $this->api->Error);
                foreach ($this->api->Error as $e) {
                    createLog("hosting", $this->Identifier, $e, [], false);
                }
                $update_status_to_error = true;
            }
        }
        if(isset($update_status_to_error) && $update_status_to_error) {
            createMessageLog("error", "account creation failed", $this->Username, "hosting", $this->Identifier);
            Database_Model::getInstance()->update("HostFact_Hosting", ["Status" => "7"])->where("id", $this->Identifier)->execute();
            global $account;
            global $company;
            if(CRONJOB_NOTIFY_HOSTING == "yes" && CRONJOB_NOTIFY_MAILADDRESS && (!isset($account->Identifier) || empty($account->Identifier))) {
                require_once "class/email.php";
                $email = new email();
                $email->Recipient = CRONJOB_NOTIFY_MAILADDRESS;
                $email->Sender = $company->CompanyName . " <" . $company->EmailAddress . ">";
                $email->Subject = sprintf(__("email subject hosting creation error"), $this->Username);
                $email->Message = file_get_contents("includes/language/" . LANGUAGE_CODE . "/mail.hosting.creation.error.html");
                $email->Message = str_replace("[username]", $this->Username, $email->Message);
                $email->Message = str_replace("[domain]", $this->Domain, $email->Message);
                $email->Message = str_replace("[server]", $this->ServerName, $email->Message);
                $email->Message = str_replace("[error]", "&bull; " . implode("<br />&bull; ", isset($this->api->Error) && is_array($this->api->Error) && 0 < count($this->api->Error) ? $this->api->Error : $this->Error), $email->Message);
                $email->Message = str_replace("[hostingurl]", BACKOFFICE_URL . "hosting.php?page=show&id=" . $this->Identifier, $email->Message);
                $email->AutoSubmitted = true;
                $email_sent = $email->sent();
            }
        }
        return false;
    }
    public function suspend($id, $current_status = NULL)
    {
        if(!is_numeric($id)) {
            $this->Error[] = __("invalid identifier for hosting");
            return false;
        }
        if(!$this->show($id, false)) {
            return false;
        }
        if(is_null($current_status) || $current_status <= 0) {
            $current_status = $this->Status;
        }
        if($current_status == 5) {
            $result = Database_Model::getInstance()->update("HostFact_Hosting", ["Status" => "4"])->where("id", $id)->execute();
            if(!$result) {
                $this->Error[] = __("cannot update status hosting account");
                return false;
            }
            if($this->getPanel()) {
                $this->api->Username = $this->Username;
                $this->api->Domain = strtolower($this->Domain);
                $this->api->TemplateName = $this->TemplateName;
                $this->api->Suspend = false;
                $result = $this->api->suspendAccount();
                if($result) {
                    $this->Success[] = sprintf(__("account unsuspended"), $this->Username);
                    createLog("hosting", $id, "account unsuspended, account is active again");
                    return true;
                }
                $this->Error[] = sprintf(__("account unsuspending failed"), $this->Username);
                $this->Error = array_merge($this->Error, $this->api->Error);
                foreach ($this->api->Error as $e) {
                    createLog("hosting", $id, $e, [], false);
                }
                Database_Model::getInstance()->update("HostFact_Hosting", ["Status" => "5"])->where("id", $id)->execute();
                return false;
            }
        } else {
            $result = Database_Model::getInstance()->update("HostFact_Hosting", ["Status" => "5"])->where("id", $id)->execute();
            if(!$result) {
                $this->Error[] = __("cannot update status hosting account");
                return false;
            }
            if($this->getPanel()) {
                $this->api->Username = $this->Username;
                $this->api->Domain = strtolower($this->Domain);
                $this->api->TemplateName = $this->TemplateName;
                $this->api->Suspend = true;
                $result = $this->api->suspendAccount();
                if($result) {
                    $this->Success[] = sprintf(__("account suspended"), $this->Username);
                    createLog("hosting", $id, "account suspended");
                    return true;
                }
                $this->Error[] = sprintf(__("account suspending failed"), $this->Username);
                $this->Error = array_merge($this->Error, $this->api->Error);
                foreach ($this->api->Error as $e) {
                    createLog("hosting", $id, $e, [], false);
                }
                Database_Model::getInstance()->update("HostFact_Hosting", ["Status" => $this->Status])->where("id", $id)->execute();
                return false;
            }
        }
    }
    public function remove($id)
    {
        if(!is_numeric($id)) {
            $this->Error[] = __("invalid identifier for hosting");
            return false;
        }
        if(!$this->show($id, false)) {
            return false;
        }
        if($this->getPanel()) {
            $this->api->Username = $this->Username;
            $this->api->Domain = strtolower($this->Domain);
            $this->api->Reseller = $this->PackageType == "reseller" ? true : false;
            $result = $this->api->deleteAccount();
            if($result) {
                $this->Success[] = sprintf(__("account deleted"), $this->Username);
                createLog("hosting", $id, "account removed on server");
                $account_info = ["id" => $this->Identifier, "Debtor" => $this->Debtor, "Username" => $this->Username, "Domain" => $this->Domain, "Server" => $this->Server];
                do_action("hosting_account_is_removed_from_server", $account_info);
                return true;
            }
            $this->Error[] = sprintf(__("account removing failed"), $this->Username);
            $this->Error = array_merge($this->Error, $this->api->Error);
            foreach ($this->api->Error as $e) {
                createLog("hosting", $id, $e, [], false);
            }
            return false;
        } else {
            $account_info = ["id" => $this->Identifier, "Debtor" => $this->Debtor, "Username" => $this->Username, "Domain" => $this->Domain, "Server" => $this->Server];
            do_action("hosting_account_is_removed_from_server", $account_info);
            return false;
        }
    }
    public function checkIfAccountExistsOnServer()
    {
        if($this->Status != 4 && $this->Status != 5) {
            return true;
        }
        require_once "class/server.php";
        $server = new server();
        $server->show($this->Server);
        if(!$server->Panel) {
            return true;
        }
        $api = $server->connect();
        if(is_object($api)) {
            $api->Username = $this->Username;
            $api->Password = $this->Password;
            $api->Domain = $this->Domain;
            if($this->PackageType == "reseller") {
                $api->Reseller = true;
            }
            if(strpos($server->Panel, "plesk") !== false) {
                $result = Database_Model::getInstance()->getOne("HostFact_ServersPleskClients", ["ClientID"])->where("ServerID", $server->Identifier)->where("DebtorID", $this->Debtor)->execute();
                if($result && 0 < $result->ClientID) {
                    $api->PleskClientID = $result->ClientID;
                }
                $api->ServerID = $server->Identifier;
            }
            if(!method_exists($api, "checkIfAccountExists")) {
                return true;
            }
            $result = $api->checkIfAccountExists();
            if($result === true) {
                return true;
            }
            $this->Error = array_merge($this->Error, $api->Error);
            return false;
        }
        $this->Error = array_merge($this->Error, $server->Error);
        return false;
    }
    public function changeAccountPassword()
    {
        require_once "class/server.php";
        $server = new server();
        $server->show($this->Server);
        $api = $server->connect();
        if(is_object($api)) {
            $api->Username = $this->Username;
            $api->Password = $this->Password;
            $api->Domain = $this->Domain;
            $api->ServerID = $this->Server;
            $api->DebtorID = $this->Debtor;
            $result = $api->changeAccountPassword();
            if($result === true) {
                $result = Database_Model::getInstance()->update("HostFact_Hosting", ["Password" => passcrypt($this->Password)])->where("id", $this->Identifier)->execute();
                if($result) {
                    $this->Success[] = __("account password adjusted in software");
                    $this->Success[] = __("account password adjusted on server");
                    createLog("hosting", $this->Identifier, "account password adjusted");
                    return true;
                }
                return false;
            }
            $this->Error[] = __("password edit on server failed");
            $this->Error = array_merge($this->Error, $api->Error);
            foreach ($api->Error as $e) {
                createLog("hosting", $this->Identifier, $e, [], false);
            }
            return false;
        } else {
            $this->Error = array_merge($this->Error, $server->Error);
            return false;
        }
    }
    public function setActive($list)
    {
        if(!is_array($list)) {
            $this->Error[] = __("invalid list of identifiers for hosting accounts");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Hosting", ["Status" => "4"])->where("id", ["IN" => $list])->where("Status", ["!=" => "4"])->execute();
        if($result) {
            foreach ($list as $hosting_id) {
                createLog("hosting", $hosting_id, "hosting set active");
            }
            $this->Success[] = __("accounts set to active");
            return true;
        } else {
            return false;
        }
    }
    public function getStats()
    {
        if(!$this->getPanel()) {
            return false;
        }
        $this->api->Domain = strtolower($this->Domain);
        $stats = $this->api->getAccountUsage($this->Username);
        if(!empty($stats)) {
            foreach ($stats as $key => $value) {
                $this->{$key} = $value;
            }
            return true;
        } else {
            $this->Error = array_merge($this->Error, $this->api->Error);
            return false;
        }
    }
    public function getPanel()
    {
        if(!$this->Server) {
            return false;
        }
        require_once "class/server.php";
        $server = new server();
        $server->Identifier = $this->Server;
        $server->show();
        $this->PanelName = $server->Panel;
        $this->DomainType = $server->DomainType;
        $this->api = $server->connect();
        if(!is_object($this->api)) {
            return false;
        }
        $this->ServerName = $server->Name;
        $this->api->Debtor = $this->Debtor;
        require_once "class/debtor.php";
        $debtor = new debtor();
        $debtor->Identifier = $this->Debtor;
        $debtor->show();
        foreach ($debtor->Variables as $v) {
            $this->api->{$v} = $debtor->{$v};
        }
        $this->api->StateName = $debtor->StateName;
        if(strpos($server->Panel, "plesk") !== false) {
            $result = Database_Model::getInstance()->getOne("HostFact_ServersPleskClients", ["ClientID"])->where("ServerID", $server->Identifier)->where("DebtorID", $debtor->Identifier)->execute();
            if($result && 0 < $result->ClientID) {
                $this->api->PleskClientID = $result->ClientID;
            }
            $this->api->ServerID = $server->Identifier;
        }
        if($this->PackageType == "reseller") {
            $this->api->Reseller = true;
        }
        $this->api->DomainType = $server->DomainType;
        $debtor->__destruct();
        unset($debtor);
        return true;
    }
    public function addDomainToServer($domaintld, $hostingID)
    {
        global $array_domaintype;
        $domain = explode(".", str_replace(" ", "", str_replace("www.", "", $domaintld)), 2);
        if(!isset($domain[0]) || !isset($domain[1])) {
            $this->Error[] = sprintf(__("invalid or no domain to add"), $domaintld);
            return false;
        }
        $this->show($hostingID, false);
        require_once "class/server.php";
        $server = new server();
        $server->show($this->Server);
        $api = $server->connect();
        if(is_object($api)) {
            $api->Domain = strtolower(implode(".", $domain));
            $api->CurrentDomain = strtolower($this->Domain);
            $api->Username = $this->Username;
            $api->Password = $this->Password;
            $api->DomainType = $server->DomainType;
            if($api->Domain == $api->CurrentDomain) {
                $this->Error[] = sprintf(__("domain cannot be add domain exists as account domain"), $api->CurrentDomain, $api->DomainType);
                $this->Error = array_merge($this->Error, $server->Error);
                return false;
            }
            $result = $api->addDomain();
            if($result === true) {
                $this->Success[] = sprintf(__("domain is added to account"), $domaintld, $this->Username);
                createLog("hosting", $this->Identifier, "domain added", $domaintld);
                return true;
            }
            $this->Error[] = sprintf(__("domain cannot be added to account"), $domaintld, $this->Username);
            $this->Error = array_merge($this->Error, $api->Error);
            foreach ($api->Error as $e) {
                createLog("hosting", $this->Identifier, $e, [], false);
            }
            return false;
        } else {
            $this->Error = array_merge($this->Error, $server->Error);
            return false;
        }
    }
    public function connectDomainToHosting($hosting_id, $debtor_id, $domain)
    {
        $result = Database_Model::getInstance()->getOne("HostFact_Domains", ["id"])->where("Debtor", $debtor_id)->where("CONCAT(`Domain`,'.',`Tld`)", $domain)->where("Status", ["<" => 8])->where("HostingID", "0")->execute();
        if($result && 0 < $result->id) {
            Database_Model::getInstance()->update("HostFact_Domains", ["HostingID" => $hosting_id])->where("id", $result->id)->execute();
            return $result->id;
        }
        return false;
    }
    public function serviceExecuteAction($service_id, $action)
    {
        if(!$this->show($service_id, false)) {
            return false;
        }
        switch ($action) {
            case "suspendhosting":
                if($this->getPanel() === false) {
                    return "manual";
                }
                return $this->suspend($service_id, "4");
                break;
            case "deleteHosting":
                if(defined("U_HOSTING_DELETE") && !U_HOSTING_DELETE) {
                    $this->Error[] = __("invalid user rights to perform action");
                    return false;
                }
                if($this->getPanel() === false) {
                    return "manual";
                }
                return $this->delete($service_id, "none", "remove", "none");
                break;
            default:
                $this->Error[] = __("invalid action to perform");
                return false;
        }
    }
    public function upDowngrade($hosting_id, $new_product_id, $periodic_details = [])
    {
        $this->show($hosting_id, false);
        if($this->Status != 4) {
            $this->Error[] = __("hosting updowngrade only active hosting");
            return false;
        }
        if(empty($this->PeriodicID)) {
            $this->Error[] = __("hosting updowngrade only with periodic");
            return false;
        }
        if(!isset($periodic_details["Periods"]) || $periodic_details["Periods"] == "") {
            $periodic_details["Periods"] = $this->Periodic->Periods;
        }
        if(!isset($periodic_details["Periodic"]) || $periodic_details["Periodic"] == "") {
            $periodic_details["Periodic"] = $this->Periodic->Periodic;
        }
        if(!isset($periodic_details["invoice_cycle"]) || $periodic_details["invoice_cycle"] != "existing_period" && $periodic_details["invoice_cycle"] != "new_period") {
            $periodic_details["invoice_cycle"] = HOSTING_UPGRADE_FINANCIAL_PROCESSING;
        }
        if(!isset($periodic_details["create_invoice"]) || $periodic_details["create_invoice"] != "yes" && $periodic_details["create_invoice"] != "no") {
            switch (HOSTING_UPGRADE_CREATE_INVOICE) {
                case "always":
                    $periodic_details["create_invoice"] = "yes";
                    break;
                case "never":
                    $periodic_details["create_invoice"] = "no";
                    break;
                case "only_positive":
            }
        }
        require_once "class/product.php";
        $product = new product();
        if(!$product->show($new_product_id)) {
            $this->Error[] = __("hosting updowngrade no valid product given");
            return false;
        }
        if(!isset($product->PackageID) || empty($product->PackageID)) {
            $this->Error[] = __("hosting updowngrade given product contains no package");
            return false;
        }
        $new_package_id = $product->PackageID;
        require_once "class/package.php";
        $package = new package();
        $package->show($new_package_id);
        $keep_current_server = false;
        $this->__isServerWithoutPanel = false;
        if($this->Server == $package->Server) {
            if(!$this->changePackageOnServer($this->Username, $package->TemplateName)) {
                $this->Error[] = sprintf(__("package change on server failed"), $this->Username);
                return false;
            }
        } else {
            $packages = new package();
            $product_packages = $packages->all(["TemplateName", "Server", "ProductCode"], false, false, "-1", "ProductCode", $product->ProductCode);
            foreach ($product_packages as $product_package) {
                if($product_package["Server"] == $this->Server && $product_package["ProductCode"] == $product->ProductCode) {
                    $product_on_server = $product_package;
                    if(isset($product_on_server) && $product_on_server["TemplateName"]) {
                        if(!$this->changePackageOnServer($this->Username, $product_on_server["TemplateName"])) {
                            $this->Error[] = sprintf(__("package change on server failed"), $this->Username);
                            return false;
                        }
                        $keep_current_server = true;
                    } else {
                        $this->Warning[] = sprintf(__("no package change, different server"), $this->Username);
                        $_SESSION["hosting"]["updowngrade_suppress_error"] = true;
                    }
                }
            }
        }
        $result = Database_Model::getInstance()->update("HostFact_Hosting", ["Product" => $product->Identifier, "Package" => $product->PackageID, "Server" => $keep_current_server === true ? $this->Server : $package->Server])->where("id", $hosting_id)->execute();
        if(!$result) {
            return false;
        }
        require_once "class/periodic.php";
        $current_periodic = new periodic();
        $current_periodic->show($this->PeriodicID);
        require_once "class/package.php";
        $current_package = new package();
        $current_package->show($this->Package);
        $periodic = new periodic();
        $periodic->show($this->PeriodicID);
        foreach ($periodic as $key => $value) {
            if(is_string($value) && in_array($key, $periodic->Variables)) {
                $periodic->{$key} = htmlspecialchars_decode($periodic->{$key});
            }
        }
        $periodic->Debtor = $this->Debtor;
        $periodic->ProductCode = htmlspecialchars_decode($product->ProductCode);
        $periodic->Description = htmlspecialchars_decode($product->ProductKeyPhrase);
        $periodic->PriceExcl = $product->PriceExcl;
        $periodic->TaxPercentage = $product->TaxPercentage;
        $periodic->DiscountPercentage = 0;
        $today = date("Y-m-d");
        $end_period = calculate_date($today, $periodic_details["Periods"], $periodic_details["Periodic"]);
        $periodic_details["Periods"] = is_numeric($periodic_details["Periods"]) && 0 < $periodic_details["Periods"] ? $periodic_details["Periods"] : 1;
        $periodic->Periods = $periodic_details["Periods"];
        $periodic->Periodic = $periodic_details["Periodic"];
        if($periodic_details["invoice_cycle"] == "existing_period") {
            $periodic->StartPeriod = $current_periodic->StartPeriod;
            $periodic->EndPeriod = calculate_date($current_periodic->StartPeriod, $periodic_details["Periods"], $periodic_details["Periodic"]);
        } elseif($periodic_details["invoice_cycle"] == "new_period") {
            $periodic->StartContract = rewrite_date_db2site($today);
            $periodic->EndContract = rewrite_date_db2site($end_period);
            $periodic->ContractPeriods = $periodic_details["Periods"];
            $periodic->ContractPeriodic = $periodic_details["Periodic"];
            $periodic->StartPeriod = rewrite_date_db2site($end_period);
            $periodic->EndPeriod = calculate_date($end_period, $periodic_details["Periods"], $periodic_details["Periodic"]);
            $periodic->NextDate = "";
        }
        $periodic->checkPricePeriod(true);
        if(!$periodic->edit()) {
            $this->Error = array_merge($this->Error, $periodic->Error);
            return false;
        }
        if($periodic_details["create_invoice"] == "yes" || !isset($periodic_details["create_invoice"]) && HOSTING_UPGRADE_CREATE_INVOICE == "only_positive") {
            $today = rewrite_date_db2site(date("Y-m-d"));
            $start_period = $this->Periodic->StartPeriod;
            $end_period = $this->Periodic->EndPeriod;
            $product_price = deformat_money($this->Periodic->PriceExcl);
            switch ($this->Periodic->Periodic) {
                case "t":
                    $current_product_ppd = $product_price / 730;
                    $number_of_days = 730;
                    break;
                case "j":
                    $current_product_ppd = $product_price / 365;
                    $number_of_days = 365;
                    break;
                case "h":
                    $current_product_ppd = $product_price * 2 / 365;
                    $number_of_days = 0;
                    break;
                case "k":
                    $current_product_ppd = $product_price * 4 / 365;
                    $number_of_days = 0;
                    break;
                case "m":
                    $current_product_ppd = $product_price * 12 / 365;
                    $number_of_days = 0;
                    break;
                case "w":
                    $current_product_ppd = $product_price / 7;
                    $number_of_days = 7;
                    break;
                case "d":
                    $current_product_ppd = $product_price;
                    $number_of_days = 1;
                    break;
                default:
                    $current_product_ppd = 0;
                    $number_of_days = 0;
                    $price_array = $product->listCustomProductPrices();
                    if(isset($price_array["period"][$periodic_details["Periods"] . "-" . $periodic_details["Periodic"]]["PriceExcl"])) {
                        $product->PriceExcl = $price_array["period"][$periodic_details["Periods"] . "-" . $periodic_details["Periodic"]]["PriceExcl"];
                        $product->PricePeriod = $periodic_details["Periodic"];
                    }
                    $product_price = deformat_money($product->PriceExcl);
                    switch ($product->PricePeriod) {
                        case "t":
                            $new_product_ppd = $product_price / 730;
                            break;
                        case "j":
                            $new_product_ppd = $product_price / 365;
                            break;
                        case "h":
                            $new_product_ppd = $product_price * 2 / 365;
                            break;
                        case "k":
                            $new_product_ppd = $product_price * 4 / 365;
                            break;
                        case "m":
                            $new_product_ppd = $product_price * 12 / 365;
                            break;
                        case "w":
                            $new_product_ppd = $product_price / 7;
                            break;
                        case "d":
                            $new_product_ppd = $product_price;
                            break;
                        default:
                            $new_product_ppd = 0;
                            if(date("Y-m-d") == calculate_date(date("Y-m-d", strtotime(rewrite_date_site2db($start_period))), -1 * $this->Periodic->Periods, $this->Periodic->Periodic)) {
                                $days_remaining_period = $number_of_days * $this->Periodic->Periods;
                            } else {
                                $days_remaining_period = max(0, getDaysFromPeriod(rewrite_date_site2db($today), rewrite_date_site2db($start_period)) - 1);
                            }
                            $invoice_lines = [];
                            if($periodic_details["invoice_cycle"] == "existing_period") {
                                $upgrade_diff_price = $new_product_ppd * $days_remaining_period - $current_product_ppd * $days_remaining_period;
                                $positive_invoice = 0 < $upgrade_diff_price ? true : false;
                                $upgradePrefix = str_ireplace(["[hosting-&gt;NewPackageName]", "[hosting-&gt;OldPackageName]", "[hosting-&gt;Domain]", "[hosting-&gt;Username]", "[periodic-&gt;Description]"], [$package->PackageName, $current_package->PackageName, $this->Domain, $this->Username, $this->Periodic->Description], HOSTING_UPGRADE_PREFIX_UPGRADE);
                                $invoice_line_desc = trim(htmlspecialchars_decode($upgradePrefix)) . " (" . $today . " " . __("till") . " " . $start_period . ")";
                                $invoice_lines[0] = ["ProductCode" => htmlspecialchars_decode($product->ProductCode), "Description" => $invoice_line_desc, "PriceExcl" => $upgrade_diff_price, "DiscountPercentage" => 0, "TaxPercentage" => $product->TaxPercentage, "Periodic" => ""];
                            } elseif($periodic_details["invoice_cycle"] == "new_period") {
                                $current_product_refund = -1 * abs($current_product_ppd * $days_remaining_period);
                                $new_period_end_date = calculate_date(rewrite_date_site2db($today), $periodic_details["Periods"], $periodic_details["Periodic"]);
                                switch ($periodic_details["Periodic"]) {
                                    case "t":
                                        $number_of_days = 730;
                                        break;
                                    case "j":
                                        $number_of_days = 365;
                                        break;
                                    case "h":
                                        $number_of_days = 0;
                                        break;
                                    case "k":
                                        $number_of_days = 0;
                                        break;
                                    case "m":
                                        $number_of_days = 0;
                                        break;
                                    case "w":
                                        $number_of_days = 7;
                                        break;
                                    case "d":
                                        $number_of_days = 1;
                                        break;
                                    default:
                                        $number_of_days = 0;
                                        $new_period_price = $new_product_ppd * $number_of_days;
                                        $positive_invoice = 0 < $new_period_price + $current_product_refund ? true : false;
                                        $invoice_line_desc = htmlspecialchars_decode($product->ProductKeyPhrase);
                                        $invoice_lines[0] = ["ProductCode" => htmlspecialchars_decode($product->ProductCode), "Description" => $invoice_line_desc, "PriceExcl" => $new_period_price, "DiscountPercentage" => 0, "TaxPercentage" => $product->TaxPercentage, "PeriodicID" => $this->Periodic->Identifier, "Periods" => $periodic_details["Periods"], "Periodic" => $periodic_details["Periodic"], "StartPeriod" => $today, "EndPeriod" => $new_period_end_date, "ProductType" => "hosting", "Reference" => $hosting_id];
                                        $refundPrefix = str_ireplace(["[hosting-&gt;NewPackageName]", "[hosting-&gt;OldPackageName]", "[hosting-&gt;Domain]", "[hosting-&gt;Username]", "[periodic-&gt;Description]"], [$package->PackageName, $current_package->PackageName, $this->Domain, $this->Username, $this->Periodic->Description], HOSTING_UPGRADE_PREFIX_REFUND);
                                        $invoice_line_desc = trim(htmlspecialchars_decode($refundPrefix)) . " (" . $today . " " . __("till") . " " . $start_period . ")";
                                        $invoice_lines[1] = ["ProductCode" => htmlspecialchars_decode($this->Periodic->ProductCode), "Description" => $invoice_line_desc, "PriceExcl" => $current_product_refund, "DiscountPercentage" => 0, "TaxPercentage" => $this->Periodic->TaxPercentage, "Periodic" => ""];
                                }
                            }
                            if($periodic_details["create_invoice"] == "yes" || !isset($periodic_details["create_invoice"]) && HOSTING_UPGRADE_CREATE_INVOICE == "only_positive" && $positive_invoice === true) {
                                require_once "class/invoice.php";
                                $invoice = new invoice();
                                $invoice->quickAdd($this->Debtor, $invoice_lines);
                                $this->Error = array_merge($this->Error, $invoice->Error);
                                $this->Warning = array_merge($this->Warning, $invoice->Warning);
                                $this->Success = array_merge($this->Success, $invoice->Success);
                            }
                    }
            }
        }
        $service_info = ["Type" => "hosting", "id" => $hosting_id, "Debtor" => $this->Debtor];
        do_action("service_is_edited", $service_info);
        if($this->__isServerWithoutPanel === true) {
            $this->Success[] = sprintf(__("hostingaccount upgraded success, but not on server"), $this->Username);
        } else {
            $this->Success[] = sprintf(__("hostingaccount upgraded success"), $this->Username);
        }
        return true;
    }
    public function changePackageOnServer($accountname, $packagename)
    {
        if(!$this->Server) {
            return false;
        }
        require_once "class/server.php";
        $server = new server();
        $server->show($this->Server);
        $api = $server->connect();
        if(is_object($api) && method_exists($api, "changePackage")) {
            $api->Domain = $this->Domain;
            $api->Reseller = $this->PackageType == "reseller" ? true : false;
            if(strpos($server->Panel, "plesk") !== false) {
                $result = Database_Model::getInstance()->getOne("HostFact_ServersPleskClients", ["ClientID"])->where("ServerID", $server->Identifier)->where("DebtorID", $this->Debtor)->execute();
                if($result && 0 < $result->ClientID) {
                    $api->PleskClientID = $result->ClientID;
                }
                $api->ServerID = $server->Identifier;
            }
            $result = $api->changePackage($accountname, $packagename);
            if(!$result) {
                $this->Error = array_merge($this->Error, $api->Error);
                $this->Warning = array_merge($this->Warning, $api->Warning);
                $this->Success = array_merge($this->Success, $api->Success);
                return false;
            }
            createLog("hosting", $this->Identifier, "hosting upgraded from to", [$this->TemplateName, $packagename]);
            return true;
        }
        $this->__isServerWithoutPanel = true;
        $this->Warning[] = sprintf(__("no package change, server has no integration"), $this->Username);
        return true;
    }
    public function singleSignOn($ip_addresses = [])
    {
        if(!$this->getPanel()) {
            return false;
        }
        if(is_object($this->api) && method_exists($this->api, "singleSignOn")) {
            $this->api->Username = $this->Username;
            if(!empty($ip_addresses)) {
                $this->api->IPAddresses = $ip_addresses;
            }
            $this->api->Reseller = $this->PackageType == "reseller" ? true : false;
            $result = $this->api->singleSignOn();
            if(!$result) {
                $this->Error = array_merge($this->Error, $this->api->Error);
                $this->Warning = array_merge($this->Warning, $this->api->Warning);
                $this->Success = array_merge($this->Success, $this->api->Success);
                return false;
            }
            return $result;
        }
        return false;
    }
}

?>