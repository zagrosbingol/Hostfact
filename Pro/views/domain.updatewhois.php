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
echo "\t\n\n<!--form-->\n<form id=\"UpdateWHOISForm\" name=\"form_create\" method=\"post\" action=\"?page=updatewhois\"><fieldset><legend>";
echo sprintf(__("update whois data for domain"), $domain->Domain . "." . $domain->Tld);
echo "</legend>\n<!--form-->\n";
if(0 < $domain_id) {
    echo "\t<input type=\"hidden\" name=\"id\" value=\"";
    echo $domain_id;
    echo "\" />\n";
}
echo "\n\t<!--heading1-->\n\t<div class=\"heading1\">\n\t<!--heading1-->\n\t\n\t\t<h2>";
echo sprintf(__("update whois data for domain"), $domain->Domain . "." . $domain->Tld);
echo "</h2>\n\t\t\n\t<!--heading1-->\n\t</div><hr />\n\t<!--heading1-->\n\t\n\t<!--box1-->\n\t<div class=\"box2\" id=\"tabs\">\n\t<!--box1-->\n\t\n\t\t<!--top-->\n\t\t<div class=\"top\">\n\t\t<!--top-->\n\t\t\n\t\t\t<ul class=\"list3\">\n\t\t\t\t<li class=\"on\"><a href=\"#tab-owner\">";
echo __("domain owner handle");
echo "</a></li>\n\t\t\t\t<li><a href=\"#tab-admin\">";
echo __("domain admin handle");
echo "</a></li>\n\t\t\t\t<li><a href=\"#tab-tech\">";
echo __("domain tech handle");
echo "</a></li>\n\t\t\t</ul>\n\t\t\t\n\t\t<!--top-->\n\t\t</div>\n\t\t<!--top-->\n\n\t\t";
$handle_types = ["Owner", "Admin", "Tech"];
foreach ($handle_types as $handle_type) {
    $tmp_handle = $domain->Handles->{$handle_type};
    echo "\n\t\t\t<!--content-->\n\t\t\t<div class=\"content\" id=\"tab-";
    echo strtolower($handle_type);
    echo "\">\n\t\t\t<!--content-->\n\t\t\t\n\t\t\t";
    if(isset($other_domains[strtolower($handle_type)]) && 1 < $other_domains[strtolower($handle_type)]) {
        echo "\t\t\t<div class=\"setting_help_box\">\n\t\t\t\t<strong>";
        echo __("update whois multiple domain title");
        echo "</strong><br />\n\t\t\t\t";
        echo sprintf(__("update whois multiple domain message"), $tmp_handle->Handle, $other_domains[strtolower($handle_type)] - 1, "<a href=\"handles.php?page=edit&amp;id=" . $tmp_handle->Identifier . "\" class=\"a1 c1\">" . sprintf(__("update whois multiple domain editpage handle"), $tmp_handle->Handle) . "</a>");
        echo "\t\t\t</div><br />\n\t\t\t";
    }
    echo "\t\n\t\t\t\t<!--split2-->\n\t\t\t\t<div class=\"split2\">\n\t\t\t\t<!--split2-->\n\t\t\t\t\n\t\t\t\t\t<!--left-->\n\t\t\t\t\t<div class=\"left\">\n\t\t\t\t\t<!--left-->\n\t\t\t\t\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t<div class=\"box3\"><h3>";
    echo __("domain " . strtolower($handle_type) . " handle");
    echo "</h3><div class=\"content\">\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t";
    if($handle_type != "Owner") {
        echo "\t\t\t\t\t\t\t<input type=\"radio\" id=\"";
        echo strtolower($handle_type) . "c";
        echo "_radio_owner\" name=\"";
        echo strtolower($handle_type) . "c";
        echo "\" value=\"owner\" ";
        if(isset($_POST[strtolower($handle_type) . "c"]) && $_POST[strtolower($handle_type) . "c"] == "owner" || !isset($_POST[strtolower($handle_type) . "c"]) && $tmp_handle->Identifier == $domain->Handles->Owner->Identifier) {
            echo "checked=\"checked\"";
        }
        echo " class=\"whois_contact_changer\"/> <label for=\"";
        echo strtolower($handle_type) . "c";
        echo "_radio_owner\">";
        echo __("same handle as owner");
        echo "</label><br />\n\t\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<input type=\"radio\" id=\"";
    echo strtolower($handle_type) . "c";
    echo "_radio_handle\" name=\"";
    echo strtolower($handle_type) . "c";
    echo "\" value=\"handle\" ";
    if(isset($_POST[strtolower($handle_type) . "c"]) && $_POST[strtolower($handle_type) . "c"] == "handle" || !isset($_POST[strtolower($handle_type) . "c"]) && $handle_type == "Owner" || $tmp_handle->Identifier != $domain->Handles->Owner->Identifier) {
        echo "checked=\"checked\"";
    }
    echo " class=\"whois_contact_changer\"/> <label for=\"";
    echo strtolower($handle_type) . "c";
    echo "_radio_handle\">";
    echo __("use an existing handle");
    echo "</label><br />\n\t\t\t\t\t\t\t<input type=\"radio\" id=\"";
    echo strtolower($handle_type) . "c";
    echo "_radio_new\" name=\"";
    echo strtolower($handle_type) . "c";
    echo "\" value=\"new\" ";
    if(isset($_POST[strtolower($handle_type) . "c"]) && $_POST[strtolower($handle_type) . "c"] == "new") {
        echo "checked=\"checked\"";
    }
    echo " class=\"whois_contact_changer\"/> <label for=\"";
    echo strtolower($handle_type) . "c";
    echo "_radio_new\">";
    echo __("use a new handle");
    echo "</label><br />\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<div id=\"div_";
    echo strtolower($handle_type) . "c";
    echo "_handle\" ";
    if(isset($_POST[strtolower($handle_type) . "c"]) && $_POST[strtolower($handle_type) . "c"] != "handle" || !isset($_POST[strtolower($handle_type) . "c"]) && $handle_type != "Owner" && $tmp_handle->Identifier == $domain->Handles->Owner->Identifier) {
        echo "class=\"hide\"";
    }
    echo ">\n\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t<strong class=\"title\"><span>";
    echo __("use handle");
    echo "</span></strong>\n\t\t\t\t\t\t\t<select name=\"";
    echo strtolower($handle_type);
    echo "Handle\" class=\"text1 size4f whois_contact_changer_select\">\n\t\t\t\t\t\t\t\t";
    $selected_handle = isset($_POST[strtolower($handle_type) . "Handle"]) ? $_POST[strtolower($handle_type) . "Handle"] : $tmp_handle->Identifier;
    $counter = 0;
    $html2 = "<optgroup label=\"" . __("handles from debtor") . "\">";
    foreach ($list_debtor_handles as $k => $v) {
        if(is_numeric($k) && ($domain->Registrar == $v["Registrar"] || $v["Registrar"] == "")) {
            $html2 .= "<option value=\"" . $v["id"] . "\" " . ($v["id"] == $selected_handle ? "selected=\"selected\"" : "") . ">" . $v["Handle"] . ($v["Name"] ? " " . $v["Name"] : "") . " - " . ($v["CompanyName"] ? $v["CompanyName"] : $v["SurName"]) . "</option>";
            $counter++;
        }
    }
    $html2 .= "</optgroup>";
    if(0 < $counter) {
        echo $html2;
    }
    $counter = 0;
    $html2 = "<optgroup label=\"" . __("general handles") . "\">";
    foreach ($list_general_handles as $k => $v) {
        if(is_numeric($k) && empty($v["Debtor"]) && ($domain->Registrar == $v["Registrar"] || $v["Registrar"] == "")) {
            $html2 .= "<option value=\"" . $v["id"] . "\" " . ($v["id"] == $selected_handle ? "selected=\"selected\"" : "") . ">" . $v["Handle"] . ($v["Name"] ? " " . $v["Name"] : "") . " - " . ($v["CompanyName"] ? $v["CompanyName"] : $v["SurName"]) . "</option>";
            $counter++;
        }
    }
    $html2 .= "</optgroup>";
    if(0 < $counter) {
        echo $html2;
    }
    echo "\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t";
    if(isset($_POST[strtolower($handle_type) . "Handle"]) && $_POST[strtolower($handle_type) . "Handle"] != $tmp_handle->Identifier) {
        echo "\t\t\t\t\t\t\t<script language=\"javascript\" type=\"text/javascript\">\n\t\t\t\t\t\t\t\$(function(){\n\t\t\t\t\t\t\t\t\$('select[name=\"";
        echo strtolower($handle_type);
        echo "Handle\"]').change();\n\t\t\t\t\t\t\t});\n\t\t\t\t\t\t\t</script>\n\t\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\n\t\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t</div></div>\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t";
    if($handle_type == "Owner" && $domain->OwnerChangeCostLabel) {
        echo "\t\t\t\t\t\t<input type=\"hidden\" name=\"cost_owner_current_handle\" value=\"";
        echo $domain->ownerHandle;
        echo "\" />\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t<div id=\"cost_owner_change_div\" class=\"box3 ";
        if(!isset($_POST[strtolower($handle_type) . "c"]) || $_POST[strtolower($handle_type) . "c"] != "new" && $_POST[strtolower($handle_type) . "Handle"] != $tmp_handle->Identifier) {
            echo "hide";
        }
        echo "\"><h3>";
        echo __("cost domain ownerchange");
        echo "</h3><div class=\"content\">\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\t";
        echo __("cost domain ownerchange ask 1");
        echo "<br />\n\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t<input type=\"checkbox\" id=\"costs_billing_checkbox\" name=\"Costs_Billing\" value=\"yes\" ";
        if(isset($_POST["Costs_Billing"]) && $_POST["Costs_Billing"] == "yes" || !isset($_POST["id"])) {
            echo "checked=\"checked\"";
        }
        echo "/> <label for=\"costs_billing_checkbox\">";
        echo sprintf(__("cost domain ownerchange ask 2"), $domain->OwnerChangeCostLabel);
        echo "</label><br />\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t</div></div>\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t<div class=\"box3 ";
    if(!isset($_POST[strtolower($handle_type) . "c"]) || $_POST[strtolower($handle_type) . "c"] != "new") {
        echo "hide";
    }
    echo " ";
    echo strtolower($handle_type) . "_newhandle";
    echo "\"><h3>";
    echo __("contact data");
    echo "</h3><div class=\"content\">\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<strong class=\"title\">";
    echo __("phonenumber");
    echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"";
    echo strtolower($handle_type);
    echo "PhoneNumber\" value=\"";
    echo isset($_POST[strtolower($handle_type) . "PhoneNumber"]) ? htmlspecialchars(esc($_POST[strtolower($handle_type) . "PhoneNumber"])) : $tmp_handle->PhoneNumber;
    echo "\" ";
    $ti = tabindex($ti);
    echo " maxlength=\"25\"/>\n\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<strong class=\"title\">";
    echo __("faxnumber");
    echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"";
    echo strtolower($handle_type);
    echo "FaxNumber\" value=\"";
    echo isset($_POST[strtolower($handle_type) . "FaxNumber"]) ? htmlspecialchars(esc($_POST[strtolower($handle_type) . "FaxNumber"])) : $tmp_handle->FaxNumber;
    echo "\" ";
    $ti = tabindex($ti);
    echo " maxlength=\"25\"/>\n\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<strong class=\"title\">";
    echo __("emailaddress");
    echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"";
    echo strtolower($handle_type);
    echo "EmailAddress\" value=\"";
    echo isset($_POST[strtolower($handle_type) . "EmailAddress"]) ? htmlspecialchars(esc($_POST[strtolower($handle_type) . "EmailAddress"])) : $tmp_handle->EmailAddress;
    echo "\" ";
    $ti = tabindex($ti);
    echo "/>\n\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t</div></div>\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\n\t\t\t\t\t<!--left-->\n\t\t\t\t\t</div>\n\t\t\t\t\t<!--left-->\n\t\n\t\t\t\t\t<!--right-->\n\t\t\t\t\t<div class=\"right\">\n\t\t\t\t\t<!--right-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t<div class=\"box3 ";
    if(isset($_POST[strtolower($handle_type) . "c"]) && $_POST[strtolower($handle_type) . "c"] == "new") {
        echo "hide";
    }
    echo " ";
    echo strtolower($handle_type) . "_existinghandle";
    echo "\"><h3>";
    echo __("handle data");
    echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t\t\t<!--box3-->\n\n\t\t\t\t\t\t\t<div class=\"contact_placeholder\">\n\t\t\t\t\t\t\t\t<strong class=\"title2_sub\" style=\"text-align:right;color:#222\">";
    echo __("domain " . strtolower($handle_type) . " handle");
    echo "</strong>\n\t\t\t\t\t\t\t\t";
    if(!$tmp_handle->Handle) {
        echo "\t\t\t\t\t\t\t\t\t<span class=\"title2_sub_value c3\">";
        echo __("could not found handle in software");
        echo "</span>\n\t\t\t\t\t\t\t\t";
    } else {
        echo "\t\t\t\t\t\t\t\t\t<span class=\"title2_sub_value\"><a href=\"handles.php?page=show&amp;id=";
        echo $tmp_handle->Identifier;
        echo "\" class=\"a1 c1\" id=\"link_";
        echo strtolower($handle_type);
        echo "_handle\">";
        echo $tmp_handle->Handle;
        echo "</a>   <a class=\"a1 contact_toggler smallfont floatr\" style=\"color:#ccc !important;\">";
        echo __("toggle handle");
        echo "</a></span>\n\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t<div id=\"div_";
        echo strtolower($handle_type);
        echo "_registrarhandle\" style=\"";
        if(!$tmp_handle->RegistrarHandle || $tmp_handle->RegistrarHandle == $tmp_handle->Handle) {
            echo "display:none";
        }
        echo "\">\n\t\t\t\t\t\t\t\t\t\t<div class=\"title2_sub align_right\">";
        echo __("registrarhandle");
        echo "</div>\n\t\t\t\t\t\t\t\t\t\t<div id=\"label_";
        echo strtolower($handle_type);
        echo "_registrarhandle\" class=\"title2_sub_value\">";
        echo $tmp_handle->RegistrarHandle;
        echo "</div>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t<div id=\"div_";
        echo strtolower($handle_type);
        echo "_company\" style=\"";
        if(!$tmp_handle->CompanyName) {
            echo "display:none";
        }
        echo "\">\n\t\t\t\t\t\t\t\t\t\t<div class=\"title2_sub align_right\">";
        echo __("companyname");
        echo "</div>\n\t\t\t\t\t\t\t\t\t\t<div id=\"label_";
        echo strtolower($handle_type);
        echo "_companyname\" class=\"title2_sub_value\">";
        echo $tmp_handle->CompanyName;
        echo "</div>\n\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t\t<div class=\"title2_sub hide align_right\">";
        echo __("company number");
        echo "</div>\n\t\t\t\t\t\t\t\t\t\t<div id=\"label_";
        echo strtolower($handle_type);
        echo "_company_number\" class=\"title2_sub_value hide\">";
        if($tmp_handle->CompanyNumber) {
            echo $tmp_handle->CompanyNumber;
        } else {
            echo "-";
        }
        echo "</div>\n\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t\t<div class=\"title2_sub hide align_right\">";
        echo __("taxnumber");
        echo "</div>\n\t\t\t\t\t\t\t\t\t\t<div id=\"label_";
        echo strtolower($handle_type);
        echo "_taxnumber\" class=\"title2_sub_value hide\">";
        if($tmp_handle->TaxNumber) {
            echo $tmp_handle->TaxNumber;
        } else {
            echo "-";
        }
        echo "</div>\n\t\t\t\t\t\t\t\t\t\t\n                                        ";
        if(!empty($array_legaltype)) {
            echo "\t\t\t\t\t\t\t\t\t\t<div class=\"title2_sub hide align_right\">";
            echo __("legal form");
            echo "</div>\n\t\t\t\t\t\t\t\t\t\t<div id=\"label_";
            echo strtolower($handle_type);
            echo "_legal_form\" class=\"title2_sub_value hide\">";
            if($tmp_handle->LegalForm) {
                echo $array_legaltype[$tmp_handle->LegalForm];
            } else {
                echo "-";
            }
            echo "</div>\n                                        ";
        }
        echo "\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t<div class=\"title2_sub align_right\">";
        echo __("contact person");
        echo "</div>\n\t\t\t\t\t\t\t\t\t<div id=\"label_";
        echo strtolower($handle_type);
        echo "_contact_person\" class=\"title2_sub_value\">";
        if($tmp_handle->Initials . $tmp_handle->SurName) {
            echo $tmp_handle->Initials . " " . $tmp_handle->SurName;
        } else {
            echo "-";
        }
        echo "</div>\n\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t<div class=\"title2_sub align_right\">";
        echo __("address");
        echo "</div>\n\t\t\t\t\t\t\t\t\t<div id=\"label_";
        echo strtolower($handle_type);
        echo "_address\" class=\"title2_sub_value\">";
        if($tmp_handle->Address) {
            echo $tmp_handle->Address;
        } else {
            echo "-";
        }
        echo "</div>\n\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t<div class=\"title2_sub align_right\">";
        echo __("zipcode and city");
        echo "</div>\n\t\t\t\t\t\t\t\t\t<div id=\"label_";
        echo strtolower($handle_type);
        echo "_zipcode_city\" class=\"title2_sub_value\">";
        if($tmp_handle->ZipCode . $tmp_handle->City) {
            echo $tmp_handle->ZipCode . " " . $tmp_handle->City;
        } else {
            echo "-";
        }
        echo "</div>\n\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t<div class=\"title2_sub align_right\">";
        echo __("country");
        echo "</div>\n\t\t\t\t\t\t\t\t\t<div id=\"label_";
        echo strtolower($handle_type);
        echo "_country\" class=\"title2_sub_value\">";
        if($tmp_handle->Country) {
            echo $array_country[$tmp_handle->Country];
        } else {
            echo "-";
        }
        echo "</div>\n\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t<div class=\"title2_sub hide align_right\">";
        echo __("emailaddress");
        echo "</div>\n\t\t\t\t\t\t\t\t\t<div id=\"label_";
        echo strtolower($handle_type);
        echo "_emailaddress\" class=\"title2_sub_value hide\">";
        if($tmp_handle->EmailAddress) {
            echo $tmp_handle->EmailAddress;
        } else {
            echo "-";
        }
        echo "</div>\n\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t<div class=\"title2_sub hide align_right\">";
        echo __("phonenumber");
        echo "</div>\n\t\t\t\t\t\t\t\t\t<div id=\"label_";
        echo strtolower($handle_type);
        echo "_phonenumber\" class=\"title2_sub_value hide\">";
        if($tmp_handle->PhoneNumber) {
            echo $tmp_handle->PhoneNumber;
        } else {
            echo "-";
        }
        echo "</div>\n\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t<div class=\"title2_sub hide align_right\">";
        echo __("faxnumber");
        echo "</div>\n\t\t\t\t\t\t\t\t\t<div id=\"label_";
        echo strtolower($handle_type);
        echo "_faxnumber\" class=\"title2_sub_value hide\">";
        if($tmp_handle->FaxNumber) {
            echo $tmp_handle->FaxNumber;
        } else {
            echo "-";
        }
        echo "</div>\n\t\t\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t</div></div>\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3 ";
    if(!isset($_POST[strtolower($handle_type) . "c"]) || $_POST[strtolower($handle_type) . "c"] != "new") {
        echo "hide";
    }
    echo " ";
    echo strtolower($handle_type) . "_newhandle";
    echo "\"><h3>";
    echo __("handle data");
    echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
    echo __("companyname");
    echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"";
    echo strtolower($handle_type);
    echo "CompanyName\" value=\"";
    echo isset($_POST[strtolower($handle_type) . "CompanyName"]) ? htmlspecialchars(esc($_POST[strtolower($handle_type) . "CompanyName"])) : $tmp_handle->CompanyName;
    echo "\" maxlength=\"100\" ";
    $ti = tabindex($ti);
    echo " />\t\t\t\t\t\t\t\n\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t<div class=\"CompanyName_extra ";
    if(isset($_POST[strtolower($handle_type) . "CompanyName"]) && !$_POST[strtolower($handle_type) . "CompanyName"] || !isset($_POST[strtolower($handle_type) . "CompanyName"]) && !$tmp_handle->CompanyName) {
        echo "hide";
    }
    echo "\">\n\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t<strong class=\"title\">";
    echo __("company number");
    echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"";
    echo strtolower($handle_type);
    echo "CompanyNumber\" value=\"";
    echo isset($_POST[strtolower($handle_type) . "CompanyNumber"]) ? htmlspecialchars(esc($_POST[strtolower($handle_type) . "CompanyNumber"])) : $tmp_handle->CompanyNumber;
    echo "\" ";
    $ti = tabindex($ti);
    echo " maxlength=\"20\"/>\n\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<strong class=\"title\">";
    echo __("vat number");
    echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"";
    echo strtolower($handle_type);
    echo "TaxNumber\" value=\"";
    echo isset($_POST[strtolower($handle_type) . "TaxNumber"]) ? htmlspecialchars(esc($_POST[strtolower($handle_type) . "TaxNumber"])) : $tmp_handle->TaxNumber;
    echo "\" ";
    $ti = tabindex($ti);
    echo " maxlength=\"20\"/>\n\t\t\t\t\t\t\t<span id=\"vat_status_";
    echo strtolower($handle_type);
    echo "\" class=\"vat_status\"></span>\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\n                            ";
    if(!empty($array_legaltype)) {
        echo "\t\t\t\t\t\t\t<strong class=\"title\">";
        echo __("legal form");
        echo "</strong>\n\t\t\t\t\t\t\t<select name=\"";
        echo strtolower($handle_type);
        echo "LegalForm\" class=\"text1 size4\">\n\t\t\t\t\t\t\t";
        foreach ($array_legaltype as $key => $value) {
            echo "\t\t\t\t\t\t\t\t<option value=\"";
            echo $key;
            echo "\" ";
            if(isset($_POST[strtolower($handle_type) . "LegalForm"]) && $_POST[strtolower($handle_type) . "LegalForm"] == $key || !isset($_POST[strtolower($handle_type) . "LegalForm"]) && $tmp_handle->LegalForm == $key) {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo $value;
            echo "</option>\n\t\t\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\t\t</select>\n\t\t\t\n\t\t\t\t\t\t\t<br />\n                            ";
    }
    echo "\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<br />\n\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
    echo __("contact person");
    echo "</strong>\n\t\t\t\t\t\t<select name=\"";
    echo strtolower($handle_type);
    echo "Sex\" class=\"text1 size6\" ";
    $ti = tabindex($ti);
    echo ">\n\t\t\t\t\t\t";
    foreach ($array_sex as $k => $v) {
        echo "\t\t\t\t\t\t\t<option value=\"";
        echo $k;
        echo "\" ";
        if(isset($_POST[strtolower($handle_type) . "Sex"]) && $_POST[strtolower($handle_type) . "Sex"] == $k || !isset($_POST[strtolower($handle_type) . "Sex"]) && $tmp_handle->Sex == $k) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo $v;
        echo "</option>\n\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t</select>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size2\" name=\"";
    echo strtolower($handle_type);
    echo "Initials\" value=\"";
    echo isset($_POST[strtolower($handle_type) . "Initials"]) ? htmlspecialchars(esc($_POST[strtolower($handle_type) . "Initials"])) : $tmp_handle->Initials;
    echo "\" ";
    $ti = tabindex($ti);
    echo " maxlength=\"25\"/> \n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"";
    echo strtolower($handle_type);
    echo "SurName\" value=\"";
    echo isset($_POST[strtolower($handle_type) . "SurName"]) ? htmlspecialchars(esc($_POST[strtolower($handle_type) . "SurName"])) : $tmp_handle->SurName;
    echo "\" ";
    $ti = tabindex($ti);
    echo " maxlength=\"100\"/>\t\t\t\t\t\t\t\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
    echo __("address");
    echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"";
    echo strtolower($handle_type);
    echo "Address\" value=\"";
    echo isset($_POST[strtolower($handle_type) . "Address"]) ? htmlspecialchars(esc($_POST[strtolower($handle_type) . "Address"])) : $tmp_handle->Address;
    echo "\" ";
    $ti = tabindex($ti);
    echo " maxlength=\"100\"/>\t\t\t\t\t\t\t\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
    echo __("zipcode and city");
    echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size2\" name=\"";
    echo strtolower($handle_type);
    echo "ZipCode\" value=\"";
    echo isset($_POST[strtolower($handle_type) . "ZipCode"]) ? htmlspecialchars(esc($_POST[strtolower($handle_type) . "ZipCode"])) : $tmp_handle->ZipCode;
    echo "\" ";
    $ti = tabindex($ti);
    echo " maxlength=\"10\"/> <input type=\"text\" class=\"text1 size1\" name=\"";
    echo strtolower($handle_type);
    echo "City\" value=\"";
    echo isset($_POST[strtolower($handle_type) . "City"]) ? htmlspecialchars(esc($_POST[strtolower($handle_type) . "City"])) : $tmp_handle->City;
    echo "\" ";
    $ti = tabindex($ti);
    echo " maxlength=\"100\"/>\t\t\t\t\t\t\t\n\t\t\t\t\t\t<br /><br />\n\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
    echo __("country");
    echo "</strong>\n\t\t\t\t\t\t<select class=\"text1 size4\" name=\"";
    echo strtolower($handle_type);
    echo "Country\" ";
    $ti = tabindex($ti);
    echo ">\n\t\t\t\t\t\t";
    foreach ($array_country as $key => $value) {
        echo "\t\t\t\t\t\t\t<option value=\"";
        echo $key;
        echo "\" ";
        if(isset($_POST[strtolower($handle_type) . "Country"]) && $_POST[strtolower($handle_type) . "Country"] == $key || !isset($_POST[strtolower($handle_type) . "Country"]) && $tmp_handle->Country == $key) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo $value;
        echo "</option>\n\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t</select>\n\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t";
    if(0 < count($handle->customfields_list)) {
        echo "\t\t\t\t\t\t\n\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t";
        if($tmp_handle->Handle) {
            echo "\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t<div class=\"box3 ";
            if(isset($_POST[strtolower($handle_type) . "c"]) && $_POST[strtolower($handle_type) . "c"] == "new") {
                echo "hide";
            }
            echo " ";
            echo strtolower($handle_type) . "_existinghandle";
            echo "\"><h3>";
            echo __("custom debtor fields h2");
            echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t\t\t<!--box3-->\n\n\t\t\t\t\t\t\t";
            foreach ($tmp_handle->customfields_list as $k => $custom_field) {
                echo show_custom_field_handlepage(strtolower($handle_type), $custom_field, isset($tmp_handle->custom->{$custom_field["FieldCode"]}) ? $tmp_handle->custom->{$custom_field["FieldCode"]} : "");
            }
            echo "\t\t\t\t\t\t\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t</div></div>\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\t\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t<div class=\"box3 ";
        if(!isset($_POST[strtolower($handle_type) . "c"]) || $_POST[strtolower($handle_type) . "c"] != "new") {
            echo "hide";
        }
        echo " ";
        echo strtolower($handle_type) . "_newhandle";
        echo "\"><h3>";
        echo __("custom debtor fields h2");
        echo "</h3><div class=\"content\">\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t";
        foreach ($tmp_handle->customfields_list as $k => $custom_field) {
            $custom_value = isset($tmp_handle->customvalues[$custom_field["FieldCode"]]) ? $tmp_handle->customvalues[$custom_field["FieldCode"]] : NULL;
            echo "\t\t\t\t\t\t\t<strong class=\"title\">";
            echo htmlspecialchars($custom_field["LabelTitle"]);
            echo "</strong>\n\t\t\t\t\t\t\t";
            echo show_custom_input_field($custom_field, $custom_value, strtolower($handle_type));
            echo "\t\t\t\t\t\t\t";
            if($k + 1 != count($tmp_handle->customfields_list)) {
                echo "<br /><br />";
            }
        }
        echo "\t\t\t\t\t\t\t\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t</div></div>\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t";
    }
    echo "\t\t\t\n\t\t\t\t\t<!--right-->\n\t\t\t\t\t</div>\n\t\t\t\t\t<!--right-->\n\t\t\t\t\t\n\t\t\t\t<!--split2-->\n\t\t\t\t</div>\n\t\t\t\t<!--split2-->\n\t\t\t\t\t\n\t\t\t<!--content-->\n\t\t\t</div>\n\t\t\t<!--content-->\n\n\t\t";
}
echo "\n\t<!--box1-->\n\t</div>\n\t<!--box1-->\n\t\n\t\t<br />\n\t\t\t\t\n\t\t<p class=\"align_right\">\n\t\t\t";
if($domain->Status == 4) {
    echo "\t\t\t<input type=\"checkbox\" id=\"update_at_registrar_yes\" name=\"update_at_registrar\" value=\"yes\" checked=\"checked\"/> <label for=\"update_at_registrar_yes\">";
    echo __("also update whoisdata at registrar");
    echo "</label><br /><br />\n\t\t\t";
}
echo "\t\t\t<a class=\"button1 alt1\" id=\"form_create_btn\">\n                <span>";
echo __("btn edit");
echo "</span>\n            </a>\n\t\t</p>\n\t\t\n\t<!--form-->\n\t</fieldset></form>\n\t<!--form-->\n\n<div id=\"div_for_handlesearch\"></div>\n\n";
require_once "views/footer.php";

?>