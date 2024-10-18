<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class interaction
{
    public $Identifier;
    public $Debtor;
    public $Category;
    public $Date;
    public $Type;
    public $Author;
    public $Message;
    public $CountRows;
    public $Table;
    public $Error;
    public $Success;
    public $Warning;
    public $Variables = ["Identifier", "Debtor", "Category", "Date", "Type", "Author", "Message"];
    public function __construct()
    {
        $this->Date = date("Y-m-d H:i:s");
        $this->Author = $_SESSION["UserPro"];
        $this->Error = [];
        $this->Warning = [];
        $this->Success = [];
    }
    public function show()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for interaction");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_Interactions", ["*"])->where("id", $this->Identifier)->execute();
        if(!$result || empty($result->id)) {
            $this->Error[] = __("invalid identifier for interaction");
            return false;
        }
        foreach ($result as $key => $value) {
            $this->{$key} = htmlspecialchars($value);
        }
        return true;
    }
    public function add()
    {
        if(!$this->validate()) {
            return false;
        }
        $result = Database_Model::getInstance()->insert("HostFact_Interactions", ["Debtor" => $this->Debtor, "Category" => $this->Category, "Date" => $this->Date, "Type" => $this->Type, "Author" => $this->Author, "Message" => $this->Message])->execute();
        if($result) {
            $this->Identifier = $result;
            return true;
        }
        return false;
    }
    public function edit()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for interaction");
            return false;
        }
        if(!$this->validate()) {
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Interactions", ["Debtor" => $this->Debtor, "Category" => $this->Category, "Date" => $this->Date, "Type" => $this->Type, "Author" => $this->Author, "Message" => $this->Message])->where("id", $this->Identifier)->execute();
        if($result) {
            return true;
        }
        return false;
    }
    public function delete()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for interaction");
            return false;
        }
        $result = Database_Model::getInstance()->delete("HostFact_Interactions")->where("id", $this->Identifier)->execute();
        if($result) {
            $this->Success[] = sprintf(__("interaction deleted"), rewrite_date_db2site($this->Date, "%d-%m-%Y " . __("at") . " %H:%i"));
            return true;
        }
        return false;
    }
    public function validate()
    {
        if(!is_numeric($this->Debtor)) {
            $this->Error[] = __("invalid debtor");
        }
        if(!is_date(rewrite_date_site2db($this->Date))) {
            $this->Error[] = __("invalid interaction date");
        }
        if(!(is_string($this->Message) && 0 < strlen($this->Message))) {
            $this->Error[] = __("invalid message for interaction");
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
        $DebtorArray = ["DebtorCode", "CompanyName", "SurName", "Initials"];
        $DebtorFields = 0 < count(array_intersect($DebtorArray, $fields)) ? true : false;
        $search_at = [];
        $DebtorSearch = false;
        if($searchat && (0 < strlen($searchfor) || $searchfor)) {
            $search_at = explode("|", $searchat);
            $DebtorSearch = 0 < count(array_intersect($DebtorArray, $search_at)) ? true : false;
        }
        $select = ["HostFact_Interactions.id"];
        foreach ($fields as $column) {
            if(in_array($column, $DebtorArray)) {
                $select[] = "HostFact_Debtors.`" . $column . "`";
            } else {
                $select[] = "HostFact_Interactions.`" . $column . "`";
            }
        }
        Database_Model::getInstance()->get("HostFact_Interactions", $select);
        if($DebtorFields || $DebtorSearch) {
            Database_Model::getInstance()->join("HostFact_Debtors", "HostFact_Debtors.`id` = HostFact_Interactions.`Debtor`");
        }
        if(!empty($search_at)) {
            $or_clausule = [];
            foreach ($search_at as $searchColumn) {
                if(in_array($searchColumn, ["Debtor"])) {
                    $or_clausule[] = ["HostFact_Interactions.`" . $searchColumn . "`", $searchfor];
                } elseif(in_array($searchColumn, $DebtorArray)) {
                    $or_clausule[] = ["HostFact_Debtors.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                } else {
                    $or_clausule[] = ["HostFact_Interactions.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                }
            }
            Database_Model::getInstance()->orWhere($or_clausule);
        }
        $order = $sort ? $order ? $order : "ASC" : "";
        if(in_array($sort, $DebtorArray)) {
            Database_Model::getInstance()->orderBy("HostFact_Debtors.`" . $sort . "`", $order);
        } elseif($sort == "Date` DESC, `id") {
            Database_Model::getInstance()->orderBy("HostFact_Interactions.Date", "DESC")->orderBy("HostFact_Interactions.id", $order);
        } elseif($sort) {
            Database_Model::getInstance()->orderBy("HostFact_Interactions." . $sort, $order);
        }
        if(0 <= $limit) {
            Database_Model::getInstance()->limit((max(1, $limit) - 1) * $show_results, $show_results);
        }
        if(is_numeric($group)) {
            Database_Model::getInstance()->where("HostFact_Interactions.`Debtor`", $group);
        }
        $list = [];
        $this->CountRows = 0;
        $list["CountRows"] = 0;
        if($interaction_list = Database_Model::getInstance()->execute()) {
            $list["CountRows"] = $this->CountRows = Database_Model::getInstance()->rowCount("HostFact_Interactions", "HostFact_Interactions.id");
            foreach ($interaction_list as $result) {
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