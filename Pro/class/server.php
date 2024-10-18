<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class server
{
    public $Identifier;
    public $Name;
    public $Panel;
    public $Location;
    public $Port;
    public $Username;
    public $Password;
    public $IP;
    public $AdditionalIP;
    public $DomainType;
    public $DNS1;
    public $DNS2;
    public $DNS3;
    public $DefaultDNSTemplate;
    public $Settings;
    public $cPanel_Password;
    public $Status;
    public $Error = [];
    public $Warning = [];
    public $Success = [];
    public $Variables;
    public function __construct()
    {
        $this->Status = 1;
        $this->Variables = ["Identifier", "Name", "Panel", "Location", "Port", "Username", "Password", "IP", "AdditionalIP", "DomainType", "DNS1", "DNS2", "DNS3", "Settings", "Status", "DefaultDNSTemplate"];
    }
    public function show($id = NULL)
    {
        $id = 0 < $id ? $id : $this->Identifier;
        if(!is_numeric($id)) {
            $this->Error[] = __("invalid identifier for server");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_Servers")->where("id", $id)->execute();
        if(!isset($result->id) || empty($result->id)) {
            $this->Error[] = __("invalid identifier for server");
            return false;
        }
        foreach ($result as $key => $value) {
            $this->{$key} = htmlspecialchars($value);
        }
        $this->Password = passcrypt($this->Password);
        $this->Identifier = $id;
        $this->VersionInfo = [];
        if($this->Panel && file_exists("3rdparty/hosting/" . $this->Panel . "/version.php")) {
            $version = [];
            include "3rdparty/hosting/" . $this->Panel . "/version.php";
            $this->VersionInfo = $version;
        }
        if($this->Settings) {
            $this->Settings = json_decode(htmlspecialchars_decode($this->Settings));
        }
        return true;
    }
    public function add()
    {
        if(!$this->validate()) {
            return false;
        }
        $result = Database_Model::getInstance()->insert("HostFact_Servers", ["Name" => $this->Name, "Panel" => $this->Panel, "Location" => $this->Location, "Port" => $this->Port, "Username" => $this->Username, "Password" => passcrypt($this->Password), "IP" => $this->IP, "AdditionalIP" => $this->AdditionalIP, "DomainType" => $this->DomainType, "DNS1" => $this->DNS1, "DNS2" => $this->DNS2, "DNS3" => $this->DNS3, "Settings" => json_encode($this->Settings), "Status" => $this->Status])->execute();
        if($result) {
            $this->Identifier = $result;
            if(is_module_active("dnsmanagement")) {
                global $_module_instances;
                $dnsmanagement = $_module_instances["dnsmanagement"];
                $dnsmanagement->integration_is_added($this, "server");
                $this->Error = array_merge($this->Error, $dnsmanagement->Error);
                $this->Success = array_merge($this->Success, $dnsmanagement->Success);
            }
            array_unshift($this->Success, sprintf(__("server is created"), $this->Name));
            return true;
        }
        return false;
    }
    public function edit($id)
    {
        $id = 0 < $id ? $id : $this->Identifier;
        if(!is_numeric($id)) {
            $this->Error[] = __("invalid identifier for server");
            return false;
        }
        if(!$this->validate()) {
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Servers", ["Name" => $this->Name, "Panel" => $this->Panel, "Location" => $this->Location, "Port" => $this->Port, "Username" => $this->Username, "Password" => passcrypt($this->Password), "IP" => $this->IP, "AdditionalIP" => $this->AdditionalIP, "DomainType" => $this->DomainType, "DNS1" => $this->DNS1, "DNS2" => $this->DNS2, "DNS3" => $this->DNS3, "Settings" => json_encode($this->Settings), "Status" => $this->Status])->where("id", $id)->execute();
        if($result) {
            $this->Identifier = $id;
            if(is_module_active("dnsmanagement")) {
                global $_module_instances;
                $dnsmanagement = $_module_instances["dnsmanagement"];
                $dnsmanagement->integration_is_added($this, "server");
                $this->Error = array_merge($this->Error, $dnsmanagement->Error);
                $this->Success = array_merge($this->Success, $dnsmanagement->Success);
            }
            $this->Success[] = sprintf(__("server is adjusted"), $this->Name);
            return true;
        }
        return false;
    }
    public function delete($id)
    {
        if(!is_numeric($id)) {
            $this->Error[] = __("invalid identifier for server");
            return false;
        }
        $result_server = Database_Model::getInstance()->getOne("HostFact_Servers", ["Status", "Name"])->where("id", $id)->execute();
        if(!$result_server) {
            $this->Error[] = __("invalid identifier for server");
            return false;
        }
        $current_status = $result_server->Status;
        $count_hosting = Database_Model::getInstance()->getOne("HostFact_Hosting", "COUNT(`id`) as CountPresent")->where("Server", $id)->where("Status", ["!=" => 9])->execute();
        if($count_hosting) {
            if($count_hosting->CountPresent == 1) {
                $this->Error[] = __("cannot delete server connected to hosting accounts");
                return false;
            }
            $count_debtor = Database_Model::getInstance()->getOne("HostFact_Debtors", "COUNT(`id`) as CountPresent")->where("Server", $id)->where("Status", ["!=" => 9])->execute();
            if($count_debtor) {
                if($count_debtor->CountPresent == 1) {
                    $this->Error[] = __("cannot delete server connected to debtors");
                    return false;
                }
                $count_package = Database_Model::getInstance()->getOne("HostFact_Packages", "COUNT(`id`) as CountPresent")->where("Server", $id)->where("Status", ["!=" => 9])->execute();
                if($count_package) {
                    if($count_package->CountPresent == 1) {
                        $this->Error[] = __("cannot delete server connected to packages");
                        return false;
                    }
                    if($current_status == 9) {
                        $result = Database_Model::getInstance()->delete("HostFact_Servers")->where("id", $id)->execute();
                        return $result ? true : false;
                    }
                    $result = Database_Model::getInstance()->update("HostFact_Servers", ["Status" => 9])->where("id", $id)->execute();
                    if(!$result) {
                        return false;
                    }
                    $this->Success[] = sprintf(__("server is removed"), $result_server->Name);
                    return true;
                }
                return false;
            }
            return false;
        }
        return false;
    }
    public function validate()
    {
        if(!trim($this->Name)) {
            $this->Error[] = __("please enter a server name");
        }
        if(!$this->is_free($this->Name)) {
            $this->Error[] = __("servername already in use");
        }
        global $array_domaintype;
        if(!in_array($this->DomainType, $array_domaintype)) {
            $this->DomainType = "additional";
        }
        global $array_serverstatus;
        if(!array_key_exists($this->Status, $array_serverstatus)) {
            $this->Error[] = __("unknown status selected for server");
        }
        if($this->Panel != "" && $this->checkServerLogin() !== true && empty($this->Error)) {
            $this->Error[] = __("could not connect to server");
        }
        return empty($this->Error) ? true : false;
    }
    public function is_free($servername)
    {
        $result = Database_Model::getInstance()->getOne("HostFact_Servers", "id")->where("Name", $servername)->where("Status", ["!=" => 9])->execute();
        if(0 < $result->id) {
            if($result->id == $this->Identifier) {
                return true;
            }
            return false;
        }
        return true;
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
        $search_at = [];
        if($searchat && $searchfor) {
            $search_at = explode("|", $searchat);
        }
        $select = ["id"];
        foreach ($fields as $column) {
            $select[] = $column;
        }
        Database_Model::getInstance()->get("HostFact_Servers", $select);
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
            Database_Model::getInstance()->where("Status", ["<" => "8"]);
        }
        $list = [];
        $list["CountRows"] = 0;
        if($server_list = Database_Model::getInstance()->execute()) {
            $list["CountRows"] = Database_Model::getInstance()->rowCount("HostFact_Servers", "id");
            foreach ($server_list as $result) {
                $list[$result->id] = ["id" => $result->id];
                foreach ($fields as $column) {
                    $list[$result->id][$column] = htmlspecialchars($result->{$column});
                }
            }
        }
        return $list;
    }
    public function getServerInformation($ip, $port)
    {
        $ip = str_replace(["http://", "https://"], "", strtolower($ip));
        $opensock = @fsockopen($ip, $port, $errno, $errstr, 2);
        if($opensock) {
            fclose($opensock);
            return true;
        }
        return false;
    }
    public function checkServerLogin()
    {
        if($this->Panel == "") {
            return false;
        }
        if(!trim($this->Location)) {
            $this->Error[] = __("no server location known");
        }
        if(!trim($this->Port)) {
            $this->Error[] = __("no server port known");
        }
        if(!trim($this->Username)) {
            $this->Error[] = __("no server username given");
        }
        if(!trim($this->Password)) {
            $this->Error[] = __("no server password given");
        }
        if(!empty($this->Error)) {
            return false;
        }
        $api = $this->connect();
        if($api) {
            if(!($result = $api->validateLogin())) {
                $this->Error = array_merge($this->Error, $api->Error);
            }
            return $result;
        }
        return false;
    }
    public function connect()
    {
        if($this->Panel && @file_exists("3rdparty/hosting/" . $this->Panel . "/" . $this->Panel . ".php")) {
            require_once "3rdparty/hosting/" . $this->Panel . "/" . $this->Panel . ".php";
            $class = $this->Panel;
            $this->api = new $class();
            $this->api->Server_Address = $this->Location;
            $this->api->Server_AddressPort = $this->Port;
            $this->api->Server_User = $this->Username;
            $this->api->Server_Pass = $this->Password;
            $this->api->Server_ServerIP = $this->IP;
            $this->api->ServerIP = $this->IP;
            $this->api->ServerAdditionalIP = $this->AdditionalIP;
            $this->api->Server_Settings = $this->Settings;
            return $this->api;
        }
        $this->Error[] = __("server api not found");
    }
    public function getPackageInformation($packageName, $reseller = false)
    {
        $api = $this->connect();
        if(is_object($api)) {
            $api->getPackage($packageName, $reseller);
            return $api;
        }
        return false;
    }
    public function getListPackages($id)
    {
        $empty_list = ["user" => [], "reseller" => []];
        if(empty($id)) {
            return $empty_list;
        }
        if(!is_numeric($id)) {
            $this->Error[] = __("invalid identifier for server");
            return $empty_list;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_Servers", "Panel")->where("id", $id)->execute();
        if(is_object($result)) {
            $this->Identifier = $id;
            $this->show();
            if(!$this->Panel) {
                return $empty_list;
            }
            $api = $this->connect();
            if(is_object($api)) {
                $list_server_packages = $api->listPackages();
                return $list_server_packages;
            }
            return $empty_list;
        }
        $this->Error[] = __("invalid identifier for server");
        return $empty_list;
    }
    public function deconnectDebtors($debtor_list)
    {
        if(!is_array($debtor_list) || empty($debtor_list)) {
            $this->Error[] = __("no debtors to unconnect from server");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Debtors", ["Server" => 0])->where("id", ["IN" => $debtor_list])->execute();
        if(!$result) {
            return false;
        }
        $this->Success[] = sprintf(__("debtors unconnected from server"), count($debtor_list));
        return true;
    }
    public function getClientIDS($sort = "Debtor", $order = "ASC", $limit = "-1", $show_results = MAX_RESULTS_LIST)
    {
        Database_Model::getInstance()->get(["HostFact_ServersPleskClients", "HostFact_Debtors"], ["HostFact_ServersPleskClients.ClientID", "HostFact_ServersPleskClients.DebtorID", "HostFact_Debtors.DebtorCode", "HostFact_Debtors.Initials", "HostFact_Debtors.SurName", "HostFact_Debtors.CompanyName", "HostFact_Debtors.EmailAddress"])->where("HostFact_ServersPleskClients.ServerID", $this->Identifier)->where("HostFact_ServersPleskClients.DebtorID = HostFact_Debtors.id");
        if($sort == "Debtor") {
            Database_Model::getInstance()->orderBy("CONCAT(HostFact_Debtors.`CompanyName`, HostFact_Debtors.`SurName`)", $order ? $order : "ASC");
        } else {
            Database_Model::getInstance()->orderBy("HostFact_ServersPleskClients.ClientID", $order ? $order : "ASC");
        }
        if(0 <= $limit) {
            Database_Model::getInstance()->limit((max(1, $limit) - 1) * $show_results, $show_results);
        }
        $list = [];
        if($result = Database_Model::getInstance()->execute()) {
            $list["CountRows"] = $this->CountRows = Database_Model::getInstance()->rowCount(["HostFact_ServersPleskClients", "HostFact_Debtors"], "ClientID");
            foreach ($result as $_clientids) {
                $list[$_clientids->ClientID] = [];
                foreach ($_clientids as $column => $val) {
                    $list[$_clientids->ClientID][$column] = htmlspecialchars($val);
                }
            }
        }
        return $list;
    }
    public function checkSameClientID($server_id, $debtor_id, $client_id)
    {
        $result = Database_Model::getInstance()->getOne("HostFact_ServersPleskClients")->where("ServerID", $server_id)->where("DebtorID", $debtor_id)->execute();
        if(!$result) {
            return true;
        }
        if($result->ClientID == $client_id) {
            return true;
        }
        $hosting_accounts = Database_Model::getInstance()->get("HostFact_Hosting", "id")->where("Server", $server_id)->where("Debtor", $debtor_id)->where("Status", ["IN" => [4, 5]])->execute();
        if(!empty($hosting_accounts)) {
            return false;
        }
        return true;
    }
    public function connectClientIDS($server_id, $debtor_id, $client_id)
    {
        if(!is_numeric($server_id) || $server_id <= 0) {
            $this->Error[] = __("invalid identifier for server");
            return false;
        }
        $delete_result = Database_Model::getInstance()->delete("HostFact_ServersPleskClients")->where("ServerID", $server_id)->where("DebtorID", $debtor_id)->execute();
        $result = Database_Model::getInstance()->insert("HostFact_ServersPleskClients", ["ServerID" => $server_id, "DebtorID" => $debtor_id, "ClientID" => $client_id])->execute();
        if($result) {
            $this->Success[] = __("clientid connected to server");
            return true;
        }
        return false;
    }
    public function deconnectClientIDS($server_id, $client_list)
    {
        if(!is_array($client_list) || empty($client_list)) {
            $this->Error[] = __("no clientids to unconnect from server");
            return false;
        }
        $result = Database_Model::getInstance()->delete("HostFact_ServersPleskClients")->where("ServerID", $server_id)->where("ClientID", ["IN" => $client_list])->execute();
        if(!$result) {
            return false;
        }
        $this->Success[] = sprintf(__("clientids unconnected from server"), count($client_list));
        return true;
    }
    public function getControlPanels($only_in_use = false)
    {
        $list = scandir("3rdparty/hosting/");
        $api_list = [];
        foreach ($list as $dir) {
            if($dir != "." && $dir != ".." && is_dir("3rdparty/hosting/" . $dir) && strpos($dir, "-outdated") === false) {
                $file_list = scandir("3rdparty/hosting/" . $dir . "/");
                if(in_array("version.php", $file_list)) {
                    $version = [];
                    include "3rdparty/hosting/" . $dir . "/version.php";
                    if(!isset($version["version"])) {
                        $version["version"] = $version["wefact_version"];
                    }
                    $api_list[$dir] = $version;
                }
            }
        }
        if($only_in_use === true) {
            $result = Database_Model::getInstance()->get("HostFact_Servers", "Panel")->where("Panel", ["!=" => ""])->where("Location", ["!=" => ""])->where("Username", ["!=" => ""])->where("Password", ["!=" => ""])->where("Status", ["!=" => "9"])->groupBy("Panel")->execute();
            $controlpanels_in_use = [];
            if(isset($result) && !empty($result)) {
                foreach ($result as $_controlpanel) {
                    $controlpanels_in_use[$_controlpanel->Panel] = $_controlpanel->Panel;
                }
            }
            $api_list = array_intersect_key($api_list, $controlpanels_in_use);
        }
        return $api_list;
    }
    public function getVersionInfo()
    {
        $this->VersionInfo = NULL;
        if(@file_exists("3rdparty/hosting/" . $this->Panel . "/version.php")) {
            include "3rdparty/hosting/" . $this->Panel . "/version.php";
            $this->VersionInfo = $version;
            return true;
        }
        return false;
    }
    public function getAvailableControlPanels()
    {
        if(isset($_SESSION["wf_cache_controlpanels"]) && is_array($_SESSION["wf_cache_controlpanels"])) {
            $array_controlpanels = $_SESSION["wf_cache_controlpanels"];
        } else {
            $array_controlpanels = ["" => __("controlpanel other")];
            $cp_candidates = scandir("3rdparty/hosting/");
            foreach ($cp_candidates as $cp_candidate) {
                if(!in_array(strtolower($cp_candidate), [".", "..", "acontrolpanel.php"]) && is_dir("3rdparty/hosting/" . $cp_candidate)) {
                    $version = [];
                    if(file_exists("3rdparty/hosting/" . $cp_candidate . "/version.php")) {
                        include "3rdparty/hosting/" . $cp_candidate . "/version.php";
                    }
                    $array_controlpanels[$cp_candidate] = isset($version["name"]) ? htmlspecialchars($version["name"]) : $cp_candidate;
                }
            }
            unset($cp_candidates);
            unset($cp_candidate);
            unset($version);
            asort($array_controlpanels, SORT_NATURAL | SORT_FLAG_CASE);
            $_SESSION["wf_cache_controlpanels"] = $array_controlpanels;
        }
        return $array_controlpanels;
    }
    public function getControlPanelName($panel)
    {
        $array_controlpanels = $this->getAvailableControlPanels();
        return $array_controlpanels[$panel];
    }
}

?>