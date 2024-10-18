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
    echo "\t\t<div class=\"mark alt1\">\n\t\t\t<strong>";
    echo __("warning");
    echo "</strong><br />\n\t\t\t<ul>\n\t\t\t\t<li>";
    echo __("this domain has been deleted. therefore no changes can be made");
    echo "</li>\n\t\t\t</ul>\n\t\t</div>\n\t\t<br />\n\t\t";
}
echo "\n<div class=\"heading1\">\n\n\t<h2>";
echo __("modification domain") . " " . $ClientareaChange->ReferenceObject->Domain . "." . $ClientareaChange->ReferenceObject->Tld;
echo "</h2>\n\n\t<a href=\"domains.php?page=show&amp;id=";
echo $ClientareaChange->ReferenceObject->Identifier;
echo "\" class=\"a1 c1 floatr\">\n\t\t<span>";
echo __("goto domain");
echo "</span>\n\t</a>\n\n</div>\n<hr />\n\n<form name=\"clientarea_change_form\" id=\"clientarea_change_whois\" method=\"post\" action=\"clientareachanges.php?page=";
echo $form_action;
echo "&amp;id=";
echo $ClientareaChange->id;
echo "\">\n\n\t<div id=\"tabs\" class=\"box2\">\n\n\t<div class=\"top\">\n\t\t<ul class=\"list3\">\n\t\t\t";
$counter = 0;
foreach ($ClientareaChange->Data as $_handle_type => $_handle) {
    echo "\t\t\t\t<li ";
    echo $counter === 0 ? "class=\"on\"" : "";
    echo ">\n\t\t\t\t\t<a href=\"#tab-";
    echo strtolower($_handle_type);
    echo "\">\n\t\t\t\t\t\t";
    echo __("domain " . strtolower($_handle_type) . " handle");
    echo "\t\t\t\t\t</a>\n\t\t\t\t</li>\n\t\t\t\t";
    $counter++;
}
echo "\t\t</ul>\n\t</div>\n\n";
foreach ($ClientareaChange->Data as $_handle_type => $_handle_new) {
    $_handle_current = $ClientareaChange->ReferenceObject->Handles->{$_handle_type};
    echo "\t\t<div class=\"content\" id=\"tab-";
    echo strtolower($_handle_type);
    echo "\">\n\n\t\t\t<div class=\"split2\">\n\t\t\t\t<div class=\"left\">\n\n\t\t\t\t\t<div class=\"box3\">\n\t\t\t\t\t\t<h3>";
    echo __("handle data");
    echo "</h3>\n\t\t\t\t\t\t<div class=\"content\">\n\n\t\t\t\t\t\t\t<strong class=\"title\">\n\t\t\t\t\t\t\t\t";
    echo __("companyname");
    echo "\t\t\t\t\t\t\t\t";
    if($_handle_current->CompanyName != $_handle_new->CompanyName) {
        echo "\t\t\t\t\t\t\t\t\t<span class=\"smallfont marleft_1 normalfont\">\n\t\t\t\t\t\t\t\t(";
        echo __("before");
        echo ": ";
        echo $_handle_current->CompanyName;
        echo ")\n\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t<input id=\"";
    echo $_handle_type;
    echo "[CompanyName]\" name=\"";
    echo $_handle_type;
    echo "[CompanyName]\" ";
    echo $_handle_current->CompanyName != $_handle_new->CompanyName ? "value=\"" . $_handle_new->CompanyName . "\" class=\"text1 size1 modified\"" : "value=\"" . $_handle_current->CompanyName . "\" class=\"text1 size1\"";
    echo " />\n\n\t\t\t\t\t\t\t<br/>\n\t\t\t\t\t\t\t<br/>\n\n\t\t\t\t\t\t\t";
    if(!empty($array_legaltype)) {
        echo "\t\t\t\t\t\t\t\t<strong class=\"title\">\n\t\t\t\t\t\t\t\t\t";
        echo __("legal form");
        echo "\t\t\t\t\t\t\t\t\t";
        if($_handle_current->LegalForm != $_handle_new->LegalForm) {
            echo "\t\t\t\t\t\t\t\t\t\t<span class=\"smallfont marleft_1 normalfont\">\n\t\t\t\t\t\t\t\t\t(";
            echo __("before");
            echo "\t\t\t\t\t\t\t\t\t: ";
            echo $array_legaltype[$_handle_current->LegalForm];
            echo ")\n\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t\t";
        $tmp_legalform = $_handle_current->LegalForm != $_handle_new->LegalForm && $_handle_new->LegalForm != "" ? $_handle_new->LegalForm : $_handle_current->LegalForm;
        echo "\t\t\t\t\t\t\t\t<select name=\"";
        echo $_handle_type;
        echo "[LegalForm]\" class=\"select1\" ";
        if($_handle_current->LegalForm == $_handle_new->LegalForm || $_handle_new->LegalForm == "") {
            echo "class=\"form_input_grey\"";
        }
        echo ">\n\t\t\t\t\t\t\t\t\t<option value=\"\" ";
        if("" == $tmp_legalform) {
            echo "selected=\"selected\"";
        }
        echo " style=\"width:200px;\">Particulier\n\t\t\t\t\t\t\t\t\t</option>\n\t\t\t\t\t\t\t\t\t";
        foreach ($array_legaltype as $key => $legalform) {
            echo "\t\t\t\t\t\t\t\t\t\t<option value=\"";
            echo $key;
            echo "\" ";
            if($key == $tmp_legalform) {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo $legalform;
            echo "</option>\n\t\t\t\t\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\t\t\t</select>\n\n\t\t\t\t\t\t\t\t<br/>\n\t\t\t\t\t\t\t\t<br/>\n\n\t\t\t\t\t\t\t\t<strong class=\"title\">\n\t\t\t\t\t\t\t\t\t";
        echo __("company number");
        echo "\t\t\t\t\t\t\t\t\t";
        if($_handle_current->CompanyNumber != $_handle_new->CompanyNumber) {
            echo "\t\t\t\t\t\t\t\t\t\t<span class=\"smallfont marleft_1 normalfont\">\n\t\t\t\t\t\t\t\t\t(";
            echo __("before");
            echo ": ";
            echo $_handle_current->CompanyNumber;
            echo ")\n\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t\t<input name=\"";
        echo $_handle_type;
        echo "[CompanyNumber]\" ";
        echo $_handle_current->CompanyNumber != $_handle_new->CompanyNumber ? "value=\"" . $_handle_new->CompanyNumber . "\" class=\"text1 size1 modified\"" : "value=\"" . $_handle_current->CompanyNumber . "\" class=\"text1 size1\"";
        echo " />\n\n\t\t\t\t\t\t\t\t<br/>\n\t\t\t\t\t\t\t\t<br/>\n\n\t\t\t\t\t\t\t\t<strong class=\"title\">\n\t\t\t\t\t\t\t\t\t";
        echo __("taxnumber");
        echo "\t\t\t\t\t\t\t\t\t";
        if($_handle_current->TaxNumber != $_handle_new->TaxNumber) {
            echo "\t\t\t\t\t\t\t\t\t\t<span class=\"smallfont marleft_1 normalfont\">\n\t\t\t\t\t\t\t\t\t(";
            echo __("before");
            echo ": ";
            echo $_handle_current->TaxNumber;
            echo ")\n\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t\t<input name=\"";
        echo $_handle_type;
        echo "[TaxNumber]\" ";
        echo $_handle_current->TaxNumber != $_handle_new->TaxNumber ? "value=\"" . $_handle_new->TaxNumber . "\" class=\"text1 size1 modified\"" : "value=\"" . $_handle_current->TaxNumber . "\" class=\"text1 size1\"";
        echo " />\n\n\t\t\t\t\t\t\t\t<br/>\n\t\t\t\t\t\t\t\t<br/>\n\n\t\t\t\t\t\t\t\t";
    }
    echo "\n\t\t\t\t\t\t\t<strong class=\"title\">";
    echo __("name");
    echo "\t\t\t\t\t\t\t\t";
    if($_handle_current->Initials != $_handle_new->Initials || $_handle_current->SurName != $_handle_new->SurName) {
        echo "\t\t\t\t\t\t\t\t\t<span class=\"smallfont marleft_1 normalfont\">\n\t\t\t\t\t\t\t\t(";
        echo __("before");
        echo "\t\t\t\t\t\t\t\t: ";
        echo settings::getGenderTranslation($_handle_current->Sex) . " " . $_handle_current->Initials . " " . $_handle_current->SurName;
        echo "\t\t\t\t\t\t\t\t)\n\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t<select name=\"";
    echo $_handle_type;
    echo "[Sex]\" class=\"text1 size6\">\n\t\t\t\t\t\t\t\t";
    foreach ($array_sex as $k => $v) {
        echo "\t\t\t\t\t\t\t\t\t<option value=\"";
        echo $k;
        echo "\" ";
        if($_handle_new->Sex == $k) {
            echo "selected=\"selected\"";
        }
        echo ">\n\t\t\t\t\t\t\t\t\t\t";
        echo $v;
        echo "\t\t\t\t\t\t\t\t\t</option>\n\t\t\t\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t<input name=\"";
    echo $_handle_type;
    echo "[Initials]\" ";
    echo $_handle_current->Initials != $_handle_new->Initials ? "value=\"" . $_handle_new->Initials . "\" class=\"text1 size2 modified\"" : "value=\"" . $_handle_current->Initials . "\" class=\"text1 size2\"";
    echo " />\n\t\t\t\t\t\t\t<input name=\"";
    echo $_handle_type;
    echo "[SurName]\" ";
    echo $_handle_current->SurName != $_handle_new->SurName ? "value=\"" . $_handle_new->SurName . "\" class=\"text1 size1 modified\"" : "value=\"" . $_handle_current->SurName . "\" class=\"text1 size1\"";
    echo " />\n\n\t\t\t\t\t\t\t<br/>\n\t\t\t\t\t\t\t<br/>\n\n\t\t\t\t\t\t\t<strong class=\"title\">";
    echo __("address");
    echo "</strong>\n\t\t\t\t\t\t\t<input name=\"";
    echo $_handle_type;
    echo "[Address]\" ";
    echo $_handle_current->Address != $_handle_new->Address ? "value=\"" . $_handle_new->Address . "\" class=\"text1 size1 modified\"" : "value=\"" . $_handle_current->Address . "\" class=\"text1 size1\"";
    echo " />\n\t\t\t\t\t\t\t";
    if($_handle_current->Address != $_handle_new->Address) {
        echo "\t\t\t\t\t\t\t\t<span class=\"smallfont marleft_1 normalfont\">\n\t\t\t\t\t\t\t(";
        echo __("before");
        echo ": ";
        echo $_handle_current->Address;
        echo ")\n\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t";
    }
    if(IS_INTERNATIONAL) {
        echo "\t\t\t\t\t\t\t\t<strong class=\"title\">&nbsp;</strong>\n\t\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1 ";
        if($_handle_current->Address2 != $_handle_new->Address2) {
            echo "modified";
        }
        echo "\" name=\"";
        echo $_handle_type;
        echo "[Address2]\" value=\"";
        echo $_handle_current->Address2 != $_handle_new->Address2 ? $_handle_new->Address2 : $_handle_current->Address2;
        echo "\" maxlength=\"100\"/>\n\t\t\t\t\t\t\t\t";
        if($_handle_current->Address2 != $_handle_new->Address2) {
            echo "\t\t\t\t\t\t\t\t\t<span class=\"smallfont marleft_1 normalfont\">\n\t\t\t\t\t\t\t\t(";
            echo __("before");
            echo ": ";
            echo $_handle_current->Address2;
            echo ")\n\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t\t";
        }
    }
    echo "\t\t\t\t\t\t\t<br/>\n\t\t\t\t\t\t\t<br/>\n\n\t\t\t\t\t\t\t<strong class=\"title\">\n\t\t\t\t\t\t\t\t";
    echo __("zipcode and city");
    echo "\t\t\t\t\t\t\t\t";
    if($_handle_current->ZipCode != $_handle_new->ZipCode || $_handle_current->City != $_handle_new->City) {
        echo "\t\t\t\t\t\t\t\t\t<span class=\"smallfont marleft_1 normalfont\">\n\t\t\t\t\t\t\t\t(";
        echo __("before");
        echo ": ";
        echo $_handle_current->ZipCode;
        echo ")\n\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t\t<span class=\"smallfont marleft_1 normalfont\">\n\t\t\t\t\t\t\t\t(";
        echo __("before");
        echo ": ";
        echo $_handle_current->City;
        echo ")\n\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t<input name=\"";
    echo $_handle_type;
    echo "[ZipCode]\" ";
    echo $_handle_current->ZipCode != $_handle_new->ZipCode ? "value=\"" . $_handle_new->ZipCode . "\" class=\"text1 size2 modified\"" : "value=\"" . $_handle_current->ZipCode . "\" class=\"text1 size2\"";
    echo " />\n\t\t\t\t\t\t\t<input name=\"";
    echo $_handle_type;
    echo "[City]\" ";
    echo $_handle_current->City != $_handle_new->City ? "value=\"" . $_handle_new->City . "\" class=\"text1 size1 modified\"" : "value=\"" . $_handle_current->City . "\" class=\"text1 size1\"";
    echo " />\n\n\t\t\t\t\t\t\t<br/>\n\t\t\t\t\t\t\t<br/>\n\n\t\t\t\t\t\t\t";
    $tmp_country = $_handle_current->Country != $_handle_new->Country ? $_handle_new->Country : $_handle_current->Country;
    if(IS_INTERNATIONAL) {
        $new_handle_state = isset($_handle_new->StateCode) && $_handle_new->StateCode ? $_handle_new->StateCode : $_handle_new->State;
        $tmp_class = $_handle_current->State != $new_handle_state ? "modified" : "";
        $tmp_state = $_handle_current->State != $new_handle_state ? $new_handle_state : $_handle_current->State;
        echo "\t\t\t\t\t\t\t\t<strong class=\"title\">\n\t\t\t\t\t\t\t\t\t";
        echo __("state");
        echo "\t\t\t\t\t\t\t\t\t";
        if($_handle_current->State != $new_handle_state) {
            echo "\t\t\t\t\t\t\t\t\t\t<span class=\"smallfont marleft_1 normalfont\">\n\t\t\t\t\t\t\t\t\t(";
            echo __("before");
            echo ": ";
            echo $_handle_current->StateName;
            echo ")\n\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\t\t\t</strong>\n\n\t\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1 ";
        echo $tmp_class;
        if(isset($array_states[$tmp_country])) {
            echo " hide";
        }
        echo "\" name=\"";
        echo $_handle_type;
        echo "[State]\" value=\"";
        if(!isset($array_states[$tmp_country])) {
            echo $tmp_state;
        }
        echo "\" maxlength=\"100\"\n\t\t\t\t\t\t\t\t/>\n\n\t\t\t\t\t\t\t\t<select class=\"text1 size4f ";
        echo $tmp_class;
        if(!isset($array_states[$tmp_country])) {
            echo " hide";
        }
        echo "\" name=\"";
        echo $_handle_type;
        echo "[StateCode]\"\n\t\t\t\t\t\t\t\t>\n\t\t\t\t\t\t\t\t\t<option value=\"\">";
        echo __("make your choice");
        echo "</option>\n\t\t\t\t\t\t\t\t\t";
        if(isset($array_states[$tmp_country])) {
            foreach ($array_states[$tmp_country] as $key => $value) {
                echo "\t\t\t\t\t\t\t\t\t\t\t<option value=\"";
                echo $key;
                echo "\" ";
                if($tmp_state == $key) {
                    echo "selected=\"selected\"";
                }
                echo ">";
                echo $value;
                echo "</option>\n\t\t\t\t\t\t\t\t\t\t";
            }
        }
        echo "\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t<br/>\n\t\t\t\t\t\t\t\t<br/>\n\t\t\t\t\t\t\t\t";
        unset($tmp_class);
        unset($tmp_state);
    }
    echo "\n\t\t\t\t\t\t\t<strong class=\"title\">\n\t\t\t\t\t\t\t\t";
    echo __("country");
    echo "\t\t\t\t\t\t\t\t";
    if($_handle_current->Country != $_handle_new->Country) {
        echo "\t\t\t\t\t\t\t\t\t<span class=\"smallfont marleft_1 normalfont\">\n\t\t\t\t\t\t\t\t(";
        echo __("before");
        echo ": ";
        echo $array_country[$_handle_current->Country];
        echo ")\n\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<select name=\"";
    echo $_handle_type;
    echo "[Country]\" class=\"select1 ";
    if($_handle_current->Country != $_handle_new->Country) {
        echo "modified";
    }
    echo "\" data-handletype=\"";
    echo $_handle_type;
    echo "\">\n\t\t\t\t\t\t\t\t";
    foreach ($array_country as $key => $country) {
        $has_states = isset($array_states[$key]) ? "true" : "false";
        echo "\t\t\t\t\t\t\t\t\t<option value=\"";
        echo $key;
        echo "\" ";
        if($key == $tmp_country) {
            echo "selected=\"selected\"";
        }
        echo " data-states=\"";
        echo $has_states;
        echo "\">\n\t\t\t\t\t\t\t\t\t\t";
        echo $country;
        echo "\t\t\t\t\t\t\t\t\t</option>\n\t\t\t\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\t</select>\n\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\n\t\t\t\t</div>\n\t\t\t\t<div class=\"right\">\n\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\">\n\t\t\t\t\t\t<h3>";
    echo __("contact data");
    echo "</h3>\n\t\t\t\t\t\t<div class=\"content\">\n\t\t\t\t\t\t\t<!--box3-->\n\n\t\t\t\t\t\t\t<strong class=\"title\">\n\t\t\t\t\t\t\t\t";
    echo __("phonenumber");
    echo "\t\t\t\t\t\t\t\t";
    if($_handle_current->PhoneNumber != $_handle_new->PhoneNumber) {
        echo "\t\t\t\t\t\t\t\t\t<span class=\"smallfont marleft_1 normalfont\">\n\t\t\t\t\t\t\t\t\t(";
        echo __("before");
        echo ": ";
        echo $_handle_current->PhoneNumber;
        echo ")\n\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t<input name=\"";
    echo $_handle_type;
    echo "[PhoneNumber]\" ";
    echo $_handle_current->PhoneNumber != $_handle_new->PhoneNumber ? "value=\"" . $_handle_new->PhoneNumber . "\" class=\"text1 size1 modified\"" : "value=\"" . $_handle_current->PhoneNumber . "\" class=\"text1 size1\"";
    echo " />\n\n\t\t\t\t\t\t\t<br/>\n\t\t\t\t\t\t\t<br/>\n\n\t\t\t\t\t\t\t<strong class=\"title\">\n\t\t\t\t\t\t\t\t";
    echo __("faxnumber");
    echo "\t\t\t\t\t\t\t\t";
    if($_handle_current->FaxNumber != $_handle_new->FaxNumber) {
        echo "\t\t\t\t\t\t\t\t\t<span class=\"smallfont marleft_1 normalfont\">\n\t\t\t\t\t\t\t\t\t(";
        echo __("before");
        echo ": ";
        echo $_handle_current->FaxNumber;
        echo ")\n\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t<input name=\"";
    echo $_handle_type;
    echo "[FaxNumber]\" ";
    echo $_handle_current->FaxNumber != $_handle_new->FaxNumber ? "value=\"" . $_handle_new->FaxNumber . "\" class=\"text1 size1 modified\"" : "value=\"" . $_handle_current->FaxNumber . "\" class=\"text1 size1\"";
    echo " />\n\n\t\t\t\t\t\t\t<br/>\n\t\t\t\t\t\t\t<br/>\n\n\t\t\t\t\t\t\t<strong class=\"title\">\n\t\t\t\t\t\t\t\t";
    echo __("emailaddress");
    echo "\t\t\t\t\t\t\t\t<span class=\"smallfont marleft_1 normalfont\">\n\t\t\t\t\t\t\t";
    if($_handle_current->EmailAddress != $_handle_new->EmailAddress) {
        echo trim($_handle_current->EmailAddress) == "" ? "(" . __("empty before") . ")" : "(" . __("before") . ": " . $_handle_current->EmailAddress . ")";
    }
    echo "\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t<input name=\"";
    echo $_handle_type;
    echo "[EmailAddress]\" ";
    echo $_handle_current->EmailAddress != $_handle_new->EmailAddress ? "value=\"" . $_handle_new->EmailAddress . "\" class=\"text1 size1 modified\"" : "value=\"" . $_handle_current->EmailAddress . "\" class=\"text1 size1\"";
    echo " />\n\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\n\t\t\t\t</div>\n\t\t\t</div>\n\t\t</div>\n\n\t\t";
}
echo "\t</div>\n\n\t<br/>\n\n</form>\n\n\n";
if(U_DOMAIN_EDIT) {
    $this->element("clientarea.changes.edit.php");
}
require_once "views/footer.php";

?>