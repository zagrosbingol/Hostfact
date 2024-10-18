<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
if($form_action == "approve") {
    echo "\t<div class=\"buttonbar\">\n\t\t<p class=\"pos1\">\n\t\t\t<a class=\"a1 c1\" onclick=\"\$('#decline_modification').dialog('open');\">\n\t\t\t\t<span>";
    echo __("decline modification");
    echo "</span>\n\t\t\t</a>\n\t\t</p>\n\t\t<p class=\"pos2\">\n\t\t\t<a onclick=\"document.clientarea_change_form.submit();\" class=\"button1 alt1 pointer\">\n\t\t\t\t<span>";
    echo __("accept modification");
    echo "</span>\n\t\t\t</a>\n\t\t</p>\n\t</div>\n\t";
} elseif($form_action == "editandexecute") {
    echo "\t<div class=\"buttonbar\">\n\t\t<p class=\"pos1\">\n\t\t\t<a class=\"a1 c1\" onclick=\"\$('#cancel_modification').dialog('open');\">\n\t\t\t\t<span>";
    echo __("clientarea change cancel and no execute");
    echo "</span>\n\t\t\t</a>\n\t\t</p>\n\t\t<p class=\"pos2\">\n\t\t\t<a onclick=\"document.clientarea_change_form.submit();\" class=\"button1 alt1 pointer\">\n\t\t\t\t<span>";
    echo __("clientarea change edit and execute");
    echo "</span>\n\t\t\t</a>\n\t\t</p>\n\t</div>\n\t";
} elseif($form_action == "editandmanuallyexecute") {
    echo "\t<div class=\"buttonbar\">\n\t\t<p class=\"pos1\">\n\t\t\t<a class=\"a1 c1\" onclick=\"\$('#cancel_modification').dialog('open');\">\n\t\t\t\t<span>";
    echo __("clientarea change cancel and no execute");
    echo "</span>\n\t\t\t</a>\n\t\t</p>\n\t\t<p class=\"pos2\">\n\t\t\t<a onclick=\"document.clientarea_change_form.submit();\" class=\"button1 alt1 pointer\">\n\t\t\t\t<span>";
    echo __("clientarea change edit and manually execute");
    echo "</span>\n\t\t\t</a>\n\t\t</p>\n\t</div>\n\t";
}
if($form_action == "approve") {
    echo "\t<div id=\"decline_modification\" class=\"hide\" title=\"";
    echo __("decline modification");
    echo "\">\n\t\t<form id=\"DeclineModificationForm\" name=\"form_decline\" method=\"post\" action=\"clientareachanges.php?page=reject&amp;id=";
    echo $ClientareaChange->id;
    echo "\">\n\n\t\t\t<p>\n\t\t\t\t<strong>";
    echo __("confirm action");
    echo "</strong><br />\n\t\t\t\t";
    echo __("are you sure to decline this modification?");
    echo "\t\t\t</p>\n\t\t\t<br />\n\n\t\t\t<p><a class=\"button1 alt1 float_left\" onclick=\"document.form_decline.submit();\"><span>";
    echo __("decline");
    echo "</span></a></p>\n\t\t\t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#decline_modification').dialog('close');\"><span>";
    echo __("cancel");
    echo "</span></a></p>\n\t\t</form>\n\t</div>\n\t";
} elseif($form_action == "editandexecute" || $form_action == "editandmanuallyexecute") {
    echo "\t<div id=\"cancel_modification\" class=\"hide\" title=\"";
    echo __("clientarea change cancel modification");
    echo "\">\n\t\t<form id=\"CancelModificationForm\" name=\"form_cancel\" method=\"post\" action=\"clientareachanges.php?page=cancel&amp;id=";
    echo $ClientareaChange->id;
    echo "\">\n\n\t\t\t<p>\n\t\t\t\t<strong>";
    echo __("confirm action");
    echo "</strong><br />\n\t\t\t\t";
    echo __("are you sure to cancel this modification?");
    echo "\t\t\t</p>\n\t\t\t<br />\n\n\t\t\t<p><a class=\"button1 alt1 float_left\" onclick=\"document.form_cancel.submit();\"><span>";
    echo __("clientarea change cancel modification");
    echo "</span></a></p>\n\t\t\t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#cancel_modification').dialog('close');\"><span>";
    echo __("cancel");
    echo "</span></a></p>\n\t\t</form>\n\t</div>\n\t";
}

?>