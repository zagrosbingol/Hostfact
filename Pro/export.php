<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "config.php";
require_once "3rdparty/export/config.php";
require_once "3rdparty/export/functions.php";
require_once "class/exportTemplate.php";
$exportTemplate = new exportTemplate();
$page = isset($_GET["page"]) ? $_GET["page"] : "overview";
$template_id = isset($_GET["id"]) ? intval(esc($_GET["id"])) : (isset($_POST["id"]) ? intval(esc($_POST["id"])) : NULL);
$template_type = isset($_GET["template_type"]) ? esc($_GET["template_type"]) : (isset($_POST["template_type"]) ? esc($_POST["template_type"]) : NULL);
$dataset = isset($_GET["dataset"]) ? esc($_GET["dataset"]) : (isset($_POST["dataset"]) ? esc($_POST["dataset"]) : NULL);
if(isset($template_type) && $template_type == "extern") {
    $filename = strtolower($dataset);
}
if(isset($filename)) {
    $result = $exportTemplate->get_external_template($filename);
    if($result) {
        require_once "3rdparty/export/templates/" . $filename . ".php";
        $func = $filename . "_export";
        $externTemplate = new $func();
        $externTemplate->ExportData = $exportdata;
        $page = $page != "download" ? "csv" : "download";
    } else {
        echo "<div id=\"form_download_extern\">" . __("license corrupt for selected external template") . "</div>";
        exit;
    }
}
$exportTemplate->config = $exportdata;
checkRight(U_EXPORT_SHOW);
switch ($page) {
    case "accounting_package":
        $package = strtolower($_GET["module"]);
        if(defined("IS_DEMO") && IS_DEMO && $package) {
            $export->Error[] = __("demo - not possible to use accounting modules");
            flashMessage($export);
            header("Location: export.php");
            exit;
        }
        if(@file_exists("3rdparty/export/" . $package . "/" . $package . ".php") && @file_exists("3rdparty/export/" . $package . "/version.php")) {
            require_once "3rdparty/export/" . $package . "/" . $package . ".php";
        } elseif(@file_exists("3rdparty/export/class." . $package . ".php")) {
            require_once "3rdparty/export/class." . $package . ".php";
        } else {
            $module_version_information = $_SESSION["module_version_information"];
            if(!isset($module_version_information["accounting_export"][$package])) {
                $error_class->Error[] = __("module install, required files could not be retrieved");
                flashMessage($error_class);
                header("Location: export.php");
                exit;
            }
            require_once __DIR__ . "/3rdparty/modules/hostfact_module.php";
            $module = new hostfact_module();
            if(!$module->installModule($module_version_information["accounting_export"][$package]["name"], "3rdparty/export", $package, $module_version_information["accounting_export"][$package]["download_link"], $module_version_information["accounting_export"][$package]["download_hash"])) {
                flashMessage($module);
                header("Location: export.php");
                exit;
            }
            require_once "3rdparty/export/class.export.php";
            $export = new export();
            $available_package_list = (array) $export->getAvailablePackages();
            $_package_info = (array) $available_package_list[$package];
            if(isset($_package_info["hostfact_integration"]) && $_package_info["hostfact_integration"]) {
                header("Location: exportaccounting.php?module=" . $package . "&action=start");
                exit;
            }
            require_once "3rdparty/export/" . $package . "/" . $package . ".php";
        }
        $className = "export" . ucfirst($package);
        $export = new $className();
        $accounting_action = isset($_GET["action"]) ? $_GET["action"] : "";
        switch ($accounting_action) {
            case "start_trial":
                if(!$export->startTrial()) {
                    flashMessage($export);
                    header("Location: export.php");
                    exit;
                }
                header("Location: export.php?page=accounting_package&module=" . $package);
                exit;
                break;
            case "end_module":
                if(!empty($_POST) && isset($_POST["imsure"]) && $_POST["imsure"] == "yes" && !$export->endSubscription($_POST["message"])) {
                    flashMessage($export);
                    header("Location: export.php?page=accounting_package&module=" . $package);
                    exit;
                }
                flashMessage($export);
                header("Location: export.php");
                exit;
                break;
            case "order_module":
                $export->orderSubscription();
                flashMessage($export);
                header("Location: export.php?page=accounting_package&module=" . $package);
                exit;
                break;
        }
        break;
    case "edit":
        if(isset($_POST) && !empty($_POST)) {
            $exportTemplate = new exportTemplate();
            $exportTemplate->Identifier = $template_id;
            if(0 < $exportTemplate->Identifier) {
                $exportTemplate->show();
            }
            $exportTemplate->Template = utf8_decode($_POST["template"]);
            $exportTemplate->Data = esc($_POST["selected_columns"]);
            $exportTemplate->ExportData = $_POST["ExportData"];
            if($exportTemplate->save_template()) {
                flashMessage($exportTemplate);
                header("Location: export.php?page=csv");
                exit;
            }
        }
        break;
    case "download":
        if(!isset($template_id)) {
            $template_id = esc($_POST["ExportData"]);
        }
        $exportTemplate->Identifier = $template_id;
        $exportTemplate->show();
        $filters = $exportdata[$exportTemplate->ExportData]["filters"];
        if(is_array($filters)) {
            foreach ($filters as $key => $value) {
                if(isset($_POST["value_" . $key])) {
                    $filters[$key]["value"] = trim(esc($_POST["value_" . $key]));
                } elseif(isset($_POST["value1_" . $key]) && isset($_POST["value2_" . $key])) {
                    $filters[$key]["value1"] = trim(esc($_POST["value1_" . $key]));
                    $filters[$key]["value2"] = trim(esc($_POST["value2_" . $key]));
                } elseif(is_array($_POST[$value["field"]]) && count($_POST[$value["field"]]) && $value["type"] == "checkbox") {
                    $filters[$key]["checked"] = $_POST[$value["field"]];
                } elseif(!is_array($_POST[$value["field"]]) && !empty($_POST[$value["field"]]) && $value["type"] == "checkbox") {
                    $filters[$key]["checked"] = trim(esc($_POST[$value["field"]]));
                }
            }
        }
        if(isset($template_type) && $template_type == "extern") {
            $externTemplate->Table = $exportTemplate->ExportData;
            $externTemplate->Filter = $filters;
            if(!$externTemplate->create_exportfile()) {
                flashMessage($externTemplate);
                header("Location: export.php?page=csv");
                exit;
            }
        } else {
            if(!is_writable("temp/")) {
                $pro_map_name = software_get_relative_path();
                $pro_map_name .= "temp/";
                $error_class->Error[] = sprintf(__("cannot export csv, no folder rights"), $pro_map_name);
                flashMessage();
                header("Location: export.php?page=csv");
                exit;
            }
            $exportTemplate->Tables = $exportdata[$exportTemplate->ExportData]["tables"];
            $exportTemplate->Filter = $filters;
            $csv_rows = $exportTemplate->get_csv_content();
            if(!empty($csv_rows)) {
                $file = "HostFact_export_" . strtolower(str_replace(" ", "-", $exportdata[$exportTemplate->ExportData]["title"])) . "_" . str_replace("%", "", date(DATE_FORMAT)) . "_" . date("His") . ".csv";
                $exclude_elements = ["CustomFields"];
                $csv_headers = "";
                foreach ($exportTemplate->Elements as $key => $value) {
                    $arr_key = explode(".", $value);
                    if(in_array($arr_key[1], $exclude_elements)) {
                        switch ($arr_key[1]) {
                            case "CustomFields":
                                require_once "class/customfields.php";
                                $custom_fields_obj = new customfields();
                                switch ($arr_key[0]) {
                                    case "HostFact_Debtors":
                                        $aCustomFields = $custom_fields_obj->getCustomDebtorFields();
                                        break;
                                    case "HostFact_Invoice":
                                        $aCustomFields = $custom_fields_obj->getCustomInvoiceFields();
                                        break;
                                    case "HostFact_PriceQuote":
                                        $aCustomFields = $custom_fields_obj->getCustomPriceQuoteFields();
                                        break;
                                    default:
                                        foreach ($aCustomFields as $custom_field) {
                                            $csv_headers .= "\"" . $custom_field["LabelTitle"] . "\",";
                                        }
                                }
                                break;
                        }
                    } elseif(isset($exportTemplate->config[$exportTemplate->ExportData]["tables"][$arr_key[0]]["field_translations"][$arr_key[1]])) {
                        $csv_headers .= "\"" . __("export." . $exportTemplate->config[$exportTemplate->ExportData]["tables"][$arr_key[0]]["field_translations"][$arr_key[1]]) . "\",";
                    } else {
                        $csv_headers .= "\"" . __("export." . $arr_key[1]) . "\",";
                    }
                }
                $csv_data = "";
                foreach ($csv_rows as $value) {
                    $csv_data .= $value . "\r\n";
                }
                $bom = "﻿";
                $csv_data = $bom . substr($csv_headers, 0, -1) . "\r\n" . $csv_data;
                file_put_contents("temp/" . $file, $csv_data);
                $_SESSION["force_download"] = $file;
                header("Location: export.php?page=csv");
                exit;
            } else {
                flashMessage($exportTemplate);
                header("Location: export.php?page=csv");
                exit;
            }
        }
        break;
    case "delete":
        if(!U_EXPORT_DELETE) {
        } elseif(isset($template_id) && isset($_POST["imsure"]) && $_POST["imsure"] == "yes") {
            $exportTemplate->Identifier = $template_id;
            $exportTemplate->show();
            if($exportTemplate->delete()) {
                flashMessage($exportTemplate);
                header("Location: export.php?page=csv");
                exit;
            }
        }
        break;
    case "csv":
        $fields = ["Name", "Type", "ExportData", "Date", "Filename"];
        $exportTemplate->TemplateType = "extern";
        $extern_templates = $exportTemplate->all($fields);
        $exportTemplate->TemplateType = "intern";
        $exportTemplates = $exportTemplate->all($fields, "Name", "ASC", "-1", false, false, 1000);
        $wfh_page_title = __("export data");
        $message = parse_message($exportTemplate);
        $current_page_url = "export.php";
        $sidebar_template = "export.sidebar.php";
        require_once "views/export.csv.php";
        break;
    case "edit":
        if(isset($template_id) && !empty($template_id)) {
            $exportTemplate->Identifier = $template_id;
            if(!$exportTemplate->show()) {
                flashMessage($exportTemplate);
                header("Location: export.php");
                exit;
            }
        } elseif(isset($_GET["exportdata"])) {
            $exportTemplate->ExportData = esc($_GET["exportdata"]);
        }
        $wfh_page_title = __("export data");
        $message = parse_message($exportTemplate);
        $current_page_url = "export.php";
        $sidebar_template = "export.sidebar.php";
        require_once "views/export.edit.php";
        break;
    case "invoicepdf":
        if(!empty($_POST)) {
            include "3rdparty/export/config.php";
            $create_pdfs = true;
            Database_Model::getInstance()->get("HostFact_Invoice", "id");
            if($_POST["InvoiceCode_Start"] != "") {
                Database_Model::getInstance()->where("InvoiceCode", [">=" => esc($_POST["InvoiceCode_Start"])])->where("LENGTH(`InvoiceCode`)", [">=" => strlen(esc($_POST["InvoiceCode_Start"]))]);
                if($_POST["InvoiceCode_End"] != "") {
                    Database_Model::getInstance()->where("InvoiceCode", ["<=" => esc($_POST["InvoiceCode_End"])])->where("LENGTH(`InvoiceCode`)", ["<=" => strlen(esc($_POST["InvoiceCode_End"]))]);
                }
            }
            if(is_date(rewrite_date_site2db($_POST["Date_Start"])) && is_date(rewrite_date_site2db(esc($_POST["Date_End"])))) {
                Database_Model::getInstance()->where("Date", ["BETWEEN" => [rewrite_date_site2db(esc($_POST["Date_Start"])), rewrite_date_site2db(esc($_POST["Date_End"]))]]);
            }
            if(isset($_POST["Status"]) && !empty($_POST["Status"])) {
                $status = [];
                foreach ($_POST["Status"] as $st) {
                    $status[] = intval($st);
                }
                Database_Model::getInstance()->where("Status", ["IN" => $status]);
            } else {
                $export->Error[] = __("no status selected");
                $create_pdfs = false;
            }
            if($create_pdfs === true) {
                $result = Database_Model::getInstance()->execute();
                $rows = count($result);
                if($rows === 0) {
                    $export->Error[] = __("no result for the selected period");
                } else {
                    $files_to_unlink = [];
                    if(function_exists("gzcompress")) {
                        require_once "class/invoice.php";
                        $zip = new ZipArchive();
                        $zip_filename = __("invoice-per-pdf") . date("d-m-Y") . ".zip";
                        if($zip->open("temp/" . $zip_filename, ZipArchive::CREATE)) {
                            foreach ($result as $invoice_obj) {
                                $invoice = new invoice();
                                $invoice->Identifier = $invoice_obj->id;
                                if($invoice->show() && $invoice->printInvoice(false, true)) {
                                    $zip->addFile("temp/" . $_SESSION["force_download"], $_SESSION["force_download"]);
                                    $files_to_unlink[] = "temp/" . $_SESSION["force_download"];
                                    unset($_SESSION["force_download"]);
                                }
                            }
                            $zip->close();
                        }
                        $_SESSION["force_download"] = $zip_filename;
                        foreach ($files_to_unlink as $file) {
                            @unlink($file);
                        }
                        header("Location: export.php?page=invoicepdf");
                        exit;
                    } else {
                        $export->Error[] = __("zlib not installed");
                    }
                }
            }
        }
        $message = parse_message($export);
        $wfh_page_title = __("export pdf invoice header title");
        $current_page_url = "export.php";
        $sidebar_template = "export.sidebar.php";
        require_once "views/export.invoice.pdf.php";
        break;
    case "accounting_package":
        if(!$export->checkLicense()) {
            if($export->getLicenseStatus() == "EXPIRED") {
                $message = parse_message($export);
                $package_information = $export->getVersionInformation();
                $current_page_url = "export.php?page=accounting_package&module=" . $package;
                $sidebar_template = "export.sidebar.php";
                require_once "3rdparty/export/view.export.expired.php";
                exit;
            }
            flashMessage($export);
            header("Location: export.php");
            exit;
        }
        $mode = isset($_POST["mode"]) ? $_POST["mode"] : "";
        if($mode == "saveSettings" && ($export->hasSettings() || isset($export->hasOAuth) && $export->hasOAuth === true)) {
            $groups = [];
            if(isset($_POST["group"]) && isset($_POST["toggleCustomLedgerTable"])) {
                $group_list = $export->getGroups();
                foreach ($group_list as $tmp_group) {
                    $groups[$tmp_group["id"]] = ["id" => NULL];
                }
                foreach ($_POST["group"] as $k => $v) {
                    $groups[$k]["id"] = trim(esc($v));
                }
            }
            $purchase_groups = [];
            if(isset($_POST["purchase_group"]) && isset($_POST["togglePurchaseLedgerTable"])) {
                $group_list = $export->getCreditorGroups();
                foreach ($group_list as $tmp_group) {
                    $purchase_groups[$tmp_group["id"]] = ["id" => NULL];
                }
                foreach ($_POST["purchase_group"] as $k => $v) {
                    $purchase_groups[$k]["id"] = trim(esc($v));
                }
            }
            $settings = $export->getSettings("ledgerAccounts");
            if(isset($_POST["accounts"])) {
                foreach ($_POST["accounts"] as $k => $v) {
                    $settings["value"]["default"][$k]["id"] = trim(esc($v));
                }
            }
            $settings["value"]["custom"] = $groups;
            $settings["value"]["purchase_groups"] = $purchase_groups;
            $export->saveSettings("ledgerAccounts", json_encode($settings["value"]));
            if(method_exists($export, "saveOtherSettings")) {
                $export->saveOtherSettings();
            }
            $export->Success[] = __("settings are modified");
            flashMessage($export);
            header("Location: export.php?page=accounting_package&module=" . $package);
            exit;
        } else {
            if($mode == "export") {
                $post = $_POST;
                if(is_array($post["filters"])) {
                    foreach ($post["filters"] as $filter => $filterOn) {
                        switch ($filter) {
                            case "debtor_code":
                                require_once "class/debtor.php";
                                $debtor = new debtor();
                                if($post["debtor_code_min"] == "") {
                                    $post["debtor_code_min"] = $debtor->getDebtorBeginEndCode("first");
                                } elseif($debtor->is_free($post["debtor_code_min"])) {
                                    $export->Error[] = __("invalid debtorcode min");
                                }
                                if($post["debtor_code_max"] == "") {
                                    $post["debtor_code_max"] = $debtor->getDebtorBeginEndCode("last");
                                } elseif($debtor->is_free($post["debtor_code_max"])) {
                                    $export->Error[] = __("invalid debtorcode max");
                                }
                                break;
                            case "creditor_code":
                                require_once "class/creditor.php";
                                $creditor = new creditor();
                                if($post["creditor_code_min"] == "") {
                                    $post["creditor_code_min"] = $creditor->getCreditorBeginEndCode("first");
                                } elseif($creditor->is_free($post["creditor_code_min"])) {
                                    $export->Error[] = __("invalid creditorcode min");
                                }
                                if($post["creditor_code_max"] == "") {
                                    $post["creditor_code_max"] = $creditor->getCreditorBeginEndCode("last");
                                } elseif($creditor->is_free($post["creditor_code_max"])) {
                                    $export->Error[] = __("invalid creditorcode max");
                                }
                                break;
                            case "invoice_code":
                                require_once "class/invoice.php";
                                $invoice = new invoice();
                                if($post["invoice_code_min"] == "") {
                                    $post["invoice_code_min"] = $invoice->getInvoiceBeginEndCode("first");
                                } elseif($invoice->is_free($post["invoice_code_min"])) {
                                    $export->Error[] = __("invalid invoicecode min");
                                }
                                if($post["invoice_code_max"] == "") {
                                    $post["invoice_code_max"] = $invoice->getInvoiceBeginEndCode("last");
                                } elseif($invoice->is_free($post["invoice_code_max"])) {
                                    $export->Error[] = __("invalid invoicecode max");
                                }
                                break;
                            case "creditinvoice_code":
                                require_once "class/creditinvoice.php";
                                $creditinvoice = new creditinvoice();
                                if($post["creditinvoice_code_min"] == "") {
                                    $post["creditinvoice_code_min"] = $creditinvoice->getCreditInvoiceBeginEndCode("first");
                                } elseif($creditinvoice->is_free($post["creditinvoice_code_min"])) {
                                    $export->Error[] = __("invalid creditinvoice min");
                                }
                                if($post["creditinvoice_code_max"] == "") {
                                    $post["creditinvoice_code_max"] = $creditinvoice->getCreditInvoiceBeginEndCode("last");
                                } elseif($creditinvoice->is_free($post["creditinvoice_code_max"])) {
                                    $export->Error[] = __("invalid creditinvoice max");
                                }
                                break;
                            case "product_code":
                                require_once "class/product.php";
                                $product = new product();
                                if($post["product_code_min"] == "") {
                                    $post["product_code_min"] = $product->getProductBeginEndCode("first");
                                } elseif($product->is_free($post["product_code_min"])) {
                                    $export->Error[] = __("invalid productcode min");
                                }
                                if($post["product_code_max"] == "") {
                                    $post["product_code_max"] = $product->getProductBeginEndCode("last");
                                } elseif($product->is_free($post["product_code_max"])) {
                                    $export->Error[] = __("invalid productcode max");
                                }
                                break;
                        }
                    }
                }
                $_POST = $post;
                if(empty($export->Error)) {
                    sleep(1);
                    $type = isset($_POST["type"]) ? trim($_POST["type"]) : "";
                    $stats = $export->getSettings("statistics");
                    $filters = [$type => []];
                    $enabled_filters = isset($_POST["filters"]) ? $_POST["filters"] : [];
                    $tokens = [];
                    foreach ($enabled_filters as $k => $v) {
                        if(strpos($k, $type) === 0) {
                            $filters[$type] = [substr($k, strlen($type) + 1) => []];
                            $tokens[] = $k;
                        }
                    }
                    foreach ($_POST as $k => $v) {
                        $keys = explode("_", $k);
                        $f = array_pop($keys);
                        $key = implode("_", $keys);
                        if($f == "date") {
                            $v_prime = explode("-", $v);
                            $v = $v_prime[2] . "-" . $v_prime[1] . "-" . $v_prime[0];
                        }
                        if(in_array($key, $tokens)) {
                            $filters[$keys[0]][$keys[1]][$f] = trim($v);
                        }
                    }
                    if(isset($filters[$type]) && is_array($filters[$type])) {
                        foreach ($filters[$type] as $_key => $_val) {
                            if(strpos($_key, "date") !== false && (!$_val["min"] || !$_val["max"])) {
                                $export->Error[] = __("export error use start and end date for filter");
                            }
                        }
                    }
                    unset($tokens);
                    unset($enabled_filters);
                    unset($k);
                    unset($v);
                    unset($f);
                    if(empty($export->Error)) {
                        if(!in_array($type, ["sddbatches"])) {
                            $stats["value"][$type . "s"]["lastExport"] = time();
                            $stats["value"][$type . "s"]["filters"] = $filters;
                            $export->saveSettings("statistics", json_encode($stats["value"]));
                        }
                        $save_stats = false;
                        if($type == "invoice") {
                            $export->getInvoices($filters);
                        } elseif($type == "sddbatches") {
                            $save_stats = $export->exportSDDBatches($filters);
                        } elseif($type == "debtor") {
                            $export->getDebtors($filters);
                        } elseif($type == "creditor") {
                            $export->getCreditors($filters);
                        } elseif($type == "product") {
                            $export->getProducts($filters);
                        } elseif($type == "creditinvoice") {
                            $export->getCreditInvoices($filters);
                        }
                        if($save_stats) {
                            $stats["value"][$type . "s"]["lastExport"] = time();
                            $stats["value"][$type . "s"]["filters"] = $filters;
                            $export->saveSettings("statistics", json_encode($stats["value"]));
                        }
                        $_POST = [];
                    }
                }
            }
            if(isset($_GET["action"]) && $_GET["action"] == "stop") {
                $show_stop_dialog = true;
            }
            $package_information = $export->getVersionInformation();
            if(empty($_POST) && isset($_SESSION["module_version_information"]["accounting_export"][$package]) && $package_information["version"] < $_SESSION["module_version_information"]["accounting_export"][$package]["version"] && 0 <= version_compare(SOFTWARE_VERSION, $_SESSION["module_version_information"]["accounting_export"][$package]["minimal_version"])) {
                $reset_export_version_warning = true;
                $export->Warning[] = sprintf(__("module export accounting update available"), $package_information["name"]);
            }
            $message = parse_message($export);
            if(isset($reset_export_version_warning) && $reset_export_version_warning === true) {
                $export->Warning = [];
            }
            $wfh_page_title = __("export to") . " " . $package_information["name"];
            $current_page_url = "export.php?page=accounting_package&module=" . $package;
            $sidebar_template = "export.sidebar.php";
            require_once "3rdparty/export/view.export.php";
        }
        break;
    case "purchaseinvoice":
        if(!empty($_POST)) {
            Database_Model::getInstance()->get(["HostFact_Documents", "HostFact_CreditInvoice"], ["HostFact_CreditInvoice.id", "HostFact_CreditInvoice.`CreditInvoiceCode`", "HostFact_Documents.`Filename`", "HostFact_Documents.`FilenameServer`"])->where("HostFact_Documents.`Type`", "creditinvoice")->where("HostFact_Documents.`Reference`=HostFact_CreditInvoice.`id`")->groupBy("HostFact_CreditInvoice.id");
            if($_POST["CreditInvoiceCode_Start"] != "") {
                Database_Model::getInstance()->orWhere([["SUBSTRING(`CreditInvoiceCode`,1,1)", [">" => substr(esc($_POST["CreditInvoiceCode_Start"]), 0, 1)]], ["AND" => [["SUBSTRING(`CreditInvoiceCode`,1,1)", substr(esc($_POST["CreditInvoiceCode_Start"]), 0, 1)], ["LENGTH(`CreditInvoiceCode`)", [">" => strlen(esc($_POST["CreditInvoiceCode_Start"]))]]]], ["AND" => [["SUBSTRING(`CreditInvoiceCode`,1,1)", substr(esc($_POST["CreditInvoiceCode_Start"]), 0, 1)], ["LENGTH(`CreditInvoiceCode`)", strlen(esc($_POST["CreditInvoiceCode_Start"]))], ["CreditInvoiceCode", [">=" => esc($_POST["CreditInvoiceCode_Start"])]]]]]);
                if($_POST["CreditInvoiceCode_End"] != "") {
                    Database_Model::getInstance()->orWhere([["SUBSTRING(`CreditInvoiceCode`,1,1)", ["<" => substr(esc($_POST["CreditInvoiceCode_End"]), 0, 1)]], ["AND" => [["SUBSTRING(`CreditInvoiceCode`,1,1)", substr(esc($_POST["CreditInvoiceCode_End"]), 0, 1)], ["LENGTH(`CreditInvoiceCode`)", ["<" => strlen(esc($_POST["CreditInvoiceCode_End"]))]]]], ["AND" => [["SUBSTRING(`CreditInvoiceCode`,1,1)", substr(esc($_POST["CreditInvoiceCode_End"]), 0, 1)], ["LENGTH(`CreditInvoiceCode`)", strlen(esc($_POST["CreditInvoiceCode_End"]))], ["CreditInvoiceCode", ["<=" => esc($_POST["CreditInvoiceCode_End"])]]]]]);
                }
            }
            if(is_date(rewrite_date_site2db($_POST["Date_Start"])) && is_date(rewrite_date_site2db(esc($_POST["Date_End"])))) {
                Database_Model::getInstance()->where("Date", ["BETWEEN" => [rewrite_date_site2db(esc($_POST["Date_Start"])), rewrite_date_site2db(esc($_POST["Date_End"]))]]);
            }
            $result = Database_Model::getInstance()->execute();
            $rows = count($result);
            if($rows === 0) {
                $export->Error[] = __("no result for the selected period");
            } elseif(1000 < $rows) {
                $export->Error[] = sprintf(__("purchase invoice download to high max allowed x"), $rows, 1000);
            } else {
                $files_to_unlink = [];
                $zip = new ZipArchive();
                $zip_filename = __("purchase-invoice-zipped") . "-" . date("d-m-Y") . ".zip";
                if($zip->open("temp/" . $zip_filename, ZipArchive::CREATE)) {
                    require_once "class/attachment.php";
                    $attachment = new attachment();
                    foreach ($result as $invoice_obj) {
                        if(!file_exists($attachment->fileDir($invoice_obj->id, "creditinvoice") . $invoice_obj->FilenameServer)) {
                            $export->Error[] = sprintf(__("export purchase invoice file does not exist"), $invoice_obj->CreditInvoiceCode, $attachment->fileDir($invoice_obj->id, "creditinvoice") . $invoice_obj->FilenameServer);
                        } else {
                            $zip->addFile($attachment->fileDir($invoice_obj->id, "creditinvoice") . $invoice_obj->FilenameServer, $invoice_obj->CreditInvoiceCode . "-" . $invoice_obj->Filename);
                        }
                    }
                    $num_files = $zip->numFiles;
                    $zip->close();
                }
                if(0 < $num_files) {
                    $_SESSION["force_download"] = $zip_filename;
                }
                flashmessage($export);
                header("Location: export.php?page=purchaseinvoice");
                exit;
            }
        }
        $message = parse_message($export);
        $wfh_page_title = __("export purchase invoice header title");
        $current_page_url = "export.php";
        $sidebar_template = "export.sidebar.php";
        require_once "views/export.purchase.invoice.php";
        break;
    default:
        if(!U_EXPORT_SHOW) {
        } else {
            require_once "3rdparty/export/class.export.php";
            $export = new export();
            $available_package_list = $_SESSION["module_version_information"]["accounting_export"];
            $available_package_list = $export->getAvailablePackages($available_package_list);
            $package_list = $export->getPackages();
            $wfh_page_title = __("export");
            $message = parse_message($export);
            $current_page_url = "export.php";
            $sidebar_template = "export.sidebar.php";
            require_once "views/export.overview.php";
        }
}

?>