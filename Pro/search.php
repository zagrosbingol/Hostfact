<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
include_once "connect.php";
$suffix = defined("DB_NAME") ? substr(md5(DB_NAME), 0, 8) : "";
$sessionPrefix = defined("SESSION_PREFIX") ? SESSION_PREFIX : "hfb";
session_name($sessionPrefix . $suffix);
$current_session_params = session_get_cookie_params();
$http_only = true;
!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off" or $secure_flag = !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off" || $_SERVER["SERVER_PORT"] == 443;
session_set_cookie_params($current_session_params["lifetime"], $current_session_params["path"], $current_session_params["domain"], $secure_flag, $http_only);
session_start();
if(isset($_POST["SearchInput"]) && $_POST["SearchInput"] != "") {
    $_SESSION["SearchInput"] = $_POST["SearchInput"];
} elseif(isset($_GET["search"]) && $_GET["search"] != "") {
    $_SESSION["SearchInput"] = urldecode($_GET["search"]);
}
if(isset($_SESSION["SearchInput"]) && $_SESSION["SearchInput"] != "") {
    $_SESSION["RefererURL"] = "search.php?search=" . urlencode($_SESSION["SearchInput"]);
}
require_once "config.php";
require_once "class/search.php";
$search = new search();
$page = isset($_GET["page"]) ? $_GET["page"] : "";
switch ($page) {
    case "show":
    case "autocomplete":
        $search->searchstring = trim(esc($_GET["term"]));
        $results = $search->getResults();
        if(!empty($results)) {
            echo json_encode($results);
        } else {
            echo json_encode([["label" => __("no autosuggest search results found"), "url" => "", "category" => "no results"]]);
        }
        break;
    default:
        $searchstring = "";
        $limit = isset($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : "1";
        $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : min(25, MAX_RESULTS_LIST));
        if(isset($_POST["SearchInput"]) && $_POST["SearchInput"] != "" || isset($_SESSION["SearchInput"]) && $_SESSION["SearchInput"] != "" || isset($_POST["ajaxPage"])) {
            $search->searchstring = trim(esc($_SESSION["SearchInput"]));
            $searchstring = esc($_SESSION["SearchInput"]);
            $searchresults["HostFact_Debtors"] = $search->getResults("HostFact_Debtors", isset($_SESSION["debtor.overview"]["sort"]) ? $_SESSION["debtor.overview"]["sort"] : "", isset($_SESSION["debtor.overview"]["order"]) ? $_SESSION["debtor.overview"]["order"] : "", $show_results, $limit);
            $searchresults["HostFact_Invoice"] = $search->getResults("HostFact_Invoice");
            $searchresults["HostFact_PriceQuote"] = $search->getResults("HostFact_PriceQuote", isset($_SESSION["pricequote.overview"]["sort"]) ? $_SESSION["pricequote.overview"]["sort"] : "", isset($_SESSION["pricequote.overview"]["order"]) ? $_SESSION["pricequote.overview"]["order"] : "", $show_results, $limit);
            $searchresults["HostFact_Domains"] = $search->getResults("HostFact_Domains", isset($_SESSION["domain.overview"]["sort"]) ? $_SESSION["domain.overview"]["sort"] : "", isset($_SESSION["domain.overview"]["order"]) ? $_SESSION["domain.overview"]["order"] : "", $show_results, $limit);
            $searchresults["HostFact_Hosting"] = $search->getResults("HostFact_Hosting", isset($_SESSION["hosting.overview"]["sort"]) ? $_SESSION["hosting.overview"]["sort"] : "", isset($_SESSION["hosting.overview"]["order"]) ? $_SESSION["hosting.overview"]["order"] : "", $show_results, $limit);
            $searchresults["HostFact_Other"] = $search->getResults("HostFact_Other", isset($_SESSION["other.overview"]["sort"]) ? $_SESSION["other.overview"]["sort"] : "", isset($_SESSION["other.overview"]["order"]) ? $_SESSION["other.overview"]["order"] : "", $show_results, $limit);
            $searchresults["HostFact_Creditors"] = $search->getResults("HostFact_Creditors", isset($_SESSION["creditor.overview"]["sort"]) ? $_SESSION["creditor.overview"]["sort"] : "", isset($_SESSION["creditor.overview"]["order"]) ? $_SESSION["creditor.overview"]["order"] : "", $show_results, $limit);
            $searchresults["HostFact_CreditInvoice"] = $search->getResults("HostFact_CreditInvoice", isset($_SESSION["creditinvoice.overview"]["sort"]) ? $_SESSION["creditinvoice.overview"]["sort"] : "", isset($_SESSION["creditinvoice.overview"]["order"]) ? $_SESSION["creditinvoice.overview"]["order"] : "", $show_results, $limit);
            $searchresults["HostFact_Products"] = $search->getResults("HostFact_Products", isset($_SESSION["product.overview"]["sort"]) ? $_SESSION["product.overview"]["sort"] : "", isset($_SESSION["product.overview"]["order"]) ? $_SESSION["product.overview"]["order"] : "", $show_results, $limit);
            $searchresults["HostFact_NewOrder"] = $search->getResults("HostFact_NewOrder", isset($_SESSION["order.overview"]["sort"]) ? $_SESSION["order.overview"]["sort"] : "", isset($_SESSION["order.overview"]["order"]) ? $_SESSION["order.overview"]["order"] : "", $show_results, $limit);
            $searchresults["HostFact_Tickets"] = $search->getResults("HostFact_Tickets", isset($_SESSION["ticket.overview"]["sort"]) ? $_SESSION["ticket.overview"]["sort"] : "", isset($_SESSION["ticket.overview"]["order"]) ? $_SESSION["ticket.overview"]["order"] : "", $show_results, $limit);
            if(!empty($additional_product_types)) {
                foreach ($additional_product_types as $tmp_product_type => $tmp_product_title) {
                    $searchresults[$tmp_product_type] = $search->getResults($tmp_product_type, "", "", $show_results, $limit);
                }
            }
            $total_results = 0;
            $redirect_url = "";
            foreach ($searchresults as $tmp_table => $tmp_value) {
                $total_results += $tmp_value["CountRows"];
            }
            if($total_results == 1) {
                foreach ($searchresults as $tmp_table => $tmp_value) {
                    if($tmp_value["CountRows"] == 1) {
                        $first_item = false;
                        foreach ($tmp_value as $k => $v) {
                            if(is_numeric($k)) {
                                $first_item = $v;
                                switch ($tmp_table) {
                                    case "HostFact_Debtors":
                                        $redirect_url = "debtors.php?page=show&id=" . $first_item["id"];
                                        break;
                                    case "HostFact_Invoice":
                                        $redirect_url = "invoices.php?page=show&id=" . $first_item["id"];
                                        break;
                                    case "HostFact_PriceQuote":
                                        $redirect_url = "pricequotes.php?page=show&id=" . $first_item["id"];
                                        break;
                                    case "HostFact_Domains":
                                        $redirect_url = "domains.php?page=show&id=" . $first_item["id"];
                                        break;
                                    case "HostFact_Hosting":
                                        $redirect_url = "hosting.php?page=show&id=" . $first_item["id"];
                                        break;
                                    case "HostFact_Other":
                                        $redirect_url = "services.php?page=show&id=" . $first_item["id"];
                                        break;
                                    case "HostFact_Creditors":
                                        $redirect_url = "creditors.php?page=show&id=" . $first_item["id"];
                                        break;
                                    case "HostFact_CreditInvoice":
                                        $redirect_url = "creditors.php?page=show_invoice&id=" . $first_item["id"];
                                        break;
                                    case "HostFact_Products":
                                        $redirect_url = "products.php?page=show&id=" . $first_item["id"];
                                        break;
                                    case "HostFact_NewOrder":
                                        $redirect_url = "orders.php?page=show&id=" . $first_item["id"];
                                        break;
                                    case "HostFact_Tickets":
                                        $redirect_url = "tickets.php?page=show&id=" . $first_item["id"];
                                        break;
                                    default:
                                        if(array_key_exists($tmp_table, $additional_product_types)) {
                                            $redirect_url = "modules.php?module=" . $tmp_table . "&page=show&id=" . $first_item["id"];
                                            if($redirect_url) {
                                                header("Location: " . $redirect_url);
                                                exit;
                                            }
                                        }
                                }
                            }
                        }
                    } elseif(1 < $total_results) {
                        $redirect_url = "";
                    }
                }
            }
        }
        $results_per_page = $show_results;
        $current_page = $limit;
        if(!isset($selected_tab) && isset($_SESSION["selected_tab"])) {
            $selected_tab = $_SESSION["selected_tab"];
            unset($_SESSION["selected_tab"]);
        }
        $message = parse_message($search);
        $current_page_url = "search.php";
        $wfh_page_title = __("searchresults");
        $sidebar_template = "search.sidebar.php";
        require_once "views/search.overview.php";
}

?>