<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "<h1 class=\"heading_3\">";
echo htmlspecialchars($package_information["name"]);
echo " stoppen</h1>\n\n";
if(isset($message_box) && $message_box) {
    echo $message_box;
}
echo "\n\n<form name=\"form_delete\" method=\"post\" action=\"";
echo url_generator("exportaccounting", "exportaccounting_end", ["module" => $package]);
echo "\">\n\n\tU staat op het punt om het gebruik van de module voor het exporteren naar uw boekhoudpakket ";
echo htmlspecialchars($package_information["name"]);
echo " te stoppen.<br /><br />\n\tGraag horen we waarom u wilt stoppen met de ";
echo htmlspecialchars($package_information["name"]);
echo " module.<br />\n\t<textarea name=\"Reason\" rows=\"4\">";
if(isset($_POST["Reason"])) {
    echo htmlspecialchars($_POST["Reason"]);
}
echo "</textarea>\n\t<br /><br />\n\t<label class=\"radio\"><input type=\"checkbox\" name=\"imsure\" value=\"yes\"/> <span>\n\t\t";
if($export->getLicenseStatus() == "EXPIRED") {
    echo "beëindig mijn proefperiode op ";
    echo htmlspecialchars($package_information["name"]);
} else {
    echo "beëindig mijn abonnement op ";
    echo htmlspecialchars($package_information["name"]);
}
echo "</span></label><br />\n\n\t<div class=\"button_bar clearfix\">\n\t\t<input id=\"submit_button\" type=\"button\" value=\"Beëindigen\" data-loading=\"bezig met verwerken\" class=\"button red disabled float_left has_loading_button\">\n\t\t<input type=\"button\" value=\"Annuleren\" class=\"button grey float_right\" onclick=\"closeModal();\">\n\t</div>\n\n</form>\n</div>";

?>