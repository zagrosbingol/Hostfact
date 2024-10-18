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
$tld_id = isset($_GET["id"]) ? intval(esc($_GET["id"])) : (isset($_POST["id"]) ? intval(esc($_POST["id"])) : NULL);
switch ($page) {
    case "add":
        $pagetype = "add";
        if(empty($_POST) || !U_SERVICEMANAGEMENT_EDIT) {
        } else {
            require_once "class/topleveldomain.php";
            $topleveldomain = new topleveldomain();
            foreach ($_POST as $key => $value) {
                if(in_array($key, $topleveldomain->Variables)) {
                    $topleveldomain->{$key} = esc($value);
                }
            }
            if(!isset($_POST["IDNsupport_helper"]) || $_POST["IDNsupport_helper"] != "yes") {
                $topleveldomain->AllowedIDNCharacters = "";
            }
            if($topleveldomain->add()) {
                flashMessage($topleveldomain);
                header("Location: topleveldomains.php?page=show&id=" . $topleveldomain->Identifier);
                exit;
            }
            foreach ($topleveldomain->Variables as $key) {
                $topleveldomain->{$key} = htmlspecialchars($topleveldomain->{$key});
            }
        }
        break;
    case "edit":
        $page = "add";
        $pagetype = "edit";
        if(empty($_POST) || !U_SERVICEMANAGEMENT_EDIT) {
        } else {
            require_once "class/topleveldomain.php";
            $topleveldomain = new topleveldomain();
            foreach ($_POST as $key => $value) {
                if(in_array($key, $topleveldomain->Variables)) {
                    $topleveldomain->{$key} = esc($value);
                }
            }
            if(!isset($_POST["IDNsupport_helper"]) || $_POST["IDNsupport_helper"] != "yes") {
                $topleveldomain->AllowedIDNCharacters = "";
            }
            $tld_id = $topleveldomain->Identifier = intval($_POST["id"]);
            if($topleveldomain->edit($tld_id)) {
                flashMessage($topleveldomain);
                header("Location: topleveldomains.php?page=show&id=" . $tld_id);
                exit;
            }
            foreach ($topleveldomain->Variables as $key) {
                $topleveldomain->{$key} = htmlspecialchars($topleveldomain->{$key});
            }
            $error = true;
        }
        break;
    case "delete":
        $pagetype = "confirmDelete";
        if($tld_id != NULL) {
            $page = "show";
        } else {
            $page = "overview";
        }
        if(empty($_POST) || !U_SERVICEMANAGEMENT_DELETE) {
        } elseif(isset($_POST["imsure"]) && $_POST["imsure"] == "yes") {
            require_once "class/topleveldomain.php";
            $topleveldomain = new topleveldomain();
            $result = $topleveldomain->delete($tld_id);
            if($result) {
                $tld_id = NULL;
                $page = "overview";
                if(!empty($_SESSION["ActionLog"]["Tld"]["delete"])) {
                    array_shift($_SESSION["ActionLog"]["Tld"]["delete"]);
                    if(!empty($_SESSION["ActionLog"]["Tld"]["delete"])) {
                        header("Location: ?page=delete&id=" . current($_SESSION["ActionLog"]["Tld"]["delete"]));
                        exit;
                    }
                }
            } else {
                $page = "overview";
            }
            $pagetype = "add";
        }
        break;
    case "show":
        if(isset($_GET["action"])) {
            if(!U_SERVICEMANAGEMENT_EDIT) {
            } else {
                require_once "class/topleveldomain.php";
                $topleveldomain = new topleveldomain();
                $topleveldomain->show($tld_id);
                switch ($_GET["action"]) {
                    case "syncserver":
                        $topleveldomain->syncWhoisServer($topleveldomain->Tld);
                        break;
                    default:
                        flashMessage($topleveldomain);
                        header("Location: topleveldomains.php?page=show&id=" . $tld_id);
                        exit;
                }
            }
        }
        break;
    default:
        $pagetype = "add";
        if(isset($_SESSION["ActionLog"]) && isset($_SESSION["ActionLog"]["Tld"]["delete"])) {
            unset($_SESSION["ActionLog"]["Tld"]["delete"]);
        }
        if(isset($_POST["action"]) && U_SERVICEMANAGEMENT_DELETE) {
            $list_tlds = is_array($_POST["tlds"]) ? $_POST["tlds"] : [];
            if(empty($list_tlds)) {
            } else {
                require_once "class/topleveldomain.php";
                switch ($_POST["action"]) {
                    case "deleteTLD":
                        $_SESSION["ActionLog"]["Tld"]["delete"] = [];
                        foreach ($list_tlds as $t_id) {
                            $_SESSION["ActionLog"]["Tld"]["delete"][] = $t_id;
                        }
                        if(!empty($_SESSION["ActionLog"]["Tld"]["delete"])) {
                            header("Location: ?page=delete&id=" . current($_SESSION["ActionLog"]["Tld"]["delete"]));
                            exit;
                        }
                        break;
                }
            }
            switch ($page) {
                case "show":
                    require_once "class/topleveldomain.php";
                    $topleveldomain = isset($topleveldomain) && is_object($topleveldomain) ? $topleveldomain : new topleveldomain();
                    $topleveldomain->show($tld_id);
                    require_once "class/product.php";
                    $product = new product();
                    $fields = ["ProductCode", "ProductName", "PriceExcl"];
                    $list_products = $product->all($fields, "ProductCode", "ASC", -1, "ProductTld", $topleveldomain->Tld);
                    require_once "class/domain.php";
                    $domain = new domain();
                    $session = isset($_SESSION["topleveldomain.show.domain"]) ? $_SESSION["topleveldomain.show.domain"] : [];
                    $fields = ["Domain", "Tld", "Debtor", "CompanyName", "SurName", "Initials", "RegistrationDate", "ExpirationDate", "Status", "Registrar", "Name", "Type", "AuthKey", "PeriodicID", "PriceExcl", "Periodic", "TerminationDate", "NextDate", "AutoRenew", "DomainAutoRenew", "TerminationID"];
                    $sort = isset($session["sort"]) ? $session["sort"] : "Domain";
                    $order = isset($session["order"]) ? $session["order"] : "ASC";
                    $limit = isset($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : "1";
                    $current_page = $limit;
                    $searchat = "Tld";
                    $searchfor = $topleveldomain->Tld;
                    $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : min(25, MAX_RESULTS_LIST);
                    $list_tld_domains = $domain->all($fields, $sort, $order, $limit, $searchat, $searchfor, false, $show_results);
                    $_SESSION["topleveldomain.show.domain"] = ["sort" => $sort, "order" => $order, "results" => $show_results];
                    $message = parse_message($topleveldomain);
                    $wfh_page_title = __("topleveldomain") . " ." . $topleveldomain->Tld;
                    $current_page_url = "topleveldomains.php?page=show&id=" . $tld_id;
                    $sidebar_template = "servicemanagement.sidebar.php";
                    require_once "views/topleveldomain.show.php";
                    break;
                case "add":
                    checkRight(U_SERVICEMANAGEMENT_EDIT);
                    require_once "class/topleveldomain.php";
                    $topleveldomain = isset($topleveldomain) && is_object($topleveldomain) ? $topleveldomain : new topleveldomain();
                    if($pagetype == "edit" && 0 < $tld_id) {
                        $topleveldomain->show($tld_id);
                    }
                    require_once "class/registrar.php";
                    $registrar = new registrar();
                    $fields = ["Name"];
                    $list_domain_registrars = $registrar->all($fields);
                    require_once "class/product.php";
                    $product = new product();
                    $fields = ["ProductCode", "ProductName", "PriceExcl"];
                    $list_products = $product->all($fields, "ProductCode", "ASC");
                    $message = parse_message($topleveldomain, $registrar, $product);
                    $wfh_page_title = $pagetype == "edit" ? __("edit topleveldomain") : __("add topleveldomain");
                    $current_page_url = "topleveldomains.php";
                    $sidebar_template = "servicemanagement.sidebar.php";
                    require_once "views/topleveldomain.add.php";
                    break;
                default:
                    require_once "class/topleveldomain.php";
                    $topleveldomain = isset($topleveldomain) && is_object($topleveldomain) ? $topleveldomain : new topleveldomain();
                    $session = isset($_SESSION["topleveldomain.overview"]) ? $_SESSION["topleveldomain.overview"] : [];
                    $fields = ["Tld", "Registrar", "Name", "DomainNumber", "WhoisServer", "AskForAuthKey"];
                    $sort = isset($session["sort"]) ? $session["sort"] : "Tld";
                    $order = isset($session["order"]) ? $session["order"] : "ASC";
                    $limit = isset($_GET["p"]) ? $_GET["p"] : (isset($session["limit"]) ? $session["limit"] : "1");
                    $searchat = isset($session["searchat"]) ? $session["searchat"] : "Tld";
                    $searchfor = isset($session["searchfor"]) ? $session["searchfor"] : "";
                    $selectgroup = isset($session["group"]) ? $session["group"] : "";
                    $show_results = isset($session["results"]) ? $session["results"] : 10;
                    $list_domain_tlds = $topleveldomain->all($fields, $sort, $order, $limit, $searchat, $searchfor, $selectgroup, $show_results);
                    if(isset($list_domain_tlds["CountRows"]) && ($list_domain_tlds["CountRows"] < $show_results * ($limit - 1) || 1 < $limit && $list_domain_tlds["CountRows"] == $show_results * ($limit - 1))) {
                        $newPage = ceil($list_domain_tlds["CountRows"] / $show_results);
                        if($newPage <= 0) {
                            $newPage = 1;
                        }
                        header("Location: topleveldomains.php?p=" . $newPage);
                        exit;
                    }
                    $_SESSION["topleveldomain.overview"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "group" => $selectgroup, "searchat" => $searchat, "searchfor" => $searchfor, "limit" => $limit];
                    $current_page = $limit;
                    $message = parse_message($topleveldomain);
                    $wfh_page_title = __("topleveldomains");
                    $current_page_url = "topleveldomains.php";
                    $sidebar_template = "servicemanagement.sidebar.php";
                    require_once "views/topleveldomain.overview.php";
            }
        }
        if(isset($_POST["whois_action"]) && $_POST["whois_action"]) {
            require_once "class/topleveldomain.php";
            $topleveldomain = new topleveldomain();
            $topleveldomain->syncWhoisServers(esc($_POST["whois_action"]));
        }
}

?>