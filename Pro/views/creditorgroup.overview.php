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
echo $message;
echo "\n<!--heading1-->\n<div class=\"heading1\">\n<!--heading1-->\n\n\t<h2>";
echo __("creditorgroup overview");
echo "</h2>\n\n\t<p class=\"pos2\"><strong class=\"textsize1\">";
echo __("total");
echo ": <span>";
echo isset($groups["CountRows"]) ? $groups["CountRows"] : "0";
echo "</span></strong></p>\n\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n";
if(U_CREDITOR_EDIT) {
    echo "<!--optionsbar-->\n<div class=\"optionsbar\">\n<!--optionsbar-->\n\t\n\t<p class=\"pos1\"><a class=\"button1 add_icon\" href=\"creditors.php?page=add_group\"><span>";
    echo __("new creditorgroup");
    echo "</span></a></p>\n\n<!--optionsbar-->\n</div>\n<!--optionsbar-->\n";
}
echo "\t\t\t\t\t\n<table id=\"MainTable\" class=\"table1\" cellpadding=\"0\" cellspacing=\"0\">\n\t<tr class=\"trtitle\">\n\t\t<th scope=\"col\"><a onclick=\"save('creditorgroup.overview','sort','GroupName','";
echo $current_page_url;
echo "');\" class=\"ico set2 ";
if($_SESSION["creditorgroup.overview"]["sort"] == "GroupName") {
    if($_SESSION["creditorgroup.overview"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("creditorgroup");
echo "</a></th>\n\t\t<th scope=\"col\" class=\"align_right\" style=\"width: 125px;\">";
echo __("number of creditors");
echo "</td>\n\t\t<th scope=\"col\" style=\"width: 15px;\">&nbsp;</td>\n\t</tr>\n\t";
$groupCounter = 0;
foreach ($groups as $groupID => $group) {
    if(is_numeric($groupID)) {
        $groupCounter++;
        echo "\t<tr class=\"hover_extra_info ";
        if($groupCounter % 2 == 1) {
            echo "tr1";
        }
        echo "\">\n\t\t<td><a href=\"creditors.php?page=show_group&amp;id=";
        echo $groupID;
        echo "\" class=\"c1 a1\">";
        echo $group["GroupName"];
        echo "</a></td>\n\t\t<td>";
        echo count($group["Creditors"]);
        echo "</td>\n\t\t<td>&nbsp;</td>\n\t</tr>\n\t";
    }
}
if($groupCounter === 0) {
    echo "\t<tr>\n\t\t<td colspan=\"3\">\n\t\t\t";
    echo __("no creditorgroups found");
    echo "\t\t</td>\n\t</tr>\n\t";
}
echo "</table>\n\n";
require_once "views/footer.php";

?>