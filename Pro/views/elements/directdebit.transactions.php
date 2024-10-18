<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
function show_directdebit_transactions($transactions, $options = [])
{
    $table_type = isset($options["table_type"]) ? $options["table_type"] : "";
    $session_name = isset($options["session_name"]) ? $options["session_name"] : "";
    $supported_actions = isset($options["actions"]) ? $options["actions"] : [];
    $hide_columns = isset($options["hide_columns"]) ? $options["hide_columns"] : [];
    $column_count = 10 - count($hide_columns);
    global $sdd_batch_id;
    global $emailtemplates;
    $session = $_SESSION[$session_name];
    echo "\t\n\t<form action=\"?page=show&amp;id=";
    echo $sdd_batch_id;
    echo "\" method=\"post\" name=\"form_SDD_";
    echo $table_type;
    echo "\" id=\"form_SDD_";
    echo $table_type;
    echo "\">\n\n\t<div id=\"SubTable_SDD_";
    echo $table_type;
    echo "\">\n\t\t<table cellspacing=\"0\" cellpadding=\"0\" class=\"table1\" id=\"MainTable_SDD_";
    echo $table_type;
    echo "\">\n\t\t<tbody>\n\t\t\t<tr class=\"trtitle\">\n\t\t\t\t<th scope=\"col\">";
    if(!empty($supported_actions)) {
        echo "<label><input type=\"checkbox\" value=\"on\" class=\"BatchCheck\" name=\"InvoiceBatch";
        echo $table_type;
        echo "\" /></label> ";
    }
    echo "<span style=\"padding-left:18px\">";
    echo __("invoice no");
    echo "</span></th>\n\t\t\t\t<th colspan=\"3\" scope=\"col\">";
    echo __("amount");
    echo "</th>\n\t\t\t\t<th scope=\"col\">";
    echo __("debtor");
    echo "</th>\n\t\t\t\t<th scope=\"col\">";
    echo __("sdd iban");
    echo "</th>\n\t\t\t\t";
    if(!in_array("Status", $hide_columns)) {
        echo "<th>";
        echo __("status");
        echo "</th>";
    }
    echo "\t\t\t\t<th style=\"width: 30px;\">&nbsp;</th>\n\t\t\t</tr>\n\t\t\t";
    $transactionCounter = 0;
    foreach ($transactions as $tmp_transaction) {
        if($table_type == "all" || $tmp_transaction["Status"] == $table_type) {
            $transactionCounter++;
            $status_class = "deleted";
            $status_popup = "";
            if($table_type == "draft" && $tmp_transaction["Status"] == "draft") {
                $status_class = "busy";
                $status_popup = __("sdd invoice status draft");
            } elseif($table_type == "success") {
                $status_class = "active";
                $status_popup = __("sdd invoice status success");
            } elseif($table_type == "failed") {
                $status_class = "error";
                $status_popup = __("sdd invoice status failed") . ": " . htmlspecialchars($tmp_transaction["Reason"]);
            } elseif(!$tmp_transaction["MandateID"]) {
                $status_class = "error";
                $status_popup = __("sdd error no mandate");
            }
            if($table_type == "all") {
                $tmp_transaction["AuthTrials"] += 1;
            }
            echo "\t\t\t\t\t<tr class=\"hover_extra_info ";
            if($transactionCounter % 2 === 1) {
                echo "tr1";
            }
            echo "\">\n\t\t\t\t\t\t<td>";
            if(!empty($supported_actions)) {
                echo "<input type=\"checkbox\" value=\"";
                echo $tmp_transaction["id"];
                echo "\" class=\"InvoiceBatch";
                echo $table_type;
                echo "\" name=\"BatchElements[]\" /> ";
            }
            echo "\t\t\t\t\t\t<span class=\"inline_status ";
            echo $status_class;
            echo " infopopuptop delaypopup\">&nbsp;";
            if($status_popup) {
                echo "<span class=\"popup\">";
                echo $status_popup;
                echo "<b></b></span>";
            }
            echo "</span>\n\t\t\t\t\n\t\t\t\t\t\t<a class=\"c1 a1\" href=\"invoices.php?page=show&amp;id=";
            echo $tmp_transaction["InvoiceID"];
            echo "\">";
            echo $tmp_transaction["InvoiceCode"];
            echo "</a>\n\t\t\t\t\t\t";
            if(1 < $tmp_transaction["AuthTrials"]) {
                echo "<span class=\"fontsmall c4\">";
                echo sprintf(__("number of authtrial"), $tmp_transaction["AuthTrials"]);
                echo "</span>";
            }
            echo "</td>\n\t\t\t\t\t\t<td style=\"width: 5px;\" class=\"currency_sign_left\">";
            echo currency_sign_td(CURRENCY_SIGN_LEFT);
            echo "</td>\n\t\t\t\t\t\t<td style=\"width: 60px;\" align=\"right\">";
            echo money($tmp_transaction["Amount"], false);
            echo "</td>\n\t\t\t\t\t\t<td style=\"width: 45px;\" class=\"currency_sign_right\">";
            echo currency_sign_td(CURRENCY_SIGN_RIGHT);
            echo "</td>\n\t\t\t\t\t\t<td><a href=\"debtors.php?page=show&id=";
            echo $tmp_transaction["Debtor"];
            echo "\" class=\"a1\">";
            echo $tmp_transaction["CompanyName"] ? htmlspecialchars($tmp_transaction["CompanyName"]) : htmlspecialchars($tmp_transaction["SurName"] . ", " . $tmp_transaction["Initials"]);
            echo "</a></td>\n\t\t\t\t\t\t<td>";
            echo htmlspecialchars($tmp_transaction["IBAN"]);
            echo "</td>\n\t\t\t\t\t\t";
            if(!in_array("Status", $hide_columns)) {
                echo "<td>";
                echo __("sdd invoice status " . $tmp_transaction["Status"]);
                echo "</td>";
            }
            echo "\t\t\t\t\t\t<td>&nbsp;<span class=\"ico actionblock tag nm hover_extra_info_span\">";
            echo __("more information");
            echo "</span></td>\n\t\t\t\t\t</tr>\n\t\t\t\t\t<tr class=\"tr_extra_info mark2\">\n\t\t\t\t\t\t<td colspan=\"4\" style=\"padding-left:33px;\">\n\t\t\t\t\t\t\t";
            echo __("invoice date");
            echo ": ";
            echo rewrite_date_db2site($tmp_transaction["InvoiceDate"]);
            echo "\t\t\t\t\t\t</td>\n\t\t\t\t\t\t<td colspan=\"";
            echo $column_count - 4;
            echo "\">\n\t\t\t\t\t\t\t<label style=\"display:inline-block;width: 120px;\">";
            echo __("sdd mandate id");
            echo ":</label> ";
            echo htmlspecialchars($tmp_transaction["MandateID"]);
            echo "<br />\n\t\t\t\t\t\t\t<label style=\"display:inline-block;width: 120px;\">";
            echo __("sdd mandate date");
            echo ":</label> ";
            echo rewrite_date_db2site($tmp_transaction["MandateDate"]);
            echo "\t\t\t\t\t\t</td>\n\t\t\t\t\t</tr>\n\t\t\t\t\t";
        }
    }
    if($transactionCounter === 0) {
        echo "\t\t\t\t<tr>\n\t\t\t\t\t<td colspan=\"";
        echo $column_count;
        echo "\">\n\t\t\t\t\t";
        echo __("sdd no invoices in batch");
        echo "\t\t\t\t\t</td>\n\t\t\t\t</tr>\n\t\t\t\t";
    }
    if(0 < $transactionCounter && !empty($supported_actions)) {
        echo "\t\t\t\t<tr class=\"table_options\">\n\t\t\t\t\t<td colspan=\"";
        echo $column_count;
        echo "\">\n\t\t\t\t\t\t<p class=\"ico inline hook\">\n\t\t\t\t\t\t\t<select class=\"select1 BatchSelect\" name=\"action\">\n\t\t\t\t\t\t\t\t<option selected=\"selected\">";
        echo __("with selected");
        echo "</option>\n\t\t\t\t\t\t\t\t";
        if(in_array("remove", $supported_actions)) {
            echo "<option value=\"dialog:remove\">";
            echo __("sdd action remove direct debit from invoice");
            echo "</option>";
        }
        echo "\t\t\t\t\t\t\t\t";
        if(in_array("move", $supported_actions)) {
            echo "<option value=\"dialog:move\">";
            echo __("sdd action move to next batch");
            echo "</option>";
        }
        echo "\t\t\t\t\t\t\t\t";
        if(in_array("paid", $supported_actions)) {
            echo "<option value=\"dialog:paid\">";
            echo __("sdd action mark as paid");
            echo "</option>";
        }
        echo "\t\t\t\t\t\t\t\t";
        if(in_array("failed", $supported_actions)) {
            echo "<option value=\"dialog:failed\">";
            echo __("sdd action mark as unpaid");
            echo "</option>";
        }
        echo "\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t</p>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t";
        if(in_array("remove", $supported_actions)) {
            echo "\t\t\t\t\t\t\t<div id=\"dialog_remove\" class=\"hide\">\n\t\t\t\t\t\t\t\t";
            echo __("sdd actiondialog remove direct debit title");
            echo "<br />\n\t\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t\t<strong>";
            echo __("sdd actiondialog remove direct debit what");
            echo "</strong><br />\n\t\t\t\t\t\t\t\t<label><input type=\"radio\" name=\"RemoveFrom\" value=\"invoice\" checked=\"checked\"/> ";
            echo __("sdd actiondialog remove direct debit invoice only");
            echo "</label><br />\n\t\t\t\t\t\t\t\t<label><input type=\"radio\" name=\"RemoveFrom\" value=\"debtor\" /> ";
            echo __("sdd actiondialog remove direct debit debtor");
            echo "</label><br />\n\t\t\t\t\t\t\t</div>\t\n\t\t\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t";
        if(in_array("move", $supported_actions)) {
            echo "\t\t\t\t\t\t\t<div id=\"dialog_move\" class=\"hide\">\n\t\t\t\t\t\t\t\t";
            echo __("sdd actiondialog move to next batch title");
            echo "<br />\n\t\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t\t<strong>";
            echo __("sdd actiondialog debtor notification");
            echo "</strong><br />\n\t\t\t\t\t\t\t \t<label><input type=\"checkbox\" name=\"NotifyDebtor\" value=\"yes\"/> ";
            echo __("sdd actiondialog notify debtor moved date");
            echo "</label><br />\n\t\t\t\t\t\t\t \t\n\t\t\t\t\t\t\t\t<div class=\"notify_debtor_div hide\"> \n\t\t\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t\t \t<strong>";
            echo __("sdd actiondialog debtor notification email");
            echo "</strong><br />\n\t\t\t\t\t\t\t\t \t<select class=\"text1 size4f\" name=\"NotifyMail\">\n\t\t\t\t\t\t\t\t \t\t<option value=\"\">";
            echo __("please choose");
            echo "</option>\n\t\t\t\t\t\t\t\t\t\t";
            foreach ($emailtemplates as $k => $v) {
                if(is_numeric($k)) {
                    echo "\t\t\t\t\t\t\t\t\t\t<option value=\"";
                    echo $k;
                    echo "\" ";
                    if(SDD_MOVED_MAIL == $k) {
                        echo "selected=\"selected\"";
                    }
                    echo ">";
                    echo $v["Name"];
                    echo "</option>\n\t\t\t\t\t\t\t\t\t\t";
                }
            }
            echo "\t\t\t\t\t\t\t\t\t</select><br />\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t";
        if(in_array("paid", $supported_actions)) {
            echo "\t\t\t\t\t\t\t<div id=\"dialog_paid\" class=\"hide\">\n\t\t\t\t\t\t\t\t";
            echo __("sdd actiondialog mark as paid title");
            echo "<br />\n\t\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t";
        if(in_array("failed", $supported_actions)) {
            echo "\t\t\t\t\n\t\t\t\t\t\t\t<div id=\"dialog_failed\" class=\"hide\">\n\t\t\t\t\t\t\t\t";
            echo __("sdd actiondialog failed title");
            echo "<br />\n\t\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<strong>";
            echo __("sdd actiondialog failed reason");
            echo "</strong><br />\n\t\t\t\t\t\t\t \t<input name=\"Reason\" type=\"text\" class=\"text1 size1\" /><br />\n\t\t\t\t\t\t\t \t<br />\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<strong>";
            echo __("sdd actiondialog failed which action to take");
            echo "</strong><br />\n\t\t\t\t\t\t\t\t<label><input type=\"radio\" name=\"InvoiceAction\" value=\"move_next\" checked=\"checked\"/> ";
            echo __("sdd actiondialog failed action move");
            echo "</label><br />\n\t\t\t\t\t\t\t \t<label><input type=\"radio\" name=\"InvoiceAction\" value=\"remove_invoice\" /> ";
            echo __("sdd actiondialog failed action remove from invoice");
            echo "</label><br />\n\t\t\t\t\t\t\t\t<label><input type=\"radio\" name=\"InvoiceAction\" value=\"remove_debtor\" /> ";
            echo __("sdd actiondialog failed action remove from debtor");
            echo "</label><br />\n\t\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t \t\n\t\t\t\t\t\t\t \t<strong>";
            echo __("sdd actiondialog debtor notification failed direct debit");
            echo "</strong><br />\n\t\t\t\t\t\t\t \t<label><input type=\"checkbox\" name=\"NotifyDebtor\" value=\"yes\"/> ";
            echo __("sdd actiondialog notify debtor failed direct debit");
            echo "</label><br />\n\t\t\t\t\t\t\t \t<div class=\"notify_debtor_div hide\"> \n\t\t\t\t\t\t\t\t \t<br />\n\t\t\t\t\t\t\t\t \t<strong>";
            echo __("sdd actiondialog debtor notification email failed direct debit");
            echo "</strong><br />\n\t\t\t\t\t\t\t\t \t<select class=\"text1 size4f\" name=\"NotifyMail\">\n\t\t\t\t\t\t\t\t \t\t<option value=\"\">";
            echo __("please choose");
            echo "</option>\n\t\t\t\t\t\t\t\t\t\t";
            foreach ($emailtemplates as $k => $v) {
                if(is_numeric($k)) {
                    echo "\t\t\t\t\t\t\t\t\t\t<option value=\"";
                    echo $k;
                    echo "\" ";
                    if(SDD_FAILED_MAIL == $k) {
                        echo "selected=\"selected\"";
                    }
                    echo ">";
                    echo $v["Name"];
                    echo "</option>\n\t\t\t\t\t\t\t\t\t\t";
                }
            }
            echo "\t\t\t\t\t\t\t\t\t</select><br />\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t</div>\t\n\t\t\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\t\n\t\t\t\t\t</td>\n\t\t\t\t</tr>\n\t\t\t\t";
    }
    echo "\t\t</tbody>\n\t\t</table>\n\t</div>\n\t</form>\n\t<br /><br />\n\t";
}

?>