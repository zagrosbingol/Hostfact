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
$email_id = isset($_GET["id"]) ? intval(esc($_GET["id"])) : (isset($_POST["id"]) ? intval(esc($_POST["id"])) : NULL);
require_once "class/email.php";
switch ($page) {
    case "batch":
        $email = new email();
        $result = $email->all(["Status"], "", "", "", "", "", "0");
        $max_versturen_tegelijk = MAX_SENT_BATCHES;
        $reeds_verstuurd = 0;
        if($result) {
            foreach ($result as $k => $v) {
                if(is_numeric($k)) {
                    $email = new email();
                    $email->Identifier = $k;
                    $email->show();
                    if($reeds_verstuurd < $max_versturen_tegelijk) {
                        $email->sent("", "", false);
                        $reeds_verstuurd += 1;
                    }
                    flashMessage($email);
                    unset($email);
                }
            }
        }
        flashMessage($email);
        header("Location: emails.php?selectgroup=0");
        exit;
        break;
    default:
        if(isset($_POST["action"])) {
            switch ($_POST["action"]) {
                case "sent":
                    foreach ($_POST["id"] as $key) {
                        $email = new email();
                        $email->Identifier = esc($key);
                        $email->show();
                        $email->sent("", "", false);
                        flashMessage($email);
                        unset($email);
                    }
                    flashMessage($email);
                    header("Location: emails.php?selectgroup=" . esc($_GET["selectgroup"]));
                    exit;
                    break;
                case "delete":
                    foreach ($_POST["id"] as $key) {
                        $email = new email();
                        $email->Identifier = esc($key);
                        $email->delete();
                        flashMessage($email);
                        unset($email);
                    }
                    flashMessage($email);
                    header("Location: emails.php?selectgroup=" . esc($_GET["selectgroup"]));
                    exit;
                    break;
            }
        }
        $email = isset($email) && $email ? $email : new email();
        $selectgroup = isset($_GET["selectgroup"]) ? esc($_GET["selectgroup"]) : "0";
        $session = isset($_SESSION["emails.overview" . $selectgroup]) ? $_SESSION["emails.overview" . $selectgroup] : [];
        $fields = ["SentDate", "Recipient", "Subject", "Status"];
        $sort = isset($session["sort"]) ? $session["sort"] : "SentDate";
        $order = isset($session["order"]) ? $session["order"] : "DESC";
        $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) ? $session["limit"] : "1");
        $searchat = isset($session["searchat"]) ? $session["searchat"] : "";
        $searchfor = isset($session["searchfor"]) ? $session["searchfor"] : "";
        $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : MAX_RESULTS_LIST);
        $email_list = $email->all($fields, $sort, $order, $limit, $searchat, $searchfor, $selectgroup, $show_results);
        $_SESSION["emails.overview" . $selectgroup] = ["sort" => $sort, "order" => $order, "results" => $show_results, "group" => $selectgroup, "searchat" => $searchat, "searchfor" => $searchfor, "limit" => $limit];
        $current_page = $limit;
        $message = parse_message($email);
        switch ($selectgroup) {
            case "0":
                $wfh_page_title = __("not sent emails");
                break;
            case "1":
                $wfh_page_title = __("sent emails");
                break;
            case "8":
                $wfh_page_title = __("emails with errors");
                break;
            default:
                $current_page_url = "emails.php?selectgroup=" . $selectgroup;
                $sidebar_show_email_sub = true;
                $sidebar_template = "debtor.sidebar.php";
                require_once "views/emails.overview.php";
        }
}

?>