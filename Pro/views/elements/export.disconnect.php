<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "<h1 class=\"heading_3\">";
echo __("export accounting - disconnect");
echo "</h1>\n\nU staat op het punt de koppeling met ";
echo htmlspecialchars($package_information["name"]);
echo " te verbreken. U dient daarna opnieuw de koppeling te leggen met HostFact alvorens u weer kunt exporteren naar ";
echo htmlspecialchars($package_information["name"]);
echo ".\n\n<div class=\"button_bar clearfix\">\n\t<a href=\"";
echo url_generator("exportaccounting", false, ["module" => $package], ["oauth" => "remove"]);
echo "\" class=\"button red float_left\">";
echo __("export accounting - disconnect");
echo "</a>\n\t<a class=\"button grey float_right\" onclick=\"closeModal();\">";
echo __("cancel");
echo "</a>\n\n</div>";

?>