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
echo __("debtor overview");
echo "</h2> \n\t";
if(isset($_SESSION["debtor.overview"]["group"]) && $_SESSION["debtor.overview"]["group"] != "") {
    if(is_numeric($_SESSION["debtor.overview"]["group"])) {
        echo "\t\t<p class=\"pos3\"><strong class=\"textsize1\">- ";
        echo __("debtorgroup");
        echo ": ";
        echo $group_list[$_SESSION["debtor.overview"]["group"]]["GroupName"];
        echo "</strong></p>\n\t\t";
    } elseif($_SESSION["debtor.overview"]["group"] == "auth") {
        echo "\t\t<p class=\"pos3\"><strong class=\"textsize1\">- ";
        echo __("only incasso debtors");
        echo "</strong></p>\n\t\t";
    } elseif($_SESSION["debtor.overview"]["group"] == "archived") {
        echo "\t\t<p class=\"pos3\"><strong class=\"textsize1\">- ";
        echo __("filter archived clients");
        echo "</strong></p>\n\t\t";
    }
    echo "\t";
}
echo "\n\t<p class=\"pos2\"><strong class=\"textsize1\">";
echo __("total");
echo ": <span>";
echo isset($list_debtors["CountRows"]) ? $list_debtors["CountRows"] : "0";
echo "</span></strong></p>\n\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n<!--optionsbar-->\n<div class=\"optionsbar\">\n<!--optionsbar-->\n\t\n\t";
if(U_DEBTOR_ADD) {
    echo "<p class=\"pos1\"><a class=\"button1 add_icon\" href=\"debtors.php?page=add\"><span>";
    echo __("new debtor");
    echo "</span></a></p>";
}
echo "\t<p class=\"pos2\"><a onclick=\"save('debtor.overview','group','auth', '";
echo $current_page_url;
echo "');\" class=\"sizenormal c1 a1\">";
echo __("only incasso debtors");
echo "</a> <span> | </span> <a onclick=\"save('debtor.overview','group','', '";
echo $current_page_url;
echo "');\" class=\"sizenormal c1 a1\">";
echo __("filter active clients");
echo "</a> <span> | </span> <a onclick=\"save('debtor.overview','group','archived', '";
echo $current_page_url;
echo "');\" class=\"sizenormal c1 a1\">";
echo __("filter archived clients");
echo "</a> ";
if(0 < $group_list["CountRows"]) {
    echo "<span> | </span> ";
    echo __("debtorgroup");
    echo " <select class=\"select1\" onchange=\"save('debtor.overview','group',this.value, '";
    echo $current_page_url;
    echo "');\">\n\t<option value=\"\">";
    echo __("please choose");
    echo "</option>\n\t";
    foreach ($group_list as $k => $v) {
        if(is_numeric($k)) {
            echo "<option value=\"";
            echo $v["id"];
            echo "\"";
            if($_SESSION["debtor.overview"]["group"] == $v["id"]) {
                echo " selected=\"selected\"";
            }
            echo ">";
            echo $v["GroupName"];
            echo "</option>";
        }
    }
    echo "</select>";
}
echo "</p>\n\t\n<!--optionsbar-->\n</div>\n<!--optionsbar-->\n\n";
require_once "views/elements/debtor.table.php";
$options = ["session_name" => "debtor.overview", "current_page" => $current_page, "current_page_url" => $current_page_url, "group_list" => $group_list, "filter" => $_SESSION["debtor.overview"]["group"]];
show_debtor_table($list_debtors, $options);
echo "\n";
require_once "views/footer.php";

?>