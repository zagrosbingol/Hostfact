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
    echo "\t\t<form id=\"InvoiceForm\" name=\"form_create\" method=\"post\" action=\"invoices.php?page=add\"><fieldset><legend>";
    echo __("create invoice");
    echo "</legend>\n";
} elseif($_GET["page"] == "edit" || isset($_GET["action"]) && $_GET["action"] == "edit") {
    echo "\t\t<form id=\"InvoiceForm\" name=\"form_create\" method=\"post\" action=\"invoices.php?page=edit\"><fieldset><legend>";
    echo __("edit invoice");
    echo "</legend>\n\t\t<input type=\"hidden\" name=\"id\" value=\"";
    echo $invoice_id;
    echo "\" />\n";
}
echo "<!--form-->\n\n<!--heading1-->\n<div class=\"heading1\">\n<!--heading1-->\n\n\t<h2>\n\t";
if($_GET["page"] == "add") {
    echo __("create invoice");
} elseif($_GET["page"] == "edit") {
    echo __("edit invoice");
}
echo "\t</h2>\n\n\t<p class=\"pos2\"><strong class=\"textsize1 pointer\" id=\"Status_text\" style=\"line-height: 22px\">";
echo $array_invoicestatus[$invoice->Status];
echo " ";
echo STATUS_CHANGE_ICON;
echo "</strong><select class=\"text1 size1 hide\" name=\"Status\">\n\t";
foreach ($array_invoicestatus as $key => $value) {
    echo "<option value=\"";
    echo $key;
    echo "\" ";
    if($invoice->Status == $key) {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo $value;
    echo "</option>";
}
echo "\t</select></p>\n\n\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n<!--split4-->\n<div class=\"split4\" style=\"padding-right: 0px;\">\n<!--split4-->\n\n\t<!--box5-->\n\t<div class=\"box5\" style=\"border-right: 0px; padding-left: 0px; padding-right: 0px;\">\n\t<!--box5-->\n\t\t\n\t\t\t<div class=\"split3\" style=\"padding-left: 39px;padding-bottom: 15px;\">\n\t\t\t\t<strong class=\"title\">\n                    ";
echo __("invoice select debtor");
echo "                    ";
if($_GET["page"] == "add") {
    echo "                            &nbsp;&nbsp;&nbsp;&nbsp;\n                            <a href=\"debtors.php?page=add\" class=\"a1 c1 normalfont smallfont hide\" style=\"display: inline;\">\n                                ";
    echo __("or create new debtor");
    echo "                            </a>\n                            ";
}
echo "                </strong>\n\t\t\t\t<input type=\"hidden\" name=\"Debtor\" value=\"";
echo $invoice->Debtor;
echo "\" />\n\t\t\t\t";
$selected_name = 0 < $invoice->Debtor ? $debtors[$invoice->Debtor]["DebtorCode"] . " " . ($debtors[$invoice->Debtor]["CompanyName"] ? $debtors[$invoice->Debtor]["CompanyName"] : $debtors[$invoice->Debtor]["SurName"] . ", " . $debtors[$invoice->Debtor]["Initials"]) : "";
createAutoComplete("debtor", "Debtor", $selected_name, ["class" => "size12"]);
echo "\t\t\t</div>\n\t\t\n\t\t\t<!--split3-->\n\t\t\t<div class=\"split3\" style=\"margin-left: 30px; margin-right: 30px;padding-bottom: 30px;min-width: 860px;\">\n\t\t\t<!--split3-->\n\t\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t<div class=\"left\" style=\"width: 43%\">\n\t\t\t\t<!--left-->\n\t\t\t\t\t\n\t\t\t\t\t<!--back3-->\n\t\t\t\t\t<div style=\"padding-left: 10px;\">\n\t\t\t\t\t<!--back3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t<input type=\"hidden\" id=\"formJQ-Taxable\" name=\"Taxable\" value=\"";
echo $debtor->Taxable ? "true" : "false";
echo "\" />\n\t\t\t\t\t\t<input type=\"hidden\" name=\"TaxRate1\" value=\"";
echo isset($debtor->TaxRate1) && !is_null($debtor->TaxRate1) ? (double) $debtor->TaxRate1 : "";
echo "\" />\n\t\t\t\t\t\t<input type=\"hidden\" name=\"TaxRate2\" value=\"";
echo isset($debtor->TaxRate2) && !is_null($debtor->TaxRate2) ? (double) $debtor->TaxRate2 : "";
echo "\" />\n\t\t\t\t\t\t<input type=\"hidden\" name=\"InvoiceID\" value=\"";
echo isset($invoice_id) ? $invoice_id : 0;
echo "\" />\n\t\t\t\t\t\t<input type=\"hidden\" name=\"VatCalcMethod\" value=\"";
echo $invoice->VatCalcMethod == "incl" ? "incl" : "excl";
echo "\" />\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<div id=\"enveloppe\" class=\"";
if(empty($invoice->Debtor)) {
    echo "hide ";
}
echo "\" style=\"min-width: 440px;\">\n\t\t\t\t\t\t\t\t<strong>";
echo __("invoice address data");
echo "</strong>&nbsp;&nbsp;&nbsp;&nbsp;<span id=\"edit_enveloppe_data\" class=\"a1 c1 smallfont edit_label\">";
echo strtolower(__("edit invoice address data"));
echo "</span><br />\n\t\t\t\t\t\t\t\t<div id=\"enveloppe_text\">\n\t\t\t\t\t\t\t\t\t<span class=\"title2_value\" id=\"formJQ-CompanyName\">";
echo $invoice->CompanyName;
echo "</span>\n\t\t\t\t\t\t\t\t\t<span class=\"title2_value\" id=\"formJQ-Name\">";
echo $invoice->Initials . " " . $invoice->SurName;
echo "</span>\n\t\t\t\t\t\t\t\t\t<span class=\"title2_value\" id=\"formJQ-Address\">";
echo $invoice->Address;
echo "</span>\n\t\t\t\t\t\t\t\t\t";
if(IS_INTERNATIONAL) {
    echo "<span class=\"title2_value ";
    if(!$invoice->Address2) {
        echo "hide";
    }
    echo "\" id=\"formJQ-Address2\">";
    echo $invoice->Address2;
    echo "</span>";
}
echo "\t\t\t\t\t\t\t\t\t<span class=\"title2_value\" id=\"formJQ-ZipCodeCity\">";
echo $invoice->ZipCode;
echo " ";
echo $invoice->City;
echo "</span>\n\t\t\t\t\t\t\t\t\t";
if(IS_INTERNATIONAL) {
    echo "<span class=\"title2_value ";
    if(!$invoice->StateName) {
        echo "hide";
    }
    echo "\" id=\"formJQ-State\">";
    echo $invoice->StateName;
    echo "</span>";
}
echo "\t\t\t\t\t\t\t\t\t<span class=\"title2_value\" id=\"formJQ-Country\">";
echo $array_country[$invoice->Country];
echo "</span>\n\t\t\t\t\t\t\t\t\t<span class=\"title2_value\" id=\"formJQ-TaxNumber\">";
if($invoice->TaxNumber) {
    echo __("vat number");
    echo " ";
    echo $invoice->TaxNumber;
}
echo "</span>\n\t\t\t\t\t\t\t\t\t<span class=\"title2_value\" id=\"formJQ-EmailAddress\"><span style=\"display:inline-block;\">";
echo str_replace(",", "&nbsp;&nbsp;</span><span style=\"display:inline-block;\">", check_email_address($invoice->EmailAddress, "convert", ","));
echo "</span></span>\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<table id=\"enveloppe_input\" class=\"noborder hide\" cellpadding=\"0\" cellspacing=\"2\" style=\"margin-left:-1px\">\n\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t<td style=\"width:115px;\">";
echo __("companyname");
echo ":</td>\n\t\t\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t\t\t<input type=\"text\" id=\"inputJQ-CompanyName\" name=\"CompanyName\" class=\"text1\" style=\"width: 276px;\" value=\"";
echo $invoice->CompanyName;
echo "\" />\n\t\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t<td>";
echo __("contactperson");
echo ":</td>\n\t\t\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t\t\t<select class=\"text1 size6\" id=\"inputJQ-Sex\" name=\"Sex\">\n\t\t\t\t\t\t\t\t\t\t\t\t";
foreach ($array_sex as $key => $value) {
    echo "\t\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"";
    echo $key;
    echo "\" ";
    if($invoice->Sex == $key) {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo $value;
    echo "</option>\n\t\t\t\t\t\t\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t\t\t\t\t\t\t</select> \n\t\t\t\t\t\t\t\t\t\t\t<input type=\"text\" id=\"inputJQ-Initials\" name=\"Initials\" class=\"text1 size2\" value=\"";
echo $invoice->Initials;
echo "\" /> \n\t\t\t\t\t\t\t\t\t\t\t<input type=\"text\" id=\"inputJQ-SurName\" name=\"SurName\" class=\"text1\" style=\"width: 129px;\" value=\"";
echo $invoice->SurName;
echo "\" />\n\t\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t<td>";
echo __("address");
echo ":</td>\n\t\t\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t\t\t<input type=\"text\" id=\"inputJQ-Address\" name=\"Address\" class=\"text1\" style=\"width: 276px;\" value=\"";
echo $invoice->Address;
echo "\" />\n\t\t\t\t\t\t\t\t\t\t\t";
if(IS_INTERNATIONAL) {
    echo "<input type=\"text\" id=\"inputJQ-Address2\" name=\"Address2\" class=\"text1 marginT2\" style=\"width: 276px;\" value=\"";
    echo $invoice->Address2;
    echo "\" />";
}
echo "\t\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t<td>";
echo __("zipcode and city");
echo ":</td>\n\t\t\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t\t\t<input type=\"text\" id=\"inputJQ-ZipCode\" name=\"ZipCode\" class=\"text1 size2\" value=\"";
echo $invoice->ZipCode;
echo "\" />\n\t\t\t\t\t\t\t\t\t\t\t<input type=\"text\" id=\"inputJQ-City\" name=\"City\" class=\"text1\" style=\"width: 210px;\" value=\"";
echo $invoice->City;
echo "\" />\n\t\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t\t";
if(IS_INTERNATIONAL) {
    echo "\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t<td>";
    echo __("state");
    echo ":</td>\n\t\t\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t\t\t<input type=\"text\" id=\"inputJQ-State\" class=\"text1 ";
    if(isset($array_states[$invoice->Country])) {
        echo "hide";
    }
    echo "\" style=\"width: 276px;\" name=\"State\" value=\"";
    echo $invoice->StateName;
    echo "\" maxlength=\"100\"/>\n\t\t\t\t\t\t\t\t\t\t\t<select id=\"inputJQ-StateCode\" class=\"text1 size4 ";
    if(!isset($array_states[$invoice->Country])) {
        echo "hide";
    }
    echo "\" style=\"width:282px;\" name=\"StateCode\">\n\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"\">";
    echo __("make your choice");
    echo "</option>\n\t\t\t\t\t\t\t\t\t\t\t\t";
    if(isset($array_states[$invoice->Country])) {
        foreach ($array_states[$invoice->Country] as $key => $value) {
            echo "\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"";
            echo $key;
            echo "\" ";
            if($invoice->State == $key) {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo $value;
            echo "</option>\n\t\t\t\t\t\t\t\t\t\t\t";
        }
    }
    echo "\t\t\t\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t<td>";
echo __("country");
echo ":</td>\n\t\t\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t\t\t<select class=\"text1\" style=\"width:276px;\" id=\"inputJQ-Country\" name=\"Country\">\n\t\t\t\t\t\t\t\t\t\t\t";
foreach ($array_country as $key => $value) {
    echo "\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"";
    echo $key;
    echo "\" ";
    if($invoice->Country == $key) {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo $value;
    echo "</option>\n\t\t\t\t\t\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t<td>";
echo __("emailaddress");
echo ":</td>\n\t\t\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t\t\t<input type=\"text\" id=\"inputJQ-EmailAddress\" name=\"EmailAddress\" class=\"text1\" style=\"width:276px;\" value=\"";
echo check_email_address($invoice->EmailAddress, "convert", ", ");
echo "\"  maxlength=\"255\" />\n\t\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t\t<tr class=\"containerTaxNumber\" style=\"";
echo $invoice->TaxNumber || $invoice->CompanyName ? "" : "display: none;";
echo "\">\n\t\t\t\t\t\t\t\t\t\t<td>";
echo __("taxnumber");
echo ":</td>\n\t\t\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t\t\t<input type=\"text\" id=\"inputJQ-TaxNumber\" name=\"TaxNumber\" class=\"text1 showTaxNumberInput ";
echo $invoice->TaxNumber || $invoice->CompanyName ? "" : "hide";
echo "\" style=\"width: 276px;\" value=\"";
echo $invoice->TaxNumber;
echo "\" />\n\t\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t</table>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t<!--back3-->\n\t\t\t\t\t</div>\n\t\t\t\t\t<!--back3-->\n\t\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t</div>\n\t\t\t\t<!--left-->\n\t\t\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t<div class=\"right\" style=\"width: 503px;\" id=\"extra_invoicedata_resize\">\n\t\t\t\t<!--right-->\n\t\t\t\t\t<!--back3-->\n\t\t\t\t\t<div id=\"extra_invoicedata\">\n\t\t\t\t\t<!--back3-->\n\t\t\t\t\t\t<strong style=\"display: inline-table;width: 125px;\">";
echo __("invoice feature data");
echo "</strong>\n\t\t\t\t\t\t<span id=\"add_existing_invoice\" class=\"a1 c1 smallfont edit_label";
echo (int) $invoice->Status === 0 ? "" : " hide";
echo "\">";
echo strtolower(__("add existing invoice"));
echo "</span>\n\t\t\t\t\t\t<span id=\"edit_extra_enveloppe_data\" class=\"a1 c1 smallfont edit_label";
echo (int) $invoice->Status === 0 ? " hide" : "";
echo "\">";
echo strtolower(__("edit invoice code and date"));
echo "</span><br />\n\t\t\t\t\t\t<div id=\"extra_invoicedata_input_text\">\n\t\t\t\t\t\t\t<span class=\"title2_value\"><div style=\"display: inline-table;width: 125px;\">";
echo __("debtorcode");
echo ":</div>\n\t\t\t\t\t\t\t\t<span id=\"formJQ-DebtorCode\">\n\t\t\t\t\t\t\t\t\t";
echo $debtor->DebtorCode ? $debtor->DebtorCode : "<i>&lt;&lt; " . strtolower(__("debtorcode")) . " &gt;&gt;</i>";
echo "\t\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t<span class=\"title2_value\"><div style=\"display: inline-table;width: 125px;\">";
echo __("invoicecode");
echo ":</div>\n\t\t\t\t\t\t\t\t<span id=\"InvoiceCode_text\">";
echo $invoice->InvoiceCode;
echo "</span>\n\t\t\t\t\t\t\t\t<input type=\"text\" id=\"InvoiceCode\" class=\"text1 size7 hide\" name=\"InvoiceCode\" value=\"";
echo $invoice->InvoiceCode;
echo "\"/>\n\t\t\t\t\t\t\t\t<input type=\"hidden\" name=\"NewNumber\" value=\"";
echo $invoice->NewNumber;
echo "\" />\n\t\t\t\t\t\t\t\t<input type=\"hidden\" name=\"ConceptCode\" value=\"";
echo $invoice->ConceptCode;
echo "\" />\n\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t<span class=\"title2_value\"><div style=\"display: inline-table;width: 125px;\">";
echo __("date");
echo ":</div>\n\t\t\t\t\t\t\t\t<span id=\"InvoiceDate_text\">";
echo $invoice->Status == "0" ? "<i>&lt;&lt; " . __("placeholder text invoice date of dispatch") . " &gt;&gt;</i>" : $invoice->Date;
echo "</span>\n\t\t\t\t\t\t\t\t\t<input type=\"text\" id=\"InvoiceDate\"class=\"text1 size7 hide datepicker\" name=\"InvoiceDate\" value=\"";
echo $invoice->Date;
echo "\"/>\n\t\t\t\t\t\t\t\t\t<input type=\"hidden\" name=\"NewDate\" value=\"";
echo $invoice->NewDate;
echo "\" />\n\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t<span class=\"title2_value\"><div style=\"display: inline-table;width: 125px;\">";
echo __("reference no");
echo ":</div>\n\t\t\t\t\t\t\t\t<input type=\"text\" class=\"text1\" style=\"width: 240px;\"  name=\"ReferenceNumber\" value=\"";
echo $invoice->ReferenceNumber;
echo "\"/></span>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t<!--back3-->\n\t\t\t\t\t</div>\n\t\t\t\t\t<!--back3-->\n\t\n\t\t\t\t<!--right-->\n\t\t\t\t</div>\n\t\t\t\t<!--right-->\n\t\t\t\n\t\t<!--split3-->\n\t\t</div>\n\t\t<!--split3-->\n\t\n\t\t";
require_once "views/elements/customfields.invoice.php";
show_customfields_add($invoice, $debtor);
require_once "views/elements/invoice.add.php";
$options = ["object_id" => $invoice_id];
show_invoice_add($invoice, $debtor, $options);
echo "\t\t\n\t<!--box5-->\n\t</div>\n\t<!--box5-->\n\n\t\n<!--split4-->\n</div>\n<!--split4-->\n\n<br />\t\t\t\t\t\t\n<p class=\"align_right\">\n    <a class=\"button1 alt1\" id=\"";
echo $_GET["page"] == "add" ? "form_create_invoice" : "form_create_btn";
echo "\">\n        <span>";
echo $_GET["page"] == "add" ? __("btn add") : __("btn edit");
echo "</span>\n    </a>\n</p>\n\n\n\n<!--box1-->\n<div class=\"box2\" id=\"tabs\">\n<!--box1-->\n\t<div id=\"dropzone\" class=\"hide\"><div class=\"bg\"></div><span class=\"dropfileshere\">";
echo __("move your file here");
echo "</span></div>\n\t<input type=\"hidden\" name=\"file_type\" value=\"invoice_files\" />\n\t\n\t<!--top-->\n\t<div class=\"top\">\n\t<!--top-->\n\t\n\t\t<ul class=\"list3\">\n\t\t\t<li class=\"on\"><a href=\"#tab-extra\">";
echo __("invoice options");
echo "</a></li>\n\t\t\t<li><a href=\"#tab-description\">";
echo __("description");
echo "</a></li>\n\t\t\t<li><a href=\"#tab-note\">";
echo __("internal note");
echo "</a></li>\n\t\t</ul>\n\t\n\t<!--top-->\n\t</div>\n\t<!--top-->\n\n\t<!--content-->\n\t<div class=\"content\" id=\"tab-extra\">\n\t<!--content-->\n\n\t\t<!--split2-->\n\t\t<div class=\"split2\">\n\t\t<!--split2-->\n\t\t\n\t\t\t<!--left-->\n\t\t\t<div class=\"left\">\n\t\t\t<!--left-->\n\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
echo __("invoice options send");
echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title\">";
echo __("invoice template");
echo "</strong>\n\t\t\t\t\t<select id=\"formJQ-Template\" name=\"Template\" class=\"text1 size1\">\n\t\t\t\t\t\t";
foreach ($templates as $key => $value) {
    if(is_numeric($key) && is_numeric($value["id"])) {
        echo "\t\t\t\t\t\t<option value=\"";
        echo $value["id"];
        echo "\" ";
        if($value["id"] == $invoice->Template) {
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
    if($key == $invoice->InvoiceMethod) {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo $value;
    echo "</option>\n\t\t\t\t\t\t";
}
echo "\t\t\t\t\t</select>\n\t\t\t\t\t<br /><br />\n\t\t\t\t\t\n\t\t\t\t\t<div id=\"formJQ-SentDate\" class=\"";
if(!in_array($invoice->Status, [2, 3, 4, 8, 9])) {
    echo "hide";
}
echo "\">\n\t\t\t\t\t<strong class=\"title\">";
echo __("sentdate invoice");
echo "</strong>\n\t\t\t\t\t<input type=\"text\" name=\"SentDate\" value=\"";
echo rewrite_date_db2site($invoice->SentDate);
echo "\" class=\"text1 size1 datepicker\" />\n\t\t\t\t\t<br /><br />\n\t\t\t\t\t</div>\n\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
echo __("invoice attachments");
echo "</h3><div class=\"content\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\t<div id=\"fileUploadError\" class=\"loading_red removeBr\"></div>\n\t\t\t\t\t<div id=\"files_list\" class=\"hide\">\n\t\t\t\t\t\t<p class=\"align_right mar4\"><i>";
echo __("total");
echo ": <span id=\"files_total\"></span></i></p>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<ul id=\"files_list_ul\" class=\"emaillist\">\n\t\t\t\t\t\t";
$attachCounter = 0;
if(!empty($invoice->Attachment)) {
    echo "\t\t\t\t\t\t\t";
    foreach ($invoice->Attachment as $id => $file) {
        echo "\t\t\t\t\t\t\t<li>\n\t\t\t\t\t\t\t\t<input type=\"hidden\" name=\"File[]\" value=\"";
        echo $file->id;
        echo "\" />\n\t\t\t\t\t\t\t\t<div class=\"delete_cross not_visible file_delete\">&nbsp;</div>\n\t\t\t\t\t\t\t\t<div class=\"file ico inline file_";
        echo getFileType($file->FilenameServer);
        echo "\">&nbsp;</div> ";
        echo $file->Filename;
        echo "\t\t\t\t\t\t\t\t<div class=\"filesize\">";
        $fileSizeUnit = getFileSizeUnit($file->Size);
        echo $fileSizeUnit["size"] . " " . $fileSizeUnit["unit"];
        echo "</div>\n\t\t\t\t\t\t\t</li>\n\t\t\t\t\t\t\t";
        $attachCounter++;
    }
    echo "\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t</ul>\n\t\t\t\t\t\t<br />\n\t\t\t\t\t</div>\n\t\t\t\t\t\n\t\t\t\t\t<p><i id=\"files_none\" ";
if($attachCounter !== 0) {
    echo "class=\"hide\"";
}
echo ">";
echo __("no attachments");
echo "</i></p>\n\t\t\t\t\t\n\t\t\t\t\t<a id=\"add_files_link\" data-filetype=\"invoice_files\" class=\"a1 c1 ico inline add upload_file\">";
echo __("add attachment");
echo "</a>\n\t\t\t\t\t<span id=\"dragndrophere\">";
echo __("or move your files here");
echo "</span>\n\t\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t\n\t\t\t<!--left-->\n\t\t\t</div>\n\t\t\t<!--left-->\n\t\t\t\n\t\t\t<!--right-->\n\t\t\t<div class=\"right\">\n\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
echo __("invoice options amounts");
echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t\t<div id=\"vatcalcmethod_helper_div\" ";
if(isset($debtor->TaxRate1) && !is_null($debtor->TaxRate1)) {
    echo "class=\"hide\"";
}
echo ">\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("invoice options which vat calc method to use");
echo "</strong>\n\t\t\t\t\t\t<select name=\"VatCalcMethodHelper\" class=\"text1 size1\">\n\t\t\t\t\t\t\t<option value=\"excl\" ";
if($invoice->VatCalcMethod == "excl") {
    echo "selected=\"selected\"";
}
echo ">";
echo __("excluding vat");
echo "</option>\n\t\t\t\t\t\t\t<option value=\"incl\" ";
if($invoice->VatCalcMethod == "incl") {
    echo "selected=\"selected\"";
}
echo ">";
echo __("including vat");
echo "</option>\n\t\t\t\t\t\t</select>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t</div>\t\t\t\t\t\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title\">";
echo __("ignore discount");
echo "</strong>\n\t\t\t\t\t<select id=\"formJQ-IgnoreDiscount\" name=\"IgnoreDiscount\" class=\"text1 size1\">\n\t\t\t\t\t\t<option value=\"0\" ";
if($invoice->IgnoreDiscount == "0") {
    echo "selected=\"selected\"";
}
echo ">";
echo __("no");
echo "</option>\n\t\t\t\t\t\t<option value=\"1\" ";
if($invoice->IgnoreDiscount == "1") {
    echo "selected=\"selected\"";
}
echo ">";
echo __("yes");
echo "</option>\n\t\t\t\t\t</select>\n\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
echo __("invoice options payment");
echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title\">";
echo __("invoice options term");
echo "</strong>\n\t\t\t\t\t<input type=\"text\" name=\"Term\" value=\"";
echo $invoice->Term;
echo "\" class=\"text1 size3\" /> ";
echo __("days");
echo "\t\t\t\t\t<br /><br />\n\n                    <div id=\"div-authorisation\"";
if($invoice->Status == 8) {
    echo " class=\"hide\"";
}
echo ">\n                        <strong class=\"title\">";
echo __("invoice options authorisation");
echo "</strong>\n                        <select id=\"formJQ-Authorisation\" name=\"Authorisation\" class=\"text1 size1\">\n                            <option value=\"no\" ";
if($invoice->Authorisation == "no") {
    echo "selected=\"selected\"";
}
echo ">";
echo __("no");
echo "</option>\n                            <option value=\"yes\" ";
if($invoice->Authorisation == "yes") {
    echo "selected=\"selected\"";
}
echo ">";
echo __("yes");
echo "</option>\n                        </select>\n                        <br /><br />\n                    </div>\n\t\t\t\t\t\n\t\t\t\t\t<div id=\"formJQ-PayDate\" class=\"";
if($invoice->Status != 4) {
    echo "hide";
}
echo "\">\n\t\t\t\t\t<strong class=\"title\">";
echo __("paydate invoice");
echo "</strong>\n\t\t\t\t\t<input type=\"text\" name=\"PayDate\" value=\"";
echo $invoice->PayDate;
echo "\" class=\"text1 size1 datepicker\" />\n\t\t\t\t\t<br /><br />\n\t\t\t\t\t</div>\n\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\n\t\t\t<!--right-->\n\t\t\t</div>\n\t\t\t<!--right-->\n\t\t\t\n\t\t<!--split2-->\n\t\t</div>\n\t\t<!--split2-->\n\t\t\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\t\n\t\n\t<!--content-->\n\t<div class=\"content\" id=\"tab-description\">\n\t<!--content-->\n\t\n\t\t<!--box3-->\n\t\t<div class=\"box3\"><h3>";
echo __("description");
echo "</h3><div class=\"content lineheight2\">\n\t\t<!--box3-->\n\t\t\t\n\t\t\t<textarea name=\"InvoiceDescription\" class=\"text1 size5 autogrow\">";
echo $invoice->InvoiceDescription;
echo "</textarea>\n\n\t\t<!--box3-->\n\t\t</div></div>\n\t\t<!--box3-->\n\t\t\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\t\n\t<!--content-->\n\t<div class=\"content\" id=\"tab-note\">\n\t<!--content-->\n\t\n\t\t<!--box3-->\n\t\t<div class=\"box3\"><h3>";
echo __("internal note");
echo "</h3><div class=\"content lineheight2\">\n\t\t<!--box3-->\n\t\t\t\n\t\t\t<textarea name=\"Comment\" class=\"text1 size5 autogrow\">";
echo $invoice->Comment;
echo "</textarea>\n\n\t\t<!--box3-->\n\t\t</div></div>\n\t\t<!--box3-->\n\t\t\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\t\n<!--box1-->\n</div>\n<!--box1-->\n\n<!--form-->\n</fieldset></form>\n<!--form-->\n\n<!-- Load invoice conceptdialog   -->\n";
require_once "views/elements/invoice.conceptdialog.php";
echo "\n<div id=\"filemanager\" title=\"";
echo __("filemanager");
echo "\"></div>\n\n";
if($_GET["page"] == "add") {
    echo "<div id=\"credit_invoice_modal\" title=\"";
    echo __("invoice add credit - positive amount modal - title");
    echo "\" class=\"hide\">\n\t";
    echo __("invoice add credit - positive amount modal - description");
    echo "\t<br/><br/>\n\n\t<strong class=\"strong\">";
    echo __("invoice add credit - positive amount modal - what to do");
    echo "</strong><br />\n\t<label><input type=\"radio\" name=\"CreditInvoiceAction\" value=\"none\" checked=\"checked\" /> ";
    echo __("invoice add credit - positive amount modal - keep");
    echo "</label><br />\n\t<label><input type=\"radio\" name=\"CreditInvoiceAction\" value=\"toggle\" /> ";
    echo __("invoice add credit - positive amount modal - toggle");
    echo "</label><br />\n\t<br />\n\n\t<a id=\"credit_invoice_toggle_btn\" class=\"button1 alt1\">";
    echo __("proceed");
    echo "</a>\n\t<a class=\"a1 c1 float_right\" onclick=\"\$('#credit_invoice_modal').dialog('close');\">";
    echo __("cancel");
    echo "</a>\n</div>\n";
}
echo "\n";
require_once "views/footer.php";

?>