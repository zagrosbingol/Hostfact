<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "<div id=\"agendaDialog\" title=\"";
echo 0 < $agenda_id ? __("edit agendaitem") : __("create agendaitem");
echo "\">\n    <form method=\"post\" name=\"AgendaForm\" action=\"agenda.php?page=";
if(0 < $agenda_id) {
    echo "edit";
} else {
    echo "add";
}
echo "\">\n        ";
if(0 < $agenda_id) {
    echo "<input type=\"hidden\" name=\"id\" value=\"";
    echo $agenda_id;
    echo "\" />";
}
echo "        \n        <strong class=\"title2\" style=\"line-height:30px;\">";
echo __("date");
echo "</strong>\n    \t<span class=\"title2_value\">\n    \t\t<input type=\"text\" name=\"Date\" class=\"text1 size6 datepicker\" tabindex=\"-1\" value=\"";
echo 0 < $agenda_id ? $agenda->Date : rewrite_date_db2site(date("Y-m-d"));
echo "\" />\n    \t</span>\n    \t\n    \t<strong class=\"title2\" style=\"line-height:30px;\">";
echo __("time");
echo "</strong>\n    \t<div class=\"title2_value timetype_time ";
if($agenda_id <= 0 || $agenda->WholeDay == 1) {
    echo "hide";
}
echo "\">\n    \t\t<input type=\"text\" name=\"TimeFrom\" class=\"text1 size3 timechecker\" value=\"";
echo 0 < $agenda_id ? $agenda->TimeFrom : "";
echo "\"/> &nbsp;";
echo __("till");
echo "&nbsp; <input type=\"text\" name=\"TimeTill\" class=\"text1 size3 timechecker\" value=\"";
echo 0 < $agenda_id ? $agenda->TimeTill : "";
echo "\" />\n    \t</div>\n    \t<strong class=\"title2 timetype_time ";
if($agenda_id <= 0 || $agenda->WholeDay == 1) {
    echo "hide";
}
echo "\" style=\"line-height:30px;\">&nbsp;</strong>\n    \t<div class=\"title2_value\">\n    \t\t<input type=\"checkbox\" id=\"WholeDay\" name=\"WholeDay\" value=\"1\" ";
if($agenda_id <= 0 || $agenda->WholeDay == 1) {
    echo "checked=\"checked\"";
}
echo "/> <label for=\"WholeDay\">";
echo __("takes the whole day");
echo "</label>\n    \t</div>\n    \t\n    \t<strong class=\"title2\" style=\"line-height:30px;\">";
echo __("description");
echo "</strong>\n    \t<span class=\"title2_value\">\n    \t\t<textarea name=\"Description\" class=\"text1 size11\" rows=\"5\">";
echo 0 < $agenda_id ? $agenda->Description : "";
echo "</textarea>\n    \t</span>\n    \t\n    \t<strong class=\"title2\" style=\"line-height:30px;\">";
echo __("agenda reminder");
echo "</strong>\n    \t<span class=\"title2_value\" style=\"line-height:30px;\">\n    \t\t";
if(0 < $agenda_id && 0 <= $agenda->EmailNotify && $agenda->Employee != $account->Identifier) {
    echo "    \t\t\t";
    echo sprintf(__("another employee will already receive a notification"), $agenda->EmployeeName, $agenda->EmployeeMailAddress);
    echo "    \t\t";
} else {
    echo "    \t\t\t<input type=\"checkbox\" id=\"EmailNotify\" name=\"EmailNotify\" value=\"1\" ";
    if(0 < $agenda_id && 0 <= $agenda->EmailNotify) {
        echo "checked=\"checked\"";
    }
    echo "/> <label for=\"EmailNotify\">";
    echo sprintf(__("do sent me a reminder per mail for agenda item"), isset($account->EmailAddress) && $account->EmailAddress ? $account->EmailAddress : $company->EmailAddress);
    echo "</label><br />\n    \t\t\t<span id=\"emailnotify_days\" ";
    if($agenda_id <= 0 || $agenda->EmailNotify < 0) {
        echo "class=\"hide\"";
    }
    echo "><input type=\"text\" name=\"EmailNotifyDays\" class=\"text1 size3\" value=\"";
    echo 0 < $agenda_id && 0 <= $agenda->EmailNotify ? $agenda->EmailNotify : "0";
    echo "\" /> ";
    echo __("days on beforehand");
    echo "</span>\t\t\n    \t\t";
}
echo "    \t</span>\n    \t\n    \t<br />\n        <p><a class=\"agendaCenterHeight a1 c1 alt1 float_right\" onclick=\"\$('#agendaDialog').dialog('close');\"><span>";
echo __("cancel");
echo "</span></a></p>\n    \t<p><a id=\"agenda_dialog_btn\" class=\"button1 alt1 float_left\"><span>";
echo __("save");
echo "</span></a><div class=\"agendaCenterHeight\" id=\"agendaEditStatus\"></div></p>\n    \t  \n    </form>\n</div>\n";

?>