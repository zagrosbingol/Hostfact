<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class handle
{
    public $Identifier;
    public $Debtor;
    public $Handle;
    public $Registrar;
    public $RegistrarHandle;
    public $Initials;
    public $SurName;
    public $Address;
    public $Address2;
    public $ZipCode;
    public $City;
    public $State;
    public $Country;
    public $PhoneNumber;
    public $FaxNumber;
    public $EmailAddress;
    public $Sex;
    public $CompanyName;
    public $LegalForm;
    public $RegType;
    public $CompanyNumber;
    public $TaxNumber;
    public $HandleType;
    public $customvalues;
    public $CountRows;
    public $Error;
    public $Warning;
    public $Success;
    public $Variables;
    public function __construct()
    {
        global $company;
        $this->Country = $company->Country ? $company->Country : "NL";
        $this->Status = "1";
        $this->StateName = "";
        global $array_handletype;
        $this->TypeList = $array_handletype;
        $this->Variables = ["Identifier", "Debtor", "Handle", "Registrar", "RegistrarHandle", "CompanyName", "CompanyNumber", "LegalForm", "TaxNumber", "Sex", "Initials", "SurName", "Address", "Address2", "ZipCode", "City", "State", "Country", "EmailAddress", "PhoneNumber", "FaxNumber", "RegType", "HandleType", "Status"];
        $this->Error = $this->Warning = $this->Success = [];
        $this->CreateAtRegistrar = false;
        $this->Sex = "m";
        $this->customfields_list = [];
        if(!isset($_SESSION["custom_fields"]["handle"]) || $_SESSION["custom_fields"]["handle"]) {
            require_once "class/customfields.php";
            $customfields = new customfields();
            $this->customfields_list = $customfields->getCustomHandleFields();
        }
    }
    public function show($id = NULL)
    {
        $this->Identifier = 0 < $id ? $id : $this->Identifier;
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for handle");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_Handles", ["HostFact_Handles.*", "HostFact_Registrar.Name", "HostFact_Registrar.Class", "HostFact_Registrar.id as RegistrarID"])->join("HostFact_Registrar", "HostFact_Registrar.id = HostFact_Handles.Registrar")->where("HostFact_Handles.id", $this->Identifier)->execute();
        if(!$result || empty($result->id)) {
            $this->Error[] = __("invalid identifier for handle");
            return false;
        }
        foreach ($result as $key => $value) {
            $this->{$key} = htmlspecialchars($value);
        }
        global $array_states;
        $this->StateName = isset($array_states[$this->Country][$this->State]) ? $array_states[$this->Country][$this->State] : $this->State;
        if(!empty($this->customfields_list)) {
            $customfields = new customfields();
            $custom_values = $customfields->getCustomHandleFieldsValues($this->Identifier);
            if($custom_values && is_array($custom_values)) {
                $this->custom = new stdClass();
                $this->customvalues = [];
                foreach ($custom_values as $field_name => $custom_value) {
                    $this->custom->{$field_name} = $custom_value["ValueFormatted"];
                    $this->customvalues[$field_name] = $custom_value["Value"];
                }
            }
        }
        return true;
    }
    public function getRegistrarHandle($handle = NULL, $registrar_id = 0, $fill_object = true)
    {
        if(!$handle) {
            $this->Error[] = __("invalid identifier for handle");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_Handles", ["HostFact_Handles.*", "HostFact_Registrar.Name", "HostFact_Registrar.id as RegistrarID"])->join("HostFact_Registrar", "HostFact_Registrar.id = HostFact_Handles.Registrar")->where("HostFact_Handles.RegistrarHandle", $handle)->where("HostFact_Handles.Registrar", $registrar_id)->where("HostFact_Handles.Status", ["!=" => 9])->execute();
        if(!$result || empty($result->id)) {
            $this->Error[] = __("invalid identifier for handle");
            return false;
        }
        if($fill_object === true) {
            foreach ($result as $key => $value) {
                $this->{$key} = htmlspecialchars($value);
            }
        }
        $this->Identifier = $result->id;
        return true;
    }
    public function add()
    {
        if(!$this->validate()) {
            return false;
        }
        $result = Database_Model::getInstance()->insert("HostFact_Handles", ["Debtor" => $this->Debtor, "Handle" => $this->Handle, "Registrar" => $this->Registrar, "RegistrarHandle" => $this->RegistrarHandle, "Initials" => $this->Initials, "SurName" => $this->SurName, "Address" => $this->Address, "Address2" => isset($this->Address2) ? $this->Address2 : "", "ZipCode" => $this->ZipCode, "City" => $this->City, "State" => isset($this->State) ? $this->State : "", "Country" => $this->Country, "PhoneNumber" => $this->PhoneNumber, "FaxNumber" => $this->FaxNumber, "EmailAddress" => $this->EmailAddress, "Sex" => $this->Sex, "CompanyName" => $this->CompanyName, "LegalForm" => $this->LegalForm, "RegType" => $this->RegType, "CompanyNumber" => $this->CompanyNumber, "TaxNumber" => $this->TaxNumber, "HandleType" => $this->HandleType, "Status" => $this->Status])->execute();
        if($result) {
            $this->Identifier = $result;
            if($this->CreateAtRegistrar) {
                $result = $this->createHandleAtRegistrar();
                if($result) {
                } else {
                    Database_Model::getInstance()->delete("HostFact_Handles")->where("id", $this->Identifier)->execute();
                    $this->Identifier = 0;
                    return false;
                }
            }
            if(isset($this->customfields_list) && 0 < count($this->customfields_list)) {
                $customfields = new customfields();
                $customfields->setCustomHandleFieldsValues($this->Identifier, $this->customvalues);
            }
            $this->Success[] = sprintf(__("handle is created"), $this->Handle);
            return true;
        }
        return false;
    }
    public function edit($id)
    {
        if(!is_numeric($id)) {
            $this->Error[] = __("invalid identifier for handle");
            return false;
        }
        if(!$this->validate()) {
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Handles", ["Debtor" => $this->Debtor, "Handle" => $this->Handle, "Registrar" => $this->Registrar, "RegistrarHandle" => $this->RegistrarHandle, "Initials" => $this->Initials, "SurName" => $this->SurName, "Address" => $this->Address, "Address2" => isset($this->Address2) ? $this->Address2 : "", "ZipCode" => $this->ZipCode, "City" => $this->City, "State" => isset($this->State) ? $this->State : "", "Country" => $this->Country, "PhoneNumber" => $this->PhoneNumber, "FaxNumber" => $this->FaxNumber, "EmailAddress" => $this->EmailAddress, "Sex" => $this->Sex, "CompanyName" => $this->CompanyName, "LegalForm" => $this->LegalForm, "RegType" => $this->RegType, "CompanyNumber" => $this->CompanyNumber, "TaxNumber" => $this->TaxNumber, "HandleType" => $this->HandleType, "Status" => $this->Status])->where("id", $id)->execute();
        if($result) {
            $this->Identifier = $id;
            if(!empty($this->customfields_list)) {
                $customfields = new customfields();
                $customfields->setCustomHandleFieldsValues($this->Identifier, $this->customvalues);
            }
            $this->Success[] = sprintf(__("handle is adjusted"), $this->Handle);
            return true;
        }
        return false;
    }
    public function delete($id)
    {
        if(!is_numeric($id)) {
            $this->Error[] = __("invalid identifier for handle");
            return false;
        }
        if($this->checkDefaultRegistrarHandle($id) === true) {
            $this->Error[] = __("handle is used by a active register");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Handles", ["Status" => 9])->where("id", $id)->execute();
        if($result) {
            return true;
        }
        return false;
    }
    public function checkDefaultRegistrarHandle($id)
    {
        $registrars = Database_Model::getInstance()->getOne("HostFact_Registrar", "count(`id`) as registrars")->orWhere([["AdminHandle", $id], ["TechHandle", $id]])->where("Status", ["!=" => 9])->execute();
        return $registrars !== false && 0 < $registrars->registrars ? true : false;
    }
    public function deleteFromDatabase($id)
    {
        if(!is_numeric($id)) {
            $this->Error[] = __("invalid identifier for handle");
            return false;
        }
        $result = Database_Model::getInstance()->delete("HostFact_Handles")->where("id", $id)->execute();
        if($result) {
            Database_Model::getInstance()->delete("HostFact_Debtor_Custom_Values")->where("ReferenceType", "handle")->where("ReferenceID", $id)->execute();
            return true;
        }
        return false;
    }
    public function validate()
    {
        if(trim($this->Handle) == "") {
            $this->Error[] = __("an internal handle must be given");
        }
        if($this->Debtor && is_numeric($this->Debtor) && 0 < $this->Debtor) {
            $result = Database_Model::getInstance()->getOne("HostFact_Debtors", "id")->where("id", $this->Debtor)->execute();
            if(!$result || $result->id != $this->Debtor) {
                $this->Error[] = __("selected debtor for handle does not exist");
            }
        } else {
            $this->Debtor = 0;
        }
        if($this->Registrar && is_numeric($this->Registrar)) {
            $result = Database_Model::getInstance()->getOne("HostFact_Registrar", "id")->where("id", $this->Registrar)->execute();
            if(!$result || $result->id != $this->Registrar) {
                $this->Error[] = __("selected registrar for handle does not exist");
            }
        } else {
            $this->Registrar = 0;
            $this->RegistrarHandle = "";
        }
        global $array_handlestatus;
        if(!array_key_exists($this->Status, $array_handlestatus)) {
            $this->Error[] = __("unknown status for handle");
        }
        if(!trim($this->CompanyName) && !trim($this->SurName)) {
            $this->Error[] = __("no companyname and no surname are given for handle");
        }
        if(0 < strlen($this->EmailAddress) && !check_email_address($this->EmailAddress, "single")) {
            $this->Error[] = sprintf(__("invalid email"), $this->EmailAddress);
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
        global $array_legaltype;
        if($this->LegalForm && $this->LegalForm != "ANDERS" && !array_key_exists($this->LegalForm, $array_legaltype)) {
            $this->Error[] = __("invalid legalform");
        }
        if($this->Sex && !in_array($this->Sex, settings::GENDER_AVAILABLE_OPTIONS)) {
            $this->Error[] = __("invalid sex");
        } elseif(!$this->Sex) {
            $this->Sex = "m";
        }
        if(!(is_string($this->Initials) && strlen($this->Initials) <= 25 || strlen($this->Initials) === 0)) {
            $this->Error[] = __("invalid initials");
        }
        if(!(is_string($this->SurName) && strlen($this->SurName) <= 100 || strlen($this->SurName) === 0)) {
            $this->Error[] = __("invalid surname");
        }
        if(!(is_string($this->Address) && strlen($this->Address) <= 255 || strlen($this->Address) === 0)) {
            $this->Error[] = __("invalid address");
        }
        if(!(is_string($this->Address2) && strlen($this->Address2) <= 100 || strlen($this->Address2) === 0)) {
            $this->Error[] = __("invalid address");
        }
        if(!(is_string($this->ZipCode) && strlen($this->ZipCode) <= 10 || strlen($this->ZipCode) === 0)) {
            $this->Error[] = __("invalid zipcode");
        }
        if(!(is_string($this->City) && strlen($this->City) <= 255 || strlen($this->City) === 0)) {
            $this->Error[] = __("invalid city");
        }
        if(!(is_string($this->State) && strlen($this->State) <= 100 || strlen($this->State) === 0)) {
            $this->Error[] = __("invalid state");
        }
        if(!$this->Country) {
            global $company;
            $this->Country = $company->Country ? $company->Country : "NL";
        } else {
            global $array_country;
            $this->Country = strtoupper($this->Country);
            $this->Country = array_key_exists($this->Country, $array_country) ? $this->Country : (array_key_exists("EU-" . $this->Country, $array_country) ? "EU-" . $this->Country : $this->Country);
        }
        if(!(is_string($this->PhoneNumber) && strlen($this->PhoneNumber) <= 25 || strlen($this->PhoneNumber) === 0)) {
            $this->Error[] = __("invalid phonenumber");
        }
        if(!(is_string($this->FaxNumber) && strlen($this->FaxNumber) <= 25 || strlen($this->FaxNumber) === 0)) {
            $this->Error[] = __("invalid faxnumber");
        }
        if(!(is_string($this->RegType) && strlen($this->RegType) <= 20 || strlen($this->RegType) === 0)) {
            $this->Error[] = __("invalid regtype");
        }
        if(!(is_string($this->HandleType) && strlen($this->HandleType) <= 20 || strlen($this->HandleType) === 0)) {
            $this->Error[] = __("invalid handletype");
        }
        global $array_legaltype;
        if(!$this->LegalForm || !array_key_exists($this->LegalForm, $array_legaltype)) {
            $this->LegalForm = "ANDERS";
        }
        if(!empty($this->customfields_list)) {
            foreach ($this->customfields_list as $custom_field) {
                switch ($custom_field["LabelType"]) {
                    case "date":
                        $this->customvalues[$custom_field["FieldCode"]] = $this->customvalues[$custom_field["FieldCode"]] && rewrite_date_site2db($this->customvalues[$custom_field["FieldCode"]]) ? date("Y-m-d", strtotime(rewrite_date_site2db($this->customvalues[$custom_field["FieldCode"]]))) : "";
                        break;
                    case "checkbox":
                        $this->customvalues[$custom_field["FieldCode"]] = json_encode($this->customvalues[$custom_field["FieldCode"]]);
                        break;
                    default:
                        if($custom_field["Regex"] && !@preg_match($custom_field["Regex"], $this->customvalues[$custom_field["FieldCode"]])) {
                            $this->Error[] = sprintf(__("custom client fields regex"), $custom_field["LabelTitle"]);
                        }
                }
            }
        }
        return empty($this->Error) ? true : false;
    }
    public function updateWhoisDataToRegistrar($tmp_domain = false)
    {
        if(!$this->Registrar) {
            $this->Error[] = __("cannot update handle at registrar, since no matching handle can be found");
            return false;
        }
        require_once "class/domain.php";
        $domain = new domain();
        $domain->Registrar = $this->Registrar;
        if(!$domain->getRegistrar()) {
            $this->Error = $domain->Error;
            return false;
        }
        if(isset($domain->api->supportHandles) && $domain->api->supportHandles === false) {
            $this->Error[] = sprintf(__("cannot update handle at registrar, since registrar dont know handles"), $domain->RegistrarName);
            return false;
        }
        if(!$this->RegistrarHandle) {
            $this->Error[] = __("cannot update handle at registrar, since no matching handle can be found");
            return false;
        }
        $domain->ownerHandle = $this->Identifier;
        $whois = $domain->createWhois();
        if($tmp_domain) {
            $domain->api->tmp_domain = $tmp_domain;
        }
        $modified_whois = $domain->api->updateContact($this->RegistrarHandle, $whois);
        $this->Error = array_merge($this->Error, $domain->api->Error);
        $this->Warning = array_merge($this->Warning, $domain->api->Warning);
        $this->Success = array_merge($this->Success, $domain->api->Success);
        if($modified_whois) {
            if(isset($domain->api->registrarHandles["owner"]) && $domain->api->registrarHandles["owner"]) {
                Database_Model::getInstance()->update("HostFact_Handles", ["RegistrarHandle" => $domain->api->registrarHandles["owner"]])->where("RegistrarHandle", $this->RegistrarHandle)->where("Registrar", $this->Registrar)->execute();
                $this->Success[] = sprintf(__("handle updated by registrar and created a new one"), $domain->api->registrarHandles["owner"]);
            } else {
                $this->Success[] = sprintf(__("handle updated by registrar"), $this->RegistrarHandle);
            }
            return true;
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
            if(array_key_exists("status", $group)) {
                $group = $group["status"];
                unset($filters["status"]);
            } else {
                $group = false;
            }
        }
        $DebtorArray = ["DebtorCode"];
        $RegistrarArray = ["Name", "Class"];
        $DebtorFields = 0 < count(array_intersect($DebtorArray, $fields)) ? true : false;
        $RegistrarFields = 0 < count(array_intersect($RegistrarArray, $fields)) ? true : false;
        $search_at = [];
        $DebtorSearch = $RegistrarSearch = false;
        if($searchat && $searchfor) {
            $search_at = explode("|", $searchat);
            $DebtorSearch = 0 < count(array_intersect($DebtorArray, $search_at)) ? true : false;
            $RegistrarSearch = 0 < count(array_intersect($RegistrarArray, $search_at)) ? true : false;
        }
        $select = ["HostFact_Handles.id"];
        foreach ($fields as $column) {
            if(in_array($column, $DebtorArray)) {
                $select[] = "HostFact_Debtors.`" . $column . "`";
            } elseif(in_array($column, $RegistrarArray)) {
                $select[] = "HostFact_Registrar.`" . $column . "`";
            } else {
                $select[] = "HostFact_Handles.`" . $column . "`";
            }
        }
        Database_Model::getInstance()->get("HostFact_Handles", $select);
        if($DebtorFields || $DebtorSearch) {
            Database_Model::getInstance()->join("HostFact_Debtors", "HostFact_Debtors.`id`=HostFact_Handles.`Debtor`");
        }
        if($RegistrarFields || $RegistrarSearch) {
            Database_Model::getInstance()->join("HostFact_Registrar", "HostFact_Registrar.`id`=HostFact_Handles.`Registrar`");
        }
        if(!empty($search_at)) {
            $or_clausule = [];
            foreach ($search_at as $searchColumn) {
                if(in_array($searchColumn, ["Debtor", "Registrar"])) {
                    $or_clausule[] = ["HostFact_Handles.`" . $searchColumn . "`", $searchfor];
                } elseif(in_array($searchColumn, $DebtorArray)) {
                    $or_clausule[] = ["HostFact_Debtors.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                } elseif(in_array($searchColumn, $RegistrarArray)) {
                    $or_clausule[] = ["HostFact_Registrar.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                } else {
                    $or_clausule[] = ["HostFact_Handles.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                }
            }
            Database_Model::getInstance()->orWhere($or_clausule);
        }
        if(isset($this->debtor_id) && 0 < $this->debtor_id) {
            Database_Model::getInstance()->orWhere([["HostFact_Handles.Debtor", $this->debtor_id], ["HostFact_Handles.Debtor", 0]]);
        }
        if(isset($this->registrar_id) && 0 < $this->registrar_id) {
            Database_Model::getInstance()->orWhere([["HostFact_Handles.Registrar", $this->registrar_id], ["HostFact_Handles.Registrar", ""]]);
        }
        $order = $sort ? $order ? $order : "ASC" : "";
        if($sort == "Debtor") {
            Database_Model::getInstance()->orderBy("CONCAT(HostFact_Debtors.`CompanyName`, HostFact_Debtors.`SurName`)", $order);
        } elseif($sort == "Registrar") {
            Database_Model::getInstance()->orderBy("HostFact_Registrar.`Name`", $order);
        } elseif(in_array($sort, $DebtorArray)) {
            Database_Model::getInstance()->orderBy("HostFact_Debtors.`" . $sort . "`", $order);
        } elseif(in_array($sort, $RegistrarArray)) {
            Database_Model::getInstance()->orderBy("HostFact_Registrar.`" . $sort . "`", $order);
        } elseif($sort) {
            Database_Model::getInstance()->orderBy("HostFact_Handles.`" . $sort . "`", $order);
        }
        if(0 <= $limit) {
            Database_Model::getInstance()->limit((max(1, $limit) - 1) * $show_results, $show_results);
        }
        if(is_string($group) && 1 < count(explode("|", $group))) {
            $group = explode("|", $group);
            $or_clausule = [];
            foreach ($group as $status) {
                $or_clausule[] = ["HostFact_Handles.`Status`", $status];
            }
            Database_Model::getInstance()->orWhere($or_clausule);
        } elseif(is_numeric($group)) {
            Database_Model::getInstance()->where("HostFact_Handles.`Status`", $group);
        } else {
            Database_Model::getInstance()->where("HostFact_Handles.`Status`", ["NOT IN" => [8, 9]]);
        }
        if(!empty($filters)) {
            foreach ($filters as $_db_field => $_db_value) {
                switch ($_db_field) {
                    case "created":
                        if(isset($_db_value["from"]) && $_db_value["from"]) {
                            Database_Model::getInstance()->where("HostFact_Handles.Created", [">=" => $_db_value["from"]]);
                        }
                        if(isset($_db_value["to"]) && $_db_value["to"]) {
                            Database_Model::getInstance()->where("HostFact_Handles.Created", ["<=" => $_db_value["to"]]);
                        }
                        break;
                    case "modified":
                        if(isset($_db_value["from"]) && $_db_value["from"]) {
                            Database_Model::getInstance()->where("HostFact_Handles.Modified", [">=" => $_db_value["from"]]);
                        }
                        if(isset($_db_value["to"]) && $_db_value["to"]) {
                            Database_Model::getInstance()->where("HostFact_Handles.Modified", ["<=" => $_db_value["to"]]);
                        }
                        break;
                }
            }
        }
        $list = [];
        $list["CountRows"] = 0;
        if($handle_list = Database_Model::getInstance()->execute()) {
            $list["CountRows"] = $this->CountRows = Database_Model::getInstance()->rowCount("HostFact_Handles", "HostFact_Handles.id");
            foreach ($handle_list as $result) {
                $list[$result->id] = ["id" => $result->id];
                foreach ($fields as $column) {
                    $list[$result->id][$column] = htmlspecialchars($result->{$column});
                }
            }
        }
        return $list;
    }
    public function createHandleAtRegistrar()
    {
        require_once "class/registrar.php";
        $registrar = new registrar();
        $registrar->show($this->Registrar);
        $result = $registrar->getAPI();
        if($result === false) {
            $this->Error = array_merge($this->Error, $registrar->Error);
            return false;
        }
        if($result === true) {
            require_once "class/whois.php";
            $whois = new whois();
            foreach ($this->Variables as $key) {
                $whois->{$key} = $this->{$key};
            }
            $whois->CompanyLegalForm = $this->LegalForm;
            if(isset($this->customfields_list) && 0 < count($this->customfields_list)) {
                $whois->custom = $this->custom;
                $whois->customvalues = $this->customvalues;
            }
            $result = $registrar->api->createContact($whois, HANDLE_OWNER);
            if($result === false) {
                $this->Error = array_merge($this->Error, $registrar->Error, $registrar->api->Error);
                return false;
            }
            if($registrar->Class == "opendomainregistry") {
                $result = $registrar->api->LastContactId;
            }
            Database_Model::getInstance()->update("HostFact_Handles", ["RegistrarHandle" => $result])->where("id", $this->Identifier)->execute();
            if($this->CreateAtRegistrar !== true) {
                $this->Success[] = sprintf(__("handle is created"), $result);
            }
            return true;
        } else {
            return true;
        }
    }
    public function nextInternalHandle($handle_type, $debtor_id = false)
    {
        $handle = "";
        switch ($handle_type) {
            case "debtor":
                $result = Database_Model::getInstance()->getOne("HostFact_Debtors", "DebtorCode")->where("id", $debtor_id)->execute();
                $debtorcode = $result->DebtorCode;
                $result = Database_Model::getInstance()->getOne("HostFact_Handles", ["Handle"])->where("Handle", ["LIKE" => $debtorcode . "-%"])->where("SUBSTR(`Handle`,:PrefixLength)", ["REGEXP" => "^[0-9]*\$"])->orderBy("LENGTH(`Handle`)", "DESC")->orderBy("Handle", "DESC")->bindValue("PrefixLength", strlen($debtorcode) + 2)->execute();
                if(isset($result->Handle)) {
                    $result->Handle = isset($result->Handle) ? str_replace($debtorcode, "", $result->Handle) : 0;
                    $result->Handle = substr($result->Handle, strpos($result->Handle, "-") + 1);
                    $result->Handle++;
                    $handle = $debtorcode . "-" . str_pad(max(1, $result->Handle), 3, "0", STR_PAD_LEFT);
                } else {
                    $handle = $debtorcode . "-001";
                }
                break;
            default:
                $result = Database_Model::getInstance()->getOne("HostFact_Handles", ["Handle"])->where("Handle", ["LIKE" => __("GENERAL_HANDLE_PREFIX") . "-%"])->where("SUBSTR(`Handle`,:PrefixLength)", ["REGEXP" => "^[0-9]*\$"])->orderBy("LENGTH(`Handle`)", "DESC")->orderBy("Handle", "DESC")->bindValue("PrefixLength", strlen(__("GENERAL_HANDLE_PREFIX")) + 2)->execute();
                if(isset($result->Handle)) {
                    $result->Handle = str_replace(__("GENERAL_HANDLE_PREFIX"), "", $result->Handle);
                    $result->Handle = substr($result->Handle, strpos($result->Handle, "-") + 1);
                    $result->Handle++;
                    $handle = __("GENERAL_HANDLE_PREFIX") . "-" . str_pad(max(1, $result->Handle), 3, "0", STR_PAD_LEFT);
                } else {
                    $handle = __("GENERAL_HANDLE_PREFIX") . "-001";
                }
                return $handle;
        }
    }
    public function searchExistingHandle($skip_debtor = false)
    {
        Database_Model::getInstance()->getOne("HostFact_Handles", "id")->where("Registrar", $this->Registrar)->where("Initials", $this->Initials)->where("SurName", $this->SurName)->where("Address", $this->Address)->where("ZipCode", $this->ZipCode)->where("City", $this->City)->where("Country", $this->Country)->where("PhoneNumber", $this->PhoneNumber)->where("FaxNumber", $this->FaxNumber)->where("EmailAddress", $this->EmailAddress)->where("Sex", $this->Sex == "" ? "m" : $this->Sex)->where("CompanyName", $this->CompanyName)->where("TaxNumber", $this->TaxNumber);
        if(defined("IS_INTERNATIONAL") && IS_INTERNATIONAL === true) {
            Database_Model::getInstance()->where("Address2", isset($this->Address2) ? $this->Address2 : "")->where("State", isset($this->State) ? $this->State : "");
        }
        if($skip_debtor === false) {
            Database_Model::getInstance()->where("Debtor", intval($this->Debtor));
        }
        $result = Database_Model::getInstance()->where("Status", ["!=" => 9])->execute();
        if($result && 0 < $result->id) {
            return $result->id;
        }
        return false;
    }
    public function lookupHandle($registrar_id)
    {
        $candidates = [];
        $result = Database_Model::getInstance()->get("HostFact_Handles", ["id", "Handle", "Debtor"])->orWhere([["Registrar", $registrar_id], ["Registrar", ""]])->where("Initials", $this->Initials)->where("SurName", $this->SurName)->where("Address", $this->Address)->where("Address2", isset($this->Address2) ? $this->Address2 : "")->where("ZipCode", $this->ZipCode)->where("City", $this->City)->where("State", isset($this->State) ? $this->State : "")->where("Country", $this->Country)->where("PhoneNumber", $this->PhoneNumber)->where("FaxNumber", $this->FaxNumber)->where("EmailAddress", $this->EmailAddress)->where("Sex", $this->Sex)->where("CompanyName", $this->CompanyName)->where("TaxNumber", $this->TaxNumber)->where("Status", ["!=" => 9])->orderBy("Debtor", "DESC")->execute();
        if($result && is_array($result)) {
            foreach ($result as $var) {
                if(isset($var->Debtor)) {
                    $candidates[$var->Debtor] = ["id" => $var->id, "handle" => $var->Handle];
                }
            }
        }
        if(isset($candidates[$this->Debtor])) {
            return $candidates[$this->Debtor]["id"];
        }
        if(count($candidates) == 1 && isset($candidates[0])) {
            return $candidates[0]["id"];
        }
        if(count($candidates) == 1) {
            foreach ($candidates as $debtor_id => $handle_id) {
                $handle_tmp = parsePrefixVariables(DEBTORCODE_PREFIX) && strpos($handle_id["handle"], parsePrefixVariables(DEBTORCODE_PREFIX)) !== false && strpos($handle_id["handle"], parsePrefixVariables(DEBTORCODE_PREFIX)) === 0 ? $this->nextInternalHandle("general") : $handle_id["handle"];
                $result = Database_Model::getInstance()->update("HostFact_Handles", ["Debtor" => 0, "Handle" => $handle_tmp])->where("id", $handle_id["id"])->where("Debtor", $debtor_id)->execute();
                if($result) {
                    return $handle_id["id"];
                }
                return false;
            }
        } elseif(1 < count($candidates)) {
            foreach ($candidates as $debtor_id => $handle_id) {
                $handle_tmp = parsePrefixVariables(DEBTORCODE_PREFIX) && strpos($handle_id["handle"], parsePrefixVariables(DEBTORCODE_PREFIX)) !== false && strpos($handle_id["handle"], parsePrefixVariables(DEBTORCODE_PREFIX)) === 0 ? $this->nextInternalHandle("general") : $handle_id["handle"];
                $result = Database_Model::getInstance()->update("HostFact_Handles", ["Debtor" => 0, "Handle" => $handle_tmp])->where("id", $handle_id["id"])->where("Debtor", $debtor_id)->execute();
                if($result) {
                    return $handle_id["id"];
                }
                return false;
            }
        }
        return false;
    }
    public function changeDebtor($new_debtor_id)
    {
        $this->show();
        if($this->Handle && (substr($this->Handle, 0, strlen(DEBTORCODE_PREFIX)) == DEBTORCODE_PREFIX || substr($this->Handle, 0, strlen(__("GENERAL_HANDLE_PREFIX"))) == __("GENERAL_HANDLE_PREFIX"))) {
            if(empty($new_debtor_id)) {
                $this->Handle = $this->nextInternalHandle("general");
            } else {
                $this->Handle = $this->nextInternalHandle("debtor", $new_debtor_id);
            }
        }
        $result = Database_Model::getInstance()->update("HostFact_Handles", ["Debtor" => $new_debtor_id, "Handle" => $this->Handle])->where("id", $this->Identifier)->execute();
        if($result) {
            return true;
        }
        return false;
    }
    public function updateGeneralToDebtor($handle_id, $new_debtor_id = false)
    {
        $pdo_statement = Database_Model::getInstance()->rawQuery("SELECT Debtor FROM `HostFact_Domains` WHERE (`ownerHandle`=:handle OR `adminHandle`=:handle OR `techHandle`=:handle) AND `Status` != 9 GROUP BY Debtor UNION SELECT Debtor FROM `HostFact_SSL_Certificates` WHERE (`ownerHandle`=:handle OR `adminHandle`=:handle OR `techHandle`=:handle) AND `Status` != 'removed' GROUP BY Debtor", ["handle" => $handle_id]);
        $rows = $pdo_statement->fetchAll();
        if(count($rows) == 1) {
            $debtor_id = $new_debtor_id === false ? $rows[0]->Debtor : $new_debtor_id;
            $this->Identifier = $handle_id;
            if(!$this->changeDebtor($debtor_id)) {
                return false;
            }
        } elseif(1 < count($rows)) {
            $this->Identifier = $handle_id;
            if(!$this->changeDebtor(0)) {
                return false;
            }
        }
        return true;
    }
    public function getID($method, $value)
    {
        switch ($method) {
            case "handle":
                $handle_id = Database_Model::getInstance()->getOne("HostFact_Handles", "id")->where("Handle", $value)->execute();
                return $handle_id && 0 <= $handle_id->id ? $handle_id->id : false;
                break;
            case "id":
                $handleCode = Database_Model::getInstance()->getOne("HostFact_Handles", "Handle")->where("id", intval($value))->execute();
                return $handleCode && $handleCode->Handle ? $handleCode->Handle : false;
                break;
        }
    }
    public function lookupDebtorHandle($debtor_id)
    {
        $result = Database_Model::getInstance()->get(["HostFact_Handles", "HostFact_Debtors"], "HostFact_Handles.id")->where("HostFact_Handles.Debtor = HostFact_Debtors.id")->where("HostFact_Handles.Debtor", $debtor_id)->where("HostFact_Handles.RegistrarHandle", "")->where("HostFact_Handles.CompanyName = HostFact_Debtors.CompanyName")->where("HostFact_Handles.Initials = HostFact_Debtors.Initials")->where("HostFact_Handles.SurName = HostFact_Debtors.SurName")->where("HostFact_Handles.Address = HostFact_Debtors.Address")->where("HostFact_Handles.Address2 = HostFact_Debtors.Address2")->where("HostFact_Handles.ZipCode = HostFact_Debtors.ZipCode")->where("HostFact_Handles.City = HostFact_Debtors.City")->where("HostFact_Handles.State = HostFact_Debtors.State")->where("HostFact_Handles.Country = HostFact_Debtors.Country")->where("HostFact_Handles.PhoneNumber = HostFact_Debtors.PhoneNumber")->where("HostFact_Handles.FaxNumber = HostFact_Debtors.FaxNumber")->where("HostFact_Handles.EmailAddress = IF(LOCATE(';',HostFact_Debtors.`EmailAddress`) = 0, HostFact_Debtors.`EmailAddress`, SUBSTRING(HostFact_Debtors.`EmailAddress`,1,LOCATE(';', HostFact_Debtors.`EmailAddress`)-1))")->where("HostFact_Handles.TaxNumber = HostFact_Debtors.TaxNumber")->where("HostFact_Handles.CompanyNumber = HostFact_Debtors.CompanyNumber")->where("HostFact_Handles.Status", ["!=" => "9"])->where("HostFact_Handles.id", ["NOT IN" => ["RAW" => "SELECT d2.`id` FROM `HostFact_Handles` as h2, `HostFact_Domains` as d2 WHERE d2.`Debtor`=:debtor_id AND h2.`Debtor`=:debtor_id AND d2.`Status`=4 AND (d2.`ownerHandle`=h2.`id` OR d2.`adminHandle`=h2.`id` OR d2.`techHandle`=h2.`id`)"]])->bindValue("debtor_id", $debtor_id)->execute();
        $list_ids = [];
        if($result && is_array($result)) {
            foreach ($result as $var) {
                $list_ids[] = $var->id;
            }
        }
        return $list_ids;
    }
    public function createHandleFromDebtor($debtor_id, $registrar_id)
    {
        require_once "class/debtor.php";
        $debtor = new debtor();
        $debtor->show($debtor_id);
        foreach ($debtor as $key => $value) {
            if(in_array($key, $this->Variables)) {
                if($key == "EmailAddress") {
                    $sEmail = explode(";", htmlspecialchars_decode($value));
                    $this->{$key} = $sEmail[0];
                } else {
                    $this->{$key} = htmlspecialchars_decode($value);
                }
            }
        }
        if(!empty($debtor->customfields_list) && !empty($this->customfields_list)) {
            $this->customvalues = is_string($debtor->customvalues) ? htmlspecialchars_decode($debtor->customvalues) : $debtor->customvalues;
        }
        if(!$this->PhoneNumber && $debtor->MobileNumber) {
            $this->PhoneNumber = $debtor->MobileNumber;
        }
        $this->Debtor = $debtor->Identifier;
        $this->Registrar = $registrar_id;
        $this->RegistrarHandle = "";
        $this->Status = 1;
        $lookup = $this->lookupHandle($this->Registrar);
        if($lookup === false) {
            $this->Handle = $this->nextInternalHandle("debtor", $debtor->Identifier);
            if($this->add()) {
                return ["id" => $this->Identifier, "created" => true];
            }
            return false;
        }
        return ["id" => $lookup, "created" => false];
    }
    public function createHandlesFromImport($whois, $registrar, $debtor_id)
    {
        global $array_country;
        $handle_ids = [];
        $handle_types = ["owner", "admin", "tech"];
        $temp_handles = [];
        foreach ($handle_types as $handle_type) {
            if(isset($temp_handles[$whois->{$handle_type . "Handle"}]) && $whois->{$handle_type . "Handle"}) {
                $handle_ids[$handle_type] = $temp_handles[$whois->{$handle_type . "Handle"}];
            } else {
                $handle_id = 0;
                unset($this->Identifier);
                if(isset($whois->{$handle_type . "Handle"}) && $whois->{$handle_type . "Handle"}) {
                    $this->getRegistrarHandle($whois->{$handle_type . "Handle"}, $registrar->Identifier, false);
                    if(isset($this->Identifier) && 0 < $this->Identifier) {
                        $handle_id = $this->Identifier;
                    } elseif($whois->{$handle_type . "EmailAddress"}) {
                        $this->Handle = $whois->{$handle_type . "Handle"};
                        $this->RegistrarHandle = $whois->{$handle_type . "Handle"};
                        $this->CompanyName = $whois->{$handle_type . "CompanyName"};
                        $this->Initials = $whois->{$handle_type . "Initials"};
                        $this->SurName = $whois->{$handle_type . "SurName"};
                        $this->Address = isset($whois->{$handle_type . "Address"}) ? $whois->{$handle_type . "Address"} : $whois->{$handle_type . "StreetName"} . " " . $whois->{$handle_type . "StreetNumber"};
                        $this->Address2 = $whois->{$handle_type . "Address2"};
                        $this->ZipCode = $whois->{$handle_type . "ZipCode"};
                        $this->City = $whois->{$handle_type . "City"};
                        $this->State = $whois->{$handle_type . "State"};
                        $this->Country = array_key_exists($whois->{$handle_type . "Country"}, $array_country) ? $whois->{$handle_type . "Country"} : (array_key_exists("EU-" . $whois->{$handle_type . "Country"}, $array_country) ? "EU-" . $whois->{$handle_type . "Country"} : "");
                        $this->PhoneNumber = $whois->{$handle_type . "PhoneNumber"};
                        $this->FaxNumber = $whois->{$handle_type . "FaxNumber"};
                        $this->EmailAddress = $whois->{$handle_type . "EmailAddress"};
                        $this->TaxNumber = $whois->{$handle_type . "TaxNumber"};
                        $this->Sex = $whois->{$handle_type . "Sex"};
                        $this->HandleType = $whois->{$handle_type . "HandleType"};
                    } else {
                        $registrar_handle = $registrar->api->getContact($whois->{$handle_type . "Handle"});
                        $this->Handle = $whois->{$handle_type . "Handle"};
                        $this->RegistrarHandle = $whois->{$handle_type . "Handle"};
                        $this->CompanyName = $registrar_handle->ownerCompanyName;
                        $this->Initials = $registrar_handle->ownerInitials;
                        $this->SurName = $registrar_handle->ownerSurName;
                        $this->Address = isset($registrar_handle->ownerAddress) ? $registrar_handle->ownerAddress : $registrar_handle->ownerStreetName . " " . $registrar_handle->ownerStreetNumber;
                        $this->Address2 = $registrar_handle->ownerAddress2;
                        $this->ZipCode = $registrar_handle->ownerZipCode;
                        $this->City = $registrar_handle->ownerCity;
                        $this->State = $registrar_handle->ownerState;
                        $this->Country = array_key_exists($registrar_handle->ownerCountry, $array_country) ? $registrar_handle->ownerCountry : (array_key_exists("EU-" . $registrar_handle->ownerCountry, $array_country) ? "EU-" . $registrar_handle->ownerCountry : "");
                        $this->PhoneNumber = $registrar_handle->ownerPhoneNumber;
                        $this->FaxNumber = $registrar_handle->ownerFaxNumber;
                        $this->EmailAddress = $registrar_handle->ownerEmailAddress;
                        $this->TaxNumber = $registrar_handle->ownerTaxNumber;
                        $this->Sex = $registrar_handle->ownerSex;
                        $this->HandleType = $registrar_handle->ownerHandleType;
                    }
                } elseif($whois->{$handle_type . "EmailAddress"}) {
                    $this->Handle = $this->nextInternalHandle("debtor", $debtor_id);
                    $this->CompanyName = $whois->{$handle_type . "CompanyName"};
                    $this->Initials = $whois->{$handle_type . "Initials"};
                    $this->SurName = $whois->{$handle_type . "SurName"};
                    $this->Address = isset($whois->{$handle_type . "Address"}) ? $whois->{$handle_type . "Address"} : $whois->{$handle_type . "StreetName"} . " " . $whois->{$handle_type . "StreetNumber"};
                    $this->Address2 = $whois->{$handle_type . "Address2"};
                    $this->ZipCode = $whois->{$handle_type . "ZipCode"};
                    $this->City = $whois->{$handle_type . "City"};
                    $this->State = $whois->{$handle_type . "State"};
                    $this->Country = array_key_exists($whois->{$handle_type . "Country"}, $array_country) ? $whois->{$handle_type . "Country"} : (array_key_exists("EU-" . $whois->{$handle_type . "Country"}, $array_country) ? "EU-" . $whois->{$handle_type . "Country"} : "");
                    $this->PhoneNumber = $whois->{$handle_type . "PhoneNumber"};
                    $this->FaxNumber = $whois->{$handle_type . "FaxNumber"};
                    $this->EmailAddress = $whois->{$handle_type . "EmailAddress"};
                    $this->TaxNumber = $whois->{$handle_type . "TaxNumber"};
                    $this->Sex = $whois->{$handle_type . "Sex"};
                    $this->HandleType = $whois->{$handle_type . "HandleType"};
                }
                $this->Registrar = $registrar->Identifier;
                $this->Debtor = 0;
                $this->Status = 1;
                $this->CreateAtRegistrar = false;
                if(isset($handle_id) && 0 < $handle_id) {
                    $handle_ids[$handle_type] = $handle_id;
                } else {
                    foreach ($this->Variables as $_variable) {
                        if(!isset($this->{$_variable})) {
                            $this->{$_variable} = "";
                        }
                    }
                    $handle_id = $this->searchExistingHandle(true);
                    if($handle_id && 0 < $handle_id) {
                        $handle_ids[$handle_type] = $handle_id;
                    } else {
                        $this->Error = [];
                        if($this->add()) {
                            $handle_ids[$handle_type] = $this->Identifier;
                        } elseif($handle_type == "owner") {
                            return false;
                        }
                    }
                }
                if($whois->{$handle_type . "Handle"}) {
                    $temp_handles[$whois->{$handle_type . "Handle"}] = $handle_ids[$handle_type];
                }
            }
        }
        return $handle_ids;
    }
    public function syncDebtorToHandle($debtor_id, $handle_ids = [])
    {
        $result = Database_Model::getInstance()->update(["HostFact_Handles", "HostFact_Debtors"], ["HostFact_Handles.CompanyName" => ["RAW" => "HostFact_Debtors.CompanyName"], "HostFact_Handles.Initials" => ["RAW" => "HostFact_Debtors.Initials"], "HostFact_Handles.SurName" => ["RAW" => "HostFact_Debtors.SurName"], "HostFact_Handles.Address" => ["RAW" => "HostFact_Debtors.Address"], "HostFact_Handles.Address2" => ["RAW" => "HostFact_Debtors.Address2"], "HostFact_Handles.ZipCode" => ["RAW" => "HostFact_Debtors.ZipCode"], "HostFact_Handles.City" => ["RAW" => "HostFact_Debtors.City"], "HostFact_Handles.State" => ["RAW" => "HostFact_Debtors.State"], "HostFact_Handles.Country" => ["RAW" => "HostFact_Debtors.Country"], "HostFact_Handles.PhoneNumber" => ["RAW" => "HostFact_Debtors.`PhoneNumber`"], "HostFact_Handles.FaxNumber" => ["RAW" => "HostFact_Debtors.FaxNumber"], "HostFact_Handles.EmailAddress" => ["RAW" => "IF(LOCATE(';',HostFact_Debtors.`EmailAddress`) = 0, HostFact_Debtors.`EmailAddress`, SUBSTRING(HostFact_Debtors.`EmailAddress`,1,LOCATE(';', HostFact_Debtors.`EmailAddress`)-1))"], "HostFact_Handles.LegalForm" => ["RAW" => "HostFact_Debtors.LegalForm"], "HostFact_Handles.TaxNumber" => ["RAW" => "HostFact_Debtors.TaxNumber"], "HostFact_Handles.CompanyNumber" => ["RAW" => "HostFact_Debtors.CompanyNumber"]])->where("HostFact_Handles.Debtor = HostFact_Debtors.id")->where("HostFact_Handles.id", ["IN" => $handle_ids])->where("HostFact_Handles.Status", ["!=" => 9])->execute();
        if(!empty($this->customfields_list)) {
            require_once "class/customfields.php";
            $customfields = new customfields();
            $customfields->syncCustomFields("debtor", $debtor_id, "handle", $handle_ids);
        }
    }
    public function lookupNewCustomerHandle($customer_id)
    {
        $result = Database_Model::getInstance()->get(["HostFact_Handles", "HostFact_NewCustomers"], "HostFact_Handles.id")->where("HostFact_Handles.Debtor", "-1")->where("HostFact_NewCustomers.id", $customer_id)->where("HostFact_Handles.RegistrarHandle", "")->where("HostFact_Handles.CompanyName = HostFact_NewCustomers.CompanyName")->where("HostFact_Handles.Initials = HostFact_NewCustomers.Initials")->where("HostFact_Handles.SurName = HostFact_NewCustomers.SurName")->where("HostFact_Handles.Address = HostFact_NewCustomers.Address")->where("HostFact_Handles.Address2 = HostFact_NewCustomers.Address2")->where("HostFact_Handles.ZipCode = HostFact_NewCustomers.ZipCode")->where("HostFact_Handles.City = HostFact_NewCustomers.City")->where("HostFact_Handles.State = HostFact_NewCustomers.State")->where("HostFact_Handles.Country = HostFact_NewCustomers.Country")->orWhere([["HostFact_Handles.PhoneNumber = HostFact_NewCustomers.PhoneNumber"], ["AND" => [["HostFact_Handles.PhoneNumber = HostFact_NewCustomers.MobileNumber"], ["HostFact_NewCustomers.PhoneNumber", ""]]]])->where("HostFact_Handles.FaxNumber = HostFact_NewCustomers.FaxNumber")->where("HostFact_Handles.EmailAddress = HostFact_NewCustomers.EmailAddress")->where("HostFact_Handles.TaxNumber = HostFact_NewCustomers.TaxNumber")->where("HostFact_Handles.CompanyNumber = HostFact_NewCustomers.CompanyNumber")->where("HostFact_Handles.Status", ["!=" => "9"])->execute();
        $list_ids = [];
        if($result && is_array($result)) {
            foreach ($result as $var) {
                $list_ids[] = $var->id;
            }
        }
        return $list_ids;
    }
    public function syncNewCustomerToHandle($customer_id, $handle_ids = [])
    {
        $result = Database_Model::getInstance()->update(["HostFact_Handles", "HostFact_NewCustomers"], ["HostFact_Handles.CompanyName" => ["RAW" => "HostFact_NewCustomers.CompanyName"], "HostFact_Handles.Initials" => ["RAW" => "HostFact_NewCustomers.Initials"], "HostFact_Handles.SurName" => ["RAW" => "HostFact_NewCustomers.SurName"], "HostFact_Handles.Address" => ["RAW" => "HostFact_NewCustomers.Address"], "HostFact_Handles.Address2" => ["RAW" => "HostFact_NewCustomers.Address2"], "HostFact_Handles.ZipCode" => ["RAW" => "HostFact_NewCustomers.ZipCode"], "HostFact_Handles.City" => ["RAW" => "HostFact_NewCustomers.City"], "HostFact_Handles.State" => ["RAW" => "HostFact_NewCustomers.State"], "HostFact_Handles.Country" => ["RAW" => "HostFact_NewCustomers.Country"], "HostFact_Handles.PhoneNumber" => ["RAW" => "IF(HostFact_NewCustomers.`PhoneNumber`,HostFact_NewCustomers.`PhoneNumber`,HostFact_NewCustomers.`MobileNumber`)"], "HostFact_Handles.FaxNumber" => ["RAW" => "HostFact_NewCustomers.FaxNumber"], "HostFact_Handles.EmailAddress" => ["RAW" => "IF(LOCATE(';',HostFact_NewCustomers.`EmailAddress`) = 0, HostFact_NewCustomers.`EmailAddress`, SUBSTRING(HostFact_NewCustomers.`EmailAddress`,1,LOCATE(';', HostFact_NewCustomers.`EmailAddress`)-1))"], "HostFact_Handles.LegalForm" => ["RAW" => "HostFact_NewCustomers.LegalForm"], "HostFact_Handles.TaxNumber" => ["RAW" => "HostFact_NewCustomers.TaxNumber"], "HostFact_Handles.CompanyNumber" => ["RAW" => "HostFact_NewCustomers.CompanyNumber"]])->where("HostFact_Handles.Debtor", "-1")->where("HostFact_NewCustomers.id", $customer_id)->where("HostFact_Handles.id", ["IN" => $handle_ids])->where("HostFact_Handles.Status", ["!=" => 9])->execute();
        if(!empty($this->customfields_list)) {
            require_once "class/customfields.php";
            $customfields = new customfields();
            $customfields->syncCustomFields("newcustomer", $customer_id, "handle", $handle_ids);
        }
    }
    public function cleanup()
    {
        $result_handles = Database_Model::getInstance()->getOne("HostFact_Handles", "GROUP_CONCAT(`id`) as handle_ids")->where("", ["NOT EXISTS" => ["RAW" => "(SELECT null FROM HostFact_Domains WHERE HostFact_Domains.`ownerHandle` = HostFact_Handles.`id`)"]])->where("", ["NOT EXISTS" => ["RAW" => "(SELECT null FROM HostFact_Domains WHERE HostFact_Domains.`adminHandle` = HostFact_Handles.`id`)"]])->where("", ["NOT EXISTS" => ["RAW" => "(SELECT null FROM HostFact_Domains WHERE HostFact_Domains.`techHandle` = HostFact_Handles.`id`)"]])->where("", ["NOT EXISTS" => ["RAW" => "(SELECT null FROM HostFact_SSL_Certificates WHERE HostFact_SSL_Certificates.`ownerHandle` = HostFact_Handles.`id`)"]])->where("", ["NOT EXISTS" => ["RAW" => "(SELECT null FROM HostFact_SSL_Certificates WHERE HostFact_SSL_Certificates.`adminHandle` = HostFact_Handles.`id`)"]])->where("", ["NOT EXISTS" => ["RAW" => "(SELECT null FROM HostFact_SSL_Certificates WHERE HostFact_SSL_Certificates.`techHandle` = HostFact_Handles.`id`)"]])->where("", ["NOT EXISTS" => ["RAW" => "(SELECT null FROM HostFact_Registrar WHERE HostFact_Registrar.`adminHandle` = HostFact_Handles.`id`)"]])->where("", ["NOT EXISTS" => ["RAW" => "(SELECT null FROM HostFact_Registrar WHERE HostFact_Registrar.`techHandle` = HostFact_Handles.`id`)"]])->where("HostFact_Handles.`Status`", 1)->execute();
        if($result_handles && isset($result_handles->handle_ids) && $result_handles->handle_ids) {
            $handle_ids = explode(",", $result_handles->handle_ids);
            if(is_array($handle_ids) && 0 < count($handle_ids)) {
                $result = Database_Model::getInstance()->update("HostFact_Handles", ["Status" => 9])->where("id", ["IN" => $handle_ids])->execute();
                if($result) {
                    $this->Success[] = sprintf(__("x number of handles cleaned up"), count($handle_ids));
                    return true;
                }
            }
        }
        $this->Warning[] = __("no handles found to be cleaned");
        return false;
    }
}

?>