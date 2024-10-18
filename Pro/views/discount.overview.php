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
echo isset($message) ? $message : "";
echo "\n<!--heading1-->\n<div class=\"heading1\">\n<!--heading1-->\n\n\t<h2>";
echo __("discount module");
echo "</h2>\n\n\t<p class=\"pos2\"><strong class=\"textsize1\">";
echo __("total");
echo ": <span>";
echo isset($discount_list["CountRows"]) ? $discount_list["CountRows"] : "0";
echo "</span></strong></p>\n\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n<!--form-->\n<form action=\"\"><fieldset>\n<!--form-->\n\t";
if(U_PRODUCT_ADD) {
    echo "\t\n\t<p>\n\t\t<a class=\"button1 add_icon\" href=\"discount.php?page=add\"><span>";
    echo __("new discount");
    echo "</span></a>\n\t</p>\n\t";
}
echo "\t\n\t<div id=\"SubTable_Discount\">\t\t\t\t\n\t<table id=\"MainTable_Discount\" class=\"table1\" cellpadding=\"0\" cellspacing=\"0\">\n\t<tr class=\"trtitle\">\n\t\t<th scope=\"col\"><a onclick=\"save('discount.overview','sort','Name','";
echo $current_page_url;
echo "');\" class=\"ico set2 ";
if($_SESSION["discount.overview"]["sort"] == "Name") {
    if($_SESSION["discount.overview"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("discount name");
echo "</a></th>\n\t\t<th scope=\"col\" style=\"width: 125px;\"><a onclick=\"save('discount.overview','sort','StartDate','";
echo $current_page_url;
echo "');\" class=\"ico set2 ";
if($_SESSION["discount.overview"]["sort"] == "StartDate") {
    if($_SESSION["discount.overview"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("discount startdate");
echo "</a></th>\n\t\t<th scope=\"col\" style=\"width: 125px;\"><a onclick=\"save('discount.overview','sort','EndDate','";
echo $current_page_url;
echo "');\" class=\"ico set2 ";
if($_SESSION["discount.overview"]["sort"] == "EndDate") {
    if($_SESSION["discount.overview"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("discount enddate");
echo "</a></th>\n\t\t<th scope=\"col\" style=\"width: 125px;\"><a onclick=\"save('discount.overview','sort','Counter','";
echo $current_page_url;
echo "');\" class=\"ico set2 ";
if($_SESSION["discount.overview"]["sort"] == "Counter") {
    if($_SESSION["discount.overview"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("discount counter");
echo "</a></th>\n\t</tr>\n\t";
$discountCounter = 0;
foreach ($discount_list as $discountID => $discount) {
    if(is_numeric($discountID)) {
        $discountCounter++;
        echo "\t<tr class=\"hover_extra_info ";
        if($discountCounter % 2 === 1) {
            echo "tr1";
        }
        echo "\">\n\t\t<td><a href=\"discount.php?page=show&amp;id=";
        echo $discountID;
        echo "\" class=\"c1 a1\">";
        echo $discount["Name"];
        echo "</a></td>\n\t\t<td>";
        echo !empty($discount["StartDate"]) ? rewrite_date_db2site($discount["StartDate"]) : "-";
        echo "</td>\n\t\t<td>";
        echo !empty($discount["EndDate"]) ? rewrite_date_db2site($discount["EndDate"]) : "-";
        echo "</td>\n\t\t<td>";
        echo $discount["Counter"];
        echo " ";
        if(isset($discount["Max"]) && 0 < $discount["Max"]) {
            echo "/ ";
            echo $discount["Max"];
        }
        echo "</td>\n\t</tr>\n\t";
    }
}
if($discountCounter === 0) {
    echo "\t<tr>\n\t\t<td colspan=\"4\">\n\t\t\t";
    echo __("no results found");
    echo "\t\t</td>\n\t</tr>\n\t";
} elseif(min(MIN_PAGINATION, $_SESSION["discount.overview"]["results"]) < $discount_list["CountRows"]) {
    echo "\t<tr>\n\t\t<td colspan=\"4\">\n\t\t\t<br />";
    ajax_paginate("Discount", isset($discount_list["CountRows"]) ? $discount_list["CountRows"] : 0, $_SESSION["discount.overview"]["results"], $current_page, $current_page_url);
    echo "\t\t</td>\n\t</tr>\n\t";
}
echo "\t</table>\n\t</div>\t\n\t\n<!--form-->\n</fieldset></form>\n<!--form-->\n\n";
require_once "views/footer.php";

?>