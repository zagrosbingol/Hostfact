<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "config.php";
checkRight(U_STATISTICS_SHOW);
$page = isset($_GET["page"]) ? $_GET["page"] : "overview";
require_once "class/statistic.php";
$stats = isset($stats) ? $stats : new statistic();
if($page === "preinvoiced") {
    if(isset($_POST["report"])) {
        $date = date("Y-m-d", strtotime(rewrite_date_site2db($_POST["date"])));
        $showSpecification = $_POST["report"] == "specification" ? true : false;
        if($showSpecification === true) {
            $filename = $stats->getPreInvoicedSpecification($date);
            $_SESSION["force_download"] = $filename;
            $stats->Success[] = __("pre-invoiced file being downloaded");
        } else {
            $turnoverPerProduct = $stats->getPreInvoicedTurnoverPerProduct($date);
            $product = new product();
            $allProducts = $product->all(["ProductCode", "ProductName"], "ProductCode", "ASC", -1);
            $allProductsByProductCode = [];
            foreach ($allProducts as $key => $productInfo) {
                if(is_numeric($key)) {
                    $allProductsByProductCode[trim($productInfo["ProductCode"])] = $productInfo;
                }
            }
            unset($allProducts);
        }
    } elseif(9 <= date("n")) {
        $date = new DateTime("1st January Next Year");
        $date = $date->format("Y-m-d");
    } else {
        $date = new DateTime("1st January This Year");
        $date = $date->format("Y-m-d");
    }
    $message = parse_message($stats);
    $wfh_page_title = __("pre-invoiced turnover");
    $current_page_url = "statistics.php";
    $sidebar_template = "statistics.sidebar.php";
    require_once "views/statistics.overview.preinvoiced.php";
    exit;
}
$stats->SelectedPeriod = isset($_SESSION["statistics.overview"]["period"]) ? esc($_SESSION["statistics.overview"]["period"]) : ($page == "btw" ? "q" : "y");
$stats->Show = isset($_SESSION["statistics.overview"]["status"]) ? esc($_SESSION["statistics.overview"]["status"]) : "all";
$stats->AutoRenew = isset($_SESSION["statistics.overview"]["autorenew"]) ? esc($_SESSION["statistics.overview"]["autorenew"]) : "";
$stats->DebtorGroupFilter = isset($_SESSION["statistics.overview"]["debtorgroup"]) ? esc($_SESSION["statistics.overview"]["debtorgroup"]) : "";
if(isset($_SESSION["statistics.overview"]["move"]) && isset($_SESSION["statistics.overview"]["StartDate"])) {
    $interval = $_SESSION["statistics.overview"]["move"] == "-1" ? -1 : 1;
    substr($stats->SelectedPeriod, 0, 1);
    switch (substr($stats->SelectedPeriod, 0, 1)) {
        case "y":
            $start_date = date("Y-m-d", strtotime($interval . " year", strtotime($_SESSION["statistics.overview"]["StartDate"])));
            $end_date = date("Y-m-d", strtotime($interval . " year", strtotime($_SESSION["statistics.overview"]["EndDate"])));
            break;
        case "q":
            $start_date = date("Y-m-d", strtotime($interval * 3 . " month", strtotime($_SESSION["statistics.overview"]["StartDate"])));
            $end_date = date("Y-m-d", strtotime($interval * 3 . " month", strtotime(substr($_SESSION["statistics.overview"]["EndDate"], 0, 8) . "01")));
            $end_date = substr($end_date, 0, 8) . date("t", strtotime($end_date));
            break;
        case "m":
            $start_date = date("Y-m-d", strtotime($interval . " month", strtotime($_SESSION["statistics.overview"]["StartDate"])));
            $end_date = date("Y-m-d", strtotime($interval . " month", strtotime(substr($_SESSION["statistics.overview"]["EndDate"], 0, 8) . "01")));
            $end_date = substr($end_date, 0, 8) . date("t", strtotime($end_date));
            break;
        default:
            $var_statstab_selected = isset($_SESSION["statstab_selected"]) && $_SESSION["statstab_selected"] != "" ? $_SESSION["statstab_selected"] : "";
            $_SESSION["statistics.overview"]["period"] = substr($stats->SelectedPeriod, 0, 1);
            unset($_SESSION["statistics.overview"]["move"]);
    }
} else {
    substr($stats->SelectedPeriod, 0, 1);
    switch (substr($stats->SelectedPeriod, 0, 1)) {
        case "y":
            if(1 < strlen($stats->SelectedPeriod)) {
                $start_date = date(substr($stats->SelectedPeriod, 2, 4) . "-01-01");
                $end_date = date(substr($stats->SelectedPeriod, 2, 4) . "-12-31");
            } else {
                $start_date = date("Y-01-01");
                $end_date = date("Y-12-31");
            }
            break;
        case "q":
            if(1 < strlen($stats->SelectedPeriod)) {
                $quarter = substr($stats->SelectedPeriod, 2, 1);
                $start_date = date(substr($stats->SelectedPeriod, 4, 4) . "-" . str_pad($quarter * 3 - 2, 2, "0", STR_PAD_LEFT) . "-01");
                $end_date = date(substr($stats->SelectedPeriod, 4, 4) . "-" . str_pad($quarter * 3, 2, "0", STR_PAD_LEFT) . "-" . date("t", strtotime(date("Y-" . $quarter * 3 . "-28"))));
            } else {
                $quarter = ceil(date("n") / 3);
                $start_date = date("Y-" . str_pad($quarter * 3 - 2, 2, "0", STR_PAD_LEFT) . "-01");
                $end_date = date("Y-" . str_pad($quarter * 3, 2, "0", STR_PAD_LEFT) . "-" . date("t", strtotime(date("Y-" . $quarter * 3 . "-28"))));
            }
            break;
        case "m":
            if(substr($stats->SelectedPeriod, 2, 4) == "prev") {
                if(date("n") == 1) {
                    $start_date = date(date("Y") - 1 . "-12-01");
                    $end_date = date(date("Y") - 1 . "-12-" . date("t", strtotime(date(date("Y") - 1 . "-12-28"))));
                } else {
                    $start_date = date("Y-" . str_pad(date("n") - 1, 2, "0", STR_PAD_LEFT) . "-01");
                    $end_date = date("Y-" . str_pad(date("n") - 1, 2, "0", STR_PAD_LEFT) . "-" . date("t", strtotime(date("Y-" . (date("n") - 1) . "-28"))));
                }
            } elseif(substr($stats->SelectedPeriod, 2, 4) == "next") {
                if(date("n") == 12) {
                    $start_date = date(date("Y") + 1 . "-01-01");
                    $end_date = date(date("Y") + 1 . "-01-" . date("t", strtotime(date(date("Y") + 1 . "-01-28"))));
                } else {
                    $start_date = date("Y-" . str_pad(date("n") + 1, 2, "0", STR_PAD_LEFT) . "-01");
                    $end_date = date("Y-" . str_pad(date("n") + 1, 2, "0", STR_PAD_LEFT) . "-" . date("t", strtotime(date("Y-" . (date("n") + 1) . "-28"))));
                }
            } else {
                $start_date = date("Y-m-01");
                $end_date = date("Y-m-" . date("t", strtotime(date("Y-m-28"))));
            }
            break;
        case "d":
            $tmp_period = explode("_", $stats->SelectedPeriod);
            $start_date = date("Y-m-d", strtotime(rewrite_date_site2db($tmp_period[1])));
            $end_date = date("Y-m-d", strtotime(rewrite_date_site2db($tmp_period[2])));
            $stats->CustomDate = true;
            break;
        default:
            unset($_SESSION["statstab_selected"]);
    }
}
$_SESSION["statistics.overview"]["StartDate"] = $start_date;
$_SESSION["statistics.overview"]["EndDate"] = $end_date;
$stats->setStartDate($start_date);
$stats->setEndDate($end_date);
if($end_date < $start_date) {
    $stats->Error[] = __("invalid period selected, back to default period");
    flashMessage($stats);
    unset($_SESSION["statistics.overview"]);
    header("Location: statistics.php?page=" . $page);
    exit;
}
switch ($page) {
    case "btw":
        if($company->Country == "NL") {
            $stats->Warning[] = "Dit BTW-overzicht is slechts ter indicatie, speciale regelingen zijn nog niet in dit overzicht meegenomen. Controleer zelf uw aangifte.";
        }
        $unit = substr($stats->SelectedPeriod, 0, 1);
        switch ($unit) {
            case "d":
                $period_label = date("j", strtotime($start_date)) . " " . __("month_" . date("n", strtotime($start_date))) . " " . date("Y", strtotime($start_date)) . " - " . date("j", strtotime($end_date)) . " " . __("month_" . date("n", strtotime($end_date))) . " " . date("Y", strtotime($end_date));
                break;
            case "y":
            case "q":
                $period_label = __("quarter") . " " . date("m", strtotime($end_date)) / 3 . " - " . date("Y", strtotime($start_date));
                break;
            case "m":
                $period_label = $array_months[date("m", strtotime($end_date))] . " " . date("Y", strtotime($start_date));
                break;
            default:
                $period_label = date("Y", strtotime($start_date));
                $tax_reporting_container["sales"] = $stats->getVATReporting($start_date, $end_date, "sales");
                $tax_reporting_container["purchases"] = $stats->getVATReporting($start_date, $end_date, "purchases");
                $message = parse_message($stats);
                $wfh_page_title = __("vat overview") . " " . ($stats->Show != "all" ? " - " . __("statistic " . $stats->Show) : "");
                $current_page_url = "statistics.php?page=btw";
                $sidebar_template = "statistics.sidebar.php";
                require_once "views/statistics.overview.vat.php";
        }
        break;
    case "period":
        $sales = $stats->showPeriodic();
        require_once "class/group.php";
        $group = new group();
        $group->Type = "debtor";
        $fields = ["GroupName", "Debtors"];
        $debtor_groups = $group->all($fields);
        $message = parse_message($stats);
        $wfh_page_title = __("periodic sales");
        $current_page_url = "statistics.php?page=period";
        $sidebar_template = "statistics.sidebar.php";
        require_once "views/statistics.overview.period.php";
        break;
    default:
        if(isset($stats->CustomDate) && $stats->CustomDate) {
            $dayStart = substr($start_date, 8, 2);
            $stats->setStartDate(date("Y-m-" . $dayStart, strtotime("-1 year", strtotime(substr($start_date, 0, 7) . "-01"))));
            $dayEnd = substr($end_date, 8, 2);
            $stats->setEndDate(date("Y-m-" . $dayEnd, strtotime("-1 year", strtotime(substr($end_date, 0, 7) . "-01"))));
        } else {
            $stats->setStartDate(date("Y-m-d", strtotime("-1 year", strtotime(substr($start_date, 0, 7) . "-01"))));
            $stats->setEndDate(date("Y-m-t", strtotime("-1 year", strtotime(substr($end_date, 0, 7) . "-01"))));
        }
        $sales_last = $stats->show();
        $purchases_last = $stats->showCredit();
        $sales_last_productgroup = $stats->showProductGroup();
        $sales_last_product = $stats->showProduct();
        $sales_last_debtorgroup = $stats->showDebtorGroup();
        $purchases_last_creditorgroup = $stats->showCreditorGroup();
        $stats->SalesExcl_last = $stats->SalesExcl;
        $stats->SalesBTW_last = $stats->SalesBTW;
        $stats->SalesIncl_last = $stats->SalesIncl;
        $stats->PurchasesExcl_last = $stats->PurchasesExcl;
        $stats->PurchasesBTW_last = $stats->PurchasesBTW;
        $stats->PurchasesIncl_last = $stats->PurchasesIncl;
        $stats->setStartDate($start_date);
        $stats->setEndDate($end_date);
        $statstab_selected = isset($var_statstab_selected) ? $var_statstab_selected : "";
        $sales = $stats->show();
        $purchases = $stats->showCredit();
        $sales_current_per_unit = $stats->showSalesPerUnit(true);
        $purchases_current_per_unit = $stats->showCreditPerUnit();
        $salesProductGroup = $stats->showProductGroup();
        $salesProduct = $stats->showProduct();
        $salesDebtorGroup = $stats->showDebtorGroup();
        $salesCreditorGroup = $stats->showCreditorGroup();
        $top_debtors = $stats->showTopDebtorsUnit();
        $message = parse_message($stats);
        $wfh_page_title = __("revenues and expenses") . " " . ($stats->Show != "all" ? " - " . __("statistic " . $stats->Show) : "");
        $current_page_url = "statistics.php";
        $sidebar_template = "statistics.sidebar.php";
        require_once "views/statistics.overview.php";
}

?>