<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class messagelogfile
{
    public $Identifier;
    public $Date;
    public $Type;
    public $Message;
    public $Values;
    public $ObjectType;
    public $Reference;
    public $Who;
    public $Page;
    public $Error;
    public $Warning;
    public $Success;
    public $Variables = ["Identifier", "Date", "Type", "Message", "Values", "ObjectType", "Reference", "Who", "Page"];
    public function __construct()
    {
        global $account;
        $this->Date = date("YmdHis");
        $this->Who = isset($account->Identifier) ? $account->Identifier : 0;
        $this->Page = isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : "";
        if(defined("API_CALL_FROM_CLIENTAREA") && API_CALL_FROM_CLIENTAREA === true) {
            $this->Who = "clientarea";
        } elseif(defined("API_DIR") && API_DIR) {
            $this->Who = "api";
        } elseif(defined("SCRIPT_IS_CRONJOB") && SCRIPT_IS_CRONJOB) {
            $this->Who = "cronjob";
        }
    }
    public function add($type, $message, $values = [], $objecttype = "", $reference = 0)
    {
        if(is_array($values)) {
            $values = implode("|", $values);
        }
        $result = Database_Model::getInstance()->insert("HostFact_MessageLog", ["Date" => $this->Date, "Type" => $type, "Message" => $message, "Values" => $values, "ObjectType" => $objecttype, "Reference" => $reference, "Who" => $this->Who, "Page" => $this->Page])->execute();
        if($result) {
            $this->Identifier = $result;
            return true;
        }
        return false;
    }
    public function delete()
    {
        Database_Model::getInstance()->delete("HostFact_MessageLog")->noWhere()->execute();
    }
    public function deleteEntry($Identifier)
    {
        Database_Model::getInstance()->delete("HostFact_MessageLog")->where("id", $Identifier)->execute();
    }
    public function deleteEntries($highestID)
    {
        $result = Database_Model::getInstance()->getOne("HostFact_MessageLog", ["COUNT(`id`) as `Count`"])->where("id", ["<=" => $highestID])->execute();
        Database_Model::getInstance()->delete("HostFact_MessageLog")->where("id", ["<=" => $highestID])->execute();
        return $result->Count;
    }
    public function lastCronjob()
    {
        $result = Database_Model::getInstance()->getOne("HostFact_MessageLog", ["*"])->where("Who", ["IN" => ["0", "cronjob"]])->where("Date", [">=" => $_SESSION["LastDate"]])->where("Type", ["!=" => "success"])->execute();
        if(0 < $result->id) {
            $this->Warning[] = __("messagelogfile results from cronjob");
        }
    }
    public function all($fields, $sort = false, $order = false, $limit = "-1", $logtype = "", $show_results = MAX_RESULTS_LIST)
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
        $EmployeeFields = 0 < count(array_intersect($EmployeeArray, $fields)) ? true : false;
        $select = ["HostFact_MessageLog.id"];
        foreach ($fields as $column) {
            if($column == "Name") {
                $select[] = "IF(HostFact_MessageLog.`Who` > 0,HostFact_Employee.`Name`,HostFact_MessageLog.`Who`) as `Name`";
            } elseif(in_array($column, $EmployeeArray)) {
                $select[] = "HostFact_Employee.`" . $column . "`";
            } else {
                $select[] = "HostFact_MessageLog.`" . $column . "`";
            }
        }
        Database_Model::getInstance()->get("HostFact_MessageLog", $select);
        if($EmployeeFields) {
            Database_Model::getInstance()->join("HostFact_Employee", "HostFact_Employee.`id` = HostFact_MessageLog.`Who`");
        }
        if($logtype) {
            Database_Model::getInstance()->where("HostFact_MessageLog.Type", $logtype);
        }
        $order = $sort ? $order ? $order : "ASC" : "";
        if($sort == "Who") {
            Database_Model::getInstance()->orderBy("Name", $order);
        } elseif(in_array($sort, $EmployeeArray)) {
            Database_Model::getInstance()->orderBy("IF(HostFact_MessageLog.`Who` > 0,HostFact_Employee.`Name`,HostFact_MessageLog.`Who`)", $order);
        } elseif($sort) {
            Database_Model::getInstance()->orderBy("HostFact_MessageLog." . $sort, $order);
        }
        Database_Model::getInstance()->orderBy("HostFact_MessageLog.id", "ASC");
        if(0 <= $limit) {
            Database_Model::getInstance()->limit((max(1, $limit) - 1) * $show_results, $show_results);
        }
        $list = [];
        $this->CountRows = 0;
        $list["CountRows"] = 0;
        if($log_list = Database_Model::getInstance()->execute()) {
            $list["CountRows"] = $this->CountRows = Database_Model::getInstance()->rowCount("HostFact_MessageLog", "HostFact_MessageLog.id");
            foreach ($log_list as $result) {
                $list[$result->id] = ["id" => $result->id];
                foreach ($fields as $column) {
                    $list[$result->id][$column] = htmlspecialchars($result->{$column});
                }
            }
        }
        return $list;
    }
}

?>