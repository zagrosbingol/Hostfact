<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
if($current_page_url && strpos($current_page_url, "exportaccounting.php") !== false) {
    echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"css/export.css\" media=\"screen\" />";
}
echo "<script type=\"text/javascript\" src=\"js/settings.js?v=";
echo JSFILE_NOCACHE;
echo "\"></script>\n<div id=\"submenu\">\n\t<ul>\n\t\t<li";
if(isset($current_page_url) && strpos($current_page_url, "export") !== false) {
    echo " class=\"active\"";
}
echo "><a href=\"export.php\">";
echo __("export");
echo "</a></li>\n\t</ul>\n</div>\n\t\t\n";
get_dashboard_statistics();

?>