<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
function show_handle_table($data_array, $options = [])
{
    $table_type = isset($options["table_type"]) ? $options["table_type"] : "subtable";
    $session_name = isset($options["session_name"]) ? $options["session_name"] : "";
    $current_page = isset($options["current_page"]) ? $options["current_page"] : "";
    $current_page_url = isset($options["current_page_url"]) ? $options["current_page_url"] : "";
    $redirect_page = isset($options["redirect_page"]) ? $options["redirect_page"] : "";
    $redirect_id = isset($options["redirect_id"]) ? $options["redirect_id"] : "";
    $hide_columns = isset($options["hide_columns"]) ? $options["hide_columns"] : [];
    $hide_columns_count = in_array("Registrar", $hide_columns) ? count($hide_columns) - 1 : count($hide_columns);
    $column_count = 5 - $hide_columns_count;
    $session = $_SESSION[$session_name];
    global $array_domainstatus;
    echo "\t<form id=\"HandleForm\" name=\"form_handle\" method=\"post\" action=\"handles.php?page=view";
    if($redirect_page) {
        echo "&amp;from_page=" . $redirect_page;
    }
    if($redirect_id) {
        echo "&amp;from_id=" . $redirect_id;
    }
    echo "\"><fieldset>\t\n\t\t\t\t\n\t<div id=\"SubTable_Handles\">\t\n\t<table id=\"MainTable_Handles\" class=\"table1\" cellpadding=\"0\" cellspacing=\"0\">\n\t<tr class=\"trtitle\">\n\t\n\t\t<th scope=\"col\"><label><input name=\"HandleBatch\" class=\"BatchCheck\" type=\"checkbox\" value=\"on\" /></label> <a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','Handle','Handles','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 pointer ";
    if($session["sort"] == "Handle") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("handle");
    echo "</a></th>\n\t\t";
    if(!in_array("Registrar", $hide_columns)) {
        echo "\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('";
        echo $session_name;
        echo "','sort','Name','Handles','";
        echo $current_page_url;
        echo "');\" class=\"ico set2 pointer ";
        if($session["sort"] == "Name") {
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
        echo "</a></th>\n\t\t";
    } else {
        echo "\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('";
        echo $session_name;
        echo "','sort','RegistrarHandle','Handles','";
        echo $current_page_url;
        echo "');\" class=\"ico set2 pointer ";
        if($session["sort"] == "RegistrarHandle") {
            if($session["order"] == "ASC") {
                echo "arrowup";
            } else {
                echo "arrowdown";
            }
        } else {
            echo "arrowhover";
        }
        echo "\">";
        echo __("registrarhandle");
        echo "</a></th>\n\t\t";
    }
    echo "\t\t";
    if(!in_array("Debtor", $hide_columns)) {
        echo "\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('";
        echo $session_name;
        echo "','sort','Debtor','Handles','";
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
        echo "</a></th>\n\t\t";
    }
    echo "\t\t<th scope=\"col\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','CompanyName','Handles','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 pointer ";
    if($session["sort"] == "CompanyName") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("companyname");
    echo "</a></th>\n\t\t<th scope=\"col\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','SurName','Handles','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 pointer ";
    if($session["sort"] == "SurName") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("contact person");
    echo "</a></th>\n\t</tr>\n\t";
    $handleCounter = 0;
    foreach ($data_array as $handleID => $handle) {
        if(is_numeric($handleID)) {
            $handleCounter++;
            echo "\t<tr class=\"hover_extra_info ";
            if($handleCounter % 2 === 1) {
                echo "tr1";
            }
            echo "\">\n\t\t<td><input name=\"handles[]\" type=\"checkbox\" class=\"HandleBatch\" value=\"";
            echo $handleID;
            echo "\" /> <a href=\"handles.php?page=show&id=";
            echo $handleID;
            echo "\" class=\"c1 a1\">";
            echo $handle["Handle"];
            echo "</a></td>\n\t\t<td>";
            if(!in_array("Registrar", $hide_columns)) {
                if(0 < $handle["Registrar"]) {
                    echo "<a href=\"registrars.php?page=show&id=" . $handle["Registrar"] . "\" class=\"a1\">" . $handle["Name"] . "</a>";
                } else {
                    echo "-";
                }
                if(isset($handle["RegistrarHandle"]) && $handle["RegistrarHandle"]) {
                    echo " (" . $handle["RegistrarHandle"] . ")";
                }
            } elseif(isset($handle["RegistrarHandle"]) && $handle["RegistrarHandle"]) {
                echo $handle["RegistrarHandle"];
            } else {
                echo "-";
            }
            echo "</td>\n\t\t";
            if(!in_array("Debtor", $hide_columns)) {
                echo "<td>";
                if(0 < $handle["Debtor"]) {
                    echo "<a href=\"debtors.php?page=show&amp;id=";
                    echo $handle["Debtor"];
                    echo "\" class=\"a1\">";
                    echo $handle["DebtorCode"];
                    echo "</a>";
                } elseif($handle["Debtor"] == -1) {
                    echo __("new customer");
                } else {
                    echo __("general handle");
                }
                echo "</td>";
            }
            echo "\t\t<td>";
            echo $handle["CompanyName"];
            echo "</td>\n\t\t<td>";
            echo $handle["SurName"] . ", " . $handle["Initials"];
            echo "</td>\n\t</tr>\n\t";
        }
    }
    if($handleCounter === 0) {
        echo "\t<tr>\n\t\t<td colspan=\"";
        echo $column_count;
        echo "\">\n\t\t\t";
        echo __("no results found");
        echo "\t\t</td>\n\t</tr>\n\t";
    } else {
        echo "\t\t";
        if(0 < $handleCounter) {
            echo "\t\t<tr class=\"table_options\">\n\t\t\t<td colspan=\"";
            echo $column_count;
            echo "\">\n\t\t\t\t\n\t\t\t\t<p class=\"ico inline hook\">\n\t\t\t\t\t<select name=\"action\" class=\"select1 BatchSelect\">\n\t\t\t\t\t\t<option selected=\"selected\">";
            echo __("with selected");
            echo "</option>\n\t\t\t\t\t\t<option value=\"dialog:deleteHandle\">";
            echo __("delete handle");
            echo "</option>\n\t\t\t\t\t</select>\n\t\t\t\t\t\n\t\t\t\t\t<div class=\"hide\" id=\"dialog_deleteHandle\" title=\"";
            echo __("delete handle");
            echo "\">\n\t\t\t\t\t\t<strong>";
            echo __("confirm action");
            echo "</strong><br />\n\t\t\t\t\t\t";
            echo __("batch dialog delete handle");
            echo "\t\t\t\t\t</div>\n\t\t\t\t</p>\n\t\t\t\t\n\t\t\t\t<br />\n\t\t\t\t\n\t\t\t\t";
            if(min(MIN_PAGINATION, $session["results"]) < $data_array["CountRows"]) {
                echo "\t\n\t\t\t\t\t<br />\n\t\t\t\t\t";
                ajax_paginate("Handles", isset($data_array["CountRows"]) ? $data_array["CountRows"] : 0, $session["results"], $current_page, $current_page_url);
                echo "\t\t\t\t";
            }
            echo "\t\t\t</td>\n\t\t</tr>\n\t\t";
        }
        echo "\t";
    }
    echo "\t</table>\n\t</div>\n\t\n\t</fieldset></form>\n\t";
}

?>