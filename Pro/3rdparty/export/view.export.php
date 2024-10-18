<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "views/header.php";
echo "\n\t";
if($message) {
    echo $message . "<br />";
}
echo "\n\t<!--optionsbar-->\n\t<div style=\"display: block;width: 500px;float: right;text-align: right;padding-bottom: 7px;\">\n\t\t\t";
if($export->getLicenseStatus() == "TRIAL") {
    echo "\t\t\t\t";
    echo sprintf(__("export accounting - trial till"), $_SESSION[strtolower($package_information["package"]) . "-trialdate"]);
    echo " &nbsp; &nbsp;\n\n\t\t\t\t";
    if(checkRight(U_EXPORT_EDIT, false)) {
        echo "\t\t\t\t\t<a class=\"a1 c1\" onclick=\"\$('#stop_accounting_package').dialog('open');\">";
        echo __("export accounting - end license?");
        echo "</a>\n                    ";
    }
    echo "    \t\t";
} elseif(checkRight(U_EXPORT_EDIT, false)) {
    echo "\t\t\t\t<a class=\"a1 c1\" onclick=\"\$('#stop_accounting_package').dialog('open');\">";
    echo sprintf(__("export accounting - end subscription"), $package_information["name"]);
    echo " </a>\n\t\t\t";
}
echo "\t</div>\n\t<br style=\"clear: both;\" />\n\t<hr />\n\t<!--optionsbar-->\n\n\t";
if(isset($export->hasOAuth) && $export->hasOAuth === true && (isset($export->configuredOAuth) && $export->configuredOAuth === false || isset($_GET["oauth"]))) {
    require_once "3rdparty/export/view.export.oauth.php";
} else {
    echo "\t<!--heading1-->\n\t<div class=\"heading1\">\n\t<!--heading1-->\n\t\n\t\t<h2>";
    echo sprintf(__("export accounting - export to btn"), $package_information["name"]);
    echo "</h2>\n\t\n\t<!--heading1-->\n\t</div><hr />\n\t<!--heading1-->\n\t\n\t<div class=\"box2\" id=\"tabs\">\n\t\n\t\t<div class=\"top\">\n\t\t    <ul class=\"list3\">\n\t\t        <li class=\"on\"><a href=\"#tab-general\">";
    echo $package_information["name"];
    echo "</a></li>\n\t\t        ";
    if($export->hasSettings()) {
        echo "<li><a href=\"#tab-settings\">";
        echo sprintf(__("export accounting - settings tab"), $package_information["name"]);
        echo " </a></li>";
    }
    echo "\t\t    </ul>\n\t\t</div>\n\t\t\n\t\t\n\t\t<div class=\"content\" id=\"tab-general\">\n\t\t    <form method=\"post\" action=\"export.php?page=accounting_package&amp;module=";
    echo $package_information["package"];
    echo "\">\n\t\t    <input type=\"hidden\" name=\"mode\" value=\"export\" />\n\t\t    <div class=\"split2\">\n\t\t        <div class=\"left\">\n\t\t        \n\t\t        \t";
    if($export->hasLoginCredentials()) {
        $export->showLoginHTML();
    } elseif(method_exists($export, "showOAuthEnvironment")) {
        $export->showOAuthEnvironment();
    }
    echo "\t\t        \n\t\t            <div class=\"box3\"><h3>";
    echo __("export accounting - which data to export");
    echo "</h3><div class=\"content\">\n\t\t            \n\t\t                    ";
    if($export->supported["debtors"] === true) {
        echo "\t\t                    <label><input type=\"radio\" name=\"type\" value=\"debtor\" class=\"export_radio\"";
        if(isset($_POST["type"]) && $_POST["type"] == "debtor") {
            echo " checked=\"checked\"";
        }
        echo "/> <strong>";
        echo __("debtors");
        echo "</strong></label>\n\t\t                    ";
        $export->showLastExport("debtors", "debtor");
        echo "\t\t                    ";
    }
    echo "\t\t                    \t\n\t\t                    ";
    if($export->supported["creditors"] === true) {
        echo "\t\t                    <label><input type=\"radio\" name=\"type\" value=\"creditor\" class=\"export_radio\"";
        if(isset($_POST["type"]) && $_POST["type"] == "creditor") {
            echo " checked=\"checked\"";
        }
        echo "/> <strong>";
        echo __("creditors");
        echo "</strong></label>\n\t\t                    ";
        $export->showLastExport("creditors", "creditor");
        echo "\t\t                    ";
    }
    echo "\t\t                    \n\t\t                    ";
    if($export->supported["products"] === true) {
        echo "\t\t                    <label><input type=\"radio\" name=\"type\" value=\"product\" class=\"export_radio\"";
        if(isset($_POST["type"]) && $_POST["type"] == "product") {
            echo " checked=\"checked\"";
        }
        echo "/> <strong>";
        echo __("products");
        echo "</strong></label>\n\t\t                    ";
        $export->showLastExport("products", "product");
        echo "\t\t                    ";
    }
    echo "\t\t                    \n\t\t                    ";
    if($export->supported["invoices"] === true) {
        echo "\t\t                    <label><input type=\"radio\" name=\"type\" value=\"invoice\" class=\"export_radio\"";
        if(isset($_POST["type"]) && $_POST["type"] == "invoice") {
            echo " checked=\"checked\"";
        }
        echo "/> <strong>";
        echo __("invoices");
        echo "</strong></label>\n\t\t                    ";
        $export->showLastExport("invoices", "invoice");
        echo "\t\t                    ";
    }
    echo "\t\t                    \n\t\t                    ";
    if(isset($export->supported["sddbatches"]) && $export->supported["sddbatches"] === true) {
        echo "\t\t                    <label><input type=\"radio\" name=\"type\" value=\"sddbatches\" class=\"export_radio\"";
        if(isset($_POST["type"]) && $_POST["type"] == "sddbatches") {
            echo " checked=\"checked\"";
        }
        echo "/> <strong>";
        echo __("sdd batches");
        echo "</strong></label>\n\t\t                    ";
        $export->showLastExport("sddbatchess", "sddbatches");
        echo "\t\t                    ";
    }
    echo "\t\t                    \n\t\t                    ";
    if($export->supported["creditinvoices"] === true) {
        echo "\t\t                    <label><input type=\"radio\" name=\"type\" value=\"creditinvoice\" class=\"export_radio\"";
        if(isset($_POST["type"]) && $_POST["type"] == "creditinvoice") {
            echo " checked=\"checked\"";
        }
        echo "/> <strong>";
        echo __("creditinvoice");
        echo "</strong></label>\n\t\t                    ";
        $export->showLastExport("creditinvoices", "creditinvoice");
        echo "\t\t                    ";
    }
    echo "\t\t                    \n\t\t\t\t\t</div></div>\n\t\t\t\t\t\n\t\t            <div id=\"div_filters\" class=\"box3";
    if(!isset($_POST["type"])) {
        echo " hide";
    }
    echo "\"><h3>";
    echo __("filters");
    echo "</h3><div class=\"content\">\n\t\t            \t\t\n\t\t            \t";
    if($export->supported["debtors"] === true) {
        echo "\t            \t\t<div id=\"div_filters-debtor\"";
        if(!isset($_POST["type"]) || $_POST["type"] != "debtor") {
            echo " class=\"hide\"";
        }
        echo ">\n\t                        <strong class=\"title\"><label><input class=\"filter_checkbox\" type=\"checkbox\" id=\"filters[debtor_code]\" name=\"filters[debtor_code]\"";
        if(isset($_POST["filters"]["debtor_code"])) {
            echo " checked=\"checked\"";
        }
        echo "/> ";
        echo __("debtorcode");
        echo "</label></strong>\n\t                        <div class=\"filters_advanced";
        if(!isset($_POST["filters"]["debtor_code"])) {
            echo " hide";
        }
        echo "\" id=\"div_debtor_code\">\n\t                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; \n\t\t\t\t\t\t\t\t<input type=\"text\" name=\"debtor_code_min\" class=\"text1 size6\" value=\"";
        if(isset($_POST["debtor_code_min"])) {
            echo htmlspecialchars($_POST["debtor_code_min"]);
        }
        echo "\"/>&nbsp; ";
        echo __("from till");
        echo " &nbsp;<input type=\"text\" name=\"debtor_code_max\" class=\"text1 size6\" value=\"";
        if(isset($_POST["debtor_code_max"])) {
            echo htmlspecialchars($_POST["debtor_code_max"]);
        }
        echo "\"/>\n\t                            <br /><br />\n\t                        </div>\n\t                        \n\t                        ";
        $filter_debtortype = $export->getFilter("debtor", "debtortype");
        if(is_array($filter_debtortype)) {
            echo "\t                        \t<strong class=\"title\"><label><input class=\"filter_checkbox\" type=\"checkbox\" id=\"filters[debtor_debtortype]\" name=\"filters[debtor_debtortype]\"";
            if(isset($_POST["filters"]["debtor_debtortype"])) {
                echo " checked=\"checked\"";
            }
            echo "/> ";
            echo $filter_debtortype["name"];
            echo "</label></strong>\n\t\t                        <div class=\"filters_advanced";
            if(!isset($_POST["filters"]["debtor_debtortype"])) {
                echo " hide";
            }
            echo "\" id=\"div_debtor_debtortype\">\n\t                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n\t                                <select name=\"debtor_debtortype_val\" class=\"text1 size12\">\n\t                                    <option value=\"companies\"";
            if(isset($_POST["debtor_debtortype_val"]) && $_POST["debtor_debtortype_val"] == "companies") {
                echo " selected=\"selected\"";
            }
            echo ">";
            echo __("export accounting - companies");
            echo "</option>\n\t                                    <option value=\"individuals\"";
            if(isset($_POST["debtor_debtortype_val"]) && $_POST["debtor_debtortype_val"] == "individuals") {
                echo " selected=\"selected\"";
            }
            echo ">";
            echo __("export accounting - consumers");
            echo "</option>\n\t                                </select>\n\t                                <br />\n\t                            </div>\n\t                        \t";
        }
        echo "\t                        \n\t                    </div>\n\t            \t\t";
    }
    echo "\t            \t\t\n\t            \t\t";
    if($export->supported["creditors"] === true) {
        echo "\t                    <div id=\"div_filters-creditor\"";
        if(!isset($_POST["type"]) || $_POST["type"] != "creditor") {
            echo " class=\"hide\"";
        }
        echo ">\n\t                        <strong class=\"title\"><label><input class=\"filter_checkbox\" type=\"checkbox\" id=\"filters[creditor_code]\" name=\"filters[creditor_code]\"";
        if(isset($_POST["filters"]["creditor_code"])) {
            echo " checked=\"checked\"";
        }
        echo "/> ";
        echo __("creditorcode full");
        echo "</label></strong>\n\t                        <div class=\"filters_advanced";
        if(!isset($_POST["filters"]["creditor_code"])) {
            echo " hide";
        }
        echo "\" id=\"div_creditor_code\">\n\t                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type=\"text\" name=\"creditor_code_min\" class=\"text1 size6\" value=\"";
        if(isset($_POST["creditor_code_min"])) {
            echo htmlspecialchars($_POST["creditor_code_min"]);
        }
        echo "\"/>&nbsp; ";
        echo __("from till");
        echo " &nbsp;<input type=\"text\" name=\"creditor_code_max\" class=\"text1 size6\" value=\"";
        if(isset($_POST["creditor_code_max"])) {
            echo htmlspecialchars($_POST["creditor_code_max"]);
        }
        echo "\"/>\n\t                            <br /><br />\n\t                        </div>\n\t                        \n\t                        ";
        $filter_creditortype = $export->getFilter("creditor", "creditortype");
        if(is_array($filter_creditortype)) {
            echo "\t                        \t<strong class=\"title\"><label><input class=\"filter_checkbox\" type=\"checkbox\" id=\"filters[creditor_creditortype]\" name=\"filters[creditor_creditortype]\"";
            if(isset($_POST["filters"]["creditor_creditortype"])) {
                echo " checked=\"checked\"";
            }
            echo "/> ";
            echo $filter_creditortype["name"];
            echo "</label></strong>\n\t\t                        <div class=\"filters_advanced";
            if(!isset($_POST["filters"]["creditor_creditortype"])) {
                echo " hide";
            }
            echo "\" id=\"div_creditor_creditortype\">\n\t                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n\t                                <select name=\"creditor_creditortype_val\" class=\"text1 size12\">\n\t                                    <option value=\"companies\"";
            if(isset($_POST["creditor_creditortype_val"]) && $_POST["creditor_creditortype_val"] == "companies") {
                echo " selected=\"selected\"";
            }
            echo ">";
            echo __("export accounting - companies");
            echo "</option>\n\t                                    <option value=\"individuals\"";
            if(isset($_POST["creditor_creditortype_val"]) && $_POST["creditor_creditortype_val"] == "individuals") {
                echo " selected=\"selected\"";
            }
            echo ">";
            echo __("export accounting - consumers");
            echo "</option>\n\t                                </select>\n\t                                <br />\n\t                            </div>\n\t                        \t";
        }
        echo "\t                    </div>\n\t                    ";
    }
    echo "\t                    \n\t                    ";
    if($export->supported["products"] === true) {
        echo "\t                    <div id=\"div_filters-product\"";
        if(!isset($_POST["type"]) || $_POST["type"] != "product") {
            echo " class=\"hide\"";
        }
        echo ">\n\t                        <strong class=\"title\"><label><input class=\"filter_checkbox\" type=\"checkbox\" id=\"filters[product_code]\" name=\"filters[product_code]\"";
        if(isset($_POST["filters"]["product_code"])) {
            echo " checked=\"checked\"";
        }
        echo "/> ";
        echo __("productcode full");
        echo "</label></strong>\n\t                        <div class=\"filters_advanced";
        if(!isset($_POST["filters"]["product_code"])) {
            echo " hide";
        }
        echo "\" id=\"div_product_code\">\n\t                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; \n\t\t\t\t\t\t\t\t<input type=\"text\" name=\"product_code_min\" class=\"text1 size6\" value=\"";
        if(isset($_POST["product_code_min"])) {
            echo htmlspecialchars($_POST["product_code_min"]);
        }
        echo "\"/>&nbsp; ";
        echo __("from till");
        echo " &nbsp;<input type=\"text\" name=\"product_code_max\" class=\"text1 size6\" value=\"";
        if(isset($_POST["product_code_max"])) {
            echo htmlspecialchars($_POST["product_code_max"]);
        }
        echo "\"/>\n\t                            <br />\n\t                        </div>\n\t                    </div>\n\t                    ";
    }
    echo "\t                    \n\t                    ";
    if($export->supported["invoices"] === true) {
        echo "\t                    <div id=\"div_filters-invoice\"";
        if(!isset($_POST["type"]) || $_POST["type"] != "invoice") {
            echo " class=\"hide\"";
        }
        echo ">\n\t                        <strong class=\"title\"><label><input class=\"filter_checkbox\" type=\"checkbox\" name=\"filters[invoice_date]\"";
        if(isset($_POST["filters"]["invoice_date"])) {
            echo " checked=\"checked\"";
        }
        echo "/> ";
        echo __("invoice date");
        echo "</label></strong>\n\t                        <div class=\"filters_advanced";
        if(!isset($_POST["filters"]["invoice_date"])) {
            echo " hide";
        }
        echo "\" id=\"div_invoice_date\">\n\t                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type=\"text\" name=\"invoice_date_min\" class=\"text1 size6 datepicker\" value=\"";
        if(isset($_POST["invoice_date_min"])) {
            echo htmlspecialchars($_POST["invoice_date_min"]);
        }
        echo "\" />&nbsp; ";
        echo __("from till");
        echo " &nbsp;<input type=\"text\" name=\"invoice_date_max\" class=\"text1 size6 datepicker\" value=\"";
        if(isset($_POST["invoice_date_max"])) {
            echo htmlspecialchars($_POST["invoice_date_max"]);
        }
        echo "\"/>\n\t                            <br />\n\t                        </div>\n\t\n\t                        <strong class=\"title\"><label><input class=\"filter_checkbox\" type=\"checkbox\" id=\"filters[invoice_code]\" name=\"filters[invoice_code]\"";
        if(isset($_POST["filters"]["invoice_code"])) {
            echo " checked=\"checked\"";
        }
        echo "/> ";
        echo __("invoicecode");
        echo "</label></strong>\n\t                        <div class=\"filters_advanced";
        if(!isset($_POST["filters"]["invoice_code"])) {
            echo " hide";
        }
        echo "\" id=\"div_invoice_code\">\n\t                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type=\"text\" name=\"invoice_code_min\" class=\"text1 size6\" value=\"";
        if(isset($_POST["invoice_code_min"])) {
            echo htmlspecialchars($_POST["invoice_code_min"]);
        }
        echo "\"/>&nbsp; ";
        echo __("from till");
        echo " &nbsp;<input type=\"text\" name=\"invoice_code_max\" class=\"text1 size6\" value=\"";
        if(isset($_POST["invoice_code_max"])) {
            echo htmlspecialchars($_POST["invoice_code_max"]);
        }
        echo "\"/>\n\t                            <br />\n\t                        </div>\n\t\n\t\t\t\t\t\t\t";
        $show_advanced_filter_div = isset($_POST["filters"]["invoice_paymentdate"]) || isset($_POST["filters"]["invoice_country"]) || isset($_POST["filters"]["invoice_status"]) ? true : false;
        echo "\t                        <div class=\"div_advanced_filters";
        if(!$show_advanced_filter_div) {
            echo " hide";
        }
        echo "\">\n\t\n\t                            <strong class=\"title\"><label><input class=\"filter_checkbox\" type=\"checkbox\" id=\"filters[invoice_paymentdate]\" name=\"filters[invoice_paymentdate]\"";
        if(isset($_POST["filters"]["invoice_paymentdate"])) {
            echo " checked=\"checked\"";
        }
        echo "/> ";
        echo __("payment date");
        echo "</label></strong>\n\t                            <div class=\"filters_advanced";
        if(!isset($_POST["filters"]["invoice_paymentdate"])) {
            echo " hide";
        }
        echo "\" id=\"div_invoice_paymentdate\">\n\t                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type=\"text\" name=\"invoice_paymentdate_min\" class=\"text1 size6 datepicker\" value=\"";
        if(isset($_POST["invoice_paymentdate_min"])) {
            echo htmlspecialchars($_POST["invoice_paymentdate_min"]);
        }
        echo "\"/>&nbsp; ";
        echo __("from till");
        echo " &nbsp;<input type=\"text\" name=\"invoice_paymentdate_max\" class=\"text1 size6 datepicker\" value=\"";
        if(isset($_POST["invoice_paymentdate_max"])) {
            echo htmlspecialchars($_POST["invoice_paymentdate_max"]);
        }
        echo "\"/>\n\t                                <br />\n\t                            </div>\n\t\n\t                            <strong class=\"title\"><label><input class=\"filter_checkbox\" type=\"checkbox\" id=\"filters[invoice_country]\" name=\"filters[invoice_country]\"";
        if(isset($_POST["filters"]["invoice_country"])) {
            echo " checked=\"checked\"";
        }
        echo "/> ";
        echo __("country");
        echo "</label></strong>\n\t                            <div class=\"filters_advanced";
        if(!isset($_POST["filters"]["invoice_country"])) {
            echo " hide";
        }
        echo "\" id=\"div_invoice_country\">\n\t                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n\t                                <select name=\"invoice_country_val\" class=\"text1 size12 datepicker\">\n\t                                    <option value=\"nl\"";
        if(isset($_POST["invoice_country_val"]) && $_POST["invoice_country_val"] == "nl") {
            echo " selected=\"selected\"";
        }
        echo ">Nederland</option>\n\t                                    <option value=\"eu\"";
        if(isset($_POST["invoice_country_val"]) && $_POST["invoice_country_val"] == "eu") {
            echo " selected=\"selected\"";
        }
        echo ">Overige EU landen</option>\n\t                                    <option value=\"non-eu\"";
        if(isset($_POST["invoice_country_val"]) && $_POST["invoice_country_val"] == "non-eu") {
            echo " selected=\"selected\"";
        }
        echo ">Alle landen buiten Europa</option>\n\t                                </select>\n\t                                <br />\n\t                            </div>\n\t\n\t\t\t\t\t\t\t\t";
        global $array_invoicestatus;
        echo "\t                            <strong class=\"title\"><label><input class=\"filter_checkbox\" type=\"checkbox\" id=\"filters[invoice_status]\" name=\"filters[invoice_status]\"";
        if(isset($_POST["filters"]["invoice_status"])) {
            echo " checked=\"checked\"";
        }
        echo "/> ";
        echo __("status");
        echo "</label></strong>\n\t                            <div class=\"filters_advanced";
        if(!isset($_POST["filters"]["invoice_status"])) {
            echo " hide";
        }
        echo "\" id=\"div_invoice_status\">\n\t                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n\t                                <select name=\"invoice_status_val\" class=\"text1 size12 datepicker\">\n\t                                    <option value=\"2\"";
        if(isset($_POST["invoice_status_val"]) && $_POST["invoice_status_val"] == "2") {
            echo " selected=\"selected\"";
        }
        echo ">";
        echo $array_invoicestatus[2];
        echo "</option>\n\t                                    <option value=\"3\"";
        if(isset($_POST["invoice_status_val"]) && $_POST["invoice_status_val"] == "3") {
            echo " selected=\"selected\"";
        }
        echo ">";
        echo $array_invoicestatus[3];
        echo "</option>\n\t                                    <option value=\"4\"";
        if(isset($_POST["invoice_status_val"]) && $_POST["invoice_status_val"] == "4") {
            echo " selected=\"selected\"";
        }
        echo ">";
        echo $array_invoicestatus[4];
        echo "</option>\n\t                                    <option value=\"8\"";
        if(isset($_POST["invoice_status_val"]) && $_POST["invoice_status_val"] == "8") {
            echo " selected=\"selected\"";
        }
        echo ">";
        echo $array_invoicestatus[8];
        echo "</option>\n\t                                    <option value=\"9\"";
        if(isset($_POST["invoice_status_val"]) && $_POST["invoice_status_val"] == "9") {
            echo " selected=\"selected\"";
        }
        echo ">";
        echo $array_invoicestatus[9];
        echo "</option>\n\t                                </select>\n\t                                <br />\n\t                            </div>\n\t                            \n\t                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a class=\"a1 c1 normalfont pointer\" onclick=\"\$('.div_advanced_filters').hide();\$('.div_advanced_toggler').show();\">";
        echo __("export accounting - hide extended filters");
        echo "</a>\n\t                        </div>\n\t                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a class=\"a1 c1 normalfont pointer div_advanced_toggler";
        if($show_advanced_filter_div) {
            echo " hide";
        }
        echo "\" onclick=\"\$(this).hide();\$('.div_advanced_filters').show();\">";
        echo __("export accounting - show extended filters");
        echo "</a>\n\t                    </div>\n\t                    ";
    }
    echo "\t                    \n\t                    ";
    if(isset($export->supported["sddbatches"]) && $export->supported["sddbatches"] === true) {
        $batches_array = $export->getSDDBatches();
        krsort($batches_array);
        $batch_options = ["not_exported" => [], "exported" => []];
        foreach ($batches_array as $_batch) {
            if(isset($_batch["ExportedAt"]) && $_batch["ExportedAt"]) {
                $batch_options["exported"][] = $_batch;
            } else {
                $batch_options["not_exported"][] = $_batch;
            }
        }
        echo "\t\t                    <div id=\"div_filters-sddbatches\"";
        if(!isset($_POST["type"]) || $_POST["type"] != "sddbatches") {
            echo " class=\"hide\"";
        }
        echo ">\n\t\t                        \n\t\t\t\t\t\t\t\t\n\t\t                        <div class=\"filters_advanced\" id=\"div_sddbatches_batch\">\n\t\t                            <strong class=\"title\"><label> ";
        echo __("sdd batch");
        echo "</label></strong> \n\t\t                            <input class=\"filter_checkbox\" type=\"hidden\" id=\"filters[sddbatches_batch]\" name=\"filters[sddbatches_batch]\"/>\n\t\t\t\t\t\t\t\t\t<select name=\"sddbatches_batch_min\" class=\"text1\">\n\t\t\t\t\t\t\t\t\t\t";
        foreach ($batch_options as $optgroup => $_group_batches) {
            if(!empty($_group_batches)) {
                $selected = isset($_POST["sddbatches_batch_min"]) ? $_POST["sddbatches_batch_min"] : "";
                echo "<optgroup label=\"" . __("accounting export optgroup label " . $optgroup) . "\">";
                if($optgroup == "not_exported" && $selected == "") {
                    $selected = end($_group_batches);
                    $selected = $selected["BatchID"];
                }
                foreach ($_group_batches as $_batch) {
                    echo "<option value=\"";
                    echo $_batch["BatchID"];
                    echo "\"";
                    if($selected == $_batch["BatchID"]) {
                        echo " selected=\"selected\"";
                    }
                    echo ">";
                    echo $_batch["BatchID"];
                    echo "</option>";
                }
                echo "</optgroup>";
            } elseif($optgroup == "not_exported") {
                $selected = isset($_POST["sddbatches_batch_min"]) ? $_POST["sddbatches_batch_min"] : "";
                echo "<optgroup label=\"" . __("accounting export optgroup label " . $optgroup) . "\">";
                echo "<option value=\"\"";
                if(!$selected) {
                    echo " selected=\"selected\"";
                }
                echo ">";
                echo __("please choose");
                echo "</option></optgroup>";
            }
        }
        echo "\t\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t\t&nbsp; tot en met &nbsp;\n\t\t\t\t\t\t\t\t\t<select name=\"sddbatches_batch_max\" class=\"text1\">\n\t\t\t\t\t\t\t\t\t\t";
        foreach ($batch_options as $optgroup => $_group_batches) {
            if(!empty($_group_batches)) {
                $selected = isset($_POST["sddbatches_batch_max"]) ? $_POST["sddbatches_batch_max"] : "";
                echo "<optgroup label=\"" . __("accounting export optgroup label " . $optgroup) . "\">";
                foreach ($_group_batches as $_batch) {
                    if($optgroup == "not_exported" && $selected == "") {
                        $selected = $_batch["BatchID"];
                    }
                    echo "<option value=\"";
                    echo $_batch["BatchID"];
                    echo "\"";
                    if($selected == $_batch["BatchID"]) {
                        echo " selected=\"selected\"";
                    }
                    echo ">";
                    echo $_batch["BatchID"];
                    echo "</option>";
                }
                echo "</optgroup>";
            } elseif($optgroup == "not_exported") {
                $selected = isset($_POST["sddbatches_batch_max"]) ? $_POST["sddbatches_batch_max"] : "";
                echo "<optgroup label=\"" . __("accounting export optgroup label " . $optgroup) . "\">";
                echo "<option value=\"\"";
                if(!$selected) {
                    echo " selected=\"selected\"";
                }
                echo ">";
                echo __("please choose");
                echo "</option></optgroup>";
            }
        }
        echo "\t\t\t\t\t\t\t\t\t</select>\n\t\t                            <br />\n\t\t                        </div>\n\t\t                    </div>\n\t\t                    ";
    }
    echo "\t                    \n\t                    ";
    if($export->supported["creditinvoices"] === true) {
        echo "\t                    <div id=\"div_filters-creditinvoice\"";
        if(!isset($_POST["type"]) || $_POST["type"] != "creditinvoice") {
            echo " class=\"hide\"";
        }
        echo ">\n\t                        <strong class=\"title\"><label><input class=\"filter_checkbox\" type=\"checkbox\" name=\"filters[creditinvoice_date]\"";
        if(isset($_POST["filters"]["creditinvoice_date"])) {
            echo " checked=\"checked\"";
        }
        echo "/> ";
        echo __("invoice date");
        echo "</label></strong>\n\t                        <div class=\"filters_advanced";
        if(!isset($_POST["filters"]["creditinvoice_date"])) {
            echo " hide";
        }
        echo "\" id=\"div_invoice_date\">\n\t                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type=\"text\" name=\"creditinvoice_date_min\" class=\"text1 size6 datepicker\" value=\"";
        if(isset($_POST["creditinvoice_date_min"])) {
            echo htmlspecialchars($_POST["creditinvoice_date_min"]);
        }
        echo "\" />&nbsp; ";
        echo __("from till");
        echo " &nbsp;<input type=\"text\" name=\"creditinvoice_date_max\" class=\"text1 size6 datepicker\" value=\"";
        if(isset($_POST["creditinvoice_date_max"])) {
            echo htmlspecialchars($_POST["creditinvoice_date_max"]);
        }
        echo "\"/>\n\t                            <br />\n\t                        </div>\n\t\n\t                        <strong class=\"title\"><label><input class=\"filter_checkbox\" type=\"checkbox\" id=\"filters[creditinvoice_code]\" name=\"filters[creditinvoice_code]\"";
        if(isset($_POST["filters"]["creditinvoice_code"])) {
            echo " checked=\"checked\"";
        }
        echo "/> ";
        echo __("export purchase creditinvoicecode");
        echo "</label></strong>\n\t                        <div class=\"filters_advanced";
        if(!isset($_POST["filters"]["creditinvoice_code"])) {
            echo " hide";
        }
        echo "\" id=\"div_creditinvoice_code\">\n\t                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type=\"text\" name=\"creditinvoice_code_min\" class=\"text1 size6\" value=\"";
        if(isset($_POST["creditinvoice_code_min"])) {
            echo htmlspecialchars($_POST["creditinvoice_code_min"]);
        }
        echo "\"/>&nbsp; ";
        echo __("from till");
        echo " &nbsp;<input type=\"text\" name=\"creditinvoice_code_max\" class=\"text1 size6\" value=\"";
        if(isset($_POST["creditinvoice_code_max"])) {
            echo htmlspecialchars($_POST["creditinvoice_code_max"]);
        }
        echo "\"/>\n\t                            <br />\n\t                        </div>\n\t\n\t                        ";
        $show_advanced_filter_div = isset($_POST["filters"]["creditinvoice_paymentdate"]) || isset($_POST["filters"]["creditinvoice_country"]) ? true : false;
        echo "\t                        <div class=\"div_advanced_filters";
        if(!$show_advanced_filter_div) {
            echo " hide";
        }
        echo "\">\n\t\n\t                            <strong class=\"title\"><label><input class=\"filter_checkbox\" type=\"checkbox\" id=\"filters[creditinvoice_paymentdate]\" name=\"filters[creditinvoice_paymentdate]\"";
        if(isset($_POST["filters"]["creditinvoice_paymentdate"])) {
            echo " checked=\"checked\"";
        }
        echo "/> ";
        echo __("paymentdate");
        echo "</label></strong>\n\t                            <div class=\"filters_advanced";
        if(!isset($_POST["filters"]["creditinvoice_paymentdate"])) {
            echo " hide";
        }
        echo "\" id=\"div_creditinvoice_paymentdate\">\n\t                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type=\"text\" name=\"creditinvoice_paymentdate_min\" class=\"text1 size6 datepicker\" value=\"";
        if(isset($_POST["creditinvoice_paymentdate_min"])) {
            echo htmlspecialchars($_POST["creditinvoice_paymentdate_min"]);
        }
        echo "\"/>&nbsp; ";
        echo __("from till");
        echo " &nbsp;<input type=\"text\" name=\"creditinvoice_paymentdate_max\" class=\"text1 size6 datepicker\" value=\"";
        if(isset($_POST["creditinvoice_paymentdate_max"])) {
            echo htmlspecialchars($_POST["creditinvoice_paymentdate_max"]);
        }
        echo "\"/>\n\t                                <br />\n\t                            </div>\n\t\n\t                            <strong class=\"title\"><label><input class=\"filter_checkbox\" type=\"checkbox\" id=\"filters[creditinvoice_country]\" name=\"filters[creditinvoice_country]\"";
        if(isset($_POST["filters"]["creditinvoice_country"])) {
            echo " checked=\"checked\"";
        }
        echo "/> ";
        echo __("country");
        echo "</label></strong>\n\t                            <div class=\"filters_advanced";
        if(!isset($_POST["filters"]["creditinvoice_country"])) {
            echo " hide";
        }
        echo "\" id=\"div_creditinvoice_country\">\n\t                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n\t                                <select name=\"creditinvoice_country_val\" class=\"text1 size12 datepicker\">\n\t                                    <option value=\"nl\"";
        if(isset($_POST["creditinvoice_country_val"]) && $_POST["creditinvoice_country_val"] == "nl") {
            echo " selected=\"selected\"";
        }
        echo ">Nederland</option>\n\t                                    <option value=\"eu\"";
        if(isset($_POST["creditinvoice_country_val"]) && $_POST["creditinvoice_country_val"] == "eu") {
            echo " selected=\"selected\"";
        }
        echo ">Overige EU landen</option>\n\t                                    <option value=\"non-eu\"";
        if(isset($_POST["creditinvoice_country_val"]) && $_POST["creditinvoice_country_val"] == "non-eu") {
            echo " selected=\"selected\"";
        }
        echo ">Alle landen buiten Europa</option>\n\t                                </select>\n\t                                <br />\n\t                            </div>\n\t                            \n\t                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a class=\"a1 c1 normalfont pointer\" onclick=\"\$('.div_advanced_filters').hide();\$('.div_advanced_toggler').show();\">";
        echo __("export accounting - hide extended filters");
        echo "</a>\n\t                        </div>\n\t                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a class=\"a1 c1 normalfont pointer div_advanced_toggler";
        if($show_advanced_filter_div) {
            echo " hide";
        }
        echo "\" onclick=\"\$(this).hide();\$('.div_advanced_filters').show();\">";
        echo __("export accounting - show extended filters");
        echo "</a>\n\t                    </div>\n\t                    ";
    }
    echo "\t                    \n\t\t                    \n\t\t            </div></div>\n\t\t           \n\t\t\t\t\t<p class=\"align_left\">\n\t\t\t\t\t<span id=\"loader_download\" class=\"hide\">\n\t\t\t\t\t\t<img src=\"images/icon_circle_loader_green.gif\" alt=\"";
    echo __("loading");
    echo "\" class=\"ico inline\" style=\"margin-bottom:-3px;\" />&nbsp;&nbsp;\n\t\t\t\t\t\t<span class=\"loading_green\">";
    echo __("loading");
    echo "</span>&nbsp;&nbsp;\n\t\t\t\t\t</span>\n\t\t\t\t\t\t<a class=\"button1 alt1 pointer";
    if(!isset($_POST["type"])) {
        echo " hide";
    }
    echo "\" id=\"form_export_btn\" onclick=\"\$('#loader_download').show(); \$(this).hide();\"><span>";
    echo __("export accounting - export");
    echo "</span></a>\n\t\t\t\t\t</p>\n\t\t           \n\t\t        </div>\n\t\t        \n\t\t        <div class=\"right\">\n\t\t        \n\t\t        \t<div class=\"accountingpackage_info\">\n\t\t        \t\t";
    $export->showHelpHTML();
    echo "\t\t        \t</div>\n\t\t        </div>\n\t\t    </div>\n\t\t    <br />\n\t\t    </form>\n\t\t</div>\n\t\t\n\t\t\n\t\t";
    if($export->hasSettings()) {
        echo "\t\t<div class=\"content\" id=\"tab-settings\">\n\t\t\n\t\t    <form method=\"post\" action=\"export.php?page=accounting_package&amp;module=";
        echo $package_information["package"];
        echo "\">\n\t\t    <input type=\"hidden\" name=\"mode\" value=\"saveSettings\" />\n\t\t    \n\t\t    ";
        if(method_exists($export, "showSettingsHTML")) {
            $export->showSettingsHTML();
        } else {
            echo "\t\t\t\t<div class=\"split2\">\n\t\t\t        <div class=\"left\">\n\t\t\t            <div class=\"box3\">\n\t\t\t                <h3>Grootboekrekeningen</h3>\n\t\t\t                <div class=\"content\">\n\t\t\t                    ";
            $accounts = $export->getSettings("ledgerAccounts");
            $accountNames = $export->getConfig("ledgerAccounts");
            echo "\t\t\t                    <table border=\"0\" cellpadding=\"0\" cellspacing=\"3\" style=\"width: 100%\">\n\t\t\t                        <thead>\n\t\t\t                        <tr>\n\t\t\t                            <td style=\"width: 60%;\">&nbsp;</td>\n\t\t\t                            <td style=\"text-align: center;\"><strong>";
            echo $package_information["name"];
            echo " kenmerk</strong></td>\n\t\t\t                        </tr>\n\t\t\t                        </thead>\n\t\t\t                        <tbody>\n\t\t\t                        ";
            foreach ($accountNames["default"] as $identifier => $item) {
                if($item == "-") {
                    echo "\t\t\t                                <tr>\n\t\t\t                                    <td colspan=\"2\">&nbsp;</td>\n\t\t\t                                </tr>\n\t\t\t                                ";
                } else {
                    echo "\t\t\t                                <tr>\n\t\t\t                                    <td><strong>";
                    echo $item["name"];
                    echo "</strong></td>\n\t\t\t                                    <td style=\"text-align: center;\"><input type=\"text\" class=\"text1 size6\" name=\"accounts[";
                    echo $identifier;
                    echo "]\" value=\"";
                    echo $accounts["value"]["default"][$identifier]["id"];
                    echo "\" style=\"text-align: center;\" /></td>\n\t\t\t                                </tr>\n\t\t\t                                ";
                }
            }
            echo "\t\t\t                        </tbody>\n\t\t\t                    </table>\n\t\t\t                </div>\n\t\t\t            </div>\n\t\t\t        </div>\n\t\t\t        <div class=\"right\">\n\t\t\t            <div class=\"setting_help_box\">\n\t\t\t                <strong>Grootboekrekeningen</strong><br/>\n\t\t\t                Geef bij iedere rekening op welk kenmerk hier binnen uw ";
            echo $package_information["name"];
            echo " administratie voor wordt gebruikt.\n\t\t\t            </div>\n\t\t\t        </div>\n\t\t\t    </div>\n\t\t\t    \n\t\t\t    ";
            $export->showCustomGroups();
        }
        echo "\t\t    <br/>\n\t\t    <p class=\"align_right\">\n\t\t        <a class=\"button1 alt1\" id=\"form_save_btn\"><span>Instellingen opslaan</span></a>\n\t\t    </p>\n\t\t    </form>\n\t\t</div>\n\t\t";
    }
    echo "\t</div>\n\t<br/>\n\t\n\t";
}
echo "\n\t";
if(checkRight(U_EXPORT_EDIT, false)) {
    echo "\t\t<div id=\"stop_accounting_package\" class=\"hide\" title=\"";
    echo sprintf(__("export accounting - end dialog title"), $package_information["name"]);
    echo "\">\n\t\t\t<form id=\"AccountingPackageForm\" name=\"form_delete\" method=\"post\" action=\"export.php?page=accounting_package&amp;action=end_module&amp;module=";
    echo $package;
    echo "\">\n\n\t\t\t\t";
    echo sprintf(__("export accounting - end dialog intro"), $package_information["name"]);
    echo "\t\t\t\t<br/><br/>\n\t\t\t\t<strong>";
    echo __("export accounting - end dialog reason");
    echo "</strong><br/>\n\t\t\t\t<textarea name=\"message\" class=\"text1\" style=\"width:400px;height:75px;\"></textarea><br/>\n\t\t\t\t<br/>\n\t\t\t\t<label><input type=\"checkbox\" name=\"imsure\" value=\"yes\"/>\n\t\t\t\t\t";
    if($export->getLicenseStatus() == "TRIAL") {
        echo sprintf(__("export accounting - end dialog trial"), $package_information["name"]);
    } else {
        echo sprintf(__("export accounting - end dialog subscription"), $package_information["name"]);
    }
    echo "\t\t\t\t</label><br/>\n\t\t\t\t<br/>\n\t\t\t\t<p><a id=\"stop_accounting_package_btn\" class=\"button2 alt1 float_left\"><span>";
    echo __("export accounting - end dialog btn");
    echo "</span></a></p>\n\t\t\t\t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#stop_accounting_package').dialog('close');\"><span>";
    echo __("cancel");
    echo "</span></a></p>\n\t\t\t</form>\n\t\t</div>\n\t\t";
}
echo "\t\n\t<script type=\"text/javascript\">\n\t\$(document).ready(function() {\n\t    var customAccounts = 0;\n\t    \$('div[id=\"customLedgerTable\"], div[id=\"purchaseLedgerTable\"]').hide();\n\t    \n\t    \$('.filter_checkbox').click(function() {\n\t        \$(this).parent().parent().next('div').toggle();\n\t    });\n\t    \$('.btnShowAdvancedFilters').click(function() {\n\t        \$(this).prev('div').toggle();\n\t        if(\$(this).prev('div').css('display') == 'none')\n\t            \$(this).html('";
echo __("export accounting - show extended filters");
echo "');\n\t        else\n\t            \$(this).html('";
echo __("export accounting - hide extended filters");
echo "');\n\t    }).prev('div').hide();\n\t\n\t\t// On click radiobuttons\n\t    \$('input[name=\"type\"]').click(function() {\n\t    \t// Hide all other filters\n\t    \t\$('div[id|=\"div_filters\"]').hide();\n\t    \t\n\t    \t// Show export button\n\t    \t\$('#form_export_btn').removeClass('hide');\n\t    \t\n\t    \t// Update export button\n\t    \t\$('#form_export_btn span').html('";
echo __("export accounting - export");
echo " ' + \$(this).parent().find('strong').html().toLowerCase());\n\t    \t\n\t    \t// show/hide filter\n\t        if(\$('#div_filters-'+ \$(this).val()).html()){\n\t        \t\$('#div_filters-'+ \$(this).val()).show();\n\t        \t\$('#div_filters').show();\n\t        }else{\n\t        \t\$('#div_filters').hide();\n\t        }\n\t    });\n\t\n\t    \$('input[name=\"toggleCustomLedgerTable\"]').change(function() {\n\t        \$('#customLedgerTable').toggle();\n\t    });\n\t    \$('input[name=\"togglePurchaseLedgerTable\"]').change(function() {\n    \t\t\$('#purchaseLedgerTable').toggle();\n\t\t});\n\t\n\t    \$('#form_export_btn, #form_save_btn').click(function() { \$(this).closest('form').submit(); });\n\t\t\n\t    \$('#addLedgerAccount').click(function() {\n\t        customAccounts++;\n\t        \$(this).prev('table').contents('tbody').append('<tr><td><input type=\"text\" class=\"text1 size32\" name=\"customAccount['+ customAccounts +']\" value=\"\"></td><td><input type=\"text\" class=\"text1 size6\" name=\"customAccountValue['+ customAccounts +']\" value=\"\"></td></tr>');\n\t    });\n\t\n\t    \$('input[name^=\"group\"], select[name^=\"group\"]').each(function(i, v) {\n\t        if(\$(this).val() > 0)\n\t        {\n\t            \$('input[name=\"toggleCustomLedgerTable\"]').prop('checked', true);\n\t            \$('#customLedgerTable').show();\n\t        }\n\t    });\n\t    \n\t    \$('input[name^=\"purchase_group\"], select[name^=\"purchase_group\"]').each(function(i, v) {\n\t        if(\$(this).val() > 0)\n\t        {\n\t            \$('input[name=\"togglePurchaseLedgerTable\"]').prop('checked', true);\n\t            \$('#purchaseLedgerTable').show();\n\t        }\n\t    });\n\t    \n\t    \t\$('#stop_accounting_package').dialog({modal: true, autoOpen: ";
if(isset($show_stop_dialog) && $show_stop_dialog === true) {
    echo "true";
} else {
    echo "false";
}
echo ", resizable: false, width: 450, height: 'auto'});\n\t\t\$('input[name=\"imsure\"]').click(function(){\n\t\t\tif(\$('input[name=\"imsure\"]:checked').val() != null)\n\t\t\t{\n\t\t\t\t\$('#stop_accounting_package_btn').removeClass('button2').addClass('button1');\n\t\t\t}\n\t\t\telse\n\t\t\t{\n\t\t\t\t\$('#stop_accounting_package_btn').removeClass('button1').addClass('button2');\n\t\t\t}\n\t\t});\n\t\t\$('#stop_accounting_package_btn').click(function(){\n\t\t\tif(\$('input[name=\"imsure\"]:checked').val() != null)\n\t\t\t{\n\t\t\t\tdocument.form_delete.submit();\n\t\t\t}\t\n\t\t});\n\n\t});\n\t</script>\n\t\n\t<style type=\"text/css\">\n\tspan.export_history { display:block;padding:2px 0 15px 30px; line-height:16px; }\n\tinput.export_radio { margin-right: 5px; }\n\t</style>\n\t\n";
require_once "views/footer.php";

?>