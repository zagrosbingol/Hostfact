<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "<div id=\"hosting_search\" class=\"hide\"  title=\"";
echo __("dialog search hosting title");
echo "\">\n\t\n\t";
if(empty($searchfor)) {
    echo __("dialog search hosting select debtor first");
} else {
    echo "\t\t";
    echo __("dialog search hosting explain");
    echo "<br />\n\t\n\t\t<div id=\"SubTable_HostingSearch\">\n\t\t\n\t\t\t<table id=\"MainTable_HostingSearch\" class=\"table1\" cellpadding=\"0\" cellspacing=\"0\">\n\t\t\t<tr class=\"trtitle\">\n\t\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('search.hosting','sort','Username','HostingSearch','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 pointer ";
    if($_SESSION["search.hosting"]["sort"] == "Username") {
        if($_SESSION["search.hosting"]["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("username");
    echo "</a></th>\n\t\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('search.hosting','sort','Domain','HostingSearch','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 pointer ";
    if($_SESSION["search.hosting"]["sort"] == "Domain") {
        if($_SESSION["search.hosting"]["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("domain");
    echo "</a></th>\n\t\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('search.hosting','sort','Status','HostingSearch','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 pointer ";
    if($_SESSION["search.hosting"]["sort"] == "Status") {
        if($_SESSION["search.hosting"]["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("status");
    echo "</a></th>\n\t\t\t</tr>\n\t\t\t";
    $hostingCounter = 0;
    foreach ($hosting_list as $hostingID => $hosting) {
        if(is_numeric($hostingID)) {
            $hostingCounter++;
            echo "\t\t\t<tr id=\"hostingID_";
            echo $hostingID;
            echo "\" class=\"hover_extra_info ";
            if($hostingCounter % 2 === 1) {
                echo "tr1";
            }
            echo " dialog_select_hover\">\n\t\t\t\t<td>";
            echo $hosting["Username"];
            echo "</td>\n\t\t\t\t<td>";
            echo $hosting["Domain"];
            echo "</td>\n\t\t\t\t<td>";
            echo $array_hostingstatus[$hosting["Status"]];
            echo "</td>\n\t\t\t</tr>\n\t\t\t";
        }
    }
    if($hostingCounter === 0) {
        echo "\t\t\t<tr>\n\t\t\t\t<td colspan=\"3\">\n\t\t\t\t\t";
        echo __("no results found");
        echo "\t\t\t\t</td>\n\t\t\t</tr>\n\t\t\t";
    } else {
        echo "\t\t\t<tr>\n\t\t\t\t<td colspan=\"3\">\n\t\t\t\t\t";
        ajax_paginate("HostingSearch", isset($hosting_list["CountRows"]) ? $hosting_list["CountRows"] : 0, $results_per_page, $current_page, $current_page_url, false);
        echo "\t\t\t\t</td>\n\t\t\t</tr>\n\t\t\t";
    }
    echo "\t\t\t</table>\n\t\t</div>\n\t\t";
}
echo "</div>";

?>