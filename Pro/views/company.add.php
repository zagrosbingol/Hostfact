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
echo isset($message) ? $message : "";
echo "\n<form id=\"CompanyForm\" name=\"form_create\" action=\"?\" method=\"post\">\n\t\n\t<input type=\"hidden\" name=\"SynchronizeEmail\" value=\"none\" />\n\t\n\t<!--heading1-->\n\t<div class=\"heading1\">\n\t<!--heading1-->\n\t\n\t\t<h2>";
echo __("company data");
echo "</h2>\n\t\t\n\t<!--heading1-->\n\t</div><hr />\n\t<!--heading1-->\n\n\t<!--box1-->\n\t<div class=\"box2\" id=\"tabs\">\n\t<!--box1-->\n\n\t\t<!--top-->\n\t\t<div class=\"top\">\n\t\t<!--top-->\n\t\t\n\t\t\t<ul class=\"list3\">\n\t\t\t\t<li class=\"on\"><a href=\"#tab-general\">";
echo __("company data");
echo "</a></li>\n\t\t\t</ul>\n\t\t\t\n\t\t<!--top-->\n\t\t</div>\n\t\t<!--top-->\n\t\t\n\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-general\">\n\t\t<!--content-->\n\t\t\t\n\t\t\t<!--split2-->\n\t\t\t<div class=\"split2\">\n\t\t\t<!--split2-->\n\t\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t<div class=\"left\">\n\t\t\t\t<!--left-->\n\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("company data");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t<input type=\"hidden\" name=\"currentCompanyName\" value=\"";
if($company->CompanyName) {
    echo $company->CompanyName;
}
echo "\" />\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("companyname");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"CompanyName\" value=\"";
if($company->CompanyName) {
    echo $company->CompanyName;
}
echo "\"  ";
$ti = tabindex($ti);
echo " />\t\t\t\t\t\t\t\n\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<div id=\"CompanyName_extra\" ";
if(!$company->CompanyName) {
    echo "class=\"hide\"";
}
echo ">\n\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("company number");
echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"CompanyNumber\" value=\"";
echo $company->CompanyNumber;
echo "\" ";
$ti = tabindex($ti);
echo " />\n\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("vat number");
echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"TaxNumber\" value=\"";
echo $company->TaxNumber;
echo "\" ";
$ti = tabindex($ti);
echo " />\n\t\t\t\t\t\t\t<span id=\"vat_status\"></span>\n\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("address");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" name=\"Address\" value=\"";
if($company->Address) {
    echo $company->Address;
}
echo "\" class=\"text1 size1\" ";
$ti = tabindex($ti);
echo "  />\n\t\t\t\t\t\t";
if(IS_INTERNATIONAL) {
    echo "<br /><input type=\"text\" class=\"text1 size1 marginT2\" name=\"Address2\" value=\"";
    echo $company->Address2;
    echo "\" ";
    $ti = tabindex($ti);
    echo " maxlength=\"100\"/>";
}
echo "\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("zipcode and city");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" name=\"ZipCode\" value=\"";
if($company->ZipCode) {
    echo $company->ZipCode;
}
echo "\" class=\"text1 size2\" ";
$ti = tabindex($ti);
echo "  />\n\t\t\t\t\t\t<input type=\"text\" name=\"City\" value=\"";
if($company->City) {
    echo $company->City;
}
echo "\" class=\"text1 size1\" ";
$ti = tabindex($ti);
echo "  />\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t";
if(IS_INTERNATIONAL) {
    echo "\t\t\t\t\t\t\t<strong class=\"title\">";
    echo __("state");
    echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1 ";
    if(isset($array_states[$company->Country])) {
        echo "hide";
    }
    echo "\" name=\"State\" value=\"";
    echo $company->StateName;
    echo "\" ";
    $ti = tabindex($ti);
    echo " maxlength=\"100\"/>\n\t\t\t\t\t\t\t<select class=\"text1 size4f ";
    if(!isset($array_states[$company->Country])) {
        echo "hide";
    }
    echo "\" name=\"StateCode\" ";
    $ti = tabindex($ti);
    echo ">\n\t\t\t\t\t\t\t\t<option value=\"\">";
    echo __("make your choice");
    echo "</option>\n\t\t\t\t\t\t\t";
    if(isset($array_states[$company->Country])) {
        foreach ($array_states[$company->Country] as $key => $value) {
            echo "\t\t\t\t\t\t\t\t<option value=\"";
            echo $key;
            echo "\" ";
            if($company->State == $key) {
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
echo "</strong>\n\t\t\t\t\t\t<select name=\"Country\" class=\"text1 size4f\">\n\t\t\t            ";
foreach ($array_country as $key => $value) {
    echo "\t\t\t\t\t\t\t<option value=\"";
    echo $key;
    echo "\" ";
    if($company->Country == $key) {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo $value;
    echo "</option>\n\t\t\t\t\t\t";
}
echo "\t\t\t            </select>\n\t\t\t            <br /><br />\n\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("contact data");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("phonenumber");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"PhoneNumber\" value=\"";
echo $company->PhoneNumber;
echo "\" ";
$ti = tabindex($ti);
echo " />\t\t\t\t\t\t\t\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("faxnumber");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"FaxNumber\" value=\"";
echo $company->FaxNumber;
echo "\" ";
$ti = tabindex($ti);
echo " />\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("mobilenumber");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"MobileNumber\" value=\"";
echo $company->MobileNumber;
echo "\" ";
$ti = tabindex($ti);
echo " />\t\t\t\t\t\t\t\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<input type=\"hidden\" name=\"currentEmailAddress\" value=\"";
echo $company->EmailAddress;
echo "\" />\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("emailaddress");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"EmailAddress\" value=\"";
echo $company->EmailAddress;
echo "\" maxlength=\"255\"  ";
$ti = tabindex($ti);
echo "/>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("website");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"Website\" value=\"";
echo $company->Website;
echo "\" ";
$ti = tabindex($ti);
echo "/>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t</div>\n\t\t\t\t<!--left-->\n\t\t\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t<div class=\"right\">\n\t\t\t\t<!--right-->\n\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("bankaccount data");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("account number");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"AccountNumber\" value=\"";
echo $company->AccountNumber;
echo "\" ";
$ti = tabindex($ti);
echo "/>\t\t\t\t\t\t\t\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("account name");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"AccountName\" value=\"";
echo $company->AccountName;
echo "\" ";
$ti = tabindex($ti);
echo "/>\t\t\t\t\t\t\t\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("bank");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"AccountBank\" value=\"";
echo $company->AccountBank;
echo "\" ";
$ti = tabindex($ti);
echo "/>\t\t\t\t\t\t\t\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("bank city");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"AccountCity\" value=\"";
echo $company->AccountCity;
echo "\" ";
$ti = tabindex($ti);
echo "/>\t\t\t\t\t\t\t\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("bic");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"AccountBIC\" value=\"";
echo $company->AccountBIC;
echo "\" ";
$ti = tabindex($ti);
echo "/>\n\t\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t</div>\n\t\t\t\t<!--right-->\n\t\t\t\t\n \t\t\t<!--split2-->\n\t\t\t</div>\n\t\t\t<!--split2-->\n\t\t\t\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\t\t\n\t<!--box1-->\n\t</div>\n\t<!--box1-->\n\t\n\t<br />\n\t\n\t";
if(U_COMPANY_EDIT) {
    echo "\t<p class=\"align_right\">\n\t\t<a class=\"button1 alt1\" id=\"form_save_company_data_btn\">\n\t\t\t<span>";
    echo __("btn edit");
    echo "</span>\n\t\t</a>\n\t</p>\n\t";
}
echo "\t\n<div id=\"sync_company_email\" class=\"hide\" title=\"";
echo __("company sync emailaddress title");
echo "\">\n\t<span id=\"companySyncDescription\"></span><br /><br />\n\t<label><input type=\"radio\" style=\"float: left;\" name=\"sync_company_address\" value=\"all\" ";
if((int) $templateCount === 0) {
    echo "checked=\"checked\"";
}
echo " /> <span style=\"display: inline-block;padding-bottom: 5px;padding-left: 4px;\">";
echo __("yes change all the senders");
echo "</span></label><br />\n\t";
if(0 < $templateCount) {
    echo "\t\t<label><input type=\"radio\" style=\"float: left;\" name=\"sync_company_address\" value=\"replace\" checked=\"checked\" /> <span style=\"display: inline-block;padding-bottom: 5px;padding-left: 4px;\">";
    echo sprintf(htmlspecialchars(__("yes, only change the senders with the emailaddress matches the old")), $templateCount, "<br />" . $company->CompanyName, $company->EmailAddress);
    echo "</span></label><br />\n\t";
}
echo "\t<label><input type=\"radio\" style=\"float: left;\" name=\"sync_company_address\" value=\"none\" /> <span style=\"display: inline-block;padding-left: 4px;\">";
echo __("no none");
echo "</span></label><br />\n\t<br />\n\t<p><a class=\"button1 alt1\" id=\"sync_company_email_btn\"><span>";
echo __("further");
echo "</span></a></p>\n</div>\n\n</form>\n\n";
require_once "views/footer.php";

?>