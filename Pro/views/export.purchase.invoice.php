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
echo $message;
echo "\n<!--heading1-->\n\t<div class=\"heading1\">\n\t<!--heading1-->\n\t\n\t\t<h2>";
echo __("export purchase invoice header title");
echo "</h2>\n\t\n\t<!--heading1-->\n\t</div><hr />\n\t<!--heading1-->\n\t\n\t<!--form-->\n\t<form id=\"ExportForm\" name=\"download_pdf_form\" method=\"post\" action=\"export.php?page=purchaseinvoice\"><fieldset>    \n\t<!--form-->\n\t\n\t<!--box1-->\n\t<div class=\"box2\" id=\"tabs\">\n\t<!--box1-->\n\t\n\t\t<!--top-->\n\t\t<div class=\"top\">\n\t\t<!--top-->\n\t\t\n\t\t\t<ul class=\"list3\">\n\t\t\t\t<li class=\"on\"><a href=\"#tab-";
echo strtolower(__("general"));
echo "\">";
echo __("general");
echo "</a></li>\n\t\t\t</ul>\n\t\t\n\t\t<!--top-->\n\t\t</div>\n\t\t<!--top-->\n\t\t\n\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-";
echo strtolower(__("general"));
echo "\">\n\t\t<!--content-->\n\t\t\t\n\t\t\t\t<!--split2-->\n\t\t\t\t<div class=\"split2\">\n\t\t\t\t<!--split2-->\n\t\t\t\t\n\t\t\t\t\t<!--left-->\n\t\t\t\t\t<div class=\"left\">\n\t\t\t\t\t<!--left-->\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("filters");
echo "</h3><div class=\"content\">\n\t\t\t\t\t\t<!--box3-->\n\n\t\t\t\t\t\t\t<table>\n\t\t\t\t\t\t\t\t<tbody>\n\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t\t\t<strong>";
echo __("export purchase creditinvoicecode");
echo "</strong>\n\t\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t<td class=\"paddingb\">\n\t\t\t\t\t\t\t\t\t\t\t<table>\n\t\t\t\t\t\t\t\t\t\t\t\t<tbody><tr>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<td>\n                                                        <input type=\"text\" name=\"CreditInvoiceCode_Start\" class=\"text1 size7\" ";
if(!empty($_POST) && $_POST["CreditInvoiceCode_Start"]) {
    echo "value=\"" . $_POST["CreditInvoiceCode_Start"] . "\"";
}
echo " />\n\t\t\t\t\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<td style=\"padding: 0 15px;\" align=\"center\">";
echo __("from till");
echo "</td>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<td>\n                                                        <input type=\"text\" name=\"CreditInvoiceCode_End\" class=\"text1 size7\" ";
if(!empty($_POST) && $_POST["CreditInvoiceCode_End"]) {
    echo "value=\"" . $_POST["CreditInvoiceCode_End"] . "\"";
}
echo " />\n\t\t\t\t\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t\t\t\t</tbody>\n\t\t\t\t\t\t\t\t\t\t\t</table>\n\t\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t\t\t<strong>";
echo __("invoice date");
echo "</strong>\n\t\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t<td class=\"paddingb\">\n\t\t\t\t\t\t\t\t\t\t\t<table>\n\t\t\t\t\t\t\t\t\t\t\t\t<tbody><tr>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<td><input type=\"text\" name=\"Date_Start\" class=\"text1 size6 datepicker\" value=\"";
if(isset($_POST["Date_Start"])) {
    echo htmlspecialchars($_POST["Date_Start"]);
}
echo "\"/></td>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<td style=\"padding: 0 15px;\" align=\"center\">";
echo __("from till");
echo "</td>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<td><input type=\"text\" name=\"Date_End\"  class=\"text1 size6 datepicker\" value=\"";
if(isset($_POST["Date_End"])) {
    echo htmlspecialchars($_POST["Date_End"]);
}
echo "\"/></td>\n\t\t\t\t\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t\t\t\t</tbody></table>\n\t\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t</tbody>\n\t\t\t\t\t\t\t</table>\n\n\t\t\t\t\t\t\t<script type=\"text/javascript\">\n\t\t\t\t\t\t\t\$(function(){\n\t\t\t\t\t\t\t\t\$('#download_export_btn').click(function(){\n\t\t\t\t\t\t\t\t\t\$('form[name=\"download_pdf_form\"]').submit();\n\t\t\t\t\t\t\t\t});\n\t\t\t\t\t\t\t});\n\t\t\t\t\t\t\t</script>\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t</div></div>\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t<!--left-->\n\t\t\t\t\t</div>\n\t\t\t\t\t<!--left-->\n\t\t\t\t\t\n\t\t\t\t\t<!--right-->\n\t\t\t\t\t<div class=\"right\">\n\t\t\t\t\t<!--right-->\n\t\t\t\t\t\t<div class=\"setting_help_box\">\n\t\t\t\t\t\t\t";
echo __("download purchase invoices help text");
echo "<br />\n\t\t\t\t\t\t</div>\n\t\t\t\t\t<!--right-->\n\t\t\t\t\t</div>\n\t\t\t\t\t<!--right-->\n\t\t\t\t\t\n\t\t\t\t<!--split2-->\n\t\t\t\t</div>\n\t\t\t\t<!--split2-->\n\t\t\t\t\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\t\n\t<!--box1-->\n\t</div>\n\t<!--box1-->\n\t\n\t<br />\n\t\n\t<p class=\"align_right\">\n\t\t<span id=\"loader_download\" class=\"hide\">\n\t\t\t<img src=\"images/icon_circle_loader_green.gif\" alt=\"laden\" class=\"ico inline\" style=\"margin-bottom:-3px;\" />&nbsp;&nbsp;\n\t\t\t<span class=\"loading_green\">";
echo __("loading");
echo "</span>&nbsp;&nbsp;\n\t\t</span>\n\t\t<a class=\"button1 alt1 pointer\" id=\"download_export_btn\" onclick=\"\$(this).hide();\$('#loader_download').show();\"><span>";
echo __("download");
echo "</span></a>\n\t</p>\n\t\n\t<!--form-->\n\t</fieldset></form>\n\n\n";
require_once "views/footer.php";

?>