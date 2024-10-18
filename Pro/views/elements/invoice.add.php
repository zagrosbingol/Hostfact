<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
function show_invoice_add($object, $debtor, $options = [])
{
    $object_id = isset($options["object_id"]) ? $options["object_id"] : 0;
    $form_type = isset($options["form_type"]) ? $options["form_type"] : "invoice";
    $periodic_dates = isset($options["periodic_dates"]) ? $options["periodic_dates"] : true;
    $periodic_dates_width = "width: " . ($form_type == "order" ? "85" : "170") . "px;";
    global $products;
    global $array_periodic;
    global $array_taxpercentages;
    global $array_taxpercentages_info;
    global $array_total_taxpercentages;
    global $array_total_taxpercentages_info;
    echo "\t<style type=\"text/css\">\n\t\thtml { overflow: visible !important; }\n\t</style>\n\n\t<table class=\"table1 alt1 noborder invoicetable\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"border-collapse: collapse;margin-top:0px;\">\n\t\t<tr class=\"trtitle\">\n\t\t\t<td scope=\"col\" style=\"width: 30px;\" class=\"invoiceTDempty\"></td>\n\t\t\t<th scope=\"col\" style=\"width: 40px;\">";
    echo __("date");
    echo "</th>\n\t\t\t<th scope=\"col\" style=\"width: 50px;\">";
    echo __("number");
    echo "</th>\n\t\t\t<th scope=\"col\" style=\"width: 165px;\">";
    echo __("product no");
    echo "</th>\n\t\t\t<th scope=\"col\">";
    echo __("description");
    echo "</th>\n\t\t\t<th scope=\"col\" style=\"";
    echo $periodic_dates_width;
    echo "\">";
    echo __("period");
    echo "</th>\n\t\t\t";
    if(!empty($array_taxpercentages)) {
        echo "<th scope=\"col\" style=\"width: 58px;\">";
        echo __("vat");
        echo "</th>";
    }
    echo "\t\t\t<th scope=\"col\" style=\"width: 85px;\">";
    if(empty($array_taxpercentages)) {
        echo __("price per unit");
    } else {
        echo "<span class=\"span_vat_incl" . ($debtor->Taxable && $object->VatCalcMethod == "incl" ? "" : " hide") . "\">" . __("price incl") . "</span><span class=\"span_vat_excl" . ($debtor->Taxable && $object->VatCalcMethod == "incl" ? " hide" : "") . "\">" . __("price excl") . "</span>";
    }
    echo "</th>\n\t\t\t<th scope=\"col\" style=\"width: 80px;\" class=\"show_col_ws\" colspan=\"2\">";
    if(empty($array_taxpercentages)) {
        echo __("line total");
    } else {
        echo "<span class=\"span_vat_incl" . ($debtor->Taxable && $object->VatCalcMethod == "incl" ? "" : " hide") . "\">" . __("total incl") . "</span><span class=\"span_vat_excl" . ($debtor->Taxable && $object->VatCalcMethod == "incl" ? " hide" : "") . "\">" . __("total excl") . "</span>";
    }
    echo "</th>\n\t\t\t<td scope=\"col\" style=\"width: 50px;\" class=\"invoiceTDempty\"></td>\n\t\t</tr>\n\t\t<tbody id=\"InvoiceElements\">\t\n\t\t";
    $i = 0;
    $number_elements = 0;
    $invoiceLineTaxNull = true;
    if(!isset($_POST["NumberOfElements"])) {
        if(isset($object->Elements) && is_array($object->Elements)) {
            foreach ($object->Elements as $itemID => $item) {
                if(is_numeric($itemID)) {
                    $number_elements++;
                    echo "<tr class=\"tr2 valign_top tr_invoiceelement ";
                    if($item["Number"] == "0") {
                        echo "hide";
                    }
                    echo "\">\n\t\t\t\t<td style=\"width:30px;\" class=\"invoiceTDempty\"><img src=\"images/ico_sort.png\" class=\"pointer sortablehandle sort_icon invoicetrhide\" /></td>\n\t\t\t\t<td style=\"width:40px;\" valign=\"top\">\n\t\t\t\t\t<span class=\"input_date\">\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size6 datepicker_icon\" name=\"Date[";
                    echo $i;
                    echo "]\" value=\"";
                    echo htmlspecialchars(esc($item["Date"]));
                    echo "\" />\n\t\t\t\t\t</span>\n\t\t\t\t\t<input type=\"hidden\" name=\"Item[";
                    echo $i;
                    echo "]\" value=\"";
                    echo $itemID;
                    echo "\" />\n\t\t\t\t</td>\n\t\t\t\t<td style=\"width: 50px;\"><input type=\"text\" name=\"Number[";
                    echo $i;
                    echo "]\" class=\"text1 size3\" value=\"";
                    echo showNumber($item["Number"], false, false) . esc($item["NumberSuffix"]);
                    echo "\" /></td>\n\t\t\t\t<td style=\"width: 165px;\">\n\t\t\t\t\t";
                    $selected_product_code = $selected_name = "";
                    foreach ($products as $key => $value) {
                        if(is_numeric($key) && isset($item["ProductCode"]) && $item["ProductCode"] == $value["ProductCode"]) {
                            $selected_product_code = $value["ProductCode"];
                            $selected_name = $value["ProductCode"] . " " . $value["ProductName"];
                        }
                    }
                    echo "\t\t\t\t\t<input type=\"hidden\" name=\"ProductCode[";
                    echo $i;
                    echo "]\" value=\"";
                    echo $selected_product_code;
                    echo "\" onchange=\"getProductData('";
                    echo $i;
                    echo "',this.value);\"/>\n\t\t\t\t\t";
                    createAutoComplete("product", "ProductCode[" . $i . "]", $selected_name, ["return_type" => "code"]);
                    echo "\t\t\t\t</td>\n\t\t\t\t<td>\n\t\t\t\t\t<textarea name=\"Description[";
                    echo $i;
                    echo "]\" class=\"text1 autogrow\" style=\"width: 97%;\">";
                    echo $item["Description"];
                    echo "</textarea>\n\t\t\t\t\t<span class=\"discountpercentage_type";
                    echo $item["Periodic"] != "" ? " pointer" : "";
                    echo " discount ";
                    echo isset($item["DiscountPercentage"]) && !isEmptyFloat(number2db($item["DiscountPercentage"])) ? "" : "hide";
                    echo "\"><span class=\"discountpercentage_type_line";
                    echo $item["DiscountPercentageType"] == "line" ? "" : " hide";
                    echo "\">";
                    echo __("discount on " . $form_type . "line");
                    echo "</span><span class=\"discountpercentage_type_subscription";
                    echo $item["DiscountPercentageType"] == "line" ? " hide" : "";
                    echo "\">";
                    echo __("discount on " . $form_type . "line and subscription");
                    echo "</span> <div style=\"margin: 2px;\" class=\"ico actionblock arrowdown mar2";
                    echo $item["Periodic"] != "" ? "" : " hide";
                    echo "\">&nbsp;</div>\n\t\t\t\t\t\n\t\t\t\t\t<div class=\"box6 hide discounttype-dialog\">\n\t\t\t\t\t\t<div class=\"mark\">\n\t\t\t\t\t\t\t<strong>";
                    echo __("discountpercentage type");
                    echo "</strong>\n\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t<label><input name=\"DiscountPercentageType[";
                    echo $i;
                    echo "]\" type=\"radio\" value=\"line\" ";
                    if($item["DiscountPercentageType"] == "line") {
                        echo "checked=\"checked\"";
                    }
                    echo "/> ";
                    echo __("discount on " . $form_type . "line");
                    echo "</label><br />\n\t\t\t\t\t\t\t<label><input name=\"DiscountPercentageType[";
                    echo $i;
                    echo "]\" type=\"radio\" value=\"subscription\" ";
                    if($item["DiscountPercentageType"] == "subscription") {
                        echo "checked=\"checked\"";
                    }
                    echo "/> ";
                    echo __("discount on " . $form_type . "line and subscription");
                    echo "</label>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\t\t\t\t\t</span>\n\t\t\t\t</td>\n\t\t\t\t<td style=\"";
                    echo $periodic_dates_width;
                    echo "\">\n\t\t\t\t\t<div class=\"periodClick pointer\">\n\t\t\t\t\t\t<span id=\"formJQ-Period-";
                    echo $i;
                    echo "\">";
                    echo $item["Periodic"] != "" ? $periodic_dates ? $item["StartPeriod"] . " " . __("till") . " " . $item["EndPeriod"] : $item["Periods"] . " " . $array_periodic[$item["Periodic"]] : __("once");
                    echo "</span>\n\t\t\t\t\t\t<div class=\"ico actionblock arrowdown mar2\" style=\"margin: 2px;\">&nbsp;</div>\n\t\t\t\t\t</div>\n\t\t\t\t\t<div id=\"Periodic-";
                    echo $i;
                    echo "\"  class=\"box6 hide periodic-dialog\" style=\"position: absolute; padding:0px;width: 250px; z-index:10; margin-top: -15px; padding:0px;\">\n\t\t\t\t\t<div class=\"mark\">\n\t\t\t\t\t\t<strong>";
                    echo __("recurring invoice line");
                    echo "</strong>\n\t\t\t\t\t\t<br />\n\t\t\t\t\t\t<label><input id=\"PeriodicType-";
                    echo $i;
                    echo "-once\" name=\"PeriodicType[";
                    echo $i;
                    echo "]\" type=\"radio\" value=\"once\" ";
                    if($item["Periodic"] == "") {
                        echo "checked=\"checked\"";
                    }
                    echo "/> ";
                    echo __("no once");
                    echo "</label><br />\n\t\t\t\t\t\t<label><input id=\"PeriodicType-";
                    echo $i;
                    echo "-period\" name=\"PeriodicType[";
                    echo $i;
                    echo "]\" type=\"radio\" value=\"period\" ";
                    if($item["Periodic"] != "") {
                        echo "checked=\"checked\"";
                    }
                    echo "/> ";
                    echo __("yes setup a period");
                    echo "</label><br />\n\t\t\t\t\t\t<div id=\"Periodic-period-";
                    echo $i;
                    echo "\" ";
                    if($item["Periodic"] == "") {
                        echo "class=\"hide\"";
                    }
                    echo " style=\"margin-top: 14px;\">\n\t\t\t\t\t\t\t<strong>";
                    echo __("recurring period");
                    echo "</strong><br />\n\t\t\t\t\t\t\t<input name=\"Periods[";
                    echo $i;
                    echo "]\" class=\"text1 size3\" value=\"";
                    echo $item["Periods"];
                    echo "\" />\n\t\t\t\t\t\t\t<select name=\"Periodic[";
                    echo $i;
                    echo "]\" class=\"text1 size7\">\n\t\t\t\t\t\t\t\t";
                    foreach ($array_periodic as $key => $value) {
                        if($key != "") {
                            echo "\t\t\t\t\t\t\t\t<option value=\"";
                            echo $key;
                            echo "\" ";
                            if($item["Periodic"] == $key) {
                                echo "selected=\"selected\"";
                            }
                            echo ">";
                            echo $value;
                            echo "</option>\n\t\t\t\t\t\t\t\t";
                        }
                    }
                    echo "\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t";
                    if($periodic_dates) {
                        echo "\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t<strong style=\"margin-top: 16px;display: inline-block;\">";
                        echo __("startdate period");
                        echo "</strong><br />\n\t\t\t\t\t\t\t<input type=\"text\" name=\"StartPeriod[";
                        echo $i;
                        echo "]\" class=\"text1 size6 datepicker\" value=\"";
                        echo $item["StartPeriod"];
                        echo "\" />\n\t\t\t\t\t\t\t<span id=\"EndPeriod-";
                        echo $i;
                        echo "\" style=\"color: #6c6c6d;padding-left: 5px;\">";
                        echo __("till") . " " . $item["EndPeriod"];
                        echo "</span>\n\t\t\t\t\t\t\t<input type=\"hidden\" name=\"EndPeriod[";
                        echo $i;
                        echo "]\" value=\"";
                        echo $item["EndPeriod"];
                        echo "\" class=\"text1 size6\" />\n\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t";
                    }
                    echo "\t\t\t\t\t\t</div>\n\t\t\t\t\t</div></div></td>\n\t\t\t\t\t";
                    if(!empty($array_taxpercentages)) {
                        echo "\t\t\t\t\t<td style=\"width: 58px;\">\n\t\t\t\t\t\t<div class=\"taxAdjusterClick pointer\">\n\t\t\t\t\t\t\t<span id=\"formJQ-TaxPercentageText-";
                        echo $i;
                        echo "\">\n\t\t\t\t\t\t\t\t";
                        echo showNumber($item["TaxPercentage"] * 100);
                        echo "%\n\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t<div id=\"taxadjuster-";
                        echo $i;
                        echo "\" class=\"ico actionblock arrowdown mar2 taxadjuster ";
                        if(!$debtor->Taxable) {
                            echo "hide";
                        }
                        echo "\" style=\"margin: 2px;\">&nbsp;</div>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t";
                        if($debtor->Taxable) {
                            $tax = $item["TaxPercentage"];
                            if(!isEmptyFloat($tax)) {
                                $invoiceLineTaxNull = false;
                            }
                        } elseif($item["ProductCode"]) {
                            foreach ($products as $key => $value) {
                                if(is_numeric($key) && $value["ProductCode"] == $item["ProductCode"]) {
                                    $tax = $value["TaxPercentage"];
                                }
                            }
                        } else {
                            $tax = STANDARD_TAX;
                        }
                        echo "\t\t\t\t\t\n\t\t\t\t\t<input type=\"text\" id=\"formJQ-TaxPercentage-";
                        echo $i;
                        echo "\" name=\"TaxPercentage[";
                        echo $i;
                        echo "]\" value=\"";
                        echo $tax;
                        echo "\" class=\"hide\"/>\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t<div id=\"TaxList-";
                        echo $i;
                        echo "\" class=\"box6 hide taxlist-dialog\" style=\"position: absolute; margin-top: -15px; padding:0px;\">\n\t\t\t\t\t<div class=\"content mark taxmark\">\n\t\t\t\t\t\t<ul class=\"emaillist\" style=\"border-top: 0px;\">\n\t\t\t\t\t\t";
                        arsort($array_taxpercentages);
                        foreach ($array_taxpercentages as $key => $value) {
                            if(is_numeric($key)) {
                                echo "\t\t\t\t\t\t\t<li style=\"border-bottom: 0px;\"><label><input type=\"radio\" name=\"TaxRadio[";
                                echo $i;
                                echo "]\" value=\"";
                                echo $key;
                                echo "\" ";
                                if($tax == $key) {
                                    echo "checked=\"checked\"";
                                }
                                echo " />&nbsp;";
                                echo vat($value);
                                echo "%</label></li>\n\t\t\t\t\t\t";
                            }
                        }
                        echo "\t\t\t\t\t\t</ul>\n\t\t\t\t\t</div></div>\n\n\t\t\t\t</td>\n\t\t\t\t";
                    }
                    echo "\t\t\t\t<td style=\"width: 85px;\">\n\t\t\t\t\t<input type=\"text\" name=\"PriceIncl[";
                    echo $i;
                    echo "]\" class=\"text1 size6 ar";
                    if($object->VatCalcMethod != "incl") {
                        echo " hide";
                    }
                    echo "\" value=\"";
                    echo money($debtor->Taxable ? $object->VatCalcMethod == "incl" ? $item["PriceIncl"] : round((double) $item["PriceIncl"], 5) : round($item["PriceExcl"] * (1 + $item["TaxPercentage"]), 5), false, false);
                    echo "\" />\n\t\t\t\t\t<input type=\"text\" name=\"PriceExcl[";
                    echo $i;
                    echo "]\" class=\"text1 size6 ar";
                    if($object->VatCalcMethod == "incl") {
                        echo " hide";
                    }
                    echo "\" value=\"";
                    echo money($item["PriceExcl"], false, false);
                    echo "\" />\n\n\t\t\t\t\t<span class=\"discount ";
                    echo isset($item["DiscountPercentage"]) && !isEmptyFloat(number2db($item["DiscountPercentage"])) ? "" : "hide";
                    echo "\"><input type=\"text\" class=\"text1 ar\" style=\"width: 55px;\" name=\"DiscountPercentage[";
                    echo $i;
                    echo "]\" value=\"";
                    echo showNumber(number2db($item["DiscountPercentage"]) * 100);
                    echo "\" maxlength=\"5\"/> % </span>\n\t\t\t\t</td>\n\t\t\t\t<td style=\"width:5px;\" class=\"show_col_ws currency_sign_left\">";
                    echo currency_sign_td(CURRENCY_SIGN_LEFT);
                    echo "<div class=\"discount_helper ";
                    echo isset($item["DiscountPercentage"]) && !isEmptyFloat(number2db($item["DiscountPercentage"])) ? "" : "hide";
                    echo "\">";
                    echo CURRENCY_SIGN_LEFT;
                    echo "</div></td>\n\t\t\t\t<td style=\"width:75px;\" class=\"align_right show_col_ws currency_sign_right\"><span id=\"formJQ-LineTotal-";
                    echo $i;
                    echo "\">";
                    if($object->VatCalcMethod == "incl") {
                        echo money(esc($item["PriceIncl"] * $item["Periods"] * $item["Number"]), false);
                    } else {
                        echo money(esc($item["PriceExcl"] * $item["Periods"] * $item["Number"]), false);
                    }
                    echo "</span>";
                    if(CURRENCY_SIGN_RIGHT) {
                        echo " " . CURRENCY_SIGN_RIGHT;
                    }
                    echo "    \t\t\t\t<div class=\"discount_helper ";
                    echo isset($item["DiscountPercentage"]) && !isEmptyFloat(number2db($item["DiscountPercentage"])) ? "" : "hide";
                    echo "\">\n                        <span>\n                            ";
                    $discount_price = ($object->VatCalcMethod == "incl" ? $item["PriceIncl"] : $item["PriceExcl"]) * $item["Periods"] * $item["Number"] * $item["DiscountPercentage"];
                    $discount_price = 0 < $discount_price ? "- " . money(esc(abs($discount_price)), false) : money(esc(abs($discount_price)), false);
                    echo $discount_price;
                    echo "                        </span>\n                        ";
                    if(CURRENCY_SIGN_RIGHT) {
                        echo " " . CURRENCY_SIGN_RIGHT;
                    }
                    echo "                    </div>\n                </td>\n\t\t\t\t<td style=\"width:50px;\" valign=\"top\" class=\"invoiceTDempty iconbox\">\n\t\t\t\t\t<img src=\"images/ico_discount.png\" onclick=\"add_discount_rule('";
                    echo $i;
                    echo "', this);\" class=\"pointer sortablehandle discount_icon invoicetrhide ";
                    echo isset($item["DiscountPercentage"]) && !isEmptyFloat(number2db($item["DiscountPercentage"])) ? "hide" : "";
                    echo "\" />\n\t\t\t\t\t<img src=\"images/ico_trash.png\" onclick=\"removeElement('";
                    echo $i;
                    echo "');\" class=\"invoicetrhide remove_icon pointer\"/>\n\t\t\t\t\t<img src=\"images/ico_trash.png\" onclick=\"remove_discount_rule('";
                    echo $i;
                    echo "', this);\" class=\"invoicetrhide discount_remove_icon pointer ";
                    echo isset($item["DiscountPercentage"]) && !isEmptyFloat(number2db($item["DiscountPercentage"])) ? "" : "hide";
                    echo "\" style=\"margin-top: 20px;clear:both;\"/>\n\t\t\t\t</td>\n\t\t\t</tr>";
                    $i++;
                }
            }
        }
    } elseif(isset($_POST["NumberOfElements"])) {
        $items = array_reverse($_POST["Date"], true);
        foreach ($items as $x => $xValue) {
            if($_POST["Number"][$x] == "1" && $_POST["Description"][$x] == "" && isEmptyFloat(deformat_money($_POST["PriceExcl"][$x]))) {
                $_POST["Number"][$x] = "0";
            } elseif($_POST["Number"][$x] != "0") {
                $linesDisplayed = 0;
                foreach ($_POST["Date"] as $i => $value) {
                    $number_elements++;
                    if(esc($_POST["Number"][$i]) != "0") {
                        $linesDisplayed++;
                    }
                    echo "<tr class=\"tr2 valign_top tr_invoiceelement ";
                    if(esc($_POST["Number"][$i]) == "0") {
                        echo "hide";
                    }
                    echo "\">\n\t\t\t\t<td style=\"width:30px;\" class=\"invoiceTDempty\"><img src=\"images/ico_sort.png\" class=\"pointer sortablehandle sort_icon invoicetrhide\" /></td>\n\t\t\t\t<td style=\"width:40px;\" valign=\"top\">\n\t\t\t\t\t<span class=\"input_date\">\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size6 datepicker_icon\" name=\"Date[";
                    echo $i;
                    echo "]\" value=\"";
                    echo htmlspecialchars(esc($_POST["Date"][$i]));
                    echo "\" />\n\t\t\t\t\t</span>\n\t\t\t\t\t<input type=\"hidden\" name=\"Item[";
                    echo $i;
                    echo "]\" value=\"";
                    echo htmlspecialchars($_POST["Item"][$i] ?? "");
                    echo "\" />\n\t\t\t\t</td>\n\t\t\t\t<td valign=\"top\" style=\"width: 50px;\"><input type=\"text\" class=\"text1 size3 \" name=\"Number[";
                    echo $i;
                    echo "]\" value=\"";
                    echo htmlspecialchars(esc(number2site($_POST["Number"][$i]) . $_POST["NumberSuffix"][$i]));
                    echo "\" /></td>\n\t\t\t\t<td valign=\"top\" style=\"width: 165px;\">\n\t\t\t\t\t";
                    $selected_product_code = $selected_name = "";
                    foreach ($products as $key => $value) {
                        if(is_numeric($key) && isset($_POST["ProductCode"][$i]) && $_POST["ProductCode"][$i] == $value["ProductCode"]) {
                            $selected_product_code = $value["ProductCode"];
                            $selected_name = $value["ProductCode"] . " " . $value["ProductName"];
                        }
                    }
                    echo "\t\t\t\t\t<input type=\"hidden\" name=\"ProductCode[";
                    echo $i;
                    echo "]\" value=\"";
                    echo $selected_product_code;
                    echo "\" onchange=\"getProductData('";
                    echo $i;
                    echo "',this.value);\"/>\n\t\t\t\t\t";
                    createAutoComplete("product", "ProductCode[" . $i . "]", $selected_name, ["return_type" => "code"]);
                    echo "\t\t\t\t</td>\n\t\t\t\t<td valign=\"top\">\n\n\t\t\t\t\t<textarea name=\"Description[";
                    echo $i;
                    echo "]\" class=\"text1 autogrow\" style=\"width: 97%;\">";
                    echo htmlspecialchars(esc($_POST["Description"][$i]));
                    echo "</textarea>\n\t\t\t\t\t<span class=\"discountpercentage_type";
                    echo $_POST["PeriodicType"][$i] != "once" ? " pointer" : "";
                    echo " discount ";
                    echo isset($_POST["DiscountPercentage"][$i]) && !isEmptyFloat(number2db($_POST["DiscountPercentage"][$i])) ? "" : "hide";
                    echo "\"><span class=\"discountpercentage_type_line";
                    echo $_POST["DiscountPercentageType"][$i] == "line" ? "" : " hide";
                    echo "\">";
                    echo __("discount on " . $form_type . "line");
                    echo "</span><span class=\"discountpercentage_type_subscription";
                    echo $_POST["DiscountPercentageType"][$i] == "line" ? " hide" : "";
                    echo "\">";
                    echo __("discount on " . $form_type . "line and subscription");
                    echo "</span> <div style=\"margin: 2px;\" class=\"ico actionblock arrowdown mar2";
                    echo $_POST["PeriodicType"][$i] != "once" ? "" : " hide";
                    echo "\">&nbsp;</div>\n\t\t\t\t\t\n\t\t\t\t\t<div class=\"box6 hide discounttype-dialog\">\n\t\t\t\t\t\t<div class=\"mark\">\n\t\t\t\t\t\t\t<strong>";
                    echo __("discountpercentage type");
                    echo "</strong>\n\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t<label><input name=\"DiscountPercentageType[";
                    echo $i;
                    echo "]\" type=\"radio\" value=\"line\" ";
                    if($_POST["DiscountPercentageType"][$i] == "line") {
                        echo "checked=\"checked\"";
                    }
                    echo "/> ";
                    echo __("discount on " . $form_type . "line");
                    echo "</label><br />\n\t\t\t\t\t\t\t<label><input name=\"DiscountPercentageType[";
                    echo $i;
                    echo "]\" type=\"radio\" value=\"subscription\" ";
                    if($_POST["DiscountPercentageType"][$i] == "subscription") {
                        echo "checked=\"checked\"";
                    }
                    echo "/> ";
                    echo __("discount on " . $form_type . "line and subscription");
                    echo "</label>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\t\t\t\t</td>\n\t\t\t\t<td valign=\"top\" style=\"";
                    echo $periodic_dates_width;
                    echo "\">\n\t\t\t\t\t<div class=\"periodClick pointer\">\n\t\t\t\t\t\t<span id=\"formJQ-Period-";
                    echo $i;
                    echo "\">";
                    echo $_POST["PeriodicType"][$i] != "once" ? $periodic_dates ? htmlspecialchars(esc($_POST["StartPeriod"][$i])) . " " . __("till") . " " . htmlspecialchars(esc($_POST["EndPeriod"][$i])) : $array_periodic[$_POST["Periodic"][$i]] : __("once");
                    echo "</span> \n\t\t\t\t\t\t<div class=\"ico actionblock arrowdown mar2\" style=\"margin: 2px;\">&nbsp;</div>\n\t\t\t\t\t</div>\n\t\t\t\t\t<div id=\"Periodic-";
                    echo $i;
                    echo "\"  class=\"box6 hide periodic-dialog\" style=\"line-height:20px;position: absolute; padding:0px;width: 250px; z-index:10; margin-top: -15px; padding:0px;\">\n\t\t\t\t\t\t<div class=\"mark\">\n\t\t\t\t\t\t\t<strong>";
                    echo __("recurring invoice line");
                    echo "</strong>\n\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t<label><input id=\"PeriodicType-";
                    echo $i;
                    echo "-once\" name=\"PeriodicType[";
                    echo $i;
                    echo "]\" type=\"radio\" value=\"once\" ";
                    if($_POST["PeriodicType"][$i] == "once") {
                        echo "checked=\"checked\"";
                    }
                    echo "/> ";
                    echo __("no once");
                    echo "</label><br />\n\t\t\t\t\t\t\t<label><input id=\"PeriodicType-";
                    echo $i;
                    echo "-period\" name=\"PeriodicType[";
                    echo $i;
                    echo "]\" type=\"radio\" value=\"period\" ";
                    if($_POST["PeriodicType"][$i] == "period") {
                        echo "checked=\"checked\"";
                    }
                    echo "/> ";
                    echo __("yes setup a period");
                    echo "</label><br />\n\t\t\t\t\t\t\t<div id=\"Periodic-period-";
                    echo $i;
                    echo "\" ";
                    if($_POST["PeriodicType"][$i] == "once") {
                        echo "class=\"hide\"";
                    }
                    echo " style=\"margin-top: 14px;\">\n\t\t\t\t\t\t\t\t<strong>";
                    echo __("recurring period");
                    echo "</strong><br />\n\t\t\t\t\t\t\t\t<input name=\"Periods[";
                    echo $i;
                    echo "]\" value=\"";
                    echo htmlspecialchars(esc($_POST["Periods"][$i]));
                    echo "\" class=\"text1 size3\" />\n\t\t\t\t\t\t\t\t<select name=\"Periodic[";
                    echo $i;
                    echo "]\" class=\"text1 size7\">\n\t\t\t\t\t\t\t\t\t";
                    foreach ($array_periodic as $key => $value) {
                        if($key != "") {
                            echo "\t\t\t\t\t\t\t\t\t<option value=\"";
                            echo $key;
                            echo "\" ";
                            if($_POST["Periodic"][$i] == $key) {
                                echo "selected=\"selected\"";
                            }
                            echo ">";
                            echo $value;
                            echo "</option>\n\t\t\t\t\t\t\t\t\t";
                        }
                    }
                    echo "\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t";
                    if($periodic_dates) {
                        echo "\t\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t\t<strong style=\"margin-top: 16px;display: inline-block;\">";
                        echo __("startdate period");
                        echo "</strong><br />\n\t\t\t\t\t\t\t\t<input type=\"text\" name=\"StartPeriod[";
                        echo $i;
                        echo "]\" value=\"";
                        echo htmlspecialchars(esc($_POST["StartPeriod"][$i]));
                        echo "\" class=\"text1 size6 datepicker\" />\n\t\t\t\t\t\t\t\t<span id=\"EndPeriod-";
                        echo $i;
                        echo "\" style=\"color: #6c6c6d;padding-left: 5px;\">";
                        echo __("till") . " " . htmlspecialchars(esc($_POST["EndPeriod"][$i]));
                        echo "</span>\n\t\t\t\t\t\t\t\t<input type=\"hidden\" name=\"EndPeriod[";
                        echo $i;
                        echo "]\" value=\"";
                        echo htmlspecialchars(esc($_POST["EndPeriod"][$i]));
                        echo "\" class=\"text1 size6\" />\n\t\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t\t";
                    }
                    echo "\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\t\t\t\t</td>\n\t\t\t\t";
                    if(!empty($array_taxpercentages)) {
                        echo "\t\t\t\t<td valign=\"top\" style=\"width: 58px;\">\n\t\t\t\t\t";
                        if($debtor->Taxable) {
                            $tax = number2db(esc($_POST["TaxPercentage"][$i]));
                            if(!isEmptyFloat($tax)) {
                                $invoiceLineTaxNull = false;
                            }
                        } elseif($_POST["ProductCode"][$i]) {
                            foreach ($products as $key => $value) {
                                if(is_numeric($key) && $value["ProductCode"] == $_POST["ProductCode"][$i]) {
                                    $tax = $value["TaxPercentage"];
                                }
                            }
                        } else {
                            $tax = STANDARD_TAX;
                        }
                        echo "\t\t\t\t\t\n\t\t\t\t\t<span id=\"formJQ-TaxPercentageText-";
                        echo $i;
                        echo "\">\n\t\t\t\t\t\t";
                        echo showNumber($debtor->Taxable ? htmlspecialchars(esc(showNumber($_POST["TaxPercentage"][$i] * 100))) : (isset($debtor->TaxRate1) && !is_null($debtor->TaxRate1) ? (double) $debtor->TaxRate1 * 100 : 0));
                        echo "%\n\t\t\t\t\t</span>\n\t\t\t\t\t\n\t\t\t\t\t<div id=\"taxadjuster-";
                        echo $i;
                        echo "\" class=\"ico actionblock arrowdown mar2 pointer taxadjuster ";
                        if(!$debtor->Taxable) {
                            echo "hide";
                        }
                        echo "\" style=\"margin: 2px;\" onclick=\"\$('#TaxList-";
                        echo $i;
                        echo "').show();\">&nbsp;</div>\n\n\t\t\t\t\t<input type=\"text\" id=\"formJQ-TaxPercentage-";
                        echo $i;
                        echo "\" name=\"TaxPercentage[";
                        echo $i;
                        echo "]\" value=\"";
                        echo htmlspecialchars($tax);
                        echo "\" class=\"hide\"/>\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t<div id=\"TaxList-";
                        echo $i;
                        echo "\" class=\"box6 hide taxlist-dialog\" style=\"position: absolute; margin-top: -15px; padding:0px;\">\n\t\t\t\t\t<div class=\"content mark taxmark\">\n\t\t\t\t\t\t<ul class=\"emaillist\" style=\"border-top: 0px;\">\n\t\t\t\t\t\t";
                        arsort($array_taxpercentages);
                        foreach ($array_taxpercentages as $key => $value) {
                            if(is_numeric($key)) {
                                echo "\t\t\t\t\t\t\t<li style=\"border-bottom: 0px;\"><label><input type=\"radio\" name=\"TaxRadio[";
                                echo $i;
                                echo "]\" value=\"";
                                echo $key;
                                echo "\" ";
                                if($tax == $key) {
                                    echo "checked=\"checked\"";
                                }
                                echo " />&nbsp;";
                                echo vat($value);
                                echo "%</label></li>\n\t\t\t\t\t\t";
                            }
                        }
                        echo "\t\t\t\t\t\t</ul>\n\t\t\t\t\t</div></div>\n\n\t\t\t\t</td>\n\t\t\t\t";
                    }
                    echo "\t\t\t\t<td valign=\"top\" style=\"width: 85px;\">\n\t\t\t\t\t<input type=\"text\" name=\"PriceIncl[";
                    echo $i;
                    echo "]\" value=\"";
                    echo htmlspecialchars(esc(money($_POST["PriceIncl"][$i], false, false)));
                    echo "\" class=\"text1 size6 ar";
                    if($object->VatCalcMethod != "incl") {
                        echo " hide";
                    }
                    echo "\" />\n\t\t\t\t\t<input type=\"text\" name=\"PriceExcl[";
                    echo $i;
                    echo "]\" value=\"";
                    echo htmlspecialchars(esc(money($_POST["PriceExcl"][$i], false, false)));
                    echo "\" class=\"text1 size6 ar";
                    if($object->VatCalcMethod == "incl") {
                        echo " hide";
                    }
                    echo "\" />\n\t\t\t\t\t\n\t\t\t\t\t<span class=\"discount ";
                    echo isset($_POST["DiscountPercentage"][$i]) && !isEmptyFloat(number2db($_POST["DiscountPercentage"][$i])) ? "" : "hide";
                    echo "\"><input type=\"text\" class=\"text1 ar\" style=\"width: 55px;\" name=\"DiscountPercentage[";
                    echo $i;
                    echo "]\" value=\"";
                    echo htmlspecialchars(esc(showNumber(number2db($_POST["DiscountPercentage"][$i]))));
                    echo "\" maxlength=\"5\"/> %</span>\n\t\t\t\t</td>\n\t\t\t\t<td style=\"width:5px;\" class=\"show_col_ws currency_sign_left\" valign=\"top\">";
                    echo currency_sign_td(CURRENCY_SIGN_LEFT);
                    echo "\t\t\t\t<div class=\"discount_helper ";
                    echo isset($_POST["DiscountPercentage"][$i]) && !isEmptyFloat(number2db($_POST["DiscountPercentage"][$i])) ? "" : "hide";
                    echo "\">";
                    echo CURRENCY_SIGN_LEFT;
                    echo "</div></td>\n\t\t\t\t<td style=\"width:75px;\" valign=\"top\" class=\"align_right show_col_ws currency_sign_right\"><span id=\"formJQ-LineTotal-";
                    echo $i;
                    echo "\">";
                    if($object->VatCalcMethod == "incl") {
                        echo money(deformat_money($_POST["PriceIncl"][$i]) * $_POST["Periods"][$i] * $_POST["Number"][$i], false);
                    } else {
                        echo money(esc(deformat_money($_POST["PriceExcl"][$i]) * $_POST["Periods"][$i] * $_POST["Number"][$i]), false);
                    }
                    echo "</span>";
                    if(CURRENCY_SIGN_RIGHT) {
                        echo " " . CURRENCY_SIGN_RIGHT;
                    }
                    echo "    \t\t\t\t<div class=\"discount_helper ";
                    echo isset($_POST["DiscountPercentage"][$i]) && !isEmptyFloat(number2db($_POST["DiscountPercentage"][$i])) ? "" : "hide";
                    echo "\">\n                        <span>\n                        ";
                    $discount_price = ($object->VatCalcMethod == "incl" ? deformat_money($_POST["PriceIncl"][$i]) : deformat_money($_POST["PriceExcl"][$i])) * $_POST["Periods"][$i] * $_POST["Number"][$i] * (double) $_POST["DiscountPercentage"][$i] / 100;
                    $discount_price = 0 < $discount_price ? "- " . money(esc(abs($discount_price)), false) : money(esc(abs($discount_price)), false);
                    echo $discount_price;
                    echo "                        </span>\n                        ";
                    if(CURRENCY_SIGN_RIGHT) {
                        echo " " . CURRENCY_SIGN_RIGHT;
                    }
                    echo "                    </div>\n                </td>\n\n\t\t\t\t<td style=\"width:50px;\" valign=\"top\" class=\"invoiceTDempty iconbox\">\n\t\t\t\t\t<img src=\"images/ico_discount.png\" onclick=\"add_discount_rule('";
                    echo $i;
                    echo "', this);\" class=\"pointer sortablehandle discount_icon invoicetrhide ";
                    echo isset($_POST["DiscountPercentage"][$i]) && !isEmptyFloat(number2db($_POST["DiscountPercentage"][$i])) ? "hide" : "";
                    echo "\" />\n\t\t\t\t\t<img src=\"images/ico_trash.png\" onclick=\"removeElement('";
                    echo $i;
                    echo "');\" class=\"invoicetrhide remove_icon pointer\"/>\n\t\t\t\t\t<img src=\"images/ico_trash.png\" onclick=\"remove_discount_rule('";
                    echo $i;
                    echo "', this);\" class=\"invoicetrhide discount_remove_icon pointer ";
                    echo isset($_POST["DiscountPercentage"][$i]) && !isEmptyFloat(number2db($_POST["DiscountPercentage"][$i])) ? "" : "hide";
                    echo "\" style=\"margin-top: 20px;clear:both;\"/>\n\t\t\t\t</td>\n\t\t\t</tr>";
                }
            }
            $x--;
        }
    }
    $i = isset($_POST["Date"]) ? count($_POST["Date"]) : $i;
    echo "\t\t<tr id=\"NewElement\" class=\"tr2 valign_top tr_invoiceelement ";
    if(isset($object_id) && 0 < $object_id || !empty($_POST) && 0 < $linesDisplayed) {
        echo "hide";
    }
    echo "\">\n\t\t\t<td style=\"width:30px;\" class=\"invoiceTDempty\"><img src=\"images/ico_sort.png\" class=\"pointer sortablehandle sort_icon invoicetrhide\" /></td>\n\t\t\t<td style=\"width:40px;\" valign=\"top\">\n\t\t\t\t<span class=\"input_date\">\n\t\t\t\t\t<input type=\"text\" class=\"text1 size6 datepicker_icon\" name=\"Date[";
    echo $i;
    echo "]\" value=\"";
    echo (int) $object->Status === 0 ? rewrite_date_db2site(date("Ymd")) : $object->Date;
    echo "\" />\n\t\t\t\t</span>\n\t\t\t\t<input type=\"hidden\" name=\"Item[";
    echo $i;
    echo "]\" />\n\t\t\t</td>\n\t\t\t<td valign=\"top\" style=\"width: 50px;\"><input type=\"text\" class=\"text1 size3\" name=\"Number[";
    echo $i;
    echo "]\" value=\"1\" /></td>\n\t\t\t<td valign=\"top\" style=\"width: 165px;\">\n\t\t\t\t";
    $selected_product_code = $selected_name = "";
    foreach ($products as $key => $value) {
        if(is_numeric($key) && isset($_POST["ProductCode"][$i]) && $_POST["ProductCode"][$i] == $value["ProductCode"]) {
            $selected_product_code = $value["ProductCode"];
            $selected_name = $value["ProductCode"] . " " . $value["ProductName"];
        }
    }
    echo "\t\t\t\t<input type=\"hidden\" name=\"ProductCode[";
    echo $i;
    echo "]\" value=\"";
    echo $selected_product_code;
    echo "\" onchange=\"getProductData('";
    echo $i;
    echo "',this.value);\"/>\n\t\t\t\t";
    createAutoComplete("product", "ProductCode[" . $i . "]", $selected_name, ["return_type" => "code"]);
    echo "\t\t\t</td>\n\t\t\t<td valign=\"top\">\n\t\t\t\t<textarea name=\"Description[";
    echo $i;
    echo "]\" class=\"text1 autogrow\" style=\"width: 97%;\"></textarea>\t\t\n\t\t\t\t<span class=\"discountpercentage_type discount hide\"><span class=\"discountpercentage_type_line\">";
    echo __("discount on " . $form_type . "line");
    echo "</span><span class=\"discountpercentage_type_subscription hide\">";
    echo __("discount on " . $form_type . "line and subscription");
    echo "</span> <div style=\"margin: 2px;\" class=\"ico actionblock arrowdown mar2 hide\">&nbsp;</div>\n\t\t\t\t\t\n\t\t\t\t<div class=\"box6 hide discounttype-dialog\">\n\t\t\t\t\t<div class=\"mark\">\n\t\t\t\t\t\t<strong>";
    echo __("discountpercentage type");
    echo "</strong>\n\t\t\t\t\t\t<br />\n\t\t\t\t\t\t<label><input name=\"DiscountPercentageType[";
    echo $i;
    echo "]\" type=\"radio\" value=\"line\" checked=\"checked\"/> ";
    echo __("discount on " . $form_type . "line");
    echo "</label><br />\n\t\t\t\t\t\t<label><input name=\"DiscountPercentageType[";
    echo $i;
    echo "]\" type=\"radio\" value=\"subscription\" /> ";
    echo __("discount on " . $form_type . "line and subscription");
    echo "</label>\n\t\t\t\t\t</div>\n\t\t\t\t</div>\n\t\t\t\t\n\t\t\t</td>\n\t\t\t<td valign=\"top\" style=\"";
    echo $periodic_dates_width;
    echo "\">\n\t\t\t\t\n\t\t\t\t<div class=\"periodClick pointer\">\n\t\t\t\t\t<span id=\"formJQ-Period-";
    echo $i;
    echo "\">";
    echo __("once");
    echo "</span> \n\t\t\t\t\t<div class=\"ico actionblock arrowdown mar2\" style=\"margin: 2px;\">&nbsp;</div>\n\t\t\t\t</div>\n\t\t\t\t\n\t\t\t\t<div id=\"Periodic-";
    echo $i;
    echo "\"  class=\"box6 hide periodic-dialog\" style=\"line-height:20px;position: absolute; padding:0px;width: 250px; z-index:10; margin-top: -15px; padding:0px;\">\n\t\t\t\t<div class=\"mark\">\n\t\t\t\t\t<strong>";
    echo __("recurring invoice line");
    echo "</strong>\n\t\t\t\t\t<br />\n\t\t\t\t\t<label><input id=\"PeriodicType-";
    echo $i;
    echo "-once\" name=\"PeriodicType[";
    echo $i;
    echo "]\" type=\"radio\" value=\"once\" checked=\"checked\"/> ";
    echo __("no once");
    echo "</label><br />\n\t\t\t\t\t<label><input id=\"PeriodicType-";
    echo $i;
    echo "-period\" name=\"PeriodicType[";
    echo $i;
    echo "]\" type=\"radio\" value=\"period\"/> ";
    echo __("yes setup a period");
    echo "</label><br />\n\t\t\t\t\t<div id=\"Periodic-period-";
    echo $i;
    echo "\" class=\"hide\" style=\"margin-top: 14px;\">\n\t\t\t\t\t\t<strong>";
    echo __("recurring period");
    echo "</strong><br />\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size3\" name=\"Periods[";
    echo $i;
    echo "]\" value=\"1\" />\n\t\t\t\t\t\t<select name=\"Periodic[";
    echo $i;
    echo "]\" class=\"text1 size7\">\n\t\t\t\t\t\t\t";
    foreach ($array_periodic as $key => $value) {
        if($key != "") {
            echo "\t\t\t\t\t\t\t<option value=\"";
            echo $key;
            echo "\" ";
            if($key == "m") {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo $value;
            echo "</option>\n\t\t\t\t\t\t\t";
        }
    }
    echo "\t\t\t\t\t\t</select>\n\t\t\t\t\t\t";
    if($periodic_dates) {
        echo "<br />\n\t\t\t\t\t\t<strong style=\"margin-top: 16px;display: inline-block;\">";
        echo __("startdate period");
        echo "</strong><br />\n\t\t\t\t\t\t<input type=\"text\" name=\"StartPeriod[";
        echo $i;
        echo "]\" class=\"text1 size6 datepicker\" />\n\t\t\t\t\t\t<span id=\"EndPeriod-";
        echo $i;
        echo "\" style=\"color: #6c6c6d;padding-left: 5px;\"></span>\n\t\t\t\t\t\t<input type=\"hidden\" name=\"EndPeriod[";
        echo $i;
        echo "]\" value=\"\" class=\"text1 size6\" /><br />\n\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t</div>\n\t\t\t\t</div></div></td>\n\t\t\t\t";
    if(!empty($array_taxpercentages)) {
        echo "\t\t\t\t<td valign=\"top\" style=\"width: 58px;\">\n\t\t\t\t\t<div class=\"taxAdjusterClick pointer\">\n\t\t\t\t\t\t<span id=\"formJQ-TaxPercentageText-";
        echo $i;
        echo "\">\n\t\t\t\t\t\t\t";
        echo showNumber($debtor->Taxable ? STANDARD_TAX * 100 : (isset($debtor->TaxRate1) && !is_null($debtor->TaxRate1) ? (double) $debtor->TaxRate1 * 100 : 0));
        echo "%\n\t\t\t\t\t\t</span>\n\t\t\t\t\t\t<div id=\"taxadjuster-";
        echo $i;
        echo "\" class=\"ico actionblock arrowdown mar2 taxadjuster ";
        if(!$debtor->Taxable) {
            echo "hide";
        }
        echo "\" style=\"margin: 2px;\" onclick=\"\$('#TaxList-";
        echo $i;
        echo "').show();\">&nbsp;</div>\n\t\t\t\t\t</div>\n\t\t\t\t\t\n\t\t\t\t\t<input type=\"text\" id=\"formJQ-TaxPercentage-";
        echo $i;
        echo "\" name=\"TaxPercentage[";
        echo $i;
        echo "]\" value=\"";
        echo STANDARD_TAX;
        echo "\" class=\"hide\"/>\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t<div id=\"TaxList-";
        echo $i;
        echo "\" class=\"box6 hide taxlist-dialog\" style=\"position: absolute; margin-top: -15px; padding:0px;\">\n\t\t\t\t\t<div class=\"content mark taxmark\">\n\t\t\t\t\t\t<ul class=\"emaillist\" style=\"border-top: 0px;\">\n\t\t\t\t\t\t";
        arsort($array_taxpercentages);
        foreach ($array_taxpercentages as $key => $value) {
            if(is_numeric($key)) {
                echo "\t\t\t\t\t\t\t<li style=\"border-bottom: 0px;\"><label><input type=\"radio\" name=\"TaxRadio[";
                echo $i;
                echo "]\" value=\"";
                echo $key;
                echo "\" ";
                if(STANDARD_TAX == $key) {
                    echo "checked=\"checked\"";
                }
                echo "/>&nbsp;";
                echo vat($value);
                echo "%</label></li>\n\t\t\t\t\t\t";
            }
        }
        echo "\t\t\t\t\t\t</ul>\n\t\t\t\t\t</div></div>\n\t\n\t\t\t\t</td>\n\t\t\t";
    }
    echo "\t\t\t<td valign=\"top\" style=\"width: 85px;\">\n\t\t\t\t<input type=\"text\" name=\"PriceIncl[";
    echo $i;
    echo "]\" class=\"text1 size6 ar";
    if($object->VatCalcMethod != "incl") {
        echo " hide";
    }
    echo "\" />\n\t\t\t\t<input type=\"text\" name=\"PriceExcl[";
    echo $i;
    echo "]\" class=\"text1 size6 ar";
    if($object->VatCalcMethod == "incl") {
        echo " hide";
    }
    echo "\" />\n\t\n\t\t\t\t<span class=\"discount hide\"><input type=\"text\" class=\"text1 ar\" style=\"width: 55px;\" name=\"DiscountPercentage[";
    echo $i;
    echo "]\" maxlength=\"5\"/> %</span>\n\t\t\t</td>\n\t\t\t<td class=\"show_col_ws currency_sign_left\" valign=\"top\" style=\"width:5px;\">";
    echo currency_sign_td(CURRENCY_SIGN_LEFT);
    echo "<div class=\"discount_helper hide\">";
    echo CURRENCY_SIGN_LEFT;
    echo "</div></td>\n\t\t\t<td style=\"width:75px;\" valign=\"top\" class=\"align_right show_col_ws currency_sign_right\"><span id=\"formJQ-LineTotal-";
    echo $i;
    echo "\">";
    echo money(0, false);
    echo "</span> ";
    if(CURRENCY_SIGN_RIGHT) {
        echo " " . CURRENCY_SIGN_RIGHT;
    }
    echo "<div class=\"discount_helper hide\"><span>0,00</span>";
    if(CURRENCY_SIGN_RIGHT) {
        echo " " . CURRENCY_SIGN_RIGHT;
    }
    echo "</div></td>\n\t\t\t<td style=\"width:50px;\" valign=\"top\" class=\"invoiceTDempty iconbox\">\n\t\t\t\t<img src=\"images/ico_discount.png\" onclick=\"add_discount_rule('";
    echo $i;
    echo "', this);\" class=\"pointer discount_icon invoicetrhide\" />\n\t\t\t\t<img src=\"images/ico_trash.png\" onclick=\"removeElement('";
    echo $i;
    echo "');\" class=\"invoicetrhide remove_icon pointer\"/>\n\t\t\t\t<img src=\"images/ico_trash.png\" onclick=\"remove_discount_rule('";
    echo $i;
    echo "', this);\" class=\"invoicetrhide discount_remove_icon pointer hide\" style=\"margin-top: 18px;clear:both;\"/>\n\t\t\t</td>\n\t\t</tr>\n\t\t</tbody>\n\t\t</table>\n\t\t\n\t\t<div style=\"margin: 0 22px 0 30px;\">\n\t\t\t<input type=\"hidden\" name=\"NumberOfElements\" value=\"";
    echo isset($number_elements) ? $number_elements : "0";
    echo "\" />\n\t\t\t<span id=\"add_new_element\" class=\"pointer\"><img src=\"images/ico_add.png\" style=\"float: left; margin-right: 10px;\"/> ";
    echo __($form_type . " add new line");
    echo "</span>\n\t\t\n\t\t\t<span id=\"discount_link\" class=\"c1 pointer\" style=\"float:right; margin-right: 30px;\">";
    if(isEmptyFloat(number2db($object->Discount))) {
        echo __("invoice add discount");
    } else {
        echo __("invoice edit discount");
    }
    echo "?</span>\n\t\t\t\t\n\t\t\t<div id=\"discount_dialog\" class=\"discountbox mark hide\" style=\"float:right;\">\n\t\t\t\t<a class=\"close pointer\">";
    echo __("close");
    echo "</a>\n\t\t\t\t\n\t\t\t\t<table>\n\t\t\t\t<tr>\n\t\t\t\t\t<td colspan=\"2\"><strong>";
    echo __($form_type . " add discount to " . $form_type);
    echo "</strong><br /><br /></td>\n\t\t\t\t</tr>\n\t\t\t\t<tr>\n\t\t\t\t\t<td style=\"width:135px;\">";
    echo __("coupon");
    echo "</td>\n\t\t\t\t\t<td><input type=\"text\" name=\"Coupon\" value=\"";
    echo $object->Coupon ? $object->Coupon : "";
    echo "\" class=\"text1 size7\" /></td>\n\t\t\t\t</tr>\n\t\t\t\t<tr>\n\t\t\t\t\t<td>";
    echo __("invoice discount percentage");
    echo "</td>\n\t\t\t\t\t<td><input type=\"text\" name=\"Discount\" value=\"";
    echo $object->Discount ? showNumber(number2db($object->Discount)) : "";
    echo "\" class=\"text1 size3\" maxlength=\"5\" /> %</td>\n\t\t\t\t</tr>\n\t\t\t\t</table>\n\t\t\t\t\n\t\t\t\t<a class=\"button1 alt1\" id=\"discount_btn\"><span>";
    if(isEmptyFloat(number2db($object->Discount))) {
        echo __("invoice add discount");
    } else {
        echo __("invoice edit discount");
    }
    echo "</span></a>\n\t\t\n\t\t\t</div>\n\t\t\t<br clear=\"both\" />\n\t\t</div>\n\t\t";
    if(!empty($array_taxpercentages) || !empty($array_total_taxpercentages)) {
        $vatShiftInputHelper = "true";
        $vatShiftClassHelper = "";
        $tmp_show_taxrate = isset($debtor->TaxRate2) && !is_null($debtor->TaxRate2) ? (string) (double) $debtor->TaxRate2 : (string) (double) $object->TaxRate;
        if(empty($debtor->Identifier) || !$debtor->Taxable && 0 < $debtor->TaxRate1 || $debtor->Taxable && (!$invoiceLineTaxNull || 0 < $tmp_show_taxrate || (string) $debtor->TaxRate2 === "" && (int) $i === 0 || 0 < $debtor->TaxRate2)) {
            $vatShiftClassHelper = "hide";
            $vatShiftInputHelper = "";
        }
        echo "\t\t\t<!--box6-->\n\t\t\t<div id=\"shiftvat_div\" class=\"";
        echo $vatShiftClassHelper;
        echo "\" style=\"float: left; margin-top: 46px; margin-left: 29px; border: 1px #cccccc dashed; border-radius: 5px; padding: 12px 11px 12px 9px;\">\n\t\t\t<!--box6-->\n\t\t\t\t<input type=\"hidden\" value=\"";
        echo $vatShiftInputHelper;
        echo "\" name=\"VatShift_helper\" />\n\t\t\t\t<label><input style=\"float: left;margin: 2px 4px 0 0;\" type=\"checkbox\" name=\"VatShift\" value=\"yes\" ";
        if($object->VatShift == "yes" || $object->VatShift == "" && (!$debtor->Taxable || $debtor->Taxable && (string) $debtor->TaxRate2 !== "" && isEmptyFloat(number2db($debtor->TaxRate2)))) {
            echo "checked=\"checked\"";
        }
        echo " /> ";
        echo __("vatshift on invoice");
        echo "</label>\n\t\t\t\t\n\t\t\t<!--box6-->\n\t\t\t</div>\n\t\t\t<!--box6-->\n\t\t\t\n\t\t\t";
    }
    echo "\t\t\n\t\t<!--box6-->\n\t\t<div class=\"box6\" style=\"margin-right: 52px;\">\n\t\t<!--box6-->\n\t\t\n\t\t\t<table class=\"table7\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\n\t\t\t<tr id=\"discount_tr\" ";
    if(isEmptyFloat(deformat_money($object->Discount))) {
        echo "class=\"hide\"";
    }
    echo ">\n\t\t\t\t<td>";
    echo __("discount");
    echo ": <span id=\"discount_txt\">";
    echo showNumber(deformat_money($object->Discount));
    echo "</span>%</td>\n\t\t\t\t<td style=\"width:15px;\" class=\"currency_sign_left\">";
    echo currency_sign_td(CURRENCY_SIGN_LEFT);
    echo "</td>\n\t\t\t\t<td style=\"width:80px;\" class=\"align_right\" id=\"total-discount\">";
    echo money(0, false);
    echo "</td>\n\t\t\t\t";
    if(CURRENCY_SIGN_RIGHT) {
        echo "<td style=\"width:15px;\" class=\"currency_sign_right\">";
        echo currency_sign_td(CURRENCY_SIGN_RIGHT);
        echo "</td>";
    }
    echo "\t\t\t</tr>\n\t\t\t";
    if(!empty($array_taxpercentages) || !empty($array_total_taxpercentages)) {
        echo "\t\t\t\t<tr>\n\t\t\t\t\t<td style=\"border-top: 1px solid black;\">";
        echo __("invoice total excl vat");
        echo "</td>\n\t\t\t\t\t<td style=\"border-top: 1px solid black; width:15px;\" class=\"currency_sign_left\">";
        echo currency_sign_td(CURRENCY_SIGN_LEFT);
        echo "</td>\n\t\t\t\t\t<td style=\"border-top: 1px solid black; width:80px;\" class=\"align_right\" id=\"total-excl\">";
        echo money(0, false);
        echo "</td>\n\t\t\t\t\t";
        if(CURRENCY_SIGN_RIGHT) {
            echo "<td style=\"border-top: 1px solid black;\" class=\"currency_sign_right\">";
            echo currency_sign_td(CURRENCY_SIGN_RIGHT);
            echo "</td>";
        }
        echo "\t\t\t\t</tr>\t\t\t\n\t\t\t\t";
    }
    asort($array_taxpercentages);
    foreach ($array_taxpercentages as $key => $value) {
        echo "\t\t\t\t<tr class=\"linetax_tr ";
        if(0 < $i || !$debtor->Taxable || $debtor->Taxable && $key != STANDARD_TAX || isset($debtor->TaxRate1) && !is_null($debtor->TaxRate1) && $key != (double) $debtor->TaxRate1) {
            echo "hide";
        }
        echo "\">\n\t\t\t\t\t<td>";
        echo isset($array_taxpercentages_info[(string) (double) $key]["label"]) ? $array_taxpercentages_info[(string) (double) $key]["label"] : "";
        echo "</td>\n\t\t\t\t\t<td class=\"currency_sign_left\">";
        echo currency_sign_td(CURRENCY_SIGN_LEFT);
        echo "</td>\n\t\t\t\t\t<td class=\"align_right\" id=\"total-tax-";
        echo str_replace(".", "_", $key);
        echo "\">";
        echo isset($object->used_taxrates[(string) (double) $key]["AmountTax"]) ? $object->used_taxrates[(string) (double) $key]["AmountTax"] : money(0, false);
        echo "</td>\n\t\t\t\t\t";
        if(CURRENCY_SIGN_RIGHT) {
            echo "<td class=\"currency_sign_right\">";
            echo currency_sign_td(CURRENCY_SIGN_RIGHT);
            echo "</td>";
        }
        echo "\t\t\t\t</tr>\n\t\t\t\t";
    }
    echo "\t\t\t\n\t\t\t";
    arsort($array_total_taxpercentages);
    if(!empty($array_total_taxpercentages)) {
        $tmp_show_taxrate = isset($debtor->TaxRate2) && !is_null($debtor->TaxRate2) ? (string) (double) $debtor->TaxRate2 : (string) (double) $object->TaxRate;
        echo "\t\t\t\t<tr>\n\t\t\t\t\t<td>\n\t\t\t\t\t\t<span>";
        echo isset($array_total_taxpercentages_info[$tmp_show_taxrate]["label"]) ? $array_total_taxpercentages_info[$tmp_show_taxrate]["label"] : "";
        echo "</span>\n\t\t\t\t\t\n\t\t\t\t\t\t<div id=\"taxadjuster-total\" class=\"ico actionblock arrowdown mar2 pointer taxadjusterTotal ";
        if(isset($debtor->TaxRate2) && !is_null($debtor->TaxRate2)) {
            echo "hide";
        }
        echo "\" style=\"margin: 2px;\" onclick=\"\$('#TaxList-total').show();\">&nbsp;</div>\n\t\t\t\t\t\t<input type=\"text\" name=\"TaxRate\" value=\"";
        echo (double) $object->TaxRate;
        echo "\" class=\"hide\"/>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<div id=\"TaxList-total\" class=\"box6 hide taxlist-dialog\" style=\"position: absolute; margin-top: -12px; padding:0px;\">\n\t\t\t\t\t\t<div class=\"content mark taxmark\">\n\t\t\t\t\t\t\t<ul class=\"emaillist\" style=\"border-top: 0px;\">\n\t\t\t\t\t\t\t";
        foreach ($array_total_taxpercentages as $key => $value) {
            if(is_numeric($key)) {
                echo "\t\t\t\t\t\t\t\t<li style=\"border-bottom: 0px;\">\n\t\t\t\t\t\t\t\t\t<label><input type=\"radio\" name=\"TotalTaxRadio\" value=\"";
                echo $key;
                echo "\" ";
                if($object->TaxRate == $key) {
                    echo "checked=\"checked\"";
                }
                echo " />&nbsp;\n\t\t\t\t\t\t\t\t\t";
                echo vat($value);
                echo "%\n\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t<span class=\"hide\">";
                echo isset($array_total_taxpercentages_info[$key]["label"]) ? $array_total_taxpercentages_info[$key]["label"] : "";
                echo "</span>\n\t\t\t\t\t\t\t\t\t<input type=\"hidden\" name=\"TaxCompound[";
                echo $key;
                echo "]\" value=\"";
                echo isset($array_total_taxpercentages_info[$key]["compound"]) ? $array_total_taxpercentages_info[$key]["compound"] : "no";
                echo "\" />\n\t\t\t\t\t\t\t\t\t</label></li>\n\t\t\t\t\t\t\t";
            }
        }
        echo "\t\t\t\t\t\t\t</ul>\n\t\t\t\t\t\t</div></div>\n\t\t\t\t\t</td>\n\t\t\t\t\t<td class=\"currency_sign_left\">";
        echo currency_sign_td(CURRENCY_SIGN_LEFT);
        echo "</td>\n\t\t\t\t\t<td class=\"align_right\" id=\"total-tax-level2\">";
        echo $object->TaxRate_Amount ? $object->TaxRate_Amount : money(0, false);
        echo "</td>\n\t\t\t\t\t";
        if(CURRENCY_SIGN_RIGHT) {
            echo "<td class=\"currency_sign_right\">";
            echo currency_sign_td(CURRENCY_SIGN_RIGHT);
            echo "</td>";
        }
        echo "\t\t\t\t</tr>\n\t\t\t\t";
    }
    echo "\t\t\t<tr class=\"line\">\n\t\t\t\t<td style=\"border-top: 1px solid black;\">";
    if(empty($array_taxpercentages) && empty($array_total_taxpercentages)) {
        echo __("invoice total");
    } else {
        echo __("invoice total incl vat");
    }
    echo "</td>\n\t\t\t\t<td style=\"border-top: 1px solid black;\" class=\"currency_sign_left\">";
    echo currency_sign_td(CURRENCY_SIGN_LEFT);
    echo "</td>\n\t\t\t\t<td style=\"border-top: 1px solid black;\" class=\"align_right\" id=\"total-incl\">";
    echo money(0, false);
    echo "</td>\n\t\t\t\t";
    if(CURRENCY_SIGN_RIGHT) {
        echo "<td style=\"border-top: 1px solid black;\" class=\"currency_sign_right\">";
        echo currency_sign_td(CURRENCY_SIGN_RIGHT);
        echo "</td>";
    }
    echo "\t\t\t</tr>\n\t\t\t</table>\n\n\t\t<!--box6-->\n\t\t</div>\n\t\t<!--box6-->\n\t\t\n\t\t<br clear=\"both\"/>\n\t\t<p style=\"float: right; padding: 0 52px 10px 0; font-style: italic; color: #414042;\">";
    echo __($form_type . " discount explanation");
    echo "</p>\n\t\t<br clear=\"both\"/>\n\t";
}

?>