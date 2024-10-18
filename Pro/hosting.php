<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "config.php";
checkRight(U_HOSTING_SHOW);
$page = isset($_GET["page"]) ? $_GET["page"] : "";
$hosting_id = isset($_GET["id"]) ? intval(esc($_GET["id"])) : (isset($_POST["id"]) ? intval(esc($_POST["id"])) : NULL);
switch ($page) {
    case "show":
        $action = isset($_POST["action"]) ? $_POST["action"] : (isset($_GET["action"]) ? $_GET["action"] : "");
        if(isset($action) && $action) {
            require_once "class/hosting.php";
            $hosting = new hosting();
            $hosting->show($hosting_id, false);
            switch ($action) {
                case "create":
                    if(!U_HOSTING_EDIT) {
                    } else {
                        $hosting->create();
                    }
                    break;
                case "removelogentry":
                    require_once "class/logfile.php";
                    $logfile = new logfile();
                    $list_log = isset($_POST["logentry"]) && is_array($_POST["logentry"]) ? $_POST["logentry"] : [];
                    foreach ($list_log as $log_id) {
                        $logfile->deleteEntry($log_id);
                    }
                    $hosting->Error = array_merge($hosting->Error, $logfile->Error);
                    if(empty($hosting->Error)) {
                        $hosting->Success[] = sprintf(__("removed count logentries"), count($list_log));
                    }
                    $_SESSION["selected_tab"] = 3;
                    break;
                case "addDomain":
                    if(!U_HOSTING_EDIT) {
                    } else {
                        $list_domains = is_array($_POST["domain"]) ? $_POST["domain"] : [];
                        require_once "class/hosting.php";
                        foreach ($list_domains as $domaintld) {
                            if(!is_numeric($domaintld)) {
                                $hosting = new hosting();
                                $hosting->addDomainToServer($domaintld, $hosting_id);
                                flashMessage($hosting);
                                unset($hosting);
                            }
                        }
                    }
                    break;
                case "create_pdf":
                    if(isset($_POST["also_change_password"]) && $_POST["also_change_password"] == "yes") {
                        $hosting->Password = esc($_POST["change_password"]);
                        if(!$hosting->changeAccountPassword()) {
                        }
                    }
                    $hosting->createPDF($hosting_id, esc($_POST["OtherTemplate"]));
                    break;
                case "email_pdf":
                    if(isset($_POST["also_change_password"]) && $_POST["also_change_password"] == "yes") {
                        $hosting->Password = esc($_POST["change_password"]);
                        if(!$hosting->changeAccountPassword()) {
                        }
                    }
                    if(esc($_POST["EmailAddress"]) && check_email_address(esc($_POST["EmailAddress"]), "multiple")) {
                        $emailaddresses = check_email_address(esc($_POST["EmailAddress"]), "convert");
                        $hosting->emailPDF($hosting_id, esc($_POST["OtherTemplate"]), $emailaddresses);
                    } else {
                        $hosting->Error[] = __("invalid emailaddress");
                    }
                    break;
                case "unsuspend":
                    $hosting->suspend($hosting_id);
                    break;
                case "changelogindetails":
                    if(isset($_POST["imsure"]) && $_POST["imsure"] == "yes") {
                        $hosting->Password = esc($_POST["chg_Password"]);
                        if($hosting->changeAccountPassword() && isset($_POST["send_login_info_email"]) && $_POST["send_login_info_email"] == "yes" && 0 < $_POST["OtherEmailTemplate"]) {
                            $hosting->emailPDF($hosting_id, $_POST["OtherEmailTemplate"]);
                        }
                    }
                    break;
                case "updowngrade_hosting":
                    if(isset($_POST["imsure"]) && $_POST["imsure"] == "yes" && 0 < $_POST["new_product"]) {
                        $periodic_details = [];
                        $periodic_details["Periods"] = esc($_POST["subscription"]["Periods"]);
                        $periodic_details["Periodic"] = esc($_POST["subscription"]["Periodic"]);
                        $periodic_details["invoice_cycle"] = esc($_POST["invoice_cycle"]);
                        $periodic_details["create_invoice"] = isset($_POST["create_invoice"]) ? esc($_POST["create_invoice"]) : "no";
                        $updowngrade_result = $hosting->upDowngrade($hosting_id, esc($_POST["new_product"]), $periodic_details);
                    }
                    break;
                default:
                    if(!in_array($action, ["startcreate"])) {
                        flashMessage($hosting);
                        header("Location: hosting.php?page=show&id=" . $hosting_id);
                        exit;
                    }
            }
        }
        if(isset($_POST["Comment"]) && U_HOSTING_EDIT) {
            require_once "class/hosting.php";
            $hosting = new hosting();
            $hosting->changeComment($hosting_id, esc($_POST["Comment"]));
            $selected_tab = 2;
        }
        break;
    case "delete":
        $pagetype = "confirmDelete";
        if($hosting_id != NULL) {
            $page = "show";
        } else {
            $page = "overview";
        }
        $batchCount = 0;
        if(empty($_POST) || !U_HOSTING_DELETE) {
        } elseif(isset($_POST["imsure"]) && $_POST["imsure"] == "yes") {
            $confirmPeriodic = isset($_POST["confirmPeriodic"]) ? esc($_POST["confirmPeriodic"]) : "";
            $confirmServer = isset($_POST["confirmServer"]) ? esc($_POST["confirmServer"]) : "";
            $_SESSION["ActionLog"]["Hosting"]["forAll"]["confirmServer"] = $confirmServer;
            require_once "class/hosting.php";
            $hosting = new hosting();
            $result = $hosting->delete($hosting_id, $confirmPeriodic, $confirmServer);
            if($result) {
                $hosting_id = NULL;
                $page = "overview";
                $subscriptionsDeleteData = [];
                $isSubscription = false;
                if(isset($_SESSION["ActionLog"]["Subscriptions"]["type"]["hosting"]["delete"]) && is_array($_SESSION["ActionLog"]["Subscriptions"]["type"]["hosting"]["delete"]) && 0 < count($_SESSION["ActionLog"]["Subscriptions"]["type"]["hosting"]["delete"])) {
                    $subscriptionsDeleteData = $_SESSION["ActionLog"]["Subscriptions"]["type"]["hosting"];
                    $isSubscription = true;
                } elseif(!empty($_SESSION["ActionLog"]["Hosting"])) {
                    $subscriptionsDeleteData = $_SESSION["ActionLog"]["Hosting"];
                }
                if(is_array($subscriptionsDeleteData["delete"]) && !empty($subscriptionsDeleteData["delete"])) {
                    array_shift($subscriptionsDeleteData["delete"]);
                    if(isset($_POST["forAll"]) && $_POST["forAll"] == "yes") {
                        $subscriptionsDeleteData["forAll"]["check"] = true;
                        foreach ($subscriptionsDeleteData["delete"] as $key => $hosting_id) {
                            $hosting = new hosting();
                            $hosting->Identifier = $hosting_id;
                            $hosting->show();
                            $batchConfirmPeriodic = isset($hosting->Periodic->Identifier) && 0 < $hosting->Periodic->Identifier ? "remove" : "";
                            if(($hosting->Status == 4 || $hosting->Status == 5) && $confirmServer != "") {
                                $result = $hosting->delete($hosting_id, $batchConfirmPeriodic, $confirmServer);
                                if($result) {
                                    unset($subscriptionsDeleteData["delete"][$key]);
                                }
                            } else {
                                $result = $hosting->delete($hosting_id, $batchConfirmPeriodic, "");
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
                            unset($_SESSION["ActionLog"]["Subscriptions"]["type"]["hosting"]);
                            unset($_SESSION["ActionLog"]["Hosting"]["forAll"]);
                            header("Location: subscriptions.php?page=delete");
                            exit;
                        }
                        $_SESSION["ActionLog"]["Subscriptions"]["type"]["hosting"] = $subscriptionsDeleteData;
                        reset($_SESSION["ActionLog"]["Subscriptions"]["type"]["hosting"]["delete"]);
                        header("Location: ?page=delete&id=" . current($_SESSION["ActionLog"]["Subscriptions"]["type"]["hosting"]["delete"]));
                        exit;
                    }
                    $_SESSION["ActionLog"]["Hosting"] = $subscriptionsDeleteData;
                    reset($_SESSION["ActionLog"]["Hosting"]["delete"]);
                    if(!empty($_SESSION["ActionLog"]["Hosting"]["delete"])) {
                        header("Location: ?page=delete&id=" . current($_SESSION["ActionLog"]["Hosting"]["delete"]));
                        exit;
                    }
                    unset($_SESSION["ActionLog"]["Hosting"]["forAll"]);
                }
                $hosting->Success = [];
                $hosting->Success[] = __("one or more hosting accounts are deleted");
            }
            flashMessage($hosting);
            if(isset($_SESSION["ActionLog"]["Hosting"]["from_page"]) && !empty($_SESSION["ActionLog"]["Hosting"]["from_page"])) {
                $from_page = $_SESSION["ActionLog"]["Hosting"]["from_page"];
                $from_id = $_SESSION["ActionLog"]["Hosting"]["from_id"];
                unset($_SESSION["ActionLog"]["Hosting"]["from_page"]);
                switch ($from_page) {
                    case "debtor":
                        header("Location: debtors.php?page=show&id=" . intval($from_id));
                        exit;
                        break;
                }
            }
            header("Location: hosting.php");
            exit;
        }
        break;
    case "suspend":
        $pagetype = "confirmSuspend";
        if($hosting_id != NULL) {
            $page = "show";
        } else {
            $page = "overview";
        }
        if(empty($_POST) || !U_HOSTING_EDIT) {
        } elseif(isset($_POST["imsure"]) && $_POST["imsure"] == "yes") {
            require_once "class/hosting.php";
            $hosting = new hosting();
            if($hosting->suspend($hosting_id)) {
                $hosting->Success = [];
                $hosting->Success[] = __("one or more hosting accounts are suspended");
            }
            flashMessage($hosting);
            header("Location: hosting.php?page=show&id=" . $hosting_id);
            exit;
        }
        break;
    default:
        if(isset($_SESSION["ActionLog"]["Hosting"]["delete"]) && is_array($_SESSION["ActionLog"]["Hosting"]["delete"])) {
            unset($_SESSION["ActionLog"]["Hosting"]["delete"]);
        }
        if(isset($_POST["action"])) {
            require_once "class/hosting.php";
            $list_accounts = isset($_POST["accounts"]) && is_array($_POST["accounts"]) ? $_POST["accounts"] : [];
            if(!empty($_POST["accounts"])) {
                switch ($_POST["action"]) {
                    case "registerHosting":
                        if(!U_HOSTING_EDIT) {
                        } else {
                            foreach ($list_accounts as $h_id) {
                                $hosting = new hosting();
                                $hosting->show($h_id);
                                $hosting->create();
                                flashMessage($hosting);
                                unset($hosting);
                            }
                        }
                        break;
                    case "dialog:suspendhosting":
                        if(!U_HOSTING_EDIT) {
                        } else {
                            foreach ($list_accounts as $h_id) {
                                $hosting = new hosting();
                                $hosting->suspend(esc($h_id));
                                flashMessage($hosting);
                                unset($hosting);
                            }
                        }
                        break;
                    case "dialog:activehosting":
                        if(!U_HOSTING_EDIT) {
                        } else {
                            $hosting = new hosting();
                            $hosting->setActive($list_accounts);
                        }
                        break;
                    case "deleteHosting":
                        if(!U_HOSTING_DELETE) {
                        } else {
                            if(!isset($_SESSION["ActionLog"]["Hosting"])) {
                                $_SESSION["ActionLog"]["Hosting"] = [];
                            }
                            $_SESSION["ActionLog"]["Hosting"]["delete"] = [];
                            foreach ($list_accounts as $h_id) {
                                $_SESSION["ActionLog"]["Hosting"]["delete"][] = $h_id;
                            }
                            if(isset($_GET["from_page"]) && !empty($_GET["from_page"])) {
                                $_SESSION["ActionLog"]["Hosting"]["from_page"] = esc($_GET["from_page"]);
                                $_SESSION["ActionLog"]["Hosting"]["from_id"] = esc($_GET["from_id"]);
                            }
                            if(!empty($_SESSION["ActionLog"]["Hosting"]["delete"])) {
                                header("location: ?page=delete&id=" . current($_SESSION["ActionLog"]["Hosting"]["delete"]));
                                exit;
                            }
                        }
                        break;
                    case "dialog:changeDebtorHosting":
                        if(!U_HOSTING_EDIT) {
                        } elseif(isset($_POST["Debtor"]) && 0 < $_POST["Debtor"]) {
                            if(isset($_POST["imsure"]) && $_POST["imsure"] == "yes") {
                                foreach ($list_accounts as $hosting_id) {
                                    $hosting = new hosting();
                                    $hosting->Identifier = $hosting_id;
                                    $hosting->changeDebtor(esc($_POST["Debtor"]));
                                    flashMessage($hosting);
                                    unset($hosting);
                                }
                            }
                        } else {
                            $error_class->Error[] = __("invalid debtor");
                        }
                        break;
                    case "dialog:terminate_hosting":
                        $error_messages = [];
                        $counters = ["already_done" => 0, "success" => 0];
                        foreach ($list_accounts as $h_id) {
                            $result = service_termination_batch_processing("hosting", esc($h_id), $_POST, $error_messages);
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
                $hosting = new hosting();
                $hosting->Warning[] = __("nothing selected");
            }
            if(isset($_GET["from_page"]) && !empty($_GET["from_page"])) {
                flashMessage($hosting);
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
            case "show":
                require_once "class/hosting.php";
                $hosting = isset($hosting) && is_object($hosting) ? $hosting : new hosting();
                if(!$hosting->show($hosting_id, false)) {
                    flashMessage($hosting);
                    header("Location: hosting.php");
                    exit;
                }
                if(0 < $hosting->PeriodicID && is_object($hosting->Periodic)) {
                    $hosting->Periodic->format();
                    $hosting->Periodic->showContractInfo();
                }
                require_once "class/debtor.php";
                $debtor = new debtor();
                $debtor->show($hosting->Debtor);
                require_once "class/logfile.php";
                $logfile = new logfile();
                $session = isset($_SESSION["hosting.show.logfile"]) ? $_SESSION["hosting.show.logfile"] : [];
                $fields = ["Date", "Who", "Name", "Action", "Values", "Translate"];
                $sort = isset($session["sort"]) ? $session["sort"] : "Date";
                $order = isset($session["order"]) ? $session["order"] : "DESC";
                $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) ? $session["limit"] : "1");
                $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : min(25, MAX_RESULTS_LIST));
                $list_hosting_logfile = $logfile->all($fields, $sort, $order, $limit, "hosting", $hosting_id, $show_results);
                $_SESSION["hosting.show.logfile"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "limit" => $limit];
                $current_page = $limit;
                require_once "class/template.php";
                $template = new template();
                $template_list = $template->all(["Name"], "Name", "ASC", "-1", "Type", "other");
                $emailtemplate = new emailtemplate();
                $emailtemplate_list = $emailtemplate->all(["Name"]);
                require_once "class/server.php";
                $server = new server();
                $server->show($hosting->Server);
                if(isset($hosting->Periodic) && $hosting->Periodic->ProductCode && $hosting->Periodic->ProductCode != "") {
                    require_once "class/product.php";
                    $product = new product();
                    $list_products = $product->all(["id", "ProductCode", "ProductName", "PackageID", "PriceExcl", "PricePeriod", "ProductKeyPhrase", "HasCustomPrice"], "ProductCode", false, "-1", "ProductType", "hosting");
                    require_once "class/upgradegroup.php";
                    $upgradegroup = new UpgradeGroup_Model("hosting");
                    $upgradegroup->showByProduct($hosting->Product);
                    require_once "class/package.php";
                    $package = new package();
                    $package_list = $package->all(["PackageName", "Product", "ProductCode"], "PackageName", "ASC", -1, "Server", $hosting->Server);
                    $packages_on_same_server = [];
                    foreach ($package_list as $k => $_package) {
                        if(is_numeric($k) && isset($_package["Product"]) && $_package["Product"]) {
                            $packages_on_same_server[$_package["Product"]] = $_package;
                        }
                    }
                    unset($package);
                    unset($package_list);
                }
                if(!isset($selected_tab) && isset($_SESSION["selected_tab"])) {
                    $selected_tab = $_SESSION["selected_tab"];
                    unset($_SESSION["selected_tab"]);
                }
                global $array_periodic;
                $array_controlpanels = $server->getAvailableControlPanels();
                if(U_INVOICE_SHOW && isset($hosting->PeriodicID) && 0 < $hosting->PeriodicID) {
                    require_once "class/invoice.php";
                    $invoice = new invoice();
                    $invoice_table_options = $invoice->getConfigInvoiceTable();
                }
                $message = parse_message($hosting, $template, $emailtemplate, $server);
                $current_page_url = "hosting.php?page=show&amp;id=" . $hosting_id;
                $sidebar_template = "service.sidebar.php";
                $is_service_terminated = service_is_terminated("hosting", $hosting_id);
                $wfh_page_title = __("hosting account") . " " . $hosting->Username;
                require_once "views/hosting.show.php";
                break;
            case "singlesignon":
                require_once "class/hosting.php";
                $hosting = isset($hosting) && is_object($hosting) ? $hosting : new hosting();
                if(!U_HOSTING_EDIT || !$hosting->show($hosting_id, false)) {
                    flashMessage($hosting);
                    header("Location: hosting.php");
                    exit;
                }
                $wfh_page_title = __("hosting account") . " " . $hosting->Username;
                require_once "views/hosting.singlesignon.php";
                break;
            default:
                unset($_SESSION["ActionLog"]["Hosting"]["forAll"]);
                require_once "class/hosting.php";
                $hosting = isset($hosting) && is_object($hosting) ? $hosting : new hosting();
                $session = isset($_SESSION["hosting.overview"]) ? $_SESSION["hosting.overview"] : [];
                $fields = ["Username", "Debtor", "CompanyName", "SurName", "Initials", "Domain", "Server", "Name", "Status", "Package", "PackageName", "PeriodicID", "PriceExcl", "Periodic", "NextDate", "StartPeriod", "TerminationDate", "AutoRenew", "TerminationID"];
                $sort = isset($session["sort"]) ? $session["sort"] : "Username";
                $order = isset($session["order"]) ? $session["order"] : "ASC";
                $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) && $session["limit"] ? $session["limit"] : "1");
                $searchat = isset($session["searchat"]) ? $session["searchat"] : "";
                $searchfor = isset($session["searchfor"]) ? $session["searchfor"] : "";
                $selectgroup = isset($session["status"]) ? $session["status"] : "-1|1|3|4|5|6|7|8";
                $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : MAX_RESULTS_LIST);
                $list_hosting_accounts = $hosting->all($fields, $sort, $order, $limit, $searchat, $searchfor, $selectgroup, $show_results);
                if(isset($list_hosting_accounts["CountRows"]) && ($list_hosting_accounts["CountRows"] < $show_results * ($limit - 1) || 1 < $limit && $list_hosting_accounts["CountRows"] == $show_results * ($limit - 1))) {
                    $newPage = ceil($list_hosting_accounts["CountRows"] / $show_results);
                    if($newPage <= 0) {
                        $newPage = 1;
                    }
                    $_SESSION["hosting.overview"]["limit"] = $newPage;
                    header("Location: hosting.php");
                    exit;
                }
                $_SESSION["hosting.overview"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "status" => $selectgroup, "searchat" => $searchat, "searchfor" => $searchfor, "limit" => $limit];
                $current_page = $limit;
                $message = parse_message($hosting);
                $wfh_page_title = __("hosting overview");
                $current_page_url = "hosting.php";
                $sidebar_template = "service.sidebar.php";
                require_once "views/hosting.overview.php";
        }
}

?>