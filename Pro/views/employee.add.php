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
$page_form_title = $pagetype == "edit" ? __("edit employee") : __("add employee");
echo "\n";
echo $message;
echo "\t\n\n<!--form-->\n<form id=\"EmployeeForm\" name=\"form_create\" method=\"post\" action=\"company.php?page=account&action=";
echo $pagetype;
echo "&id=";
echo $acc->Identifier;
echo "\"><fieldset><legend>";
echo $page_form_title;
echo "</legend>\n<!--form-->\n\n\t<input type=\"hidden\" name=\"Identifier\" value=\"";
echo $acc->Identifier;
echo "\" />\n\n\t<!--heading1-->\n\t<div class=\"heading1\">\n\t<!--heading1-->\n\t\n\t\t<h2>";
echo $page_form_title;
echo "</h2>\n\t\t\n\t<!--heading1-->\n\t</div><hr />\n\t<!--heading1-->\n\t\n\t<!--box1-->\n\t<div class=\"box2\" id=\"tabs\">\n\t<!--box1-->\n\t\n\t\t<!--top-->\n\t\t<div class=\"top\">\n\t\t<!--top-->\n\t\t\n\t\t\t<ul class=\"list3\">\n\t\t\t\t<li class=\"on\"><a href=\"#tab-general\">";
echo __("general");
echo "</a></li>\n\t\t\t\t";
if(0 < $acc->Identifier) {
    echo "<li><a href=\"#tab-dashboard\">";
    echo __("dashboard");
    echo "</a></li>";
}
echo "\t\t\t\t<li><a href=\"#tab-ticketsystem\">";
echo __("ticketsystem");
echo "</a></li>\n\t\t\t</ul>\n\t\t\n\t\t<!--top-->\n\t\t</div>\n\t\t<!--top-->\n\n\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-general\">\n\t\t<!--content-->\n\n\t\t\t<!--split2-->\n\t\t\t<div class=\"split2\">\n\t\t\t<!--split2-->\n\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t<div class=\"left\">\n\t\t\t\t<!--left-->\n\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("employee data");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("name");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"Name\" value=\"";
if($acc->Name) {
    echo $acc->Name;
}
echo "\" ";
$ti = tabindex($ti);
echo " />\t\t\t\t\t\t\t\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("function");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"Function\" value=\"";
if($acc->Function) {
    echo $acc->Function;
}
echo "\" ";
$ti = tabindex($ti);
echo " />\t\t\t\t\t\t\t\n\t\t\t\t\t\t<br /><br />\n                        \n                        <strong class=\"title\">";
echo __("backoffice language");
echo "</strong>\n                        <select class=\"text1 size1\" name=\"Language\">\n                            ";
global $array_backoffice_languages;
foreach ($array_backoffice_languages as $_lang_key => $_lang_translation) {
    echo "                                    <option value=\"";
    echo $_lang_key;
    echo "\" ";
    echo $acc->Language == $_lang_key ? "selected=\"selected\"" : "";
    echo ">\n                                        ";
    echo $_lang_translation;
    echo "                                    </option>\n                                    ";
}
echo "                        </select>\n\t\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("login data employee");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("username");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"UserName\" autocomplete=\"off\" value=\"";
if($acc->UserName) {
    echo $acc->UserName;
}
echo "\" ";
$ti = tabindex($ti);
echo " />\t\t\t\t\t\t\t\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("password");
echo "</strong>\n\t\t\t\t\t\t<input type=\"password\" class=\"text1 size1\" name=\"Password\" autocomplete=\"off\" ";
$ti = tabindex($ti);
echo " />\t\t\t\t\t\t\t\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("password again");
echo "</strong>\n\t\t\t\t\t\t<input type=\"password\" class=\"text1 size1\" name=\"PasswordAgain\" autocomplete=\"off\" ";
$ti = tabindex($ti);
echo " />\t\t\t\t\t\t\t\n\t\t\t\t\t\t<br /><br />\n\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("contact data");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("emailaddress");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"EmailAddress\" value=\"";
if($acc->EmailAddress) {
    echo $acc->EmailAddress;
}
echo "\" ";
$ti = tabindex($ti);
echo " />\t\t\t\t\t\t\t\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("phonenumber");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"PhoneNumber\" value=\"";
if($acc->PhoneNumber) {
    echo $acc->PhoneNumber;
}
echo "\" ";
$ti = tabindex($ti);
echo " />\t\t\t\t\t\t\t\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("mobilenumber");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"MobileNumber\" value=\"";
if($acc->MobileNumber) {
    echo $acc->MobileNumber;
}
echo "\" ";
$ti = tabindex($ti);
echo " />\t\t\t\t\t\t\t\n\t\t\t\t\t\t<br /><br />\n\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t</div>\n\t\t\t\t<!--left-->\n\n\t\t\t\t<!--right-->\n\t\t\t\t<div class=\"right\">\n\t\t\t\t<!--right-->\n                \n                ";
if(U_COMPANY_EDIT) {
    echo "\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
    echo __("userrights");
    echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t\t<table>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td width=\"200\"><strong>";
    echo __("relations");
    echo "</strong></td>\n\t\t\t\t\t\t\t\t<td width=\"80\" class=\"smallfont\"><a onclick=\"checkColumn('EmployeeForm','SHOW');\" class=\"c1 a1\">";
    echo __("view");
    echo "</a></td>\n\t\t\t\t\t\t\t\t<td width=\"80\" class=\"smallfont\"><a onclick=\"checkColumn('EmployeeForm','EDIT');\" class=\"c1 a1\">";
    echo __("edit");
    echo "</a></td>\n\t\t\t\t\t\t\t\t<td width=\"80\" class=\"smallfont\"><a onclick=\"checkColumn('EmployeeForm','DELETE');\" class=\"c1 a1\">";
    echo __("remove");
    echo "</a></td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td><a class=\"a1\" onclick=\"checkRow('EmployeeForm','DEBTOR');\">";
    echo __("debtors");
    echo "</a></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[DEBTOR_SHOW]\" value=\"1\" ";
    if(isset($acc->U_DEBTOR_SHOW) && $acc->U_DEBTOR_SHOW == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[DEBTOR_EDIT]\" value=\"1\" ";
    if(isset($acc->U_DEBTOR_EDIT) && $acc->U_DEBTOR_EDIT == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[DEBTOR_DELETE]\" value=\"1\" ";
    if(isset($acc->U_DEBTOR_DELETE) && $acc->U_DEBTOR_DELETE == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td><a class=\"a1\" onclick=\"checkRow('EmployeeForm','CREDITOR');\">";
    echo __("creditors");
    echo "</a></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[CREDITOR_SHOW]\" value=\"1\" ";
    if(isset($acc->U_CREDITOR_SHOW) && $acc->U_CREDITOR_SHOW == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[CREDITOR_EDIT]\" value=\"1\" ";
    if(isset($acc->U_CREDITOR_EDIT) && $acc->U_CREDITOR_EDIT == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[CREDITOR_DELETE]\" value=\"1\" ";
    if(isset($acc->U_CREDITOR_DELETE) && $acc->U_CREDITOR_DELETE == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td><a class=\"a1\" onclick=\"checkRow('EmployeeForm','CREDITOR_INVOICE');\">";
    echo __("creditinvoices");
    echo "</a></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[CREDITOR_INVOICE_SHOW]\" value=\"1\" ";
    if(isset($acc->U_CREDITOR_INVOICE_SHOW) && $acc->U_CREDITOR_INVOICE_SHOW == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[CREDITOR_INVOICE_EDIT]\" value=\"1\" ";
    if(isset($acc->U_CREDITOR_INVOICE_EDIT) && $acc->U_CREDITOR_INVOICE_EDIT == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[CREDITOR_INVOICE_DELETE]\" value=\"1\" ";
    if(isset($acc->U_CREDITOR_INVOICE_DELETE) && $acc->U_CREDITOR_INVOICE_DELETE == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td colspan=\"4\">&nbsp;</td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td><strong>";
    echo __("services");
    echo "</strong></td>\n\t\t\t\t\t\t\t\t<td class=\"smallfont\"><a onclick=\"checkColumn('EmployeeForm','SHOW');\" class=\"c1 a1\">";
    echo __("view");
    echo "</a></td>\n\t\t\t\t\t\t\t\t<td class=\"smallfont\"><a onclick=\"checkColumn('EmployeeForm','EDIT');\" class=\"c1 a1\">";
    echo __("edit");
    echo "</a></td>\n\t\t\t\t\t\t\t\t<td class=\"smallfont\"><a onclick=\"checkColumn('EmployeeForm','DELETE');\" class=\"c1 a1\">";
    echo __("remove");
    echo "</a></td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t";
    $service_rights = [];
    $service_rights["DOMAIN"] = __("domains");
    $service_rights["HOSTING"] = __("hosting accounts");
    $service_rights["SERVICE"] = __("other services");
    $service_rights = do_filter("employee_service_rights", $service_rights);
    foreach ($service_rights as $tmp_service_right_key => $tmp_service_right_title) {
        echo "\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t<td><a class=\"a1\" onclick=\"checkRow('EmployeeForm','";
        echo $tmp_service_right_key;
        echo "');\">";
        echo $tmp_service_right_title;
        echo "</a></td>\n\t\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[";
        echo $tmp_service_right_key;
        echo "_SHOW]\" value=\"1\" ";
        if(isset($acc->{"U_" . $tmp_service_right_key . "_SHOW"}) && $acc->{"U_" . $tmp_service_right_key . "_SHOW"} == "1" || $pagetype == "add") {
            echo "checked=\"checked\"";
        }
        echo " /></td>\n\t\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[";
        echo $tmp_service_right_key;
        echo "_EDIT]\" value=\"1\" ";
        if(isset($acc->{"U_" . $tmp_service_right_key . "_EDIT"}) && $acc->{"U_" . $tmp_service_right_key . "_EDIT"} == "1" || $pagetype == "add") {
            echo "checked=\"checked\"";
        }
        echo " /></td>\n\t\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[";
        echo $tmp_service_right_key;
        echo "_DELETE]\" value=\"1\" ";
        if(isset($acc->{"U_" . $tmp_service_right_key . "_DELETE"}) && $acc->{"U_" . $tmp_service_right_key . "_DELETE"} == "1" || $pagetype == "add") {
            echo "checked=\"checked\"";
        }
        echo " /></td>\n\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td colspan=\"4\">&nbsp;</td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td><strong>";
    echo __("invoicing");
    echo "</strong></td>\n\t\t\t\t\t\t\t\t<td class=\"smallfont\"><a onclick=\"checkColumn('EmployeeForm','SHOW');\" class=\"c1 a1\">";
    echo __("view");
    echo "</a></td>\n\t\t\t\t\t\t\t\t<td class=\"smallfont\"><a onclick=\"checkColumn('EmployeeForm','EDIT');\" class=\"c1 a1\">";
    echo __("edit");
    echo "</a></td>\n\t\t\t\t\t\t\t\t<td class=\"smallfont\"><a onclick=\"checkColumn('EmployeeForm','DELETE');\" class=\"c1 a1\">";
    echo __("remove");
    echo "</a></td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td><a class=\"a1\" onclick=\"checkRow('EmployeeForm','INVOICE');\">";
    echo __("invoices");
    echo "</a></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[INVOICE_SHOW]\" value=\"1\" ";
    if(isset($acc->U_INVOICE_SHOW) && $acc->U_INVOICE_SHOW == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[INVOICE_EDIT]\" value=\"1\" ";
    if(isset($acc->U_INVOICE_EDIT) && $acc->U_INVOICE_EDIT == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[INVOICE_DELETE]\" value=\"1\" ";
    if(isset($acc->U_INVOICE_DELETE) && $acc->U_INVOICE_DELETE == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td><a class=\"a1\" onclick=\"checkRow('EmployeeForm','PRICEQUOTE');\">";
    echo __("pricequotes");
    echo "</a></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[PRICEQUOTE_SHOW]\" value=\"1\" ";
    if(isset($acc->U_PRICEQUOTE_SHOW) && $acc->U_PRICEQUOTE_SHOW == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[PRICEQUOTE_EDIT]\" value=\"1\" ";
    if(isset($acc->U_PRICEQUOTE_EDIT) && $acc->U_PRICEQUOTE_EDIT == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[PRICEQUOTE_DELETE]\" value=\"1\" ";
    if(isset($acc->U_PRICEQUOTE_DELETE) && $acc->U_PRICEQUOTE_DELETE == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td><a class=\"a1\" onclick=\"checkRow('EmployeeForm','ORDER');\">";
    echo __("orders");
    echo "</a></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[ORDER_SHOW]\" value=\"1\" ";
    if(isset($acc->U_ORDER_SHOW) && $acc->U_ORDER_SHOW == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[ORDER_EDIT]\" value=\"1\" ";
    if(isset($acc->U_ORDER_EDIT) && $acc->U_ORDER_EDIT == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[ORDER_DELETE]\" value=\"1\" ";
    if(isset($acc->U_ORDER_DELETE) && $acc->U_ORDER_DELETE == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td>";
    echo __("subscriptions");
    echo "</td>\n\t\t\t\t\t\t\t\t<td colspan=\"3\"><span class=\"smallfont\">";
    echo __("subscription rights explained");
    echo "</span></td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td colspan=\"4\">&nbsp;</td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td><strong>";
    echo __("ticket system");
    echo "</strong></td>\n\t\t\t\t\t\t\t\t<td class=\"smallfont\"><a onclick=\"checkColumn('EmployeeForm','SHOW');\" class=\"c1 a1\">";
    echo __("view");
    echo "</a></td>\n\t\t\t\t\t\t\t\t<td class=\"smallfont\"><a onclick=\"checkColumn('EmployeeForm','EDIT');\" class=\"c1 a1\">";
    echo __("edit");
    echo "</a></td>\n\t\t\t\t\t\t\t\t<td class=\"smallfont\"><a onclick=\"checkColumn('EmployeeForm','DELETE');\" class=\"c1 a1\">";
    echo __("remove");
    echo "</a></td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td><a class=\"a1\" onclick=\"checkRow('EmployeeForm','TICKET');\">";
    echo __("ticket system");
    echo "</a></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[TICKET_SHOW]\" value=\"1\" ";
    if(isset($acc->U_TICKET_SHOW) && $acc->U_TICKET_SHOW == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[TICKET_EDIT]\" value=\"1\" ";
    if(isset($acc->U_TICKET_EDIT) && $acc->U_TICKET_EDIT == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[TICKET_DELETE]\" value=\"1\" ";
    if(isset($acc->U_TICKET_DELETE) && $acc->U_TICKET_DELETE == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td colspan=\"4\">&nbsp;</td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td><strong>";
    echo __("statistics");
    echo "</strong></td>\n\t\t\t\t\t\t\t\t<td class=\"smallfont\"><a onclick=\"checkColumn('EmployeeForm','SHOW');\" class=\"c1 a1\">";
    echo __("view");
    echo "</a></td>\n\t\t\t\t\t\t\t\t<td class=\"smallfont\">&nbsp;</td>\n\t\t\t\t\t\t\t\t<td class=\"smallfont\">&nbsp;</td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td><a class=\"a1\" onclick=\"checkRow('EmployeeForm','STATISTICS');\">";
    echo __("statistics");
    echo "</a></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[STATISTICS_SHOW]\" value=\"1\" ";
    if(isset($acc->U_STATISTICS_SHOW) && $acc->U_STATISTICS_SHOW == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t\t<td>&nbsp;</td>\n\t\t\t\t\t\t\t\t<td>&nbsp;</td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td colspan=\"4\">&nbsp;</td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td><strong>";
    echo __("agenda");
    echo "</strong></td>\n\t\t\t\t\t\t\t\t<td class=\"smallfont\"><a onclick=\"checkColumn('EmployeeForm','SHOW');\" class=\"c1 a1\">";
    echo __("view");
    echo "</a></td>\n\t\t\t\t\t\t\t\t<td class=\"smallfont\"><a onclick=\"checkColumn('EmployeeForm','EDIT');\" class=\"c1 a1\">";
    echo __("edit");
    echo "</a></td>\n\t\t\t\t\t\t\t\t<td class=\"smallfont\"><a onclick=\"checkColumn('EmployeeForm','DELETE');\" class=\"c1 a1\">";
    echo __("remove");
    echo "</a></td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td><a class=\"a1\" onclick=\"checkRow('EmployeeForm','AGENDA');\">";
    echo __("agenda");
    echo "</a></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[AGENDA_SHOW]\" value=\"1\" ";
    if(isset($acc->U_AGENDA_SHOW) && $acc->U_AGENDA_SHOW == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[AGENDA_EDIT]\" value=\"1\" ";
    if(isset($acc->U_AGENDA_EDIT) && $acc->U_AGENDA_EDIT == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[AGENDA_DELETE]\" value=\"1\" ";
    if(isset($acc->U_AGENDA_DELETE) && $acc->U_AGENDA_DELETE == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td colspan=\"4\">&nbsp;</td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td><strong>";
    echo __("menu.management");
    echo "</strong></td>\n\t\t\t\t\t\t\t\t<td class=\"smallfont\"><a onclick=\"checkColumn('EmployeeForm','SHOW');\" class=\"c1 a1\">";
    echo __("view");
    echo "</a></td>\n\t\t\t\t\t\t\t\t<td class=\"smallfont\"><a onclick=\"checkColumn('EmployeeForm','EDIT');\" class=\"c1 a1\">";
    echo __("edit");
    echo "</a></td>\n\t\t\t\t\t\t\t\t<td class=\"smallfont\"><a onclick=\"checkColumn('EmployeeForm','DELETE');\" class=\"c1 a1\">";
    echo __("remove");
    echo "</a></td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td><a class=\"a1\" onclick=\"checkRow('EmployeeForm','COMPANY');\">";
    echo __("companyinfo employees");
    echo "</a></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[COMPANY_SHOW]\" value=\"1\" ";
    if(isset($acc->U_COMPANY_SHOW) && $acc->U_COMPANY_SHOW == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[COMPANY_EDIT]\" value=\"1\" ";
    if(isset($acc->U_COMPANY_EDIT) && $acc->U_COMPANY_EDIT == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[COMPANY_DELETE]\" value=\"1\" ";
    if(isset($acc->U_COMPANY_DELETE) && $acc->U_COMPANY_DELETE == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td><a class=\"a1\" onclick=\"checkRow('EmployeeForm','PRODUCT');\">";
    echo __("products");
    echo "</a></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[PRODUCT_SHOW]\" value=\"1\" ";
    if(isset($acc->U_PRODUCT_SHOW) && $acc->U_PRODUCT_SHOW == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[PRODUCT_EDIT]\" value=\"1\" ";
    if(isset($acc->U_PRODUCT_EDIT) && $acc->U_PRODUCT_EDIT == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[PRODUCT_DELETE]\" value=\"1\" ";
    if(isset($acc->U_PRODUCT_DELETE) && $acc->U_PRODUCT_DELETE == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td><a class=\"a1\" onclick=\"checkRow('EmployeeForm','LAYOUT');\">";
    echo __("menu.templates");
    echo "</a></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[LAYOUT_SHOW]\" value=\"1\" ";
    if(isset($acc->U_LAYOUT_SHOW) && $acc->U_LAYOUT_SHOW == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[LAYOUT_EDIT]\" value=\"1\" ";
    if(isset($acc->U_LAYOUT_EDIT) && $acc->U_LAYOUT_EDIT == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[LAYOUT_DELETE]\" value=\"1\" ";
    if(isset($acc->U_LAYOUT_DELETE) && $acc->U_LAYOUT_DELETE == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td><a class=\"a1\" onclick=\"checkRow('EmployeeForm','SERVICEMANAGEMENT');\">";
    echo __("menu.services");
    echo "</a></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[SERVICEMANAGEMENT_SHOW]\" value=\"1\" ";
    if(isset($acc->U_SERVICEMANAGEMENT_SHOW) && $acc->U_SERVICEMANAGEMENT_SHOW == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[SERVICEMANAGEMENT_EDIT]\" value=\"1\" ";
    if(isset($acc->U_SERVICEMANAGEMENT_EDIT) && $acc->U_SERVICEMANAGEMENT_EDIT == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[SERVICEMANAGEMENT_DELETE]\" value=\"1\" ";
    if(isset($acc->U_SERVICEMANAGEMENT_DELETE) && $acc->U_SERVICEMANAGEMENT_DELETE == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td><a class=\"a1\" onclick=\"checkRow('EmployeeForm','LOGFILE');\">";
    echo __("menu.loglines");
    echo "</a></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[LOGFILE_SHOW]\" value=\"1\" ";
    if(isset($acc->U_LOGFILE_SHOW) && $acc->U_LOGFILE_SHOW == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t\t<td>&nbsp;</td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[LOGFILE_DELETE]\" value=\"1\" ";
    if(isset($acc->U_LOGFILE_DELETE) && $acc->U_LOGFILE_DELETE == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td><a class=\"a1\" onclick=\"checkRow('EmployeeForm','EXPORT');\">";
    echo __("export");
    echo "</a></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[EXPORT_SHOW]\" value=\"1\" ";
    if(isset($acc->U_EXPORT_SHOW) && $acc->U_EXPORT_SHOW == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[EXPORT_EDIT]\" value=\"1\" ";
    if(isset($acc->U_EXPORT_EDIT) && $acc->U_EXPORT_EDIT == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[EXPORT_DELETE]\" value=\"1\" ";
    if(isset($acc->U_EXPORT_DELETE) && $acc->U_EXPORT_DELETE == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td colspan=\"4\">&nbsp;</td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td><strong>";
    echo __("settings");
    echo "</strong></td>\n\t\t\t\t\t\t\t\t<td class=\"smallfont\"><a onclick=\"checkColumn('EmployeeForm','SHOW');\" class=\"c1 a1\">";
    echo __("view");
    echo "</a></td>\n\t\t\t\t\t\t\t\t<td class=\"smallfont\"><a onclick=\"checkColumn('EmployeeForm','EDIT');\" class=\"c1 a1\">";
    echo __("edit");
    echo "</a></td>\n\t\t\t\t\t\t\t\t<td class=\"smallfont\"><a onclick=\"checkColumn('EmployeeForm','DELETE');\" class=\"c1 a1\">";
    echo __("remove");
    echo "</a></td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td><a class=\"a1\" onclick=\"checkRow('EmployeeForm','SETTINGS');\">";
    echo __("menu.software.settings");
    echo "</a></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[SETTINGS_SHOW]\" value=\"1\" ";
    if(isset($acc->U_SETTINGS_SHOW) && $acc->U_SETTINGS_SHOW == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[SETTINGS_EDIT]\" value=\"1\" ";
    if(isset($acc->U_SETTINGS_EDIT) && $acc->U_SETTINGS_EDIT == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[SETTINGS_DELETE]\" value=\"1\" ";
    if(isset($acc->U_SETTINGS_DELETE) && $acc->U_SETTINGS_DELETE == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td><a class=\"a1\" onclick=\"checkRow('EmployeeForm','CUSTOMERPANEL');\">";
    echo __("menu.customerpanel");
    echo "</a></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[CUSTOMERPANEL_SHOW]\" value=\"1\" ";
    if(isset($acc->U_CUSTOMERPANEL_SHOW) && $acc->U_CUSTOMERPANEL_SHOW == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[CUSTOMERPANEL_EDIT]\" value=\"1\" ";
    if(isset($acc->U_CUSTOMERPANEL_EDIT) && $acc->U_CUSTOMERPANEL_EDIT == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[CUSTOMERPANEL_DELETE]\" value=\"1\" ";
    if(isset($acc->U_CUSTOMERPANEL_DELETE) && $acc->U_CUSTOMERPANEL_DELETE == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td><a class=\"a1\" onclick=\"checkRow('EmployeeForm','ORDERFORM');\">";
    echo __("menu.orderform");
    echo "</a></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[ORDERFORM_SHOW]\" value=\"1\" ";
    if(isset($acc->U_ORDERFORM_SHOW) && $acc->U_ORDERFORM_SHOW == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[ORDERFORM_EDIT]\" value=\"1\" ";
    if(isset($acc->U_ORDERFORM_EDIT) && $acc->U_ORDERFORM_EDIT == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[ORDERFORM_DELETE]\" value=\"1\" ";
    if(isset($acc->U_ORDERFORM_DELETE) && $acc->U_ORDERFORM_DELETE == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td><a class=\"a1\" onclick=\"checkRow('EmployeeForm','PAYMENT');\">";
    echo __("menu.paymentoptions");
    echo "</a></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[PAYMENT_SHOW]\" value=\"1\" ";
    if(isset($acc->U_PAYMENT_SHOW) && $acc->U_PAYMENT_SHOW == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[PAYMENT_EDIT]\" value=\"1\" ";
    if(isset($acc->U_PAYMENT_EDIT) && $acc->U_PAYMENT_EDIT == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[PAYMENT_DELETE]\" value=\"1\" ";
    if(isset($acc->U_PAYMENT_DELETE) && $acc->U_PAYMENT_DELETE == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td><a class=\"a1\" onclick=\"checkRow('EmployeeForm','SERVICESETTING');\">";
    echo __("menu.settings for services");
    echo "</a></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[SERVICESETTING_SHOW]\" value=\"1\" ";
    if(isset($acc->U_SERVICESETTING_SHOW) && $acc->U_SERVICESETTING_SHOW == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[SERVICESETTING_EDIT]\" value=\"1\" ";
    if(isset($acc->U_SERVICESETTING_EDIT) && $acc->U_SERVICESETTING_EDIT == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"Rights[SERVICESETTING_DELETE]\" value=\"1\" ";
    if(isset($acc->U_SERVICESETTING_DELETE) && $acc->U_SERVICESETTING_DELETE == "1" || $pagetype == "add") {
        echo "checked=\"checked\"";
    }
    echo " /></td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t</table>\n\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n                    ";
}
echo "\t\t\t\t<!--right-->\n\t\t\t\t</div>\n\t\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t<!--split2-->\n\t\t\t</div>\n\t\t\t<!--split2-->\n\t\t\t\t\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\n\t\t";
if(0 < $acc->Identifier) {
    echo "\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-dashboard\">\n\t\t<!--content-->\n\t\t\n\t\t\t<!--split2-->\n\t\t\t<div class=\"split2\">\n\t\t\t<!--split2-->\n\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t<div class=\"left\">\n\t\t\t\t<!--left-->\n\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
    echo __("tables on dashboard");
    echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" id=\"dashboard_checkbox_table\">\n\t\t\t\t\t\t";
    foreach ($acc->Preferences["home"] as $key => $value) {
        echo "\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t<input id=\"table_";
        echo $key;
        echo "\" class=\"checkbox1\" type=\"checkbox\" name=\"Prefs[home][";
        echo $key;
        echo "][Value]\" data-pref-checkbox=\"";
        echo $key;
        echo "\" value=\"show\" ";
        if($value["Value"] == "show") {
            echo "checked=\"checked\"";
        }
        echo " />\n\t\t\t\t\t\t\t\t\t<label for=\"table_";
        echo $key;
        echo "\">";
        echo __($key);
        echo "</label>\n\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t</table>\n\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t</div>\n\t\t\t\t<!--left-->\n\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t<div class=\"right\">\n\t\t\t\t<!--right-->\n\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
    echo __("table order dashboard");
    echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t\t\t<input type=\"hidden\" id=\"employee\" value=\"";
    echo $acc->Identifier;
    echo "\" />\n\t\t\t\t\n\t\t\t\t\t\t<div id=\"dashboard_list\">\n\t\t\t\t\t\t";
    foreach ($acc->Preferences["home"] as $key => $value) {
        echo "\t\t\t\t\t\t\t<ul class=\"dashboard_order ";
        if($value["Value"] == "hidden") {
            echo "hide";
        }
        echo "\"  data-pref-order=\"";
        echo $key;
        echo "\" id=\"order_";
        echo str_replace("_", ".", $value["Action"]);
        echo "\">\n\t\t\t\t\t\t\t\t<li><a class=\"a1 c1 ico inline ico_sortable floatl\">&nbsp;</a> <strong class=\"title\">";
        echo __($key);
        echo "</strong></li>\n\t\t\t\t\t\t\t</ul>\n\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t</div>\n\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t</div>\n\t\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t<!--split2-->\n\t\t\t</div>\n\t\t\t<!--split2-->\n\t\t\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\t\t";
}
echo "\t\t\n\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-ticketsystem\">\n\t\t<!--content-->\n\t\t\n\t\t\t<strong>";
echo __("signature used for ticketsystem");
echo "</strong><br /><br />\n\t\t\n\t\t\t<textarea class=\"ckeditor\" cols=\"80\" id=\"Signature\" name=\"Signature\" rows=\"10\">";
echo isset($acc->Signature) ? $acc->Signature : "";
echo "</textarea>\n\t\t\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\n\t<!--box1-->\n\t</div>\n\t<!--box1-->\n\t\n\t<br />\n\t\t\t\n\t<p class=\"align_right\">\n\t\t<a class=\"button1 alt1\" id=\"form_create_btn\">\n            <span>";
echo $pagetype == "edit" ? __("btn edit") : __("btn add");
echo "</span>\n        </a>\n\t</p>\n\t\t\n<!--form-->\n</fieldset></form>\n<!--form-->\n\n";
require_once "views/footer.php";

?>