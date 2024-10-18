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
echo isset($message) ? $message : "";
echo "\n<!--heading1-->\n<div class=\"heading1\">\n<!--heading1-->\n\n\t<h2>";
echo __("discount") . " " . $discount->Name;
echo "</h2>\n\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n\n\n\t<!--box1-->\n\t<div class=\"box2\" id=\"tabs\">\n\t<!--box1-->\n\t\n\t\t<!--top-->\n\t\t<div class=\"top\">\n\t\t<!--top-->\n\t\t\n\t\t\t<ul class=\"list3\">\n\t\t\t\t<li class=\"on\"><a href=\"#tab-general\">";
echo __("discounttab general");
echo "</a></li>\n\t\t\t\t<li><a href=\"#tab-extended\">";
echo __("discounttab extended");
echo "</a></li>\n\t\t\t\t<li><a href=\"#tab-restrictions\">";
echo __("discounttab restrictions");
echo "</a></li>\n\t\t\t</ul>\n\t\t\t\n\t\t\n\t\t<!--top-->\n\t\t</div>\n\t\t<!--top-->\n\n\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-general\">\n\t\t<!--content-->\n\t\t\n\t\t\t<!--split2-->\n\t\t\t<div class=\"split2\">\n\t\t\t<!--split2-->\n\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t<div class=\"left\">\n\t\t\t\t<!--left-->\n\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("discount general");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("discount name");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
echo $discount->Name;
echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t";
if($discount->DiscountType == "TotalAmount") {
    echo "\t\t\t\t\t\t<strong class=\"title2\">";
    echo __("discount description invoice");
    echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
    echo $discount->Description;
    echo "</span>\n\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("discount type");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
switch ($discount->DiscountType) {
    case "TotalAmount":
        echo __("discount type fixed amount");
        break;
    case "TotalPercentage":
        echo __("discount type fixed percentage");
        break;
    case "PartialRestrictedPercentage":
        echo __("discount type partial restricted percentage");
        break;
    case "PartialPercentage":
        echo __("discount type partial percentage");
        break;
    case "PartialAmount":
        echo __("discount type product price");
        break;
    default:
        echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t";
        switch ($discount->DiscountType) {
            case "TotalAmount":
                echo "<strong class=\"title2\">";
                echo __("discount amount");
                echo "</strong><span class=\"title2_value\">";
                echo money($discount->Discount);
                echo "</span>";
                break;
            case "TotalPercentage":
                echo "<strong class=\"title2\">";
                echo __("discount type percentage");
                echo "</strong><span class=\"title2_value\">";
                echo showNumber($discount->DiscountPercentage) . "%";
                echo "</span>";
                break;
            case "PartialRestrictedPercentage":
                echo "<strong class=\"title2\">";
                echo __("discount type percentage");
                echo "</strong><span class=\"title2_value\">";
                echo sprintf($discount->DiscountPercentageType == "subscription" ? __("x discount on invoiceline and subscription") : __("x discount on invoiceline"), showNumber($discount->DiscountPart));
                echo "</span>";
                break;
            case "PartialPercentage":
                echo "<strong class=\"title2\">";
                echo __("discount product restrictions table restriction");
                echo "</strong><span class=\"title2_value\">";
                echo __("discount product restrictions table restriction") . " " . $discount->DiscountPartRestriction;
                echo "</span>\n\t\t\t\t\t\t\t<strong class=\"title2\">";
                echo __("discount type percentage");
                echo "</strong><span class=\"title2_value\">";
                echo sprintf($discount->DiscountPercentageType == "subscription" ? __("x discount on invoiceline and subscription") : __("x discount on invoiceline"), showNumber($discount->DiscountPart));
                echo "</span>";
                break;
            case "PartialAmount":
                echo "<strong class=\"title2\">";
                echo __("discount product restrictions table restriction");
                echo "</strong><span class=\"title2_value\">";
                echo __("discount product restrictions table restriction") . " " . $discount->DiscountPartRestriction;
                echo "</span>\n\t\t\t\t\t\t\t<strong class=\"title2\">";
                echo __("discount amount");
                echo "</strong><span class=\"title2_value\">";
                echo money($discount->DiscountPart);
                echo "</span>";
                break;
            default:
                echo "\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t</div>\n\t\t\t\t<!--left-->\n\t\t\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t<div class=\"right\">\n\t\t\t\t<!--right-->\n\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
                echo __("discount for");
                echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
                echo __("discount for debtor(group)");
                echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">\n\t\t\t\t\t\t";
                switch ($discount->DebtorRestriction) {
                    case "group":
                        echo __("discount for debtorgroup(s)");
                        break;
                    case "debtor":
                        echo __("discount for debtor");
                        break;
                    case "-1":
                        echo __("discount for new debtors");
                        break;
                    case "-2":
                        echo __("discount for existing debtors");
                        break;
                    case "-3":
                        echo __("discount for auth debtors");
                        break;
                    default:
                        echo __("discount for all debtors");
                        echo "\t\t\t\t\t\t</span>\n\n\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t";
                        if($discount->DebtorRestriction == "group") {
                            echo "\t\t\t\t\t\t<p>\n\t\t\t\t\t\t\t<strong>";
                            echo __("debtorgroups");
                            echo ":</strong>\n\t\t\t\t\t\t</p>\n\t\t\t\t\t\t<div class=\"height1 overflow-y\">\n\t\t\t\t\t\t\t<ul class=\"emaillist\">\n\t\t\t\t\t\t\t\t";
                            foreach ($debtorgroups as $groupID => $debtorGroup) {
                                if(is_numeric($groupID) && in_array($groupID, $discount->DebtorGroup)) {
                                    echo "\t\t\t\t\t\t\t\t<li><span class=\"mar2\">";
                                    echo $debtorGroup["GroupName"];
                                    echo "</span></li>\n\t\t\t\t\t\t\t\t";
                                }
                            }
                            echo "\t\t\t\t\t\t\t</ul>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\t";
                        }
                        echo "\t\t\t\t\t\t\n\t\t\t\t\t\t";
                        if($discount->DebtorRestriction == "debtor") {
                            echo "\t\t\t\t\t\t<strong class=\"title2\">";
                            echo __("debtor");
                            echo ":</strong>\n\t\t\t\t\t\t<span class=\"title2_value\"><a href=\"debtors.php?page=show&id=";
                            echo $discount->Debtor;
                            echo "\" class=\"c1 a1\">";
                            echo $debtors[$discount->Debtor]["CompanyName"] ? $debtors[$discount->Debtor]["CompanyName"] : $debtors[$discount->Debtor]["SurName"] . ", " . $debtors[$discount->Debtor]["Initials"];
                            echo "</a></span>\n\t\t\t\t\t\t";
                        }
                        echo "\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t</div>\n\t\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t<!--split2-->\n\t\t\t</div>\n\t\t\t<!--split2-->\n\t\t\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\t\t\n\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-extended\">\n\t\t<!--content-->\n\t\t\n\t\t\t<!--split2-->\n\t\t\t<div class=\"split2\">\n\t\t\t<!--split2-->\n\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t<div class=\"left\">\n\t\t\t\t<!--left-->\n\t\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
                        echo __("discount valid");
                        echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t<p>\n\t\t\t\t\t\t";
                        if($discount->StartDate && $discount->EndDate) {
                            echo sprintf(__("discount valid view between"), $discount->StartDate, $discount->EndDate);
                        } elseif($discount->EndDate) {
                            echo sprintf(__("discount valid view till"), $discount->EndDate);
                        } else {
                            echo __("discount valid view always");
                        }
                        echo "\t\t\t\t\t\t</p>\n\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t</div>\n\t\t\t\t<!--left-->\n\t\t\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t<div class=\"right\">\n\t\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
                        echo __("discount restrictions");
                        echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
                        echo __("discount restriction coupon");
                        echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
                        echo $discount->Coupon;
                        echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
                        echo __("discount counter");
                        echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
                        echo $discount->Counter;
                        echo " ";
                        if(0 < $discount->Max) {
                            echo "/ ";
                            echo $discount->Max;
                        }
                        echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t";
                        if(!isEmptyFloat($discount->MaxPerInvoice)) {
                            echo "\t\t\t\t\t\t<strong class=\"title2\">";
                            echo __("discount restrictions max per invoice short");
                            echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
                            echo $discount->MaxPerInvoice;
                            echo " x</span>\n\t\t\t\t\t\t";
                        }
                        echo "\t\t\t\t\t\t\n\t\t\t\t\t\t";
                        if(!isEmptyFloat($discount->MinAmount)) {
                            echo "\t\t\t\t\t\t<strong class=\"title2\">";
                            echo __("discount restrictions amount short");
                            echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
                            echo money($discount->MinAmount);
                            echo "</span>\n\t\t\t\t\t\t";
                        }
                        echo "\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
                        echo __("discount document type");
                        echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
                        if($discount->DocumentType == "") {
                            echo __("discount document type all");
                        } else {
                            echo __("discount document type order");
                        }
                        echo "</span>\n\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t</div>\n\t\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t<!--split2-->\n\t\t\t</div>\n\t\t\t<!--split2-->\n\t\t\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\t\t\n\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-restrictions\">\n\t\t<!--content-->\n\t\t\t\t\t\t\n\t\t\t<!--box3-->\n\t\t\t<div class=\"box3\"><h3>";
                        echo __("discount product restrictions");
                        echo "</h3><div class=\"content\">\n\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t";
                        if(!($discount->Product1 || $discount->Product2 || $discount->Product3 || $discount->ProductGroup1 || $discount->ProductGroup2 || $discount->ProductGroup3)) {
                            echo "\t\t\t\t<p>\n\t\t\t\t\t";
                            echo __("discount product no restrictions");
                            echo "\t\t\t\t</p>\n\t\t\t\t";
                        } else {
                            echo "\t\t\t\t<table cellspacing=\"0\" cellpadding=\"0\" class=\"table1\">\n\t\t\t\t\t<tbody>\n\t\t\t\t\t\t<tr class=\"trtitle\">\n\t\t\t\t\t\t\t<th scope=\"col\">";
                            echo __("discount product restrictions table restriction");
                            echo "</th>\n\t\t\t\t\t\t\t<th scope=\"col\">";
                            echo __("discount product restrictions table value");
                            echo "</th>\n\t\t\t\t\t\t\t<th scope=\"col\" style=\"width:100px;\"><span class=\"";
                            if(!$discount->Price1 && !$discount->Price2 && !$discount->Price3) {
                                echo "hide";
                            }
                            echo "\">";
                            echo __("discount product restrictions table price");
                            echo "</span></th>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr class=\"hover_extra_info tr3\">\n\t\t\t\t\t\t\t<td><strong>";
                            echo __("discount product restrictions table restriction");
                            echo " 1</strong></td>\n\t\t\t\t\t\t\t<td>";
                            if(0 < $discount->Product1) {
                                echo __("discount product restrictions table product") . ": ";
                                echo "<a href=\"products.php?page=show&id=" . $discount->Product1 . "\" class=\"a1 c1\">" . $products[$discount->Product1]["ProductCode"] . " " . $products[$discount->Product1]["ProductName"] . "</a>";
                            } elseif(0 < $discount->ProductGroup1) {
                                echo __("discount product restrictions table productgroup") . ": ";
                                echo "<a href=\"products.php?page=show_group&id=" . $discount->ProductGroup1 . "\" class=\"a1 c1\">" . $productgroups[$discount->ProductGroup1]["GroupName"] . "</a>";
                            } else {
                                echo __("discount product restrictions table no restriction");
                            }
                            echo "</td>\n\t\t\t\t\t\t\t<td>";
                            echo $discount->Price1;
                            echo "</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr class=\"hover_extra_info tr1\">\n\t\t\t\t\t\t\t<td><strong>";
                            echo __("discount product restrictions table restriction");
                            echo " 2</strong></td>\n\t\t\t\t\t\t\t<td>";
                            if(0 < $discount->Product2) {
                                echo __("discount product restrictions table product") . ": ";
                                echo "<a href=\"products.php?page=show&id=" . $discount->Product2 . "\" class=\"a1 c1\">" . $products[$discount->Product2]["ProductCode"] . " " . $products[$discount->Product2]["ProductName"] . "</a>";
                            } elseif(0 < $discount->ProductGroup2) {
                                echo __("discount product restrictions table productgroup") . ": ";
                                echo "<a href=\"products.php?page=show_group&id=" . $discount->ProductGroup2 . "\" class=\"a1 c1\">" . $productgroups[$discount->ProductGroup2]["GroupName"] . "</a>";
                            } else {
                                echo __("discount product restrictions table no restriction");
                            }
                            echo "</td>\n\t\t\t\t\t\t\t<td>";
                            echo $discount->Price2;
                            echo "</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr class=\"hover_extra_info tr3\">\n\t\t\t\t\t\t\t<td><strong>";
                            echo __("discount product restrictions table restriction");
                            echo " 3</strong></td>\n\t\t\t\t\t\t\t<td>";
                            if(0 < $discount->Product3) {
                                echo __("discount product restrictions table product") . ": ";
                                echo "<a href=\"products.php?page=show&id=" . $discount->Product3 . "\" class=\"a1 c1\">" . $products[$discount->Product3]["ProductCode"] . " " . $products[$discount->Product3]["ProductName"] . "</a>";
                            } elseif(0 < $discount->ProductGroup3) {
                                echo __("discount product restrictions table productgroup") . ": ";
                                echo "<a href=\"products.php?page=show_group&id=" . $discount->ProductGroup3 . "\" class=\"a1 c1\">" . $productgroups[$discount->ProductGroup3]["GroupName"] . "</a>";
                            } else {
                                echo __("discount product restrictions table no restriction");
                            }
                            echo "</td>\n\t\t\t\t\t\t\t<td>";
                            echo $discount->Price3;
                            echo "</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t</tbody>\n\t\t\t\t</table>\n\t\t\t\t";
                        }
                        echo "\t\t\t<!--box3-->\n\t\t\t</div></div>\n\t\t\t<!--box3-->\n\t\t\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\n\t<!--box1-->\n\t</div>\n\t<!--box1-->\n\n\t<br />\n\t\t\t\n\t<!--buttonbar-->\n\t<div class=\"buttonbar\">\n\t<!--buttonbar-->\n\t\n\t\t";
                        if(U_PRODUCT_EDIT) {
                            echo "<p class=\"pos1\"><a class=\"button1 edit_icon\" href=\"discount.php?page=edit&id=";
                            echo $discount->Identifier;
                            echo "\"><span>";
                            echo __("edit discount");
                            echo "</span></a></p>";
                        }
                        echo "\t\t";
                        if(U_PRODUCT_DELETE) {
                            echo "<p class=\"pos2\"><a class=\"button1 delete_icon\" onclick=\"\$('#delete_discount').dialog('open');\"><span>";
                            echo __("delete discount");
                            echo "</span></a></p>";
                        }
                        echo "\n\t<!--buttonbar-->\n\t</div>\n\t<!--buttonbar-->\n\n";
                        if(U_PRODUCT_DELETE) {
                            echo "<div id=\"delete_discount\" class=\"hide\" title=\"";
                            echo __("deletedialog discount title");
                            echo "\">\n\t<form id=\"DiscountForm\" name=\"form_delete\" method=\"post\" action=\"discount.php?page=delete\">\n\t<input type=\"hidden\" name=\"id\" value=\"";
                            echo $discount->Identifier;
                            echo "\"/>\n\t";
                            echo sprintf(__("deletedialog discount message"), $discount->Name);
                            echo "<br />\n\t<br />\n\t<input type=\"checkbox\" name=\"agree_delete_discount\" id=\"imsure\" value=\"yes\"/> <label for=\"imsure\">";
                            echo __("deletedialog discount agree");
                            echo "</label><br />\n\t<br />\n\t<p><a id=\"delete_discount_btn\" class=\"button2 alt1 float_left\"><span>";
                            echo __("delete");
                            echo "</span></a></p>\n\t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#delete_discount').dialog('close');\"><span>";
                            echo __("cancel");
                            echo "</span></a></p>\n\t</form>\n</div>\n";
                        }
                        require_once "views/footer.php";
                }
        }
}

?>