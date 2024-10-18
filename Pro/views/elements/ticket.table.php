<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
function show_ticket_table($data_array, $options = [])
{
    $table_name = isset($options["table_name"]) ? $options["table_name"] : "Tickets";
    $table_type = isset($options["table_type"]) ? $options["table_type"] : "subtable";
    $session_name = isset($options["session_name"]) ? $options["session_name"] : "";
    $current_page = isset($options["current_page"]) ? $options["current_page"] : "1";
    $current_page_url = isset($options["current_page_url"]) ? $options["current_page_url"] : "";
    $redirect_page = isset($options["redirect_page"]) ? $options["redirect_page"] : "";
    $redirect_id = isset($options["redirect_id"]) ? $options["redirect_id"] : "";
    $hide_columns = isset($options["hide_columns"]) ? $options["hide_columns"] : [];
    $column_count = 9 - count($hide_columns);
    $hide_actions = isset($options["hide_actions"]) ? $options["hide_actions"] : [];
    $session = $_SESSION[$session_name];
    global $array_ticketstatus;
    global $array_priority;
    echo "\t\n\t<!--form-->\n\t<form id=\"TicketForm";
    if(strtolower($table_name) != "tickets") {
        echo "2";
    }
    echo "\" name=\"form_";
    echo strtolower($table_name);
    echo "\" method=\"post\" action=\"tickets.php?page=overview";
    if($redirect_page) {
        echo "&amp;from_page=" . $redirect_page;
    }
    if($redirect_id) {
        echo "&amp;from_id=" . $redirect_id;
    }
    echo "\"><fieldset>\n\t<!--form-->\n\t\n\t<div id=\"SubTable_";
    echo $table_name;
    echo "\">\t\n\t<table id=\"MainTable_";
    echo $table_name;
    echo "\" class=\"table1\" cellpadding=\"0\" cellspacing=\"0\">\n\t<tr class=\"trtitle\">\n\t\t<th scope=\"col\" style=\"width: 120px;\"><label><input name=\"";
    echo $table_name;
    echo "Batch\" class=\"BatchCheck\" type=\"checkbox\" value=\"on\" /></label> <a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','TicketID','";
    echo $table_name;
    echo "','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 pointer ";
    if($session["sort"] == "TicketID") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("ticket no");
    echo "</a></th>\n\t\t<th scope=\"col\" style=\"width: 20px;\">#</th>\n\t\t<th scope=\"col\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','Subject','";
    echo $table_name;
    echo "','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 pointer ";
    if($session["sort"] == "Subject") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("subject");
    echo "</a></th>\n\t\t";
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
    echo "\t\t<th scope=\"col\" class=\"show_col_ws\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','LastName','";
    echo $table_name;
    echo "','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 pointer ";
    if($session["sort"] == "LastName") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("ticket lastname");
    echo "</a></th>\n\t\t<th scope=\"col\" style=\"width: 100px;\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','Owner','";
    echo $table_name;
    echo "','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 pointer ";
    if($session["sort"] == "Owner") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("owner");
    echo "</a></th>\n\t\t<th scope=\"col\" style=\"width: 120px;\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','LastDate','";
    echo $table_name;
    echo "','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 pointer ";
    if($session["sort"] == "LastDate") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("last message");
    echo "</a></th>\n\t\t<th scope=\"col\" style=\"width: 100px;\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','Priority','";
    echo $table_name;
    echo "','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 pointer ";
    if($session["sort"] == "Priority") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("priority");
    echo "</a></th>\n\t\t";
    if(!in_array("Status", $hide_columns)) {
        echo "<th scope=\"col\" style=\"width: 100px;\"><a onclick=\"ajaxSave('";
        echo $session_name;
        echo "','sort','Status','";
        echo $table_name;
        echo "','";
        echo $current_page_url;
        echo "');\" class=\"ico set2 pointer ";
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
        echo __("status");
        echo "</a></th>";
    }
    echo "\t</tr>\n\t";
    $ticketCounter = 0;
    foreach ($data_array as $ticketID => $ticket) {
        if(is_numeric($ticketID)) {
            $ticketCounter++;
            echo "\t<tr class=\"hover_extra_info ";
            if($ticketCounter % 2 === 1) {
                echo "tr1";
            }
            echo "\">\n\t\t<td><input name=\"id[]\" type=\"checkbox\" class=\"";
            echo $table_name;
            echo "Batch\" value=\"";
            echo $ticketID;
            echo "\" /> <a href=\"tickets.php?page=show&id=";
            echo $ticketID;
            echo "\" class=\"c1 a1\">";
            echo $ticket["TicketID"];
            echo "</a></td>\n\t\t<td>";
            echo $ticket["Number"];
            echo "</td>\n\t\t<td>";
            echo $ticket["Subject"];
            echo "</td>\n\t\t";
            if(!in_array("Debtor", $hide_columns)) {
                echo "<td>";
                if(0 < $ticket["Debtor"]) {
                    echo "<a href=\"debtors.php?page=show&amp;id=";
                    echo $ticket["Debtor"];
                    echo "\" class=\"a1\">";
                    echo $ticket["CompanyName"] ? $ticket["CompanyName"] : $ticket["SurName"] . ", " . $ticket["Initials"];
                    echo "</a>";
                } else {
                    echo "-";
                }
                echo "</td>";
            }
            echo "\t\t<td class=\"show_col_ws\">";
            echo $ticket["LastName"];
            echo "</td>\n\t\t<td>";
            echo isset($ticket["Name"]) ? $ticket["Name"] : "-";
            echo "</td>\n\t\t<td>";
            echo rewrite_date_db2site($ticket["LastDate"], DATE_FORMAT . " " . __("at") . " %H:%i");
            echo "</td>\n\t\t<td>";
            echo $array_priority[$ticket["Priority"]];
            echo "</td>\n\t\t";
            if(!in_array("Status", $hide_columns)) {
                echo "<td>";
                echo $array_ticketstatus[$ticket["Status"]];
                echo "</td>";
            }
            echo "\t</tr>\n\t";
        }
    }
    if($ticketCounter === 0) {
        echo "\t<tr>\n\t\t<td colspan=\"";
        echo $column_count;
        echo "\">\n\t\t\t";
        echo __("no results found");
        echo "\t\t</td>\n\t</tr>\n\t";
    } else {
        echo "\t<tr class=\"table_options\">\n\t\t<td colspan=\"";
        echo $column_count;
        echo "\">\n\t\t\t";
        if(0 < $ticketCounter) {
            echo "\t\t\t<p class=\"ico inline hook\">\n\t\t\t\t<select name=\"action\" class=\"select1 BatchSelect\">\n\t\t\t\t\t<option value=\"\">";
            echo __("with selected");
            echo "</option>\n\t\t\t\t\t";
            if(!in_array("closeticket", $hide_actions)) {
                echo "<option value=\"dialog:closeTicket\">";
                echo __("close ticket");
                echo "</option>";
            }
            echo "\t\t\t\t\t<option value=\"dialog:removeTicket\">";
            echo __("delete ticket");
            echo "</option>\n\t\t\t\t</select>\n\t\t\t\t\n\t\t\t\t";
            if(!in_array("closeticket", $hide_actions)) {
                echo "\t\t\t\t<div class=\"hide\" id=\"dialog_closeTicket\" title=\"";
                echo __("close ticket");
                echo "\">\n\t\t\t\t\t<strong>";
                echo __("confirm action");
                echo "</strong><br />\n\t\t\t\t\t";
                echo __("dialog batch ticket close");
                echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t</div>\n\t\t\t\t";
            }
            echo "\t\t\t\t\n\t\t\t\t<div class=\"hide\" id=\"dialog_removeTicket\" title=\"";
            echo __("delete ticket");
            echo "\">\n\t\t\t\t\t<strong>";
            echo __("confirm action");
            echo "</strong><br />\n\t\t\t\t\t";
            echo __("dialog batch ticket delete");
            echo "\t\t\t\t</div>\n\t\t\t</p>\n\t\t\t\n\t\t\t<br />\n\t\t\t";
        }
        echo "\t\t\t\n\t\t\t";
        if(min(MIN_PAGINATION, $session["results"]) < $data_array["CountRows"]) {
            echo "<br /><br />";
            ajax_paginate($table_name, isset($data_array["CountRows"]) ? $data_array["CountRows"] : 0, $session["results"], $current_page, $current_page_url);
        }
        echo "\t\t</td>\n\t</tr>\n\t";
    }
    echo "\t</table>\n\t</div>\n\t\n\t<!--form-->\n\t</fieldset></form>\n\t<!--form-->\n\t\n\t";
}

?>