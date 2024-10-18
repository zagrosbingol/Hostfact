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
echo "\n<!--form-->\n<form name=\"form_push\" method=\"post\" action=\"?page=push\"><fieldset><legend>";
echo __("push debtors");
echo "</legend>\n<!--form-->\n\n<!--heading1-->\n<div class=\"heading1\">\n<!--heading1-->\n\n\t<h2>";
echo __("push debtors");
echo "</h2>\n\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n<div class=\"setting_help_box\">\n\t";
echo __("you want to merge debtors, explained");
echo "<br />\n</div>\n\n<br />\n\t\n<!--split2-->\n<div class=\"split2\">\n<!--split2-->\n\t\n\t<!--left-->\n\t<div class=\"left\">\n\t<!--left-->\n\t\n\t\t<!--box3-->\n\t\t<div class=\"box3\"><h3>";
echo __("push debtor from");
echo "</h3><div class=\"content lineheight2\">\n\t\t<!--box3-->\n\t\t\t\t\n\t\t\t<strong class=\"title\">";
echo __("push debtor from debtor");
echo "</strong>\n\t\t\t<input type=\"hidden\" name=\"FromDebtor\" value=\"\" />\n\t\t\t";
createAutoComplete("debtor", "FromDebtor", "", ["class" => "size12"]);
echo "\t\t\t<br />\n\t\t\t<br />\n\t\t\t\n\t\t\t<div id=\"EnveloppeFromDebtor\"></div>\n\t\t\t\n\t\t\t<div id=\"checkbox_div\" class=\"hide\">\n\t\t\t\t<br />\n\t\t\t\t<strong class=\"title\">";
echo __("which debtor data must be merged");
echo "</strong>\n\t\t\t\t\n\t\t\t\t<label id=\"label_Invoices\" class=\"hide\"><input type=\"checkbox\" name=\"Invoices\" value=\"yes\" /> <span></span> ";
echo strtolower(__("invoices"));
echo "<br /></label>\n\t\t\t\t<label id=\"label_Orders\" class=\"hide\"><input type=\"checkbox\" name=\"Orders\" value=\"yes\" /> <span></span> ";
echo strtolower(__("orders"));
echo "<br /></label>\n\t\t\t\t<label id=\"label_PriceQuotes\" class=\"hide\"><input type=\"checkbox\" name=\"PriceQuotes\" value=\"yes\" /> <span></span> ";
echo strtolower(__("pricequotes"));
echo "<br /></label>\n\t\t\t\t<label id=\"label_Services\" class=\"hide\"><input type=\"checkbox\" name=\"Services\" value=\"yes\" /> <span></span> ";
echo strtolower(__("services and handles"));
echo "<br /></label>\n\t\t\t\t<label id=\"label_Interactions\" class=\"hide\"><input type=\"checkbox\" name=\"Interactions\" value=\"yes\" /> <span></span> ";
echo strtolower(__("interactions"));
echo "<br /></label>\n\t\t\t\t<label id=\"label_Tickets\" class=\"hide\"><input type=\"checkbox\" name=\"Tickets\" value=\"yes\" /> <span></span> ";
echo strtolower(__("tickets"));
echo "<br /></label>\n\t\t\t\t<label id=\"label_Comment\" class=\"hide\"><input type=\"checkbox\" name=\"Comment\" value=\"yes\" /> ";
echo strtolower(__("comment"));
echo "<br /></label>\n\t\t\t\t\n\t\t\t\t<div id=\"warning_notallchecked\" class=\"hide\"><br />";
echo __("debtor push warning debtor will not be deleted");
echo "</div>\n\t\t\t</div>\t\t\n\t\t\t\t\t\n\t\t<!--box3-->\n\t\t</div></div>\n\t\t<!--box3-->\n\t\t\n\t<!--left-->\n\t</div>\n\t<!--left-->\n\t\t\n\t<!--right-->\n\t<div class=\"right\">\n\t<!--right-->\n\t\n\t\t<!--box3-->\n\t\t<div class=\"box3\"><h3>";
echo __("merge with debtor");
echo "</h3><div class=\"content lineheight2\">\n\t\t<!--box3-->\n\t\t\t\n\t\t\t<strong class=\"title\">";
echo __("to which debtor  must be merged to");
echo "</strong>\n\t\t\t<input type=\"hidden\" name=\"ToDebtor\" value=\"\" />\n\t\t\t";
createAutoComplete("debtor", "ToDebtor", "", ["class" => "size12"]);
echo "\t\t\t<br />\n\t\t\t<br />\n\t\t\t\n\t\t\t<div id=\"EnveloppeToDebtor\"></div>\n\t\t\t\n\t\t<!--box3-->\n\t\t</div></div>\n\t\t<!--box3-->\n\t\t\t\n\t\t\n\t<!--right-->\n\t</div>\n\t<!--right-->\n\n<!--split2-->\n</div>\n<!--split2-->\n\n<!--buttonbar-->\n<div class=\"buttonbar\">\n<!--buttonbar-->\n\n\t<p class=\"pos2\"><a id=\"push_debtor_submit_btn\" class=\"button1 alt1\"><span>";
echo __("merge debtors");
echo "</span></a></p>\n\t\t\t\t\n<!--buttonbar-->\n</div>\n<!--buttonbar-->\n\n</form>\n\n<script type=\"text/javascript\">\n\$(function(){\t\n\t\$('input[name=\"FromDebtor\"]').change(function(){\n\t\tif(\$(this).val() == ''){\n\t\t\t\$('#EnveloppeFromDebtor').html('');\n\t\t\t\$('#checkbox_div').hide();\n\t\t\treturn false;\t\n\t\t}\n\t\t\$.post(\"XMLRequest.php\", { action: 'get_debtor', debtor: \$(this).val(), return_pushdata: 'true' }, function(data){\n\t\t\t\t\t\t\n\t\t\t\$('#EnveloppeFromDebtor').html('');\n\t\t\tif(data.CompanyName){\n\t\t\t\t\$('#EnveloppeFromDebtor').html(htmlspecialchars(data.CompanyName) + '<br />');\n\t\t\t}\n\t\t\t\$('#EnveloppeFromDebtor').append(htmlspecialchars(data.Initials + ' ' + data.SurName) + '<br />');\n\t\t\t\$('#EnveloppeFromDebtor').append(htmlspecialchars(data.Address) + '<br />');\n\t\t\t\$('#EnveloppeFromDebtor').append(htmlspecialchars(data.ZipCode + ' ' + data.City) + '<br />');\n\t\t\t\$('#EnveloppeFromDebtor').append(htmlspecialchars(data.CountryLong) + '<br />');\n\t\t\t\n\t\t\tif(data.PhoneNumber || data.MobileNumber || data.EmailAddress)\n\t\t\t{\n\t\t\t\t\$('#EnveloppeFromDebtor').append('<br />');\n\t\t\t\tif(data.EmailAddress){\n\t\t\t\t\t\$('#EnveloppeFromDebtor').append(htmlspecialchars(data.EmailAddress).replace(/;/g, ', ') + '<br />');\n\t\t\t\t}\n\t\t\t\tif(data.PhoneNumber){\n\t\t\t\t\t\$('#EnveloppeFromDebtor').append(htmlspecialchars(data.PhoneNumber) + '<br />');\n\t\t\t\t}\n\t\t\t\tif(data.MobileNumber){\n\t\t\t\t\t\$('#EnveloppeFromDebtor').append(htmlspecialchars(data.MobileNumber) + '<br />');\n\t\t\t\t}\n\t\t\t}\n\t\t\t\n\t\t\t// Update checkboxes\n\t\t\tvar CheckBoxes = new Array(\"Invoices\",\"Orders\",\"PriceQuotes\", \"Services\", \"Interactions\", \"Tickets\", \"Comment\");\n\t\t\tfor(var i=0; i < CheckBoxes.length; i++){\n\t\t\t\tif(data.PushData[CheckBoxes[i]] > 0){\n\t\t\t\t\t\$('input[name=\"'+CheckBoxes[i]+'\"]').prop('checked',true);\n\t\t\t\t\t\$('#label_'+CheckBoxes[i]).find('span').html(data.PushData[CheckBoxes[i]]);\n\t\t\t\t\t\$('#label_'+CheckBoxes[i]).show();\n\t\t\t\t}else{\n\t\t\t\t\t\$('input[name=\"'+CheckBoxes[i]+'\"]').prop('checked',false);\n\t\t\t\t\t\$('#label_'+CheckBoxes[i]).hide();\n\t\t\t\t}\n\t\t\t}\t\n\n\t\t\tif(\$('#checkbox_div').find('input:checked').length > 0){\n\t\t\t\t\$('#checkbox_div').show();\n\t\t\t}else{\n\t\t\t\t\$('#checkbox_div').hide();\n\t\t\t}\n\t\t\t\n\t\t}, 'json');\n\t});\n\t\n\t\$('#checkbox_div input[type=\"checkbox\"]').click(function(){\n\t\t\$('#warning_notallchecked').hide();\n\n\t\t\$(\$('#checkbox_div').find('label:visible')).each(function(index, tmpElement){\n            console.log('test', \$(tmpElement).find('input[type=\"checkbox\"]').prop('checked'));\n\n\t\t\tif(\$(tmpElement).attr('id') != \"label_Comment\" && \$(tmpElement).find('input[type=\"checkbox\"]').prop('checked') === false){\n\t\t\t\t\$('#warning_notallchecked').show();\n\t\t\t}\t\n\t\t});\t\t\t\n\t});\n\t\n\t\$('input[name=\"ToDebtor\"]').change(function(){\n\t\tif(\$(this).val() == ''){\n\t\t\t\$('#EnveloppeToDebtor').html('');\n\t\t\treturn false;\t\n\t\t}\n\t\t\$.post(\"XMLRequest.php\", { action: 'get_debtor', debtor: \$(this).val(), return_pushdata: 'false' }, function(data){\n\t\t\t\t\t\t\n\t\t\t\$('#EnveloppeToDebtor').html('');\n\t\t\tif(data.CompanyName){\n\t\t\t\t\$('#EnveloppeToDebtor').html(htmlspecialchars(data.CompanyName) + '<br />');\n\t\t\t}\n\t\t\t\$('#EnveloppeToDebtor').append(htmlspecialchars(data.Initials + ' ' + data.SurName) + '<br />');\n\t\t\t\$('#EnveloppeToDebtor').append(htmlspecialchars(data.Address) + '<br />');\n\t\t\t\$('#EnveloppeToDebtor').append(htmlspecialchars(data.ZipCode + ' ' + data.City) + '<br />');\n\t\t\t\$('#EnveloppeToDebtor').append(htmlspecialchars(data.CountryLong) + '<br />');\n\t\t\t\n\t\t\tif(data.PhoneNumber || data.MobileNumber || data.EmailAddress)\n\t\t\t{\n\t\t\t\t\$('#EnveloppeToDebtor').append('<br />');\n\t\t\t\tif(data.EmailAddress){\n\t\t\t\t\t\$('#EnveloppeToDebtor').append(htmlspecialchars(data.EmailAddress.replace(/;/g, ', ')) + '<br />');\n\t\t\t\t}\n\t\t\t\tif(data.PhoneNumber){\n\t\t\t\t\t\$('#EnveloppeToDebtor').append(htmlspecialchars(data.PhoneNumber) + '<br />');\n\t\t\t\t}\n\t\t\t\tif(data.MobileNumber){\n\t\t\t\t\t\$('#EnveloppeToDebtor').append(htmlspecialchars(data.MobileNumber) + '<br />');\n\t\t\t\t}\n\t\t\t}\n\t\t\t\n\t\t}, 'json');\n\t});\n\t\n\t\$('#push_debtor_submit_btn').click(function(){\n\t\t\$('form[name=\"form_push\"]').submit();\n\t});\n});\n</script>\n";
require_once "views/footer.php";

?>