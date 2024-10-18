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
echo "\n<div class=\"heading1\">\n\n\t<h2>";
echo __("modification domain") . " " . $ClientareaChange->ReferenceObject->Domain . "." . $ClientareaChange->ReferenceObject->Tld;
echo "</h2>\n\n\t<a href=\"domains.php?page=show&amp;id=";
echo $ClientareaChange->ReferenceObject->Identifier;
echo "\" class=\"a1 c1 floatr\">\n\t\t<span>";
echo __("goto domain");
echo "</span>\n\t</a>\n\n</div>\n<hr />\n\n<div id=\"tabs\" class=\"box2\">\n\n<div class=\"top\">\n\t<ul class=\"list3\">\n\t\t";
$counter = 0;
foreach ($ClientareaChange->Data as $_handle_type => $_handle) {
    echo "\t\t\t<li ";
    echo $counter === 0 ? "class=\"on\"" : "";
    echo ">\n\t\t\t\t<a href=\"#tab-";
    echo strtolower($_handle_type);
    echo "\">\n\t\t\t\t\t";
    echo __("domain " . strtolower($_handle_type) . " handle");
    echo "\t\t\t\t</a>\n\t\t\t</li>\n\t\t\t";
    $counter++;
}
echo "\t</ul>\n</div>\n\n";
foreach ($ClientareaChange->Data as $_handle_type => $_handle_new) {
    echo "<div class=\"content\" id=\"tab-";
    echo strtolower($_handle_type);
    echo "\">\n\n\t<div class=\"split2\">\n\t\t<div class=\"left\">\n\n\t\t<div class=\"box3\">\n\t\t\t<h3>";
    echo __("handle data");
    echo "</h3>\n\t\t\t<div class=\"content\">\n\n\t\t\t\t";
    if($_handle_new->CompanyName) {
        echo "\n\t\t\t\t\t<strong class=\"title2\">";
        echo __("companyname");
        echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
        echo $_handle_new->CompanyName;
        echo "</span>\n\n\t\t\t\t\t<strong class=\"title2\">";
        echo __("legal form");
        echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
        echo $array_legaltype[$_handle_new->LegalForm];
        echo "</span>\n\n\t\t\t\t\t<strong class=\"title2\">";
        echo __("company number");
        echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
        echo $_handle_new->CompanyNumber;
        echo "</span>\n\n\t\t\t\t\t<strong class=\"title2\">";
        echo __("taxnumber");
        echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
        echo $_handle_new->TaxNumber;
        echo "</span>\n\n\t\t\t\t\t";
    }
    echo "\n\t\t\t\t<strong class=\"title2\">";
    echo __("name");
    echo "</strong>\n\t\t\t\t<span class=\"title2_value\">";
    echo settings::getGenderTranslation($_handle_new->Sex) . " " . $_handle_new->Initials . " " . $_handle_new->SurName;
    echo "</span>\n\n\t\t\t\t<strong class=\"title2\">";
    echo __("address");
    echo "</strong>\n\t\t\t\t<span class=\"title2_value\">";
    echo $_handle_new->Address;
    echo "</span>\n\n\t\t\t\t";
    if(IS_INTERNATIONAL) {
        echo "\t\t\t\t\t<strong class=\"title\">&nbsp;</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
        echo $_handle_new->Address2;
        echo "</span>\n\t\t\t\t\t";
    }
    echo "\n\t\t\t\t<strong class=\"title2\">";
    echo __("zipcode and city");
    echo "</strong>\n\t\t\t\t<span class=\"title2_value\">";
    echo $_handle_new->ZipCode . " " . $_handle_new->ZipCode;
    echo "</span>\n\n\t\t\t\t";
    if(IS_INTERNATIONAL) {
        echo "\t\t\t\t\t<strong class=\"title\">";
        echo __("state");
        echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
        echo $array_states[$_handle_new->State];
        echo "</span>\n\t\t\t\t\t";
    }
    echo "\n\t\t\t\t<strong class=\"title2\">";
    echo __("country");
    echo "</strong>\n\t\t\t\t<span class=\"title2_value\">";
    echo $array_country[$_handle_new->Country];
    echo "</span>\n\n\t\t\t</div>\n\t\t</div>\n\n\t\t</div>\n\t\t<div class=\"right\">\n\n\t\t\t<div class=\"box3\">\n\t\t\t\t<h3>";
    echo __("contact data");
    echo "</h3>\n\t\t\t\t<div class=\"content\">\n\n\t\t\t\t\t<strong class=\"title2\">";
    echo __("phonenumber");
    echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
    echo phoneNumberLink($_handle_new->PhoneNumber);
    echo "</span>\n\n\t\t\t\t\t<strong class=\"title2\">";
    echo __("faxnumber");
    echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
    echo $_handle_new->FaxNumber;
    echo "</span>\n\n\t\t\t\t\t<strong class=\"title2\">";
    echo __("emailaddress");
    echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
    echo $_handle_new->EmailAddress;
    echo "</span>\n\n\t\t\t\t</div>\n\t\t\t</div>\n\n\t\t</div>\n\t</div>\n\t</div>\n\n\t";
}
echo "</div>\n\n";
require_once "views/footer.php";

?>