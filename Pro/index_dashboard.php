<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
if(isset($_POST["UserName"])) {
    require_once "login.php";
    exit;
}
define("INDEXPAGE", true);
require_once "config.php";
if(isset($_SESSION["UserPro"])) {
    $account = isset($account) ? $account : new employee();
    $account->Identifier = $_SESSION["UserPro"];
    $account->show();
}
require_once "class/invoice.php";
require_once "class/pricequote.php";
require_once "class/creditinvoice.php";
require_once "class/agenda.php";
require_once "class/statistic.php";
require_once "class/widget.php";
$widget = new widget();
$action = isset($_GET["action"]) && $_GET["action"] ? esc($_GET["action"]) : (isset($_POST["action"]) && $_POST["action"] ? esc($_POST["action"]) : NULL);
$page = isset($_GET["page"]) && $_GET["page"] ? esc($_GET["page"]) : NULL;
switch ($action) {
    case "download_patch":
        $url = INTERFACE_URL_CUSTOMERPANEL . "/download_patch.php?license=" . urlencode(LICENSE) . "&from=" . SOFTWARE_VERSION . "&download=" . htmlspecialchars($_GET["version"]) . (function_exists("phpversion") ? "&php=" . phpversion() : "");
        require_once "class/autopatcher.php";
        $autoPatcher = new AutoPatcher();
        if(!$autoPatcher->patchFile($url, $_GET["filehash"])) {
            $autoPatcher->Error[] = sprintf(__("auto updater not possible - manual"), "[hyperlink_1]" . $url . "[hyperlink_2]" . __("auto updater not possible - manual click") . "[hyperlink_3]");
        }
        flashMessage($autoPatcher);
        header("Location:?");
        exit;
        break;
    case "addwidget":
        require_once "class/widget.php";
        $widget = new widget();
        $widget->Identifier = intval($_POST["widgetID"]);
        $widget->Options = isset($_POST["options"]) ? $_POST["options"] : false;
        $widget->add();
        flashMessage($widget);
        header("Location:?");
        exit;
        break;
    case "editwidget":
        require_once "class/widget.php";
        $widget = new widget();
        $widget->Identifier = intval($_POST["widgetID"]);
        $widget->Options = isset($_POST["options"]) ? $_POST["options"] : false;
        $widget->edit();
        flashMessage($widget);
        header("Location:?");
        exit;
        break;
    case "removewidget":
        require_once "class/widget.php";
        $widget = new widget();
        $widget->Identifier = intval($_POST["widgetID"]);
        $widget->remove();
        flashMessage($widget);
        header("Location:?");
        exit;
        break;
    default:
        if(U_INVOICE_SHOW) {
            if(!isset($_POST["tableID"]) || $_POST["tableID"] == "InvoicesWaiting") {
                $invoice_waiting = [];
                $invoice_waiting["show"] = $account->Preferences["home"]["invoice_waiting"]["Value"];
                $invoice_waiting["order"] = $account->Preferences["home"]["invoice_waiting"]["Order"];
            }
            if(!isset($_POST["tableID"]) || $_POST["tableID"] == "InvoicesOpen") {
                $invoice_open = [];
                $invoice_open["show"] = $account->Preferences["home"]["invoice_open"]["Value"];
                $invoice_open["order"] = $account->Preferences["home"]["invoice_open"]["Order"];
            }
            if(!isset($_POST["tableID"]) || $_POST["tableID"] == "InvoicesWaitingC") {
                $invoice_waiting_c = [];
                $invoice_waiting_c["show"] = $account->Preferences["home"]["invoice_waiting_c"]["Value"];
                $invoice_waiting_c["order"] = $account->Preferences["home"]["invoice_waiting_c"]["Order"];
            }
        }
        if(U_PRICEQUOTE_SHOW) {
            $pricequote = new pricequote();
            $fields = ["PriceQuoteCode", "CompanyName", "Initials", "SurName", "Debtor", "Date", "SentDate", "AmountExcl", "AmountIncl", "ReferenceNumber"];
            $pricequote_open = $pricequote->all($fields, "", "", "", "", "", "2");
            $pricequote_waiting = $pricequote->all($fields, "", "", "", "", "", "1");
            $pricequote_concept = $pricequote->all($fields, "", "", "", "", "", "0");
        }
        if(U_CREDITOR_INVOICE_SHOW && (!isset($_POST["tableID"]) || $_POST["tableID"] == "CreditInvoices")) {
            $creditinvoice = new creditinvoice();
            $fields = ["CreditInvoiceCode", "CompanyName", "Initials", "SurName", "Creditor", "Date", "PayDate", "Status", "AmountExcl", "AmountIncl", "AmountPaid", "Location", "InvoiceCode", "Authorisation", "ReferenceNumber"];
            $session = isset($_SESSION["invoice.dashboard.creditinvoice"]) ? $_SESSION["invoice.dashboard.creditinvoice"] : [];
            $sort = isset($session["sort"]) ? $session["sort"] : "InvoiceCode";
            $order = isset($session["order"]) ? $session["order"] : "ASC";
            $limit = -1;
            $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : MAX_RESULTS_LIST;
            $current_page = $limit;
            $searchat = isset($session["searchat"]) ? $session["searchat"] : "InvoiceCode|Debtor";
            $searchfor = isset($session["searchfor"]) ? $session["searchfor"] : "";
            $group_id = "0|1|2";
            $creditinvoice->page_total_method = "all_results_open_amount";
            $creditinvoice = $creditinvoice->all($fields, $sort, $order, $limit, $searchat, $searchfor, $group_id, $show_results);
            $creditinvoice["show"] = $account->Preferences["home"]["creditinvoice"]["Value"];
            $creditinvoice["order"] = $account->Preferences["home"]["creditinvoice"]["Order"];
            $_SESSION["invoice.dashboard.creditinvoice"] = ["sort" => $sort, "order" => $order, "results" => $show_results];
        }
        if(!isset($_SESSION["hostfact_checks"]["cronjob_lastdate"]) && isset($_SESSION["index_cronjob"]["ready"]) && $_SESSION["index_cronjob"]["ready"] == 1) {
            require_once "class/template.php";
            $template = new template();
            if(!$template->getStandard("invoice")) {
                $error_class->Warning[] = __("there is no default template for invoices");
            }
            if(!$template->getStandard("pricequote")) {
                $error_class->Warning[] = __("there is no default template for pricequotes");
            }
            $templates = $template->all(["Name", "EmailTemplate", "Type"]);
            foreach ($templates as $k => $v) {
                if(is_numeric($k) && empty($v["EmailTemplate"]) && $v["Type"] != "other") {
                    $error_class->Warning[] = sprintf(__("template x has no emailtemplate"), $v["Name"]);
                }
            }
            if(ORDER_ACCEPT_WELCOME_MAIL == 1 && !WELCOME_MAIL) {
                $error_class->Warning[] = __("no welcome mail setup");
            }
            if(!REMINDER_MAIL) {
                $error_class->Warning[] = __("no reminder mail setup");
            }
            if(INT_SUPPORT_SUMMATIONS && !SUMMATION_MAIL) {
                $error_class->Warning[] = __("no summation mail setup");
            }
            if(!check_email_address(TICKET_NOTIFY_EMAILADDRESS) && 0 < TICKET_NOTIFY) {
                $error_class->Warning[] = __("no emailaddress given for ticket notifications");
            }
            $pro_url = str_replace("www.", "", $_SERVER["SERVER_NAME"]) . $_SERVER["REQUEST_URI"];
            $hostfact_const_url = str_replace(["www.", "http://", "https://"], "", BACKOFFICE_URL);
            if(strpos($pro_url, $hostfact_const_url) === false) {
                $error_class->Error[] = __("your url settings seems to be incorrect");
            }
            if(CRONJOB_LASTDATE && CRONJOB_LASTDATE < date("Y-m-d H:i:s", strtotime("-24 hours")) && INSTALL_DATE < date("Ymd", strtotime("-2 days"))) {
                $error_class->Warning[] = __("cronjob run has been more than 24 hours ago") . " [hyperlink_1]settings.php?page=automation[hyperlink_2]" . __("cronjob run view link") . "[hyperlink_3]";
            }
            $_SESSION["hostfact_checks"]["cronjob_lastdate"] = true;
            $memory_limit = @ini_get("memory_limit");
            if($memory_limit && $memory_limit != -1 && (substr($memory_limit, -1) == "M" && substr($memory_limit, 0, -1) < 128 || substr($memory_limit, -1) == "K")) {
                $error_class->Warning[] = sprintf(__("memory limit under default php setting"), $memory_limit);
            }
        }
        $selected_widgets = $widget->all(true);
        $all_widgets = $widget->all();
        $not_selected_widgets = array_diff_key($all_widgets, $selected_widgets);
        if(defined("SOFTWARE_FILE_VERSION") && SOFTWARE_FILE_VERSION != SOFTWARE_VERSION) {
            $widget->Warning[] = sprintf(__("database and files differ in version number. Please check your update"), SOFTWARE_FILE_VERSION, SOFTWARE_VERSION);
        }
        if(defined("PDF_MODULE") && PDF_MODULE == "fpdf") {
            $error_class->Warning[] = __("fpdf deprecated - dashboard warning");
        }
        $message = parse_message(isset($invoices) ? $invoices : NULL, isset($pricequote) ? $pricequote : NULL, isset($creditinvoice) ? $creditinvoice : NULL, isset($agenda) ? $agenda : NULL, $widget);
        self::sendAnonymousStatistics();
        $current_page_url = "index.php";
        $sidebar_template = "dashboard.sidebar.php";
        require_once "views/dashboard.php";
}

?>