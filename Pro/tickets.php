<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "config.php";
require_once "class/ticket.php";
checkRight(U_TICKET_SHOW);
$page = isset($_GET["page"]) ? $_GET["page"] : "overview";
$ticket_id = isset($_GET["id"]) ? intval(esc($_GET["id"])) : (isset($_POST["id"]) ? intval(esc($_POST["id"])) : NULL);
switch ($page) {
    case "add":
        if(!U_TICKET_ADD) {
        } elseif(isset($_POST["TicketID"])) {
            $ticket = new ticket();
            foreach ($_POST as $key => $value) {
                if(in_array($key, $ticket->Variables)) {
                    $ticket->{$key} = $value;
                }
            }
            $ticketmessage = new ticketmessage();
            foreach ($_POST as $key => $value) {
                if(in_array($key, $ticketmessage->Variables)) {
                    $ticketmessage->{$key} = $value;
                }
            }
            if(!$ticket->is_free($ticket->TicketID)) {
                $ticket->TicketID = $ticket->newTicketID();
            }
            if($ticket->Debtor <= 0) {
                $ticket->Type = "email";
            }
            $ticketmessage->Attachments = [];
            if(isset($_FILES["add_attachment"])) {
                if(defined("IS_DEMO") && IS_DEMO) {
                    $ticket->Warning[] = __("demo - not able to add attachments");
                } else {
                    $number_of_attachments = count($_FILES["add_attachment"]["name"]);
                    for ($i = 0; $i < $number_of_attachments; $i++) {
                        if(empty($_FILES["add_attachment"]["error"][$i])) {
                            $extension = strtolower(substr($_FILES["add_attachment"]["name"][$i], strrpos($_FILES["add_attachment"]["name"][$i], ".") + 1));
                            $x = 2;
                            if(!is_dir(DIR_TICKET_ATTACHMENTS . $ticket->TicketID)) {
                                mkdir(DIR_TICKET_ATTACHMENTS . $ticket->TicketID, 511);
                            }
                            while (file_exists(DIR_TICKET_ATTACHMENTS . $ticket->TicketID . "/" . $_FILES["add_attachment"]["name"][$i])) {
                                $tmp = substr($_FILES["add_attachment"]["name"][$i], 0, strlen($_FILES["add_attachment"]["name"][$i]) - strlen($extension) - 1);
                                if(2 < $x) {
                                    $_FILES["add_attachment"]["name"][$i] = substr($tmp, 0, strlen($tmp) - strlen($x) - 2) . "(" . $x . ")." . $extension;
                                } else {
                                    $_FILES["add_attachment"]["name"][$i] = $tmp . "(" . $x . ")." . $extension;
                                }
                                $x++;
                            }
                            if(@move_uploaded_file($_FILES["add_attachment"]["tmp_name"][$i], DIR_TICKET_ATTACHMENTS . $ticket->TicketID . "/" . $_FILES["add_attachment"]["name"][$i])) {
                                $ticketmessage->Attachments[] = DIR_TICKET_ATTACHMENTS . $ticket->TicketID . "/" . $_FILES["add_attachment"]["name"][$i];
                            }
                        }
                    }
                }
            }
            if($ticket->add()) {
                $ticket_id = $ticket->Identifier;
                $ticketmessage->TicketID = $ticket->TicketID;
                if($ticketmessage->add()) {
                    if($ticket->Type == "email") {
                        $ticketmessage->CC = $ticket->CC;
                        $ticketmessage->sent();
                    } elseif(0 < TICKET_REACTION_EMAIL_TEMPLATE && $_POST["EmailAddress"] && check_email_address($_POST["EmailAddress"])) {
                        $objects = ["ticket" => $ticket, "ticketmessage" => $ticketmessage];
                        $emailParameters = ["Sender" => $ticketmessage->SenderName . "<" . $ticketmessage->SenderEmail . ">", "Recipient" => check_email_address(esc($_POST["EmailAddress"]), "convert"), "Sent_bcc" => false, "Debtor" => $ticket->Debtor];
                        if($ticketmessage->sentNotification(TICKET_REACTION_EMAIL_TEMPLATE, $objects, $emailParameters)) {
                            $ticketmessage->Success[] = sprintf(__("a notification for ticket reaction is sent by mail"), esc($_POST["EmailAddress"]));
                        }
                    }
                    flashMessage($ticket, $ticketmessage);
                    header("Location:tickets.php");
                    exit;
                }
                $ticket->delete();
                $ticket->Success = [];
            }
            foreach ($ticket->Variables as $key) {
                $ticket->{$key} = is_string($ticket->{$key}) ? htmlspecialchars($ticket->{$key}) : $ticket->{$key};
            }
            foreach ($ticketmessage->Variables as $key) {
                $ticketmessage->{$key} = is_string($ticketmessage->{$key}) ? htmlspecialchars($ticketmessage->{$key}) : $ticketmessage->{$key};
            }
        }
        break;
    case "edit":
        if(!U_TICKET_EDIT) {
        } elseif(isset($_POST["TicketID"])) {
            $ticket = new ticket();
            $ticket->Identifier = $ticket_id;
            $ticket->show();
            $old_ticketid = $ticket->TicketID;
            foreach ($_POST as $key => $value) {
                if(in_array($key, $ticket->Variables)) {
                    $ticket->{$key} = $value;
                }
            }
            $ticket->LastName = htmlspecialchars_decode($ticket->LastName);
            if(!$ticket->is_free($ticket->TicketID)) {
                $ticket->TicketID = $ticket->newTicketID();
            }
            if($ticket->Debtor <= 0) {
                $ticket->Type = "email";
            }
            if($ticket->edit()) {
                if($old_ticketid != $ticket->TicketID) {
                    $ticketmessage = new ticketmessage();
                    $ticketmessage->changeTicketID($old_ticketid, $ticket->TicketID);
                }
                flashMessage($ticket, $ticketmessage);
                header("Location:tickets.php?page=show&id=" . $ticket_id);
                exit;
            }
            foreach ($ticket->Variables as $key) {
                $ticket->{$key} = htmlspecialchars($ticket->{$key});
            }
        }
        break;
    case "addmessage":
        if(!U_TICKET_ADD) {
        } elseif(isset($_POST["id"])) {
            $ticket = new ticket();
            $ticket->Identifier = $ticket_id;
            if(!$ticket->show()) {
                flashMessage($ticket);
                header("Location: tickets.php");
                exit;
            }
            $ticketmessage = new ticketmessage();
            $ticketmessage->TicketID = $ticket->TicketID;
            $ticketmessage->Attachments = "";
            $ticketmessage->Subject = htmlspecialchars_decode($ticket->Subject);
            $ticketmessage->Message = esc($_POST["Message"]);
            $ticketmessage->SenderID = $account->Identifier;
            $ticketmessage->Attachments = [];
            if(isset($_FILES["add_attachment"])) {
                if(defined("IS_DEMO") && IS_DEMO) {
                    $ticket->Warning[] = __("demo - not able to add attachments");
                } else {
                    $number_of_attachments = count($_FILES["add_attachment"]["name"]);
                    for ($i = 0; $i < $number_of_attachments; $i++) {
                        if(empty($_FILES["add_attachment"]["error"][$i])) {
                            $extension = strtolower(substr($_FILES["add_attachment"]["name"][$i], strrpos($_FILES["add_attachment"]["name"][$i], ".") + 1));
                            $x = 2;
                            if(!is_dir(DIR_TICKET_ATTACHMENTS . $ticket->TicketID)) {
                                mkdir(DIR_TICKET_ATTACHMENTS . $ticket->TicketID, 511);
                            }
                            while (file_exists(DIR_TICKET_ATTACHMENTS . $ticket->TicketID . "/" . $_FILES["add_attachment"]["name"][$i])) {
                                $tmp = substr($_FILES["add_attachment"]["name"][$i], 0, strlen($_FILES["add_attachment"]["name"][$i]) - strlen($extension) - 1);
                                if(2 < $x) {
                                    $_FILES["add_attachment"]["name"][$i] = substr($tmp, 0, strlen($tmp) - strlen($x) - 2) . "(" . $x . ")." . $extension;
                                } else {
                                    $_FILES["add_attachment"]["name"][$i] = $tmp . "(" . $x . ")." . $extension;
                                }
                                $x++;
                            }
                            if(@move_uploaded_file($_FILES["add_attachment"]["tmp_name"][$i], DIR_TICKET_ATTACHMENTS . $ticket->TicketID . "/" . $_FILES["add_attachment"]["name"][$i])) {
                                $ticketmessage->Attachments[] = DIR_TICKET_ATTACHMENTS . $ticket->TicketID . "/" . $_FILES["add_attachment"]["name"][$i];
                            }
                        }
                    }
                }
            }
            if($ticketmessage->add()) {
                if($ticket->Type == "email") {
                    $ticketmessage->EmailAddress = $ticket->EmailAddress;
                    if(isset($_POST["ticket_sent_reply_to_cc"]) && $_POST["ticket_sent_reply_to_cc"] == "yes" && !empty($ticket->CC)) {
                        $ticketmessage->CC = $ticket->CC;
                    }
                    $ticketmessage->sent();
                } elseif(0 < TICKET_REACTION_EMAIL_TEMPLATE && $ticket->EmailAddress && check_email_address($ticket->EmailAddress)) {
                    $objects = ["ticket" => $ticket, "ticketmessage" => $ticketmessage];
                    $emailParameters = ["Sender" => $ticketmessage->SenderName . "<" . $ticketmessage->SenderEmail . ">", "Recipient" => check_email_address($ticket->EmailAddress, "convert"), "Sent_bcc" => false, "Debtor" => $ticket->Debtor];
                    if($ticketmessage->sentNotification(TICKET_REACTION_EMAIL_TEMPLATE, $objects, $emailParameters)) {
                        $ticketmessage->Success[] = sprintf(__("a notification for ticket reaction is sent by mail"), $ticket->EmailAddress);
                    }
                }
                if(isset($_POST["ticket_close_after_reply"]) && $_POST["ticket_close_after_reply"] == "yes") {
                    $ticket->changeStatus(3);
                } else {
                    $ticket->changeStatus(1);
                }
                if(empty($ticket->Owner)) {
                    $ticket->changeOwner($account->Identifier);
                }
                $ticket->unlockTicket();
            }
            flashMessage($ticket, $ticketmessage);
            header("Location:tickets.php?page=show&id=" . $ticket_id);
            exit;
        }
        break;
    case "show":
        if(isset($_POST["Comment"]) && U_TICKET_EDIT) {
            $ticket = new ticket();
            $ticket->changeComment($ticket_id, esc($_POST["Comment"]));
        }
        if(isset($_GET["action"]) && $_GET["action"] == "toggle_order") {
            $account->toggleTicketOrder();
            header("Location:tickets.php?page=show&id=" . $ticket_id);
            exit;
        }
        break;
    case "delete":
        if(!U_TICKET_DELETE) {
        } else {
            if(isset($ticket_id) && isset($_POST["imsure"]) && $_POST["imsure"] == "yes") {
                $ticket = new ticket();
                $ticket->Identifier = $ticket_id;
                $ticket->delete();
            }
            flashMessage($ticket);
            header("Location: tickets.php");
            exit;
        }
        break;
    default:
        if(isset($_POST["id"])) {
            if(!empty($_POST["id"])) {
                switch ($_POST["action"]) {
                    case "dialog:closeTicket":
                        if(!U_TICKET_EDIT) {
                        } else {
                            $ticket = new ticket();
                            foreach ($_POST["id"] as $key => $id) {
                                $ticket->Identifier = esc($id);
                                $ticket->changeStatus(3);
                            }
                            $ticket->Success[] = sprintf(__("one or more tickets are closed"), count($_POST["id"]));
                        }
                        break;
                    case "dialog:removeTicket":
                        if(!U_TICKET_DELETE) {
                        } else {
                            $ticket = new ticket();
                            foreach ($_POST["id"] as $key => $id) {
                                $ticket->Identifier = esc($id);
                                $ticket->delete();
                            }
                        }
                        break;
                    default:
                        unset($_POST["id"]);
                }
            } elseif(isset($_POST["action"])) {
                $ticket->Warning[] = __("nothing selected");
            }
            flashMessage($ticket);
            if(isset($_GET["from_page"]) && !empty($_GET["from_page"])) {
                switch ($_GET["from_page"]) {
                    case "debtor":
                        $_SESSION["selected_tab"] = 6;
                        header("Location: debtors.php?page=show&id=" . intval($_GET["from_id"]));
                        exit;
                        break;
                }
            }
            header("Location: tickets.php");
            exit;
        }
        switch ($page) {
            case "show":
                $ticket = isset($ticket) && is_object($ticket) ? $ticket : new ticket();
                $ticket->Identifier = $ticket_id;
                if(!$ticket->show()) {
                    flashMessage($ticket);
                    header("Location: tickets.php");
                    exit;
                }
                $ticket->isTicketLocked();
                $ticketmessage = new ticketmessage();
                $fields = ["TicketID", "Subject", "Message", "SenderName", "SenderEmail", "SenderID", "Date", "Tstatus", "Attachments", "Name"];
                $message_list = $ticketmessage->all($fields, "Date", $account->TicketOrder, "-1", "", "", $ticket->TicketID);
                require_once "class/debtor.php";
                if(0 < $ticket->Debtor) {
                    $debtor = new debtor();
                    $debtor->show($ticket->Debtor);
                }
                $message = parse_message($ticket, $ticketmessage, isset($debtor) ? $debtor : NULL);
                $wfh_page_title = __("show ticket") . " " . $ticket->TicketID;
                $current_page_url = "tickets.php?page=show&id" . $ticket_id;
                $sidebar_template = "ticket.sidebar.php";
                require_once "views/ticket.show.php";
                break;
            case "add":
                checkRight(U_TICKET_ADD);
                if(!isset($ticket) || !is_object($ticket)) {
                    $ticket = new ticket();
                    $ticketmessage = new ticketmessage();
                    $ticket->TicketID = $ticket->newTicketID();
                }
                require_once "class/employee.php";
                $employee = new employee();
                $fields = ["Name"];
                $employees = $employee->all($fields);
                require_once "class/debtor.php";
                $debtor = isset($debtor) && is_object($debtor) ? $debtor : new debtor();
                $fields = ["Initials", "SurName", "CompanyName", "DebtorCode", "EmailAddress"];
                $debtors = $debtor->all($fields);
                if(isset($_GET["debtor"]) && 0 < $_GET["debtor"]) {
                    $ticket->Debtor = intval(esc($_GET["debtor"]));
                    $debtor->Identifier = intval(esc($_GET["debtor"]));
                    $debtor->show();
                    $ticket->EmailAddress = $debtor->EmailAddress;
                }
                $message = parse_message($ticket, $debtor);
                $wfh_page_title = __("create ticket");
                $sidebar_template = "ticket.sidebar.php";
                require_once "views/ticket.add.php";
                break;
            case "edit":
                checkRight(U_TICKET_EDIT);
                if(!isset($ticket) || !is_object($ticket)) {
                    $ticket = new ticket();
                }
                $ticket->Identifier = $ticket_id;
                $ticket->show();
                require_once "class/employee.php";
                $employee = new employee();
                $fields = ["Name"];
                $employees = $employee->all($fields);
                require_once "class/debtor.php";
                $debtor = isset($debtor) && is_object($debtor) ? $debtor : new debtor();
                $fields = ["Initials", "SurName", "CompanyName", "DebtorCode", "EmailAddress"];
                $debtors = $debtor->all($fields);
                if(0 < $ticket->Debtor) {
                    $debtor->Identifier = $ticket->Debtor;
                    $debtor->show();
                }
                $message = parse_message($ticket, $debtor);
                $wfh_page_title = __("edit ticket");
                $current_page_url = "tickets.php?page=edit&id" . $ticket_id;
                $sidebar_template = "ticket.sidebar.php";
                require_once "views/ticket.edit.php";
                break;
            default:
                $ticket = new ticket();
                $session = isset($_SESSION["ticket.overview"]) ? $_SESSION["ticket.overview"] : [];
                $fields = ["TicketID", "Debtor", "CompanyName", "SurName", "Initials", "Subject", "Owner", "Priority", "Status", "Number", "LastDate", "LastName", "Name"];
                $sort = isset($session["sort"]) ? $session["sort"] : "TicketID";
                $order = isset($session["order"]) ? $session["order"] : "DESC";
                $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) ? $session["limit"] : "1");
                $searchat = isset($session["searchat"]) ? $session["searchat"] : "";
                $searchfor = isset($session["searchfor"]) ? $session["searchfor"] : "";
                $selectgroup = isset($session["status"]) ? $session["status"] : "0|1|2";
                $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : MAX_RESULTS_LIST);
                $list_tickets = $ticket->all($fields, $sort, $order, $limit, $searchat, $searchfor, $selectgroup, $show_results);
                if(isset($list_tickets["CountRows"]) && ($list_tickets["CountRows"] < $show_results * ($limit - 1) || 1 < $limit && $list_tickets["CountRows"] == $show_results * ($limit - 1))) {
                    $newPage = ceil($list_tickets["CountRows"] / $show_results);
                    if($newPage <= 0) {
                        $newPage = 1;
                    }
                    $_SESSION["ticket.overview"]["limit"] = $newPage;
                    header("Location: tickets.php");
                    exit;
                }
                $_SESSION["ticket.overview"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "status" => $selectgroup, "searchat" => $searchat, "searchfor" => $searchfor, "limit" => $limit];
                $current_page = $limit;
                $message = parse_message($ticket);
                $wfh_page_title = __("ticket overview");
                $current_page_url = "tickets.php";
                $sidebar_template = "ticket.sidebar.php";
                require_once "views/ticket.overview.php";
        }
}

?>