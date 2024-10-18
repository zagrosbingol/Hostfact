<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
register_shutdown_function("index_cronjob_error_handler");
define("SCRIPT_IS_INDEX_CRONJOB", true);
define("InStAlLHosTFacT", "Ra.33sUdhWlkd22");
define("LOGINSYSTEM", true);
require_once "config.php";
if(hasCronCollision() === true) {
    $executeLoginActionsOnly = true;
} else {
    $executeLoginActionsOnly = false;
}
require_once "class/automation.php";
$automation = new automation();
$automation->show();
$IndexCronjobCounter = isset($_POST["IndexCronjobCounter"]) && is_numeric($_POST["IndexCronjobCounter"]) ? (int) $_POST["IndexCronjobCounter"] : 0;
if($IndexCronjobCounter === 0) {
    if($executeLoginActionsOnly === false) {
        require_once "class/invoice.php";
        $invoicel = new invoice();
        $result = $invoicel->all(["InvoiceCode"], "", "", "", "Paid", "2", "4");
        foreach ($result as $key => $value) {
            if(is_numeric($key)) {
                $invoice = new invoice();
                $invoice->Identifier = $key;
                $invoice->show();
                if($invoice->Status == 4 && $invoice->Paid == 2) {
                    Database_Model::getInstance()->update("HostFact_Invoice", ["Paid" => "1", "PayDate" => ["RAW" => "CURDATE()"]])->where("id", $invoice->Identifier)->execute();
                    $invoice->checkAuto();
                }
                flashMessage($invoice);
                unset($invoice);
            }
        }
        flashMessage($invoicel);
        unset($invoicel);
        $invoice = new invoice();
        $invoice->cronAccountingTransactions();
        flashMessage($invoice);
        unset($invoice);
    }
    if($automation->acceptorder_value == 1 && $automation->acceptorder_run != "cronjob" && ($executeLoginActionsOnly === false || $automation->acceptorder_run == "login")) {
        require_once "class/neworders.php";
        $order = new neworder();
        $order->acceptorders();
        flashMessage($order);
        unset($order);
    }
    if(0 < PERIODIC_REMINDER_DAYS && $executeLoginActionsOnly === false) {
        require_once "class/periodic.php";
        $periodic = new periodic();
        $periodic->checkReminders();
        flashMessage($periodic);
        unset($periodic);
    }
    if($automation->makeinvoice_value == 1 && $automation->makeinvoice_run != "cronjob" && ($executeLoginActionsOnly === false || $automation->makeinvoice_run == "login")) {
        require_once "class/periodic.php";
        $periodic = new periodic();
        $periodic->makeinvoice();
        flashMessage($periodic);
        unset($periodic);
    }
    if($automation->sentinvoice_value == 1 && $automation->sentinvoice_run != "cronjob" && ($executeLoginActionsOnly === false || $automation->sentinvoice_run == "login")) {
        $_exceptions = $automation->sentinvoice_exception ? json_decode($automation->sentinvoice_exception, true) : [];
        if(empty($_exceptions) || $_exceptions["Hours"] < date("H") || date("H") == $_exceptions["Hours"] && $_exceptions["Minutes"] <= date("i")) {
            require_once "class/invoice.php";
            $invoice = new invoice();
            $invoice->sentinvoices(false);
            if(!empty($invoice->Success)) {
                $invoice->Success = [sprintf(__("invoices sent by index cronjob succesfully"), count($invoice->Success))];
            }
            flashMessage($invoice);
            unset($invoice);
        }
    }
    if($automation->makebackup_value == 1 && $automation->makebackup_run != "cronjob" && ($executeLoginActionsOnly === false || $automation->makebackup_run == "login")) {
        require_once "class/backup.php";
        $backup = new backup();
        $backup->make();
        flashMessage($backup);
        unset($backup);
    }
    if(TICKET_USE == 1 && $automation->checkticket_value == 1 && $automation->checkticket_run != "cronjob" && TICKET_USE_MAIL == 1 && ($executeLoginActionsOnly === false || $automation->checkticket_run == "login")) {
        require_once "class/ticket.php";
        $m = new mailserver();
        $m->fromCronjob = true;
        $m->checkMail();
        flashMessage($m);
        unset($m);
    }
    if($executeLoginActionsOnly === false) {
        $result_db = Database_Model::getInstance()->get("HostFact_Agenda", "id")->where("EmailNotify", [">=" => "0"])->where("(`Date` - INTERVAL `EmailNotify` DAY)", ["<=" => ["RAW" => "CURDATE()"]])->where("Status", "1")->execute();
        if(!empty($result_db)) {
            require_once "class/agenda.php";
            $agendaItem = new agenda();
            foreach ($result_db as $row) {
                $agendaItem->Identifier = $row->id;
                if($agendaItem->show()) {
                    $agendaItem->sent();
                }
            }
            flashMessage($agendaItem);
            unset($agendaItem);
        }
    }
    if($automation->remindersummation_value == 1 && $automation->remindersummation_run != "cronjob" && ($executeLoginActionsOnly === false || $automation->remindersummation_run == "login")) {
        $_exceptions = $automation->remindersummation_exception ? json_decode($automation->remindersummation_exception, true) : [];
        if(empty($_exceptions) || $_exceptions["Hours"] < date("H") || date("H") == $_exceptions["Hours"] && $_exceptions["Minutes"] <= date("i")) {
            require_once "class/invoice.php";
            $invoices = new invoice();
            $fields = ["SubStatus", "InvoiceCode", "Date", "SentDate", "InvoiceMethod", "Reminders", "ReminderDate", "Summations", "SummationDate", "Term", "Authorisation", "AmountPaid", "AmountIncl"];
            $invoice_open = $invoices->all($fields, "Date` DESC, `InvoiceCode", "DESC", -1, "", "", "2|3");
            foreach ($invoice_open as $key => $value) {
                if(is_numeric($key) && $value["SubStatus"] != "PAUSED" && ((int) $value["InvoiceMethod"] === 0 || $value["InvoiceMethod"] == 3 || $value["InvoiceMethod"] == 4)) {
                    if($value["PartPayment"] <= 0 || $value["Authorisation"] == "yes") {
                    } elseif((int) $value["Reminders"] === 0 && str_replace("-", "", substr($value["PayBefore"], 0, 10)) < date("Ymd")) {
                        $invoice = new invoice();
                        $invoice->Identifier = $key;
                        $invoice->show();
                        $invoice->sentReminder();
                    } elseif(0 < $value["Reminders"] && str_replace("-", "", substr($value["PayBefore"], 0, 10)) < date("Ymd") && $value["Reminders"] < INVOICE_REMINDER_NUMBER && str_replace("-", "", date("Y-m-d", strtotime($value["ReminderDate"]) + 86400 * INVOICE_REMINDER_TERM)) <= date("Ymd")) {
                        $invoice = new invoice();
                        $invoice->Identifier = $key;
                        $invoice->show();
                        $invoice->sentReminder();
                    } elseif((int) $value["Summations"] === 0 && INVOICE_REMINDER_NUMBER <= $value["Reminders"] && str_replace("-", "", substr($value["PayBefore"], 0, 10)) < date("Ymd") && $value["Summations"] < INVOICE_SUMMATION_NUMBER && str_replace("-", "", date("Y-m-d", strtotime($value["ReminderDate"]) + 86400 * INVOICE_SUMMATION_TERM)) <= date("Ymd")) {
                        $invoice = new invoice();
                        $invoice->Identifier = $key;
                        $invoice->show();
                        $invoice->sentSummation();
                    } elseif(0 < $value["Summations"] && INVOICE_REMINDER_NUMBER <= $value["Reminders"] && str_replace("-", "", substr($value["PayBefore"], 0, 10)) < date("Ymd") && $value["Summations"] < INVOICE_SUMMATION_NUMBER && str_replace("-", "", date("Y-m-d", strtotime($value["SummationDate"]) + 86400 * INVOICE_SUMMATION_TERM)) <= date("Ymd")) {
                        $invoice = new invoice();
                        $invoice->Identifier = $key;
                        $invoice->show();
                        $invoice->sentSummation();
                    }
                    if(isset($invoice)) {
                        flashMessage($invoice);
                    }
                    unset($invoice);
                }
            }
        }
    }
    if($executeLoginActionsOnly === false) {
        Database_Model::getInstance()->delete("HostFact_MessageLog")->where("Date", ["<" => ["RAW" => "DATE_ADD(CURDATE(), INTERVAL -40 DAY)"]])->execute();
    }
    if(SDD_ID && $executeLoginActionsOnly === false) {
        require_once "class/directdebit.php";
        $directdebit = new directdebit();
        $directdebit->cronDirectDebit();
        flashMessage($directdebit);
    }
    require_once "class/attachment.php";
    $attachment = new attachment();
    $attachment->cronCleanUp();
    flashMessage($attachment);
    require_once "class/terminationprocedure.php";
    $termination = new Termination_Model();
    $termination->cronTasks();
    $termination_actions = new Action_Model("termination");
    $termination_actions->cronTasks();
    flashMessage($termination_actions);
    require_once "class/apilogfile.php";
    $apilogfile = new apilogfile();
    $apilogfile->cleanUp();
    do_action("employee_login_task", $executeLoginActionsOnly);
    require_once "class/backup.php";
    $backup = new backup();
    $backup->deleteOldBackups();
    flashMessage($backup);
    unset($backup);
    Database_Model::getInstance()->delete("HostFact_FailedLoginAttempts")->where("DateTime", ["<" => ["RAW" => "DATE_ADD(CURDATE(), INTERVAL -14 DAY)"]])->where("Type", "clientarea")->execute();
}
$emails_todo = 0;
$hosting_todo = 0;
$domain_todo = 0;
if(SENT_BATCHES == "1" && $automation->batchmail_value == 1 && $automation->batchmail_run != "cronjob" && ($executeLoginActionsOnly === false || $automation->batchmail_run == "login")) {
    require_once "class/email.php";
    $emails = new email();
    $result = $emails->all(["Status"], "", "", "", "", "", "0");
    $max_versturen_tegelijk = MAX_SENT_BATCHES;
    $reeds_verstuurd = 0;
    if($result) {
        foreach ($result as $k => $v) {
            if(is_numeric($k)) {
                $email = new email();
                $email->Identifier = $k;
                $email->show();
                if($reeds_verstuurd < $max_versturen_tegelijk) {
                    $email->Sent_bcc = false;
                    $email->sent("", "", false);
                    $reeds_verstuurd += 1;
                    flashMessage($email);
                    unset($email);
                } else {
                    unset($email);
                    $emails_todo = max(0, $result["CountRows"] - $max_versturen_tegelijk);
                }
            }
        }
    } else {
        $emails_todo = 0;
    }
    flashMessage($emails);
    unset($emails);
}
$x = 0;
if($automation->makeaccount_value == 1 && $executeLoginActionsOnly === false) {
    require_once "class/hosting.php";
    $hosting = new hosting();
    $fields = ["Username", "Domain"];
    $result_hosting = $hosting->all($fields, "", "", -1, "", "", "3");
    if(!isset($_SESSION["index_cronjob"]["hosting"])) {
        $_SESSION["index_cronjob"]["hosting"] = [];
    }
    foreach ($result_hosting as $k => $v) {
        if($x <= 0 && is_numeric($k) && $v["Domain"] && !array_key_exists($k, $_SESSION["index_cronjob"]["hosting"])) {
            $hosting->Identifier = $v["id"];
            $hosting->show();
            $hosting->create();
            $x++;
            $_SESSION["index_cronjob"]["hosting"][$k] = ["Username" => $v["Username"], "Domain" => $v["Domain"], "Error" => implode(" ", $hosting->Error)];
        }
    }
    $hosting_todo = max(0, $result_hosting["CountRows"] - count($_SESSION["index_cronjob"]["hosting"]));
    flashMessage($hosting);
    unset($hosting);
}
if($automation->registerdomain_value == 1 && $executeLoginActionsOnly === false) {
    require_once "class/domain.php";
    $domain = new domain();
    $fields = ["Type", "Domain", "Tld"];
    $result_domain = $domain->all($fields, "", "", -1, "", "", "3");
    if(!isset($_SESSION["index_cronjob"]["domain"])) {
        $_SESSION["index_cronjob"]["domain"] = [];
    }
    foreach ($result_domain as $k => $v) {
        if($x <= 0 && is_numeric($k) && $v["Domain"] && !array_key_exists($k, $_SESSION["index_cronjob"]["domain"])) {
            $domain->Identifier = $v["id"];
            $domain->show();
            if($automation->registerdomain_exception == "transfer") {
                $domain->TransferAutoBlock = true;
            }
            if($v["Type"] == "transfer") {
                $domain->transfer();
            } else {
                $domain->register();
            }
            $x++;
            $_SESSION["index_cronjob"]["domain"][$k] = ["Domain" => $v["Domain"], "Tld" => $v["Tld"], "Error" => implode(" ", $domain->Error)];
        }
    }
    $domain_todo = max(0, $result_domain["CountRows"] - count($_SESSION["index_cronjob"]["domain"]));
    flashMessage($domain);
    unset($domain);
}
$result_db = Database_Model::getInstance()->get("HostFact_DomainsPending")->where("NextDate", ["<=" => ["RAW" => "NOW()"]])->execute();
if($result_db && is_array($result_db)) {
    foreach ($result_db as $_domain) {
        require_once "class/domain.php";
        $domain = new domain();
        $domain->doPending($_domain->DomainID);
    }
}
$handle = opendir("temp/");
while (false !== ($f = readdir($handle))) {
    if($f != "." && $f != ".." && $f != "index.php" && $f != ".htaccess" && $f != "stats_summary.php" && @filemtime("temp/" . $f) < time() - 14400) {
        @unlink("temp/" . $f);
    }
}
closedir($handle);
if(isset($_SESSION["index_cronjob"]["results"]) && is_array($_SESSION["index_cronjob"]["results"])) {
    if(isset($_SESSION["index_cronjob"]["results"]["Error"])) {
        $error_class->Error = array_merge($_SESSION["index_cronjob"]["results"]["Error"], $error_class->Error);
    }
    if(isset($_SESSION["index_cronjob"]["results"]["Warning"])) {
        $error_class->Warning = array_merge($_SESSION["index_cronjob"]["results"]["Warning"], $error_class->Warning);
    }
    if(isset($_SESSION["index_cronjob"]["results"]["Success"])) {
        $error_class->Success = array_merge($_SESSION["index_cronjob"]["results"]["Success"], $error_class->Success);
    }
}
if($emails_todo + $hosting_todo + $domain_todo === 0) {
    $_SESSION["index_cronjob"] = [];
    $_SESSION["index_cronjob"]["ready"] = "1";
}
if($executeLoginActionsOnly === false) {
    $handle = opendir("temp/");
    while (false !== ($f = readdir($handle))) {
        if($f != "." && $f != ".." && $f != "index.php" && $f != ".htaccess" && $f != "stats_summary.php" && 900 < time() - filemtime("temp/" . $f)) {
            @unlink("temp/" . $f);
        }
    }
    closedir($handle);
}
if($executeLoginActionsOnly === false) {
    $settings->Variable = "CRONJOB_IS_RUNNING";
    $settings->Value = "";
    $settings->edit();
}
if($emails_todo + $hosting_todo + $domain_todo === 0) {
    echo $emails_todo + $hosting_todo + $domain_todo;
    $message = parse_message();
    if($message) {
        echo $message;
    }
    exit;
}
flashMessage();
echo $emails_todo + $hosting_todo + $domain_todo;
echo "|" . $emails_todo . "|" . $hosting_todo . "|" . $domain_todo;
$_SESSION["index_cronjob"]["results"] = ["Error" => isset($_SESSION["flashMessage"]["Error"]) ? $_SESSION["flashMessage"]["Error"] : [], "Warning" => isset($_SESSION["flashMessage"]["Warning"]) ? $_SESSION["flashMessage"]["Warning"] : [], "Success" => isset($_SESSION["flashMessage"]["Success"]) ? $_SESSION["flashMessage"]["Success"] : []];
unset($_SESSION["flashMessage"]);
exit;
function index_cronjob_error_handler()
{
    $error = error_get_last();
    if($error && in_array($error["type"], [1, 4, 16, 64, 256])) {
        echo $error["message"];
        exit;
    }
}

?>