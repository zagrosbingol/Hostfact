<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
function show_subscription_tab($subscription_tab, $options = [])
{
    $product_id = isset($options["product_id"]) ? $options["product_id"] : 0;
    $show_debtor = isset($options["show_debtor"]) ? $options["show_debtor"] : false;
    $debtor_info = isset($options["debtor_info"]) ? $options["debtor_info"] : false;
    $show_number = isset($options["show_number"]) ? $options["show_number"] : false;
    global $array_periodes;
    global $array_periodesMV;
    global $array_country;
    global $array_taxpercentages;
    echo "\n\t<!--split2-->\n\t<div class=\"split2\">\n\t<!--split2-->\n\t\n\t\t<!--left-->\n\t\t<div class=\"left\">\n\t\t<!--left-->\n\t\n\t\t\t<!--box3-->\n\t\t\t<div class=\"box3\"><h3>";
    echo __("financial");
    echo "</h3><div class=\"content lineheight2\">\n\t\t\t<!--box3-->\n\t\t\t\t";
    if($show_debtor) {
        echo "\t\t\t\t\t<strong class=\"title2\">Debiteur</strong>\n\t\t\t\t\t<span class=\"title2_value\" style=\"clear:none; float: left;\">\n\t\t\t\t\t\t<a href=\"debtors.php?page=show&amp;id=";
        echo $debtor_info->Debtor;
        echo "\" class=\"c1 a1\">\n\t\t\t\t\t\t\t";
        echo $debtor_info->CompanyName ? $debtor_info->CompanyName : $debtor_info->Initials . " " . $debtor_info->SurName;
        echo "\t\t\t\t\t\t</a>\n\t\t\t\t\t</span>\n\t\t\t\t\t<br clear=\"both\" /> <br />\n\t\t\t\t";
    }
    echo "\t\t\t\t\n\t\t\t\t\n\t\t\t\t<strong class=\"title2\">";
    echo __("amountexcl");
    echo "</strong>\n\t\t\t\t<span class=\"title2_value\">";
    echo $subscription_tab->AmountExcl;
    echo "</span>\n\t\t\t\t\n\t\t\t\t";
    if(!empty($array_taxpercentages)) {
        echo "\t\t\t\t<strong class=\"title2\">";
        echo __("amountincl");
        echo "</strong>\n\t\t\t\t<span class=\"title2_value\">";
        echo $subscription_tab->AmountIncl;
        echo "</span>\n\t\t\t\t";
    }
    echo "\t\n\t\t\t<!--box3-->\n\t\t\t</div></div>\n\t\t\t<!--box3-->\n\t\t\t\n\t\t\t<!--box3-->\n\t\t\t<div class=\"box3\"><h3>";
    echo __("contract data");
    echo "</h3><div class=\"content lineheight2\">\n\t\t\t<!--box3-->\n\t\t\t\t";
    if(0 < $subscription_tab->ContractPeriods && ($subscription_tab->ContractPeriods != $subscription_tab->Periods || $subscription_tab->ContractPeriodic != $subscription_tab->Periodic)) {
        echo "\t\t\t\t<strong class=\"title2\">";
        echo __("contract period");
        echo "</strong>\n\t\t\t\t<span class=\"title2_value\">";
        echo $subscription_tab->ContractPeriods;
        echo " ";
        echo $subscription_tab->ContractPeriods != 1 ? $array_periodesMV[$subscription_tab->ContractPeriodic] : $array_periodes[$subscription_tab->ContractPeriodic];
        echo "</span>\n\t\t\t\t";
    }
    echo "\t\t\t\t\n\t\t\t\t";
    if($subscription_tab->StartContract) {
        echo "\t\t\t\t<strong class=\"title2\">";
        echo __("start contract");
        echo "</strong>\n\t\t\t\t<span class=\"title2_value\">";
        echo $subscription_tab->StartContract;
        echo "</span>\n\t\t\t\t";
    }
    echo "\t\t\t\t\n\t\t\t\t";
    if($subscription_tab->ContractRenewalDate || rewrite_date_site2db($subscription_tab->EndContract) <= date("Ymdhis") && isset($subscription_tab->ContractInfo["Date"])) {
        echo "\t\t\t\t<strong class=\"title2\">";
        echo __("renewaldate contract");
        echo "</strong>\n\t\t\t\t\t";
        if($subscription_tab->ContractRenewalDate) {
            echo "\t\t\t\t\t\t<span class=\"title2_value\">";
            echo $subscription_tab->ContractRenewalDate;
            echo "\t\t\t\t\t";
        } else {
            echo "\t\t\t\t\t\t<span class=\"title2_value\">";
            echo rewrite_date_db2site($subscription_tab->ContractInfo["Date"]) . " " . __("at") . " " . rewrite_date_db2site($subscription_tab->ContractInfo["Date"], "%H:%i");
            echo "\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\n\t\t\t\t\t";
        if(isset($subscription_tab->ContractInfo["Date"])) {
            echo "\t\t\t\t\t\t<span class=\"infopopupright\"><em>meer info</em><span class=\"popup\">door ";
            echo $subscription_tab->ContractInfo["Name"];
            echo " ";
            if($subscription_tab->ContractInfo["Role"]) {
                echo "(";
                echo $subscription_tab->ContractInfo["Role"];
                echo ") ";
            }
            echo "met ";
            echo $subscription_tab->ContractInfo["Periods"];
            echo " ";
            echo $array_periodes[$subscription_tab->ContractInfo["Periodic"]];
            echo " (";
            echo rewrite_date_db2site($subscription_tab->ContractInfo["StartContract"]);
            echo " tot ";
            echo rewrite_date_db2site($subscription_tab->ContractInfo["EndContract"]);
            echo ")<b></b></span></span>\n\t\t\t\t\t";
        }
        echo "\t\t\t\t\t</span>\n\t\t\t\t";
    }
    echo "\t\t\t\t\n\t\t\t\t<strong class=\"title2\">";
    echo __("end contract");
    echo "</strong>\n\t\t\t\t<span class=\"title2_value\">";
    echo $subscription_tab->EndContract;
    echo "</span>\n\t\t\n\t\t\t<!--box3-->\n\t\t\t</div></div>\n\t\t\t<!--box3-->\n\n\t\t<!--left-->\n\t\t</div>\n\t\t<!--left-->\n\t\t\n\t\t<!--right-->\n\t\t<div class=\"right\">\n\t\t<!--right-->\n\t\t\t\n\t\t\t<!--box3-->\n\t\t\t<div class=\"box3\"><h3>";
    echo __("period to invoice");
    echo "</h3><div class=\"content lineheight2\">\n\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t<strong class=\"title2\">";
    echo __("periodic");
    echo "</strong>\n\t\t\t\t<span class=\"title2_value\">";
    echo $subscription_tab->Periods;
    echo " ";
    echo 1 < $subscription_tab->Periods ? $array_periodesMV[$subscription_tab->Periodic] : $array_periodes[$subscription_tab->Periodic];
    echo "</span>\n\t\t\t\t\n\t\t\t\t<strong class=\"title2\">";
    echo __("period to invoice");
    echo "</strong>\n\t\t\t\t<span class=\"title2_value\">";
    echo $subscription_tab->StartPeriod;
    echo " ";
    echo __("till");
    echo " ";
    echo $subscription_tab->EndPeriod;
    echo "</span>\n\t\t\t\t\n\t\t\t\t";
    if($subscription_tab->TerminationDate && rewrite_date_site2db($subscription_tab->TerminationDate) <= rewrite_date_site2db($subscription_tab->StartPeriod)) {
    } else {
        echo "\t\t\t\t\t<strong class=\"title2\">";
        echo __("invoice at date");
        echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
        echo $subscription_tab->NextDate;
        echo "</span>\n\t\t\t\t\t";
    }
    echo "\t\t\t\t\n\t\n\t\t\t<!--box3-->\n\t\t\t</div></div>\n\t\t\t<!--box3-->\n\t\t\t\n\t\t\t";
    if($subscription_tab->TerminationDate) {
        echo "\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
        echo __("end subscription");
        echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t<!--box3-->\n\t\t\t\n\t\t\t\t\t<strong class=\"title2\">";
        echo __("do not invoice after");
        echo "</strong>\n\t\t\t\t\t<span class=\"title2_value\">";
        echo $subscription_tab->TerminationDate;
        echo "</span>\n\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t";
    }
    echo "\t\t\t\n\t\n\t\t<!--right-->\n\t\t</div>\n\t\t<!--right-->\n\t\t\n\t<!--split2-->\n\t</div>\n\t<!--split2-->\n\t\t\n\t<!--box3-->\n\t<div class=\"box3\"><h3>";
    echo __("financial");
    echo "</h3><div class=\"content\">\n\t<!--box3-->\n\t\t\n\t\t<table border=\"0\" width=\"100%\">\n\t\t\t<tr>\n\t\t\t\t";
    if($show_number) {
        echo "<td style=\"width: 55px;\"><strong>";
        echo __("number");
        echo "</strong></td>";
    }
    echo "\t\t\t\t<td style=\"width: 220px;\"><strong>";
    echo __("product");
    echo "</strong></td>\n\t\t\t\t<td style=\"\"><strong>";
    echo __("description");
    echo "</strong></td>\n\t\t\t\t<td style=\"width: 120px;\"><strong>";
    echo __("price per unit");
    if(!empty($array_taxpercentages)) {
        echo " " . (VAT_CALC_METHOD == "incl" ? __("incl vat") : __("excl vat"));
    }
    echo "</strong></td>\n\t\t\t\t";
    if(!empty($array_taxpercentages)) {
        echo "<td style=\"width: 70px;\"><strong>";
        echo __("vat");
        echo "</strong></td>";
    }
    echo "\t\t\t</tr>\n\t\t\t<tr>\n\t\t\t\t";
    if($show_number) {
        echo "<td style=\"vertical-align: top;\">";
        echo showNumber($subscription_tab->Number) . $subscription_tab->NumberSuffix;
        echo "</td>";
    }
    echo "\t\t\t\t<td style=\"vertical-align: top;\">";
    if(0 < $product_id) {
        echo "<a href=\"products.php?page=show&amp;id=";
        echo $product_id;
        echo "\" class=\"c1 a1\">";
        echo isset($subscription_tab->ProductName) ? $subscription_tab->ProductName : "";
        echo "</a>";
    } else {
        echo "-";
    }
    echo "</td>\n\t\t\t\t<td style=\"vertical-align: top;\">";
    echo nl2br($subscription_tab->Description);
    echo "</td>\n\t\t\t\t<td style=\"vertical-align: top;\">";
    echo !empty($array_taxpercentages) && VAT_CALC_METHOD == "incl" ? $subscription_tab->PriceIncl : $subscription_tab->PriceExcl;
    echo " ";
    echo __("per") . " " . $array_periodes[$subscription_tab->Periodic];
    echo "</td>\n\t\t\t\t";
    if(!empty($array_taxpercentages)) {
        echo "<td style=\"vertical-align: top;\">";
        echo $subscription_tab->TaxPercentage * 100;
        echo "%</td>";
    }
    echo "\t\t\t</tr>\n\t\t\t";
    if(0 < $subscription_tab->FullDiscountPercentage) {
        echo "\t\t\t<tr>\n\t\t\t\t<td colspan=\"";
        echo $show_number ? "2" : "1";
        echo "\" style=\"vertical-align: top;\">&nbsp;</td>\n\t\t\t\t<td style=\"vertical-align: top;\"><br /><i>";
        echo $subscription_tab->FullDiscountPercentage;
        echo "% ";
        echo __("discount on subscription line");
        echo "</i></td>\n\t\t\t\t<td colspan=\"2\" style=\"vertical-align: top;\">&nbsp;</td>\n\t\t\t</tr>\n\t\t\t";
    }
    echo "\t\t\t\n\t\t</table>\n\t\n\t<!--box3-->\n\t</div></div>\n\t<!--box3-->\n\t";
}

?>