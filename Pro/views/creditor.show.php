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
if($creditor->Status < 9 && U_CREDITOR_INVOICE_EDIT) {
    echo "<ul class=\"list1\">\n\t<li><a class=\"ico set1 purchase large_actionname\" href=\"creditors.php?page=add_invoice&amp;creditor=";
    echo $creditor->Identifier;
    echo "\">";
    echo __("create new creditinvoice");
    echo "</a></li>\n</ul>\n\t\t\n<hr />\n";
}
echo "\n";
echo $message;
echo "\n<!--heading1-->\n<div class=\"heading1\">\n<!--heading1-->\n\n\t<h2>";
echo __("creditor");
echo " ";
echo $creditor->CompanyName ? $creditor->CompanyName : $creditor->Initials . " " . $creditor->SurName;
echo "</h2>\n\t<p class=\"pos2\"><strong class=\"textsize1\">";
echo $creditor->CreditorCode;
echo "</strong></p>\n\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n<!--box1-->\n<div class=\"box2\" id=\"tabs\">\n<!--box1-->\n\t\n\t<!--top-->\n\t<div class=\"top\">\n\t<!--top-->\n\t\t\n\t\t<ul class=\"list3\">\n\t\t\t<li class=\"on\"><a href=\"#tab-general\">";
echo __("general");
echo "</a></li>\n\t\t\t<li><a href=\"#tab-groups\">";
echo __("creditorgroups");
echo "</a></li>\n\t\t\t";
if($creditor->Comment) {
    echo "<li><a href=\"#tab-comment\">";
    echo __("internal note");
    echo " <span class=\"ico actionblock info nm\">";
    echo __("more information");
    echo "</span></a></li>";
}
echo "            ";
if(!empty($creditor->Attachment)) {
    echo "                    <li><a href=\"#tab-attachments\">";
    echo __("attachments");
    echo " (";
    echo count($creditor->Attachment);
    echo ")</a></li>\n                    ";
}
echo "\t\t</ul>\n\t\t\n\t<!--top-->\n\t</div>\n\t<!--top-->\n\n\t<!--content-->\n\t<div class=\"content\" id=\"tab-general\">\n\t<!--content-->\n\n\t\t<!--split2-->\n\t\t<div class=\"split2\">\n\t\t<!--split2-->\n\t\t\t\n\t\t\t<!--left-->\n\t\t\t<div class=\"left\">\n\t\t\t<!--left-->\n\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
echo __("creditor data");
echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t";
if($creditor->CompanyName) {
    echo "\t\t\t\t\t<strong class=\"title2\">";
    echo __("companyname");
    echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
    echo $creditor->CompanyName;
    echo "</span>\n\n\t\t\t\t\t<strong class=\"title2\">";
    echo __("company number");
    echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
    if($creditor->CompanyNumber && $creditor->Country == "NL") {
        echo "<a class=\"a1 c1 fontnormal pointer\" href=\"";
        echo COC_LOCATION . $creditor->CompanyNumber;
        echo "\" target=\"_blank\">";
        echo $creditor->CompanyNumber;
        echo "</a>";
    } elseif($creditor->CompanyNumber) {
        echo $creditor->CompanyNumber;
    } else {
        echo "&nbsp;";
    }
    echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title2\">";
    echo __("vat number");
    echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
    if($creditor->TaxNumber) {
        echo $creditor->TaxNumber;
    } else {
        echo "&nbsp;";
    }
    echo "</span>\n\t\t\t\t\t<br />\n\t\t\t\t\t";
}
echo "\t\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title2\">";
echo __("contact person");
echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
echo settings::getGenderTranslation($creditor->Sex) . " " . $creditor->Initials . "&nbsp;" . $creditor->SurName;
echo "</span>\n\t\t\t\t\t<strong class=\"title2\">";
echo __("address");
echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
if($creditor->Address) {
    echo $creditor->Address;
} else {
    echo "&nbsp;";
}
if(IS_INTERNATIONAL && $creditor->Address2) {
    echo "<br />" . $creditor->Address2;
}
echo "</span>\n\t\t\n\t\t\t\t\t<strong class=\"title2\">";
echo __("zipcode and city");
echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
echo $creditor->ZipCode . "&nbsp;&nbsp;" . $creditor->City;
echo "</span>\n\n\t\t\t\t\t";
if(IS_INTERNATIONAL) {
    echo "\t\t\t\t\t\t<strong class=\"title2\">";
    echo __("state");
    echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
    echo $creditor->StateName;
    echo "</span>\n\t\t\t\t\t";
}
echo "\n\t\t\t\t\t<strong class=\"title2\">";
echo __("country");
echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
if($creditor->Country) {
    echo $array_country[$creditor->Country];
} else {
    echo "&nbsp;";
}
echo "</span>\n\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t<br />\n\t\t\t\t\n\t\t\t\t";
if($creditor->AccountNumber || $creditor->AccountName || $creditor->AccountBank || $creditor->AccountCity || $creditor->AccountBIC) {
    echo "\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
    echo __("contact data");
    echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title2\">";
    echo __("emailaddress");
    echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\" style=\"display: block;\">";
    if(0 < strlen($creditor->EmailAddress)) {
        echo "<div style=\"display: inline-block;\">";
        $ArrayEmailAddress = explode(";", check_email_address($creditor->EmailAddress, "convert"));
        foreach ($ArrayEmailAddress as $email) {
            echo "<a class=\"a1 c1 fontnormal\" href=\"mailto:";
            echo urlencode($email);
            echo "\">";
            echo $email;
            echo "</a><br />";
        }
        echo "</div>";
    } else {
        echo "&nbsp;";
    }
    echo "</span>\n\t\t\t\t\t\n\t\t\t\t\t";
    if($creditor->PhoneNumber || !$creditor->PhoneNumber && !$creditor->MobileNumber) {
        echo "\t\t\t\t\t<strong class=\"title2\">";
        echo __("phonenumber");
        echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
        if($creditor->PhoneNumber) {
            echo phoneNumberLink($creditor->PhoneNumber);
        } else {
            echo "&nbsp;";
        }
        echo "</span>\n\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\n\t\t\t\t\t";
    if($creditor->MobileNumber) {
        echo "\t\t\t\t\t<strong class=\"title2\">";
        echo __("mobilenumber");
        echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
        echo phoneNumberLink($creditor->MobileNumber);
        echo "</span>\n\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\n\t\t\t\t\t";
    if($creditor->FaxNumber) {
        echo "\t\t\t\t\t<strong class=\"title2\">";
        echo __("faxnumber");
        echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
        echo $creditor->FaxNumber;
        echo "</span>\n\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\n\t\t\t\t\t";
    if($creditor->MyCustomerCode) {
        echo "\t\t\t\t\t<br />\n\t\t\t\t\t<strong class=\"title2\">";
        echo __("my customer code");
        echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
        echo $creditor->MyCustomerCode;
        echo "</span>\n\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t\t";
}
echo "\t\t\t\t\n\t\t\t<!--left-->\n\t\t\t</div>\n\t\t\t<!--left-->\n\t\t\t\t\n\t\t\t<!--right-->\n\t\t\t<div class=\"right\">\n\t\t\t<!--right-->\n\t\t\t\t\t\n\t\t\t\t";
if($creditor->AccountNumber || $creditor->AccountName || $creditor->AccountBank || $creditor->AccountCity || $creditor->AccountBIC) {
    echo "\t\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
    echo __("bankaccount data");
    echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t";
    if($creditor->AccountNumber) {
        echo "\t\t\t\t\t<strong class=\"title2\">";
        echo __("account number");
        echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
        echo $creditor->AccountNumber;
        echo "</span>\n\t\t\t\t\t";
    }
    echo "\n\t\t\t\t\t";
    if($creditor->AccountNumber || $creditor->AccountName) {
        echo "\t\t\t\t\t<strong class=\"title2\">";
        echo __("account name");
        echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
        if($creditor->AccountName) {
            echo $creditor->AccountName;
        } else {
            echo "&nbsp;";
        }
        echo "</span>\n\t\t\t\t\t";
    }
    echo "\n\t\t\t\t\t";
    if($creditor->AccountBank) {
        echo "\t\t\t\t\t<strong class=\"title2\">";
        echo __("bank");
        echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
        echo $creditor->AccountBank;
        echo "</span>\n\t\t\t\t\t";
    }
    echo "\n\t\t\t\t\t";
    if($creditor->AccountCity) {
        echo "\t\t\t\t\t<strong class=\"title2\">";
        echo __("bank city");
        echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
        echo $creditor->AccountCity;
        echo "</span>\n\t\t\t\t\t";
    }
    echo "\n\t\t\t\t\t";
    if($creditor->AccountBIC) {
        echo "\t\t\t\t\t<strong class=\"title2\">";
        echo __("bic");
        echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
        echo $creditor->AccountBIC;
        echo "</span>\n\t\t\t\t\t";
    }
    echo "\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t";
} else {
    echo "\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
    echo __("contact data");
    echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title2\">";
    echo __("emailaddress");
    echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\" style=\"display: block;\">";
    if(0 < strlen($creditor->EmailAddress)) {
        echo "<div style=\"display: inline-block;\">";
        $ArrayEmailAddress = explode(";", check_email_address($creditor->EmailAddress, "convert"));
        foreach ($ArrayEmailAddress as $email) {
            echo "<a class=\"a1 c1 fontnormal\" href=\"mailto:";
            echo urlencode($email);
            echo "\">";
            echo $email;
            echo "</a><br />";
        }
        echo "</div>";
    } else {
        echo "&nbsp;";
    }
    echo "</span>\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t";
    if($creditor->PhoneNumber || !$creditor->PhoneNumber && !$creditor->MobileNumber) {
        echo "\t\t\t\t\t<strong class=\"title2\">";
        echo __("phonenumber");
        echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
        if($creditor->PhoneNumber) {
            echo phoneNumberLink($creditor->PhoneNumber);
        } else {
            echo "&nbsp;";
        }
        echo "</span>\n\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\n\t\t\t\t\t";
    if($creditor->MobileNumber) {
        echo "\t\t\t\t\t<strong class=\"title2\">";
        echo __("mobilenumber");
        echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
        echo phoneNumberLink($creditor->MobileNumber);
        echo "</span>\n\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\n\t\t\t\t\t";
    if($creditor->FaxNumber) {
        echo "\t\t\t\t\t<strong class=\"title2\">";
        echo __("faxnumber");
        echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
        echo $creditor->FaxNumber;
        echo "</span>\n\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\t\t\t\t\t\n\t\t\t\t\n\t\t\t\t";
}
echo "\t\t\t\t\n\t\t\t\t";
if(isset($creditor->AmountUnpaid) || isset($creditor->AmountPaid) || 0 < $creditor->Term || $creditor->Authorisation == "yes") {
    echo "\t\t\t\t<br />\n\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
    echo __("financial data");
    echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t<!--box3-->\n\n                    ";
    if($creditor->Authorisation == "yes") {
        echo "                        <strong class=\"title2\">";
        echo __("authorization");
        echo "</strong>\n                        <span class=\"title2_value\">";
        echo __("creditinvoice authorisation");
        echo "</span>\n                    ";
    }
    echo "\n\t\t\t\t\t";
    if(0 < $creditor->Term) {
        echo "\t\t\t\t\t<strong class=\"title2\">";
        echo __("payment term");
        echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
        echo $creditor->Term . " " . __("days");
        echo "</span>\n\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\n\t\t\t\t\t";
    if(isset($creditor->AmountUnpaid)) {
        echo "\t\t\t\t\t<strong class=\"title2\">";
        echo __("open sum");
        echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
        echo money($creditor->AmountUnpaid);
        echo "</span>\n\t\t\t\t\t";
    }
    echo "\n\t\t\t\t\t";
    if(isset($creditor->AmountPaid)) {
        echo "\t\t\t\t\t<strong class=\"title2\">";
        echo __("paid amount");
        echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
        echo money($creditor->AmountPaid);
        echo "</span>\n\t\t\t\t\t";
    }
    echo "\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t\t";
}
echo "\t\t\n\t\t\t<!--right-->\n\t\t\t</div>\n\t\t\t<!--right-->\n\t\t\t\t\n\t\t<!--split2-->\n\t\t</div>\n\t\t<!--split2-->\n\t\t\t\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\t\t\n\t<!--content-->\n\t<div class=\"content\" id=\"tab-groups\">\n\t<!--content-->\n\t\t\n\t\t<p>\n\t\t\t";
echo __("the creditor is in the following creditorgroups");
echo "\t\t</p>\n\t\t\t\t\t\t\t\n\t\t<table id=\"MainTable\" class=\"table1\" cellpadding=\"0\" cellspacing=\"0\">\n\t\t\t<tr class=\"trtitle\">\n\t\t\t\t<th scope=\"col\">";
echo __("creditorgroups");
echo "</th>\n\t\t\t</tr>\n\t\t\t";
$groupCounter = 0;
foreach ($creditor->Groups as $groupID => $group) {
    if(is_numeric($groupID)) {
        $groupCounter++;
        echo "\t\t\t\t\t<tr";
        if($groupCounter % 2 == 1) {
            echo " class=\"tr1\"";
        }
        echo ">\n\t\t\t\t\t\t<td><a href=\"creditors.php?page=show_group&amp;id=";
        echo $groupID;
        echo "\" class=\"c1 a1\">";
        echo $group["GroupName"];
        echo "</a></td>\n\t\t\t\t\t</tr>\n\t\t\t\t\t";
    }
}
if($groupCounter === 0) {
    echo "\t\t\t\t<tr class=\"tr1\">\n\t\t\t\t\t<td>";
    echo __("creditor is in no creditorgroups");
    echo "</td>\n\t\t\t\t</tr>\n\t\t\t\t";
}
echo "\t\t</table>\n\t\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\t\n\t";
if($creditor->Comment) {
    echo "\t<!--content-->\n\t<div class=\"content\" id=\"tab-comment\">\n\t<!--content-->\n\t\t\n\t\t<!--box3-->\n\t\t<div class=\"box3\"><h3>";
    echo __("internal note");
    echo "</h3><div class=\"content lineheight2\">\n\t\t<!--box3-->\n\t\t\t\t\n\t\t\t<textarea class=\"text1 size5 autogrow\" readonly=\"readonly\">";
    echo $creditor->Comment;
    echo "</textarea>\n\n\t\t<!--box3-->\n\t\t</div></div>\n\t\t<!--box3-->\n\t\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\t";
}
echo "    \n    ";
if(!empty($creditor->Attachment)) {
    echo "            <div class=\"content\" id=\"tab-attachments\">\n                \n    \t\t\t<div id=\"files_list\" class=\"hide\">\n    \t\t\t\t<p class=\"align_right mar4\">\n                        <i>";
    echo __("total");
    echo ": <span id=\"files_total\"></span></i>\n                    </p>\n    \t\t\t\t\n    \t\t\t\t<ul id=\"files_list_ul\" class=\"emaillist\">\n    \t\t\t\t";
    $attachCounter = 0;
    foreach ($creditor->Attachment as $key => $value) {
        echo "                            <li>\n                                <input type=\"hidden\" name=\"File[]\" value=\"";
        echo $value->id;
        echo "\" />\n                                <span class=\"float_left\" style=\"margin-right: 30px;\">\n                                    ";
        echo rewrite_date_db2site($value->DateTime) . " " . rewrite_date_db2site($value->DateTime, "%H:%i:%s");
        echo "                                </span> \n                                <div class=\"file ico inline file_";
        echo getFileType($value->FilenameServer);
        echo "\">&nbsp;</div>\n                                \n                                <a href=\"download.php?type=creditor&amp;id=";
        echo $value->id;
        echo "\" class=\"a1 c1\" target=\"_blank\">\n                                    ";
        echo $value->Filename;
        echo "                                </a>\n                                <div class=\"filesize\">\n                                    ";
        $fileSizeUnit = getFileSizeUnit($value->Size);
        echo $fileSizeUnit["size"] . " " . $fileSizeUnit["unit"];
        echo "                                </div>\n                            </li>\n                            ";
        $attachCounter++;
    }
    echo "    \t\t\t\t</ul>\n    \t\t\t\t\n    \t\t\t\t<br />\n    \t\t\t</div>\n    \n            </div>\n            ";
}
echo "\n<!--box1-->\n</div>\n<!--box1-->\n\n<br />\n\n";
if($creditor->Status < 9) {
    echo "\t\t\n\t<!--buttonbar-->\n\t<div class=\"buttonbar\">\n\t<!--buttonbar-->\n\t\t\n\t\t";
    if(U_CREDITOR_EDIT) {
        echo "<p class=\"pos1\"><a class=\"button1 edit_icon\" href=\"creditors.php?page=edit&amp;id=";
        echo $creditor->Identifier;
        echo "\"><span>";
        echo __("edit");
        echo "</span></a></p>";
    }
    echo "\t\t";
    if(U_CREDITOR_DELETE) {
        echo "<p class=\"pos2\"><a class=\"button1 delete_icon\" onclick=\"\$('#delete_creditor').dialog('open');\"><span>";
        echo __("delete");
        echo "</span></a></p>";
    }
    echo "\t\n\t<!--buttonbar-->\n\t</div>\n\t<!--buttonbar-->\n\t\n\t<br />\n";
}
echo "\n";
if(U_CREDITOR_INVOICE_SHOW) {
    echo "\t\n<!--box1-->\n<div class=\"box2\" id=\"subtabs\">\n<!--box1-->\n\t\n\t<!--top-->\n\t<div class=\"top\">\n\t<!--top-->\n\t\t\n\t\t<ul class=\"list3\">\n\t\t\t<li class=\"on\"><a href=\"#subtab-invoices\">";
    echo __("tab_invoices");
    echo " (";
    echo $invoices["CountRows"];
    echo ")</a></li>\n\t\t</ul>\n\t\t\n\t<!--top-->\n\t</div>\n\t<!--top-->\n\n\t<!--content-->\n\t<div class=\"content\" id=\"subtab-invoices\">\n\t<!--content-->\n\t\t\n\t\t";
    if(isset($transaction_matches_options) && $transaction_matches_options !== false) {
        echo "\t\t\t<br />\n\t\t\t<p class=\"float_left\">\t\n\t\t\t\t";
        echo __("go directly to");
        echo ": \n\t\t\t\t<a class=\"a1 c1 inline_a\" href=\"#invoices\">";
        echo __("tab_invoices");
        echo " (";
        echo $invoices["CountRows"];
        echo ")</a><span>,</span> \n\t\t\t\t<a class=\"a1 c1 inline_a\" href=\"#transactions\">";
        echo __("bank transactions tab");
        echo " (<span id=\"page_total_placeholder_transactions\">0</span>)</a>\n\t\t\t</p>\n\t\t\t\n\t\t\t<br clear=\"all\" />\n\t\t\t<br />\n\t\t\n\t\t\t<p><a class=\"subtitle\" name=\"invoices\">";
        echo __("tab_invoices");
        echo "</a></p>\n\t\t\t\n\t\t\t";
    }
    require_once "views/elements/creditinvoice.table.php";
    $options = ["redirect_page" => "creditor", "redirect_id" => $creditor->Identifier, "session_name" => "creditor.show.invoice", "hide_columns" => ["Creditor"], "current_page" => $current_page, "current_page_url" => $current_page_url];
    show_creditinvoice_table($invoices, $options);
    if(isset($transaction_matches_options) && $transaction_matches_options !== false) {
        echo "\t\t\t<br />\n\t\t\t<p><a class=\"subtitle\" name=\"transactions\">";
        echo __("bank transactions tab");
        echo "</a></p>\n\t\t\t";
        generate_table("list_transaction_matches_debtor", $transaction_matches_options);
    }
    echo "\t\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\n<!--box1-->\n</div>\n<!--box1-->\n";
}
echo "\n";
if(U_CREDITOR_DELETE) {
    echo "<div id=\"delete_creditor\" class=\"hide ";
    if(isset($pagetype) && $pagetype == "confirmDelete") {
        echo "autoopen";
    }
    echo "\" title=\"";
    echo __("delete creditor title");
    echo "\">\n\t<form id=\"CreditorForm\" name=\"form_delete\" method=\"post\" action=\"?page=delete_creditor&amp;id=";
    echo $creditor->Identifier;
    echo "\">\n\t<input type=\"hidden\" name=\"id\" value=\"";
    echo $creditor->Identifier;
    echo "\"/>\n\t";
    echo sprintf(__("delete creditor description"), $creditor->CreditorCode . " " . ($creditor->CompanyName ? $creditor->CompanyName : $creditor->Initials . " " . $creditor->SurName));
    echo "<br />\n\t<br />\n\t";
    if(isset($numberOfUnpaidInvoices) && $numberOfUnpaidInvoices == 1) {
        echo "\t<div class=\"mark alt1\">\n\t\t<a class=\"close pointer\">";
        echo __("close");
        echo "</a>\n\t\t<strong><em style=\"color:#414042\">";
        echo __("warning");
        echo "</em></strong><br />\n\t\t";
        echo __("delete creditor one open invoice");
        echo "\t</div>\n\t";
    } elseif(isset($numberOfUnpaidInvoices) && 1 < $numberOfUnpaidInvoices) {
        echo "\t<div class=\"mark alt1\">\n\t\t<a class=\"close pointer\">";
        echo __("close");
        echo "</a>\n\t\t<strong><em style=\"color:#414042\">";
        echo __("warning");
        echo "</em></strong><br />\n\t\t";
        echo sprintf(__("delete creditor multiple open invoice"), $numberOfUnpaidInvoices);
        echo "\t</div>                               \n\t";
    }
    echo "\t";
    if(isset($invoices["CountRows"]) && 0 < $invoices["CountRows"]) {
        echo "        <strong>";
        echo __("select which action has to be taken");
        echo "</strong><br />\n        <input type=\"radio\" id=\"withcreditinvoice_keep\" name=\"withcreditinvoice\" value=\"keep\" checked=\"selected\" /> <label for=\"withcreditinvoice_keep\">";
        echo __("delete creditor action keep invoices");
        echo "</label><br />\n        <input type=\"radio\" id=\"withcreditinvoice_delete\" name=\"withcreditinvoice\" value=\"delete\" /> <label for=\"withcreditinvoice_delete\">";
        echo __("delete creditor action delete invoices");
        echo "</label><br />\n\t\t<br />\n\t";
    } else {
        echo "\t\t<input type=\"hidden\" name=\"withcreditinvoice\" value=\"keep\"/>\n\t";
    }
    echo "\t\n\t<input type=\"checkbox\" id=\"imsure\" name=\"imsure\" value=\"yes\"/> <label for=\"imsure\">";
    echo __("delete this creditor");
    echo "</label><br />\n\t<br />\n                \n\t<p><a id=\"delete_creditor_btn\" class=\"button2 alt1 float_left\"><span>";
    echo __("delete");
    echo "</span></a></p>\n\t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#delete_creditor').dialog('close');\"><span>";
    echo __("cancel");
    echo "</span></a></p>\n\n\t";
    if(isset($_SESSION["ActionLog"]) && isset($_SESSION["ActionLog"]["Creditor"]) && is_array($_SESSION["ActionLog"]["Creditor"]["delete"]) && 1 < count($_SESSION["ActionLog"]["Creditor"]["delete"])) {
        echo "\t\t<br class=\"clear\"/><br />\n\t    <b>";
        echo __("progress batch actions");
        echo "</b><br />\n\t\t";
        if(count($_SESSION["ActionLog"]["Creditor"]["delete"]) - 1 != 1) {
            echo sprintf(__("progress batch multiple"), count($_SESSION["ActionLog"]["Creditor"]["delete"]) - 1);
        } else {
            echo sprintf(__("progress batch one"), count($_SESSION["ActionLog"]["Creditor"]["delete"]) - 1);
        }
        echo "    ";
    }
    echo "\t</form>\n</div>\n";
}
echo "\n";
require_once "views/footer.php";

?>