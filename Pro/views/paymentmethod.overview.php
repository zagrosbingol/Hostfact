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
echo $message;
echo "\n<!--heading1-->\n<div class=\"heading1\">\n<!--heading1-->\n\n\t<h2>";
echo __("paymentmethod overview");
echo "</h2>\n\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n<!--box1-->\n<div class=\"box2\" id=\"tabs\">\n<!--box1-->\n\n\t<!--top-->\n\t<div class=\"top\">\n\t<!--top-->\n\t\n\t\t<ul class=\"list3\">\n\t\t\t<li class=\"on\"><a href=\"#tab-paymentmethods\">";
echo __("paymentmethod overview");
echo "</a></li>\n\t\t\t<li class=\"on\"><a href=\"#tab-setting\">";
echo __("paymentmethod settings");
echo "</a></li>\n\t\t</ul>\n\n\t<!--top-->\n\t</div>\n\t<!--top-->\n\t\n\t<!--content-->\n\t<div class=\"content\" id=\"tab-paymentmethods\">\n\t<!--content-->\n\n\t\t";
if(U_PAYMENT_EDIT) {
    echo "\t\t<br /><p class=\"pos1\"><a class=\"button1 add_icon\" href=\"paymentmethods.php?action=add\"><span>";
    echo __("add paymentmethod");
    echo "</span></a></p>\n\t\t\n\t\t";
}
echo "\t\t<br />\n\t\t\n\t\t<form action=\"paymentmethods.php\" method=\"post\">\n\t\t\n\t\t<div id=\"paymentmethod_list\">\t\t\n\t\t";
foreach ($payment_methods as $key => $method) {
    $method_transaction_cost = $method["FEETYPE"] == "EUR" ? VAT_CALC_METHOD == "incl" ? money($method["FEEAMOUNT"] * (1 + STANDARD_TAX)) . " " . __("incl vat") : money($method["FEEAMOUNT"]) . (!empty($array_taxpercentages) ? " " . __("excl vat") : "") : ($method["FEETYPE"] == "PROC" ? $method["FEEAMOUNT"] . "%" : 0);
    echo "\t\t\t<div class=\"setting_box\" id=\"box_";
    echo $method["id"];
    echo "\">\n\t\t\t\t<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">\n\t\t\t\t<tr>\n\t\t\t\t\t<td width=\"30\" valign=\"middle\"><a class=\"a1 c1 ico inline ico_sortable\">&nbsp;</a></td>\n\t\t\t\t\t<td width=\"175\">";
    if($method["IMAGE"]) {
        echo "<label for=\"payment_method_";
        echo $key;
        echo "\"><img src=\"";
        echo IDEAL_EMAIL;
        echo "images/";
        echo $method["IMAGE"];
        echo "\" alt=\"\"/></label>";
    } else {
        echo "&nbsp;";
    }
    echo "</td>\n\t\t\t\t\t<td><label for=\"payment_method_";
    echo $key;
    echo "\"><strong>";
    echo $method["TITLE"];
    echo "</strong><br />\n\t\t\t\t\t";
    echo __("availability");
    echo ": ";
    echo $paymentmethod->AvailabilityArray[$method["AVAILABLE"]];
    echo "<br />\n\t\t\t\t\t";
    if(!isEmptyFloat($method["FEEAMOUNT"]) && ($method["AVAILABLE"] == 1 || $method["AVAILABLE"] == 3)) {
        echo "\t\t\t\t\t";
        if(0 < $method["FEEAMOUNT"]) {
            echo __("transaction costs for orderform") . ": " . $method_transaction_cost;
        } else {
            echo __("transaction discount for orderform") . ": " . str_replace("-", "", $method_transaction_cost);
        }
    }
    echo "</label></td>\n\t\t\t\t\t\n\t\t\t\t\t";
    if(U_PAYMENT_EDIT || U_PAYMENT_DELETE) {
        echo "\t\t\t\t\t<td width=\"100\" align=\"center\">\n\t\t\t\t\t\t";
        if(U_PAYMENT_EDIT) {
            echo "<p><a href=\"?page=payment&amp;action=edit&amp;id=";
            echo $method["id"];
            echo "\" class=\"button1 alt1\"><span>";
            echo __("edit");
            echo "</span></a></p>";
        }
        echo "\t\t\t\t\t\t";
        if(U_PAYMENT_DELETE) {
            echo "<p><a onclick=\"deletePaymentMethod('";
            echo $method["id"];
            echo "')\" class=\"a1 c1 pointer\">";
            echo __("delete");
            echo "</a></p>";
        }
        echo "\t\t\t\t\t</td>\n\t\t\t\t\t";
    }
    echo "\t\t\t\t</tr>\n\t\t\t\t</table>\n\t\t\t</div>\n\t\t";
}
echo "\t\t</div>\n\t\t\n\t\t</form>\n\t\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\t\n\t<!--content-->\n\t<div class=\"content\" id=\"tab-setting\">\n\t<!--content-->\n\t\n\t\t<!--form-->\n\t\t<form id=\"payment_settings_form\" name=\"form_create\" method=\"post\" action=\"paymentmethods.php?page=settings\"><fieldset><legend>";
echo __("paymentmethod settings");
echo "</legend>\n\t\t<!--form-->\n\n\t\t<!--split2-->\n\t\t<div class=\"split2\">\n\t\t<!--split2-->\n\t\t\n\t\t\t<!--left-->\n\t\t\t<div class=\"left\">\n\t\t\t<!--left-->\n\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
echo __("online payments via mail");
echo "</h3><div class=\"content\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t\t<strong class=\"title\">";
echo __("url for online payments");
echo "</strong>\n\t\t\t\t\t<input type=\"text\" class=\"text1 size12\" name=\"IDEAL_EMAIL\" value=\"";
echo IDEAL_EMAIL;
echo "\" ";
$ti = tabindex($ti);
echo "/> <span id=\"ideal-email-url-img\" class=\"loading_float\"></span>\n\t\t\t\t\t<br /><br />\n        \n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t\n\t\t\t<!--left-->\n\t\t\t</div>\n\t\t\t<!--left-->\n\n\t\t\t<!--right-->\n\t\t\t<div class=\"right\">\n\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t\t<div class=\"setting_help_box\">\n\t\t\t\t\t<strong>";
echo __("setting paymentmethod explained title");
echo "</strong><br />\n\t\t\t\t\t\t";
echo __("setting paymentmethod explained");
echo "\t\t\t\t</div>\n\t\n\t\t\t<!--right-->\n\t\t\t</div>\n\t\t\t<!--right-->\n\t\t\t\n\t\t<!--split2-->\n\t\t</div>\n\t\t<!--split2-->\n\t\t";
if(U_PAYMENT_EDIT) {
    echo "\t\t<p class=\"align_right\">\n\t\t\t<a class=\"button1 alt1\" id=\"form_create_btn\">\n                <span>";
    echo __("btn edit");
    echo "</span>\n            </a>\n\t\t</p>\n\t\t";
}
echo "\t\t\n\t\t<!--form-->\n\t\t</fieldset></form>\n\t\t<!--form-->\n\t\t\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\t\n<!--box1-->\n</div>\n<!--box1-->\n\n";
if(U_PAYMENT_DELETE) {
    echo "<div id=\"delete_paymentmethod\" class=\"hide\" title=\"";
    echo __("deletedialog paymentmethod title");
    echo "\">\n\t<form id=\"ProductForm\" name=\"form_delete\" method=\"post\" action=\"paymentmethods.php?page=delete\">\n\t<input type=\"hidden\" name=\"id\" value=\"\"/>\n\t";
    echo __("deletedialog paymentmethod description");
    echo "<br />\n\t<br />\n\t<input type=\"checkbox\" id=\"imsure\" name=\"imsure\" value=\"yes\"/> <label for=\"imsure\">";
    echo __("delete this paymentmethod");
    echo "</label><br />\n\t<br />\n\t<p><a id=\"delete_paymentmethod_btn\" class=\"button2 alt1 float_left\"><span>";
    echo __("delete");
    echo "</span></a></p>\n\t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#delete_paymentmethod').dialog('close');\"><span>";
    echo __("cancel");
    echo "</span></a></p>\n\t</form>\n</div>\n";
}
echo "\n";
require_once "views/footer.php";

?>