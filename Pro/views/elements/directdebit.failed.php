<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "<div id=\"dialog_directdebit_failed\" class=\"hide\" title=\"";
echo __("confirm action");
echo "\">\n\t<form method=\"post\" action=\"invoices.php?page=show&action=faileddirectdebit&id=";
echo $invoice->Identifier;
echo "\" name=\"SDD_failed\">\n\t";
echo __("sdd actiondialog single failed title");
echo "<br />\n\t<br />\n\t\n\t<strong>";
echo __("sdd actiondialog failed reason");
echo "</strong><br />\n \t<input name=\"Reason\" type=\"text\" class=\"text1 size1\" /><br />\n \t<br />\n\t\n\t<strong>";
echo __("sdd actiondialog failed which action to take");
echo "</strong><br />\n\t<label><input type=\"radio\" name=\"InvoiceAction\" value=\"move_next\" checked=\"checked\"/> ";
echo __("sdd actiondialog single failed action move");
echo "</label><br />\n \t<label><input type=\"radio\" name=\"InvoiceAction\" value=\"remove_invoice\" /> ";
echo __("sdd actiondialog single failed action remove from invoice");
echo "</label><br />\n\t<label><input type=\"radio\" name=\"InvoiceAction\" value=\"remove_debtor\" /> ";
echo __("sdd actiondialog single failed action remove from debtor");
echo "</label><br />\n\t<br />\n \t\n \t<strong>";
echo __("sdd actiondialog debtor notification failed direct debit");
echo "</strong><br />\n \t<label><input type=\"checkbox\" name=\"NotifyDebtor\" value=\"yes\"/> ";
echo __("sdd actiondialog notify debtor failed direct debit");
echo "</label><br />\n \t\n \t<div class=\"notify_debtor_div hide\"> \n\t \t<br />\n\t \t<strong>";
echo __("sdd actiondialog debtor notification email failed direct debit");
echo "</strong><br />\n\t \t<select class=\"text1 size4f\" name=\"NotifyMail\">\n\t \t\t<option value=\"\">";
echo __("please choose");
echo "</option>\n\t\t\t";
foreach ($emailtemplates as $k => $v) {
    if(is_numeric($k)) {
        echo "\t\t\t<option value=\"";
        echo $k;
        echo "\" ";
        if(SDD_FAILED_MAIL == $k) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo $v["Name"];
        echo "</option>\n\t\t\t";
    }
}
echo "\t\t</select><br />\n\t</div>\n\t\n\t<br />\n\t\n\t<p><a class=\"button1 alt1 preventDoubleClick float_left\" id=\"dialog_directdebit_failed_btn\"><span>";
echo __("proceed");
echo "</span></a></p>\n\t\n\t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#dialog_directdebit_failed').dialog('close');\"><span>";
echo __("cancel");
echo "</span></a></p>\n\t\n\t</form>\n</div>\n<script type=\"text/javascript\">\n\$(function(){\n    \$(document).on('click', 'input[name=\"NotifyDebtor\"]', function(){\n\t\tif(\$(this).prop('checked')){\n\t\t\t\$('.notify_debtor_div').removeClass('hide');\n\t\t}else{\n\t\t\t\$('.notify_debtor_div').addClass('hide');\n\t\t}\n\t});\n});\n</script>";

?>