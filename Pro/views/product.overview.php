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
echo __("product overview");
echo "</h2>\n\n\t";
if(isset($_SESSION["product.overview"]["group"]) && $_SESSION["product.overview"]["group"] != "") {
    if(is_numeric($_SESSION["product.overview"]["group"])) {
        echo "\t\t<p class=\"pos3\"><strong class=\"textsize1\">- ";
        echo __("productgroup");
        echo ": ";
        echo $groups[$_SESSION["product.overview"]["group"]]["GroupName"];
        echo "</strong></p>\n\t\t";
    }
    echo "\t";
}
echo "\t\n\t<p class=\"pos2\">\n\t\t<strong class=\"textsize1\">";
echo __("total");
echo ": <span>";
echo isset($products["CountRows"]) ? $products["CountRows"] : "0";
echo "</span></strong>\n\t</p>\n\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n<!--optionsbar-->\n<div class=\"optionsbar\">\n<!--optionsbar-->\n\n\t";
if(U_PRODUCT_ADD) {
    echo "\t<p class=\"pos1\"><a class=\"button1 add_icon\" href=\"products.php?page=add\"><span>";
    echo __("add product");
    echo "</span></a></p>\n\t";
}
echo "\t\n\t";
if(0 < $groups["CountRows"]) {
    echo "\t\t<p class=\"pos2\"><a onclick=\"save('product.overview','group','', '";
    echo $current_page_url;
    echo "');\" class=\"sizenormal c1 a1 pointer\">";
    echo __("show all");
    echo "</a> <span> | </span> ";
    echo __("productgroup");
    echo " <select class=\"select1\" onchange=\"save('product.overview','group',this.value, '";
    echo $current_page_url;
    echo "');\">\n\t\t<option value=\"\">";
    echo __("make your choice");
    echo "</option>\n\t\t";
    foreach ($groups as $k => $v) {
        if(is_numeric($k)) {
            echo "<option value=\"";
            echo $v["id"];
            echo "\"";
            if($_SESSION["product.overview"]["group"] == $v["id"]) {
                echo " selected=\"selected\"";
            }
            echo ">";
            echo $v["GroupName"];
            echo "</option>";
        }
    }
    echo "</select></p>\n\t";
}
echo "<!--optionsbar-->\n</div>\n<!--optionsbar-->\n\n";
require_once "views/elements/product.table.php";
$options = ["redirect_page" => "products", "session_name" => "product.overview", "current_page" => $current_page, "current_page_url" => $current_page_url];
show_product_table($products, $options);
echo "\n";
require_once "views/footer.php";

?>