<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class ClientareaChange_Model
{
    public $id;
    public $ReferenceType;
    public $ReferenceID;
    public $Action;
    public $Data;
    public $Debtor;
    public $Approval;
    public $Status;
    public $CreatorType;
    public $CreatorID;
    public $Created;
    public $Modified;
    public $Error;
    public $Success;
    public $Warning;
    public $Variables = ["id", "ReferenceType", "ReferenceID", "Action", "Data", "Debtor", "Approval", "Status", "CreatorType", "CreatorID", "Created", "Modified"];
    public function __construct()
    {
        $this->StatusArray = ["pending" => __("action status pending"), "executed" => __("action status executed"), "error" => __("action status error"), "canceled" => __("action status canceled"), "removed" => __("action status removed")];
        $this->Error = $this->Warning = $this->Success = [];
    }
    public function add()
    {
        if(!$this->__validate()) {
            return false;
        }
        $result = Database_Model::getInstance()->insert("HostFact_Clientarea_Changes", ["ReferenceType" => $this->ReferenceType, "ReferenceID" => $this->ReferenceID, "Action" => $this->Action, "Data" => json_encode($this->Data), "Debtor" => $this->Debtor, "Approval" => $this->Approval, "Status" => $this->Status, "CreatorType" => $this->CreatorType, "CreatorID" => $this->CreatorID, "Created" => ["RAW" => "NOW()"], "Modified" => ["RAW" => "NOW()"], "IP" => $this->IP])->execute();
        if(!$result) {
            return false;
        }
        $this->id = $result;
        return true;
    }
    public function edit()
    {
        if(!is_numeric($this->id)) {
            $this->Error[] = __("invalid identifier for clientarea change");
            return false;
        }
        if(!$this->__validate()) {
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Clientarea_Changes", ["ReferenceType" => $this->ReferenceType, "ReferenceID" => $this->ReferenceID, "Action" => $this->Action, "Data" => json_encode($this->Data), "Debtor" => $this->Debtor, "Approval" => $this->Approval, "Status" => $this->Status, "Modified" => ["RAW" => "NOW()"], "IP" => $this->IP])->where("id", $this->id)->execute();
        if(!$result) {
            return false;
        }
        return true;
    }
    public function cronTasks()
    {
        $tasks = Database_Model::getInstance()->get("HostFact_Clientarea_Changes", "id")->where("Status", "pending")->where("Approval", ["IN" => ["notused", "approved"]])->where("CreatorType", "debtor")->where("CreatorID", [">" => 0])->where("ReferenceType", ["IN" => ["debtor"]])->execute();
        if(!empty($tasks)) {
            foreach ($tasks as $_task) {
                $this->execute($_task->id);
            }
        }
        $tasks = Database_Model::getInstance()->get("HostFact_Clientarea_Changes", "id")->where("Status", "pending")->where("Approval", ["IN" => ["notused", "approved"]])->where("CreatorType", "debtor")->where("CreatorID", [">" => 0])->where("ReferenceType", ["IN" => ["domain"]])->limit(0, 5)->execute();
        if(!empty($tasks)) {
            foreach ($tasks as $_task) {
                $this->execute($_task->id);
            }
        }
    }
    public function execute($task_id)
    {
        $task = Database_Model::getInstance()->getOne("HostFact_Clientarea_Changes")->where("id", $task_id)->execute();
        if(!$task) {
            return false;
        }
        if($task->Status == "executed") {
            $this->Error[] = sprintf(__("cannot execute clientarea change, already executed"), __("clientarea change action " . strtolower($task->Action)));
            return false;
        }
        switch ($task->ReferenceType) {
            case "domain":
                require_once "class/domain.php";
                $domain = new domain();
                $result = $domain->serviceExecuteAction($task->ReferenceID, $task->Action, $task->Data);
                $this->Success = array_merge($this->Success, $domain->Success);
                $this->Warning = array_merge($this->Warning, $domain->Warning);
                $this->Error = array_merge($this->Error, $domain->Error);
                break;
            case "debtor":
                $result = false;
                require_once "class/debtor.php";
                $debtor = new debtor();
                $debtor->Identifier = $task->ReferenceID;
                if($debtor->show()) {
                    $clientarea_change_data = json_decode($task->Data);
                    if(isset($clientarea_change_data->StateCode) && $clientarea_change_data->StateCode) {
                        $clientarea_change_data->State = $clientarea_change_data->StateCode;
                    }
                    $old_invoice_email = htmlspecialchars_decode(check_email_address($debtor->InvoiceEmailAddress && check_email_address($debtor->InvoiceEmailAddress) ? $debtor->InvoiceEmailAddress : $debtor->EmailAddress, "convert"));
                    $old_pricequote_email = htmlspecialchars_decode(check_email_address($debtor->InvoiceDataForPriceQuote == "yes" && $debtor->InvoiceEmailAddress && check_email_address($debtor->InvoiceEmailAddress) ? $debtor->InvoiceEmailAddress : $debtor->EmailAddress, "convert"));
                    foreach ($debtor as $_field => $_value) {
                        $debtor->{$_field} = is_string($debtor->{$_field}) ? htmlspecialchars_decode($debtor->{$_field}) : $debtor->{$_field};
                        if(isset($clientarea_change_data->{$_field})) {
                            $debtor->{$_field} = $clientarea_change_data->{$_field};
                        }
                    }
                    $result = $debtor->edit();
                    if(isset($clientarea_change_data->InvoiceEmailAddress) || isset($clientarea_change_data->EmailAddress)) {
                        $new_invoice_email = check_email_address($debtor->InvoiceEmailAddress && check_email_address($debtor->InvoiceEmailAddress) ? $debtor->InvoiceEmailAddress : $debtor->EmailAddress, "convert");
                        $new_pricequote_email = check_email_address($debtor->InvoiceDataForPriceQuote == "yes" && $debtor->InvoiceEmailAddress && check_email_address($debtor->InvoiceEmailAddress) ? $debtor->InvoiceEmailAddress : $debtor->EmailAddress, "convert");
                        if($old_invoice_email != $new_invoice_email) {
                            Database_Model::getInstance()->update("HostFact_Invoice", ["EmailAddress" => $new_invoice_email])->where("Debtor", $debtor->Identifier)->where("EmailAddress", $old_invoice_email)->where("Status", ["<" => 4])->execute();
                        }
                        if($old_pricequote_email != $new_pricequote_email) {
                            Database_Model::getInstance()->update("HostFact_PriceQuote", ["EmailAddress" => $new_pricequote_email])->where("Debtor", $debtor->Identifier)->where("EmailAddress", $old_pricequote_email)->where("Status", ["<" => 3])->execute();
                        }
                    }
                }
                $this->Success = array_merge($this->Success, $debtor->Success);
                $this->Warning = array_merge($this->Warning, $debtor->Warning);
                $this->Error = array_merge($this->Error, $debtor->Error);
                break;
            default:
                if($result === true) {
                    Database_Model::getInstance()->update("HostFact_Clientarea_Changes", ["Status" => "executed", "Modified" => ["RAW" => "NOW()"]])->where("id", $task_id)->execute();
                    delete_stats_summary();
                    $this->Success[] = __("clientarea change executed succesfull");
                    return true;
                }
                if($result == "manual") {
                    $this->Warning[] = sprintf(__("clientarea change domain execute manually"), __("clientarea change action " . strtolower($task->Action)));
                    return false;
                }
                Database_Model::getInstance()->update("HostFact_Clientarea_Changes", ["Status" => "error", "Modified" => ["RAW" => "NOW()"]])->where("id", $task_id)->execute();
                array_unshift($this->Error, __("clientarea change executed error"));
                return false;
        }
    }
    public function markAsExecutedAndApproved()
    {
        Database_Model::getInstance()->update("HostFact_Clientarea_Changes", ["Status" => "executed", "Approval" => "approved", "Modified" => ["RAW" => "NOW()"]])->where("id", $this->id)->execute();
        delete_stats_summary();
        $this->Success[] = sprintf(__("clientarea change marked as executed succesfull"), __("clientarea change action " . strtolower($this->Action)));
        return true;
    }
    public function approve($new_data = false)
    {
        if(!is_numeric($this->id)) {
            $this->Error[] = __("invalid identifier for clientarea change");
            return false;
        }
        $update_array = ["Approval" => "approved", "Modified" => ["RAW" => "NOW()"]];
        if($new_data && is_array($new_data)) {
            $update_array["Data"] = json_encode($new_data);
        }
        $result = Database_Model::getInstance()->update("HostFact_Clientarea_Changes", $update_array)->where("id", $this->id)->execute();
        if(!$result) {
            return false;
        }
        delete_stats_summary();
        $this->Success[] = sprintf(__("clientarea change approved"), __("clientarea change action " . strtolower($this->Action)));
        return true;
    }
    public function reject()
    {
        if(!is_numeric($this->id)) {
            $this->Error[] = __("invalid identifier for clientarea change");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Clientarea_Changes", ["Approval" => "rejected", "Status" => "canceled", "Modified" => ["RAW" => "NOW()"]])->where("id", $this->id)->execute();
        if(!$result) {
            return false;
        }
        delete_stats_summary();
        $this->Success[] = sprintf(__("clientarea change rejected"), __("clientarea change action " . strtolower($this->Action)));
        return true;
    }
    public function delete()
    {
        if(!is_numeric($this->id)) {
            $this->Error[] = __("invalid identifier for clientarea change");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Clientarea_Changes", ["Status" => "removed", "Modified" => ["RAW" => "NOW()"]])->where("id", $this->id)->execute();
        if(!$result) {
            return false;
        }
        delete_stats_summary();
        $this->Success[] = sprintf(__("clientarea change removed"), __("clientarea change action " . strtolower($this->Action)));
        return true;
    }
    public function getTableConfig($option_filter)
    {
        $options = [];
        $options["cols"] = [["key" => "debtor", "title" => __("debtor"), "sortable" => "Debtor"], ["key" => "action", "title" => __("action"), "sortable" => "Action"], ["key" => "referencetype", "title" => __("clientarea change referencetype overview header"), "sortable" => "ReferenceType"], ["key" => "service", "title" => __("clientarea change service name")], ["key" => "date", "title" => __("date clientarea change"), "sortable" => "Modified"], ["key" => "status", "title" => __("status")]];
        $options["data"] = ["class/clientareachange.php", "ClientareaChange_Model", "getTableData"];
        if($option_filter == "open") {
            $options["actions"] = [["action" => "approve", "title" => __("accept modifications"), "dialog" => ["title" => __("batch clientarea changes approve title"), "content" => __("batch clientarea changes approve description")]], ["action" => "reject", "title" => __("decline modifications"), "dialog" => ["title" => __("batch clientarea changes reject title"), "content" => __("batch clientarea changes reject description")]], ["action" => "executedmanually", "title" => __("clientarea change execute manually"), "dialog" => ["title" => __("batch clientarea change execute manually title"), "content" => __("batch clientarea change execute manually description")]]];
        } elseif($option_filter == "processed") {
            $options["actions"] = [["action" => "execute", "title" => __("execute clientarea changes now"), "dialog" => ["title" => __("batch clientarea changes execute title"), "content" => __("batch clientarea changes execute description")]], ["action" => "executedmanually", "title" => __("clientarea change execute manually"), "dialog" => ["title" => __("batch clientarea change execute manually title"), "content" => __("batch clientarea change execute manually description")]], ["action" => "remove", "title" => __("remove"), "dialog" => ["title" => __("batch clientarea changes remove title"), "content" => __("batch clientarea changes remove description")]]];
        }
        $options["sort_by"] = "Modified";
        $options["sort_order"] = "DESC";
        $options["form_action"] = "clientareachanges.php";
        return $options;
    }
    public function getTableData($offset, $results_per_page, $sort_by = "", $sort_order = "ASC", $parameters = [])
    {
        $options = !empty($parameters) ? $parameters : [];
        $options["offset"] = $offset;
        $options["results_per_page"] = $results_per_page;
        $options["sort_by"] = $sort_by;
        $options["sort_order"] = $sort_order;
        $changes_list = $this->listChanges($options);
        $data = ["TotalResults" => $this->total_results];
        if(is_array($changes_list)) {
            foreach ($changes_list as $_change) {
                $service_url = "";
                $clientarea_change_url = "clientareachanges.php?page=show&amp;id=" . $_change->id;
                switch ($_change->ReferenceType) {
                    case "domain":
                        $service_url = "domains.php?page=show&id=" . $_change->ReferenceID;
                        break;
                    case "hosting":
                        $service_url = "hosting.php?page=show&id=" . $_change->ReferenceID;
                        break;
                    case "debtor":
                        if($_change->Status == "pending" && $_change->Approval == "pending" || $_change->Status == "error") {
                            $active_tab = $_change->Action == "editpayment" ? "#tab-directdebit" : "";
                            $clientarea_change_url = "debtors.php?page=edit&amp;id=" . $_change->ReferenceID . "&amp;clientareachange=" . $_change->id . $active_tab;
                        }
                        break;
                    default:
                        if(isset($_module_instances[$_change->ReferenceType])) {
                            $service_url = "modules.php?module=" . $_change->ReferenceType . "&page=show&id= " . $_change->ReferenceID;
                        } else {
                            $service_url = "services.php?page=show&id=" . $_change->ReferenceID;
                        }
                        if($_change->ReferenceType != "debtor") {
                            $service_url = "<a href=\"" . htmlspecialchars($service_url) . "\" class=\"a1\">" . htmlspecialchars($_change->ServiceName) . "</a>";
                        }
                        $data[] = ["id" => $_change->id, 0 < $_change->Debtor ? "<a href=\"debtors.php?page=show&amp;id=" . $_change->Debtor . "\" class=\"a1\">" . ($_change->CompanyName ? htmlspecialchars($_change->CompanyName) : htmlspecialchars($_change->SurName . ", " . $_change->Initials)) . "</a>" : __("new customer"), "<a href=\"" . $clientarea_change_url . "\" class=\"a1 c1\">" . __("clientarea change action " . strtolower($_change->Action)) . "</a>", __("clientarea change referencetype " . strtolower($_change->ReferenceType)), $service_url, rewrite_date_db2site($_change->Modified, "%d-%m-%Y " . __("at") . " %H:%i"), $this->StatusArray[$_change->Status]];
                }
            }
        }
        return $data;
    }
    public function listChanges($options = [])
    {
        $fields = isset($options["fields"]) && is_array($options["fields"]) ? $options["fields"] : [];
        $sort_by = isset($options["sort_by"]) && $options["sort_by"] ? $options["sort_by"] : "Modified";
        $sort_order = isset($options["sort_order"]) && $options["sort_order"] ? $options["sort_order"] : "DESC";
        $offset = isset($options["offset"]) && $options["offset"] ? $options["offset"] : "0";
        $results_per_page = isset($options["results_per_page"]) && $options["results_per_page"] ? $options["results_per_page"] : MAX_RESULTS_LIST;
        $filter = isset($options["filter"]) && $options["filter"] ? $options["filter"] : "";
        $filters = isset($options["filters"]) && $options["filters"] ? $options["filters"] : [];
        $group_by = isset($options["group_by"]) && $options["group_by"] ? $options["group_by"] : "";
        if(!is_array($fields) || empty($fields)) {
            $fields = ["id", "ReferenceType", "ReferenceID", "Action", "Data", "Debtor", "Approval", "Status", "CreatorType", "CreatorID", "Created", "Modified", "CompanyName", "SurName", "Initials", "DebtorCode"];
        }
        $debtor_array = ["CompanyName", "SurName", "Initials", "DebtorCode"];
        $select = [];
        if(!in_array("id", $fields)) {
            $select[] = "HostFact_Clientarea_Changes.`id`";
        }
        foreach ($fields as $column) {
            if(in_array($column, $debtor_array)) {
                $select[] = "HostFact_Debtors.`" . $column . "`";
            } else {
                $select[] = "HostFact_Clientarea_Changes.`" . $column . "`";
            }
        }
        global $account;
        if(!$account) {
            require_once "class/employee.php";
            $account = new employee();
        }
        $reference_types = [];
        if($account->checkUserRights("domain")) {
            $reference_types["domain"] = ["table_name" => "HostFact_Domains", "service_name" => "CONCAT(`HostFact_Domains`.`Domain`,'.',`HostFact_Domains`.`Tld`)"];
        }
        if($account->checkUserRights("hosting")) {
            $reference_types["hosting"] = ["table_name" => "HostFact_Hosting", "service_name" => "HostFact_Hosting.Username"];
        }
        global $additional_product_types;
        global $_module_instances;
        if(is_array($additional_product_types)) {
            foreach ($additional_product_types as $product_type => $product_type_title) {
                if($account->checkUserRights($product_type) && isset($_module_instances[$product_type]) && method_exists($_module_instances[$product_type], "service_get_termination_parameters")) {
                    $reference_types[$product_type] = $_module_instances[$product_type]->service_get_termination_parameters();
                    $reference_types[$product_type]["service_name"] = str_replace("service.", $reference_types[$product_type]["table_name"] . ".", $reference_types[$product_type]["service_name"]);
                }
            }
        }
        if(defined("U_DEBTOR_SHOW") && U_DEBTOR_SHOW || !isset($account->Identifier) || !$account->Identifier) {
            $reference_types["debtor"] = ["table_name" => "HostFact_Debtors", "service_name" => "HostFact_Debtors.CompanyName"];
        }
        if(isset($filters["reference_type"]) && $filters["reference_type"]) {
            if(isset($reference_types[$filters["reference_type"]])) {
                $reference_types = [$filters["reference_type"] => $reference_types[$filters["reference_type"]]];
            } else {
                $reference_types = [];
            }
        }
        $loop_array_for_results_and_rowcount = ["results", "rowcount"];
        $this->total_results = 0;
        $list = [];
        if(count($reference_types) === 0) {
            return $list;
        }
        foreach ($loop_array_for_results_and_rowcount as $_sql_type) {
            $counter = 0;
            foreach ($reference_types as $_reference_name => $_reference_type) {
                if($_sql_type == "results") {
                    $select_with_reference = $select;
                    $select_with_reference[] = $_reference_type["service_name"] . " as `ServiceName`";
                } else {
                    $select_with_reference = ["HostFact_Clientarea_Changes.`id`"];
                }
                if($counter === 0) {
                    Database_Model::getInstance()->get("HostFact_Clientarea_Changes", $select_with_reference);
                } else {
                    Database_Model::getInstance()->getUnion("HostFact_Clientarea_Changes", $select_with_reference);
                }
                $search_at = [];
                $DebtorSearch = false;
                if(isset($options["searchat"]) && $options["searchat"] && isset($options["search_for"]) && $options["search_for"]) {
                    $search_at = explode("|", $options["searchat"]);
                    $DebtorSearch = 0 < count(array_intersect($debtor_array, $search_at)) ? true : false;
                }
                if($_sql_type == "results") {
                    Database_Model::getInstance()->join($_reference_type["table_name"], "HostFact_Clientarea_Changes.`ReferenceType` = '" . preg_replace("/[^a-z0-9]/i", "", $_reference_name) . "' AND HostFact_Clientarea_Changes.`ReferenceID` = `" . preg_replace("/[^a-z0-9_]/i", "", $_reference_type["table_name"]) . "`.`id`");
                    if((0 < count(array_intersect($debtor_array, $fields)) || $DebtorSearch) && $_reference_name != "debtor") {
                        Database_Model::getInstance()->join("HostFact_Debtors", "HostFact_Clientarea_Changes.`Debtor` = HostFact_Debtors.`id`");
                    }
                    if(str_replace("`", "", $_reference_type["service_name"]) == "HostFact_PeriodicElements.Description") {
                        Database_Model::getInstance()->join("HostFact_PeriodicElements", " HostFact_Clientarea_Changes.`ReferenceID`=HostFact_PeriodicElements.`Reference` AND HostFact_PeriodicElements.`PeriodicType`=HostFact_Clientarea_Changes.`ReferenceType` AND HostFact_PeriodicElements.`Status`!=9");
                    }
                }
                if($_sql_type == "results") {
                    Database_Model::getInstance()->where("HostFact_Clientarea_Changes.ReferenceType", $_reference_name);
                } else {
                    Database_Model::getInstance()->where("HostFact_Clientarea_Changes.ReferenceType", ["IN" => array_keys($reference_types)]);
                }
                if(isset($filters["approval"]) && $filters["approval"]) {
                    Database_Model::getInstance()->where("HostFact_Clientarea_Changes.`Approval`", ["IN" => explode("|", $filters["approval"])]);
                }
                if(isset($filters["reference_type"]) && $filters["reference_type"]) {
                    Database_Model::getInstance()->where("HostFact_Clientarea_Changes.`ReferenceType`", $filters["reference_type"]);
                }
                if(isset($filters["reference_id"]) && $filters["reference_id"]) {
                    Database_Model::getInstance()->where("HostFact_Clientarea_Changes.`ReferenceID`", $filters["reference_id"]);
                }
                if(isset($filters["debtor"]) && $filters["debtor"]) {
                    Database_Model::getInstance()->where("HostFact_Clientarea_Changes.`Debtor`", $filters["debtor"]);
                }
                if(isset($filters["action"]) && $filters["action"]) {
                    Database_Model::getInstance()->where("HostFact_Clientarea_Changes.`Action`", $filters["action"]);
                }
                if($filter && array_key_exists($filter, $this->StatusArray)) {
                    Database_Model::getInstance()->where("HostFact_Clientarea_Changes.`Status`", $filter);
                } elseif($filter && strpos($filter, "|") !== false) {
                    Database_Model::getInstance()->where("HostFact_Clientarea_Changes.`Status`", ["IN" => explode("|", $filter)]);
                } else {
                    Database_Model::getInstance()->where("HostFact_Clientarea_Changes.`Status`", ["IN" => ["pending", "executed", "error", "canceled"]]);
                }
                $counter++;
                if($_sql_type == "rowcount") {
                    if($_sql_type == "results") {
                        Database_Model::getInstance()->closeUnion();
                        if($sort_by) {
                            Database_Model::getInstance()->orderBy($sort_by, $sort_order);
                        }
                        if(0 <= $offset && $results_per_page != "all") {
                            Database_Model::getInstance()->limit($offset, $results_per_page);
                        }
                        $list = Database_Model::getInstance()->execute();
                    } else {
                        $row_count = Database_Model::getInstance()->rowCount("HostFact_Clientarea_Changes", "HostFact_Clientarea_Changes.id");
                        $this->total_results = $row_count;
                    }
                } else {
                    if($sort_by) {
                        Database_Model::getInstance()->orderBy("HostFact_Clientarea_Changes.`" . $sort_by . "`", $sort_order);
                    }
                    if(0 <= $offset && $results_per_page != "all") {
                        Database_Model::getInstance()->limit(0, $offset + $results_per_page);
                    }
                }
            }
        }
        return $list;
    }
    public function show($get_reference_data = false)
    {
        if(!is_numeric($this->id)) {
            $this->Error[] = __("invalid identifier for clientarea change");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_Clientarea_Changes")->where("id", $this->id)->execute();
        if(!$result) {
            return false;
        }
        foreach ($result as $key => $value) {
            if($key == "Data") {
                $this->{$key} = json_decode($value);
            } else {
                $this->{$key} = $value;
            }
        }
        if($get_reference_data) {
            switch ($this->ReferenceType) {
                case "domain":
                    require_once "class/domain.php";
                    $this->ReferenceObject = new domain();
                    $this->ReferenceObject->Identifier = $this->ReferenceID;
                    if(!$this->ReferenceObject->show()) {
                        $this->Error = array_merge($this->Error, $this->ReferenceObject->Error);
                        return false;
                    }
                    switch ($this->Action) {
                        case "editwhois":
                            $this->ReferenceObject->showHandles();
                            break;
                        case "editdnszone":
                            if(is_module_active("dnsmanagement")) {
                                global $_module_instances;
                                $dnsmanagement = $_module_instances["dnsmanagement"];
                                if($current_dns_zone = $dnsmanagement->getOrSaveDNSZone($this->ReferenceObject->Identifier)) {
                                    $this->ReferenceObject->DNSZone = $current_dns_zone;
                                    $this->ReferenceObject->DNSZone["SettingSingleTTL"] = isset($dnsmanagement->dns_integration_obj->SettingSingleTTL) && $dnsmanagement->dns_integration_obj->SettingSingleTTL === true ? true : false;
                                } else {
                                    $this->Error = array_merge($this->Error, $dnsmanagement->Error);
                                }
                            }
                            break;
                    }
                    break;
                case "debtor":
                    require_once "class/debtor.php";
                    $this->ReferenceObject = new debtor();
                    $this->ReferenceObject->Identifier = $this->ReferenceID;
                    if(!$this->ReferenceObject->show()) {
                        $this->Error = array_merge($this->Error, $this->ReferenceObject->Error);
                        return false;
                    }
                    break;
            }
        }
        return true;
    }
    public function cancel()
    {
        if(!is_numeric($this->id)) {
            $this->Error[] = __("invalid identifier for clientarea change");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Clientarea_Changes", ["Status" => "canceled", "Modified" => ["RAW" => "NOW()"]])->where("id", $this->id)->execute();
        if(!$result) {
            return false;
        }
        delete_stats_summary();
        $this->Success[] = sprintf(__("clientarea change canceled"), __("clientarea change action " . strtolower($this->Action)));
        return true;
    }
    public function cancelPendingChange($service_type, $service_id, $service_debtor, $action)
    {
        if($action === false) {
            $this->Error[] = __("no clientarea change type given");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_Clientarea_Changes")->where("ReferenceType", $service_type)->where("ReferenceID", $service_id)->where("Debtor", $service_debtor)->where("Approval", ["IN" => ["notused", "approved", "pending"]])->where("Status", ["IN" => ["pending", "error"]])->where("Action", $action)->orderBy("Modified", "DESC")->execute();
        if($result && $result->id) {
            Database_Model::getInstance()->update("HostFact_Clientarea_Changes", ["Status" => "canceled", "Modified" => ["RAW" => "NOW()"]])->where("id", $result->id)->execute();
            $this->Success[] = __("clientarea change has been canceled");
            return true;
        }
        $this->Error[] = __("no pending clientarea changes or already processed");
        return false;
    }
    public function cancelPendingChanges($reference_type, $reference_id, $debtor)
    {
        $result = Database_Model::getInstance()->update("HostFact_Clientarea_Changes", ["Status" => "canceled", "Modified" => ["RAW" => "NOW()"]])->where("ReferenceType", $reference_type)->where("ReferenceID", $reference_id)->where("Debtor", $debtor)->orWhere([["Approval", "pending"], ["Status", "pending"]])->orderBy("Modified", "DESC")->execute();
        return $result ? true : false;
    }
    public function executedManually()
    {
        if($this->Status == "executed") {
            $this->Error[] = sprintf(__("cannot execute clientarea change, already executed"), __("clientarea change action " . strtolower($this->Action)));
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Clientarea_Changes", ["Status" => "executed", "Approval" => in_array($this->Approval, ["pending", "approved", "rejected"]) ? "approved" : "notused", "Modified" => ["RAW" => "NOW()"]])->where("id", $this->id)->execute();
        if(!$result) {
            return false;
        }
        strtolower($this->ReferenceType);
        switch (strtolower($this->ReferenceType)) {
            case "domain":
                createLog("domain", $this->ReferenceID, "clientarea change is executed manually", __("clientarea change action " . strtolower($this->Action)));
                break;
            default:
                delete_stats_summary();
                $this->Success[] = sprintf(__("clientarea change is marked as manually executed"), __("clientarea change action " . strtolower($this->Action)));
                return true;
        }
    }
    private function __validate()
    {
        if(!array_key_exists($this->Status, $this->StatusArray)) {
            $this->Error[] = __("invalid status");
        }
        return empty($this->Error) ? true : false;
    }
}

?>