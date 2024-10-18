<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
if(isset($_SERVER["SCRIPT_FILENAME"]) && $_SERVER["SCRIPT_FILENAME"]) {
    $basedir = str_replace("cronjob_accounting.php", "", $_SERVER["SCRIPT_FILENAME"]);
    if(!empty($basedir)) {
        chdir($basedir);
    }
}
define("SCRIPT_IS_CRONJOB", true);
define("InStAlLHosTFacT", "Ra.33sUdhWlkd22");
define("LOGINSYSTEM", true);
require_once "config.php";
if(defined("SOFTWARE_FILE_VERSION") && SOFTWARE_FILE_VERSION != SOFTWARE_VERSION) {
    exit;
}
if(CRONJOB_ACCOUNTING_IS_RUNNING == "" || 30 <= abs(strtotime(CRONJOB_ACCOUNTING_IS_RUNNING) - time()) / 60) {
    $settings->Variable = "CRONJOB_ACCOUNTING_IS_RUNNING";
    $settings->Value = date("Y-m-d H:i:s");
    $settings->edit();
    require_once "class/automation.php";
    $automation = new automation();
    $automation->show();
    require_once "class/company.php";
    $company = new company();
    $company->show();
    if(defined("CRONJOB_ACCOUNTING_LASTDATE")) {
        $settings->Variable = "CRONJOB_ACCOUNTING_LASTDATE";
        $settings->Value = date("Y-m-d H:i:s");
        $settings->edit();
    }
    $automation_packages = Database_Model::getInstance()->get("HostFact_ExportSettings", "package")->where("name", "automationSettings")->execute();
    if(!$automation_packages) {
        exit;
    }
    $time_started = time();
    $time_limit = 0 < ini_get("max_execution_time") ? ini_get("max_execution_time") : 30;
    $time_gap_for_another_batch = 10;
    foreach ($automation_packages as $_package) {
        $package = $_package->package;
        if(@file_exists("3rdparty/export/" . $package . "/" . $package . "_new.php") && @file_exists("3rdparty/export/" . $package . "/version.php")) {
            require_once "3rdparty/export/" . $package . "/" . $package . "_new.php";
            $className = "export_accounting_package_" . $package;
            $export = new $className();
            $to_be_exported = $export->getStatistics(false);
            $errors_export_types = [];
            $automate_export_types = [];
            $automationSettings = $export->getSettingsValue("automationSettings");
            if(isset($automationSettings["data"])) {
                foreach ($automationSettings["data"] as $_export_type) {
                    $automate_export_types[$_export_type] = true;
                }
            }
            $export->removeGlobalErrors();
            foreach ($to_be_exported as $export_type => $_type_info) {
                $export_type_original = $export_type;
                if($export_type == "payment_invoice" || $export_type == "payment_purchase") {
                    $export_type = "payment_invoice";
                    if(isset($payment_invoice_is_executed) && $payment_invoice_is_executed === true) {
                    } else {
                        $payment_invoice_is_executed = true;
                    }
                }
                if(($export_type == "payment_invoice" || !empty($_type_info["export_needed"])) && isset($automate_export_types[$export_type]) && $automate_export_types[$export_type] === true) {
                    $skip_remaining_time_check = $export_type == "payment_invoice" ? true : false;
                    $remaining_ids = array_keys($_type_info["export_needed"]);
                    $export->saveStatistics($export_type_original, "export_needed");
                    switch ($export_type) {
                        case "debtor":
                            $remaining_ids = $export->exportDebtors($remaining_ids);
                            break;
                        case "invoice":
                            $remaining_ids = $export->exportInvoices($remaining_ids);
                            break;
                        case "creditor":
                            $remaining_ids = $export->exportCreditors($remaining_ids);
                            break;
                        case "creditinvoice":
                            $remaining_ids = $export->exportCreditInvoices($remaining_ids);
                            break;
                        case "product":
                            $remaining_ids = $export->exportProducts($remaining_ids);
                            break;
                        case "sddbatch":
                            $remaining_ids = $export->exportSDDBatches($remaining_ids);
                            break;
                        case "payment_invoice":
                            $remaining_sales_ids = $export->importPayments();
                            $remaining_purchase_ids = $export->importPurchasePayments();
                            $remaining_ids = [];
                            if(is_array($remaining_sales_ids) && 0 < count($remaining_sales_ids)) {
                                $remaining_ids = $remaining_sales_ids;
                            } elseif(is_array($remaining_purchase_ids) && 0 < count($remaining_purchase_ids)) {
                                $remaining_ids = $remaining_purchase_ids;
                            }
                            break;
                        default:
                            $time_passed = time() - $time_started;
                            if($remaining_ids === false || !empty($export->Error)) {
                                if($export->ErrorType == "connection") {
                                    $errors_export_types["connection"] = $export->Error;
                                } elseif($export->ErrorType == "authentication") {
                                    $errors_export_types["authentication"] = $export->Error;
                                } elseif($export->ErrorType == "export") {
                                    $errors_export_types[$export_type] = $export->Error;
                                    $export->Error = [];
                                    $automate_export_types[$export_type] = false;
                                    if($export_type == "debtor") {
                                        $automate_export_types["invoice"] = false;
                                    } elseif($export_type == "creditor") {
                                        $automate_export_types["creditinvoice"] = false;
                                    }
                                }
                                if(!empty($errors_export_types)) {
                                    $export->setGlobalErrors($errors_export_types);
                                }
                                $export->setAdministrationCounter();
                            }
                            if(!empty($remaining_ids) && $time_passed < $time_limit - $time_gap_for_another_batch) {
                            }
                    }
                }
            }
        } else {
            exit;
        }
    }
    $settings->Variable = "CRONJOB_ACCOUNTING_IS_RUNNING";
    $settings->Value = "";
    $settings->edit();
} else {
    exit;
}

?>