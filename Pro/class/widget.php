<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class widget
{
    public $Identifier;
    public $Employee;
    public $Widget;
    public $Option1;
    public $Position;
    public $File;
    public $Period;
    public $Width;
    public $Type;
    public $Options;
    public $Error;
    public function show($employeeWidgetID = true)
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for widget");
            return false;
        }
        if($employeeWidgetID === true) {
            $result = Database_Model::getInstance()->getOne("HostFact_EmployeeWidgets", ["HostFact_EmployeeWidgets.id", "HostFact_EmployeeWidgets.Widget", "HostFact_EmployeeWidgets.Option1", "HostFact_EmployeeWidgets.Position", "HostFact_Widgets.Name", "HostFact_Widgets.File", "HostFact_Widgets.Period", "HostFact_Widgets.Width", "HostFact_Widgets.Type"])->join("HostFact_Widgets", "HostFact_Widgets.`id` = HostFact_EmployeeWidgets.`Widget`")->where("HostFact_EmployeeWidgets.id", $this->Identifier)->execute();
        } else {
            $result = Database_Model::getInstance()->getOne("HostFact_Widgets", ["HostFact_EmployeeWidgets.id", "HostFact_EmployeeWidgets.Option1", "HostFact_EmployeeWidgets.Position", "HostFact_Widgets.id as `Widget`", "HostFact_Widgets.Name", "HostFact_Widgets.File", "HostFact_Widgets.Period", "HostFact_Widgets.Width", "HostFact_Widgets.Type"])->join("HostFact_EmployeeWidgets", "HostFact_Widgets.`id` = HostFact_EmployeeWidgets.`Widget`")->where("HostFact_Widgets.id", $this->Identifier)->execute();
        }
        if($employeeWidgetID && (!isset($result->id) || empty($result->id)) || !$employeeWidgetID && empty($result->Widget)) {
            $this->Error[] = __("invalid identifier for widget");
            return false;
        }
        foreach ($result as $key => $value) {
            $this->{$key} = htmlspecialchars($value);
        }
        return true;
    }
    public function add()
    {
        $position = Database_Model::getInstance()->getOne("HostFact_EmployeeWidgets", ["MAX(`Position`)+1 AS `Position`"])->where("Employee", $_SESSION["UserPro"])->execute();
        $result = Database_Model::getInstance()->insert("HostFact_EmployeeWidgets", ["Employee" => $_SESSION["UserPro"], "Widget" => $this->Identifier, "Position" => $position->Position])->execute();
        $this->Identifier = $result;
        if($this->Options) {
            foreach ($this->Options as $key => $value) {
                Database_Model::getInstance()->update("HostFact_EmployeeWidgets", ["Option" . $key => $value])->where("id", $this->Identifier)->execute();
            }
        }
        return true;
    }
    public function edit()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for widget");
            return false;
        }
        if($this->Options) {
            foreach ($this->Options as $key => $value) {
                Database_Model::getInstance()->update("HostFact_EmployeeWidgets", ["Option" . $key => $value])->where("id", $this->Identifier)->execute();
            }
        }
        return true;
    }
    public function remove()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for widget");
            return false;
        }
        $result = Database_Model::getInstance()->delete("HostFact_EmployeeWidgets")->where("id", $this->Identifier)->execute();
        if(!$result) {
            return false;
        }
        return true;
    }
    public function updateOrder($order)
    {
        if(!is_array($order)) {
            return false;
        }
        foreach ($order as $key => $value) {
            Database_Model::getInstance()->update("HostFact_EmployeeWidgets", ["Position" => $key])->where("id", $value)->execute();
        }
        return true;
    }
    public function all($employee = false)
    {
        if(isset($employee) && $employee === true) {
            $result = Database_Model::getInstance()->get("HostFact_EmployeeWidgets", ["HostFact_EmployeeWidgets.id", "HostFact_EmployeeWidgets.Employee", "HostFact_EmployeeWidgets.Widget", "HostFact_EmployeeWidgets.Option1", "HostFact_EmployeeWidgets.Position", "HostFact_Widgets.Name", "HostFact_Widgets.File", "HostFact_Widgets.Width", "HostFact_Widgets.Type"])->join("HostFact_Widgets", "HostFact_Widgets.`id` = HostFact_EmployeeWidgets.`Widget`")->where("HostFact_EmployeeWidgets.Employee", $_SESSION["UserPro"])->orderBy("HostFact_EmployeeWidgets.Position")->asArray()->execute();
        } else {
            $result = Database_Model::getInstance()->get("HostFact_Widgets")->asArray()->execute();
        }
        $list = [];
        foreach ($result as $_widget) {
            $list[$_widget["id"]] = $_widget;
        }
        $list["CountRows"] = count($result);
        return $list;
    }
    public function getDates($period = "m")
    {
        $dates = [];
        switch ($period) {
            case "d":
            case "day":
                $dates["start_date"] = date("Y-m-d");
                $dates["end_date"] = date("Y-m-d");
                break;
            case "w":
            case "week":
                $dates["start_date"] = date("Y-m-d", week2date(date("Y"), date("W")));
                $dates["end_date"] = date("Y-m-d", strtotime($start_date . " +6 day"));
                break;
            case "m":
            case "month":
            case "q":
            case "quarter":
                $current_quarter = ceil(date("n") / 3);
                $start_month = ($current_quarter - 1) * 3 + 1;
                $end_month = $start_month + 2;
                $dates["start_date"] = date("Y-" . str_pad($start_month, 2, "0", STR_PAD_LEFT) . "-01");
                $dates["end_date"] = date("Y-" . str_pad($end_month, 2, "0", STR_PAD_LEFT) . "-t", strtotime(date("Y-" . str_pad($end_month, 2, "0", STR_PAD_LEFT) . "-01")));
                break;
            case "y":
            case "year":
                $dates["start_date"] = date("Y-01-01");
                $dates["end_date"] = date("Y-12-31");
                break;
            default:
                $dates["start_date"] = date("Y-m-01");
                $dates["end_date"] = date("Y-m-t");
                return $dates;
        }
    }
    public function getWidgetTurnover($period = "m")
    {
        require_once "class/statistic.php";
        $stats = new statistic();
        $dates = $this->getDates($period);
        $stats->setStartDate($dates["start_date"]);
        $stats->setEndDate($dates["end_date"]);
        $stats->show();
        $result = $stats;
        return isset($result) ? $result : false;
    }
    public function getWidgetRevenue($period = "m")
    {
        require_once "class/statistic.php";
        $stats = new statistic();
        $dates = $this->getDates($period);
        $stats->setStartDate($dates["start_date"]);
        $stats->setEndDate($dates["end_date"]);
        $stats->show();
        $stats->showCredit();
        $result = $stats;
        return isset($result) ? $result : false;
    }
    public function getWidgetExpenses($period = "m")
    {
        require_once "class/statistic.php";
        $stats = new statistic();
        $dates = $this->getDates($period);
        $stats->setStartDate($dates["start_date"]);
        $stats->setEndDate($dates["end_date"]);
        $stats->showCredit();
        $result = $stats;
        return isset($result) ? $result : false;
    }
    public function getWidgetInvoices($period = "m")
    {
        $dates = $this->getDates($period);
        $invoices = Database_Model::getInstance()->getOne("HostFact_Invoice", ["COUNT(`id`) as `count`"])->where("Date", [">=" => $dates["start_date"]])->where("Date", ["<=" => $dates["end_date"]])->where("Status", ["!=" => 0])->execute();
        return $invoices->count;
    }
    public function getWidgetSubscriptions($period = "m")
    {
        require_once "class/statistic.php";
        $stats = new statistic();
        $dates = $this->getDates($period);
        $stats->setStartDate(date("Y-m-d", strtotime("-1 year")));
        $stats->setEndDate($dates["end_date"]);
        $sales = $stats->showPeriodic();
        $result = $stats->real_SalesExcl;
        return isset($result) ? $result : false;
    }
    public function getWidgetGraphRevenue($period = "last12m")
    {
        require_once "class/statistic.php";
        $stats = new statistic();
        $stats->isWidget = true;
        global $array_months_short;
        switch ($period) {
            case "last3m":
                $start_date = date("Y-m-d", strtotime("-2 month", strtotime(date("Y-m-01"))));
                $end_date = date("Y-m-t");
                $number = 3;
                break;
            case "last6m":
                $start_date = date("Y-m-d", strtotime("-5 month", strtotime(date("Y-m-01"))));
                $end_date = date("Y-m-t");
                $number = 6;
                break;
            default:
                $start_date = date("Y-m-d", strtotime("-11 month", strtotime(date("Y-m-01"))));
                $end_date = date("Y-m-t");
                $number = 12;
                $stats->setStartDate(date("Y-m-d", strtotime("-1 year", strtotime(substr($start_date, 0, 7) . "-01"))));
                $stats->setEndDate(date("Y-m-t", strtotime("-1 year", strtotime(substr($end_date, 0, 7) . "-01"))));
                $sales_lastyear = $stats->showSalesPerUnit();
                $stats->setStartDate($start_date);
                $stats->setEndDate($end_date);
                $sales = $stats->showSalesPerUnit();
                if(empty($sales)) {
                    return false;
                }
                $return = [];
                $x = (int) date("n");
                for ($i = 0; $i < $number; $i++) {
                    $tmp_array = ["label" => substr($array_months_short[$x], 0, 1), "value" => isset($sales[$x]["SalesExcl"]) ? $sales[$x]["SalesExcl"] : 0];
                    if(!empty($sales_lastyear)) {
                        $tmp_array["value_lastyear"] = isset($sales_lastyear[$x]["SalesExcl"]) ? $sales_lastyear[$x]["SalesExcl"] : 0;
                    }
                    $return[] = $tmp_array;
                    $x--;
                    if($x === 0) {
                        $x = 12;
                    }
                }
                $return = array_reverse($return);
                return $return;
        }
    }
    public function getWidgetOrders($period)
    {
        $dates = $this->getDates($period);
        $orders = Database_Model::getInstance()->getOne("HostFact_NewOrder", ["COUNT(`id`) as `Count`", "SUM(`AmountExcl`) AS `TotalAmountExcl`"])->where("Date", [">=" => $dates["start_date"] . " 00:00:00"])->where("Date", ["<=" => $dates["end_date"] . " 23:59:59"])->where("Status", ["!=" => 9])->asArray()->execute();
        $result = [];
        foreach ($orders as $k => $v) {
            $result[$k] = !empty($v) ? $v : 0;
        }
        return $result;
    }
    public function addWidgetOptions($widget)
    {
        $this->Identifier = $widget;
        $this->show(false);
        if($this->Period != "") {
            $periods = explode(",", $this->Period);
            foreach ($periods as $key => $value) {
                $options[$this->Widget]["period"][$value] = $value;
            }
        }
        if(isset($options[$widget]) && is_array($options[$widget])) {
            $options[$widget]["current"] = in_array("month", $periods) ? "month" : "";
            return $options[$widget];
        }
        return false;
    }
    public function getWidgetOptions($widgetID)
    {
        $this->Identifier = $widgetID;
        $this->show();
        if($this->Period != "") {
            $periods = explode(",", $this->Period);
            foreach ($periods as $key => $value) {
                $options[$this->Widget]["period"][$value] = $value;
            }
        }
        if(isset($options[$this->Widget]) && is_array($options[$this->Widget])) {
            $options[$widgetID] = $options[$this->Widget];
            $options[$widgetID]["current"] = $this->Option1;
            return $options[$widgetID];
        }
        return false;
    }
}

?>