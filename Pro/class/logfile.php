<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class logfile
{
    public $Identifier;
    public $Date;
    public $Type;
    public $Reference;
    public $Who;
    public $Action;
    public $Values;
    public $Translate;
    public $Page;
    public $CountRows;
    public $Table;
    public $Error;
    public $Variables = ["Identifier", "Date", "Type", "Reference", "Who", "Action", "Values", "Translate", "Page"];
    public function __construct($id = "")
    {
        global $account;
        $this->Date = date("YmdHis");
        $this->Type = "invoice";
        $this->Reference = $id;
        $this->Who = isset($account->Identifier) ? $account->Identifier : 0;
        $this->Action = "";
        $this->Page = isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : "";
        $this->Error = [];
        $this->Warning = [];
        $this->Success = [];
        $this->Translate = "no";
        $this->Values = "";
        if(defined("API_CALL_FROM_CLIENTAREA") && API_CALL_FROM_CLIENTAREA === true) {
            $this->Who = "clientarea";
        } elseif((int) $this->Who === 0 && defined("API_DIR") && API_DIR) {
            $this->Who = "api";
        } elseif(defined("SCRIPT_IS_CRONJOB") && SCRIPT_IS_CRONJOB) {
            $this->Who = "cronjob";
        }
    }
    public function add()
    {
        if(is_array($this->Values)) {
            $this->Values = implode("|", $this->Values);
        }
        $result = Database_Model::getInstance()->insert("HostFact_Log", ["Date" => $this->Date, "Type" => $this->Type, "Reference" => $this->Reference, "Who" => $this->Who, "Action" => $this->Action, "Values" => $this->Values, "Translate" => $this->Translate, "Page" => $this->Page])->execute();
        if($result) {
            $this->Identifier = $result;
            return true;
        }
        return false;
    }
    public function deleteEntry($Identifier)
    {
        $result = Database_Model::getInstance()->delete("HostFact_Log")->where("id", $Identifier)->execute();
        if($result) {
            return true;
        }
        return false;
    }
    public function all($fields, $sort = false, $order = false, $limit = "-1", $searchat = false, $searchfor = false, $show_results = MAX_RESULTS_LIST)
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
        $EmployeeArray = ["Name"];
        $select = ["HostFact_Log.id"];
        foreach ($fields as $column) {
            if($column == "Name") {
                $select[] = "IF(HostFact_Log.`Who` > 0, HostFact_Employee.`" . $column . "`, HostFact_Log.`Who`) as `Name`";
            } elseif(in_array($column, $EmployeeArray)) {
                $select[] = "HostFact_Employee." . $column;
            } else {
                $select[] = "HostFact_Log." . $column;
            }
        }
        Database_Model::getInstance()->get("HostFact_Log", $select);
        $EmployeeFields = 0 < count(array_intersect($EmployeeArray, $fields)) ? true : false;
        if($EmployeeFields) {
            Database_Model::getInstance()->join("HostFact_Employee", "HostFact_Employee.id = HostFact_Log.Who");
        }
        if(0 < $searchfor) {
            Database_Model::getInstance()->where("HostFact_Log.Reference", $searchfor)->where("HostFact_Log.Type", $searchat);
        }
        $order = $sort ? $order ? $order : "ASC" : "";
        if($sort == "Who") {
            Database_Model::getInstance()->orderBy("Name", $order);
        } elseif(in_array($sort, $EmployeeArray)) {
            Database_Model::getInstance()->orderBy("IF(HostFact_Log.`Who` > 0, HostFact_Employee.`" . $column . "`, HostFact_Log.`Who`)", $order);
        } elseif($sort) {
            Database_Model::getInstance()->orderBy("HostFact_Log." . $sort, $order);
        }
        if(0 <= $limit) {
            Database_Model::getInstance()->limit((max(1, $limit) - 1) * $show_results, $show_results);
        }
        if($sort && $order) {
            Database_Model::getInstance()->orderBy("HostFact_Log.id", $order);
        }
        $list = [];
        $this->CountRows = 0;
        if($log_list = Database_Model::getInstance()->execute()) {
            $this->CountRows = Database_Model::getInstance()->rowCount("HostFact_Log", "HostFact_Log.id");
            foreach ($log_list as $result) {
                $list[$result->id] = ["id" => $result->id];
                foreach ($fields as $column) {
                    $list[$result->id][$column] = htmlspecialchars($result->{$column});
                }
            }
        }
        $list["CountRows"] = $this->CountRows;
        return $list;
    }
}

?>