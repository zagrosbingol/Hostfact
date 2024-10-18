<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
function show_apilog_table($data_array, $options = [])
{
    $table_type = isset($options["table_type"]) ? $options["table_type"] : "subtable";
    $session_name = isset($options["session_name"]) ? $options["session_name"] : "";
    $current_page = isset($options["current_page"]) ? $options["current_page"] : "1";
    $current_page_url = isset($options["current_page_url"]) ? $options["current_page_url"] : "";
    $SearchString = isset($options["searchfor"]) ? $options["searchfor"] : "";
    $responseType = $options["responseType"];
    $session = $_SESSION[$session_name];
    $statusoptions = $options["status_options"];
    echo "\t\n\t<!--optionsbar-->\n\t<div class=\"optionsbar\" style=\"position:static;margin-top: 9px;\">\n\t<!--optionsbar-->\n\t\t<p class=\"pos1\">\n\t\t\t<input type=\"hidden\" id=\"current_url\" value=\"";
    echo $current_page_url;
    echo "\" />\n\t\t\t<input type=\"text\" name=\"apiLogSearch\" value=\"";
    if(isset($SearchString) && $SearchString) {
        echo $SearchString;
    }
    echo "\" placeholder=\"";
    echo __("apilog search placeholder");
    echo "\" class=\"text1 size1\"/>\n\t\t\t&nbsp;<a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','searchfor','','apiLogfile','";
    echo $current_page_url;
    echo "');\$(this).hide();\$('input[name=apiLogSearch]').val('');\" class=\"apilogsearchlink ";
    if(!(isset($SearchString) && $SearchString)) {
        echo "hide";
    }
    echo " sizenormal c1 a1 pointer\">";
    echo __("apilog delete search");
    echo "</a>\n\t\t</p>\n\t\t\n\t\t<p class=\"pos2\"  style=\"position:static;\">\n\t\t\t";
    if(1 < count($statusoptions)) {
        echo "\t\t\t\t<a onclick=\"ajaxSave('";
        echo $session_name;
        echo "','responseType','false','apiLogfile','";
        echo $current_page_url;
        echo "');\$('#selectResponseType').val('false')\"  class=\"sizenormal c1 a1 pointer\">";
        echo __("all");
        echo "</a> <span class=\"c_gray\"> | </span>\n\t\t\t\t";
        echo __("status");
        echo "&nbsp;\n\t\t\t\t\n\t\t\t\t<select class=\"select1\" id=\"selectResponseType\" onchange=\"ajaxSave('";
        echo $session_name;
        echo "','responseType',\$(this).val(),'apiLogfile','";
        echo $current_page_url;
        echo "');\">\n\t\t\t\t\t<option value=\"false\" ";
        echo $responseType == "false" ? "selected" : "";
        echo ">";
        echo __("please choose");
        echo "</option>\n\t\t\t\t\t";
        if(in_array("success", $statusoptions)) {
            echo "<option value=\"success\" ";
            echo $responseType == "success" ? "selected" : "";
            echo ">";
            echo __("api log responsetype-success");
            echo "</option>";
        }
        echo "\t\t\t\t\t";
        if(in_array("error", $statusoptions)) {
            echo "<option value=\"error\" ";
            echo $responseType == "error" ? "selected" : "";
            echo ">";
            echo __("api log responsetype-error");
            echo "</option>";
        }
        echo "\t\t\t\t</select>\n\t\t\t";
    }
    echo "\t\t</p>\n\t\t\n\t<!--optionsbar-->\n\t</div>\n\t<!--optionsbar-->\n\t\n\t<div id=\"SubTable_apiLogfile\">\t\t\n\t\t<table id=\"MainTable_apiLogfile\" class=\"table1\" cellpadding=\"0\" cellspacing=\"0\">\n\t\t<tr class=\"trtitle\">\n\t\t\t<th scope=\"col\" style=\"width: 170px;\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','DateTime','apiLogfile','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 pointer ";
    if($session["sort"] == "DateTime") {
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
    echo "</a></th>\n\t\t\t<th scope=\"col\" style=\"width: 100px;\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','Controller','apiLogfile','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 ";
    if($session["sort"] == "Controller") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("apiController");
    echo "</a></th>\n\t\t\t<th scope=\"col\" style=\"width: 180px;\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','Action','apiLogfile','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 ";
    if($session["sort"] == "Action") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("apiAction");
    echo "</a></th>\n\t\t\t<th scope=\"col\" style=\"width: 70px;\">";
    echo __("apiStatus");
    echo "</th>\n\t\t\t<th scope=\"col\" style=\"width: 180px;\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','ResponseType','apiLogfile','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 ";
    if($session["sort"] == "ResponseType") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("apiResponse");
    echo "</a></th>\n\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','IP','apiLogfile','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 ";
    if($session["sort"] == "IP") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("ip address");
    echo "</a></th>\n\t\t</tr>\n\t\t";
    $logCounter = 0;
    if(!is_array($data_array)) {
        $data_array = [];
    }
    foreach ($data_array as $logID => $log_item) {
        if(is_numeric($logID)) {
            $logCounter++;
            echo "\t\t<tr class=\"hover_extra_info ";
            if($logCounter % 2 === 1) {
                echo "tr1";
            }
            echo "\">\n\t\t\t<td>";
            echo rewrite_date_db2site($log_item["DateTime"]) . " " . __("at") . " " . rewrite_date_db2site($log_item["DateTime"], "%H:%i:%s");
            echo "</td>\n\t\t\t<td>";
            echo $log_item["Controller"];
            echo "</td>\n\t\t\t<td>";
            echo $log_item["Action"];
            echo "</td>\n\t\t\t<td>";
            echo __("api log responsetype-" . $log_item["ResponseType"]);
            echo "</td>\n\t\t\t<td>";
            echo "<span class=\"api_logfile c1 a1\"><input type=\"hidden\" class=\"logrow\" value=\"" . $log_item["id"] . "\" />" . __("show in- output") . "</span>";
            echo "</td>\n\t\t\t<td>";
            echo $log_item["IP"];
            echo "</td>\n\t\t</tr>\n\t\t";
        }
    }
    echo "\t\t";
    if($logCounter === 0) {
        echo "\t\t<tr>\n\t\t\t<td colspan=\"6\">\n\t\t\t\t";
        echo __("no results found");
        echo "\t\t\t</td>\n\t\t</tr>\n\t\t";
    } elseif(min(MIN_PAGINATION, $session["results"]) < $data_array["CountRows"]) {
        echo "\t\t<tr class=\"table_options\">\n\t\t\t<td colspan=\"6\">\n\t\t\t\t<br />";
        ajax_paginate("apiLogfile", isset($data_array["CountRows"]) ? $data_array["CountRows"] : 0, $session["results"], $current_page, $current_page_url);
        echo " \n\t\t\t</td>\n\t\t</tr>\n\t\t";
    }
    echo "\t\t</table>\n\t</div>\n\t\n\t";
}

?>