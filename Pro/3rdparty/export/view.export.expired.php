<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "views/header.php";
echo "\n\t";
echo $message;
echo "\n\t<!--heading1-->\n\t<div class=\"heading1\">\n\t<!--heading1-->\n\t\n\t\t<h2>Uw proefperiode voor ";
echo $export->getPackageName();
echo " is afgelopen</h2>\n\t\n\t<!--heading1-->\n\t</div><hr />\n\t<!--heading1-->\n\n\t<div class=\"split2\">\n        <div class=\"left\">\n        \n        \t<br />\n        \n            <div class=\"box3\"><h3>";
echo $export->getPackageName();
echo " module bestellen</h3><div class=\"content lineheight2\">\n            \n            \t<form id=\"AccountingPackageForm\" name=\"form_order\" method=\"post\" action=\"export.php?page=accounting_package&amp;action=order_module&amp;module=";
echo $package;
echo "\">\n\n            \n            \t<p>U heeft 14 dagen de boekhoudmodule ";
echo $export->getPackageName();
echo " kunnen uitproberen. Indien u gebruik wilt blijven maken van de boekhoudmodule, dan kunt u hieronder een bestelling plaatsen.</p>\n            \t\n            \t<p>Na uw bestelling kunt u direct de boekhoudmodule gebruiken.</p>\n            \t\n            \t<br />\n            \n\t\t\t\t<strong class=\"title2\">Kosten</strong>\n\t\t\t\t<span class=\"title2_value\">&euro; 5,- per maand &nbsp; <i>(excl. BTW)</i></span>\n\t\t\t\t<br />\n\n\t\t\t\t\t<p>De module zal samen met uw HostFact abonnement gefactureerd worden. De periode tot dat moment wordt apart gefactureerd.</p>\n\n\t\t\t\t\t<br />\n\t\t\t\t\n\t\t\t\t<label><input name=\"OrderConfirmation\" type=\"checkbox\" value=\"yes\" /> &nbsp; Ik ga akkoord met bovengenoemde kosten</label>\n\t\t\t\t\n\t\t\t\t<br />\n\t\t\t\t\n\t\t\t\t</form>\n                    \n\t\t\t</div></div>\n           \n           <!--buttonbar-->\n           <div class=\"buttonbar\">\n\t\t\t<!--buttonbar-->\n\t\t\t\n\t\t\t\t<p class=\"pos1\"><a class=\"button1 alt1\" onclick=\"\$('#AccountingPackageForm').submit();\"><span>";
echo $export->getPackageName();
echo " bestellen</span></a></p>\n\t\t\t\t<p class=\"pos2\"><a class=\"a1 c1\" style=\"line-height:30px;\" onclick=\"\$('#stop_accounting_package').dialog('open');\">niet bestellen</a> &nbsp;&nbsp;&nbsp;</p>\n\t\t\t\t\n\t\t\t<!--buttonbar-->\n\t\t\t</div>\n\t\t\t<!--buttonbar-->\n           \n        </div>\n        \n        <div class=\"right\">\n        ";
if(!empty($package_information["logo"])) {
    echo "        \t<div class=\"accountingpackage_info align_center\">\n        \t\t<img src=\"";
    echo $package_information["logo"];
    echo "\" alt=\"\" style=\"display:inline; max-height:175px; max-width:300px;\" />\n        \t</div>\n\t\t\t";
}
echo "        </div>\n    </div>\n\n\n<div id=\"stop_accounting_package\" class=\"hide\" title=\"Wilt u het gebruik van ";
echo $package_information["name"];
echo " beëindigen?\">\n\t\t<form id=\"DelAccountingPackageForm\" name=\"form_delete\" method=\"post\" action=\"export.php?page=accounting_package&amp;action=end_module&amp;module=";
echo $package_information["package"];
echo "\">\n\n\t\tU staat op het punt om het gebruik van de module voor het exporteren naar uw boekhoudpakket ";
echo $export->getPackageName();
echo "  te stoppen.<br /><br />\n\t\tGraag horen we waarom u wilt stoppen met de ";
echo $export->getPackageName();
echo " module.<br />\n\t\t<br />\n\t\t<textarea name=\"message\" class=\"text1\" style=\"width:250px;height:100px;\"></textarea><br />\n\t\t<br />\n\t\t<label><input type=\"checkbox\" name=\"imsure\" value=\"yes\"/>\n\t\t";
if($export->getLicenseStatus() == "EXPIRED") {
    echo "beëindig mijn proefperiode op ";
    echo $export->getPackageName();
} else {
    echo "\t\tbeëindig mijn abonnement op ";
    echo $export->getPackageName();
}
echo "</label><br />\n\t\t<br />\n\t\t<p><a id=\"stop_accounting_package_btn\" class=\"button2 alt1 float_left\"><span>Beëindigen</span></a></p>\n\t\t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#stop_accounting_package').dialog('close');\"><span>";
echo __("cancel");
echo "</span></a></p>\n\t\t</form>\n\t</div>\n\n\t<script type=\"text/javascript\">\n\t\$(document).ready(function() {\n\n\t    \$('#stop_accounting_package').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});\n\t\t\$('input[name=imsure]').click(function(){\n\t\t\tif(\$('input[name=imsure]:checked').val() != null)\n\t\t\t{\n\t\t\t\t\$('#stop_accounting_package_btn').removeClass('button2').addClass('button1');\n\t\t\t}\n\t\t\telse\n\t\t\t{\n\t\t\t\t\$('#stop_accounting_package_btn').removeClass('button1').addClass('button2');\n\t\t\t}\n\t\t});\n\t\t\$('#stop_accounting_package_btn').click(function(){\n\t\t\tif(\$('input[name=imsure]:checked').val() != null)\n\t\t\t{\n\t\t\t\t\$('#DelAccountingPackageForm').submit();\n\t\t\t}\t\n\t\t});\n\t});\n\t</script>\n\n";
require_once "views/footer.php";

?>