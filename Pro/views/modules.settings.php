<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "views/header.php";
echo "\n";
echo isset($message) ? $message : "";
echo "\n<!--form-->\n<form id=\"ModuleForm\" name=\"form_create\" method=\"post\" action=\"modules.php?page=settings\"><fieldset><legend>";
echo sprintf(__("module settings page"), __("module type " . $module_info->ModuleType) . ": " . (isset($version["name"]) && $version["name"] ? htmlspecialchars($version["name"]) : $module_info->Module));
echo "</legend>\n<input type=\"hidden\" name=\"id\" value=\"";
echo $module_info->id;
echo "\" />\n<!--form-->\n\n<!--heading1-->\n<div class=\"heading1\">\n<!--heading1-->\n\n\t<h2>";
echo sprintf(__("module settings page"), __("module type " . $module_info->ModuleType) . ": " . (isset($version["name"]) && $version["name"] ? htmlspecialchars($version["name"]) : $module_info->Module));
echo "</h2>\n\t\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n\t";
if(array_key_exists($module_info->Module, $additional_modules_all)) {
    $module->module_form_settings();
}
echo "\n\t<br />\n\t\t\t\t\t\n\t<p class=\"align_right\">\n\t\t<span id=\"loader_circle\" class=\"hide\">\n\t\t\t<img src=\"images/icon_circle_loader_green.gif\" alt=\"laden\" class=\"ico inline\" style=\"margin-bottom:-3px;\" />&nbsp;&nbsp;\n\t\t\t<span class=\"loading_green\">";
echo __("loading");
echo "</span>&nbsp;&nbsp;\n\t\t</span>\n\t\t<a class=\"button1 alt1 pointer\" onclick=\"\$('#loader_circle').show();\" id=\"form_create_btn\">\n            <span>";
echo __("btn edit");
echo "</span>\n        </a>\n\t</p>\n\t\n<!--form-->\n</fieldset></form>\n<!--form-->\n\n\n";
require_once "views/footer.php";

?>