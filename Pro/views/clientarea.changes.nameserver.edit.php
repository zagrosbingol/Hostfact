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
echo "</h3>\n\t\t\t\t\t<div class=\"content\">\n\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">\n\t\t\t\t\t\t\t";
echo __("nameserver") . " 1";
if($ClientareaChange->ReferenceObject->DNS1 != $ClientareaChange->Data->DNS1) {
    echo "\t\t\t\t\t\t\t\t<span class=\"smallfont marleft_1 normalfont\">\n\t\t\t\t\t\t\t\t\t(";
    echo __("before");
    echo ": ";
    echo $ClientareaChange->ReferenceObject->DNS1;
    echo ")\n\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t<input id=\"DNS1\" name=\"DNS1\" ";
echo $ClientareaChange->ReferenceObject->DNS1 != $ClientareaChange->Data->DNS1 ? "value=\"" . $ClientareaChange->Data->DNS1 . "\" class=\"text1 size1 modified\"" : "value=\"" . $ClientareaChange->ReferenceObject->DNS1 . "\" class=\"text1 size1\"";
echo " />\n\n\t\t\t\t\t\t<br /><br />\n\n\t\t\t\t\t\t";
if($ClientareaChange->ReferenceObject->DNS1IP || $ClientareaChange->ReferenceObject->DNS1IP != $ClientareaChange->Data->DNS1IP) {
    echo "\t\t\t\t\t\t\t<div class=\"tr_ns\">\n\t\t\t\t\t\t\t\t<strong class=\"title\">\n\t\t\t\t\t\t\t\t\t";
    echo __("nameserver ipadres") . " 1";
    if($ClientareaChange->ReferenceObject->DNS1IP && $ClientareaChange->ReferenceObject->DNS1IP != $ClientareaChange->Data->DNS1IP) {
        echo "\t\t\t\t\t\t\t\t\t\t<span class=\"smallfont marleft_1 normalfont\">\n\t\t\t\t\t\t\t\t\t\t(";
        echo __("before");
        echo ": ";
        echo $ClientareaChange->ReferenceObject->DNS1IP;
        echo ")\n\t\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t\t<input id=\"DNS1IP\" name=\"DNS1IP\" ";
    echo $ClientareaChange->ReferenceObject->DNS1IP != $ClientareaChange->Data->DNS1IP ? "value=\"" . $ClientareaChange->Data->DNS1IP . "\" class=\"text1 size1 modified\"" : "value=\"" . $ClientareaChange->ReferenceObject->DNS1IP . "\" class=\"text1 size1\"";
    echo " />\n\t\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t";
}
echo "\n\t\t\t\t\t\t<strong class=\"title\">\n\t\t\t\t\t\t\t";
echo __("nameserver") . " 2";
if($ClientareaChange->ReferenceObject->DNS2 != $ClientareaChange->Data->DNS2) {
    echo "\t\t\t\t\t\t\t\t<span class=\"smallfont marleft_1 normalfont\">\n\t\t\t\t\t\t\t\t\t(";
    echo __("before");
    echo ": ";
    echo $ClientareaChange->ReferenceObject->DNS2;
    echo ")\n\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t<input id=\"DNS2\" name=\"DNS2\" ";
echo $ClientareaChange->ReferenceObject->DNS2 != $ClientareaChange->Data->DNS2 ? "value=\"" . $ClientareaChange->Data->DNS2 . "\" class=\"text1 size1 modified\"" : "value=\"" . $ClientareaChange->ReferenceObject->DNS2 . "\" class=\"text1 size1\"";
echo " />\n\n\t\t\t\t\t\t<br /><br />\n\n\t\t\t\t\t\t";
if($ClientareaChange->ReferenceObject->DNS2IP || $ClientareaChange->ReferenceObject->DNS2IP != $ClientareaChange->Data->DNS2IP) {
    echo "\t\t\t\t\t\t\t<div class=\"tr_ns\">\n\t\t\t\t\t\t\t\t<strong class=\"title\">\n\t\t\t\t\t\t\t\t\t";
    echo __("nameserver ipadres") . " 2";
    if($ClientareaChange->ReferenceObject->DNS2IP && $ClientareaChange->ReferenceObject->DNS2IP != $ClientareaChange->Data->DNS2IP) {
        echo "\t\t\t\t\t\t\t\t\t\t<span class=\"smallfont marleft_1 normalfont\">\n\t\t\t\t\t\t\t\t\t\t\t(";
        echo __("before");
        echo ": ";
        echo $ClientareaChange->ReferenceObject->DNS2IP;
        echo ")\n\t\t\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t\t<input id=\"DNS2IP\" name=\"DNS2IP\" ";
    echo $ClientareaChange->ReferenceObject->DNS2IP != $ClientareaChange->Data->DNS2IP ? "value=\"" . $ClientareaChange->Data->DNS2IP . "\" class=\"text1 size1 modified\"" : "value=\"" . $ClientareaChange->ReferenceObject->DNS2IP . "\" class=\"text1 size1\"";
    echo " />\n\t\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t";
}
echo "\n\t\t\t\t\t\t<strong class=\"title\">\n\t\t\t\t\t\t\t";
echo __("nameserver") . " 3";
if($ClientareaChange->ReferenceObject->DNS3 != $ClientareaChange->Data->DNS3) {
    echo "\t\t\t\t\t\t\t\t<span class=\"smallfont marleft_1 normalfont\">\n\t\t\t\t\t\t\t\t\t(";
    echo __("before");
    echo ": ";
    echo $ClientareaChange->ReferenceObject->DNS3;
    echo ")\n\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t<input id=\"DNS3\" name=\"DNS3\" ";
echo $ClientareaChange->ReferenceObject->DNS3 != $ClientareaChange->Data->DNS3 ? "value=\"" . $ClientareaChange->Data->DNS3 . "\" class=\"text1 size1 modified\"" : "value=\"" . $ClientareaChange->ReferenceObject->DNS3 . "\" class=\"text1 size1\"";
echo " />\n\n\t\t\t\t\t\t<br /><br />\n\n\t\t\t\t\t\t";
if($ClientareaChange->ReferenceObject->DNS3IP || $ClientareaChange->ReferenceObject->DNS3IP != $ClientareaChange->Data->DNS3IP) {
    echo "\t\t\t\t\t\t\t<div class=\"tr_ns\">\n\t\t\t\t\t\t\t\t<strong class=\"title\">\n\t\t\t\t\t\t\t\t\t";
    echo __("nameserver ipadres") . " 3";
    if($ClientareaChange->ReferenceObject->DNS3IP && $ClientareaChange->ReferenceObject->DNS3IP != $ClientareaChange->Data->DNS3IP) {
        echo "\t\t\t\t\t\t\t\t\t\t<span class=\"smallfont marleft_1 normalfont\">\n\t\t\t\t\t\t\t\t\t\t\t(";
        echo __("before");
        echo ": ";
        echo $ClientareaChange->ReferenceObject->DNS3IP;
        echo ")\n\t\t\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t\t<input id=\"DNS3IP\" name=\"DNS3IP\" ";
    echo $ClientareaChange->ReferenceObject->DNS3IP != $ClientareaChange->Data->DNS3IP ? "value=\"" . $ClientareaChange->Data->DNS3IP . "\" class=\"text1 size1 modified\"" : "value=\"" . $ClientareaChange->ReferenceObject->DNS3IP . "\" class=\"text1 size1\"";
    echo " />\n\t\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t";
}
echo "\n\t\t\t\t\t</div>\n\t\t\t\t</div>\n\t\t\t</div>\n\t\t</div>\n\n\t\t<br />\n\n\t</form>\n\n";
if(U_DOMAIN_EDIT) {
    $this->element("clientarea.changes.edit.php");
}
require_once "views/footer.php";

?>