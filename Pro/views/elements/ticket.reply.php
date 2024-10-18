<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "<!--form-->\n<form id=\"TicketForm\" name=\"form_create\" method=\"post\" action=\"?page=addmessage\" enctype=\"multipart/form-data\"><fieldset><legend>";
echo __("reply on ticket");
echo "</legend>\n<!--form-->\n<input type=\"hidden\" name=\"id\" value=\"";
echo $ticket_id;
echo "\" />\n\n<!--box1-->\n<div class=\"box2 ticket_reply_div ";
if(0 < $ticket->LockEmployee && $ticket->LockEmployee != $account->Identifier) {
    echo "hide";
}
echo "\">\n<!--box1-->\n\n\t<!--top-->\n\t<div class=\"top\">\n\t<!--top-->\n\t\t<strong><a class=\"reply_on_ticket_div";
if(empty($ticket->LockEmployee) || $ticket->LockEmployee != $account->Identifier) {
    echo " a1 ico inline add";
}
echo "\">";
echo __("reply on ticket");
echo "</a></strong>\n\t<!--top-->\n\t</div>\n\t<!--top-->\n\n\t<!--content-->\n\t<div id=\"ticket_reply_show\" class=\"content ";
if(empty($ticket->LockEmployee) || $ticket->LockEmployee != $account->Identifier) {
    echo "hide";
}
echo "\">\n\t<!--content-->\n\t\n\t\t<div id=\"ticket_lock_status\" ";
if(empty($ticket->LockEmployee)) {
    echo "class=\"hide\"";
}
echo ">\n\t\t\t";
if(0 < $ticket->LockEmployee) {
    echo sprintf(__("ticket is locked for you since y"), rewrite_date_db2site($ticket->LockDate, "%H:%i:%s"));
    echo "<a id=\"ticket_unlock_self\" class=\"a1 c1 pointer float_right\">" . __("ticket is locked for me, release now") . "</a>";
    echo "<br /><br />";
}
echo "\t\t</div>\n\t\t\n\t\t<div id=\"ticket_textarea_div\">\n\t\t\t<textarea class=\"ckeditor_2\" cols=\"80\" id=\"message\" name=\"Message\" rows=\"10\">";
echo isset($ticketmessage->Message) ? $ticketmessage->Message : "";
echo "</textarea>\n\t\t\t<br />\n\t\t\t\n\t\t\t<strong style=\"display:block;width: 80px;float:left;\">";
echo __("ticket attachments");
echo "</strong> \n\t\t\t<div id=\"attachment_div\" style=\"margin-left: 90px;\">\n\t\t\t\t<div></div>\n\t\t\t\t<a class=\"a1 c1 ico inline add upload_file\" id=\"attachment_add\">";
echo __("add attachment");
echo "</a>\n\t\t\t</div>\n\t\t\t\n\t\t\t<div style=\"float:right;text-align: right;\">\n\t\t        ";
if(isset($ticket->CC) && !empty($ticket->CC)) {
    echo "\t\t            <label for=\"ticket_sent_reply_to_cc\">";
    echo __("sent reply to CC");
    echo "</label><input type=\"checkbox\" name=\"ticket_sent_reply_to_cc\" value=\"yes\" id=\"ticket_sent_reply_to_cc\" checked=\"checked\" /><br /><br /><br />\n\t\t        ";
}
echo "\t\t\t\t<label for=\"ticket_close_after_reply\">";
echo __("close ticket after reply");
echo "</label><input type=\"checkbox\" name=\"ticket_close_after_reply\" value=\"yes\" id=\"ticket_close_after_reply\" /><br /><br />\n\t\t\t\t\t\n\t\t\t\t<a class=\"button1 alt1 float_right\" id=\"form_create_btn\"><span>";
echo __("reply on ticket btn");
echo "</span></a>\n\t\t\t</div>\n\t\t\t\n\t\t\t<br clear=\"both\" />\t\n\t\n\t\t</div>\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\n<!--box1-->\n</div>\n<!--box1-->\n\t\n<!--form-->\n</fieldset></form>\n<!--form-->";

?>