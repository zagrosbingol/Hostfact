<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
if(U_INVOICE_SHOW) {
    require_once "class/widget.php";
    $current_widget = new widget();
    $data = $current_widget->getWidgetInvoices($widget["Option1"]);
    if($data < 99999) {
        $fontsize = "medium";
    } else {
        $fontsize = "small";
    }
    echo "<h3 class=\"";
    echo $fontsize;
    echo "\">";
    echo isset($data) && $data != "" ? $data : 0;
    echo "</h3>\n<font style=\"font-weight:bold;color:#999\">\n\t";
    echo strtolower(__("invoices"));
    echo "<br />\n\t";
    if($widget["Option1"] == "d" || $widget["Option1"] == "day") {
        echo __("today");
    }
    echo "    ";
    if($widget["Option1"] == "w" || $widget["Option1"] == "week") {
        echo __("this week");
    }
    echo "\t";
    if($widget["Option1"] == "m" || $widget["Option1"] == "month") {
        echo __("this month");
    }
    echo "\t";
    if($widget["Option1"] == "q" || $widget["Option1"] == "quarter") {
        echo __("this quarter");
    }
    echo "\t";
    if($widget["Option1"] == "y" || $widget["Option1"] == "year") {
        echo __("this year");
    }
    echo "</font>\n\n";
} else {
    echo "\t<div class=\"norights\">";
    echo __("insufficient rights");
    echo "</div>\n";
}

?>