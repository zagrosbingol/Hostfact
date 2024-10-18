<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "<script type=\"text/javascript\" src=\"js/product.js?v=";
echo JSFILE_NOCACHE;
echo "\"></script>\n<script type=\"text/javascript\" src=\"js/group.js?v=";
echo JSFILE_NOCACHE;
echo "\"></script>\n<script type=\"text/javascript\" src=\"js/discount.js?v=";
echo JSFILE_NOCACHE;
echo "\"></script>\n<div id=\"submenu\">\n\t<ul>\n\t\t<li ";
if(strpos($_SERVER["REQUEST_URI"], "group") === false && strpos($_SERVER["REQUEST_URI"], "products.php") !== false) {
    echo "class=\"active\"";
}
echo "><a href=\"products.php\">";
echo __("products");
echo "</a></li>\n\t\t<li ";
if(strpos($_SERVER["REQUEST_URI"], "group") !== false) {
    echo "class=\"active\"";
}
echo "><a href=\"products.php?page=groups\">";
echo __("productgroups");
echo "</a></li>\n\t\t<li ";
if(strpos($_SERVER["REQUEST_URI"], "discount") !== false) {
    echo "class=\"active\"";
}
echo "><a href=\"discount.php\">";
echo __("discount module");
echo "</a></li>\n\t</ul>\n</div>\n\n";
get_dashboard_statistics();

?>