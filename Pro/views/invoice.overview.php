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
echo "\n<!--heading1-->\n<div class=\"heading1\">\n<!--heading1-->\n\n\t<h2>";
echo __("invoice overview");
echo " (<span id=\"page_total_placeholder_invoices\"></span>)</h2>\n\n    ";
$array_invoicestatus["substatus_paused"] = __("overview status collection");
$new_array_invoicestatus = [];
foreach ($array_invoicestatus as $k => $v) {
    $new_array_invoicestatus[$k] = $v;
    if($k == "0") {
        $new_array_invoicestatus["draft_scheduled"] = __("invoice status draft planned");
    }
}
$array_invoicestatus = $new_array_invoicestatus;
if(isset($invoice_table_options["filter"]) && $invoice_table_options["filter"] != "" && isset($array_invoicestatus[$invoice_table_options["filter"]])) {
    echo "            <p class=\"pos3\">\n                <strong class=\"textsize1\">- ";
    echo __("status");
    echo ": ";
    echo $array_invoicestatus[$invoice_table_options["filter"]];
    echo "</strong>\n            </p>\n            ";
} elseif(isset($invoice_table_options["filter"]) && $invoice_table_options["filter"] == "0|1") {
    echo "            <p class=\"pos3\">\n                <strong class=\"textsize1\">- ";
    echo __("unsent invoices");
    echo "</strong>\n            </p>\n\t       ";
} elseif(isset($invoice_table_options["filter"]) && $invoice_table_options["filter"] == "2|3") {
    echo "            <p class=\"pos3\">\n                <strong class=\"textsize1\">- ";
    echo __("open invoices statuschange");
    echo "</strong>\n            </p>\n            ";
} elseif(isset($invoice_table_options["filter"]) && $invoice_table_options["filter"] == "reminders") {
    echo "            <p class=\"pos3\">\n                <strong class=\"textsize1\">- ";
    echo __("open invoices which need reminders");
    echo "</strong>\n            </p>\n            ";
} elseif(INT_SUPPORT_SUMMATIONS && isset($invoice_table_options["filter"]) && $invoice_table_options["filter"] == "summations") {
    echo "            <p class=\"pos3\">\n                <strong class=\"textsize1\">- ";
    echo __("open invoices which need summations");
    echo "</strong>\n            </p>\n            ";
} elseif(isset($invoice_table_options["filter"]) && $invoice_table_options["filter"] == "substatus_paused") {
    echo "            <p class=\"pos3\">\n                <strong class=\"textsize1\">- ";
    echo __("paused invoices");
    echo "</strong>\n            </p>\n            ";
}
echo "\t<p class=\"pos2\">\n\t\t<strong class=\"textsize1 pagetotals\">";
echo __("page total");
echo ": <span id=\"page_total_text_placeholder_invoices\" style=\"font-size: 14px;\"></span></strong>\n\t</p>\n\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n\n<div class=\"optionsbar\">\n\t";
if(U_INVOICE_ADD) {
    echo "            <p class=\"pos1\">\n                <a class=\"button1 add_icon\" href=\"invoices.php?page=add\">\n                    <span>";
    echo __("add invoice");
    echo "</span>\n                </a>\n            </p>\n            ";
}
echo "\t<p class=\"pos2\">\n        <a onclick=\"save('backoffice_table.list_invoice','filter','0|1', '";
echo $current_page_url;
echo "');\" class=\"sizenormal c1 a1 pointer\">\n            ";
echo __("unsent invoices");
echo "        </a> \n        <span class=\"c_gray\"> | </span> \n        <a onclick=\"save('backoffice_table.list_invoice','filter','2|3', '";
echo $current_page_url;
echo "');\" class=\"sizenormal c1 a1 pointer\">\n            ";
echo __("open invoices statuschange");
echo "        </a> \n        <span class=\"c_gray\"> | </span> \n        <a onclick=\"save('backoffice_table.list_invoice','filter','', '";
echo $current_page_url;
echo "');\" class=\"sizenormal c1 a1 pointer\">\n            ";
echo __("all invoices");
echo "        </a> \n        <span class=\"c_gray\"> | </span> \n        ";
echo __("status");
echo "        \n        <select class=\"select1\" onchange=\"save('backoffice_table.list_invoice','filter',this.value, '";
echo $current_page_url;
echo "');\">\n            <option value=\"\">";
echo __("please choose");
echo "</option>\n            ";
foreach ($array_invoicestatus as $k => $v) {
    if(is_numeric($k) || in_array($k, ["draft_scheduled", "substatus_paused"])) {
        echo "                    <option value=\"";
        echo $k;
        echo "\"";
        if($invoice_table_options["filter"] != "" && $invoice_table_options["filter"] == $k) {
            echo " selected=\"selected\"";
        }
        echo ">\n                        ";
        echo $v;
        echo "                    </option>\n                    ";
    }
}
echo "        </select> \n\t</p>\n</div>\n\n";
$invoice_table_options["page_total_text_placeholder"] = "page_total_text_placeholder_invoices";
generate_table("list_invoice", $invoice_table_options);
echo "\n";
require_once "views/footer.php";

?>