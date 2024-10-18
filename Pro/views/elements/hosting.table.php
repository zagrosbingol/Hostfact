<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
function show_hosting_table($data_array, $options = [])
{
    $table_type = isset($options["table_type"]) ? $options["table_type"] : "subtable";
    $session_name = isset($options["session_name"]) ? $options["session_name"] : "";
    $current_page = isset($options["current_page"]) ? $options["current_page"] : "";
    $current_page_url = isset($options["current_page_url"]) ? $options["current_page_url"] : "";
    $redirect_page = isset($options["redirect_page"]) ? $options["redirect_page"] : "";
    $redirect_id = isset($options["redirect_id"]) ? $options["redirect_id"] : "";
    $hide_columns = isset($options["hide_columns"]) ? $options["hide_columns"] : [];
    $column_count = 8 - count($hide_columns);
    $session = $_SESSION[$session_name];
    global $array_hostingstatus;
    global $array_periodic;
    global $array_taxpercentages;
    echo "\t<form id=\"HostingForm\" name=\"form_hosting\" method=\"post\" action=\"hosting.php?page=view";
    if($redirect_page) {
        echo "&amp;from_page=" . $redirect_page;
    }
    if($redirect_id) {
        echo "&amp;from_id=" . $redirect_id;
    }
    echo "\"><fieldset>\t\n\t\t\t\t\t\n\t<div id=\"SubTable_Hosting\">\t\n\t\t<table id=\"MainTable_Hosting\" class=\"table1\" cellpadding=\"0\" cellspacing=\"0\">\n\t\t<tr class=\"trtitle\">\t\n\t\t\t<th scope=\"col\"><label><input name=\"HostingBatch\" class=\"BatchCheck\" type=\"checkbox\" value=\"on\" /></label> <a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','Username','Hosting','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 pointer ";
    if($session["sort"] == "Username") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\" style=\"padding-left:18px\">";
    echo __("account");
    echo "</a></th>\n\t\t\t";
    if(!in_array("Debtor", $hide_columns)) {
        echo "<th scope=\"col\"><a onclick=\"ajaxSave('";
        echo $session_name;
        echo "','sort','Debtor','Hosting','";
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
    echo "\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','Domain','Hosting','";
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
    echo "\">";
    echo __("domain");
    echo "</a></th>\n\t\t\t";
    if(!in_array("Package", $hide_columns)) {
        echo "<th scope=\"col\"><a onclick=\"ajaxSave('";
        echo $session_name;
        echo "','sort','Package','Hosting','";
        echo $current_page_url;
        echo "');\" class=\"ico set2 pointer ";
        if($session["sort"] == "Package") {
            if($session["order"] == "ASC") {
                echo "arrowup";
            } else {
                echo "arrowdown";
            }
        } else {
            echo "arrowhover";
        }
        echo "\">";
        echo __("package");
        echo "</a></th>";
    }
    echo "\t\t\t";
    if(!in_array("Server", $hide_columns)) {
        echo "<th scope=\"col\"><a onclick=\"ajaxSave('";
        echo $session_name;
        echo "','sort','Server','Hosting','";
        echo $current_page_url;
        echo "');\" class=\"ico set2 pointer ";
        if($session["sort"] == "Server") {
            if($session["order"] == "ASC") {
                echo "arrowup";
            } else {
                echo "arrowdown";
            }
        } else {
            echo "arrowhover";
        }
        echo "\">";
        echo __("server");
        echo "</a></th>";
    }
    echo "\t\t\t<th scope=\"col\" style=\"width: 30px;\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','PeriodicID','Hosting','";
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
    $hostingCounter = 0;
    foreach ($data_array as $hostingID => $hosting) {
        if(is_numeric($hostingID)) {
            $hostingCounter++;
            echo "\t\t<tr class=\"hover_extra_info ";
            if($hostingCounter % 2 === 1) {
                echo "tr1";
            }
            echo "\">\n\t\t\t<td><input name=\"accounts[]\" type=\"checkbox\" class=\"HostingBatch\" value=\"";
            echo $hostingID;
            echo "\" />\n\t\t\t";
            switch ($hosting["Status"]) {
                case "4":
                    echo "<span class=\"inline_status active infopopuptop delaypopup\">&nbsp;<span class=\"popup\">";
                    echo $array_hostingstatus[$hosting["Status"]];
                    echo "<b></b></span></span>";
                    break;
                case "3":
                    echo "<span class=\"inline_status busy infopopuptop delaypopup\">&nbsp;<span class=\"popup\">";
                    echo $array_hostingstatus[$hosting["Status"]];
                    echo "<b></b></span></span>";
                    break;
                case "5":
                case "7":
                    echo "<span class=\"inline_status error infopopuptop delaypopup\">&nbsp;<span class=\"popup\">";
                    echo $array_hostingstatus[$hosting["Status"]];
                    echo "<b></b></span></span>";
                    break;
                default:
                    echo "<span class=\"inline_status deleted infopopuptop delaypopup\">&nbsp;<span class=\"popup\">";
                    echo $array_hostingstatus[$hosting["Status"]];
                    echo "<b></b></span></span>";
                    echo "\t\t\t<a href=\"hosting.php?page=show&id=";
                    echo $hostingID;
                    echo "\" class=\"";
                    if($hosting["Status"] == "7") {
                        echo "c3";
                    } else {
                        echo "c1";
                    }
                    echo " a1\">";
                    echo $hosting["Username"];
                    echo "</a>";
                    if($hosting["Status"] == "5") {
                        echo " <span class=\"fontsmall c4\">";
                        echo __("account is blocked");
                        echo "</span>";
                    } elseif($hosting["Status"] == 4 && 0 < $hosting["TerminationID"]) {
                        echo " <span class=\"fontsmall c4\">";
                        echo "- " . __("subscription tab - terminated at x");
                        echo "</span>";
                    }
                    echo "</td>\n\t\t\t";
                    if(!in_array("Debtor", $hide_columns)) {
                        echo "<td>";
                        if(0 < $hosting["Debtor"]) {
                            echo "<a href=\"debtors.php?page=show&amp;id=";
                            echo $hosting["Debtor"];
                            echo "\" class=\"a1\">";
                            echo $hosting["CompanyName"] ? $hosting["CompanyName"] : $hosting["SurName"] . ", " . $hosting["Initials"];
                            echo "</a>";
                        } else {
                            echo __("new customer");
                        }
                        echo "</td>";
                    }
                    echo "\t\t\t<td>";
                    echo $hosting["Domain"];
                    echo "</td>\n\t\t\t";
                    if(!in_array("Package", $hide_columns)) {
                        echo "<td><a href=\"packages.php?page=show&amp;id=";
                        echo $hosting["Package"];
                        echo "\" class=\"a1\">";
                        echo $hosting["PackageName"];
                        echo "</a></td>";
                    }
                    echo "\t\t\t";
                    if(!in_array("Server", $hide_columns)) {
                        echo "<td><a href=\"servers.php?page=show&amp;id=";
                        echo $hosting["Server"];
                        echo "\" class=\"a1\">";
                        echo $hosting["Name"];
                        echo "</a></td>";
                    }
                    echo "\t\t\t<td>";
                    echo show_subscription_column($hosting);
                    echo "</td>\n\t\t</tr>\n\t\t";
            }
        }
    }
    if($hostingCounter === 0) {
        echo "\t\t<tr>\n\t\t\t<td colspan=\"";
        echo $column_count;
        echo "\">\n\t\t\t\t";
        echo __("no results found");
        echo "\t\t\t</td>\n\t\t</tr>\n\t\t";
    } else {
        echo "\t\t\t";
        if(0 < $hostingCounter) {
            echo "\t\t\t<tr class=\"table_options\">\n\t\t\t\t<td colspan=\"";
            echo $column_count;
            echo "\">\n\t\t\t\t\t<p class=\"ico inline hook\">\n\t\t\t\t\t\t<select name=\"action\" class=\"select1 BatchSelect\">\n\t\t\t\t\t\t\t<option selected=\"selected\">";
            echo __("with selected");
            echo "</option>\n\t\t\t\t\t\t\t";
            if(U_HOSTING_EDIT) {
                echo "                                <optgroup label=\"";
                echo __("optgroup label actions in software");
                echo "\">\n                                \t<option value=\"dialog:activehosting\">";
                echo __("hostingview action make active");
                echo "</option>\n\t                                ";
                if($session_name == "debtor.show.hosting") {
                    echo "\t                                    <option value=\"dialog:changeDebtorHosting\">";
                    echo __("move to debtor");
                    echo "</option>\n\t                                    ";
                }
                echo "                                </optgroup>\n                                ";
            }
            if(U_HOSTING_EDIT || U_HOSTING_DELETE) {
                echo "                                <optgroup label=\"";
                echo __("optgroup label actions to server");
                echo "\">\n\t                                ";
                if(U_HOSTING_EDIT) {
                    echo "\t\t\t\t\t\t\t\t\t\t<option value=\"registerHosting\">";
                    echo __("create hosting");
                    echo "</option>\n\t\t    \t\t\t\t\t\t\t<option value=\"dialog:suspendhosting\">";
                    echo __("block hosting");
                    echo "</option>\n\t\t    \t\t\t\t\t\t\t";
                }
                if(U_HOSTING_DELETE) {
                    echo "\t\t\t\t\t\t\t\t\t\t<option value=\"dialog:terminate_hosting\">";
                    echo __("terminate hosting");
                    echo "</option>\n\t\t\t\t\t\t\t\t\t\t<option value=\"deleteHosting\">";
                    echo __("delete hosting");
                    echo "</option>\n\t\t\t\t\t\t\t\t\t\t";
                }
                echo "                                </optgroup>\n                                ";
            }
            echo "\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\n\t\t\t\t\t\t";
            if(U_HOSTING_EDIT) {
                echo "\t\t\t\t\t\t<div class=\"hide\" id=\"dialog_suspendhosting\" title=\"";
                echo __("block hosting");
                echo "\">\n\t\t\t\t\t\t\t<strong>";
                echo __("confirm action");
                echo "</strong><br />\n\t\t\t\t\t\t\t";
                echo __("batch dialog block hostingaccount");
                echo "<br /><br />\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t";
                echo __("the following actions will be executed");
                echo ":<br />\n\t\t\t\t\t\t\t- ";
                echo __("the account will be blocked in software");
                echo "<br />\n\t\t\t\t\t\t\t- ";
                echo __("the account will be blocked on the server");
                echo "\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<div class=\"hide\" id=\"dialog_activehosting\" title=\"";
                echo __("hostingview action make active");
                echo "\">\n\t\t\t\t\t\t\t<strong>";
                echo __("confirm action");
                echo "</strong><br />\n\t\t\t\t\t\t\t";
                echo __("batch dialog action make active");
                echo "\t\t\t\t\t\t</div>\n\t\t\t\t\t\t";
            }
            echo "\t\t\t\t\t</p>\n\t\t\t\t\t\n\t\t\t\t\t<br />\n\t\t\t\t\t\n\t\t\t\t\t";
            if(min(MIN_PAGINATION, $session["results"]) < $data_array["CountRows"]) {
                echo "\t\t\t\t\t\t<br />\n\t\t\t\t\t\t";
                ajax_paginate("Hosting", isset($data_array["CountRows"]) ? $data_array["CountRows"] : 0, $session["results"], $current_page, $current_page_url);
                echo "\t\t\t\t\t";
            }
            echo "\t\t\t\t</td>\n\t\t\t</tr>\n\t\t\t";
        }
        echo "\t\t";
    }
    echo "\t\t</table>\n\t</div>\n    \n    ";
    if(U_HOSTING_EDIT && $session_name == "debtor.show.hosting") {
        echo "            <div class=\"hide\" id=\"dialog_changeDebtorHosting\" title=\"";
        echo __("transfer services");
        echo "\">\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\n                ";
        require "views/dialog.change.debtor.php";
        echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\n            </div>\n            ";
    }
    echo "\t\n\t</fieldset></form>\n\t";
    if(U_HOSTING_DELETE) {
        service_termination_batch_dialog("hosting", "form_hosting");
    }
}

?>