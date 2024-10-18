<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class agenda
{
    public $Identifier;
    public $Description;
    public $Date;
    public $TimeFrom;
    public $TimeTill;
    public $WholeDay;
    public $Status;
    public $Employee;
    public $EmailNotify;
    public $ItemType;
    public $ItemID;
    public $s_day;
    public $s_month;
    public $s_year;
    public $e_day;
    public $e_month;
    public $e_year;
    public $CountRows;
    public $Table;
    public $Error;
    public $Warning;
    public $Success;
    public $Variables = ["Identifier", "Description", "Date", "TimeFrom", "TimeTill", "WholeDay", "Status", "Employee", "EmailNotify", "ItemType", "ItemID"];
    public function __construct()
    {
        $this->TimeFrom = "";
        $this->TimeTill = "";
        $this->s_day = date("d");
        $this->s_month = date("m") == "01" ? "12" : str_repeat("0", 2 - strlen(date("n") - 1)) . "" . (date("n") - 1);
        $this->s_year = date("m") == "01" ? date("Y") - 1 : date("Y");
        $this->e_day = date("d");
        $this->e_month = date("m");
        $this->e_year = date("Y");
        $this->NumberOfUnits = 12;
        $this->SelectedPeriod = "y";
        $this->Label = "";
        $this->SearchString = "";
        $this->Employee = isset($_SESSION["UserPro"]) ? $_SESSION["UserPro"] : 0;
        $this->Status = 1;
        $this->Error = [];
        $this->Warning = [];
        $this->Success = [];
    }
    public function show()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for agenda item");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_Agenda", ["HostFact_Agenda.*", "HostFact_Employee.Name as EmployeeName", "HostFact_Employee.EmailAddress as EmployeeMailAddress"])->join("HostFact_Employee", "HostFact_Agenda.Employee = HostFact_Employee.id")->where("HostFact_Agenda.id", $this->Identifier)->execute();
        if(!$result || empty($result->id)) {
            $this->Error[] = __("invalid identifier for agenda item");
            return false;
        }
        foreach ($result as $key => $value) {
            $this->{$key} = htmlspecialchars($value);
        }
        if(!$this->EmployeeMailAddress && 0 <= $this->EmailNotify) {
            global $company;
            $this->EmployeeMailAddress = $company->EmailAddress;
        }
        $this->Date = rewrite_date_db2site($this->Date);
        $this->TimeFrom = substr($this->TimeFrom, 0, 5);
        $this->TimeTill = substr($this->TimeTill, 0, 5);
        return true;
    }
    public function add()
    {
        if(!$this->validate()) {
            return false;
        }
        $result = Database_Model::getInstance()->insert("HostFact_Agenda", ["Description" => $this->Description, "Date" => $this->Date, "TimeFrom" => $this->TimeFrom, "TimeTill" => $this->TimeTill, "WholeDay" => $this->WholeDay, "Status" => $this->Status, "Employee" => $this->Employee, "EmailNotify" => $this->EmailNotify, "ItemType" => $this->ItemType, "ItemID" => $this->ItemID])->execute();
        if($result !== false) {
            $this->Identifier = $result;
            $this->Success[] = sprintf(__("agenda item created"), rewrite_date_db2site($this->Date));
            return true;
        }
        return false;
    }
    public function edit()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for agenda item");
            return false;
        }
        if(!$this->validate()) {
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Agenda", ["Description" => $this->Description, "Date" => $this->Date, "TimeFrom" => $this->TimeFrom, "TimeTill" => $this->TimeTill, "WholeDay" => $this->WholeDay, "Status" => $this->Status, "Employee" => $this->Employee, "EmailNotify" => $this->EmailNotify, "ItemType" => $this->ItemType, "ItemID" => $this->ItemID])->where("id", $this->Identifier)->execute();
        if($result) {
            $this->Success[] = sprintf(__("agenda item adjusted"), rewrite_date_db2site($this->Date));
            return true;
        }
        return false;
    }
    public function delete()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for agenda item");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Agenda", ["Status" => "9"])->where("id", $this->Identifier)->execute();
        if($result) {
            $this->Success[] = __("agenda item deleted");
            return true;
        }
        return false;
    }
    public function validate()
    {
        $date = str_replace("-", "", $this->Date);
        if(!checkdate(substr($date, 4, 2), substr($date, 6, 2), substr($date, 0, 4))) {
            $this->Error[] = __("invalid date agenda item");
        }
        if(!(is_string($this->Description) && 3 <= strlen($this->Description))) {
            $this->Error[] = __("invalid description agenda item");
        }
        if(intval($this->EmailNotify) < -2 || 127 < intval($this->EmailNotify)) {
            $this->Warning[] = __("invalid emailnotify agendaitem");
            $this->EmailNotify = -1;
        }
        return !empty($this->Error) ? false : true;
    }
    public function all($fields, $start = false, $end = false, $description = "")
    {
        $list = ["CountRows" => 0];
        Database_Model::getInstance()->get("HostFact_Agenda", ["HostFact_Agenda.id", "DATE_FORMAT(`Date`, '%Y%m%d') as Date", "Description", "TimeFrom", "TimeTill", "WholeDay", "HostFact_Agenda.Status", "Employee", "EmailNotify", "ItemType", "ItemID", "HostFact_Employee.UserName as EmployeeName", "HostFact_Employee.EmailAddress as EmployeeMailAddress"])->join("HostFact_Employee", "HostFact_Employee.id = HostFact_Agenda.Employee")->where("HostFact_Agenda.Status", ["!=" => 9]);
        if($start && $end) {
            Database_Model::getInstance()->where("Date", [">=" => $start])->where("Date", ["<=" => $end])->orderBy("Date", "ASC")->orderBy("TimeFrom", "ASC");
        } elseif($description) {
            Database_Model::getInstance()->where("Description", ["LIKE" => "%" . $description . "%"])->orderBy("Date", "DESC")->orderBy("TimeFrom", "ASC");
        }
        if($result = Database_Model::getInstance()->asArray()->execute()) {
            $list["CountRows"] = Database_Model::getInstance()->rowCount("HostFact_Agenda", "HostFact_Agenda.id");
            foreach ($result as $_agenda_item) {
                $_agenda_item["Date"] = date("Ymd", strtotime($_agenda_item["Date"]));
                $_agenda_item["Description"] = str_replace(["<wf_desc>", "</wf_desc>", "<wf_date>", "</wf_date>"], "", $_agenda_item["Description"]);
                $list[$_agenda_item["Date"]][$_agenda_item["id"]] = $_agenda_item;
            }
        }
        $or_clausule = [["AND" => [["HostFact_Domains.ExpirationDate", [">=" => $start]], ["HostFact_Domains.ExpirationDate", ["<=" => $end]]]], ["AND" => [["DATE_SUB(HostFact_Domains.`ExpirationDate`, INTERVAL :domain_warning DAY)", [">=" => $start]], ["DATE_SUB(HostFact_Domains.`ExpirationDate`, INTERVAL :domain_warning DAY)", ["<=" => $end]]]]];
        $result = Database_Model::getInstance()->get("HostFact_Domains", ["HostFact_Domains.id", "HostFact_Domains.Domain", "HostFact_Domains.Tld", "HostFact_Domains.ExpirationDate", "HostFact_Domains.Registrar", "DATE_SUB(HostFact_Domains.`ExpirationDate`, INTERVAL :domain_warning DAY) as WarnDate"])->join("HostFact_Registrar", "HostFact_Registrar.`id`=HostFact_Domains.`Registrar`")->where("HostFact_Domains.Status", 4)->orWhere([["HostFact_Domains.`DomainAutoRenew`", "off"], ["HostFact_Domains.`DomainAutoRenew`", ""], ["AND" => [["HostFact_Domains.`DomainAutoRenew`", "on"], ["OR" => [["HostFact_Domains.`IsSynced`", ["!=" => "yes"]], ["HostFact_Registrar.`Class`", ""]]]]]])->bindValue("domain_warning", DOMAINWARNING)->orWhere($or_clausule)->execute();
        if($result) {
            foreach ($result as $_agenda_domain_warning) {
                if($start <= $_agenda_domain_warning->ExpirationDate && $_agenda_domain_warning->ExpirationDate <= $end) {
                    $list[str_replace("-", "", $_agenda_domain_warning->ExpirationDate)]["D" . $_agenda_domain_warning->id] = ["ItemType" => "domain", "ItemID" => $_agenda_domain_warning->id, "WholeDay" => 1, "Description" => sprintf(__("agenda domain expired"), $_agenda_domain_warning->Domain . "." . $_agenda_domain_warning->Tld)];
                }
                if($start <= $_agenda_domain_warning->WarnDate && $_agenda_domain_warning->WarnDate <= $end) {
                    $list[str_replace("-", "", $_agenda_domain_warning->WarnDate)]["D" . $_agenda_domain_warning->id] = ["ItemType" => "domain", "ItemID" => $_agenda_domain_warning->id, "WholeDay" => 1, "Description" => sprintf(__("agenda domain almost expired"), $_agenda_domain_warning->Domain . "." . $_agenda_domain_warning->Tld, DOMAINWARNING)];
                }
            }
        }
        global $array_months;
        $unit = substr($this->SelectedPeriod, 0, 1);
        switch ($unit) {
            case "q":
            case "m":
                $timestamp = mktime(0, 0, 0, $this->s_month, 1, $this->s_year);
                $this->Label = $array_months[date("m", $timestamp)] . " " . $this->s_year;
                break;
            case "w":
                $timestamp = mktime(0, 0, 0, $this->s_month, $this->s_day, $this->s_year);
                $this->Label = __("week") . " " . date("W", $timestamp) . " - " . ($this->s_month == 12 ? $this->e_year : $this->s_year);
                break;
            default:
                $this->Label = __("quarter") . " " . $this->e_month / 3 . " - " . $this->s_year;
                return !empty($this->Error) ? $this->Error : $list;
        }
    }
    public function setStartDate($db_date = "")
    {
        $tmp = explode("-", $db_date);
        $this->s_day = isset($tmp[2]) ? $tmp[2] : "01";
        $this->s_month = isset($tmp[1]) ? $tmp[1] : "01";
        $this->s_year = $tmp[0];
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
    public function sent()
    {
        global $company;
        if(0 < $this->Employee) {
            require_once "class/employee.php";
            $employee = new employee();
            $employee->Identifier = $this->Employee;
            $employee->show();
            if(!empty($employee->EmailAddress) && $employee->Status != 9) {
                $receiver = $employee->EmailAddress;
            }
        }
        if(!isset($receiver)) {
            $receiver = $company->EmailAddress;
        }
        require_once "class/email.php";
        $email = new email();
        $email->Recipient = $receiver;
        $email->Sender = $company->CompanyName . " <" . $company->EmailAddress . ">";
        $email->Subject = sprintf(__("email subject agenda notification"), $this->Date);
        $email->Message = file_get_contents("includes/language/" . LANGUAGE_CODE . "/mail.agenda.notification.html");
        $email->Message = str_replace("[date]", $this->Date, $email->Message);
        $email->Message = str_replace("[description]", nl2br($this->Description), $email->Message);
        $email->Message = str_replace("[time]", $this->WholeDay ? __("whole day") : $this->TimeFrom . " " . __("till") . " " . $this->TimeTill, $email->Message);
        $email->Message = str_replace("[employee]", isset($employee->Name) ? $employee->Name : "", $email->Message);
        if($email->sent()) {
            Database_Model::getInstance()->update("HostFact_Agenda", ["EmailNotify" => "-2"])->where("id", $this->Identifier)->execute();
            createMessageLog("success", "agenda notification sent to", $receiver, "agenda", $this->Identifier);
            $this->Success[] = sprintf(__("agenda notification sent to"), $receiver);
            return true;
        }
        createMessageLog("error", "agenda notification not sent", isset($email->MailerError) ? $email->MailerError : "", "agenda", $this->Identifier);
        $this->Error[] = sprintf(__("agenda notification not sent"), isset($email->MailerError) ? $email->MailerError : "");
        return false;
    }
}

?>