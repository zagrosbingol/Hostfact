<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "config.php";
checkRight(U_DEBTOR_SHOW);
$page = isset($_GET["page"]) ? $_GET["page"] : "";
$debtor_id = isset($_GET["id"]) ? intval(esc($_GET["id"])) : (isset($_POST["id"]) ? intval(esc($_POST["id"])) : NULL);
$page = $page == "edit" ? "add" : $page;
switch ($page) {
    case "add":
        $pagetype = isset($debtor_id) && 0 < $debtor_id ? "edit" : "add";
        if(empty($_POST) || !U_DEBTOR_EDIT) {
        } else {
            require_once "class/debtor.php";
            $debtor = new debtor();
            if(defined("IS_DEMO") && IS_DEMO && $pagetype == "edit" && $debtor_id == 1) {
                $debtor->Error[] = __("demo - not allowed to edit demo client");
            } else {
                if(isset($debtor_id) && 0 < $debtor_id) {
                    $debtor->Identifier = $debtor_id;
                    $debtor->show();
                    if($debtor->Status == 9 && $debtor->Anonymous == "yes") {
                        $debtor->Error[] = __("this debtor has been made anonymous and cannot be used again");
                    }
                }
                if($_POST["payment_mail_when_helper"] == "-1" || $_POST["payment_mail_when_helper"] == "") {
                    $_POST["PaymentMail"] = $_POST["payment_mail_when_helper"];
                } else {
                    $_POST["PaymentMail"] = isset($_POST["PaymentMail"]) ? implode("|", $_POST["PaymentMail"]) : "";
                }
                foreach ($_POST as $key => $value) {
                    if(in_array($key, $debtor->Variables)) {
                        $debtor->{$key} = esc($value);
                    }
                }
                $debtor->Password = passcrypt($debtor->Password);
                $debtor->WelcomeMail = isset($_POST["WelcomeMail"]) ? esc($_POST["WelcomeMail"]) : "";
                $debtor->SynchronizeEmail = isset($_POST["SynchronizeEmail"]) ? esc($_POST["SynchronizeEmail"]) : "";
                $debtor->SynchronizeAuth = isset($_POST["SynchronizeAuth"]) ? esc($_POST["SynchronizeAuth"]) : "";
                $debtor->SynchronizeAddress = isset($_POST["SynchronizeAddress"]) ? esc($_POST["SynchronizeAddress"]) : "";
                $debtor->SynchronizeHandles = isset($_POST["SynchronizeHandles"]) ? esc($_POST["SynchronizeHandles"]) : "";
                $debtor->ActiveLogin = isset($_POST["CustomerPanelAccess"]) ? "yes" : "no";
                $debtor->Groups = isset($_POST["Groups"]) ? esc($_POST["Groups"]) : [];
                if(isset($_POST["File"]) && is_array($_POST["File"])) {
                    $debtor->Attachment = [];
                    if(!empty($_POST["File"])) {
                        require_once "class/attachment.php";
                        $attachment = new attachment();
                        foreach ($_POST["File"] as $id => $Attachemtfilename) {
                            $attachmentInfo = $attachment->getAttachmentInfo($Attachemtfilename);
                            if($attachmentInfo !== false) {
                                $debtor->Attachment[] = $attachmentInfo;
                            }
                        }
                    }
                }
                if(IS_INTERNATIONAL) {
                    $debtor->State = isset($_POST["StateCode"]) && $_POST["StateCode"] ? esc($_POST["StateCode"]) : $debtor->State;
                    $debtor->InvoiceState = isset($_POST["InvoiceStateCode"]) && $_POST["InvoiceStateCode"] ? esc($_POST["InvoiceStateCode"]) : $debtor->InvoiceState;
                }
                if($debtor->SynchronizeHandles == "yes") {
                    require_once "class/handle.php";
                    $handle = new handle();
                    $matched_handles = $handle->lookupDebtorHandle($debtor->Identifier);
                }
                if(!isset($_POST["AbnormalInvoiceData"]) || $_POST["AbnormalInvoiceData"] != "on") {
                    $debtor->InvoiceCompanyName = $debtor->InvoiceInitials = $debtor->InvoiceSurName = $debtor->InvoiceAddress = $debtor->InvoiceZipCode = $debtor->InvoiceCity = $debtor->InvoiceCountry = $debtor->InvoiceEmailAddress = "";
                    $debtor->InvoiceCountry = $debtor->Country;
                    $debtor->InvoiceSex = $debtor->Sex;
                    if(IS_INTERNATIONAL) {
                        $debtor->InvoiceAddress2 = "";
                        global $array_states;
                        $debtor->InvoiceState = isset($array_states[$debtor->Country]) ? $debtor->InvoiceState = $debtor->State : ($debtor->InvoiceState = "");
                    }
                    $debtor->InvoiceDataForPriceQuote = "no";
                } elseif(isset($_POST["InvoiceDataForPriceQuote"]) && $_POST["InvoiceDataForPriceQuote"] == "yes") {
                    $debtor->InvoiceDataForPriceQuote = "yes";
                } else {
                    $debtor->InvoiceDataForPriceQuote = "no";
                }
                if(!isset($_POST["UseCustomNameservers"]) || $_POST["UseCustomNameservers"] != "yes") {
                    $debtor->DNS1 = $debtor->DNS2 = $debtor->DNS3 = "";
                }
                if(!isset($_POST["UseCustomReminderEmailAddress"]) || $_POST["UseCustomReminderEmailAddress"] != "yes") {
                    $debtor->ReminderEmailAddress = "";
                }
                if(!isset($_POST["CustomInvoiceTermCheckbox"]) || $_POST["CustomInvoiceTermCheckbox"] != "yes") {
                    $debtor->InvoiceTerm = "";
                }
                if(isset($_POST["CustomPeriodicInvoiceCheckbox"]) && $_POST["CustomPeriodicInvoiceCheckbox"] == "yes") {
                    $debtor->PeriodicInvoiceDays = esc($_POST["PERIODIC_INVOICE_DAYS"]);
                } else {
                    $debtor->PeriodicInvoiceDays = -1;
                }
                if(!isset($_POST["InvoiceAuthorisation"]) || $_POST["InvoiceAuthorisation"] != "yes") {
                    $debtor->InvoiceAuthorisation = "no";
                }
                if(!isset($_SESSION["custom_fields"]["debtor"]) || $_SESSION["custom_fields"]["debtor"]) {
                    $customfields_list = $_SESSION["custom_fields"]["debtor"];
                    $debtor->customvalues = [];
                    foreach ($customfields_list as $k => $custom_field) {
                        $debtor->customvalues[$custom_field["FieldCode"]] = isset($_POST["custom"][$custom_field["FieldCode"]]) ? esc($_POST["custom"][$custom_field["FieldCode"]]) : "";
                    }
                }
                if($pagetype == "add") {
                    $result = $debtor->add();
                } elseif($pagetype == "edit") {
                    $result = $debtor->edit();
                    if($result) {
                        if(isset($_POST["PleskClientID"])) {
                            $client_ids = explode("&", str_replace("server_", "", $_POST["PleskClientID"]));
                            $debtor->updatePleskClientIDs($client_ids);
                        }
                        if($debtor->SynchronizeHandles == "yes" && !empty($matched_handles)) {
                            $handle->syncDebtorToHandle($debtor_id, $matched_handles);
                        }
                        require_once "class/clientareachange.php";
                        $ClientareaChanges = new ClientareaChange_Model();
                        $options = [];
                        $options["filters"]["reference_type"] = "debtor";
                        $options["filters"]["reference_id"] = $debtor->Identifier;
                        $options["filters"]["debtor"] = $debtor->Identifier;
                        $options["filter"]["approval"] = "pending";
                        $options["filter"] = "pending|error";
                        $changes_result = $ClientareaChanges->listChanges($options);
                        if(!empty($changes_result)) {
                            foreach ($changes_result as $_change) {
                                $ClientareaChanges = new ClientareaChange_Model();
                                $ClientareaChanges->id = $_change->id;
                                $ClientareaChanges->show();
                                $ClientareaChanges->markAsExecutedAndApproved();
                                $debtor->Success = array_merge($debtor->Success, $ClientareaChanges->Success);
                                $debtor->Error = array_merge($debtor->Error, $ClientareaChanges->Error);
                                unset($ClientareaChanges);
                            }
                            if(isset($_POST["ClientareaChange"]) && $_POST["ClientareaChange"]) {
                                flashMessage($debtor);
                                header("Location: clientareachanges.php");
                                exit;
                            }
                            flashMessage($debtor);
                            header("Location: debtors.php?page=show&id=" . $debtor_id);
                            exit;
                        }
                    }
                }
                if($result) {
                    $debtor_id = $debtor->Identifier;
                    $files = !isset($_POST["File"]) || !is_array($_POST["File"]) ? [] : $_POST["File"];
                    if($pagetype == "add" && empty($files)) {
                        $attachment = [];
                    } else {
                        $Param = ["Identifier" => $debtor->Identifier, "Files" => $files, "Type" => "debtor"];
                        require_once "class/attachment.php";
                        $attachment = new attachment();
                        $attachment->checkAttachments($Param);
                    }
                    flashMessage($debtor);
                    header("Location: debtors.php?page=show&id=" . $debtor_id);
                    exit;
                }
                foreach ($debtor->Variables as $key) {
                    if(is_string($debtor->{$key})) {
                        $debtor->{$key} = htmlspecialchars($debtor->{$key});
                    }
                }
                $tmp_group = [];
                foreach ($debtor->Groups as $k => $v) {
                    $tmp_group[$v] = $v;
                }
                $debtor->Groups = $tmp_group;
            }
        }
        break;
    case "changelogindetails":
        if(empty($_POST) || !U_DEBTOR_EDIT) {
        } else {
            require_once "class/debtor.php";
            $debtor = new debtor();
            if(isset($debtor_id) && 0 < $debtor_id) {
                if(defined("IS_DEMO") && IS_DEMO && $debtor_id == 1) {
                    $debtor->Error[] = __("demo - not allowed to edit demo client");
                    flashMessage($debtor);
                    header("Location: debtors.php?page=show&id=" . $debtor_id);
                    exit;
                }
                $debtor->Identifier = $debtor_id;
                if($debtor->show()) {
                    $resend_details = isset($_POST["resend"]) && $_POST["resend"] == "yes" ? true : false;
                    $debtor->updateLoginDetails(esc($_POST["chg_Username"]), esc($_POST["chg_Password"]), $resend_details);
                    flashMessage($debtor);
                    header("Location: debtors.php?page=show&id=" . $debtor->Identifier);
                    exit;
                }
            }
        }
        break;
    case "add_group":
        if(!U_DEBTOR_EDIT) {
        } elseif(isset($_POST["GroupName"])) {
            require_once "class/group.php";
            $group = new group();
            if(isset($debtor_id) && 0 < $debtor_id) {
                $group->Identifier = $debtor_id;
                $group->show();
                $group->Products = isset($_POST["Groups"]) && 1 < strlen($_POST["Groups"]) ? array_unique(explode(",", substr($_POST["Groups"], 1, -1))) : [];
            } else {
                $group->Products = isset($_POST["Groups"]) && 1 < strlen($_POST["Groups"]) ? array_unique(explode(",", substr($_POST["Groups"], 1, -1))) : [];
            }
            $group->GroupName = esc($_POST["GroupName"]);
            if(0 < $group->Identifier) {
                $group->Debtors = [];
                if($group->edit()) {
                    $page = "show_group";
                    $debtor_id = $group->Identifier;
                } else {
                    $group->Debtors = $group->Products;
                    $group->GroupName = htmlspecialchars($group->GroupName);
                }
            } else {
                $group->Type = "debtor";
                if($group->add()) {
                    $page = "show_group";
                    $debtor_id = $group->Identifier;
                } else {
                    $group->Debtors = $group->Products;
                    $group->GroupName = htmlspecialchars($group->GroupName);
                }
            }
        }
        break;
    case "mailing":
        if(!U_DEBTOR_EDIT) {
        } else {
            require_once "class/email.php";
            $mail = new email();
            foreach ($_POST as $key => $value2) {
                if(is_string($value2)) {
                    $mail->{$key} = htmlspecialchars(esc($value2));
                }
            }
            if(isset($_POST["Message"]) && $_POST["Message"] && isset($_POST["Subject"]) && $_POST["Subject"] && isset($_SESSION["mailing.add"]["Recipients"]["CountRows"]) && 0 < $_SESSION["mailing.add"]["Recipients"]["CountRows"]) {
                $recipient_array = [];
                $receivers = $_SESSION["mailing.add"]["Recipients"];
                $firstTime = true;
                if(isset($_POST["SenderEmail"]) && !check_email_address(esc($_POST["SenderEmail"]), "single") || !isset($_POST["SenderEmail"])) {
                    $error_class->Error[] = __("invalid emailaddress sender");
                } else {
                    foreach ($receivers as $key => $value) {
                        if(is_numeric($value) && is_numeric($key)) {
                            if(isset($mail)) {
                                unset($mail);
                            }
                            $mail = new email();
                            foreach ($_POST as $key => $value2) {
                                $mail->{$key} = esc($value2);
                            }
                            $mail->Sender = esc($_POST["SenderName"]) . " <" . esc($_POST["SenderEmail"]) . ">";
                            $mail->Status = 0;
                            require_once "class/debtor.php";
                            $debtor = new debtor();
                            $debtor->Identifier = esc($value);
                            $debtor->show();
                            $mail->Debtor = $debtor->Identifier;
                            if(!isset($debtor->EmailAddress) || empty($debtor->EmailAddress)) {
                                $error_class->Warning[] = sprintf(__("debtor has no mailaddress"), $debtor->DebtorCode);
                            } else {
                                $EmailAddress = explode(";", $debtor->EmailAddress);
                                $sentTo = [];
                                foreach ($EmailAddress as $recipientEmail) {
                                    if(!in_array($recipientEmail, $recipient_array)) {
                                        $recipient_array[] = $recipientEmail;
                                        $sentTo[] = $recipientEmail;
                                    }
                                }
                                if(!empty($sentTo)) {
                                    $mail->Recipient = implode(";", $sentTo);
                                    if(isset($_POST["only_once"]) && $_POST["only_once"] == "1") {
                                        $mail->Sent_bcc = false;
                                        if($firstTime === true) {
                                            $mail->BlindCarbonCopy = check_email_address((!check_email_address($mail->BlindCarbonCopy) ? BCC_EMAILADDRESS : $mail->BlindCarbonCopy) . ";" . BCC_EMAILADDRESS, "convert");
                                        } else {
                                            $mail->BlindCarbonCopy = check_email_address($_POST["BlindCarbonCopy"], "convert");
                                        }
                                    } elseif(!isset($_POST["only_once"]) || $_POST["only_once"] != "1") {
                                        $mail->BlindCarbonCopy = check_email_address((!check_email_address($mail->BlindCarbonCopy) ? BCC_EMAILADDRESS : $mail->BlindCarbonCopy) . ";" . BCC_EMAILADDRESS, "convert");
                                        $mail->Sent_bcc = false;
                                    }
                                    $mail->Attachment = explode(",", esc($_POST["Attachment"]));
                                    $mail->add();
                                    $logEmailAddress = check_email_address($mail->Recipient, "convert", ", ");
                                    if(SENT_BATCHES != "1") {
                                        $mail->sent(false, false, false);
                                        if(empty($mail->Error)) {
                                            $error_class->Success[] = sprintf(__("mailing succesfully sent to"), $logEmailAddress);
                                        }
                                    } elseif((int) $mail->Status === 0) {
                                        $error_class->Success[] = sprintf(__("mailing added to batchqueue"), $logEmailAddress);
                                    }
                                    flashMessage($mail);
                                    $mail->AlreadySent = true;
                                }
                                $firstTime = false;
                            }
                        }
                    }
                    unset($_SESSION["mailing.add"]["Recipients"]);
                    unset($_SESSION["recipients.add"]["searchfor"]);
                    delete_stats_summary();
                    flashMessage($debtor);
                    header("Location: debtors.php");
                    exit;
                }
            } elseif(isset($_POST["Message"])) {
                if(!$_POST["Message"]) {
                    $error_class->Error[] = __("message is empty");
                }
                if(!$_POST["Subject"]) {
                    $error_class->Error[] = __("subject is empty");
                }
                if(empty($_SESSION["mailing.add"]["Recipients"]["CountRows"])) {
                    $error_class->Error[] = __("no recipients selected");
                }
            }
        }
        break;
    case "delete":
        if(!U_DEBTOR_DELETE) {
        } elseif(isset($debtor_id) && isset($_POST["imsure"]) && $_POST["imsure"] == "yes") {
            require_once "class/debtor.php";
            $debtor = new debtor();
            $debtor->Identifier = $debtor_id;
            $debtor->show();
            if(defined("IS_DEMO") && IS_DEMO && $debtor_id == 1) {
                $debtor->Error[] = __("demo - not allowed to delete demo client");
            } elseif($debtor->delete()) {
                flashMessage($debtor);
                header("Location: debtors.php");
                exit;
            }
        }
        break;
    case "push":
        if(isset($_POST["FromDebtor"]) && isset($_POST["ToDebtor"]) && U_DEBTOR_EDIT) {
            require_once "class/debtor.php";
            $debtor = new debtor();
            $debtor->Identifier = esc($_POST["FromDebtor"]);
            $debtor->show();
            $pushdata = $debtor->getPushData();
            $debtor->FromDebtor = esc($_POST["FromDebtor"]);
            $debtor->ToDebtor = esc($_POST["ToDebtor"]);
            if($debtor->pushDebtor($_POST, $pushdata)) {
                flashMessage($debtor);
                header("Location: debtors.php?page=show&id=" . $debtor->Identifier);
                exit;
            }
            flashMessage($debtor);
            header("Location: debtors.php?page=push");
            exit;
        }
        break;
    case "view":
        if(!empty($_POST["id"])) {
            switch ($_POST["action"]) {
                case "mailing":
                    $to = implode("|", esc($_POST["id"]));
                    header("Location: debtors.php?page=mailing&add_debtors=" . $to);
                    exit;
                    break;
                case "reactivate":
                    require_once "class/debtor.php";
                    foreach ($_POST["id"] as $debtor_id) {
                        $debtor = new debtor();
                        $debtor->Identifier = $debtor_id;
                        $debtor->recover();
                        flashMessage($debtor);
                        unset($debtor);
                    }
                    break;
                case "dialog:anonimize":
                    require_once "class/debtor.php";
                    if(isset($_POST["imsure"]) && $_POST["imsure"] == "yes" && wf_password_verify($_POST["Password"], $account->Password)) {
                        foreach ($_POST["id"] as $debtor_id) {
                            $debtor = new debtor();
                            $debtor->Identifier = $debtor_id;
                            $debtor->show();
                            $debtor->anonimize();
                            flashMessage($debtor);
                            unset($debtor);
                        }
                    } else {
                        $error_class->Error[] = __("could not anonimize debtor, check password");
                    }
                    break;
            }
        } elseif(isset($_POST["action"])) {
            $error_class->Warning[] = __("nothing selected");
        }
        if(isset($_GET["from_page"]) && !empty($_GET["from_page"]) && empty($_SESSION["flashMessage"]["Error"])) {
            flashMessage();
            switch ($_GET["from_page"]) {
                case "search":
                    $_SESSION["selected_tab"] = 0;
                    header("Location: search.php?page=show");
                    exit;
                    break;
            }
        }
        break;
    case "show":
        if(isset($_GET["action"])) {
            switch ($_GET["action"]) {
                case "recover":
                    require_once "class/debtor.php";
                    $debtor = new debtor();
                    $debtor->Identifier = $debtor_id;
                    $debtor->recover();
                    flashMessage($debtor);
                    header("Location: debtors.php?page=show&id=" . $debtor_id);
                    exit;
                    break;
                case "deactivate-two-factor-auth":
                    require_once "class/debtor.php";
                    $debtor = new debtor();
                    $debtor->Identifier = $debtor_id;
                    if($debtor->show()) {
                        $debtor->TwoFactorAuthentication = "off";
                        $debtor->TokenData = "";
                        $debtor->edit();
                        $debtor->Success = [__("two factor authentication debtor deactivated")];
                    }
                    break;
            }
        }
        break;
    case "delete_group":
        $page = "show_group";
        if(empty($_POST) || !U_DEBTOR_DELETE) {
        } elseif(isset($_POST["imsure"]) && $_POST["imsure"] == "yes") {
            require_once "class/group.php";
            $group = new group();
            $group->Type = "debtor";
            $group->Identifier = $debtor_id;
            if($group->delete()) {
                flashMessage($group);
                header("Location: debtors.php?page=groups");
                exit;
            }
        }
        break;
    case "pdf":
        if(isset($_POST["Template"]) && 0 < $_POST["Template"]) {
            require_once "class/debtor.php";
            $debtor = new debtor();
            $debtor->Identifier = $debtor_id;
            $debtor->show();
            $options["domain"] = isset($_POST["Domain"]) && 0 < $_POST["Domain"] ? esc($_POST["Domain"]) : 0;
            $options["hosting"] = isset($_POST["Hosting"]) && 0 < $_POST["Hosting"] ? esc($_POST["Hosting"]) : 0;
            $download_instead = isset($_POST["printtype"]) && esc($_POST["printtype"]) == "print" ? false : true;
            $debtor->getPDF(esc($_POST["Template"]), $options, $download_instead);
            flashMessage($debtor);
            header("Location: debtors.php?page=show&id=" . $debtor_id);
            exit;
        }
        break;
    case "redirect_clientarea":
        if(U_DEBTOR_EDIT) {
            require_once "class/debtor.php";
            $debtor = new debtor();
            $debtor->Identifier = $debtor_id;
            $debtor->show();
            $data = [];
            $data["time_generated"] = date("Y-m-d H:i:s");
            $data["debtor_id"] = $debtor_id;
            $data["debtor_key"] = sha1($debtor->DebtorCode);
            $data["db_key"] = sha1(microtime(true) . $debtor->DebtorCode . mt_rand(10000, 90000));
            $url_key = base64_encode(serialize($data));
            if($debtor->setCustomerpanelKey($data["db_key"])) {
                $url_get = strpos(CLIENTAREA_URL, "?") === false ? "?wfh_key=" : "&wfh_key=";
                header("Location: " . CLIENTAREA_URL . $url_get . $url_key);
                exit;
            }
        }
        break;
    case "anonimize":
        require_once "class/debtor.php";
        $debtor = isset($debtor) && is_object($debtor) ? $debtor : new debtor();
        if(!$debtor->show($debtor_id, true)) {
            flashMessage($debtor);
            header("Location: debtors.php");
            exit;
        }
        if($debtor->Status == 9 && $debtor->Anonymous == "no" && isset($_POST["imsure"]) && $_POST["imsure"] == "yes" && wf_password_verify($_POST["Password"], $account->Password)) {
            $debtor->anonimize();
        } else {
            $debtor->Error[] = __("could not anonimize debtor, check password");
        }
        flashMessage($debtor);
        header("Location: debtors.php?page=show&id=" . $debtor->Identifier);
        exit;
        break;
    case "delete":
    case "show":
        require_once "class/debtor.php";
        $debtor = isset($debtor) && is_object($debtor) ? $debtor : new debtor();
        if(!$debtor->show($debtor_id, true)) {
            flashMessage($debtor);
            header("Location: debtors.php");
            exit;
        }
        if($debtor->Status == 9 && $debtor->Anonymous == "no") {
            $error_class->Warning[] = __("this debtor has been removed and cannot be used again");
        } elseif($debtor->Status == 9 && $debtor->Anonymous == "yes") {
            $error_class->Warning[] = __("this debtor has been made anonymous and cannot be used again");
        }
        $group_list = $debtor->Groups;
        require_once "class/attachment.php";
        $attachment = new attachment();
        $debtor->Attachment = $attachment->getAttachments($debtor_id, "debtor");
        if(U_PRICEQUOTE_SHOW && (!isset($_POST["tableID"]) || $_POST["tableID"] == "PriceQuotes")) {
            require_once "class/pricequote.php";
            $pricequote = new pricequote();
            $session = isset($_SESSION["debtor.show.pricequote"]) ? $_SESSION["debtor.show.pricequote"] : [];
            $fields = ["PriceQuoteCode", "Date", "SentDate", "Status", "AmountExcl", "AmountIncl", "PriceQuoteMethod", "ReferenceNumber"];
            $sort = isset($session["sort"]) ? $session["sort"] : "Date` DESC, `PriceQuoteCode";
            $order = isset($session["order"]) ? $session["order"] : "DESC";
            $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) ? $session["limit"] : "1");
            $current_page = $limit;
            $searchat = "Debtor";
            $searchfor = $debtor->Identifier;
            $group_id = implode("|", array_keys($array_pricequotestatus));
            $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : min(25, MAX_RESULTS_LIST));
            $pricequote_list = $pricequote->all($fields, $sort, $order, $limit, $searchat, $searchfor, $group_id, $show_results);
            $_SESSION["debtor.show.pricequote"] = ["sort" => $sort, "order" => $order, "results" => $show_results];
        }
        if(U_INVOICE_SHOW && (!isset($_POST["tableID"]) || $_POST["tableID"] == "Invoices")) {
            require_once "class/invoice.php";
            $invoice = new invoice();
            $invoice_table_options = $invoice->getConfigInvoiceTable(["page" => "debtor"]);
            require_once "class/bankstatement.php";
            $transaction_matches = new Transaction_Matches_Model();
            $transaction_matches_options = $transaction_matches->getTransactionMatchesTable("debtor", $debtor->Identifier);
        }
        if(U_ORDER_SHOW && (!isset($_POST["tableID"]) || $_POST["tableID"] == "Orders")) {
            require_once "class/neworders.php";
            $orderlist = new neworder();
            $session = isset($_SESSION["debtor.show.order"]) ? $_SESSION["debtor.show.order"] : [];
            $fields = ["OrderCode", "CompanyName", "Initials", "SurName", "Address", "ZipCode", "City", "Country", "EmailAddress", "PhoneNumber", "MobileNumber", "Debtor", "Customer", "Type", "Date", "Authorisation", "InvoiceMethod", "Status", "AmountExcl", "AmountIncl", "Paid", "Comment", "Employee"];
            $sort = isset($session["sort"]) ? $session["sort"] : "Date` DESC, `OrderCode";
            $order = isset($session["order"]) ? $session["order"] : "DESC";
            $selectgroup = isset($session["status"]) ? $session["status"] : "0|1|2|8";
            $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) ? $session["limit"] : "1");
            $current_page = $limit;
            $searchat = "Debtor";
            $searchfor = $debtor->Identifier;
            $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : min(25, MAX_RESULTS_LIST));
            $order_list = $orderlist->all($fields, $sort, $order, $limit, $searchat, $searchfor, $selectgroup, $show_results);
            $_SESSION["debtor.show.order"] = ["sort" => $sort, "order" => $order, "results" => $show_results];
        }
        require_once "class/employee.php";
        $employee = new employee();
        $employees = $employee->all(["Name"]);
        require_once "class/interaction.php";
        $interaction = new interaction();
        $fields = ["Date", "Category", "Type", "Author", "Message"];
        $interactions = $interaction->all($fields, "Date` DESC, `id", "DESC", "-1", "", "", $debtor->Identifier, "5");
        if((U_SERVICE_SHOW || U_DOMAIN_SHOW || U_HOSTING_SHOW) && (!isset($_POST["tableID"]) || $_POST["tableID"] == "Subscriptions" || $_POST["tableID"] == "Subscriptions2")) {
            require_once "class/periodic.php";
            $subscription = new periodic();
            if(!U_SERVICE_SHOW || !U_DOMAIN_SHOW || !U_HOSTING_SHOW) {
                $subscription->restrictedAll = [];
                if(!U_SERVICE_SHOW) {
                    $subscription->restrictedAll[] = "other";
                }
                if(!U_DOMAIN_SHOW) {
                    $subscription->restrictedAll[] = "domain";
                }
                if(!U_HOSTING_SHOW) {
                    $subscription->restrictedAll[] = "hosting";
                }
            }
            $session = isset($_SESSION["debtor.show.subscription"]) ? $_SESSION["debtor.show.subscription"] : [];
            $fields = ["Description", "Number", "NumberSuffix", "PriceExcl", "TaxPercentage", "NextDate", "ProductName", "Periods", "Periodic", "StartPeriod", "EndPeriod", "Status", "TerminationDate", "PeriodicType", "Reference", "InvoiceAuthorisation", "DebtorInvoiceAuthorisation", "AutoRenew"];
            $sort = isset($session["sort"]) ? $session["sort"] : "NextDate";
            $order = isset($session["order"]) ? $session["order"] : "ASC";
            $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) && (!isset($_POST["tableID"]) || $_POST["tableID"] == "Subscriptions") ? $_POST["ajaxPage"] : (isset($session["limit"]) ? $session["limit"] : "1");
            $current_page = $limit;
            $searchat = "Debtor";
            $searchfor = $debtor->Identifier;
            $group_id = isset($session["status"]) && isset($_POST["tableID"]) ? $session["status"] : "active";
            $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : min(25, MAX_RESULTS_LIST));
            $subscription_list = $subscription->all($fields, $sort, $order, $limit, $searchat, $searchfor, $group_id, false, $show_results);
            $_SESSION["debtor.show.subscription"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "status" => $group_id];
            require_once "class/terminationprocedure.php";
            $termination = new Termination_Model();
            $table_config_terminations = $termination->getTableConfig();
            $table_config_terminations["parameters"]["debtor_id"] = $debtor->Identifier;
        }
        if(U_DOMAIN_SHOW && (!isset($_POST["tableID"]) || $_POST["tableID"] == "Domains")) {
            require_once "class/domain.php";
            $domain = new domain();
            $session = isset($_SESSION["debtor.show.domain"]) ? $_SESSION["debtor.show.domain"] : [];
            $fields = ["Domain", "Tld", "RegistrationDate", "ExpirationDate", "Status", "Registrar", "Name", "Type", "AuthKey", "PeriodicID", "PriceExcl", "Periodic", "NextDate", "StartPeriod", "TerminationDate", "AutoRenew", "DomainAutoRenew", "TerminationID"];
            $sort = isset($session["sort"]) ? $session["sort"] : "Domain";
            $order = isset($session["order"]) ? $session["order"] : "ASC";
            $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) ? $session["limit"] : "1");
            $current_page = $limit;
            $searchat = "Debtor";
            $searchfor = $debtor->Identifier;
            $group_id = "-1|1|3|4|5|6|7|8";
            $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : min(25, MAX_RESULTS_LIST));
            $list_debtor_domains = $domain->all($fields, $sort, $order, $limit, $searchat, $searchfor, $group_id, $show_results);
            $_SESSION["debtor.show.domain"] = ["sort" => $sort, "order" => $order, "results" => $show_results];
        }
        if(U_HOSTING_SHOW && (!isset($_POST["tableID"]) || $_POST["tableID"] == "Hosting")) {
            require_once "class/hosting.php";
            $hosting = new hosting();
            $session = isset($_SESSION["debtor.show.hosting"]) ? $_SESSION["debtor.show.hosting"] : [];
            $fields = ["PeriodicID", "Username", "Domain", "Server", "Name", "Status", "Package", "PackageName", "PeriodicID", "PriceExcl", "Periodic", "NextDate", "StartPeriod", "TerminationDate", "AutoRenew", "TerminationID"];
            $sort = isset($session["sort"]) ? $session["sort"] : "Domain";
            $order = isset($session["order"]) ? $session["order"] : "ASC";
            $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) ? $session["limit"] : "1");
            $current_page = $limit;
            $searchat = "Debtor";
            $searchfor = $debtor->Identifier;
            $group_id = "-1|1|3|4|5|7";
            $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : min(25, MAX_RESULTS_LIST));
            $list_debtor_hosting = $hosting->all($fields, $sort, $order, $limit, $searchat, $searchfor, $group_id, $show_results);
            $_SESSION["debtor.show.hosting"] = ["sort" => $sort, "order" => $order, "results" => $show_results];
        }
        if(U_SERVICE_SHOW && (!isset($_POST["tableID"]) || $_POST["tableID"] == "Services")) {
            require_once "class/periodic.php";
            $subscription = new periodic();
            $session = isset($_SESSION["debtor.show.other"]) ? $_SESSION["debtor.show.other"] : [];
            $fields = ["Description", "ProductID", "ProductName", "Periods", "Periodic", "PriceExcl", "Number", "NumberSuffix", "TaxPercentage", "StartPeriod", "EndPeriod", "Status", "TerminationDate", "NextDate", "ContractPeriods", "ContractPeriodic", "EndContract", "AutoRenew"];
            $sort = isset($session["sort"]) ? $session["sort"] : "ProductCode";
            $order = isset($session["order"]) ? $session["order"] : "ASC";
            $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) ? $session["limit"] : "1");
            $current_page = $limit;
            $searchat = "Debtor";
            $searchfor = $debtor->Identifier;
            $group_id = "1|8";
            $subscription->OtherServicesOnly = true;
            $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : min(25, MAX_RESULTS_LIST));
            $list_debtor_other = $subscription->all($fields, $sort, $order, $limit, $searchat, $searchfor, $group_id, false, $show_results);
            $_SESSION["debtor.show.other"] = ["sort" => $sort, "order" => $order, "results" => $show_results];
        }
        if(U_DOMAIN_SHOW && (!isset($_POST["tableID"]) || $_POST["tableID"] == "Handles")) {
            require_once "class/handle.php";
            $handle = new handle();
            $session = isset($_SESSION["debtor.show.handle"]) ? $_SESSION["debtor.show.handle"] : [];
            $fields = ["Handle", "RegistrarHandle", "Registrar", "Name", "CompanyName", "SurName", "Initials"];
            $sort = isset($session["sort"]) ? $session["sort"] : "Handle";
            $order = isset($session["order"]) ? $session["order"] : "ASC";
            $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) ? $session["limit"] : "1");
            $current_page = $limit;
            $searchat = "Debtor";
            $searchfor = $debtor->Identifier;
            $group_id = "";
            $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : min(25, MAX_RESULTS_LIST));
            $list_debtor_handles = $handle->all($fields, $sort, $order, $limit, $searchat, $searchfor, $group_id, $show_results);
            $_SESSION["debtor.show.handle"] = ["sort" => $sort, "order" => $order, "results" => $show_results];
        }
        if(U_TICKET_SHOW && (!isset($_POST["tableID"]) || $_POST["tableID"] == "Tickets" || $_POST["tableID"] == "Tickets2")) {
            require_once "class/ticket.php";
            $ticket = new ticket();
            $session = isset($_SESSION["debtor.show.ticket"]) ? $_SESSION["debtor.show.ticket"] : [];
            $fields = ["TicketID", "Subject", "Owner", "Priority", "Status", "Number", "LastDate", "LastName", "Name"];
            $sort = isset($session["sort"]) ? $session["sort"] : "TicketID";
            $order = isset($session["order"]) ? $session["order"] : "DESC";
            $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) && (!isset($_POST["tableID"]) || $_POST["tableID"] == "Tickets") ? $_POST["ajaxPage"] : (isset($session["limit"]) ? $session["limit"] : "1");
            $current_page = $limit;
            $searchat = "Debtor";
            $searchfor = $debtor->Identifier;
            $group_id = "0|1|2";
            $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : min(25, MAX_RESULTS_LIST));
            $tickets = $ticket->all($fields, $sort, $order, $limit, $searchat, $searchfor, $group_id, $show_results);
            $_SESSION["debtor.show.ticket"] = ["sort" => $sort, "order" => $order, "results" => $show_results];
            $session = isset($_SESSION["debtor.show.ticket.closed"]) ? $_SESSION["debtor.show.ticket.closed"] : [];
            $sort = isset($session["sort"]) ? $session["sort"] : "TicketID";
            $order = isset($session["order"]) ? $session["order"] : "DESC";
            $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) && (!isset($_POST["tableID"]) || $_POST["tableID"] == "Tickets2") ? $_POST["ajaxPage"] : (isset($session["limit"]) ? $session["limit"] : "1");
            $current_page2 = $limit;
            $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : min(25, MAX_RESULTS_LIST));
            $tickets_closed = $ticket->all($fields, $sort, $order, $limit, $searchat, $searchfor, "3", $show_results);
            $_SESSION["debtor.show.ticket.closed"] = ["sort" => $sort, "order" => $order, "results" => $show_results];
        }
        require_once "class/template.php";
        $template = new template();
        $fields = ["Name"];
        $templates = $template->all($fields, "", "", "", "Type", "invoice");
        $pricequotetemplates = $template->all($fields, "", "", "", "Type", "pricequote");
        $othertemplates = $template->all($fields, "", "", "", "Type", "other");
        $emailtemplate = new emailtemplate();
        $emailtemplates = $emailtemplate->all($fields);
        require_once "class/server.php";
        $server = new server();
        $list_servers = $server->all(["Name", "Panel", "Location", "Port"]);
        require_once "class/clientareachange.php";
        $ClientareaChange = new ClientareaChange_Model();
        $ca_options = [];
        $ca_options["filters"]["approval"] = "pending";
        $ca_options["filters"]["debtor"] = $debtor_id;
        $ca_options["filters"]["reference_type"] = "debtor";
        $ca_options["filters"]["reference_id"] = $debtor_id;
        $ca_options["filter"] = "pending";
        $clientarea_changes = $ClientareaChange->listChanges($ca_options);
        if(!$clientarea_changes || count($clientarea_changes) === 0) {
            unset($ClientareaChange);
        } else {
            $error_class->Warning[] = __("clientarea change warning debtor");
        }
        require_once "class/clientareaprofiles.php";
        $ClientareaProfiles_Model = new ClientareaProfiles_Model();
        if($debtor->ClientareaProfile && 0 < $debtor->ClientareaProfile) {
            $ClientareaProfiles_Model->id = $debtor->ClientareaProfile;
            $ClientareaProfiles_Model->show();
        } else {
            $ClientareaProfiles_Model->showDefault();
        }
        if(!isset($selected_tab) && isset($_SESSION["selected_tab"])) {
            $selected_tab = $_SESSION["selected_tab"];
            unset($_SESSION["selected_tab"]);
        }
        $message = parse_message($debtor, isset($pricequote) ? $pricequote : NULL, isset($invoice) ? $invoice : NULL, isset($domain) ? $domain : NULL, isset($hosting) ? $hosting : NULL, isset($ticket) ? $ticket : NULL, isset($modification) ? $modification : NULL);
        $wfh_page_title = __("debtor") . ($debtor->CompanyName ? " " . $debtor->CompanyName : " " . $debtor->Initials . " " . $debtor->SurName);
        $content_has_sidebar = true;
        $current_page_url = "debtors.php?page=show&id=" . $debtor->Identifier;
        $sidebar_template = "debtor.sidebar.php";
        require_once "views/debtor.show.php";
        break;
    case "add":
        checkRight(U_DEBTOR_EDIT);
        require_once "class/debtor.php";
        if($pagetype == "add" && !isset($_POST["DebtorCode"]) && defined("INT_WF_ACTIVE_DEBTOR_LIMIT") && (INT_WF_ACTIVE_DEBTOR_LIMIT <= $_SESSION["active_clients"] || $_SESSION["wf_cache_licensehash"] != md5(LICENSE . $_SESSION["active_clients"]))) {
            header("Location:debtors.php?page=upgrade");
            exit;
        }
        if(!isset($debtor) || !is_object($debtor)) {
            $debtor = new debtor();
            if($pagetype == "edit" && $debtor_id) {
                $debtor->Identifier = $debtor_id;
                if(!$debtor->show($debtor->Identifier, true)) {
                    flashMessage($debtor);
                    header("Location: debtors.php");
                    exit;
                }
                if($debtor->Status == 9 && $debtor->Anonymous == "yes") {
                    $debtor->Error[] = __("this debtor has been made anonymous and cannot be used again");
                    flashMessage($debtor);
                    header("Location: debtors.php");
                    exit;
                }
                require_once "class/handle.php";
                $handle = new handle();
                $matched_handles = $handle->lookupDebtorHandle($debtor->Identifier);
            }
        }
        if($debtor->DebtorCode == "") {
            $debtor->DebtorCode = $debtor->newDebtorCode();
        }
        require_once "class/template.php";
        $template = new template();
        $fields = ["Name"];
        $templates = $template->all($fields, "", "", "", "Type", "invoice");
        $pricequotetemplates = $template->all($fields, "", "", "", "Type", "pricequote");
        $emailtemplate = new emailtemplate();
        $emailtemplates = $emailtemplate->all($fields);
        require_once "class/group.php";
        $group = new group();
        $group->Type = "debtor";
        $fields = ["GroupName", "Products", "Debtors"];
        $groups = $group->all($fields);
        require_once "class/server.php";
        $server = new server();
        $list_servers = $server->all(["Name", "Panel"]);
        require_once "class/clientareaprofiles.php";
        $ClientareaProfiles_Model = new ClientareaProfiles_Model();
        $clientarea_profiles = $ClientareaProfiles_Model->listProfiles();
        if(0 < $debtor->Identifier) {
            $emailsync = $debtor->syncEmailAddressNeeded();
            $authsync = $debtor->syncAuthorizationNeeded();
            $addresssync = $debtor->syncAddressNeeded();
            if(empty($debtor->Attachment)) {
                require_once "class/attachment.php";
                $attachment = new attachment();
                $debtor->Attachment = $attachment->getAttachments($debtor_id, "debtor");
            }
            require_once "class/clientareachange.php";
            $ClientareaChanges = new ClientareaChange_Model();
            $options = [];
            $options["filters"]["reference_type"] = "debtor";
            $options["filters"]["reference_id"] = $debtor->Identifier;
            $options["filters"]["debtor"] = $debtor->Identifier;
            $options["filter"]["approval"] = "pending";
            $options["filter"] = "pending|error";
            $changes_result = $ClientareaChanges->listChanges($options);
            if(!empty($changes_result)) {
                $debtor_changes = [];
                foreach ($changes_result as $_change) {
                    $debtor_changes = array_merge($debtor_changes, json_decode($_change->Data, true));
                }
                if(!empty($debtor_changes)) {
                    $clientarea_change = true;
                    $current_debtor_data = [];
                    foreach ($debtor_changes as $_debtor_field => $_change_value) {
                        if(in_array($_debtor_field, $debtor->Variables) && $debtor->{$_debtor_field} != htmlspecialchars($_change_value)) {
                            $current_debtor_data[$_debtor_field] = $debtor->{$_debtor_field};
                            $debtor->{$_debtor_field} = htmlspecialchars($_change_value);
                        } else {
                            unset($debtor_changes[$_debtor_field]);
                        }
                    }
                    $debtor_field_labels = ["CompanyName" => "companyname", "CompanyNumber" => "company number", "TaxNumber" => "vat number", "LegalForm" => "legal form", "Initials" => "initials", "Sex" => "gender", "SurName" => "surname", "Address" => "address", "Address2" => "address", "ZipCode" => "zipcode", "City" => "city", "State" => "state", "Country" => "country", "EmailAddress" => "emailaddress", "SecondEmailAddress" => "extra emailaddress", "PhoneNumber" => "phonenumber", "MobileNumber" => "mobilenumber", "FaxNumber" => "faxnumber", "Website" => "website", "Mailing" => "mailing", "InvoiceCompanyName" => "companyname", "InvoiceInitials" => "initials", "InvoiceSex" => "gender", "InvoiceSurName" => "surname", "InvoiceAddress" => "address", "InvoiceAddress2" => "address", "InvoiceZipCode" => "zipcode", "InvoiceCity" => "city", "InvoiceState" => "state", "InvoiceCountry" => "country", "InvoiceEmailAddress" => "emailaddress", "ReminderEmailAddress" => "emailaddress", "AccountNumber" => "account number", "AccountName" => "account name", "AccountBank" => "bank", "AccountCity" => "bank city", "AccountBIC" => "bic", "InvoiceAuthorisation" => "authorization"];
                }
            }
        } else {
            $emailsync = $authsync = $addresssync = false;
        }
        $message = parse_message($debtor, $template, $emailtemplate, $server);
        $wfh_page_title = $pagetype == "edit" ? __("edit debtor") : __("add debtor");
        $sidebar_template = "debtor.sidebar.php";
        require_once "views/debtor.add.php";
        break;
    case "push":
        checkRight(U_DEBTOR_EDIT);
        $message = parse_message();
        $wfh_page_title = __("push debtors");
        $current_page_url = "debtors.php?page=push";
        $sidebar_template = "debtor.sidebar.php";
        require_once "views/debtor.push.php";
        break;
    case "groups":
        require_once "class/group.php";
        $group = new group();
        $group->Type = "debtor";
        $session = isset($_SESSION["debtorgroup.overview"]) ? $_SESSION["debtorgroup.overview"] : [];
        $fields = ["GroupName", "Products", "Debtors"];
        $sort = isset($session["sort"]) ? $session["sort"] : "GroupName";
        $order = isset($session["order"]) ? $session["order"] : "ASC";
        $groups = $group->all($fields, $sort, $order);
        $_SESSION["debtorgroup.overview"] = ["sort" => $sort, "order" => $order];
        $message = parse_message($group);
        $wfh_page_title = __("debtorgroups");
        $current_page_url = "debtors.php?page=groups";
        $sidebar_template = "debtor.sidebar.php";
        require_once "views/debtorgroup.overview.php";
        break;
    case "add_group":
        checkRight(U_DEBTOR_EDIT);
        require_once "class/group.php";
        $group = isset($group) && is_object($group) ? $group : new group();
        $group->Type = "debtor";
        if(isset($debtor_id) && 0 < $debtor_id && empty($group->Error)) {
            $group->Identifier = $debtor_id;
            if(!$group->show()) {
                flashMessage($group);
                header("Location: debtors.php?page=groups");
                exit;
            }
        }
        require_once "class/debtor.php";
        $debtor = new debtor();
        $session = isset($_SESSION["debtorgroup.add"]) ? $_SESSION["debtorgroup.add"] : [];
        $fields = ["DebtorCode", "CompanyName", "Initials", "SurName", "EmailAddress", "Address", "ZipCode", "City", "Country"];
        $sort = isset($session["sort"]) ? $session["sort"] : "DebtorCode";
        $order = isset($session["order"]) ? $session["order"] : "ASC";
        $searchat = isset($session["searchat"]) ? $session["searchat"] : "DebtorCode|CompanyName|SurName|EmailAddress";
        $searchfor = isset($session["searchfor"]) ? $session["searchfor"] : "";
        $selectgroup = isset($session["group"]) ? $session["group"] : "";
        $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) && $session["limit"] ? $session["limit"] : "1");
        $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : min(25, MAX_RESULTS_LIST));
        $debtors = $debtor->all($fields, $sort, $order, $limit, $searchat, $searchfor, $selectgroup, $show_results);
        $_SESSION["debtorgroup.add"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "group" => $selectgroup, "searchat" => $searchat, "searchfor" => $searchfor, "limit" => $limit];
        $current_page = $limit;
        $message = parse_message($group, $debtor);
        if(0 < $group->Identifier) {
            $current_page_url = "debtors.php?page=add_group&amp;id=" . $group->Identifier;
        } else {
            $current_page_url = "debtors.php?page=add_group";
        }
        $wfh_page_title = 0 < $debtor_id ? __("edit debtorgroup") : __("add debtorgroup");
        $sidebar_template = "debtor.sidebar.php";
        require_once "views/debtorgroup.add.php";
        break;
    case "show_group":
        checkRight(U_DEBTOR_SHOW);
        require_once "class/group.php";
        $group = isset($group) && is_object($group) ? $group : new group();
        $group->Type = "debtor";
        $group->Identifier = $debtor_id;
        if(!$group->show()) {
            flashMessage($group);
            header("Location: debtors.php?page=groups");
            exit;
        }
        require_once "class/debtor.php";
        $debtor = new debtor();
        $session = isset($_SESSION["debtorgroup.show"]) ? $_SESSION["debtorgroup.show"] : [];
        $fields = ["DebtorCode", "CompanyName", "Initials", "SurName", "EmailAddress", "Address", "ZipCode", "City", "Country"];
        $sort = isset($session["sort"]) ? $session["sort"] : "DebtorCode";
        $order = isset($session["order"]) ? $session["order"] : "ASC";
        $searchat = isset($session["searchat"]) ? $session["searchat"] : "DebtorCode|CompanyName|SurName|EmailAddress";
        $searchfor = isset($session["searchfor"]) ? $session["searchfor"] : "";
        $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) && $session["limit"] ? $session["limit"] : "1");
        $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : min(25, MAX_RESULTS_LIST));
        $debtors = $debtor->all($fields, $sort, $order, $limit, $searchat, $searchfor, $group->Identifier, $show_results);
        if(isset($debtors["CountRows"]) && ($debtors["CountRows"] < $show_results * ($limit - 1) || 1 < $limit && $debtors["CountRows"] == $show_results * ($limit - 1))) {
            $newPage = ceil($debtors["CountRows"] / $show_results);
            if($newPage <= 0) {
                $newPage = 1;
            }
            $limit = $newPage;
            $debtors = $debtor->all($fields, $sort, $order, $limit, $searchat, $searchfor, $group->Identifier, $show_results);
        }
        $_SESSION["debtorgroup.show"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "searchat" => $searchat, "searchfor" => $searchfor, "limit" => $limit];
        $current_page = $limit;
        $message = parse_message($group, $debtor);
        $wfh_page_title = __("debtorgroup") . " " . $group->GroupName;
        $current_page_url = "debtors.php?page=show_group&id=" . $group->Identifier;
        $sidebar_template = "debtor.sidebar.php";
        require_once "views/debtorgroup.show.php";
        break;
    case "mailing":
        checkRight(U_DEBTOR_EDIT);
        require_once "class/email.php";
        $mail = isset($mail) && is_object($mail) ? $mail : new email();
        require_once "class/template.php";
        $template = new emailtemplate();
        $fields = ["Name"];
        $templates = $template->all($fields);
        require_once "class/debtor.php";
        $debtor = new debtor();
        $debtor_fields = ["DebtorCode", "Initials", "SurName", "CompanyName", "EmailAddress", "Mailing"];
        require_once "class/group.php";
        $group = new group();
        $group->Type = "debtor";
        $fields = ["GroupName"];
        $debtorgroups = $group->all($fields);
        require_once "class/product.php";
        $product = new product();
        $products = $product->all(["ProductCode", "ProductName"], "ProductCode", "ASC", "-1");
        require_once "class/server.php";
        $server = new server();
        $servers = $server->all(["Name"], "Name", "ASC", "-1");
        $recipients = isset($_SESSION["mailing.add"]["Recipients"]) ? $_SESSION["mailing.add"]["Recipients"] : [];
        unset($recipients["CountRows"]);
        $_SESSION["recipients.add"]["sort"] = isset($_GET["Sort"]) ? $_GET["Sort"] : (isset($_SESSION["recipients.add"]["sort"]) ? $_SESSION["recipients.add"]["sort"] : "DebtorCode");
        $_SESSION["recipients.add"]["order"] = isset($_GET["Order"]) ? $_GET["Order"] : (isset($_SESSION["recipients.add"]["order"]) ? $_SESSION["recipients.add"]["order"] : "ASC");
        $_SESSION["recipients.add"]["searchfor"] = isset($_SESSION["recipients.add"]["searchfor"]) ? $_SESSION["recipients.add"]["searchfor"] : "";
        $_SESSION["recipients.add"]["results"] = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($_SESSION["recipients.add"]["results"]) ? $_SESSION["recipients.add"]["results"] : min(25, MAX_RESULTS_LIST));
        $_SESSION["recipients.add"]["limit"] = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($_SESSION["recipients.add"]["limit"]) ? $_SESSION["recipients.add"]["limit"] : "1");
        $_SESSION["mailing.add"]["sort"] = isset($_GET["Sort"]) ? $_GET["Sort"] : (isset($_SESSION["mailing.add"]["sort"]) ? $_SESSION["mailing.add"]["sort"] : "DebtorCode");
        $_SESSION["mailing.add"]["order"] = isset($_GET["Order"]) ? $_GET["Order"] : (isset($_SESSION["mailing.add"]["order"]) ? $_SESSION["mailing.add"]["order"] : "ASC");
        $_SESSION["mailing.add"]["results"] = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($_SESSION["mailing.add"]["results"]) ? $_SESSION["mailing.add"]["results"] : min(25, MAX_RESULTS_LIST));
        $_SESSION["mailing.add"]["limit"] = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($_SESSION["mailing.add"]["limit"]) ? $_SESSION["mailing.add"]["limit"] : "1");
        $recipients_add = $debtor->all($debtor_fields, $_SESSION["recipients.add"]["sort"], $_SESSION["recipients.add"]["order"], $_SESSION["recipients.add"]["limit"], "DebtorCode|CompanyName|SurName|EmailAddress", $_SESSION["recipients.add"]["searchfor"], false, $_SESSION["recipients.add"]["results"]);
        $debtors = $debtor->all($debtor_fields, $_SESSION["mailing.add"]["sort"], $_SESSION["mailing.add"]["order"]);
        if(isset($_GET["add_debtors"])) {
            if($_GET["add_debtors"] == "all") {
                foreach ($debtors as $k => $v) {
                    if(is_numeric($k) && $v["Mailing"] == "yes" && $v["EmailAddress"] != "") {
                        $add[] = $k;
                    }
                }
            } else {
                $add = explode("|", $_GET["add_debtors"]);
                foreach ($add as $k => $v) {
                    if(!$v) {
                        unset($add[$k]);
                    }
                }
            }
            $_SESSION["mailing.add"]["Recipients"] = array_unique(array_merge($recipients, $add));
            $recipients = $_SESSION["mailing.add"]["Recipients"];
        } elseif(isset($_GET["add_group"])) {
            $add = [];
            $group->Identifier = esc($_GET["add_group"]);
            $group->show();
            foreach ($debtors as $k => $v) {
                if(is_numeric($k) && $v["Mailing"] == "yes" && $v["EmailAddress"] != "" && in_array($v["id"], $group->Debtors)) {
                    $add[] = $k;
                }
            }
            $_SESSION["mailing.add"]["Recipients"] = array_unique(array_merge($recipients, $add));
            $recipients = $_SESSION["mailing.add"]["Recipients"];
        } elseif(isset($_GET["add_product"])) {
            require_once "class/periodic.php";
            $periodic = new periodic();
            $periodics = $periodic->all(["Debtor", "TerminationDate"], false, false, -1, "ProductCode", esc($_GET["add_product"]));
            $debtor_filter = $add = [];
            foreach ($periodics as $pID) {
                if(isset($pID["Debtor"]) && 0 < $pID["Debtor"]) {
                    if($pID["TerminationDate"] == "0000-00-00" || $pID["TerminationDate"] == "") {
                        $debtor_filter[] = $pID["Debtor"];
                    }
                }
            }
            foreach ($debtors as $k => $v) {
                if(is_numeric($k) && $v["Mailing"] == "yes" && $v["EmailAddress"] != "" && in_array($k, $debtor_filter)) {
                    $add[] = $k;
                }
            }
            $_SESSION["mailing.add"]["Recipients"] = array_unique(array_merge($recipients, $add));
            $recipients = $_SESSION["mailing.add"]["Recipients"];
        } elseif(isset($_GET["add_server"])) {
            require_once "class/hosting.php";
            $hosting = new hosting();
            $hostinglist = $hosting->all(["Debtor"], false, false, -1, "Server", esc($_GET["add_server"]));
            $debtor_filter = $add = [];
            foreach ($hostinglist as $hID) {
                if(isset($hID["Debtor"]) && 0 < $hID["Debtor"]) {
                    $debtor_filter[] = $hID["Debtor"];
                }
            }
            foreach ($debtors as $k => $v) {
                if(is_numeric($k) && $v["Mailing"] == "yes" && $v["EmailAddress"] != "" && in_array($k, $debtor_filter)) {
                    $add[] = $k;
                }
            }
            $_SESSION["mailing.add"]["Recipients"] = array_unique(array_merge($recipients, $add));
            $recipients = $_SESSION["mailing.add"]["Recipients"];
        } elseif(isset($_GET["remove_debtors"])) {
            $add = explode("|", $_GET["remove_debtors"]);
            foreach ($add as $k => $v) {
                if(!$v) {
                    unset($add[$k]);
                }
            }
            $_SESSION["mailing.add"]["Recipients"] = array_diff($recipients, $add);
            $recipients = $_SESSION["mailing.add"]["Recipients"];
        }
        foreach ($debtors as $key => $value) {
            if(is_numeric($key) && !in_array($key, $recipients)) {
                unset($debtors[$key]);
                $debtors["CountRows"] = $debtors["CountRows"] - 1;
            }
        }
        $_SESSION["mailing.add"]["Recipients"]["CountRows"] = $debtors["CountRows"];
        if(isset($_SESSION["mailing.add"]["Recipients"]["CountRows"]) && $_SESSION["mailing.add"]["Recipients"]["CountRows"] == 1) {
            $debtor->show($_SESSION["mailing.add"]["Recipients"][0]);
            if($debtor->Mailing == "no") {
                $debtor->Warning[] = sprintf(__("no mailing wanted for debtor"), $debtor->CompanyName ? $debtor->CompanyName : $debtor->SurName . ", " . $debtor->Initials);
            }
        } elseif(isset($_SESSION["mailing.add"]["Recipients"]) && is_array($_SESSION["mailing.add"]["Recipients"])) {
            foreach ($_SESSION["mailing.add"]["Recipients"] as $k => $debtor_id) {
                if(!is_numeric($debtor_id)) {
                } elseif(isset($debtors[$debtor_id]["Mailing"]) && $debtors[$debtor_id]["Mailing"] == "no") {
                    $error_company = $debtors[$debtor_id]["CompanyName"] ? $debtors[$debtor_id]["CompanyName"] : $debtors[$debtor_id]["SurName"] . ", " . $debtors[$debtor_id]["Initials"];
                    $error_name = $debtors[$debtor_id]["DebtorCode"] . " (" . $error_company . ")";
                    $debtor->Warning[] = sprintf(__("no mailing wanted for debtor"), $error_name);
                }
            }
        }
        if(isset($_GET["template"])) {
            $current_template = new emailtemplate();
            $current_template->Identifier = esc($_GET["template"]);
            $current_template->show();
        }
        if(!empty($current_template->Attachment)) {
            $attachments = [];
            $attach2 = [];
            if($handle = opendir(DIR_EMAIL_ATTACHMENTS)) {
                while (false !== ($file = readdir($handle))) {
                    if($file != "." && $file != ".." && $file != "index.php" && file_exists(DIR_EMAIL_ATTACHMENTS . $file)) {
                        $attachments[DIR_EMAIL_ATTACHMENTS . $file] = $file;
                    }
                }
            }
            asort($attachments);
            $templatelist = new template();
            $fields = ["Name"];
            $template_list = $templatelist->all($fields, "", "", "", "Type", "other");
            foreach ($template_list as $k => $v) {
                if(is_numeric($k)) {
                    $attach2["TemplateOther" . $k] = $v["Name"];
                }
            }
            asort($attach2);
        }
        $message = parse_message($template, $debtor, $group, $product, $server);
        $wfh_page_title = __("new mailing");
        $current_page_url = "debtors.php?page=mailing";
        $sidebar_template = "debtor.sidebar.php";
        require_once "views/mailing.add.php";
        break;
    case "upgrade":
        $url = INTERFACE_URL . "/hosting/infofile.php?action=upgrade";
        $url .= "&licensemd5=" . md5(trim(LICENSE));
        $url .= "&limit=" . (1 <= $_SESSION["active_clients"] / INT_WF_ACTIVE_DEBTOR_LIMIT ? "reached" : "almost");
        $content = getContent($url);
        $sidebar_template = "debtor.sidebar.php";
        require_once "views/header.php";
        echo $content;
        require_once "views/footer.php";
        break;
    default:
        require_once "class/debtor.php";
        $debtor = new debtor();
        $session = isset($_SESSION["debtor.overview"]) ? $_SESSION["debtor.overview"] : [];
        $fields = ["DebtorCode", "CompanyName", "Initials", "SurName", "EmailAddress", "Groups", "PhoneNumber", "MobileNumber", "FaxNumber", "Sex", "Address", "ZipCode", "City", "Country", "OpenAmountIncl"];
        $sort = isset($session["sort"]) ? $session["sort"] : "DebtorCode";
        $order = isset($session["order"]) ? $session["order"] : "ASC";
        $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) ? $session["limit"] : "1");
        $searchat = isset($session["searchat"]) ? $session["searchat"] : "DebtorCode|CompanyName|SurName|EmailAddress";
        $searchfor = isset($session["searchfor"]) ? $session["searchfor"] : "";
        $selectgroup = isset($session["group"]) ? $session["group"] : "";
        $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : MAX_RESULTS_LIST);
        $list_debtors = $debtor->all($fields, $sort, $order, $limit, $searchat, $searchfor, $selectgroup, $show_results);
        if(isset($list_debtors["CountRows"]) && ($list_debtors["CountRows"] < $show_results * ($limit - 1) || 1 < $limit && $list_debtors["CountRows"] == $show_results * ($limit - 1))) {
            $newPage = ceil($list_debtors["CountRows"] / $show_results);
            if($newPage <= 0) {
                $newPage = 1;
            }
            $_SESSION["debtor.overview"]["limit"] = $newPage;
            header("Location: debtors.php");
            exit;
        }
        $_SESSION["debtor.overview"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "group" => $selectgroup, "searchat" => $searchat, "searchfor" => $searchfor, "limit" => $limit];
        $current_page = $limit;
        require_once "class/group.php";
        $groups = new group();
        $groups->Type = "debtor";
        $fields = ["GroupName", "Products"];
        $group_list = $groups->all($fields);
        $message = parse_message($debtor, $groups);
        $wfh_page_title = __("debtors");
        $current_page_url = "debtors.php";
        $sidebar_template = "debtor.sidebar.php";
        require_once "views/debtor.overview.php";
}

?>