<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
if(isset($_SERVER["SCRIPT_FILENAME"]) && $_SERVER["SCRIPT_FILENAME"]) {
    $basedir = str_replace("cronjob.php", "", $_SERVER["SCRIPT_FILENAME"]);
    if(!empty($basedir)) {
        chdir($basedir);
    }
}
$argv = isset($_SERVER["argv"]) ? $_SERVER["argv"] : false;
if(is_array($argv) && 1 < count($argv)) {
    array_shift($argv);
    $execute_parts = $argv;
} else {
    $execute_parts = ["all"];
}
define("SCRIPT_IS_CRONJOB", true);
define("InStAlLHosTFacT", "Ra.33sUdhWlkd22");
define("LOGINSYSTEM", true);
require_once "config.php";
if(defined("SOFTWARE_FILE_VERSION") && SOFTWARE_FILE_VERSION != SOFTWARE_VERSION) {
    exit;
}
if(hasCronCollision() === true) {
    exit;
}
require_once "class/automation.php";
$automation = new automation();
$automation->show();
require_once "class/company.php";
$company = new company();
$company->show();
if(defined("CRONJOB_LASTDATE")) {
    $settings->Variable = "CRONJOB_LASTDATE";
    $settings->Value = date("Y-m-d H:i:s");
    $settings->edit();
}
if(in_array("all", $execute_parts) || in_array("ideal", $execute_parts)) {
    require_once "class/invoice.php";
    $invoicel = new invoice();
    $result = $invoicel->all(["InvoiceCode"], "", "", "", "Paid", "2");
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
}
require_once "class/invoice.php";
$invoice = new invoice();
$invoice->cronAccountingTransactions();
if((in_array("all", $execute_parts) || in_array("order", $execute_parts)) && $automation->acceptorder_value == 1 && $automation->acceptorder_run != "login") {
    require_once "class/neworders.php";
    $order = new neworder();
    $order->acceptorders();
    flashMessage($order);
    unset($order);
}
if((in_array("all", $execute_parts) || in_array("periodicreminder", $execute_parts)) && 0 < PERIODIC_REMINDER_DAYS) {
    require_once "class/periodic.php";
    $periodic = new periodic();
    $periodic->checkReminders();
    flashMessage($periodic);
    unset($periodic);
}
if((in_array("all", $execute_parts) || in_array("periodicinvoice", $execute_parts)) && $automation->makeinvoice_value == 1 && $automation->makeinvoice_run != "login") {
    require_once "class/periodic.php";
    $periodic = new periodic();
    $periodic->makeinvoice();
    flashMessage($periodic);
    unset($periodic);
}
if((in_array("all", $execute_parts) || in_array("sendinvoice", $execute_parts)) && $automation->sentinvoice_value == 1 && $automation->sentinvoice_run != "login") {
    $_exceptions = $automation->sentinvoice_exception ? json_decode($automation->sentinvoice_exception, true) : [];
    if(empty($_exceptions) || $_exceptions["Hours"] < date("H") || date("H") == $_exceptions["Hours"] && $_exceptions["Minutes"] <= date("i")) {
        require_once "class/invoice.php";
        $invoice = new invoice();
        $invoice->sentinvoices(false);
        $invoice->Success = [];
        flashMessage($invoice);
        unset($invoice);
    }
}
if(in_array("all", $execute_parts) || in_array("sendscheduledinvoice", $execute_parts)) {
    require_once "class/invoice.php";
    $invoice = new invoice();
    $invoices_scheduled = $invoice->all(["InvoiceCode", "Debtor", "SentDate", "InvoiceMethod"], "SentDate", "ASC", "-1", false, false, "draft_scheduled_sendable");
    if(is_array($invoices_scheduled) && isset($invoices_scheduled["CountRows"]) && 0 < $invoices_scheduled["CountRows"]) {
        $invoices_sent_counter = 0;
        $now = new DateTime();
        foreach ($invoices_scheduled as $k => $inv) {
            if(is_numeric($k) && isset($inv["InvoiceMethod"]) && (int) $inv["InvoiceMethod"] === 0) {
                $invoice = new invoice();
                $invoice->Identifier = $inv["id"];
                if($invoice->show()) {
                    $invoice->sent();
                    $invoices_sent_counter++;
                }
            }
            if(100 <= $invoices_sent_counter) {
            }
        }
    }
}
if(in_array("all", $execute_parts) || in_array("backupdelete", $execute_parts)) {
    require_once "class/backup.php";
    $backup = new backup();
    $backup->deleteOldBackups();
    flashMessage($backup);
    unset($backup);
}
if((in_array("all", $execute_parts) || in_array("ticket", $execute_parts)) && TICKET_USE == 1 && $automation->checkticket_value == 1 && $automation->checkticket_run != "login" && TICKET_USE_MAIL == 1) {
    require_once "class/ticket.php";
    $m = new mailserver();
    $m->fromCronjob = true;
    $m->checkMail();
    flashMessage($m);
    unset($m);
}
$emails_todo = 0;
$hosting_todo = 0;
$domain_todo = 0;
if((in_array("all", $execute_parts) || in_array("mail", $execute_parts)) && SENT_BATCHES == "1" && $automation->batchmail_value == 1 && $automation->batchmail_run != "login") {
    require_once "class/email.php";
    $emails = new email();
    $result = $emails->all(["Status"], "", "", 1, "", "", "0", MAX_SENT_BATCHES);
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
                }
                flashMessage($email);
                unset($email);
            }
        }
        $emails_todo = max(0, $result["CountRows"] - $max_versturen_tegelijk);
    } else {
        $emails_todo = 0;
    }
    flashMessage($emails);
    unset($emails);
}
if(in_array("all", $execute_parts) || in_array("terminations", $execute_parts)) {
    require_once "class/terminationprocedure.php";
    $termination = new Termination_Model();
    $termination->cronTasks();
    $termination_actions = new Action_Model("termination");
    $termination_actions->cronTasks();
}
$x = 0;
if((in_array("all", $execute_parts) || in_array("hosting", $execute_parts)) && $automation->makeaccount_value == 1) {
    require_once "class/hosting.php";
    $hosting = new hosting();
    $fields = ["Username", "Domain"];
    $result_hosting = $hosting->all($fields, "", "", "", "", "", "3");
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
if((in_array("all", $execute_parts) || in_array("domain", $execute_parts)) && $automation->registerdomain_value == 1) {
    require_once "class/domain.php";
    $domain = new domain();
    $fields = ["Type", "Domain", "Tld"];
    $result_domain = $domain->all($fields, "", "", "", "", "", "3");
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
if(in_array("all", $execute_parts) || in_array("domain", $execute_parts)) {
    $result_db = parent::getInstance()->get("HostFact_DomainsPending")->where("NextDate", ["<=" => ["RAW" => "NOW()"]])->execute();
    if($result_db && is_array($result_db)) {
        foreach ($result_db as $_domain) {
            require_once "class/domain.php";
            $domain = new domain();
            $domain->doPending($_domain->DomainID);
        }
    }
}
if(in_array("all", $execute_parts) || in_array("agenda", $execute_parts)) {
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
if((in_array("all", $execute_parts) || in_array("reminder", $execute_parts)) && $automation->remindersummation_value == 1 && $automation->remindersummation_run != "login") {
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
if((in_array("all", $execute_parts) || in_array("directdebit", $execute_parts)) && SDD_ID) {
    require_once "class/directdebit.php";
    $directdebit = new directdebit();
    $directdebit->cronDirectDebit();
}
if((in_array("all", $execute_parts) || in_array("domainsync", $execute_parts)) && defined("DOMAIN_SYNC") && DOMAIN_SYNC == "yes" && (DOMAIN_SYNC_EXPDATE == "yes" || DOMAIN_SYNC_NAMESERVERS == "yes")) {
    require_once "class/domain.php";
    $domain = new domain();
    $list_domains = $domain->getDomainsToSync();
    $domain->syncDomainsByRegistrar($list_domains);
}
if(in_array("all", $execute_parts) || in_array("processclientareachanges", $execute_parts)) {
    require_once "class/clientareachange.php";
    $ClientareaChange = new ClientareaChange_Model();
    $ClientareaChange->cronTasks();
}
if(API_ACTIVE == "yes" && API_LOG_TYPE != "none") {
    require_once "class/apilogfile.php";
    $apilogfile = new apilogfile();
    $apilogfile->cleanUp();
}
Database_Model::getInstance()->update("HostFact_Debtors", ["Password" => "", "OneTimePasswordValidTill" => ["RAW" => "NULL"]])->where("OneTimePasswordValidTill", ["<" => ["RAW" => "NOW()"]])->execute();
Database_Model::getInstance()->delete("HostFact_MessageLog")->where("Date", ["<" => ["RAW" => "DATE_ADD(CURDATE(), INTERVAL -40 DAY)"]])->execute();
Database_Model::getInstance()->delete("HostFact_FailedLoginAttempts")->where("DateTime", ["<" => ["RAW" => "DATE_ADD(CURDATE(), INTERVAL -14 DAY)"]])->where("Type", "clientarea")->execute();
require_once "class/email.php";
email::cronCleanup();
if((in_array("all", $execute_parts) || in_array("backup", $execute_parts)) && $automation->makebackup_value == 1 && $automation->makebackup_run != "login") {
    require_once "class/backup.php";
    $backup = new backup();
    $try_to_make_backup = true;
    if(BACKUP_IS_RUNNING) {
        $backup_info = json_decode(htmlspecialchars_decode(BACKUP_IS_RUNNING), true);
        if($backup_info["started"] < date("Y-m-d H:i:s", strtotime("24 hours ago"))) {
            $try_to_make_backup = true;
        } else {
            $try_to_make_backup = false;
        }
        if(!isset($backup_info["mailed"])) {
            $backup->failedBackupCronMail($backup_info);
        }
        unset($backup_info);
    }
    if($try_to_make_backup === true) {
        $backup->make();
        flashMessage($backup);
        unset($backup);
    }
}
do_action("cronjob_task");
$handle = opendir("temp/");
if($handle !== false) {
    $temp_count = 0;
    while ($handle && false !== ($f = readdir($handle)) && $temp_count < 1000) {
        $temp_count++;
        if($f != "." && $f != ".." && $f != "index.php" && $f != ".htaccess" && $f != "stats_summary.php" && 900 < time() - filemtime("temp/" . $f)) {
            @unlink("temp/" . $f);
        }
    }
}
closedir($handle);
$settings->Variable = "CRONJOB_IS_RUNNING";
$settings->Value = "";
$settings->edit();

?>