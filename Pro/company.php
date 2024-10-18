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
$account_id = isset($_GET["id"]) ? intval(esc($_GET["id"])) : NULL;
global $account;
if(($page == "account" || $page == "accountshow") && $account->Identifier == $account_id) {
} else {
    checkRight(U_COMPANY_SHOW);
}
require_once "class/employee.php";
$acc = new employee();
switch ($page) {
    case "account":
        if(isset($_GET["action"]) && $_GET["action"] == "edit" && isset($_POST["Identifier"]) && (U_COMPANY_EDIT || $account->Identifier == $account_id)) {
            $account_id = intval(esc($_POST["Identifier"]));
            if(!U_COMPANY_EDIT && $account->Identifier != $account_id) {
                header("Location:norights.php");
                exit;
            }
            $acc->Identifier = $account_id;
            $acc->show($account_id);
            foreach ($_POST as $key => $value) {
                if(in_array($key, $acc->Variables) && ($key != "Password" || $value != "")) {
                    $acc->{$key} = esc($value);
                } elseif($key == "Rights") {
                    $acc->editRights($value);
                } elseif($key == "Prefs") {
                    $check_array = ["creditinvoice", "invoice_waiting", "invoice_waiting_c", "invoice_open"];
                    foreach ($check_array as $k) {
                        if(!isset($value["home"][$k])) {
                            $value["home"][$k] = ["Value" => "hidden"];
                        }
                    }
                    $acc->editPreferences($value);
                }
            }
            if(!isset($_POST["Prefs"]) || !is_array($_POST["Prefs"])) {
                $acc->editPreferences("hide");
            }
            $acc->Notes = htmlspecialchars_decode($acc->Notes);
            if($_POST["Password"] != $_POST["PasswordAgain"]) {
                $acc->Error[] = __("passwords are not the same");
            } elseif(trim($_POST["Password"])) {
                $acc->Password = wf_password_hash(trim($_POST["Password"]));
            }
            if(defined("IS_DEMO") && IS_DEMO) {
                $acc->Error[] = __("demo - this feature is unavailable in demo");
                $page = "account";
            } else {
                if($acc->edit()) {
                    if(!empty($_POST["PasswordAgain"])) {
                        session_regenerate_id(true);
                    }
                    flashMessage($acc);
                    header("Location: company.php?page=accountshow&id=" . $account_id);
                    exit;
                }
                $page = "account";
            }
        } elseif(isset($_GET["action"]) && $_GET["action"] == "add" && !empty($_POST) && U_COMPANY_EDIT) {
            foreach ($_POST as $key => $value) {
                if(in_array($key, $acc->Variables) && ($key != "Password" || $value != "")) {
                    $acc->{$key} = esc($value);
                }
            }
            if(!trim($_POST["Password"]) || $_POST["Password"] != $_POST["PasswordAgain"]) {
                $acc->Error[] = __("passwords are not the same");
            } else {
                $acc->Password = wf_password_hash(trim($_POST["Password"]));
            }
            if($acc->add()) {
                if(isset($_POST["Rights"])) {
                    $acc->editRights(esc($_POST["Rights"]));
                }
                flashMessage($acc);
                header("Location: company.php?page=accounts");
                exit;
            }
            $page = "account";
        }
        break;
    case "accounts":
        if(isset($_GET["action"]) && $_GET["action"] == "delete" && isset($_POST["id"]) && 0 < $_POST["id"] && U_COMPANY_DELETE) {
            $account_id = intval($_POST["id"]);
            if($account_id == $account->Identifier) {
                $acc->Error[] = __("Cannot delete own account");
            } else {
                $acc->delete($account_id);
                flashMessage($acc);
                header("Location: company.php?page=accounts");
                exit;
            }
        }
        break;
    case "accountshow":
        if(isset($_GET["action"]) && $_GET["action"] == "activate_twofactor" && $account_id == $_SESSION["UserPro"] && isset($_POST["verify_result"]) && $_POST["verify_result"] == "success") {
            $acc->Identifier = $account_id;
            $acc->editAuthentication("activate");
            flashMessage($acc);
            header("Location: company.php?page=accountshow&id=" . $account_id);
            exit;
        }
        if(isset($_GET["action"]) && $_GET["action"] == "deactivate_twofactor" && isset($_POST["id"]) && ($account_id == $_SESSION["UserPro"] || U_COMPANY_EDIT) && $_POST["imsure"] == "yes") {
            $acc->Identifier = $account_id;
            $acc->editAuthentication("deactivate");
            flashMessage($acc);
            header("Location: company.php?page=accountshow&id=" . $account_id);
            exit;
        }
        break;
    default:
        if(isset($_POST["CompanyName"]) && U_COMPANY_EDIT) {
            require_once "class/company.php";
            $company = new company();
            $company->show();
            if(in_array($_POST["SynchronizeEmail"], ["replace", "all"]) && ($_POST["currentEmailAddress"] != $_POST["EmailAddress"] || $_POST["currentCompanyName"] != $_POST["CompanyName"])) {
                $company->currentSender = esc($_POST["currentCompanyName"] . " <" . $_POST["currentEmailAddress"] . ">");
                $company->Sender = esc($_POST["CompanyName"] . " <" . $_POST["EmailAddress"] . ">");
                $company->SynchronizeEmail = $_POST["SynchronizeEmail"];
            }
            foreach ($_POST as $key => $value) {
                if(in_array($key, $company->Variables)) {
                    $company->{$key} = esc($value);
                }
            }
            if(IS_INTERNATIONAL) {
                $company->State = isset($_POST["StateCode"]) && $_POST["StateCode"] ? esc($_POST["StateCode"]) : $company->State;
            }
            $company->edit();
            $company->show();
        }
        switch ($page) {
            case "accounts":
                $acc = isset($acc) && is_object($acc) ? $acc : new employee();
                $session = isset($_SESSION["account.overview"]) ? $_SESSION["account.overview"] : [];
                $fields = ["UserName", "Name", "Function", "LastDate", "TwoFactorAuthentication", "Language"];
                $sort = isset($session["sort"]) ? $session["sort"] : "UserName";
                $order = isset($session["order"]) ? $session["order"] : "ASC";
                $limit = isset($_GET["p"]) ? $_GET["p"] : (isset($session["limit"]) ? $session["limit"] : "1");
                $searchat = isset($session["searchat"]) ? $session["searchat"] : "UserName|Name";
                $searchfor = isset($session["searchfor"]) ? $session["searchfor"] : "";
                $selectgroup = isset($session["group"]) ? $session["group"] : "";
                $show_results = isset($session["results"]) ? $session["results"] : MAX_RESULTS_LIST;
                $accounts = $acc->all($fields, $sort, $order, $limit, $searchat, $searchfor, $selectgroup, $show_results);
                $_SESSION["account.overview"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "group" => $selectgroup, "searchat" => $searchat, "searchfor" => $searchfor, "limit" => $limit];
                $current_page = $limit;
                $message = parse_message($acc);
                $wfh_page_title = __("employee overview");
                $current_page_url = "company.php?page=accounts";
                $sidebar_template = "company.sidebar.php";
                require_once "views/employee.overview.php";
                break;
            case "accountshow":
                $acc = isset($acc) && is_object($acc) ? $acc : new employee();
                $acc->show($account_id);
                $acc->showRights($account_id);
                $acc->showPreferences($account_id);
                $acc->Identifier = $account_id;
                $message = parse_message($acc);
                $wfh_page_title = __("employee") . ": " . $acc->Name;
                $sidebar_template = "company.sidebar.php";
                require_once "views/employee.show.php";
                break;
            case "account":
                if(isset($account_id) && $account_id) {
                    $acc->show($account_id);
                    $acc->showRights($account_id);
                    $acc->showPreferences($account_id);
                    $acc->Identifier = $account_id;
                    $pagetype = "edit";
                } else {
                    $pagetype = "add";
                }
                $message = parse_message($acc);
                $wfh_page_title = $pagetype == "edit" ? __("edit employee") : __("add employee");
                $sidebar_template = "company.sidebar.php";
                require_once "views/employee.add.php";
                break;
            default:
                if(U_COMPANY_SHOW) {
                    require_once "class/template.php";
                    $emailtemplate = new emailtemplate();
                    $templateCount = $emailtemplate->getAllTemplatesBySender($company->CompanyName . " <" . $company->EmailAddress . ">");
                    $message = parse_message($company);
                    $wfh_page_title = __("company data");
                    $sidebar_template = "company.sidebar.php";
                    require_once "views/company.add.php";
                }
        }
}

?>