<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "<script type=\"text/javascript\" src=\"js/creditor.js?v=";
echo JSFILE_NOCACHE;
echo "\"></script>\n<script type=\"text/javascript\" src=\"js/creditinvoice.js?v=";
echo JSFILE_NOCACHE;
echo "\"></script>\n<script type=\"text/javascript\" src=\"js/group.js?v=";
echo JSFILE_NOCACHE;
echo "\"></script>\n<div id=\"submenu\">\n\t<ul>\n\t\t<li ";
if(strpos($page, "group") === false && strpos($page, "invoice") === false) {
    echo "class=\"active\"";
}
echo "><a href=\"creditors.php\">";
echo __("creditors");
echo "</a></li>\n\t\t<li ";
if(strpos($page, "group") !== false) {
    echo "class=\"active\"";
}
echo "><a href=\"creditors.php?page=groups\">";
echo __("creditorgroups");
echo "</a></li>\n\t\t";
if(U_CREDITOR_INVOICE_SHOW) {
    echo "<li ";
    if(strpos($page, "invoice") !== false) {
        echo "class=\"active\"";
    }
    echo "><a href=\"creditors.php?page=overview_creditinvoice\">";
    echo __("creditinvoices");
    echo " </a></li>";
}
echo "\t</ul>\n</div>\n\n";
get_dashboard_statistics();

?>