<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
function show_product_table($data_array, $options = [])
{
    $table_type = isset($options["table_type"]) ? $options["table_type"] : "subtable";
    $session_name = isset($options["session_name"]) ? $options["session_name"] : "";
    $current_page = isset($options["current_page"]) ? $options["current_page"] : "1";
    $current_page_url = isset($options["current_page_url"]) ? $options["current_page_url"] : "";
    $redirect_page = isset($options["redirect_page"]) ? $options["redirect_page"] : "";
    $redirect_id = isset($options["redirect_id"]) ? $options["redirect_id"] : "";
    $hide_columns = isset($options["hide_columns"]) ? $options["hide_columns"] : [];
    $column_count = 11 - count($hide_columns);
    $groups = isset($options["groups"]) ? $options["groups"] : "";
    $session = $_SESSION[$session_name];
    global $array_producttypes;
    global $array_periodic;
    echo "\t\n\t<!--form-->\n\t<form action=\"products.php?page=overview";
    if($redirect_page) {
        echo "&amp;from_page=" . $redirect_page;
    }
    if($redirect_id) {
        echo "&amp;from_id=" . $redirect_id;
    }
    echo "\" method=\"post\"><fieldset>\n\t<!--form-->\n\t\t\n\t\t<div id=\"SubTable_Products\">\n\t\t\t<table id=\"MainTable_Products\" class=\"table1\" cellpadding=\"0\" cellspacing=\"0\">\n\t\t\t<tr class=\"trtitle\">\n\t\t\t\t<th scope=\"col\" style=\"width: 100px;\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','ProductCode','Products','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 ";
    if($session["sort"] == "ProductCode") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("product no");
    echo "</a></th>\n\t\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','ProductName','Products','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 ";
    if($session["sort"] == "ProductName") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("productname");
    echo "</a></td>\n\t\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','ProductType','Products','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 ";
    if($session["sort"] == "ProductType") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("producttype");
    echo "</a></td>\n\t\t\t\t\n\t\t\t\t<th scope=\"col\" ";
    if(VAT_CALC_METHOD == "incl") {
        echo "class=\"show_col_ws\"";
    }
    echo " colspan=\"3\" style=\"width: ";
    echo VAT_CALC_METHOD == "incl" ? "85" : "140";
    echo "px;\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','PriceExcl','Products','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 ";
    if($session["sort"] == "PriceExcl") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("price excl");
    echo "</a></th>\n\t\t\t\t<th scope=\"col\" ";
    if(VAT_CALC_METHOD == "excl") {
        echo "class=\"show_col_ws\"";
    }
    echo " colspan=\"3\" style=\"width: ";
    echo VAT_CALC_METHOD == "excl" ? "85" : "140";
    echo "px;\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','PriceIncl','Products','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 ";
    if($session["sort"] == "PriceIncl") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("price incl");
    echo "</a></th>\n\t\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','Sold','Products','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 ";
    if($session["sort"] == "Sold") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("sold");
    echo "</a></td>\n\t\t\t\t<th scope=\"col\">";
    echo __("productgroups");
    echo "</th>\n\t\t\t</tr>\n\t\t\t";
    $productCounter = 0;
    foreach ($data_array as $productID => $product) {
        if(is_numeric($productID)) {
            $productCounter++;
            echo "\t\t\t<tr class=\"hover_extra_info ";
            if($productCounter % 2 === 1) {
                echo "tr1";
            }
            echo "\">\n\t\t\t\t<td><a href=\"products.php?page=show&amp;id=";
            echo $productID;
            echo "\" class=\"c1 a1\">";
            echo $product["ProductCode"];
            echo "</a></td>\n\t\t\t\t<td><a href=\"products.php?page=show&amp;id=";
            echo $productID;
            echo "\" class=\"a1\">";
            echo $product["ProductName"];
            echo "</a></td>\n\t\t\t\t<td>";
            echo $array_producttypes[$product["ProductType"]];
            echo "</td>\n\t\t\t\t\n\t\t\t\t<td style=\"width: 5px;\" class=\"";
            if(VAT_CALC_METHOD == "incl") {
                echo "show_col_ws ";
            }
            echo "currency_sign_left\">";
            echo currency_sign_td(CURRENCY_SIGN_LEFT);
            echo "</td>\n\t\t\t\t<td style=\"width: 50px; padding-left: 0px;\" class=\"";
            if(VAT_CALC_METHOD == "incl") {
                echo "show_col_ws ";
            }
            echo "\" align=\"right\">";
            echo money($product["PriceExcl"], false);
            echo "</td>\n\t\t\t\t<td class=\"";
            if(VAT_CALC_METHOD == "incl") {
                echo "show_col_ws ";
            }
            echo "currency_sign_right\" style=\"width: ";
            echo VAT_CALC_METHOD == "incl" ? "20" : "75";
            echo "px;\">";
            if(CURRENCY_SIGN_RIGHT) {
                echo CURRENCY_SIGN_RIGHT . " ";
            }
            if($product["PricePeriod"] && VAT_CALC_METHOD == "excl") {
                echo __("per") . " " . $array_periodic[$product["PricePeriod"]];
            } else {
                echo "&nbsp;";
            }
            echo "</td>\n\t\t\t\t\n\t\t\t\t<td class=\"";
            if(VAT_CALC_METHOD == "excl") {
                echo "show_col_ws ";
            }
            echo " currency_sign_left\" style=\"width: 5px;\">";
            echo currency_sign_td(CURRENCY_SIGN_LEFT);
            echo "</td>\n\t\t\t\t<td class=\"";
            if(VAT_CALC_METHOD == "excl") {
                echo "show_col_ws ";
            }
            echo "\" style=\"width: 50px; padding-left: 0px;\" align=\"right\">";
            echo money($product["PriceIncl"], false);
            echo "</td>\n\t\t\t\t<td class=\"";
            if(VAT_CALC_METHOD == "excl") {
                echo "show_col_ws ";
            }
            echo "currency_sign_right\" style=\"width: ";
            echo VAT_CALC_METHOD == "excl" ? "20" : "75";
            echo "px;\">";
            if(CURRENCY_SIGN_RIGHT) {
                echo CURRENCY_SIGN_RIGHT . " ";
            }
            if($product["PricePeriod"] && VAT_CALC_METHOD == "incl") {
                echo __("per") . " " . $array_periodic[$product["PricePeriod"]];
            } else {
                echo "&nbsp;";
            }
            echo "</td>\n\t\t\t\t\n\t\t\t\t\n\t\t\t\t<td>";
            echo $product["Sold"];
            echo "x</td>\n\t\t\t\t<td>";
            $group_html = "";
            foreach ($product["Groups"] as $key2 => $value2) {
                if(is_numeric($key2)) {
                    $group_html .= $value2["GroupName"] . "; ";
                }
            }
            echo substr($group_html, 0, -2);
            echo "</td>\n\t\t\t</tr>\n\t\t\t";
        }
    }
    if($productCounter === 0) {
        echo "\t\t\t<tr>\n\t\t\t\t<td colspan=\"";
        echo $column_count;
        echo "\">\n\t\t\t\t\t";
        echo __("no products found");
        echo "\t\t\t\t</td>\n\t\t\t</tr>\n\t\t\t";
    }
    echo "\t\t\t";
    if(min(MIN_PAGINATION, $session["results"]) < $data_array["CountRows"]) {
        echo "\t\t\t<tr class=\"table_options\">\n\t\t\t\t<td colspan=\"";
        echo $column_count;
        echo "\">\n\t\t\t\t\t<br />\n\t\t\t\t\t";
        ajax_paginate("Products", isset($data_array["CountRows"]) ? $data_array["CountRows"] : 0, $session["results"], $current_page, $current_page_url);
        echo "\t\t\t\t</td>\n\t\t\t</tr>\n\t\t\t";
    }
    echo "\t\t\t</table>\n\t\t</div>\n\t\t\n\t<!--form-->\n\t</fieldset></form>\n\t<!--form-->\n\n\t";
}

?>