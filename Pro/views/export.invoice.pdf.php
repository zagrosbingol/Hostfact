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
echo "\n\t\n<!--heading1-->\n<div class=\"heading1\">\n<!--heading1-->\n\n\t<h2>";
echo __("export pdf invoice header title");
echo "</h2>\n\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n<!--form-->\n<form id=\"ExportTemplateForm\" name=\"download_pdf_form\" method=\"post\" action=\"export.php?page=invoicepdf\"><fieldset>\n<!--form-->\n\n<!--box1-->\n<div class=\"box2\" id=\"tabs\">\n<!--box1-->\n\n\t<!--top-->\n\t<div class=\"top\">\n\t<!--top-->\n\t\n\t\t<ul class=\"list3\">\n\t\t\t<li class=\"on\"><a href=\"#tab-";
echo strtolower(__("general"));
echo "\">";
echo __("general");
echo "</a></li>\n\t\t</ul>\n\t\n\t<!--top-->\n\t</div>\n\t<!--top-->\n\t\n\t<!--content-->\n\t<div class=\"content\" id=\"tab-";
echo strtolower(__("general"));
echo "\">\n\t<!--content-->\n\t\t\n\t\t\t<!--split2-->\n\t\t\t<div class=\"split2\">\n\t\t\t<!--split2-->\n\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t<div class=\"left\">\n\t\t\t\t<!--left-->\n\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("filters");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t\t\t<table>\n\t\t\t\t\t\t\t\t<tbody>\n\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t\t\t<strong>";
echo __("invoicecode");
echo "</strong>\n\t\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t<td class=\"paddingb\">\n\t\t\t\t\t\t\t\t\t\t\t<table>\n\t\t\t\t\t\t\t\t\t\t\t\t<tbody><tr>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<input type=\"text\" name=\"InvoiceCode_Start\" class=\"text1 size7\" ";
if(!empty($postData) && $postData["InvoiceCode_Start"]) {
    echo "value=\"" . $postData["InvoiceCode_Start"] . "\"";
}
echo " />\n\t\t\t\t\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<td style=\"padding: 0 15px;\" align=\"center\">";
echo __("from till");
echo "</td>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<input type=\"text\" name=\"InvoiceCode_End\" class=\"text1 size7\" ";
if(!empty($postData) && $postData["InvoiceCode_End"]) {
    echo "value=\"" . $postData["InvoiceCode_End"] . "\"";
}
echo " />\n\t\t\t\t\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t\t\t\t</tbody></table>\n\t\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t\t\t<strong>";
echo __("invoice date");
echo "</strong>\n\t\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t<td class=\"paddingb\">\n\t\t\t\t\t\t\t\t\t\t\t<table>\n\t\t\t\t\t\t\t\t\t\t\t\t<tbody><tr>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<td><input type=\"text\" name=\"Date_Start\" class=\"text1 size7 datepicker\" ";
if(!empty($postData) && $postData["Date_Start"]) {
    echo "value=\"" . $postData["Date_Start"] . "\"";
}
echo " /></td>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<td style=\"padding: 0 15px;\" align=\"center\">";
echo __("from till");
echo "</td>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<td><input type=\"text\" name=\"Date_End\" class=\"text1 size7 datepicker\" ";
if(!empty($postData) && $postData["Date_End"]) {
    echo "value=\"" . $postData["Date_End"] . "\"";
}
echo " /></td>\n\t\t\t\t\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t\t\t\t</tbody></table>\n\t\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t\t\t<strong>";
echo __("status");
echo "</strong>\n\t\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t<td class=\"paddingb\">\n\t\t\t\t\t\t\t\t\t\t\t";
foreach ($array_invoicestatus as $statusId => $statusName) {
    $checked = "";
    if(isset($postData["Status"]) && in_array($statusId, $postData["Status"]) || empty($postData) && (int) $statusId !== 0) {
        $checked = "checked=\"checked\" ";
    }
    echo "<label><input type=\"checkbox\" value=\"" . $statusId . "\" name=\"Status[]\" " . $checked . "/> " . $statusName . "</label><br />";
}
echo "\t\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t</tbody>\n\t\t\t\t\t\t\t</table>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t</form>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<script type=\"text/javascript\">\n\t\t\t\t\t\t\$(function(){\n\t\t\t\t\t\t\t\$('#download_pdf_export_btn').click(function(){\n\t\t\t\t\t\t\t\t\$('form[name=\"download_pdf_form\"]').submit();\n\t\t\t\t\t\t\t});\n\t\t\t\t\t\t});\n\t\t\t\t\t\t</script>\n\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t</div>\n\t\t\t\t<!--left-->\n\t\t\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t<div class=\"right\">\n\t\t\t\t<!--right-->\n\t\t\t\t\t<div class=\"setting_help_box\">\n\t\t\t\t\t\t";
echo __("export pdf invoices help text");
echo "<br />\n\t\t\t\t\t</div>\n\t\t\t\t<!--right-->\n\t\t\t\t</div>\n\t\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t<!--split2-->\n\t\t\t</div>\n\t\t\t<!--split2-->\n\t\t\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\n<!--box1-->\n</div>\n<!--box1-->\n\n<br />\n\n<p class=\"align_right\">\n\t<span id=\"loader_download\" class=\"hide\">\n\t\t<img src=\"images/icon_circle_loader_green.gif\" alt=\"laden\" class=\"ico inline\" style=\"margin-bottom:-3px;\" />&nbsp;&nbsp;\n\t\t<span class=\"loading_green\">";
echo __("loading");
echo "</span>&nbsp;&nbsp;\n\t</span>\n\t<a class=\"button1 alt1 pointer\" id=\"download_pdf_export_btn\" onclick=\"\$(this).hide();\$('#loader_download').show();\"><span>";
echo __("download");
echo "</span></a>\n</p>\n\n<!--form-->\n</fieldset></form>\n<!--form-->\n\n\n";
require_once "views/footer.php";

?>