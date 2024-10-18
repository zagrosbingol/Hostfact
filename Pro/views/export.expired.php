<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "<!--right-->\n<div class=\"right content accounting_export\">\n\t<!--right-->\n\n\t";
echo $message_box;
echo "\n\t<h1 class=\"heading_3 margin_bottom_40px\">Uw proefperiode voor ";
echo htmlspecialchars($package_information["name"]);
echo " is afgelopen</h1>\n\n\t<div class=\"width_50 float_left\">\n\n\t\t<div class=\"content_box form_box margin_bottom_40px margin_right_20px\">\n\n\t\t\t<form id=\"AccountingPackageForm\" name=\"form_order\" method=\"post\" action=\"";
echo url_generator("exportaccounting", "exportaccounting_order", ["module" => $package]);
echo "\">\n\n\n\t\t\t\t<h2 class=\"heading_4 margin_bottom_20px\">";
echo htmlspecialchars($package_information["name"]);
echo " bestellen</h2>\n\n\t\t\t\t<p>U heeft 14 dagen de boekhoudmodule ";
echo htmlspecialchars($package_information["name"]);
echo " kunnen uitproberen. Indien u gebruik wilt blijven maken van de boekhoudmodule, dan kunt u hieronder een bestelling plaatsen.</p>\n\n\t\t\t\t<p>Na uw bestelling kunt u direct de boekhoudmodule gebruiken.</p>\n\n\t\t\t\t<br />\n\n\t\t\t\t<label class=\"width_230px\">Kosten</label>\n\n\t\t\t\t\t<div class=\"input_column input_lineheight\">&euro; 5,- per maand &nbsp; <i>(excl. BTW)</i></div>\n\n\t\t\t\t\t<p>De module zal samen met uw HostFact abonnement gefactureerd worden. De periode tot dat moment wordt apart gefactureerd.</p>\n\n\t\t\t\t\t<br />\n\n\t\t\t\t\t<label><input name=\"OrderConfirmation\" type=\"checkbox\" value=\"yes\" /> <span>Ik ga akkoord met bovengenoemde kosten</span></label>\n\t\t\t\t<br />\n\n\t\t\t\t<div class=\"button_bar clearfix\">\n\t\t\t\t\t<input id=\"submit_button\" type=\"button\" onclick=\"\$('#AccountingPackageForm').submit();\" value=\"";
echo htmlspecialchars($package_information["name"]);
echo " bestellen\" class=\"button blue float_left\" />\n\t\t\t\t\t<a class=\"button grey float_right modal_link\" data-href=\"";
echo url_generator("exportaccounting", "exportaccounting_end", ["module" => $package]);
echo "\">niet bestellen</a>\n\n\t\t\t\t</div>\n\n\t\t\t</form>\n\n\t\t</div>\n\t</div>\n\n\t";
if(isset($show_stop_dialog) && $show_stop_dialog === true) {
    echo "\t\t<script type=\"text/javascript\">\n\t\t\t\$(function(){\n\t\t\t\topenModal('";
    echo url_generator("exportaccounting", "exportaccounting_end", ["module" => $package]);
    echo "');\n\t\t\t});\n\t\t</script>\n\t\t";
}
echo "\n\t<div class=\"clearfix\"></div>\n\t<!--right-->\n</div>\n<!--right-->";

?>