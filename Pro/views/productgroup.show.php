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
echo __("productgroup");
echo " ";
echo $group->GroupName;
echo " </h2>\n\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n<!--box1-->\n<div class=\"box2\" id=\"tabs\">\n<!--box1-->\n\n\t<!--top-->\n\t<div class=\"top\">\n\t<!--top-->\n\t\n\t\t<ul class=\"list3\">\n\t\t\t<li class=\"on\"><a href=\"#tab-general\">";
echo __("general");
echo "</a></li>\n\t\t</ul>\t\t\t\n\t\t\n\t<!--top-->\n\t</div>\n\t<!--top-->\n\n\t<!--content-->\n\t<div class=\"content\" id=\"tab-general\">\n\t<!--content-->\n\n\t\t<p>\n\t\t\t<strong>";
echo __("the following products are in the productgroup");
echo "</strong>\n\t\t</p>\n\n\t\t<div id=\"SubTable_Products\">\t\t\t\t\t\t\t\t\n\t\t<table id=\"MainTable_Products\" class=\"table1\" cellpadding=\"0\" cellspacing=\"0\">\n\t\t\t<tr class=\"trtitle\">\n\t\t\t\t<th scope=\"col\" style=\"width: 125px;\"><a onclick=\"ajaxSave('productgroup.show','sort','ProductCode','Products','";
echo $current_page_url;
echo "');\" class=\"ico set2 pointer ";
if($_SESSION["productgroup.show"]["sort"] == "ProductCode") {
    if($_SESSION["productgroup.show"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("product no");
echo "</a></th>\n\t\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('productgroup.show','sort','ProductName','Products','";
echo $current_page_url;
echo "');\" class=\"ico set2 pointer ";
if($_SESSION["productgroup.show"]["sort"] == "ProductName") {
    if($_SESSION["productgroup.show"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("name");
echo "</a></th>\n\t\t\t\t<th scope=\"col\" colspan=\"3\"><a onclick=\"ajaxSave('productgroup.show','sort','PriceExcl','Products','";
echo $current_page_url;
echo "');\" class=\"ico set2 pointer ";
if($_SESSION["productgroup.show"]["sort"] == "PriceExcl") {
    if($_SESSION["productgroup.show"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("amountexcl");
echo "</a></th>\t\t\t\t\n\t\t\t</tr>\n\t\t\t";
$productCounter = 0;
foreach ($products as $productID => $product) {
    if(is_numeric($productID)) {
        $productCounter++;
        echo "\t\t\t\t\t<tr";
        if($productCounter % 2 === 1) {
            echo " class=\"tr1\"";
        }
        echo ">\n\t\t\t\t\t\t<td><a href=\"products.php?page=show&amp;id=";
        echo $productID;
        echo "\" class=\"c1 a1\">";
        echo $product["ProductCode"];
        echo "</a></td>\n\t\t\t\t\t\t<td>";
        echo $product["ProductName"];
        echo "</td>\n\t\t\t\t\t\t<td style=\"width: 5px;\" class=\"currency_sign_left\">";
        echo currency_sign_td(CURRENCY_SIGN_LEFT);
        echo "</td>\n\t\t\t\t\t\t<td style=\"width: 50px; padding-left: 0px;\" align=\"right\">";
        echo money($product["PriceExcl"], false);
        echo "</td>\n\t\t\t\t\t\t<td style=\"width: 75px;\" class=\"currency_sign_right\">";
        if(CURRENCY_SIGN_RIGHT) {
            echo CURRENCY_SIGN_RIGHT . " ";
        }
        if($product["PricePeriod"]) {
            echo __("per") . " " . $array_periodic[$product["PricePeriod"]];
        } else {
            echo "&nbsp;";
        }
        echo "</td>\n\t\t\t\t\t</tr>\n\t\t\t\t\t";
    }
}
if($productCounter === 0) {
    echo "\t\t\t\t<tr class=\"tr1\">\n\t\t\t\t\t<td colspan=\"5\">";
    echo __("no products found");
    echo "</td>\n\t\t\t\t</tr>\n\t\t\t\t";
}
if(0 * MIN_PAGINATION < $products["CountRows"]) {
    echo "\t\t\t\t<tr>\n\t\t\t\t\t<td colspan=\"5\">\n\t\t\t\t\t\t<br />\n\t\t\t\t\t\t";
    ajax_paginate("Products", isset($products["CountRows"]) ? $products["CountRows"] : 0, $_SESSION["productgroup.show"]["results"], $current_page, $current_page_url);
    echo "\t\t\t\t\t</td>\n\t\t\t\t</tr>\n\t\t\t\t";
}
echo "\t\t</table>\n\t\t</div>\n\t\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\n<!--box1-->\n</div>\n<!--box1-->\n\n<br />\n\t\n<!--buttonbar-->\n<div class=\"buttonbar\">\n<!--buttonbar-->\n\t\n\t";
if(U_PRODUCT_EDIT) {
    echo "<p class=\"pos1\"><a class=\"button1 edit_icon\" href=\"products.php?page=add_group&amp;id=";
    echo $group->Identifier;
    echo "\"><span>";
    echo __("edit");
    echo "</span></a></p>";
}
echo "\t";
if(U_PRODUCT_DELETE) {
    echo "<p class=\"pos2\"><a class=\"button1 delete_icon\" onclick=\"\$('#delete_group').dialog('open');\"><span>";
    echo __("delete");
    echo "</span></a></p>";
}
echo "\n<!--buttonbar-->\n</div>\n<!--buttonbar-->\n\n";
if(U_PRODUCT_DELETE) {
    echo "<div id=\"delete_group\" class=\"hide\" title=\"";
    echo __("delete productgroup dialog title");
    echo "\">\n\t<form id=\"GroupForm\" name=\"form_delete\" method=\"post\" action=\"products.php?page=delete_group&amp;id=";
    echo $group->Identifier;
    echo "\">\n\t";
    echo sprintf(__("delete productgroup dialog description"), $group->GroupName);
    echo "<br />\n\t<br />\n\t<input type=\"checkbox\" name=\"imsure\" value=\"yes\" id=\"imsure\"/> <label for=\"imsure\">";
    echo __("delete productgroup dialog agree");
    echo "</label><br />\n\t<br />\n\t<p><a id=\"delete_group_btn\" class=\"button2 alt1 float_left\"><span>";
    echo __("delete");
    echo "</span></a></p>\n\t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#delete_group').dialog('close');\"><span>";
    echo __("cancel");
    echo "</span></a></p>\n\t</form>\n</div>\n";
}
echo "\n";
require_once "views/footer.php";

?>