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
$handle_id = isset($_GET["id"]) ? intval(esc($_GET["id"])) : (isset($_POST["id"]) ? intval(esc($_POST["id"])) : NULL);
$page = $page == "edit" ? "add" : $page;
switch ($page) {
    case "add":
        $pagetype = "add";
        if(empty($_POST) || !U_SERVICEMANAGEMENT_ADD) {
        } else {
            require_once "class/handle.php";
            $handle = new handle();
            foreach ($_POST as $key => $value) {
                if(in_array($key, $handle->Variables)) {
                    $handle->{$key} = esc($value);
                }
            }
            if(IS_INTERNATIONAL) {
                $handle->State = isset($_POST["StateCode"]) && $_POST["StateCode"] ? esc($_POST["StateCode"]) : $handle->State;
            }
            if($handle->Registrar == "") {
                $handle->RegistrarHandle = "";
            }
            if(!isset($_SESSION["custom_fields"]["handle"]) || $_SESSION["custom_fields"]["handle"]) {
                $customfields_list = $_SESSION["custom_fields"]["handle"];
                $handle->customvalues = [];
                foreach ($customfields_list as $k => $custom_field) {
                    $handle->customvalues[$custom_field["FieldCode"]] = isset($_POST["custom"][$custom_field["FieldCode"]]) ? esc($_POST["custom"][$custom_field["FieldCode"]]) : "";
                }
            }
            if(0 < $handle_id) {
                $handle->Status = 1;
                $result = $handle->edit($handle_id);
                $edit = true;
            } else {
                if(isset($_POST["debtor_helper"]) && $_POST["debtor_helper"] == "yes") {
                    $handle->Handle = $handle->nextInternalHandle("debtor", $handle->Debtor);
                } else {
                    $handle->Handle = $handle->nextInternalHandle("general");
                }
                $result = $handle->add();
                if($result) {
                    $handle_id = $handle->Identifier;
                }
                $edit = false;
            }
            if($result) {
                if($edit === true && 0 < $handle_id && $handle->RegistrarHandle && isset($_POST["update_handle_at_registrar"]) && $_POST["update_handle_at_registrar"] == "yes") {
                    require_once "class/domain.php";
                    $domain = new domain();
                    $fields = ["Domain", "Tld"];
                    $searchat = "ownerHandle|adminHandle|techHandle";
                    $searchfor = $handle_id;
                    $list_handle_domains = $domain->all($fields, false, false, -1, $searchat, $searchfor);
                    if($list_handle_domains["CountRows"] == 1) {
                        foreach ($list_handle_domains as $k => $v) {
                            if(is_numeric($k)) {
                                $handle->updateWhoisDataToRegistrar($v["Domain"] . "." . $v["Tld"]);
                            }
                        }
                    } else {
                        $handle->updateWhoisDataToRegistrar();
                    }
                } elseif($edit === true && 0 < $handle_id && $handle->Registrar && $handle->RegistrarHandle) {
                    $handle->Warning[] = __("if you want to update handle at registrar, use action sync handles");
                }
                flashMessage($handle);
                header("Location: handles.php?page=show&id=" . $handle_id);
                exit;
            }
            foreach ($handle->Variables as $key) {
                $handle->{$key} = htmlspecialchars($handle->{$key});
            }
        }
        break;
    case "delete":
        $pagetype = "confirmDelete";
        if($handle_id != NULL) {
            $page = "show";
        } else {
            $page = "overview";
        }
        if(empty($_POST) || !U_SERVICEMANAGEMENT_DELETE) {
        } elseif(isset($_POST["imsure"]) && $_POST["imsure"] == "yes") {
            require_once "class/handle.php";
            $handle = new handle();
            $result = $handle->delete($handle_id);
            if($result) {
                $_SESSION["flashMessage"]["Success"][] = __("handles are deleted");
                header("Location: handles.php");
                exit;
            }
            unset($pagetype);
        }
        break;
    case "show":
        if(isset($_GET["action"])) {
            switch ($_GET["action"]) {
                case "updatecontact":
                    if(!U_SERVICEMANAGEMENT_EDIT) {
                    } else {
                        require_once "class/handle.php";
                        $handle = new handle();
                        $handle->show($handle_id);
                        $handle->updateWhoisDataToRegistrar();
                        flashMessage($handle);
                        header("Location: handles.php?page=show&id=" . $handle_id);
                        exit;
                    }
                    break;
                case "createhandleatregistrar":
                    if(!U_SERVICEMANAGEMENT_EDIT) {
                    } else {
                        require_once "class/handle.php";
                        $handle = new handle();
                        $handle->show($handle_id);
                        foreach ($handle as $key => $value) {
                            if(is_string($value)) {
                                $handle->{$key} = htmlspecialchars_decode($value);
                            }
                        }
                        $handle->createHandleAtRegistrar();
                        flashMessage($handle);
                        header("Location: handles.php?page=show&id=" . $handle_id);
                        exit;
                    }
                    break;
            }
        }
        break;
    default:
        if(isset($_POST["action"]) && $_POST["action"] == "cleanup") {
            if(isset($_POST["imsure"]) && $_POST["imsure"] == "yes") {
                require_once "class/handle.php";
                $handle = new handle();
                $handle->cleanup();
                flashMessage($handle);
                header("Location: handles.php");
                exit;
            }
        } elseif(isset($_POST["action"]) && U_SERVICEMANAGEMENT_DELETE) {
            require_once "class/handle.php";
            if(!empty($_POST["handles"])) {
                $list_handles = is_array($_POST["handles"]) ? $_POST["handles"] : [];
                switch ($_POST["action"]) {
                    case "dialog:deleteHandle":
                        foreach ($list_handles as $handle_id) {
                            $handle = new handle();
                            $handle->delete($handle_id);
                        }
                        $_SESSION["flashMessage"]["Success"][] = __("handles are deleted");
                        break;
                }
            } elseif(isset($_POST["action"])) {
                $handle->Warning[] = __("nothing selected");
            }
            if(isset($_GET["from_page"]) && !empty($_GET["from_page"])) {
                flashMessage($handle);
                switch ($_GET["from_page"]) {
                    case "registrar":
                        header("Location: registrars.php?page=show&id=" . intval($_GET["from_id"]) . "#tab-handles");
                        exit;
                        break;
                    case "debtor":
                        $_SESSION["selected_tab"] = 5;
                        header("Location: debtors.php?page=show&id=" . intval($_GET["from_id"]) . "#tab-handles");
                        exit;
                        break;
                }
            }
        }
        switch ($page) {
            case "show":
                require_once "class/handle.php";
                $handle = isset($handle) && is_object($handle) ? $handle : new handle();
                if(!$handle->show($handle_id)) {
                    flashMessage($handle);
                    header("Location: handles.php");
                    exit;
                }
                if($handle->Status == 9) {
                    $error_class->Warning[] = __("this handle has been removed and cannot be used again");
                }
                require_once "class/registrar.php";
                $registrar = new registrar();
                if(0 < $handle->Registrar) {
                    $registrar->show($handle->Registrar);
                    $registrar->getVersionInfo();
                }
                require_once "class/debtor.php";
                $debtor = new debtor();
                if(0 < $handle->Debtor) {
                    $debtor->show($handle->Debtor, false);
                }
                require_once "class/domain.php";
                $domain = new domain();
                $session = isset($_SESSION["handle.show.domain"]) ? $_SESSION["handle.show.domain"] : [];
                $fields = ["Domain", "Tld", "Debtor", "CompanyName", "SurName", "Initials", "RegistrationDate", "ExpirationDate", "Status", "Registrar", "Name", "Type", "AuthKey", "PeriodicID", "PriceExcl", "Periodic", "NextDate", "TerminationDate", "AutoRenew", "DomainAutoRenew", "TerminationID"];
                $sort = isset($session["sort"]) ? $session["sort"] : "Domain";
                $order = isset($session["order"]) ? $session["order"] : "ASC";
                $limit = isset($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : "1";
                $current_page = $limit;
                $searchat = "ownerHandle|adminHandle|techHandle";
                $searchfor = $handle_id;
                $tmp_array_domainstatus = $array_domainstatus;
                unset($tmp_array_domainstatus[9]);
                $group_id = implode("|", array_keys($tmp_array_domainstatus));
                $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : min(25, MAX_RESULTS_LIST);
                $list_handle_domains = $domain->all($fields, $sort, $order, $limit, $searchat, $searchfor, $group_id, $show_results);
                $_SESSION["handle.show.domain"] = ["sort" => $sort, "order" => $order, "results" => $show_results];
                $message = parse_message($handle, $debtor, $domain);
                $wfh_page_title = __("handle") . " " . $handle->Handle;
                $current_page_url = "handles.php?page=show&id=" . $handle_id;
                $sidebar_template = "servicemanagement.sidebar.php";
                require_once "views/handle.show.php";
                break;
            case "add":
                checkRight(U_SERVICEMANAGEMENT_ADD);
                require_once "class/handle.php";
                $handle = isset($handle) && is_object($handle) ? $handle : new handle();
                if(0 < $handle_id && count($handle->Error) === 0) {
                    $handle->show($handle_id);
                    $pagetype = "edit";
                    require_once "class/domain.php";
                    $domain = new domain();
                    $fields = ["Domain", "Tld"];
                    $searchat = "ownerHandle|adminHandle|techHandle";
                    $searchfor = $handle_id;
                    $list_handle_domains = $domain->all($fields, false, false, -1, $searchat, $searchfor);
                    if(1 < $list_handle_domains["CountRows"]) {
                        $handle->Warning[] = sprintf(__("you are editing a handle for x domains"), $list_handle_domains["CountRows"]);
                    }
                } else {
                    $pagetype = "add";
                }
                require_once "class/debtor.php";
                $debtor = new debtor();
                $list_debtors = $debtor->all_small();
                require_once "class/registrar.php";
                $registrar = new registrar();
                $list_domain_registrars = $registrar->all(["Name", "Class"]);
                $message = parse_message($handle, $debtor, $registrar);
                if(0 < $handle_id) {
                    $current_page_url = "handles.php?page=show&id=" . $handle_id;
                } else {
                    $current_page_url = "handles.php?page=add";
                }
                $wfh_page_title = $pagetype == "edit" ? __("edit handle") : __("add handle");
                $sidebar_template = "servicemanagement.sidebar.php";
                require_once "views/handle.add.php";
                break;
            default:
                require_once "class/handle.php";
                $handle = isset($handle) && is_object($handle) ? $handle : new handle();
                $session = isset($_SESSION["handle.overview"]) ? $_SESSION["handle.overview"] : [];
                $fields = ["Handle", "Name", "Registrar", "RegistrarHandle", "Debtor", "DebtorCode", "CompanyName", "Initials", "SurName", "EmailAddress", "Status"];
                $sort = isset($session["sort"]) ? $session["sort"] : "Handle";
                $order = isset($session["order"]) ? $session["order"] : "ASC";
                $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) && $session["limit"] ? $session["limit"] : "1");
                $searchat = isset($session["searchat"]) ? $session["searchat"] : "";
                $searchfor = isset($session["searchfor"]) ? $session["searchfor"] : "";
                $selectgroup = isset($session["status"]) ? $session["status"] : "-1|1|3|4|5|6|7|8";
                $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : MAX_RESULTS_LIST);
                $list_domain_handles = $handle->all($fields, $sort, $order, $limit, $searchat, $searchfor, $selectgroup, $show_results);
                if(isset($list_domain_handles["CountRows"]) && ($list_domain_handles["CountRows"] < $show_results * ($limit - 1) || 1 < $limit && $list_domain_handles["CountRows"] == $show_results * ($limit - 1))) {
                    $newPage = ceil($list_domain_handles["CountRows"] / $show_results);
                    if($newPage <= 0) {
                        $newPage = 1;
                    }
                    $_SESSION["handle.overview"]["limit"] = $newPage;
                    header("Location: handles.php");
                    exit;
                }
                $_SESSION["handle.overview"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "status" => $selectgroup, "searchat" => $searchat, "searchfor" => $searchfor, "limit" => $limit];
                $current_page = $limit;
                $message = parse_message($handle);
                $wfh_page_title = __("handle overview");
                $current_page_url = "handles.php";
                $sidebar_template = "servicemanagement.sidebar.php";
                require_once "views/handle.overview.php";
        }
}

?>