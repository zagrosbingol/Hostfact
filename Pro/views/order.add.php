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
echo "\n<!--form-->\n";
if($_GET["page"] == "add" || isset($_GET["action"]) && $_GET["action"] == "add") {
    echo "\t\t<form id=\"OrderForm\" name=\"form_create\" method=\"post\" action=\"orders.php?page=add\"><fieldset><legend>";
    echo __("create order");
    echo "</legend>\n";
} elseif($_GET["page"] == "edit" || isset($_GET["action"]) && $_GET["action"] == "edit") {
    echo "\t\t<form id=\"OrderForm\" name=\"form_create\" method=\"post\" action=\"orders.php?page=edit\"><fieldset><legend>";
    echo __("edit order");
    echo "</legend>\n\t\t<input type=\"hidden\" name=\"id\" value=\"";
    echo $order_id;
    echo "\" />\n";
}
echo "<!--form-->\n\n<!--heading1-->\n<div class=\"heading1\">\n<!--heading1-->\n\n\t<h2>\n\t";
if($_GET["page"] == "add") {
    echo __("create order");
} elseif($_GET["page"] == "edit") {
    echo __("edit order");
}
echo "\t</h2>\n\n\t<p class=\"pos2\"><strong class=\"textsize1 pointer\" id=\"Status_text\" style=\"line-height: 22px\">";
echo $array_orderstatus[$order->Status];
echo " ";
echo STATUS_CHANGE_ICON;
echo "</strong><select class=\"text1 size1 hide\" name=\"Status\">\n\t";
foreach ($array_orderstatus as $key => $value) {
    echo "<option value=\"";
    echo $key;
    echo "\" ";
    if($order->Status == $key) {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo $value;
    echo "</option>";
}
echo "\t</select></p>\n\n\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n<!--split4-->\n<div class=\"split4\" style=\"padding-right: 0px;\">\n<!--split4-->\n\n\t<!--box5-->\n\t<div class=\"box5\" style=\"border-right: 0px; padding-left: 0px; padding-right: 0px;\">\n\t<!--box5-->\n\t\t\t\n\t\t\t<div class=\"split3\" style=\"padding-left: 39px;padding-bottom: 15px;\">\n\t\t\t\t<strong class=\"title\">";
echo __("debtor");
echo "</strong>\n\t\t\t\t";
if($order->Type == "new" && empty($order->Customer)) {
    echo "\t\t\t\t\t<input type=\"hidden\" name=\"NewCustomer\" value=\"";
    echo $order->Debtor;
    echo "\" />\n\t\t\t\t\t<label><input type=\"radio\" name=\"CustomerType\" value=\"new\" checked=\"checked\" /> ";
    echo __("order add new debtor");
    echo "</label><br />\n\t\t\t\t\t<label><input type=\"radio\" name=\"CustomerType\" value=\"debtor\" /> ";
    echo __("order add existing debtor");
    echo "</label>\n\t\t\t\t\t\n\t\t\t\t\t<div id=\"debtor_select\" class=\"hide\">\n\t\t\t\t\t\t<br />\n\t\t\t\t\t\t<input type=\"hidden\" name=\"Debtor\" value=\"\" />\n\t\t\t\t\t\t";
    createAutoComplete("debtor", "Debtor", "", ["class" => "size12"]);
    echo "\t\t\t\t\t</div>\n\n\t\t\t\t";
} else {
    echo "\t\t\t\t\t<input type=\"hidden\" name=\"Debtor\" value=\"";
    echo $order->Debtor;
    echo "\" />\n\t\t\t\t\t";
    $selected_name = 0 < $order->Debtor ? $debtors[$order->Debtor]["DebtorCode"] . " " . ($debtors[$order->Debtor]["CompanyName"] ? $debtors[$order->Debtor]["CompanyName"] : $debtors[$order->Debtor]["SurName"] . ", " . $debtors[$order->Debtor]["Initials"]) : "";
    createAutoComplete("debtor", "Debtor", $selected_name, ["class" => "size12"]);
    echo "\t\t\t\t";
}
echo " \n\t\t\t</div>\n\t\t\t\n\t\t\t<!--split3-->\n\t\t\t<div class=\"split3\" style=\"margin-left: 30px; margin-right: 30px;padding-bottom: 30px;min-width: 860px;\">\n\t\t\t<!--split3-->\n\t\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t<div class=\"left\" style=\"width: 43%\">\n\t\t\t\t<!--left-->\n\t\t\t\t\t\n\t\t\t\t\t<!--back3-->\n\t\t\t\t\t<div style=\"padding-left: 10px;\">\n\t\t\t\t\t<!--back3-->\n\t\t\t\t\t\t\n\t\t\t\t\t<input type=\"hidden\" id=\"formJQ-Taxable\" name=\"Taxable\" value=\"";
echo $debtor->Taxable ? "true" : "false";
echo "\" />\n\t\t\t\t\t<input type=\"hidden\" name=\"TaxRate1\" value=\"";
echo isset($debtor->TaxRate1) && !is_null($debtor->TaxRate1) ? (double) $debtor->TaxRate1 : "";
echo "\" />\n\t\t\t\t\t<input type=\"hidden\" name=\"TaxRate2\" value=\"";
echo isset($debtor->TaxRate2) && !is_null($debtor->TaxRate2) ? (double) $debtor->TaxRate2 : "";
echo "\" />\n\t\t\t\t\t<input type=\"hidden\" name=\"OrderID\" value=\"";
echo $order_id;
echo "\" />\n\t\t\t\t\t<input type=\"hidden\" name=\"VatCalcMethod\" value=\"";
echo $order->VatCalcMethod == "incl" ? "incl" : "excl";
echo "\" />\n\t\t\t\t\t\n\t\t\t\t\t<div id=\"enveloppe\" class=\"";
if(empty($order->Debtor)) {
    echo "hide ";
}
echo "\" style=\"min-width: 440px;\">\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong>";
echo __("invoice address data");
echo "</strong>&nbsp;&nbsp;&nbsp;&nbsp;<span id=\"edit_enveloppe_data\" class=\"a1 c1 smallfont edit_label\">";
echo strtolower(__("edit invoice address data"));
echo "</span><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<div id=\"enveloppe_text\">\n\t\t\t\t\t\t\t<span class=\"title2_value\" id=\"formJQ-CompanyName\">";
echo $order->CompanyName;
echo "</span>\n\t\t\t\t\t\t\t<span class=\"title2_value\" id=\"formJQ-Name\">";
echo $order->Initials . " " . $order->SurName;
echo "</span>\n\t\t\t\t\t\t\t<span class=\"title2_value\" id=\"formJQ-Address\">";
echo $order->Address;
echo "</span>\n\t\t\t\t\t\t\t";
if(IS_INTERNATIONAL) {
    echo "<span class=\"title2_value ";
    if(!$order->Address2) {
        echo "hide";
    }
    echo "\" id=\"formJQ-Address2\">";
    echo $order->Address2;
    echo "</span>";
}
echo "\t\t\t\t\t\t\t<span class=\"title2_value\" id=\"formJQ-ZipCodeCity\">";
echo $order->ZipCode;
echo " ";
echo $order->City;
echo "</span>\n\t\t\t\t\t\t\t";
if(IS_INTERNATIONAL) {
    echo "<span class=\"title2_value ";
    if(!$order->StateName) {
        echo "hide";
    }
    echo "\" id=\"formJQ-State\">";
    echo $order->StateName;
    echo "</span>";
}
echo "\t\t\t\t\t\t\t<span class=\"title2_value\" id=\"formJQ-Country\">";
echo $array_country[$order->Country];
echo "</span>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<span class=\"title2_value\" id=\"formJQ-TaxNumber\">";
if($debtor->TaxNumber) {
    echo __("vat number");
    echo ": ";
    echo $debtor->TaxNumber;
}
echo "</span>\n\t\t\t\t\t\t\t<span class=\"title2_value\" id=\"formJQ-EmailAddress\"><span style=\"display:inline-block;\">";
echo str_replace(",", "&nbsp;&nbsp;</span><span style=\"display:inline-block;\">", check_email_address($order->EmailAddress, "convert", ","));
echo "</span></span>\n\t\t\t\t\t\t</div>\n\n\n\t\t\t\t\t\t<table id=\"enveloppe_input\" class=\"noborder hide\" cellpadding=\"0\" cellspacing=\"2\" style=\"margin-left:-1px\">\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td style=\"width:115px;\">";
echo __("companyname");
echo ":</td>\n\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t<input type=\"text\" id=\"inputJQ-CompanyName\" name=\"CompanyName\" class=\"text1\" style=\"width: 276px;\" value=\"";
echo $order->CompanyName;
echo "\" />\n\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td>";
echo __("contactperson");
echo ":</td>\n\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t<select class=\"text1 size6\" id=\"inputJQ-Sex\" name=\"Sex\">\n\t\t\t\t\t\t\t\t\t\t";
foreach ($array_sex as $key => $value) {
    echo "\t\t\t\t\t\t\t\t\t\t\t<option value=\"";
    echo $key;
    echo "\" ";
    if($order->Sex == $key) {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo $value;
    echo "</option>\n\t\t\t\t\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t\t\t\t</select> \n\t\t\t\t\t\t\t\t\t<input type=\"text\" id=\"inputJQ-Initials\" name=\"Initials\" class=\"text1 size2\" value=\"";
echo $order->Initials;
echo "\" /> \n\t\t\t\t\t\t\t\t\t<input type=\"text\" id=\"inputJQ-SurName\" name=\"SurName\" class=\"text1\" style=\"width: 129px;\" value=\"";
echo $order->SurName;
echo "\" />\n\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td>";
echo __("address");
echo ":</td>\n\t\t\t\t\t\t\t\t<td>\n\n\t\t\t\t\t\t\t\t\t<input type=\"text\" id=\"inputJQ-Address\" name=\"Address\" class=\"text1\" style=\"width: 276px;\" value=\"";
echo $order->Address;
echo "\" />\n\t\t\t\t\t\t\t\t\t";
if(IS_INTERNATIONAL) {
    echo "<input type=\"text\" id=\"inputJQ-Address2\" name=\"Address2\" class=\"text1 marginT2\" style=\"width: 276px;\" value=\"";
    echo $order->Address2;
    echo "\" />";
}
echo "\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td>";
echo __("zipcode and city");
echo ":</td>\n\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t<input type=\"text\" id=\"inputJQ-ZipCode\" name=\"ZipCode\" class=\"text1 size2\" value=\"";
echo $order->ZipCode;
echo "\" />\n\t\t\t\t\t\t\t\t\t<input type=\"text\" id=\"inputJQ-City\" name=\"City\" class=\"text1\" style=\"width: 210px;\" value=\"";
echo $order->City;
echo "\" />\n\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t";
if(IS_INTERNATIONAL) {
    echo "\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td>";
    echo __("state");
    echo ":</td>\n\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t<input type=\"text\" id=\"inputJQ-State\" class=\"text1 ";
    if(isset($array_states[$order->Country])) {
        echo "hide";
    }
    echo "\" style=\"width: 276px;\" name=\"State\" value=\"";
    echo $order->StateName;
    echo "\" maxlength=\"100\"/>\n\t\t\t\t\t\t\t\t\t<select id=\"inputJQ-StateCode\" class=\"text1 size4 ";
    if(!isset($array_states[$order->Country])) {
        echo "hide";
    }
    echo "\" style=\"width: 282px;\" name=\"StateCode\">\n\t\t\t\t\t\t\t\t\t\t<option value=\"\">";
    echo __("make your choice");
    echo "</option>\n\t\t\t\t\t\t\t\t\t\t";
    if(isset($array_states[$order->Country])) {
        foreach ($array_states[$order->Country] as $key => $value) {
            echo "\t\t\t\t\t\t\t\t\t\t<option value=\"";
            echo $key;
            echo "\" ";
            if($order->State == $key) {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo $value;
            echo "</option>\n\t\t\t\t\t\t\t\t\t";
        }
    }
    echo "\t\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td>";
echo __("country");
echo ":</td>\n\t\t\t\t\t\t\t\t<td>\n\n\t\t\t\t\t\t\t\t\t<select class=\"text1\" style=\"width:276px;\" id=\"inputJQ-Country\" name=\"Country\">\n\t\t\t\t\t\t\t\t\t";
foreach ($array_country as $key => $value) {
    echo "\t\t\t\t\t\t\t\t\t\t<option value=\"";
    echo $key;
    echo "\" ";
    if($order->Country == $key) {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo $value;
    echo "</option>\n\t\t\t\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td>";
echo __("emailaddress");
echo ":</td>\n\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t<input type=\"text\" id=\"inputJQ-EmailAddress\" name=\"EmailAddress\" class=\"text1\" style=\"width:276px;\" value=\"";
echo check_email_address($order->EmailAddress, "convert", ", ");
echo "\" />\n\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t</table>\n\t\t\t\t\t</div>\n\t\t\t\n\t\t\t\t<!--back3-->\n\t\t\t\t</div>\n\t\t\t\t<!--back3-->\n\t\t\n\t\t\t<!--left-->\n\t\t\t</div>\n\t\t\t<!--left-->\n\t\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t<div class=\"right\" style=\"width: 503px;\" id=\"extra_invoicedata_resize\">\n\t\t\t\t<!--right-->\n\t\t\t\t\t<!--back3-->\n\t\t\t\t\t<div id=\"extra_invoicedata\">\n\t\t\t\t\t<!--back3-->\n\t\t\t\t\t\t<strong style=\"display: inline-table;width: 125px;\">";
echo __("order feature data");
echo "</strong>\n\t\t\t\t\t\t<span id=\"edit_extra_enveloppe_data\" class=\"a1 c1 smallfont edit_label \">";
echo strtolower(__("edit order code and date"));
echo "</span><br />\n\t\t\t\t\t\t<div id=\"extra_invoicedata_input_text\">\n\t\t\t\t\t\t\t<span class=\"title2_value\"><div style=\"display: inline-table;width: 125px;\">";
echo __("debtor no");
echo ":</div>\n\t\t\t\t\t\t\t\t<span id=\"formJQ-DebtorCode\">\n\t\t\t\t\t\t\t\t\t";
echo $debtor->DebtorCode ? $debtor->DebtorCode : "<i>&lt;&lt; " . strtolower(__("debtor no")) . " &gt;&gt;</i>";
echo "\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t<span class=\"title2_value\"><div style=\"display: inline-table;width: 125px;\">";
echo __("order no");
echo ":</div>\n\t\t\t\t\t\t\t\t<span id=\"OrderCode_text\">";
echo $order->OrderCode;
echo "</span>\n\t\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size7 hide\" id=\"OrderCode\" name=\"OrderCode\" value=\"";
echo $order->OrderCode;
echo "\"/>\n\t\t\t\t\t\t\t\t<input type=\"hidden\" name=\"NewNumber\" value=\"";
echo isset($order->NewNumber) ? $order->NewNumber : "";
echo "\" />\n\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t<span class=\"title2_value\"><div style=\"display: inline-table;width: 125px;\">";
echo __("date");
echo ":</div>\n\t\t\t\t\t\t\t\t<span id=\"formJQ-DebtorCode\">";
echo $order->ShowDate;
echo "</span>\n\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t<!--back3-->\n\t\t\t\t\t</div>\n\t\t\t\t\t<!--back3-->\n\t\n\t\t\t<!--right-->\n\t\t\t</div>\n\t\t\t<!--right-->\n\t\t\t\n\t\t<!--split3-->\n\t\t</div>\n\t\t<!--split3-->\n\t\n\t\t";
require_once "views/elements/invoice.add.php";
$options = ["object_id" => $order_id, "form_type" => "order", "periodic_dates" => false];
show_invoice_add($order, $debtor, $options);
echo "\t\t\n\t<!--box5-->\n\t</div>\n\t<!--box5-->\n\n\t\n<!--split4-->\n</div>\n<!--split4-->\n\n<br />\t\t\t\t\t\t\n<p class=\"align_right\">\n    <a class=\"button1 alt1\" id=\"form_create_btn\">\n        <span>";
echo $_GET["page"] == "add" ? __("btn add") : __("btn edit");
echo "</span>\n    </a>\n</p>\n\n\n\n<!--box1-->\n<div class=\"box2\" id=\"tabs\">\n<!--box1-->\n\n\t<!--top-->\n\t<div class=\"top\">\n\t<!--top-->\n\t\n\t\t<ul class=\"list3\">\n\t\t\t<li class=\"on\"><a href=\"#tab-extra\">";
echo __("invoice options");
echo "</a></li>\n\t\t\t";
if($order->Type == "new") {
    echo "\t\t\t<li><a href=\"#tab-newdebtor\">";
    echo __("new debtor");
    echo "</a></li>\n\t\t\t";
}
echo "\t\t\t<li><a href=\"#tab-note\">";
echo __("remark");
echo "</a></li>\n\t\t</ul>\n\t\n\t<!--top-->\n\t</div>\n\t<!--top-->\n\n\t<!--content-->\n\t<div class=\"content\" id=\"tab-note\">\n\t<!--content-->\n\n\t\t<!--box3-->\n\t\t<div class=\"box3\"><h3>";
echo __("remark");
echo "</h3><div class=\"content\">\n\t\t<!--box3-->\n\t\t\n\t\t\t<textarea class=\"text1 size5\" name=\"Comment\">";
echo $order->Comment;
echo "</textarea>\n\n\t\t<!--box3-->\n\t\t</div></div>\n\t\t<!--box3-->\n\t\t\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\n\t<!--content-->\n\t<div class=\"content\" id=\"tab-extra\">\n\t<!--content-->\n\n\t\t<!--split2-->\n\t\t<div class=\"split2\">\n\t\t<!--split2-->\n\t\t\n\t\t\t<!--left-->\n\t\t\t<div class=\"left\">\n\t\t\t<!--left-->\n\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
echo __("invoice options send");
echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title\">";
echo __("invoice template");
echo "</strong>\n\t\t\t\t\t<select id=\"formJQ-Template\" name=\"Template\" class=\"text1 size1\">\n\t\t\t\t\t\t";
foreach ($templates as $key => $value) {
    if(is_numeric($key) && is_numeric($value["id"])) {
        echo "\t\t\t\t\t\t<option value=\"";
        echo $value["id"];
        echo "\" ";
        if($value["id"] == $order->Template) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo $value["Name"];
        echo "</option>\n\t\t\t\t\t\t";
    }
}
echo "\t\t\t\t\t</select>\n\t\t\t\t\t<br /><br />\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title\">";
echo __("invoice options invoicemethod");
echo "</strong>\n\t\t\t\t\t<select id=\"formJQ-InvoiceMethod\" name=\"InvoiceMethod\" class=\"text1 size1\">\n\t\t\t\t\t\t";
foreach ($array_invoicemethod as $key => $value) {
    echo "\t\t\t\t\t\t<option value=\"";
    echo $key;
    echo "\" ";
    if($key == $order->InvoiceMethod) {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo $value;
    echo "</option>\n\t\t\t\t\t\t";
}
echo "\t\t\t\t\t</select>\n\t\t\t\t\t<br /><br />\n\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t\n\t\t\t\n\t\t\t<!--left-->\n\t\t\t</div>\n\t\t\t<!--left-->\n\t\t\t\n\t\t\t<!--right-->\n\t\t\t<div class=\"right\">\n\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
echo __("invoice options amounts");
echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t\t<div id=\"vatcalcmethod_helper_div\" ";
if(isset($debtor->TaxRate1) && !is_null($debtor->TaxRate1)) {
    echo "class=\"hide\"";
}
echo ">\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("invoice options which vat calc method to use");
echo "</strong>\n\t\t\t\t\t\t<select name=\"VatCalcMethodHelper\" class=\"text1 size1\">\n\t\t\t\t\t\t\t<option value=\"excl\" ";
if($order->VatCalcMethod == "excl") {
    echo "selected=\"selected\"";
}
echo ">";
echo __("excluding vat");
echo "</option>\n\t\t\t\t\t\t\t<option value=\"incl\" ";
if($order->VatCalcMethod == "incl") {
    echo "selected=\"selected\"";
}
echo ">";
echo __("including vat");
echo "</option>\n\t\t\t\t\t\t</select>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t</div>\t\t\t\t\t\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title\">";
echo __("ignore discount");
echo "</strong>\n\t\t\t\t\t<select id=\"formJQ-IgnoreDiscount\" name=\"IgnoreDiscount\" class=\"text1 size1\">\n\t\t\t\t\t\t<option value=\"0\" ";
if($order->IgnoreDiscount == "0") {
    echo "selected=\"selected\"";
}
echo ">";
echo __("no");
echo "</option>\n\t\t\t\t\t\t<option value=\"1\" ";
if($order->IgnoreDiscount == "1") {
    echo "selected=\"selected\"";
}
echo ">";
echo __("yes");
echo "</option>\n\t\t\t\t\t</select>\n\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
echo __("invoice options payment");
echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title\">";
echo __("invoice options term");
echo "</strong>\n\t\t\t\t\t<input type=\"text\" name=\"Term\" value=\"";
echo $order->Term;
echo "\" class=\"text1 size3\" /> ";
echo __("days");
echo "\t\t\t\t\t<br /><br />\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title\">";
echo __("invoice options authorisation");
echo "</strong>\n\t\t\t\t\t<select id=\"formJQ-Authorisation\" name=\"Authorisation\" class=\"text1 size1\">\n\t\t\t\t\t\t<option value=\"no\" ";
if($order->Authorisation == "no") {
    echo "selected=\"selected\"";
}
echo ">";
echo __("no");
echo "</option>\n\t\t\t\t\t\t<option value=\"yes\" ";
if($order->Authorisation == "yes") {
    echo "selected=\"selected\"";
}
echo ">";
echo __("yes");
echo "</option>\n\t\t\t\t\t</select>\n\t\t\t\t\t<br /><br />\n\n\t\t\t\t\t<strong class=\"title\">";
echo __("order is paid");
echo "</strong>\n\t\t\t\t\t<select name=\"Paid\" class=\"text1 size1\">\n\t\t\t\t\t\t<option value=\"0\" ";
if($order->Paid == "0") {
    echo "selected=\"selected\"";
}
echo ">";
echo __("no");
echo "</option>\n\t\t\t\t\t\t<option value=\"1\" ";
if($order->Paid == "1") {
    echo "selected=\"selected\"";
}
echo ">";
echo __("yes");
echo "</option>\n\t\t\t\t\t</select>\n\t\t\t\t\t<br /><br />\n\t\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\n\t\t\t<!--right-->\n\t\t\t</div>\n\t\t\t<!--right-->\n\t\t\t\n\t\t<!--split2-->\n\t\t</div>\n\t\t<!--split2-->\n\t\t\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\t\n\t";
if($order->Type == "new") {
    echo "\t\n\t<!--content-->\n\t<div class=\"content\" id=\"tab-newdebtor\">\n\t<!--content-->\n\t\t\n\t\t<!--split2-->\n\t\t<div class=\"split2\">\n\t\t<!--split2-->\n\t\t\n\t\t\t<!--left-->\n\t\t\t<div class=\"left\">\n\t\t\t<!--left-->\n\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
    echo __("debtor data");
    echo "</h3><div class=\"content\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title\">";
    echo __("companyname");
    echo "</strong>\n\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"CustomerCompanyName\" value=\"";
    echo $customer->CompanyName;
    echo "\" maxlength=\"100\" ";
    $ti = tabindex($ti);
    echo " />\n\t\t\t\t\t<br />\n\t\t\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t<div id=\"CompanyName_extra\" ";
    if(!$customer->CompanyName) {
        echo "class=\"hide\"";
    }
    echo ">\n\t\t\t\t\t\t<br />\n\t\t\t\t\t\t<strong class=\"title\">";
    echo __("company number");
    echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"CustomerCompanyNumber\" value=\"";
    echo $customer->CompanyNumber;
    echo "\" ";
    $ti = tabindex($ti);
    echo " maxlength=\"20\"/>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
    echo __("vat number");
    echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"CustomerTaxNumber\" value=\"";
    echo $customer->TaxNumber;
    echo "\" ";
    $ti = tabindex($ti);
    echo " maxlength=\"20\"/>\n\t\t\t\t\t\t<span id=\"vat_status\"></span>\t\t\t\t\t\t\t\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n                        ";
    if(!empty($array_legaltype)) {
        echo "\t\t\t\t\t\t<strong class=\"title\">";
        echo __("legal form");
        echo "</strong>\n\t\t\t\t\t\t<select name=\"CustomerLegalForm\" class=\"text1 size14_percentage\">\n\t\t\t\t\t\t";
        foreach ($array_legaltype as $key => $value) {
            echo "\t\t\t\t\t\t\t<option value=\"";
            echo $key;
            echo "\" ";
            if($customer->LegalForm == $key) {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo $value;
            echo "</option>\n\t\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\t</select>\n\t\n\t\t\t\t\t\t<br />\n                        ";
    }
    echo "\t\t\t\t\t</div>\n\t\t\t\t\t\n\t\t\t\t\t<br />\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title\">";
    echo __("contact person");
    echo "</strong>\n\t\t\t\t\t\t<div class=\"inputgroup_size14_percentage\">\n\t\t\t\t\t\t\t<select name=\"CustomerSex\" class=\"text1 size16\" ";
    $ti = tabindex($ti);
    echo ">\n\t\t\t\t\t\t";
    foreach ($array_sex as $k => $v) {
        echo "\t\t\t\t\t\t\t<option value=\"";
        echo $k;
        echo "\" ";
        if($customer->Sex == $k) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo $v;
        echo "</option>\n\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t</select>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size16\" name=\"CustomerInitials\" value=\"";
    echo $customer->Initials;
    echo "\" ";
    $ti = tabindex($ti);
    echo " maxlength=\"25\"/>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size15_calc\" name=\"CustomerSurName\" value=\"";
    echo $customer->SurName;
    echo "\" ";
    $ti = tabindex($ti);
    echo " maxlength=\"100\"/>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t</div>\n\n\t\t\t\t\t<strong class=\"title\">";
    echo __("address");
    echo "</strong>\n\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"CustomerAddress\" value=\"";
    echo $customer->Address;
    echo "\" ";
    $ti = tabindex($ti);
    echo " maxlength=\"100\"/>\n\t\t\t\t\t";
    if(IS_INTERNATIONAL) {
        echo "<br /><input type=\"text\" class=\"text1 size1 marginT2\" name=\"CustomerAddress2\" value=\"";
        echo $customer->Address2;
        echo "\" ";
        $ti = tabindex($ti);
        echo " maxlength=\"100\"/>";
    }
    echo "\t\t\t\t\t\n\t\t\t\t\t<br /><br />\n\n\t\t\t\t\t<div class=\"inputgroup_size14_percentage\">\n\t\t\t\t\t\t<strong class=\"title\">";
    echo __("zipcode and city");
    echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size16\" name=\"CustomerZipCode\" value=\"";
    echo $customer->ZipCode;
    echo "\" ";
    $ti = tabindex($ti);
    echo " maxlength=\"10\"/> <input type=\"text\" class=\"text1 size_calc_100-size16\" name=\"CustomerCity\" value=\"";
    echo $customer->City;
    echo "\" ";
    $ti = tabindex($ti);
    echo " maxlength=\"100\"/>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t</div>\n\n\t\t\t\t\t";
    if(IS_INTERNATIONAL) {
        echo "\t\t\t\t\t\t<strong class=\"title\">";
        echo __("state");
        echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage ";
        if(isset($array_states[$customer->Country])) {
            echo "hide";
        }
        echo "\" name=\"CustomerState\" value=\"";
        if(!isset($array_states[$customer->Country])) {
            echo $customer->StateName;
        }
        echo "\" ";
        $ti = tabindex($ti);
        echo " maxlength=\"100\"/>\n\t\t\t\t\t\t<select class=\"text1 size14_percentage ";
        if(!isset($array_states[$customer->Country])) {
            echo "hide";
        }
        echo "\" name=\"CustomerStateCode\" ";
        $ti = tabindex($ti);
        echo ">\n\t\t\t\t\t\t\t<option value=\"\">";
        echo __("make your choice");
        echo "</option>\n\t\t\t\t\t\t";
        if(isset($array_states[$customer->Country])) {
            foreach ($array_states[$customer->Country] as $key => $value) {
                echo "\t\t\t\t\t\t\t<option value=\"";
                echo $key;
                echo "\" ";
                if($customer->State == $key) {
                    echo "selected=\"selected\"";
                }
                echo ">";
                echo $value;
                echo "</option>\n\t\t\t\t\t\t";
            }
        }
        echo "\t\t\t\t\t\t</select>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t";
    }
    echo "\t\n\t\t\t\t\t<strong class=\"title\">";
    echo __("country");
    echo "</strong>\n\t\t\t\t\t<select class=\"text1 size14_percentage\" name=\"CustomerCountry\" ";
    $ti = tabindex($ti);
    echo ">\n\t\t\t\t\t";
    foreach ($array_country as $key => $value) {
        echo "\t\t\t\t\t\t<option value=\"";
        echo $key;
        echo "\" ";
        if($customer->Country == $key) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo $value;
        echo "</option>\n\t\t\t\t\t";
    }
    echo "\t\t\t\t\t</select>\n\t\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
    echo __("contact data");
    echo "</h3><div class=\"content\">\n\t\t\t\t<!--box3-->\n\t\n\t\t\t\t\t<strong class=\"title\">";
    echo __("emailaddress");
    echo "</strong>\n\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"CustomerEmailAddress\" value=\"";
    echo check_email_address($customer->EmailAddress, "convert", ", ");
    echo "\" ";
    $ti = tabindex($ti);
    echo "/>\n\t\n\t\t\t\t\t<br /><br />\n\t\n\t\t\t\t\t<strong class=\"title\">";
    echo __("phonenumber");
    echo "</strong>\n\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"CustomerPhoneNumber\" value=\"";
    echo $customer->PhoneNumber;
    echo "\" ";
    $ti = tabindex($ti);
    echo " maxlength=\"25\"/>\n\t\t\t\t\t<br /><br />\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title\">";
    echo __("mobilenumber");
    echo "</strong>\n\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"CustomerMobileNumber\" value=\"";
    echo $customer->MobileNumber;
    echo "\" ";
    $ti = tabindex($ti);
    echo " maxlength=\"25\"/>\n\t\t\t\t\t<br /><br />\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title\">";
    echo __("faxnumber");
    echo "</strong>\n\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"CustomerFaxNumber\" value=\"";
    echo $customer->FaxNumber;
    echo "\" ";
    $ti = tabindex($ti);
    echo " maxlength=\"25\"/>\n\t\t\t\t\t<br /><br />\n\t\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t<br />\n\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
    echo __("customerpanel");
    echo "</h3><div class=\"content\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\t<input id=\"CustomerPanelAccess\" name=\"CustomerPanelAccess\" type=\"checkbox\" class=\"checkbox1\" value=\"on\" ";
    if($customer->Username) {
        echo "checked=\"checked\"";
    }
    echo " /> <label for=\"CustomerPanelAccess\"><strong>";
    echo __("access to customerpanel");
    echo "</strong></label>\n\t\t\t\t\t<br clear=\"both\"/>\n\t\t\t\t\t\n\t\t\t\t\t<div id=\"CustomerPanelAccess_extra\" ";
    if(!$customer->Username) {
        echo "class=\"hide\"";
    }
    echo ">\n\t\t\t\t\t\t<br />\t\t\t\n\t\t\t\t\t\t<div class=\"add alt1\">\n\t\t\t\t\t\t\t<strong class=\"title\">\n\t\t\t\t\t\t\t\t";
    echo __("username");
    echo "\t\t\t\t\t\t\t\t<span class=\"infopopupright\" style=\"margin-top:-3px;\">\n\t\t\t\t\t\t\t\t\t<em>";
    echo __("more info");
    echo "</em>\n\t\t\t\t\t\t\t\t\t<span class=\"popup\">\n\t\t\t\t\t\t\t\t\t\t";
    echo __("username debtorcode info");
    echo "\t\t\t\t\t\t\t\t\t\t<b></b>\n\t\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t</div><br>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"CustomerUsername\" value=\"";
    echo $customer->Username;
    echo "\" ";
    $ti = tabindex($ti);
    echo "/>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
    echo __("password");
    echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"CustomerPassword\" value=\"";
    echo passcrypt($customer->Password);
    echo "\" ";
    $ti = tabindex($ti);
    echo "/><br /><br />\n\n\t\t\t\t\t\t<strong class=\"title\">";
    echo __("language preference");
    echo "</strong>\n\t\t\t\t\t\t<select name=\"CustomerDefaultLanguage\" class=\"text1 size14_percentage\">\n\t\t\t\t\t\t\t<option value=\"\" ";
    if($customer->DefaultLanguage == "") {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo __("standard");
    echo "</option>\n\t\t\t\t\t\t\t";
    foreach ($array_customer_languages as $key => $value) {
        echo "\t\t\t\t\t\t\t\t<option value=\"";
        echo $key;
        echo "\" ";
        if($customer->DefaultLanguage == $key) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo $value;
        echo "</option>\n\t\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t</select>\n\t\t\t\t\t</div>\n\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t<!--left-->\n\t\t\t</div>\n\t\t\t<!--left-->\n\t\t\t\n\t\t\t<!--right-->\n\t\t\t<div class=\"right\">\n\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
    echo __("invoice information");
    echo "</h3><div class=\"content\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t\t<input id=\"AbnormalInvoiceData\" name=\"AbnormalInvoiceData\" type=\"checkbox\" class=\"checkbox1\" value=\"on\" ";
    if($customer->InvoiceInitials || $customer->InvoiceSurName || $customer->InvoiceAddress || $customer->InvoiceAddress2 || $customer->InvoiceZipCode || $customer->InvoiceCity || $customer->InvoiceCountry && $customer->InvoiceCountry != $customer->Country || $customer->InvoiceEmailAddress) {
        echo "checked=\"checked\"";
    }
    echo "/> <label for=\"AbnormalInvoiceData\"><strong>";
    echo __("abnormal invoice information");
    echo "</strong></label>\n<a id=\"AbnormalInvoiceData_copylink\" onclick=\"copyOrderInvoiceData();\" class=\"c1 pointer floatr ";
    if(!($customer->InvoiceInitials || $customer->InvoiceSurName || $customer->InvoiceAddress || $customer->InvoiceAddress2 || $customer->InvoiceZipCode || $customer->InvoiceCity || $customer->InvoiceCountry && $customer->InvoiceCountry != $customer->Country || $customer->InvoiceEmailAddress)) {
        echo "hide";
    }
    echo "\">";
    echo __("copy from general data");
    echo "</a>\n\t\t\t\t\t<br clear=\"both\"/>\n\t\t\t\t\t\t\n\t\t\t\t\t<div id=\"AbnormalInvoiceData_extra\" ";
    if(!($customer->InvoiceInitials || $customer->InvoiceSurName || $customer->InvoiceAddress || $customer->InvoiceAddress2 || $customer->InvoiceZipCode || $customer->InvoiceCity || $customer->InvoiceCountry && $customer->InvoiceCountry != $customer->Country || $customer->InvoiceEmailAddress)) {
        echo "class=\"hide\"";
    }
    echo ">\n\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
    echo __("companyname");
    echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"CustomerInvoiceCompanyName\" value=\"";
    echo $customer->InvoiceCompanyName;
    echo "\" ";
    $ti = tabindex($ti);
    echo " maxlength=\"100\"/>\n\t\t\t\t\t\t<br /><br />\n\n\t\t\t\t\t\t<div class=\"inputgroup_size14_percentage\">\n\t\t\t\t\t\t\t<strong class=\"title\">";
    echo __("contact person");
    echo "</strong>\n                        \t<select class=\"text1 size16\" name=\"CustomerInvoiceSex\">\n\t\t\t\t\t\t\t";
    foreach ($array_sex as $key => $value) {
        echo "\t\t\t\t\t\t\t\t<option value=\"";
        echo $key;
        echo "\" ";
        if($customer->InvoiceSex == $key) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo $value;
        echo "</option>\n\t\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size16\" name=\"CustomerInvoiceInitials\" value=\"";
    echo $customer->InvoiceInitials;
    echo "\" ";
    $ti = tabindex($ti);
    echo " maxlength=\"25\"/> <input type=\"text\" class=\"text1 size15_calc\" name=\"CustomerInvoiceSurName\" value=\"";
    echo $customer->InvoiceSurName;
    echo "\" ";
    $ti = tabindex($ti);
    echo " maxlength=\"100\"/>\n\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t</div>\n\n\t\t\t\t\t\t<strong class=\"title\">";
    echo __("address");
    echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"CustomerInvoiceAddress\" value=\"";
    echo $customer->InvoiceAddress;
    echo "\" ";
    $ti = tabindex($ti);
    echo " maxlength=\"100\"/>\n\t\t\t\t\t\t";
    if(IS_INTERNATIONAL) {
        echo "<br /><input type=\"text\" class=\"text1 size14_percentage marginT2\" name=\"CustomerInvoiceAddress2\" value=\"";
        echo $customer->InvoiceAddress2;
        echo "\" ";
        $ti = tabindex($ti);
        echo " maxlength=\"100\"/>";
    }
    echo "\t\t\t\t\t\t<br /><br />\n\n\t\t\t\t\t\t<div class=\"inputgroup_size14_percentage\">\n\t\t\t\t\t\t\t<strong class=\"title\">";
    echo __("zipcode and city");
    echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size16\" name=\"CustomerInvoiceZipCode\" value=\"";
    echo $customer->InvoiceZipCode;
    echo "\" ";
    $ti = tabindex($ti);
    echo " maxlength=\"10\"/> <input type=\"text\" class=\"text1 size_calc_100-size16\" name=\"CustomerInvoiceCity\" value=\"";
    echo $customer->InvoiceCity;
    echo "\" ";
    $ti = tabindex($ti);
    echo " maxlength=\"100\"/>\n\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t</div>\n\n\t\t\t\t\t\t";
    if(IS_INTERNATIONAL) {
        echo "\t\t\t\t\t\t\t<strong class=\"title\">";
        echo __("state");
        echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage ";
        if(isset($array_states[$customer->InvoiceCountry])) {
            echo "hide";
        }
        echo "\" name=\"CustomerInvoiceState\" value=\"";
        echo $customer->InvoiceStateName;
        echo "\" ";
        $ti = tabindex($ti);
        echo " maxlength=\"100\"/>\n\t\t\t\t\t\t\t<select class=\"text1 size14_percentage ";
        if(!isset($array_states[$customer->InvoiceCountry])) {
            echo "hide";
        }
        echo "\" name=\"CustomerInvoiceStateCode\" ";
        $ti = tabindex($ti);
        echo ">\n\t\t\t\t\t\t\t\t<option value=\"\">";
        echo __("make your choice");
        echo "</option>\n\t\t\t\t\t\t\t";
        if(isset($array_states[$customer->InvoiceCountry])) {
            foreach ($array_states[$customer->InvoiceCountry] as $key => $value) {
                echo "\t\t\t\t\t\t\t\t<option value=\"";
                echo $key;
                echo "\" ";
                if($customer->InvoiceState == $key) {
                    echo "selected=\"selected\"";
                }
                echo ">";
                echo $value;
                echo "</option>\n\t\t\t\t\t\t\t";
            }
        }
        echo "\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
    echo __("country");
    echo "</strong>\n\t\t\t\t\t\t<select class=\"text1 size14_percentage\" name=\"CustomerInvoiceCountry\" ";
    $ti = tabindex($ti);
    echo ">\n\t\t\t\t\t\t";
    $customer->InvoiceCountry = $customer->InvoiceCountry && $customer->InvoiceAddress ? $customer->InvoiceCountry : $customer->Country;
    foreach ($array_country as $key => $value) {
        echo "\t\t\t\t\t\t\t<option value=\"";
        echo $key;
        echo "\" ";
        if($customer->InvoiceCountry == $key) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo $value;
        echo "</option>\n\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t</select>\t\t\t\t\t\t\t\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
    echo __("emailaddress");
    echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"CustomerInvoiceEmailAddress\" value=\"";
    echo check_email_address($customer->InvoiceEmailAddress, "convert", ", ");
    echo "\" ";
    $ti = tabindex($ti);
    echo "/>\n\t\t\t\t\t</div>\n\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
    echo __("bankaccount data");
    echo "</h3><div class=\"content\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title\">";
    echo __("authorization");
    echo "</strong>\n\t\t\t\t\t<select name=\"CustomerInvoiceAuthorisation\" class=\"text1 size16\">\n\t\t\t\t\t\t";
    foreach ($array_authorisation as $k => $v) {
        echo "\t\t\t\t\t\t\t<option value=\"";
        echo $k;
        echo "\" ";
        if($customer->InvoiceAuthorisation == $k) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo $v;
        echo "</option>\n\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t</select>\n\t\t\t\t\t<br /><br />\n\t\t\t\t\n\t\t\t\t\t<strong class=\"title\">";
    echo __("account number");
    echo "</strong>\n\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"CustomerAccountNumber\" value=\"";
    echo $customer->AccountNumber;
    echo "\" ";
    $ti = tabindex($ti);
    echo "/>\n\t\t\t\t\t<br /><br />\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title\">";
    echo __("account name");
    echo "</strong>\n\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"CustomerAccountName\" value=\"";
    echo $customer->AccountName;
    echo "\" ";
    $ti = tabindex($ti);
    echo "/>\n\t\t\t\t\t<br /><br />\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title\">";
    echo __("bank");
    echo "</strong>\n\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"CustomerAccountBank\" value=\"";
    echo $customer->AccountBank;
    echo "\" ";
    $ti = tabindex($ti);
    echo "/>\n\t\t\t\t\t<br /><br />\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title\">";
    echo __("bank city");
    echo "</strong>\n\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"CustomerAccountCity\" value=\"";
    echo $customer->AccountCity;
    echo "\" ";
    $ti = tabindex($ti);
    echo "/>\n\t\t\t\t\t<br /><br />\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title\">";
    echo __("bic");
    echo "</strong>\n\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"CustomerAccountBIC\" value=\"";
    echo $customer->AccountBIC;
    echo "\" ";
    $ti = tabindex($ti);
    echo "/>\n\t\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t<br />\n\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
    echo __("invoicing");
    echo "</h3><div class=\"content\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t\t<strong class=\"title\">";
    echo __("invoice method");
    echo "</strong>\n\t\t\t\t\t<select name=\"CustomerInvoiceMethod\" class=\"text1 size14_percentage\" ";
    $ti = tabindex($ti);
    echo ">\n\t\t\t\t\t";
    foreach ($array_invoicemethod as $k => $v) {
        echo "\t\t\t\t\t<option value=\"";
        echo $k;
        echo "\" ";
        if($customer->InvoiceMethod == $k) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo $v;
        echo "</option>\n\t\t\t\t\t";
    }
    echo "\t\t\t\t\t</select>\n\t\t\t\t\t<br /><br />\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title\">";
    echo __("pricequote template");
    echo "</strong>\n\t\t\t\t\t<select name=\"CustomerPriceQuoteTemplate\" class=\"text1 size14_percentage\" ";
    $ti = tabindex($ti);
    echo ">\n\t\t\t\t\t<option value=\"\">";
    echo __("standard template");
    echo "</option>\n\t\t\t\t\t";
    foreach ($pricequotetemplates as $k => $v) {
        if(is_numeric($k)) {
            echo "\t\t\t\t\t<option value=\"";
            echo $k;
            echo "\" ";
            if($customer->PriceQuoteTemplate == $k) {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo $v["Name"];
            echo "</option>\n\t\t\t\t\t";
        }
    }
    echo "\t\t\t\t\t</select>\n\t\t\t\t\t<br /><br />\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title\">";
    echo __("invoice template");
    echo "</strong>\n\t\t\t\t\t<select name=\"CustomerInvoiceTemplate\" class=\"text1 size14_percentage\" ";
    $ti = tabindex($ti);
    echo ">\n\t\t\t\t\t<option value=\"\">";
    echo __("standard template");
    echo "</option>\n\t\t\t\t\t";
    foreach ($templates as $k => $v) {
        if(is_numeric($k)) {
            echo "\t\t\t\t\t<option value=\"";
            echo $k;
            echo "\" ";
            if($customer->InvoiceTemplate == $k) {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo $v["Name"];
            echo "</option>\n\t\t\t\t\t";
        }
    }
    echo "\t\t\t\t\t</select>\n\t\t\t\t\t<br /><br />\n\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t\n\t\t\t\t";
    if(0 < count($customer->customfields_list)) {
        echo "\t\t\t\t\t\n\t\t\t\t\t<br />\n\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
        echo __("custom debtor fields h2");
        echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t";
        foreach ($customer->customfields_list as $k => $custom_field) {
            $custom_value = isset($customer->customvalues[$custom_field["FieldCode"]]) ? $customer->customvalues[$custom_field["FieldCode"]] : NULL;
            echo "\t\t\t\t\t\t<strong class=\"title\">";
            echo htmlspecialchars($custom_field["LabelTitle"]);
            echo "</strong>\n\t\t\t\t\t\t";
            echo show_custom_input_field($custom_field, $custom_value);
            echo "\t\t\t\t\t\t";
            if($k + 1 != count($customer->customfields_list)) {
                echo "<br /><br />";
            }
        }
        echo "\t\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t";
    }
    echo "\t\n\t\t\t<!--right-->\n\t\t\t</div>\n\t\t\t<!--right-->\n\t\t\n\t\t<!--split2-->\n\t\t</div>\n\t\t<!--split2-->\n\t\t\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\t\n\t";
}
echo "\t\n<!--box1-->\n</div>\n<!--box1-->\n\n<!--form-->\n</fieldset></form>\n<!--form-->\n\n";
require_once "views/footer.php";

?>