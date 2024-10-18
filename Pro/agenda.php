<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "config.php";
checkRight(U_AGENDA_SHOW);
$page = isset($_GET["page"]) ? $_GET["page"] : "overview";
$agenda_id = isset($_GET["id"]) ? intval(esc($_GET["id"])) : (isset($_POST["id"]) ? intval(esc($_POST["id"])) : NULL);
require_once "class/agenda.php";
$agenda = isset($agenda) ? $agenda : new agenda();
if(isset($_POST["action"]) && $_POST["action"] == "agenda_remove_item") {
    $agenda->Identifier = intval(esc($_POST["agendaId"]));
    if($agenda->delete()) {
        echo json_encode(["result" => "OK"]);
        exit;
    }
    echo json_encode(["result" => "BAD", "msg" => __("emailstatus error")]);
    exit;
}
if(isset($_POST["action"]) && $_POST["action"] == "agenda_save_item") {
    $agenda->Identifier = intval(esc($_POST["agendaId"]));
    if($agenda->Identifier !== 0 && !$agenda->show()) {
        echo json_encode(["result" => "BAD", "msg" => $agenda->Error[0]]);
        exit;
    }
    if(isset($_POST["fields"])) {
        parse_str(esc($_POST["fields"]), $aFields);
    }
    $agenda->WholeDay = 0;
    if(intval($aFields["EmailNotifyDays"]) < 0) {
        $aFields["EmailNotifyDays"] = 0;
    }
    if(isset($aFields["EmailNotify"]) && $aFields["EmailNotify"] != "") {
        $aFields["EmailNotify"] = esc($aFields["EmailNotifyDays"]);
        $agenda->Employee = 0 < $agenda->Employee ? $account->Identifier : 0;
    } elseif($agenda->Employee == $account->Identifier) {
        $aFields["EmailNotify"] = -1;
    }
    foreach ($aFields as $key => $value) {
        if(in_array($key, $agenda->Variables)) {
            $agenda->{$key} = esc($value);
        }
    }
    $agenda->Date = rewrite_date_site2db($agenda->Date);
    if(empty($agenda->Identifier)) {
        $action = $agenda->add();
    } else {
        $action = $agenda->edit();
    }
    if($action) {
        echo json_encode(["result" => "OK", "msg" => sprintf(__("agenda item adjusted"), rewrite_date_db2site($agenda->Date))]);
        exit;
    }
    echo json_encode(["result" => "BAD", "msg" => $agenda->Error[0]]);
    exit;
} else {
    if($page == "add" && U_AGENDA_ADD) {
        require_once "views/elements/agenda.dialog.php";
        exit;
    }
    if($page == "edit" && U_AGENDA_EDIT && 0 < $agenda_id) {
        $agenda->Identifier = esc($agenda_id);
        if(!$agenda->show()) {
            exit;
        }
        require_once "views/elements/agenda.dialog.php";
        exit;
    }
    $agenda->SelectedPeriod = isset($_SESSION["agenda.overview"]["period"]) ? esc($_SESSION["agenda.overview"]["period"]) : "w";
    if(isset($_SESSION["agenda.overview"]["today"]) && $_SESSION["agenda.overview"]["today"] == "yes") {
        unset($_SESSION["agenda.overview"]["StartDate"]);
        unset($_SESSION["agenda.overview"]["EndDate"]);
        unset($_SESSION["agenda.overview"]["today"]);
        if(substr($agenda->SelectedPeriod, 0, 1) == "d") {
            $agenda->SelectedPeriod = "w";
            $_SESSION["agenda.overview"]["period"] = "w";
        }
        $show_today = true;
    }
    if(isset($_SESSION["agenda.overview"]["move"])) {
        $interval = $_SESSION["agenda.overview"]["move"] == "-1" ? -1 : 1;
        substr($agenda->SelectedPeriod, 0, 1);
        switch (substr($agenda->SelectedPeriod, 0, 1)) {
            case "q":
                $start_date = date("Y-m-d", strtotime($interval * 3 . " month", strtotime($_SESSION["agenda.overview"]["StartDate"])));
                $end_date = date("Y-m-d", strtotime($interval * 3 . " month", strtotime(substr($_SESSION["agenda.overview"]["EndDate"], 0, 8) . "01")));
                $end_date = substr($end_date, 0, 8) . date("t", strtotime($end_date));
                break;
            case "m":
                $start_date = date("Y-m-d", strtotime($interval . " month", strtotime($_SESSION["agenda.overview"]["StartDate"])));
                $end_date = date("Y-m-d", strtotime($interval . " month", strtotime(substr($_SESSION["agenda.overview"]["EndDate"], 0, 8) . "01")));
                $end_date = substr($end_date, 0, 8) . date("t", strtotime($end_date));
                break;
            case "w":
                $start_date = date("Y-m-d", strtotime($interval . " week", strtotime($_SESSION["agenda.overview"]["StartDate"])));
                $end_date = date("Y-m-d", strtotime("+6 days", strtotime($start_date)));
                break;
            default:
                $_SESSION["agenda.overview"]["period"] = substr($agenda->SelectedPeriod, 0, 1);
                unset($_SESSION["agenda.overview"]["move"]);
        }
    } else {
        $start_date = isset($_SESSION["agenda.overview"]["StartDate"]) && $_SESSION["agenda.overview"]["StartDate"] ? esc($_SESSION["agenda.overview"]["StartDate"]) : date("Y-m-d");
        substr($agenda->SelectedPeriod, 0, 1);
        switch (substr($agenda->SelectedPeriod, 0, 1)) {
            case "q":
                $quarter = ceil(date("n", strtotime($start_date)) / 3);
                $start_date = date("Y-" . str_pad($quarter * 3 - 2, 2, "0", STR_PAD_LEFT) . "-01", strtotime($start_date));
                $end_date = date("Y-" . str_pad($quarter * 3, 2, "0", STR_PAD_LEFT) . "-" . date("t", strtotime(date("Y-" . $quarter * 3 . "-28"))), strtotime($start_date));
                break;
            case "m":
                $start_date = date("Y-m-01", strtotime($start_date));
                $end_date = date("Y-m-" . date("t", strtotime(date("Y-m-28", strtotime($start_date)))), strtotime($start_date));
                break;
            case "w":
                $current_week_day = date("N", strtotime($start_date));
                $start_date = date("Y-m-d", strtotime("-" . ($current_week_day - 1) . " days", strtotime($start_date)));
                $end_date = date("Y-m-d", strtotime("+6 days", strtotime($start_date)));
                break;
            case "d":
                $tmp_selected_period = explode("_", $agenda->SelectedPeriod, 3);
                $tmp_s = explode("-", substr($agenda->SelectedPeriod, 2, 10));
                $tmp_e = explode("-", substr($agenda->SelectedPeriod, 13, 10));
                $start_date = date("Y-m-d", strtotime(rewrite_date_site2db($tmp_selected_period[1])));
                $end_date = date("Y-m-d", strtotime(rewrite_date_site2db($tmp_selected_period[2])));
                if($end_date < $start_date) {
                    $agenda->Error[] = __("invalid period selected, redirected to current week");
                    $_SESSION["agenda.overview"]["today"] = "yes";
                    flashMessage($agenda);
                    header("Location:agenda.php");
                    exit;
                }
                $agenda->CustomDate = true;
                break;
        }
    }
    $_SESSION["agenda.overview"]["StartDate"] = $start_date;
    $_SESSION["agenda.overview"]["EndDate"] = $end_date;
    $agenda->setStartDate($start_date);
    $agenda->setEndDate($end_date);
    $fields = ["Description", "TimeFrom", "TimeTill", "WholeDay", "Employee", "EmailNotify", "ItemType", "ItemID"];
    if(isset($_SESSION["agenda.overview"]["search"]) && $_SESSION["agenda.overview"]["search"]) {
        $agenda->SearchString = trim(esc($_SESSION["agenda.overview"]["search"]), " \r\n\t");
        $agenda_items = $agenda->all($fields, false, false, $agenda->SearchString);
    } else {
        $agenda_items = $agenda->all($fields, $start_date, $end_date);
    }
    $message = parse_message($agenda);
    $wfh_page_title = __("agenda");
    $current_page_url = "agenda.php";
    $sidebar_template = "dashboard.sidebar.php";
    require_once "views/agenda.overview.php";
}

?>