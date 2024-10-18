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
$page_form_title = 0 < $creditinvoice->Identifier ? __("edit creditinvoice") : __("add creditinvoice");
echo "\n";
echo $message;
echo "\n<!--form-->\n<form id=\"CreditorInvoiceForm\" name=\"form_create\" method=\"post\" action=\"creditors.php?page=edit_creditinvoice&amp;id=";
echo $creditinvoice->Identifier;
echo "\" enctype=\"multipart/form-data\"><fieldset><legend>";
echo $page_form_title;
echo "</legend>\n<input type=\"hidden\" name=\"id\" value=\"";
echo $creditinvoice->Identifier;
echo "\" />\t\n<!--form-->\n\n\n<!--heading1-->\n<div class=\"heading1\">\n<!--heading1-->\n\n\t<h2>";
echo $page_form_title;
echo "</h2>\n\t<p class=\"pos2\"><strong class=\"textsize1 pointer\" id=\"CreditInvoiceCode_text\" style=\"line-height: 22px\">";
echo $creditinvoice->CreditInvoiceCode;
echo " <span class=\"ico actionblock arrowdown mar2 pointer\" style=\"margin: 4px 2px 2px 2px; z-index: 1;\">&nbsp;</span></strong><input type=\"text\" class=\"text2 size7 hide\" name=\"CreditInvoiceCode\" value=\"";
echo $creditinvoice->CreditInvoiceCode;
echo "\"/></p>\n\n\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\t\n\t<!--box1-->\n\t<div class=\"box2\" id=\"tabs\">\n\t<!--box1-->\n\t\n\t\t<!--top-->\n\t\t<div class=\"top\">\n\t\t<!--top-->\n\t\t\n\t\t\t<ul class=\"list3\">\n\t\t\t\t<li class=\"on\"><a href=\"#tab-general\">";
echo __("general");
echo "</a></li>\n\t\t\t</ul>\n\t\t\n\t\t<!--top-->\n\t\t</div>\n\t\t<!--top-->\n\n\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-general\">\n\t\t<!--content-->\n\t\t\t<div id=\"dropzone\" data-filetype=\"creditinvoice_files\" class=\"hide\"><div class=\"bg\"></div><span class=\"dropfileshere\">";
echo __("move your file here");
echo "</span></div>\n\t\t\t<input type=\"hidden\" name=\"file_type\" value=\"creditinvoice_files\" />\n\t\t\t\t\t\t\n\t\t\t<!--split2-->\n\t\t\t<div class=\"split2\">\n\t\t\t<!--split2-->\n\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t<div class=\"left\">\n\t\t\t\t<!--left-->\n\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("general");
echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n                        <strong class=\"title\">\n                            ";
echo __("purchasing invoice select creditor");
echo "                            ";
if(!$creditinvoice->Identifier) {
    echo "                                    &nbsp;&nbsp;&nbsp;&nbsp;\n                                    <a id=\"creditor_new\" class=\"a1 c1 normalfont smallfont ";
    echo $creditinvoice->Creditor && $creditinvoice->Creditor != "new" ? "" : " hide";
    echo "\">\n                                        ";
    echo __("or create new creditor");
    echo "                                    </a>\n                                    ";
}
echo "                        </strong>\n\t\t\t\t\t\t<input type=\"hidden\" name=\"Creditor\" value=\"";
echo $creditinvoice->Creditor ? $creditinvoice->Creditor : "new";
echo "\" />\n\t\t\t\t\t\t";
$selected_name = 0 < $creditinvoice->Creditor ? $list_creditors[$creditinvoice->Creditor]["CreditorCode"] . " " . ($list_creditors[$creditinvoice->Creditor]["CompanyName"] ? $list_creditors[$creditinvoice->Creditor]["CompanyName"] : $list_creditors[$creditinvoice->Creditor]["SurName"] . ", " . $list_creditors[$creditinvoice->Creditor]["Initials"]) : "";
createAutoComplete("creditor", "Creditor", $selected_name, ["class" => "size12"]);
echo "\t\t\t\t\t\t<br />\n\n\t\t\t\t\t\t<div id=\"enveloppe\">\n\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t<strong class=\"enveloppe_title_new";
echo $creditinvoice->Creditor && $creditinvoice->Creditor != "new" ? " hide" : "";
echo "\">";
echo ucfirst(__("or create new creditor"));
echo "</strong>\n\t\t\t\t\t\t\t<strong class=\"enveloppe_title";
echo $creditinvoice->Creditor && $creditinvoice->Creditor != "new" ? "" : " hide";
echo "\">";
echo __("address data");
echo "</strong><br />\n\t\t\t\t\t\t\t<div id=\"enveloppe_text\" class=\"";
if(0 >= $creditinvoice->Creditor) {
    echo " hide";
}
echo "\">\n\t\t\t\t\t\t\t\t<span id=\"formJQ-Name\">";
echo $creditor->CompanyName ? $creditor->CompanyName : $creditor->Initials . " " . $creditor->SurName;
echo "</span><br />\n\t\t\t\t\t\t\t\t<span id=\"formJQ-Address\">";
echo $creditor->Address;
echo "</span><br />\n\t\t\t\t\t\t\t\t";
if(IS_INTERNATIONAL) {
    echo "<span class=\"title2_value ";
    if(!$creditor->Address2) {
        echo "hide";
    }
    echo "\" id=\"formJQ-Address2\">";
    echo $creditor->Address2;
    echo "</span>";
}
echo "\t\t\t\t\t\t\t\t<span id=\"formJQ-ZipCode\">";
echo $creditor->ZipCode;
echo "</span>&nbsp;&nbsp;<span id=\"formJQ-City\">";
echo $creditor->City;
echo "</span><br />\n\t\t\t\t\t\t\t\t";
if(IS_INTERNATIONAL) {
    echo "<span class=\"title2_value ";
    if(!$creditor->StateName) {
        echo "hide";
    }
    echo "\" id=\"formJQ-State\">";
    echo $creditor->StateName;
    echo "</span>";
}
echo "\t\t\t\t\t\t\t\t<span id=\"formJQ-Country\">";
echo $array_country[$creditor->Country];
echo "</span>\n\t\t\t\t\t\t\t</div>\n\n\t\t\t\t\t\t\t<table id=\"enveloppe_input\" class=\"noborder ";
if(0 < $creditinvoice->Creditor) {
    echo " hide";
}
echo "\" cellpadding=\"0\" cellspacing=\"2\" style=\"margin-left:-1px;width:430px;\">\n\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t<td style=\"width:125px;\">";
echo __("companyname");
echo ":</td>\n\t\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t\t<input type=\"text\" id=\"inputJQ-CompanyName\" name=\"CompanyName\" class=\"text1\" style=\"width: 270px;\" value=\"";
echo $creditor->CompanyName;
echo "\" />\n\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t<td>";
echo __("contactperson");
echo ":</td>\n\t\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t\t<select class=\"text1 size6\" id=\"inputJQ-Sex\" name=\"Sex\">\n\t\t\t\t\t\t\t\t\t\t\t";
foreach ($array_sex as $key => $value) {
    echo "\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"";
    echo $key;
    echo "\" ";
    if($creditor->Sex == $key) {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo $value;
    echo "</option>\n\t\t\t\t\t\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t\t\t<input type=\"text\" id=\"inputJQ-Initials\" name=\"Initials\" class=\"text1 size2\" value=\"";
echo $creditor->Initials;
echo "\" />\n\t\t\t\t\t\t\t\t\t\t<input type=\"text\" id=\"inputJQ-SurName\" name=\"SurName\" class=\"text1\" style=\"width: 123px;\" value=\"";
echo $creditor->SurName;
echo "\" />\n\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t<td>";
echo __("address");
echo ":</td>\n\t\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t\t";
if(defined("PDF_MODULE") && PDF_MODULE == "tcpdf") {
    echo "\t\t\t\t\t\t\t\t\t\t\t<textarea name=\"Address\" id=\"inputJQ-Address\" class=\"text1 autogrow size1\" style=\"width: 269px;\">";
    echo $creditor->Address;
    echo "</textarea>\n\t\t\t\t\t\t\t\t\t\t\t";
} else {
    echo "\t\t\t\t\t\t\t\t\t\t\t<input type=\"text\" id=\"inputJQ-Address\" name=\"Address\" class=\"text1\" style=\"width: 269px;\" value=\"";
    echo $creditor->Address;
    echo "\" />\n\t\t\t\t\t\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t<td>";
echo __("zipcode and city");
echo ":</td>\n\t\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t\t<input type=\"text\" id=\"inputJQ-ZipCode\" name=\"ZipCode\" class=\"text1 size2\" value=\"";
echo $creditor->ZipCode;
echo "\" />\n\t\t\t\t\t\t\t\t\t\t<input type=\"text\" id=\"inputJQ-City\" name=\"City\" class=\"text1\" style=\"width: 204px;\" value=\"";
echo $creditor->City;
echo "\" />\n\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t<td>";
echo __("country");
echo ":</td>\n\t\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t\t<select class=\"text1 size4\" id=\"inputJQ-Country\" style=\"width:269px;\" name=\"Country\">\n\t\t\t\t\t\t\t\t\t\t\t";
foreach ($array_country as $key => $value) {
    echo "\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"";
    echo $key;
    echo "\" ";
    if($creditor->Country == $key) {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo $value;
    echo "</option>\n\t\t\t\t\t\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t</table>\n\t\t\t\t\t\t\t";
if(isset($creditor->UBLMyCustomerCode) && $creditor->UBLMyCustomerCode) {
    echo "\t\t\t\t\t\t\t\t<input type=\"hidden\" name=\"MyCustomerCode\" value=\"";
    echo $creditor->UBLMyCustomerCode;
    echo "\" />\n\t\t\t\t\t\t\t\t";
}
if(isset($creditor->UBLAccountNumber) && $creditor->UBLAccountNumber) {
    echo "\t\t\t\t\t\t\t\t<input type=\"hidden\" name=\"AccountNumber\" value=\"";
    echo $creditor->UBLAccountNumber;
    echo "\" />\n\t\t\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("invoice date");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1 datepicker\" name=\"Date\" value=\"";
echo $creditinvoice->Date;
echo "\" ";
$ti = tabindex($ti);
echo " />\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("external invoice code");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"InvoiceCode\" value=\"";
echo $creditinvoice->InvoiceCode;
echo "\" maxlength=\"100\" ";
$ti = tabindex($ti);
echo " />\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("status");
echo "</strong>\n\t\t\t\t\t\t<select name=\"Status\" class=\"text1 size4\">\n\t\t\t\t\t\t\t";
foreach ($array_creditinvoicestatus as $key => $value) {
    if(is_numeric($key)) {
        echo "\t\t\t\t\t\t\t<option value=\"";
        echo $key;
        echo "\" ";
        if($creditinvoice->Status == $key) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo $value;
        echo "</option>\n\t\t\t\t\t\t\t";
    }
}
echo "\t\t\t\t\t\t</select>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<div id=\"div_partpayment\" ";
if($creditinvoice->Status != 2) {
    echo "class=\"hide\"";
}
echo ">\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("partial payment paid payment");
echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" name=\"AmountPaid\" class=\"text1 size6\" value=\"";
echo $creditinvoice->Status == 2 ? money($creditinvoice->AmountPaid, false) : "";
echo "\" />\n\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<div id=\"div_payment_date\" ";
if($creditinvoice->Status != 3) {
    echo "class=\"hide\"";
}
echo ">\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("payment date");
echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1 datepicker\" name=\"PayDate\" value=\"";
echo $creditinvoice->PayDate ? $creditinvoice->PayDate : rewrite_date_db2site(date("Y-m-d"));
echo "\" ";
$ti = tabindex($ti);
echo " />\n\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\t\t\t\t\t\n\t\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t</div>\n\t\t\t\t<!--left-->\n\t\t\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t<div class=\"right\">\n\t\t\t\t<!--right-->\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("upload original credit invoice");
echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t<div id=\"fileUploadError\" class=\"loading_red removeBr\"></div>\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("file");
echo "</strong>\n\t\t\t\t\t\t";
$creditAttachment = isset($creditinvoice->Attachment[0]) ? $creditinvoice->Attachment[0] : "";
$creditAttachmentNameId = "";
if(0 < strlen($creditinvoice->Location)) {
    $creditAttachmentNameId = $creditinvoice->Location;
    $fileName = $creditinvoice->Location;
} elseif(isset($creditinvoice->Attachment[0])) {
    $creditAttachmentNameId = $creditAttachment->id;
    $fileName = $creditAttachment->Filename;
} else {
    $fileName = __("no attachments");
}
echo "\t\t\t\t\t\t\n\t\t\t\t\t\t<input type=\"hidden\" name=\"File[]\" value=\"";
echo $creditAttachmentNameId;
echo "\" />\n\t\t\t\t\t\t<span id=\"creditinvoice_file\">\n                            ";
if(0 < strlen($creditinvoice->Location) || isset($creditinvoice->Attachment[0])) {
    echo " \n                                    <div class=\"file ico inline file_";
    echo getFileType($fileName);
    echo "\">&nbsp;</div>\n                                    ";
}
echo "                            ";
echo $fileName;
echo "                        </span>\n\t\t\t\t\t\t<span class=\"a1 c1 ico actionblock trash\" id=\"creditinvoice_file_link\" ";
if(!isset($creditinvoice->Attachment[0]) && strlen($creditinvoice->Location) === 0) {
    echo "style=\"display:none;\"";
}
echo ">";
echo strtolower(__("edit"));
echo "</span><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<a data-filetype=\"creditinvoice_files\" class=\"add_files_link a1 c1 ico inline add pointer upload_file\">";
echo __("upload file");
echo "</a> \n\t\t\t\t\t\t<span id=\"dragndrophere\">";
echo __("or move your file here");
echo "</span>\n\t\t\t\t\t\t<br/>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("extra information");
echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("reference number");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"ReferenceNumber\" value=\"";
echo $creditinvoice->ReferenceNumber;
echo "\" ";
$ti = tabindex($ti);
echo " />\n\t\t\t\t\t\t<br />\n\n                        <div id=\"div_authorisation\" style=\"margin-top: 10px;\" ";
if((int) $creditinvoice->Status !== 0 && $creditinvoice->Status != 1 && $creditinvoice->Status != 3) {
    echo "class=\"hide\"";
}
echo ">\n                            <label>\n                                <input type=\"checkbox\" name=\"Authorisation\" value=\"yes\" ";
echo $creditinvoice->Authorisation == "yes" ? "checked=\"checked\"" : "";
echo " />\n                                ";
echo __("creditinvoice authorisation single");
echo "                            </label>\n                            <br />\n                        </div>\n\n                        <div id=\"div_payment_term\" ";
if(((int) $creditinvoice->Status === 0 || $creditinvoice->Status == 1 || $creditinvoice->Status == 3) && $creditinvoice->Authorisation == "yes") {
    echo "class=\"hide\"";
}
echo ">\n                            <br />\n                            <strong class=\"title\">";
echo __("payment term");
echo "</strong>\n                            <input type=\"text\" class=\"text1 size3\" name=\"Term\" value=\"";
echo 0 < $creditinvoice->Term || 0 < $creditinvoice->Identifier ? $creditinvoice->Term : $creditor->Term;
echo "\" maxlength=\"3\" ";
$ti = tabindex($ti);
echo " /> ";
echo __("days");
echo "                            <br />\n                        </div>\n\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("private part title");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\n\t\t\t\t\t\t<label><input type=\"checkbox\" name=\"invoiceHasPrivatePart\" value=\"yes\" ";
$ti = tabindex($ti);
echo " ";
if(0 < $creditinvoice->Private || 0 < $creditinvoice->PrivatePercentage) {
    echo "checked=\"checked\"";
}
echo "/> <strong>";
echo __("is there a private part");
echo "</strong></label>\n\t\t\t\t\t\n\t\t\t\t\t\t<div id=\"invoice_private_part_div\" ";
if((int) $creditinvoice->Private === 0 && isEmptyFloat(number2db($creditinvoice->PrivatePercentage))) {
    echo "class=\"hide\"";
}
echo ">\n\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("private amount excl vat");
echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"Private\" value=\"";
echo money($creditinvoice->Private, false);
echo "\" ";
$ti = tabindex($ti);
echo " />\n\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("private percentage");
echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size3\" name=\"PrivatePercentage\" value=\"";
echo showNumber($creditinvoice->PrivatePercentage);
echo "\" ";
$ti = tabindex($ti);
echo " /> %\n\t\t\t\t\t\t</div>\n\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t</div>\n\t\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t<!--split2-->\n\t\t\t</div>\n\t\t\t<!--split2-->\n\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
echo __("invoice elements");
echo "</h3><div class=\"content\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t<table border=\"0\" width=\"100%\">\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<td style=\"width: 55px;\"><strong>";
echo __("number");
echo "</strong></td>\n\t\t\t\t\t\t\t<td style=\"\"><strong>";
echo __("description");
echo "</strong></td>\n\t\t\t\t\t\t\t<td style=\"width: 120px;\"><strong>";
echo __("pieceprice") . " " . __("excl vat");
echo "</strong></td>\n\t\t\t\t\t\t\t<td style=\"width: 60px;\"><strong>";
echo __("vat");
echo " (%)</strong></td>\n\t\t\t\t\t\t\t<td style=\"width: 22px;\">&nbsp;</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tbody id=\"InvoiceElements\">\n\t\t\t\t\t\t";
$i = 0;
$linesDisplayed = 0;
foreach ($creditinvoice->Elements as $key => $item) {
    if(is_numeric($key)) {
        $linesDisplayed++;
        echo "<tr class=\"tr_invoiceelement\">\n\t\t\t\t\t\t\t\t\t<td><input type=\"hidden\" name=\"LineID[";
        echo $i;
        echo "]\" value=\"";
        echo $item["id"];
        echo "\" />\n\t\t\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size3\" name=\"Number[";
        echo $i;
        echo "]\" value=\"";
        echo showNumber($item["Number"], false, false);
        echo "\" ";
        $ti = tabindex($ti);
        echo " /></td>\n\t\t\t\t\t\t\t\t\t<td><input type=\"text\" class=\"text1 size8\" name=\"Description[";
        echo $i;
        echo "]\" value=\"";
        echo $item["Description"];
        echo "\" ";
        $ti = tabindex($ti);
        echo " /></td>\n\t\t\t\t\t\t\t\t\t<td><input type=\"text\" class=\"text1 size6\" name=\"PriceExcl[";
        echo $i;
        echo "]\" value=\"";
        echo money($item["PriceExcl"], false, false);
        echo "\" ";
        $ti = tabindex($ti);
        echo " /></td>\n\t\t\t\t\t\t\t\t\t<td><input type=\"text\" class=\"text1 size6\" name=\"TaxPercentage[";
        echo $i;
        echo "]\" value=\"";
        echo showNumber($item["TaxPercentage"] * 100);
        echo "\" ";
        $ti = tabindex($ti);
        echo " /></td>\n\t\t\t\t\t\t\t\t\t<td><img class=\"pointer\" onclick=\"removeCreditInvoiceElement('";
        echo $i;
        echo "');\" src=\"images/ico_trash.png\" /></td>\n\t\t\t\t\t\t\t\t</tr>";
        $i++;
    }
}
if(isset($_POST["NumberOfElements"])) {
    $items = array_reverse($_POST["Number"], true);
    foreach ($items as $x => $xValue) {
        if($_POST["Number"][$x] == "1" && $_POST["Description"][$x] == "" && isEmptyFloat(deformat_money($_POST["PriceExcl"][$x]))) {
            $_POST["Number"][$x] = "0";
        } elseif($_POST["Number"][$x] != "0") {
            while ($i <= $_POST["NumberOfElements"]) {
                if($_POST["Number"][$i] !== NULL && (string) $_POST["Number"][$i] !== "0") {
                    $linesDisplayed++;
                    echo "<tr class=\"tr_invoiceelement\">\n\t\t\t\t\t\t\t\t\t\t<td><input type=\"text\" class=\"text1 size3\" name=\"Number[";
                    echo $i;
                    echo "]\" value=\"";
                    echo htmlspecialchars(esc(number2site($_POST["Number"][$i]) . $_POST["NumberSuffix"][$i]));
                    echo "\" ";
                    $ti = tabindex($ti);
                    echo " /></td>\n\t\t\t\t\t\t\t\t\t\t<td><input type=\"text\" class=\"text1 size8\" name=\"Description[";
                    echo $i;
                    echo "]\" value=\"";
                    echo htmlspecialchars(esc($_POST["Description"][$i]));
                    echo "\" ";
                    $ti = tabindex($ti);
                    echo " /></td>\n\t\t\t\t\t\t\t\t\t\t<td><input type=\"text\" class=\"text1 size6\" name=\"PriceExcl[";
                    echo $i;
                    echo "]\" value=\"";
                    echo htmlspecialchars(esc(money($_POST["PriceExcl"][$i], false, false)));
                    echo "\" ";
                    $ti = tabindex($ti);
                    echo " /></td>\n\t\t\t\t\t\t\t\t\t\t<td><input type=\"text\" class=\"text1 size6\" name=\"TaxPercentage[";
                    echo $i;
                    echo "]\" value=\"";
                    echo htmlspecialchars(esc(str_replace(".", ",", $_POST["TaxPercentage"][$i])));
                    echo "\" ";
                    $ti = tabindex($ti);
                    echo " /></td>\n\t\t\t\t\t\t\t\t\t\t<td><img class=\"pointer\" onclick=\"removeCreditInvoiceElement('";
                    echo $i;
                    echo "');\" src=\"images/ico_trash.png\" /></td>\n\t\t\t\t\t\t\t\t\t</tr>";
                }
                $i++;
            }
        }
        $x--;
    }
}
echo "\t\t\t\t\t\t<tr id=\"NewElement\" class=\"tr_invoiceelement ";
if(0 < $linesDisplayed || 0 < $creditinvoice->Identifier && !empty($_POST)) {
    echo "hide";
}
echo "\">\n\t\t\t\t\t\t\t<td><input type=\"text\" class=\"text1 size3\" name=\"Number[";
echo $i;
echo "]\" value=\"1\" /></td>\n\t\t\t\t\t\t\t<td><input type=\"text\" class=\"text1 size8\" name=\"Description[";
echo $i;
echo "]\" value=\"\" /></td>\n\t\t\t\t\t\t\t<td><input type=\"text\" class=\"text1 size6\" name=\"PriceExcl[";
echo $i;
echo "]\" value=\"\" /></td>\n\t\t\t\t\t\t\t<td><input type=\"text\" class=\"text1 size6\" name=\"TaxPercentage[";
echo $i;
echo "]\" value=\"";
echo showNumber(STANDARD_TAX * 100);
echo "\" /></td>\n\t\t\t\t\t\t\t<td><img class=\"pointer\" onclick=\"removeCreditInvoiceElement('";
echo $i;
echo "');\" src=\"images/ico_trash.png\" /></td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t</tbody>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td colspan=\"4\"><br /><span id=\"add_new_element\" class=\"pointer\"><img src=\"images/ico_add.png\" style=\"float: left; margin-right: 10px;\"/> ";
echo __("add new element");
echo "</span></td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t</table>\n\t\t\t\t\t\n\t\t\t\t\t<table class=\"table8\" border=\"0\" width=\"100%\" cellspacing=\"0\" >\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td>&nbsp;</td>\n\t\t\t\t\t\t\t<td colspan=\"5\" class=\"line\"></td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td>&nbsp;</td>\n\t\t\t\t\t\t\t<td style=\"width:172px;\" class=\"align_right\">";
echo __("amountexcl");
echo ":</td>\n\t\t\t\t\t\t\t<td style=\"width:20px;\">&nbsp;</td>\n\t\t\t\t\t\t\t<td style=\"width:7px;\" class=\"currency_sign_left\">";
echo currency_sign_td(CURRENCY_SIGN_LEFT);
echo "</td>\n\t\t\t\t\t\t\t<td class=\"align_right\" style=\"width: 70px;\" id=\"total-excl\">";
echo money($creditinvoice->AmountExcl, false);
echo "</td>\n\t\t\t\t\t\t\t<td style=\"width: 37px;\" class=\"currency_sign_right\">";
echo currency_sign_td(CURRENCY_SIGN_RIGHT);
echo "</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tbody id=\"creditInvoiceTax\"></tbody>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td>&nbsp;</td>\n\t\t\t\t\t\t\t<td class=\"align_right\">";
echo __("amountincl");
echo ":</td>\n\t\t\t\t\t\t\t<td>&nbsp;</td>\n\t\t\t\t\t\t\t<td class=\"currency_sign_left\">";
echo currency_sign_td(CURRENCY_SIGN_LEFT);
echo "</td>\n\t\t\t\t\t\t\t<td class=\"align_right\"><input type=\"text\" name=\"total-incl\" class=\"text1 size7\" style=\"text-align: right;margin-right: -6px;margin-left: 10px;width: 65px;\" id=\"total-incl\" value=\"";
echo money(deformat_money($creditinvoice->AmountIncl), false);
echo "\" /></td>\n\t\t\t\t\t\t\t<td class=\"currency_sign_right\" style=\"padding-left: 8px;\">";
echo currency_sign_td(CURRENCY_SIGN_RIGHT);
echo "</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t</table>\n\t\t\t\t\t\n\t\t\t\t\t<input type=\"hidden\" name=\"NumberOfElements\" value=\"";
echo $i;
echo "\" />\n\t\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\t\t\n\t<!--box1-->\n\t</div>\n\t<!--box1-->\n\n\t<br />\n\t<p class=\"align_right\">\n\t\t";
if(!$creditinvoice->Identifier) {
    echo "\t\t\t<label class=\"create_another_label\"><input type=\"checkbox\" name=\"CreateAnother\" value=\"yes\" ";
    echo isset($_POST["CreateAnother"]) && $_POST["CreateAnother"] == "yes" ? "checked=\\\"checked\\\"" : "";
    echo " /> ";
    echo __("add another one");
    echo "</label>\n\t\t";
}
echo "\t\t<a class=\"button1 alt1\" id=\"form_create_btn\">\n            <span>";
echo 0 < $creditinvoice->Identifier ? __("btn edit") : __("btn add");
echo "</span>\n        </a>\n\t</p>\n\t\n\t<!--form-->\n\t</fieldset></form>\n\t<!--form-->\n\n<div id=\"filemanager\" title=\"";
echo __("filemanager");
echo "\"></div>\n\n<div id=\"div_for_creditorsearch\"></div>\n\n";
require_once "views/footer.php";

?>