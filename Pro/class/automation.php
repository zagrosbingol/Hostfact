<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class automation
{
    public $Identifier;
    public $Variable;
    public $Value;
    public $Run;
    public $Exception;
    public $Table;
    public $Success;
    public $Warning;
    public $Error;
    public $Variables = ["Identifier", "Variable", "Value", "Run", "Exception"];
    public function __construct()
    {
        $this->Success = [];
        $this->Warning = [];
        $this->Error = [];
    }
    public function show()
    {
        $result = Database_Model::getInstance()->get("HostFact_Automation")->execute();
        if($result) {
            foreach ($result as $_automation_info) {
                $this->{$_automation_info->Variable . "_value"} = $_automation_info->Value;
                $this->{$_automation_info->Variable . "_run"} = $_automation_info->Run;
                $this->{$_automation_info->Variable . "_exception"} = $_automation_info->Exception;
            }
            return true;
        } else {
            return false;
        }
    }
    public function edit($set)
    {
        foreach (["acceptorder", "sentinvoice", "remindersummation"] as $_type) {
            $set[$_type]["exception"] = isset($set[$_type]["checkbox_exception"]) ? json_encode($set[$_type]["checkbox_exception"]) : "";
        }
        foreach ($set as $k => $v) {
            Database_Model::getInstance()->update("HostFact_Automation", ["Value" => $v["value"], "Run" => $v["run"], "Exception" => isset($v["exception"]) ? $v["exception"] : ""])->where("Variable", $k)->execute();
        }
        $this->show();
        if($this->acceptorder_value == "1" && $this->sentinvoice_value == "1" && ORDERACCEPT_STATUS != 1) {
            $this->Warning[] = __("acceptorder invoice no auto sent");
        }
        if($this->sentinvoice_value == "1") {
            require_once "class/template.php";
            $template = new template();
            if((int) $template->getStandard("invoice") === 0) {
                $this->Warning[] = __("no standard invoice template");
            }
            if(!is_writable("temp/")) {
                $this->Warning[] = __("temp folder not writable");
            }
            if((int) ORDERACCEPT_STATUS === 0) {
                $this->Warning[] = __("auto sent invoices, but invoices will be created as concept invoices");
            }
        }
        if($this->makeinvoice_value == "1") {
            require_once "class/template.php";
            $template = new template();
            if((int) $template->getStandard("invoice") === 0) {
                $this->Warning[] = __("no standard invoice template");
            }
        }
        if($this->registerdomain_value == "1") {
            require_once "class/registrar.php";
            $registrar = new registrar();
            $registrars = $registrar->all(["Name", "Class"]);
            if((int) $registrars["CountRows"] === 0) {
                $this->Warning[] = __("no registrars in software");
            }
            require_once "class/topleveldomain.php";
            $tld = new topleveldomain();
            $tlds = $tld->all(["Tld", "Registrar"], false, false, "-1");
            if((int) $tlds["CountRows"] === 0) {
                $this->Warning[] = __("no tlds in software");
            } else {
                foreach ($tlds as $k => $v) {
                    if(is_numeric($k) && empty($v["Registrar"])) {
                        $this->Warning[] = sprintf(__("no registrar for tld"), $v["Tld"]);
                    }
                }
            }
        }
        if($this->makeaccount_value == "1") {
            require_once "class/package.php";
            $package = new package();
            $packages = $package->all(["PackageName"]);
            if((int) $packages["CountRows"] === 0) {
                $this->Warning[] = __("no hostingpackages in software");
            }
        }
        if($this->makebackup_value == "1") {
            if(!is_dir(BACKUP_DIR)) {
                $this->Warning[] = sprintf(__("no backup folder"), BACKUP_DIR);
            } elseif(!is_writable(BACKUP_DIR)) {
                $this->Warning[] = sprintf(__("backup folder not writable"), BACKUP_DIR);
            }
        }
        if($this->remindersummation_value == "1" && !(0 < INVOICE_REMINDER_TERM && is_numeric(INVOICE_REMINDER_TERM) && 0 < INVOICE_SUMMATION_TERM && is_numeric(INVOICE_SUMMATION_TERM) && 0 < INVOICE_REMINDER_NUMBER && is_numeric(INVOICE_REMINDER_NUMBER) && 0 < INVOICE_SUMMATION_NUMBER && is_numeric(INVOICE_SUMMATION_NUMBER))) {
            $this->Warning[] = __("reminder days not set");
        }
        return true;
    }
    public function enableCheckTicket()
    {
        Database_Model::getInstance()->update("HostFact_Automation", ["Value" => "1", "Run" => "both", "Exception" => ""])->where("Variable", "checkticket")->where("Value", "0")->execute();
    }
}

?>