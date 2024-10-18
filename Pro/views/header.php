<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">\n<head>\n\t<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n\t<meta http-equiv=\"X-UA-Compatible\" content=\"IE=Edge\"/>\n\t<title>";
echo isset($wfh_page_title) && $wfh_page_title != "" ? $wfh_page_title . " - " : "";
echo __("software name");
echo "</title>\n\t<link rel=\"apple-touch-icon\" sizes=\"180x180\" href=\"images/favicons/apple-touch-icon.png\">\n\t<link rel=\"icon\" type=\"image/png\" sizes=\"32x32\" href=\"images/favicons/favicon-32x32.png\">\n\t<link rel=\"icon\" type=\"image/png\" sizes=\"16x16\" href=\"images/favicons/favicon-16x16.png\">\n\t<link rel=\"manifest\" href=\"images/favicons/manifest.json\">\n\t<link rel=\"mask-icon\" href=\"images/favicons/safari-pinned-tab.svg\" color=\"#184b64\">\n\t<link rel=\"shortcut icon\" href=\"images/favicons/favicon.ico\">\n\t<meta name=\"msapplication-config\" content=\"images/favicons/browserconfig.xml\">\n\t<meta name=\"theme-color\" content=\"#ffffff\">\n\t<link href=\"js/jquery-ui-1.12.1.custom/jquery-ui.css?v=";
echo JSFILE_NOCACHE;
echo "\" rel=\"stylesheet\">\n\t<link type=\"text/css\" href=\"css/global.css?v=";
echo JSFILE_NOCACHE;
echo "\" rel=\"stylesheet\" />\n\t<link rel=\"stylesheet\" type=\"text/css\" href=\"3rdparty/fancybox/jquery.fancybox.min.css\" media=\"screen\" />\n\t<script type=\"text/javascript\" src=\"js/translated_vars.php?v=";
echo JSFILE_NOCACHE;
echo "&amp;csrf_token=";
echo CSRF_Model::getToken(true);
echo "\"></script>\n\t<script type=\"text/javascript\" src=\"js/jquery-3.4.0.min.js\"></script>\n\t<script type=\"text/javascript\" src=\"js/jquery-ui-1.12.1.custom/jquery-ui.js\"></script>\n\t<script type=\"text/javascript\" src=\"3rdparty/fancybox/jquery.fancybox.min.js\"></script>\n\t<script type=\"text/javascript\" src=\"js/global.js?v=";
echo JSFILE_NOCACHE;
echo "\"></script>\n\t<script type=\"text/javascript\" src=\"js/search.js?v=";
echo JSFILE_NOCACHE;
echo "\"></script>\n\n\t<!--[if lt IE 9]>\n\t   <link rel=\"stylesheet\" type=\"text/css\" href=\"css/max1530.css?v=";
echo JSFILE_NOCACHE;
echo "\" media=\"\" id=\"stylesheet-a\" />\n\t   <link rel=\"stylesheet\" type=\"text/css\" href=\"css/min1530.css?v=";
echo JSFILE_NOCACHE;
echo "\" media=\"\" id=\"stylesheet-b\" />\n\t   <link rel=\"stylesheet\" type=\"text/css\" href=\"css/min1762.css?v=";
echo JSFILE_NOCACHE;
echo "\" media=\"\" id=\"stylesheet-c\" />\n\t \n\t   <script type=\"text/javascript\" src=\"css/css-media-query-ie.js\"></script>\n\t<![endif]-->\n\t";
include_once "css/tablet.php";
echo "</head>\n<body class=\"lang-";
echo strtolower(substr(LANGUAGE_CODE, 0, 2));
echo "\">\n\n<div id=\"header\">\n\t<img src=\"images/logo.png?v=";
echo JSFILE_NOCACHE;
echo "\" class=\"logo\" alt=\"";
echo SOFTWARE_NAME;
echo "\" />\n\t";
if(defined("IS_DEMO") && IS_DEMO) {
} else {
    echo "\t\t<div class=\"version\"><a href=\"";
    echo INTERFACE_URL_CUSTOMERPANEL;
    echo "/download.php?license=";
    echo urlencode(LICENSE);
    echo "&amp;from=";
    echo SOFTWARE_VERSION;
    if(function_exists("ioncube_loader_version")) {
        echo "&amp;ioncube=" . ioncube_loader_version();
    }
    echo "\" target=\"_blank\">";
    echo __("version") . " " . SOFTWARE_VERSION;
    echo "</a></div>\n\t\t";
}
echo "\t";
if(defined("IS_DEMO") && IS_DEMO) {
    echo "<div class=\"message\">";
    echo __("demo - request for a trial");
    echo "</div>";
} else {
    $has_message = false;
    if(defined("INT_WF_ACTIVE_DEBTOR_LIMIT") && isset($_SESSION["active_clients"])) {
        if(1 <= $_SESSION["active_clients"] / INT_WF_ACTIVE_DEBTOR_LIMIT) {
            $has_message = true;
            echo "<div class=\"message\"><a href=\"debtors.php?page=upgrade\">";
            echo __("you are not able to add extra clients, because your limit has been reached");
            echo "</a></div>";
        } elseif(INT_WF_ACTIVE_DEBTOR_LIMIT - $_SESSION["active_clients"] < 50) {
            $has_message = true;
            echo "<div class=\"message\"><a href=\"debtors.php?page=upgrade\">";
            echo sprintf(__("client limit almost reached, still x clients remaining"), max(0, INT_WF_ACTIVE_DEBTOR_LIMIT - $_SESSION["active_clients"]));
            echo "</a></div>";
        }
    }
    $checkVersion = checkVersion();
    if($checkVersion) {
        $has_message = true;
        echo "<div class=\"message\">";
        checkVersion(true);
        echo "</div>";
    } else {
        require_once __DIR__ . "/../3rdparty/modules/hostfact_module.php";
        $updatable_modules = hostfact_module::newModuleUpdatesAvailable(true);
        if(!empty($updatable_modules)) {
            $has_message = true;
            echo "\t\t\t\t<div class=\"message\"><a href=\"modules.php\">";
            echo count($updatable_modules) <= 2 ? sprintf(__("new version available for 1 or 2 modules"), implode(" " . __("and") . " ", $updatable_modules)) : sprintf(__("new version available for x modules"), count($updatable_modules));
            echo "</a></div>\n\t\t\t\t";
        }
        unset($updatable_modules);
    }
    if($has_message === false && isset($_SESSION["header_message"]) && $_SESSION["header_message"]) {
        echo "<div class=\"message\">";
        echo $_SESSION["header_message"];
        echo "</div>";
    }
}
echo "\t<a href=\"login.php?action=logout\" class=\"logout\">";
echo __("logout");
echo "</a>\n\t<a href=\"#\" class=\"user_fullname\">";
global $account;
echo $account->Name;
echo "</a>\n</div>\n\n<div id=\"menu\">\n\t<ul>\n\t\t<li><a href=\"index.php\">";
echo __("menu.dashboard");
echo "</a></li>\n\t\t\n\t\t";
if(U_DEBTOR_SHOW || U_CREDITOR_SHOW) {
    echo "\t\t<li><a href=\"debtors.php\">";
    echo __("menu.contacts");
    echo "</a>\n\t\t\t<ul class=\"sub-menu\">\n\t\t\t\t";
    if(U_DEBTOR_SHOW) {
        echo "\t\t\t\t<li>\n\t\t\t\t\t<div>\n\t\t\t\t\t\t<a href=\"debtors.php\">";
        echo __("menu.debtors");
        echo "</a>\n\t\t\t\t\t\t<span>\n\t\t\t\t\t\t\t<a href=\"debtors.php?page=add\" class=\"sub-menu-sublink\">";
        echo __("menu.debtors.add");
        echo "</a>,\n\t\t\t\t\t\t\t<a href=\"debtors.php?page=groups\" class=\"sub-menu-sublink\">";
        echo __("menu.debtors.groups");
        echo "</a>\n\t\t\t\t\t\t</span>\n\t\t\t\t\t</div>\n\t\t\t\t</li>\n\t\t\t\t";
    }
    echo "\t\t\t\t";
    if(U_CREDITOR_SHOW) {
        echo "\t\t\t\t<li>\n\t\t\t\t\t<div>\n\t\t\t\t\t\t<a href=\"creditors.php\">";
        echo __("menu.creditors");
        echo "</a>\n\t\t\t\t\t\t<span>\n\t\t\t\t\t\t\t<a href=\"creditors.php?page=add\" class=\"sub-menu-sublink\">";
        echo __("menu.creditors.add");
        echo "</a>,\n\t\t\t\t\t\t\t<a href=\"creditors.php?page=groups\" class=\"sub-menu-sublink\">";
        echo __("menu.creditors.groups");
        echo "</a>,\n\t\t\t\t\t\t\t";
        if(U_CREDITOR_INVOICE_SHOW) {
            echo "<a href=\"creditors.php?page=overview_creditinvoice\" class=\"sub-menu-sublink\">";
            echo __("menu.creditors.invoice.overview");
            echo "</a>";
        }
        echo "\t\t\t\t\t\t</span>\n\t\t\t\t\t</div>\n\t\t\t\t</li>\n\t\t\t\t";
    }
    echo "\t\t\t\t";
    if(U_DEBTOR_EDIT) {
        echo "\t\t\t\t<li class=\"no_subs\">\n\t\t\t\t\t<div>\n\t\t\t\t\t\t<a href=\"debtors.php?page=mailing\">";
        echo __("menu.debtors.mailing");
        echo "</a>\n\t\t\t\t\t</div>\n\t\t\t\t</li>\n\t\t\t\t";
    }
    echo "\t\t\t</ul>\n\t\t</li>\n\t\t";
}
echo "\t\t";
$items_menu = [];
if(U_DOMAIN_SHOW) {
    $items_menu[] = ["type" => "domain", "title" => __("menu.domains"), "url" => "domains.php", "children" => [["title" => __("menu.domains.add"), "url" => "services.php?page=add&type=domain"]]];
}
if(U_HOSTING_SHOW) {
    $items_menu[] = ["type" => "hosting", "title" => __("menu.hosting"), "url" => "hosting.php", "children" => [["title" => __("menu.hosting.add"), "url" => "services.php?page=add&type=hosting"]]];
}
if(U_SERVICE_SHOW) {
    $items_menu[] = ["type" => "other", "title" => __("menu.other.services"), "url" => "services.php", "children" => [["title" => __("menu.other.services.add"), "url" => "services.php?page=add"]]];
}
$items_menu = do_filter("service_menu", $items_menu);
if(!empty($items_menu)) {
    echo "\t\t<li><a href=\"services.php\">";
    echo __("menu.services");
    echo "</a>\n\t\t\t<ul class=\"sub-menu\">\n\t\t\t\t";
    show_menu($items_menu);
    echo "\t\t\t</ul>\t\t\n\t\t</li>\n\t\t";
}
echo "\t\t";
if(U_INVOICE_SHOW || U_PRICEQUOTE_SHOW || U_ORDER_SHOW || U_DOMAIN_SHOW || U_HOSTING_SHOW || U_SERVICE_SHOW) {
    echo "\t\t<li><a href=\"invoices.php\">";
    echo __("menu.invoicing");
    echo "</a>\n\t\t\t<ul class=\"sub-menu\">\n\t\t\t\t";
    if(U_INVOICE_SHOW) {
        echo "\t\t\t\t<li>\n\t\t\t\t\t<div>\n\t\t\t\t\t\t<a href=\"invoices.php\">";
        echo __("menu.invoices");
        echo "</a>\n\t\t\t\t\t\t<span>\n\t\t\t\t\t\t\t<a href=\"invoices.php?page=add\" class=\"sub-menu-sublink\">";
        echo __("menu.invoices.add");
        echo "</a>,\n\n\t\t\t\t\t\t\t<a href=\"directdebit.php\" class=\"sub-menu-sublink\">";
        echo __("menu.invoices.inc");
        echo "</a>";
        if(INT_SUPPORT_BANKIMPORT_CAMT) {
            echo ", ";
        }
        echo "                            \n                            ";
        if(INT_SUPPORT_BANKIMPORT_CAMT) {
            echo "<a href=\"invoices.php?page=bankstatement\" class=\"sub-menu-sublink\">";
            echo __("menu.invoices.mutations");
            echo "</a>";
        }
        echo "\t\t\t\t\t\t</span>\n\t\t\t\t\t</div>\n\t\t\t\t</li>\n\t\t\t\t";
    }
    echo "\t\t\t\t";
    if(U_PRICEQUOTE_SHOW) {
        echo "\t\t\t\t<li>\n\t\t\t\t\t<div>\n\t\t\t\t\t\t<a href=\"pricequotes.php\">";
        echo __("menu.pricequotes");
        echo "</a>\n\t\t\t\t\t\t<span>\n\t\t\t\t\t\t\t<a href=\"pricequotes.php?page=add\" class=\"sub-menu-sublink\">";
        echo __("menu.pricequotes.add");
        echo "</a>\n\t\t\t\t\t\t</span>\n\t\t\t\t\t</div>\n\t\t\t\t</li>\n\t\t\t\t";
    }
    echo "\t\t\t\t";
    if(U_ORDER_SHOW) {
        echo "\t\t\t\t<li class=\"no_subs\">\n\t\t\t\t\t<div>\n\t\t\t\t\t\t<a href=\"orders.php\">";
        echo __("menu.orders");
        echo "</a>\n\t\t\t\t\t</div>\n\t\t\t\t</li>\n\t\t\t\t";
    }
    echo "\t\t\t\t";
    if(U_DOMAIN_SHOW || U_HOSTING_SHOW || U_SERVICE_SHOW) {
        echo "\t\t\t\t<li>\n\t\t\t\t\t<div>\n\t\t\t\t\t\t<a href=\"subscriptions.php\">";
        echo __("menu.subscriptions");
        echo "</a>\n\t\t\t\t\t\t<span>\n\t\t\t\t\t\t\t<a href=\"services.php?page=terminations\" class=\"sub-menu-sublink\">";
        echo strtolower(__("terminations"));
        echo "</a>, \n\t\t\t\t\t\t\t<a href=\"services.php?page=termination_actions\" class=\"sub-menu-sublink\">";
        echo strtolower(__("termination actions sidebar"));
        echo "</a>\n\t\t\t\t\t\t</span>\n\t\t\t\t\t</div>\n\t\t\t\t\t\n\t\t\t\t\t\n\t\t\t\t</li>\n\t\t\t\t";
    }
    echo "\t\t\t</ul>\n\t\t</li>\n\t\t";
}
echo "\n\t\t";
if(U_TICKET_SHOW && TICKET_USE == 1) {
    echo "\t\t<li><a href=\"tickets.php\">";
    echo __("menu.ticketsystem");
    echo "</a></li>\n\t\t";
}
echo "\n\t\t";
if(U_STATISTICS_SHOW) {
    echo "\t\t<li><a href=\"statistics.php\">";
    echo __("menu.statistics");
    echo "</a>\n\t\t\t<ul class=\"sub-menu\">\n\t\t\t\t<li class=\"no_subs\">\n\t\t\t\t\t<div>\n\t\t\t\t\t\t<a href=\"statistics.php\">";
    echo __("revenues and expenses");
    echo "</a>\n\t\t\t\t\t</div>\n\t\t\t\t</li>\n\t\t\t\t<li class=\"no_subs\">\n\t\t\t\t\t<div>\n\t\t\t\t\t\t<a href=\"statistics.php?page=period\">";
    echo __("future revenues");
    echo "</a>\n\t\t\t\t\t</div>\n\t\t\t\t</li>\n                <li class=\"no_subs\">\n                    <div>\n                        <a href=\"statistics.php?page=preinvoiced\">";
    echo __("pre-invoiced turnover");
    echo "</a>\n                    </div>\n                </li>\n\t\t\t\t<li class=\"no_subs\">\n\t\t\t\t\t<div>\n\t\t\t\t\t\t<a href=\"statistics.php?page=btw\">";
    echo __("vat return");
    echo "</a>\n\t\t\t\t\t</div>\n\t\t\t\t</li>\n\t\t\t</ul>\n\t\t\n\t\t</li>\n\t\t";
}
echo "\n\t\t";
if(@file_exists("menu_custom.php")) {
    echo "<li>";
    include_once "menu_custom.php";
    echo "</li>";
}
echo "\t\t\n\t\t";
if(U_COMPANY_SHOW || U_PRODUCT_SHOW || U_LAYOUT_SHOW || U_SERVICEMANAGEMENT_SHOW || U_LOGFILE_SHOW || U_EXPORT_SHOW) {
    echo "\t\t<li><a>";
    echo __("menu.management");
    echo "</a>\n\t\t\t<ul class=\"sub-menu\">\n\t\t\t\t";
    if(U_COMPANY_SHOW) {
        echo "\t\t\t\t<li>\n\t\t\t\t\t<div>\n\t\t\t\t\t\t<a href=\"company.php\">";
        echo __("menu.companyinfo");
        echo "</a>\n\t\t\t\t\t\t<span>\n\t\t\t\t\t\t\t<a href=\"company.php?page=accounts\" class=\"sub-menu-sublink\">";
        echo __("menu.employees.manage");
        echo "</a>\n\t\t\t\t\t\t</span>\n\t\t\t\t\t</div>\n\t\t\t\t</li>\n\t\t\t\t";
    }
    echo "\t\t\t\t";
    if(U_PRODUCT_SHOW) {
        echo "\t\t\t\t<li>\n\t\t\t\t\t<div>\n\t\t\t\t\t\t<a href=\"products.php\">";
        echo __("menu.products.manage");
        echo "</a>\n\t\t\t\t\t\t<span>\n\t\t\t\t\t\t\t<a href=\"products.php?page=add\" class=\"sub-menu-sublink\">";
        echo __("menu.products.add");
        echo "</a>,\n\t\t\t\t\t\t\t<a href=\"products.php?page=groups\" class=\"sub-menu-sublink\">";
        echo __("menu.products.groups");
        echo "</a>,\n\t\t\t\t\t\t\t<a href=\"discount.php\" class=\"sub-menu-sublink\">";
        echo __("menu.discount");
        echo "</a>\n\t\t\t\t\t\t</span>\n\t\t\t\t\t</div>\n\t\t\t\t</li>\n\t\t\t\t";
    }
    echo "\t\t\t\t";
    if(U_LAYOUT_SHOW) {
        echo "\t\t\t\t<li>\n\t\t\t\t\t<div>\n\t\t\t\t\t\t<a href=\"templates.php\">";
        echo __("menu.templates");
        echo "</a>\n\t\t\t\t\t\t<span>\n\t\t\t\t\t\t\t<a href=\"templates.php?page=invoice\" class=\"sub-menu-sublink\">";
        echo __("menu.templates.invoice");
        echo "</a>,\n\t\t\t\t\t\t\t<a href=\"templates.php?page=pricequote\" class=\"sub-menu-sublink\">";
        echo __("menu.templates.pricequote");
        echo "</a>,\n\t\t\t\t\t\t\t<a href=\"templates.php?page=other\" class=\"sub-menu-sublink\">";
        echo __("menu.templates.other");
        echo "</a>,<br />\n\t\t\t\t\t\t\t<a href=\"templates.php?page=email\" class=\"sub-menu-sublink\">";
        echo __("menu.templates.email");
        echo "</a>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t</span>\n\t\t\t\t\t</div>\n\t\t\t\t</li>\n\t\t\t\t";
    }
    echo "\t\t\t\t";
    if(U_SERVICEMANAGEMENT_SHOW) {
        $items_menu = [];
        $items_menu[] = ["title" => strtolower(__("menu.registrars")), "url" => "registrars.php"];
        $items_menu[] = ["title" => strtolower(__("menu.handles")), "url" => "handles.php"];
        $items_menu[] = ["title" => strtolower(__("menu.extensions")), "url" => "topleveldomains.php"];
        $items_menu[] = ["title" => strtolower(__("menu.servers")), "url" => "servers.php"];
        $items_menu[] = ["title" => strtolower(__("menu.service.hosting")), "url" => "packages.php"];
        $items_menu = do_filter("service_management_menu", $items_menu);
        echo "\t\t\t\t\n\t\t\t\t\t<li>\n\t\t\t\t\t\t<div>\n\t\t\t\t\t\t\t<a href=\"registrars.php\">";
        echo __("menu.services");
        echo "</a>\n\t\t\t\t\t\t\t";
        show_menu($items_menu, 1);
        echo "\t\t\t\t\t\t</div>\n\t\t\t\t\t</li>\n\t\t\t\t\t";
    }
    echo "\t\t\t\t<li class=\"no_subs\">\n\t\t\t\t\t<div>\n\t\t\t\t\t\t<a href=\"clientareachanges.php\">\n\t\t\t\t\t\t\t";
    echo __("menu.modifications");
    echo "\t\t\t\t\t\t</a>\n\t\t\t\t\t</div>\n\t\t\t\t</li>\n\t\t\t\t";
    if(U_LOGFILE_SHOW) {
        echo "\t\t\t\t<li class=\"no_subs\">\n\t\t\t\t\t<div>\n\t\t\t\t\t\t<a href=\"logfile.php\">";
        echo __("menu.loglines");
        echo "</a>\n\t\t\t\t\t</div>\n\t\t\t\t</li>\n\t\t\t\t";
    }
    echo "\t\t\t\t";
    if(U_EXPORT_SHOW) {
        echo "\t\t\t\t<li class=\"no_subs\">\n\t\t\t\t\t<div>\n\t\t\t\t\t\t<a href=\"export.php\">";
        echo __("menu.export");
        echo "</a>\n\t\t\t\t\t</div>\n\t\t\t\t</li>\n\t\t\t\t";
    }
    echo "\t\t\t\t</ul>\n\t\t</li>\n\t\t";
}
echo "\t\t";
if(U_SETTINGS_SHOW || U_CUSTOMERPANEL_SHOW || U_ORDERFORM_SHOW || U_PAYMENT_SHOW || U_SERVICESETTING_SHOW) {
    echo "\t\t<li><a ";
    if(U_SETTINGS_SHOW) {
        echo "href=\"settings.php\"";
    }
    echo ">";
    echo __("menu.settings");
    echo "</a>\t\t\n\t\t\t\t<ul class=\"sub-menu\">\n\t\t\t\t";
    if(U_SETTINGS_SHOW) {
        echo "\t\t\t\t<li>\n\t\t\t\t\t<div>\n\t\t\t\t\t\t<a href=\"settings.php\">";
        echo __("menu.software.settings");
        echo "</a>\n\t\t\t\t\t\t<span>\n\t\t\t\t\t\t\t<a href=\"settings.php?page=general\" class=\"sub-menu-sublink\">";
        echo __("menu.settings.general");
        echo "</a>,\n\t\t\t\t\t\t\t<a href=\"settings.php?page=finance\" class=\"sub-menu-sublink\">";
        echo __("menu.settings.invoices.pricequotes");
        echo "</a>,\n\t\t\t\t\t\t\t<a href=\"settings.php?page=mail\" class=\"sub-menu-sublink\">";
        echo __("menu.settings.email");
        echo "</a>,\n\t\t\t\t\t\t\t<a href=\"settings.php?page=automation\" class=\"sub-menu-sublink\">";
        echo __("menu.settings.automation");
        echo "</a>,\n\t\t\t\t\t\t\t<a href=\"settings.php?page=api\" class=\"sub-menu-sublink\">";
        echo __("menu.settings.api");
        echo "</a>,\n\t\t\t\t\t\t\t<a href=\"backup.php\" class=\"sub-menu-sublink\">";
        echo __("menu.settings.backups");
        echo "</a>,\n\t\t\t\t\t\t\t<a href=\"customclientfields.php\" class=\"sub-menu-sublink\">";
        echo __("menu.settings.custom client fields");
        echo "</a>,\n\t\t\t\t\t\t\t<a href=\"termination-procedures.php\" class=\"sub-menu-sublink\">";
        echo strtolower(__("menu.termination procedures"));
        echo "</a>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t</span>\n\t\t\t\t\t</div>\n\t\t\t\t</li>\n\t\t\t\t";
    }
    echo "\t\t\t\t";
    if(U_CUSTOMERPANEL_SHOW) {
        echo "\t\t\t\t\t<li class=\"no_subs\">\n\t\t\t\t\t\t<div>\n\t\t\t\t\t\t\t<a href=\"clientareasettings.php\">\n\t\t\t\t\t\t\t\t";
        echo __("menu.customerpanel");
        echo "\t\t\t\t\t\t\t</a>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</li>\n\t\t\t\t";
    }
    echo "\t\t\t\t";
    if(U_PAYMENT_SHOW) {
        echo "\t\t\t\t<li class=\"no_subs\">\n\t\t\t\t\t<div>\n\t\t\t\t\t\t<a href=\"paymentmethods.php\">";
        echo __("menu.paymentoptions");
        echo "</a>\n\t\t\t\t\t</div>\n\t\t\t\t</li>\n\t\t\t\t";
    }
    echo "\t\t\t\t";
    if(U_ORDERFORM_SHOW) {
        echo "\t\t\t\t<li>\n\t\t\t\t\t<div>\n\t\t\t\t\t\t<a href=\"orderform.php\">";
        echo __("orderforms");
        echo "</a>\n\t\t\t\t\t\t<span>\n\t\t\t\t\t\t\t<a href=\"orderform.php\" class=\"sub-menu-sublink\">";
        echo strtolower(__("orderforms"));
        echo "</a>,\n\t\t\t\t\t\t\t<a href=\"orderform.php#tab-wizard\" class=\"sub-menu-sublink\">";
        echo strtolower(__("whoisform"));
        echo "</a>\n\t\t\t\t\t\t</span>\n\t\t\t\t\t</div>\n\t\t\t\t</li>\n\t\t\t\t";
    }
    echo "\t\t\t\t";
    if(U_SERVICESETTING_SHOW) {
        $items_menu = [["title" => __("menu.settings.domains"), "url" => "settings.php?page=services#tab-domains"], ["title" => strtolower(__("hosting accounts")), "url" => "settings.php?page=services#tab-hosting"]];
        $items_menu = do_filter("service_setting_menu", $items_menu);
        echo "\t\t\t\t\n\t\t\t\t\t<li>\n\t\t\t\t\t\t<div>\n\t\t\t\t\t\t\t<a href=\"settings.php?page=services\">";
        echo __("menu.settings for services");
        echo "</a>\n\t\t\t\t\t\t\t";
        show_menu($items_menu, 1);
        echo "\t\t\t\t\t\t</div>\n\t\t\t\t\t</li>\n\t\t\t\t\t";
    }
    echo "\t\t\t\t";
    if(U_SETTINGS_SHOW) {
        echo "\t\t\t\t<li class=\"no_subs\">\n\t\t\t\t\t<div>\n\t\t\t\t\t\t<a href=\"settings.php?page=tickets\">";
        echo __("menu.ticketsystem");
        echo "</a>\n\t\t\t\t\t</div>\n\t\t\t\t</li>\n\t\t\t\t<li class=\"no_subs\">\n\t\t\t\t\t<div>\n\t\t\t\t\t\t<a href=\"modules.php\">";
        echo __("menu.modules");
        echo "</a>\n\t\t\t\t\t</div>\n\t\t\t\t</li>\n\t\t\t\t";
    }
    echo "\t\t\t</ul>\n\t\t</li>\n\t\t";
}
echo "\t</ul>\n\t\n\t<div class=\"search_block\">\n\t\t<form action=\"search.php?page=show\" method=\"post\" name=\"HeaderSearchForm\">\n\t\t\t<input type=\"text\" class=\"SearchInput\" name=\"SearchInput\" id=\"SearchInput\" autocomplete=\"off\" placeholder=\"";
echo __("header search placeholder");
echo "\" />\n\t\t</form>\n\t\t<img src=\"images/indicator.gif\" style=\"margin-top:8px;\" class=\"search_block_loading\" />\n\t</div>\n\t\n</div>\n\n<div id=\"container\">\n\n<div id=\"sidebar\" style=\"float:none;position:absolute;left:0px;margin:0px;\">\n    ";
if($sidebar_template) {
    if(file_exists("views/" . $sidebar_template)) {
        require_once "views/" . $sidebar_template;
    } elseif(file_exists($sidebar_template)) {
        require_once $sidebar_template;
    }
}
echo "</div>\n\n<div id=\"content\" ";
if(isset($content_has_sidebar) && $content_has_sidebar) {
    echo "class=\"has_sidebar\"";
} elseif(isset($_has_template_canvas) && $_has_template_canvas) {
    echo "class=\"has_template_canvas\"";
}
if(isset($_is_debtor_show) && $_is_debtor_show) {
    echo "style=\"min-width:950px;\"";
} elseif(isset($_has_template_canvas) && $_has_template_canvas) {
    echo "style=\"min-width:" . $block_model->mm_to_px(PDF_PAGE_WIDTH) . "px;\"";
}
echo ">\n";

?>