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
echo __("logfile overview");
echo "</h2>\n\t\n\t";
if(isset($_SESSION["logfile.overview"]["status"]) && $_SESSION["logfile.overview"]["status"]) {
    echo "\t\t<p class=\"pos3\"><strong class=\"textsize1\">- ";
    echo __("logtype");
    echo ": ";
    echo __($_SESSION["logfile.overview"]["status"] . " logtype");
    echo "</strong></p>\n\t";
}
echo "\n\t<p class=\"pos2\">\n\t\t<strong class=\"textsize1\">";
echo __("total");
echo ": <span>";
echo isset($list_logfile["CountRows"]) ? $list_logfile["CountRows"] : "0";
echo "</strong>\n\t</p>\n\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n<!--optionsbar-->\n<div class=\"optionsbar\">\n<!--optionsbar-->\n\t\n\t<p class=\"pos2\">\n\t\t<a onclick=\"save('logfile.overview','status','','";
echo $current_page_url;
echo "');\" class=\"sizenormal c1 a1\">";
echo __("show all");
echo "</a> <span> | ";
echo __("logtype");
echo "\t\t<select class=\"select1\" onchange=\"save('logfile.overview','status',this.value, '";
echo $current_page_url;
echo "');\">\n\t\t\t<option value=\"\">";
echo __("please choose");
echo "</option>\n\t\t\t<option value=\"error\"";
if(isset($_SESSION["logfile.overview"]["status"]) && $_SESSION["logfile.overview"]["status"] == "error") {
    echo " selected=\"selected\"";
}
echo ">";
echo __("error logtype");
echo "</option>\n\t\t\t<option value=\"warning\"";
if(isset($_SESSION["logfile.overview"]["status"]) && $_SESSION["logfile.overview"]["status"] == "warning") {
    echo " selected=\"selected\"";
}
echo ">";
echo __("warning logtype");
echo "</option>\n\t\t\t<option value=\"success\"";
if(isset($_SESSION["logfile.overview"]["status"]) && $_SESSION["logfile.overview"]["status"] == "success") {
    echo " selected=\"selected\"";
}
echo ">";
echo __("success logtype");
echo "</option>\n\t\t</select>\n\t</p>\n\n<!--optionsbar-->\n</div>\n<!--optionsbar-->\n\n<form id=\"LogFileForm\" name=\"form_logfile\" method=\"post\" action=\"logfile.php\">\t\n\t\t\t\t\t\n<div id=\"SubTable_LogFile\">\t\n\t<table id=\"MainTable_LogFile\" class=\"table1\" cellpadding=\"0\" cellspacing=\"0\">\n\t<tr class=\"trtitle\">\n\t\t<th scope=\"col\" style=\"width: 180px;\"><label><input name=\"LogFileBatch\" class=\"BatchCheck\" type=\"checkbox\" value=\"on\" /></label> <a onclick=\"ajaxSave('logfile.overview','sort','Date','LogFile','";
echo $current_page_url;
echo "');\" class=\"ico set2 pointer ";
if($_SESSION["logfile.overview"]["sort"] == "Date") {
    if($_SESSION["logfile.overview"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\" style=\"padding-left:18px;\">";
echo __("date");
echo "</a></th>\n\t\t<th scope=\"col\">";
echo __("log message");
echo "</th>\n\t\t<th scope=\"col\" style=\"width: 150px;\"><a onclick=\"ajaxSave('logfile.overview','sort','Who','Logfile','";
echo $current_page_url;
echo "');\" class=\"ico set2 ";
if($_SESSION["logfile.overview"]["sort"] == "Who") {
    if($_SESSION["logfile.overview"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("employee");
echo "</a></th>\n\t</tr>\n\t";
$logCounter = 0;
foreach ($list_logfile as $logID => $log_item) {
    if(is_numeric($logID)) {
        $logCounter++;
        echo "\t<tr class=\"hover_extra_info ";
        if($logCounter % 2 === 1) {
            echo "tr1";
        }
        echo "\" ";
        if($log_item["Type"] == "error") {
            echo "style=\"color:#B92621;\"";
        }
        echo ">\n\t\t<td><input name=\"id[]\" type=\"checkbox\" class=\"LogFileBatch\" value=\"";
        echo $logID;
        echo "\" />\n\t\t\t";
        if($log_item["Type"] == "error") {
            echo "\t\t\t<span class=\"inline_status inline_block error\">&nbsp;</span>\n\t\t\t";
        } elseif($log_item["Type"] == "warning") {
            echo "\t\t\t<span class=\"inline_status inline_block busy\">&nbsp;</span>\n\t\t\t";
        } else {
            echo "\t\t\t<span class=\"inline_status inline_block active\">&nbsp;</span>\n\t\t\t";
        }
        echo " \n\t\t\t";
        echo rewrite_date_db2site($log_item["Date"]) . " " . __("at") . " " . rewrite_date_db2site($log_item["Date"], "%H:%i:%s");
        echo "\t\t\t</td>\n\t\t<td>";
        $log_item["Message"] = __($log_item["Message"], $log_item["ObjectType"]);
        $log_item["Values"] = explode("|", $log_item["Values"]);
        if(strpos($log_item["Message"], "%s") && count($log_item["Values"]) < count(explode("%s", $log_item["Message"])) - 1) {
            echo replace_hyperlink($log_item["Message"]);
        } else {
            echo replace_hyperlink(call_user_func_array("sprintf", array_merge([$log_item["Message"]], $log_item["Values"])));
        }
        echo "</td>\n\t\t<td>";
        echo !in_array($log_item["Name"], ["0", "api", "clientarea", "cronjob"]) ? $log_item["Name"] : __("log line who " . $log_item["Name"]);
        echo "</td>\n\t</tr>\n\t";
    }
}
if($logCounter === 0) {
    echo "\t<tr>\n\t\t<td colspan=\"3\">\n\t\t\t";
    echo __("no results found");
    echo "\t\t</td>\n\t</tr>\n\t";
} else {
    echo "\t";
    if(0 < $logCounter) {
        echo "\t<tr class=\"table_options\">\n\t\t<td colspan=\"3\">\n\t\t\t";
        if(U_LOGFILE_DELETE) {
            echo "\t\t\t<p class=\"ico inline hook\">\n\t\t\t\t<select name=\"action\" class=\"select1 BatchSelect\">\n\t\t\t\t\t<option selected=\"selected\">";
            echo __("with selected");
            echo "</option>\n\t\t\t\t\t<option value=\"deleteSelected\">";
            echo __("delete");
            echo "</option>\n\t\t\t\t\t<option value=\"delete\">";
            echo __("delete till selected");
            echo "</option>\n\t\t\t\t</select>\n\t\t\t</p>\n\t\t\t<br />\n\t\t\t";
        }
        echo "\t\t\n\t\t\t";
        if(min(MIN_PAGINATION, $_SESSION["logfile.overview"]["results"]) < $list_logfile["CountRows"]) {
            echo "<br />";
            ajax_paginate("LogFile", isset($list_logfile["CountRows"]) ? $list_logfile["CountRows"] : 0, $_SESSION["logfile.overview"]["results"], $current_page, $current_page_url);
        }
        echo "\t\t</td>\n\t</tr>\n\t";
    }
}
echo "\t</table>\n</div>\n\n</form>\n\n";
require_once "views/footer.php";

?>