<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
function show_directdebit_table($data_array, $options = [])
{
    $title = isset($options["title"]) ? $options["title"] : "";
    $table_type = isset($options["table_type"]) ? $options["table_type"] : "current";
    $session_name = isset($options["session_name"]) ? $options["session_name"] : "";
    $table_name = isset($options["table_name"]) ? $options["table_name"] : "SDD_archive";
    $current_page = isset($options["current_page"]) ? $options["current_page"] : "1";
    $current_page_url = isset($options["current_page_url"]) ? $options["current_page_url"] : "";
    $total_archived_batches = isset($options["total_archived_batches"]) ? $options["total_archived_batches"] : 0;
    $session = $_SESSION[$session_name];
    if($title) {
        echo "\t\t<!--heading1-->\n\t\t<div class=\"heading1\">\n\t\t<!--heading1-->\n\t\t\n\t\t\t<h2>\n            ";
        echo $title;
        echo 0 < $total_archived_batches ? " (" . $total_archived_batches . ")" : "";
        echo "            </h2>\n\t\t\n\t\t<!--heading1-->\n\t\t</div><hr />\n\t\t<!--heading1-->\n\t\t";
    }
    echo "\t\n\t<div id=\"SubTable_SDD_";
    echo $table_type;
    echo "\">\n\t\t<table id=\"MainTable_SDD_";
    echo $table_type;
    echo "\" class=\"table1\" cellpadding=\"0\" cellspacing=\"0\">\n\t\t<tr class=\"trtitle\">\n\t\t\t<th style=\"width: 180px;\">";
    echo __("sdd batch id");
    echo "</th>\n\t\t\t<th style=\"width: 120px;\">";
    echo __("sdd direct debit date");
    echo "</th>\n\t\t\t<th style=\"width: 100px;\" align=\"center\">";
    echo __("sdd batch count");
    echo "</th>\n\t\t\t<th colspan=\"3\">";
    echo __("sdd batch total amount");
    echo "</th>\n\t\t\t<th style=\"width: 160px;\">";
    echo __("sdd download date");
    echo "</th>\n\t\t\t<th>";
    echo __("status");
    echo "</th>\n\t\t</tr>\n\t\t";
    $batchCounter = 0;
    foreach ($data_array as $batch_info) {
        $batchCounter++;
        $status_class = "deleted";
        $status_popup = "";
        $status_text = __("sdd batch status " . $batch_info["Status"]);
        $download_date = rewrite_date_db2site($batch_info["DownloadDate"]);
        if($batch_info["Status"] == "draft") {
            $status_class = "deleted";
            $status_text = "Concept";
            $download_date = sprintf(__("sdd download before date"), rewrite_date_db2site($batch_info["DownloadDate"]));
        } elseif($batch_info["Status"] == "downloadable") {
            $status_class = "busy";
            $status_popup = sprintf(__("sdd download batch before date popup"), rewrite_date_db2site($batch_info["DownloadDate"]));
            $status_text = "<a href=\"?page=download&id=" . $batch_info["BatchID"] . "\" class=\"a1 c1\">" . __("sdd download batch") . "</a>";
            $download_date = "<font class=\"c3\">" . sprintf(__("sdd download before date"), rewrite_date_db2site($batch_info["DownloadDate"])) . "</font>";
        }
        if(isset($batch_info["ErrCount"]) && 0 < $batch_info["ErrCount"]) {
            $status_class = "error";
            $status_popup = __("sdd batch contains one or more errors");
        }
        echo "\t\t\t<tr class=\"hover_extra_info ";
        if($batchCounter % 2 == 1) {
            echo "tr1";
        }
        echo "\">\n\t\t\t\t<td>\n\t\t\t\t<span class=\"inline_status ";
        echo $status_class;
        echo " infopopuptop delaypopup\">&nbsp;";
        if($status_popup) {
            echo "<span class=\"popup\">";
            echo $status_popup;
            echo "<b></b></span>";
        }
        echo "</span>\n\t\t\t\t<a href=\"?page=show&amp;id=";
        echo $batch_info["BatchID"];
        echo "\" class=\"a1 c1\">";
        echo $batch_info["BatchID"];
        echo "</a>\n\t\t\t\t";
        if(isset($batch_info["ErrCount"]) && 0 < $batch_info["ErrCount"]) {
            echo "<span class=\"fontsmall c3\">- ";
            echo $batch_info["ErrCount"] == 1 ? __("sdd error 1 error") : sprintf(__("sdd error x errors"), $batch_info["ErrCount"]);
            echo "</span>";
        }
        echo "\t\t\t\t</td>\n\t\t\t\t<td>";
        echo rewrite_date_db2site($batch_info["Date"]);
        echo "</td>\n\t\t\t\t<td align=\"center\">";
        echo $batch_info["Count"];
        echo "</td>\n\t\t\t\t<td style=\"width: 5px;\" class=\"currency_sign_left\">";
        echo currency_sign_td(CURRENCY_SIGN_LEFT);
        echo "</td>\n\t\t\t\t<td style=\"width: 60px;\" align=\"right\">";
        echo money($batch_info["Amount"], false);
        echo "</td>\n\t\t\t\t<td style=\"width: 45px;\" class=\"currency_sign_right\">";
        echo currency_sign_td(CURRENCY_SIGN_RIGHT);
        echo "</td>\n\t\t\t\t<td>";
        echo $download_date;
        echo "</td>\n\t\t\t\t<td>";
        echo $status_text;
        echo "</td>\n\t\t\t</tr>\n\t\t\t";
    }
    if(empty($data_array)) {
        echo "\t\t\t<tr><td colspan=\"8\">";
        echo __("no results found");
        echo "</td></tr>\n\t\t\t";
    }
    if($table_type == "archive" && 0 <= $current_page && min(MIN_PAGINATION, $session["results"]) < $total_archived_batches) {
        echo "                <tr class=\"table_options\">\n                    <td colspan=\"8\">\n    \t\t\t         <br />\n    \t\t\t         ";
        ajax_paginate($table_name, $total_archived_batches, $session["results"], $current_page, $current_page_url);
        echo "                    </td>\n                </tr> \n                ";
    }
    echo "                \n\t\t</table>\n        \n\t</div>\n\n\t<br /><br /><br />\n\n";
}

?>