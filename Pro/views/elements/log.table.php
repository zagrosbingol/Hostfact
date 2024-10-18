<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
function show_log_table($data_array, $options = [])
{
    $table_type = isset($options["table_type"]) ? $options["table_type"] : "subtable";
    $session_name = isset($options["session_name"]) ? $options["session_name"] : "";
    $current_page = isset($options["current_page"]) ? $options["current_page"] : "1";
    $current_page_url = isset($options["current_page_url"]) ? $options["current_page_url"] : "";
    $form_action = isset($options["form_action"]) ? $options["form_action"] : "?";
    $allow_delete = isset($options["allow_delete"]) ? $options["allow_delete"] : true;
    $show_icons = isset($options["show_icons"]) ? $options["show_icons"] : false;
    $session = $_SESSION[$session_name];
    global $array_producttypes;
    global $array_country;
    echo "\t\n\t<!--form-->\n\t<form id=\"DomainLogfileForm\" name=\"form_logfile\" method=\"post\" action=\"";
    echo $form_action;
    echo "\"><fieldset>\n\t<!--form-->\n\t\t\n\t\t<div id=\"SubTable_Logfile\">\t\t\n\t\t\t<table id=\"MainTable_Logfile\" class=\"table1\" cellpadding=\"0\" cellspacing=\"0\">\n\t\t\t<tr class=\"trtitle\">\n\t\t\t\t<th scope=\"col\" style=\"width: 180px;\">";
    if($allow_delete) {
        echo "<label><input name=\"LogfileBatch\" class=\"BatchCheck\" type=\"checkbox\" value=\"on\" /></label> ";
    }
    echo "<a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','Date','Logfile','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 pointer ";
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
    echo __("date");
    echo "</a></th>\n\t\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','Action','Logfile','";
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
    echo "\" ";
    if($show_icons) {
        echo "style=\"padding-left:18px;\"";
    }
    echo ">";
    echo __("action");
    echo "</a></td>\n\t\t\t\t<th scope=\"col\" style=\"width: 150px;\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','Who','Logfile','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 ";
    if($session["sort"] == "Who") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("employee");
    echo "</a></th>\n\t\t\t</tr>\n\t\t\t";
    $logCounter = 0;
    foreach ($data_array as $logID => $log_item) {
        if(is_numeric($logID)) {
            $logCounter++;
            echo "\t\t\t\t<tr class=\"hover_extra_info ";
            if($logCounter % 2 === 1) {
                echo "tr1";
            }
            echo "\">\n\t\t\t\t<td>";
            if($allow_delete) {
                echo "<input name=\"logentry[]\" type=\"checkbox\" class=\"LogfileBatch\" value=\"";
                echo $logID;
                echo "\" /> ";
            }
            echo rewrite_date_db2site($log_item["Date"], "%d-%m-%Y") . " " . __("at") . " " . rewrite_date_db2site($log_item["Date"], "%H:%i:%s");
            echo "</td>\n\t\t\t\t<td>";
            if($show_icons) {
                $icon = "";
                switch ($log_item["Action"]) {
                    case "invoice printed":
                    case "invoice sent per post":
                    case "reminder sent per post":
                    case "summation sent per post":
                    case "invoice downloaded via api":
                    case "invoice downloaded via clientarea":
                        $icon = "ico_printsmall.png";
                        break;
                    case "invoice adjusted":
                    case "comment adjusted":
                    case "invoice created":
                    case "invoice created - from credit":
                    case "invoice created - from partly credit":
                        $icon = "ico_edit.png";
                        break;
                    case "invoice sent per email":
                    case "reminder sent per email":
                    case "summation sent per email":
                    case "invoice payment notification":
                        $icon = "ico_sendemail.png";
                        break;
                    case "invoice paid partly":
                    case "invoice paid partly on":
                    case "invoice paid partly via package":
                    case "invoice paid via package":
                    case "log online payment succeeded":
                    case "log online payment failed":
                        $icon = "ico_money.png";
                        break;
                    case "invoice paid":
                        $icon = "ico_success.png";
                        break;
                    case "invoice paid on":
                        $icon = "ico_paid_small.png";
                        break;
                    case "invoice unpaid":
                        $icon = "ico_unpaid.png";
                        break;
                    case "invoice expired":
                    case "invoice partly credited":
                        $icon = "ico_credit_small.png";
                        break;
                    case "invoice created from pricequote x":
                        $icon = "ico_quote2invoice.png";
                        break;
                    case "pricequote printed":
                    case "pricequote sent per post":
                    case "pricequote downloaded via api":
                    case "pricequote downloaded via clientarea":
                        $icon = "ico_printsmall.png";
                        break;
                    case "pricequote adjusted":
                    case "pricequote created":
                        $icon = "ico_edit.png";
                        break;
                    case "pricequote sent per email":
                        $icon = "ico_sendemail.png";
                        break;
                    case "invoice x created":
                        $icon = "ico_quote2invoice.png";
                        break;
                    case "pricequote accepted":
                    case "pricequote accepted from clientarea":
                    case "pricequote accepted online":
                        $icon = "ico_success.png";
                        break;
                    case "pricequote declined":
                    case "pricequote declined from clientarea":
                        $icon = "ico_cancelled.png";
                        break;
                    case "invoicemethod and emailaddress changed":
                    case "invoicemethod changed":
                    case "pricequotemethod and emailaddress changed":
                    case "pricequotemethod changed":
                        $icon = "ico_sendmethodchanged.png";
                        break;
                    case "draft invoice scheduled":
                        $icon = "ico_schedule.png";
                        break;
                    case "draft invoice scheduled undone":
                    case "draft invoice scheduled undone edit":
                        $icon = "ico_unschedule.png";
                        break;
                    default:
                        if(strpos($log_item["Action"], "Exact") !== false) {
                            $icon = "ico_export_exact.png";
                        } elseif(strpos($log_item["Action"], "Twinfield") !== false) {
                            $icon = "ico_export_twinfield.png";
                        }
                        if($icon) {
                            echo "<img src=\"images/";
                            echo $icon;
                            echo "\" style=\"width:12px; height:12px;display:inline-block; margin-right:6px;\" alt=\"\" />";
                        } else {
                            echo "<span style=\"width:16px;display:inline-block;\">&nbsp;</span>";
                        }
                }
            }
            if($log_item["Translate"] == "no") {
                echo $log_item["Action"];
            } else {
                $log_item["Action"] = __("log." . $log_item["Action"]);
                $log_item["Values"] = explode("|", $log_item["Values"]);
                if(strpos($log_item["Action"], "%s") && count($log_item["Values"]) < count(explode("%s", $log_item["Message"])) - 1) {
                    echo $log_item["Action"];
                } else {
                    echo call_user_func_array("sprintf", array_merge([$log_item["Action"]], $log_item["Values"]));
                }
            }
            echo "</a></td>\n\t\t\t\t<td>";
            echo !in_array($log_item["Name"], ["0", "api", "clientarea", "cronjob"]) ? $log_item["Name"] : __("log line who " . $log_item["Name"]);
            echo "</td>\n\t\t\t</tr>\n\t\t\t";
        }
    }
    echo "\t\t\t";
    if($logCounter === 0) {
        echo "\t\t\t<tr>\n\t\t\t\t<td colspan=\"3\">\n\t\t\t\t\t";
        echo __("no results found");
        echo "\t\t\t\t</td>\n\t\t\t</tr>\n\t\t\t";
    }
    echo "\t\t\t";
    if(0 < $logCounter) {
        echo "<tr class=\"table_options\">\n\t\t\t\t<td colspan=\"3\">\n\t\t\t\t\t";
        if($allow_delete) {
            echo "\t\t\t\t\t<p class=\"ico inline hook\">\n\t\t\t\t\t\t<select name=\"action\" class=\"select1 BatchSelect\">\n\t\t\t\t\t\t\t<option value=\"\" selected=\"selected\">";
            echo __("with selected");
            echo "</option>\n\t\t\t\t\t\t\t";
            if($allow_delete) {
                echo "<option value=\"removelogentry\">";
                echo __("remove logelements");
                echo "</option>";
            }
            echo "\t\t\t\t\t\t</select>\n\t\t\t\t\t</p>\n\t\t\t\t\t\n\t\t\t\t\t<br />\n\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\n\t\t\t\t\t";
        if(min(MIN_PAGINATION, $session["results"]) < $data_array["CountRows"]) {
            echo "<br />";
            ajax_paginate("Logfile", isset($data_array["CountRows"]) ? $data_array["CountRows"] : 0, $session["results"], $current_page, $current_page_url);
        }
        echo "\t\t\t\t</td>\n\t\t\t</tr>\n\t\t\t";
    }
    echo "\t\t\t</table>\n\t\t</div>\n\t\t\n\t<!--form-->\n\t</fieldset></form>\n\t<!--form-->\n\t\n\t";
}

?>