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
$page_form_title = $pagetype == "edit" ? __("edit product") : __("add product");
echo "\n";
echo $message;
echo "\n<!--form-->\n<form id=\"ProductForm\" name=\"form_create\" method=\"post\" action=\"products.php?page=";
echo $pagetype;
echo "\"><fieldset><legend>";
echo $page_form_title;
echo "</legend>\n\t\n";
if(0 < $product->Identifier) {
    echo "\t<input type=\"hidden\" name=\"id\" value=\"";
    echo $product->Identifier;
    echo "\" />\n";
}
echo "<!--form-->\n\n<!--heading1-->\n<div class=\"heading1\">\n<!--heading1-->\n\n\t<h2>";
echo $page_form_title;
echo "</h2>\n\t<p class=\"pos2\"><strong class=\"textsize1 pointer\" id=\"ProductCode_text\" style=\"line-height: 22px\">";
echo $product->ProductCode;
echo " <span class=\"ico actionblock arrowdown mar2 pointer\" style=\"margin: 4px 2px 2px 2px; z-index: 1;\">&nbsp;</span></strong><input type=\"text\" class=\"text2 size7 hide\" name=\"ProductCode\" value=\"";
echo $product->ProductCode;
echo "\"/></p>\n\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n\t<!--box1-->\n\t<div class=\"box2\" id=\"tabs\">\n\t<!--box1-->\n\t\n\t\t<!--top-->\n\t\t<div class=\"top\">\n\t\t<!--top-->\n\t\t\n\t\t\t<ul class=\"list3\">\n\t\t\t\t<li class=\"on\"><a href=\"#tab-general\">";
echo __("general");
echo "</a></li>\n\t\t\t\t<li><a href=\"#tab-extended-description\">";
echo __("extended description");
echo "</a></li>\n\t\t\t\t<li><a href=\"#tab-groups\">";
echo __("productgroups");
echo "</a></li>\n\t\t\t</ul>\n\t\t\n\t\t<!--top-->\n\t\t</div>\n\t\t<!--top-->\n\n\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-general\">\n\t\t<!--content-->\n\n\t\t\t<!--split2-->\n\t\t\t<div class=\"split2\">\n\t\t\t<!--split2-->\n\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t<div class=\"left\">\n\t\t\t\t<!--left-->\n\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("producttype");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t<input type=\"radio\" id=\"producttype_other\" name=\"ProductType\" value=\"other\" ";
if($product->ProductType == "other") {
    echo "checked=\"checked\"";
}
echo "/> <label for=\"producttype_other\">";
echo $array_producttypes["other"];
echo "</label><br />\n\t\t\t\t\t\t<input type=\"radio\" id=\"producttype_domain\" name=\"ProductType\" value=\"domain\" ";
if($product->ProductType == "domain") {
    echo "checked=\"checked\"";
}
echo "/>  <label for=\"producttype_domain\">";
echo $array_producttypes["domain"];
echo "</label><br />\n\t\t\t\t\t\t<input type=\"radio\" id=\"producttype_hosting\" name=\"ProductType\" value=\"hosting\" ";
if($product->ProductType == "hosting") {
    echo "checked=\"checked\"";
}
echo "/>  <label for=\"producttype_hosting\">";
echo $array_producttypes["hosting"];
echo "</label><br />\n\t\t\t\t\t\t";
if(!empty($additional_product_types)) {
    foreach ($additional_product_types as $product_type_key => $product_type_value) {
        echo "<input type=\"radio\" id=\"producttype_";
        echo $product_type_key;
        echo "\" name=\"ProductType\" value=\"";
        echo $product_type_key;
        echo "\" ";
        if($product->ProductType == $product_type_key) {
            echo "checked=\"checked\"";
        }
        echo "/>  <label for=\"producttype_";
        echo $product_type_key;
        echo "\">";
        echo $product_type_value;
        echo "</label><br />";
    }
}
echo "\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t</div>\n\t\t\t\t<!--left-->\n\t\t\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t<div class=\"right\">\n\t\t\t\t<!--right-->\n\t\t\t\t\t\n\t\t\t\t\t<div class=\"setting_help_box\">\n\t\t\t\t\t\t\t<strong>";
echo __("producttype");
echo "</strong><br />\n\t\t\t\t\t\t\t";
echo __("producttype explained");
echo " \t\t\t\t\t\t\t\n\t\t\t\t\t</div>\n\t\t\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t</div>\n\t\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t\t<br clear=\"both\" /><br />\n\t\t\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t<div class=\"left\">\n\t\t\t\t<!--left-->\t\n\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("general");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("productname");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"ProductName\" value=\"";
echo $product->ProductName;
echo "\" maxlength=\"100\" ";
$ti = tabindex($ti);
echo " />\t\t\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("description for invoice");
echo "</strong>\n\t\t\t\t\t\t<textarea name=\"ProductKeyPhrase\" class=\"text1 autogrow\" style=\"width: 97%;\" ";
$ti = tabindex($ti);
echo ">";
echo $product->ProductKeyPhrase;
echo "</textarea>\n\n\t\t\t\t\t\t\t<br /><br />\n\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("product unit");
echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" name=\"NumberSuffix\" value=\"";
echo $product->NumberSuffix;
echo "\" class=\"text1 size6\" maxlength=\"19\" />\n\t\t\t\t\t\t\t<span style=\"font-style: italic;\">";
echo __("product unit example");
echo "</span>\n\n\t\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t<br />\n\t\t\t\t\t\n\t\t\t\t\t\n\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("financial");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\n\t\t\t\t\t\t";
if(VAT_CALC_METHOD == "incl") {
    echo "\t\t\t\t\t\t\t<strong class=\"title\">";
    echo __("price incl");
    echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"PriceIncl\" value=\"";
    echo money($product->PriceIncl, false, false);
    echo "\" ";
    $ti = tabindex($ti);
    echo " />\n\t\t\t\t\t\t\t<span class=\"span_product_excl_incl";
    if(empty($product->Identifier)) {
        echo " hide";
    }
    echo "\">";
    echo CURRENCY_SIGN_LEFT;
    echo " <span>";
    echo money($product->PriceExcl, false);
    echo "</span> ";
    if(CURRENCY_SIGN_RIGHT) {
        echo CURRENCY_SIGN_RIGHT . " ";
    }
    echo __("excl vat");
    echo "</span>\n\t\t\t\t\t\t";
} else {
    echo "\t\t\t\t\t\t\t<strong class=\"title\">";
    echo __("price excl");
    echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"PriceExcl\" value=\"";
    echo money($product->PriceExcl, false, false);
    echo "\" ";
    $ti = tabindex($ti);
    echo " />\n\t\t\t\t\t\t\t<span class=\"span_product_excl_incl";
    if(empty($product->Identifier)) {
        echo " hide";
    }
    echo "\">";
    echo CURRENCY_SIGN_LEFT;
    echo " <span>";
    echo money($product->PriceIncl, false);
    echo "</span> ";
    if(CURRENCY_SIGN_RIGHT) {
        echo CURRENCY_SIGN_RIGHT . " ";
    }
    echo __("incl vat");
    echo "</span>\n\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n                        ";
if(!empty($array_taxpercentages)) {
    echo "    \t\t\t\t\t\t<strong class=\"title\">";
    echo __("vat percentage");
    echo "</strong>\n    \t\t\t\t\t\t<select name=\"TaxPercentage\" class=\"text1 size4\">\n    \t\t\t\t\t\t\t";
    foreach ($array_taxpercentages as $key => $value) {
        echo "    \t\t\t\t\t\t\t<option value=\"";
        echo $key;
        echo "\" ";
        if($product->TaxPercentage == $key) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo vat($value);
        echo "%</option>\n    \t\t\t\t\t\t\t";
    }
    echo "    \t\t\t\t\t\t</select>\n    \t\t\t\t\t\t\n    \t\t\t\t\t\t<br /><br />\n                        ";
}
echo "\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("price per");
echo "</strong>\n\t\t\t\t\t\t<select name=\"PricePeriod\" class=\"text1 size4\">\n\t\t\t\t\t\t\t";
foreach ($array_periodic as $key => $value) {
    echo "\t\t\t\t\t\t\t<option value=\"";
    echo $key;
    echo "\" ";
    if($product->PricePeriod == $key) {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo $value;
    echo "</option>\n\t\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("costprice");
if(!empty($array_taxpercentages)) {
    echo " " . __("excl vat");
}
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"Cost\" value=\"";
echo money($product->Cost, false);
echo "\" ";
$ti = tabindex($ti);
echo " />\n\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t<br />\n\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div id=\"CustomPricesDiv\" class=\"box3";
if($product->PricePeriod == "") {
    echo " hide";
}
echo "\"><h3>";
echo __("product custom prices and periods");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t<label><input type=\"radio\" name=\"HasCustomPrice\" value=\"no\" ";
if($product->HasCustomPrice == "no") {
    echo "checked=\"checked\"";
}
echo "/> ";
echo __("product custom prices none");
echo "</label><br />\n\t\t\t\t\t\t<label><input type=\"radio\" name=\"HasCustomPrice\" value=\"period\" ";
if($product->HasCustomPrice == "period") {
    echo "checked=\"checked\"";
}
echo "/> ";
echo __("product custom prices period type");
echo "</label><br />\n\t\t\t\t\t\t<br />\n\t\t\t\t\t\n\t\t\t\t\t\t<div id=\"CustomPricesPeriodDiv\"";
if($product->HasCustomPrice == "no") {
    echo " class=\"hide\"";
}
echo ">\n\t\t\t\t\t\t\t<strong class=\"title\" style=\"float: left;width:230px;;\">";
echo __("product custom prices periods");
echo "</strong>\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo VAT_CALC_METHOD == "incl" ? __("price incl") : __("price excl");
echo "</strong>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<div id=\"CustomPricesPeriodDiv_example\" class=\"hide\">\n\t\t\t\t\t\t\t\t<input type=\"text\" name=\"CustomPrices[period][Periods][helper]\" class=\"text1 size3\" value=\"\"/>\n\t\t\t\t\t\t\t\t<select name=\"CustomPrices[period][Periodic][helper]\" class=\"text1 size10\">\n\t\t\t\t\t\t\t\t\t";
foreach ($array_periodes as $k => $v) {
    if($k) {
        echo "\t\t\t\t\t\t\t\t\t<option value=\"";
        echo $k;
        echo "\" ";
        if($k == $product->PricePeriod) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo $v;
        echo "</option>\n\t\t\t\t\t\t\t\t\t";
    }
}
echo "\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t&nbsp; \n\t\t\t\t\t\t\t\t";
if(VAT_CALC_METHOD == "incl") {
    echo "\t\t\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size6\" name=\"CustomPrices[period][PriceIncl][helper]\" value=\"\" />\n\t\t\t\t\t\t\t\t\t<span style=\"color: #6C6C6D; font-size: 11px; margin-left: 10px;\">";
    echo __("per");
    echo " <span>";
    echo $array_periodes[$product->PricePeriod];
    echo "</span></span>\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t";
} else {
    echo "\t\t\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size6\" name=\"CustomPrices[period][PriceExcl][helper]\" value=\"\" />\n\t\t\t\t\t\t\t\t\t<span style=\"color: #6C6C6D; font-size: 11px; margin-left: 10px;\">";
    echo __("per");
    echo " <span>";
    echo $array_periodes[$product->PricePeriod];
    echo "</span></span>\n\t\t\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<span class=\"removeBillingCycle\">&nbsp;</span>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t";
if(isset($custom_prices["period"]) && is_array($custom_prices["period"])) {
    foreach ($custom_prices["period"] as $tmp_key => $tmp_period_price) {
        if($tmp_key == "default") {
        } else {
            $tmp_key = explode("-", $tmp_key);
            echo "\t\t\t\t\t\t\t\t\t<div>\n\t\t\t\t\t\t\t\t\t\t<input type=\"text\" name=\"CustomPrices[period][Periods][]\" class=\"text1 size3\" value=\"";
            echo htmlspecialchars($tmp_key[0]);
            echo "\"/>\n\t\t\t\t\t\t\t\t\t\t<select name=\"CustomPrices[period][Periodic][]\" class=\"text1 size10\">\n\t\t\t\t\t\t\t\t\t\t\t";
            foreach ($array_periodes as $k => $v) {
                if($k) {
                    echo "\t\t\t\t\t\t\t\t\t\t\t<option value=\"";
                    echo $k;
                    echo "\" ";
                    if($k == $tmp_key[1]) {
                        echo "selected=\"selected\"";
                    }
                    echo ">";
                    echo $v;
                    echo "</option>\n\t\t\t\t\t\t\t\t\t\t\t";
                }
            }
            echo "\t\t\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t\t&nbsp; \n\t\t\t\t\t\t\t\t\t\t";
            if(VAT_CALC_METHOD == "incl") {
                echo "\t\t\t\t\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size6\" name=\"CustomPrices[period][PriceIncl][]\" value=\"";
                echo money($tmp_period_price["PriceIncl"], false, false);
                echo "\" />\n\t\t\t\t\t\t\t\t\t\t\t<span style=\"color: #6C6C6D; font-size: 11px; margin-left: 10px;\">";
                echo __("per");
                echo " <span>";
                echo $array_periodes[$tmp_key[1]];
                echo "</span></span>\n\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t\t";
            } else {
                echo "\t\t\t\t\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size6\" name=\"CustomPrices[period][PriceExcl][]\" value=\"";
                echo money($tmp_period_price["PriceExcl"], false, false);
                echo "\" />\n\t\t\t\t\t\t\t\t\t\t\t<span style=\"color: #6C6C6D; font-size: 11px; margin-left: 10px;\">";
                echo __("per");
                echo " <span>";
                echo $array_periodes[$tmp_key[1]];
                echo "</span></span>\n\t\t\t\t\t\t\t\t\t\t";
            }
            echo "\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t\t<span class=\"removeBillingCycle\">&nbsp;</span>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t";
        }
    }
}
echo "\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<a id=\"addBillingCycle\" class=\"a1 c1 normalfont pointer\">";
echo __("product custom prices add new period");
echo "</a><br />\t\t\t\t\t\t\n\t\t\t\t\t\t</div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t</div>\n\t\t\t\t<!--left-->\n\t\t\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t<div class=\"right\">\n\t\t\t\t<!--right-->\n\t\t\t\t\t\n\t\t\t\t\t<div id=\"domain_div\" ";
if($product->ProductType != "domain") {
    echo "class=\"hide\"";
}
echo ">\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("properties of domain product");
echo "</h3><div class=\"content\">\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("tld");
echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"ProductTld\" value=\"";
echo $product->ProductTld;
echo "\" ";
$ti = tabindex($ti);
echo " /> \n\t\t\t\t\t\t\t<div class=\"ico actionblock find mar2 pointer\" id=\"tld_search_icon\">&nbsp;</div>\n\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<div id=\"domain_div_new\" class=\"hide\">\n\t\t\t\t\t\t\t\t<div class=\"add alt1\">\n\t\t\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("registrar");
echo "\t\t\t\t\t\t\t\t\t\t<span class=\"infopopupright\">\n\t\t\t\t\t\t\t\t\t\t\t<em>";
echo __("more info");
echo "</em>\n\t\t\t\t\t\t\t\t\t\t\t<span class=\"popup\">\n\t\t\t\t\t\t\t\t\t\t\t\t<strong>";
echo __("explain this setting");
echo "</strong><br />\n\t\t\t\t\t\t\t\t\t\t\t\t";
echo __("product registrar explained");
echo "\t\t\t\t\t\t\t\t\t\t\t\t<b></b>\n\t\t\t\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t\t\t<select name=\"Registrar\" class=\"text1 size4\">\n\t\t\t\t\t\t\t\t\t\t<option value=\"\">";
echo __("select a registrar");
echo "</option>\n\t\t\t\t\t\t\t\t\t\t";
foreach ($list_registrars as $key => $value) {
    if(is_numeric($key)) {
        echo "\t\t\t\t\t\t\t\t\t\t<option value=\"";
        echo $key;
        echo "\">";
        echo $value["Name"];
        echo "</option>\n\t\t\t\t\t\t\t\t\t\t";
    }
}
echo "\t\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<div class=\"add alt1\">\t\n\t\t\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("cost domain ownerchange");
echo "\t\t\t\t\t\t\t\t\t\t<span class=\"infopopupright\">\n\t\t\t\t\t\t\t\t\t\t\t<em>";
echo __("more info");
echo "</em>\n\t\t\t\t\t\t\t\t\t\t\t<span class=\"popup\">\n\t\t\t\t\t\t\t\t\t\t\t\t<strong>";
echo __("explain this setting");
echo "</strong><br />\n\t\t\t\t\t\t\t\t\t\t\t\t";
echo __("product cost domain ownerchange explained");
echo "\t\t\t\t\t\t\t\t\t\t\t\t<b></b>\n\t\t\t\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t\t\t<select name=\"OwnerChangeCost\" class=\"text1 size4\">\n\t\t\t\t\t\t\t\t\t\t<option value=\"\">";
echo __("no cost domain ownerchange");
echo "</option>\n\t\t\t\t\t\t\t\t\t\t";
foreach ($products as $key => $value) {
    if(is_numeric($key)) {
        $price_ownerchange = VAT_CALC_METHOD == "incl" ? money($value["PriceIncl"], true) . " " . __("incl vat") : money($value["PriceExcl"], true) . (!empty($array_taxpercentages) ? " " . __("excl vat") : "");
        echo "\t\t\t\t\t\t\t\t\t\t<option value=\"";
        echo $key;
        echo "\">";
        echo $value["ProductCode"] . " - " . $value["ProductName"] . " (" . $price_ownerchange . ")";
        echo "</option>\n\t\t\t\t\t\t\t\t\t\t";
    }
}
echo "\t\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("public whois server");
echo "</strong>\n\t\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"WhoisServer\" value=\"\" maxlength=\"250\" />\t\n\t\t\t\t\t\t\t\t<span id=\"public_whois_status\" style=\"float: right;line-height: 30px;\"></span>\t\n\t\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("public whois server nomatch");
echo "</strong>\n\t\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"WhoisNoMatch\" value=\"\" maxlength=\"250\" />\t\t\n\t\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("ask for auth key");
echo "</strong>\n\t\t\t\t\t\t\t\t<select name=\"AskForAuthKey\" class=\"text1 size4\">\n\t\t\t\t\t\t\t\t\t<option value=\"yes\">";
echo __("yes");
echo "</option>\n\t\t\t\t\t\t\t\t\t<option value=\"no\" selected=\"selected\">";
echo __("no");
echo "</option>\n\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<div id=\"domain_div_existing\" class=\"hide\">\n\t\t\t\t\t\t\t\t<strong class=\"title2\">";
echo __("registrar");
echo "</strong>\n\t\t\t\t\t\t\t\t<span class=\"title2_value\" id=\"domain_div_existing_registrar\">&nbsp;</span>\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<strong class=\"title2\">";
echo __("cost domain ownerchange");
echo "</strong>\n\t\t\t\t\t\t\t\t<span class=\"title2_value\" id=\"domain_div_existing_costownerchange\">&nbsp;</span>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t</div></div>\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div>\n\n\t\t\t\t\t<div id=\"hosting_div\" ";
if($product->ProductType != "hosting") {
    echo "class=\"hide\"";
}
echo ">\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("properties of hosting product");
echo "</h3><div class=\"content\">\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("webhosting type");
echo "</strong>\n\t\t\t\t\t\t\t\t<select name=\"PackageType\" class=\"text1 size4\">\n\t\t\t\t\t\t\t\t\t";
foreach ($array_packagetypes as $key => $value) {
    echo "\t\t\t\t                        <option value=\"";
    echo $key;
    echo "\" ";
    if(isset($package->PackageType) && $package->PackageType == $key) {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo $value;
    echo "</option>\n\t\t\t\t                    ";
}
echo "\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("server");
echo "</strong>\n\t\t\t\t\t\t\t\t<select name=\"Server\" class=\"text1 size4\">\n\t\t\t\t\t\t\t\t\t<option value=\"\">";
echo __("make your choice");
echo "</option>\n\t\t\t\t\t\t\t\t\t";
foreach ($list_servers as $key => $value) {
    if(is_numeric($key)) {
        echo "\t\t\t\t\t\t\t\t\t<option value=\"";
        echo $key;
        echo "\" ";
        if($server->Identifier == $key) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo $value["Name"];
        echo "</option>\n\t\t\t\t\t\t\t\t\t";
    }
}
echo "\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("template");
echo "</strong>\n\t\t\t\t\t\t\t\t<span id=\"hosting_div_template\">";
echo __("no server selected yet");
echo "</span>\n\t\t\t\t\t\t\t\t<select name=\"TemplateName\" class=\"text1 size4 hide\">\n\t\n\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t<input type=\"hidden\" name=\"TemplateNameHidden\" value=\"";
if(0 < $product->PackageID) {
    echo "ex:" . $product->PackageID;
}
echo "\" />\n\t\t\t\t\t\t\t\t<br /><br />\n\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t</div></div>\n\t\t\t\t\t\t<!--box3-->\n\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t<div class=\"box3 hostingTemplates hide\"><h3>";
echo __("hosting account data briefing");
echo "</h3><div class=\"content\">\n\t\t\t\t\t\t\t\t<!--box3-->\n\n\t\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("hosting account data briefing mailtemplate");
echo "</strong>\n\t\t\t\t\t\t\t\t<select name=\"EmailTemplate\" class=\"text1 size4f\">\n\t\t\t\t\t\t\t\t\t<option value=\"\">";
echo __("make your choice");
echo "</option>\n\t\t\t\t\t\t\t\t\t";
foreach ($emailtemplates as $k => $v) {
    if(is_numeric($k)) {
        echo "<option value=\"";
        echo $k;
        echo "\" ";
        if(isset($package->EmailTemplate) && $k == $package->EmailTemplate) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo $v["Name"];
        echo "</option>";
    }
}
echo "\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t<br /><br />\n\n\t\t\t\t\t\t\t\t<div id=\"hosting_pdf_email_div\" class=\"";
if(!isset($package->EmailTemplate) || $package->EmailTemplate <= 0) {
    echo "hide";
}
echo "\">\n\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" id=\"pdf_email_sent_yes\" name=\"EmailAuto\" value=\"yes\" class=\"text1\" ";
if(!isset($package->EmailAuto) || $package->EmailAuto == "yes") {
    echo "checked=\"checked\"";
}
echo "/> <label for=\"pdf_email_sent_yes\">";
echo __("send hosting account briefing mail automatically");
echo "</label><br /><br />\n\t\t\t\t\t\t\t\t</div>\n\n\t\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("hosting account data briefing pdf");
echo "</strong>\n\t\t\t\t\t\t\t\t<select name=\"PdfTemplate\" class=\"text1 size4f\">\n\t\t\t\t\t\t\t\t\t<option value=\"\">";
echo __("make your choice");
echo "</option>\n\t\t\t\t\t\t\t\t\t";
foreach ($templates_other as $k => $v) {
    if(is_numeric($k)) {
        echo "<option value=\"";
        echo $k;
        echo "\" ";
        if(isset($package->PdfTemplate) && $k == $package->PdfTemplate) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo $v["Name"];
        echo "</option>";
    }
}
echo "\t\t\t\t\t\t\t\t</select>\n\n\t\t\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\t</div></div>\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div>\n\t\t\t\t\t\n\t\t\t\t\t<div id=\"producttype_module\" ";
if(!array_key_exists($product->ProductType, $additional_product_types)) {
    echo "class=\"hide\"";
}
echo ">\n\t\t\t\t\t";
if(array_key_exists($product->ProductType, $additional_product_types)) {
    $module_name = $product->ProductType;
    $namespace = "modules\\products\\" . $module_name;
    $classname = class_exists($namespace . "\\" . $module_name) ? $namespace . "\\" . $module_name : $module_name;
    $module = new $classname();
    if($product->Identifier) {
        $module->product_form_edit($product->Identifier);
    } else {
        $module->product_form_add();
    }
}
echo "\t\t\t\t\t</div>\n\t\t\t\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t</div>\n\t\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t<!--split2-->\n\t\t\t</div>\n\t\t\t<!--split2-->\n\t\t\t\t\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\t\t\t\n\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-extended-description\">\n\t\t<!--content-->\n\t\t\n\t\t\t<!--box3-->\n\t\t\t<div class=\"box3\"><h3>";
echo __("extended description");
echo "</h3><div class=\"content\">\n\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t<strong class=\"title\">";
echo __("extended product description explained");
echo "</strong>\n\t\t\t\t<textarea class=\"text1 size5\" name=\"ProductDescription\" ";
$ti = tabindex($ti);
echo ">";
echo $product->ProductDescription;
echo "</textarea>\n\n\t\t\t<!--box3-->\n\t\t\t</div></div>\n\t\t\t<!--box3-->\n\t\t\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\t\t\n\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-groups\">\n\t\t<!--content-->\n\t\t\t\n\t\t\t\t<p>\n\t\t\t\t\t<strong>";
echo __("connect products to productgroups");
echo "</strong>\n\t\t\t\t</p>\n\t\t\t\t\t\t\n\t\t\t\t<table id=\"MainTable\" class=\"table1\" cellpadding=\"0\" cellspacing=\"0\">\n\t\t\t\t\t<tr class=\"trtitle\">\n\t\t\t\t\t\t<th scope=\"col\"><label><input name=\"GroupBatch\" class=\"BatchCheck\" type=\"checkbox\" value=\"on\" /></label> ";
echo __("productgroup");
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
        if(array_key_exists($groupID, $product->Groups)) {
            echo "checked=\"checked\"";
        }
        echo "/> ";
        echo $group["GroupName"];
        echo "</label></td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t";
    }
}
if($groupCounter === 0) {
    echo "\t\t\t\t\t\t<tr class=\"tr1\">\n\t\t\t\t\t\t\t<td>";
    echo __("no productgroups found");
    echo "</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t";
}
echo "\t\t\t\t</table>\n\n\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\n\t<!--box1-->\n\t</div>\n\t<!--box1-->\n\n\t<br />\n\t\t\t\t\t\n\t<p class=\"align_right\">\n        <a class=\"button1 alt1\" id=\"form_create_btn\">\n            <span>";
echo $pagetype == "edit" ? __("btn edit") : __("btn add");
echo "</span>\n        </a>\n    </p>\n\t\n<!--form-->\n</fieldset></form>\n<!--form-->\n\n<div id=\"div_for_tldsearch\"></div>\n\n";
require_once "views/footer.php";

?>