<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "<script type=\"text/javascript\" src=\"js/dashboard.js?v=";
echo JSFILE_NOCACHE;
echo "\"></script>\n<script type=\"text/javascript\" src=\"js/raphael.js\"></script>\n<script type=\"text/javascript\" src=\"js/raphael.popup.js\"></script>\n<div id=\"submenu\">\n\t<ul>\n\t\t<li ";
if(isset($current_page_url) && strpos($current_page_url, "index") !== false) {
    echo "class=\"active\"";
}
echo "><a href=\"index.php\">";
echo __("menu.dashboard");
echo "</a></li>\n\t\t";
if(U_AGENDA_SHOW) {
    echo "<li ";
    if(isset($current_page_url) && strpos($current_page_url, "agenda") !== false) {
        echo "class=\"active\"";
    }
    echo "><a href=\"agenda.php\">";
    echo __("menu.agenda");
    echo "</a></li>";
}
echo "\t</ul>\n</div>\n\n\n";
get_dashboard_statistics();

?>