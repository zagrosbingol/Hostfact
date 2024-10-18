<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "<div id=\"concept_date_change\" class=\"hide\" title=\"";
echo __("add an existing invoice");
echo "\">\n\t";
echo __("edit invoice details below");
echo "<br />\n\t\n\t<table border=\"0\" cellpadding=\"0\" cellspacing=\"3\">\n\t\t<tr>\n\t\t\t<td width=\"130\">";
echo __("status of the invoice");
echo ":</td>\n\t\t\t<td><select name=\"ConceptDateChangeStatus\" class=\"text1 size10\">\n\t\t\t\t\t<option value=\"\">";
echo __("please choose");
echo "</option>\n\t\t\t\t\t";
foreach ($array_invoicestatus as $key => $value) {
    if(0 < $key) {
        echo "<option value=\"";
        echo $key;
        echo "\">";
        echo $value;
        echo "</option>";
    }
}
echo "\t\t\t\t</select>\n\t\t\t</td>\n\t\t</tr>\n\t\t<tr>\n\t\t\t<td>";
echo __("invoicecode");
echo ":</td>\n\t\t\t<td><input type=\"text\" name=\"ConceptDateChangeInvoiceCode\" value=\"";
echo $invoice->NewNumber;
echo "\" class=\"text1 size7\" /></td>\n\t\t</tr>\n\t\t<tr>\n\t\t\t<td>";
echo __("invoice date");
echo ":</td>\n\t\t\t<td><input type=\"text\" class=\"text1 size7 datepicker\" name=\"ConceptDateChangeInvoiceDate\" value=\"";
echo $invoice->Date;
echo "\"/></td>\n\t\t</tr>\n\t</table>\n\t<br />\n\t\n\t<p><a id=\"concept_date_change_btn\" class=\"button2 alt1 float_left\"><span>";
echo __("proceed");
echo "</span></a></p>\n\t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#concept_date_change').dialog('close');\"><span>";
echo __("cancel");
echo "</span></a></p>\n</div>";

?>