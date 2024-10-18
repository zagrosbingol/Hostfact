<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
function show_creditinvoice_table($data_array, $options = [])
{
    $table_type = isset($options["table_type"]) ? $options["table_type"] : "subtable";
    $session_name = isset($options["session_name"]) ? $options["session_name"] : "";
    $current_page = isset($options["current_page"]) ? $options["current_page"] : "1";
    $current_page_url = isset($options["current_page_url"]) ? $options["current_page_url"] : "";
    $redirect_page = isset($options["redirect_page"]) ? $options["redirect_page"] : "";
    $redirect_id = isset($options["redirect_id"]) ? $options["redirect_id"] : "";
    $hide_columns = isset($options["hide_columns"]) ? $options["hide_columns"] : [];
    $column_count = 12 - count($hide_columns);
    $session = $_SESSION[$session_name];
    global $array_invoicemethod;
    global $array_creditinvoicestatus;
    global $array_country;
    echo "\t\n\t<form id=\"InvoiceForm\" name=\"form_creditinvoices\" method=\"post\" action=\"creditors.php?page=overview_creditinvoice";
    if($redirect_page) {
        echo "&amp;from_page=" . $redirect_page;
    }
    if($redirect_id) {
        echo "&amp;from_id=" . $redirect_id;
    }
    echo "\"><fieldset>\n\t\n\t<div id=\"SubTable_CreditInvoices\">\n\t\t<table id=\"MainTable_CreditInvoices\" class=\"table1\" cellpadding=\"0\" cellspacing=\"0\">\n\t\t\t<tr class=\"trtitle\">\n\t\t\t\t<th scope=\"col\" style=\"width: 120px;\"><label><input name=\"CreditInvoiceBatch\" class=\"BatchCheck\" type=\"checkbox\" value=\"on\" /></label> <a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','CreditInvoiceCode','CreditInvoices','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 ";
    if($session["sort"] == "CreditInvoiceCode") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("creditinvoicecode");
    echo "</a></th>\n\t\t\t\t<th scope=\"col\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','InvoiceCode','CreditInvoices','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 ";
    if($session["sort"] == "InvoiceCode") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("creditinvoice external");
    echo "</a></td>\n\t\t\t\t";
    if(!in_array("Creditor", $hide_columns)) {
        echo "<th scope=\"col\"><a onclick=\"ajaxSave('";
        echo $session_name;
        echo "','sort','Creditor','CreditInvoices','";
        echo $current_page_url;
        echo "');\" class=\"ico set2 ";
        if($session["sort"] == "Creditor") {
            if($session["order"] == "ASC") {
                echo "arrowup";
            } else {
                echo "arrowdown";
            }
        } else {
            echo "arrowhover";
        }
        echo "\">";
        echo __("creditor");
        echo "</a></td>";
    }
    echo "\t\t\t\t<th scope=\"col\" style=\"width: 90px;\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','Date','CreditInvoices','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 ";
    if($session["sort"] == "Date") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("invoice date");
    echo "</a></td>\n\t\t\t\t<th scope=\"col\" colspan=\"3\" style=\"width: 100px;\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','AmountExcl','CreditInvoices','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 ";
    if($session["sort"] == "AmountExcl") {
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
    echo "</a></th>\n\t\t\t\t<th scope=\"col\" colspan=\"3\" style=\"width: 105px;\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','AmountIncl','CreditInvoices','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 ";
    if($session["sort"] == "AmountIncl") {
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
    echo "</a></th>\n                <th scope=\"col\" style=\"width: 150px;\" class=\"show_col_widescreen_large\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','ReferenceNumber','CreditInvoices','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 ";
    if($session["sort"] == "ReferenceNumber") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("reference no");
    echo "</a></th>\n\t\t\t\t";
    if(!in_array("Status", $hide_columns)) {
        echo "<th scope=\"col\" style=\"width: 200px;\"><a onclick=\"ajaxSave('";
        echo $session_name;
        echo "','sort','Status','CreditInvoices','";
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
        echo __("status");
        echo "</a></th>";
    }
    echo "\t\t\t\t";
    if($session_name == "invoice.dashboard.creditinvoice") {
        $column_count++;
        echo "<th scope=\"col\" style=\"width: 150px;\"><a onclick=\"ajaxSave('";
        echo $session_name;
        echo "','sort','PayBefore','CreditInvoices','";
        echo $current_page_url;
        echo "');\" class=\"ico set2 ";
        if($session["sort"] == "PayBefore") {
            if($session["order"] == "ASC") {
                echo "arrowup";
            } else {
                echo "arrowdown";
            }
        } else {
            echo "arrowhover";
        }
        echo "\">";
        echo __("last paymentdate");
        echo "</a></th>\n\t\t\t\t";
    }
    echo "\t\t\t</tr>\n\t\t\t";
    require_once "class/attachment.php";
    $attachment = new attachment();
    $creditinvoiceCounter = 0;
    foreach ($data_array as $creditinvoiceID => $creditinvoice) {
        if(is_numeric($creditinvoiceID)) {
            $creditinvoiceCounter++;
            $attachmentData = $attachment->getAttachments($creditinvoiceID, "creditinvoice");
            echo "\t\t\t<tr class=\"hover_extra_info ";
            if($creditinvoiceCounter % 2 === 1) {
                echo "tr1";
            }
            echo "\">\n\t\t\t\t<td>\n                    <input name=\"id[]\" type=\"checkbox\" class=\"CreditInvoiceBatch\" value=\"";
            echo $creditinvoiceID;
            echo "\" />\n                    <a href=\"creditors.php?page=show_invoice&amp;id=";
            echo $creditinvoiceID;
            echo "\" class=\"c1 a1\">\n                        ";
            echo $creditinvoice["CreditInvoiceCode"];
            echo "                    </a>\n                    ";
            echo $creditinvoice["Authorisation"] == "yes" ? " <span class=\"fontsmall c4\">" . __("inc") . "</span>" : "";
            echo "                </td>\n\t\t\t\t<td><span style=\"display:block;white-space: nowrap;overflow: hidden;display: block;text-overflow: ellipsis;max-width: 263px;\">\n\t\t\t\t\t";
            if(!empty($attachmentData)) {
                echo "\t\t\t\t\t\t<a href=\"download.php?type=invoice&id=";
                echo $attachmentData[0]->id;
                echo "\" class=\"ico inline file_";
                getFileType($attachmentData[0]->Filename);
                echo "\">&nbsp;</a>\n\t\t\t\t\t";
            } elseif($creditinvoice["Location"]) {
                echo "\t\t\t\t\t\t<a href=\"creditors.php?page=show_invoice&amp;id=";
                echo $creditinvoiceID;
                echo "&amp;action=download\" class=\"ico inline file_";
                getFileType($creditinvoice["Location"]);
                echo "\">&nbsp;</a>\n\t\t\t\t\t";
            }
            echo "\t\t\t\t\t";
            echo $creditinvoice["InvoiceCode"] . "</span>";
            echo "</td>\n\t\t\t\t";
            if(!in_array("Creditor", $hide_columns)) {
                echo "<td><a href=\"creditors.php?page=show&id=";
                echo $creditinvoice["Creditor"];
                echo "\" class=\"a1\">";
                echo $creditinvoice["CompanyName"] ? $creditinvoice["CompanyName"] : $creditinvoice["SurName"] . ", " . $creditinvoice["Initials"];
                echo "</a></td>";
            }
            echo "\t\t\t\t<td>";
            echo rewrite_date_db2site($creditinvoice["Date"]);
            echo "</td>\n\t\t\t\t<td style=\"width: 5px;\" class=\"currency_sign_left\">";
            echo currency_sign_td(CURRENCY_SIGN_LEFT);
            echo "</td>\n\t\t\t\t<td style=\"width: 50px; padding-left: 0px;\" align=\"right\">";
            echo money($creditinvoice["AmountExcl"], false);
            echo "</td>\n\t\t\t\t<td style=\"width: 35px;\" class=\"currency_sign_right\">";
            echo currency_sign_td(CURRENCY_SIGN_RIGHT);
            echo "</td>\n\t\t\t\t<td style=\"width: 5px;\" class=\"currency_sign_left\">";
            echo currency_sign_td(CURRENCY_SIGN_LEFT);
            echo "</td>\n\t\t\t\t<td style=\"width: 50px; padding-left: 0px;\" align=\"right\">";
            echo money($creditinvoice["AmountIncl"], false);
            echo "</td>\n\t\t\t\t<td style=\"width: 35px;\" class=\"currency_sign_right\">";
            echo currency_sign_td(CURRENCY_SIGN_RIGHT);
            echo "\t\t\t\t\t";
            if($creditinvoice["AmountIncl"] != $creditinvoice["PartPayment"] && $creditinvoice["Status"] == 2) {
                echo "\t\t\t\t\t\t<span class=\"ico inline money infopopuptop\">\n\t\t\t\t\t\t\t&nbsp;\n\t\t\t\t\t\t\t<span class=\"popup\">\n\t\t\t\t\t\t\t\t";
                echo __("open sum");
                echo ": ";
                echo money($creditinvoice["PartPayment"]);
                echo "\t\t\t\t\t\t\t\t<b></b>\n\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t</span>\n\t\t\t\t\t";
            }
            echo "\t\t\t\t</td>\n\t\t\t\t<td style=\"width:150px;\" class=\"show_col_widescreen_large table_text_ellipsis\"><span>";
            echo $creditinvoice["ReferenceNumber"];
            echo "</span></td>\n\t\t\t\t";
            if(!in_array("Status", $hide_columns)) {
                echo "<td>";
                echo $array_creditinvoicestatus[$creditinvoice["Status"]];
                if(($creditinvoice["Status"] == 1 && $creditinvoice["Authorisation"] == "no" || $creditinvoice["Status"] == 2) && 0 < $creditinvoice["Term"]) {
                    echo " (" . __("credit for") . " " . rewrite_date_db2site($creditinvoice["PayBefore"]) . ")";
                } elseif($creditinvoice["Status"] == 1 && $creditinvoice["Authorisation"] == "yes") {
                    echo " " . __("credit auth status");
                } elseif($creditinvoice["Status"] == 3 && isset($creditinvoice["PayDate"]) && $creditinvoice["PayDate"] != "" && $creditinvoice["PayDate"] != "0000-00-00") {
                    echo " (" . rewrite_date_db2site($creditinvoice["PayDate"]) . ")";
                }
                echo "\t\t\t\t</td>";
            }
            echo "\t\t\t\t\n\t\t\t\t";
            if($session_name == "invoice.dashboard.creditinvoice") {
                echo "\t\t\t\t<td>\n\t\t\t\t\t";
                if($creditinvoice["Authorisation"] == "yes") {
                    echo __("credit auth");
                } elseif(($creditinvoice["Status"] == 1 || $creditinvoice["Status"] == 2) && isset($creditinvoice["PayBefore"]) && $creditinvoice["PayBefore"] != "" && $creditinvoice["PayBefore"] != "0000-00-00") {
                    echo rewrite_date_db2site($creditinvoice["PayBefore"]);
                }
                echo "\t\t\t\t</td>\n\t\t\t\t";
            }
            echo "\t\t\t</tr>\n\t\t\t";
        }
    }
    if($creditinvoiceCounter === 0) {
        echo "\t\t\t<tr>\n\t\t\t\t<td colspan=\"";
        echo $column_count;
        echo "\">\n\t\t\t\t\t";
        echo __("no creditinvoices found");
        echo "\t\t\t\t</td>\n\t\t\t</tr>\n\t\t\t";
    }
    echo "\t\t\t";
    if(0 < $creditinvoiceCounter) {
        echo "\t\t\t<tr class=\"table_options\">\n\t\t\t\t<td colspan=\"";
        echo $column_count;
        echo "\">\n\t\t\t\t\t<p class=\"ico inline hook\">\n\t\t\t\t\t\t<select name=\"action\" class=\"select1 BatchSelect\">\n\t\t\t\t\t\t\t<option value=\"\" selected=\"selected\">";
        echo __("with selected");
        echo "</option>\n\t\t\t\t\t\t\t";
        if(U_CREDITOR_EDIT) {
            echo "\t\t\t\t\t\t\t<option value=\"receivedInvoice\">";
            echo __("received invoice action");
            echo "</option>\n\t\t\t\t\t\t    <option value=\"markAsPaid\">";
            echo __("paid action");
            echo "</option>\n\t\t\t\t\t\t    <option value=\"markAsNotPaid\">";
            echo __("unpaid action");
            echo "</option>\n\t\t\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\t    ";
        if(U_CREDITOR_DELETE) {
            echo "<option value=\"dialog:delete_creditinvoice_table\">";
            echo __("delete");
            echo "</option>";
        }
        echo "\t\t\t\t\t\t</select>\t\t\t\n\t\t\t\t\t</p>\n\t\t\t\t\t\n\t\t\t\t\t";
        if(U_CREDITOR_DELETE) {
            echo "\t\n\t\t\t\t\t<div id=\"dialog_delete_creditinvoice_table\" class=\"hide\" title=\"";
            echo __("delete creditinvoice title");
            echo "\">\n\t\t\t\t\t\t<strong>";
            echo __("confirm action");
            echo "</strong><br />\n\t\t\t\t\t\t";
            echo __("batchdialog creditinvoice delete");
            echo "\t\t\t\t\t</div>\n\t\t\t\t\t";
        }
        echo "\t\t\t\t\t<br />\n\t\t\t\t\t\n\t\t\t\t\t";
        if(0 <= $current_page && min(MIN_PAGINATION, $session["results"]) < $data_array["CountRows"]) {
            echo "\t\t\t\t\t\t<br />\t\t\t\n\n\t\t\t\t\t\t";
            ajax_paginate("CreditInvoices", isset($data_array["CountRows"]) ? $data_array["CountRows"] : 0, $session["results"], $current_page, $current_page_url);
            echo "\t\t\t\t\t";
        }
        echo "\t\t\t\t</td>\n\t\t\t</tr>\n\t\t\t";
    }
    echo "\t\t</table>\n\t</div>\n\t\n\t</fieldset></form>\n\t";
}

?>