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
echo __("debtorgroup");
echo " ";
echo $group->GroupName;
echo " </h2>\n\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n<!--box1-->\n<div class=\"box2\" id=\"tabs\">\n<!--box1-->\n\n\t<!--top-->\n\t<div class=\"top\">\n\t<!--top-->\n\t\n\t\t<ul class=\"list3\">\n\t\t\t<li class=\"on\"><a href=\"#tab-general\">";
echo __("general");
echo "</a></li>\n\t\t</ul>\t\t\t\n\t\t\n\t<!--top-->\n\t</div>\n\t<!--top-->\n\n\t<!--content-->\n\t<div class=\"content\" id=\"tab-general\">\n\t<!--content-->\n\n\t\t<p>\n\t\t\t<strong>";
echo __("the following debtors are in the debtorgroup");
echo "</strong>\n\t\t</p>\n\n\t\t<div id=\"SubTable_Debtors\">\t\t\t\t\t\t\t\t\n\t\t<table id=\"MainTable_Debtors\" class=\"table1\" cellpadding=\"0\" cellspacing=\"0\">\n\t\t\t<tr class=\"trtitle\">\n\t\t\t\t<th scope=\"col\" style=\"width: 125px;\"><a onclick=\"ajaxSave('debtorgroup.show','sort','DebtorCode','Debtors','";
echo $current_page_url;
echo "');\" class=\"ico set2 pointer ";
if($_SESSION["debtorgroup.show"]["sort"] == "DebtorCode") {
    if($_SESSION["debtorgroup.show"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("debtor no");
echo "</a></th>\n\t\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('debtorgroup.show','sort','Debtor','Debtors','";
echo $current_page_url;
echo "');\" class=\"ico set2 pointer ";
if($_SESSION["debtorgroup.show"]["sort"] == "Debtor") {
    if($_SESSION["debtorgroup.show"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("name");
echo "</a></th>\n\t\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('debtorgroup.show','sort','Address','Debtors','";
echo $current_page_url;
echo "');\" class=\"ico set2 pointer ";
if($_SESSION["debtorgroup.show"]["sort"] == "Address") {
    if($_SESSION["debtorgroup.show"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("address");
echo "</a></th>\n\t\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('debtorgroup.show','sort','EmailAddress','Debtors','";
echo $current_page_url;
echo "');\" class=\"ico set2 pointer ";
if($_SESSION["debtorgroup.show"]["sort"] == "EmailAddress") {
    if($_SESSION["debtorgroup.show"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("emailaddress");
echo "</a></th>\n\t\t\t</tr>\n\t\t\t";
$debtorCounter = 0;
foreach ($debtors as $debtorID => $debtor) {
    if(is_numeric($debtorID)) {
        $debtorCounter++;
        $EmailAddress = $debtor["EmailAddress"] != "" ? rtrim(substr(check_email_address($debtor["EmailAddress"], "convert", ", "), 0, 75), ", ") . (75 < strlen($debtor["EmailAddress"]) ? "..." : "") : "&nbsp;";
        echo "\t\t\t\t\t<tr";
        if($debtorCounter % 2 === 1) {
            echo " class=\"tr1\"";
        }
        echo ">\n\t\t\t\t\t\t<td><a href=\"debtors.php?page=show&amp;id=";
        echo $debtorID;
        echo "\" class=\"c1 a1\">";
        echo $debtor["DebtorCode"];
        echo "</a></td>\n\t\t\t\t\t\t<td>";
        echo $debtor["CompanyName"] ? $debtor["CompanyName"] : $debtor["SurName"] . ", " . $debtor["Initials"];
        echo "</td>\n\t\t\t\t\t\t<td>";
        echo $debtor["Address"] . ", " . $debtor["ZipCode"] . "&nbsp;&nbsp;" . $debtor["City"];
        echo "</td>\n\t\t\t\t\t\t<td>";
        echo $EmailAddress;
        echo "</td>\n\t\t\t\t\t</tr>\n\t\t\t\t\t";
    }
}
if($debtorCounter === 0) {
    echo "\t\t\t\t<tr class=\"tr1\">\n\t\t\t\t\t<td colspan=\"4\">";
    echo __("no debtors found");
    echo "</td>\n\t\t\t\t</tr>\n\t\t\t\t";
}
if(MIN_PAGINATION < $debtors["CountRows"]) {
    echo "\t\t\t\t<tr>\n\t\t\t\t\t<td colspan=\"4\">\n\t\t\t\t\t\t<br />\n\t\t\t\t\t\t";
    ajax_paginate("Debtors", isset($debtors["CountRows"]) ? $debtors["CountRows"] : 0, $_SESSION["debtorgroup.show"]["results"], $current_page, $current_page_url);
    echo "\t\t\t\t\t</td>\n\t\t\t\t</tr>\n\t\t\t\t";
}
echo "\t\t</table>\n\t\t</div>\n\t\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\n<!--box1-->\n</div>\n<!--box1-->\n\n<br />\n\t\n<!--buttonbar-->\n<div class=\"buttonbar\">\n<!--buttonbar-->\n\t\n\t";
if(U_DEBTOR_EDIT) {
    echo "<p class=\"pos1\"><a class=\"button1 edit_icon\" href=\"debtors.php?page=add_group&amp;id=";
    echo $group->Identifier;
    echo "\"><span>";
    echo __("edit");
    echo "</span></a></p>";
}
echo "\t";
if(U_DEBTOR_DELETE) {
    echo "<p class=\"pos2\"><a class=\"button1 delete_icon\" onclick=\"\$('#delete_group').dialog('open');\"><span>";
    echo __("delete");
    echo "</span></a></p>";
}
echo "\n<!--buttonbar-->\n</div>\n<!--buttonbar-->\n\n<div id=\"delete_group\" class=\"hide\" title=\"";
echo __("delete debtorgroup dialog title");
echo "\">\n\t<form id=\"GroupForm\" name=\"form_delete\" method=\"post\" action=\"debtors.php?page=delete_group&amp;id=";
echo $group->Identifier;
echo "\">\n\t";
echo sprintf(__("delete debtorgroup dialog description"), $group->GroupName);
echo "<br />\n\t<br />\n\t<input type=\"checkbox\" name=\"imsure\" value=\"yes\" id=\"imsure\"/> <label for=\"imsure\">";
echo __("delete debtorgroup dialog agree");
echo "</label><br />\n\t<br />\n\t<p><a id=\"delete_group_btn\" class=\"button2 alt1 float_left\"><span>";
echo __("delete");
echo "</span></a></p>\n\t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#delete_group').dialog('close');\"><span>";
echo __("cancel");
echo "</span></a></p>\n\t</form>\n</div>\n\n\n";
require_once "views/footer.php";

?>