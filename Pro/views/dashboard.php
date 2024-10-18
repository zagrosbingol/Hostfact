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
if(!isset($_SESSION["index_cronjob"]["ready"]) || $_SESSION["index_cronjob"]["ready"] != 1) {
    echo "\t<div id=\"indexCronjob\" class=\"hide\" title=\"";
    echo __("index cronjob dialog title");
    echo "\">\n\t";
    echo __("index cronjob dialog description");
    echo "<br />\n\t<br />    \n\t<center><br /><img src=\"images/loadinfo.gif\" alt=\"\"/></center>\n\t<div id=\"indexCronjob_progress\" class=\"hide\">\n\t\t<strong>";
    echo __("index cronjob dialog tasks to execute");
    echo "</strong><br />\n\t\t<span id=\"ic_count_mails\">0</span> ";
    echo __("index cronjob dialog tasks mails");
    echo "<br />\n\t\t<span id=\"ic_count_hosting\">0</span> ";
    echo __("index cronjob dialog tasks hosting");
    echo "<br />\n\t\t<span id=\"ic_count_domain\">0</span> ";
    echo __("index cronjob dialog tasks domain");
    echo "<br />\n\t</div>\n    <br />\n    <a href=\"#\" onclick=\"runScript = 'false';\" class=\"a1 c1 floatr\"><span>";
    echo __("index cronjob dialog stop after action");
    echo "</span></a><br />\n\t</div>\n\t";
}
echo "\n<!--right-->\n<div class=\"right\">\n<!--right-->\n\n\t";
echo $message;
echo "\t\n\t<div id=\"container_widgets\">\n\t\t\n\t\t<a class=\"c1 a1 floatr\" style=\"margin:-16px 23px 0 0;\" id=\"edit_widgets\">";
echo __("edit widgets");
echo "</a>\n\t\t<a class=\"c1 a1 floatr hide\" style=\"margin:-16px 23px 0 0;\" id=\"edit_widgets_done\">";
echo __("done edit widgets");
echo "</a>\n\t\t\n\t\t<ul id=\"widgets\" ";
if((int) $selected_widgets["CountRows"] === 0) {
    echo "class=\"hide\"";
}
echo ">\n\t\t\t";
$x = 1;
if(is_array($selected_widgets)) {
    foreach ($selected_widgets as $widgetID => $widget) {
        if(is_numeric($widgetID)) {
            if($widget["File"] == "widget.graph_turnover.php" && in_array($widget["Option1"], ["last3m", "last6m"])) {
                $widget["Width"] = 160;
            }
            echo "                        <li class=\"widget\" id=\"widget_";
            echo $widgetID;
            echo "\" style=\"width:";
            echo $widget["Width"];
            echo "px;\">\n\n                            <input type=\"hidden\" class=\"widgetID\" value=\"";
            echo $widgetID;
            echo "\"/>\n                            <input type=\"hidden\" class=\"widget\" value=\"";
            echo $widget["Widget"];
            echo "\"/>\n                            <input type=\"hidden\" class=\"widgetName\" value=\"";
            echo __($widget["Name"]);
            echo "\"/>\n\n                            ";
            if($widget["Type"] == "external") {
                echo "                                <iframe src=\"";
                echo $widget["File"];
                echo "\" frameborder=\"0\" scrolling=\"no\" width=\"100%\" height=\"100%\"></iframe>\n                                ";
            } elseif($widget["Type"] == "internal") {
                require "views/widgets/" . $widget["File"];
            }
            echo "\n                        </li>\n                        ";
        }
        $x++;
    }
}
echo "\t\t\t\n\t\t\t<li class=\"widget add pointer hide\">\n\t\t\t\t";
echo __("add new widget");
echo "\t\t\t</li>\n\t\t</ul>\n\t\t<br clear=\"both\" />\n\t</div>\n\n";
for ($i = 0; $i < 5; $i++) {
    if(isset($invoice_waiting) && $invoice_waiting["show"] == "show" && $invoice_waiting["order"] == $i) {
        echo "            <!-- invoices ready to send -->\n       \t\t<div id=\"show_table_list_invoice_waiting_dashboard_div\">\n            \n        \t\t<div class=\"heading1\" style=\"margin-bottom: 0px;\">\n        \t\t\t<h2>";
        echo __("invoices ready to send");
        echo " (<span id=\"page_total_placeholder_waiting_invoices\">0</span>)</h2>\n        \t\t\t<p class=\"pos2\">\n        \t\t\t\t<strong class=\"textsize1\">";
        echo __("total");
        echo ": <span id=\"page_total_text_placeholder_waiting_invoices\" style=\"font-size: 14px;\"></span></strong>\n        \t\t\t</p>\n        \t\t</div>\n    \t\t\n        \t\t";
        $invoice = new invoice();
        $invoice_table_options = [];
        $invoice_table_options = $invoice->getConfigInvoiceTable(["Status" => "0"]);
        $invoice_table_options["redirect_url"] = "index.php";
        $invoice_table_options["results_per_page"] = "all";
        $invoice_table_options["filter"] = "1";
        $invoice_table_options["page_total_placeholder"] = "page_total_placeholder_waiting_invoices";
        $invoice_table_options["page_total_text_placeholder"] = "page_total_text_placeholder_waiting_invoices";
        $invoice_table_options["parameters"]["page_total_method"] = "all_results";
        $invoice_table_options["hide_table_if_no_results"] = "list_invoice_waiting_dashboard";
        generate_table("list_invoice_waiting_dashboard", $invoice_table_options);
        echo "                <br/><br />\n            </div>\n            ";
    }
    if(isset($invoice_open) && $invoice_open["show"] == "show" && $invoice_open["order"] == $i) {
        echo "            <!-- open invoices -->\n    \t\t<div class=\"heading1\" style=\"margin-bottom: 0px;\">\n    \t\t\t<h2>";
        echo __("open invoices");
        echo " (<span id=\"page_total_placeholder_open_invoices\">0</span>)</h2>\n    \t\t\t<p class=\"pos2\">\n    \t\t\t\t<strong class=\"textsize1\">";
        echo __("open amount abbr");
        echo ": <span id=\"page_total_text_placeholder_open_invoices\" style=\"font-size: 14px;\"></span></strong>\n    \t\t\t</p>\n    \t\t</div>\n\n    \t\t";
        $invoice = isset($invoice) ? $invoice : new invoice();
        $invoice_table_options = [];
        $invoice_table_options = $invoice->getConfigInvoiceTable(["Status" => "2|3"]);
        $invoice_table_options["redirect_url"] = "index.php";
        $invoice_table_options["results_per_page"] = "all";
        $invoice_table_options["filter"] = "2|3";
        $invoice_table_options["page_total_placeholder"] = "page_total_placeholder_open_invoices";
        $invoice_table_options["page_total_text_placeholder"] = "page_total_text_placeholder_open_invoices";
        $invoice_table_options["parameters"]["page_total_method"] = "all_results_open_amount";
        generate_table("list_invoice_open_dashboard", $invoice_table_options);
        echo "            <br/><br />\n            ";
    }
    if(isset($invoice_waiting_c) && $invoice_waiting_c["show"] == "show" && $invoice_waiting_c["order"] == $i) {
        echo "            <!-- credited invoices of the last x days waiting to be sent -->\n            <div id=\"show_table_list_invoice_waitingc_dashboard_div\">\n        \t\t\n        \t\t<div class=\"heading1\" style=\"margin-bottom: 0px;\">\n        \t\t\t<h2>";
        echo sprintf(__("credited invoices of the last x days waiting to be sent"), CREDIT_SHOW_NOT_SENT);
        echo " (<span id=\"page_total_placeholder_waitingc_invoices\">0</span>)</h2>\n        \t\t\t<p class=\"pos2\">\n        \t\t\t\t<strong class=\"textsize1\">";
        echo __("total");
        echo ": <span id=\"page_total_text_placeholder_waitingc_invoices\" style=\"font-size: 14px;\"></span></strong>\n        \t\t\t</p>\n        \t\t</div>\n                \n        \t\t";
        $invoice = isset($invoice) ? $invoice : new invoice();
        $invoice_table_options = [];
        $invoice_table_options = $invoice->getConfigInvoiceTable(["Status" => "8"]);
        $invoice_table_options["redirect_url"] = "index.php";
        $invoice_table_options["results_per_page"] = "all";
        $invoice_table_options["filter"] = "8";
        $invoice_table_options["page_total_placeholder"] = "page_total_placeholder_waitingc_invoices";
        $invoice_table_options["hide_table_if_no_results"] = "list_invoice_waitingc_dashboard";
        $invoice_table_options["page_total_text_placeholder"] = "page_total_text_placeholder_waitingc_invoices";
        $invoice_table_options["parameters"]["searchat"] = "Sent";
        $invoice_table_options["parameters"]["searchfor"] = "0";
        generate_table("list_invoice_waitingc_dashboard", $invoice_table_options);
        echo "                <br/><br />\n            </div>\n        ";
    }
    if(isset($creditinvoice) && 0 < $creditinvoice["CountRows"] && $creditinvoice["show"] == "show" && $creditinvoice["order"] == $i) {
        echo "    \t\t\n    \t\t<!--heading1-->\n    \t\t<div class=\"heading1\" style=\"margin-bottom: 0px;\">\n    \t\t<!--heading1-->\n    \t\t\n    \t\t\t<h2>";
        echo __("open invoices from creditors");
        echo " (";
        echo $creditinvoice["CountRows"];
        echo ")</h2>\n    \t\t\n    \t\t\t<p class=\"pos2\">\n    \t\t\t\t<strong class=\"textsize1 pagetotals\">";
        echo __("open amount abbr");
        echo ": ";
        echo money($creditinvoice["OpenAmountIncl"]);
        echo " ";
        echo __("incl vat");
        echo "</strong>\n    \t\t\t</p>\n    \t\t\n    \t\t<!--heading1-->\n    \t\t</div>\n    \t\t<!--heading1-->\n    \t\t\n    \t\t";
        require_once "views/elements/creditinvoice.table.php";
        $options = ["redirect_page" => "home", "session_name" => "invoice.dashboard.creditinvoice", "current_page" => $current_page, "current_page_url" => $current_page_url, "hide_columns" => ["Status"]];
        show_creditinvoice_table($creditinvoice, $options);
        echo "<br/><br />";
    }
}
echo "\n    \t\n\n<!--right-->\n</div>\n<!--right-->\n\n<div id=\"dialog_widgets_add\" class=\"actiondialog\" title=\"";
echo __("widgets");
echo "\">\n\t\n\t<form name=\"form_widgets_add\" method=\"post\" action=\"\">\n\t\n\t\t<input type=\"hidden\" name=\"action\" value=\"addwidget\" />\n\t\n\t\t<strong class=\"title\">";
echo __("widget");
echo "</strong>\n\t\t<select class=\"select1\" id=\"widgets_select\" name=\"widgetID\">\n\t\t\t<option value=\"\">";
echo __("please choose");
echo "</option>\n\t\t\t";
foreach ($all_widgets as $widgetID => $widget) {
    if(is_numeric($widgetID)) {
        echo "\t\t\t<option value=\"";
        echo $widgetID;
        echo "\">";
        echo __($widget["Name"]);
        echo "</option>\n\t\t\t";
    }
}
echo "\t\t</select>\n\t\t\n\t\t<div class=\"widget_options\"></div>\n\t\t\n\t\t<a class=\"button1 alt1 pointer\" onclick=\"document.form_widgets_add.submit();\" id=\"widget_add\"><span>";
echo __("add widget");
echo "</span></a>\n\t\t<a class=\"c1 a1 floatr\" onclick=\"\$('#dialog_widgets_add').dialog('close');\"><span>";
echo __("cancel");
echo "</span></a>\n\t\n\t</form>\n\t\n</div>\n\n<div id=\"dialog_widgets_edit\" class=\"actiondialog\" title=\"";
echo __("widgets");
echo "\">\n\t\n\t<form name=\"form_widgets\" method=\"post\" action=\"\">\n\t\t<input type=\"hidden\" name=\"action\" />\n\t\t<input type=\"hidden\" name=\"widgetID\" />\n\t\n\t\t<strong class=\"title\">";
echo __("widget");
echo "</strong>\n\t\t<span id=\"widget_name\"></span>\n\t\t\n\t\t<div class=\"widget_options\"></div>\n\t\t\n\t\t<a class=\"button1 alt1 pointer\" id=\"widget_edit\"><span>";
echo __("edit widget");
echo "</span></a>\n\t\t<a class=\"c1 a1 floatr\" id=\"widget_remove\"><span>";
echo __("remove");
echo "</span></a>\n\t\n\t</form>\n\t\n</div>\n\n";
require_once "views/footer.php";

?>