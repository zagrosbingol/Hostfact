<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
function show_debtor_table($data_array, $options = [])
{
    $table_type = isset($options["table_type"]) ? $options["table_type"] : "subtable";
    $session_name = isset($options["session_name"]) ? $options["session_name"] : "";
    $current_page = isset($options["current_page"]) ? $options["current_page"] : "1";
    $current_page_url = isset($options["current_page_url"]) ? $options["current_page_url"] : "";
    $filter = isset($options["filter"]) ? $options["filter"] : "";
    $redirect_page = isset($options["redirect_page"]) ? $options["redirect_page"] : "";
    $redirect_id = isset($options["redirect_id"]) ? $options["redirect_id"] : "";
    $hide_columns = isset($options["hide_columns"]) ? $options["hide_columns"] : [];
    $column_count = 9 - count($hide_columns);
    $session = $_SESSION[$session_name];
    global $array_producttypes;
    global $array_country;
    echo "\t\n\t<!--form-->\n\t<form id=\"DebtorForm\" name=\"form_debtors\" method=\"post\" action=\"debtors.php?page=view";
    if($redirect_page) {
        echo "&amp;from_page=" . $redirect_page;
    }
    if($redirect_id) {
        echo "&amp;from_id=" . $redirect_id;
    }
    echo "\"><fieldset>\n\t<!--form-->\n\t\t\n\t\t<div id=\"SubTable_Debtors\">\t\t\n\t\t\t<table id=\"MainTable_Debtors\" class=\"table1\" cellpadding=\"0\" cellspacing=\"0\">\n\t\t\t<tr class=\"trtitle\">\n\t\t\t\t<th scope=\"col\" style=\"width: 120px;\"><label><input name=\"DebtorBatch\" class=\"BatchCheck\" type=\"checkbox\" value=\"on\" /></label> <a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','DebtorCode','Debtors','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 pointer ";
    if($session["sort"] == "DebtorCode") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("debtor no");
    echo "</a></th>\n\t\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','Debtor','Debtors','";
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
    echo __("name");
    echo "</a></th>\n\t\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','EmailAddress','Debtors','";
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
    echo __("debtorgroup");
    echo "</th>\n\t\t\t\t<th scope=\"col\" colspan=\"3\">";
    echo __("open sum");
    echo "</th>\n\t\t\t\t<th scope=\"col\" style=\"width: 25px;\">&nbsp;</th>\n\t\t\t</tr>\n\t\t\t";
    $debtorCounter = 0;
    foreach ($data_array as $debtorID => $debtor) {
        if(is_numeric($debtorID)) {
            $debtorCounter++;
            $EmailAddress = check_email_address($debtor["EmailAddress"], "convert", ", ");
            echo "\t\t\t<tr class=\"hover_extra_info ";
            if($debtorCounter % 2 === 1) {
                echo "tr1";
            }
            echo "\">\n\t\t\t\t<td><input name=\"id[]\" type=\"checkbox\" value=\"";
            echo $debtorID;
            echo "\" class=\"DebtorBatch\"/> <a href=\"debtors.php?page=show&id=";
            echo $debtorID;
            echo "\" class=\"c1 a1\">";
            echo $debtor["DebtorCode"];
            echo "</a></td>\n\t\t\t\t<td><a href=\"debtors.php?page=show&id=";
            echo $debtorID;
            echo "\" class=\"a1\">";
            echo $debtor["CompanyName"] ? $debtor["CompanyName"] : $debtor["SurName"] . ", " . $debtor["Initials"];
            echo "</a></td>\n\t\t\t\t<td>";
            echo $EmailAddress != "" ? trim(substr($EmailAddress, 0, 50)) . (50 < strlen($EmailAddress) ? "..." : "") : "&nbsp;";
            echo "</td>\n\t\t\t\t<td>";
            $group_html = "";
            foreach ($debtor["Groups"] as $key2 => $value2) {
                if(is_numeric($key2)) {
                    $group_html .= $value2["GroupName"] . "; ";
                }
            }
            echo substr($group_html, 0, -2);
            echo "</td>\n\t\t\t\t<td style=\"width: 5px;\" class=\"currency_sign_left\">";
            echo currency_sign_td(CURRENCY_SIGN_LEFT);
            echo "</td>\n\t\t\t\t<td style=\"width: 60px;\" align=\"right\">";
            echo money($debtor["OpenAmountIncl"], false);
            echo "</td>\n\t\t\t\t<td style=\"width: 45px;\" class=\"currency_sign_right\">";
            echo currency_sign_td(CURRENCY_SIGN_RIGHT);
            echo "</td>\n\t\t\t\t<td>&nbsp;\n\t\t\t\t<span class=\"ico actionblock tag nm hover_extra_info_span\">";
            echo __("more information");
            echo "</span></td>\n\t\t\t</tr>\n\t\t\t<tr class=\"tr_extra_info mark2\">\n\t\t\t\t<td colspan=\"2\" class=\"padding1\">\n\t\t\t\t\t";
            echo settings::getGenderTranslation($debtor["Sex"]);
            echo " ";
            echo $debtor["Initials"];
            echo " ";
            echo $debtor["SurName"];
            echo "<br />\n\t\t\t\t\t";
            echo $debtor["Address"];
            echo "<br />\n\t\t\t\t\t";
            echo $debtor["ZipCode"];
            echo "&nbsp;&nbsp;";
            echo $debtor["City"];
            echo "<br />\n\t\t\t\t\t";
            echo $array_country[$debtor["Country"]];
            echo "\t\t\t\t</td>\n\t\t\t\t<td colspan=\"2\">\n\t\t\t\t\t";
            if($debtor["PhoneNumber"]) {
                echo "<span class=\"ei_title\">";
                echo __("abbr_phonenumber");
                echo ":</span><span class=\"ei_value\">";
                echo phoneNumberLink($debtor["PhoneNumber"]);
                echo "</span>";
            }
            echo "\t\t\t\t\t";
            if($debtor["MobileNumber"]) {
                echo "<span class=\"ei_title\">";
                echo __("abbr_mobilenumber");
                echo ":</span><span class=\"ei_value\">";
                echo phoneNumberLink($debtor["MobileNumber"]);
                echo "</span>";
            }
            echo "\t\t\t\t\t";
            if($debtor["FaxNumber"]) {
                echo "<span class=\"ei_title\">";
                echo __("abbr_faxnumber");
                echo ":</span><span class=\"ei_value\">";
                echo $debtor["FaxNumber"];
                echo "</span>";
            }
            echo "\t\t\t\t</td>\n\t\t\t\t<td colspan=\"5\" class=\"lineheight1\">\n\t\t\t\t\t<a class=\"ico inline arrowrightwhite a1\" href=\"invoices.php?page=add&amp;debtor=";
            echo $debtorID;
            echo "\">";
            echo __("create new invoice");
            echo "</a><br />\n\t\t\t\t\t<a class=\"ico inline arrowrightwhite a1\" href=\"pricequotes.php?page=add&amp;debtor=";
            echo $debtorID;
            echo "\">";
            echo __("create new pricequote");
            echo "</a> <br />\n\t\t\t\t\t<a class=\"ico inline arrowrightwhite a1\" href=\"services.php?page=add&amp;debtor=";
            echo $debtorID;
            echo "\">";
            echo __("create new service");
            echo "</a>\n\t\t\t\t</td>\n\t\t\t</tr>\n\t\t\t";
        }
    }
    if($debtorCounter === 0) {
        echo "\t\t\t<tr>\n\t\t\t\t<td colspan=\"";
        echo $column_count;
        echo "\">\n\t\t\t\t\t";
        echo __("no results found");
        echo "\t\t\t\t</td>\n\t\t\t</tr>\n\t\t\t";
    }
    echo "\t\t\t";
    if(0 < $debtorCounter) {
        echo "\t\t\t<tr class=\"table_options\">\n\t\t\t\t<td colspan=\"";
        echo $column_count;
        echo "\">\n\t\t\t\t\t<p class=\"ico inline hook\">\n\t\t\t\t\t\t<select name=\"action\" class=\"select1 BatchSelect\">\n\t\t\t\t\t\t\t<option selected=\"selected\">";
        echo __("with selected");
        echo "</option>\n\t\t\t\t\t\t\t";
        if($filter == "archived") {
            echo "\t\t\t\t\t\t\t<option value=\"reactivate\">";
            echo __("undo delete debtor");
            echo "</option>\n\t\t\t\t\t\t\t<option value=\"dialog:anonimize\">";
            echo __("anonimize debtor");
            echo "</option>\n\t\t\t\t\t\t\t";
        } else {
            echo "\t\t\t\t\t\t\t<option value=\"mailing\">";
            echo __("send mailing");
            echo "</option>\n\t\t\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\t</select>\n\n\t\t\t\t\t\t";
        if($filter == "archived") {
            echo "\t\t\t\t\t\t\t<div class=\"hide\" id=\"dialog_anonimize\" title=\"";
            echo __("anonimize debtor title");
            echo "\">\n\n\t\t\t\t\t\t\t\t<input type=\"hidden\" name=\"page\" value=\"";
            echo $session_name;
            echo "\" />\n\n\n\t\t\t\t\t\t\t\t";
            echo __("anonimize debtor batch description");
            echo "<br />\n\t\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t\t<strong>";
            echo __("confirm action");
            echo "</strong><br />\n\t\t\t\t\t\t\t\t<label>\n\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"imsure\" value=\"yes\" onchange=\"\$('div[data-toggle=password]').toggle();\"/>\n\t\t\t\t\t\t\t\t\t";
            echo __("anonimize these debtors");
            echo "\t\t\t\t\t\t\t\t</label>\n\n\n\t\t\t\t\t\t\t\t<div data-toggle=\"password\" class=\"hide\">\n\t\t\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t\t\t<strong class=\"title\">";
            echo __("confirm with password");
            echo "</strong>\n\t\t\t\t\t\t\t\t\t<input type=\"password\" name=\"Password\" autocomplete=\"off\" />\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t";
        }
        echo "\t\t\t\t\t</p>\n\t\t\t\t\t\n\t\t\t\t\t<br />\n\t\t\t\t\t\n\t\t\t\t\t";
        if(min(MIN_PAGINATION, $session["results"]) < $data_array["CountRows"]) {
            echo "\t\t\t\t\t\t<br />\n\t\t\t\t\t\t";
            ajax_paginate("Debtors", isset($data_array["CountRows"]) ? $data_array["CountRows"] : 0, $session["results"], $current_page, $current_page_url);
            echo "\t\t\t\t\t";
        }
        echo "\t\t\t\t</td>\n\t\t\t</tr>\n\t\t\t";
    }
    echo "\t\t\t</table>\n\t\t</div>\n\t\t\n\t<!--form-->\n\t</fieldset></form>\n\t<!--form-->\n\t\n\t";
}

?>