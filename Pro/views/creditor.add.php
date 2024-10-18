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
$page_form_title = 0 < $creditor_id ? __("edit creditor") : __("add creditor");
echo "\n";
echo $message;
echo "\t\n\n<!--form-->\n<form id=\"CreditorForm\" name=\"form_create\" method=\"post\" action=\"?page=";
if(0 < $creditor_id) {
    echo "edit_creditor";
} else {
    echo "add_creditor";
}
echo "\"><fieldset><legend>";
echo $page_form_title;
echo "</legend>\n<!--form-->\n";
if(0 < $creditor_id) {
    echo "\t<input type=\"hidden\" name=\"id\" value=\"";
    echo $creditor_id;
    echo "\" />\n";
}
echo "\n\t<!--heading1-->\n\t<div class=\"heading1\">\n\t<!--heading1-->\n\t\n\t\t<h2>";
echo $page_form_title;
echo "</h2>\n\t\t<p class=\"pos2\"><strong class=\"textsize1 pointer\" id=\"CreditorCode_text\" style=\"line-height: 22px\">";
echo $creditor->CreditorCode;
echo " <span class=\"ico actionblock arrowdown mar2 pointer\" style=\"margin: 4px 2px 2px 2px; z-index: 1;\">&nbsp;</span></strong><input type=\"text\" class=\"text2 size7 hide\" name=\"CreditorCode\" value=\"";
echo $creditor->CreditorCode;
echo "\" maxlength=\"50\"/></p>\n\t\n\t<!--heading1-->\n\t</div><hr />\n\t<!--heading1-->\n\t\n\t<!--box1-->\n\t<div class=\"box2\" id=\"tabs\">\n\t<!--box1-->\n        \n        <div id=\"dropzone\" class=\"hide\"><div class=\"bg\"></div><span class=\"dropfileshere\">";
echo __("move your file here");
echo "</span></div>\n        <input type=\"hidden\" name=\"file_type\" value=\"creditor_files\" />\n\t\n\t\t<!--top-->\n\t\t<div class=\"top\">\n\t\t<!--top-->\n\t\t\n\t\t\t<ul class=\"list3\">\n\t\t\t\t<li class=\"on\"><a href=\"#tab-general\">";
echo __("general");
echo "</a></li>\n\t\t\t\t<li><a href=\"#tab-groups\">";
echo __("creditorgroups");
echo "</a></li>\n\t\t\t\t<li><a href=\"#tab-comment\">";
echo __("internal note");
echo "</a></li>\n                <li><a href=\"#tab-attachments\">";
echo __("attachments");
echo "</a></li>\n\t\t\t</ul>\n\t\t\t\n\t\t\n\t\t<!--top-->\n\t\t</div>\n\t\t<!--top-->\n\n\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-general\">\n\t\t<!--content-->\n\n\t\t\t<!--split2-->\n\t\t\t<div class=\"split2\">\n\t\t\t<!--split2-->\n\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t<div class=\"left\">\n\t\t\t\t<!--left-->\n\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("creditor data");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("companyname");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"CompanyName\" value=\"";
echo $creditor->CompanyName;
echo "\" maxlength=\"100\" ";
$ti = tabindex($ti);
echo " />\n\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t<div id=\"CompanyName_extra\" ";
if(!$creditor->CompanyName) {
    echo "class=\"hide\"";
}
echo ">\n\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("company number");
echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"CompanyNumber\" value=\"";
echo $creditor->CompanyNumber;
echo "\" ";
$ti = tabindex($ti);
echo " maxlength=\"20\"/>\n\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("vat number");
echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"TaxNumber\" value=\"";
echo $creditor->TaxNumber;
echo "\" ";
$ti = tabindex($ti);
echo " maxlength=\"20\"/>\n\t\t\t\t\t\t\t<span id=\"vat_status\"></span>\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<br />\n\n\t\t\t\t\t\t<div class=\"inputgroup_size14_percentage\">\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("contact person");
echo "</strong>\n\t\t\t\t\t\t\t<select name=\"Sex\" class=\"text1 size16\" ";
$ti = tabindex($ti);
echo ">\n\t\t\t\t\t\t\t";
foreach ($array_sex as $k => $v) {
    echo "\t\t\t\t\t\t\t\t<option value=\"";
    echo $k;
    echo "\" ";
    if($creditor->Sex == $k) {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo $v;
    echo "</option>\n\t\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size16\" name=\"Initials\" value=\"";
echo $creditor->Initials;
echo "\" ";
$ti = tabindex($ti);
echo " maxlength=\"25\"/>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size15_calc\" name=\"SurName\" value=\"";
echo $creditor->SurName;
echo "\" ";
$ti = tabindex($ti);
echo " maxlength=\"100\"/>\n\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("address");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"Address\" value=\"";
echo $creditor->Address;
echo "\" ";
$ti = tabindex($ti);
echo " maxlength=\"100\"/>\n\t\t\t\t\t\t";
if(IS_INTERNATIONAL) {
    echo "<br /><input type=\"text\" class=\"text1 size14_percentage marginT2\" name=\"Address2\" value=\"";
    echo $creditor->Address2;
    echo "\" ";
    $ti = tabindex($ti);
    echo " maxlength=\"100\"/>";
}
echo "\t\t\t\t\t\t<br /><br />\n\n\t\t\t\t\t\t<div class=\"inputgroup_size14_percentage\">\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("zipcode and city");
echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size16\" name=\"ZipCode\" value=\"";
echo $creditor->ZipCode;
echo "\" ";
$ti = tabindex($ti);
echo " maxlength=\"10\"/> <input type=\"text\" class=\"text1 size_calc_100-size16\" name=\"City\" value=\"";
echo $creditor->City;
echo "\" ";
$ti = tabindex($ti);
echo " maxlength=\"100\"/>\n\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t</div>\n\n\t\t\t\t\t\t";
if(IS_INTERNATIONAL) {
    echo "\t\t\t\t\t\t\t<strong class=\"title\">";
    echo __("state");
    echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage ";
    if(isset($array_states[$creditor->Country])) {
        echo "hide";
    }
    echo "\" name=\"State\" value=\"";
    echo $creditor->StateName;
    echo "\" ";
    $ti = tabindex($ti);
    echo " maxlength=\"100\"/>\n\t\t\t\t\t\t\t<select class=\"text1 size14_percentage ";
    if(!isset($array_states[$creditor->Country])) {
        echo "hide";
    }
    echo "\" name=\"StateCode\" ";
    $ti = tabindex($ti);
    echo ">\n\t\t\t\t\t\t\t\t<option value=\"\">";
    echo __("make your choice");
    echo "</option>\n\t\t\t\t\t\t\t";
    if(isset($array_states[$creditor->Country])) {
        foreach ($array_states[$creditor->Country] as $key => $value) {
            echo "\t\t\t\t\t\t\t\t<option value=\"";
            echo $key;
            echo "\" ";
            if($creditor->State == $key) {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo $value;
            echo "</option>\n\t\t\t\t\t\t\t";
        }
    }
    echo "\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t";
}
echo "\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("country");
echo "</strong>\n\t\t\t\t\t\t<select class=\"text1 size14_percentage\" name=\"Country\" ";
$ti = tabindex($ti);
echo ">\n\t\t\t\t\t\t";
foreach ($array_country as $key => $value) {
    echo "\t\t\t\t\t\t\t<option value=\"";
    echo $key;
    echo "\" ";
    if($creditor->Country == $key) {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo $value;
    echo "</option>\n\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t</select>\n\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t<br />\n\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("contact data");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("emailaddress");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"EmailAddress\" value=\"";
echo check_email_address($creditor->EmailAddress, "convert", ", ");
echo "\" ";
$ti = tabindex($ti);
echo "/>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("phonenumber");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"PhoneNumber\" value=\"";
echo $creditor->PhoneNumber;
echo "\" ";
$ti = tabindex($ti);
echo " maxlength=\"25\"/>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("mobilenumber");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"MobileNumber\" value=\"";
echo $creditor->MobileNumber;
echo "\" ";
$ti = tabindex($ti);
echo " maxlength=\"25\"/>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("faxnumber");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"FaxNumber\" value=\"";
echo $creditor->FaxNumber;
echo "\" ";
$ti = tabindex($ti);
echo " maxlength=\"25\"/>\n\t\t\t\t\t\t<br /><br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("my customer code");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"MyCustomerCode\" value=\"";
echo $creditor->MyCustomerCode;
echo "\" ";
$ti = tabindex($ti);
echo "/>\n\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t</div>\n\t\t\t\t<!--left-->\n\n\t\t\t\t<!--right-->\n\t\t\t\t<div class=\"right\">\n\t\t\t\t<!--right-->\n\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("bankaccount data");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("account number");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"AccountNumber\" value=\"";
echo $creditor->AccountNumber;
echo "\" ";
$ti = tabindex($ti);
echo "/>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("account name");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"AccountName\" value=\"";
echo $creditor->AccountName;
echo "\" ";
$ti = tabindex($ti);
echo "/>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("bank");
echo "</strong>\n\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"AccountBank\" value=\"";
echo $creditor->AccountBank;
echo "\" ";
$ti = tabindex($ti);
echo "/>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("bank city");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"AccountCity\" value=\"";
echo $creditor->AccountCity;
echo "\" ";
$ti = tabindex($ti);
echo "/>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("bic");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"AccountBIC\" value=\"";
echo $creditor->AccountBIC;
echo "\" ";
$ti = tabindex($ti);
echo "/>\n\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("financial data");
echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t\t<!--box3-->\n\n                        <label><input type=\"checkbox\" name=\"Authorisation\" value=\"yes\" ";
echo $creditor->Authorisation == "yes" ? "checked=\"checked\"" : "";
echo "/> ";
echo __("creditinvoice authorisation");
echo "</label>\n\n                        <br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("payment term");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size6\" name=\"Term\" value=\"";
echo 0 < $creditor->Term ? $creditor->Term : "";
echo "\" ";
$ti = tabindex($ti);
echo "/> ";
echo __("days");
echo "\t\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t</div>\n\t\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t<!--split2-->\n\t\t\t</div>\n\t\t\t<!--split2-->\n\t\t\t\t\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\t\t\n\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-groups\">\n\n\t\t<!--content-->\n\t\t\n\t\t\t\t<p>\n\t\t\t\t\t<strong>";
echo __("select creditorgroups to connect to creditor");
echo "</strong>\n\t\t\t\t</p>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t<table id=\"MainTable\" class=\"table1\" cellpadding=\"0\" cellspacing=\"0\">\n\t\t\t\t\t<tr class=\"trtitle\">\n\t\t\t\t\t\t<th scope=\"col\"><label><input name=\"GroupBatch\" class=\"BatchCheck\" type=\"checkbox\" value=\"on\" /></label> Crediteurgroep</th>\n\t\t\t\t\t</tr>\n\t\t\t\t\t";
$groupCounter = 0;
foreach ($groups as $groupID => $group) {
    if(is_numeric($groupID)) {
        $groupCounter++;
        echo "\t\t\t\t\t\t\t<tr";
        if($groupCounter % 2 == 1) {
            echo " class=\"tr1\"";
        }
        echo ">\n\t\t\t\t\t\t\t\t<td><label><input name=\"Groups[]\" type=\"checkbox\" class=\"GroupBatch\" value=\"";
        echo $groupID;
        echo "\" ";
        if(array_key_exists($groupID, $creditor->Groups)) {
            echo "checked=\"checked\"";
        }
        echo "/> ";
        echo $group["GroupName"];
        echo "</label></td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t";
    }
}
if($groupCounter === 0) {
    echo "\t\t\t\t\t\t<tr class=\"tr1\">\n\t\t\t\t\t\t\t<td>";
    echo __("no creditorgroups found");
    echo "</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t";
}
echo "\t\t\t\t</table>\t\t   \n\t\t\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\n\t\t\n\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-comment\">\n\t\t<!--content-->\n\t\t\n\t\t\t<!--box3-->\n\t\t\t<div class=\"box3\"><h3>";
echo __("internal note");
echo "</h3><div class=\"content\">\n\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t<strong class=\"title\">";
echo __("add comment to creditor");
echo "</strong>\n\t\t\t\t<textarea class=\"text1 size5 autogrow\" name=\"Comment\">";
if($creditor->Comment) {
    echo $creditor->Comment;
}
echo "</textarea>\n\n\t\t\t<!--box3-->\n\t\t\t</div></div>\n\t\t\t<!--box3-->\n\t\t\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n        \n        <!-- ATTACHMENTS -->\n        <div class=\"content\" id=\"tab-attachments\">\n\t\t\t<div class=\"box3\">\n            \n                <h3>";
echo __("attachments");
echo "</h3>\n                \n                <div class=\"content\">\n\t\t\t\t\t\t\n    \t\t\t\t<div id=\"fileUploadError\" class=\"loading_red removeBr\"></div>\n\t\t\t\t\t\n                    <div id=\"files_list\" class=\"hide\">\n\t\t\t\t\t\t<p class=\"align_right mar4\">\n                            <i>";
echo __("total");
echo ": <span id=\"files_total\"></span></i>\n                        </p>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<ul id=\"files_list_ul\" class=\"emaillist\">\n                        ";
$attachCounter = 0;
if(!empty($creditor->Attachment)) {
    foreach ($creditor->Attachment as $id => $file) {
        echo "        \t\t\t\t\t\t\t<li>\n        \t\t\t\t\t\t\t\t<input type=\"hidden\" name=\"File[]\" value=\"";
        echo $file->id;
        echo "\" />\n        \t\t\t\t\t\t\t\t<div class=\"delete_cross not_visible file_delete\">&nbsp;</div>\n        \t\t\t\t\t\t\t\t<div class=\"file ico inline file_";
        echo getFileType($file->FilenameServer);
        echo "\">&nbsp;</div> ";
        echo $file->Filename;
        echo "        \t\t\t\t\t\t\t\t<div class=\"filesize\">";
        $fileSizeUnit = getFileSizeUnit($file->Size);
        echo $fileSizeUnit["size"] . " " . $fileSizeUnit["unit"];
        echo "</div>\n        \t\t\t\t\t\t\t</li>\n                                    ";
        $attachCounter++;
    }
}
echo "\t\t\t\t\t\t</ul>\n\t\t\t\t\t\t<br />\n\t\t\t\t\t</div>\n\t\t\t\t\t\n\t\t\t\t\t<p><i id=\"files_none\" ";
if($attachCounter !== 0) {
    echo "class=\"hide\"";
}
echo ">";
echo __("no attachments");
echo "</i></p>\n\t\t\t\t\t\n\t\t\t\t\t<a id=\"add_files_link\" data-filetype=\"creditor_files\" class=\"a1 c1 ico inline add upload_file\">";
echo __("add attachment");
echo "</a>\n\t\t\t\t\t<span id=\"dragndrophere\">";
echo __("or move your files here");
echo "</span>\n\n                </div>\n            \n            </div>\n\t\t</div>\n\n\n\t<!--box1-->\n\t</div>\n\t<!--box1-->\n\t\n\t\t<br />\n\t\t\t\t\n\t\t<p class=\"align_right\">\n\t\t\t<a class=\"button1 alt1\" id=\"form_create_btn\">\n                <span>";
echo 0 < $creditor_id ? __("btn edit") : __("btn add");
echo "</span>\n            </a>\n\t\t</p>\n\t\t\n\t<!--form-->\n\t</fieldset></form>\n\t<!--form-->\n \n<div id=\"filemanager\" title=\"";
echo __("filemanager");
echo "\"></div>   \n \n";
require_once "views/footer.php";

?>