<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "<!--right-->\n<div class=\"accounting_export\">\n\t<!--right-->\n\n\t";
echo $message_box;
echo "\n\n\t";
if(isset($office_name) && $office_name) {
    echo "<div class=\"float_right\" style=\"margin-top: 22px;\"><a href=\"" . url_generator("exportaccounting", false, ["module" => $package], ["oauth" => "settings"]) . "\">" . $package_information["name"] . " instellingen</a></div>";
    echo "<h1 class=\"heading_3 margin_bottom_40px\">" . htmlspecialchars($package_information["name"]) . ": " . htmlspecialchars($office_name) . "</h1>";
} else {
    echo "<h1 class=\"heading_3 margin_bottom_40px\">" . htmlspecialchars($package_information["name"]) . "</h1>";
}
echo "\n\n\t<div style=\"text-align:center\">\n\t\t<img src=\"";
echo __SITE_URL;
echo "/images/loadinfo.gif\" /><br />\n\t\t<br />\n\t\t<strong class=\"strong\">Een ogenblik geduld a.u.b.</strong><br />\n\t\tWe controleren welke onderdelen reeds bekend zijn in ";
echo htmlspecialchars($package_information["name"]);
echo ".<br />\n\t\tDit proces kan enkele minuten duren...\n\n\t</div>\n\n\t<script type=\"text/javascript\">\n\t\t\$(function(){\n\t\t\t\$.post('";
echo url_generator("exportaccounting", false, ["module" => $package], ["fillhistory" => "yes"]);
echo "', function(data){\n\t\t\t\tif(data == 'OK')\n\t\t\t\t{\n\t\t\t\t\twindow.location.replace('";
echo url_generator("exportaccounting", false, ["module" => $package]);
echo "');\n\t\t\t\t}\n\t\t\t});\n\t\t});\n\t</script>\n\t<!--right-->\n</div>\n<!--right-->";

?>