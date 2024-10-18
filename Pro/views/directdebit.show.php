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
if(!($batch_info->Status == "draft" || $batch_info->Status == "downloadable")) {
    $counter_draft = $counter_success = $counter_failed = 0;
    foreach ($transactions as $tmp_transaction) {
        switch ($tmp_transaction["Status"]) {
            case "draft":
                $counter_draft++;
                break;
            case "success":
                $counter_success++;
                break;
            case "failed":
                $counter_failed++;
                break;
        }
    }
}
echo "\n<ul class=\"list1\">\n\t";
if(in_array($batch_info->Status, ["draft", "downloadable"])) {
    echo "\t\t<li><a class=\"ico set1 downloadbatch\" href=\"?page=download&id=";
    echo $sdd_batch_id;
    echo "\">";
    echo __("sdd action button download batch");
    echo "</a></li>\n\t";
}
echo "\t";
if(in_array($batch_info->Status, ["downloaded", "processed", "rejected"])) {
    echo "\t\t<li><a class=\"ico set1 downloadbatch large_actionname\" href=\"?page=download&id=";
    echo $sdd_batch_id;
    echo "\">";
    echo __("sdd action button redownload batch");
    echo "</a></li>\n\t";
}
echo "\t";
if(in_array($batch_info->Status, ["downloaded"])) {
    echo "\t\t\n\t\t";
    if($counter_success === 0 && $counter_failed === 0) {
        echo "\t\t\t<li><a class=\"ico set1 credit large_actionname\" href=\"?page=draft&id=";
        echo $sdd_batch_id;
        echo "\">";
        echo __("sdd action button back to draft");
        echo "</a></li>\n\t\t";
    }
    echo "\t\t\n\t";
}
echo "\t";
if(in_array($batch_info->Status, ["draft", "downloadable"])) {
    echo "\t\t<li>\n\t\t\t<a onclick=\"\$('#batch_delete_modal').dialog('open');\" class=\"inline_modal_link ico set1 decline\">\n\t\t\t\t";
    echo __("sdd action button delete batch");
    echo "\t\t\t</a>\n\t\t</li>\n\t\t";
}
echo "</ul>\n\n<hr />\n\n";
echo $message;
echo "\n<!--heading1-->\n<div class=\"heading1\">\n<!--heading1-->\n\n\t<h2>";
echo __("sdd batch");
echo " ";
echo $batch_info->BatchID;
echo "</h2>\n\t\n\t<p class=\"pos2\">\n\t\t<strong class=\"textsize1\">";
echo __("sdd batch status " . $batch_info->Status);
echo "</strong>\n\t</p>\n\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n\n\n\t<!--box1-->\n\t<div class=\"box2\" id=\"tabs\">\n\t<!--box1-->\n\t\n\t\t<!--top-->\n\t\t<div class=\"top\">\n\t\t<!--top-->\n\t\t\n\t\t\t<ul class=\"list3\">\n\t\t\t\t<li class=\"on\"><a href=\"#tab-general\">";
echo __("sdd batch information");
echo "</a></li>\n\t\t\t\t<li><a href=\"#tab-logfile\">";
echo __("logfile");
echo "</a></li>\n\t\t\t</ul>\n\t\t\n\t\t<!--top-->\n\t\t</div>\n\t\t<!--top-->\n\n\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-general\">\n\t\t<!--content-->\n\n\t\t\t<!--split2-->\n\t\t\t<div class=\"split2\">\n\t\t\t<!--split2-->\n\t\t\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t<div class=\"left\">\n\t\t\t\t<!--left-->\t\n\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("general");
echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("sdd batch id");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
echo $batch_info->BatchID;
echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("sdd direct debit date");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">\n\t\t\t\t\t\t\t";
echo rewrite_date_db2site($batch_info->Date);
if(in_array($batch_info->Status, ["draft", "downloadable"])) {
    echo "&nbsp; <a onclick=\"\$('#BatchChangeDateDialog').dialog('open');\" class=\"a1 c1\">";
    echo __("sdd direct debit change date");
    echo "</a>\n\t\t\t\t\t\t\t\t<div id=\"BatchChangeDateDialog\" title=\"";
    echo __("sdd change date dialog title");
    echo "\" class=\"hide\">\n\t\t\t\t\t\t\t\t\t<form method=\"post\" name=\"BatchChangeDateDialogForm\" action=\"directdebit.php?page=change_date&amp;id=";
    echo $sdd_batch_id;
    echo "\">\n\t\t\t\t\t\t\t\t\t";
    echo sprintf(__("sdd change date dialog description"), $batch_info->ProcessingTime, rewrite_date_db2site($directdebit->nextWorkingDay(date("Y-m-d", strtotime("+" . $batch_info->ProcessingTime . " day")))));
    echo "\t\t\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t\t\t<strong>";
    echo __("sdd change date dialog new date");
    echo "</strong><br />\n\t\t\t\t\t\t\t\t\t<input type=\"input\" name=\"NewDate\" value=\"\" class=\"datepicker text1 size6\" tabindex=\"-1\"/><br />\n\t\t\t\t\t\t\t\t \t<br />\n\t\n\t\t\t\t\t\t\t\t    <p><a id=\"batch_change_date_btn\" class=\"button1 alt1 float_left\"><span>";
    echo __("sdd change date button");
    echo "</span></a></p>\n\t\t\t\t\t\t\t\t    <p><a class=\"a1 c1 float_right\" onclick=\"\$('#BatchChangeDateDialog').dialog('close');\"><span>";
    echo __("cancel");
    echo "</span></a></p>\n\t\t\t\t\t\t\t\t    <br clear=\"both\"/>\n\t\t\t\t\t\t\t\t    <br />\n\t\t\t\t\t\t\t\t    </form>\n\t\t\t\t\t\t\t\t</div>\t\n\t\t\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("sdd download date");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">\n\t\t\t\t\t\t\t";
if($batch_info->Status == "draft" || $batch_info->Status == "downloadable") {
    echo sprintf(__("sdd download before date"), rewrite_date_db2site($batch_info->DownloadDate));
    if($batch_info->Status == "downloadable") {
        echo "&nbsp; <a href=\"?page=download&id=";
        echo $sdd_batch_id;
        echo "\" class=\"a1 c1\">";
        echo __("sdd download batch now");
        echo "</a>";
    }
} else {
    echo rewrite_date_db2site($batch_info->DownloadDate);
}
echo "\t\t\t\t\t\t</span>\n\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t</div>\n\t\t\t\t<!--left-->\n\t\t\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t<div class=\"right\">\n\t\t\t\t<!--right-->\n\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("sdd batch information");
echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("sdd sepa id");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
echo $batch_info->SDD_ID;
echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("sdd iban");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
echo $batch_info->SDD_IBAN;
echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("sdd bic");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
echo $batch_info->SDD_BIC;
echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("sdd processing time");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
echo $batch_info->ProcessingTime;
echo " ";
echo __("days");
echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t</div>\n\t\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t<!--split2-->\n\t\t\t</div>\n\t\t\t<!--split2-->\n\t\t\t\t\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\t\t\t\n\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-logfile\">\n\t\t<!--content-->\n\t\t\t\n\t\t\t";
require_once "views/elements/log.table.php";
$options = ["form_action" => "directdebit.php?page=show&amp;id=" . $sdd_batch_id, "session_name" => "directdebit.show.logfile", "current_page" => $current_page, "current_page_url" => $current_page_url, "allow_delete" => U_INVOICE_DELETE];
show_log_table($list_directdebit_logfile, $options);
echo "\t\n\t\t\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\t\t\n\n\t<!--box1-->\n\t</div>\n\t<!--box1-->\n\t\n\t<br /><br /><br />\n\n\t<!--heading1-->\n\t<div class=\"heading1\">\n\t<!--heading1-->\n\t\n\t\t<h2>";
echo __("sdd invoices in batch h2");
echo " (";
echo $batch_info->Count;
echo ")</h2>\n\t\t\n\t\t<p class=\"pos2\">\n\t\t\t<strong class=\"textsize1\">";
echo money($batch_info->Amount);
echo "</strong>\n\t\t</p>\n\t\t\n\t\n\t<!--heading1-->\n\t</div><hr />\n\t<!--heading1-->\t\n\t";
require_once "views/elements/directdebit.transactions.php";
if($batch_info->Status == "draft" || $batch_info->Status == "downloadable") {
    $options = ["table_type" => "all", "actions" => ["remove", "move"], "hide_columns" => ["Status"]];
    show_directdebit_transactions($transactions, $options);
} else {
    if(0 < $counter_draft) {
        echo "\t\t\t<strong class=\"subtitle\">";
        echo __("sdd invoices to process h2");
        echo " (";
        echo $counter_draft;
        echo ")</strong>\t\t\n\t\t\t";
        $options = ["table_type" => "draft", "actions" => ["paid", "failed"], "hide_columns" => ["Status"]];
        show_directdebit_transactions($transactions, $options);
    }
    if(0 < $counter_success) {
        echo "\t\t\t<strong class=\"subtitle\">";
        echo __("sdd invoices successfully direct debit h2");
        echo " (";
        echo $counter_success;
        echo ")</strong>\t\t\n\t\t\t";
        $options = ["table_type" => "success", "actions" => ["failed"], "hide_columns" => ["Status"]];
        show_directdebit_transactions($transactions, $options);
    }
    if(0 < $counter_failed) {
        echo "\t\t\t<strong class=\"subtitle\">";
        echo __("sdd invoices failed direct debit h2");
        echo " (";
        echo $counter_failed;
        echo ")</strong>\t\t\n\t\t\t";
        $options = ["table_type" => "failed", "actions" => [], "hide_columns" => ["Status"]];
        show_directdebit_transactions($transactions, $options);
    }
}
echo "\t";
if(in_array($batch_info->Status, ["draft", "downloadable"])) {
    echo "\t\t<div id=\"batch_delete_modal\" class=\"hide\" title=\"";
    echo __("sdd delete batch");
    echo "\">\n\t\t\t<form name=\"batch_delete\" method=\"post\" action=\"?page=delete&id=";
    echo $sdd_batch_id;
    echo "\">\n\t\t\t\t<strong>";
    echo __("confirm your action");
    echo "</strong><br />\n\t\t\t\t";
    echo sprintf(__("sdd delete batch confirm text"), $batch_info->BatchID);
    echo "\t\t\t\t<br />\n\n\t\t\t\t";
    if($batch_info->Count == 1) {
        echo __("sdd delete batch invoice in next batch") . "<br /><br />";
    } elseif(0 < $batch_info->Count) {
        echo __("sdd delete batch invoices in batch") . "<br /><br />";
    }
    echo "\n\t\t\t\t<strong>";
    echo __("sdd actiondialog debtor notification");
    echo "</strong><br />\n\t\t\t\t<label><input type=\"checkbox\" name=\"NotifyDebtor\" value=\"yes\"/> ";
    echo __("sdd actiondialog notify debtor moved date");
    echo "</label><br />\n\n\t\t\t\t<div class=\"notify_debtor_div hide\">\n\t\t\t\t\t<br />\n\t\t\t\t\t<strong>";
    echo __("sdd actiondialog debtor notification email");
    echo "</strong><br />\n\t\t\t\t\t<select class=\"text1 size4f\" name=\"NotifyMail\">\n\t\t\t\t\t\t<option value=\"\">";
    echo __("please choose");
    echo "</option>\n\t\t\t\t\t\t";
    foreach ($emailtemplates as $k => $v) {
        if(is_numeric($k)) {
            echo "\t\t\t\t\t\t\t<option value=\"";
            echo $k;
            echo "\" ";
            if(SDD_MOVED_MAIL == $k) {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo $v["Name"];
            echo "</option>\n\t\t\t\t\t\t";
        }
    }
    echo "\t\t\t\t\t</select><br />\n\t\t\t\t</div>\n\n\t\t\t\t<br />\n\n\t\t\t\t<label>\n\t\t\t\t\t<input type=\"checkbox\" name=\"imsure\" id=\"imsure\" value=\"yes\"/>\n\t\t\t\t\t";
    echo __("sdd delete batch confirm delete");
    echo "\t\t\t\t</label>\n\t\t\t\t<br /><br />\n\n\t\t\t\t<p><a id=\"batch_delete_submit\" class=\"button2 alt1 red float_left\"><span>";
    echo __("action delete");
    echo "</span></a></p>\n\t\t\t\t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#batch_delete_modal').dialog('close');\"><span>";
    echo __("cancel");
    echo "</span></a></p>\n\t\t\t</form>\n\t\t</div>\n\t\t";
}
echo "\n\n<script type=\"text/javascript\">\n\$(function(){\n    \$(document).on('click', 'input[name=\"NotifyDebtor\"], input[name=\"BatchNotifyDebtor\"]', function(){\n\t\tif(\$(this).prop('checked')){\n\t\t\t\$('.notify_debtor_div').removeClass('hide');\n\t\t}else{\n\t\t\t\$('.notify_debtor_div').addClass('hide');\n\t\t}\n\t});\n\t\n\t";
if(isset($selected_tab) && $selected_tab) {
    echo "\t\$('#tabs').tabs(\"option\", \"active\", ";
    echo $selected_tab;
    echo ");\n\t";
}
echo "\t\n\t";
if(in_array($batch_info->Status, ["draft", "downloadable", "downloaded"])) {
    echo "\t\t\$('#BatchChangeDateDialog').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});\n\t\t\n\t\t\$('#BatchChangeDateDialog').find('.datepicker').datepicker( \"option\", \"minDate\", ";
    echo $batch_info->ProcessingTime;
    echo ");\n\t\t\$('#BatchChangeDateDialog').find('.datepicker').datepicker( \"option\", \"beforeShowDay\", \$.datepicker.noWeekends );\n\t\t\n\t\t\$('#batch_change_date_btn').click(function(){\n\t\t\t\$('form[name=\"BatchChangeDateDialogForm\"]').submit();\n\t\t});\n\t";
}
echo "\t";
if(in_array($batch_info->Status, ["draft", "downloadable"])) {
    echo "\t\t\$('#batch_delete_modal').dialog({modal: true, autoOpen: false, resizable: false, width: 550, height: 'auto'});\n\t\t\$('#batch_delete_modal input[name=imsure]').change(function()\n\t\t{\n\t\t\tif(\$(this).is(':checked'))\n\t\t\t{\n\t\t\t\t\$('#batch_delete_submit').removeClass('button2').addClass('button1').addClass('red');\n\t\t\t}\n\t\t\telse\n\t\t\t{\n\t\t\t\t\$('#batch_delete_submit').removeClass('red').removeClass('button1').addClass('button2');\n\t\t\t}\n\t\t});\n\t\t\$('#batch_delete_submit').click( function()\n\t\t{\n\t\t\tif(\$(this).hasClass('button1'))\n\t\t\t{\n\t\t\t\t\$('form[name=batch_delete]').submit();\n\t\t\t}\n\t\t});\n\n\t";
}
echo "});\n</script>\t\n\t\n";
require_once "views/footer.php";

?>