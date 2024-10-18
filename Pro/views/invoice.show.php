<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "views/header.php";
echo "\t<div class=\"white_overlay hide\"></div>\n<!--right-->\n<div class=\"right\">\n<!--right-->\n\t<ul class=\"list1\">\n    \n\t\t";
if(U_INVOICE_EDIT && !((int) $invoice->Status === 0 && $invoice->SubStatus == "BLOCKED")) {
    echo "            <li>\n                <a class=\"ico set1 send ";
    if(0 < $invoice->InvoiceMethod) {
        echo "printQuestion";
    }
    echo "\" \n                   href=\"invoices.php?page=show&action=sentinvoice&id=";
    echo $invoice->Identifier;
    echo "\">\n                    ";
    echo __("action send");
    echo "                </a>\n            </li>\n            ";
}
echo "\t\t\n        <li><a class=\"ico set1 print  ";
if((int) $invoice->Status === 0) {
    echo " large_actionname";
}
echo " printQuestion\" href=\"invoices.php?page=show&action=print&id=";
echo $invoice->Identifier;
echo "\">";
echo (int) $invoice->Status === 0 ? __("action print draft invoice") : __("action print");
echo "</a></li>\n\t\t\n\t\t";
if(U_INVOICE_EDIT) {
    echo "\t\t\t";
    if($invoice->SubStatus != "PAUSED" && ($invoice->Status == 2 || $invoice->Status == 3) && substr(rewrite_date_site2db($invoice->PayBefore), 0, 8) < date("Ymd")) {
        echo "\t\t\t<li><a class=\"ico set1 reminder pointer\" onclick=\"\$('#dialog_invoice_reminder').dialog('open');\">";
        echo __("action reminder");
        echo "</a></li>\n\t\t\t";
    }
    echo "\t\t\t";
    if(INT_SUPPORT_SUMMATIONS === true) {
        echo "\t\t \t\t";
        if($invoice->SubStatus != "PAUSED" && ($invoice->Status == 2 || $invoice->Status == 3) && substr(rewrite_date_site2db($invoice->PayBefore), 0, 8) < date("Ymd") && INVOICE_REMINDER_NUMBER <= $invoice->Reminders) {
            echo "\t\t\t\t<li><a class=\"ico set1 summation pointer\" onclick=\"\$('#dialog_invoice_summation').dialog('open');\">";
            echo __("action summation");
            echo "</a></li>\n\t\t\t\t";
        }
        echo "\t\t\t";
    }
    echo "\t\t\t\n\t\t\n\t\t\t";
    if($invoice->Status == 2 || $invoice->Status == 3) {
        if($invoice->Authorisation == "yes" && $invoice->TransactionID) {
            echo "\t\t\t\t\t<li><a class=\"ico set1 paid\" onclick=\"\$('#dialog_paid_confirm').dialog('open');\">";
            echo __("action authorisation successfull");
            echo "</a></li>\n\t\t\t\t\t\n\t\t\t\t\t";
            if(SDD_ID && $invoice->SDDBatchID) {
                $include_sdd_dialog = true;
                echo "<li><a class=\"ico set1 pointer unpaid\" onclick=\"\$('#dialog_directdebit_failed').dialog('open');\">";
                echo __("authorisation failed");
                echo "</a></li>";
            }
            echo "\t\t\t\t\t\n\t\t\t\t";
        } else {
            echo "\t\t\t\t\t<li><a class=\"ico set1 paid\"  onclick=\"\$('#dialog_paid_confirm').dialog('open');\">";
            echo __("action paid");
            echo "</a></li>\n\t\t\t\t";
        }
        echo "\t\t\t";
    } elseif($invoice->Status == 4) {
        if($invoice->Authorisation == "yes") {
            if(SDD_ID && $invoice->SDDBatchID) {
                $include_sdd_dialog = true;
                echo "<li><a class=\"ico set1 pointer unpaid\" onclick=\"\$('#dialog_directdebit_failed').dialog('open');\">";
                echo __("authorisation failed");
                echo "</a></li>";
            } else {
                echo "<li><a class=\"ico set1 unpaid\" href=\"invoices.php?page=show&action=markasunpaid&id=";
                echo $invoice->Identifier;
                echo "\">";
                echo __("authorisation failed");
                echo "</a></li>";
            }
            echo "\t\t\t\t\t\n\t\t\t\t";
        } else {
            echo "\t\t\t\t\t<li><a class=\"ico set1 unpaid\" href=\"invoices.php?page=show&action=markasunpaid&id=";
            echo $invoice->Identifier;
            echo "\">";
            echo __("action unpaid");
            echo "</a></li>\n\t\t\t\t";
        }
        echo "\t\t\t";
    }
    echo "\n            ";
    if($invoice->Status == "0" && $invoice->SentDate != "0000-00-00 00:00:00" && $invoice->InvoiceMethod == "0" && $invoice->SentDate <= date("Y-m-d H:i:s", strtotime("+1 hour"))) {
        echo "\t\t\t\t<li><a onclick=\"\$('#dialog_edit_scheduled').dialog('open');\" class=\"ico set1 editdocument\">";
        echo __("action edit");
        echo "</a></li>\n                ";
    } elseif(!in_array($invoice->Status, [2, 3, 4, 9])) {
        echo "\t\t\t\t<li><a class=\"ico set1 editdocument\" href=\"invoices.php?page=edit&id=";
        echo $invoice->Identifier;
        echo "\">";
        echo __("action edit");
        echo "</a></li>\n            ";
    }
    echo "\t\t\t\t\n\t\t";
}
echo "\t\t\t\n\t\t";
if(U_INVOICE_DELETE) {
    echo "\t\t\t";
    if((int) $invoice->Status === 0 && substr($invoice->InvoiceCode, 0, 8) != "[concept" || 1 <= $invoice->Status && $invoice->Status < 8) {
        echo "\t\t\t\t<li><a class=\"ico set1 credit\" onclick=\"\$('#credit_invoice').dialog('open');\">";
        echo isset($invoice->Elements["CountRows"]) && 2 <= $invoice->Elements["CountRows"] ? __("action partly credit") : __("action credit");
        echo "</a></li>\n\t\t\t";
    } elseif((int) $invoice->Status === 0 && substr($invoice->InvoiceCode, 0, 8) == "[concept") {
        echo "\t\t\t\t<li><a class=\"ico set1 decline\" onclick=\"\$('#credit_invoice').dialog('open');\">";
        echo __("action delete");
        echo "</a></li>\n\t\t\t";
    }
    echo "\t\t";
}
$collectionActionButton = do_filter("invoice_show_additional_action_button", [], ["invoice" => $invoice]);
$extraDialog = generate_action_button($collectionActionButton);
echo "\t</ul> ";
if(0 < strlen($extraDialog)) {
    echo $extraDialog;
}
if(U_INVOICE_EDIT) {
    echo " \n\t\n\t\t";
    if(SDD_ID && isset($include_sdd_dialog) && $include_sdd_dialog) {
        include_once "views/elements/directdebit.failed.php";
    }
    echo "\t\t\n\t\t";
    if(($invoice->Status == 2 || $invoice->Status == 3) && substr(rewrite_date_site2db($invoice->PayBefore), 0, 8) < date("Ymd")) {
        echo "\t\t<div id=\"dialog_invoice_reminder\" title=\"";
        echo __("dialog invoice reminder");
        echo "\" class=\"hide";
        if(isset($_GET["open"]) && $_GET["open"] == "reminder") {
            echo " autoopen";
        }
        echo "\">\n\t\t\t<form name=\"form_summation\" method=\"post\" action=\"invoices.php?page=show&action=sentreminder&id=";
        echo $invoice->Identifier;
        echo "\">\n\t\t\t<strong>";
        echo __("dialog invoice reminder");
        echo "</strong><br />\n\t\t\t<input type=\"radio\" id=\"dialog_reminder_invoicemethod_email\" name=\"radio_send_invoicemethod\" value=\"email\" ";
        if((int) $invoice->InvoiceMethod === 0) {
            echo "checked=\"checked\"";
        }
        echo "/> <label for=\"dialog_reminder_invoicemethod_email\">";
        echo __("dialog invoice reminder option1");
        echo "</label><br />\n\t\t\t<input type=\"radio\" id=\"dialog_reminder_invoicemethod_post\" name=\"radio_send_invoicemethod\" class=\"design_div_toggle\" value=\"post\" ";
        if($invoice->InvoiceMethod == 1) {
            echo "checked=\"checked\"";
        }
        echo "/> <label for=\"dialog_reminder_invoicemethod_post\">";
        echo __("dialog invoice reminder option2");
        echo "</label><br />\n\t\t\t<input type=\"radio\" id=\"dialog_reminder_invoicemethod_both\" name=\"radio_send_invoicemethod\" class=\"design_div_toggle\" value=\"both\" ";
        if(1 < $invoice->InvoiceMethod) {
            echo "checked=\"checked\"";
        }
        echo "/> <label for=\"dialog_reminder_invoicemethod_both\">";
        echo __("dialog invoice reminder option3");
        echo "</label><br />\n\t\t\t\n\t\t\t<div class=\"design_div ";
        if((int) $invoice->InvoiceMethod === 0) {
            echo "hide";
        }
        echo "\">\n\t\t\t\t<br />\n\t\t\t\t<strong>";
        echo __("dialog template design title");
        echo "</strong><br />\n\t\t\t\t<input type=\"radio\" id=\"dialog_reminder_printtype_download\" name=\"printtype\" value=\"download\" checked=\"checked\"/> <label for=\"dialog_reminder_printtype_download\">";
        echo __("dialog template design option1");
        echo "</label><br />\n\t\t\t\t<input type=\"radio\" id=\"dialog_reminder_printtype_print\" name=\"printtype\" value=\"print\"/> <label for=\"dialog_reminder_printtype_print\">";
        echo __("dialog template design option2");
        echo "</label><br />\n\t\t\t</div>\n\t\t\t\n\t\t\t<br />\n\t\t\t<p><a class=\"button1 alt1 float_left\" id=\"reminder_btn\"><span>";
        echo __("dialog template design process");
        echo "</span></a></p>\n\t\t\t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#dialog_invoice_reminder').dialog('close');\"><span>";
        echo __("cancel");
        echo "</span></a></p>\n\t\t\t</form>\n\t\t</div>\n\t\t";
    }
    if(INT_SUPPORT_SUMMATIONS === true && ($invoice->Status == 2 || $invoice->Status == 3) && substr(rewrite_date_site2db($invoice->PayBefore), 0, 8) < date("Ymd") && INVOICE_REMINDER_NUMBER <= $invoice->Reminders) {
        echo "\t\t<div id=\"dialog_invoice_summation\" title=\"";
        echo __("dialog invoice summation");
        echo "\" class=\"hide";
        if(isset($_GET["open"]) && $_GET["open"] == "summation") {
            echo " autoopen";
        }
        echo "\">\n\t\t\t<form name=\"form_summation\" method=\"post\" action=\"invoices.php?page=show&action=sentsummation&id=";
        echo $invoice->Identifier;
        echo "\">\n\t\t\t<strong>";
        echo __("dialog invoice summation");
        echo "</strong><br />\n\t\t\t<input type=\"radio\" id=\"dialog_summation_invoicemethod_email\" name=\"radio_send_invoicemethod\" value=\"email\" ";
        if((int) $invoice->InvoiceMethod === 0) {
            echo "checked=\"checked\"";
        }
        echo "/> <label for=\"dialog_summation_invoicemethod_email\">";
        echo __("dialog invoice summation option1");
        echo "</label><br />\n\t\t\t<input type=\"radio\" class=\"design_div_toggle\" id=\"dialog_summation_invoicemethod_post\" name=\"radio_send_invoicemethod\" value=\"post\" ";
        if($invoice->InvoiceMethod == 1) {
            echo "checked=\"checked\"";
        }
        echo " /> <label for=\"dialog_summation_invoicemethod_post\">";
        echo __("dialog invoice summation option2");
        echo "</label><br />\n\t\t\t<input type=\"radio\" class=\"design_div_toggle\" id=\"dialog_summation_invoicemethod_both\" name=\"radio_send_invoicemethod\" value=\"both\" ";
        if(1 < $invoice->InvoiceMethod) {
            echo "checked=\"checked\"";
        }
        echo " /> <label for=\"dialog_summation_invoicemethod_both\">";
        echo __("dialog invoice summation option3");
        echo "</label><br />\n\t\t\t\n\t\t\t<div class=\"design_div ";
        if((int) $invoice->InvoiceMethod === 0) {
            echo "hide";
        }
        echo "\">\n\t\t\t\t<br />\n\t\t\t\t<strong>";
        echo __("dialog template design title");
        echo "</strong><br />\n\t\t\t\t<input type=\"radio\" id=\"dialog_summation_printtype_download\" name=\"printtype\" value=\"download\" checked=\"checked\"/> <label for=\"dialog_summation_printtype_download\">";
        echo __("dialog template design option1");
        echo "</label><br />\n\t\t\t\t<input type=\"radio\" id=\"dialog_summation_printtype_print\" name=\"printtype\" value=\"print\"/> <label for=\"dialog_summation_printtype_print\">";
        echo __("dialog template design option2");
        echo "</label><br />\t\t\t\t\t\t\t\t\t\t\t\t\n\t\t\t</div>\n\t\t\t\n\t\t\t<br />\n\t\t\t<p><a class=\"button1 alt1 float_left\" id=\"summation_btn\"><span>";
        echo __("dialog template design process");
        echo "</span></a></p>\n\t\t\t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#dialog_invoice_summation').dialog('close');\"><span>";
        echo __("cancel");
        echo "</span></a></p>\n\t\t\t</form>\n\t\t</div>\n\t\t";
    }
    echo "\n\t\t\t ";
    if($invoice->Status == "0" && $invoice->SentDate != "0000-00-00 00:00:00" && $invoice->InvoiceMethod == "0" && $invoice->SentDate <= date("Y-m-d H:i:s", strtotime("+1 hour"))) {
        echo "\t\t\t\t <div id=\"dialog_edit_scheduled\" class=\"hide\" title=\"";
        echo __("edit scheduled draft invoice title");
        echo "\">\n\t\t\t\t\t <strong>";
        echo __("confirm your action");
        echo "</strong><br />\n\n                     ";
        echo sprintf(__("edit scheduled draft invoice description"), rewrite_date_db2site($invoice->SentDate) . " " . __("at") . " " . rewrite_date_db2site($invoice->SentDate, "%H:%i"));
        echo "<br /><br />\n\n\n\t\t\t\t\t <p>\n\t\t\t\t\t\t <a class=\"button1 alt1 float_left\" href=\"invoices.php?page=edit&id=";
        echo $invoice->Identifier;
        echo "\">\n\t\t\t\t\t\t\t <span>";
        echo __("edit");
        echo "</span>\n\t\t\t\t\t\t </a>\n\t\t\t\t\t </p>\n\t\t\t\t\t <p>\n\t\t\t\t\t\t <a class=\"c1 a1 float_right\" onclick=\"\$('#dialog_edit_scheduled').dialog('close');\">\n\t\t\t\t\t\t\t <span>";
        echo __("cancel");
        echo "</span>\n\t\t\t\t\t\t </a>\n\t\t\t\t\t </p>\n\t\t\t\t </div>\n                 ";
    }
    echo "\n\t";
}
echo " \n\t<hr />\n\t\n\t";
echo $message;
echo "\t\n\t<!--heading1-->\n\t<div class=\"heading1\">\n\t<!--heading1-->\n\t\t\n\t\t<h2>\n            ";
echo __("invoice");
echo " \n            ";
echo $invoice->InvoiceCode;
echo " \n            ";
if($invoice->Authorisation == "yes") {
    echo __("invoice authed");
}
echo "            ";
if(($invoice->Paid == 1 || $invoice->Paid == 2) && ($invoice->Status == 1 || (int) $invoice->Status === 0)) {
    echo __("invoice show invoice is paid");
}
echo "        </h2>\n\t\t\n\t\t<p class=\"pos2\"><strong class=\"textsize1\">\n\t\t";
if($invoice->Status == "4") {
    echo "<span class=\"ico inline check\">";
    echo __("invoice status paid");
    if($invoice->PayDate != "") {
        echo " (" . $invoice->PayDate . ")";
    }
    echo "</span>";
} elseif($invoice->Status == "2" || $invoice->Status == "3") {
    if($invoice->SubStatus == "PAUSED") {
        $pauseFilter = __("invoice status collection");
        echo do_filter("invoice_show_pause_status", $pauseFilter, ["Identifier" => $invoice->Identifier]);
    } elseif($invoice->Authorisation == "yes") {
        if($invoice->TransactionID) {
            echo 1 < $invoice->AuthTrials ? sprintf(__("invoice status waiting incasso trials"), $invoice->AuthTrials) : __("invoice status waiting incasso");
        } else {
            echo 1 <= $invoice->AuthTrials ? sprintf(__("invoice status open for incasso trials"), $invoice->AuthTrials + 1) : __("invoice status open for incasso");
        }
    } elseif(date("Ymd") <= substr(rewrite_date_site2db($invoice->PayBefore), 0, 8)) {
        echo __("invoice status unpaid");
    } elseif((int) $invoice->Summations === 0 && ((int) $invoice->Reminders === 0 || $invoice->Reminders < INVOICE_REMINDER_NUMBER && date("Y-m-d", strtotime("+ " . INVOICE_REMINDER_TERM . " day", strtotime($invoice->ReminderDate))) < date("Y-m-d"))) {
        echo "<a class=\"ico inline sendemail\" onclick=\"\$('#dialog_invoice_reminder').dialog('open');\"><em>";
        echo str_replace("{count}", $invoice->Reminders + 1, __("invoice status send reminder"));
        echo "</em></a>";
    } elseif((int) $invoice->Summations === 0 && date("Y-m-d") <= date("Y-m-d", strtotime("+ " . INVOICE_REMINDER_TERM . " day", strtotime($invoice->ReminderDate)))) {
        echo str_replace("{count}", $invoice->Reminders, __("invoice status reminder sent"));
    } elseif(INT_SUPPORT_SUMMATIONS && ((int) $invoice->Summations === 0 || date("Y-m-d", strtotime("+ " . INVOICE_SUMMATION_TERM . " day", strtotime($invoice->SummationDate))) < date("Y-m-d") && $invoice->Summations < INVOICE_SUMMATION_NUMBER)) {
        echo "<a class=\"ico inline sendemail\" onclick=\"\$('#dialog_invoice_summation').dialog('open');\"><em>";
        echo str_replace("{count}", $invoice->Summations + 1, __("invoice status send summation"));
        echo "</em></a>";
    } elseif(INT_SUPPORT_SUMMATIONS && (date("Y-m-d") <= date("Y-m-d", strtotime("+ " . INVOICE_SUMMATION_TERM . " day", strtotime($invoice->SummationDate))) || INVOICE_SUMMATION_NUMBER <= $invoice->Summations)) {
        echo str_replace("{count}", $invoice->Summations, __("invoice status summation sent"));
    } else {
        echo __("invoice status unpaid");
    }
} else {
    echo $array_invoicestatus[$invoice->Status];
}
echo "\t\t</strong></p>\n\t\n\t<!--heading1-->\n\t</div><hr />\n\t<!--heading1-->\n\t\n\t<!--split4-->\n\t<div class=\"split4\" style=\"padding-right: 0px;\">\n\t<!--split4-->\n\t\n\t\t<div class=\"box5\" style=\"min-height:570px;padding:0px; margin-right: 200px; overflow: visible;\">\n\t\n\t\t<!--invoicedata-->\n\t\t<div class=\"invoicedata\" style=\"margin-right: -200px;\">\n\t\t<!--invoicedata-->\n\t\t\t\n\t\t\t<strong style=\"font-size: 12px;\">";
echo __("additional options");
echo "</strong><br />\n\t\t\t";
$invoice_show_additional_options = [];
if(U_INVOICE_EDIT) {
    if(in_array($invoice->Status, [2, 3, 4, 9])) {
        $invoice_show_additional_options["edit"] = ["href" => "invoices.php?page=edit&id=" . $invoice->Identifier, "title" => __("edit")];
    }
    if(($invoice->Status == 2 || $invoice->Status == 3) && 0 < deformat_money($invoice->PartPayment)) {
        if($invoice->SubStatus == "") {
            $invoice_show_additional_options["pause"] = ["onclick" => "\$('#dialog_paymentprocess').dialog('open');", "title" => __("additional option title pause")];
        } elseif($invoice->SubStatus == "PAUSED") {
            $invoice_show_additional_options["activate"] = ["onclick" => "\$('#dialog_paymentprocess').dialog('open');", "title" => __("additional option title reactivate")];
        }
    }
    if($invoice->Status == 2 || $invoice->Status == 3) {
        $invoice_show_additional_options["partpayment"] = ["onclick" => "\$('#invoicedialog_partpayment').dialog('open');", "title" => __("partpaymentdialog title")];
    }
    if(!empty($invoice->Attachment)) {
        $invoice_show_additional_options["clone"] = ["onclick" => "\$('#invoicedialog_clone').dialog('open');", "title" => __("clone invoice")];
    } else {
        $invoice_show_additional_options["clone"] = ["href" => "invoices.php?page=add&clone=" . $invoice->Identifier, "title" => __("clone invoice")];
    }
    if($invoice->Status == "0" && $invoice->SentDate != "0000-00-00 00:00:00") {
        $invoice_show_additional_options["schedule_draft_sending"] = ["onclick" => "\$('#dialog_schedule').dialog('open');", "title" => __("undo schedule draft sending")];
    } elseif((int) $invoice->Status === 0) {
        if($invoice->SubStatus == "") {
            $invoice_show_additional_options["block"] = ["onclick" => "\$('#dialog_block').dialog('open');", "title" => __("block send")];
        } elseif((int) $invoice->Status === 0 && $invoice->SubStatus == "BLOCKED") {
            $invoice_show_additional_options["block"] = ["onclick" => "\$('#dialog_block').dialog('open');", "title" => __("unblock send")];
        }
        $invoice_show_additional_options["schedule_draft_sending"] = ["onclick" => "\$('#dialog_schedule').dialog('open');", "title" => __("schedule draft sending")];
    }
}
$invoice_show_additional_options = do_filter("invoice_show_additional_options", $invoice_show_additional_options, ["InvoiceCode" => $invoice->InvoiceCode, "id" => $invoice->Identifier]);
if(!empty($invoice_show_additional_options)) {
    echo "<ul>";
    foreach ($invoice_show_additional_options as $key => $additional_options) {
        echo "<li>&bull; <a class=\"c1 a1\"" . (isset($additional_options["onclick"]) ? " onclick=\"" . $additional_options["onclick"] . "\"" : "") . (isset($additional_options["href"]) ? " href=\"" . $additional_options["href"] . "\"" : "") . ">" . $additional_options["title"] . "</a></li>";
    }
    echo "</ul><br />";
}
if((int) $invoice->Status === 0) {
    echo "\t\t\t\t<strong>";
    echo __("invoice options term");
    echo "</strong><br />\n\t\t\t\t<span>";
    echo $invoice->Term;
    echo " ";
    echo __("days");
    echo "</span>\n\t\t\t\t<br /><br />\n\t\t\t";
} elseif($invoice->Status < 4) {
    echo "\t\t\t\t<strong>";
    echo __("last paymentdate");
    echo "</strong><br />\n\t\t\t\t";
    if(date("Ymd") <= substr(rewrite_date_site2db($invoice->PayBefore), 0, 8) || $invoice->Status < 2 || 4 <= $invoice->Status) {
        echo "\t\t\t\t\t<span>";
        echo $invoice->PayBefore;
        echo " (";
        echo $invoice->Term;
        echo " ";
        echo __("days");
        echo ")</span>\n\t\t\t\t";
    } else {
        echo "\t\t\t\t\t<span class=\"c6\">";
        echo $invoice->PayBefore;
        echo " (";
        echo $invoice->Term;
        echo " ";
        echo __("days");
        echo ")</span>\n\t\t\t\t";
    }
    echo "\t\t\t\t<br /><br />\n\t\t\t";
}
echo "\t\t\t\n\t\t\t<strong>";
echo __("invoice properties");
echo "</strong><br />\t\n\t\t\t";
echo $invoice->Sent;
echo __("times sent");
echo "\t\t\t";
if($invoice->Status != 8) {
    echo "<br />\n\t\t\t\t";
    echo $invoice->Reminders;
    echo __("times reminder");
    echo "\t\t\t\t";
    if(INT_SUPPORT_SUMMATIONS) {
        echo "<br />";
        echo $invoice->Summations;
        echo __("times summation");
    }
    echo "\t\t\t";
}
echo "\t\t\t\n\t\t\t<br /><br />\n\n\t\t\t";
if(0 < $invoice->CorrespondingInvoice) {
    echo "\t\t\t\t<strong>";
    echo __("corresponding invoice");
    echo "</strong><br />\n\t\t\t\t<a href=\"invoices.php?page=show&id=";
    echo $invoice->CorrespondingInvoice;
    echo "\" class=\"a1 c1\">";
    echo $correspondingInvoice->InvoiceCode;
    echo "</a>\n\t\t\t\t<br /><br />\n\t\t\t\t";
}
echo "\n\t\t\t<strong>";
echo __("invoice options invoicemethod");
echo "</strong><br />\n\t\t\t";
echo $array_invoicemethod[$invoice->InvoiceMethod];
echo " ";
if(U_INVOICE_EDIT) {
    echo "<a onclick=\"\$('#invoicedialog_invoicemethod').dialog('open');\" class=\"pointer c1\">";
    echo __("change");
    echo "</a>";
}
echo "\t\t\t";
echo (int) $invoice->InvoiceMethod === 0 || $invoice->InvoiceMethod == 3 ? "<span class=\"sidebar_hooksmall\">" . str_replace("||", "</span><span class=\"sidebar_hooksmall\">", check_email_address($invoice->EmailAddress, "convert", "||")) . "</span>" : "<br />";
echo "\t\t\t\n\t\t\t<br />\n\t\t\t\n\t\t\t";
if($invoice->Authorisation == "yes" && $invoice->SDDBatchID) {
    echo "\t\t\t\t<strong>";
    echo __("information about direct debit");
    echo "</strong><br />\n\t\t\t\t";
    echo __("sdd batch id");
    echo ": <a href=\"directdebit.php?page=show&amp;id=";
    echo $invoice->SDDBatchID;
    echo "\" class=\"a1 c1\">";
    echo $invoice->SDDBatchID;
    echo "</a><br />\n\t\t\t\t";
    echo __("sdd direct debit date");
    echo ": ";
    echo $invoice->DirectDebitDate;
    echo "<br />\n\t\t\t\t";
    if($invoice->PayDate && $invoice->Status == 4) {
        echo __("paid at") . " " . $invoice->PayDate . "<br />";
    }
    echo "\t\t\t\t<br />\n\t\t\t";
} elseif($invoice->TransactionID || $invoice->Status == 4) {
    echo "\t\t\t\t<strong>";
    echo __("information about payment");
    echo "</strong><br />\n\t\t\t\t";
    if($invoice->PayDate && $invoice->Status == 4) {
        echo __("paid at") . " " . $invoice->PayDate . "<br />";
    }
    echo "\t\t\t\t";
    echo __("paid via");
    echo " ";
    echo $invoice->PaymentMethodName ? $invoice->PaymentMethodName : (isset($array_paymentmethod[$invoice->PaymentMethod]) ? $array_paymentmethod[$invoice->PaymentMethod] : $array_paymentmethod["wire"]);
    echo "<br />\n\t\t\t\t";
    if($invoice->TransactionID) {
        echo "\t\t\t\t\t";
        echo __("transaction id");
        echo ": ";
        echo $invoice->TransactionID;
        echo "<br />\n\t\t\t\t\t";
        echo __("transaction status");
        echo ": ";
        echo 1 <= $invoice->Paid ? __("online transaction status ok") : __("online transaction status open");
        echo "<br />\n\t\t\t\t\t";
        if(U_INVOICE_EDIT && isEmptyFloat($invoice->Paid) && $invoice->Status != 4) {
            echo "\t\t\t\t\t\t<a href=\"invoices.php?page=show&amp;id=";
            echo $invoice->Identifier;
            echo "&amp;action=cancelonlinepayment\" class=\"c1 a1\">";
            echo __("cancel online payment");
            echo "</a><br />\n\t\t\t\t\t";
        }
        echo "\t\t\t\t";
    }
    echo "\t\t\n\t\t\t\t<br />\n\t\t\t";
}
echo "\t\t\t\n\t\t\t<strong>";
echo __("history");
echo "</strong>\n\t\t\t<hr />\n\t\t\t";
$logcounter = 0;
foreach ($history as $k => $value) {
    if($logcounter == 3 || !is_numeric($k)) {
        if(isset($history["CountRows"]) && 0 < $history["CountRows"]) {
            echo "\t\t\t\t<p class=\"align_center\"><a onclick=\"\$('#invoicedialog_history').dialog('open');\" class=\"c1 a1\">";
            echo __("more");
            echo "</a></p>\n\t\t\t\t";
        } elseif(!isset($history["CountRows"]) || (int) $history["CountRows"] === 0) {
            echo "\t\t\t\t<p>";
            echo __("no history available");
            echo "</p>\n\t\t\t\t";
        }
        echo "\t\t\t<br />\n\t\t\n\t\t\n\t\t<!--invoicedata-->\n\t\t</div>\n\t\t<!--invoicedata-->\n\n\t\t<!--box5-->\n\t\t<div style=\"padding: 20px 30px 0px 30px;\">\n\t\t<!--box5-->\n\t\t\t\n\t\t\t<!--split3-->\n\t\t\t<div class=\"split3\">\n\t\t\t<!--split3-->\n\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t<div class=\"left\">\n\t\t\t\t<!--left-->\n\t\t\t\t\n\t\t\t\t\t<!--back1-->\n\t\t\t\t\t<div class=\"noback\" style=\"margin-bottom: 0px;\">\n\t\t\t\t\t<!--back1-->\n\n\t\t\t\t\t\t<p>\n\t\t\t\t\t\t\t";
        if($invoice->CompanyName) {
            echo "<strong><a href=\"debtors.php?page=show&amp;id=";
            echo $invoice->Debtor;
            echo "\" class=\"c1 a1\">";
            echo $invoice->CompanyName;
            echo "</a></strong><br />";
        }
        echo "\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t";
        if(!$invoice->CompanyName) {
            echo "<a href=\"debtors.php?page=show&amp;id=";
            echo $invoice->Debtor;
            echo "\" class=\"c1 a1\">";
        }
        echo "\t\t\t\t\t\t\t";
        echo $invoice->Initials . " " . $invoice->SurName;
        echo "\t\t\t\t\t\t\t";
        if(!$invoice->CompanyName) {
            echo "</a>";
        }
        echo "<br />\n\t\t\t\t\t\t\t";
        echo $invoice->Address;
        echo "<br />\n\t\t\t\t\t\t\t";
        if(IS_INTERNATIONAL && $invoice->Address2) {
            echo $invoice->Address2 . "<br />";
        }
        echo "\t\t\t\t\t\t\t";
        echo $invoice->ZipCode . "&nbsp;&nbsp;" . $invoice->City;
        echo "<br />\n\t\t\t\t\t\t\t";
        if(IS_INTERNATIONAL && $invoice->StateName) {
            echo $invoice->StateName . "<br />";
        }
        echo "\t\t\t\t\t\t\t";
        echo $array_country[$invoice->Country];
        echo " <br />\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t";
        if(isset($invoice->TaxNumber) && $invoice->TaxNumber != "") {
            echo "<br />" . __("vat number") . ": " . $invoice->TaxNumber;
        }
        echo "\t\t\t\t\t\t</p>\n\t\t\t\t\t\n\t\t\t\t\t<!--back1-->\n\t\t\t\t\t</div>\n\t\t\t\t\t<!--back1-->\n\t\t\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t</div>\n\t\t\t\t<!--left-->\n\t\t\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t<div class=\"right\" style=\"position: absolute; bottom: 0px; right: 0;\">\n\t\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t\t\t<!--back3-->\n\t\t\t\t\t<div class=\"noback\" style=\"padding-left: 0px; margin-bottom: 0px;\">\n\t\t\t\t\t<!--back3-->\n\t\t\t\t\t\n\t\t\t\t\t<table class=\"table3\">\n\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td style=\"width:105px;\">";
        echo __("debtor no");
        echo ":</td>\n\t\t\t\t\t\t<td><a href=\"debtors.php?page=show&amp;id=";
        echo $invoice->Debtor;
        echo "\" class=\"c1 a1\">";
        echo $debtor->DebtorCode;
        echo "</a></td>\n\t\t\t\t\t</tr>\n\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td>";
        echo __("invoice no");
        echo ":</td>\n\t\t\t\t\t\t<td>";
        echo $invoice->InvoiceCode;
        echo "</td>\n\t\t\t\t\t</tr>\n\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td>";
        echo __("date");
        echo ":</td>\n\t\t\t\t\t\t<td>";
        echo $invoice->Date;
        echo "</td>\n\t\t\t\t\t</tr>\n\t\t\t\t\t";
        if($invoice->ReferenceNumber) {
            echo "\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td colspan=\"2\">&nbsp;</td>\n\t\t\t\t\t</tr>\n\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td>";
            echo __("reference no");
            echo ":</td>\n\t\t\t\t\t\t<td>";
            echo $invoice->ReferenceNumber;
            echo "</td>\n\t\t\t\t\t</tr>\n\t\t\t\t\t";
        }
        echo "\t\t\t\t\t</table>\n\t\t\n\t\t\t\t\t<!--back3-->\n\t\t\t\t\t</div>\n\t\t\t\t\t<!--back3-->\n\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t</div>\n\t\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t<!--split3-->\n\t\t\t</div>\n\t\t\t<!--split3-->\n\n            ";
        if(!empty($invoice->customfields_list)) {
            echo "                <div class=\"lineheight_text\" style=\"margin-left:15px;\">\n                    <div class=\"field_row\">\n                        <strong>";
            echo __("custom debtor fields h2");
            echo "</strong>\n                    </div>\n\n                    ";
            foreach ($invoice->customfields_list as $k => $custom_field) {
                echo show_custom_field_invoice($custom_field, isset($invoice->custom->{$custom_field["FieldCode"]}) ? $invoice->custom->{$custom_field["FieldCode"]} : "");
            }
            echo "\n                </div>\n                ";
        }
        echo "\t\t\t<div class=\"table_wrap\">\n\n\t\t\t\t<div class=\"credit_arrow hide\"><img src=\"";
        echo __SITE_URL;
        echo "/images/credit-partially-arrow.svg\"/></div>\n\t\t\t\t<h4 class=\"hide h4_choose_rows\">";
        echo __("credit invoice select lines to credit");
        echo "</h4>\n\n\t\t\t\t<form name=\"form_lines\" method=\"post\">\n\t\t\t\t\t<input type=\"hidden\" name=\"imsure\" value=\"yes\"/>\n\t\t\t\t\t<table class=\"table1 alt1 noborder\" cellpadding=\"0\" cellspacing=\"0\">\n\t\t\t\t\t<tr class=\"trtitle\">\n\t\t\t\t\t\t<th class=\"credit_checkbox_head hide\" width=\"10\"><input style=\"margin-bottom: 0;\" name=\"credit_checkbox\" type=\"checkbox\" class=\"BatchCheck\"/></th>\n\t\t\t\t\t\t<th scope=\"col\" class=\"table-th-date\" style=\"border-top-left-radius: 5px;\">";
        echo __("date");
        echo "</th>\n\t\t\t\t\t\t<th scope=\"col\">";
        echo __("number");
        echo "</th>\n\t\t\t\t\t\t<th scope=\"col\">";
        echo __("productcode");
        echo "</th>\n\t\t\t\t\t\t<th scope=\"col\">";
        echo __("description");
        echo "</th>\n\t\t\t\t\t\t";
        if(!empty($array_taxpercentages)) {
            echo "<th scope=\"col\">";
            echo __("vat");
            echo "</th>";
        }
        echo "\t\t\t\t\t\t<th scope=\"col\" colspan=\"2\">";
        echo __("price per unit");
        echo "</th>\n\t\t\t\t\t\t<th scope=\"col\" colspan=\"2\">";
        if(empty($array_taxpercentages)) {
            echo __("line total");
        } elseif($invoice->VatCalcMethod == "incl") {
            echo __("total incl");
        } else {
            echo __("total excl");
        }
        echo "</th>\n\t\t\t\t\t</tr>\n\t\t\t\t\t";
        foreach ($invoice->Elements as $k => $element) {
            if(is_numeric($k)) {
                echo "\t\t\t\t\t\t<tr class=\"tr2 valign_top ";
                if(isEmptyFloat($element["DiscountPercentage"])) {
                    echo "tr_invoice";
                }
                echo "\">\n\t\t\t\t\t\t\t<td class=\"credit_checkbox_row hide\"><input style=\"margin-bottom: 0;\" name=\"ids[]\" value=\"";
                echo $element["id"];
                echo "\" type=\"checkbox\" class=\"credit_checkbox\"/></td>\n\t\t\t\t\t\t\t<td class=\"table-tr-date\" style=\"width:70px;\">";
                if(!isEmptyFloat($element["PriceExcl"]) || $element["ProductCode"]) {
                    echo $element["Date"];
                } else {
                    echo "&nbsp;";
                }
                echo "</td>\n\t\t\t\t\t\t\t<td style=\"width:45px; white-space:nowrap;\" class=\"credit_checkbox_number_td\">\n\t\t\t\t\t\t\t\t<span class=\"credit_checkbox_number_span\">";
                if(!isEmptyFloat($element["PriceExcl"]) || $element["ProductCode"]) {
                    echo showNumber($element["Number"]) . $element["NumberSuffix"];
                } else {
                    echo "&nbsp;";
                }
                echo "</span>\n\t\t\t\t\t\t\t\t<span class=\"credit_checkbox_number_input hide\">\n\t\t\t\t\t\t\t\t\t<input type=\"text\" name=\"Number[";
                echo $element["id"];
                echo "]\" disabled class=\"text1 size3\" data-number=\"";
                echo 0 < $element["Number"] ? "positive" : "negative";
                echo "\" value=\"";
                echo number2site($element["Number"]) . $element["NumberSuffix"];
                echo "\"/>\n\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t</td>\n\n\t\t\t\t\t\t\t";
                if(defined("SHOW_PRODUCTNAME") && SHOW_PRODUCTNAME === true) {
                    echo "\t\t\t\t\t\t\t\t<td>";
                    echo $element["ProductCode"] . " - " . $element["ProductName"];
                    echo "</td>\n\t\t\t\t\t\t\t";
                } else {
                    echo "\t\t\t\t\t\t\t\t<td style=\"width:50px;\">";
                    echo $element["ProductCode"];
                    echo "</td>\n\t\t\t\t\t\t\t";
                }
                echo "\n\t\t\t\t\t\t\t<td>";
                if(0 < $element["Reference"] && $element["ProductType"] && isset($_module_instances[$element["ProductType"]])) {
                    echo "<a href=\"modules.php?module=" . $element["ProductType"] . "&page=show&amp;id=" . $element["Reference"] . "\" class=\"a1 c1\">" . nl2br($element["Description"]) . "</a>";
                } elseif(0 < $element["Reference"] && in_array($element["ProductType"], ["domain", "hosting"])) {
                    echo "<a href=\"" . ($element["ProductType"] == "domain" ? "domains" : "hosting") . ".php?page=show&amp;id=" . $element["Reference"] . "\" class=\"a1 c1\">" . nl2br($element["Description"]) . "</a>";
                } elseif(0 < $element["PeriodicID"]) {
                    echo "<a href=\"services.php?page=show&amp;id=" . $element["PeriodicID"] . "\" class=\"a1 c1\">" . nl2br($element["Description"]) . "</a>";
                } else {
                    echo nl2br($element["Description"]);
                }
                echo "\t\t\t\t\t\t\t\t";
                if($element["Periodic"]) {
                    echo "<br />" . __("period") . ": " . $element["StartPeriod"] . " " . __("till") . " " . $element["EndPeriod"] . " (" . $element["Periods"] . " " . (1 < $element["Periods"] ? $array_periodesMV[$element["Periodic"]] : $array_periodes[$element["Periodic"]]) . ")";
                }
                echo "\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t";
                if(!empty($array_taxpercentages)) {
                    echo "\t\t\t\t\t\t\t<td style=\"width:20px;\" class=\"align_right\">\n\t\t\t\t\t\t\t\t";
                    if(!isEmptyFloat($element["PriceExcl"]) || $element["ProductCode"]) {
                        echo vat($element["TaxPercentage"] * 100) . "%";
                    } else {
                        echo "&nbsp;";
                    }
                    echo "\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t";
                }
                echo "\t\t\t\t\t\t\t<td style=\"width:5px;\" class=\"currency_sign_left\">";
                if(!isEmptyFloat($element["PriceExcl"]) || $element["ProductCode"]) {
                    echo currency_sign_td(CURRENCY_SIGN_LEFT);
                } else {
                    echo "&nbsp;";
                }
                echo "</td>\n\t\t\t\t\t\t\t<td style=\"width:65px;\" class=\"align_right currency_sign_right\">\n\t\t\t\t\t\t\t\t";
                if(!isEmptyFloat($element["PriceExcl"]) || $element["ProductCode"]) {
                    if($invoice->VatCalcMethod == "incl") {
                        echo money($element["PriceExcl"] * round(1 + $element["TaxPercentage"], 3), false);
                    } else {
                        echo money($element["PriceExcl"], false);
                    }
                    if(CURRENCY_SIGN_RIGHT) {
                        echo " " . CURRENCY_SIGN_RIGHT;
                    }
                } else {
                    echo "&nbsp;";
                }
                echo "\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t<td style=\"width:5px;\" class=\"currency_sign_left\">";
                if(!isEmptyFloat($element["PriceExcl"]) || $element["ProductCode"]) {
                    echo currency_sign_td(CURRENCY_SIGN_LEFT);
                } else {
                    echo "&nbsp;";
                }
                echo "</td>\n\t\t\t\t\t\t\t<td style=\"width:75px;\" class=\"align_right currency_sign_right\">\n\t\t\t\t\t\t\t\t";
                if(!isEmptyFloat($element["PriceExcl"]) || $element["ProductCode"]) {
                    if($invoice->VatCalcMethod == "incl") {
                        echo money($element["PriceExcl"] * $element["Periods"] * $element["Number"] * round(1 + $element["TaxPercentage"], 3), false);
                    } else {
                        echo money($element["PriceExcl"] * $element["Periods"] * $element["Number"], false);
                    }
                    if(CURRENCY_SIGN_RIGHT) {
                        echo " " . CURRENCY_SIGN_RIGHT;
                    }
                } else {
                    echo "&nbsp;";
                }
                echo "\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t</tr>\n\n\t\t\t\t\t\t";
                if(isset($element["DiscountPercentage"]) && !isEmptyFloat($element["DiscountPercentage"])) {
                    $discount = -1 * ($invoice->VatCalcMethod == "incl" ? $element["PriceExcl"] * $element["Periods"] * $element["Number"] * $element["DiscountPercentage"] * round(1 + $element["TaxPercentage"], 3) : $element["PriceExcl"] * $element["Periods"] * $element["Number"] * $element["DiscountPercentage"]);
                    if(0 < $discount) {
                        $discount = number_format($discount + 0, 2, ".", "");
                    } elseif($discount < 0) {
                        $discount = number_format($discount - 0, 2, ".", "");
                    }
                    echo "\t\t\t\t\t\t\t<tr class=\"tr2 valign_top tr_invoice\">\n\t\t\t\t\t\t\t\t<td class=\"credit_checkbox_row_discount hide\"></td>\n\t\t\t\t\t\t\t\t<td colspan=\"3\">&nbsp;</td>\n\t\t\t\t\t\t\t\t<td><i>";
                    echo sprintf($element["DiscountPercentageType"] == "subscription" ? __("x discount on invoiceline and subscription") : __("x discount on invoiceline"), showNumber(round($element["DiscountPercentage"] * 100, 2)));
                    echo "</i></td>\n\t\t\t\t\t\t\t\t<td>&nbsp;</td>\n\t\t\t\t\t\t\t\t<td>&nbsp;</td>\n\t\t\t\t\t\t\t\t";
                    if(!empty($array_taxpercentages)) {
                        echo "<td>&nbsp;</td>";
                    }
                    echo "\t\t\t\t\t\t\t\t<td class=\"currency_sign_left\">";
                    echo currency_sign_td(CURRENCY_SIGN_LEFT);
                    echo "</td>\n\t\t\t\t\t\t\t\t<td class=\"currency_sign_right\" align=\"right\">";
                    echo money($discount, false);
                    if(CURRENCY_SIGN_RIGHT) {
                        echo " " . CURRENCY_SIGN_RIGHT;
                    }
                    echo "</td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t";
                }
                echo "\n\t\t\t\t\t";
            }
        }
        echo "\n\t\t\t\t\t";
        if(!isEmptyFloat($invoice->Discount)) {
            echo "\t\t\t\t\t<tr class=\"tr2 valign_top\">\n\t\t\t\t\t\t<td class=\"credit_checkbox_row_discount hide\"></td>\n\t\t\t\t\t\t<td colspan=\"";
            echo !empty($array_taxpercentages) ? 9 : 8;
            echo "\">&nbsp;</td>\n\t\t\t\t\t</tr>\n\t\t\t\t\t<tr class=\"tr2 valign_top\">\n\t\t\t\t\t\t<td class=\"credit_checkbox_row_discount hide\"></td>\n\t\t\t\t\t\t<td colspan=\"3\">&nbsp;</td>\n\t\t\t\t\t\t<td><strong>";
            echo showNumber($invoice->Discount);
            echo "% ";
            echo __("discount on invoice");
            echo "</strong></td>\n\t\t\t\t\t\t<td>&nbsp;</td>\n\t\t\t\t\t\t<td>&nbsp;</td>\n\t\t\t\t\t\t";
            if(!empty($array_taxpercentages)) {
                echo "<td>&nbsp;</td>";
            }
            echo "\t\t\t\t\t\t<td class=\"currency_sign_left\">";
            echo currency_sign_td(CURRENCY_SIGN_LEFT);
            echo "</td>\n\t\t\t\t\t\t<td class=\"currency_sign_right\" align=\"right\">";
            if($invoice->VatCalcMethod == "incl") {
                echo $invoice->AmountDiscountIncl;
            } else {
                echo $invoice->AmountDiscount;
            }
            if(CURRENCY_SIGN_RIGHT) {
                echo " " . CURRENCY_SIGN_RIGHT;
            }
            echo "</td>\n\t\t\t\t\t</tr>\n\t\t\t\t\t";
        }
        echo "\t\t\t\t</table>\n\t\t\t</form>\n\t\t\t<div class=\"partial_credit_buttons hide\" style=\"position: absolute; width: 100%; margin-top:20px;\">\n\n\t\t\t\t<p><a id=\"credit_partial_btn\" style=\"line-height: 16px; padding: 8px 10px; font-size: 14px;\" class=\"button1 alt1 disabled float_left\"><span>";
        echo __("action credit");
        echo "</span></a></p>\n\t\t\t\t<p><a id=\"credit_partial_cancel_btn\" class=\"a1 c1 float_right\" style=\"margin-bottom: 50px;\"><span>";
        echo __("cancel");
        echo "</span></a></p>\n\n\t\t\t</div>\n\t\t\t</div>\n\t\t\n\t\t\t<!--box6-->\n\t\t\t<div class=\"box6\">\n\t\t\t<!--box6-->\n\t\t\t\n\t\t\t\t<table class=\"table7\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\n\t\t\t\t\n\t\t\t\t\n\t\t\t\t\n\t\t\t\t";
        if(!empty($array_taxpercentages) || !empty($array_total_taxpercentages)) {
            echo "\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td style=\"border-top: 1px solid black;\">";
            echo __("invoice total excl vat");
            echo "</td>\n\t\t\t\t\t\t<td style=\"border-top: 1px solid black;width:15px;\" class=\"currency_sign_left\">";
            echo currency_sign_td(CURRENCY_SIGN_LEFT);
            echo "</td>\n\t\t\t\t\t\t<td style=\"border-top: 1px solid black;width:75px;\" class=\"align_right currency_sign_right\">";
            echo $invoice->AmountExcl;
            if(CURRENCY_SIGN_RIGHT) {
                echo " " . CURRENCY_SIGN_RIGHT;
            }
            echo "</td>\n\t\t\t\t\t</tr>\n\t\t\t\t\t";
        }
        global $array_taxpercentages;
        global $array_taxpercentages_info;
        asort($array_taxpercentages);
        foreach ($array_taxpercentages as $key => $value) {
            if(isset($invoice->used_taxrates[(string) (double) $key]["AmountTax"]) && 0 < (double) $key) {
                echo "\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td>";
                echo isset($array_taxpercentages_info[(string) (double) $key]["label"]) ? $array_taxpercentages_info[(string) (double) $key]["label"] : "";
                echo "</td>\n\t\t\t\t\t\t\t<td class=\"currency_sign_left\">";
                echo currency_sign_td(CURRENCY_SIGN_LEFT);
                echo "</td>\n\t\t\t\t\t\t\t<td class=\"align_right currency_sign_right\">";
                echo $invoice->used_taxrates[(string) (double) $key]["AmountTax"];
                if(CURRENCY_SIGN_RIGHT) {
                    echo " " . CURRENCY_SIGN_RIGHT;
                }
                echo "</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t";
            }
        }
        if(isset($invoice->TaxRate_Label) && $invoice->TaxRate_Label) {
            echo "\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td>";
            echo $invoice->TaxRate_Label;
            echo "</td>\n\t\t\t\t\t\t<td class=\"currency_sign_left\">";
            echo currency_sign_td(CURRENCY_SIGN_LEFT);
            echo "</td>\n\t\t\t\t\t\t<td class=\"align_right currency_sign_right\">";
            echo $invoice->TaxRate_Amount;
            if(CURRENCY_SIGN_RIGHT) {
                echo " " . CURRENCY_SIGN_RIGHT;
            }
            echo "</td>\n\t\t\t\t\t</tr>\n\t\t\t\t\t";
        }
        echo "\t\t\t\t\n\t\t\t\t\n\t\t\t\t<tr class=\"line\">\n\t\t\t\t\t<td style=\"border-top: 1px solid black;\">";
        if(empty($array_taxpercentages) && empty($array_total_taxpercentages)) {
            echo __("invoice total");
        } else {
            echo __("invoice total incl vat");
        }
        echo "</td>\n\t\t\t\t\t<td style=\"border-top: 1px solid black;\" class=\"currency_sign_left\">";
        echo currency_sign_td(CURRENCY_SIGN_LEFT);
        echo "</td>\n\t\t\t\t\t<td style=\"border-top: 1px solid black;\" class=\"align_right currency_sign_right\">";
        echo $invoice->AmountIncl;
        if(CURRENCY_SIGN_RIGHT) {
            echo " " . CURRENCY_SIGN_RIGHT;
        }
        echo "</td>\n\t\t\t\t</tr>\n\n\n\t\t\t\t";
        if($invoice->Status == 3) {
            echo "                        <tr>\n        \t\t\t\t\t<td colspan=\"3\"><br /><br /></td>\n        \t\t\t\t</tr>                \n        \t\t\t\t<tr>\n        \t\t\t\t\t<td>";
            echo __("partial payment");
            echo "</td>\n        \t\t\t\t\t<td class=\"currency_sign_left\">";
            echo currency_sign_td(CURRENCY_SIGN_LEFT);
            echo "</td>\n        \t\t\t\t\t<td class=\"align_right currency_sign_right\">";
            echo $invoice->AmountPaid;
            if(CURRENCY_SIGN_RIGHT) {
                echo " " . CURRENCY_SIGN_RIGHT;
            }
            echo "</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td>";
            echo __("open sum");
            echo "</td>\n\t\t\t\t\t\t\t<td class=\"currency_sign_left\">";
            echo currency_sign_td(CURRENCY_SIGN_LEFT);
            echo "</td>\n\t\t\t\t\t\t\t<td class=\"align_right currency_sign_right\">";
            echo $invoice->PartPayment;
            if(CURRENCY_SIGN_RIGHT) {
                echo " " . CURRENCY_SIGN_RIGHT;
            }
            echo "</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\n\t\t\t\t</table>\n\t\t\t\n\t\t\t<!--box6-->\n\t\t\t</div>\n\t\t\t<!--box6-->\n\t\t\t\n\t\t\t";
        if(isset($show_vatshift_text) && $show_vatshift_text !== false) {
            echo "\t\t\t\t<div class=\"vatshift_text_view\" style=\"margin-top: 70px\">";
            echo $show_vatshift_text;
            echo "</div>\t\t\t\t\t\t\n\t\t\t";
        }
        echo "\t\t\t\n\t\t<!--box5-->\n\t\t</div>\n\t\t<!--box5-->\n\t\t\n\t\t<br clear=\"both\"/>\n\t\t\n\t\t</div>\n\t\t\n\t<!--split4-->\n\t</div>\n\t<!--split4-->\n\n<!--right-->\n</div>\n<!--right-->\n\n<br />\n\n<!--box1-->\n<div class=\"box2\" id=\"tabs\">\n<!--box1-->\n\n\t<!--top-->\n\t<div class=\"top\">\n\t<!--top-->\n\t\n\t\t<ul class=\"list3\">\n\t\t\t<li class=\"on\"><a href=\"#tab-extra\">";
        echo __("invoice options");
        echo "</a></li>\n\t\t\t";
        if($invoice->Description) {
            echo "<li><a href=\"#tab-description\">";
            echo __("description");
            echo " ";
            if($invoice->Description) {
                echo "<span class=\"ico actionblock info nm\">";
                echo __("more information");
                echo "</span>";
            }
            echo "</a></li>";
        }
        echo "\t\t\t<li><a href=\"#tab-note\">";
        echo __("internal note");
        echo " ";
        if($invoice->Comment) {
            echo "<span class=\"ico actionblock info nm\">";
            echo __("more information");
            echo "</span>";
        }
        echo "</a></li>\n\t\t\t";
        if($transaction_matches_options !== false) {
            echo "<li><a href=\"#tab-transactions\">";
            echo __("bank transactions tab");
            echo " (<span id=\"page_total_placeholder_transactions\" class=\"hide\">0</span>)</a></li>";
        }
        echo "\t\t\t";
        if(!empty($accounting_transactions)) {
            foreach ($accounting_transactions as $_package => $_accounting_package) {
                echo "<li><a href=\"#tab-accounting_transactions_";
                echo $_package;
                echo "\">";
                echo $_accounting_package["Title"];
                echo " ";
                echo __("invoice show accounting transactions");
                echo " (<span id=\"page_total_placeholder_accounting_transactions_";
                echo $_package;
                echo "\" class=\"hide\">0</span>)</a></li>";
            }
        }
        echo "\t\t</ul>\n\t\n\t<!--top-->\n\t</div>\n\t<!--top-->\n\t\n\t<!--content-->\n\t<div class=\"content\" id=\"tab-extra\">\n\t<!--content-->\n\t\n\t\t<!--split2-->\n\t\t<div class=\"split2\">\n\t\t<!--split2-->\n\t\t\n\t\t\t<!--left-->\n\t\t\t<div class=\"left\">\n\t\t\t<!--left-->\n\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
        echo __("invoice options send");
        echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title2\">";
        echo __("invoice template");
        echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
        echo $template->Name;
        echo "</span>\n\t\t\t\t\t\n\t\t\t\t\t";
        if($invoice->Sent) {
            echo "\t\t\t\t\t<strong class=\"title2\">";
            echo __("sentdate invoice");
            echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
            echo rewrite_date_db2site($invoice->SentDate);
            echo "</span>\n\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3";
        if(empty($invoice->Attachment)) {
            echo " hide";
        }
        echo "\"><h3>";
        echo __("invoice attachments");
        echo "</h3><div class=\"content\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t<div id=\"files_list\" class=\"hide\">\n\t\t\t\t\t\t<p class=\"align_right mar4\"><i>";
        echo __("total");
        echo ": <span id=\"files_total\"></span></i></p>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<ul id=\"files_list_ul\" class=\"emaillist\">\n\t\t\t\t\t\t";
        $attachCounter = 0;
        if(!empty($invoice->Attachment)) {
            echo "\t\t\t\t\t\t\t";
            foreach ($invoice->Attachment as $key => $value) {
                echo "\t\t\t\t\t\t\t<li>\n\t\t\t\t\t\t\t\t<input type=\"hidden\" name=\"File[]\" value=\"";
                echo $value->id;
                echo "\" />\n\t\t\t\t\t\t\t\t<div class=\"file ico inline file_";
                echo getFileType($value->FilenameServer);
                echo "\">&nbsp;</div> <a href=\"download.php?type=invoice&amp;id=";
                echo $value->id;
                echo "\" class=\"a1\" target=\"_blank\">";
                echo $value->Filename;
                echo "</a>\n\t\t\t\t\t\t\t\t<div class=\"filesize\">";
                $fileSizeUnit = getFileSizeUnit($value->Size);
                echo $fileSizeUnit["size"] . " " . $fileSizeUnit["unit"];
                echo "</div>\n\t\t\t\t\t\t\t</li>\n\t\t\t\t\t\t\t";
                $attachCounter++;
            }
            echo "\t\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\t</ul>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<br />\n\t\t\t\t\t</div>\n\t\t\t\t\t\n\t\t\t\t\t<p><i id=\"files_none\" ";
        if($attachCounter !== 0) {
            echo "class=\"hide\"";
        }
        echo ">";
        echo __("no attachments");
        echo "</i></p>\n\t\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t<!--left-->\n\t\t\t</div>\n\t\t\t<!--left-->\n\t\t\t\n\t\t\t<!--right-->\n\t\t\t<div class=\"right\">\n\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
        echo __("invoice options payment");
        echo "</h3><div class=\"content lineheight2 label_medium\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title2\">";
        echo __("invoice options term");
        echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
        echo $invoice->Term;
        echo " ";
        echo __("days");
        echo "</span>\n\n                    ";
        if($invoice->Status != "8") {
            echo "\t\t\t\t\t<strong class=\"title2\">";
            echo __("invoice options authorisation");
            echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
            echo $array_authorisation[$invoice->Authorisation];
            echo " ";
            if($invoice->Authorisation == "yes" && U_INVOICE_EDIT && $invoice->Status < 4) {
                echo "<a href=\"?page=show&amp;action=removeauth&amp;id=";
                echo $invoice->Identifier;
                echo "\" class=\"a1 c1 marleft_1\">";
                echo __("invoice options remove authorisation");
                echo "</a>";
            }
            echo "</span>\n                    ";
        }
        echo "\t\t\t\t\t\n\t\t\t\t\t";
        if($invoice->Coupon) {
            echo "\t\t\t\t\t<strong class=\"title2\">";
            echo __("coupon");
            echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
            echo $invoice->Coupon;
            echo "</span>\n\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\n\t\t\t\t\t";
        if($invoice->IgnoreDiscount == 1) {
            echo "\t\t\t\t\t<strong class=\"title2\">";
            echo __("ignore discount");
            echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
            echo __("yes");
            echo "</span>\n\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\n\t\t\t\t\t";
        if(2 <= $invoice->Status && $invoice->Status < 4 && $payment_methods_available) {
            echo "\t\t\t\t\t<strong class=\"title2\">";
            echo __("paymentURL");
            echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\"><a onclick=\"\$('#invoicePaymentURL').show().select();\$(this).hide();\"  class=\"c1\">";
            echo __("show paymentURL");
            echo "</a></span>\n\t\t\t\t\t\n\t\t\t\t\t<span class=\"title2_value\" style=\"padding-right: 10px;\"><input id=\"invoicePaymentURL\" class=\"text1 size9 hide\" type=\"text\"  value=\"";
            echo $invoice->PaymentURLRaw;
            echo "\" /></span>\n\t\t\t\t\t\n\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\n\t\t\t<!--right-->\n\t\t\t</div>\n\t\t\t<!--right-->\n\t\t\t\n\t\t<!--split2-->\n\t\t</div>\n\t\t<!--split2-->\n\t\t\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\t\n\t";
        if($invoice->Description) {
            echo "\t<!--content-->\n\t<div class=\"content\" id=\"tab-description\">\n\t<!--content-->\n\t\n\t\t<!--box3-->\n\t\t<div class=\"box3\"><h3>";
            echo __("description");
            echo "</h3><div class=\"content\" style=\"overflow-x: auto;\">\n\t\t<!--box3-->\n\t\t\n\t\t\t";
            echo $invoice->Description;
            echo "\n\t\t<!--box3-->\n\t\t</div></div>\n\t\t<!--box3-->\n\t\t\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\t";
        }
        echo "\n\t<!--content-->\n\t<div class=\"content\" id=\"tab-note\">\n\t<!--content-->\n\t\n\t\t<!--box3-->\n\t\t<div class=\"box3\"><h3>";
        echo __("internal note");
        echo "</h3><div class=\"content lineheight2\">\n\t\t<!--box3-->\n\t\t\t\n\t\t\t<form action=\"invoices.php?page=show&id=";
        echo $invoice->Identifier;
        echo "\" method=\"post\" name=\"invoice_comment_form\">\n\t\t\t\n\t\t\t<textarea class=\"text1 size5 autogrow\" name=\"Comment\">";
        echo $invoice->Comment;
        echo "</textarea>\n\t\t\t\n\t\t\t<br />\n\t\t\t\n\t\t\t";
        if(U_INVOICE_EDIT) {
            echo "\t\t\t<a class=\"button1 alt1 margint\" href=\"javascript:document.invoice_comment_form.submit();\"><span>";
            echo __("edit note");
            echo "</span></a>\n\t\t\t";
        }
        echo "\t\t\t</form>\n\n\t\t<!--box3-->\n\t\t</div></div>\n\t\t<!--box3-->\n\t\t\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\t\n\t";
        if($transaction_matches_options !== false) {
            echo "\t<!--content-->\n\t<div class=\"content\" id=\"tab-transactions\">\n\t<!--content-->\n\t";
            $transaction_matches_options["hide_cols"] = ["referencecode"];
            generate_table("list_transaction_matches_invoice", $transaction_matches_options);
            echo "\t<!--content-->\n\t</div>\n\t<!--content-->\n\t";
        }
        echo "\n\t";
        if(!empty($accounting_transactions)) {
            foreach ($accounting_transactions as $_package => $_accounting_package) {
                echo "\t\t\t<!--content-->\n\t\t\t<div class=\"content\" id=\"tab-accounting_transactions_";
                echo $_package;
                echo "\">\n\t\t\t\t<!--content-->\n\t\t\t\t";
                $options = $invoice->getAccountingTransactionsTableConfig($_package);
                generate_table("invoice_show_accounting_transactions_" . $_package, $options);
                echo "\t\t\t\t<!--content-->\n\t\t\t</div>\n\t\t\t<!--content-->\n\t\t\t";
            }
        }
        echo "\t\n<!--box1-->\n</div>\n<!--box1-->\n\n";
        if(U_INVOICE_EDIT && ($invoice->Status == 2 || $invoice->Status == 3)) {
            echo "<div id=\"invoicedialog_partpayment\" class=\"hide\" title=\"";
            echo __("partpaymentdialog title");
            echo "\">\n\t<form name=\"form_partpayment\" method=\"post\" action=\"invoices.php?page=show&action=partialpayment\">\n\t<input type=\"hidden\" name=\"id\" value=\"";
            echo $invoice->Identifier;
            echo "\"/>\n\t";
            echo sprintf(__("partpaymentdialog description"), $invoice->InvoiceCode);
            echo "<br />\n\t<br />\n\t<span class=\"title2\">";
            echo __("total invoice");
            echo ":</span>\n\t<span class=\"title2_value\">";
            echo CURRENCY_SIGN_LEFT . " " . $invoice->AmountIncl . " " . CURRENCY_SIGN_RIGHT;
            echo "</span>\n\t<span class=\"title2\">";
            echo __("already paid");
            echo ":</span>\n\t<span class=\"title2_value\">";
            echo CURRENCY_SIGN_LEFT . " " . $invoice->AmountPaid . " " . CURRENCY_SIGN_RIGHT;
            echo "</span>\n\t<br />\n\t<strong class=\"title2\">";
            echo __("still to pay");
            echo ":</strong>\n\t<strong class=\"title2_value\">";
            echo CURRENCY_SIGN_LEFT . " " . $invoice->PartPayment . " " . CURRENCY_SIGN_RIGHT;
            echo "</strong>    \n\t<br />\n\t<strong class=\"title2 lineheight_input\">";
            echo __("partial payment received payment");
            echo "</strong>\n\t<span class=\"title2_value\"><input type=\"text\" name=\"AmountPaid\" class=\"text1 size6\" value=\"\" /></span>\n    <strong class=\"title2 lineheight_input\">";
            echo __("partial payment received date");
            echo "</strong>\n\t<span class=\"title2_value\"><input type=\"text\" class=\"text1 size6 datepicker\" name=\"PayDate\" value=\"";
            echo rewrite_date_db2site(date("Y-m-d"));
            echo "\" /></span><br />\n\t<br />\n\t<p><a class=\"button1 alt1 float_left\" id=\"partpayment_btn\"><span>";
            echo __("partpaymentdialog process");
            echo "</span></a></p>\n\t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#invoicedialog_partpayment').dialog('close');\"><span>";
            echo __("cancel");
            echo "</span></a></p>\n\t</form>\n</div>\n";
        }
        if(U_INVOICE_EDIT && !empty($invoice->Attachment)) {
            echo "\t<div id=\"invoicedialog_clone\" class=\"hide\" title=\"";
            echo __("clone invoice");
            echo "\">\n\t\t<form name=\"form_clone\" method=\"post\" action=\"";
            echo "invoices.php?page=add&clone=" . $invoice->Identifier;
            echo "\">\n\t\t\n\t\t";
            echo __("clone invoice with or without attachments");
            echo "<br />\n\t\t\n\t\t";
            foreach ($invoice->Attachment as $key => $value) {
                echo "\t\t<label><input type=\"checkbox\" name=\"copyAttachments[]\" value=\"";
                echo $value->id;
                echo "\" /> ";
                echo $value->Filename;
                echo "</label><br />\n\t\t";
            }
            echo "\n\t\t<br />\n\t\t<p><a class=\"button1 alt1 float_left\" id=\"clone_btn\"><span>";
            echo __("clone invoice btn");
            echo "</span></a></p>\n\t\t<p style=\"line-height:30px\"><a class=\"a1 c1 float_right\" onclick=\"\$('#invoicedialog_clone').dialog('close');\"><span>";
            echo __("cancel");
            echo "</span></a></p>\n\t\t</form>\n\t</div>\n\t";
        }
        echo "<div id=\"invoicedialog_history\" class=\"hide\" title=\"";
        echo __("historydialog title");
        echo " ";
        echo $invoice->InvoiceCode;
        echo "\">\n\t\n\t";
        echo __("historydialog description");
        echo "\t\n\t";
        require_once "views/elements/log.table.php";
        $options = ["form_action" => "invoices.php?page=show&amp;id=" . $invoice->Identifier, "session_name" => "invoice.show.logfile", "current_page" => $current_page, "current_page_url" => $current_page_url, "allow_delete" => U_INVOICE_DELETE, "show_icons" => true];
        show_log_table($history, $options);
        echo "\t\t\n\t\n\t<p><a class=\"button1 alt1 float_right\" onclick=\"\$('#invoicedialog_history').dialog('close');\"><span>";
        echo __("close");
        echo "</span></a></p>\n</div>\n\n";
        if(U_INVOICE_EDIT) {
            echo "    <div id=\"invoicedialog_invoicemethod\" class=\"hide\" title=\"";
            echo __("invoicemethoddialog title");
            echo "\">\n    \t<form name=\"form_invoicemethod\" method=\"post\" action=\"invoices.php?page=show&action=changesendmethod&id=";
            echo $invoice->Identifier;
            echo "\">\n    \t<input type=\"hidden\" name=\"id\" value=\"";
            echo $invoice->Identifier;
            echo "\"/>\n    \t";
            echo sprintf(__("invoicemethoddialog description"), $invoice->InvoiceCode);
            echo "<br />\n    \t<br />\n    \t<span class=\"title2 lineheight_input\">";
            echo __("invoice method");
            echo ":</span>\n    \t<span class=\"title2_value\">\n\t\t\t<select name=\"InvoiceMethod\" class=\"text1 size1\">\n\t\t\t\t";
            foreach ($array_invoicemethod as $key => $value) {
                echo "\t\t\t\t<option value=\"";
                echo $key;
                echo "\" ";
                if($key == $invoice->InvoiceMethod) {
                    echo "selected=\"selected\"";
                }
                echo ">";
                echo $value;
                echo "</option>\n\t\t\t\t";
            }
            echo "\t\t\t</select>\n\t\t</span>\n\n\t\t";
            $invoice_emailaddress = "";
            if($invoice->EmailAddress) {
                $invoice_emailaddress = $invoice->EmailAddress;
            } elseif($debtor->InvoiceEmailAddress) {
                $invoice_emailaddress = $debtor->InvoiceEmailAddress;
            } elseif($debtor->EmailAddress) {
                $invoice_emailaddress = $debtor->EmailAddress;
            }
            echo "\t\t<div class=\"";
            if($invoice->InvoiceMethod == "1") {
                echo "hide";
            }
            echo "\" id=\"NewMethodEmailAddress\">\n\t\t\t<span class=\"title2 lineheight_input\">";
            echo __("emailaddress");
            echo ":</span>\n\t\t\t<span class=\"title2_value\">\n\t\t\t\t<input type=\"text\" name=\"NewMethodEmailAddress\" value=\"";
            echo check_email_address($invoice_emailaddress, "convert", ", ");
            echo "\" class=\"text1 size1\" />\n\t\t\t</span>\n\t\t</div>\n\n    \t<br />\n    \t<p><a class=\"button1 alt1 float_left\" id=\"invoicemethod_btn\"><span>";
            echo __("invoicemethoddialog process");
            echo "</span></a></p>\n    \t<p><a class=\"c1 a1 float_right\" onclick=\"\$('#invoicedialog_invoicemethod').dialog('close');\"><span>";
            echo __("cancel");
            echo "</span></a></p>\n    \t</form>\n    </div>\n\t\n\t";
            if(($invoice->Status == 2 || $invoice->Status == 3) && 0 < deformat_money($invoice->PartPayment)) {
                $collectionDialog = "";
                if($invoice->SubStatus == "") {
                    $collectionDialog = ["title" => __("payment process dialog title"), "dialogClass" => "paymentprocess", "description" => __("payment process dialog description") . "<br />", "dialogButtonText" => __("payment process dialog process"), "action" => "paymentprocesspause", "buttonClass" => "button1", "extraInput" => ""];
                } elseif($invoice->SubStatus == "PAUSED") {
                    $collectionDialog = ["title" => __("payment process dialog title stop"), "dialogClass" => "paymentprocess", "description" => __("payment process dialog description stop") . "<br />", "dialogButtonText" => __("payment process dialog process stop"), "action" => "paymentprocessreactivate", "buttonClass" => "button2", "extraInput" => "<label><input type=\"checkbox\" name=\"paymentprocess_imsure\" id=\"paymentprocess_imsure\" value=\"yes\"/> " . __("imsure stop collection") . "</label><br /><br />"];
                }
                if(isset($collectionDialog)) {
                    echo "\t \t\t<div id=\"dialog_";
                    echo $collectionDialog["dialogClass"];
                    echo "\" class=\"hide\" title=\"";
                    echo $collectionDialog["title"];
                    echo "\">\n\t\t\t\t<form name=\"form_paymentprocess\" method=\"post\" action=\"invoices.php?page=show&action=";
                    echo $collectionDialog["action"];
                    echo "&id=";
                    echo $invoice->Identifier;
                    echo "\">\n\t\t\t\t<input type=\"hidden\" name=\"id\" value=\"";
                    echo $invoice->Identifier;
                    echo "\"/>\n\t\t\t\t";
                    echo $collectionDialog["description"];
                    echo "<br />\n\t\t\t\t";
                    echo $collectionDialog["extraInput"];
                    echo "\t\t\t\t\n\t\t\t\t<p><a class=\"";
                    echo $collectionDialog["buttonClass"];
                    echo " alt1 float_left\" onclick=\"if(\$('#paymentprocess_imsure').html() == null || \$('#paymentprocess_imsure:checked').val() != undefined){ \$('#paymentprocess_loader_download').show(); \$(this).hide(); }\" id=\"paymentprocess_btn\"><span>";
                    echo $collectionDialog["dialogButtonText"];
                    echo "</span></a></p>\n\t\t\t\t<p><a class=\"c1 a1 float_right\" onclick=\"\$('#dialog_paymentprocess').dialog('close');\"><span>";
                    echo __("cancel");
                    echo "</span></a></p>\n\t\t\t\t<span id=\"paymentprocess_loader_download\" class=\"hide\">\n\t\t\t\t\t<img src=\"images/icon_circle_loader_green.gif\" class=\"ico inline\" style=\"margin-bottom:-3px;\" />&nbsp;&nbsp;\n\t\t\t\t\t<span class=\"loading_green\">";
                    echo __("loading");
                    echo "</span>\n\t\t\t\t</span>\n\t\t\t\t</form>\n\t\t\t</div>\n\t\t\t";
                }
            }
            if((int) $invoice->Status === 0) {
                $blockDialog = "";
                if($invoice->SubStatus == "") {
                    $blockDialog = ["title" => __("block send dialog title"), "dialogClass" => "block", "description" => __("block send dialog description") . "<br />", "dialogButtonText" => __("block send dialog process"), "action" => "block", "buttonClass" => "button1"];
                } elseif($invoice->SubStatus == "BLOCKED") {
                    $blockDialog = ["title" => __("unblock send dialog title"), "dialogClass" => "block", "description" => __("unblock send dialog description") . "<br />", "dialogButtonText" => __("unblock send dialog process"), "action" => "unblock", "buttonClass" => "button1"];
                }
                if(isset($blockDialog)) {
                    echo "\t \t\t<div id=\"dialog_";
                    echo $blockDialog["dialogClass"];
                    echo "\" class=\"hide\" title=\"";
                    echo $blockDialog["title"];
                    echo "\">\n\t\t\t\t<form name=\"form_block\" method=\"post\" action=\"invoices.php?page=show&action=";
                    echo $blockDialog["action"];
                    echo "&id=";
                    echo $invoice->Identifier;
                    echo "\">\n\t\t\t\t<input type=\"hidden\" name=\"id\" value=\"";
                    echo $invoice->Identifier;
                    echo "\"/>\n\t\t\t\t";
                    echo $blockDialog["description"];
                    echo "<br />\n\t\t\t\t<p>\n                    <a class=\"";
                    echo $blockDialog["buttonClass"];
                    echo " alt1 float_left\" onclick=\"\$(this).hide();\" id=\"block_btn\">\n                        <span>";
                    echo $blockDialog["dialogButtonText"];
                    echo "</span>\n                    </a>\n                </p>\n\t\t\t\t<p>\n                    <a class=\"c1 a1 float_right\" onclick=\"\$('#dialog_";
                    echo $blockDialog["dialogClass"];
                    echo "').dialog('close');\">\n                        <span>";
                    echo __("cancel");
                    echo "</span>\n                    </a>\n                </p>\n\t\t\t\t</form>\n\t\t\t</div>\n\t\t\t";
                }
                $scheduleDialog = "";
                if($invoice->SentDate == "0000-00-00 00:00:00") {
                    $scheduleDialog = ["title" => __("schedule send dialog title"), "dialogClass" => "schedule", "description" => __("schedule send dialog description") . "<br />", "dialogButtonText" => __("schedule send dialog process"), "action" => "schedule", "buttonClass" => "button1"];
                    if($invoice->InvoiceMethod != "0") {
                        $scheduleDialog["description"] = sprintf(__("schedule send dialog description manual"), strtolower($array_invoicemethod[$invoice->InvoiceMethod])) . "<br />";
                    }
                } else {
                    $scheduleDialog = ["title" => __("undo schedule send dialog title"), "dialogClass" => "schedule", "description" => __("undo schedule send dialog description") . "<br />", "dialogButtonText" => __("undo schedule send dialog process"), "action" => "undo_schedule", "buttonClass" => "button1"];
                }
                if(isset($scheduleDialog)) {
                    echo "\t\t\t<div id=\"dialog_";
                    echo $scheduleDialog["dialogClass"];
                    echo "\" class=\"hide\" title=\"";
                    echo $scheduleDialog["title"];
                    echo "\">\n\t\t\t\t<form name=\"form_schedule\" method=\"post\" action=\"invoices.php?page=show&action=";
                    echo $scheduleDialog["action"];
                    echo "&id=";
                    echo $invoice->Identifier;
                    echo "\">\n\t\t\t\t\t<input type=\"hidden\" name=\"id\" value=\"";
                    echo $invoice->Identifier;
                    echo "\"/>\n                    ";
                    echo $scheduleDialog["description"];
                    echo "<br />\n\n                    ";
                    if($invoice->SentDate == "0000-00-00 00:00:00") {
                        $default_date = new DateTime("+1 day");
                        echo "\t\t\t\t\t\t<strong>";
                        echo __("schedule send dialog datetime label");
                        echo "</strong>\n\t\t\t\t\t\t<br/>\n\n\t\t\t\t\t\t<span class=\"title2 lineheight_input\">";
                        echo __("date");
                        echo "</span>\n\t\t\t\t\t\t<span class=\"title2_value\"><input type=\"text\" class=\"text1 datepicker\" name=\"Date\" value=\"";
                        echo rewrite_date_db2site($default_date->format("Y-m-d"));
                        echo "\" data-dp-mindate=\"";
                        echo rewrite_date_db2site(date("Y-m-d"));
                        echo "\" tabindex=\"-1\" style=\"width:108px;\"></span>\n\n\n\t\t\t\t\t\t<span class=\"title2 lineheight_input\">";
                        echo __("time");
                        echo "</span>\n\t\t\t\t\t\t<span class=\"title2_value\">\n\t\t\t\t\t\t\t<select class=\"text1\" name=\"Hours\">\n\t\t\t\t\t\t\t\t";
                        $current_automatic_sending_time = explode(":", "09:00", 2);
                        for ($i = 0; $i <= 23; $i++) {
                            $hours = str_pad($i, 2, "0", STR_PAD_LEFT);
                            $selected = isset($current_automatic_sending_time[0]) && $current_automatic_sending_time[0] == $hours ? " selected=\"selected\"" : "";
                            echo "\t\t\t\t\t\t\t\t\t<option value=\"";
                            echo $hours;
                            echo "\"";
                            echo $selected;
                            echo ">";
                            echo $hours;
                            echo "</option>";
                        }
                        echo "\t\t\t\t\t\t\t</select> : <select class=\"text1\" name=\"Minutes\">\n\t\t\t\t\t\t\t\t";
                        foreach (["00", "15", "30", "45"] as $minutes) {
                            $selected = isset($current_automatic_sending_time[1]) && $current_automatic_sending_time[1] == $minutes ? " selected=\"selected\"" : "";
                            echo "\t\t\t\t\t\t\t\t\t<option value=\"";
                            echo $minutes;
                            echo "\"";
                            echo $selected;
                            echo ">";
                            echo $minutes;
                            echo "</option>";
                        }
                        echo "\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t</span>\n\n                        ";
                    }
                    echo "\n\t\t\t\t\t<p>\n\t\t\t\t\t\t<a class=\"";
                    echo $scheduleDialog["buttonClass"];
                    echo " alt1 float_left button_has_loader\" id=\"schedule_btn\">\n\t\t\t\t\t\t\t<span>";
                    echo $scheduleDialog["dialogButtonText"];
                    echo "</span>\n\t\t\t\t\t\t</a>\n\t\t\t\t\t\t<span class=\"button_loader_span hide float_left\" style=\"line-height:32px\">\n\t\t\t\t\t\t\t<img src=\"images/icon_circle_loader_green.gif\" alt=\"\" class=\"ico inline\" style=\"margin-bottom:-3px;\" />&nbsp;&nbsp;\n\t\t\t\t\t\t\t<span class=\"loading_green\">";
                    echo __("loading");
                    echo "</span>\n\t\t\t\t\t\t</span>\n\t\t\t\t\t</p>\n\t\t\t\t\t<p>\n\t\t\t\t\t\t<a class=\"c1 a1 float_right\" onclick=\"\$('#dialog_";
                    echo $scheduleDialog["dialogClass"];
                    echo "').dialog('close');\">\n\t\t\t\t\t\t\t<span>";
                    echo __("cancel");
                    echo "</span>\n\t\t\t\t\t\t</a>\n\t\t\t\t\t</p>\n\t\t\t\t</form>\n\t\t\t</div>\n            ";
                }
            }
            if($invoice->Status == 2 || $invoice->Status == 3) {
                echo "        <div id=\"dialog_paid_confirm\" class=\"hide\" title=\"";
                echo __("invoice paid confirm dialog title");
                echo "\">\n        \n            <form name=\"form_paid_confirm\" method=\"post\" action=\"invoices.php?page=show&action=markaspaid&id=";
                echo $invoice->Identifier;
                echo "\">\n                <p tabindex=\"1\"></p>                 <p>\n                    ";
                echo sprintf(__("confirm paid"), $invoice->InvoiceCode);
                echo "                    <br /><br />\n                    <strong>";
                echo __("enter paid date");
                echo "</strong>\n                    <br />\n                    <span class=\"input_date\">\n                        <input type=\"text\" class=\"text1 size6 datepicker\" name=\"PayDate\" value=\"";
                echo rewrite_date_db2site(date("Y-m-d"));
                echo "\" />\n                    </span>\n                </p>\n                <br />\n                <p>\n                    <a class=\"button1 alt1 float_left\" onclick=\"\$(this).hide();\" id=\"confirm_paid_btn\">\n                        <span>";
                echo __("action paid");
                echo "</span>\n                    </a>\n                </p>\n                <p>\n                    <a class=\"c1 a1 float_right\" onclick=\"\$('#dialog_paid_confirm').dialog('close');\">\n                        <span>";
                echo __("cancel");
                echo "</span>\n                    </a>\n                </p>\n            </form>\n            \n        </div>\n        ";
            }
        }
        echo "\n";
        if(U_INVOICE_DELETE) {
            echo "\t";
            if((int) $invoice->Status === 0 && substr($invoice->InvoiceCode, 0, 8) == "[concept") {
                echo "\t<div id=\"credit_invoice\" class=\"hide\" title=\"";
                echo __("delete concept invoice title");
                echo "\">\n\t\t<form id=\"DebtorForm\" name=\"form_credit\" method=\"post\" action=\"invoices.php?page=overview&action=delete&id=";
                echo $invoice->Identifier;
                echo "\">\n\t\t<input type=\"hidden\" name=\"id\" value=\"";
                echo $invoice->Identifier;
                echo "\"/>\n\t\t\n\t\t";
                echo sprintf(__("delete concept invoice description"), $invoice->InvoiceCode);
                echo "<br /><br />\n\t\t\n\t\t<input type=\"checkbox\" name=\"imsure\" id=\"imsure\" value=\"yes\"/> <label for=\"imsure\">";
                echo __("delete this concept invoice");
                echo "</label><br />\n\t\t<br />\n\t\t\n\t\t<p><a id=\"credit_invoice_btn\" class=\"button2 alt1 float_left\"><span>";
                echo __("delete");
                echo "</span></a></p>\n\t\t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#credit_invoice').dialog('close');\"><span>";
                echo __("cancel");
                echo "</span></a></p>\n\t\n\t\t</form>\n\t</div>\n\t";
            } else {
                echo "\t<div id=\"credit_invoice\" class=\"hide\" title=\"";
                echo __("credit invoice title");
                echo "\">\n\t\t<form id=\"DebtorForm\" name=\"form_credit\" method=\"post\" action=\"invoices.php?page=overview&action=delete&id=";
                echo $invoice->Identifier;
                echo "\">\n\t\t<input type=\"hidden\" name=\"id\" value=\"";
                echo $invoice->Identifier;
                echo "\"/>\n\t\t<input type=\"hidden\" name=\"imsure\" value=\"yes\"/>\n\n\t\t";
                if(isset($related_invoices) && 0 < $related_invoices["CountRows"]) {
                    echo "<div class=\"mark blue\">";
                    echo __("this invoice has been credited before");
                    echo "<br />\n\t\t\t";
                    foreach ($related_invoices as $_k => $_inv) {
                        if(is_numeric($_k)) {
                            echo "&bull; <a class=\"a1\" href=\"invoices.php?page=show&id=" . $_inv["id"] . "\">" . $_inv["InvoiceCode"] . "</a><br />";
                        }
                    }
                    echo "</div>";
                }
                echo "\n\t\t<strong>";
                echo __("confirm your action");
                echo "</strong><br />\n\t\t";
                echo sprintf(__("credit invoice description"), $invoice->InvoiceCode);
                echo "\t\t<br/><br/>\n\t\t";
                if(isset($invoice->Elements["CountRows"]) && 2 <= $invoice->Elements["CountRows"]) {
                    echo "\t\t\t<p>\n\t\t\t\t<strong>";
                    echo __("credit invoice what to credit");
                    echo "</strong>\n\t\t\t</p>\n\t\t\t<label>\n\t\t\t\t<input type=\"radio\" name=\"credit\" id=\"credit_whole\" value=\"credit_whole\" checked/> ";
                    echo __("credit invoice whole invoice");
                    echo "\t\t\t</label>\n\t\t\t<br/>\n\t\t\t<label>\n\t\t\t\t<input type=\"radio\" name=\"credit\" id=\"credit_partially\" value=\"credit_partially\"/> ";
                    echo __("credit invoice some lines");
                    echo "\t\t\t</label>\n\t\t\t<br/>\n\t\t\t<br/>\n\t\t\t";
                }
                echo "\t\t<p>\n\t\t\t<a id=\"credit_invoice_btn\" class=\"credit_btn button1 alt1 float_left\">\n\t\t\t\t<span class=\"credit_button_text\">";
                echo __("credit");
                echo "</span>\n\t\t\t\t<span class=\"credit_partial_button_text hide\">";
                echo __("credit invoice some lines btn");
                echo "</span>\n\t\t\t</a>\n\t\t</p>\n\t\t<p>\n\t\t\t<a class=\"a1 c1 float_right\" onclick=\"\$('#credit_invoice').dialog('close');\"><span>";
                echo __("cancel");
                echo "</span></a>\n\t\t</p>\n\t\n\t\t</form>\n\t</div>\n\t";
            }
        }
        echo "\n";
        if(isset($selected_tab) && $selected_tab) {
            echo "<script language=\"javascript\" type=\"text/javascript\">\n\$(function(){\n\t\$('#tabs').tabs(\"option\", \"active\", ";
            echo strlen($invoice->Description) === 0 ? $selected_tab : $selected_tab + 1;
            echo ");\n});\n</script>\n";
        }
        echo "\n";
        require_once "views/footer.php";
    } else {
        $icon = "";
        switch ($value["Action"]) {
            case "invoice printed":
            case "invoice sent per post":
            case "reminder sent per post":
            case "summation sent per post":
            case "invoice downloaded via api":
            case "invoice downloaded via clientarea":
                $icon = "ico_printsmall.png";
                break;
            case "invoice adjusted":
            case "comment adjusted":
            case "invoice created":
            case "invoice created - from credit":
            case "invoice created - from partly credit":
                $icon = "ico_edit.png";
                break;
            case "invoice sent per email":
            case "reminder sent per email":
            case "summation sent per email":
            case "invoice payment notification":
                $icon = "ico_sendemail.png";
                break;
            case "invoice paid partly":
            case "invoice paid partly on":
            case "invoice paid partly via package":
            case "invoice paid via package":
            case "log online payment succeeded":
            case "log online payment failed":
                $icon = "ico_money.png";
                break;
            case "invoice paid":
                $icon = "ico_success.png";
                break;
            case "invoice paid on":
                $icon = "ico_paid_small.png";
                break;
            case "invoice unpaid":
                $icon = "ico_unpaid.png";
                break;
            case "invoice expired":
            case "invoice partly credited":
                $icon = "ico_credit_small.png";
                break;
            case "invoice created from pricequote x":
                $icon = "ico_quote2invoice.png";
                break;
            case "invoicemethod and emailaddress changed":
            case "invoicemethod changed":
                $icon = "ico_sendmethodchanged.png";
                break;
            case "draft invoice scheduled":
                $icon = "ico_schedule.png";
                break;
            case "draft invoice scheduled undone":
            case "draft invoice scheduled undone edit":
                $icon = "ico_unschedule.png";
                break;
            default:
                if(strpos($value["Action"], "Exact") !== false) {
                    $icon = "ico_export_exact.png";
                } elseif(strpos($value["Action"], "Twinfield") !== false) {
                    $icon = "ico_export_twinfield.png";
                }
                echo "\t\t\t\t\n\t\t\t\t<div class=\"log\">\n\t\t\t\t\t";
                if($icon) {
                    echo "<img src=\"images/";
                    echo $icon;
                    echo "\" style=\"width:12px; height:12px;\" alt=\"\" />";
                } else {
                    echo "<span style=\"width:16px;display:inline-block;\">&nbsp;</span>";
                }
                echo "\t\t\t\t\t<span style=\"width: 162px; overflow:hidden;\">\n\t\t\t\t\t\t";
                echo rewrite_date_db2site($value["Date"], "%d-%m-%Y " . __("at") . " %H:%i");
                echo "<br />\n\t\t\t\t\t\t";
                if($value["Translate"] == "no") {
                    echo $value["Action"];
                } else {
                    $value["Action"] = __("log." . $value["Action"]);
                    $value["Values"] = explode("|", $value["Values"]);
                    if(strpos($value["Action"], "%s") && count($value["Values"]) < count(explode("%s", $value["Action"])) - 1) {
                        echo $value["Action"];
                    } else {
                        echo call_user_func_array("sprintf", array_merge([$value["Action"]], $value["Values"]));
                    }
                }
                echo "\t\t\t\t\t</span>\n\t\t\t\t\t<div class=\"clear\">&nbsp;</div>\n\t\t\t\t</div>\n\t\t\t\t\n\t\t\t\t<hr />\n\t\t\t\t\n\t\t\t\t";
                $logcounter++;
        }
    }
}

?>