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
$page_form_title = 0 < $product_id ? __("edit productgroup") : __("add productgroup");
echo "\n";
echo $message;
echo "\n<!--form-->\n<form id=\"GroupForm\" name=\"form_create\" method=\"post\" action=\"products.php?page=add_group\"><fieldset><legend>";
echo $page_form_title;
echo "</legend>\n";
if(0 < $product_id) {
    echo "<input type=\"hidden\" name=\"id\" value=\"";
    echo $product_id;
    echo "\" />\n";
}
echo "<!--form-->\n\n<!--heading1-->\n<div class=\"heading1\">\n<!--heading1-->\n\n\t<h2>";
echo $page_form_title;
echo "</h2>\n\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n\t<!--box1-->\n\t<div class=\"box2\" id=\"tabs\">\n\t<!--box1-->\n\t\n\t\t<!--top-->\n\t\t<div class=\"top\">\n\t\t<!--top-->\n\t\t\n\t\t\t<ul class=\"list3\">\n\t\t\t\t<li class=\"on\"><a href=\"#tab-general\">";
echo __("general");
echo "</a></li>\n\t\t\t</ul>\n\t\t\t\n\t\t\n\t\t<!--top-->\n\t\t</div>\n\t\t<!--top-->\n\n\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-general\">\n\t\t<!--content-->\n\n\t\t\t\t\t\n\t\t\t<!--box3-->\n\t\t\t<div class=\"box3\"><h3>";
echo __("name");
echo "</h3><div class=\"content\">\n\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t<strong class=\"title\">";
echo __("name");
echo "</strong>\n\t\t\t\t<input type=\"text\" name=\"GroupName\" class=\"text1 size1\" value=\"";
echo $group->GroupName;
echo "\" />\n\t\t\t\t<br /><br />\n\n\t\t\t<!--box3-->\n\t\t\t</div></div>\n\t\t\t<!--box3-->\n\t\t\t\n\t\t\t<p>\n\t\t\t\t<strong>";
echo __("select products to connect to productgroup");
echo "</strong>\n\t\t\t</p>\n\t\t\t\n\t\t\t<input name=\"Groups\" type=\"hidden\" value=\",";
echo implode(",", $group->Products);
if(!empty($group->Products)) {
    echo ",";
}
echo "\" />\n\t\t\t\n\t\t\t<div id=\"SubTable_Products\">\t\t\t\t\t\t\t\t\n\t\t\t<table id=\"MainTable_Products\" class=\"table1\" cellpadding=\"0\" cellspacing=\"0\">\n\t\t\t\t<tr class=\"trtitle\">\n\t\t\t\t\t<th scope=\"col\" style=\"width: 125px;\"><label><input name=\"GroupBatch\" class=\"GroupsBatchCheck\" type=\"checkbox\" value=\"on\" /></label> <a onclick=\"ajaxSave('productgroup.add','sort','ProductCode','Products','";
echo $current_page_url;
echo "');\" class=\"ico set2 pointer ";
if($_SESSION["productgroup.add"]["sort"] == "ProductCode") {
    if($_SESSION["productgroup.add"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("product no");
echo "</a></th>\n\t\t\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('productgroup.add','sort','ProductName','Products','";
echo $current_page_url;
echo "');\" class=\"ico set2 pointer ";
if($_SESSION["productgroup.add"]["sort"] == "ProductName") {
    if($_SESSION["productgroup.add"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("name");
echo "</a></th>\n\t\t\t\t\t<th scope=\"col\" colspan=\"3\"><a onclick=\"ajaxSave('productgroup.add','sort','PriceExcl','Products','";
echo $current_page_url;
echo "');\" class=\"ico set2 pointer ";
if($_SESSION["productgroup.add"]["sort"] == "PriceExcl") {
    if($_SESSION["productgroup.add"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("amountexcl");
echo "</a></th>\n\t\t\t\t</tr>\n\t\t\t\t";
$productCounter = 0;
foreach ($products as $productID => $product) {
    if(is_numeric($productID)) {
        $productCounter++;
        echo "\t\t\t\t\t\t<tr";
        if($productCounter % 2 == 1) {
            echo " class=\"tr1\"";
        }
        echo ">\n\t\t\t\t\t\t\t<td><input name=\"Products[]\" type=\"checkbox\" class=\"GroupBatch\" value=\"";
        echo $productID;
        echo "\" ";
        if(in_array($productID, $group->Products)) {
            echo "checked=\"checked\"";
        }
        echo "/> ";
        echo $product["ProductCode"];
        echo "</td>\n\t\t\t\t\t\t\t<td>";
        echo $product["ProductName"];
        echo "</td>\n\t\t\t\t\t\t\t<td style=\"width: 5px;\" class=\"currency_sign_left\">";
        echo currency_sign_td(CURRENCY_SIGN_LEFT);
        echo "</td>\n\t\t\t\t\t\t\t<td style=\"width: 50px; padding-left: 0px;\" align=\"right\">";
        echo money($product["PriceExcl"], false);
        echo "</td>\n\t\t\t\t\t\t\t<td style=\"width: 75px;\" class=\"currency_sign_right\">";
        if(CURRENCY_SIGN_RIGHT) {
            echo CURRENCY_SIGN_RIGHT . " ";
        }
        if($product["PricePeriod"]) {
            echo __("per") . " " . $array_periodic[$product["PricePeriod"]];
        } else {
            echo "&nbsp;";
        }
        echo "</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t";
    }
}
if($productCounter === 0) {
    echo "\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td colspan=\"5\">\n\t\t\t\t\t\t\t";
    echo __("no results found");
    echo "\t\t\t\t\t\t</td>\n\t\t\t\t\t</tr>\n\t\t\t\t\t";
} elseif(MIN_PAGINATION < $products["CountRows"]) {
    echo "\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td colspan=\"5\">\n\t\t\t\t\t\t\t";
    ajax_paginate("Products", isset($products["CountRows"]) ? $products["CountRows"] : 0, $_SESSION["productgroup.add"]["results"], $current_page, $current_page_url);
    echo "\t\t\t\t\t\t</td>\n\t\t\t\t\t</tr>\n\t\t\t\t\t";
}
echo "\t\t\t</table>\n\t\t\t</div>\n\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\n\t<!--box1-->\n\t</div>\n\t<!--box1-->\n\n\t<br />\n\t\t\t\t\t\n\t<p class=\"align_right\">\n        <span id=\"selectedNumber\" class=\"c2";
if(empty($group->Products)) {
    echo " hide";
}
echo "\">\n            ";
echo sprintf(__("number of selected products"), "<strong>" . count($group->Products) . "</strong>");
echo "        </span> \n        <a class=\"button1 alt1\" id=\"form_create_btn\">\n            <span>";
echo 0 < $product_id ? __("btn edit") : __("btn add");
echo "</span>\n        </a>\n    </p>\n\t\n<!--form-->\n</fieldset></form>\n<!--form-->\n\t\n";
require_once "views/footer.php";

?>