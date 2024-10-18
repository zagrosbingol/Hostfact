<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "<script type=\"text/javascript\" src=\"js/invoice.js?v=";
echo JSFILE_NOCACHE;
echo "\"></script>\n<script type=\"text/javascript\" src=\"js/pricequote.js?v=";
echo JSFILE_NOCACHE;
echo "\"></script>\n<script type=\"text/javascript\" src=\"js/order.js?v=";
echo JSFILE_NOCACHE;
echo "\"></script>\n\n<div id=\"submenu\">\n\t<ul>\n\t\t";
if(U_INVOICE_SHOW) {
    echo "\t\t<li ";
    if(strpos($current_page_url, "invoices") !== false && !in_array($page, ["mutation"])) {
        echo "class=\"active\"";
    }
    echo ">\n            <a href=\"invoices.php\">";
    echo __("menu.invoices");
    echo "</a>\n        </li>\n\t\t\n\t\t<li ";
    if(strpos($current_page_url, "directdebit") !== false) {
        echo "class=\"active\"";
    }
    echo ">\n            <a href=\"directdebit.php\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    echo ucfirst(__("menu.invoices.inc"));
    echo "</a>\n        </li>\n\t\t\n\t\t";
    if(INT_SUPPORT_BANKIMPORT_CAMT) {
        echo "<li ";
        if($page == "bankstatement") {
            echo "class=\"active\"";
        }
        echo "><a href=\"invoices.php?page=bankstatement\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        echo ucfirst(__("menu.invoices.mutations"));
        echo "</a></li>";
    }
    echo "\t\t";
}
echo "\t\t";
if(U_PRICEQUOTE_SHOW) {
    echo "\t\t<li ";
    if(strpos($current_page_url, "pricequotes") !== false) {
        echo "class=\"active\"";
    }
    echo "><a href=\"pricequotes.php\">";
    echo __("menu.pricequotes");
    echo "</a></li>\n\t\t";
}
echo "\t\t";
if(U_ORDER_SHOW) {
    echo "\t\t<li ";
    if(strpos($current_page_url, "orders") !== false) {
        echo "class=\"active\"";
    }
    echo "><a href=\"orders.php\">";
    echo __("menu.orders");
    echo "</a></li>\n\t\t";
}
echo "\t</ul>\n</div>\n\n";
if(U_DOMAIN_SHOW || U_HOSTING_SHOW || U_SERVICE_SHOW) {
    echo "<div id=\"submenu\">\n\t<ul>\n\t\t<li ";
    if(strpos($current_page_url, "subscriptions") !== false) {
        echo "class=\"active\"";
    }
    echo "><a href=\"subscriptions.php\">";
    echo __("menu.subscriptions");
    echo "</a></li>\n\t\t<li ";
    if(strpos($current_page_url, "terminations") !== false) {
        echo "class=\"active\"";
    }
    echo "><a href=\"services.php?page=terminations\">";
    echo __("terminations");
    echo "</a></li>\n\t\t<li ";
    if(strpos($current_page_url, "termination_actions") !== false) {
        echo "class=\"active\"";
    }
    echo "><a href=\"services.php?page=termination_actions\">";
    echo __("termination actions sidebar");
    echo "</a></li>\n\t</ul>\n</div>\n";
}
echo "\n";
get_dashboard_statistics();

?>