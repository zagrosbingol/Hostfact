<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "config.php";
checkRight(U_DOMAIN_SHOW);
$page = isset($_GET["page"]) ? $_GET["page"] : "";
$domain_id = isset($_GET["id"]) ? intval(esc($_GET["id"])) : (isset($_POST["id"]) ? intval(esc($_POST["id"])) : NULL);
switch ($page) {
    case "show":
        if(isset($_GET["action"])) {
            if(!U_DOMAIN_EDIT) {
            } else {
                require_once "class/domain.php";
                $domain = new domain();
                $domain->show($domain_id);
                switch ($_GET["action"]) {
                    case "register":
                        $domain->register();
                        break;
                    case "transfer":
                        $domain->transfer();
                        break;
                    case "updatens":
                        $domain->DNS1 = esc($_POST["ns1"]);
                        $domain->DNS2 = esc($_POST["ns2"]);
                        $domain->DNS3 = esc($_POST["ns3"]);
                        $domain->DNS1IP = esc($_POST["ip1"]);
                        $domain->DNS2IP = esc($_POST["ip2"]);
                        $domain->DNS3IP = esc($_POST["ip3"]);
                        $domain->changeNameserver(true);
                        break;
                    case "extend":
                        $domain->extend();
                        break;
                    case "gettoken":
                        $domain->getToken(true);
                        break;
                    case "lock":
                        $domain->lock(true);
                        break;
                    case "unlock":
                        $domain->lock(false);
                        break;
                    case "sync":
                        $domain_array = $domain->checkForSync(false);
                        $domain->syncDomainsByRegistrar($domain_array);
                        break;
                    case "autorenew":
                        $domain->changeAutoRenew($domain->DomainAutoRenew == "on" ? "off" : "on");
                        break;
                    default:
                        if(!in_array($_GET["action"], ["startregister"])) {
                            flashMessage($domain);
                            header("Location: domains.php?page=show&id=" . $domain_id);
                            exit;
                        }
                }
            }
        } elseif(isset($_POST["action"])) {
            if(!U_DOMAIN_DELETE) {
            } else {
                require_once "class/domain.php";
                $domain = new domain();
                $domain->show($domain_id);
                switch ($_POST["action"]) {
                    case "removelogentry":
                        require_once "class/logfile.php";
                        $logfile = new logfile();
                        $list_log = isset($_POST["logentry"]) && is_array($_POST["logentry"]) ? $_POST["logentry"] : [];
                        foreach ($list_log as $log_id) {
                            $logfile->deleteEntry($log_id);
                        }
                        $domain->Error = array_merge($domain->Error, $logfile->Error);
                        if(empty($domain->Error)) {
                            $domain->Success[] = sprintf(__("removed count logentries"), count($list_log));
                        }
                        $_SESSION["selected_tab"] = 3;
                        break;
                    default:
                        flashMessage($domain);
                        header("Location: domains.php?page=show&id=" . $domain_id);
                        exit;
                }
            }
        }
        if(isset($_POST["Comment"]) && U_DOMAIN_EDIT) {
            require_once "class/domain.php";
            $domain = new domain();
            $domain->changeComment($domain_id, esc($_POST["Comment"]));
            $selected_tab = 2;
        }
        break;
    case "delete":
        $pagetype = "confirmDelete";
        if($domain_id != NULL) {
            $page = "show";
        } else {
            $page = "overview";
        }
        $resultCount = 0;
        if(empty($_POST) || !U_DOMAIN_DELETE) {
        } elseif(isset($_POST["imsure"]) && $_POST["imsure"] == "yes") {
            $confirmRegistrar = isset($_POST["confirmRegistrar"]) ? $_POST["confirmRegistrar"] : "";
            $_SESSION["ActionLog"]["Domain"]["forAll"]["confirmRegistrar"] = $confirmRegistrar;
            require_once "class/domain.php";
            $domain = new domain();
            $domain->Identifier = $domain_id;
            $result = $domain->delete($domain_id, $confirmRegistrar);
            if($result) {
                $domain_id = NULL;
                $page = "overview";
                $subscriptionsDeleteData = [];
                $isSubscription = false;
                if(isset($_SESSION["ActionLog"]["Subscriptions"]["type"]["domain"]["delete"]) && is_array($_SESSION["ActionLog"]["Subscriptions"]["type"]["domain"]["delete"]) && 0 < count($_SESSION["ActionLog"]["Subscriptions"]["type"]["domain"]["delete"])) {
                    $subscriptionsDeleteData = $_SESSION["ActionLog"]["Subscriptions"]["type"]["domain"];
                    $isSubscription = true;
                } elseif(!empty($_SESSION["ActionLog"]["Domain"])) {
                    $subscriptionsDeleteData = $_SESSION["ActionLog"]["Domain"];
                }
                if(!empty($subscriptionsDeleteData["delete"])) {
                    array_shift($subscriptionsDeleteData["delete"]);
                    if(isset($_POST["forAll"]) && $_POST["forAll"] == "yes") {
                        $subscriptionsDeleteData["forAll"]["check"] = true;
                        foreach ($subscriptionsDeleteData["delete"] as $key => $domain_id) {
                            $domain = new domain();
                            $domain->Identifier = $domain_id;
                            $domain->show();
                            $removeDomain = true;
                            if($domain->Status == 4 && $confirmRegistrar == "") {
                                $removeDomain = false;
                            }
                            if($removeDomain) {
                                $result = $domain->delete($domain_id, $confirmRegistrar);
                                if($result) {
                                    unset($subscriptionsDeleteData["delete"][$key]);
                                }
                            }
                        }
                    } else {
                        $subscriptionsDeleteData["forAll"]["check"] = false;
                    }
                    if($isSubscription) {
                        if(is_array($subscriptionsDeleteData["delete"]) && empty($subscriptionsDeleteData["delete"])) {
                            unset($_SESSION["ActionLog"]["Subscriptions"]["type"]["domain"]);
                            unset($_SESSION["ActionLog"]["Domain"]["forAll"]);
                            header("Location: subscriptions.php?page=delete");
                            exit;
                        }
                        $_SESSION["ActionLog"]["Subscriptions"]["type"]["domain"] = $subscriptionsDeleteData;
                        reset($_SESSION["ActionLog"]["Subscriptions"]["type"]["domain"]["delete"]);
                        header("Location: ?page=delete&id=" . current($_SESSION["ActionLog"]["Subscriptions"]["type"]["domain"]["delete"]));
                        exit;
                    }
                    $_SESSION["ActionLog"]["Domain"] = $subscriptionsDeleteData;
                    reset($_SESSION["ActionLog"]["Domain"]["delete"]);
                    if(!empty($_SESSION["ActionLog"]["Domain"]["delete"])) {
                        header("Location: ?page=delete&id=" . current($_SESSION["ActionLog"]["Domain"]["delete"]));
                        exit;
                    }
                    unset($_SESSION["ActionLog"]["Domain"]["forAll"]);
                }
                $domain->Success = [];
                $domain->Success[] = __("one or more domains are deleted");
            }
            flashMessage($domain);
            if(isset($_SESSION["ActionLog"]["Domain"]["from_page"]) && !empty($_SESSION["ActionLog"]["Domain"]["from_page"])) {
                $from_page = $_SESSION["ActionLog"]["Domain"]["from_page"];
                $from_id = $_SESSION["ActionLog"]["Domain"]["from_id"];
                unset($_SESSION["ActionLog"]["Domain"]["from_page"]);
                switch ($from_page) {
                    case "tld":
                        header("Location: topleveldomains.php?page=show&id=" . intval($from_id));
                        exit;
                        break;
                    case "registrar":
                        header("Location: registrars.php?page=show&id=" . intval($from_id));
                        exit;
                        break;
                    case "debtor":
                        header("Location: debtors.php?page=show&id=" . intval($from_id));
                        exit;
                        break;
                }
            }
            if($result) {
                header("Location: domains.php");
                exit;
            }
            header("Location: domains.php?page=show&id=" . $domain_id);
            exit;
        }
        break;
    case "updatewhois":
        if(isset($_POST["action"]) && $_POST["action"] == "sync") {
            require_once "class/domain.php";
            $domain = new domain();
            $domain->show($domain_id);
            if(isset($_POST["Costs_Billing"]) && $_POST["Costs_Billing"] == "yes") {
                $domain->ChargeCosts = true;
            }
            $domain->updateWhoisDataToRegistrar();
            flashMessage($domain);
            header("Location: domains.php?page=show&id=" . $domain->Identifier);
            exit;
        }
        if(isset($_POST["action"]) && $_POST["action"] == "contacts") {
            $_POST = [];
        }
        if(empty($_POST) || !U_DOMAIN_EDIT) {
        } else {
            require_once "class/domain.php";
            $domain = new domain();
            $domain->show(esc($_POST["id"]));
            $created_handles = [];
            $handle_types = ["owner", "admin", "tech"];
            foreach ($handle_types as $handle_type) {
                switch ($_POST[$handle_type . "c"]) {
                    case "handle":
                        $domain->{$handle_type . "Handle"} = esc($_POST[$handle_type . "Handle"]);
                        break;
                    case "owner":
                        $domain->{$handle_type . "Handle"} = $domain->ownerHandle;
                        break;
                    case "new":
                        require_once "class/handle.php";
                        $handle = new handle();
                        foreach ($_POST as $key => $value) {
                            if(strpos($key, $handle_type) !== false && in_array(substr($key, strlen($handle_type)), $handle->Variables)) {
                                $handle->{substr($key, strlen($handle_type))} = esc($value);
                            }
                        }
                        if(!isset($_SESSION["custom_fields"]["handle"]) || $_SESSION["custom_fields"]["handle"]) {
                            $customfields_list = $_SESSION["custom_fields"]["handle"];
                            $handle->customvalues = [];
                            foreach ($customfields_list as $k => $custom_field) {
                                $handle->customvalues[$custom_field["FieldCode"]] = isset($_POST[$handle_type . "custom"][$custom_field["FieldCode"]]) ? esc($_POST[$handle_type . "custom"][$custom_field["FieldCode"]]) : "";
                            }
                        }
                        $handle->Registrar = $domain->Registrar;
                        $handle->RegistrarHandle = "";
                        $tmp_existing_handle = $handle->searchExistingHandle();
                        if(0 < $tmp_existing_handle) {
                            $domain->{$handle_type . "Handle"} = $tmp_existing_handle;
                            unset($tmp_existing_handle);
                            $domain->Warning[] = sprintf(__("we didnt create a new handle for role x, but used an exact match instead"), __("domain " . $handle_type . " handle"));
                        } else {
                            $handle->Handle = $handle->nextInternalHandle($handle_type == "owner" ? "debtor" : "general", $domain->Debtor);
                            $result = $handle->add();
                            if($result) {
                                $domain->{$handle_type . "Handle"} = $handle->Identifier;
                                $created_handles[] = $handle->Identifier;
                            } else {
                                $domain->Error[] = __("domain " . $handle_type . " handle not adjusted");
                                $domain->Error = array_merge($domain->Error, $handle->Error);
                            }
                        }
                        break;
                }
            }
            if(empty($domain->Error)) {
                $result = $domain->updateContactsInDatabase();
                if($result) {
                    if(isset($_POST["update_at_registrar"]) && $_POST["update_at_registrar"] == "yes") {
                        if(isset($_POST["Costs_Billing"]) && $_POST["Costs_Billing"] == "yes") {
                            $domain->ChargeCosts = true;
                        }
                        $domain->updateWhoisDataToRegistrar();
                    }
                    flashMessage($domain);
                    header("Location: domains.php?page=show&id=" . $domain->Identifier);
                    exit;
                }
            }
            foreach ($created_handles as $handle_id) {
                $handle->deleteFromDatabase($handle_id);
            }
        }
        break;
    default:
        if(isset($_SESSION["ActionLog"]["Domain"]["delete"]) && is_array($_SESSION["ActionLog"]["Domain"]["delete"])) {
            unset($_SESSION["ActionLog"]["Domain"]["delete"]);
        }
        if(isset($_POST["action"])) {
            require_once "class/domain.php";
            $list_domains = isset($_POST["domains"]) && is_array($_POST["domains"]) ? $_POST["domains"] : [];
            if(!empty($_POST["domains"])) {
                switch ($_POST["action"]) {
                    case "dialog:registerDomain":
                        if(!U_DOMAIN_EDIT) {
                        } else {
                            foreach ($list_domains as $d_id) {
                                $domain = new domain();
                                $domain->show($d_id);
                                if(!$domain->AuthKey && ($domain->Type == "register" || $domain->Type == "")) {
                                    $domain->register();
                                } else {
                                    $domain->transfer();
                                }
                                flashMessage($domain);
                                unset($domain);
                            }
                        }
                        break;
                    case "dialog:active":
                        if(!U_DOMAIN_EDIT) {
                        } else {
                            $domain = new domain();
                            $domain->setActive($list_domains);
                        }
                        break;
                    case "dialog:changeNameservers":
                        if(!U_DOMAIN_EDIT) {
                        } elseif(isset($_POST["imsure"]) && $_POST["imsure"] == "yes") {
                            foreach ($list_domains as $d_id) {
                                $domain = new domain();
                                $domain->show($d_id);
                                $domain->DNS1 = esc($_POST["ns1"]);
                                $domain->DNS2 = esc($_POST["ns2"]);
                                $domain->DNS3 = esc($_POST["ns3"]);
                                $domain->DNS1IP = "";
                                $domain->DNS2IP = "";
                                $domain->DNS3IP = "";
                                $domain->changeNameserver(true);
                                flashMessage($domain);
                                unset($domain);
                            }
                        }
                        break;
                    case "deleteDomain":
                        if(!U_DOMAIN_DELETE) {
                        } else {
                            if(!isset($_SESSION["ActionLog"]["Domain"])) {
                                $_SESSION["ActionLog"]["Domain"] = [];
                            }
                            $_SESSION["ActionLog"]["Domain"]["delete"] = [];
                            foreach ($list_domains as $d_id) {
                                $_SESSION["ActionLog"]["Domain"]["delete"][] = $d_id;
                            }
                            if(isset($_GET["from_page"]) && !empty($_GET["from_page"])) {
                                $_SESSION["ActionLog"]["Domain"]["from_page"] = esc($_GET["from_page"]);
                                $_SESSION["ActionLog"]["Domain"]["from_id"] = esc($_GET["from_id"]);
                            }
                            if(!empty($_SESSION["ActionLog"]["Domain"]["delete"])) {
                                header("location: ?page=delete&id=" . current($_SESSION["ActionLog"]["Domain"]["delete"]));
                                exit;
                            }
                        }
                        break;
                    case "dialog:extendDomain":
                        if(!U_DOMAIN_EDIT) {
                        } else {
                            foreach ($list_domains as $d_id) {
                                $domain = new domain();
                                $domain->show($d_id);
                                $domain->extend();
                                flashMessage($domain);
                                unset($domain);
                            }
                        }
                        break;
                    case "dialog:changeDebtorDomain":
                        if(!U_DOMAIN_EDIT) {
                        } elseif(isset($_POST["Debtor"]) && 0 < $_POST["Debtor"]) {
                            if(isset($_POST["imsure"]) && $_POST["imsure"] == "yes") {
                                foreach ($list_domains as $domain_id) {
                                    $domain = new domain();
                                    $domain->Identifier = $domain_id;
                                    $domain->changeDebtor(esc($_POST["Debtor"]));
                                    flashMessage($domain);
                                    unset($domain);
                                }
                            }
                        } else {
                            $error_class->Error[] = __("invalid debtor");
                        }
                        break;
                    case "dialog:changeWHOIS":
                        if(!U_DOMAIN_EDIT) {
                        } elseif(isset($_POST["imsure"]) && $_POST["imsure"] == "yes") {
                            $registrars = $_POST["registrar"];
                            $registrar_error = [];
                            foreach ($list_domains as $domain_id) {
                                $domain_id = esc($domain_id);
                                $domain = new domain();
                                $domain->show($domain_id);
                                if(isset($registrars[$domain->Registrar]) && is_array($registrars[$domain->Registrar])) {
                                    $old_handles = [$domain->ownerHandle, $domain->adminHandle, $domain->techHandle];
                                    Database_Model::getInstance()->beginTransaction();
                                    $commit_transaction = true;
                                    $handle_types = ["owner", "admin", "tech"];
                                    foreach ($handle_types as $handle_type) {
                                        $handle_postdata = $registrars[$domain->Registrar][$handle_type];
                                        switch ($handle_postdata) {
                                            case "":
                                            case "nochange":
                                            case "useDebtor":
                                                require_once "class/handle.php";
                                                $handle = new handle();
                                                $new_handle_data = $handle->createHandleFromDebtor($domain->Debtor, $domain->Registrar);
                                                $domain->{$handle_type . "Handle"} = $new_handle_data["id"];
                                                break;
                                            case "changeToOwner":
                                                if(is_numeric(esc($registrars[$domain->Registrar]["owner"]))) {
                                                    $domain->{$handle_type . "Handle"} = esc($registrars[$domain->Registrar]["owner"]);
                                                } else {
                                                    $domain->{$handle_type . "Handle"} = $domain->ownerHandle;
                                                }
                                                break;
                                            default:
                                                if(is_numeric($registrars[$domain->Registrar][$handle_type])) {
                                                    $domain->{$handle_type . "Handle"} = esc($registrars[$domain->Registrar][$handle_type]);
                                                }
                                        }
                                    }
                                    $new_handles = [$domain->ownerHandle, $domain->adminHandle, $domain->techHandle];
                                    if(count(array_diff_assoc($old_handles, $new_handles)) === 0) {
                                        Database_Model::getInstance()->rollBack();
                                    } else {
                                        if($domain->updateContactsInDatabase() && isset($_POST["update_at_registrar"]) && $_POST["update_at_registrar"] == "yes" && $domain->Status == 4) {
                                            if(isset($_POST["Costs_Billing"]) && $_POST["Costs_Billing"] == "yes" && isset($domain->OwnerChangeCost) && 0 < $domain->OwnerChangeCost) {
                                                $domain->ChargeCosts = true;
                                            }
                                            $domain->getRegistrar();
                                            if(isset($domain->NoRegistrarImplementation) && $domain->NoRegistrarImplementation || !isset($domain->api) || !is_object($domain->api)) {
                                                if(!isset($registrar_error[$domain->Registrar])) {
                                                    $registrar_error[$domain->Registrar] = sprintf(__("registrar x has no implementation"), $domain->Name);
                                                }
                                            } elseif($domain->ownerHandle <= 0) {
                                                $error_class->Error[] = sprintf(__("domain whois cannot be updated, no handle in software"), $domain->Domain . "." . $domain->Tld);
                                                Database_Model::getInstance()->rollBack();
                                                $commit_transaction = false;
                                            } elseif(!$domain->updateWhoisDataToRegistrar()) {
                                                Database_Model::getInstance()->rollBack();
                                                $commit_transaction = false;
                                            }
                                        }
                                        if($commit_transaction === true) {
                                            Database_Model::getInstance()->commit();
                                            $domain_link = "[hyperlink_1]domains.php?page=show&id=" . $domain->Identifier . "[hyperlink_2]" . $domain->Domain . "." . $domain->Tld . "[hyperlink_3]";
                                            $error_class->Success[] = sprintf(__("domain whois updated in software"), $domain_link);
                                        }
                                    }
                                }
                                flashMessage($domain);
                                unset($domain);
                            }
                            $error_class->Error = array_merge($error_class->Error, $registrar_error);
                        }
                        break;
                    case "dialog:terminate_domain":
                        $error_messages = [];
                        $counters = ["already_done" => 0, "success" => 0];
                        foreach ($list_domains as $domain_id) {
                            $domain_id = esc($domain_id);
                            $domain = new domain();
                            $domain->show($domain_id);
                            $result = service_termination_batch_processing("domain", $domain_id, $_POST, $error_messages, rewrite_date_site2db($domain->ExpirationDate));
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
                    case "dialog:syncDomains":
                        if(isset($_SESSION["temp_batch_sync_domains"]["skipped_domains"]) && $_SESSION["temp_batch_sync_domains"]["skipped_domains"] == 1) {
                            $error_class->Warning[] = __("domain not processed due to status");
                        } elseif(isset($_SESSION["temp_batch_sync_domains"]["skipped_domains"]) && 1 < $_SESSION["temp_batch_sync_domains"]["skipped_domains"]) {
                            $error_class->Warning[] = sprintf(__("domains not processed due to status"), $_SESSION["temp_batch_sync_domains"]["skipped_domains"]);
                        }
                        if(!empty($_SESSION["temp_batch_sync_domains"]["errors"])) {
                            $error_class->Warning[] = __("one of more domains not succesfull synced");
                            $error_class->Error = array_merge($_SESSION["temp_batch_sync_domains"]["errors"], $error_class->Error);
                        } elseif(isset($_SESSION["temp_batch_sync_domains"]) && $_SESSION["temp_batch_sync_domains"]["skipped_domains"] < count($list_domains)) {
                            $success_domains = intval(count($list_domains)) - intval($_SESSION["temp_batch_sync_domains"]["skipped_domains"]);
                            $error_class->Success[] = sprintf(__("batch domain sync succesfull"), $success_domains);
                        }
                        $error_class->Error = array_unique($error_class->Error);
                        unset($_SESSION["temp_batch_sync_domains"]);
                        break;
                }
            } elseif(isset($_POST["action"])) {
                $domain = new domain();
                $domain->Warning[] = __("nothing selected");
            }
            if(isset($_GET["from_page"]) && !empty($_GET["from_page"])) {
                flashMessage($domain);
                switch ($_GET["from_page"]) {
                    case "tld":
                        header("Location: topleveldomains.php?page=show&id=" . intval($_GET["from_id"]));
                        exit;
                        break;
                    case "registrar":
                        header("Location: registrars.php?page=show&id=" . intval($_GET["from_id"]));
                        exit;
                        break;
                    case "handle":
                        header("Location: handles.php?page=show&id=" . intval($_GET["from_id"]));
                        exit;
                        break;
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
            case "show":
                require_once "class/domain.php";
                $domain = isset($domain) && is_object($domain) ? $domain : new domain();
                if(!$domain->show($domain_id)) {
                    flashMessage($domain);
                    header("Location: domains.php");
                    exit;
                }
                $domain->showExtraFields($domain_id);
                $domain->showHandles();
                if($domain->Status == 6 && isset($domain->PendingInformation) && is_array($domain->PendingInformation) && $domain->_checkDomainPending($domain_id)) {
                    $domain->doPending($domain_id);
                    $domain->show($domain_id);
                }
                if($domain->Status == 6) {
                    if(isset($domain->PendingInformation) && is_array($domain->PendingInformation)) {
                        $domain->Warning[] = sprintf(__("domain in progress, last check, next check, current status"), $domain->PendingInformation["LastDate"], $domain->PendingInformation["NextDate"]);
                    } else {
                        $domain->Warning[] = __("domain is in progress");
                    }
                } elseif($domain->Status == 7) {
                    $domain->Warning[] = __("domain has errors");
                } elseif($domain->Status == 8 && isset($domain->Periodic) && $domain->Periodic->TerminationDate == "") {
                    $domain->Warning[] = __("domain cancelled, but subscription is active") . " " . __("edit domain to cancel subscription");
                }
                if(0 < $domain->PeriodicID && is_object($domain->Periodic)) {
                    $domain->Periodic->format();
                    $domain->Periodic->showContractInfo();
                }
                require_once "class/debtor.php";
                $debtor = new debtor();
                $debtor->show($domain->Debtor);
                require_once "class/logfile.php";
                $logfile = new logfile();
                $session = isset($_SESSION["domain.show.logfile"]) ? $_SESSION["domain.show.logfile"] : [];
                $fields = ["Date", "Who", "Name", "Action", "Values", "Translate"];
                $sort = isset($session["sort"]) ? $session["sort"] : "Date";
                $order = isset($session["order"]) ? $session["order"] : "DESC";
                $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) ? $session["limit"] : "1");
                $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : min(25, MAX_RESULTS_LIST));
                $list_domain_logfile = $logfile->all($fields, $sort, $order, $limit, "domain", $domain_id, $show_results);
                $_SESSION["domain.show.logfile"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "limit" => $limit];
                $current_page = $limit;
                require_once "class/handle.php";
                $handle = new handle();
                $list_domain_handles = $handle->all(["Handle", "HandleType", "Registrar", "Name", "RegistrarHandle"]);
                $domain->getRegistrar();
                require_once "class/clientareachange.php";
                $ClientareaChange = new ClientareaChange_Model();
                $ca_options = [];
                $ca_options["filters"]["approval"] = "pending|notused";
                $ca_options["filters"]["debtor"] = $domain->Debtor;
                $ca_options["filters"]["reference_type"] = "domain";
                $ca_options["filters"]["reference_id"] = $domain->id;
                $ca_options["filter"] = "pending|error";
                $clientarea_changes = $ClientareaChange->listChanges($ca_options);
                if(empty($clientarea_changes)) {
                    unset($ClientareaChange);
                } else {
                    foreach ($clientarea_changes as $_change) {
                        $change_url = "clientareachanges.php?page=show&amp;id=" . $_change->id;
                        $domain->Warning[] = sprintf(__("clientarea change warning " . $_change->Action), "[hyperlink_1]" . $change_url . "[hyperlink_2]" . __("show modifications") . "[hyperlink_3]");
                    }
                }
                if(U_INVOICE_SHOW && isset($domain->PeriodicID) && 0 < $domain->PeriodicID) {
                    require_once "class/invoice.php";
                    $invoice = new invoice();
                    $invoice_table_options = $invoice->getConfigInvoiceTable();
                }
                if(!isset($selected_tab) && isset($_SESSION["selected_tab"])) {
                    $selected_tab = $_SESSION["selected_tab"];
                    unset($_SESSION["selected_tab"]);
                }
                $message = parse_message($domain, isset($modification) ? $modification : NULL);
                $current_page_url = "domains.php?page=show&amp;id=" . $domain->Identifier;
                $sidebar_template = "service.sidebar.php";
                $is_service_terminated = service_is_terminated("domain", $domain->Identifier);
                $wfh_page_title = __("domain") . " " . $domain->Domain . "." . $domain->Tld;
                require_once "views/domain.show.php";
                break;
            case "updatewhois":
                require_once "class/domain.php";
                $domain = isset($domain) && is_object($domain) ? $domain : new domain();
                if(!$domain->show($domain_id)) {
                    flashMessage($domain);
                    header("Location: domains.php");
                    exit;
                }
                $domain->showHandles();
                require_once "class/debtor.php";
                $debtor = new debtor();
                $debtor->show($domain->Debtor);
                require_once "class/handle.php";
                $handle = new handle();
                $fields = ["Handle", "HandleType", "Registrar", "Name", "RegistrarHandle", "Debtor", "CompanyName", "Initials", "SurName"];
                $list_debtor_handles = $handle->all($fields, "Handle", "ASC", -1, "Debtor", $domain->Debtor);
                $list_general_handles = $handle->all($fields, "Handle", "ASC", -1, "Debtor", "0");
                $other_domains = ["owner" => 0, "admin" => 0, "tech" => 0];
                foreach ($other_domains as $k => $v) {
                    $tmp_list_domains = $domain->all(["id"], false, false, -1, $k . "Handle", $domain->{$k . "Handle"});
                    $other_domains[$k] = $tmp_list_domains["CountRows"];
                    unset($tmp_list_domains);
                }
                $domain->getRegistrar();
                $message = parse_message($domain);
                $wfh_page_title = sprintf(__("update whois data for domain"), $domain->Domain . "." . $domain->Tld);
                $current_page_url = "domains.php?page=updatewhois&amp;id=" . $domain->Identifier;
                $sidebar_template = "service.sidebar.php";
                require_once "views/domain.updatewhois.php";
                break;
            default:
                require_once "class/domain.php";
                $domain = isset($domain) && is_object($domain) ? $domain : new domain();
                $session = isset($_SESSION["domain.overview"]) ? $_SESSION["domain.overview"] : [];
                $fields = ["Domain", "Tld", "Debtor", "CompanyName", "SurName", "Initials", "RegistrationDate", "ExpirationDate", "Status", "Registrar", "Name", "Type", "AuthKey", "PeriodicID", "PriceExcl", "Periodic", "NextDate", "StartPeriod", "TerminationDate", "AutoRenew", "DomainAutoRenew", "TerminationID"];
                $sort = isset($session["sort"]) ? $session["sort"] : "Domain";
                $order = isset($session["order"]) ? $session["order"] : "ASC";
                $searchat = isset($session["searchat"]) ? $session["searchat"] : "";
                $searchfor = isset($session["searchfor"]) ? $session["searchfor"] : "";
                $selectgroup = isset($session["status"]) ? $session["status"] : "-1|1|3|4|5|6|7|8";
                $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) ? $session["limit"] : "1");
                $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : MAX_RESULTS_LIST);
                $list_domain_domains = $domain->all($fields, $sort, $order, $limit, $searchat, $searchfor, $selectgroup, $show_results);
                if(isset($list_domain_domains["CountRows"]) && ($list_domain_domains["CountRows"] < $show_results * ($limit - 1) || 1 < $limit && $list_domain_domains["CountRows"] == $show_results * ($limit - 1))) {
                    $newPage = ceil($list_domain_domains["CountRows"] / $show_results);
                    if($newPage <= 0) {
                        $newPage = 1;
                    }
                    $_SESSION["domain.overview"]["limit"] = $newPage;
                    header("Location: domains.php");
                    exit;
                }
                $_SESSION["domain.overview"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "status" => $selectgroup, "searchat" => $searchat, "searchfor" => $searchfor, "limit" => $limit];
                $current_page = $limit;
                $message = parse_message($domain);
                $wfh_page_title = __("domain overview");
                $current_page_url = "domains.php";
                $sidebar_template = "service.sidebar.php";
                require_once "views/domain.overview.php";
        }
}

?>