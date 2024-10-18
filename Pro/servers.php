<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "config.php";
checkRight(U_SERVICEMANAGEMENT_SHOW);
$page = isset($_GET["page"]) ? $_GET["page"] : "";
$server_id = isset($_GET["id"]) ? intval(esc($_GET["id"])) : (isset($_POST["id"]) ? intval(esc($_POST["id"])) : NULL);
if(isset($_GET["debug"])) {
    if($_GET["debug"] == "true") {
        $_SESSION["server_debug"]["flag"] = true;
    } elseif($_GET["debug"] == "show") {
        if(isset($_SESSION["server_debug"]["data"]) && is_array($_SESSION["server_debug"]["data"]) && U_SERVICEMANAGEMENT_EDIT) {
            foreach ($_SESSION["server_debug"]["data"] as $_debug) {
                var_dump($_debug);
            }
        }
        exit;
    } else {
        $_SESSION["server_debug"]["flag"] = false;
        unset($_SESSION["server_debug"]["data"]);
    }
}
switch ($page) {
    case "show":
        if(isset($_POST["action"]) && $_POST["action"]) {
            require_once "class/server.php";
            $server = new server();
            if(isset($_POST["action"]) && $_POST["action"] == "removeConnectedDebtor" && U_SERVICEMANAGEMENT_EDIT) {
                $list_debtors = is_array($_POST["debtor"]) ? $_POST["debtor"] : [];
                $server->deconnectDebtors($list_debtors);
                $_SESSION["selected_tab"] = 2;
            }
            if(isset($_POST["action"]) && $_POST["action"] == "removeConnectedClientID" && U_SERVICEMANAGEMENT_EDIT) {
                $list_clients = is_array($_POST["clients"]) ? $_POST["clients"] : [];
                $server->deconnectClientIDS($server_id, $list_clients);
            }
            if(isset($_POST["action"]) && $_POST["action"] == "ConnectClientID" && U_SERVICEMANAGEMENT_EDIT) {
                $server->connectClientIDS($server_id, esc($_POST["Debtor"]), esc($_POST["ClientID"]));
            }
            flashMessage($server);
            header("Location: servers.php?page=show&id=" . $server_id);
            exit;
        }
        if(isset($_GET["action"]) && $_GET["action"] == "mailing") {
            $list_servers = [$server_id];
            require_once "class/hosting.php";
            $hosting = new hosting();
            $hostinglist = $hosting->all(["Debtor", "Server"]);
            $debtor_filter = $add = [];
            foreach ($hostinglist as $hID) {
                if(isset($hID["Debtor"]) && 0 < $hID["Debtor"] && in_array($hID["Server"], $list_servers)) {
                    $debtor_filter[] = $hID["Debtor"];
                }
            }
            require_once "class/debtor.php";
            $debtor = new debtor();
            $debtors = $debtor->all(["DebtorCode"]);
            foreach ($debtors as $k => $v) {
                if(in_array($k, $debtor_filter)) {
                    $add[] = $k;
                }
            }
            $_SESSION["mailing.add"]["Recipients"] = array_unique($add);
            header("Location: debtors.php?page=mailing");
            exit;
        }
        break;
    case "add":
        $pagetype = "add";
        if(empty($_POST) || !U_SERVICEMANAGEMENT_EDIT) {
        } else {
            require_once "class/server.php";
            $server = new server();
            foreach ($_POST as $key => $value) {
                if(in_array($key, $server->Variables)) {
                    $server->{$key} = esc($value);
                }
            }
            $server->Location = trim($server->Location);
            $server->Port = preg_replace("/[^0-9]/i", "", $server->Port);
            if($server->Location && preg_match("|^(http(s)?://)|", $server->Location) == 0) {
                $server->Location = "http://" . $server->Location;
            }
            if($server->Panel) {
                $server->Password = esc($_POST["ServerPassword"]);
                if(file_exists("3rdparty/hosting/" . $server->Panel . "/version.php")) {
                    $version = [];
                    include "3rdparty/hosting/" . $server->Panel . "/version.php";
                    if(isset($version["password_type"]) && $version["password_type"] == "textarea") {
                        $server->Password = esc($_POST["ServerKey"]);
                    }
                }
            }
            if(is_module_active("dnsmanagement")) {
                global $_module_instances;
                $dnsmanagement = $_module_instances["dnsmanagement"];
                $server = $dnsmanagement->before_integration_is_added($server);
            }
            if($server->add()) {
                $server_id = $server->Identifier;
                flashMessage($server);
                header("Location: servers.php?page=show&id=" . $server_id);
                exit;
            }
            foreach ($server->Variables as $key) {
                if(is_string($server->{$key})) {
                    $server->{$key} = htmlspecialchars($server->{$key});
                }
            }
        }
        break;
    case "edit":
        $pagetype = "edit";
        $page = "add";
        if(empty($_POST) || !U_SERVICEMANAGEMENT_EDIT) {
        } else {
            require_once "class/server.php";
            $server = new server();
            $server->Identifier = $server_id;
            $server->show();
            foreach ($_POST as $key => $value) {
                if(in_array($key, $server->Variables)) {
                    $server->{$key} = esc($value);
                }
            }
            $server->Location = trim($server->Location);
            $server->Port = preg_replace("/[^0-9]/i", "", $server->Port);
            if($server->Location && preg_match("|^(http(s)?://)|", $server->Location) == 0) {
                $server->Location = "http://" . $server->Location;
            }
            if($server->Panel) {
                if(esc($_POST["ServerPassword"])) {
                    $server->Password = esc($_POST["ServerPassword"]);
                }
                if(file_exists("3rdparty/hosting/" . $server->Panel . "/version.php")) {
                    $version = [];
                    include "3rdparty/hosting/" . $server->Panel . "/version.php";
                    if(isset($version["password_type"]) && $version["password_type"] == "textarea") {
                        $server->Password = esc($_POST["ServerKey"]);
                    }
                }
            }
            if(is_module_active("dnsmanagement")) {
                global $_module_instances;
                $dnsmanagement = $_module_instances["dnsmanagement"];
                $server = $dnsmanagement->before_integration_is_added($server);
            }
            if($server->edit($server_id)) {
                flashMessage($server);
                header("Location: servers.php?page=show&id=" . $server_id);
                exit;
            }
            foreach ($server->Variables as $key) {
                if(is_string($server->{$key})) {
                    $server->{$key} = htmlspecialchars($server->{$key});
                }
            }
            $error = true;
        }
        break;
    case "delete":
        $pagetype = "confirmDelete";
        if($server_id != NULL) {
            $page = "show";
        } else {
            $page = "overview";
        }
        if(empty($_POST) || !U_SERVICEMANAGEMENT_DELETE) {
        } elseif(isset($_POST["imsure"]) && $_POST["imsure"] == "yes") {
            require_once "class/server.php";
            $server = new server();
            $result = $server->delete($server_id);
            if($result) {
                $server_id = NULL;
                $page = "overview";
            }
            flashMessage($server);
            header("Location: servers.php");
            exit;
        }
        break;
    case "importAccount":
        $list_accounts = unserialize($_SESSION["Server_" . $server_id . "_import_response"]);
        $page = "importAccounts";
        if(!isset($_POST["Debtor"])) {
        } elseif(empty($_POST["Debtor"])) {
            $error_class->Error[] = __("no debtor selected, unable to import account");
        } else {
            if(!isset($_POST["Subscription"]) || $_POST["Subscription"] != "yes") {
                unset($_POST["Description"]);
                unset($_POST["Product"]);
                unset($_POST["PriceExcl"]);
                unset($_POST["Periods"]);
                unset($_POST["Periodic"]);
                unset($_POST["StartPeriod"]);
                unset($_POST["EndPeriod"]);
            }
            require_once "class/server.php";
            $server = new server();
            $i = esc($_POST["account_id"]);
            require_once "class/hosting.php";
            $hosting = new hosting();
            $hosting->Debtor = esc($_POST["Debtor"]);
            $hosting->Product = isset($_POST["Product"]) ? esc($_POST["Product"]) : 0;
            $hosting->Package = esc($_POST["Package"]);
            $hosting->Username = $list_accounts[$i]["Username"];
            $hosting->Password = isset($list_accounts[$i]["Password"]) && $list_accounts[$i]["Password"] ? $list_accounts[$i]["Password"] : "onbekend";
            $hosting->Domain = $list_accounts[$i]["Domain"];
            $hosting->Status = isset($list_accounts[$i]["Suspended"]) && $list_accounts[$i]["Suspended"] == "yes" ? 5 : 4;
            $hosting->Server = $server_id;
            if(isset($list_accounts[$i]["PleskClientID"]) && 0 < $list_accounts[$i]["PleskClientID"] && !$server->checkSameClientID($hosting->Server, $hosting->Debtor, $list_accounts[$i]["PleskClientID"])) {
                require_once "class/debtor.php";
                $debtor = new debtor();
                $debtor->Identifier = $hosting->Debtor;
                $debtor->show();
                $hosting->Error[] = sprintf(__("plesk hosting account import fails because of another client id"), $hosting->Username, $debtor->DebtorCode);
            }
            if($hosting->add()) {
                $hosting->Success[] = sprintf(__("account imported, but not billable"), $hosting->Username);
                $search_for_domain = true;
                if(isset($_POST["Subscription"]) && $_POST["Subscription"] == "yes") {
                    require_once "class/product.php";
                    $product = new parent();
                    $product->show($hosting->Product);
                    require_once "class/periodic.php";
                    $subscription = new periodic();
                    $subscription->ProductCode = htmlspecialchars_decode($product->ProductCode);
                    $subscription->Debtor = $hosting->Debtor;
                    $subscription->Description = esc($_POST["Description"]);
                    $subscription->PeriodicType = "hosting";
                    $subscription->Reference = $hosting->Identifier;
                    $subscription->StartPeriod = esc($_POST["StartPeriod"]);
                    $subscription->EndContract = esc($_POST["StartPeriod"]);
                    $subscription->Periods = esc($_POST["Periods"]);
                    $subscription->Periodic = esc($_POST["Periodic"]);
                    $subscription->PriceExcl = esc($_POST["PriceExcl"]);
                    $subscription->TaxPercentage = $product->TaxPercentage ? btwcheck($hosting->Debtor, $product->TaxPercentage) : $subscription->TaxPercentage;
                    $subscription->Number = 1;
                    if($subscription->add()) {
                        $hosting->Success = [sprintf(__("account imported and billable"), $hosting->Username)];
                        $subscription->changeReference($subscription->Identifier, "hosting", $hosting->Identifier);
                    } else {
                        $hosting->deleteFromDatabase($hosting->Identifier);
                        $hosting->Success = [];
                        $search_for_domain = false;
                    }
                    if(isset($subscription) && is_object($subscription)) {
                        $subscription->Success = [];
                        flashMessage($subscription);
                        unset($subscription);
                    }
                }
                if($search_for_domain) {
                    $hosting->connectDomainToHosting($hosting->Identifier, $hosting->Debtor, $hosting->Domain);
                }
                $account_info = ["id" => $hosting->Identifier, "Debtor" => $hosting->Debtor, "Username" => $hosting->Username, "Domain" => $hosting->Domain];
                do_action("hosting_account_is_imported", $account_info);
                if(isset($list_accounts[$i]["PleskClientID"]) && 0 < $list_accounts[$i]["PleskClientID"]) {
                    $server->connectClientIDS($server_id, esc($_POST["Debtor"]), esc($list_accounts[$i]["PleskClientID"]));
                }
            }
            flashMessage($hosting);
            unset($hosting);
            if(isset($subscription) && is_object($subscription)) {
                flashMessage($subscription);
                unset($subscription);
            }
        }
        break;
    default:
        if(isset($_POST["action"]) && $_POST["action"] == "mailing") {
            $list_servers = is_array($_POST["servers"]) ? $_POST["servers"] : [];
            require_once "class/hosting.php";
            $hosting = new hosting();
            $hostinglist = $hosting->all(["Debtor", "Server"]);
            $debtor_filter = $add = [];
            foreach ($hostinglist as $hID) {
                if(isset($hID["Debtor"]) && 0 < $hID["Debtor"] && in_array($hID["Server"], $list_servers)) {
                    $debtor_filter[] = $hID["Debtor"];
                }
            }
            require_once "class/debtor.php";
            $debtor = new debtor();
            $debtors = $debtor->all(["DebtorCode"]);
            foreach ($debtors as $k => $v) {
                if(in_array($k, $debtor_filter)) {
                    $add[] = $k;
                }
            }
            $_SESSION["mailing.add"]["Recipients"] = array_unique($add);
            header("Location: debtors.php?page=mailing");
            exit;
        } else {
            switch ($page) {
                case "show":
                    require_once "class/server.php";
                    $server = isset($server) && is_object($server) ? $server : new server();
                    $server->show($server_id);
                    require_once "class/package.php";
                    $package = new package();
                    $session = isset($_SESSION["server.show.packages"]) ? $_SESSION["server.show.packages"] : [];
                    $fields = ["PackageName", "PackageType", "Product", "ProductName", "ProductCode", "Template", "TemplateName"];
                    $sort = isset($session["sort"]) ? $session["sort"] : "PackageName";
                    $order = isset($session["order"]) ? $session["order"] : "ASC";
                    $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) ? $session["limit"] : "1");
                    $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : min(25, MAX_RESULTS_LIST));
                    $list_hosting_packages = $package->all($fields, $sort, $order, $limit, "Server", $server_id, false, $show_results);
                    $_SESSION["server.show.packages"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "limit" => $limit];
                    $current_page = $limit;
                    require_once "class/hosting.php";
                    $hosting = new hosting();
                    $session = isset($_SESSION["server.show.accounts"]) ? $_SESSION["server.show.accounts"] : [];
                    $fields = ["Username", "Debtor", "CompanyName", "SurName", "Initials", "Domain", "Status", "Package", "PackageName", "Server", "PeriodicID", "PriceExcl", "Periodic", "NextDate", "StartPeriod", "TerminationDate", "AutoRenew", "TerminationID"];
                    $sort = isset($session["sort"]) ? $session["sort"] : "Username";
                    $order = isset($session["order"]) ? $session["order"] : "ASC";
                    $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) ? $session["limit"] : "1");
                    $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : min(25, MAX_RESULTS_LIST));
                    $list_hosting_accounts = $hosting->all($fields, $sort, $order, $limit, "Server", $server_id, false, $show_results);
                    $_SESSION["server.show.accounts"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "limit" => $limit];
                    $current_page = $limit;
                    require_once "class/debtor.php";
                    $debtor = new debtor();
                    $session = isset($_SESSION["server.show.debtors"]) ? $_SESSION["server.show.debtors"] : [];
                    $fields = ["DebtorCode", "CompanyName", "Initials", "SurName", "EmailAddress"];
                    $sort = isset($session["sort"]) ? $session["sort"] : "DebtorCode";
                    $order = isset($session["order"]) ? $session["order"] : "ASC";
                    $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) ? $session["limit"] : "1");
                    $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : min(25, MAX_RESULTS_LIST));
                    $list_hosting_debtors = $debtor->all($fields, $sort, $order, $limit, "Server", $server_id, false, $show_results);
                    $_SESSION["server.show.debtors"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "limit" => $limit];
                    $current_page = $limit;
                    if(substr($server->Panel, 0, 5) == "plesk") {
                        $session = isset($_SESSION["server.show.plesk"]) ? $_SESSION["server.show.plesk"] : [];
                        $sort = isset($session["sort"]) ? $session["sort"] : "ClientID";
                        $order = isset($session["order"]) ? $session["order"] : "ASC";
                        $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) ? $session["limit"] : "1");
                        $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : min(25, MAX_RESULTS_LIST));
                        $list_server_clientids = $server->getClientIDS($sort, $order, $limit, $show_results);
                        $_SESSION["server.show.plesk"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "limit" => $limit];
                        $current_page = $limit;
                    }
                    if(!isset($selected_tab) && isset($_SESSION["selected_tab"])) {
                        $selected_tab = $_SESSION["selected_tab"];
                        unset($_SESSION["selected_tab"]);
                    }
                    $message = parse_message($server, $package, $hosting, $debtor);
                    $wfh_page_title = __("server") . " " . $server->Name;
                    $current_page_url = "servers.php?page=show&id=" . $server_id;
                    $sidebar_template = "servicemanagement.sidebar.php";
                    require_once "views/server.show.php";
                    break;
                case "add":
                    checkRight(U_SERVICEMANAGEMENT_EDIT);
                    require_once "class/server.php";
                    $server = isset($server) && is_object($server) ? $server : new server();
                    if($pagetype == "edit" && (!isset($error) || $error === false)) {
                        $server->show($server_id);
                    } else {
                        $api_list = $server->getControlPanels();
                        if(isset($_GET["module"]) && isset($api_list[$_GET["module"]])) {
                            $server->Name = $api_list[$_GET["module"]]["name"];
                            $server->Panel = esc($_GET["module"]);
                        }
                    }
                    $message = parse_message($server);
                    $wfh_page_title = $pagetype == "edit" ? __("edit server") : __("add server");
                    $current_page_url = "servers.php";
                    $sidebar_template = "servicemanagement.sidebar.php";
                    require_once "views/server.add.php";
                    break;
                case "importAccounts":
                    checkRight(U_SERVICEMANAGEMENT_EDIT);
                    require_once "class/server.php";
                    $server = isset($server) && is_object($server) ? $server : new server();
                    $server->show($server_id);
                    if(isset($_SESSION["Server_" . $server_id . "_import_response"]) && count(unserialize($_SESSION["Server_" . $server_id . "_import_response"])) === 0) {
                        unset($_SESSION["Server_" . $server_id . "_import_response"]);
                    }
                    if(!isset($_SESSION["Server_" . $server_id . "_import_response"]) || isset($_GET["action"]) && $_GET["action"] == "refresh") {
                        $api = $server->connect();
                        if(is_object($api)) {
                            $list_accounts = $api->listAccounts();
                            $_SESSION["Server_" . $server_id . "_import_response"] = serialize($list_accounts);
                        }
                    } else {
                        $list_accounts = unserialize($_SESSION["Server_" . $server_id . "_import_response"]);
                    }
                    require_once "class/hosting.php";
                    $hosting = new hosting();
                    $fields = ["Username", "Domain", "Package", "Debtor", "CompanyName", "SurName", "Initials", "Server", "Name", "Status"];
                    $fulllist_hostfact_account = $hosting->all($fields);
                    $temp_hostfact_accounts = [];
                    foreach ($fulllist_hostfact_account as $k => $value) {
                        if($k != "CountRows") {
                            $temp_hostfact_accounts[] = $value["Username"];
                            $fulllist_hostfact_account[$value["Username"]] = $value;
                            unset($fulllist_hostfact_account[$k]);
                        }
                    }
                    $list_hostfact_accounts = $temp_hostfact_accounts;
                    $hosting = new hosting();
                    $list_server_accounts = $hosting->all($fields, "Username", "ASC", -1, "Server", $server_id);
                    $temp_server_accounts = [];
                    foreach ($list_server_accounts as $k => $value) {
                        if($k != "CountRows") {
                            $temp_server_accounts[] = $value["Username"];
                        }
                    }
                    $list_server_accounts = $temp_server_accounts;
                    if($list_accounts && is_array($list_accounts)) {
                        $collision_warning = [];
                        foreach ($list_accounts as $accountID => $accountObject) {
                            if(!in_array($accountObject["Username"], $list_server_accounts, true) && in_array($accountObject["Username"], $list_hostfact_accounts)) {
                                $hostfact_account = $fulllist_hostfact_account[$accountObject["Username"]];
                                if(in_array($hostfact_account["Status"], [4, 5])) {
                                    $collision_warning[$hostfact_account["Server"]]["Name"] = $hostfact_account["Name"];
                                    $collision_warning[$hostfact_account["Server"]]["Accounts"][$hostfact_account["id"]] = $hostfact_account;
                                }
                            }
                        }
                        if(0 < count($collision_warning)) {
                            $warning_text = __("hosting accounts already imported, but on a different server");
                            foreach ($collision_warning as $_server_id => $_server_info) {
                                $warning_text .= "<br />" . __("server") . " \"" . $_server_info["Name"] . "\"<br />";
                                foreach ($_server_info["Accounts"] as $_hosting_id => $_hosting_info) {
                                    $warning_text .= "- [hyperlink_1]hosting.php?page=show&amp;id=" . $_hosting_info["id"] . "[hyperlink_2]" . $_hosting_info["Username"] . " (" . $_hosting_info["Domain"] . ")[hyperlink_3]<br />";
                                }
                            }
                            $hosting->Warning[] = $warning_text;
                        }
                    }
                    require_once "class/debtor.php";
                    $debtor = new debtor();
                    $list_debtors = $debtor->all_small();
                    require_once "class/package.php";
                    $package = new package();
                    $fields = ["PackageName", "TemplateName"];
                    $list_hosting_packages = $package->all($fields, "PackageName", "ASC", -1, "Server", $server_id);
                    require_once "class/product.php";
                    $product = new product();
                    $fields = ["ProductCode", "ProductName", "ProductType", "PriceExcl", "Sold", "PricePeriod", "Groups"];
                    $list_products = $product->all($fields, "ProductCode", "ASC", -1, "ProductType", "hosting");
                    class batchMessages
                    {
                    }
                    $batch = new batchMessages();
                    $batch->Error = [];
                    $batch->Warning = [];
                    if(isset($_SESSION["batchMessages"])) {
                        $batch->Warning = $_SESSION["batchMessages"]["Warning"];
                        $batch->Success = $_SESSION["batchMessages"]["Success"];
                    }
                    unset($_SESSION["batchMessages"]);
                    $message = parse_message($hosting, $server, $debtor, $batch);
                    $wfh_page_title = __("import accounts from") . " " . $server->Name;
                    $current_page_url = "servers.php?page=importAccounts&id=" . $server_id;
                    $sidebar_template = "servicemanagement.sidebar.php";
                    require_once "views/server.import.account.php";
                    break;
                default:
                    require_once "class/server.php";
                    $server = isset($server) && is_object($server) ? $server : new server();
                    $session = isset($_SESSION["server.overview"]) ? $_SESSION["server.overview"] : [];
                    $fields = ["Name", "OS", "Panel", "Location", "Port", "IP", "Status"];
                    $sort = isset($session["sort"]) ? $session["sort"] : "Name";
                    $order = isset($session["order"]) ? $session["order"] : "ASC";
                    $limit = isset($_GET["p"]) ? $_GET["p"] : (isset($session["limit"]) ? $session["limit"] : "1");
                    $searchat = isset($session["searchat"]) ? $session["searchat"] : "Name";
                    $searchfor = isset($session["searchfor"]) ? $session["searchfor"] : "";
                    $selectgroup = isset($session["group"]) ? $session["group"] : "";
                    $show_results = isset($session["results"]) ? $session["results"] : 10;
                    $list_servers = $server->all($fields, $sort, $order, $limit, $searchat, $searchfor, $selectgroup, $show_results);
                    if(isset($list_servers["CountRows"]) && ($list_servers["CountRows"] < $show_results * ($limit - 1) || 1 < $limit && $list_servers["CountRows"] == $show_results * ($limit - 1))) {
                        $newPage = ceil($list_servers["CountRows"] / $show_results);
                        if($newPage <= 0) {
                            $newPage = 1;
                        }
                        header("Location: servers.php?p=" . $newPage);
                        exit;
                    }
                    $_SESSION["server.overview"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "group" => $selectgroup, "searchat" => $searchat, "searchfor" => $searchfor, "limit" => $limit];
                    $current_page = $limit;
                    $message = parse_message($server);
                    $wfh_page_title = __("server overview");
                    $current_page_url = "servers.php";
                    $sidebar_template = "servicemanagement.sidebar.php";
                    require_once "views/server.overview.php";
                    if(isset($_SESSION["wf_cache_controlpanels"]) && is_array($_SESSION["wf_cache_controlpanels"])) {
                        unset($_SESSION["wf_cache_controlpanels"]);
                    }
            }
        }
}

?>