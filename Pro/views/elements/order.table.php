<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
function show_order_table($data_array, $options = [])
{
    $table_type = isset($options["table_type"]) ? $options["table_type"] : "subtable";
    $session_name = isset($options["session_name"]) ? $options["session_name"] : "";
    $current_page = isset($options["current_page"]) ? $options["current_page"] : "1";
    $current_page_url = isset($options["current_page_url"]) ? $options["current_page_url"] : "";
    $redirect_page = isset($options["redirect_page"]) ? $options["redirect_page"] : "";
    $redirect_id = isset($options["redirect_id"]) ? $options["redirect_id"] : "";
    $hide_columns = isset($options["hide_columns"]) ? $options["hide_columns"] : [];
    $column_count = 11 - count($hide_columns);
    $session = $_SESSION[$session_name];
    global $array_country;
    global $array_orderstatus;
    echo "\t\n\t<form id=\"OrderForm\" name=\"form_orders\" method=\"post\" action=\"orders.php?page=view";
    if($redirect_page) {
        echo "&amp;from_page=" . $redirect_page;
    }
    if($redirect_id) {
        echo "&amp;from_id=" . $redirect_id;
    }
    echo "\"><fieldset>\t\n\t\t\n\t\t<div id=\"SubTable_Orders\">\n\t\t\t<table id=\"MainTable_Orders\" class=\"table1\" cellpadding=\"0\" cellspacing=\"0\">\n\t\t\t<tr class=\"trtitle\">\n\t\t\t\t<th scope=\"col\"><label><input name=\"OrderBatch\" class=\"BatchCheck\" type=\"checkbox\" value=\"on\" /></label> <a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','OrderCode','Orders','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 ";
    if($session["sort"] == "OrderCode") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("order no");
    echo "</a></td>\n\t\t\t\t";
    if(!in_array("Debtor", $hide_columns)) {
        echo "<th scope=\"col\"><a onclick=\"ajaxSave('";
        echo $session_name;
        echo "','sort','Debtor','Orders','";
        echo $current_page_url;
        echo "');\" class=\"ico set2 ";
        if($session["sort"] == "Debtor") {
            if($session["order"] == "ASC") {
                echo "arrowup";
            } else {
                echo "arrowdown";
            }
        } else {
            echo "arrowhover";
        }
        echo "\">";
        echo __("debtor");
        echo "</a></td>";
    }
    echo "\t\t\t\t<th scope=\"col\" width=\"100\" colspan=\"3\" class=\"show_col_ws\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','AmountExcl','Orders','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 ";
    if($session["sort"] == "AmountExcl") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("amountexcl");
    echo "</a></th>\n\t\t\t\t<th scope=\"col\" width=\"100\" colspan=\"3\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','AmountIncl','Orders','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 ";
    if($session["sort"] == "AmountIncl") {
        if($_SESSION["order.overview"]["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("amountincl");
    echo "</a></td>\n\t\t\t\t<th scope=\"col\" width=\"100\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','Date','Orders','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 ";
    if($session["sort"] == "Date") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("order date");
    echo "</a></td>\n\t\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','Status','Orders','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 ";
    if($session["sort"] == "Status") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("status");
    echo "</a></td>\n\t\t\t\t<th scope=\"col\" width=\"30\">&nbsp;</td>\n\t\t\t</tr>\n\t\t\t";
    $orderCounter = 0;
    $post_invoices = false;
    foreach ($data_array as $orderID => $order) {
        if(is_numeric($orderID)) {
            $orderCounter++;
            if(isset($order["InvoiceMethod"]) && 0 < $order["InvoiceMethod"]) {
                $post_invoices = true;
            }
            echo "\t\t\t<tr class=\"hover_extra_info ";
            if($orderCounter % 2 === 1) {
                echo "tr1";
            }
            echo "\">\n\t\t\t\t<td><input name=\"id[]\" type=\"checkbox\" class=\"OrderBatch\" value=\"";
            echo $orderID;
            echo "\" /> <a href=\"orders.php?page=show&id=";
            echo $orderID;
            echo "\" class=\"";
            echo 0 < $order["Paid"] ? "c1" : "c1";
            echo " a1\">";
            echo $order["OrderCode"];
            echo "</a>\n\t\t\t\t\t";
            if($order["Comment"] != "") {
                echo "\t\t\t\t\t\t<span class=\"ico inline comment infopopuptop\" style=\"float:none;\">\n\t\t\t\t\t\t\t&nbsp;\n\t\t\t\t\t\t\t<span class=\"popup\">\n\t\t\t\t\t\t\t\t<strong>";
                echo __("remark");
                echo "</strong><br />\n\t\t\t\t\t\t\t\t";
                echo substr(str_replace("\r\n", "", nl2br($order["Comment"])), 0, 250);
                echo "\t\t\t\t\t\t\t\t<b></b>\n\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t</span>\n\t\t\t\t\t";
            }
            echo "\t\t\t\t\t";
            if($order["Authorisation"] == "yes") {
                echo "<span class=\"fontsmall c4\">";
                echo __("inc");
                echo "</span>";
            }
            echo "\t\t\t\t\t";
            if(0 < $order["Paid"]) {
                echo "<span class=\"fontsmall c2\">- ";
                echo __("action paid");
                echo "</span>";
            }
            echo "\t\t\t\t</td>\n\t\t\t\t";
            if(!in_array("Debtor", $hide_columns)) {
                echo "<td>\n\t\t\t\t\t";
                if($order["Type"] == "debtor") {
                    echo "\t\t\t\t\t\t<a href=\"debtors.php?page=show&id=";
                    echo $order["Debtor"];
                    echo "\" class=\"a1\">";
                    echo $order["CompanyName"] ? $order["CompanyName"] : $order["SurName"] . ", " . $order["Initials"];
                    echo "</a>\n\t\t\t\t\t";
                } else {
                    echo $order["CompanyName"] ? $order["CompanyName"] : $order["SurName"] . ", " . $order["Initials"];
                }
                echo "\t\t\t\t</td>";
            }
            echo "\t\t\t\t<td style=\"width: 5px;\" class=\"currency_sign_left show_col_ws\">";
            echo currency_sign_td(CURRENCY_SIGN_LEFT);
            echo "</td>\n\t\t\t\t<td style=\"width: 60px;\" class=\"show_col_ws\" align=\"right\">";
            echo money($order["AmountExcl"], false);
            echo "</td>\n\t\t\t\t<td style=\"width: 45px;\" class=\"currency_sign_right show_col_ws\">";
            echo currency_sign_td(CURRENCY_SIGN_RIGHT);
            echo "</td>\n\t\t\t\t<td style=\"width: 5px;\" class=\"currency_sign_left\">";
            echo currency_sign_td(CURRENCY_SIGN_LEFT);
            echo "</td>\n\t\t\t\t<td style=\"width: 60px;\" align=\"right\">";
            echo money($order["AmountIncl"], false);
            echo "</td>\n\t\t\t\t<td style=\"width: 15px;\" class=\"currency_sign_right\">";
            echo currency_sign_td(CURRENCY_SIGN_RIGHT);
            echo "</td>\n\t\t\t\t<td>";
            echo rewrite_date_db2site($order["Date"]);
            echo "</td>\n\t\t\t\t<td>";
            echo $array_orderstatus[$order["Status"]];
            echo "</td>\t\n\t\t\t\t<td>&nbsp;\n\t\t\t\t";
            if(!in_array("Debtor", $hide_columns)) {
                echo "<span class=\"ico actionblock tag nm hover_extra_info_span\">";
                echo __("more information");
                echo "</span>";
            }
            echo "</td>\n\t\t\t</tr>\n\t\t\t";
            if(!in_array("Debtor", $hide_columns)) {
                echo "\t\t\t<tr class=\"tr_extra_info mark2\">\n\t\t\t\t<td>&nbsp;</td>\n\t\t\t\t<td colspan=\"4\">\n\t\t\t\t\t";
                echo $order["Initials"];
                echo " ";
                echo $order["SurName"];
                echo "<br />\n\t\t\t\t\t";
                echo $order["Address"];
                echo "<br />\n\t\t\t\t\t";
                echo $order["ZipCode"];
                echo "&nbsp;&nbsp;";
                echo $order["City"];
                echo "<br />\n\t\t\t\t\t";
                echo $array_country[$order["Country"]];
                echo "\t\t\t\t</td>\n\t\t\t\t<td colspan=\"4\">\t\t\t\n\t\t\t\t\t";
                if($order["PhoneNumber"]) {
                    echo "<span class=\"ei_title\">";
                    echo __("abbr_phonenumber");
                    echo ":</span><span class=\"ei_value\">";
                    echo phoneNumberLink($order["PhoneNumber"]);
                    echo "</span>";
                }
                echo "\t\t\t\t\t";
                if($order["MobileNumber"]) {
                    echo "<span class=\"ei_title\">";
                    echo __("abbr_mobilenumber");
                    echo ":</span><span class=\"ei_value\">";
                    echo phoneNumberLink($order["MobileNumber"]);
                    echo "</span>";
                }
                echo "\t\t\t\t\t";
                if($order["EmailAddress"]) {
                    echo "<span class=\"ei_title\">";
                    echo __("abbr_emailaddress");
                    echo ":</span><span class=\"ei_value\">";
                    echo $order["EmailAddress"];
                    echo "</span>";
                }
                echo "\t\t\t\t</td>\n\t\t\t\t<td colspan=\"2\" class=\"lineheight1\">\n\t\t\t\t\t";
                if(U_ORDER_EDIT && $order["Status"] <= 2) {
                    echo "\t\t\t\t\t\t<a class=\"ico inline arrowrightwhite a1\" href=\"orders.php?page=show&action=makeinvoice&id=";
                    echo $orderID;
                    echo "\">";
                    echo __("order makeinvoice");
                    echo "</a>\n\t\t\t\t\t";
                }
                echo "\t\t\t\t</td>\n\t\t\t</tr>\n\t\t\t";
            }
            echo "\t\t\t";
        }
    }
    if($orderCounter === 0) {
        echo "\t\t\t<tr>\n\t\t\t\t<td colspan=\"";
        echo $column_count;
        echo "\">\n\t\t\t\t\t";
        echo __("no results found");
        echo "\t\t\t\t</td>\n\t\t\t</tr>\n\t\t\t";
    }
    echo "\t\t\t";
    if(0 < $orderCounter) {
        echo "\t\t\t<tr class=\"table_options\">\n\t\t\t\t<td colspan=\"";
        echo $column_count;
        echo "\">\n\t\t\t\t\t";
        if(U_ORDER_EDIT) {
            echo "\t\t\t\t\t<p class=\"ico inline hook\">\n\t\t\t\t\t\t<select name=\"action\" class=\"select1 BatchSelect\">\n\t\t\t\t\t\t\t<option selected=\"selected\">";
            echo __("with selected");
            echo "</option>\n\t\t\t\t\t\t\t<option value=\"dialog:ordermakeinvoice\">";
            echo __("order makeinvoice");
            echo "</option>\n\t\t\t\t\t\t\t<option value=\"delete\">";
            echo __("delete order");
            echo "</option>\n\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<div class=\"hide\" id=\"dialog_ordermakeinvoice\" title=\"";
            echo __("order makeinvoice");
            echo "\">\n\t\t\t\t\t\t\t<strong>";
            echo __("confirm action");
            echo "</strong><br />\n\t\t\t\t\t\t\t";
            echo __("batchdialog order makeinvoice");
            echo "\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\n\t\t\t\t\t</p>\n\t\t\t\t\t\n\t\t\t\t\t<br />\n\t\t\t\t\t\n\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\n\t\t\t\t\t";
        if(min(MIN_PAGINATION, $session["results"]) < $data_array["CountRows"]) {
            echo "\t\t\t\t\t\t<br />\t\t\n\t\t\t\t\t\t";
            ajax_paginate("Orders", isset($data_array["CountRows"]) ? $data_array["CountRows"] : 0, $session["results"], $current_page, $current_page_url);
            echo "\t\t\t\t\t";
        }
        echo "\t\t\t\t</td>\n\t\t\t</tr>\n\t\t\t";
    }
    echo "\t\t\t</table>\n\t\t</div>\n\t\n\t</fieldset></form>\n\t";
}

?>