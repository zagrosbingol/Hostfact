<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "<div id=\"domain_search\" class=\"hide\"  title=\"";
echo __("dialog search domain title");
echo "\">\n\t\n\t";
if(empty($searchfor)) {
    echo __("dialog search domain select debtor first");
} else {
    echo "\t\t";
    echo __("dialog search domain explain");
    echo "<br />\n\t\n\t\t<div id=\"SubTable_DomainSearch\">\n\t\t\n\t\t\t<table id=\"MainTable_DomainSearch\" class=\"table1\" cellpadding=\"0\" cellspacing=\"0\">\n\t\t\t<tr class=\"trtitle\">\n\t\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('search.domain','sort','Domain','DomainSearch','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 pointer ";
    if($_SESSION["search.domain"]["sort"] == "Domain") {
        if($_SESSION["search.domain"]["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("domain");
    echo "</a></th>\n\t\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('search.domain','sort','Status','DomainSearch','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 pointer ";
    if($_SESSION["search.domain"]["sort"] == "Status") {
        if($_SESSION["search.domain"]["order"] == "ASC") {
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
    $domainCounter = 0;
    foreach ($domains as $domainID => $domain) {
        if(is_numeric($domainID)) {
            $domainCounter++;
            echo "\t\t\t<tr class=\"hover_extra_info ";
            if($domainCounter % 2 === 1) {
                echo "tr1";
            }
            echo " dialog_select_hover\">\n\t\t\t\t<td>";
            echo $domain["Domain"] . "." . $domain["Tld"];
            echo "</td>\n\t\t\t\t<td>";
            echo $array_domainstatus[$domain["Status"]];
            echo "</td>\n\t\t\t</tr>\n\t\t\t";
        }
    }
    if($domainCounter === 0) {
        echo "\t\t\t<tr>\n\t\t\t\t<td colspan=\"2\">\n\t\t\t\t\t";
        echo __("no results found");
        echo "\t\t\t\t</td>\n\t\t\t</tr>\n\t\t\t";
    } else {
        echo "\t\t\t<tr>\n\t\t\t\t<td colspan=\"2\">\n\t\t\t\t\t";
        ajax_paginate("DomainSearch", isset($domains["CountRows"]) ? $domains["CountRows"] : 0, $results_per_page, $current_page, $current_page_url, false);
        echo "\t\t\t\t</td>\n\t\t\t</tr>\n\t\t\t";
    }
    echo "\t\t\t</table>\n\t\t</div>\n\t\t";
}
echo "</div>";

?>