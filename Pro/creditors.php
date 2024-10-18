<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "config.php";
checkRight(U_CREDITOR_SHOW);
require_once "class/creditor.php";
require_once "class/group.php";
require_once "class/creditinvoice.php";
require_once "class/attachment.php";
$page = isset($_GET["page"]) ? $_GET["page"] : "overview";
$creditor_id = isset($_GET["id"]) ? intval(esc($_GET["id"])) : (isset($_POST["id"]) ? intval(esc($_POST["id"])) : NULL);
switch ($page) {
    case "confirmDelete":
        $pagetype = "confirmDelete";
        $page = "show";
        break;
    case "overview_creditinvoice":
        if(isset($_POST["action"])) {
            switch ($_POST["action"]) {
                case "dialog:delete_creditinvoice_table":
                    if(!U_CREDITOR_INVOICE_DELETE) {
                    } elseif(isset($_POST["id"]) && is_array($_POST["id"])) {
                        foreach ($_POST["id"] as $key => $id) {
                            $creditinvoice = new creditinvoice();
                            $creditinvoice->Identifier = esc($id);
                            $creditinvoice->show();
                            $creditinvoice->delete();
                            flashMessage($creditinvoice);
                        }
                    }
                    break;
                case "markAsPaid":
                    if(!U_CREDITOR_INVOICE_EDIT) {
                    } elseif(isset($_POST["id"]) && is_array($_POST["id"])) {
                        foreach ($_POST["id"] as $key => $id) {
                            $creditinvoice = new creditinvoice();
                            $creditinvoice->Identifier = $id;
                            $creditinvoice->show();
                            if($creditinvoice->Status <= 2) {
                                $creditinvoice->markaspaid();
                                flashMessage($creditinvoice);
                            }
                        }
                    }
                    break;
                case "markAsNotPaid":
                    if(!U_CREDITOR_INVOICE_EDIT) {
                    } elseif(isset($_POST["id"]) && is_array($_POST["id"])) {
                        foreach ($_POST["id"] as $key => $id) {
                            $creditinvoice = new creditinvoice();
                            $creditinvoice->Identifier = $id;
                            $creditinvoice->show();
                            $creditinvoice->markasnotpaid();
                            flashMessage($creditinvoice);
                        }
                    }
                    break;
                case "receivedInvoice":
                    if(!U_CREDITOR_INVOICE_EDIT) {
                    } elseif(isset($_POST["id"]) && is_array($_POST["id"])) {
                        foreach ($_POST["id"] as $key => $id) {
                            $creditinvoice = new creditinvoice();
                            $creditinvoice->Identifier = $id;
                            $creditinvoice->show();
                            $creditinvoice->receivedInvoice();
                            flashMessage($creditinvoice);
                        }
                    }
                    break;
                default:
                    flashMessage();
                    header("Location: creditors.php?page=overview_creditinvoice");
                    exit;
            }
        } else {
            if(isset($_GET["action"])) {
                switch ($_GET["action"]) {
                    case "creditinvoice_delete":
                        if(!U_CREDITOR_INVOICE_DELETE) {
                        } elseif(isset($_GET["id"])) {
                            require_once "class/creditinvoice.php";
                            $creditinvoice = new creditinvoice();
                            $creditinvoice->Identifier = $_GET["id"];
                            $creditinvoice->show();
                            $creditinvoice->delete();
                            flashMessage($creditinvoice);
                        }
                        break;
                }
            }
            if(isset($_GET["from_page"]) && !empty($_GET["from_page"]) && (!isset($_SESSION["flashMessage"]["Error"]) || empty($_SESSION["flashMessage"]["Error"]))) {
                flashMessage($creditinvoice);
                switch ($_GET["from_page"]) {
                    case "creditor":
                        header("Location: creditors.php?page=show&id=" . intval($_GET["from_id"]));
                        exit;
                        break;
                    case "home":
                        header("Location: index.php");
                        exit;
                        break;
                }
            }
        }
        break;
    case "add_creditor":
        if(!U_CREDITOR_EDIT) {
        } else {
            $creditor = new creditor();
            $_POST["Authorisation"] = isset($_POST["Authorisation"]) ? "yes" : "no";
            foreach ($_POST as $key => $value) {
                if(in_array($key, $creditor->Variables)) {
                    $creditor->{$key} = esc($value);
                }
            }
            if(IS_INTERNATIONAL) {
                $creditor->State = isset($_POST["StateCode"]) && $_POST["StateCode"] ? esc($_POST["StateCode"]) : $creditor->State;
            }
            $creditor->Groups = isset($_POST["Groups"]) ? $_POST["Groups"] : [];
            if(isset($_POST["File"]) && is_array($_POST["File"])) {
                $creditor->Attachment = [];
                if(!empty($_POST["File"])) {
                    require_once "class/attachment.php";
                    $attachment = new attachment();
                    foreach ($_POST["File"] as $id => $Attachemtfilename) {
                        $attachmentInfo = $attachment->getAttachmentInfo($Attachemtfilename);
                        if($attachmentInfo !== false) {
                            $creditor->Attachment[] = $attachmentInfo;
                        }
                    }
                }
            }
            if($creditor->add()) {
                $files = !isset($_POST["File"]) || !is_array($_POST["File"]) ? [] : $_POST["File"];
                if(empty($files)) {
                    $attachment = [];
                } else {
                    $Param = ["Identifier" => $creditor->Identifier, "Files" => $files, "Type" => "creditor"];
                    require_once "class/attachment.php";
                    $attachment = new attachment();
                    $attachment->checkAttachments($Param);
                }
                flashMessage($creditor);
                header("Location: creditors.php?page=show&id=" . $creditor->Identifier);
                exit;
            }
            $page = "add";
            foreach ($_POST as $key => $value) {
                if(in_array($key, $creditor->Variables)) {
                    $creditor->{$key} = htmlspecialchars($creditor->{$key});
                }
            }
            $tmp_group = [];
            foreach ($creditor->Groups as $k => $v) {
                $tmp_group[$v] = $v;
            }
            $creditor->Groups = $tmp_group;
        }
        break;
    case "edit_creditor":
        if(!U_CREDITOR_EDIT) {
        } elseif(isset($_POST["id"])) {
            $creditor = new creditor();
            $creditor->Identifier = $creditor_id;
            $creditor->show();
            $_POST["Authorisation"] = isset($_POST["Authorisation"]) ? "yes" : "no";
            foreach ($_POST as $key => $value) {
                if(in_array($key, $creditor->Variables)) {
                    $creditor->{$key} = esc($value);
                }
            }
            if(IS_INTERNATIONAL) {
                $creditor->State = isset($_POST["StateCode"]) && $_POST["StateCode"] ? esc($_POST["StateCode"]) : $creditor->State;
            }
            $creditor->Groups = isset($_POST["Groups"]) ? $_POST["Groups"] : [];
            if(isset($_POST["File"]) && is_array($_POST["File"])) {
                $creditor->Attachment = [];
                if(!empty($_POST["File"])) {
                    require_once "class/attachment.php";
                    $attachment = new attachment();
                    foreach ($_POST["File"] as $id => $Attachemtfilename) {
                        $attachmentInfo = $attachment->getAttachmentInfo($Attachemtfilename);
                        if($attachmentInfo !== false) {
                            $creditor->Attachment[] = $attachmentInfo;
                        }
                    }
                }
            }
            if($creditor->edit()) {
                $files = !isset($_POST["File"]) || !is_array($_POST["File"]) ? [] : $_POST["File"];
                if(empty($files)) {
                    $attachment = [];
                } else {
                    $Param = ["Identifier" => $creditor->Identifier, "Files" => $files, "Type" => "creditor"];
                    require_once "class/attachment.php";
                    $attachment = new attachment();
                    $attachment->checkAttachments($Param);
                }
                flashMessage($creditor);
                header("Location: creditors.php?page=show&id=" . $creditor_id);
                exit;
            }
            $page = "edit";
            foreach ($_POST as $key => $value) {
                if(in_array($key, $creditor->Variables)) {
                    $creditor->{$key} = htmlspecialchars($creditor->{$key});
                }
            }
            $tmp_group = [];
            foreach ($creditor->Groups as $k => $v) {
                $tmp_group[$v] = $v;
            }
            $creditor->Groups = $tmp_group;
        }
        break;
    case "delete_creditor":
        if(!U_CREDITOR_DELETE) {
        } else {
            if(isset($_GET["id"])) {
                $creditor = new creditor();
                $creditor->Identifier = intval($_GET["id"]);
                if(isset($_POST["withcreditinvoice"]) && $_POST["withcreditinvoice"] == "delete") {
                    $ci = new creditinvoice();
                    $fields = ["Creditor"];
                    $ci = $ci->all(["id"], false, false, -1, "Creditor", $_GET["id"], false);
                    foreach ($ci as $key => $elem) {
                        if(is_numeric($key)) {
                            $inv = new creditinvoice();
                            $inv->Identifier = $key;
                            $inv->delete();
                        }
                    }
                }
                $creditor->delete();
                flashMessage($creditor);
            }
            $page = "overview";
        }
        break;
    case "edit_creditinvoice":
        if(!U_CREDITOR_INVOICE_EDIT) {
        } else {
            $attachment = new attachment();
            $creditinvoice = new creditinvoice();
            Database_Model::getInstance()->beginTransaction();
            $creditorError = false;
            if(isset($_POST["id"]) && 1 <= $_POST["id"]) {
                $creditinvoice->Identifier = intval($_POST["id"]);
                $creditinvoice->show();
            }
            $_POST["Authorisation"] = isset($_POST["Authorisation"]) && in_array($_POST["Status"], [0, 1, 3]) ? "yes" : "no";
            foreach ($_POST as $post_key => $post_value) {
                if(in_array($post_key, $creditinvoice->Variables)) {
                    $post_value = is_string($post_value) ? esc(trim($post_value)) : $post_value;
                    $creditinvoice->{$post_key} = $post_value;
                }
            }
            $creditor = new creditor();
            if($creditinvoice->Creditor == "new") {
                $creditor->CreditorCode = $creditor->newCreditorCode();
                $creditor->CompanyName = isset($_POST["CompanyName"]) ? trim(esc($_POST["CompanyName"])) : "";
                $creditor->Sex = isset($_POST["Sex"]) ? trim(esc($_POST["Sex"])) : "";
                $creditor->Initials = isset($_POST["Initials"]) ? trim(esc($_POST["Initials"])) : "";
                $creditor->SurName = isset($_POST["SurName"]) ? trim(esc($_POST["SurName"])) : "";
                $creditor->Address = isset($_POST["Address"]) ? trim(esc($_POST["Address"])) : "";
                $creditor->ZipCode = isset($_POST["ZipCode"]) ? trim(esc($_POST["ZipCode"])) : "";
                $creditor->City = isset($_POST["City"]) ? trim(esc($_POST["City"])) : "";
                $creditor->Country = isset($_POST["Country"]) ? trim(esc($_POST["Country"])) : "";
                $creditor->Authorisation = esc($_POST["Authorisation"]);
                $creditor->InvoiceTerm = intval($_POST["Term"]);
                $creditor->MyCustomerCode = isset($_POST["MyCustomerCode"]) ? trim(esc($_POST["MyCustomerCode"])) : "";
                $creditor->AccountNumber = isset($_POST["AccountNumber"]) ? trim(esc($_POST["AccountNumber"])) : "";
                if($creditor->add()) {
                    $creditinvoice->Creditor = $creditor->Identifier;
                    $newCreditor = true;
                } else {
                    $creditinvoice->Creditor = "new";
                    $creditorError = true;
                }
            }
            if($creditorError === false) {
                $added_lines = [];
                if(!$creditinvoice->is_free(esc($_POST["CreditInvoiceCode"]))) {
                    $creditinvoice->CreditInvoiceCode = $creditinvoice->newCreditInvoiceCode();
                }
                $creditinvoice->Attachment = [];
                if(isset($_POST["File"])) {
                    $creditinvoice->Attachment = buildAttachmentArray($_POST["File"]);
                }
                $errors_lines = false;
                for ($i = 0; $i <= $_POST["NumberOfElements"]; $i++) {
                    $invoice_element = new creditinvoiceelement();
                    $invoice_element->CreditInvoiceCode = $creditinvoice->CreditInvoiceCode;
                    $invoice_element->Creditor = $creditinvoice->Creditor;
                    $invoice_element->Number = esc($_POST["Number"][$i]);
                    $invoice_element->Description = esc($_POST["Description"][$i]);
                    $invoice_element->PriceExcl = esc($_POST["PriceExcl"][$i]);
                    $invoice_element->TaxPercentage = esc(deformat_money($_POST["TaxPercentage"][$i]) / 100);
                    if(empty($_POST["LineID"][$i])) {
                        if(!(is_numeric(number2db($invoice_element->Number)) && isEmptyFloat(number2db(esc($invoice_element->Number)))) && $invoice_element->Description != "") {
                            $result_elements = $invoice_element->add();
                            if($result_elements) {
                                $added_lines[] = $invoice_element->Identifier;
                            }
                        }
                    } else {
                        $invoice_element->Identifier = esc($_POST["LineID"][$i]);
                        $result_elements = $invoice_element->edit();
                    }
                    if(isset($result_elements) && $result_elements === false) {
                        $errors_lines = true;
                        $creditinvoice->Error = array_merge($creditinvoice->Error, $invoice_element->Error);
                        $creditinvoice->Warning = array_merge($creditinvoice->Warning, $invoice_element->Warning);
                    }
                }
                if(!isset($_POST["invoiceHasPrivatePart"]) || $_POST["invoiceHasPrivatePart"] != "yes") {
                    $creditinvoice->Private = 0;
                    $creditinvoice->PrivatePercentage = 0;
                }
                if($errors_lines === false) {
                    if(empty($creditinvoice->Identifier)) {
                        if(isset($_POST["total-incl"])) {
                            $creditinvoice->AmountIncl = deformat_money($_POST["total-incl"]);
                        }
                        $result = $creditinvoice->add();
                        if($result !== true && $creditinvoice->Location) {
                            @unlink(DIR_CREDIT_INVOICES . "/" . $creditinvoice->Location);
                            $creditinvoice->Location = "";
                        }
                    } else {
                        if(isset($_POST["total-incl"])) {
                            $creditinvoice->AmountIncl = deformat_money($_POST["total-incl"]);
                        }
                        $result = $creditinvoice->edit();
                    }
                }
            }
            if(isset($_POST["CreateAnother"]) && $_POST["CreateAnother"] == "yes") {
                $_SESSION["CreateAnother"] = "yes";
            } else {
                unset($_SESSION["CreateAnother"]);
            }
            if($result === true && $errors_lines === false && $creditorError === false) {
                if(defined("IS_DEMO") && IS_DEMO && isset($_POST["File"]) && 1 <= strlen($_POST["File"][0])) {
                    $creditinvoice->Warning[] = __("demo - not able to add attachments");
                } else {
                    $FileArray = isset($_POST["File"][0]) && is_numeric($_POST["File"][0]) ? $_POST["File"] : [];
                    $Param = ["Identifier" => $creditinvoice->Identifier, "Files" => $FileArray, "Type" => "creditinvoice"];
                    $attachment->checkAttachments($Param);
                    $countAttachments = $attachment->getAttachments($creditinvoice->Identifier, "creditinvoice");
                    if(!empty($countAttachments) && strlen($creditinvoice->Location) && empty($creditinvoice->Error)) {
                        @unlink(DIR_CREDIT_INVOICES . $creditinvoice->Location);
                        $creditinvoice->Location = "";
                        $creditinvoice->edit();
                        $creditinvoice->Success = array_unique($creditinvoice->Success);
                    }
                }
                Database_Model::getInstance()->commit();
                flashMessage($creditinvoice, $attachment);
                if(isset($_POST["CreateAnother"]) && $_POST["CreateAnother"] == "yes") {
                    header("Location: creditors.php?page=add_invoice&creditor=" . $creditinvoice->Creditor);
                    exit;
                }
                header("Location: creditors.php?page=show_invoice&id=" . $creditinvoice->Identifier);
                exit;
            }
            if($newCreditor) {
                $creditinvoice->Creditor = "new";
            }
            if(0 < $creditinvoice->Creditor) {
                $CreditorID = $creditinvoice->Creditor;
            }
            $page = "add_invoice";
            Database_Model::getInstance()->rollBack();
            $creditor->Success = [];
            $invoice_element = new creditinvoiceelement();
            $creditinvoice->Elements = $invoice_element->all($creditinvoice->CreditInvoiceCode);
            if(isset($_POST["LineID"])) {
                foreach ($_POST as $post_key => $post_value) {
                    if(in_array($post_key, $creditinvoice->Variables)) {
                        $creditinvoice->{$post_key} = htmlspecialchars(esc($post_value));
                    }
                }
            }
            foreach ($_POST as $key => $value) {
                if(in_array($key, $creditor->Variables)) {
                    $creditor->{$key} = htmlspecialchars($creditor->{$key});
                }
            }
        }
        break;
    case "delete_invoice":
        if(!U_CREDITOR_INVOICE_DELETE) {
        } elseif(isset($_GET["id"]) && !is_array($_GET["id"])) {
            $invoice = new creditinvoice();
            $invoice->Identifier = intval($_GET["id"]);
            $invoice->show();
            $invoice->delete();
            flashMessage($invoice);
        }
        break;
    case "add_group":
        if(!U_CREDITOR_EDIT) {
        } elseif(isset($_POST["GroupName"])) {
            require_once "class/group.php";
            $group = new group();
            $group->Type = "creditor";
            if(isset($creditor_id) && 0 < $creditor_id) {
                $group->Identifier = $creditor_id;
                $group->show();
                $group->Products = isset($_POST["Groups"]) && 1 < strlen($_POST["Groups"]) ? array_unique(explode(",", substr($_POST["Groups"], 1, -1))) : [];
            } else {
                $group->Products = isset($_POST["Groups"]) && 1 < strlen($_POST["Groups"]) ? array_unique(explode(",", substr($_POST["Groups"], 1, -1))) : [];
            }
            $group->GroupName = esc($_POST["GroupName"]);
            if(0 < $group->Identifier) {
                $group->Creditors = [];
                if($group->edit()) {
                    $page = "show_group";
                    $creditor_id = $group->Identifier;
                } else {
                    $group->Creditors = $group->Products;
                    $group->GroupName = htmlspecialchars($group->GroupName);
                }
            } else {
                $group->Type = "creditor";
                if($group->add()) {
                    $page = "show_group";
                    $creditor_id = $group->Identifier;
                } else {
                    $group->Creditors = $group->Products;
                    $group->GroupName = htmlspecialchars($group->GroupName);
                }
            }
        }
        break;
    case "delete_group":
        $page = "show_group";
        if(empty($_POST) || !U_CREDITOR_DELETE) {
        } elseif(isset($_POST["imsure"]) && $_POST["imsure"] == "yes") {
            require_once "class/group.php";
            $group = new group();
            $group->Type = "creditor";
            $group->Identifier = $creditor_id;
            if($group->delete()) {
                flashMessage($group);
                header("Location: creditors.php?page=groups");
                exit;
            }
        }
        break;
    case "show_invoice":
        if(isset($creditor_id) && isset($_GET["action"])) {
            switch ($_GET["action"]) {
                case "markaspaid":
                    if(!U_CREDITOR_INVOICE_EDIT) {
                    } else {
                        $creditinvoice = new creditinvoice();
                        $creditinvoice->Identifier = $creditor_id;
                        $creditinvoice->show();
                        if($creditinvoice->Status != 3) {
                            if(!empty($_POST["PayDate"])) {
                                $creditinvoice->markaspaid(rewrite_date_site2db($_POST["PayDate"]));
                            } else {
                                $creditinvoice->markaspaid();
                            }
                        }
                    }
                    break;
                case "download":
                    $creditinvoice = new creditinvoice();
                    $creditinvoice->Identifier = $creditor_id;
                    $creditinvoice->show();
                    $creditinvoice->download();
                    break;
                case "partialpayment":
                    if(!U_CREDITOR_INVOICE_EDIT) {
                    } else {
                        $creditinvoice = new creditinvoice();
                        $creditinvoice->Identifier = $creditor_id;
                        $creditinvoice->show();
                        $creditinvoice->partpayment(esc($_POST["AmountPaid"]));
                    }
                    break;
                default:
                    flashMessage($creditinvoice);
                    header("Location:creditors.php?page=show_invoice&id=" . $creditor_id);
                    exit;
            }
        } elseif(isset($_POST["action"])) {
            switch ($_POST["action"]) {
                case "removelogentry":
                    require_once "class/logfile.php";
                    $logfile = new logfile();
                    $list_log = isset($_POST["logentry"]) && is_array($_POST["logentry"]) ? $_POST["logentry"] : [];
                    foreach ($list_log as $log_id) {
                        $logfile->deleteEntry($log_id);
                    }
                    $creditinvoice->Error = array_merge($creditinvoice->Error, $logfile->Error);
                    if(empty($creditinvoice->Error)) {
                        $creditinvoice->Success[] = sprintf(__("removed count logentries"), count($list_log));
                    }
                    break;
                default:
                    flashMessage($creditinvoice);
                    header("Location:creditors.php?page=show_invoice&id=" . $creditor_id);
                    exit;
            }
        }
        break;
    case "show":
        require_once "class/creditor.php";
        $creditor = isset($creditor) && is_object($creditor) ? $creditor : new creditor();
        if(!$creditor->show($creditor_id, true)) {
            flashMessage($creditor);
            header("Location: creditors.php");
            exit;
        }
        if($creditor->Status == 9) {
            $creditor->Warning[] = __("creditor is deleted, no actions available");
        }
        if(U_CREDITOR_INVOICE_SHOW) {
            $invoice = new creditinvoice();
            $session = isset($_SESSION["creditor.show.invoice"]) ? $_SESSION["creditor.show.invoice"] : [];
            $fields = ["CreditInvoiceCode", "InvoiceCode", "CompanyName", "Initials", "SurName", "Creditor", "Date", "Term", "PayDate", "Status", "AmountExcl", "AmountIncl", "AmountPaid", "Location", "Authorisation", "ReferenceNumber"];
            $sort = isset($session["sort"]) ? $session["sort"] : "Date` DESC, `CreditInvoiceCode";
            $order = isset($session["order"]) ? $session["order"] : "ASC";
            $searchat = "Creditor";
            $searchfor = $creditor->Identifier;
            $selectgroup = implode("|", array_keys($array_creditinvoicestatus));
            $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) ? $session["limit"] : "1");
            $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : min(25, MAX_RESULTS_LIST));
            $invoices = $invoice->all($fields, $sort, $order, $limit, $searchat, $searchfor, $selectgroup, $show_results);
            if(isset($invoices["CountRows"]) && $invoices["CountRows"] < $show_results * ($limit - 1)) {
                $limit = 1;
                $invoices = $invoice->all($fields, $sort, $order, $limit, $searchat, $searchfor, $selectgroup, $show_results);
            }
            $_SESSION["creditor.show.invoice"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "group" => $selectgroup, "searchat" => $searchat, "searchfor" => $searchfor, "limit" => $limit];
            $current_page = $limit;
            $ci = $invoice->all(["id"], false, false, -1, $searchat, $searchfor, "0|1|2");
            $numberOfUnpaidInvoices = $ci["CountRows"];
            unset($ci);
            require_once "class/bankstatement.php";
            $transaction_matches = new Transaction_Matches_Model();
            $transaction_matches_options = $transaction_matches->getTransactionMatchesTable("creditor", $creditor->Identifier);
        }
        require_once "class/attachment.php";
        $attachment = new attachment();
        $creditor->Attachment = $attachment->getAttachments($creditor_id, "creditor");
        $groups = new group();
        $groups->Type = "creditor";
        $fields = ["GroupName"];
        $groups = $groups->all($fields);
        $message = parse_message(isset($invoice) ? $invoice : NULL, $creditor, $groups);
        $wfh_page_title = __("creditor") . " " . ($creditor->CompanyName ? $creditor->CompanyName : $creditor->Initials . " " . $creditor->SurName);
        $current_page_url = "creditors.php?page=show&id=" . $creditor->Identifier;
        $sidebar_template = "creditor.sidebar.php";
        require_once "views/creditor.show.php";
        break;
    case "edit":
        checkRight(U_CREDITOR_EDIT);
        if(!isset($creditor) && !is_object($creditor) && isset($creditor_id) && 0 < $creditor_id) {
            $creditor = new creditor();
            $creditor->Identifier = $creditor_id;
            $creditor->show();
            if(empty($creditor->Attachment)) {
                require_once "class/attachment.php";
                $attachment = new attachment();
                $creditor->Attachment = $attachment->getAttachments($creditor_id, "creditor");
            }
        }
        $group = new group();
        $group->Type = "creditor";
        $fields = ["GroupName", "Creditors"];
        $groups = $group->all($fields);
        $message = parse_message($creditor, $group);
        $wfh_page_title = 0 < $creditor_id ? __("edit creditor") : __("add creditor");
        $sidebar_template = "creditor.sidebar.php";
        require_once "views/creditor.add.php";
        break;
    case "add":
        checkRight(U_CREDITOR_EDIT);
        require_once "class/creditor.php";
        if(!isset($creditor) || !is_object($creditor)) {
            $creditor = new creditor();
        }
        if($creditor->CreditorCode == "") {
            $creditor->CreditorCode = $creditor->newCreditorCode();
        }
        require_once "class/group.php";
        $group = new group();
        $group->Type = "creditor";
        $fields = ["GroupName", "Products", "Creditors"];
        $groups = $group->all($fields);
        $message = parse_message($creditor, $group);
        $wfh_page_title = 0 < $creditor_id ? __("edit creditor") : __("add creditor");
        $sidebar_template = "creditor.sidebar.php";
        require_once "views/creditor.add.php";
        break;
    case "add_invoice":
        checkRight(U_CREDITOR_INVOICE_EDIT);
        if(!isset($creditinvoice) || !is_object($creditinvoice)) {
            $creditinvoice = new creditinvoice();
            $creditinvoice->CreditInvoiceCode = $creditinvoice->newCreditInvoiceCode();
            if(isset($_GET["id"]) && 0 < $_GET["id"]) {
                $creditinvoice->Identifier = intval($_GET["id"]);
                $creditinvoice->show();
            }
        }
        if(!$creditor) {
            $creditor = new creditor();
        }
        if(isset($_SESSION["CreateAnother"]) && $_SESSION["CreateAnother"] == "yes") {
            $_POST["CreateAnother"] = "yes";
        }
        if(isset($_GET["creditor"]) && 0 < $_GET["creditor"]) {
            $creditinvoice->Creditor = intval($_GET["creditor"]);
            $creditor->Identifier = intval($_GET["creditor"]);
            $creditor->show();
            if(empty($_POST)) {
                $creditinvoice->Authorisation = $creditor->Authorisation;
                $creditinvoice->Term = $creditor->Term;
            }
        } elseif(isset($_GET["clone"]) && 0 < $_GET["clone"]) {
            $creditinvoice->Identifier = intval(esc($_GET["clone"]));
            if($creditinvoice->show()) {
                $creditinvoice->CreditInvoiceCode = $creditinvoice->newCreditInvoiceCode();
                $creditinvoice->InvoiceCode = "";
                $creditinvoice->Status = 1;
                $creditinvoice->Identifier = 0;
                $creditinvoice->Date = rewrite_date_db2site(date("Y-m-d"));
                $creditinvoice->PayDate = "";
                $creditinvoice->AmountPaid = 0;
                $creditor->Identifier = $creditinvoice->Creditor;
                $creditor->show();
                $x = 0;
                foreach ($creditinvoice->Elements as $key => $data) {
                    if(is_numeric($key)) {
                        $_POST["Number"][$x] = $data["Number"];
                        $_POST["Description"][$x] = htmlspecialchars_decode($data["Description"]);
                        $_POST["TaxPercentage"][$x] = $data["TaxPercentage"] * 100;
                        $_POST["PriceExcl"][$x] = $data["PriceExcl"];
                        $x++;
                    }
                }
                $_POST["NumberOfElements"] = $x;
                $creditinvoice->Elements = [];
            }
        } elseif(isset($_GET["UBL"]) && $_GET["UBL"]) {
            $ubl_error = false;
            require_once "class/attachment.php";
            $attachment = new attachment();
            if(!($ubl_file = $attachment->getAttachmentInfo(intval(esc($_GET["UBL"]))))) {
                $creditinvoice->Warning[] = sprintf(__("the attachment can not be found"), "");
                $ubl_error = true;
            }
            if(!$ubl_error && !@file_exists(DIR_CREDIT_INVOICES . $ubl_file->FilenameServer)) {
                $creditinvoice->Warning[] = sprintf(__("the attachment can not be found"), "");
                $attachment->deleteAttachment($ubl_file->id);
                $ubl_error = true;
            }
            if($ubl_error === false) {
                require_once "class/ubl.php";
                $ubl = new UBL();
                $ubl_data = $ubl->importUBL(DIR_CREDIT_INVOICES . $ubl_file->FilenameServer);
                if($ubl_data === false) {
                    $creditinvoice->Error = array_merge($creditinvoice->Error, $ubl->Error);
                    $attachment->deleteAttachment($ubl_file->id);
                    $ubl_error = true;
                }
            }
            if($ubl_error === false) {
                $creditinvoice = $ubl->attachUBLtoCreditInvoice($ubl_data, $creditinvoice);
                $creditor = $creditinvoice->newCreditor;
                if(isset($creditinvoice->CreditorID)) {
                    $CreditorID = $creditinvoice->CreditorID;
                    $newCreditor = false;
                } else {
                    $newCreditor = true;
                }
                if(!empty($creditinvoice->Elements)) {
                    $x = 0;
                    foreach ($creditinvoice->Elements as $key => $data) {
                        if(is_numeric($key)) {
                            $_POST["Number"][$x] = $data["Number"];
                            $_POST["Description"][$x] = htmlspecialchars_decode($data["Description"]);
                            $_POST["TaxPercentage"][$x] = $data["TaxPercentage"];
                            $_POST["PriceExcl"][$x] = $data["PriceExcl"];
                            $x++;
                        }
                    }
                    $_POST["NumberOfElements"] = $x;
                    $creditinvoice->Elements = [];
                }
                if(isset($creditinvoice->AttachmentUBLPDF)) {
                    $attachment->FileType = "creditinvoice";
                    $attachment->FileDir = DIR_CREDIT_INVOICES;
                    $attachment->FilenameOriginal = strpos($creditinvoice->AttachmentUBLPDF["filename"], ".pdf") === false ? $creditinvoice->AttachmentUBLPDF["filename"] . ".pdf" : $creditinvoice->AttachmentUBLPDF["filename"];
                    $attachment->FileBase64 = $creditinvoice->AttachmentUBLPDF["base64"];
                    $attachment->saveBase64(true);
                    $attachment_id = $attachment->getAttachmentInfoByFilename($attachment->FilenameOriginal, "creditinvoice", 0);
                    $creditinvoice->Attachment[0] = new stdClass();
                    $creditinvoice->Attachment[0]->id = $attachment_id;
                    $creditinvoice->Attachment[0]->Filename = $attachment->FilenameOriginal;
                    $attachment->deleteAttachment($ubl_file->id);
                } else {
                    $creditinvoice->Attachment[0] = new stdClass();
                    $creditinvoice->Attachment[0]->id = $ubl_file->id;
                    $creditinvoice->Attachment[0]->Filename = $ubl_file->Filename;
                }
            }
        } elseif(0 < $creditinvoice->Creditor) {
            $creditor->Identifier = $creditinvoice->Creditor;
            $creditor->show();
        }
        if(0 < $creditinvoice->Identifier) {
        } elseif(empty($creditinvoice->CreditInvoiceCode)) {
            $creditinvoice->CreditInvoiceCode = $creditinvoice->newCreditInvoiceCode();
        }
        if(isset($CreditorID) && 0 < $CreditorID && $newCreditor !== true) {
            $creditinvoice->Creditor = $CreditorID;
            $creditor = new creditor();
            $creditor->Identifier = $creditinvoice->Creditor;
            $creditor->show();
            if(isset($_POST["MyCustomerCode"])) {
                $creditor->UBLMyCustomerCode = trim(esc($_POST["MyCustomerCode"]));
            }
            if(isset($_POST["AccountNumber"])) {
                $creditor->UBLAccountNumber = trim(esc($_POST["AccountNumber"]));
            }
            if(isset($creditinvoice->AccountNumber) && $creditinvoice->AccountNumber) {
                $creditor->UBLAccountNumber = $creditinvoice->AccountNumber;
            }
            if(isset($creditinvoice->MyCustomerCode) && $creditinvoice->MyCustomerCode) {
                $creditor->UBLMyCustomerCode = $creditinvoice->MyCustomerCode;
            }
            if(empty($_POST)) {
                $creditinvoice->Authorisation = $creditor->Authorisation;
                $creditinvoice->Term = 0 <= $creditor->InvoiceTerm ? $creditor->InvoiceTerm : "0";
            }
        }
        if($newCreditor === true && $creditorError === false) {
            $creditinvoice->Creditor = "new";
        }
        if(0 < $creditinvoice->Identifier) {
            $attachments = new attachment();
            $creditinvoice->Attachment = $attachments->getAttachments($creditinvoice->Identifier, "creditinvoice");
        }
        $list_creditors = $creditor->all_small();
        $page = "update_creditinvoice";
        $message = parse_message($creditor, $creditinvoice);
        $wfh_page_title = 0 < $creditinvoice->Identifier ? __("edit creditinvoice") : __("add creditinvoice");
        $sidebar_template = "creditor.sidebar.php";
        require_once "views/creditinvoice.add.php";
        break;
    case "show_invoice":
        checkRight(U_CREDITOR_INVOICE_SHOW);
        if(!isset($creditinvoice) && isset($_GET["id"])) {
            $creditinvoice = new creditinvoice();
            $creditinvoice->Identifier = intval($_GET["id"]);
        }
        $creditinvoice->show();
        $creditinvoice->format();
        $creditor = new creditor();
        $creditor->Identifier = $creditinvoice->Creditor;
        $creditor->show();
        require_once "class/bankstatement.php";
        $transaction_matches = new Transaction_Matches_Model();
        $transaction_matches_options = $transaction_matches->getTransactionMatchesTable("creditinvoice", $creditinvoice->Identifier);
        require_once "class/logfile.php";
        $logfile = new logfile();
        $session = isset($_SESSION["creditinvoice.show.logfile"]) ? $_SESSION["creditinvoice.show.logfile"] : [];
        $fields = ["Date", "Who", "Name", "Action", "Values", "Translate"];
        $sort = isset($session["sort"]) ? $session["sort"] : "Date";
        $order = isset($session["order"]) ? $session["order"] : "DESC";
        $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) ? $session["limit"] : "1");
        $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : min(25, MAX_RESULTS_LIST));
        $list_creditinvoice_logfile = $logfile->all($fields, $sort, $order, $limit, "creditinvoice", $creditinvoice->Identifier, $show_results);
        $_SESSION["creditinvoice.show.logfile"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "limit" => $limit];
        $current_page = $limit;
        if(isset($_GET["pagetype"])) {
            $pagetype = $_GET["pagetype"];
        }
        $message = parse_message($creditinvoice, $creditor);
        $attachments = new attachment();
        $creditinvoice->Attachment = $attachments->getAttachments($creditinvoice->Identifier, "creditinvoice");
        $wfh_page_title = __("show creditinvoice");
        $current_page_url = "creditors.php?page=show_invoice&amp;id=" . $creditinvoice->Identifier;
        $sidebar_template = "creditor.sidebar.php";
        require_once "views/creditinvoice.show.php";
        break;
    case "groups":
        require_once "class/group.php";
        $group = new group();
        $group->Type = "creditor";
        $session = isset($_SESSION["creditorgroup.overview"]) ? $_SESSION["creditorgroup.overview"] : [];
        $fields = ["GroupName", "Products", "Creditors"];
        $sort = isset($session["sort"]) ? $session["sort"] : "GroupName";
        $order = isset($session["order"]) ? $session["order"] : "ASC";
        $groups = $group->all($fields, $sort, $order);
        $_SESSION["creditorgroup.overview"] = ["sort" => $sort, "order" => $order];
        $message = parse_message($group);
        $wfh_page_title = __("creditorgroup overview");
        $current_page_url = "creditors.php?page=groups";
        $sidebar_template = "creditor.sidebar.php";
        require_once "views/creditorgroup.overview.php";
        break;
    case "add_group":
        checkRight(U_CREDITOR_EDIT);
        require_once "class/group.php";
        $group = isset($group) && is_object($group) ? $group : new group();
        $group->Type = "creditor";
        if(isset($creditor_id) && 0 < $creditor_id && empty($group->Error)) {
            $group->Identifier = $creditor_id;
            if(!$group->show()) {
                flashMessage($group);
                header("Location: creditors.php?page=groups");
                exit;
            }
        }
        require_once "class/creditor.php";
        $creditor = new creditor();
        $session = isset($_SESSION["creditorgroup.add"]) ? $_SESSION["creditorgroup.add"] : [];
        $fields = ["CreditorCode", "CompanyName", "Initials", "SurName", "EmailAddress", "Address", "ZipCode", "City", "Country"];
        $sort = isset($session["sort"]) ? $session["sort"] : "CreditorCode";
        $order = isset($session["order"]) ? $session["order"] : "ASC";
        $searchat = isset($session["searchat"]) ? $session["searchat"] : "CreditorCode|CompanyName|SurName|EmailAddress";
        $searchfor = isset($session["searchfor"]) ? $session["searchfor"] : "";
        $selectgroup = isset($session["group"]) ? $session["group"] : "";
        $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) && $session["limit"] ? $session["limit"] : "1");
        $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : min(25, MAX_RESULTS_LIST));
        $creditors = $creditor->all($fields, $sort, $order, $limit, $searchat, $searchfor, $selectgroup, $show_results);
        $_SESSION["creditorgroup.add"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "group" => $selectgroup, "searchat" => $searchat, "searchfor" => $searchfor, "limit" => $limit];
        $current_page = $limit;
        $message = parse_message($group, $creditor);
        if(0 < $group->Identifier) {
            $current_page_url = "creditors.php?page=add_group&amp;id=" . $group->Identifier;
        } else {
            $current_page_url = "creditors.php?page=add_group";
        }
        $wfh_page_title = 0 < $creditor_id ? __("edit creditorgroup") : __("add creditorgroup");
        $sidebar_template = "creditor.sidebar.php";
        require_once "views/creditorgroup.add.php";
        break;
    case "show_group":
        checkRight(U_CREDITOR_SHOW);
        require_once "class/group.php";
        $group = isset($group) && is_object($group) ? $group : new group();
        $group->Type = "creditor";
        $group->Identifier = $creditor_id;
        if(!$group->show()) {
            flashMessage($group);
            header("Location: creditors.php?page=groups");
            exit;
        }
        require_once "class/creditor.php";
        $creditor = new creditor();
        $session = isset($_SESSION["creditorgroup.show"]) ? $_SESSION["creditorgroup.show"] : [];
        $fields = ["CreditorCode", "CompanyName", "Initials", "SurName", "EmailAddress", "Address", "ZipCode", "City", "Country"];
        $sort = isset($session["sort"]) ? $session["sort"] : "CreditorCode";
        $order = isset($session["order"]) ? $session["order"] : "ASC";
        $searchat = isset($session["searchat"]) ? $session["searchat"] : "CreditorCode|CompanyName|SurName|EmailAddress";
        $searchfor = isset($session["searchfor"]) ? $session["searchfor"] : "";
        $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) && $session["limit"] ? $session["limit"] : "1");
        $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : min(25, MAX_RESULTS_LIST));
        $creditors = $creditor->all($fields, $sort, $order, $limit, $searchat, $searchfor, $group->Identifier, $show_results);
        if(isset($creditors["CountRows"]) && ($creditors["CountRows"] < $show_results * ($limit - 1) || 1 < $limit && $creditors["CountRows"] == $show_results * ($limit - 1))) {
            $newPage = ceil($creditors["CountRows"] / $show_results);
            if($newPage <= 0) {
                $newPage = 1;
            }
            $limit = $newPage;
            $creditors = $creditor->all($fields, $sort, $order, $limit, $searchat, $searchfor, $group->Identifier, $show_results);
        }
        $_SESSION["creditorgroup.show"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "searchat" => $searchat, "searchfor" => $searchfor, "limit" => $limit];
        $current_page = $limit;
        $message = parse_message($group, $creditor);
        $wfh_page_title = __("creditorgroup") . " " . $group->GroupName;
        $current_page_url = "creditors.php?page=show_group&amp;id=" . $group->Identifier;
        $sidebar_template = "creditor.sidebar.php";
        require_once "views/creditorgroup.show.php";
        break;
    case "overview_creditinvoice":
        checkRight(U_CREDITOR_INVOICE_SHOW);
        $invoice = new creditinvoice();
        $session = isset($_SESSION["creditinvoice.overview"]) ? $_SESSION["creditinvoice.overview"] : [];
        $fields = ["CreditInvoiceCode", "InvoiceCode", "CompanyName", "Initials", "SurName", "Creditor", "Date", "Term", "PayDate", "Status", "AmountExcl", "AmountIncl", "AmountPaid", "Location", "Authorisation", "ReferenceNumber"];
        $sort = isset($session["sort"]) ? $session["sort"] : "CreditInvoiceCode";
        $order = isset($session["order"]) ? $session["order"] : "DESC";
        $searchat = isset($session["searchat"]) ? $session["searchat"] : "";
        $searchfor = isset($session["searchfor"]) ? $session["searchfor"] : "";
        $selectgroup = isset($session["status"]) ? $session["status"] : "0|1|2|3|8";
        $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) ? $session["limit"] : "1");
        $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : MAX_RESULTS_LIST);
        $creditinvoices = $invoice->all($fields, $sort, $order, $limit, $searchat, $searchfor, $selectgroup, $show_results);
        if(isset($creditinvoices["CountRows"]) && ($creditinvoices["CountRows"] < $show_results * ($limit - 1) || 1 < $limit && $creditinvoices["CountRows"] == $show_results * ($limit - 1))) {
            $newPage = ceil($creditinvoices["CountRows"] / $show_results);
            if($newPage <= 0) {
                $newPage = 1;
            }
            $_SESSION["creditinvoice.overview"]["limit"] = $newPage;
            header("Location: creditors.php?page=overview_creditinvoice");
            exit;
        }
        $_SESSION["creditinvoice.overview"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "status" => $selectgroup, "searchat" => $searchat, "searchfor" => $searchfor, "limit" => $limit];
        $current_page = $limit;
        $message = parse_message($invoice);
        $wfh_page_title = __("creditinvoice overview");
        $current_page_url = "creditors.php?page=overview_creditinvoice";
        $sidebar_template = "creditor.sidebar.php";
        require_once "views/creditinvoice.overview.php";
        break;
    default:
        require_once "class/creditor.php";
        $creditor = new creditor();
        $session = isset($_SESSION["creditor.overview"]) ? $_SESSION["creditor.overview"] : [];
        $fields = ["CreditorCode", "CompanyName", "Initials", "SurName", "EmailAddress", "Groups", "PhoneNumber", "MobileNumber", "FaxNumber", "Sex", "Address", "ZipCode", "City", "Country"];
        $sort = isset($session["sort"]) ? $session["sort"] : "CreditorCode";
        $order = isset($session["order"]) ? $session["order"] : "ASC";
        $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) ? $session["limit"] : "1");
        $searchat = isset($session["searchat"]) ? $session["searchat"] : "DebtorCode|CompanyName|SurName|EmailAddress";
        $searchfor = isset($session["searchfor"]) ? $session["searchfor"] : "";
        $selectgroup = isset($session["group"]) ? $session["group"] : "";
        $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : MAX_RESULTS_LIST);
        $creditors = $creditor->all($fields, $sort, $order, $limit, $searchat, $searchfor, $selectgroup, $show_results);
        if(isset($creditors["CountRows"]) && ($creditors["CountRows"] < $show_results * ($limit - 1) || 1 < $limit && $creditors["CountRows"] == $show_results * ($limit - 1))) {
            $newPage = ceil($creditors["CountRows"] / $show_results);
            if($newPage <= 0) {
                $newPage = 1;
            }
            $_SESSION["creditor.overview"]["limit"] = $newPage;
            header("Location: creditors.php");
            exit;
        }
        $_SESSION["creditor.overview"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "group" => $selectgroup, "searchat" => $searchat, "searchfor" => $searchfor, "limit" => $limit];
        $current_page = $limit;
        require_once "class/group.php";
        $groups = new group();
        $groups->Type = "creditor";
        $fields = ["GroupName", "Products"];
        $group_list = $groups->all($fields);
        $message = parse_message($creditor, $groups);
        $wfh_page_title = __("creditor overview");
        $current_page_url = "creditors.php";
        $sidebar_template = "creditor.sidebar.php";
        require_once "views/creditor.overview.php";
}

?>