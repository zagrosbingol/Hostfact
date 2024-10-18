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
echo "\n<!--form-->\n<form id=\"OrderForm\" name=\"form_create\" method=\"post\" action=\"?page=";
echo htmlspecialchars($page);
echo "&amp;action=edit&amp;page=old\"><fieldset><legend>";
echo __("orderpanel");
echo "</legend>\n<!--form-->\n\n<!--heading1-->\n<div class=\"heading1\">\n<!--heading1-->\n\n\t<h2>";
echo __("orderform");
echo "</h2>\n\t\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n<!--optionsbar-->\n<div class=\"optionsbar\">\n<!--optionsbar-->\n\t<p class=\"pos2\"><a href=\"orderform.php?page=new\" class=\"a1 c1\">";
echo __("orderform settings new form");
echo "</a></p>\n<!--optionsbar-->\n</div>\n<!--optionsbar-->\n\n<!--box1-->\n<div class=\"box2\" id=\"tabs\">\n<!--box1-->\n\n\t<!--top-->\n\t<div class=\"top\">\n\t<!--top-->\n\t\n\t\t<ul class=\"list3\">\n\t\t\t<li class=\"on\"><a href=\"#tab-general\">";
echo __("settings");
echo "</a></li>\n\t\t\t<li><a href=\"#tab-products\">";
echo __("productgroups for orderform");
echo "</a></li>\n\t\t</ul>\n\t\t\n\t<!--top-->\n\t</div>\n\t<!--top-->\n\n\t<!--content-->\n\t<div class=\"content\" id=\"tab-general\">\n\t<!--content-->\n\n\t\t<!--split2-->\n\t\t<div class=\"split2\">\n\t\t<!--split2-->\n\t\t\n\t\t\t<!--left-->\n\t\t\t<div class=\"left\">\n\t\t\t<!--left-->\n\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
echo __("general settings");
echo "</h3><div class=\"content\">\n\t\t\t\t<!--box3-->\n\n\t\t\t\t\t<strong class=\"title\">";
echo __("orderform");
echo "</strong>\n\t\t\t\t\t<select name=\"ORDERFORM_ENABLED\" class=\"text1 size4f\">\n\t\t\t\t\t\t<option value=\"yes\" ";
if("yes" == ORDERFORM_ENABLED) {
    echo "selected=\"selected\"";
}
echo ">";
echo __("enabled");
echo "</option>\n\t\t\t\t\t\t<option value=\"no\" ";
if("no" == ORDERFORM_ENABLED) {
    echo "selected=\"selected\"";
}
echo ">";
echo __("disabled");
echo "</option>\n\t\t\t\t\t</select>\n\t\t\t\t\t<br /><br />\n\t\t\t\t\t\n\t\t\t\t\t<div class=\"orderform_enabled ";
if("no" == ORDERFORM_ENABLED) {
    echo "hide";
}
echo "\">\n\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("companyname");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" name=\"COMPANY_NAME\" class=\"text1 size1\" value=\"";
echo COMPANY_NAME;
echo "\" ";
$ti = tabindex($ti);
echo " />\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("general email");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" name=\"COMPANY_EMAIL\" class=\"text1 size1\" value=\"";
echo COMPANY_EMAIL;
echo "\" ";
$ti = tabindex($ti);
echo " />\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("logo for printing");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" name=\"COMPANY_LOGO\" class=\"text1 size1\" value=\"";
echo COMPANY_LOGO;
echo "\" ";
$ti = tabindex($ti);
echo " />\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("relative path terms");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" name=\"COMPANY_AV_HTML\" class=\"text1 size1\" value=\"";
echo COMPANY_AV_HTML;
echo "\" ";
$ti = tabindex($ti);
echo " />\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("url terms");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" name=\"COMPANY_AV_PDF\" class=\"text1 size1\" value=\"";
echo COMPANY_AV_PDF;
echo "\" ";
$ti = tabindex($ti);
echo " /> <span id=\"orderform-terms-url-img\" class=\"loading_float\"></span>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\n\t\t\t\t\t</div>\n\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3 orderform_enabled ";
if("no" == ORDERFORM_ENABLED) {
    echo "hide";
}
echo "\"><h3>";
echo __("preferences");
echo "</h3><div class=\"content\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t\t<strong class=\"title\">";
echo __("send confirmationmail");
echo "</strong>\n\t\t\t\t\t<select name=\"ORDERMAIL_SENT\" class=\"text1 size4f\">\n\t\t\t\t\t\t<option value=\"yes\" ";
if("yes" == ORDERMAIL_SENT) {
    echo "selected=\"selected\"";
}
echo ">";
echo __("do send confirmationmail");
echo "</option>\n\t\t\t\t\t\t<option value=\"no\" ";
if("no" == ORDERMAIL_SENT) {
    echo "selected=\"selected\"";
}
echo ">";
echo __("do not send confirmationmail");
echo "</option>\n\t\t\t\t\t</select>\n\t\t\t\t\t<br /><br />\n\t\t\t\t\t\n\t\t\t\t\t<div id=\"ordermail_sent_div\" ";
if("no" == ORDERMAIL_SENT) {
    echo "class=\"hide\"";
}
echo ">\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("email copy confirmationmail");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" name=\"ORDERMAIL_SENT_BCC\" class=\"text1 size1\" value=\"";
echo check_email_address(ORDERMAIL_SENT_BCC, "convert", ", ");
echo "\" ";
$ti = tabindex($ti);
echo " />\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t</div>\n\n\t\t\t\t\t<strong class=\"title\">";
echo __("accept multiple domains");
echo "</strong>\n\t\t\t\t\t<select name=\"MULTIPLE_DOMAIN\" class=\"text1 size4f\">\n\t\t\t\t\t\t<option value=\"yes\" ";
if("yes" == MULTIPLE_DOMAIN) {
    echo "selected=\"selected\"";
}
echo ">";
echo __("possible");
echo "</option>\n\t\t\t\t\t\t<option value=\"no\" ";
if("no" == MULTIPLE_DOMAIN) {
    echo "selected=\"selected\"";
}
echo ">";
echo __("not possible");
echo "</option>\n\t\t\t\t\t</select>\n\t\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t<!--left-->\n\t\t\t</div>\n\t\t\t<!--left-->\n\n\t\t\t<!--right-->\n\t\t\t<div class=\"right\">\n\t\t\t<!--right-->\n\t\t\t\n\t\t\t\t<div class=\"setting_help_box\">\n\t\t\t\t\t<strong>";
echo __("explanation");
echo "</strong><br />\n\t\t\t\t\t";
echo __("orderform explanation");
echo "\t\t\t\t</div>\n\t\t\t\t\t\n\t\t\t<!--right-->\n\t\t\t</div>\n\t\t\t<!--right-->\n\t\t\t\t\n\t\t<!--split2-->\n\t\t</div>\n\t\t<!--split2-->\n\t\t\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\t\n\t<!--content-->\n\t<div class=\"content\" id=\"tab-products\">\n\t<!--content-->\n\n\t\t<!--split2-->\n\t\t<div class=\"split2\">\n\t\t<!--split2-->\n\n\t\t\t<!--left-->\n\t\t\t<div class=\"left\">\n\t\t\t<!--left-->\n\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
echo __("productgroups for orderform");
echo "</h3><div class=\"content\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t\t<strong class=\"title\">";
echo __("productgroup for domains");
echo "</strong>\n\t\t\t\t\t<select name=\"GROUP_DOMAIN\" class=\"text1 size4f\">\n\t\t\t\t\t\t<option value=\"\">";
echo __("please choose");
echo "</option>\n\t\t\t\t\t\t";
foreach ($groups as $k => $v) {
    if(is_numeric($k)) {
        echo "\t\t\t\t\t\t\t<option value=\"";
        echo $k;
        echo "\" ";
        echo $k == GROUP_DOMAIN ? "selected=\"selected\"" : "";
        echo ">";
        echo $v["GroupName"];
        echo "</option>\n\t\t\t\t\t\t";
    }
}
echo "\t\t\t\t\t</select>\n\t\t\t\t\t<br /><br />\n\t\t\t\n\t\t\t\t\t<strong class=\"title\">";
echo __("productgroup for hosting accounts");
echo "</strong>\n\t\t\t\t\t<select name=\"GROUP_HOSTING\" class=\"text1 size4f\">\n\t\t\t\t\t\t<option value=\"\">";
echo __("please choose");
echo "</option>\n\t\t\t\t\t\t";
foreach ($groups as $k => $v) {
    if(is_numeric($k)) {
        echo "\t\t\t\t\t\t\t<option value=\"";
        echo $k;
        echo "\" ";
        echo $k == GROUP_HOSTING ? "selected=\"selected\"" : "";
        echo ">";
        echo $v["GroupName"];
        echo "</option>\n\t\t\t\t\t\t";
    }
}
echo "\t\t\t\t\t</select>\n\t\t\t\t\t<br /><br />\n\t\t\t\n\t\t\t\t\t<strong class=\"title\">";
echo __("productgroup for options");
echo "</strong>\n\t\t\t\t\t<select name=\"GROUP_OPTIES\" class=\"text1 size4f\">\n\t\t\t\t\t\t<option value=\"\">";
echo __("please choose");
echo "</option>\n\t\t\t\t\t\t";
foreach ($groups as $k => $v) {
    if(is_numeric($k)) {
        echo "\t\t\t\t\t\t\t<option value=\"";
        echo $k;
        echo "\" ";
        echo $k == GROUP_OPTIES ? "selected=\"selected\"" : "";
        echo ">";
        echo $v["GroupName"];
        echo "</option>\n\t\t\t\t\t\t";
    }
}
echo "\t\t\t\t\t</select>\n\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\n\t\t\t<!--left-->\n\t\t\t</div>\n\t\t\t<!--left-->\n\t\n\t\t\t<!--right-->\n\t\t\t<div class=\"right\">\n\t\t\t<!--right-->\n\t\n\t\t\t\t<div class=\"setting_help_box\">\n\t\t\t\t\t<strong>";
echo __("productgroups");
echo "</strong><br />\n\t\t\t\t\t";
echo __("orderpanel productgroups explained");
echo "\t\t\t\t</div>\n\t\n\t\t\t<!--right-->\n\t\t\t</div>\n\t\t\t<!--right-->\n\n\t\t<!--split2-->\n\t\t</div>\n\t\t<!--split2-->\n\t\t\n\t\t";
if("no" == ORDERFORM_ENABLED) {
    echo "\t\t<script type=\"text/javascript\">\n\t\t\$(function(){\n\t\t\t\$('#tabs').tabs(\"disable\",1);\n\t\t});\n\t\t</script>\n\t\t";
}
echo "\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\n<!--box1-->\n</div>\n<!--box1-->\n\n<br />\n\t\n";
if(U_ORDERFORM_EDIT) {
    echo "\t\t\t\n<p class=\"align_right\">\n\t<a class=\"button1 alt1\" id=\"form_create_btn\">\n        <span>";
    echo __("btn edit");
    echo "</span>\n    </a>\n</p>\n";
}
echo "\n<!--form-->\n</fieldset></form>\n<!--form-->\n\n";
require_once "views/footer.php";

?>