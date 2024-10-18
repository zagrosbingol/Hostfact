<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "<div id=\"handle_search\" class=\"hide\"  title=\"";
echo __("dialog search handle title");
echo "\">\n\t";
echo __("dialog search handle explain");
echo "<br /><br />\n\t<input type=\"text\" name=\"SearchHandle\" value=\"\" class=\"text1 size1\" style=\"float: left; margin-top: 2px;\" onkeypress=\"if(event.keyCode == 13){ ajaxSave('search.handle','searchfor',\$('input[name=SearchHandle]').val(),'HandleSearch','";
echo $current_page_url;
echo "'); }\"/> <p style=\"margin-left: 15px; float: left;\"><a class=\"button1 alt1 float_left\"  onclick=\"ajaxSave('search.handle','searchfor',\$('input[name=SearchHandle]').val(),'HandleSearch','";
echo $current_page_url;
echo "');\"  ><span>";
echo __("search");
echo "</span></a></p>\n\t<br clear=\"both\" />\n\n\t<div id=\"SubTable_HandleSearch\">\n\t\n\t<table id=\"MainTable_HandleSearch\" class=\"table1\" cellpadding=\"0\" cellspacing=\"0\">\n\t<tr class=\"trtitle\">\n\t\t<th scope=\"col\"><a onclick=\"ajaxSave('search.handle','sort','Handle','HandleSearch','";
echo $current_page_url;
echo "');\" class=\"ico set2 pointer ";
if($_SESSION["search.handle"]["sort"] == "Handle") {
    if($_SESSION["search.handle"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("handle");
echo "</a></th>\n\t\t<th scope=\"col\"><a onclick=\"ajaxSave('search.handle','sort','Name','HandleSearch','";
echo $current_page_url;
echo "');\" class=\"ico set2 pointer ";
if($_SESSION["search.handle"]["sort"] == "Name") {
    if($_SESSION["search.handle"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("registrar");
echo "</a></th>\n\t\t<th scope=\"col\"><a onclick=\"ajaxSave('search.handle','sort','CompanyName','HandleSearch','";
echo $current_page_url;
echo "');\" class=\"ico set2 pointer ";
if($_SESSION["search.handle"]["sort"] == "CompanyName") {
    if($_SESSION["search.handle"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("companyname");
echo "</a></td>\n\t\t<th scope=\"col\"><a onclick=\"ajaxSave('search.handle','sort','SurName','HandleSearch','";
echo $current_page_url;
echo "');\" class=\"ico set2 pointer ";
if($_SESSION["search.handle"]["sort"] == "SurName") {
    if($_SESSION["search.handle"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("contact person");
echo "</a></th>\n\t</tr>\n\t";
$handleCounter = 0;
foreach ($list_handles as $handleID => $handle) {
    if(is_numeric($handleID)) {
        $handleCounter++;
        echo "\t<tr id=\"tr_";
        echo $handle["id"];
        echo "\" class=\"hover_extra_info ";
        if($handleCounter % 2 === 1) {
            echo "tr1";
        }
        echo " dialog_select_hover\">\n\t\t<td>";
        echo $handle["Handle"];
        echo "</td>\n\t\t<td>";
        if($handle["Registrar"]) {
            echo $handle["Name"];
            if($handle["RegistrarHandle"]) {
                echo " (" . $handle["RegistrarHandle"] . ")";
            }
        } else {
            echo "-";
        }
        echo "</td>\n\t\t<td>";
        echo $handle["CompanyName"] ? $handle["CompanyName"] : "-";
        echo "</td>\n\t\t<td>";
        echo $handle["SurName"] . ", " . $handle["Initials"];
        echo "</td>\n\t</tr>\n\t";
    }
}
if($handleCounter === 0) {
    echo "\t<tr>\n\t\t<td colspan=\"4\">\n\t\t\t";
    echo __("no results found");
    echo "\t\t</td>\n\t</tr>\n\t";
} else {
    echo "\t<tr>\n\t\t<td colspan=\"4\">\n\t\t\t";
    ajax_paginate("HandleSearch", isset($list_handles["CountRows"]) ? $list_handles["CountRows"] : 0, $results_per_page, $current_page, $current_page_url, false);
    echo "\t\t</td>\n\t</tr>\n\t";
}
echo "\t</table>\n\t</div>\n</div>";

?>