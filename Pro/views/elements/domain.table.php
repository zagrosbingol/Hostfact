<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
function show_domain_table($data_array, $options = [])
{
    global $debtor;
    $table_type = isset($options["table_type"]) ? $options["table_type"] : "subtable";
    $session_name = isset($options["session_name"]) ? $options["session_name"] : "";
    $current_page = isset($options["current_page"]) ? $options["current_page"] : "";
    $current_page_url = isset($options["current_page_url"]) ? $options["current_page_url"] : "";
    $redirect_page = isset($options["redirect_page"]) ? $options["redirect_page"] : "";
    $redirect_id = isset($options["redirect_id"]) ? $options["redirect_id"] : "";
    $hide_columns = isset($options["hide_columns"]) ? $options["hide_columns"] : [];
    $column_count = 6 - count($hide_columns);
    $session = $_SESSION[$session_name];
    global $array_domainstatus;
    global $array_periodic;
    global $array_taxpercentages;
    echo "\t<form id=\"DomainForm\" name=\"form_domains\" method=\"post\" action=\"domains.php?page=view";
    if($redirect_page) {
        echo "&amp;from_page=" . $redirect_page;
    }
    if($redirect_id) {
        echo "&amp;from_id=" . $redirect_id;
    }
    echo "\"><fieldset>\t\n\t\t\t\t\t\n\t<div id=\"SubTable_Domains\">\t\n\t\t<table id=\"MainTable_Domains\" class=\"table1\" cellpadding=\"0\" cellspacing=\"0\">\n\t\t<tr class=\"trtitle\">\n\t\t\t<th scope=\"col\"><label><input name=\"DomainBatch\" class=\"BatchCheck\" type=\"checkbox\" value=\"on\" /></label> <a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','Domain','Domains','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 pointer ";
    if($session["sort"] == "Domain") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\" style=\"padding-left:18px\">";
    echo __("domain");
    echo "</a></th>\n\t\t\t";
    if(!in_array("Debtor", $hide_columns)) {
        echo "<th scope=\"col\"><a onclick=\"ajaxSave('";
        echo $session_name;
        echo "','sort','Debtor','Domains','";
        echo $current_page_url;
        echo "');\" class=\"ico set2 pointer ";
        if($session["sort"] == "Debtor") {
            if($session["order"] == "ASC") {
                echo "arrowup";
            } else {
                echo "arrowdown";
            }
        } else {
            echo "arrowhover";
        }
        echo "\">";
        echo __("debtor");
        echo "</a></th>";
    }
    echo "\t\t\t<th scope=\"col\" style=\"width: 120px;\" class=\"show_col_ws\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','RegistrationDate','Domains','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 pointer ";
    if($session["sort"] == "RegistrationDate") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("registered");
    echo "</a></th>\n\t\t\t<th scope=\"col\" style=\"width: 120px;\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','ExpirationDate','Domains','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 pointer ";
    if($session["sort"] == "ExpirationDate") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("expires at");
    echo "</a></th>\n\t\t\t";
    if(!in_array("Registrar", $hide_columns)) {
        echo "<th scope=\"col\" style=\"width: 120px;\"><a onclick=\"ajaxSave('";
        echo $session_name;
        echo "','sort','Registrar','Domains','";
        echo $current_page_url;
        echo "');\" class=\"ico set2 pointer ";
        if($session["sort"] == "Registrar") {
            if($session["order"] == "ASC") {
                echo "arrowup";
            } else {
                echo "arrowdown";
            }
        } else {
            echo "arrowhover";
        }
        echo "\">";
        echo __("registrar");
        echo "</a></th>";
    }
    echo "\t\t\t<th scope=\"col\" style=\"width: 30px;\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','PeriodicID','Domains','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 pointer ";
    if($session["sort"] == "PeriodicID") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("subscription abbr");
    echo "</a></th>\n\t\t</tr>\n\t\t";
    $domainCounter = 0;
    foreach ($data_array as $domainID => $domain) {
        if(is_numeric($domainID)) {
            $domainCounter++;
            echo "\t\t<tr class=\"hover_extra_info ";
            if($domainCounter % 2 === 1) {
                echo "tr1";
            }
            echo "\">\n\t\t\t<td style=\"vertical-align: top;\">\n\t\t\t\t<span style=\"width: 43px;display: block;float: left;\">\n\t\t\t\t\t<input name=\"domains[]\" type=\"checkbox\" class=\"DomainBatch\" value=\"";
            echo $domainID;
            echo "\" /> \n\t\t\t\t\t";
            switch ($domain["Status"]) {
                case "4":
                    echo "<span class=\"inline_status active infopopuptop delaypopup\">&nbsp;<span class=\"popup\">";
                    echo $array_domainstatus[$domain["Status"]];
                    echo "<b></b></span></span>";
                    break;
                case "3":
                case "6":
                    echo "<span class=\"inline_status busy infopopuptop delaypopup\">&nbsp;<span class=\"popup\">";
                    echo $array_domainstatus[$domain["Status"]];
                    echo "<b></b></span></span>";
                    break;
                case "7":
                    echo "<span class=\"inline_status error infopopuptop delaypopup\">&nbsp;<span class=\"popup\">";
                    echo $array_domainstatus[$domain["Status"]];
                    echo "<b></b></span></span>";
                    break;
                default:
                    echo "<span class=\"inline_status deleted infopopuptop delaypopup\">&nbsp;<span class=\"popup\">";
                    echo $array_domainstatus[$domain["Status"]];
                    echo "<b></b></span></span>";
                    echo "\t\t\t\t</span>\n\t\t\t\t<span style=\"word-break: break-all;display: block;margin-left: 44px;\">\n\t\t\t\t\t<a href=\"domains.php?page=show&id=";
                    echo $domainID;
                    echo "\" class=\"";
                    if($domain["Status"] == "7") {
                        echo "c3";
                    } else {
                        echo "c1";
                    }
                    echo " a1\" style=\"word-wrap: normal;\">";
                    echo $domain["Domain"] . "." . $domain["Tld"];
                    echo "</a>";
                    if($domain["RegistrationDate"] == "0000-00-00 00:00:00" && $domain["Status"] != 4) {
                        echo " <span class=\"fontsmall c4\">";
                        echo $domain["Type"] == "transfer" || $domain["AuthKey"] ? __("movement concerns") : __("registration concerns");
                        echo "</span>";
                    } elseif($domain["Status"] == 4 && 0 < $domain["TerminationID"]) {
                        echo " <span class=\"fontsmall c4\">";
                        echo "- " . __("subscription tab - terminated at x");
                        echo "</span>";
                    }
                    echo "\t\t\t\t</span>\n\t\t\t</td>\n\t\t\t";
                    if(!in_array("Debtor", $hide_columns)) {
                        echo "<td style=\"vertical-align: top;\">";
                        if(0 < $domain["Debtor"]) {
                            echo "<a href=\"debtors.php?page=show&amp;id=";
                            echo $domain["Debtor"];
                            echo "\" class=\"a1\">";
                            echo $domain["CompanyName"] ? $domain["CompanyName"] : $domain["SurName"] . ", " . $domain["Initials"];
                            echo "</a>";
                        } else {
                            echo __("new customer");
                        }
                        echo "</td>";
                    }
                    echo "\t\t\t";
                    if(0 < $domain["RegistrationDate"] || $domain["Status"] == 4) {
                        echo "\t\t\t<td style=\"width: 60px;vertical-align: top;\" class=\"show_col_ws\">";
                        echo rewrite_date_db2site($domain["RegistrationDate"]);
                        echo "</td>\n\t\t\t<td style=\"width: 60px;vertical-align: top;\">\n                <span class=\"inline_status ";
                        echo $domain["DomainAutoRenew"] == "on" ? "autorenew_on" : "autorenew_off";
                        echo " infopopuptop delaypopup\">&nbsp;\n                    <span class=\"popup\">";
                        echo $domain["DomainAutoRenew"] == "on" ? __("autorenew on") : __("autorenew off");
                        echo "</span>\n                </span>\n                ";
                        echo rewrite_date_db2site($domain["ExpirationDate"]);
                        echo "            </td>\n\t\t\t";
                    } else {
                        echo "\t\t\t<td style=\"width: 60px;vertical-align: top;\">&nbsp;</td>\n\t\t\t<td style=\"width: 60px;vertical-align: top;\" class=\"show_col_ws\">&nbsp;</td>\n\t\t\t";
                    }
                    echo "\t\t\t";
                    if(!in_array("Registrar", $hide_columns)) {
                        echo "<td style=\"vertical-align: top;\">";
                        if($domain["Registrar"]) {
                            echo "<a href=\"registrars.php?page=show&amp;id=";
                            echo $domain["Registrar"];
                            echo "\">";
                            echo $domain["Name"];
                            echo "</a>";
                        } else {
                            echo "-";
                        }
                        echo "</td>";
                    }
                    echo "\t\t\t<td style=\"vertical-align: top;\">";
                    echo show_subscription_column($domain);
                    echo "</td>\n\t\t</tr>\n\t\t";
            }
        }
    }
    if($domainCounter === 0) {
        echo "\t\t<tr>\n\t\t\t<td colspan=\"";
        echo $column_count;
        echo "\">\n\t\t\t\t";
        echo __("no results found");
        echo "\t\t\t</td>\n\t\t</tr>\n\t\t";
    } else {
        echo "\t\t<tr class=\"table_options\">\n\t\t\t<td colspan=\"";
        echo $column_count;
        echo "\">\n\t\t\t\t<p class=\"ico inline hook\">\n\t\t\t\t\t<select name=\"action\" class=\"select1 BatchSelect\">\n\t\t\t\t\t\t<option selected=\"selected\">";
        echo __("with selected");
        echo "</option>\n\t\t\t\t\t\t";
        if(U_DOMAIN_EDIT) {
            echo "\t\t\t\t\t\t\t<optgroup label=\"";
            echo __("optgroup label actions in software");
            echo "\">\n\t\t\t\t\t\t\t\t<option value=\"dialog:active\">";
            echo __("change status to active");
            echo "</option>\n\t\t\t\t\t\t\t\t<option value=\"dialog:extendDomain\">";
            echo __("move expiration date 1 year");
            echo "</option>\n    \t\t\t\t\t\t\t";
            if($session_name == "debtor.show.domain") {
                echo "\t                                <option value=\"dialog:changeDebtorDomain\">";
                echo __("move to debtor");
                echo "</option>\n\t                                ";
            }
            echo "    \t\t\t\t\t\t</optgroup>\n\t\t\t\t\t\t";
        }
        if(U_DOMAIN_EDIT || U_DOMAIN_DELETE) {
            echo "\t\t\t\t\t\t\t<optgroup label=\"";
            echo __("optgroup label actions to registrar");
            echo "\">\n\t\t    \t\t\t\t\t";
            if(U_DOMAIN_EDIT) {
                echo "\t\t\t\t\t\t\t\t\t<option value=\"dialog:registerDomain\">";
                echo __("register domain");
                echo "</option>\n\t\t    \t\t\t\t\t\t<option value=\"dialog:changeNameservers\">";
                echo __("change nameservers");
                echo "</option>\n\t\t    \t\t\t\t\t\t<option value=\"dialog:changeWHOIS\">";
                echo __("change whois");
                echo "</option>\n                                    <option value=\"dialog:syncDomains\">";
                echo __("batch sync domains with registrar");
                echo "</option>\n\t    \t\t\t\t\t\t\t";
            }
            if(U_DOMAIN_DELETE) {
                echo "\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t<option value=\"dialog:terminate_domain\">";
                echo __("terminate domain");
                echo "</option>\n\t    \t\t\t\t\t\t\t<option value=\"deleteDomain\">";
                echo __("delete domain");
                echo "</option>\n\t    \t\t\t\t\t\t\t";
            }
            echo "\t\t\t\t\t\t\t</optgroup>\n\t\t\t\t\t\t\t";
        }
        echo "\t\t\t\t\t</select>\n\t\t\t\t\t\n\t\t\t\t\t";
        if(U_DOMAIN_EDIT) {
            echo "        \t\t\t\t\t<div class=\"hide\" id=\"dialog_active\" title=\"";
            echo __("batch dialog active domain title");
            echo "\">\n        \t\t\t\t\t\t<strong>";
            echo __("confirm action");
            echo "</strong><br />\n        \t\t\t\t\t\t";
            echo __("batch dialog active domain");
            echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\n        \t\t\t\t\t</div>\n        \t\t\t\t\t\n        \t\t\t\t\t<div class=\"hide\" id=\"dialog_extendDomain\" title=\"";
            echo __("move expiration date 1 year");
            echo "\">\n        \t\t\t\t\t\t<strong>";
            echo __("confirm action");
            echo "</strong><br />\n        \t\t\t\t\t\t";
            echo __("batch dialog extend domain");
            echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\n        \t\t\t\t\t</div>\n        \t\t\t\t\t\n        \t\t\t\t\t<div class=\"hide\" id=\"dialog_registerDomain\" title=\"";
            echo __("register domain");
            echo "\">\n        \t\t\t\t\t\t<strong>";
            echo __("confirm action");
            echo "</strong><br />\n        \t\t\t\t\t\t";
            echo __("batch dialog register domain");
            echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\n        \t\t\t\t\t</div>\n        \t\t\t\t\t\n        \t\t\t\t\t<div class=\"hide\" id=\"dialog_changeNameservers\" title=\"";
            echo __("change nameservers");
            echo "\">\n        \t\t\t\t\t\t\n                                <strong>";
            echo __("confirm action");
            echo "</strong>:<br />\n        \t\t\t\t\t\t";
            echo __("batch dialog change nameservers");
            echo "<br />\n        \n        \t\t\t\t\t\t<table width=\"100%\">\n                                ";
            if(is_module_active("dnsmanagement")) {
                global $_module_instances;
                $dnsmanagement = $_module_instances["dnsmanagement"];
                echo $dnsmanagement->page_domain_ns_dialog();
            }
            echo "        \t\t\t\t\t\t<tr>\n        \t\t\t\t\t\t\t<th>&nbsp;</td>\n        \t\t\t\t\t\t\t<th align=\"left\">";
            echo __("hostname");
            echo ":</th>\n        \t\t\t\t\t\t</tr>\n        \t\t\t\t\t\t<tr>\n        \t\t\t\t\t\t\t<td><strong>";
            echo __("nameserver 1");
            echo ":</strong></td>\n        \t\t\t\t\t\t\t<td><input type=\"text\" name=\"ns1\" value=\"\" class=\"text1 size1\" /></td>\n        \t\t\t\t\t\t</tr>\n        \t\t\t\t\t\t<tr>\n        \t\t\t\t\t\t\t<td><strong>";
            echo __("nameserver 2");
            echo ":</strong></td>\n        \t\t\t\t\t\t\t<td><input type=\"text\" name=\"ns2\" value=\"\" class=\"text1 size1\" /></td>\n        \t\t\t\t\t\t</tr>\n        \t\t\t\t\t\t<tr>\n        \t\t\t\t\t\t\t<td><strong>";
            echo __("nameserver 3");
            echo ":</strong></td>\n        \t\t\t\t\t\t\t<td><input type=\"text\" name=\"ns3\" value=\"\" class=\"text1 size1\" /></td>\n        \t\t\t\t\t\t</tr>\n        \t\t\t\t\t\t</table>\n        \t\t\t\t\t\t<br />\n        \t\t\t\t\t\t<input type=\"checkbox\" id=\"imsure2\" name=\"imsure\" value=\"yes\"/> <label for=\"imsure2\">";
            echo __("i want to change nameservers for selected domains");
            echo "</label>\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\n        \t\t\t\t\t</div>\n                            \n                            <div class=\"hide\" id=\"dialog_changeWHOIS\" title=\"";
            echo __("change whois");
            echo "\">\n                                \n                                <input type=\"hidden\" name=\"page\" value=\"";
            echo $session_name;
            echo "\" />\n                                <input type=\"hidden\" name=\"debtor\" value=\"";
            echo $debtor->Identifier ?? "";
            echo "\" />\n        \t\t\t\t\t\t\n                                <strong>";
            echo __("confirm action");
            echo "</strong>:<br />\n        \t\t\t\t\t\t";
            echo __("batch dialog change whois");
            echo "<br />\n                                \n                                <div id=\"change_whois_registrar_contacts\">\n                                    <img src=\"images/icon_circle_loader_green.gif\" alt=\"\" class=\"ico inline\" />\n                                </div>\n        \t\t\t\t\t\t\n        \t\t\t\t\t\t<br />\n        \t\t\t\t\t\t<label>\n                                    <input type=\"checkbox\" name=\"imsure\" value=\"yes\"/>\n                                    ";
            echo __("i want to change whois for selected domains");
            echo "                                </label>\t\n                                \n                                <script language=\"javascript\" type=\"text/javascript\">\n                                    \$(function()\n                                    {\n                                        \$(document).on('change', '.BatchSelect', function()\n                                        {\n                                            if(\$(this).val() == \"dialog:changeWHOIS\")\n                                            {\n                                                \$( \"#batch_confirm\" ).dialog( \"option\", \"width\", 500 );\n                                                                                                \n                                                var domains = new Array();\n                                                \$(this).parents('form').find('input[name=\"domains[]\"]:checked').each(function() {\n                                                    domains.push(\$(this).val());     \n                                                });\n                                                var pagename = \$('#dialog_changeWHOIS input[name=\"page\"]').val();\n                                                var debtor_id = \$('#dialog_changeWHOIS input[name=\"debtor\"]').val();\n                                                \n                                                \$.post('XMLRequest.php', { action: 'create_dialog_change_whois', selected_domains: domains, page: pagename, debtor: debtor_id },function(data)\n                                                {\t\n                                                    \$('#batch_confirm #change_whois_registrar_contacts').html(data);\n                                                    \$('#change_whois_registrar_contacts').html(data);\n                                                    \$('#batch_confirm input[name=\"update_at_registrar\"]').prop('checked', true);\n                                            \t});\n                                             }\n                                        });\n                                    });\n                                </script>\n                                \t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\n        \t\t\t\t\t</div>\n                            \n                            <div class=\"hide\" id=\"dialog_syncDomains\" title=\"";
            echo __("dialog sync domains title");
            echo "\">\n        \t\t\t\t\t\t\n                                <strong>";
            echo __("confirm action");
            echo "</strong><br />\n                                <p>";
            echo __("batch dialog sync domains");
            echo "</p>                                \n\n                                <div class=\"domain_sync_loader hide\">\n                                    <br />\n                                    <img src=\"images/icon_circle_loader_green.gif\" alt=\"\" class=\"ico inline\" style=\"margin-bottom:-3px;\" /> &nbsp;\n                                    <span class=\"remaining\">0</span> ";
            echo __("of the");
            echo " <span class=\"total\">0</span> ";
            echo __("domains processed");
            echo "                                </div>\n                                \n                                <br />\n                                <div>\n                                    <a class=\"alt1 float_left button1 batch_confirm_sync\">\n                                        <span>";
            echo __("proceed");
            echo "</span>\n                                    </a>\n                                    \t\n                                \t<a class=\"a1 c1 float_right cancel\" onclick=\"\$('#batch_confirm').dialog('close');\">";
            echo __("cancel");
            echo "</a>\n                                </div>\n\n        \t\t\t\t\t</div>\n                            \n                            <script language=\"javascript\" type=\"text/javascript\">\n                                \$(function()\n                                {\n                                    \$(document).on('change', '.BatchSelect', function()\n                                    {\n                                        \n                                        if(\$(this).val() == \"dialog:syncDomains\")\n                                        {\n                                            // hide the default confirm/cancel buttons to use our own\n                                            \$('#batch_confirm_submit').parent('p').hide();\n                                        }\n                                    });\n                                    \n                                    var domains_to_sync = new Array();\n                                    var total_domains_to_sync = 0;\n\n                                    \$('#batch_confirm').off('click', '.batch_confirm_sync');\n                                    \$('#batch_confirm').on('click','.batch_confirm_sync', function()\n                                    {\n                                        if(\$('form#DomainForm').find('input[name=\"domains[]\"]:checked').length == 0)\n                                        {\n                                            \$('form#DomainForm').submit();\n                                            return;\n                                        }\n                                        \n                                        \$(this).parent('div').hide();\n                                        \$('#batch_confirm_text br').last().remove();\n                                        \$('#batch_confirm_text br').last().remove();\n\n\n                                        \$('form#DomainForm').find('input[name=\"domains[]\"]:checked').each(function() {\n                                            domains_to_sync.push(\$(this).val());     \n                                        });\n                                        \n                                        total_domains_to_sync = domains_to_sync.length;\n                                        \n                                        \$('#batch_confirm').find('.remaining').text('0');\n                                        \$('#batch_confirm').find('.total').text(total_domains_to_sync);\n                                        \$('#batch_confirm').find('.domain_sync_loader').show();\n                                        \n                                        batch_sync_domains();\n\n                                    });\n\n                                    // recursive function which performs an ajax call to sync domains per registrar, until all domains have been processed\n                                    function batch_sync_domains()\n                                    {\n                                        \$.post('XMLRequest.php', { action: 'batch_sync_domains', selected_domains: domains_to_sync },function(data)\n                                        {\n                                            var remainig_domains = new Array();\n                                            for(var key in data.remaining_domains)\n                                            {\n                                                remainig_domains.push(data.remaining_domains[key]);\n                                            }\n                                            domains_to_sync = remainig_domains;\n                                            \n                                            \$('#batch_confirm').find('.remaining').text(total_domains_to_sync - domains_to_sync.length);\n                                            \n                                            if(domains_to_sync.length > 0)\n                                            {\n                                                batch_sync_domains();\n                                            }\n                                            else\n                                            {\n                                                \$('form#DomainForm').submit();\n                                            }\n                                    \t}, 'json');\n                                    }\n\n                                });\n                            </script>\n\n\t\t\t\t\t       ";
        }
        echo "\t\t\t\t</p>\n\t\t\t\t\n\t\t\t\t";
        if(min(MIN_PAGINATION, $session["results"]) < $data_array["CountRows"]) {
            echo "\t\t\t\t\t<br />\n\t\t\t\t\t";
            ajax_paginate("Domains", isset($data_array["CountRows"]) ? $data_array["CountRows"] : 0, $session["results"], $current_page, $current_page_url);
            echo "\t\t\t\t";
        }
        echo "\t\t\t</td>\n\t\t</tr>\n\t\t";
    }
    echo "\t\t</table>\n\t</div>\n    \n    ";
    if(U_DEBTOR_EDIT && $session_name == "debtor.show.domain") {
        echo "            <div class=\"hide\" id=\"dialog_changeDebtorDomain\" title=\"";
        echo __("transfer services");
        echo "\">\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\n                ";
        require "views/dialog.change.debtor.php";
        echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\n            </div>\n            ";
    }
    echo "\t\n\t</fieldset></form>\n\t\n\t";
    if(U_DOMAIN_DELETE) {
        service_termination_batch_dialog("domain", "form_domains");
    }
    echo "\t";
}

?>