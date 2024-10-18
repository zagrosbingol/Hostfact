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
echo "\n";
if($ClientareaChange->ReferenceObject->Status == 9) {
    echo "\t<div class=\"mark alt1\">\n\t\t<strong>";
    echo __("warning");
    echo "</strong>\n\t\t<br/>\n\t\t<ul>\n\t\t\t<li>";
    echo __("this debtor has been deleted. therefore no changes can be made");
    echo "</li>\n\t\t</ul>\n\t</div>\n\t<br/>\n\t";
}
echo "\n\t<div class=\"heading1\">\n\t\t<h2>";
echo __("modification debtor") . " " . $ClientareaChange->ReferenceObject->DebtorCode;
echo "</h2>\n\n\t\t<a href=\"debtors.php?page=show&amp;id=";
echo $ClientareaChange->ReferenceObject->Identifier;
echo "\" class=\"a1 c1 floatr\">\n\t\t\t<span>";
echo __("goto debtor");
echo "</span>\n\t\t</a>\n\t</div>\n\t<hr />\n\n\t<form name=\"clientarea_change_form\" method=\"post\" action=\"clientareachanges.php?page=";
echo $form_action;
echo "&amp;id=";
echo $ClientareaChange->id;
echo "\">\n\n\t\t<div id=\"tabs\" class=\"box2\">\n\t\t\t<div class=\"top\">\n\t\t\t\t<ul class=\"list3\">\n\t\t\t\t\t<li class=\"on\"><a href=\"#tab-general\">";
echo __("details debtor");
echo "</a></li>\n\t\t\t\t</ul>\n\t\t\t</div>\n\n\t\t\t";
$debtor_new = $ClientareaChange->Data;
$debtor_current = $ClientareaChange->ReferenceObject;
echo "\n\t\t\t<div class=\"content\">\n\t\t\t\t<div class=\"split2\">\n\n\t\t\t\t\t<div class=\"left\">\n\n\t\t\t\t\t";
if(isset($debtor_new->CompanyName)) {
    echo "\n\t\t\t\t\t\t<div class=\"box3\">\n\t\t\t\t\t\t\t<h3>";
    echo __("debtor data");
    echo "</h3>\n\t\t\t\t\t\t\t<div class=\"content\">\n\n\t\t\t\t\t\t\t\t<strong class=\"title2\">\n\t\t\t\t\t\t\t\t\t";
    echo __("companyname");
    echo "\t\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t\t<span class=\"title2_value\">";
    echo $debtor_new->CompanyName;
    echo "</span>\n\n\t\t\t\t\t\t\t\t<strong class=\"title2\">\n\t\t\t\t\t\t\t\t\t";
    echo __("company number");
    echo "\t\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t\t<span class=\"title2_value\">";
    echo $debtor_new->CompanyNumber;
    echo "</span>\n\n\t\t\t\t\t\t\t\t<strong class=\"title2\">\n\t\t\t\t\t\t\t\t\t";
    echo __("taxnumber");
    echo "\t\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t\t<span class=\"title2_value\">";
    echo $debtor_new->TaxNumber;
    echo "</span>\n\n\t\t\t\t\t\t\t\t<strong class=\"title2\">\n\t\t\t\t\t\t\t\t\t";
    echo __("legal form");
    echo "\t\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t\t<span class=\"title2_value\">";
    echo $array_legaltype[$debtor_new->LegalForm];
    echo "</span>\n\n\t\t\t\t\t\t\t\t<strong class=\"title2\">\n\t\t\t\t\t\t\t\t\t";
    echo __("contact person");
    echo "\t\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t\t<span class=\"title2_value\">";
    echo settings::getGenderTranslation($debtor_new->Sex) . " " . $debtor_new->Initials . " " . $debtor_new->SurName;
    echo "</span>\n\n\t\t\t\t\t\t\t\t<strong class=\"title2\">\n\t\t\t\t\t\t\t\t\t";
    echo __("address");
    echo "\t\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t\t<span class=\"title2_value\">\n\t\t\t\t\t\t\t\t\t";
    echo $debtor_new->Address;
    echo "\t\t\t\t\t\t\t\t\t";
    if(IS_INTERNATIONAL) {
        echo "<br />" . $debtor_new->Address2;
    }
    echo "\t\t\t\t\t\t\t\t</span>\n\n\t\t\t\t\t\t\t\t<strong class=\"title2\">\n\t\t\t\t\t\t\t\t\t";
    echo __("zipcode and city");
    echo "\t\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t\t<span class=\"title2_value\">";
    echo $debtor_new->ZipCode . " " . $debtor_new->City;
    echo "</span>\n\n\t\t\t\t\t\t\t\t";
    if(IS_INTERNATIONAL) {
        echo "\t\t\t\t\t\t\t\t\t\t<strong class=\"title2\">\n\t\t\t\t\t\t\t\t\t\t\t";
        echo __("state");
        echo "\t\t\t\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t\t\t\t<span class=\"title2_value\">\n\t\t\t\t\t\t\t\t\t\t\t";
        if(isset($array_states[$debtor_new->Country][$debtor_new->StateCode])) {
            echo $array_states[$debtor_new->Country][$debtor_new->StateCode];
        } else {
            echo $debtor_new->State;
        }
        echo "\t\t\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t\t\t";
    }
    echo "\n\t\t\t\t\t\t\t\t<strong class=\"title2\">\n\t\t\t\t\t\t\t\t\t";
    echo __("country");
    echo "\t\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t\t<span class=\"title2_value\">";
    echo $array_country[$debtor_new->Country];
    echo "</span>\n\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t</div>\n\n\t\t\t\t\t\t<div class=\"box3\">\n\t\t\t\t\t\t\t<h3>";
    echo __("contact data");
    echo "</h3>\n\t\t\t\t\t\t\t<div class=\"content\">\n\n\t\t\t\t\t\t\t\t<strong class=\"title2\">\n\t\t\t\t\t\t\t\t\t";
    echo __("emailaddress");
    echo "\t\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t\t<span class=\"title2_value\">";
    echo check_email_address($debtor_new->EmailAddress, "convert", ", ");
    echo "</span>\n\n\t\t\t\t\t\t\t\t<strong class=\"title2\">\n\t\t\t\t\t\t\t\t\t";
    echo __("extra emailaddress");
    echo "\t\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t\t<span class=\"title2_value\">";
    echo check_email_address($debtor_new->SecondEmailAddress, "convert", ", ");
    echo "</span>\n\n\t\t\t\t\t\t\t\t<strong class=\"title2\">\n\t\t\t\t\t\t\t\t\t";
    echo __("phonenumber");
    echo "\t\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t\t<span class=\"title2_value\">";
    echo phoneNumberLink($debtor_new->PhoneNumber);
    echo "</span>\n\n\t\t\t\t\t\t\t\t<strong class=\"title2\">\n\t\t\t\t\t\t\t\t\t";
    echo __("mobilenumber");
    echo "\t\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t\t<span class=\"title2_value\">";
    echo phoneNumberLink($debtor_new->MobileNumber);
    echo "</span>\n\n\t\t\t\t\t\t\t\t<strong class=\"title2\">\n\t\t\t\t\t\t\t\t\t";
    echo __("faxnumber");
    echo "\t\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t\t<span class=\"title2_value\">";
    echo $debtor_new->FaxNumber;
    echo "</span>\n\n\t\t\t\t\t\t\t\t<strong class=\"title2\">\n\t\t\t\t\t\t\t\t\t";
    echo __("receive mailings");
    echo "\t\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t\t<span class=\"title2_value\">";
    echo $array_mailingoptin[$debtor_new->Mailing];
    echo "</span>\n\n\t\t\t\t\t\t\t</div>\n\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\t";
} elseif(isset($debtor_new->InvoiceCompanyName)) {
    if($debtor_new->InvoiceCompanyName || $debtor_new->InvoiceInitials || $debtor_new->InvoiceSurName || $debtor_new->InvoiceAddress || $debtor_new->InvoiceAddress2 || $debtor_new->InvoiceZipCode || $debtor_new->InvoiceCity || $debtor_new->InvoiceCountry && $debtor_new->InvoiceCountry != $debtor_current->Country || $debtor_new->InvoiceEmailAddress) {
        echo "\t\t\t\t\t\t\t<div class=\"box3\">\n\t\t\t\t\t\t\t\t<h3>";
        echo __("invoice information");
        echo "</h3>\n\t\t\t\t\t\t\t\t<div class=\"content\">\n\n\t\t\t\t\t\t\t\t\t<strong class=\"title2\">\n\t\t\t\t\t\t\t\t\t\t";
        echo __("companyname");
        echo "\t\t\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t\t\t<span class=\"title2_value\">";
        echo $debtor_new->InvoiceCompanyName;
        echo "</span>\n\n\t\t\t\t\t\t\t\t\t<strong class=\"title2\">\n\t\t\t\t\t\t\t\t\t\t";
        echo __("company number");
        echo "\t\t\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t\t\t<span class=\"title2_value\">";
        echo $debtor_new->InvoiceCompanyNumber;
        echo "</span>\n\n\t\t\t\t\t\t\t\t\t<strong class=\"title2\">\n\t\t\t\t\t\t\t\t\t\t";
        echo __("taxnumber");
        echo "\t\t\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t\t\t<span class=\"title2_value\">";
        echo $debtor_new->InvoiceTaxNumber;
        echo "</span>\n\n\t\t\t\t\t\t\t\t\t<strong class=\"title2\">\n\t\t\t\t\t\t\t\t\t\t";
        echo __("contact person");
        echo "\t\t\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t\t\t<span class=\"title2_value\">";
        echo settings::getGenderTranslation($debtor_new->InvoiceSex) . " " . $debtor_new->InvoiceInitials . " " . $debtor_new->InvoiceSurName;
        echo "</span>\n\n\t\t\t\t\t\t\t\t\t<strong class=\"title2\">\n\t\t\t\t\t\t\t\t\t\t";
        echo __("address");
        echo "\t\t\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t\t\t<span class=\"title2_value\">\n\t\t\t\t\t\t\t\t\t\t";
        echo $debtor_new->InvoiceAddress;
        echo "\t\t\t\t\t\t\t\t\t\t";
        if(IS_INTERNATIONAL) {
            echo "<br />" . $debtor_new->InvoiceAddress2;
        }
        echo "\t\t\t\t\t\t\t\t\t</span>\n\n\t\t\t\t\t\t\t\t\t<strong class=\"title2\">\n\t\t\t\t\t\t\t\t\t\t";
        echo __("zipcode and city");
        echo "\t\t\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t\t\t<span class=\"title2_value\">";
        echo $debtor_new->InvoiceZipCode . " " . $debtor_new->InvoiceCity;
        echo "</span>\n\n\t\t\t\t\t\t\t\t\t";
        if(IS_INTERNATIONAL) {
            echo "\t\t\t\t\t\t\t\t\t\t<strong class=\"title2\">\n\t\t\t\t\t\t\t\t\t\t\t";
            echo __("state");
            echo "\t\t\t\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t\t\t\t<span class=\"title2_value\">\n\t\t\t\t\t\t\t\t\t\t\t\t";
            if(isset($array_states[$debtor_new->InvoiceCountry][$debtor_new->InvoiceStateCode])) {
                echo $array_states[$debtor_new->InvoiceCountry][$debtor_new->InvoiceStateCode];
            } else {
                echo $debtor_new->InvoiceState;
            }
            echo "\t\t\t\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t\t\t";
        }
        echo "\n\t\t\t\t\t\t\t\t\t<strong class=\"title2\">\n\t\t\t\t\t\t\t\t\t\t";
        echo __("country");
        echo "\t\t\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t\t\t<span class=\"title2_value\">";
        echo $array_country[$debtor_new->InvoiceCountry];
        echo "</span>\n\n\t\t\t\t\t\t\t\t\t<strong class=\"title2\">\n\t\t\t\t\t\t\t\t\t\t";
        echo __("emailaddress");
        echo "\t\t\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t\t\t<span class=\"title2_value\">";
        echo check_email_address($debtor_new->InvoiceEmailAddress, "convert", ", ");
        echo "</span>\n\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t";
    } else {
        echo "\t\t\t\t\t\t\t<div class=\"box3\">\n\t\t\t\t\t\t\t\t<h3>";
        echo __("invoice information");
        echo "</h3>\n\t\t\t\t\t\t\t\t<div class=\"content\">\n\t\t\t\t\t\t\t\t\t";
        echo "<p>" . __("billing data uses general data") . "<p>";
        echo "\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t";
    }
} elseif(isset($debtor_new->AccountNumber)) {
    echo "\t\t\t\t\t\t<div class=\"box3\">\n\t\t\t\t\t\t\t<h3>";
    echo __("bankaccount data");
    echo "</h3>\n\t\t\t\t\t\t\t<div class=\"content\">\n\n\t\t\t\t\t\t\t\t<strong class=\"title2\">\n\t\t\t\t\t\t\t\t\t";
    echo __("authorization");
    echo "\t\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t\t<span class=\"title2_value\">";
    echo $array_authorisation[$debtor_new->InvoiceAuthorisation];
    echo "</span>\n\n\t\t\t\t\t\t\t\t<strong class=\"title2\">\n\t\t\t\t\t\t\t\t\t";
    echo __("account number");
    echo "\t\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t\t<span class=\"title2_value\">";
    echo $debtor_new->AccountNumber;
    echo "</span>\n\n\t\t\t\t\t\t\t\t<strong class=\"title2\">\n\t\t\t\t\t\t\t\t\t";
    echo __("account name");
    echo "\t\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t\t<span class=\"title2_value\">";
    echo $debtor_new->AccountName;
    echo "</span>\n\n\t\t\t\t\t\t\t\t<strong class=\"title2\">\n\t\t\t\t\t\t\t\t\t";
    echo __("bank");
    echo "\t\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t\t<span class=\"title2_value\">";
    echo $debtor_new->AccountBank;
    echo "</span>\n\n\t\t\t\t\t\t\t\t<strong class=\"title2\">\n\t\t\t\t\t\t\t\t\t";
    echo __("bic");
    echo "\t\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t\t<span class=\"title2_value\">";
    echo $debtor_new->AccountBIC;
    echo "</span>\n\n\t\t\t\t\t\t\t</div>\n\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\t\t\t\t\t\t";
}
echo "\t\t\t\t\t</div>\n\n\t\t\t\t</div>\n\t\t\t</div>\n\n\t\t</div>\n\n\t\t<br />\n\n\t</form>\n\n";
$this->element("clientarea.changes.edit.php");
require_once "views/footer.php";
function getbeforespan($var)
{
    if(trim($var) == "") {
        return "<span class=\"smallfont marleft_1 normalfont\">(" . __("empty before") . ")</span>";
    }
    return "<span class=\"smallfont marleft_1 normalfont\">(" . __("before") . ": " . $var . ")</span>";
}

?>