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
echo $message;
echo "\n";
$page_form_title = 0 < $paymentmethod->Identifier ? sprintf(__("edit paymentmethod"), $paymentmethod->Title) : __("add paymentmethod");
$btn_form_title = 0 < $paymentmethod->Identifier ? __("btn edit") : __("btn add");
echo "\n<!--form-->\n<form id=\"PaymentMethodForm\" name=\"form_create\" method=\"post\" action=\"paymentmethods.php\"><fieldset><legend>";
echo $page_form_title;
echo "</legend>\n\t\n";
if(0 < $paymentmethod->Identifier) {
    echo "\t<input type=\"hidden\" name=\"id\" value=\"";
    echo $paymentmethod->Identifier;
    echo "\" />\n";
}
echo "<!--form-->\n\n<!--heading1-->\n<div class=\"heading1\">\n<!--heading1-->\n\t<h2>";
echo $page_form_title;
echo "</h2>\n\t";
if(0 < $paymentmethod->Identifier) {
    echo "<p class=\"pos2\"><strong class=\"textsize1\" style=\"line-height: 22px\">";
    echo $array_paymentmethod[$paymentmethod->PaymentType];
    echo " ";
    if($paymentmethod->PaymentType == "ideal") {
        echo "(" . $paymentmethod->Directory . ")";
    }
    echo "</strong></p>";
}
echo "\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n\t<!--box1-->\n\t<div class=\"box2\" id=\"tabs\">\n\t<!--box1-->\n\t\n\t\t<!--top-->\n\t\t<div class=\"top\">\n\t\t<!--top-->\n\t\t\n\t\t\t<ul class=\"list3\">\n\t\t\t\t<li class=\"on\"><a href=\"#tab-general\">";
echo __("general");
echo "</a></li>\n\t\t\t\t<li><a href=\"#tab-transactioncost\">";
echo __("transaction costs");
echo "</a></li>\n\t\t\t</ul>\n\t\t\n\t\t<!--top-->\n\t\t</div>\n\t\t<!--top-->\n\n\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-general\">\n\t\t<!--content-->\n\n\t\t\t<!--split2-->\n\t\t\t<div class=\"split2\">\n\t\t\t<!--split2-->\n\t\t\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t<div class=\"left\">\n\t\t\t\t<!--left-->\t\n\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3";
if(0 < $paymentmethod->Identifier) {
    echo " hide";
}
echo "\"><h3>";
echo __("general");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("paymentmethod type");
echo "</strong>\n\n\n\t\t\t\t\t\t\t<select name=\"PaymentType\" class=\"text1 size4f\" ";
if(0 < $paymentmethod->Identifier) {
    echo "disabled=\"disabled\"";
}
echo ">\n\t\t\t\t\t\t\t\t<option value=\"\">";
echo __("make your choice");
echo "</option>\n\t\t\t\t\t\t\t\t";
$first_option = true;
foreach ($array_paymentmethod as $k => $v) {
    if(!in_array($k, ["ideal", "paypal", "other"]) && (!in_array($k, $payment_types) || 0 < $paymentmethod->Identifier)) {
        if($first_option) {
            echo "<optgroup label=\"";
            echo __("paymentmethod type");
            echo "\">";
            $first_option = false;
        }
        echo "<option value=\"";
        echo $k;
        echo "\" ";
        if($paymentmethod->PaymentType == $k) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo $v;
        echo "</option>";
    }
}
if($first_option === false) {
    echo "</optgroup>";
}
echo "\t\t\t\t\t\t\t\t<optgroup label=\"";
echo __("paymentmethod other");
echo "\"></optgroup>\n\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t<input type=\"hidden\" name=\"DirectoryHidden\" value=\"";
echo $paymentmethod->Directory;
echo "\" />\n\t\t\t\t\t\t\t<input type=\"hidden\" name=\"InternalName\" value=\"\" />\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("general");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("paymentmethod title");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"Title\" value=\"";
echo $paymentmethod->Title;
echo "\" ";
$ti = tabindex($ti);
echo " />\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t";
if(defined("ORDERFORM_ENABLED") && ORDERFORM_ENABLED == "yes") {
    echo "\t\t\t\t\t\t\t<strong class=\"title\">";
    echo __("paymentmethod description");
    echo "</strong>\n\t\t\t\t\t\t\t<textarea class=\"text1 size11\" name=\"Description\" ";
    $ti = tabindex($ti);
    echo ">";
    echo $paymentmethod->Description;
    echo "</textarea>\n\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("paymentmethod logo");
echo "</strong>\n\t\t\t\t\t\t<input type=\"hidden\" name=\"ImageHidden\" value=\"";
echo $paymentmethod->Image;
echo "\" />\n\t\t\t\t\t\t<select name=\"Image\" class=\"text1 size4f\">\n\t\t\t\t\t\t\t<option value=\"\">";
echo __("please select a logo");
echo "</option>\n\t\t\t\t\t\t</select>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("availability");
echo "</strong>\n\t\t\t\t\t\t<select name=\"Availability\" class=\"text1 size4f\">\n\t\t\t\t\t\t\t";
foreach ($paymentmethod->AvailabilityArray as $key => $val) {
    echo "\t\t\t\t\t\t\t<option value=\"";
    echo $key;
    echo "\" ";
    if($paymentmethod->Availability == $key) {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo $val;
    echo "</option>\n\t\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t</select>\t\n\t\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t</div>\n\t\t\t\t<!--left-->\n\t\t\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t<div class=\"right\">\n\t\t\t\t<!--right-->\n\t\t\t\t\t\n\t\t\t\t\t";
if(empty($paymentmethod->Identifier) || $paymentmethod->PaymentType == "other" || $paymentmethod->PaymentType == "ideal" || $paymentmethod->PaymentType == "paypal") {
    echo "\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t<div id=\"form_div_specific\" class=\"box3 hide\"><h3>";
    echo __("settings for paymentmethod");
    echo "</h3><div class=\"content\">\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<div id=\"form_div_MerchantID\">\n\t\t\t\t\t\t\t\t<strong id=\"form_txt_MerchantID\" class=\"title\">";
    echo __("merchantID");
    echo "</strong>\n\t\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"MerchantID\" value=\"";
    echo $paymentmethod->MerchantID;
    echo "\" ";
    $ti = tabindex($ti);
    echo " />\n\t\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<div id=\"form_div_Password\">\n\t\t\t\t\t\t\t\t<strong id=\"form_txt_Password\" class=\"title\">";
    echo __("secret key");
    echo "</strong>\n\t\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"Password\" value=\"";
    echo $paymentmethod->Password;
    echo "\" ";
    $ti = tabindex($ti);
    echo " />\n\t\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<div";
    if(empty($paymentmethod->Identifier) || $paymentmethod->Extra == "") {
        echo " class=\"hide\"";
    }
    echo ">\n\t\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t\t<strong class=\"title\">";
    echo __("paymentmethod text bank select");
    echo "</strong>\n\t\t\t\t\t\t\t\t<input type=\"text\" name=\"Extra\" class=\"text1 size1\" value=\"";
    echo $paymentmethod->Extra;
    echo "\" ";
    $ti = tabindex($ti);
    echo "/>\n\t\t\t\t\t\t\t</div>\n\t\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t</div></div>\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t";
}
echo "\t\t\t\t\t\n\t\t\t\t\t";
if(empty($paymentmethod->Identifier) || $paymentmethod->PaymentType == "auth") {
    echo "\t\t\t\t\t\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t<div class=\"form_div_directdebit box3";
    if(empty($paymentmethod->Identifier) && $paymentmethod->PaymentType != "auth") {
        echo " hide";
    }
    echo "\"><h3>";
    echo __("sdd setting h3 title");
    echo "</h3><div class=\"content\">\n\t\t\t\t\t\t<!--box3-->\n\n                            <input type=\"hidden\" name=\"change_sepa_type_value\" value=\"\" />\n\n\t\t\t\t\t\t\t<strong class=\"title\">";
    echo __("sdd setting sdd_id");
    echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" data-current-sddid=\"";
    echo SDD_ID;
    echo "\" name=\"SDD_ID\" value=\"";
    echo SDD_ID;
    echo "\"/>\n\t\t\t\t\t\t\t<br /><br />\n\n\t\t\t\t\t\t\t<strong class=\"title\">";
    echo __("sepa type");
    echo "</strong>\n\t\t\t\t\t\t\t<select name=\"SDD_TYPE\" class=\"text1 size4f\">\n\t\t\t\t\t\t\t\t<option value=\"CORE\" ";
    if(SDD_TYPE == "CORE") {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo __("sepa type core");
    echo "</option>\n\t\t\t\t\t\t\t\t<option value=\"B2B\" ";
    if(SDD_TYPE == "B2B") {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo __("sepa type b2b");
    echo "</option>\n\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t";
    $expl_ssd_days = explode(",", SDD_DAYS);
    echo "\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<strong class=\"title\">";
    echo __("sdd setting batches per month");
    echo "</strong>\n\t\t\t\t\t\t\t<select name=\"SDD_helper_batches_per_month\" class=\"text1 size6\">\n\t\t\t\t\t\t\t\t";
    for ($tmp_counter = 1; $tmp_counter <= 4; $tmp_counter++) {
        echo "\t\t\t\t\t\t\t\t<option value=\"";
        echo $tmp_counter;
        echo "\" ";
        if($tmp_counter == count($expl_ssd_days)) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo $tmp_counter;
        echo "</option>\n\t\t\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\t</select> \n\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<strong class=\"title\">";
    echo __("sdd setting batches on days");
    echo "</strong>\n\n                            ";
    $expl_sdd_days = constant("SDD_DAYS") ? explode(",", constant("SDD_DAYS")) : [1];
    echo "\n\t\t\t\t\t\t\t<div id=\"sdd_days_div\">\n                                ";
    foreach ($expl_sdd_days as $day) {
        echo "                                <div class=\"input_row\">\n                                    <select name=\"SDD_DAYS[]\" class=\"text1 size6\" style=\"margin-bottom:5px;\">\n                                        ";
        for ($tmp_day = 1; $tmp_day <= 28; $tmp_day++) {
            echo "                                        <option value=\"";
            echo $tmp_day;
            echo "\" ";
            if($tmp_day == $day) {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo $tmp_day;
            echo "</option>\n                                        ";
        }
        echo "                                    </select>\n                                    <div class=\"ico actionblock trash nm pointer sdd_remove_day\"></div>\n                                </div>\n                                ";
    }
    echo "                            </div>\n\n                            <a id=\"sdd_add_day\" class=\"a1 c1 normalfont pointer\">";
    echo __("sdd setting add day");
    echo "</a>\n\t\t\t\t\t\t\t<br /><br /><br />\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<label><input type=\"checkbox\" name=\"SDD_MAIL_NOTIFY\" value=\"yes\" ";
    if(SDD_MAIL_NOTIFY == "yes") {
        echo "checked=\"checked\"";
    }
    echo "/> ";
    echo sprintf(__("sdd setting notify email download file"), $company->EmailAddress);
    echo "</label>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t</div></div>\n\t\t\t\t\t\t<!--box3-->\t\n\t\t\t\t\t\t\n\t\t\t\t\t\t\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t<div class=\"form_div_directdebit box3";
    if(empty($paymentmethod->Identifier) && $paymentmethod->PaymentType != "auth") {
        echo " hide";
    }
    echo "\"><h3>";
    echo __("sdd setting terms h3");
    echo "</h3><div class=\"content\">\n\t\t\t\t\t\t<!--box3-->\t\t\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<strong class=\"title\">";
    echo __("sdd setting terms notification to customer");
    echo " <a class=\"a1 c1 smallfont normalfont\" id=\"sdd_default_notice_link\">(";
    echo __("sdd setting terms set default value");
    echo ")</a></strong>\n\t\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size3\" name=\"SDD_NOTICE\" value=\"";
    echo SDD_NOTICE;
    echo "\" /> ";
    echo __("sdd setting terms notification to customer, days before direct debit date");
    echo "\t\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<strong class=\"title\">";
    echo __("sdd setting terms processing times");
    echo "</strong>\n\n\t\t\t\t\t\t\t\t";
    if(SDD_PROCESSING_RCUR == "2") {
        echo "                                    <p>";
        echo __("sdd setting terms default values default 2");
        echo "<br /><a onclick=\"\$('#sdd_processing_times').removeClass('hide');\$(this).remove();\" class=\"a1 c1\">";
        echo __("sdd setting terms set default values");
        echo "</a></p>\n                                    ";
    }
    echo "                                <div id=\"sdd_processing_times\"";
    if(SDD_PROCESSING_RCUR == "2") {
        echo " class=\"hide\"";
    }
    echo ">\n                                    <input type=\"text\" class=\"text1 size3\" name=\"SDD_PROCESSING_RCUR\" value=\"";
    echo SDD_PROCESSING_RCUR;
    echo "\" /> ";
    echo __("sdd setting terms processing times, days");
    echo "                                </div>\n\t\t\t\t\t\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t</div></div>\n\t\t\t\t\t\t<!--box3-->\t\n\t\t\t\t\t\t\n\t\t\t\t\t\t\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t<div class=\"form_div_directdebit box3";
    if(empty($paymentmethod->Identifier) && $paymentmethod->PaymentType != "auth") {
        echo " hide";
    }
    echo "\"><h3>";
    echo __("sdd setting limits h3");
    echo "</h3><div class=\"content\">\n\t\t\t\t\t\t<!--box3-->\t\n\t\t\t\t\t\t\n\t\t\t\t\t\t\t<label><input type=\"checkbox\" name=\"SDD_limit_helper\" value=\"yes\" ";
    if(SDD_LIMIT_TRANSACTION || SDD_LIMIT_BATCH) {
        echo "checked=\"checked\"";
    }
    echo "/> ";
    echo __("sdd setting notify for limits");
    echo "</label>\n\t\t\t\t\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<div id=\"sdd_directdebit_limits\" ";
    if(!SDD_LIMIT_TRANSACTION && !SDD_LIMIT_BATCH) {
        echo "class=\"hide\"";
    }
    echo ">\n\t\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<strong class=\"title\">";
    echo __("sdd setting limit max per transaction");
    echo "</strong>\n\t\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size6\" name=\"SDD_LIMIT_TRANSACTION\" value=\"";
    echo SDD_LIMIT_TRANSACTION ? money(SDD_LIMIT_TRANSACTION, false) : "";
    echo "\"/>\n\t\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<strong class=\"title\">";
    echo __("sdd setting limit max per batch");
    echo "</strong>\n\t\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size6\" name=\"SDD_LIMIT_BATCH\" value=\"";
    echo SDD_LIMIT_BATCH ? money(SDD_LIMIT_BATCH, false) : "";
    echo "\"/>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t</div></div>\n\t\t\t\t\t\t<!--box3-->\t\t\n\t\t\t\t\t\t\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t<div class=\"form_div_directdebit box3";
    if(empty($paymentmethod->Identifier) && $paymentmethod->PaymentType != "auth") {
        echo " hide";
    }
    echo "\"><h3>";
    echo __("sdd setting bankaccount h3");
    echo "</h3><div class=\"content\">\n\t\t\t\t\t\t<!--box3-->\t\n\t\t\t\t\t\t\n\t\t\t\t\t\t\t<label><input type=\"checkbox\" name=\"SDD_iban_bic_helper\" value=\"yes\" ";
    if(SDD_IBAN || SDD_BIC) {
        echo "checked=\"checked\"";
    }
    echo "/> ";
    echo __("sdd setting use custom bankaccount");
    echo "</label>\n\t\t\t\t\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<div id=\"sdd_directdebit_iban_bic\" ";
    if(!SDD_IBAN && !SDD_BIC) {
        echo "class=\"hide\"";
    }
    echo ">\n\t\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t\t<strong class=\"title\">";
    echo __("iban number");
    echo "</strong>\n\t\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"SDD_IBAN\" value=\"";
    echo SDD_IBAN;
    echo "\"/>\n\t\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<strong class=\"title\">";
    echo __("bic");
    echo "</strong>\n\t\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"SDD_BIC\" value=\"";
    echo SDD_BIC;
    echo "\"/>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t</div></div>\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t<div class=\"form_div_directdebit box3";
    if(empty($paymentmethod->Identifier) && $paymentmethod->PaymentType != "auth") {
        echo " hide";
    }
    echo "\"><h3>";
    echo __("sdd setting messages h3");
    echo "</h3><div class=\"content\">\n\t\t\t\t\t\t<!--box3-->\t\n\t\t\t\t\t\t\n\t\t\t\t\t\t\t<strong class=\"title\">";
    echo __("sdd mail moved direct debit");
    echo "</strong>\n\t\t\t\t\t\t\t<select class=\"text1 size4f\" name=\"SDD_MOVED_MAIL\">\n\t\t\t\t\t\t \t\t<option value=\"\">";
    echo __("please choose");
    echo "</option>\n\t\t\t\t\t\t\t\t";
    foreach ($emailtemplates as $k => $v) {
        if(is_numeric($k)) {
            echo "\t\t\t\t\t\t\t\t<option value=\"";
            echo $k;
            echo "\" ";
            if(SDD_MOVED_MAIL == $k) {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo $v["Name"];
            echo "</option>\n\t\t\t\t\t\t\t\t";
        }
    }
    echo "\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<strong class=\"title\">";
    echo __("sdd mail failed direct debit");
    echo "</strong>\n\t\t\t\t\t\t\t<select class=\"text1 size4f\" name=\"SDD_FAILED_MAIL\">\n\t\t\t\t\t\t \t\t<option value=\"\">";
    echo __("please choose");
    echo "</option>\n\t\t\t\t\t\t\t\t";
    foreach ($emailtemplates as $k => $v) {
        if(is_numeric($k)) {
            echo "\t\t\t\t\t\t\t\t<option value=\"";
            echo $k;
            echo "\" ";
            if(SDD_FAILED_MAIL == $k) {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo $v["Name"];
            echo "</option>\n\t\t\t\t\t\t\t\t";
        }
    }
    echo "\t\t\t\t\t\t\t</select>\t\t\t\t\t\t\t\n\t\t\t\t\t\t\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t</div></div>\n\t\t\t\t\t\t<!--box3-->\n\n                        <div id=\"change_sdd_id\" class=\"hide\" title=\"";
    echo __("sdd changed");
    echo "\">\n                            <p>";
    echo __("sdd changed direct debit type");
    echo "</p>\n                            <br>\n                            <p><strong>";
    echo __("sdd change back");
    echo "</strong></p>\n                            <label><input type=\"radio\" name=\"change_sepa_type\" id=\"sepa_type_first\" value=\"sepa_type_first\" checked=\"checked\">";
    echo __("sdd change back yes");
    echo "</label><br>\n                            <label><input type=\"radio\" name=\"change_sepa_type\" id=\"sepa_type_no_change\" value=\"sepa_type_no_change\">";
    echo __("sdd change back no");
    echo "</label><br><br>\n\n                            <p><a id=\"change_sdd_id_confirm\" class=\"button1 alt1 float_left\"><span>";
    echo __("save");
    echo "</span></a></p>\n                        </div>\n\n\t\t\t\t\t";
}
echo "\t\t\t\t\t\n\t\t\t\t\t<div class=\"setting_help_box";
if(0 < $paymentmethod->Identifier || $paymentmethod->PaymentType == "auth") {
    echo " hide";
}
echo "\">\n\t\t\t\t\t\t<strong>";
echo __("payment method explained title");
echo "</strong><br />\n\t\t\t\t\t\t<span id=\"form_txt_Hint\">";
echo __("payment method explained");
echo "</span>\n\t\t\t\t\t</div>\n\t\t\t\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t</div>\n\t\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t<!--split2-->\n\t\t\t</div>\n\t\t\t<!--split2-->\n\t\t\t\t\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\t\t\n\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-transactioncost\">\n\t\t<!--content-->\n\n\t\t\t<!--split2-->\n\t\t\t<div class=\"split2\">\n\t\t\t<!--split2-->\n\t\t\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t<div class=\"left\">\n\t\t\t\t<!--left-->\t\n\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("general");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("should we bill transaction costs");
echo "</strong>\n\t\t\t\t\t\t<input type=\"radio\" class=\"text1\" id=\"feetype_helper_no\" name=\"FeeType_helper\" value=\"no\" ";
if($paymentmethod->FeeType == "") {
    echo "checked=\"checked\"";
}
echo "/> <label for=\"feetype_helper_no\">";
echo __("no");
echo "</label><br />\n\t\t\t\t\t\t<input type=\"radio\" class=\"text1\" id=\"feetype_helper_discount\" name=\"FeeType_helper\" value=\"discount\" ";
if($paymentmethod->FeeType && $paymentmethod->FeeAmount < 0) {
    echo "checked=\"checked\"";
}
echo "/> <label for=\"feetype_helper_discount\">";
echo __("yes, give discount");
echo "</label><br />\n\t\t\t\t\t\t<input type=\"radio\" class=\"text1\" id=\"feetype_helper_fee\" name=\"FeeType_helper\" value=\"fee\" ";
if($paymentmethod->FeeType && 0 < $paymentmethod->FeeAmount) {
    echo "checked=\"checked\"";
}
echo "/> <label for=\"feetype_helper_fee\">";
echo __("yes, bill fee");
echo "</label><br />\n\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<div id=\"form_div_transactioncost\" ";
if($paymentmethod->FeeType == "") {
    echo "class=\"hide\"";
}
echo ">\n\t\t\t\t\t\t\n\t\t\t\t\t\t\t<strong class=\"title\"><span id=\"feetype_title_discount\" ";
if(0 < $paymentmethod->FeeAmount) {
    echo "class=\"hide\"";
}
echo ">";
echo __("what kind of transaction discount do you want to give");
echo "</span><span id=\"feetype_title_fee\" ";
if($paymentmethod->FeeAmount < 0) {
    echo "class=\"hide\"";
}
echo ">";
echo __("what kind of transaction fee do you want to bill");
echo "</span></strong>\n\t\t\t\t\t\t\t<input type=\"radio\" class=\"text1\" id=\"feetype_eur\" name=\"FeeType\" value=\"EUR\" ";
if($paymentmethod->FeeType == "EUR" || $paymentmethod->FeeType == "") {
    echo "checked=\"checked\"";
}
echo "/> <label for=\"feetype_eur\">";
echo __("transaction fee as amount");
echo "</label><br />\n\t\t\t\t\t\t\t<input type=\"radio\" class=\"text1\" id=\"feetype_proc\" name=\"FeeType\" value=\"PROC\" ";
if($paymentmethod->FeeType == "PROC") {
    echo "checked=\"checked\"";
}
echo "/> <label for=\"feetype_proc\">";
echo __("transaction fee as percentage");
echo "</label><br />\n\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<strong class=\"title\"><span id=\"feetype_title_eur\" ";
if($paymentmethod->FeeType != "EUR") {
    echo "class=\"hide\"";
}
echo ">";
echo __("transaction fee as amount");
if(!empty($array_taxpercentages)) {
    echo " " . (VAT_CALC_METHOD == "incl" ? __("incl vat") : __("excl vat"));
}
echo "</span><span id=\"feetype_title_proc\" ";
if($paymentmethod->FeeType != "PROC") {
    echo "class=\"hide\"";
}
echo ">";
echo __("transaction fee as percentage");
echo "</span></strong>\n\t\t\t\t\t\t\t";
if($paymentmethod->FeeType != "EUR") {
    $tmp_fee_amount = 0 < $paymentmethod->FeeAmount ? $paymentmethod->FeeAmount : -1 * $paymentmethod->FeeAmount;
} else {
    $tmp_fee_amount = !empty($array_taxpercentages) && VAT_CALC_METHOD == "incl" ? $paymentmethod->FeeAmount * (1 + STANDARD_TAX) : $paymentmethod->FeeAmount;
    $tmp_fee_amount = 0 < $tmp_fee_amount ? money($tmp_fee_amount, false) : money(-1 * $tmp_fee_amount, false);
}
echo "\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size2";
if(VAT_CALC_METHOD == "incl") {
    echo " price_incl";
}
echo "\" name=\"FeeAmount\" value=\"";
echo $tmp_fee_amount;
echo "\" ";
$ti = tabindex($ti);
echo " /> <span id=\"transaction_fee_percentage_sign\" ";
if($paymentmethod->FeeType != "PROC") {
    echo "class=\"hide\"";
}
echo ">%</span>\n\t\t\t\t\t\t\t";
if(!empty($array_taxpercentages)) {
    if(VAT_CALC_METHOD == "incl") {
        echo "\t\t\t\t\t\t\t\t\t<span class=\"span_product_excl_incl";
        if($paymentmethod->FeeType != "EUR" || !$paymentmethod->FeeAmount) {
            echo " hide";
        }
        echo "\">";
        echo CURRENCY_SIGN_LEFT;
        echo " <span>";
        echo money($paymentmethod->FeeAmount, false);
        echo "</span> ";
        if(CURRENCY_SIGN_RIGHT) {
            echo CURRENCY_SIGN_RIGHT . " ";
        }
        echo __("excl vat");
        echo "</span>\n\t\t\t\t\t\t\t\t\t";
    } else {
        echo "\t\t\t\t\t\t\t\t\t<span class=\"span_product_excl_incl";
        if($paymentmethod->FeeType != "EUR" || !$paymentmethod->FeeAmount) {
            echo " hide";
        }
        echo "\">";
        echo CURRENCY_SIGN_LEFT;
        echo " <span>";
        echo money($paymentmethod->FeeAmount * (1 + STANDARD_TAX), false);
        echo "</span> ";
        if(CURRENCY_SIGN_RIGHT) {
            echo CURRENCY_SIGN_RIGHT . " ";
        }
        echo __("incl vat");
        echo "</span>\n\t\t\t\t\t\t\t\t\t";
    }
}
echo "\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("description transaction fee");
echo "</strong>\n\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"FeeDesc\" value=\"";
echo $paymentmethod->FeeDesc;
echo "\" ";
$ti = tabindex($ti);
echo " />\t\t\t\t\t\t\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t</div>\n\t\t\t\t<!--left-->\n\t\t\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t<div class=\"right\">\n\t\t\t\t<!--right-->\n\n\t\t\t\t\t<div class=\"setting_help_box\">\n\t\t\t\t\t\t";
echo __("transaction fee explained and warning");
echo "\t\t\t\t\t</div>\n\t\t\t\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t</div>\n\t\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t<!--split2-->\n\t\t\t</div>\n\t\t\t<!--split2-->\n\t\t\t\t\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\n\t<!--box1-->\n\t</div>\n\t<!--box1-->\n\n\t<br />\n\t\t\t\t\t\n\t<p class=\"align_right\"><a class=\"button1 alt1\" id=\"form_create_btn\"><span>";
echo $btn_form_title;
echo "</span></a></p>\n\t\n<!--form-->\n</fieldset></form>\n<!--form-->\n\n";
require_once "views/footer.php";

?>