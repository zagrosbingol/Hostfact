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
echo "\n<!--form-->\n<form id=\"DiscountForm\" name=\"form_create\" method=\"post\" action=\"discount.php?page=add\"><fieldset><legend>";
echo __("discount create");
echo "</legend>\n";
if(0 < $discount_id) {
    echo "<input type=\"hidden\" name=\"Identifier\" value=\"";
    echo $discount_id;
    echo "\" />\n";
}
echo "<!--form-->\n\n<!--heading1-->\n<div class=\"heading1\">\n<!--heading1-->\n\n\t<h2>";
echo $pagetype == "add" ? __("discount create") : __("discount edit");
echo "</h2>\n\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n\t<!--box1-->\n\t<div class=\"box2\" id=\"tabs\">\n\t<!--box1-->\n\t\n\t\t<!--top-->\n\t\t<div class=\"top\">\n\t\t<!--top-->\n\t\t\n\t\t\t<ul class=\"list3\">\n\t\t\t\t<li class=\"on\"><a href=\"#tab-general\">";
echo __("discounttab general");
echo "</a></li>\n\t\t\t\t<li><a href=\"#tab-extended\">";
echo __("discounttab extended");
echo "</a></li>\n\t\t\t\t<li><a href=\"#tab-restrictions\">";
echo __("discounttab restrictions");
echo "</a></li>\n\t\t\t</ul>\n\t\t\t\n\t\t\n\t\t<!--top-->\n\t\t</div>\n\t\t<!--top-->\n\n\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-general\">\n\t\t<!--content-->\n\t\t\n\t\t\t<!--split2-->\n\t\t\t<div class=\"split2\">\n\t\t\t<!--split2-->\n\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t<div class=\"left\">\n\t\t\t\t<!--left-->\n\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("discount general");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("discount name");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"Name\" value=\"";
echo $discount->Name;
echo "\" />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("discount type");
echo "</strong>\n\t\t\t\t\t\t<p>\n\t\t\t\t\t\t<input type=\"radio\" class=\"radio1\" id=\"discounttype_radio_totalamount\" value=\"TotalAmount\" name=\"DiscountType\" ";
if($discount->DiscountType == "TotalAmount") {
    echo "checked=\"checked\"";
}
echo "/> <label for=\"discounttype_radio_totalamount\">";
echo __("discount type fixed amount");
echo "</label><br />\n\t\t\t\t\t\t<input type=\"radio\" class=\"radio1\" id=\"discounttype_radio_totalpercentage\" value=\"TotalPercentage\" name=\"DiscountType\" ";
if($discount->DiscountType == "TotalPercentage") {
    echo "checked=\"checked\"";
}
echo "/> <label for=\"discounttype_radio_totalpercentage\">";
echo __("discount type fixed percentage");
echo "</label><br />\n\t\t\t\t\t\t<input type=\"radio\" class=\"radio1\" id=\"discounttype_radio_partialrestrictedpercentage\" value=\"PartialRestrictedPercentage\" name=\"DiscountType\" ";
if($discount->DiscountType == "PartialRestrictedPercentage") {
    echo "checked=\"checked\"";
}
echo "/> <label for=\"discounttype_radio_partialrestrictedpercentage\">";
echo __("discount type partial restricted percentage");
echo "</label><br />\n\t\t\t\t\t\t<input type=\"radio\" class=\"radio1\" id=\"discounttype_radio_partialpercentage\" value=\"PartialPercentage\" name=\"DiscountType\" ";
if($discount->DiscountType == "PartialPercentage") {
    echo "checked=\"checked\"";
}
echo "/> <label for=\"discounttype_radio_partialpercentage\">";
echo __("discount type partial percentage");
echo "</label><br />\n\t\t\t\t\t\t<input type=\"radio\" class=\"radio1\" id=\"discounttype_radio_partialamount\" value=\"PartialAmount\" name=\"DiscountType\" ";
if($discount->DiscountType == "PartialAmount") {
    echo "checked=\"checked\"";
}
echo "/> <label for=\"discounttype_radio_partialamount\">";
echo __("discount type product price");
echo "</label>\n\t\t\t\t\t\t</p>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<div id=\"type_amount\" ";
if($discount->DiscountType != "TotalAmount") {
    echo "class=\"hide\"";
}
echo ">\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("discount type amount");
echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"Discount\" value=\"";
echo money($discount->Discount, false);
echo "\" />\n\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("discount type amount description");
echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"Description\" value=\"";
echo $discount->Description;
echo "\" />\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<div id=\"type_percentage\" ";
if($discount->DiscountType != "TotalPercentage") {
    echo "class=\"hide\"";
}
echo ">\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("discount type percentage");
echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"DiscountPercentage\" value=\"";
echo isset($discount->DiscountPercentage) ? showNumber(round((double) $discount->DiscountPercentage, 2)) : "";
echo "\" /> %\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<div id=\"type_partialrestrictedpercentage\" ";
if($discount->DiscountType != "PartialRestrictedPercentage") {
    echo "class=\"hide\"";
}
echo ">\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("discount type restricted discount part");
echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"PartialRestrictedDiscountPercentage\" value=\"";
echo isset($discount->PartialRestrictedDiscountPercentage) ? showNumber(round((double) $discount->PartialRestrictedDiscountPercentage, 2)) : "";
echo "\" /> %\n\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("discountpercentage type");
echo "</strong>\n\t\t\t\t\t\t\t<select class=\"text1 size4f\" name=\"PartialRestrictedDiscountPercentageType\">\n\t\t\t\t\t\t\t\t<option value=\"line\" ";
if($discount->DiscountPercentageType == "line") {
    echo "selected=\"selected\"";
}
echo "> ";
echo __("discount on invoiceline");
echo "</option>\n\t\t\t\t\t\t\t\t<option value=\"subscription\" ";
if($discount->DiscountPercentageType == "subscription") {
    echo "selected=\"selected\"";
}
echo "> ";
echo __("discount on invoiceline and subscription");
echo "</option>\n\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<div id=\"type_partialpercentage\" ";
if($discount->DiscountType != "PartialPercentage") {
    echo "class=\"hide\"";
}
echo ">\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("discount product restrictions table restriction");
echo "</strong>\n\t\t\t\t\t\t\t<select class=\"text1 size4f\" name=\"PartialDiscountPercentageRestriction\">\n\t\t\t\t\t\t\t\t<option value=\"1\" ";
if($discount->DiscountPartRestriction == 1) {
    echo "selected=\"selected\"";
}
echo ">";
echo __("discount product restrictions table restriction");
echo " 1</option>\n\t\t\t\t\t\t\t\t<option value=\"2\" ";
if($discount->DiscountPartRestriction == 2) {
    echo "selected=\"selected\"";
}
echo ">";
echo __("discount product restrictions table restriction");
echo " 2</option>\n\t\t\t\t\t\t\t\t<option value=\"3\" ";
if($discount->DiscountPartRestriction == 3) {
    echo "selected=\"selected\"";
}
echo ">";
echo __("discount product restrictions table restriction");
echo " 3</option>\n\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("discount type restricted discount part");
echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"PartialDiscountPercentage\" value=\"";
echo isset($discount->PartialDiscountPercentage) ? showNumber(round((double) $discount->PartialDiscountPercentage, 2)) : "";
echo "\" /> %\n\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("discountpercentage type");
echo "</strong>\n\t\t\t\t\t\t\t<select class=\"text1 size4f\" name=\"PartialDiscountPercentageType\">\n\t\t\t\t\t\t\t\t<option value=\"line\" ";
if($discount->DiscountPercentageType == "line") {
    echo "selected=\"selected\"";
}
echo "> ";
echo __("discount on invoiceline");
echo "</option>\n\t\t\t\t\t\t\t\t<option value=\"subscription\" ";
if($discount->DiscountPercentageType == "subscription") {
    echo "selected=\"selected\"";
}
echo "> ";
echo __("discount on invoiceline and subscription");
echo "</option>\n\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<div id=\"type_product\" ";
if($discount->DiscountType != "PartialAmount") {
    echo "class=\"hide\"";
}
echo ">\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("discount product restrictions table restriction");
echo "</strong>\n\t\t\t\t\t\t\t<select class=\"text1 size4f\" name=\"PartialDiscountRestriction\">\n\t\t\t\t\t\t\t\t<option value=\"1\" ";
if($discount->DiscountPartRestriction == 1) {
    echo "selected=\"selected\"";
}
echo ">";
echo __("discount product restrictions table restriction");
echo " 1</option>\n\t\t\t\t\t\t\t\t<option value=\"2\" ";
if($discount->DiscountPartRestriction == 2) {
    echo "selected=\"selected\"";
}
echo ">";
echo __("discount product restrictions table restriction");
echo " 2</option>\n\t\t\t\t\t\t\t\t<option value=\"3\" ";
if($discount->DiscountPartRestriction == 3) {
    echo "selected=\"selected\"";
}
echo ">";
echo __("discount product restrictions table restriction");
echo " 3</option>\n\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("discount amount");
echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"PartialDiscount\" value=\"";
echo isset($discount->PartialDiscount) ? money($discount->PartialDiscount, false) : "";
echo "\" />\n\t\t\t\t\t\t</div>\n\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t</div>\n\t\t\t\t<!--left-->\n\t\t\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t<div class=\"right\">\n\t\t\t\t<!--right-->\n\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("discount for");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("discount for debtor(group)");
echo "</strong>\n\t\t\t\t\t\t<select class=\"text1 size4f\" name=\"DebtorRestriction\">\n\t\t\t                <option value=\"none\" ";
if($discount->DebtorRestriction == "none") {
    echo "selected=\"selected\"";
}
echo ">";
echo __("discount for all debtors");
echo "</option>\n\t\t\t                <option value=\"group\" ";
if($discount->DebtorRestriction == "group") {
    echo "selected=\"selected\"";
}
echo ">";
echo __("discount for debtorgroup(s)");
echo "</option>\n\t\t\t                <option value=\"debtor\" ";
if($discount->DebtorRestriction == "debtor") {
    echo "selected=\"selected\"";
}
echo ">";
echo __("discount for debtor");
echo "</option>\n\t\t\t                <option value=\"-1\" ";
if($discount->DebtorRestriction == "-1") {
    echo "selected=\"selected\"";
}
echo ">";
echo __("discount for new debtors");
echo "</option>\n\t\t\t     \t\t\t<option value=\"-2\" ";
if($discount->DebtorRestriction == "-2") {
    echo "selected=\"selected\"";
}
echo ">";
echo __("discount for existing debtors");
echo "</option>\n\t\t\t        \t\t<option value=\"-3\" ";
if($discount->DebtorRestriction == "-3") {
    echo "selected=\"selected\"";
}
echo ">";
echo __("discount for auth debtors");
echo "</option>\n\t\t                </select>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<div id=\"debtor_group\" ";
if($discount->DebtorRestriction != "group") {
    echo "class=\"hide\"";
}
echo ">\n\t\t\t\t\t\t<p>\n\t\t\t\t\t\t\t<strong>";
echo __("discount for select debtorgroups");
echo ":</strong>\n\t\t\t\t\t\t</p>\n\t\t\t\t\t\t<div class=\"height1 overflow-y\">\n\t\t\t\t\t\t\t<ul class=\"emaillist\">\n\t\t\t\t\t\t\t\t";
foreach ($debtorgroups as $groupID => $debtorGroup) {
    if(is_numeric($groupID)) {
        echo "\t\t\t\t\t\t\t\t<li><input type=\"checkbox\" id=\"debtorgroup_";
        echo $groupID;
        echo "\" class=\"checkbox1 mar5\" value=\"";
        echo $groupID;
        echo "\" name=\"DebtorGroup[]\" ";
        if(is_array($discount->DebtorGroup) && in_array($groupID, $discount->DebtorGroup)) {
            echo "checked=\"checked\"";
        }
        echo "/> <label for=\"debtorgroup_";
        echo $groupID;
        echo "\">";
        echo $debtorGroup["GroupName"];
        echo "</label></li>\n\t\t\t\t\t\t\t\t";
    }
}
echo "\t\t\t\t\t\t\t</ul>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<div id=\"debtor_debtor\" ";
if($discount->DebtorRestriction != "debtor") {
    echo "class=\"hide\"";
}
echo ">\n\t\t\t\t\t\t<p>\n\t\t\t\t\t\t\t<strong>";
echo __("discount for select debtor");
echo ":</strong>\n\t\t\t\t\t\t</p>\n\t\t\t\t\t\t<input type=\"hidden\" name=\"Debtor\" value=\"";
echo $discount->Debtor;
echo "\" />\n\t\t\t\t\t\t";
$selected_name = 0 < $discount->Debtor ? $debtors[$discount->Debtor]["DebtorCode"] . " " . ($debtors[$discount->Debtor]["CompanyName"] ? $debtors[$discount->Debtor]["CompanyName"] : $debtors[$discount->Debtor]["SurName"] . ", " . $debtors[$discount->Debtor]["Initials"]) : "";
createAutoComplete("debtor", "Debtor", $selected_name, ["class" => "size12"]);
echo "\t\t\t\t\t\t</div>\n\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t</div>\n\t\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t<!--split2-->\n\t\t\t</div>\n\t\t\t<!--split2-->\n\t\t\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\t\t\n\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-extended\">\n\t\t<!--content-->\n\t\t\n\t\t\t<!--split2-->\n\t\t\t<div class=\"split2\">\n\t\t\t<!--split2-->\n\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t<div class=\"left\">\n\t\t\t\t<!--left-->\n\t\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("discount valid");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("discount valid");
echo "</strong>\n\t\t\t\t\t\t<select name=\"Period\" class=\"text1 size4\">\n\t\t\t\t\t\t\t<option value=\"always\" ";
if(!$discount->StartDate && !$discount->EndDate) {
    echo "selected=\"selected\"";
}
echo ">";
echo __("discount valid always");
echo "</option>\n\t\t\t\t\t\t\t<option value=\"till\" ";
if(!$discount->StartDate && $discount->EndDate) {
    echo "selected=\"selected\"";
}
echo ">";
echo __("discount valid till");
echo "</option>\n\t\t\t\t\t\t\t<option value=\"between\" ";
if($discount->StartDate && $discount->EndDate) {
    echo "selected=\"selected\"";
}
echo ">";
echo __("discount valid between");
echo "</option>\n\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t<div id=\"period_startdate\" ";
if(!$discount->StartDate) {
    echo "class=\"hide\"";
}
echo ">\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("discount startdate");
echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1 datepicker\" name=\"StartDate\" value=\"";
echo $discount->StartDate;
echo "\" />\t\t\n\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<div id=\"period_enddate\" ";
if(!$discount->EndDate) {
    echo "class=\"hide\"";
}
echo ">\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("discount enddate");
echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1 datepicker\" name=\"EndDate\" value=\"";
echo $discount->EndDate;
echo "\" />\t\t\t\n\t\t\t\t\t\t</div>\n\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t</div>\n\t\t\t\t<!--left-->\n\t\t\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t<div class=\"right\">\n\t\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("discount restrictions");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("discount restriction coupon");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"Coupon\" value=\"";
echo $discount->Coupon;
echo "\" />\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("discount restrictions max");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"Max\" value=\"";
echo $discount->Max;
echo "\" />\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<div id=\"MaxPerInvoiceHolder\"";
echo in_array($discount->DiscountType, ["TotalPercentage", "TotalAmount", "PartialAmount"]) ? " class=\"hide\"" : "";
echo ">\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("discount restrictions max per invoice");
echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"MaxPerInvoice\" value=\"";
echo $discount->MaxPerInvoice;
echo "\" />\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t</div>\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("discount restrictions amount");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"MinAmount\" value=\"";
echo $discount->MinAmount;
echo "\" />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("discount document type");
echo "</strong>\n\t\t\t\t\t\t<select class=\"text1 size4f\" name=\"DocumentType\">\n\t\t\t                <option value=\"\" ";
if($discount->DocumentType == "") {
    echo "selected=\"selected\"";
}
echo ">";
echo __("discount document type all");
echo "</option>\n\t\t\t                <option value=\"order\" ";
if($discount->DocumentType == "order") {
    echo "selected=\"selected\"";
}
echo ">";
echo __("discount document type order");
echo "</option>\n\t\t\t            </select>\n\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t</div>\n\t\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t<!--split2-->\n\t\t\t</div>\n\t\t\t<!--split2-->\n\t\t\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\t\t\n\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-restrictions\">\n\t\t<!--content-->\n\t\t\t\t\t\t\n\t\t\t<!--box3-->\n\t\t\t<div class=\"box3\"><h3>";
echo __("discount product restrictions");
echo "</h3><div class=\"content\">\n\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t<strong class=\"title\">";
echo __("discount product restrictions activated");
echo "</strong>\n\t\t\t\t<select class=\"text1 size4\" name=\"product_restriction\">\n\t\t               <option value=\"no\">";
echo __("no");
echo "</option>\n\t\t               <option value=\"yes\" ";
if($discount->Product1 || $discount->Product2 || $discount->Product3 || $discount->ProductGroup1 || $discount->ProductGroup2 || $discount->ProductGroup3) {
    echo "selected=\"selected\"";
}
echo ">";
echo __("yes");
echo "</option>\n    \t\t\t</select>\n\n\t\t\t\t<br />\n\n\t\t\t\t<table id=\"ProductRestrictionTable\" cellspacing=\"0\" cellpadding=\"0\" class=\"table1 ";
if(!($discount->Product1 || $discount->Product2 || $discount->Product3 || $discount->ProductGroup1 || $discount->ProductGroup2 || $discount->ProductGroup3)) {
    echo "hide";
}
echo "\">\n\t\t\t\t\t<tbody>\n\t\t\t\t\t\t<tr class=\"trtitle\">\n\t\t\t\t\t\t\t<th scope=\"col\" style=\"width: 150px;\">";
echo __("discount product restrictions table restriction");
echo "</th>\n\t\t\t\t\t\t\t<th scope=\"col\" style=\"width: 200px;\">";
echo __("discount product restrictions table type");
echo "</th>\n\t\t\t\t\t\t\t<th scope=\"col\">";
echo __("discount product restrictions table value");
echo "</th>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr class=\"hover_extra_info tr3\">\n\t\t\t\t\t\t\t<td><strong>";
echo __("discount product restrictions table restriction");
echo " 1</strong></td>\n\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t<select class=\"text1 size10 mar6 productrestriction\" name=\"Restriction1Type\">\n\t\t\t\t                   \t<option value=\"none\">";
echo __("discount product restrictions table no restriction");
echo "</option>\n\t\t\t\t               \t\t<option value=\"product\" ";
if($discount->Product1) {
    echo "selected=\"selected\"";
}
echo ">";
echo __("discount product restrictions table product");
echo "</option>\n\t\t\t\t                   \t<option value=\"group\" ";
if($discount->ProductGroup1) {
    echo "selected=\"selected\"";
}
echo ">";
echo __("discount product restrictions table productgroup");
echo "</option>\n\t\t\t\t                </select>\n\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t<div class=\"restriction_product";
if(!$discount->Product1) {
    echo " hide";
}
echo "\">\n\t\t\t\t\t\t\t\t\t<input type=\"hidden\" name=\"Restriction1Product\" value=\"";
echo $discount->Product1;
echo "\" />\n\t\t\t\t\t\t\t\t\t";
$selected_name = 0 < $discount->Product1 ? $products[$discount->Product1]["ProductCode"] . " " . $products[$discount->Product1]["ProductName"] : "";
createAutoComplete("product", "Restriction1Product", $selected_name, ["class" => "size12"]);
echo "\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<select class=\"text1 size10 mar6 ";
if(!$discount->ProductGroup1) {
    echo "hide";
}
echo "\" name=\"Restriction1Group\">\n\t\t\t\t\t\t\t\t\t<option value=\"\">";
echo __("please choose");
echo "</option>\n\t\t\t\t\t\t\t\t\t";
foreach ($productgroups as $groupID => $value) {
    if(is_numeric($groupID)) {
        echo "\t\t\t\t\t\t\t\t\t<option value=\"";
        echo $groupID;
        echo "\" ";
        if($groupID == $discount->ProductGroup1) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo $value["GroupName"];
        echo "</option>\n\t\t\t\t\t\t\t\t\t";
    }
}
echo "\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr class=\"hover_extra_info tr1\">\n\t\t\t\t\t\t\t<td><strong>";
echo __("discount product restrictions table restriction");
echo " 2</strong></td>\n\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t<select class=\"text1 size10 mar6 productrestriction\" name=\"Restriction2Type\">\n\t\t\t\t                   \t<option value=\"none\">";
echo __("discount product restrictions table no restriction");
echo "</option>\n\t\t\t\t               \t\t<option value=\"product\" ";
if($discount->Product2) {
    echo "selected=\"selected\"";
}
echo ">";
echo __("discount product restrictions table product");
echo "</option>\n\t\t\t\t                   \t<option value=\"group\" ";
if($discount->ProductGroup2) {
    echo "selected=\"selected\"";
}
echo ">";
echo __("discount product restrictions table productgroup");
echo "</option>\n\t\t\t\t                </select>\n\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t<div class=\"restriction_product";
if(!$discount->Product2) {
    echo " hide";
}
echo "\">\n\t\t\t\t\t\t\t\t\t<input type=\"hidden\" name=\"Restriction2Product\" value=\"";
echo $discount->Product2;
echo "\" />\n\t\t\t\t\t\t\t\t\t";
$selected_name = 0 < $discount->Product2 ? $products[$discount->Product2]["ProductCode"] . " " . $products[$discount->Product2]["ProductName"] : "";
createAutoComplete("product", "Restriction2Product", $selected_name, ["class" => "size12"]);
echo "\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<select class=\"text1 size10 mar6 ";
if(!$discount->ProductGroup2) {
    echo "hide";
}
echo "\" name=\"Restriction2Group\">\n\t\t\t\t\t\t\t\t\t<option value=\"\">";
echo __("please choose");
echo "</option>\n\t\t\t\t\t\t\t\t\t";
foreach ($productgroups as $groupID => $value) {
    if(is_numeric($groupID)) {
        echo "\t\t\t\t\t\t\t\t\t<option value=\"";
        echo $groupID;
        echo "\" ";
        if($groupID == $discount->ProductGroup2) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo $value["GroupName"];
        echo "</option>\n\t\t\t\t\t\t\t\t\t";
    }
}
echo "\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr class=\"hover_extra_info tr3\">\n\t\t\t\t\t\t\t<td><strong>";
echo __("discount product restrictions table restriction");
echo " 3</strong></td>\n\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t<select class=\"text1 size10 mar6 productrestriction\" name=\"Restriction3Type\">\n\t\t\t\t                   \t<option value=\"none\">";
echo __("discount product restrictions table no restriction");
echo "</option>\n\t\t\t\t               \t\t<option value=\"product\" ";
if($discount->Product3) {
    echo "selected=\"selected\"";
}
echo ">";
echo __("discount product restrictions table product");
echo "</option>\n\t\t\t\t                   \t<option value=\"group\" ";
if($discount->ProductGroup3) {
    echo "selected=\"selected\"";
}
echo ">";
echo __("discount product restrictions table productgroup");
echo "</option>\n\t\t\t\t                </select>\n\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t<div class=\"restriction_product";
if(!$discount->Product3) {
    echo " hide";
}
echo "\">\n\t\t\t\t\t\t\t\t\t<input type=\"hidden\" name=\"Restriction3Product\" value=\"";
echo $discount->Product3;
echo "\" />\n\t\t\t\t\t\t\t\t\t";
$selected_name = 0 < $discount->Product3 ? $products[$discount->Product3]["ProductCode"] . " " . $products[$discount->Product3]["ProductName"] : "";
createAutoComplete("product", "Restriction3Product", $selected_name, ["class" => "size12"]);
echo "\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<select class=\"text1 size10 mar6 ";
if(!$discount->ProductGroup3) {
    echo "hide";
}
echo "\" name=\"Restriction3Group\">\n\t\t\t\t\t\t\t\t\t<option value=\"\">";
echo __("please choose");
echo "</option>\n\t\t\t\t\t\t\t\t\t";
foreach ($productgroups as $groupID => $value) {
    if(is_numeric($groupID)) {
        echo "\t\t\t\t\t\t\t\t\t<option value=\"";
        echo $groupID;
        echo "\" ";
        if($groupID == $discount->ProductGroup3) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo $value["GroupName"];
        echo "</option>\n\t\t\t\t\t\t\t\t\t";
    }
}
echo "\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t</tbody>\n\t\t\t\t</table>\n\n\t\t\t<!--box3-->\n\t\t\t</div></div>\n\t\t\t<!--box3-->\n\t\t\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\n\t<!--box1-->\n\t</div>\n\t<!--box1-->\n\n\t<br />\n\t\t\t\t\t\n\t<p class=\"align_right\">\n        <a class=\"button1 alt1\" id=\"form_create_btn\">\n            <span>";
echo $pagetype == "add" ? __("btn add") : __("btn edit");
echo "</span>\n        </a>\n    </p>\n\t\n<!--form-->\n</fieldset></form>\n<!--form-->\n\n";
require_once "views/footer.php";

?>