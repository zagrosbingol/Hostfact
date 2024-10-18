<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "<div id=\"handle_add\" class=\"hide\" title=\"";
echo __("select default handle");
echo "\">\n\n<br />\n<!--form-->\n<form id=\"HandleForm\" name=\"form_handle\" method=\"post\" action=\"#\"><fieldset><legend>";
echo __("select default handle");
echo "</legend>\n<!--form-->\n\n<div id=\"handle_result_box\"></div>\n\n<!--split2-->\n<div class=\"split2\">\n<!--split2-->\n\n\t<!--left-->\n\t<div class=\"left\">\n\t<!--left-->\n\n\t\t<!--box3-->\n\t\t<div class=\"box3\"><h3>";
echo __("handle data");
echo "</h3><div class=\"content\">\n\t\t<!--box3-->\n\t\t\t";
if($from_page == "show") {
    echo "\t\t\t<strong class=\"title\">";
    echo __("handle already in software");
    echo "</strong>\n\t\t\t<input type=\"radio\" id=\"defaulthandletype_radio_new\" name=\"DefaultHandleType\" value=\"new\" checked=\"checked\"/> <label for=\"defaulthandletype_radio_new\">";
    echo __("create a new default handle in software");
    echo "</label><br />\n\t\t\t<input type=\"radio\" id=\"defaulthandletype_radio_existing\" name=\"DefaultHandleType\" value=\"existing\"/> <label for=\"defaulthandletype_radio_existing\">";
    echo __("use an existing handle");
    echo "</label><br />\n\t\t\t<br />\n\t\t\t";
} else {
    echo "\t\t\t<input type=\"hidden\" name=\"DefaultHandleType\" value=\"new\"/>\n\t\t\t";
}
echo "\t\n\t\t\t\n\t\t\t<div class=\"new_default_handle\">\t\t\n\t\t\t\n\t\t\t\t<input name=\"Registrar\" type=\"hidden\" value=\"\" />\n\t\t\t\t<input type=\"hidden\" name=\"Debtor\" value=\"0\"/>\n\t\t\t\t\n\t\t\t\t<div id=\"dialog_handle_registrarhandle\">\n\t\t\t\t\t<strong class=\"title\">";
echo __("registrarhandle");
echo "</strong>\n\t\t\t\t\t<input type=\"radio\" id=\"registrarhandletype_radio_new\" name=\"RegistrarHandleType\" value=\"new\" checked=\"checked\"/> <label for=\"registrarhandletype_radio_new\">";
echo __("concerns a new handle for registrar");
echo "</label><br />\n\t\t\t\t\t<input type=\"radio\" id=\"registrarhandletype_radio_existing\" name=\"RegistrarHandleType\" value=\"existing\"/> <label for=\"registrarhandletype_radio_existing\">";
echo __("concerns an existing handle for registrar");
echo "</label><br />\t\n\t\t\t\t\t\n\t\t\t\t\t<div id=\"dialog_handle_registrarhandle_type\" class=\"hide\">\n\t\t\t\t\t\t<br />\t\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("registrarhandle");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"RegistrarHandle\" value=\"\" ";
$ti = tabindex($ti);
echo "/>\n\t\t\t\t\t</div>\n\t\t\t\t</div>\t\n\t\t\t</div>\n\t\t\t";
if($from_page == "show") {
    echo "\t\t\t<div class=\"existing_default_handle hide\">\t\t\n\t\t\t\t<strong class=\"title\">";
    echo __("use existing handle for registrar");
    echo "</strong>\n\t\t\t\t<select name=\"ExistingHandle\" class=\"text1 size4f\">\n\t\t\t\t\t<option value=\"\">";
    echo __("select a handle");
    echo "</option>\n\t\t\t\t\t";
    foreach ($list_registrar_handles_all as $k => $v) {
        if(is_numeric($k)) {
            echo "<option value=\"";
            echo $k;
            echo "\">";
            echo $v["Handle"];
            if($v["Name"]) {
                echo " (" . $v["Name"] . ": " . $v["RegistrarHandle"] . ")";
            }
            echo "</option>";
        }
    }
    echo "\t\t\t\t</select>\n\t\t\t</div>\n\t\t\t";
}
echo "\t\t<!--box3-->\n\t\t</div></div>\n\t\t<!--box3-->\n\n\t\t<!--box3-->\n\t\t<div class=\"box3 new_default_handle\"><h3>";
echo __("contact data");
echo "</h3><div class=\"content\">\n\t\t<!--box3-->\n\t\t\t\n\t\t\t<strong class=\"title\">";
echo __("phonenumber");
echo "</strong>\n\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"PhoneNumber\" value=\"";
echo $company->PhoneNumber;
echo "\" ";
$ti = tabindex($ti);
echo " maxlength=\"25\"/>\n\t\t\t<br /><br />\n\t\t\t\n\t\t\t<strong class=\"title\">";
echo __("faxnumber");
echo "</strong>\n\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"FaxNumber\" value=\"";
echo $company->FaxNumber;
echo "\" ";
$ti = tabindex($ti);
echo " maxlength=\"25\"/>\n\t\t\t<br /><br />\n\t\t\t\n\t\t\t<strong class=\"title\">";
echo __("emailaddress");
echo "</strong>\n\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"EmailAddress\" value=\"";
echo $company->EmailAddress;
echo "\" ";
$ti = tabindex($ti);
echo "/>\n\t\t\t\t\t\t\n\t\t\t\n\t\t<!--box3-->\n\t\t</div></div>\n\t\t<!--box3-->\n\t\t\n\t\n\t<!--left-->\n\t</div>\n\t<!--left-->\n\t\n\t<!--right-->\n\t<div class=\"right\">\n\t<!--right-->\n\t\n\t\t<!--box3-->\n\t\t<div class=\"box3 new_default_handle\"><h3>";
echo __("handle data");
echo "</h3><div class=\"content\">\n\t\t<!--box3-->\n\t\t\t\n\t\t\t<div id=\"dialog_handle_use_other_handle\" class=\"hide\">\n\t\t\t\t<strong class=\"title\">";
echo __("use data from another handle");
echo "<a id=\"dialog_handle_use_other_handle_btn2\" class=\"a1 normalfont smallfont floatr\" style=\"color:#ccc !important;\">";
echo __("do not use data from another handle");
echo "</a></strong>\n\t\t\t\t<select name=\"UseOtherHandle\" class=\"text1 size4f\">\t\n\t\t\t\t</select>\n\t\t\t\t<div id=\"handle_search_use_other\" class=\"ico actionblock find mar2 pointer\">&nbsp;</div>\t\n\t\t\t\t<br /><br />\n\t\t\t</div>\n\t\t\t\n\t\t\t<strong class=\"title\">";
echo __("companyname");
echo " <a id=\"dialog_handle_use_other_handle_btn\" class=\"a1 c1 normalfont smallfont marleft_1 floatr\">";
echo __("use data from another handle");
echo "</a></strong>\n\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"CompanyName\" value=\"";
echo $company->CompanyName;
echo "\" maxlength=\"100\" ";
$ti = tabindex($ti);
echo " />\t\t\t\t\t\t\t\n\t\t\t<br />\n\t\t\t\t\t\t\t\t\t\t\n\t\t\t<div id=\"CompanyName_extra\" ";
if(!$company->CompanyName) {
    echo "class=\"hide\"";
}
echo ">\n\t\t\t\t<br />\n\t\t\t\t<strong class=\"title\">";
echo __("company number");
echo "</strong>\n\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"CompanyNumber\" value=\"";
echo $company->CompanyNumber;
echo "\" ";
$ti = tabindex($ti);
echo " maxlength=\"20\"/>\n\t\t\t\t<br /><br />\n\t\t\t\t\n\t\t\t\t<strong class=\"title\">";
echo __("vat number");
echo "</strong>\n\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"TaxNumber\" value=\"";
echo $company->TaxNumber;
echo "\" ";
$ti = tabindex($ti);
echo " maxlength=\"20\"/>\n\t\t\t\t<span id=\"vat_status\"></span>\t\t\t\t\t\t\t\n\t\t\t\t<br /><br />\n\t\t\t\t\n                ";
if(!empty($array_legaltype)) {
    echo "\t\t\t\t<strong class=\"title\">";
    echo __("legal form");
    echo "</strong>\n\t\t\t\t<select name=\"LegalForm\" class=\"text1 size4\">\n\t\t\t\t";
    foreach ($array_legaltype as $key => $value) {
        echo "\t\t\t\t\t<option value=\"";
        echo $key;
        echo "\">";
        echo $value;
        echo "</option>\n\t\t\t\t";
    }
    echo "\t\t\t\t</select>\n                \n\t\t\t\t<br />\n                ";
}
echo "\t\t\t</div>\n\t\t\t\n\t\t\t<br />\n\n\t\t\t<strong class=\"title\">";
echo __("contact person");
echo "</strong>\n\t\t\t<select name=\"Sex\" class=\"text1 size6\" ";
$ti = tabindex($ti);
echo ">\n\t\t\t";
foreach ($array_sex as $k => $v) {
    echo "\t\t\t\t<option value=\"";
    echo $k;
    echo "\">";
    echo $v;
    echo "</option>\n\t\t\t";
}
echo "\t\t\t</select>\n\t\t\t<input type=\"text\" class=\"text1 size2\" name=\"Initials\" value=\"\" ";
$ti = tabindex($ti);
echo " maxlength=\"25\"/> \n\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"SurName\" value=\"\" ";
$ti = tabindex($ti);
echo " maxlength=\"100\"/>\t\t\t\t\t\t\t\n\t\t\t<br /><br />\n\t\t\t\n\t\t\t<strong class=\"title\">";
echo __("address");
echo "</strong>\n\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"Address\" value=\"";
echo $company->Address;
echo "\" ";
$ti = tabindex($ti);
echo " maxlength=\"100\"/>\n\t\t\t";
if(IS_INTERNATIONAL) {
    echo "<br /><input type=\"text\" class=\"text1 size1 marginT2\" name=\"Address2\" value=\"";
    echo $company->Address2;
    echo "\" ";
    $ti = tabindex($ti);
    echo " maxlength=\"100\"/>";
}
echo "\t\t\t\t\t\t\t\t\n\t\t\t<br /><br />\n\t\t\t\n\t\t\t<strong class=\"title\">";
echo __("zipcode and city");
echo "</strong>\n\t\t\t<input type=\"text\" class=\"text1 size2\" name=\"ZipCode\" value=\"";
echo $company->ZipCode;
echo "\" ";
$ti = tabindex($ti);
echo " maxlength=\"10\"/> <input type=\"text\" class=\"text1 size1\" name=\"City\" value=\"";
echo $company->City;
echo "\" ";
$ti = tabindex($ti);
echo " maxlength=\"100\"/>\t\t\t\t\t\t\t\n\t\t\t<br /><br />\n\t\t\t\n\t\t\t";
if(IS_INTERNATIONAL) {
    echo "\t\t\t\t<strong class=\"title\">";
    echo __("state");
    echo "</strong>\n\t\t\t\t<input type=\"text\" class=\"text1 size1 ";
    if(isset($array_states[$company->Country])) {
        echo "hide";
    }
    echo "\" name=\"State\" value=\"";
    if(!isset($array_states[$company->Country])) {
        echo $company->StateName;
    }
    echo "\" ";
    $ti = tabindex($ti);
    echo " maxlength=\"100\"/>\n\t\t\t\t<select class=\"text1 size4f ";
    if(!isset($array_states[$company->Country])) {
        echo "hide";
    }
    echo "\" name=\"StateCode\" ";
    $ti = tabindex($ti);
    echo ">\n\t\t\t\t\t<option value=\"\">";
    echo __("make your choice");
    echo "</option>\n\t\t\t\t";
    if(isset($array_states[$company->Country])) {
        foreach ($array_states[$company->Country] as $key => $value) {
            echo "\t\t\t\t\t<option value=\"";
            echo $key;
            echo "\" ";
            if($company->State == $key) {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo $value;
            echo "</option>\n\t\t\t\t";
        }
    }
    echo "\t\t\t\t</select>\n\t\t\t\t<br /><br />\n\t\t\t\t";
}
echo "\n\t\t\t\n\t\t\t<strong class=\"title\">";
echo __("country");
echo "</strong>\n\t\t\t<select class=\"text1 size4\" name=\"Country\" ";
$ti = tabindex($ti);
echo ">\n\t\t\t";
foreach ($array_country as $key => $value) {
    echo "\t\t\t\t<option value=\"";
    echo $key;
    echo "\" ";
    if($company->Country == $key) {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo $value;
    echo "</option>\n\t\t\t";
}
echo "\t\t\t</select>\n\n\t\t<!--box3-->\n\t\t</div></div>\n\t\t<!--box3-->\n\t\t\n\t\t";
if(0 < count($handle->customfields_list)) {
    echo "\t\t\t\n\t\t\t<br />\n\t\t\t\n\t\t\t<!--box3-->\n\t\t\t<div class=\"box3\"><h3>";
    echo __("custom debtor fields h2");
    echo "</h3><div class=\"content\">\n\t\t\t<!--box3-->\n\t\t\t\n\t\t\t";
    foreach ($handle->customfields_list as $k => $custom_field) {
        $custom_value = isset($handle->customvalues[$custom_field["FieldCode"]]) ? $handle->customvalues[$custom_field["FieldCode"]] : NULL;
        echo "\t\t\t\t<strong class=\"title\">";
        echo htmlspecialchars($custom_field["LabelTitle"]);
        echo "</strong>\n\t\t\t\t";
        echo show_custom_input_field($custom_field, $custom_value);
        echo "\t\t\t\t";
        if($k + 1 != count($handle->customfields_list)) {
            echo "<br /><br />";
        }
    }
    echo "\t\t\t\t\n\t\t\t<!--box3-->\n\t\t\t</div></div>\n\t\t\t<!--box3-->\n\t\t\t";
}
echo "\t\n\t\n\t<!--right-->\n\t</div>\n\t<!--right-->\n\t\n<!--split2-->\n</div>\n<!--split2-->\n\n<p class=\"floatl\" style=\"height:30px;line-height:30px;\"><a id=\"handle_add_btn\" class=\"button1 alt1 float_left\" ><span>";
echo __("create handle");
echo "</span></a>\n\t<span id=\"handle_add_loader\" class=\"hide\" style=\"margin-left: 10px;\">\n\t\t<img src=\"images/icon_circle_loader_grey.gif\" alt=\"laden\" class=\"ico inline\" style=\"margin-bottom:-3px;\" />&nbsp;&nbsp;\n\t\t<span class=\"loading_grey\">";
echo __("please wait while we create the handle at registrar");
echo " <span></span>.</span>&nbsp;&nbsp;\n\t</span>\n</p>\n<p class=\"floatr\" style=\"height:30px;line-height:30px;\"><a class=\"a1 c1 float_right\" onclick=\"\$('#handle_add').dialog('close');\"><span>";
echo __("cancel");
echo "</span></a></p>\n\n<!--form-->\n</fieldset></form>\n<!--form-->\n\n</div>";

?>