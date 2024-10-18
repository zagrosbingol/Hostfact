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
echo __("hosting overview");
echo "</h2> \n\t\n\t";
if(isset($_SESSION["hosting.overview"]["status"]) && $_SESSION["hosting.overview"]["status"] != "" && isset($array_hostingstatus[$_SESSION["hosting.overview"]["status"]])) {
    echo "\t\t<p class=\"pos3\"><strong class=\"textsize1\">- ";
    echo __("status");
    echo ": ";
    echo $array_hostingstatus[$_SESSION["hosting.overview"]["status"]];
    echo "</strong></p>\n\t";
}
echo "\t<p class=\"pos2\"><strong class=\"textsize1\">";
echo __("total");
echo ": <span>";
echo isset($list_hosting_accounts["CountRows"]) ? $list_hosting_accounts["CountRows"] : "0";
echo "</span></strong></p>\n\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n\t<!--optionsbar-->\n\t<div class=\"optionsbar\">\n\t<!--optionsbar-->\n\t\t";
if(U_HOSTING_ADD) {
    echo "<p class=\"pos1\"><a class=\"button1 add_icon\" href=\"services.php?page=add&amp;type=hosting\"><span>";
    echo __("new account");
    echo "</span></a></p>";
}
echo "\t\t\n\n\t\t<p class=\"pos2\"><a onclick=\"save('hosting.overview','status','','";
echo $current_page_url;
echo "');\" class=\"sizenormal c1 a1\">";
echo __("show all");
echo "</a> <span> | </span> ";
echo __("status");
echo " <select class=\"select1\" onchange=\"save('hosting.overview','status',this.value, '";
echo $current_page_url;
echo "');\">\n\t\t<option value=\"\">";
echo __("please choose");
echo "</option>\n\t\t";
foreach ($array_hostingstatus as $k => $v) {
    if(is_numeric($k)) {
        echo "<option value=\"";
        echo $k;
        echo "\"";
        if($_SESSION["hosting.overview"]["status"] == $k && strlen($_SESSION["hosting.overview"]["status"]) == strlen($k)) {
            echo " selected=\"selected\"";
        }
        echo ">";
        echo $v;
        echo "</option>";
    }
}
echo "</select></p>\n\n\t<!--optionsbar-->\n\t</div>\n\t<!--optionsbar-->\n\t";
require_once "views/elements/hosting.table.php";
$options = ["table_type" => "maintable", "redirect_page" => "", "redirect_id" => "", "session_name" => "hosting.overview", "current_page" => $current_page, "current_page_url" => $current_page_url];
show_hosting_table($list_hosting_accounts, $options);
echo "\n";
require_once "views/footer.php";

?>