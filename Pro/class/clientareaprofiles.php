<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class ClientareaProfiles_Model
{
    public $Error;
    public $Success;
    public $Warning;
    public function __construct()
    {
        $this->StatusArray = ["active" => __("action status active"), "removed" => __("action status removed")];
        $this->Status = "active";
        $this->Default = "";
        $this->Error = $this->Warning = $this->Success = [];
    }
    public function show()
    {
        if(!is_numeric($this->id)) {
            $this->Error[] = __("invalid identifier for clientarea profile");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_Clientarea_Profiles")->where("id", $this->id)->execute();
        if(!$result) {
            return false;
        }
        foreach ($result as $key => $value) {
            if($key == "Rights" || $key == "Orderforms") {
                $this->{$key} = json_decode($value, true);
            } else {
                $this->{$key} = $value;
            }
        }
        return true;
    }
    public function showDefault()
    {
        $result = Database_Model::getInstance()->getOne("HostFact_Clientarea_Profiles", "id")->where("Default", "yes")->where("Status", "active")->execute();
        if(!$result) {
            $this->Error[] = __("no default clientarea profile could be found");
            return false;
        }
        $this->id = $result->id;
        return $this->show();
    }
    public function add()
    {
        if(!$this->__validate()) {
            return false;
        }
        $result = Database_Model::getInstance()->insert("HostFact_Clientarea_Profiles", ["Name" => $this->Name, "WelcomeTitle" => $this->WelcomeTitle, "WelcomeMessage" => $this->WelcomeMessage, "Rights" => json_encode($this->Rights), "Orderforms" => json_encode($this->Orderforms), "TwoFactorAuthentication" => $this->TwoFactorAuthentication, "Default" => isset($this->Default) && $this->Default == "yes" ? "yes" : "no", "Status" => $this->Status])->execute();
        if(!$result) {
            return false;
        }
        $this->Success[] = sprintf(__("clientarea profile added"), $this->Name);
        $this->id = $result;
        return true;
    }
    public function edit()
    {
        if(!is_numeric($this->id)) {
            $this->Error[] = __("invalid identifier for clientarea profile");
            return false;
        }
        if(!$this->__validate()) {
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Clientarea_Profiles", ["Name" => $this->Name, "WelcomeTitle" => $this->WelcomeTitle, "WelcomeMessage" => $this->WelcomeMessage, "Rights" => json_encode($this->Rights), "Orderforms" => json_encode($this->Orderforms), "TwoFactorAuthentication" => $this->TwoFactorAuthentication, "Status" => $this->Status])->where("id", $this->id)->execute();
        if(!$result) {
            return false;
        }
        $this->Success[] = sprintf(__("clientarea profile edited"), $this->Name);
        return true;
    }
    public function listProfiles()
    {
        $fields = isset($options["fields"]) && is_array($options["fields"]) ? $options["fields"] : [];
        $sort_by = isset($options["sort_by"]) && $options["sort_by"] ? $options["sort_by"] : "";
        $sort_order = isset($options["sort_order"]) && $options["sort_order"] ? $options["sort_order"] : "";
        $offset = isset($options["offset"]) && $options["offset"] ? $options["offset"] : "0";
        $results_per_page = isset($options["results_per_page"]) && $options["results_per_page"] ? $options["results_per_page"] : MAX_RESULTS_LIST;
        $filter = isset($options["filter"]) && $options["filter"] ? $options["filter"] : "";
        $filters = isset($options["filters"]) && $options["filters"] ? $options["filters"] : [];
        $group_by = isset($options["group_by"]) && $options["group_by"] ? $options["group_by"] : "";
        if(!is_array($fields) || empty($fields)) {
            $fields = ["id", "Name", "Rights", "Orderforms", "Default", "Status"];
        }
        $select = [];
        if(!in_array("id", $fields)) {
            $select[] = "HostFact_Clientarea_Profiles.`id`";
        }
        foreach ($fields as $column) {
            $select[] = "HostFact_Clientarea_Profiles.`" . $column . "`";
        }
        Database_Model::getInstance()->get("HostFact_Clientarea_Profiles", $select);
        if($filter && array_key_exists($filter, $this->StatusArray)) {
            Database_Model::getInstance()->where("HostFact_Clientarea_Profiles.`Status`", $filter);
        } else {
            Database_Model::getInstance()->where("HostFact_Clientarea_Profiles.`Status`", ["IN" => ["active"]]);
        }
        if($sort_by) {
            Database_Model::getInstance()->orderBy("HostFact_Clientarea_Profiles.`" . $sort_by . "`", $sort_order);
        } else {
            Database_Model::getInstance()->orderBy("HostFact_Clientarea_Profiles.`Default` DESC, HostFact_Clientarea_Profiles.`Name`");
        }
        if(0 <= $offset && $results_per_page != "all") {
            Database_Model::getInstance()->limit($offset, $results_per_page);
        }
        $this->total_results = 0;
        if($list = Database_Model::getInstance()->execute()) {
            $this->total_results = Database_Model::getInstance()->rowCount("HostFact_Clientarea_Profiles", "HostFact_Clientarea_Profiles.id");
        }
        return $list;
    }
    public function delete($new_profile)
    {
        if(!is_numeric($this->id)) {
            $this->Error[] = __("invalid identifier for clientarea profile");
            return false;
        }
        $this->show();
        if($this->Default == "yes" || $new_profile == $this->id) {
            $this->Error[] = __("invalid identifier for clientarea profile");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Clientarea_Profiles", ["Status" => "removed"])->where("id", $this->id)->execute();
        $removed_profile_id = $this->id;
        if($result) {
            Database_Model::getInstance()->update("HostFact_Debtors", ["ClientareaProfile" => $new_profile])->where("ClientareaProfile", $removed_profile_id)->execute();
        }
        $this->Success[] = __("clientarea profile deleted");
        return true;
    }
    public function setDefaultValues()
    {
        $result = Database_Model::getInstance()->getOne("HostFact_Clientarea_Profiles")->where("Default", "yes")->where("Status", "active")->execute();
        if(!$result) {
            $this->Error[] = __("no default clientarea profile could be found");
            return false;
        }
        $this->TwoFactorAuthentication = $result->TwoFactorAuthentication;
        $this->Rights = json_decode($result->Rights, true);
        $this->Orderforms = json_decode($result->Orderforms, true);
        $this->WelcomeTitle = $result->WelcomeTitle;
        $this->WelcomeMessage = $result->WelcomeMessage;
        return true;
    }
    private function __validate()
    {
        if(trim($this->Name) == "") {
            $this->Error[] = __("invalid profile name");
        }
        if(!array_key_exists($this->Status, $this->StatusArray)) {
            $this->Error[] = __("invalid status");
        }
        return empty($this->Error) ? true : false;
    }
}

?>