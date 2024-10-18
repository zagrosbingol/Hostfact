<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
function show_pricequote_table($data_array, $options = [])
{
    $table_type = isset($options["table_type"]) ? $options["table_type"] : "subtable";
    $session_name = isset($options["session_name"]) ? $options["session_name"] : "";
    $current_page = isset($options["current_page"]) ? $options["current_page"] : "1";
    $current_page_url = isset($options["current_page_url"]) ? $options["current_page_url"] : "";
    $redirect_page = isset($options["redirect_page"]) ? $options["redirect_page"] : "";
    $redirect_id = isset($options["redirect_id"]) ? $options["redirect_id"] : "";
    $hide_columns = isset($options["hide_columns"]) ? $options["hide_columns"] : [];
    $column_count = 12 - count($hide_columns);
    $session = $_SESSION[$session_name];
    global $array_invoicemethod;
    global $array_pricequotestatus;
    global $array_country;
    echo "\t\n\t<form id=\"PriceQuoteForm\" name=\"form_pricequotes\" method=\"post\" action=\"pricequotes.php?page=view";
    if($redirect_page) {
        echo "&amp;from_page=" . $redirect_page;
    }
    if($redirect_id) {
        echo "&amp;from_id=" . $redirect_id;
    }
    echo "\"><fieldset>\n\t\n\t<div id=\"SubTable_PriceQuotes\">\n\t\t<table id=\"MainTable_PriceQuotes\" class=\"table1\" cellpadding=\"0\" cellspacing=\"0\">\n\t\t<tr class=\"trtitle\">\n\t\t\t<th scope=\"col\" width=\"120\"><label><input name=\"PriceQuoteBatch\" class=\"BatchCheck\" type=\"checkbox\" value=\"on\" /></label> <a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','PriceQuoteCode','PriceQuotes','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 ";
    if($session["sort"] == "PriceQuoteCode") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("pricequote no");
    echo "</a></th>\n\t\t\t";
    if(!in_array("Debtor", $hide_columns)) {
        echo "<th scope=\"col\"><a onclick=\"ajaxSave('";
        echo $session_name;
        echo "','sort','Debtor','PriceQuotes','";
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
        echo "</a></th>";
    }
    echo "\t\t\t<th scope=\"col\" width=\"100\" colspan=\"3\" class=\"show_col_widescreen_medium\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','AmountExcl','PriceQuotes','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 ";
    if($session["sort"] == "AmountIncl") {
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
    echo "</a></th>\n\t\t\t<th scope=\"col\" width=\"100\" colspan=\"3\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','AmountIncl','PriceQuotes','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 ";
    if($session["sort"] == "AmountIncl") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("amountincl");
    echo "</a></th>\n\t\t\t<th scope=\"col\" width=\"100\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','Date','PriceQuotes','";
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
    echo __("pricequote date");
    echo "</a></th>\n            <th scope=\"col\" width=\"150\" class=\"show_col_widescreen_large\">\n                <a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','ReferenceNumber','PriceQuotes','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 \n                ";
    if($session["sort"] == "ReferenceNumber") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">\n                    ";
    echo __("reference number");
    echo "                </a>\n            </th>\n            <th scope=\"col\" width=\"180\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','Status','PriceQuotes','";
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
    echo "</a></th>\n\t\t\t";
    if(!in_array("Debtor", $hide_columns)) {
        echo "<th scope=\"col\" width=\"30\">&nbsp;</th>";
    }
    echo "\t\t</tr>\n\t\t";
    $pricequoteCounter = 0;
    $post_invoices = false;
    foreach ($data_array as $pricequoteID => $pricequote) {
        if(is_numeric($pricequoteID)) {
            $pricequoteCounter++;
            $printmethod = "";
            if(isset($pricequote["PriceQuoteMethod"]) && 0 < $pricequote["PriceQuoteMethod"]) {
                $post_invoices = true;
                $printmethod = " printmethod";
            }
            echo "\t\t<tr class=\"hover_extra_info ";
            if($pricequoteCounter % 2 === 1) {
                echo "tr1";
            }
            echo "\">\n\t\t\t<td><input name=\"id[]\" type=\"checkbox\" class=\"PriceQuoteBatch\" value=\"";
            echo $pricequoteID;
            echo "\" /> <a href=\"pricequotes.php?page=show&id=";
            echo $pricequoteID;
            echo "\" class=\"c1 a1";
            echo $printmethod;
            echo "\">";
            echo $pricequote["PriceQuoteCode"];
            echo "</a>\n\t\t\t";
            if($pricequote["PriceQuoteMethod"] != STANDARD_INVOICEMETHOD) {
                echo "<br /><span class=\"fontsmall c4\" style=\"margin-left:24px;\">";
                echo strtolower($array_invoicemethod[$pricequote["PriceQuoteMethod"]]);
                echo "</span>";
            }
            echo "\t\t\t</td>\n\t\t\t";
            if(!in_array("Debtor", $hide_columns)) {
                echo "<td><a href=\"debtors.php?page=show&id=";
                echo $pricequote["Debtor"];
                echo "\" class=\"a1\">";
                echo $pricequote["CompanyName"] ? $pricequote["CompanyName"] : $pricequote["SurName"] . ", " . $pricequote["Initials"];
                echo "</a></td>";
            }
            echo "\t\t\t<td style=\"width: 5px;\" class=\"currency_sign_left show_col_widescreen_medium\">";
            echo currency_sign_td(CURRENCY_SIGN_LEFT);
            echo "</td>\n\t\t\t<td style=\"width: 60px;\" class=\"show_col_widescreen_medium\" align=\"right\">";
            echo money($pricequote["AmountExcl"], false);
            echo "</td>\n\t\t\t<td style=\"width: 45px;\" class=\"currency_sign_right show_col_widescreen_medium\">";
            echo currency_sign_td(CURRENCY_SIGN_RIGHT);
            echo "</td>\n\t\t\t<td style=\"width: 5px;\" class=\"currency_sign_left\">";
            echo currency_sign_td(CURRENCY_SIGN_LEFT);
            echo "</td>\n\t\t\t<td style=\"width: 60px;\" align=\"right\">";
            echo money($pricequote["AmountIncl"], false);
            echo "</td>\n\t\t\t<td style=\"width: 45px;\" class=\"currency_sign_right\">";
            echo currency_sign_td(CURRENCY_SIGN_RIGHT);
            echo "</td>\n\t\t\t<td>";
            echo rewrite_date_db2site($pricequote["Date"]);
            echo "</td>\n            <td class=\"show_col_widescreen_large nowrap\">\n                ";
            echo substr($pricequote["ReferenceNumber"], 0, 30);
            if(30 < strlen($pricequote["ReferenceNumber"])) {
                echo "...";
            }
            echo "            </td>\n\t\t\t<td>";
            echo $array_pricequotestatus[$pricequote["Status"]];
            echo "</td>\t\n\t\t\t";
            if(!in_array("Debtor", $hide_columns)) {
                echo "<td>&nbsp;<span class=\"ico actionblock tag nm hover_extra_info_span\">";
                echo __("more information");
                echo "</span></td>";
            }
            echo "\t\t</tr>\n\t\t";
            if(!in_array("Debtor", $hide_columns)) {
                echo "\t\t<tr class=\"tr_extra_info mark2\">\n\t\t\t<td>&nbsp;</td>\n\t\t\t<td>\n\t\t\t\t";
                echo $pricequote["Initials"];
                echo " ";
                echo $pricequote["SurName"];
                echo "<br />\n\t\t\t\t";
                echo $pricequote["Address"];
                echo "<br />\n\t\t\t\t";
                echo $pricequote["ZipCode"];
                echo "&nbsp;&nbsp;";
                echo $pricequote["City"];
                echo "<br />\n\t\t\t\t";
                echo $array_country[$pricequote["Country"]];
                echo "\t\t\t</td>\n\t\t\t";
                $EmailAddress = $pricequote["EmailAddress"] != "" ? rtrim(substr(check_email_address($pricequote["EmailAddress"], "convert", ", "), 0, 75), ", ") . (75 < strlen($pricequote["EmailAddress"]) ? "..." : "") : "";
                echo "\t\t\t<td colspan=\"";
                echo $column_count - 4;
                echo "\" class=\"show_col_widescreen_large\">\n\t\t\t\t";
                if($pricequote["PhoneNumber"]) {
                    echo "<span class=\"ei_title\">";
                    echo __("abbr_phonenumber");
                    echo ":</span><span class=\"ei_value\">";
                    echo phoneNumberLink($pricequote["PhoneNumber"]);
                    echo "</span>";
                }
                echo "\t\t\t\t";
                if($pricequote["MobileNumber"]) {
                    echo "<span class=\"ei_title\">";
                    echo __("abbr_mobilenumber");
                    echo ":</span><span class=\"ei_value\">";
                    echo phoneNumberLink($pricequote["MobileNumber"]);
                    echo "</span>";
                }
                echo "\t\t\t\t";
                if($EmailAddress != "") {
                    echo "<span class=\"ei_title\">";
                    echo __("abbr_emailaddress");
                    echo ":</span><span class=\"ei_value\">";
                    echo $EmailAddress;
                    echo "</span>";
                }
                echo "\t\t\t</td>\n\t\t\t<td colspan=\"";
                echo $column_count - 5;
                echo "\" class=\"show_col_widescreen_medium hide_col_widescreen_large\">\n\t\t\t\t";
                if($pricequote["PhoneNumber"]) {
                    echo "<span class=\"ei_title\">";
                    echo __("abbr_phonenumber");
                    echo ":</span><span class=\"ei_value\">";
                    echo phoneNumberLink($pricequote["PhoneNumber"]);
                    echo "</span>";
                }
                echo "\t\t\t\t";
                if($pricequote["MobileNumber"]) {
                    echo "<span class=\"ei_title\">";
                    echo __("abbr_mobilenumber");
                    echo ":</span><span class=\"ei_value\">";
                    echo phoneNumberLink($pricequote["MobileNumber"]);
                    echo "</span>";
                }
                echo "\t\t\t\t";
                if($EmailAddress != "") {
                    echo "<span class=\"ei_title\">";
                    echo __("abbr_emailaddress");
                    echo ":</span><span class=\"ei_value\">";
                    echo $EmailAddress;
                    echo "</span>";
                }
                echo "\t\t\t</td>\n\t\t\t<td colspan=\"";
                echo $column_count - 8;
                echo "\" class=\"show_col_no_widescreen\">\n\t\t\t\t";
                if($pricequote["PhoneNumber"]) {
                    echo "<span class=\"ei_title\">";
                    echo __("abbr_phonenumber");
                    echo ":</span><span class=\"ei_value\">";
                    echo phoneNumberLink($pricequote["PhoneNumber"]);
                    echo "</span>";
                }
                echo "\t\t\t\t";
                if($pricequote["MobileNumber"]) {
                    echo "<span class=\"ei_title\">";
                    echo __("abbr_mobilenumber");
                    echo ":</span><span class=\"ei_value\">";
                    echo phoneNumberLink($pricequote["MobileNumber"]);
                    echo "</span>";
                }
                echo "\t\t\t\t";
                if($EmailAddress != "") {
                    echo "<span class=\"ei_title\">";
                    echo __("abbr_emailaddress");
                    echo ":</span><span class=\"ei_value\">";
                    echo $EmailAddress;
                    echo "</span>";
                }
                echo "\t\t\t</td>\n\t\t\t\t\t\t<td colspan=\"2\" class=\"lineheight1\">\n\t\t\t\t<a class=\"ico inline arrowrightwhite a1\" href=\"pricequotes.php?page=show&action=sent&id=";
                echo $pricequoteID;
                echo "\">";
                echo __("send pricequote");
                echo "</a> <br />\n\t\t\t\t<a class=\"ico inline arrowrightwhite a1\" href=\"pricequotes.php?page=show&action=print&id=";
                echo $pricequoteID;
                echo "\">";
                echo __("print pricequote");
                echo "</a>\n\t\t\t</td>\n\t\t</tr>\n\t\t";
            }
            echo "\t\t";
        }
    }
    if($pricequoteCounter === 0) {
        echo "\t\t<tr>\n\t\t\t<td colspan=\"";
        echo $column_count;
        echo "\">\n\t\t\t\t";
        echo __("no results found");
        echo "\t\t\t</td>\n\t\t</tr>\n\t\t";
    }
    echo "\t\t";
    if(0 < $pricequoteCounter) {
        echo "\t\t<tr class=\"table_options\">\n\t\t\t<td colspan=\"";
        echo $column_count;
        echo "\">\n\t\t\t\t<p class=\"ico inline hook\">\n\t\t\t\t\t<select name=\"action\" class=\"select1 BatchSelect\">\n\t\t\t\t\t\t<option selected=\"selected\">";
        echo __("with selected");
        echo "</option>\n\t\t\t\t\t\t<option value=\"dialog:sent\">";
        echo __("send pricequote");
        echo "</option>\n\t\t\t\t\t\t<option value=\"dialog:print\">";
        echo __("print pricequote");
        echo "</option>\n\t\t\t\t\t\t<option value=\"dialog:accept\">";
        echo __("accept pricequote");
        echo "</option>\n\t\t\t\t\t\t<option value=\"dialog:decline\">";
        echo __("decline pricequote");
        echo "</option>\n\t\t\t\t\t</select>\n\t\t\t\t\t\n\t\t\t\t\t<div class=\"hide\" id=\"dialog_accept\" title=\"";
        echo __("accept pricequote");
        echo "\">\n\t\t\t\t\t\t<strong>";
        echo __("confirm action");
        echo "</strong><br />\n\t\t\t\t\t\t";
        echo __("batchdialog accept pricequote");
        echo "<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong>";
        echo __("batchdialog accept pricequote makeinvoice");
        echo "</strong><br />\n\t\t\t\t\t\t<input type=\"radio\" id=\"createinvoice_yes\" name=\"createinvoice\" value=\"yes\"/> <label for=\"createinvoice_yes\">";
        echo __("dialog accept pricequote yes");
        echo "</label><br />\n\t\t\t\t\t\t<input type=\"radio\" id=\"createinvoice_no\" name=\"createinvoice\" value=\"no\" checked=\"checked\"/> <label for=\"createinvoice_no\">";
        echo __("dialog accept pricequote no");
        echo "</label>\n                        <div class=\"accept_pricequote_options hide\">\n                            <br />\n                            <label><input type=\"checkbox\" name=\"usepricequoteasinvoiceref\" value=\"yes\"/> ";
        echo __("use pricequotecode as reference");
        echo "</label><br />    \n                        </div>\n                    </div>\n\t\t\t\t\t\n\t\t\t\t\t<div class=\"hide\" id=\"dialog_decline\" title=\"";
        echo __("decline pricequote");
        echo "\">\n\t\t\t\t\t\t<strong>";
        echo __("confirm action");
        echo "</strong><br />\n\t\t\t\t\t\t";
        echo __("batchdialog decline pricequote");
        echo "\t\t\t\t\t</div>\n\t\t\t\t\t\n\t\t\t\t\t<div class=\"hide\" id=\"dialog_print\" title=\"";
        echo __("print pricequote");
        echo "\">\n\t\t\t\t\t\t<strong>";
        echo __("dialog template design title");
        echo "</strong><br />\n\t\t\t\t\t\t<input type=\"radio\" id=\"print_printtype_download\" name=\"printtype\" value=\"download\" checked=\"checked\"/> <label for=\"print_printtype_download\">";
        echo __("dialog template design option1");
        echo "</label><br />\n\t\t\t\t\t\t<input type=\"radio\" id=\"print_printtype_print\" name=\"printtype\" value=\"print\"/> <label for=\"print_printtype_print\">";
        echo __("dialog template design option2");
        echo "</label>\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t</div>\n\t\t\t\t\t\n\t\t\t\t\t<div class=\"hide\" id=\"dialog_sent\" title=\"";
        echo __("send pricequote");
        echo "\">\n\t\t\t\t\t\t<strong>";
        echo __("confirm action");
        echo "</strong><br />\n\t\t\t\t\t\t";
        echo __("batchdialog pricequote send");
        echo "\t\t\t\t\t\t";
        if($post_invoices) {
            echo "\t\t\t\t\t\t\t<div id=\"dialog_send_pricequote_print\" class=\"hide\"><br /><br />\n\t\t\t\t\t\t\t\t<strong>";
            echo __("dialog template design title for post method pricequotes");
            echo "</strong><br />\n\t\t\t\t\t\t\t\t<label><input type=\"radio\" name=\"printtype\" value=\"download\" checked=\"checked\"/> ";
            echo __("dialog template design option1");
            echo "</label><br />\n\t\t\t\t\t\t\t\t<label><input type=\"radio\" name=\"printtype\" value=\"print\"/> ";
            echo __("dialog template design option2");
            echo "</label>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t";
        }
        echo "\t\t\t\t\t</div>\n\t\t\t\t</p>\n\t\t\t\t\n\t\t\t\t";
        if(min(MIN_PAGINATION, $session["results"]) < $data_array["CountRows"]) {
            echo "<br />";
            ajax_paginate("PriceQuotes", isset($data_array["CountRows"]) ? $data_array["CountRows"] : 0, $session["results"], $current_page, $current_page_url);
        }
        echo "\t\t\t</td>\n\t\t</tr>\n\t\t";
    }
    echo "\t\t</table>\n\t</div>\n\t\n\t</fieldset></form>\n\t\n\t";
}

?>