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
$page_form_title = $pagetype == "edit" ? __("edit handle") : __("add handle");
echo "\n";
echo $message;
echo "\t\n\n<!--form-->\n<form id=\"HandleForm\" name=\"form_create\" method=\"post\" action=\"?page=";
echo $pagetype;
echo "\"><fieldset><legend>";
echo $page_form_title;
echo "</legend>\n<!--form-->\n";
if(0 < $handle_id) {
    echo "\t<input type=\"hidden\" name=\"id\" value=\"";
    echo $handle_id;
    echo "\" />\n";
}
echo "\n\t<!--heading1-->\n\t<div class=\"heading1\">\n\t<!--heading1-->\n\t\n\t\t<h2>";
echo $page_form_title;
echo "</h2>\n\t\t\n\t\t";
if(0 < $handle_id) {
    echo "\t\t\t<p class=\"pos2\"><strong class=\"textsize1 pointer\" id=\"Handle_text\" style=\"line-height: 22px\">";
    echo $handle->Handle;
    echo " <span class=\"ico actionblock arrowdown mar2 pointer\" style=\"margin: 4px 2px 2px 2px; z-index: 1;\">&nbsp;</span></strong><input type=\"text\" class=\"text2 size7 hide\" name=\"Handle\" value=\"";
    echo $handle->Handle;
    echo "\"/></p>\n\t\t";
}
echo "\t\t\n\t<!--heading1-->\n\t</div><hr />\n\t<!--heading1-->\n\t\n\t<!--box1-->\n\t<div class=\"box2\" id=\"tabs\">\n\t<!--box1-->\n\t\n\t\t<!--top-->\n\t\t<div class=\"top\">\n\t\t<!--top-->\n\t\t\n\t\t\t<ul class=\"list3\">\n\t\t\t\t<li class=\"on\"><a href=\"#tab-general\">";
echo __("general");
echo "</a></li>\n\t\t\t</ul>\n\t\t\t\n\t\t\n\t\t<!--top-->\n\t\t</div>\n\t\t<!--top-->\n\n\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-general\">\n\t\t<!--content-->\n\n\t\t\t<!--split2-->\n\t\t\t<div class=\"split2\">\n\t\t\t<!--split2-->\n\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t<div class=\"left\">\n\t\t\t\t<!--left-->\n\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("handle data");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("handle for debtor");
echo "</strong>\n\t\t\t\t\t\t<input type=\"radio\" id=\"debtor_helper_radio_no\" name=\"debtor_helper\" value=\"no\" ";
if($handle->Debtor <= 0) {
    echo "checked=\"checked\"";
}
echo "/> <label for=\"debtor_helper_radio_no\">";
echo __("handle for general purpose");
echo "</label><br />\n\t\t\t\t\t\t<input type=\"radio\" id=\"debtor_helper_radio_yes\" name=\"debtor_helper\" value=\"yes\" ";
if(0 < $handle->Debtor) {
    echo "checked=\"checked\"";
}
echo "/> <label for=\"debtor_helper_radio_yes\">";
echo __("handle for specific debtor");
echo "</label><br />\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<div id=\"debtor_helper_div\" ";
if($handle->Debtor <= 0) {
    echo "class=\"hide\"";
}
echo ">\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("debtor");
echo "</strong>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<input type=\"hidden\" name=\"Debtor\" value=\"";
echo $handle->Debtor;
echo "\" />\n\t\t\t\t\t\t\t";
$selected_name = 0 < $handle->Debtor ? $list_debtors[$handle->Debtor]["DebtorCode"] . " " . ($list_debtors[$handle->Debtor]["CompanyName"] ? $list_debtors[$handle->Debtor]["CompanyName"] : $list_debtors[$handle->Debtor]["SurName"] . ", " . $list_debtors[$handle->Debtor]["Initials"]) : "";
createAutoComplete("debtor", "Debtor", $selected_name, ["class" => "size12"]);
echo "\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<div class=\"add alt1\"><strong class=\"title\">";
echo __("registrar");
echo "\t\t\t\t\t\t\t<span class=\"infopopupright\">\n\t\t\t\t\t\t\t\t<em>";
echo __("more info");
echo "</em>\n\t\t\t\t\t\t\t\t<span class=\"popup\">\n\t\t\t\t\t\t\t\t\t";
echo __("registrar handle info");
echo "\t\t\t\t\t\t\t\t\t<b></b>\n\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t</span></strong>\n\t\t\t\t\t\t<select name=\"Registrar\" class=\"text1 size4\">\n\t\t\t\t\t\t\t<option value=\"\">";
echo __("select a registrar");
echo "</option>\n\t\t\t\t\t\t\t";
foreach ($list_domain_registrars as $key => $value) {
    if(is_numeric($key)) {
        echo "\t\t\t\t\t\t\t<option value=\"";
        echo $key;
        echo "\" ";
        if($key == $handle->Registrar) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo $value["Name"];
        echo "</option>\n\t\t\t\t\t\t\t";
    }
}
echo "\t\t\t\t\t\t</select>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<div id=\"handle_registrarhandle\" ";
if($handle->Registrar <= 0) {
    echo "class=\"hide\"";
}
echo ">\n\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<input type=\"radio\" id=\"registrarhandletype_radio_new\" name=\"RegistrarHandleType\" value=\"new\" ";
if(!$handle->RegistrarHandle) {
    echo "checked=\"checked\"";
}
echo "/> <label for=\"registrarhandletype_radio_new\">";
echo __("handle is a new handle");
echo "</label><br />\n\t\t\t\t\t\t\t<input type=\"radio\" id=\"registrarhandletype_radio_existing\" name=\"RegistrarHandleType\" value=\"existing\" ";
if($handle->RegistrarHandle) {
    echo "checked=\"checked\"";
}
echo "/> <label for=\"registrarhandletype_radio_existing\">";
echo __("handle already exists at registrar");
echo "</label><br />\t\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<div id=\"handle_registrarhandle_type\" ";
if(!$handle->RegistrarHandle) {
    echo "class=\"hide\"";
}
echo ">\n\t\t\t\t\t\t\t\t<br />\t\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("registrarhandle");
echo "</strong>\n\t\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"RegistrarHandle\" value=\"";
echo $handle->RegistrarHandle;
echo "\" ";
$ti = tabindex($ti);
echo "/>\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t";
if(0 < $handle_id) {
    echo "\t\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\t<input type=\"checkbox\" id=\"update_handle_at_registrar_yes\" name=\"update_handle_at_registrar\" value=\"yes\" ";
    if($handle->RegistrarHandle) {
        echo "checked=\"checked\"";
    }
    echo " /> <label for=\"update_handle_at_registrar_yes\">";
    echo __("update handle at registrar");
    echo "</label>\n\t\t\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t</div>\t\n\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("contact data");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("phonenumber");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"PhoneNumber\" value=\"";
echo $handle->PhoneNumber;
echo "\" ";
$ti = tabindex($ti);
echo " maxlength=\"25\"/>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("faxnumber");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"FaxNumber\" value=\"";
echo $handle->FaxNumber;
echo "\" ";
$ti = tabindex($ti);
echo " maxlength=\"25\"/>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("emailaddress");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"EmailAddress\" value=\"";
echo $handle->EmailAddress;
echo "\" ";
$ti = tabindex($ti);
echo "/>\n\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t</div>\n\t\t\t\t<!--left-->\n\n\t\t\t\t<!--right-->\n\t\t\t\t<div class=\"right\">\n\t\t\t\t<!--right-->\n\t\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
echo __("handle data");
echo "</h3><div class=\"content\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\t";
if($handle->Identifier <= 0) {
    echo "\t\t\t\t\t<div id=\"handle_use_other_handle\" class=\"hide\">\n\t\t\t\t\t\t<strong class=\"title\">";
    echo __("use data from handle");
    echo "<a id=\"handle_use_other_handle_btn2\" class=\"a1 normalfont smallfont floatr\" style=\"color:#ccc !important;\">";
    echo __("do not use data from other handle");
    echo "</a></strong>\n\t\t\t\t\t\t<select name=\"UseOtherHandle\" class=\"text1 size4f\">\t\n\t\t\t\t\t\t</select>\n\t\t\t\t\t\t<div id=\"handle_search_use_other\" class=\"ico actionblock find mar2 pointer\">&nbsp;</div>\t\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t</div>\n\t\t\t\t\t";
}
echo "\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title\">";
echo __("companyname");
echo " ";
if($handle->Identifier <= 0) {
    echo "<a id=\"handle_use_other_handle_btn\" class=\"a1 c1 normalfont smallfont marleft_1 floatr\">";
    echo __("use data from other handle");
    echo "</a>";
}
echo "</strong>\n\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"CompanyName\" value=\"";
echo $handle->CompanyName;
echo "\" maxlength=\"100\" ";
$ti = tabindex($ti);
echo " />\t\t\t\t\t\t\t\n\t\t\t\t\t<br />\n\t\t\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t<div id=\"CompanyName_extra\" ";
if(!$handle->CompanyName) {
    echo "class=\"hide\"";
}
echo ">\n\t\t\t\t\t\t<br />\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("company number");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"CompanyNumber\" value=\"";
echo $handle->CompanyNumber;
echo "\" ";
$ti = tabindex($ti);
echo " maxlength=\"20\"/>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("vat number");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"TaxNumber\" value=\"";
echo $handle->TaxNumber;
echo "\" ";
$ti = tabindex($ti);
echo " maxlength=\"20\"/>\n\t\t\t\t\t\t<span id=\"vat_status\"></span>\t\t\t\t\t\t\t\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n                        ";
if(!empty($array_legaltype)) {
    echo "\t\t\t\t\t\t<strong class=\"title\">";
    echo __("legal form");
    echo "</strong>\n\t\t\t\t\t\t<select name=\"LegalForm\" class=\"text1 size4\">\n\t\t\t\t\t\t";
    foreach ($array_legaltype as $key => $value) {
        echo "\t\t\t\t\t\t\t<option value=\"";
        echo $key;
        echo "\" ";
        if($handle->LegalForm == $key) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo $value;
        echo "</option>\n\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t</select>\n\t\t\n\t\t\t\t\t\t<br />\n                        ";
}
echo "\t\t\t\t\t</div>\n\t\t\t\t\t\n\t\t\t\t\t<br />\n\t\t\n\t\t\t\t\t<strong class=\"title\">";
echo __("contact person");
echo "</strong>\n\t\t\t\t\t<select name=\"Sex\" class=\"text1 size6\" ";
$ti = tabindex($ti);
echo ">\n\t\t\t\t\t";
foreach ($array_sex as $k => $v) {
    echo "\t\t\t\t\t\t<option value=\"";
    echo $k;
    echo "\" ";
    if($handle->Sex == $k) {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo $v;
    echo "</option>\n\t\t\t\t\t";
}
echo "\t\t\t\t\t</select>\n\t\t\t\t\t<input type=\"text\" class=\"text1 size2\" name=\"Initials\" value=\"";
echo $handle->Initials;
echo "\" ";
$ti = tabindex($ti);
echo " maxlength=\"25\"/> \n\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"SurName\" value=\"";
echo $handle->SurName;
echo "\" ";
$ti = tabindex($ti);
echo " maxlength=\"100\"/>\t\t\t\t\t\t\t\n\t\t\t\t\t<br /><br />\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title\">";
echo __("address");
echo "</strong>\n\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"Address\" value=\"";
echo $handle->Address;
echo "\" ";
$ti = tabindex($ti);
echo " maxlength=\"100\"/>\n\t\t\t\t\t";
if(IS_INTERNATIONAL) {
    echo "<br /><input type=\"text\" class=\"text1 size1 marginT2\" name=\"Address2\" value=\"";
    echo $handle->Address2;
    echo "\" ";
    $ti = tabindex($ti);
    echo " maxlength=\"100\"/>";
}
echo "\t\t\t\t\t\t\n\t\t\t\t\t<br /><br />\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title\">";
echo __("zipcode and city");
echo "</strong>\n\t\t\t\t\t<input type=\"text\" class=\"text1 size2\" name=\"ZipCode\" value=\"";
echo $handle->ZipCode;
echo "\" ";
$ti = tabindex($ti);
echo " maxlength=\"10\"/> <input type=\"text\" class=\"text1 size1\" name=\"City\" value=\"";
echo $handle->City;
echo "\" ";
$ti = tabindex($ti);
echo " maxlength=\"100\"/>\t\t\t\t\t\t\t\n\t\t\t\t\t<br /><br />\n\t\t\t\t\t\n\t\t\t\t\t";
if(IS_INTERNATIONAL) {
    echo "\t\t\t\t\t\t<strong class=\"title\">";
    echo __("state");
    echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1 ";
    if(isset($array_states[$handle->Country])) {
        echo "hide";
    }
    echo "\" name=\"State\" value=\"";
    if(!isset($array_states[$handle->Country])) {
        echo $handle->StateName;
    }
    echo "\" ";
    $ti = tabindex($ti);
    echo " maxlength=\"100\"/>\n\t\t\t\t\t\t<select class=\"text1 size4f ";
    if(!isset($array_states[$handle->Country])) {
        echo "hide";
    }
    echo "\" name=\"StateCode\" ";
    $ti = tabindex($ti);
    echo ">\n\t\t\t\t\t\t\t<option value=\"\">";
    echo __("make your choice");
    echo "</option>\n\t\t\t\t\t\t";
    if(isset($array_states[$handle->Country])) {
        foreach ($array_states[$handle->Country] as $key => $value) {
            echo "\t\t\t\t\t\t\t<option value=\"";
            echo $key;
            echo "\" ";
            if($handle->State == $key) {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo $value;
            echo "</option>\n\t\t\t\t\t\t";
        }
    }
    echo "\t\t\t\t\t\t</select>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title\">";
echo __("country");
echo "</strong>\n\t\t\t\t\t<select class=\"text1 size4f\" name=\"Country\" ";
$ti = tabindex($ti);
echo ">\n\t\t\t\t\t";
foreach ($array_country as $key => $value) {
    echo "\t\t\t\t\t\t<option value=\"";
    echo $key;
    echo "\" ";
    if($handle->Country == $key) {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo $value;
    echo "</option>\n\t\t\t\t\t";
}
echo "\t\t\t\t\t</select>\n\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t";
if(0 < count($handle->customfields_list)) {
    echo "\t\t\t\t\t\n\t\t\t\t\t<br />\n\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
    echo __("custom debtor fields h2");
    echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t";
    foreach ($handle->customfields_list as $k => $custom_field) {
        $custom_value = isset($handle->customvalues[$custom_field["FieldCode"]]) ? $handle->customvalues[$custom_field["FieldCode"]] : NULL;
        echo "\t\t\t\t\t\t<strong class=\"title\">";
        echo htmlspecialchars($custom_field["LabelTitle"]);
        echo "</strong>\n\t\t\t\t\t\t";
        echo show_custom_input_field($custom_field, $custom_value);
        echo "\t\t\t\t\t\t";
        if($k + 1 != count($handle->customfields_list)) {
            echo "<br /><br />";
        }
    }
    echo "\t\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t";
}
echo "\t\t\t\n\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t</div>\n\t\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t<!--split2-->\n\t\t\t</div>\n\t\t\t<!--split2-->\n\t\t\t\t\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\n\t<!--box1-->\n\t</div>\n\t<!--box1-->\n\t\n\t\t<br />\n\t\t\t\t\n\t\t<p class=\"align_right\">\n\t\t\t<a class=\"button1 alt1\" id=\"form_create_btn\">\n                <span>";
echo $pagetype == "edit" ? __("btn edit") : __("btn add");
echo "</span>\n            </a>\n\t\t</p>\n\t\t\n\t<!--form-->\n\t</fieldset></form>\n\t<!--form-->\n\n<div id=\"div_for_handlesearch\"></div>\n\n";
require_once "views/footer.php";

?>