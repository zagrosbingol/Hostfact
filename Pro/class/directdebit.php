<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class directdebit
{
    public $Success;
    public $Warning;
    public $Error;
    private $__official_holidays;
    private $__counters;
    public function __construct()
    {
        $this->Success = $this->Warning = $this->Error = [];
        $this->__official_holidays = ["01-01", "12-25", "12-26"];
        $this->__counters = ["RCUR" => 0, "FRST" => 0, "OOFF" => 0];
    }
    public function removeDirectDebitFromInvoiceByElementID($element_id, $removeDirectDebitFromDebtor = false, $notifyDebtorAboutNewDirectDebitDate = false, $notifyDebtorWithMailID = 0)
    {
        $element = Database_Model::getInstance()->getOne(["HostFact_SDD_BatchElements", "HostFact_Invoice", "HostFact_Debtors"], ["HostFact_SDD_BatchElements.Debtor", "HostFact_SDD_BatchElements.InvoiceID", "HostFact_SDD_BatchElements.BatchID", "HostFact_SDD_BatchElements.Status", "HostFact_Invoice.InvoiceCode", "HostFact_Debtors.DebtorCode"])->where("HostFact_SDD_BatchElements.id", $element_id)->where("HostFact_SDD_BatchElements.`InvoiceID` = HostFact_Invoice.`id`")->where("HostFact_Debtors.`id` = HostFact_SDD_BatchElements.`Debtor`")->execute();
        if(!$element) {
            return false;
        }
        $invoice_ids = [];
        if($removeDirectDebitFromDebtor === true) {
            Database_Model::getInstance()->update("HostFact_SDD_BatchElements", ["Status" => "cancelled"])->where("Debtor", $element->Debtor)->where("Status", "draft")->execute();
            Database_Model::getInstance()->update("HostFact_Debtors", ["InvoiceAuthorisation" => "no"])->where("id", $element->Debtor)->execute();
            $result = Database_Model::getInstance()->get("HostFact_Invoice", ["id"])->where("Debtor", $element->Debtor)->where("Authorisation", "yes")->where("Status", ["<" => "4"])->execute();
            if(is_array($result)) {
                foreach ($result as $tmp_result) {
                    $invoice_ids[] = $tmp_result->id;
                }
            }
            $invoice_ids[] = $element->InvoiceID;
            $invoice_ids = array_unique($invoice_ids);
            Database_Model::getInstance()->update("HostFact_SDD_Mandates", ["Status" => "suspended"])->where("Debtor", $element->Debtor)->where("Status", "active")->execute();
            $this->Success[] = sprintf(__("debtor authorization is removed"), $element->DebtorCode);
            createLog("directdebit", str_replace("SDD", "", $element->BatchID), "sdd direct debit removed from debtor x", $element->DebtorCode);
        } else {
            if($element->Status == "draft") {
                Database_Model::getInstance()->update("HostFact_SDD_BatchElements", ["Status" => "cancelled"])->where("id", $element_id)->execute();
            }
            $invoice_ids[] = $element->InvoiceID;
            $this->Success[] = sprintf(__("invoice authorization is removed"), $element->InvoiceCode);
            createLog("directdebit", str_replace("SDD", "", $element->BatchID), "sdd direct debit removed from invoice x", $element->InvoiceCode);
        }
        if(!empty($invoice_ids)) {
            foreach ($invoice_ids as $invoice_id) {
                Database_Model::getInstance()->update("HostFact_Invoice", ["PaymentMethod" => ["RAW" => "IF(`PaymentMethod` = 'auth','',`PaymentMethod`)"], "TransactionID" => ["RAW" => "IF(SUBSTRING(`TransactionID`,1,3) = 'SDD','',`TransactionID`)"], "Authorisation" => "no", "SDDBatchID" => ""])->where("id", $invoice_id)->execute();
                createLog("invoice", $invoice_id, "sdd invoice removed from batch", $element->BatchID);
            }
        }
        if($notifyDebtorAboutNewDirectDebitDate === true && 0 < $notifyDebtorWithMailID) {
            require_once "class/invoice.php";
            $invoice = new invoice();
            $invoice->Identifier = $element->InvoiceID;
            $invoice->show();
            require_once "class/debtor.php";
            $debtor = new debtor();
            $debtor->show($invoice->Debtor);
            require_once "class/template.php";
            $emailtemplate = new emailtemplate();
            $emailtemplate->Identifier = $notifyDebtorWithMailID;
            if($emailtemplate->show()) {
                require_once "class/email.php";
                $email = new email();
                foreach ($emailtemplate->Variables as $v) {
                    if(is_string($emailtemplate->{$v})) {
                        $email->{$v} = htmlspecialchars_decode($emailtemplate->{$v});
                    } else {
                        $email->{$v} = $emailtemplate->{$v};
                    }
                }
                $objects = ["debtor" => $debtor, "invoice" => $invoice];
                $email->Recipient = $debtor->EmailAddress;
                $email->Debtor = $invoice->Debtor;
                $email->add($objects);
                if($email->sent(false, false, false, $objects)) {
                    $this->Success[] = sprintf(__("debtor succesfully notified about failed direct debit"), $debtor->EmailAddress);
                }
            }
        }
        $this->updateBatchStatus($element->BatchID);
        return true;
    }
    public function removeDirectDebitFromInvoiceByBatchAndInvoiceID($batch_id, $invoice_id)
    {
        $elements = Database_Model::getInstance()->get(["HostFact_SDD_BatchElements", "HostFact_SDD_Batches"], ["HostFact_SDD_BatchElements.id", "HostFact_SDD_BatchElements.BatchID", "HostFact_SDD_Batches.Status"])->where("HostFact_SDD_BatchElements.InvoiceID", $invoice_id)->where("HostFact_SDD_BatchElements.BatchID", $batch_id)->where("HostFact_SDD_Batches.`BatchID` = HostFact_SDD_BatchElements.`BatchID`")->where("HostFact_SDD_BatchElements.Status", ["!=" => "cancelled"])->execute();
        if(!$elements) {
            return false;
        }
        if(is_array($elements)) {
            foreach ($elements as $element) {
                if(in_array($element->Status, ["draft", "downloadable"])) {
                    $this->removeDirectDebitFromInvoiceByElementID($element->id);
                } else {
                    $this->failedDirectDebitByElementID($element->id);
                    Database_Model::getInstance()->update("HostFact_Invoice", ["PaymentMethod" => "", "TransactionID" => "", "Authorisation" => "no", "SDDBatchID" => ""])->where("id", $invoice_id)->where("Status", ["<" => 4])->execute();
                    createLog("invoice", $invoice_id, "sdd invoice removed from batch", $element->BatchID);
                }
            }
        }
        $this->updateBatchStatus($element->BatchID);
    }
    public function failedDirectDebitByInvoiceID($invoice_id, $reason = "", $failedAction = "", $notifyDebtorAboutNewDirectDebitDate = false, $notifyDebtorWithMailID = 0)
    {
        $element = Database_Model::getInstance()->getOne(["HostFact_SDD_BatchElements", "HostFact_Invoice"], ["HostFact_SDD_BatchElements.id"])->where("HostFact_Invoice.id", $invoice_id)->where("HostFact_Invoice.`id` = HostFact_SDD_BatchElements.`InvoiceID`")->where("HostFact_SDD_BatchElements.`BatchID` = HostFact_Invoice.`SDDBatchID`")->execute();
        if(!$element) {
            return false;
        }
        return $this->failedDirectDebitByElementID($element->id, $reason, $failedAction, $notifyDebtorAboutNewDirectDebitDate, $notifyDebtorWithMailID);
    }
    public function failedDirectDebitByElementID($element_id, $reason = "", $failedAction = "", $notifyDebtorAboutNewDirectDebitDate = false, $notifyDebtorWithMailID = 0)
    {
        $element = Database_Model::getInstance()->getOne("HostFact_SDD_BatchElements", ["InvoiceID", "BatchID"])->where("id", $element_id)->execute();
        if(!$element) {
            return false;
        }
        $batch_info = $this->getBatchInfo($element->BatchID);
        Database_Model::getInstance()->update("HostFact_SDD_BatchElements", ["Status" => isset($batch_info->Status) && $batch_info->Status == "draft" ? "cancelled" : "failed", "Reason" => $reason])->where("id", $element_id)->execute();
        require_once "class/invoice.php";
        $invoice = new invoice();
        $invoice->Identifier = $element->InvoiceID;
        $invoice->show();
        createLog("invoice", $invoice->Identifier, "sdd direct debit payment failed", $reason);
        createLog("directdebit", str_replace("SDD", "", $element->BatchID), "sdd direct debit failed for invoice x", [$invoice->InvoiceCode, $reason]);
        if($invoice->Status == 4) {
            $invoice->preventDirectDebitAction = true;
            $invoice->markasunpaid();
            $this->Error = array_merge($this->Error, $invoice->Error);
            $this->Warning = array_merge($this->Warning, $invoice->Warning);
            $this->Success = array_merge($this->Success, $invoice->Success);
        }
        if($failedAction) {
            switch ($failedAction) {
                case "move_next":
                    $this->moveInvoiceToNextBatchByElementID($element_id, $notifyDebtorAboutNewDirectDebitDate, $notifyDebtorWithMailID);
                    break;
                case "remove_invoice":
                    $this->removeDirectDebitFromInvoiceByElementID($element_id, false, $notifyDebtorAboutNewDirectDebitDate, $notifyDebtorWithMailID);
                    break;
                case "remove_debtor":
                    $this->removeDirectDebitFromInvoiceByElementID($element_id, true, $notifyDebtorAboutNewDirectDebitDate, $notifyDebtorWithMailID);
                    break;
            }
        }
        Database_Model::getInstance()->update("HostFact_ExportHistory", ["Status" => "success", "Message" => ""])->where("Type", "payment_invoice")->where("ReferenceID", $invoice->Identifier)->where("PackageReference", "sdd_reversal")->execute();
        $invoice_info = ["id" => $invoice->Identifier, "Debtor" => $invoice->Debtor, "InvoiceCode" => $invoice->InvoiceCode, "Reason" => $reason, "Action" => $failedAction];
        do_action("invoice_direct_debit_has_reversal", $invoice_info);
    }
    public function moveInvoiceToNextBatchByElementID($element_id, $notifyDebtorAboutNewDirectDebitDate = false, $notifyDebtorWithMailID = 0)
    {
        $element = Database_Model::getInstance()->getOne(["HostFact_SDD_BatchElements", "HostFact_SDD_Batches"], ["HostFact_SDD_BatchElements.InvoiceID", "HostFact_SDD_BatchElements.BatchID", "HostFact_SDD_BatchElements.Status", "HostFact_SDD_Batches.Date"])->where("HostFact_SDD_BatchElements.id", $element_id)->where("HostFact_SDD_Batches.`BatchID` = HostFact_SDD_BatchElements.`BatchID`")->execute();
        if(!$element) {
            return false;
        }
        if(!$this->putInvoiceInBatch($element->InvoiceID, $element->Date, true)) {
            return false;
        }
        if($element->Status == "draft" || $element->Status == "downloadable") {
            Database_Model::getInstance()->update("HostFact_SDD_BatchElements", ["Status" => "cancelled"])->where("id", $element_id)->where("Status", "draft")->execute();
        }
        Database_Model::getInstance()->update("HostFact_Invoice", ["TransactionID" => "", "SDDBatchID" => $this->BatchID])->where("id", $element->InvoiceID)->where("Status", ["<" => 4])->where("Authorisation", "yes")->execute();
        require_once "class/invoice.php";
        $invoice = new invoice();
        $invoice->Identifier = $element->InvoiceID;
        $invoice->show();
        if($notifyDebtorAboutNewDirectDebitDate === true && 0 < $notifyDebtorWithMailID) {
            require_once "class/debtor.php";
            $debtor = new debtor();
            $debtor->show($invoice->Debtor);
            require_once "class/template.php";
            $emailtemplate = new emailtemplate();
            $emailtemplate->Identifier = $notifyDebtorWithMailID;
            if($emailtemplate->show()) {
                require_once "class/email.php";
                $email = new email();
                foreach ($emailtemplate->Variables as $v) {
                    if(is_string($emailtemplate->{$v})) {
                        $email->{$v} = htmlspecialchars_decode($emailtemplate->{$v});
                    } else {
                        $email->{$v} = $emailtemplate->{$v};
                    }
                }
                $objects = ["debtor" => $debtor, "invoice" => $invoice];
                $email->Recipient = $debtor->EmailAddress;
                $email->Debtor = $invoice->Debtor;
                $email->add($objects);
                if($email->sent(false, false, false, $objects)) {
                    $this->Success[] = sprintf(__("debtor succesfully notified about moved direct debit date"), $debtor->EmailAddress);
                }
            }
        }
        $this->updateBatchStatus($element->BatchID);
        createLog("directdebit", str_replace("SDD", "", $element->BatchID), "sdd direct debit moved invoice x to batch y", [$invoice->InvoiceCode, $this->BatchID]);
        $this->Success[] = sprintf(__("invoice moved to new batch"), $invoice->InvoiceCode, $this->BatchID);
        return true;
    }
    public function markInvoiceAsPaidByElementID($element_id)
    {
        $element = Database_Model::getInstance()->getOne(["HostFact_SDD_BatchElements", "HostFact_SDD_Batches"], ["HostFact_SDD_BatchElements.InvoiceID", "HostFact_SDD_BatchElements.BatchID", "HostFact_SDD_BatchElements.MandateType", "HostFact_SDD_BatchElements.Debtor", "HostFact_SDD_Batches.Date"])->where("HostFact_SDD_BatchElements.id", $element_id)->where("HostFact_SDD_Batches.`BatchID` = HostFact_SDD_BatchElements.`BatchID`")->execute();
        if(!$element) {
            return false;
        }
        Database_Model::getInstance()->update("HostFact_SDD_BatchElements", ["Status" => "success"])->where("id", $element_id)->execute();
        Database_Model::getInstance()->update("HostFact_Invoice", ["TransactionID" => $element->BatchID, "PaymentMethod" => "auth", "Paid" => "1"])->where("id", $element->InvoiceID)->execute();
        if($element->MandateType == "FRST") {
            Database_Model::getInstance()->update("HostFact_SDD_Mandates", ["MandateType" => "RCUR"])->where("Debtor", $element->Debtor)->where("Status", "active")->execute();
        } elseif($element->MandateType == "OOFF") {
            Database_Model::getInstance()->update("HostFact_SDD_Mandates", ["Status" => "suspended"])->where("Debtor", $element->Debtor)->where("Status", "active")->execute();
        }
        require_once "class/invoice.php";
        $invoice = new invoice();
        $invoice->Identifier = $element->InvoiceID;
        $invoice->show();
        if($invoice->Status <= 3 && substr($invoice->InvoiceCode, 0, 8) != "[concept") {
            $invoice->preventDirectDebitAction = true;
            $invoice->markaspaid();
            $invoice->checkAuto();
            $this->Error = array_merge($this->Error, $invoice->Error);
            $this->Warning = array_merge($this->Warning, $invoice->Warning);
            $this->Success = array_merge($this->Success, $invoice->Success);
        }
        createLog("directdebit", str_replace("SDD", "", $element->BatchID), "sdd direct debit invoice x marked as paid", $invoice->InvoiceCode);
        $this->updateBatchStatus($element->BatchID);
    }
    public function markInvoiceAsPaidByInvoiceID($invoice_id)
    {
        $elements = Database_Model::getInstance()->get(["HostFact_SDD_BatchElements", "HostFact_SDD_Batches"], ["HostFact_SDD_BatchElements.id", "HostFact_SDD_BatchElements.Status", "HostFact_SDD_BatchElements.BatchID", "HostFact_SDD_BatchElements.MandateType", "HostFact_SDD_BatchElements.Debtor", "HostFact_SDD_Batches.Status as `BatchStatus`", "HostFact_Invoice.InvoiceCode"])->join("HostFact_Invoice", "HostFact_Invoice.`id` = HostFact_SDD_BatchElements.`InvoiceID`")->where("HostFact_SDD_BatchElements.InvoiceID", $invoice_id)->where("HostFact_SDD_Batches.`BatchID` = HostFact_SDD_BatchElements.`BatchID`")->where("HostFact_SDD_BatchElements.Status", ["!=" => "cancelled"])->execute();
        if(!$elements) {
            return false;
        }
        if(is_array($elements)) {
            foreach ($elements as $element) {
                if(in_array($element->BatchStatus, ["draft", "downloadable"])) {
                    $this->removeDirectDebitFromInvoiceByElementID($element->id);
                } elseif($element->Status == "draft") {
                    Database_Model::getInstance()->update("HostFact_SDD_BatchElements", ["Status" => "success"])->where("id", $element->id)->execute();
                    Database_Model::getInstance()->update("HostFact_Invoice", ["TransactionID" => $element->BatchID, "PaymentMethod" => "auth", "Paid" => "1"])->where("id", $invoice_id)->execute();
                    if($element->MandateType == "FRST") {
                        Database_Model::getInstance()->update("HostFact_SDD_Mandates", ["MandateType" => "RCUR"])->where("Debtor", $element->Debtor)->where("Status", "active")->execute();
                    } elseif($element->MandateType == "OOFF") {
                        Database_Model::getInstance()->update("HostFact_SDD_Mandates", ["Status" => "suspended"])->where("Debtor", $element->Debtor)->where("Status", "active")->execute();
                    }
                    createLog("directdebit", str_replace("SDD", "", $element->BatchID), "sdd direct debit invoice x marked as paid", $element->InvoiceCode);
                }
            }
        }
        $this->updateBatchStatus($element->BatchID);
    }
    public function putInvoiceInBatch($invoice_id, $min_required_batch_date = false, $move_to_next_batch = false, $first_send = false)
    {
        $query = Database_Model::getInstance()->getOne("HostFact_Invoice", ["HostFact_Invoice.id", "HostFact_Invoice.Date", "HostFact_Invoice.Debtor", "HostFact_Invoice.InvoiceCode", "HostFact_SDD_Mandates.MandateType"])->join("HostFact_SDD_Mandates", "HostFact_Invoice.`Debtor` = HostFact_SDD_Mandates.`Debtor` AND HostFact_SDD_Mandates.`Status` = 'active'")->where("HostFact_Invoice.id", $invoice_id);
        if($first_send !== true) {
            $query->where("HostFact_Invoice.Status", ["IN" => [2, 3]]);
        }
        $invoice_info = $query->execute();
        if(!isset($invoice_info->id) || $invoice_info->id <= 0) {
            return false;
        }
        if($invoice_info->Date < date("Y-m-d")) {
            $invoice_info->Date = date("Y-m-d");
        }
        $min_batch_notify_date = date("Y-m-d", strtotime("+ " . SDD_NOTICE . " day", strtotime($invoice_info->Date)));
        $processing_time = SDD_PROCESSING_RCUR;
        $min_batch_proc_date = $this->getDateAfterWorkingDays($invoice_info->Date, $processing_time);
        $batch_date = $min_batch_proc_date < $min_batch_notify_date ? $min_batch_notify_date : $min_batch_proc_date;
        if($move_to_next_batch === true) {
            $batch_date = $min_batch_proc_date;
            $future_sql = ["draft", "downloadable"];
        } else {
            $future_sql = ["draft"];
        }
        $batch_days = explode(",", SDD_DAYS);
        $future_batch = Database_Model::getInstance()->getOne("HostFact_SDD_Batches")->where("Date", [">" => $batch_date < $min_required_batch_date ? $min_required_batch_date : $batch_date])->where("Status", ["IN" => $future_sql])->orderBy("Date", "ASC")->execute();
        while (!in_array(substr($batch_date, 8, 2), $batch_days) || $min_required_batch_date !== false && $batch_date <= $min_required_batch_date) {
            $batch_date = date("Y-m-d", strtotime("+1 day", strtotime($batch_date)));
        }
        $batch_date = $this->nextWorkingDay($batch_date);
        if($future_batch && $future_batch->Date <= $batch_date) {
            $batch_candidate = $future_batch;
            $batch_date = $future_batch->Date;
        } else {
            $batch_candidate = Database_Model::getInstance()->getOne("HostFact_SDD_Batches")->where("Date", $batch_date)->execute();
            if($batch_candidate !== false && $batch_candidate->Status != "draft") {
                return $this->putInvoiceInBatch($invoice_id, $batch_date, false, $first_send);
            }
        }
        if($batch_candidate === false) {
            $batch_id = "SDD" . str_replace("-", "", $batch_date);
            $result = Database_Model::getInstance()->insert("HostFact_SDD_Batches", ["BatchID" => $batch_id, "Date" => $batch_date, "Status" => "draft", "Count" => 0, "Amount" => 0])->execute();
            if($result === false) {
                return false;
            }
            $this->BatchID = $batch_id;
        } else {
            $this->BatchID = $batch_candidate->BatchID;
        }
        $result = Database_Model::getInstance()->insert("HostFact_SDD_BatchElements", ["BatchID" => $this->BatchID, "Debtor" => $invoice_info->Debtor, "InvoiceID" => $invoice_info->id, "Status" => "draft"])->execute();
        if(!$result) {
            return false;
        }
        createLog("invoice", $invoice_id, "sdd invoice added to batch", $this->BatchID);
        createLog("directdebit", str_replace("SDD", "", $this->BatchID), "sdd invoice x added to batch", $invoice_info->InvoiceCode);
        $this->updateBatchStatus($this->BatchID);
        return true;
    }
    public function listBatches($status, $sort = "BatchID", $order = "DESC")
    {
        $sortable_columns = ["BatchID"];
        Database_Model::getInstance()->get("HostFact_SDD_Batches")->where("Status", ["!=" => "cancelled"]);
        if($sort && in_array($sort, $sortable_columns)) {
            Database_Model::getInstance()->orderBy($sort, $order);
        }
        $batches = Database_Model::getInstance()->asArray()->execute();
        $batch_array = ["current" => [], "processing" => [], "archive" => []];
        foreach ($batches as $tmp_batch) {
            if($status != "all" && !in_array($tmp_batch["Status"], explode("|", $status))) {
            } else {
                switch ($tmp_batch["Status"]) {
                    case "draft":
                    case "downloadable":
                        $return = $this->checkBatchForErrors($tmp_batch["BatchID"], true);
                        if(!empty($return["error"])) {
                            $tmp_batch["ErrCount"] = count($return["error"]);
                        }
                        $batch_array["current"][] = $tmp_batch;
                        break;
                    case "downloaded":
                        $batch_array["processing"][] = $tmp_batch;
                        break;
                    default:
                        $batch_array["archive"][] = $tmp_batch;
                }
            }
        }
        return $batch_array;
    }
    public function listArchivedBatches($sort = "BatchID", $order = "DESC", $limit = "-1", $show_results = MAX_RESULTS_LIST)
    {
        $sortable_columns = ["BatchID"];
        $limit = !is_numeric($show_results) ? -1 : $limit;
        Database_Model::getInstance()->get("HostFact_SDD_Batches")->where("Status", ["NOT IN" => ["draft", "downloadable", "downloaded"]]);
        if($sort && in_array($sort, $sortable_columns)) {
            Database_Model::getInstance()->orderBy($sort, $order);
        }
        if(0 <= $limit) {
            Database_Model::getInstance()->limit(round((max(1, $limit) - 1) * $show_results), $show_results);
        }
        $batches = Database_Model::getInstance()->asArray()->execute();
        return $batches;
    }
    public function getBatchesCount($status)
    {
        $status_array = explode("|", $status);
        $batches = Database_Model::getInstance()->getOne("HostFact_SDD_Batches", "COUNT(`BatchID`) as `Count`")->where("Status", ["IN" => $status_array])->execute();
        return $batches && isset($batches->Count) ? $batches->Count : 0;
    }
    public function getBatchInfo($batch_id)
    {
        $batch_info = Database_Model::getInstance()->getOne("HostFact_SDD_Batches")->where("BatchID", $batch_id)->execute();
        if($batch_info->Status == "draft" || $batch_info->Status == "downloadable" || $batch_info->Status == "downloaded") {
            global $company;
            $batch_info->SDD_ID = SDD_ID;
            $batch_info->SDD_IBAN = SDD_IBAN || SDD_BIC ? SDD_IBAN : $company->AccountNumber;
            $batch_info->SDD_BIC = SDD_IBAN || SDD_BIC ? SDD_BIC : $company->AccountBIC;
        }
        return $batch_info;
    }
    public function getBatchTransactions($batch_id, $join_information = true)
    {
        $transaction_list = [];
        $this->__counters = ["RCUR" => 0, "FRST" => 0, "OOFF" => 0];
        $select = ["HostFact_SDD_BatchElements.*", "HostFact_Invoice.InvoiceCode", "HostFact_Invoice.AmountIncl", "HostFact_Invoice.AmountPaid", "HostFact_Invoice.Status as `InvoiceStatus`", "HostFact_Invoice.AuthTrials", "HostFact_Invoice.Date as `InvoiceDate`", "HostFact_Debtors.CompanyName", "HostFact_Debtors.SurName", "HostFact_Debtors.Initials", "HostFact_Debtors.Address", "HostFact_Debtors.ZipCode", "HostFact_Debtors.City", "HostFact_Debtors.Country", "HostFact_Debtors.DebtorCode", "HostFact_Debtors.AccountName"];
        if($join_information === true) {
            $select[] = "HostFact_SDD_Mandates.MandateID";
            $select[] = "HostFact_SDD_Mandates.MandateType";
            $select[] = "HostFact_SDD_Mandates.MandateDate";
            $select[] = "HostFact_Debtors.AccountNumber as `IBAN`";
            $select[] = "HostFact_Debtors.AccountBIC as `BIC`";
        }
        $transactions = Database_Model::getInstance()->get(["HostFact_SDD_BatchElements", "HostFact_Invoice", "HostFact_Debtors"], $select)->join("HostFact_SDD_Mandates", "HostFact_SDD_Mandates.`Debtor` = HostFact_SDD_BatchElements.`Debtor` AND HostFact_SDD_Mandates.`Status`='active'")->where("HostFact_SDD_BatchElements.BatchID", $batch_id)->where("HostFact_SDD_BatchElements.`InvoiceID` = HostFact_Invoice.`id`")->where("HostFact_Debtors.`id` = HostFact_SDD_BatchElements.`Debtor`")->where("HostFact_SDD_BatchElements.Status", ["!=" => "cancelled"])->orderBy("IF(SUBSTRING(HostFact_Invoice.`InvoiceCode`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(HostFact_Invoice.`InvoiceCode`,1,1))", "DESC")->orderBy("LENGTH(HostFact_Invoice.`InvoiceCode`)", "DESC")->orderBy("HostFact_Invoice.`InvoiceCode`", "DESC")->groupBy("HostFact_SDD_BatchElements.`InvoiceID`")->asArray()->execute();
        $counter = 0;
        $amount = 0;
        foreach ($transactions as $tmp_transaction) {
            if($join_information) {
                $invoiceAmount = $tmp_transaction["InvoiceStatus"] == 3 ? round($tmp_transaction["AmountIncl"] - $tmp_transaction["AmountPaid"], 2) : $tmp_transaction["AmountIncl"];
                $invoiceAmount = max(0, $invoiceAmount);
                $tmp_transaction["Amount"] = $invoiceAmount;
                $counter++;
                $amount += $invoiceAmount;
            }
            $tmp_transaction["MandateType"] = "RCUR";
            $this->__counters[$tmp_transaction["MandateType"]] += 1;
            $transaction_list[] = $tmp_transaction;
        }
        if($join_information === true) {
            Database_Model::getInstance()->update("HostFact_SDD_Batches", ["Count" => $counter, "Amount" => $amount])->where("BatchID", $batch_id)->execute();
        }
        return $transaction_list;
    }
    public function getTypeCounter($type)
    {
        return isset($this->__counters[$type]) && 0 < $this->__counters[$type] ? $this->__counters[$type] : 0;
    }
    public function checkBatchForErrors($batch_id, $from_list = false)
    {
        $errors = [];
        $error_for_debtor = [];
        $warning_for_debtor = [];
        $batch_info = $this->getBatchInfo($batch_id);
        if(SDD_LIMIT_BATCH && SDD_LIMIT_BATCH < $batch_info->Amount) {
            $this->Warning[] = sprintf(__("sdd warning batch exceeds limit"), money(SDD_LIMIT_BATCH));
        }
        if(!$this->checkIBAN($batch_info->SDD_IBAN)) {
            $errors["error"][] = sprintf(__("sdd error invalid iban x for company"), $batch_info->SDD_IBAN);
        }
        $transactions = $this->getBatchTransactions($batch_id);
        foreach ($transactions as $tmp_transaction) {
            if(($batch_info->Status == "draft" || $batch_info->Status == "downloadable") && 4 <= $tmp_transaction["InvoiceStatus"]) {
                if($from_list === true) {
                    $errors["error"][] = sprintf(__("sdd error invoice already paid"), $tmp_transaction["InvoiceCode"]);
                } else {
                    $this->Warning[] = sprintf(__("sdd error invoice already paid"), $tmp_transaction["InvoiceCode"]);
                    $this->removeDirectDebitFromInvoiceByElementID($tmp_transaction["id"]);
                    $this->Success = [];
                }
            } else {
                if(!in_array($tmp_transaction["Debtor"], $error_for_debtor)) {
                    if(!$tmp_transaction["MandateID"]) {
                        $errors["error"][] = sprintf(__("sdd error no mandate for debtor x"), $tmp_transaction["Debtor"], $tmp_transaction["DebtorCode"]);
                        $error_for_debtor[] = $tmp_transaction["Debtor"];
                    } elseif(!$tmp_transaction["IBAN"]) {
                        $errors["error"][] = sprintf(__("sdd error no iban for debtor x"), $tmp_transaction["Debtor"], $tmp_transaction["DebtorCode"]);
                        $error_for_debtor[] = $tmp_transaction["Debtor"];
                    } elseif($tmp_transaction["IBAN"] && !$this->checkIBAN($tmp_transaction["IBAN"])) {
                        $errors["error"][] = sprintf(__("sdd error invalid iban x for debtor x"), $tmp_transaction["IBAN"], $tmp_transaction["Debtor"], $tmp_transaction["DebtorCode"]);
                        $error_for_debtor[] = $tmp_transaction["Debtor"];
                    }
                }
                if(SDD_LIMIT_TRANSACTION && SDD_LIMIT_TRANSACTION < $tmp_transaction["Amount"]) {
                    $this->Warning[] = sprintf(__("sdd warning invoice exceeds limit"), $tmp_transaction["InvoiceCode"], money(SDD_LIMIT_TRANSACTION));
                }
            }
        }
        return $errors;
    }
    public function downloadBatch($batch_id)
    {
        $return = $this->checkBatchForErrors($batch_id);
        if(!empty($return["error"])) {
            $this->Warning = [];
            $this->Error = array_merge($this->Error, $return["error"]);
            return false;
        }
        $batch_info = $this->getBatchInfo($batch_id);
        $rebuild = $batch_info->Status == "draft" || $batch_info->Status == "downloadable" || $batch_info->Status == "downloaded" ? true : false;
        $transactions = $this->getBatchTransactions($batch_id, $rebuild);
        $sdd = new SepaDirectDebit($batch_info);
        foreach ($transactions as $tmp_transaction) {
            $sdd->addInvoice($tmp_transaction);
        }
        if(!$sdd->generateXML()) {
            $this->Error[] = __("sdd error cannot download batch");
            $this->Error = array_merge($this->Error, $sdd->Error);
            return false;
        }
        if($rebuild) {
            Database_Model::getInstance()->update("HostFact_SDD_Batches", ["Status" => "downloaded", "SDD_ID" => $batch_info->SDD_ID, "SDD_IBAN" => strtoupper(trim(str_replace(" ", "", $batch_info->SDD_IBAN))), "SDD_BIC" => strtoupper(trim(str_replace(" ", "", $batch_info->SDD_BIC))), "DownloadDate" => ["RAW" => "CURDATE()"]])->where("BatchID", $batch_id)->execute();
            foreach ($transactions as $tmp_transaction) {
                Database_Model::getInstance()->update("HostFact_SDD_BatchElements", ["Amount" => $tmp_transaction["Amount"], "MandateID" => $tmp_transaction["MandateID"], "MandateDate" => $tmp_transaction["MandateDate"], "MandateType" => $tmp_transaction["MandateType"], "IBAN" => strtoupper(trim(str_replace(" ", "", $tmp_transaction["IBAN"]))), "BIC" => strtoupper(trim(str_replace(" ", "", $tmp_transaction["BIC"])))])->where("id", $tmp_transaction["id"])->execute();
                if($batch_info->Status == "draft" || $batch_info->Status == "downloadable") {
                    Database_Model::getInstance()->update("HostFact_Invoice", ["AuthTrials" => ["RAW" => "`AuthTrials`+1"], "TransactionID" => $batch_id])->where("id", $tmp_transaction["InvoiceID"])->execute();
                }
            }
        }
        createLog("directdebit", str_replace("SDD", "", $batch_id), "sdd batch downloaded");
        delete_stats_summary();
    }
    public function changeDate($batch_id, $new_batch_date)
    {
        if(!$new_batch_date || new DateTime($new_batch_date) < new DateTime()) {
            $this->Error[] = sprintf(__("sdd batch date change - invalid date"), rewrite_date_db2site($new_batch_date));
            return false;
        }
        $batch_candidate = Database_Model::getInstance()->getOne("HostFact_SDD_Batches")->where("Date", $new_batch_date)->execute();
        if($batch_candidate !== false) {
            $this->Error[] = __("sdd batch cannot change date, already exists");
            return false;
        }
        $batch_info = $this->getBatchInfo($batch_id);
        $new_batch_id = "SDD" . str_replace("-", "", $new_batch_date);
        Database_Model::getInstance()->update("HostFact_SDD_BatchElements", ["BatchID" => $new_batch_id])->where("BatchID", $batch_info->BatchID)->execute();
        Database_Model::getInstance()->update("HostFact_SDD_Batches", ["BatchID" => $new_batch_id, "Date" => $new_batch_date])->where("BatchID", $batch_info->BatchID)->where("Date", $batch_info->Date)->execute();
        Database_Model::getInstance()->update("HostFact_Log", ["Reference" => str_replace("-", "", $new_batch_date)])->where("Reference", str_replace("-", "", $batch_info->Date))->where("Type", "directdebit")->execute();
        Database_Model::getInstance()->update("HostFact_Log", ["Values" => ["RAW" => "REPLACE(`Values`, :old_batch_id, :new_batch_id)"]])->where("Values", ["LIKE" => "%" . $batch_info->BatchID . "%"])->where("Type", "invoice")->bindValue("new_batch_id", $new_batch_id)->bindValue("old_batch_id", $batch_info->BatchID)->execute();
        Database_Model::getInstance()->update("HostFact_Invoice", ["SDDBatchID" => $new_batch_id])->where("SDDBatchID", $batch_info->BatchID)->execute();
        createLog("directdebit", str_replace("SDD", "", $new_batch_id), "sdd batch date changed", [rewrite_date_db2site($batch_info->Date), rewrite_date_db2site($new_batch_date)]);
        delete_stats_summary();
        $this->Success[] = sprintf(__("sdd batch date is changed"), rewrite_date_db2site($batch_info->Date), rewrite_date_db2site($new_batch_date));
        $this->setBatchToDraft($new_batch_id, false);
        return true;
    }
    public function setBatchToDraft($batch_id, $createLog = true)
    {
        $batch_info = $this->getBatchInfo($batch_id);
        $status = date("Y-m-d") <= date("Y-m-d", strtotime("-" . SDD_NOTICE . " days", strtotime($batch_info->Date))) ? "draft" : "downloadable";
        Database_Model::getInstance()->update("HostFact_SDD_Batches", ["Status" => $status, "SDD_ID" => "", "SDD_IBAN" => "", "SDD_BIC" => ""])->where("BatchID", $batch_id)->execute();
        Database_Model::getInstance()->update("HostFact_SDD_BatchElements", ["Amount" => "0", "MandateID" => "", "MandateDate" => "", "MandateType" => "", "IBAN" => "", "BIC" => ""])->where("BatchID", $batch_id)->execute();
        if($status == "draft" && $status != $batch_info->Status) {
            $transactions = $this->getBatchTransactions($batch_id, false);
            foreach ($transactions as $tmp_transaction) {
                Database_Model::getInstance()->update("HostFact_Invoice", ["AuthTrials" => ["RAW" => "`AuthTrials`-1"], "TransactionID" => ""])->where("id", $tmp_transaction["InvoiceID"])->execute();
            }
        }
        if($createLog === true) {
            createLog("directdebit", str_replace("SDD", "", $batch_id), "sdd batch set to status draft");
            $this->Success[] = sprintf(__("sdd batch set to status draft"), $batch_id);
        }
        if($status == "downloadable" && $createLog === true) {
            createLog("directdebit", str_replace("SDD", "", $batch_id), "sdd batch ready for download");
            $this->Success[] = sprintf(__("sdd batch ready for download"), $batch_id);
        }
        $this->updateBatchStatus($batch_id);
        delete_stats_summary();
        return true;
    }
    public function cronDirectDebit()
    {
        $result = Database_Model::getInstance()->get("HostFact_SDD_Batches")->where("Status", "draft")->orWhere([["DATE_ADD(`Date`, INTERVAL -:notice_term DAY) < CURDATE()"], ["DownloadDate", ["<=" => ["RAW" => "CURDATE()"]]]])->bindValue("notice_term", SDD_NOTICE)->execute();
        if($result) {
            foreach ($result as $batch) {
                if(0 < $batch->Count) {
                    Database_Model::getInstance()->update("HostFact_SDD_Batches", ["Status" => "downloadable"])->where("BatchID", $batch->BatchID)->execute();
                    createLog("directdebit", str_replace("SDD", "", $batch->BatchID), "sdd batch ready for download");
                    $this->Success[] = sprintf(__("sdd batch ready for download"), $batch->BatchID);
                    if(SDD_MAIL_NOTIFY == "yes") {
                        global $company;
                        require_once "class/email.php";
                        $email = new email();
                        $email->Recipient = $company->EmailAddress;
                        $email->Sender = $company->CompanyName . " <" . $company->EmailAddress . ">";
                        $email->Subject = sprintf(__("email subject direct debit batch notification"), $batch->BatchID);
                        $email->Message = file_get_contents("includes/language/" . LANGUAGE_CODE . "/mail.directdebit.batch.html");
                        $email->Message = str_replace("[batchid]", $batch->BatchID, $email->Message);
                        $email->Message = str_replace("[count]", $batch->Count, $email->Message);
                        $email->Message = str_replace("[amount]", money($batch->Amount), $email->Message);
                        $email->Message = str_replace("[downloaddate]", rewrite_date_db2site($batch->DownloadDate), $email->Message);
                        $email->Message = str_replace("[directdebitdate]", rewrite_date_db2site($batch->Date), $email->Message);
                        $email->Message = str_replace("[hostingurl]", BACKOFFICE_URL . "directdebit.php?page=show&id=" . $batch->BatchID, $email->Message);
                        if(!$email->sent(false, false, true)) {
                            createLog("directdebit", str_replace("SDD", "", $batch->BatchID), "sdd batch ready for download notification error", implode(", ", $email->Error));
                            $this->Error = array_merge($this->Error, $email->Error);
                            $this->Warning = array_merge($this->Warning, $email->Warning);
                            $this->Success = array_merge($this->Success, $email->Success);
                        } else {
                            createLog("directdebit", str_replace("SDD", "", $batch->BatchID), "sdd batch ready for download notification success", $email->Recipient);
                        }
                    }
                } else {
                    Database_Model::getInstance()->update("HostFact_SDD_Batches", ["Status" => "cancelled"])->where("BatchID", $batch->BatchID)->execute();
                }
            }
        }
        $invoices_not_in_batch = Database_Model::getInstance()->get("HostFact_Invoice", ["id"])->where("SDDBatchID", "")->where("Authorisation", "yes")->where("PaymentMethod", ["IN" => ["", "wire", "auth"]])->where("TransactionID", "")->where("Status", ["IN" => [2, 3]])->execute();
        if($invoices_not_in_batch) {
            foreach ($invoices_not_in_batch as $tmp_inv) {
                if($this->putInvoiceInBatch($tmp_inv->id)) {
                    Database_Model::getInstance()->update("HostFact_Invoice", ["PaymentMethod" => "auth", "SDDBatchID" => $this->BatchID])->where("id", $tmp_inv->id)->execute();
                }
            }
        }
        $debtors_without_mandates = Database_Model::getInstance()->get("HostFact_Debtors", ["id"])->where("InvoiceAuthorisation", "yes")->where("Status", ["<" => 9])->where("id", ["NOT IN" => ["RAW" => "SELECT `Debtor` FROM `HostFact_SDD_Mandates` WHERE `Status`='active'"]])->execute();
        if($debtors_without_mandates) {
            foreach ($debtors_without_mandates as $tmp_deb) {
                require_once "class/debtor.php";
                $debtor = new debtor();
                $debtor->Identifier = $tmp_deb->id;
                $debtor->show();
                $debtor->MandateID = $debtor->getDirectDebitMandateID();
                $debtor->MandateDate = rewrite_date_db2site(date("Y-m-d"));
                $debtor->MandateType = "FRST";
                $debtor->createDirectDebitMandate();
            }
        }
        $result = Database_Model::getInstance()->get("HostFact_SDD_Batches")->where("Status", ["!=" => "draft"])->where("Count", "0")->execute();
        if($result) {
            foreach ($result as $batch) {
                Database_Model::getInstance()->update("HostFact_SDD_Batches", ["Status" => "cancelled"])->where("BatchID", $batch->BatchID)->execute();
            }
        }
    }
    public function updateBatchStatus($batch_id)
    {
        $batch_info = Database_Model::getInstance()->getOne("HostFact_SDD_Batches", ["Date", "Status"])->where("BatchID", $batch_id)->execute();
        if(!$batch_info) {
            return false;
        }
        if($batch_info->Status == "draft" || $batch_info->Status == "downloadable") {
            $result = Database_Model::getInstance()->get(["HostFact_SDD_BatchElements", "HostFact_Invoice"], ["HostFact_SDD_BatchElements.id", "HostFact_Invoice.AmountIncl", "HostFact_Invoice.AmountPaid", "HostFact_Invoice.Status as `InvoiceStatus`", "HostFact_SDD_Mandates.MandateType"])->join("HostFact_SDD_Mandates", "HostFact_SDD_Mandates.`Debtor` = HostFact_Invoice.`Debtor` AND HostFact_SDD_Mandates.`Status`='active'")->where("HostFact_SDD_BatchElements.BatchID", $batch_id)->where("HostFact_SDD_BatchElements.Status", "draft")->where("HostFact_SDD_BatchElements.`InvoiceID` = HostFact_Invoice.`id`")->execute();
            $counter = 0;
            $amount = 0;
            $processing_time = 0;
            foreach ($result as $tmp) {
                if(($tmp->InvoiceStatus == 3 ? $tmp->AmountIncl - $tmp->AmountPaid : $tmp->AmountIncl) <= 0) {
                    Database_Model::getInstance()->update("HostFact_SDD_BatchElements", ["Status" => "cancelled"])->where("id", $tmp->id)->execute();
                } else {
                    $counter++;
                    $amount += $tmp->InvoiceStatus == 3 ? $tmp->AmountIncl - $tmp->AmountPaid : $tmp->AmountIncl;
                    $processing_time = max($processing_time, SDD_PROCESSING_RCUR);
                }
            }
            $download_before = $this->getDateAfterWorkingDays($batch_info->Date, $processing_time, "-");
            Database_Model::getInstance()->update("HostFact_SDD_Batches", ["Count" => $counter, "Amount" => $amount, "ProcessingTime" => $processing_time, "DownloadDate" => $download_before])->where("BatchID", $batch_id)->execute();
        } elseif($batch_info->Status == "downloaded") {
            $result = Database_Model::getInstance()->get("HostFact_SDD_BatchElements", ["Status", "COUNT(`id`) as `Counter`"])->where("BatchID", $batch_id)->groupBy("Status")->execute();
            if(empty($result)) {
                Database_Model::getInstance()->update("HostFact_SDD_Batches", ["Status" => "cancelled"])->where("BatchID", $batch_id)->execute();
            } else {
                $draft_counter = $success_counter = $failed_counter = 0;
                foreach ($result as $tmp_result) {
                    switch ($tmp_result->Status) {
                        case "draft":
                            $draft_counter = (int) $tmp_result->Counter;
                            break;
                        case "success":
                            $success_counter = (int) $tmp_result->Counter;
                            break;
                        case "failed":
                            $failed_counter = (int) $tmp_result->Counter;
                            break;
                    }
                }
                if($draft_counter === 0) {
                    $new_status = 0 < $success_counter ? "processed" : "rejected";
                    Database_Model::getInstance()->update("HostFact_SDD_Batches", ["Status" => $new_status])->where("BatchID", $batch_id)->execute();
                }
            }
        }
    }
    private function getDateAfterWorkingDays($date_start, $workingdays, $sign = "+")
    {
        $timestamp = strtotime($date_start);
        while (0 < $workingdays) {
            $timestamp = strtotime($sign . "1 day", $timestamp);
            if(date("N", $timestamp) < 6 && !in_array(date("m-d", $timestamp), $this->__official_holidays)) {
                $workingdays--;
            }
        }
        return date("Y-m-d", $timestamp);
    }
    public function nextWorkingDay($date_start)
    {
        $timestamp = strtotime($date_start);
        while (6 <= date("N", $timestamp) || in_array(date("m-d", $timestamp), $this->__official_holidays)) {
            $timestamp = strtotime("+1 day", $timestamp);
        }
        return date("Y-m-d", $timestamp);
    }
    public function checkIBAN($iban)
    {
        $banknr = strtoupper(str_replace([" ", "."], "", trim($iban)));
        $country = substr($banknr, 0, 2);
        $info = $this->_iban_landcode($country);
        if(!is_array($info)) {
            return false;
        }
        if(strlen($banknr) != $info[0]) {
            return false;
        }
        $parts = explode(",", "2n," . $info[1]);
        $i = 2;
        foreach ($parts as $format) {
            $len = substr($format, 0, strlen($format) - 1);
            $string = substr($banknr, $i, $len);
            $i += $len;
            substr($format, -1);
            switch (substr($format, -1)) {
                case "a":
                    if(!ctype_alpha($string)) {
                        return false;
                    }
                    break;
                case "c":
                    if(!ctype_alnum($string)) {
                        return false;
                    }
                    break;
                case "n":
                    if(!is_numeric($string)) {
                        return false;
                    }
                    break;
                default:
                    return false;
            }
        }
        $nr = substr($banknr, 4) . substr($banknr, 0, 4);
        $nwnr = "";
        for ($i = 0; $i < strlen($nr); $i++) {
            $ch = substr($nr, $i, 1);
            if("0" <= $ch && $ch <= "9") {
                $nwnr .= $ch;
            } elseif("A" <= $ch && $ch <= "Z") {
                $nwnr .= strval(ord($ch) - 55);
            } else {
                return false;
            }
        }
        if($this->_iban_mod97($nwnr)) {
            return false;
        }
        return true;
    }
    private function checkBIC($bic)
    {
        $bic = strtoupper(str_replace(" ", "", trim($bic)));
        if(preg_match("/^[a-z]{4}[a-z]{2}[0-9a-z]{2}([0-9a-z]{3})?\\z/i", $bic) === 1) {
            return true;
        }
        return false;
    }
    private function _iban_landcode($country)
    {
        if(!isset($countries[$country])) {
            return false;
        }
        return $countries[$country];
    }
    private function _iban_mod97($iban)
    {
        $parts = ceil(strlen($iban) / 7);
        $remainder = "";
        for ($i = 0; $i < $parts; $i++) {
            $remainder = strval(intval($remainder . substr($iban, $i * 7, 7)) % 97);
        }
        return intval($remainder) != 1;
    }
    public function is_free($batchID)
    {
        if($batchID) {
            $result = Database_Model::getInstance()->getOne("HostFact_SDD_Batches", "BatchID")->where("BatchID", $batchID)->execute();
            if(!$result) {
                return true;
            }
            return false;
        }
        return false;
    }
    public function getBatchID($firstLast = "first")
    {
        $orderBy = $firstLast == "first" ? "ASC" : "DESC";
        $result = Database_Model::getInstance()->getOne("HostFact_SDD_Batches", ["BatchID"])->orderBy("BatchID", $orderBy)->execute();
        return $result->BatchID;
    }
    public function delete($batch_id, $notifyDebtorAboutNewDirectDebitDate = false, $notifyDebtorWithMailID = 0)
    {
        $batch_info = $this->getBatchInfo($batch_id);
        if(!in_array($batch_info->Status, ["draft", "downloadable"])) {
            $this->Error[] = __("sdd cannot remove batch, wrong status");
            return false;
        }
        if(0 < $batch_info->Count) {
            $batch_transactions = $this->getBatchTransactions($batch_id);
            foreach ($batch_transactions as $_transaction) {
                $this->moveInvoiceToNextBatchByElementID($_transaction["id"], $notifyDebtorAboutNewDirectDebitDate, $notifyDebtorWithMailID);
            }
            if(!empty($this->Error)) {
                return false;
            }
            $invoices_moved = true;
        }
        Database_Model::getInstance()->delete("HostFact_SDD_BatchElements")->where("BatchID", $batch_id)->execute();
        Database_Model::getInstance()->delete("HostFact_SDD_Batches")->where("BatchID", $batch_id)->execute();
        Database_Model::getInstance()->delete("HostFact_Log")->where("Type", "directdebit")->where("Reference", str_replace("SDD", "", $batch_id))->execute();
        $this->Success = [];
        $this->Success[] = __("sdd batch deleted");
        if(isset($invoices_moved) && $invoices_moved) {
            $this->Success[] = sprintf(__("sdd batch delete, invoices moved to batch"), $batch_info->Count, $this->BatchID);
        }
        return true;
    }
}
class SepaDirectDebit
{
    public $Success;
    public $Warning;
    public $Error;
    private $_transactions;
    private $_counter;
    private $_controlsum;
    private $conf;
    private $pain;
    private $batch;
    private $euCountries;
    public function __construct($batch_info)
    {
        $this->Success = $this->Warning = $this->Error = [];
        $this->_transactions = [];
        $this->_counter = ["Total" => 0, "FRST" => 0, "RCUR" => 0, "FNAL" => 0, "OOFF" => 0];
        $this->_controlsum = ["Total" => 0, "FRST" => 0, "RCUR" => 0, "FNAL" => 0, "OOFF" => 0];
        $this->batch["BatchID"] = $batch_info->BatchID;
        $this->batch["Date"] = $batch_info->Date;
        global $company;
        $this->Company["CompanyName"] = $this->stripCharacters(htmlspecialchars_decode($company->CompanyName));
        $this->Company["IBAN"] = strtoupper(trim(str_replace(" ", "", $batch_info->SDD_IBAN)));
        $this->Company["BIC"] = strtoupper(trim(str_replace(" ", "", $batch_info->SDD_BIC)));
        $this->Company["SepaID"] = $batch_info->SDD_ID;
        global $array_country_EU;
        $this->euCountries = array_keys($array_country_EU);
    }
    public function addInvoice($transaction_info)
    {
        $inv_inf = ["DebtorCode" => $transaction_info["DebtorCode"], "InvoiceCode" => $transaction_info["InvoiceCode"], "Amount" => round((double) $transaction_info["Amount"], 2), "MandateID" => $transaction_info["MandateID"], "MandateDate" => $transaction_info["MandateDate"], "BIC" => "", "Name" => substr($this->stripCharacters(trim($transaction_info["AccountName"]) ? $transaction_info["AccountName"] : ($transaction_info["CompanyName"] ? $transaction_info["CompanyName"] : $transaction_info["Initials"] . " " . $transaction_info["SurName"])), 0, 70), "AddressLine1" => substr($this->stripCharacters($transaction_info["Address"]), 0, 70), "AddressLine2" => substr($this->stripCharacters(trim($transaction_info["ZipCode"] . " " . $transaction_info["City"])), 0, 70), "Country" => strtoupper($transaction_info["Country"]), "IBAN" => strtoupper(trim(str_replace(" ", "", $transaction_info["IBAN"]))), "Description" => $transaction_info["InvoiceCode"], "Type" => $transaction_info["MandateType"]];
        $this->_transactions[$inv_inf["Type"]][] = $inv_inf;
        $this->_counter["Total"] += 1;
        $this->_controlsum["Total"] += $inv_inf["Amount"];
        $this->_counter[$inv_inf["Type"]] += 1;
        $this->_controlsum[$inv_inf["Type"]] += $inv_inf["Amount"];
        return true;
    }
    public function generateXML()
    {
        if(!is_writable("temp/")) {
            $pro_map_name = software_get_relative_path();
            $pro_map_name .= "temp/";
            $this->Error[] = sprintf(__("global - temp folder is not writable"), $pro_map_name);
            return false;
        }
        $this->pain = new SimpleXMLElement("<Document xmlns=\"urn:iso:std:iso:20022:tech:xsd:pain.008.001.02\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"></Document>");
        $this->pain->addChild("CstmrDrctDbtInitn");
        $this->generateGroupHeader();
        foreach ($this->_transactions as $type => $invoices) {
            if(!$this->generatePaymentInformation($type)) {
                return false;
            }
        }
        $this->filename = $this->batch["BatchID"] . ".xml";
        $xml = $this->pain->asXML();
        $xml = str_replace("><", ">\n<", $xml);
        if(!file_put_contents("temp/" . $this->filename, $xml, LOCK_EX)) {
            @unlink("temp/" . $this->filename);
            return false;
        }
        $_SESSION["force_download"] = $this->filename;
        return true;
    }
    private function generateGroupHeader()
    {
        $this->pain->CstmrDrctDbtInitn->addChild("GrpHdr");
        $this->pain->CstmrDrctDbtInitn->GrpHdr->addChild("MsgId", substr($this->Company["CompanyName"], 0, 17) . " " . $this->batch["Date"] . " " . date("His"));
        $this->pain->CstmrDrctDbtInitn->GrpHdr->addChild("CreDtTm", substr(date("c"), 0, 19));
        $this->pain->CstmrDrctDbtInitn->GrpHdr->addChild("NbOfTxs", $this->_counter["Total"]);
        $this->pain->CstmrDrctDbtInitn->GrpHdr->addChild("CtrlSum", number_format($this->_controlsum["Total"], 2, ".", ""));
        $this->pain->CstmrDrctDbtInitn->GrpHdr->addChild("InitgPty");
        $this->pain->CstmrDrctDbtInitn->GrpHdr->InitgPty->addChild("Nm", $this->Company["CompanyName"]);
        return true;
    }
    private function generatePaymentInformation($type)
    {
        if(!in_array($type, ["FNAL", "FRST", "OOFF", "RCUR"])) {
            $this->Error[] = __("sdd error invalid type for transaction");
            return false;
        }
        $child = $this->pain->CstmrDrctDbtInitn->addChild("PmtInf");
        $child->addChild("PmtInfId", $this->batch["BatchID"] . "-" . $type);
        $child->addChild("PmtMtd", "DD");
        $child->addChild("NbOfTxs", $this->_counter[$type]);
        $child->addChild("CtrlSum", number_format($this->_controlsum[$type], 2, ".", ""));
        $child->addChild("PmtTpInf");
        $child->PmtTpInf->addChild("SvcLvl");
        $child->PmtTpInf->SvcLvl->addChild("Cd", "SEPA");
        $child->PmtTpInf->addChild("LclInstrm");
        if(defined("SDD_TYPE") && in_array(SDD_TYPE, ["CORE", "B2B"])) {
            $child->PmtTpInf->LclInstrm->addChild("Cd", SDD_TYPE);
        } else {
            $child->PmtTpInf->LclInstrm->addChild("Cd", "CORE");
        }
        $child->PmtTpInf->addChild("SeqTp", $type);
        $child->addChild("ReqdColltnDt", $this->batch["Date"]);
        $child->addChild("Cdtr");
        $child->Cdtr->addChild("Nm", $this->Company["CompanyName"]);
        $child->addChild("CdtrAcct");
        $child->CdtrAcct->addChild("Id");
        $child->CdtrAcct->Id->addChild("IBAN", preg_replace("/[^a-z0-9]/i", "", $this->Company["IBAN"]));
        $child->addChild("CdtrAgt");
        $child->CdtrAgt->addChild("FinInstnId");
        if($this->Company["BIC"]) {
            $child->CdtrAgt->FinInstnId->addChild("BIC", preg_replace("/[^a-z0-9]/i", "", $this->Company["BIC"]));
        }
        $child->addChild("CdtrSchmeId");
        $child->CdtrSchmeId->addChild("Nm", $this->Company["CompanyName"]);
        $child->CdtrSchmeId->addChild("Id");
        $child->CdtrSchmeId->Id->addChild("PrvtId");
        $child->CdtrSchmeId->Id->PrvtId->addChild("Othr");
        $child->CdtrSchmeId->Id->PrvtId->Othr->addChild("Id", $this->Company["SepaID"]);
        $child->CdtrSchmeId->Id->PrvtId->Othr->addChild("SchmeNm");
        $child->CdtrSchmeId->Id->PrvtId->Othr->SchmeNm->addChild("Prtry", "SEPA");
        foreach ($this->_transactions[$type] as $key => $data) {
            $this->generateDirectDebitTransactionInformation($data, $child);
        }
        return true;
    }
    private function generateDirectDebitTransactionInformation($data, $child)
    {
        $child = $child->addChild("DrctDbtTxInf");
        $child->addChild("PmtId");
        $child->PmtId->addChild("EndToEndId", substr($data["InvoiceCode"] . " - " . $data["DebtorCode"], 0, 35));
        $child->addChild("InstdAmt", $data["Amount"]);
        $child->InstdAmt->addAttribute("Ccy", "EUR");
        $child->addChild("ChrgBr", "SLEV");
        $child->addChild("DrctDbtTx");
        $child->DrctDbtTx->addChild("MndtRltdInf");
        $child->DrctDbtTx->MndtRltdInf->addChild("MndtId", $data["MandateID"]);
        $child->DrctDbtTx->MndtRltdInf->addChild("DtOfSgntr", $data["MandateDate"]);
        $child->DrctDbtTx->MndtRltdInf->addChild("AmdmntInd", "false");
        $child->addChild("DbtrAgt");
        $child->DbtrAgt->addChild("FinInstnId");
        if($data["BIC"]) {
            $child->DbtrAgt->FinInstnId->addChild("BIC", preg_replace("/[^a-z0-9]/i", "", $data["BIC"]));
        } else {
            $child->DbtrAgt->FinInstnId->addChild("Othr");
            $child->DbtrAgt->FinInstnId->Othr->addChild("Id", "NOTPROVIDED");
        }
        $child->addChild("Dbtr");
        $child->Dbtr->addChild("Nm", $data["Name"]);
        if(!in_array($data["Country"], $this->euCountries)) {
            $child->Dbtr->addChild("PstlAdr");
            $child->Dbtr->PstlAdr->addChild("Ctry", $data["Country"]);
            $child->Dbtr->PstlAdr->addChild("AdrLine", $data["AddressLine1"]);
            $child->Dbtr->PstlAdr->addChild("AdrLine", $data["AddressLine2"]);
        }
        $child->addChild("DbtrAcct");
        $child->DbtrAcct->addChild("Id");
        $child->DbtrAcct->Id->addChild("IBAN", preg_replace("/[^a-z0-9]/i", "", $data["IBAN"]));
        $child->addChild("RmtInf");
        $child->RmtInf->addChild("Ustrd", substr($data["InvoiceCode"] . " / " . $data["DebtorCode"], 0, 35));
        return true;
    }
    private function stripCharacters($string)
    {
        $string = preg_replace("/[^a-z0-9\\/\\-?:().,'+ )]/i", "", $string);
        return $string;
    }
}

?>