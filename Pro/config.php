<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
if(!isset($_SESSION)) {
    if(!defined("INSTALL_DIR")) {
        include_once "connect.php";
    }
    $suffix = defined("DB_NAME") ? substr(md5(DB_NAME), 0, 8) : "";
    $sessionPrefix = defined("SESSION_PREFIX") ? SESSION_PREFIX : "hfb";
    session_name($sessionPrefix . $suffix);
    $current_session_params = session_get_cookie_params();
    $http_only = true;
    !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off" or $secure_flag = !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off" || isset($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] == 443;
    session_set_cookie_params($current_session_params["lifetime"], $current_session_params["path"], $current_session_params["domain"], $secure_flag, $http_only);
    session_start();
}
require_once "includes/csrf.php";
if(!empty($_POST) && (!defined("SKIP_CSRF_CHECK") || SKIP_CSRF_CHECK !== true)) {
    $result = CSRF_Model::validateToken();
}
require_once "3rdparty/vendor/autoload.php";
error_reporting(24565);
define("SCRIPT_START_TIME", microtime(true));
define("MIN_PAGINATION", 10);
define("SOFTWARE_FILE_VERSION", "6.0.2");
define("JSFILE_NOCACHE", "20230110");
define("INT_SUPPORT_TAX_OVER_TOTAL", true);
define("INT_SUPPORT_SUMMATIONS", true);
define("INT_SUPPORT_BANKIMPORT_CAMT", true);
define("INT_SUPPORT_ACCOUNTING_MODULES", true);
define("INT_SUPPORT_EXTENDING_RECURRING_PROFILES", true);
if(defined("INTERFACE_URL")) {
    exit;
}
define("INTERFACE_URL", "https://www.hostfact.nl");
define("INTERFACE_URL_CUSTOMERPANEL", "https://www.hostfact.nl/klanten");
define("EURO_SYMBOL", iconv("iso-8859-15", "UTF-8", chr(164)));
define("REMINDER_LIMIT", 1);
define("STATUS_CHANGE_ICON", "<span class=\"ico actionblock arrowdown mar2 pointer\" style=\"margin: 4px 2px 2px 2px; z-index: 1;\">&nbsp;</span>");
define("HANDLE_OWNER", "DOHO");
define("HANDLE_ADMIN", "DOAC");
define("HANDLE_TECH", "DOTC");
define("HANDLE_STANDARD", "NONSIDN");
define("COC_LOCATION", "https://www.kvk.nl/zoeken/handelsregister/?zoekuitgeschreven=1&kvknummer=");
define("DEBTOR_INTERACTIONS_SMALL_LIMIT", 3);
define("DEBTOR_INTERACTIONS_DIALOG_LIMIT", 5);
if(isset($_GET["language"]) && in_array($_GET["language"], ["nl_NL", "en_EN"])) {
    $_SESSION["language"] = esc($_GET["language"]);
}
if(isset($_SESSION["language"]) && $_SESSION["language"]) {
    define("LANGUAGE_CODE", $_SESSION["language"]);
} else {
    define("LANGUAGE_CODE", "nl_NL");
}
if(isset($_SESSION["delete_download"]) && !defined("DOWNLOAD_PHP_ACTIVE")) {
    @unlink("temp/" . @esc($_SESSION["force_download"]));
    unset($_SESSION["force_download"]);
    unset($_SESSION["delete_download"]);
}
$ti = 1;
if(!isset($_SESSION["ActionLog"])) {
    $_SESSION["ActionLog"] = [];
}
if(isset($_GET["redirect"])) {
    $_SESSION["RedirectURL"] = str_replace("_", "&", $_GET["redirect"]);
}
if(!isset($_SERVER["REQUEST_URI"])) {
    $_SERVER["REQUEST_URI"] = $_SERVER["SCRIPT_NAME"];
    if(isset($_SERVER["QUERY_STRING"]) && $_SERVER["QUERY_STRING"]) {
        $_SERVER["REQUEST_URI"] .= "?" . $_SERVER["QUERY_STRING"];
    }
}
require "includes/language/" . LANGUAGE_CODE . "/" . LANGUAGE_CODE . ".php";
require_once "class/overall.php";
require_once "includes/functions.php";
settings::setSecurityHeaders();
if(isset($_GET["wf_debug"]) && $_GET["wf_debug"] == "true" || isset($_SESSION["wf_debug"]) && $_SESSION["wf_debug"]) {
    if(defined("IP_WHITELIST") && isset($_SESSION["UserPro"]) && 0 < $_SESSION["UserPro"]) {
        $json_whitelist = json_decode(htmlspecialchars_decode(IP_WHITELIST), true);
        foreach ($json_whitelist as $_ip_whitelist) {
            if($_SERVER["REMOTE_ADDR"] == $_ip_whitelist["IP"]) {
                $_SESSION["wf_debug"] = true;
                set_error_handler("HostFactErrorHandler");
                register_shutdown_function("checkFatalErrors");
            }
        }
    }
} elseif(isset($_GET["wf_debug"]) && $_GET["wf_debug"] == "false") {
    unset($_SESSION["wf_debug"]);
} elseif(isset($_SESSION["UserPro"]) && 0 < $_SESSION["UserPro"]) {
    register_shutdown_function("checkFatalErrors");
}
if(!empty($_POST)) {
    $ini_get_max_input_vars = @ini_get("max_input_vars");
    if(0 < $ini_get_max_input_vars && $ini_get_max_input_vars < count($_POST, true)) {
        fatal_error(__("server configuration error"), __("max_input_vars must be increased in order to process post data"));
    }
}
if(!isset($_SESSION["IP_BLACKLIST_CHECKED"])) {
    if((!defined("SKIP_BLACKLIST_CHECK") || SKIP_BLACKLIST_CHECK !== true) && defined("IP_BLACKLIST") && IP_BLACKLIST && $_SERVER["REMOTE_ADDR"]) {
        $json_blacklist = IP_BLACKLIST && json_decode(htmlspecialchars_decode(IP_BLACKLIST), true) ? json_decode(htmlspecialchars_decode(IP_BLACKLIST), true) : [];
        $blacklist_array = [];
        foreach ($json_blacklist as $_ip_blacklist) {
            $blacklist_array[] = $_ip_blacklist["IP"];
        }
        if(ip_in_range($_SERVER["REMOTE_ADDR"], $blacklist_array)) {
            fatal_error(__("fatal ip blacklist"), sprintf(__("your ip address is on the blacklist"), htmlspecialchars($_SERVER["REMOTE_ADDR"])));
        }
    }
    $_SESSION["IP_BLACKLIST_CHECKED"] = true;
}
define("__SITE_URL", defined("BACKOFFICE_URL") ? rtrim(BACKOFFICE_URL, "/") : "");
get_hostfact_plan();
if(defined("INT_WF_ACTIVE_DEBTOR_LIMIT") && 0 < INT_WF_ACTIVE_DEBTOR_LIMIT && defined("MAX_RESULTS_LIST") && MAX_RESULTS_LIST && !isset($_SESSION["active_clients"])) {
    require_once "class/debtor.php";
    $debtor = new debtor();
    $active_debtors_counter = $debtor->getActiveClientsCount();
    $_SESSION["active_clients"] = $active_debtors_counter;
    $_SESSION["wf_cache_licensehash"] = md5(LICENSE . $active_debtors_counter);
    unset($debtor);
}
if(isset($_SESSION["wf_cache_array_tax"]) && is_array($_SESSION["wf_cache_array_tax"]) && isset($_SESSION["wf_cache_array_tax_default"])) {
    $array_taxpercentages = $_SESSION["wf_cache_array_tax"];
    $array_taxpercentages_info = $_SESSION["wf_cache_array_tax_info"];
    if(!defined("STANDARD_TAX")) {
        define("STANDARD_TAX", $_SESSION["wf_cache_array_tax_default"]);
    }
} else {
    $array_taxpercentages = [];
    $array_taxpercentages_info = [];
    $result_db = Database_Model::getInstance()->get("HostFact_Settings_Taxrates")->where("TaxType", "line")->orderBy("Rate", "DESC")->execute();
    if($result_db !== false) {
        foreach ($result_db as $v) {
            $array_taxpercentages["" . (double) $v->Rate] = 100 * (double) $v->Rate;
            if($v->Default == "yes" && !defined("STANDARD_TAX")) {
                define("STANDARD_TAX", (double) $v->Rate);
            }
            $array_taxpercentages_info["" . (double) $v->Rate] = ["label" => htmlspecialchars($v->Label)];
        }
        $_SESSION["wf_cache_array_tax"] = $array_taxpercentages;
        $_SESSION["wf_cache_array_tax_info"] = $array_taxpercentages_info;
        $_SESSION["wf_cache_array_tax_default"] = !defined("STANDARD_TAX") ? 0 : STANDARD_TAX;
    }
}
if(isset($_SESSION["wf_cache_array_total_tax"]) && is_array($_SESSION["wf_cache_array_total_tax"]) && isset($_SESSION["wf_cache_array_total_tax_default"])) {
    $array_total_taxpercentages = $_SESSION["wf_cache_array_total_tax"];
    $array_total_taxpercentages_info = $_SESSION["wf_cache_array_total_taxpercentages_info"];
    if(!defined("STANDARD_TOTAL_TAX")) {
        define("STANDARD_TOTAL_TAX", $_SESSION["wf_cache_array_total_tax_default"]);
    }
} else {
    $array_total_taxpercentages = [];
    $array_total_taxpercentages_info = [];
    $result_db = Database_Model::getInstance()->get("HostFact_Settings_Taxrates")->where("TaxType", "total")->orderBy("Rate", "DESC")->execute();
    if($result_db !== false) {
        foreach ($result_db as $v) {
            $array_total_taxpercentages["" . (double) $v->Rate] = 100 * (double) $v->Rate;
            if($v->Default == "yes" && !defined("STANDARD_TOTAL_TAX")) {
                define("STANDARD_TOTAL_TAX", (double) $v->Rate);
            }
            $array_total_taxpercentages_info["" . (double) $v->Rate] = ["label" => htmlspecialchars($v->Label), "compound" => $v->Compound];
        }
        $_SESSION["wf_cache_array_total_tax"] = $array_total_taxpercentages;
        $_SESSION["wf_cache_array_total_taxpercentages_info"] = $array_total_taxpercentages_info;
        $_SESSION["wf_cache_array_total_tax_default"] = !defined("STANDARD_TOTAL_TAX") ? 0 : STANDARD_TOTAL_TAX;
    }
}
if(!defined("STANDARD_TAX")) {
    define("STANDARD_TAX", 0);
}
if(!defined("STANDARD_TOTAL_TAX")) {
    define("STANDARD_TOTAL_TAX", 0);
}
if(isset($_SESSION["wf_cache_array_legaltype"]) && is_array($_SESSION["wf_cache_array_legaltype"])) {
    $array_legaltype = $_SESSION["wf_cache_array_legaltype"];
} else {
    $array_legaltype = [];
    $result_db = Database_Model::getInstance()->get("HostFact_Settings_LegalForms")->orderBy("OrderID", "ASC")->orderBy("Title", "ASC")->execute();
    if($result_db !== false) {
        foreach ($result_db as $v) {
            $array_legaltype[$v->LegalForm] = $v->Title;
        }
        $_SESSION["wf_cache_array_legaltype"] = $array_legaltype;
    }
}
if(isset($_SESSION["wf_cache_array_country"][LANGUAGE_CODE]) && is_array($_SESSION["wf_cache_array_country"][LANGUAGE_CODE])) {
    $array_country = $_SESSION["wf_cache_array_country"][LANGUAGE_CODE];
    $array_country_EU = $_SESSION["wf_cache_array_country_EU"];
} else {
    $array_country = [];
    $array_country_EU = [];
    $result_db = Database_Model::getInstance()->get("HostFact_Settings_Countries")->where("Visible", "yes")->orderBy("OrderID", "ASC")->orderBy(LANGUAGE_CODE, "ASC")->execute();
    if($result_db) {
        foreach ($result_db as $v) {
            $array_country[$v->CountryCode] = $v->{LANGUAGE_CODE};
            if($v->EUCountry == "yes") {
                $array_country_EU[$v->CountryCode] = $v->{LANGUAGE_CODE};
            }
        }
        $_SESSION["wf_cache_array_country_EU"] = $array_country_EU;
        $_SESSION["wf_cache_array_country"][LANGUAGE_CODE] = $array_country;
    }
}
if(isset($_SESSION["wf_cache_array_states"]) && is_array($_SESSION["wf_cache_array_states"])) {
    $array_states = $_SESSION["wf_cache_array_states"];
} else {
    $array_states = [];
    $result_db = Database_Model::getInstance()->get("HostFact_Settings_States")->orderBy("State", "ASC")->execute();
    if($result_db) {
        foreach ($result_db as $v) {
            $array_states[$v->CountryCode][$v->StateCode] = $v->State;
        }
        $_SESSION["wf_cache_array_states"] = $array_states;
    }
}
$_backoffice_hooks = [];
$_module_language_array = [];
$_module_instances = [];
$additional_product_types_all = [];
$additional_modules_all = [];
if(!defined("InStAlLHosTFacT") || !InStAlLHosTFacT || defined("API_DIR") || defined("SCRIPT_IS_CRONJOB") || defined("SCRIPT_IS_INDEX_CRONJOB")) {
    $additional_product_types = load_additional_product_types();
    $additional_modules = load_additional_modules();
} else {
    $additional_product_types = [];
    $additional_modules = [];
}
$array_emailstatus = [__("emailstatus queue"), __("emailstatus sent"), 8 => __("emailstatus error"), 9 => __("emailstatus removed")];
$array_messagetype = ["error" => __("messagetype error"), "warning" => __("messagetype warning"), "success" => __("messagetype success")];
$array_producttypes = ["other" => __("producttype other"), "domain" => __("producttype domain"), "hosting" => __("producttype hosting")];
$array_producttypes = array_merge($array_producttypes, $additional_product_types);
$array_account_generation = [1 => __("hostingaccount generationmethod auto"), 2 => __("hostingaccount generationmethod debtor"), 3 => __("hostingaccount generationmethod domain")];
$array_domainstatus = [1 => __("domainstatus wait"), 3 => __("domainstatus request"), 4 => __("domainstatus active"), 5 => __("domainstatus expired"), 6 => __("domainstatus in progress"), 7 => __("domainstatus error"), 8 => __("domainstatus cancelled"), 9 => __("domainstatus removed"), -1 => __("domainstatus in order")];
$array_handlestatus = [1 => __("handlestatus active"), 9 => __("handlestatus removed")];
$array_hostingstatus = [-1 => __("hostingstatus in order"), __("hostingstatus wait"), 3 => __("hostingstatus create"), 4 => __("hostingstatus active"), 5 => __("hostingstatus suspended"), 7 => __("hostingstatus error"), 9 => __("hostingstatus removed")];
$array_domaintype = ["additional", "pointer", "alias"];
$array_serverstatus = [1 => __("serverstatus active"), 9 => __("serverstatus removed")];
$array_packagetypes = ["normal" => __("packagetype normal"), "reseller" => __("packagetype reseller")];
$array_usetemplate = ["yes" => __("usetemplate option yes"), "no" => __("usetemplate option no")];
$array_mailingoptin = ["yes" => __("yes"), "no" => __("no")];
$array_registrarstatus = [1 => __("registrarstatus active"), 9 => __("registrarstatus removed")];
$array_sex = ["m" => __("gender male"), "f" => __("gender female"), "d" => __("gender department"), "u" => __("gender unknown")];
$array_onoff = ["<font class=\"middlegrey\">" . __("onoff off") . "</font>", __("onoff on")];
$array_yesno = [__("no"), __("yes")];
$array_taxable = ["auto" => __("taxable option auto"), "yes" => __("taxable option yes"), "no" => __("taxable option no")];
$array_debtorstatus = [__("debtorstatus active"), __("debtorstatus active"), 9 => __("debtorstatus removed")];
$array_servicestatus = [1 => __("servicestatus active"), 8 => __("servicestatus terminated"), 9 => __("servicestatus removed")];
$array_invoiceterm = [-1 => defined("INVOICE_TERM") ? INVOICE_TERM : 14];
$interaction_category = [1 => __("interaction category global"), 2 => __("interaction category billing"), 3 => __("interaction category technical"), 4 => __("interaction category support"), 5 => __("interaction category sales")];
$interaction_type = ["mail" => __("interaction type mail"), "phone" => __("interaction type phone"), "post" => __("interaction type post"), "appointment" => __("interaction type appointment"), "other" => __("interaction type other")];
$array_invoiceemailattachments = ["pdf" => __("ubl - only pdf"), "pdfubl" => __("ubl - pdf and ubl"), "ublwithpdf" => __("ubl - ubl with pdf included"), "pdfublwithpdf" => __("ubl - pdf and ubl with pdf included")];
$array_periodes = ["" => __("periods_singular_once"), "d" => __("periods_singular_day"), "w" => __("periods_singular_week"), "m" => __("periods_singular_month"), "k" => __("periods_singular_quarter"), "h" => __("periods_singular_half_year"), "j" => __("periods_singular_year"), "t" => __("periods_singular_two_year")];
$array_periodic = $array_periodes;
$array_periodesMV = ["d" => __("periods_plural_days"), "w" => __("periods_plural_weeks"), "m" => __("periods_plural_months"), "k" => __("periods_plural_quarters"), "h" => __("periods_plural_half_years"), "j" => __("periods_plural_years"), "t" => __("periods_plural_two_years")];
$array_handletype = ["NONSIDN" => "Algemeen", "DOHO" => "Houder", "DOAC" => "Administratief contact", "DOTC" => "Technisch contact"];
$array_months = ["01" => __("month_1"), "02" => __("month_2"), "03" => __("month_3"), "04" => __("month_4"), "05" => __("month_5"), "06" => __("month_6"), "07" => __("month_7"), "08" => __("month_8"), "09" => __("month_9"), 10 => __("month_10"), 11 => __("month_11"), 12 => __("month_12")];
$array_months_short = [1 => __("shortmonth_1"), 2 => __("shortmonth_2"), 3 => __("shortmonth_3"), 4 => __("shortmonth_4"), 5 => __("shortmonth_5"), 6 => __("shortmonth_6"), 7 => __("shortmonth_7"), 8 => __("shortmonth_8"), 9 => __("shortmonth_9"), 10 => __("shortmonth_10"), 11 => __("shortmonth_11"), 12 => __("shortmonth_12")];
$array_days = [1 => __("monday"), 2 => __("tuesday"), 3 => __("wednesday"), 4 => __("thursday"), 5 => __("friday"), 6 => __("saturday"), 7 => __("sunday")];
$array_daysEN = ["1" => "Monday", "2" => "Tuesday", "3" => "Wednesday", "4" => "Thursday", "5" => "Friday", "6" => "Saturday", "7" => "Sunday"];
$array_shortdays = [__("shortday_sunday"), __("shortday_monday"), __("shortday_tuesday"), __("shortday_wednesday"), __("shortday_thursday"), __("shortday_friday"), __("shortday_saturday")];
$array_authorisation = ["yes" => __("yes"), "no" => __("no")];
$array_ignorediscount = [1 => __("yes"), 0 => __("no")];
$array_invoicemethod = [__("invoicemethod by email"), __("invoicemethod by post"), 3 => __("invoicemethod by email and postal")];
$array_pricequotemethod = [__("invoicemethod by email"), __("invoicemethod by post"), 3 => __("invoicemethod by email and postal")];
$array_invoicestatus = [__("invoicestatus concept"), __("invoicestatus queue"), __("invoicestatus sent"), __("invoicestatus partly paid"), __("invoicestatus paid"), 8 => __("invoicestatus credit invoice"), 9 => __("invoicestatus expire")];
$array_pricequotestatus = [__("pricequotestatus concept"), __("pricequotestatus queue"), __("pricequotestatus sent"), __("pricequotestatus accepted"), __("pricequotestatus invoiced"), 8 => __("pricequotestatus not accepted"), 9 => __("pricequotestatus expire")];
$array_creditinvoicestatus = [__("creditinvoicestatus not received"), __("creditinvoicestatus not paid"), __("creditinvoicestatus partly paid"), __("creditinvoicestatus paid"), 8 => __("creditinvoicestatus credit"), 9 => __("creditinvoicestatus expire")];
$array_orderstatus = [__("orderstatus received"), __("orderstatus in treatment"), __("orderstatus cronjob error"), 8 => __("orderstatus treated"), 9 => __("orderstatus cancelled")];
$array_paymentmethod = ["wire" => __("paymentmethod wire"), "auth" => __("paymentmethod auth"), "paypal" => __("paymentmethod paypal"), "ideal" => __("paymentmethod ideal"), "other" => __("paymentmethod other")];
$array_paymentstatus = [__("paymentstatus unpaid"), __("paymentstatus paid"), __("paymentstatus payment in progress")];
$array_priority = [5 => __("priority low"), 0 => __("priority normal"), 1 => __("priority high")];
$array_ticketstatus = [__("ticketstatus new"), __("ticketstatus open"), __("ticketstatus in treatment"), __("ticketstatus closed")];
$orderform_availability = ["yes" => __("orderform availability yes"), "no" => __("orderform availability no")];
$array_filetypes = ["xls" => "Microsoft Excel", "doc" => "Microsoft Word", "psp" => "Microsoft PowerPoint", "pdf" => "Acrobat Reader", "jpeg" => __("filetype picture"), "gif" => __("filetype picture"), "png" => __("filetype picture"), "bmp" => __("filetype picture"), "jpg" => __("filetype picture"), "zip" => __("filetype compressed"), "rar" => __("filetype compressed"), "tar" => __("filetype compressed"), "mp3" => __("filetype audio"), "wav" => __("filetype audio"), "mid" => __("filetype audio"), "avi" => __("filetype video"), "mpg" => __("filetype video"), "mpeg" => __("filetype video"), "divx" => __("filetype video"), "html" => __("filetype webpage"), "css" => __("filetype css"), "php" => __("filetype webpage"), "asp" => __("filetype webpage"), "js" => __("filetype script"), "perl" => __("filetype script"), "sql" => __("filetype sql")];
$array_filetypes_images = ["xls" => "file_xls.png", "doc" => "file_doc.png", "docx" => "file_doc.png", "pdf" => "file_pdf.png", "jpeg" => "file_img.png", "gif" => "file_img.png", "png" => "file_img.png", "bmp" => "file_img.png", "jpg" => "file_img.png", "zip" => "file_zip.png", "rar" => "file_zip.png", "tar" => "file_zip.png", "html" => "file_txt.png", "css" => "file_txt.png", "php" => "file_txt.png", "asp" => "file_txt.png", "js" => "file_txt.png", "perl" => "file_txt.png", "sql" => "file_txt.png", "ppt" => "file_ppt.png"];
$array_customer_languages = ["nl_NL" => __("language nl_NL"), "en_EN" => __("language en_EN"), "de_DE" => __("language de_DE")];
$array_backoffice_languages = ["nl_NL" => __("language nl_NL"), "en_EN" => __("language en_EN")];
add_filter("export_menu_notification", "filter_export_menu_notification");
add_filter("sidebar_notifications", "filter_export_sidebar_notifications");
spl_autoload_register(function ($class) {
    if(file_exists("class/" . $class . ".php")) {
        require_once "class/" . $class . ".php";
    }
});
if(@file_exists("includes/hooks.php")) {
    include_once "includes/hooks.php";
}
if(@file_exists("includes/functions_custom.php")) {
    include_once "includes/functions_custom.php";
}
class flashMessage
{
}
function __($string, $arguments = NULL, $namespace = "hostfact", $htmlspecialchar_arguments = true)
{
    global $LANG;
    global $_module_language_array;
    if(!is_null($arguments) && is_string($arguments)) {
        $namespace = $arguments;
        $arguments = NULL;
    }
    if($namespace && $namespace != "hostfact" && isset($_module_language_array[$namespace][$string])) {
        $translation = $_module_language_array[$namespace][$string];
    } elseif(isset($LANG[$string])) {
        $translation = $LANG[$string];
    } else {
        $translation = $string;
    }
    if(!empty($arguments) && is_array($arguments)) {
        foreach ($arguments as $search => $replace) {
            $translation = str_replace("%{" . $search . "}%", $replace, $translation);
            if($htmlspecialchar_arguments === true) {
                $translation = str_replace("@{" . $search . "}@", "<em>" . htmlspecialchars($replace) . "</em>", $translation);
                $translation = str_replace("{" . $search . "}", htmlspecialchars($replace), $translation);
            }
        }
    }
    return $translation;
}
function fatal_error($title, $msg)
{
    $html_title = !defined("IS_INTERNATIONAL") || IS_INTERNATIONAL ? "Error" : "Foutmelding";
    exit("<html><head><title>" . $html_title . "</title><style type=\"text/css\">body{margin:40px;font-family:Verdana;font-size:12px;color:#000;}#content{border:1px solid #999;background-color:#fff;padding: 25px;width:600px;position:absolute;left:50%;margin-left:-300px;}a{color:#000099;}h1{font-weight:normal;font-size:14px;color:#990000;margin:0 0 4px 0;}</style></head><body><div id=\"content\"><h1>" . $title . "</h1>" . $msg . "</div></body></html>");
}
function add_action($hook, $function_to_execute, $priority = 10)
{
    global $_backoffice_hooks;
    if(is_array($function_to_execute)) {
        count($function_to_execute);
        switch (count($function_to_execute)) {
            case 2:
                $class = is_object($function_to_execute[0]) ? get_class($function_to_execute[0]) : $function_to_execute[0];
                $method = $function_to_execute[1];
                $_backoffice_hooks["actions"][$hook][$priority][] = ["class" => $class, "method" => $method];
                break;
            case 3:
                $namespace = $function_to_execute[0];
                $class = is_object($function_to_execute[1]) ? get_class($function_to_execute[1]) : $function_to_execute[1];
                $method = $function_to_execute[2];
                $_backoffice_hooks["actions"][$hook][$priority][] = ["namespace" => $namespace, "class" => $class, "method" => $method];
                return true;
                break;
            default:
                return false;
        }
    } else {
        if(function_exists($function_to_execute)) {
            $_backoffice_hooks["actions"][$hook][$priority][] = ["function" => $function_to_execute];
            return true;
        }
        return false;
    }
}
function do_action($hook, $parameters = NULL)
{
    global $_backoffice_hooks;
    global $_module_instances;
    if(isset($_backoffice_hooks["actions"][$hook]) && is_array($_backoffice_hooks["actions"][$hook])) {
        ksort($_backoffice_hooks["actions"][$hook]);
        $hooks_called = [];
        foreach ($_backoffice_hooks["actions"][$hook] as $priority => $tmp_array) {
            foreach ($tmp_array as $do_hook) {
                if(isset($do_hook["class"]) && $do_hook["class"] && isset($_module_instances[$do_hook["class"]]) && $do_hook["method"]) {
                    if(in_array("class-" . $do_hook["class"] . "-" . $do_hook["method"], $hooks_called)) {
                    } else {
                        $hook_class = $_module_instances[$do_hook["class"]];
                        if(method_exists($hook_class, $do_hook["method"])) {
                            try {
                                $hook_class->{$do_hook}["method"]($parameters);
                                $hooks_called[] = "class-" . $do_hook["class"] . "-" . $do_hook["method"];
                            } catch (Exception $e) {
                            }
                        }
                        unset($hook_class);
                    }
                } elseif($do_hook["function"] && function_exists($do_hook["function"])) {
                    if(in_array("function-" . $do_hook["function"], $hooks_called)) {
                    } else {
                        try {
                            $do_hook["function"]($parameters);
                            $hooks_called[] = "function-" . $do_hook["function"];
                        } catch (Exception $e) {
                        }
                    }
                }
            }
        }
        return true;
    } else {
        return false;
    }
}
function add_filter($hook, $function_to_execute, $priority = 10)
{
    global $_backoffice_hooks;
    if(is_array($function_to_execute)) {
        count($function_to_execute);
        switch (count($function_to_execute)) {
            case 2:
                $class = is_object($function_to_execute[0]) ? get_class($function_to_execute[0]) : $function_to_execute[0];
                $method = $function_to_execute[1];
                $_backoffice_hooks["filters"][$hook][$priority][] = ["class" => $class, "method" => $method];
                break;
            case 3:
                $namespace = $function_to_execute[0];
                $class = is_object($function_to_execute[1]) ? get_class($function_to_execute[1]) : $function_to_execute[1];
                $method = $function_to_execute[2];
                $_backoffice_hooks["filters"][$hook][$priority][] = ["namespace" => $namespace, "class" => $class, "method" => $method];
                return true;
                break;
            default:
                return false;
        }
    } else {
        if(function_exists($function_to_execute)) {
            $_backoffice_hooks["filters"][$hook][$priority][] = ["function" => $function_to_execute];
            return true;
        }
        return false;
    }
}
function do_filter($hook, $input, $parameters = NULL)
{
    global $_backoffice_hooks;
    global $_module_instances;
    if(isset($_backoffice_hooks["filters"][$hook]) && is_array($_backoffice_hooks["filters"][$hook])) {
        ksort($_backoffice_hooks["filters"][$hook]);
        $hooks_called = [];
        foreach ($_backoffice_hooks["filters"][$hook] as $priority => $tmp_array) {
            foreach ($tmp_array as $do_hook) {
                if(isset($do_hook["class"]) && $do_hook["class"] && isset($_module_instances[$do_hook["class"]]) && $do_hook["method"]) {
                    if(in_array("class-" . $do_hook["class"] . "-" . $do_hook["method"], $hooks_called)) {
                    } else {
                        $hook_class = $_module_instances[$do_hook["class"]];
                        if(method_exists($hook_class, $do_hook["method"])) {
                            $input = $hook_class->{$do_hook}["method"]($input, $parameters);
                            $hooks_called[] = "class-" . $do_hook["class"] . "-" . $do_hook["method"];
                        }
                        unset($hook_class);
                    }
                } elseif($do_hook["function"] && function_exists($do_hook["function"])) {
                    if(in_array("function-" . $do_hook["function"], $hooks_called)) {
                    } else {
                        $input = $do_hook["function"]($input, $parameters);
                        $hooks_called[] = "function-" . $do_hook["function"];
                    }
                }
            }
        }
    }
    return $input;
}
function load_additional_product_types()
{
    $additional_product_types = [];
    global $additional_product_types_all;
    global $load_all_product_module_languagefiles;
    Database_Model::getInstance()->get("HostFact_Modules")->where("ModuleType", "product");
    if(isset($load_all_product_module_languagefiles) && $load_all_product_module_languagefiles === true) {
    } else {
        Database_Model::getInstance()->where("Active", "active");
    }
    $_candidates = Database_Model::getInstance()->execute();
    if($_candidates && is_array($_candidates)) {
        foreach ($_candidates as $_candidate) {
            if(is_dir("3rdparty/modules/products/" . $_candidate->Module)) {
                $_LANG = [];
                $load_language_via_method = false;
                if(file_exists("3rdparty/modules/products/" . $_candidate->Module . "/language/" . LANGUAGE_CODE . ".php")) {
                    include_once "3rdparty/modules/products/" . $_candidate->Module . "/language/" . LANGUAGE_CODE . ".php";
                } elseif(file_exists("3rdparty/modules/products/" . $_candidate->Module . "/language/" . htmlspecialchars($_candidate->Language) . ".php")) {
                    include_once "3rdparty/modules/products/" . $_candidate->Module . "/language/" . htmlspecialchars($_candidate->Language) . ".php";
                } else {
                    $load_language_via_method = true;
                }
                if(!empty($_LANG)) {
                    global $_module_language_array;
                    $_module_language_array[$_candidate->Module] = $_LANG;
                }
                if(($_candidate->Active == "active" || $load_language_via_method === true) && file_exists("3rdparty/modules/products/" . $_candidate->Module . "/" . $_candidate->Module . ".php")) {
                    include_once "3rdparty/modules/products/product_module.php";
                    include_once "3rdparty/modules/products/" . $_candidate->Module . "/" . $_candidate->Module . ".php";
                    global $_module_instances;
                    $namespace = "modules\\products\\" . $_candidate->Module;
                    if(class_exists($namespace . "\\" . $_candidate->Module)) {
                        $classname = $namespace . "\\" . $_candidate->Module;
                        $_module_instances[$_candidate->Module] = new $classname();
                    } else {
                        $_module_instances[$_candidate->Module] = new $_candidate->Module();
                    }
                    if($load_language_via_method === true && @method_exists($_module_instances[$_candidate->Module], "loadLanguageArray")) {
                        global $_module_language_array;
                        $_module_language_array[$_candidate->Module] = $_module_instances[$_candidate->Module]->loadLanguageArray(LANGUAGE_CODE);
                    }
                }
                if($_candidate->Active == "active") {
                    $additional_product_types[$_candidate->Module] = __("module-name", $_candidate->Module) != "module-name" ? __("module-name", $_candidate->Module) : $_candidate->Module;
                } elseif(isset($_module_instances[$_candidate->Module])) {
                    unset($_module_instances[$_candidate->Module]);
                }
                $additional_product_types_all[$_candidate->Module] = $_candidate->Active;
            }
        }
    }
    unset($_candidates);
    unset($cp_candidate);
    unset($version);
    asort($additional_product_types, SORT_NATURAL | SORT_FLAG_CASE);
    return $additional_product_types;
}
function load_additional_modules()
{
    $additional_modules = [];
    global $additional_modules_all;
    global $load_all_module_languagefiles;
    Database_Model::getInstance()->get("HostFact_Modules")->where("ModuleType", ["!=" => "product"]);
    if(isset($load_all_module_languagefiles) && $load_all_module_languagefiles === true) {
    } else {
        Database_Model::getInstance()->where("Active", "active");
    }
    $_candidates = Database_Model::getInstance()->execute();
    if(array_search("collection", array_column($_candidates, "ModuleType"))) {
        if(class_exists("hostfact_error_handler")) {
            hostfact_error_handler::$ioncube_error_handler = false;
        }
        require_once __DIR__ . "/3rdparty/modules/hostfact_module.php";
        register_shutdown_function(["hostfact_module", "ioncube_error_handler"]);
        hostfact_module::$module_error_handler = true;
        hostfact_module::$module_error_handler_full_styling = true;
    }
    if($_candidates && is_array($_candidates)) {
        foreach ($_candidates as $_candidate) {
            if(is_dir("3rdparty/modules/" . $_candidate->ModuleType . "/" . $_candidate->Module)) {
                $_LANG = [];
                $load_language_via_method = false;
                if(file_exists("3rdparty/modules/" . $_candidate->ModuleType . "/" . $_candidate->Module . "/language/" . LANGUAGE_CODE . ".php")) {
                    include_once "3rdparty/modules/" . $_candidate->ModuleType . "/" . $_candidate->Module . "/language/" . LANGUAGE_CODE . ".php";
                } elseif(file_exists("3rdparty/modules/" . $_candidate->ModuleType . "/" . $_candidate->Module . "/language/" . htmlspecialchars($_candidate->Language) . ".php")) {
                    include_once "3rdparty/modules/" . $_candidate->ModuleType . "/" . $_candidate->Module . "/language/" . htmlspecialchars($_candidate->Language) . ".php";
                } else {
                    $load_language_via_method = true;
                }
                if(!empty($_LANG)) {
                    global $_module_language_array;
                    $_module_language_array[$_candidate->Module] = $_LANG;
                }
                if(($_candidate->Active == "active" || $load_language_via_method === true) && file_exists("3rdparty/modules/" . $_candidate->ModuleType . "/" . $_candidate->Module . "/" . $_candidate->Module . ".php")) {
                    include_once "3rdparty/modules/" . $_candidate->ModuleType . "/" . $_candidate->ModuleType . "_module.php";
                    include_once "3rdparty/modules/" . $_candidate->ModuleType . "/" . $_candidate->Module . "/" . $_candidate->Module . ".php";
                    global $_module_instances;
                    $namespace = "modules\\" . $_candidate->ModuleType . "\\" . $_candidate->Module;
                    if(class_exists($namespace . "\\" . $_candidate->Module)) {
                        $classname = $namespace . "\\" . $_candidate->Module;
                        $_module_instances[$_candidate->Module] = new $classname();
                    } else {
                        $_module_instances[$_candidate->Module] = new $_candidate->Module();
                    }
                    if($load_language_via_method === true && @method_exists($_module_instances[$_candidate->Module], "loadLanguageArray")) {
                        global $_module_language_array;
                        $_module_language_array[$_candidate->Module] = $_module_instances[$_candidate->Module]->loadLanguageArray(LANGUAGE_CODE);
                    }
                }
                if($_candidate->Active == "active") {
                    $additional_modules[$_candidate->Module] = __("module-name", $_candidate->Module) != "module-name" ? __("module-name", $_candidate->Module) : $_candidate->Module;
                } elseif(isset($_module_instances[$_candidate->Module])) {
                    unset($_module_instances[$_candidate->Module]);
                }
                $additional_modules_all[$_candidate->Module] = $_candidate->Active;
            }
        }
    }
    unset($_candidates);
    unset($cp_candidate);
    unset($version);
    if(class_exists("hostfact_module") && hostfact_module::$module_error_handler) {
        if(class_exists("hostfact_error_handler")) {
            hostfact_error_handler::$ioncube_error_handler = true;
        }
        hostfact_module::$module_error_handler = false;
        hostfact_module::$module_error_handler_full_styling = false;
    }
    return $additional_modules;
}
function is_module_active($module_name)
{
    if(isset($_SESSION["wf_cache_is_module_active"][$module_name])) {
        return $_SESSION["wf_cache_is_module_active"][$module_name];
    }
    $_candidate = Database_Model::getInstance()->getOne("HostFact_Modules", ["id"])->where("Active", "active")->where("Module", $module_name)->execute();
    if(isset($_candidate->id) && 0 < $_candidate->id) {
        $_SESSION["wf_cache_is_module_active"][$module_name] = true;
        return true;
    }
    $_SESSION["wf_cache_is_module_active"][$module_name] = false;
    return false;
}
function createMessageLog($type, $message, $values = "", $objecttype = "", $id = 0, $force_message = false)
{
    global $account;
    if($force_message === true || !isset($account->Identifier) || empty($account->Identifier)) {
        require_once "class/messagelogfile.php";
        $mlog = new messagelogfile();
        $mlog->add($type, $message, $values, $objecttype, $id);
    }
}
function parse_message()
{
    global $error_class;
    $args = func_get_args();
    $message_html = "";
    $message_err = [];
    $message_war = [];
    $message_suc = [];
    $message_info = [];
    $flashMessage = new flashMessage();
    if(isset($_SESSION["flashMessage"]["Error"])) {
        $flashMessage->Error = $_SESSION["flashMessage"]["Error"];
        unset($_SESSION["flashMessage"]["Error"]);
    }
    if(isset($_SESSION["flashMessage"]["Warning"])) {
        $flashMessage->Warning = $_SESSION["flashMessage"]["Warning"];
        unset($_SESSION["flashMessage"]["Warning"]);
    }
    if(isset($_SESSION["flashMessage"]["Success"])) {
        $flashMessage->Success = $_SESSION["flashMessage"]["Success"];
        unset($_SESSION["flashMessage"]["Success"]);
    }
    if(isset($_SESSION["flashMessage"]["Info"])) {
        $flashMessage->Info = $_SESSION["flashMessage"]["Info"];
        unset($_SESSION["flashMessage"]["Info"]);
    }
    $args[] = $flashMessage;
    $args[] = $error_class;
    $replace_array = [];
    $replace_array["\r\n"] = "<br />";
    $replace_array["&amp;euro;"] = "&euro;";
    $replace_array["[hyperlink_1]"] = "<a href=\"";
    $replace_array["[hyperlink_2]"] = "\" class=\"a1 c1\">";
    $replace_array["[hyperlink_3]"] = "</a>";
    foreach ($args as $arg) {
        if(is_object($arg) && !empty($arg->Error)) {
            foreach ($arg->Error as $err) {
                $err = str_replace("<br />", "\r\n", $err);
                $message_err[] = str_replace(array_keys($replace_array), array_values($replace_array), htmlspecialchars(htmlspecialchars_decode($err)));
            }
        }
        if(is_object($arg) && !empty($arg->Warning)) {
            foreach ($arg->Warning as $war) {
                $war = str_replace("<br />", "\r\n", $war);
                $message_war[] = str_replace(array_keys($replace_array), array_values($replace_array), htmlspecialchars(htmlspecialchars_decode($war)));
            }
        }
        if(is_object($arg) && !empty($arg->Success)) {
            foreach ($arg->Success as $suc) {
                $suc = str_replace("<br />", "\r\n", $suc);
                $message_suc[] = str_replace(array_keys($replace_array), array_values($replace_array), htmlspecialchars(htmlspecialchars_decode($suc)));
            }
        }
        if(is_object($arg) && !empty($arg->Info)) {
            foreach ($arg->Info as $msg) {
                $msg = str_replace("<br />", "\r\n", $msg);
                $message_info[] = str_replace(array_keys($replace_array), array_values($replace_array), htmlspecialchars(htmlspecialchars_decode($msg)));
            }
        }
    }
    if(!empty($message_err)) {
        $message_html .= "<div class=\"mark alt3\"><a class=\"close pointer\">" . __("close") . "</a><p><strong><em>" . __("errormessage") . "</em></strong><br />" . (1 < count($message_err) ? "&bull; " : "") . implode("<br />&bull; ", $message_err) . "</p></div><hr />";
    }
    if(!empty($message_war)) {
        $message_html .= "<div class=\"mark alt1\"><a class=\"close pointer\">" . __("close") . "</a><p><strong><em style=\"color: #ED8A00\">" . __("warningmessage") . "</em></strong><br />" . (1 < count($message_war) ? "&bull; " : "") . implode("<br />&bull; ", $message_war) . "</p></div><hr />";
    }
    if(!empty($message_suc)) {
        $message_html .= "<div class=\"mark alt2\"><a class=\"close pointer\">" . __("close") . "</a><p><strong><em style=\"color:#0E5704\">" . __("successmessage") . "</em></strong><br />" . (1 < count($message_suc) ? "&bull; " : "") . implode("<br />&bull; ", $message_suc) . "</p></div><hr />";
    }
    if(!empty($message_info)) {
        $message_html .= "<div class=\"mark blue\"><p>" . (1 < count($message_info) ? "&bull; " : "") . implode("<br />&bull; ", $message_info) . "</p></div><hr />";
    }
    return $message_html;
}
function replace_hyperlink($text)
{
    $replace_array = [];
    $replace_array["[hyperlink_1]"] = "<a href=\"";
    $replace_array["[hyperlink_2]"] = "\">";
    $replace_array["[hyperlink_3]"] = "</a>";
    return str_replace(array_keys($replace_array), array_values($replace_array), htmlspecialchars(htmlspecialchars_decode($text)));
}
function flashMessage()
{
    global $error_class;
    $args = func_get_args();
    $args[] = $error_class;
    foreach ($args as $arg) {
        if(is_object($arg) && !empty($arg->Error)) {
            $_SESSION["flashMessage"]["Error"] = isset($_SESSION["flashMessage"]["Error"]) ? array_merge($_SESSION["flashMessage"]["Error"], $arg->Error) : $arg->Error;
        }
        if(is_object($arg) && !empty($arg->Warning)) {
            $_SESSION["flashMessage"]["Warning"] = isset($_SESSION["flashMessage"]["Warning"]) ? array_merge($_SESSION["flashMessage"]["Warning"], $arg->Warning) : $arg->Warning;
        }
        if(is_object($arg) && !empty($arg->Success)) {
            $_SESSION["flashMessage"]["Success"] = isset($_SESSION["flashMessage"]["Success"]) ? array_merge($_SESSION["flashMessage"]["Success"], $arg->Success) : $arg->Success;
        }
    }
    $error_class->Error = $error_class->Warning = $error_class->Success = [];
}
function esc($string = "")
{
    return $string;
}
function checkRight($bool = U_SETTINGS_SHOW, $redirect = true)
{
    if(!$bool && $redirect) {
        header("Location:norights.php");
        exit;
    }
    return $bool;
}
function tabindex($ti, $rightside = false)
{
    if(!is_numeric($ti)) {
        $ti = 1;
    }
    if($rightside === false) {
        echo "tabindex=\"" . $ti . "\"";
    } else {
        echo "tabindex=\"" . 100 * $ti . "\"";
    }
    $ti++;
    return $ti;
}
function is_domain($domain, $allow_subdomain = false, $levels = false)
{
    $dom_expl = explode(".", $domain);
    if($allow_subdomain === false && 3 < count($dom_expl) || $allow_subdomain === true && ($levels === false ? 4 : $levels) < count($dom_expl)) {
        return false;
    }
    $dom_expl = explode(".", $domain, $allow_subdomain === false ? 2 : 3);
    list($sld, $tld) = $dom_expl;
    require_once "class/topleveldomain.php";
    $topleveldomain = new topleveldomain();
    $idn = $topleveldomain->getAllowedIDNCharacters($tld);
    if(preg_match("/^[a-z" . $idn . "0-9-]+(\\.[a-z" . $idn . "0-9-]+)*(\\.[^\\\\ \\/@.]{2,63})\$/iu", trim(rtrim($domain))) == 0) {
        return false;
    }
    return true;
}
function generatePassword($length = false)
{
    $password_set["lowercase"] = str_split("abcdefghijklmnopqrstuvwxyz");
    $password_set["uppercase"] = str_split("ABCDEFGHIJKLMNOPQRSTUVWXYZ");
    $password_set["special_symbol"] = str_split("!@#\$%^&*?_~)");
    $password_set["digit"] = str_split("1234567890");
    switch (PASSWORD_GENERATION) {
        case "medium":
        case "strong":
            $length = $length === false ? 12 : $length;
            $required_symbols = ["uppercase", "lowercase", "special_symbol", "special_symbol", "digit", "digit"];
            break;
        case "verystrong":
            $length = $length === false ? 16 : $length;
            $required_symbols = ["uppercase", "lowercase", "special_symbol", "special_symbol", "digit", "digit"];
            break;
        default:
            $length = $length === false ? 8 : $length;
            $required_symbols = ["uppercase", "lowercase", "digit"];
            unset($password_set["special_symbol"]);
            $generated_password = "";
            foreach ($required_symbols as $required_symbol) {
                $generated_password .= $password_set[$required_symbol][array_rand($password_set[$required_symbol], 1)];
            }
            for ($i = 0; $i < $length - count($required_symbols); $i++) {
                $random_set = array_rand($password_set, 1);
                $generated_password .= $password_set[$random_set][array_rand($password_set[$random_set], 1)];
            }
            $generated_password = str_shuffle($generated_password);
            return $generated_password;
    }
}
function paginate($total, $rows_per_page, $current_page, $current_page_url, $active_menu)
{
    echo "\t<p class=\"float_left\">";
    echo __("show");
    echo " <select class=\"select1 size2\" onchange=\"save('";
    echo $active_menu;
    echo "','results',this.value, '";
    echo $current_page_url;
    echo "');\">\n\t\t<option value=\"10\"";
    if($rows_per_page == 10) {
        echo " selected=\"selected\"";
    }
    echo ">10</option>\n\t<option value=\"25\"";
    if($rows_per_page == 25) {
        echo " selected=\"selected\"";
    }
    echo ">25</option>\n\t<option value=\"50\"";
    if($rows_per_page == 50) {
        echo " selected=\"selected\"";
    }
    echo ">50</option>\n\t<option value=\"75\"";
    if($rows_per_page == 75) {
        echo " selected=\"selected\"";
    }
    echo ">75</option>\n\t<option value=\"100\"";
    if($rows_per_page == 100) {
        echo " selected=\"selected\"";
    }
    echo ">100</option>\n\t<option value=\"99999\"";
    if($rows_per_page == 99999) {
        echo " selected=\"selected\"";
    }
    echo ">";
    echo __("all");
    echo "</option>\n\t</select> ";
    echo __("results per page");
    echo "</p>\n\t";
    $pages = max(1, ceil($total / $rows_per_page));
    $range_start = max(1, $current_page - 2);
    $range_end = min($pages, $current_page + 2);
    if(strpos($current_page_url, "?") !== false) {
        $current_page_url .= "&amp;";
    } else {
        $current_page_url .= "?";
    }
    echo "\t\n\t<ul class=\"list4 float_right\">\n\t\t";
    if($current_page != 1) {
        echo "\t\t<li><a class=\"ico inline arrowleft\" href=\"";
        echo $current_page_url;
        echo "p=";
        echo $current_page - 1;
        echo "\">";
        echo __("previous");
        echo "</a></li>\n\t\t";
    }
    echo "\t\t\n\t\t";
    for ($p = 1; $p <= $pages; $p++) {
        if($range_start <= $p && $p <= $range_end) {
            echo "<li";
            if($p == $current_page) {
                echo " class=\"on\"";
            }
            echo "><a href=\"";
            echo $current_page_url;
            echo "p=";
            echo $p;
            echo "\">";
            echo $p;
            echo "</a></li>";
        } elseif($p == 1 && 1 < $range_start) {
            echo "<li><a href=\"";
            echo $current_page_url;
            echo "p=";
            echo $p;
            echo "\">";
            echo $p;
            echo "</a></li>";
            if(2 < $range_start) {
                echo "<li><span>...</span></li>";
            }
        } elseif($p == $pages && $range_end < $pages) {
            if($range_end < $pages - 1) {
                echo "<li><span>...</span></li>";
            }
            echo "<li><a href=\"";
            echo $current_page_url;
            echo "p=";
            echo $p;
            echo "\">";
            echo $p;
            echo "</a></li>";
        }
    }
    echo "\t\t";
    if($current_page != $pages) {
        echo "\t\t<li><a class=\"ico inline arrowright\" href=\"";
        echo $current_page_url;
        echo "p=";
        echo $current_page + 1;
        echo "\">";
        echo __("next");
        echo "</a></li>\n\t\t";
    }
    echo "\t</ul>\n\t";
}
function ajax_paginate($subtable, $total, $rows_per_page, $current_page, $current_page_url, $display_number_of_results = true)
{
    echo "\t<div id=\"SubTable_Paginate_";
    echo $subtable;
    echo "\">\n\t";
    if($display_number_of_results) {
        echo "\t<p class=\"float_left\">";
        echo __("show");
        echo " <select class=\"select1 size2\" onchange=\"loadPage('";
        echo $subtable;
        echo "','";
        echo $current_page_url;
        echo "','1',this.value)\">\n\t\t<option value=\"10\"";
        if($rows_per_page == 10) {
            echo " selected=\"selected\"";
        }
        echo ">10</option>\n\t<option value=\"25\"";
        if($rows_per_page == 25) {
            echo " selected=\"selected\"";
        }
        echo ">25</option>\n\t<option value=\"50\"";
        if($rows_per_page == 50) {
            echo " selected=\"selected\"";
        }
        echo ">50</option>\n\t<option value=\"75\"";
        if($rows_per_page == 75) {
            echo " selected=\"selected\"";
        }
        echo ">75</option>\n\t<option value=\"100\"";
        if($rows_per_page == 100) {
            echo " selected=\"selected\"";
        }
        echo ">100</option>\n\t<option value=\"99999\"";
        if($rows_per_page == 99999) {
            echo " selected=\"selected\"";
        }
        echo ">";
        echo __("all");
        echo "</option>\n\t</select> ";
        echo __("results per page");
        echo "</p>\n\t";
    }
    $pages = max(1, ceil($total / $rows_per_page));
    $range_start = max(1, $current_page - 2);
    $range_end = min($pages, $current_page + 2);
    echo "\t\n\t<ul class=\"list4 float_right\">\n\t\t";
    if($current_page != 1) {
        echo "\t\t<li><a class=\"ico inline arrowleft pointer\" onclick=\"loadPage('";
        echo $subtable;
        echo "','";
        echo $current_page_url;
        echo "','";
        echo $current_page - 1;
        echo "','";
        echo $rows_per_page;
        echo "');\">";
        echo __("previous");
        echo "</a></li>\n\t\t";
    }
    echo "\t\t\n\t\t\n\t\t";
    for ($p = 1; $p <= $pages; $p++) {
        if($range_start <= $p && $p <= $range_end) {
            echo "<li";
            if($p == $current_page) {
                echo " class=\"on\"";
            }
            echo "><a class=\"pointer\" onclick=\"loadPage('";
            echo $subtable;
            echo "','";
            echo $current_page_url;
            echo "','";
            echo $p;
            echo "','";
            echo $rows_per_page;
            echo "');\">";
            echo $p;
            echo "</a></li>";
        } elseif($p == 1 && 1 < $range_start) {
            echo "<li><a class=\"pointer\" onclick=\"loadPage('";
            echo $subtable;
            echo "','";
            echo $current_page_url;
            echo "','";
            echo $p;
            echo "','";
            echo $rows_per_page;
            echo "');\">";
            echo $p;
            echo "</a></li>";
            if(2 < $range_start) {
                echo "<li><span>...</span></li>";
            }
        } elseif($p == $pages && $range_end < $pages) {
            if($range_end < $pages - 1) {
                echo "<li><span>...</span></li>";
            }
            echo "<li><a class=\"pointer\" onclick=\"loadPage('";
            echo $subtable;
            echo "','";
            echo $current_page_url;
            echo "','";
            echo $p;
            echo "','";
            echo $rows_per_page;
            echo "');\">";
            echo $p;
            echo "</a></li>";
        }
    }
    echo "\t\t\n\t\t";
    if($current_page != $pages) {
        echo "\t\t<li><a class=\"ico inline arrowright pointer\" onclick=\"loadPage('";
        echo $subtable;
        echo "','";
        echo $current_page_url;
        echo "','";
        echo $current_page + 1;
        echo "','";
        echo $rows_per_page;
        echo "');\">";
        echo __("next");
        echo "</a></li>\n\t\t";
    }
    echo "\t</ul>\n\t</div><br />\n\t";
}
function print_r_pre($a)
{
    echo "<pre>";
    print_r($a);
    echo "</pre>";
}
function double_procent($string)
{
    return str_replace("%", "%%", $string);
}
function replace_special_chars($text)
{
    $text = mb_convert_encoding($text, "HTML-ENTITIES", "UTF-8");
    $text = preg_replace(["/&szlig;/", "/&(..)lig;/", "/&([aouAOU])uml;/", "/&(.)[^;]*;/"], ["ss", "\$1", "\$1e", "\$1"], $text);
    $text = strtolower($text);
    $text = preg_replace("/(&|'|\"|<|>)/", "", $text);
    $text = preg_replace("/\\s+/", "_", trim($text));
    $text = htmlentities($text, ENT_NOQUOTES);
    $text = preg_replace("/&([a-z]{1})([a-z])+;/", "\$1", $text);
    return $text;
}
function btwcheck($debtor_id, $tax, $taxtype = "line", $new_customer = false)
{
    global $company;
    if(0 < $debtor_id) {
        if($new_customer === true) {
            require_once "class/newcustomer.php";
            $debtor = new newcustomer();
            $debtor->Identifier = $debtor_id;
            $debtor->show();
        } else {
            require_once "class/debtor.php";
            $debtor = new debtor();
            $debtor->Identifier = $debtor_id;
            $debtor->show();
            if($debtor->TaxableSetting == "no") {
                return 0;
            }
            if($debtor->TaxableSetting == "yes") {
                return $tax;
            }
        }
        $debtor_country = $debtor->InvoiceCountry && $debtor->InvoiceAddress ? $debtor->InvoiceCountry : $debtor->Country;
        $result_db = Database_Model::getInstance()->get("HostFact_Settings_TaxRules")->where("CountryCode", $debtor_country)->execute();
        if($result_db) {
            foreach ($result_db as $v) {
                if(($v->StateCode == "all" || $v->StateCode == "same" && $debtor->State == $company->State || $v->StateCode == "other" && $debtor->State != $company->State) && ($v->Restriction == "all" || $v->Restriction == "company" && $debtor->CompanyName || $v->Restriction == "company_vat" && $debtor->CompanyName && $debtor->TaxNumber || $v->Restriction == "individual" && (!$debtor->CompanyName || !$debtor->TaxNumber))) {
                    if($taxtype == "line" && !is_null($v->TaxLevel1)) {
                        return $v->TaxLevel1;
                    }
                    if($taxtype == "total" && !is_null($v->TaxLevel2)) {
                        return $v->TaxLevel2;
                    }
                }
            }
        }
        $eu_countries = [];
        $result_db = Database_Model::getInstance()->get("HostFact_Settings_Countries")->where("EUCountry", "yes")->execute();
        if($result_db) {
            foreach ($result_db as $v) {
                $eu_countries[] = $v->CountryCode;
            }
        }
        $result_db = Database_Model::getInstance()->get("HostFact_Settings_TaxRules")->where("CountryCode", ["IN" => ["all", "other", "otherEU", "nonEU"]])->execute();
        if($result_db) {
            foreach ($result_db as $v) {
                if(($v->CountryCode == "all" || $v->CountryCode == "other" && $company->Country != $debtor_country || $v->CountryCode == "otherEU" && in_array($debtor_country, $eu_countries) && $company->Country != $debtor_country || $v->CountryCode == "nonEU" && !in_array($debtor_country, $eu_countries)) && ($v->StateCode == "all" || $v->StateCode == "same" && $debtor->State == $company->State || $v->StateCode == "other" && $debtor->State != $company->State) && ($v->Restriction == "all" || $v->Restriction == "company" && $debtor->CompanyName || $v->Restriction == "company_vat" && $debtor->CompanyName && $debtor->TaxNumber || $v->Restriction == "individual" && (!$debtor->CompanyName || !$debtor->TaxNumber))) {
                    if($taxtype == "line" && !is_null($v->TaxLevel1)) {
                        return $v->TaxLevel1;
                    }
                    if($taxtype == "total" && !is_null($v->TaxLevel2)) {
                        return $v->TaxLevel2;
                    }
                }
            }
        }
        return $tax;
    } else {
        return $tax;
    }
}
function createLog($type, $reference, $action, $values = "", $translate = true, $date = "")
{
    if(empty($reference)) {
        return false;
    }
    require_once "class/logfile.php";
    $logfile = new logfile();
    $logfile->Type = $type;
    $logfile->Reference = $reference;
    $logfile->Translate = $translate ? "yes" : "no";
    $logfile->Action = $action;
    $logfile->Values = $values;
    if($date != "") {
        $logfile->Date = $date;
    }
    $logfile->add();
}
function getFileSizeUnit($filesize)
{
    $unit = 0;
    for ($size_array = ["B", "KB", "MB"]; 1024 < $filesize; $unit++) {
        $filesize = $filesize / 1024;
    }
    return ["size" => number_format($filesize, 2, AMOUNT_DEC_SEPERATOR, AMOUNT_THOU_SEPERATOR), "unit" => $size_array[$unit]];
}
function getFileType($filename, $return = false)
{
    $array_filetypes = ["bmp", "doc", "gif", "jpg", "pdf", "png", "ppt", "rar", "txt", "xls", "zip"];
    $filetype = strtolower(substr($filename, strrpos($filename, ".") + 1));
    if(in_array($filetype, $array_filetypes)) {
        if($return) {
            return $filetype;
        }
        echo $filetype;
    } else {
        if($return) {
            return "unknown";
        }
        echo "unknown";
    }
}
function vat($amount)
{
    if(is_numeric($amount)) {
        if(round((double) $amount, 0) == round((double) $amount, 2)) {
            $amount = number_format($amount, 0, AMOUNT_DEC_SEPERATOR, AMOUNT_THOU_SEPERATOR);
        } elseif((string) round($amount, 1) === (string) $amount) {
            $amount = number_format($amount, 1, AMOUNT_DEC_SEPERATOR, AMOUNT_THOU_SEPERATOR);
        } else {
            $amount = number_format($amount, 2, AMOUNT_DEC_SEPERATOR, AMOUNT_THOU_SEPERATOR);
        }
        return $amount;
    }
    return $amount;
}
function get_dashboard_statistics()
{
    $x = 60;
    echo "<div id=\"notification_sidebar_hr\" class=\"hr\"></div><div id=\"notification_sidebar\">";
    if(file_exists("temp/stats_summary.php") && time() < filemtime("temp/stats_summary.php") + $x) {
        include_once "temp/stats_summary.php";
        if(function_exists("get_stats_summary")) {
            get_stats_summary();
        }
    }
    echo "</div>";
}
function kb_execute_query($data)
{
    $data["help_language"] = LANGUAGE_CODE;
    $ch = curl_init();
    settings::disableSSLVerificationIfNeeded($ch);
    curl_setopt($ch, CURLOPT_URL, "https://help.hostfact.nl/interface.php");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $result = curl_exec($ch);
    if($result) {
        curl_close($ch);
        $array = json_decode($result, true);
        return $array;
    }
    curl_close($ch);
    return false;
}
function filter_export_menu_notification($array)
{
    if(!checkright(U_EXPORT_EDIT, false)) {
        return $array;
    }
    if(isset($_SESSION["export_menu_notification"])) {
        $accounting_array = $_SESSION["export_menu_notification"];
    } else {
        $accounting_array = [];
        require_once "3rdparty/export/class.export.php";
        $export = new export();
        $package_list = $export->getPackages();
        if(!empty($package_list)) {
            foreach ($package_list as $package) {
                if(@file_exists("3rdparty/export/" . $package . "/" . $package . "_new.php") && @file_exists("3rdparty/export/" . $package . "/version.php")) {
                    require_once "3rdparty/export/" . $package . "/" . $package . "_new.php";
                    $className = "export_accounting_package_" . $package;
                    $export = new $className();
                    $error_counter = $export->setAdministrationCounter();
                    if(0 < $error_counter) {
                        $accounting_array[$package] = $error_counter;
                    }
                }
            }
        }
        $_SESSION["export_menu_notification"] = $accounting_array;
    }
    $array = array_merge($array, $accounting_array);
    return $array;
}
function filter_export_sidebar_notifications($html)
{
    if(!checkright(U_EXPORT_EDIT, false)) {
        return $html;
    }
    $accounting_errors = filter_export_menu_notification([]);
    foreach ($accounting_errors as $_package => $_error) {
        $html .= "<a class=\"summary_stat\" href=\"" . url_generator("exportaccounting", false, ["module" => $_package]) . "\"><div class=\"number red\">" . $_error . "</div><div class=\"description\">";
        $html .= __("export accounting - errors during export");
        $html .= "</div><br clear=\"all\" /></a>";
    }
    return $html;
}
function delete_stats_summary()
{
    @unlink("temp/stats_summary.php");
}
function hasCronCollision()
{
    $current_setting = Database_Model::getInstance()->getOne("HostFact_Settings")->where("Variable", "CRONJOB_IS_RUNNING")->execute();
    if($current_setting !== false && ($current_setting->Value == "" || 30 <= abs(strtotime($current_setting->Value) - time()) / 60)) {
        global $settings;
        $settings->Variable = "CRONJOB_IS_RUNNING";
        $settings->Value = date("Y-m-d H:i:s");
        $settings->edit();
        $hash = function_exists("random_int") ? random_int(1, 999) : uniqid();
        if(defined("CRONJOB_COLLISION_HASH")) {
            Database_Model::getInstance()->update("HostFact_Settings", ["Value" => $hash])->where("Variable", "CRONJOB_COLLISION_HASH")->execute();
        } else {
            Database_Model::getInstance()->insert("HostFact_Settings", ["Variable" => "CRONJOB_COLLISION_HASH", "Value" => $hash])->execute();
        }
        usleep(200000);
        $value = Database_Model::getInstance()->getOne("HostFact_Settings", ["Value"])->where("Variable", "CRONJOB_COLLISION_HASH")->execute();
        if($value->Value == $hash) {
            return false;
        }
        createmessagelog("error", __("cron collision detected"), "", "", 0, true);
        return true;
    }
    createmessagelog("error", __("cron collision detected"), "", "", 0, true);
    return true;
}
function hasApiCollision()
{
    $current_setting = Database_Model::getInstance()->getOne("HostFact_Settings")->where("Variable", "API_IS_RUNNING")->execute();
    if($current_setting === false) {
        Database_Model::getInstance()->insert("HostFact_Settings", ["Variable" => "API_IS_RUNNING", "Value" => date("Y-m-d H:i:s")])->execute();
    } elseif($current_setting->Value == "" || 2 <= abs(strtotime($current_setting->Value) - time()) / 60) {
        Database_Model::getInstance()->update("HostFact_Settings", ["Value" => date("Y-m-d H:i:s")])->where("Variable", "API_IS_RUNNING")->execute();
    } else {
        return true;
    }
    $hash = function_exists("random_int") ? random_int(1, 999) : uniqid();
    if(defined("API_COLLISION_HASH")) {
        Database_Model::getInstance()->update("HostFact_Settings", ["Value" => $hash])->where("Variable", "API_COLLISION_HASH")->execute();
    } else {
        Database_Model::getInstance()->insert("HostFact_Settings", ["Variable" => "API_COLLISION_HASH", "Value" => $hash])->execute();
    }
    usleep(200000);
    $value = Database_Model::getInstance()->getOne("HostFact_Settings", ["Value"])->where("Variable", "API_COLLISION_HASH")->execute();
    if($value->Value == $hash) {
        return false;
    }
    return true;
}
function isEmptyFloat($value)
{
    return $value === "" || $value == 0 && (!is_string($value) || is_numeric($value));
}

?>