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
echo __("creditinvoice overview");
echo "</h2>\n\n\t";
if(isset($_SESSION["creditinvoice.overview"]["status"]) && $_SESSION["creditinvoice.overview"]["status"] != "" && isset($array_creditinvoicestatus[$_SESSION["creditinvoice.overview"]["status"]])) {
    echo "\t\t<p class=\"pos3\"><strong class=\"textsize1\">- ";
    echo __("status");
    echo ": ";
    echo $array_creditinvoicestatus[$_SESSION["creditinvoice.overview"]["status"]];
    echo "</strong></p>\n\t";
}
echo "\n\t<p class=\"pos2\"><strong class=\"textsize1 pagetotals\">";
echo __("page total");
echo ": <span>";
echo money($creditinvoices["TotalAmountIncl"]);
echo "</span></strong></p>\n\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n<!--optionsbar-->\n<div class=\"optionsbar\">\n<!--optionsbar-->\n\t<p class=\"pos1\">";
if(U_CREDITOR_EDIT) {
    echo "<a class=\"button1 add_icon\" href=\"creditors.php?page=add_invoice\"><span>";
    echo __("add creditinvoice");
    echo "</span></a>";
}
echo "</p>\n\t<p class=\"pos2\"><a onclick=\"save('creditinvoice.overview','status','', '";
echo $current_page_url;
echo "');\" class=\"sizenormal c1 a1 pointer\">";
echo __("show all");
echo "</a> <span> | </span> ";
echo __("status");
echo " <select class=\"select1\" onchange=\"save('creditinvoice.overview','status',this.value, '";
echo $current_page_url;
echo "');\">\n\t\t<option value=\"\">";
echo __("make your choice");
echo "</option>\n\t\t";
foreach ($array_creditinvoicestatus as $k => $v) {
    echo "<option value=\"";
    echo $k;
    echo "\" ";
    if(is_numeric($_SESSION["creditinvoice.overview"]["status"]) && $_SESSION["creditinvoice.overview"]["status"] == $k) {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo $v;
    echo "</option>";
}
echo "\t\t\t\t\n\t</select></p>\n\n<!--optionsbar-->\n</div>\n<!--optionsbar-->\n\n";
require_once "views/elements/creditinvoice.table.php";
$options = ["session_name" => "creditinvoice.overview", "current_page" => $current_page, "current_page_url" => $current_page_url];
show_creditinvoice_table($creditinvoices, $options);
echo "\n";
require_once "views/footer.php";

?>