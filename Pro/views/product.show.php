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
echo "\n<!--heading1-->\n<div class=\"heading1\">\n<!--heading1-->\n\n\t<h2>";
echo __("product") . " " . $product->ProductName;
echo "</h2>\n\t<p class=\"pos2\"><strong class=\"textsize1\">";
echo $product->ProductCode;
echo "</strong></p>\n\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\t\n\t<!--box1-->\n\t<div class=\"box2\" id=\"tabs\">\n\t<!--box1-->\n\t\n\t\t<!--top-->\n\t\t<div class=\"top\">\n\t\t<!--top-->\n\t\t\n\t\t\t<ul class=\"list3\">\n\t\t\t\t<li class=\"on\"><a href=\"#tab-general\">";
echo __("general");
echo "</a></li>\n\t\t\t\t<li><a href=\"#tab-extended-description\">";
echo __("extended description");
echo "</a></li>\n\t\t\t\t<li><a href=\"#tab-groups\">";
echo __("productgroups");
echo "</a></li>\n\t\t\t\t<li><a href=\"#tab-statistics\" id=\"tab-product-stats\">";
echo __("statistics");
echo "</a></li>\n\t\t\t</ul>\n\t\t\n\t\t<!--top-->\n\t\t</div>\n\t\t<!--top-->\n\n\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-general\">\n\t\t<!--content-->\n\n\t\t\t<!--split2-->\n\t\t\t<div class=\"split2\">\n\t\t\t<!--split2-->\n\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t<div class=\"left\">\n\t\t\t\t<!--left-->\n\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("general");
echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("productname");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
echo $product->ProductName;
echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("description");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\" style=\"word-wrap: break-word;margin-left: 140px;\">";
echo nl2br($product->ProductKeyPhrase);
echo "</span>\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("producttype");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
echo $array_producttypes[$product->ProductType];
echo "</span>\n\n\t\t\t\t\t\t";
if($product->NumberSuffix) {
    echo "\t\t\t\t\t\t\t<strong class=\"title2\">";
    echo __("product unit");
    echo "</strong>\n\t\t\t\t\t\t\t<span class=\"title2_value\">";
    echo $product->NumberSuffix;
    echo "</span>\n\t\t\t\t\t\t\t";
}
echo "\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t\n\t\t\t\t\t";
if($product->ProductType == "other") {
    echo "<!--left-->\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\t<!--left-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t<!--right-->\n\t\t\t\t\t\t<div class=\"right\">\n\t\t\t\t\t\t<!--right-->\n\t\t\t\t\t\t";
} else {
    echo "<br />";
}
echo "\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("financial");
echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t\t";
if(defined("VAT_CALC_METHOD") && VAT_CALC_METHOD == "incl") {
    echo "\t\t\t\n\t\t\t\t\t\t\t<strong class=\"title2\">";
    echo __("price incl");
    echo "</strong>\n\t\t\t\t\t\t\t<span class=\"title2_value\"><span class=\"span_width_amount\">";
    echo money($product->PriceIncl, true, false);
    echo " ";
    if($product->PricePeriod) {
        echo __("per") . " " . $array_periodic[$product->PricePeriod];
    }
    echo "</span>\n\t\t\t\t\t\t\t";
    if(!empty($array_taxpercentages)) {
        echo "<span class=\"span_product_excl_incl\">";
        echo money($product->PriceExcl);
        echo " ";
        echo __("excl vat");
        echo "</span>";
    }
    echo "\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t";
} else {
    echo "\t\t\t\t\t\t\t<strong class=\"title2\">";
    echo __("price excl");
    echo "</strong>\n\t\t\t\t\t\t\t<span class=\"title2_value\"><span class=\"span_width_amount\">";
    echo money($product->PriceExcl, true, false);
    echo " ";
    if($product->PricePeriod) {
        echo __("per") . " " . $array_periodic[$product->PricePeriod];
    }
    echo "</span>\n\t\t\t\t\t\t\t";
    if(!empty($array_taxpercentages)) {
        echo "<span class=\"span_product_excl_incl\">";
        echo money($product->PriceIncl);
        echo " ";
        echo __("incl vat");
        echo "</span>";
    }
    echo "\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t\n                        ";
if(!empty($array_taxpercentages)) {
    echo "\t\t\t\t\t\t<strong class=\"title2\">";
    echo __("vat percentage");
    echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
    echo vat($product->TaxPercentage * 100);
    echo "%</span>\n\t\t\t\t\t\t";
}
echo "                        \n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("costprice");
if(!empty($array_taxpercentages)) {
    echo " " . __("excl vat");
}
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
echo money($product->Cost);
echo "</span>\n\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t";
if($product->HasCustomPrice == "period") {
    echo "\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t<div id=\"CustomPricesDiv\" class=\"box3\"><h3>";
    echo __("product custom prices and periods");
    echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t";
    if(is_array($custom_prices["period"]) && 0 < count($custom_prices["period"])) {
        echo "<strong class=\"title2\">1 ";
        echo $array_periodic[$product->PricePeriod];
        echo " <span class=\"fontsmall c4\" style=\"font-weight:normal;\">- ";
        echo __("product custom prices default price");
        echo "</span></strong>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t";
        if(defined("VAT_CALC_METHOD") && VAT_CALC_METHOD == "incl") {
            echo "\t\t\t\t\t\t\t<span class=\"title2_value\"><span class=\"span_width_amount\">";
            echo money($product->PriceIncl, true, false);
            echo " ";
            if($product->PricePeriod) {
                echo __("per") . " " . $array_periodic[$product->PricePeriod];
            }
            echo "</span>\n\t\t\t\t\t\t\t";
            if(!empty($array_taxpercentages)) {
                echo "<span class=\"span_product_excl_incl\">";
                echo money($product->PriceExcl);
                echo " ";
                echo __("excl vat");
                echo "</span>";
            }
            echo "\t\t\t\t\t\t\t";
        } else {
            echo "\t\t\t\t\t\t\t<span class=\"title2_value\"><span class=\"span_width_amount\">";
            echo money($product->PriceExcl, true, false);
            echo " ";
            if($product->PricePeriod) {
                echo __("per") . " " . $array_periodic[$product->PricePeriod];
            }
            echo "</span>\n\t\t\t\t\t\t\t";
            if(!empty($array_taxpercentages)) {
                echo "<span class=\"span_product_excl_incl\">";
                echo money($product->PriceIncl);
                echo " ";
                echo __("incl vat");
                echo "</span>";
            }
            echo "\t\t\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\t\t</span>";
        foreach ($custom_prices["period"] as $key => $tmp_price) {
            if($key == "default") {
            } else {
                $key = explode("-", $key);
                echo "<strong class=\"title2\">";
                echo $key[0] . " " . (0 < $key[0] ? $array_periodesMV[$key[1]] : $array_periodic[$key[1]]);
                echo "</strong><span class=\"title2_value\">";
                if(defined("VAT_CALC_METHOD") && VAT_CALC_METHOD == "incl") {
                    echo "<span class=\"span_width_amount\">" . money($tmp_price["PriceIncl"], true, false) . " " . __("per") . " " . $array_periodic[$key[1]] . "</span> <span class=\"span_product_excl_incl\">" . money($tmp_price["PriceExcl"]) . " " . __("excl vat") . "</span>";
                    echo "</span>";
                } else {
                    echo "<span class=\"span_width_amount\">" . money($tmp_price["PriceExcl"], true, false) . " " . __("per") . " " . $array_periodic[$key[1]] . "</span> <span class=\"span_product_excl_incl\">" . money($tmp_price["PriceIncl"]) . " " . __("incl vat") . "</span>";
                    echo "</span>";
                }
                echo "</span>";
            }
        }
    }
    echo "\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t</div></div>\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t";
}
echo "\t\t\t\t\n\t\t\t\t\t";
if($product->ProductType != "other") {
    echo "<!--left-->\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\t<!--left-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t<!--right-->\n\t\t\t\t\t\t<div class=\"right\">\n\t\t\t\t\t\t<!--right-->\n\t\t\t\t\t\t";
}
echo "\t\t\t\t\n\t\t\t\t\t<!--box3-->\t\t\t\t\t\n\t\t\t\t\t";
if($product->ProductType == "domain") {
    echo "\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
    echo __("properties of domain product");
    echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
    echo __("tld");
    echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">.";
    echo $product->ProductTld;
    echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
    echo __("registrar");
    echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
    echo $tld->Name;
    echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
    echo __("cost domain ownerchange");
    echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">\n\t\t\t\t\t\t\t";
    if($tld->ProductCode) {
        echo "\t\t\t\t\t\t\t\t";
        echo $tld->ProductCode;
        echo " - ";
        echo $tld->ProductName;
        echo " (";
        echo money($tld->PriceExcl);
        echo ")\n\t\t\t\t\t\t\t";
    } else {
        echo "\t\t\t\t\t\t\t\t";
        echo __("no cost domain ownerchange");
        echo "\t\t\t\t\t\t\t";
    }
    echo "\t\t\n\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t<a href=\"topleveldomains.php?page=edit&amp;id=";
    echo $tld->Identifier;
    echo "\" class=\"a1 c1 smallfont\">";
    echo __("change tld settings");
    echo "</a>\n\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t";
} elseif($product->ProductType == "hosting") {
    echo "\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
    echo __("properties of hosting product");
    echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
    echo __("webhosting type");
    echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
    echo $array_packagetypes[$package->PackageType];
    echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
    echo __("server");
    echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
    echo $server->Name;
    echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
    echo __("template");
    echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
    if($package->Template == "yes") {
        echo $package->TemplateName;
    } else {
        echo __("use no package template");
    }
    echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<a href=\"packages.php?page=edit&amp;id=";
    echo $package->id;
    echo "\" class=\"a1 c1 smallfont\">";
    echo __("change package settings");
    echo "</a>\n\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t";
} elseif(array_key_exists($product->ProductType, $additional_product_types)) {
    $module_name = $product->ProductType;
    $namespace = "modules\\products\\" . $module_name;
    $classname = class_exists($namespace . "\\" . $module_name) ? $namespace . "\\" . $module_name : $module_name;
    $module = new $classname();
    $module->product_show($product->Identifier);
}
echo "\n\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t</div>\n\t\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t<!--split2-->\n\t\t\t</div>\n\t\t\t<!--split2-->\n\t\t\t\t\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\t\t\t\n\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-extended-description\">\n\t\t<!--content-->\n\t\t\n\t\t\t<!--box3-->\n\t\t\t<div class=\"box3\"><h3>";
echo __("extended description");
echo "</h3><div class=\"content\">\n\t\t\t<!--box3-->\n\t\t\t\n\t\t\t\t<textarea class=\"text1 size5 readonly\" disabled=\"disabled\">";
echo $product->ProductDescription;
echo "</textarea>\n\n\t\t\t<!--box3-->\n\t\t\t</div></div>\n\t\t\t<!--box3-->\n\t\t\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\t\t\n\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-groups\">\n\t\t<!--content-->\n\t\t\t\n\t\t\t\t<p>\n\t\t\t\t\t<strong>";
echo __("product is connected to productgroups");
echo "</strong>\n\t\t\t\t</p>\n\t\t\t\t\t\t\n\t\t\t\t<table id=\"MainTable\" class=\"table1\" cellpadding=\"0\" cellspacing=\"0\">\n\t\t\t\t\t<tr class=\"trtitle\">\n\t\t\t\t\t\t<th scope=\"col\">";
echo __("productgroups");
echo "</th>\n\t\t\t\t\t</tr>\n\t\t\t\t\t";
$groupCounter = 0;
foreach ($product->Groups as $groupID => $group) {
    if(is_numeric($groupID)) {
        $groupCounter++;
        echo "\t\t\t\t\t\t\t<tr";
        if($groupCounter % 2 == 1) {
            echo " class=\"tr1\"";
        }
        echo ">\n\t\t\t\t\t\t\t\t<td><a href=\"products.php?page=show_group&amp;id=";
        echo $groupID;
        echo "\" class=\"c1 a1\">";
        echo $group["GroupName"];
        echo "</a></td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t";
    }
}
if($groupCounter === 0) {
    echo "\t\t\t\t\t\t<tr class=\"tr1\">\n\t\t\t\t\t\t\t<td>";
    echo __("product is not connected to productgroups");
    echo "</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t";
}
echo "\t\t\t\t</table>\n\n\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\t\t\n\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-statistics\">\n\t\t<!--content-->\n\t\t\t\n\t\t\t<!--split2-->\n\t\t\t<div class=\"split2\">\n\t\t\t<!--split2-->\n\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t<div class=\"left\">\n\t\t\t\t<!--left-->\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("product statistics");
echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t<p>\n\t\t\t\t\t\t\t";
echo __("product statistics description");
echo "\t\t\t\t\t\t</p>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<table class=\"table5\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n\t\t\t\t\t\t\t<thead>\n\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t<th>&nbsp;</th>\n\t\t\t\t\t\t\t\t\t<th colspan=\"2\" align=\"right\">";
echo date("Y");
echo "</th>\n\t\t\t\t\t\t\t\t\t<th>&nbsp;</th>\n\t\t\t\t\t\t\t\t\t<th colspan=\"2\" align=\"right\">";
echo date("Y") - 1;
echo "</th>\n\t\t\t\t\t\t\t\t\t";
if(CURRENCY_SIGN_RIGHT) {
    echo "<th>&nbsp;</th>";
}
echo "\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t</thead>\n\t\t\t\t\t\t\t<tbody>\n\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t<th align=\"left\">";
echo __("sold");
echo "</th>\n\t\t\t\t\t\t\t\t\t<td colspan=\"2\" align=\"right\">";
echo $productStats[date("Y")]["Sold"];
echo "</td>\n\t\t\t\t\t\t\t\t\t<td>&nbsp;</td>\n\t\t\t\t\t\t\t\t\t<td colspan=\"2\" align=\"right\">";
echo $productStats[date("Y") - 1]["Sold"];
echo "</td>\n\t\t\t\t\t\t\t\t\t";
if(CURRENCY_SIGN_RIGHT) {
    echo "<td>&nbsp;</td>";
}
echo "\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t<th align=\"left\">";
echo __("sales");
echo "</th>\n\t\t\t\t\t\t\t\t\t<td style=\"width: 10px;\" class=\"currency_sign_left\">";
echo currency_sign_td(CURRENCY_SIGN_LEFT);
echo "</td>\n\t\t\t\t\t\t\t\t\t<td style=\"width: 90px;\" align=\"right\">";
echo money($productStats[date("Y")]["Sales"], false);
echo "</td>\n\t\t\t\t\t\t\t\t\t<td style=\"width: 25px;\" class=\"currency_sign_right\">";
echo currency_sign_td(CURRENCY_SIGN_RIGHT);
echo "</td>\n\t\t\t\t\t\t\t\t\t<td style=\"width: 10px;\">";
echo currency_sign_td(CURRENCY_SIGN_LEFT);
echo "</td>\n\t\t\t\t\t\t\t\t\t<td style=\"width: 90px;\" align=\"right\" class=\"currency_sign_right\">";
echo money($productStats[date("Y") - 1]["Sales"], false);
echo "</td>\n\t\t\t\t\t\t\t\t\t";
if(CURRENCY_SIGN_RIGHT) {
    echo "<td style=\"width: 25px;\" class=\"currency_sign_right\">";
    echo currency_sign_td(CURRENCY_SIGN_RIGHT);
    echo "</td>";
}
echo "\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t<th style=\"border-top:1px solid #222;\" align=\"left\">";
echo __("profit");
echo "</th>\n\t\t\t\t\t\t\t\t\t<td style=\"border-top:1px solid #222;\" class=\"currency_sign_left\">";
echo currency_sign_td(CURRENCY_SIGN_LEFT);
echo "</td>\n\t\t\t\t\t\t\t\t\t<td style=\"border-top:1px solid #222;\" align=\"right\">";
echo money($productStats[date("Y")]["Sales"] - $productStats[date("Y")]["Sold"] * $product->Cost, false);
echo "</td>\n\t\t\t\t\t\t\t\t\t<td style=\"border-top:1px solid #222;\" class=\"currency_sign_right\">";
echo currency_sign_td(CURRENCY_SIGN_RIGHT);
echo "</td>\n\t\t\t\t\t\t\t\t\t<td style=\"border-top:1px solid #222;\" class=\"currency_sign_left\">";
echo currency_sign_td(CURRENCY_SIGN_LEFT);
echo "</td>\n\t\t\t\t\t\t\t\t\t<td style=\"border-top:1px solid #222;\" align=\"right\" class=\"currency_sign_right\">";
echo money($productStats[date("Y") - 1]["Sales"] - $productStats[date("Y") - 1]["Sold"] * $product->Cost, false);
echo "</td>\n\t\t\t\t\t\t\t\t\t";
if(CURRENCY_SIGN_RIGHT) {
    echo "<td style=\"border-top:1px solid #222;\" class=\"currency_sign_right\">";
    echo currency_sign_td(CURRENCY_SIGN_RIGHT);
    echo "</td>";
}
echo "\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t</tbody>\n\t\t\t\t\t\t</table>\n\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t</div>\n\t\t\t\t<!--left-->\n\t\t\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t<div class=\"right\">\n\t\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("product graphical statistics");
echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t<div id=\"placeholder\" style=\"width: 100%; height: 178px;\"></div>\n\t\t\t\t\t\t<script language=\"javascript\" type=\"text/javascript\" src=\"js/jquery.flot.js\"></script>\n\t\t\t\t\t\t<script language=\"javascript\" type=\"text/javascript\" src=\"js/excanvas.mini.js\"></script>\n\t\t\t\t\t\t<script id=\"source\" language=\"javascript\" type=\"text/javascript\">\n\t\t\t\t\t\t\$(function () {\n\t\t\t\t\t\t\t\$('#tab-product-stats').click(function(){\n\t\t\t\t\t\t\t    var d1 = [";
for ($i = 1; $i <= 12; $i++) {
    echo "[" . $i . ", " . $productStats[date("Y") - 1][$i]["Sales"] . "], ";
}
echo "];\n\t\t\t\t\t\t\t    var d2 = [";
for ($i = 1; $i <= 12; $i++) {
    echo "[" . $i . ", " . $productStats[date("Y")][$i]["Sales"] . "], ";
}
echo "];\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t    \$.plot(\$(\"#placeholder\"), [ { data: d1, label: \"";
echo date("Y") - 1;
echo "\", color: 2 }, { data: d2, label: \"";
echo date("Y");
echo "\", color: 1 } ],{ legend: { position: 'nw' } , yaxis: {min: 0, minTickSize: 1, tickFormatter: yAxisMoneyFormatter}, xaxis: {  ticks: [[1, \"";
echo $array_months_short[1];
echo "\"], [2, \"";
echo $array_months_short[2];
echo "\"], [3, \"";
echo $array_months_short[3];
echo "\"],[4, \"";
echo $array_months_short[4];
echo "\"],[5, \"";
echo $array_months_short[5];
echo "\"], [6, \"";
echo $array_months_short[6];
echo "\"],[7, \"";
echo $array_months_short[7];
echo "\"],[8, \"";
echo $array_months_short[8];
echo "\"],[9, \"";
echo $array_months_short[9];
echo "\"],[10, \"";
echo $array_months_short[10];
echo "\"],[11, \"";
echo $array_months_short[11];
echo "\"],[12, \"";
echo $array_months_short[12];
echo "\"]] }});\n\t\t\t\t\t\t    });\n\t\t\t\t\t\t});\n\t\t\t\t\t\t\n\t\t\t\t\t\tfunction yAxisMoneyFormatter(v) {\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\tvar result = formatAsMoney(v).slice(0,-3);\n\t\t\t\t\t\t\tif(CURRENCY_SIGN_LEFT){ \n\t\t\t\t\t\t\t\t\tresult = CURRENCY_SIGN_LEFT + \" \" + result; \n\t\t\t\t\t\t\t}\n\t\t\t\t\t\t\tif(CURRENCY_SIGN_RIGHT){ \n\t\t\t\t\t\t\t\tresult = result + \" \" + CURRENCY_SIGN_RIGHT; \n\t\t\t\t\t\t\t}\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\treturn result;\n\t\t\t\t\t\t}\n\t\t\t\t\t\t\n\t\t\t\t\t\t</script>\n\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t</div>\n\t\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t<!--split2-->\n\t\t\t</div>\n\t\t\t<!--split2-->\n\n\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\n\t<!--box1-->\n\t</div>\n\t<!--box1-->\n\n\t<br />\n\t\t\t\n\t<!--buttonbar-->\n\t<div class=\"buttonbar\">\n\t<!--buttonbar-->\n\t\n\t\t";
if(U_PRODUCT_EDIT) {
    echo "<p class=\"pos1\"><a class=\"button1 edit_icon\" href=\"products.php?page=edit&amp;id=";
    echo $product->Identifier;
    echo "\"><span>";
    echo __("edit");
    echo "</span></a></p>";
}
echo "\t\t";
if(U_PRODUCT_DELETE) {
    echo "<p class=\"pos2\"><a class=\"button1 delete_icon\" onclick=\"\$('#delete_product').dialog('open');\"><span>";
    echo __("delete");
    echo "</span></a></p>";
}
echo "\t\t\n\t<!--buttonbar-->\n\t</div>\n\t<!--buttonbar-->\n\t\n\t<br />\n\t\n";
if($periodics["CountRows"]) {
    echo "\t\n\n<!--box1-->\n<div class=\"box2\" id=\"subtabs\">\n<!--box1-->\n\t\n\t<!--top-->\n\t<div class=\"top\">\n\t<!--top-->\n\t\t\n\t\t<ul class=\"list3\">\n\t\t\t<li class=\"on\"><a href=\"#subtab-subscriptions\">";
    echo __("subscriptions with this product");
    echo " (";
    echo $periodics["CountRows"];
    echo ")</a></li>\n\t\t</ul>\n\t\t\n\t<!--top-->\n\t</div>\n\t<!--top-->\n\n\t<!--content-->\n\t<div class=\"content\" id=\"subtab-subscriptions\">\n\t<!--content-->\n\t\t";
    echo __("product subscriptions");
    echo "\t\t\t\t\n\t\t<form id=\"ProductSubscriptionForm\" name=\"form_subscriptions\" method=\"post\" action=\"products.php?page=show&amp;id=";
    echo $product->Identifier;
    echo "\">\n\t\t\n\t\t<div id=\"SubTable_Subscriptions\">\t\n\t\t<table id=\"MainTable_Subscriptions\" class=\"table1\" cellpadding=\"0\" cellspacing=\"0\">\n\t\t<tr class=\"trtitle\">\n\t\t\t<th scope=\"col\"><label><input name=\"SubscriptionBatch\" class=\"BatchCheck\" type=\"checkbox\" value=\"on\" /></label> <a onclick=\"ajaxSave('product.show.subscription','sort','Description','Subscriptions','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 pointer ";
    if($_SESSION["product.show.subscription"]["sort"] == "Description") {
        if($_SESSION["product.show.subscription"]["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("description");
    echo "</a></th>\n\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('product.show.subscription','sort','Debtor','Subscriptions','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 pointer ";
    if($_SESSION["product.show.subscription"]["sort"] == "Debtor") {
        if($_SESSION["product.show.subscription"]["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("debtor");
    echo "</a></td>\n\t\t\t<th scope=\"col\" colspan=\"3\"><a onclick=\"ajaxSave('product.show.subscription','sort','AmountExcl','Subscriptions','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 pointer ";
    if($_SESSION["product.show.subscription"]["sort"] == "AmountExcl") {
        if($_SESSION["product.show.subscription"]["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("amountexcl");
    echo "</a></th>\n\t\t\t<th scope=\"col\" colspan=\"3\"><a onclick=\"ajaxSave('product.show.subscription','sort','AmountIncl','Subscriptions','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 pointer ";
    if($_SESSION["product.show.subscription"]["sort"] == "AmountIncl") {
        if($_SESSION["product.show.subscription"]["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("amountincl");
    echo "</a></th>\n\t\t\t<th scope=\"col\" style=\"width: 100px;\"><a onclick=\"ajaxSave('product.show.subscription','sort','NextDate','Subscriptions','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 pointer ";
    if($_SESSION["product.show.subscription"]["sort"] == "NextDate") {
        if($_SESSION["product.show.subscription"]["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("next invoice date");
    echo "</a></th>\n\t\t\t<th scope=\"col\" style=\"width: 150px;\"><a onclick=\"ajaxSave('product.show.subscription','sort','StartPeriod','Subscriptions','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 pointer ";
    if($_SESSION["product.show.subscription"]["sort"] == "StartPeriod") {
        if($_SESSION["product.show.subscription"]["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("period to invoice");
    echo "</a></th>\n\t\t</tr>\n\t\t";
    $subscriptionCounter = 0;
    foreach ($periodics as $subscriptionID => $subscription) {
        if(is_numeric($subscriptionID)) {
            $subscriptionCounter++;
            echo "\t\t<tr class=\"hover_extra_info ";
            if($subscriptionCounter % 2 == 1) {
                echo "tr1";
            }
            echo "\">\n\t\t\t<td>\n\t\t\t\t<input name=\"periodic[]\" type=\"checkbox\" class=\"SubscriptionBatch\" value=\"";
            echo $subscriptionID;
            echo "\" />\n\t\t\t\t";
            if($subscription["Number"] != 1 || $subscription["NumberSuffix"]) {
                echo "\t\t\t\t\t<span>\n\t\t\t\t\t";
                echo $subscription["NumberSuffix"] ? showNumber($subscription["Number"]) . $subscription["NumberSuffix"] : showNumber($subscription["Number"]) . "x";
                echo "\t\t\t\t\t</span>\n\t\t\t\t\t";
            }
            echo "\t\t\t\t<a href=\"services.php?page=show&amp;id=";
            echo $subscriptionID;
            echo "\" class=\"c1 a1\">\n\t\t\t\t\t";
            echo $subscription["Description"];
            echo "\t\t\t\t</a>\n\t\t\t</td>\n\t\t\t<td><a href=\"debtors.php?page=show&amp;id=";
            echo $subscription["Debtor"];
            echo "\" class=\"c1 a1\">";
            echo $subscription["CompanyName"] ? $subscription["CompanyName"] : $subscription["SurName"] . ", " . $subscription["Initials"];
            echo "</a></td>\n\t\t\t<td style=\"width: 5px;\" class=\"currency_sign_left\">";
            echo currency_sign_td(CURRENCY_SIGN_LEFT);
            echo "</td>\n\t\t\t<td style=\"width: 50px; padding-left: 0px;\" align=\"right\">";
            echo money($subscription["AmountExcl"], false);
            echo "</td>\n\t\t\t<td style=\"width: 55px;\" class=\"currency_sign_right\">";
            echo currency_sign_td(CURRENCY_SIGN_RIGHT);
            echo "</td>\n\t\t\t<td style=\"width: 5px;\" class=\"currency_sign_left\">";
            echo currency_sign_td(CURRENCY_SIGN_LEFT);
            echo "</td>\n\t\t\t<td style=\"width: 50px; padding-left: 0px;\" align=\"right\">";
            echo money($subscription["AmountIncl"], false);
            echo "</td>\n\t\t\t<td style=\"width: 55px;\" class=\"currency_sign_right\">";
            echo currency_sign_td(CURRENCY_SIGN_RIGHT);
            echo "</td>\n\t\t\t<td>";
            echo $subscription["NextDate"];
            echo "</td>\n\t\t\t<td>";
            echo $subscription["StartPeriod"];
            echo " ";
            echo __("till");
            echo " ";
            echo $subscription["EndPeriod"];
            echo "</td>\n\t\t</tr>\n\t\t";
        }
    }
    echo "\t\t<tr class=\"table_options\">\n\t\t\t<td colspan=\"10\">\n\t\t\t\t";
    if(0 < $subscriptionCounter) {
        echo "\t\t\t\t<p class=\"ico inline hook\">\n\t\t\t\t\t<select name=\"action\" class=\"select1 BatchSelect\">\n\t\t\t\t\t\t<option value=\"\" selected=\"selected\">";
        echo __("with selected");
        echo "</option>\n\t\t\t\t\t\t<option value=\"dialog:sync\">";
        echo __("sync price subscriptions");
        echo "</option>\n\t\t\t\t\t\t<option value=\"dialog:removeConnectedPeriodic\">";
        echo __("delete connection");
        echo "</option>\n\t\t\t\t\t</select>\n\t\t\t\t\t<div class=\"hide\" id=\"dialog_removeConnectedPeriodic\">";
        echo __("batchdialog subscription removeConnectedPeriodic");
        echo "</div>\n\t\t\t\t\t<div class=\"hide\" id=\"dialog_sync\">";
        echo __("batchdialog subscription sync");
        echo "</div>\n\t\t\t\t</p>\n\t\t\t\t\n\t\t\t\t<br />\n\t\t\t\t";
    }
    echo "\t\t\t\t\n\t\t\t\t";
    if(MIN_PAGINATION < $periodics["CountRows"]) {
        echo "<br />";
        ajax_paginate("Subscriptions", isset($periodics["CountRows"]) ? $periodics["CountRows"] : 0, $_SESSION["product.show.subscription"]["results"], $current_page, $current_page_url);
    }
    echo "\t\t\t</td>\n\t\t</tr>\n\t\t</table>\n\t\t</div>\n\t\t\n\t\t</form>\n\t\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\t\n<!--box1-->\n</div>\n<!--box1-->\n\n";
}
echo "\n";
if(U_PRODUCT_DELETE) {
    echo "<div id=\"delete_product\" class=\"hide ";
    if(isset($pagetype) && $pagetype == "confirmDelete") {
        echo "autoopen";
    }
    echo "\" title=\"";
    echo __("deletedialog product title");
    echo "\">\n\t<form id=\"ProductForm\" name=\"form_delete\" method=\"post\" action=\"products.php?page=delete\">\n\t<input type=\"hidden\" name=\"id\" value=\"";
    echo $product->Identifier;
    echo "\"/>\n\t";
    echo sprintf(__("deletedialog product description"), $product->ProductCode . " " . $product->ProductName);
    echo "<br />\n       \n\t<br />\n\t<input type=\"checkbox\" id=\"imsure\" name=\"imsure\" value=\"yes\"/> <label for=\"imsure\">";
    echo __("delete this product");
    echo "</label><br />\n\t<br />\n\t<p><a id=\"delete_product_btn\" class=\"button2 alt1 float_left\"><span>";
    echo __("delete");
    echo "</span></a></p>\n\t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#delete_product').dialog('close');\"><span>";
    echo __("cancel");
    echo "</span></a></p>\n\t\n\t";
    if(isset($_SESSION["ActionLog"]) && isset($_SESSION["ActionLog"]["Product"]) && is_array($_SESSION["ActionLog"]["Product"]["delete"]) && 1 < count($_SESSION["ActionLog"]["Product"]["delete"])) {
        echo "\t\t<br class=\"clear\"/><br />\n\t    <b>";
        echo __("progress batch actions");
        echo "</b><br />\n\t\t";
        if(count($_SESSION["ActionLog"]["Product"]["delete"]) - 1 != 1) {
            echo sprintf(__("progress batch multiple"), count($_SESSION["ActionLog"]["Product"]["delete"]) - 1);
        } else {
            echo sprintf(__("progress batch one"), count($_SESSION["ActionLog"]["Product"]["delete"]) - 1);
        }
        echo "    ";
    }
    echo "\t\n\t</form>\n</div>\n";
}
echo "\n";
require_once "views/footer.php";

?>