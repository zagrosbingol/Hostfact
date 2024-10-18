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
$page_form_title = 0 < $debtor_id ? __("edit debtorgroup") : __("add debtorgroup");
echo "\n";
echo $message;
echo "\n<!--form-->\n<form id=\"GroupForm\" name=\"form_create\" method=\"post\" action=\"debtors.php?page=add_group\"><fieldset><legend>";
echo $page_form_title;
echo "</legend>\n";
if(0 < $debtor_id) {
    echo "<input type=\"hidden\" name=\"id\" value=\"";
    echo $debtor_id;
    echo "\" />\n";
}
echo "<!--form-->\n\n<!--heading1-->\n<div class=\"heading1\">\n<!--heading1-->\n\n\t<h2>";
echo $page_form_title;
echo "</h2>\n\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n\t<!--box1-->\n\t<div class=\"box2\" id=\"tabs\">\n\t<!--box1-->\n\t\n\t\t<!--top-->\n\t\t<div class=\"top\">\n\t\t<!--top-->\n\t\t\n\t\t\t<ul class=\"list3\">\n\t\t\t\t<li class=\"on\"><a href=\"#tab-general\">";
echo __("general");
echo "</a></li>\n\t\t\t</ul>\n\t\t\t\n\t\t\n\t\t<!--top-->\n\t\t</div>\n\t\t<!--top-->\n\n\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-general\">\n\t\t<!--content-->\n\n\t\t\t\t\t\n\t\t\t<!--box3-->\n\t\t\t<div class=\"box3\"><h3>";
echo __("name");
echo "</h3><div class=\"content\">\n\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t<strong class=\"title\">";
echo __("name");
echo "</strong>\n\t\t\t\t<input type=\"text\" name=\"GroupName\" class=\"text1 size1\" value=\"";
echo $group->GroupName;
echo "\" />\n\t\t\t\t<br /><br />\n\n\t\t\t<!--box3-->\n\t\t\t</div></div>\n\t\t\t<!--box3-->\n\t\t\t\n\t\t\t<p>\n\t\t\t\t<strong>";
echo __("select debtors to connect to debtorgroup");
echo "</strong>\n\t\t\t</p>\n\t\t\t\n\t\t\t<input name=\"Groups\" type=\"hidden\" value=\",";
echo implode(",", $group->Debtors);
if(!empty($group->Debtors)) {
    echo ",";
}
echo "\" />\n\t\t\t\n\t\t\t<div id=\"SubTable_Debtors\">\t\t\t\t\t\t\t\t\n\t\t\t<table id=\"MainTable_Debtors\" class=\"table1\" cellpadding=\"0\" cellspacing=\"0\">\n\t\t\t\t<tr class=\"trtitle\">\n\t\t\t\t\t<th scope=\"col\" style=\"width: 125px;\"><label><input name=\"GroupBatch\" class=\"GroupsBatchCheck\" type=\"checkbox\" value=\"on\" /></label> <a onclick=\"ajaxSave('debtorgroup.add','sort','DebtorCode','Debtors','";
echo $current_page_url;
echo "');\" class=\"ico set2 pointer ";
if($_SESSION["debtorgroup.add"]["sort"] == "DebtorCode") {
    if($_SESSION["debtorgroup.add"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("debtor no");
echo "</a></th>\n\t\t\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('debtorgroup.add','sort','Debtor','Debtors','";
echo $current_page_url;
echo "');\" class=\"ico set2 pointer ";
if($_SESSION["debtorgroup.add"]["sort"] == "Debtor") {
    if($_SESSION["debtorgroup.add"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("name");
echo "</a></th>\n\t\t\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('debtorgroup.add','sort','Address','Debtors','";
echo $current_page_url;
echo "');\" class=\"ico set2 pointer ";
if($_SESSION["debtorgroup.add"]["sort"] == "Address") {
    if($_SESSION["debtorgroup.add"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("address");
echo "</a></th>\n\t\t\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('debtorgroup.add','sort','EmailAddress','Debtors','";
echo $current_page_url;
echo "');\" class=\"ico set2 pointer ";
if($_SESSION["debtorgroup.add"]["sort"] == "EmailAddress") {
    if($_SESSION["debtorgroup.add"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("emailaddress");
echo "</a></th>\n\t\t\t\t</tr>\n\t\t\t\t";
$debtorCounter = 0;
foreach ($debtors as $debtorID => $debtor) {
    if(is_numeric($debtorID)) {
        $debtorCounter++;
        $EmailAddress = $debtor["EmailAddress"] != "" ? rtrim(substr(check_email_address($debtor["EmailAddress"], "convert", ", "), 0, 75), ", ") . (75 < strlen($debtor["EmailAddress"]) ? "..." : "") : "&nbsp;";
        echo "\t\t\t\t\t\t<tr";
        if($debtorCounter % 2 === 1) {
            echo " class=\"tr1\"";
        }
        echo ">\n\t\t\t\t\t\t\t<td><input name=\"Products[]\" type=\"checkbox\" class=\"GroupBatch\" value=\"";
        echo $debtorID;
        echo "\" ";
        if(in_array($debtorID, $group->Debtors)) {
            echo "checked=\"checked\"";
        }
        echo "/> ";
        echo $debtor["DebtorCode"];
        echo "</td>\n\t\t\t\t\t\t\t<td>";
        echo $debtor["CompanyName"] ? $debtor["CompanyName"] : $debtor["SurName"] . ", " . $debtor["Initials"];
        echo "</td>\n\t\t\t\t\t\t\t<td>";
        echo $debtor["Address"] . ", " . $debtor["ZipCode"] . "&nbsp;&nbsp;" . $debtor["City"];
        echo "</td>\n\t\t\t\t\t\t\t<td>";
        echo $EmailAddress;
        echo "</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t";
    }
}
if($debtorCounter === 0) {
    echo "\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td colspan=\"4\">\n\t\t\t\t\t\t\t";
    echo __("no results found");
    echo "\t\t\t\t\t\t</td>\n\t\t\t\t\t</tr>\n\t\t\t\t\t";
} elseif(MIN_PAGINATION < $debtors["CountRows"]) {
    echo "\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td colspan=\"4\">\n\t\t\t\t\t\t\t";
    ajax_paginate("Debtors", isset($debtors["CountRows"]) ? $debtors["CountRows"] : 0, $_SESSION["debtorgroup.add"]["results"], $current_page, $current_page_url);
    echo "\t\t\t\t\t\t</td>\n\t\t\t\t\t</tr>\n\t\t\t\t\t";
}
echo "\t\t\t</table>\n\t\t\t</div>\n\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\n\t<!--box1-->\n\t</div>\n\t<!--box1-->\n\n\t<br />\n\t\t\t\t\t\n\t<p class=\"align_right\">\n        <span id=\"selectedNumber\" class=\"c2";
if(empty($group->Debtors)) {
    echo " hide";
}
echo "\">\n            ";
echo sprintf(__("number of selected debtors"), "<strong>" . count($group->Debtors) . "</strong>");
echo "        </span> \n        <a class=\"button1 alt1\" id=\"form_create_btn\">\n            <span>";
echo 0 < $debtor_id ? __("btn edit") : __("btn add");
echo "</span>\n        </a>\n    </p>\n\t\n<!--form-->\n</fieldset></form>\n<!--form-->\n\t\n";
require_once "views/footer.php";

?>