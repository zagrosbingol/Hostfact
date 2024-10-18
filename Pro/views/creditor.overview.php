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
echo __("creditor overview");
echo "</h2>\n\t";
if(isset($_SESSION["creditor.overview"]["group"]) && $_SESSION["creditor.overview"]["group"] != "") {
    if(is_numeric($_SESSION["creditor.overview"]["group"])) {
        echo "\t\t<p class=\"pos3\"><strong class=\"textsize1\">- ";
        echo __("creditorgroup");
        echo ": ";
        echo $group_list[$_SESSION["creditor.overview"]["group"]]["GroupName"];
        echo "</strong></p>\n\t\t";
    }
    echo "\t";
}
echo "\t\n\t<p class=\"pos2\"><strong class=\"textsize1\">";
echo __("total");
echo ": <span>";
echo isset($creditors["CountRows"]) ? $creditors["CountRows"] : "0";
echo "</span></strong></p>\n\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n<!--optionsbar-->\n<div class=\"optionsbar\">\n<!--optionsbar-->\n\t";
if(U_CREDITOR_ADD) {
    echo "<p class=\"pos1\"><a class=\"button1 add_icon\" href=\"creditors.php?page=add\"><span>";
    echo __("new creditor");
    echo "</span></a></p>";
}
echo "\t\n\t";
if(0 < $group_list["CountRows"]) {
    echo "\t\t<p class=\"pos2\">\n\t\t\t<a onclick=\"save('creditor.overview','group','','";
    echo $current_page_url;
    echo "');\" class=\"sizenormal c1 a1\">";
    echo __("show all");
    echo "</a> <span> | </span> ";
    echo __("creditorgroup");
    echo "\t\t\t<select class=\"select1\" onchange=\"save('creditor.overview','group',this.value,'";
    echo $current_page_url;
    echo "');\">\n\t\t\t\t<option value=\"\">";
    echo __("please choose");
    echo "</option>\n\t\t\t\t";
    foreach ($group_list as $k => $v) {
        if(is_numeric($k)) {
            echo "\t\t\t\t\t<option value=\"";
            echo $v["id"];
            echo "\"";
            if($_SESSION["creditor.overview"]["group"] == $v["id"]) {
                echo " selected=\"selected\"";
            }
            echo ">";
            echo $v["GroupName"];
            echo "</option>\n\t\t\t\t";
        }
    }
    echo "\t\t\t</select>\n\t\t</p>\n\t";
}
echo "<!--optionsbar-->\n</div>\n<!--optionsbar-->\n\n";
require_once "views/elements/creditor.table.php";
$options = ["redirect_page" => "creditors", "session_name" => "creditor.overview", "current_page" => $current_page, "current_page_url" => $current_page_url];
show_creditor_table($creditors, $options);
echo "\n";
require_once "views/footer.php";

?>