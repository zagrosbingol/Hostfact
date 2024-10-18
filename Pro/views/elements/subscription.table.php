<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
function show_subscription_table($data_array, $options = [])
{
    $table_name = isset($options["table_name"]) ? $options["table_name"] : "Subscriptions";
    $session_name = isset($options["session_name"]) ? $options["session_name"] : "";
    $current_page = isset($options["current_page"]) ? $options["current_page"] : "1";
    $current_page_url = isset($options["current_page_url"]) ? $options["current_page_url"] : "";
    $sidebar_template = isset($options["sidebar_template"]) ? $options["sidebar_template"] : "";
    $redirect_page = isset($options["redirect_page"]) ? $options["redirect_page"] : "";
    $redirect_id = isset($options["redirect_id"]) ? $options["redirect_id"] : "";
    $hide_columns = isset($options["hide_columns"]) ? $options["hide_columns"] : [];
    $column_count = (defined("SHOW_PRODUCTNAME") && SHOW_PRODUCTNAME === true ? 12 : 11) - count($hide_columns);
    $hide_actions = isset($options["hide_actions"]) ? $options["hide_actions"] : [];
    $session = $_SESSION[$session_name];
    global $array_producttypes;
    global $_module_instances;
    echo "\t\n\t\t<form id=\"DebtorSubscriptionForm\" name=\"form_";
    echo strtolower($table_name);
    echo "\" method=\"post\" action=\"subscriptions.php";
    if($sidebar_template) {
        echo "?sidebar=" . $sidebar_template;
    }
    if($redirect_page) {
        echo ($sidebar_template ? "&" : "?") . "from_page=" . $redirect_page;
    }
    if($redirect_id) {
        echo "&amp;from_id=" . $redirect_id;
    }
    echo "\"><fieldset>\n\t\t\t\t\t\n\t\t<div id=\"SubTable_";
    echo $table_name;
    echo "\">\t\n\t\t\t<table id=\"MainTable_";
    echo $table_name;
    echo "\" class=\"table1\" cellpadding=\"0\" cellspacing=\"0\">\n\t\t\t<tr class=\"trtitle\">\n\t\t\t\t<th scope=\"col\"><label><input name=\"";
    echo $table_name;
    echo "Batch\" class=\"BatchCheck\" type=\"checkbox\" value=\"on\" /></label> <a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','Description','";
    echo $table_name;
    echo "','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 pointer ";
    if($session["sort"] == "Description") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\" style=\"padding-left:18px;\">";
    echo __("description");
    echo "</a></th>\n\t\t\t\t\n\t\t\t\t";
    if(defined("SHOW_PRODUCTNAME") && SHOW_PRODUCTNAME === true) {
        echo "\t\t\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('";
        echo $session_name;
        echo "','sort','ProductName','";
        echo $table_name;
        echo "','";
        echo $current_page_url;
        echo "');\" class=\"ico set2 pointer ";
        if($session["sort"] == "ProductName") {
            if($session["order"] == "ASC") {
                echo "arrowup";
            } else {
                echo "arrowdown";
            }
        } else {
            echo "arrowhover";
        }
        echo "\">";
        echo __("product");
        echo "</a></th>\n\t\t\t\t";
    }
    echo "\t\t\t\t\n\t\t\t\t<th scope=\"col\" style=\"width: 80px;\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','PeriodicType','";
    echo $table_name;
    echo "','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 pointer ";
    if($session["sort"] == "PeriodicType") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("subscription type");
    echo "</a></th>\n\t\t\t\t";
    if(!in_array("Debtor", $hide_columns)) {
        echo "<th scope=\"col\"><a onclick=\"ajaxSave('";
        echo $session_name;
        echo "','sort','Debtor','";
        echo $table_name;
        echo "','";
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
    echo "\t\t\t\t<th scope=\"col\" colspan=\"3\" ";
    if(VAT_CALC_METHOD == "incl") {
        echo "class=\"show_col_ws\"";
    }
    echo "><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','PriceExcl','";
    echo $table_name;
    echo "','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 pointer ";
    if($session["sort"] == "PriceExcl") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("amountexcl");
    echo "</a></th>\n\t\t\t\t<th scope=\"col\" colspan=\"3\" ";
    if(VAT_CALC_METHOD == "excl") {
        echo "class=\"show_col_ws\"";
    }
    echo "><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','PriceIncl','";
    echo $table_name;
    echo "','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 pointer ";
    if($session["sort"] == "PriceIncl") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("amountincl");
    echo "</a></th>\n\t\t\t\t<th scope=\"col\" style=\"width: 90px;\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','NextDate','";
    echo $table_name;
    echo "','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 pointer ";
    if($session["sort"] == "NextDate") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("next invoice date");
    echo "</a></th>\n\t\t\t\t<th scope=\"col\" style=\"width: 150px;\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','StartPeriod','";
    echo $table_name;
    echo "','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 pointer ";
    if($session["sort"] == "StartPeriod") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("period to invoice");
    echo "</a></th>\n\t\t\t</tr>\n\t\t\t";
    $subscriptionCounter = 0;
    foreach ($data_array as $subscriptionID => $subscription) {
        if(is_numeric($subscriptionID)) {
            $subscriptionCounter++;
            echo "\t\t\t<tr class=\"hover_extra_info ";
            if($subscriptionCounter % 2 === 1) {
                echo "tr1";
            }
            echo "\">\n\t\t\t\t<td style=\"vertical-align: top;\">\n\t\t\t\t\t<span style=\"width: 43px;display: block;float: left;\">\n\t\t\t\t\t\t<input name=\"id[]\" type=\"checkbox\" class=\"";
            echo $table_name;
            echo "Batch\" value=\"";
            echo $subscriptionID;
            echo "\" />\n\t\t\t\t\t\t";
            if($subscription["Status"] < 8 && $subscription["AutoRenew"] != "no" && ($subscription["TerminationDate"] == "" || rewrite_date_site2db($subscription["StartPeriod"]) < rewrite_date_site2db($subscription["TerminationDate"]))) {
                echo "\t\t\t\t\t\t\t";
                if(rewrite_date_site2db($subscription["NextDate"]) <= date("YmdHis")) {
                    echo "\t\t\t\t\t\t\t\t<span class=\"inline_status busy infopopuptop delaypopup\">&nbsp;<span class=\"popup\">";
                    echo __("invoice subscription");
                    echo "<b></b></span></span>\n\t\t\t\t\t\t\t";
                } elseif($subscription["TerminationDate"]) {
                    echo "\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<span class=\"inline_status active infopopuptop delaypopup\">&nbsp;<span class=\"popup\">";
                    echo sprintf(__("active, do not invoice after"), $subscription["TerminationDate"]);
                    echo "<b></b></span></span>\t\n\t\t\t\t\t\t\t";
                } elseif($subscription["AutoRenew"] == "once") {
                    echo "\t\n\t\t\t\t\t\t\t\t<span class=\"inline_status active infopopuptop delaypopup\">&nbsp;<span class=\"popup\">";
                    echo sprintf(__("active, one time billing"), $subscription["NextDate"]);
                    echo "<b></b></span></span>\n\t\t\t\t\t\t\t";
                } else {
                    echo "\t\t\t\t\t\t\t\t<span class=\"inline_status active infopopuptop delaypopup\">&nbsp;<span class=\"popup\">";
                    echo __("active");
                    echo "<b></b></span></span>\n\t\t\t\t\t\t\t";
                }
                echo "\t\t\t\t\t\t";
            } elseif($subscription["AutoRenew"] == "no") {
                echo "\t\t\t\t\t\t<span class=\"inline_status deleted infopopuptop delaypopup\">&nbsp;<span class=\"popup\">";
                echo __("do not invoice automatically");
                echo "<b></b></span></span>\n\t\t\t\t\t\t";
            } else {
                echo "\t\t\t\t\t\t<span class=\"inline_status deleted infopopuptop delaypopup\">&nbsp;<span class=\"popup\">";
                echo __("terminated");
                echo "<b></b></span></span>\n\t\t\t\t\t\t";
            }
            echo " \n\t\t\t\t\t</span>\n\t\t\t\t\t<span style=\"word-break: break-all;display: block;margin-left: 44px;\">\n\t\t\t\t\t\t";
            if($subscription["Number"] != 1 || $subscription["NumberSuffix"]) {
                echo "\t\t\t\t\t\t\t<span>\n\t\t\t\t\t\t\t";
                echo $subscription["NumberSuffix"] ? showNumber($subscription["Number"]) . $subscription["NumberSuffix"] : showNumber($subscription["Number"]) . "x";
                echo "\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t";
            }
            switch ($subscription["PeriodicType"]) {
                case "domain":
                    echo "<a href=\"domains.php?page=show&amp;id=";
                    echo $subscription["Reference"];
                    echo "#tab-domain-subscription\" class=\"c1 a1\">";
                    echo stripReturnAndSubstring($subscription["Description"], 170);
                    echo "</a>";
                    break;
                case "hosting":
                    echo "<a href=\"hosting.php?page=show&amp;id=";
                    echo $subscription["Reference"];
                    echo "#tab-hosting-subscription\" class=\"c1 a1\">";
                    echo stripReturnAndSubstring($subscription["Description"], 170);
                    echo "</a>";
                    break;
                default:
                    if(isset($_module_instances[$subscription["PeriodicType"]])) {
                        echo "<a href=\"modules.php?module=";
                        echo $subscription["PeriodicType"];
                        echo "&amp;page=show&amp;id=";
                        echo $subscription["Reference"];
                        echo "#tab-";
                        echo $subscription["PeriodicType"];
                        echo "-subscription\" class=\"c1 a1\">";
                        echo stripReturnAndSubstring($subscription["Description"], 170);
                        echo "</a>";
                    } else {
                        echo "<a href=\"services.php?page=show&amp;id=";
                        echo $subscriptionID;
                        echo "\" class=\"c1 a1\">";
                        echo stripReturnAndSubstring($subscription["Description"], 170);
                        echo "</a>";
                    }
                    if($subscription["InvoiceAuthorisation"] == "yes" && $subscription["DebtorInvoiceAuthorisation"] == "yes") {
                        echo " <span class=\"fontsmall c4\">";
                        echo __("inc");
                        echo "</span>";
                    }
                    echo "\t\t\t\t\t</span>\n\t\t\t\t</td>\n\t\t\t\t\t\n\t\t\t\t";
                    if(defined("SHOW_PRODUCTNAME") && SHOW_PRODUCTNAME === true) {
                        echo "\t\t\t\t<td style=\"vertical-align: top;\">";
                        echo $subscription["ProductName"] ? $subscription["ProductName"] : "-";
                        echo "</td>\n\t\t\t\t";
                    }
                    echo "\t\n\t\t\t\t\t\n\t\t\t\t<td style=\"white-space:nowrap;vertical-align: top;\">";
                    echo $array_producttypes[$subscription["PeriodicType"]];
                    echo "</td>\n\t\t\t\t";
                    if(!in_array("Debtor", $hide_columns)) {
                        echo "<td style=\"vertical-align: top;\">";
                        if(0 < $subscription["Debtor"]) {
                            echo "<a href=\"debtors.php?page=show&amp;id=";
                            echo $subscription["Debtor"];
                            echo "\" class=\"a1\">";
                            echo $subscription["CompanyName"] ? $subscription["CompanyName"] : $subscription["SurName"] . ", " . $subscription["Initials"];
                            echo "</a>";
                        } else {
                            echo "-";
                        }
                        echo "</td>";
                    }
                    echo "\t\t\t\t<td style=\"width: 5px;vertical-align: top;\" class=\"";
                    if(VAT_CALC_METHOD == "incl") {
                        echo "show_col_ws ";
                    }
                    echo "currency_sign_left\">";
                    echo currency_sign_td(CURRENCY_SIGN_LEFT);
                    echo "</td>\n\t\t\t\t<td style=\"width: 70px;vertical-align: top;\" align=\"right\" class=\"";
                    if(VAT_CALC_METHOD == "incl") {
                        echo "show_col_ws";
                    }
                    echo "\">";
                    echo money($subscription["AmountExcl"], false);
                    echo "</td>\n\t\t\t\t<td style=\"width: 5px;vertical-align: top;\" class=\"";
                    if(VAT_CALC_METHOD == "incl") {
                        echo "show_col_ws ";
                    }
                    echo "currency_sign_right\">";
                    echo currency_sign_td(CURRENCY_SIGN_RIGHT);
                    echo "</td>\n\t\t\t\t<td style=\"width: 5px;vertical-align: top;\" class=\"";
                    if(VAT_CALC_METHOD == "excl") {
                        echo "show_col_ws ";
                    }
                    echo "currency_sign_left\">";
                    echo currency_sign_td(CURRENCY_SIGN_LEFT);
                    echo "</td>\n\t\t\t\t<td style=\"width: 70px;vertical-align: top;\" align=\"right\" class=\"";
                    if(VAT_CALC_METHOD == "excl") {
                        echo "show_col_ws";
                    }
                    echo "\">";
                    echo money($subscription["AmountIncl"], false);
                    echo "</td>\n\t\t\t\t<td style=\"width: 5px;vertical-align: top;\" class=\"";
                    if(VAT_CALC_METHOD == "excl") {
                        echo "show_col_ws ";
                    }
                    echo "currency_sign_right\">";
                    echo currency_sign_td(CURRENCY_SIGN_RIGHT);
                    echo "</td>\n\t\t\t\t<td style=\"white-space:nowrap;vertical-align: top;\">";
                    echo $subscription["NextDate"];
                    if($subscription["AutoRenew"] == "no") {
                        echo "<span class=\"fontsmall c4\"> - " . __("subscription autorenew no for nextdate col") . "</span>";
                    }
                    echo "</td>\n\t\t\t\t<td style=\"vertical-align: top;\">";
                    echo $subscription["StartPeriod"];
                    echo " ";
                    echo __("till");
                    echo " ";
                    echo $subscription["EndPeriod"];
                    echo "</td>\n\t\t\t</tr>\n\t\t\t";
            }
        }
    }
    if($subscriptionCounter === 0) {
        echo "\t\t\t<tr>\n\t\t\t\t<td colspan=\"";
        echo $column_count;
        echo "\">\n\t\t\t\t\t";
        echo __("no results found");
        echo "\t\t\t\t</td>\n\t\t\t</tr>\n\t\t\t";
    } else {
        echo "\t\t\t";
        if(0 < $subscriptionCounter) {
            echo "\t\t\t<tr class=\"table_options\">\n\t\t\t\t<td colspan=\"";
            echo $column_count;
            echo "\">\n\t\t\t\t\n\t\t\t\t\t<p class=\"ico inline hook\">\n\t\t\t\t\t\t<select name=\"action\" class=\"select1 BatchSelect\">\n\t\t\t\t\t\t\t<option selected=\"selected\">";
            echo __("with selected");
            echo "</option>\n\t\t\t\t\t\t\t";
            if(!in_array("makeinvoice", $hide_actions)) {
                echo "<option value=\"dialog:submakeinvoice\">";
                echo __("invoice selected subscriptions");
                echo "</option>";
            }
            echo "\t\t\t\t\t\t\t<option value=\"dialog:terminate_subscription\">";
            echo __("terminate service");
            echo "\t\t\t\t\t\t\t<option value=\"delete\">";
            echo __("delete service");
            echo "</option>\n\t\t\t\t\t\t</select>\n\t\t\t\t\t\t";
            if(!in_array("makeinvoice", $hide_actions)) {
                echo "\t\t\t\t\t\t<div class=\"hide\" id=\"dialog_submakeinvoice\" title=\"";
                echo __("invoice selected subscriptions");
                echo "\">\n\t\t\t\t\t\t\t<strong>";
                echo __("confirm action");
                echo "</strong><br />\n\t\t\t\t\t\t\t";
                echo __("batchdialog invoice selected subscriptions");
                echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\t";
            }
            echo "\t\t\t\t\t</p>\n\t\t\t\t\t<br />\n\t\t\t\t\n\t\t\t\t\t";
            if(min(MIN_PAGINATION, $session["results"]) < $data_array["CountRows"]) {
                echo "<br />";
                ajax_paginate($table_name, isset($data_array["CountRows"]) ? $data_array["CountRows"] : 0, $session["results"], $current_page, $current_page_url);
            }
            echo "\t\t\t\t</td>\n\t\t\t</tr>\n\t\t\t";
        }
    }
    echo "\t\t\t</table>\n\t\t</div>\n\t\t\n\t\t</fieldset></form>\n\t\n\t<div id=\"custom_dialog_terminate_subscription\" title=\"";
    echo __("terminate service batch dialog title");
    echo "\" class=\"hide\">\n\t\t\n\t\t<p>\n\t\t\t";
    echo __("terminate service batch dialog from subscription");
    echo "\t\t\t<br />\n\t\t\t<br />\t\t\t\n\t\t</p>\n\t\t\n\t\t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#custom_dialog_terminate_subscription').dialog('close');\">";
    echo __("cancel");
    echo "</a></p>\n\t</div>\n\t<script type=\"text/javascript\">\n\t\$(function(){\n\t\t\$('#custom_dialog_terminate_subscription').dialog({ autoOpen: false, width: 450, modal: true, resizable: false, beforeClose: function( event, ui ) { \$('form[name='+BatchForm+']').find('.BatchSelect').val(''); BatchAction = BatchForm = ''; }});\n\n        \$(document).on('change', '.BatchSelect', function()\n        {\n            if(\$(this).val() == \"dialog:terminate_subscription\")\n            {\n            \t\$('#custom_dialog_terminate_subscription').dialog('open');\n           \t}\n       \t});\n\t\t\n\t});\n\t</script>\n\t";
}

?>