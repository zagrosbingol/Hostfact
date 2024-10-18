<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "<h1 class=\"heading_3\">";
echo sprintf(__("import accounting modal title"), __("export accounting export_type - " . $export_type));
echo " </h1>\n\n";
if(isset($message_box) && $message_box) {
    echo $message_box;
}
echo "\n<div style=\"text-align:center\">\n\t<img src=\"";
echo __SITE_URL;
echo "/images/loadinfo.gif\" /><br />\n\t<br />\n\t<strong class=\"strong\">Een ogenblik geduld a.u.b.</strong><br />\n\tWe halen de ";
echo strtolower(__("export accounting export_type - " . $export_type));
echo " op uit ";
echo htmlspecialchars($package_information["name"]);
echo ".<br />\n\tDit proces kan enkele minuten duren...\n\n</div>\n\n<script type=\"text/javascript\">\n\t\$(function(){\n\t\t\$.post('";
echo url_generator("exportaccounting", "exportaccounting_import", ["module" => $package], ["import" => $export_type, "start" => "yes"]);
echo "', function(data){\n\t\t\tif(data == 'OK')\n\t\t\t{\n\t\t\t\twindow.location.replace('";
echo url_generator("exportaccounting", false, ["module" => $package]);
echo "');\n\t\t\t}\n\t\t});\n\t});\n</script>";

?>