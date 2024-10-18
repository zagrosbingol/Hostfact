<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "views/header.php";
echo "\n<ul class=\"list1\">\n\t<li><a class=\"ico set1 send";
if(0 < $pricequote->PriceQuoteMethod) {
    echo " printQuestion";
}
echo "\" href=\"pricequotes.php?page=show&action=sent&id=";
echo $pricequote->Identifier;
echo "\">";
echo __("action send");
echo "</a></li>\n\t\n\t<li><a class=\"ico set1 print printQuestion\" href=\"pricequotes.php?page=show&action=print&id=";
echo $pricequote->Identifier;
echo "\">";
echo __("action print");
echo "</a></li>\n\t\n\t";
if($pricequote->Status == 2) {
    echo "\t\n\t<li><a class=\"ico set1 accept acceptQuestion\" href=\"pricequotes.php?page=show&action=accept&id=";
    echo $pricequote->Identifier;
    echo "\">";
    echo __("action accept");
    echo "</a></li>\n\t\n\t<li><a class=\"ico set1 cancel declineQuestion\" href=\"pricequotes.php?page=show&action=decline&id=";
    echo $pricequote->Identifier;
    echo "\">";
    echo __("action decline");
    echo "</a></li>\n\t\n\t";
} elseif($pricequote->Status == 3) {
    echo "\t\n\t<li><a class=\"ico set1 invoice\" onclick=\"\$('#dialog_makeinvoice').dialog('open');\">";
    echo __("action makeinvoice");
    echo "</a></li>\n\t\n\t";
}
echo "\t\n\t<li><a class=\"ico set1 editdocument\" href=\"pricequotes.php?page=edit&id=";
echo $pricequote->Identifier;
echo "\">";
echo __("action edit");
echo "</a></li>\n\t\n\t<li><a class=\"ico set1 decline\" onclick=\"\$('#delete_pricequote').dialog('open');\">";
echo __("action delete");
echo "</a></li>\n\t\n</ul>\n\n<hr />\n\n";
echo $message;
echo "\n<!--heading1-->\n<div class=\"heading1 mar1\">\n<!--heading1-->\n\n\t<h2>";
echo __("pricequote");
echo " ";
echo $pricequote->PriceQuoteCode;
echo "</h2>\n\n\t<p class=\"pos2\"><strong class=\"textsize1\">";
echo $array_pricequotestatus[$pricequote->Status];
echo "</strong></p>\n\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n<!--clone pricequote-->\n";
if(U_PRICEQUOTE_EDIT) {
    echo "<div class=\"duplicateInvoice\">\n\t";
    if(!empty($pricequote->Attachment)) {
        echo "<a onclick=\"\$('#invoicedialog_clone').dialog('open');\" class=\"pointer\">";
        echo __("clone pricequote");
        echo "</a>";
    } else {
        echo "<a href=\"pricequotes.php?page=add&clone=";
        echo $pricequote->Identifier;
        echo "\">";
        echo __("clone pricequote");
        echo "</a>";
    }
    echo "\t</div>";
}
echo "<!--clone pricequote-->\n\n<!--split4-->\n<div class=\"split4\" style=\"padding-right: 0px;\">\n<!--split4-->\n\n\t<div class=\"box5\" style=\"min-height:500px;padding:0px; margin-right: 200px; overflow: visible;\">\n\n\t<!--pricequoteinfo-->\n\t<div class=\"invoicedata\" style=\"margin-right: -200px;\">\n\t<!--pricequoteinfo-->\n\t\t\n\t\t<strong>";
echo __("pricequote expirationdate");
echo "</strong><br />\n\t\t";
if(date("Ymd") <= substr(rewrite_date_site2db($pricequote->ExpirationDate), 0, 8) || $pricequote->Status < 2 || 4 <= $pricequote->Status) {
    echo "\t\t\t<span>";
    echo $pricequote->ExpirationDate;
    echo " (";
    echo $pricequote->Term;
    echo " ";
    echo __("days");
    echo ")</span>\n\t\t";
} else {
    echo "\t\t\t<span class=\"c6\">";
    echo $pricequote->ExpirationDate;
    echo " (";
    echo $pricequote->Term;
    echo " ";
    echo __("days");
    echo ")</span>\n\t\t";
}
echo "\t\t\n\t\t<br /><br />\n\t\t\n\t\t<strong>";
echo __("pricequote properties");
echo "</strong><br />\n\t\t";
echo $pricequote->Sent;
echo __("times sent");
echo "\t\t\n\t\t<br /><br />\n\t\t\n\t\t<strong>";
echo __("invoice options invoicemethod");
echo "</strong><br />\n\t\t";
echo $array_invoicemethod[$pricequote->PriceQuoteMethod];
echo " <a onclick=\"\$('#invoicedialog_invoicemethod').dialog('open');\" class=\"pointer c1\">";
echo __("change");
echo "</a>\n\t\t";
echo (int) $pricequote->PriceQuoteMethod === 0 || $pricequote->PriceQuoteMethod == 3 ? "<span class=\"sidebar_hooksmall\">" . str_replace("||", "</span><span class=\"sidebar_hooksmall\">", check_email_address($pricequote->EmailAddress, "convert", "||")) . "</span>" : "<br />";
echo "\t\t<br />\n\n\t\t";
if(in_array($pricequote->Status, [3, 4]) && !$pricequote->AcceptName && $pricequote->IPAddress) {
    echo "\t\t\t<strong>";
    echo __("ip address");
    echo "</strong>\n\t\t\t<br/>\n\t\t\t";
    echo $pricequote->IPAddress;
    echo "\t\t\t<br/>\n\t\t\t<br/>\n\t\t\t";
}
echo "\t\t\n\t\t<strong>";
echo __("history");
echo "</strong>\n\t\t<hr />\n\t\t";
$logcounter = 0;
foreach ($history as $k => $value) {
    if($logcounter == 3 || !is_numeric($k)) {
        if(isset($history["CountRows"]) && 0 < $history["CountRows"]) {
            echo "\t\t\t<p class=\"align_center\"><a onclick=\"\$('#pricequotedialog_history').dialog('open');\" class=\"c1 a1\">";
            echo __("more");
            echo "</a></p>\n\t\t\t";
        } elseif(!isset($history["CountRows"]) || (int) $history["CountRows"] === 0) {
            echo "\t\t\t<p>";
            echo __("no history available");
            echo "</p>\n\t\t\t";
        }
        echo "\t\t<br />\n\n\t<!--pricequoteinfo-->\n\t</div>\n\t<!--pricequoteinfo-->\n\n\t";
        if(in_array($pricequote->Status, [3, 4]) && $pricequote->AcceptName) {
            echo "\t\t<div class=\"split3\" style=\"background: #FBFBFB; padding: 20px 30px 0px 30px;\">\n\n\t\t\t<a href=\"download.php?type=pricequote_accepted&id=";
            echo $pricequote->AcceptPDF;
            echo "\" class=\"c1 a1\" style=\"float:right;\">";
            echo __("pricequote downloadlink signed PDF");
            echo "</a>\n\t\t\t<br class=\"clear\" />\n\n\t\t\t<div class=\"left\">\n\n\t\t\t\t<div class=\"noback\" style=\"margin-bottom: 0px; padding-right:0; padding-top:0;\">\n\t\t\t\t\t<strong class=\"title2_value\">";
            echo sprintf(__("pricequote accepted online on"), rewrite_date_db2site($pricequote->AcceptDate, "%d-%m-%Y"), rewrite_date_db2site($pricequote->AcceptDate, "%H:%i"));
            echo "</strong>\n\n\t\t\t\t\t<span class=\"title2\">";
            echo __("name");
            echo ":</span>\n\t\t\t\t\t<span class=\"title2_value\">";
            echo $pricequote->AcceptName;
            echo "</span>\n\n\t\t\t\t\t<span class=\"title2\">";
            echo __("emailaddress");
            echo ":</span>\n\t\t\t\t\t<div class=\"title2_value\" style=\"margin-left: 140px;\">";
            echo check_email_address($pricequote->AcceptEmailAddress, "convert", ", ");
            echo "</div>\n\n\t\t\t\t\t";
            if($pricequote->AcceptComment) {
                echo "\t\t\t\t\t\t<span class=\"title2\">";
                echo __("remark");
                echo ":</span>\n\t\t\t\t\t\t<span class=\"title2_value\" style=\"margin-left: 140px;\">";
                echo nl2br($pricequote->AcceptComment);
                echo "</span>\n\t\t\t\t\t\t";
            }
            echo "\n\t\t\t\t\t<span class=\"title2\">";
            echo __("ip address");
            echo ":</span>\n\t\t\t\t\t<span class=\"title2_value\">";
            echo $pricequote->AcceptIPAddress;
            echo "</span>\n\n\t\t\t\t\t<a class=\"a1 c1\" onclick=\"\$('#pricequote-accept-info-toggle').show(); \$(this).hide();\">";
            echo __("more info");
            echo "</a>\n\n\t\t\t\t\t<div id=\"pricequote-accept-info-toggle\" class=\"hide\">\n\t\t\t\t\t\t<span class=\"title2\">";
            echo __("user agent");
            echo ":</span>\n\t\t\t\t\t\t<div class=\"title2_value\" style=\"margin-left: 140px;\">";
            echo nl2br($pricequote->AcceptUserAgent);
            echo "</div>\n\t\t\t\t\t</div>\n\n\t\t\t\t</div>\n\n\t\t\t</div>\n\n\t\t\t<div class=\"right\">\n\n\t\t\t\t<div class=\"noback\" style=\"margin-bottom: 0px; padding-top:0;\">\n\t\t\t\t\t<span class=\"title2_value\">&nbsp;</span>\n\t\t\t\t\t<span class=\"\">";
            echo __("signature");
            echo ":</span>\n\t\t\t\t\t<br/>\n\t\t\t\t\t";
            if($pricequote->AcceptSignatureBase64) {
                echo "\t\t\t\t\t\t<img src=\"data:image/svg+xml;base64,";
                echo $pricequote->AcceptSignatureBase64;
                echo "\" height=\"100\"/>\n\t\t\t\t\t\t";
            }
            echo "\t\t\t\t</div>\n\n\t\t\t</div>\n\n\t\t</div>\n\t\t";
        }
        echo "\n\t<!--box5-->\n\t<div style=\"padding: 20px 30px 0px 30px;\">\n\t<!--box5-->\n\t\t\n\t\t<!--split3-->\n\t\t<div class=\"split3\">\n\t\t<!--split3-->\n\t\t\n\t\t\t<!--left-->\n\t\t\t<div class=\"left\">\n\t\t\t<!--left-->\n\n\t\t\t\t<!--back1-->\n\t\t\t\t<div class=\"noback\" style=\"margin-bottom: 0px;\">\n\t\t\t\t<!--back1-->\n\t\t\t\t\n\t\t\t\t\t\n\t\t\t\t\t<p>\n\t\t\t\t\t\t";
        if($pricequote->CompanyName) {
            echo "<strong><a href=\"debtors.php?page=show&amp;id=";
            echo $pricequote->Debtor;
            echo "\" class=\"c1 a1\">";
            echo $pricequote->CompanyName;
            echo "</a></strong><br />";
        }
        echo "\t\t\t\t\t\t\n\t\t\t\t\t\t";
        if(!$pricequote->CompanyName) {
            echo "<a href=\"debtors.php?page=show&amp;id=";
            echo $pricequote->Debtor;
            echo "\" class=\"c1 a1\">";
        }
        echo "\t\t\t\t\t\t";
        echo $pricequote->Initials . " " . $pricequote->SurName;
        echo "\t\t\t\t\t\t";
        if(!$pricequote->CompanyName) {
            echo "</a>";
        }
        echo "<br />\n\t\t\t\t\t\t";
        echo $pricequote->Address;
        echo "<br />\n\t\t\t\t\t\t";
        if(IS_INTERNATIONAL && $pricequote->Address2) {
            echo $pricequote->Address2 . "<br />";
        }
        echo "\t\t\t\t\t\t";
        echo $pricequote->ZipCode . "&nbsp;&nbsp;" . $pricequote->City;
        echo "<br />\n\t\t\t\t\t\t";
        if(IS_INTERNATIONAL && $pricequote->StateName) {
            echo $pricequote->StateName . "<br />";
        }
        echo "\t\t\t\t\t\t";
        echo $array_country[$pricequote->Country];
        echo " <br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t";
        if(isset($debtor->TaxNumber) && $debtor->TaxNumber != "") {
            echo "<br />" . __("vat number") . ": " . $debtor->TaxNumber;
        }
        echo "\t\t\t\t\t</p>\n\t\t\t\t\n\t\t\t\t<!--back1-->\n\t\t\t\t</div>\n\t\t\t\t<!--back1-->\n\t\t\t\n\t\t\t<!--left-->\n\t\t\t</div>\n\t\t\t<!--left-->\n\t\t\t\n\t\t\t<!--right-->\n\t\t\t<div class=\"right\" style=\"position: absolute; bottom: 0px; right: 0;\">\n\t\t\t<!--right-->\n\t\t\t\n\t\t\t\t<!--back3-->\n\t\t\t\t<div class=\"noback\" style=\"padding-left: 0px; margin-bottom: 0px;\">\n\t\t\t\t<!--back3-->\n\t\t\t\t\n\t\t\t\t<table class=\"table3\">\n\t\t\t\t<tr>\n\t\t\t\t\t<td style=\"width:105px;\">";
        echo __("debtorcode");
        echo ":</td>\n\t\t\t\t\t<td><a href=\"debtors.php?page=show&amp;id=";
        echo $pricequote->Debtor;
        echo "\" class=\"c1 a1\">";
        echo $debtor->DebtorCode;
        echo "</a></td>\n\t\t\t\t</tr>\n\t\t\t\t<tr>\n\t\t\t\t\t<td>";
        echo __("pricequotecode");
        echo ":</td>\n\t\t\t\t\t<td>";
        echo $pricequote->PriceQuoteCode;
        echo "</td>\n\t\t\t\t</tr>\n\t\t\t\t<tr>\n\t\t\t\t\t<td>";
        echo __("date");
        echo ":</td>\n\t\t\t\t\t<td>";
        echo $pricequote->Date;
        echo "</td>\n\t\t\t\t</tr>\n\t\t\t\t";
        if($pricequote->ReferenceNumber) {
            echo "\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td colspan=\"2\">&nbsp;</td>\n\t\t\t\t\t</tr>\n\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td>";
            echo __("reference no");
            echo ":</td>\n\t\t\t\t\t\t<td>";
            echo $pricequote->ReferenceNumber;
            echo "</td>\n\t\t\t\t\t</tr>\n\t\t\t\t\t";
        }
        echo "\t\t\t\t</table>\n\t\n\t\t\t\t<!--back3-->\n\t\t\t\t</div>\n\t\t\t\t<!--back3-->\n\t\n\t\t\t<!--right-->\n\t\t\t</div>\n\t\t\t<!--right-->\n\t\t\t\n\t\t<!--split3-->\n\t\t</div>\n\t\t<!--split3-->\n\n        ";
        if(!empty($pricequote->customfields_list)) {
            echo "            <div class=\"lineheight_text\" style=\"margin-left:15px;\">\n                <div class=\"field_row\">\n                    <strong>";
            echo __("custom debtor fields h2");
            echo "</strong>\n                </div>\n\n                ";
            foreach ($pricequote->customfields_list as $k => $custom_field) {
                echo show_custom_field_invoice($custom_field, isset($pricequote->custom->{$custom_field["FieldCode"]}) ? $pricequote->custom->{$custom_field["FieldCode"]} : "");
            }
            echo "\n            </div>\n            ";
        }
        echo "\n\t\t<table class=\"table1 alt1 noborder\" cellpadding=\"0\" cellspacing=\"0\">\n\t\t<tr class=\"trtitle\">\n\t\t\t<th scope=\"col\">";
        echo __("date");
        echo "</th>\n\t\t\t<th scope=\"col\">";
        echo __("number");
        echo "</th>\n\t\t\t<th scope=\"col\">";
        echo __("productcode");
        echo "</th>\n\t\t\t<th scope=\"col\">";
        echo __("description");
        echo "</th>\n\t\t\t";
        if(!empty($array_taxpercentages)) {
            echo "<th scope=\"col\">";
            echo __("vat");
            echo "</th>";
        }
        echo "\t\t\t<th scope=\"col\" colspan=\"2\">";
        echo __("price per unit");
        echo "</th>\n\t\t\t<th scope=\"col\" colspan=\"2\">";
        if(empty($array_taxpercentages)) {
            echo __("line total");
        } elseif($pricequote->VatCalcMethod == "incl") {
            echo __("total incl");
        } else {
            echo __("total excl");
        }
        echo "</th>\n\t\t</tr>\n\t\t";
        foreach ($pricequote->Elements as $k => $element) {
            if(is_numeric($k)) {
                echo "\t\t\t<tr class=\"tr2 valign_top ";
                if(isEmptyFloat($element["DiscountPercentage"])) {
                    echo "tr_invoice";
                }
                echo "\">\n\t\t\t\t<td style=\"width:70px;\">";
                if(!isEmptyFloat($element["PriceExcl"]) || $element["ProductCode"]) {
                    echo $element["Date"];
                } else {
                    echo "&nbsp;";
                }
                echo "</td>\n\t\t\t\t<td style=\"width:45px;\">";
                if(!isEmptyFloat($element["PriceExcl"]) || $element["ProductCode"]) {
                    echo showNumber($element["Number"]) . $element["NumberSuffix"];
                } else {
                    echo "&nbsp;";
                }
                echo "</td>\n\t\t\t\t\n\t\t\t\t";
                if(defined("SHOW_PRODUCTNAME") && SHOW_PRODUCTNAME === true) {
                    echo "\t\t\t\t\t<td>";
                    echo $element["ProductCode"] . " - " . $element["ProductName"];
                    echo "</td>\n\t\t\t\t";
                } else {
                    echo "\t\t\t\t\t<td style=\"width:50px;\">";
                    echo $element["ProductCode"];
                    echo "</td>\n\t\t\t\t";
                }
                echo "\t\t\t\t\n\t\t\t\t<td>";
                echo nl2br($element["Description"]);
                echo "\t\t\t\t\t";
                if($element["Periodic"]) {
                    echo "<br />" . __("period") . ": " . $element["StartPeriod"] . " " . __("till") . " " . $element["EndPeriod"] . " (" . $element["Periods"] . " " . (1 < $element["Periods"] ? $array_periodesMV[$element["Periodic"]] : $array_periodes[$element["Periodic"]]) . ")";
                }
                echo "\t\t\t\t</td>\n\t\t\t\t";
                if(!empty($array_taxpercentages)) {
                    echo "\t\t\t\t<td style=\"width:20px;\" class=\"align_right\">\n\t\t\t\t\t";
                    if(!isEmptyFloat($element["PriceExcl"]) || $element["ProductCode"]) {
                        echo showNumber($element["TaxPercentage"] * 100) . "%";
                    } else {
                        echo "&nbsp;";
                    }
                    echo "\t\t\t\t</td>\n\t\t\t\t";
                }
                echo "\t\t\t\t<td style=\"width:5px;\" class=\"currency_sign_left\">";
                if(!isEmptyFloat($element["PriceExcl"]) || $element["ProductCode"]) {
                    echo currency_sign_td(CURRENCY_SIGN_LEFT);
                } else {
                    echo "&nbsp;";
                }
                echo "</td>\n\t\t\t\t<td style=\"width:65px;\" class=\"align_right currency_sign_right\">\n\t\t\t\t\t";
                if(!isEmptyFloat($element["PriceExcl"]) || $element["ProductCode"]) {
                    if($pricequote->VatCalcMethod == "incl") {
                        echo money($element["PriceExcl"] * round(1 + $element["TaxPercentage"], 3), false);
                    } else {
                        echo money($element["PriceExcl"], false);
                    }
                    if(CURRENCY_SIGN_RIGHT) {
                        echo " " . CURRENCY_SIGN_RIGHT;
                    }
                } else {
                    echo "&nbsp;";
                }
                echo "\t\t\t\t</td>\n\t\t\t\t<td style=\"width:5px;\" class=\"currency_sign_left\">";
                if(!isEmptyFloat($element["PriceExcl"]) || $element["ProductCode"]) {
                    echo currency_sign_td(CURRENCY_SIGN_LEFT);
                } else {
                    echo "&nbsp;";
                }
                echo "</td>\n\t\t\t\t<td style=\"width:75px;\" class=\"align_right currency_sign_right\">\n\t\t\t\t\t";
                if(!isEmptyFloat($element["PriceExcl"]) || $element["ProductCode"]) {
                    if($pricequote->VatCalcMethod == "incl") {
                        echo money($element["PriceExcl"] * $element["Periods"] * $element["Number"] * round(1 + $element["TaxPercentage"], 3), false);
                    } else {
                        echo money($element["PriceExcl"] * $element["Periods"] * $element["Number"], false);
                    }
                    if(CURRENCY_SIGN_RIGHT) {
                        echo " " . CURRENCY_SIGN_RIGHT;
                    }
                } else {
                    echo "&nbsp;";
                }
                echo "\t\t\t\t</td>\n\t\t\t</tr>\n\t\t\t\n\t\t\t";
                if(isset($element["DiscountPercentage"]) && !isEmptyFloat($element["DiscountPercentage"])) {
                    $discount = -1 * ($pricequote->VatCalcMethod == "incl" ? $element["PriceExcl"] * $element["Periods"] * $element["Number"] * $element["DiscountPercentage"] * round(1 + $element["TaxPercentage"], 3) : $element["PriceExcl"] * $element["Periods"] * $element["Number"] * $element["DiscountPercentage"]);
                    if(0 < $discount) {
                        $discount = number_format($discount + 0, 2, ".", "");
                    } elseif($discount < 0) {
                        $discount = number_format($discount - 0, 2, ".", "");
                    }
                    echo "\t\t\t\t<tr class=\"tr2 valign_top tr_invoice\">\n\t\t\t\t\t<td colspan=\"3\">&nbsp;</td>\n\t\t\t\t\t<td><i>";
                    echo sprintf($element["DiscountPercentageType"] == "subscription" ? __("x discount on pricequoteline and subscription") : __("x discount on pricequoteline"), showNumber(round($element["DiscountPercentage"] * 100, 2)));
                    echo "</i></td>\n\t\t\t\t\t<td>&nbsp;</td>\n\t\t\t\t\t<td>&nbsp;</td>\n\t\t\t\t\t";
                    if(!empty($array_taxpercentages)) {
                        echo "<td>&nbsp;</td>";
                    }
                    echo "\t\t\t\t\t<td class=\"currency_sign_left\">";
                    echo currency_sign_td(CURRENCY_SIGN_LEFT);
                    echo "</td>\n\t\t\t\t\t<td class=\"currency_sign_right\" align=\"right\">";
                    echo money($discount, false);
                    if(CURRENCY_SIGN_RIGHT) {
                        echo " " . CURRENCY_SIGN_RIGHT;
                    }
                    echo "</td>\n\t\t\t\t</tr>\n\t\t\t";
                }
                echo "\t\t\n\t\t";
            }
        }
        echo "\t\t\n\t\t";
        if(!isEmptyFloat($pricequote->Discount)) {
            echo "\t\t<tr class=\"tr2 valign_top\">\n\t\t\t<td colspan=\"";
            echo !empty($array_taxpercentages) ? 9 : 8;
            echo "\">&nbsp;</td>\n\t\t</tr>\n\t\t<tr class=\"tr2 valign_top\">\n\t\t\t<td colspan=\"3\">&nbsp;</td>\n\t\t\t<td><strong>";
            echo showNumber($pricequote->Discount);
            echo "% ";
            echo __("discount on pricequote");
            echo "</strong></td>\n\t\t\t<td>&nbsp;</td>\n\t\t\t<td>&nbsp;</td>\n\t\t\t";
            if(!empty($array_taxpercentages)) {
                echo "<td>&nbsp;</td>";
            }
            echo "\t\t\t<td class=\"currency_sign_left\">";
            echo currency_sign_td(CURRENCY_SIGN_LEFT);
            echo "</td>\n\t\t\t<td class=\"currency_sign_right\" align=\"right\">";
            if($pricequote->VatCalcMethod == "incl") {
                echo $pricequote->AmountDiscountIncl;
            } else {
                echo $pricequote->AmountDiscount;
            }
            if(CURRENCY_SIGN_RIGHT) {
                echo " " . CURRENCY_SIGN_RIGHT;
            }
            echo "</td>\n\t\t</tr>\n\t\t";
        }
        echo "\t\t</table>\n\t\n\t\t<!--box6-->\n\t\t<div class=\"box6\">\n\t\t<!--box6-->\n\t\t\n\t\t\t<table class=\"table7\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\n\t\t\t\n\t\t\t";
        if(!empty($array_taxpercentages) || !empty($array_total_taxpercentages)) {
            echo "\t\t\t\t<tr>\n\t\t\t\t\t<td style=\"border-top: 1px solid black;\">";
            echo __("invoice total excl vat");
            echo "</td>\n\t\t\t\t\t<td style=\"border-top: 1px solid black;width:15px;\" class=\"currency_sign_left\">";
            echo currency_sign_td(CURRENCY_SIGN_LEFT);
            echo "</td>\n\t\t\t\t\t<td style=\"border-top: 1px solid black;width:75px;\" class=\"align_right currency_sign_right\">";
            echo $pricequote->AmountExcl;
            if(CURRENCY_SIGN_RIGHT) {
                echo " " . CURRENCY_SIGN_RIGHT;
            }
            echo "</td>\n\t\t\t\t</tr>\n\t\t\t\t";
        }
        global $array_taxpercentages;
        global $array_taxpercentages_info;
        asort($array_taxpercentages);
        foreach ($array_taxpercentages as $key => $value) {
            if(isset($pricequote->used_taxrates[(string) (double) $key]["AmountTax"]) && 0 < (double) $key) {
                echo "\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td>";
                echo isset($array_taxpercentages_info[(string) (double) $key]["label"]) ? $array_taxpercentages_info[(string) (double) $key]["label"] : "";
                echo "</td>\n\t\t\t\t\t\t<td class=\"currency_sign_left\">";
                echo currency_sign_td(CURRENCY_SIGN_LEFT);
                echo "</td>\n\t\t\t\t\t\t<td class=\"align_right currency_sign_right\">";
                echo $pricequote->used_taxrates[(string) (double) $key]["AmountTax"];
                if(CURRENCY_SIGN_RIGHT) {
                    echo " " . CURRENCY_SIGN_RIGHT;
                }
                echo "</td>\n\t\t\t\t\t</tr>\n\t\t\t\t\t";
            }
        }
        if(isset($pricequote->TaxRate_Label) && $pricequote->TaxRate_Label) {
            echo "\t\t\t\t<tr>\n\t\t\t\t\t<td>";
            echo $pricequote->TaxRate_Label;
            echo "</td>\n\t\t\t\t\t<td class=\"currency_sign_left\">";
            echo currency_sign_td(CURRENCY_SIGN_LEFT);
            echo "</td>\n\t\t\t\t\t<td class=\"align_right currency_sign_right\">";
            echo $pricequote->TaxRate_Amount;
            if(CURRENCY_SIGN_RIGHT) {
                echo " " . CURRENCY_SIGN_RIGHT;
            }
            echo "</td>\n\t\t\t\t</tr>\n\t\t\t\t";
        }
        echo "\n\t\t\t<tr class=\"line\">\n\t\t\t\t<td style=\"border-top: 1px solid black;\">";
        if(empty($array_taxpercentages) && empty($array_total_taxpercentages)) {
            echo __("pricequote total");
        } else {
            echo __("invoice total incl vat");
        }
        echo "</td>\n\t\t\t\t<td style=\"border-top: 1px solid black;\" class=\"currency_sign_left\">";
        echo currency_sign_td(CURRENCY_SIGN_LEFT);
        echo "</td>\n\t\t\t\t<td style=\"border-top: 1px solid black;\" class=\"align_right currency_sign_right\">";
        echo $pricequote->AmountIncl;
        if(CURRENCY_SIGN_RIGHT) {
            echo " " . CURRENCY_SIGN_RIGHT;
        }
        echo "</td>\n\t\t\t</tr>\n\t\t\t</table>\n\t\t\n\t\t<!--box6-->\n\t\t</div>\n\t\t<!--box6-->\n\t\n\t\t";
        if(isset($show_vatshift_text) && $show_vatshift_text !== false) {
            echo "\t\t\t<div class=\"vatshift_text_view\" style=\"margin-top: 70px\">";
            echo $show_vatshift_text;
            echo "</div>\t\t\t\t\t\t\n\t\t";
        }
        echo "\t\t\n\t<!--box5-->\n\t</div>\n\t<!--box5-->\n\t\n\t<br clear=\"both\"/>\n\t\t\n\t</div>\n\t\n<!--split4-->\n</div>\n<!--split4-->\n\n<!--box1-->\n<div class=\"box2\" id=\"tabs\">\n<!--box1-->\n\n\t<!--top-->\n\t<div class=\"top\">\n\t<!--top-->\n\t\n\t\t<ul class=\"list3\">\n\t\t\t<li class=\"on\"><a href=\"#tab-extra\">";
        echo __("invoice options");
        echo "</a></li>\n\t\t\t";
        if($pricequote->Description) {
            echo "<li><a href=\"#tab-comment\">";
            echo __("description");
            echo " ";
            if($pricequote->Description) {
                echo "<span class=\"ico actionblock info nm\">";
                echo __("more information");
                echo "</span>";
            }
            echo "</a></li>";
        }
        echo "\t\t\t";
        if(in_array($pricequote->Status, [3, 4]) && !$pricequote->AcceptName && $pricequote->Reason) {
            echo "<li><a href=\"#tab-reason\">";
            echo __("clientarea comment");
            echo "</a></li>";
        }
        echo "\t\t\t<li><a href=\"#tab-note\">";
        echo __("internal note");
        echo " ";
        if($pricequote->Comment) {
            echo "<span class=\"ico actionblock info nm\">";
            echo __("more information");
            echo "</span>";
        }
        echo "</a></li>\n\t\t</ul>\n\t\n\t<!--top-->\n\t</div>\n\t<!--top-->\n\t\n\t\t<!--content-->\n\t<div class=\"content\" id=\"tab-extra\">\n\t<!--content-->\n\t\n\t\t<!--split2-->\n\t\t<div class=\"split2\">\n\t\t<!--split2-->\n\t\t\n\t\t\t<!--left-->\n\t\t\t<div class=\"left\">\n\t\t\t<!--left-->\n\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
        echo __("invoice options send");
        echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title2\">";
        echo __("pricequote template");
        echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
        echo $template->Name;
        echo "</span>\n\t\t\t\t\t\n\t\t\t\t\t";
        if($pricequote->Sent) {
            echo "\t\t\t\t\t<strong class=\"title2\">";
            echo __("sentdate pricequote");
            echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
            echo rewrite_date_db2site($pricequote->SentDate);
            echo "</span>\n\t\t\t\t\t";
        }
        echo "\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3";
        if(empty($pricequote->Attachment)) {
            echo " hide";
        }
        echo "\"><h3>";
        echo __("pricequote attachments");
        echo "</h3><div class=\"content\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t<div id=\"files_list\" class=\"hide\">\n\t\t\t\t\t\t<p class=\"align_right mar4\"><i>";
        echo __("total");
        echo ": <span id=\"files_total\"></span></i></p>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<ul id=\"files_list_ul\" class=\"emaillist\">\n\t\t\t\t\t\t";
        $attachCounter = 0;
        if(!empty($pricequote->Attachment)) {
            echo "\t\t\t\t\t\t\t";
            foreach ($pricequote->Attachment as $key => $file) {
                echo "\t\t\t\t\t\t\t<li>\n\t\t\t\t\t\t\t\t<input type=\"hidden\" name=\"File[]\" value=\"";
                echo $file->id;
                echo "\" />\n\t\t\t\t\t\t\t\t<div class=\"file ico inline file_";
                echo getFileType($file->FilenameServer);
                echo "\">&nbsp;</div> <a href=\"download.php?type=pricequote&amp;id=";
                echo $file->id;
                echo "\" class=\"a1\" target=\"_blank\">";
                echo $file->Filename;
                echo "</a>\n\t\t\t\t\t\t\t\t<div class=\"filesize\">";
                $fileSizeUnit = getFileSizeUnit($file->Size);
                echo $fileSizeUnit["size"] . " " . $fileSizeUnit["unit"];
                echo "</div>\n\t\t\t\t\t\t\t</li>\n\t\t\t\t\t\t\t";
                $attachCounter++;
            }
            echo "\t\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\t</ul>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<br />\n\t\t\t\t\t</div>\n\t\t\t\t\t\n\t\t\t\t\t<p><i id=\"files_none\" ";
        if($attachCounter !== 0) {
            echo "class=\"hide\"";
        }
        echo ">";
        echo __("no attachments");
        echo "</i></p>\n\t\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t<!--left-->\n\t\t\t</div>\n\t\t\t<!--left-->\n\t\t\t\n\t\t\t<!--right-->\n\t\t\t<div class=\"right\">\n\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
        echo __("pricequote options expiration");
        echo "</h3><div class=\"content lineheight2 label_medium\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title2\">";
        echo __("pricequote options term");
        echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
        echo $pricequote->Term;
        echo " ";
        echo __("days");
        echo "</span>\n\n\t\t\t\t\t";
        if($pricequote->Coupon) {
            echo "\t\t\t\t\t<strong class=\"title2\">";
            echo __("coupon");
            echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
            echo $pricequote->Coupon;
            echo "</span>\n\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\n\t\t\t\t\t";
        if($pricequote->IgnoreDiscount == 1) {
            echo "\t\t\t\t\t<strong class=\"title2\">";
            echo __("ignore discount");
            echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
            echo __("yes");
            echo "</span>\n\t\t\t\t\t";
        }
        echo "\n\t\t\t\t\t\t";
        if(PDF_MODULE == "tcpdf" && in_array($pricequote->Status, [2])) {
            echo "\t\t\t\t\t\t\t<strong class=\"title2\">";
            echo __("pricequote accept link");
            echo "</strong>\n\t\t\t\t\t\t\t<span class=\"title2_value\">\n\t\t\t\t\t\t\t\t<a onclick=\"\$('#pricequote-accept-url').removeClass('hide').select();\$(this).hide();\"  class=\"c1 pointer\">\n\t\t\t\t\t\t\t\t\t";
            echo __("show pricequote accept url");
            echo "\t\t\t\t\t\t\t\t</a>\n\t\t\t\t\t\t\t</span>\n\n\t\t\t\t\t\t\t<span class=\"title2_value\" style=\"padding-right: 10px;\">\n\t\t\t\t\t\t\t\t<input id=\"pricequote-accept-url\" class=\"text1 size9 hide\" type=\"text\" value=\"";
            echo $pricequote->AcceptURLRaw;
            echo "\" />\n\t\t\t\t\t\t\t</span>\n\n\t\t\t\t\t\t\t";
        }
        echo "\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\n\t\t\t<!--right-->\n\t\t\t</div>\n\t\t\t<!--right-->\n\t\t\t\n\t\t<!--split2-->\n\t\t</div>\n\t\t<!--split2-->\n\t\t\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\n\t";
        if($pricequote->Description) {
            echo "\t<!--content-->\n\t<div class=\"content\" id=\"tab-comment\">\n\t<!--content-->\n\n\t\t<!--box3-->\n\t\t<div class=\"box3\"><h3>";
            echo __("description");
            echo "</h3><div class=\"content\" style=\"overflow-x: auto;\">\n\t\t<!--box3-->\n\t\t\n\t\t\t";
            echo $pricequote->Description;
            echo "\n\t\t<!--box3-->\n\t\t</div></div>\n\t\t<!--box3-->\n\t\t\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\t";
        }
        echo "\n\t";
        if(in_array($pricequote->Status, [3, 4]) && !$pricequote->AcceptName && $pricequote->Reason) {
            echo "\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-reason\">\n\t\t\t<!--content-->\n\n\t\t\t<!--box3-->\n\t\t\t<div class=\"box3\"><h3>";
            echo __("clientarea comment");
            echo "</h3><div class=\"content\" style=\"overflow-x: auto;\">\n\t\t\t\t\t<!--box3-->\n\n\t\t\t\t\t";
            echo nl2br($pricequote->Reason);
            echo "\n\t\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t<!--box3-->\n\n\t\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\t";
        }
        echo "\t\n\t<!--content-->\n\t<div class=\"content\" id=\"tab-note\">\n\t<!--content-->\n\t\t\n\t\t<!--box3-->\n\t\t<div class=\"box3\"><h3>";
        echo __("internal note");
        echo "</h3><div class=\"content lineheight2\">\n\t\t<!--box3-->\n\t\t\t\n\t\t\t<form action=\"pricequotes.php?page=show&id=";
        echo $pricequote->Identifier;
        echo "\" method=\"post\" name=\"pricequote_comment_form\">\n\t\t\t\n\t\t\t<textarea class=\"text1 size5 autogrow\" name=\"Comment\">";
        echo $pricequote->Comment;
        echo "</textarea>\n\t\t\t\n\t\t\t<br />\n\t\t\t\n\t\t\t";
        if(U_PRICEQUOTE_EDIT) {
            echo "\t\t\t<a class=\"button1 alt1 margint\" href=\"javascript:document.pricequote_comment_form.submit();\"><span>";
            echo __("edit note");
            echo "</span></a>\n\t\t\t";
        }
        echo "\t\t\t</form>\n\t\t\t\n\t\t<!--box3-->\n\t\t</div></div>\n\t\t<!--box3-->\n\t\t\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\t\n<!--box1-->\n</div>\n<!--box1-->\n\n\n";
        if(U_PRICEQUOTE_DELETE) {
            echo " \n<div id=\"delete_pricequote\" class=\"hide\" title=\"";
            echo __("delete pricequote title");
            echo "\">\n\t<form id=\"PriceQuoteForm\" name=\"form_delete\" method=\"post\" action=\"pricequotes.php?page=delete&id=";
            echo $pricequote->Identifier;
            echo "\">\n\t<input type=\"hidden\" name=\"id\" value=\"";
            echo $pricequote->Identifier;
            echo "\"/>\n\t\n\t";
            echo __("delete this pricequote description");
            echo "<br />\n\t<br />\n\t\n\t<input type=\"radio\" id=\"deletetype_hide\" checked=\"checked\" value=\"hide\" name=\"deleteType\" /> <label for=\"deletetype_hide\">";
            echo __("expire pricequote, do not remove");
            echo "</label><br />\n\t<input type=\"radio\" id=\"deletetype_remove\" value=\"remove\" name=\"deleteType\" /> <label for=\"deletetype_remove\">";
            echo __("completely delete pricequote");
            echo "</label>\n\t\n\t<br /><br />\n\t<strong>";
            echo __("sure to delete this pricequote");
            echo "</strong><br />\n\t<input type=\"checkbox\" id=\"imsure\" name=\"imsure\" value=\"yes\"/> <label for=\"imsure\">";
            echo __("delete this pricequote");
            echo "</label><br />\n\t<br />\n                \n\t<p><a id=\"delete_pricequote_btn\" class=\"button2 alt1 float_left\"><span>";
            echo __("delete");
            echo "</span></a></p>\n\t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#delete_pricequote').dialog('close');\"><span>";
            echo __("cancel");
            echo "</span></a></p>\n\n\t";
            if(isset($_SESSION["ActionLog"]) && isset($_SESSION["ActionLog"]["PriceQuote"]) && is_array($_SESSION["ActionLog"]["PriceQuote"]["delete"]) && 1 < count($_SESSION["ActionLog"]["PriceQuote"]["delete"])) {
                echo "\t\t<br class=\"clear\"/><br />\n\t\t<b>";
                echo __("progress batch actions");
                echo "</b><br />\n\t\t";
                if(count($_SESSION["ActionLog"]["PriceQuote"]["delete"]) - 1 != 1) {
                    echo sprintf(__("progress batch multiple"), count($_SESSION["ActionLog"]["PriceQuote"]["delete"]) - 1);
                } else {
                    echo sprintf(__("progress batch one"), count($_SESSION["ActionLog"]["PriceQuote"]["delete"]) - 1);
                }
                echo "\t";
            }
            echo "\t</form>\n</div>\n";
        }
        echo "\n";
        if($pricequote->Status == 3) {
            echo "<div class=\"hide\" id=\"dialog_makeinvoice\" title=\"";
            echo __("action makeinvoice");
            echo "\">\n\t";
            echo __("dialog accept makeinvoice title single");
            echo "<br /><br />\n\t<form name=\"form_dialog_makeinvoice\" method=\"post\" action=\"pricequotes.php?page=show&action=accept&id=";
            echo $pricequote->Identifier;
            echo "\">\n\t\t<label><input type=\"checkbox\" name=\"usepricequoteasinvoiceref\" value=\"yes\"/> ";
            echo __("use pricequotecode as reference");
            echo "</label><br /><br />\n\t\t\n\t\t<p><a class=\"button1 alt1 float_left\" id=\"dialog_makeinvoice_btn\"><span>";
            echo __("proceed");
            echo "</span></a></p>\n\t\t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#dialog_makeinvoice').dialog('close');\"><span>";
            echo __("cancel");
            echo "</span></a></p>\n\t</form>\n</div>\n";
        }
        echo "\n";
        if(U_PRICEQUOTE_EDIT && !empty($pricequote->Attachment)) {
            echo "\t<div id=\"invoicedialog_clone\" class=\"hide\" title=\"";
            echo __("clone pricequote");
            echo "\">\n\t\t<form name=\"form_clone\" method=\"post\" action=\"pricequotes.php?page=add&clone=";
            echo $pricequote->Identifier;
            echo "\">\n\t\t\n\t\t";
            echo __("clone pricequote with or without attachments");
            echo "<br />\n\t\t\n\t\t";
            foreach ($pricequote->Attachment as $key => $value) {
                echo "\t\t<label><input type=\"checkbox\" name=\"copyAttachments[]\" value=\"";
                echo $value->id;
                echo "\" /> ";
                echo $value->Filename;
                echo "</label><br />\n\t\t";
            }
            echo "\n\t\t<br />\n\t\t<p><a class=\"button1 alt1 float_left\" id=\"clone_btn\"><span>";
            echo __("clone pricequote btn");
            echo "</span></a></p>\n\t\t<p style=\"line-height:30px\"><a class=\"a1 c1 float_right\" onclick=\"\$('#invoicedialog_clone').dialog('close');\"><span>";
            echo __("cancel");
            echo "</span></a></p>\n\t\t</form>\n\t</div>\n\t";
        }
        echo "\n";
        if($pricequote->Status == 2) {
            echo "<div class=\"hide\" id=\"dialog_accept_pricequote\" title=\"";
            echo __("accept pricequote title");
            echo "\">\n\t";
            echo __("dialog accept pricequote title single");
            echo "<br /><br />\n\t<form name=\"form_accept_pricequote\" method=\"post\" action=\"\">\n\t<input type=\"radio\" id=\"createinvoice_yes\" name=\"createinvoice\" value=\"yes\"/> <label for=\"createinvoice_yes\">";
            echo __("dialog accept pricequote yes");
            echo "</label><br />\n\t<input type=\"radio\" id=\"createinvoice_no\" name=\"createinvoice\" value=\"no\" checked=\"checked\"/> <label for=\"createinvoice_no\">";
            echo __("dialog accept pricequote no");
            echo "</label><br />\n\t<div class=\"accept_pricequote_options hide\">\n\t\t<br />\n\t\t<label><input type=\"checkbox\" name=\"usepricequoteasinvoiceref\" value=\"yes\"/> ";
            echo __("use pricequotecode as reference");
            echo "</label><br />    \n\t</div>\n\t<br />\n\t<p><a class=\"button1 alt1 float_left\" id=\"accept_pricequote_btn\"><span>";
            echo __("proceed");
            echo "</span></a></p>\n\t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#dialog_accept_pricequote').dialog('close');\"><span>";
            echo __("cancel");
            echo "</span></a></p>\n\t</form>\n</div>\n\n<div class=\"hide\" id=\"dialog_decline_pricequote\" title=\"";
            echo __("decline pricequote title");
            echo "\">\n\t";
            echo __("dialog decline pricequote title single");
            echo "<br /><br />\n\t<form name=\"form_decline_pricequote\" method=\"post\" action=\"\">\n\t<p><a class=\"button1 alt1 float_left\" id=\"decline_pricequote_btn\"><span>";
            echo __("proceed");
            echo "</span></a></p>\n\t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#dialog_decline_pricequote').dialog('close');\"><span>";
            echo __("cancel");
            echo "</span></a></p>\n\t</form>\n</div>\n";
        }
        echo "\n<div id=\"pricequotedialog_history\" class=\"hide\" title=\"";
        echo __("historydialog pricequote title");
        echo " ";
        echo $pricequote->PriceQuoteCode;
        echo "\">\n\t\n\t";
        echo __("historydialog pricequote description");
        echo "\t\n\t";
        require_once "views/elements/log.table.php";
        $options = ["form_action" => "pricequotes.php?page=show&amp;id=" . $pricequote->Identifier, "session_name" => "pricequote.show.logfile", "current_page" => $current_page, "current_page_url" => $current_page_url, "allow_delete" => U_PRICEQUOTE_DELETE, "show_icons" => true];
        show_log_table($history, $options);
        echo "\t\t\n\n\t<p><a class=\"button1 alt1 float_right\" onclick=\"\$('#pricequotedialog_history').dialog('close');\"><span>";
        echo __("close");
        echo "</span></a></p>\n</div>\n\n<div id=\"invoicedialog_invoicemethod\" class=\"hide\" title=\"";
        echo __("invoicemethoddialog title");
        echo "\">\n\t<form name=\"form_invoicemethod\" method=\"post\" action=\"pricequotes.php?page=show&action=changePriceQuoteMethod\">\n\t<input type=\"hidden\" name=\"id\" value=\"";
        echo $pricequote->Identifier;
        echo "\"/>\n\t";
        echo sprintf(__("invoicemethoddialog description pricequote"), $pricequote->PriceQuoteCode);
        echo "<br />\n\t<br />\n\n\t<span class=\"title2 lineheight_input\">";
        echo __("invoice method");
        echo ":</span>\n\t<span class=\"title2_value\">\n\t\t<select name=\"InvoiceMethod\" class=\"text1 size1\">\n\t\t\t";
        foreach ($array_invoicemethod as $key => $value) {
            echo "\t\t\t\t<option value=\"";
            echo $key;
            echo "\" ";
            if($key == $pricequote->PriceQuoteMethod) {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo $value;
            echo "</option>\n\t\t\t";
        }
        echo "\t\t</select>\n\t</span>\n\n\t";
        $invoice_emailaddress = "";
        if($pricequote->EmailAddress) {
            $invoice_emailaddress = $pricequote->EmailAddress;
        } elseif($debtor->InvoiceEmailAddress) {
            $invoice_emailaddress = $debtor->InvoiceEmailAddress;
        } elseif($debtor->EmailAddress) {
            $invoice_emailaddress = $debtor->EmailAddress;
        }
        echo "\t<div class=\"";
        if($pricequote->PriceQuoteMethod == "1") {
            echo "hide";
        }
        echo "\" id=\"NewMethodEmailAddress\">\n\t\t<span class=\"title2 lineheight_input\">";
        echo __("emailaddress");
        echo ":</span>\n\t\t<span class=\"title2_value\">\n\t\t\t<input type=\"text\" name=\"NewMethodEmailAddress\" value=\"";
        echo check_email_address($invoice_emailaddress, "convert", ", ");
        echo "\" class=\"text1 size1\" />\n\t\t</span>\n\t</div>\n\n\t<br />\n\t<p><a class=\"button1 alt1 float_left\" id=\"invoicemethod_btn\"><span>";
        echo __("invoicemethoddialog process");
        echo "</span></a></p>\n\t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#invoicedialog_invoicemethod').dialog('close');\"><span>";
        echo __("cancel");
        echo "</span></a></p>\n\t</form>\n</div>\n\n";
        if(isset($selected_tab) && $selected_tab) {
            echo "<script language=\"javascript\" type=\"text/javascript\">\n\$(function(){\n\t\$('#tabs').tabs(\"option\", \"active\", ";
            echo strlen($pricequote->Description) === 0 ? $selected_tab : $selected_tab + 1;
            echo ");\n});\n</script>\n";
        }
        echo "\n";
        require_once "views/footer.php";
    } else {
        $icon = "";
        switch ($value["Action"]) {
            case "pricequote printed":
            case "pricequote sent per post":
            case "pricequote downloaded via api":
            case "pricequote downloaded via clientarea":
                $icon = "ico_printsmall.png";
                break;
            case "pricequote adjusted":
            case "pricequote created":
                $icon = "ico_edit.png";
                break;
            case "pricequote sent per email":
                $icon = "ico_sendemail.png";
                break;
            case "invoice x created":
                $icon = "ico_quote2invoice.png";
                break;
            case "pricequote accepted":
            case "pricequote accepted from clientarea":
            case "pricequote accepted online":
                $icon = "ico_success.png";
                break;
            case "pricequote declined":
            case "pricequote declined from clientarea":
                $icon = "ico_cancelled.png";
                break;
            case "pricequotemethod and emailaddress changed":
            case "pricequotemethod changed":
                $icon = "ico_sendmethodchanged.png";
                break;
            default:
                echo "\t\t\t<div class=\"log\">\n\t\t\t\t";
                if($icon) {
                    echo "<img src=\"images/";
                    echo $icon;
                    echo "\" style=\"width:12px; height:12px;\" alt=\"\" />";
                } else {
                    echo "<span style=\"width:16px;display:inline-block;\">&nbsp;</span>";
                }
                echo "\t\t\t\t<span style=\"width: 162px; overflow:hidden;\">\n\t\t\t\t\t";
                echo rewrite_date_db2site($value["Date"], "%d-%m-%Y " . __("at") . " %H:%i");
                echo "<br />\n\t\t\t\t\t";
                if($value["Translate"] == "no") {
                    echo $value["Action"];
                } else {
                    $value["Action"] = __("log." . $value["Action"]);
                    $value["Values"] = explode("|", $value["Values"]);
                    if(strpos($value["Action"], "%s") && count($value["Values"]) < count(explode("%s", $value["Action"])) - 1) {
                        echo $value["Action"];
                    } else {
                        echo call_user_func_array("sprintf", array_merge([$value["Action"]], $value["Values"]));
                    }
                }
                echo "\t\t\t\t</span>\n\t\t\t\t<div class=\"clear\">&nbsp;</div>\n\t\t\t</div>\n\t\t\t\n\t\t\t<hr />\n\t\t\t\n\t\t\t";
                $logcounter++;
        }
    }
}

?>