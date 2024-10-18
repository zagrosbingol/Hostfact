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
$page_form_title = $pagetype == "edit" ? __("edit debtor") : __("add debtor");
echo "\n";
echo $message;
echo "\t\n\n<!--form-->\n<form id=\"DebtorForm\" name=\"form_create\" method=\"post\" action=\"?page=";
echo $pagetype;
echo "\"><fieldset><legend>";
echo $page_form_title;
echo "</legend>\n<!--form-->\n";
if(0 < $debtor_id) {
    echo "\t<input type=\"hidden\" name=\"id\" value=\"";
    echo $debtor_id;
    echo "\" />\n\t<input type=\"hidden\" name=\"SynchronizeEmail\" value=\"no\" />\n\t<input type=\"hidden\" name=\"SynchronizeAuth\" value=\"no\" />\n\t<input type=\"hidden\" name=\"SynchronizeAddress\" value=\"no\" />\n\t<input type=\"hidden\" name=\"SynchronizeHandles\" value=\"no\" />\n";
}
echo "\n\t";
if(isset($_GET["clientareachange"])) {
    echo "<input type=\"hidden\" name=\"ClientareaChange\" value=\"" . htmlspecialchars(esc($_GET["clientareachange"])) . "\" />";
}
echo "\n\t<!--heading1-->\n\t<div class=\"heading1\">\n\t<!--heading1-->\n\t\n\t\t<h2>";
echo $page_form_title;
echo "</h2>\n\t\t<p class=\"pos2\"><strong class=\"textsize1 pointer\" id=\"DebtorCode_text\" style=\"line-height: 22px\">";
echo $debtor->DebtorCode;
echo " <span class=\"ico actionblock arrowdown mar2 pointer\" style=\"margin: 4px 2px 2px 2px; z-index: 1;\">&nbsp;</span></strong><input type=\"text\" class=\"text2 size7 hide\" name=\"DebtorCode\" value=\"";
echo $debtor->DebtorCode;
echo "\" maxlength=\"50\"/></p>\n\t\n\t<!--heading1-->\n\t</div><hr />\n\t<!--heading1-->\n\n\t";
if(isset($clientarea_change) && $clientarea_change === true) {
    echo "\t\t\t<div class=\"setting_help_box\" style=\"background-color: #fffbd4; border:1px solid #dad28d; line-height:18px;\">\n\n\t\t\t\t<div id=\"changed_data\">\n\t\t\t\t\t<strong>";
    echo __("clientarea changes");
    echo "</strong><br>\n\t\t\t\t\t";
    foreach ($current_debtor_data as $_debtor_field => $_change_value) {
        if(isset($debtor->{$_debtor_field})) {
            echo "<div data-field=\"" . $_debtor_field . "\">";
            echo "<span style=\"display: inline-block; min-width: 120px;padding-right: 20px;\">" . __($debtor_field_labels[$_debtor_field]) . ": </span>";
            switch ($_debtor_field) {
                case "Sex":
                case "InvoiceSex":
                    echo "&quot;" . settings::getGenderTranslation($debtor->{$_debtor_field}, true) . "&quot; (" . __("before") . ": &quot;" . settings::getGenderTranslation($_change_value, true) . "&quot;)";
                    break;
                case "Country":
                case "InvoiceCountry":
                    echo "&quot;" . $array_country[$debtor->{$_debtor_field}] . "&quot; (" . __("before") . ": &quot;" . $array_country[$_change_value] . "&quot;)";
                    break;
                case "Mailing":
                    echo "&quot;" . $array_mailingoptin[$debtor->{$_debtor_field}] . "&quot; (" . __("before") . ": &quot;" . $array_mailingoptin[$_change_value] . "&quot;)";
                    break;
                case "State":
                case "InvoiceState":
                    $country = $_debtor_field == "InvoiceState" ? "InvoiceCountry" : "Country";
                    if(isset($array_states[$debtor->{$country}])) {
                        echo "&quot;" . $array_states[$debtor->{$country}][$debtor->{$_debtor_field}] . "&quot; (" . __("before") . ": &quot;" . $array_states[$debtor->{$country}][$_change_value] . "&quot;)";
                    } else {
                        echo "&quot;" . $debtor->{$_debtor_field} . "&quot; (" . __("before") . ": &quot;" . $_change_value . "&quot;)";
                    }
                    break;
                case "LegalForm":
                    echo "&quot;" . $array_legaltype[$debtor->{$_debtor_field}] . "&quot; (" . __("before") . ": &quot;" . $array_legaltype[$_change_value] . "&quot;)";
                    break;
                case "InvoiceAuthorisation":
                    echo "&quot;" . $array_authorisation[$debtor->{$_debtor_field}] . "&quot; (" . __("before") . ": &quot;" . $array_authorisation[$_change_value] . "&quot;)";
                    break;
                default:
                    echo "&quot;" . $debtor->{$_debtor_field} . "&quot; (" . __("before") . ": &quot;" . $_change_value . "&quot;)";
                    echo "</div>";
            }
        }
    }
    echo "\t\t\t\t</div>\n\t\t\t</div>\n\t\t\t<br />\n\n\t\t\t<script type=\"application/javascript\">\n\t\t\t\t\$(function()\n\t\t\t\t{\n\t\t\t\t\t// mark changed fields as modified (yellow background)\n\t\t\t\t\t\$('#changed_data div').each(function()\n\t\t\t\t\t{\n\t\t\t\t\t\tif(\$('input[name=\"' + \$(this).data('field') + '\"]').length > 0)\n\t\t\t\t\t\t{\n\t\t\t\t\t\t\t\$('input[name=\"' + \$(this).data('field') + '\"]').addClass('modified');\n\t\t\t\t\t\t}\n\t\t\t\t\t\telse if(\$('select[name=\"' + \$(this).data('field') + '\"]').length > 0)\n\t\t\t\t\t\t{\n\t\t\t\t\t\t\t\$('select[name=\"' + \$(this).data('field') + '\"]').addClass('modified');\n\t\t\t\t\t\t}\n\t\t\t\t\t\telse if(\$('textarea[name=\"' + \$(this).data('field') + '\"]').length > 0)\n\t\t\t\t\t\t{\n\t\t\t\t\t\t\t\$('textarea[name=\"' + \$(this).data('field') + '\"]').addClass('modified');\n\t\t\t\t\t\t}\n\t\t\t\t\t});\n\t\t\t\t});\n\t\t\t</script>\n\t\t\t";
}
echo "\t\n\t<!--box1-->\n\t<div class=\"box2\" id=\"tabs\">\n\t<!--box1-->\n    \n        <div id=\"dropzone\" class=\"hide\"><div class=\"bg\"></div><span class=\"dropfileshere\">";
echo __("move your file here");
echo "</span></div>\n\t   <input type=\"hidden\" name=\"file_type\" value=\"debtor_files\" />\n\t\n\t\t<!--top-->\n\t\t<div class=\"top\">\n\t\t<!--top-->\n\t\t\n\t\t\t<ul class=\"list3\">\n\t\t\t\t<li class=\"on\"><a href=\"#tab-general\">";
echo __("general");
echo "</a></li>\n\t\t\t\t<li><a href=\"#tab-directdebit\">";
echo __("direct debit and bank data");
echo "</a></li>\n\t\t\t\t<li><a href=\"#tab-groups\">";
echo __("debtorgroups");
echo "</a></li>\n\t\t\t\t<li><a href=\"#tab-comment\">";
echo __("internal note");
echo "</a></li>\n\t\t\t\t<li><a href=\"#tab-settings\">";
echo __("settings");
echo "</a></li>\n                <li><a href=\"#tab-attachments\">";
echo __("attachments");
echo "</a></li>\n\t\t\t</ul>\n\t\t\t\n\t\t<!--top-->\n\t\t</div>\n\t\t<!--top-->\n\n\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-general\">\n\t\t<!--content-->\n\n\t\t\t<!--split2-->\n\t\t\t<div class=\"split2\">\n\t\t\t<!--split2-->\n\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t<div class=\"left\">\n\t\t\t\t<!--left-->\n\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("debtor data");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("companyname");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"CompanyName\" value=\"";
echo $debtor->CompanyName;
echo "\" maxlength=\"100\" ";
$ti = tabindex($ti);
echo " />\n\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t<div id=\"CompanyName_extra\" ";
if(!$debtor->CompanyName) {
    echo "class=\"hide\"";
}
echo ">\n\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("company number");
echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"CompanyNumber\" value=\"";
echo $debtor->CompanyNumber;
echo "\" ";
$ti = tabindex($ti);
echo " maxlength=\"20\"/>\n\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("vat number");
echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"TaxNumber\" value=\"";
echo $debtor->TaxNumber;
echo "\" ";
$ti = tabindex($ti);
echo " maxlength=\"20\"/>\n\t\t\t\t\t\t\t<span id=\"vat_status\"></span>\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\n                            ";
if(!empty($array_legaltype)) {
    echo "\t\t\t\t\t\t\t<strong class=\"title\">";
    echo __("legal form");
    echo "</strong>\n\t\t\t\t\t\t\t<select name=\"LegalForm\" class=\"text1 size14_percentage\">\n\t\t\t\t\t\t\t";
    foreach ($array_legaltype as $key => $value) {
        echo "\t\t\t\t\t\t\t\t<option value=\"";
        echo $key;
        echo "\" ";
        if($debtor->LegalForm == $key) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo $value;
        echo "</option>\n\t\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\t</select>\n\n\t\t\t\t\t\t\t<br />\n                            ";
}
echo "\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<br />\n\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("contact person");
echo "</strong>\n\t\t\t\t\t\t\t<div class=\"inputgroup_size14_percentage\">\n\t\t\t\t\t\t\t\t<select name=\"Sex\" class=\"text1 size16\" ";
$ti = tabindex($ti);
echo ">\n\t\t\t\t\t\t\t\t";
foreach ($array_sex as $k => $v) {
    echo "\t\t\t\t\t\t\t\t\t<option value=\"";
    echo $k;
    echo "\" ";
    if($debtor->Sex == $k) {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo $v;
    echo "</option>\n\t\t\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size16\" name=\"Initials\" value=\"";
echo $debtor->Initials;
echo "\" ";
$ti = tabindex($ti);
echo " maxlength=\"25\"/>\n\t\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size15_calc\" name=\"SurName\" value=\"";
echo $debtor->SurName;
echo "\" ";
$ti = tabindex($ti);
echo " maxlength=\"100\"/>\n\t\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t</div>\n\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("address");
echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"Address\" value=\"";
echo $debtor->Address;
echo "\" ";
$ti = tabindex($ti);
echo " maxlength=\"100\"/>\n\t\t\t\t\t\t\t";
if(IS_INTERNATIONAL) {
    echo "<br /><input type=\"text\" class=\"text1 size14_percentage marginT2\" name=\"Address2\" value=\"";
    echo $debtor->Address2;
    echo "\" ";
    $ti = tabindex($ti);
    echo " maxlength=\"100\"/>";
}
echo "\t\t\t\t\t\t\t<br /><br />\n\n\t\t\t\t\t\t\t<div class=\"inputgroup_size14_percentage\">\n\t\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("zipcode and city");
echo "</strong>\n\t\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size16\" name=\"ZipCode\" value=\"";
echo $debtor->ZipCode;
echo "\" ";
$ti = tabindex($ti);
echo " maxlength=\"10\"/> <input type=\"text\" class=\"text1 size_calc_100-size16\" name=\"City\" value=\"";
echo $debtor->City;
echo "\" ";
$ti = tabindex($ti);
echo " maxlength=\"100\"/>\n\t\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t</div>\n\n\n\t\t\t\t\t\t";
if(IS_INTERNATIONAL) {
    echo "\t\t\t\t\t\t\t<strong class=\"title\">";
    echo __("state");
    echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage ";
    if(isset($array_states[$debtor->Country])) {
        echo "hide";
    }
    echo "\" name=\"State\" value=\"";
    if(!isset($array_states[$debtor->Country])) {
        echo $debtor->StateName;
    }
    echo "\" ";
    $ti = tabindex($ti);
    echo " maxlength=\"100\"/>\n\t\t\t\t\t\t\t<select class=\"text1 size14_percentage ";
    if(!isset($array_states[$debtor->Country])) {
        echo "hide";
    }
    echo "\" name=\"StateCode\" ";
    $ti = tabindex($ti);
    echo ">\n\t\t\t\t\t\t\t\t<option value=\"\">";
    echo __("make your choice");
    echo "</option>\n\t\t\t\t\t\t\t";
    if(isset($array_states[$debtor->Country])) {
        foreach ($array_states[$debtor->Country] as $key => $value) {
            echo "\t\t\t\t\t\t\t\t<option value=\"";
            echo $key;
            echo "\" ";
            if($debtor->State == $key) {
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
echo "</strong>\n\t\t\t\t\t\t<select class=\"text1 size14_percentage\" name=\"Country\" ";
$ti = tabindex($ti);
echo ">\n\t\t\t\t\t\t";
foreach ($array_country as $key => $value) {
    echo "\t\t\t\t\t\t\t<option value=\"";
    echo $key;
    echo "\" ";
    if($debtor->Country == $key) {
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
echo check_email_address($debtor->EmailAddress, "convert", ", ");
echo "\" ";
$ti = tabindex($ti);
echo "/>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("phonenumber");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"PhoneNumber\" value=\"";
echo $debtor->PhoneNumber;
echo "\" ";
$ti = tabindex($ti);
echo " maxlength=\"25\"/>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("mobilenumber");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"MobileNumber\" value=\"";
echo $debtor->MobileNumber;
echo "\" ";
$ti = tabindex($ti);
echo " maxlength=\"25\"/>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("faxnumber");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"FaxNumber\" value=\"";
echo $debtor->FaxNumber;
echo "\" ";
$ti = tabindex($ti);
echo " maxlength=\"25\"/>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("website");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"Website\" value=\"";
echo $debtor->Website;
echo "\" ";
$ti = tabindex($ti);
echo "/>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("would like to receive mailings");
echo "</strong>\n\t\t\t\t\t\t<select class=\"text1 size16\" name=\"Mailing\" ";
$ti = tabindex($ti);
echo ">\n\t\t\t\t\t\t";
foreach ($array_mailingoptin as $key => $value) {
    echo "\t\t\t\t\t\t\t<option value=\"";
    echo $key;
    echo "\" ";
    if($debtor->Mailing == $key) {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo $value;
    echo "</option>\n\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t</select>\t\n\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t</div>\n\t\t\t\t<!--left-->\n\n\t\t\t\t<!--right-->\n\t\t\t\t<div class=\"right\">\n\t\t\t\t<!--right-->\n\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("invoice information");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t\t<input id=\"AbnormalInvoiceData\" name=\"AbnormalInvoiceData\" type=\"checkbox\" class=\"checkbox1\" value=\"on\" ";
if($debtor->InvoiceCompanyName || $debtor->InvoiceInitials || $debtor->InvoiceSurName || $debtor->InvoiceAddress || $debtor->InvoiceAddress2 || $debtor->InvoiceZipCode || $debtor->InvoiceCity || $debtor->InvoiceCountry && $debtor->InvoiceCountry != $debtor->Country || $debtor->InvoiceEmailAddress) {
    echo "checked=\"checked\"";
}
echo "/> <label for=\"AbnormalInvoiceData\"><strong>";
echo __("abnormal invoice information");
echo "</strong></label>\n<a id=\"AbnormalInvoiceData_copylink\" onclick=\"copyInvoiceData();\" class=\"c1 pointer floatr ";
if(!($debtor->InvoiceCompanyName || $debtor->InvoiceInitials || $debtor->InvoiceSurName || $debtor->InvoiceAddress || $debtor->InvoiceAddress2 || $debtor->InvoiceZipCode || $debtor->InvoiceCity || $debtor->InvoiceCountry && $debtor->InvoiceCountry != $debtor->Country || $debtor->InvoiceEmailAddress)) {
    echo "hide";
}
echo "\">";
echo __("copy from general data");
echo "</a>\n\t\t\t\t\t\t<br clear=\"both\"/>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<div id=\"AbnormalInvoiceData_extra\" ";
if(!($debtor->InvoiceCompanyName || $debtor->InvoiceInitials || $debtor->InvoiceSurName || $debtor->InvoiceAddress || $debtor->InvoiceAddress2 || $debtor->InvoiceZipCode || $debtor->InvoiceCity || $debtor->InvoiceCountry && $debtor->InvoiceCountry != $debtor->Country || $debtor->InvoiceEmailAddress)) {
    echo "class=\"hide\"";
}
echo ">\n\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("companyname");
echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"InvoiceCompanyName\" value=\"";
echo $debtor->InvoiceCompanyName;
echo "\" ";
$ti = tabindex($ti);
echo " maxlength=\"100\"/>\n\t\t\t\t\t\t\t<br /><br />\n\n\t\t\t\t\t\t\t<div class=\"inputgroup_size14_percentage\">\n\t\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("contact person");
echo "</strong>\n                            \t<select name=\"InvoiceSex\" class=\"text1 size16\" ";
$ti = tabindex($ti);
echo ">\n    \t\t\t\t\t\t\t";
foreach ($array_sex as $k => $v) {
    echo "    \t\t\t\t\t\t\t\t<option value=\"";
    echo $k;
    echo "\" ";
    if($debtor->InvoiceSex == $k) {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo $v;
    echo "</option>\n    \t\t\t\t\t\t\t";
}
echo "    \t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size16\" name=\"InvoiceInitials\" value=\"";
echo $debtor->InvoiceInitials;
echo "\" ";
$ti = tabindex($ti);
echo " maxlength=\"25\"/> <input type=\"text\" class=\"text1 size15_calc\" name=\"InvoiceSurName\" value=\"";
echo $debtor->InvoiceSurName;
echo "\" ";
$ti = tabindex($ti);
echo " maxlength=\"100\"/>\n\t\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t</div>\n\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("address");
echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"InvoiceAddress\" value=\"";
echo $debtor->InvoiceAddress;
echo "\" ";
$ti = tabindex($ti);
echo " maxlength=\"100\"/>\n\t\t\t\t\t\t\t";
if(IS_INTERNATIONAL) {
    echo "<br /><input type=\"text\" class=\"text1 size14_percentage marginT2\" name=\"InvoiceAddress2\" value=\"";
    echo $debtor->InvoiceAddress2;
    echo "\" ";
    $ti = tabindex($ti);
    echo " maxlength=\"100\"/>";
}
echo "\t\t\t\t\t\t\t<br /><br />\n\n\t\t\t\t\t\t\t<div class=\"inputgroup_size14_percentage\">\n\t\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("zipcode and city");
echo "</strong>\n\t\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size16\" name=\"InvoiceZipCode\" value=\"";
echo $debtor->InvoiceZipCode;
echo "\" ";
$ti = tabindex($ti);
echo " maxlength=\"10\"/> <input type=\"text\" class=\"text1 size_calc_100-size16\" name=\"InvoiceCity\" value=\"";
echo $debtor->InvoiceCity;
echo "\" ";
$ti = tabindex($ti);
echo " maxlength=\"100\"/>\n\t\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t";
if(IS_INTERNATIONAL) {
    echo "\t\t\t\t\t\t\t\t<strong class=\"title\">";
    echo __("state");
    echo "</strong>\n\t\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage ";
    if(isset($array_states[$debtor->InvoiceCountry])) {
        echo "hide";
    }
    echo "\" name=\"InvoiceState\" value=\"";
    echo $debtor->InvoiceStateName;
    echo "\" ";
    $ti = tabindex($ti);
    echo " maxlength=\"100\"/>\n\t\t\t\t\t\t\t\t<select class=\"text1 size14_percentage ";
    if(!isset($array_states[$debtor->InvoiceCountry])) {
        echo "hide";
    }
    echo "\" name=\"InvoiceStateCode\" ";
    $ti = tabindex($ti);
    echo ">\n\t\t\t\t\t\t\t\t\t<option value=\"\">";
    echo __("make your choice");
    echo "</option>\n\t\t\t\t\t\t\t\t";
    if(isset($array_states[$debtor->InvoiceCountry])) {
        foreach ($array_states[$debtor->InvoiceCountry] as $key => $value) {
            echo "\t\t\t\t\t\t\t\t\t<option value=\"";
            echo $key;
            echo "\" ";
            if($debtor->InvoiceState == $key) {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo $value;
            echo "</option>\n\t\t\t\t\t\t\t\t";
        }
    }
    echo "\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("country");
echo "</strong>\n\t\t\t\t\t\t\t<select class=\"text1 size14_percentage\" name=\"InvoiceCountry\" ";
$ti = tabindex($ti);
echo ">\n\t\t\t\t\t\t\t";
$debtor->InvoiceCountry = $debtor->InvoiceCountry && $debtor->InvoiceAddress ? $debtor->InvoiceCountry : $debtor->Country;
foreach ($array_country as $key => $value) {
    echo "\t\t\t\t\t\t\t\t<option value=\"";
    echo $key;
    echo "\" ";
    if($debtor->InvoiceCountry == $key) {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo $value;
    echo "</option>\n\t\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t\t</select>\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("emailaddress");
echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"InvoiceEmailAddress\" value=\"";
echo check_email_address($debtor->InvoiceEmailAddress, "convert", ", ");
echo "\" ";
$ti = tabindex($ti);
echo "/>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<label><input name=\"InvoiceDataForPriceQuote\" type=\"checkbox\" class=\"checkbox1\" value=\"yes\" ";
if($debtor->InvoiceDataForPriceQuote == "yes") {
    echo "checked=\"checked\"";
}
echo "/> ";
echo __("use abnormal billingdata for pricequote");
echo "</label>\n\t\t\t\t\t\t</div>\n\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\n                    <br />\n                    \n                    <!--box3-->\n                    <div class=\"box3\"><h3>";
echo __("reminders and summations");
echo "</h3><div class=\"content\">\n                            <!--box3-->\n                            <input id=\"UseCustomReminderEmailAddress\" type=\"checkbox\" name=\"UseCustomReminderEmailAddress\" value=\"yes\" class=\"checkbox1\" ";
if(!empty($debtor->ReminderEmailAddress)) {
    echo "checked=\"checked\"";
}
echo "/> <label for=\"UseCustomReminderEmailAddress\"><strong>";
echo __("debtor use custom reminderemailadres");
echo "</strong></label><br />\n                            <div id=\"debtor_use_custom_reminder_emailaddress\" ";
if(empty($debtor->ReminderEmailAddress)) {
    echo "class=\"hide\"";
}
echo ">\n                                <br />\n                                <strong class=\"title\">";
echo __("emailaddress");
echo "</strong>\n                                <input type=\"text\" class=\"text1 size14_percentage\" name=\"ReminderEmailAddress\" value=\"";
echo check_email_address($debtor->ReminderEmailAddress, "convert", ", ");
echo "\" ";
$ti = tabindex($ti);
echo " />\n                            </div>\n                            <!--box3-->\n                        </div>\n                    </div>\n                    <!--box3-->\n\n\t\t\t\t\t<br />\n\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("customerpanel");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t<input id=\"CustomerPanelAccess\" name=\"CustomerPanelAccess\" type=\"checkbox\" class=\"checkbox1\" value=\"on\" ";
if($debtor->ActiveLogin == "yes") {
    echo "checked=\"checked\"";
}
echo " /> <label for=\"CustomerPanelAccess\"><strong>";
echo __("access to customerpanel");
echo "</strong></label>\n\t\t\t\t\t\t<br clear=\"both\"/>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<div id=\"CustomerPanelAccess_extra\" ";
if($debtor->ActiveLogin != "yes") {
    echo "class=\"hide\"";
}
echo ">\n\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t<a onclick=\"createLogin();\" class=\"c1 pointer floatr\">";
echo __("generate data");
echo "</a>\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("username");
echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"Username\" value=\"";
echo $debtor->Username;
echo "\" ";
$ti = tabindex($ti);
echo "/>\n\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("temp password");
echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"Password\" value=\"";
echo passcrypt($debtor->Password);
echo "\" ";
$ti = tabindex($ti);
echo "/>\n\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("language preference");
echo "</strong>\n\t\t\t\t\t\t\t<select name=\"DefaultLanguage\" class=\"text1 size14_percentage\">\n\t\t\t\t\t\t\t\t<option value=\"\" ";
if($debtor->DefaultLanguage == "") {
    echo "selected=\"selected\"";
}
echo ">";
echo __("standard");
echo "</option>\n\t\t\t\t\t\t\t\t";
foreach ($array_customer_languages as $key => $value) {
    echo "\t\t\t\t\t\t\t\t\t<option value=\"";
    echo $key;
    echo "\" ";
    if($debtor->DefaultLanguage == $key) {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo $value;
    echo "</option>\n\t\t\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t\t</select>\n\n\t\t\t\t\t\t\t";
if(isset($clientarea_profiles) && 0 < count($clientarea_profiles)) {
    echo "\t\t\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\t\t<strong class=\"title\">";
    echo __("clientarea profile");
    echo "</strong>\n\t\t\t\t\t\t\t\t\t<select name=\"ClientareaProfile\" class=\"text1 size7\">\n\t\t\t\t\t\t\t\t\t\t";
    foreach ($clientarea_profiles as $_profile) {
        echo "\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"";
        echo $_profile->Default == "yes" ? 0 : $_profile->id;
        echo "\" ";
        if($debtor->ClientareaProfile == $_profile->id || $_profile->Default == "yes" && (int) $debtor->ClientareaProfile === 0) {
            echo "selected=\"selected\"";
        }
        echo ">\n\t\t\t\t\t\t\t\t\t\t\t\t\t";
        if($_profile->Default == "yes") {
            echo __("standard") . " (" . htmlspecialchars($_profile->Name) . ")";
        } else {
            echo htmlspecialchars($_profile->Name);
        }
        echo "\t\t\t\t\t\t\t\t\t\t\t\t</option>\n\t\t\t\t\t\t\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t</div>\n\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("send welcomemail");
echo "</strong>\n\t\t\t\t\t\t<select name=\"WelcomeMail\" class=\"text1 size14_percentage\">\n\t\t\t\t\t\t\t<option value=\"\" ";
if($debtor->SentWelcome != "yes") {
    echo "selected=\"selected\"";
}
echo ">";
echo __("do not send");
echo "</option>\n\t\t\t\t\t\t\t";
foreach ($emailtemplates as $k => $v) {
    if(is_numeric($k)) {
        echo "\t\t\t\t\t\t\t<option value=\"";
        echo $k;
        echo "\" ";
        if(WELCOME_MAIL == $k && $debtor->SentWelcome == "yes") {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo $v["Name"];
        echo "</option>\n\t\t\t\t\t\t\t";
    }
}
echo "\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\n\t\t\t\t\t\t";
if(!$debtor->Username && 0 < WELCOME_MAIL && $debtor->SentWelcome == "yes") {
    echo "\t\t\t\t\t\t<script type=\"text/javascript\">\$(document).ready(function() { createLogin(); });</script>\n\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\n\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t";
if(!empty($debtor->customfields_list)) {
    echo "\n\t\t\t\t\t\t<br />\n\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t<div class=\"box3\"><h3>";
    echo __("custom debtor fields h2");
    echo "</h3><div class=\"content customfields_debtor customfields\">\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t";
    foreach ($debtor->customfields_list as $k => $custom_field) {
        $custom_value = isset($debtor->customvalues[$custom_field["FieldCode"]]) ? $debtor->customvalues[$custom_field["FieldCode"]] : NULL;
        echo "\t\t\t\t\t\t\t<strong class=\"title\">";
        echo htmlspecialchars($custom_field["LabelTitle"]);
        echo "</strong>\n\t\t\t\t\t\t\t";
        echo show_custom_input_field($custom_field, $custom_value);
        echo "\t\t\t\t\t\t\t";
        if($k + 1 != count($debtor->customfields_list)) {
            echo "<br /><br />";
        }
    }
    echo "\t\t\t\t\t\t\t\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t</div></div>\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t";
}
echo "\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t</div>\n\t\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t<!--split2-->\n\t\t\t</div>\n\t\t\t<!--split2-->\n\t\t\t\t\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\t\t\n\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-directdebit\">\n\t\t<!--content-->\n\t\t\n\t\t\t<!--split2-->\n\t\t\t<div class=\"split2\">\n\t\t\t<!--split2-->\n\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t<div class=\"left\">\n\t\t\t\t<!--left-->\n\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("bankaccount data");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("account number");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"AccountNumber\" value=\"";
echo $debtor->AccountNumber;
echo "\" ";
$ti = tabindex($ti);
echo "/>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("account name");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"AccountName\" value=\"";
echo $debtor->AccountName;
echo "\" ";
$ti = tabindex($ti);
echo "/>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("bank");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"AccountBank\" value=\"";
echo $debtor->AccountBank;
echo "\" ";
$ti = tabindex($ti);
echo "/>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("bank city");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"AccountCity\" value=\"";
echo $debtor->AccountCity;
echo "\" ";
$ti = tabindex($ti);
echo "/>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("bic");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"AccountBIC\" value=\"";
echo $debtor->AccountBIC;
echo "\" ";
$ti = tabindex($ti);
echo "/>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t</div>\n\t\t\t\t<!--left-->\n\n\t\t\t\t<!--right-->\n\t\t\t\t<div class=\"right\">\n\t\t\t\t<!--right-->\n\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("direct debit for debtor h3");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t\t\t<label><input type=\"checkbox\" name=\"InvoiceAuthorisation\" value=\"yes\" ";
if($debtor->InvoiceAuthorisation == "yes") {
    echo "checked=\"checked\"";
}
echo "/> ";
echo __("this debtor want to pay via direct debit");
echo "</label>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<div id=\"debtor_div_directdebit\" ";
if($debtor->InvoiceAuthorisation == "no") {
    echo "class=\"hide\"";
}
echo ">\n\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("sdd mandate id");
echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"MandateID\" value=\"";
echo $debtor->MandateID ? $debtor->MandateID : ($debtor->InvoiceAuthorisation == "yes" ? $debtor->getDirectDebitMandateID() : "");
echo "\" maxlength=\"35\" ";
$ti = tabindex($ti);
echo "/>\n\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("sdd mandate date");
echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size6 datepicker\" name=\"MandateDate\" value=\"";
echo $debtor->MandateDate ? $debtor->MandateDate : rewrite_date_db2site(date("Y-m-d"));
echo "\" ";
$ti = tabindex($ti);
echo "/>\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<br /><br />\n\n\t\t\t\t\t\t</div>\n\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t</div>\n\t\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t<!--split2-->\n\t\t\t</div>\n\t\t\t<!--split2-->\n\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\t\t\n\t\t\n\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-groups\">\n\n\t\t<!--content-->\n\t\t\n\t\t\t\t<p>\n\t\t\t\t\t<strong>";
echo __("connect debtors to debtorgroups");
echo "</strong>\n\t\t\t\t</p>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t<table id=\"MainTable\" class=\"table1\" cellpadding=\"0\" cellspacing=\"0\">\n\t\t\t\t\t<tr class=\"trtitle\">\n\t\t\t\t\t\t<th scope=\"col\"><label><input name=\"GroupBatch\" class=\"BatchCheck\" type=\"checkbox\" value=\"on\" /></label> ";
echo __("debtorgroup");
echo "</th>\n\t\t\t\t\t</tr>\n\t\t\t\t\t";
$groupCounter = 0;
foreach ($groups as $groupID => $group) {
    if(is_numeric($groupID)) {
        $groupCounter++;
        echo "\t\t\t\t\t\t\t<tr";
        if($groupCounter % 2 === 1) {
            echo " class=\"tr1\"";
        }
        echo ">\n\t\t\t\t\t\t\t\t<td><label><input name=\"Groups[]\" type=\"checkbox\" class=\"GroupBatch\" value=\"";
        echo $groupID;
        echo "\" ";
        if(array_key_exists($groupID, $debtor->Groups)) {
            echo "checked=\"checked\"";
        }
        echo "/> ";
        echo $group["GroupName"];
        echo "</label></td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t";
    }
}
if($groupCounter === 0) {
    echo "\t\t\t\t\t\t<tr class=\"tr1\">\n\t\t\t\t\t\t\t<td>";
    echo __("no debtorgroups found");
    echo "</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t";
}
echo "\t\t\t\t</table>\t\t   \n\t\t\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\n\t\t\n\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-comment\">\n\t\t<!--content-->\n\t\t\n\t\t\t<!--box3-->\n\t\t\t<div class=\"box3\"><h3>";
echo __("internal note");
echo "</h3><div class=\"content\">\n\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t<strong class=\"title\">";
echo __("comment a debtor");
echo "</strong>\n\t\t\t\t<textarea class=\"text1 size5 autogrow\" name=\"Comment\">";
if($debtor->Comment) {
    echo $debtor->Comment;
}
echo "</textarea>\n\n\t\t\t<!--box3-->\n\t\t\t</div></div>\n\t\t\t<!--box3-->\n\t\t\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\t\t\n\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-settings\">\n\t\t<!--content-->\n\t\t\n\t\t\t<!--split2-->\n\t\t\t<div class=\"split2\">\n\t\t\t<!--split2-->\n\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t<div class=\"left\">\n\t\t\t\t<!--left-->\n\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("invoicing");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("invoice method");
echo "</strong>\n\t\t\t\t\t\t<select name=\"InvoiceMethod\" class=\"text1 size4f\" ";
$ti = tabindex($ti);
echo ">\n\t\t\t\t\t\t";
foreach ($array_invoicemethod as $k => $v) {
    echo "\t\t\t\t\t\t<option value=\"";
    echo $k;
    echo "\" ";
    if($debtor->InvoiceMethod == $k) {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo $v;
    echo "</option>\n\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t</select>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("calculate vat");
echo "</strong>\n\t\t\t\t\t\t<select name=\"Taxable\" class=\"text1 size4f\" ";
$ti = tabindex($ti);
echo ">\n\t\t\t\t\t\t";
foreach ($array_taxable as $k => $v) {
    echo "\t\t\t\t\t\t<option value=\"";
    echo $k;
    echo "\" ";
    if($debtor->TaxableSetting == $k) {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo $v;
    echo "</option>\n\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t</select>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("enable collective invoice");
echo "</strong>\n\t\t\t\t\t\t<select name=\"InvoiceCollect\" class=\"text1 size4f\" ";
$ti = tabindex($ti);
echo " onchange=\"\$('#tr_invoice_collect_explain1').hide(); \$('#tr_invoice_collect_explain2').hide(); if(this.value > 0){ \$('#tr_invoice_collect_explain' + this.value).show();  }else if(this.value == '-1'){ ";
if(INVOICE_COLLECT_ENABLED == "yes" && INVOICE_COLLECT_TPM == 1) {
    echo "\$('#tr_invoice_collect_explain1').show();";
} elseif(INVOICE_COLLECT_ENABLED == "yes" && INVOICE_COLLECT_TPM == 2) {
    echo "\$('#tr_invoice_collect_explain2').show();";
}
echo "}\">\n                        <option value=\"0\" ";
if($debtor->InvoiceCollect == "0") {
    echo "selected=\"selected\"";
}
echo ">";
echo __("never for this debtor");
echo "</option>\n\t\t\t\t\t\t<option value=\"1\" ";
if($debtor->InvoiceCollect == "1") {
    echo "selected=\"selected\"";
}
echo ">";
echo __("always 1x per month for this debtor");
echo "</option>\n\t\t\t\t\t\t<option value=\"2\" ";
if($debtor->InvoiceCollect == "2") {
    echo "selected=\"selected\"";
}
echo ">";
echo __("always 2x per month for this debtor");
echo "</option>\n\t\t\t\t\t\t<option value=\"-1\" ";
if($debtor->InvoiceCollect == "-1") {
    echo "selected=\"selected\"";
}
echo ">";
echo __("standard setting");
echo "</option>\n\t\t\t\t\t\t</select>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<span id=\"tr_invoice_collect_explain2\" ";
if(!($debtor->InvoiceCollect == "2" || $debtor->InvoiceCollect == "-1" && INVOICE_COLLECT_ENABLED == "yes" && INVOICE_COLLECT_TPM == 2)) {
    echo "style=\"display:none;\"";
}
echo ">\n\t\t\t\t\t\t";
echo __("invoicecollection twice a month explained");
echo "<br /><br />\n\t\t\t\t\t\t</span> \n\t\n\t\t\t\t\t\t<span id=\"tr_invoice_collect_explain1\" ";
if(!($debtor->InvoiceCollect == "1" || $debtor->InvoiceCollect == "-1" && INVOICE_COLLECT_ENABLED == "yes" && INVOICE_COLLECT_TPM == 1)) {
    echo "style=\"display:none;\"";
}
echo ">\n\t\t\t\t\t\t";
echo __("invoicecollection once a month explained");
echo "<br /><br />\n\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<input type=\"checkbox\" class=\"checkbox1\" id=\"CustomPeriodicInvoiceCheckbox\" name=\"CustomPeriodicInvoiceCheckbox\" value=\"yes\" ";
if($debtor->PeriodicInvoiceDays != -1) {
    echo "checked=\"checked\"";
}
echo " />\n\t\t\t\t\t\t<label for=\"CustomPeriodicInvoiceCheckbox\"><strong>";
echo __("custom bill subscriptions on beforehand");
echo "</strong></label>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<div id=\"CustomPeriodicInvoice\" ";
if($debtor->PeriodicInvoiceDays == -1) {
    echo "class=\"hide\"";
}
echo ">\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("bill subscriptions on beforehand");
echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size6\" name=\"PERIODIC_INVOICE_DAYS\" value=\"";
echo $debtor->PeriodicInvoiceDays != -1 ? $debtor->PeriodicInvoiceDays : PERIODIC_INVOICE_DAYS;
echo "\" ";
$ti = tabindex($ti);
echo " onkeyup=\"if(this.value!=1){ \$('#new_periodic_invoice_days').html(this.value); \$('#div_periodic_invoice_days').show(); }else{ \$('#div_periodic_invoice_days').hide(); }\" /> ";
echo __("days before start period");
echo "\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<div id=\"div_periodic_invoice_days\" class=\"hide\">";
echo sprintf(__("the modification for number of days subscriptions should be billed on beforehand results in adjustment of existing subscriptions"), "<span id=\"new_periodic_invoice_days\">&nbsp;</span>", $debtor->PeriodicInvoiceDays != -1 ? $debtor->PeriodicInvoiceDays : PERIODIC_INVOICE_DAYS);
echo "<br /><br /></div>\n\t\t\t\t\t\t</div>\n\n\t\t\t\t\t\t<br />\n\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("ubl - send setting title");
echo "</strong>\n\t\t\t\t\t\t<select class=\"text1 size4f\" name=\"InvoiceEmailAttachments\">\n\t\t\t\t\t\t\t<option value=\"\" ";
if($debtor->InvoiceEmailAttachments == "") {
    echo "selected=\"selected\"";
}
echo ">\n\t\t\t\t\t\t\t\t";
echo __("use standard setting");
echo "\t\t\t\t\t\t\t</option>\n\t\t\t\t\t\t\t";
foreach ($array_invoiceemailattachments as $k => $v) {
    echo "\t\t\t\t\t\t\t\t<option value=\"";
    echo $k;
    echo "\" ";
    if($debtor->InvoiceEmailAttachments == $k) {
        echo "selected=\"selected\"";
    }
    echo ">\n\t\t\t\t\t\t\t\t\t";
    echo $v;
    echo "\t\t\t\t\t\t\t\t</option>\n\t\t\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\n                    <br />\n\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("different templates");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("pricequote template");
echo "</strong>\n\t\t\t\t\t\t<select name=\"PriceQuoteTemplate\" class=\"text1 size4f\" ";
$ti = tabindex($ti);
echo ">\n\t\t\t\t\t\t<option value=\"\">";
echo __("standard template");
echo "</option>\n\t\t\t\t\t\t";
foreach ($pricequotetemplates as $k => $v) {
    if(is_numeric($k)) {
        echo "\t\t\t\t\t\t<option value=\"";
        echo $k;
        echo "\" ";
        if($debtor->PriceQuoteTemplate == $k) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo $v["Name"];
        echo "</option>\n\t\t\t\t\t\t";
    }
}
echo "\t\t\t\t\t\t</select>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("invoice template");
echo "</strong>\n\t\t\t\t\t\t<select name=\"InvoiceTemplate\" class=\"text1 size4f\" ";
$ti = tabindex($ti);
echo ">\n\t\t\t\t\t\t<option value=\"\">";
echo __("standard template");
echo "</option>\n\t\t\t\t\t\t";
foreach ($templates as $k => $v) {
    if(is_numeric($k)) {
        echo "\t\t\t\t\t\t<option value=\"";
        echo $k;
        echo "\" ";
        if($debtor->InvoiceTemplate == $k) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo $v["Name"];
        echo "</option>\n\t\t\t\t\t\t";
    }
}
echo "\t\t\t\t\t\t</select>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t<br />\t\t\t\t\n\t\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("reminder email");
echo "</strong>\n\t\t\t\t\t\t<select name=\"ReminderTemplate\" class=\"text1 size4f\" ";
$ti = tabindex($ti);
echo ">\n\t\t\t\t\t\t<option value=\"\">";
echo __("standard template");
echo "</option>\n\t\t\t\t\t\t";
foreach ($emailtemplates as $k => $v) {
    if(is_numeric($k)) {
        echo "\t\t\t\t\t\t<option value=\"";
        echo $k;
        echo "\" ";
        if($debtor->ReminderTemplate == $k) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo $v["Name"];
        echo "</option>\n\t\t\t\t\t\t";
    }
}
echo "\t\t\t\t\t\t</select>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("second reminder email");
echo "</strong>\n\t\t\t\t\t\t<select name=\"SecondReminderTemplate\" class=\"text1 size4f\" ";
$ti = tabindex($ti);
echo ">\n\t\t\t\t\t\t<option value=\"-1\" ";
if(-1 == $debtor->SecondReminderTemplate) {
    echo "selected=\"selected\"";
}
echo ">";
echo __("use standard setting");
echo "</option>\n\t\t\t\t\t\t<option value=\"0\" ";
if(0 == $debtor->SecondReminderTemplate) {
    echo "selected=\"selected\"";
}
echo ">";
echo __("as first reminder email");
echo "</option>\n\t\t\t\t\t\t";
foreach ($emailtemplates as $k => $v) {
    if(is_numeric($k)) {
        echo "\t\t\t\t\t\t<option value=\"";
        echo $k;
        echo "\" ";
        if($debtor->SecondReminderTemplate == $k) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo $v["Name"];
        echo "</option>\n\t\t\t\t\t\t";
    }
}
echo "\t\t\t\t\t\t</select>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t";
if(INT_SUPPORT_SUMMATIONS) {
    echo "\t\t\t\t\t\t<strong class=\"title\">";
    echo __("summation email");
    echo "</strong>\n\t\t\t\t\t\t<select name=\"SummationTemplate\" class=\"text1 size4f\" ";
    $ti = tabindex($ti);
    echo ">\n\t\t\t\t\t\t<option value=\"\">";
    echo __("standard template");
    echo "</option>\n\t\t\t\t\t\t";
    foreach ($emailtemplates as $k => $v) {
        if(is_numeric($k)) {
            echo "\t\t\t\t\t\t<option value=\"";
            echo $k;
            echo "\" ";
            if($debtor->SummationTemplate == $k) {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo $v["Name"];
            echo "</option>\n\t\t\t\t\t\t";
        }
    }
    echo "\t\t\t\t\t\t</select>\n\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t<br />\n\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("payment");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t\t\n\t\t\t\t\t\t<input type=\"checkbox\" class=\"checkbox1\" id=\"CustomInvoiceTermCheckbox\" name=\"CustomInvoiceTermCheckbox\" value=\"yes\" ";
if($debtor->InvoiceTerm != "" && $debtor->InvoiceTerm != INVOICE_TERM) {
    echo "checked=\"checked\"";
}
echo " />\n\t\t\t\t\t\t<label for=\"CustomInvoiceTermCheckbox\"><strong>";
echo __("custom term of payment");
echo "</strong></label>\n\t\t\t\t\t\t<br /><br />\n\n\t\t\t\t\t\t<div id=\"CustomInvoiceTerm\" ";
if($debtor->InvoiceTerm == "" || $debtor->InvoiceTerm == INVOICE_TERM) {
    echo "class=\"hide\"";
}
echo ">\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("term of payment");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size6\" name=\"InvoiceTerm\" value=\"";
echo $debtor->InvoiceTerm;
echo "\" ";
$ti = tabindex($ti);
echo "/> ";
echo __("days");
echo "\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("send a payment notification by");
echo "</strong>\n\t\t\t\t\t\t\t<select name=\"payment_mail_when_helper\" class=\"text1 size4f\" >\n\t\t\t\t\t\t\t\t<option value=\"-1\" ";
echo $debtor->PaymentMail == "-1" ? "selected=\"selected\"" : "";
echo ">";
echo __("use standard setting");
echo "</option> <!-- -1 -->\n\t\t\t\t\t\t\t\t<option value=\"\" ";
echo $debtor->PaymentMail == "" ? "selected=\"selected\"" : "";
echo ">";
echo __("do not send notification");
echo "</option>\n\t\t\t\t\t\t\t\t<option value=\"custom\" ";
echo $debtor->PaymentMail != "-1" && $debtor->PaymentMail != "" ? "selected=\"selected\"" : "";
echo "> ";
echo __("custom settings");
echo "</option> \n\t\t\t\t\t\t\t</select><br />\n\t\t\t\t\t\t\t";
$paymentMailWhen = explode("|", $debtor->PaymentMail);
echo "\t\t\t\t\t\t\t<div id=\"payment_mail_when\" style=\"padding-top: 5px;\" ";
echo $debtor->PaymentMail == "-1" || $debtor->PaymentMail == "" ? "class=\"hide\"" : "";
echo ">\n\t\t\t\t\t\t\t\t<label><input type=\"checkbox\" name=\"PaymentMail[]\" value=\"auth\" ";
echo in_array("auth", $paymentMailWhen) ? "checked=\"checked\"" : "";
echo " ";
$ti = tabindex($ti);
echo " /> ";
echo __("send a payment notification by auth");
echo "</label><br />\n\t\t\t\t\t\t\t\t<label><input type=\"checkbox\" name=\"PaymentMail[]\" value=\"wire\" ";
echo in_array("wire", $paymentMailWhen) ? "checked=\"checked\"" : "";
echo " ";
$ti = tabindex($ti);
echo " /> ";
echo __("send a payment notification by wire");
echo "</label><br />\n\t\t\t\t\t\t\t\t<label><input type=\"checkbox\" name=\"PaymentMail[]\" value=\"order\" ";
echo in_array("order", $paymentMailWhen) ? "checked=\"checked\"" : "";
echo " ";
$ti = tabindex($ti);
echo " /> ";
echo __("send a payment notification by order");
echo "</label><br />\n\t\t\t\t\t\t\t\t<label><input type=\"checkbox\" name=\"PaymentMail[]\" value=\"paid\" ";
echo in_array("paid", $paymentMailWhen) ? "checked=\"checked\"" : "";
echo " ";
$ti = tabindex($ti);
echo " /> ";
echo __("send a payment notification by paid");
echo "</label><br />\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t<div id=\"paymentmail_template\" ";
if($debtor->PaymentMail == "") {
    echo "class=\"hide\"";
}
echo ">\n\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("payment confirmation template");
echo "</strong>\n\t\t\t\t\t\t\t<select name=\"PaymentMailTemplate\" class=\"text1 size4f\" ";
$ti = tabindex($ti);
echo ">\n\t\t\t\t\t\t\t<option value=\"\">";
echo __("use standard template");
echo "</option>\n\t\t\t\t\t\t\t";
foreach ($emailtemplates as $k => $v) {
    if(is_numeric($k)) {
        echo "\t\t\t\t\t\t\t<option value=\"";
        echo $k;
        echo "\" ";
        if($debtor->PaymentMailTemplate == $k) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo $v["Name"];
        echo "</option>\n\t\t\t\t\t\t\t";
    }
}
echo "\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t</div>\n\t\t\t\t<!--left-->\n\n\t\t\t\t<!--right-->\n\t\t\t\t<div class=\"right\">\n\t\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("domains");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t\t<input id=\"UseCustomNameservers\" type=\"checkbox\" name=\"UseCustomNameservers\" value=\"yes\" class=\"checkbox1\" ";
if($debtor->DNS1 || $debtor->DNS2 || $debtor->DNS3) {
    echo "checked=\"checked\"";
}
echo "/> <label for=\"UseCustomNameservers\"><strong>";
echo __("debtor use custom nameservers");
echo "</strong></label><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<div id=\"debtor_use_custom_nameservers\" ";
if(!$debtor->DNS1 && !$debtor->DNS2 && !$debtor->DNS3) {
    echo "class=\"hide\"";
}
echo ">\n\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("nameserver 1");
echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"DNS1\" value=\"";
echo $debtor->DNS1;
echo "\" ";
$ti = tabindex($ti);
echo " />\n\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("nameserver 2");
echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"DNS2\" value=\"";
echo $debtor->DNS2;
echo "\" ";
$ti = tabindex($ti);
echo " />\n\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("nameserver 3");
echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size14_percentage\" name=\"DNS3\" value=\"";
echo $debtor->DNS3;
echo "\" ";
$ti = tabindex($ti);
echo " />\n\t\t\t\t\t\t</div>\t\t\t\n\t\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t<br />\n\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("webhosting");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("preference server");
echo "</strong>\n\t\t\t\t\t\t<select name=\"Server\" class=\"text1 size4f\">\n\t\t\t                <option value=\"\">";
echo __("no preference");
echo "</option>\n\t\t\t                ";
$plesk = false;
foreach ($list_servers as $k => $v) {
    if(is_numeric($k)) {
        if(substr($v["Panel"], 0, 5) == "plesk") {
            $plesk = true;
        }
        echo "<option value=\"";
        echo $v["id"];
        echo "\" ";
        if($debtor->Server == $v["id"]) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo $v["Name"];
        echo "</option>";
    }
}
echo "\t\t\t             </select>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t";
if(0 < $debtor->Identifier && $plesk) {
    echo "\t\t\t\t\t\t<strong class=\"title\">";
    echo __("plesk client ids");
    echo "</strong>\n\t\t\t\t\t\t";
    echo sprintf(__("plesk client description"), "<a onclick=\"\$('#pleskclients').dialog('open');\" class=\"pointer a1 c1\">", "</a>");
    echo "\t\t\t\t\t\t<input type=\"hidden\" name=\"PleskClientID\" value=\"";
    echo $debtor->PleskClientID;
    echo "\" id=\"PleskClientID\"/>\t\n\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t</div>\n\t\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t<!--split2-->\n\t\t\t</div>\n\t\t\t<!--split2-->\n\t\t\n\t\t\t\n\t\t\t\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n        \n        \n        <!-- ATTACHMENTS -->\n        <div class=\"content\" id=\"tab-attachments\">\n\t\t\t<div class=\"box3\">\n            \n                <h3>";
echo __("attachments");
echo "</h3>\n                \n                <div class=\"content\">\n\t\t\t\t\t\t\n    \t\t\t\t<div id=\"fileUploadError\" class=\"loading_red removeBr\"></div>\n\t\t\t\t\t\n                    <div id=\"files_list\" class=\"hide\">\n\t\t\t\t\t\t<p class=\"align_right mar4\">\n                            <i>";
echo __("total");
echo ": <span id=\"files_total\"></span></i>\n                        </p>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<ul id=\"files_list_ul\" class=\"emaillist\">\n                        ";
$attachCounter = 0;
if(!empty($debtor->Attachment)) {
    foreach ($debtor->Attachment as $id => $file) {
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
echo "</i></p>\n\t\t\t\t\t\n\t\t\t\t\t<a id=\"add_files_link\" data-filetype=\"debtor_files\" class=\"a1 c1 ico inline add upload_file\">";
echo __("add attachment");
echo "</a>\n\t\t\t\t\t<span id=\"dragndrophere\">";
echo __("or move your files here");
echo "</span>\n\n                </div>\n            \n            </div>\n\t\t</div>\n\n\t<!--box1-->\n\t</div>\n\t<!--box1-->\n\t\n\t\t<br />\n\n\t\t";
if(isset($clientarea_change) && $clientarea_change === true) {
    echo "\t\t\t<div class=\"buttonbar\">\n\t\t\t\t<p class=\"pos2\">\n\t\t\t\t\t";
    if($emailsync && (in_array("EmailAddress", array_keys($debtor_changes)) || in_array("InvoiceEmailAddress", array_keys($debtor_changes)))) {
        echo "\t\t\t\t\t\t\t<label>\n\t\t\t\t\t\t\t\t<input type=\"checkbox\" data-input=\"SynchronizeEmail\" value=\"\" class=synchronize_toggle>\n\t\t\t\t\t\t\t\t";
        echo __("debtor sync email title");
        echo "\t\t\t\t\t\t\t</label>\n\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t";
        $emailsync = false;
    }
    if($authsync && in_array("InvoiceAuthorisation", array_keys($debtor_changes))) {
        echo "\t\t\t\t\t\t\t<label>\n\t\t\t\t\t\t\t\t<input type=\"checkbox\" data-input=\"SynchronizeAuth\" value=\"\" class=synchronize_toggle>\n\t\t\t\t\t\t\t\t";
        echo __("debtor sync auth title");
        echo "\t\t\t\t\t\t\t</label>\n\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t";
        $authsync = false;
    }
    $address_fields = ["CompanyName", "Sex", "Initials", "SurName", "Address", "Address2", "ZipCode", "City", "State", "StateCode", "Country", "InvoiceCompanyName", "InvoiceSex", "InvoiceInitials", "InvoiceSurName", "InvoiceAddress", "InvoiceAddress2", "InvoiceZipCode", "InvoiceCity", "InvoiceState", "InvoiceStateCode", "InvoiceCountry"];
    if($addresssync && 0 < count(array_intersect(array_keys($debtor_changes), $address_fields))) {
        echo "\t\t\t\t\t\t\t<label>\n\t\t\t\t\t\t\t\t<input type=\"checkbox\" data-input=\"SynchronizeAddress\" value=\"\" class=synchronize_toggle>\n\t\t\t\t\t\t\t\t";
        echo __("debtor sync address title");
        echo "\t\t\t\t\t\t\t</label>\n\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t";
        $addresssync = false;
    }
    $contact_fields = ["CompanyName", "Initials", "SurName", "Address", "Address2", "ZipCode", "City", "State", "StateCode", "Country", "EmailAddress", "PhoneNumber", "FaxNumber", "TaxNumber", "CompanyNumber", "LegalForm"];
    if(0 < count(array_intersect(array_keys($debtor_changes), $contact_fields)) && !empty($matched_handles)) {
        echo "\t\t\t\t\t\t\t<label>\n\t\t\t\t\t\t\t\t<input type=\"checkbox\" data-input=\"SynchronizeHandles\" value=\"\" class=synchronize_toggle>\n\t\t\t\t\t\t\t\t";
        echo __("debtor sync handles title");
        echo "\t\t\t\t\t\t\t</label>\n\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t";
    }
    echo "\n\t\t\t\t\t<script type=\"application/javascript\">\n\t\t\t\t\t\t\$('input.synchronize_toggle').click(function()\n\t\t\t\t\t\t{\n\t\t\t\t\t\t\tif(\$(this).prop('checked') === true)\n\t\t\t\t\t\t\t{\n\t\t\t\t\t\t\t\t\$('input[name=\"' + \$(this).data('input') + '\"]').val('yes');\n\t\t\t\t\t\t\t}\n\t\t\t\t\t\t\telse\n\t\t\t\t\t\t\t{\n\t\t\t\t\t\t\t\t\$('input[name=\"' + \$(this).data('input') + '\"]').val('no');\n\t\t\t\t\t\t\t}\n\t\t\t\t\t\t})\n\t\t\t\t\t</script>\n\t\t\t\t</p>\n\t\t\t\t<br class=\"clear\" /><br />\n\n\t\t\t\t<p class=\"pos1\">\n\t\t\t\t\t<a class=\"a1 c1\" onclick=\"\$('#decline_modification').dialog('open');\">\n\t\t\t\t\t\t<span>";
    echo __("decline modification");
    echo "</span>\n\t\t\t\t\t</a>\n\t\t\t\t</p>\n\t\t\t\t<p class=\"pos2\">\n\t\t\t\t\t<a onclick=\"document.form_create.submit();\" class=\"button1 alt1 pointer\">\n\t\t\t\t\t\t<span>";
    echo __("accept modification");
    echo "</span>\n\t\t\t\t\t</a>\n\t\t\t\t</p>\n\t\t\t</div>\n\t\t\t";
} else {
    echo "\t\t\t<p class=\"align_right\">\n\t\t\t\t<a class=\"button1 alt1\" id=\"";
    if(0 < $debtor_id) {
        echo "form_debtor_edit_btn";
    } else {
        echo "form_create_btn";
    }
    echo "\">\n\t\t\t\t\t<span>";
    echo $pagetype == "edit" ? __("btn edit") : __("btn add");
    echo "</span>\n\t\t\t\t</a>\n\t\t\t</p>\n\t\t\t";
}
echo "\t\t\n\t<!--form-->\n\t</fieldset></form>\n\t<!--form-->\n\n\n";
if(0 < $debtor->Identifier && $plesk) {
    $list_debtor_clientids = $debtor->getPleskClientIDs();
    echo "\t<div id=\"pleskclients\" class=\"actiondialog\" title=\"";
    echo __("plesk client ids");
    echo "\">\n\t";
    echo __("plesk client dialog description");
    echo "<br />\n\t<form id=\"pleskform\" method=\"post\" action=\"?\" onsubmit=\"\$('#PleskClientID').val(\$(this).serialize()); return false;\">\n\t<table class=\"table1\" style=\"width: 100%;\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"> \n    <thead> \n    <tr> \n        <th>";
    echo __("server");
    echo "</th>\n        <th>";
    echo __("clientid");
    echo "</th>\n  \t</tr>\n  \t</thead>\n  \t<tbody>\n  \t";
    foreach ($list_servers as $k => $v) {
        if(is_numeric($k) && substr($v["Panel"], 0, 5) == "plesk") {
            echo "  \t<tr>\n  \t\t<td>";
            echo $v["Name"];
            echo "</td>\n  \t\t<td><input type=\"text\" id=\"form_ClientID_";
            echo $v["id"];
            echo "\" name=\"server_";
            echo $v["id"];
            echo "\" value=\"";
            echo isset($list_debtor_clientids[$v["id"]]) ? $list_debtor_clientids[$v["id"]] : "";
            echo "\"  class=\"text1 size4\"/> <span class=\"viewicon delete_icon\" style=\"vertical-align: text-bottom;\" onclick=\"\$('#form_ClientID_";
            echo $v["id"];
            echo "').val('');\">&nbsp;</span></td>\n\t</tr>\n\t";
        }
    }
    echo "\t</tbody>\n\t</table>\n\t<br />\n\t<i>";
    echo __("modifications are done after saving");
    echo "</i><br />\n\t<br />\n\t<a class=\"button1 alt1 pointer\" onclick=\"\$('#pleskform').submit(); \$('#pleskclients').dialog('close');\"><span>";
    echo __("close dialog");
    echo "</span></a>\n\t</form>\n\t<script type=\"text/javascript\">\n\t\$(function(){ \$('#PleskClientID').val(\$('#pleskform').serialize()); });\n\t</script>\n</div>\n";
}
echo "\n";
if(0 < $debtor_id) {
    echo "\t";
    if($emailsync === true) {
        echo "\t<div id=\"sync_emailaddress\" class=\"hide\" title=\"";
        echo __("debtor sync email title");
        echo "\">\n\t\t";
        echo __("debtor sync email description");
        echo "\t\t<br />\n\t\t<input type=\"radio\" id=\"sync_email_yes\" name=\"sync_email\" value=\"yes\"/> <label for=\"sync_email_yes\">";
        echo __("yes");
        echo "</label><br />\n\t\t<input type=\"radio\" id=\"sync_email_no\" name=\"sync_email\" value=\"no\"/> <label for=\"sync_email_no\">";
        echo __("no");
        echo "</label><br />\n\t\t<br />\n\t\t<p><a class=\"button1 alt1\" onclick=\"\$('#sync_emailaddress').dialog('close');\"><span>";
        echo __("further");
        echo "</span></a></p>\n\t</div>\n\t";
    }
    echo "\t\n\t";
    if($authsync === true) {
        echo "\t<div id=\"sync_authorisation\" class=\"hide\" title=\"";
        echo __("debtor sync auth title");
        echo "\">\n\t\t";
        echo __("debtor sync auth description");
        echo "<br />\n\t\t<br />\n\t\t<input type=\"radio\" id=\"sync_auth_yes\" name=\"sync_auth\" value=\"yes\"/> <label for=\"sync_auth_yes\">";
        echo __("yes");
        echo "</label><br />\n\t\t<input type=\"radio\" id=\"sync_auth_no\" name=\"sync_auth\" value=\"no\"/> <label for=\"sync_auth_no\">";
        echo __("no");
        echo "</label><br />\n\t\t<br />\n\t\t<p><a class=\"button1 alt1\" onclick=\"\$('#sync_authorisation').dialog('close');\"><span>";
        echo __("further");
        echo "</span></a></p>\n\t</div>\n\t";
    }
    echo "\t\n\t";
    if($addresssync === true) {
        echo "\t<div id=\"sync_address\" class=\"hide\" title=\"";
        echo __("debtor sync address title");
        echo "\">\n\t\t";
        echo __("debtor sync address description");
        echo "<br />\n\t\t<br />\n\t\t<input type=\"radio\" id=\"sync_address_yes\" name=\"sync_address\" value=\"yes\"/> <label for=\"sync_address_yes\">";
        echo __("yes");
        echo "</label><br />\n\t\t<input type=\"radio\" id=\"sync_address_no\" name=\"sync_address\" value=\"no\"/> <label for=\"sync_address_no\">";
        echo __("no");
        echo "</label><br />\n\t\t<br />\n\t\t<p><a class=\"button1 alt1\" id=\"sync_address_btn\"><span>";
        echo __("further");
        echo "</span></a></p>\n\t</div>\n\t";
    }
    echo "\t\n\t";
    if(0 < $debtor_id && !empty($matched_handles)) {
        echo "\t<div id=\"sync_handles\" class=\"hide\" title=\"";
        echo __("debtor sync handles title");
        echo "\">\n\t\t";
        echo __("debtor sync handles description");
        echo "<br />\n\t\t<br />\n\t\t<input type=\"radio\" id=\"sync_handles_yes\" name=\"sync_handles\" value=\"yes\"/> <label for=\"sync_handles_yes\">";
        echo __("yes");
        echo "</label><br />\n\t\t<input type=\"radio\" id=\"sync_handles_no\" name=\"sync_handles\" value=\"no\"/> <label for=\"sync_handles_no\">";
        echo __("no");
        echo "</label><br />\n\t\t<br />\n\t\t<p><a class=\"button1 alt1\" onclick=\"\$('form[name=form_create]').submit();\"><span>";
        echo __("further");
        echo "</span></a></p>\n\t</div>\n\t";
    }
    echo "\t\n";
}
echo "\n<div id=\"filemanager\" title=\"";
echo __("filemanager");
echo "\"></div>\n\n";
if(isset($clientarea_change) && $clientarea_change === true) {
    echo "\t<div id=\"decline_modification\" class=\"hide\" title=\"";
    echo __("decline modification");
    echo "\">\n\t\t<form id=\"DeclineModificationForm\" name=\"form_decline\" method=\"post\" action=\"clientareachanges.php?page=reject&amp;referenceid=";
    echo $debtor_id;
    echo "&amp;referencetype=debtor&amp;debtor=";
    echo $debtor_id;
    echo "\">\n\n\t\t\t<p>\n\t\t\t\t<strong>";
    echo __("confirm action");
    echo "</strong>\n\t\t\t\t<br/>\n\t\t\t\t";
    echo __("are you sure to decline this modification?");
    echo "\t\t\t</p>\n\t\t\t<br/>\n\n\t\t\t";
    if(isset($_GET["clientareachange"])) {
        echo "<input type=\"hidden\" name=\"ClientareaChange\" value=\"" . htmlspecialchars(esc($_GET["clientareachange"])) . "\" />";
    }
    echo "\n\t\t\t<p>\n\t\t\t\t<a class=\"button1 alt1 float_left\" onclick=\"document.form_decline.submit();\">\n\t\t\t\t\t<span>";
    echo __("decline");
    echo "</span>\n\t\t\t\t</a>\n\t\t\t</p>\n\t\t\t<p>\n\t\t\t\t<a class=\"a1 c1 float_right\" onclick=\"\$('#decline_modification').dialog('close');\">\n\t\t\t\t\t<span>";
    echo __("cancel");
    echo "</span>\n\t\t\t\t</a>\n\t\t\t</p>\n\t\t</form>\n\t</div>\n\n\t<script type=\"application/javascript\">\n\t\t\$(function()\n\t\t{\n\t\t\t\$('#decline_modification').dialog({\n\t\t\t\tmodal: true,\n\t\t\t\tautoOpen: false,\n\t\t\t\tresizable: false,\n\t\t\t\twidth: 450,\n\t\t\t\theight: 'auto'\n\t\t\t});\n\t\t})\n\t</script>\n\t";
}
echo "\n";
require_once "views/footer.php";

?>