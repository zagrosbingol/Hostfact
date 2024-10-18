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
if($creditinvoice->Status != 3) {
    echo "<ul class=\"list1\">\n\t<li><a class=\"ico set1 paid\" onclick=\"\$('#dialog_paid_confirm').dialog('open');\">";
    echo __("action paid");
    echo "</a></li>\n</ul>\n\n<div id=\"dialog_paid_confirm\" class=\"hide\" title=\"";
    echo __("invoice paid confirm dialog title");
    echo "\">\n\n\t<form name=\"form_paid_confirm\" method=\"post\" action=\"creditors.php?page=show_invoice&action=markaspaid&id=";
    echo $creditinvoice->Identifier;
    echo "\">\n\t\t<p tabindex=\"1\"></p> \t\t<p>\n\t\t\t";
    echo sprintf(__("confirm paid"), $creditinvoice->CreditInvoiceCode);
    echo "\t\t\t<br /><br />\n\t\t\t<strong>";
    echo __("enter paid date");
    echo "</strong>\n\t\t\t<br />\n\t\t\t<span class=\"input_date\">\n\t\t\t\t\t<input type=\"text\" class=\"text1 size6 datepicker\" name=\"PayDate\" value=\"";
    echo rewrite_date_db2site(date("Y-m-d"));
    echo "\" />\n\t\t\t\t</span>\n\t\t</p>\n\t\t<br />\n\t\t<p>\n\t\t\t<a class=\"button1 alt1 float_left\" onclick=\"\$(this).hide();\$('#dialog_paid_confirm form').submit();\">\n\t\t\t\t<span>";
    echo __("action paid");
    echo "</span>\n\t\t\t</a>\n\t\t</p>\n\t\t<p>\n\t\t\t<a class=\"c1 a1 float_right\" onclick=\"\$('#dialog_paid_confirm').dialog('close');\">\n\t\t\t\t<span>";
    echo __("cancel");
    echo "</span>\n\t\t\t</a>\n\t\t</p>\n\t</form>\n\n</div>\n\n";
}
echo $message;
echo "\t\n<!--heading1-->\n<div class=\"heading1 mar1\">\n<!--heading1-->\n\n    <h2>\n        ";
echo __("show creditinvoice");
echo "        ";
echo $creditinvoice->CreditInvoiceCode . (in_array($creditinvoice->Status, [0, 1, 3]) && $creditinvoice->Authorisation == "yes" ? " " . __("creditinvoice authed") : "");
echo "    </h2>\n    <p class=\"pos2\"><strong class=\"textsize1\">";
echo $array_creditinvoicestatus[$creditinvoice->Status];
echo "</strong></p>\n\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n<!--clone creditinvoice-->\n";
if(U_CREDITOR_INVOICE_EDIT) {
    echo "        <div class=\"duplicateInvoice\">\n            <a href=\"creditors.php?page=add_invoice&clone=";
    echo $creditinvoice->Identifier;
    echo "\">";
    echo __("clone creditinvoice");
    echo "</a>\n    \t</div>\n        ";
}
echo "<!--clone creditinvoice-->\n\t\n<!--box1-->\n<div class=\"box2\" id=\"tabs\">\n<!--box1-->\n\n\t<!--top-->\n\t<div class=\"top\">\n\t<!--top-->\n\t\n\t\t<ul class=\"list3\">\n\t\t\t<li class=\"on\"><a href=\"#tab-general\">";
echo __("general");
echo "</a></li>\n\t\t\t";
if($transaction_matches_options !== false) {
    echo "<li><a href=\"#tab-transactions\">";
    echo __("bank transactions tab");
    echo " (<span id=\"page_total_placeholder_transactions\" class=\"hide\">0</span>)</a></li>";
}
echo "            <li><a href=\"#tab-logfile\">";
echo __("logfile");
echo "</a></li>\n\t\t</ul>\n\t\n\t<!--top-->\n\t</div>\n\t<!--top-->\n\t\n\t<!--content-->\n\t<div class=\"content\" id=\"tab-general\">\n\t<!--content-->\n\t\n\t\t<!--split2-->\n\t\t<div class=\"split2\">\n\t\t<!--split2-->\n\t\t\n\t\t\t<!--left-->\n\t\t\t<div class=\"left\">\n\t\t\t<!--left-->\n\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
echo __("general");
echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title2\">";
echo __("creditor");
echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\" style=\"clear:none; float: left;\">\n\t\t\t\t\t\t<a href=\"creditors.php?page=show&amp;id=";
echo $creditinvoice->Creditor;
echo "\" class=\"c1 a1\">";
echo $creditor->CompanyName ? $creditor->CompanyName : $creditor->SurName . ", " . $creditor->Initials;
echo "</a>\n\t\t\t\t\t\t";
if($creditor->Address) {
    echo "<br />";
    echo $creditor->Address;
}
echo "\t\t\t\t\t\t";
if($creditor->ZipCode || $creditor->City) {
    echo "<br />";
    echo $creditor->ZipCode;
    echo "&nbsp;&nbsp;";
    echo $creditor->City;
}
echo "\t\t\t\t\t\t";
if($creditor->Country) {
    echo "<br />";
    echo $array_country[$creditor->Country];
}
echo "\t\t\t\t\t</span>\n\t\t\t\t\t<div class=\"clear\"></div>\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title2\">";
echo __("invoice date");
echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
echo $creditinvoice->Date;
echo "</span>\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title2\">";
echo __("external invoice code");
echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\" style=\"display:block;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;max-width: 480px;min-width: 130px;\">";
echo $creditinvoice->InvoiceCode;
echo "</span>\n\t\t\t\t\t<div class=\"clear\"></div>\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t<!--left-->\n\t\t\t</div>\n\t\t\t<!--left-->\n\t\t\t\n\t\t\t<!--right-->\n\t\t\t<div class=\"right\">\n\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t\t";
if($creditinvoice->Location || isset($creditinvoice->Attachment[0])) {
    echo "\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
    echo __("download invoice");
    echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t";
    if($creditinvoice->Location) {
        echo "\t\t\t\t\t\t<strong class=\"title2\">";
        echo __("file");
        echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">\n\t\t\t\t\t\t\t<a href=\"creditors.php?page=show_invoice&amp;id=";
        echo $creditinvoice->Identifier;
        echo "&amp;action=download\" class=\"a1 c1\">\n\t\t\t\t\t\t\t\t<div class=\"file ico inline file_";
        echo getFileType($creditinvoice->Location);
        echo "\">&nbsp;</div>\n\t\t\t\t\t\t\t\t";
        echo $creditinvoice->Location;
        echo "\t\t\t\t\t\t\t</a>\n\t\t\t\t\t\t</span>";
    } elseif(isset($creditinvoice->Attachment[0])) {
        echo "\t\t\t\t\t\t<strong class=\"title2\">";
        echo __("file");
        echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">\n\t\t\t\t\t\t\t<a href=\"download.php?type=creditinvoice&id=";
        echo $creditinvoice->Attachment[0]->id;
        echo "\" class=\"a1 c1\" target=\"_blank\">\n\t\t\t\t\t\t\t\t<div class=\"file ico inline file_";
        echo getFileType($creditinvoice->Attachment[0]->Filename);
        echo "\">&nbsp;</div>\n\t\t\t\t\t\t\t\t";
        echo $creditinvoice->Attachment[0]->Filename;
        echo "\t\t\t\t\t\t\t</a>\n\t\t\t\t\t\t</span>";
    }
    echo "\t\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t\t";
}
echo "\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
echo __("extra information");
echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\t";
if($creditinvoice->ReferenceNumber) {
    echo "\t\t\t\t\t<strong class=\"title2\">";
    echo __("reference number");
    echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
    echo $creditinvoice->ReferenceNumber;
    echo "</span>\n\t\t\t\t\t";
}
echo "\n\t\t\t\t\t<strong class=\"title2\">";
echo __("payment term");
echo "</strong>\n                    <span class=\"title2_value\">\n                        ";
echo in_array($creditinvoice->Status, [0, 1, 3]) && $creditinvoice->Authorisation == "yes" ? __("credit auth") : $creditinvoice->Term . " " . ($creditinvoice->Term != 1 ? __("days") : __("day"));
if((int) $creditinvoice->Term === 0 && $creditinvoice->Status == 1 && $creditinvoice->Authorisation != "yes" || isEmptyFloat($creditinvoice->AmountPaid) && $creditinvoice->Status == 2) {
    echo "                                <span id=\"partialPayment\" class=\"a1 c1 float_right\">";
    echo __("credit invoice partial payment");
    echo "</span>\n                                ";
}
echo "                    </span>\n\n                    ";
if(in_array($creditinvoice->Status, [0, 1, 2]) && $creditinvoice->Authorisation != "yes" && 0 < $creditinvoice->Term) {
    echo "                        <strong class=\"title2\">";
    echo __("pay before date");
    echo "</strong>\n                        <span class=\"title2_value\">\n                            ";
    echo in_array($creditinvoice->Status, [0, 1]) && $creditinvoice->Authorisation != "yes" ? __("credit for") . " " . rewrite_date_db2site($creditinvoice->PayBefore) : ($creditinvoice->Authorisation == "yes" ? " " . __("credit auth") : "");
    if($creditinvoice->Status == 1 && $creditinvoice->Authorisation != "yes" || isEmptyFloat($creditinvoice->AmountPaid) && $creditinvoice->Status == 2) {
        echo "                                    <span id=\"partialPayment\" class=\"a1 c1 float_right\">";
        echo __("credit invoice partial payment");
        echo "</span>\n                                    ";
    }
    echo "                        </span>\n                    ";
}
echo "\n                    ";
if($creditinvoice->Status == 2 && !isEmptyFloat($creditinvoice->AmountPaid)) {
    echo "                        <br />\n                        <strong class=\"title2\">";
    echo __("partial payment");
    echo "</strong>\n                        <span class=\"title2_value\">\n                            ";
    echo money($creditinvoice->AmountPaid);
    echo "                            <span id=\"partialPayment\" class=\"a1 c1 float_right\">";
    echo __("credit invoice partial payment");
    echo "</span>\n                        </span>\n\n                        <strong class=\"title2\">";
    echo __("open amount");
    echo "</strong>\n                        <span class=\"title2_value\">";
    echo money($creditinvoice->PartPayment);
    echo "</span>\n                    ";
}
echo "\n                    ";
if($creditinvoice->PayDate) {
    echo "                        <strong class=\"title2\">";
    echo __("creditinvoice payment date");
    echo "</strong>\n                        <span class=\"title2_value\">";
    echo $creditinvoice->PayDate;
    echo "</span>\n                    ";
}
echo "\t\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t";
if(0 < $creditinvoice->Private || 0 < $creditinvoice->PrivatePercentage) {
    echo "\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
    echo __("private part title");
    echo "</h3><div class=\"content\">\n\t\t\t\t<!--box3-->\t\n\t\t\t\t\t\n\t\t\t\t\t";
    if(0 < $creditinvoice->Private) {
        echo "\t\t\t\t\t<strong class=\"title2\">";
        echo __("private amount");
        echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
        echo money($creditinvoice->Private);
        echo " ";
        echo __("excl vat");
        echo "</span>\n\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\n\t\t\t\t\t";
    if(0 < $creditinvoice->PrivatePercentage) {
        echo "\t\t\t\t\t<strong class=\"title2\">";
        echo __("private percentage");
        echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
        echo showNumber($creditinvoice->PrivatePercentage);
        echo "%</span>\n\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t\t";
}
echo "\t\t\t<!--right-->\n\t\t\t</div>\n\t\t\t<!--right-->\n\t\t\t\n\t\t<!--split2-->\n\t\t</div>\n\t\t<!--split2-->\n\t\t\t\n\t\t\t<!--box3-->\n\t\t\t<div class=\"box3\"><h3>";
echo __("invoice elements");
echo "</h3><div class=\"content\">\n\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t<table border=\"0\" width=\"100%\">\n\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td style=\"width: 55px;\"><strong>";
echo __("number");
echo "</strong></td>\n\t\t\t\t\t\t<td><strong>";
echo __("description");
echo "</strong></td>\n\t\t\t\t\t\t<td colspan=\"3\"><strong>";
echo __("piecepriceexcl");
echo "</strong></td>\n\t\t\t\t\t\t<td style=\"width: 40px;\" class=\"align_right\"><strong>";
echo __("vat");
echo "</strong></td>\n\t\t\t\t\t\t<td style=\"width: 20px;\">&nbsp;</td>\n\t\t\t\t\t\t<td colspan=\"3\"><strong>";
echo __("amountexcl");
echo "</strong></td>\n\t\t\t\t\t</tr>\n\t\t\t\t\t";
foreach ($creditinvoice->Elements as $key => $element) {
    if(is_numeric($key)) {
        echo "\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td>";
        echo showNumber($element["Number"]);
        echo "</td>\n\t\t\t\t\t\t\t<td>";
        echo $element["Description"];
        echo "</td>\n\t\t\t\t\t\t\t<td style=\"width: 5px;\" class=\"currency_sign_left\">";
        echo currency_sign_td(CURRENCY_SIGN_LEFT);
        echo "</td>\n\t\t\t\t\t\t\t<td style=\"width: 60px;\" class=\"align_right\">";
        echo money($element["PriceExcl"], false, false);
        echo "</td>\n\t\t\t\t\t\t\t<td style=\"width: 45px;\" class=\"currency_sign_right\">";
        echo currency_sign_td(CURRENCY_SIGN_RIGHT);
        echo "</td>\n\t\t\t\t\t\t\t<td class=\"align_right\">";
        echo showNumber($element["TaxPercentage"] * 100) . "%";
        echo "</td>\n\t\t\t\t\t\t\t<td>&nbsp;</td>\n\t\t\t\t\t\t\t<td style=\"width: 5px;\" class=\"currency_sign_left\">";
        echo currency_sign_td(CURRENCY_SIGN_LEFT);
        echo "</td>\n\t\t\t\t\t\t\t<td style=\"width: 60px;\" class=\"align_right\">";
        echo money($element["PriceExcl"] * $element["Number"], false);
        echo "</td>\n\t\t\t\t\t\t\t<td style=\"width: 30px;\" class=\"currency_sign_right\">";
        echo currency_sign_td(CURRENCY_SIGN_RIGHT);
        echo "</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t";
    }
}
echo "\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td colspan=\"10\">&nbsp;</td>\n\t\t\t\t\t</tr>\n\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td colspan=\"2\">&nbsp;</td>\n\t\t\t\t\t\t<td colspan=\"8\" class=\"line\">&nbsp;</td>\n\t\t\t\t\t</tr>\n\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td colspan=\"2\">&nbsp;</td>\n\t\t\t\t\t\t<td colspan=\"4\" class=\"align_right\">";
echo __("amountexcl");
echo ":</td>\n\t\t\t\t\t\t<td>&nbsp;</td>\n\t\t\t\t\t\t<td class=\"currency_sign_left\">";
echo currency_sign_td(CURRENCY_SIGN_LEFT);
echo "</td>\n\t\t\t\t\t\t<td class=\"align_right\">";
echo money(deformat_money($creditinvoice->AmountExcl), false);
echo "</td>\n\t\t\t\t\t\t<td class=\"currency_sign_right\">";
echo currency_sign_td(CURRENCY_SIGN_RIGHT);
echo "</td>\n\t\t\t\t\t</tr>\n\t\t\t\t\t";
$creditinvoiceTax = [];
foreach ($creditinvoice->Elements as $key => $element) {
    if(!is_numeric($key)) {
    } elseif(!isEmptyFloat($element["TaxPercentage"])) {
        $creditinvoiceTax[$element["TaxPercentage"] * 10000] += $element["PriceExcl"] * $element["Number"] * $element["TaxPercentage"];
    }
}
ksort($creditinvoiceTax);
foreach ($creditinvoiceTax as $key => $value) {
    echo "\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td colspan=\"2\">&nbsp;</td>\n\t\t\t\t\t\t\t<td colspan=\"4\" class=\"align_right\">";
    echo showNumber($key / 100) . "% " . __("vat");
    echo ":</td>\n\t\t\t\t\t\t\t<td>&nbsp;</td>\n\t\t\t\t\t\t\t<td class=\"currency_sign_left\">";
    echo currency_sign_td(CURRENCY_SIGN_LEFT);
    echo "</td>\n\t\t\t\t\t\t\t<td class=\"align_right\">";
    echo money($value, false);
    echo "</td>\n\t\t\t\t\t\t\t<td class=\"currency_sign_right\">";
    echo currency_sign_td(CURRENCY_SIGN_RIGHT);
    echo "</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\n\t\t\t\t\t";
}
echo "\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td colspan=\"2\">&nbsp;</td>\n\t\t\t\t\t\t<td colspan=\"4\" class=\"align_right\">";
echo __("amountincl");
echo ":</td>\n\t\t\t\t\t\t<td>&nbsp;</td>\n\t\t\t\t\t\t<td class=\"currency_sign_left\">";
echo currency_sign_td(CURRENCY_SIGN_LEFT);
echo "</td>\n\t\t\t\t\t\t<td class=\"align_right\">";
echo money(deformat_money($creditinvoice->AmountIncl), false);
echo "</td>\n\t\t\t\t\t\t<td class=\"currency_sign_right\">";
echo currency_sign_td(CURRENCY_SIGN_RIGHT);
echo "</td>\n\t\t\t\t\t</tr>\n\t\t\t\t\t\n\t\t\t\t\t";
if($creditinvoice->Status == 2 && !isEmptyFloat($creditinvoice->AmountPaid)) {
    echo "\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td colspan=\"10\">&nbsp;</td>\n\t\t\t\t\t</tr>\n\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td colspan=\"2\">&nbsp;</td>\n\t\t\t\t\t\t<td colspan=\"4\" class=\"align_right\">";
    echo __("partial payment");
    echo ":</td>\n\t\t\t\t\t\t<td>&nbsp;</td>\n\t\t\t\t\t\t<td class=\"currency_sign_left\">";
    echo currency_sign_td(CURRENCY_SIGN_LEFT);
    echo "</td>\n\t\t\t\t\t\t<td class=\"align_right\">";
    echo money(deformat_money($creditinvoice->AmountPaid), false);
    echo "</td>\n\t\t\t\t\t\t<td class=\"currency_sign_right\">";
    echo currency_sign_td(CURRENCY_SIGN_RIGHT);
    echo "</td>\n\t\t\t\t\t</tr>\n\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td colspan=\"2\">&nbsp;</td>\n\t\t\t\t\t\t<td colspan=\"4\" class=\"align_right\">";
    echo __("open amount");
    echo ":</td>\n\t\t\t\t\t\t<td>&nbsp;</td>\n\t\t\t\t\t\t<td class=\"currency_sign_left\">";
    echo currency_sign_td(CURRENCY_SIGN_LEFT);
    echo "</td>\n\t\t\t\t\t\t<td class=\"align_right\">";
    echo money(deformat_money($creditinvoice->PartPayment), false);
    echo "</td>\n\t\t\t\t\t\t<td class=\"currency_sign_right\">";
    echo currency_sign_td(CURRENCY_SIGN_RIGHT);
    echo "</td>\n\t\t\t\t\t</tr>\n\t\t\t\t\t";
}
echo "\t\t\t\t\t\n\t\t\t\t</table>\n\t\t\t\t\n\t\t\t\t<input type=\"hidden\" name=\"NumberOfElements\" value=\"";
echo $i;
echo "\" />\n\t\t\t\t\n\t\t\t<!--box3-->\n\t\t\t</div></div>\n\t\t\t<!--box3-->\n\t\t\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\t\n\t";
if($transaction_matches_options !== false) {
    echo "\t<!--content-->\n\t<div class=\"content\" id=\"tab-transactions\">\n\t<!--content-->\n\t";
    $transaction_matches_options["hide_cols"] = ["referencecode"];
    generate_table("list_transaction_matches_invoice", $transaction_matches_options);
    echo "\t<!--content-->\n\t</div>\n\t<!--content-->\n\t";
}
echo "\n    <!--content-->\n    <div class=\"content\" id=\"tab-logfile\">\n        <!--content-->\n\n        ";
require_once "views/elements/log.table.php";
$options = ["form_action" => "creditors.php?page=show_invoice&amp;id=" . $creditinvoice->Identifier, "session_name" => "creditinvoice.show.logfile", "current_page" => $current_page, "current_page_url" => $current_page_url, "allow_delete" => U_CREDITOR_INVOICE_EDIT];
show_log_table($list_creditinvoice_logfile, $options);
echo "\n        <!--content-->\n    </div>\n    <!--content-->\n\t\n<!--box1-->\n</div>\n<!--box1-->\n\n<br />\n\n<!--buttonbar-->\n<div class=\"buttonbar\">\n<!--buttonbar-->\n\n\t";
if(U_CREDITOR_INVOICE_EDIT) {
    echo "<p class=\"pos1\"><a class=\"button1 edit_icon\" href=\"creditors.php?page=add_invoice&amp;id=";
    echo $creditinvoice->Identifier;
    echo "\"><span>";
    echo __("edit");
    echo "</span></a></p>";
}
echo "\t";
if(U_CREDITOR_INVOICE_DELETE) {
    echo "<p class=\"pos2\"><a class=\"button1 delete_icon\" onclick=\"\$('#delete_creditinvoice').dialog('open');\"><span>";
    echo __("delete");
    echo "</span></a></p>";
}
echo "\t\n<!--buttonbar-->\n</div>\n";
if(U_CREDITOR_INVOICE_EDIT && in_array($creditinvoice->Status, [1, 2]) && $creditinvoice->Authorisation != "yes") {
    echo "<div id=\"partialPaymentDialog\" class=\"hide\" title=\"";
    echo __("partpaymentdialog process");
    echo "\">\n\t<form id=\"CreditorPartPaymentForm\" name=\"form_partpayment\" method=\"post\" action=\"creditors.php?page=show_invoice&amp;action=partialpayment&amp;id=";
    echo $creditinvoice->Identifier;
    echo "\">\n\t<input type=\"hidden\" name=\"id\" value=\"";
    echo $creditinvoice->Identifier;
    echo "\"/>\n\t";
    echo sprintf(__("partpaymentdialog description"), $creditinvoice->InvoiceCode ? $creditinvoice->InvoiceCode : $creditinvoice->CreditInvoiceCode);
    echo "<br />\n\t<br />\n\t<span class=\"title2\">";
    echo __("total invoice");
    echo ":</span>\n\t<span class=\"title2_value\">";
    echo money($creditinvoice->AmountIncl);
    echo "</span>\n\t<span class=\"title2\">";
    echo __("already paid");
    echo ":</span>\n\t<span class=\"title2_value\">";
    echo money($creditinvoice->AmountPaid);
    echo "</span>\n\t<br />\n\t<strong class=\"title2\">";
    echo __("still to pay");
    echo ":</strong>\n\t<strong class=\"title2_value\">";
    echo money($creditinvoice->PartPayment);
    echo "</strong>\n\t<br />\n\t<strong class=\"title\">";
    echo __("partial payment paid payment");
    echo "</strong>\n\t<input type=\"text\" name=\"AmountPaid\" class=\"text1 size6\" value=\"\" /><br />\n\t<br />\n\t<p><a id=\"partialPaymentDialog_btn\" class=\"button1 alt1 float_left\"><span>";
    echo __("partpaymentdialog process");
    echo "</span></a></p>\n\t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#partialPaymentDialog').dialog('close');\"><span>";
    echo __("cancel");
    echo "</span></a></p>\n\t</form>\n</div>\n";
}
echo "<!--buttonbar-->\n";
if(U_CREDITOR_INVOICE_DELETE) {
    echo "\t\t\n<div id=\"delete_creditinvoice\" class=\"hide ";
    if(isset($pagetype) && $pagetype == "confirmDelete") {
        echo "autoopen";
    }
    echo "\" title=\"";
    echo __("delete creditinvoice title");
    echo "\">\n\t<form id=\"CreditorInvoiceForm\" name=\"form_delete\" method=\"post\" action=\"creditors.php?page=overview_creditinvoice&amp;action=creditinvoice_delete&amp;id=";
    echo $creditinvoice->Identifier;
    echo "\">\n\t<input type=\"hidden\" name=\"id\" value=\"";
    echo $creditinvoice->Identifier;
    echo "\"/>\n\t";
    echo sprintf(__("delete creditinvoice description"), $creditinvoice->CreditInvoiceCode);
    echo "<br />\n\t<br />\n\t<input type=\"checkbox\" id=\"imsure\" name=\"imsure\" value=\"yes\"/> <label for=\"imsure\">";
    echo __("delete this creditinvoice");
    echo "</label><br />\n\t<br />\n\t<p><a id=\"delete_creditinvoice_btn\" class=\"button2 alt1 float_left\"><span>";
    echo __("delete");
    echo "</span></a></p>\n\t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#delete_creditinvoice').dialog('close');\"><span>";
    echo __("cancel");
    echo "</span></a></p>\n\t</form>\n</div>\n";
}
echo "\n";
require_once "views/footer.php";

?>