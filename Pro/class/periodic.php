<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class periodic
{
    public $Identifier;
    public $ProductCode;
    public $Debtor;
    public $Description;
    public $PeriodicType;
    public $Reference;
    public $PStatus;
    public $StartPeriod;
    public $EndPeriod;
    public $Periods;
    public $Periodic;
    public $LastDate;
    public $NextDate;
    public $PriceExcl;
    public $TaxPercentage;
    public $DiscountPercentage;
    public $Number;
    public $NumberSuffix;
    public $Status;
    public $AutoRenew;
    public $TerminationDate;
    public $Reminder;
    public $ReminderDate;
    public $ReminderPeriodics;
    public $PricePeriod;
    public $InvoiceAuthorisation;
    public $StartContract;
    public $EndContract;
    public $ContractRenewalDate;
    public $ContractPeriods;
    public $ContractPeriodic;
    public $Domain;
    public $Hosting;
    public $Date;
    public $Free1;
    public $Free2;
    public $Free3;
    public $Free4;
    public $Free5;
    public $CountRows;
    public $Table;
    public $Error;
    public $Warning;
    public $Success;
    public $Variables = ["Identifier", "ProductCode", "Debtor", "Description", "PeriodicType", "Reference", "StartPeriod", "EndPeriod", "Periods", "Periodic", "LastDate", "NextDate", "PriceExcl", "TaxPercentage", "DiscountPercentage", "Number", "NumberSuffix", "Status", "AutoRenew", "TerminationDate", "Reminder", "ReminderDate", "InvoiceAuthorisation", "StartContract", "EndContract", "ContractRenewalDate", "ContractPeriods", "ContractPeriodic", "Free1", "Free2", "Free3", "Free4", "Free5"];
    public function __construct()
    {
        $this->TaxPercentage = STANDARD_TAX;
        $this->Date = date("Ymd");
        $this->Status = 1;
        $this->AutoRenew = "yes";
        $this->Periods = 1;
        $this->Periodic = "j";
        $this->ContractPeriods = 1;
        $this->DiscountPercentage = "";
        $this->Number = 1;
        $this->NumberSuffix = "";
        $this->InvoiceAuthorisation = "yes";
        $this->Error = [];
        $this->Warning = [];
        $this->Success = [];
        $this->ContractInfo = [];
    }
    public function __destruct()
    {
    }
    public function show($id = NULL)
    {
        $this->Identifier = 0 < $id ? $id : $this->Identifier;
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for subscription");
            return false;
        }
        $result = Database_Model::getInstance()->getOne(["HostFact_PeriodicElements", "HostFact_Debtors"], ["HostFact_PeriodicElements.*", "HostFact_PeriodicElements.`NextDate` AS OriginalNextDate", "(SELECT CASE HostFact_Debtors.InvoiceCollect \n\t\t\t\t\t\t\t\t\tWHEN '2' THEN CONCAT(DATE_FORMAT(HostFact_PeriodicElements.`NextDate`, '%Y-%m-'),IF(DAYOFMONTH(HostFact_PeriodicElements.`NextDate`) >= 15, '15', '01')) \n\t\t\t\t\t\t\t\t\tWHEN '1' THEN CONCAT(DATE_FORMAT(HostFact_PeriodicElements.`NextDate`, '%Y-%m-'),'01') \n\t\t\t\t\t\t\t\t\tWHEN '-1' THEN " . (INVOICE_COLLECT_ENABLED == "yes" ? "CONCAT(DATE_FORMAT(HostFact_PeriodicElements.`NextDate`, '%Y-%m-'),IF(DAYOFMONTH(HostFact_PeriodicElements.`NextDate`) >= 15, '" . (INVOICE_COLLECT_TPM == 1 ? "01" : "15") . "', '01'))" : "HostFact_PeriodicElements.`NextDate`") . " \n\t\t\t\t\t\t\t\t\tELSE HostFact_PeriodicElements.`NextDate` END) as NextDate", "HostFact_Products.ProductName"])->join("HostFact_Products", "HostFact_Products.ProductCode = HostFact_PeriodicElements.ProductCode")->where("HostFact_PeriodicElements.id", $this->Identifier)->where("HostFact_PeriodicElements.Debtor = HostFact_Debtors.id")->execute();
        if(!$result || empty($result->id)) {
            $this->Error[] = __("invalid identifier for subscription");
            return false;
        }
        foreach ($result as $key => $value) {
            $this->{$key} = htmlspecialchars($value);
        }
        $this->OldTerminationDate = $this->TerminationDate;
        $this->StartPeriod = rewrite_date_db2site($this->StartPeriod);
        $this->EndPeriod = rewrite_date_db2site($this->EndPeriod);
        $this->LastDate = rewrite_date_db2site($this->LastDate);
        $this->NextDate = rewrite_date_db2site($this->NextDate);
        $this->TerminationDate = rewrite_date_db2site($this->TerminationDate);
        $this->ReminderDate = rewrite_date_db2site($this->ReminderDate);
        $this->StartContract = rewrite_date_db2site($this->StartContract);
        $this->EndContract = rewrite_date_db2site($this->EndContract);
        $this->ContractRenewalDate = rewrite_date_db2site($this->ContractRenewalDate);
        $this->OldNextDateVF = $this->NextDate;
        $this->PriceIncl = VAT_CALC_METHOD == "incl" ? round($this->PriceExcl * (1 + $this->TaxPercentage), 5) : round((double) $this->PriceExcl, 5) * (1 + $this->TaxPercentage);
        $line_amount = getLineAmount(VAT_CALC_METHOD, $this->PriceExcl, $this->Periods, $this->Number, $this->TaxPercentage, $this->DiscountPercentage);
        $this->AmountIncl = $line_amount["incl"];
        $this->AmountExcl = $line_amount["excl"];
        return true;
    }
    public function lookupSubscription($periodic_type, $periodic_reference)
    {
        $result = Database_Model::getInstance()->getOne("HostFact_PeriodicElements", ["id"])->where("PeriodicType", $periodic_type)->where("Reference", $periodic_reference)->where("Status", ["!=" => "9"])->execute();
        if(!$result || empty($result->id)) {
            return false;
        }
        return $result->id;
    }
    public function showContractInfo()
    {
        $result = Database_Model::getInstance()->getOne("HostFact_PeriodicElements_Renewals")->where("PeriodicID", $this->Identifier)->orderBy("Date", "DESC")->execute();
        if($result) {
            foreach ($result as $key => $value) {
                $this->ContractInfo[$key] = htmlspecialchars($value);
            }
        }
    }
    public function format($currency_sign = true)
    {
        $this->AmountIncl = money($this->AmountIncl, $currency_sign);
        $this->AmountExcl = money($this->AmountExcl, $currency_sign);
        $this->PriceIncl = money($this->PriceIncl, $currency_sign);
        $this->PriceExcl = money($this->PriceExcl, $currency_sign);
        $pattern = "/\\[[A-Z]\\:(.*?)\\]/is";
        $replacements = "";
        $this->Description = preg_replace($pattern, $replacements, $this->Description);
        $this->FullDiscountPercentage = round($this->DiscountPercentage * 100, 2);
    }
    public function changeDebtor($new_debtor_id)
    {
        if(!is_numeric($new_debtor_id)) {
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_PeriodicElements", ["Debtor" => $new_debtor_id])->where("id", $this->Identifier)->execute();
        if($result) {
            return true;
        }
        return false;
    }
    public function add()
    {
        $this->OldNumber = $this->Number;
        $this->PriceExcl = deformat_money($this->PriceExcl);
        $this->DiscountPercentage = floatval(round(deformat_money($this->DiscountPercentage), 2)) / 100;
        $this->Number = deformat_money($this->Number);
        $this->StartPeriod = rewrite_date_site2db($this->StartPeriod);
        $m0 = substr($this->StartPeriod, 4, 2);
        $d0 = substr($this->StartPeriod, 6, 2);
        $y0 = substr($this->StartPeriod, 0, 4);
        if(!$this->StartPeriod) {
            $this->StartPeriod = $this->sqlNextDate($this->Date);
        }
        $this->EndPeriod = rewrite_date_site2db($this->EndPeriod);
        if(!$this->EndPeriod) {
            $this->EndPeriod = $this->sqlNextDate("StartPeriod");
        }
        $this->LastDate = rewrite_date_site2db($this->LastDate);
        $m = substr($this->LastDate, 4, 2);
        $d = substr($this->LastDate, 6, 2);
        $y = substr($this->LastDate, 0, 4);
        if(!$this->LastDate) {
            $this->LastDate = "";
        }
        $this->NextDate = rewrite_date_site2db($this->NextDate);
        if(!$this->NextDate) {
            $PeriodicInvoiceDays = $this->getPeriodicInvoiceDays($this->Debtor);
            if($this->LastDate == "" || (int) $PeriodicInvoiceDays !== 0) {
                if(isset($this->IsInvoice) && $this->IsInvoice) {
                    if(0 < $y0) {
                        $this->NextDate = date("Ymd", strtotime("-" . $PeriodicInvoiceDays . " days", mktime(0, 0, 0, $m0, $d0, $y0)));
                    } else {
                        $this->NextDate = "DATE_ADD(`StartPeriod`, INTERVAL -" . intval($PeriodicInvoiceDays) . " DAY)";
                    }
                } else {
                    $this->NextDate = date("Ymd", strtotime("-" . $PeriodicInvoiceDays . " days", mktime(0, 0, 0, $m0, $d0, $y0)));
                }
            } elseif(isset($this->IsInvoice) && $this->IsInvoice) {
                $this->NextDate = $this->sqlNextDate(date("Ymd", mktime(0, 0, 0, $m, $d, $y)));
            } else {
                $this->NextDate = date("Ymd", mktime(0, 0, 0, $m, $d, $y));
            }
        }
        $this->TerminationDate = rewrite_date_site2db($this->TerminationDate);
        $this->ReminderDate = rewrite_date_site2db($this->ReminderDate);
        $this->StartContract = rewrite_date_site2db($this->StartContract);
        $this->EndContract = rewrite_date_site2db($this->EndContract);
        $this->ContractRenewalDate = rewrite_date_site2db($this->ContractRenewalDate);
        if(!$this->validate()) {
            $this->repair();
            return false;
        }
        if(!$this->EndContract) {
            if(isset($this->IsInvoice) && $this->IsInvoice) {
                $this->EndContract = $this->StartPeriod;
            } elseif($this->ContractPeriods != $this->Periods || $this->ContractPeriodic != $this->Periodic) {
                $interval = $this->__getMySQLInterval($this->ContractPeriodic, $this->ContractPeriods);
                $this->EndContract = "DATE_ADD(`StartContract`, INTERVAL " . $interval . ")";
            } else {
                $this->EndContract = $this->EndPeriod;
            }
        }
        $result = Database_Model::getInstance()->insert("HostFact_PeriodicElements", ["ProductCode" => $this->ProductCode, "Debtor" => $this->Debtor, "Description" => $this->Description, "PeriodicType" => $this->PeriodicType, "Reference" => $this->Reference, "StartPeriod" => substr($this->StartPeriod, 0, 4) != "DATE" ? $this->StartPeriod : ["RAW" => $this->StartPeriod], "EndPeriod" => substr($this->EndPeriod, 0, 4) != "DATE" ? $this->EndPeriod : ["RAW" => $this->EndPeriod], "Periods" => $this->Periods, "Periodic" => $this->Periodic, "LastDate" => $this->LastDate, "NextDate" => substr($this->NextDate, 0, 4) != "DATE" ? $this->NextDate : ["RAW" => $this->NextDate], "PriceExcl" => $this->PriceExcl, "TaxPercentage" => $this->TaxPercentage, "DiscountPercentage" => $this->DiscountPercentage, "Number" => $this->Number, "NumberSuffix" => $this->NumberSuffix, "Status" => $this->Status, "TerminationDate" => $this->TerminationDate, "Reminder" => $this->Reminder, "ReminderDate" => $this->ReminderDate, "InvoiceAuthorisation" => $this->InvoiceAuthorisation, "StartContract" => substr($this->StartContract, 0, 4) != "DATE" ? $this->StartContract : ["RAW" => $this->StartContract], "EndContract" => substr($this->EndContract, 0, 4) != "DATE" ? $this->EndContract : ["RAW" => $this->EndContract], "ContractRenewalDate" => substr($this->ContractRenewalDate, 0, 4) != "DATE" ? $this->ContractRenewalDate : ["RAW" => $this->ContractRenewalDate], "ContractPeriods" => $this->ContractPeriods, "ContractPeriodic" => $this->ContractPeriodic, "AutoRenew" => $this->AutoRenew])->execute();
        if($result) {
            $this->Identifier = $result;
            if(!$this->Reference && (!isset($this->FromServicePage) || !$this->FromServicePage)) {
                $this->check_reference();
            } elseif($this->PeriodicType && $this->PeriodicType != "other" && 0 < $this->Reference && isset($this->isProductTypeKnown) && $this->isProductTypeKnown === true && (!isset($this->FromServicePage) || !$this->FromServicePage)) {
                $this->check_reference($this->PeriodicType, $this->Reference);
            }
            if(!empty($this->Error)) {
                Database_Model::getInstance()->delete("HostFact_PeriodicElements")->where("id", $this->Identifier)->execute();
                $this->Identifier = 0;
                $this->repair();
                return false;
            }
            $this->Success[] = sprintf(__("subscription is created"), stripReturnAndSubstring($this->Description, 120));
            return true;
        }
        $this->repair();
        return false;
    }
    public function edit()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for subscription");
            return false;
        }
        $this->OldNumber = $this->Number;
        $this->PriceExcl = deformat_money($this->PriceExcl);
        $this->DiscountPercentage = floatval(round(deformat_money($this->DiscountPercentage), 2)) / 100;
        $this->Number = deformat_money($this->Number);
        $this->StartPeriod = rewrite_date_site2db($this->StartPeriod);
        $m0 = substr($this->StartPeriod, 4, 2);
        $d0 = substr($this->StartPeriod, 6, 2);
        $y0 = substr($this->StartPeriod, 0, 4);
        $this->EndPeriod = rewrite_date_site2db($this->EndPeriod);
        if(!$this->EndPeriod) {
            $this->EndPeriod = $this->sqlNextDate($this->StartPeriod);
        }
        $this->LastDate = rewrite_date_site2db($this->LastDate);
        $m = substr($this->LastDate, 4, 2);
        $d = substr($this->LastDate, 6, 2);
        $y = substr($this->LastDate, 0, 4);
        if(!$this->LastDate) {
            $this->LastDate = "";
        }
        $this->StartContract = rewrite_date_site2db($this->StartContract);
        $this->EndContract = rewrite_date_site2db($this->EndContract);
        $this->ContractRenewalDate = rewrite_date_site2db($this->ContractRenewalDate);
        $update_reminder_date = false;
        if($this->OldNextDateVF != $this->NextDate) {
            $result = Database_Model::getInstance()->getOne("HostFact_PeriodicElements", ["id"])->where("id", $this->Identifier)->where("`ReminderDate` = `NextDate`")->execute();
            if($result && 0 < $result->id && $result->id == $this->Identifier) {
                $update_reminder_date = true;
            }
        }
        $this->NextDate = rewrite_date_site2db($this->NextDate);
        if(!$this->NextDate) {
            $PeriodicInvoiceDays = $this->getPeriodicInvoiceDays($this->Debtor);
            if($this->LastDate == "" || (int) $PeriodicInvoiceDays !== 0) {
                if(isset($this->IsInvoice) && $this->IsInvoice) {
                    $this->NextDate = date("Ymd", strtotime("-" . $PeriodicInvoiceDays . " days", mktime(0, 0, 0, $m0, $d0, $y0)));
                } else {
                    $this->NextDate = date("Ymd", strtotime("-" . $PeriodicInvoiceDays . " days", mktime(0, 0, 0, $m0, $d0, $y0)));
                }
            } elseif(isset($this->IsInvoice) && $this->IsInvoice) {
                $this->NextDate = $this->sqlNextDate(date("Ymd", mktime(0, 0, 0, $m, $d, $y)));
            } else {
                $this->NextDate = $this->sqlNextDate($this->LastDate);
            }
        }
        $this->TerminationDate = rewrite_date_site2db($this->TerminationDate);
        if(!$this->TerminationDate && $this->Status == 8) {
            $this->Status = 1;
        }
        if(isset($this->OldTerminationDate) && $this->OldTerminationDate != "0000-00-00" && $this->TerminationDate && $this->OldTerminationDate != date("Y-m-d", strtotime($this->TerminationDate))) {
            $this->Error[] = __("please reactivate first, before changing the termination date");
        }
        if($update_reminder_date === true) {
            $this->ReminderDate = $this->NextDate;
        } else {
            $this->ReminderDate = rewrite_date_site2db($this->ReminderDate);
        }
        if(!$this->validate()) {
            $this->repair();
            return false;
        }
        if($this->PeriodicType == "hosting" && 0 < $this->Reference) {
            Database_Model::getInstance()->update("HostFact_Hosting", ["Debtor" => $this->Debtor])->where("PeriodicID", $this->Identifier)->where("id", $this->Reference)->where("Debtor", ["!=" => $this->Debtor])->execute();
        } elseif($this->PeriodicType == "domain" && 0 < $this->Reference) {
            Database_Model::getInstance()->update("HostFact_Domains", ["Debtor" => $this->Debtor])->where("PeriodicID", $this->Identifier)->where("id", $this->Reference)->where("Debtor", ["!=" => $this->Debtor])->execute();
        }
        $result = Database_Model::getInstance()->update("HostFact_PeriodicElements", ["ProductCode" => $this->ProductCode, "Debtor" => $this->Debtor, "Description" => $this->Description, "PeriodicType" => $this->PeriodicType, "Reference" => $this->Reference, "StartPeriod" => substr($this->StartPeriod, 0, 4) != "DATE" ? $this->StartPeriod : ["RAW" => $this->StartPeriod], "EndPeriod" => substr($this->EndPeriod, 0, 4) != "DATE" ? $this->EndPeriod : ["RAW" => $this->EndPeriod], "Periods" => $this->Periods, "Periodic" => $this->Periodic, "LastDate" => $this->LastDate, "NextDate" => substr($this->NextDate, 0, 4) != "DATE" ? $this->NextDate : ["RAW" => $this->NextDate], "PriceExcl" => $this->PriceExcl, "TaxPercentage" => $this->TaxPercentage, "DiscountPercentage" => $this->DiscountPercentage, "Number" => $this->Number, "NumberSuffix" => $this->NumberSuffix, "Status" => $this->Status, "TerminationDate" => $this->TerminationDate, "Reminder" => $this->Reminder, "ReminderDate" => substr($this->ReminderDate, 0, 4) != "DATE" ? $this->ReminderDate : ["RAW" => $this->ReminderDate], "InvoiceAuthorisation" => $this->InvoiceAuthorisation, "StartContract" => substr($this->StartContract, 0, 4) != "DATE" ? $this->StartContract : ["RAW" => $this->StartContract], "EndContract" => substr($this->EndContract, 0, 4) != "DATE" ? $this->EndContract : ["RAW" => $this->EndContract], "ContractRenewalDate" => substr($this->ContractRenewalDate, 0, 4) != "DATE" ? $this->ContractRenewalDate : ["RAW" => $this->ContractRenewalDate], "ContractPeriods" => $this->ContractPeriods, "ContractPeriodic" => $this->ContractPeriodic, "AutoRenew" => $this->AutoRenew])->where("id", $this->Identifier)->execute();
        if($result) {
            if(!$this->Reference && (!isset($this->FromServicePage) || !$this->FromServicePage)) {
                $this->check_reference();
            }
            if(!empty($this->Error)) {
                $this->repair();
                return false;
            }
            $this->Success[] = sprintf(__("subscription is adjusted"), stripReturnAndSubstring($this->Description, 120));
            if(isset($this->OldTerminationDate) && $this->OldTerminationDate == "0000-00-00" && $this->TerminationDate) {
                require_once "class/terminationprocedure.php";
                $termination = new Termination_Model();
                $termination->terminateService($this->PeriodicType, 0 < $this->Reference ? $this->Reference : $this->Identifier, $this->TerminationDate);
                $service_info = ["Type" => $this->PeriodicType, "id" => 0 < $this->Reference ? $this->Reference : $this->Identifier, "Debtor" => $this->Debtor, "TerminationDate" => date("Y-m-d", strtotime($this->TerminationDate))];
                do_action("service_is_terminated", $service_info);
            } elseif(isset($this->OldTerminationDate) && $this->OldTerminationDate != "0000-00-00" && !$this->TerminationDate) {
                require_once "class/terminationprocedure.php";
                $termination = new Termination_Model();
                if($termination->show($this->PeriodicType, 0 < $this->Reference ? $this->Reference : $this->Identifier)) {
                    $termination->undoTermination(false, true);
                }
                $service_info = ["Type" => $this->PeriodicType, "id" => 0 < $this->Reference ? $this->Reference : $this->Identifier, "Debtor" => $this->Debtor];
                do_action("service_is_reactivated", $service_info);
            }
            return true;
        }
        $this->repair();
        return false;
    }
    public function updatePrice()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for subscription");
            return false;
        }
        $this->PriceExcl = deformat_money($this->PriceExcl);
        $result = Database_Model::getInstance()->update("HostFact_PeriodicElements", ["PriceExcl" => $this->PriceExcl])->where("id", $this->Identifier)->execute();
        if($result) {
            return true;
        }
        return false;
    }
    public function delete($id, $confirmReference = "none")
    {
        $id = 0 < $id ? $id : $this->Identifier;
        if(!is_numeric($id)) {
            $this->Error[] = __("invalid identifier for subscription");
            return false;
        }
        $result_info = Database_Model::getInstance()->getOne("HostFact_PeriodicElements", ["Description", "PeriodicType", "Reference", "Debtor"])->where("id", $id)->execute();
        if(!$result_info) {
            $this->Error[] = __("invalid identifier for subscription");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_PeriodicElements", ["Status" => "9"])->where("id", $id)->execute();
        if(!$result) {
            return false;
        }
        if($confirmReference == "remove") {
            $result = Database_Model::getInstance()->update("HostFact_Domains", ["Status" => "9"])->where("PeriodicID", $id)->execute();
            if(!$result) {
                return false;
            }
            $result = Database_Model::getInstance()->update("HostFact_Hosting", ["Status" => "9"])->where("PeriodicID", $id)->execute();
            if(!$result) {
                return false;
            }
        } else {
            $result = Database_Model::getInstance()->update("HostFact_Domains", ["PeriodicID" => "0"])->where("PeriodicID", $id)->execute();
            if(!$result) {
                return false;
            }
            $result = Database_Model::getInstance()->update("HostFact_Hosting", ["PeriodicID" => "0"])->where("PeriodicID", $id)->execute();
            if(!$result) {
                return false;
            }
        }
        global $_module_instances;
        if(0 < $result->Reference && $result->PeriodicType && isset($_module_instances[$result->PeriodicType])) {
            $_module_instances[$result->PeriodicType]->subscription_removed($result->Reference);
        }
        $this->Success[] = sprintf(__("subscription is removed"), stripReturnAndSubstring($result->Description, 120));
        $service_info = ["Type" => $result_info->PeriodicType, "id" => 0 < $result_info->Reference ? $result_info->Reference : $id, "Debtor" => $result_info->Debtor];
        do_action("service_is_removed", $service_info);
        return true;
    }
    public function deleteFromDatabase($id)
    {
        if(!is_numeric($id)) {
            $this->Error[] = __("invalid identifier for subscription");
            return false;
        }
        $result = Database_Model::getInstance()->delete("HostFact_PeriodicElements")->where("id", $id)->execute();
        if(!$result) {
            return false;
        }
        Database_Model::getInstance()->update("HostFact_Domains", ["PeriodicID" => "0"])->where("PeriodicID", $id)->execute();
        Database_Model::getInstance()->update("HostFact_Hosting", ["PeriodicID" => "0"])->where("PeriodicID", $id)->execute();
        return true;
    }
    public function repair()
    {
        for ($i = 1; $i < count($this->Variables); $i++) {
            $this->{$this->Variables[$i]} = htmlspecialchars($this->{$this->Variables[$i]});
        }
        $this->StartPeriod = rewrite_date_db2site($this->StartPeriod);
        $this->EndPeriod = rewrite_date_db2site($this->EndPeriod);
        $this->LastDate = rewrite_date_db2site($this->LastDate);
        $this->NextDate = rewrite_date_db2site($this->NextDate);
        $this->TerminationDate = rewrite_date_db2site($this->TerminationDate);
        $this->StartContract = rewrite_date_db2site($this->StartContract);
        $this->EndContract = rewrite_date_db2site($this->EndContract);
        $this->ContractRenewalDate = rewrite_date_db2site($this->ContractRenewalDate);
    }
    public function check_reference($service_type = false, $service_reference = 0)
    {
        if($service_type === false) {
            $result = $this->checkRecognition($this->ProductCode, $this->Description);
        }
        if($service_type == "hosting" || $service_type === false && $result["PeriodicType"] == "hosting") {
            $this->PeriodicType = "hosting";
            require_once "class/hosting.php";
            $hosting = new hosting();
            if(isset($result["Auto"]) && $result["Auto"] === true) {
                require_once "class/debtor.php";
                $debtor = new debtor();
                $debtor->Identifier = $this->Debtor;
                $debtor->show();
                $accountname = $hosting->generateNewAccountname(ACCOUNT_GENERATION, $debtor->CompanyName, $debtor->SurName, $debtor->Initials, $this->Domain);
                $debtor->__destruct();
                unset($debtor);
                if($accountname === false) {
                    $this->Error = array_merge($this->Error, $hosting->Error);
                    return false;
                }
            } elseif(isset($result["Match"])) {
                $accountname = $result["Match"];
                if(!$accountname) {
                    $this->Error[] = __("cannot generate accountname");
                }
            }
            if($service_type === false && !$accountname) {
                return false;
            }
            $existing = $service_type == "hosting" ? $service_reference : $hosting->is_free($accountname, true);
            if(0 < $existing) {
                $hosting->Identifier = $existing;
                $hosting->show($existing, false);
                if($this->Debtor == $hosting->Debtor || $hosting->Debtor == "-1") {
                    $hosting->Debtor = $this->Debtor;
                    if($hosting->Status == "-1") {
                        $hosting->Status = "1";
                        require_once "class/automation.php";
                        $automation = new automation();
                        $automation->show();
                        if($automation->makeaccount_value == 1 && $automation->makeaccount_run == "create" && $hosting->Domain) {
                            $hosting->Status = "3";
                        }
                        unset($automation);
                    }
                    $result_db = Database_Model::getInstance()->update("HostFact_Hosting", ["Debtor" => $hosting->Debtor, "PeriodicID" => $this->Identifier, "Status" => $hosting->Status])->where("id", $existing)->execute();
                    $this->Hosting = $existing;
                    if($result_db !== false) {
                        createLog("hosting", $existing, "account connected to periodic element");
                        Database_Model::getInstance()->update("HostFact_PeriodicElements", ["Reference" => $existing, "PeriodicType" => "hosting"])->where("id", $this->Identifier)->execute();
                        return true;
                    }
                    return false;
                }
                $this->Error[] = sprintf(__("hostingaccount already assigned"), $this->Username);
                return false;
            }
            $hosting->Debtor = $this->Debtor;
            $hosting->Product = $result["ProductID"];
            require_once "class/product.php";
            $product = new product();
            $product->Identifier = $result["ProductID"];
            $product->show();
            $hosting->Package = $product->PackageID;
            unset($product);
            $hosting->Username = $accountname;
            global $h_domain;
            global $h_domainID;
            $hosting->Domain = isset($h_domain) ? $h_domain : "";
            if($hosting->Domain) {
                unset($h_domain);
            }
            $hosting->Password = generatePassword();
            require_once "class/automation.php";
            $automation = new automation();
            $automation->show();
            if($automation->makeaccount_value == 1 && $automation->makeaccount_run == "create" && $hosting->Domain) {
                $hosting->Status = "3";
            }
            unset($automation);
            $hosting->Status = $this->PStatus ? $this->PStatus : $hosting->Status;
            $hosting->PeriodicID = $this->Identifier;
            $hosting->add();
            $this->Hosting = $hosting->Identifier;
            if($hosting->Domain && isset($h_domainID) && 0 < $h_domainID) {
                $result_db = Database_Model::getInstance()->getOne("HostFact_Domains", ["HostingID"])->where("id", $h_domainID)->execute();
                if(empty($result_db->HostingID)) {
                    Database_Model::getInstance()->update("HostFact_Domains", ["HostingID" => $this->Hosting])->where("id", $h_domainID)->execute();
                    require_once "class/domain.php";
                    $domain = new domain();
                    $domain->determineNameservers($h_domainID);
                }
            }
            $this->Error = array_merge($this->Error, $hosting->Error);
            $this->Reference = $hosting->Identifier;
            $hosting->__destruct();
            unset($hosting);
        } elseif($service_type == "domain" || $service_type === false && $result["PeriodicType"] == "domain" && 0 < $result["TLDcheck"]) {
            $this->PeriodicType = "domain";
            require_once "class/domain.php";
            $domain = new domain();
            if($service_type === false) {
                $found_domain = explode(".", $result["Match"], 2);
            }
            $existing = $service_type == "domain" ? $service_reference : $domain->is_free($found_domain[0], isset($found_domain[1]) ? $found_domain[1] : "", true);
            if(0 < $existing) {
                $domain->show($existing);
                if($service_type == "domain") {
                    $found_domain = explode(".", $domain->Domain . "." . $domain->Tld, 2);
                }
                if($this->Debtor == $domain->Debtor || $domain->Debtor == "-1") {
                    if(0 < $domain->PeriodicID && $this->Identifier != $domain->PeriodicID) {
                        $this->Warning[] = sprintf(__("domain already linked to other subscription"), $found_domain[0] . "." . $found_domain[1]);
                        return false;
                    }
                    $domain->Debtor = $this->Debtor;
                    if($domain->Status == "-1") {
                        $domain->Status = "1";
                        require_once "class/automation.php";
                        $automation = new automation();
                        $automation->show();
                        if($automation->registerdomain_value == 1 && $automation->registerdomain_run == "create") {
                            $domain->Status = "3";
                        }
                        unset($automation);
                    }
                    global $h_hosting;
                    global $h_domainID;
                    $domain->HostingID = isset($h_hosting) ? $h_hosting : (0 < $domain->HostingID ? $domain->HostingID : "0");
                    if(isset($h_domainID) && 0 < $h_domainID) {
                    } elseif(0 < $domain->HostingID) {
                        $result_db = Database_Model::getInstance()->getOne("HostFact_Hosting", ["Domain", "Username"])->where("id", $domain->HostingID)->execute();
                        if($result_db->Domain == "") {
                            if(ACCOUNT_GENERATION == 3 && substr($result_db->Username, 0, strlen(ACCOUNTCODE_PREFIX)) == ACCOUNTCODE_PREFIX && strlen($result_db->Username) == strlen(ACCOUNTCODE_PREFIX . ACCOUNTCODE_NUMBER)) {
                                require_once "class/hosting.php";
                                $hosting = new hosting();
                                $accountname = $hosting->generateNewAccountname(ACCOUNT_GENERATION, "", "", "", $result["Match"]);
                                $hosting->__destruct();
                                unset($hosting);
                            }
                            if(isset($accountname) && $accountname) {
                                Database_Model::getInstance()->update("HostFact_Hosting", ["Username" => $accountname, "Domain" => $result["Match"]])->where("id", $domain->HostingID)->execute();
                            } else {
                                Database_Model::getInstance()->update("HostFact_Hosting", ["Domain" => $result["Match"]])->where("id", $domain->HostingID)->execute();
                            }
                        }
                    }
                    $result_db = Database_Model::getInstance()->update("HostFact_Domains", ["Debtor" => $domain->Debtor, "PeriodicID" => $this->Identifier, "Status" => $domain->Status, "HostingID" => $domain->HostingID])->where("id", $existing)->execute();
                    $this->Domain = $domain->Domain . "." . $domain->Tld;
                    $this->DomainID = $existing;
                    $domain->__destruct();
                    unset($domain);
                    if($result_db !== false) {
                        createLog("domain", $existing, "domain connected to periodic element");
                        Database_Model::getInstance()->update("HostFact_PeriodicElements", ["Reference" => $existing, "PeriodicType" => "domain"])->where("id", $this->Identifier)->execute();
                        return true;
                    }
                    return false;
                }
                $this->Error[] = sprintf(__("domain already assigned"), $found_domain[0] . "." . $found_domain[1]);
                return false;
            }
            $domain->Debtor = $this->Debtor;
            $domain->Product = $result["ProductID"];
            list($domain->Domain, $domain->Tld) = $found_domain;
            $domain->PeriodicID = $this->Identifier;
            require_once "class/automation.php";
            $automation = new automation();
            $automation->show();
            if($automation->registerdomain_value == 1 && $automation->registerdomain_run == "create") {
                $domain->Status = "3";
            }
            $domain->Status = $this->PStatus ? $this->PStatus : $domain->Status;
            require_once "class/registrar.php";
            $registrar = new registrar();
            $registrar->search($domain->Tld);
            $domain->Registrar = $registrar->Identifier ? $registrar->Identifier : 0;
            if($domain->Status == "4") {
                $domain->RegistrationDate = is_date(str_replace("'", "", $this->StartPeriod)) ? rewrite_date_db2site(str_replace("'", "", $this->StartPeriod)) : rewrite_date_db2site(date("Ymd"));
                $domain->ExpirationDate = is_date(str_replace("'", "", $this->EndPeriod)) ? rewrite_date_db2site(str_replace("'", "", $this->EndPeriod)) : rewrite_date_db2site(date("Ymd", mktime(0, 0, 0, date("m"), date("d"), date("Y") + 1)));
            }
            $domain->generateWHOIS($this->Debtor);
            global $h_hosting;
            global $h_domainID;
            $domain->HostingID = isset($h_hosting) ? $h_hosting : "0";
            unset($automation);
            $registrar->__destruct();
            unset($registrar);
            $domain->add();
            $this->DomainID = $domain->Identifier;
            $domain->determineNameservers($domain->Identifier);
            if(isset($h_domainID) && 0 < $h_domainID) {
            } elseif(0 < $domain->HostingID) {
                $result_db = Database_Model::getInstance()->getOne("HostFact_Hosting", ["Domain"])->where("id", $domain->HostingID)->execute();
                if($result_db->Domain == "") {
                    if(ACCOUNT_GENERATION == 3 && substr($result_db->Username, 0, strlen(ACCOUNTCODE_PREFIX)) == ACCOUNTCODE_PREFIX && strlen($result_db->Username) == strlen(ACCOUNTCODE_PREFIX . ACCOUNTCODE_NUMBER)) {
                        require_once "class/hosting.php";
                        $hosting = new hosting();
                        $accountname = $hosting->generateNewAccountname(ACCOUNT_GENERATION, "", "", "", $result["Match"]);
                        $hosting->__destruct();
                        unset($hosting);
                    }
                    if(isset($accountname) && $accountname) {
                        Database_Model::getInstance()->update("HostFact_Hosting", ["Username" => $accountname, "Domain" => $result["Match"]])->where("id", $domain->HostingID)->execute();
                    } else {
                        Database_Model::getInstance()->update("HostFact_Hosting", ["Domain" => $result["Match"]])->where("id", $domain->HostingID)->execute();
                    }
                }
            }
            $this->Domain = $result["Match"];
            $this->Error = array_merge($this->Error, $domain->Error);
            $this->Reference = $this->DomainID;
            $domain->__destruct();
            unset($domain);
        } elseif($result["PeriodicType"] != "other") {
            global $additional_product_types;
            if(array_key_exists($result["PeriodicType"], $additional_product_types)) {
                global $_module_instances;
                $module = $_module_instances[$result["PeriodicType"]];
                if($module->service_add_from_invoice_line($result["ProductID"], $this->Identifier, $this->Debtor)) {
                    $this->PeriodicType = $result["PeriodicType"];
                    $this->Reference = $module->Identifier;
                }
            }
        }
        if($this->Reference) {
            Database_Model::getInstance()->update("HostFact_PeriodicElements", ["Reference" => $this->Reference, "PeriodicType" => $this->PeriodicType])->where("id", $this->Identifier)->execute();
        }
    }
    public function checkPricePeriod($use_custom_prices = false)
    {
        if(!$this->ProductCode) {
            return false;
        }
        if($use_custom_prices) {
            require_once "class/product.php";
            $product = new product();
            $product->ProductCode = $this->ProductCode;
            $product->show();
            if($product->HasCustomPrice == "period") {
                $price_array = $product->listCustomProductPrices();
                if(isset($price_array["period"][$this->Periods . "-" . $this->Periodic]["PriceExcl"])) {
                    $this->PriceExcl = $price_array["period"][$this->Periods . "-" . $this->Periodic]["PriceExcl"];
                    return true;
                }
                $this->PriceExcl = $price_array["period"]["default"]["PriceExcl"];
            }
        }
        $result = Database_Model::getInstance()->getOne("HostFact_Products", ["PricePeriod", "PriceExcl"])->where("ProductCode", $this->ProductCode)->execute();
        $PricePeriod = $result->PricePeriod;
        $PriceExcl = $result->PriceExcl;
        if($this->Periodic != $PricePeriod && $PriceExcl == $this->PriceExcl) {
            switch ($this->Periodic) {
                case "t":
                    if($PricePeriod == "j") {
                        $this->PriceExcl = $this->PriceExcl * 2;
                    } elseif($PricePeriod == "h") {
                        $this->PriceExcl = $this->PriceExcl * 4;
                    } elseif($PricePeriod == "k") {
                        $this->PriceExcl = $this->PriceExcl * 8;
                    } elseif($PricePeriod == "m") {
                        $this->PriceExcl = $this->PriceExcl * 24;
                    } elseif($PricePeriod == "w") {
                        $this->PriceExcl = $this->PriceExcl * 104;
                    } elseif($PricePeriod == "d") {
                        $this->PriceExcl = $this->PriceExcl * 365 * 2;
                    }
                    break;
                case "j":
                    if($PricePeriod == "t") {
                        $this->PriceExcl = $this->PriceExcl / 2;
                    } elseif($PricePeriod == "h") {
                        $this->PriceExcl = $this->PriceExcl * 2;
                    } elseif($PricePeriod == "k") {
                        $this->PriceExcl = $this->PriceExcl * 4;
                    } elseif($PricePeriod == "m") {
                        $this->PriceExcl = $this->PriceExcl * 12;
                    } elseif($PricePeriod == "w") {
                        $this->PriceExcl = $this->PriceExcl * 52;
                    } elseif($PricePeriod == "d") {
                        $this->PriceExcl = $this->PriceExcl * 365;
                    }
                    break;
                case "h":
                    if($PricePeriod == "t") {
                        $this->PriceExcl = $this->PriceExcl / 4;
                    } elseif($PricePeriod == "j") {
                        $this->PriceExcl = $this->PriceExcl / 2;
                    } elseif($PricePeriod == "k") {
                        $this->PriceExcl = $this->PriceExcl * 4 / 2;
                    } elseif($PricePeriod == "m") {
                        $this->PriceExcl = $this->PriceExcl * 12 / 2;
                    } elseif($PricePeriod == "w") {
                        $this->PriceExcl = $this->PriceExcl * 52 / 2;
                    } elseif($PricePeriod == "d") {
                        $this->PriceExcl = $this->PriceExcl * 365 / 2;
                    }
                    break;
                case "k":
                    if($PricePeriod == "t") {
                        $this->PriceExcl = $this->PriceExcl / 8;
                    } elseif($PricePeriod == "j") {
                        $this->PriceExcl = $this->PriceExcl / 4;
                    } elseif($PricePeriod == "h") {
                        $this->PriceExcl = $this->PriceExcl / 2;
                    } elseif($PricePeriod == "m") {
                        $this->PriceExcl = $this->PriceExcl * 12 / 4;
                    } elseif($PricePeriod == "w") {
                        $this->PriceExcl = $this->PriceExcl * 52 / 4;
                    } elseif($PricePeriod == "d") {
                        $this->PriceExcl = $this->PriceExcl * 365 / 4;
                    }
                    break;
                case "m":
                    if($PricePeriod == "t") {
                        $this->PriceExcl = $this->PriceExcl / 24;
                    } elseif($PricePeriod == "j") {
                        $this->PriceExcl = $this->PriceExcl / 12;
                    } elseif($PricePeriod == "h") {
                        $this->PriceExcl = $this->PriceExcl / 6;
                    } elseif($PricePeriod == "k") {
                        $this->PriceExcl = $this->PriceExcl / 3;
                    } elseif($PricePeriod == "w") {
                        $this->PriceExcl = $this->PriceExcl * 52 / 12;
                    } elseif($PricePeriod == "d") {
                        $this->PriceExcl = $this->PriceExcl * 365 / 12;
                    }
                    break;
                case "w":
                    if($PricePeriod == "t") {
                        $this->PriceExcl = $this->PriceExcl / 104;
                    } elseif($PricePeriod == "j") {
                        $this->PriceExcl = $this->PriceExcl / 52;
                    } elseif($PricePeriod == "h") {
                        $this->PriceExcl = $this->PriceExcl / 26;
                    } elseif($PricePeriod == "k") {
                        $this->PriceExcl = $this->PriceExcl * 4 / 52;
                    } elseif($PricePeriod == "m") {
                        $this->PriceExcl = $this->PriceExcl * 12 / 52;
                    } elseif($PricePeriod == "d") {
                        $this->PriceExcl = $this->PriceExcl * 365 / 52;
                    }
                    break;
                case "d":
                    if($PricePeriod == "t") {
                        $this->PriceExcl = $this->PriceExcl / 730;
                    } elseif($PricePeriod == "j") {
                        $this->PriceExcl = $this->PriceExcl / 365;
                    } elseif($PricePeriod == "h") {
                        $this->PriceExcl = $this->PriceExcl * 2 / 365;
                    } elseif($PricePeriod == "k") {
                        $this->PriceExcl = $this->PriceExcl * 4 / 365;
                    } elseif($PricePeriod == "m") {
                        $this->PriceExcl = $this->PriceExcl * 12 / 365;
                    } elseif($PricePeriod == "w") {
                        $this->PriceExcl = $this->PriceExcl / 7;
                    }
                    break;
            }
        }
        return true;
    }
    public function sqlNextDate($date)
    {
        $interval = $this->__getMySQLInterval($this->Periodic, $this->Periods);
        if(!is_numeric($date)) {
            $date = "DATE_ADD(`" . $date . "`, INTERVAL " . $interval . ")";
        } else {
            $date = "DATE_ADD('" . $date . "', INTERVAL " . $interval . ")";
        }
        return $date;
    }
    private function __getMySQLInterval($periodic, $periods)
    {
        $interval = "";
        $periods = (int) $periods;
        switch ($periodic) {
            case "d":
                $interval = $periods . " DAY";
                break;
            case "w":
                $interval = $periods * 7 . " DAY";
                break;
            case "m":
                $interval = $periods . " MONTH";
                break;
            case "k":
                $interval = $periods * 3 . " MONTH";
                break;
            case "h":
                $interval = $periods * 6 . " MONTH";
                break;
            case "j":
                $interval = $periods . " YEAR";
                break;
            case "t":
                $interval = $periods * 2 . " YEAR";
                break;
            default:
                return $interval;
        }
    }
    public function validate()
    {
        if(!is_numeric($this->Debtor)) {
            $this->Error[] = __("invalid debtor");
        }
        if(!(is_date($this->StartPeriod) || substr($this->StartPeriod, 0, 8) == "DATE_ADD")) {
            $this->Error[] = sprintf(__("invalid startperiod subscription"), stripReturnAndSubstring($this->Description, 120));
        }
        if(!(is_date($this->EndPeriod) || strlen($this->EndPeriod) === 0 || substr($this->EndPeriod, 0, 8) == "DATE_ADD")) {
            $this->Error[] = sprintf(__("invalid endperiod subscription"), stripReturnAndSubstring($this->Description, 120));
        }
        if(!(is_date($this->LastDate) || strlen($this->LastDate) === 0)) {
            $this->Error[] = sprintf(__("invalid lastdate subscription"), stripReturnAndSubstring($this->Description, 120));
        }
        if(!(is_date($this->NextDate) || strlen($this->NextDate) === 0 || substr($this->NextDate, 0, 8) == "DATE_ADD")) {
            $this->Error[] = sprintf(__("invalid nextdate subscription"), stripReturnAndSubstring($this->Description, 120));
        }
        if(!(is_string($this->Description) && strlen($this->Description) <= 21845 && 0 < strlen($this->Description))) {
            $this->Error[] = __("invalid description for subscription");
        }
        if(!is_numeric($this->PriceExcl)) {
            $this->Error[] = sprintf(__("invalid priceexcl subscription"), stripReturnAndSubstring($this->Description, 120));
        }
        if(!is_numeric($this->DiscountPercentage) || $this->DiscountPercentage < 0 || 1 < $this->DiscountPercentage) {
            $this->Error[] = __("invalid subscription line discountpercentage");
        }
        if($this->DiscountPercentage && 2 < strlen(substr(strrchr($this->DiscountPercentage * 100, "."), 1))) {
            $this->Error[] = __("invalid subscription line discountpercentage digits");
        }
        if(!is_numeric($this->Number) || 20 < strlen($this->NumberSuffix)) {
            $this->Error[] = sprintf(__("invalid number subscription"), $this->OldNumber);
        }
        global $array_periodes;
        if(!array_key_exists($this->Periodic, $array_periodes) || $this->Periodic == "") {
            $this->Error[] = __("invalid periodic");
        }
        if(!is_numeric($this->Periods)) {
            $this->Error[] = __("invalid period");
        }
        if(!$this->PeriodicType) {
            $this->PeriodicType = "other";
        }
        if(!$this->ContractPeriodic) {
            $this->ContractPeriodic = $this->Periodic;
            $this->ContractPeriods = $this->Periods;
        } else {
            if(!array_key_exists($this->ContractPeriodic, $array_periodes)) {
                $this->Error[] = __("invalid contract periodic");
            }
            if(!is_numeric($this->ContractPeriods)) {
                $this->Error[] = __("invalid contract period");
            }
        }
        return empty($this->Error) ? true : false;
    }
    public function all($fields, $sort = false, $order = false, $limit = "-1", $searchat = false, $searchfor = false, $group = false, $strict_search = false, $show_results = MAX_RESULTS_LIST)
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
        if($group !== false && is_array($group) && 0 < count($group)) {
            $filters = $group;
            if(array_key_exists("status", $group)) {
                $group = $group["status"];
                unset($filters["status"]);
            } else {
                $group = false;
            }
        }
        $fields[] = "DebtorCode";
        $DebtorArray = ["DebtorCode", "CompanyName", "SurName", "Initials", "DebtorInvoiceAuthorisation"];
        $DebtorFields = 0 < count(array_intersect($DebtorArray, $fields)) ? true : false;
        $ProductArray = ["ProductName", "ProductID"];
        $ProductFields = 0 < count(array_intersect($ProductArray, $fields)) ? true : false;
        $search_at = [];
        $DebtorSearch = $ProductSearch = false;
        if($searchat && (0 < strlen($searchfor) || $searchfor)) {
            $search_at = explode("|", $searchat);
            $DebtorSearch = 0 < count(array_intersect($DebtorArray, $search_at)) ? true : false;
            $ProductSearch = 0 < count(array_intersect($ProductArray, $search_at)) ? true : false;
        }
        $line_amount = [];
        $mysql_offset_helper = " + IF(HostFact_PeriodicElements.`Number` * HostFact_PeriodicElements.`Periods` * HostFact_PeriodicElements.`PriceExcl` >= 0, 0.000001, -0.000001)";
        if(defined("VAT_CALC_METHOD") && VAT_CALC_METHOD == "incl") {
            $line_amount["incl"] = "(ROUND(HostFact_PeriodicElements.`Number` * HostFact_PeriodicElements.`Periods` * HostFact_PeriodicElements.`PriceExcl` * (1+HostFact_PeriodicElements.`TaxPercentage`)" . $mysql_offset_helper . ",2) - ROUND(HostFact_PeriodicElements.`Number` * HostFact_PeriodicElements.`Periods` * HostFact_PeriodicElements.`PriceExcl` * ROUND(HostFact_PeriodicElements.`DiscountPercentage`,4) * (1+HostFact_PeriodicElements.`TaxPercentage`)" . $mysql_offset_helper . ",2))";
            $line_amount["excl"] = "ROUND(" . $line_amount["incl"] . " / ROUND((1+HostFact_PeriodicElements.`TaxPercentage`),4),2)";
        } else {
            $line_amount["excl"] = "(ROUND(HostFact_PeriodicElements.`Number` * HostFact_PeriodicElements.`Periods` * HostFact_PeriodicElements.`PriceExcl`" . $mysql_offset_helper . ",2) - ROUND(HostFact_PeriodicElements.`Number` * HostFact_PeriodicElements.`Periods` * HostFact_PeriodicElements.`PriceExcl` * ROUND(HostFact_PeriodicElements.`DiscountPercentage`,4)" . $mysql_offset_helper . ",2))";
            $line_amount["incl"] = "ROUND(" . $line_amount["excl"] . " * ROUND((1+HostFact_PeriodicElements.`TaxPercentage`),4),2)";
        }
        $select = ["HostFact_PeriodicElements.id", "HostFact_PeriodicElements.`NextDate` AS OriginalNextDate"];
        foreach ($fields as $column) {
            if($column == "AmountExcl") {
                $select[] = $line_amount["excl"] . " as `AmountExcl`";
            } elseif($column == "AmountIncl") {
                $select[] = $line_amount["incl"] . " as `AmountIncl`";
            } elseif($column == "NextDate") {
                $select[] = "(SELECT CASE HostFact_Debtors.`InvoiceCollect` \n\t\t\t\t\t\t\t\tWHEN '2' THEN CONCAT(DATE_FORMAT(HostFact_PeriodicElements.`NextDate`, '%Y-%m-'),IF(DAYOFMONTH(HostFact_PeriodicElements.`NextDate`) >= 15, '15', '01')) \n\t\t\t\t\t\t\t\tWHEN '1' THEN CONCAT(DATE_FORMAT(HostFact_PeriodicElements.`NextDate`, '%Y-%m-'),'01') \n\t\t\t\t\t\t\t\tWHEN '-1' THEN " . (INVOICE_COLLECT_ENABLED == "yes" ? "CONCAT(DATE_FORMAT(HostFact_PeriodicElements.`NextDate`, '%Y-%m-'),IF(DAYOFMONTH(HostFact_PeriodicElements.`NextDate`) >= 15, '" . (INVOICE_COLLECT_TPM == 1 ? "01" : "15") . "', '01'))" : "HostFact_PeriodicElements.`NextDate`") . " \n\t\t\t\t\t\t\t\tELSE HostFact_PeriodicElements.`NextDate` \n\t\t\t\t\t\t\t  END) as NextDate";
            } elseif(in_array($column, $DebtorArray)) {
                if($column == "DebtorInvoiceAuthorisation") {
                    $select[] = "HostFact_Debtors.`InvoiceAuthorisation` as DebtorInvoiceAuthorisation";
                } else {
                    $select[] = "HostFact_Debtors.`" . $column . "`";
                }
            } elseif(in_array($column, $ProductArray)) {
                if($column == "ProductID") {
                    $select[] = "HostFact_Products.`id` AS `ProductID`";
                } else {
                    $select[] = "HostFact_Products.`" . $column . "`";
                }
            } else {
                $select[] = "HostFact_PeriodicElements.`" . $column . "`";
            }
            if($column == "PriceExcl") {
                $select[] = "HostFact_PeriodicElements.`DiscountPercentage`";
            }
        }
        Database_Model::getInstance()->get("HostFact_PeriodicElements", $select);
        if($DebtorFields || $DebtorSearch) {
            Database_Model::getInstance()->join("HostFact_Debtors", "HostFact_Debtors.`id` = HostFact_PeriodicElements.`Debtor`");
        }
        if($ProductFields || $ProductSearch) {
            Database_Model::getInstance()->join("HostFact_Products", "HostFact_Products.`ProductCode` = HostFact_PeriodicElements.`ProductCode`");
        }
        if(!empty($search_at)) {
            $or_clausule = [];
            foreach ($search_at as $searchColumn) {
                if(in_array($searchColumn, ["Debtor"])) {
                    $or_clausule[] = ["HostFact_PeriodicElements.`" . $searchColumn . "`", $searchfor];
                } elseif(in_array($searchColumn, ["id_in_array"])) {
                    $or_clausule[] = ["HostFact_PeriodicElements.`id`", ["IN" => explode(",", $searchfor)]];
                } elseif(in_array($searchColumn, $DebtorArray)) {
                    $or_clausule[] = ["HostFact_Debtors.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                } elseif(in_array($searchColumn, $ProductArray)) {
                    $or_clausule[] = ["HostFact_Products.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                } elseif($strict_search === true) {
                    $or_clausule[] = ["HostFact_PeriodicElements.`" . $searchColumn . "`", $searchfor];
                } else {
                    $or_clausule[] = ["HostFact_PeriodicElements.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                }
            }
            Database_Model::getInstance()->orWhere($or_clausule);
        }
        $order = $sort ? $order ? $order : "DESC" : "";
        if($sort == "Debtor") {
            Database_Model::getInstance()->orderBy("CONCAT(HostFact_Debtors.`CompanyName`, HostFact_Debtors.`SurName`)", $order);
        } elseif(in_array($sort, $DebtorArray)) {
            Database_Model::getInstance()->orderBy("HostFact_Debtors.`" . $sort . "`", $order);
        } elseif(in_array($sort, $ProductArray)) {
            Database_Model::getInstance()->orderBy("HostFact_Products.`" . $sort . "`", $order);
        } elseif($sort == "AmountExcl" || $sort == "PriceExcl") {
            Database_Model::getInstance()->orderBy("(HostFact_PeriodicElements.`Number` * HostFact_PeriodicElements.`Periods` * HostFact_PeriodicElements.`PriceExcl`*ROUND((1-HostFact_PeriodicElements.`DiscountPercentage`),4))", $order);
        } elseif($sort == "AmountIncl" || $sort == "PriceIncl") {
            Database_Model::getInstance()->orderBy("(HostFact_PeriodicElements.`Number` * HostFact_PeriodicElements.`Periods` * HostFact_PeriodicElements.`PriceExcl` * ROUND((1-HostFact_PeriodicElements.`DiscountPercentage`),4) * (1+HostFact_PeriodicElements.`TaxPercentage`))", $order);
        } elseif($sort == "NextDate") {
            Database_Model::getInstance()->orderBy("(SELECT CASE HostFact_Debtors.InvoiceCollect \n\t\t\t\t\t\t\t\tWHEN '2' THEN CONCAT(DATE_FORMAT(HostFact_PeriodicElements.`NextDate`, '%Y-%m-'),IF(DAYOFMONTH(HostFact_PeriodicElements.`NextDate`) >= 15, '15', '01')) \n\t\t\t\t\t\t\t\tWHEN '1' THEN CONCAT(DATE_FORMAT(HostFact_PeriodicElements.`NextDate`, '%Y-%m-'),'01') \n\t\t\t\t\t\t\t\tWHEN '-1' THEN " . (INVOICE_COLLECT_ENABLED == "yes" ? "CONCAT(DATE_FORMAT(HostFact_PeriodicElements.`NextDate`, '%Y-%m-'),IF(DAYOFMONTH(HostFact_PeriodicElements.`NextDate`) >= 15, '" . (INVOICE_COLLECT_TPM == 1 ? "01" : "15") . "', '01'))" : "HostFact_PeriodicElements.`NextDate`") . " ELSE HostFact_PeriodicElements.`NextDate` END)", $order)->orderBy("IF(HostFact_PeriodicElements.`AutoRenew`='no',1,0)", "ASC")->orderBy("HostFact_PeriodicElements.Debtor", "ASC")->orderBy("HostFact_PeriodicElements.id", "ASC");
        } elseif($sort) {
            Database_Model::getInstance()->orderBy("HostFact_PeriodicElements." . $sort, $order);
        } else {
            Database_Model::getInstance()->orderBy("HostFact_PeriodicElements.`id`", $order);
        }
        if(0 <= $limit) {
            Database_Model::getInstance()->limit((max(1, $limit) - 1) * $show_results, $show_results);
        }
        if($group == "activeterminated") {
            Database_Model::getInstance()->where("HostFact_PeriodicElements.Status", ["<" => "8"])->where("HostFact_PeriodicElements.TerminationDate", [">" => "0000-00-00"])->where("HostFact_PeriodicElements.TerminationDate", [">" => ["RAW" => "HostFact_PeriodicElements.`StartPeriod`"]]);
        } elseif($group == "terminated") {
            Database_Model::getInstance()->orWhere([["HostFact_PeriodicElements.Status", "8"], ["AND" => [["HostFact_PeriodicElements.Status", ["<" => "8"]], ["HostFact_PeriodicElements.TerminationDate", [">" => "0000-00-00"]], ["HostFact_PeriodicElements.TerminationDate", ["<=" => ["RAW" => "HostFact_PeriodicElements.`StartPeriod`"]]]]]]);
        } elseif($group == "nextdatepassed") {
            $compare_with = isset($this->NextDatePassedDate) && is_date($this->NextDatePassedDate) ? $this->NextDatePassedDate : ["RAW" => "CURDATE()"];
            Database_Model::getInstance()->where("HostFact_PeriodicElements.Status", ["<" => "8"])->orWhere([["HostFact_PeriodicElements.`TerminationDate`", "0000-00-00"], ["HostFact_PeriodicElements.`TerminationDate`", [">" => ["RAW" => "HostFact_PeriodicElements.`StartPeriod`"]]]])->where("(SELECT CASE HostFact_Debtors.`InvoiceCollect` \n\t\t\t\t\t\t\tWHEN '2' THEN CONCAT(DATE_FORMAT(HostFact_PeriodicElements.`NextDate`, '%Y-%m-'),IF(DAYOFMONTH(HostFact_PeriodicElements.`NextDate`) >= 15, '15', '01')) \n\t\t\t\t\t\t\tWHEN '1' THEN CONCAT(DATE_FORMAT(HostFact_PeriodicElements.`NextDate`, '%Y-%m-'),'01') \n\t\t\t\t\t\t\tWHEN '-1' THEN " . (INVOICE_COLLECT_ENABLED == "yes" ? "CONCAT(DATE_FORMAT(HostFact_PeriodicElements.`NextDate`, '%Y-%m-'),IF(DAYOFMONTH(HostFact_PeriodicElements.`NextDate`) >= 15, '" . (INVOICE_COLLECT_TPM == 1 ? "01" : "15") . "', '01'))" : "HostFact_PeriodicElements.`NextDate`") . " \n\t\t\t\t\t\t\tELSE HostFact_PeriodicElements.`NextDate` \n\t\t\t\t\t\tEND)", ["<=" => $compare_with]);
        } elseif($group == "autorenew_no") {
            Database_Model::getInstance()->where("HostFact_PeriodicElements.Status", ["<" => "8"])->orWhere([["HostFact_PeriodicElements.`TerminationDate`", "0000-00-00"], ["HostFact_PeriodicElements.`TerminationDate`", [">" => ["RAW" => "HostFact_PeriodicElements.`StartPeriod`"]]]])->where("HostFact_PeriodicElements.AutoRenew", "no");
        } elseif($group == "active") {
            Database_Model::getInstance()->where("HostFact_PeriodicElements.Status", ["<" => "8"])->orWhere([["HostFact_PeriodicElements.`TerminationDate`", "0000-00-00"], ["HostFact_PeriodicElements.`TerminationDate`", [">" => ["RAW" => "HostFact_PeriodicElements.`StartPeriod`"]]]]);
        } elseif(is_string($group) && 1 < count(explode("|", $group))) {
            $group = explode("|", $group);
            $or_clausule = [];
            foreach ($group as $status) {
                $or_clausule[] = ["HostFact_PeriodicElements.`Status`", $status];
            }
            Database_Model::getInstance()->orWhere($or_clausule);
        } elseif(is_numeric($group)) {
            Database_Model::getInstance()->where("HostFact_PeriodicElements.`Status`", $group);
        } else {
            Database_Model::getInstance()->where("HostFact_PeriodicElements.`Status`", ["<" => 8]);
        }
        if(isset($this->OtherServicesOnly) && $this->OtherServicesOnly) {
            Database_Model::getInstance()->where("HostFact_PeriodicElements.PeriodicType", "other");
        } elseif(isset($this->restrictedAll) && is_array($this->restrictedAll)) {
            Database_Model::getInstance()->where("HostFact_PeriodicElements.PeriodicType", ["NOT IN" => $this->restrictedAll]);
        } elseif(isset($this->CustomServicesOnly) && $this->CustomServicesOnly) {
            Database_Model::getInstance()->where("HostFact_PeriodicElements.PeriodicType", $this->CustomServicesOnly);
        }
        Database_Model::getInstance()->groupBy("HostFact_PeriodicElements.id");
        if(!empty($filters)) {
            foreach ($filters as $_db_field => $_db_value) {
                switch ($_db_field) {
                    case "debtor":
                        if(is_numeric($_db_value) && 0 < $_db_value) {
                            Database_Model::getInstance()->where("HostFact_PeriodicElements.Debtor", $_db_value);
                        }
                        break;
                    case "created":
                        if(isset($_db_value["from"]) && $_db_value["from"]) {
                            Database_Model::getInstance()->where("HostFact_PeriodicElements.Created", [">=" => $_db_value["from"]]);
                        }
                        if(isset($_db_value["to"]) && $_db_value["to"]) {
                            Database_Model::getInstance()->where("HostFact_PeriodicElements.Created", ["<=" => $_db_value["to"]]);
                        }
                        break;
                    case "modified":
                        if(isset($_db_value["from"]) && $_db_value["from"]) {
                            static::getInstance()->where("HostFact_PeriodicElements.Modified", [">=" => $_db_value["from"]]);
                        }
                        if(isset($_db_value["to"]) && $_db_value["to"]) {
                            Database_Model::getInstance()->where("HostFact_PeriodicElements.Modified", ["<=" => $_db_value["to"]]);
                        }
                        break;
                }
            }
        }
        $list = [];
        $list["TotalAmountIncl"] = 0;
        $list["TotalAmountExcl"] = 0;
        $list["CountRows"] = 0;
        $this->CountRows = 0;
        if($subscription_list = Database_Model::getInstance()->execute()) {
            $list["CountRows"] = $this->CountRows = Database_Model::getInstance()->rowCount("HostFact_PeriodicElements", "HostFact_PeriodicElements.id");
            foreach ($subscription_list as $result) {
                $list[$result->id] = ["id" => $result->id];
                foreach ($fields as $column) {
                    if(in_array($column, ["StartPeriod", "EndPeriod", "LastDate", "NextDate", "TerminationDate", "StartContract", "EndContract", "ContractRenewalDate"])) {
                        $list[$result->id][$column] = rewrite_date_db2site($result->{$column}) ? rewrite_date_db2site($result->{$column}) : "";
                    } else {
                        $list[$result->id][$column] = htmlspecialchars($result->{$column});
                    }
                }
                $list[$result->id]["OriginalNextDate"] = $result->OriginalNextDate;
                if(isset($list[$result->id]["Description"])) {
                    $pattern = "/\\[[A-Z]\\:(.*?)\\]/is";
                    $replacements = "";
                    $list[$result->id]["Description"] = preg_replace($pattern, $replacements, $list[$result->id]["Description"]);
                }
                if(isset($result->PriceExcl)) {
                    $list[$result->id]["PriceIncl"] = $list[$result->id]["PriceExcl"] * (1 + $result->TaxPercentage);
                    $list[$result->id]["PriceIncl"] = VAT_CALC_METHOD == "incl" ? round((double) $list[$result->id]["PriceIncl"], 5) : $list[$result->id]["PriceIncl"];
                    $list[$result->id]["AmountExclPerPeriod"] = $result->Number * $list[$result->id]["PriceExcl"] * round(1 - $result->DiscountPercentage, 8);
                    $list[$result->id]["AmountInclPerPeriod"] = $result->Number * $list[$result->id]["PriceIncl"] * round(1 - $result->DiscountPercentage, 8);
                    $list[$result->id]["AmountIncl"] = VAT_CALC_METHOD == "incl" ? round($result->Periods * $list[$result->id]["AmountInclPerPeriod"], 2) : round(round($result->Periods * $list[$result->id]["AmountExclPerPeriod"], 2) * (1 + $result->TaxPercentage), 2);
                    $list[$result->id]["AmountExcl"] = round($result->Periods * $list[$result->id]["AmountExclPerPeriod"], 2);
                    $list["TotalAmountIncl"] += $list[$result->id]["AmountIncl"];
                    $list["TotalAmountExcl"] += $list[$result->id]["AmountExcl"];
                }
            }
        }
        return $list;
    }
    public function makeinvoice2($invoices = [], $force_future = false)
    {
        $debtor_id = NULL;
        foreach ($invoices as $key => $value) {
            if(substr($key, 0, 6) == "noinc-") {
                $debtor_id = substr($key, 6);
                $noinc = true;
            } else {
                $debtor_id = $key;
                $noinc = false;
            }
            require_once "class/invoice.php";
            $invoice = new invoice();
            require_once "class/debtor.php";
            $debtor = new debtor();
            $debtor->Identifier = $debtor_id;
            if(!$debtor->show()) {
            } else {
                $fields = ["InvoiceCode", "Authorisation", "Paid", "SubStatus", "SentDate"];
                $invoicelist = $invoice->all($fields, "id", "DESC", "", "Debtor", $debtor_id, "0");
                foreach ($invoicelist as $invoiceId => $value) {
                    if(is_numeric($invoiceId) && (!isEmptyFloat($value["Paid"]) || $value["SubStatus"] == "BLOCKED")) {
                        unset($invoicelist[$invoiceId]);
                        $invoicelist["CountRows"] -= 1;
                    } elseif(is_numeric($invoiceId) && SUBSCRIPTIONS_EXTEND_SCHEDULED == "no" && $value["SentDate"] != "0000-00-00 00:00:00") {
                        unset($invoicelist[$invoiceId]);
                        $invoicelist["CountRows"] -= 1;
                    }
                }
                foreach ($debtor as $k => $v) {
                    if(is_string($v) && in_array($k, $debtor->Variables)) {
                        $debtor->{$k} = htmlspecialchars_decode($debtor->{$k});
                    }
                }
                if($noinc === true) {
                    $noinc_found = false;
                    foreach ($invoicelist as $k2 => $v2) {
                        if(is_numeric($k2) && $v2["Authorisation"] == "no") {
                            unset($invoicelist);
                            $invoicelist["CountRows"] = 1;
                            $invoicelist[$k2] = $v2;
                            $noinc_found = true;
                            if($noinc_found === false) {
                                unset($invoicelist);
                                $invoicelist["CountRows"] = 0;
                            }
                        }
                    }
                } elseif(1 <= $invoicelist["CountRows"]) {
                    $found = false;
                    foreach ($invoicelist as $k2 => $v2) {
                        if(is_numeric($k2) && $v2["Authorisation"] == $debtor->InvoiceAuthorisation) {
                            unset($invoicelist);
                            $invoicelist["CountRows"] = 1;
                            $invoicelist[$k2] = $v2;
                            $found = true;
                            if($found === false) {
                                unset($invoicelist);
                                $invoicelist["CountRows"] = 0;
                            }
                        }
                    }
                }
                if((int) $invoicelist["CountRows"] === 0) {
                    $newInvoiceCode = $invoice->newInvoiceCode();
                    $invoice->InvoiceCode = $newInvoiceCode;
                    $invoice->Debtor = $debtor->Identifier;
                    if((is_string($debtor->InvoiceTerm) && $debtor->InvoiceTerm != "-1" || is_int($debtor->InvoiceTerm) && $debtor->InvoiceTerm != -1) && is_numeric($debtor->InvoiceTerm) && 0 <= intval($debtor->InvoiceTerm)) {
                        $invoice->Term = $debtor->InvoiceTerm;
                    }
                    $invoice->CompanyName = $debtor->InvoiceCompanyName ? $debtor->InvoiceCompanyName : $debtor->CompanyName;
                    $invoice->TaxNumber = $debtor->TaxNumber;
                    $invoice->Sex = $debtor->InvoiceSurName ? $debtor->InvoiceSex : $debtor->Sex;
                    $invoice->Initials = $debtor->InvoiceInitials ? $debtor->InvoiceInitials : $debtor->Initials;
                    $invoice->SurName = $debtor->InvoiceSurName ? $debtor->InvoiceSurName : $debtor->SurName;
                    $invoice->Address = $debtor->InvoiceAddress ? $debtor->InvoiceAddress : $debtor->Address;
                    $invoice->ZipCode = $debtor->InvoiceZipCode ? $debtor->InvoiceZipCode : $debtor->ZipCode;
                    $invoice->City = $debtor->InvoiceCity ? $debtor->InvoiceCity : $debtor->City;
                    $invoice->Country = $debtor->InvoiceCountry && $debtor->InvoiceAddress ? $debtor->InvoiceCountry : $debtor->Country;
                    $invoice->EmailAddress = check_email_address($debtor->InvoiceEmailAddress ? $debtor->InvoiceEmailAddress : $debtor->EmailAddress, "convert");
                    $invoice->Authorisation = $noinc === true ? "no" : $debtor->InvoiceAuthorisation;
                    $invoice->InvoiceMethod = $debtor->InvoiceMethod;
                    if(0 < $debtor->InvoiceTemplate) {
                        $invoice->Template = $debtor->InvoiceTemplate;
                    }
                    $invoice->Status = ORDERACCEPT_STATUS;
                    $invoice->PeriodicInvoice = true;
                    if((int) $invoice->Status === 0) {
                        $invoice->InvoiceCode = $invoice->newConceptCode();
                    }
                } else {
                    foreach ($invoicelist as $k => $v) {
                        if(is_numeric($k)) {
                            if($v["SentDate"] == "0000-00-00 00:00:00") {
                                $invoice->Identifier = $k;
                                if(!$invoice->show()) {
                                    $this->Error[] = __("error while getting concept invoice for subscriptions");
                                }
                            } elseif($invoice->Identifier <= 0) {
                                $invoice->Identifier = $k;
                            }
                        }
                    }
                }
                $this_invoice_error = [];
                foreach ($invoices[$key] as $key2 => $value2) {
                    if(is_numeric($key2)) {
                        if(!isset($value2["InvoiceCollect"])) {
                            $do_while_next_check = date("Ymd");
                        } else {
                            $do_while_next_check = str_replace("-", "", $value2["InvoiceCollect"]);
                        }
                        $element = new invoiceelement();
                        $element->VatCalcMethod = $invoice->VatCalcMethod;
                        $this->Identifier = $key2;
                        $this->show();
                        if(!$this->StartPeriod || !$this->EndPeriod || !$this->Periods || !$this->Periodic) {
                            $this->Error[] = sprintf(__("subscription invoicing add new invoiceelement failure"), stripReturnAndSubstring($this->Description, 120));
                            if(0 < $invoice->Identifier) {
                                foreach ($invoice->Variables as $k) {
                                    if(is_string($k)) {
                                        $invoice->{$k} = htmlspecialchars_decode($invoice->{$k});
                                    }
                                }
                                $invoice->edit();
                                $invoice_made = false;
                            } else {
                                $invoice->add();
                                $invoice_made = true;
                            }
                            if(!empty($invoice->Error) || !empty($this_invoice_error)) {
                                $this->Error[] = sprintf(__("periodic failure by invoice"), $debtor->DebtorCode);
                                if(!empty($invoice->Error)) {
                                    $this->Error = array_merge($this->Error, $invoice->Error);
                                }
                                if(!empty($this_invoice_error)) {
                                    $this->Error = array_merge($this->Error, $this_invoice_error);
                                }
                                foreach ($invoices[$key] as $key2 => $value2) {
                                    $this->Identifier = $key2;
                                    $this->show();
                                    $this->LastDate = rewrite_date_site2db($invoices[$key][$key2]["LastDate"]);
                                    $this->NextDate = rewrite_date_site2db($invoices[$key][$key2]["NextDate"]);
                                    $this->StartPeriod = rewrite_date_site2db($invoices[$key][$key2]["StartPeriod"]);
                                    $this->EndPeriod = rewrite_date_site2db($invoices[$key][$key2]["EndPeriod"]);
                                    $this->AutoRenew = $this->AutoRenew == "no" ? "once" : $this->AutoRenew;
                                    Database_Model::getInstance()->update("HostFact_PeriodicElements", ["LastDate" => substr($this->LastDate, 0, 4) != "DATE" ? $this->LastDate : ["RAW" => $this->LastDate], "NextDate" => substr($this->NextDate, 0, 4) != "DATE" ? $this->NextDate : ["RAW" => $this->NextDate], "StartPeriod" => substr($this->StartPeriod, 0, 4) != "DATE" ? $this->StartPeriod : ["RAW" => $this->StartPeriod], "EndPeriod" => substr($this->EndPeriod, 0, 4) != "DATE" ? $this->EndPeriod : ["RAW" => $this->EndPeriod], "AutoRenew" => $this->AutoRenew])->where("id", $this->Identifier)->execute();
                                    if(!empty($value2["InvoiceElements"])) {
                                        Database_Model::getInstance()->delete("HostFact_InvoiceElements")->where("id", ["IN" => $value2["InvoiceElements"]])->execute();
                                        $lowest_added_element_id = min($value2["InvoiceElements"]);
                                        Database_Model::getInstance()->delete("HostFact_InvoiceElements")->where("id", [">" => $lowest_added_element_id])->where("InvoiceCode", $invoice->InvoiceCode)->execute();
                                    }
                                    if(empty($invoice->Error) && $invoice_made) {
                                        Database_Model::getInstance()->delete("HostFact_Invoice")->where("id", $invoice->Identifier)->execute();
                                        Database_Model::getInstance()->delete("HostFact_InvoiceElements")->where("InvoiceCode", $invoice->InvoiceCode)->execute();
                                    }
                                }
                            } else {
                                foreach ($invoices[$key] as $key2 => $value2) {
                                    $this->Identifier = $key2;
                                    $this->show();
                                    $call_hook_reference_id = $this->PeriodicType == "other" ? $this->Identifier : $this->Reference;
                                    $service_info = ["Type" => $this->PeriodicType, "id" => $call_hook_reference_id, "Debtor" => $this->Debtor];
                                    do_action("service_is_invoiced", $service_info);
                                }
                            }
                        } else {
                            $z = 0;
                            if($force_future && $do_while_next_check < substr(rewrite_date_site2db($this->NextDate), 0, 8)) {
                                $do_while_next_check = substr(rewrite_date_site2db($this->NextDate), 0, 8);
                            }
                            while (substr(rewrite_date_site2db($this->NextDate), 0, 8) <= $do_while_next_check && $z < 32) {
                                if(8 <= $this->Status || $this->TerminationDate && (substr(rewrite_date_site2db($this->TerminationDate), 0, 8) < date("Ymd") || substr(rewrite_date_site2db($this->TerminationDate), 0, 8) <= substr(rewrite_date_site2db($this->StartPeriod), 0, 8))) {
                                    break;
                                }
                                foreach ($this as $k => $v) {
                                    if(is_string($v) && in_array($k, $this->Variables)) {
                                        $this->{$k} = htmlspecialchars_decode($this->{$k});
                                    }
                                }
                                $element->InvoiceCode = $invoice->InvoiceCode;
                                $element->Debtor = $invoice->Debtor;
                                $element->Date = rewrite_date_db2site(date("Y-m-d"));
                                $element->Number = $this->Number;
                                $element->NumberSuffix = $this->NumberSuffix;
                                $element->ProductCode = $this->ProductCode;
                                $element->Description = $this->Description;
                                $element->PriceExcl = $this->PriceExcl;
                                $element->TaxPercentage = $this->TaxPercentage;
                                $element->DiscountPercentage = $this->DiscountPercentage * 100;
                                if(0 < $this->DiscountPercentage) {
                                    $element->DiscountPercentageType = "subscription";
                                }
                                $element->Periods = $this->Periods;
                                $element->Periodic = $this->Periodic;
                                $element->PeriodicID = $this->Identifier;
                                $element->StartPeriod = $this->StartPeriod;
                                $element->EndPeriod = $this->EndPeriod;
                                $element->Ordering = isset($invoice->Elements["CountRows"]) ? $invoice->Elements["CountRows"] : 0;
                                $element->ProductType = $this->PeriodicType != "other" && 0 < $this->Reference ? $this->PeriodicType : "";
                                $element->Reference = $this->PeriodicType != "other" && 0 < $this->Reference ? $this->Reference : "";
                                $add_result = $element->add();
                                $invoices[$key][$key2]["LastDate"] = $this->LastDate;
                                $invoices[$key][$key2]["NextDate"] = $this->NextDate;
                                $invoices[$key][$key2]["StartPeriod"] = $this->StartPeriod;
                                $invoices[$key][$key2]["EndPeriod"] = $this->EndPeriod;
                                if($add_result === true && 0 < $element->Identifier && is_numeric($element->Identifier)) {
                                    $invoices[$key][$key2]["InvoiceElements"][] = $element->Identifier;
                                }
                                $this->LastDate = $this->OriginalNextDate;
                                $this->NextDate = "";
                                $this->StartPeriod = $this->EndPeriod;
                                $this->EndPeriod = "";
                                if($this->PeriodicType == "domain" && $this->Reference) {
                                    require_once "class/domain.php";
                                    $domain = new domain();
                                    $domain->Identifier = $this->Reference;
                                    if(!$domain->getRegistrar() || !is_object($domain->api) || !method_exists($domain->api, "getSyncData") || DOMAIN_SYNC != "yes" || DOMAIN_SYNC_EXPDATE != "yes") {
                                        $domain->extend($this->StartPeriod);
                                    }
                                }
                                if(count($element->Error) === 0) {
                                    $this->StartPeriod = rewrite_date_site2db($this->StartPeriod);
                                    $m0 = substr($this->StartPeriod, 4, 2);
                                    $d0 = substr($this->StartPeriod, 6, 2);
                                    $y0 = substr($this->StartPeriod, 0, 4);
                                    $this->EndPeriod = $this->sqlNextDate($this->StartPeriod);
                                    $this->LastDate = rewrite_date_site2db($this->LastDate);
                                    $PeriodicInvoiceDays = $this->getPeriodicInvoiceDays($invoice->Debtor);
                                    if((int) $PeriodicInvoiceDays !== 0) {
                                        $this->NextDate = date("Ymd", strtotime("-" . $PeriodicInvoiceDays . " days", mktime(0, 0, 0, $m0, $d0, $y0)));
                                    } else {
                                        $this->NextDate = $this->sqlNextDate($this->LastDate);
                                    }
                                    $this->AutoRenew = $this->AutoRenew == "once" ? "no" : $this->AutoRenew;
                                    $result = Database_Model::getInstance()->update("HostFact_PeriodicElements", ["LastDate" => substr($this->LastDate, 0, 4) != "DATE" ? $this->LastDate : ["RAW" => $this->LastDate], "NextDate" => substr($this->NextDate, 0, 4) != "DATE" ? $this->NextDate : ["RAW" => $this->NextDate], "StartPeriod" => substr($this->StartPeriod, 0, 4) != "DATE" ? $this->StartPeriod : ["RAW" => $this->StartPeriod], "EndPeriod" => substr($this->EndPeriod, 0, 4) != "DATE" ? $this->EndPeriod : ["RAW" => $this->EndPeriod], "AutoRenew" => $this->AutoRenew])->where("id", $this->Identifier)->execute();
                                    if($result) {
                                        $this->Success[] = sprintf(__("subscription invoiced"), stripReturnAndSubstring($this->Description, 120), $invoice->InvoiceCode);
                                        createMessageLog("success", "subscription invoiced", [stripReturnAndSubstring($this->Description, 120), $invoice->InvoiceCode], "subscription", $this->Identifier);
                                    } else {
                                        $this_invoice_error[] = sprintf(__("subscription invoicing newdate determination failure"), stripReturnAndSubstring($this->Description, 120));
                                    }
                                } else {
                                    $this_invoice_error = array_merge($this_invoice_error, $element->Error);
                                    $this->Error[] = sprintf(__("subscription invoicing add new invoiceelement failure"), stripReturnAndSubstring($this->Description, 120));
                                }
                                $this->Identifier = $key2;
                                $this->show();
                                if($force_future) {
                                    $this->NextDate = $this->OriginalNextDate;
                                }
                                $z++;
                                if($this->AutoRenew == "once" || $this->AutoRenew == "no") {
                                }
                            }
                            unset($element);
                        }
                    }
                }
            }
        }
        delete_stats_summary();
    }
    public function makeinvoice()
    {
        $fields = ["Debtor", "Periodic", "NextDate", "StartPeriod", "EndPeriod", "TerminationDate", "Status", "InvoiceAuthorisation", "DebtorInvoiceAuthorisation", "AutoRenew"];
        $periodics = $this->all($fields, "id", "ASC", -1, false, false, "nextdatepassed");
        $invoices = [];
        foreach ($periodics as $key => $value) {
            if(is_numeric($key)) {
                if(!$value["NextDate"] || !$value["StartPeriod"] || !$value["EndPeriod"]) {
                } elseif(substr(rewrite_date_site2db($value["NextDate"]), 0, 8) <= date("Ymd") && (date("Ymd") <= substr(rewrite_date_site2db($value["TerminationDate"]), 0, 8) && substr(rewrite_date_site2db($value["StartPeriod"]), 0, 8) < substr(rewrite_date_site2db($value["TerminationDate"]), 0, 8) || $value["TerminationDate"] == "") && $value["Status"] < 8) {
                    if($value["AutoRenew"] == "no") {
                    } else {
                        require_once "class/debtor.php";
                        $debtor = new debtor();
                        $debtor->Identifier = $value["Debtor"];
                        $debtor->show();
                        if($debtor->Status == 9) {
                            Database_Model::getInstance()->update("HostFact_PeriodicElements", ["Status" => "8"])->where("id", $value["id"])->execute();
                        } elseif($value["DebtorInvoiceAuthorisation"] == "yes" && $value["InvoiceAuthorisation"] == "no") {
                            $invoices["noinc-" . $value["Debtor"]][$value["id"]] = $value;
                        } else {
                            $invoices[$value["Debtor"]][$value["id"]] = $value;
                        }
                    }
                } elseif(substr(rewrite_date_site2db($value["NextDate"]), 0, 8) <= date("Ymd") && (substr(rewrite_date_site2db($value["TerminationDate"]), 0, 8) < date("Ymd") || substr(rewrite_date_site2db($value["TerminationDate"]), 0, 8) <= substr(rewrite_date_site2db($value["StartPeriod"]), 0, 8)) && $value["Status"] < 8) {
                    Database_Model::getInstance()->update("HostFact_PeriodicElements", ["Status" => "8"])->where("id", $value["id"])->execute();
                }
            }
        }
        $this->makeinvoice2($invoices);
        Database_Model::getInstance()->update("HostFact_PeriodicElements", ["Status" => "8"])->where("Status", ["<" => "8"])->where("TerminationDate", ["!=" => "0000-00-00"])->where("TerminationDate", ["<=" => ["RAW" => "StartPeriod"]])->where("StartPeriod", ["<=" => ["RAW" => "CURDATE()"]])->execute();
    }
    public function makeinvoice_manual($batch_ids)
    {
        $fields = ["Description", "Debtor", "CompanyName", "SurName", "Initials", "LastDate", "NextDate", "PriceExcl", "Number", "Status", "TaxPercentage", "Periods", "Periodic", "StartPeriod", "EndPeriod", "InvoiceAuthorisation", "DebtorInvoiceAuthorisation", "TerminationDate"];
        $result = $this->all($fields, "id", "ASC", "-1", "id_in_array", implode(",", $batch_ids), "all_included_no_autorenew");
        $result = array_intersect_key($result, array_flip($batch_ids));
        $invoices = [];
        foreach ($result as $key => $value) {
            if((date("Ymd") <= substr(rewrite_date_site2db($value["TerminationDate"]), 0, 8) && substr(rewrite_date_site2db($value["StartPeriod"]), 0, 8) < substr(rewrite_date_site2db($value["TerminationDate"]), 0, 8) || $value["TerminationDate"] == "") && $value["Status"] < 8) {
                require_once "class/debtor.php";
                $debtor = new debtor();
                $debtor->Identifier = $value["Debtor"];
                $debtor->show();
                if($debtor->Status == 9) {
                    Database_Model::getInstance()->update("HostFact_PeriodicElements", ["Status" => "8"])->where("id", $key)->execute();
                } elseif($value["DebtorInvoiceAuthorisation"] == "yes" && $value["InvoiceAuthorisation"] == "no") {
                    $invoices["noinc-" . $value["Debtor"]][$key] = $value;
                } else {
                    $invoices[$value["Debtor"]][$key] = $value;
                }
            } elseif((substr(rewrite_date_site2db($value["TerminationDate"]), 0, 8) < date("Ymd") || substr(rewrite_date_site2db($value["TerminationDate"]), 0, 8) <= substr(rewrite_date_site2db($value["StartPeriod"]), 0, 8)) && $value["Status"] < 8) {
                Database_Model::getInstance()->update("HostFact_PeriodicElements", ["Status" => "8"])->where("id", $key)->execute();
            }
        }
        $this->makeinvoice2($invoices, true);
    }
    public function sentMail()
    {
        if(PERIODIC_REMINDER_MAIL <= 0) {
            return false;
        }
        require_once "class/template.php";
        $emailtemplate = new emailtemplate();
        $emailtemplate->Identifier = PERIODIC_REMINDER_MAIL;
        $emailtemplate->show();
        if(!$emailtemplate->Name) {
            return false;
        }
        require_once "class/email.php";
        $email = new email();
        foreach ($emailtemplate->Variables as $v) {
            if(is_string($emailtemplate->{$v})) {
                $email->{$v} = htmlspecialchars_decode($emailtemplate->{$v});
            } else {
                $email->{$v} = $emailtemplate->{$v};
            }
        }
        if(!empty($this->Identifier)) {
            $totals = Database_Model::getInstance()->getOne("HostFact_PeriodicElements", ["COUNT(`id`) as count", "SUM(`PriceExcl`*`Number`*`Periods`*ROUND((1-DiscountPercentage),8)) AS `totalExcl`", "SUM(`PriceExcl`*`Number`*`Periods`*ROUND((1+TaxPercentage),2)*ROUND((1-DiscountPercentage),8)) AS `totalIncl`", "SUM(`PriceExcl`*ROUND((TaxPercentage),2)*ROUND((1-DiscountPercentage),8)) AS `totalBTW`"])->where("id", $this->Identifier)->groupBy("id")->asArray()->execute();
        } else {
            $totals = ["count" => 0, "totalExcl" => 0, "totalIncl" => 0, "totalBTW" => 0];
            foreach ($this->ReminderPeriodics as $periodic_value) {
                $totals["count"]++;
                $totals["totalExcl"] += $periodic_value["totalExcl"];
                $totals["totalIncl"] += $periodic_value["totalIncl"];
                $totals["totalBTW"] += $periodic_value["totalBTW"];
            }
        }
        $email->ReminderPeriodics = $this->ReminderPeriodics;
        $email->Message = str_replace("[START:periodics]", "&lt;?php \$listperiodics = \$this->ReminderPeriodics; foreach(\$listperiodics as \$periodic_value){ \$periodicElement = new periodic(); \$periodicElement->Identifier = \$periodic_value['id']; \$periodicElement->show(); \$periodicElement->format(); ?&gt;", $email->Message);
        $email->Message = str_replace("[END:periodics]", "&lt;?php } ?&gt;", $email->Message);
        $email->Message = str_replace("[periodic-&gt;totalAmountExcl]", money($totals["totalExcl"]), $email->Message);
        $email->Message = str_replace("[periodic-&gt;totalAmountIncl]", money($totals["totalIncl"]), $email->Message);
        $email->Message = str_replace("[periodic-&gt;totalAmountBTW]", money($totals["totalBTW"]), $email->Message);
        $email->Message = str_replace("[periodic-&gt;totalAmountTax]", money($totals["totalBTW"]), $email->Message);
        $email->Message = str_replace("[periodic-&gt;count]", $totals["count"], $email->Message);
        $email->Debtor = $this->Debtor;
        $email->PeriodicIdentifier = PERIODIC_REMINDER_MERGE == "1" ? $this->ReminderPeriodics[0]["id"] : $this->Identifier;
        require_once "class/debtor.php";
        $debtor = new debtor();
        $debtor->Identifier = $this->Debtor;
        $debtor->show();
        if($this->ContractRenewable) {
            $email->Message = str_replace("[START:renew]<br />", "", $email->Message);
            $email->Message = str_replace("[START:renew]", "", $email->Message);
            $email->Message = str_replace("<br />[END:renew]", "", $email->Message);
            $email->Message = str_replace("[END:renew]", "", $email->Message);
            $link_hash = md5(sha1($debtor->Identifier . "-wetvandam-" . $debtor->DebtorCode));
            $email->Message = str_replace("[renewLinkRaw]", str_replace("betalen", "verlengen", IDEAL_EMAIL) . "?key=" . $link_hash, $email->Message);
            $email->Message = str_replace("[renewLink]", "<a href={&quot;}" . str_replace("betalen", "verlengen", IDEAL_EMAIL) . "?key=" . $link_hash . "{&quot;}>" . str_replace("betalen", "verlengen", IDEAL_EMAIL) . "?key=" . $link_hash . "</a>", $email->Message);
        } else {
            $pattern = "/(\\[START:renew\\].+?)+(\\[END:renew\\]<br \\/>)/is";
            $email->Message = preg_replace($pattern, "", $email->Message);
            $pattern = "/(\\[START:renew\\].+?)+(\\[END:renew\\])/is";
            $email->Message = preg_replace($pattern, "", $email->Message);
        }
        $email->Recipient = $debtor->EmailAddress;
        if(!$email->Subject) {
            $email->Subject = __("subscription reminder mail subject");
        }
        $email->add();
        if($email->sent()) {
            if(PERIODIC_REMINDER_MERGE == "1") {
                foreach ($this->ReminderPeriodics as $periodic_value) {
                    $this->ReminderDate = rewrite_date_site2db($periodic_value["NextDate"]);
                    Database_Model::getInstance()->update("HostFact_PeriodicElements", ["ReminderDate" => ["RAW" => "NextDate"]])->where("id", $periodic_value["id"])->execute();
                }
                createMessageLog("success", "subscription reminder merged mail sent", [count($this->ReminderPeriodics), $debtor->DebtorCode], "", 0, true);
            } else {
                $this->ReminderDate = rewrite_date_site2db($this->NextDate);
                Database_Model::getInstance()->update("HostFact_PeriodicElements", ["ReminderDate" => ["RAW" => "NextDate"]])->where("id", $this->Identifier)->execute();
                createMessageLog("success", "subscription reminder mail sent", $debtor->DebtorCode, "subscription", $this->Identifier, true);
            }
        } elseif(0 < $this->Identifier) {
            createMessageLog("error", "subscription reminder mail not sent", [$debtor->DebtorCode, implode(" ", $email->Error)], "subscription", $this->Identifier, true);
        } else {
            createMessageLog("error", "subscription reminder mail not sent", [$debtor->DebtorCode, implode(" ", $email->Error)], "", 0, true);
        }
    }
    public function checkReminders()
    {
        if(0 >= PERIODIC_REMINDER_DAYS) {
            return false;
        }
        $renew_contract_for_debtor = [];
        if(CONTRACT_RENEW_FOR != "none") {
            Database_Model::getInstance()->get(["HostFact_PeriodicElements", "HostFact_Debtors"], ["HostFact_PeriodicElements.id", "HostFact_PeriodicElements.Debtor"])->where("HostFact_Debtors.`id` = HostFact_PeriodicElements.`Debtor`")->where("HostFact_PeriodicElements.EndContract", ["<=" => ["RAW" => "DATE_ADD(CURDATE(), INTERVAL " . intval(CONTRACT_RENEW_DAYS_BEFORE) . " DAY)"]])->where("HostFact_PeriodicElements.Status", ["<" => "8"])->where("HostFact_PeriodicElements.TerminationDate", "0000-00-00")->where("HostFact_PeriodicElements.EndPeriod", [">=" => ["RAW" => "DATE_ADD(HostFact_PeriodicElements.`StartPeriod`, INTERVAL " . intval(CONTRACT_RENEW_MIN_PERIOD) . " MONTH)"]])->where("HostFact_PeriodicElements.AutoRenew", "yes");
            if(CONTRACT_RENEW_FOR == "private") {
                Database_Model::getInstance()->where("HostFact_Debtors.Companyname", "");
            }
            $result = Database_Model::getInstance()->groupBy("HostFact_PeriodicElements.Debtor")->execute();
            if($result && is_array($result)) {
                foreach ($result as $var) {
                    $renew_contract_for_debtor[$var->Debtor] = $var->id;
                }
            }
        }
        Database_Model::getInstance()->get(["HostFact_PeriodicElements", "HostFact_Debtors"], ["HostFact_PeriodicElements.id", "HostFact_PeriodicElements.Debtor", "HostFact_PeriodicElements.Status", "HostFact_PeriodicElements.TerminationDate", "(SELECT CAST(\n\t\t\t\t\t\tCASE HostFact_Debtors.InvoiceCollect \n\t\t\t\t\t\tWHEN 2 THEN CONCAT(CAST(DATE_FORMAT(HostFact_PeriodicElements.`NextDate`, '%Y-%m-') AS CHAR),CAST(IF(DAYOFMONTH(HostFact_PeriodicElements.`NextDate`) >= 15, '15', '01') AS CHAR)) \n\t\t\t\t\t\tWHEN 1 THEN CONCAT(CAST(DATE_FORMAT(HostFact_PeriodicElements.`NextDate`, '%Y-%m-') AS CHAR),CAST('01' AS CHAR)) \n\t\t\t\t\t\tWHEN -1 THEN " . (INVOICE_COLLECT_ENABLED == "yes" ? "CONCAT(CAST(DATE_FORMAT(HostFact_PeriodicElements.`NextDate`, '%Y-%m-') AS CHAR),CAST(IF(DAYOFMONTH(HostFact_PeriodicElements.`NextDate`) >= 15, '" . (INVOICE_COLLECT_TPM == 1 ? "01" : "15") . "', '01') AS CHAR))" : "HostFact_PeriodicElements.`NextDate`") . " \n\t\t\t\t\t\tELSE HostFact_PeriodicElements.`NextDate` \n\t\t\t\t\tEND AS CHAR)) as NextDate", "CAST(DATE_ADD((SELECT CAST(\n\t\t\t\t\t\tCASE HostFact_Debtors.InvoiceCollect \n\t\t\t\t\t\tWHEN 2 THEN CONCAT(CAST(DATE_FORMAT(HostFact_PeriodicElements.`NextDate`, '%Y-%m-') AS CHAR),CAST(IF(DAYOFMONTH(HostFact_PeriodicElements.`NextDate`) >= 15, '15', '01') AS CHAR)) \n\t\t\t\t\t\tWHEN 1 THEN CONCAT(CAST(DATE_FORMAT(HostFact_PeriodicElements.`NextDate`, '%Y-%m-') AS CHAR),CAST('01' AS CHAR)) \n\t\t\t\t\t\tWHEN -1 THEN " . (INVOICE_COLLECT_ENABLED == "yes" ? "CONCAT(CAST(DATE_FORMAT(HostFact_PeriodicElements.`NextDate`, '%Y-%m-') AS CHAR),CAST(IF(DAYOFMONTH(HostFact_PeriodicElements.`NextDate`) >= 15, '" . (INVOICE_COLLECT_TPM == 1 ? "01" : "15") . "', '01') AS CHAR))" : "HostFact_PeriodicElements.`NextDate`") . " \n\t\t\t\t\t\tELSE HostFact_PeriodicElements.`NextDate` END AS CHAR)), INTERVAL -" . intval(PERIODIC_REMINDER_DAYS) . " DAY) AS CHAR) as WarningDate", "(HostFact_PeriodicElements.`PriceExcl`*HostFact_PeriodicElements.`Number`*HostFact_PeriodicElements.`Periods`*ROUND((1-HostFact_PeriodicElements.DiscountPercentage),8)) AS `totalExcl`", "(HostFact_PeriodicElements.`PriceExcl`*HostFact_PeriodicElements.`Number`*HostFact_PeriodicElements.`Periods`*ROUND((1+HostFact_PeriodicElements.TaxPercentage),2)*ROUND((1-HostFact_PeriodicElements.DiscountPercentage),8)) AS `totalIncl`", "(HostFact_PeriodicElements.`PriceExcl`*ROUND((HostFact_PeriodicElements.TaxPercentage),2)*ROUND((1-HostFact_PeriodicElements.DiscountPercentage),8)) AS `totalBTW`"]);
        if(PERIODIC_REMINDER_SENT == "1") {
            Database_Model::getInstance()->where("HostFact_Debtors.`id` = HostFact_PeriodicElements.`Debtor`")->where("HostFact_Debtors.EmailAddress", ["!=" => ""])->where("HostFact_PeriodicElements.Reminder", ["!=" => "no"])->where("HostFact_PeriodicElements.`NextDate` != HostFact_PeriodicElements.`ReminderDate`")->where("HostFact_PeriodicElements.AutoRenew", "yes");
            if(PERIODIC_REMINDER_SENT_FOR == "private") {
                Database_Model::getInstance()->where("HostFact_Debtors.Companyname", "");
            }
            $result = Database_Model::getInstance()->execute();
        } else {
            Database_Model::getInstance()->where("HostFact_Debtors.`id` = HostFact_PeriodicElements.`Debtor`")->where("HostFact_Debtors.EmailAddress", ["!=" => ""])->where("HostFact_PeriodicElements.Reminder", "yes")->where("HostFact_PeriodicElements.`NextDate` != HostFact_PeriodicElements.`ReminderDate`")->where("HostFact_PeriodicElements.AutoRenew", "yes");
            $result = Database_Model::getInstance()->execute();
        }
        if($result && is_array($result)) {
            foreach ($result as $var) {
                $Warndate = str_replace("-", "", $var->WarningDate);
                if($Warndate <= date("Ymd") && $var->Status < 8 && date("Y-m-d") < $var->NextDate && $var->TerminationDate == "0000-00-00") {
                    if(PERIODIC_REMINDER_MERGE == "0") {
                        $this->NextDate = $var->NextDate;
                        $this->Debtor = $var->Debtor;
                        $this->ReminderPeriodics = [["id" => $var->id]];
                        $this->Identifier = $var->id;
                        $this->ContractRenewable = isset($renew_contract_for_debtor[$this->Debtor]) ? true : false;
                        $this->sentMail();
                    } else {
                        $this->Identifier = "";
                        $array_periodics[$var->Debtor][] = ["id" => $var->id, "NextDate" => $var->NextDate, "totalExcl" => $var->totalExcl, "totalIncl" => $var->totalIncl, "totalBTW" => $var->totalBTW];
                    }
                } elseif($Warndate <= date("Ymd") && $var->Status < 8) {
                }
            }
        }
        if(PERIODIC_REMINDER_MERGE == "1" && !empty($array_periodics)) {
            foreach ($array_periodics as $reminder_key => $reminder_value) {
                $this->Debtor = $reminder_key;
                $this->NextDate = $reminder_value[0]["NextDate"];
                foreach ($reminder_value as $reminder_value2) {
                    $this->ReminderPeriodics[] = $reminder_value2;
                }
                $this->ContractRenewable = isset($renew_contract_for_debtor[$this->Debtor]) ? true : false;
                $this->sentMail();
                $this->ReminderPeriodics = [];
            }
        }
    }
    public function changeReference($id, $type, $reference, $new_debtor = 0)
    {
        Database_Model::getInstance()->update("HostFact_PeriodicElements", ["PeriodicType" => $type, "Reference" => $reference])->where("id", $id)->execute();
        if(0 < $new_debtor) {
            Database_Model::getInstance()->update("HostFact_PeriodicElements", ["Debtor" => $new_debtor])->where("id", $id)->execute();
        }
        $result = Database_Model::getInstance()->getOne("HostFact_PeriodicElements", ["HostFact_Products.id"])->join("HostFact_Products", "HostFact_PeriodicElements.`ProductCode` = HostFact_Products.`ProductCode` AND HostFact_Products.`Status` < 9")->where("HostFact_PeriodicElements.id", $id)->execute();
        $product_id = isset($result->id) && 0 < $result->id ? $result->id : 0;
        if($type == "domain") {
            Database_Model::getInstance()->update("HostFact_Domains", ["PeriodicID" => $id, "Product" => $product_id])->where("id", $reference)->execute();
        } elseif($type == "hosting") {
            Database_Model::getInstance()->update("HostFact_Hosting", ["PeriodicID" => $id, "Product" => $product_id])->where("id", $reference)->execute();
        } else {
            Database_Model::getInstance()->update("HostFact_Domains", ["PeriodicID" => "0"])->where("PeriodicID", $id)->execute();
            Database_Model::getInstance()->update("HostFact_Hosting", ["PeriodicID" => "0"])->where("PeriodicID", $id)->execute();
        }
    }
    public function checkRecognition($productcode, $description)
    {
        $var_product = Database_Model::getInstance()->getOne("HostFact_Products", ["id", "ProductKeyPhrase", "ProductType"])->where("ProductCode", $productcode)->execute();
        if(0 < $var_product->id) {
            switch ($var_product->ProductType) {
                case "hosting":
                    $hosting = false;
                    $pattern = "/\\[h\\:(.*?)\\]/is";
                    preg_match($pattern, $description, $matches);
                    if(isset($matches[1])) {
                        $hosting = trim($matches[1]);
                    }
                    if(!$hosting) {
                        return ["PeriodicType" => "hosting", "Match" => "unknown", "ProductID" => $var_product->id, "Auto" => true];
                    }
                    return ["PeriodicType" => "hosting", "Match" => $hosting, "ProductID" => $var_product->id];
                    break;
                case "domain":
                    $domain = false;
                    $pattern = "/\\[d\\:(.*?)\\]/is";
                    preg_match($pattern, $description, $matches);
                    if(isset($matches[1])) {
                        $domain = explode(".", trim($matches[1]), 2);
                    }
                    if(isset($domain[0]) && 0 < strlen($domain[0]) && isset($domain[1]) && 0 < strlen($domain[1]) && is_domain(implode(".", $domain))) {
                        $result = Database_Model::getInstance()->getOne("HostFact_TopLevelDomain", ["id"])->where("Tld", $domain[1])->execute();
                        $tld_id = isset($result->id) ? $result->id : NULL;
                        return ["PeriodicType" => "domain", "Match" => implode(".", $domain), "ProductID" => $var_product->id, "TLD" => $domain[1], "TLDcheck" => $tld_id];
                    }
                    $description = explode(" ", $description);
                    foreach ($description as $candidate) {
                        $domain = explode(".", str_replace("www.", "", $candidate), 2);
                        if(isset($domain[0]) && 0 < strlen($domain[0]) && isset($domain[1]) && 0 < strlen($domain[1]) && is_domain(implode(".", $domain))) {
                            $result = Database_Model::getInstance()->getOne("HostFact_TopLevelDomain", ["id"])->where("Tld", $domain[1])->execute();
                            $tld_id = isset($result->id) ? $result->id : NULL;
                            return ["PeriodicType" => "domain", "Match" => implode(".", $domain), "ProductID" => $var_product->id, "TLD" => $domain[1], "TLDcheck" => $tld_id];
                        }
                    }
                    break;
                default:
                    if($var_product->ProductType && $var_product->ProductType != "other") {
                        global $additional_product_types;
                        if(array_key_exists($var_product->ProductType, $additional_product_types)) {
                            return ["PeriodicType" => $var_product->ProductType, "ProductID" => $var_product->id];
                        }
                        return ["PeriodicType" => $var_product->ProductType, "Match" => "", "ProductID" => $var_product->id];
                    }
            }
        }
        return ["PeriodicType" => "other", "Match" => "", "ProductID" => ""];
    }
    public function terminate($id, $termination_date)
    {
        if(!is_numeric($id)) {
            $this->Error[] = __("invalid identifier for subscription");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_PeriodicElements", ["*"])->where("id", $id)->execute();
        if($termination_date <= date("Y-m-d")) {
            $result_db = Database_Model::getInstance()->update("HostFact_PeriodicElements", ["TerminationDate" => $termination_date, "Status" => "8"])->where("id", $id)->execute();
        } elseif($result->Status == 8) {
            $result_db = Database_Model::getInstance()->update("HostFact_PeriodicElements", ["TerminationDate" => $termination_date, "Status" => "1"])->where("id", $id)->execute();
        } else {
            $result_db = Database_Model::getInstance()->update("HostFact_PeriodicElements", ["TerminationDate" => $termination_date])->where("id", $id)->execute();
        }
        if(!$result_db) {
            return false;
        }
        $service_info = ["Type" => $result->PeriodicType, "id" => 0 < $result->Reference ? $result->Reference : $id, "Debtor" => $result->Debtor, "TerminationDate" => $termination_date];
        do_action("service_is_terminated", $service_info);
        return true;
    }
    public function reactivate($id, $new_start_period = false)
    {
        if(!is_numeric($id)) {
            $this->Error[] = __("invalid identifier for subscription");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_PeriodicElements", ["*"])->where("id", $id)->execute();
        if(!$result) {
            $this->Error[] = __("invalid identifier for subscription");
            return false;
        }
        if($new_start_period === false) {
            $result_upd = Database_Model::getInstance()->update("HostFact_PeriodicElements", ["TerminationDate" => "0000-00-00", "Status" => "1"])->where("id", $id)->execute();
        } else {
            $PeriodicInvoiceDays = $this->getPeriodicInvoiceDays($result->Debtor);
            if((int) $PeriodicInvoiceDays !== 0) {
                $NextDate = date("Y-m-d", strtotime("-" . $PeriodicInvoiceDays . " DAYS", strtotime($new_start_period)));
            } else {
                $NextDate = $new_start_period;
            }
            $result_upd = Database_Model::getInstance()->update("HostFact_PeriodicElements", ["StartPeriod" => $new_start_period, "EndPeriod" => ["RAW" => "DATE_ADD(`StartPeriod`, INTERVAL " . $this->__getMySQLInterval($result->Periodic, $result->Periods) . ")"], "TerminationDate" => "0000-00-00", "NextDate" => $NextDate, "Status" => "1"])->where("id", $id)->execute();
        }
        if(!$result_upd) {
            return false;
        }
        $service_info = ["Type" => $result->PeriodicType, "id" => 0 < $result->Reference ? $result->Reference : $id, "Debtor" => $result->Debtor];
        do_action("service_is_reactivated", $service_info);
        return true;
    }
    public function getID($method, $value, $debtor_id = false)
    {
        switch ($method) {
            case "clientarea":
                $service_id = Database_Model::getInstance()->getOne("HostFact_PeriodicElements", ["id"])->where("id", intval($value))->where("Debtor", $debtor_id)->execute();
                return $service_id !== false && 0 < $debtor_id ? $service_id->id : false;
                break;
        }
    }
    private function getPeriodicInvoiceDays($iDebtorId)
    {
        $result = Database_Model::getInstance()->getOne("HostFact_Debtors", ["PeriodicInvoiceDays"])->where("id", $iDebtorId)->execute();
        if(isset($result->PeriodicInvoiceDays) && $result->PeriodicInvoiceDays != -1) {
            return $result->PeriodicInvoiceDays;
        }
        return PERIODIC_INVOICE_DAYS;
    }
}

?>