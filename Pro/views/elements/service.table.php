<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
function show_service_table($data_array, $options = [])
{
    $session_name = isset($options["session_name"]) ? $options["session_name"] : "";
    $current_page = isset($options["current_page"]) ? $options["current_page"] : "1";
    $current_page_url = isset($options["current_page_url"]) ? $options["current_page_url"] : "";
    $redirect_page = isset($options["redirect_page"]) ? $options["redirect_page"] : "";
    $redirect_id = isset($options["redirect_id"]) ? $options["redirect_id"] : "";
    $hide_columns = isset($options["hide_columns"]) ? $options["hide_columns"] : [];
    $column_count = 5 - count($hide_columns);
    $session = $_SESSION[$session_name];
    global $array_servicestatus;
    global $array_periodic;
    global $array_periodesMV;
    global $array_taxpercentages;
    echo "\t<!--form-->\n\t<form id=\"ServiceForm\" name=\"form_services\" method=\"post\" action=\"services.php?page=view";
    if($redirect_page) {
        echo "&amp;from_page=" . $redirect_page;
    }
    if($redirect_id) {
        echo "&amp;from_id=" . $redirect_id;
    }
    echo "\"><fieldset>\n\t<!--form-->\n\t<div id=\"SubTable_Services\">\t\n\t\t<table id=\"MainTable_Services\" class=\"table1\" cellpadding=\"0\" cellspacing=\"0\">\t\t\n\t\t<tr class=\"trtitle\">\n\t\t\t<th scope=\"col\"><label><input name=\"ServiceBatch\" class=\"BatchCheck\" type=\"checkbox\" value=\"on\" /></label> <a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','Description','Services','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 ";
    if($session["sort"] == "Description") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\" style=\"padding-left:18px\">";
    echo __("description");
    echo "</a></th>\n\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','ProductName','Services','";
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
    echo "</a></td>\n\t\t\t";
    if(!in_array("Debtor", $hide_columns)) {
        echo "<th scope=\"col\"><a onclick=\"ajaxSave('";
        echo $session_name;
        echo "','sort','Debtor','Services','";
        echo $current_page_url;
        echo "');\" class=\"ico set2 ";
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
        echo "</a></td>";
    }
    echo "\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','EndContract','Services','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 ";
    if($session["sort"] == "EndContract") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("contract period");
    echo "</a></th>\n\t\t\t<th scope=\"col\" style=\"width: 30px;\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','Status','Services','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 ";
    if($session["sort"] == "Status") {
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
    $serviceCounter = 0;
    foreach ($data_array as $subscriptionID => $subscription) {
        if(is_numeric($subscriptionID)) {
            $serviceCounter++;
            echo "\t\t<tr class=\"hover_extra_info ";
            if($serviceCounter % 2 === 1) {
                echo "tr1";
            }
            echo "\">\n\t\t\t<td style=\"vertical-align: top;\">\n\t\t\t\t<span style=\"width: 43px;display: block;float: left;\">\n\t\t\t\t\t<input name=\"services[]\" type=\"checkbox\" value=\"";
            echo $subscriptionID;
            echo "\" class=\"ServiceBatch\"/>\n\t\t\t\t\t";
            if($subscription["Status"] < 8 && ($subscription["TerminationDate"] == "" || rewrite_date_site2db($subscription["StartPeriod"]) < rewrite_date_site2db($subscription["TerminationDate"]))) {
                echo "\t\t\t\t\t<span class=\"inline_status active infopopuptop delaypopup\">&nbsp;<span class=\"popup\">";
                echo __("active");
                echo "<b></b></span></span>\n\t\t\t\t\t";
            } else {
                echo "\t\t\t\t\t<span class=\"inline_status deleted infopopuptop delaypopup\">&nbsp;<span class=\"popup\">";
                echo __("terminated");
                echo "<b></b></span></span>\n\t\t\t\t\t";
            }
            echo "\t\t\t\t</span>\n\t\t\t\t<span style=\"word-break: break-all;display: block;margin-left: 44px;\">\n\t\t\t\t\t";
            if($subscription["Number"] != 1 || $subscription["NumberSuffix"]) {
                echo "\t\t\t\t\t\t<span>\n\t\t\t\t\t\t";
                echo $subscription["NumberSuffix"] ? showNumber($subscription["Number"]) . $subscription["NumberSuffix"] : showNumber($subscription["Number"]) . "x";
                echo "\t\t\t\t\t\t</span>\n\t\t\t\t\t\t";
            }
            echo "\t\t\t\t\t<a href=\"services.php?page=show&id=";
            echo $subscriptionID;
            echo "\" class=\"c1 a1\">";
            echo stripReturnAndSubstring($subscription["Description"], 170);
            echo "</a></td>\n\t\t\t\t</span>\n\t\t\t<td style=\"vertical-align: top;\">";
            if(0 < $subscription["ProductID"]) {
                echo "<a href=\"products.php?page=show&amp;id=";
                echo $subscription["ProductID"];
                echo "\" class=\"a1\">";
                echo $subscription["ProductName"];
                echo "</a>";
            } else {
                echo "-";
            }
            echo "</td>\n\t\t\t";
            if(!in_array("Debtor", $hide_columns)) {
                echo "<td style=\"vertical-align: top;\"><a href=\"debtors.php?page=show&amp;id=";
                echo $subscription["Debtor"];
                echo "\" class=\"a1\">";
                echo $subscription["CompanyName"] ? $subscription["CompanyName"] : $subscription["SurName"] . ", " . $subscription["Initials"];
                echo "</a></td>";
            }
            echo "\t        <td style=\"vertical-align: top;\">";
            if(0 < $subscription["ContractPeriods"] && $subscription["EndContract"]) {
                echo "\t\t\t\t";
                echo $subscription["ContractPeriods"];
                echo " ";
                echo $subscription["ContractPeriods"] != 1 ? $array_periodesMV[$subscription["ContractPeriodic"]] : $array_periodic[$subscription["ContractPeriodic"]];
                echo " (";
                echo __("till");
                echo " ";
                echo $subscription["EndContract"];
                echo ")\n\t\t\t\t";
            } else {
                echo __("unknown");
            }
            echo "\t\t\t\t</td>\n\t\t\t<td style=\"vertical-align: top;\">\n\t\t\t\t";
            $_tmp_amount = VAT_CALC_METHOD == "incl" ? $subscription["AmountInclPerPeriod"] : $subscription["AmountExclPerPeriod"];
            if($subscription["TerminationDate"] == "") {
                echo "\t\t\t\t\t<span class=\"inline_subscription active infopopupleftsmall\">&nbsp;<span class=\"popup\">";
                echo money($_tmp_amount) . " " . __("per") . " " . $array_periodic[$subscription["Periodic"]];
                if(!empty($array_taxpercentages)) {
                    echo " " . (VAT_CALC_METHOD == "incl" ? __("incl vat") : __("excl vat"));
                }
                echo "<br />";
                echo __("next invoice at date");
                echo " ";
                echo $subscription["NextDate"];
                echo "<b></b></span></span>\n\t\t\t\t";
            } elseif(rewrite_date_site2db($subscription["StartPeriod"]) < rewrite_date_site2db($subscription["TerminationDate"])) {
                echo "\t\t\t\t\t<span class=\"inline_subscription cancelled infopopupleftsmall\">&nbsp;<span class=\"popup\">";
                echo money($_tmp_amount) . " " . __("per") . " " . $array_periodic[$subscription["Periodic"]];
                if(!empty($array_taxpercentages)) {
                    echo " " . (VAT_CALC_METHOD == "incl" ? __("incl vat") : __("excl vat"));
                }
                echo "<br />";
                echo __("next invoice at date") . " " . $subscription["NextDate"];
                echo "<br />";
                echo __("subscription terminated at") . " " . $subscription["TerminationDate"];
                echo "<b></b></span></span>\n\t\t\t\t";
            } else {
                echo "\t\t\t\t\t<span class=\"inline_subscription none infopopupleftsmall\">&nbsp;<span class=\"popup\">";
                if(!$subscription["TerminationDate"] || $subscription["TerminationDate"] == "0") {
                    echo __("no subscription");
                } else {
                    echo __("subscription terminated at") . " " . $subscription["TerminationDate"];
                }
                echo "<b></b></span></span>\n\t\t\t\t";
            }
            echo "\t\t\t</td>\n\t\t</tr>\n\t\t\t\n\t\t";
        }
    }
    if($serviceCounter === 0) {
        echo "\t\t<tr>\n\t\t\t<td colspan=\"";
        echo $column_count;
        echo "\">\n\t\t\t\t";
        echo __("no results found");
        echo "\t\t\t</td>\n\t\t</tr>\n\t\t";
    }
    echo "\t\t";
    if(0 < $serviceCounter) {
        echo "\t\t<tr class=\"table_options\">\n\t\t\t<td colspan=\"";
        echo $column_count;
        echo "\">\n\t\t\t\t<p class=\"ico inline hook\">\n\t\t\t\t\t<select name=\"action\" class=\"select1 BatchSelect\">\n\t\t\t\t\t\t<option selected=\"selected\">";
        echo __("with selected");
        echo "</option>\n                        ";
        if(U_SERVICE_EDIT && $session_name == "debtor.show.other") {
            echo "                                <option value=\"dialog:changeDebtorOther\">";
            echo __("move to debtor");
            echo "</option>\n                                ";
        }
        if(U_SERVICE_DELETE) {
            echo "                                <option value=\"dialog:terminate_other\">";
            echo __("terminate service");
            echo "</option>\n                                <option value=\"delete\">";
            echo __("delete service");
            echo "</option>\n                                ";
        }
        echo "                                                                                            \n\t\t\t\t\t</select>\n\t\t\t\t</p>\n\t\t\t\t\n\t\t\t\t<br />\n\t\t\t\t\n\t\t\t\t";
        if(min(MIN_PAGINATION, $session["results"]) < $data_array["CountRows"]) {
            echo "\t\n\t\t\t\t\t<br />\n\t\t\t\t\t";
            ajax_paginate("Services", isset($data_array["CountRows"]) ? $data_array["CountRows"] : 0, $session["results"], $current_page, $current_page_url);
            echo "\t\t\t\t";
        }
        echo "\t\t\t</td>\n\t\t</tr>\n\t\t";
    }
    echo "\t\t</table>\n\t</div>\n    \n    ";
    if(U_SERVICE_EDIT && $session_name == "debtor.show.other") {
        echo "            <div class=\"hide\" id=\"dialog_changeDebtorOther\" title=\"";
        echo __("transfer services");
        echo "\">\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\n                ";
        require "views/dialog.change.debtor.php";
        echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\n            </div>\n            ";
    }
    echo "\t\n\t<!--form-->\n\t</fieldset></form>\n\t<!--form-->\n\t";
    if(U_SERVICE_DELETE) {
        service_termination_batch_dialog("other", "form_services");
    }
}

?>