<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "config.php";
checkRight(U_SETTINGS_SHOW);
$page = isset($_GET["page"]) ? $_GET["page"] : "overview";
$action = isset($_POST["action"]) ? $_POST["action"] : (isset($_GET["action"]) ? $_GET["action"] : "");
require_once "class/backup.php";
switch ($page) {
    case "create":
        if(!U_SETTINGS_ADD) {
        } else {
            $backup = new backup();
            $backup->make(false);
            flashMessage($backup);
            header("Location: backup.php");
            exit;
        }
        break;
    case "cleantestdata":
        if(!empty($_POST) && U_SETTINGS_EDIT) {
            $aClean = [];
            foreach ($_POST as $key => $value) {
                $aClean[] = esc($key);
            }
            $backup = new backup();
            $backup->clean($aClean);
            flashMessage($backup);
            header("Location: backup.php");
            exit;
        }
        break;
    case "deleteyears":
        $backup = new backup();
        $accounting_years = $backup->getAccountingYearsWithItems();
        if(isset($accounting_years[$_POST["AccountingYear"]]) && U_SETTINGS_EDIT) {
            if(!wf_password_verify($_POST["Password"], $account->Password)) {
                $backup->Error[] = __("confirm with correct password");
            } else {
                if(isset($_POST["Backup"]) && $_POST["Backup"] == "yes") {
                    $backup_result = $backup->make(false);
                } else {
                    $backup_result = true;
                }
                if($backup_result === true) {
                    $data_to_delete = isset($_POST["Data"]) ? array_keys($_POST["Data"]) : [];
                    $backup->deleteAccountingYear($_POST["AccountingYear"], $data_to_delete);
                }
            }
        }
        flashMessage($backup);
        header("Location: backup.php");
        exit;
        break;
    case "settings":
        if(!empty($_POST) && U_SETTINGS_EDIT) {
            $settings = new settings();
            if(isset($_POST["BACKUP_DIR"]) && $_POST["BACKUP_DIR"] && substr($_POST["BACKUP_DIR"], -1) != "/") {
                $_POST["BACKUP_DIR"] = $_POST["BACKUP_DIR"] . "/";
            }
            if(!isset($_POST["backup_delete"]) || $_POST["backup_delete"] != "yes") {
                $_POST["BACKUP_DELETE_AFTER_DAYS"] = 0;
            }
            foreach ($_POST as $key => $value) {
                if($key == "BACKUP_EMAILADDRESS" && 0 < strlen($value) && !check_email_address($value)) {
                    $settings->Error[] = __("invalid emailaddress");
                } else {
                    $settings->Variable = esc($key);
                    $settings->Value = esc($value);
                    $settings->edit();
                }
            }
            if(empty($settings->Error)) {
                $settings->Success[] = __("settings are modified");
            }
            flashMessage($settings);
            header("Location: backup.php");
            exit;
        }
        break;
    default:
        switch ($action) {
            case "dialog:delete":
                if(!U_SETTINGS_DELETE) {
                } else {
                    $backup = new backup();
                    $counter = 0;
                    foreach ($_POST["backups"] as $id) {
                        $backup->Identifier = $id;
                        if($backup->delete()) {
                            $counter++;
                        }
                    }
                    if(0 < $counter) {
                        $backup->Success[] = sprintf("Er zijn %d backups succesvol verwijderd", $counter);
                    }
                }
                break;
            default:
                if(isset($_POST["downloadaction"]) && $_POST["downloadaction"] == "download") {
                    $backup = new backup();
                    $backup->Identifier = intval(esc($_POST["id"]));
                    $backup->show();
                    header("Location: download.php?type=backup&filename=" . $backup->FileName);
                    exit;
                }
                if(isset($_POST["downloadaction"]) && $_POST["downloadaction"] == "restore" && U_SETTINGS_EDIT) {
                    $backup = new backup();
                    $backup->Identifier = intval(esc($_POST["id"]));
                    $backup->show();
                    if($backup->Version == SOFTWARE_VERSION) {
                        $backup->replace();
                    } else {
                        $backup->Error[] = __("backup restore of older version is not possible");
                    }
                    flashMessage($backup);
                    header("Location: backup.php");
                    exit;
                }
                $backup = isset($backup) && is_object($backup) ? $backup : new backup();
                $session = isset($_SESSION["backup.overview"]) ? $_SESSION["backup.overview"] : [];
                $fields = ["Date", "FileName", "Version", "Author", "Name"];
                $sort = isset($session["sort"]) ? $session["sort"] : "Date";
                $order = isset($session["order"]) ? $session["order"] : "DESC";
                $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) ? $session["limit"] : "1");
                $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : MAX_RESULTS_LIST);
                $backup_list = $backup->all($fields, $sort, $order, $limit, false, false, false, $show_results);
                $_SESSION["backup.overview"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "limit" => $limit];
                $session = $_SESSION["backup.overview"];
                $current_page = $limit;
                $accounting_years = $backup->getAccountingYearsWithItems();
                $message = parse_message($backup);
                $wfh_page_title = __("settings") . " - " . __("backup overview");
                $current_page_url = "backup.php";
                $sidebar_template = "settings.sidebar.php";
                require_once "views/settings.backup.php";
        }
}

?>