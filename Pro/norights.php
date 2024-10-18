<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "config.php";
$sidebar_template = "";
require_once "views/header.php";
echo "\n<!--right-->\n<div class=\"right\">\n<!--right-->\n\n\t<div class=\"setting_help_box\">\n\t\t<strong>";
echo __("you have no permission to view this page");
echo "</strong><br />\n\t\t";
echo sprintf(__("you have no permission to view this page explained"), "<a href=\"javascript:history.back();\" class=\"a1 c1\">" . __("previous page") . "</a>");
echo " \n\t</div>\n\n<!--right-->\n</div>\n<!--right-->\n\n\n";
require_once "views/footer.php";

?>