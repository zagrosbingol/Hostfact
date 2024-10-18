<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
$_is_debtor_show = true;
require_once "views/header.php";
echo "\n";
if($debtor->Status != 9) {
    echo "<ul class=\"list1\">\n\t";
    if(U_INVOICE_EDIT) {
        echo "<li><a class=\"ico set1 invoice\" href=\"invoices.php?page=add&debtor=";
        echo $debtor->Identifier;
        echo "\">";
        echo __("new invoice");
        echo "</a></li>";
    }
    echo "\t";
    if(U_PRICEQUOTE_EDIT) {
        echo "<li><a class=\"ico set1 pricequote\" href=\"pricequotes.php?page=add&debtor=";
        echo $debtor->Identifier;
        echo "\">";
        echo __("new pricequote");
        echo "</a></li>";
    }
    echo "\t";
    if(U_SERVICE_ADD) {
        echo "<li><a class=\"ico set1 subscription\" href=\"services.php?page=add&debtor=";
        echo $debtor->Identifier;
        echo "\">";
        echo __("new service");
        echo "</a></li>";
    }
    echo "\t";
    if(U_DEBTOR_EDIT && isset($debtor->EmailAddress) && $debtor->EmailAddress) {
        echo "\t<li><a class=\"ico set1 send\" href=\"debtors.php?page=mailing&add_debtors=";
        echo $debtor->Identifier;
        echo "\">";
        echo __("new mailing");
        echo "</a></li>\n\t";
    }
    echo "    ";
    if(U_DEBTOR_EDIT) {
        echo "\t<li><a class=\"ico set1 interaction\" id=\"new_interaction_btn\">";
        echo __("new interaction");
        echo "</a></li>\n    ";
    }
    echo "\t<li><a class=\"ico set1 generatepdf\" id=\"debtor_pdf_generation\">";
    echo __("generate PDF");
    echo "</a></li>\n    ";
    if(TICKET_USE && $debtor->Status < 9) {
        echo "    <li><a class=\"ico set1 editdocument\" href=\"tickets.php?page=add&amp;debtor=";
        echo $debtor->Identifier;
        echo "\">";
        echo __("add ticket");
        echo "</a></li>\n    ";
    }
    echo "</ul>\n";
}
echo "\n<hr />\n\n";
echo $message;
echo "\n<!--heading1-->\n<div class=\"heading1\">\n<!--heading1-->\n\n\t<h2>";
echo __("debtor");
echo " ";
echo $debtor->CompanyName ? $debtor->CompanyName : $debtor->Initials . " " . $debtor->SurName;
echo "</h2>\n\t<p class=\"pos2\"><strong class=\"textsize1\">";
echo $debtor->DebtorCode;
echo "</strong></p>\n    \n    <input type=\"hidden\" name=\"id\" value=\"";
echo $debtor->Identifier;
echo "\" data-fullname=\"";
echo $debtor->DebtorCode . " " . ($debtor->CompanyName ? $debtor->CompanyName : $debtor->SurName . ", " . $debtor->Initials);
echo "\"/>\n\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n\t<!--box1-->\n\t<div class=\"box2 debtorinfo_inline\" id=\"tabs\">\n\t<!--box1-->\n\t\t\n\t\t<!--top-->\n\t\t<div class=\"top\">\n\t\t<!--top-->\n\t\t\n\t\t\t<ul class=\"list3\">\n\t\t\t\t<li class=\"on\"><a href=\"#tab-debtorinfo\">";
echo __("debtor information");
echo "</a></li>\n\t\t\t\t";
if($debtor->AccountNumber || $debtor->AccountName || $debtor->AccountBank || $debtor->AccountCity || $debtor->AccountBIC || $debtor->InvoiceAuthorisation == "yes") {
    echo "<li><a href=\"#tab-directdebit\">";
    echo __("direct debit and bank data");
    echo "</a></li>";
}
echo "\t\t\t\t<li><a href=\"#tab-debtorgroups\">";
echo __("debtorgroups");
echo "</a></li>\n\t\t\t\t";
if($debtor->Comment) {
    echo "<li><a href=\"#tab-note\">";
    echo __("internal note");
    echo " <span class=\"ico actionblock info nm\">";
    echo __("more information");
    echo "</span></a></li>";
}
echo "\t\t\t\t<li><a href=\"#tab-settings\">";
echo __("settings");
echo "</a></li>\n                ";
if(!empty($debtor->Attachment)) {
    echo "                        <li><a href=\"#tab-attachments\">";
    echo __("attachments");
    echo " (";
    echo count($debtor->Attachment);
    echo ")</a></li>\n                        ";
}
echo "\t\t\t</ul>\n\t\t\t\n\t\t<!--top-->\n\t\t</div>\n\t\t<!--top-->\n\t\t\n\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-debtorinfo\">\n\t\t<!--content-->\n\t\t\n\t\t\t<!--split2-->\n\t\t\t<div class=\"split2\">\n\t\t\t<!--split2-->\n\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t<div class=\"left\">\n\t\t\t\t<!--left-->\n\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("debtor information");
echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t\t";
if($debtor->CompanyName) {
    echo "\t\t\t\t\t\t<strong class=\"title2\">";
    echo __("companyname");
    echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
    echo $debtor->CompanyName;
    echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
    echo __("company number");
    echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
    if($debtor->CompanyNumber && $debtor->Country == "NL") {
        echo "<a class=\"a1 c1 fontnormal pointer\" href=\"";
        echo COC_LOCATION . $debtor->CompanyNumber;
        echo "\" target=\"_blank\">";
        echo $debtor->CompanyNumber;
        echo "</a>";
    } elseif($debtor->CompanyNumber) {
        echo $debtor->CompanyNumber;
    } else {
        echo __("unknown");
    }
    echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
    echo __("vat number");
    echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
    if($debtor->TaxNumber) {
        echo $debtor->TaxNumber;
    } else {
        echo __("unknown");
    }
    echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t";
    if(!empty($array_legaltype)) {
        echo "\t\t\t\t\t\t<strong class=\"title2\">";
        echo __("legal form");
        echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
        if($debtor->LegalForm) {
            echo $array_legaltype[$debtor->LegalForm];
        } else {
            echo __("unknown");
        }
        echo "</span>\n\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\n\t\t\t\t\t\t<br />\n\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("contact person");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
echo settings::getGenderTranslation($debtor->Sex) . " " . $debtor->Initials . "&nbsp;" . $debtor->SurName;
echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("address");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
if($debtor->Address) {
    echo $debtor->Address;
} else {
    echo "&nbsp;";
}
if(IS_INTERNATIONAL && $debtor->Address2) {
    echo "<br />" . $debtor->Address2;
}
echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("zipcode and city");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
echo $debtor->ZipCode . "&nbsp;&nbsp;" . $debtor->City;
echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t";
if(IS_INTERNATIONAL) {
    echo "\t\t\t\t\t\t\t<strong class=\"title2\">";
    echo __("state");
    echo "</strong>\n\t\t\t\t\t\t\t<span class=\"title2_value\">";
    echo $debtor->StateName;
    echo "</span>\n\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("country");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
if($debtor->Country) {
    echo $array_country[$debtor->Country];
} else {
    echo "&nbsp;";
}
echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("contact data");
echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("emailaddress");
echo "</strong><span class=\"title2_value\" style=\"display: block;\">\n\t\t\t\t\t\t\t";
if(0 < strlen($debtor->EmailAddress)) {
    echo "<div style=\"display: inline-block;\">";
    $ArrayEmailAddress = explode(";", check_email_address($debtor->EmailAddress, "convert"));
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
echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t";
if($debtor->PhoneNumber) {
    echo "\t\t\t\t\t\t<strong class=\"title2\">";
    echo __("phonenumber");
    echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
    echo phoneNumberLink($debtor->PhoneNumber);
    echo "</span>\n\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t\n\t\t\t\t\t\t";
if($debtor->MobileNumber) {
    echo "\t\t\t\t\t\t<strong class=\"title2\">";
    echo __("mobilenumber");
    echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
    if($debtor->MobileNumber) {
        echo phoneNumberLink($debtor->MobileNumber);
    } else {
        echo "&nbsp;";
    }
    echo "</span>\n\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t\n\t\t\t\t\t\t";
if($debtor->FaxNumber) {
    echo "\t\t\t\t\t\t<strong class=\"title2\">";
    echo __("faxnumber");
    echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
    if($debtor->FaxNumber) {
        echo $debtor->FaxNumber;
    } else {
        echo "&nbsp;";
    }
    echo "</span>\n\t\t\t\t\t\t";
}
echo "\n\t\t\t\t\t\t";
if($debtor->Website) {
    echo "\t\t\t\t\t\t<strong class=\"title2\">";
    echo __("website");
    echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
    if($debtor->Website) {
        echo "<a href=\"" . (strpos($debtor->Website, "http") === false ? "http://" : "") . $debtor->Website . "\" target=\"_blank\" class=\"a1 c1\">" . $debtor->Website . "</a>";
    } else {
        echo "&nbsp;";
    }
    echo "</span>\n\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("receive mailings");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
echo $array_mailingoptin[$debtor->Mailing];
echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t</div>\n\t\t\t\t<!--left-->\n\t\t\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t<div class=\"right\">\n\t\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t\t\t";
if($debtor->InvoiceCompanyName || $debtor->InvoiceInitials || $debtor->InvoiceSurName || $debtor->InvoiceAddress || $debtor->InvoiceAddress2 || $debtor->InvoiceZipCode || $debtor->InvoiceCity || $debtor->InvoiceCountry && $debtor->InvoiceCountry != $debtor->Country || $debtor->InvoiceEmailAddress) {
    echo "\t\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
    echo $debtor->InvoiceDataForPriceQuote == "yes" ? __("abnormal estimate and billingdata") : __("invoice information");
    echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t\t";
    if($debtor->InvoiceCompanyName) {
        echo "\t\t\t\t\t\t<strong class=\"title2\">";
        echo __("companyname");
        echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
        echo $debtor->InvoiceCompanyName;
        echo "</span>\n\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\n\t\t\t\t\t\t";
    if($debtor->InvoiceInitials || $debtor->InvoiceSurName) {
        echo "\t\t\t\t\t\t<strong class=\"title2\">";
        echo __("contact person");
        echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
        echo settings::getGenderTranslation($debtor->InvoiceSex) . " " . $debtor->InvoiceInitials . " " . $debtor->InvoiceSurName;
        echo "</span>\n\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\n\t\t\t\t\t\t";
    if($debtor->InvoiceAddress || $debtor->InvoiceAddress2) {
        echo "\t\t\t\t\t\t<strong class=\"title2\">";
        echo __("address");
        echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
        echo $debtor->InvoiceAddress;
        if(IS_INTERNATIONAL && $debtor->InvoiceAddress2) {
            echo "<br />" . $debtor->InvoiceAddress2;
        }
        echo "</span>\n\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\n\t\t\t\t\t\t";
    if($debtor->InvoiceZipCode || $debtor->InvoiceCity) {
        echo "\t\t\t\t\t\t<strong class=\"title2\">";
        echo __("zipcode and city");
        echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
        echo $debtor->InvoiceZipCode;
        echo "&nbsp;&nbsp;";
        echo $debtor->InvoiceCity;
        echo "</span>\n\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\n\t\t\t\t\t\t";
    if(IS_INTERNATIONAL) {
        echo "\t\t\t\t\t\t\t<strong class=\"title2\">";
        echo __("state");
        echo "</strong>\n\t\t\t\t\t\t\t<span class=\"title2_value\">";
        echo $debtor->InvoiceStateName;
        echo "</span>\n\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\n\t\t\t\t\t\t";
    if($debtor->InvoiceCountry && ($debtor->InvoiceCountry != $debtor->Country || $debtor->InvoiceAddress)) {
        echo "\t\t\t\t\t\t<strong class=\"title2\">";
        echo __("country");
        echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
        echo $array_country[$debtor->InvoiceCountry];
        echo "</span>\n\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\n\t\t\t\t\t\t";
    if($debtor->InvoiceEmailAddress) {
        echo "\t\t\t\t\t\t<strong class=\"title2\">";
        echo __("emailaddress");
        echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\" style=\"display: block;\">\n\t\t\t\t\t\t\t";
        if(0 < strlen($debtor->InvoiceEmailAddress)) {
            echo "<div style=\"display: inline-block;\">";
            $ArrayEmailAddress = explode(";", check_email_address($debtor->InvoiceEmailAddress, "convert"));
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
        echo "</span>\n\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t";
}
echo "\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("invoicing and payment");
echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("invoice method");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
echo $array_invoicemethod[$debtor->InvoiceMethod];
echo "</span>\n\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\n                    ";
if(!empty($debtor->ReminderEmailAddress)) {
    echo "                        <!--box3-->\n                        <div class=\"box3\"><h3>";
    echo __("reminders and summations");
    echo "</h3><div class=\"content lineheight2\">\n                                <!--box3-->\n                                <strong class=\"title2\">";
    echo __("emailaddress");
    echo "</strong>\n                                <span class=\"title2_value\">\n                                    <div style=\"display: inline-block;\">";
    $ArrayEmailAddress = explode(";", check_email_address($debtor->ReminderEmailAddress, "convert"));
    foreach ($ArrayEmailAddress as $email) {
        echo "<a class=\"a1 c1 fontnormal\" href=\"mailto:";
        echo urlencode($email);
        echo "\">";
        echo $email;
        echo "</a><br />";
    }
    echo "                                    </div>\n                                </span>\n                                <!--box3-->\n                            </div>\n                        </div>\n                        <!--box3-->\n                    ";
}
echo "\n\t\t\t\t\t";
if($debtor->Username) {
    echo "\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
    echo __("customerpanel");
    echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t\t";
    if($debtor->ActiveLogin == "no") {
        echo __("has no access to customerpanel");
    } else {
        echo "\t\t\t\t\t\t\n\t\t\t\t\t\t\t<strong class=\"title2\">";
        echo __("username");
        echo "</strong>\n\t\t\t\t\t\t\t<span class=\"title2_value\">\n                                ";
        echo $debtor->Username;
        echo "                                ";
        if(U_DEBTOR_EDIT) {
            echo "\t\t\t\t\t\t\t\t\t\t<a href=\"debtors.php?page=redirect_clientarea&id=";
            echo $debtor->Identifier;
            echo "\" target=\"_blank\" class=\"a1 c1 float_right\">\n\t\t\t\t\t\t\t\t\t\t\t";
            echo __("login to customerpanel");
            echo "\t\t\t\t\t\t\t\t\t\t</a>\n\t\t\t\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t\t\t\t";
        }
        echo "                            </span>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<strong class=\"title2\">";
        echo __("password");
        echo "</strong>\n\t\t\t\t\t\t\t<span class=\"title2_value\">\n\t\t\t\t\t\t\t\t******\n\t\t\t\t\t\t\t\t";
        if(U_DEBTOR_EDIT) {
            echo "<span class=\"ico actionblock find mar2 pointer\" onclick=\"\$('#DebtorPasswordDialog').dialog('open');\">&nbsp;</span>";
        }
        echo "\t\t\t\t\t\t\t</span>\n\n\t\t\t\t\t\t\t<strong class=\"title2\">";
        echo __("2 factor authentication");
        echo "</strong>\n\t\t\t\t\t\t\t<span class=\"title2_value\">\n\t\t\t\t\t\t\t\t";
        echo $debtor->TwoFactorAuthentication == "on" ? __("active") : __("not active");
        echo "\t\t\t\t\t\t\t\t";
        if($debtor->TwoFactorAuthentication == "on") {
            echo "<a onclick=\"\$('#deactivate_two_factor_auth').dialog('open');\" class=\"a1 c1 float_right\">";
            echo __("two factor authentication deactivate");
            echo "</a>";
        }
        echo "\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<strong class=\"title2\">";
        echo __("language preference");
        echo "</strong>\n\t\t\t\t\t\t\t<span class=\"title2_value\">";
        if(!$debtor->DefaultLanguage) {
            echo __("standard") . " (" . $array_customer_languages[LANGUAGE] . ")";
        } else {
            echo $array_customer_languages[$debtor->DefaultLanguage];
        }
        echo "</span>\n\n\t\t\t\t\t\t\t<strong class=\"title2\">";
        echo __("clientarea profile");
        echo "</strong>\n\t\t\t\t\t\t\t<span class=\"title2_value\">\n\t\t\t\t\t\t\t\t";
        echo htmlspecialchars($ClientareaProfiles_Model->Name);
        echo "\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<strong class=\"title2\">";
        echo __("last login");
        echo "</strong>\n\t\t\t\t\t\t\t<span class=\"title2_value\">";
        if($debtor->LastDate != "0000-00-00 00:00:00") {
            echo rewrite_date_db2site($debtor->LastDate, "%d-%m-%Y %H:%i:%s");
        } else {
            echo __("never logged in");
        }
        echo "</span>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t";
}
echo "\t\t\t\t\t\n\t\t\t\t\t";
if(U_INVOICE_SHOW) {
    echo "\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
    echo __("statistics");
    echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
    echo __("open amount");
    echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\"><span id=\"OpenAmountIncl\">-</span> ";
    echo __("incl vat");
    echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
    echo __("turnover this year");
    echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\"><span id=\"TurnoverThisYear\">-</span> ";
    echo __("excl vat");
    echo " <span id=\"TurnoverLastYear\"></span></span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
    echo __("total turnover");
    echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\"><span id=\"TotalAmountExcl\">-</span> ";
    echo __("excl vat");
    echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
    echo __("payment behavior");
    echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\"><span id=\"AverageOutstandingDays\">-</span> ";
    echo __("days");
    echo " ";
    echo __("average");
    echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t<script language=\"javascript\" type=\"text/javascript\">\n\t\t\t\t\t\t\$(function(){\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\$('#OpenAmountIncl').load('XMLRequest.php',{action:'debtor_openamount',id:";
    echo $debtor_id;
    echo "});\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\$('#AverageOutstandingDays').load('XMLRequest.php',{action:'debtor_financial_information',id:";
    echo $debtor_id;
    echo ",variable:'AverageOutstandingDays'}); \n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\$.post('XMLRequest.php', { action: 'debtor_financial_information', id: ";
    echo $debtor_id;
    echo ",variable:'total'}, function(data){\n\t\t\t\t\t\t\t\t\tif(data.TotalAmountExcl){\n\t\t\t\t\t\t\t\t\t\t\$('#TotalAmountExcl').html(data.TotalAmountExcl);\n\t\t\t\t\t\t\t\t\t}\n\t\t\t\t\t\t\t\t\tif(data.TurnoverThisYear){\n\t\t\t\t\t\t\t\t\t\t\$('#TurnoverThisYear').html(data.TurnoverThisYear);\n\t\t\t\t\t\t\t\t\t}\n\t\t\t\t\t\t\t\t\tif(data.TurnoverLastYear){\n\t\t\t\t\t\t\t\t\t\t\$('#TurnoverLastYear').html(data.TurnoverLastYear);\n\t\t\t\t\t\t\t\t\t}\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t},'json');\n\t\t\t\t\t\t\t});\n\t\t\t\t\t</script>\n\t\t\t\t\t\n\t\t\t\t\t";
}
echo "\t\n\t\t\t\t\t\n\t\t\t\t\t\n\t\t\t\t\t";
if(0 < count($debtor->customfields_list)) {
    echo "\t\t\t\t\t\t\n\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t<div class=\"box3\"><h3>";
    echo __("custom debtor fields h2");
    echo "</h3><div class=\"content\">\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t";
    foreach ($debtor->customfields_list as $k => $custom_field) {
        echo show_custom_field($custom_field, isset($debtor->custom->{$custom_field["FieldCode"]}) ? $debtor->custom->{$custom_field["FieldCode"]} : "");
    }
    echo "\t\t\t\t\t\t\t\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t</div></div>\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t";
}
echo "\t\t\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t</div>\n\t\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t<!--split2-->\n\t\t\t</div>\n\t\t\t<!--split2-->\n\t\t\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\t\t\n\t\t";
if($debtor->AccountNumber || $debtor->AccountName || $debtor->AccountBank || $debtor->AccountCity || $debtor->AccountBIC || $debtor->InvoiceAuthorisation == "yes") {
    echo "\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-directdebit\">\n\t\t<!--content-->\n\t\t\n\t\t\t<!--split2-->\n\t\t\t<div class=\"split2\">\n\t\t\t<!--split2-->\n\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t<div class=\"left\">\n\t\t\t\t<!--left-->\n\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
    echo __("bankaccount data");
    echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t\t<!--box3-->\n\n\t\t\t\t\t\t";
    if($debtor->AccountNumber) {
        echo "\t\t\t\t\t\t<strong class=\"title2\">";
        echo __("account number");
        echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
        echo $debtor->AccountNumber;
        echo "</span>\n\t\t\t\t\t\t";
    }
    echo "\n\t\t\t\t\t\t";
    if($debtor->AccountNumber || $debtor->AccountName) {
        echo "\t\t\t\t\t\t<strong class=\"title2\">";
        echo __("account name");
        echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
        if($debtor->AccountName) {
            echo $debtor->AccountName;
        } else {
            echo "&nbsp;";
        }
        echo "</span>\n\t\t\t\t\t\t";
    }
    echo "\n\t\t\t\t\t\t";
    if($debtor->AccountBank) {
        echo "\t\t\t\t\t\t<strong class=\"title2\">";
        echo __("bank");
        echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
        echo $debtor->AccountBank;
        echo "</span>\n\t\t\t\t\t\t";
    }
    echo "\n\t\t\t\t\t\t";
    if($debtor->AccountCity) {
        echo "\t\t\t\t\t\t<strong class=\"title2\">";
        echo __("bank city");
        echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
        echo $debtor->AccountCity;
        echo "</span>\n\t\t\t\t\t\t";
    }
    echo "\n\t\t\t\t\t\t";
    if($debtor->AccountBIC) {
        echo "\t\t\t\t\t\t<strong class=\"title2\">";
        echo __("bic");
        echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
        echo $debtor->AccountBIC;
        echo "</span>\n\t\t\t\t\t\t";
    }
    echo "\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t</div>\n\t\t\t\t<!--left-->\n\n\t\t\t\t<!--right-->\n\t\t\t\t<div class=\"right\">\n\t\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
    echo __("direct debit for debtor h3");
    echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
    echo __("authorization");
    echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
    echo $array_authorisation[$debtor->InvoiceAuthorisation];
    echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t\n\t\t\t\t\t\t";
    if(SDD_ID && $debtor->MandateID) {
        echo "\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
        echo __("sdd mandate id");
        echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
        echo $debtor->MandateID;
        echo "</span>\n\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
        echo __("sdd mandate date");
        echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
        echo $debtor->MandateDate;
        echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t</div>\n\t\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t<!--split2-->\n\t\t\t</div>\n\t\t\t<!--split2-->\n\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\t\t";
}
echo "\t\t\n\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-debtorgroups\">\n\t\t<!--content-->\n\t\t\n\t\t\t<table id=\"MainTable\" class=\"table1\" cellpadding=\"0\" cellspacing=\"0\">\n\t\t\t\t<tr class=\"trtitle\">\n\t\t\t\t\t<th scope=\"col\"><a href=\"";
echo $current_page_url;
echo "\" class=\"ico set2\">";
echo __("debtorgroup");
echo "</a></th>\n\t\t\t\t</tr>\n\t\t\t\t";
$groupCounter = 0;
foreach ($group_list as $groupID => $group) {
    if(is_numeric($groupID)) {
        $groupCounter++;
        echo "\t\t\t\t<tr class=\"hover_extra_info ";
        if($groupCounter % 2 === 1) {
            echo "tr1";
        }
        echo "\">\n\t\t\t\t\t<td><a href=\"debtors.php?page=show_group&amp;id=";
        echo $groupID;
        echo "\" class=\"c1 a1\">";
        echo $group["GroupName"];
        echo "</a></td>\n\t\t\t\t</tr>\n\t\t\t\t";
    }
}
if($groupCounter === 0) {
    echo "\t\t\t\t<tr>\n\t\t\t\t\t<td colspan=\"9\">\n\t\t\t\t\t\t";
    echo __("no debtorgroups found");
    echo "\t\t\t\t\t</td>\n\t\t\t\t</tr>\n\t\t\t\t";
}
echo "\t\t\t</table>\n\t\t\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\t\t\n\t\t";
if($debtor->Comment) {
    echo "\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-note\">\n\t\t<!--content-->\n\t\t\n\t\t\t<!--box3-->\n\t\t\t<div class=\"box3\"><h3>";
    echo __("internal note");
    echo "</h3><div class=\"content lineheight2\">\n\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t<textarea class=\"text1 size5 autogrow\" readonly=\"readonly\">";
    echo $debtor->Comment;
    echo "</textarea>\n\n\t\t\t<!--box3-->\n\t\t\t</div></div>\n\t\t\t<!--box3-->\n\t\t\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\t\t";
}
echo "\t\t\n\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-settings\">\n\t\t<!--content-->\n\t\t\n\t\t\t<!--split2-->\n\t\t\t<div class=\"split2\">\n\t\t\t<!--split2-->\n\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t<div class=\"left\">\n\t\t\t\t<!--left-->\n\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("invoicing");
echo "</h3><div class=\"content lineheight2 label_medium\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("invoice method");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
echo $array_invoicemethod[$debtor->InvoiceMethod];
echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("calculate vat");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
echo $array_taxable[$debtor->TaxableSetting];
echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("enable collective invoice");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">\n\t\t\t\t\t\t\t";
switch ($debtor->InvoiceCollect) {
    case "-1":
        if(INVOICE_COLLECT_ENABLED == "no") {
            echo __("no") . ", ";
            echo strtolower(__("standard setting"));
        } else {
            echo __("yes");
            echo ", ";
            echo strtolower(__("standard setting"));
        }
        break;
    case "0":
        echo __("never for this debtor");
        break;
    case "1":
        echo __("always 1x per month for this debtor");
        break;
    case "2":
        echo __("always 2x per month for this debtor");
        break;
    default:
        echo __("always for this debtor");
        echo "\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
        echo __("bill subscriptions on beforehand");
        echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
        echo $debtor->PeriodicInvoiceDays == -1 ? __("standard") . " (" . PERIODIC_INVOICE_DAYS . " " . __("days") . ")" : $debtor->PeriodicInvoiceDays . " " . __("days");
        echo "</span>\n\n\t\t\t\t\t\t";
        if($debtor->InvoiceEmailAttachments != "") {
            echo "\t\t\t\t\t\t\t\t<strong class=\"title2\">";
            echo __("ubl - invoice email attachments");
            echo "</strong>\n\t\t\t\t\t\t\t\t<span class=\"title2_value\"> ";
            echo $array_invoiceemailattachments[$debtor->InvoiceEmailAttachments];
            echo "</span>\n\t\t\t\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
        echo __("different templates");
        echo "</h3><div class=\"content lineheight2 label_medium\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
        echo __("pricequote template");
        echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
        echo $debtor->PriceQuoteTemplate ? $pricequotetemplates[$debtor->PriceQuoteTemplate]["Name"] : __("standard template");
        echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
        echo __("invoice template");
        echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
        echo $debtor->InvoiceTemplate ? $templates[$debtor->InvoiceTemplate]["Name"] : __("standard template");
        echo "</span>\n\n\t\t\t\t\t\t<strong class=\"title2\">";
        echo __("reminder email");
        echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
        echo $debtor->ReminderTemplate ? $emailtemplates[$debtor->ReminderTemplate]["Name"] : __("standard template");
        echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
        echo __("second reminder email");
        echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
        echo $debtor->SecondReminderTemplate == -1 ? __("use standard setting") : ((int) $debtor->SecondReminderTemplate === 0 ? __("as first reminder email") : $emailtemplates[$debtor->SecondReminderTemplate]["Name"]);
        echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t";
        if(INT_SUPPORT_SUMMATIONS) {
            echo "\t\t\t\t\t\t<strong class=\"title2\">";
            echo __("summation email");
            echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
            echo $debtor->SummationTemplate ? $emailtemplates[$debtor->SummationTemplate]["Name"] : __("standard template");
            echo "</span>\n\t\t\t\t\t\t";
        }
        echo "\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t</div>\n\t\t\t\t<!--left-->\n\t\t\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t<div class=\"right\">\n\t\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
        echo __("payment");
        echo "</h3><div class=\"content lineheight2 label_medium\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
        echo __("term of payment");
        echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
        echo 0 < $debtor->InvoiceTerm ? $debtor->InvoiceTerm . " " . __("days") : __("standard") . " (" . INVOICE_TERM . " " . __("days abbreviation") . ")";
        echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
        echo __("send payment notification");
        echo "</strong>\n\t\t\t\t\t\t\n\t\t\t\t\t\t";
        if($debtor->PaymentMail == "-1") {
            echo "<span class=\"title2_value\">" . __("use standard setting") . "</span>";
        } elseif($debtor->PaymentMail == "") {
            echo "<span class=\"title2_value\">" . __("do not send notification") . "</span>";
        } else {
            $i = 1;
            $PaymentMail = explode("|", $debtor->PaymentMail);
            foreach ($PaymentMail as $paymentOption) {
                echo "\t\t\t\t\t\t\t\t\t";
                if($i != 1) {
                    echo " <strong class=\"title2\" ";
                    echo $i < count($PaymentMail) ? "style=\"padding-bottom: 0;\"" : "";
                    echo ">&nbsp;</strong> ";
                }
                echo "\t\t\t\t\t\t\t\t\t<span class=\"title2_value";
                if(1 < $i) {
                    echo " lineheight0";
                }
                echo "\" ";
                echo $i < count($PaymentMail) ? "style=\"padding-bottom: 0;\"" : "";
                echo ">";
                echo __("send a payment notification by " . $paymentOption);
                echo "</span>\n\t\t\t\t\t\t";
                $i++;
            }
        }
        echo "\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
        echo __("payment confirmation template");
        echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
        echo (int) $debtor->PaymentMailTemplate === 0 ? __("use standard template") : $emailtemplates[$debtor->PaymentMailTemplate]["Name"];
        echo "</span>\n\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t";
        if($debtor->DNS1 || $debtor->DNS2 || $debtor->DNS3) {
            echo "\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
            echo __("domains");
            echo "</h3><div class=\"content lineheight2 label_medium\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\t<p>\n\t\t\t\t\t\t\t\t";
            echo __("debtor nameserver explained");
            echo "\t\t\t\t\t\t\t</p>\n\n\t\t\t\t\t\t\t<strong class=\"title2\">";
            echo __("nameserver 1");
            echo "</strong>\n\t\t\t\t\t\t\t<span class=\"title2_value\">";
            echo $debtor->DNS1 ? $debtor->DNS1 : "-";
            echo "</span>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<strong class=\"title2\">";
            echo __("nameserver 2");
            echo "</strong>\n\t\t\t\t\t\t\t<span class=\"title2_value\">";
            echo $debtor->DNS2 ? $debtor->DNS2 : "-";
            echo "</span>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<strong class=\"title2\">";
            echo __("nameserver 3");
            echo "</strong>\n\t\t\t\t\t\t\t<span class=\"title2_value\">";
            echo $debtor->DNS3 ? $debtor->DNS3 : "-";
            echo "</span>\t\t\t\t\t\t\n\t\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
        echo __("webhosting");
        echo "</h3><div class=\"content lineheight2 label_medium\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
        echo __("preference server");
        echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
        if(0 < $debtor->Server) {
            echo "<a href=\"servers.php?page=show&amp;id=";
            echo $debtor->Server;
            echo "\" class=\"a1\">";
            echo isset($list_servers) ? $list_servers[$debtor->Server]["Name"] : "";
            echo "</a> ";
            if(0 < $debtor->Server && $list_servers[$debtor->Server]["Location"]) {
                echo "<a href=\"";
                echo substr($list_servers[$debtor->Server]["Location"], 0, 4) != "http" ? "http://" : "";
                echo $list_servers[$debtor->Server]["Location"];
                echo ":";
                echo $list_servers[$debtor->Server]["Port"];
                echo "\" target=\"_blank\" class=\"a1 c1\">";
                echo __("server controlpanel link");
                echo "</a>";
            }
        } else {
            echo __("no preference");
        }
        echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t</div>\n\t\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t<!--split2-->\n\t\t\t</div>\n\t\t\t<!--split2-->\n\t\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n        \n        ";
        if(!empty($debtor->Attachment)) {
            echo "                <div class=\"content\" id=\"tab-attachments\">\n                    \n        \t\t\t<div id=\"files_list\" class=\"hide\">\n        \t\t\t\t<p class=\"align_right mar4\">\n                            <i>";
            echo __("total");
            echo ": <span id=\"files_total\"></span></i>\n                        </p>\n        \t\t\t\t\n        \t\t\t\t<ul id=\"files_list_ul\" class=\"emaillist\">\n        \t\t\t\t";
            $attachCounter = 0;
            foreach ($debtor->Attachment as $key => $value) {
                echo "                                <li>\n                                    <input type=\"hidden\" name=\"File[]\" value=\"";
                echo $value->id;
                echo "\" />\n                                    <span class=\"float_left\" style=\"margin-right: 30px;\">\n                                        ";
                echo rewrite_date_db2site($value->DateTime) . " " . rewrite_date_db2site($value->DateTime, "%H:%i");
                echo "                                    </span> \n                                    <div class=\"file ico inline file_";
                echo getFileType($value->FilenameServer);
                echo "\">&nbsp;</div>\n                                    \n                                    <a href=\"download.php?type=debtor&amp;id=";
                echo $value->id;
                echo "\" class=\"a1 c1\" target=\"_blank\">\n                                        ";
                echo $value->Filename;
                echo "                                    </a>\n                                    <div class=\"filesize\">\n                                        ";
                $fileSizeUnit = getFileSizeUnit($value->Size);
                echo $fileSizeUnit["size"] . " " . $fileSizeUnit["unit"];
                echo "                                    </div>\n                                </li>\n                                ";
                $attachCounter++;
            }
            echo "        \t\t\t\t</ul>\n        \t\t\t\t\n        \t\t\t\t<br />\n        \t\t\t</div>\n        \n                </div>\n                ";
        }
        echo "        \n\t\t\n\t<!--box1-->\n\t</div>\n\t<!--box1-->\n\n\t<!--buttonbar-->\n\t<div class=\"buttonbar\" id=\"buttonbar_show_debtor\" style=\"min-height:30px;\">\n\t<!--buttonbar-->\n\n\t\t";
        if($debtor->Status != 9 && U_DEBTOR_EDIT) {
            echo "<p class=\"pos1\"><a class=\"button1 edit_icon\" href=\"debtors.php?page=edit&amp;id=";
            echo $debtor->Identifier;
            echo "\"><span>";
            echo __("edit debtor");
            echo "</span></a></p>";
        } elseif($debtor->Status == 9 && U_DEBTOR_DELETE && $debtor->Anonymous == "no") {
            echo "<p class=\"pos1\"><a class=\"button1 edit_icon\" href=\"debtors.php?page=show&amp;id=";
            echo $debtor->Identifier;
            echo "&amp;action=recover\"><span>";
            echo __("undo delete debtor");
            echo "</span></a></p>";
        }
        echo "\t\t\n\t\t<p class=\"pos3\"><a id=\"more_info\" class=\"a1 c1\">";
        echo __("show debtor information");
        echo "</a><a id=\"less_info\" class=\"a1 c1 hide\">";
        echo __("hide debtor information");
        echo "</a></p>\n\t\t\n\t\t";
        if(U_DEBTOR_DELETE) {
            if($debtor->Status != 9) {
                echo "<p class=\"pos2\"><a class=\"button1 delete_icon\" onclick=\"\$('#delete_debtor').dialog('open');\"><span>";
                echo __("delete debtor");
                echo "</span></a></p>";
            } elseif($debtor->Status == 9 && $debtor->Anonymous == "no") {
                echo "<p class=\"pos2\"><a class=\"button1 delete_icon\" onclick=\"\$('#anonimize_debtor').dialog('open');\"><span>";
                echo __("anonimize debtor");
                echo "</span></a></p>\n\t\t";
            }
        }
        echo "\t\t\n\t<!--buttonbar-->\n\t</div>\n\t<!--buttonbar-->\n\n\t";
        $services_array = [];
        if(U_DOMAIN_SHOW && isset($list_debtor_domains)) {
            $services_array["domain"] = __("domains") . " (" . $list_debtor_domains["CountRows"] . ")";
        }
        if(U_HOSTING_SHOW && isset($list_debtor_hosting)) {
            $services_array["hosting"] = __("hosting accounts") . " (" . $list_debtor_hosting["CountRows"] . ")";
        }
        if(U_SERVICE_SHOW && isset($list_debtor_other)) {
            $services_array["other"] = __("other services") . " (" . $list_debtor_other["CountRows"] . ")";
        }
        $services_array = do_filter("debtor_show_services_tab", $services_array, ["debtor_id" => $debtor->Identifier]);
        if(U_INVOICE_SHOW && isset($invoice_table_options) || U_PRICEQUOTE_SHOW && isset($pricequote_list) || U_ORDER_SHOW && isset($order_list) || (U_SERVICE_SHOW || U_DOMAIN_SHOW || U_HOSTING_SHOW) && isset($subscription_list) || !empty($services_array) || isset($list_debtor_handles) || U_TICKET_SHOW && isset($tickets) && (TICKET_USE || 0 < $tickets["CountRows"] || 0 < $tickets_closed["CountRows"])) {
            echo "\t\t<!--box1-->\n\t\t<div class=\"box2\" id=\"subtabs\">\n\t\t<!--box1-->\n\t\t\n\t\t\t<!--top-->\n\t\t\t<div class=\"top\">\n\t\t\t<!--top-->\n\t\t\t\n\t\t\t\t<ul class=\"list3\">\n\t\t\t\t\t";
            if(U_INVOICE_SHOW && isset($invoice_table_options)) {
                echo "<li class=\"on\"><a href=\"#tab-invoices\">";
                echo __("tab_invoices");
                echo " (<span id=\"page_total_placeholder_invoices\"></span>)</a></li>";
            }
            echo "\t\t\t\t\t";
            if(U_PRICEQUOTE_SHOW && isset($pricequote_list)) {
                echo "<li><a href=\"#tab-pricequotes\">";
                echo __("tab_pricequotes");
                echo " (";
                echo $pricequote_list["CountRows"];
                echo ")</a></li>";
            }
            echo "\t\t\t\t\t";
            if(U_ORDER_SHOW && isset($order_list)) {
                echo "<li><a href=\"#tab-orders\">";
                echo __("tab_orders");
                echo " (";
                echo $order_list["CountRows"];
                echo ")</a></li>";
            }
            echo "\t\t\t\t\t";
            if((U_SERVICE_SHOW || U_DOMAIN_SHOW || U_HOSTING_SHOW) && isset($subscription_list)) {
                echo "<li><a href=\"#tab-subscriptions\">";
                echo __("tab_subscriptions");
                echo " (";
                echo $subscription_list["CountRows"];
                echo ")</a></li>";
            }
            echo "\t\t\t\t\t";
            if(!empty($services_array)) {
                echo "<li><a href=\"#tab-services\">";
                echo __("tab_services");
                echo " (<span id=\"debtor_show_services_total\">";
                echo (isset($list_debtor_domains) ? $list_debtor_domains["CountRows"] : 0) + (isset($list_debtor_hosting) ? $list_debtor_hosting["CountRows"] : 0) + (isset($list_debtor_other) ? $list_debtor_other["CountRows"] : 0) + (isset($list_debtor_licenses) ? $list_debtor_licenses["CountRows"] : 0);
                echo "</span>)</a></li>";
            }
            echo "\t\t\t\t\t";
            if(isset($list_debtor_handles)) {
                echo "<li><a href=\"#tab-handles\">";
                echo __("tab_handles");
                echo " (";
                echo $list_debtor_handles["CountRows"];
                echo ")</a></li>";
            }
            echo "\t\t\t\t\t";
            if(U_TICKET_SHOW && isset($tickets) && (TICKET_USE || 0 < $tickets["CountRows"] || 0 < $tickets_closed["CountRows"])) {
                echo "<li><a href=\"#tab-tickets\">";
                echo __("tab_tickets");
                echo " (";
                echo $tickets["CountRows"];
                echo ")</a></li>";
            }
            echo "\t\t\t\t</ul>\n\t\t\t\n\t\t\t<!--top-->\n\t\t\t</div>\n\t\t\t<!--top-->\n\t\t\t\n\t\t\t";
            if(U_INVOICE_SHOW && isset($invoice_table_options)) {
                echo "                    <div class=\"content\" id=\"tab-invoices\">\n        \t\t\t\t";
                if(isset($transaction_matches_options) && $transaction_matches_options !== false) {
                    echo "        \t\t\t\t\t<br />\n        \t\t\t\t\t<p class=\"float_left\">\t\n        \t\t\t\t\t\t";
                    echo __("go directly to");
                    echo ": \n        \t\t\t\t\t\t<a class=\"a1 c1 inline_a\" href=\"#invoices\">";
                    echo __("tab_invoices");
                    echo " (<span id=\"page_total_placeholder_invoices2\"></span>)</a><span>,</span> \n        \t\t\t\t\t\t<a class=\"a1 c1 inline_a\" href=\"#transactions\">";
                    echo __("bank transactions tab");
                    echo " (<span id=\"page_total_placeholder_transactions\">0</span>)</a>\n        \t\t\t\t\t</p>\n        \t\t\t\t\t\n        \t\t\t\t\t<br clear=\"all\" />\n        \t\t\t\t\t<br />\n        \t\t\t\t\n        \t\t\t\t\t<p><a class=\"subtitle\" name=\"invoices\">";
                    echo __("tab_invoices");
                    echo "</a></p>\n        \t\t\t\t\t\n        \t\t\t\t\t";
                }
                $invoice_table_options["hide_cols"] = ["Debtor", "subtr"];
                $invoice_table_options["parameters"]["searchat"] = "Debtor";
                $invoice_table_options["parameters"]["searchfor"] = $debtor->Identifier;
                $invoice_table_options["redirect_url"] = "debtors.php?page=show&id=" . $debtor->Identifier . "#tab-invoices";
                generate_table("list_invoice_debtor", $invoice_table_options);
                if(isset($transaction_matches_options) && $transaction_matches_options !== false) {
                    echo "                            <script type=\"text/javascript\">\n                                \$(function(){\n                                    \$('#page_total_placeholder_invoices2').html(\$('#page_total_placeholder_invoices').html());\n                                });\n                            </script>\n        \t\t\t\t\t<br />\n        \t\t\t\t\t<p><a class=\"subtitle\" name=\"transactions\">";
                    echo __("bank transactions tab");
                    echo "</a></p>\n        \t\t\t\t\t";
                    generate_table("list_transaction_matches_debtor", $transaction_matches_options);
                }
                echo "                    </div>\n                    ";
            }
            echo "\t\t\t\n\t\t\t";
            if(U_PRICEQUOTE_SHOW && isset($pricequote_list)) {
                echo "\t\t\t<!--content-->\n\t\t\t<div class=\"content\" id=\"tab-pricequotes\">\n\t\t\t<!--content-->\n\t\t\t\n\t\t\t\t";
                require_once "views/elements/pricequote.table.php";
                $options = ["redirect_page" => "debtor", "redirect_id" => $debtor->Identifier, "session_name" => "debtor.show.pricequote", "current_page" => $current_page, "current_page_url" => $current_page_url, "hide_columns" => ["Debtor"]];
                show_pricequote_table($pricequote_list, $options);
                echo "\t\n\t\t\t<!--content-->\n\t\t\t</div>\n\t\t\t<!--content-->\n\t\t\t";
            }
            echo "\t\t\t\n\t\t\t";
            if(U_ORDER_SHOW && isset($order_list)) {
                echo "\t\t\t<!--content-->\n\t\t\t<div class=\"content\" id=\"tab-orders\">\n\t\t\t<!--content-->\n\t\t\t\n\t\t\t\t";
                require_once "views/elements/order.table.php";
                $options = ["redirect_page" => "debtor", "redirect_id" => $debtor->Identifier, "session_name" => "debtor.show.order", "current_page" => $current_page, "current_page_url" => $current_page_url, "hide_columns" => ["Debtor"]];
                show_order_table($order_list, $options);
                echo "\t\n\t\t\t<!--content-->\n\t\t\t</div>\n\t\t\t<!--content-->\n\t\t\t";
            }
            echo "\t\t\t\n\t\t\t";
            if((U_SERVICE_SHOW || U_DOMAIN_SHOW || U_HOSTING_SHOW) && isset($subscription_list)) {
                echo "\t\t\t<!--content-->\n\t\t\t<div class=\"content\" id=\"tab-subscriptions\">\n\t\t\t<!--content-->\n\t\t\t\t<br />\n\t\t\t\t<p class=\"float_left\"><a name=\"subscriptions\" class=\"subtitle\">";
                echo __("subscriptions");
                echo "</a>\n\t\t\t\t\t";
                if(isset($_SESSION["debtor.show.subscription"]["status"]) && in_array($_SESSION["debtor.show.subscription"]["status"], ["nextdatepassed", "active", "terminated", "autorenew_no"])) {
                    echo "<strong class=\"textsize1\"><span id=\"subscription_status_filter_span\">\n\t\t\t\t\t\t";
                    if($_SESSION["debtor.show.subscription"]["status"] == "nextdatepassed") {
                        echo "- " . __("still to invoice");
                    } elseif($_SESSION["debtor.show.subscription"]["status"] == "terminated") {
                        echo "- " . __("terminated");
                    } elseif($_SESSION["debtor.show.subscription"]["status"] == "autorenew_no") {
                        echo "- " . __("filter autorenew_no");
                    } elseif($_SESSION["debtor.show.subscription"]["status"] == "active") {
                        echo "- " . __("active");
                    }
                    echo "</span></strong>\n\t\t\t\t\t\t";
                }
                echo "\t\t\t\t</p>\n\t\t\t\t\n\t\t\t\t<p class=\"float_right\">\n\t\t\t\t\t ";
                echo __("status");
                echo "\t\t\t\t\t<select id=\"subscription_status_select\" class=\"select1\">\n\t\t\t\t\t\t<option value=\"\">";
                echo __("please choose");
                echo "</option>\n\t\t\t\t\t\t<option value=\"active\"";
                if(isset($_SESSION["debtor.show.subscription"]["status"]) && $_SESSION["debtor.show.subscription"]["status"] == "active") {
                    echo " selected=\"selected\"";
                }
                echo ">";
                echo __("active");
                echo "</option>\n\t\t\t\t\t\t<option value=\"nextdatepassed\"";
                if(isset($_SESSION["debtor.show.subscription"]["status"]) && $_SESSION["debtor.show.subscription"]["status"] == "nextdatepassed") {
                    echo " selected=\"selected\"";
                }
                echo ">";
                echo ucfirst(__("still to invoice"));
                echo "</option>\n\t\t\t\t\t\t<option value=\"autorenew_no\"";
                if(isset($_SESSION["debtor.show.subscription"]["status"]) && $_SESSION["debtor.show.subscription"]["status"] == "autorenew_no") {
                    echo " selected=\"selected\"";
                }
                echo ">";
                echo __("filter autorenew_no");
                echo "</option>\n\t\t\t\t\t\t<option value=\"activeterminated\"";
                if(isset($_SESSION["debtor.show.subscription"]["status"]) && $_SESSION["debtor.show.subscription"]["status"] == "activeterminated") {
                    echo " selected=\"selected\"";
                }
                echo ">";
                echo __("activeterminated");
                echo "</option>\n\t\t\t\t\t\t<option value=\"terminated\"";
                if(isset($_SESSION["debtor.show.subscription"]["status"]) && $_SESSION["debtor.show.subscription"]["status"] == "terminated") {
                    echo " selected=\"selected\"";
                }
                echo ">";
                echo __("terminated");
                echo "</option>\n\t\t\t\t\t</select>\n\t\t\t\t\t<script type=\"text/javascript\">\n\t\t\t\t\t\$(function(){\n\t\t\t\t\t\t\$('#subscription_status_select').change(function(){\n\t\t\t\t\t\t\tajaxSave('debtor.show.subscription','status',\$(this).val(),'Subscriptions', '";
                echo $current_page_url;
                echo "');\n\t\t\t\t\t\t\tif(\$(this).val())\n\t\t\t\t\t\t\t{\n\t\t\t\t\t\t\t\t\$('#subscription_status_filter_span').html('- ' + \$(this).find('option:selected').text());\n\t\t\t\t\t\t\t}\n\t\t\t\t\t\t\telse\n\t\t\t\t\t\t\t{\n\t\t\t\t\t\t\t\t\$('#subscription_status_filter_span').html('');\n\t\t\t\t\t\t\t}\n\t\t\t\t\t\t});\n\t\t\t\t\t});\n\t\t\t\t\t</script>\n\t\t\t\t</p>\n\t\t\t\t<br clear=\"both\" />\n\t\t\t\t\n\t\t\t\t\n\t\t\t\t";
                require_once "views/elements/subscription.table.php";
                $options = ["redirect_page" => "debtor", "redirect_id" => $debtor->Identifier, "session_name" => "debtor.show.subscription", "current_page" => $current_page, "current_page_url" => $current_page_url, "hide_columns" => ["Debtor"]];
                show_subscription_table($subscription_list, $options);
                echo "\t\t\t\t<br />\n\t\t\t\t\n\t\t\t\t<div id=\"show_table_terminations_div\">\n\t\t\t\t\t<p class=\"float_left\"><a name=\"terminations\" class=\"subtitle\">";
                echo __("terminations");
                echo "</a></p>\n\t\t\t\t\t<br clear=\"both\" />\n\t\t\t\t\t\n\t\t\t\t\t";
                $table_config_terminations["hide_cols"][] = "debtor";
                $table_config_terminations["hide_table_if_no_results"] = "terminations";
                generate_table("list_terminations_debtor", $table_config_terminations);
                echo "\t\t\t\t</div>\n\t\t\t<!--content-->\n\t\t\t</div>\n\t\t\t<!--content-->\n\t\t\t";
            }
            echo "\t\t\t\n\t\t\t";
            if(!empty($services_array)) {
                echo "\t\t\t<!--content-->\n\t\t\t<div class=\"content\" id=\"tab-services\">\n\t\t\t<!--content-->\n\t\t\t\t<br />\n\t\t\t\t<p class=\"float_left\">\t";
                echo __("go directly to");
                echo ": \n\t\t\t\t\n\t\t\t\t";
                $_service_counter = 0;
                foreach ($services_array as $_service_type => $_service_title) {
                    $_service_counter++;
                    echo "<a href=\"#";
                    echo $_service_type;
                    echo "\" class=\"a1 c1 inline_a\">";
                    echo $_service_title;
                    echo "</a>";
                    if($_service_counter < count($services_array)) {
                        echo "<span>,</span> ";
                    }
                }
                echo "\t\t\t\t";
                if(U_SERVICE_SHOW && isset($list_debtor_licenses)) {
                    echo ", <a href=\"#license\" class=\"a1 c1 inline_a\">";
                    echo __("license licenses");
                    echo " (";
                    echo $list_debtor_licenses["CountRows"];
                    echo ")</a>";
                }
                echo "\t\t\t\t</p>\n\t\t\t\t\n\t\t\t\t<p class=\"float_right\">\n\t\t\t\t\t\n\t\t\t\t</p>\n\t\t\t\t<br clear=\"both\" /><br />\n\t\t\t\t\n\t\t\t\t";
                foreach ($services_array as $_service_type => $_service_title) {
                    switch ($_service_type) {
                        case "domain":
                            echo "\t\t\t\t\t\t\t<p class=\"float_left\"><a name=\"domain\" class=\"subtitle\">";
                            echo __("domains");
                            echo "</a></p>\n\t\t\t\t\t\t\t";
                            if($debtor->Status < 9) {
                                echo "\t\t\t\t\t\t\t\t<p class=\"float_right\"><a href=\"services.php?page=add&amp;type=domain&amp;debtor=";
                                echo $debtor->Identifier;
                                echo "\" class=\"a1 c1 float_right\">";
                                echo __("new domain");
                                echo "</a></p>\n                                ";
                            }
                            echo "\t\t\t\t\t\t\t<br clear=\"both\" />\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t";
                            require_once "views/elements/domain.table.php";
                            $options = ["redirect_page" => "debtor", "redirect_id" => $debtor->Identifier, "session_name" => "debtor.show.domain", "current_page" => $current_page, "current_page_url" => $current_page_url, "hide_columns" => ["Debtor"]];
                            show_domain_table($list_debtor_domains, $options);
                            echo "\t\t\t\t\t\t\t \n\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t";
                            break;
                        case "hosting":
                            echo "\t\t\t\t\t\t\t<p class=\"float_left\"><a name=\"hosting\" class=\"subtitle\">";
                            echo __("hosting accounts");
                            echo "</a></p>\n                            ";
                            if($debtor->Status < 9) {
                                echo "\t\t\t\t\t\t\t\t<p class=\"float_right\"><a href=\"services.php?page=add&amp;type=hosting&amp;debtor=";
                                echo $debtor->Identifier;
                                echo "\" class=\"a1 c1 float_right\">";
                                echo __("new hosting account");
                                echo "</a></p>\n                                ";
                            }
                            echo "\t\t\t\t\t\t\t<br clear=\"both\" />\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t";
                            require_once "views/elements/hosting.table.php";
                            $options = ["redirect_page" => "debtor", "redirect_id" => $debtor->Identifier, "session_name" => "debtor.show.hosting", "current_page" => $current_page, "current_page_url" => $current_page_url, "hide_columns" => ["Debtor"]];
                            show_hosting_table($list_debtor_hosting, $options);
                            echo "\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t";
                            break;
                        case "other":
                            echo "\t\t\t\t\t\t\t<p class=\"float_left\"><a name=\"other\" class=\"subtitle\">";
                            echo __("other service");
                            echo "</a></p>\n                            ";
                            if($debtor->Status < 9) {
                                echo "\t\t\t\t\t\t\t\t<p class=\"float_right\"><a href=\"services.php?page=add&amp;type=other&amp;debtor=";
                                echo $debtor->Identifier;
                                echo "\" class=\"a1 c1 float_right\">";
                                echo strtolower(__("new service"));
                                echo "</a></p>\n                                ";
                            }
                            echo "\t\t\t\t\t\t\t<br clear=\"both\" />\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t";
                            require_once "views/elements/service.table.php";
                            $options = ["redirect_page" => "debtor", "redirect_id" => $debtor->Identifier, "session_name" => "debtor.show.other", "current_page" => $current_page, "current_page_url" => $current_page_url, "hide_columns" => ["Debtor"]];
                            show_service_table($list_debtor_other, $options);
                            echo "\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t";
                            break;
                        default:
                            do_action("debtor_show_services_tab_content", $_service_type);
                    }
                }
                echo "\t\n\t\t\t\t";
                if(U_SERVICE_SHOW && isset($list_debtor_licenses)) {
                    echo "\t\t\t\t<p class=\"float_left\"><a name=\"license\" class=\"subtitle\">";
                    echo __("license licenses");
                    echo "</a></p>\n                ";
                    if($debtor->Status < 9) {
                        echo "\t\t\t\t\t<p class=\"float_right\"><a href=\"services.php?page=add&amp;type=license&amp;debtor=";
                        echo $debtor->Identifier;
                        echo "\" class=\"a1 c1 float_right\">";
                        echo strtolower(__("new license"));
                        echo "</a></p>\n                    ";
                    }
                    echo "\t\t\t\t<br clear=\"both\" />\n\t\t\t\t\n\t\t\t\t";
                    require_once "views/elements/license.table.php";
                    $options = ["redirect_page" => "debtor", "redirect_id" => $debtor->Identifier, "session_name" => "debtor.show.license", "current_page" => $current_page, "current_page_url" => $current_page_url, "hide_columns" => ["Debtor"]];
                    show_license_table($list_debtor_licenses, $options);
                    echo "\t\t\t\t\n\t\t\t\t<br />\n\t\t\t\t";
                }
                echo "\t\t\t\n\t\t\t<!--content-->\n\t\t\t</div>\n\t\t\t<!--content-->\n\t\t\t";
            }
            echo "\t\t\t\n\t\t\t";
            if(isset($list_debtor_handles)) {
                echo "\t\t\t<!--content-->\n\t\t\t<div class=\"content\" id=\"tab-handles\">\n\t\t\t<!--content-->\n\t\t\t\n\t\t\t\t";
                require_once "views/elements/handle.table.php";
                $options = ["redirect_page" => "debtor", "redirect_id" => $debtor->Identifier, "session_name" => "debtor.show.handle", "current_page" => $current_page, "current_page_url" => $current_page_url, "hide_columns" => ["Debtor"]];
                show_handle_table($list_debtor_handles, $options);
                echo "\t\n\t\t\t\t\t\n\t\t\t<!--content-->\n\t\t\t</div>\n\t\t\t<!--content-->\n\t\t\t";
            }
            echo "\t\t\t\n\t\t\t";
            if(U_TICKET_SHOW && isset($tickets) && (TICKET_USE || 0 < $tickets["CountRows"] || 0 < $tickets_closed["CountRows"])) {
                echo "\t\t\t<!--content-->\n\t\t\t<div class=\"content\" id=\"tab-tickets\">\n\t\t\t<!--content-->\n\t\t\t\t<br />\n\t\t\t\t";
                if(TICKET_USE || 0 < $tickets["CountRows"]) {
                    echo "\t\t\t\t\t<p class=\"float_left\"><a class=\"subtitle\">";
                    echo __("active tickets debtor");
                    echo "</a></p>\n\t\t\t\t\t";
                    if(TICKET_USE && $debtor->Status < 9) {
                        echo "<p class=\"float_right\"><a href=\"tickets.php?page=add&amp;debtor=";
                        echo $debtor->Identifier;
                        echo "\" class=\"a1 c1 float_right\">";
                        echo strtolower(__("add ticket"));
                        echo "</a></p>";
                    }
                    echo "\t\t\t\t\t<br clear=\"both\" />\n\t\t\t\t\t\n\t\t\t\t\t";
                    require_once "views/elements/ticket.table.php";
                    $options = ["redirect_page" => "debtor", "redirect_id" => $debtor->Identifier, "session_name" => "debtor.show.ticket", "current_page" => $current_page, "current_page_url" => $current_page_url, "hide_columns" => ["Debtor"]];
                    show_ticket_table($tickets, $options);
                    echo "\t\t\t\t\t<br />\n\t\t\t\t";
                }
                echo "\t\t\t\t\n\t\t\t\t<p class=\"float_left\"><a class=\"subtitle\">";
                echo __("closed tickets");
                echo "</a></p>\n\t\t\t\t<br clear=\"both\" />\n\t\t\t\t\n\t\t\t\n\t\t\t\t";
                require_once "views/elements/ticket.table.php";
                $options = ["redirect_page" => "debtor", "redirect_id" => $debtor->Identifier, "session_name" => "debtor.show.ticket.closed", "current_page" => $current_page, "current_page_url" => $current_page_url, "hide_columns" => ["Debtor", "Status"], "hide_actions" => ["closeticket"], "table_name" => "Tickets2"];
                show_ticket_table($tickets_closed, $options);
                echo "\t\t\t\n\t\t\t<!--content-->\n\t\t\t</div>\n\t\t\t<!--content-->\n\t\t\t";
            }
            echo "\t\t\t\n\t\t<!--box1-->\n\t\t</div>\n\t\t<!--box1-->\n\t";
        }
        echo "\t\n";
        if(U_DEBTOR_DELETE) {
            echo "<div id=\"delete_debtor\" class=\"hide ";
            if(isset($pagetype) && $pagetype == "confirmDelete") {
                echo "autoopen";
            }
            echo "\" title=\"";
            echo __("delete debtor title");
            echo "\">\n\t<form id=\"DebtorForm\" name=\"form_delete\" method=\"post\" action=\"debtors.php?page=delete&amp;id=";
            echo $debtor->Identifier;
            echo "\">\n\t<input type=\"hidden\" name=\"id\" value=\"";
            echo $debtor->Identifier;
            echo "\"/>\n\t";
            echo sprintf(__("delete debtor description"), $debtor->DebtorCode . " " . ($debtor->CompanyName ? $debtor->CompanyName : $debtor->Initials . " " . $debtor->SurName));
            echo "<br />\n\t<br />\n\t";
            if(isset($subscription_list["CountRows"]) && 0 < $subscription_list["CountRows"]) {
                echo "\t";
                echo sprintf(__("delete debtor subscriptions"), $subscription_list["CountRows"], $subscription_list["CountRows"]);
                echo "<br />\n\t<br />\n\t";
            }
            echo "\t\n\t<input type=\"checkbox\" name=\"imsure\" value=\"yes\" id=\"imsure\"/> <label for=\"imsure\">";
            echo __("delete this debtor");
            echo "</label><br />\n\t<br />\n\t\n\t<p><a id=\"delete_debtor_btn\" class=\"button2 alt1 float_left\"><span>";
            echo __("delete debtor btn");
            echo "</span></a></p>\n\t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#delete_debtor').dialog('close');\"><span>";
            echo __("cancel");
            echo "</span></a></p>\n\t</form>\n</div>\n<div id=\"anonimize_debtor\" class=\"hide\" title=\"";
            echo __("anonimize debtor title");
            echo "\">\n\t<form name=\"form_anonimize\" method=\"post\" action=\"debtors.php?page=anonimize&amp;id=";
            echo $debtor->Identifier;
            echo "\">\n\t\t<input type=\"hidden\" name=\"id\" value=\"";
            echo $debtor->Identifier;
            echo "\"/>\n\t\t";
            echo sprintf(__("anonimize debtor description"), $debtor->DebtorCode . " " . ($debtor->CompanyName ? $debtor->CompanyName : $debtor->Initials . " " . $debtor->SurName));
            echo "<br />\n\t\t<br />\n\n\t\t<strong>";
            echo __("confirm action");
            echo "</strong><br />\n\t\t<label><input type=\"checkbox\" name=\"imsure\" value=\"yes\" onchange=\"\$('div[data-toggle=password]').toggle();\" /> ";
            echo __("anonimize this debtor");
            echo "</label><br />\n\t\t<br />\n\n\t\t<div data-toggle=\"password\" class=\"hide\">\n\t\t\t<strong class=\"title\">";
            echo __("confirm with password");
            echo "</strong>\n\t\t\t<input type=\"password\" name=\"Password\" autocomplete=\"off\" /><br />\n\t\t\t<br />\n\t\t</div>\n\n\t\t<p><a id=\"anonimize_debtor_btn\" class=\"button2 alt1 float_left\"><span>";
            echo __("anonimize debtor btn");
            echo "</span></a></p>\n\t\t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#anonimize_debtor').dialog('close');\"><span>";
            echo __("cancel");
            echo "</span></a></p>\n\t</form>\n</div>\n";
        }
        echo "\n";
        if(U_DEBTOR_EDIT) {
            echo "<div id=\"DebtorPasswordDialog\" title=\"";
            echo sprintf(__("change logindetails dialog title"), $debtor->DebtorCode);
            echo "\" class=\"hide actiondialog\">\n\t<form method=\"post\" name=\"DebtorPasswordForm\" action=\"debtors.php?page=changelogindetails&amp;id=";
            echo $debtor->Identifier;
            echo "\">\n\t\n\t<strong class=\"title2\" style=\"line-height: 30px;\">";
            echo __("username");
            echo "</strong>\n\t<span class=\"title2_value\"><input type=\"text\" name=\"chg_Username\" value=\"";
            echo $debtor->Username;
            echo "\" class=\"text1 size10\"/></span>\n\t\n\t<strong class=\"title2\" style=\"line-height: 30px;\">";
            echo __("temp password");
            echo "</strong>\n\t<span class=\"title2_value\">\n        <input id=\"form_Password\" type=\"text\" name=\"chg_Password\" value=\"";
            echo htmlspecialchars(passcrypt($debtor->Password));
            echo "\" class=\"text1 size10\" \n               ";
            echo htmlspecialchars(passcrypt($debtor->Password)) == "" ? "placeholder=\"******\"" : "";
            echo " /> \n        <span class=\"a1 c1 smallfont\" onclick=\"\$('#resend_login_details').show(); generatePassword(\$('#form_Password'));\">";
            echo __("new password");
            echo "</span>\n    </span>\n\t<div id=\"resend_login_details\" class=\"";
            echo htmlspecialchars(passcrypt($debtor->Password)) == "" ? "hide" : "";
            echo "\">\n\t\t";
            $passwordforgot_email_template = CLIENTAREA_PASSWORDFORGOT_EMAIL;
            echo "\t   <input type=\"checkbox\" name=\"resend\" value=\"yes\" ";
            if($passwordforgot_email_template <= 0) {
                echo "onclick=\"if(this.checked){ \$('#DebtorPasswordDialogErrorMessage').show(); }else{ \$('#DebtorPasswordDialogErrorMessage').hide(); }\"";
            }
            echo " id=\"resend\"/> <label for=\"resend\">";
            echo __("change logindetails dialog send mail");
            echo "</label><br />\n    </div>\n    \n\t<br />\n\t";
            if($passwordforgot_email_template <= 0) {
                echo "\t<div id=\"DebtorPasswordDialogErrorMessage\" class=\"hide mark alt1\">\n\t\t";
                echo __("change logindetails dialog no password-mail");
                echo "\t</div>\n\t";
            }
            echo "\t\n\t<p><a class=\"button1 alt1 float_left\" onclick=\"\$('form[name=DebtorPasswordForm]').submit();\"><span>";
            echo __("change logindetails");
            echo "</span></a></p>\n\t<br clear=\"both\"/>\n\t<br />\n\t</form>\n</div>\n\n";
        }
        echo "\n<div id=\"DebtorGeneratePDFDialog\" title=\"";
        echo sprintf(__("generate PDF dialog title"), $debtor->DebtorCode);
        echo "\" class=\"hide\">\n\t<form method=\"post\" name=\"DebtorGeneratePDFForm\" action=\"debtors.php?page=pdf&amp;id=";
        echo $debtor->Identifier;
        echo "\">\n\t<input type=\"hidden\" name=\"id\" value=\"";
        echo $debtor->Identifier;
        echo "\"/>\n\t";
        echo __("generate PDF dialog description");
        echo "<br />\n\t<br />\n\t<strong class=\"title\">";
        echo __("template");
        echo "</strong>\n\t<select name=\"Template\" class=\"text1 size4f\">\n\t<option value=\"\">";
        echo __("make your choice");
        echo "</option>\n\t\t";
        foreach ($othertemplates as $k => $v) {
            if(is_numeric($k) && !in_array($k, [INVOICE_REMINDER_LETTER, INVOICE_SUMMATION_LETTER])) {
                echo "\t\t\t<option value=\"";
                echo $k;
                echo "\">";
                echo $v["Name"];
                echo "</option>\n\t\t";
            }
        }
        echo "\t</select><br /><br />\n\t\n\t";
        if(U_DOMAIN_SHOW) {
            echo "\t<div id=\"debtor_generate_pdf_dialog_domain\" class=\"hide\">\n\t\t<strong>";
            echo __("use domain for PDF generation");
            echo "</strong><br />\n\t\t<select name=\"Domain\" class=\"text1 size4f\">\n\t\t\n\t\t</select><br /><br />\n\t</div>\n\t";
        }
        echo "\t\n\t";
        if(U_HOSTING_SHOW) {
            echo "\t<div id=\"debtor_generate_pdf_dialog_hosting\" class=\"hide\">\n\t\t<strong>";
            echo __("use hostingaccount for PDF generation");
            echo "</strong><br />\n\t\t<select name=\"Hosting\" class=\"text1 size4f\">\n\t\t\n\t\t</select><br /><br />\n\t</div>\n\t";
        }
        echo "\t\n\t<strong>";
        echo __("dialog template design title");
        echo "</strong><br />\n\t<label><input type=\"radio\" name=\"printtype\" value=\"download\" checked=\"checked\"/> ";
        echo __("dialog template design option1");
        echo "</label><br />\n\t<label><input type=\"radio\" name=\"printtype\" value=\"print\"/> ";
        echo __("dialog template design option2");
        echo "</label><br />\n\t<br />\n\t\n\t<p><a id=\"generate_pdf_btn\" class=\"button2 alt1 float_left\"><span>";
        echo __("generate PDF");
        echo "</span></a></p>\n\t<br clear=\"both\"/>\n\t<br />\n\t</form>\n</div>\n\n";
        if($debtor->TwoFactorAuthentication == "on") {
            echo "<div id=\"deactivate_two_factor_auth\" class=\"hide\" title=\"";
            echo __("deactivate two factor authentication");
            echo "\">\n\t<form id=\"deactivateTwoFactor\" name=\"form_deactivate_two_factor\" method=\"post\" action=\"?page=show&id=";
            echo $debtor->Identifier;
            echo "&action=deactivate-two-factor-auth\">\n\t\t";
            echo __("deactivate two factor authentication debtor description");
            echo "<br /><br />\n\n\t\t<label>\n\t\t\t<input type=\"checkbox\" name=\"imsure\" value=\"yes\"/>\n\t\t\t";
            echo __("deactivate two factor are you sure");
            echo "\t\t</label>\n\t\t<br /><br />\n\n\t\t<p><a id=\"deactivate_twofactor_btn\" class=\"button2 alt1 float_left\"><span>";
            echo __("deactivate");
            echo "</span></a></p>\n\t\t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#deactivate_two_factor_auth').dialog('close');\"><span>";
            echo __("cancel");
            echo "</span></a></p>\n\t</form>\n</div>\n";
        }
        echo "\n";
        if(isset($selected_tab) && $selected_tab) {
            echo "<script language=\"javascript\" type=\"text/javascript\">\n\$(function(){\n\t\$('#subtabs').tabs(\"option\", \"active\", ";
            echo $selected_tab;
            echo ");\n});\n</script>\n";
        }
        echo "\n<div id=\"debtor_sidebar_container\">\n\t<div id=\"debtorinfo_sidebar\" class=\"debtorinfo_sidebar sidebar_right\">\n\t\t<h3 class=\"no-margin-top\">";
        echo strtoupper(__("debtor information"));
        echo "</h3>\n\n\t\t";
        if($debtor->CompanyName) {
            echo "\t\t";
            echo $debtor->CompanyName;
            echo "<br />\n\t\t";
        }
        echo "\n\t\t";
        if($debtor->Initials || $debtor->SurName) {
            echo "\t\t";
            echo settings::getGenderTranslation($debtor->Sex) . " " . $debtor->Initials . "&nbsp;" . $debtor->SurName;
            echo "<br />\n\t\t";
        }
        echo "\n\t\t";
        if($debtor->Address) {
            echo "\t\t";
            echo $debtor->Address;
            echo "<br />\n\t\t";
        }
        echo "\n\t\t";
        echo $debtor->ZipCode . "&nbsp;&nbsp;" . $debtor->City;
        echo "<br />\n\t\t";
        if($debtor->Country) {
            echo $array_country[$debtor->Country];
        }
        echo "<br />\n\n\t\t<br />\n\n\t\t";
        if(0 < strlen($debtor->EmailAddress)) {
            $ArrayEmailAddress = explode(";", check_email_address($debtor->EmailAddress, "convert"));
            foreach ($ArrayEmailAddress as $email) {
                echo "<a href=\"mailto:" . urlencode($email) . "\" >" . $email . "</a><br />";
            }
            if(2 <= count($ArrayEmailAddress)) {
                echo "<br />";
            }
        }
        echo "\n\t\t";
        if($debtor->PhoneNumber) {
            echo "\t\t";
            echo phoneNumberLink($debtor->PhoneNumber);
            echo "<br />\n\t\t";
        }
        echo "\n\t\t";
        if($debtor->MobileNumber) {
            echo "\t\t";
            echo phoneNumberLink($debtor->MobileNumber);
            echo "<br />\n\t\t";
        }
        echo "\n\t\t";
        if(!empty($debtor->Attachment)) {
            echo "\t\t\t\t<div class=\"hr\"></div>\n\t\t\t\t<h3>";
            echo __("attachments");
            echo "</h3>\n\t\t\t\t<a id=\"view_attachments\">\n\t\t\t\t\t";
            echo sprintf(__("debtor has attachments"), count($debtor->Attachment));
            echo "\t\t\t\t</a>\n\t\t\t\t";
        }
        echo "\n\t\t";
        if($debtor->Comment) {
            echo "\t\t\t<div class=\"hr\"></div>\n\t\t\t<h3>";
            echo __("note");
            echo "</h3>\n\t\t\t\t<a id=\"view_full_comment\">\n\t\t\t\t\t";
            echo substr($debtor->Comment, 0, 100);
            echo "\t\t\t\t\t";
            if(100 < strlen($debtor->Comment)) {
                echo "...";
            }
            echo "\t\t\t\t</a>\n\t\t";
        }
        echo "\n\n\n\t\t<div class=\"hr\"></div>\n\t\t<h3>";
        echo strtoupper(__("interactions"));
        echo "</h3>\n\n\t\t<div id=\"interaction_block\">\n\t\t";
        if(0 < $interactions["CountRows"]) {
            $i = 0;
            echo "\t\t\t";
            foreach ($interactions as $interactionID => $interactionitem) {
                if(is_numeric($interactionID)) {
                    echo "\t\t\t<a onclick=\"\$('#all_interactions').show(); \$('#new_interaction').hide(); \$('#dialog_interactions').dialog('open');\" class=\"interaction\">\n\t\t\t\t<div class=\"type_";
                    echo $interactionitem["Type"];
                    echo "\">";
                    echo $interaction_type[$interactionitem["Type"]];
                    echo "</div>\n\t\t\t\t<div class=\"date\">";
                    echo sprintf(date("j %\\s Y", strtotime($interactionitem["Date"])), strtolower($array_months[date("m", strtotime($interactionitem["Date"]))]));
                    echo "</div>\n\t\t\t\t<div class=\"message\">";
                    echo substr($interactionitem["Message"], 0, 50);
                    echo "</div>\n\t\t\t</a>\n\t\t\t";
                    $debtor_interaction_limit = $debtor->Comment ? 2 : DEBTOR_INTERACTIONS_SMALL_LIMIT;
                    echo "\t\t\t";
                    if($debtor_interaction_limit - 1 <= $i) {
                        echo "\t\t\t<p class=\"more_interactions\">\n\t\t\t\t<a class=\"view_more_interactions\">\n\t\t\t\t\t";
                        echo sprintf(__("show all interactions"), $interactions["CountRows"]);
                        echo "\t\t\t\t</a>\n\t\t\t</p>\n\t\t";
                    } else {
                        $i++;
                    }
                }
                echo "\t\t\t";
            }
        } else {
            echo "\t\t\t";
            echo __("no results found");
            echo "\t\t";
        }
        echo "\t\t</div>\n\n\t</div>\n</div>\n\n\n<script type=\"text/javascript\">\n\n\$(function()\n{\n    \$(document).on('input', '#DebtorPasswordDialog input[name=\"chg_Password\"]', function()\n\t{\n\t\tif(\$(this).val() == '')\n\t\t{\n\t\t\t\$('#resend_login_details').hide();\n\t\t}\n\t\telse\n\t\t{\n\t\t\t\$('#resend_login_details').show();\n\t\t}\n\t});\n\n});\n</script>\n\n";
        require_once "views/footer.php";
}

?>