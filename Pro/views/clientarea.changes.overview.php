<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo $message;
echo "\n<div id=\"show_table_clientarea_changes_open_div\">\n\t<div class=\"heading1\">\n\n\t\t<h2>";
echo __("open clientarea changes which need approval");
echo "</h2>\n\n\t\t<p class=\"pos2\">\n\t\t\t<strong class=\"textsize1\">\n\t\t\t\t";
echo __("total");
echo ": <span id=\"page_total_placeholder_open_changes\" class=\"hide\">0</span>\n\t\t\t</strong>\n\t\t</p>\n\n\t</div>\n\t<hr />\n\n\t";
$table_config_open["parameters"]["filters"]["approval"] = "pending";
$table_config_open["filter"] = "pending";
$table_config_open["page_total_placeholder"] = "page_total_placeholder_open_changes";
$table_config_open["hide_table_if_no_results"] = "clientarea_changes_open";
generate_table("list_clientarea_changes_open", $table_config_open);
echo "\t<br /><br />\n</div>\n\n<div class=\"heading1\">\n\n\t<h2>";
echo __("processed clientarea changes");
echo "</h2>\n\n\t";
if(isset($table_config_processed["filter"]) && $table_config_processed["filter"] != "") {
    echo "\t\t\t<p class=\"pos3\">\n\t\t\t\t<strong class=\"textsize1\">-\n\t\t\t\t\t";
    if(isset($status_array[$table_config_processed["filter"]])) {
        echo __("status") . ":" . $status_array[$table_config_processed["filter"]];
    } elseif($table_config_processed["filter"] == "pending|error") {
        echo __("clientarea changes to be processed");
    }
    echo "\t\t\t\t</strong>\n\t\t\t</p>\n\t\t";
}
echo "\n\t<p class=\"pos2\">\n\t\t<strong class=\"textsize1\">\n\t\t\t";
echo __("total");
echo ": <span id=\"page_total_placeholder_processed_changes\" class=\"hide\">0</span>\n\t\t</strong>\n\t</p>\n\n</div>\n\n";
$table_config_processed["parameters"]["filters"]["approval"] = "notused|approved|rejected";
$table_config_processed["page_total_placeholder"] = "page_total_placeholder_processed_changes";
echo "\n<div class=\"optionsbar\">\n\t<p class=\"pos2\">\n\t\t<a onclick=\"save('backoffice_table.list_clientarea_changes_processed','filter', 'pending|error', '";
echo $current_page_url;
echo "');\" class=\"sizenormal c1 a1 pointer\">\n\t\t\t";
echo __("clientarea changes to be processed");
echo "\t\t</a>\n\t\t<span class=\"c_gray\"> | </span>\n\t\t<a onclick=\"save('backoffice_table.list_clientarea_changes_processed','filter','', '";
echo $current_page_url;
echo "');\" class=\"sizenormal c1 a1 pointer\">\n\t\t\t";
echo __("all");
echo "\t\t</a>\n\t\t<span class=\"c_gray\"> | </span> ";
echo __("status");
echo "\t\t<select class=\"select1\" onchange=\"save('backoffice_table.list_clientarea_changes_processed','filter',this.value, '";
echo $current_page_url;
echo "');\">\n\t\t\t<option value=\"\">";
echo __("please choose");
echo "</option>\n\t\t\t";
$active_filter = $table_config_processed["filter"];
foreach ($status_array as $status_key => $status_name) {
    if($status_key != "removed") {
        echo "\t\t\t\t\t<option value=\"";
        echo $status_key;
        echo "\"";
        if(isset($active_filter) && $active_filter == $status_key) {
            echo " selected=\"selected\"";
        }
        echo ">\n\t\t\t\t\t\t";
        echo $status_name;
        echo "\t\t\t\t\t</option>\n\t\t\t\t\t";
    }
}
echo "\t\t</select>\n\t</p>\n\n</div>\n\n";
generate_table("list_clientarea_changes_processed", $table_config_processed);

?>