<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "config.php";
checkRight(U_PAYMENT_SHOW);
$page = isset($_GET["page"]) ? $_GET["page"] : "payment";
$paymentmethod_id = isset($_GET["id"]) ? intval(esc($_GET["id"])) : (isset($_POST["id"]) ? intval(esc($_POST["id"])) : NULL);
switch ($page) {
    case "settings":
        if(!empty($_POST)) {
            $settings = new settings();
            foreach ($_POST as $key => $value) {
                $settings->Variable = esc($key);
                $settings->Value = esc($value);
                $settings->edit();
            }
        }
        if(empty($settings->Error)) {
            $settings->Success[] = __("settings are modified");
        }
        flashMessage($settings);
        header("Location: paymentmethods.php");
        exit;
        break;
    case "delete":
        if(!empty($_POST) && U_PAYMENT_EDIT && 0 < $paymentmethod_id && isset($_POST["imsure"]) && $_POST["imsure"] == "yes") {
            require_once "class/paymentmethod.php";
            $paymentmethod = new paymentmethod();
            $paymentmethod->Identifier = $paymentmethod_id;
            $paymentmethod->show();
            $paymentmethod->delete();
            flashMessage($paymentmethod);
        }
        header("Location: paymentmethods.php");
        exit;
        break;
    default:
        if(isset($_GET["action"]) && $_GET["action"] == "add") {
            $page = "add";
        } elseif(isset($_GET["action"]) && $_GET["action"] == "edit") {
            $page = "edit";
        }
        if(!empty($_POST) && isset($_POST["Title"])) {
            require_once "class/paymentmethod.php";
            $paymentmethod = new paymentmethod();
            if(0 < $paymentmethod_id) {
                $paymentmethod->Identifier = $paymentmethod_id;
                $paymentmethod->show();
            }
            foreach ($_POST as $key => $value) {
                if(in_array($key, $paymentmethod->Variables)) {
                    $paymentmethod->{$key} = esc($value);
                }
            }
            if(isset($_POST["PaymentType"]) && !in_array($_POST["PaymentType"], ["", "wire", "auth"])) {
                $paymentmethod->Directory = esc($_POST["PaymentType"]);
            }
            if($paymentmethod->Directory == "paypal") {
                $paymentmethod->PaymentType = "paypal";
            } elseif(strpos($paymentmethod->Directory, "ideal") !== false) {
                $paymentmethod->PaymentType = "ideal";
            } elseif($paymentmethod->Directory != "" && !in_array($paymentmethod->PaymentType, ["wire", "auth"])) {
                $paymentmethod->PaymentType = "other";
            }
            $paymentmethod->FeeAmount = deformat_money($paymentmethod->FeeAmount);
            if($_POST["FeeType_helper"] == "no") {
                $paymentmethod->FeeType = "";
            } elseif($_POST["FeeType_helper"] == "discount") {
                $paymentmethod->FeeAmount = -1 * $paymentmethod->FeeAmount;
            }
            if($paymentmethod->FeeType == "EUR" && !empty($array_taxpercentages) && VAT_CALC_METHOD == "incl") {
                $paymentmethod->FeeAmount = $paymentmethod->FeeAmount / (1 + STANDARD_TAX);
            }
            if(0 < $paymentmethod_id) {
                $result = $paymentmethod->edit();
                $page = "edit";
            } else {
                $result = $paymentmethod->add();
                $page = "add";
            }
            if($result) {
                if($paymentmethod->PaymentType == "auth") {
                    $settings_to_update = [];
                    if(SDD_ID != "" && (SDD_ID != $_POST["SDD_ID"] || !isset($_POST["sdd_helper"])) && isset($_POST["change_sepa_type_value"]) && $_POST["change_sepa_type_value"] == "sepa_type_first") {
                        Database_Model::getInstance()->update("HostFact_SDD_Mandates", ["MandateType" => "FRST"])->where("MandateType", "RCUR")->where("Status", "active")->execute();
                        $error_class->Success[] = __("mandate type changed for all debtor to first");
                    }
                    $settings_to_update["SDD_ID"] = esc($_POST["SDD_ID"]);
                    $settings_to_update["SDD_TYPE"] = isset($_POST["SDD_TYPE"]) && in_array($_POST["SDD_TYPE"], ["CORE", "B2B"]) ? esc($_POST["SDD_TYPE"]) : "CORE";
                    $settings_to_update["SDD_DAYS"] = esc(implode(",", array_unique($_POST["SDD_DAYS"])));
                    $settings_to_update["SDD_MAIL_NOTIFY"] = isset($_POST["SDD_MAIL_NOTIFY"]) && $_POST["SDD_MAIL_NOTIFY"] == "yes" ? "yes" : "no";
                    $settings_to_update["SDD_NOTICE"] = is_numeric($_POST["SDD_NOTICE"]) && intval($_POST["SDD_NOTICE"]) == $_POST["SDD_NOTICE"] ? esc($_POST["SDD_NOTICE"]) : "14";
                    $settings_to_update["SDD_PROCESSING_RCUR"] = is_numeric($_POST["SDD_PROCESSING_RCUR"]) && intval($_POST["SDD_PROCESSING_RCUR"]) == $_POST["SDD_PROCESSING_RCUR"] ? esc($_POST["SDD_PROCESSING_RCUR"]) : "2";
                    $settings_to_update["SDD_LIMIT_TRANSACTION"] = isset($_POST["SDD_limit_helper"]) && $_POST["SDD_limit_helper"] == "yes" ? deformat_money(esc($_POST["SDD_LIMIT_TRANSACTION"])) : "";
                    $settings_to_update["SDD_LIMIT_BATCH"] = isset($_POST["SDD_limit_helper"]) && $_POST["SDD_limit_helper"] == "yes" ? deformat_money(esc($_POST["SDD_LIMIT_BATCH"])) : "";
                    $settings_to_update["SDD_IBAN"] = isset($_POST["SDD_iban_bic_helper"]) && $_POST["SDD_iban_bic_helper"] == "yes" ? esc($_POST["SDD_IBAN"]) : "";
                    $settings_to_update["SDD_BIC"] = isset($_POST["SDD_iban_bic_helper"]) && $_POST["SDD_iban_bic_helper"] == "yes" ? esc($_POST["SDD_BIC"]) : "";
                    $settings_to_update["SDD_MOVED_MAIL"] = esc($_POST["SDD_MOVED_MAIL"]);
                    $settings_to_update["SDD_FAILED_MAIL"] = esc($_POST["SDD_FAILED_MAIL"]);
                    if($settings_to_update["SDD_NOTICE"] <= $settings_to_update["SDD_PROCESSING_RCUR"]) {
                        $settings_to_update["SDD_NOTICE"] = max(14, $settings_to_update["SDD_PROCESSING_RCUR"] + 1);
                        $error_class->Warning[] = __("sdd notice days must be greater than processing days");
                    }
                    $settings = new settings();
                    foreach ($settings_to_update as $k => $v) {
                        $settings->Variable = $k;
                        $settings->Value = $v;
                        $settings->edit();
                    }
                    require_once "class/directdebit.php";
                    $directdebit = new directdebit();
                    $company_IBAN = $settings_to_update["SDD_IBAN"] ? $settings_to_update["SDD_IBAN"] : $company->AccountNumber;
                    if(!$directdebit->checkIBAN($company_IBAN)) {
                        $error_class->Warning[] = sprintf(__("sdd error invalid iban x for company"), $company_IBAN);
                    }
                    if($_POST["SDD_ID"] != "") {
                        $_SESSION["updateSDDData"] = true;
                    }
                }
                flashMessage($paymentmethod);
                header("Location: paymentmethods.php");
                exit;
            } else {
                foreach ($paymentmethod->Variables as $key) {
                    $paymentmethod->{$key} = htmlspecialchars($paymentmethod->{$key});
                }
            }
        }
        switch ($page) {
            case "add":
            case "edit":
                if(!isset($paymentmethod) || !is_object($paymentmethod)) {
                    require_once "class/paymentmethod.php";
                    $paymentmethod = new paymentmethod();
                }
                if($paymentmethod_id && empty($paymentmethod->Error)) {
                    $paymentmethod->Identifier = $paymentmethod_id;
                    $paymentmethod->show();
                }
                $payment_types = $paymentmethod->get_types([0, 1, 2, 3]);
                require_once "class/template.php";
                $fields = ["Name"];
                $emailtemplate = new emailtemplate();
                $emailtemplates = $emailtemplate->all($fields);
                $wfh_page_title = 0 < $paymentmethod->Identifier ? sprintf(__("edit paymentmethod"), $paymentmethod->Title) : __("add paymentmethod");
                $message = parse_message($paymentmethod);
                $page = "payment";
                $sidebar_template = "settings.sidebar.php";
                require_once "views/paymentmethod.add.php";
                break;
            default:
                if(isset($_SESSION["updateSDDData"]) && $_SESSION["updateSDDData"] === true) {
                    unset($_SESSION["updateSDDData"]);
                    require_once "class/directdebit.php";
                    $directdebit = new directdebit();
                    $directdebit->cronDirectDebit();
                }
                if(!isset($paymentmethod) || !is_object($paymentmethod)) {
                    require_once "class/paymentmethod.php";
                    $paymentmethod = new paymentmethod();
                }
                $payment_methods = $paymentmethod->all();
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, IDEAL_EMAIL . "backoffice.php");
                settings::disableSSLVerificationIfNeeded($ch);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, "10");
                $content = curl_exec($ch);
                $curl_error = curl_error($ch);
                $curl_info = curl_getinfo($ch);
                curl_close($ch);
                if(json_decode($content) === NULL) {
                    $paymentmethod->Warning[] = __("not possible to add extra paymentmethods");
                    if($curl_error) {
                        $paymentmethod->Error[] = "cURL error: " . $curl_error;
                    } else {
                        $curl_debug = "";
                        if($curl_info && isset($curl_info["http_code"])) {
                            $curl_debug .= "URL: " . $curl_info["url"] . "<br />" . "HTTP status code: " . $curl_info["http_code"] . "<br />" . "Redirect: " . ($curl_info["redirect_url"] ? $curl_info["redirect_url"] : "-") . "<br />" . "IP: " . $curl_info["primary_ip"] . "<br />";
                        }
                        $curl_debug .= "Response preview: " . mb_substr(htmlspecialchars($content), 0, 500);
                        if($curl_debug) {
                            $paymentmethod->Warning[] = "<br />" . $curl_debug;
                        }
                    }
                }
                $message = parse_message($paymentmethod);
                $wfh_page_title = __("settings") . " - " . __("paymentmethod overview");
                $sidebar_template = "settings.sidebar.php";
                require_once "views/paymentmethod.overview.php";
        }
}

?>