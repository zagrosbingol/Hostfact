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
echo "\n<!--heading1-->\n<div class=\"heading1\">\n<!--heading1-->\n\n\t<h2>";
echo __("employee") . ": " . $acc->Name;
echo "</h2>\n\t\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n<!--box1-->\n<div class=\"box2\" id=\"tabs\">\n<!--box1-->\n\t\n\t<!--top-->\n\t<div class=\"top\">\n\t<!--top-->\n\t\t\n\t\t<ul class=\"list3\">\n\t\t\t<li class=\"on\"><a href=\"#tab-general\">";
echo __("general");
echo "</a></li>\n\t\t\t<li><a href=\"#tab-dashboard\">";
echo __("dashboard");
echo "</a></li>\n\t\t\t<li><a href=\"#tab-ticketsystem\" class=\"ticketsystem_signature\">";
echo __("ticketsystem");
echo "</a></li>\n\t\t</ul>\n\t\t\n\t<!--top-->\n\t</div>\n\t<!--top-->\n\n\t<!--content-->\n\t<div class=\"content\" id=\"tab-general\">\n\t<!--content-->\n\n\t\t<!--split2-->\n\t\t<div class=\"split2\">\n\t\t<!--split2-->\n\t\t\t\n\t\t\t<!--left-->\n\t\t\t<div class=\"left\">\n\t\t\t<!--left-->\n\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
echo __("employee data");
echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t<!--box3-->\n\t\t\t\n\t\t\t\t\t<strong class=\"title2\">";
echo __("name");
echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
if($acc->Name) {
    echo $acc->Name;
} else {
    echo "&nbsp;";
}
echo "</span>\n\t\t\t\t\t\n\t\t\t\t\t";
if($acc->Function) {
    echo "\t\t\t\t\t<strong class=\"title2\">";
    echo __("function");
    echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
    echo $acc->Function;
    echo "</span>\n\t\t\t\t\t";
}
echo "\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title2\">";
echo __("emailaddress");
echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
if($acc->EmailAddress) {
    echo "<a class=\"a1\" href=\"mailto:" . $acc->EmailAddress . "\">" . $acc->EmailAddress . "</a>";
} else {
    echo "-";
}
echo "</span>\n\t\t\t\t\t\n\t\t\t\t\t";
if($acc->PhoneNumber) {
    echo "\t\t\t\t\t<strong class=\"title2\">";
    echo __("phonenumber");
    echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
    echo phoneNumberLink($acc->PhoneNumber);
    echo "</span>\n\t\t\t\t\t";
}
echo "\t\t\t\t\t\n\t\t\t\t\t";
if($acc->MobileNumber) {
    echo "\t\t\t\t\t<strong class=\"title2\">";
    echo __("mobilenumber");
    echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
    echo phoneNumberLink($acc->MobileNumber);
    echo "</span>\n\t\t\t\t\t";
}
echo "                    \n\t\t\t\t\t<strong class=\"title2\">";
echo __("backoffice language");
echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
echo $array_backoffice_languages[$acc->Language];
echo "</span>\n\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
echo __("login data employee");
echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t<!--box3-->\n\t\t\t\n\t\t\t\t\t<strong class=\"title2\">";
echo __("username");
echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
if($acc->UserName) {
    echo $acc->UserName;
} else {
    echo "&nbsp;";
}
echo "</span>\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title2\">";
echo __("last login");
echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
if($acc->LastDate != "0000-00-00 00:00:00") {
    echo rewrite_date_db2site($acc->LastDate) . " " . __("at") . " " . rewrite_date_db2site($acc->LastDate, "%H:%i");
} else {
    echo "-";
}
echo "</span>\n                    \n                    <strong class=\"title2\">";
echo __("2 factor authentication");
echo "</strong>\n\t\t\t\t\t\n                        ";
if($acc->TwoFactorAuthentication == "on" && $acc->TokenData != "") {
    $token_data = unserialize(base64_decode($acc->TokenData));
    echo "                                    <span class=\"title2_value\">\n                                        ";
    if($token_data["tokentype"] == "HOTP") {
        echo __("activated") . ", " . strtolower(__("token type counter based"));
    } elseif($token_data["tokentype"] == "TOTP") {
        echo __("activated") . ", " . strtolower(__("token type time based"));
    }
    echo "                                        ";
    if($acc->Identifier == $_SESSION["UserPro"] || U_COMPANY_EDIT) {
        echo "                                                <a class=\"a1 c1 float_right\" onclick=\"\$('#deactivate_two_factor_auth').dialog('open');\">\n                                                    ";
        echo __("employee deactivate two factor authentication");
        echo "                                                </a>\n                                                ";
    }
    echo "                                    </span>                                    \n                                ";
} else {
    echo "                                <span class=\"title2_value\">\n                                    ";
    echo __("not activated");
    echo "                                    ";
    if($acc->Identifier == $_SESSION["UserPro"]) {
        echo "                                            <a class=\"a1 c1 float_right\" onclick=\"\$('#two_factor_authentication').dialog('open');\">\n                                                ";
        echo __("employee activate two factor authentication");
        echo "                                            </a>\n                                            ";
    }
    echo "                                </span>\n                                ";
}
echo "     \n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\n\t\t\t<!--left-->\n\t\t\t</div>\n\t\t\t<!--left-->\n\t\t\t\n\t\t\t<!--right-->\n\t\t\t<div class=\"right\">\n\t\t\t<!--right-->\n            ";
if(U_COMPANY_EDIT) {
    echo "\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
    echo __("userrights");
    echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t<table>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td width=\"200\"><strong>";
    echo __("relations");
    echo "</strong></td>\n\t\t\t\t\t\t\t<td width=\"80\" class=\"smallfont\">";
    echo __("view");
    echo "</td>\n\t\t\t\t\t\t\t<td width=\"80\" class=\"smallfont\">";
    echo __("edit");
    echo "</td>\n\t\t\t\t\t\t\t<td width=\"80\" class=\"smallfont\">";
    echo __("remove");
    echo "</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td>";
    echo __("debtors");
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_DEBTOR_SHOW) && $acc->U_DEBTOR_SHOW == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_DEBTOR_EDIT) && $acc->U_DEBTOR_EDIT == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_DEBTOR_DELETE) && $acc->U_DEBTOR_DELETE == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td>";
    echo __("creditors");
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_CREDITOR_SHOW) && $acc->U_CREDITOR_SHOW == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_CREDITOR_EDIT) && $acc->U_CREDITOR_EDIT == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_CREDITOR_DELETE) && $acc->U_CREDITOR_DELETE == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td>";
    echo __("creditinvoices");
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_CREDITOR_INVOICE_SHOW) && $acc->U_CREDITOR_INVOICE_SHOW == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_CREDITOR_INVOICE_EDIT) && $acc->U_CREDITOR_INVOICE_EDIT == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_CREDITOR_INVOICE_DELETE) && $acc->U_CREDITOR_INVOICE_DELETE == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td colspan=\"4\">&nbsp;</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td width=\"150\"><strong>";
    echo __("services");
    echo "</strong></td>\n\t\t\t\t\t\t\t<td width=\"80\" class=\"smallfont\">";
    echo __("view");
    echo "</td>\n\t\t\t\t\t\t\t<td width=\"80\" class=\"smallfont\">";
    echo __("edit");
    echo "</td>\n\t\t\t\t\t\t\t<td width=\"80\" class=\"smallfont\">";
    echo __("remove");
    echo "</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t";
    $service_rights = [];
    $service_rights["DOMAIN"] = __("domains");
    $service_rights["HOSTING"] = __("hosting accounts");
    $service_rights["SERVICE"] = __("other services");
    $service_rights = do_filter("employee_service_rights", $service_rights);
    foreach ($service_rights as $tmp_service_right_key => $tmp_service_right_title) {
        echo "\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td>";
        echo $tmp_service_right_title;
        echo "</td>\n\t\t\t\t\t\t\t\t<td>";
        if(isset($acc->{"U_" . $tmp_service_right_key . "_SHOW"}) && $acc->{"U_" . $tmp_service_right_key . "_SHOW"} == "1") {
            echo "<img src=\"images/ico_check.png\" />";
        } else {
            echo "<img src=\"images/ico_close.png\" />";
        }
        echo "</td>\n\t\t\t\t\t\t\t\t<td>";
        if(isset($acc->{"U_" . $tmp_service_right_key . "_EDIT"}) && $acc->{"U_" . $tmp_service_right_key . "_EDIT"} == "1") {
            echo "<img src=\"images/ico_check.png\" />";
        } else {
            echo "<img src=\"images/ico_close.png\" />";
        }
        echo "</td>\n\t\t\t\t\t\t\t\t<td>";
        if(isset($acc->{"U_" . $tmp_service_right_key . "_DELETE"}) && $acc->{"U_" . $tmp_service_right_key . "_DELETE"} == "1") {
            echo "<img src=\"images/ico_check.png\" />";
        } else {
            echo "<img src=\"images/ico_close.png\" />";
        }
        echo "</td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td colspan=\"4\">&nbsp;</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td width=\"150\"><strong>";
    echo __("invoicing");
    echo "</strong></td>\n\t\t\t\t\t\t\t<td width=\"80\" class=\"smallfont\">";
    echo __("view");
    echo "</td>\n\t\t\t\t\t\t\t<td width=\"80\" class=\"smallfont\">";
    echo __("edit");
    echo "</td>\n\t\t\t\t\t\t\t<td width=\"80\" class=\"smallfont\">";
    echo __("remove");
    echo "</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td>";
    echo __("invoices");
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_INVOICE_SHOW) && $acc->U_INVOICE_SHOW == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_INVOICE_EDIT) && $acc->U_INVOICE_EDIT == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_INVOICE_DELETE) && $acc->U_INVOICE_DELETE == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td>";
    echo __("pricequotes");
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_PRICEQUOTE_SHOW) && $acc->U_PRICEQUOTE_SHOW == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_PRICEQUOTE_EDIT) && $acc->U_PRICEQUOTE_EDIT == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_PRICEQUOTE_DELETE) && $acc->U_PRICEQUOTE_DELETE == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td>";
    echo __("orders");
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_ORDER_SHOW) && $acc->U_ORDER_SHOW == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_ORDER_EDIT) && $acc->U_ORDER_EDIT == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_ORDER_DELETE) && $acc->U_ORDER_DELETE == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td>";
    echo __("subscriptions");
    echo "</td>\n\t\t\t\t\t\t\t<td colspan=\"3\"><span class=\"smallfont\">";
    echo __("subscription rights explained");
    echo "</span></td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td colspan=\"4\">&nbsp;</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td width=\"150\"><strong>";
    echo __("ticket system");
    echo "</strong></td>\n\t\t\t\t\t\t\t<td width=\"80\" class=\"smallfont\">";
    echo __("view");
    echo "</td>\n\t\t\t\t\t\t\t<td width=\"80\" class=\"smallfont\">";
    echo __("edit");
    echo "</td>\n\t\t\t\t\t\t\t<td width=\"80\" class=\"smallfont\">";
    echo __("remove");
    echo "</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td>";
    echo __("ticket system");
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_TICKET_SHOW) && $acc->U_TICKET_SHOW == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_TICKET_EDIT) && $acc->U_TICKET_EDIT == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_TICKET_DELETE) && $acc->U_TICKET_DELETE == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td colspan=\"4\">&nbsp;</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td width=\"150\"><strong>";
    echo __("statistics");
    echo "</strong></td>\n\t\t\t\t\t\t\t<td width=\"80\" class=\"smallfont\">";
    echo __("view");
    echo "</td>\n\t\t\t\t\t\t\t<td width=\"80\" class=\"smallfont\">&nbsp;</td>\n\t\t\t\t\t\t\t<td width=\"80\" class=\"smallfont\">&nbsp;</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td>";
    echo __("statistics");
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_STATISTICS_SHOW) && $acc->U_STATISTICS_SHOW == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t\t<td>&nbsp;</td>\n\t\t\t\t\t\t\t<td>&nbsp;</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td colspan=\"4\">&nbsp;</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td width=\"150\"><strong>";
    echo __("agenda");
    echo "</strong></td>\n\t\t\t\t\t\t\t<td width=\"80\" class=\"smallfont\">";
    echo __("view");
    echo "</td>\n\t\t\t\t\t\t\t<td width=\"80\" class=\"smallfont\">";
    echo __("edit");
    echo "</td>\n\t\t\t\t\t\t\t<td width=\"80\" class=\"smallfont\">";
    echo __("remove");
    echo "</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td>";
    echo __("agenda");
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_AGENDA_SHOW) && $acc->U_AGENDA_SHOW == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_AGENDA_EDIT) && $acc->U_AGENDA_EDIT == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_AGENDA_DELETE) && $acc->U_AGENDA_DELETE == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td colspan=\"4\">&nbsp;</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td width=\"150\"><strong>";
    echo __("menu.management");
    echo "</strong></td>\n\t\t\t\t\t\t\t<td width=\"80\" class=\"smallfont\">";
    echo __("view");
    echo "</td>\n\t\t\t\t\t\t\t<td width=\"80\" class=\"smallfont\">";
    echo __("edit");
    echo "</td>\n\t\t\t\t\t\t\t<td width=\"80\" class=\"smallfont\">";
    echo __("remove");
    echo "</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td>";
    echo __("companyinfo employees");
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_COMPANY_SHOW) && $acc->U_COMPANY_SHOW == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_COMPANY_EDIT) && $acc->U_COMPANY_EDIT == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_COMPANY_DELETE) && $acc->U_COMPANY_DELETE == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td>";
    echo __("products");
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_PRODUCT_SHOW) && $acc->U_PRODUCT_SHOW == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_PRODUCT_EDIT) && $acc->U_PRODUCT_EDIT == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_PRODUCT_DELETE) && $acc->U_PRODUCT_DELETE == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td>";
    echo __("menu.templates");
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_LAYOUT_SHOW) && $acc->U_LAYOUT_SHOW == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_LAYOUT_EDIT) && $acc->U_LAYOUT_EDIT == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_LAYOUT_DELETE) && $acc->U_LAYOUT_DELETE == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td>";
    echo __("menu.services");
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_SERVICEMANAGEMENT_SHOW) && $acc->U_SERVICEMANAGEMENT_SHOW == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_SERVICEMANAGEMENT_EDIT) && $acc->U_SERVICEMANAGEMENT_EDIT == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_SERVICEMANAGEMENT_DELETE) && $acc->U_SERVICEMANAGEMENT_DELETE == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td>";
    echo __("menu.loglines");
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_LOGFILE_SHOW) && $acc->U_LOGFILE_SHOW == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t\t<td>&nbsp;</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_LOGFILE_DELETE) && $acc->U_LOGFILE_DELETE == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td>";
    echo __("export");
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_EXPORT_SHOW) && $acc->U_EXPORT_SHOW == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_EXPORT_EDIT) && $acc->U_EXPORT_EDIT == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_EXPORT_DELETE) && $acc->U_EXPORT_DELETE == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td colspan=\"4\">&nbsp;</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td width=\"150\"><strong>";
    echo __("settings");
    echo "</strong></td>\n\t\t\t\t\t\t\t<td width=\"80\" class=\"smallfont\">";
    echo __("view");
    echo "</td>\n\t\t\t\t\t\t\t<td width=\"80\" class=\"smallfont\">";
    echo __("edit");
    echo "</td>\n\t\t\t\t\t\t\t<td width=\"80\" class=\"smallfont\">";
    echo __("remove");
    echo "</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td>";
    echo __("menu.software.settings");
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_SETTINGS_SHOW) && $acc->U_SETTINGS_SHOW == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_SETTINGS_EDIT) && $acc->U_SETTINGS_EDIT == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_SETTINGS_DELETE) && $acc->U_SETTINGS_DELETE == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td>";
    echo __("menu.customerpanel");
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_CUSTOMERPANEL_SHOW) && $acc->U_CUSTOMERPANEL_SHOW == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_CUSTOMERPANEL_EDIT) && $acc->U_CUSTOMERPANEL_EDIT == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_CUSTOMERPANEL_DELETE) && $acc->U_CUSTOMERPANEL_DELETE == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td>";
    echo __("menu.orderform");
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_ORDERFORM_SHOW) && $acc->U_ORDERFORM_SHOW == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_ORDERFORM_EDIT) && $acc->U_ORDERFORM_EDIT == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_ORDERFORM_DELETE) && $acc->U_ORDERFORM_DELETE == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td>";
    echo __("menu.paymentoptions");
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_PAYMENT_SHOW) && $acc->U_PAYMENT_SHOW == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_PAYMENT_EDIT) && $acc->U_PAYMENT_EDIT == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_PAYMENT_DELETE) && $acc->U_PAYMENT_DELETE == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td>";
    echo __("menu.settings for services");
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_SERVICESETTING_SHOW) && $acc->U_SERVICESETTING_SHOW == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_SERVICESETTING_EDIT) && $acc->U_SERVICESETTING_EDIT == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t\t<td>";
    if(isset($acc->U_SERVICESETTING_DELETE) && $acc->U_SERVICESETTING_DELETE == "1") {
        echo "<img src=\"images/ico_check.png\" />";
    } else {
        echo "<img src=\"images/ico_close.png\" />";
    }
    echo "</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\n\t\t\t\t\t</table>\n\t\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n                ";
}
echo "\t\t\t<!--right-->\n\t\t\t</div>\n\t\t\t<!--right-->\n\t\t\t\n\t\t<!--split2-->\n\t\t</div>\n\t\t<!--split2-->\n\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\n\t<!--content-->\n\t<div class=\"content\" id=\"tab-dashboard\">\n\t<!--content-->\n\t\n\t\t<!--split2-->\n\t\t<div class=\"split2\">\n\t\t<!--split2-->\n\t\n\t\t\t<!--left-->\n\t\t\t<div class=\"left\">\n\t\t\t<!--left-->\n\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
echo __("tables on dashboard");
echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t<!--box3-->\n\t\t\t\n\t\t\t\t\t";
$x = 0;
foreach ($acc->Preferences["home"] as $key => $value) {
    if($value["Value"] == "show") {
        $x++;
        echo "\t\t\t\t\t\t<strong class=\"title\">";
        echo __($key);
        echo "</strong>\n\t\t\t\t\t";
    }
}
if($x === 0) {
    echo __("no tables are shown on dashboard");
}
echo "\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\n\t\t\t<!--left-->\n\t\t\t</div>\n\t\t\t<!--left-->\n\t\t\t\n\t\t\t<!--right-->\n\t\t\t<div class=\"right\">\n\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
echo __("tables hidden from dashboard");
echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t<!--box3-->\n\t\t\t\n\t\t\t\t\t";
$y = 0;
foreach ($acc->Preferences["home"] as $key => $value) {
    if($value["Value"] == "hidden") {
        $y++;
        echo "\t\t\t\t\t\t<strong class=\"title\">";
        echo __($key);
        echo "</strong>\n\t\t\t\t\t";
    }
}
if($y === 0) {
    echo __("no tables are hidden from dashboard");
}
echo "\t\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t<!--right-->\n\t\t\t</div>\n\t\t\t<!--right-->\n\t\t\n\t\t<!--split2-->\n\t\t</div>\n\t\t<!--split2-->\n\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\t\n\t<!--content-->\n\t<div class=\"content\" id=\"tab-ticketsystem\">\n\t<!--content-->\n\t\n\t\t<strong>";
echo __("signature used for ticketsystem");
echo "</strong><br />\n\n\t\t<div class=\"box2\" style=\"padding: 10px;\"><iframe id=\"ticketmessage_signature\" src=\"showticket.php?employee_id=";
echo $acc->Identifier;
echo "\" width=\"100%\" frameborder=\"0\" style=\"overflow-y:hidden;\"></iframe>\t\t\n\t\t<script type=\"text/javascript\">\n\t\t\$(function(){\n\t\t\t\$(\"#ticketmessage_signature\").on('load', function(){\n\t\t\t\tif(\$('#ticketmessage_signature').contents().height() > 0){\n\t\t\t\t\t\$('#ticketmessage_signature').attr('height',\$('#ticketmessage_signature').contents().height());\n\t\t\t\t}\n\t\t\t});\n\t\t\t\$('.ticketsystem_signature').click(function(){\n\t\t\t\tif(\$('#ticketmessage_signature').contents().height() > 0){\n\t\t\t\t\t\$('#ticketmessage_signature').attr('height',\$('#ticketmessage_signature').contents().height());\n\t\t\t\t}\n\t\t\t});\n\t\t\t\n\t\t});\n\t\t</script></div>\n\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\n<!--box1-->\n</div>\n<!--box1-->\n\n<br />\n\n<!--buttonbar-->\n<div class=\"buttonbar\">\n<!--buttonbar-->\n\n\t";
if(U_COMPANY_EDIT || $account->Identifier == $acc->Identifier) {
    echo "            <p class=\"pos1\">\n                <a class=\"button1 edit_icon\" href=\"company.php?page=account&action=edit&id=";
    echo $acc->Identifier;
    echo "\">\n                    <span>";
    echo __("edit");
    echo "</span>\n                </a>\n            </p>\n            ";
}
if(U_COMPANY_DELETE && $account->Identifier != $acc->Identifier) {
    echo "            <p class=\"pos2\">\n                <a class=\"button1 delete_icon\" onclick=\"\$('#delete_employee').dialog('open');\">\n                    <span>";
    echo __("delete");
    echo "</span>\n                </a>\n            </p>\n            ";
}
echo "\t\n<!--buttonbar-->\n</div>\n<!--buttonbar-->\n\n";
if(U_COMPANY_DELETE && $account->Identifier != $acc->Identifier) {
    echo "        <div id=\"delete_employee\" class=\"hide\" title=\"";
    echo __("delete employee title");
    echo "\">\n        \t<form id=\"EmployeeForm\" name=\"form_delete\" method=\"post\" action=\"company.php?page=accounts&action=delete\">\n        \t<input type=\"hidden\" name=\"id\" value=\"";
    echo $acc->Identifier;
    echo "\"/>\n        \t\n        \t";
    echo sprintf(__("delete employee description"), $acc->Name);
    echo "<br /><br />\n        \t\n        \t<input type=\"checkbox\" id=\"imsure\" name=\"imsure\" value=\"yes\"/> <label for=\"imsure\">";
    echo __("delete this employee");
    echo "</label><br />\n        \t<br />\n                        \n        \t<p><a id=\"delete_employee_btn\" class=\"button2 alt1 float_left\"><span>";
    echo __("delete");
    echo "</span></a></p>\n        \t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#delete_employee').dialog('close');\"><span>";
    echo __("cancel");
    echo "</span></a></p>\n        \t</form>\n        </div>\n        ";
}
if($acc->TwoFactorAuthentication == "on" && $acc->TokenData != "") {
    if($acc->Identifier == $_SESSION["UserPro"] || U_COMPANY_EDIT) {
        echo "            <div id=\"deactivate_two_factor_auth\" class=\"hide\" title=\"";
        echo __("deactivate two factor authentication");
        echo "\">\n            \t<form id=\"deactivateTwoFactor\" name=\"form_two_factor\" method=\"post\" action=\"company.php?page=accountshow&id=";
        echo $acc->Identifier;
        echo "&action=deactivate_twofactor\">\n                \t<input type=\"hidden\" name=\"id\" value=\"";
        echo $acc->Identifier;
        echo "\"/>\n                \t\n                \t";
        echo sprintf(__("deactivate two factor authentication description"), $acc->Name);
        echo "<br /><br />\n                \t\n                    <label>\n                \t   <input type=\"checkbox\" name=\"imsure\" value=\"yes\"/> \n                       ";
        echo __("deactivate two factor are you sure");
        echo "                    </label>\n                    <br /><br />\n                                \n                \t<p><a id=\"deactivate_twofactor_btn\" class=\"button2 alt1 float_left\"><span>";
        echo __("deactivate");
        echo "</span></a></p>\n                \t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#deactivate_two_factor_auth').dialog('close');\"><span>";
        echo __("cancel");
        echo "</span></a></p>\n            \t</form>\n            </div>\n            ";
    }
} elseif($acc->Identifier == $_SESSION["UserPro"]) {
    echo "            <div id=\"two_factor_authentication\" class=\"hide\" title=\"";
    echo __("2 factor authentication");
    echo "\">\n            \t\n                <form id=\"activateTwoFactor\" name=\"activateTwoFactor\" method=\"post\" action=\"company.php?page=accountshow&id=";
    echo $acc->Identifier;
    echo "&action=activate_twofactor\">\n                \n                    <!-- STEP 1: choose token type -->\n                    <div id=\"authenticator_generate_key\">    \n                        <p>\n                            ";
    echo __("two factor authentication explained");
    echo "                        </p>\n                        <br />\n                        \n                        <strong>";
    echo __("download authentication app");
    echo ":</strong>\n                        <ul style=\"list-style: disc; margin-left: 25px;\">\n                            <li><a class=\"a1 c1\" target=\"_blank\" href=\"https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2\">";
    echo __("google authenticator android");
    echo "</a></li>\n                            <li><a class=\"a1 c1\" target=\"_blank\" href=\"https://itunes.apple.com/app/google-authenticator/id388497605\">";
    echo __("google authenticator ios");
    echo "</a></li>\n                            <li><a class=\"a1 c1\" target=\"_blank\" href=\"https://www.winauth.com/\">";
    echo __("winauth authenticator windows");
    echo "</a></li>\n                            <li><a class=\"a1 c1\" target=\"_blank\" href=\"https://www.authy.com/\">";
    echo __("winauth authenticator mac");
    echo "</a></li>\n                        </ul>\n                        <br />\n\n                        <strong class=\"title2 hide\">";
    echo __("token type");
    echo "</strong>\n                        <span class=\"title2_value hide\">\n                            <select name=\"tokentype\" class=\"size1 text1\">\n                                <option value=\"HOTP\">";
    echo __("token type counter based");
    echo "</option>\n                                <option value=\"TOTP\" selected=\"selected\">";
    echo __("token type time based");
    echo "</option>\n                            </select>\n                            &nbsp;&nbsp;\n                        </span>\n\t\t\t\t\t\t<p>";
    echo __("token generate text");
    echo "</p>\n\t\t\t\t\t\t<a class=\"a1 c1 generate_key\">";
    echo __("generate authenticator key");
    echo "</a>\n                    </div>\n                    \n                    <!-- STEP 2: generate key and validate code -->\n                    <div id=\"authenticator_key\" class=\"hide\">\n                        <p>\n                            ";
    echo __("login authenticator key explained");
    echo "<br /><br />                            \n                        </p>\n                        \n                        <div style=\"text-align: center;\">\n                            <strong class=\"auth_key\" style=\"font-family: monospace; font-size: 15px;\"></strong>\n                            <img class=\"qr_code\" src=\"\" width=\"150\" height=\"150\" style=\"margin: 0px auto;\" />\n                            <input type=\"hidden\" name=\"authenticator_key\" value=\"\" />\n                        </div>\n                        \n                        <strong>";
    echo __("verify your code");
    echo "</strong><br />\n                        <span>\n                            ";
    echo __("validate authenticator code description");
    echo "                        </span>\n                        <br /><br />\n                        \n                        <strong class=\"title2\">";
    echo __("authentication code");
    echo "</strong>\n                        <span class=\"title2_value\">\n                            <input type=\"text\" name=\"authCode\" class=\"text1 size1\" autocorrect=\"off\" autocapitalize=\"off\" autocomplete=\"off\" autofocus />\n                            &nbsp;&nbsp;<a class=\"a1 c1\" id=\"verify_auth_code\">";
    echo __("validate");
    echo "</a>\n                        </span>\n                        \n                        <strong class=\"title2\"></strong>\n                        <span id=\"verify_result_error\" class=\"c6 hide\">";
    echo __("the code you entered is invalid");
    echo "</span>\n                        <span id=\"verify_result_success\" class=\"c2 hide\">";
    echo __("the code you entered is valid");
    echo "</span>\n                        \n                        <input type=\"hidden\" name=\"verify_result\" value=\"\" />\n                                         \n                    </div>\n                    \n                    <br />\n                    <p class=\"align_right\">\n                        <a class=\"a1 c1 float_left\" onclick=\"\$('#two_factor_authentication').dialog('close');\"><span>";
    echo __("cancel");
    echo "</span></a>\n                \t\t<a class=\"button2 alt1\" id=\"activate_twofactor_btn\"><span>";
    echo __("activate");
    echo "</span></a>\n                \t</p>\n                    \n                </form>\n            </div>\n            \n            <script type=\"text/javascript\">\n    \t\t\t\$(function()\n                {\n                    \$('#two_factor_authentication .generate_key').click( function()\n                    {\n                        \$.post('XMLRequest.php', { action: 'generate_twofactor_key', tokentype: \$('select[name=\"tokentype\"]').val() }, \n                            function(data)\n                            { \n                                \$('#authenticator_key .auth_key').text(data.auth_key);\n                                \$('#authenticator_key input[name=\"authenticator_key\"]').val(data.auth_key);\n                                \$('#authenticator_key img.qr_code').attr('src', 'https://chart.googleapis.com/chart?chs=150x150&chld=M|0&cht=qr&chl=' + data.qr_url);\n                                \$('#authenticator_generate_key').hide();\n                                \$('#authenticator_key').show();\n                                \$('#authenticator_key input[name=\"verify_result\"]').val('');\n                            }\n                            , \"json\"\n                        );\n                    });\n                       \n                    \$('a#verify_auth_code').click(function()\n                    {\n                        \$.post('XMLRequest.php', { action: 'verify_auth_code', authCode: \$('input[name=\"authCode\"]').val() }, \n                            function(data)\n                            {\n                                if(data)\n                                {\n                                    \$('#verify_result_error').hide();\n                                    \$('#verify_result_success').show();\n                                    \$('#authenticator_key input[name=\"verify_result\"]').val('success');\n                                    \$('#activate_twofactor_btn').removeClass('button2').addClass('button1');\n                                }\n                                else\n                                {\n                                    \$('#verify_result_success').hide();\n                                    \$('#authenticator_key input[name=\"verify_result\"]').val('');\n                                    \$('#verify_result_error').hide().fadeIn();\n                                    \$('#activate_twofactor_btn').removeClass('button1').addClass('button2');\n                                }\n                            }\n                            , \"json\"\n                        );\n                    });\n                });\n            </script>\n            ";
}
echo "\n";
require_once "views/footer.php";

?>