<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "config.php";
$page = isset($_GET["page"]) ? htmlspecialchars(esc($_GET["page"])) : "";
$pagetype = "add";
$service_id = isset($_GET["id"]) ? intval(esc($_GET["id"])) : (isset($_POST["id"]) ? intval(esc($_POST["id"])) : NULL);
class Services_Controller extends Base_Controller
{
    public function terminations()
    {
        require_once "class/terminationprocedure.php";
        $termination = new Termination_Model();
        if(isset($_POST["action"]) && $_POST["action"]) {
            $_ids = isset($_POST["ids"]) && is_array($_POST["ids"]) ? $_POST["ids"] : [];
            switch ($_POST["action"]) {
                case "dialog:approve_termination":
                    $has_errors = false;
                    foreach ($_ids as $_tmp_id) {
                        $termination = new Termination_Model();
                        $termination->id = esc($_tmp_id);
                        if(!$termination->approveTermination()) {
                            $this->merge_messages($termination);
                            $has_errors = true;
                        }
                        unset($termination);
                    }
                    delete_stats_summary();
                    if($has_errors === false && !empty($_ids)) {
                        $this->Success[] = sprintf(__("approved x terminations"), count($_ids));
                    }
                    break;
                case "dialog:reject_termination":
                    $has_errors = false;
                    foreach ($_ids as $_tmp_id) {
                        $termination = new Termination_Model();
                        $termination->id = esc($_tmp_id);
                        if(!$termination->rejectTermination()) {
                            $this->merge_messages($termination);
                            $has_errors = true;
                        }
                        unset($termination);
                    }
                    delete_stats_summary();
                    if($has_errors === false && !empty($_ids)) {
                        $this->Success[] = sprintf(__("rejected x terminations"), count($_ids));
                    }
                    break;
                default:
                    if(empty($_ids)) {
                        $this->Warning[] = __("nothing selected");
                    }
                    flashMessage($this);
                    if(isset($_POST["table_redirect_url"]) && $_POST["table_redirect_url"]) {
                        header("Location: " . esc($_POST["table_redirect_url"]));
                        exit;
                    }
                    header("Location: services.php?page=terminations");
                    exit;
            }
        } else {
            $table_config = $termination->getTableConfig();
            if(isset($_SESSION["backoffice_tables_config"]["list_terminations"]["filter"])) {
                $table_config["filter"] = $_SESSION["backoffice_tables_config"]["list_terminations"]["filter"];
            } else {
                $table_config["filter"] = "pending";
            }
            $this->set("table_config", $table_config);
            $this->set("termination", $termination);
            $this->set("message", parse_message($termination));
            $this->set("sidebar_template", isset($_GET["sidebar"]) && $_GET["sidebar"] == "services" ? "service.sidebar.php" : "invoice.sidebar.php");
            $this->set("sidebar_active", "terminations");
            $this->set("current_page_url", "services.php?page=terminations" . (isset($_GET["sidebar"]) && $_GET["sidebar"] == "services" ? "&sidebar=services" : ""));
            $this->set("page", "terminations");
            $this->set("wfh_page_title", __("termination overview"));
            $this->view("termination.overview.php");
        }
    }
    public function terminations_terminate()
    {
        require_once "class/terminationprocedure.php";
        $termination_procedure = new TerminationProcedure_Model();
        $termination = new Termination_Model();
        if(isset($_POST["ProcedureAction"]) && is_array($_POST["ProcedureAction"])) {
            $termination->ProcedureActions = $termination_procedure->parseProcedureActionPost($_POST["ProcedureAction"]);
        }
        $service_type = esc($_POST["ServiceType"]);
        $service_id = intval(esc($_POST["ServiceID"]));
        $termination->ServiceType = $service_type;
        $termination->ServiceID = $service_id;
        $termination->Date = in_array($_POST["TerminationTerm"], ["date", "contract"]) ? date("Y-m-d", strtotime(rewrite_date_site2db(esc($_POST["TerminationDate"])))) : date("Y-m-d");
        $termination->ProcedureID = isset($_POST["TerminationProcedure"]) && 0 < $_POST["TerminationProcedure"] && !isset($_POST["ProcedureAction"]) ? esc($_POST["TerminationProcedure"]) : 0;
        $termination->Term = esc($_POST["TerminationTerm"]);
        $termination->Reason = esc($_POST["TerminationReason"]);
        $termination->add();
        flashMessage($termination_procedure, $termination);
        return $this->__redirectService($service_type, $service_id);
    }
    public function terminations_approve_termination()
    {
        require_once "class/terminationprocedure.php";
        $termination = new Termination_Model();
        $service_type = esc($_POST["ServiceType"]);
        $service_id = intval(esc($_POST["ServiceID"]));
        $termination->approveTermination($service_type, $service_id);
        flashMessage($termination);
        return $this->__redirectService($service_type, $service_id);
    }
    public function terminations_reject_termination()
    {
        require_once "class/terminationprocedure.php";
        $termination = new Termination_Model();
        $service_type = esc($_POST["ServiceType"]);
        $service_id = intval(esc($_POST["ServiceID"]));
        $termination->rejectTermination($service_type, $service_id);
        flashMessage($termination);
        return $this->__redirectService($service_type, $service_id);
    }
    public function terminations_editActions()
    {
        require_once "class/terminationprocedure.php";
        $termination = new Termination_Model();
        $service_type = esc($_POST["ServiceType"]);
        $service_id = intval(esc($_POST["ServiceID"]));
        $has_errors = false;
        if(!$termination->show($service_type, $service_id)) {
            flashMessage($termination);
            return $this->__redirectService($service_type, $service_id);
        }
        $current_action_ids = [];
        if(isset($termination->TerminationActions)) {
            foreach ($termination->TerminationActions as $_action_info) {
                $current_action_ids[$_action_info->id] = $_action_info->id;
            }
        }
        Database_Model::getInstance()->beginTransaction();
        if(isset($_POST["TerminationAction"]) && is_array($_POST["TerminationAction"])) {
            foreach ($_POST["TerminationAction"]["Type"] as $index => $action_type) {
                if((int) $index === 0) {
                } else {
                    if($action_type == "automatic") {
                        $action_description = esc($_POST["TerminationAction"]["AutomatedTask"][$index]);
                    } elseif(in_array($action_type, ["mail2client", "mail2user"])) {
                        $action_description = esc($_POST["TerminationAction"]["EmailTemplate"][$index]);
                    } else {
                        $action_type = "manual";
                        $action_description = esc($_POST["TerminationAction"]["Description"][$index]);
                    }
                    if(trim($action_description) == "") {
                    } else {
                        $datetime1 = new DateTime($termination->Date);
                        $datetime2 = new DateTime($_POST["TerminationAction"]["Date"][$index]);
                        $interval = $datetime1->diff($datetime2);
                        $date_diff = $interval->format("%R%a");
                        if(substr($date_diff, 0, 1) == "-") {
                            $action_days = (int) substr($date_diff, 1);
                            $action_when = "before";
                        } elseif(substr($date_diff, 1) == "0") {
                            $action_days = (int) substr($date_diff, 1);
                            $action_when = "direct";
                        }
                        $termination_action = new Action_Model("termination");
                        $termination_action->id = 0 < $_POST["TerminationAction"]["ID"][$index] ? esc($_POST["TerminationAction"]["ID"][$index]) : 0;
                        $termination_action->ReferenceID = $termination->id;
                        $termination_action->Date = rewrite_date_site2db(esc($_POST["TerminationAction"]["Date"][$index]));
                        $termination_action->ActionType = $action_type;
                        $termination_action->Description = $action_description;
                        $termination_action->When = $action_when;
                        $termination_action->Days = $action_days;
                        $termination_action->Status = $_POST["TerminationAction"]["Status"][$index];
                        if(0 < $termination_action->id) {
                            unset($current_action_ids[$termination_action->id]);
                        }
                        if(empty($termination_action->id) && !$termination_action->add() || 0 < $termination_action->id && !$termination_action->edit()) {
                            $has_errors = true;
                            $this->merge_messages($termination_action);
                        }
                    }
                }
            }
            if(!empty($current_action_ids)) {
                foreach ($current_action_ids as $_id) {
                    $termination_action = new Action_Model("termination");
                    $termination_action->deleteByID($_id);
                }
                delete_stats_summary();
            }
        }
        if($has_errors === true) {
            $this->Error[] = __("termination actions are not modified because of an error");
            flashMessage($this);
            Database_Model::getInstance()->rollBack();
        } else {
            $this->Success[] = __("termination actions are modified");
            flashMessage($this);
            Database_Model::getInstance()->commit();
        }
        return $this->__redirectService($service_type, $service_id);
    }
    public function terminations_reactivate()
    {
        require_once "class/terminationprocedure.php";
        $termination = new Termination_Model();
        $service_type = esc($_POST["ServiceType"]);
        $service_id = intval(esc($_POST["ServiceID"]));
        if(!$termination->show($service_type, $service_id)) {
            flashMessage($termination);
            return $this->__redirectService($service_type, $service_id);
        }
        $new_start_period = isset($_POST["subscription"]["StartPeriod"]) ? rewrite_date_site2db($_POST["subscription"]["StartPeriod"]) : false;
        $termination->undoTermination($new_start_period);
        flashMessage($termination);
        return $this->__redirectService($service_type, $service_id);
    }
    public function ajax_getActionsTableEdit()
    {
        require_once "class/terminationprocedure.php";
        $termination = new Termination_Model();
        $termination_procedure = new TerminationProcedure_Model();
        $termination->id = isset($_POST["termination_id"]) ? intval(esc($_POST["termination_id"])) : 0;
        if(!$termination->show()) {
            exit;
        }
        $this->set("array_automated_tasks", $termination_procedure->getProcedureAutomatedTasks());
        require_once "class/template.php";
        $emailtemplate = new emailtemplate();
        $emailtemplates = $emailtemplate->all(["Name"]);
        $this->set("emailtemplates", $emailtemplates);
        $termination_actions = new Action_Model("termination");
        $this->set("array_action_status", $termination_actions->StatusArray);
        $this->set("termination", $termination);
        $this->element("termination.actions.add.php");
        exit;
    }
    public function termination_actions()
    {
        require_once "class/terminationprocedure.php";
        $termination_actions = new Action_Model("termination");
        if(isset($_POST["action"]) && $_POST["action"]) {
            $_ids = isset($_POST["ids"]) && is_array($_POST["ids"]) ? $_POST["ids"] : [];
            switch ($_POST["action"]) {
                case "dialog:action_delete":
                    $has_errors = false;
                    foreach ($_ids as $_tmp_id) {
                        $termination_action = new Action_Model("termination");
                        if(!$termination_action->deleteByID($_tmp_id)) {
                            $this->merge_messages($termination_action);
                            $has_errors = true;
                        }
                        unset($termination_action);
                    }
                    delete_stats_summary();
                    if($has_errors === false && !empty($_ids)) {
                        $this->Success[] = sprintf(__("removed x actions"), count($_ids));
                    }
                    break;
                case "dialog:action_execute":
                    foreach ($_ids as $_tmp_id) {
                        $termination_action = new Action_Model("termination");
                        $result = $termination_action->execute($_tmp_id);
                        $this->merge_messages($termination_action);
                        unset($termination_action);
                    }
                    break;
                case "dialog:action_execute_manually":
                    foreach ($_ids as $_tmp_id) {
                        $termination_action = new Action_Model("termination");
                        $result = $termination_action->executeManually($_tmp_id);
                        $this->merge_messages($termination_action);
                        if(!$result) {
                        } else {
                            unset($termination_action);
                        }
                    }
                    break;
                default:
                    if(empty($_ids)) {
                        $this->Warning[] = __("nothing selected");
                    }
                    flashMessage($this);
                    if(isset($_POST["table_redirect_url"]) && $_POST["table_redirect_url"]) {
                        header("Location: " . esc($_POST["table_redirect_url"]));
                        exit;
                    }
                    header("Location: services.php?page=termination_actions");
                    exit;
            }
        } else {
            $table_config = $termination_actions->getTableConfig();
            if(isset($_SESSION["backoffice_tables_config"]["list_termination_actions"]["filter"])) {
                $table_config["filter"] = $_SESSION["backoffice_tables_config"]["list_termination_actions"]["filter"];
            } else {
                $table_config["filter"] = "pending";
            }
            $this->set("table_config", $table_config);
            $this->set("termination_actions", $termination_actions);
            $this->set("message", parse_message($termination_actions));
            $this->set("sidebar_template", isset($_GET["sidebar"]) && $_GET["sidebar"] == "services" ? "service.sidebar.php" : "invoice.sidebar.php");
            $this->set("sidebar_active", "termination_actions");
            $this->set("current_page_url", "services.php?page=termination_actions" . (isset($_GET["sidebar"]) && $_GET["sidebar"] == "services" ? "&sidebar=services" : ""));
            $this->set("page", "actions");
            $this->set("wfh_page_title", __("termination actions title"));
            $this->view("termination.actions.php");
        }
    }
    private function __redirectService($service_type, $service_id)
    {
        switch ($service_type) {
            case "domain":
                header("Location: domains.php?page=show&id=" . $service_id);
                exit;
                break;
            case "hosting":
                header("Location: hosting.php?page=show&id=" . $service_id);
                exit;
                break;
            default:
                if($service_type != "other") {
                    header("Location: modules.php?module=" . $service_type . "&page=show&id=" . $service_id);
                    exit;
                }
                header("Location: services.php?page=show&id=" . $service_id);
                exit;
        }
    }
}
if(in_array($page, ["terminations", "ajax", "termination_actions"])) {
    $service = new Services_Controller();
    $service->router($page);
    exit;
}
switch ($page) {
    case "add":
        if(isset($_POST["PeriodicType"]) && $_POST["PeriodicType"]) {
            $NumberSuffix = extractNumberAndSuffix($_POST["subscription"]["Number"]);
            if($NumberSuffix[1] === false) {
                $NumberSuffix[0] = $_POST["subscription"]["Number"];
                $NumberSuffix[1] = "";
            }
            list($_POST["subscription"]["Number"], $_POST["subscription"]["NumberSuffix"]) = $NumberSuffix;
            switch ($_POST["PeriodicType"]) {
                case "domain":
                    if(!U_DOMAIN_EDIT) {
                        checkRight(U_DOMAIN_EDIT);
                    }
                    break;
                case "hosting":
                    if(!U_HOSTING_EDIT) {
                        checkRight(U_HOSTING_EDIT);
                    }
                    break;
                default:
                    $label = "U_" . strtoupper($_POST["PeriodicType"]) . "_EDIT";
                    if(defined($label)) {
                        if(!constant($label)) {
                            checkRight(constant($label));
                        }
                    } elseif(!U_SERVICE_EDIT) {
                        checkRight(U_SERVICE_EDIT);
                    }
                    require_once "class/service.php";
                    $service = new service();
                    if(isset($_POST["emptyNextdate"]) && $_POST["emptyNextdate"] == "true") {
                        $_POST["subscription"]["NextDate"] = "";
                    }
                    $escaped_post_data = [];
                    foreach ($_POST as $key => $value) {
                        if(!is_array($value)) {
                            $escaped_post_data[$key] = esc($value);
                        } else {
                            foreach ($value as $key_sub => $value_sub) {
                                $escaped_post_data[$key][$key_sub] = esc($value_sub);
                            }
                        }
                    }
                    if($service->add($escaped_post_data)) {
                        flashMessage($service);
                        if($service->RedirectURL) {
                            header("Location: " . $service->RedirectURL);
                            exit;
                        }
                        header("Location: services.php?page=show&id=" . $service->Subscription->Identifier);
                        exit;
                    }
                    $service_error = true;
            }
        }
        break;
    case "edit":
        if(isset($_POST["PeriodicType"]) && $_POST["PeriodicType"]) {
            switch ($_POST["PeriodicType"]) {
                case "domain":
                    if(!U_DOMAIN_EDIT) {
                        checkRight(U_DOMAIN_EDIT);
                    }
                    break;
                case "hosting":
                    if(!U_HOSTING_EDIT) {
                        checkRight(U_HOSTING_EDIT);
                    }
                    break;
                default:
                    $label = "U_" . strtoupper($_POST["PeriodicType"]) . "_EDIT";
                    if(defined($label)) {
                        if(!constant($label)) {
                            checkRight(constant($label));
                        }
                    } elseif(!U_SERVICE_EDIT) {
                        checkRight(U_SERVICE_EDIT);
                    }
                    require_once "class/service.php";
                    $service = new service();
                    if(isset($_POST["emptyNextdate"]) && $_POST["emptyNextdate"] == "true") {
                        $_POST["subscription"]["NextDate"] = "";
                    }
                    $NumberSuffix = extractNumberAndSuffix($_POST["subscription"]["Number"]);
                    list($_POST["subscription"]["Number"], $_POST["subscription"]["NumberSuffix"]) = $NumberSuffix;
                    $escaped_post_data = [];
                    foreach ($_POST as $key => $value) {
                        if(!is_array($value)) {
                            $escaped_post_data[$key] = esc($value);
                        } else {
                            foreach ($value as $key_sub => $value_sub) {
                                $escaped_post_data[$key][$key_sub] = esc($value_sub);
                            }
                        }
                    }
                    if($service->edit($escaped_post_data)) {
                        flashMessage($service);
                        if($service->RedirectURL) {
                            header("Location: " . $service->RedirectURL);
                            exit;
                        }
                        header("Location: services.php?page=show&id=" . $service->Subscription->Identifier);
                        exit;
                    }
                    $service->show_debtor($service->Debtor);
            }
        }
        break;
    case "delete":
        $pagetype = "confirmDelete";
        $countResult = 0;
        if($service_id != NULL) {
            $page = "show";
        } else {
            $page = "overview";
        }
        if(empty($_POST) || !U_SERVICE_DELETE) {
        } elseif(isset($_POST["imsure"]) && $_POST["imsure"] == "yes") {
            require_once "class/service.php";
            $service = new service();
            $result = $service->Subscription->delete($service_id);
            if($result) {
                $service_id = NULL;
                $page = "overview";
                $subscriptionsDeleteData = [];
                $isSubscription = false;
                if(isset($_SESSION["ActionLog"]["Subscriptions"]["type"]["services"]["delete"]) && is_array($_SESSION["ActionLog"]["Subscriptions"]["type"]["services"]["delete"]) && 0 < count($_SESSION["ActionLog"]["Subscriptions"]["type"]["services"]["delete"])) {
                    $subscriptionsDeleteData = $_SESSION["ActionLog"]["Subscriptions"]["type"]["services"];
                    $isSubscription = true;
                } elseif(!empty($_SESSION["ActionLog"]["Services"])) {
                    $subscriptionsDeleteData = $_SESSION["ActionLog"]["Services"];
                }
                if(!empty($subscriptionsDeleteData["delete"])) {
                    array_shift($subscriptionsDeleteData["delete"]);
                    if(isset($_POST["forAll"]) && $_POST["forAll"] == "yes") {
                        $subscriptionsDeleteData["forAll"]["check"] = true;
                        foreach ($subscriptionsDeleteData["delete"] as $key => $service_id) {
                            $service = new service();
                            $batchResult = $service->Subscription->delete($service_id);
                            if($batchResult) {
                                unset($subscriptionsDeleteData["delete"][$key]);
                            }
                        }
                    } else {
                        $subscriptionsDeleteData["forAll"]["check"] = false;
                    }
                    if($isSubscription) {
                        if(is_array($subscriptionsDeleteData["delete"]) && empty($subscriptionsDeleteData["delete"])) {
                            unset($_SESSION["ActionLog"]["Subscriptions"]["type"]["services"]);
                            unset($_SESSION["ActionLog"]["Services"]["forAll"]);
                            header("Location: subscriptions.php?page=delete");
                            exit;
                        }
                        $_SESSION["ActionLog"]["Subscriptions"]["type"]["services"] = $subscriptionsDeleteData;
                        reset($_SESSION["ActionLog"]["Subscriptions"]["type"]["services"]["delete"]);
                        header("Location: ?page=delete&id=" . current($_SESSION["ActionLog"]["Subscriptions"]["type"]["services"]["delete"]));
                        exit;
                    }
                    $_SESSION["ActionLog"]["Services"] = $subscriptionsDeleteData;
                    reset($_SESSION["ActionLog"]["Services"]["delete"]);
                    if(!empty($_SESSION["ActionLog"]["Services"]["delete"])) {
                        header("Location: ?page=delete&id=" . current($_SESSION["ActionLog"]["Services"]["delete"]));
                        exit;
                    }
                    unset($_SESSION["ActionLog"]["Services"]["forAll"]);
                }
                $service->Subscription->Success = [];
                $service->Subscription->Success[] = __("one or more subscriptions are deleted");
                flashMessage($service->Subscription);
            }
            if(isset($_SESSION["ActionLog"]["Services"]["from_page"])) {
                $from_page = $_SESSION["ActionLog"]["Services"]["from_page"];
                $from_id = $_SESSION["ActionLog"]["Services"]["from_id"];
                unset($_SESSION["ActionLog"]["Services"]["from_page"]);
                unset($_SESSION["ActionLog"]["Services"]["from_id"]);
                switch ($from_page) {
                    case "debtor":
                        $_SESSION["selected_tab"] = 4;
                        header("Location: debtors.php?page=show&id=" . intval($from_id));
                        exit;
                        break;
                    case "search":
                        header("Location: search.php?page=show");
                        exit;
                        break;
                }
            }
            unset($pagetype);
            unset($current_status);
            $pagetype = "add";
        }
        break;
    default:
        if(isset($_SESSION["ActionLog"]["Services"]["delete"]) && is_array($_SESSION["ActionLog"]["Services"]["delete"])) {
            unset($_SESSION["ActionLog"]["Services"]["delete"]);
        }
        if(isset($_POST["action"])) {
            require_once "class/service.php";
            $list_services = isset($_POST["services"]) && is_array($_POST["services"]) ? $_POST["services"] : [];
            if(!empty($_POST["services"])) {
                switch ($_POST["action"]) {
                    case "delete":
                        if(!U_SERVICE_DELETE) {
                        } else {
                            if(!isset($_SESSION["ActionLog"]["Services"])) {
                                $_SESSION["ActionLog"]["Services"] = [];
                            }
                            $_SESSION["ActionLog"]["Services"]["delete"] = [];
                            foreach ($list_services as $s_id) {
                                $_SESSION["ActionLog"]["Services"]["delete"][] = $s_id;
                            }
                            if(isset($_GET["from_page"]) && !empty($_GET["from_page"])) {
                                $_SESSION["ActionLog"]["Services"]["from_page"] = esc($_GET["from_page"]);
                                $_SESSION["ActionLog"]["Services"]["from_id"] = intval(esc($_GET["from_id"]));
                            }
                            if(!empty($_SESSION["ActionLog"]["Services"]["delete"])) {
                                header("location: ?page=delete&id=" . current($_SESSION["ActionLog"]["Services"]["delete"]));
                                exit;
                            }
                        }
                        break;
                    case "dialog:changeDebtorOther":
                        if(!U_SERVICE_EDIT) {
                        } elseif(isset($_POST["Debtor"]) && 0 < $_POST["Debtor"]) {
                            if(isset($_POST["imsure"]) && $_POST["imsure"] == "yes") {
                                foreach ($list_services as $service_id) {
                                    $service = new service();
                                    $service->Identifier = $service_id;
                                    $service->changeDebtor(esc($_POST["Debtor"]));
                                    flashMessage($service);
                                    unset($service);
                                }
                            }
                        } else {
                            $error_class->Error[] = __("invalid debtor");
                        }
                        break;
                    case "dialog:terminate_other":
                        $error_messages = [];
                        $counters = ["already_done" => 0, "success" => 0];
                        foreach ($list_services as $service_id) {
                            $result = service_termination_batch_processing("other", esc($service_id), $_POST, $error_messages);
                            if($result === true) {
                                $counters["success"]++;
                            } elseif($result === "already_done") {
                                $counters["already_done"]++;
                            }
                        }
                        if(0 < $counters["success"]) {
                            $error_class->Success[] = sprintf(__("termination batch result success"), $counters["success"]);
                        }
                        if(0 < $counters["already_done"]) {
                            $error_class->Warning[] = sprintf(__("termination batch result already_done"), $counters["already_done"]);
                        }
                        $error_class->Error = array_merge($error_class->Error, $error_messages);
                        break;
                }
            } elseif(isset($_POST["action"])) {
                $service->Warning[] = __("nothing selected");
            }
            if(isset($_GET["from_page"]) && !empty($_GET["from_page"])) {
                flashMessage($service);
                switch ($_GET["from_page"]) {
                    case "debtor":
                        $_SESSION["selected_tab"] = 4;
                        header("Location: debtors.php?page=show&id=" . intval($_GET["from_id"]));
                        exit;
                        break;
                    case "search":
                        $_SESSION["selected_tab"] = 3;
                        header("Location: search.php?page=show");
                        exit;
                        break;
                }
            }
        }
        switch ($page) {
            case "add":
                require_once "class/debtor.php";
                $debtor = new debtor();
                $fields = ["Initials", "SurName", "CompanyName", "DebtorCode", "PeriodicInvoiceDays"];
                $debtors = $debtor->all($fields);
                require_once "class/product.php";
                $product = new product();
                $fields = ["ProductCode", "ProductName", "ProductType", "PriceExcl", "Sold", "PricePeriod"];
                $list_products = $product->all($fields, "ProductCode", "ASC", -1);
                require_once "class/package.php";
                $package = new package();
                $fields = ["PackageName", "TemplateName", "Server"];
                $list_hosting_packages = $package->all($fields, "PackageName", "ASC", -1);
                require_once "class/server.php";
                $server = new server();
                $fields = ["Name", "Location", "Port"];
                $list_hosting_servers = $server->all($fields, "Name", "ASC", -1);
                require_once "class/topleveldomain.php";
                $topleveldomain = new topleveldomain();
                $fields = ["Tld", "Registrar"];
                $list_domain_tlds = $topleveldomain->all($fields, "Tld", "ASC", -1);
                require_once "class/registrar.php";
                $registrar = new registrar();
                $list_domain_registrars = $registrar->all(["Name"]);
                require_once "class/service.php";
                $service = isset($service) && is_object($service) ? $service : new service();
                require_once "class/domain.php";
                if(!isset($service->domain) || !isset($service_error) || !$service_error) {
                    $service->domain = new domain();
                }
                require_once "class/hosting.php";
                if(!isset($service->hosting) || !isset($service_error) || !$service_error) {
                    $service->hosting = new hosting();
                }
                $service->Subscription->Periodic = "m";
                if(isset($_SESSION["search.domain"]["searchfor"])) {
                    unset($_SESSION["search.domain"]["searchfor"]);
                }
                $service_types_to_add = ["domain", "hosting"];
                if(!empty($additional_product_types)) {
                    $service_types_to_add = array_merge($service_types_to_add, array_keys($additional_product_types));
                }
                if(isset($_GET["type"]) && in_array($_GET["type"], $service_types_to_add)) {
                    $service->PeriodicType = esc($_GET["type"]);
                    $sidebar_active = esc($_GET["type"]);
                }
                if(isset($_GET["debtor"]) && is_numeric($_GET["debtor"])) {
                    $service->Debtor = esc($_GET["debtor"]);
                    if(!$service->show_debtor($service->Debtor)) {
                        $service->Debtor = 0;
                    }
                    if($_GET["type"] == "domain") {
                        require_once "class/hosting.php";
                        $hosting = new hosting();
                        $list_hosting_accounts = $hosting->all(["Username", "Status"], "Username", false, "-1", "Debtor", $service->Debtor, "1|3|4|5|7");
                    }
                } elseif(isset($_GET["from"]) && $_GET["from"] == "hosting") {
                    $hosting_id = isset($_GET["from_id"]) && is_numeric($_GET["from_id"]) ? esc($_GET["from_id"]) : 0;
                    if(!$service->show($hosting_id, "hosting")) {
                        flashMessage($service);
                        header("Location: hosting.php");
                        exit;
                    }
                    $service->PeriodicType = "domain";
                    $service->domain->HostingID = $hosting_id;
                    $sidebar_active = "domain";
                    $service->Subscription = new periodic();
                    $service->Subscription->FromServicePage = true;
                    if(isset($_GET["extradomain"]) && $_GET["extradomain"] == "true") {
                    } elseif(isset($_GET["extradomain"]) && $_GET["extradomain"]) {
                        $tmp_domain = explode(".", esc($_GET["extradomain"]), 2);
                        $service->domain->Domain = $tmp_domain[0];
                        $service->domain->Tld = isset($tmp_domain[1]) ? $tmp_domain[1] : "";
                    } else {
                        $tmp_domain = explode(".", $service->hosting->Domain, 2);
                        $service->domain->Domain = $tmp_domain[0];
                        $service->domain->Tld = isset($tmp_domain[1]) ? $tmp_domain[1] : "";
                    }
                    require_once "class/hosting.php";
                    $hosting = new hosting();
                    $list_hosting_accounts = $hosting->all(["Username", "Status"], "Username", false, "-1", "Debtor", $service->Debtor, "1|3|4|5|7");
                    if(0 < $service->domain->HostingID) {
                        $hosting->show($service->domain->HostingID, false);
                        $hosting->getPanel();
                    }
                    if(!isset($service->NameserverFromDebtor) || !$service->NameserverFromDebtor) {
                        require_once "class/server.php";
                        $server = new server();
                        $server->show($service->hosting->Server);
                        if($server->DNS1) {
                            $service->domain->DNS1 = $server->DNS1;
                            $service->domain->DNS2 = $server->DNS2;
                            $service->domain->DNS3 = $server->DNS3;
                        }
                    }
                } elseif(isset($_GET["from"]) && $_GET["from"] == "subscription") {
                    $subscription_id = isset($_GET["from_id"]) && is_numeric($_GET["from_id"]) ? esc($_GET["from_id"]) : 0;
                    if(!$service->show($subscription_id, "other")) {
                        flashMessage($service);
                        header("Location: services.php");
                        exit;
                    }
                    if(isset($_GET["type"]) && $_GET["type"] == "domain") {
                        $service->PeriodicType = "domain";
                        $sidebar_active = "domain";
                        require_once "class/hosting.php";
                        $hosting = new hosting();
                        $list_hosting_accounts = $hosting->all(["Username", "Status"], "Username", false, "-1", "Debtor", $service->Debtor, "1|3|4|5|7");
                        $service->Warning[] = __("you are redirected to the service add page. The recurring profile is connected");
                    } elseif(isset($_GET["type"]) && $_GET["type"] == "hosting") {
                        $service->PeriodicType = "hosting";
                        $sidebar_active = "hosting";
                        $service->Warning[] = __("you are redirected to the service add page. The recurring profile is connected");
                    } elseif(isset($_GET["type"]) && in_array($_GET["type"], $service_types_to_add)) {
                        $service->PeriodicType = esc($_GET["type"]);
                        $sidebar_active = esc($_GET["type"]);
                        $service->Warning[] = __("you are redirected to the service add page. The recurring profile is connected");
                    }
                } elseif(!empty($service->Error) && $service->PeriodicType == "domain" && $service->Debtor) {
                    require_once "class/hosting.php";
                    $hosting = new hosting();
                    $list_hosting_accounts = $hosting->all(["Username", "Status"], "Username", false, "-1", "Debtor", $service->Debtor, "1|3|4|5|7");
                }
                switch ($service->PeriodicType) {
                    case "domain":
                        if(!U_DOMAIN_EDIT) {
                            checkRight(U_DOMAIN_EDIT);
                        }
                        break;
                    case "hosting":
                        if(!U_HOSTING_EDIT) {
                            checkRight(U_HOSTING_EDIT);
                        }
                        break;
                    default:
                        $label = "U_" . strtoupper($service->PeriodicType) . "_EDIT";
                        if(defined($label) && !constant($label)) {
                            checkRight(constant($label));
                        }
                        $message = parse_message($debtor, $product, $service);
                        $wfh_page_title = __("add service");
                        $current_page_url = "services.php";
                        $sidebar_template = "service.sidebar.php";
                        require_once "views/service.add.php";
                }
                break;
            case "edit":
                require_once "class/service.php";
                $service = isset($service) && is_object($service) ? $service : new service();
                $what_to_edit = "";
                if(isset($_POST["hosting_id"]) && 0 < $_POST["hosting_id"] || isset($_GET["hosting_id"]) && 0 < $_GET["hosting_id"]) {
                    $hosting_id = isset($_POST["hosting_id"]) ? intval(esc($_POST["hosting_id"])) : intval(esc($_GET["hosting_id"]));
                    if(isset($_POST["hosting_id"])) {
                        $service->hosting->oldServer = $escaped_post_data["hosting"]["oldServer"];
                        $service->hosting->oldPackage = $escaped_post_data["hosting"]["oldPackage"];
                    } else {
                        if(!$service->show($hosting_id, "hosting")) {
                            flashMessage($service);
                            header("Location: hosting.php");
                            exit;
                        }
                        $service->hosting->oldServer = $service->hosting->Server;
                        $service->hosting->oldPackage = $service->hosting->Package;
                    }
                    require_once "class/package.php";
                    $package = new package();
                    $fields = ["PackageName", "TemplateName", "Server"];
                    $list_hosting_packages = $package->all($fields, "PackageName", "ASC", -1);
                    require_once "class/server.php";
                    $server = new server();
                    $fields = ["Name", "Location", "Port"];
                    $list_hosting_servers = $server->all($fields, "Name", "ASC", -1);
                    $what_to_edit = __("hosting") . " " . $service->hosting->Username;
                    $sidebar_active = "hosting";
                } elseif(isset($_POST["domain_id"]) && 0 < $_POST["domain_id"] || isset($_GET["domain_id"]) && 0 < $_GET["domain_id"]) {
                    $domain_id = isset($_POST["domain_id"]) ? intval(esc($_POST["domain_id"])) : intval(esc($_GET["domain_id"]));
                    if(isset($_POST["domain_id"])) {
                    } elseif(!$service->show($domain_id, "domain")) {
                        flashMessage($service);
                        header("Location: domains.php");
                        exit;
                    }
                    require_once "class/topleveldomain.php";
                    $topleveldomain = new topleveldomain();
                    $fields = ["Tld", "Registrar"];
                    $list_domain_tlds = $topleveldomain->all($fields, "Tld", "ASC", -1);
                    require_once "class/registrar.php";
                    $registrar = new registrar();
                    $list_domain_registrars = $registrar->all(["Name"]);
                    require_once "class/hosting.php";
                    $hosting = new hosting();
                    $list_hosting_accounts = $hosting->all(["Username", "Status"], "Username", false, "-1", "Debtor", $service->domain->Debtor, "1|3|4|5|7");
                    if(0 < $service->domain->HostingID) {
                        $hosting->show($service->domain->HostingID, false);
                        $hosting->getPanel();
                    }
                    $what_to_edit = __("domain") . " " . $service->domain->Domain . "." . $service->domain->Tld;
                    $sidebar_active = "domain";
                } elseif(isset($_GET["type"]) && array_key_exists($_GET["type"], $additional_product_types)) {
                    if(isset($_POST["PeriodicType"])) {
                    } else {
                        $service->PeriodicType = esc($_GET["type"]);
                        $sidebar_active = esc($_GET["type"]);
                        if(!$service->show($service_id, $service->PeriodicType)) {
                            flashMessage($service);
                            header("Location: modules.php?module=" . $service->PeriodicType);
                            exit;
                        }
                    }
                    $what_to_edit = __("service edit title", $service->PeriodicType);
                    $additional_type = $service->PeriodicType;
                } else {
                    if(isset($_POST["subscription"]["Identifier"])) {
                    } else {
                        $id = isset($_POST["id"]) ? intval(esc($_POST["id"])) : intval(esc($_GET["id"]));
                        if(!$service->show($id, "other")) {
                            flashMessage($service);
                            header("Location: services.php");
                            exit;
                        }
                    }
                    $what_to_edit = __("subscription") . " " . $service->Subscription->Description;
                }
                require_once "class/debtor.php";
                $debtor = new debtor();
                $fields = ["Initials", "SurName", "CompanyName", "DebtorCode"];
                $debtors = $debtor->all($fields);
                require_once "class/product.php";
                $product = new product();
                $fields = ["ProductCode", "ProductName", "ProductType", "PriceExcl", "Sold", "PricePeriod"];
                $list_products = $product->all($fields, "ProductCode", "ASC", -1);
                if(isset($_SESSION["search.domain"]["searchfor"])) {
                    unset($_SESSION["search.domain"]["searchfor"]);
                }
                switch ($service->PeriodicType) {
                    case "domain":
                        if(!U_DOMAIN_EDIT) {
                            checkRight(U_DOMAIN_EDIT);
                        }
                        break;
                    case "hosting":
                        if(!U_HOSTING_EDIT) {
                            checkRight(U_HOSTING_EDIT);
                        }
                        break;
                    default:
                        $label = "U_" . strtoupper($service->PeriodicType) . "_EDIT";
                        if(defined($label)) {
                            if(!constant($label)) {
                                checkRight(constant($label));
                            }
                        } elseif(!U_SERVICE_EDIT) {
                            checkRight(U_SERVICE_EDIT);
                        }
                        $message = parse_message($debtor, $product, $service);
                        $wfh_page_title = $what_to_edit . " " . strtolower(__("edit"));
                        $current_page_url = "services.php";
                        $sidebar_template = "service.sidebar.php";
                        require_once "views/service.edit.php";
                }
                break;
            case "show":
                require_once "class/service.php";
                $service = isset($service) && is_object($service) ? $service : new service();
                $id = isset($_POST["id"]) ? intval(esc($_POST["id"])) : intval(esc($_GET["id"]));
                if(!$service->show($id, "other") || $service->Debtor <= 0) {
                    flashMessage($service);
                    header("Location: services.php");
                    exit;
                }
                if($service->Subscription->PeriodicType == "domain") {
                    header("Location: domains.php?page=show&id=" . $service->Subscription->Reference);
                    exit;
                }
                if($service->Subscription->PeriodicType == "hosting") {
                    header("Location: hosting.php?page=show&id=" . $service->Subscription->Reference);
                    exit;
                }
                if($service->Subscription->PeriodicType != "other" && isset($_module_instances[$service->Subscription->PeriodicType])) {
                    header("Location: modules.php?module=" . $service->Subscription->PeriodicType . "&page=show&id=" . $service->Subscription->Reference);
                    exit;
                }
                if($service->Subscription->PeriodicType != "other" && file_exists("services-" . $service->Subscription->PeriodicType . ".php")) {
                    header("Location: services-" . $service->Subscription->PeriodicType . ".php?page=show&id=" . $service->Subscription->Reference);
                    exit;
                }
                checkRight(U_SERVICE_SHOW);
                $service->Subscription->format();
                $service->Subscription->showContractInfo();
                require_once "class/product.php";
                $product = new product();
                if($service->Subscription->ProductCode) {
                    $product->ProductCode = htmlspecialchars_decode($service->Subscription->ProductCode);
                    $product->show();
                }
                if(U_INVOICE_SHOW) {
                    require_once "class/invoice.php";
                    $invoice = new invoice();
                    $invoice_table_options = $invoice->getConfigInvoiceTable();
                }
                $message = parse_message($product, $service);
                $current_page_url = "services.php?page=show&amp;id=" . $id;
                $sidebar_template = "service.sidebar.php";
                $is_service_terminated = service_is_terminated("other", $service->Subscription->Identifier);
                $wfh_page_title = __("other service") . " " . $service->Subscription->Description;
                require_once "views/service.show.php";
                break;
            default:
                checkRight(U_SERVICE_SHOW);
                require_once "class/periodic.php";
                $subscription = isset($subscription) && is_object($subscription) ? $subscription : new periodic();
                $session = isset($_SESSION["service.overview"]) ? $_SESSION["service.overview"] : [];
                $fields = ["Description", "ProductID", "ProductName", "Debtor", "CompanyName", "SurName", "Initials", "Periods", "Periodic", "PriceExcl", "Number", "NumberSuffix", "TaxPercentage", "StartPeriod", "EndPeriod", "Status", "TerminationDate", "NextDate", "ContractPeriods", "ContractPeriodic", "EndContract", "AutoRenew"];
                $sort = isset($session["sort"]) ? $session["sort"] : "Description";
                $order = isset($session["order"]) ? $session["order"] : "ASC";
                $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) ? $session["limit"] : "1");
                $searchat = "PeriodicType";
                $searchfor = "other";
                $selectgroup = isset($session["status"]) ? $session["status"] : "1|8";
                $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : MAX_RESULTS_LIST);
                $list_other_services = $subscription->all($fields, $sort, $order, $limit, $searchat, $searchfor, $selectgroup, false, $show_results);
                if(isset($list_other_services["CountRows"]) && $list_other_services["CountRows"] < $show_results * ($limit - 1)) {
                    $newPage = ceil($list_other_services["CountRows"] / $show_results);
                    if($newPage <= 0) {
                        $newPage = 1;
                    }
                    $_SESSION["service.overview"]["limit"] = $newPage;
                    header("Location: services.php");
                    exit;
                }
                $_SESSION["service.overview"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "status" => $selectgroup, "searchat" => $searchat, "searchfor" => $searchfor, "limit" => $limit];
                $current_page = $limit;
                $message = parse_message($subscription);
                $wfh_page_title = __("other service overview");
                $current_page_url = "services.php";
                $sidebar_template = "service.sidebar.php";
                require_once "views/service.overview.php";
        }
}

?>