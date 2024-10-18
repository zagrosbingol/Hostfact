<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
if(U_STATISTICS_SHOW) {
    require_once "class/widget.php";
    $current_widget = new widget();
    $data = $current_widget->getWidgetGraphRevenue($widget["Option1"]);
    if($data) {
        echo "\t\t<div id=\"diagram_bar_";
        echo $widget["id"];
        echo "\" class=\"diagram_bar\"></div>\n\t\t<script type=\"text/javascript\">\n\t\t\tvar values_";
        echo $widget["id"];
        echo " = new Array();\n\t\t\tvar units_";
        echo $widget["id"];
        echo " = new Array();\n\t\t\tvar values_lastyear_";
        echo $widget["id"];
        echo " = new Array();\n\t\t\t";
        foreach ($data as $key => $value) {
            echo "values_";
            echo $widget["id"];
            echo "[";
            echo $key;
            echo "] = ";
            echo $value["value"];
            echo ";units_";
            echo $widget["id"];
            echo "[";
            echo $key;
            echo "] = '";
            echo $value["label"];
            echo "';";
            if(isset($value["value_lastyear"])) {
                echo "values_lastyear_";
                echo $widget["id"];
                echo "[";
                echo $key;
                echo "] = ";
                echo $value["value_lastyear"];
                echo ";";
            }
        }
        echo "\t\t\t\n\t\t\tif(!\$('#diagram_bar_";
        echo $widget["id"];
        echo "').html().length){\n\t\t\t\tcreateDiagramBar('diagram_bar_";
        echo $widget["id"];
        echo "',values_";
        echo $widget["id"];
        echo ",units_";
        echo $widget["id"];
        echo ",'";
        echo __("turnover");
        echo "', values_lastyear_";
        echo $widget["id"];
        echo ");\n\t\t\t}\n\t\t</script>\n\t\t";
    } else {
        echo "<div class=\"widget_graph_nodata\" ";
        if($widget["Width"] == 160) {
            echo "style=\"width:158px;\"";
        }
        echo ">";
        echo __("widget graph no data yet");
        echo "</div>";
    }
} else {
    echo "\t<div class=\"norights\">";
    echo __("insufficient rights");
    echo "</div>\n";
}

?>