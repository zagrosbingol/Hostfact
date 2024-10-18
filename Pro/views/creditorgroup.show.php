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
echo __("creditorgroup");
echo " ";
echo $group->GroupName;
echo " </h2>\n\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n<!--box1-->\n<div class=\"box2\" id=\"tabs\">\n<!--box1-->\n\n\t<!--top-->\n\t<div class=\"top\">\n\t<!--top-->\n\t\n\t\t<ul class=\"list3\">\n\t\t\t<li class=\"on\"><a href=\"#tab-general\">";
echo __("general");
echo "</a></li>\n\t\t</ul>\t\t\t\n\t\t\n\t<!--top-->\n\t</div>\n\t<!--top-->\n\n\t<!--content-->\n\t<div class=\"content\" id=\"tab-general\">\n\t<!--content-->\n\n\t\t<p>\n\t\t\t<strong>";
echo __("the following creditors are in the creditorgroup");
echo "</strong>\n\t\t</p>\n\n\t\t<div id=\"SubTable_Creditors\">\t\t\t\t\t\t\t\t\n\t\t<table id=\"MainTable_Creditors\" class=\"table1\" cellpadding=\"0\" cellspacing=\"0\">\n\t\t\t<tr class=\"trtitle\">\n\t\t\t\t<th scope=\"col\" style=\"width: 125px;\"><a onclick=\"ajaxSave('creditorgroup.show','sort','CreditorCode','Creditors','";
echo $current_page_url;
echo "');\" class=\"ico set2 pointer ";
if($_SESSION["creditorgroup.show"]["sort"] == "CreditorCode") {
    if($_SESSION["creditorgroup.show"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("creditor no");
echo "</a></th>\n\t\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('creditorgroup.show','sort','Creditor','Creditors','";
echo $current_page_url;
echo "');\" class=\"ico set2 pointer ";
if($_SESSION["creditorgroup.show"]["sort"] == "Creditor") {
    if($_SESSION["creditorgroup.show"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("name");
echo "</a></th>\n\t\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('creditorgroup.show','sort','Address','Creditors','";
echo $current_page_url;
echo "');\" class=\"ico set2 pointer ";
if($_SESSION["creditorgroup.show"]["sort"] == "Address") {
    if($_SESSION["creditorgroup.show"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("address");
echo "</a></th>\n\t\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('creditorgroup.show','sort','EmailAddress','Creditors','";
echo $current_page_url;
echo "');\" class=\"ico set2 pointer ";
if($_SESSION["creditorgroup.show"]["sort"] == "EmailAddress") {
    if($_SESSION["creditorgroup.show"]["order"] == "ASC") {
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
$creditorCounter = 0;
foreach ($creditors as $creditorID => $creditor) {
    if(is_numeric($creditorID)) {
        $creditorCounter++;
        $EmailAddress = $creditor["EmailAddress"] != "" ? rtrim(substr(check_email_address($creditor["EmailAddress"], "convert", ", "), 0, 75), ", ") . (75 < strlen($creditor["EmailAddress"]) ? "..." : "") : "&nbsp;";
        echo "\t\t\t\t\t<tr";
        if($creditorCounter % 2 === 1) {
            echo " class=\"tr1\"";
        }
        echo ">\n\t\t\t\t\t\t<td><a href=\"creditors.php?page=show&amp;id=";
        echo $creditorID;
        echo "\" class=\"c1 a1\">";
        echo $creditor["CreditorCode"];
        echo "</a></td>\n\t\t\t\t\t\t<td>";
        echo $creditor["CompanyName"] ? $creditor["CompanyName"] : $creditor["SurName"] . ", " . $creditor["Initials"];
        echo "</td>\n\t\t\t\t\t\t<td>";
        echo $creditor["Address"] . ", " . $creditor["ZipCode"] . "&nbsp;&nbsp;" . $creditor["City"];
        echo "</td>\n\t\t\t\t\t\t<td>";
        echo $EmailAddress;
        echo "</td>\n\t\t\t\t\t</tr>\n\t\t\t\t\t";
    }
}
if($creditorCounter === 0) {
    echo "\t\t\t\t<tr class=\"tr1\">\n\t\t\t\t\t<td colspan=\"4\">";
    echo __("no creditors found");
    echo "</td>\n\t\t\t\t</tr>\n\t\t\t\t";
}
if(MIN_PAGINATION < $creditors["CountRows"]) {
    echo "\t\t\t\t<tr>\n\t\t\t\t\t<td colspan=\"4\">\n\t\t\t\t\t\t<br />\n\t\t\t\t\t\t";
    ajax_paginate("Creditors", isset($creditors["CountRows"]) ? $creditors["CountRows"] : 0, $_SESSION["creditorgroup.show"]["results"], $current_page, $current_page_url);
    echo "\t\t\t\t\t</td>\n\t\t\t\t</tr>\n\t\t\t\t";
}
echo "\t\t</table>\n\t\t</div>\n\t\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\n<!--box1-->\n</div>\n<!--box1-->\n\n<br />\n\t\n<!--buttonbar-->\n<div class=\"buttonbar\">\n<!--buttonbar-->\n\t\n\t";
if(U_CREDITOR_EDIT) {
    echo "<p class=\"pos1\"><a class=\"button1 edit_icon\" href=\"creditors.php?page=add_group&amp;id=";
    echo $group->Identifier;
    echo "\"><span>";
    echo __("edit");
    echo "</span></a></p>";
}
echo "\t";
if(U_CREDITOR_DELETE) {
    echo "<p class=\"pos2\"><a class=\"button1 delete_icon\" onclick=\"\$('#delete_group').dialog('open');\"><span>";
    echo __("delete");
    echo "</span></a></p>";
}
echo "\n<!--buttonbar-->\n</div>\n<!--buttonbar-->\n\n";
if(U_CREDITOR_DELETE) {
    echo "<div id=\"delete_group\" class=\"hide\" title=\"";
    echo __("delete creditorgroup dialog title");
    echo "\">\n\t<form id=\"GroupForm\" name=\"form_delete\" method=\"post\" action=\"creditors.php?page=delete_group&amp;id=";
    echo $group->Identifier;
    echo "\">\n\t";
    echo sprintf(__("delete creditorgroup dialog description"), $group->GroupName);
    echo "<br />\n\t<br />\n\t<input type=\"checkbox\" name=\"imsure\" value=\"yes\" id=\"imsure\"/> <label for=\"imsure\">";
    echo __("delete creditorgroup dialog agree");
    echo "</label><br />\n\t<br />\n\t<p><a id=\"delete_group_btn\" class=\"button2 alt1 float_left\"><span>";
    echo __("delete");
    echo "</span></a></p>\n\t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#delete_group').dialog('close');\"><span>";
    echo __("cancel");
    echo "</span></a></p>\n\t</form>\n</div>\n";
}
echo "\n";
require_once "views/footer.php";

?>