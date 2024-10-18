<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "<div id=\"tld_search\" class=\"hide\" title=\"";
echo __("dialog search tld title");
echo "\">\n\t";
echo __("dialog search tld explain");
echo "<br /><br />\n\t<input type=\"text\" name=\"SearchTld\" value=\"\" class=\"text1 size1\" style=\"float: left; margin-top: 2px;\" onkeypress=\"if(event.keyCode == 13){ ajaxSave('search.tld','searchfor',\$('input[name=SearchTld]').val(),'TldSearch','";
echo $current_page_url;
echo "'); }\"/> <p style=\"margin-left: 15px; float: left;\"><a class=\"button1 alt1 float_left\"  onclick=\"ajaxSave('search.tld','searchfor',\$('input[name=SearchTld]').val(),'TldSearch','";
echo $current_page_url;
echo "');\"  ><span>";
echo __("search");
echo "</span></a></p>\n\t<br clear=\"both\" /><br />\n\n\t<div id=\"SubTable_TldSearch\">\n\n\t\t<table id=\"MainTable_TldSearch\" class=\"table1\" cellpadding=\"0\" cellspacing=\"0\">\n\t\t<tr class=\"trtitle\">\n\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('search.tld','sort','Tld','TldSearch','";
echo $current_page_url;
echo "');\" class=\"ico set2 pointer ";
if($_SESSION["search.tld"]["sort"] == "Tld") {
    if($_SESSION["search.tld"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("topleveldomain");
echo "</a></th>\n\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('search.tld','sort','Name','TldSearch','";
echo $current_page_url;
echo "');\" class=\"ico set2 pointer ";
if($_SESSION["search.tld"]["sort"] == "Name") {
    if($_SESSION["search.tld"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("registrar");
echo "</a></td>\n\t\t</tr>\n\t\t";
$tldCounter = 0;
foreach ($tlds as $tldID => $tld) {
    if(is_numeric($tldID)) {
        $tldCounter++;
        echo "\t\t<tr class=\"hover_extra_info ";
        if($tldCounter % 2 === 1) {
            echo "tr1";
        }
        echo " dialog_select_hover\">\n\t\t\t<td>";
        echo $tld["Tld"];
        echo "</td>\n\t\t\t<td>";
        echo $tld["Registrar"] ? $tld["Name"] : "-";
        echo "</td>\n\t\t</tr>\n\t\t";
    }
}
if($tldCounter === 0) {
    echo "\t\t<tr>\n\t\t\t<td colspan=\"2\">\n\t\t\t\t";
    echo __("no results found");
    echo "\t\t\t</td>\n\t\t</tr>\n\t\t";
} else {
    echo "\t\t<tr>\n\t\t\t<td colspan=\"2\">\n\t\t\t\t";
    ajax_paginate("TldSearch", isset($tlds["CountRows"]) ? $tlds["CountRows"] : 0, $results_per_page, $current_page, $current_page_url, false);
    echo "\t\t\t</td>\n\t\t</tr>\n\t\t";
}
echo "\t\t</table>\n\t</div>\n</div>";

?>