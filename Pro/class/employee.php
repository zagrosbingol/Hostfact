<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class employee
{
    public $Identifier;
    public $Name;
    public $Function;
    public $EmailAddress;
    public $PhoneNumber;
    public $MobileNumber;
    public $UserName;
    public $Password;
    public $PasswordAgain;
    public $Signature;
    public $Language;
    public $LastDate;
    public $Notes;
    public $Status;
    public $Preferences;
    public $Error = [];
    public $Warning = [];
    public $Success = [];
    public $Variables;
    public function __construct()
    {
        $this->Status = "1";
        $this->admin = false;
        $this->Preferences = [];
        $this->Language = IS_INTERNATIONAL == "true" ? "en_EN" : "nl_NL";
        $this->Variables = ["Identifier", "Name", "Function", "EmailAddress", "PhoneNumber", "MobileNumber", "UserName", "Password", "TokenData", "TwoFactorAuthentication", "PasswordAgain", "Signature", "Language", "LastDate", "Notes", "Status"];
    }
    public function show($id = NULL)
    {
        $this->Identifier = 0 < $id ? $id : $this->Identifier;
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for employee");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_Employee")->where("id", $this->Identifier)->execute();
        if(!isset($result->id) || empty($result->id)) {
            $this->Error[] = __("invalid identifier for employee");
            return false;
        }
        foreach ($result as $key => $value) {
            $this->{$key} = $key != "Signature" ? htmlspecialchars($value) : $value;
        }
        return true;
    }
    public function search($password_type = "unencrypted")
    {
        if(!is_string($this->UserName)) {
            $this->Error[] = __("invalid username employee");
            return false;
        }
        if(!is_string($this->Password)) {
            $this->Error[] = __("invalid password employee");
            return false;
        }
        $user_record = Database_Model::getInstance()->getOne("HostFact_Employee")->where("UserName", $this->UserName)->where("Status", ["!=" => 9])->execute();
        if(!$user_record) {
            return false;
        }
        if($password_type == "unencrypted" && strpos($user_record->Password, "\$") === false) {
            $password_type = "md5";
        }
        if($password_type == "unencrypted" && wf_password_verify($this->Password, $user_record->Password) || $password_type == "already_encrypted" && wf_password_verify($user_record->Password . $_SERVER["REMOTE_ADDR"] . "-" . $_COOKIE["CookieExpiration"], $this->Password) || $password_type == "md5" && md5($this->Password) == $user_record->Password) {
            if($password_type == "md5") {
                $user_record->Password = wf_password_hash($this->Password);
                Database_Model::getInstance()->update("HostFact_Employee", ["Password" => $user_record->Password])->where("id", $user_record->id)->execute();
            }
            foreach ($user_record as $key => $value) {
                $this->{$key} = $key != "Signature" ? htmlspecialchars($value) : $value;
            }
            $this->Identifier = $user_record->id;
            return true;
        } else {
            return false;
        }
    }
    public function username_exists($username)
    {
        $result = Database_Model::getInstance()->getOne("HostFact_Employee")->where("UserName", $username)->where("Status", ["!=" => 9])->execute();
        if(!isset($result->id) || empty($result->id)) {
            return 0;
        }
        return $result->id;
    }
    public function add()
    {
        if(!$this->validate()) {
            return false;
        }
        $result = Database_Model::getInstance()->insert("HostFact_Employee", ["Name" => $this->Name, "Function" => $this->Function, "EmailAddress" => $this->EmailAddress, "PhoneNumber" => $this->PhoneNumber, "MobileNumber" => $this->MobileNumber, "UserName" => $this->UserName, "Password" => $this->Password, "Signature" => $this->Signature, "Language" => $this->Language, "Status" => $this->Status])->execute();
        if($result) {
            $this->Identifier = $result;
            $this->addRights();
            $this->addPreferences();
            $this->Success[] = sprintf(__("employee created"), $this->Name);
            Database_Model::getInstance()->insert("HostFact_EmployeeWidgets", ["Employee" => $this->Identifier, "Widget" => 1, "Option1" => "year", "Position" => 1])->execute();
            Database_Model::getInstance()->insert("HostFact_EmployeeWidgets", ["Employee" => $this->Identifier, "Widget" => 2, "Option1" => "quarter", "Position" => 2])->execute();
            Database_Model::getInstance()->insert("HostFact_EmployeeWidgets", ["Employee" => $this->Identifier, "Widget" => 4, "Option1" => "year", "Position" => 3])->execute();
            Database_Model::getInstance()->insert("HostFact_EmployeeWidgets", ["Employee" => $this->Identifier, "Widget" => 3, "Option1" => "last6m", "Position" => 0])->execute();
            Database_Model::getInstance()->insert("HostFact_EmployeeWidgets", ["Employee" => $this->Identifier, "Widget" => 5, "Option1" => "month", "Position" => 4])->execute();
            return true;
        }
        return false;
    }
    public function edit($id = NULL)
    {
        $this->Identifier = 0 < $id ? $id : $this->Identifier;
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for employee");
            return false;
        }
        if(!$this->validate()) {
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Employee", ["Name" => $this->Name, "Function" => $this->Function, "EmailAddress" => $this->EmailAddress, "PhoneNumber" => $this->PhoneNumber, "MobileNumber" => $this->MobileNumber, "UserName" => $this->UserName, "Password" => $this->Password, "Signature" => $this->Signature, "Language" => $this->Language, "Notes" => $this->Notes])->where("id", $this->Identifier)->execute();
        if($result) {
            global $account;
            if($account->Identifier == $this->Identifier) {
                $_SESSION["language"] = $this->Language;
            }
            $this->editRights();
            $this->Success[] = sprintf(__("employee adjusted"), $this->Name);
            return true;
        }
        return false;
    }
    public function updateLastDate($id, $lastdate)
    {
        $this->Identifier = 0 < $id ? $id : $this->Identifier;
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for employee");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Employee", ["LastDate" => $lastdate])->where("id", $this->Identifier)->execute();
        if($result) {
            return true;
        }
        return false;
    }
    public function toggleTicketOrder()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for employee");
            return false;
        }
        $neworder = $this->TicketOrder == "DESC" ? "ASC" : "DESC";
        $result = Database_Model::getInstance()->update("HostFact_Employee", ["TicketOrder" => $neworder])->where("id", $this->Identifier)->execute();
        if($result) {
            $this->TicketOrder = $neworder;
            return true;
        }
        return false;
    }
    public function updateNotes($id = NULL)
    {
        $this->Identifier = 0 < $id ? $id : $this->Identifier;
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for employee");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Employee", ["Notes" => $this->Notes])->where("id", $this->Identifier)->execute();
        if($result) {
            $this->Success[] = __("employee notes adjusted");
            return true;
        }
        return false;
    }
    public function delete($id)
    {
        $this->Identifier = 0 < $id ? $id : $this->Identifier;
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for employee");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Employee", ["Status" => 9])->where("id", $this->Identifier)->execute();
        if($result) {
            $this->Success[] = __("employee deleted");
            return true;
        }
        return false;
    }
    public function showRights($id, $define = false)
    {
        if(!is_numeric($id)) {
            $this->Error[] = __("invalid identifier for employee");
            return false;
        }
        $result = Database_Model::getInstance()->get("HostFact_EmployeeRights")->where("Employee", $id)->execute();
        foreach ($result as $right) {
            if($define && !defined("U_" . $right->Right)) {
                if($right->Value == "1") {
                    define("U_" . $right->Right, true);
                } else {
                    define("U_" . $right->Right, false);
                }
            }
            $v = "U_" . $right->Right;
            $this->{$v} = $right->Value;
        }
        return NULL;
    }
    public function showPreferences($id, $define = false)
    {
        if(!is_numeric($id)) {
            $this->Error[] = __("invalid identifier for employee");
            return false;
        }
        $result = Database_Model::getInstance()->get("HostFact_EmployeePreferences")->where("Employee", $id)->orderBy("Order", "ASC")->execute();
        if($result && empty($result)) {
            $this->Identifier = $id;
            $this->addPreferences();
        }
        if($result) {
            foreach ($result as $pref) {
                if((int) $pref->Order === 0) {
                    $this->Identifier = $id;
                    $result = Database_Model::getInstance()->delete("HostFact_EmployeePreferences")->where("Employee", $this->Identifier)->execute();
                    $this->addPreferences();
                } else {
                    if($define) {
                        define("P_" . $pref->Page . "_" . $pref->Action . "_Value", $pref->Value);
                        define("P_" . $pref->Page . "_" . $pref->Action . "_Order", $pref->Order);
                    }
                    $this->Preferences[$pref->Page][$pref->Action] = ["Value" => $pref->Value, "Order" => $pref->Order, "Action" => $pref->Action];
                }
            }
        }
    }
    public function addPreferences()
    {
        $prefs = [];
        $prefs["home"] = [];
        $prefs["home"]["invoice_waiting"] = ["show", "1"];
        $prefs["home"]["invoice_open"] = ["show", "2"];
        $prefs["home"]["invoice_waiting_c"] = ["show", "3"];
        $prefs["home"]["creditinvoice"] = ["show", "4"];
        foreach ($prefs as $page => $array) {
            if(is_array($array)) {
                foreach ($array as $action => $value) {
                    $result = Database_Model::getInstance()->insert("HostFact_EmployeePreferences", ["Employee" => $this->Identifier, "Page" => $page, "Action" => $action, "Value" => $value[0], "Order" => $value[1]])->execute();
                }
            }
        }
    }
    public function editPreferences($array)
    {
        if(is_array($array)) {
            foreach ($array as $page => $array2) {
                foreach ($array2 as $action => $value) {
                    Database_Model::getInstance()->update("HostFact_EmployeePreferences", ["Value" => $value["Value"]])->where("Employee", $this->Identifier)->where("Page", $page)->where("Action", $action)->execute();
                }
            }
        } elseif($array = "hide") {
            Database_Model::getInstance()->update("HostFact_EmployeePreferences", ["Value" => "hidden"])->where("Employee", $this->Identifier)->where("Page", "home")->execute();
        }
    }
    public function addRights()
    {
        $value = $this->admin ? 1 : 0;
        $rights = ["DEBTOR", "CREDITOR", "CREDITOR_INVOICE", "DOMAIN", "HOSTING", "SERVICE", "INVOICE", "PRICEQUOTE", "ORDER", "TICKET", "STATISTICS", "AGENDA", "COMPANY", "PRODUCT", "LAYOUT", "SERVICEMANAGEMENT", "MODIFICATIONS", "LOGFILE", "EXPORT", "SETTINGS", "CUSTOMERPANEL", "ORDERFORM", "PAYMENT", "SERVICESETTING"];
        $service_rights = do_filter("employee_service_rights", []);
        $rights = array_merge($rights, array_keys($service_rights));
        foreach ($rights as $k) {
            Database_Model::getInstance()->rawQuery("REPLACE INTO `HostFact_EmployeeRights` (Employee,`Right`,Value) VALUES (:employee, :right, :value)", ["employee" => $this->Identifier, "right" => $k . "_SHOW", "value" => $value]);
            if(!in_array($k, ["STATISTICS", "LOGFILE"])) {
                Database_Model::getInstance()->rawQuery("REPLACE INTO `HostFact_EmployeeRights` (Employee,`Right`,Value) VALUES (:employee, :right, :value)", ["employee" => $this->Identifier, "right" => $k . "_ADD", "value" => $value]);
                Database_Model::getInstance()->rawQuery("REPLACE INTO `HostFact_EmployeeRights` (Employee,`Right`,Value) VALUES (:employee, :right, :value)", ["employee" => $this->Identifier, "right" => $k . "_EDIT", "value" => $value]);
            }
            if(!in_array($k, ["STATISTICS"])) {
                Database_Model::getInstance()->rawQuery("REPLACE INTO `HostFact_EmployeeRights` (Employee,`Right`,Value) VALUES (:employee, :right, :value)", ["employee" => $this->Identifier, "right" => $k . "_DELETE", "value" => $value]);
            }
        }
    }
    public function editRights($array = false)
    {
        if(is_array($array)) {
            Database_Model::getInstance()->update("HostFact_EmployeeRights", ["Value" => "0"])->where("Employee", $this->Identifier)->execute();
            $value_on = [];
            foreach ($array as $k => $v) {
                if($v == "1") {
                    $value_on[] = $k;
                    if(strpos($k, "EDIT") !== false) {
                        $value_on[] = str_replace("_EDIT", "_ADD", $k);
                    }
                }
            }
            if(!empty($value_on)) {
                Database_Model::getInstance()->update("HostFact_EmployeeRights", ["Value" => "1"])->where("Employee", $this->Identifier)->where("Right", ["IN" => $value_on])->execute();
            }
            Database_Model::getInstance()->update("HostFact_EmployeeRights", ["Value" => "0"])->where("Employee", $this->Identifier)->where("Right", ["NOT IN" => $value_on])->execute();
        }
    }
    public function reorderPreferences($employee, $order)
    {
        parse_str($order, $order);
        $ordering = 1;
        foreach ($order["order"] as $item) {
            Database_Model::getInstance()->update("HostFact_EmployeePreferences", ["Order" => $ordering])->where("Employee", $employee)->where("Action", str_replace(".", "_", $item))->execute();
            $ordering++;
        }
        exit;
    }
    public function validate()
    {
        if(!is_string($this->Name) || 255 < strlen($this->Name) || strlen($this->Name) === 0) {
            $this->Error[] = __("invalid name for employee");
        }
        if(0 < strlen($this->EmailAddress) && !check_email_address($this->EmailAddress, "single")) {
            $this->Error[] = __("invalid emailaddress");
        }
        if(!is_string($this->UserName) || 50 < strlen($this->UserName) || strlen($this->Name) === 0) {
            $this->Error[] = __("invalid username for employee");
        }
        if(0 < $this->username_exists($this->UserName) && $this->Identifier != $this->username_exists($this->UserName)) {
            $this->Error[] = __("username in use");
        }
        global $array_backoffice_languages;
        if($this->Language && !in_array($this->Language, array_keys($array_backoffice_languages))) {
            $this->Error[] = __("invalid backoffice language");
        }
        return empty($this->Error) ? true : false;
    }
    public function all($fields, $sort = "Name", $order = "ASC")
    {
        if(!is_array($fields) || empty($fields)) {
            $this->Error[] = __("no fields given for retrieving data");
            return false;
        }
        Database_Model::getInstance()->get("HostFact_Employee", array_merge(["id"], $fields))->where("Status", ["<" => "9"]);
        if($sort) {
            Database_Model::getInstance()->orderBy($sort, $order ? $order : "ASC");
        }
        $list = [];
        $list["CountRows"] = 0;
        if($result = Database_Model::getInstance()->execute()) {
            $list["CountRows"] = $this->CountRows = Database_Model::getInstance()->rowCount("HostFact_Employee", "id");
            foreach ($result as $_employee) {
                $list[$_employee->id] = ["id" => $_employee->id];
                foreach ($fields as $column) {
                    if(in_array($column, $this->Variables)) {
                        $list[$_employee->id][$column] = htmlspecialchars($_employee->{$column});
                    }
                }
            }
        }
        return $list;
    }
    public function updateTokenData($token, $id = NULL)
    {
        $this->Identifier = 0 < $id ? $id : $this->Identifier;
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for employee");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Employee", ["TokenData" => $token])->where("id", $this->Identifier)->execute();
        if(!$result) {
            return false;
        }
        return true;
    }
    public function editAuthentication($action)
    {
        if($action == "activate") {
            $result = Database_Model::getInstance()->update("HostFact_Employee", ["TwoFactorAuthentication" => "on"])->where("id", $this->Identifier)->execute();
            if($result) {
                $this->Success[] = __("two factor authentication activated");
            } else {
                $this->Error[] = __("two factor authentication could not be activated");
            }
        } elseif($action == "deactivate") {
            $result = Database_Model::getInstance()->update("HostFact_Employee", ["TwoFactorAuthentication" => "off", "TokenData" => ""])->where("id", $this->Identifier)->execute();
            if($result) {
                $this->Success[] = __("two factor authentication deactivated");
            } else {
                $this->Error[] = __("two factor authentication could not be deactivated");
            }
        }
        return $result;
    }
    public function updatePassword($password)
    {
        $result = Database_Model::getInstance()->update("HostFact_Employee", ["Password" => wf_password_hash($password)])->where("id", $this->Identifier)->execute();
        return $result;
    }
    public function checkUserRights($service_type, $right_type = "show")
    {
        if(!isset($this->Identifier) || !$this->Identifier) {
            return true;
        }
        $right_type = strtoupper($right_type);
        $service_type_upper = strtoupper($service_type);
        switch ($service_type) {
            case "domain":
            case "hosting":
                return constant("U_" . $service_type_upper . "_" . $right_type);
                break;
            case "other":
                return constant("U_SERVICE_" . $right_type);
                break;
            default:
                $service_rights = do_filter("employee_service_rights", []);
                if(isset($service_rights[$service_type_upper]) && defined("U_" . $service_type_upper . "_" . $right_type)) {
                    return constant("U_" . $service_type_upper . "_" . $right_type);
                }
                return constant("U_SERVICE_" . $right_type);
        }
    }
}

?>