<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
header("Content-type: text/html; charset=utf-8");
$postDataBeforeCSRFCheck = $_POST ?? [];
require_once "config.php";
if($postDataBeforeCSRFCheck !== [] && $_POST === []) {
    echo json_encode(["ajaxResponse" => "csrf", "csrfToken" => CSRF_Model::getToken(true)], JSON_THROW_ON_ERROR);
    exit;
}
if(isset($_POST["action"]) && $_POST["action"] == "autocomplete_search") {
    $response_array = [];
    $item_ids = [];
    switch ($_POST["search_type"]) {
        case "debtor":
            require_once "class/debtor.php";
            $debtor = new debtor();
            $fields = ["id", "DebtorCode", "CompanyName", "Initials", "SurName"];
            $limit = -1;
            $searchfor = isset($_POST["search_for"]) ? $_POST["search_for"] : "";
            $debtor_list = $debtor->all($fields, "DebtorCode", "ASC", -1, "DebtorCode|CompanyName|SurName|Initials", $searchfor);
            $response_array["search_type"] = "debtor";
            $response_array["search_results"] = [];
            $exclude_debtor = false;
            if(isset($_POST["filter"]) && 0 < strlen($_POST["filter"]) && substr($_POST["filter"], 0, 15) == "exclude_debtor.") {
                $exclude_debtor = substr($_POST["filter"], 15);
            }
            foreach ($debtor_list as $_k => $_debtor) {
                if(!is_numeric($_k) || $exclude_debtor && $exclude_debtor == $_debtor["id"]) {
                } else {
                    $item_ids[] = (int) $_debtor["id"];
                    $response_array["search_results"][] = ["id" => $_debtor["id"], "code" => htmlspecialchars_decode($_debtor["DebtorCode"]), "title" => htmlspecialchars_decode($_debtor["CompanyName"] ? $_debtor["CompanyName"] : $_debtor["SurName"] . ", " . $_debtor["Initials"]), "searchable" => mb_strtolower(htmlspecialchars_decode($_debtor["DebtorCode"]) . "|" . htmlspecialchars_decode($_debtor["CompanyName"]) . "|" . htmlspecialchars_decode($_debtor["SurName"]) . "|" . htmlspecialchars_decode($_debtor["Initials"]))];
                }
            }
            require_once "class/group.php";
            $group = new group("debtor");
            $group_list = $group->all(["GroupName", "Debtors"]);
            $group_key = "Debtors";
            break;
        case "creditor":
            require_once "class/creditor.php";
            $creditor = new creditor();
            $fields = ["id", "CreditorCode", "CompanyName", "Initials", "SurName"];
            $limit = -1;
            $searchfor = isset($_POST["search_for"]) ? $_POST["search_for"] : "";
            $creditor_list = $creditor->all($fields, "CreditorCode", "ASC", -1, "CreditorCode|CompanyName|SurName|Initials", $searchfor);
            $response_array["search_type"] = "creditor";
            $response_array["search_results"] = [];
            foreach ($creditor_list as $_k => $_creditor) {
                if(!is_numeric($_k)) {
                } else {
                    $item_ids[] = (int) $_creditor["id"];
                    $response_array["search_results"][] = ["id" => $_creditor["id"], "code" => htmlspecialchars_decode($_creditor["CreditorCode"]), "title" => htmlspecialchars_decode($_creditor["CompanyName"] ? $_creditor["CompanyName"] : $_creditor["SurName"] . ", " . $_creditor["Initials"]), "searchable" => mb_strtolower(htmlspecialchars_decode($_creditor["CreditorCode"]) . "|" . htmlspecialchars_decode($_creditor["CompanyName"]) . "|" . htmlspecialchars_decode($_creditor["SurName"]) . "|" . htmlspecialchars_decode($_creditor["Initials"]))];
                }
            }
            require_once "class/group.php";
            $group = new group("creditor");
            $group_list = $group->all(["GroupName", "Creditors"]);
            $group_key = "Creditors";
            break;
        case "product":
            require_once "class/product.php";
            $product = new product();
            $fields = ["id", "ProductCode", "ProductName", "ProductKeyPhrase", "PricePeriod", "PriceExcl"];
            $limit = -1;
            $searchfor = isset($_POST["search_for"]) ? $_POST["search_for"] : "";
            if(isset($_POST["filter"]) && 0 < strlen($_POST["filter"]) && $_POST["filter"] != "undefined") {
                $product->OnlyProductType = strpos($_POST["filter"], "|") !== false ? explode("|", $_POST["filter"]) : [$_POST["filter"]];
            }
            $product_list = $product->all($fields, "ProductCode", "ASC", -1, "ProductCode|ProductName|ProductKeyPhrase", $searchfor);
            $response_array["search_type"] = "product";
            $response_array["search_results"] = [];
            foreach ($product_list as $_k => $_product) {
                if(!is_numeric($_k)) {
                } else {
                    $item_ids[] = (int) $_product["id"];
                    $response_array["search_results"][] = ["id" => $_product["id"], "code" => htmlspecialchars_decode($_product["ProductCode"]), "title" => htmlspecialchars_decode($_product["ProductName"]), "priceperiod" => $_product["PricePeriod"] == "" ? "&nbsp;" : "&nbsp;p/" . $_product["PricePeriod"], "priceexcl" => money($_product["PriceExcl"], false), "priceincl" => money($_product["PriceIncl"], false), "searchable" => mb_strtolower(htmlspecialchars_decode($_product["ProductCode"]) . "|" . htmlspecialchars_decode($_product["ProductName"]) . "|" . htmlspecialchars_decode($_product["ProductKeyPhrase"]))];
                }
            }
            require_once "class/group.php";
            $group = new group("product");
            $group_list = $group->all(["GroupName", "Products"]);
            $group_key = "Products";
            break;
        default:
            $item_ids_not_in_any_group = $item_ids;
            foreach ($group_list as $_k => $_group) {
                if(!is_numeric($_k)) {
                } else {
                    $_group_item_ids = array_intersect(array_keys($_group[$group_key]), $item_ids);
                    sort($_group_item_ids);
                    $item_ids_not_in_any_group = array_diff($item_ids_not_in_any_group, array_keys($_group[$group_key]));
                    if($searchfor == "" || !empty($_group_item_ids)) {
                        $response_array["search_groups"][] = ["id" => $_group["id"], "title" => htmlspecialchars_decode($_group["GroupName"]), "children" => $_group_item_ids];
                    }
                }
            }
            if(!empty($item_ids_not_in_any_group)) {
                sort($item_ids_not_in_any_group);
                $response_array["search_groups"][] = ["id" => 0, "title" => __("autocomplete items without a group"), "children" => $item_ids_not_in_any_group];
            }
            $json = json_encode($response_array);
            if($json === false && json_last_error() == JSON_ERROR_UTF8) {
                $json = json_encode($response_array, JSON_PARTIAL_OUTPUT_ON_ERROR);
            }
            echo $json;
            exit;
    }
} elseif(isset($_POST["action"]) && $_POST["action"] == "autocomplete_save_setting") {
    if(isset($_POST["type"]) && in_array($_POST["value"], ["yes", "no"]) && defined("SHOW_" . strtoupper($_POST["type"]) . "_SEARCH_GROUPS")) {
        require_once "class/settings.php";
        $setting = new settings();
        $setting->Variable = "SHOW_" . strtoupper($_POST["type"]) . "_SEARCH_GROUPS";
        $setting->Value = $_POST["value"] == "yes" ? "yes" : "no";
        $setting->edit();
    }
} else {
    if(isset($_POST["action"]) && $_POST["action"] == "generate_new_api_key") {
        echo md5($_SESSION["UserNamePro"] . time() . microtime(true));
        exit;
    }
    if(isset($_POST["action"]) && $_POST["action"] == "set_stats_tab") {
        $tabArray = ["overview" => 1, "revenue" => 2, "expense" => 3, "25" => 4];
        if(array_key_exists($_POST["tab"], $tabArray)) {
            if($_POST["tab"] == "25") {
                $translate = "debtor_25";
            } else {
                $translate = $tabArray[$_POST["tab"]];
            }
            $_SESSION["statstab_selected"] = "tab-" . $translate;
        }
        exit;
    }
    if(isset($_POST["action"]) && $_POST["action"] == "show_api_logrow") {
        require_once "class/apilogfile.php";
        $_id = esc($_POST["id"]);
        $apilogfile = new apilogfile();
        $apilogfile->Identifier = $_id;
        if($apilogfile->show()) {
            echo json_encode(["input" => normalize(json_decode($apilogfile->Input)), "output" => normalize(json_decode($apilogfile->Response)), "type" => $apilogfile->ResponseType]);
        }
        exit;
    }
    if(isset($_POST["action"]) && $_POST["action"] == "debtor_openamount") {
        require_once "class/debtor.php";
        $_id = esc($_POST["id"]);
        $debtor = new debtor();
        $debtor->Identifier = $_id;
        $debtor->getOpenAmount();
        echo $debtor->OpenAmountIncl;
        exit;
    }
    if(isset($_POST["action"]) && $_POST["action"] == "debtor_financial_information") {
        require_once "class/debtor.php";
        $_id = esc($_POST["id"]);
        $debtor = new debtor();
        $debtor->Identifier = $_id;
        $debtor->getFinancialInfo($_POST["variable"]);
        if($_POST["variable"] == "total") {
            $getFinancialInfo = [];
            $getFinancialInfo["TotalAmountExcl"] = money(deformat_money($debtor->TotalAmountExcl));
            $getFinancialInfo["TurnoverThisYear"] = money(is_numeric($debtor->TurnoverThisYear) ? $debtor->TurnoverThisYear : 0);
            $getFinancialInfo["TurnoverLastYear"] = !isEmptyFloat($debtor->TurnoverLastYear) ? money($debtor->TurnoverLastYear) . " " . __("last year") : "";
            echo json_encode($getFinancialInfo);
            exit;
        }
        if($_POST["variable"] == "AverageOutstandingDays") {
            echo $debtor->AverageOutstandingDays;
            exit;
        }
        exit;
    }
    if(isset($_POST["action"]) && $_POST["action"] == "invoice_get_debtor") {
        if(0 < $_POST["debtor"]) {
            require_once "class/company.php";
            require_once "class/debtor.php";
            $company = new company();
            $company->show();
            $debtor = new debtor();
            $debtor->Identifier = esc($_POST["debtor"]);
            if($debtor->show()) {
                foreach ($debtor->Variables as $k) {
                    $debtor->{$k} = !is_array($debtor->{$k}) ? htmlspecialchars_decode($debtor->{$k}) : $debtor->{$k};
                }
                $useAbnormalInvoiceData = isset($_POST["type"]) && $_POST["type"] == "pricequote" && $debtor->InvoiceDataForPriceQuote != "yes" ? false : true;
                $data = [];
                $data["CompanyName"] = $debtor->InvoiceCompanyName && $useAbnormalInvoiceData ? $debtor->InvoiceCompanyName : $debtor->CompanyName;
                $data["DebtorCode"] = $debtor->DebtorCode;
                $data["TaxNumber"] = $debtor->TaxNumber;
                $data["Initials"] = $debtor->InvoiceInitials && $useAbnormalInvoiceData ? $debtor->InvoiceInitials : $debtor->Initials;
                $data["Sex"] = $debtor->InvoiceSurName && $useAbnormalInvoiceData ? $debtor->InvoiceSex : $debtor->Sex;
                $data["SurName"] = $debtor->InvoiceSurName && $useAbnormalInvoiceData ? $debtor->InvoiceSurName : $debtor->SurName;
                $data["Address"] = $debtor->InvoiceAddress && $useAbnormalInvoiceData ? $debtor->InvoiceAddress : $debtor->Address;
                $data["Address2"] = $debtor->InvoiceAddress2 && $useAbnormalInvoiceData ? $debtor->InvoiceAddress2 : $debtor->Address2;
                $data["ZipCode"] = $debtor->InvoiceZipCode && $useAbnormalInvoiceData ? $debtor->InvoiceZipCode : $debtor->ZipCode;
                $data["City"] = $debtor->InvoiceCity && $useAbnormalInvoiceData ? $debtor->InvoiceCity : $debtor->City;
                $data["State"] = $debtor->InvoiceState && $useAbnormalInvoiceData ? $debtor->InvoiceState : $debtor->State;
                $data["StateName"] = $debtor->InvoiceStateName && $useAbnormalInvoiceData ? $debtor->InvoiceStateName : $debtor->StateName;
                $data["Country"] = $debtor->InvoiceCountry && $debtor->InvoiceAddress && $useAbnormalInvoiceData ? $debtor->InvoiceCountry : $debtor->Country;
                $data["PeriodicInvoiceDays"] = $debtor->PeriodicInvoiceDays;
                $data["InvoiceCollectionDays"] = $debtor->InvoiceCollect;
                $data["EmailAddress"] = $debtor->InvoiceEmailAddress && $useAbnormalInvoiceData ? $debtor->InvoiceEmailAddress : $debtor->EmailAddress;
                $data["InvoiceTemplate"] = $debtor->InvoiceTemplate;
                $data["InvoiceMethod"] = $debtor->InvoiceMethod;
                $data["InvoiceAuthorisation"] = $debtor->InvoiceAuthorisation;
                $data["Taxable"] = $debtor->Taxable ? "true" : "false";
                $data["TaxRate1"] = isset($debtor->TaxRate1) && !is_null($debtor->TaxRate1) ? (double) $debtor->TaxRate1 : "";
                $data["TaxRate2"] = isset($debtor->TaxRate2) && !is_null($debtor->TaxRate2) ? (double) $debtor->TaxRate2 : "";
                $data["Term"] = $debtor->InvoiceTerm == NULL ? INVOICE_TERM : $debtor->InvoiceTerm;
                $data["Server"] = $debtor->Server;
                $data["CountryLong"] = $array_country[$data["Country"]];
                $data["InvoiceMethodLong"] = strtolower($array_invoicemethod[$debtor->InvoiceMethod]);
                if(isset($debtor->custom) && isset($debtor->customvalues)) {
                    $data["Custom"] = $debtor->custom;
                    $data["CustomFields"] = $debtor->customvalues;
                }
                if(isset($_POST["return_nameservers"]) && $_POST["return_nameservers"] == "true" && ($debtor->DNS1 || $debtor->DNS2 || $debtor->DNS3)) {
                    $data["DNS1"] = $debtor->DNS1 ? $debtor->DNS1 : "";
                    $data["DNS2"] = $debtor->DNS2 ? $debtor->DNS2 : "";
                    $data["DNS3"] = $debtor->DNS3 ? $debtor->DNS3 : "";
                }
                echo json_encode($data);
                exit;
            }
        }
    } elseif(isset($_POST["action"]) && $_POST["action"] == "get_debtor") {
        if(0 < $_POST["debtor"]) {
            require_once "class/company.php";
            require_once "class/debtor.php";
            $company = new company();
            $company->show();
            $debtor = new debtor();
            $debtor->Identifier = esc($_POST["debtor"]);
            if($debtor->show()) {
                foreach ($debtor->Variables as $k) {
                    $debtor->{$k} = !is_array($debtor->{$k}) ? htmlspecialchars_decode($debtor->{$k}) : $debtor->{$k};
                }
                $data = [];
                $data["CompanyName"] = $debtor->CompanyName;
                $data["DebtorCode"] = $debtor->DebtorCode;
                $data["TaxNumber"] = $debtor->TaxNumber;
                $data["Initials"] = $debtor->Initials;
                $data["SurName"] = $debtor->SurName;
                $data["Address"] = $debtor->Address;
                $data["ZipCode"] = $debtor->ZipCode;
                $data["City"] = $debtor->City;
                $data["Country"] = $debtor->Country;
                $data["EmailAddress"] = $debtor->EmailAddress;
                $data["PhoneNumber"] = $debtor->PhoneNumber;
                $data["MobileNumber"] = $debtor->MobileNumber;
                $data["CountryLong"] = $array_country[$data["Country"]];
                if(isset($_POST["return_pushdata"]) && $_POST["return_pushdata"] == "true") {
                    $data["PushData"] = $debtor->getPushData();
                }
                echo json_encode($data);
                exit;
            }
        }
    } elseif(isset($_GET["action"]) && $_GET["action"] == "updatesearchgroupbysetting") {
        if(isset($_POST["SHOW_PRODUCT_SEARCH_GROUPS"]) && in_array($_POST["SHOW_PRODUCT_SEARCH_GROUPS"], ["yes", "no"])) {
            require_once "class/settings.php";
            $setting = new settings();
            $setting->Variable = "SHOW_PRODUCT_SEARCH_GROUPS";
            $setting->Value = $_POST["SHOW_PRODUCT_SEARCH_GROUPS"] == "yes" ? "yes" : "no";
            $setting->edit();
        }
    } else {
        if(isset($_GET["action"]) && $_GET["action"] == "searchdomain") {
            require_once "class/domain.php";
            $domain = new domain();
            $session = isset($_SESSION["search.domain"]) ? $_SESSION["search.domain"] : [];
            $fields = ["Domain", "Tld", "Status"];
            $sort = isset($session["sort"]) ? $session["sort"] : "Domain";
            $order = isset($session["order"]) ? $session["order"] : "ASC";
            $limit = isset($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : "1";
            $searchat = "Debtor";
            $searchfor = isset($_GET["id"]) && esc($_GET["id"]) ? intval(esc($_GET["id"])) : (isset($session["searchfor"]) && $session["searchfor"] ? $session["searchfor"] : 0);
            $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : min(10, MAX_RESULTS_LIST);
            $domains = $domain->all($fields, $sort, $order, $limit, $searchat, $searchfor, "1|3|4|5|6|7|8", $show_results);
            $_SESSION["search.domain"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "searchfor" => $searchfor];
            $results_per_page = $show_results;
            $current_page = $limit;
            $current_page_url = "XMLRequest.php?action=searchdomain";
            require_once "views/dialog.search.domain.php";
            exit;
        }
        if(isset($_GET["action"]) && $_GET["action"] == "searchhosting") {
            require_once "class/hosting.php";
            $hosting = new hosting();
            $session = isset($_SESSION["search.hosting"]) ? $_SESSION["search.hosting"] : [];
            $fields = ["Username", "Domain", "Status"];
            $sort = isset($session["sort"]) ? $session["sort"] : "Username";
            $order = isset($session["order"]) ? $session["order"] : "ASC";
            $limit = isset($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : "1";
            $searchat = "Debtor";
            $searchfor = isset($_GET["id"]) && esc($_GET["id"]) ? intval(esc($_GET["id"])) : (isset($session["searchfor"]) && $session["searchfor"] ? $session["searchfor"] : 0);
            $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : min(10, MAX_RESULTS_LIST);
            $hosting_list = $hosting->all($fields, $sort, $order, $limit, $searchat, $searchfor, "1|3|4|5|7", $show_results);
            $_SESSION["search.hosting"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "searchfor" => $searchfor];
            $results_per_page = $show_results;
            $current_page = $limit;
            $current_page_url = "XMLRequest.php?action=searchhosting";
            require_once "views/dialog.search.hosting.php";
            exit;
        }
        if(isset($_GET["action"]) && $_GET["action"] == "searchhandle") {
            require_once "class/handle.php";
            $handle = new handle();
            $session = isset($_SESSION["search.handle"]) ? $_SESSION["search.handle"] : [];
            $handle->debtor_id = isset($_GET["debtor_id"]) ? esc($_GET["debtor_id"]) : (isset($session["debtor_id"]) ? $session["debtor_id"] : 0);
            $handle->registrar_id = isset($_GET["registrar_id"]) ? esc($_GET["registrar_id"]) : (isset($session["registrar_id"]) ? $session["registrar_id"] : 0);
            $fields = ["Handle", "Registrar", "Name", "RegistrarHandle", "Debtor", "DebtorCode", "CompanyName", "Initials", "SurName"];
            $sort = isset($session["sort"]) ? $session["sort"] : "Handle";
            $order = isset($session["order"]) ? $session["order"] : "ASC";
            $limit = isset($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : "1";
            $searchat = "Handle|CompanyName|SurName|Initials";
            $searchfor = isset($session["searchfor"]) && $session["searchfor"] ? $session["searchfor"] : "";
            $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : min(10, MAX_RESULTS_LIST);
            $list_handles = $handle->all($fields, $sort, $order, $limit, $searchat, $searchfor, "1|3|4|5|7", $show_results);
            $_SESSION["search.handle"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "searchfor" => $searchfor, "debtor_id" => $handle->debtor_id, "registrar_id" => $handle->registrar_id];
            $results_per_page = $show_results;
            $current_page = $limit;
            $current_page_url = "XMLRequest.php?action=searchhandle";
            require_once "views/dialog.search.handle.php";
            exit;
        }
        if(isset($_GET["action"]) && $_GET["action"] == "is_domain_in_software") {
            require_once "class/domain.php";
            $domain = new domain();
            $tmp_domain = explode(".", esc($_GET["domain"]), 2);
            if($domain->is_free($tmp_domain[0], isset($tmp_domain[1]) ? $tmp_domain[1] : "")) {
                echo "no";
            } else {
                echo "yes";
            }
            exit;
        }
        if(isset($_GET["action"]) && $_GET["action"] == "addhandle") {
            require_once "class/handle.php";
            require_once "class/debtor.php";
            $debtor = new debtor();
            if(isset($_GET["debtor_id"]) && $_GET["debtor_id"]) {
                $debtor->show(esc($_GET["debtor_id"]));
            }
            $handle = new handle();
            require_once "class/registrar.php";
            $registrar = new registrar();
            $list_domain_registrars = $registrar->all(["Name"]);
            require_once "views/dialog.handle.add.php";
            exit;
        }
        if(isset($_GET["action"]) && $_GET["action"] == "add_default_handle") {
            require_once "class/handle.php";
            $handle = new handle();
            $registrar_id = isset($_POST["registrar_id"]) ? intval(esc($_POST["registrar_id"])) : 0;
            $fields = ["Handle", "Name", "RegistrarHandle"];
            $list_registrar_handles_all = $handle->all($fields, "Handle", "ASC", -1, "Registrar", $registrar_id);
            $from_page = isset($_POST["from_page"]) && $_POST["from_page"] == "add" ? "add" : "show";
            require_once "views/dialog.handle.default.add.php";
            exit;
        }
        if(isset($_POST["action"]) && $_POST["action"] == "get_product") {
            if(isset($_POST["product"]) || isset($_POST["productcode"])) {
                require_once "class/product.php";
                $product = new product();
                if(isset($_POST["line"]) && $_POST["line"] == "subscription") {
                    $product->Identifier = esc($_POST["productcode"]);
                } elseif(isset($_POST["product"])) {
                    $product->Identifier = esc($_POST["product"]);
                } else {
                    $product->ProductCode = esc($_POST["productcode"]);
                }
                if($product->show()) {
                    if(isset($_POST["TaxDebtor"]) && 0 < $_POST["TaxDebtor"]) {
                        $product->TaxPercentage = btwcheck(esc($_POST["TaxDebtor"]), $product->TaxPercentage);
                    }
                    $data = [];
                    $data["PricePeriod"] = $product->PricePeriod;
                    $data["ProductCode"] = $product->ProductCode;
                    $data["Description"] = html_entity_decode($product->ProductKeyPhrase);
                    $data["NumberSuffix"] = html_entity_decode($product->NumberSuffix);
                    $data["PriceExcl"] = money($product->PriceExcl, false, false);
                    $data["PriceIncl"] = money(round($product->PriceExcl * (1 + $product->TaxPercentage), 5), false, false);
                    $data["TaxPercentage"] = isEmptyFloat($product->TaxPercentage) ? number_format($product->TaxPercentage, 2, ".", "") : $product->TaxPercentage;
                    $data["PricePeriodLong"] = $array_periodic[$data["PricePeriod"]];
                    $data["TaxPercentageLong"] = $data["TaxPercentage"] == "0.00" ? $array_taxpercentages[0] : $array_taxpercentages[$data["TaxPercentage"]];
                    $data["HasCustomPrice"] = $product->HasCustomPrice;
                    if($product->HasCustomPrice == "period" && ($custom_prices = $product->listCustomProductPrices())) {
                        $period_div = "<strong>" . __("this product has custom period prices") . "</strong><br />";
                        $period_div .= "&bull; <a class=\"a1 c1\" onclick=\"selectCustomPeriodPrice(this, '1', '" . $product->PricePeriod . "');\">" . "1 " . $array_periodic[$product->PricePeriod] . " (" . (CURRENCY_SIGN_LEFT ? CURRENCY_SIGN_LEFT . " " : "") . "<span class=\"custom_price_excl\">" . money($product->PriceExcl, false, false) . "</span><span class=\"custom_price_incl\">" . money($product->PriceIncl, false, false) . "</span>" . (CURRENCY_SIGN_RIGHT ? " " . CURRENCY_SIGN_RIGHT : "") . " " . __("per") . " " . $array_periodic[$product->PricePeriod] . ")</a><br />";
                        foreach ($custom_prices["period"] as $k => $tmp_period) {
                            if($k != "default") {
                                $k_exp = explode("-", $k);
                                $period_div .= "&bull; <a class=\"a1 c1\" onclick=\"selectCustomPeriodPrice(this, '" . $k_exp[0] . "', '" . $k_exp[1] . "');\">" . $k_exp[0] . " " . $array_periodic[$k_exp[1]] . " (" . (CURRENCY_SIGN_LEFT ? CURRENCY_SIGN_LEFT . " " : "") . "<span class=\"custom_price_excl\">" . money($tmp_period["PriceExcl"], false, false) . "</span><span class=\"custom_price_incl\">" . money($tmp_period["PriceIncl"], false, false) . "</span>" . (CURRENCY_SIGN_RIGHT ? " " . CURRENCY_SIGN_RIGHT : "") . " " . __("per") . " " . $array_periodic[$k_exp[1]] . ")</a><br />";
                            }
                        }
                        $period_div .= "<div style=\"margin-top:5px;\">" . __("other periods will use the default price") . " " . (CURRENCY_SIGN_LEFT ? CURRENCY_SIGN_LEFT . " " : "") . "<span class=\"custom_price_excl\">" . money($product->PriceExcl, false, false) . "</span><span class=\"custom_price_incl\">" . money($product->PriceIncl, false, false) . "</span>" . (CURRENCY_SIGN_RIGHT ? " " . CURRENCY_SIGN_RIGHT : "") . " " . __("per") . " " . $array_periodic[$product->PricePeriod] . ".</div>";
                        $custom_prices["periodHTML"] = $period_div;
                        $data["CustomPrices"] = json_encode($custom_prices);
                    }
                    if(isset($_POST["line"])) {
                        $data["LineID"] = esc($_POST["line"]);
                    }
                    echo json_encode($data);
                    exit;
                }
            }
            $data = [];
            $data["PricePeriodLong"] = strtolower(__("period"));
            $data["TaxPercentageLong"] = $array_taxpercentages[STANDARD_TAX];
            echo json_encode($data);
            exit;
        }
        if(isset($_POST["action"]) && $_POST["action"] == "get_product_by_tld") {
            if(isset($_POST["tld"])) {
                require_once "class/topleveldomain.php";
                $tld = new topleveldomain();
                $post_tld = substr(esc($_POST["tld"]), 0, 1) == "." ? substr(esc($_POST["tld"]), 1) : esc($_POST["tld"]);
                if($tld->showbyTLD($post_tld)) {
                    $price_ownerchange = VAT_CALC_METHOD == "incl" ? money($tld->PriceIncl) . " " . __("incl vat") : money($tld->PriceExcl) . (!empty($array_taxpercentages) ? " " . __("excl vat") : "");
                    $data = [];
                    $data["Tld"] = $tld->Tld;
                    $data["OwnerChangeCost"] = $tld->OwnerChangeCost;
                    $data["OwnerChangeCostProductCode"] = $tld->ProductCode;
                    $data["OwnerChangeCostProductName"] = $tld->ProductName;
                    $data["OwnerChangeCostPriceExcl"] = $tld->PriceExcl;
                    $data["OwnerChangeCostPriceExclF"] = $price_ownerchange;
                    $data["Registrar"] = $tld->Registrar;
                    $data["RegistrarName"] = $tld->Name;
                    require_once "class/product.php";
                    $product = new product();
                    if($product->show_by_tld($post_tld)) {
                        $data["ProductID"] = $product->Identifier;
                        $data["PricePeriod"] = $product->PricePeriod;
                        $data["ProductCode"] = $product->ProductCode;
                        $data["Description"] = html_entity_decode($product->ProductKeyPhrase);
                        $data["Name"] = html_entity_decode($product->ProductName);
                        $data["PriceExcl"] = $product->PriceExcl;
                        $data["TaxPercentage"] = isEmptyFloat($product->TaxPercentage) ? number_format($product->TaxPercentage, 2, ".", "") : $product->TaxPercentage;
                        $data["PricePeriodLong"] = $array_periodic[$data["PricePeriod"]];
                        $data["TaxPercentageLong"] = $array_taxpercentages[$data["TaxPercentage"]];
                    }
                    echo json_encode($data);
                    exit;
                }
                echo "{ \"errorSet\": [";
                echo json_encode(["Message" => "<i>" . __("tld not found") . "</i>"]);
                echo "] }";
                exit;
            }
            echo "{ \"errorSet\": [";
            echo json_encode(["Message" => __("no product found")]);
            echo "] }";
            exit;
        }
        if(isset($_POST["action"]) && $_POST["action"] == "get_public_whois_for_tld") {
            if(isset($_POST["tld"])) {
                require_once "class/topleveldomain.php";
                $topleveldomain = new topleveldomain();
                $whois_list = $topleveldomain->getWhoisServers();
                $tld = esc($_POST["tld"]);
                if(substr($tld, 0, 1) == ".") {
                    $tld = substr($tld, 1);
                }
                if(isset($whois_list[$tld])) {
                    $data = [];
                    $data["Tld"] = $tld;
                    $data["WhoisServer"] = $whois_list[$tld]["server"];
                    $data["WhoisNoMatch"] = $whois_list[$tld]["nomatch"];
                    echo json_encode($data);
                    exit;
                }
            }
            echo "{ \"errorSet\": [";
            echo json_encode(["Message" => __("no public whois server found")]);
            echo "] }";
            exit;
        }
        if(isset($_POST["action"]) && $_POST["action"] == "get_idn_support_for_tld") {
            if(isset($_POST["tld"])) {
                require_once "class/topleveldomain.php";
                $topleveldomain = new topleveldomain();
                $idn_support_list = $topleveldomain->getIDNSupport();
                $tld = esc($_POST["tld"]);
                if(substr($tld, 0, 1) == ".") {
                    $tld = substr($tld, 1);
                }
                if(isset($idn_support_list[$tld])) {
                    echo "OK" . $idn_support_list[$tld];
                    exit;
                }
            }
            exit;
        }
        if(isset($_POST["action"]) && $_POST["action"] == "get_products_in_productgroup") {
            if(isset($_POST["group_id"])) {
                require_once "class/product.php";
                $product = new product();
                $fields = ["ProductName", "PriceExcl", "PricePeriod"];
                $product_list = $product->all($fields, "ProductCode", false, "-1", false, false, $_POST["group_id"]);
                $data = [];
                if(0 < $product_list["CountRows"]) {
                    foreach ($product_list as $k => $v) {
                        if(is_numeric($k)) {
                            $data[$v["id"]]["ProductID"] = $v["id"];
                            $data[$v["id"]]["ProductName"] = $v["ProductName"];
                            $data[$v["id"]]["PriceExcl"] = money($v["PriceExcl"], false);
                            $data[$v["id"]]["PricePeriod"] = $v["PricePeriod"] ? __("per") . " " . $array_periodes[$v["PricePeriod"]] : "";
                            $data[$v["id"]]["PriceLine"] = money($v["PriceExcl"]) . " " . __("per") . " " . $array_periodes[$v["PricePeriod"]];
                        }
                    }
                }
                echo json_encode($data);
                exit;
            } else {
                echo "{ \"errorSet\": [";
                echo json_encode(["Message" => __("invalid identifier for group")]);
                echo "] }";
                exit;
            }
        } else {
            if(isset($_POST["action"]) && $_POST["action"] == "get_product_packageid") {
                if(isset($_POST["id"])) {
                    require_once "class/product.php";
                    $product = new product();
                    if($product->show(esc($_POST["id"]))) {
                        $data = [];
                        $data["ProductType"] = $product->ProductType;
                        $data["PackageID"] = $product->PackageID;
                        echo json_encode($data);
                        exit;
                    }
                }
                echo "{ \"errorSet\": [";
                echo json_encode(["Message" => __("no product found")]);
                echo "] }";
                exit;
            }
            if(isset($_POST["action"]) && $_POST["action"] == "package_information") {
                require_once "class/package.php";
                $_id = esc($_POST["id"]);
                if(is_numeric($_id) && 0 < $_id) {
                    $package = new package();
                    $package->show($_id);
                    if(0 < $package->id) {
                        $package->DiscSpace = formatMB($package->DiscSpace);
                        $package->BandWidth = formatMB($package->BandWidth);
                        echo "{ \"resultSet\": [";
                        echo json_encode($package);
                        echo "] }";
                        exit;
                    }
                    echo "{ \"errorSet\": [";
                    echo json_encode(["Message" => "<i>" . __("no package selected") . "</i>"]);
                    echo "] }";
                    exit;
                }
                echo "{ \"errorSet\": [";
                echo json_encode(["Message" => "<i>" . __("no package selected") . "</i>"]);
                echo "] }";
                exit;
            }
            if(isset($_POST["action"]) && $_POST["action"] == "package_information_from_server") {
                require_once "class/server.php";
                $server = new server();
                $server->show(esc($_POST["server_id"]));
                $api = $server->connect();
                if(is_object($api)) {
                    if(esc($_POST["package_type"]) == "reseller") {
                        $api->getPackage(esc($_POST["template_name"]), true);
                    } else {
                        $api->getPackage(esc($_POST["template_name"]));
                    }
                    $result = [];
                    foreach ($api as $key => $value) {
                        if(substr($key, 0, 8) == "Package_") {
                            $result[substr($key, 8)] = $value;
                        }
                    }
                    $result["DiscSpace"] = is_numeric($result["DiscSpace"]) ? formatMB($result["DiscSpace"]) : $result["DiscSpace"];
                    $result["BandWidth"] = is_numeric($result["BandWidth"]) ? formatMB($result["BandWidth"]) : $result["BandWidth"];
                    echo "{ \"resultSet\": [";
                    echo json_encode($result);
                    echo "] }";
                    exit;
                } else {
                    echo "{ \"errorSet\": [";
                    echo json_encode(["Message" => sprintf(__("cant retrieve data from template name"), esc($_POST["template_name"]))]);
                    echo "] }";
                    exit;
                }
            } elseif(isset($_POST["action"]) && $_POST["action"] == "debtor_information") {
                require_once "class/debtor.php";
                $_id = esc($_POST["id"]);
                if(is_numeric($_id) && 0 < $_id) {
                    $debtor = new debtor();
                    $debtor->show($_id);
                    if(0 < $debtor->id) {
                        foreach ($debtor as $k => $v) {
                            if(is_string($v)) {
                                $debtor->{$k} = htmlspecialchars_decode($v);
                            }
                        }
                        if(isset($_POST["emailaddress"]) && $_POST["emailaddress"] == "single") {
                            $debtor->EmailAddress = getFirstMailAddress($debtor->EmailAddress);
                            $debtor->InvoiceEmailAddress = getFirstMailAddress($debtor->InvoiceEmailAddress);
                        }
                        echo "{ \"resultSet\": [";
                        echo json_encode($debtor);
                        echo "] }";
                        exit;
                    } else {
                        echo "{ \"errorSet\": [";
                        echo json_encode(["Message" => "<i>" . __("no debtor selected yet") . "</i>"]);
                        echo "] }";
                        exit;
                    }
                } else {
                    echo "{ \"errorSet\": [";
                    echo json_encode(["Message" => "<i>" . __("no debtor selected yet") . "</i>"]);
                    echo "] }";
                    exit;
                }
            } else {
                if(isset($_POST["action"]) && $_POST["action"] == "server_information_by_account_id") {
                    require_once "class/server.php";
                    require_once "class/hosting.php";
                    $_id = esc($_POST["id"]);
                    if(is_numeric($_id) && 0 < $_id) {
                        $hosting = new hosting();
                        $hosting->show($_id);
                        if(0 < $hosting->id) {
                            $server = new server();
                            $server->show($hosting->Server);
                            if(0 < $server->id) {
                                $array_controlpanels = $server->getAvailableControlPanels();
                                $server->HostingAccountDomain = $hosting->Domain;
                                $server->PanelName = $array_controlpanels[$server->Panel];
                                $server->Password = "";
                                $server->DomainTypeTranslated = __("server domaintype " . $server->DomainType);
                                echo "{ \"resultSet\": [";
                                echo json_encode($server);
                                echo "] }";
                                exit;
                            }
                            echo "{ \"errorSet\": [";
                            echo json_encode(["Message" => "<i>" . __("no server selected yet") . "</i>"]);
                            echo "] }";
                            exit;
                        }
                    }
                    echo "{ \"errorSet\": [";
                    echo json_encode(["Message" => "<i>" . __("no hosting account selected yet") . "</i>"]);
                    echo "] }";
                    exit;
                }
                if(isset($_POST["action"]) && $_POST["action"] == "registrar_information") {
                    require_once "class/registrar.php";
                    $_id = esc($_POST["id"]);
                    if(is_numeric($_id) && 0 < $_id) {
                        $registrar = new registrar();
                        $registrar->show($_id);
                        if(0 < $registrar->id) {
                            $registrar->AutoRenew = strip_tags($array_onoff[$registrar->AutoRenew]);
                            $registrar->Testmode = strip_tags($array_onoff[$registrar->Testmode]);
                            $result = [];
                            $result["Identifier"] = $registrar->Identifier;
                            $result["Name"] = $registrar->Name;
                            $result["Class"] = $registrar->Class;
                            $result["DNS1"] = $registrar->DNS1;
                            $result["DNS2"] = $registrar->DNS2;
                            $result["DNS3"] = $registrar->DNS3;
                            $result["domain_admin_customer"] = $registrar->domain_admin_customer;
                            $result["domain_admin_handle"] = $registrar->domain_admin_handle;
                            $result["domain_tech_customer"] = $registrar->domain_tech_customer;
                            $result["domain_tech_handle"] = $registrar->domain_tech_handle;
                            $result["DomainEnabled"] = $registrar->DomainEnabled;
                            $result["SSLEnabled"] = $registrar->SSLEnabled;
                            echo "{ \"resultSet\": [";
                            echo json_encode($result);
                            echo "] }";
                            exit;
                        }
                        echo "{ \"errorSet\": [";
                        echo json_encode(["Message" => "<i>" . __("no registrar selected yet") . "</i>"]);
                        echo "] }";
                        exit;
                    }
                    echo "{ \"errorSet\": [";
                    echo json_encode(["Message" => "<i>" . __("no registrar selected yet") . "</i>"]);
                    echo "] }";
                    exit;
                }
                if(isset($_POST["action"]) && $_POST["action"] == "domain_extra_fields") {
                    require_once "class/domain.php";
                    $_registrar = esc($_POST["registrar"]);
                    $_tld = esc($_POST["tld"]);
                    $_domain = esc($_POST["domain"]);
                    if(is_numeric($_registrar) && 0 < $_registrar) {
                        $domain = new domain();
                        $domain->showExtraFields($_domain);
                        $extra_fields = $domain->listExtraFields($_tld, $_registrar);
                        if(!empty($extra_fields)) {
                            echo "\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
                            echo __("domain extra fields data");
                            echo "</h3><div class=\"content\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t";
                            foreach ($extra_fields as $field_id => $field) {
                                echo "\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title\">";
                                echo $field["LabelTitle"];
                                echo "</strong>\n\t\t\t\t\t\n\t\t\t\t\t";
                                if($field["LabelType"] == "text") {
                                    echo "<input type=\"text\" name=\"domain[Extra_";
                                    echo $field_id;
                                    echo "]\" ";
                                    tabindex($ti);
                                    echo " value=\"";
                                    echo isset($domain->ExtraFields[$field_id]) ? $domain->ExtraFields[$field_id]["Value"] : $field["LabelDefault"];
                                    echo "\" class=\"text1 size4\"/>";
                                } elseif($field["LabelType"] == "options") {
                                    echo "<select name=\"domain[Extra_";
                                    echo $field_id;
                                    echo "]\" class=\"text1 size4f\">\n\t\t\t\t\t\t";
                                    foreach ($field["LabelOptions"] as $key => $value) {
                                        echo "\t\t\t\t\t\t\t<option value=\"";
                                        echo $key;
                                        echo "\" ";
                                        if(isset($domain->ExtraFields[$field_id]) && $key == $domain->ExtraFields[$field_id]["Value"] || !isset($domain->ExtraFields[$field_id]) && $key == $field["LabelDefault"]) {
                                            echo "selected=\"selected\"";
                                        }
                                        echo " >";
                                        echo $value;
                                        echo "</option>\n\t\t\t\t\t\t";
                                    }
                                    echo "\t\t\t\t\t\t</select>";
                                }
                                echo "\t\t\t\t\t<br /><br />\n\t\t\t\t\t";
                            }
                            echo "\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\n\t\t\t";
                        } else {
                            exit;
                        }
                    } else {
                        exit;
                    }
                } else {
                    if(isset($_POST["action"]) && $_POST["action"] == "server_logincheck") {
                        require_once "class/server.php";
                        $_id = esc($_POST["id"]);
                        if(is_numeric($_id) && 0 < $_id) {
                            $server = new server();
                            $server->show($_id);
                            if(0 < $server->id) {
                                $server_check_login = $server->checkServerLogin();
                                if($server_check_login) {
                                    echo "{ \"resultSet\": [";
                                    echo json_encode(["Message" => "<font class=\"c5\">" . __("logincheck success") . "</font>"]);
                                    echo "] }";
                                    exit;
                                }
                                echo "{ \"errorSet\": [";
                                echo json_encode(["Message" => "<font class=\"c3\">" . __("logincheck error") . "</font>", "ServerError" => implode(", ", $server->Error)]);
                                echo "] }";
                                exit;
                            }
                            echo "{ \"errorSet\": [";
                            echo json_encode(["Message" => "<i>" . __("we are not able to check server") . "</i>"]);
                            echo "] }";
                            exit;
                        }
                        echo "{ \"errorSet\": [";
                        echo json_encode(["Message" => "<i>" . __("we are not able to check server") . "</i>"]);
                        echo "] }";
                        exit;
                    }
                    if(isset($_POST["action"]) && $_POST["action"] == "registrar_api") {
                        require_once "class/registrar.php";
                        $_id = esc($_POST["id"]);
                        if($_id) {
                            $registrar = new registrar();
                            $registrar->Class = $_id;
                            $registrar->getVersionInfo();
                            if(is_array($registrar->VersionInfo)) {
                                $info = [];
                                $info["registrar_version"] = $registrar->VersionInfo["api_version"];
                                $info["integration_version"] = $registrar->VersionInfo["version"];
                                $info["dev_logo"] = isset($registrar->VersionInfo["dev_logo"]) && substr($registrar->VersionInfo["dev_logo"], 0, 8) == "https://" ? $registrar->VersionInfo["dev_logo"] : "";
                                $info["dev_author"] = isset($registrar->VersionInfo["dev_author"]) ? $registrar->VersionInfo["dev_author"] : "";
                                $info["dev_website"] = isset($registrar->VersionInfo["dev_website"]) ? $registrar->VersionInfo["dev_website"] : "";
                                $info["dev_email"] = isset($registrar->VersionInfo["dev_email"]) ? $registrar->VersionInfo["dev_email"] : "";
                                $info["dev_phone"] = isset($registrar->VersionInfo["dev_phone"]) ? $registrar->VersionInfo["dev_phone"] : "";
                                $info["settings"]["user"]["show"] = isset($registrar->VersionInfo["settings"]["user"]) && $registrar->VersionInfo["settings"]["user"] === false ? false : true;
                                $info["settings"]["password"]["show"] = isset($registrar->VersionInfo["settings"]["password"]) && $registrar->VersionInfo["settings"]["password"] === false ? false : true;
                                $info["settings"]["registrar_ip"]["show"] = isset($registrar->VersionInfo["settings"]["registrar_ip"]) && $registrar->VersionInfo["settings"]["registrar_ip"] ? true : false;
                                $settingArray = ["setting1", "setting2", "setting3", "dnstemplate"];
                                foreach ($settingArray as $settingName) {
                                    if(isset($registrar->VersionInfo["settings"][$settingName])) {
                                        $info["settings"][$settingName]["show"] = true;
                                        $info["settings"][$settingName]["label"] = $registrar->VersionInfo["settings"][$settingName]["label"];
                                        $info["settings"][$settingName]["type"] = $registrar->VersionInfo["settings"][$settingName]["type"];
                                        if($registrar->VersionInfo["settings"][$settingName]["type"] == "select") {
                                            $info["settings"][$settingName]["options"] = "";
                                            foreach ($registrar->VersionInfo["settings"][$settingName]["options"] as $opt_k => $opt_v) {
                                                $info["settings"][$settingName]["options"] .= "<option value=\"" . $opt_k . "\">" . htmlspecialchars($opt_v) . "</option>";
                                            }
                                        } else {
                                            $info["settings"][$settingName]["options"] = "";
                                        }
                                        $info["settings"][$settingName]["defaultValue"] = $registrar->VersionInfo["settings"][$settingName]["default"];
                                        $info["settings"][$settingName]["helpText"] = isset($registrar->VersionInfo["settings"][$settingName]["helpText"]) ? $registrar->VersionInfo["settings"][$settingName]["helpText"] : "";
                                    } else {
                                        $info["settings"][$settingName]["show"] = false;
                                    }
                                }
                                $info["domain_support"] = isset($registrar->VersionInfo["domain_support"]) ? $registrar->VersionInfo["domain_support"] : true;
                                $info["ssl_support"] = isset($registrar->VersionInfo["ssl_support"]) ? $registrar->VersionInfo["ssl_support"] : false;
                                echo json_encode($info);
                                exit;
                            } else {
                                echo "{ \"errorSet\": [";
                                echo json_encode(["Message" => "<i>" . __("registrar api information cannot be retrieved") . "</i>"]);
                                echo "] }";
                                exit;
                            }
                        } else {
                            echo "{ \"errorSet\": [";
                            echo json_encode(["Message" => "<i>" . __("registrar api information cannot be retrieved") . "</i>"]);
                            echo "] }";
                            exit;
                        }
                    } else {
                        if(isset($_POST["action"]) && $_POST["action"] == "controlpanel_api") {
                            $server_panel = esc($_POST["id"]);
                            if($server_panel) {
                                if(file_exists("3rdparty/hosting/" . $server_panel . "/version.php")) {
                                    $version = [];
                                    include "3rdparty/hosting/" . $server_panel . "/version.php";
                                }
                                if(isset($version["name"])) {
                                    $info = [];
                                    $info["name"] = $version["name"];
                                    $info["password_type"] = isset($version["password_type"]) ? $version["password_type"] : "input";
                                    $info["password_label"] = isset($version["password_label"]) ? $version["password_label"] : __("password");
                                    $info["dev_logo"] = isset($version["dev_logo"]) && substr($version["dev_logo"], 0, 8) == "https://" ? $version["dev_logo"] : "";
                                    $info["dev_author"] = isset($version["dev_author"]) ? $version["dev_author"] : "";
                                    $info["dev_website"] = isset($version["dev_website"]) ? $version["dev_website"] : "";
                                    $info["dev_email"] = isset($version["dev_email"]) ? $version["dev_email"] : "";
                                    $info["dev_phone"] = isset($version["dev_phone"]) ? $version["dev_phone"] : "";
                                    $info["DomainType"] = isset($version["DomainType"]) ? $version["DomainType"] : [];
                                    $info["additional_server_ip"] = isset($version["additional_server_ip"]) ? $version["additional_server_ip"] : false;
                                    if(isset($version["hasAdditionalSettings"]) && $version["hasAdditionalSettings"] === true) {
                                        if(isset($_POST["server_id"]) && 0 < $_POST["server_id"]) {
                                            require_once "class/server.php";
                                            $server = new server();
                                            $server->Identifier = esc($_POST["server_id"]);
                                            $server->show();
                                        }
                                        require_once "3rdparty/hosting/" . $server_panel . "/" . $server_panel . ".php";
                                        $server_api = new $server_panel();
                                        $server_api->Server_Settings = $server->Settings;
                                        if(is_object($server_api) && @method_exists($server_api, "showSettingsHTML")) {
                                            $info["hasAdditionalSettings"] = $server_api->showSettingsHTML(esc($_POST["edit_or_show"]));
                                        }
                                    }
                                    echo json_encode($info);
                                    exit;
                                }
                                echo "{ \"errorSet\": [";
                                echo json_encode(["Message" => "<i>" . __("controlpanel api information cannot be retrieved") . "</i>"]);
                                echo "] }";
                                exit;
                            }
                            echo "{ \"errorSet\": [";
                            echo json_encode(["Message" => "<i>" . __("controlpanel api information cannot be retrieved") . "</i>"]);
                            echo "] }";
                            exit;
                        }
                        if(isset($_POST["action"]) && $_POST["action"] == "package_list") {
                            require_once "class/server.php";
                            $_id = esc($_POST["id"]);
                            $also_existing = isset($_POST["also_existing"]) && $_POST["also_existing"] == "true" ? true : false;
                            if(is_numeric($_id) && 0 < $_id) {
                                $server = new server();
                                $server->show($_id);
                                if(0 < $server->id) {
                                    if($also_existing) {
                                        require_once "class/package.php";
                                        $package = new package();
                                        $fields = ["PackageName", "PackageType"];
                                        $list_hosting_packages = $package->all($fields, "PackageName", "ASC", -1, "Server", $server->id);
                                        echo "{ \"existing\": [";
                                        $existing_packages = [];
                                        foreach ($list_hosting_packages as $k => $v) {
                                            if(is_numeric($k) && isset($_POST["type"]) && $_POST["type"] == $v["PackageType"]) {
                                                $existing_packages[] = json_encode([$v["id"], $v["PackageName"]]);
                                            }
                                        }
                                        echo implode(",", $existing_packages);
                                        echo "], \"newtemplate\": ";
                                    }
                                    $api = $server->connect();
                                    if(!$server->Panel) {
                                        echo "{ \"errorSet\": [";
                                        echo json_encode(["nopanel" => true, "Message" => __("specify package on right")]);
                                        echo "] }";
                                    } elseif(!is_object($api)) {
                                        echo "{ \"errorSet\": [";
                                        echo json_encode(["Message" => __("couldnt connect to server")]);
                                        echo "] }";
                                    } else {
                                        $list_server_packages = $api->listPackages();
                                        if($list_server_packages === false) {
                                            echo "{ \"errorSet\": [";
                                            echo json_encode(["Message" => __("couldnt connect to server")]);
                                            echo "] }";
                                        } else {
                                            $result = [];
                                            if(isset($_POST["type"]) && $_POST["type"] == "reseller") {
                                                if(is_array($list_server_packages["reseller"])) {
                                                    echo "{ \"resultSet\": [";
                                                    foreach ($list_server_packages["reseller"] as $k => $v) {
                                                        if(is_numeric($k)) {
                                                            $result[] = json_encode($v);
                                                        }
                                                    }
                                                    echo implode(",", $result);
                                                    echo "] }";
                                                } else {
                                                    echo "{ \"errorSet\": [";
                                                    echo json_encode(["Message" => __("no access to reseller level")]);
                                                    echo "] }";
                                                }
                                            } elseif(is_array($list_server_packages["user"])) {
                                                echo "{ \"resultSet\": [";
                                                foreach ($list_server_packages["user"] as $k => $v) {
                                                    if(is_numeric($k)) {
                                                        $result[] = json_encode($v);
                                                    }
                                                }
                                                echo implode(",", $result);
                                                echo "] }";
                                            } else {
                                                echo "{ \"errorSet\": [";
                                                echo json_encode(["Message" => __("no templates found on server")]);
                                                echo "] }";
                                            }
                                        }
                                    }
                                    if($also_existing) {
                                        echo "}";
                                        exit;
                                    }
                                } else {
                                    echo "{ \"errorSet\": [";
                                    echo json_encode(["Message" => __("no server selected yet")]);
                                    echo "] }";
                                    exit;
                                }
                            } else {
                                echo "{ \"errorSet\": [";
                                echo json_encode(["Message" => __("no server selected yet")]);
                                echo "] }";
                                exit;
                            }
                        } elseif(isset($_POST["action"]) && $_POST["action"] == "package_options_for_server") {
                            require_once "class/package.php";
                            $_id = esc($_POST["id"]);
                            $package = new package();
                            $fields = ["PackageName", "TemplateName", "Server"];
                            $list_hosting_packages = $package->all($fields, "PackageName", "ASC", -1, "Server", $_id);
                            $return = "<option value=\"\" selected=\"selected\">" . __("make your choice") . "</option>";
                            if(0 < $list_hosting_packages["CountRows"]) {
                                foreach ($list_hosting_packages as $key => $value) {
                                    if(is_numeric($key)) {
                                        if($value["id"] == $_POST["package_id"]) {
                                            $return .= "<option value=\"" . $value["id"] . "\" selected=\"selected\">" . $value["PackageName"] . "</option>";
                                        } else {
                                            $return .= "<option value=\"" . $value["id"] . "\">" . $value["PackageName"] . "</option>";
                                        }
                                    }
                                }
                            } else {
                                $return = "<option value=\"\">" . __("this server has no packages") . "</option>";
                            }
                            exit($return);
                        } else {
                            if(isset($_POST["action"]) && $_POST["action"] == "debtor_username") {
                                require_once "class/debtor.php";
                                $debtor = new debtor();
                                $list = $debtor->all(["DebtorCode"], "DebtorCode", false, "-1", "Username", esc($_POST["username"]));
                                $debtor_id = esc($_POST["debtor_id"]);
                                if(0 < $debtor_id && $list["CountRows"] == 1 && isset($list[$debtor_id])) {
                                    $list["CountRows"] = 0;
                                }
                                echo "{ \"resultSet\": [";
                                echo json_encode(["Number" => $list["CountRows"]]);
                                echo "] }";
                                exit;
                            }
                            if(isset($_POST["action"]) && $_POST["action"] == "get_select_handles") {
                                require_once "class/handle.php";
                                $handle = new handle();
                                $html = "<option value=\"\">" . __("select a handle") . "</option>";
                                if(isset($_POST["debtor_id"]) && 0 < $_POST["debtor_id"]) {
                                    $list = $handle->all(["Handle", "HandleType", "Registrar", "Name", "RegistrarHandle", "CompanyName", "SurName"], "Handle", "ASC", -1, "Debtor", esc($_POST["debtor_id"]));
                                    $counter = 0;
                                    $html2 = "<optgroup label=\"" . __("handles from debtor") . "\">";
                                    foreach ($list as $k => $v) {
                                        if(is_numeric($k) && (!$_POST["registrar_id"] || $_POST["registrar_id"] == $v["Registrar"] || $v["Registrar"] == "0")) {
                                            $html2 .= "<option value=\"" . $v["id"] . "\">" . $v["Handle"] . ($v["Name"] ? " " . $v["Name"] : "") . " - " . ($v["CompanyName"] ? $v["CompanyName"] : $v["SurName"]) . "</option>";
                                            $counter++;
                                        }
                                    }
                                    $html2 .= "</optgroup>";
                                    if(0 < $counter) {
                                        $html .= $html2;
                                    }
                                }
                                $list = $handle->all(["Handle", "HandleType", "Registrar", "Name", "RegistrarHandle", "Debtor", "CompanyName", "SurName"], "Handle", "ASC", -1, "Debtor", "0");
                                $counter = 0;
                                $html2 = "<optgroup label=\"" . __("general handles") . "\">";
                                foreach ($list as $k => $v) {
                                    if(is_numeric($k) && (int) $v["Debtor"] === 0 && (!$_POST["registrar_id"] || $_POST["registrar_id"] == $v["Registrar"] || $v["Registrar"] == "0")) {
                                        $html2 .= "<option value=\"" . $v["id"] . "\">" . $v["Handle"] . ($v["Name"] ? " " . $v["Name"] : "") . " - " . ($v["CompanyName"] ? $v["CompanyName"] : $v["SurName"]) . "</option>";
                                        $counter++;
                                    }
                                }
                                $html2 .= "</optgroup>";
                                if(0 < $counter) {
                                    $html .= $html2;
                                }
                                if(isset($_POST["show"]) && $_POST["show"] == "all") {
                                    $list = $handle->all(["Handle", "HandleType", "Registrar", "Name", "RegistrarHandle", "Debtor", "CompanyName", "SurName"], "Handle", "ASC", -1);
                                    $counter = 0;
                                    $html2 = "<optgroup label=\"" . __("handles from other debtors") . "\">";
                                    foreach ($list as $k => $v) {
                                        if(is_numeric($k) && 0 < $v["Debtor"] && $v["Debtor"] != $_POST["debtor_id"] && (!$_POST["registrar_id"] || $_POST["registrar_id"] == $v["Registrar"] || $v["Registrar"] == "")) {
                                            $html2 .= "<option value=\"" . $v["id"] . "\">" . $v["Handle"] . ($v["Name"] ? " " . $v["Name"] : "") . " - " . ($v["CompanyName"] ? $v["CompanyName"] : $v["SurName"]) . "</option>";
                                            $counter++;
                                        }
                                    }
                                    $html2 .= "<optgroup>";
                                    if(0 < $counter) {
                                        $html .= $html2;
                                    }
                                }
                                echo $html;
                                exit;
                            } elseif(isset($_POST["action"]) && $_POST["action"] == "debtor_hosting_select") {
                                require_once "class/hosting.php";
                                $bFirst_account_selected = $_POST["first_account_selected"] == "true" ? true : false;
                                $hosting = new hosting();
                                $list = $hosting->all(["Username", "Status"], "Username", false, "-1", "Debtor", esc($_POST["debtor_id"]), "1|3|4|5|7");
                                $return = "<option value=\"0\">" . __("no hosting account") . "</option>";
                                foreach ($list as $key => $value) {
                                    if(is_numeric($key)) {
                                        if($bFirst_account_selected && $list["CountRows"] == 1) {
                                            $selected = " selected=\"selected\"";
                                            $bFirst_account_selected = false;
                                        } else {
                                            $selected = "";
                                        }
                                        $return .= "<option value=\"" . $value["id"] . "\"" . $selected . ">" . $value["Username"] . "</option>";
                                    }
                                }
                                exit($return);
                            } else {
                                if(isset($_POST["action"]) && $_POST["action"] == "generate_accountname") {
                                    require_once "class/hosting.php";
                                    require_once "class/debtor.php";
                                    $_method = esc($_POST["method"]);
                                    $_debtor = esc($_POST["debtor"]);
                                    $_domain = esc($_POST["domain"]);
                                    $hosting = new hosting();
                                    $debtor = new debtor();
                                    $debtor->show($_debtor);
                                    if(!($resultName = $hosting->generateNewAccountname($_method, $debtor->CompanyName, $debtor->SurName, $debtor->Initials, $_domain))) {
                                        echo "{ \"errorSet\": [";
                                        echo json_encode(["Message" => ""]);
                                        echo "] }";
                                        exit;
                                    }
                                    echo "{ \"resultSet\": [";
                                    echo json_encode(["Name" => $resultName]);
                                    echo "] }";
                                    exit;
                                }
                                if(isset($_POST["action"]) && $_POST["action"] == "generate_hosting_password") {
                                    echo generatePassword();
                                    exit;
                                }
                                if(isset($_POST["action"]) && $_POST["action"] == "template_information") {
                                    require_once "class/template.php";
                                    $template = new emailtemplate();
                                    $template->Identifier = esc($_POST["templateid"]);
                                    $template->show();
                                    $template->SenderName = substr($template->Sender, 0, strpos($template->Sender, "&lt;"));
                                    $template->SenderEmail = substr($template->Sender, strpos($template->Sender, "&lt;") + 4, -4);
                                    if($template->SenderName === false) {
                                        $template->SenderName = "";
                                    }
                                    if($template->SenderEmail === false) {
                                        $template->SenderEmail = "";
                                    }
                                    foreach ($template as $k => $v) {
                                        if(is_string($v)) {
                                            $template->{$k} = htmlspecialchars_decode($v);
                                        }
                                    }
                                    echo "{ \"resultSet\": [";
                                    echo json_encode($template);
                                    echo "] }";
                                    exit;
                                } elseif(isset($_POST["action"]) && $_POST["action"] == "get_hosting_details") {
                                    require_once "class/hosting.php";
                                    $hosting = new hosting();
                                    if($_POST["type"] == "domains") {
                                        $hosting->show(esc($_POST["id"]));
                                    } else {
                                        $hosting->show(esc($_POST["id"]), false);
                                    }
                                    switch ($_POST["type"]) {
                                        case "domains":
                                            if(isset($hosting->DomainList) && is_array($hosting->DomainList)) {
                                                require_once "class/server.php";
                                                $server = new server();
                                                if($server->show($hosting->Server)) {
                                                    $serverType = $server->DomainType ? $server->DomainType : "additional";
                                                } else {
                                                    $serverType = "additional";
                                                }
                                                unset($server);
                                                require_once "views/hosting.show.domains.php";
                                                exit;
                                            }
                                            break;
                                        case "stats":
                                            $hosting->Error = [];
                                            $account_exists_on_server = $hosting->checkIfAccountExistsOnServer();
                                            $hosting_account_error = !$account_exists_on_server ? $hosting->Error[0] : "";
                                            if(isset($_SESSION["hosting"]["updowngrade_suppress_error"]) && $_SESSION["hosting"]["updowngrade_suppress_error"] === true) {
                                                $hosting_account_error = "";
                                                unset($_SESSION["hosting"]["updowngrade_suppress_error"]);
                                            }
                                            if(($hosting->Status == 4 || $hosting->Status == 5) && $account_exists_on_server) {
                                                $hosting->getStats();
                                            }
                                            $a = [];
                                            $a["traffic"] = $hosting->UsedBandWidth || $hosting->UsedBandWidth == "0" && 0 < strlen($hosting->UsedBandWidth) ? formatMB($hosting->UsedBandWidth, formatMB($hosting->BandWidth)) : __("unknown");
                                            $a["traffic"] .= " / ";
                                            $a["traffic"] .= $hosting->uBandWidth == 1 ? __("unlimited") : formatMB($hosting->BandWidth);
                                            $a["disc"] = $hosting->UsedDiscSpace || $hosting->UsedDiscSpace == "0" && 0 < strlen($hosting->UsedDiscSpace) ? formatMB($hosting->UsedDiscSpace, formatMB($hosting->DiscSpace)) : __("unknown");
                                            $a["disc"] .= " / ";
                                            $a["disc"] .= $hosting->uDiscSpace == 1 ? __("unlimited") : formatMB($hosting->DiscSpace);
                                            echo "{ \"result\": [" . $account_exists_on_server . "],";
                                            echo "\"errorMessage\": [" . json_encode($hosting_account_error) . "],";
                                            echo "\"resultSet\": [" . json_encode($a) . "] }";
                                            break;
                                        default:
                                            exit;
                                    }
                                } else {
                                    if(isset($_POST["action"]) && $_POST["action"] == "create_hosting") {
                                        require_once "class/hosting.php";
                                        $hosting = new hosting();
                                        $hosting->show(esc($_POST["id"]), false);
                                        if($hosting->create()) {
                                            echo "OK";
                                        } else {
                                            echo "BAD";
                                        }
                                        flashMessage($hosting);
                                        exit;
                                    }
                                    if(isset($_POST["action"]) && $_POST["action"] == "create_domain") {
                                        require_once "class/domain.php";
                                        $domain = new domain();
                                        $domain->show(esc($_POST["id"]), false);
                                        if(isset($domain->AuthKey) && $domain->AuthKey != "") {
                                            $create_result = $domain->transfer();
                                        } else {
                                            $create_result = $domain->register();
                                        }
                                        if($create_result) {
                                            echo "OK";
                                        } else {
                                            echo "BAD";
                                        }
                                        flashMessage($domain);
                                        exit;
                                    }
                                    if(isset($_POST["action"]) && $_POST["action"] == "get_domain_info") {
                                        require_once "class/registrar.php";
                                        $registrar = new registrar();
                                        $registrar_id = esc($_POST["id"]);
                                        $registrar->show($registrar_id);
                                        $list_domains = unserialize($_SESSION["Registrar_" . $registrar_id . "_import_response"]);
                                        if($list_domains[$_POST["counter"]]["Information"]["whois"] != "") {
                                            $whois = $list_domains[$_POST["counter"]]["Information"]["whois"];
                                            echo "<strong>" . ($whois->ownerCompanyName == "" ? $whois->ownerInitials . " " . $whois->ownerSurName : $whois->ownerCompanyName) . "</strong> (" . ($whois->ownerStreetName != "" ? $whois->ownerStreetName . " " : "") . ($whois->ownerStreetNumber != "" ? $whois->ownerStreetNumber . ", " : "") . ($whois->ownerZipCode != "" ? $whois->ownerZipCode . ", " : "") . ($whois->ownerCity != "" ? $whois->ownerCity . ", " : "") . $whois->ownerPhoneNumber . ")";
                                            exit;
                                        }
                                        $domainInformation = $registrar->importDomainInformation($list_domains[$_POST["counter"]]["Domain"]);
                                        if(isset($list_domains[$_POST["counter"]]["Information"]["expiration_date"]) && $domainInformation["Information"]["expiration_date"] == "") {
                                            $domainInformation["Information"]["expiration_date"] = $list_domains[$_POST["counter"]]["Information"]["expiration_date"];
                                        } elseif(isset($list_domains[$_POST["counter"]]["Information"]["expires"]) && $domainInformation["Information"]["expires"] == "") {
                                            $domainInformation["Information"]["expires"] = $list_domains[$_POST["counter"]]["Information"]["expires"];
                                        }
                                        $list_domains[$_POST["counter"]] = $domainInformation;
                                        $_SESSION["Registrar_" . $registrar_id . "_import_response"] = serialize($list_domains);
                                        $whois = $domainInformation["Information"]["whois"];
                                        if(!is_object($whois)) {
                                            echo __("unknown");
                                        } else {
                                            echo "<strong>" . ($whois->ownerCompanyName == "" ? $whois->ownerInitials . " " . $whois->ownerSurName : $whois->ownerCompanyName) . "</strong> (" . ($whois->ownerStreetName != "" ? $whois->ownerStreetName . " " : "") . ($whois->ownerStreetNumber != "" ? $whois->ownerStreetNumber . ", " : "") . ($whois->ownerZipCode != "" ? $whois->ownerZipCode . ", " : "") . ($whois->ownerCity != "" ? $whois->ownerCity . ", " : "") . $whois->ownerPhoneNumber . ")";
                                        }
                                        exit;
                                    }
                                    if(isset($_POST["action"]) && $_POST["action"] == "get_handle_info") {
                                        require_once "class/registrar.php";
                                        $registrar = new registrar();
                                        $registrar_id = esc($_POST["id"]);
                                        $registrar->show($registrar_id);
                                        $list_handles = unserialize($_SESSION["Registrar_" . $registrar_id . "_import2_response"]);
                                        if(isset($list_handles[$_POST["counter"]]["EmailAddress"]) && $list_handles[$_POST["counter"]]["EmailAddress"] != "") {
                                            echo $list_handles[$_POST["counter"]]["EmailAddress"];
                                            exit;
                                        }
                                        $handleInformation = $registrar->importHandleInformation($list_handles[$_POST["counter"]]["Handle"]);
                                        $handleInformation["Retrieved"] = true;
                                        $list_handles[$_POST["counter"]] = $handleInformation;
                                        $_SESSION["Registrar_" . $registrar_id . "_import2_response"] = serialize($list_handles);
                                        echo $handleInformation["EmailAddress"];
                                        exit;
                                    }
                                    if(isset($_POST["action"]) && $_POST["action"] == "add_handle") {
                                        require_once "class/handle.php";
                                        $handle = new handle();
                                        parse_str($_POST["values"], $data);
                                        foreach ($data as $key => $value) {
                                            if(in_array($key, $handle->Variables)) {
                                                $handle->{$key} = esc($value);
                                            }
                                        }
                                        if(IS_INTERNATIONAL) {
                                            $handle->State = isset($data["StateCode"]) && $data["StateCode"] ? esc($data["StateCode"]) : $handle->State;
                                        }
                                        if(isset($data["Debtor"]) && 0 < $data["Debtor"]) {
                                            $handle->Handle = $handle->nextInternalHandle("debtor", esc($data["Debtor"]));
                                        } else {
                                            $handle->Handle = $handle->nextInternalHandle("general");
                                        }
                                        if($handle->Registrar && $data["RegistrarHandleType"] == "new") {
                                            $handle->CreateAtRegistrar = true;
                                        }
                                        if(!isset($_SESSION["custom_fields"]["handle"]) || $_SESSION["custom_fields"]["handle"]) {
                                            $customfields_list = $_SESSION["custom_fields"]["handle"];
                                            $handle->customvalues = [];
                                            foreach ($customfields_list as $k => $custom_field) {
                                                $handle->customvalues[$custom_field["FieldCode"]] = isset($data["custom"][$custom_field["FieldCode"]]) ? esc($data["custom"][$custom_field["FieldCode"]]) : "";
                                            }
                                        }
                                        if($handle->add()) {
                                            echo "{ \"handle_id\": " . $handle->Identifier . " }";
                                            exit;
                                        }
                                        echo "{ \"errorSet\": [";
                                        echo json_encode(["Message" => parse_message($handle)]);
                                        echo "] }";
                                        exit;
                                    } elseif(isset($_POST["action"]) && $_POST["action"] == "add_default_handle") {
                                        require_once "class/handle.php";
                                        require_once "class/registrar.php";
                                        $handle = new handle();
                                        parse_str($_POST["values"], $data);
                                        if($data["DefaultHandleType"] == "new") {
                                            foreach ($data as $key => $value) {
                                                if(in_array($key, $handle->Variables)) {
                                                    $handle->{$key} = esc($value);
                                                }
                                            }
                                            if(IS_INTERNATIONAL) {
                                                $handle->State = isset($data["StateCode"]) && $data["StateCode"] ? esc($data["StateCode"]) : $handle->State;
                                            }
                                            if($handle->Registrar && $data["RegistrarHandleType"] == "new") {
                                            }
                                            $handle->Handle = $handle->nextInternalHandle("general");
                                            if(!isset($_SESSION["custom_fields"]["handle"]) || $_SESSION["custom_fields"]["handle"]) {
                                                $customfields_list = $_SESSION["custom_fields"]["handle"];
                                                $handle->customvalues = [];
                                                foreach ($customfields_list as $k => $custom_field) {
                                                    $handle->customvalues[$custom_field["FieldCode"]] = isset($data["custom"][$custom_field["FieldCode"]]) ? esc($data["custom"][$custom_field["FieldCode"]]) : "";
                                                }
                                            }
                                            $result = $handle->add();
                                        } elseif($data["DefaultHandleType"] == "existing") {
                                            $handle->Identifier = $data["ExistingHandle"];
                                            $handle->Registrar = $data["Registrar"];
                                            $result = true;
                                        }
                                        if($result) {
                                            if(isset($_POST["role"]) && ($_POST["role"] == "admin" || $_POST["role"] == "tech")) {
                                                $registrar = new registrar();
                                                $registrar->updateDefaultHandle($handle->Registrar, esc($_POST["role"]), $handle->Identifier);
                                            }
                                            echo "{ \"handle_id\": " . $handle->Identifier . " }";
                                            exit;
                                        }
                                        echo "{ \"errorSet\": [";
                                        echo json_encode(["Message" => parse_message($handle)]);
                                        echo "] }";
                                        exit;
                                    } elseif(isset($_POST["action"]) && $_POST["action"] == "handle_information") {
                                        require_once "class/handle.php";
                                        $_id = esc($_POST["id"]);
                                        if(is_numeric($_id) && 0 < $_id) {
                                            $handle = new handle();
                                            if($handle->show($_id)) {
                                                foreach ($handle as $k => $v) {
                                                    if(is_string($v)) {
                                                        $handle->{$k} = htmlspecialchars_decode($v);
                                                    }
                                                }
                                                $handle->CountryLong = $array_country[$handle->Country];
                                                $handle->LegalFormLong = isset($array_legaltype[$handle->LegalForm]) ? $array_legaltype[$handle->LegalForm] : "";
                                                $handle->EmailAddress = getFirstMailAddress($handle->EmailAddress);
                                                echo "{ \"resultSet\": [";
                                                echo json_encode($handle);
                                                echo "] }";
                                                exit;
                                            } else {
                                                echo "{ \"errorSet\": [";
                                                echo json_encode(["Message" => __("no valid handle selected yet")]);
                                                echo "] }";
                                                exit;
                                            }
                                        } else {
                                            echo "{ \"errorSet\": [";
                                            echo json_encode(["Message" => __("no valid handle selected yet")]);
                                            echo "] }";
                                            exit;
                                        }
                                    } elseif(isset($_POST["action"]) && $_POST["action"] == "get_periodics") {
                                        require_once "class/periodic.php";
                                        $periodic = new periodic();
                                        $periodics = $periodic->all(["id", "Description", "Reference"], "Description", "ASC", "-1", "Debtor", esc($_POST["debtor_id"]));
                                        $return = "<option value=\"\" selected=\"selected\">" . __("select an existing subscription") . "</option>";
                                        if(!empty($_POST["debtor_id"])) {
                                            foreach ($periodics as $key => $value) {
                                                if(is_numeric($key) && (int) $value["Reference"] === 0) {
                                                    if($value["id"] == $_POST["periodic_id"]) {
                                                        $return .= "<option value=\"" . $value["id"] . "\" selected=\"selected\">" . $value["Description"] . "</option>";
                                                    } else {
                                                        $return .= "<option value=\"" . $value["id"] . "\">" . $value["Description"] . "</option>";
                                                    }
                                                }
                                            }
                                        }
                                        if((int) $periodics["CountRows"] === 0) {
                                            $return = "<option value=\"\">" . __("this debtor has no subscriptions") . "</option>";
                                        }
                                        exit($return);
                                    } else {
                                        if(isset($_POST["action"]) && $_POST["action"] == "get_periodic") {
                                            require_once "class/periodic.php";
                                            $periodic = new periodic();
                                            $periodic->Identifier = esc($_POST["subscription_id"]);
                                            if($periodic->show()) {
                                                if(isset($_POST["format"]) && $_POST["format"] == "true") {
                                                    $periodic->Price_text = money($periodic->PriceExcl) . " " . __("per") . " " . (1 < $periodic->Periods ? $periodic->Periods . " " : "") . $array_periodes[$periodic->Periodic];
                                                    $periodic->Period_text = $periodic->StartPeriod . " " . __("till") . " " . $periodic->EndPeriod;
                                                    $periodic->NextDate_text = !rewrite_date_site2db($periodic->TerminationDate) || rewrite_date_site2db($periodic->NextDate) < rewrite_date_site2db($periodic->TerminationDate) ? $periodic->NextDate : "";
                                                    $periodic->Termation_text = $periodic->TerminationDate;
                                                }
                                                echo json_encode($periodic);
                                                exit;
                                            }
                                            echo "{ \"errorSet\": [";
                                            echo json_encode(["Message" => __("subscription cannot be found")]);
                                            echo "] }";
                                            exit;
                                        }
                                        if(isset($_POST["action"]) && $_POST["action"] == "check_url") {
                                            $response = ["content_type" => NULL, "http_code" => 404];
                                            $url = $_POST["url"];
                                            $url_data = parse_url($url);
                                            $valid_check = true;
                                            if(!in_array($url_data["scheme"], ["http", "https"])) {
                                                $valid_check = false;
                                            }
                                            if(isset($url_data["port"]) && !in_array($url_data["port"], ["8080", "80", "443"])) {
                                                $valid_check = false;
                                            }
                                            if($valid_check === true) {
                                                $ch = curl_init();
                                                curl_setopt($ch, CURLOPT_URL, $url);
                                                require_once "class/settings.php";
                                                settings::disableSSLVerificationIfNeeded($ch);
                                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                                curl_setopt($ch, CURLOPT_TIMEOUT, "2");
                                                $content = curl_exec($ch);
                                                $info = curl_getinfo($ch);
                                                curl_close($ch);
                                                $response = ["content_type" => $info["content_type"], "http_code" => $info["http_code"]];
                                            }
                                            echo json_encode($response);
                                            exit;
                                        }
                                        if(isset($_POST["action"]) && $_POST["action"] == "check_ideal_email") {
                                            $url = substr($_POST["url"], -1) == "/" ? $_POST["url"] : $_POST["url"] . "/";
                                            $result = getContent($url . "?testurl=true");
                                            if($result == "ideal") {
                                                echo "OK";
                                            } else {
                                                echo "BAD";
                                            }
                                            exit;
                                        }
                                        if(isset($_POST["action"]) && $_POST["action"] == "order_paymentmethods") {
                                            require_once "class/paymentmethod.php";
                                            $paymentmethod = new paymentmethod();
                                            exit($paymentmethod->reorder($_POST["order"]));
                                        }
                                        if(isset($_POST["action"]) && $_POST["action"] == "order_customfields") {
                                            require_once "class/customfields.php";
                                            $customfields = new customfields();
                                            exit($customfields->reorder($_POST["order"]));
                                        }
                                        if(isset($_POST["action"]) && $_POST["action"] == "order_dashboard") {
                                            require_once "class/employee.php";
                                            $acc = new employee();
                                            exit($acc->reorderPreferences($_POST["employee"], $_POST["order"]));
                                        }
                                        if(isset($_POST["action"]) && $_POST["action"] == "payment_backoffice") {
                                            $ch = curl_init();
                                            curl_setopt($ch, CURLOPT_URL, IDEAL_EMAIL . "backoffice.php");
                                            require_once "class/settings.php";
                                            settings::disableSSLVerificationIfNeeded($ch);
                                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                            curl_setopt($ch, CURLOPT_TIMEOUT, "10");
                                            $fields_string = "";
                                            foreach ($_POST as $key => $value) {
                                                $fields_string .= $key . "=" . $value . "&";
                                            }
                                            rtrim($fields_string, "&");
                                            curl_setopt($ch, CURLOPT_POST, count($_POST));
                                            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
                                            $content = curl_exec($ch);
                                            curl_close($ch);
                                            echo $content;
                                            exit;
                                        } elseif(isset($_POST["action"]) && $_POST["action"] == "vat_check") {
                                            require_once "class/debtor.php";
                                            $eu_countries = [];
                                            $result = Database_Model::getInstance()->get("HostFact_Settings_Countries", ["*"])->where("EUCountry", "yes")->execute();
                                            if($result) {
                                                foreach ($result as $v) {
                                                    $eu_countries[] = $v->CountryCode;
                                                }
                                            }
                                            if(!in_array($_POST["cc"], $eu_countries)) {
                                                echo "";
                                                exit;
                                            }
                                            if(is_numeric(substr(trim($_POST["vat"]), 0, 1))) {
                                                $countrycode = strtoupper($_POST["cc"]);
                                                $vat = str_replace([" ", ".", "-"], "", $_POST["vat"]);
                                            } else {
                                                $countrycode = strtoupper(substr($_POST["vat"], 0, 2));
                                                $vat = str_replace([" ", ".", "-"], "", substr($_POST["vat"], 2));
                                            }
                                            if($countrycode == "GR") {
                                                $countrycode = "EL";
                                            }
                                            try {
                                                $result = @@VATvalidator::_getSoapClient()->checkVat(["countryCode" => $countrycode, "vatNumber" => $vat]);
                                                if($result === false || $result == "servicedown" || isset($result->faultstring) && $result->faultstring) {
                                                    echo "UNAVAILABLE";
                                                } elseif(!isset($result->valid) || $result->valid !== true) {
                                                    echo "BAD";
                                                } else {
                                                    echo "OK";
                                                }
                                            } catch (SoapFault $e) {
                                                echo "UNAVAILABLE";
                                            }
                                            exit;
                                        } elseif(isset($_POST["action"]) && $_POST["action"] == "creditinvoice_get_creditor" && 0 < $_POST["creditor"]) {
                                            require_once "class/creditor.php";
                                            $creditor = new creditor();
                                            $creditor->Identifier = esc($_POST["creditor"]);
                                            if($creditor->show()) {
                                                $data = [];
                                                $data["CompanyName"] = $creditor->CompanyName;
                                                $data["TaxNumber"] = $creditor->TaxNumber;
                                                $data["Initials"] = $creditor->Initials;
                                                $data["SurName"] = $creditor->SurName;
                                                $data["Address"] = $creditor->Address;
                                                $data["Address2"] = $creditor->Address2;
                                                $data["ZipCode"] = $creditor->ZipCode;
                                                $data["City"] = $creditor->City;
                                                $data["StateName"] = $creditor->StateName;
                                                $data["Country"] = $creditor->Country;
                                                $data["Authorisation"] = $creditor->Authorisation;
                                                $data["Term"] = $creditor->Term;
                                                $data["CountryLong"] = $array_country[$data["Country"]];
                                                echo json_encode($data);
                                                exit;
                                            }
                                        } else {
                                            if(isset($_GET["action"]) && $_GET["action"] == "searchtld") {
                                                require_once "class/topleveldomain.php";
                                                $tld = new topleveldomain();
                                                $session = isset($_SESSION["search.tld"]) ? $_SESSION["search.tld"] : [];
                                                $fields = ["Tld", "Registrar", "Name"];
                                                $sort = isset($session["sort"]) ? $session["sort"] : "Tld";
                                                $order = isset($session["order"]) ? $session["order"] : "ASC";
                                                $limit = isset($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : "1";
                                                $searchat = "Tld|Name";
                                                $searchfor = isset($session["searchfor"]) && $session["searchfor"] ? $session["searchfor"] : "";
                                                if(substr($searchfor, 0, 1) == ".") {
                                                    $searchfor = substr($searchfor, 1);
                                                }
                                                $group_id = "";
                                                $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : min(10, MAX_RESULTS_LIST);
                                                $tlds = $tld->all($fields, $sort, $order, $limit, $searchat, $searchfor, $group_id, $show_results);
                                                $_SESSION["search.tld"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "searchfor" => $searchfor];
                                                $results_per_page = $show_results;
                                                $current_page = $limit;
                                                $current_page_url = "XMLRequest.php?action=searchtld";
                                                require_once "views/dialog.search.tld.php";
                                                exit;
                                            }
                                            if(isset($_POST["action"]) && $_POST["action"] == "delete_session_file") {
                                                @unlink("temp/" . $_SESSION["force_download"]);
                                                unset($_SESSION["force_download"]);
                                                exit;
                                            }
                                            if(isset($_GET["action"]) && $_GET["action"] == "getIPFromHost") {
                                                $ip = gethostbyname(trim(urlencode($_GET["hostname"])));
                                                if(long2ip(ip2long($ip)) != $ip || $ip == "") {
                                                    echo __("cannot obtain IP");
                                                } else {
                                                    echo $ip;
                                                }
                                                exit;
                                            }
                                            if(isset($_POST["action"]) && $_POST["action"] == "widgets_save") {
                                                require_once "class/widget.php";
                                                $widget = new widget();
                                                parse_str($_POST["order"], $order);
                                                if(isset($order) && is_array($order)) {
                                                    $widget->updateOrder($order["widget"]);
                                                }
                                                exit;
                                            }
                                            if(isset($_POST["action"]) && $_POST["action"] == "widgets_getoptions") {
                                                require_once "class/widget.php";
                                                $widget = new widget();
                                                if(isset($_POST["widget"])) {
                                                    $options = $widget->addWidgetOptions($_POST["widget"]);
                                                } elseif(isset($_POST["widgetID"])) {
                                                    $options = $widget->getWidgetOptions($_POST["widgetID"]);
                                                }
                                                $x = 1;
                                                if($options) {
                                                    foreach ($options as $mainkey => $array) {
                                                        if($mainkey != "current") {
                                                            $ret = "<strong class=\"title\">" . __($mainkey) . "</strong>";
                                                            $ret .= "<select class=\"select1\" name=\"options[" . $x . "]\">";
                                                            foreach ($array as $key => $value) {
                                                                if($options["current"] == $key) {
                                                                    $ret .= "<option value=\"" . $key . "\" selected=\"selected\">" . __($value) . "</option>";
                                                                } else {
                                                                    $ret .= "<option value=\"" . $key . "\">" . __($value) . "</option>";
                                                                }
                                                            }
                                                            $ret .= "</select>";
                                                            $x++;
                                                        }
                                                    }
                                                } else {
                                                    $ret = __("no options found for this widget");
                                                }
                                                echo $ret;
                                                exit;
                                            } else {
                                                if(isset($_POST["action"]) && $_POST["action"] == "add_element") {
                                                    require_once "config.php";
                                                    require_once "class/template.php";
                                                    $templateelement = new templateelement();
                                                    $templateelement->Name = "Nieuwe regel";
                                                    $templateelement->Template = $_POST["layout_id"];
                                                    $templateelement->add();
                                                    echo $templateelement->Identifier;
                                                    exit;
                                                }
                                                if(isset($_POST["action"]) && $_POST["action"] == "delete_element") {
                                                    if(isset($_POST["element"])) {
                                                        require_once "config.php";
                                                        require_once "class/template.php";
                                                        $templateelement = new templateelement();
                                                        $templateelement->Identifier = esc($_POST["element"]);
                                                        $templateelement->delete();
                                                        exit;
                                                    }
                                                } elseif(isset($_POST["action"]) && $_POST["action"] == "empty_location") {
                                                    require_once "config.php";
                                                    require_once "class/template.php";
                                                    $template = new template();
                                                    $template->Identifier = esc($_POST["Identifier"]);
                                                    if(isset($_POST["type"]) && $_POST["type"] == "print") {
                                                        $template->updatePostLocation("empty", "");
                                                    } else {
                                                        $template->updateLocation("empty", "");
                                                    }
                                                } elseif(isset($_POST["action"]) && $_POST["action"] == "restore_location") {
                                                    require_once "config.php";
                                                    require_once "class/template.php";
                                                    $template = new template();
                                                    $template->Identifier = esc($_POST["Identifier"]);
                                                    if(isset($_POST["type"]) && $_POST["type"] == "print") {
                                                        $template->restoreLocation("print");
                                                    } else {
                                                        $template->restoreLocation("download");
                                                    }
                                                } elseif(isset($_POST["action"]) && $_POST["action"] == "edit_element") {
                                                    require_once "config.php";
                                                    require_once "class/template.php";
                                                    $templateelement = new templateelement();
                                                    $templateelement->Template = esc($_POST["Template"]);
                                                    $templateelement->Identifier = esc($_POST["Identifier"]);
                                                    $templateelement->Name = esc($_POST["Name"]);
                                                    $templateelement->Type = esc($_POST["Type"]);
                                                    $templateelement->Visible = esc($_POST["Visible"]);
                                                    $templateelement->X = esc($_POST["X"]);
                                                    $templateelement->Y = esc($_POST["Y"]);
                                                    $templateelement->Value = esc($_POST["Value"]);
                                                    $templateelement->Font = esc($_POST["Font"]);
                                                    $templateelement->FontSize = esc($_POST["FontSize"]);
                                                    $templateelement->FontStyle = esc($_POST["FontStyle"]);
                                                    $templateelement->Align = esc($_POST["Align"]);
                                                    $templateelement->Page = esc($_POST["Page"]);
                                                    $templateelement->Width = esc($_POST["Width"]);
                                                    $templateelement->Height = esc($_POST["Height"]);
                                                    $templateelement->edit();
                                                } elseif(isset($_POST["action"]) && $_POST["action"] == "get_element") {
                                                    if(isset($_POST["element"])) {
                                                        require_once "config.php";
                                                        require_once "class/template.php";
                                                        $templateelement = new templateelement();
                                                        $templateelement->Identifier = esc($_POST["element"]);
                                                        if($templateelement->show()) {
                                                            $data = [];
                                                            $data["Name"] = htmlspecialchars_decode($templateelement->Name);
                                                            $data["Type"] = $templateelement->Type;
                                                            $data["Visible"] = $templateelement->Visible;
                                                            $data["Page"] = $templateelement->Page;
                                                            $data["Font"] = $templateelement->Font;
                                                            $data["FontSize"] = $templateelement->FontSize;
                                                            $data["FontStyle"] = $templateelement->FontStyle;
                                                            $data["Align"] = $templateelement->Align;
                                                            $data["X"] = $templateelement->X;
                                                            $data["Y"] = $templateelement->Y;
                                                            $data["Width"] = $templateelement->Width;
                                                            $data["Height"] = $templateelement->Height;
                                                            $data["Value"] = htmlspecialchars_decode(htmlspecialchars_decode($templateelement->Value));
                                                            echo json_encode($data);
                                                            exit;
                                                        }
                                                    }
                                                } elseif(isset($_POST["action"]) && $_POST["action"] == "get_pdf_variable_objects") {
                                                    require_once "config.php";
                                                    $data = [];
                                                    if(defined("PDF_MODULE") && PDF_MODULE == "tcpdf") {
                                                        require_once "class/templateblock.php";
                                                        $templateblock = new templateblock();
                                                        $items = $templateblock->listBlocks(esc($_POST["template"]));
                                                        foreach ($items as $k => $v) {
                                                            if(is_numeric($k)) {
                                                                $tmp_value = is_array($v["value"]) ? serialize($v["value"]) : $v["value"];
                                                                if(strpos($tmp_value, "[domain->") !== false) {
                                                                    $data["domain"] = true;
                                                                }
                                                                if(strpos($tmp_value, "[hosting->") !== false) {
                                                                    $data["hosting"] = true;
                                                                }
                                                            }
                                                        }
                                                    } else {
                                                        require_once "class/template.php";
                                                        $templateelement = new templateelement();
                                                        $data = ["domain" => false, "hosting" => false];
                                                        $items = $templateelement->all(["Value"], false, false, -1, false, false, esc($_POST["template"]));
                                                        foreach ($items as $k => $v) {
                                                            if(is_numeric($k)) {
                                                                if(strpos($v["Value"], "[domain-&gt;") !== false) {
                                                                    $data["domain"] = true;
                                                                }
                                                                if(strpos($v["Value"], "[hosting-&gt;") !== false) {
                                                                    $data["hosting"] = true;
                                                                }
                                                            }
                                                        }
                                                    }
                                                    if($data["domain"] === true) {
                                                        require_once "class/domain.php";
                                                        $domain = new domain();
                                                        $fields = ["Domain", "Tld"];
                                                        $list_debtor_domains = $domain->all($fields, "Domain", "ASC", -1, "Debtor", esc($_POST["debtor"]));
                                                        $data["domain"] = "<option value=\"\">" . __("make your choice") . "</option>";
                                                        foreach ($list_debtor_domains as $k => $v) {
                                                            if(is_numeric($k)) {
                                                                $data["domain"] .= "<option value=\"" . $k . "\">" . $v["Domain"] . "." . $v["Tld"] . "</option>";
                                                            }
                                                        }
                                                    }
                                                    if($data["hosting"] === true) {
                                                        require_once "class/hosting.php";
                                                        $hosting = new hosting();
                                                        $fields = ["Username"];
                                                        $list_debtor_hosting = $hosting->all($fields, "Username", "ASC", -1, "Debtor", esc($_POST["debtor"]));
                                                        $data["hosting"] = "<option value=\"\">" . __("make your choice") . "</option>";
                                                        foreach ($list_debtor_hosting as $k => $v) {
                                                            if(is_numeric($k)) {
                                                                $data["hosting"] .= "<option value=\"" . $k . "\">" . $v["Username"] . "</option>";
                                                            }
                                                        }
                                                    }
                                                    echo json_encode($data);
                                                    exit;
                                                } elseif(isset($_POST["action"]) && $_POST["action"] == "get_uploaded_files") {
                                                    if(isset($_SESSION["tmp_uploaded_file"]) && is_array($_SESSION["tmp_uploaded_file"])) {
                                                        require_once "class/template.php";
                                                        $template = new template();
                                                        foreach ($_SESSION["tmp_uploaded_file"] as $key => $value) {
                                                            if(strstr($value["FileName"], "TemplateOther") && !strstr($value["FileName"], "/")) {
                                                                $template->Identifier = substr($value["FileName"], strlen("TemplateOther"));
                                                                $template->show();
                                                                $_SESSION["tmp_uploaded_file"][$key]["FileName"] = $template->Name . " <span class=\"fontsmall c4\">- " . __("generated") . "</span>";
                                                            }
                                                        }
                                                        $data = $_SESSION["tmp_uploaded_file"];
                                                        if($_SESSION["tmp_uploaded_file"][0]["FileType"] == "creditinvoice" && $_SESSION["tmp_uploaded_file"][0]["FileExtension"] == "xml") {
                                                            $data[0]["redirect"] = "creditors.php?page=add_invoice&UBL=" . $_SESSION["tmp_uploaded_file"][0]["FilePath"];
                                                        }
                                                        unset($_SESSION["tmp_uploaded_file"]);
                                                        echo json_encode($data);
                                                        exit;
                                                    } else {
                                                        echo json_encode([]);
                                                        exit;
                                                    }
                                                } elseif(isset($_POST["action"]) && $_POST["action"] == "get_files_total") {
                                                    if($_POST["files"]) {
                                                        $data = [];
                                                        $data["FileSize"] = 0;
                                                        $arr = [];
                                                        parse_str($_POST["files"], $arr);
                                                        foreach ($arr as $files) {
                                                            foreach ($files as $key => $value) {
                                                                if(is_numeric($value)) {
                                                                    $dbResults = Database_Model::getInstance()->getOne("HostFact_Documents", ["Size"])->where("id", $value)->execute();
                                                                    if($dbResults) {
                                                                        $data["FileSize"] = $data["FileSize"] + $dbResults->Size;
                                                                    }
                                                                } elseif(file_exists($value)) {
                                                                    $data["FileSize"] = $data["FileSize"] + filesize($value);
                                                                }
                                                            }
                                                        }
                                                        $size = getFileSizeUnit($data["FileSize"]);
                                                        $data["Counter"] = count($files);
                                                        $data["FileSize"] = $size["size"];
                                                        $data["FileSizeUnit"] = $size["unit"];
                                                        echo json_encode($data);
                                                        exit;
                                                    } else {
                                                        $data["Counter"] = 0;
                                                        echo json_encode($data);
                                                        exit;
                                                    }
                                                } elseif(isset($_POST["action"]) && $_POST["action"] == "get_stats_summary") {
                                                    $x = 60;
                                                    if(!file_exists("temp/stats_summary.php") || filemtime("temp/stats_summary.php") + $x < time()) {
                                                        require_once "class/neworders.php";
                                                        $neworder = new neworder();
                                                        $neworder_received = $neworder->all(["id"], "", "", "", "", "", "0");
                                                        $neworder_busy = $neworder->all(["id"], "", "", "", "", "", "1|2");
                                                        $neworder_count = $neworder_busy["CountRows"] + $neworder_received["CountRows"];
                                                        require_once "class/ticket.php";
                                                        $tickets = new ticket();
                                                        $tickets_new = $tickets->all(["id"], "", "", "", "", "", "0");
                                                        require_once "class/invoice.php";
                                                        $invoices = new invoice();
                                                        $fields = ["InvoiceCode"];
                                                        $invoice_concept = $invoices->all($fields, false, false, "-1", false, false, "0");
                                                        $invoice_reminders = $invoices->all($fields, false, false, "-1", false, false, "reminders");
                                                        $invoice_paused = $invoices->all($fields, false, false, "-1", false, false, "substatus_paused");
                                                        if(INT_SUPPORT_SUMMATIONS) {
                                                            $invoice_summations = $invoices->all($fields, false, false, "-1", false, false, "summations");
                                                        }
                                                        require_once "class/periodic.php";
                                                        $subscription = new periodic();
                                                        $fields = ["Description"];
                                                        $subscriptions_to_invoice = $subscription->all($fields, false, false, "-1", false, false, "nextdatepassed");
                                                        require_once "class/email.php";
                                                        $email = new email();
                                                        $emailresult = $email->all(["Status"], "", "", "", "", "", "0");
                                                        require_once "class/clientareachange.php";
                                                        $ClientareaChange_Model = new ClientareaChange_Model();
                                                        $options = [];
                                                        $options["filter"] = "pending|error";
                                                        $result = $ClientareaChange_Model->listChanges($options);
                                                        if(!empty($result)) {
                                                            $error_class->ClientareaChanges = count($result);
                                                        }
                                                        require_once "class/domain.php";
                                                        $domain = new domain();
                                                        $domain_error = $domain->all(["id"], "", "", "", "", "", "7");
                                                        $domain_to_request = $domain->all(["id"], "", "", "", "", "", "3");
                                                        $domain_progress = $domain->all(["id"], "", "", "", "", "", "6");
                                                        require_once "class/hosting.php";
                                                        $hosting = new hosting();
                                                        $hosting_to_request = $hosting->all(["id"], "", "", "", "", "", "3");
                                                        $hosting_error = $hosting->all(["id"], "", "", "", "", "", "7");
                                                        require_once "class/terminationprocedure.php";
                                                        $termination = new Termination_Model();
                                                        $terminations_to_be_approved = $termination->listTerminations(["results_per_page" => "all", "filter" => "approval"]);
                                                        $termination_action = new Action_Model();
                                                        $termination_actions_today = $termination_action->listActions(["results_per_page" => "all", "filter" => "pending", "period" => "today"]);
                                                        $termination_actions_with_error = $termination_action->listActions(["results_per_page" => "all", "filter" => "error"]);
                                                        if(SDD_ID) {
                                                            require_once "class/directdebit.php";
                                                            $directdebit = new directdebit();
                                                            $directdebit_batches = $directdebit->listBatches("downloadable");
                                                        }
                                                        $function = "<?php function get_stats_summary(){ ?>";
                                                        $extra_linebreak = false;
                                                        if(0 < (int) $neworder_count) {
                                                            $function .= "<?php if(U_ORDER_SHOW){ ?><a class=\"summary_stat\" href=\"orders.php\"><div class=\"number green\">" . $neworder_count . "</div><div class=\"description\">";
                                                            $function .= __("new orders");
                                                            $function .= "</div><br clear=\"all\" /></a><?php } ?>";
                                                            $extra_linebreak = true;
                                                        }
                                                        if(0 < (int) $subscriptions_to_invoice["CountRows"]) {
                                                            $function .= "<?php if(U_DOMAIN_SHOW || U_HOSTING_SHOW || U_SERVICE_SHOW){ ?><a class=\"summary_stat\" onclick=\"save('subscription.overview','status','nextdatepassed', 'subscriptions.php');\"><div class=\"number gray\">" . $subscriptions_to_invoice["CountRows"] . "</div><div class=\"description\">";
                                                            $function .= __("subscriptions to invoice");
                                                            $function .= "</div><br clear=\"all\" /></a><?php } ?>";
                                                            $extra_linebreak = true;
                                                        }
                                                        if(!empty($terminations_to_be_approved)) {
                                                            $function .= "<?php if(U_DOMAIN_SHOW || U_HOSTING_SHOW || U_SERVICE_SHOW){ ?><a class=\"summary_stat\" onclick=\"save('backoffice_table.list_terminations','filter','approval', 'services.php?page=terminations');\"><div class=\"number gray\">" . count($terminations_to_be_approved) . "</div><div class=\"description\">";
                                                            $function .= __("terminations to be approved");
                                                            $function .= "</div><br clear=\"all\" /></a><?php } ?>";
                                                            $extra_linebreak = true;
                                                        }
                                                        if(!empty($termination_actions_today)) {
                                                            $function .= "<?php if(U_DOMAIN_SHOW || U_HOSTING_SHOW || U_SERVICE_SHOW){ ?><a class=\"summary_stat\" onclick=\"save('backoffice_table.list_termination_actions','filter','pending', 'services.php?page=termination_actions');\"><div class=\"number gray\">" . count($termination_actions_today) . "</div><div class=\"description\">";
                                                            $function .= __("termination actions to be executed today");
                                                            $function .= "</div><br clear=\"all\" /></a><?php } ?>";
                                                            $extra_linebreak = true;
                                                        }
                                                        if(!empty($termination_actions_with_error)) {
                                                            $function .= "<?php if(U_DOMAIN_SHOW || U_HOSTING_SHOW || U_SERVICE_SHOW){ ?><a class=\"summary_stat\" onclick=\"save('backoffice_table.list_termination_actions','filter','error', 'services.php?page=termination_actions');\"><div class=\"number red\">" . count($termination_actions_with_error) . "</div><div class=\"description\">";
                                                            $function .= __("termination actions with errors");
                                                            $function .= "</div><br clear=\"all\" /></a><?php } ?>";
                                                            $extra_linebreak = true;
                                                        }
                                                        if(0 < (int) $invoice_concept["CountRows"]) {
                                                            $function .= "<?php if(U_INVOICE_SHOW){ ?><a class=\"summary_stat\" onclick=\"save('backoffice_table.list_invoice','filter','0', 'invoices.php');\"><div class=\"number gray\">" . $invoice_concept["CountRows"] . "</div><div class=\"description\">";
                                                            $function .= __("concept invoices");
                                                            $function .= "</div><br clear=\"all\" /></a><?php } ?>";
                                                            $extra_linebreak = true;
                                                        }
                                                        if(0 < (int) $invoice_reminders["CountRows"]) {
                                                            $function .= "<?php if(U_INVOICE_SHOW){ ?><a class=\"summary_stat\" onclick=\"save('backoffice_table.list_invoice','filter','reminders', 'invoices.php');\"><div class=\"number gray\">" . $invoice_reminders["CountRows"] . "</div><div class=\"description\">";
                                                            $function .= __("reminders to send");
                                                            $function .= "</div><br clear=\"all\" /></a><?php } ?>";
                                                            $extra_linebreak = true;
                                                        }
                                                        if(INT_SUPPORT_SUMMATIONS && 0 < (int) $invoice_summations["CountRows"]) {
                                                            $function .= "<?php if(U_INVOICE_SHOW){ ?><a class=\"summary_stat\" onclick=\"save('backoffice_table.list_invoice','filter','summations', 'invoices.php');\"><div class=\"number gray\">" . $invoice_summations["CountRows"] . "</div><div class=\"description\">";
                                                            $function .= __("summations to send");
                                                            $function .= "</div><br clear=\"all\" /></a><?php } ?>";
                                                            $extra_linebreak = true;
                                                        }
                                                        if(SDD_ID && !empty($directdebit_batches["current"])) {
                                                            $function .= "<?php if(U_INVOICE_SHOW){ ?><a class=\"summary_stat\" href=\"directdebit.php\"><div class=\"number green\">" . count($directdebit_batches["current"]) . "</div><div class=\"description\">";
                                                            $function .= __("direct debit batches to download");
                                                            $function .= "</div><br clear=\"all\" /></a><?php } ?>";
                                                            $extra_linebreak = true;
                                                        }
                                                        if(0 < (int) $invoice_paused["CountRows"]) {
                                                            $function .= "<?php if(U_INVOICE_SHOW){ ?><a class=\"summary_stat\" onclick=\"save('backoffice_table.list_invoice','filter','substatus_paused','invoices.php');\"><div class=\"number gray\">" . $invoice_paused["CountRows"] . "</div><div class=\"description\">";
                                                            $function .= __("paused invoices");
                                                            $function .= "</div><br clear=\"all\" /></a><?php } ?>";
                                                            $extra_linebreak = true;
                                                        }
                                                        if($extra_linebreak) {
                                                            $function .= "<br clear=\"all\" />";
                                                            $extra_linebreak = false;
                                                        }
                                                        if(0 < (int) $domain_to_request["CountRows"]) {
                                                            $function .= "<?php if(U_DOMAIN_SHOW){ ?><a class=\"summary_stat\" onclick=\"save('domain.overview','status','3', 'domains.php');\"><div class=\"number green\">" . $domain_to_request["CountRows"] . "</div><div class=\"description\">";
                                                            $function .= __("domains to request manually");
                                                            $function .= "</div><br clear=\"all\" /></a><?php } ?>";
                                                            $extra_linebreak = true;
                                                        }
                                                        if(0 < (int) $domain_progress["CountRows"]) {
                                                            $function .= "<?php if(U_DOMAIN_SHOW){ ?><a class=\"summary_stat\" onclick=\"save('domain.overview','status','6', 'domains.php');\"><div class=\"number gray\">" . $domain_progress["CountRows"] . "</div><div class=\"description\">";
                                                            $function .= __("domains in progress");
                                                            $function .= "</div><br clear=\"all\" /></a><?php } ?>";
                                                            $extra_linebreak = true;
                                                        }
                                                        if(0 < (int) $domain_error["CountRows"]) {
                                                            $function .= "<?php if(U_DOMAIN_SHOW){ ?><a class=\"summary_stat\" onclick=\"save('domain.overview','status','7', 'domains.php');\"><div class=\"number red\">" . $domain_error["CountRows"] . "</div><div class=\"description\">";
                                                            $function .= __("domains with errors");
                                                            $function .= "</div><br clear=\"all\" /></a><?php } ?>";
                                                            $extra_linebreak = true;
                                                        }
                                                        if(DOMAIN_HOME_WARNING != "off") {
                                                            $dom_expired = $dom_expire_today = $dom_expire_almost = 0;
                                                            $result = Database_Model::getInstance()->get("HostFact_Domains", ["HostFact_Domains.id", "HostFact_Domains.Domain", "HostFact_Domains.Tld", "HostFact_Domains.ExpirationDate", "HostFact_Domains.Registrar", "DATE_SUB(HostFact_Domains.`ExpirationDate`, INTERVAL :domainwarning DAY) as WarnDate"])->join("HostFact_Registrar", "HostFact_Registrar.id = HostFact_Domains.Registrar")->where("HostFact_Domains.Status", "4")->where("HostFact_Domains.ExpirationDate", ["!=" => "0000-00-00"])->where("DATE_SUB(HostFact_Domains.`ExpirationDate`, INTERVAL :domainwarning DAY)", ["<=" => ["RAW" => "CURDATE()"]])->orWhere([["HostFact_Domains.DomainAutoRenew", "off"], ["HostFact_Domains.DomainAutoRenew", ""], ["AND" => [["HostFact_Domains.DomainAutoRenew", "on"], ["OR" => [["HostFact_Domains.IsSynced", ["!=" => "yes"]], ["HostFact_Registrar.Class", ""]]]]]])->bindValue("domainwarning", DOMAINWARNING)->execute();
                                                            if($result && is_array($result)) {
                                                                foreach ($result as $value) {
                                                                    if($value->ExpirationDate < date("Y-m-d")) {
                                                                        if(DOMAIN_HOME_WARNING == "on") {
                                                                            $dom_expired++;
                                                                        }
                                                                    } elseif($value->ExpirationDate == date("Y-m-d")) {
                                                                        $dom_expire_today++;
                                                                    } else {
                                                                        $dom_expire_almost++;
                                                                    }
                                                                }
                                                            }
                                                            if(0 < $dom_expire_today) {
                                                                $function .= "<?php if(U_DOMAIN_SHOW){ ?><a class=\"summary_stat\" onclick=\"save('domain.overview','status','expiretoday', 'domains.php');\"><div class=\"number red\">" . $dom_expire_today . "</div><div class=\"description\">";
                                                                $function .= __("domains expire today");
                                                                $function .= "</div><br clear=\"all\" /></a><?php } ?>";
                                                                $extra_linebreak = true;
                                                            }
                                                            if(0 < $dom_expire_almost) {
                                                                $function .= "<?php if(U_DOMAIN_SHOW){ ?><a class=\"summary_stat\" onclick=\"save('domain.overview','status','expirealmost', 'domains.php');\"><div class=\"number gray\">" . $dom_expire_almost . "</div><div class=\"description\">";
                                                                $function .= sprintf(__("domains expire almost"), DOMAINWARNING);
                                                                $function .= "</div><br clear=\"all\" /></a><?php } ?>";
                                                                $extra_linebreak = true;
                                                            }
                                                            if(0 < $dom_expired) {
                                                                $function .= "<?php if(U_DOMAIN_SHOW){ ?><a class=\"summary_stat\" onclick=\"save('domain.overview','status','expired', 'domains.php');\"><div class=\"number gray\">" . $dom_expired . "</div><div class=\"description\">";
                                                                $function .= __("domains alreadys expired");
                                                                $function .= "</div><br clear=\"all\" /></a><?php } ?>";
                                                                $extra_linebreak = true;
                                                            }
                                                        }
                                                        if($extra_linebreak) {
                                                            $function .= "<br clear=\"all\" />";
                                                            $extra_linebreak = false;
                                                        }
                                                        if(0 < (int) $hosting_to_request["CountRows"]) {
                                                            $function .= "<?php if(U_HOSTING_SHOW){ ?><a class=\"summary_stat\" onclick=\"save('hosting.overview','status','3', 'hosting.php');\"><div class=\"number green\">" . $hosting_to_request["CountRows"] . "</div><div class=\"description\">";
                                                            $function .= __("hosting to request manually");
                                                            $function .= "</div><br clear=\"all\" /></a><?php } ?>";
                                                            $extra_linebreak = true;
                                                        }
                                                        if(0 < (int) $hosting_error["CountRows"]) {
                                                            $function .= "<?php if(U_HOSTING_SHOW){ ?><a class=\"summary_stat\" onclick=\"save('hosting.overview','status','7', 'hosting.php');\"><div class=\"number red\">" . $hosting_error["CountRows"] . "</div><div class=\"description\">";
                                                            $function .= __("hosting with errors");
                                                            $function .= "</div><br clear=\"all\" /></a><?php } ?>";
                                                            $extra_linebreak = true;
                                                        }
                                                        if($extra_linebreak) {
                                                            $function .= "<br clear=\"all\" />";
                                                            $extra_linebreak = false;
                                                        }
                                                        if(0 < (int) $tickets_new["CountRows"]) {
                                                            $function .= "<?php if(U_TICKET_SHOW){ ?><a class=\"summary_stat\" onclick=\"save('ticket.overview','status','0', 'tickets.php');\"><div class=\"number gray\">" . $tickets_new["CountRows"] . "</div><div class=\"description\">";
                                                            $function .= __("new tickets");
                                                            $function .= "</div><br clear=\"all\" /></a><?php } ?>";
                                                        }
                                                        if(isset($error_class->ClientareaChanges) && 0 < (int) $error_class->ClientareaChanges) {
                                                            $function .= "<?php if(U_DEBTOR_SHOW || U_DOMAIN_SHOW){ ?><a class=\"summary_stat\" onclick=\"save('backoffice_table.list_clientarea_changes_processed','filter', 'pending|error', 'clientareachanges.php');\"><div class=\"number gray\">" . $error_class->ClientareaChanges . "</div><div class=\"description\">";
                                                            $function .= __("debtor modifications");
                                                            $function .= "</div><br clear=\"all\" /></a><?php } ?>";
                                                        }
                                                        if(0 < (int) $emailresult["CountRows"]) {
                                                            $function .= "<a class=\"summary_stat\" href=\"emails.php\"><div class=\"number gray\">" . $emailresult["CountRows"] . "</div><div class=\"description\">";
                                                            $function .= __("batch emails to be sent");
                                                            $function .= "</div><br clear=\"all\" /></a>";
                                                        }
                                                        $function .= do_filter("sidebar_notifications", "");
                                                        $function .= "<?php } ?>";
                                                        @file_put_contents("temp/stats_summary.php", $function);
                                                    }
                                                    include_once "temp/stats_summary.php";
                                                    if(function_exists("get_stats_summary")) {
                                                        get_stats_summary();
                                                    }
                                                } elseif(isset($_POST["action"]) && $_POST["action"] == "get_new_codes_examples") {
                                                    $data = [];
                                                    $get_code = isset($_POST["get"]) && $_POST["get"] ? esc($_POST["get"]) : "all";
                                                    $prefix = isset($_POST["prefix"]) && $_POST["prefix"] ? esc($_POST["prefix"]) : "";
                                                    $number = isset($_POST["number"]) && $_POST["number"] ? esc($_POST["number"]) : "";
                                                    if(!is_numeric($number)) {
                                                        $prefix = "-";
                                                        $number = "";
                                                    }
                                                    switch ($get_code) {
                                                        case "debtorcode":
                                                            require_once "class/debtor.php";
                                                            $tmp = new debtor();
                                                            $data["debtorcode"] = $tmp->newDebtorCode($prefix, $number);
                                                            break;
                                                        case "creditorcode":
                                                            require_once "class/creditor.php";
                                                            $tmp = new creditor();
                                                            $data["creditorcode"] = $tmp->newCreditorCode($prefix, $number);
                                                            break;
                                                        case "productcode":
                                                            require_once "class/product.php";
                                                            $tmp = new product();
                                                            $data["productcode"] = $tmp->newProductCode($prefix, $number);
                                                            break;
                                                        case "ordercode":
                                                            require_once "class/neworders.php";
                                                            $tmp = new neworder();
                                                            $data["ordercode"] = $tmp->newOrderCode($prefix, $number);
                                                            break;
                                                        case "pricequotecode":
                                                            require_once "class/pricequote.php";
                                                            $tmp = new pricequote();
                                                            $data["pricequotecode"] = $tmp->newPriceQuoteCode($prefix, $number);
                                                            break;
                                                        case "invoicecode":
                                                            require_once "class/invoice.php";
                                                            $tmp = new invoice();
                                                            $data["invoicecode"] = $tmp->newInvoiceCode($prefix, $number);
                                                            break;
                                                        case "creditinvoicecode":
                                                            require_once "class/creditinvoice.php";
                                                            $tmp = new creditinvoice();
                                                            $data["creditinvoicecode"] = $tmp->newCreditInvoiceCode($prefix, $number);
                                                            break;
                                                        default:
                                                            require_once "class/debtor.php";
                                                            $tmp = new debtor();
                                                            $data["debtorcode"] = $tmp->newDebtorCode();
                                                            require_once "class/creditor.php";
                                                            $tmp = new creditor();
                                                            $data["creditorcode"] = $tmp->newCreditorCode();
                                                            require_once "class/product.php";
                                                            $tmp = new product();
                                                            $data["productcode"] = $tmp->newProductCode();
                                                            require_once "class/neworders.php";
                                                            $tmp = new neworder();
                                                            $data["ordercode"] = $tmp->newOrderCode();
                                                            require_once "class/pricequote.php";
                                                            $tmp = new pricequote();
                                                            $data["pricequotecode"] = $tmp->newPriceQuoteCode();
                                                            require_once "class/invoice.php";
                                                            $tmp = new invoice();
                                                            $data["invoicecode"] = $tmp->newInvoiceCode();
                                                            require_once "class/creditinvoice.php";
                                                            $tmp = new creditinvoice();
                                                            $data["creditinvoicecode"] = $tmp->newCreditInvoiceCode();
                                                            echo json_encode($data);
                                                            exit;
                                                    }
                                                } elseif(isset($_POST["action"]) && $_POST["action"] == "kb_get_articles") {
                                                    $parameters = [];
                                                    if(isset($_POST["article_search"])) {
                                                        $parameters["article_search"] = esc($_POST["article_search"]);
                                                    }
                                                    if(isset($_POST["page1"])) {
                                                        $parameters["page1"] = esc($_POST["page1"]);
                                                    }
                                                    if(isset($_POST["page2"])) {
                                                        $parameters["page2"] = esc($_POST["page2"]);
                                                    }
                                                    $result = kb_execute_query($parameters);
                                                    if(!isset($result["knowledgebase"]["info"]["results"])) {
                                                        exit;
                                                    }
                                                    if((int) $result["knowledgebase"]["info"]["results"] === 0) {
                                                        echo "\t\t<center><strong>";
                                                        echo sprintf(__("knowledgebase results found"), $result["knowledgebase"]["info"]["results"]);
                                                        echo "</strong></center>\n\t\t<div class=\"hr\"></div>\n\t\t";
                                                        if(isset($result["knowledgebase"]["info"]["noresult"])) {
                                                            echo base64_decode($result["knowledgebase"]["info"]["noresult"]);
                                                        }
                                                        exit;
                                                    }
                                                    if(isset($result["knowledgebase"]["articles"]["article"]["article_id"])) {
                                                        $result["knowledgebase"]["articles"]["article"] = [$result["knowledgebase"]["articles"]["article"]];
                                                    }
                                                    echo "\t<center><strong>";
                                                    echo $result["knowledgebase"]["info"]["results"] == 1 ? __("knowledgebase one result found") : sprintf(__("knowledgebase results found"), $result["knowledgebase"]["info"]["results"]);
                                                    echo "</strong></center>\n\t<div class=\"hr\"></div>\n\t\t\t\t\n\t";
                                                    foreach ($result["knowledgebase"]["articles"]["article"] as $tmp_article) {
                                                        echo "\t\t<div id=\"kb_article_";
                                                        echo $tmp_article["article_id"];
                                                        echo "\" class=\"kb_article\">\n\t\t\t<h3>";
                                                        echo $tmp_article["title"];
                                                        echo "</h3><br />\n\t\t\t";
                                                        echo htmlspecialchars_decode(base64_decode($tmp_article["description"]));
                                                        echo "\t\t\t<br />\n\t\t\t<a class=\"kb_article_more fontsmall float_right\">";
                                                        echo __("knowledgebase show more");
                                                        echo "</a>\n\t\t\t<br clear=\"both\" />\n\t\t</div>\n\t\t<div class=\"hr\"></div>\n\t";
                                                    }
                                                    echo "\t";
                                                    exit;
                                                } else {
                                                    if(isset($_POST["action"]) && $_POST["action"] == "kb_get_article") {
                                                        $parameters = [];
                                                        if(isset($_POST["article_id"])) {
                                                            $parameters["article_id"] = esc($_POST["article_id"]);
                                                        }
                                                        $result = kb_execute_query($parameters);
                                                        if(!isset($result["knowledgebase"]["info"]["results"])) {
                                                            exit;
                                                        }
                                                        if((int) $result["knowledgebase"]["info"]["results"] === 0) {
                                                            echo "\t\t<center><strong onclick=\"\$('#kb_scroll').show();\$('#kb_scroll2').hide();\" class=\"pointer\">";
                                                            echo __("knowledgebase back to results");
                                                            echo "</strong></center>\n\t\t<div class=\"hr\"></div>\n\t\t";
                                                            if(isset($result["knowledgebase"]["info"]["noresult"])) {
                                                                echo base64_decode($result["knowledgebase"]["info"]["noresult"]);
                                                            }
                                                            exit;
                                                        }
                                                        echo "\t<center><strong onclick=\"\$('#kb_scroll').show();\$('#kb_scroll2').hide();\" class=\"pointer\">";
                                                        echo __("knowledgebase back to results");
                                                        echo "</strong></center>\n\t<div class=\"hr\"></div>\t\t\t\n\t";
                                                        if(isset($result["knowledgebase"]["article"])) {
                                                            echo base64_decode($result["knowledgebase"]["article"]["html"]);
                                                        }
                                                        exit;
                                                    }
                                                    if(isset($_POST["action"]) && $_POST["action"] == "save_note") {
                                                        if(isset($_POST["Notes"])) {
                                                            require_once "class/employee.php";
                                                            $account = new employee();
                                                            $account->Notes = esc($_POST["Notes"]);
                                                            $account->updateNotes(esc($_SESSION["UserPro"]));
                                                        }
                                                        exit;
                                                    }
                                                    if(isset($_POST["action"]) && $_POST["action"] == "change_ticket_status") {
                                                        require_once "class/ticket.php";
                                                        $ticket = new ticket();
                                                        $ticket->Identifier = esc($_POST["id"]);
                                                        if($ticket->show()) {
                                                            $ticket->changeStatus(esc($_POST["status"]));
                                                        }
                                                    } else {
                                                        if(isset($_POST["action"]) && $_POST["action"] == "check_domain_availability") {
                                                            require_once "class/domain.php";
                                                            $domain = new domain();
                                                            $domain->Registrar = esc($_POST["registrar"]);
                                                            $domain->Domain = esc($_POST["sld"]);
                                                            $domain->Tld = esc($_POST["tld"]);
                                                            $result = $domain->check(true);
                                                            if($result === false) {
                                                                $result = $domain->publicCheck();
                                                            }
                                                            if($result !== false) {
                                                                if($domain->Type == "transfer") {
                                                                    echo json_encode(["result" => "transfer", "msg" => __("domain status check unavailable")]);
                                                                    exit;
                                                                }
                                                                echo json_encode(["result" => "register", "msg" => __("domain status check available")]);
                                                                exit;
                                                            }
                                                            if($domain->Type == "invalid") {
                                                                echo json_encode(["result" => "invalid", "msg" => __("domain status check invalid")]);
                                                                exit;
                                                            }
                                                            echo json_encode(["result" => "unknown", "msg" => __("domain status check unknown")]);
                                                            exit;
                                                        }
                                                        if(isset($_POST["action"]) && $_POST["action"] == "get_states") {
                                                            $countrycode = esc($_POST["countrycode"]);
                                                            if(isset($array_states[$countrycode])) {
                                                                $options = "<option value=\"\">" . __("make your choice") . "</option>";
                                                                foreach ($array_states[$countrycode] as $key => $value) {
                                                                    $options .= "<option value=\"" . $key . "\">" . $value . "</option>";
                                                                }
                                                                $return = ["type" => "select", "options" => $options];
                                                            } else {
                                                                $return = ["type" => "input"];
                                                            }
                                                            echo json_encode($return);
                                                            exit;
                                                        } elseif(isset($_POST["action"]) && $_POST["action"] == "debtor_update_mandateid") {
                                                            require_once "class/debtor.php";
                                                            $return = ["MandateID" => ""];
                                                            $debtor_id = esc($_POST["debtor"]);
                                                            $debtor = new debtor();
                                                            if(0 < $debtor_id) {
                                                                $debtor->Identifier = $debtor_id;
                                                                $debtor->show();
                                                                if($debtor->getDirectDebitMandate()) {
                                                                    echo json_encode($return);
                                                                    exit;
                                                                }
                                                            }
                                                            $debtor->DebtorCode = esc($_POST["debtorcode"]);
                                                            if($_POST["mandateid"]) {
                                                                if(substr($_POST["mandateid"], 0, strlen($_POST["olddebtorcode"])) == $_POST["olddebtorcode"]) {
                                                                    $return["MandateID"] = esc($_POST["debtorcode"]) . substr(esc($_POST["mandateid"]), strlen($_POST["olddebtorcode"]));
                                                                } else {
                                                                    $return["MandateID"] = esc($_POST["mandateid"]);
                                                                }
                                                            } else {
                                                                $return["MandateID"] = $debtor->getDirectDebitMandateID();
                                                            }
                                                            echo json_encode($return);
                                                        } else {
                                                            if(isset($_POST["action"]) && $_POST["action"] == "ticket_lock_information") {
                                                                require_once "class/ticket.php";
                                                                $ticket = new ticket();
                                                                $ticket->Identifier = esc($_POST["id"]);
                                                                $ticket->show();
                                                                if(0 < $ticket->LockEmployee && $ticket->LockEmployee != $account->Identifier) {
                                                                    $ticket->isTicketLocked();
                                                                    $message = sprintf(__("ticket is locked by employee x since time y"), $ticket->LockEmployeeName, substr($ticket->LockDate, 0, 10) == date("Y-m-d") ? rewrite_date_db2site($ticket->LockDate, "%H:%i") : rewrite_date_db2site($ticket->LockDate, DATE_FORMAT . " " . __("at") . " %H:%i")) . " <a id=\"ticket_unlock_link\" class=\"a1 c1 pointer\">" . __("ticket is locked, release now") . "</a>";
                                                                    echo json_encode(["locked" => true, "message" => $message]);
                                                                    exit;
                                                                }
                                                                $ticket->lockTicket();
                                                                $message = sprintf(__("ticket is locked for you since y"), rewrite_date_db2site($ticket->LockDate, "%H:%i:%s"));
                                                                $message .= "<a id=\"ticket_unlock_self\" class=\"a1 c1 pointer float_right\">" . __("ticket is locked for me, release now") . "</a>";
                                                                $message .= "<br /><br />";
                                                                echo json_encode(["locked" => false, "message" => $message]);
                                                                exit;
                                                            }
                                                            if(isset($_POST["action"]) && $_POST["action"] == "ticket_unlock") {
                                                                require_once "class/ticket.php";
                                                                $ticket = new ticket();
                                                                $ticket->Identifier = esc($_POST["id"]);
                                                                $ticket->show();
                                                                $ticket->unlockTicket();
                                                                echo json_encode(["locked" => false]);
                                                                exit;
                                                            }
                                                            if(isset($_POST["action"]) && $_POST["action"] == "create_dialog_change_whois") {
                                                                $selected_domains = $_POST["selected_domains"];
                                                                if(!empty($selected_domains)) {
                                                                    require_once "views/dialog.change.whois.php";
                                                                }
                                                                exit;
                                                            }
                                                            if(isset($_POST["action"]) && $_POST["action"] == "domain_sync") {
                                                                require_once "class/domain.php";
                                                                $domain = new domain();
                                                                $domain->Identifier = $_POST["id"];
                                                                $response = ["synced_text" => __("unknown")];
                                                                if($sync_domain = $domain->checkForSync()) {
                                                                    if($domain->syncDomainsByRegistrar($sync_domain, true)) {
                                                                        $response["synced_text"] = rewrite_date_db2site(date("Y-m-d")) . " " . __("at") . " " . rewrite_date_db2site(date("Y-m-d H:i"), "%H:%i");
                                                                    }
                                                                    if($domain->Success) {
                                                                        $response["success"] = $domain->Success;
                                                                    }
                                                                    if($domain->Error) {
                                                                        $response["error"] = $domain->Error[0];
                                                                    }
                                                                }
                                                                echo json_encode($response);
                                                                exit;
                                                            }
                                                            if(isset($_POST["action"]) && $_POST["action"] == "generate_twofactor_key") {
                                                                require_once "class/authentication.php";
                                                                $authentication = new Authentication_Model();
                                                                $response["auth_key"] = $authentication->setUser(esc($_SESSION["UserNamePro"]), esc($_POST["tokentype"]));
                                                                $response["qr_url"] = urlencode($authentication->createURL(esc($_SESSION["UserNamePro"])));
                                                                echo json_encode($response);
                                                                exit;
                                                            }
                                                            if(isset($_POST["action"]) && $_POST["action"] == "verify_auth_code") {
                                                                require_once "class/authentication.php";
                                                                $authentication = new Authentication_Model();
                                                                $verify_result = $authentication->authenticateUser(esc($_SESSION["UserNamePro"]), esc($_POST["authCode"]));
                                                                echo json_encode($verify_result);
                                                                exit;
                                                            }
                                                            if(isset($_POST["action"]) && $_POST["action"] == "batch_sync_domains") {
                                                                if(!isset($_SESSION["temp_batch_sync_domains"])) {
                                                                    $_SESSION["temp_batch_sync_domains"] = [];
                                                                    $_SESSION["temp_batch_sync_domains"]["skipped_domains"] = 0;
                                                                    $_SESSION["temp_batch_sync_domains"]["errors"] = [];
                                                                }
                                                                $remaining_domains = $_POST["selected_domains"];
                                                                require_once "class/domain.php";
                                                                $domain = new domain();
                                                                $domain->Identifier = $remaining_domains[key($remaining_domains)];
                                                                $domain->show();
                                                                $sync_domains = Database_Model::getInstance()->get("HostFact_Domains")->where("Registrar", $domain->Registrar)->where("id", ["IN" => $remaining_domains])->where("Status", ["IN" => ["4", "7"]])->asArray()->execute();
                                                                $remove_domains = Database_Model::getInstance()->get("HostFact_Domains", ["GROUP_CONCAT(`id`) AS ids"])->where("Registrar", $domain->Registrar)->where("id", ["IN" => $remaining_domains])->asArray()->execute();
                                                                $remove_domains = explode(",", $remove_domains[key($remove_domains)]["ids"]);
                                                                $_SESSION["temp_batch_sync_domains"]["skipped_domains"] = $_SESSION["temp_batch_sync_domains"]["skipped_domains"] + intval(count($remove_domains) - count($sync_domains));
                                                                $remaining_domains = array_diff($remaining_domains, $remove_domains);
                                                                $response = ["remaining_domains" => $remaining_domains];
                                                                if(empty($sync_domains)) {
                                                                    echo json_encode($response);
                                                                    exit;
                                                                }
                                                                $domain->syncDomainsByRegistrar($sync_domains);
                                                                if(!empty($domain->Error)) {
                                                                    $_SESSION["temp_batch_sync_domains"]["errors"] = array_merge($_SESSION["temp_batch_sync_domains"]["errors"], $domain->Error);
                                                                }
                                                                echo json_encode($response);
                                                                exit;
                                                            }
                                                            if(isset($_POST["action"]) && $_POST["action"] == "get_interaction") {
                                                                require_once "class/interaction.php";
                                                                $interaction = new interaction();
                                                                $interaction->Identifier = intval($_POST["id"]);
                                                                $interaction->show();
                                                                $date = $interaction->Date;
                                                                $interaction->Date = $date ? rewrite_date_db2site($date) : rewrite_date_db2site(date("Y-m-d"));
                                                                $interaction->Time = $date ? date("H:i", strtotime($date)) : date("H:i");
                                                                echo json_encode($interaction);
                                                                exit;
                                                            }
                                                            if(isset($_POST["action"]) && $_POST["action"] == "save_interaction") {
                                                                if(!U_DEBTOR_EDIT) {
                                                                    echo json_encode(["result" => false, "errors" => [__("invalid user rights to perform action")]]);
                                                                    exit;
                                                                }
                                                                if(isset($_POST["form_fields"])) {
                                                                    parse_str(esc($_POST["form_fields"]), $fields);
                                                                }
                                                                require_once "class/interaction.php";
                                                                $interaction = new interaction();
                                                                if(isset($fields["InteractionID"]) && $fields["InteractionID"] && 0 < $fields["InteractionID"]) {
                                                                    $interaction->Identifier = intval(esc($fields["InteractionID"]));
                                                                }
                                                                $interaction->Author = esc($fields["Author"]);
                                                                $interaction->Category = esc($fields["Category"]);
                                                                $interaction->Date = rewrite_date_site2db(esc($fields["Date"]) . " " . esc($fields["Time"]), DATE_FORMAT . " %H:%i");
                                                                $interaction->Debtor = esc($fields["Debtor"]);
                                                                $interaction->Message = esc($fields["Comment"]);
                                                                $interaction->Type = esc($fields["Type"]);
                                                                if(isset($interaction->Identifier) && 0 < $interaction->Identifier) {
                                                                    $save_result = $interaction->edit();
                                                                } else {
                                                                    $save_result = $interaction->add();
                                                                }
                                                                if($save_result) {
                                                                    echo json_encode(["result" => true]);
                                                                } else {
                                                                    echo json_encode(["result" => false, "errors" => $interaction->Error]);
                                                                }
                                                                exit;
                                                            }
                                                            if(isset($_POST["action"]) && $_POST["action"] == "delete_interaction") {
                                                                if(!U_DEBTOR_DELETE) {
                                                                    exit;
                                                                }
                                                                require_once "class/interaction.php";
                                                                $interaction = new interaction();
                                                                $interaction->Identifier = intval(esc($_POST["interaction_id"]));
                                                                if($interaction->delete()) {
                                                                    echo json_encode(["result" => true]);
                                                                } else {
                                                                    echo json_encode(["result" => false]);
                                                                }
                                                                exit;
                                                            }
                                                            if(isset($_POST["action"]) && $_POST["action"] == "hosting_singlesignon" && isset($_POST["hosting_id"]) && $_POST["hosting_id"]) {
                                                                require_once "class/hosting.php";
                                                                $hosting_id = intval($_POST["hosting_id"]);
                                                                $hosting = new hosting();
                                                                $hosting->show($hosting_id, false);
                                                                $allowed_ips = [$_SERVER["REMOTE_ADDR"]];
                                                                if(U_HOSTING_EDIT && ($result = $hosting->singleSignOn($allowed_ips))) {
                                                                    if(isset($result["form_action"])) {
                                                                        echo json_encode(["data" => $result["data"], "form_action" => $result["form_action"]]);
                                                                        exit;
                                                                    }
                                                                    if(isset($result["url"])) {
                                                                        echo json_encode(["url" => $result["url"]]);
                                                                        exit;
                                                                    }
                                                                }
                                                                flashMessage($hosting);
                                                                exit;
                                                            }
                                                            if(isset($_POST["action"]) && $_POST["action"] == "get_oauth2_ms_device_code") {
                                                                require_once "config.php";
                                                                $ch = curl_init();
                                                                curl_setopt($ch, CURLOPT_URL, ticket::$OAUTH2_MS_DEVICECODE_URL);
                                                                settings::disableSSLVerificationIfNeeded($ch);
                                                                curl_setopt($ch, CURLOPT_TIMEOUT, "10");
                                                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                                                curl_setopt($ch, CURLOPT_POSTFIELDS, ["client_id" => ticket::$OAUTH2_MS_CLIENT_ID, "scope" => "offline_access https://outlook.office.com/POP.AccessAsUser.All"]);
                                                                $result = curl_exec($ch);
                                                                curl_close($ch);
                                                                $response = json_decode($result);
                                                                if($response && !isset($response->error) && isset($response->device_code) && isset($response->user_code)) {
                                                                    $_SESSION["oauth2_device_code"] = $response->device_code;
                                                                    echo json_encode(["result" => true, "code" => $response->user_code]);
                                                                } else {
                                                                    echo json_encode(["result" => false]);
                                                                }
                                                                exit;
                                                            }
                                                            echo "{ \"errorSet\": [";
                                                            echo json_encode(["Message" => "<i>" . __("invalid action") . "</i>"]);
                                                            echo "] }";
                                                            exit;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

?>