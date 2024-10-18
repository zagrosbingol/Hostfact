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
    echo "\t\t<form id=\"PriceQuoteForm\" name=\"form_create\" method=\"post\" action=\"pricequotes.php?page=add\"><fieldset><legend>";
    echo __("create pricequote");
    echo "</legend>\n";
} elseif($_GET["page"] == "edit" || isset($_GET["action"]) && $_GET["action"] == "edit") {
    echo "\t\t<form id=\"PriceQuoteForm\" name=\"form_create\" method=\"post\" action=\"pricequotes.php?page=edit\"><fieldset><legend>";
    echo __("edit pricequote");
    echo "</legend>\n\t\t<input type=\"hidden\" name=\"id\" value=\"";
    echo $pricequote_id;
    echo "\" />\n";
}
echo "<!--form-->\n\n<!--heading1-->\n<div class=\"heading1\">\n<!--heading1-->\n\n\t<h2>\n\t";
if($_GET["page"] == "add") {
    echo __("create pricequote");
} elseif($_GET["page"] == "edit") {
    echo __("edit pricequote");
}
echo "\t</h2>\n\n\t<p class=\"pos2\"><strong class=\"textsize1 pointer\" id=\"Status_text\" style=\"line-height: 22px\">";
echo $array_pricequotestatus[$pricequote->Status];
echo " ";
echo STATUS_CHANGE_ICON;
echo "</strong><select class=\"text1 size1 hide\" name=\"Status\">\n\t";
foreach ($array_pricequotestatus as $key => $value) {
    echo "<option value=\"";
    echo $key;
    echo "\" ";
    if($pricequote->Status == $key) {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo $value;
    echo "</option>";
}
echo "\t</select></p>\n\n\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n<!--split4-->\n<div class=\"split4\" style=\"padding-right: 0px;\">\n<!--split4-->\n\n\t<!--box5-->\n\t<div class=\"box5\" style=\"border-right: 0px; padding-left: 0px; padding-right: 0px;\">\n\t<!--box5-->\n\t\n\t\t<div class=\"split3\" style=\"padding-left: 39px;padding-bottom: 15px;\">\n            <strong class=\"title\">\n                ";
echo __("invoice select debtor");
echo "                ";
if($_GET["page"] == "add") {
    echo "                        &nbsp;&nbsp;&nbsp;&nbsp;\n                        <a href=\"debtors.php?page=add\" class=\"a1 c1 normalfont smallfont hide\" style=\"display: inline;\">\n                            ";
    echo __("or create new debtor");
    echo "                        </a>\n                        ";
}
echo "            </strong>\n\t\t\t<input type=\"hidden\" name=\"Debtor\" value=\"";
echo $pricequote->Debtor;
echo "\" />\n\t\t\t";
$selected_name = 0 < $pricequote->Debtor ? $debtors[$pricequote->Debtor]["DebtorCode"] . " " . ($debtors[$pricequote->Debtor]["CompanyName"] ? $debtors[$pricequote->Debtor]["CompanyName"] : $debtors[$pricequote->Debtor]["SurName"] . ", " . $debtors[$pricequote->Debtor]["Initials"]) : "";
createAutoComplete("debtor", "Debtor", $selected_name, ["class" => "size12"]);
echo "\t\t</div>\n\t\t\n\t\n\t\t<!--split3-->\n\t\t<div class=\"split3\" style=\"margin-left: 30px; margin-right: 30px;padding-bottom: 30px;min-width: 860px;\">\n\t\t<!--split3-->\n\t\t\t\n\t\t\t<!--left-->\n\t\t\t<div class=\"left\" style=\"width: 43%\">\n\t\t\t<!--left-->\n\t\t\t\t\n\t\t\t\t<!--back3-->\n\t\t\t\t<div style=\"padding-left: 10px;\">\n\t\t\t\t<!--back3-->\n\t\t\t\t\t\n\t\t\t\t\t<input type=\"hidden\" id=\"formJQ-Taxable\" name=\"Taxable\" value=\"";
echo $debtor->Taxable ? "true" : "false";
echo "\" />\n\t\t\t\t\t<input type=\"hidden\" name=\"TaxRate1\" value=\"";
echo isset($debtor->TaxRate1) && !is_null($debtor->TaxRate1) ? (double) $debtor->TaxRate1 : "";
echo "\" />\n\t\t\t\t\t<input type=\"hidden\" name=\"TaxRate2\" value=\"";
echo isset($debtor->TaxRate2) && !is_null($debtor->TaxRate2) ? (double) $debtor->TaxRate2 : "";
echo "\" />\n\t\t\t\t\t<input type=\"hidden\" name=\"PriceQuoteID\" value=\"";
echo isset($pricequote_id) ? $pricequote_id : 0;
echo "\" />\n\t\t\t\t\t<input type=\"hidden\" name=\"VatCalcMethod\" value=\"";
echo $pricequote->VatCalcMethod == "incl" ? "incl" : "excl";
echo "\" />\n\n\t\t\t\t\t<div id=\"enveloppe\" class=\"";
if(empty($pricequote->Debtor)) {
    echo "hide ";
}
echo "\" style=\"min-width: 405px;\">\n\t\t\t\t\t\t<strong>";
echo __("invoice address data");
echo "</strong>&nbsp;&nbsp;&nbsp;&nbsp;<span id=\"edit_enveloppe_data\" class=\"a1 c1 smallfont edit_label\">";
echo strtolower(__("edit invoice address data"));
echo "</span><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<div id=\"enveloppe_text\">\n\t\t\t\t\t\t\t<span class=\"title2_value\" id=\"formJQ-CompanyName\">";
echo $pricequote->CompanyName;
echo "</span>\n\t\t\t\t\t\t\t<span class=\"title2_value\" id=\"formJQ-Name\">";
echo $pricequote->Initials . " " . $pricequote->SurName;
echo "</span>\n\t\t\t\t\t\t\t<span class=\"title2_value\" id=\"formJQ-Address\">";
echo $pricequote->Address;
echo "</span>\n\t\t\t\t\t\t\t";
if(IS_INTERNATIONAL) {
    echo "<span class=\"title2_value ";
    if(!$pricequote->Address2) {
        echo "hide";
    }
    echo "\" id=\"formJQ-Address2\">";
    echo $pricequote->Address2;
    echo "</span>";
}
echo "\t\t\t\t\t\t\t<span class=\"title2_value\" id=\"formJQ-ZipCodeCity\">";
echo $pricequote->ZipCode;
echo " ";
echo $pricequote->City;
echo "</span>\n\t\t\t\t\t\t\t";
if(IS_INTERNATIONAL) {
    echo "<span class=\"title2_value ";
    if(!$pricequote->StateName) {
        echo "hide";
    }
    echo "\" id=\"formJQ-State\">";
    echo $pricequote->StateName;
    echo "</span>";
}
echo "\t\t\t\t\t\t\t<span class=\"title2_value\" id=\"formJQ-Country\">";
echo $array_country[$pricequote->Country];
echo "</span>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<span class=\"title2_value\" id=\"formJQ-TaxNumber\">";
if($debtor->TaxNumber) {
    echo __("vat number");
    echo ": ";
    echo $debtor->TaxNumber;
}
echo "</span>\n\t\t\t\t\t\t\t<span class=\"title2_value\" id=\"formJQ-EmailAddress\"><span style=\"display:inline-block;\">";
echo str_replace(",", "&nbsp;&nbsp;</span><span style=\"display:inline-block;\">", check_email_address($pricequote->EmailAddress, "convert", ","));
echo "</span></span>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<table id=\"enveloppe_input\" class=\"noborder hide\" cellpadding=\"0\" cellspacing=\"2\" style=\"margin-left:-1px\">\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td style=\"width:115px;\">";
echo __("companyname");
echo "</td>\n\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t<input type=\"text\" id=\"inputJQ-CompanyName\" name=\"CompanyName\" class=\"text1\" style=\"width: 276px;\" value=\"";
echo $pricequote->CompanyName;
echo "\" />\n\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td>";
echo __("contactperson");
echo ":</td>\n\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t<select class=\"text1 size6\" id=\"inputJQ-Sex\" name=\"Sex\">\n\t\t\t\t\t\t\t\t\t\t";
foreach ($array_sex as $key => $value) {
    echo "\t\t\t\t\t\t\t\t\t\t\t<option value=\"";
    echo $key;
    echo "\" ";
    if($pricequote->Sex == $key) {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo $value;
    echo "</option>\n\t\t\t\t\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t\t\t\t\t</select> \n\t\t\t\t\t\t\t\t\t<input type=\"text\" id=\"inputJQ-Initials\" name=\"Initials\" class=\"text1 size2\" value=\"";
echo $pricequote->Initials;
echo "\" /> \n\t\t\t\t\t\t\t\t\t<input type=\"text\" id=\"inputJQ-SurName\" name=\"SurName\" class=\"text1\" style=\"width: 129px;\" value=\"";
echo $pricequote->SurName;
echo "\" />\n\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td>";
echo __("address");
echo ":</td>\n\t\t\t\t\t\t\t\t<td>\n\n\t\t\t\t\t\t\t\t\t<input type=\"text\" id=\"inputJQ-Address\" name=\"Address\" class=\"text1\" style=\"width: 276px;\" value=\"";
echo $pricequote->Address;
echo "\" />\n\t\t\t\t\t\t\t\t\t";
if(IS_INTERNATIONAL) {
    echo "<input type=\"text\" id=\"inputJQ-Address2\" name=\"Address2\" class=\"text1 marginT2\" style=\"width: 276px;\" value=\"";
    echo $pricequote->Address2;
    echo "\" />";
}
echo "\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td>";
echo __("zipcode and city");
echo ":</td>\n\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t<input type=\"text\" id=\"inputJQ-ZipCode\" name=\"ZipCode\" class=\"text1 size2\" value=\"";
echo $pricequote->ZipCode;
echo "\" />\n\t\t\t\t\t\t\t\t\t<input type=\"text\" id=\"inputJQ-City\" name=\"City\" class=\"text1\" style=\"width: 210px;\" value=\"";
echo $pricequote->City;
echo "\" />\n\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t";
if(IS_INTERNATIONAL) {
    echo "\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td>";
    echo __("state");
    echo ":</td>\n\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t<input type=\"text\" id=\"inputJQ-State\" class=\"text1 ";
    if(isset($array_states[$pricequote->Country])) {
        echo "hide";
    }
    echo "\" style=\"width: 276px;\" name=\"State\" value=\"";
    echo $pricequote->StateName;
    echo "\" maxlength=\"100\"/>\n\t\t\t\t\t\t\t\t\t<select id=\"inputJQ-StateCode\" class=\"text1 size4 ";
    if(!isset($array_states[$pricequote->Country])) {
        echo "hide";
    }
    echo "\" style=\"width: 282px;\" name=\"StateCode\">\n\t\t\t\t\t\t\t\t\t\t<option value=\"\">";
    echo __("make your choice");
    echo "</option>\n\t\t\t\t\t\t\t\t\t\t";
    if(isset($array_states[$pricequote->Country])) {
        foreach ($array_states[$pricequote->Country] as $key => $value) {
            echo "\t\t\t\t\t\t\t\t\t\t<option value=\"";
            echo $key;
            echo "\" ";
            if($pricequote->State == $key) {
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
    if($pricequote->Country == $key) {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo $value;
    echo "</option>\n\t\t\t\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td>";
echo __("emailaddress");
echo ":</td>\n\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t<input type=\"text\" id=\"inputJQ-EmailAddress\" name=\"EmailAddress\" class=\"text1\" style=\"width:276px;\" value=\"";
echo check_email_address($pricequote->EmailAddress, "convert", ", ");
echo "\" maxlength=\"255\" />\n\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t</table>\n\t\t\t\t\t\t\n\t\t\t\t\t</div>\n\t\t\t\n\t\t\t\t<!--back3-->\n\t\t\t\t</div>\n\t\t\t\t<!--back3-->\n\n\t\t\t<!--left-->\n\t\t\t</div>\n\t\t\t<!--left-->\n\t\t\t\n\t\t\t<!--right-->\n\t\t\t<div class=\"right\" style=\"width: 503px;\" id=\"extra_invoicedata_resize\">\n\t\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t\t<!--back3-->\n\t\t\t\t<div id=\"extra_invoicedata\">\n\t\t\t\t<!--back3-->\n\t\t\t\t\t<strong style=\"display: inline-table;width: 125px;\">";
echo __("pricequote feature data");
echo "</strong>\n\t\t\t\t\t<span id=\"edit_extra_enveloppe_data\" class=\"a1 c1 smallfont edit_label \">";
echo strtolower(__("edit pricequote code and date"));
echo "</span><br />\n\t\t\t\t\t<div id=\"extra_invoicedata_input_text\">\n\t\t\t\t\t\t<span class=\"title2_value\"><div style=\"display: inline-table;width: 125px;\">";
echo __("debtorcode");
echo ":</div>\n\t\t\t\t\t\t\t<span id=\"formJQ-DebtorCode\">\n\t\t\t\t\t\t\t\t";
echo $debtor->DebtorCode ? $debtor->DebtorCode : "<i>&lt;&lt; " . strtolower(__("debtorcode")) . " &gt;&gt;</i>";
echo "\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t<span class=\"title2_value\"><div style=\"display: inline-table;width: 125px;\">";
echo __("pricequotecode");
echo ":</div>\n\t\t\t\t\t\t\t<span id=\"PriceQuoteCode_text\">";
echo $pricequote->PriceQuoteCode;
echo "</span>\n\t\t\t\t\t\t\t<input type=\"text\" id=\"PriceQuoteCode\" class=\"text1 size7 hide\" name=\"PriceQuoteCode\" value=\"";
echo $pricequote->PriceQuoteCode;
echo "\"/>\n\t\t\t\t\t\t\t<input type=\"hidden\" name=\"NewNumber\" value=\"";
echo $pricequote->NewNumber ?? "";
echo "\" />\n\t\t\t\t\t\t\t<input type=\"hidden\" name=\"ConceptCode\" value=\"";
echo $pricequote->ConceptCode ?? "";
echo "\" />\n\t\t\t\t\t\t</span>\n\t\t\t\t\t\t<span class=\"title2_value\"><div style=\"display: inline-table;width: 125px;\">";
echo __("date");
echo ":</div>\n\t\t\t\t\t\t\t<span id=\"PriceQuoteDate_text\">";
echo $pricequote->Date;
echo "</span>\n\t\t\t\t\t\t\t\t<input type=\"text\" id=\"PriceQuoteDate\"class=\"text1 size7 hide datepicker\" name=\"PriceQuoteDate\" value=\"";
echo $pricequote->Date;
echo "\"/>\n\t\t\t\t\t\t\t\t<input type=\"hidden\" name=\"NewDate\" value=\"";
echo $pricequote->NewDate ?? "";
echo "\" />\n\t\t\t\t\t\t</span>\n\t\t\t\t\t\t<span class=\"title2_value\"><div style=\"display: inline-table;width: 125px;\">";
echo __("reference no");
echo ":</div>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1\" style=\"width: 240px;\"  name=\"ReferenceNumber\" value=\"";
echo $pricequote->ReferenceNumber;
echo "\"/></span>\n\t\t\t\t\t</div>\n\t\t\t\t<!--back3-->\n\t\t\t\t</div>\n\t\t\t\t<!--back3-->\n\t\n\t\t\t<!--right-->\n\t\t\t</div>\n\t\t\t<!--right-->\n\t\t\n\t\t<!--split3-->\n\t\t</div>\n\t\t<!--split3-->\n\n\t\t";
require_once "elements/customfields.invoice.php";
show_customfields_add($pricequote, $debtor, "pricequote");
require_once "views/elements/invoice.add.php";
$options = ["object_id" => $pricequote_id, "form_type" => "pricequote"];
show_invoice_add($pricequote, $debtor, $options);
echo "\t\t\n\t<!--box5-->\n\t</div>\n\t<!--box5-->\n\n\t\n<!--split4-->\n</div>\n<!--split4-->\n\n<br />\t\t\t\t\t\t\n<p class=\"align_right\">\n    <a class=\"button1 alt1\" id=\"form_create_btn\">\n        <span>";
echo $_GET["page"] == "add" ? __("btn add") : __("btn edit");
echo "</span>\n    </a>\n</p>\n\n\n<!--box1-->\n<div class=\"box2\" id=\"tabs\">\n<!--box1-->\n\t<div id=\"dropzone\" class=\"hide\"><div class=\"bg\"></div><span class=\"dropfileshere\">";
echo __("move your file here");
echo "</span></div>\n\t<input type=\"hidden\" name=\"file_type\" value=\"invoice_files\" />\n\t\n\t<!--top-->\n\t<div class=\"top\">\n\t<!--top-->\n\t\n\t\t<ul class=\"list3\">\n\t\t\t<li><a href=\"#tab-general\">";
echo __("invoice options");
echo "</a></li>\n\t\t\t<li class=\"on\"><a href=\"#tab-comment\">";
echo __("description");
echo "</a></li>\n\t\t\t<li><a href=\"#tab-note\">";
echo __("internal note");
echo "</a></li>\n\t\t</ul>\n\t\n\t<!--top-->\n\t</div>\n\t<!--top-->\n\n\t<!--content-->\n\t<div class=\"content\" id=\"tab-comment\">\n\t<!--content-->\n\n\t\t<!--box3-->\n\t\t<div class=\"box3\"><h3>";
echo __("description");
echo "</h3><div class=\"content\">\n\t\t<!--box3-->\n\t\t\n\t\t\t<textarea class=\"text1 size5 autogrow\" name=\"PriceQuoteDescription\">";
echo $pricequote->PriceQuoteDescription;
echo "</textarea>\n\n\t\t<!--box3-->\n\t\t</div></div>\n\t\t<!--box3-->\n\t\t\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\n\t<!--content-->\n\t<div class=\"content\" id=\"tab-general\">\n\t<!--content-->\n\n\t\t<!--split2-->\n\t\t<div class=\"split2\">\n\t\t<!--split2-->\n\t\t\n\t\t\t<!--left-->\n\t\t\t<div class=\"left\">\n\t\t\t<!--left-->\n\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
echo __("invoice options send");
echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title\">";
echo __("pricequote template");
echo "</strong>\n\t\t\t\t\t<select id=\"formJQ-Template\" name=\"Template\" class=\"text1 size1\">\n\t\t\t\t\t\t";
foreach ($templates as $key => $value) {
    if(is_numeric($key) && is_numeric($value["id"])) {
        echo "\t\t\t\t\t\t<option value=\"";
        echo $value["id"];
        echo "\" ";
        if($value["id"] == $pricequote->Template) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo $value["Name"];
        echo "</option>\n\t\t\t\t\t\t";
    }
}
echo "\t\t\t\t\t</select>\n\t\t\t\t\t<br /><br />\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title\">";
echo __("invoice options invoicemethod");
echo "</strong>\n\t\t\t\t\t<select id=\"formJQ-PriceQuoteMethod\" name=\"PriceQuoteMethod\" class=\"text1 size1\">\n\t\t\t\t\t\t";
foreach ($array_invoicemethod as $key => $value) {
    echo "\t\t\t\t\t\t<option value=\"";
    echo $key;
    echo "\" ";
    if($key == $pricequote->PriceQuoteMethod) {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo $value;
    echo "</option>\n\t\t\t\t\t\t";
}
echo "\t\t\t\t\t</select>\n\t\t\t\t\t<br /><br />\n\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
echo __("pricequote attachments");
echo "</h3><div class=\"content\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\t<div id=\"fileUploadError\" class=\"loading_red removeBr\"></div>\n\t\t\t\t\t<div id=\"files_list\" class=\"hide\">\n\t\t\t\t\t\t<p class=\"align_right mar4\"><i>";
echo __("total");
echo ": <span id=\"files_total\"></span></i></p>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<ul id=\"files_list_ul\" class=\"emaillist\">\n\t\t\t\t\t\t";
$attachCounter = 0;
if(!empty($pricequote->Attachment)) {
    echo "\t\t\t\t\t\t\t";
    foreach ($pricequote->Attachment as $id => $file) {
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
echo "\t\t\t\t\t\t</ul>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<br />\n\t\t\t\t\t</div>\n\t\t\t\t\t\n\t\t\t\t\t<p><i id=\"files_none\" ";
if($attachCounter !== 0) {
    echo "class=\"hide\"";
}
echo ">";
echo __("no attachments");
echo "</i></p>\n\t\t\t\t\t\n\t\t\t\t\t<a id=\"add_files_link\" data-filetype=\"pricequote_files\" class=\"a1 c1 ico inline add upload_file\">";
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
if($pricequote->VatCalcMethod == "excl") {
    echo "selected=\"selected\"";
}
echo ">";
echo __("excluding vat");
echo "</option>\n\t\t\t\t\t\t\t<option value=\"incl\" ";
if($pricequote->VatCalcMethod == "incl") {
    echo "selected=\"selected\"";
}
echo ">";
echo __("including vat");
echo "</option>\n\t\t\t\t\t\t</select>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t</div>\t\t\t\t\t\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title\">";
echo __("ignore discount");
echo "</strong>\n\t\t\t\t\t<select id=\"formJQ-IgnoreDiscount\" name=\"IgnoreDiscount\" class=\"text1 size1\">\n\t\t\t\t\t\t<option value=\"0\" ";
if($pricequote->IgnoreDiscount == "0") {
    echo "selected=\"selected\"";
}
echo ">";
echo __("no");
echo "</option>\n\t\t\t\t\t\t<option value=\"1\" ";
if($pricequote->IgnoreDiscount == "1") {
    echo "selected=\"selected\"";
}
echo ">";
echo __("yes");
echo "</option>\n\t\t\t\t\t</select>\n\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
echo __("pricequote options expiration");
echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title\">";
echo __("pricequote options term");
echo "</strong>\n\t\t\t\t\t<input type=\"text\" name=\"Term\" value=\"";
echo $pricequote->Term;
echo "\" class=\"text1 size3\" /> ";
echo __("days");
echo "\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\n\t\t\t<!--right-->\n\t\t\t</div>\n\t\t\t<!--right-->\n\t\t\t\n\t\t<!--split2-->\n\t\t</div>\n\t\t<!--split2-->\n\t\t\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\t\n\t<!--content-->\n\t<div class=\"content\" id=\"tab-note\">\n\t<!--content-->\n\t\t\n\t\t<!--box3-->\n\t\t<div class=\"box3\"><h3>";
echo __("internal note");
echo "</h3><div class=\"content lineheight2\">\n\t\t<!--box3-->\n\t\t\t\n\t\t\t<textarea name=\"Comment\" class=\"text1 size5 autogrow\">";
echo $pricequote->Comment;
echo "</textarea>\n\t\t\t\n\t\t<!--box3-->\n\t\t</div></div>\n\t\t<!--box3-->\n\t\t\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\t\n<!--box1-->\n</div>\n<!--box1-->\n\n<!--form-->\n</fieldset></form>\n<!--form-->\n\n<div id=\"filemanager\" title=\"";
echo __("filemanager");
echo "\"></div>\n\n\n";
if(in_array($pricequote->Status, [3, 4]) && $pricequote->AcceptName != "") {
    echo "\t<script type=\"text/javascript\">\n\n        \$(function()\n        {\n            \$('#change_status_modal').dialog({modal: true, autoOpen: false, resizable: false, width: 550, height: 'auto'});\n\n            \$('select[name=\"Status\"]').focus(function()\n            {\n                \$(this).data('current', \$(this).val());\n            });\n\n            \$('select[name=\"Status\"]').change(function()\n            {\n                if((\$(this).data('current') == '3' || \$(this).data('current') == '4') && (\$(this).val() != '3' && \$(this).val() != '4'))\n                {\n                    \$('#change_status_modal').dialog('open');\n                }\n            });\n\n            \$(document).on('click', '#change_status_cancel', function()\n            {\n                \$('select[name=\"Status\"]').val(\$('select[name=\"Status\"]').data('current'));\n                \$('select[name=\"Status\"]').blur();\n                \$('#change_status_modal').dialog('close');\n            });\n        });\n\n\t</script>\n\n\t<div class=\"hide\" id=\"change_status_modal\" title=\"";
    echo __("change pricequote status");
    echo "\">\n\t\t<strong>";
    echo __("confirm your action");
    echo "</strong><br />\n\t\t";
    echo __("change pricequote status will remove accept data");
    echo "<br /><br />\n\n\t\t<a class=\"button1 alt1 float_left\" onclick=\"\$('#change_status_modal').dialog('close');\"><span>";
    echo __("proceed");
    echo "</span></a>\n\t\t<a class=\"a1 c1 float_right\" id=\"change_status_cancel\"><span>";
    echo __("cancel");
    echo "</span></a>\n\t</div>\n\t";
}
echo "\n";
require_once "views/footer.php";

?>