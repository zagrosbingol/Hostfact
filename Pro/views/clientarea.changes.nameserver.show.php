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
echo "\n";
if($ClientareaChange->ReferenceObject->Status == 9) {
    echo "\t<div class=\"mark alt1\">\n\t\t<strong>";
    echo __("warning");
    echo "</strong><br />\n\t\t<ul>\n\t\t\t<li>";
    echo __("this domain has been deleted. therefore no changes can be made");
    echo "</li>\n\t\t</ul>\n\t</div>\n\t<br />\n\t";
}
echo "\n\t<div class=\"heading1\">\n\n\t\t<h2>";
echo __("modification domain") . " " . $ClientareaChange->ReferenceObject->Domain . "." . $ClientareaChange->ReferenceObject->Tld;
echo "</h2>\n\n\t\t<a href=\"domains.php?page=show&amp;id=";
echo $ClientareaChange->ReferenceObject->Identifier;
echo "\" class=\"a1 c1 floatr\">\n\t\t\t<span>";
echo __("goto domain");
echo "</span>\n\t\t</a>\n\n\t</div>\n\t<hr />\n\n\t<form name=\"clientarea_change_form\" method=\"post\" action=\"clientareachanges.php?page=";
echo $form_action;
echo "&amp;id=";
echo $ClientareaChange->id;
echo "\">\n\n\t\t<div class=\"box2\" id=\"tabs\">\n\n\t\t\t<div class=\"top\">\n\t\t\t\t<ul class=\"list3\">\n\t\t\t\t\t<li class=\"on\">\n\t\t\t\t\t\t<a href=\"#tab-general\">";
echo __("nameservers");
echo "</a>\n\t\t\t\t\t</li>\n\t\t\t\t</ul>\n\t\t\t</div>\n\n\t\t\t<div class=\"content\" id=\"tab-general\">\n\n\t\t\t\t<div class=\"box3\">\n\t\t\t\t\t<h3>";
echo __("modifications");
echo "</h3>\n\t\t\t\t\t<div class=\"content lineheight2\">\n\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("nameserver 1");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">\n\t\t\t\t\t\t\t";
echo $ClientareaChange->Data->DNS1;
if(isset($ClientareaChange->Data->DNS1IP) && $ClientareaChange->Data->DNS1IP) {
    echo " (" . $ClientareaChange->Data->DNS1IP . ")";
}
echo "\t\t\t\t\t\t</span>\n\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("nameserver 2");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">\n\t\t\t\t\t\t\t";
echo $ClientareaChange->Data->DNS2;
if(isset($ClientareaChange->Data->DNS2IP) && $ClientareaChange->Data->DNS2IP) {
    echo " (" . $ClientareaChange->Data->DNS2IP . ")";
}
echo "\t\t\t\t\t\t</span>\n\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("nameserver 3");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">\n\t\t\t\t\t\t\t";
echo $ClientareaChange->Data->DNS3;
if(isset($ClientareaChange->Data->DNS3IP) && $ClientareaChange->Data->DNS3IP) {
    echo " (" . $ClientareaChange->Data->DNS3IP . ")";
}
echo "\t\t\t\t\t\t</span>\n\n\t\t\t\t\t</div>\n\t\t\t\t</div>\n\n\t\t\t\t<br />\n\n\t\t\t</div>\n\t\t</div>\n\n\t\t<br />\n\n\t</form>\n\n";
require_once "views/footer.php";

?>