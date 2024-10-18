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
echo __("pricequote overview") . " (" . $pricequotes["CountRows"] . ")";
echo "</h2>\n\t\n\t";
if(isset($_SESSION["pricequote.overview"]["status"]) && $_SESSION["pricequote.overview"]["status"] != "" && isset($array_pricequotestatus[$_SESSION["pricequote.overview"]["status"]])) {
    echo "\t\t<p class=\"pos3\"><strong class=\"textsize1\">- ";
    echo __("status");
    echo ": ";
    echo $array_pricequotestatus[$_SESSION["pricequote.overview"]["status"]];
    echo "</strong></p>\n\t";
}
echo "\t\n\t<p class=\"pos2\">\n\t\t<strong class=\"textsize1 pagetotals\">";
echo __("page total");
echo ": ";
echo money($pricequotes["TotalAmountExcl"]);
echo " ";
echo __("excl vat");
echo " / ";
echo money($pricequotes["TotalAmountIncl"]);
echo " ";
echo __("incl vat");
echo "</strong>\n\t</p>\n\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n<!--optionsbar-->\n<div class=\"optionsbar\">\n<!--optionsbar-->\n\t";
if(U_PRICEQUOTE_ADD) {
    echo "<p class=\"pos1\"><a class=\"button1 add_icon\" href=\"pricequotes.php?page=add\"><span>";
    echo __("add pricequote");
    echo "</span></a></p>";
}
echo "\t<p class=\"pos2\"><a onclick=\"save('pricequote.overview','status','2','";
echo $current_page_url;
echo "');\" class=\"sizenormal c1 a1\">";
echo __("show open pricequotes");
echo "</a> <span> | </span>  <a onclick=\"save('pricequote.overview','status','','";
echo $current_page_url;
echo "');\" class=\"sizenormal c1 a1\">";
echo __("show all");
echo "</a> <span> | </span> ";
echo __("status");
echo " <select class=\"select1\" onchange=\"save('pricequote.overview','status',this.value, '";
echo $current_page_url;
echo "');\">\n\t<option value=\"\">";
echo __("please choose");
echo "</option>\n\t";
foreach ($array_pricequotestatus as $k => $v) {
    if(is_numeric($k)) {
        echo "<option value=\"";
        echo $k;
        echo "\"";
        if($_SESSION["pricequote.overview"]["status"] == $k . "") {
            echo " selected=\"selected\"";
        }
        echo ">";
        echo $v;
        echo "</option>";
    }
}
echo "</select></p>\n\n<!--optionsbar-->\n</div>\n<!--optionsbar-->\n\n";
require_once "views/elements/pricequote.table.php";
$options = ["redirect_page" => "pricequotes", "session_name" => "pricequote.overview", "current_page" => $current_page, "current_page_url" => $current_page_url];
show_pricequote_table($pricequotes, $options);
echo "\t\t\n";
require_once "views/footer.php";

?>