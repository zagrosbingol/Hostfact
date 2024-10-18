<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "<div style=\"position:relative;\">\n\t<div id=\"chooseperiod\" class=\"hide\">\n\t\t<table id=\"tableleft\">\n\t\t\t<tr>\n\t\t\t\t<td colspan=\"2\"><strong>";
echo __("choose period");
echo "</strong></td>\n\t\t\t</tr>\n\t\t\t<tr>\n\t\t\t\t<td>";
echo __("startdate");
echo ":</td>\n\t\t\t\t<td><input type=\"text\" class=\"text1\" id=\"start_date\" name=\"start_date\" value=\"";
echo rewrite_date_db2site($stats->s_year . "-" . $stats->s_month . "-" . $stats->s_day);
echo "\" /></td>\n\t\t\t</tr>\n\t\t\t<tr>\n\t\t\t\t<td>";
echo __("enddate");
echo ":</td>\n\t\t\t\t<td><input type=\"text\" class=\"text1\" id=\"end_date\" name=\"end_date\" value=\"";
echo rewrite_date_db2site($stats->e_year . "-" . $stats->e_month . "-" . $stats->e_day);
echo "\" /></td>\n\t\t\t</tr>\n\t\t\t<tr>\n\t\t\t\t<td>&nbsp;</td>\n\t\t\t\t<td><a id=\"view_period\" class=\"c1 a1\">";
echo __("view period");
echo "</a></td>\n\t\t\t</tr>\n\t\t</table>\n\t\t\n\t\t<table id=\"tableright\" cellpadding=\"3\">\n\t\t\t<tr>\n\t\t\t\t<td colspan=\"2\"><a class=\"pointer\" id=\"m_current\">";
echo __("current month");
echo "</a></td>\n\t\t\t</tr>\n\t\t\t";
if(isset($period_dialog_future) && $period_dialog_future === true) {
    echo "\t\t\t<tr>\n\t\t\t\t<td colspan=\"2\"><a class=\"pointer\" id=\"m_next\">";
    echo __("next month");
    echo "</a></td>\n\t\t\t</tr>\n\t\t\t";
} else {
    echo "\t\t\t<tr>\n\t\t\t\t<td colspan=\"2\"><a class=\"pointer\" id=\"m_previous\">";
    echo __("previous month");
    echo "</a></td>\n\t\t\t</tr>\n\t\t\t";
}
echo "\t\t\t<tr>\n\t\t\t\t<td colspan=\"2\">&nbsp;</td>\n\t\t\t</tr>\n\t\t\t";
if(isset($period_dialog_future) && $period_dialog_future === true) {
    echo "\t\t\t\t<tr>\n\t\t\t\t\t<td><strong><a class=\"pointer\" id=\"y_";
    echo date("Y");
    echo "\">";
    echo date("Y");
    echo "</a></strong></td>\n\t\t\t\t\t<td><strong><a class=\"pointer\" id=\"y_";
    echo date("Y") + 1;
    echo "\">";
    echo date("Y") + 1;
    echo "</a></strong></td>\n\t\t\t\t</tr>\n\t\t\t\t";
    for ($x = 1; $x <= 4; $x++) {
        echo "\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td><a class=\"pointer\" id=\"q_";
        echo $x;
        echo "_";
        echo date("Y");
        echo "\">";
        echo __("quarter");
        echo " ";
        echo $x;
        echo "</a></td>\n\t\t\t\t\t\t<td><a class=\"pointer\" id=\"q_";
        echo $x;
        echo "_";
        echo date("Y") + 1;
        echo "\">";
        echo __("quarter");
        echo " ";
        echo $x;
        echo "</a></td>\n\t\t\t\t\t</tr>\n\t\t\t\t";
    }
    echo "\t\t\t";
} else {
    echo "\t\t\t\t<tr>\n\t\t\t\t\t<td><strong><a class=\"pointer\" id=\"y_";
    echo date("Y") - 1;
    echo "\">";
    echo date("Y") - 1;
    echo "</a></strong></td>\n\t\t\t\t\t<td><strong><a class=\"pointer\" id=\"y_";
    echo date("Y");
    echo "\">";
    echo date("Y");
    echo "</a></strong></td>\n\t\t\t\t</tr>\n\t\t\t\t";
    for ($x = 1; $x <= 4; $x++) {
        echo "\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td><a class=\"pointer\" id=\"q_";
        echo $x;
        echo "_";
        echo date("Y") - 1;
        echo "\">";
        echo __("quarter");
        echo " ";
        echo $x;
        echo "</a></td>\n\t\t\t\t\t\t<td><a class=\"pointer\" id=\"q_";
        echo $x;
        echo "_";
        echo date("Y");
        echo "\">";
        echo __("quarter");
        echo " ";
        echo $x;
        echo "</a></td>\n\t\t\t\t\t</tr>\n\t\t\t\t";
    }
    echo "\t\t\t";
}
echo "\t\t</table>\n\t</div>\n</div>";

?>