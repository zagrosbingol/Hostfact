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
$page_form_title = 0 < $orderform->Identifier ? sprintf(__("edit orderform"), $orderform->Title) : __("add orderform");
$btn_form_title = 0 < $orderform->Identifier ? __("btn edit") : __("btn add");
$orderform_product_types = [];
$orderform_product_types["domain"] = ["title" => __("orderform type domain"), "tab" => __("orderform domain settings")];
$orderform_product_types["hosting"] = ["title" => __("orderform type hosting"), "tab" => __("orderform hosting settings")];
$orderform_product_types = do_filter("orderform_add_types", $orderform_product_types, ["orderform_id" => $orderform->Identifier]);
echo "<!--form-->\n<form id=\"OrderForm\" name=\"form_create\" method=\"post\" action=\"orderform.php\"><fieldset><legend>";
echo $page_form_title;
echo "</legend>\n";
if(0 < $orderform->Identifier) {
    echo "\t<input type=\"hidden\" name=\"id\" value=\"";
    echo $orderform->Identifier;
    echo "\" />\n";
}
echo "<!--form-->\n\n<!--heading1-->\n<div class=\"heading1\">\n<!--heading1-->\n\t<h2>";
echo $page_form_title;
echo "</h2>\n\t\n\t<p class=\"pos2\"><strong class=\"textsize1 pointer\" id=\"Available_text\" style=\"line-height: 22px\">";
echo $orderform_availability[$orderform->Available];
echo " ";
echo STATUS_CHANGE_ICON;
echo "</strong><select class=\"text1 size1 hide\" name=\"Available\">\n\t";
foreach ($orderform_availability as $k => $v) {
    echo "\t<option value=\"";
    echo $k;
    echo "\" ";
    if($orderform->Available == $k) {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo $v;
    echo "</option>\n\t";
}
echo "\t</select></p>\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n\t<!--box1-->\n\t<div class=\"box2\" id=\"tabs\">\n\t<!--box1-->\n\t\n\t\t<!--top-->\n\t\t<div class=\"top\">\n\t\t<!--top-->\n\t\t\n\t\t\t<ul class=\"list3\">\n\t\t\t\t<li class=\"on\"><a href=\"#tab-general\">";
echo __("general");
echo "</a></li>\n\t\t\t\t";
foreach ($orderform_product_types as $_tmp_product_type => $_tmp_product_type_info) {
    if($_tmp_product_type_info["tab"]) {
        echo "<li><a href=\"#tab-";
        echo $_tmp_product_type;
        echo "\">";
        echo $_tmp_product_type_info["tab"];
        echo "</a></li>";
    }
}
echo "\t\t\t\t<li><a href=\"#tab-custom\">";
echo __("orderform custom settings");
echo "</a></li>\n\t\t\t</ul>\n\t\t\n\t\t<!--top-->\n\t\t</div>\n\t\t<!--top-->\n\n\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-general\">\n\t\t<!--content-->\n\n\t\t\t<!--split2-->\n\t\t\t<div class=\"split2\">\n\t\t\t<!--split2-->\n\t\t\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t<div class=\"left\">\n\t\t\t\t<!--left-->\t\n\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("general");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("orderform name");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" name=\"Title\" class=\"text1 size1\" value=\"";
echo htmlspecialchars($orderform->Title);
echo "\" />\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("what do you want to sell");
echo "</strong>\n\t\t\t\t\t\t<select name=\"Type\" class=\"text1 size4f\">\n\t\t\t\t\t\t\t<option value=\"other\" ";
if($orderform->Type == "other") {
    echo "selected=\"selected\"";
}
echo ">";
echo __("orderform type other");
echo "</option>\n\t\t\t\t\t\t\t";
foreach ($orderform_product_types as $_tmp_product_type => $_tmp_product_type_info) {
    echo "<option value=\"";
    echo $_tmp_product_type;
    echo "\" ";
    if($orderform->Type == $_tmp_product_type) {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo $_tmp_product_type_info["title"];
    echo "</option>";
}
if($orderform->Type == "custom") {
    echo "                                <option value=\"custom\" ";
    if($orderform->Type == "custom") {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo __("orderform type custom");
    echo "</option>";
}
echo "\t\t\t\t\t\t</select>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("orderform language");
echo "</strong>\n\t\t\t\t\t\t<select name=\"Language\" class=\"text1 size4f\">\n\t\t\t\t\t\t\t";
foreach ($array_languages_orderform as $key => $value) {
    echo "\t\t\t\t\t\t\t<option value=\"";
    echo $key;
    echo "\" ";
    if($orderform->Language == $key) {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo $value;
    echo "</option>\n\t\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\n\t\t\t\t<!--left-->\n\t\t\t\t</div>\n\t\t\t\t<!--left-->\n\t\t\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t<div class=\"right\">\n\t\t\t\t<!--right-->\n\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("orderform options amounts");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\t\n\t\t\t\t\t\n\t\t\t\t\t\t<label><input name=\"ShowPrices\" type=\"checkbox\" value=\"yes\" ";
if($orderform->ShowPrices == "yes") {
    echo "checked=\"checked\"";
}
echo "/> ";
echo __("orderform show prices for products and options");
echo "</label><br />\n\t\t\t\t\t\t<label><input name=\"ShowDiscountCoupon\" type=\"checkbox\" value=\"yes\" ";
if($orderform->ShowDiscountCoupon == "yes") {
    echo "checked=\"checked\"";
}
echo "/> ";
echo __("orderform show discount coupon");
echo "</label><br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t";
if(!empty($array_taxpercentages)) {
    echo "\t\t\t\t\t\t\t<strong class=\"title\">";
    echo __("orderform options which vat calc method to use");
    echo "</strong>\n\t\t\t\t\t\t\t<label><input name=\"VatCalcMethod\" type=\"radio\" value=\"default\" ";
    if($orderform->VatCalcMethod == "default") {
        echo "checked=\"checked\"";
    }
    echo "/> ";
    echo __("orderform vatcalcmethod use default setting") . " (" . (VAT_CALC_METHOD == "incl" ? __("incl vat") : __("excl vat")) . ")";
    echo "</label><br />\n\t\t\t\t\t\t\t<label><input name=\"VatCalcMethod\" type=\"radio\" value=\"excl\" ";
    if($orderform->VatCalcMethod == "excl") {
        echo "checked=\"checked\"";
    }
    echo "/> ";
    echo __("orderform vatcalcmethod excl");
    echo "</label><br />\n\t\t\t\t\t\t\t<label><input name=\"VatCalcMethod\" type=\"radio\" value=\"incl\" ";
    if($orderform->VatCalcMethod == "incl") {
        echo "checked=\"checked\"";
    }
    echo "/> ";
    echo __("orderform vatcalcmethod incl");
    echo "</label><br />\n\t\t\t\t\t\t\t";
}
echo "\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\t\t\t\t\t\n\t\t\t\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t</div>\n\t\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t<!--split2-->\n\t\t\t</div>\n\t\t\t<!--split2-->\n\t\t\t\n\t\t\t<!--split2-->\n\t\t\t<div class=\"split2\">\n\t\t\t<!--split2-->\n\t\t\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t<div class=\"left\">\n\t\t\t\t<!--left-->\t\n\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("productgroups");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t<div class=\"isOtherEnabled ";
if($orderform->Type != "other") {
    echo "hide";
}
echo " \">\t\t\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("productgroup for other products");
echo "</strong>\n\t\t\t\t\t\t\t<select name=\"ProductGroups[main]\" class=\"text1 size4f\">\n\t\t\t\t\t\t\t\t<option value=\"\">";
echo __("please choose");
echo "</option>\n\t\t\t\t\t\t\t\t";
foreach ($groups as $k => $v) {
    if(is_numeric($k)) {
        echo "\t\t\t\t\t\t\t\t\t<option value=\"";
        echo $k;
        echo "\" ";
        echo isset($orderform->ProductGroups->main) && $k == $orderform->ProductGroups->main ? "selected=\"selected\"" : "";
        echo ">";
        echo $v["GroupName"];
        echo "</option>\n\t\t\t\t\t\t\t\t";
    }
}
echo "\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("productgroup for options");
echo "</strong>\n\t\t\t\t\t\t<select name=\"ProductGroups[options]\" class=\"text1 size4f\">\n\t\t\t\t\t\t\t<option value=\"\">";
echo __("please choose");
echo "</option>\n\t\t\t\t\t\t\t";
foreach ($groups as $k => $v) {
    if(is_numeric($k)) {
        echo "\t\t\t\t\t\t\t\t<option value=\"";
        echo $k;
        echo "\" ";
        echo isset($orderform->ProductGroups->options) && $k == $orderform->ProductGroups->options ? "selected=\"selected\"" : "";
        echo ">";
        echo $v["GroupName"];
        echo "</option>\n\t\t\t\t\t\t\t";
    }
}
echo "\t\t\t\t\t\t</select>\n\t\t\t\t\t\t<br /><br />\t\t\t\n\t\t\t\t\t\t\n\t\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t</div>\n\t\t\t\t<!--left-->\n\t\t\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t<div class=\"right\">\n\t\t\t\t<!--right-->\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3 billing_cycle_choice\"><h3>";
echo __("billing period");
echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t\t<strong>";
echo __("billing period");
echo "</strong><br />\n\t\t\t\t\t\t<label><input name=\"PeriodChoice\" type=\"radio\" value=\"no\" ";
if($orderform->PeriodChoice == "no") {
    echo "checked=\"checked\"";
}
echo "/> ";
echo __("orderform billing period use product");
echo "</label><br />\n\t\t\t\t\t\t<label><input name=\"PeriodChoice\" type=\"radio\" value=\"default\" ";
if($orderform->PeriodChoice == "default") {
    echo "checked=\"checked\"";
}
echo "/> ";
echo __("orderform billing period use default");
echo "</label><br />\n\t\t\t\t\t\t<label><input name=\"PeriodChoice\" type=\"radio\" value=\"yes\" ";
if($orderform->PeriodChoice == "yes") {
    echo "checked=\"checked\"";
}
echo "/> ";
echo __("orderform billing period use options");
echo "</label><br />\n\t\t\t\t\t\n\t\t\t\t\t\t<div id=\"BillingCycleDiv\" class=\"";
if($orderform->PeriodChoice == "no") {
    echo "hide";
}
echo "\">\n\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("default billing period");
echo "</strong>\n\t\t\t\t\t\t\t<div class=\"addBillingCycleHTML\">\n\t\t\t\t\t\t\t\t<input type=\"text\" name=\"PeriodDefaultPeriods\" class=\"text1 size3\" value=\"";
echo htmlspecialchars($orderform->PeriodDefaultPeriods);
echo "\" />\n\t\t\t\t\t\t\t\t<select name=\"PeriodDefaultPeriodic\" class=\"text1 size10\">\n\t\t\t\t\t\t\t\t\t";
foreach ($array_periodes as $k => $v) {
    if($k) {
        echo "\t\t\t\t\t\t\t\t\t<option value=\"";
        echo $k;
        echo "\" ";
        if($k == $orderform->PeriodDefaultPeriodic) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo $v;
        echo "</option>\n\t\t\t\t\t\t\t\t\t";
    }
}
echo "\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\n\t\t\t\t\t\t\t<div id=\"BillingCycleChoicesDiv\"  ";
if($orderform->PeriodChoice != "yes") {
    echo "class=\"hide\"";
}
echo ">\n\t\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("billing period options");
echo "</strong>\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t";
if(isset($orderform->PeriodChoiceOptions) && is_array($orderform->PeriodChoiceOptions)) {
    foreach ($orderform->PeriodChoiceOptions as $tmp_period) {
        echo "\t\t\t\t\t\t\t\t\t\t<div>\n\t\t\t\t\t\t\t\t\t\t\t<input type=\"text\" name=\"PeriodChoiceOptions_Periods[]\" class=\"text1 size3\" value=\"";
        echo htmlspecialchars($tmp_period->Periods);
        echo "\"/>\n\t\t\t\t\t\t\t\t\t\t\t<select name=\"PeriodChoiceOptions_Periodic[]\" class=\"text1 size10\">\n\t\t\t\t\t\t\t\t\t\t\t\t";
        foreach ($array_periodes as $k => $v) {
            if($k) {
                echo "\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"";
                echo $k;
                echo "\" ";
                if($k == $tmp_period->Periodic) {
                    echo "selected=\"selected\"";
                }
                echo ">";
                echo $v;
                echo "</option>\n\t\t\t\t\t\t\t\t\t\t\t\t";
            }
        }
        echo "\t\t\t\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t\t\t\t<span class=\"removeBillingCycle\">&nbsp;</span>\n\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t";
    }
}
echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<a id=\"addBillingCycle\" class=\"a1 c1 normalfont pointer\">";
echo __("add billing period option");
echo "</a><br />\t\t\t\t\t\t\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t</div>\n\t\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t<!--split2-->\n\t\t\t</div>\n\t\t\t<!--split2-->\n\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\t\t\n\t\t";
if(array_key_exists("domain", $orderform_product_types)) {
    echo "\t\t\t<!--content-->\n\t\t\t<div class=\"content\" id=\"tab-domain\">\n\t\t\t<!--content-->\n\t\n\t\t\t\t<!--split2-->\n\t\t\t\t<div class=\"split2\">\n\t\t\t\t<!--split2-->\n\t\t\t\t\t\t\n\t\t\t\t\t<!--left-->\n\t\t\t\t\t<div class=\"left\">\n\t\t\t\t\t<!--left-->\t\n\t\t\t\t\t\t\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t<div class=\"box3\"><h3>";
    echo __("orderform domain settings");
    echo "</h3><div class=\"content\">\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\t<div class=\"hideForDomain ";
    if($orderform->Type == "domain") {
        echo "hide";
    }
    echo "\">\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<label><input name=\"domain[Available]\" type=\"checkbox\" value=\"yes\" ";
    if(isset($orderform->OtherSettings->domain->Available) && $orderform->OtherSettings->domain->Available == "yes") {
        echo "checked=\"checked\"";
    }
    echo "/> ";
    echo __("orderform domain availability");
    echo "</label><br /><br />\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<div class=\"isDomainEnabled ";
    if(!isset($orderform->OtherSettings->domain->Available) || $orderform->OtherSettings->domain->Available != "yes") {
        echo "hide";
    }
    echo " \">\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<strong class=\"title\">";
    echo __("productgroup for domains");
    echo "</strong>\n\t\t\t\t\t\t\t\t<select name=\"ProductGroups[domain]\" class=\"text1 size4f\">\n\t\t\t\t\t\t\t\t\t<option value=\"\">";
    echo __("please choose");
    echo "</option>\n\t\t\t\t\t\t\t\t\t";
    foreach ($groups as $k => $v) {
        if(is_numeric($k)) {
            echo "\t\t\t\t\t\t\t\t\t\t<option value=\"";
            echo $k;
            echo "\" ";
            if(isset($orderform->ProductGroups->domain) && $k == $orderform->ProductGroups->domain) {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo $v["GroupName"];
            echo "</option>\n\t\t\t\t\t\t\t\t\t";
        }
    }
    echo "\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<label><input name=\"domain[CustomWhois]\" type=\"checkbox\" value=\"yes\" ";
    if(isset($orderform->OtherSettings->domain->CustomWhois) && $orderform->OtherSettings->domain->CustomWhois == "yes") {
        echo "checked=\"checked\"";
    }
    echo "/> ";
    echo __("orderform domain custom whois");
    echo "</label><br />\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<div class=\"hideForHosting ";
    if($orderform->Type == "hosting") {
        echo "hide";
    }
    echo "\">\n\t\t\t\t\t\t\t\t\t<label><input name=\"domain[OwnNameservers]\" type=\"checkbox\" value=\"yes\" ";
    if(isset($orderform->OtherSettings->domain->OwnNameservers) && $orderform->OtherSettings->domain->OwnNameservers == "yes") {
        echo "checked=\"checked\"";
    }
    echo "/> ";
    echo __("orderform domain own nameservers");
    echo "</label><br />\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<strong class=\"title\">";
    echo __("whois check for tlds");
    echo "</strong>\n\t\t\t\t\t\t\t\t<select name=\"domain[ShowTLDs]\" class=\"text1 size4f\">\n\t\t\t\t\t\t\t\t\t<option value=\"popular\" ";
    if(isset($orderform->OtherSettings->domain->ShowTLDs) && $orderform->OtherSettings->domain->ShowTLDs == "popular") {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo __("whois check only popular tlds");
    echo "</option>\n\t\t\t\t\t\t\t\t\t<option value=\"toggle\" ";
    if(isset($orderform->OtherSettings->domain->ShowTLDs) && $orderform->OtherSettings->domain->ShowTLDs == "toggle") {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo __("whois check popular tlds, with toggle");
    echo "</option>\n\t\t\t\t\t\t\t\t\t<option value=\"all\" ";
    if(isset($orderform->OtherSettings->domain->ShowTLDs) && $orderform->OtherSettings->domain->ShowTLDs == "all") {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo __("whois check all tlds");
    echo "</option>\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t</div></div>\n\t\t\t\t\t\t<!--box3-->\n\t\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t<div class=\"box3 isDomainEnabled ";
    if(!isset($orderform->OtherSettings->domain->Available) || $orderform->OtherSettings->domain->Available != "yes") {
        echo "hide";
    }
    echo "\"><h3>";
    echo __("integrated via iframe");
    echo "</h3><div class=\"content\">\n\t\t\t\t\t\t<!--box3-->\t\n\t\t\t\t\t\t\n\t\t\t\t\t\t\t<label><input name=\"iframe_integration\" type=\"checkbox\" value=\"yes\" ";
    if(isset($orderform->OtherSettings->domain->ResultURL) && $orderform->OtherSettings->domain->ResultURL || isset($orderform->OtherSettings->domain->OrderFormURL) && $orderform->OtherSettings->domain->OrderFormURL) {
        echo "checked=\"checked\"";
    }
    echo "/> ";
    echo __("orderform or whois integrated via iframe");
    echo "</label><br /><br />\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<div id=\"iframe_integration_div\" ";
    if(!(isset($orderform->OtherSettings->domain->ResultURL) && $orderform->OtherSettings->domain->ResultURL || isset($orderform->OtherSettings->domain->OrderFormURL) && $orderform->OtherSettings->domain->OrderFormURL)) {
        echo "class=\"hide\"";
    }
    echo ">\n\t\t\t\t\t\t\t\t<div class=\"add alt1\">\n\t\t\t\t\t\t\t\t\t<strong class=\"title\">";
    echo __("whois result url");
    echo "\t\t\t\t\t\t\t\t\t\t<span class=\"infopopupright\">\n\t\t\t\t\t\t\t\t\t\t<em>";
    echo __("more info");
    echo "</em>\n\t\t\t\t\t\t\t\t\t\t<span class=\"popup\">\n\t\t\t\t\t\t\t\t\t\t\t";
    echo __("whois result url info");
    echo "\t\t\t\t\t\t\t\t\t\t\t<b></b>\n\t\t\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t\t\t<input type=\"text\" name=\"domain[ResultURL]\" class=\"text1 size12\" value=\"";
    if(isset($orderform->OtherSettings->domain->ResultURL)) {
        echo htmlspecialchars($orderform->OtherSettings->domain->ResultURL);
    }
    echo "\" />\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t<span id=\"whoisform-result-url\" class=\"loading_float\" style=\"margin-top:15px;\"></span>\n\t\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<div class=\"add alt1\">\n\t\t\t\t\t\t\t\t\t<strong class=\"title\">";
    echo __("custom orderform url");
    echo "\t\t\t\t\t\t\t\t\t\t<span class=\"infopopupright\">\n\t\t\t\t\t\t\t\t\t\t<em>";
    echo __("more info");
    echo "</em>\n\t\t\t\t\t\t\t\t\t\t<span class=\"popup\">\n\t\t\t\t\t\t\t\t\t\t\t";
    echo __("custom orderform url info");
    echo "\t\t\t\t\t\t\t\t\t\t\t<b></b>\n\t\t\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t\t\t<input type=\"text\" name=\"domain[OrderFormURL]\" class=\"text1 size12\" value=\"";
    if(isset($orderform->OtherSettings->domain->OrderFormURL)) {
        echo htmlspecialchars($orderform->OtherSettings->domain->OrderFormURL);
    }
    echo "\" />\t\t\t\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t<span id=\"orderform-url-img\" class=\"loading_float\" style=\"margin-top:15px;\"></span>\t\t\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t</div></div>\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t<!--left-->\n\t\t\t\t\t</div>\n\t\t\t\t\t<!--left-->\n\t\t\t\t\t\n\t\t\t\t\t<!--right-->\n\t\t\t\t\t<div id=\"right\" class=\"right\">\n\t\t\t\t\t<!--right-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t";
    if(0 < $domain_group_id) {
        echo "\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t<div class=\"box3\"><h3>";
        echo __("popular tlds");
        echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t\t\t<!--box3-->\n\t\t\n\t\t\t\t\t\t\t<div id=\"popular_tld_list\" class=\"tld_list\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t<ul>\n\t\t\t\t\t\t\t\t\t\t";
        $popular_counter = 0;
        $popular_list = isset($orderform->OtherSettings->domain->PopularList) ? explode("|", $orderform->OtherSettings->domain->PopularList) : [];
        foreach ($popular_list as $_tld) {
            if(isset($tld_products[$_tld])) {
                $v = $tld_products[$_tld];
                echo "\t\t\t\t\t\t\t\t\t\t\t<li id=\"popular_tld_";
                echo str_replace(".", "_", $_tld);
                echo "\"><strong>";
                echo $v["ProductTld"];
                echo "</strong>\n\t\t\t\t\t\t\t\t\t\t\t\t<span style=\"width:100px;padding-left:10px;\">";
                echo __("per") . " " . $array_periodic[$v["PricePeriod"]];
                echo "</span>\n\t\t\t\t\t\t\t\t\t\t\t\t<span>";
                echo money($v["PriceExcl"]);
                echo "</span>\n\t\t\t\t\t\t\t\t\t\t\t\t<input type=\"hidden\" name=\"domain[Popular][]\" value=\"";
                echo $v["ProductTld"];
                echo "\" />\n\t\t\t\t\t\t\t\t\t\t\t\t<a class=\"a1\">";
                echo __("remove from popular tld list");
                echo "</a>\n\t\t\t\t\t\t\t\t\t\t\t</li>\n\t\t\t\t\t\t\t\t\t\t\t";
                $popular_counter++;
            }
        }
        echo "\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t</ul>\n\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t<span class=\"none\" ";
        if(0 < $popular_counter) {
            echo "style=\"display:none;\"";
        }
        echo " >";
        echo __("whois no popular tlds chosen");
        echo "</span>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t</div></div>\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t<div class=\"box3\"><h3>";
        echo __("whois other tlds");
        echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t\t\t<!--box3-->\n\t\t\n\t\t\n\t\t\t\t\t\t\t<div id=\"other_tld_list\" class=\"tld_list\" style=\"max-height:310px;overflow:auto;\">\n\t\t\t\t\t\t\t\t\t<ul>\n\t\t\t\t\t\t\t\t\t";
        foreach ($tld_products as $_tld => $v) {
            echo "\t\t\t\t\t\t\t\t\t\t<li id=\"other_tld_";
            echo str_replace(".", "_", $_tld);
            echo "\" ";
            if(in_array($_tld, $popular_list)) {
                echo "class=\"popular\"";
            }
            echo "><strong>";
            echo $v["ProductTld"];
            echo "</strong>\n\t\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t\t\t<span style=\"width:100px;padding-left:10px;\">";
            echo __("per") . " " . $array_periodic[$v["PricePeriod"]];
            echo "</span>\n\t\t\t\t\t\t\t\t\t\t\t<span>";
            echo money($v["PriceExcl"]);
            echo "</span>\n\t\t\t\t\t\t\t\t\t\t</li>\n\t\t\t\t\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\t\t\t\t</ul>\n\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t<span class=\"none\" ";
        if(0 < count($tld_products)) {
            echo "style=\"display:none;\"";
        }
        echo ">";
        echo __("whois productgroup contains no tlds");
        echo "</span>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t</div></div>\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\t\n\t\t\t\t\t<!--right-->\n\t\t\t\t\t</div>\n\t\t\t\t\t<!--right-->\n\t\t\t\t\t\n\t\t\t\t<!--split2-->\n\t\t\t\t</div>\n\t\t\t\t<!--split2-->\n\t\t\t\t\t\t\n\t\t\t<!--content-->\n\t\t\t</div>\n\t\t\t<!--content-->\n\t\t\t";
}
if(array_key_exists("hosting", $orderform_product_types)) {
    echo "\t\t\t<!--content-->\n\t\t\t<div class=\"content\" id=\"tab-hosting\">\n\t\t\t<!--content-->\n\t\n\t\t\t\t<!--split2-->\n\t\t\t\t<div class=\"split2\">\n\t\t\t\t<!--split2-->\n\t\t\t\t\t\t\n\t\t\t\t\t<!--left-->\n\t\t\t\t\t<div class=\"left\">\n\t\t\t\t\t<!--left-->\t\n\t\n\t\t\t\t\t\t\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t<div class=\"box3\"><h3>";
    echo __("orderform hosting settings");
    echo "</h3><div class=\"content\">\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t\t<div class=\"hideForHosting ";
    if($orderform->Type == "hosting") {
        echo "hide";
    }
    echo "\">\n\t\t\t\t\t\t\t\t<label><input name=\"hosting[Available]\" type=\"checkbox\" value=\"yes\" ";
    if(isset($orderform->OtherSettings->hosting->Available) && $orderform->OtherSettings->hosting->Available == "yes") {
        echo "checked=\"checked\"";
    }
    echo "/> ";
    echo __("orderform hosting availability");
    echo "</label><br /><br />\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<div class=\"isHostingEnabled ";
    if(!isset($orderform->OtherSettings->hosting->Available) || $orderform->OtherSettings->hosting->Available != "yes") {
        echo "hide";
    }
    echo " \">\n\t\t\t\t\t\t\t\t<strong class=\"title\">";
    echo __("productgroup for hosting");
    echo "</strong>\n\t\t\t\t\t\t\t\t<select name=\"ProductGroups[hosting]\" class=\"text1 size4f\">\n\t\t\t\t\t\t\t\t\t<option value=\"\">";
    echo __("please choose");
    echo "</option>\n\t\t\t\t\t\t\t\t\t";
    foreach ($groups as $k => $v) {
        if(is_numeric($k)) {
            echo "\t\t\t\t\t\t\t\t\t\t<option value=\"";
            echo $k;
            echo "\" ";
            if(isset($orderform->ProductGroups->hosting) && $k == $orderform->ProductGroups->hosting) {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo $v["GroupName"];
            echo "</option>\n\t\t\t\t\t\t\t\t\t";
        }
    }
    echo "\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<strong class=\"title\">";
    echo __("orderform hosting theme");
    echo "</strong>\n\t\t\t\t\t\t\t\t<select name=\"hosting[Theme]\" class=\"text1 size4f\">\n\t\t\t\t\t\t\t\t\t<option value=\"simple\" ";
    if(isset($orderform->OtherSettings->hosting->Theme) && $orderform->OtherSettings->hosting->Theme == "simple") {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo __("orderform hosting theme simple");
    echo "</option>\n\t\t\t\t\t\t\t\t\t<option value=\"packages\" ";
    if(isset($orderform->OtherSettings->hosting->Theme) && $orderform->OtherSettings->hosting->Theme == "packages") {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo __("orderform hosting theme packages");
    echo "</option>\n\t\t\t\t\t\t\t\t\t<option value=\"compare\" ";
    if(isset($orderform->OtherSettings->hosting->Theme) && $orderform->OtherSettings->hosting->Theme == "compare") {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo __("orderform hosting theme compare");
    echo "</option>\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<strong class=\"title\">";
    echo __("orderform hosting default selected");
    echo "</strong>\n\t\t\t\t\t\t\t\t<select name=\"hosting[DefaultPackage]\" class=\"text1 size4f\">\n\t\t\t\t\t\t\t\t\t<option value=\"\">";
    echo __("please choose");
    echo "</option>\n\t\t\t\t\t\t\t\t\t";
    foreach ($compare_matrix as $prod_id => $prod_info) {
        echo "\t\t\t\t\t\t\t\t\t<option value=\"";
        echo $prod_info["ProductCode"];
        echo "\" ";
        if(isset($orderform->OtherSettings->hosting->DefaultPackage) && $prod_info["ProductCode"] == $orderform->OtherSettings->hosting->DefaultPackage) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo $prod_info["ProductName"];
        echo "</option>\n\t\t\t\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<label><input name=\"hosting[DefaultDomain]\" type=\"checkbox\" value=\"yes\" ";
    if(isset($orderform->OtherSettings->hosting->DefaultDomain) && $orderform->OtherSettings->hosting->DefaultDomain == "yes") {
        echo "checked=\"checked\"";
    }
    echo "/> ";
    echo __("orderform hosting ask default domain");
    echo "</label><br />\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t</div></div>\n\t\t\t\t\t\t<!--box3-->\n\t\n\t\t\t\t\t<!--left-->\n\t\t\t\t\t</div>\n\t\t\t\t\t<!--left-->\n\t\t\t\t\t\n\t\t\t\t\t<!--right-->\n\t\t\t\t\t<div class=\"right\">\n\t\t\t\t\t<!--right-->\n\t\t\t\t\t\n\t\t\t\t\n\t\t\t\t\t<!--right-->\n\t\t\t\t\t</div>\n\t\t\t\t\t<!--right-->\n\t\t\t\t\t\n\t\t\t\t<!--split2-->\n\t\t\t\t</div>\n\t\t\t\t<!--split2-->\n\t\n\t\t\t\t<div class=\"isHostingEnabled ";
    if(!isset($orderform->OtherSettings->hosting->Available) || $orderform->OtherSettings->hosting->Available != "yes") {
        echo "hide";
    }
    echo " \">\n\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div id=\"ThemeCompareDiv\" class=\"box3 ";
    if(!isset($orderform->OtherSettings->hosting->Theme) || $orderform->OtherSettings->hosting->Theme != "compare") {
        echo "hide";
    }
    echo "\"><h3>";
    echo __("orderform hosting theme compare");
    echo "</h3><div class=\"content hosting_compare_table\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t\t<div class=\"hosting_compare_table_label_div\">\n\t\t\t\t\t\t&nbsp;<br /><strong>";
    echo __("orderform hosting theme compare labels");
    echo "</strong><br />\n\t\t\t\t\t\t";
    if(isset($orderform->OtherSettings->hosting->CompareLabels)) {
        foreach ($orderform->OtherSettings->hosting->CompareLabels as $label) {
            echo "\t\t\t\t\t\t\t\t<div><span>&nbsp;</span><input type=\"text\" name=\"hosting[CompareLabels][]\" class=\"text1\" value=\"";
            echo htmlspecialchars($label);
            echo "\"/></div>\n\t\t\t\t\t\t\t\t";
        }
    } else {
        echo "\t\t\t\t\t\t\t<div><span>&nbsp;</span><input type=\"text\" name=\"hosting[CompareLabels][]\" class=\"text1\" value=\"\"/></div>\n\t\t\t\t\t\t\t";
    }
    echo "\t\n\t\t\t\t\t\t<a id=\"hosting_compare_table_add_record\" class=\"a1 c1 pointer\">";
    echo __("orderform hosting theme compare add label");
    echo "</a>\t\n\t\t\t\t\t</div>\n\t\t\t\t\t\n\t\t\t\t\t<div id=\"hosting_compare_overflow_container\">\n\t\t\t\t\t\t<div class=\"overflow_inner_wrapper\">\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t";
    if(isset($orderform->ProductGroups->hosting) && 0 < $orderform->ProductGroups->hosting && !empty($compare_matrix)) {
        if(isset($orderform->OtherSettings->hosting->CompareValues)) {
            foreach ($orderform->OtherSettings->hosting->CompareValues as $prod_id => $cols) {
                if(isset($compare_matrix[$prod_id])) {
                    echo "<div id=\"hosting_compare_";
                    echo $prod_id;
                    echo "\" class=\"hosting_compare_table_product_div\">\n\t\t\t\t\t\t\t\t\t\t\t\t<span class=\"arrowleft\">&nbsp;</span>\n\t\t\t\t\t\t\t\t\t\t\t\t<span class=\"arrowright\">&nbsp;</span>\n\t\t\t\t\t\t\t\t\t\t\t\t<strong>";
                    echo $compare_matrix[$prod_id]["ProductName"];
                    echo "</strong><br />";
                    echo money($compare_matrix[$prod_id]["PriceExcl"]) . " " . __("per") . " " . $array_periodes[$compare_matrix[$prod_id]["PricePeriod"]];
                    echo "<br />\n\t\t\t\t\t\t\t\t\t\t\t\t";
                    foreach ($cols as $col) {
                        echo "\t\t\t\t\t\t\t\t\t\t\t\t\t<input type=\"text\" name=\"hosting[CompareValues][";
                        echo $prod_id;
                        echo "][]\" class=\"text1\" value=\"";
                        echo htmlspecialchars($col);
                        echo "\" />\n\t\t\t\t\t\t\t\t\t\t\t\t";
                    }
                    echo "\t\t\t\t\t\t\t\t\t\t\t</div>";
                    unset($compare_matrix[$prod_id]);
                }
            }
        }
        if(!empty($compare_matrix)) {
            foreach ($compare_matrix as $prod_id => $prod_info) {
                echo "<div id=\"hosting_compare_";
                echo $prod_id;
                echo "\" class=\"hosting_compare_table_product_div\">\n\t\t\t\t\t\t\t\t\t\t\t<span class=\"arrowleft\">&nbsp;</span>\n\t\t\t\t\t\t\t\t\t\t\t<span class=\"arrowright\">&nbsp;</span>\n\t\t\t\t\t\t\t\t\t\t\t<strong>";
                echo $prod_info["ProductName"];
                echo "</strong><br />";
                echo money($prod_info["PriceExcl"]) . " " . __("per") . " " . $array_periodes[$prod_info["PricePeriod"]];
                echo "<br />\n\t\t\t\t\t\t\t\t\t\t\t";
                if(isset($orderform->OtherSettings->hosting->CompareLabels)) {
                    foreach ($orderform->OtherSettings->hosting->CompareLabels as $label) {
                        echo "\t\t\t\t\t\t\t\t\t\t\t\t<input type=\"text\" name=\"hosting[CompareValues][";
                        echo $prod_id;
                        echo "][]\" class=\"text1\" value=\"\" />\n\t\t\t\t\t\t\t\t\t\t\t";
                    }
                }
                echo "\t\t\t\t\t\t\t\t\t\t</div>";
            }
        }
    }
    echo "\t\t\t\t\t\t\t\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\t\t\t\n\t\t\t\t\t<br clear=\"both\" />\n\t\t\t\t\n\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div id=\"ThemePackagesDiv\" class=\"box3 ";
    if(!isset($orderform->OtherSettings->hosting->Theme) || $orderform->OtherSettings->hosting->Theme != "packages") {
        echo "hide";
    }
    echo "\"><h3>";
    echo __("orderform hosting theme packages");
    echo "</h3><div class=\"content hosting_compare_table\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t\t<strong class=\"title\">";
    echo __("orderform hosting theme packages grid");
    echo "</strong>\n\t\t\t\t\t<select name=\"hosting[PackagesGrid]\" class=\"text1 size4f\">\n\t\t\t\t\t\t<option value=\"4\" ";
    if(isset($orderform->OtherSettings->hosting->PackagesGrid) && $orderform->OtherSettings->hosting->PackagesGrid == 4) {
        echo "selected=\"selected\"";
    }
    echo ">4</option>\n\t\t\t\t\t\t<option value=\"3\" ";
    if(isset($orderform->OtherSettings->hosting->PackagesGrid) && $orderform->OtherSettings->hosting->PackagesGrid == 3) {
        echo "selected=\"selected\"";
    }
    echo ">3</option>\n\t\t\t\t\t\t<option value=\"2\" ";
    if(isset($orderform->OtherSettings->hosting->PackagesGrid) && $orderform->OtherSettings->hosting->PackagesGrid == 2) {
        echo "selected=\"selected\"";
    }
    echo ">2</option>\n\t\t\t\t\t\t<option value=\"1\" ";
    if(isset($orderform->OtherSettings->hosting->PackagesGrid) && $orderform->OtherSettings->hosting->PackagesGrid == 1) {
        echo "selected=\"selected\"";
    }
    echo ">1</option>\t\t\t\t\t\t\n\t\t\t\t\t</select>\n\t\t\t\t\t<br /><br />\n\t\t\t\t\t\n\t\t\t\t\t\n\t\t\t\t\t<table border=\"0\" cellpadding=\"1\" cellspacing=\"0\" width=\"100%\" class=\"table1 hosting_packages_table\">\n\t\t\t\t\t<thead>\n\t\t\t\t\t<tr>\n\t\t\t\t\t\t<th width=\"30\">&nbsp;</th>\n\t\t\t\t\t\t<th align=\"left\">";
    echo __("productname");
    echo "</th>\n\t\t\t\t\t\t<th align=\"left\" colspan=\"3\">";
    echo __("price excl");
    echo "</th>\n\t\t\t\t\t\t<th align=\"left\">";
    echo __("orderform hosting theme packages description");
    echo "</th>\n\t\t\t\t\t</tr>\n\t\t\t\t\t</thead>\n\t\t\t\t\t<tbody>\n\t\t\t\t\t";
    if(isset($orderform->ProductGroups->hosting) && 0 < $orderform->ProductGroups->hosting && !empty($product_list)) {
        if(isset($orderform->OtherSettings->hosting->PackagesDescription)) {
            foreach ($orderform->OtherSettings->hosting->PackagesDescription as $prod_id => $description) {
                if(isset($packages_descriptions[$prod_id])) {
                    echo "<tr>\n\t\t\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t\t\t<span class=\"up_hosting_packages_table\">&nbsp;</span>\n\t\t\t\t\t\t\t\t\t\t\t<span class=\"down_hosting_packages_table\">&nbsp;</span>\n\t\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t\t<td>";
                    echo $packages_descriptions[$prod_id]["ProductName"];
                    echo "</td>\n\t\t\t\t\t\t\t\t\t\t<td style=\"width: 5px;\" class=\"currency_sign_left\">";
                    echo currency_sign_td(CURRENCY_SIGN_LEFT);
                    echo "</td>\n\t\t\t\t\t\t\t\t\t\t<td style=\"width: 50px; padding-left: 0px; padding-right: 0px;\" align=\"right\" class=\"currency_sign_right\">";
                    echo money($packages_descriptions[$prod_id]["PriceExcl"], false);
                    if(CURRENCY_SIGN_RIGHT) {
                        echo "&nbsp;" . CURRENCY_SIGN_RIGHT;
                    }
                    echo "</td>\n\t\t\t\t\t\t\t\t\t\t<td style=\"width: 75px;\">";
                    if($packages_descriptions[$prod_id]["PricePeriod"]) {
                        echo __("per") . " " . $array_periodic[$packages_descriptions[$prod_id]["PricePeriod"]];
                    } else {
                        echo "&nbsp;";
                    }
                    echo "</td>\n\t\t\t\t\t\t\t\t\t\t<td><input type=\"text\" name=\"hosting[PackagesDescription][";
                    echo $prod_id;
                    echo "]\" class=\"text1 size11\" value=\"";
                    echo htmlspecialchars($description);
                    echo "\" /></td>\n\t\t\t\t\t\t\t\t\t</tr>";
                    unset($packages_descriptions[$prod_id]);
                }
            }
        }
        if(!empty($packages_descriptions)) {
            foreach ($packages_descriptions as $prod_id => $prod_info) {
                echo "<tr>\n\t\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t\t<span class=\"up_hosting_packages_table\">&nbsp;</span>\n\t\t\t\t\t\t\t\t\t\t<span class=\"down_hosting_packages_table\">&nbsp;</span>\n\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t<td>";
                echo $prod_info["ProductName"];
                echo "</td>\n\t\t\t\t\t\t\t\t\t<td style=\"width: 5px;\" class=\"currency_sign_left\">";
                echo currency_sign_td(CURRENCY_SIGN_LEFT);
                echo "</td>\n\t\t\t\t\t\t\t\t\t<td style=\"width: 50px; padding-left: 0px; padding-right: 0px;\" align=\"right\" class=\"currency_sign_right\">";
                echo money($prod_info["PriceExcl"], false);
                if(CURRENCY_SIGN_RIGHT) {
                    echo "&nbsp;" . CURRENCY_SIGN_RIGHT;
                }
                echo "</td>\n\t\t\t\t\t\t\t\t\t<td style=\"width: 75px;\">";
                if($prod_info["PricePeriod"]) {
                    echo __("per") . " " . $array_periodic[$prod_info["PricePeriod"]];
                } else {
                    echo "&nbsp;";
                }
                echo "</td>\n\t\t\t\t\t\t\t\t\t<td><input type=\"text\" name=\"hosting[PackagesDescription][";
                echo $prod_id;
                echo "]\" class=\"text1 size11\" value=\"\" /></td>\n\t\t\t\t\t\t\t\t</tr>";
            }
        }
    }
    echo "\t\t\t\t\t</tbody>\n\t\t\t\t\t</table>\n\t\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t</div>\n\t\t\t\t\t\n\t\t\t<!--content-->\n\t\t\t</div>\n\t\t\t<!--content-->\n\t\t\t";
}
foreach ($orderform_product_types as $_tmp_product_type => $_tmp_product_type_info) {
    if(!in_array($_tmp_product_type, ["domain", "hosting"]) && isset($_tmp_product_type_info["tab"])) {
        echo "\t\t\t\t<!--content-->\n\t\t\t\t<div class=\"content\" id=\"tab-";
        echo $_tmp_product_type;
        echo "\">\n\t\t\t\t<!--content-->\n\t\t\t\t\t";
        do_action("show_custom_tab_content", "orderform_" . $_tmp_product_type);
        echo "\t\t\t\n\t\t\t\t<!--content-->\n\t\t\t\t</div>\n\t\t\t\t<!--content-->\n\t\t\t\t";
    }
}
echo "\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-custom\">\n\t\t<!--content-->\n\n\t\t\t<!--split2-->\n\t\t\t<div class=\"split2\">\n\t\t\t<!--split2-->\n\t\t\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t<div class=\"left\">\n\t\t\t\t<!--left-->\t\n\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("orderform custom settings");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("orderform custom controllername");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" name=\"custom[ControllerName]\" class=\"text1 size1\" value=\"";
if(isset($orderform->OtherSettings->custom->ControllerName)) {
    echo htmlspecialchars($orderform->OtherSettings->custom->ControllerName);
}
echo "\" />\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t</div>\n\t\t\t\t<!--left-->\n\t\t\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t<div class=\"right\">\n\t\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t\t\t\n\n\t\t\t\t<!--right-->\n\t\t\t\t</div>\n\t\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t<!--split2-->\n\t\t\t</div>\n\t\t\t<!--split2-->\n\t\t\t\t\t\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\t\n\t<!--box1-->\n\t</div>\n\t<!--box1-->\n\n\t<br />\n\t\t\t\t\t\n\t<p class=\"align_right\"><a class=\"button1 alt1\" id=\"form_create_btn\"><span>";
echo $btn_form_title;
echo "</span></a></p>\n\t\n<!--form-->\n</fieldset></form>\n<!--form-->\n\n";
require_once "views/footer.php";

?>