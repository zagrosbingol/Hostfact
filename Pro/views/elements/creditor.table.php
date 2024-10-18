<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
function show_creditor_table($data_array, $options = [])
{
    $table_type = isset($options["table_type"]) ? $options["table_type"] : "subtable";
    $session_name = isset($options["session_name"]) ? $options["session_name"] : "";
    $current_page = isset($options["current_page"]) ? $options["current_page"] : "1";
    $current_page_url = isset($options["current_page_url"]) ? $options["current_page_url"] : "";
    $redirect_page = isset($options["redirect_page"]) ? $options["redirect_page"] : "";
    $redirect_id = isset($options["redirect_id"]) ? $options["redirect_id"] : "";
    $hide_columns = isset($options["hide_columns"]) ? $options["hide_columns"] : [];
    $column_count = 9 - count($hide_columns);
    $session = $_SESSION[$session_name];
    global $array_country;
    echo "\n\t<!--form-->\n\t<form action=\"creditors.php?page=view";
    if($redirect_page) {
        echo "&amp;from_page=" . $redirect_page;
    }
    if($redirect_id) {
        echo "&amp;from_id=" . $redirect_id;
    }
    echo "\" method=\"post\"><fieldset>\n\t<!--form-->\n\t\t\n\t\t<div id=\"SubTable_Creditors\">\t\t\t\n\t\t\t<table id=\"MainTable_Creditors\" class=\"table1\" cellpadding=\"0\" cellspacing=\"0\">\n\t\t\t<tr class=\"trtitle\">\n\t\t\t\t<th scope=\"col\" style=\"width: 125px;\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','CreditorCode','Creditors','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 ";
    if($session["sort"] == "CreditorCode") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("creditor no");
    echo "</a></th>\n\t\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','Creditor','Creditors','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 ";
    if($session["sort"] == "Creditor") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("name");
    echo "</a></td>\n\t\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','EmailAddress','Creditors','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 ";
    if($session["sort"] == "EmailAddress") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("emailaddress");
    echo "</a></th>\n\t\t\t\t<th scope=\"col\">";
    echo __("creditorgroup");
    echo "</th>\n\t\t\t\t<th scope=\"col\" colspan=\"3\">";
    echo __("open sum");
    echo "</th>\n\t\t\t\t<th scope=\"col\" style=\"width: 25px;\">&nbsp;</th>\n\t\t\t</tr>\n\t\t\t";
    $creditorCounter = 0;
    foreach ($data_array as $creditorID => $creditor) {
        if(is_numeric($creditorID)) {
            $creditorCounter++;
            $EmailAddress = check_email_address($creditor["EmailAddress"], "convert", ", ");
            echo "\t\t\t<tr class=\"hover_extra_info ";
            if($creditorCounter % 2 === 1) {
                echo "tr1";
            }
            echo "\">\n\t\t\t\t<td><a href=\"creditors.php?page=show&id=";
            echo $creditorID;
            echo "\" class=\"c1 a1\">";
            echo $creditor["CreditorCode"];
            echo "</a></td>\n\t\t\t\t<td><a href=\"creditors.php?page=show&id=";
            echo $creditorID;
            echo "\" class=\"a1\">";
            echo $creditor["CompanyName"] ? $creditor["CompanyName"] : $creditor["SurName"] . ", " . $creditor["Initials"];
            echo "</a></td>\n\t\t\t\t<td>";
            echo $EmailAddress != "" ? trim(substr($EmailAddress, 0, 50)) . (50 < strlen($EmailAddress) ? "..." : "") : "&nbsp;";
            echo "</td>\n\t\t\t\t<td>";
            $group_html = "";
            foreach ($creditor["Groups"] as $key2 => $value2) {
                if(is_numeric($key2)) {
                    $group_html .= $value2["GroupName"] . "; ";
                }
            }
            echo substr($group_html, 0, -2);
            echo "</td>\n\t\t\t\t<td style=\"width: 5px;\" class=\"currency_sign_left\">";
            echo currency_sign_td(CURRENCY_SIGN_LEFT);
            echo "</td>\n\t\t\t\t<td style=\"width: 60px;\" align=\"right\">";
            echo money(max(0, $creditor["AmountUnpaid"]), false);
            echo "</td>\n\t\t\t\t<td style=\"width: 45px;\" class=\"currency_sign_right\">";
            echo currency_sign_td(CURRENCY_SIGN_RIGHT);
            echo "</td>\n\t\t\t\t<td>&nbsp;\n\t\t\t\t<span class=\"ico actionblock tag nm hover_extra_info_span\">";
            echo __("more information");
            echo "</span></td>\n\t\t\t</tr>\n\t\t\t<tr class=\"tr_extra_info mark2\">\n\t\t\t\t<td colspan=\"2\" class=\"padding1\">\n\t\t\t\t\t";
            echo settings::getGenderTranslation($creditor["Sex"]);
            echo " ";
            echo $creditor["Initials"];
            echo " ";
            echo $creditor["SurName"];
            echo "<br />\n\t\t\t\t\t";
            echo $creditor["Address"];
            echo "<br />\n\t\t\t\t\t";
            echo $creditor["ZipCode"];
            echo "&nbsp;&nbsp;";
            echo $creditor["City"];
            echo "<br />\n\t\t\t\t\t";
            echo $array_country[$creditor["Country"]];
            echo "\t\t\t\t</td>\n\t\t\t\t<td colspan=\"2\">\n\t\t\t\t\t";
            if($creditor["PhoneNumber"]) {
                echo "<span class=\"ei_title\">";
                echo __("abbr_phonenumber");
                echo ":</span><span class=\"ei_value\">";
                echo phoneNumberLink($creditor["PhoneNumber"]);
                echo "</span>";
            }
            echo "\t\t\t\t\t";
            if($creditor["MobileNumber"]) {
                echo "<span class=\"ei_title\">";
                echo __("abbr_mobilenumber");
                echo ":</span><span class=\"ei_value\">";
                echo phoneNumberLink($creditor["MobileNumber"]);
                echo "</span>";
            }
            echo "\t\t\t\t\t";
            if($creditor["FaxNumber"]) {
                echo "<span class=\"ei_title\">";
                echo __("abbr_faxnumber");
                echo ":</span><span class=\"ei_value\">";
                echo $creditor["FaxNumber"];
                echo "</span>";
            }
            echo "\t\t\t\t</td>\n\t\t\t\t<td colspan=\"5\" class=\"lineheight1\">\n\t\t\t\t\t<a class=\"ico inline arrowrightwhite a1\" href=\"creditors.php?page=add_invoice&creditor=";
            echo $creditorID;
            echo "\">";
            echo __("create new creditinvoice");
            echo "</a>\n\t\t\t\t</td>\n\t\t\t</tr>\n\t\t\t";
        }
    }
    echo "\t\t\t";
    if($creditorCounter === 0) {
        echo "\t\t\t<tr>\n\t\t\t\t<td colspan=\"";
        echo $column_count;
        echo "\">\n\t\t\t\t\t";
        echo __("no creditors found");
        echo "\t\t\t\t</td>\n\t\t\t</tr>\n\t\t\t";
    }
    echo "\t\t\t\n\t\t\t";
    if(min(MIN_PAGINATION, $session["results"]) < $data_array["CountRows"]) {
        echo "\t\t\t<tr class=\"table_options\">\n\t\t\t\t<td colspan=\"";
        echo $column_count;
        echo "\">\n\t\t\t\t\t<br />\n\t\t\t\t\t";
        ajax_paginate("Creditors", isset($data_array["CountRows"]) ? $data_array["CountRows"] : 0, $session["results"], $current_page, $current_page_url);
        echo "\t\t\t\t</td>\n\t\t\t</tr>\n\t\t\t";
    }
    echo "\t\t\t</table>\n\t\t</div>\n\t\t\n\t<!--form-->\n\t</fieldset></form>\n\t<!--form-->\n\n\t";
}

?>