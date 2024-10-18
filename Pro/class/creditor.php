<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class creditor
{
    public $Identifier;
    public $CreditorCode;
    public $MyCustomerCode;
    public $Status;
    public $CompanyName;
    public $CompanyNumber;
    public $TaxNumber;
    public $Sex;
    public $Initials;
    public $SurName;
    public $Address;
    public $Address2;
    public $ZipCode;
    public $City;
    public $State;
    public $Country;
    public $BirthDate;
    public $EmailAddress;
    public $PhoneNumber;
    public $MobileNumber;
    public $FaxNumber;
    public $Comment;
    public $Term;
    public $Authorisation;
    public $AccountNumber;
    public $AccountBIC;
    public $AccountName;
    public $AccountBank;
    public $AccountCity;
    public $Groups;
    public $Attachment;
    public $CountRows;
    public $Error;
    public $Success;
    public $Warning;
    public $Variables = ["Identifier", "CreditorCode", "MyCustomerCode", "Status", "CompanyName", "CompanyNumber", "TaxNumber", "Sex", "Initials", "SurName", "Address", "Address2", "ZipCode", "City", "State", "Country", "BirthDate", "EmailAddress", "PhoneNumber", "MobileNumber", "FaxNumber", "Comment", "Term", "Authorisation", "AccountNumber", "AccountBIC", "AccountName", "AccountBank", "AccountCity", "Free1", "Free2", "Free3", "Free4", "Free5"];
    public function __construct()
    {
        global $company;
        $this->Country = $company->Country ? $company->Country : "NL";
        $this->StateName = "";
        $this->Status = "0";
        $this->Groups = [];
        $this->Term = 0;
        $this->Authorisation = "no";
        $this->Attachment = [];
        $this->Error = [];
        $this->Success = [];
        $this->Warning = [];
    }
    public function show($id = NULL)
    {
        $this->Identifier = 0 < $id ? $id : $this->Identifier;
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for creditor");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_Creditors")->where("id", $this->Identifier)->execute();
        if(!$result || empty($result->id)) {
            $this->Error[] = __("invalid identifier for creditor");
            return false;
        }
        foreach ($result as $key => $value) {
            $this->{$key} = htmlspecialchars($value);
        }
        global $array_country;
        global $array_states;
        $this->CountryLong = $array_country[$this->Country];
        $this->BirthDate = rewrite_date_db2site($this->BirthDate);
        $this->StateName = isset($array_states[$this->Country][$this->State]) ? $array_states[$this->Country][$this->State] : $this->State;
        $this->Groups = [];
        $group_list = Database_Model::getInstance()->get(["HostFact_Group", "HostFact_GroupRelations"], ["HostFact_Group.id", "HostFact_Group.GroupName", "HostFact_Group.Status"])->where("HostFact_GroupRelations.Reference", $this->Identifier)->where("HostFact_GroupRelations.Type", "creditor")->where("HostFact_GroupRelations.Group = HostFact_Group.id")->where("HostFact_Group.Type", "creditor")->where("HostFact_Group.Status", ["!=" => 9])->execute();
        if($group_list && is_array($group_list)) {
            foreach ($group_list as $result) {
                $this->Groups[$result->id] = ["id" => $result->id, "GroupName" => htmlspecialchars($result->GroupName)];
            }
        }
        $amount_by_status = Database_Model::getInstance()->get("HostFact_CreditInvoice", ["Status", "SUM(`AmountIncl`) as Amount"])->where("Creditor", $this->Identifier)->groupBy("Status")->execute();
        if($amount_by_status && is_array($amount_by_status)) {
            foreach ($amount_by_status as $result) {
                $a = $result->Status == "3" ? "Paid" : ($result->Status == "8" || $result->Status == "9" ? "Paid" : "Unpaid");
                $this->{"Amount" . $a} = isset($this->{"Amount" . $a}) ? $this->{"Amount" . $a} + $result->Amount : $result->Amount;
            }
        }
        return true;
    }
    public function add()
    {
        $this->BirthDate = rewrite_date_site2db($this->BirthDate);
        if(!$this->validate()) {
            return false;
        }
        $result = Database_Model::getInstance()->insert("HostFact_Creditors", ["CreditorCode" => $this->CreditorCode, "MyCustomerCode" => $this->MyCustomerCode, "Status" => $this->Status, "CompanyName" => $this->CompanyName, "CompanyNumber" => $this->CompanyNumber, "TaxNumber" => $this->TaxNumber, "Sex" => $this->Sex, "Initials" => $this->Initials, "SurName" => $this->SurName, "Address" => $this->Address, "Address2" => $this->Address2, "ZipCode" => $this->ZipCode, "City" => $this->City, "State" => $this->State, "Country" => $this->Country, "BirthDate" => $this->BirthDate, "EmailAddress" => check_email_address($this->EmailAddress, "convert"), "PhoneNumber" => $this->PhoneNumber, "MobileNumber" => $this->MobileNumber, "FaxNumber" => $this->FaxNumber, "Comment" => $this->Comment, "Term" => $this->Term, "Authorisation" => $this->Authorisation, "AccountNumber" => $this->AccountNumber, "AccountBIC" => $this->AccountBIC, "AccountName" => $this->AccountName, "AccountBank" => $this->AccountBank, "AccountCity" => $this->AccountCity])->execute();
        if($result) {
            $this->Identifier = $result;
            foreach ($this->Groups as $key => $value) {
                $input = is_numeric($value) ? $value : $key;
                Database_Model::getInstance()->insert("HostFact_GroupRelations", ["Group" => $input, "Type" => "creditor", "Reference" => $this->Identifier])->execute();
            }
            $this->Success[] = sprintf(__("creditor added"), $this->CreditorCode);
            return true;
        } else {
            return false;
        }
    }
    public function edit()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for creditor");
            return false;
        }
        $this->BirthDate = rewrite_date_site2db($this->BirthDate);
        if(!$this->validate()) {
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Creditors", ["CreditorCode" => $this->CreditorCode, "MyCustomerCode" => $this->MyCustomerCode, "Status" => $this->Status, "CompanyName" => $this->CompanyName, "CompanyNumber" => $this->CompanyNumber, "TaxNumber" => $this->TaxNumber, "Sex" => $this->Sex, "Initials" => $this->Initials, "SurName" => $this->SurName, "Address" => $this->Address, "Address2" => $this->Address2, "ZipCode" => $this->ZipCode, "City" => $this->City, "State" => $this->State, "Country" => $this->Country, "BirthDate" => $this->BirthDate, "EmailAddress" => check_email_address($this->EmailAddress, "convert"), "PhoneNumber" => $this->PhoneNumber, "MobileNumber" => $this->MobileNumber, "FaxNumber" => $this->FaxNumber, "Comment" => $this->Comment, "Term" => $this->Term, "Authorisation" => $this->Authorisation, "AccountNumber" => $this->AccountNumber, "AccountBIC" => $this->AccountBIC, "AccountName" => $this->AccountName, "AccountBank" => $this->AccountBank, "AccountCity" => $this->AccountCity])->where("id", $this->Identifier)->execute();
        if($result) {
            Database_Model::getInstance()->delete("HostFact_GroupRelations")->where("Type", "creditor")->where("Reference", $this->Identifier)->execute();
            foreach ($this->Groups as $key => $value) {
                $input = is_numeric($value) ? $value : $key;
                Database_Model::getInstance()->insert("HostFact_GroupRelations", ["Group" => $input, "Type" => "creditor", "Reference" => $this->Identifier])->execute();
            }
            $this->Success[] = sprintf(__("creditor updated"), $this->CreditorCode);
            return true;
        } else {
            return false;
        }
    }
    public function delete()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for creditor");
            return false;
        }
        Database_Model::getInstance()->delete("HostFact_GroupRelations")->where("Type", "creditor")->where("Reference", $this->Identifier)->execute();
        $result = Database_Model::getInstance()->update("HostFact_Creditors", ["Status" => 9])->where("id", $this->Identifier)->execute();
        if($result) {
            $this->show();
            $this->Success[] = sprintf(__("creditor deleted"), $this->CreditorCode);
            return true;
        }
        return false;
    }
    public function newCreditorCode($prefix = CREDITORCODE_PREFIX, $number = CREDITORCODE_NUMBER)
    {
        $prefix = parsePrefixVariables($prefix);
        $length = strlen($prefix . $number);
        $result = Database_Model::getInstance()->getOne("HostFact_Creditors", ["CreditorCode"])->where("CreditorCode", ["LIKE" => $prefix . "%"])->where("LENGTH(`CreditorCode`)", [">=" => $length])->where("(SUBSTR(`CreditorCode`,:PrefixLength)*1)", [">" => 0])->where("SUBSTR(`CreditorCode`,:PrefixLength)", ["REGEXP" => "^[0-9]+\$"])->orderBy("(SUBSTR(`CreditorCode`,:PrefixLength)*1)", "DESC")->bindValue("PrefixLength", strlen($prefix) + 1)->execute();
        if(isset($result->CreditorCode) && $result->CreditorCode && is_numeric(substr($result->CreditorCode, strlen($prefix)))) {
            $CreditorCode = substr($result->CreditorCode, strlen($prefix));
            $CreditorCode = $prefix . @str_repeat("0", @max(@strlen($number) - @strlen($CreditorCode + 1), 0)) . ($CreditorCode + 1);
        } else {
            $CreditorCode = $prefix . $number;
        }
        if(!$this->is_free($CreditorCode)) {
            $this->Error[] = sprintf(__("Creditorcode could not be generated"), $CreditorCode);
        }
        return !empty($this->Error) ? false : $CreditorCode;
    }
    public function validate()
    {
        if(!$this->is_free($this->CreditorCode)) {
            $this->Error[] = __("creditorcode already in use");
        }
        if(!is_numeric($this->Status)) {
            $this->Error[] = __("invalid creditor status");
        }
        if($this->Sex && !in_array($this->Sex, settings::GENDER_AVAILABLE_OPTIONS)) {
            $this->Error[] = __("invalid sex");
        } elseif(!$this->Sex) {
            $this->Sex = "m";
        }
        if(strlen($this->CompanyName) === 0 && strlen($this->SurName) === 0) {
            $this->Error[] = __("no creditor name specified");
        }
        if(!(is_string($this->MyCustomerCode) && strlen($this->MyCustomerCode) <= 50 || strlen($this->MyCustomerCode) === 0)) {
            $this->Error[] = __("invalid customercode");
        }
        if(!(is_string($this->CompanyName) && strlen($this->CompanyName) <= 100 || strlen($this->CompanyName) === 0)) {
            $this->Error[] = __("invalid companyname");
        }
        if(!(is_string($this->CompanyNumber) && strlen($this->CompanyNumber) <= 20 || strlen($this->CompanyNumber) === 0)) {
            $this->Error[] = __("invalid companynumber");
        }
        if(!(is_string($this->TaxNumber) && strlen($this->TaxNumber) <= 20 || strlen($this->TaxNumber) === 0)) {
            $this->Error[] = __("invalid taxnumber");
        }
        if(!(is_string($this->Initials) && strlen($this->Initials) <= 25 || strlen($this->Initials) === 0)) {
            $this->Error[] = __("invalid initials");
        }
        if(!(is_string($this->SurName) && strlen($this->SurName) <= 100 || strlen($this->SurName) === 0)) {
            $this->Error[] = __("invalid surname");
        }
        if(!(is_string($this->Address) && strlen($this->Address) <= 100 || strlen($this->Address) === 0)) {
            $this->Error[] = __("invalid address");
        }
        if(!(is_string($this->Address2) && strlen($this->Address2) <= 100 || strlen($this->Address2) === 0)) {
            $this->Error[] = __("invalid address");
        }
        if(!(is_string($this->ZipCode) && strlen($this->ZipCode) <= 10 || strlen($this->ZipCode) === 0)) {
            $this->Error[] = __("invalid zipcode");
        }
        if(!(is_string($this->City) && strlen($this->City) <= 100 || strlen($this->City) === 0)) {
            $this->Error[] = __("invalid city");
        }
        if(!(is_string($this->State) && strlen($this->State) <= 100 || strlen($this->State) === 0)) {
            $this->Error[] = __("invalid state");
        }
        global $array_country;
        if(!(is_string($this->Country) && !is_numeric($this->Country) && strlen($this->Country) <= 10)) {
            $this->Error[] = __("invalid country");
        } elseif(!array_key_exists($this->Country, $array_country)) {
            $this->Error[] = __("invalid country");
        }
        if(!(check_email_address($this->EmailAddress) || strlen($this->EmailAddress) === 0 || 255 < strlen($this->EmailAddress))) {
            $this->Error[] = __("invalid emailaddress");
        }
        if(!(is_string($this->PhoneNumber) && strlen($this->PhoneNumber) <= 25 || strlen($this->PhoneNumber) === 0)) {
            $this->Error[] = __("invalid phonenumber");
        }
        if(!(is_string($this->MobileNumber) && strlen($this->MobileNumber) <= 25 || strlen($this->MobileNumber) === 0)) {
            $this->Error[] = __("invalid mobilenumber");
        }
        if(!(is_string($this->FaxNumber) && strlen($this->FaxNumber) <= 25 || strlen($this->FaxNumber) === 0)) {
            $this->Error[] = __("invalid faxnumber");
        }
        $this->Term = $this->Term != "" ? $this->Term : 0;
        if(!is_numeric($this->Term) || $this->Term < 0) {
            $this->Error[] = __("invalid creditinvoice term");
        }
        if(!(is_string($this->AccountNumber) && strlen($this->AccountNumber) <= 50 || strlen($this->AccountNumber) === 0)) {
            $this->Error[] = __("invalid accountnumber");
        }
        if(!(is_string($this->AccountBIC) && strlen($this->AccountBIC) <= 50 || strlen($this->AccountBIC) === 0)) {
            $this->Error[] = __("invalid accountbic");
        }
        if(!(is_string($this->AccountName) && strlen($this->AccountName) <= 100 || strlen($this->AccountName) === 0)) {
            $this->Error[] = __("invalid accountname");
        }
        if(!(is_string($this->AccountBank) && strlen($this->AccountBank) <= 100 || strlen($this->AccountBank) === 0)) {
            $this->Error[] = __("invalid accountbank");
        }
        if(!(is_string($this->AccountCity) && strlen($this->AccountCity) <= 100 || strlen($this->AccountCity) === 0)) {
            $this->Error[] = __("invalid accountcity");
        }
        if(!in_array($this->Authorisation, ["yes", "no"])) {
            $this->Error[] = __("invalid creditor authorisation");
        }
        return empty($this->Error);
    }
    public function getID($method, $value)
    {
        switch ($method) {
            case "creditorcode":
                $creditor_id = Database_Model::getInstance()->getOne("HostFact_Creditors", ["id"])->where("CreditorCode", $value)->execute();
                return $creditor_id !== false ? $creditor_id->id : false;
                break;
            case "identifier":
                $creditor_id = Database_Model::getInstance()->getOne("HostFact_Creditors", ["id"])->where("id", intval($value))->execute();
                return $creditor_id !== false ? $creditor_id->id : false;
                break;
        }
    }
    public function _checkGroup($CreditorGroupId)
    {
        if(!is_numeric($CreditorGroupId)) {
            return false;
        }
        $CreditorGroup_id = Database_Model::getInstance()->getOne("HostFact_Group", ["id"])->where("Type", "creditor")->where("id", $CreditorGroupId)->execute();
        return $CreditorGroup_id !== false && 0 < $CreditorGroup_id->id ? $CreditorGroup_id->id : false;
    }
    public function is_free($CreditorCode)
    {
        if($CreditorCode) {
            $result = Database_Model::getInstance()->getOne("HostFact_Creditors", ["id"])->where("CreditorCode", $CreditorCode)->execute();
            if(!$result || $result->id == $this->Identifier) {
                return true;
            }
            return false;
        }
        return false;
    }
    public function all($fields, $sort = false, $order = false, $limit = "-1", $searchat = false, $searchfor = false, $group = false, $show_results = MAX_RESULTS_LIST)
    {
        $limit = !is_numeric($show_results) ? -1 : $limit;
        if(!is_array($fields) || empty($fields)) {
            $this->Error[] = __("no fields given for retrieving data");
            return false;
        }
        if(!is_numeric($show_results)) {
            $this->Error[] = __("invalid number for displaying results");
            return false;
        }
        if($group !== false && is_array($group) && 0 < count($group)) {
            $filters = $group;
            if(array_key_exists("group", $group)) {
                $group = $group["group"];
                unset($filters["group"]);
            }
        }
        $search_at = [];
        if($searchat && $searchfor) {
            $search_at = explode("|", $searchat);
        }
        $select = ["HostFact_Creditors.id"];
        foreach ($fields as $column) {
            if($column != "Groups") {
                $select[] = "HostFact_Creditors.`" . $column . "`";
            }
        }
        if($group) {
            Database_Model::getInstance()->get(["HostFact_Creditors", "HostFact_GroupRelations"], $select)->where("HostFact_GroupRelations.`Group`", $group)->where("HostFact_GroupRelations.`Reference` = HostFact_Creditors.`id`");
        } else {
            Database_Model::getInstance()->get("HostFact_Creditors", $select);
        }
        if(!empty($search_at)) {
            $or_clausule = [];
            foreach ($search_at as $searchColumn) {
                if($searchColumn == "PhoneNumber" || $searchColumn == "MobileNumber" || $searchColumn == "FaxNumber") {
                    $searchfor_regexed = preg_replace("/[^0-9a-z]/i", "", $searchfor);
                    if($searchfor_regexed && is_numeric($searchfor_regexed)) {
                        $regex_string = ".*";
                        for ($regex_i = 0; $regex_i < strlen($searchfor_regexed); $regex_i++) {
                            $regex_string .= $searchfor_regexed[$regex_i] . ".*";
                        }
                        $or_clausule[] = ["HostFact_Creditors.`" . $searchColumn . "`", ["REGEXP" => "^" . $regex_string . "\$"]];
                    } else {
                        $or_clausule[] = ["HostFact_Creditors.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                    }
                } elseif($searchColumn == "AccountNumber") {
                    $searchfor_regexed = preg_replace("/[^0-9A-Za-z]/i", "", $searchfor);
                    $regex_string = "^.*";
                    for ($regex_i = 0; $regex_i < strlen($searchfor_regexed); $regex_i++) {
                        $regex_string .= $searchfor_regexed[$regex_i] . ".*";
                    }
                    $or_clausule[] = ["HostFact_Creditors.`" . $searchColumn . "`", ["REGEXP" => "^" . $regex_string . "\$"]];
                } elseif($searchColumn == "CompanyNameExactMatch") {
                    $or_clausule[] = ["HostFact_Creditors.`CompanyName`", $searchfor];
                } elseif($searchColumn == "MyCustomerCodeExactMatch") {
                    $or_clausule[] = ["HostFact_Creditors.`MyCustomerCode`", $searchfor];
                } else {
                    $or_clausule[] = ["HostFact_Creditors.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                }
            }
            if(0 < count($or_clausule)) {
                Database_Model::getInstance()->orWhere($or_clausule);
            }
        }
        $order = $sort ? $order ? $order : "ASC" : "";
        if($sort == "Creditor") {
            Database_Model::getInstance()->orderBy("CONCAT(HostFact_Creditors.`CompanyName`, HostFact_Creditors.`SurName`)", $order);
        } elseif($sort == "CreditorCode") {
            $order = $order ? $order : "ASC";
            Database_Model::getInstance()->orderBy("IF(SUBSTRING(HostFact_Creditors.`CreditorCode`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(HostFact_Creditors.`CreditorCode`,1,1))", $order)->orderBy("LENGTH(HostFact_Creditors.`CreditorCode`)", $order)->orderBy("HostFact_Creditors.`CreditorCode`", $order);
        } elseif($sort) {
            Database_Model::getInstance()->orderBy("HostFact_Creditors.`" . $sort . "`", $order);
        }
        if(0 <= $limit) {
            Database_Model::getInstance()->limit((max(1, $limit) - 1) * $show_results, $show_results);
        }
        Database_Model::getInstance()->where("HostFact_Creditors.Status", ["!=" => 9]);
        if(!empty($filters)) {
            foreach ($filters as $_db_field => $_db_value) {
                switch ($_db_field) {
                    case "created":
                        if(isset($_db_value["from"]) && $_db_value["from"]) {
                            Database_Model::getInstance()->where("HostFact_Creditors.Created", [">=" => $_db_value["from"]]);
                        }
                        if(isset($_db_value["to"]) && $_db_value["to"]) {
                            Database_Model::getInstance()->where("HostFact_Creditors.Created", ["<=" => $_db_value["to"]]);
                        }
                        break;
                    case "modified":
                        if(isset($_db_value["from"]) && $_db_value["from"]) {
                            Database_Model::getInstance()->where("HostFact_Creditors.Modified", [">=" => $_db_value["from"]]);
                        }
                        if(isset($_db_value["to"]) && $_db_value["to"]) {
                            Database_Model::getInstance()->where("HostFact_Creditors.Modified", ["<=" => $_db_value["to"]]);
                        }
                        break;
                }
            }
        }
        $list = [];
        $list["CountRows"] = 0;
        if($creditor_list = Database_Model::getInstance()->execute()) {
            if($group) {
                $list["CountRows"] = Database_Model::getInstance()->rowCount(["HostFact_Creditors", "HostFact_GroupRelations"], "HostFact_Creditors.id");
            } else {
                $list["CountRows"] = Database_Model::getInstance()->rowCount("HostFact_Creditors", "HostFact_Creditors.id");
            }
            foreach ($creditor_list as $result) {
                $list[$result->id] = ["id" => $result->id];
                foreach ($fields as $column) {
                    if(in_array($column, $this->Variables) || in_array($column, ["Created", "Modified"])) {
                        $list[$result->id][$column] = htmlspecialchars($result->{$column});
                    } elseif($column == "Groups") {
                        $list[$result->id][$column] = [];
                        $group_list = Database_Model::getInstance()->get(["HostFact_Group", "HostFact_GroupRelations"], ["HostFact_Group.id", "HostFact_Group.GroupName"])->where("HostFact_GroupRelations.Reference", $result->id)->where("HostFact_GroupRelations.Type", "creditor")->where("HostFact_GroupRelations.Group = HostFact_Group.id")->where("HostFact_Group.Type", "creditor")->where("HostFact_Group.Status", ["!=" => 9])->execute();
                        if($group_list && is_array($group_list)) {
                            foreach ($group_list as $result2) {
                                $list[$result->id][$column][$result2->id] = ["id" => $result2->id, "GroupName" => htmlspecialchars($result2->GroupName)];
                            }
                        }
                    }
                }
                $amount_by_status = Database_Model::getInstance()->get("HostFact_CreditInvoice", ["Status", "SUM(`AmountIncl`) as Amount"])->where("Creditor", $result->id)->groupBy("Status")->execute();
                if($amount_by_status && is_array($amount_by_status)) {
                    foreach ($amount_by_status as $result2) {
                        $a = $result2->Status == "3" ? "Paid" : ($result2->Status == "8" || $result2->Status == "9" ? "Paid" : "Unpaid");
                        $list[$result->id]["Amount" . $a] = isset($list[$result->id]["Amount" . $a]) ? $list[$result->id]["Amount" . $a] + $result2->Amount : $result2->Amount;
                    }
                }
            }
        }
        return $list;
    }
    public function all_small()
    {
        $result = Database_Model::getInstance()->get("HostFact_Creditors", ["id", "CreditorCode", "CompanyName", "SurName", "Initials"])->where("Status", ["!=" => 9])->orderBy("CONCAT(`CompanyName`,`SurName`,`CreditorCode`)", "ASC")->asArray()->execute();
        $list = [];
        if($result && is_array($result)) {
            foreach ($result as $var) {
                $list[$var["id"]] = $var;
                foreach ($var as $column => $value) {
                    $list[$var["id"]][$column] = htmlspecialchars($value);
                }
            }
        }
        return $list;
    }
    public function getCreditorBeginEndCode($firstLast = "first")
    {
        $orderBy = $firstLast == "first" ? "ASC" : "DESC";
        $result = Database_Model::getInstance()->getOne("HostFact_Creditors", ["CreditorCode"])->orderBy("IF(SUBSTRING(`CreditorCode`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(`CreditorCode`,1,1))", $orderBy)->orderBy("LENGTH(`CreditorCode`)", $orderBy)->orderBy("CreditorCode", $orderBy)->execute();
        return $result->CreditorCode;
    }
}

?>