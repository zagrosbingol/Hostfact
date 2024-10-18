<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
function show_email_table($data_array, $options = [])
{
    $table_type = isset($options["table_type"]) ? $options["table_type"] : "subtable";
    $session_name = isset($options["session_name"]) ? $options["session_name"] : "";
    $current_page = isset($options["current_page"]) ? $options["current_page"] : "";
    $current_page_url = isset($options["current_page_url"]) ? $options["current_page_url"] : "";
    $redirect_page = isset($options["redirect_page"]) ? $options["redirect_page"] : "";
    $redirect_id = isset($options["redirect_id"]) ? $options["redirect_id"] : "";
    $hide_columns = isset($options["hide_columns"]) ? $options["hide_columns"] : [];
    $column_count = 5 - count($hide_columns);
    $session = $_SESSION[$session_name];
    $selectgroup = isset($options["selectgroup"]) ? $options["selectgroup"] : "0";
    global $array_emailstatus;
    echo "\t<form id=\"EmailForm\" name=\"form_email\" method=\"post\" action=\"emails.php?selectgroup=";
    echo $selectgroup;
    echo "\"><fieldset>\t\n\t\t\t\t\n\t<div id=\"SubTable_Emails\">\t\n\t<table id=\"MainTable_Emails\" class=\"table1\" cellpadding=\"0\" cellspacing=\"0\">\n\t<tr class=\"trtitle\">\n\t\t<th scope=\"col\" style=\"width:180px;\"><label><input name=\"EmailBatch\" class=\"BatchCheck\" type=\"checkbox\" value=\"on\" /></label> <a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','SentDate','Emails','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 pointer ";
    if($session["sort"] == "SentDate") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("date");
    echo "</a></th>\n\t\t<th scope=\"col\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','Subject','Emails','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 pointer ";
    if($session["sort"] == "Subject") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("subject");
    echo "</a></th>\n\t\t<th scope=\"col\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','Recipient','Emails','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 pointer ";
    if($session["sort"] == "Recipient") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("recipient");
    echo "</a></th>\n\t\t<th scope=\"col\" style=\"width:120px;\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','Status','Emails','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 pointer ";
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
    echo "</a></th>\n\t</tr>\n\t";
    $emailCounter = 0;
    foreach ($data_array as $emailID => $email) {
        if(is_numeric($emailID)) {
            $emailCounter++;
            $EmailAddress = $email["Recipient"] != "" ? rtrim(substr(check_email_address($email["Recipient"], "convert", ", "), 0, 75), ", ") . (75 < strlen($email["Recipient"]) ? "..." : "") : "&nbsp;";
            echo "\t<tr class=\"hover_extra_info ";
            if($emailCounter % 2 === 1) {
                echo "tr1";
            }
            echo "\">\n\t\t<td><input name=\"id[]\" type=\"checkbox\" class=\"EmailBatch\" value=\"";
            echo $emailID;
            echo "\" />\n\t\t";
            echo rewrite_date_db2site($email["SentDate"], "%d-%m-%Y " . __("at") . " %H:%i:%s");
            echo "</td>\n\t\t<td>";
            echo $email["Subject"];
            echo "</td>\n\t\t<td>";
            echo $EmailAddress;
            echo "</td>\n\t\t<td>";
            echo $array_emailstatus[$email["Status"]];
            echo "</td>\n\t</tr>\n\t";
        }
    }
    if($emailCounter === 0) {
        echo "\t<tr>\n\t\t<td colspan=\"";
        echo $column_count;
        echo "\">\n\t\t\t";
        echo __("no results found");
        echo "\t\t</td>\n\t</tr>\n\t";
    } else {
        echo "\t\t";
        if(0 < $emailCounter) {
            echo "\t\t<tr class=\"table_options\">\n\t\t\t<td colspan=\"";
            echo $column_count;
            echo "\">\n\t\t\t\t\n\t\t\t\t<p class=\"ico inline hook\">\n\t\t\t\t\t<select name=\"action\" class=\"select1 BatchSelect\">\n\t\t\t\t\t\t<option selected=\"selected\">";
            echo __("with selected");
            echo "</option>\n\t\t\t\t\t\t";
            if($selectgroup != 1) {
                echo "<option value=\"sent\">";
                echo __("send");
                echo "</option>";
            }
            echo "\t\t\t\t\t\t<option value=\"delete\">";
            echo __("delete");
            echo "</option>\n\t\t\t\t\t</select>\n\t\t\t\t</p>\n\t\t\t\t\n\t\t\t\t<br />\n\t\t\t\t\n\t\t\t\t";
            if(min(MIN_PAGINATION, $session["results"]) < $data_array["CountRows"]) {
                echo "\t\n\t\t\t\t\t<br />\n\t\t\t\t\t";
                ajax_paginate("Emails", isset($data_array["CountRows"]) ? $data_array["CountRows"] : 0, $session["results"], $current_page, $current_page_url);
                echo "\t\t\t\t";
            }
            echo "\t\t\t</td>\n\t\t</tr>\n\t\t";
        }
        echo "\t";
    }
    echo "\t</table>\n\t</div>\n\t\n\t</fieldset></form>\n\t";
}

?>