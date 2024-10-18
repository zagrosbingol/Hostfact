<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class TerminationProcedure_Model
{
    public $id;
    public $Name;
    public $ServiceType;
    public $Default;
    public $TermPreference;
    public $Status;
    public $Success;
    public $Warning;
    public $Error;
    public $Variables = ["Name", "ServiceType", "Default", "TermPreference", "Status"];
    public function __construct()
    {
        $this->ServiceType = "other";
        $this->Default = "no";
        $this->TermPreference = "direct";
        $this->Status = "active";
        $this->Success = $this->Warning = $this->Error = [];
    }
    public function getTableConfig()
    {
        $options = [];
        $options["cols"] = [["key" => "name", "title" => __("termination procedure name"), "sortable" => "Name"], ["key" => "servicetype", "title" => __("service type"), "sortable" => "ServiceType"], ["key" => "term", "title" => __("termination term preference"), "sortable" => "TermPreference"]];
        $options["data"] = ["class/terminationprocedure.php", "TerminationProcedure_Model", "getTableData"];
        $options["sort_by"] = "Name";
        $options["sort_order"] = "ASC";
        $options["form_action"] = "termination-procedures.php";
        $options["actions"] = [["action" => "delete", "title" => __("batch action delete termination procedure"), "dialog" => ["title" => __("batch action delete termination procedure"), "content" => __("batch action delete termination procedure description")]]];
        return $options;
    }
    public function getTableData($offset, $results_per_page, $sort_by = "", $sort_order = "ASC", $parameters = [])
    {
        $options = !empty($parameters) ? $parameters : [];
        $options["offset"] = $offset;
        $options["results_per_page"] = $results_per_page;
        $options["sort_by"] = $sort_by;
        $options["sort_order"] = $sort_order;
        $procedure_list = $this->listProcedures($options);
        global $array_producttypes;
        $data = ["TotalResults" => $this->total_results];
        foreach ($procedure_list as $_procedure) {
            $suffix = $_procedure->Default == "yes" ? "<span class=\"fontsmall c4\"> - " . __("default") . "</span>" : "";
            $data[] = ["id" => $_procedure->id, "<a href=\"termination-procedures.php?page=edit&amp;id=" . $_procedure->id . "\" class=\"a1 c1\">" . htmlspecialchars($_procedure->Name) . "</a>" . $suffix, isset($array_producttypes[$_procedure->ServiceType]) ? $array_producttypes[$_procedure->ServiceType] : "-", __("termination term preference " . $_procedure->TermPreference)];
        }
        return $data;
    }
    public function checkDefaultProcedures()
    {
        $result = Database_Model::getInstance()->get("HostFact_TerminationProcedures", ["ServiceType"])->where("Default", "yes")->execute();
        global $array_producttypes;
        $temp_array = $array_producttypes;
        foreach ($result as $_type) {
            unset($temp_array[$_type->ServiceType]);
        }
        if(0 < count($temp_array)) {
            $this->Warning[] = sprintf(__("no default termination procedure for types"), implode(", ", $temp_array));
        }
    }
    public function listProcedures($options = [])
    {
        $fields = isset($options["fields"]) && is_array($options["fields"]) ? $options["fields"] : [];
        $sort_by = isset($options["sort_by"]) && $options["sort_by"] ? $options["sort_by"] : "Name";
        $sort_order = isset($options["sort_order"]) && $options["sort_order"] ? $options["sort_order"] : "ASC";
        $offset = isset($options["offset"]) && $options["offset"] ? $options["offset"] : "0";
        $results_per_page = isset($options["results_per_page"]) && $options["results_per_page"] ? $options["results_per_page"] : MAX_RESULTS_LIST;
        $filter = isset($options["filter"]) && $options["filter"] ? $options["filter"] : "";
        if(!is_array($fields) || empty($fields)) {
            $fields = ["id", "Name", "ServiceType", "Default", "TermPreference"];
        }
        $select = [];
        foreach ($fields as $column) {
            $select[] = "`" . $column . "`";
        }
        Database_Model::getInstance()->get("HostFact_TerminationProcedures", $select);
        if(isset($options["service_type"])) {
            Database_Model::getInstance()->where("ServiceType", $options["service_type"]);
        }
        if(isset($options["default"])) {
            Database_Model::getInstance()->where("Default", $options["default"]);
        }
        Database_Model::getInstance()->where("Status", "active");
        Database_Model::getInstance()->orderBy("`" . $sort_by . "`", $sort_order);
        if(0 <= $offset && $results_per_page != "all") {
            Database_Model::getInstance()->limit($offset, $results_per_page);
        }
        $this->total_results = 0;
        if($list = Database_Model::getInstance()->execute()) {
            $this->total_results = Database_Model::getInstance()->rowCount("HostFact_TerminationProcedures", "HostFact_TerminationProcedures.id");
        }
        return $list;
    }
    public function getDefaultProcedure($service_type)
    {
        $result = Database_Model::getInstance()->getOne("HostFact_TerminationProcedures", ["id"])->where("Default", "yes")->where("ServiceType", $service_type)->where("Status", "active")->execute();
        return isset($result->id) && 0 < $result->id ? $result->id : 0;
    }
    public function getProcedureAutomatedTasks()
    {
        $automated_tasks = ["domain:cancelend" => __("cancel domain at end of period") . " (" . __("termination action at registrar") . ")", "domain:cancelnow" => __("cancel domain direct") . " (" . __("termination action at registrar") . ")", "domain:unlock" => __("unlock domain") . " (" . __("termination action at registrar") . ")", "hosting:suspendhosting" => __("suspenddialog hosting title"), "hosting:deleteHosting" => __("deletedialog hosting title")];
        $automated_tasks_other_types = do_filter("termination_procedure_automated_tasks", []);
        $automated_tasks = array_merge($automated_tasks, $automated_tasks_other_types);
        return $automated_tasks;
    }
    public function show()
    {
        if(!is_numeric($this->id)) {
            $this->Error[] = __("invalid identifier for termination procedure");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_TerminationProcedures")->where("id", $this->id)->execute();
        if($result) {
            foreach ($result as $key => $value) {
                $this->{$key} = $value;
            }
            $result = Database_Model::getInstance()->get("HostFact_TerminationProceduresActions")->where("ProcedureID", $this->id)->orderBy("FIELD(`When`, 'direct', 'before', 'on', 'after')")->orderBy("Days", "ASC")->execute();
            $this->ProcedureActions = $result;
            return true;
        } else {
            return false;
        }
    }
    public function add()
    {
        if(!$this->__validateProcedure()) {
            return false;
        }
        if($this->Default == "yes") {
            $this->__removeDefaultProcedure($this->ServiceType);
        }
        $result = Database_Model::getInstance()->insert("HostFact_TerminationProcedures", ["Name" => $this->Name, "ServiceType" => $this->ServiceType, "Default" => $this->Default, "TermPreference" => $this->TermPreference, "Status" => $this->Status])->execute();
        if(!$result) {
            return false;
        }
        $this->id = Database_Model::getInstance()->lastInsertId();
        if(!$this->__updateProcedureActions()) {
            return false;
        }
        $this->Success[] = sprintf(__("termination procedure succesfully created"), htmlspecialchars($this->Name));
        return true;
    }
    public function edit()
    {
        if(!is_numeric($this->id)) {
            $this->Error[] = __("invalid identifier for termination procedure");
            return false;
        }
        if(!$this->__validateProcedure()) {
            return false;
        }
        if($this->Default == "yes") {
            $this->__removeDefaultProcedure($this->ServiceType);
        }
        $result = Database_Model::getInstance()->update("HostFact_TerminationProcedures", ["Name" => $this->Name, "ServiceType" => $this->ServiceType, "Default" => $this->Default, "TermPreference" => $this->TermPreference, "Status" => $this->Status])->where("id", $this->id)->execute();
        if(!$result) {
            return false;
        }
        if(!$this->__updateProcedureActions()) {
            return false;
        }
        $this->Success[] = sprintf(__("termination procedure succesfully edited"), htmlspecialchars($this->Name));
        return true;
    }
    public function delete()
    {
        if(!is_numeric($this->id)) {
            $this->Error[] = __("invalid identifier for termination procedure");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_TerminationProcedures", ["Default" => "no", "Status" => "removed"])->where("id", $this->id)->execute();
        if(!$result) {
            return false;
        }
        $this->Success[] = sprintf(__("termination procedure succesfully deleted"), htmlspecialchars($this->Name));
        return true;
    }
    public function getTableConfigActions()
    {
        $options = [];
        $options["cols"] = [["key" => "counter", "title" => "&nbsp;", "width" => 10], ["key" => "actiontype", "title" => __("termination action type"), "width" => 100], ["key" => "description", "title" => __("termination action action"), "width" => 320], ["key" => "when", "title" => __("termination action when")]];
        $options["data"] = ["class/terminationprocedure.php", "TerminationProcedure_Model", "getTableDataActions"];
        return $options;
    }
    public function getTableDataActions($offset, $results_per_page, $sort_by = "", $sort_order = "ASC", $parameters = [])
    {
        $options = !empty($parameters) ? $parameters : [];
        $action_list = [];
        if(isset($options["procedure_id"]) && $options["procedure_id"]) {
            $action_list = Database_Model::getInstance()->get("HostFact_TerminationProceduresActions")->where("ProcedureID", $options["procedure_id"])->orderBy("FIELD(`When`, 'direct', 'before', 'on', 'after')")->orderBy("Days", "ASC")->execute();
        }
        $array_automated_tasks = $this->getProcedureAutomatedTasks();
        require_once "class/template.php";
        $emailtemplate = new emailtemplate();
        $emailtemplates = $emailtemplate->all(["Name"]);
        $data = ["TotalResults" => count($action_list)];
        $counter = 0;
        foreach ($action_list as $_termination_action) {
            $counter++;
            if($_termination_action->ActionType == "automatic") {
                $td_description = $array_automated_tasks[$_termination_action->Description];
            } elseif(in_array($_termination_action->ActionType, ["mail2client", "mail2user"])) {
                $td_description = isset($emailtemplates[$_termination_action->Description]) ? $emailtemplates[$_termination_action->Description]["Name"] : "-";
            } else {
                $td_description = htmlspecialchars($_termination_action->Description);
            }
            if($_termination_action->When == "after") {
                $td_when = sprintf(__("termination action when after td"), $_termination_action->Days);
            } elseif($_termination_action->When == "before") {
                $td_when = sprintf(__("termination action when before td"), $_termination_action->Days);
            } elseif($_termination_action->When == "direct") {
                $td_when = __("termination action when direct td");
            } else {
                $td_when = __("termination action when on td");
            }
            $data[] = [$counter . ".", __("termination action type " . $_termination_action->ActionType), $td_description, $td_when];
        }
        return $data;
    }
    public function parseProcedureActionPost($post_data)
    {
        $return_array = [];
        foreach ($post_data["Type"] as $index => $action_type) {
            if((int) $index === 0) {
            } else {
                if($action_type == "automatic") {
                    $action_description = esc($post_data["AutomatedTask"][$index]);
                } elseif(in_array($action_type, ["mail2client", "mail2user"])) {
                    $action_description = esc($post_data["EmailTemplate"][$index]);
                } else {
                    $action_type = "manual";
                    $action_description = esc($post_data["Description"][$index]);
                    if(trim($action_description) == "") {
                    }
                }
                $action_when = esc($post_data["When"][$index]);
                switch ($action_when) {
                    case "before":
                    case "after":
                        $action_days = (int) $post_data["Days"][$index];
                        break;
                    default:
                        $action_days = 0;
                        $return_array[] = (object) ["ActionType" => $action_type, "Description" => $action_description, "When" => $action_when, "Days" => $action_days];
                }
            }
        }
        return $return_array;
    }
    private function __validateProcedure()
    {
        $this->Name = trim($this->Name);
        if(strlen($this->Name) === 0 || 100 < strlen($this->Name)) {
            $this->Error[] = __("termination procedure invalid name");
        }
        global $array_producttypes;
        if(!array_key_exists($this->ServiceType, $array_producttypes)) {
            $this->Error[] = __("termination procedure invalid servicetype");
        }
        if(!in_array($this->Default, ["yes", "no"])) {
            $this->Default = "no";
        }
        if(!in_array($this->TermPreference, ["direct", "date", "contract"])) {
            $this->TermPreference = "direct";
        }
        if(!in_array($this->Status, ["active", "removed"])) {
            $this->Status = "active";
        }
        return empty($this->Error) ? true : false;
    }
    private function __removeDefaultProcedure($service_type)
    {
        $result = Database_Model::getInstance()->update("HostFact_TerminationProcedures", ["Default" => "no"])->where("ServiceType", $service_type)->execute();
        if(!$result) {
            return false;
        }
        return true;
    }
    private function __updateProcedureActions()
    {
        $result = Database_Model::getInstance()->delete("HostFact_TerminationProceduresActions")->where("ProcedureID", $this->id)->execute();
        if(!$result) {
            return false;
        }
        $actions_added = [];
        foreach ($this->ProcedureActions as $tmp_procedure) {
            if(!$this->__validateProcedureAction($tmp_procedure)) {
                return false;
            }
            if(in_array($this->id . $tmp_procedure->ActionType . $tmp_procedure->Description . $tmp_procedure->When . $tmp_procedure->Days, $actions_added)) {
            } else {
                $result = Database_Model::getInstance()->insert("HostFact_TerminationProceduresActions", ["ProcedureID" => $this->id, "ActionType" => $tmp_procedure->ActionType, "Description" => $tmp_procedure->Description, "When" => $tmp_procedure->When, "Days" => $tmp_procedure->Days])->execute();
                if($result === false) {
                    return false;
                }
                $actions_added[] = $this->id . $tmp_procedure->ActionType . $tmp_procedure->Description . $tmp_procedure->When . $tmp_procedure->Days;
            }
        }
        return true;
    }
    private function __validateProcedureAction($tmp_procedure)
    {
        if(!in_array($tmp_procedure->ActionType, ["manual", "automatic", "mail2client", "mail2user"])) {
            $this->Error[] = __("termination procedure action invalid actiontype");
        }
        if(strlen($tmp_procedure->Description) === 0 || 200 < strlen($tmp_procedure->Description)) {
            $this->Error[] = __("termination procedure action invalid description");
        }
        if(!is_numeric($tmp_procedure->Days) || $tmp_procedure->Days != (int) $tmp_procedure->Days) {
            $this->Error[] = __("termination procedure action invalid days");
        }
        return empty($this->Error) ? true : false;
    }
}
class Action_Model
{
    public $id;
    public $Date;
    public $ReferenceType;
    public $ReferenceID;
    public $ProcedureID;
    public $ActionType;
    public $Description;
    public $When;
    public $Days;
    public $Status;
    public $Success;
    public $Warning;
    public $Error;
    public $Variables = ["Date", "ReferenceType", "ReferenceID", "ActionType", "Description", "When", "Days", "Status"];
    public function __construct($type = "termination")
    {
        $this->Date = date("Y-m-d");
        $this->ReferenceType = $type;
        $this->ReferenceID = 0;
        $this->ActionType = "manual";
        $this->When = "on";
        $this->Days = 0;
        $this->Status = "pending";
        $this->StatusArray = ["pending" => __("action status pending"), "executed" => __("action status executed"), "error" => __("action status error"), "canceled" => __("action status canceled"), "removed" => __("action status removed")];
        $this->Success = $this->Warning = $this->Error = [];
    }
    public function add()
    {
        if(!$this->__validateAction()) {
            return false;
        }
        $result = Database_Model::getInstance()->insert("HostFact_Actions", ["Date" => $this->Date, "ReferenceType" => $this->ReferenceType, "ReferenceID" => $this->ReferenceID, "ActionType" => $this->ActionType, "Description" => $this->Description, "When" => $this->When, "Days" => $this->Days, "Status" => $this->Status, "Created" => ["RAW" => "NOW()"], "Modified" => ["RAW" => "NOW()"]])->execute();
        if(!$result) {
            return false;
        }
        $this->id = $result;
        return true;
    }
    public function edit()
    {
        if(!is_numeric($this->id)) {
            $this->Error[] = __("invalid identifier for termination action");
            return false;
        }
        if(!$this->__validateAction()) {
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Actions", ["Date" => $this->Date, "ActionType" => $this->ActionType, "Description" => $this->Description, "When" => $this->When, "Days" => $this->Days, "Status" => $this->Status, "Modified" => ["RAW" => "NOW()"]])->where("id", $this->id)->execute();
        if(!$result) {
            return false;
        }
        return true;
    }
    public function cronTasks()
    {
        $list_actions = Database_Model::getInstance()->get("HostFact_Actions")->where("ReferenceType", "termination")->where("Status", "pending")->where("ActionType", ["IN" => ["automatic", "mail2client", "mail2user"]])->where("Date", ["<=" => ["RAW" => "CURDATE()"]])->execute();
        global $company;
        $has_errors = false;
        $this->_cronjob_notification_errors = [];
        foreach ($list_actions as $_action) {
            if(!$this->execute($_action->id, defined("SCRIPT_IS_CRONJOB"))) {
                $has_errors = true;
            }
        }
        if($has_errors === true && !empty($this->_cronjob_notification_errors) && CRONJOB_NOTIFY_TERMINATIONS == "yes" && CRONJOB_NOTIFY_MAILADDRESS && defined("SCRIPT_IS_CRONJOB")) {
            global $array_producttypes;
            $termination = new Termination_Model();
            $list_terminations_with_errors = $termination->listTerminations(["result_per_page" => "all", "termination_ids" => array_keys($this->_cronjob_notification_errors)]);
            $error_text = "";
            foreach ($list_terminations_with_errors as $_termination) {
                $error_text .= "<strong>" . (isset($array_producttypes[$_termination->ServiceType]) ? $array_producttypes[$_termination->ServiceType] . ": " : "") . $_termination->ServiceName . "</strong><br />";
                foreach ($this->_cronjob_notification_errors[$_termination->id] as $e) {
                    $error_text .= "- " . $e . "<br />";
                }
                $error_text .= "<br />";
            }
            require_once "class/email.php";
            $email = new email();
            $email->Recipient = CRONJOB_NOTIFY_MAILADDRESS;
            $email->Sender = $company->CompanyName . " <" . $company->EmailAddress . ">";
            $email->Subject = __("mail subject terminations error");
            $email->Message = file_get_contents("includes/language/" . LANGUAGE_CODE . "/mail.terminations.error.html");
            $email->Message = str_replace("[error_text]", $error_text, $email->Message);
            $email->Message = str_replace("[backoffice_url]", BACKOFFICE_URL . "services.php?page=termination_actions", $email->Message);
            $email->AutoSubmitted = true;
            $email_sent = $email->sent();
        }
    }
    public function execute($action_id, $is_cronjob = false)
    {
        global $account;
        if(!$account) {
            require_once "class/employee.php";
            $account = new employee();
        }
        $_action = Database_Model::getInstance()->getOne("HostFact_Actions")->where("id", $action_id)->where("ReferenceType", "termination")->execute();
        if(!$_action) {
            return false;
        }
        $termination = new Termination_Model();
        $termination->id = $_action->ReferenceID;
        $termination->show();
        if(!($result = $account->checkUserRights($termination->ServiceType, "edit"))) {
            if(!defined("SCRIPT_IS_INDEX_CRONJOB") || !SCRIPT_IS_INDEX_CRONJOB) {
                $this->Error[] = __("invalid user rights to perform action");
            }
            return false;
        }
        if($termination->Status != "pending") {
            return false;
        }
        if($_action->Status == "pending" && CRONJOB_IS_RUNNING != "" && abs(strtotime(CRONJOB_IS_RUNNING) - time()) / 60 < 5) {
            return true;
        }
        if($_action->ActionType == "automatic") {
            switch ($termination->ServiceType) {
                case "other":
                case "domain":
                    require_once "class/domain.php";
                    $domain = new domain();
                    $action_description = str_replace("domain:", "", $_action->Description);
                    $result = $domain->serviceExecuteAction($termination->ServiceID, $action_description);
                    $this->Success = array_merge($this->Success, $domain->Success);
                    $this->Warning = array_merge($this->Warning, $domain->Warning);
                    $this->Error = array_merge($this->Error, $domain->Error);
                    break;
                case "hosting":
                    require_once "class/hosting.php";
                    $hosting = new hosting();
                    $action_description = str_replace("hosting:", "", $_action->Description);
                    $result = $hosting->serviceExecuteAction($termination->ServiceID, $action_description);
                    $this->Success = array_merge($this->Success, $hosting->Success);
                    $this->Warning = array_merge($this->Warning, $hosting->Warning);
                    $this->Error = array_merge($this->Error, $hosting->Error);
                    break;
                default:
                    global $_module_instances;
                    if(isset($_module_instances[$termination->ServiceType]) && method_exists($_module_instances[$termination->ServiceType], "serviceExecuteAction")) {
                        $action_description = str_replace($termination->ServiceType . ":", "", $_action->Description);
                        $result = $_module_instances[$termination->ServiceType]->serviceExecuteAction($termination->ServiceID, $action_description);
                        $this->Success = array_merge($this->Success, $_module_instances[$termination->ServiceType]->Success);
                        $this->Warning = array_merge($this->Warning, $_module_instances[$termination->ServiceType]->Warning);
                        $this->Error = array_merge($this->Error, $_module_instances[$termination->ServiceType]->Error);
                    }
            }
        } elseif(in_array($_action->ActionType, ["mail2client", "mail2user"])) {
            $debtor_id = $termination->Debtor;
            require_once "class/debtor.php";
            $debtor = new debtor();
            $debtor->show($debtor_id);
            require_once "class/template.php";
            $emailtemplate = new emailtemplate();
            $emailtemplate->Identifier = $_action->Description;
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
            require_once "class/service.php";
            $service = new service();
            if(!$service->show($termination->ServiceID, $termination->ServiceType)) {
                $this->Error[] = __("service with this id is not found");
                $result = false;
            } else {
                $options = ["termination_ids" => [$termination->id], "limit" => 1];
                $list_terminations = $termination->listTerminations($options);
                $tmp = current($list_terminations);
                $termination_object = (object) ["TerminationDate" => rewrite_date_db2site($termination->Date), "ServiceName" => htmlspecialchars($tmp->ServiceName)];
                $objects = ["termination" => $termination_object, "periodicElement" => $service->Subscription, "periodic" => $service->Subscription];
                if($service->PeriodicType != "other" && isset($service->{$service->PeriodicType})) {
                    $objects[$service->PeriodicType] = $service->{$service->PeriodicType};
                }
                if($_action->ActionType == "mail2client") {
                    $email->Recipient = $debtor->EmailAddress;
                    $logEmailAddress = check_email_address($debtor->EmailAddress, "convert", ", ");
                } else {
                    global $company;
                    $email->Recipient = $company->EmailAddress;
                    $logEmailAddress = $company->EmailAddress;
                }
                $email->Debtor = $debtor_id;
                $email->add($objects);
                $email->Debtor = 0;
                if($email->sent(false, false, false, $objects)) {
                    if($is_cronjob === false) {
                        $this->Success[] = sprintf(__("termination action mail sent"), $logEmailAddress);
                    }
                    $result = true;
                } else {
                    $this->Error[] = sprintf(__("error while sending termination action mail"), isset($email->MailerError) ? $email->MailerError : "");
                    $result = false;
                }
            }
        } else {
            $result = true;
        }
        if($result !== true && $result == "manual") {
            $termination_procedure = new TerminationProcedure_Model();
            $automated_tasks = $termination_procedure->getProcedureAutomatedTasks();
            $result = Database_Model::getInstance()->update("HostFact_Actions", ["ActionType" => "manual", "Description" => $automated_tasks[$_action->Description], "Modified" => ["RAW" => "NOW()"]])->where("id", $action_id)->execute();
            if(!$result) {
                return false;
            }
            $this->Error[] = sprintf(__("automatic action is transformed to manual"), htmlspecialchars($automated_tasks[$_action->Description]));
            return true;
        }
        if($result !== true && is_array($result) && isset($result["ResultType"]) && $result["ResultType"] == "scheduled" && $result["Description"]) {
            $tmp = [];
            $tmp["Date"] = isset($result["Date"]) ? $result["Date"] : $termination->Date;
            $tmp["ActionType"] = isset($result["ActionType"]) && $result["ActionType"] == "automatic" ? "automatic" : "manual";
            $tmp["Description"] = $result["Description"];
            if($tmp["Date"] == $termination->Date) {
                $tmp["When"] = "on";
                $tmp["Days"] = 0;
            } elseif($termination->Date < $tmp["Date"]) {
                $tmp["When"] = "after";
                $action_date = new DateTime($tmp["Date"]);
                $tmp["Days"] = $action_date->diff(new DateTime($termination->Date))->format("%a");
            } elseif($tmp["Date"] < $termination->Date) {
                $tmp["When"] = "before";
                $action_date = new DateTime($tmp["Date"]);
                $tmp["Days"] = $action_date->diff(new DateTime($termination->Date))->format("%a");
            } elseif($tmp["Date"] == date("Y-m-d")) {
                $tmp["When"] = "direct";
                $tmp["Days"] = 0;
            }
            $result_action = Database_Model::getInstance()->insert("HostFact_Actions", ["Date" => $tmp["Date"], "ReferenceType" => "termination", "ReferenceID" => $termination->id, "ActionType" => $tmp["ActionType"], "Description" => $tmp["Description"], "When" => $tmp["When"], "Days" => $tmp["Days"], "Status" => "pending", "Created" => ["RAW" => "NOW()"], "Modified" => ["RAW" => "NOW()"]])->execute();
            if(!$result_action) {
                return false;
            }
            unset($tmp);
            $result = true;
        }
        delete_stats_summary();
        if($result === false) {
            Database_Model::getInstance()->update("HostFact_Actions", ["Status" => "error", "Modified" => ["RAW" => "NOW()"]])->where("id", $action_id)->execute();
            if($is_cronjob === true) {
                $this->_cronjob_notification_errors[$termination->id] = $this->Error;
                $this->Error = [];
            }
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Actions", ["Status" => "executed", "Modified" => ["RAW" => "NOW()"]])->where("id", $action_id)->execute();
        if(!$result) {
            return false;
        }
        if($termination->Date <= date("Y-m-d")) {
            $still_has_actions = false;
            $termination->show();
            foreach ($termination->TerminationActions as $_other_action) {
                if(in_array($_other_action->Status, ["pending", "error"])) {
                    $still_has_actions = true;
                }
            }
            if($still_has_actions === false) {
                $termination->completeTermination();
            }
        }
        if($_action->ActionType == "manual") {
            $this->Success[] = sprintf(__("manual action is marked as executed"), htmlspecialchars($_action->Description));
        }
        return true;
    }
    public function executeManually($action_id)
    {
        $_action = Database_Model::getInstance()->getOne("HostFact_Actions", ["HostFact_Actions.`ActionType`", "HostFact_Actions.`Description`", "HostFact_Actions.`ReferenceID`", "HostFact_Terminations.`ServiceID`", "HostFact_Terminations.`ServiceType`"])->join("HostFact_Terminations", "HostFact_Terminations.`id` = HostFact_Actions.`ReferenceID`")->where("HostFact_Actions.`id`", $action_id)->where("HostFact_Actions.`ReferenceType`", "termination")->execute();
        if(!$_action) {
            return false;
        }
        if($_action->ActionType == "automatic") {
            $termination_procedure = new TerminationProcedure_Model();
            $array_automated_tasks = $termination_procedure->getProcedureAutomatedTasks();
            $action_description = isset($array_automated_tasks[$_action->Description]) ? $array_automated_tasks[$_action->Description] : $_action->Description;
            $action_description = htmlspecialchars($action_description);
            createLog($_action->ServiceType, $_action->ServiceID, "action is executed manually", [$action_description]);
            if($_action->ServiceType == "domain" && $_action->Description == "domain:cancelnow") {
                Database_Model::getInstance()->update("HostFact_Domains", ["Status" => 8])->where("id", $_action->ServiceID)->execute();
            }
        } elseif(in_array($_action->ActionType, ["mail2client", "mail2user"])) {
            switch ($_action->ActionType) {
                case "mail2client":
                    $action_description = __("termination action type mail2client");
                    break;
                case "mail2user":
                    $action_description = __("termination action type mail2user");
                    break;
                default:
                    require_once "class/template.php";
                    $emailtemplate = new emailtemplate();
                    $emailtemplates = $emailtemplate->all(["Name"]);
                    $action_description .= isset($emailtemplates[$_action->Description]) ? " (" . $emailtemplates[$_action->Description]["Name"] . ")" : " (-)";
                    $action_description = htmlspecialchars($action_description);
                    createLog($_action->ServiceType, $_action->ServiceID, "action is executed manually", [$action_description]);
            }
        } else {
            $action_description = htmlspecialchars($_action->Description);
        }
        $result = Database_Model::getInstance()->update("HostFact_Actions", ["Status" => "executed", "Modified" => ["RAW" => "NOW()"]])->where("id", $action_id)->execute();
        if(!$result) {
            return false;
        }
        if($_action->ActionType != "manual") {
            $this->Success[] = sprintf(__("action is marked as manually executed"), htmlspecialchars($action_description));
        } else {
            $this->Success[] = sprintf(__("manual action is marked as executed"), htmlspecialchars($action_description));
        }
        return true;
    }
    public function cancel($reference_id)
    {
        $result = Database_Model::getInstance()->update("HostFact_Actions", ["Status" => "canceled", "Modified" => ["RAW" => "NOW()"]])->where("ReferenceType", $this->ReferenceType)->where("ReferenceID", $reference_id)->where("Status", "pending")->execute();
        if(!$result) {
            return false;
        }
        return true;
    }
    public function deleteByID($action_id)
    {
        global $account;
        if(!$account) {
            require_once "class/employee.php";
            $account = new employee();
        }
        $_action = Database_Model::getInstance()->getOne("HostFact_Actions")->where("id", $action_id)->where("ReferenceType", "termination")->execute();
        if(!$_action) {
            return false;
        }
        $termination = new Termination_Model();
        $termination->id = $_action->ReferenceID;
        $termination->show();
        if(!($result = $account->checkUserRights($termination->ServiceType, "edit"))) {
            $this->Error[] = __("invalid user rights to perform action");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Actions", ["Status" => "removed", "Modified" => ["RAW" => "NOW()"]])->where("id", $action_id)->execute();
        if(!$result) {
            return false;
        }
        return true;
    }
    public function getTableConfig()
    {
        $options = [];
        $options["cols"] = [["key" => "date", "title" => __("action execution date"), "width" => 120, "sortable" => "Date"], ["key" => "actiontype", "title" => __("termination action type"), "width" => 100, "sortable" => "ActionType"], ["key" => "description", "title" => __("termination action action")], ["key" => "service", "title" => __("termination service name"), "sortable" => "ServiceName"], ["key" => "servicetype", "title" => __("subscription type"), "width" => 80, "sortable" => "ServiceType"], ["key" => "debtor", "title" => __("debtor"), "sortable" => "Debtor"], ["key" => "status", "title" => __("status")]];
        $options["data"] = ["class/terminationprocedure.php", "Action_Model", "getTableData"];
        $options["form_action"] = "services.php?page=termination_actions" . (isset($_GET["sidebar"]) && $_GET["sidebar"] == "services" ? "&sidebar=services" : "");
        $options["actions"] = [["action" => "action_execute", "title" => __("action execute now"), "dialog" => ["title" => __("batch action execute now title"), "content" => __("batch action execute now description")]], ["action" => "action_execute_manually", "title" => __("action execute manually"), "dialog" => ["title" => __("batch action execute manually title"), "content" => __("batch action execute manually description")]], ["action" => "action_delete", "title" => __("action delete from queue"), "dialog" => ["title" => __("batch action delete from queue title"), "content" => __("batch action delete from queue description")]]];
        return $options;
    }
    public function getTableData($offset, $results_per_page, $sort_by = "", $sort_order = "ASC", $parameters = [])
    {
        $options = !empty($parameters) ? $parameters : [];
        $options["offset"] = $offset;
        $options["results_per_page"] = $results_per_page;
        $options["sort_by"] = $sort_by;
        $options["sort_order"] = $sort_order;
        $action_list = $this->listActions($options);
        global $array_producttypes;
        global $_module_instances;
        $termination_procedure = new TerminationProcedure_Model();
        $array_automated_tasks = $termination_procedure->getProcedureAutomatedTasks();
        require_once "class/template.php";
        $emailtemplate = new emailtemplate();
        $emailtemplates = $emailtemplate->all(["Name"]);
        $data = ["TotalResults" => $this->total_results];
        foreach ($action_list as $_action) {
            $service_url = "";
            switch ($_action->ServiceType) {
                case "domain":
                    $service_url = "domains.php?page=show&id=" . $_action->ServiceID;
                    break;
                case "hosting":
                    $service_url = "hosting.php?page=show&id=" . $_action->ServiceID;
                    break;
                default:
                    if(isset($_module_instances[$_action->ServiceType])) {
                        $service_url = "modules.php?module=" . $_action->ServiceType . "&page=show&id= " . $_action->ServiceID;
                    } else {
                        $service_url = "services.php?page=show&id=" . $_action->ServiceID;
                    }
                    $td_prefix = $td_suffix = $td_error_prefix = $td_error_suffix = "";
                    if(isset($options["termination_id"]) && $options["termination_id"] && $_action->Status == "executed") {
                        $td_prefix = "<span class=\"c_mgray\">";
                        $td_suffix = "</span>";
                    }
                    if($_action->Status == "error") {
                        $td_error_prefix = "<span class=\"c3\">";
                        $td_error_suffix = "</span>";
                    }
                    if($_action->ActionType == "automatic") {
                        $td_description = $array_automated_tasks[$_action->Description];
                    } elseif(in_array($_action->ActionType, ["mail2client", "mail2user"])) {
                        $td_description = isset($emailtemplates[$_action->Description]) ? $emailtemplates[$_action->Description]["Name"] : "-";
                    } else {
                        $td_description = htmlspecialchars($_action->Description);
                    }
                    $data[] = ["id" => $_action->id, $td_prefix . rewrite_date_db2site($_action->Date) . $td_suffix, $td_prefix . __("termination action type " . $_action->ActionType) . $td_suffix, $td_prefix . $td_error_prefix . $td_description . $td_error_suffix . $td_suffix, $td_prefix . "<a href=\"" . htmlspecialchars($service_url) . "\" class=\"a1 c1\">" . htmlspecialchars($_action->ServiceName) . "</a>" . $td_suffix, $td_prefix . (isset($array_producttypes[$_action->ServiceType]) ? $array_producttypes[$_action->ServiceType] : "-") . $td_suffix, $td_prefix . "<a href=\"debtors.php?page=show&amp;id=" . $_action->Debtor . "\" class=\"a1\">" . ($_action->CompanyName ? htmlspecialchars($_action->CompanyName) : htmlspecialchars($_action->SurName . ", " . $_action->Initials)) . "</a>" . $td_suffix, $td_prefix . $td_error_prefix . $this->StatusArray[$_action->Status] . $td_error_suffix . $td_suffix];
            }
        }
        return $data;
    }
    public function listActions($options = [])
    {
        global $account;
        if(!$account) {
            require_once "class/employee.php";
            $account = new employee();
        }
        $sort_by = isset($options["sort_by"]) && $options["sort_by"] ? $options["sort_by"] : "Date";
        $sort_order = isset($options["sort_order"]) && $options["sort_order"] ? $options["sort_order"] : "ASC";
        $offset = isset($options["offset"]) && $options["offset"] ? $options["offset"] : "0";
        $results_per_page = isset($options["results_per_page"]) && $options["results_per_page"] ? $options["results_per_page"] : MAX_RESULTS_LIST;
        $filter = isset($options["filter"]) && $options["filter"] ? $options["filter"] : "";
        if($sort_by == "Debtor") {
            $sort_by = "CONCAT(HostFact_Debtors.`CompanyName`, HostFact_Debtors.`SurName`)";
        }
        $_where = [];
        $termination_where = "";
        if(isset($options["termination_id"]) && $options["termination_id"]) {
            $_where[] = ["HostFact_Actions.ReferenceID", $options["termination_id"]];
        } else {
            $termination_where .= " AND HostFact_Terminations.`Status` IN ('pending')";
        }
        if(isset($options["filter"]) && ($options["filter"] == "pending" || $options["filter"] == "pending_only")) {
            if($options["filter"] == "pending_only") {
                $_where[] = ["HostFact_Actions.Status", "pending"];
            } else {
                $_where[] = ["HostFact_Actions.Status", ["IN" => ["pending", "error"]]];
            }
            if(isset($options["period"]) && $options["period"] == "today") {
                $_where[] = ["HostFact_Actions.Date", ["<=" => ["RAW" => "CURDATE()"]]];
            } elseif(isset($options["period"]) && $options["period"] == "future") {
                $_where[] = ["HostFact_Actions.Date", [">" => ["RAW" => "CURDATE()"]]];
            }
        } elseif(isset($options["filter"]) && isset($this->StatusArray[$options["filter"]])) {
            $_where[] = ["HostFact_Actions.Status", $options["filter"]];
        } else {
            $_where[] = ["HostFact_Actions.Status", ["NOT IN" => ["canceled", "removed"]]];
        }
        $has_sql = false;
        $termination = new Termination_Model();
        if($account->checkUserRights("domain")) {
            Database_Model::getInstance()->get("HostFact_Actions", ["HostFact_Actions.*", "HostFact_Terminations.ServiceType", "HostFact_Terminations.ServiceID", "HostFact_Terminations.Debtor", "HostFact_Debtors.DebtorCode", "HostFact_Debtors.CompanyName", "HostFact_Debtors.Initials", "HostFact_Debtors.SurName", "CONCAT(HostFact_Domains.`Domain`,'.',HostFact_Domains.`Tld`) as ServiceName"])->join("HostFact_Terminations", "HostFact_Actions.`ReferenceType`='termination' AND HostFact_Actions.`ReferenceID`=HostFact_Terminations.`id`" . $termination_where, "")->join("HostFact_Domains", "HostFact_Terminations.`ServiceType`='domain' AND HostFact_Terminations.`ServiceID`=HostFact_Domains.`id`", "")->join("HostFact_Debtors", "HostFact_Terminations.`Debtor`=HostFact_Debtors.`id`");
            if(0 < count($_where)) {
                foreach ($_where as $_where_part) {
                    Database_Model::getInstance()->where($_where_part[0], $_where_part[1]);
                }
            }
            if($sort_by) {
                Database_Model::getInstance()->orderBy($sort_by, $sort_order);
            }
            Database_Model::getInstance()->orderBy("Debtor", "ASC");
            if(0 <= $offset && $results_per_page != "all") {
                Database_Model::getInstance()->limit(0, $offset + $results_per_page);
            }
            $has_sql = true;
        }
        if($account->checkUserRights("hosting")) {
            Database_Model::getInstance()->{$has_sql ? "getUnion" : "get"}("HostFact_Actions", ["HostFact_Actions.*", "HostFact_Terminations.ServiceType", "HostFact_Terminations.ServiceID", "HostFact_Terminations.Debtor", "HostFact_Debtors.DebtorCode", "HostFact_Debtors.CompanyName", "HostFact_Debtors.Initials", "HostFact_Debtors.SurName", "CONCAT(HostFact_Hosting.`Username`) as ServiceName"])->join("HostFact_Terminations", "HostFact_Actions.`ReferenceType`='termination' AND HostFact_Actions.`ReferenceID`=HostFact_Terminations.`id`" . $termination_where, "")->join("HostFact_Hosting", "HostFact_Terminations.`ServiceType`='hosting' AND HostFact_Terminations.`ServiceID`=HostFact_Hosting.`id`", "")->join("HostFact_Debtors", "HostFact_Terminations.`Debtor`=HostFact_Debtors.`id`");
            if(!empty($_where)) {
                foreach ($_where as $_where_part) {
                    Database_Model::getInstance()->where($_where_part[0], $_where_part[1]);
                }
            }
            if($sort_by) {
                Database_Model::getInstance()->orderBy($sort_by, $sort_order);
            }
            Database_Model::getInstance()->orderBy("Debtor", "ASC");
            if(0 <= $offset && $results_per_page != "all") {
                Database_Model::getInstance()->limit(0, $offset + $results_per_page);
            }
            $has_sql = true;
        }
        global $additional_product_types;
        global $_module_instances;
        foreach ($additional_product_types as $product_type => $product_type_title) {
            if($account->checkUserRights($product_type) && isset($_module_instances[$product_type]) && method_exists($_module_instances[$product_type], "service_get_termination_parameters")) {
                $_params = $_module_instances[$product_type]->service_get_termination_parameters();
                Database_Model::getInstance()->{$has_sql ? "getUnion" : "get"}("HostFact_Actions", ["HostFact_Actions.*", "HostFact_Terminations.ServiceType", "HostFact_Terminations.ServiceID", "HostFact_Terminations.Debtor", "HostFact_Debtors.DebtorCode", "HostFact_Debtors.CompanyName", "HostFact_Debtors.Initials", "HostFact_Debtors.SurName", $_params["service_name"] . " as ServiceName"])->join("HostFact_Terminations", "HostFact_Actions.`ReferenceType`='termination' AND HostFact_Actions.`ReferenceID`=HostFact_Terminations.`id`" . $termination_where, "")->join($_params["table_name"] . " as service", "HostFact_Terminations.`ServiceType`='" . $product_type . "' AND HostFact_Terminations.`ServiceID`=service.`id`", "")->join("HostFact_Debtors", "HostFact_Terminations.`Debtor`=HostFact_Debtors.`id`");
                if(strpos($_params["service_name"], "HostFact_PeriodicElements.") !== false) {
                    Database_Model::getInstance()->join("HostFact_PeriodicElements", " HostFact_Terminations.`ServiceID`=HostFact_PeriodicElements.`Reference` AND HostFact_PeriodicElements.`PeriodicType`=HostFact_Terminations.`ServiceType` AND HostFact_PeriodicElements.`Status`!=9");
                }
                if(!empty($_where)) {
                    foreach ($_where as $_where_part) {
                        Database_Model::getInstance()->where($_where_part[0], $_where_part[1]);
                    }
                }
                if($sort_by) {
                    Database_Model::getInstance()->orderBy($sort_by, $sort_order);
                }
                Database_Model::getInstance()->orderBy("Debtor", "ASC");
                if(0 <= $offset && $results_per_page != "all") {
                    Database_Model::getInstance()->limit(0, $offset + $results_per_page);
                }
                $has_sql = true;
            }
        }
        if($account->checkUserRights("other")) {
            Database_Model::getInstance()->{$has_sql ? "getUnion" : "get"}("HostFact_Actions", ["HostFact_Actions.*", "HostFact_Terminations.ServiceType", "HostFact_Terminations.ServiceID", "HostFact_Terminations.Debtor", "HostFact_Debtors.DebtorCode", "HostFact_Debtors.CompanyName", "HostFact_Debtors.Initials", "HostFact_Debtors.SurName", "HostFact_PeriodicElements.Description as ServiceName"])->join("HostFact_Terminations", "HostFact_Actions.`ReferenceType`='termination' AND HostFact_Actions.`ReferenceID`=HostFact_Terminations.`id`", "")->join("HostFact_PeriodicElements", "HostFact_Terminations.`ServiceType`='other' AND HostFact_Terminations.`ServiceID`=HostFact_PeriodicElements.`id` AND HostFact_PeriodicElements.`PeriodicType`='other'", "")->join("HostFact_Debtors", "HostFact_Terminations.`Debtor`=HostFact_Debtors.`id`");
            if(!empty($_where)) {
                foreach ($_where as $_where_part) {
                    Database_Model::getInstance()->where($_where_part[0], $_where_part[1]);
                }
            }
            if($sort_by) {
                Database_Model::getInstance()->orderBy($sort_by, $sort_order);
            }
            Database_Model::getInstance()->orderBy("Debtor", "ASC");
            if(0 <= $offset && $results_per_page != "all") {
                Database_Model::getInstance()->limit(0, $offset + $results_per_page);
            }
            $has_sql = true;
        }
        if(!$has_sql) {
            $this->total_results = 0;
            return [];
        }
        Database_Model::getInstance()->closeUnion();
        if($sort_by == "CONCAT(HostFact_Debtors.`CompanyName`, HostFact_Debtors.`SurName`)") {
            Database_Model::getInstance()->orderBy("CONCAT(`CompanyName`, `SurName`)", $sort_order);
        } elseif($sort_by) {
            Database_Model::getInstance()->orderBy($sort_by, $sort_order);
        }
        Database_Model::getInstance()->orderBy("Debtor", "ASC");
        if(0 <= $offset && $results_per_page != "all") {
            Database_Model::getInstance()->limit($offset, $results_per_page);
        }
        $list = Database_Model::getInstance()->execute();
        Database_Model::getInstance()->get("HostFact_Actions", "HostFact_Actions.id")->join("HostFact_Terminations", "HostFact_Actions.`ReferenceType`='termination' AND HostFact_Actions.`ReferenceID`=HostFact_Terminations.`id`" . $termination_where, "");
        if(!empty($_where)) {
            foreach ($_where as $_where_part) {
                Database_Model::getInstance()->where($_where_part[0], $_where_part[1]);
            }
        }
        $total_results = Database_Model::getInstance()->execute();
        $this->total_results = count($total_results);
        return $list;
    }
    private function __validateAction()
    {
        if(!trim($this->Description) || 200 < strlen($this->Description)) {
            $this->Error[] = __("invalid description for action");
        }
        if(!is_date($this->Date)) {
            $this->Error[] = __("action has invalid date");
        }
        return empty($this->Error) ? true : false;
    }
}
class Termination_Model
{
    public $id;
    public $Debtor;
    public $ServiceType;
    public $ServiceID;
    public $Date;
    public $ProcedureID;
    public $Term;
    public $Reason;
    public $Status;
    public $Success;
    public $Warning;
    public $Error;
    public $TerminationActions;
    public $Variables = ["Debtor", "ServiceType", "ServiceID", "Date", "ProcedureID", "Term", "Reason", "Status", "Who", "IP"];
    public function __construct()
    {
        $this->Debtor = 0;
        $this->ServiceType = "other";
        $this->ServiceID = 0;
        $this->Date = date("Y-m-d");
        $this->ProcedureID = 0;
        $this->Term = "direct";
        $this->Reason = "";
        $this->Status = "pending";
        global $account;
        $this->Who = isset($account->Identifier) ? $account->Identifier : "";
        if(defined("API_CALL_FROM_CLIENTAREA") && API_CALL_FROM_CLIENTAREA === true) {
            $this->Who = "clientarea";
        } elseif(defined("API_DIR") && API_DIR) {
            $this->Who = "api";
        } elseif(defined("SCRIPT_IS_CRONJOB") && SCRIPT_IS_CRONJOB) {
            $this->Who = "cronjob";
        }
        $this->IP = "";
        $this->TerminationActions = [];
        $this->StatusArray = ["approval" => __("termination status approval"), "rejected" => __("termination status rejected"), "pending" => __("termination status pending"), "processed" => __("termination status processed"), "canceled" => __("termination status canceled")];
        $this->Success = $this->Warning = $this->Error = [];
    }
    public function show($service_type = "other", $service_id = 0)
    {
        if(0 < $service_id) {
            $result = Database_Model::getInstance()->getOne("HostFact_Terminations")->where("ServiceType", $service_type)->where("ServiceID", $service_id)->where("Status", ["NOT IN" => ["canceled", "rejected"]])->orderBy("id", "DESC")->execute();
        } else {
            $result = Database_Model::getInstance()->getOne("HostFact_Terminations")->where("id", $this->id)->execute();
        }
        if($result) {
            foreach ($result as $key => $value) {
                $this->{$key} = $value;
            }
            $this->TerminationActions = Database_Model::getInstance()->get("HostFact_Actions")->where("ReferenceType", "termination")->where("ReferenceID", $this->id)->where("Status", ["IN" => ["pending", "executed", "error"]])->orderBy("Date", "ASC")->execute();
            return true;
        } else {
            return false;
        }
    }
    public function terminateService($service_type, $service_id, $termination_date = false, $service_has_expiration_date = false, $from_customerpanel = false)
    {
        if($this->show($service_type, $service_id)) {
            $this->Error[] = __("a termination already exists");
            return false;
        }
        require_once "class/service.php";
        $service = new service();
        if(!$service->show($service_id, $service_type)) {
            $this->Error[] = __("service with this id is not found");
            return false;
        }
        if($service_type != $service->PeriodicType) {
            $this->Error[] = __("service with this id is from another servicetype");
            return false;
        }
        $termination_procedure = new TerminationProcedure_Model();
        $default_template = $termination_procedure->getDefaultProcedure($service_type);
        $this->ServiceType = $service_type;
        $this->ServiceID = $service_id;
        $this->Date = $termination_date ? $termination_date : $this->getTerminationEndDate($service, $service_has_expiration_date);
        $this->ProcedureID = $default_template;
        $this->Term = $termination_date ? "date" : "contract";
        $this->Reason = $this->Reason ? $this->Reason : "";
        $this->Status = $from_customerpanel ? "approval" : "pending";
        $result = $this->add();
        return $result;
    }
    public function add()
    {
        require_once "class/service.php";
        $service = new service();
        if(!$service->show($this->ServiceID, $this->ServiceType)) {
            return false;
        }
        $this->Debtor = $service->Debtor;
        if(!$this->__validateTermination()) {
            return false;
        }
        $has_errors = false;
        Database_Model::getInstance()->beginTransaction();
        $result = Database_Model::getInstance()->insert("HostFact_Terminations", ["Debtor" => $this->Debtor, "ServiceType" => $this->ServiceType, "ServiceID" => $this->ServiceID, "Date" => $this->Date, "ProcedureID" => $this->ProcedureID, "Term" => $this->Term, "Reason" => $this->Reason, "Status" => $this->Status, "Who" => $this->Who, "IP" => $this->IP, "Created" => ["RAW" => "NOW()"]])->execute();
        if(!$result) {
            $has_errors = true;
        }
        if($has_errors === false) {
            $this->id = $result;
            $termination_action = new Action_Model("termination");
            $termination_action->ReferenceID = $this->id;
            if(!$termination_action->cancel($termination_action->ReferenceID)) {
                $this->Error = array_merge($this->Error, $termination_action->Error);
                $has_errors = true;
            }
            if(0 < $this->ProcedureID) {
                $termination_procedure = new TerminationProcedure_Model();
                $termination_procedure->id = $this->ProcedureID;
                $termination_procedure->show();
                $this->ProcedureActions = $termination_procedure->ProcedureActions;
            }
            if($has_errors === false && isset($this->ProcedureActions) && is_array($this->ProcedureActions)) {
                foreach ($this->ProcedureActions as $tmp_action) {
                    switch ($tmp_action->When) {
                        case "direct":
                            $termination_action->Date = date("Y-m-d");
                            break;
                        case "before":
                            $termination_action->Date = date("Y-m-d", strtotime("-" . $tmp_action->Days . " days", strtotime($this->Date)));
                            break;
                        case "on":
                            $termination_action->Date = $this->Date;
                            break;
                        case "after":
                            $termination_action->Date = date("Y-m-d", strtotime("+" . $tmp_action->Days . " days", strtotime($this->Date)));
                            break;
                        default:
                            $termination_action->ActionType = $tmp_action->ActionType;
                            $termination_action->Description = $tmp_action->Description;
                            $termination_action->When = $tmp_action->When;
                            $termination_action->Days = $tmp_action->Days;
                            if(!$termination_action->add()) {
                                $this->Error = array_merge($this->Error, $termination_action->Error);
                                $has_errors = true;
                            }
                    }
                }
            }
            if($has_errors === false && isset($service->Subscription->Identifier) && 0 < $service->Subscription->Identifier && $this->Status != "approval") {
                if(!$service->Subscription->terminate($service->Subscription->Identifier, $this->Date)) {
                    $this->Error = array_merge($this->Error, $service->Subscription->Error);
                    $has_errors = true;
                }
            } elseif($has_errors === false && (!isset($service->Subscription->Identifier) || !$service->Subscription->Identifier) && $this->Status != "approval") {
                $service_info = ["Type" => $this->ServiceType, "id" => $this->ServiceID, "Debtor" => $this->Debtor, "TerminationDate" => date("Y-m-d", strtotime($this->Date))];
                do_action("service_is_terminated", $service_info);
            }
        }
        if($has_errors === true) {
            Database_Model::getInstance()->rollBack();
            return false;
        }
        $this->Success[] = __("service is terminated");
        if($this->ServiceType != "other") {
            switch ($this->Term) {
                case "direct":
                    $log_description = "service is terminated directly - ";
                    $log_values = [];
                    break;
                case "date":
                    $log_description = "service is terminated on date - ";
                    $log_values = [rewrite_date_db2site($this->Date)];
                    break;
                case "contract":
                    $log_description = "service is terminated on end contract - ";
                    $log_values = [rewrite_date_db2site($this->Date)];
                    break;
                default:
                    if(0 < $this->ProcedureID) {
                        $log_description .= "procedure";
                        $log_values[] = $termination_procedure->Name;
                    } else {
                        $log_description .= "nonprocedure";
                    }
                    createLog($this->ServiceType, $this->ServiceID, $log_description, $log_values);
            }
        }
        Database_Model::getInstance()->commit();
        return true;
    }
    public function completeTermination()
    {
        if(!$this->ServiceID) {
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Terminations", ["Status" => "processed"])->where("id", $this->id)->execute();
        if(!$result) {
            return false;
        }
        return true;
    }
    public function undoTermination($new_start_period = false, $skip_subscription = false)
    {
        if(!$this->ServiceID) {
            return false;
        }
        Database_Model::getInstance()->beginTransaction();
        $has_errors = false;
        $result = Database_Model::getInstance()->update("HostFact_Terminations", ["Status" => "canceled"])->where("id", $this->id)->execute();
        if(!$result) {
            $has_errors = true;
        }
        $termination_action = new Action_Model("termination");
        if(!$termination_action->cancel($this->id)) {
            $has_errors = true;
        }
        require_once "class/service.php";
        $service = new service();
        if(!$service->show($this->ServiceID, $this->ServiceType)) {
            $has_errors = true;
        }
        if(isset($service->Subscription->Identifier) && 0 < $service->Subscription->Identifier) {
            if($new_start_period !== false && !is_date($new_start_period)) {
                $this->Error[] = __("invalid date for startperiod subscription");
                $has_errors = true;
            } elseif($skip_subscription === false && !$service->Subscription->reactivate($service->Subscription->Identifier, $new_start_period)) {
                $has_errors = true;
            }
            Database_Model::getInstance()->delete("HostFact_Agenda")->where("ItemType", "periodic")->where("ItemID", $service->Subscription->Identifier)->execute();
        } else {
            $service_info = ["Type" => $this->ServiceType, "id" => $this->ServiceID, "Debtor" => $service->Debtor];
            do_action("service_is_reactivated", $service_info);
        }
        if($has_errors === true) {
            Database_Model::getInstance()->rollBack();
            return false;
        }
        if($this->ServiceType != "other") {
            createLog($this->ServiceType, $this->ServiceID, "service is reactived");
        }
        Database_Model::getInstance()->commit();
        $this->Success[] = __("service is reactived");
        return true;
    }
    public function approveTermination($service_type = false, $service_id = false)
    {
        if($service_type === false && $service_type === false && 0 < $this->id) {
            $result = Database_Model::getInstance()->getOne("HostFact_Terminations")->where("id", $this->id)->execute();
        } else {
            $result = Database_Model::getInstance()->getOne("HostFact_Terminations")->where("ServiceType", $service_type)->where("ServiceID", $service_id)->where("Status", "approval")->execute();
        }
        if(!$result) {
            return false;
        }
        if($service_type === false && $service_type === false && 0 < $this->id) {
            $service_type = $result->ServiceType;
            $service_id = $result->ServiceID;
        }
        require_once "class/service.php";
        $service = new service();
        if(!$service->show($service_id, $service_type)) {
            $this->Error[] = __("service with this id is not found");
            return false;
        }
        Database_Model::getInstance()->beginTransaction();
        $has_errors = false;
        if(isset($service->Subscription->Identifier) && 0 < $service->Subscription->Identifier && !$service->Subscription->terminate($service->Subscription->Identifier, $result->Date)) {
            $this->Error = array_merge($this->Error, $service->Subscription->Error);
            $has_errors = true;
        }
        $result_upd = Database_Model::getInstance()->update("HostFact_Terminations", ["Status" => "pending"])->where("id", $result->id)->execute();
        if(!$result_upd) {
            $has_errors = true;
        }
        if($has_errors === true) {
            Database_Model::getInstance()->rollBack();
            return false;
        }
        $this->Success[] = __("termination is approved");
        if($service_type != "other") {
            createLog($service_type, $service_id, "termination is approved");
        }
        Database_Model::getInstance()->commit();
        delete_stats_summary();
        return true;
    }
    public function rejectTermination($service_type = false, $service_id = false)
    {
        if($service_type === false && $service_type === false && 0 < $this->id) {
            $result = Database_Model::getInstance()->getOne("HostFact_Terminations")->where("id", $this->id)->where("Status", "approval")->execute();
        } else {
            $result = Database_Model::getInstance()->getOne("HostFact_Terminations")->where("ServiceType", $service_type)->where("ServiceID", $service_id)->where("Status", "approval")->execute();
        }
        if(!$result) {
            return false;
        }
        Database_Model::getInstance()->beginTransaction();
        $has_errors = false;
        $result_upd = Database_Model::getInstance()->update("HostFact_Terminations", ["Status" => "rejected"])->where("id", $result->id)->execute();
        if(!$result_upd) {
            $has_errors = true;
        }
        $result_upd = Database_Model::getInstance()->update("HostFact_Actions", ["Status" => "canceled"])->where("ReferenceType", "termination")->where("ReferenceID", $result->id)->execute();
        if(!$result_upd) {
            $has_errors = true;
        }
        if($has_errors === true) {
            Database_Model::getInstance()->rollBack();
            return false;
        }
        Database_Model::getInstance()->commit();
        $this->Success[] = __("termination is rejected");
        if($service_type != "other") {
            createLog($service_type, $service_id, "termination is rejected");
        }
        delete_stats_summary();
        return true;
    }
    public function getTableConfig()
    {
        $options = [];
        $options["cols"] = [["key" => "date", "title" => __("termination date"), "width" => 120, "sortable" => "Date"], ["key" => "service", "title" => __("termination service name"), "sortable" => "ServiceName"], ["key" => "servicetype", "title" => __("subscription type"), "width" => 80, "sortable" => "ServiceType"], ["key" => "debtor", "title" => __("debtor"), "sortable" => "Debtor"], ["key" => "subscription", "title" => __("subscription"), "width" => 90, "sortable" => "StartPeriod"], ["key" => "created", "title" => __("termination done at"), "width" => 220, "sortable" => "Created"], ["key" => "status", "title" => __("status"), "width" => 160, "sortable" => "Status"]];
        $options["data"] = ["class/terminationprocedure.php", "Termination_Model", "getTableData"];
        $options["form_action"] = "services.php?page=terminations" . (isset($_GET["sidebar"]) && $_GET["sidebar"] == "services" ? "&sidebar=services" : "");
        if(isset($_SESSION["backoffice_tables_config"]["list_terminations"]["filter"]) && $_SESSION["backoffice_tables_config"]["list_terminations"]["filter"] == "approval") {
            $options["actions"] = [["action" => "approve_termination", "title" => __("termination approve dialog title"), "dialog" => ["title" => __("termination approve dialog title"), "content" => __("termination approve dialog description")]], ["action" => "reject_termination", "title" => __("termination reject dialog title"), "dialog" => ["title" => __("termination reject dialog title"), "content" => __("termination reject dialog description")]]];
        } else {
            $options["actions"] = [];
        }
        $options["sort_by"] = "Date";
        $options["sort_order"] = "ASC";
        return $options;
    }
    public function getTableData($offset, $results_per_page, $sort_by = "", $sort_order = "ASC", $parameters = [])
    {
        $options = !empty($parameters) ? $parameters : [];
        $options["offset"] = $offset;
        $options["results_per_page"] = $results_per_page;
        $options["sort_by"] = $sort_by;
        $options["sort_order"] = $sort_order;
        $termination_list = $this->listTerminations($options);
        global $array_producttypes;
        global $_module_instances;
        $data = ["TotalResults" => $this->total_results];
        foreach ($termination_list as $_termination) {
            $service_url = "";
            switch ($_termination->ServiceType) {
                case "domain":
                    $service_url = "domains.php?page=show&id=" . $_termination->ServiceID;
                    break;
                case "hosting":
                    $service_url = "hosting.php?page=show&id=" . $_termination->ServiceID;
                    break;
                default:
                    if(isset($_module_instances[$_termination->ServiceType])) {
                        $service_url = "modules.php?module=" . $_termination->ServiceType . "&page=show&id= " . $_termination->ServiceID;
                    } else {
                        $service_url = "services.php?page=show&id=" . $_termination->ServiceID;
                    }
                    $reason_span = $_termination->Reason ? " <span class=\"ico inline comment infopopuptop\" style=\"float:none\">&nbsp;<span class=\"popup\" style=\"left: -5px; top: -38px; display: none;\"><strong>" . __("termination reason") . "</strong><br />" . htmlspecialchars(mb_substr($_termination->Reason, 0, 250)) . "<b style=\"top: 30px;\"></b></span></span>" : "";
                    $status_suffix = $_termination->Status == "pending" && 0 < $_termination->RemainingActions ? " <span class=\"fontsmall c4\">- " . sprintf(__("still x actions remaining"), $_termination->RemainingActions) . "</span>" : "";
                    $created_td = $_termination->Created == "0000-00-00 00:00:00" ? "-" : rewrite_date_db2site($_termination->Created) . " " . __("at") . " " . rewrite_date_db2site($_termination->Created, "%H:%i") . " <span class=\"fontsmall c4\">- " . __("termination term preference " . $_termination->Term) . "</span>";
                    $data[] = ["id" => $_termination->id, rewrite_date_db2site($_termination->Date) . $reason_span, "<a href=\"" . htmlspecialchars($service_url) . "\" class=\"a1 c1\">" . htmlspecialchars($_termination->ServiceName) . "</a>", isset($array_producttypes[$_termination->ServiceType]) ? $array_producttypes[$_termination->ServiceType] : "-", "<a href=\"debtors.php?page=show&amp;id=" . $_termination->Debtor . "\" class=\"a1\">" . ($_termination->CompanyName ? htmlspecialchars($_termination->CompanyName) : htmlspecialchars($_termination->SurName . ", " . $_termination->Initials)) . "</a>", $_termination->StartPeriod ? __("till") . " " . rewrite_date_db2site($_termination->StartPeriod) : "-", $created_td, $this->StatusArray[$_termination->Status] . $status_suffix];
            }
        }
        return $data;
    }
    public function listTerminations($options = [])
    {
        global $account;
        if(!$account) {
            require_once "class/employee.php";
            $account = new employee();
        }
        $sort_by = isset($options["sort_by"]) && $options["sort_by"] ? $options["sort_by"] : "Date";
        $sort_order = isset($options["sort_order"]) && $options["sort_order"] ? $options["sort_order"] : "ASC";
        $offset = isset($options["offset"]) && $options["offset"] ? $options["offset"] : "0";
        $results_per_page = isset($options["results_per_page"]) && $options["results_per_page"] ? $options["results_per_page"] : MAX_RESULTS_LIST;
        $filter = isset($options["filter"]) && $options["filter"] ? $options["filter"] : "";
        if($sort_by == "Debtor") {
            $sort_by = "CONCAT(HostFact_Debtors.`CompanyName`, HostFact_Debtors.`SurName`)";
        }
        $_where = [];
        if(isset($options["debtor_id"])) {
            $_where[] = ["HostFact_Terminations.Debtor", $options["debtor_id"]];
        }
        if(isset($options["termination_ids"]) && 0 < count($options["termination_ids"])) {
            $_where[] = ["HostFact_Terminations.id", ["IN" => $options["termination_ids"]]];
        }
        if(isset($options["filter"]) && isset($this->StatusArray[$options["filter"]])) {
            $_where[] = ["HostFact_Terminations.Status", $options["filter"]];
        } else {
            $_where[] = ["HostFact_Terminations.Status", ["!=" => "canceled"]];
        }
        $has_sql = false;
        if($account->checkUserRights("domain")) {
            Database_Model::getInstance()->get("HostFact_Terminations", ["HostFact_Terminations.*", "HostFact_Debtors.DebtorCode", "HostFact_Debtors.CompanyName", "HostFact_Debtors.Initials", "HostFact_Debtors.SurName", "HostFact_PeriodicElements.StartPeriod", "CONCAT(HostFact_Domains.`Domain`,'.',HostFact_Domains.`Tld`) as ServiceName", "COUNT(HostFact_Actions.`id`) as `RemainingActions`"])->join("HostFact_Domains", "HostFact_Terminations.`ServiceType`='domain' AND HostFact_Terminations.`ServiceID`=HostFact_Domains.`id`", "")->join("HostFact_Debtors", "HostFact_Terminations.`Debtor`=HostFact_Debtors.`id`")->join("HostFact_PeriodicElements", "HostFact_Terminations.`ServiceID`=HostFact_PeriodicElements.`Reference` AND HostFact_PeriodicElements.`PeriodicType`=HostFact_Terminations.`ServiceType` AND HostFact_PeriodicElements.`Status`!=9")->join("HostFact_Actions", "HostFact_Actions.`ReferenceType`='termination' AND HostFact_Actions.`ReferenceID`=HostFact_Terminations.`id` AND HostFact_Actions.`Status` IN ('pending','error')");
            if(!empty($_where)) {
                foreach ($_where as $_where_part) {
                    Database_Model::getInstance()->where($_where_part[0], $_where_part[1]);
                }
            }
            Database_Model::getInstance()->groupBy("HostFact_Terminations.id");
            if($sort_by) {
                Database_Model::getInstance()->orderBy($sort_by, $sort_order);
            }
            if(0 <= $offset && $results_per_page != "all") {
                Database_Model::getInstance()->limit(0, $offset + $results_per_page);
            }
            $has_sql = true;
        }
        if($account->checkUserRights("hosting")) {
            Database_Model::getInstance()->{$has_sql ? "getUnion" : "get"}("HostFact_Terminations", ["HostFact_Terminations.*", "HostFact_Debtors.DebtorCode", "HostFact_Debtors.CompanyName", "HostFact_Debtors.Initials", "HostFact_Debtors.SurName", "HostFact_PeriodicElements.StartPeriod", "CONCAT(HostFact_Hosting.`Username`) as ServiceName", "COUNT(HostFact_Actions.`id`) as `RemainingActions`"])->join("HostFact_Hosting", "HostFact_Terminations.`ServiceType`='hosting' AND HostFact_Terminations.`ServiceID`=HostFact_Hosting.`id`", "")->join("HostFact_Debtors", "HostFact_Terminations.`Debtor`=HostFact_Debtors.`id`")->join("HostFact_PeriodicElements", "HostFact_Terminations.`ServiceID`=HostFact_PeriodicElements.`Reference` AND HostFact_PeriodicElements.`PeriodicType`=HostFact_Terminations.`ServiceType` AND HostFact_PeriodicElements.`Status`!=9")->join("HostFact_Actions", "HostFact_Actions.`ReferenceType`='termination' AND HostFact_Actions.`ReferenceID`=HostFact_Terminations.`id` AND HostFact_Actions.`Status` IN ('pending','error')");
            if(!empty($_where)) {
                foreach ($_where as $_where_part) {
                    Database_Model::getInstance()->where($_where_part[0], $_where_part[1]);
                }
            }
            Database_Model::getInstance()->groupBy("HostFact_Terminations.id");
            if($sort_by) {
                Database_Model::getInstance()->orderBy($sort_by, $sort_order);
            }
            if(0 <= $offset && $results_per_page != "all") {
                Database_Model::getInstance()->limit(0, $offset + $results_per_page);
            }
            $has_sql = true;
        }
        global $additional_product_types;
        global $_module_instances;
        foreach ($additional_product_types as $product_type => $product_type_title) {
            if($account->checkUserRights($product_type) && isset($_module_instances[$product_type]) && method_exists($_module_instances[$product_type], "service_get_termination_parameters")) {
                $_params = $_module_instances[$product_type]->service_get_termination_parameters();
                Database_Model::getInstance()->{$has_sql ? "getUnion" : "get"}("HostFact_Terminations", ["HostFact_Terminations.*", "HostFact_Debtors.DebtorCode", "HostFact_Debtors.CompanyName", "HostFact_Debtors.Initials", "HostFact_Debtors.SurName", "HostFact_PeriodicElements.StartPeriod", $_params["service_name"] . " as `ServiceName`", "COUNT(HostFact_Actions.`id`) as `RemainingActions`"])->join($_params["table_name"] . " as service", "HostFact_Terminations.`ServiceType`='" . $product_type . "' AND HostFact_Terminations.`ServiceID`=service.`id`", "")->join("HostFact_Debtors", "HostFact_Terminations.`Debtor`=HostFact_Debtors.`id`")->join("HostFact_PeriodicElements", "HostFact_Terminations.`ServiceID`=HostFact_PeriodicElements.`Reference` AND HostFact_PeriodicElements.`PeriodicType`=HostFact_Terminations.`ServiceType` AND HostFact_PeriodicElements.`Status`!=9")->join("HostFact_Actions", "HostFact_Actions.`ReferenceType`='termination' AND HostFact_Actions.`ReferenceID`=HostFact_Terminations.`id` AND HostFact_Actions.`Status` IN ('pending','error')");
                if(!empty($_where)) {
                    foreach ($_where as $_where_part) {
                        Database_Model::getInstance()->where($_where_part[0], $_where_part[1]);
                    }
                }
                Database_Model::getInstance()->groupBy("HostFact_Terminations.id");
                if($sort_by) {
                    Database_Model::getInstance()->orderBy($sort_by, $sort_order);
                }
                if(0 <= $offset && $results_per_page != "all") {
                    Database_Model::getInstance()->limit(0, $offset + $results_per_page);
                }
                $has_sql = true;
            }
        }
        if($account->checkUserRights("other")) {
            Database_Model::getInstance()->{$has_sql ? "getUnion" : "get"}("HostFact_Terminations", ["HostFact_Terminations.*", "HostFact_Debtors.DebtorCode", "HostFact_Debtors.CompanyName", "HostFact_Debtors.Initials", "HostFact_Debtors.SurName", "HostFact_PeriodicElements.StartPeriod", "HostFact_PeriodicElements.Description as ServiceName", "COUNT(HostFact_Actions.`id`) as `RemainingActions`"])->join("HostFact_PeriodicElements", "HostFact_Terminations.`ServiceID`=HostFact_PeriodicElements.`id` AND HostFact_PeriodicElements.`PeriodicType`='other' AND HostFact_Terminations.`ServiceType`='other' AND HostFact_PeriodicElements.`Status`!=9", "")->join("HostFact_Debtors", "HostFact_Terminations.`Debtor`=HostFact_Debtors.`id`")->join("HostFact_Actions", "HostFact_Actions.`ReferenceType`='termination' AND HostFact_Actions.`ReferenceID`=HostFact_Terminations.`id` AND HostFact_Actions.`Status` IN ('pending','error')");
            if(!empty($_where)) {
                foreach ($_where as $_where_part) {
                    Database_Model::getInstance()->where($_where_part[0], $_where_part[1]);
                }
            }
            Database_Model::getInstance()->groupBy("HostFact_Terminations.id");
            if($sort_by) {
                Database_Model::getInstance()->orderBy($sort_by, $sort_order);
            }
            if(0 <= $offset && $results_per_page != "all") {
                Database_Model::getInstance()->limit(0, $offset + $results_per_page);
            }
            $has_sql = true;
        }
        if(!$has_sql) {
            $this->total_results = 0;
            return [];
        }
        Database_Model::getInstance()->closeUnion();
        if($sort_by == "CONCAT(HostFact_Debtors.`CompanyName`, HostFact_Debtors.`SurName`)") {
            Database_Model::getInstance()->orderBy("CONCAT(`CompanyName`, `SurName`)", $sort_order);
        } elseif($sort_by) {
            Database_Model::getInstance()->orderBy($sort_by, $sort_order);
        }
        if(0 <= $offset && $results_per_page != "all") {
            Database_Model::getInstance()->limit($offset, $results_per_page);
        }
        $list = Database_Model::getInstance()->execute();
        Database_Model::getInstance()->get("HostFact_Terminations", "id");
        if(!empty($_where)) {
            foreach ($_where as $_where_part) {
                Database_Model::getInstance()->where($_where_part[0], $_where_part[1]);
            }
        }
        $total_results = Database_Model::getInstance()->execute();
        $this->total_results = count($total_results);
        return $list;
    }
    public function getTerminationEndDate($service, $service_has_expiration_date = false)
    {
        $subscription_info = $service->Subscription;
        if(0 < $subscription_info->Identifier) {
            $end_contract = $subscription_info->EndContract ? $subscription_info->EndContract : $subscription_info->StartPeriod;
            $contract_periods = 0 < $subscription_info->ContractPeriods ? $subscription_info->ContractPeriods : $subscription_info->Periods;
            $contract_periodic = 0 < $subscription_info->ContractPeriods ? $subscription_info->ContractPeriodic : $subscription_info->Periodic;
            $current_date_with_term = date("Y-m-d", strtotime("+" . (int) TERMINATION_NOTICE_PERIOD . " days"));
            $end_date = date("Y-m-d", strtotime(rewrite_date_site2db($end_contract)));
            if($current_date_with_term <= $end_date) {
            } elseif(TERMINATION_NOTICE_PERIOD_WVD == "yes" && $service->CompanyName == "") {
                $end_date = date("Y-m-d");
                $contract_periods = 30 < TERMINATION_NOTICE_PERIOD ? 1 : TERMINATION_NOTICE_PERIOD;
                $contract_periodic = 30 < TERMINATION_NOTICE_PERIOD ? "m" : "d";
                do {
                    $end_date = calculate_date($end_date, $contract_periods, $contract_periodic);
                } while (!($end_date < $current_date_with_term && $end_date !== false));
            } else {
                do {
                    $end_date = calculate_date($end_date, $contract_periods, $contract_periodic);
                } while (!($end_date < $current_date_with_term && $end_date !== false));
            }
        } else {
            $end_date = date("Y-m-d", strtotime("+" . (int) TERMINATION_NOTICE_PERIOD . " days"));
            if($service_has_expiration_date !== false && $end_date < $service_has_expiration_date) {
                $end_date = $service_has_expiration_date;
            }
        }
        return $end_date;
    }
    public function cronTasks()
    {
        $result = Database_Model::getInstance()->get("HostFact_Terminations", "id")->where("Status", "pending")->where("Date", ["<=" => ["RAW" => "CURDATE()"]])->where("id", ["NOT IN" => ["RAW" => "SELECT action.`ReferenceID` FROM `HostFact_Actions` action WHERE action.`ReferenceType`='termination' AND (action.`Status`='pending' OR action.`Status`='error')"]])->asArray()->execute();
        if(!empty($result)) {
            $ids = array_column($result, "id");
            Database_Model::getInstance()->update("HostFact_Terminations", ["Status" => "processed"])->where("Status", "pending")->where("Date", ["<=" => ["RAW" => "CURDATE()"]])->where("id", ["IN" => $ids])->execute();
        }
    }
    private function __validateTermination()
    {
        if(!is_date($this->Date)) {
            $this->Error[] = __("termination has invalid date");
        }
        return empty($this->Error) ? true : false;
    }
}

?>