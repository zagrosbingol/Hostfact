<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class apilogfile
{
    public $Identifier;
    public $Type;
    public $Action;
    public $Page;
    public $CountRows;
    public $Table;
    public $Error;
    public $SearchString;
    public $Variables = ["Identifier", "DateTime", "Controller", "Action", "Input", "ResponseType", "Response", "IP"];
    public function __construct()
    {
    }
    public function show()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_API_Calls")->where("id", $this->Identifier)->execute();
        if(!$result || empty($result->id)) {
            $this->Error[] = __("invalid identifier");
            return false;
        }
        foreach ($result as $key => $value) {
            if($key != "id") {
                $this->{$key} = $value;
            }
        }
        return true;
    }
    public function all($fields, $sort = false, $order = false, $limit = "-1", $searchat = false, $searchfor = false, $group = "false", $show_results = MAX_RESULTS_LIST)
    {
        $limit = !is_numeric($show_results) ? -1 : $limit;
        $pdo_bind = [];
        if(!is_array($fields) || empty($fields)) {
            $this->Error[] = __("no fields given for retrieving data");
            return false;
        }
        if(!is_numeric($show_results)) {
            $this->Error[] = __("invalid number for displaying results");
            return false;
        }
        $fields[] = "id";
        Database_Model::getInstance()->get("HostFact_API_Calls", $fields);
        if($searchat && (0 < strlen($searchfor) || $searchfor)) {
            $search_at = explode("|", $searchat);
        }
        if(!empty($search_at)) {
            $or_clausule = [];
            foreach ($search_at as $searchColumn) {
                $or_clausule[] = [$searchColumn, ["LIKE" => "%" . $searchfor . "%"]];
            }
            Database_Model::getInstance()->orWhere($or_clausule);
        }
        if($group == "error") {
            Database_Model::getInstance()->orWhere([["ResponseType", "error"], ["ResponseType", ""]]);
        } elseif($group != "false") {
            Database_Model::getInstance()->where("ResponseType", $group);
        }
        if($sort) {
            Database_Model::getInstance()->orderBy($sort, $order)->orderBy("id", $order);
        }
        if(0 <= $limit) {
            Database_Model::getInstance()->limit((max(1, $limit) - 1) * $show_results, $show_results);
        }
        $list = [];
        if($result = Database_Model::getInstance()->asArray()->execute()) {
            $list["CountRows"] = Database_Model::getInstance()->rowCount("HostFact_API_Calls", "id");
            foreach ($result as $_api_logline) {
                foreach ($fields as $column) {
                    $list[$_api_logline["id"]][$column] = htmlspecialchars($_api_logline[$column]);
                }
            }
        }
        return $list;
    }
    public function cleanUp()
    {
        $days = intval(API_CLEAN_LOG_AFTER_DAYS);
        if(0 < $days) {
            Database_Model::getInstance()->delete("HostFact_API_Calls")->where("DateTime", ["<" => ["RAW" => "DATE_ADD(CURDATE(), INTERVAL -:days DAY)"]])->bindValue(":days", $days)->execute();
        }
    }
    public function countAll()
    {
        $return = ["StatusOptions" => [], "CountRows" => 0];
        $result = Database_Model::getInstance()->get("HostFact_API_Calls", ["ResponseType", "COUNT(`id`) AS Count"])->groupBy("ResponseType")->execute();
        foreach ($result as $data) {
            if($data->ResponseType == "") {
                $data->ResponseType = "error";
            }
            $return["StatusOptions"][] = $data->ResponseType;
            $return["CountRows"] += $data->Count;
        }
        $return["StatusOptions"] = array_unique($return["StatusOptions"]);
        return $return;
    }
}

?>