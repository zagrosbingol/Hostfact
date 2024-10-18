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
echo __("agenda");
echo "</h2>\n\t<input type=\"hidden\" value=\"";
echo $current_page_url;
echo "\" id=\"current_url\" />\n\t\n\t";
if(isset($agenda->SearchString) && $agenda->SearchString) {
    echo "\t\t<p class=\"pos3\"><strong class=\"textsize1\">- ";
    echo sprintf(__("agenda search results for"), htmlspecialchars($agenda->SearchString));
    echo "</strong></p>\n\t";
} else {
    echo "\t\n\t\t";
    if(!isset($agenda->CustomDate) || $agenda->CustomDate !== true) {
        echo "\t\t<p class=\"pos1\" style=\"line-height: 20px; margin-left: -50px;\">\n\t\t\t<a class=\"ico set3 arrowleft pointer\" style=\"margin-top: 3px;\" onclick=\"save('agenda.overview','move',-1, '";
        echo $current_page_url;
        echo "');\">&nbsp;&lt; ";
        echo __("agenda last period");
        echo "</a> \n\t\t\t<strong>";
        echo $agenda->Label;
        echo "</strong> \n\t\t\t<a class=\"ico set3 arrowright pointer\" style=\"margin-top: 3px;\" onclick=\"save('agenda.overview','move',1, '";
        echo $current_page_url;
        echo "');\">&nbsp;&gt; ";
        echo __("agenda next period");
        echo "</a>\n\t\t</p>\n\t\t";
    }
    echo "\t\t\n\t\t\n\t\t<p class=\"pos2\"><strong id=\"agendaperiodselect\" class=\"textsize1 pointer\" id=\"Status_text\" style=\"line-height: 22px\">\n\t\t";
    echo date("j", strtotime($start_date));
    echo "\t\t";
    echo __("month_" . date("n", strtotime($start_date)));
    echo "\t\t";
    echo date("Y", strtotime($start_date));
    echo " - \n\t\t";
    echo date("j", strtotime($end_date));
    echo "\t\t";
    echo __("month_" . date("n", strtotime($end_date)));
    echo "\t\t";
    echo date("Y", strtotime($end_date));
    echo "\t\t<span style=\"margin-top:3px;\" class=\"ico actionblock arrowdown mar2 pointer\">&nbsp;</span>\n\t\t</strong></p>\n\t\t\n\t";
}
echo "\t\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n<div style=\"position:relative;\">\n\t<div id=\"agendaperiod\" class=\"hide\">\n\t\t\n\t\t<label>";
echo __("choose period");
echo "</label>\n\t\t<select name=\"period\" class=\"text1 size1\">\n\t\t\t<option value=\"w\" ";
if($agenda->SelectedPeriod == "w") {
    echo "selected=\"selected\"";
}
echo ">";
echo strtolower(__("week"));
echo "</option>\n\t\t\t<option value=\"m\" ";
if($agenda->SelectedPeriod == "m") {
    echo "selected=\"selected\"";
}
echo ">";
echo strtolower(__("month"));
echo "</option>\n\t\t\t<option value=\"q\" ";
if($agenda->SelectedPeriod == "q") {
    echo "selected=\"selected\"";
}
echo ">";
echo strtolower(__("quarter"));
echo "</option>\n\t\t\t<option value=\"d\" ";
if(substr($agenda->SelectedPeriod, 0, 1) == "d") {
    echo "selected=\"selected\"";
}
echo ">";
echo __("custom period");
echo "</option>\n\t\t</select><br />\n\t\t<br />\n\t\t\n\t\t<div id=\"agendaperiod_custom\" ";
if(substr($agenda->SelectedPeriod, 0, 1) != "d") {
    echo "class=\"hide\"";
}
echo ">\n\t\t\n\t\t\t<label>&nbsp;</label>\n\t\t\t<input type=\"text\" class=\"text1 size6 datepicker_range\" id=\"start_date\" name=\"start_date\" value=\"";
echo rewrite_date_db2site($start_date);
echo "\" /> <span style=\"width:33px;display:inline-block;text-align:center;\">";
echo __("upto and including");
echo "</span> <input type=\"text\" class=\"text1 size6 datepicker_range\" id=\"end_date\" name=\"end_date\" value=\"";
echo rewrite_date_db2site($end_date);
echo "\" /><br />\n\t\t\t<br />\n\t\t\t<a id=\"agendaperiod_submit_btn\" class=\"c1 a1 float_right\">";
echo __("view period");
echo "</a>\n\t\t\n\t\t</div>\t\n\t</div>\n</div>\n\n\n<!--optionsbar-->\n<div class=\"optionsbar\">\n<!--optionsbar-->\n\t";
if(U_AGENDA_ADD) {
    echo "\t<p class=\"pos1\"><a class=\"agendaDialogNewItem button1 add_icon\"><span>";
    echo __("add agenda");
    echo "</span></a></p>\n\t";
}
echo "\t\n\t<p class=\"pos2\" style=\"position:static;\">";
if(isset($agenda->SearchString) && $agenda->SearchString) {
    echo "<a onclick=\"save('agenda.overview','search','', '";
    echo $current_page_url;
    echo "');\" class=\"sizenormal c1 a1 pointer\">";
    echo __("delete agenda search");
    echo "</a>";
} else {
    echo "<a onclick=\"save('agenda.overview','today','yes', '";
    echo $current_page_url;
    echo "');\" class=\"sizenormal c1 a1 pointer\">";
    echo __("go to today");
    echo "</a>";
}
echo " <span class=\"c_gray\"> | </span> <input type=\"text\" name=\"AgendaSearch\" value=\"\" placeholder=\"";
if(isset($agenda->SearchString) && $agenda->SearchString) {
    echo htmlspecialchars($agenda->SearchString);
} else {
    echo __("agenda search placeholder");
}
echo "\" class=\"text1 size1\"/>\n\t</p>\n\t\n<!--optionsbar-->\n</div>\n<!--optionsbar-->\n\n<br />\n\n<div id=\"agenda_container\">\n    <table id=\"agenda_table\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n    \t";
if(isset($agenda->SearchString) && $agenda->SearchString) {
    $array_counter = (int) $agenda_items["CountRows"];
    foreach ($agenda_items as $day => $tmp_items) {
        if(is_numeric($day)) {
            $day_counter = 0;
            $current_date = substr($day, 0, 4) . "-" . substr($day, 4, 2) . "-" . substr($day, 6, 2);
            $border_bottom = $array_counter === 0 || 1 < count($tmp_items) ? true : false;
            $html_current_date = "<span class=\"day\">" . date("j", strtotime($current_date)) . "</span><span class=\"monthyear\">" . ucfirst($array_months_short[date("n", strtotime($current_date))]) . "<br />" . date("Y", strtotime($current_date)) . "</span>";
            foreach ($tmp_items as $tmp_id => $tmp_item) {
                $day_counter++;
                $array_counter--;
                if($border_bottom && $day_counter == count($tmp_items) && 1 < $day_counter) {
                    $border_bottom = false;
                }
                if($day_counter == count($tmp_items) && $array_counter === 0) {
                    $border_bottom = true;
                }
                echo "    \t\t\t\t<tr class=\"";
                if($day_counter == 1) {
                    echo "border_top";
                }
                echo " ";
                if($border_bottom) {
                    echo "border_bottom";
                }
                echo " ";
                if($day == date("Ymd")) {
                    echo "agenda_today";
                }
                echo "\">\n    \t\t\t\t\t";
                if($day_counter == 1) {
                    echo "<td width=\"100\" class=\"col_day\"><a class=\"agendaDialogNewItem\" rel=\"";
                    echo rewrite_date_db2site($current_date);
                    echo "\">";
                    echo $html_current_date;
                    echo "</a></td>";
                } else {
                    echo "<td class=\"col_day_repeat\">&nbsp;</td>";
                }
                echo "    \t\t\t\t\t<td width=\"100\">";
                if($tmp_item["WholeDay"] == 1) {
                    echo __("whole day");
                } else {
                    echo substr($tmp_item["TimeFrom"], 0, 5) . " " . __("till") . " " . substr($tmp_item["TimeTill"], 0, 5);
                }
                echo "</td>\n    \t\t\t\t\t<td>\n    \t\t\t\t\t\t";
                if($tmp_item["ItemType"] == "domain") {
                    echo "<div class=\"agendatype_domain\">" . __("domain") . "</div>";
                } elseif($tmp_item["ItemType"] == "periodic") {
                    echo "<div class=\"agendatype_subscription\">" . __("subscription") . "</div>";
                }
                echo "    \t\t\t\t\t\t\n    \t\t\t\t\t\t";
                if(isset($tmp_item["EmailNotify"]) && 0 <= $tmp_item["EmailNotify"]) {
                    echo "    \t\t\t\t\t\t\t<span class=\"inline_status agenda_notify infopopuptop delaypopup\">&nbsp;<span class=\"popup\" style=\"left: -5px; top: -38px; display: none;\">";
                    echo sprintf(__("sent agenda notification x days to mailaddress y"), $tmp_item["EmailNotify"], $tmp_item["EmployeeMailAddress"] ? htmlspecialchars($tmp_item["EmployeeMailAddress"]) : htmlspecialchars($company->EmailAddress));
                    echo "<b style=\"top: 30px;\"></b></span></span>\n    \t\t\t\t\t\t";
                } elseif(isset($tmp_item["EmailNotify"]) && $tmp_item["EmailNotify"] == -2) {
                    echo "    \t\t\t\t\t\t\t<span class=\"inline_status agenda_notify infopopuptop delaypopup\">&nbsp;<span class=\"popup\" style=\"left: -5px; top: -38px; display: none;\">";
                    echo sprintf(__("agenda notification already sent to mailaddress y"), $tmp_item["EmployeeMailAddress"] ? htmlspecialchars($tmp_item["EmployeeMailAddress"]) : htmlspecialchars($company->EmailAddress));
                    echo "<b style=\"top: 30px;\"></b></span></span>\n    \t\t\t\t\t\t";
                }
                echo "    \t\t\t\t\t\t\n    \t\t\t\t\t\t";
                if($tmp_item["ItemType"] == "periodic") {
                    echo "<a href=\"services.php?page=show&id=" . $tmp_item["ItemID"] . "\" class=\"a1\">" . htmlspecialchars($tmp_item["Description"]) . "</a>";
                } else {
                    echo htmlspecialchars($tmp_item["Description"]);
                }
                echo "    \t\t\t\t\t\t\n    \t\t\t\t\t\t";
                if(isset($tmp_item["Employee"]) && 0 < $tmp_item["Employee"] && $tmp_item["Employee"] != $account->Identifier) {
                    echo " <font class=\"c_gray\">(" . __("agenda added by") . " " . htmlspecialchars($tmp_item["EmployeeName"]) . ")</font>";
                }
                echo "    \t\t\t\t\t\t\n    \t\t\t\t\t\t\n                            ";
                if(substr($tmp_id, 0, 1) != "D" && U_AGENDA_EDIT) {
                    echo "                                <span class=\"agenda_edit_btn float_right\"><a rel=\"";
                    echo $tmp_id;
                    echo "\" class=\"agendaDialogEditItem a1 c1\">";
                    echo __("edit");
                    echo "</a>\n                                    &nbsp;&nbsp;|&nbsp;&nbsp;\n                                <a rel=\"";
                    echo $tmp_id;
                    echo "\" class=\"agendaDialogDeleteItem a1 c1 alt1\">";
                    echo __("delete");
                    echo "</a></span>\n                            ";
                }
                echo "    \t\t\t\t\t</td>\n    \t\t\t\t</tr>\n    \t\t\t\t";
            }
        }
    }
    if((int) $agenda_items["CountRows"] === 0) {
        echo "<p>";
        echo __("no results found");
        echo "</p>";
    }
} else {
    $current_date = $start_date;
    while ($current_date <= $end_date) {
        $next_current_date = date("Y-m-d", strtotime("+1 day", strtotime($current_date)));
        $border_bottom = $current_date == $end_date || isset($agenda_items[str_replace("-", "", $current_date)]) && 1 < count($agenda_items[str_replace("-", "", $current_date)]) ? true : false;
        $html_current_date = "<span class=\"day\">" . date("j", strtotime($current_date)) . "</span><span class=\"monthyear\">" . ucfirst($array_months_short[date("n", strtotime($current_date))]) . "<br />" . date("Y", strtotime($current_date)) . "</span>";
        if(isset($agenda_items[str_replace("-", "", $current_date)]) && 0 < count($agenda_items[str_replace("-", "", $current_date)])) {
            $tmp_items = $agenda_items[str_replace("-", "", $current_date)];
            $day_counter = 0;
            foreach ($tmp_items as $tmp_id => $tmp_item) {
                $day_counter++;
                if($border_bottom && $day_counter == count($agenda_items[str_replace("-", "", $current_date)]) && 1 < $day_counter) {
                    $border_bottom = false;
                }
                if($day_counter == count($agenda_items[str_replace("-", "", $current_date)]) && $current_date == $end_date) {
                    $border_bottom = true;
                }
                echo "    \t\t\t\t\t<tr ";
                if(isset($show_today) && $show_today && $day_counter == 1 && $current_date == date("Y-m-d")) {
                    echo "id=\"today\"";
                }
                echo " class=\"";
                if($day_counter == 1) {
                    echo "border_top";
                }
                echo " ";
                if($border_bottom) {
                    echo "border_bottom";
                }
                echo " ";
                if($current_date == date("Y-m-d")) {
                    echo "agenda_today";
                }
                echo "\">\n    \t\t\t\t\t\t";
                if($day_counter == 1) {
                    echo "<td width=\"100\" class=\"col_day\" ><a class=\"agendaDialogNewItem\" rel=\"";
                    echo rewrite_date_db2site($current_date);
                    echo "\">";
                    echo $html_current_date;
                    echo "</a></td>";
                } else {
                    echo "<td class=\"col_day_repeat\"><a class=\"agendaDialogNewItem\" rel=\"";
                    echo rewrite_date_db2site($current_date);
                    echo "\">&nbsp;</a></td>";
                }
                echo "    \t\t\t\t\t\t<td width=\"100\">";
                if($tmp_item["WholeDay"] == 1) {
                    echo __("whole day");
                } else {
                    echo substr($tmp_item["TimeFrom"], 0, 5) . " " . __("till") . " " . substr($tmp_item["TimeTill"], 0, 5);
                }
                echo "</td>\n    \t\t\t\t\t\t<td>\n    \t\t\t\t\t\t\t";
                if($tmp_item["ItemType"] == "domain") {
                    echo "<div class=\"agendatype_domain\">" . __("domain") . "</div>";
                } elseif($tmp_item["ItemType"] == "periodic") {
                    echo "<div class=\"agendatype_subscription\">" . __("subscription") . "</div>";
                }
                echo "    \t\t\t\t\t\t\t\n    \t\t\t\t\t\t\t";
                if(isset($tmp_item["EmailNotify"]) && 0 <= $tmp_item["EmailNotify"]) {
                    echo "    \t\t\t\t\t\t\t\t<span class=\"inline_status agenda_notify infopopuptop delaypopup\">&nbsp;<span class=\"popup\" style=\"left: -5px; top: -38px; display: none;\">";
                    echo sprintf(__("sent agenda notification x days to mailaddress y"), $tmp_item["EmailNotify"], $tmp_item["EmployeeMailAddress"] ? htmlspecialchars($tmp_item["EmployeeMailAddress"]) : htmlspecialchars($company->EmailAddress));
                    echo "<b style=\"top: 30px;\"></b></span></span>\n    \t\t\t\t\t\t\t";
                } elseif(isset($tmp_item["EmailNotify"]) && $tmp_item["EmailNotify"] == -2) {
                    echo "    \t\t\t\t\t\t\t\t<span class=\"inline_status agenda_notify infopopuptop delaypopup\">&nbsp;<span class=\"popup\" style=\"left: -5px; top: -38px; display: none;\">";
                    echo sprintf(__("agenda notification already sent to mailaddress y"), $tmp_item["EmployeeMailAddress"] ? htmlspecialchars($tmp_item["EmployeeMailAddress"]) : htmlspecialchars($company->EmailAddress));
                    echo "<b style=\"top: 30px;\"></b></span></span>\n    \t\t\t\t\t\t\t";
                }
                echo "    \t\t\t\t\t\t\t\n    \t\t\t\t\t\t\t";
                if($tmp_item["ItemType"] == "periodic") {
                    echo "<a href=\"services.php?page=show&id=" . $tmp_item["ItemID"] . "\" class=\"a1\">" . htmlspecialchars($tmp_item["Description"]) . "</a>";
                } else {
                    echo htmlspecialchars($tmp_item["Description"]);
                }
                echo "    \t\t\t\t\t\t\t\n    \t\t\t\t\t\t\t";
                if(isset($tmp_item["Employee"]) && 0 < $tmp_item["Employee"] && $tmp_item["Employee"] != $account->Identifier) {
                    echo " <font class=\"c_gray\">(" . __("agenda added by") . " " . htmlspecialchars($tmp_item["EmployeeName"]) . ")</font>";
                }
                echo "    \t\t\t\t\t\t\t\n    \t\t\t\t\t\t\t";
                if(substr($tmp_id, 0, 1) != "D" && U_AGENDA_EDIT) {
                    echo "                                    <span class=\"agenda_edit_btn float_right\"><a rel=\"";
                    echo $tmp_id;
                    echo "\" class=\"agendaDialogEditItem a1 c1\">";
                    echo __("edit");
                    echo "</a>\n                                        &nbsp;&nbsp;|&nbsp;&nbsp;\n                                    <a rel=\"";
                    echo $tmp_id;
                    echo "\" class=\"agendaDialogDeleteItem a1 c1 alt1\">";
                    echo __("delete");
                    echo "</a></span>\n                                ";
                }
                echo "    \t\t\t\t\t\t</td>\n    \t\t\t\t\t</tr>\n    \t\t\t\t\t";
            }
        } else {
            echo "    \t\t\t\t<tr ";
            if(isset($show_today) && $show_today && $current_date == date("Y-m-d")) {
                echo "id=\"today\"";
            }
            echo " class=\"border_top ";
            if($border_bottom) {
                echo "border_bottom";
            }
            echo " ";
            if($current_date == date("Y-m-d")) {
                echo "agenda_today";
            }
            echo "\">\n    \t\t\t\t\t<td width=\"100\" class=\"col_day\"><a class=\"agendaDialogNewItem\" rel=\"";
            echo rewrite_date_db2site($current_date);
            echo "\">";
            echo $html_current_date;
            echo "</a></td>\n    \t\t\t\t\t<td width=\"100\">&nbsp;</td>\n    \t\t\t\t\t<td>&nbsp;</td>\n    \t\t\t\t</tr>\n    \t\t\t\t";
        }
        $current_date = $next_current_date;
    }
}
echo "    </table>\n</div>\n\n<div id=\"agenda_dialog_container\"></div>\n\n<div id=\"agendaDialogDeleteItem\" class=\"hide \" title=\"";
echo __("remove agenda item");
echo "\">\n\t<input type=\"hidden\" name=\"agendaId\" value=\"0\" />\n\t";
echo __("are you sure you want to delete this agenda item");
echo "<br />\n\t<br />\n    <p><a class=\"agendaCenterHeight a1 c1 float_right\" onclick=\"\$('#agendaDialogDeleteItem').dialog('close');\"><span >";
echo __("cancel");
echo "</span></a></p>\n\t<p><a id=\"agenda_dialog_remove_btn\" class=\"button1 alt1 float_left\"><span>";
echo __("remove");
echo "</span></a></p><div class=\"agendaCenterHeight\" id=\"agendaRemoveStatus\"></div>\n\t\n\t</form>\n</div>\n\n\n";
require_once "views/footer.php";

?>