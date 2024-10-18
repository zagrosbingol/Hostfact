<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class statistic
{
    public $Show;
    public $s_day;
    public $s_month;
    public $s_year;
    public $e_day;
    public $e_month;
    public $e_year;
    public $SalesExcl;
    public $SalesBTW;
    public $SalesIncl;
    public $PurchasesExcl;
    public $PurchasesBTW;
    public $PurchasesIncl;
    public $TotalBTW;
    public $PaidBTW;
    public $Error;
    public $Warning;
    public $Success;
    public $DebtorGroupFilter = "";
    public function __construct()
    {
        $this->Show = "all";
        $this->AutoRenew = "";
        $this->s_day = date("d");
        $this->s_month = date("m") == "01" ? "12" : str_repeat("0", 2 - strlen(date("n") - 1)) . "" . (date("n") - 1);
        $this->s_year = date("m") == "01" ? date("Y") - 1 : date("Y");
        $this->e_day = date("d");
        $this->e_month = date("m");
        $this->e_year = date("Y");
        $this->NumberOfUnits = 12;
        $this->SelectedPeriod = "y";
        $this->SalesExcl = 0;
        $this->SalesBTW = 0;
        $this->SalesIncl = 0;
        $this->TotalAmountExcl = 0;
        $this->TotalAmountIncl = 0;
        $this->TotalBTW = 0;
        $this->PaidBTW = 0;
        $this->Error = [];
        $this->Warning = [];
        $this->Success = [];
        $this->real_SalesExcl = 0;
        $this->real_SalesBTW = 0;
        $this->real_SalesIncl = 0;
        $this->isWidget = false;
    }
    public function show()
    {
        global $array_months;
        $sales = [];
        $unit = substr($this->SelectedPeriod, 0, 1);
        switch ($unit) {
            case "d":
                $start_date = $this->s_year . "-" . $this->s_month . "-" . $this->s_day;
                $end_date = $this->e_year . "-" . $this->e_month . "-" . $this->e_day;
                $sales["Label"] = date("j", strtotime($start_date)) . " " . __("month_" . date("n", strtotime($start_date))) . " " . date("Y", strtotime($start_date)) . " - " . date("j", strtotime($end_date)) . " " . __("month_" . date("n", strtotime($end_date))) . " " . date("Y", strtotime($end_date));
                break;
            case "y":
            case "q":
                $sales["Label"] = __("quarter") . " " . $this->e_month / 3 . " - " . $this->s_year;
                $this->NumberOfUnits = 3;
                break;
            case "m":
                $timestamp = mktime(0, 0, 0, $this->s_month, 1, $this->s_year);
                $sales["Label"] = $array_months[date("m", $timestamp)] . " " . $this->s_year;
                $this->NumberOfUnits = date("t", strtotime($this->s_year . "-" . (int) $this->s_month . "-01"));
                break;
            default:
                $sales["Label"] = $this->s_year;
                $this->NumberOfUnits = 12;
                Database_Model::getInstance()->get(["HostFact_Invoice"], ["SUM(`AmountExcl`) as `SalesExcl`", "SUM(`AmountIncl`) as `SalesIncl`"]);
                switch ($this->Show) {
                    case "all":
                        Database_Model::getInstance()->where("HostFact_Invoice.Status", ["IN" => [2, 3, 4, 8, 9]]);
                        break;
                    case "paid":
                        Database_Model::getInstance()->where("HostFact_Invoice.Status", ["IN" => [4, 8, 9]]);
                        break;
                    case "open":
                        Database_Model::getInstance()->where("HostFact_Invoice.Status", ["IN" => [2, 3]]);
                        break;
                    default:
                        $result = Database_Model::getInstance()->where("HostFact_Invoice.Date", [">=" => $this->s_year . "-" . $this->s_month . "-" . $this->s_day])->where("HostFact_Invoice.Date", ["<=" => $this->e_year . "-" . $this->e_month . "-" . $this->e_day])->execute();
                        foreach ($result as $var) {
                            $this->SalesExcl += round($var->SalesExcl, 2);
                            $this->SalesIncl += round($var->SalesIncl, 2);
                        }
                        $this->SalesExcl = $this->SalesExcl;
                        $this->SalesIncl = $this->SalesIncl;
                        return $sales;
                }
        }
    }
    public function showCredit()
    {
        $sales = [];
        $this->PurchasesExcl = $this->PurchasesBTW = $this->PurchasesIncl = 0;
        Database_Model::getInstance()->get(["HostFact_Creditors", "HostFact_CreditInvoice", "HostFact_CreditInvoiceElements"], ["HostFact_Creditors.CreditorCode", "HostFact_Creditors.CompanyName", "HostFact_Creditors.SurName", "HostFact_Creditors.Initials", "HostFact_Creditors.id", "SUM(HostFact_CreditInvoiceElements.`Number`*HostFact_CreditInvoiceElements.`PriceExcl`*(1-HostFact_CreditInvoice.`PrivatePercentage`)*(1-(ROUND(HostFact_CreditInvoice.`Private`/HostFact_CreditInvoice.`AmountExcl`,2)))) as `SalesExcl`", "SUM(HostFact_CreditInvoiceElements.`Number`*HostFact_CreditInvoiceElements.`PriceExcl`*(1-HostFact_CreditInvoice.`PrivatePercentage`)*(1-(ROUND(HostFact_CreditInvoice.`Private`/HostFact_CreditInvoice.`AmountExcl`,2)))*ROUND((1+HostFact_CreditInvoiceElements.`TaxPercentage`),2)) as `SalesIncl`"])->where("HostFact_CreditInvoiceElements.`CreditInvoiceCode` = HostFact_CreditInvoice.`CreditInvoiceCode`")->where("HostFact_Creditors.`id` = HostFact_CreditInvoice.`Creditor`");
        switch ($this->Show) {
            case "all":
                Database_Model::getInstance()->where("HostFact_CreditInvoice.Status", ["IN" => [1, 2, 3, 8]]);
                break;
            case "paid":
                Database_Model::getInstance()->where("HostFact_CreditInvoice.Status", ["IN" => [3, 8]]);
                break;
            case "open":
                Database_Model::getInstance()->where("HostFact_CreditInvoice.Status", ["IN" => [1, 2]]);
                break;
            default:
                $result = Database_Model::getInstance()->where("HostFact_CreditInvoice.Date", [">=" => $this->s_year . "-" . $this->s_month . "-" . $this->s_day])->where("HostFact_CreditInvoice.Date", ["<=" => $this->e_year . "-" . $this->e_month . "-" . $this->e_day])->groupBy("HostFact_CreditInvoice.Creditor")->orderBy("HostFact_Creditors.CreditorCode", "ASC")->execute();
                foreach ($result as $var) {
                    $sales[$var->CreditorCode] = ["CreditorCode" => htmlspecialchars($var->CreditorCode), "CreditorID" => $var->id, "CompanyName" => htmlspecialchars($var->CompanyName), "SurName" => htmlspecialchars($var->SurName), "Initials" => htmlspecialchars($var->Initials), "SalesExcl" => money($var->SalesExcl, false), "SalesIncl" => money($var->SalesIncl, false)];
                    $this->PurchasesExcl += round($var->SalesExcl, 2);
                    $this->PurchasesIncl += round($var->SalesIncl, 2);
                }
                $this->PurchasesExcl = $this->PurchasesExcl;
                $this->PurchasesIncl = $this->PurchasesIncl;
                return $sales;
        }
    }
    public function showTopDebtorsUnit()
    {
        global $array_months;
        $debtorsales = [];
        $unit = substr($this->SelectedPeriod, 0, 1);
        switch ($unit) {
            case "m":
                $timestamp = mktime(0, 0, 0, $this->s_month, 1, $this->s_year);
                $debtorsales["Label"] = $array_months[date("m", $timestamp)] . " " . $this->s_year;
                $debtorsales["Label2"] = $array_months[date("m", $timestamp)] . " " . ($this->s_year - 1);
                break;
            case "q":
                $debtorsales["Label"] = __("quarter") . " " . $this->e_month / 3 . " - " . $this->s_year;
                $debtorsales["Label2"] = __("quarter") . " " . $this->e_month / 3 . " - " . ($this->s_year - 1);
                break;
            default:
                $debtorsales["Label"] = $this->s_year;
                $debtorsales["Label2"] = $this->s_year - 1;
                $prev_interval = "YEAR";
                $unit = $this->SelectedPeriod;
                $result = Database_Model::getInstance()->get("HostFact_Invoice", ["HostFact_Invoice.Debtor", "SUM(HostFact_Invoice.`AmountExcl`) as `SalesExcl`", "HostFact_Debtors.CompanyName", "HostFact_Debtors.Initials", "HostFact_Debtors.SurName"])->join("HostFact_Debtors", "HostFact_Invoice.`Debtor` = HostFact_Debtors.`id`")->where("HostFact_Invoice.Status", ["IN" => [2, 3, 4, 8, 9]])->where("HostFact_Invoice.Date", [">=" => $this->s_year . "-" . $this->s_month . "-" . $this->s_day])->where("HostFact_Invoice.Date", ["<=" => $this->e_year . "-" . $this->e_month . "-" . $this->e_day])->groupBy("HostFact_Invoice.Debtor")->orderBy("SalesExcl", "DESC")->limit(0, 25)->execute();
                $debtors = [];
                foreach ($result as $var) {
                    $debtorsales[] = ["DebtorId" => $var->Debtor, "Debtor" => $var->CompanyName != "" ? $var->CompanyName : $var->Initials . " " . $var->SurName, "SalesExcl" => round($var->SalesExcl, 2)];
                    $debtors[] = $var->Debtor;
                }
                if(!empty($debtors)) {
                    $result = Database_Model::getInstance()->get("HostFact_Invoice", ["HostFact_Invoice.Debtor", "SUM(HostFact_Invoice.`AmountExcl`) as `LastSalesExcl`"])->join("HostFact_Debtors", "HostFact_Invoice.`Debtor` = HostFact_Debtors.`id`")->where("HostFact_Invoice.Debtor", ["IN" => $debtors])->where("HostFact_Invoice.Status", ["IN" => [2, 3, 4, 8, 9]])->where("HostFact_Invoice.Date", [">=" => date("Y-m-d", strtotime("-1 " . $prev_interval, strtotime($this->s_year . "-" . $this->s_month . "-" . $this->s_day)))])->where("HostFact_Invoice.Date", ["<=" => date("Y-m-d", strtotime("-1 " . $prev_interval, strtotime($this->e_year . "-" . $this->e_month . "-" . $this->e_day)))])->groupBy("HostFact_Invoice.Debtor")->execute();
                    foreach ($result as $var) {
                        $debtorsales["LastSalesExcl"][$var->Debtor] = round($var->LastSalesExcl, 2);
                    }
                }
                return $debtorsales;
        }
    }
    public function showSalesPerUnit($get_previous_period = false)
    {
        global $array_months;
        $sales = [];
        $unit = substr($this->SelectedPeriod, 0, 1);
        $select_unit = "MONTH(`Date`) as Unit";
        $group_by = "MONTH(`Date`)";
        $order_by = "MONTH(`Date`)";
        $this->NumberOfUnits = 12;
        $prev_interval = "YEAR";
        if($this->isWidget) {
            $start_date = $this->s_year . "-" . $this->s_month . "-" . $this->s_day;
            $end_date = $this->e_year . "-" . $this->e_month . "-" . $this->e_day;
        } else {
            $start_date = $this->e_year . "-01-01";
            $end_date = $this->e_year . "-12-31";
        }
        Database_Model::getInstance()->get("HostFact_Invoice", [$select_unit, "SUM(`AmountExcl`) as `SalesExcl`", "SUM(`AmountIncl`) as `SalesIncl`"]);
        switch ($this->Show) {
            case "all":
                Database_Model::getInstance()->where("Status", ["IN" => [2, 3, 4, 8, 9]]);
                break;
            case "paid":
                Database_Model::getInstance()->where("Status", ["IN" => [4, 8, 9]]);
                break;
            case "open":
                Database_Model::getInstance()->where("Status", ["IN" => [2, 3]]);
                break;
            default:
                $result = Database_Model::getInstance()->where("HostFact_Invoice.Date", [">=" => $start_date])->where("HostFact_Invoice.Date", ["<=" => $end_date])->groupBy(["RAW" => $group_by])->orderBy($order_by)->execute();
                $i = 1;
                foreach ($result as $var) {
                    $sales[$var->Unit] = ["Unit" => $var->Unit, "SalesExcl" => round($var->SalesExcl, 2), "SalesIncl" => round($var->SalesIncl, 2)];
                }
                if($get_previous_period === true) {
                    Database_Model::getInstance()->get("HostFact_Invoice", [$select_unit, "SUM(`AmountExcl`) as `SalesExcl`", "SUM(`AmountIncl`) as `SalesIncl`"]);
                    switch ($this->Show) {
                        case "all":
                            Database_Model::getInstance()->where("Status", ["IN" => [2, 3, 4, 8, 9]]);
                            break;
                        case "paid":
                            Database_Model::getInstance()->where("Status", ["IN" => [4, 8, 9]]);
                            break;
                        case "open":
                            Database_Model::getInstance()->where("Status", ["IN" => [2, 3]]);
                            break;
                        default:
                            $result = Database_Model::getInstance()->where("HostFact_Invoice.Date", [">=" => date("Y-m-d", strtotime("-1 " . $prev_interval, strtotime($start_date)))])->where("HostFact_Invoice.Date", ["<=" => date("Y-m-d", strtotime("-1 " . $prev_interval, strtotime($end_date)))])->groupBy(["RAW" => $group_by])->orderBy($order_by)->execute();
                            $i = 1;
                            foreach ($result as $var) {
                                $sales["prev" . $var->Unit] = ["Unit" => $var->Unit, "SalesExcl" => round($var->SalesExcl, 2), "SalesIncl" => round($var->SalesIncl, 2)];
                            }
                    }
                }
                $this->SalesUnitLabels = ["current" => date("Y", strtotime($start_date)), "previous" => date("Y", strtotime("-1 " . $prev_interval, strtotime($start_date)))];
                return $sales;
        }
    }
    public function showCreditPerUnit()
    {
        global $array_months;
        $sales = [];
        $unit = substr($this->SelectedPeriod, 0, 1);
        $select_unit = "MONTH(HostFact_CreditInvoice.`Date`) as Unit";
        $group_by = "MONTH(HostFact_CreditInvoice.`Date`)";
        $order_by = "MONTH(HostFact_CreditInvoice.`Date`)";
        $this->NumberOfUnits = 12;
        $prev_interval = "YEAR";
        $start_date = $this->e_year . "-01-01";
        $end_date = $this->e_year . "-12-31";
        Database_Model::getInstance()->get(["HostFact_CreditInvoice", "HostFact_CreditInvoiceElements"], [$select_unit, "SUM(HostFact_CreditInvoiceElements.`Number`*HostFact_CreditInvoiceElements.`PriceExcl`*(1-HostFact_CreditInvoice.`PrivatePercentage`)*(1-(ROUND(HostFact_CreditInvoice.`Private`/HostFact_CreditInvoice.`AmountExcl`,2)))) as `SalesExcl`", "SUM(HostFact_CreditInvoiceElements.`Number`*HostFact_CreditInvoiceElements.`PriceExcl`*(1-HostFact_CreditInvoice.`PrivatePercentage`)*(1-(ROUND(HostFact_CreditInvoice.`Private`/HostFact_CreditInvoice.`AmountExcl`,2)))*ROUND((1+HostFact_CreditInvoiceElements.`TaxPercentage`),2)) as `SalesIncl`"])->where("HostFact_CreditInvoiceElements.`CreditInvoiceCode` = HostFact_CreditInvoice.`CreditInvoiceCode`");
        switch ($this->Show) {
            case "all":
                Database_Model::getInstance()->where("HostFact_CreditInvoice.Status", ["IN" => [1, 2, 3, 8]]);
                break;
            case "paid":
                Database_Model::getInstance()->where("HostFact_CreditInvoice.Status", ["IN" => [3, 8]]);
                break;
            case "open":
                Database_Model::getInstance()->where("HostFact_CreditInvoice.Status", ["IN" => [1, 2]]);
                break;
            default:
                $result = Database_Model::getInstance()->where("HostFact_CreditInvoice.Date", [">=" => $start_date])->where("HostFact_CreditInvoice.Date", ["<=" => $end_date])->groupBy(["RAW" => $group_by])->orderBy($order_by)->execute();
                $i = 1;
                foreach ($result as $var) {
                    $sales[$var->Unit] = ["Unit" => $var->Unit, "SalesExcl" => round($var->SalesExcl, 2), "SalesIncl" => round($var->SalesIncl, 2)];
                }
                Database_Model::getInstance()->get(["HostFact_CreditInvoice", "HostFact_CreditInvoiceElements"], [$select_unit, "SUM(HostFact_CreditInvoiceElements.`Number`*HostFact_CreditInvoiceElements.`PriceExcl`*(1-HostFact_CreditInvoice.`PrivatePercentage`)*(1-(ROUND(HostFact_CreditInvoice.`Private`/HostFact_CreditInvoice.`AmountExcl`,2)))) as `SalesExcl`", "SUM(HostFact_CreditInvoiceElements.`Number`*HostFact_CreditInvoiceElements.`PriceExcl`*(1-HostFact_CreditInvoice.`PrivatePercentage`)*(1-(ROUND(HostFact_CreditInvoice.`Private`/HostFact_CreditInvoice.`AmountExcl`,2)))*ROUND((1+HostFact_CreditInvoiceElements.`TaxPercentage`),2)) as `SalesIncl`"])->where("HostFact_CreditInvoiceElements.`CreditInvoiceCode` = HostFact_CreditInvoice.`CreditInvoiceCode`");
                switch ($this->Show) {
                    case "all":
                        Database_Model::getInstance()->where("HostFact_CreditInvoice.Status", ["IN" => [1, 2, 3, 8]]);
                        break;
                    case "paid":
                        Database_Model::getInstance()->where("HostFact_CreditInvoice.Status", ["IN" => [3, 8]]);
                        break;
                    case "open":
                        Database_Model::getInstance()->where("HostFact_CreditInvoice.Status", ["IN" => [1, 2]]);
                        break;
                    default:
                        $result = Database_Model::getInstance()->where("HostFact_CreditInvoice.Date", [">=" => date("Y-m-d", strtotime("-1 " . $prev_interval, strtotime($start_date)))])->where("HostFact_CreditInvoice.Date", ["<=" => date("Y-m-d", strtotime("-1 " . $prev_interval, strtotime($end_date)))])->groupBy(["RAW" => $group_by])->orderBy($order_by)->execute();
                        $i = 1;
                        foreach ($result as $var) {
                            $sales["prev" . $var->Unit] = ["Unit" => $var->Unit, "SalesExcl" => round($var->SalesExcl, 2), "SalesIncl" => round($var->SalesIncl, 2)];
                        }
                        return $sales;
                }
        }
    }
    public function showProduct()
    {
        $sales = [];
        Database_Model::getInstance()->get(["HostFact_Invoice", "HostFact_InvoiceElements"], ["HostFact_Products.id", "HostFact_Products.ProductName", "HostFact_Products.ProductCode", "SUM(HostFact_InvoiceElements.`Number`*HostFact_InvoiceElements.`Periods`*HostFact_InvoiceElements.`PriceExcl`*ROUND((1-HostFact_InvoiceElements.`DiscountPercentage`),4)*ROUND((1-HostFact_Invoice.`Discount`),4)) as `SalesExcl`", "SUM(HostFact_InvoiceElements.`Number`*HostFact_InvoiceElements.`Periods`*HostFact_InvoiceElements.`PriceExcl`*ROUND((1-HostFact_InvoiceElements.`DiscountPercentage`),4)*ROUND((1-HostFact_Invoice.`Discount`),4)*ROUND((1+HostFact_InvoiceElements.`TaxPercentage`),2)) as `SalesIncl`", "HostFact_Invoice.AmountExcl as `TotalAmountExcl`", "HostFact_Invoice.AmountIncl as `TotalAmountIncl`"])->join("HostFact_Products", "HostFact_Products.`ProductCode` = HostFact_InvoiceElements.`ProductCode`")->where("HostFact_InvoiceElements.`InvoiceCode` = HostFact_Invoice.`InvoiceCode`");
        switch ($this->Show) {
            case "all":
                Database_Model::getInstance()->where("HostFact_Invoice.Status", ["IN" => [2, 3, 4, 8, 9]]);
                break;
            case "paid":
                Database_Model::getInstance()->where("HostFact_Invoice.Status", ["IN" => [4, 8, 9]]);
                break;
            case "open":
                Database_Model::getInstance()->where("HostFact_Invoice.Status", ["IN" => [2, 3]]);
                break;
            default:
                $result = Database_Model::getInstance()->where("HostFact_Invoice.Date", [">=" => $this->s_year . "-" . $this->s_month . "-" . $this->s_day])->where("HostFact_Invoice.Date", ["<=" => $this->e_year . "-" . $this->e_month . "-" . $this->e_day])->groupBy("HostFact_Products.ProductName")->orderBy("HostFact_Products.ProductCode", "ASC")->orderBy("HostFact_Products.ProductName", "ASC")->execute();
                foreach ($result as $var) {
                    $sales[$var->ProductName . "." . $var->ProductCode] = ["ProductCode" => htmlspecialchars($var->ProductCode), "ProductName" => htmlspecialchars($var->ProductName), "ProductID" => $var->id, "SalesExcl" => money($var->SalesExcl, false), "SalesIncl" => money($var->SalesIncl, false)];
                }
                return $sales;
        }
    }
    public function showProductGroup()
    {
        $sales = [];
        Database_Model::getInstance()->get(["HostFact_Invoice", "HostFact_InvoiceElements"], ["HostFact_Group.GroupName", "HostFact_Group.id", "HostFact_InvoiceElements.ProductCode", "SUM(HostFact_InvoiceElements.`Number`*HostFact_InvoiceElements.`Periods`*HostFact_InvoiceElements.`PriceExcl`*ROUND((1-HostFact_InvoiceElements.`DiscountPercentage`),4)*ROUND((1-HostFact_Invoice.`Discount`),4)) as `SalesExcl`", "SUM(HostFact_InvoiceElements.`Number`*HostFact_InvoiceElements.`Periods`*HostFact_InvoiceElements.`PriceExcl`*ROUND((1-HostFact_InvoiceElements.`DiscountPercentage`),4)*ROUND((1-HostFact_Invoice.`Discount`),4)*ROUND((1+HostFact_InvoiceElements.`TaxPercentage`),2)) as `SalesIncl`", "HostFact_Invoice.AmountExcl as `TotalAmountExcl`", "HostFact_Invoice.AmountIncl as `TotalAmountIncl`"])->join("HostFact_Products", "HostFact_Products.`ProductCode` = HostFact_InvoiceElements.`ProductCode`")->join("HostFact_GroupRelations", "HostFact_GroupRelations.`Reference` = HostFact_Products.`id` AND HostFact_GroupRelations.`Type`='product'")->join("HostFact_Group", "HostFact_Group.`id` = HostFact_GroupRelations.`Group`")->where("HostFact_InvoiceElements.`InvoiceCode` = HostFact_Invoice.`InvoiceCode`");
        switch ($this->Show) {
            case "all":
                Database_Model::getInstance()->where("HostFact_Invoice.Status", ["IN" => [2, 3, 4, 8, 9]]);
                break;
            case "paid":
                Database_Model::getInstance()->where("HostFact_Invoice.Status", ["IN" => [4, 8, 9]]);
                break;
            case "open":
                Database_Model::getInstance()->where("HostFact_Invoice.Status", ["IN" => [2, 3]]);
                break;
            default:
                $result = Database_Model::getInstance()->where("HostFact_Invoice.Date", [">=" => $this->s_year . "-" . $this->s_month . "-" . $this->s_day])->where("HostFact_Invoice.Date", ["<=" => $this->e_year . "-" . $this->e_month . "-" . $this->e_day])->groupBy("HostFact_Group.GroupName")->orderBy("HostFact_Group.GroupName", "ASC")->execute();
                foreach ($result as $var) {
                    if($var->GroupName != "") {
                        $sales[$var->GroupName] = ["GroupName" => htmlspecialchars($var->GroupName), "ProductGroupID" => $var->id, "SalesExcl" => money($var->SalesExcl, false), "SalesIncl" => money($var->SalesIncl, false)];
                    }
                }
                return $sales;
        }
    }
    public function showDebtorGroup()
    {
        $sales = [];
        Database_Model::getInstance()->get("HostFact_Invoice", ["HostFact_Group.GroupName", "HostFact_Group.id", "SUM(HostFact_Invoice.AmountExcl) as `SalesExcl`", "SUM(HostFact_Invoice.AmountIncl) as `SalesIncl`"])->join("HostFact_Debtors", "HostFact_Debtors.`id` = HostFact_Invoice.`Debtor`")->join("HostFact_GroupRelations", "HostFact_GroupRelations.`Reference` = HostFact_Debtors.`id` AND HostFact_GroupRelations.`Type`='debtor'")->join("HostFact_Group", "HostFact_Group.`id` = HostFact_GroupRelations.`Group`");
        switch ($this->Show) {
            case "all":
                Database_Model::getInstance()->where("HostFact_Invoice.Status", ["IN" => [2, 3, 4, 8, 9]]);
                break;
            case "paid":
                Database_Model::getInstance()->where("HostFact_Invoice.Status", ["IN" => [4, 8, 9]]);
                break;
            case "open":
                Database_Model::getInstance()->where("HostFact_Invoice.Status", ["IN" => [2, 3]]);
                break;
            default:
                $result = Database_Model::getInstance()->where("HostFact_Invoice.Date", [">=" => $this->s_year . "-" . $this->s_month . "-" . $this->s_day])->where("HostFact_Invoice.Date", ["<=" => $this->e_year . "-" . $this->e_month . "-" . $this->e_day])->groupBy("HostFact_Group.GroupName")->orderBy("HostFact_Group.GroupName", "ASC")->execute();
                foreach ($result as $var) {
                    if($var->GroupName != "") {
                        $sales[$var->GroupName] = ["GroupName" => htmlspecialchars($var->GroupName), "DebtorGroupID" => $var->id, "SalesExcl" => money($var->SalesExcl, false), "SalesIncl" => money($var->SalesIncl, false)];
                    }
                }
                return $sales;
        }
    }
    public function showCreditorGroup()
    {
        $sales = [];
        Database_Model::getInstance()->get(["HostFact_CreditInvoice", "HostFact_CreditInvoiceElements"], ["HostFact_Group.GroupName", "HostFact_Group.id", "SUM(HostFact_CreditInvoiceElements.`Number`*HostFact_CreditInvoiceElements.`PriceExcl`*(1-HostFact_CreditInvoice.`PrivatePercentage`)*(1-(ROUND(HostFact_CreditInvoice.`Private`/HostFact_CreditInvoice.`AmountExcl`,2)))) as `SalesExcl`", "SUM(HostFact_CreditInvoiceElements.`Number`*HostFact_CreditInvoiceElements.`PriceExcl`*(1-HostFact_CreditInvoice.`PrivatePercentage`)*(1-(ROUND(HostFact_CreditInvoice.`Private`/HostFact_CreditInvoice.`AmountExcl`,2)))*ROUND((1+HostFact_CreditInvoiceElements.`TaxPercentage`),2)) as `SalesIncl`"])->join("HostFact_Creditors", "HostFact_Creditors.`id` = HostFact_CreditInvoice.`Creditor`")->join("HostFact_GroupRelations", "HostFact_GroupRelations.`Reference` = HostFact_Creditors.`id` AND HostFact_GroupRelations.`Type`='creditor'")->join("HostFact_Group", "HostFact_Group.`id` = HostFact_GroupRelations.`Group`")->where("HostFact_CreditInvoice.`CreditInvoiceCode` = HostFact_CreditInvoiceElements.`CreditInvoiceCode`");
        switch ($this->Show) {
            case "all":
                Database_Model::getInstance()->where("HostFact_CreditInvoice.Status", ["IN" => [1, 2, 3, 8]]);
                break;
            case "paid":
                Database_Model::getInstance()->where("HostFact_CreditInvoice.Status", ["IN" => [3, 8]]);
                break;
            case "open":
                Database_Model::getInstance()->where("HostFact_CreditInvoice.Status", ["IN" => [1, 2]]);
                break;
            default:
                $result = Database_Model::getInstance()->where("HostFact_CreditInvoice.Date", [">=" => $this->s_year . "-" . $this->s_month . "-" . $this->s_day])->where("HostFact_CreditInvoice.Date", ["<=" => $this->e_year . "-" . $this->e_month . "-" . $this->e_day])->groupBy("HostFact_Group.GroupName")->orderBy("HostFact_Group.GroupName", "ASC")->execute();
                foreach ($result as $var) {
                    if($var->GroupName != "") {
                        $sales[$var->GroupName] = ["GroupName" => htmlspecialchars($var->GroupName), "CreditorGroupID" => $var->id, "SalesExcl" => money($var->SalesExcl, false), "SalesIncl" => money($var->SalesIncl, false)];
                    }
                }
                return $sales;
        }
    }
    public function getVATReporting($start_date, $end_date, $vat_type = "sales")
    {
        global $array_country_EU;
        $eu_countries = array_keys($array_country_EU);
        if($vat_type == "sales") {
            $result = Database_Model::getInstance()->get(["HostFact_Invoice", "HostFact_InvoiceElements", "HostFact_Debtors"], ["HostFact_Invoice.InvoiceCode", "HostFact_Invoice.Debtor", "HostFact_Invoice.Country", "HostFact_Invoice.CompanyName", "HostFact_Invoice.Date", "HostFact_Invoice.AmountExcl", "HostFact_Invoice.AmountIncl", "HostFact_Invoice.id", "HostFact_Invoice.VatCalcMethod", "HostFact_Invoice.TaxNumber", "HostFact_Debtors.DebtorCode as `RelationCode`", "CASE WHEN HostFact_Debtors.`CompanyName`!='' THEN HostFact_Debtors.`CompanyName` ELSE CONCAT(HostFact_Debtors.Initials, ' ', HostFact_Debtors.SurName) END AS RelationName", "HostFact_InvoiceElements.TaxPercentage", "SUM(ROUND(HostFact_InvoiceElements.`Number`*HostFact_InvoiceElements.`Periods`*HostFact_InvoiceElements.`PriceExcl`*ROUND((1-HostFact_InvoiceElements.`DiscountPercentage`),4)*ROUND((1-HostFact_Invoice.`Discount`),4)+(IF(HostFact_InvoiceElements.`PriceExcl`*HostFact_InvoiceElements.`Periods`*HostFact_InvoiceElements.`Number`*ROUND((1-HostFact_InvoiceElements.`DiscountPercentage`),4) > 0, 1, -1) * 0.000001),IF(HostFact_Invoice.VatCalcMethod = 'incl',5,2))) as SalesExcl", "ROUND(SUM(ROUND(HostFact_InvoiceElements.`Number`*HostFact_InvoiceElements.`Periods`*HostFact_InvoiceElements.`PriceExcl`*ROUND((1-HostFact_InvoiceElements.`DiscountPercentage`),4)*ROUND((1-HostFact_Invoice.`Discount`),4)+(IF(HostFact_InvoiceElements.`PriceExcl`*HostFact_InvoiceElements.`Periods`*HostFact_InvoiceElements.`Number`*ROUND((1-HostFact_InvoiceElements.`DiscountPercentage`),4) > 0, 1, -1) * 0.000001),2))*ROUND((1+HostFact_InvoiceElements.`TaxPercentage`),4) * 1.00000001,2) as SalesInclExcl", "SUM(ROUND(HostFact_InvoiceElements.`Number`*HostFact_InvoiceElements.`Periods`*HostFact_InvoiceElements.`PriceExcl`*ROUND((1+HostFact_InvoiceElements.`TaxPercentage`),4)*ROUND((1-HostFact_InvoiceElements.`DiscountPercentage`),4)*ROUND((1-HostFact_Invoice.`Discount`),4)+(IF(HostFact_InvoiceElements.`PriceExcl`*HostFact_InvoiceElements.`Periods`*HostFact_InvoiceElements.`Number`*ROUND((1-HostFact_InvoiceElements.`DiscountPercentage`),4) > 0, 1, -1) * 0.000001),2)) as SalesInclIncl"])->where("HostFact_Invoice.Status", ["IN" => [2, 3, 4, 8, 9]])->where("HostFact_Invoice.Date", ["BETWEEN" => [$start_date, $end_date]])->where("HostFact_Invoice.InvoiceCode = HostFact_InvoiceElements.InvoiceCode")->where("HostFact_Debtors.id = HostFact_Invoice.Debtor")->orderBy("HostFact_Invoice.Country", "ASC")->orderBy("HostFact_InvoiceElements.TaxPercentage", "DESC")->orderBy("IF(SUBSTRING(HostFact_Debtors.`DebtorCode`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(HostFact_Debtors.`DebtorCode`,1,1))", "ASC")->orderBy("LENGTH(HostFact_Debtors.`DebtorCode`)", "ASC")->orderBy("HostFact_Debtors.`DebtorCode`", "ASC")->orderBy("IF(SUBSTRING(HostFact_Invoice.`InvoiceCode`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(HostFact_Invoice.`InvoiceCode`,1,1))", "ASC")->orderBy("LENGTH(HostFact_Invoice.`InvoiceCode`)", "ASC")->orderBy("HostFact_Invoice.`InvoiceCode`", "ASC")->groupBy("HostFact_Invoice.InvoiceCode")->groupBy("HostFact_InvoiceElements.TaxPercentage")->execute();
        } else {
            $result = Database_Model::getInstance()->get(["HostFact_CreditInvoice", "HostFact_CreditInvoiceElements", "HostFact_Creditors"], ["HostFact_CreditInvoice.CreditInvoiceCode as `InvoiceCode`", "HostFact_CreditInvoice.Creditor", "HostFact_CreditInvoice.Date", "(HostFact_CreditInvoice.AmountExcl *(1-HostFact_CreditInvoice.`PrivatePercentage`)*(1-IF(HostFact_CreditInvoice.`Private`,(ROUND(HostFact_CreditInvoice.`Private`/HostFact_CreditInvoice.`AmountExcl`,2)),0))) as `AmountExcl`", "(HostFact_CreditInvoice.AmountIncl*(1-HostFact_CreditInvoice.`PrivatePercentage`)*(1-IF(HostFact_CreditInvoice.`Private`,(ROUND(HostFact_CreditInvoice.`Private`/HostFact_CreditInvoice.`AmountExcl`,2)),0))) as `AmountIncl`", "HostFact_CreditInvoice.id", "HostFact_Creditors.CreditorCode as `RelationCode`", "HostFact_Creditors.Country", "HostFact_Creditors.CompanyName", "HostFact_Creditors.TaxNumber", "CASE WHEN HostFact_Creditors.`CompanyName`!='' THEN HostFact_Creditors.`CompanyName` ELSE CONCAT(HostFact_Creditors.Initials, ' ', HostFact_Creditors.SurName) END AS RelationName", "HostFact_CreditInvoiceElements.TaxPercentage", "SUM(ROUND(HostFact_CreditInvoiceElements.`Number`*HostFact_CreditInvoiceElements.`PriceExcl`*(1-HostFact_CreditInvoice.`PrivatePercentage`)*(1-IF(HostFact_CreditInvoice.`Private`,(ROUND(HostFact_CreditInvoice.`Private`/HostFact_CreditInvoice.`AmountExcl`,2)),0)),2)) as SalesExcl", "SUM(ROUND(HostFact_CreditInvoiceElements.`Number`*HostFact_CreditInvoiceElements.`PriceExcl`*(1-HostFact_CreditInvoice.`PrivatePercentage`)*(1-IF(HostFact_CreditInvoice.`Private`,(ROUND(HostFact_CreditInvoice.`Private`/HostFact_CreditInvoice.`AmountExcl`,2)),0))*ROUND((1+HostFact_CreditInvoiceElements.`TaxPercentage`),2),2)) as SalesIncl"])->where("HostFact_CreditInvoice.Status", ["IN" => [1, 2, 3, 8]])->where("HostFact_CreditInvoice.Date", ["BETWEEN" => [$start_date, $end_date]])->where("HostFact_CreditInvoice.CreditInvoiceCode = HostFact_CreditInvoiceElements.CreditInvoiceCode")->where("HostFact_Creditors.id = HostFact_CreditInvoice.Creditor")->orderBy("HostFact_Creditors.Country", "ASC")->orderBy("HostFact_CreditInvoiceElements.TaxPercentage", "DESC")->orderBy("IF(SUBSTRING(HostFact_Creditors.`CreditorCode`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(HostFact_Creditors.`CreditorCode`,1,1))", "ASC")->orderBy("LENGTH(HostFact_Creditors.`CreditorCode`)", "ASC")->orderBy("HostFact_Creditors.`CreditorCode`", "ASC")->orderBy("IF(SUBSTRING(HostFact_CreditInvoice.`CreditInvoiceCode`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(HostFact_CreditInvoice.`CreditInvoiceCode`,1,1))", "ASC")->orderBy("LENGTH(HostFact_CreditInvoice.`CreditInvoiceCode`)", "ASC")->orderBy("HostFact_CreditInvoice.`CreditInvoiceCode`", "ASC")->groupBy("HostFact_CreditInvoice.CreditInvoiceCode")->groupBy("HostFact_CreditInvoiceElements.TaxPercentage")->execute();
        }
        global $company;
        $vat_report_array = [];
        $vat_report_array["OWN-COUNTRY"] = ["AmountExcl" => 0, "AmountIncl" => 0, "details" => []];
        $vat_report_array["EU-BUSINESS"] = ["AmountExcl" => 0, "AmountIncl" => 0, "details" => []];
        $vat_report_array["EU-CONSUMER"] = ["AmountExcl" => 0, "AmountIncl" => 0, "details" => []];
        $vat_report_array["OUTSIDE-EU"] = ["AmountExcl" => 0, "AmountIncl" => 0, "details" => []];
        if($result && is_array($result)) {
            foreach ($result as $_inv) {
                if(!isset($_inv->SalesIncl)) {
                    $_inv->SalesIncl = $_inv->VatCalcMethod == "incl" ? $_inv->SalesInclIncl : $_inv->SalesInclExcl;
                }
                if($_inv->Country == $company->Country) {
                    $_reporting_type = "OWN-COUNTRY";
                } elseif($_inv->CompanyName && ($vat_type == "purchases" || $_inv->TaxNumber) && in_array($_inv->Country, $eu_countries)) {
                    $_reporting_type = "EU-BUSINESS";
                } elseif(in_array($_inv->Country, $eu_countries)) {
                    $_reporting_type = "EU-CONSUMER";
                } else {
                    $_reporting_type = "OUTSIDE-EU";
                }
                if(!isset($vat_report_array[$_reporting_type]["details"][$_inv->Country])) {
                    $vat_report_array[$_reporting_type]["details"][$_inv->Country] = ["AmountExcl" => 0, "AmountIncl" => 0, "details" => []];
                }
                if(!isset($vat_report_array[$_reporting_type]["details"][$_inv->Country]["details"][$_inv->TaxPercentage])) {
                    $vat_report_array[$_reporting_type]["details"][$_inv->Country]["details"][$_inv->TaxPercentage] = ["AmountExcl" => 0, "AmountIncl" => 0, "details" => []];
                }
                if(!isset($vat_report_array[$_reporting_type]["details"][$_inv->Country]["details"][$_inv->TaxPercentage]["details"][$_inv->RelationCode])) {
                    $vat_report_array[$_reporting_type]["details"][$_inv->Country]["details"][$_inv->TaxPercentage]["details"][$_inv->RelationCode] = ["AmountExcl" => 0, "AmountIncl" => 0, "details" => []];
                }
                $vat_report_array[$_reporting_type]["AmountExcl"] += $_inv->SalesExcl;
                $vat_report_array[$_reporting_type]["AmountIncl"] += $_inv->SalesIncl;
                $vat_report_array[$_reporting_type]["details"][$_inv->Country]["AmountExcl"] += $_inv->SalesExcl;
                $vat_report_array[$_reporting_type]["details"][$_inv->Country]["AmountIncl"] += $_inv->SalesIncl;
                $vat_report_array[$_reporting_type]["details"][$_inv->Country]["details"][$_inv->TaxPercentage]["AmountExcl"] += $_inv->SalesExcl;
                $vat_report_array[$_reporting_type]["details"][$_inv->Country]["details"][$_inv->TaxPercentage]["AmountIncl"] += $_inv->SalesIncl;
                $vat_report_array[$_reporting_type]["details"][$_inv->Country]["details"][$_inv->TaxPercentage]["details"][$_inv->RelationCode]["AmountExcl"] += $_inv->SalesExcl;
                $vat_report_array[$_reporting_type]["details"][$_inv->Country]["details"][$_inv->TaxPercentage]["details"][$_inv->RelationCode]["AmountIncl"] += $_inv->SalesIncl;
                $vat_report_array[$_reporting_type]["details"][$_inv->Country]["details"][$_inv->TaxPercentage]["details"][$_inv->RelationCode]["details"][$_inv->InvoiceCode] = $_inv;
            }
        }
        return $vat_report_array;
    }
    public function showPeriodic()
    {
        global $array_months;
        $sales = [];
        $unit = substr($this->SelectedPeriod, 0, 1);
        switch ($unit) {
            case "y":
            case "q":
                $sales["Label"] = __("quarter") . " " . $this->e_month / 3 . " - " . $this->s_year;
                break;
            case "m":
                $timestamp = mktime(0, 0, 0, $this->e_month, 1, $this->s_year);
                $sales["Label"] = $array_months[date("m", $timestamp)] . " " . $this->s_year;
                break;
            default:
                $sales["Label"] = $this->s_year;
                $start_date = sprintf("%s-%02d-%02d", $this->s_year, $this->s_month, $this->s_day);
                $end_date = sprintf("%s-%02d-%02d", $this->e_year, $this->e_month, $this->e_day);
                Database_Model::getInstance()->get("HostFact_PeriodicElements", ["HostFact_PeriodicElements.id", "HostFact_PeriodicElements.ProductCode", "HostFact_Products.id as `ProductID`", "HostFact_Products.ProductName", "(HostFact_PeriodicElements.`Number`*HostFact_PeriodicElements.`Periods`*HostFact_PeriodicElements.`PriceExcl`*ROUND((1-HostFact_PeriodicElements.`DiscountPercentage`),4)) as `AmountExcl`", "((HostFact_PeriodicElements.`Number`*HostFact_PeriodicElements.`Periods`*HostFact_PeriodicElements.`PriceExcl`*ROUND((1-HostFact_PeriodicElements.`DiscountPercentage`),4)) * ROUND(HostFact_PeriodicElements.`TaxPercentage`,2)) as `AmountTax`", "((HostFact_PeriodicElements.`Number`*HostFact_PeriodicElements.`Periods`*HostFact_PeriodicElements.`PriceExcl`*ROUND((1-HostFact_PeriodicElements.`DiscountPercentage`),4)) * ROUND((1+HostFact_PeriodicElements.`TaxPercentage`),2)) as `AmountIncl`", "(SELECT CASE HostFact_Debtors.`InvoiceCollect` WHEN '2' THEN CONCAT(DATE_FORMAT(HostFact_PeriodicElements.`NextDate`, '%Y-%m-'),IF(DAYOFMONTH(HostFact_PeriodicElements.`NextDate`) >= 15, '15', '01')) WHEN '1' THEN CONCAT(DATE_FORMAT(HostFact_PeriodicElements.`NextDate`, '%Y-%m-'),'01') WHEN '-1' THEN " . (INVOICE_COLLECT_ENABLED == "yes" ? "CONCAT(DATE_FORMAT(HostFact_PeriodicElements.`NextDate`, '%Y-%m-'),IF(DAYOFMONTH(HostFact_PeriodicElements.`NextDate`) >= 15, '" . (INVOICE_COLLECT_TPM == 1 ? "01" : "15") . "', '01'))" : "HostFact_PeriodicElements.`NextDate`") . " ELSE HostFact_PeriodicElements.`NextDate` END) as NextDate", "HostFact_PeriodicElements.Periods", "HostFact_PeriodicElements.Periodic", "HostFact_PeriodicElements.TerminationDate", "HostFact_PeriodicElements.StartPeriod", "HostFact_Debtors.InvoiceCollect", "HostFact_Debtors.PeriodicInvoiceDays"])->join("HostFact_Products", "HostFact_Products.`ProductCode` = HostFact_PeriodicElements.`ProductCode`")->join("HostFact_Debtors", "HostFact_Debtors.`id` = HostFact_PeriodicElements.`Debtor`")->where("HostFact_PeriodicElements.Status", ["NOT IN" => [8, 9]])->orWhere([["HostFact_PeriodicElements.TerminationDate", "0000-00-00"], ["AND" => [["HostFact_PeriodicElements.`TerminationDate` > HostFact_PeriodicElements.StartPeriod"], ["HostFact_PeriodicElements.TerminationDate", [">=" => $start_date]]]]]);
                if($this->AutoRenew == "yes") {
                    Database_Model::getInstance()->where("HostFact_PeriodicElements.AutoRenew", "yes");
                } elseif($this->AutoRenew == "no_once") {
                    Database_Model::getInstance()->where("HostFact_PeriodicElements.AutoRenew", ["IN" => ["no", "once"]]);
                }
                if($this->DebtorGroupFilter) {
                    Database_Model::getInstance()->where("HostFact_Debtors.id", ["IN" => ["RAW" => "(SELECT `Reference` FROM `HostFact_GroupRelations` WHERE `Type`='debtor' AND `Group`=:debtor_group_id)"]])->bindValue("debtor_group_id", $this->DebtorGroupFilter);
                }
                $result = Database_Model::getInstance()->where("(SELECT CASE HostFact_Debtors.`InvoiceCollect` WHEN '2' THEN CONCAT(DATE_FORMAT(HostFact_PeriodicElements.`NextDate`, '%Y-%m-'),IF(DAYOFMONTH(HostFact_PeriodicElements.`NextDate`) >= 15, '15', '01')) WHEN '1' THEN CONCAT(DATE_FORMAT(HostFact_PeriodicElements.`NextDate`, '%Y-%m-'),'01') WHEN '-1' THEN " . (INVOICE_COLLECT_ENABLED == "yes" ? "CONCAT(DATE_FORMAT(HostFact_PeriodicElements.`NextDate`, '%Y-%m-'),IF(DAYOFMONTH(HostFact_PeriodicElements.`NextDate`) >= 15, '" . (INVOICE_COLLECT_TPM == 1 ? "01" : "15") . "', '01'))" : "HostFact_PeriodicElements.`NextDate`") . " ELSE HostFact_PeriodicElements.`NextDate` END)", ["<=" => $end_date])->orderBy("HostFact_PeriodicElements.ProductCode", "ASC")->orderBy("HostFact_Products.ProductName", "ASC")->asArray()->execute();
                $vars = [];
                foreach ($result as $var) {
                    if($var["Periodic"] == "d" || $var["Periodic"] == "w") {
                        $multiply = 0;
                        $d1 = $var["NextDate"] < $start_date ? strtotime($start_date) : strtotime($var["NextDate"]);
                        $d2 = $var["TerminationDate"] < $end_date && $var["TerminationDate"] != "0000-00-00" ? strtotime($var["TerminationDate"]) : strtotime($end_date);
                        $days = ($d2 - $d1) / 86400;
                        $result["extra"] = $start_date <= $var["NextDate"] ? 1 : 0;
                        switch ($var["Periodic"]) {
                            case "d":
                                $multiply = $result["extra"] + floor($days / $var["Periods"]);
                                break;
                            case "w":
                                $multiply = $result["extra"] + floor(floor($days / 7) / $var["Periods"]);
                                break;
                        }
                    } else {
                        $var_periodic_invoice_days = $var["PeriodicInvoiceDays"] != "-1" ? $var["PeriodicInvoiceDays"] : PERIODIC_INVOICE_DAYS;
                        if($var["InvoiceCollect"] == "-1" && INVOICE_COLLECT_ENABLED == "yes" || "0" < $var["InvoiceCollect"]) {
                            $var_periodic_invoice_days = 0;
                        }
                        $start = strtotime($start_date);
                        $end = date("Y-m-d", strtotime("-1 day", strtotime($var["TerminationDate"]))) < $end_date && $var["TerminationDate"] != "0000-00-00" ? strtotime("-1 day", strtotime($var["TerminationDate"])) : strtotime($end_date);
                        $invoiceDate = strtotime($var["NextDate"]);
                        $nextperiod = strtotime($var["StartPeriod"]);
                        $multiply = 0;
                        $max_tries = 0;
                        while ($invoiceDate <= $end) {
                            if($var["TerminationDate"] != "0000-00-00" && $var["TerminationDate"] <= date("Y-m-d", $nextperiod)) {
                            } else {
                                $max_tries++;
                                if($start <= $invoiceDate) {
                                    $multiply++;
                                }
                                $nextperiod_day_of_month = date("d", $nextperiod);
                                $nextperiod = strtotime(date("Y-m-01", $nextperiod));
                                switch ($var["Periodic"]) {
                                    case "m":
                                        $interval = "+ " . $var["Periods"] . " month";
                                        break;
                                    case "k":
                                        $interval = "+ " . 3 * $var["Periods"] . " month";
                                        break;
                                    case "h":
                                        $interval = "+ " . 6 * $var["Periods"] . " month";
                                        break;
                                    case "j":
                                        $interval = "+ " . $var["Periods"] . " year";
                                        break;
                                    case "t":
                                        $interval = "+ " . 2 * $var["Periods"] . " year";
                                        $nextperiod = strtotime($interval, $nextperiod);
                                        $nextperiod = strtotime(date("Y-m-" . min($nextperiod_day_of_month, date("t", $nextperiod)), $nextperiod));
                                        if((int) $var_periodic_invoice_days === 0) {
                                            $invoiceDate = strtotime($interval, $invoiceDate);
                                        } else {
                                            $plusmin = 0 < $var_periodic_invoice_days ? "-" : "+";
                                            $invoiceDate = strtotime($plusmin . abs($var_periodic_invoice_days) . " days", $nextperiod);
                                        }
                                        if(50 <= $max_tries) {
                                            $this->Error[] = __("statistics future period to far away");
                                            return [];
                                        }
                                        break;
                                    default:
                                        $this->Error[] = __("subscription with invalid period found");
                                        $multiply = 0;
                                }
                            }
                        }
                    }
                    $var["SalesExcl"] = $var["AmountExcl"] * $multiply;
                    $var["SalesBTW"] = $var["AmountTax"] * $multiply;
                    $var["SalesIncl"] = $var["AmountIncl"] * $multiply;
                    if(array_key_exists($var["ProductCode"], $vars)) {
                        $vars[$var["ProductCode"]]["SalesExcl"] += $var["SalesExcl"];
                        $vars[$var["ProductCode"]]["SalesBTW"] += $var["SalesBTW"];
                        $vars[$var["ProductCode"]]["SalesIncl"] += $var["SalesIncl"];
                    } else {
                        $vars[$var["ProductCode"]] = $var;
                    }
                }
                if(strtotime($start_date . " 23:59:59") < time()) {
                    $this->Warning[] = sprintf(__("no future period"));
                }
                foreach ($vars as $key => $var) {
                    $sales[$var["ProductCode"]] = ["ProductName" => htmlspecialchars($var["ProductName"]), "ProductID" => $var["ProductID"], "SalesExcl" => money($var["SalesExcl"], false), "SalesBTW" => money($var["SalesBTW"], false), "SalesIncl" => money($var["SalesIncl"], false)];
                    $this->real_SalesExcl += round($var["SalesExcl"], 2);
                    $this->real_SalesBTW += round($var["SalesBTW"], 2);
                    $this->real_SalesIncl += round($var["SalesIncl"], 2);
                }
                return $sales;
        }
    }
    public function date_diff($d1, $d2)
    {
        if($d1 <= $d2) {
            $temp = $d2;
            $d2 = $d1;
            $d1 = $temp;
            $diff["signed"] = false;
        } else {
            $temp = $d1;
            $diff["signed"] = true;
        }
        $d1 = date_parse(date("Y-m-d H:i:s", $d1));
        $d2 = date_parse(date("Y-m-d H:i:s", $d2));
        if($d2["day"] <= $d1["day"]) {
            $diff["day"] = $d1["day"] - $d2["day"];
        } else {
            $d1["month"]--;
            $diff["day"] = date("t", $temp) - $d2["day"] + $d1["day"];
        }
        if($d2["month"] <= $d1["month"]) {
            $diff["month"] = $d1["month"] - $d2["month"];
        } else {
            $d1["year"]--;
            $diff["month"] = 12 - $d2["month"] + $d1["month"];
        }
        $diff["year"] = $d1["year"] - $d2["year"];
        return $diff;
    }
    public function setStartDate($db_date = "")
    {
        $tmp = explode("-", $db_date);
        $this->s_day = isset($tmp[2]) ? $tmp[2] : "01";
        $this->s_month = isset($tmp[1]) ? $tmp[1] : "01";
        $this->s_year = $tmp[0];
        $this->SalesExcl = 0;
        $this->SalesBTW = 0;
        $this->SalesIncl = 0;
        $this->TotalAmountExcl = 0;
        $this->TotalAmountIncl = 0;
        $this->TotalBTW = 0;
        $this->PaidBTW = 0;
        $this->real_SalesExcl = 0;
        $this->real_SalesBTW = 0;
        $this->real_SalesIncl = 0;
        return true;
    }
    public function setEndDate($db_date = "")
    {
        $tmp = explode("-", $db_date);
        $this->e_day = isset($tmp[2]) ? $tmp[2] : "31";
        $this->e_month = isset($tmp[1]) ? $tmp[1] : "12";
        $this->e_year = $tmp[0];
        return false;
    }
    private function getPreInvoicedTurnover($date)
    {
        if(isset($result[$date])) {
            return $result[$date];
        }
        $rateSql = "ROUND(IF(HostFact_InvoiceElements.StartPeriod < :date, \n                    (IF(HostFact_InvoiceElements.EndPeriod > :date,\n                    DATEDIFF(EndPeriod,:date)/DATEDIFF(EndPeriod,StartPeriod),\n                    0))  \n                ,1  ),4)";
        $result[$date] = Database_Model::getInstance()->get(["HostFact_Invoice", "HostFact_InvoiceElements", "HostFact_Debtors"], ["HostFact_Invoice.id as `InvoiceId`", "SUBSTRING(HostFact_Invoice.`Date`,1,10) as InvoiceDate", "HostFact_InvoiceElements.id as `LineId`", "HostFact_InvoiceElements.InvoiceCode", "HostFact_InvoiceElements.ProductCode", "HostFact_InvoiceElements.StartPeriod", "HostFact_InvoiceElements.EndPeriod", "HostFact_InvoiceElements.LineAmountExcl", $rateSql . " as `Rate`", "ROUND(ROUND(LineAmountExcl * ROUND(1-HostFact_Invoice.Discount,4),2) * " . $rateSql . ",2) as `NewYearTurnover`", "HostFact_InvoiceElements.Description", "HostFact_Invoice.Discount as `InvoiceDiscount`", "HostFact_Debtors.DebtorCode"])->bindValue("date", $date)->where("HostFact_Invoice.`Status`", [">" => 1])->where("HostFact_Invoice.`InvoiceCode` = HostFact_InvoiceElements.`InvoiceCode`")->where("HostFact_Invoice.`Debtor` = HostFact_Debtors.`id`")->where("HostFact_Invoice.Date", ["<" => $date])->where("HostFact_InvoiceElements.EndPeriod", [">" => $date])->where("HostFact_InvoiceElements.LineAmountExcl", ["!=" => 0])->where("HostFact_InvoiceElements.Periodic", ["!=" => ""])->orderBy("HostFact_Invoice.`InvoiceCode`", "ASC")->execute();
        return $result[$date];
    }
    public function getPreInvoicedTurnoverPerProduct($date)
    {
        $result = $this->getPreInvoicedTurnover($date);
        $resultPerProduct = ["-" => 0];
        foreach ($result as $item) {
            $productCode = trim($item->ProductCode);
            $productCode = $productCode ? $productCode : "-";
            if(!isset($resultPerProduct[$productCode])) {
                $resultPerProduct[$productCode] = $item->NewYearTurnover;
            } else {
                $resultPerProduct[$productCode] += $item->NewYearTurnover;
            }
        }
        return $resultPerProduct;
    }
    public function getPreInvoicedSpecification($date)
    {
        $result = $this->getPreInvoicedTurnover($date);
        $csvContent = [];
        $csvRow = [];
        $csvRow["date"] = __("invoice date");
        $csvRow["invoicecode"] = __("invoicecode");
        $csvRow["rate"] = __("pre-invoiced rate");
        $csvRow["preinvoiced"] = __("pre-invoiced turnover");
        $csvRow["debtorcode"] = __("export.DebtorCode");
        $csvRow["productcode"] = __("export.ProductCode");
        $csvRow["description"] = __("export.Description");
        $csvRow["startperiod"] = __("period start");
        $csvRow["endperiod"] = __("period end");
        $csvRow["lineamount"] = __("line total");
        $csvRow["invoicediscount"] = __("pre-invoiced invoice discount");
        $csvRow["lineamountafterdiscount"] = __("pre-invoice line total after invoice discount");
        $csvContent[] = $csvRow;
        foreach ($result as $item) {
            $csvRow = [];
            $csvRow["date"] = rewrite_date_db2site($item->InvoiceDate);
            $csvRow["invoicecode"] = $item->InvoiceCode;
            $csvRow["rate"] = number_format(100 * $item->Rate, 2, ".", "");
            $csvRow["preinvoiced"] = $item->NewYearTurnover;
            $csvRow["debtorcode"] = $item->DebtorCode;
            $csvRow["productcode"] = $item->ProductCode;
            $csvRow["description"] = $item->Description;
            $csvRow["startperiod"] = rewrite_date_db2site($item->StartPeriod);
            $csvRow["endperiod"] = rewrite_date_db2site($item->EndPeriod);
            $csvRow["lineamount"] = $item->LineAmountExcl;
            $csvRow["invoicediscount"] = number_format(100 * $item->InvoiceDiscount, 2, ".", "");
            $csvRow["lineamountafterdiscount"] = number_format($item->LineAmountExcl * (1 - $item->InvoiceDiscount), 2, ".", "");
            $csvContent[] = $csvRow;
        }
        $filename = __("pre-invoiced turnover filename", ["date" => rewrite_date_db2site($date), "created" => date("d-m-Y-H-i-s")]);
        $filepath = "temp/" . $filename;
        $f = fopen($filepath, "w");
        fwrite($f, "﻿");
        foreach ($csvContent as $line) {
            fputcsv($f, $line);
        }
        fclose($f);
        return $filename;
    }
}

?>