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
echo isset($message) ? $message : "";
echo "\n<!--form-->\n<form id=\"MailingForm\" name=\"form_create\" method=\"post\" action=\"debtors.php?page=mailing\">\n<!--form-->\n\n<!--heading1-->\n<div class=\"heading1\">\n<!--heading1-->\n\n\t<h2>";
echo __("new mailing");
echo "</h2>\n\n\t<input type=\"hidden\" value=\"";
echo $current_page_url;
echo "\" id=\"current_url\" />\n\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n\t<!--box1-->\n\t<div class=\"box2\">\n\t<!--box1-->\n\t\n\t\t<!--top-->\n\t\t<div class=\"top\">\n\t\t<!--top-->\n\t\t\n\t\t\t<strong>";
echo __("mailingtab general");
echo "</strong>\n\t\t\t\n\t\t<!--top-->\n\t\t</div>\n\t\t<!--top-->\n\n\t\t<!--content-->\n\t\t<div class=\"content\" style=\"padding:20px 20px 0 20px;\">\n\t\t<!--content-->\n\n\t\t\t<!--split2-->\n\t\t\t<div class=\"split2\">\n\t\t\t<!--split2-->\n\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t<div class=\"left\">\n\t\t\t\t<!--left-->\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("mailing");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t<div class=\"float_left\">\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("mailing template");
echo "</strong>\n\t\t\t\t\t\t\t<select class=\"text1 size4\" name=\"Template\" onchange=\"GetTemplateInformation(this.value);\">\n\t\t                        <option value=\"\">";
echo __("no mailing template select");
echo "</option>\n\t\t                        <optgroup label=\"";
echo __("mailtemplate");
echo "\">\n\t\t\t                    ";
foreach ($templates as $key => $value) {
    if(is_numeric($key)) {
        $selected = $key == $mail->Template ? "selected=\"selected\"" : "";
        echo "<option value=\"" . $key . "\" " . $selected . ">" . $value["Name"] . "</option>";
    }
}
echo "\t\t                        </optgroup>\n\t\t                    </select>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<br style=\"clear:both;\" /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<div class=\"float_left\">\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("mailing subject");
echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size11\" name=\"Subject\" value=\"";
echo isset($mail->Subject) ? $mail->Subject : "";
echo "\" />\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<br style=\"clear:both;\" /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<div class=\"float_left\">\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("mailing sendername");
echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size10\" name=\"SenderName\" value=\"";
echo isset($mail->SenderName) ? $mail->SenderName : "";
echo "\" />\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<div class=\"float_left width2\">\n\t\t\t\t\t\t\t&nbsp;\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<div class=\"float_left\">\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("mailing senderemailaddress");
echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size10\" name=\"SenderEmail\" value=\"";
echo isset($mail->SenderEmail) ? $mail->SenderEmail : "";
echo "\" />\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<div class=\"clear\"></div>\n\t\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t</div>\n\t\t\t\t<!--left-->\n\t\t\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t<div class=\"right\">\n\t\t\t\t<!--right-->\n\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("mailing");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t<div class=\"float_left\">\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("cc");
echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size12\" name=\"CarbonCopy\" value=\"";
echo isset($mail->CarbonCopy) ? check_email_address($mail->CarbonCopy, "convert", ", ") : "";
echo "\" />\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<br style=\"clear:both;\" /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<div class=\"float_left\">\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("bcc");
echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size12\" name=\"BlindCarbonCopy\" value=\"";
echo isset($mail->BlindCarbonCopy) ? check_email_address($mail->BlindCarbonCopy, "convert", ", ") : "";
echo "\" />\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<br style=\"clear:both;\" /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<div class=\"float_left\">\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("standard bcc");
echo "</strong>\n\t\t\t\t\t\t\t<input type=\"checkbox\" value=\"1\" id=\"only_once\" name=\"only_once\" ";
if(isset($mail->only_once) && $mail->only_once == 1) {
    echo "checked=\"checked\"";
}
echo " /> <label for=\"only_once\">";
echo __("send bcc once");
echo "</label>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<div class=\"clear\"></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t</div>\n\t\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t<!--split2-->\n\t\t\t</div>\n\t\t\t<!--split2-->\n\t\t\t\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\n\t\t<div class=\"content\" style=\"padding:0 20px 20px 20px;\">\n\t\t\t\n\t\t\t<a class=\"button1 alt1\" id=\"add_recipient_link\"><span>";
echo __("mailing add recipient");
echo "</span></a>\n\t\t\t<span class=\"floatr mar4\"><i>";
echo __("total");
echo ": <span id=\"recipient_total\"><span id=\"recipient_count\">";
echo count($debtors) - 1;
echo "</span></span></i></span>\n\t\t\t\n\t\t\t<div id=\"SubTable_Recipients\">\n\t\t\t\t<table id=\"MainTable_Recipients\" class=\"table1\" cellpadding=\"0\" cellspacing=\"0\">\n\t\t\t\t\t<tr class=\"trtitle\">\n\t\t\t\t\t\t<th scope=\"col\" style=\"width:120px;\"><label><input name=\"DebtorBatch\" class=\"BatchCheck\" type=\"checkbox\" value=\"on\" /></label> <a onclick=\"ajaxSave('mailing.add','sort','DebtorCode','Recipients','";
echo $current_page_url;
echo "');\" class=\"ico set2 pointer ";
if($_SESSION["mailing.add"]["sort"] == "DebtorCode") {
    if($_SESSION["mailing.add"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("debtor no");
echo "</a></th>\n\t\t\t\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('mailing.add','sort','CompanyName','Recipients','";
echo $current_page_url;
echo "');\" class=\"ico set2 pointer ";
if($_SESSION["mailing.add"]["sort"] == "CompanyName") {
    if($_SESSION["mailing.add"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("companyname");
echo "</a></td>\n\t\t\t\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('mailing.add','sort','SurName','Recipients','";
echo $current_page_url;
echo "');\" class=\"ico set2 pointer ";
if($_SESSION["mailing.add"]["sort"] == "ContactPerson") {
    if($_SESSION["mailing.add"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("contactperson");
echo "</a></th>\n\t\t\t\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('mailing.add','sort','EmailAddress','Recipients','";
echo $current_page_url;
echo "');\" class=\"ico set2 pointer ";
if($_SESSION["mailing.add"]["sort"] == "EmailAddress") {
    if($_SESSION["mailing.add"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("emailaddress");
echo "</a></th>\n\t\t\t\t\t\t<th width=\"20\">&nbsp;</th>\n\t\t\t\t\t</tr>\n\t\t\t\t\t";
$debtorCounter = 0;
$showed = 0;
foreach ($debtors as $key => $value) {
    if(is_numeric($key)) {
        $debtorCounter++;
        echo "\t\t\t\t\t<tr class=\"";
        if($debtorCounter % 2 === 1) {
            echo "tr1";
        }
        echo "\">\n\t\t\t\t\t\t<td style=\"vertical-align: top;\"><input name=\"id[]\" type=\"checkbox\" class=\"DebtorBatch\" value=\"";
        echo $value["id"];
        echo "\" /> <a href=\"debtors.php?page=show&id=";
        echo $value["id"];
        echo "\" class=\"c1 a1\">";
        echo $value["DebtorCode"];
        echo "</a></td>\n\t\t\t\t\t\t<td style=\"vertical-align: top;\">";
        echo $value["CompanyName"];
        echo "</td>\n\t\t\t\t\t\t<td style=\"vertical-align: top;\">";
        echo $value["SurName"] . ", " . $value["Initials"];
        echo "</td>\n\t\t\t\t\t\t<td>";
        echo check_email_address($value["EmailAddress"], "convert", ", ");
        echo "</td>\n\t\t\t\t\t\t<td align=\"right\">\n\t\t\t\t\t\t\t<img src=\"images/ico_trash.png\" rel=\"";
        echo $value["id"];
        echo "\" class=\"removeSingleDebtor hide pointer\"/>\n\t\t\t\t\t\t</td>\n\t\t\t\t\t</tr>\n\t\t\t\t\t";
    }
}
if($debtorCounter === 0) {
    echo "\t\t\t\t\t<tr class=\"hover_extra_info\" id=\"norecipients\">\n\t\t\t\t\t\t<td colspan=\"4\"><i>";
    echo __("mailing no recipients");
    echo "</i></td>\n\t\t\t\t\t</tr>\n\t\t\t\t\t";
}
echo "\t\t\t\t</table>\n\t\t\t</div>\n\t\t\t\n\t\t\t<input type=\"hidden\" name=\"Recipients\" />\n\t\t\t\n\t\t\t<p class=\"ico inline hook\">\n\t\t\t\t<select id=\"removeDebtors\" class=\"select1\">\n\t\t\t\t\t<option value=\"first\" selected=\"selected\">";
echo __("with selected");
echo "</option>\n\t\t\t\t\t<option value=\"removeDebtors\">";
echo __("remove from list");
echo "</option>\n\t\t\t\t</select>\n\t\t\t</p>\n\t\t\t\n\t\t\t<br />\n\t\t\t\n\t\t\t\t\t</div>\n\n\t<!--box1-->\n\t</div>\n\t<!--box1-->\n\t\n\t<br />\n\t\n\t<!--box1-->\n\t<div class=\"box2\" style=\"float:right;width:350px;\">\n\t<!--box1-->\n\t\n\t\t<!--top-->\n\t\t<div class=\"top\">\n\t\t<!--top-->\n\t\t\n\t\t\t<strong>";
echo __("templateblock edit - available variables");
echo "</strong>\n\t\t\t\n\t\t<!--top-->\n\t\t</div>\n\t\t<!--top-->\n\n\t\t<!--content-->\n\t\t<div class=\"content\">\n\t\t<!--content-->\n\n\t\t\t\n\t\t\t<div id=\"variable_search\"><input type=\"text\" name=\"SearchVariable\" class=\"text1 size1\"/></div>\n\t\t\t\n\t\t\t<br />\n\t\t\t\n\t\t\t";
$variable_list_show_email = true;
$variable_list_show_invoice = false;
$variable_list_show_pricequote = false;
$variable_list_show_services = false;
$variable_list_show_subscriptions = false;
$variable_list_show_ticket = false;
include_once "views/elements/variables_list.php";
echo "\t\t\t\n\t\n\t\t\t<script type=\"text/javascript\">\n\t\t\t\$(function(){\n\t\t\t\t \$('#variable_list').accordion({autoHeight: false, animate: false});\n\t\t\t});\n\t\t\t</script>\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\n\t<!--box1-->\n\t</div>\n\t<!--box1-->\n\n\t<!--box1-->\n\t<div class=\"box2\" style=\"margin-right:360px;\">\n\t<!--box1-->\n\n\t\n\t\t<!--top-->\n\t\t<div class=\"top\">\n\t\t<!--top-->\n\t\t\n\t\t\t<strong>";
echo __("mailing message");
echo "</strong>\n\n\t\t<!--top-->\n\t\t</div>\n\t\t<!--top-->\n\n\t\t<!--content-->\n\t\t<div class=\"content\">\n\t\t<!--content-->\n\t\t\t\n\t\t\t<div id=\"attachmentbox\">\n\t\t\t\t<span id=\"attachment_files\">\n\t\t\t\t";
if(!empty($current_template->Attachment) && !empty($current_template->Attachment[0])) {
    echo "\t\t\t\t<img src=\"images/attach.png\" style=\"float:left; margin:0 10px 0 10px;\" />\n\t\t\t\t";
}
echo "\t\t\t\t\n\t\t\t\t";
if(isset($attachments)) {
    foreach ($attachments as $file => $filename) {
        if(in_array($file, $current_template->Attachment)) {
            echo "<span class=\"div_attachment\" id=\"" . $file . "\"><div class=\"ico inline file_" . getFileType($filename, true) . "\">&nbsp;</div>" . $filename . "</span>&nbsp;&nbsp;&nbsp;";
        }
    }
}
echo "\t    \t\t";
if(isset($attach2)) {
    foreach ($attach2 as $file => $filename) {
        if(in_array($file, $current_template->Attachment)) {
            echo "<span class=\"div_attachment\" id=\"" . $file . "\"><div class=\"ico inline file_" . getFileType($filename, true) . "\">&nbsp;</div>" . $filename . "</span>&nbsp;&nbsp;&nbsp;";
        }
    }
}
echo "\t    \t\t</span>\n    \t\t</div>\n    \t\t<input type=\"hidden\" name=\"Attachment\" id=\"Attachment\" />\n\t\t\t\n\t\t\t<textarea class=\"ckeditor\" cols=\"80\" id=\"Message\" name=\"Message\" rows=\"10\">";
echo isset($mail->Message) ? $mail->Message : "";
echo "</textarea>\n\t\t\t\t\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\t\t\n\n\t<!--box1-->\n\t</div>\n\t<!--box1-->\n\t\n\t<!--buttonbar-->\n\t<div class=\"buttonbar\">\n\t<!--buttonbar-->\n\t\t<br />\n\t\t<input type=\"hidden\" id=\"DirectSend\" name=\"DirectSend\" value=\"no\" />\n\t\t<p class=\"pos1\"><a onclick=\"\$('#DirectSend').val('yes'); document.form_create.submit();\" class=\"button1 send_icon\"><span>";
echo __("mailing send mailing");
echo "</span></a></p>\n\t\t\n\t<!--buttonbar-->\n\t</div>\n\t<!--buttonbar-->\n\n\t<br clear=\"both\" />\n\n\n<!--form-->\n</form>\n<!--form-->\n\n<div id=\"add_recipients\" title=\"";
echo __("mailing dialog add emailaddresses");
echo "\" class=\"hide\">\n\t\n\t<table cellpadding=\"0\" cellspacing=\"0\" style=\"line-height:34px;\">\n\t\t<tr>\n\t\t\t<td width=\"150\"><strong>";
echo __("debtorgroup");
echo "</strong></td>\n\t\t\t<td>\n\t\t\t\t<select name=\"DebtorGroup\" class=\"text1 size1\" onchange=\"selectDebtorGroup(this.value); this.value='';\">\n\t\t\t\t\t<option value=\"\">";
echo __("please choose");
echo "</option>\n\t\t\t\t\t<optgroup label=\"";
echo __("mailing dialog select debtorgroup standard");
echo "\">\n\t\t\t\t\t\t<option value=\"all\">&nbsp;&nbsp; ";
echo __("mailing dialog select all debtors");
echo "</option>\n\t\t\t\t\t</optgroup>\n\t\t\t\t\t<optgroup label=\"";
echo __("mailing dialog select debtorgroup own");
echo "\">\n\t\t\t\t\t\t";
foreach ($debtorgroups as $groupID => $debtorGroup) {
    if(is_numeric($groupID)) {
        echo "\t\t\t\t\t\t<option value=\"";
        echo $groupID;
        echo "\">&nbsp;&nbsp; ";
        echo $debtorGroup["GroupName"];
        echo "</option>\n\t\t\t\t\t\t";
    }
}
echo "\t\t\t\t\t</optgroup>\n\t\t\t\t</select>\n\t\t\t</td>\n\t\t</tr>\n\t\t<tr>\n\t\t\t<td><strong>";
echo __("subscription for product");
echo "</strong></td>\n\t\t\t<td>\n\t\t\t\t<input type=\"hidden\" name=\"ProductGroup\"/>\n\t\t\t\t";
createAutoComplete("product", "ProductGroup", "", ["class" => "size1", "return_type" => "code"]);
echo "\t\t\t</td>\n\t\t</tr>\n\t\t<tr>\n\t\t\t<td><strong>";
echo __("server");
echo "</strong></td>\n\t\t\t<td>\n\t\t\t\t<select name=\"ServerGroup\" class=\"text1 size1\" onchange=\"selectServer(this.value); this.value='';\">\n\t\t\t\t\t<option value=\"\">";
echo __("please choose");
echo "</option>\n\t\t\t\t\t";
foreach ($servers as $key => $serv) {
    if(is_numeric($key)) {
        echo "\t\t\t\t\t\t<option value=\"";
        echo $key;
        echo "\">";
        echo $serv["Name"];
        echo "</option>\n\t\t\t\t\t";
    }
}
echo "\t\t\t    </select>\n\t\t\t</td>\n\t\t</tr>\n\t</table>\n\t\n\t<strong class=\"title\">";
echo __("mailing dialog debtor list");
echo ":</strong>\n\t";
echo __("mailing dialog debtor list explain");
echo "<br />\n\t<br />\n\t<input type=\"text\" name=\"SearchDebtor\" value=\"";
echo $_SESSION["recipients.add"]["searchfor"];
echo "\" style=\"float: left; margin-top: 2px;\" class=\"size1 text1\" onkeypress=\"if(event.keyCode == 13){ ajaxSave('recipients.add','searchfor',\$('input[name=SearchDebtor]').val(),'DebtorSearch','";
echo $current_page_url;
echo "'); }\"/> <p style=\"margin-left: 15px; float: left;\"><a class=\"button1 alt1 float_left\"  onclick=\"ajaxSave('recipients.add','searchfor',\$('input[name=SearchDebtor]').val(),'DebtorSearch','";
echo $current_page_url;
echo "');\"><span>";
echo __("search");
echo "</span></a></p>\n\t<br clear=\"both\" />\n\t\n\t<form>\n\t\n\t<div id=\"SubTable_DebtorSearch\">\n\t\n\t<div class=\"loading_red hide\" id=\"no_emailaddress\" style=\"position:absolute;\"><br />";
echo __("selected debtor has no emailaddress");
echo "<br /></div>\n\t\n\t<table id=\"MainTable_DebtorSearch\" class=\"table1\" cellpadding=\"0\" cellspacing=\"0\">\n\t<tr class=\"trtitle\">\n\t\t<th scope=\"col\" style=\"min-width: 96px;\"><label><input name=\"DebtorSearchBatch\" class=\"BatchCheck\" type=\"checkbox\" value=\"on\" /></label> <a onclick=\"ajaxSave('recipients.add','sort','DebtorCode','DebtorSearch','";
echo $current_page_url;
echo "');\" class=\"ico set2 pointer ";
if($_SESSION["recipients.add"]["sort"] == "DebtorCode") {
    if($_SESSION["recipients.add"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("debtor no");
echo "</a></th>\n\t\t<th scope=\"col\"><a onclick=\"ajaxSave('recipients.add','sort','CompanyName','DebtorSearch','";
echo $current_page_url;
echo "');\" class=\"ico set2 pointer ";
if($_SESSION["recipients.add"]["sort"] == "CompanyName") {
    if($_SESSION["recipients.add"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("companyname");
echo "</a></td>\n\t\t<th scope=\"col\"><a onclick=\"ajaxSave('recipients.add','sort','SurName','DebtorSearch','";
echo $current_page_url;
echo "');\" class=\"ico set2 pointer ";
if($_SESSION["recipients.add"]["sort"] == "SurName") {
    if($_SESSION["recipients.add"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("contactperson");
echo "</a></th>\n\t\t<th scope=\"col\"><a onclick=\"ajaxSave('recipients.add','sort','EmailAddress','DebtorSearch','";
echo $current_page_url;
echo "');\" class=\"ico set2 pointer ";
if($_SESSION["recipients.add"]["sort"] == "EmailAddress") {
    if($_SESSION["recipients.add"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("emailaddress");
echo "</a></th>\n\t\t<th scope=\"col\"><a onclick=\"ajaxSave('recipients.add','sort','Mailing','DebtorSearch','";
echo $current_page_url;
echo "');\" class=\"ico set2 pointer ";
if($_SESSION["recipients.add"]["sort"] == "Mailing") {
    if($_SESSION["recipients.add"]["order"] == "ASC") {
        echo "arrowup";
    } else {
        echo "arrowdown";
    }
} else {
    echo "arrowhover";
}
echo "\">";
echo __("mailing");
echo "?</a></th>\n\t</tr>\n\t";
$recipientCounter = 0;
foreach ($recipients_add as $key => $recipient) {
    if(is_numeric($key)) {
        $recipientCounter++;
        echo "\t<tr class=\"hover_extra_info ";
        if($recipientCounter % 2 === 1) {
            echo "tr1";
        }
        echo "\">\n\t\t<td style=\"vertical-align: top;\"><input name=\"debtorid[]\" type=\"checkbox\" class=\"DebtorSearchBatch\" value=\"";
        echo $recipient["id"];
        echo "\"  />";
        echo $recipient["DebtorCode"];
        echo "</td>\n\t\t<td style=\"vertical-align: top;\">";
        echo $recipient["CompanyName"] ? $recipient["CompanyName"] : "-";
        echo "</td>\n\t\t<td style=\"vertical-align: top;\">";
        echo $recipient["SurName"] . ", " . $recipient["Initials"];
        echo "</td>\n\t\t<td style=\"vertical-align: top;word-break: break-all;\">\n\t\t";
        echo isset($recipient["EmailAddress"]) && $recipient["EmailAddress"] != "" ? check_email_address($recipient["EmailAddress"], "convert", ", ") : "<i>" . __("no emailaddress") . "</i>";
        echo "</td>\n\t\t<td style=\"vertical-align: top;\">";
        echo $array_mailingoptin[$recipient["Mailing"]];
        echo "</td>\n\t</tr>\n\t";
    }
}
if($recipientCounter === 0) {
    echo "\t<tr>\n\t\t<td colspan=\"5\">\n\t\t\t";
    echo __("no results found");
    echo "\t\t</td>\n\t</tr>\n\t";
} else {
    echo "\t<tr>\n\t\t<td colspan=\"5\">\n\t\t\t";
    ajax_paginate("DebtorSearch", isset($recipients_add["CountRows"]) ? $recipients_add["CountRows"] : 0, $_SESSION["recipients.add"]["results"], $_SESSION["recipients.add"]["limit"], $current_page_url, false);
    echo "\t\n\t\t</td>\n\t</tr>\n\t";
}
echo "\t</table>\n\t\n\t</div>\n\t\n\t\n\t<br /><br />\n\t\n\t<p><a class=\"button1 alt1 float_right\" id=\"selectDebtors\"><span>";
echo __("select");
echo "</span></a></p>\n\t\n\t</form>\n</div>\n";
require_once "views/footer.php";

?>