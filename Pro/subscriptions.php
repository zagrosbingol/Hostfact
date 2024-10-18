<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "config.php";
$page = isset($_GET["page"]) ? $_GET["page"] : "";
$pagetype = "add";
$service_id = isset($_GET["id"]) ? intval(esc($_GET["id"])) : (isset($_POST["id"]) ? intval(esc($_POST["id"])) : NULL);
if(!U_DOMAIN_SHOW && !U_HOSTING_SHOW && !U_SERVICE_SHOW) {
    checkRight(false);
}
switch ($page) {
    case "delete":
        $periodicType = "";
        $reference = "";
        if(0 < $service_id) {
            require_once "class/periodic.php";
            $subscription = new periodic();
            if($subscription->show($service_id)) {
                $periodicType = $subscription->PeriodicType;
                if($periodicType == "domain" || $periodicType == "hosting") {
                    $reference = $subscription->Reference;
                } else {
                    $reference = $service_id;
                }
            }
        } elseif(!empty($_SESSION["ActionLog"]["Subscriptions"]["type"])) {
            $periodicType = key($_SESSION["ActionLog"]["Subscriptions"]["type"]);
            $reference = current($_SESSION["ActionLog"]["Subscriptions"]["type"][$periodicType]["delete"]);
        }
        if($periodicType != "" && $reference != "") {
            switch ($periodicType) {
                case "domain":
                    header("location: domains.php?page=delete&id=" . $reference);
                    exit;
                    break;
                case "hosting":
                    header("location: hosting.php?page=delete&id=" . $reference);
                    exit;
                    break;
                default:
                    if($periodicType != "other" && isset($_module_instances[$periodicType])) {
                        header("Location: modules.php?module=" . $periodicType . "&page=delete&id=" . $reference);
                        exit;
                    }
                    header("location: services.php?page=delete&id=" . $reference);
                    exit;
            }
        } else {
            $error_class->Success[] = __("one or more subscriptions are deleted");
            flashMessage();
            if(isset($_SESSION["ActionLog"]["Subscriptions"]["from_page"]) && !empty($_SESSION["ActionLog"]["Subscriptions"]["from_page"])) {
                $from_page = $_SESSION["ActionLog"]["Subscriptions"]["from_page"];
                $from_id = $_SESSION["ActionLog"]["Subscriptions"]["from_id"];
                unset($_SESSION["ActionLog"]["Subscriptions"]["from_page"]);
                switch ($from_page) {
                    case "debtor":
                        $_SESSION["selected_tab"] = 3;
                        header("Location: debtors.php?page=show&id=" . intval($from_id));
                        exit;
                        break;
                }
            }
            if(isset($_SESSION["ActionLog"]["Subscriptions"]["sidebar_template"]) && $_SESSION["ActionLog"]["Subscriptions"]["sidebar_template"] == "services") {
                header("Location: subscriptions.php?sidebar=services");
                exit;
            }
            header("Location: subscriptions.php");
            exit;
        }
        break;
    default:
        if(isset($_SESSION["ActionLog"]["Subscriptions"]["type"]) && is_array($_SESSION["ActionLog"]["Subscriptions"]["type"])) {
            unset($_SESSION["ActionLog"]["Subscriptions"]["type"]);
        }
        if(isset($_POST["action"])) {
            require_once "class/periodic.php";
            $list_subscriptions = isset($_POST["id"]) && is_array($_POST["id"]) ? $_POST["id"] : [];
            if(!empty($_POST["id"])) {
                switch ($_POST["action"]) {
                    case "delete":
                        if(!isset($_SESSION["ActionLog"]["Subscriptions"])) {
                            $_SESSION["ActionLog"]["Subscriptions"] = [];
                        }
                        $_SESSION["ActionLog"]["Subscriptions"]["type"] = [];
                        foreach ($list_subscriptions as $d_id) {
                            $subscription = new periodic();
                            $subscription->show($d_id);
                            if($subscription->PeriodicType == "other") {
                                $_SESSION["ActionLog"]["Subscriptions"]["type"]["services"]["delete"][] = $d_id;
                            } else {
                                $_SESSION["ActionLog"]["Subscriptions"]["type"][$subscription->PeriodicType]["delete"][] = $subscription->Reference;
                            }
                            unset($subscription);
                        }
                        if(isset($_GET["from_page"]) && !empty($_GET["from_page"])) {
                            $_SESSION["ActionLog"]["Subscriptions"]["from_page"] = esc($_GET["from_page"]);
                            $_SESSION["ActionLog"]["Subscriptions"]["from_id"] = esc($_GET["from_id"]);
                        }
                        if(isset($_GET["sidebar"]) && $_GET["sidebar"] == "services") {
                            $_SESSION["ActionLog"]["Subscriptions"]["sidebar_template"] = "services";
                        } else {
                            unset($_SESSION["ActionLog"]["Subscriptions"]["sidebar_template"]);
                        }
                        if(!empty($_SESSION["ActionLog"]["Subscriptions"]["type"])) {
                            $firstType = key($_SESSION["ActionLog"]["Subscriptions"]["type"]);
                            switch ($firstType) {
                                case "domain":
                                    header("location: domains.php?page=delete&id=" . current($_SESSION["ActionLog"]["Subscriptions"]["type"]["domain"]["delete"]));
                                    exit;
                                    break;
                                case "hosting":
                                    header("location: hosting.php?page=delete&id=" . current($_SESSION["ActionLog"]["Subscriptions"]["type"]["hosting"]["delete"]));
                                    exit;
                                    break;
                                default:
                                    if($firstType != "other" && isset($_module_instances[$firstType])) {
                                        header("Location: modules.php?module=" . $firstType . "&page=delete&id=" . current($_SESSION["ActionLog"]["Subscriptions"]["type"][$firstType]["delete"]));
                                        exit;
                                    }
                                    header("location: services.php?page=delete&id=" . current($_SESSION["ActionLog"]["Subscriptions"]["type"]["services"]["delete"]));
                                    exit;
                            }
                        }
                        break;
                    case "dialog:submakeinvoice":
                        $subscription = new periodic();
                        $subscription->makeinvoice_manual($list_subscriptions);
                        break;
                }
            } elseif(isset($_POST["action"])) {
                $subscription->Warning[] = __("nothing selected");
            }
            flashMessage($subscription);
            if(isset($_GET["from_page"]) && !empty($_GET["from_page"])) {
                $from_page = esc($_GET["from_page"]);
                $from_id = esc($_GET["from_id"]);
                switch ($from_page) {
                    case "debtor":
                        $_SESSION["selected_tab"] = 3;
                        header("Location: debtors.php?page=show&id=" . intval($from_id));
                        exit;
                        break;
                }
            }
            if(isset($_GET["sidebar"])) {
                header("Location: subscriptions.php?sidebar=services");
                exit;
            }
            header("Location: subscriptions.php");
            exit;
        }
        require_once "class/periodic.php";
        $subscription = isset($subscription) && is_object($subscription) ? $subscription : new periodic();
        if(!U_SERVICE_SHOW || !U_DOMAIN_SHOW || !U_HOSTING_SHOW) {
            $subscription->restrictedAll = [];
            if(!U_SERVICE_SHOW) {
                $subscription->restrictedAll[] = "other";
            }
            if(!U_DOMAIN_SHOW) {
                $subscription->restrictedAll[] = "domain";
            }
            if(!U_HOSTING_SHOW) {
                $subscription->restrictedAll[] = "hosting";
            }
        }
        $session = isset($_SESSION["subscription.overview"]) ? $_SESSION["subscription.overview"] : [];
        $fields = ["Description", "Number", "NumberSuffix", "PriceExcl", "TaxPercentage", "NextDate", "ProductName", "Debtor", "CompanyName", "SurName", "Initials", "Periods", "Periodic", "StartPeriod", "EndPeriod", "Status", "TerminationDate", "PeriodicType", "Reference", "InvoiceAuthorisation", "DebtorInvoiceAuthorisation", "AutoRenew"];
        $sort = isset($session["sort"]) ? $session["sort"] : "NextDate";
        $order = isset($session["order"]) ? $session["order"] : "ASC";
        $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) ? $session["limit"] : "1");
        $selectgroup = isset($session["status"]) ? $session["status"] : "active";
        $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : MAX_RESULTS_LIST);
        $list_subscriptions = $subscription->all($fields, $sort, $order, $limit, false, false, $selectgroup, false, $show_results);
        if(isset($list_subscriptions["CountRows"]) && ($list_subscriptions["CountRows"] < $show_results * ($limit - 1) || 1 < $limit && $list_subscriptions["CountRows"] == $show_results * ($limit - 1))) {
            $newPage = ceil($list_subscriptions["CountRows"] / $show_results);
            if($newPage <= 0) {
                $newPage = 1;
            }
            $_SESSION["subscription.overview"]["limit"] = $newPage;
            header("Location: subscriptions.php");
            exit;
        }
        $_SESSION["subscription.overview"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "status" => $selectgroup, "limit" => $limit];
        $current_page = $limit;
        $message = parse_message($subscription);
        if(isset($_GET["sidebar"]) && $_GET["sidebar"] == "services") {
            $current_page_url = "subscriptions.php?sidebar=services";
            $sidebar_template = "service.sidebar.php";
        } else {
            $current_page_url = "subscriptions.php";
            $sidebar_template = "invoice.sidebar.php";
        }
        $wfh_page_title = __("subscription overview") . " (" . $list_subscriptions["CountRows"] . ")";
        require_once "views/subscription.overview.php";
}

?>