<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "config.php";
checkRight(U_PRICEQUOTE_SHOW);
$page = isset($_GET["page"]) ? $_GET["page"] : "overview";
$page = $page == "edit" ? "add" : $page;
$pricequote_id = isset($_GET["id"]) ? intval(esc($_GET["id"])) : (isset($_POST["id"]) ? intval(esc($_POST["id"])) : NULL);
switch ($page) {
    case "show":
        if(isset($pricequote_id) && isset($_GET["action"])) {
            require_once "class/pricequote.php";
            switch ($_GET["action"]) {
                case "sent":
                    if(!U_PRICEQUOTE_EDIT) {
                    } else {
                        $pricequote = new pricequote();
                        $pricequote->Identifier = $pricequote_id;
                        $pricequote->show();
                        $download_instead = isset($_POST["printtype"]) && esc($_POST["printtype"]) == "print" ? false : true;
                        $pricequote->sent(true, $download_instead);
                    }
                    break;
                case "accept":
                    if(!U_PRICEQUOTE_EDIT) {
                    } else {
                        $pricequote = new pricequote();
                        $pricequote->Identifier = $pricequote_id;
                        $pricequote->show();
                        if($pricequote->Status == 3) {
                            if(isset($_POST["usepricequoteasinvoiceref"]) && $_POST["usepricequoteasinvoiceref"] == "yes") {
                                $pricequote->UsePriceQuoteAsReferenceNumber = true;
                            }
                            if($pricequote->makeInvoice()) {
                                flashMessage($pricequote);
                                header("Location: invoices.php?page=show&id=" . $pricequote->InvoiceID);
                                exit;
                            }
                        } elseif($pricequote->Status < 3) {
                            $pricequote->Status = 3;
                            if($pricequote->changeStatus("accept")) {
                                $pricequote->Success = [sprintf(__("pricequote x accepted"), $pricequote->PriceQuoteCode)];
                            }
                            if(isset($_POST["createinvoice"]) && $_POST["createinvoice"] == "yes") {
                                if(isset($_POST["usepricequoteasinvoiceref"]) && $_POST["usepricequoteasinvoiceref"] == "yes") {
                                    $pricequote->UsePriceQuoteAsReferenceNumber = true;
                                }
                                if($pricequote->makeInvoice()) {
                                    flashMessage($pricequote);
                                    header("Location: invoices.php?page=show&id=" . $pricequote->InvoiceID);
                                    exit;
                                }
                            }
                        }
                    }
                    break;
                case "decline":
                    if(!U_PRICEQUOTE_EDIT) {
                    } else {
                        $pricequote = new pricequote();
                        $pricequote->Identifier = $pricequote_id;
                        $pricequote->show();
                        if($pricequote->Status != 4) {
                            $pricequote->Status = 8;
                            if($pricequote->changeStatus("decline")) {
                                $pricequote->Success = [sprintf(__("pricequote x declined"), $pricequote->PriceQuoteCode)];
                            }
                        } else {
                            $pricequote->Error[] = sprintf(__("invoice already created"), $pricequote->PriceQuoteCode);
                        }
                    }
                    break;
                case "print":
                    $pricequote = new pricequote();
                    $pricequote->Identifier = $pricequote_id;
                    $pricequote->show();
                    $download_instead = isset($_POST["printtype"]) && esc($_POST["printtype"]) == "print" ? false : true;
                    $show_accepted_data = isset($_GET["accepted"]) && esc($_GET["accepted"]) == "yes" ? true : false;
                    $pricequote->printPriceQuote(true, $download_instead, $show_accepted_data);
                    break;
                case "changePriceQuoteMethod":
                    if(!U_PRICEQUOTE_EDIT) {
                    } else {
                        $pricequote = new pricequote();
                        $pricequote->Identifier = $pricequote_id;
                        $pricequote->show();
                        $pricequote->PriceQuoteMethod = esc($_POST["InvoiceMethod"]);
                        $new_emailaddress = false;
                        if(isset($_POST["NewMethodEmailAddress"]) && in_array($pricequote->PriceQuoteMethod, [0, 3])) {
                            $new_emailaddress = esc($_POST["NewMethodEmailAddress"]);
                        }
                        $pricequote->changesendmethod($new_emailaddress);
                    }
                    break;
                default:
                    flashMessage($pricequote);
                    header("Location:pricequotes.php?page=show&id=" . $pricequote_id);
                    exit;
            }
        } elseif(isset($_POST["action"])) {
            if(!U_PRICEQUOTE_DELETE) {
            } else {
                switch ($_POST["action"]) {
                    case "removelogentry":
                        require_once "class/logfile.php";
                        $logfile = new logfile();
                        $list_log = isset($_POST["logentry"]) && is_array($_POST["logentry"]) ? $_POST["logentry"] : [];
                        foreach ($list_log as $log_id) {
                            $logfile->deleteEntry($log_id);
                        }
                        if(empty($logfile->Error)) {
                            $logfile->Success[] = sprintf(__("removed count logentries"), count($list_log));
                        }
                        flashMessage($logfile);
                        $_SESSION["selected_tab"] = 3;
                        break;
                    default:
                        flashMessage();
                        header("Location: pricequotes.php?page=show&id=" . $pricequote_id);
                        exit;
                }
            }
        }
        break;
    case "add":
        if(empty($pricequote_id) && !U_PRICEQUOTE_ADD || 0 < $pricequote_id && !U_PRICEQUOTE_EDIT) {
        } else {
            $pagetype = 0 < $pricequote_id ? "edit" : "add";
            require_once "class/pricequote.php";
            $pricequote = new pricequote();
            if($pagetype == "edit") {
                $pricequote->Identifier = $pricequote_id;
                $pricequote->show();
                $pricequote->format(false);
                $current_debtor = $pricequote->Debtor;
                $current_status = $pricequote->Status;
                $current_pricequotecode = $pricequote->PriceQuoteCode;
            }
            if(!empty($_POST) && isset($_POST["Status"])) {
                $pricequote->CurrentStatus = $pricequote->Status;
                if($_POST["VatShift_helper"] == "true") {
                    if(!isset($_POST["VatShift"])) {
                        $_POST["VatShift"] = "no";
                    } else {
                        $_POST["VatShift"] = "yes";
                    }
                } else {
                    $_POST["VatShift"] = "";
                }
                if($pagetype == "edit") {
                    foreach ($pricequote as $key => $value) {
                        if(in_array($key, $pricequote->Variables)) {
                            $pricequote->{$key} = html_entity_decode($value);
                        }
                    }
                } elseif(!$pricequote->is_free(esc($_POST["PriceQuoteCode"]))) {
                    $_POST["PriceQuoteCode"] = $pricequote->newPriceQuoteCode();
                }
                if(isset($_POST["File"]) && is_array($_POST["File"])) {
                    $pricequote->Attachment = [];
                    if(!empty($_POST["File"])) {
                        require_once "class/attachment.php";
                        $attachment = new attachment();
                        foreach ($_POST["File"] as $id => $Attachemtfilename) {
                            $attachmentInfo = $attachment->getAttachmentInfo($Attachemtfilename);
                            if($attachmentInfo !== false) {
                                $pricequote->Attachment[] = $attachmentInfo;
                            }
                        }
                    }
                }
                foreach ($_POST as $post_key => $post_value) {
                    if(in_array($post_key, $pricequote->Variables)) {
                        $post_value = is_string($post_value) ? esc(trim($post_value)) : $post_value;
                        $pricequote->{$post_key} = $post_value;
                    }
                }
                $pricequote->Description = esc($_POST["PriceQuoteDescription"]);
                $pricequote->Date = esc($_POST["PriceQuoteDate"]);
                if(IS_INTERNATIONAL) {
                    $pricequote->State = isset($_POST["StateCode"]) && $_POST["StateCode"] ? esc($_POST["StateCode"]) : $pricequote->State;
                }
                $added_lines = [];
                foreach ($_POST["Number"] as $invoiceKey => $value) {
                    $NumberSuffix = extractNumberAndSuffix($value);
                    if($NumberSuffix[1] === false) {
                        $NumberSuffix[0] = $value;
                        $NumberSuffix[1] = "";
                    }
                    list($_POST["Number"][$invoiceKey], $_POST["NumberSuffix"][$invoiceKey]) = $NumberSuffix;
                }
                if(!$pricequote->is_free(esc($_POST["PriceQuoteCode"]))) {
                    $pricequote->Error[] = sprintf(__("pricequotecode not available"), esc($_POST["PriceQuoteCode"]));
                    $result = false;
                } elseif(empty($pricequote->Debtor)) {
                    $pricequote->Error[] = __("invalid debtor");
                    $result = false;
                } else {
                    $pricequote->VatCalcMethod = $_POST["TaxRate1"] != "" && isEmptyFloat(number2db($_POST["TaxRate1"])) ? "excl" : $pricequote->VatCalcMethod;
                    $last_filled_line = -1;
                    $line_counter = 0;
                    foreach ($_POST["Date"] as $i => $value) {
                        if($pricequote->VatCalcMethod == "incl" && isset($_POST["PriceIncl"][$i]) && deformat_money(esc($_POST["PriceIncl"][$i])) && (esc($_POST["Taxable"]) == "true" || $_POST["TaxRate1"] != "" && 0 < $_POST["TaxRate1"])) {
                            $tax_percentage = $_POST["TaxRate1"] != "" ? esc($_POST["TaxRate1"]) : esc($_POST["TaxPercentage"][$i]);
                            $_POST["PriceExcl"][$i] = deformat_money(esc($_POST["PriceIncl"][$i])) / (1 + $tax_percentage);
                        }
                        if(isset($_POST["Item"][$i]) && 1 <= esc($_POST["Item"][$i]) && !isEmptyFloat(deformat_money(esc($_POST["Number"][$i]))) && esc($_POST["Description"][$i]) && esc($_POST["Description"][$i]) != " ") {
                            $last_filled_line = $line_counter;
                        } elseif((!isset($_POST["Item"][$i]) || esc($_POST["Item"][$i]) < 1) && esc($_POST["Number"][$i]) != "" && (esc($_POST["Description"][$i]) != "" || esc($_POST["PriceExcl"][$i]) != "")) {
                            $last_filled_line = $line_counter;
                        }
                        $line_counter++;
                    }
                    $line_counter = 0;
                    foreach ($_POST["Date"] as $i => $value) {
                        if($line_counter <= $last_filled_line || isset($_POST["Item"][$i]) && 1 <= $_POST["Item"][$i]) {
                            $pricequoteelement = new pricequoteelement();
                            $pricequoteelement->VatCalcMethod = $pricequote->VatCalcMethod;
                            if($pagetype == "edit") {
                                if(!esc($_POST["Description"][$i])) {
                                    $_POST["Description"][$i] = " ";
                                }
                                if(isset($_POST["Item"][$i]) && 0 < $_POST["Item"][$i]) {
                                    $pricequoteelement->Identifier = intval(esc($_POST["Item"][$i]));
                                    $pricequoteelement->show();
                                }
                            }
                            if(!(is_numeric(number2db(esc($_POST["Number"][$i]))) && isEmptyFloat(number2db(esc($_POST["Number"][$i])))) && ($line_counter <= $last_filled_line || esc($_POST["Description"][$i]) != " " || esc($_POST["PriceExcl"][$i]) != "")) {
                                $pricequoteelement->PriceQuoteCode = $pricequote->PriceQuoteCode;
                                $pricequoteelement->Debtor = $pricequote->Debtor;
                                $pricequoteelement->Date = esc($_POST["Date"][$i]);
                                $pricequoteelement->Number = esc($_POST["Number"][$i]);
                                $pricequoteelement->NumberSuffix = esc($_POST["NumberSuffix"][$i]);
                                $pricequoteelement->ProductCode = isset($_POST["ProductCode"][$i]) ? esc($_POST["ProductCode"][$i]) : "";
                                $pricequoteelement->Description = esc($_POST["Description"][$i]) == "" && $i < $_POST["NumberOfElements"] ? " " : esc($_POST["Description"][$i]);
                                $pricequoteelement->PriceExcl = esc($_POST["PriceExcl"][$i]);
                                $pricequoteelement->DiscountPercentage = esc($_POST["DiscountPercentage"][$i]);
                                $pricequoteelement->DiscountPercentageType = esc($_POST["DiscountPercentageType"][$i]);
                                $pricequoteelement->TaxPercentage = esc($_POST["Taxable"]) == "true" || $_POST["TaxRate1"] != "" ? esc($_POST["TaxPercentage"][$i]) : "0";
                                if(isset($_POST["PeriodicType"][$i]) && esc($_POST["PeriodicType"][$i]) == "period") {
                                    $pricequoteelement->Periods = esc($_POST["Periods"][$i]);
                                    $pricequoteelement->Periodic = esc($_POST["Periodic"][$i]);
                                    $pricequoteelement->StartPeriod = esc($_POST["StartPeriod"][$i]);
                                    $pricequoteelement->EndPeriod = esc($_POST["EndPeriod"][$i]);
                                } else {
                                    $pricequoteelement->Periods = 1;
                                    $pricequoteelement->Periodic = "";
                                    $pricequoteelement->StartPeriod = "";
                                    $pricequoteelement->EndPeriod = "";
                                }
                                if($pagetype == "edit") {
                                    $pricequoteelement->OldDebtor = $pricequote->OldDebtor;
                                }
                            } else {
                                $pricequoteelement->Number = 0;
                                $pricequoteelement->NumberSuffix = "";
                            }
                            $pricequoteelement->Ordering = array_search($i, array_keys($_POST["Date"]));
                            if(isset($_POST["Item"][$i]) && 0 < $_POST["Item"][$i]) {
                                $result_elements = $pricequoteelement->edit();
                                if(isEmptyFloat(number2db($pricequoteelement->Number)) || !$pricequoteelement->Number) {
                                    $_POST["Item"][$i] = 0;
                                }
                            } else {
                                $result_elements = $pricequoteelement->add();
                                if($result_elements) {
                                    $added_lines[] = $pricequoteelement->Identifier;
                                }
                            }
                            if(!$result_elements) {
                                $pricequote->Error = array_merge($pricequote->Error, $pricequoteelement->Error);
                            }
                        }
                        $line_counter++;
                    }
                    if(!isset($_SESSION["custom_fields"]["pricequote"]) || $_SESSION["custom_fields"]["pricequote"]) {
                        $customfields_list = $_SESSION["custom_fields"]["pricequote"];
                        $pricequote->customvalues = [];
                        foreach ($customfields_list as $k => $custom_field) {
                            $pricequote->customvalues[$custom_field["FieldCode"]] = isset($_POST["custom"][$custom_field["FieldCode"]]) ? esc($_POST["custom"][$custom_field["FieldCode"]]) : "";
                        }
                    }
                    if($pagetype == "edit") {
                        $result = $pricequote->edit();
                    } else {
                        $result = $pricequote->add();
                    }
                    if($result === true) {
                        if(!isset($_POST["File"]) || !is_array($_POST["File"])) {
                            $files = [];
                        } else {
                            $files = $_POST["File"];
                        }
                        if($pagetype == "add" && empty($files)) {
                            $attachment = [];
                        } else {
                            $Param = ["Identifier" => $pricequote->Identifier, "Files" => $files, "Type" => "pricequote"];
                            require_once "class/attachment.php";
                            $attachment = new attachment();
                            $attachment->checkAttachments($Param);
                        }
                        flashMessage($pricequote, $attachment);
                        header("Location: pricequotes.php?page=show&id=" . $pricequote->Identifier);
                        exit;
                    }
                    if(0 < $pricequote->Debtor) {
                        require_once "class/debtor.php";
                        $debtor = new debtor();
                        $debtor->Identifier = $pricequote->Debtor;
                        $debtor->show();
                    }
                    foreach ($added_lines as $lineID) {
                        $pricequoteelement = new pricequoteelement();
                        $pricequoteelement->Identifier = $lineID;
                        $pricequoteelement->show();
                        $pricequoteelement->delete(true);
                    }
                    foreach ($_POST as $post_key => $post_value) {
                        if(in_array($post_key, $pricequote->Variables) && is_string($post_value)) {
                            $pricequote->{$post_key} = htmlspecialchars(esc($post_value));
                        }
                    }
                    $error_class->Error = array_merge($error_class->Error, $pricequote->Error);
                    $pricequote->Error = [];
                    $page = "add";
                }
            }
        }
        break;
    case "view":
        require_once "class/pricequote.php";
        if(!empty($_POST["id"])) {
            switch ($_POST["action"]) {
                case "sent":
                case "dialog:sent":
                    if(!U_PRICEQUOTE_EDIT) {
                    } else {
                        $pricequote = new pricequote();
                        foreach ($_POST["id"] as $key => $id) {
                            $pricequote->Identifier = esc($id);
                            $pricequote->show();
                            $pricequote->PartOfBatch = true;
                            $download_instead = isset($_POST["printtype"]) && esc($_POST["printtype"]) == "print" ? false : true;
                            $result = $pricequote->sent(true, $download_instead);
                            if(count($_POST["id"]) == $key + 1 && $pricequote->hasPrintQueue) {
                                $pricequote->downloadBatchPDF("F");
                                $_SESSION["force_download"] = $pricequote->pdf->Name;
                            }
                        }
                    }
                    break;
                case "print":
                case "dialog:print":
                    $pricequote = new pricequote();
                    foreach ($_POST["id"] as $key => $id) {
                        $pricequote->Identifier = esc($id);
                        $pricequote->show();
                        if(count($_POST["id"]) == $key + 1) {
                            $pricequote->LastOfBatch = true;
                        }
                        $pricequote->PartOfBatch = true;
                        $download_instead = isset($_POST["printtype"]) && esc($_POST["printtype"]) == "print" ? false : true;
                        $pricequote->printBatch(true, $download_instead);
                    }
                    break;
                case "accept":
                case "dialog:accept":
                    if(!U_PRICEQUOTE_EDIT) {
                    } else {
                        foreach ($_POST["id"] as $key => $id) {
                            $pricequote = new pricequote();
                            $pricequote->Identifier = $id;
                            $pricequote->show();
                            if($pricequote->Status == 3) {
                                if(isset($_POST["usepricequoteasinvoiceref"]) && $_POST["usepricequoteasinvoiceref"] == "yes") {
                                    $pricequote->UsePriceQuoteAsReferenceNumber = true;
                                }
                                $pricequote->makeInvoice();
                            } elseif($pricequote->Status < 3) {
                                $pricequote->Status = 3;
                                if($pricequote->changeStatus("accept")) {
                                    $pricequote->Success = [sprintf(__("pricequote x accepted"), $pricequote->PriceQuoteCode)];
                                }
                                if(isset($_POST["createinvoice"]) && $_POST["createinvoice"] == "yes") {
                                    if(isset($_POST["usepricequoteasinvoiceref"]) && $_POST["usepricequoteasinvoiceref"] == "yes") {
                                        $pricequote->UsePriceQuoteAsReferenceNumber = true;
                                    }
                                    $pricequote->makeInvoice();
                                }
                            }
                            flashMessage($pricequote);
                            unset($pricequote);
                        }
                    }
                    break;
                case "decline":
                case "dialog:decline":
                    if(!U_PRICEQUOTE_EDIT) {
                    } else {
                        foreach ($_POST["id"] as $key => $id) {
                            $pricequote = new pricequote();
                            $pricequote->Identifier = $id;
                            $pricequote->show();
                            if($pricequote->Status != 4) {
                                $pricequote->Status = 8;
                                if($pricequote->changeStatus("decline")) {
                                    $pricequote->Success = [sprintf(__("pricequote x declined"), $pricequote->PriceQuoteCode)];
                                }
                            } else {
                                $pricequote->Error[] = sprintf(__("invoice already created"), $pricequote->PriceQuoteCode);
                            }
                            flashMessage($pricequote);
                            unset($pricequote);
                        }
                    }
                    break;
                default:
                    unset($_POST["id"]);
            }
        } elseif(isset($_POST["action"])) {
            $error_class->Warning[] = __("nothing selected");
        }
        flashMessage($pricequote);
        if(isset($_GET["from_page"]) && !empty($_GET["from_page"])) {
            switch ($_GET["from_page"]) {
                case "debtor":
                    $_SESSION["selected_tab"] = 1;
                    header("Location: debtors.php?page=show&id=" . intval($_GET["from_id"]));
                    exit;
                    break;
                case "search":
                    $_SESSION["selected_tab"] = 2;
                    header("Location: search.php?page=show");
                    exit;
                    break;
            }
        }
        header("Location: pricequotes.php");
        exit;
        break;
    case "delete":
        $pagetype = "confirmDelete";
        if($pricequote_id != NULL) {
            $page = "show";
        } else {
            $page = "overview";
        }
        if(empty($_POST) || !U_PRICEQUOTE_DELETE) {
        } elseif(isset($_POST["imsure"]) && $_POST["imsure"] == "yes") {
            $deleteType = isset($_POST["deleteType"]) ? esc($_POST["deleteType"]) : "hide";
            require_once "class/pricequote.php";
            $pricequote = new pricequote();
            $pricequote->Identifier = $pricequote_id;
            $pricequote->show();
            $result = $pricequote->delete($pricequote_id, $deleteType);
            if($result) {
                $pricequote_id = NULL;
                $page = "overview";
                if(!empty($_SESSION["ActionLog"]["PriceQuote"]["delete"])) {
                    array_shift($_SESSION["ActionLog"]["PriceQuote"]["delete"]);
                    if(!empty($_SESSION["ActionLog"]["PriceQuote"]["delete"])) {
                        header("Location: ?page=delete&id=" . current($_SESSION["ActionLog"]["PriceQuote"]["delete"]));
                        exit;
                    }
                }
            }
            flashMessage($pricequote);
            if(isset($_SESSION["ActionLog"]["PriceQuote"]["from_page"]) && !empty($_SESSION["ActionLog"]["PriceQuote"]["from_page"])) {
                $from_page = $_SESSION["ActionLog"]["PriceQuote"]["from_page"];
                $from_id = $_SESSION["ActionLog"]["PriceQuote"]["from_id"];
                unset($_SESSION["ActionLog"]["PriceQuote"]["from_page"]);
            }
            if($result) {
                header("Location: pricequotes.php");
                exit;
            }
            header("Location: pricequotes.php?page=show&id=" . $pricequote_id);
            exit;
        }
        break;
    case "overview":
    case "add":
        checkRight(U_PRICEQUOTE_EDIT);
        require_once "class/debtor.php";
        $debtor = isset($debtor) && is_object($debtor) ? $debtor : new debtor();
        $fields = ["Initials", "SurName", "CompanyName", "DebtorCode"];
        $debtors = $debtor->all($fields);
        require_once "class/pricequote.php";
        $pricequote = isset($pricequote) && is_object($pricequote) ? $pricequote : new pricequote();
        if(!empty($pricequote_id) && 0 < $pricequote_id) {
            $pricequote->Identifier = $pricequote_id;
            $pricequote->show();
            $pricequote->format();
            if(empty($pricequote->Attachment)) {
                require_once "class/attachment.php";
                $attachment = new attachment();
                $pricequote->Attachment = $attachment->getAttachments($pricequote_id, "pricequote");
            }
            if(!is_array($debtors) || !in_array($pricequote->Debtor, array_keys($debtors))) {
                $debtor->show($pricequote->Debtor);
                if($debtor->Anonymous == "yes") {
                    $error_class->Warning[] = __("this debtor has been made anonymous and cannot be used again");
                    if((int) $pricequote->Status === 0) {
                        $pricequote->Debtor = 0;
                    }
                } else {
                    $error_class->Warning[] = __("this debtor is removed, only use as archive");
                }
                if(0 < $pricequote->Debtor) {
                    $debtors[$pricequote->Debtor] = ["id" => $pricequote->Debtor, "Initials" => $debtor->Initials, "SurName" => $debtor->SurName, "CompanyName" => $debtor->CompanyName, "DebtorCode" => $debtor->DebtorCode];
                }
            } else {
                $debtor->Identifier = $pricequote->Debtor;
                $debtor->show();
            }
        } else {
            if(isset($_GET["debtor"]) && 0 < $_GET["debtor"]) {
                $pricequote->Debtor = intval(esc($_GET["debtor"]));
                $debtor = new debtor();
                $debtor->Identifier = $pricequote->Debtor;
                $debtor->show();
                if($debtor->Status == 9 && $debtor->Anonymous == "yes") {
                    $debtor->Error[] = __("this debtor has been made anonymous and cannot be used again");
                    flashMessage($debtor);
                    header("Location: pricequotes.php");
                    exit;
                }
                $pricequote->InvoiceMethod = $debtor->InvoiceMethod;
                $pricequote->Template = 0 < $debtor->PriceQuoteTemplate ? $debtor->PriceQuoteTemplate : $pricequote->Template;
                $useAbnormalInvoiceData = $debtor->InvoiceDataForPriceQuote == "yes" ? true : false;
                $pricequote->CompanyName = $debtor->InvoiceCompanyName && $useAbnormalInvoiceData ? $debtor->InvoiceCompanyName : $debtor->CompanyName;
                $pricequote->Sex = $debtor->InvoiceSurName && $useAbnormalInvoiceData ? $debtor->InvoiceSex : $debtor->Sex;
                $pricequote->Initials = $debtor->InvoiceInitials && $useAbnormalInvoiceData ? $debtor->InvoiceInitials : $debtor->Initials;
                $pricequote->SurName = $debtor->InvoiceSurName && $useAbnormalInvoiceData ? $debtor->InvoiceSurName : $debtor->SurName;
                $pricequote->Address = $debtor->InvoiceAddress && $useAbnormalInvoiceData ? $debtor->InvoiceAddress : $debtor->Address;
                $pricequote->ZipCode = $debtor->InvoiceZipCode && $useAbnormalInvoiceData ? $debtor->InvoiceZipCode : $debtor->ZipCode;
                $pricequote->City = $debtor->InvoiceCity && $useAbnormalInvoiceData ? $debtor->InvoiceCity : $debtor->City;
                $pricequote->Country = $debtor->InvoiceCountry && $debtor->InvoiceAddress && $useAbnormalInvoiceData ? $debtor->InvoiceCountry : $debtor->Country;
                $pricequote->EmailAddress = $debtor->InvoiceEmailAddress && $useAbnormalInvoiceData ? $debtor->InvoiceEmailAddress : $debtor->EmailAddress;
                $pricequote->Authorisation = $debtor->InvoiceAuthorisation;
                if(!empty($debtor->customfields_list)) {
                    foreach ($debtor->customfields_list as $k => $custom_field) {
                        $pricequote->customvalues[$custom_field["FieldCode"]] = $debtor->customvalues[$custom_field["FieldCode"]];
                    }
                }
            } elseif(isset($_GET["clone"]) && 0 < $_GET["clone"]) {
                $pricequote->Identifier = intval(esc($_GET["clone"]));
                if($pricequote->show()) {
                    $pricequote->PriceQuoteCode = $pricequote->newPriceQuoteCode();
                    $pricequote->Status = 0;
                    $pricequote->Identifier = 0;
                    $pricequote->Date = rewrite_date_db2site(date("Y-m-d"));
                    $pricequote->SentDate = "";
                    $pricequote->Sent = 0;
                    $pricequote->AcceptName = "";
                    $pricequote->AcceptEmailAddress = "";
                    $pricequote->AcceptComment = "";
                    $pricequote->AcceptSignatureBase64 = "";
                    $pricequote->AcceptDate = "";
                    $pricequote->AcceptIPAddress = "";
                    $pricequote->AcceptUserAgent = "";
                    $pricequote->AcceptPDF = 0;
                    $pricequote->Attachment = [];
                    $debtor->Identifier = $pricequote->Debtor;
                    $debtor->show();
                    if($debtor->Status == 9 && $debtor->Anonymous == "yes") {
                        $pricequote->Debtor = 0;
                        unset($debtor);
                    }
                    $x = 0;
                    foreach ($pricequote->Elements as $key => $data) {
                        if(is_numeric($key)) {
                            $_POST["Date"][$x] = $data["Date"] ? rewrite_date_db2site(date("Ymd")) : "";
                            $_POST["Number"][$x] = $data["Number"];
                            $_POST["NumberSuffix"][$x] = $data["NumberSuffix"];
                            $_POST["ProductCode"][$x] = $data["ProductCode"];
                            $_POST["Description"][$x] = htmlspecialchars_decode($data["Description"]);
                            $_POST["Periods"][$x] = $data["Periods"];
                            $_POST["Periodic"][$x] = $data["Periodic"];
                            $_POST["StartPeriod"][$x] = $data["StartPeriod"];
                            $_POST["EndPeriod"][$x] = $data["EndPeriod"];
                            $_POST["PeriodicType"][$x] = $data["Periodic"] == "" ? "once" : "period";
                            $_POST["TaxPercentage"][$x] = $data["TaxPercentage"];
                            $_POST["PriceIncl"][$x] = $pricequote->VatCalcMethod == "incl" ? $data["PriceIncl"] : round((double) $data["PriceIncl"], 5);
                            $_POST["PriceExcl"][$x] = $pricequote->VatCalcMethod == "excl" ? $data["PriceExcl"] : round((double) $data["PriceExcl"], 5);
                            $_POST["DiscountPercentage"][$x] = $data["DiscountPercentage"] * 100;
                            $_POST["DiscountPercentageType"][$x] = $data["DiscountPercentageType"];
                            $x++;
                        }
                    }
                    $_POST["NumberOfElements"] = $x;
                    if(!empty($_POST["copyAttachments"])) {
                        require_once "class/attachment.php";
                        $attachment = new attachment();
                        $pricequote->Attachment = $attachment->getAttachments(esc($_GET["clone"]), "pricequote");
                        $attachment_array = [];
                        foreach ($pricequote->Attachment as $k => $_file) {
                            if(!in_array($_file->id, $_POST["copyAttachments"])) {
                            } elseif(@copy(@$attachment->fileDir($_file->Reference, $_file->Type) . $_file->FilenameServer, @$attachment->fileDir("", $_file->Type, true) . $_file->FilenameServer)) {
                                $attachment->FilenameOriginal = $_file->Filename;
                                $attachment->FilenameServer = $_file->FilenameServer;
                                $attachment->FileSize = $_file->Size;
                                $attachment->FileType = $_file->Type;
                                $attachment->Reference = 0;
                                $document_id = $attachment->_saveFileToDb();
                                if(0 < $document_id) {
                                    $attachment_array[] = $document_id;
                                }
                            }
                        }
                        $pricequote->Attachment = buildAttachmentArray($attachment_array);
                    }
                }
            }
            if(!$pricequote->PriceQuoteCode) {
                $pricequote->PriceQuoteCode = $pricequote->newPriceQuoteCode();
            }
        }
        require_once "class/product.php";
        $product = new product();
        $fields = ["ProductCode", "ProductName", "ProductKeyPhrase", "PriceExcl", "PricePeriod", "TaxPercentage"];
        $products = $product->all($fields, "ProductCode", "ASC", -1);
        require_once "class/template.php";
        $templates = new template();
        $fields = ["Name"];
        $templates = $templates->all($fields, "Name", "ASC", "", "Type", "pricequote");
        $message = parse_message($pricequote, $debtor, $product, isset($attachment) ? $attachment : NULL);
        $wfh_page_title = $_GET["page"] == "add" ? __("create pricequote") : ($_GET["page"] == "edit" ? __("edit pricequote") : "");
        $current_page_url = "pricequotes.php?page=add&id=" . $pricequote->Identifier;
        $sidebar_template = "invoice.sidebar.php";
        require_once "views/pricequote.add.php";
        break;
    case "show":
        if(isset($_POST["Comment"]) && U_PRICEQUOTE_EDIT && (!isset($_GET["action"]) || $_GET["action"] != "update")) {
            require_once "class/pricequote.php";
            $pricequote = new pricequote();
            $pricequote->changeComment($pricequote_id, esc($_POST["Comment"]));
            $selected_tab = 1;
        }
        require_once "class/pricequote.php";
        $pricequote = isset($pricequote) && is_object($pricequote) ? $pricequote : new pricequote();
        $pricequote->Identifier = $pricequote_id;
        if(!$pricequote->show()) {
            flashMessage($pricequote);
            header("Location: pricequotes.php");
            exit;
        }
        $pricequote->format(false);
        $pricequote_element = new pricequoteelement();
        $pricequote->Elements = $pricequote_element->all($pricequote->PriceQuoteCode);
        require_once "class/attachment.php";
        $attachment = new attachment();
        $pricequote->Attachment = $attachment->getAttachments($pricequote_id, "pricequote");
        require_once "class/debtor.php";
        $debtor = new debtor();
        $debtor->Identifier = $pricequote->Debtor;
        $debtor->show();
        $template = new template();
        $template->Identifier = $pricequote->Template;
        $template->show();
        require_once "class/logfile.php";
        $logfile = new logfile();
        $session = isset($_SESSION["pricequote.show.logfile"]) ? $_SESSION["pricequote.show.logfile"] : [];
        $fields = ["Date", "Who", "Name", "Action", "Values", "Translate"];
        $sort = isset($session["sort"]) ? $session["sort"] : "Date";
        $order = isset($session["order"]) ? $session["order"] : "DESC";
        $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) ? $session["limit"] : "1");
        $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : min(25, MAX_RESULTS_LIST));
        $history = $logfile->all($fields, $sort, $order, $limit, "pricequote", $pricequote_id, $show_results);
        $_SESSION["pricequote.show.logfile"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "limit" => $limit];
        $current_page = $limit;
        if($pricequote->VatShift == "yes" || $pricequote->VatShift == "" && $pricequote->AmountTax == money(0, false) && $debtor->Country != $company->Country && $debtor->CompanyName != "" && $debtor->TaxNumber != "" && ($debtor->Country == "NL" || in_array($debtor->Country, $_SESSION["wf_cache_array_country_EU"]))) {
            $show_vatshift_text = __("default vatshift text");
        } else {
            $show_vatshift_text = false;
        }
        $message = parse_message($pricequote, $debtor, $logfile, $attachment);
        $wfh_page_title = __("pricequote") . " " . $pricequote->PriceQuoteCode;
        $current_page_url = "pricequotes.php?page=show&id=" . $pricequote->Identifier;
        $sidebar_template = "invoice.sidebar.php";
        require_once "views/pricequote.show.php";
        break;
    default:
        require_once "class/pricequote.php";
        $pricequote = new pricequote();
        $fields = ["PriceQuoteCode", "Debtor", "AmountExcl", "AmountIncl", "Date", "Status", "Initials", "SurName", "CompanyName", "Address", "ZipCode", "City", "Country", "PhoneNumber", "MobileNumber", "EmailAddress", "PriceQuoteMethod", "ReferenceNumber"];
        $session = isset($_SESSION["pricequote.overview"]) ? $_SESSION["pricequote.overview"] : [];
        $sort = isset($session["sort"]) ? $session["sort"] : "Date` DESC, `PriceQuoteCode";
        $order = isset($session["order"]) ? $session["order"] : "DESC";
        $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) ? $session["limit"] : "1");
        $searchat = isset($session["searchat"]) ? $session["searchat"] : "PriceQuoteCode|CompanyName|SurName";
        $searchfor = isset($session["searchfor"]) ? $session["searchfor"] : "";
        $selectgroup = isset($session["status"]) ? $session["status"] : "0|1|2|3|4|8";
        $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : MAX_RESULTS_LIST);
        $pricequotes = $pricequote->all($fields, $sort, $order, $limit, $searchat, $searchfor, $selectgroup, $show_results);
        if(isset($pricequotes["CountRows"]) && ($pricequotes["CountRows"] < $show_results * ($limit - 1) || 1 < $limit && $pricequotes["CountRows"] == $show_results * ($limit - 1))) {
            $newPage = ceil($pricequotes["CountRows"] / $show_results);
            if($newPage <= 0) {
                $newPage = 1;
            }
            $_SESSION["pricequote.overview"]["limit"] = $newPage;
            header("Location: pricequotes.php");
            exit;
        }
        $_SESSION["pricequote.overview"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "status" => $selectgroup, "searchat" => $searchat, "searchfor" => $searchfor, "limit" => $limit];
        $current_page = $limit;
        $message = parse_message($pricequote);
        $wfh_page_title = __("pricequote overview") . " (" . $pricequotes["CountRows"] . ")";
        $current_page_url = "pricequotes.php";
        $sidebar_template = "invoice.sidebar.php";
        require_once "views/pricequote.overview.php";
}

?>