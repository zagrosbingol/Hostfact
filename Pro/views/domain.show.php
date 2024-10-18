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
if(U_DOMAIN_EDIT) {
    echo "\t<ul class=\"list1\">\n\t\t";
    if($domain->Status == 1 || $domain->Status == 3 || $domain->Status == 7) {
        echo "<li><a class=\"ico set1 registerdomain\" href=\"domains.php?page=show&amp;id=";
        echo $domain->Identifier;
        echo "&amp;action=register\" onclick=\"\$('#dialog').dialog('open');\">";
        echo __("action register");
        echo "</a></li>";
    }
    echo "\t\t";
    if($domain->Status == 1 || $domain->Status == 3 || $domain->Status == 7) {
        echo "<li><a class=\"ico set1 transferdomain\" href=\"domains.php?page=show&amp;id=";
        echo $domain->Identifier;
        echo "&amp;action=transfer\" onclick=\"\$('#dialog').dialog('open');\">";
        echo __("action transfer");
        echo "</a></li>";
    }
    echo "\n\t\t";
    $termination_processed = isset($domain->Termination->Date) && $domain->Termination->Date != "0000-00-00" && $domain->Termination->Date < date("Y-m-d") ? true : false;
    if($domain->Status == 4 || $domain->Status == 8 && $termination_processed === false) {
        echo "\t\t\t\t<li>\n\t\t\t\t\t<a class=\"ico set1 accept\" onclick=\"\$('#dialog_update_whois').dialog('open');\">\n\t\t\t\t\t\t";
        echo __("action edit whois");
        echo "\t\t\t\t\t</a>\n\t\t\t\t</li>\n\n\t\t\t\t<li>\n\t\t\t\t\t<a class=\"ico set1 lock\" href=\"domains.php?page=show&amp;id=";
        echo $domain->Identifier;
        echo "&amp;action=lock\" onclick=\"\$('#dialog').dialog('open');\">\n\t\t\t\t\t\t";
        echo __("action lock domain");
        echo "\t\t\t\t\t</a>\n\t\t\t\t</li>\n\n\t\t\t\t<li>\n\t\t\t\t\t<a class=\"ico set1 unlock\" href=\"domains.php?page=show&amp;id=";
        echo $domain->Identifier;
        echo "&amp;action=unlock\" onclick=\"\$('#dialog').dialog('open');\">";
        echo __("action unlock domain");
        echo "</a>\n\t\t\t\t</li>\n\n\t\t\t\t";
        if(is_module_active("dnsmanagement")) {
            global $_module_instances;
            $dnsmanagement = $_module_instances["dnsmanagement"];
            $dnsmanagement->page_domain_show_button($domain);
        }
    }
    echo "\t</ul>\n\t<hr />\n";
}
echo "\n";
echo $message;
echo "\n\n<div class=\"mark alt2 hide\" id=\"domain_success\">\n    <a class=\"close pointer\">";
echo __("close");
echo "</a>\n    <strong>\n        <em style=\"color:#0E5704\">";
echo __("successmessage");
echo "</em>\n    </strong>\n    <br>\n    <ul>\n        \n    </ul>\n</div>\n\n<div class=\"mark alt3 hide\" id=\"domain_error\">\n    <a class=\"close pointer\">";
echo __("close");
echo "</a>\n    <strong>\n        <em style=\"color:#c02e19\">";
echo __("errormessage");
echo "</em>\n    </strong>\n    <br>\n    <ul>\n    \n    </ul>\n</div>\n\n<!--heading1-->\n<div class=\"heading1\" style=\"margin-bottom: 10px;\">\n<!--heading1-->\n\n\t<h2 style=\"width: 630px;white-space: nowrap;overflow: hidden;display: inline-block;text-overflow: ellipsis;\">";
echo __("domain");
echo " ";
echo $domain->Domain . "." . $domain->Tld;
echo "</h2>\n \n\t<p class=\"pos2\"><strong class=\"textsize1\">";
echo $array_domainstatus[$domain->Status];
echo "</span></strong></p>\n    \n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n";
$enable_auto_sync = "";
if($domain->api && method_exists($domain->api, "getSyncData") && 0 < $domain->Registrar && ($domain->Status == 4 || $domain->Status == 7)) {
    if(DOMAIN_SYNC == "yes" && (DOMAIN_SYNC_EXPDATE == "yes" || DOMAIN_SYNC_NAMESERVERS == "yes") && ($domain->LastSyncDate == "0000-00-00 00:00:00" || date("Y-m-d", strtotime("+" . DOMAIN_SYNC_DAYS . " days", strtotime($domain->LastSyncDate))) <= date("Y-m-d"))) {
        $enable_auto_sync = "yes";
        $domain->LastSyncDate = date("");
    }
    echo "        <p id=\"domain_sync_text\" class=\"float_right smallfont";
    if($enable_auto_sync == "yes") {
        echo " hide";
    }
    echo "\">\n            ";
    echo __("last sync label");
    echo ": \n            <span>\n\t\t\t";
    if($domain->IsSynced == "yes" && $domain->LastSyncDate != "0000-00-00 00:00:00") {
        echo rewrite_date_db2site($domain->LastSyncDate) . " " . __("at") . " " . rewrite_date_db2site($domain->LastSyncDate, "%H:%i");
    } else {
        echo __("unknown");
    }
    echo "            </span>\n            &nbsp;&nbsp; \n            <a class=\"a1 c1\" onclick=\"\$(this).parent().hide(); \$(this).parent().siblings('.loader_saving').show();\" href=\"domains.php?page=show&amp;id=";
    echo $domain->Identifier;
    echo "&amp;action=sync\">\n                ";
    echo __("synchronize");
    echo "            </a>\n            \n        </p>\n        <p id=\"domain_sync_loader\" class=\"float_right smallfont";
    if($enable_auto_sync != "yes") {
        echo " hide";
    }
    echo " loader_saving\">\n            <span class=\"\" style=\"padding: 0;\">\n    \t\t\t<img src=\"images/icon_circle_loader_green.gif\" alt=\"\" class=\"ico inline\" style=\"margin-bottom:-6px;\" />&nbsp;&nbsp;\n    \t\t\t<span class=\"loading_green\">";
    echo __("loading");
    echo "</span>\n    \t\t</span>\n        </p>\n        \n        <br class=\"clear\" />\n        ";
}
echo "\n<input type=\"hidden\" name=\"enable_auto_sync\" value=\"";
echo $enable_auto_sync;
echo "\" />\n\n<input type=\"hidden\" name=\"domain_id\" value=\"";
echo $domain->Identifier;
echo "\" />\n";
echo $is_service_terminated;
echo "\n<!--box1-->\n<div class=\"box2 tabs\">\n<!--box1-->\n\n\t<!--top-->\n\t<div class=\"top\">\n\t<!--top-->\n\t\n\t\t<ul class=\"list3\">\n\t\t\t<li class=\"on\"><a href=\"#tab-domain\">";
echo __("general");
echo "</a></li>\n\t\t\t<li><a href=\"#tab-domain-subscription\">";
echo isset($domain->Periodic) ? show_subscription_tab_title($domain->Periodic->TerminationDate, $domain->Periodic->StartPeriod, $domain->Periodic->AutoRenew) : show_subscription_tab_title("", "", "");
echo "</a></li>\n            ";
if(U_INVOICE_SHOW && isset($domain->PeriodicID) && 0 < $domain->PeriodicID) {
    echo "                    <li><a href=\"#tab-invoices\">";
    echo __("invoices") . " (<span id=\"page_total_placeholder_invoices\"></span>)";
    echo "</a></li>\n                    ";
}
echo "\t\t\t<li><a href=\"#tab-domain-comment\">";
echo __("internal note");
echo " ";
if($domain->Comment) {
    echo "<span class=\"ico actionblock info nm\">";
    echo __("more information");
    echo "</span>";
}
echo "</a></li>\n\t\t\t<li><a href=\"#tab-domain-logfile\">";
echo __("logfile");
echo "</a></li>\n\t\t\t";
if(!empty($domain->ExtraFields)) {
    echo "<li><a href=\"#tab-domain-extra\"><span>";
    echo __("extra domain data");
    echo "</span></a></li>";
}
echo "\t\t</ul>\n\t\n\t<!--top-->\n\t</div>\n\t<!--top-->\n\n\t<!--content-->\n\t<div class=\"content\" id=\"tab-domain\">\n\t<!--content-->\n\n\t\t\n\n\t\t<!--split2-->\n\t\t<div class=\"split2\">\n\t\t<!--split2-->\n\t\t\n\t\t\t<!--left-->\n\t\t\t<div class=\"left\">\n\t\t\t<!--left-->\n\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
echo __("domain");
echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t\t<strong class=\"title2\">";
echo __("domain");
echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\" style=\"word-wrap: break-word;margin-left: 140px;\">";
echo $domain->Domain . "." . $domain->Tld;
echo "</span>\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title2\">";
echo __("hosting account");
echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
if($domain->HostingID) {
    echo "<a href=\"hosting.php?page=show&amp;id=";
    echo $domain->HostingID;
    echo "\" class=\"a1\">";
    echo $domain->Username;
    echo "</a>";
} else {
    echo __("no hosting account connected");
}
echo "</span>\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title2\">";
echo __("debtor");
echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
if(0 < $domain->Debtor) {
    echo "<a href=\"debtors.php?page=show&amp;id=";
    echo $domain->Debtor;
    echo "\" class=\"c1 a1\">";
    echo $debtor->CompanyName ? $debtor->CompanyName : $debtor->Initials . " " . $debtor->SurName;
    echo "</a>";
} else {
    echo __("new customer");
}
echo "</span>\n\t\t\t\t\t\n\t\t\t\t\t<br />\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title2\">";
echo __("domain registration date");
echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
echo $domain->RegistrationDate ? $domain->RegistrationDate : "-";
echo "</span>\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title2\">";
echo $domain->DomainAutoRenew == "on" ? __("domain extends at") : __("domain expires at");
echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
echo $domain->ExpirationDate ? $domain->ExpirationDate : "-";
echo " ";
if(U_DOMAIN_EDIT && $domain->Status == 4) {
    echo "<a class=\"a1 c1 smallfont floatr\" href=\"domains.php?page=show&amp;id=";
    echo $domain->Identifier;
    echo "&amp;action=extend\">";
    echo __("move expiredate with one year");
    echo "</a>";
}
echo "</span>\n\t\t\t\t\t\n\t\t\t\t\t";
if($domain->AuthKey || $domain->Status == 4 || $domain->Status == 8) {
    echo "\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
    echo __("domain authcode");
    echo "</strong>\n\t\t\t\t\t\t<div class=\"title2_value\">\n                            ";
    echo $domain->AuthKey ? $domain->AuthKey : __("unknown");
    echo " \n                            ";
    if($domain->Status == 4 || $domain->Status == 8) {
        echo "                                <a class=\"a1 c1 smallfont floatr\" href=\"domains.php?page=show&amp;id=";
        echo $domain->Identifier;
        echo "&amp;action=gettoken\" onclick=\"\$(this).hide(); \$(this).siblings('.loader_saving').show();\">\n                                    ";
        echo __("get domain authcode");
        echo "                                </a>\n                                <span class=\"floatr smallfont loader_saving hide\" style=\"padding: 0;\">\n                        \t\t\t<img src=\"images/icon_circle_loader_green.gif\" alt=\"\" class=\"ico inline\" style=\"margin-bottom:-6px;\" />&nbsp;&nbsp;\n                        \t\t\t<span class=\"loading_green\">";
        echo __("loading");
        echo "</span>\n                        \t\t</span>\n                            ";
    }
    echo "                        </div>\n\t\t\t\t\t";
}
echo "                    \n                    ";
if($domain->Status == 4 || $domain->Status == 8) {
    echo "                            <strong class=\"title2\">";
    echo ucfirst(__("autorenew"));
    echo "</strong>\n                            <div class=\"title2_value\">\n                                ";
    echo $domain->DomainAutoRenew == "on" ? __("on") : __("off");
    echo "                                <a class=\"a1 c1 floatr smallfont\" onclick=\"\$(this).hide(); \$(this).siblings('.loader_saving').show();\" href=\"domains.php?page=show&amp;id=";
    echo $domain->Identifier;
    echo "&amp;action=autorenew\">\n                                    ";
    echo $domain->DomainAutoRenew == "on" ? __("turn autorenew off") : __("turn autorenew on");
    echo "                                </a>\n                                \n                                <span class=\"floatr smallfont loader_saving hide\" style=\"padding: 0;\">\n                        \t\t\t<img src=\"images/icon_circle_loader_green.gif\" alt=\"\" class=\"ico inline\" style=\"margin-bottom:-6px;\" />&nbsp;&nbsp;\n                        \t\t\t<span class=\"loading_green\">";
    echo __("loading");
    echo "</span>\n                        \t\t</span>\n                            </div>\n                            ";
}
echo "                    \n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
echo __("registrar and nameservers");
echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title2\">";
echo __("registrar");
echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
if(0 < $domain->Registrar) {
    echo "<a href=\"registrars.php?page=show&amp;id=";
    echo $domain->Registrar;
    echo "\" class=\"a1\">";
    echo $domain->Name;
    echo "</a>";
} else {
    echo __("no registrar known");
}
echo "</span>\n\n                        ";
if(($domain->Status == 4 || $domain->Status == 8 && $termination_processed === false) && is_module_active("dnsmanagement")) {
    global $_module_instances;
    $dnsmanagement = $_module_instances["dnsmanagement"];
    echo $dnsmanagement->page_domain_show_nameserver_manager($domain);
}
echo "\t\t\t\t\t\n\t\t\t\t\t";
if($domain->DNS1) {
    echo "\t\t\t\t\t<strong class=\"title2\">";
    echo __("nameserver 1");
    echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
    echo $domain->DNS1;
    echo "  ";
    if($domain->DNS1IP) {
        echo "(" . $domain->DNS1IP . ")";
    }
    echo "</span>\n\t\t\t\t\t";
}
echo "\t\t\t\t\t\n\t\t\t\t\t";
if($domain->DNS2) {
    echo "\t\t\t\t\t<strong class=\"title2\">";
    echo __("nameserver 2");
    echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
    echo $domain->DNS2;
    echo "  ";
    if($domain->DNS2IP) {
        echo "(" . $domain->DNS2IP . ")";
    }
    echo "</span>\n\t\t\t\t\t";
}
echo "\t\t\t\t\t\n\t\t\t\t\t";
if($domain->DNS3) {
    echo "\t\t\t\t\t<strong class=\"title2\">";
    echo __("nameserver 3");
    echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
    echo $domain->DNS3;
    echo "  ";
    if($domain->DNS3IP) {
        echo "(" . $domain->DNS3IP . ")";
    }
    echo "</span>\n\t\t\t\t\t";
}
echo "                    \n                    ";
if(($domain->Status < 4 || $domain->Status == 7 && $domain->RegistrationDate == "" && $domain->ExpirationDate == "") && is_module_active("dnsmanagement")) {
    global $_module_instances;
    $dnsmanagement = $_module_instances["dnsmanagement"];
    echo $dnsmanagement->page_domain_show_template($domain);
}
echo "\t\t\t\t\t\n\t\t\t\t\t";
if(U_DOMAIN_EDIT && in_array($domain->Status, [1, 3, 4, 7])) {
    echo "\t\t\t\t\t<strong class=\"title2\">&nbsp;</strong>\n\t\t\t\t\t<span class=\"title2_value\"><a class=\"a1 c1 smallfont\" onclick=\"\$('#dialog_nameservers').dialog('open');\">";
    echo __("edit/sync nameservers");
    echo "</a></span>\n\t\t\t\t\t";
}
echo "\t\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t\n\t\t\t<!--left-->\n\t\t\t</div>\n\t\t\t<!--left-->\n\t\t\t\n\t\t\t<!--right-->\n\t\t\t<div class=\"right\">\n\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
echo __("handles");
echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t";
$handle_types = ["Owner", "Admin", "Tech"];
foreach ($handle_types as $handle_type) {
    $tmp_handle = isset($domain->Handles->{$handle_type}) ? $domain->Handles->{$handle_type} : NULL;
    echo "\t\t\t\t\t\t<div class=\"contact_placeholder\">\n\t\t\t\t\t\t\t<strong class=\"title2_sub\" style=\"text-align:right;color:#222\">";
    echo __("domain " . strtolower($handle_type) . " handle");
    echo "</strong>\n\t\t\t\t\t\t\t";
    if(!isset($tmp_handle->Handle) || !$tmp_handle->Handle) {
        echo "\t\t\t\t\t\t\t\t<span class=\"title2_sub_value c3\">";
        echo __("could not found handle in software");
        echo "</span>\n\t\t\t\t\t\t\t";
    } elseif($handle_type != "Owner" && $domain->Handles->Owner->Handle == $tmp_handle->Handle) {
        echo "\t\t\t\t\t\t\t\t<span class=\"title2_sub_value\"><a href=\"handles.php?page=show&amp;id=";
        echo $domain->ownerHandle;
        echo "\" class=\"a1 c1\">";
        echo $tmp_handle->Handle;
        echo "</a></span>\n\t\t\t\t\t\t\t\t<div class=\"title2_sub align_right\">&nbsp;</div>\n\t\t\t\t\t\t\t\t<div class=\"title2_sub_value\">";
        echo __("same handle as owner");
        echo "</div>\n\t\t\t\t\t\t\t";
    } else {
        echo "\t\t\t\t\t\t\t\t<span class=\"title2_sub_value\"><a href=\"handles.php?page=show&amp;id=";
        echo $tmp_handle->Identifier;
        echo "\" class=\"a1 c1\">";
        echo $tmp_handle->Handle;
        echo "</a>   <a class=\"a1 contact_toggler smallfont floatr\" style=\"color:#ccc !important;\">";
        echo __("toggle handle");
        echo "</a></span>\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t";
        if($tmp_handle->RegistrarHandle && $tmp_handle->RegistrarHandle != $tmp_handle->Handle) {
            echo "\t\t\t\t\t\t\t\t<div class=\"title2_sub align_right\">";
            echo __("registrarhandle");
            echo "</div>\n\t\t\t\t\t\t\t\t<div class=\"title2_sub_value\">";
            echo $tmp_handle->RegistrarHandle;
            echo "</div>\n\t\t\t\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t";
        if($tmp_handle->CompanyName) {
            echo "\t\t\t\t\t\t\t\t<div class=\"title2_sub align_right\">";
            echo __("companyname");
            echo "</div>\n\t\t\t\t\t\t\t\t<div class=\"title2_sub_value\">";
            echo $tmp_handle->CompanyName;
            echo "</div>\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<div class=\"title2_sub hide align_right\">";
            echo __("company number");
            echo "</div>\n\t\t\t\t\t\t\t\t<div class=\"title2_sub_value hide\">";
            if($tmp_handle->CompanyNumber) {
                echo $tmp_handle->CompanyNumber;
            } else {
                echo "-";
            }
            echo "</div>\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<div class=\"title2_sub hide align_right\">";
            echo __("taxnumber");
            echo "</div>\n\t\t\t\t\t\t\t\t<div class=\"title2_sub_value hide\">";
            if($tmp_handle->TaxNumber) {
                echo $tmp_handle->TaxNumber;
            } else {
                echo "-";
            }
            echo "</div>\n\t\t\t\t\t\t\t\t\n                                ";
            if(!empty($array_legaltype)) {
                echo "\t\t\t\t\t\t\t\t<div class=\"title2_sub hide align_right\">";
                echo __("legal form");
                echo "</div>\n\t\t\t\t\t\t\t\t<div class=\"title2_sub_value hide\">";
                if($tmp_handle->LegalForm) {
                    echo $array_legaltype[$tmp_handle->LegalForm];
                } else {
                    echo "-";
                }
                echo "</div>\n\t\t\t\t\t\t\t\t";
            }
            echo "                                ";
        }
        echo "\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<div class=\"title2_sub align_right\">";
        echo __("contact person");
        echo "</div>\n\t\t\t\t\t\t\t\t<div class=\"title2_sub_value\">";
        if($tmp_handle->Initials . $tmp_handle->SurName) {
            echo $tmp_handle->Initials . " " . $tmp_handle->SurName;
        } else {
            echo "-";
        }
        echo "</div>\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<div class=\"title2_sub align_right\">";
        echo __("address");
        echo "</div>\n\t\t\t\t\t\t\t\t<div class=\"title2_sub_value\">";
        if($tmp_handle->Address) {
            echo $tmp_handle->Address;
        } else {
            echo "-";
        }
        echo "</div>\n\t\t\t\t\t\t\t\t";
        if(IS_INTERNATIONAL && $tmp_handle->Address && $tmp_handle->Address2) {
            echo "<div class=\"title2_sub align_right\">&nbsp;</div><div class=\"title2_sub_value\">";
            echo $tmp_handle->Address2;
            echo "</div>";
        }
        echo "\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<div class=\"title2_sub align_right\">";
        echo __("zipcode and city");
        echo "</div>\n\t\t\t\t\t\t\t\t<div class=\"title2_sub_value\">";
        if($tmp_handle->ZipCode . $tmp_handle->City) {
            echo $tmp_handle->ZipCode . " " . $tmp_handle->City;
        } else {
            echo "-";
        }
        echo "</div>\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t";
        if(IS_INTERNATIONAL) {
            echo "\t\t\t\t\t\t\t\t\t<div class=\"title2_sub align_right\">";
            echo __("state");
            echo "</div>\n\t\t\t\t\t\t\t\t\t<div class=\"title2_sub_value\">";
            if($tmp_handle->StateName) {
                echo $tmp_handle->StateName;
            } else {
                echo "-";
            }
            echo "</div>\n\t\t\t\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<div class=\"title2_sub align_right\">";
        echo __("country");
        echo "</div>\n\t\t\t\t\t\t\t\t<div class=\"title2_sub_value\">";
        if($tmp_handle->Country) {
            echo $array_country[$tmp_handle->Country];
        } else {
            echo "-";
        }
        echo "</div>\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<div class=\"title2_sub hide align_right\">";
        echo __("emailaddress");
        echo "</div>\n\t\t\t\t\t\t\t\t<div class=\"title2_sub_value hide\">";
        if($tmp_handle->EmailAddress) {
            echo $tmp_handle->EmailAddress;
        } else {
            echo "-";
        }
        echo "</div>\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<div class=\"title2_sub hide align_right\">";
        echo __("phonenumber");
        echo "</div>\n\t\t\t\t\t\t\t\t<div class=\"title2_sub_value hide\">";
        if($tmp_handle->PhoneNumber) {
            echo phoneNumberLink($tmp_handle->PhoneNumber);
        } else {
            echo "-";
        }
        echo "</div>\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<div class=\"title2_sub hide align_right\">";
        echo __("faxnumber");
        echo "</div>\n\t\t\t\t\t\t\t\t<div class=\"title2_sub_value hide\">";
        if($tmp_handle->FaxNumber) {
            echo $tmp_handle->FaxNumber;
        } else {
            echo "-";
        }
        echo "</div>\n\t\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t</div>\n\t\t\t\t\t\t";
    if($handle_type != "Tech") {
        echo "<br />";
    }
}
echo "\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t\n\t\t\t<!--right-->\n\t\t\t</div>\n\t\t\t<!--right-->\n\t\t\t\n\t\t<!--split2-->\n\t\t</div>\n\t\t<!--split2-->\n\t\t\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\t\n\t<!--content-->\n\t<div class=\"content\" id=\"tab-domain-subscription\">\n\t<!--content-->\n\t\n\t\t";
if(0 < $domain->PeriodicID && is_object($domain->Periodic)) {
    require_once "views/elements/subscription.tab.php";
    $options = ["product_id" => $domain->Product];
    show_subscription_tab($domain->Periodic, $options);
} else {
    echo "\t\t\n\t\t\t";
    echo __("this domain name is not charged");
    echo "\t\t\n\t\t";
}
echo "\t\t\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n    \n    ";
if(U_INVOICE_SHOW && isset($domain->PeriodicID) && 0 < $domain->PeriodicID && isset($invoice_table_options)) {
    echo "            <div class=\"content\" id=\"tab-invoices\">\n            \n            \t<p>";
    echo __("here you can find an overview of invoices for this service subscription");
    echo "</p>\n            \n            \t";
    $invoice_table_options["hide_cols"] = ["Debtor", "subtr"];
    $invoice_table_options["parameters"]["searchat"] = "PeriodicID";
    $invoice_table_options["parameters"]["searchfor"] = $domain->PeriodicID;
    $invoice_table_options["redirect_url"] = "domains.php?page=show&id=" . $domain->id . "#tab-invoices";
    generate_table("list_invoice_domain", $invoice_table_options);
    echo "            </div>\n            ";
}
echo "\n\t<!--content-->\n\t<div class=\"content\" id=\"tab-domain-comment\">\n\t<!--content-->\n\t\t\n\t\t<form name=\"domain_comment_form\" method=\"post\" action=\"domains.php?page=show&amp;id=";
echo $domain->Identifier;
echo "\">\n\t\t\n\t\t<!--box3-->\n\t\t<div class=\"box3\"><h3>";
echo __("internal note");
echo "</h3><div class=\"content lineheight2\">\n\t\t<!--box3-->\n\t\t\t\n\t\t\t<textarea name=\"Comment\" class=\"text1 size5 autogrow\">";
echo $domain->Comment;
echo "</textarea>\n\t\t\t\n\t\t\t";
if(U_DOMAIN_EDIT) {
    echo "\t\t\t<br clear=\"both\" /><br />\n\t\t\t<p><a class=\"button1 alt1 float_left\" onclick=\"\$('form[name=domain_comment_form]').submit();\"><span>";
    echo __("save comment");
    echo "</span></a></p>\n\t\t\t<br clear=\"both\" />\n\t\t\t";
}
echo "\n\t\t<!--box3-->\n\t\t</div></div>\n\t\t<!--box3-->\n\t\t\n\t\t</form>\n\t\t\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\t\n\t<!--content-->\n\t<div class=\"content\" id=\"tab-domain-logfile\">\n\t<!--content-->\n\t\t\n\t\t";
require_once "views/elements/log.table.php";
$options = ["form_action" => "domains.php?page=show&amp;id=" . $domain->Identifier, "session_name" => "domain.show.logfile", "current_page" => $current_page, "current_page_url" => $current_page_url, "allow_delete" => U_DOMAIN_DELETE];
show_log_table($list_domain_logfile, $options);
echo "\t\n\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\t\n\t";
if(isset($domain->ExtraFields) && is_array($domain->ExtraFields) && 0 < count($domain->ExtraFields)) {
    echo "\t    <!--content-->\n\t\t<div class=\"content\" id=\"tab-domain-extra\">\n\t\t<!--content-->\n\t    \n\t\t    <!--box3-->\n\t\t\t<div class=\"box3\"><h3>";
    echo __("extra domain data");
    echo "</h3><div class=\"content lineheight2\">\n\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t";
    foreach ($domain->ExtraFields as $field) {
        echo "\t\t\t\t\t<strong class=\"title2\" style=\"width:200px;\">";
        echo $field["LabelTitle"];
        echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
        echo $field["LabelType"] == "options" ? $field["LabelOptions"][$field["Value"]] : $field["Value"];
        echo "</span>\n\t\t\t\t";
    }
    echo "\t\t\t\t\n\t\t\t<!--box3-->\n\t\t\t</div></div>\n\t\t\t<!--box3-->\n\t\t\t    \n\t    <!--content-->\n\t\t</div>\n\t\t<!--content-->\n    ";
}
echo "\n<!--box1-->\n</div>\n<!--box1-->\n\n\t<br />\n\t\t\t\n\t<!--buttonbar-->\n\t<div class=\"buttonbar\">\n\t<!--buttonbar-->\n\t\n\t\t";
if(U_DOMAIN_EDIT && 0 < $domain->Debtor) {
    echo "\t\t\t<p class=\"pos1\">\n\t\t\t\t<a class=\"button1 edit_icon\" href=\"services.php?page=edit&amp;domain_id=";
    echo $domain->Identifier;
    echo "\"><span>";
    echo __("edit");
    echo "</span></a>\n\t\t\t</p>\n\t\t\t";
}
if(U_DOMAIN_DELETE) {
    echo "\t\t\t<p class=\"pos2\">\n\t\t\t\t<a class=\"a1 c1\" onclick=\"\$('#delete_domain').dialog('open');\"><span>";
    echo __("delete");
    echo "</span></a>\n\t\t\t\t";
    if(!in_array($domain->Status, ["-1", "8", "9"])) {
        service_termination_function("domain", $domain_id, false, rewrite_date_site2db($domain->ExpirationDate));
    }
    echo "\t\t\t</p>\n\t\t\t";
}
echo "\t\n\t<!--buttonbar-->\n\t</div>\n\t<!--buttonbar-->\n\t\n\t<br />\n\n";
if(U_DOMAIN_EDIT) {
    echo "\t<div id=\"dialog_update_whois\" class=\"hide\" title=\"";
    echo __("dialog update whois title");
    echo "\">\n\t<form name=\"update_whois\" method=\"post\" action=\"domains.php?page=updatewhois&amp;id=";
    echo $domain_id;
    echo "\">\n\t<strong>";
    echo __("how do you want to update the WHOIS data");
    echo "</strong><br />\n\t<input type=\"radio\" id=\"action_contacts\" name=\"action\" value=\"contacts\" checked=\"checked\" onclick=\"\$('#dialog_update_whois_ownerchangecost_div').slideUp();\"/> <label for=\"action_contacts\">";
    echo __("change handles for domain");
    echo "</label><br />\n\t<input type=\"radio\" id=\"action_sync\" name=\"action\" value=\"sync\" onclick=\"\$('#dialog_update_whois_ownerchangecost_div').slideDown();\"/> <label for=\"action_sync\">";
    echo sprintf(__("sync WHOIS data to registrar"), $domain->Name);
    echo "</label><br />\n\t<br />\n\t\n\t";
    if($domain->OwnerChangeCostLabel) {
        echo "\t<div id=\"dialog_update_whois_ownerchangecost_div\" class=\"hide\">\n\t\t<strong>";
        echo __("cost domain ownerchange");
        echo "</strong><br />\n\t\t";
        echo __("cost domain ownerchange ask 1");
        echo "<br />\n\t\t<br />\n\t\t<input type=\"checkbox\" id=\"costs_billing_checkbox\" name=\"Costs_Billing\" value=\"yes\" checked=\"checked\"/> <label for=\"costs_billing_checkbox\">";
        echo sprintf(__("cost domain ownerchange ask 2"), $domain->OwnerChangeCostLabel);
        echo "</label><br /><br />\n\t</div>\n\t";
    }
    echo "\t\n\n\t<p><a class=\"button1 alt1 float_left\" id=\"dialog_update_whois_btn\"><span>";
    echo __("further");
    echo "</span></a></p>\n\t<p><a class=\"a1 c1 alt1 float_right\" onclick=\"\$('#dialog_update_whois').dialog('close');\"><span>";
    echo __("cancel");
    echo "</span></a></p>\n\t<br clear=\"all\" /><br />\n\t<font class=\"fontsmall\">";
    echo __("or do you want to change a contact");
    echo ": <a href=\"handles.php?page=edit&amp;id=";
    echo $domain->ownerHandle;
    echo "\" class=\"a1 c1\">";
    echo __("domain handle owner list");
    echo "</a>, <a href=\"handles.php?page=edit&amp;id=";
    echo $domain->adminHandle;
    echo "\" class=\"a1 c1\">";
    echo __("domain handle admin list");
    echo "</a> ";
    echo __("or");
    echo " <a href=\"handles.php?page=edit&amp;id=";
    echo $domain->techHandle;
    echo "\" class=\"a1 c1\">";
    echo __("domain handle tech list");
    echo "</a></font><br />\n\t\n\t</form>\n\t</div>\n";
}
echo "\n";
if(U_DOMAIN_EDIT) {
    echo "\t<div id=\"dialog_nameservers\" class=\"hide\" title=\"";
    echo sprintf(__("dialog change nameservers title"), $domain->Domain . "." . $domain->Tld);
    echo "\">\n\t<form name=\"updatens_form\" method=\"post\" action=\"domains.php?page=show&amp;id=";
    echo $domain_id;
    echo "&action=updatens\">\n\n\t";
    echo $domain->Status == 4 || $domain->Status == 8 ? __("change or update nameservers to registrar active domain") : __("change or update nameservers to registrar nonactive domain");
    echo "<br />\n\n\t<table width=\"100%\">\n    ";
    if(in_array($domain->Status, [1, 3, 4, 7]) && is_module_active("dnsmanagement")) {
        global $_module_instances;
        $dnsmanagement = $_module_instances["dnsmanagement"];
        echo $dnsmanagement->page_domain_ns_dialog($domain);
    }
    echo "\t<tr>\n\t<th>&nbsp;</td>\n\t<th align=\"left\">";
    echo __("hostname");
    echo ":</td>\n\t<th align=\"left\">";
    echo __("ip address");
    echo ":</td>\n\t</tr>\n\t<tr>\n\t<td><strong>";
    echo __("nameserver 1");
    echo ":</td>\n\t<td><input type=\"text\" name=\"ns1\" value=\"";
    echo $domain->DNS1;
    echo "\" class=\"text1 size1\" /></td>\n\t<td><input type=\"text\" name=\"ip1\" tabindex=\"-1\" value=\"";
    echo $domain->DNS1IP;
    echo "\" class=\"text1 size7\" /></td>\n\t</tr>\n\t<tr>\n\t<td><strong>";
    echo __("nameserver 2");
    echo ":</td>\n\t<td><input type=\"text\" name=\"ns2\" value=\"";
    echo $domain->DNS2;
    echo "\" class=\"text1 size1\" /></td>\n\t<td><input type=\"text\" name=\"ip2\" tabindex=\"-1\" value=\"";
    echo $domain->DNS2IP;
    echo "\" class=\"text1 size7\" /></td>\n\t</tr>\n\t<tr>\n\t<td><strong>";
    echo __("nameserver 3");
    echo ":</td>\n\t<td><input type=\"text\" name=\"ns3\" value=\"";
    echo $domain->DNS3;
    echo "\" class=\"text1 size1\" /></td>\n\t<td><input type=\"text\" name=\"ip3\" tabindex=\"-1\" value=\"";
    echo $domain->DNS3IP;
    echo "\" class=\"text1 size7\" /></td>\n\t</tr>\n\t</table>\n\t<br />\n\t<br />\n\t<div class=\"buttonbar\">\n        <p class=\"pos1\">\n            <a class=\"button1 alt1\" onclick=\"\$(this).siblings('span').show(); \$(this).hide(); \$('form[name=updatens_form]').submit();\">\n                <span>";
    echo __("dialog domain change nameserver button");
    echo "</span>\n            </a>\n            <span class=\"hide\">\n    \t\t\t<img src=\"images/icon_circle_loader_green.gif\" alt=\"\" class=\"ico inline\" style=\"margin-bottom:-6px;\" />&nbsp;&nbsp;\n    \t\t\t<span class=\"loading_green\">";
    echo __("loading");
    echo "</span>\n    \t\t</span>\n        </p>\n    \t<p class=\"pos2\">\n            <a class=\"a1 c1 alt1\" onclick=\"\$('#dialog_nameservers').dialog('close');\">\n                <span>";
    echo __("cancel");
    echo "</span>\n            </a>\n        </p>\n    </div>    \n\t</form>\n\t</div>\n";
}
echo "\n";
if(U_DOMAIN_DELETE) {
    echo "<div id=\"delete_domain\" class=\"hide ";
    if(isset($pagetype) && $pagetype == "confirmDelete") {
        echo "autoopen";
    }
    echo "\" title=\"";
    echo __("deletedialog domain title");
    echo "\">\n\t<form id=\"DomainForm\" name=\"form_delete\" method=\"post\" action=\"domains.php?page=delete\">\n\t<input type=\"hidden\" name=\"id\" value=\"";
    echo $domain->Identifier;
    echo "\"/>\n\t";
    echo sprintf(__("deletedialog domain description"), $domain->Domain . "." . $domain->Tld);
    echo "<br /><br />\n\n    ";
    if($domain->Status == 4) {
        echo "\t\t<strong>";
        echo __("domain at registrar");
        echo ":</strong><br />\n\t\t<input type=\"radio\" id=\"confirmregistrar_direct\" name=\"confirmRegistrar\" value=\"direct\" ";
        if(isset($_SESSION["ActionLog"]["Domain"]["forAll"]["confirmRegistrar"]) && $_SESSION["ActionLog"]["Domain"]["forAll"]["confirmRegistrar"] == "direct" || !isset($_SESSION["ActionLog"]["Domain"]["forAll"]["confirmRegistrar"]) && (!isset($_GET["confirmRegistrar"]) || $_GET["confirmRegistrar"] == "direct")) {
            echo "checked=\"checked\" ";
        }
        echo "/> <label for=\"confirmregistrar_direct\">";
        echo __("delete domain at registrar");
        echo "</label><br />\n\t\t<input type=\"radio\" id=\"confirmregistrar_norenew\" name=\"confirmRegistrar\" value=\"norenew\" ";
        if(isset($_SESSION["ActionLog"]["Domain"]["forAll"]["confirmRegistrar"]) && $_SESSION["ActionLog"]["Domain"]["forAll"]["confirmRegistrar"] == "norenew" || !isset($_SESSION["ActionLog"]["Domain"]["forAll"]["confirmRegistrar"]) && isset($_GET["confirmRegistrar"]) && $_GET["confirmRegistrar"] == "norenew") {
            echo "checked=\"checked\" ";
        }
        echo "/> <label for=\"confirmregistrar_norenew\">";
        echo __("do not extend domain at registrar");
        echo "</label><br />\n\t\t<input type=\"radio\" id=\"confirmregistrar_none\" name=\"confirmRegistrar\" value=\"none\" ";
        if(isset($_SESSION["ActionLog"]["Domain"]["forAll"]["confirmRegistrar"]) && $_SESSION["ActionLog"]["Domain"]["forAll"]["confirmRegistrar"] == "none" || !isset($_SESSION["ActionLog"]["Domain"]["forAll"]["confirmRegistrar"]) && isset($_GET["confirmRegistrar"]) && $_GET["confirmRegistrar"] == "none") {
            echo "checked=\"checked\" ";
        }
        echo "/> <label for=\"confirmregistrar_none\">";
        echo __("do nothing with domain at registrar");
        echo "</label><br />\n        <br />\n    ";
    }
    echo "  \n    \t\n\t<input type=\"checkbox\" id=\"imsure\" name=\"imsure\" value=\"yes\"/> <label for=\"imsure\">";
    echo __("delete this domain");
    echo "</label><br />\n\t<br />\n\t<p><a id=\"delete_domain_btn\" class=\"button2 alt1 float_left\"><span>";
    echo __("delete");
    echo "</span></a></p>\n\t<p><a class=\"a1 c1 alt1 float_right\" onclick=\"\$('#delete_domain').dialog('close');\"><span>";
    echo __("cancel");
    echo "</span></a></p>\n\t\n\t";
    if(isset($_SESSION["ActionLog"]) && isset($_SESSION["ActionLog"]["Domain"]["delete"]) && is_array($_SESSION["ActionLog"]["Domain"]["delete"]) && 1 < count($_SESSION["ActionLog"]["Domain"]["delete"])) {
        echo "\t\t\t<br class=\"clear\"/><br />\n\t\t\t<strong>";
        echo sprintf(__("batch remove all other domains"), count($_SESSION["ActionLog"]["Domain"]["delete"]) - 1);
        echo "</strong><br />\n\t\t\t<label style=\"display:block;margin: 2px 0 5px;\"><input type=\"checkbox\" id=\"forAll\" name=\"forAll\" value=\"yes\" ";
        if(isset($_SESSION["ActionLog"]["Domain"]["forAll"]["check"]) && $_SESSION["ActionLog"]["Domain"]["forAll"]["check"] === true) {
            echo "checked=\"checked\" ";
        }
        echo " /> ";
        echo __("batch after this domain directly remove the others");
        echo "</label>\n\t";
    } elseif(isset($_SESSION["ActionLog"]) && isset($_SESSION["ActionLog"]["Subscriptions"]["type"]["domain"]["delete"]) && is_array($_SESSION["ActionLog"]["Subscriptions"]["type"]["domain"]["delete"]) && 1 < count($_SESSION["ActionLog"]["Subscriptions"]["type"]["domain"]["delete"])) {
        echo "\t\t<br class=\"clear\"/><br />\n\t\t<strong>";
        echo sprintf(__("batch remove all other domains"), count($_SESSION["ActionLog"]["Subscriptions"]["type"]["domain"]["delete"]) - 1);
        echo "</strong><br />\n\t\t<label style=\"display:block;margin: 2px 0 5px;\"><input type=\"checkbox\" id=\"forAll\" name=\"forAll\" value=\"yes\" ";
        if(isset($_SESSION["ActionLog"]["Subscriptions"]["type"]["domain"]["forAll"]["check"]) && $_SESSION["ActionLog"]["Subscriptions"]["type"]["domain"]["forAll"]["check"] === true) {
            echo "checked=\"checked\" ";
        }
        echo " /> ";
        echo __("batch after this domain directly remove the others");
        echo "</label>\n\t";
    }
    echo "\t\n\t\n\t</form>\n</div>\n";
}
echo "\n";
if(isset($_GET["action"]) && $_GET["action"] == "startregister" && ($domain->Status == 1 || $domain->Status == 3 || $domain->Status == 7)) {
    echo "\t<script language=\"javascript\" type=\"text/javascript\">\n\t\$(function(){\n\t\t\$('#dialog').dialog('open');\n\t\t\$.post(\"XMLRequest.php\", { action: \"create_domain\", id: ";
    echo $domain->Identifier;
    echo "},\n\t\tfunction(data){\n\t\t\t\n\t\t\t";
    if(isset($_SESSION["redirect_after_register"])) {
        echo "\t\t\t\tif(data == 'OK'){\n\t\t\t\t\tlocation.href = '";
        echo $_SESSION["redirect_after_register"];
        echo "';\n\t\t\t\t\treturn true;\n\t\t\t\t}\n\t\t\t\t\n\t\t\t\t";
        unset($_SESSION["redirect_after_register"]);
    }
    echo "\t\t\t\n\t\t\tlocation.href = '?page=show&id=";
    echo $domain->Identifier;
    echo "';\n\t\t\t\n\t\t}, \"html\");\n\t});\n\t</script>";
}
echo "\n";
if(isset($selected_tab) && $selected_tab) {
    echo "<script language=\"javascript\" type=\"text/javascript\">\n\$(function(){\n\t\$('.tabs').tabs(\"option\", \"active\", ";
    echo $selected_tab;
    echo ");\n});\n</script>\n";
}
echo "\n";
require_once "views/footer.php";

?>