<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "views/header.php";
echo "\n<ul class=\"list1\">\n\t";
if(U_HOSTING_EDIT) {
    if($hosting->Status == 1 || $hosting->Status == 3 || $hosting->Status == 7) {
        echo "                <li>\n                    <a class=\"ico set1 create\" href=\"hosting.php?page=show&amp;id=";
        echo $hosting->Identifier;
        echo "&amp;action=create\">\n                        <div class=\"add\">&nbsp;</div>\n                        ";
        echo __("action create hosting");
        echo "                    </a>\n                </li>\n                ";
    }
    if($hosting->Status == 4) {
        echo "                <li>\n                    <a class=\"ico set1 cancel\" onclick=\"\$('#suspend_hosting').dialog('open');\">\n                        ";
        echo __("action suspend hosting");
        echo "                    </a>\n                </li>\n                ";
    }
    if($hosting->Status == 5) {
        echo "                <li>\n                    <a class=\"ico set1 accept\" href=\"hosting.php?page=show&amp;id=";
        echo $hosting->Identifier;
        echo "&amp;action=unsuspend\">\n                        ";
        echo __("action unsuspend hosting");
        echo "                    </a>\n                </li>\n                ";
    }
}
if($hosting->Status == 4) {
    echo "            <li>\n                <a class=\"ico set1 downloadhostingaccount large_actionname\" onclick=\"\$('#dialog_download_pdf').dialog('open');\">\n                    ";
    echo __("action download accountdata");
    echo "                </a>\n            </li>\n            <li>\n                <a class=\"ico set1 send large_actionname\" onclick=\"\$('#dialog_email_pdf').dialog('open');\">\n                    ";
    echo __("action mail accountdata");
    echo "                </a>\n            </li>\n            ";
}
echo "\n\t<li>\n        <a class=\"ico set1 registerdomain large_actionname\" href=\"services.php?page=add&amp;from=hosting&amp;from_id=";
echo $hosting->Identifier;
echo "&amp;extradomain=true\">\n            ";
echo __("action extra domain");
echo "        </a>\n    </li>\n\n\t";
if($hosting->Status == 4 && U_HOSTING_EDIT && 0 < $hosting->Product && isset($hosting->Periodic) && $hosting->Periodic->ProductCode && $hosting->Periodic->ProductCode != "") {
    echo "            <li>\n                <a class=\"ico set1 updowngradehosting large_actionname\" onclick=\"\$('#dialog_updowngrade_hosting').dialog('open');\">\n                    ";
    echo __("action updowngrade");
    echo "                </a>\n            </li>\n            ";
}
if(U_HOSTING_EDIT && in_array($hosting->Status, [4, 5]) && isset($server->VersionInfo["sso_support"]) && in_array($server->VersionInfo["sso_support"], ["post", "url"])) {
    echo "\t\t\t<li>\n\t\t\t\t<a class=\"ico set1 icon contactsync large_actionname no-disable\" target=\"_blank\" href=\"hosting.php?page=singlesignon&amp;id=";
    echo $hosting->Identifier;
    echo "\">\n\t\t\t\t\t";
    echo sprintf(__("login to control panel as"), $server->getControlPanelName($server->Panel));
    echo "\t\t\t\t</a>\n\t\t\t</li>\n\t\t\t";
}
echo "</ul>\n\n<div id=\"dialog_download_pdf\" title=\"";
echo __("dialog hosting download pdf");
echo "\" class=\"hide\">\n\t";
echo __("dialog hosting download pdf description");
echo "<br /><br />\n    <form method=\"post\" name=\"form_create_PDF\" action=\"hosting.php?page=show&amp;id=";
echo $hosting->Identifier;
echo "&amp;action=create_pdf\">\n    <select name=\"OtherTemplate\" class=\"text1 size4\">\n        ";
foreach ($template_list as $k => $tmp) {
    if(is_numeric($k)) {
        echo "<option value=\"";
        echo $k;
        echo "\" ";
        if($hosting->PdfTemplate == $k) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo $tmp["Name"];
        echo "</option>";
    }
}
echo "    </select><br />\n    <br />\n\n    ";
if(U_DEBTOR_EDIT && ($hosting->Status == 4 || $hosting->Status == 5) && $server->Panel) {
    echo "        <strong>";
    echo __("also reset password");
    echo "</strong><br />\n\n        <label>\n            <input type=\"checkbox\" name=\"also_change_password\" value=\"yes\" /> ";
    echo __("change hosting password confirm");
    echo "        </label>\n        <br /><br />\n\n        <div class=\"change_password_div hide\">\n            <strong>";
    echo __("password");
    echo "</strong><br />\n            <span class=\"title2_value lineheight2\">\n                <input type=\"text\" name=\"change_password\" value=\"";
    echo generatePassword();
    echo "\" class=\"text1 size10\"/>\n                <a class=\"a1 c1 smallfont marleft_1\" onclick=\"generatePassword(\$(this).prev());\">\n                    ";
    echo __("random password");
    echo "                </a>\n            </span>\n            ";
    echo __("change hosting password warning");
    echo "<br />\n            <br />\n        </div>\n        ";
}
echo "\n    <p><a class=\"button1 alt1 float_left button_has_loader\" onclick=\"\$('form[name=form_create_PDF]').submit();window.setTimeout('\$(\\'#dialog_download_pdf\\').dialog(\\'close\\')',2500);\"><span>";
echo __("dialog hosting download button");
echo "</span></a>\n        <span class=\"button_loader_span hide float_left\" style=\"line-height:32px\">\n            <img src=\"images/icon_circle_loader_green.gif\" alt=\"\" class=\"ico inline\" style=\"margin-bottom:-3px;\" />&nbsp;&nbsp;\n            <span class=\"loading_green\">";
echo __("loading");
echo "</span>\n        </span>\n    </p>\n    <p><a class=\"a1 c1 alt1 float_right\" onclick=\"\$('#dialog_download_pdf').dialog('close');\"><span>";
echo __("cancel");
echo "</span></a></p>\n    </form>\n</div>\n\n<div id=\"dialog_email_pdf\" title=\"";
echo __("dialog hosting email pdf");
echo "\" class=\"hide\">\n    ";
echo __("dialog hosting email pdf description");
echo "<br /><br />\n    <form method=\"post\" name=\"form_email_PDF\" action=\"hosting.php?page=show&amp;id=";
echo $hosting->Identifier;
echo "&amp;action=email_pdf\">\n    <select name=\"OtherTemplate\" class=\"text1 size4f\">\n        ";
foreach ($emailtemplate_list as $k => $tmp) {
    if(is_numeric($k)) {
        echo "<option value=\"";
        echo $k;
        echo "\" ";
        if($hosting->EmailTemplate == $k) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo $tmp["Name"];
        echo "</option>";
    }
}
echo "    </select><br />\n    <br />\n\n    <strong>";
echo __("send email to emailaddressess");
echo "</strong><br />\n    <input type=\"text\" name=\"EmailAddress\" value=\"";
echo check_email_address($debtor->EmailAddress, "convert", ", ");
echo "\" class=\"text1 size12\"/>\n    <br /><br />\n    ";
if(U_DEBTOR_EDIT && ($hosting->Status == 4 || $hosting->Status == 5) && $server->Panel) {
    echo "        <strong>";
    echo __("also reset password");
    echo "</strong><br />\n\n        <label>\n            <input type=\"checkbox\" name=\"also_change_password\" value=\"yes\" /> ";
    echo __("change hosting password confirm");
    echo "        </label>\n        <br /><br />\n\n        <div class=\"change_password_div hide\">\n            <strong>";
    echo __("password");
    echo "</strong><br />\n        <span class=\"title2_value lineheight2\">\n            <input type=\"text\" name=\"change_password\" value=\"";
    echo generatePassword();
    echo "\" class=\"text1 size10\"/>\n            <a class=\"a1 c1 smallfont marleft_1\" onclick=\"generatePassword(\$(this).prev());\">\n                ";
    echo __("random password");
    echo "            </a>\n        </span>\n            ";
    echo __("change hosting password warning");
    echo "<br />\n            <br />\n        </div>\n        ";
}
echo "\n    <p><a class=\"button1 alt1 float_left button_has_loader\" onclick=\"\$('form[name=form_email_PDF]').submit();\"><span>";
echo __("dialog hosting email button");
echo "</span></a>\n\n        <span class=\"button_loader_span hide float_left\" style=\"line-height:32px\">\n            <img src=\"images/icon_circle_loader_green.gif\" alt=\"\" class=\"ico inline\" style=\"margin-bottom:-3px;\" />&nbsp;&nbsp;\n            <span class=\"loading_green\">";
echo __("loading");
echo "</span>\n        </span>\n    </p>\n    <p><a class=\"a1 c1 alt1 float_right\" onclick=\"\$('#dialog_email_pdf').dialog('close');\"><span>";
echo __("cancel");
echo "</span></a></p>\n    </form>\n</div>\n\n<hr />\n\n<div class=\"mark alt3 hide\" id=\"hosting_account_error\">\n    <a class=\"close pointer\">";
echo __("close");
echo "</a>\n    <strong>\n        <em style=\"color:#c02e19\">";
echo __("messagetype error");
echo "</em>\n    </strong>\n    <br>\n    <ul>\n        <li></li>\n    </ul>\n</div>\n\n";
echo $message;
echo "\n<!--heading1-->\n<div class=\"heading1\">\n<!--heading1-->\n\n\t<h2>";
echo __("hosting account");
echo " ";
echo $hosting->Username;
echo "</h2> \n\n\t<p class=\"pos2\"><strong class=\"textsize1\">";
echo $array_hostingstatus[$hosting->Status];
echo "</span></strong></p>\n\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n<input type=\"hidden\" name=\"hosting_id\" value=\"";
echo $hosting->Identifier;
echo "\" />\n";
echo $is_service_terminated;
echo "<!--box1-->\n<div class=\"box2 tabs\">\n<!--box1-->\n\n\t<!--top-->\n\t<div class=\"top\">\n\t<!--top-->\n\t\n\t\t<ul class=\"list3\">\n\t\t\t<li class=\"on\"><a href=\"#tab-hosting\">";
echo __("general");
echo "</a></li>\n\t\t\t<li><a href=\"#tab-hosting-subscription\">";
echo isset($hosting->Periodic) ? show_subscription_tab_title($hosting->Periodic->TerminationDate, $hosting->Periodic->StartPeriod, $hosting->Periodic->AutoRenew) : show_subscription_tab_title("", "", "");
echo "</a></li>\n            ";
if(U_INVOICE_SHOW && isset($hosting->PeriodicID) && 0 < $hosting->PeriodicID) {
    echo "                    <li><a href=\"#tab-invoices\">";
    echo __("invoices") . " (<span id=\"page_total_placeholder_invoices\"></span>)";
    echo "</a></li>\n                    ";
}
echo "\t\t\t<li><a href=\"#tab-hosting-comment\">";
echo __("internal note");
echo " ";
if($hosting->Comment) {
    echo "<span class=\"ico actionblock info nm\">";
    echo __("more information");
    echo "</span>";
}
echo "</a></li>\n\t\t\t<li><a href=\"#tab-hosting-logfile\">";
echo __("logfile");
echo "</a></li>\n\t\t</ul>\n\t\n\t<!--top-->\n\t</div>\n\t<!--top-->\n\n\t<!--content-->\n\t<div class=\"content\" id=\"tab-hosting\">\n\t<!--content-->\n\n\t\t<!--split2-->\n\t\t<div class=\"split2\">\n\t\t<!--split2-->\n\t\t\n\t\t\t<!--left-->\n\t\t\t<div class=\"left\">\n\t\t\t<!--left-->\n\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
echo __("account data");
echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title2\">";
echo __("accountname");
echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
echo $hosting->Username;
echo "</span>\n                    \n                    <strong class=\"title2\">";
echo __("password");
echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">\n                        ";
$password_length = strlen(passcrypt($hosting->Password));
echo str_repeat("*", $password_length);
if(U_HOSTING_EDIT && ($hosting->Status == 4 || $hosting->Status == 5) && $server->Panel) {
    echo "                                <a id=\"hosting_account_password_change\" class=\"a1 c1 float_right hide\" onclick=\"\$('#HostingPasswordDialog').dialog('open');\">";
    echo __("edit password");
    echo "</a>\n                                ";
}
echo "                    </span>                                        \n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title2\">";
echo __("debtor");
echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
if(0 < $hosting->Debtor) {
    echo "<a href=\"debtors.php?page=show&amp;id=";
    echo $hosting->Debtor;
    echo "\" class=\"c1 a1\">";
    echo $debtor->CompanyName ? $debtor->CompanyName : $debtor->Initials . " " . $debtor->SurName;
    echo "</a>";
} else {
    echo __("new customer");
}
echo "</span>\n\t\t\t\t\t\n\t\t\t\t\t<br />\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title2\">";
echo __("server");
echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\"><a href=\"servers.php?page=show&amp;id=";
echo $hosting->Server;
echo "\" class=\"a1\">";
echo $server->Name;
echo "</a></span>\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title2\">";
echo __("package");
echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\"><a href=\"packages.php?page=show&amp;id=";
echo $hosting->Package;
echo "\" class=\"a1\">";
echo $hosting->PackageName;
echo "</a></span>\n\t\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t\n\t\t\t<!--left-->\n\t\t\t</div>\n\t\t\t<!--left-->\n\t\t\t\n\t\t\t<!--right-->\n\t\t\t<div class=\"right\">\n\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
echo __("account usage");
echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t<div class=\"hide\"><i>";
echo __("first select a hosting account");
echo "</i></div>\t\n\t\t\t\t\t\n\t\t\t\t\t<div>\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("discspace");
echo "</strong>\n\t\t\t\t\t\t<span id=\"hosting_discspace\" class=\"title2_value\"><img src=\"images/indicator.gif\" alt=\"\" /></span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("traffic");
echo "</strong>\n\t\t\t\t\t\t<span id=\"hosting_traffic\" class=\"title2_value\"><img src=\"images/indicator.gif\" alt=\"\" /></span>\t\t\t\t\t\t\n\t\t\t\t\t</div>\n\t\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\n\t\t\t<!--right-->\n\t\t\t</div>\n\t\t\t<!--right-->\n\t\t\t\n\t\t<!--split2-->\n\t\t</div>\n\t\t<!--split2-->\n\t\t\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\t\n\t<!--content-->\n\t<div class=\"content\" id=\"tab-hosting-subscription\">\n\t<!--content-->\n\t\t";
if(0 < $hosting->PeriodicID && is_object($hosting->Periodic)) {
    require_once "views/elements/subscription.tab.php";
    $options = ["product_id" => $hosting->Product];
    show_subscription_tab($hosting->Periodic, $options);
} else {
    echo "\t\t\n\t\t\t";
    echo __("this hosting account is not charged");
    echo "\t\t\n\t\t";
}
echo "\t\t\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n    \n    ";
if(U_INVOICE_SHOW && isset($hosting->PeriodicID) && 0 < $hosting->PeriodicID && isset($invoice_table_options)) {
    echo "            <div class=\"content\" id=\"tab-invoices\">\n            \n            \t<p>";
    echo __("here you can find an overview of invoices for this service subscription");
    echo "</p>\n            \n            \t";
    $invoice_table_options["hide_cols"] = ["Debtor", "subtr"];
    $invoice_table_options["parameters"]["searchat"] = "PeriodicID";
    $invoice_table_options["parameters"]["searchfor"] = $hosting->PeriodicID;
    $invoice_table_options["redirect_url"] = "hosting.php?page=show&id=" . $hosting->id . "#tab-invoices";
    generate_table("list_invoice_hosting", $invoice_table_options);
    echo "            </div>\n            ";
}
echo "    \n\n\t<!--content-->\n\t<div class=\"content\" id=\"tab-hosting-comment\">\n\t<!--content-->\n\t\t\n\t\t<form name=\"hosting_comment_form\" method=\"post\" action=\"hosting.php?page=show&amp;id=";
echo $hosting->Identifier;
echo "\">\n\t\t\n\t\t<!--box3-->\n\t\t<div class=\"box3\"><h3>";
echo __("internal note");
echo "</h3><div class=\"content lineheight2\">\n\t\t<!--box3-->\n\t\t\t\n\t\t\t<textarea name=\"Comment\" class=\"text1 size5 autogrow\">";
echo $hosting->Comment;
echo "</textarea>\n\t\t\t\n\t\t\t";
if(U_HOSTING_EDIT) {
    echo "\t\t\t<br clear=\"both\" /><br />\n\t\t\t<p><a class=\"button1 alt1 float_left\" onclick=\"\$('form[name=hosting_comment_form]').submit();\"><span>";
    echo __("save comment");
    echo "</span></a></p>\n\t\t\t<br clear=\"both\" />\n\t\t\t";
}
echo "\n\t\t<!--box3-->\n\t\t</div></div>\n\t\t<!--box3-->\n\t\t\n\t\t</form>\n\t\t\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\t\n\t<!--content-->\n\t<div class=\"content\" id=\"tab-hosting-logfile\">\n\t<!--content-->\n\t\t\n\t\t";
require_once "views/elements/log.table.php";
$options = ["form_action" => "hosting.php?page=show&amp;id=" . $hosting->Identifier, "session_name" => "hosting.show.logfile", "current_page" => $current_page, "current_page_url" => $current_page_url, "allow_delete" => U_HOSTING_DELETE];
show_log_table($list_hosting_logfile, $options);
echo "\t\t\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\n<!--box1-->\n</div>\n<!--box1-->\n\n\t<br />\n\t\t\t\n\t<!--buttonbar-->\n\t<div class=\"buttonbar\">\n\t<!--buttonbar-->\n\t\n\t\t";
if(U_HOSTING_EDIT && 0 < $hosting->Debtor) {
    echo "<p class=\"pos1\"><a class=\"button1 edit_icon\" href=\"services.php?page=edit&amp;hosting_id=";
    echo $hosting->Identifier;
    echo "\"><span>";
    echo __("edit");
    echo "</span></a></p>";
}
echo "\t\t";
if(U_HOSTING_DELETE) {
    echo "\t\t\t<p class=\"pos2\">\n\t\t\t\t<a class=\"a1 c1\" onclick=\"\$('#delete_hosting').dialog('open');\"><span>";
    echo __("delete");
    echo "</span></a>\n\t\t\t\t";
    if(!in_array($hosting->Status, ["-1", "9"])) {
        service_termination_function("hosting", $hosting->Identifier);
    }
    echo "\t\t\t</p>\n\t\t\t";
}
echo "\t\n\t<!--buttonbar-->\n\t</div>\n\t<!--buttonbar-->\n\t\n\t<br />\n\t\n\n<div id=\"domain_placeholder\">\n\t<center><img src=\"images/loadinfo.gif\" /></center>\n</div>\n\n";
if(U_DEBTOR_EDIT && ($hosting->Status == 4 || $hosting->Status == 5) && $server->Panel) {
    echo "        <div id=\"HostingPasswordDialog\" title=\"";
    echo sprintf(__("change hosting password dialog title"), $hosting->Username);
    echo "\" class=\"hide actiondialog\">\n        \t<form method=\"post\" name=\"HostingPasswordForm\" id=\"HostingPasswordForm\" action=\"hosting.php?page=show&action=changelogindetails&amp;id=";
    echo $hosting->Identifier;
    echo "\">\n        \t   \n                <strong class=\"lineheight2\">";
    echo __("login information");
    echo "</strong>\n                <br />\n               \n            \t<span class=\"title2 lineheight2\">";
    echo __("username");
    echo "</span>\n            \t<span class=\"title2 lineheight2\">";
    echo $hosting->Username;
    echo "</span>\n                <br class=\"clear\" />\n            \t\n            \t<span class=\"title2 lineheight2\">";
    echo __("password");
    echo "</span>\n            \t<span class=\"title2_value lineheight2\">\n                    <input id=\"form_Password\" type=\"text\" name=\"chg_Password\" value=\"";
    echo $hosting->Password;
    echo "\" class=\"text1 size10\"/> \n                    <a class=\"a1 c1 smallfont float_right\" style=\"display: inline-block; margin-top: 6px;\" onclick=\"generatePassword(\$('#form_Password'));\">\n                        ";
    echo __("random password");
    echo "                    </a>\n                </span>\n                <br />\n                \n                <strong>";
    echo __("send new login information email");
    echo "</strong><br />\n                <label>\n            \t   <input type=\"checkbox\" name=\"send_login_info_email\" value=\"yes\" onclick=\"\$('#new_login_info_email').toggle();\" /> \n                    ";
    echo __("send new login information email confirm");
    echo " (";
    echo check_email_address($debtor->EmailAddress, "convert", ", ");
    echo ")\n                </label>\n                <br />\n                \n                <div id=\"new_login_info_email\" class=\"hide\" style=\"padding-top: 3px;\">                \n                    <select name=\"OtherEmailTemplate\" class=\"text1 size4 marginL22\">\n                        ";
    foreach ($emailtemplate_list as $k => $tmp) {
        if(is_numeric($k)) {
            echo "<option value=\"";
            echo $k;
            echo "\" ";
            if($hosting->EmailTemplate == $k) {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo $tmp["Name"];
            echo "</option>";
        }
    }
    echo "                    </select>\n                    <br />\n                </div>\n                <br />\n            \t\n                <label>\n            \t   <input type=\"checkbox\" name=\"imsure\" value=\"yes\" /> \n                    ";
    echo __("change hosting password confirm");
    echo "                </label>\n                <br />\n                <span class=\"paddingL22 inlineblock\">";
    echo __("change hosting password warning");
    echo "</span>\n                <br /><br />\n            \t\n                <a id=\"change_hosting_password_btn\" class=\"button2 alt1 float_left\">\n                    <span>";
    echo __("change password");
    echo "</span>\n                </a>\n                \n                <span id=\"loader_saving\" class=\"float_left hide\" style=\"padding: 6px 0;\">\n        \t\t\t<img src=\"images/icon_circle_loader_green.gif\" alt=\"\" class=\"ico inline\" style=\"margin-bottom:-3px;\" />&nbsp;&nbsp;\n        \t\t\t<span class=\"loading_green\">";
    echo __("loading");
    echo "</span>&nbsp;&nbsp;\n        \t\t</span>\n                \n                <a class=\"a1 c1 alt1 float_right\" onclick=\"\$('#HostingPasswordDialog').dialog('close');\">\n                    <span>";
    echo __("cancel");
    echo "</span>\n                </a>\n          \n            \t<br class=\"clear\" />\n        \t</form>\n        </div>\n        ";
}
if($hosting->Status == 4 && U_HOSTING_EDIT && 0 < $hosting->Product && isset($hosting->Periodic) && $hosting->Periodic->ProductCode && $hosting->Periodic->ProductCode != "") {
    echo "        <div id=\"dialog_updowngrade_hosting\" title=\"";
    echo __("dialog hosting updowngrade");
    echo "\" class=\"hide\">\n            ";
    echo __("dialog hosting updowngrade description");
    echo "<br /><br />\n            <form method=\"post\" name=\"form_updowngrade_hosting\" action=\"hosting.php?page=show&amp;id=";
    echo $hosting->Identifier;
    echo "&amp;action=updowngrade_hosting\">\n\n                <input type=\"hidden\" name=\"start_period\" value=\"";
    echo rewrite_date_site2db($hosting->Periodic->StartPeriod);
    echo "\" />\n                <input type=\"hidden\" name=\"end_period\" value=\"";
    echo rewrite_date_site2db($hosting->Periodic->EndPeriod);
    echo "\" />\n                \n                <input type=\"hidden\" name=\"hosting_upgrade_prefix_upgrade\" value=\"";
    echo HOSTING_UPGRADE_PREFIX_UPGRADE;
    echo "\" />\n\t\t\t\t<input type=\"hidden\" name=\"hosting_upgrade_prefix_refund\" value=\"";
    echo HOSTING_UPGRADE_PREFIX_REFUND;
    echo "\" />\n                <input type=\"hidden\" name=\"hosting_upgrade_create_invoice\" value=\"";
    echo HOSTING_UPGRADE_CREATE_INVOICE;
    echo "\" />\n                \n                \n                <!-- CURRENT PRODUCT -->\n                <span class=\"inlineblock width3 lineheight2\">\n                    <strong>";
    echo __("current hostingproduct");
    echo ":</strong> \n                </span>\n                ";
    $product_price = VAT_CALC_METHOD == "incl" ? deformat_money($hosting->Periodic->PriceIncl) : deformat_money($hosting->Periodic->PriceExcl);
    if(!isEmptyFloat($hosting->Periodic->DiscountPercentage)) {
        $product_price = round(1 - $hosting->Periodic->DiscountPercentage, 8) * $product_price;
    }
    switch ($hosting->Periodic->Periodic) {
        case "t":
            $price_per_day = $product_price / 730;
            break;
        case "j":
            $price_per_day = $product_price / 365;
            break;
        case "h":
            $price_per_day = $product_price * 2 / 365;
            break;
        case "k":
            $price_per_day = $product_price * 4 / 365;
            break;
        case "m":
            $price_per_day = $product_price * 12 / 365;
            break;
        case "w":
            $price_per_day = $product_price / 7;
            break;
        case "d":
            $price_per_day = $product_price;
            break;
        default:
            $price_per_day = 0;
            echo "                <span id=\"current_product\" data-priceperday=\"";
            echo $price_per_day;
            echo "\" data-productkeyphrase=\"";
            echo stripReturnAndSubstring($hosting->Periodic->Description, 70);
            echo "\" data-periods=\"";
            echo $hosting->Periodic->Periods;
            echo "\" data-periodic=\"";
            echo $hosting->Periodic->Periodic;
            echo "\" data-endperiod=\"";
            echo $hosting->Periodic->StartPeriod;
            echo "\">\n                    ";
            echo $hosting->Periodic->ProductCode . " - " . $hosting->Periodic->ProductName;
            echo "                </span>\n                <span class=\"float_right lineheight2\">";
            echo money($product_price);
            echo "</span>\n                <br />\n                \n                <span class=\"inlineblock width3 lineheight2\"></span>\n                <span class=\"inlineblock c_mgray\">\n                    ";
            echo $hosting->PackageName ? "'" . $hosting->PackageName . "'" : "";
            echo " ";
            echo __("on server");
            echo "  \n                    '";
            echo $server->Name;
            echo "'\n                </span>\n                <span class=\"float_right c_mgray lineheight2\">";
            echo __("per") . " " . $array_periodic[$hosting->Periodic->Periodic];
            echo "</span>\n                <br /><br />\n\n                <!-- NEW PRODUCT -->\n                <label class=\"lineheight2\">\n                    <span class=\"inlineblock width3\">\n                        <strong>";
            echo __("new hostingproduct");
            echo ":</strong>\n                    </span>\n                </label>\n                <select name=\"new_product\" class=\"text1 size4f\" id=\"new_product\" style=\"margin-bottom: 5px;\">\n                    <option selected=\"selected\" value=\"\">";
            echo __("please choose");
            echo "</option>\n                    ";
            $upgradegroup_products = [];
            if(isset($upgradegroup->ProductsInfo) && $upgradegroup->ProductsInfo) {
                echo "                        <optgroup label=\"";
                echo sprintf(__("products same upgradegroup"), $upgradegroup->Name);
                echo "\">\n                        ";
                require_once "class/package.php";
                foreach ($upgradegroup->ProductsInfo as $product) {
                    if($product["id"] != $hosting->Product) {
                        $upgradegroup_products[] = $product["id"];
                        if(isset($packages_on_same_server[$product["id"]])) {
                            $product_package = new package();
                            $product_package->show($packages_on_same_server[$product["id"]]["id"]);
                            $product_server = $server;
                        } else {
                            $product_package = new package();
                            $product_package->show($product["PackageID"]);
                            $product_server = new server();
                            $product_server->show($product_package->Server);
                        }
                        $product_price = VAT_CALC_METHOD == "incl" ? $product["PriceIncl"] : $product["PriceExcl"];
                        switch ($product["PricePeriod"]) {
                            case "t":
                                $price_per_day = $product_price / 730;
                                break;
                            case "j":
                                $price_per_day = $product_price / 365;
                                break;
                            case "h":
                                $price_per_day = $product_price * 2 / 365;
                                break;
                            case "k":
                                $price_per_day = $product_price * 4 / 365;
                                break;
                            case "m":
                                $price_per_day = $product_price * 12 / 365;
                                break;
                            case "w":
                                $price_per_day = $product_price / 7;
                                break;
                            case "d":
                                $price_per_day = $product_price;
                                break;
                            default:
                                $price_per_day = 0;
                                echo "                                <option value=\"";
                                echo $product["id"];
                                echo "\"\n\t\t\t\t\t\t\t\t\t\tdata-packagenameold=\"";
                                echo $hosting->PackageName;
                                echo "\"\n\t\t\t\t\t\t\t\t\t\tdata-packagenamenew=\"";
                                echo $product_package->PackageName;
                                echo "\"\n\t\t\t\t\t\t\t\t\t\tdata-periodicdescription=\"";
                                echo stripReturnAndSubstring($hosting->Periodic->Description, 70);
                                echo "\"\n\t\t\t\t\t\t\t\t\t\tdata-domain=\"";
                                echo $hosting->Domain;
                                echo "\"\n\t\t\t\t\t\t\t\t\t\tdata-username=\"";
                                echo $hosting->Username;
                                echo "\"\n                                        data-servername=\"";
                                echo $product_server->Name;
                                echo "\"\n                                        data-price=\"";
                                echo money($product_price);
                                echo "\"\n                                        data-period=\"";
                                echo __("per") . " " . $array_periodic[$product["PricePeriod"]];
                                echo "\"\n                                        data-priceperday=\"";
                                echo $price_per_day;
                                echo "\"\n                                        data-productkeyphrase=\"";
                                echo htmlspecialchars(stripReturnAndSubstring($product["ProductKeyPhrase"], 70));
                                echo "\"\n\t\t\t\t\t\t\t\t\t\tdata-hascustomprice=\"";
                                echo $product["HasCustomPrice"];
                                echo "\">\n                                    ";
                                echo $product["ProductCode"] . " " . $product["ProductName"];
                                echo "                                </option>\n                                ";
                        }
                    }
                    unset($product_package);
                    unset($product_server);
                }
                echo "                        </optgroup>\n                        ";
            }
            foreach ($list_products as $key => $value) {
                if(in_array($value["id"], $upgradegroup_products) || $value["id"] == $hosting->Product) {
                    unset($list_products[$key]);
                }
            }
            unset($list_products["CountRows"]);
            if(!empty($list_products)) {
                echo "                        <optgroup label=\"";
                echo __("other products");
                echo "\">\n                        ";
                foreach ($list_products as $product) {
                    if($product["id"] != $hosting->Product && is_numeric($product["id"]) && !in_array($product["id"], $upgradegroup_products)) {
                        $product_package = new package();
                        $product_package->show($product["PackageID"]);
                        $product_server = new server();
                        $product_server->show($product_package->Server);
                        $product_price = VAT_CALC_METHOD == "incl" ? $product["PriceIncl"] : $product["PriceExcl"];
                        switch ($product["PricePeriod"]) {
                            case "t":
                                $price_per_day = $product_price / 730;
                                break;
                            case "j":
                                $price_per_day = $product_price / 365;
                                break;
                            case "h":
                                $price_per_day = $product_price * 2 / 365;
                                break;
                            case "k":
                                $price_per_day = $product_price * 4 / 365;
                                break;
                            case "m":
                                $price_per_day = $product_price * 12 / 365;
                                break;
                            case "w":
                                $price_per_day = $product_price / 7;
                                break;
                            case "d":
                                $price_per_day = $product_price;
                                break;
                            default:
                                $price_per_day = 0;
                                echo "                                <option value=\"";
                                echo $product["id"];
                                echo "\"\n\t\t\t\t\t\t\t\t\t\tdata-packagenameold=\"";
                                echo $hosting->PackageName;
                                echo "\"\n                                        data-packagenamenew=\"";
                                echo $product_package->PackageName;
                                echo "\"\n\t\t\t\t\t\t\t\t\t\tdata-periodicdescription=\"";
                                echo stripReturnAndSubstring($hosting->Periodic->Description, 70);
                                echo "\"\n\t\t\t\t\t\t\t\t\t\tdata-domain=\"";
                                echo $hosting->Domain;
                                echo "\"\n\t\t\t\t\t\t\t\t\t\tdata-username=\"";
                                echo $hosting->Username;
                                echo "\"\n                                        data-servername=\"";
                                echo $product_server->Name;
                                echo "\"\n                                        data-price=\"";
                                echo money($product_price);
                                echo "\"\n                                        data-period=\"";
                                echo __("per") . " " . $array_periodic[$product["PricePeriod"]];
                                echo "\"\n                                        data-priceperday=\"";
                                echo $price_per_day;
                                echo "\"\n                                        data-productkeyphrase=\"";
                                echo htmlspecialchars(stripReturnAndSubstring($product["ProductKeyPhrase"], 70));
                                echo "\"\n\t\t\t\t\t\t\t\t\t\tdata-hascustomprice=\"";
                                echo $product["HasCustomPrice"];
                                echo "\">\n                                    ";
                                echo $product["ProductCode"] . " " . $product["ProductName"];
                                echo "                                </option>\n                                ";
                        }
                    }
                    unset($product_package);
                    unset($product_server);
                }
                echo "                        </optgroup>\n                        ";
            }
            echo "                </select>\n                <span id=\"new_product_price_label\" class=\"float_right lineheight2\" style=\"margin-top: 6px;\"></span>\n                <br clear=\"both\"/>\n                \n                <!-- NEW PRODUCT PACKAGE INFO -->\n                <div id=\"new_product_server_package\" class=\"hide\">\n                    <span class=\"inlineblock width3 lineheight2\"></span>\n                    <div class=\"inlineblock c_mgray\">\n                         '<span class=\"packagename\"></span>' ";
            echo __("on server");
            echo " \n                         '<span class=\"servername\"></span>'\n                    </div>\n                    \n                    <span class=\"new_product_period_label float_right c_mgray\">";
            echo __("per") . " " . $array_periodic[$hosting->Periodic->Periodic];
            echo "</span>                   \n                </div>\n\n                \n                \n                <div id=\"updowngrade_new_invoice\" class=\"hide\">\n                <div id=\"tab-subscription\">                 \n                \t<div id=\"subscription_div_period\" style=\"margin-left:180px;\"></div>\n\t\t\t\t\t\n\t\t\t\t\t<br />    \n                \n                    <!-- NEW PERIOD -->\n                    <div id=\"new_product_period\" style=\"margin-bottom: 10px;\">\n        \t\t\t\t<strong class=\"inlineblock width3 lineheight2\">";
            echo __("billing frequency");
            echo ":</strong>\n        \t\t\t\t<input type=\"text\" class=\"text1 size3\" name=\"subscription[Periods]\" value=\"";
            echo $hosting->Periodic->Periods;
            echo "\"  />\n        \t\t\t\t<select name=\"subscription[Periodic]\" class=\"text1 size4\">\n        \t\t\t\t\t";
            foreach ($array_periodic as $key => $value) {
                if($key) {
                    switch ($key) {
                        case "t":
                            $number_of_days = 730;
                            break;
                        case "j":
                            $number_of_days = 365;
                            break;
                        case "h":
                            $number_of_days = 0;
                            break;
                        case "k":
                            $number_of_days = 0;
                            break;
                        case "m":
                            $number_of_days = 0;
                            break;
                        case "w":
                            $number_of_days = 7;
                            break;
                        case "d":
                            $number_of_days = 1;
                            break;
                        default:
                            $number_of_days = 0;
                            echo "                                        <option value=\"";
                            echo $key;
                            echo "\" \n\t\t\t\t\t\t\t\t\t\t\tdata-daysinperiod=\"";
                            echo $number_of_days;
                            echo "\" \n\t\t\t\t\t\t\t\t\t\t\t";
                            if($key == $hosting->Periodic->Periodic) {
                                echo "selected=\"selected\"";
                            }
                            echo ">\n                                            ";
                            echo $value;
                            echo "                                        </option>\n        \t\t\t\t\t           ";
                    }
                }
            }
            echo "        \t\t\t\t</select>\n        \t\t\t\t<br />\n                    </div>\n                        \n                    <!-- INVOICE CYCLE -->\n                    <strong class=\"inlineblock width3 lineheight2 float_left\">";
            echo __("financial process");
            echo ":</strong>\n                    \n                    <div class=\"inlineblock float_left\" style=\"width: 540px;\">\n                        <label>\n                            <input type=\"radio\" name=\"invoice_cycle\" value=\"existing_period\" ";
            if(HOSTING_UPGRADE_FINANCIAL_PROCESSING == "existing_period") {
                echo "checked=\"checked\"";
            }
            echo " />\n                            ";
            echo ucfirst(__("updowngrade leave period equal"));
            echo "                        </label><br />\n                        <label>\n                            <input type=\"radio\" name=\"invoice_cycle\" value=\"new_period\" ";
            if(HOSTING_UPGRADE_FINANCIAL_PROCESSING == "new_period") {
                echo "checked=\"checked\"";
            }
            echo " />\n                            ";
            echo ucfirst(__("updowngrade new period"));
            echo "                        </label><br />\n                    </div>\n                    <br class=\"clear\" />\n                    \n                    <!-- NEW INVOICE -->\n                    \n                    <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"table1 zebra_table existing_period\" style=\"width: 100%; margin: 10px 0 10px 0;\">\n                \t\t<thead>\n                \t\t\t<tr>\n                                <th>";
            echo __("description");
            echo "</th>\n                                <th width=\"200\">";
            echo __("period");
            echo "</th>\n                                <th colspan=\"2\" width=\"80\">\n                                    ";
            echo VAT_CALC_METHOD == "incl" ? __("price incl") : __("price excl");
            echo "                                </th>\n                \t\t\t</tr>\n                \t\t</thead>\n                \t\t<tbody>\n                            <tr>\n                \t\t\t\t<td valign=\"top\"  style=\"word-break: break-all;\">\n                                    <span class=\"product_name\"></span>\n                                </td>\n                                \n                                <td valign=\"top\">\n                                    ";
            echo rewrite_date_db2site(date("Y-m-d")) . " " . __("till") . " " . $hosting->Periodic->StartPeriod;
            echo "                                </td>\n                                \n                                <td valign=\"top\" style=\"width: 5px;\" class=\"currency_sign_left\">\n                                    ";
            echo currency_sign_td(CURRENCY_SIGN_LEFT);
            echo "                                </td>\n                \t\t\t\t<td valign=\"top\" style=\"width: 75px; padding-left: 0px;\" align=\"right\">\n                                    <span class=\"price\"></span>";
            if(CURRENCY_SIGN_RIGHT) {
                echo " " . CURRENCY_SIGN_RIGHT;
            }
            echo "                                </td>\n                            </tr>\n                        </tbody>\n                    </table>\n                    \n                    <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"table1 zebra_table new_period\" style=\"width: 100%; margin: 10px 0 0px 0;\">\n                \t\t<thead>\n                \t\t\t<tr>\n                                <th>";
            echo __("description");
            echo "</th>\n                                <th width=\"200\">";
            echo __("period");
            echo "</th>\n                                <th colspan=\"2\" width=\"80\">\n                                    ";
            echo VAT_CALC_METHOD == "incl" ? __("price incl") : __("price excl");
            echo "                                </th>\n                \t\t\t</tr>\n                \t\t</thead>\n                \t\t<tbody>\n                            <tr>\n                \t\t\t\t<td valign=\"top\"  style=\"word-break: break-all;\">\n                                    <span class=\"product_name\"></span>\n                                </td>\n                                \n                                <td valign=\"top\">\n                                    ";
            echo rewrite_date_db2site(date("Y-m-d")) . " " . __("till") . " <span class=\"new_date\"></span>";
            echo "                                </td>\n                                \n                                <td valign=\"top\" style=\"width: 5px;\" class=\"currency_sign_left\">\n                                    ";
            echo currency_sign_td(CURRENCY_SIGN_LEFT);
            echo "                                </td>\n                \t\t\t\t<td valign=\"top\" style=\"width: 75px; padding-left: 0px;\" align=\"right\">\n                                    <span class=\"price\"></span>";
            if(CURRENCY_SIGN_RIGHT) {
                echo " " . CURRENCY_SIGN_RIGHT;
            }
            echo "                                </td>\n                            </tr>\n                            \n                            <tr class=\"tr1\">\n                \t\t\t\t<td valign=\"top\"  style=\"word-break: break-all;\">\n\t\t\t\t\t\t\t\t\t<span class=\"refund_text\"></span>\n                                </td>\n                                \n                                <td valign=\"top\">\n                                    ";
            echo rewrite_date_db2site(date("Y-m-d")) . " " . __("till") . " " . $hosting->Periodic->StartPeriod;
            echo "                                </td>\n                                \n                                <td valign=\"top\" style=\"width: 5px;\" class=\"currency_sign_left\">\n                                    ";
            echo currency_sign_td(CURRENCY_SIGN_LEFT);
            echo "                                </td>\n                \t\t\t\t<td valign=\"top\" style=\"width: 75px; padding-left: 0px;\" align=\"right\">\n                                    <span class=\"refund_price\"></span>";
            if(CURRENCY_SIGN_RIGHT) {
                echo " " . CURRENCY_SIGN_RIGHT;
            }
            echo "                                </td>\n                            </tr>\n                        </tbody>\n                    </table>\n                    \n                    <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"table1 new_period_sum\" style=\"border: 0px none; width: 100%; margin: 0 0 10px 0;\">\n                        <tr>\n                            <td>&nbsp;</td>\n                            <td width=\"200\" align=\"right\">";
            echo __("total");
            echo "</td>\n                            <td valign=\"top\" style=\"width: 5px;\" class=\"currency_sign_left\">\n                                ";
            echo currency_sign_td(CURRENCY_SIGN_LEFT);
            echo "                            </td>\n            \t\t\t\t<td valign=\"top\" style=\"width: 75px; padding-left: 0px;\" align=\"right\">\n                                <span class=\"sum_price_new_period\"></span>";
            if(CURRENCY_SIGN_RIGHT) {
                echo " " . CURRENCY_SIGN_RIGHT;
            }
            echo "                            </td>\n                        </tr>\n                    </table>                    \n                \n                    <label style=\"margin-top: 5px;\">\n                        <input type=\"checkbox\" name=\"create_invoice\" value=\"yes\" ";
            if(HOSTING_UPGRADE_CREATE_INVOICE == "always") {
                echo "checked=\"checked\"";
            }
            echo " /> \n                        ";
            echo __("create an invoice");
            echo "                    </label>\n                    \n                    <br />\n                    <br />\n                    <label>\n                        <input type=\"checkbox\" name=\"imsure\" value=\"yes\" /> \n                        ";
            echo __("are you sure updowngrade hosting");
            echo "                    </label>\n                </div>\n                </div>\n\n                <br />\n                <p>\n                    <span class=\"loader_saving float_left hide\" style=\"padding: 6px 0;\">\n            \t\t\t<img src=\"images/icon_circle_loader_green.gif\" alt=\"\" class=\"ico inline\" style=\"margin-bottom:-3px;\" />&nbsp;&nbsp;\n            \t\t\t<span class=\"loading_green\">";
            echo __("loading");
            echo "</span>&nbsp;&nbsp;\n            \t\t</span>\n                    <a id=\"updowngrade_hosting_submit\" class=\"button2 alt1 float_left\">\n                        <span>";
            echo __("proceed");
            echo "</span>\n                    </a>\n                </p>\n                <p>\n                    <a class=\"a1 c1 alt1 float_right\" onclick=\"\$('#dialog_updowngrade_hosting').dialog('close');\">\n                        <span>";
            echo __("cancel");
            echo "</span>\n                    </a>\n                </p>\n            </form>\n        </div>\n        ";
    }
}
echo "\n";
if(U_HOSTING_DELETE) {
    echo "<div id=\"delete_hosting\" class=\"hide ";
    if(isset($pagetype) && $pagetype == "confirmDelete") {
        echo "autoopen";
    }
    echo "\" title=\"";
    echo __("deletedialog hosting title");
    echo "\">\n\t<form id=\"HostingForm\" name=\"form_delete\" method=\"post\" action=\"hosting.php?page=delete\">\n\t<input type=\"hidden\" name=\"id\" value=\"";
    echo $hosting->Identifier;
    echo "\"/>\n\t";
    echo sprintf(__("deletedialog hosting description"), $hosting->Username);
    echo "<br /><br />\n    \n    ";
    if(isset($hosting->Periodic->Identifier) && 0 < $hosting->Periodic->Identifier) {
        echo "    <input type=\"radio\" name=\"confirmPeriodic\" value=\"remove\" checked=\"checked\" class=\"hide\"/>\n\t";
    }
    echo "\t\n\t";
    if($hosting->Status == 4 || $hosting->Status == 5) {
        echo "\t\t<strong>";
        echo __("hostingaccount on server");
        echo ":</strong><br />\n\t\t<input type=\"radio\" id=\"confirmserver_remove\" name=\"confirmServer\" value=\"remove\" ";
        if(isset($_SESSION["ActionLog"]["Hosting"]["forAll"]["confirmServer"]) && $_SESSION["ActionLog"]["Hosting"]["forAll"]["confirmServer"] == "remove" || !isset($_SESSION["ActionLog"]["Hosting"]["forAll"]["confirmServer"]) && (!isset($_GET["confirmServer"]) || $_GET["confirmServer"] == "remove")) {
            echo "checked=\"checked\" ";
        }
        echo "/> <label for=\"confirmserver_remove\">";
        echo __("delete hostingaccount");
        echo "</label><br />\n\t\t<input type=\"radio\" id=\"confirmserver_suspend\" name=\"confirmServer\" value=\"suspend\" ";
        if(isset($_SESSION["ActionLog"]["Hosting"]["forAll"]["confirmServer"]) && $_SESSION["ActionLog"]["Hosting"]["forAll"]["confirmServer"] == "suspend" || !isset($_SESSION["ActionLog"]["Hosting"]["forAll"]["confirmServer"]) && isset($_GET["confirmServer"]) && $_GET["confirmServer"] == "suspend") {
            echo "checked=\"checked\" ";
        }
        echo "/> <label for=\"confirmserver_suspend\">";
        echo __("block hostingaccount");
        echo "</label><br />\n\t\t<input type=\"radio\" id=\"confirmserver_none\" name=\"confirmServer\" value=\"none\"  ";
        if(isset($_SESSION["ActionLog"]["Hosting"]["forAll"]["confirmServer"]) && $_SESSION["ActionLog"]["Hosting"]["forAll"]["confirmServer"] == "none" || !isset($_SESSION["ActionLog"]["Hosting"]["forAll"]["confirmServer"]) && isset($_GET["confirmServer"]) && $_GET["confirmServer"] == "none") {
            echo "checked=\"checked\" ";
        }
        echo "/> <label for=\"confirmserver_none\">";
        echo __("do nothing on server");
        echo "</label><br />\n\t\t<br />\n\t";
    }
    echo "\t\t\n\t<input type=\"checkbox\" id=\"imsure_delete\" name=\"imsure\" value=\"yes\" /> <label for=\"imsure_delete\">";
    echo __("delete this hosting");
    echo "</label><br />\n\t<br />\n    \n    <i>";
    echo __("hosting delete domains only disconnect");
    echo "</i><br /><br />\n        \n\t<p><a id=\"delete_hosting_btn\" class=\"button2 alt1 float_left\"><span>";
    echo __("delete");
    echo "</span></a></p>\n\t<p><a class=\"a1 c1 alt1 float_right\" onclick=\"\$('#delete_hosting').dialog('close');\"><span>";
    echo __("cancel");
    echo "</span></a></p>\n\t\n\t";
    if(isset($_SESSION["ActionLog"]) && isset($_SESSION["ActionLog"]["Hosting"]["delete"]) && is_array($_SESSION["ActionLog"]["Hosting"]["delete"]) && 1 < count($_SESSION["ActionLog"]["Hosting"]["delete"])) {
        echo "\t\t\t<br class=\"clear\"/><br />\n\t\t\t<strong>";
        echo sprintf(__("batch remove all other hosting"), count($_SESSION["ActionLog"]["Hosting"]["delete"]) - 1);
        echo "</strong><br />\n\t\t\t<label style=\"display:block;margin: 2px 0 5px;\"><input type=\"checkbox\" id=\"forAll\" name=\"forAll\" value=\"yes\" ";
        if(isset($_SESSION["ActionLog"]["Hosting"]["forAll"]["check"]) && $_SESSION["ActionLog"]["Hosting"]["forAll"]["check"] === true) {
            echo "checked=\"checked\" ";
        }
        echo " /> ";
        echo __("batch after this hostingaccount directly remove the others");
        echo "</label>\n\t";
    } elseif(isset($_SESSION["ActionLog"]) && isset($_SESSION["ActionLog"]["Subscriptions"]["type"]["hosting"]["delete"]) && is_array($_SESSION["ActionLog"]["Subscriptions"]["type"]["hosting"]["delete"]) && 1 < count($_SESSION["ActionLog"]["Subscriptions"]["type"]["hosting"]["delete"])) {
        echo "\t\t<br class=\"clear\"/><br />\n\t\t<strong>";
        echo sprintf(__("batch remove all other hosting"), count($_SESSION["ActionLog"]["Subscriptions"]["type"]["hosting"]["delete"]) - 1);
        echo "</strong><br />\n\t\t<label style=\"display:block;margin: 2px 0 5px;\"><input type=\"checkbox\" id=\"forAll\" name=\"forAll\" value=\"yes\" ";
        if(isset($_SESSION["ActionLog"]["Subscriptions"]["type"]["hosting"]["forAll"]["check"]) && $_SESSION["ActionLog"]["Subscriptions"]["type"]["hosting"]["forAll"]["check"] === true) {
            echo "checked=\"checked\" ";
        }
        echo " /> ";
        echo __("batch after this hostingaccount directly remove the others");
        echo "</label>\n\t";
    }
    echo "\t\n\t\n\t</form>\n</div>\n";
}
echo "\n<div id=\"suspend_hosting\" class=\"hide\" title=\"";
echo __("suspenddialog hosting title");
echo "\">\n\t<form id=\"HostingForm\" name=\"form_suspend\" method=\"post\" action=\"hosting.php?page=suspend\">\n\t<input type=\"hidden\" name=\"id\" value=\"";
echo $hosting->Identifier;
echo "\"/>\n\t";
echo sprintf(__("suspenddialog hosting description"), $hosting->Username);
echo "<br /><br />\n    \n\t";
echo __("the following actions will be executed");
echo ":<br />\n\t- ";
echo __("the account will be blocked in software");
echo "<br />\n\t- ";
echo __("the account will be blocked on the server");
echo "<br />\n    <br />\n    \n\t<input type=\"checkbox\" id=\"imsure_suspend\" name=\"imsure\" value=\"yes\"/> <label for=\"imsure_suspend\">";
echo __("suspend this hosting");
echo "</label><br />\n\t<br />\n\t<p><a id=\"suspend_hosting_btn\" class=\"button2 alt1 float_left\"><span>";
echo __("suspend");
echo "</span></a></p>\n\t<p><a class=\"a1 c1 alt1 float_right\" onclick=\"\$('#suspend_hosting').dialog('close');\"><span>";
echo __("cancel");
echo "</span></a></p>\n\t</form>\n</div>\n\n";
if(isset($_GET["action"]) && $_GET["action"] == "startcreate" && ($hosting->Status == 1 || $hosting->Status == 3 || $hosting->Status == 7)) {
    echo "\t<script language=\"javascript\" type=\"text/javascript\">\n\t\$(function(){\n\t\t\$('#dialog').dialog('open');\n\t\t\$.post(\"XMLRequest.php\", { action: \"create_hosting\", id: ";
    echo $hosting->Identifier;
    echo "},\n\t\tfunction(data){\n\t\t\t\n\t\t\t";
    if(isset($_SESSION["redirect_after_create"])) {
        echo "\t\t\tif(data == 'OK'){\n\t\t\t\tlocation.href = '";
        echo $_SESSION["redirect_after_create"];
        echo "';\n\t\t\t\treturn true;\n\t\t\t}\n\t\t\t\n\t\t\t";
        unset($_SESSION["redirect_after_create"]);
    }
    echo "\t\t\t\n\t\t\tlocation.href = '?page=show&id=";
    echo $hosting->Identifier;
    echo "';\n\t\t\t\t\n\t\t}, \"html\");\n\t});\n\t</script>";
}
echo "\n";
if(isset($selected_tab) && $selected_tab) {
    echo "<script language=\"javascript\" type=\"text/javascript\">\n\$(function()\n{\n\t\$('.tabs').tabs(\"option\", \"active\", ";
    echo $selected_tab;
    echo ");\n});\n</script>\n";
}
echo "\n";
if($hosting->Status == 4 && U_HOSTING_EDIT && 0 < $hosting->Product) {
    echo "    \n    <script language=\"javascript\" type=\"text/javascript\">\n    \$(function()\n    {\n        \$(document).on('change', '#dialog_updowngrade_hosting input[name=\"imsure\"]', function()\n        {\n            if(\$(this).is(':checked'))\n            {\n                \$('#updowngrade_hosting_submit').removeClass('button2');\n                \$('#updowngrade_hosting_submit').addClass('button1');\n            }\n            else\n            {\n                \$('#updowngrade_hosting_submit').removeClass('button1');\n                \$('#updowngrade_hosting_submit').addClass('button2');\n            }  \n        });\n        \n        \$('#updowngrade_hosting_submit').click( function()\n        {\n            if(\$(this).hasClass('button1'))\n            {\n                \$(this).hide();\n                \$(this).siblings('.loader_saving').show();\n                \$('form[name=form_updowngrade_hosting]').submit();\n            }     \n        });\n\n        \$(document).on('change', 'select#new_product', function()\n        {\n            // show package/server/price/period info of the newly choosen product\n            if(\$(this).val() != '')\n            {\n                \$('#new_product_server_package span.packagename').html(\$(this).find('option:selected').data('packagenamenew'));\n                \$('#new_product_server_package span.servername').html(\$(this).find('option:selected').data('servername'));\n                \$('#new_product_price_label').html(\$(this).find('option:selected').data('price'));\n                \$('#new_product_server_package span.new_product_period_label').html(\$(this).find('option:selected').data('period'));\n                \$('#new_product_server_package').show();\n               \n                if(\$(this).find('option:selected').data('hascustomprice') == 'period')\n                {\n                \tshowCustomPeriodPrice('subscription', \$('select#new_product'));\n                \tsetTimeout('createNewInvoiceData()', 500);\n                \treturn;\n                }\n                else\n                {\n                \t\$('#subscription_div_period').html('');\n                }\n            }\n            else\n            {\n                \$('#new_product_price_label').html();\n                \$('#new_product_server_package span.new_product_period_label').html();\n                \$('#new_product_server_package').hide();\n            }\n            \n            createNewInvoiceData();\n        });\n\n        \$(document).on('change', 'input[name=\"invoice_cycle\"]', function()\n        {\n            createNewInvoiceData();\n        });\n\n        \$(document).on('keyup', 'input[name=\"subscription[Periods]\"]', function()\n        {\n            createNewInvoiceData();\n        });\n\n        \$(document).on('change', 'select[name=\"subscription[Periodic]\"]', function()\n        {\n            createNewInvoiceData();\n        });\n    });\n    \n    function createNewInvoiceData()\n    {\n    \t\n        // check if needed values are set\n        if(\$('select#new_product').val() == '')\n        {\n            \$('#updowngrade_new_invoice').hide();\n            return false;\n        }\n        \n        // prefix texts\n        var prefix_upgrade_text = \$('input[name=\"hosting_upgrade_prefix_upgrade\"]').val();\n\t\tvar prefix_refund_text = \$('input[name=\"hosting_upgrade_prefix_refund\"]').val();\n\n        // name of the new (selected) product\n        var new_product_name = \$('select#new_product').find('option:selected').data('productkeyphrase');\n        \n        // price per day of the current product\n        var current_product_ppd = \$('#current_product').data('priceperday');\n        \n        // price per day of the new product\n        var new_product_ppd = \$('select#new_product').find('option:selected').data('priceperday');\n        \n        // If product has custom prices, we should change the new_product_ppd number\n        if(\$('select#new_product').find('option:selected').data('hascustomprice') == 'period')\n        {\n        \tvar Temp = \$.parseJSON(CustomPriceObject[\$('select#new_product').val()]);\n\n\t\t\tif(VAT_CALC_METHOD == 'incl')\n\t\t\t{\n\t\t\t\tif(Temp.period[\$('input[name=\"subscription[Periods]\"]').val() + '-' + \$('select[name=\"subscription[Periodic]\"]').val()] != undefined)\n\t\t\t\t{\n\t\t\t\t\tnew_product_ppd = Temp.period[\$('input[name=\"subscription[Periods]\"]').val() + '-' + \$('select[name=\"subscription[Periodic]\"]').val()]['PriceIncl'];\n\t\t\t\t\tnew_product_ppd = new_product_ppd / \$('select[name=\"subscription[Periodic]\"] option:selected').data('daysinperiod');\t\n\t\t\t\t}\n\t\t\t}\n\t\t\telse\n\t\t\t{\n\t\t\t\tif(Temp.period[\$('input[name=\"subscription[Periods]\"]').val() + '-' + \$('select[name=\"subscription[Periodic]\"]').val()] != undefined)\n\t\t\t\t{\n\t\t\t\t\tnew_product_ppd = Temp.period[\$('input[name=\"subscription[Periods]\"]').val() + '-' + \$('select[name=\"subscription[Periodic]\"]').val()]['PriceExcl'];\n\t\t\t\t\tnew_product_ppd = new_product_ppd / \$('select[name=\"subscription[Periodic]\"] option:selected').data('daysinperiod');\n\t\t\t\t}\n\t\t\t}\n        }\n        \n        // get today as date string\n    \tvar start_date = new Date();\n        // add zero's before single numbers, getDaysFromPeriod won't work on firefox with 7-1-2014\n        var currentMonth = start_date.getMonth() + 1;\n        currentMonth = (currentMonth < 10) ? '0' + currentMonth : currentMonth;\n        var currentDay = (start_date.getDate() < 10) ? '0' + start_date.getDate() : start_date.getDate();\n        var today = start_date.getFullYear() + \"-\" + currentMonth + \"-\" + currentDay;\n        \n        // get end date of current periodic as string\n        var end_period_db = \$('input[name=\"end_period\"]').val();\n        end_period = end_period_db.substr(0,4) + '-' + end_period_db.substr(4,2) + '-' + end_period_db.substr(6,2);\n        \n        // get start date of current periodic as string\n        var start_period_db = \$('input[name=\"start_period\"]').val();\n        start_period = start_period_db.substr(0,4) + '-' + start_period_db.substr(4,2) + '-' + start_period_db.substr(6,2);\n        \n\t\t// If we up/downgraden on the same day as subscription renewal....fix remaining days calculation\n        var current_dates = changePeriodCalc(\$('#current_product').data('periodic'), -1 * \$('#current_product').data('periods'), \$('#current_product').data('endperiod'));\n        current_dates = rewrite_date_site2db(current_dates[1]);\n        current_dates = current_dates.substr(0,4) + '-' + current_dates.substr(4,2) + '-' + current_dates.substr(6,2);\n        \n        if(today == current_dates)\n        {\n       \t\tvar days_remaining_period = \$('select[name=\"subscription[Periodic]\"] option[value=\"'+\$('#current_product').data('periodic')+'\"]').data('daysinperiod') * \$('#current_product').data('periods');\n   \t\t}\n   \t\telse\n   \t\t{\n   \t\t\t// get amount of days from the remaining period (-1 day, because the start_period is until, and not including), max prevents days below zero\n        \tvar days_remaining_period = Math.max(0, getDaysFromPeriod(today, start_period) -1);\n   \t\t}\n\n        // calculate based on new period        \n        if(\$('input[name=\"invoice_cycle\"]:checked').val() == 'existing_period')\n        {\n            var upgrade_diff_price = (new_product_ppd * days_remaining_period) - (current_product_ppd * days_remaining_period);\n            \n            \$('#updowngrade_new_invoice').show();\n            \$('#updowngrade_new_invoice .new_period').hide();\n            \$('#updowngrade_new_invoice .new_period_sum').hide();\n            \$('#updowngrade_new_invoice .existing_period').show();\n            \$('#updowngrade_new_invoice .existing_period span.price').html(formatAsMoney(upgrade_diff_price, 2));\n            \$('#updowngrade_new_invoice .existing_period span.product_name').html(prefix_upgrade_text.replace(/\\[hosting\\-\\>NewPackageName\\]/gi, \$('select#new_product').find('option:selected').data('packagenamenew')).replace(/\\[hosting\\-\\>OldPackageName\\]/gi, \$('select#new_product').find('option:selected').data('packagenameold')).replace(/\\[hosting\\-\\>Domain\\]/gi, \$('select#new_product').find('option:selected').data('domain')).replace(/\\[hosting\\-\\>Username\\]/gi, \$('select#new_product').find('option:selected').data('username')).replace(/\\[periodic\\-\\>Description\\]/gi, \$('select#new_product').find('option:selected').data('periodicdescription')));\n            \n            // switch create invoice checkbox based on setting and end amount invoice\n            if(\$('input[name=\"hosting_upgrade_create_invoice\"]').val() == 'only_positive')\n            {\n                if(upgrade_diff_price > 0)\n                {\n                    \$('input[name=\"create_invoice\"]').prop('checked', true);\n                }\n                else\n                {\n                    \$('input[name=\"create_invoice\"]').prop('checked', false);\n                }\n            }\n        }\n        // calculate based on existing period\n        else if(\$('input[name=\"invoice_cycle\"]:checked').val() == 'new_period')\n        {\n            var current_product_refund = Math.abs(current_product_ppd * days_remaining_period) * -1;\n            \n            // get price of the new period\n            var new_dates = changePeriodCalc(\$('select[name=\"subscription[Periodic]\"]').val(), \$('input[name=\"subscription[Periods]\"]').val());\n            var new_period_end_date = rewrite_date_site2db(new_dates[1]);\n            new_period_end_date = new_period_end_date.substr(0,4) + '-' + new_period_end_date.substr(4,2) + '-' + new_period_end_date.substr(6,2);\n            \n\t\t\tvar days_new_period = \$('select[name=\"subscription[Periodic]\"] option:selected').data('daysinperiod');\n\n            var new_period_price = new_product_ppd * days_new_period * \$('input[name=\"subscription[Periods]\"]').val();\n            var new_period_sum = new_period_price + current_product_refund;\n\n            \$('#updowngrade_new_invoice').show();\n            \$('#updowngrade_new_invoice .existing_period').hide();\n            \$('#updowngrade_new_invoice .new_period').show();\n            \$('#updowngrade_new_invoice .new_period_sum').show();\n            \$('#updowngrade_new_invoice .new_period_sum span.sum_price_new_period').html(formatAsMoney(new_period_sum, 2));\n            \$('#updowngrade_new_invoice .new_period span.price').html(formatAsMoney(new_period_price, 2));\n            \$('#updowngrade_new_invoice .new_period span.refund_price').html(formatAsMoney(current_product_refund, 2));\n            \$('#updowngrade_new_invoice .new_period span.new_date').html(new_dates[1]);\n            \$('#updowngrade_new_invoice .new_period span.product_name').html(new_product_name);\n\t\t\t\$('#updowngrade_new_invoice .new_period span.refund_text').html(prefix_refund_text.replace(/\\[hosting\\-\\>NewPackageName\\]/gi, \$('select#new_product').find('option:selected').data('packagenamenew')).replace(/\\[hosting\\-\\>OldPackageName\\]/gi, \$('select#new_product').find('option:selected').data('packagenameold')).replace(/\\[hosting\\-\\>Domain\\]/gi, \$('select#new_product').find('option:selected').data('domain')).replace(/\\[hosting\\-\\>Username\\]/gi, \$('select#new_product').find('option:selected').data('username')).replace(/\\[periodic\\-\\>Description\\]/gi, \$('select#new_product').find('option:selected').data('periodicdescription')));\n\n            // switch create invoice checkbox based on setting and end amount invoice\n            if(\$('input[name=\"hosting_upgrade_create_invoice\"]').val() == 'only_positive')\n            {\n                if(new_period_sum > 0)\n                {\n                    \$('input[name=\"create_invoice\"]').prop('checked', true);\n                }\n                else\n                {\n                    \$('input[name=\"create_invoice\"]').prop('checked', false);\n                }\n            }\n        }\n    }\n    </script>\n    \n    ";
}
require_once "views/footer.php";

?>