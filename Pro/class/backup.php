<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
register_shutdown_function("backup_error_handler");
class backup
{
    public $Identifier;
    public $Date;
    public $FileName;
    public $Author;
    public $SoftwareVersion;
    public $Table;
    public $Error = [];
    public $Warning = [];
    public $Success = [];
    public $Variables = ["Identifier", "Date", "FileName", "Author"];
    public function __construct()
    {
        $this->Date = date("YmdHis");
        $this->Author = isset($_SESSION["UserPro"]) ? $_SESSION["UserPro"] : 0;
        $this->Error = [];
        $this->Warning = [];
        $this->Success = [];
        $this->SoftwareVersion = defined("WEFACT_VERSION") ? WEFACT_VERSION : SOFTWARE_VERSION;
    }
    public function show($id = NULL)
    {
        $this->Identifier = 0 < $id ? $id : $this->Identifier;
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for backup");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_Backups")->where("id", $this->Identifier)->execute();
        if(!$result || empty($result->id)) {
            $this->Error[] = __("invalid identifier for backup");
            return false;
        }
        foreach ($result as $key => $value) {
            $this->{$key} = htmlspecialchars($value);
        }
        return true;
    }
    public function make($datecheck = true)
    {
        global $company;
        if(BACKUP_DIR === "") {
            return true;
        }
        if($datecheck === true) {
            if(BACKUP_DAYS === "") {
                return true;
            }
            $backupDays = max(0, (int) BACKUP_DAYS);
            $dateBackupDaysAgo = date("Ymd", time() - $backupDays * 24 * 3600);
            if($dateBackupDaysAgo <= BACKUP_LASTDATE) {
                return true;
            }
        }
        Database_Model::getInstance()->update("HostFact_Settings", ["Value" => json_encode(["started" => date("Y-m-d H:i:s")])])->where("Variable", "BACKUP_IS_RUNNING")->execute();
        try {
            $mkBackup = new backupmodule();
            $mkBackup->generate();
            $this->FileName = $mkBackup->fname;
        } catch (Exception $e) {
            $this->Error[] = $e->getMessage();
            return false;
        }
        $result = Database_Model::getInstance()->insert("HostFact_Backups", ["Date" => $this->Date, "FileName" => $this->FileName, "Author" => $this->Author, "Version" => $this->SoftwareVersion])->execute();
        if(!$result) {
            return false;
        }
        $this->Identifier = $result;
        Database_Model::getInstance()->update("HostFact_Settings", ["Value" => date("Ymd")])->where("Variable", "BACKUP_LASTDATE")->execute();
        Database_Model::getInstance()->update("HostFact_Settings", ["Value" => ""])->where("Variable", "BACKUP_IS_RUNNING")->execute();
        if(BACKUP_EMAILADDRESS && check_email_address(BACKUP_EMAILADDRESS)) {
            require_once "class/email.php";
            $email = new email();
            $email->Recipient = BACKUP_EMAILADDRESS;
            $email->Sender = $company->CompanyName . " <" . $company->EmailAddress . ">";
            $email->Subject = sprintf(__("email subject backup notification"), $this->SoftwareVersion);
            $email->Message = file_get_contents("includes/language/" . LANGUAGE_CODE . "/mail.backup.notification.html");
            $email->Message = str_replace("[date]", date("d-m-Y"), $email->Message);
            $email->Message = str_replace("[time]", date("H:i"), $email->Message);
            $email->Message = str_replace("[version]", $this->SoftwareVersion, $email->Message);
            $email->Attachment = BACKUP_DIR . "/" . $this->FileName;
            $email->Sent_bcc = false;
            if(!$email->sent(false, false, true)) {
                $this->Error = array_merge($this->Error, $email->Error);
                $this->Warning = array_merge($this->Warning, $email->Warning);
                $this->Success = array_merge($this->Success, $email->Success);
                createMessageLog("error", "error during mailing backup");
            } else {
                $this->Success = array_merge($this->Success, $email->Success);
            }
        }
        createMessageLog("success", "backup created");
        $this->Success[] = __("backup created");
        return true;
    }
    public function failedBackupCronMail($backup_info)
    {
        $backup_info["mailed"] = date("Y-m-d H:i:s");
        Database_Model::getInstance()->update("HostFact_Settings", ["Value" => json_encode($backup_info)])->where("Variable", "BACKUP_IS_RUNNING")->execute();
        createMessageLog("error", "backup cron error messagelog");
        global $company;
        $recipient = "";
        if(CRONJOB_NOTIFY_MAILADDRESS && check_email_address(CRONJOB_NOTIFY_MAILADDRESS)) {
            $recipient = CRONJOB_NOTIFY_MAILADDRESS;
        } elseif($company->EmailAddress && check_email_address($company->EmailAddress)) {
            $recipient = $company->EmailAddress;
        }
        if($recipient) {
            require_once "class/email.php";
            $email = new email();
            $email->Recipient = $recipient;
            $email->Sender = $company->CompanyName . " <" . $company->EmailAddress . ">";
            $email->Subject = __("email subject error backup notification");
            $email->Message = file_get_contents("includes/language/" . LANGUAGE_CODE . "/mail.backup.error.html");
            $email->Message = str_replace("[date]", date("d-m-Y"), $email->Message);
            $email->Message = str_replace("[time]", date("H:i"), $email->Message);
            $email->Message = str_replace("[version]", $this->SoftwareVersion, $email->Message);
            $email->Attachment = BACKUP_DIR . "/" . $this->FileName;
            $email->Sent_bcc = false;
            $email->sent(false, false, true);
        }
    }
    public function delete()
    {
        $result = Database_Model::getInstance()->getOne("HostFact_Backups", ["FileName"])->where("id", $this->Identifier)->execute();
        if($result && file_exists(BACKUP_DIR . $result->FileName)) {
            @unlink(BACKUP_DIR . $result->FileName);
        }
        $result = Database_Model::getInstance()->delete("HostFact_Backups")->where("id", $this->Identifier)->execute();
        if($result) {
            return true;
        }
        return false;
    }
    public function deleteOldBackups()
    {
        $delete_after_days = intval(BACKUP_DELETE_AFTER_DAYS);
        if(0 < $delete_after_days) {
            $backups_to_delete = Database_Model::getInstance()->get("HostFact_Backups", ["id"])->where("Date", ["<" => ["RAW" => "DATE_ADD(CURDATE(), INTERVAL -:days DAY)"]])->bindValue("days", $delete_after_days)->execute();
            if(!empty($backups_to_delete)) {
                foreach ($backups_to_delete as $backup_to_delete) {
                    $this->Identifier = $backup_to_delete->id;
                    $this->delete();
                }
            }
        }
    }
    public function replace()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for backup");
            return false;
        }
        $var = Database_Model::getInstance()->getOne("HostFact_Backups", ["FileName"])->where("id", $this->Identifier)->execute();
        if(!file_exists(BACKUP_DIR . $var->FileName)) {
            $this->Error[] = sprintf(__("backup file not found"), BACKUP_DIR . $var->FileName);
            return false;
        }
        $mkBackup = new backupmodule();
        if($mkBackup->import(BACKUP_DIR . $var->FileName)) {
            $this->Success[] = __("backup replaced");
            return true;
        }
    }
    public function getAccountingYearsWithItems()
    {
        $years = [];
        $result = Database_Model::getInstance()->get("HostFact_Invoice", ["DATE_FORMAT(`Date`,\"%Y\") as `Year`", "COUNT(`id`) as `Counter`"])->where("Status", [">" => 0])->groupBy(["RAW" => "DATE_FORMAT(`Date`,\"%Y\")"])->execute();
        if($result) {
            foreach ($result as $_data) {
                $years[$_data->Year]["invoices"] = $_data->Counter;
            }
        }
        $result = Database_Model::getInstance()->get("HostFact_PriceQuote", ["DATE_FORMAT(`Date`,\"%Y\") as `Year`", "COUNT(`id`) as `Counter`"])->groupBy(["RAW" => "DATE_FORMAT(`Date`,\"%Y\")"])->execute();
        if($result) {
            foreach ($result as $_data) {
                $years[$_data->Year]["estimates"] = $_data->Counter;
            }
        }
        $result = Database_Model::getInstance()->get("HostFact_NewOrder", ["DATE_FORMAT(`Date`,\"%Y\") as `Year`", "COUNT(`id`) as `Counter`"])->groupBy(["RAW" => "DATE_FORMAT(`Date`,\"%Y\")"])->execute();
        if($result) {
            foreach ($result as $_data) {
                $years[$_data->Year]["orders"] = $_data->Counter;
            }
        }
        $result = Database_Model::getInstance()->get("HostFact_CreditInvoice", ["DATE_FORMAT(`Date`,\"%Y\") as `Year`", "COUNT(`id`) as `Counter`"])->groupBy(["RAW" => "DATE_FORMAT(`Date`,\"%Y\")"])->execute();
        if($result) {
            foreach ($result as $_data) {
                $years[$_data->Year]["purchaseinvoices"] = $_data->Counter;
            }
        }
        ksort($years);
        return $years;
    }
    public function deleteAccountingYear($year, $data_to_delete = [])
    {
        if(empty($data_to_delete)) {
            return false;
        }
        if(in_array("invoices", $data_to_delete)) {
            Database_Model::getInstance()->rawQuery("DELETE tbl.* FROM `HostFact_InvoiceElements` tbl WHERE `InvoiceCode` IN (SELECT `InvoiceCode` FROM (SELECT InvoiceCode FROM `HostFact_Invoice` WHERE `Status` > 0 AND DATE_FORMAT(`Date`,\"%Y\")=:year) temptable)", ["year" => $year]);
            Database_Model::getInstance()->rawQuery("DELETE tbl.* FROM `HostFact_Log` tbl WHERE `Type`='invoice' AND `Reference` IN (SELECT `id` FROM (SELECT id FROM `HostFact_Invoice` WHERE `Status` > 0 AND DATE_FORMAT(`Date`,\"%Y\")=:year) temptable)", ["year" => $year]);
            Database_Model::getInstance()->rawQuery("DELETE tbl.* FROM `HostFact_Transaction_Matches` tbl WHERE `ReferenceType`='invoice' AND `ReferenceID` IN (SELECT `id` FROM (SELECT id FROM `HostFact_Invoice` WHERE `Status` > 0 AND DATE_FORMAT(`Date`,\"%Y\")=:year) temptable)", ["year" => $year]);
            Database_Model::getInstance()->rawQuery("DELETE tbl.* FROM `HostFact_ExportPaymentTransactions` tbl WHERE `InvoiceID` IN (SELECT `id` FROM (SELECT id FROM `HostFact_Invoice` WHERE `Status` > 0 AND DATE_FORMAT(`Date`,\"%Y\")=:year) temptable)", ["year" => $year]);
            Database_Model::getInstance()->rawQuery("DELETE tbl.* FROM `HostFact_ExportHistory` tbl WHERE `Type` IN ('invoice','payment_invoice') AND `ReferenceID` IN (SELECT `id` FROM (SELECT id FROM `HostFact_Invoice` WHERE `Status` > 0 AND DATE_FORMAT(`Date`,\"%Y\")=:year) temptable)", ["year" => $year]);
            Database_Model::getInstance()->rawQuery("DELETE tbl.* FROM `HostFact_Debtor_Custom_Values` tbl WHERE `ReferenceType`='invoice' AND `ReferenceID` IN (SELECT `id` FROM (SELECT id FROM `HostFact_Invoice` WHERE `Status` > 0 AND DATE_FORMAT(`Date`,\"%Y\")=:year) temptable)", ["year" => $year]);
            $_documents_result = Database_Model::getInstance()->rawQuery("SELECT tbl.* FROM `HostFact_Documents` tbl WHERE `Type`='invoice' AND `Reference` IN (SELECT `id` FROM (SELECT id FROM `HostFact_Invoice` WHERE `Status` > 0 AND DATE_FORMAT(`Date`,\"%Y\")=:year) temptable)", ["year" => $year]);
            $_documents = $_documents_result->fetchAll(PDO::FETCH_OBJ);
            if($_documents) {
                foreach ($_documents as $var) {
                    require_once "class/attachment.php";
                    $attachment = new attachment();
                    $Param = ["Identifier" => $var->Reference, "Files" => [], "Type" => "invoice"];
                    $attachment->checkAttachments($Param);
                }
            }
            Database_Model::getInstance()->delete("HostFact_Invoice")->where("Status", [">" => "0"])->where("DATE_FORMAT(`Date`,\"%Y\")", $year)->execute();
        }
        if(in_array("estimates", $data_to_delete)) {
            Database_Model::getInstance()->rawQuery("DELETE tbl.* FROM `HostFact_PriceQuoteElements` tbl WHERE `PriceQuoteCode` IN (SELECT `PriceQuoteCode` FROM (SELECT PriceQuoteCode FROM `HostFact_PriceQuote` WHERE DATE_FORMAT(`Date`,\"%Y\")=:year) temptable)", ["year" => $year]);
            Database_Model::getInstance()->rawQuery("DELETE tbl.* FROM `HostFact_Log` tbl WHERE `Type`='pricequote' AND `Reference` IN (SELECT `id` FROM (SELECT id FROM `HostFact_PriceQuote` WHERE DATE_FORMAT(`Date`,\"%Y\")=:year) temptable)", ["year" => $year]);
            Database_Model::getInstance()->rawQuery("DELETE tbl.* FROM `HostFact_Debtor_Custom_Values` tbl WHERE `ReferenceType`='pricequote' AND `ReferenceID` IN (SELECT `id` FROM (SELECT id FROM `HostFact_PriceQuote` WHERE DATE_FORMAT(`Date`,\"%Y\")=:year) temptable)", ["year" => $year]);
            $_documents_result = Database_Model::getInstance()->rawQuery("SELECT tbl.* FROM `HostFact_Documents` tbl WHERE `Type` IN ('pricequote','pricequote_accepted') AND `Reference` IN (SELECT `id` FROM (SELECT id FROM `HostFact_PriceQuote` WHERE DATE_FORMAT(`Date`,\"%Y\")=:year) temptable)", ["year" => $year]);
            $_documents = $_documents_result->fetchAll(PDO::FETCH_OBJ);
            if($_documents) {
                foreach ($_documents as $var) {
                    require_once "class/attachment.php";
                    $attachment = new attachment();
                    $Param = ["Identifier" => $var->Reference, "Files" => [], "Type" => "pricequote"];
                    $attachment->checkAttachments($Param);
                }
            }
            Database_Model::getInstance()->delete("HostFact_PriceQuote")->where("DATE_FORMAT(`Date`,\"%Y\")", $year)->execute();
        }
        if(in_array("orders", $data_to_delete)) {
            Database_Model::getInstance()->rawQuery("DELETE tbl.* FROM `HostFact_NewOrderElements` tbl WHERE `OrderCode` IN (SELECT `OrderCode` FROM (SELECT OrderCode FROM `HostFact_NewOrder` WHERE DATE_FORMAT(`Date`,\"%Y\")=:year) temptable)", ["year" => $year]);
            Database_Model::getInstance()->delete("HostFact_NewOrder")->where("DATE_FORMAT(`Date`,\"%Y\")", $year)->execute();
            Database_Model::getInstance()->rawQuery("DELETE cust.* FROM `HostFact_NewCustomers` cust WHERE `id` NOT IN (SELECT `Customer` FROM (SELECT Customer FROM `HostFact_NewOrder` WHERE `Type`='new' AND `Customer` > 0) temptable)");
        }
        if(in_array("purchaseinvoices", $data_to_delete)) {
            Database_Model::getInstance()->rawQuery("DELETE tbl.* FROM `HostFact_CreditInvoiceElements` tbl WHERE `CreditInvoiceCode` IN (SELECT `CreditInvoiceCode` FROM (SELECT CreditInvoiceCode FROM `HostFact_CreditInvoice` WHERE DATE_FORMAT(`Date`,\"%Y\")=:year) temptable)", ["year" => $year]);
            $_documents_result = Database_Model::getInstance()->rawQuery("SELECT tbl.* FROM `HostFact_Documents` tbl WHERE `Type` IN ('creditinvoice','payment_purchase') AND `Reference` IN (SELECT `id` FROM (SELECT id FROM `HostFact_CreditInvoice` WHERE DATE_FORMAT(`Date`,\"%Y\")=:year) temptable)", ["year" => $year]);
            $_documents = $_documents_result->fetchAll(PDO::FETCH_OBJ);
            if($_documents) {
                foreach ($_documents as $var) {
                    require_once "class/attachment.php";
                    $attachment = new attachment();
                    $Param = ["Identifier" => $var->Reference, "Files" => [], "Type" => "creditinvoice"];
                    $attachment->checkAttachments($Param);
                }
            }
            Database_Model::getInstance()->rawQuery("DELETE tbl.* FROM `HostFact_Transaction_Matches` tbl WHERE `ReferenceType`='creditinvoice' AND `ReferenceID` IN (SELECT `id` FROM (SELECT id FROM `HostFact_CreditInvoice` WHERE DATE_FORMAT(`Date`,\"%Y\")=:year) temptable)", ["year" => $year]);
            Database_Model::getInstance()->rawQuery("DELETE tbl.* FROM `HostFact_ExportHistory` tbl WHERE `Type` = 'creditinvoice' AND `ReferenceID` IN (SELECT `id` FROM (SELECT id FROM `HostFact_CreditInvoice` WHERE DATE_FORMAT(`Date`,\"%Y\")=:year) temptable)", ["year" => $year]);
            Database_Model::getInstance()->delete("HostFact_CreditInvoice")->where("DATE_FORMAT(`Date`,\"%Y\")", $year)->execute();
        }
        if(Database_Model::getInstance()->getLastError() === false) {
            $this->Success[] = sprintf(__("delete accounting periods - succesfully deleted selected data for year x"), $year);
            return true;
        }
    }
    public function clean($aClean = [])
    {
        $this->make(false);
        if(in_array("clean_debtors", $aClean)) {
            Database_Model::getInstance()->truncate("HostFact_Debtors")->execute();
            Database_Model::getInstance()->delete("HostFact_Clientarea_Changes")->where("ReferenceType", "debtor")->execute();
            Database_Model::getInstance()->truncate("HostFact_SDD_Mandates")->execute();
            Database_Model::getInstance()->delete("HostFact_Debtor_Custom_Values")->where("ReferenceType", "debtor")->execute();
            Database_Model::getInstance()->delete("HostFact_GroupRelations")->where("Type", "debtor")->execute();
            Database_Model::getInstance()->delete("HostFact_Log")->where("Type", "debtor")->execute();
            Database_Model::getInstance()->delete("HostFact_Discount")->where("Debtor", [">" => "0"])->execute();
            Database_Model::getInstance()->delete("HostFact_ExportHistory")->where("Type", "debtor")->execute();
        }
        if(in_array("clean_invoice", $aClean) || in_array("clean_debtors", $aClean)) {
            Database_Model::getInstance()->truncate("HostFact_Invoice")->execute();
            Database_Model::getInstance()->truncate("HostFact_InvoiceElements")->execute();
            Database_Model::getInstance()->truncate("HostFact_SDD_Batches")->execute();
            Database_Model::getInstance()->truncate("HostFact_SDD_BatchElements")->execute();
            Database_Model::getInstance()->delete("HostFact_Log")->where("Type", "directdebit")->execute();
            Database_Model::getInstance()->delete("HostFact_Log")->where("Type", "invoice")->execute();
            Database_Model::getInstance()->delete("HostFact_Transaction_Matches")->where("ReferenceType", "invoice")->execute();
            Database_Model::getInstance()->delete("HostFact_Debtor_Custom_Values")->where("ReferenceType", "invoice")->execute();
            $_documents = Database_Model::getInstance()->getOne("HostFact_Documents", ["Reference"])->where("Type", "invoice")->execute();
            if($_documents) {
                foreach ($_documents as $var) {
                    require_once "class/attachment.php";
                    $attachment = new attachment();
                    $Param = ["Identifier" => $var->Reference, "Files" => [], "Type" => "invoice"];
                    $attachment->checkAttachments($Param);
                }
            }
            Database_Model::getInstance()->delete("HostFact_ExportHistory")->where("Type", ["IN" => ["invoice", "sddbatch", "payment_invoice"]])->execute();
            Database_Model::getInstance()->truncate("HostFact_ExportPaymentTransactions")->execute();
        }
        if(in_array("clean_pricequote", $aClean) || in_array("clean_debtors", $aClean)) {
            Database_Model::getInstance()->truncate("HostFact_PriceQuote")->execute();
            Database_Model::getInstance()->truncate("HostFact_PriceQuoteElements")->execute();
            Database_Model::getInstance()->delete("HostFact_Log")->where("Type", "pricequote")->execute();
            Database_Model::getInstance()->delete("HostFact_Debtor_Custom_Values")->where("ReferenceType", "pricequote")->execute();
            $_documents = Database_Model::getInstance()->get("HostFact_Documents", ["Reference", "Type"])->where("Type", ["LIKE" => "pricequote%"])->execute();
            if($_documents) {
                foreach ($_documents as $var) {
                    require_once "class/attachment.php";
                    $attachment = new attachment();
                    $Param = ["Identifier" => $var->Reference, "Files" => [], "Type" => $var->Type];
                    $attachment->checkAttachments($Param);
                }
            }
        }
        if(in_array("clean_orders", $aClean) || in_array("clean_debtors", $aClean)) {
            Database_Model::getInstance()->truncate("HostFact_NewOrder")->execute();
            Database_Model::getInstance()->truncate("HostFact_NewOrderElements")->execute();
            Database_Model::getInstance()->truncate("HostFact_NewCustomers")->execute();
            Database_Model::getInstance()->delete("HostFact_Debtor_Custom_Values")->where("ReferenceType", "newcustomer")->execute();
        }
        if(in_array("clean_services", $aClean) || in_array("clean_debtors", $aClean)) {
            Database_Model::getInstance()->truncate("HostFact_Domains")->execute();
            Database_Model::getInstance()->delete("HostFact_Clientarea_Changes")->where("ReferenceType", ["IN" => ["domain"]])->execute();
            Database_Model::getInstance()->truncate("HostFact_DomainsPending")->execute();
            Database_Model::getInstance()->truncate("HostFact_Domain_Extra_Values")->execute();
            Database_Model::getInstance()->truncate("HostFact_Hosting")->execute();
            Database_Model::getInstance()->truncate("HostFact_PeriodicElements")->execute();
            Database_Model::getInstance()->truncate("HostFact_Services")->execute();
            Database_Model::getInstance()->delete("HostFact_Log")->where("Type", "domain")->execute();
            Database_Model::getInstance()->delete("HostFact_Log")->where("Type", "hosting")->execute();
            Database_Model::getInstance()->truncate("HostFact_Terminations")->execute();
            Database_Model::getInstance()->delete("HostFact_Actions")->where("ReferenceType", "termination")->execute();
            do_action("clean_services");
            if(in_array("clean_handles", $aClean) || in_array("clean_debtors", $aClean)) {
                Database_Model::getInstance()->delete("HostFact_Handles")->where("Debtor", [">" => "0"])->execute();
                Database_Model::getInstance()->delete("HostFact_Debtor_Custom_Values")->where("ReferenceType", "handle")->execute();
            }
        }
        if(in_array("clean_creditors", $aClean)) {
            Database_Model::getInstance()->truncate("HostFact_Creditors")->execute();
            Database_Model::getInstance()->delete("HostFact_GroupRelations")->where("Type", "creditor")->execute();
            Database_Model::getInstance()->delete("HostFact_ExportHistory")->where("Type", "creditor")->execute();
        }
        if(in_array("clean_creditinvoice", $aClean) || in_array("clean_creditors", $aClean)) {
            Database_Model::getInstance()->truncate("HostFact_CreditInvoice")->execute();
            Database_Model::getInstance()->truncate("HostFact_CreditInvoiceElements")->execute();
            $_documents = Database_Model::getInstance()->getOne("HostFact_Documents", ["Reference"])->where("Type", "creditinvoice")->execute();
            if($_documents) {
                foreach ($_documents as $var) {
                    require_once "class/attachment.php";
                    $attachment = new attachment();
                    $Param = ["Identifier" => $var->Reference, "Files" => [], "Type" => "creditinvoice"];
                    $attachment->checkAttachments($Param);
                }
            }
            Database_Model::getInstance()->delete("HostFact_Transaction_Matches")->where("ReferenceType", "creditinvoice")->execute();
            Database_Model::getInstance()->delete("HostFact_ExportHistory")->where("Type", ["IN" => ["creditinvoice", "payment_purchase"]])->execute();
        }
        if(in_array("clean_products", $aClean)) {
            Database_Model::getInstance()->truncate("HostFact_Products")->execute();
            Database_Model::getInstance()->truncate("HostFact_Product_Custom_Prices")->execute();
            Database_Model::getInstance()->delete("HostFact_GroupRelations")->where("Type", "product")->execute();
            Database_Model::getInstance()->delete("HostFact_Discount")->orWhere([["Product1", [">" => "0"]], ["Product2", [">" => "0"]], ["Product3", [">" => "0"]]])->execute();
            Database_Model::getInstance()->update("HostFact_PeriodicElements", ["ProductCode" => ""])->noWhere()->execute();
            Database_Model::getInstance()->update("HostFact_Packages", ["Product" => ""])->noWhere()->execute();
            Database_Model::getInstance()->update("HostFact_TopLevelDomain", ["OwnerChangeCost" => ""])->noWhere()->execute();
            Database_Model::getInstance()->update("HostFact_Hosting", ["Product" => ""])->noWhere()->execute();
            Database_Model::getInstance()->update("HostFact_Domains", ["Product" => ""])->noWhere()->execute();
            Database_Model::getInstance()->delete("HostFact_ExportHistory")->where("Type", "product")->execute();
            do_action("clean_products");
        }
        if(in_array("clean_ticketsystem", $aClean)) {
            Database_Model::getInstance()->truncate("HostFact_TicketMessage")->execute();
            Database_Model::getInstance()->truncate("HostFact_Tickets")->execute();
        }
        if(in_array("clean_apilog", $aClean)) {
            Database_Model::getInstance()->truncate("HostFact_API_Calls")->execute();
        }
        Database_Model::getInstance()->truncate("HostFact_Emails")->execute();
        delete_stats_summary();
        $this->Success[] = __("testdata cleaned");
        return true;
    }
    public function all($fields, $sort = false, $order = false, $limit = "-1", $searchat = false, $searchfor = false, $group = false, $show_results = MAX_RESULTS_LIST)
    {
        $limit = !is_numeric($show_results) ? -1 : $limit;
        if(!is_array($fields) || empty($fields)) {
            $this->Error[] = __("no fields given for retrieving data");
            return false;
        }
        if(!is_numeric($show_results)) {
            $this->Error[] = __("invalid number for displaying results");
            return false;
        }
        $EmployeeArray = ["Name"];
        $select = ["HostFact_Backups.id"];
        foreach ($fields as $column) {
            if(in_array($column, $EmployeeArray)) {
                $select[] = "HostFact_Employee.`" . $column . "`";
            } else {
                $select[] = "HostFact_Backups.`" . $column . "`";
            }
        }
        Database_Model::getInstance()->get("HostFact_Backups", $select);
        $EmployeeFields = 0 < count(array_intersect($EmployeeArray, $fields)) ? true : false;
        $search_at = [];
        $EmployeeSearch = false;
        if($searchat && $searchfor) {
            $search_at = explode("|", $searchat);
            $EmployeeSearch = 0 < count(array_intersect($EmployeeArray, $search_at)) ? true : false;
        }
        if($EmployeeFields || $EmployeeSearch) {
            Database_Model::getInstance()->join("HostFact_Employee", "HostFact_Employee.`id`=HostFact_Backups.`Author`");
        }
        if(!empty($search_at)) {
            $or_clausule = [];
            foreach ($search_at as $searchColumn) {
                if(in_array($searchColumn, ["Author"])) {
                    $or_clausule[] = ["HostFact_Backups." . $searchColumn, $searchfor];
                } elseif(in_array($searchColumn, $EmployeeArray)) {
                    $or_clausule[] = ["HostFact_Employee." . $searchColumn, ["LIKE" => "%" . $searchfor . "%"]];
                } else {
                    $or_clausule[] = ["HostFact_Backups." . $searchColumn, ["LIKE" => "%" . $searchfor . "%"]];
                }
            }
            Database_Model::getInstance()->orWhere($or_clausule);
        }
        if(in_array($sort, $EmployeeArray)) {
            Database_Model::getInstance()->orderBy("HostFact_Employee.`" . $sort . "`", $order);
        } elseif($sort) {
            Database_Model::getInstance()->orderBy("HostFact_Backups.`" . $sort . "`", $order);
        }
        if(0 <= $limit) {
            Database_Model::getInstance()->limit((max(1, $limit) - 1) * $show_results, $show_results);
        }
        $list = [];
        if($backup_list = Database_Model::getInstance()->execute()) {
            $list["CountRows"] = Database_Model::getInstance()->rowCount("HostFact_Backups", "HostFact_Backups.id");
            foreach ($backup_list as $result) {
                if(file_exists(BACKUP_DIR . "/" . $result->FileName)) {
                    $list[$result->id] = ["id" => $result->id];
                    foreach ($fields as $column) {
                        $list[$result->id][$column] = htmlspecialchars($result->{$column});
                    }
                    $list[$result->id]["FileSize"] = filesize(BACKUP_DIR . "/" . $result->FileName);
                } else {
                    Database_Model::getInstance()->delete("HostFact_Backups")->where("id", $result->id)->execute();
                }
            }
        }
        return $list;
    }
}
class backupmodule
{
    public $dbName = "";
    public $SoftwareVersion;
    public $reserved_words = ["ADD", "ANALYZE", "ASC", "BDB", "BETWEEN", "BLOB", "CALL", "CHANGE", "CHECK", "COLUMNS", "CONSTRAINT", "CROSS", "CURRENT_TIMESTAMP", "DATABASES", "DAY_MINUTE", "DECIMAL", "DELAYED", "DESCRIBE", "DISTINCTROW", "DROP", "ENCLOSED", "EXIT", "FETCH", "FOR", "FOUND", "FULLTEXT", "HAVING", "HOUR_MINUTE", "IGNORE", "INFILE", "INOUT", "INT", "INTO", "ITERATE", "KEYS", "LEAVE", "LIMIT", "LOCALTIME", "LONG", "LOOP", "MATCH", "MEDIUMTEXT", "MINUTE_SECOND", "NOT", "NUMERIC", "OPTION", "ORDER", "OUTFILE", "PRIVILEGES", "READ", "REGEXP", "REPLACE", "RETURN", "RLIKE", "SENSITIVE", "SHOW", "SONAME", "SQL", "SQLWARNING", "SQL_SMALL_RESULT", "SQL_TSI_HOUR", "SQL_TSI_QUARTER", "SQL_TSI_YEAR", "STRAIGHT_JOIN", "TABLES", "TIMESTAMPADD", "TINYINT", "TRAILING", "UNION", "UNSIGNED", "USE", "UTC_DATE", "VALUES", "VARCHARACTER", "WHERE", "WRITE", "ZEROFILL", "ALL", "AND", "ASENSITIVE", "BEFORE", "BIGINT", "BOTH", "CASCADE", "CHAR", "COLLATE", "CONDITION", "CONTINUE", "CURRENT_DATE", "CURSOR", "DAY_HOUR", "DAY_SECOND", "DECLARE", "DELETE", "DETERMINISTIC", "DIV", "ELSE", "ESCAPED", "EXPLAIN", "FIELDS", "FORCE", "FRAC_SECOND", "GRANT", "HIGH_PRIORITY", "HOUR_SECOND", "IN", "INNER", "INSENSITIVE", "INTEGER", "IO_THREAD", "JOIN", "KILL", "LEFT", "LINES", "LOCALTIMESTAMP", "LONGBLOB", "LOW_PRIORITY", "MEDIUMBLOB", "MIDDLEINT", "MOD", "NO_WRITE_TO_BINLOG", "ON", "OPTIONALLY", "OUT", "PRECISION", "PROCEDURE", "REAL", "RENAME", "REQUIRE", "REVOKE", "SECOND_MICROSECOND", "SEPARATOR", "SMALLINT", "SPATIAL", "SQLEXCEPTION", "SQL_BIG_RESULT", "SQL_TSI_DAY", "SQL_TSI_MINUTE", "SQL_TSI_SECOND", "SSL", "STRIPED", "TERMINATED", "TIMESTAMPDIFF", "TINYTEXT", "TRUE", "UNIQUE", "UPDATE", "USER_RESOURCES", "UTC_TIME", "VARBINARY", "VARYING", "WHILE", "XOR", "ALTER", "AS", "AUTO_INCREMENT", "BERKELEYDB", "BINARY", "BY", "CASE", "CHARACTER", "COLUMN", "CONNECTION", "CREATE", "CURRENT_TIME", "DATABASE", "DAY_MICROSECOND", "DEC", "DEFAULT", "DESC", "DISTINCT", "DOUBLE", "ELSEIF", "EXISTS", "FALSE", "FLOAT", "FOREIGN", "FROM", "GROUP", "HOUR_MICROSECOND", "IF", "INDEX", "INNODB", "INSERT", "INTERVAL", "IS", "KEY", "LEADING", "LIKE", "LOAD", "LOCK", "LONGTEXT", "MASTER_SERVER_ID", "MEDIUMINT", "MINUTE_MICROSECOND", "NATURAL", "NULL", "OPTIMIZE", "OR", "OUTER", "PRIMARY", "PURGE", "REFERENCES", "REPEAT", "RESTRICT", "RIGHT", "SELECT", "SET", "SOME", "SPECIFIC", "SQLSTATE", "SQL_CALC_FOUND_ROWS", "SQL_TSI_FRAC_SECOND", "SQL_TSI_MONTH", "SQL_TSI_WEEK", "STARTING", "TABLE", "THEN", "TINYBLOB", "TO", "UNDO", "UNLOCK", "USAGE", "USING", "UTC_TIMESTAMP", "VARCHAR", "WHEN", "WITH", "YEAR_MONTH"];
    public $backupdir = BACKUP_DIR;
    public $compression;
    public function __construct()
    {
        $this->compression = "zlib";
        $this->dbName = Database_Model::getInstance()->getDatabaseName();
        $this->SoftwareVersion = defined("WEFACT_VERSION") ? WEFACT_VERSION : SOFTWARE_VERSION;
    }
    public function query($query)
    {
        $this->dbQryResult = Database_Model::getInstance()->rawQuery($query);
        return $this->dbQryResult;
    }
    public function fetch_row($result = "")
    {
        $this->dbResultLine = $result->fetch(PDO::FETCH_NUM);
        return $this->dbResultLine;
    }
    public function get_data($result = "")
    {
        if($result != "") {
            return $this->fetch_row($result);
        }
        return $this->fetch_row($this->dbQryResult);
    }
    public function fetch_array($result = "")
    {
        $this->dbResultLine = $result->fetch(PDO::FETCH_BOTH);
        return $this->dbResultLine;
    }
    public function list_tables($dbname)
    {
        $this->dbResultLine = Database_Model::getInstance()->rawQuery("SHOW TABLES");
        return $this->dbResultLine;
    }
    public function get_db_tables()
    {
        $result = @$this->list_tables($this->dbName);
        if(!$result) {
            echo "Error loading tables for backup\n";
            exit;
        }
        while ($row = $this->fetch_row($result)) {
            $Tables[] = $row[0];
        }
        return $Tables;
    }
    public function escape_string($string = "")
    {
        return Database_Model::getInstance()->quote($string);
    }
    public function valid_table_name($tbl_name)
    {
        if(in_array(strtoupper($tbl_name), $this->reserved_words)) {
            return false;
        }
        return true;
    }
    public function generate()
    {
        if(!is_dir($this->backupdir)) {
            @mkdir($this->backupdir, @octdec($this->file_mod));
        }
        if(!is_writable($this->backupdir)) {
            throw new Exception(sprintf(__("backup folder not writable"), BACKUP_DIR));
        }
        $result = @$this->list_tables($this->dbName);
        $Dx_Create_Tables = "";
        while ($table = @$this->fetch_row($result)) {
            if(substr(strtolower($table[0]), 0, 7) == "wefact_" || substr(strtolower($table[0]), 0, 9) == "hostfact_") {
                $res = $this->query("SHOW CREATE TABLE `" . $this->dbName . "`." . $table[0]);
                do {
                    $resu[] = $this->get_data();
                } while (!$resu);
            }
        }
        foreach ($resu as $key => $val) {
            if(!is_array($val)) {
            } else {
                $tbl_name_status = $this->valid_table_name($val[0]);
                if(trim($val[0]) !== "" && $tbl_name_status) {
                    $Dx_Create_Tables .= "\n# XQUERY\nDROP TABLE IF EXISTS " . $val[0] . ";\n# /XQUERY\n# XQUERY\n" . $val[1] . " ;\n# /XQUERY\n\r\n";
                    $query = "Insert into `" . $val[0] . "` (";
                    $this->query("LOCK TABLES " . $val[0] . " WRITE");
                    $qresult = $this->query("Select * from " . $val[0]);
                    $insert_values = [];
                    while ($line = $this->fetch_array($qresult)) {
                        unset($fields);
                        unset($values);
                        $fields = "";
                        $values = "";
                        $j = 0;
                        foreach ($line as $col_name => $col_value) {
                            if(!is_int($col_name)) {
                                $fields .= "`" . $col_name . "`,";
                                $values .= is_null($col_value) ? "NULL," : "" . $this->escape_string($col_value) . ",";
                            }
                        }
                        $fields = substr($fields, 0, strlen($fields) - 1);
                        $values = substr($values, 0, strlen($values) - 1);
                        $insert_values[] = "(" . $values . ")";
                    }
                    $insert_values_chuncked = array_chunk($insert_values, 100);
                    foreach ($insert_values_chuncked as $_inserts) {
                        $myquery = $query . $fields . ") values " . implode(",", $_inserts) . ";";
                        $Dx_Create_Tables .= "\r\n# XQUERY\r\n" . $myquery . "\r\n" . "# /XQUERY" . "\r\n" . "\r\n";
                    }
                    $this->query("UNLOCK TABLES;");
                } elseif(!$tbl_name_status) {
                }
            }
        }
        $hash = substr(sha1(date("Y-m-d H-i-s") . rand(0, 100)), 0, 6);
        switch ($this->compression) {
            case "zlib":
                $fname = $this->dbName . "-" . $this->SoftwareVersion . "-" . date("Y-m-d H-i-s") . "_" . $hash . ".gz";
                touch($this->backupdir . "/" . $fname);
                $fp = gzopen($this->backupdir . "/" . $fname, "w");
                gzwrite($fp, $Dx_Create_Tables);
                gzclose($fp);
                break;
            default:
                $fname = $this->dbName . "-" . $this->SoftwareVersion . "-" . date("Y-m-d H-i-s") . "_" . $hash . ".sql";
                touch($this->backupdir . "/" . $fname);
                $fp = fopen($this->backupdir . "/" . $fname, "w");
                fwrite($fp, $Dx_Create_Tables);
                fclose($fp);
                $this->fname = $fname;
                return NULL;
        }
    }
    public function import($bfile = "")
    {
        @set_time_limit(0);
        if(is_file($bfile)) {
            $FileOpen = fopen($bfile, "rb");
            fseek($FileOpen, -4, SEEK_END);
            $buf = fread($FileOpen, 4);
            $buf = unpack("V", $buf);
            $GZFileSize = end($buf);
            fclose($FileOpen);
            $bz = gzopen($bfile, "r");
            $contents = gzread($bz, $GZFileSize);
            gzclose($bz);
            $contents = str_replace("<xquery>", "# XQUERY", str_replace("</xquery>", "# /XQUERY", $contents));
            $contents = str_replace("# /XQUERY", "", $contents);
            $requetes = explode("# XQUERY", $contents);
            unset($contents);
            $this->query("SET foreign_key_checks = 0");
            $this->query("SET autocommit=0");
            foreach ($requetes as $key => $val) {
                if(strpos(strtolower($val), "wefact_backups") === false && strpos(strtolower($val), "hostfact_backups") === false && trim($val)) {
                    $this->query(trim($val));
                }
            }
            $this->query("COMMIT");
            $this->query("SET foreign_key_checks = 1");
            $this->query("SET autocommit=1");
            return true;
        } else {
            return false;
        }
    }
}
function backup_error_handler()
{
    $error = error_get_last();
    if($error && $error["type"] == 1 && strpos(strtolower($error["message"]), "allowed memory size") !== false) {
        if(CRONJOB_NOTIFY_MAILADDRESS && defined("SCRIPT_IS_CRONJOB") && SCRIPT_IS_CRONJOB === true) {
            @mail(CRONJOB_NOTIFY_MAILADDRESS, "[HostFact] cronjob error", $error["message"]);
        } else {
            fatal_error("Backup error", $error["message"]);
            exit;
        }
    }
}

?>