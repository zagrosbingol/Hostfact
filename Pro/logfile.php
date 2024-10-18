<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "config.php";
checkRight(U_LOGFILE_SHOW);
$page = isset($_GET["page"]) ? $_GET["page"] : "overview";
$log_id = isset($_GET["id"]) ? intval(esc($_GET["id"])) : (isset($_POST["id"]) ? intval(esc($_POST["id"])) : NULL);
if(isset($_POST["action"]) && U_LOGFILE_DELETE) {
    switch ($_POST["action"]) {
        case "delete":
            $list_ids = isset($_POST["id"]) && is_array($_POST["id"]) ? $_POST["id"] : [];
            if(empty($list_ids)) {
            } else {
                require_once "class/messagelogfile.php";
                $mlog = new messagelogfile();
                $counter = $mlog->deleteEntries($list_ids[0]);
                $mlog->Success[] = sprintf(__("deleted message log entries"), $counter);
            }
            break;
        case "deleteSelected":
            $list_ids = isset($_POST["id"]) && is_array($_POST["id"]) ? $_POST["id"] : [];
            if(empty($list_ids)) {
            } else {
                require_once "class/messagelogfile.php";
                $mlog = new messagelogfile();
                foreach ($list_ids as $list_id) {
                    $mlog->deleteEntry($list_id);
                }
                $mlog->Success[] = sprintf(__("deleted message log entries"), count($list_ids));
            }
            break;
    }
}
require_once "class/messagelogfile.php";
$mlog = isset($mlog) && $mlog ? $mlog : new messagelogfile();
$session = isset($_SESSION["logfile.overview"]) ? $_SESSION["logfile.overview"] : [];
$fields = ["Date", "Type", "Message", "Values", "Name", "ObjectType"];
$sort = isset($session["sort"]) ? $session["sort"] : "Date";
$order = isset($session["order"]) ? $session["order"] : "DESC";
$limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) && $session["limit"] ? $session["limit"] : "1");
$searchat = isset($session["searchat"]) ? $session["searchat"] : "";
$searchfor = isset($session["searchfor"]) ? $session["searchfor"] : "";
$selectgroup = isset($session["status"]) ? $session["status"] : "";
$show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : MAX_RESULTS_LIST);
$list_logfile = $mlog->all($fields, $sort, $order, $limit, $selectgroup, $show_results);
$_SESSION["logfile.overview"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "status" => $selectgroup, "searchat" => $searchat, "searchfor" => $searchfor, "limit" => $limit];
$current_page = $limit;
if(isset($list_logfile["CountRows"]) && ($list_logfile["CountRows"] < $show_results * ($limit - 1) || 1 < $limit && $list_logfile["CountRows"] == $show_results * ($limit - 1))) {
    $newPage = ceil($list_logfile["CountRows"] / $show_results);
    if($newPage <= 0) {
        $newPage = 1;
    }
    $_SESSION["logfile.overview"]["limit"] = $newPage;
    header("Location: logfile.php");
    exit;
}
$message = parse_message($mlog);
$wfh_page_title = __("logfile overview");
$current_page_url = "logfile.php";
$sidebar_template = "logfile.sidebar.php";
require_once "views/logfile.overview.php";

?>