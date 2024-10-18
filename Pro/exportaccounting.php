<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "config.php";
$page = isset($_GET["page"]) ? $_GET["page"] : "index";
class ExportAccounting_Controller extends Base_Controller
{
    public function __construct(Template_Helper $template)
    {
        parent::__construct();
        $this->template = $template;
        $this->template->setSidebar("export.sidebar");
        $this->template->current_page_url = "exportaccounting.php";
        checkRight(U_EXPORT_EDIT);
        $this->package = isset($_GET["module"]) ? strtolower($_GET["module"]) : "";
        if($this->package && @file_exists("3rdparty/export/" . $this->package . "/" . $this->package . "_new.php") && @file_exists("3rdparty/export/" . $this->package . "/version.php")) {
            require_once "3rdparty/export/" . $this->package . "/" . $this->package . "_new.php";
            $className = "export_accounting_package_" . $this->package;
            $this->export = new $className();
            $version = $this->export->getVersionInformation();
            $this->template->wfh_page_title = __("export to") . " " . $version["name"];
        } else {
            header("Location: " . url_generator("export"));
            exit;
        }
    }
    public function index()
    {
        if(isset($_GET["action"]) && $_GET["action"] == "stop") {
            $this->template->show_stop_dialog = true;
        } elseif(isset($_GET["action"]) && $_GET["action"] == "start") {
            if(!$this->export->startTrial()) {
                flashMessage($this->export);
                header("Location: " . url_generator("export"));
                exit;
            }
            header("Location: " . url_generator("exportaccounting", false, ["module" => $this->package]));
            exit;
        }
        if(!$this->export->checkLicense()) {
            if($this->export->getLicenseStatus() == "EXPIRED") {
                $_action = isset($_GET["action"]) && $_GET["action"] == "stop" ? ["action" => "stop"] : [];
                header("Location: " . url_generator("exportaccounting", "exportaccounting_order", ["module" => $this->package], $_action));
                exit;
            }
            flashMessage($this->export);
            header("Location: " . url_generator("export"));
            exit;
        }
        $mode = isset($_POST["mode"]) ? $_POST["mode"] : "";
        if($mode == "saveSettings" && isset($this->export->hasOAuth) && $this->export->hasOAuth === true) {
            $groups = [];
            if(isset($_POST["group"]) && isset($_POST["toggleCustomLedgerTable"])) {
                $group_list = $this->export->getProductGroups();
                foreach ($group_list as $tmp_group) {
                    $groups[$tmp_group["id"]] = ["id" => NULL];
                }
                foreach ($_POST["group"] as $k => $v) {
                    $groups[$k]["id"] = trim(esc($v));
                }
            }
            $purchase_groups = [];
            if(isset($_POST["purchase_group"]) && isset($_POST["togglePurchaseLedgerTable"])) {
                $group_list = $this->export->getCreditorGroups();
                foreach ($group_list as $tmp_group) {
                    $purchase_groups[$tmp_group["id"]] = ["id" => NULL];
                }
                foreach ($_POST["purchase_group"] as $k => $v) {
                    $purchase_groups[$k]["id"] = trim(esc($v));
                }
            }
            $settings = $this->export->getSettings("ledgerAccounts");
            if(isset($_POST["accounts"])) {
                foreach ($_POST["accounts"] as $k => $v) {
                    $settings["value"]["default"][$k]["id"] = trim(esc($v));
                }
            }
            $settings["value"]["custom"] = $groups;
            $settings["value"]["purchase_groups"] = $purchase_groups;
            $this->export->saveSettings("ledgerAccounts", json_encode($settings["value"]));
            if(method_exists($this->export, "saveOtherSettings")) {
                $this->export->saveOtherSettings();
            }
            if(!empty($this->export->Error)) {
                generate_flash_message($this->export);
                header("Location: " . url_generator("exportaccounting", false, ["module" => $this->package], ["oauth" => "settings"]));
                exit;
            }
            $this->export->Success[] = __("settings all adjusted");
            generate_flash_message($this->export);
            header("Location: " . url_generator("exportaccounting", false, ["module" => $this->package]));
            exit;
        } elseif(isset($_GET["import"]) && $_GET["import"]) {
            switch ($_GET["import"]) {
                case "debtors":
                    $this->export->importDebtors();
                    break;
                default:
                    header("Location: " . url_generator("exportaccounting", false, ["module" => $this->package]));
                    exit;
            }
        } else {
            if(isset($this->export->hasOAuth) && $this->export->hasOAuth === true && (isset($this->export->configuredOAuth) && $this->export->configuredOAuth === false || isset($_GET["oauth"]))) {
                $template = "export.oauth";
            } elseif((isset($_GET["fillhistory"]) && $_GET["fillhistory"] == "yes" || !($activated_since_date = $this->export->getSettingsValue("activated_since_date"))) && method_exists($this->export, "fillExportHistory")) {
                $template = "export.fillhistory";
                if(isset($_GET["fillhistory"]) && $_GET["fillhistory"] == "yes") {
                    $start_date = isset($_GET["start_date"]) && $_GET["start_date"] ? $_GET["start_date"] : date("Y") . "-01-01";
                    $this->export->fillExportHistory($start_date, ["debtor", "invoice", "creditor", "creditinvoice", "product"]);
                    $this->export->saveSettings("activated_since_date", json_encode(["start_date" => $start_date, "activation_date" => date("Y-m-d")]));
                    generate_flash_message($this->export);
                    exit("OK");
                }
            } else {
                $template = "export.dashboard";
                $statistics = $this->export->getStatistics();
                $manual_actions = $this->export->getManualActions();
                $paid_in_hostfact_diffs = $this->export->getPaidInHostFactDiffs();
                if(!empty($manual_actions)) {
                    $this->export->Warning[] = __("export accounting - one or more manual actions") . " " . sprintf("<a onclick=\"\$('html,body').animate({scrollTop: \$('#manual_action_block_div').offset().top},'slow');\">%s</a>", __("export accounting - manual action view them"));
                }
                $globalErrors = $this->export->getSettingsValue("globalErrors");
                if(!empty($globalErrors["errors"]) && !isset($_SESSION["flashMessage"]["Error"])) {
                    foreach ($globalErrors["errors"] as $_err) {
                        $this->export->Error = array_merge($this->export->Error, $_err);
                    }
                }
                foreach ($statistics as $_type => $_info) {
                    if(!$this->export->hasAnyItems($_type)) {
                        $statistics[$_type]["has_no_items"] = true;
                    }
                }
                $this->template->statistics = $statistics;
                $this->template->manual_actions = $manual_actions;
                $this->template->paid_in_hostfact_diffs = $paid_in_hostfact_diffs;
            }
            $officecode = $this->export->getSettingsValue("officecode");
            if($officecode) {
                $this->template->office_name = $officecode["officename"];
            }
            if(!empty($this->export->Error)) {
                $this->export->Error = array_unique($this->export->Error);
            }
            $this->template->export = $this->export;
            $this->template->dashboard_groups = $this->export->getDashboardGroups();
            $this->template->package_information = $this->export->getVersionInformation();
            $this->template->package = $this->package;
            $this->template->message_box = generate_message_box($this->export);
            $this->template->show($template);
        }
    }
    public function exportaccounting_modal()
    {
        $export_type = isset($_GET["type"]) && isset($this->export->supported[$_GET["type"]]) && $this->export->supported[$_GET["type"]] === true ? $_GET["type"] : false;
        $statistics = $this->export->getStatistics();
        $this->template->statistics = $statistics;
        $item_ids = [];
        if(isset($_GET["retry"])) {
            if($_GET["retry"] && isset($this->export->supported[$_GET["retry"]]) && $this->export->supported[$_GET["retry"]] === true) {
                $export_type = $_GET["retry"];
                $manual_actions = $this->export->getManualActions($export_type);
            } elseif(!$_GET["retry"]) {
                $export_type = "all";
                $manual_actions = $this->export->getManualActions();
            } else {
                exit("reload");
            }
            $grouped_item_ids = [];
            foreach ($manual_actions as $_action) {
                $grouped_item_ids[$_action->Type][] = $_action->ReferenceID;
            }
            foreach ($grouped_item_ids as $export_type => $ids) {
                $this->export->saveStatistics($export_type, "retry_all");
            }
            $_SESSION["export_remaining_items"][$this->package] = $grouped_item_ids;
            $this->template->grouped_item_ids = $grouped_item_ids;
        } elseif(isset($_POST["Filter"]) && $_POST["Filter"]) {
            if($_POST["Filter"] == "export_needed") {
                $item_ids = array_keys($statistics[$export_type]["export_needed"]);
                if(!empty($item_ids)) {
                    $this->export->saveStatistics($export_type, "export_needed");
                }
            } elseif($_POST["Filter"] == "manual") {
                if($_POST["FilterBy"] == "date" && isset($_POST["Date"]["Min"]) && isset($_POST["Date"]["Max"])) {
                    $item_ids = $this->export->filterItemsByMinMax($export_type, "date", date("Y-m-d", strtotime(rewrite_date_site2db($_POST["Date"]["Min"]))), date("Y-m-d", strtotime(rewrite_date_site2db($_POST["Date"]["Max"]))));
                } elseif($_POST["FilterBy"] == "code" && isset($_POST["Code"]["Min"]) && isset($_POST["Code"]["Max"])) {
                    $item_ids = $this->export->filterItemsByMinMax($export_type, "code", trim($_POST["Code"]["Min"]), trim($_POST["Code"]["Max"]));
                }
            }
            if(!empty($item_ids)) {
                $_SESSION["export_remaining_items"][$this->package][$export_type] = $item_ids;
                $this->template->grouped_item_ids = [$export_type => $item_ids];
            }
        }
        $this->template->available_filters = $this->export->getAvailableFilter($export_type);
        $this->template->package = $this->package;
        $this->template->export_type = $export_type;
        $this->template->message_box = generate_message_box($this->export);
        $this->template->element("export.modal");
        exit;
    }
    public function exportaccounting_import()
    {
        $export_type = isset($_GET["type"]) && isset($this->export->supported[$_GET["type"]]) && $this->export->supported[$_GET["type"]] === true ? $_GET["type"] : false;
        if(isset($_GET["import"]) && isset($_GET["start"])) {
            switch ($_GET["import"]) {
                case "debtor":
                    $this->export->importDebtors();
                    break;
                default:
                    exit("OK");
            }
        } else {
            $this->template->package = $this->package;
            $this->template->package_information = $this->export->getVersionInformation();
            $this->template->export_type = $export_type;
            $this->template->message_box = generate_message_box($this->export);
            $this->template->element("export.import");
            exit;
        }
    }
    public function exportaccounting_export()
    {
        delete_stats_summary();
        $loading_type = "modal";
        $export_type = isset($_GET["type"]) && isset($this->export->supported[$_GET["type"]]) && $this->export->supported[$_GET["type"]] === true ? $_GET["type"] : false;
        if(isset($_GET["retry"]) && $_GET["retry"]) {
            if($_GET["type"] == "payment_invoice" || $_GET["type"] == "payment_purchase") {
                $export_type = $_GET["type"];
                $remaining_ids = [$_GET["retry"]];
                $loading_type = "inline";
            } else {
                $statistics = $this->export->getStatistics();
                $item_ids = array_keys($statistics[$export_type]["export_needed"]);
                $remaining_ids = in_array($_GET["retry"], $item_ids) ? [$_GET["retry"]] : [];
                $loading_type = "inline";
                if(!empty($remaining_ids)) {
                    $this->export->saveStatistics($export_type, "retry");
                }
            }
        } else {
            if(isset($_GET["ignore"]) && $_GET["ignore"]) {
                $this->export->ignoreManualAction($export_type, $_GET["ignore"]);
                exit;
            }
            $remaining_ids = $_SESSION["export_remaining_items"][$this->package][$export_type];
        }
        $_attempt_date = date("Y-m-d H:i:s");
        if(!empty($remaining_ids)) {
            $this->export->removeGlobalErrors();
            if(isset($_SESSION["export_error_container"][$this->package][$export_type])) {
                $this->export->errorContainer = $_SESSION["export_error_container"][$this->package][$export_type];
                unset($_SESSION["export_error_container"][$this->package][$export_type]);
            }
            switch ($export_type) {
                case "debtor":
                    $remaining_ids = $this->export->exportDebtors($remaining_ids);
                    break;
                case "invoice":
                    $remaining_ids = $this->export->exportInvoices($remaining_ids);
                    break;
                case "creditor":
                    $remaining_ids = $this->export->exportCreditors($remaining_ids);
                    break;
                case "creditinvoice":
                    $remaining_ids = $this->export->exportCreditInvoices($remaining_ids);
                    break;
                case "product":
                    $remaining_ids = $this->export->exportProducts($remaining_ids);
                    break;
                case "sddbatch":
                    $remaining_ids = $this->export->exportSDDBatches($remaining_ids);
                    break;
                case "payment_invoice":
                    $remaining_ids = $this->export->importPayments($remaining_ids);
                    break;
                case "payment_purchase":
                    $remaining_ids = $this->export->importPurchasePayments($remaining_ids);
                    break;
                default:
                    if($remaining_ids === false || !empty($this->export->Error)) {
                        if($this->export->ErrorType != "none") {
                            $errors_export_types = [];
                            $errors_export_types[$this->export->ErrorType] = $this->export->Error;
                            $this->export->setGlobalErrors($errors_export_types);
                        }
                        if(!empty($this->export->Error)) {
                            $this->export->Error = array_unique($this->export->Error);
                        }
                        generate_flash_message($this->export);
                        echo json_encode("error");
                        exit;
                    }
            }
        }
        if(!empty($remaining_ids) && isset($this->export->errorContainer)) {
            $_SESSION["export_error_container"][$this->package][$export_type] = $this->export->errorContainer;
        }
        $_SESSION["export_remaining_items"][$this->package][$export_type] = $remaining_ids;
        if($loading_type == "inline") {
            $result = [];
            if(0 < $this->export->errorsReported) {
                $result["Status"] = "error";
                $manual_action = $this->export->getManualAction($export_type, $_GET["retry"]);
                ob_start();
                $this->template->package = $this->package;
                $this->template->package_information = $this->export->getVersionInformation();
                $this->template->statistics = $statistics;
                $this->template->_item = $manual_action;
                $this->template->export = $this->export;
                $this->template->element("export.manual.block");
                $html = ob_get_clean();
                $result["Message"] = $html;
            } else {
                $result["Status"] = "success";
            }
            $result["DateText"] = $this->export->showLastExportDate($_attempt_date);
            echo json_encode($result);
            exit;
        }
        if(!empty($this->export->Error)) {
            $this->export->Error = array_unique($this->export->Error);
        }
        generate_flash_message($this->export);
        echo json_encode(["remaining_ids" => count($remaining_ids), "errors_reported" => $this->export->errorsReported, "items_exported" => $this->export->itemsExported]);
        exit;
    }
    public function exportaccounting_payment()
    {
        delete_stats_summary();
        $this->export->importPayments();
        $this->export->importPurchasePayments();
        generate_flash_message($this->export);
        header("Location: " . url_generator("exportaccounting", false, ["module" => $this->package]));
        exit;
    }
    public function exportaccounting_order()
    {
        if(isset($_POST["OrderConfirmation"]) && $_POST["OrderConfirmation"] == "yes") {
            $this->export->orderSubscription();
            generate_flash_message($this->export);
            header("Location: " . url_generator("exportaccounting", false, ["module" => $this->package]));
            exit;
        }
        if(isset($_GET["action"]) && $_GET["action"] == "stop") {
            $this->template->show_stop_dialog = true;
        }
        $this->template->ExpiredDaysTillSubscription = $_SESSION[$this->package . "-expired"]["ExpiredDaysTillSubscription"];
        $this->template->ExpiredMessage = $_SESSION[$this->package . "-expired"]["ExpiredMessage"];
        $this->template->ExpiredSubscriptionStart = $_SESSION[$this->package . "-expired"]["ExpiredSubscriptionStart"];
        $this->template->package_information = $this->export->getVersionInformation();
        $this->template->export = $this->export;
        $this->template->package = $this->package;
        $this->template->message_box = generate_message_box($this->export);
        $this->template->show("export.expired");
    }
    public function exportaccounting_end()
    {
        if(isset($_POST["Reason"])) {
            if(isset($_POST["imsure"]) && $_POST["imsure"] == "yes") {
                $this->export->endSubscription($_POST["Reason"]);
                flashMessage($this->export);
                echo "<script type=\"text/javascript\">location.href='" . url_generator("export") . "';</script>";
                exit;
            }
            $this->export->Error[] = "U dient te bevestigen dat u de koppeling wilt stopzetten.";
        }
        $this->template->package_information = $this->export->getVersionInformation();
        $this->template->export = $this->export;
        $this->template->package = $this->package;
        $this->template->message_box = generate_message_box($this->export);
        $this->template->element("export.endmodule");
    }
    public function exportaccounting_disconnect_oauth()
    {
        delete_stats_summary();
        $this->template->package_information = $this->export->getVersionInformation();
        $this->template->package = $this->package;
        $this->template->element("export.disconnect");
    }
}
$template = new Template_Helper();
$exportAccounting = new ExportAccounting_Controller($template);
$exportAccounting->router($page);

?>