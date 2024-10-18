<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class topleveldomain
{
    public $Identifier;
    public $Tld;
    public $Registrar;
    public $WhoisServer;
    public $WhoisNoMatch;
    public $AskForAuthKey;
    public $AllowedIDNCharacters;
    public $CountRows;
    public $Error;
    public $Succes;
    public $Variables;
    public function __construct()
    {
        $this->AskForAuthKey = "yes";
        $this->Variables = ["Identifier", "Tld", "OwnerChangeCost", "Registrar", "WhoisServer", "WhoisNoMatch", "AskForAuthKey", "AllowedIDNCharacters"];
    }
    public function show($id = NULL)
    {
        if(!is_numeric($id)) {
            $this->Error[] = __("invalid identifier for topleveldomain");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_TopLevelDomain", ["HostFact_TopLevelDomain.*", "HostFact_Products.ProductCode", "HostFact_Products.ProductName", "HostFact_Products.PriceExcl", "HostFact_Registrar.Name"])->join("HostFact_Products", "HostFact_Products.id = HostFact_TopLevelDomain.OwnerChangeCost")->join("HostFact_Registrar", "HostFact_Registrar.id = HostFact_TopLevelDomain.Registrar")->where("HostFact_TopLevelDomain.id", $id)->execute();
        if(!isset($result->id) || empty($result->id)) {
            $this->Error[] = __("invalid identifier for topleveldomain");
            return false;
        }
        foreach ($result as $key => $value) {
            $this->{$key} = htmlspecialchars($value);
        }
        $this->Identifier = $id;
        return true;
    }
    public function showbyTLD($tld, $retunBool = false)
    {
        if(!$tld) {
            $this->Error[] = __("invalid identifier for topleveldomain");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_TopLevelDomain", ["HostFact_TopLevelDomain.*", "HostFact_Products.ProductCode", "HostFact_Products.ProductName", "HostFact_Products.PriceExcl", "HostFact_Products.TaxPercentage", "HostFact_Registrar.Name"])->join("HostFact_Products", "HostFact_Products.id = HostFact_TopLevelDomain.OwnerChangeCost")->join("HostFact_Registrar", "HostFact_Registrar.id = HostFact_TopLevelDomain.Registrar")->where("HostFact_TopLevelDomain.Tld", $tld)->execute();
        if($retunBool === true) {
            return !isset($result->id) || empty($result->id) ? false : true;
        }
        if(!isset($result->id) || empty($result->id)) {
            $this->Error[] = __("invalid identifier for topleveldomain");
            return false;
        }
        foreach ($result as $key => $value) {
            $this->{$key} = htmlspecialchars($value);
        }
        $this->PriceIncl = VAT_CALC_METHOD == "incl" ? round((double) $this->PriceExcl * (1 + (double) $this->TaxPercentage), 5) : round(round((double) $this->PriceExcl, 2) * (1 + (double) $this->TaxPercentage), 2);
        $this->Identifier = $result->id;
        return true;
    }
    public function add()
    {
        if(!$this->validate()) {
            return false;
        }
        $result = Database_Model::getInstance()->insert("HostFact_TopLevelDomain", ["Tld" => $this->Tld, "OwnerChangeCost" => $this->OwnerChangeCost, "Registrar" => $this->Registrar, "WhoisServer" => $this->WhoisServer, "WhoisNoMatch" => $this->WhoisNoMatch, "AskForAuthKey" => $this->AskForAuthKey, "AllowedIDNCharacters" => $this->AllowedIDNCharacters])->execute();
        if($result) {
            $this->Identifier = $result;
            $this->Success[] = sprintf(__("tld is created"), $this->Tld);
            return true;
        }
        return false;
    }
    public function edit($id)
    {
        if(!$id) {
            $this->Error[] = __("invalid identifier for topleveldomain");
            return false;
        }
        if(!$this->validate()) {
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_TopLevelDomain", ["Tld" => $this->Tld, "OwnerChangeCost" => $this->OwnerChangeCost, "Registrar" => $this->Registrar, "WhoisServer" => $this->WhoisServer, "WhoisNoMatch" => $this->WhoisNoMatch, "AskForAuthKey" => $this->AskForAuthKey, "AllowedIDNCharacters" => $this->AllowedIDNCharacters])->where("id", $id)->execute();
        if($result) {
            $this->Identifier = $id;
            $this->Success[] = sprintf(__("tld is adjusted"), $this->Tld);
            return true;
        }
        return false;
    }
    public function delete($id)
    {
        if(!$id) {
            $this->Error[] = __("invalid identifier for topleveldomain");
            return false;
        }
        $result = Database_Model::getInstance()->delete("HostFact_TopLevelDomain")->where("id", $id)->execute();
        if(!$result) {
            return false;
        }
        return true;
    }
    public function validate()
    {
        $this->Tld = strtolower($this->Tld);
        if(substr($this->Tld, 0, 1) == ".") {
            $this->Tld = substr($this->Tld, 1);
        }
        if(!trim($this->Tld)) {
            $this->Error[] = __("please enter a TLD");
        }
        if(!$this->is_free($this->Tld)) {
            $this->Error[] = __("TLD already exists");
        }
        if(strlen($this->Tld) < 2) {
            $this->Error[] = __("TLD must be at least 2 characters long");
        }
        if(preg_match("/^[^\\\\ \\/@]{2,63}\$/iu", $this->Tld) == 0) {
            $this->Error[] = __("TLD is invalid");
        }
        return empty($this->Error) ? true : false;
    }
    public function is_free($tld)
    {
        if(trim($tld)) {
            $result = Database_Model::getInstance()->getOne("HostFact_TopLevelDomain")->where("Tld", $tld)->execute();
            $numrows = $result ? 1 : 0;
            if($result->id == $this->Identifier) {
                $numrows = 0;
            }
            return $numrows === 0 ? true : false;
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
        $select = ["HostFact_TopLevelDomain.id"];
        $DomainArray = ["DomainNumber"];
        $RegistrarArray = ["Name"];
        foreach ($fields as $column) {
            if(in_array($column, $DomainArray)) {
                if($column == "DomainNumber") {
                    $select[] = "COUNT(HostFact_Domains.`Tld`) as DomainNumber";
                }
            } elseif(in_array($column, $RegistrarArray)) {
                $select[] = "HostFact_Registrar.`" . $column . "`";
            } else {
                $select[] = "HostFact_TopLevelDomain.`" . $column . "`";
            }
        }
        Database_Model::getInstance()->get("HostFact_TopLevelDomain", $select);
        $search_at = [];
        $DomainSearch = $RegistrarSearch = false;
        if(isset($searchat) && $searchat && isset($searchfor) && $searchfor) {
            $search_at = explode("|", $searchat);
            $DomainSearch = 0 < count(array_intersect($DomainArray, $search_at)) ? true : false;
            $RegistrarSearch = 0 < count(array_intersect($RegistrarArray, $search_at)) ? true : false;
        }
        if(0 < count(array_intersect($DomainArray, $fields)) || $DomainSearch) {
            Database_Model::getInstance()->join("HostFact_Domains", "HostFact_Domains.`Tld` = HostFact_TopLevelDomain.`Tld` AND HostFact_Domains.`Status` != 9");
        }
        if(0 < count(array_intersect($RegistrarArray, $fields)) || $RegistrarSearch) {
            Database_Model::getInstance()->join("HostFact_Registrar", "HostFact_TopLevelDomain.`Registrar` = HostFact_Registrar.`id`");
        }
        if(!empty($search_at)) {
            $or_clausule = [];
            foreach ($search_at as $searchColumn) {
                if(in_array($searchColumn, $DomainArray)) {
                    $or_clausule[] = ["HostFact_Domains.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                } elseif(in_array($searchColumn, $RegistrarArray)) {
                    $or_clausule[] = ["HostFact_Registrar.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                } else {
                    $or_clausule[] = ["HostFact_TopLevelDomain.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                }
            }
            Database_Model::getInstance()->orWhere($or_clausule);
        }
        if($sort == "DomainNumber") {
            Database_Model::getInstance()->orderBy("DomainNumber", $order);
        } elseif(in_array($sort, $DomainArray)) {
            Database_Model::getInstance()->orderBy("HostFact_Domains.`" . $sort . "`", $order);
        } elseif(in_array($sort, $RegistrarArray)) {
            Database_Model::getInstance()->orderBy("HostFact_Registrar.`" . $sort . "`", $order);
        } elseif($sort) {
            Database_Model::getInstance()->orderBy("HostFact_TopLevelDomain.`" . $sort . "`", $order);
        }
        if(0 <= $limit) {
            Database_Model::getInstance()->limit((max(1, $limit) - 1) * $show_results, $show_results);
        }
        Database_Model::getInstance()->groupBy("HostFact_TopLevelDomain.`TLD`");
        $list = [];
        $list["CountRows"] = 0;
        if($tld_list = Database_Model::getInstance()->execute()) {
            $this->CountRows = Database_Model::getInstance()->rowCount("HostFact_TopLevelDomain", "HostFact_TopLevelDomain.id");
            foreach ($tld_list as $result) {
                $list[$result->id] = ["id" => $result->id];
                foreach ($fields as $column) {
                    $list[$result->id][$column] = htmlspecialchars($result->{$column});
                }
            }
            $list["CountRows"] = $this->CountRows;
        }
        return $list;
    }
    public function syncWhoisServers($update_type)
    {
        $updated_counter = 0;
        $whois_server_list = $this->getWhoisServers();
        foreach ($whois_server_list as $tld => $tld_info) {
            Database_Model::getInstance()->update("HostFact_TopLevelDomain", ["WhoisServer" => $tld_info["server"], "WhoisNoMatch" => $tld_info["nomatch"]])->where("Tld", $tld);
            if($update_type == "update") {
            } else {
                Database_Model::getInstance()->where("WhoisServer", "");
            }
            $result = Database_Model::getInstance()->execute();
            if($result && 0 < Database_Model::getInstance()->getAffectedRows()) {
                $updated_counter++;
            }
        }
        $this->Success[] = sprintf(__("public whois servers changed for domains"), $updated_counter);
        return true;
    }
    public function syncWhoisServer($tld)
    {
        $whois_server_list = $this->getWhoisServers();
        if(isset($whois_server_list[$tld])) {
            $result = Database_Model::getInstance()->update("HostFact_TopLevelDomain", ["WhoisServer" => $whois_server_list[$tld]["server"], "WhoisNoMatch" => $whois_server_list[$tld]["nomatch"]])->where("Tld", $tld)->execute();
            if($result) {
                $this->Success[] = __("public whois server changed");
                return true;
            }
        } else {
            $this->Error[] = __("public whois server unknown");
            return false;
        }
    }
    public function getWhoisServers()
    {
        if(isset($_SESSION["whois_server_list"]) && is_array($_SESSION["whois_server_list"])) {
            $whois_server_list = $_SESSION["whois_server_list"];
        } else {
            $url = INTERFACE_URL . "/hosting/whois.txt";
            $content = getContent($url);
            $whois_server_list = [];
            if($content) {
                $lines = explode("\n", str_replace("\r\n", "\n", $content));
                foreach ($lines as $line) {
                    $line_expl = explode("|", $line, 3);
                    $whois_server_list[$line_expl[0]] = ["server" => $line_expl[1], "nomatch" => str_replace("_tab_", "\t", $line_expl[2])];
                }
            }
            $_SESSION["whois_server_list"] = $whois_server_list;
        }
        return $whois_server_list;
    }
    public function getIDNSupport()
    {
        if(isset($_SESSION["idn_support_list"]) && is_array($_SESSION["idn_support_list"])) {
            $idn_support_list = $_SESSION["idn_support_list"];
        } else {
            $url = INTERFACE_URL . "/hosting/idnsupport.txt";
            $content = getContent($url);
            $idn_support_list = [];
            if($content) {
                $lines = explode("\n", str_replace("\r\n", "\n", $content));
                foreach ($lines as $line) {
                    $line_expl = explode("|||", $line, 2);
                    $idn_support_list[$line_expl[0]] = $line_expl[1];
                }
            }
            $_SESSION["idn_support_list"] = $idn_support_list;
        }
        return $idn_support_list;
    }
    public function getAllowedIDNCharacters($tld = "")
    {
        Database_Model::getInstance()->get("HostFact_TopLevelDomain", ["AllowedIDNCharacters"]);
        if($tld) {
            Database_Model::getInstance()->where("Tld", $tld);
        }
        $result = Database_Model::getInstance()->execute();
        $idn_per_tld = $result;
        $idn = "";
        foreach ($idn_per_tld as $idn_tld) {
            $idn .= $idn_tld->AllowedIDNCharacters;
        }
        return $idn;
    }
}

?>