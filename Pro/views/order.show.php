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
if(U_ORDER_EDIT && $order->Status <= 2) {
    echo "<ul class=\"list1\">\n\t\n\t";
    if(!in_array($order->PaymentMethod, ["ideal", "paypal", "other"]) || $order->Paid == 1 || rewrite_date_site2db($order->Date, DATE_FORMAT . " %H:%i") < date("YmdHis", time() - 3600)) {
        echo "\t<li><a class=\"ico set1 accept\" href=\"orders.php?page=show&action=makeinvoice&id=";
        echo $order->Identifier;
        echo "\">";
        echo __("push order");
        echo "</a></li>\n\t";
    }
    echo "\t";
    if(in_array($order->PaymentMethod, ["ideal", "paypal", "other"]) && isEmptyFloat($order->Paid)) {
        echo "\t<li><a class=\"ico set1 paid pointer\" onclick=\"\$('#dialog_order_paid').dialog('open');\">";
        echo __("action paid");
        echo "</a></li>\n\t";
    }
    echo "\t<li><a class=\"ico set1 invoice\" href=\"orders.php?page=edit&id=";
    echo $order->Identifier;
    echo "\">";
    echo __("action edit");
    echo "</a></li>\n\t";
    if(U_ORDER_DELETE) {
        echo "<li><a class=\"ico set1 decline\" onclick=\"\$('#delete_order').dialog('open');\">";
        echo __("action delete");
        echo "</a></li>";
    }
    echo "</ul>\n\n";
    if(U_ORDER_EDIT && $order->Status <= 2 && in_array($order->PaymentMethod, ["ideal", "paypal", "other"]) && isEmptyFloat($order->Paid)) {
        echo "<div id=\"dialog_order_paid\" title=\"";
        echo __("dialog order paid title");
        echo "\">\n\t<form name=\"form_orderpaid\" method=\"post\" action=\"orders.php?page=show&action=markaspaid&id=";
        echo $order->Identifier;
        echo "\">\n\t";
        echo __("dialog order paid intro");
        echo "<br />\n\t<br />\n\t<label>";
        echo __("transaction id");
        echo ":&nbsp;</label>\n\t<input type=\"text\" name=\"TransactionID\" value=\"";
        echo $order->TransactionID;
        echo "\" class=\"text1 size1\" /><br />\n\t<br />\n\t<strong>";
        echo __("dialog order paid process order title");
        echo "</strong><br />\n\t<label><input type=\"checkbox\" name=\"ProcessOrder\" value=\"yes\" class=\"text1 checkbox\" /> ";
        echo __("dialog order paid process order");
        echo "</label><br />\n\t<br />\n\n\t<p><a class=\"button1 alt1 float_left\" id=\"order_paid_btn\"><span>";
        echo __("proceed");
        echo "</span></a></p>\n\t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#dialog_order_paid').dialog('close');\"><span>";
        echo __("cancel");
        echo "</span></a></p>\n\t</form>\n</div>\n";
    }
    echo "\n<hr />\n";
}
echo "\n";
echo $message;
echo "\n<!--heading1-->\n<div class=\"heading1\">\n<!--heading1-->\n\n\t<h2>";
echo __("order");
echo " ";
echo $order->OrderCode;
echo " ";
if($order->Authorisation == "yes") {
    echo __("invoice authed");
}
if(0 < $order->Paid) {
    echo " - " . __("action paid");
}
echo "</h2>\n\n\t<p class=\"pos2\"><strong class=\"textsize1\">";
echo $array_orderstatus[$order->Status];
echo "</strong></p>\n\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n<!--split4-->\n<div class=\"split4\" style=\"padding-right: 0px;\">\n<!--split4-->\n\t\n\t<div class=\"box5\" style=\"min-height:390px;padding:0px; margin-right: 200px; overflow: visible;\">\n\t\n\t<!--orderinfo-->\n\t<div class=\"invoicedata\" style=\"margin-right: -200px;\">\n\t<!--orderinfo-->\n\t\t\n\t\t<strong>";
echo __("invoice options invoicemethod");
echo "</strong><br />\n\t\t";
echo $array_invoicemethod[$order->InvoiceMethod];
echo " <a onclick=\"\$('#invoicedialog_invoicemethod').dialog('open');\" class=\"pointer c1\">";
echo __("change");
echo "</a>\n\t\t";
echo (int) $order->InvoiceMethod === 0 || $order->InvoiceMethod == 3 ? "<span class=\"sidebar_hooksmall\">" . str_replace("||", "</span><span class=\"sidebar_hooksmall\">", check_email_address($order->EmailAddress, "convert", "||")) . "</span>" : "<br />";
echo "\t\t\n\t\t<br /><br />\n\t\t\n\t\t<strong>";
echo __("ip address");
echo "</strong><br />\n\t\t";
echo $order->IPAddress;
echo "\t\t<br /><br />\n\t\t\n\t\t\n\t\t<strong>";
echo __("information about payment");
echo "</strong><br />\n\t\t";
echo __("payment via");
echo " ";
echo isset($array_paymentmethod[$order->PaymentMethod]) ? $array_paymentmethod[$order->PaymentMethod] : $array_paymentmethod["wire"];
echo "<br />\n\t\t\n\t\t";
if($order->TransactionID || $order->PaymentMethod == "ideal" || $order->PaymentMethod == "paypal" || $order->PaymentMethod == "other") {
    echo "\t\t\t\n\t\t\t";
    echo __("transaction id");
    echo ": ";
    echo $order->TransactionID ? $order->TransactionID : __("unknown");
    echo "<br />\n\t\t\t";
    echo __("transaction status");
    echo ": ";
    echo 1 <= $order->Paid ? __("online transaction status ok") : __("online transaction status open");
    echo "<br />\n\t\t\t\n\t\t\t";
    if(U_ORDER_EDIT && empty($order->Paid) && $order->Status != 8) {
        echo "\t\t\t\t<a href=\"orders.php?page=show&amp;id=";
        echo $order->Identifier;
        echo "&amp;action=cancelonlinepayment\" class=\"c1 a1\">";
        echo __("cancel online payment");
        echo "</a><br />\n\t\t\t";
    }
    echo "\t\t\t\t\n\t\t\t<br />\n\t\t";
}
echo "\t\t\n\t<!--factuurinfo-->\n\t</div>\n\t<!--factuurinfo-->\n\t\n\t<!--box5-->\n\t<div style=\"padding: 20px 30px 0px 30px;\">\n\t<!--box5-->\n\t\t\n\t\t<!--split3-->\n\t\t<div class=\"split3\">\n\t\t<!--split3-->\n\t\t\n\t\t\t<!--left-->\n\t\t\t<div class=\"left\">\n\t\t\t<!--left-->\n\t\t\t\n\t\t\t\t<!--back1-->\n\t\t\t\t<div class=\"noback\" style=\"margin-bottom: 0px;\">\n\t\t\t\t<!--back1-->\n\t\t\t\t\t<p>\n\t\t\t\t\t";
if($order->Type == "debtor") {
    echo "\t\t\t\t\t\n\t\t\t\t\t\t";
    if($order->CompanyName) {
        echo "<strong><a href=\"debtors.php?page=show&amp;id=";
        echo $order->Debtor;
        echo "\" class=\"c1 a1\">";
        echo $order->CompanyName;
        echo "</a></strong><br />";
    }
    echo "\t\t\t\t\t\t";
    if(!$order->CompanyName) {
        echo "<a href=\"debtors.php?page=show&amp;id=";
        echo $order->Debtor;
        echo "\" class=\"c1 a1\">";
    }
    echo "\t\t\t\t\t\t\t";
    echo $order->Initials . " " . $order->SurName;
    echo "\t\t\t\t\t\t";
    if(!$order->CompanyName) {
        echo "</a>";
    }
    echo "<br />\n\t\t\t\t\t";
} else {
    echo "\t\t\t\t\t\t";
    if($order->CompanyName) {
        echo "<strong>";
        echo $order->CompanyName;
        echo "</strong><br />";
    }
    echo "\t\t\t\t\t\t";
    if($order->CompanyName) {
        echo __("attn") . " ";
    }
    echo $order->Initials . " " . $order->SurName;
    echo "<br />\n\t\t\t\t\t";
}
echo "\t\t\t\t\t\t";
echo $order->Address;
echo "<br />\n\t\t\t\t\t\t";
if(IS_INTERNATIONAL && $order->Address2) {
    echo $order->Address2 . "<br />";
}
echo "\t\t\t\t\t\t";
echo $order->ZipCode . "&nbsp;&nbsp;" . $order->City;
echo "<br />\n\t\t\t\t\t\t";
if(IS_INTERNATIONAL && $order->StateName) {
    echo $order->StateName . "<br />";
}
echo "\t\t\t\t\t\t";
echo $array_country[$order->Country];
echo " <br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t";
if(isset($debtor->TaxNumber) && $debtor->TaxNumber != "") {
    echo "<br />" . __("vat number") . ": " . $debtor->TaxNumber;
}
echo "\t\t\t\t\t</p>\n\t\t\t\t\n\t\t\t\t<!--back1-->\n\t\t\t\t</div>\n\t\t\t\t<!--back1-->\n\t\t\t\n\t\t\t<!--left-->\n\t\t\t</div>\n\t\t\t<!--left-->\n\t\t\t\n\t\t\t<!--right-->\n\t\t\t<div class=\"right\" style=\"position: absolute; bottom: 0px; right: 0;\">\n\t\t\t<!--right-->\n\t\t\t\n\t\t\t\t<!--back3-->\n\t\t\t\t<div class=\"noback\" style=\"padding-left: 0px; margin-bottom: 0px;\">\n\t\t\t\t<!--back3-->\n\t\t\t\t\n\t\t\t\t<table class=\"table3\">\n\t\t\t\t<tr>\n\t\t\t\t\t<td style=\"width:105px;\">";
echo __("debtorcode");
echo ":</td>\n\t\t\t\t\t<td>\n\t\t\t\t\t\t";
echo $order->Type == "new" ? "<i>" . __("new debtor") . "</i>" : $debtor->DebtorCode;
echo "\t\t\t\t\t</td>\n\t\t\t\t</tr>\n\t\t\t\t<tr>\n\t\t\t\t\t<td>";
echo __("order no");
echo ":</td>\n\t\t\t\t\t<td>";
echo $order->OrderCode;
echo "</td>\n\t\t\t\t</tr>\n\t\t\t\t<tr>\n\t\t\t\t\t<td>";
echo __("date");
echo ":</td>\n\t\t\t\t\t<td>";
echo $order->ShowDate;
echo "</td>\n\t\t\t\t</tr>\n\t\t\t\t</table>\n\t\t\t\t\n\t\t\t\t<!--back3-->\n\t\t\t\t</div>\n\t\t\t\t<!--back3-->\n\t\t\t\t\n\t\t\t<!--right-->\n\t\t\t</div>\n\t\t\t<!--right-->\n\t\t\t\n\t\t<!--split3-->\n\t\t</div>\n\t\t<!--split3-->\n\t\t\n\t\t<table class=\"table1 alt1 noborder\" cellpadding=\"0\" cellspacing=\"0\">\n\t\t\t<tr class=\"trtitle\">\n\t\t\t\t<th scope=\"col\">";
echo __("date");
echo "</th>\n\t\t\t\t<th scope=\"col\">";
echo __("number");
echo "</th>\n\t\t\t\t<th scope=\"col\">";
echo __("productcode");
echo "</th>\n\t\t\t\t<th scope=\"col\">";
echo __("description");
echo "</th>\n\t\t\t\t";
if(!empty($array_taxpercentages)) {
    echo "<th scope=\"col\">";
    echo __("vat");
    echo "</th>";
}
echo "\t\t\t\t<th scope=\"col\" colspan=\"2\">";
echo __("price per unit");
echo "</th>\n\t\t\t\t<th scope=\"col\" colspan=\"2\">";
if(empty($array_taxpercentages)) {
    echo __("line total");
} elseif($order->VatCalcMethod == "incl") {
    echo __("total incl");
} else {
    echo __("total excl");
}
echo "</th>\n\t\t\t</tr>\n\t\t\t";
foreach ($order->Elements as $k => $element) {
    if(is_numeric($k)) {
        echo "\t\t\t\t<tr class=\"tr2 valign_top ";
        if(isEmptyFloat($element["DiscountPercentage"])) {
            echo "tr_invoice";
        }
        echo "\">\n\t\t\t\t\t<td style=\"width:70px;\">";
        if(!isEmptyFloat($element["PriceExcl"]) || $element["ProductCode"]) {
            echo $element["Date"];
        } else {
            echo "&nbsp;";
        }
        echo "</td>\n\t\t\t\t\t<td style=\"width:40px;\">";
        if(!isEmptyFloat($element["PriceExcl"]) || $element["ProductCode"]) {
            echo showNumber($element["Number"]) . $element["NumberSuffix"];
        } else {
            echo "&nbsp;";
        }
        echo "</td>\n\t\t\t\t\t";
        if(defined("SHOW_PRODUCTNAME") && SHOW_PRODUCTNAME === true) {
            echo "\t\t\t\t\t\t<td>";
            echo $element["ProductCode"] . " - " . $element["ProductName"];
            echo "</td>\n\t\t\t\t\t";
        } else {
            echo "\t\t\t\t\t\t<td style=\"width:50px;\">";
            echo $element["ProductCode"];
            echo "</td>\n\t\t\t\t\t";
        }
        echo "\t\t\t\t\t<td>\n\t\t\t\t\t\t";
        if(0 < $element["Reference"] && $element["ProductType"] && isset($_module_instances[$element["ProductType"]])) {
            echo "<a href=\"modules.php?module=" . $element["ProductType"] . "&page=show&amp;id=" . $element["Reference"] . "\" class=\"a1 c1\">" . nl2br($element["Description"]) . "</a>";
        } elseif(0 < $element["Reference"] && in_array($element["ProductType"], ["domain", "hosting"])) {
            echo "<a href=\"" . ($element["ProductType"] == "domain" ? "domains" : "hosting") . ".php?page=show&amp;id=" . $element["Reference"] . "\" class=\"a1 c1\">" . nl2br($element["Description"]) . "</a>";
        } else {
            echo nl2br($element["Description"]);
        }
        echo "\t\t\t\t\t\t";
        if($element["Periodic"]) {
            echo "<br />" . __("period") . ": " . $element["Periods"] . " " . (1 < $element["Periods"] ? $array_periodesMV[$element["Periodic"]] : $array_periodes[$element["Periodic"]]);
        }
        echo "\t\t\t\t\t</td>\n\t\t\t\t\t";
        if(!empty($array_taxpercentages)) {
            echo "\t\t\t\t\t<td style=\"width:20px;\" class=\"align_right\">\n\t\t\t\t\t\t";
            if(!isEmptyFloat($element["PriceExcl"]) || $element["ProductCode"]) {
                echo showNumber($element["TaxPercentage"] * 100) . "%";
            } else {
                echo "&nbsp;";
            }
            echo "\t\t\t\t\t</td>\n\t\t\t\t\t";
        }
        echo "\t\t\t\t\t<td style=\"width:5px;\" class=\"currency_sign_left\">";
        if(!isEmptyFloat($element["PriceExcl"]) || $element["ProductCode"]) {
            echo currency_sign_td(CURRENCY_SIGN_LEFT);
        } else {
            echo "&nbsp;";
        }
        echo "</td>\n\t\t\t\t\t<td style=\"width:65px;\" class=\"align_right currency_sign_right\">\n\t\t\t\t\t\t";
        if(!isEmptyFloat($element["PriceExcl"]) || $element["ProductCode"]) {
            if($order->VatCalcMethod == "incl") {
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
        echo "\t\t\t\t\t</td>\n\t\t\t\t\t<td style=\"width:5px;\" class=\"currency_sign_left\">";
        if(!isEmptyFloat($element["PriceExcl"]) || $element["ProductCode"]) {
            echo currency_sign_td(CURRENCY_SIGN_LEFT);
        } else {
            echo "&nbsp;";
        }
        echo "</td>\n\t\t\t\t\t<td style=\"width:75px;\" class=\"align_right currency_sign_right\">\n\t\t\t\t\t\t";
        if(!isEmptyFloat($element["PriceExcl"]) || $element["ProductCode"]) {
            if($order->VatCalcMethod == "incl") {
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
        echo "\t\t\t\t\t</td>\n\t\t\t\t</tr>\n\t\t\t\t\n\t\t\t\t";
        if(isset($element["DiscountPercentage"]) && !isEmptyFloat($element["DiscountPercentage"])) {
            $discount = -1 * ($order->VatCalcMethod == "incl" ? $element["PriceExcl"] * $element["Periods"] * $element["Number"] * $element["DiscountPercentage"] * round(1 + $element["TaxPercentage"], 3) : $element["PriceExcl"] * $element["Periods"] * $element["Number"] * $element["DiscountPercentage"]);
            if(0 < $discount) {
                $discount = number_format($discount + 0, 2, ".", "");
            } elseif($discount < 0) {
                $discount = number_format($discount - 0, 2, ".", "");
            }
            echo "\t\t\t\t\t<tr class=\"tr2 valign_top tr_invoice\">\n\t\t\t\t\t\t<td colspan=\"3\">&nbsp;</td>\n\t\t\t\t\t\t<td><i>";
            echo sprintf($element["DiscountPercentageType"] == "subscription" ? __("x discount on orderline and subscription") : __("x discount on orderline"), showNumber(round($element["DiscountPercentage"] * 100, 2)));
            echo "</i></td>\n\t\t\t\t\t\t<td>&nbsp;</td>\n\t\t\t\t\t\t<td>&nbsp;</td>\n\t\t\t\t\t\t";
            if(!empty($array_taxpercentages)) {
                echo "<td>&nbsp;</td>";
            }
            echo "\t\t\t\t\t\t<td class=\"currency_sign_left\">";
            echo currency_sign_td(CURRENCY_SIGN_LEFT);
            echo "</td>\n\t\t\t\t\t\t<td align=\"right\" class=\"currency_sign_right\">";
            echo money($discount, false);
            if(CURRENCY_SIGN_RIGHT) {
                echo " " . CURRENCY_SIGN_RIGHT;
            }
            echo "</td>\n\t\t\t\t\t</tr>\n\t\t\t\t";
        }
        echo "\t\t\t\n\t\t\t";
    }
}
echo "\t\t\t\n\t\t\t";
if(!isEmptyFloat($order->Discount)) {
    echo "\t\t\t<tr class=\"tr2 valign_top\">\n\t\t\t\t<td colspan=\"";
    echo !empty($array_taxpercentages) ? 9 : 8;
    echo "\">&nbsp;</td>\n\t\t\t</tr>\n\t\t\t<tr class=\"tr2 valign_top\">\n\t\t\t\t<td colspan=\"3\">&nbsp;</td>\n\t\t\t\t<td><strong>";
    echo showNumber($order->Discount);
    echo "% ";
    echo __("discount on order");
    echo "</strong></td>\n\t\t\t\t<td>&nbsp;</td>\n\t\t\t\t<td>&nbsp;</td>\n\t\t\t\t";
    if(!empty($array_taxpercentages)) {
        echo "<td>&nbsp;</td>";
    }
    echo "\t\t\t\t<td class=\"currency_sign_left\">";
    echo currency_sign_td(CURRENCY_SIGN_LEFT);
    echo "</td>\n\t\t\t\t<td align=\"right\" class=\"currency_sign_right\">";
    if($order->VatCalcMethod == "incl") {
        echo $order->AmountDiscountIncl;
    } else {
        echo $order->AmountDiscount;
    }
    if(CURRENCY_SIGN_RIGHT) {
        echo " " . CURRENCY_SIGN_RIGHT;
    }
    echo "</td>\n\t\t\t</tr>\n\t\t\t";
}
echo "\t\t\t</table>\n\t\t\t\n\t\t\t\n\t\t<!--box6-->\n\t\t<div class=\"box6\">\n\t\t<!--box6-->\n\t\t\n\t\t\t<table class=\"table7\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\n\t\t\t";
if(!empty($array_taxpercentages) || !empty($array_total_taxpercentages)) {
    echo "\t\t\t\t<tr>\n\t\t\t\t\t<td style=\"border-top: 1px solid black;\">";
    echo __("invoice total excl vat");
    echo "</td>\n\t\t\t\t\t<td style=\"border-top: 1px solid black;width:15px;\" class=\"currency_sign_left\">";
    echo currency_sign_td(CURRENCY_SIGN_LEFT);
    echo "</td>\n\t\t\t\t\t<td style=\"border-top: 1px solid black;width:75px;\" class=\"align_right currency_sign_right\">";
    echo $order->AmountExcl;
    if(CURRENCY_SIGN_RIGHT) {
        echo " " . CURRENCY_SIGN_RIGHT;
    }
    echo "</td>\n\t\t\t\t</tr>\n\t\t\t\t";
}
global $array_taxpercentages;
global $array_taxpercentages_info;
asort($array_taxpercentages);
foreach ($array_taxpercentages as $key => $value) {
    if(isset($order->used_taxrates[(string) (double) $key]["AmountTax"]) && 0 < (double) $key) {
        echo "\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td>";
        echo isset($array_taxpercentages_info[(string) (double) $key]["label"]) ? $array_taxpercentages_info[(string) (double) $key]["label"] : "";
        echo "</td>\n\t\t\t\t\t\t<td class=\"currency_sign_left\">";
        echo currency_sign_td(CURRENCY_SIGN_LEFT);
        echo "</td>\n\t\t\t\t\t\t<td class=\"align_right currency_sign_right\">";
        echo $order->used_taxrates[(string) (double) $key]["AmountTax"];
        if(CURRENCY_SIGN_RIGHT) {
            echo " " . CURRENCY_SIGN_RIGHT;
        }
        echo "</td>\n\t\t\t\t\t</tr>\n\t\t\t\t\t";
    }
}
if(isset($order->TaxRate_Label) && $order->TaxRate_Label) {
    echo "\t\t\t\t<tr>\n\t\t\t\t\t<td>";
    echo $order->TaxRate_Label;
    echo "</td>\n\t\t\t\t\t<td class=\"currency_sign_left\">";
    echo currency_sign_td(CURRENCY_SIGN_LEFT);
    echo "</td>\n\t\t\t\t\t<td class=\"align_right currency_sign_right\">";
    echo $order->TaxRate_Amount;
    if(CURRENCY_SIGN_RIGHT) {
        echo " " . CURRENCY_SIGN_RIGHT;
    }
    echo "</td>\n\t\t\t\t</tr>\n\t\t\t\t";
}
echo "\t\t\t\n\t\t\t<tr class=\"line\">\n\t\t\t\t<td style=\"border-top: 1px solid black;\">";
if(empty($array_taxpercentages) && empty($array_total_taxpercentages)) {
    echo __("invoice total");
} else {
    echo __("invoice total incl vat");
}
echo "</td>\n\t\t\t\t<td style=\"border-top: 1px solid black;\" class=\"currency_sign_left\">";
echo currency_sign_td(CURRENCY_SIGN_LEFT);
echo "</td>\n\t\t\t\t<td style=\"border-top: 1px solid black;\" class=\"align_right currency_sign_right\">";
echo $order->AmountIncl;
if(CURRENCY_SIGN_RIGHT) {
    echo " " . CURRENCY_SIGN_RIGHT;
}
echo "</td>\n\t\t\t</tr>\n\t\t\t</table>\n\t\t\t\n\t\t<!--box6-->\n\t\t</div>\n\t\t<!--box6-->\n\t\t\n\t\t";
if(isset($show_vatshift_text) && $show_vatshift_text !== false) {
    echo "\t\t\t<div class=\"vatshift_text_view\" style=\"margin-top: 70px\">";
    echo $show_vatshift_text;
    echo "</div>\t\t\t\t\t\t\n\t\t";
}
echo "\t\t\n\t<!--box5-->\n\t</div>\n\t<!--box5-->\n\t\n\t<br clear=\"both\"/>\n\t\t\n\t</div>\n\t\n<!--split4-->\n</div>\n<!--split4-->\n\n<!--box1-->\n<div class=\"box2\" id=\"tabs\">\n<!--box1-->\n\n\t<!--top-->\n\t<div class=\"top\">\n\t<!--top-->\n\t\t\n\t\t<ul class=\"list3\">\n\t\t\t<li class=\"on\"><a href=\"#tab-extra\">";
echo __("invoice options");
echo "</a></li>\n\t\t\t";
if($order->Type == "new") {
    echo "\t\t\t<li><a href=\"#tab-newdebtor\">";
    echo __("new debtor");
    echo "</a></li>\n\t\t\t";
}
echo "\t\t\t";
if($order->Comment) {
    echo "\t\t\t<li><a href=\"#tab-comment\">";
    echo __("remark");
    echo " <span class=\"ico actionblock info nm\">";
    echo __("more information");
    echo "</span></a></li>\n\t\t\t";
}
echo "\t\t</ul>\n\t\t\n\t<!--top-->\n\t</div>\n\t<!--top-->\n\t\n\t<!--content-->\n\t<div class=\"content\" id=\"tab-extra\">\n\t<!--content-->\n\t\t\n\t\t<!--split2-->\n\t\t<div class=\"split2\">\n\t\t<!--split2-->\n\t\t\t\n\t\t\t<!--left-->\n\t\t\t<div class=\"left\">\n\t\t\t<!--left-->\n\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
echo __("invoice options send");
echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title2\">";
echo __("invoice template");
echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
echo $template->Name;
echo "</span>\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title2\">";
echo __("invoice options invoicemethod");
echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
echo $array_invoicemethod[$order->InvoiceMethod];
echo "</span>\n\t\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t<!--left-->\n\t\t\t</div>\n\t\t\t<!--left-->\n\t\t\t\n\t\t\t<!--right-->\n\t\t\t<div class=\"right\">\n\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
echo __("invoice options payment");
echo "</h3><div class=\"content lineheight2 label_medium\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title2\">";
echo __("invoice options term");
echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
echo $order->Term;
echo " ";
echo __("days");
echo "</span>\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title2\">";
echo __("invoice options authorisation");
echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
echo $array_authorisation[$order->Authorisation];
echo "</span>\n\t\t\t\t\t\n\t\t\t\t\t";
if($order->Coupon) {
    echo "\t\t\t\t\t<strong class=\"title2\">";
    echo __("coupon");
    echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
    echo $order->Coupon;
    echo "</span>\n\t\t\t\t\t";
}
echo "\t\t\t\t\t\n\t\t\t\t\t";
if($order->IgnoreDiscount == 1) {
    echo "\t\t\t\t\t<strong class=\"title2\">";
    echo __("ignore discount");
    echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
    echo __("yes");
    echo "</span>\n\t\t\t\t\t";
}
echo "\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\n\t\t\t<!--right-->\n\t\t\t</div>\n\t\t\t<!--right-->\n\t\t\t\n\t\t<!--split2-->\n\t\t</div>\n\t\t<!--split2-->\n\t\t\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\t\n\t";
if($order->Type == "new") {
    echo "\t<!--content-->\n\t<div class=\"content\" id=\"tab-newdebtor\">\n\t<!--content-->\n\t\n\t\t<!--split2-->\n\t\t<div class=\"split2\">\n\t\t<!--split2-->\n\t\t\n\t\t\t<!--left-->\n\t\t\t<div class=\"left\">\n\t\t\t<!--left-->\n\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
    echo __("debtor information");
    echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t\t";
    if($customer->CompanyName) {
        echo "\t\t\t\t\t<strong class=\"title2\">";
        echo __("companyname");
        echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
        echo $customer->CompanyName;
        echo "</span>\n\t\n\t\t\t\t\t<strong class=\"title2\">";
        echo __("company number");
        echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
        if($customer->CompanyNumber && $customer->Country == "NL") {
            echo "<a class=\"a1 c1 fontnormal pointer\" href=\"";
            echo COC_LOCATION . $customer->CompanyNumber;
            echo "\" target=\"_blank\">";
            echo $customer->CompanyNumber;
            echo "</a>";
        } elseif($customer->CompanyNumber) {
            echo $customer->CompanyNumber;
        } else {
            echo __("unknown");
        }
        echo "</span>\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title2\">";
        echo __("vat number");
        echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
        if($customer->TaxNumber) {
            echo $customer->TaxNumber;
        } else {
            echo __("unknown");
        }
        echo "</span>\n\t\t\t\t\t\n                    ";
        if(!empty($array_legaltype)) {
            echo "\t\t\t\t\t<strong class=\"title2\">";
            echo __("legal form");
            echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
            if($customer->LegalForm) {
                echo $array_legaltype[$customer->LegalForm];
            } else {
                echo __("unknown");
            }
            echo "</span>\n\t\t\t\t\t";
        }
        echo "                    \n\t\t\t\t\t<br />\n\t\t\t\t\t";
    }
    echo "\t\t\t\t\n\t\t\t\t\t<strong class=\"title2\">";
    echo __("contact person");
    echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
    echo settings::getGenderTranslation($customer->Sex) . " " . $customer->Initials . "&nbsp;" . $customer->SurName;
    echo "</span>\n\t\n\t\t\t\t\t<strong class=\"title2\">";
    echo __("address");
    echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
    if($customer->Address) {
        echo $customer->Address;
    } else {
        echo "&nbsp;";
    }
    if(IS_INTERNATIONAL && $customer->Address2) {
        echo "<br />" . $customer->Address2;
    }
    echo "</span>\n\t\n\t\t\t\t\t<strong class=\"title2\">";
    echo __("zipcode and city");
    echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
    echo $customer->ZipCode . "&nbsp;&nbsp;" . $customer->City;
    echo "</span>\n\t\t\t\t\t\n\t\t\t\t\t";
    if(IS_INTERNATIONAL) {
        echo "\t\t\t\t\t\t<strong class=\"title2\">";
        echo __("state");
        echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
        echo $customer->StateName;
        echo "</span>\n\t\t\t\t\t";
    }
    echo "\t\n\t\t\t\t\t<strong class=\"title2\">";
    echo __("country");
    echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
    if($customer->Country) {
        echo $array_country[$customer->Country];
    } else {
        echo "&nbsp;";
    }
    echo "</span>\n\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
    echo __("contact data");
    echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t<!--box3-->\n\t\t\n\t\t\t\t\t<strong class=\"title2\">";
    echo __("emailaddress");
    echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\" style=\"display: block;\">\n\t\t\t\t\t\t\t";
    if(0 < strlen($customer->EmailAddress)) {
        echo "<div style=\"display: inline-block;\">";
        $ArrayEmailAddress = explode(";", check_email_address($customer->EmailAddress, "convert"));
        foreach ($ArrayEmailAddress as $email) {
            echo "<a class=\"a1 c1 fontnormal\" href=\"mailto:";
            echo urlencode($email);
            echo "\">";
            echo $email;
            echo "</a><br />";
        }
        echo "</div>";
    } else {
        echo "&nbsp;";
    }
    echo "</span>\n\t\t\n\t\t\t\t\t";
    if($customer->PhoneNumber) {
        echo "\t\t\t\t\t<strong class=\"title2\">";
        echo __("phonenumber");
        echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
        echo phoneNumberLink($customer->PhoneNumber);
        echo "</span>\n\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\n\t\t\t\t\t";
    if($customer->MobileNumber) {
        echo "\t\t\t\t\t<strong class=\"title2\">";
        echo __("mobilenumber");
        echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
        echo phoneNumberLink($debtor->MobileNumber);
        echo "</span>\n\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\n\t\t\t\t\t";
    if($customer->FaxNumber) {
        echo "\t\t\t\t\t<strong class=\"title2\">";
        echo __("faxnumber");
        echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
        echo $customer->FaxNumber;
        echo "</span>\n\t\t\t\t\t";
    }
    echo "\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\n\t\t\t<!--left-->\n\t\t\t</div>\n\t\t\t<!--left-->\n\t\t\t\n\t\t\t<!--right-->\n\t\t\t<div class=\"right\">\n\t\t\t<!--right-->\n\t\t\t\n\t\t\t\t";
    if($customer->InvoiceCompanyName || $customer->InvoiceInitials || $customer->InvoiceSurName || $customer->InvoiceAddress || $customer->InvoiceAddress2 || $customer->InvoiceZipCode || $customer->InvoiceCity || $customer->InvoiceCountry && $customer->InvoiceCountry != $customer->Country || $customer->InvoiceEmailAddress) {
        echo "\t\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
        echo __("invoice information");
        echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t";
        if($customer->InvoiceCompanyName) {
            echo "\t\t\t\t\t<strong class=\"title2\">";
            echo __("companyname");
            echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
            echo $customer->InvoiceCompanyName;
            echo "</span>\n\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\n\t\t\t\t\t";
        if($customer->InvoiceInitials || $customer->InvoiceSurName) {
            echo "\t\t\t\t\t<strong class=\"title2\">";
            echo __("contact person");
            echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
            echo settings::getGenderTranslation($customer->InvoiceSex) . " " . $customer->InvoiceInitials . " " . $customer->InvoiceSurName;
            echo "</span>\n\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\n\t\t\t\t\t";
        if($customer->InvoiceAddress || $customer->InvoiceAddress2) {
            echo "\t\t\t\t\t<strong class=\"title2\">";
            echo __("address");
            echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
            echo $customer->InvoiceAddress;
            if(IS_INTERNATIONAL && $customer->InvoiceAddress2) {
                echo "<br />" . $customer->InvoiceAddress2;
            }
            echo "</span>\n\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\n\t\t\t\t\t";
        if($customer->InvoiceZipCode || $customer->InvoiceCity) {
            echo "\t\t\t\t\t<strong class=\"title2\">";
            echo __("zipcode and city");
            echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
            echo $customer->InvoiceZipCode;
            echo "&nbsp;&nbsp;";
            echo $customer->InvoiceCity;
            echo "</span>\n\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\n\t\t\t\t\t";
        if(IS_INTERNATIONAL) {
            echo "\t\t\t\t\t\t<strong class=\"title2\">";
            echo __("state");
            echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
            echo $customer->InvoiceStateName;
            echo "</span>\n\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\n\t\t\t\t\t";
        if($customer->InvoiceCountry) {
            echo "\t\t\t\t\t<strong class=\"title2\">";
            echo __("country");
            echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
            echo $array_country[$customer->InvoiceCountry];
            echo "</span>\n\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\n\t\t\t\t\t";
        if($customer->InvoiceEmailAddress) {
            echo "\t\t\t\t\t<strong class=\"title2\">";
            echo __("emailaddress");
            echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\" style=\"display: block;\">\n\t\t\t\t\t\t\t<div style=\"display: inline-block;\">";
            $ArrayEmailAddress = explode(";", check_email_address($customer->InvoiceEmailAddress, "convert"));
            foreach ($ArrayEmailAddress as $email) {
                echo "<a class=\"a1 c1 fontnormal\" href=\"mailto:";
                echo urlencode($email);
                echo "\">";
                echo $email;
                echo "</a><br />";
            }
            echo "</div>\n\t\t\t\t\t\t</span>\n\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t<br />\n\t\t\t\t\n\t\t\t\t";
    }
    echo "\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
    echo __("invoicing and payment");
    echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t\t<strong class=\"title2\">";
    echo __("invoice method");
    echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
    echo $array_invoicemethod[$customer->InvoiceMethod];
    echo "</span>\n\n\t\t\t\t\t<strong class=\"title2\">";
    echo __("authorization");
    echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
    echo $array_authorisation[$customer->InvoiceAuthorisation];
    echo "</span>\n\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t";
    if($customer->AccountNumber || $customer->AccountName || $customer->AccountBank || $customer->AccountCity || $customer->AccountBIC) {
        echo "\t\t\t\t\n\t\t\t\t<br />\n\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
        echo __("bankaccount data");
        echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t\t";
        if($customer->AccountNumber) {
            echo "\t\t\t\t\t<strong class=\"title2\">";
            echo __("account number");
            echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
            echo $customer->AccountNumber;
            echo "</span>\n\t\t\t\t\t";
        }
        echo "\n\t\t\t\t\t";
        if($customer->AccountNumber || $customer->AccountName) {
            echo "\t\t\t\t\t<strong class=\"title2\">";
            echo __("account name");
            echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
            if($customer->AccountName) {
                echo $customer->AccountName;
            } else {
                echo "&nbsp;";
            }
            echo "</span>\n\t\t\t\t\t";
        }
        echo "\n\t\t\t\t\t";
        if($customer->AccountBank) {
            echo "\t\t\t\t\t<strong class=\"title2\">";
            echo __("bank");
            echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
            echo $customer->AccountBank;
            echo "</span>\n\t\t\t\t\t";
        }
        echo "\n\t\t\t\t\t";
        if($customer->AccountCity) {
            echo "\t\t\t\t\t<strong class=\"title2\">";
            echo __("bank city");
            echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
            echo $customer->AccountCity;
            echo "</span>\n\t\t\t\t\t";
        }
        echo "\n\t\t\t\t\t";
        if($customer->AccountBIC) {
            echo "\t\t\t\t\t<strong class=\"title2\">";
            echo __("bic");
            echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
            echo $customer->AccountBIC;
            echo "</span>\n\t\t\t\t\t";
        }
        echo "\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t";
    }
    echo "\t\t\t\t\n\t\t\t\t";
    if($customer->Username) {
        echo "\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
        echo __("customerpanel");
        echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title2\">";
        echo __("username");
        echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
        echo $customer->Username;
        echo "</span>\n\n\t\t\t\t\t<strong class=\"title2\">";
        echo __("password");
        echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">******</span>\n\n\t\t\t\t\t<strong class=\"title2\">";
        echo __("language preference");
        echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
        if(!$customer->DefaultLanguage || !isset($array_customer_languages[$customer->DefaultLanguage])) {
            echo __("standard") . " (" . $array_customer_languages[LANGUAGE] . ")";
        } else {
            echo $array_customer_languages[$customer->DefaultLanguage];
        }
        echo "</span>\n\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t\t";
    }
    echo "\t\n\t\t\t\t\n\t\t\t\t";
    if(0 < count($customer->customfields_list)) {
        echo "\t\t\t\t\t\n\t\t\t\t\t<br />\n\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
        echo __("custom debtor fields h2");
        echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t";
        foreach ($customer->customfields_list as $k => $custom_field) {
            echo show_custom_field($custom_field, isset($customer->custom->{$custom_field["FieldCode"]}) ? $customer->custom->{$custom_field["FieldCode"]} : "");
        }
        echo "\t\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t";
    }
    echo "\t\t\t\t\n\t\t\t<!--right-->\n\t\t\t</div>\n\t\t\t<!--right-->\n\t\t\t\n\t\t<!--split2-->\n\t\t</div>\n\t\t<!--split2-->\n\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\t";
}
echo "\t";
if($order->Comment) {
    echo "\t<!--content-->\n\t<div class=\"content\" id=\"tab-comment\">\n\t<!--content-->\n\n\t\t<!--box3-->\n\t\t<div class=\"box3\"><h3>";
    echo __("remark");
    echo "</h3><div class=\"content\" style=\"overflow-x: auto;\">\n\t\t<!--box3-->\n\t\t\n\t\t\t";
    echo nl2br($order->Comment);
    echo "\n\t\t<!--box3-->\n\t\t</div></div>\n\t\t<!--box3-->\n\t\t\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\t";
}
echo "\n<!--box1-->\n</div>\n<!--box1-->\n\n<div id=\"invoicedialog_invoicemethod\" class=\"hide\" title=\"";
echo __("invoicemethoddialog title");
echo "\">\n\t<form name=\"form_invoicemethod\" method=\"post\" action=\"orders.php?page=show&action=changesendmethod\">\n\t<input type=\"hidden\" name=\"id\" value=\"";
echo $order->Identifier;
echo "\"/>\n\t";
echo sprintf(__("invoicemethoddialog description order"), $order->OrderCode);
echo "<br />\n\t<br />\n\t<span class=\"title2 lineheight_input\">";
echo __("invoice method");
echo ":</span>\n\t<span class=\"title2_value\">\n\t\t<select name=\"InvoiceMethod\" class=\"text1 size1\">\n\t\t\t";
foreach ($array_invoicemethod as $key => $value) {
    echo "\t\t\t\t<option value=\"";
    echo $key;
    echo "\" ";
    if($key == $order->InvoiceMethod) {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo $value;
    echo "</option>\n\t\t\t";
}
echo "\t\t</select>\n\t</span>\n\n\t";
$invoice_emailaddress = "";
if($order->EmailAddress) {
    $invoice_emailaddress = $order->EmailAddress;
} elseif($debtor->InvoiceEmailAddress) {
    $invoice_emailaddress = $debtor->InvoiceEmailAddress;
} elseif($debtor->EmailAddress) {
    $invoice_emailaddress = $debtor->EmailAddress;
}
echo "\t<div class=\"";
if($order->InvoiceMethod == "1") {
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
if(U_ORDER_DELETE && $order->Status <= 2) {
    echo "<div id=\"delete_order\" class=\"hide ";
    if(isset($pagetype) && $pagetype == "confirmDelete") {
        echo "autoopen";
    }
    echo "\" title=\"";
    echo __("delete order title");
    echo "\">\n\t<form id=\"OrderForm\" name=\"form_delete\" method=\"post\" action=\"orders.php?page=delete&id=";
    echo $order->Identifier;
    echo "\">\n\t<input type=\"hidden\" name=\"id\" value=\"";
    echo $order->Identifier;
    echo "\"/>\n\t\n\t<p>";
    echo sprintf(__("delete order dialog description"), $order->OrderCode);
    echo "</p>\n\t\n\t<br />\n\t\n\t<input type=\"checkbox\" id=\"imsure\" name=\"imsure\" value=\"yes\"/> <label for=\"imsure\">";
    echo __("delete this order");
    echo "</label><br />\n\t<br />\n\t\n\t<p><a id=\"delete_order_btn\" class=\"button2 alt1 float_left\"><span>";
    echo __("delete");
    echo "</span></a></p>\n\t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#delete_order').dialog('close');\"><span>";
    echo __("cancel");
    echo "</span></a></p>\n\t\n\t";
    if(isset($_SESSION["ActionLog"]) && isset($_SESSION["ActionLog"]["Order"]["delete"]) && is_array($_SESSION["ActionLog"]["Order"]["delete"]) && 1 < count($_SESSION["ActionLog"]["Order"]["delete"])) {
        echo "\t\t<br class=\"clear\"/><br />\n\t\t<strong>";
        echo sprintf(__("batch remove all orders"), count($_SESSION["ActionLog"]["Order"]["delete"]) - 1);
        echo "</strong><br />\n\t\t<label style=\"display:block;margin: 2px 0 5px;\"><input type=\"checkbox\" id=\"forAll\" name=\"forAll\" value=\"yes\" /> ";
        echo __("batch after this directly remove the others");
        echo "</label>\n\t";
    }
    echo "\t\n\t\n\t</form>\n</div>\n";
}
echo "\n";
require_once "views/footer.php";

?>