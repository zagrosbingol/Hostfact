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
echo __("employee overview");
echo "</h2> \n\n\t<p class=\"pos2\"><strong class=\"textsize1\">";
echo __("total");
echo ": <span>";
echo isset($accounts["CountRows"]) ? $accounts["CountRows"] : "0";
echo "</span></strong></p>\n\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n<!--form-->\n<form id=\"EmployeeForm\" name=\"form_employees\" method=\"post\" action=\"company.php?page=accounts\">\t<fieldset>\n<!--form-->\n\t\n\t";
if(U_COMPANY_EDIT) {
    echo "\t<!--optionsbar-->\n\t<div class=\"optionsbar\">\n\t<!--optionsbar-->\n\t\n\t\t<p class=\"pos1\"><a class=\"button1 add_icon\" href=\"company.php?page=account\"><span>";
    echo __("new employee");
    echo "</span></a></p>\n\n\t<!--optionsbar-->\n\t</div>\n\t<!--optionsbar-->\n\t";
}
echo "\t\n\t<div id=\"SubTable_Employees\">\t\t\n\t\t<table id=\"MainTable_Employees\" class=\"table1\" cellpadding=\"0\" cellspacing=\"0\">\n\t\t<tr class=\"trtitle\">\n\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('account.overview','sort','UserName','Employees','";
echo $current_page_url;
echo "');\" class=\"ico set2 pointer ";
if($_SESSION["account.overview"]["sort"] == "UserName") {
    if($_SESSION["account.overview"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("username");
echo "</a></th>\n\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('account.overview','sort','Name','Employees','";
echo $current_page_url;
echo "');\" class=\"ico set2 ";
if($_SESSION["account.overview"]["sort"] == "Name") {
    if($_SESSION["account.overview"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("name");
echo "</a></td>\n\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('account.overview','sort','Function','Employees','";
echo $current_page_url;
echo "');\" class=\"ico set2 ";
if($_SESSION["account.overview"]["sort"] == "Function") {
    if($_SESSION["account.overview"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("function");
echo "</a></th>\n\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('account.overview','sort','Language','Employees','";
echo $current_page_url;
echo "');\" class=\"ico set2 ";
if($_SESSION["account.overview"]["sort"] == "Language") {
    if($_SESSION["account.overview"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("backoffice language");
echo "</a></th>\n            <th scope=\"col\"><a onclick=\"ajaxSave('account.overview','sort','TwoFactorAuthentication','Employees','";
echo $current_page_url;
echo "');\" class=\"ico set2 ";
if($_SESSION["account.overview"]["sort"] == "TwoFactorAuthentication") {
    if($_SESSION["account.overview"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("2 factor authentication");
echo "</a></th>\n            <th scope=\"col\"><a onclick=\"ajaxSave('account.overview','sort','LastDate','Employees','";
echo $current_page_url;
echo "');\" class=\"ico set2 ";
if($_SESSION["account.overview"]["sort"] == "LastDate") {
    if($_SESSION["account.overview"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("last login");
echo "</a></th>\n\t\t</tr>\n\t\t";
global $array_backoffice_languages;
$accountCounter = 0;
foreach ($accounts as $accountID => $value) {
    if(is_numeric($accountID)) {
        $accountCounter++;
        echo "\t\t<tr class=\"hover_extra_info ";
        if($accountCounter % 2 === 1) {
            echo "tr1";
        }
        echo "\">\n\t\t\t<td><a href=\"company.php?page=accountshow&id=";
        echo $accountID;
        echo "\" class=\"c1 a1\">";
        echo $value["UserName"];
        echo "</a></td>\n\t\t\t<td>";
        echo $value["Name"];
        echo "</td>\n\t\t\t<td>";
        echo $value["Function"];
        echo "</td>\n\t\t\t<td>";
        echo $array_backoffice_languages[$value["Language"]];
        echo "</td>\n            <td>";
        echo $value["TwoFactorAuthentication"] == "on" ? __("on") : __("off");
        echo "</td>\n            <td>";
        if($value["LastDate"] != "0000-00-00 00:00:00") {
            echo rewrite_date_db2site($value["LastDate"]) . " " . __("at") . " " . rewrite_date_db2site($value["LastDate"], "%H:%i");
        } else {
            echo "-";
        }
        echo "</td>\n\t\t</tr>\n\t\t";
    }
}
if($accountCounter === 0) {
    echo "\t\t<tr>\n\t\t\t<td colspan=\"5\">\n\t\t\t\t";
    echo __("no results found");
    echo "\t\t\t</td>\n\t\t</tr>\n\t\t";
} elseif(min(MIN_PAGINATION, $_SESSION["account.overview"]["results"]) < $accounts["CountRows"]) {
    echo "\t\t<tr class=\"table_options\">\n\t\t\t<td colspan=\"5\">\n\t\t\t\t<br />";
    paginate(isset($accounts["CountRows"]) ? $accounts["CountRows"] : 0, $_SESSION["account.overview"]["results"], $current_page, $current_page_url, "account.overview");
    echo "\t\t\t</td>\n\t\t</tr>\n\t\t";
}
echo "\t\t</table>\n\t</div>\n\t\n<!--form-->\n</fieldset></form>\n<!--form-->\n\n";
require_once "views/footer.php";

?>