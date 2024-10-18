<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "config.php";
checkRight(U_INVOICE_SHOW);
$page = isset($_GET["page"]) ? $_GET["page"] : "overview";
$page = $page == "edit" ? "add" : $page;
$invoice_id = isset($_GET["id"]) ? intval(esc($_GET["id"])) : (isset($_POST["id"]) ? intval(esc($_POST["id"])) : NULL);
if($page == "bankstatement") {
    class invoiceController
    {
        public function router($page)
        {
            $action = isset($_POST["action"]) && $_POST["action"] ? $_POST["action"] : (isset($_GET["action"]) && $_GET["action"] ? $_GET["action"] : "");
            if(method_exists($this, $page . "_" . $action)) {
                return $this->{$page . "_" . $action}();
            }
            if(method_exists($this, $page)) {
                return $this->{$page}();
            }
            exit("page not found");
        }
        public function bankstatement_process_match()
        {
            if(!U_INVOICE_EDIT) {
                exit;
            }
            require_once "class/bankstatement.php";
            $transactionID = esc($_POST["transaction_id"]);
            $transaction = new Transaction_Model();
            if(isset($_POST["match_info"][0]["reference_type"]) && $_POST["match_info"][0]["reference_type"] == "batch") {
                $transaction->show($transactionID);
                $transaction->findMatch($transaction, "invoices");
                $candidates = $transaction->getCandidates();
                foreach ($candidates as $_candidate) {
                    $_POST["match_info"][] = ["reference_type" => $_candidate->Type, "reference_id" => $_candidate->ReferenceID, "amount_payable" => $_candidate->AmountPayable, "amount_matched" => $_candidate->AmountMatched];
                }
                unset($_POST["match_info"][0]);
                $_POST["match_info"] = array_values($_POST["match_info"]);
            }
            $result = $transaction->processMatch($_POST["transaction_id"], $_POST["match_info"], $_POST["directdebit"]);
            if($result === true) {
                flashMessage($transaction);
                echo "OK";
                exit;
            }
            $transaction->Success = [];
            $message = parse_message($transaction);
            echo $message;
            exit;
        }
        public function bankstatement_process_no_match()
        {
            if(!U_INVOICE_EDIT) {
                exit;
            }
            require_once "class/bankstatement.php";
            $transaction = new Transaction_Model();
            $transaction->processNoMatch($_POST["transaction_id"]);
            exit;
        }
        public function bankstatement_process()
        {
            checkRight(U_INVOICE_EDIT);
            require_once "class/bankstatement.php";
            $transaction = new Transaction_Model();
            if(!isset($_SESSION["invoices.bankstatement.process"])) {
                $_list = $transaction->listUnprocessedTransactions();
                $transaction_list = [];
                foreach ($_list as $k => $v) {
                    $transaction_list[$v->id] = $v;
                }
                $_SESSION["invoices.bankstatement.process"]["transaction_list"] = $transaction_list;
            } else {
                $transaction_list = $_SESSION["invoices.bankstatement.process"]["transaction_list"];
            }
            $current_record = isset($_GET["id"]) && isset($transaction_list[$_GET["id"]]) ? $transaction_list[$_GET["id"]] : current($transaction_list);
            $transaction->show($current_record->id);
            if($transaction->Status != "unmatched") {
                header("Location: invoices.php?page=bankstatement&action=show&id=" . $current_record->id);
                exit;
            }
            $current_record_counter = 0;
            $next_record_id = false;
            foreach ($transaction_list as $_record_id => $_record) {
                if($_record_id == $current_record->id) {
                    $current_record_counter++;
                    $next_record_id = true;
                } elseif($next_record_id === false) {
                    $current_record_counter++;
                } elseif($next_record_id === true) {
                    $next_record_id = $_record_id;
                    $next_record_id = $next_record_id === true ? false : $next_record_id;
                    $bank_transaction = new Transaction_Model();
                    $matched = $bank_transaction->findMatch($current_record);
                    $candidate_list = $bank_transaction->getCandidates();
                    if($current_record->Type == "reversal" && SDD_ID) {
                        require_once "class/template.php";
                        $emailtemplate = new emailtemplate();
                        $emailtemplates = $emailtemplate->all(["Name"]);
                    }
                    $message = parse_message($transaction);
                    $page = "bankstatement";
                    $sidebar_template = "invoice.sidebar.php";
                    $wfh_page_title = __("banktransaction process h2");
                    require_once "views/transaction.process.php";
                }
            }
        }
        public function bankstatement_show()
        {
            require_once "class/bankstatement.php";
            $transaction = new Transaction_Model();
            $transaction->show(esc($_GET["id"]));
            $current_record = $transaction;
            if($transaction->Status == "unmatched") {
                header("Location: invoices.php?page=bankstatement&action=process&id=" . esc($_GET["id"]));
                exit;
            }
            $match_list = $transaction->getMatches();
            $message = parse_message($transaction);
            $page = "bankstatement";
            $sidebar_template = "invoice.sidebar.php";
            $wfh_page_title = __("banktransaction show h2");
            require_once "views/transaction.show.php";
        }
        public function bankstatement_show_undo()
        {
            require_once "class/bankstatement.php";
            $transaction = new Transaction_Model();
            $transaction->undoMatching(esc($_GET["id"]));
            header("Location: invoices.php?page=bankstatement&action=process&id=" . esc($_GET["id"]));
            exit;
        }
        public function bankstatement()
        {
            if(isset($_SESSION["invoices.bankstatement.process"])) {
                unset($_SESSION["invoices.bankstatement.process"]);
            }
            if(isset($_GET["bankaccount"]) && $_GET["bankaccount"]) {
                return $this->__bankstatement_bankaccount_overview($_GET["bankaccount"]);
            }
            require_once "class/bankstatement.php";
            $transaction = new Transaction_Model();
            if(!empty($_FILES)) {
                if(defined("IS_DEMO") && IS_DEMO) {
                    $transaction->Warning[] = __("demo - not able to add attachments");
                } elseif(defined("IS_DEMO") && IS_DEMO) {
                    $transaction->Warning[] = __("demo - not able to add attachments");
                } else {
                    if(isset($_FILES["file"]["error"]) && empty($_FILES["file"]["error"])) {
                        if($_POST["fileformat"] == "camt") {
                            $file = new CAMT053_Model();
                            $file->readFile($_FILES["file"]["tmp_name"], $_FILES["file"]["name"]);
                        } elseif($_POST["fileformat"] == "mt940structured") {
                            $file = new MT940Structured_Model();
                            $file->readFile($_FILES["file"]["tmp_name"], $_FILES["file"]["name"]);
                        } elseif($_POST["fileformat"] == "mt940") {
                            $file = new MT940_Model();
                            $file->readFile($_FILES["file"]["tmp_name"], $_FILES["file"]["name"]);
                        } else {
                            $file = new Bank_Statement_Model();
                            $file->Error[] = sprintf(__("bankstatement invalid file"), htmlspecialchars($_FILES["file"]["name"]));
                        }
                        flashMessage($file);
                        header("Location: ?page=bankstatement");
                        exit;
                    }
                    $transaction->Error[] = __("cannot read file");
                }
                flashMessage($transaction);
                header("Location: ?page=bankstatement");
                exit;
            }
            $transaction_list = $transaction->listUnprocessedTransactions();
            $bank_statement = new Bank_Statement_Model();
            $recent_activity = $bank_statement->getRecentActivity();
            $message = parse_message($transaction);
            $page = "bankstatement";
            $sidebar_template = "invoice.sidebar.php";
            $wfh_page_title = __("banktransaction import h2");
            require_once "views/transaction.import.php";
        }
        private function __bankstatement_bankaccount_overview($bankaccount)
        {
            require_once "class/bankstatement.php";
            $transaction = new Transaction_Model();
            if(U_INVOICE_EDIT && isset($_POST["action"]) && $_POST["action"] == "dialog:delete_transaction") {
                $transaction->deleteTransactions($_POST["ids"]);
                flashMessage($transaction);
                header("Location: ?page=bankstatement&bankaccount=" . htmlspecialchars($bankaccount));
                exit;
            }
            if(isset($_SESSION["backoffice_tables_config"]["list_transaction"]["filter"])) {
                $options["filter"] = $_SESSION["backoffice_tables_config"]["list_transaction"]["filter"];
            }
            $message = parse_message($transaction);
            $page = "bankstatement";
            $sidebar_template = "invoice.sidebar.php";
            $wfh_page_title = sprintf(__("banktransaction overview account h2"), htmlspecialchars($bankaccount));
            require_once "views/transaction.overview.php";
        }
    }
    $router = new invoiceController();
    $router->router($page);
    exit;
}
switch ($page) {
    case "show":
        if(isset($_POST["Comment"]) && U_INVOICE_EDIT && (!isset($_GET["action"]) || $_GET["action"] != "update")) {
            require_once "class/invoice.php";
            $invoice = new invoice();
            $invoice->changeComment($invoice_id, esc($_POST["Comment"]));
            $selected_tab = 1;
        }
        if(isset($invoice_id) && isset($_GET["action"])) {
            require_once "class/invoice.php";
            require_once "class/debtor.php";
            switch ($_GET["action"]) {
                case "paymentprocessreactivate":
                    if(!U_INVOICE_EDIT) {
                    } else {
                        $invoice = new invoice();
                        $invoice->Identifier = $invoice_id;
                        $invoice->show();
                        $invoice->changePaymentProcessStatus("reactivate");
                    }
                    break;
                case "paymentprocesspause":
                    if(!U_INVOICE_EDIT) {
                    } else {
                        $invoice = new invoice();
                        $invoice->Identifier = $invoice_id;
                        $invoice->show();
                        $debtor = new debtor();
                        $debtor->Identifier = $invoice->Debtor;
                        $debtor->show();
                        $invoice->changePaymentProcessStatus("pause");
                    }
                    break;
                case "sentinvoice":
                    if(!U_INVOICE_EDIT) {
                    } else {
                        $invoice = new invoice();
                        $invoice->Identifier = $invoice_id;
                        $invoice->show();
                        $download_instead = isset($_POST["printtype"]) && esc($_POST["printtype"]) == "print" ? false : true;
                        $invoice->sent(true, true, $download_instead);
                    }
                    break;
                case "sentreminder":
                    if(!U_INVOICE_EDIT) {
                    } else {
                        $invoice = new invoice();
                        $invoice->Identifier = $invoice_id;
                        $invoice->show();
                        $invoice->useInvoiceMethod = esc($_POST["radio_send_invoicemethod"]);
                        $download_instead = isset($_POST["printtype"]) && esc($_POST["printtype"]) == "print" ? false : true;
                        $invoice->sentReminder($download_instead);
                    }
                    break;
                case "sentsummation":
                    if(!U_INVOICE_EDIT) {
                    } else {
                        $invoice = new invoice();
                        $invoice->Identifier = $invoice_id;
                        $invoice->show();
                        $invoice->useInvoiceMethod = esc($_POST["radio_send_invoicemethod"]);
                        $download_instead = isset($_POST["printtype"]) && esc($_POST["printtype"]) == "print" ? false : true;
                        $invoice->sentSummation($download_instead);
                    }
                    break;
                case "changesendmethod":
                    if(!U_INVOICE_EDIT) {
                    } else {
                        $invoice = new invoice();
                        $invoice->Identifier = $invoice_id;
                        $invoice->InvoiceMethod = esc($_POST["InvoiceMethod"]);
                        $new_emailaddress = false;
                        if(isset($_POST["NewMethodEmailAddress"]) && in_array($invoice->InvoiceMethod, [0, 3])) {
                            $new_emailaddress = esc($_POST["NewMethodEmailAddress"]);
                        }
                        $invoice->changesendmethod($new_emailaddress);
                    }
                    break;
                case "markaspaid":
                    if(!U_INVOICE_EDIT) {
                    } else {
                        $invoice = new invoice();
                        $invoice->Identifier = $invoice_id;
                        $invoice->show();
                        if($invoice->Status <= 3 && substr($invoice->InvoiceCode, 0, 8) != "[concept") {
                            if(isset($_POST["PayDate"])) {
                                $_POST["PayDate"] = rewrite_date_site2db($_POST["PayDate"]);
                            }
                            if(isset($_POST["PayDate"]) && is_date($_POST["PayDate"])) {
                                $invoice->markaspaid($_POST["PayDate"]);
                            } else {
                                $invoice->markaspaid();
                            }
                            $invoice->checkAuto();
                        } elseif($invoice->Status <= 3 && substr($invoice->InvoiceCode, 0, 8) == "[concept") {
                            $error_class->Error[] = sprintf(__("invoice not marked as paid, because status or conceptcode"), $invoice->InvoiceCode);
                        }
                    }
                    break;
                case "markasunpaid":
                    if(!U_INVOICE_EDIT) {
                    } else {
                        $invoice = new invoice();
                        $invoice->Identifier = $invoice_id;
                        $invoice->show();
                        if($invoice->Status == 4) {
                            $invoice->markasunpaid();
                        }
                    }
                    break;
                case "faileddirectdebit":
                    $invoice = new invoice();
                    $invoice->Identifier = $invoice_id;
                    $invoice->show();
                    require_once "class/directdebit.php";
                    $directdebit = new directdebit();
                    $failedAction = $_POST["InvoiceAction"];
                    $reason = esc($_POST["Reason"]);
                    $notifyDebtorAboutNewDirectDebitDate = isset($_POST["NotifyDebtor"]) && $_POST["NotifyDebtor"] == "yes" ? true : false;
                    $notifyDebtorWithMailID = $notifyDebtorAboutNewDirectDebitDate === true ? esc($_POST["NotifyMail"]) : 0;
                    $directdebit->failedDirectDebitByInvoiceID($invoice_id, $reason, $failedAction, $notifyDebtorAboutNewDirectDebitDate, $notifyDebtorWithMailID);
                    flashMessage($invoice, $directdebit);
                    header("Location:invoices.php?page=show&id=" . $invoice_id);
                    exit;
                    break;
                case "cancelonlinepayment":
                    if(!U_INVOICE_EDIT) {
                    } else {
                        $invoice = new invoice();
                        $invoice->Identifier = $invoice_id;
                        $invoice->show();
                        if((int) $invoice->Paid === 0) {
                            $invoice->removeTransactionID();
                        }
                    }
                    break;
                case "removeauth":
                    if(!U_INVOICE_EDIT) {
                    } else {
                        $invoice = new invoice();
                        $invoice->Identifier = $invoice_id;
                        $invoice->show();
                        if($invoice->Status < 4) {
                            $invoice->removeAuthorization();
                        }
                    }
                    break;
                case "print":
                    $invoice = new invoice();
                    $invoice->Identifier = $invoice_id;
                    $invoice->show();
                    $download_instead = isset($_POST["printtype"]) && esc($_POST["printtype"]) == "print" ? false : true;
                    $invoice->printInvoice(true, $download_instead);
                    break;
                case "partialpayment":
                    if(!U_INVOICE_EDIT) {
                    } else {
                        $invoice = new invoice();
                        $invoice->Identifier = $invoice_id;
                        $invoice->show();
                        if($invoice->Status <= 3 && substr($invoice->InvoiceCode, 0, 8) != "[concept") {
                            if(isset($_POST["PayDate"])) {
                                $_POST["PayDate"] = rewrite_date_site2db($_POST["PayDate"]);
                            }
                            if(isset($_POST["PayDate"]) && is_date($_POST["PayDate"])) {
                                $invoice->partpayment(esc($_POST["AmountPaid"]), $_POST["PayDate"]);
                            } else {
                                $invoice->partpayment(esc($_POST["AmountPaid"]));
                            }
                            if($invoice->Status == 4) {
                                $invoice->checkAuto();
                            }
                        } elseif((int) $invoice->Status === 0 && substr($invoice->InvoiceCode, 0, 8) == "[concept") {
                            $error_class->Error[] = sprintf(__("invoice is still concept invoice, cannot do part payment"), $invoice->InvoiceCode);
                        }
                    }
                    break;
                case "block":
                    if(!U_INVOICE_EDIT) {
                    } else {
                        $invoice = new invoice();
                        $invoice->Identifier = $invoice_id;
                        $invoice->show();
                        $invoice->changeBlockStatus("block");
                    }
                    break;
                case "unblock":
                    if(!U_INVOICE_EDIT) {
                    } else {
                        $invoice = new invoice();
                        $invoice->Identifier = $invoice_id;
                        $invoice->show();
                        $invoice->changeBlockStatus("unblock");
                    }
                    break;
                case "schedule":
                    if(!U_INVOICE_EDIT) {
                    } else {
                        $invoice = new invoice();
                        $invoice->Identifier = $invoice_id;
                        $invoice->show();
                        $datetime = ($_POST["Date"] ? date("Y-m-d", strtotime(rewrite_date_site2db($_POST["Date"]))) : "") . " " . $_POST["Hours"] . ":" . $_POST["Minutes"] . ":00";
                        $invoice->scheduleDraftInvoice($datetime);
                        $invoice->Success = [];
                    }
                    break;
                case "undo_schedule":
                    if(!U_INVOICE_EDIT) {
                    } else {
                        $invoice = new invoice();
                        $invoice->Identifier = $invoice_id;
                        $invoice->show();
                        $invoice->undoScheduleDraftInvoice();
                    }
                    break;
                default:
                    $invoice = new static();
                    $invoice->Identifier = $invoice_id;
                    $invoice->show();
                    do_action("invoice_show_additional_action", ["action" => $_GET["action"], "invoice_id" => $invoice_id, "invoiceCode" => $invoice->InvoiceCode, "invoice_debtor" => $invoice->Debtor]);
                    flashMessage($invoice);
                    header("Location:invoices.php?page=show&id=" . $invoice_id);
                    exit;
            }
        } elseif(isset($_POST["action"])) {
            if(!U_INVOICE_DELETE) {
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
                        header("Location: invoices.php?page=show&id=" . $invoice_id);
                        exit;
                }
            }
        }
        break;
    case "add":
        if(empty($invoice_id) && !U_INVOICE_ADD || 0 < $invoice_id && !U_INVOICE_EDIT) {
        } else {
            $pagetype = 0 < $invoice_id ? "edit" : "add";
            require_once "class/invoice.php";
            $invoice = new invoice();
            if($pagetype == "edit") {
                $invoice->Identifier = $invoice_id;
                $invoice->show();
                $invoice->format(false, false);
                if($invoice->Status == "0" && $invoice->SentDate != "0000-00-00 00:00:00" && $invoice->InvoiceMethod == "0" && $invoice->SentDate <= date("Y-m-d H:i:s", strtotime("+1 hour"))) {
                    $invoice->undoScheduleDraftInvoice("edit");
                }
                $current_debtor = $invoice->Debtor;
                $current_status = $invoice->Status;
                $current_invoicecode = $invoice->InvoiceCode;
            }
            if(!empty($_POST) && isset($_POST["Status"])) {
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
                    foreach ($invoice as $key => $value) {
                        if(in_array($key, $invoice->Variables)) {
                            $invoice->{$key} = html_entity_decode($value);
                        }
                    }
                } elseif($_POST["Status"] == "0" && !$invoice->is_free(esc($_POST["InvoiceCode"]))) {
                    $_POST["InvoiceCode"] = $invoice->newConceptCode();
                }
                if(isset($_POST["File"]) && is_array($_POST["File"])) {
                    $invoice->Attachment = [];
                    if(!empty($_POST["File"])) {
                        require_once "class/attachment.php";
                        $attachment = new attachment();
                        foreach ($_POST["File"] as $id => $Attachemtfilename) {
                            $attachmentInfo = $attachment->getAttachmentInfo($Attachemtfilename);
                            if($attachmentInfo !== false) {
                                $invoice->Attachment[] = $attachmentInfo;
                            }
                        }
                    }
                }
                if($_POST["Status"] == "0") {
                    unset($_POST["SentDate"]);
                }
                foreach ($_POST as $post_key => $post_value) {
                    if(in_array($post_key, $invoice->Variables)) {
                        $post_value = is_string($post_value) ? esc(trim($post_value)) : $post_value;
                        $invoice->{$post_key} = $post_value;
                    }
                }
                $invoice->Description = esc($_POST["InvoiceDescription"]);
                $invoice->Date = esc($_POST["InvoiceDate"]);
                if(IS_INTERNATIONAL) {
                    $invoice->State = isset($_POST["StateCode"]) && $_POST["StateCode"] ? esc($_POST["StateCode"]) : $invoice->State;
                }
                $h_domain = "";
                $h_domainID = "";
                $h_hosting = "";
                $added_lines = [];
                foreach ($_POST["Number"] as $invoiceKey => $value) {
                    $NumberSuffix = extractNumberAndSuffix($value);
                    if($NumberSuffix[1] === false) {
                        $NumberSuffix[0] = $value;
                        $NumberSuffix[1] = "";
                    }
                    list($_POST["Number"][$invoiceKey], $_POST["NumberSuffix"][$invoiceKey]) = $NumberSuffix;
                }
                if(!$invoice->is_free(esc($_POST["InvoiceCode"]))) {
                    $invoice->Error[] = sprintf(__("invoicecode not available"), esc($_POST["InvoiceCode"]));
                    $result = false;
                } elseif(empty($invoice->Debtor)) {
                    $invoice->Error[] = __("invalid debtor");
                    $result = false;
                } else {
                    $invoice->VatCalcMethod = $_POST["TaxRate1"] != "" && isEmptyFloat(number2db($_POST["TaxRate1"])) ? "excl" : $invoice->VatCalcMethod;
                    $last_filled_line = -1;
                    $line_counter = 0;
                    foreach ($_POST["Date"] as $i => $value) {
                        if($invoice->VatCalcMethod == "incl" && isset($_POST["PriceIncl"][$i]) && deformat_money(esc($_POST["PriceIncl"][$i])) && (esc($_POST["Taxable"]) == "true" || $_POST["TaxRate1"] != "" && 0 < $_POST["TaxRate1"])) {
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
                            $invoiceelement = new invoiceelement();
                            $invoiceelement->VatCalcMethod = $invoice->VatCalcMethod;
                            if($pagetype == "edit") {
                                if(!esc($_POST["Description"][$i])) {
                                    $_POST["Description"][$i] = " ";
                                }
                                if(isset($_POST["Item"][$i]) && 0 < $_POST["Item"][$i]) {
                                    $invoiceelement->Identifier = esc($_POST["Item"][$i]);
                                    $invoiceelement->show();
                                }
                            }
                            if(!(is_numeric(number2db(esc($_POST["Number"][$i]))) && isEmptyFloat(number2db(esc($_POST["Number"][$i])))) && ($line_counter <= $last_filled_line || esc($_POST["Description"][$i]) != " " || esc($_POST["PriceExcl"][$i]) != "")) {
                                $invoiceelement->InvoiceCode = $invoice->InvoiceCode;
                                $invoiceelement->Debtor = $invoice->Debtor;
                                $invoiceelement->Date = esc($_POST["Date"][$i]);
                                $invoiceelement->Number = esc($_POST["Number"][$i]);
                                $invoiceelement->NumberSuffix = esc($_POST["NumberSuffix"][$i]);
                                $invoiceelement->ProductCode = isset($_POST["ProductCode"][$i]) ? esc($_POST["ProductCode"][$i]) : "";
                                $invoiceelement->Description = esc($_POST["Description"][$i]) == "" && $i < $_POST["NumberOfElements"] ? " " : esc($_POST["Description"][$i]);
                                $invoiceelement->PriceExcl = esc($_POST["PriceExcl"][$i]);
                                $invoiceelement->DiscountPercentage = esc($_POST["DiscountPercentage"][$i]);
                                $invoiceelement->DiscountPercentageType = esc($_POST["DiscountPercentageType"][$i]);
                                $invoiceelement->TaxPercentage = esc($_POST["Taxable"]) == "true" || $_POST["TaxRate1"] != "" ? esc($_POST["TaxPercentage"][$i]) : "0";
                                if(isset($_POST["PeriodicType"][$i]) && esc($_POST["PeriodicType"][$i]) == "period") {
                                    $invoiceelement->Periods = esc($_POST["Periods"][$i]);
                                    $invoiceelement->Periodic = esc($_POST["Periodic"][$i]);
                                    $invoiceelement->StartPeriod = esc($_POST["StartPeriod"][$i]);
                                    $invoiceelement->EndPeriod = esc($_POST["EndPeriod"][$i]);
                                } else {
                                    $invoiceelement->Periods = 1;
                                    $invoiceelement->Periodic = "";
                                    $invoiceelement->StartPeriod = "";
                                    $invoiceelement->EndPeriod = "";
                                }
                                if($pagetype == "edit") {
                                    $invoiceelement->OldDebtor = $invoice->OldDebtor;
                                }
                            } else {
                                $invoiceelement->Number = 0;
                                $invoiceelement->NumberSuffix = "";
                            }
                            $invoiceelement->Ordering = array_search($i, array_keys($_POST["Date"]));
                            if(isset($_POST["Item"][$i]) && 0 < $_POST["Item"][$i]) {
                                $result_elements = $invoiceelement->edit();
                                if(isEmptyFloat(number2db($invoiceelement->Number))) {
                                    $_POST["Item"][$i] = 0;
                                }
                            } else {
                                $result_elements = $invoiceelement->add();
                                if($result_elements) {
                                    $added_lines[] = $invoiceelement->Identifier;
                                }
                                $h_domain = isset($invoiceelement->Domain) && $invoiceelement->Domain ? $invoiceelement->Domain : $h_domain;
                                $h_domainID = isset($invoiceelement->Domain) && $invoiceelement->Domain ? $invoiceelement->DomainID : $h_domainID;
                                $h_hosting = isset($invoiceelement->Hosting) && $invoiceelement->Hosting ? $invoiceelement->Hosting : $h_hosting;
                            }
                            if(!$result_elements) {
                                $invoice->Error = array_merge($invoice->Error, $invoiceelement->Error);
                            } else {
                                $invoice->Warning = array_merge($invoice->Warning, $invoiceelement->Warning);
                            }
                        }
                        $line_counter++;
                    }
                    if(!isset($_SESSION["custom_fields"]["invoice"]) || $_SESSION["custom_fields"]["invoice"]) {
                        $customfields_list = $_SESSION["custom_fields"]["invoice"];
                        $invoice->customvalues = [];
                        foreach ($customfields_list as $k => $custom_field) {
                            $invoice->customvalues[$custom_field["FieldCode"]] = isset($_POST["custom"][$custom_field["FieldCode"]]) ? esc($_POST["custom"][$custom_field["FieldCode"]]) : "";
                        }
                    }
                    if($pagetype == "edit") {
                        $invoice->AmountPaid = deformat_money($invoice->AmountPaid);
                        $result = $invoice->edit();
                        if(0 < $current_status && (int) $invoice->Status === 0 && strpos($invoice->InvoiceCode, "[concept]") !== false) {
                            if($invoice->newInvoiceCode() == $current_invoicecode) {
                            } else {
                                $invoice->Warning[] = sprintf(__("invoice status change caused a gap"), $current_invoicecode);
                            }
                        }
                        if($invoice->isExportedToAccountingPackage()) {
                            $invoice->Warning[] = __("export accounting - warning invoice edit after export");
                        }
                    } else {
                        $result = $invoice->add();
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
                            $Param = ["Identifier" => $invoice->Identifier, "Files" => $files, "Type" => "invoice"];
                            require_once "class/attachment.php";
                            $attachment = new attachment();
                            $attachment->checkAttachments($Param);
                        }
                        flashMessage($invoice, $attachment);
                        header("Location: invoices.php?page=show&id=" . $invoice->Identifier);
                        exit;
                    }
                    if(0 < $invoice->Debtor) {
                        require_once "class/debtor.php";
                        $debtor = new debtor();
                        $debtor->Identifier = $invoice->Debtor;
                        $debtor->show();
                    }
                    foreach ($added_lines as $lineID) {
                        $invoiceelement = new invoiceelement();
                        $invoiceelement->Identifier = $lineID;
                        $invoiceelement->show();
                        $invoiceelement->delete();
                        if(0 < $invoiceelement->PeriodicID) {
                            require_once "class/periodic.php";
                            $periodic = new periodic();
                            $periodic->Identifier = $invoiceelement->PeriodicID;
                            $periodic->deleteFromDatabase($invoiceelement->PeriodicID);
                            $periodic->__destruct();
                            unset($periodic);
                        }
                    }
                    foreach ($_POST as $post_key => $post_value) {
                        if(in_array($post_key, $invoice->Variables) && is_string($post_value)) {
                            $invoice->{$post_key} = htmlspecialchars(esc($post_value));
                        }
                    }
                    $error_class->Error = array_merge($error_class->Error, $invoice->Error);
                    $invoice->Error = [];
                    $page = "add";
                }
                if($pagetype == "add") {
                    $invoice->NewNumber = strpos($invoice->InvoiceCode, "[concept]") !== false ? $invoice->newInvoiceCode() : $invoice->InvoiceCode;
                    $invoice->ConceptCode = $invoice->newConceptCode();
                    $invoice->NewDate = (int) $invoice->Status === 0 && strpos($invoice->InvoiceCode, "[concept]") !== false ? rewrite_date_db2site(date("Ymd")) : $invoice->Date;
                }
            }
        }
        break;
    case "view":
        require_once "class/invoice.php";
        if(!empty($_POST["ids"])) {
            switch ($_POST["action"]) {
                case "dialog:send_invoice_queue":
                case "dialog:send_invoice_concept":
                case "invoicesent":
                case "dialog:invoicesent":
                    if(!U_INVOICE_EDIT) {
                    } else {
                        $invoice = new invoice();
                        foreach ($_POST["ids"] as $key => $id) {
                            $invoice->Identifier = esc($id);
                            $invoice->show();
                            if($_POST["action"] == "dialog:send_invoice_concept" && 1 <= $invoice->Status || $_POST["action"] == "dialog:send_invoice_queue" && $invoice->Status != 1) {
                            } else {
                                $invoice->PartOfBatch = true;
                                $download_instead = isset($_POST["printtype"]) && esc($_POST["printtype"]) == "print" ? false : true;
                                $result = $invoice->sent(true, true, $download_instead);
                                if(count($_POST["ids"]) == $key + 1 && $invoice->hasPrintQueue) {
                                    $invoice->downloadBatchPDF("F");
                                    $_SESSION["force_download"] = $invoice->pdf->Name;
                                }
                            }
                        }
                    }
                    break;
                case "invoiceprint":
                case "dialog:invoiceprint":
                    $invoice = new invoice();
                    foreach ($_POST["ids"] as $key => $id) {
                        $invoice->Identifier = esc($id);
                        $invoice->show();
                        if(count($_POST["ids"]) == $key + 1) {
                            $invoice->LastOfBatch = true;
                        }
                        $invoice->PartOfBatch = true;
                        $download_instead = isset($_POST["printtype"]) && esc($_POST["printtype"]) == "print" ? false : true;
                        $invoice->printBatch(true, $download_instead);
                    }
                    break;
                case "remindersent":
                case "dialog:remindersent":
                    if(!U_INVOICE_EDIT) {
                    } else {
                        $invoice = new invoice();
                        foreach ($_POST["ids"] as $key => $id) {
                            $invoice->Identifier = esc($id);
                            $invoice->show();
                            $invoice->useInvoiceMethod = esc($_POST["radio_send_invoicemethod"]);
                            $invoice->PartOfBatch = true;
                            $download_instead = isset($_POST["printtype"]) && esc($_POST["printtype"]) == "print" ? false : true;
                            $invoice->sentReminder($download_instead);
                            if(count($_POST["ids"]) == $key + 1 && $invoice->hasPrintQueue) {
                                $invoice->LastOfBatch = true;
                                $invoice->downloadBatchPDF("F");
                                $_SESSION["force_download"] = $invoice->pdf->Name;
                            }
                        }
                    }
                    break;
                case "summationsent":
                case "dialog:summationsent":
                    if(!U_INVOICE_EDIT) {
                    } else {
                        $invoice = new invoice();
                        foreach ($_POST["ids"] as $key => $id) {
                            $invoice->Identifier = esc($id);
                            $invoice->show();
                            $invoice->useInvoiceMethod = esc($_POST["radio_send_invoicemethod"]);
                            $invoice->PartOfBatch = true;
                            $download_instead = isset($_POST["printtype"]) && esc($_POST["printtype"]) == "print" ? false : true;
                            $invoice->sentSummation($download_instead);
                            if(count($_POST["ids"]) == $key + 1 && $invoice->hasPrintQueue) {
                                $invoice->LastOfBatch = true;
                                $invoice->downloadBatchPDF("F");
                                $_SESSION["force_download"] = $invoice->pdf->Name;
                            }
                        }
                    }
                    break;
                case "markaspaid":
                case "dialog:markaspaid":
                    if(!U_INVOICE_EDIT) {
                    } else {
                        foreach ($_POST["ids"] as $key => $id) {
                            $invoice = new invoice();
                            $invoice->Identifier = esc($id);
                            $invoice->show();
                            if($invoice->Status <= 3 && substr($invoice->InvoiceCode, 0, 8) != "[concept") {
                                $invoice->markaspaid();
                                $invoice->checkAuto();
                            } elseif($invoice->Status <= 3 && substr($invoice->InvoiceCode, 0, 8) == "[concept") {
                                $invoice->Error[] = sprintf(__("invoice not sent, therefore not marked as paid"), $invoice->InvoiceCode);
                            }
                            flashMessage($invoice);
                            unset($invoice);
                        }
                    }
                    break;
                case "createcreditinvoice":
                case "dialog:createcreditinvoice":
                    if(!U_INVOICE_EDIT) {
                    } else {
                        foreach ($_POST["ids"] as $key => $id) {
                            $invoice = new invoice();
                            $invoice->Identifier = esc($id);
                            $invoice->createCreditInvoice();
                            flashMessage($invoice);
                            unset($invoice);
                        }
                    }
                    break;
                case "dialog:schedule_invoices":
                    $datetime = ($_POST["Date"] ? date("Y-m-d", strtotime(rewrite_date_site2db($_POST["Date"]))) : "") . " " . $_POST["Hours"] . ":" . $_POST["Minutes"] . ":00";
                    $success_messages = [];
                    foreach ($_POST["ids"] as $k => $invoiceID) {
                        $invoice = new invoice();
                        $invoice->Identifier = $invoiceID;
                        $invoice->show();
                        $result = $invoice->scheduleDraftInvoice($datetime);
                        if($result === true) {
                            $success_messages = array_merge($success_messages, $invoice->Success);
                        } else {
                            flashMessage($invoice);
                            unset($invoice);
                        }
                    }
                    if(1 < count($success_messages)) {
                        $invoice = new invoice();
                        $invoice->Success = [sprintf(__("multiple draft invoices scheduled"), count($success_messages), rewrite_date_db2site($datetime) . " " . __("at") . " " . rewrite_date_db2site($datetime, "%H:%i"))];
                    } else {
                        $invoice = new invoice();
                        $invoice->Success = array_merge($invoice->Success, $success_messages);
                    }
                    break;
                case "dialog:undo_schedule_invoices":
                    $success_messages = [];
                    foreach ($_POST["ids"] as $k => $invoiceID) {
                        $invoice = new invoice();
                        $invoice->Identifier = $invoiceID;
                        $invoice->show();
                        $result = $invoice->undoScheduleDraftInvoice();
                        if($result === true) {
                            $success_messages = array_merge($success_messages, $invoice->Success);
                        } else {
                            flashMessage($invoice);
                            unset($invoice);
                        }
                    }
                    if(1 < count($success_messages)) {
                        $invoice = new invoice();
                        $invoice->Success = [sprintf(__("multiple draft invoices schedule undone"), count($success_messages))];
                    } else {
                        $invoice = new invoice();
                        $invoice->Success = array_merge($invoice->Success, $success_messages);
                    }
                    break;
                case "dialog:delete_invoices":
                    if(!U_INVOICE_DELETE) {
                    } else {
                        foreach ($_POST["ids"] as $key => $id) {
                            $invoice = new invoice();
                            $invoice->Identifier = esc($id);
                            $invoice->show();
                            $invoice->delete(true);
                            flashMessage($invoice);
                            unset($invoice);
                        }
                    }
                    break;
                case "dialog:mergeconceptinvoices":
                    if(!U_INVOICE_EDIT) {
                    } else {
                        $invoice = new invoice();
                        $invoice->mergeConceptInvoices($_POST["ids"]);
                        flashMessage($invoice);
                        unset($invoice);
                    }
                    break;
                default:
                    unset($_POST["ids"]);
            }
        } elseif(isset($_POST["action"])) {
            $invoice->Warning[] = __("nothing selected");
        }
        flashMessage($invoice);
        if(isset($_POST["table_redirect_url"]) && $_POST["table_redirect_url"]) {
            header("Location: " . esc($_POST["table_redirect_url"]));
        } else {
            header("Location: invoices.php");
        }
        exit;
        break;
    default:
        if(isset($invoice_id) && isset($_GET["action"])) {
            switch ($_GET["action"]) {
                case "delete":
                    if(!U_INVOICE_DELETE) {
                    } elseif(isset($_POST["imsure"]) && $_POST["imsure"] == "yes") {
                        require_once "class/invoice.php";
                        $invoice = new invoice();
                        $invoice->Identifier = $invoice_id;
                        $invoice->show();
                        if($invoice->Status < 1 && substr($invoice->InvoiceCode, 0, 8) == "[concept") {
                            $invoice->delete(true);
                        } elseif($invoice->delete()) {
                            flashMessage($invoice);
                            header("Location: invoices.php?page=show&id=" . $invoice->Identifier);
                            exit;
                        }
                    }
                    break;
                default:
                    flashMessage($invoice);
                    header("Location:invoices.php");
                    exit;
            }
        } else {
            switch ($page) {
                case "overview":
                case "show":
                    require_once "class/invoice.php";
                    $invoice = isset($invoice) && is_object($invoice) ? $invoice : new invoice();
                    $invoice->Identifier = $invoice_id;
                    if(!$invoice->show()) {
                        flashMessage($invoice);
                        header("Location: invoices.php");
                        exit;
                    }
                    $invoice->format(false);
                    $invoice_element = new invoiceelement();
                    $invoice->Elements = $invoice_element->all($invoice->InvoiceCode);
                    require_once "class/attachment.php";
                    $attachment = new attachment();
                    $invoice->Attachment = $attachment->getAttachments($invoice_id, "invoice");
                    require_once "class/debtor.php";
                    $debtor = new debtor();
                    $debtor->Identifier = $invoice->Debtor;
                    $debtor->show();
                    $template = new template();
                    $template->Identifier = $invoice->Template;
                    $template->show();
                    require_once "class/logfile.php";
                    $logfile = new logfile();
                    $session = isset($_SESSION["invoice.show.logfile"]) ? $_SESSION["invoice.show.logfile"] : [];
                    $fields = ["Date", "Who", "Name", "Action", "Values", "Translate"];
                    $sort = isset($session["sort"]) ? $session["sort"] : "Date";
                    $order = isset($session["order"]) ? $session["order"] : "DESC";
                    $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : "1";
                    $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : min(25, MAX_RESULTS_LIST);
                    $history = $logfile->all($fields, $sort, $order, $limit, "invoice", $invoice_id, $show_results);
                    $_SESSION["invoice.show.logfile"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "limit" => $limit];
                    $current_page = $limit;
                    if(2 <= $invoice->Status && $invoice->Status < 4) {
                        require_once "class/paymentmethod.php";
                        $paymentmethod = new paymentmethod();
                        $payment_methods_available = 0 < count($paymentmethod->get_types([paymentmethod::AVAILABILITY_CLIENTAREA, paymentmethod::AVAILABILITY_ALWAYS])) ? true : false;
                    }
                    if(SDD_ID) {
                        $emailtemplate = new emailtemplate();
                        $emailtemplates = $emailtemplate->all(["Name"]);
                    }
                    if($invoice->VatShift == "yes" || $invoice->VatShift == "" && $invoice->AmountTax == money(0, false) && $invoice->Country != $company->Country && $invoice->CompanyName != "" && $invoice->TaxNumber != "" && ($invoice->Country == "NL" || in_array($invoice->Country, $_SESSION["wf_cache_array_country_EU"]))) {
                        $show_vatshift_text = __("default vatshift text");
                    } else {
                        $show_vatshift_text = false;
                    }
                    require_once "class/bankstatement.php";
                    $transaction_matches = new Transaction_Matches_Model();
                    $transaction_matches_options = $transaction_matches->getTransactionMatchesTable("invoice", $invoice->Identifier);
                    $accounting_transactions = $invoice->hasAccountingTransactions();
                    if((int) $invoice->Status === 0 && $invoice->SubStatus == "BLOCKED") {
                        $unblock_link = "[hyperlink_1]invoices.php?page=show&action=unblock&id=" . $invoice->Identifier . "[hyperlink_2]" . __("unblock invoice link") . "[hyperlink_3]";
                        $invoice->Info[] = sprintf(__("warning invoice blocked, cannot send"), $unblock_link);
                    } elseif($invoice->Status == "0" && $invoice->SentDate != "0000-00-00 00:00:00") {
                        $undo_schedule_link = "[hyperlink_1]invoices.php?page=show&action=undo_schedule&id=" . $invoice->Identifier . "[hyperlink_2]" . __("undo schedule draft invoice link") . "[hyperlink_3]";
                        $_SentDate = new DateTime($invoice->SentDate);
                        if($invoice->InvoiceMethod == "0") {
                            $invoice->Info[] = sprintf(__("info message draft invoice scheduled, automatic sending"), rewrite_date_db2site($invoice->SentDate) . " " . __("at") . " " . $_SentDate->format("H:i"), $undo_schedule_link);
                        } else {
                            $invoice->Info[] = sprintf(__("info message draft invoice scheduled, manual sending"), rewrite_date_db2site($invoice->SentDate) . " " . __("at") . " " . $_SentDate->format("H:i"), $undo_schedule_link);
                        }
                    }
                    if((int) $invoice->Status === 0 && strpos($invoice->InvoiceCode, __("[draft_prefix]")) === false || 1 <= $invoice->Status && $invoice->Status < 8) {
                        $related_invoices = $invoice->all(["InvoiceCode", "Date"], "InvoiceCode", "ASC", "-1", "CorrespondingInvoice", $invoice->Identifier);
                    }
                    if(0 < $invoice->CorrespondingInvoice) {
                        $correspondingInvoice = new invoice();
                        $correspondingInvoice->Identifier = $invoice->CorrespondingInvoice;
                        $correspondingInvoice->show();
                    }
                    $message = parse_message($invoice, $debtor, $logfile, $attachment);
                    $wfh_page_title = __("invoice") . " " . $invoice->InvoiceCode . ($invoice->Authorisation == "yes" ? __("invoice authed") : "");
                    $current_page_url = "invoices.php?page=show&id=" . $invoice->Identifier;
                    $sidebar_template = "invoice.sidebar.php";
                    require_once "views/invoice.show.php";
                    break;
                case "add":
                    checkRight(U_INVOICE_EDIT);
                    require_once "class/debtor.php";
                    $debtor = isset($debtor) && is_object($debtor) ? $debtor : new debtor();
                    $fields = ["Initials", "SurName", "CompanyName", "TaxNumber", "DebtorCode"];
                    $debtors = $debtor->all($fields);
                    require_once "class/invoice.php";
                    $invoice = isset($invoice) && is_object($invoice) ? $invoice : new invoice();
                    if(!empty($invoice_id) && 0 < $invoice_id) {
                        $invoice->Identifier = $invoice_id;
                        $invoice->show();
                        $invoice->format();
                        if(empty($invoice->Attachment)) {
                            require_once "class/attachment.php";
                            $attachment = new attachment();
                            $invoice->Attachment = $attachment->getAttachments($invoice_id, "invoice");
                        }
                        if(!is_array($debtors) || !in_array($invoice->Debtor, array_keys($debtors))) {
                            $debtor->show($invoice->Debtor);
                            if($debtor->Anonymous == "yes") {
                                $error_class->Warning[] = __("this debtor has been made anonymous and cannot be used again");
                                if((int) $invoice->Status === 0) {
                                    $invoice->Debtor = 0;
                                }
                            } else {
                                $error_class->Warning[] = __("this debtor is removed, only use as archive");
                            }
                            if(0 < $invoice->Debtor) {
                                $debtors[$invoice->Debtor] = ["id" => $invoice->Debtor, "Initials" => $debtor->Initials, "SurName" => $debtor->SurName, "CompanyName" => $debtor->CompanyName, "DebtorCode" => $debtor->DebtorCode];
                            }
                        } else {
                            $debtor->Identifier = $invoice->Debtor;
                            $debtor->show();
                        }
                    } else {
                        if(isset($_GET["debtor"]) && 0 < $_GET["debtor"]) {
                            $invoice->Debtor = intval(esc($_GET["debtor"]));
                            $debtor = new debtor();
                            $debtor->Identifier = $invoice->Debtor;
                            $debtor->show();
                            if($debtor->Status == 9 && $debtor->Anonymous == "yes") {
                                $debtor->Error[] = __("this debtor has been made anonymous and cannot be used again");
                                flashMessage($debtor);
                                header("Location: invoices.php");
                                exit;
                            }
                            $invoice->InvoiceMethod = $debtor->InvoiceMethod;
                            $invoice->Term = $debtor->InvoiceTerm !== NULL ? $debtor->InvoiceTerm : $invoice->Term;
                            $invoice->Template = 0 < $debtor->InvoiceTemplate ? $debtor->InvoiceTemplate : $invoice->Template;
                            $invoice->CompanyName = $debtor->InvoiceCompanyName ? $debtor->InvoiceCompanyName : $debtor->CompanyName;
                            $invoice->TaxNumber = $debtor->TaxNumber;
                            $invoice->Sex = $debtor->InvoiceSurName ? $debtor->InvoiceSex : $debtor->Sex;
                            $invoice->Initials = $debtor->InvoiceInitials ? $debtor->InvoiceInitials : $debtor->Initials;
                            $invoice->SurName = $debtor->InvoiceSurName ? $debtor->InvoiceSurName : $debtor->SurName;
                            $invoice->Address = $debtor->InvoiceAddress ? $debtor->InvoiceAddress : $debtor->Address;
                            $invoice->Address2 = $debtor->InvoiceAddress2 ? $debtor->InvoiceAddress2 : $debtor->Address2;
                            $invoice->ZipCode = $debtor->InvoiceZipCode ? $debtor->InvoiceZipCode : $debtor->ZipCode;
                            $invoice->City = $debtor->InvoiceCity ? $debtor->InvoiceCity : $debtor->City;
                            $invoice->State = $debtor->InvoiceState ? $debtor->InvoiceState : $debtor->State;
                            $invoice->StateName = $debtor->InvoiceStateName ? $debtor->InvoiceStateName : $debtor->StateName;
                            $invoice->Country = $debtor->InvoiceCountry && $debtor->InvoiceAddress ? $debtor->InvoiceCountry : $debtor->Country;
                            $invoice->EmailAddress = $debtor->InvoiceEmailAddress ? $debtor->InvoiceEmailAddress : $debtor->EmailAddress;
                            $invoice->Authorisation = $debtor->InvoiceAuthorisation;
                            if(!empty($debtor->customfields_list)) {
                                foreach ($debtor->customfields_list as $k => $custom_field) {
                                    $invoice->customvalues[$custom_field["FieldCode"]] = $debtor->customvalues[$custom_field["FieldCode"]];
                                }
                            }
                        } elseif(isset($_GET["clone"]) && 0 < $_GET["clone"]) {
                            $invoice->Identifier = esc($_GET["clone"]);
                            if($invoice->show()) {
                                $debtor->Identifier = $invoice->Debtor;
                                $debtor->show();
                                if($debtor->Status == 9 && $debtor->Anonymous == "yes") {
                                    $invoice->Debtor = 0;
                                    unset($debtor);
                                }
                                $invoice->InvoiceCode = $invoice->newConceptCode();
                                $invoice->Status = 0;
                                $invoice->Identifier = 0;
                                $invoice->Date = rewrite_date_db2site(date("Ymd"));
                                $invoice->SentDate = "";
                                $invoice->Sent = 0;
                                $invoice->Reminders = 0;
                                $invoice->ReminderDate = "";
                                $invoice->Summations = 0;
                                $invoice->SummationDate = "";
                                $invoice->AmountPaid = 0;
                                $invoice->PaymentMethod = "";
                                $invoice->PaymentMethodID = 0;
                                $invoice->Paid = 0;
                                $invoice->PayDate = "";
                                $invoice->TransactionID = "";
                                $invoice->SDDBatchID = "";
                                $invoice->AuthTrials = 0;
                                $invoice->Attachment = [];
                                $invoice->SubStatus = "";
                                $x = 0;
                                $warn_subscriptions = false;
                                foreach ($invoice->Elements as $key => $data) {
                                    if(is_numeric($key)) {
                                        $_POST["Date"][$x] = $data["Date"] ? rewrite_date_db2site(date("Ymd")) : "";
                                        $_POST["Number"][$x] = $data["Number"];
                                        $_POST["NumberSuffix"][$x] = $data["NumberSuffix"];
                                        $_POST["ProductCode"][$x] = $data["ProductCode"];
                                        $_POST["Description"][$x] = htmlspecialchars_decode($data["Description"]);
                                        if($data["Periodic"]) {
                                            $warn_subscriptions = true;
                                        }
                                        $_POST["Periods"][$x] = $data["Periods"];
                                        $_POST["Periodic"][$x] = $data["Periodic"];
                                        $_POST["StartPeriod"][$x] = $data["StartPeriod"];
                                        $_POST["EndPeriod"][$x] = $data["EndPeriod"];
                                        $_POST["PeriodicType"][$x] = $data["Periodic"] == "" ? "once" : "period";
                                        $_POST["TaxPercentage"][$x] = $data["TaxPercentage"];
                                        $_POST["PriceIncl"][$x] = $invoice->VatCalcMethod == "incl" ? $data["PriceIncl"] : round((double) $data["PriceIncl"], 5);
                                        $_POST["PriceExcl"][$x] = $invoice->VatCalcMethod == "excl" ? $data["PriceExcl"] : round((double) $data["PriceExcl"], 5);
                                        $_POST["DiscountPercentage"][$x] = $data["DiscountPercentage"] * 100;
                                        $_POST["DiscountPercentageType"][$x] = $data["DiscountPercentageType"];
                                        $x++;
                                    }
                                }
                                if($warn_subscriptions === true) {
                                    $error_class->Warning[] = __("warning duplicate invoice, subscriptions on invoice");
                                }
                                $_POST["NumberOfElements"] = $x;
                                if(!empty($_POST["copyAttachments"])) {
                                    require_once "class/attachment.php";
                                    $attachment = new attachment();
                                    $invoice->Attachment = $attachment->getAttachments(esc($_GET["clone"]), "invoice");
                                    $attachment_array = [];
                                    foreach ($invoice->Attachment as $k => $_file) {
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
                                    $invoice->Attachment = buildAttachmentArray($attachment_array);
                                }
                            }
                        }
                        if(!$invoice->InvoiceCode) {
                            $invoice->InvoiceCode = $invoice->newConceptCode();
                        }
                    }
                    $invoice->NewNumber = strpos($invoice->InvoiceCode, "[concept]") !== false ? $invoice->newInvoiceCode() : $invoice->InvoiceCode;
                    $invoice->ConceptCode = INVOICECODE_CONCEPT == "no" ? $invoice->InvoiceCode : $invoice->newConceptCode();
                    $invoice->NewDate = (int) $invoice->Status === 0 && strpos($invoice->InvoiceCode, "[concept]") !== false ? rewrite_date_db2site(date("Ymd")) : $invoice->Date;
                    require_once "class/product.php";
                    $product = new product();
                    $fields = ["ProductCode", "ProductName", "ProductKeyPhrase", "PriceExcl", "PricePeriod", "TaxPercentage"];
                    $products = $product->all($fields, "ProductCode", "ASC", -1);
                    require_once "class/template.php";
                    $templates = new template();
                    $fields = ["Name"];
                    $templates = $templates->all($fields, "Name", "ASC", "", "Type", "invoice");
                    if($_GET["page"] == "edit" && 2 <= $invoice->Status && 0 < $invoice->Sent) {
                        $invoice->Warning[] = __("invoice editwarning if already sent");
                    }
                    $message = parse_message($invoice, $debtor, $product, isset($attachment) ? $attachment : NULL);
                    $wfh_page_title = $_GET["page"] == "add" ? __("create invoice") : ($_GET["page"] == "edit" ? __("edit invoice") : "");
                    $current_page_url = "invoices.php?page=add&id=" . $invoice->Identifier;
                    $sidebar_template = "invoice.sidebar.php";
                    require_once "views/invoice.add.php";
                    break;
                default:
                    require_once "class/invoice.php";
                    $invoice = isset($invoice) && $invoice ? $invoice : new invoice();
                    if(isset($_SESSION["backoffice_tables_config"]["list_invoice"]["filter"])) {
                        $invoice_table_options = $invoice->getConfigInvoiceTable(["filter" => $_SESSION["backoffice_tables_config"]["list_invoice"]["filter"]]);
                        $invoice_table_options["filter"] = $_SESSION["backoffice_tables_config"]["list_invoice"]["filter"];
                    } else {
                        $invoice_table_options = $invoice->getConfigInvoiceTable();
                    }
                    $message = parse_message($invoice);
                    $wfh_page_title = __("invoice overview");
                    $current_page_url = "invoices.php";
                    $sidebar_template = "invoice.sidebar.php";
                    require_once "views/invoice.overview.php";
            }
        }
}

?>