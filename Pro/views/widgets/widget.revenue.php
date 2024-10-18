<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
if(U_INVOICE_SHOW && U_STATISTICS_SHOW) {
    require_once "class/widget.php";
    $current_widget = new widget();
    $data = $current_widget->getWidgetRevenue($widget["Option1"]);
    $revenue = $data->SalesExcl - $data->PurchasesExcl;
    if(mb_strlen(money($revenue, true)) <= 10) {
        $fontsize = "medium";
    } elseif(mb_strlen(money($revenue, true)) <= 14) {
        $fontsize = "small";
    } else {
        $fontsize = "extrasmall";
    }
    echo "<h3 class=\"";
    echo $fontsize;
    echo "\">";
    echo isset($revenue) ? money($revenue, true) : money(0, true);
    echo "</h3>\n<font style=\"font-weight:bold;color:#999\">\n\t";
    echo strtolower(__("profit"));
    echo "<br />\n\t";
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