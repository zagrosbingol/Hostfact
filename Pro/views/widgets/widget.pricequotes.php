<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
if(U_PRICEQUOTE_SHOW) {
    require_once "class/widget.php";
    $current_widget = new widget();
    $data = $current_widget->getWidgetOpenPricequotes();
    echo "<h3>";
    echo isset($data["CountRows"]) && $data["CountRows"] != "" ? $data["CountRows"] : 0;
    echo "</h3>\n\n<font style=\"font-weight:bold;color:#999\">\n\tfacturen<br />\n\t";
    if($widget["Option1"] == "d") {
        echo __("today");
    }
    echo "\t";
    if($widget["Option1"] == "w") {
        echo __("this week");
    }
    echo "\t";
    if($widget["Option1"] == "m") {
        echo __("this month");
    }
    echo "\t";
    if($widget["Option1"] == "q") {
        echo __("this quarter");
    }
    echo "\t";
    if($widget["Option1"] == "y") {
        echo __("this year");
    }
    echo "</font>\n\n";
} else {
    echo "\t<div class=\"norights\">";
    echo __("insufficient rights");
    echo "</div>\n";
}

?>