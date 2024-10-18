<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class domain
{
    public $Identifier;
    public $Debtor;
    public $Product;
    public $Domain;
    public $Tld;
    public $RegistrationDate;
    public $ExpirationDate;
    public $Registrar;
    public $DNS1;
    public $DNS2;
    public $DNS3;
    public $DNS1IP;
    public $DNS2IP;
    public $DNS3IP;
    public $DNSTemplate;
    public $api;
    public $OldDNS1;
    public $OldDNS2;
    public $OldDNS3;
    public $Status;
    public $PeriodicID;
    public $HostingID;
    public $ownerHandle;
    public $adminHandle;
    public $techHandle;
    public $AuthKey;
    public $Type;
    public $Comment;
    public $ExtraFields;
    public $listExtraFields;
    public $CountRows;
    public $Error = [];
    public $Warning = [];
    public $Success = [];
    public $Variables;
    public function __construct()
    {
        global $company;
        if($company->Country) {
            $result = Database_Model::getInstance()->getOne("HostFact_TopLevelDomain", ["id", "Registrar"])->where("Tld", strtolower($company->Country))->execute();
            if($result && 0 < $result->id) {
                $this->Tld = strtolower($company->Country);
                $this->Registrar = $result->Registrar;
            }
        }
        global $array_domainstatus;
        $this->StatusList = $array_domainstatus;
        $this->Variables = ["Identifier", "Debtor", "Product", "Domain", "Tld", "RegistrationDate", "ExpirationDate", "Registrar", "DNS1", "DNS2", "DNS3", "Status", "PeriodicID", "HostingID", "ownerHandle", "adminHandle", "techHandle", "AuthKey", "Type", "Comment", "DNS1IP", "DNS2IP", "DNS3IP", "DNSTemplate"];
        $this->PeriodicID = 0;
        $this->HostingID = 0;
        $this->Status = 1;
        $this->Periodic_TaxPercentage = STANDARD_TAX;
        $this->ChargeCosts = false;
        $this->ExtraFields = [];
    }
    public function __destruct()
    {
        if(isset($this->Periodic) && is_object($this->Periodic)) {
            $this->Periodic->__destruct();
            unset($this->Periodic);
        }
    }
    public function show($id = NULL)
    {
        $id = 0 < $id ? $id : $this->Identifier;
        if(!is_numeric($id)) {
            $this->Error[] = __("invalid identifier for domain");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_Domains", ["HostFact_Domains.*", "HostFact_Registrar.Name", "HostFact_Registrar.Class as RegistrarClass", "HostFact_Hosting.Username"])->join("HostFact_Registrar", "HostFact_Registrar.id = HostFact_Domains.Registrar")->join("HostFact_Hosting", "HostFact_Hosting.id = HostFact_Domains.HostingID")->where("HostFact_Domains.id", $id)->execute();
        if(!$result || empty($result->id)) {
            $this->Error[] = __("invalid identifier for domain");
            return false;
        }
        foreach ($result as $key => $value) {
            $this->{$key} = htmlspecialchars($value);
        }
        $this->RegistrationDate = rewrite_date_db2site($this->RegistrationDate);
        $this->ExpirationDate = rewrite_date_db2site($this->ExpirationDate);
        $temp_ns = explode(";", $this->DNS1);
        $this->DNS1 = $temp_ns[0];
        $this->DNS1IP = isset($temp_ns[1]) && $temp_ns[1] != __("cannot obtain IP") ? $temp_ns[1] : "";
        $temp_ns = explode(";", $this->DNS2);
        $this->DNS2 = $temp_ns[0];
        $this->DNS2IP = isset($temp_ns[1]) && $temp_ns[1] != __("cannot obtain IP") ? $temp_ns[1] : "";
        $temp_ns = explode(";", $this->DNS3);
        $this->DNS3 = $temp_ns[0];
        $this->DNS3IP = isset($temp_ns[1]) && $temp_ns[1] != __("cannot obtain IP") ? $temp_ns[1] : "";
        unset($temp_ns);
        $this->OldDNS1 = $this->DNS1;
        $this->OldDNS2 = $this->DNS2;
        $this->OldDNS3 = $this->DNS3;
        $this->OldDNSTemplate = $this->DNSTemplate;
        if(0 < $this->PeriodicID) {
            require_once "class/periodic.php";
            $this->Periodic = new periodic();
            $this->Periodic->show($this->PeriodicID);
            if($this->Periodic->Status == 9 || $this->Periodic->Status == 8 && $this->Periodic->TerminationDate == "") {
                $this->Periodic->delete($this->PeriodicID);
                unset($this->Periodic);
                $this->PeriodicID = 0;
            }
        }
        $this->Periodic_TaxPercentage = btwcheck($this->Debtor, STANDARD_TAX);
        if($this->Status == 6) {
            $this->getPendingInformation($id);
        }
        if($this->Status == 4) {
            $result = Database_Model::getInstance()->getOne("HostFact_Domains", ["HostFact_Products.PriceExcl as OwnerChangeCost", "HostFact_Products.TaxPercentage"])->join("HostFact_TopLevelDomain", "HostFact_Domains.Tld = HostFact_TopLevelDomain.Tld")->join("HostFact_Products", "HostFact_TopLevelDomain.OwnerChangeCost = HostFact_Products.id")->where("HostFact_Domains.id", $id)->execute();
            global $array_taxpercentages;
            $this->OwnerChangeCostLabel = $result && 0 < $result->OwnerChangeCost ? VAT_CALC_METHOD == "incl" ? money(round($result->OwnerChangeCost * (1 + $result->TaxPercentage), 5)) . " " . __("incl vat") : money($result->OwnerChangeCost) . (!empty($array_taxpercentages) ? " " . __("excl vat") : "") : "";
            $this->OwnerChangeCost = $result && 0 < $result->OwnerChangeCost ? VAT_CALC_METHOD == "incl" ? round($result->OwnerChangeCost * (1 + $result->TaxPercentage), 5) : $result->OwnerChangeCost : "";
        }
        require_once "class/terminationprocedure.php";
        $termination = new Termination_Model();
        if($termination->show("domain", $this->id)) {
            $this->Termination = (object) ["Date" => $termination->Date, "Created" => $termination->Created, "Status" => $termination->Status];
        }
        $this->Identifier = $id;
        return true;
    }
    public function showHandles()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for domain");
            return false;
        }
        require_once "class/handle.php";
        if(!isset($this->Handles)) {
            $this->Handles = new stdClass();
        }
        if($this->ownerHandle) {
            $this->Handles->Owner = new handle();
            $this->Handles->Owner->Identifier = $this->ownerHandle;
            $this->Handles->Owner->show();
        }
        if($this->adminHandle) {
            $this->Handles->Admin = new handle();
            $this->Handles->Admin->Identifier = $this->adminHandle;
            $this->Handles->Admin->show();
        }
        if($this->techHandle) {
            $this->Handles->Tech = new handle();
            $this->Handles->Tech->Identifier = $this->techHandle;
            $this->Handles->Tech->show();
        }
        return true;
    }
    public function showExtraFields($id = NULL)
    {
        $id = 0 < $id ? $id : $this->Identifier;
        if(!is_numeric($id)) {
            $this->Error[] = __("invalid identifier for domain");
            return false;
        }
        $field_list = Database_Model::getInstance()->get(["HostFact_Domain_Extra_Values", "HostFact_Domain_Extra_Fields"], ["HostFact_Domain_Extra_Fields.*", "HostFact_Domain_Extra_Values.Value"])->where("HostFact_Domain_Extra_Values.DomainID", $id)->where("HostFact_Domain_Extra_Fields.`id` = HostFact_Domain_Extra_Values.`FieldID`")->orderBy("HostFact_Domain_Extra_Fields.id", "ASC")->execute();
        if($field_list && is_array($field_list)) {
            foreach ($field_list as $result) {
                $opt_array = [];
                if($result->LabelType == "options") {
                    $tmp = explode(";", $result->LabelOptions);
                    foreach ($tmp as $tmp_val) {
                        $tmp2 = explode("=", $tmp_val);
                        $opt_array[$tmp2[0]] = $tmp2[1];
                    }
                }
                $this->ExtraFields[$result->id] = ["LabelTitle" => htmlspecialchars($result->LabelTitle), "LabelType" => $result->LabelType, "LabelOptions" => $opt_array, "LabelDefault" => $result->LabelDefault, "Value" => htmlspecialchars($result->Value)];
            }
        }
        return true;
    }
    public function getExtraFields($id)
    {
        $extra_fields = [];
        if(!is_numeric($id)) {
            return $extra_fields;
        }
        $field_list = Database_Model::getInstance()->get(["HostFact_Domain_Extra_Values", "HostFact_Domain_Extra_Fields"], ["HostFact_Domain_Extra_Fields.*", "HostFact_Domain_Extra_Values.Value"])->where("HostFact_Domain_Extra_Values.DomainID", $id)->where("HostFact_Domain_Extra_Fields.`id` = HostFact_Domain_Extra_Values.`FieldID`")->execute();
        if($field_list && is_array($field_list)) {
            foreach ($field_list as $result) {
                $extra_fields[$result->RegistrarField] = ["LabelTitle" => htmlspecialchars($result->LabelTitle), "Value" => htmlspecialchars($result->Value)];
            }
        }
        return $extra_fields;
    }
    public function editExtraFields($domain_id, $extra_fields)
    {
        $result = Database_Model::getInstance()->delete("HostFact_Domain_Extra_Values")->where("DomainID", $domain_id)->where("FieldID", ["NOT IN" => ["RAW" => "SELECT `id` FROM `HostFact_Domain_Extra_Fields` WHERE `Registrar`=:registrar_id"]])->bindValue("registrar_id", $this->Registrar)->execute();
        $this->showExtraFields($domain_id);
        if(!empty($extra_fields)) {
            foreach ($extra_fields as $field_id => $field_value) {
                if(array_key_exists($field_id, $this->ExtraFields)) {
                    Database_Model::getInstance()->update("HostFact_Domain_Extra_Values", ["Value" => esc($field_value)])->where("DomainID", $domain_id)->where("FieldID", $field_id)->execute();
                } else {
                    Database_Model::getInstance()->insert("HostFact_Domain_Extra_Values", ["Value" => esc($field_value), "DomainID" => $domain_id, "FieldID" => $field_id])->execute();
                }
            }
        }
        return NULL;
    }
    public function listExtraFields($tld, $registrar)
    {
        $listExtraFields = [];
        if(!$tld || !is_numeric($registrar)) {
            return $listExtraFields;
        }
        $field_list = Database_Model::getInstance()->get("HostFact_Domain_Extra_Fields")->where("Registrar", $registrar)->where("Tld", ["IN" => ["all", $tld]])->orderBy("id", "ASC")->execute();
        if($field_list && is_array($field_list)) {
            foreach ($field_list as $result) {
                $opt_array = [];
                if($result->LabelType == "options") {
                    $tmp = explode(";", $result->LabelOptions);
                    foreach ($tmp as $tmp_val) {
                        $tmp2 = explode("=", $tmp_val);
                        $opt_array[$tmp2[0]] = $tmp2[1];
                    }
                }
                $listExtraFields[$result->id] = ["LabelTitle" => htmlspecialchars($result->LabelTitle), "LabelType" => $result->LabelType, "LabelOptions" => $opt_array, "LabelDefault" => $result->LabelDefault];
            }
        }
        return $listExtraFields;
    }
    public function getHandleData($handle_id, $prefix)
    {
        if(!is_numeric($handle_id)) {
            $this->Error[] = __("invalid identifier for handle");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_Handles")->where("id", $handle_id)->execute();
        foreach ($result as $key => $value) {
            if($key == "LegalForm") {
                $key = "CompanyLegalForm";
            }
            $this->{$prefix . $key} = htmlspecialchars($value);
        }
        global $array_states;
        $this->{$prefix . "StateName"} = isset($array_states[$this->{$prefix . "Country"}][$this->{$prefix . "State"}]) ? $array_states[$this->{$prefix . "Country"}][$this->{$prefix . "State"}] : $this->{$prefix . "State"};
        return true;
    }
    public function add()
    {
        $this->getRegistrar();
        $this->RegistrationDate = rewrite_date_site2db($this->RegistrationDate);
        $this->ExpirationDate = rewrite_date_site2db($this->ExpirationDate);
        if(!$this->validate()) {
            return false;
        }
        $this->concatNameservers();
        $result = Database_Model::getInstance()->insert("HostFact_Domains", ["Debtor" => $this->Debtor, "Product" => $this->Product, "Domain" => $this->Domain, "Tld" => $this->Tld, "RegistrationDate" => $this->RegistrationDate, "ExpirationDate" => $this->ExpirationDate, "Registrar" => $this->Registrar, "DNS1" => $this->DNS1, "DNS2" => $this->DNS2, "DNS3" => $this->DNS3, "Status" => $this->Status, "PeriodicID" => $this->PeriodicID, "HostingID" => $this->HostingID, "ownerHandle" => $this->ownerHandle, "adminHandle" => $this->adminHandle, "techHandle" => $this->techHandle, "AuthKey" => $this->AuthKey, "Type" => $this->Type, "Comment" => $this->Comment])->execute();
        if($result) {
            $this->Identifier = $result;
            createLog("domain", $this->Identifier, "domain created", [$this->Domain . "." . $this->Tld]);
            if(0 < $this->HostingID && isset($this->DirectServerCreation) && $this->DirectServerCreation) {
                $result = Database_Model::getInstance()->getOne("HostFact_Hosting", "Status")->where("id", $this->HostingID)->execute();
                if($result && $result->Status == 4) {
                    require_once "class/hosting.php";
                    $hosting = new hosting();
                    $hosting->addDomainToServer(strtolower($this->Domain . "." . $this->Tld), $this->HostingID);
                    $this->Warning = array_merge($this->Warning, $hosting->Error);
                    $this->Success = array_merge($this->Success, $hosting->Success);
                    unset($hosting);
                }
            }
            $this->Success[] = sprintf(__("domain created"), $this->Domain . "." . $this->Tld);
            return true;
        }
        return false;
    }
    public function edit($id, $nameserverchange = true)
    {
        if(!is_numeric($id)) {
            $this->Error[] = __("invalid identifier for domain");
            return false;
        }
        $this->getRegistrar();
        $this->RegistrationDate = rewrite_date_site2db($this->RegistrationDate);
        $this->ExpirationDate = rewrite_date_site2db($this->ExpirationDate);
        if(!$this->validate()) {
            return false;
        }
        if($nameserverchange && ($this->DNS1 != $this->OldDNS1 || $this->DNS2 != $this->OldDNS2 || $this->DNS3 != $this->OldDNS3 || 0 < $this->DNSTemplate && $this->DNSTemplate != $this->OldDNSTemplate) && in_array($this->Status, [4, 8])) {
            $this->changeNameserver(false);
        }
        $this->concatNameservers();
        $result = Database_Model::getInstance()->update("HostFact_Domains", ["Debtor" => $this->Debtor, "Product" => $this->Product, "Domain" => $this->Domain, "Tld" => $this->Tld, "RegistrationDate" => $this->RegistrationDate, "ExpirationDate" => $this->ExpirationDate, "Registrar" => $this->Registrar, "DNS1" => $this->DNS1, "DNS2" => $this->DNS2, "DNS3" => $this->DNS3, "Status" => $this->Status, "PeriodicID" => $this->PeriodicID, "HostingID" => $this->HostingID, "ownerHandle" => $this->ownerHandle, "adminHandle" => $this->adminHandle, "techHandle" => $this->techHandle, "AuthKey" => $this->AuthKey, "Type" => $this->Type, "Comment" => $this->Comment])->where("id", $id)->execute();
        if($result) {
            $this->Identifier = $id;
            createLog("domain", $this->Identifier, "domain adjusted", [$this->Domain . "." . $this->Tld]);
            if(0 < $this->HostingID && isset($this->DirectServerCreation) && $this->DirectServerCreation) {
                $result = Database_Model::getInstance()->getOne("HostFact_Hosting", "Status")->where("id", $this->HostingID)->execute();
                if($result && $result->Status == 4) {
                    require_once "class/hosting.php";
                    $hosting = new hosting();
                    $hosting->addDomainToServer(strtolower($this->Domain . "." . $this->Tld), $this->HostingID);
                    $this->Warning = array_merge($this->Warning, $hosting->Error);
                    $this->Success = array_merge($this->Success, $hosting->Success);
                    unset($hosting);
                }
            }
            $this->Success[] = sprintf(__("domain adjusted"), $this->Domain . "." . $this->Tld);
            return true;
        }
        return false;
    }
    public function changeComment($id, $comment = "")
    {
        if(!is_numeric($id)) {
            $this->Error[] = __("invalid identifier for domain");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Domains", ["Comment" => $comment])->where("id", $id)->execute();
        if($result) {
            createLog("domain", $this->Identifier, "comment adjusted");
            $this->Success[] = sprintf(__("domain comment adjusted"), $this->Domain . "." . $this->Tld);
            return true;
        }
        return false;
    }
    public function getID($method, $value, $tld = "")
    {
        switch ($method) {
            case "identifier":
                $domain_id = Database_Model::getInstance()->getOne("HostFact_Domains", "id")->where("id", intval($value))->execute();
                return $domain_id && 0 <= $domain_id->id ? $domain_id->id : false;
                break;
            case "domain":
                $domain_id = Database_Model::getInstance()->getOne("HostFact_Domains", "id")->where("Domain", $value)->where("Tld", $tld)->where("Status", ["!=" => 9])->execute();
                return $domain_id && 0 <= $domain_id->id ? $domain_id->id : false;
                break;
            case "clientarea":
                $or_where = [];
                $or_where[] = ["HostFact_Domains.Status", ["IN" => [-1, 1, 3, 4, 5, 6, 7]]];
                $or_where[] = ["AND" => [["HostFact_Domains.`Status`", 8], ["HostFact_Terminations.Date", [">" => ["RAW" => "CURDATE()"]]]]];
                $domain_id = Database_Model::getInstance()->getOne("HostFact_Domains", ["HostFact_Domains.id"])->join("HostFact_Terminations", "HostFact_Terminations.`ServiceType`='domain' AND HostFact_Terminations.`ServiceID`=HostFact_Domains.`id` AND HostFact_Terminations.`Status` IN ('pending', 'processed')")->where("HostFact_Domains.id", intval($value))->where("HostFact_Domains.Debtor", $tld)->orWhere($or_where)->execute();
                return $domain_id !== false && 0 < $tld ? $domain_id->id : false;
                break;
        }
    }
    public function delete($id, $confirmRegistrar)
    {
        if(!is_numeric($id)) {
            $this->Error[] = __("invalid identifier for domain");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_Domains", ["HostFact_Domains.Domain", "HostFact_Domains.Tld", "HostFact_Domains.Status", "HostFact_Domains.PeriodicID", "HostFact_Domains.HostingID", "HostFact_Domains.Registrar", "HostFact_Domains.DNSTemplate", "HostFact_Registrar.Class"])->join("HostFact_Registrar", "HostFact_Registrar.id = HostFact_Domains.Registrar")->where("HostFact_Domains.id", $id)->execute();
        if($result) {
            $this->Identifier = $id;
            $this->Domain = $result->Domain;
            $this->Tld = $result->Tld;
            $this->Status = $result->Status;
            $this->Registrar = $result->Registrar;
            $this->DNSTemplate = $result->DNSTemplate;
            if($confirmRegistrar == "direct") {
                if($this->Status == 4) {
                    if(!$this->deleteAtRegistrar("now")) {
                        return false;
                    }
                } else {
                    $this->Error[] = sprintf(__("cannot cancel domain at registrar, since it is not active"), $this->Domain . "." . $this->Tld);
                }
            } elseif($confirmRegistrar == "norenew") {
                if($this->Status == 4) {
                    if(!$this->deleteAtRegistrar("end")) {
                        return false;
                    }
                } else {
                    $this->Error[] = sprintf(__("cannot stop autorenew domain at registrar, since it is not active"), $this->Domain . "." . $this->Tld);
                }
            }
            $result = Database_Model::getInstance()->update("HostFact_Domains", ["Status" => 9, "HostingID" => 0])->where("id", $id)->execute();
            if(!$result) {
                return false;
            }
            createLog("domain", $id, "domain deleted");
            $this->Success[] = sprintf(__("domain deleted"), $this->Domain . "." . $this->Tld);
            $result = Database_Model::getInstance()->update("HostFact_PeriodicElements", ["Status" => 9])->where("PeriodicType", "domain")->where("Reference", $id)->execute();
            if(!$result) {
                $this->Error[] = sprintf(__("cannot remove subscription connected to domain"), $this->Domain . "." . $this->Tld);
            }
            $service_info = ["Type" => "domain", "id" => $this->Identifier, "Debtor" => $this->Debtor];
            do_action("service_is_removed", $service_info);
            delete_stats_summary();
            return true;
        }
        $this->Error[] = __("invalid identifier for domain");
        return false;
    }
    public function deleteFromDatabase($id)
    {
        if(!is_numeric($id)) {
            $this->Error[] = __("invalid identifier for domain");
            return false;
        }
        $result = Database_Model::getInstance()->delete("HostFact_Domains")->where("id", $id)->execute();
        if(!$result) {
            return false;
        }
        Database_Model::getInstance()->delete("HostFact_Log")->where("Reference", $id)->where("Type", "domain")->execute();
        Database_Model::getInstance()->delete("HostFact_DomainsPending")->where("DomainID", $id)->execute();
        Database_Model::getInstance()->update("HostFact_PeriodicElements", ["PeriodicType" => "other", "Reference" => 0])->where("PeriodicType", "domain")->where("Reference", $id)->execute();
        return true;
    }
    public function deleteAtRegistrar($delType = "end", $from_termination = false)
    {
        $domain = $this->Domain . "." . $this->Tld;
        if(!$this->getRegistrar() || !is_object($this->api)) {
            $this->Warning[] = sprintf(__("domain not cancelled, no api implementation found"), $this->Domain . "." . $this->Tld);
            $this->Status = 8;
            $result = Database_Model::getInstance()->update("HostFact_Domains", ["Status" => $this->Status])->where("id", $this->Identifier)->execute();
            if($result) {
                $domain_info = ["id" => $this->Identifier, "Type" => $delType, "Debtor" => $this->Debtor, "Domain" => $this->Domain, "Tld" => $this->Tld, "Nameservers" => [$this->DNS1, $this->DNS2, $this->DNS3], "HostingID" => $this->HostingID];
                do_action("domain_is_removed_at_registrar", $domain_info);
                return true;
            }
            return false;
        }
        $otherDomainWithSameFQDN = Database_Model::getInstance()->getOne("HostFact_Domains", "id")->where("Domain", $this->Domain)->where("Tld", $this->Tld)->where("id", ["!=" => $this->Identifier])->where("Status", ["!=" => 9])->execute();
        if($otherDomainWithSameFQDN && 0 < $otherDomainWithSameFQDN->id) {
            $this->Error[] = sprintf(__("domain not cancelled, duplicated domains found"), htmlspecialchars($domain));
            return false;
        }
        if($from_termination === true) {
            $this->api->DeleteFromTermination = true;
        }
        $result = $this->api->deleteDomain(strtolower($domain), $delType);
        if($result === true) {
            $this->Success = array_merge($this->Success, $this->api->Success);
            $this->Warning = array_merge($this->Warning, $this->api->Warning);
            $this->Status = 8;
            Database_Model::getInstance()->update("HostFact_Domains", ["Status" => $this->Status, "DomainAutoRenew" => "off"])->where("id", $this->Identifier)->execute();
            if($delType == "end") {
                createLog("domain", $this->Identifier, "domain deleted at registrar end");
                $this->Success[] = sprintf(__("autorenew domain is cancelled at registrar"), $this->Domain . "." . $this->Tld);
            } else {
                createLog("domain", $this->Identifier, "domain deleted at registrar now");
                $this->Success[] = sprintf(__("domain is cancelled at registrar"), $this->Domain . "." . $this->Tld);
            }
            if(0 < $this->PeriodicID) {
                $termination_date = date("Y-m-d");
                Database_Model::getInstance()->update("HostFact_PeriodicElements", ["TerminationDate" => $termination_date])->where("id", $this->PeriodicID)->where("TerminationDate", "0000-00-00")->execute();
                $this->Success[] = sprintf(__("domain subscription is ended"), $this->Domain . "." . $this->Tld);
            }
            if($delType == "now" && is_module_active("dnsmanagement")) {
                global $_module_instances;
                $dnsmanagement = $_module_instances["dnsmanagement"];
                $dnsmanagement->domain_is_removed($this);
                $this->Error = array_merge($this->Error, $dnsmanagement->Error);
                $this->Warning = array_merge($this->Warning, $dnsmanagement->Warning);
                $this->Success = array_merge($this->Success, $dnsmanagement->Success);
                $_module_instances["dnsmanagement"]->reset();
            }
            $domain_info = ["id" => $this->Identifier, "Type" => $delType, "Debtor" => $this->Debtor, "Domain" => $this->Domain, "Tld" => $this->Tld, "Nameservers" => [$this->DNS1, $this->DNS2, $this->DNS3], "HostingID" => $this->HostingID];
            do_action("domain_is_removed_at_registrar", $domain_info);
            return true;
        }
        if($from_termination === true && isset($this->api->DeleteDirectOnDate) && $this->api->DeleteDirectOnDate) {
            $this->Warning[] = sprintf(__("domain cancellation scheduled, because registrar does not support disabling autorenew"), $this->Domain . "." . $this->Tld);
            createLog("domain", $this->Identifier, "domain cancellation scheduled");
            $result = [];
            $result["ResultType"] = "scheduled";
            $result["Date"] = $this->api->DeleteDirectOnDate;
            $result["ActionType"] = "automatic";
            $result["Description"] = "domain:cancelnow";
            return $result;
        }
        $this->Error[] = sprintf(__("domain cannot be cancelled at registrar"), $this->Domain . "." . $this->Tld);
        $this->Error = array_merge($this->Error, $this->api->Error);
        foreach ($this->api->Error as $e) {
            createLog("domain", $this->Identifier, $e, [], false);
        }
        return false;
    }
    public function validate()
    {
        if(!isset($this->Domain) || !trim($this->Domain)) {
            $this->Error[] = __("no domainname given");
        }
        $this->Domain = trim($this->Domain);
        if(!(is_string($this->Domain) && strlen($this->Domain) <= 255) || preg_match("/^[a-z0-9-]+\$/i", $this->Domain) == 0 && strtolower($this->Tld) == "nl") {
            $this->Error[] = __("invalid characters in .nl domain");
        }
        if(!(is_string($this->Tld) && strlen($this->Tld) <= 63 && 0 < strlen($this->Tld)) || preg_match("/^[^\\\\ \\/@]{2,63}\$/iu", $this->Tld) == 0) {
            $this->Error[] = __("invalid toplevel domain");
        }
        if(!$this->is_free($this->Domain, $this->Tld)) {
            $this->Warning[] = sprintf(__("domain already in software"), $this->Domain . "." . $this->Tld);
        }
        if($this->Debtor && is_numeric($this->Debtor) && 0 < $this->Debtor) {
            $result = Database_Model::getInstance()->getOne("HostFact_Debtors", "id")->where("id", $this->Debtor)->execute();
            if(!$result || $result->id != $this->Debtor) {
                $this->Error[] = __("debtor does not exist");
            }
        } elseif($this->Debtor == "-1" && $this->Status == "-1") {
        } else {
            $this->Error[] = __("no debtor selected");
        }
        if($this->Product && is_numeric($this->Product) && 0 < $this->Product) {
            $result = Database_Model::getInstance()->getOne("HostFact_Products", "id")->where("id", $this->Product)->execute();
            if(!$result || $result->id != $this->Product) {
                $this->Error[] = __("product does not exist");
            }
        }
        if(isset($this->HostingID) && is_numeric($this->HostingID) && 0 < $this->HostingID) {
            $result = Database_Model::getInstance()->getOne("HostFact_Hosting", "id")->where("id", $this->HostingID)->where("Debtor", $this->Debtor)->execute();
            if(!$result || $result->id != $this->HostingID) {
                $this->Error[] = __("invalid identifier for hosting");
            }
        }
        if(isset($this->PeriodicID) && $this->PeriodicID && is_numeric($this->PeriodicID)) {
            $result = Database_Model::getInstance()->getOne("HostFact_PeriodicElements", "id")->where("id", $this->PeriodicID)->where("Debtor", $this->Debtor)->execute();
            if(!$result || $result->id != $this->PeriodicID) {
                $this->Error[] = __("subscription does not exist");
            }
        }
        global $array_domainstatus;
        if(!array_key_exists($this->Status, $array_domainstatus)) {
            $this->Error[] = __("invalid domain status");
        }
        if($this->Status == 4 && (!$this->RegistrationDate || !$this->ExpirationDate)) {
            $this->Warning[] = __("domain registration date not set");
        }
        if(!$this->ownerHandle) {
            $this->Error[] = __("invalid domain owner handle");
        }
        return empty($this->Error) ? true : false;
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
        $DebtorArray = ["DebtorCode", "CompanyName", "SurName", "Initials"];
        $RegistrarArray = ["Name"];
        $SubscriptionArray = ["PriceExcl", "Periods", "Periodic", "TerminationDate", "NextDate", "StartPeriod", "EndPeriod", "ProductCode", "Description", "Number", "TaxPercentage", "AutoRenew", "DiscountPercentage"];
        $TerminationArray = ["TerminationID", "TerminatedDate", "Termination.id", "Termination.Date", "Termination.Created", "Termination.Status"];
        $DebtorFields = 0 < count(array_intersect($DebtorArray, $fields)) ? true : false;
        $RegistrarFields = 0 < count(array_intersect($RegistrarArray, $fields)) ? true : false;
        $SubscriptionFields = 0 < count(array_intersect($SubscriptionArray, $fields)) ? true : false;
        $TerminationFields = 0 < count(array_intersect($TerminationArray, $fields)) ? true : false;
        $search_at = [];
        $DebtorSearch = $RegistrarSearch = $SubscriptionSearch = false;
        if($searchat && $searchfor) {
            $search_at = explode("|", $searchat);
            $DebtorSearch = 0 < count(array_intersect($DebtorArray, $search_at)) ? true : false;
            $RegistrarSearch = 0 < count(array_intersect($RegistrarArray, $search_at)) ? true : false;
            $SubscriptionSearch = 0 < count(array_intersect($SubscriptionArray, $search_at)) ? true : false;
        }
        $select = ["HostFact_Domains.id", "DATE_SUB(HostFact_Domains.`ExpirationDate`,INTERVAL :domwarning DAY) as `WarnDate`"];
        foreach ($fields as $column) {
            if(in_array($column, $DebtorArray)) {
                $select[] = "HostFact_Debtors.`" . $column . "`";
            } elseif(in_array($column, $RegistrarArray)) {
                $select[] = "HostFact_Registrar.`" . $column . "`";
            } elseif(in_array($column, $SubscriptionArray)) {
                if($column == "NextDate") {
                    $select[] = "(SELECT CASE HostFact_Debtors.`InvoiceCollect`\n\t\t\t\t\t\t\t\tWHEN '2' THEN CONCAT(DATE_FORMAT(HostFact_PeriodicElements.`NextDate`, '%Y-%m-'),IF(DAYOFMONTH(HostFact_PeriodicElements.`NextDate`) >= 15, '15', '01'))\n\t\t\t\t\t\t\t\tWHEN '1' THEN CONCAT(DATE_FORMAT(HostFact_PeriodicElements.`NextDate`, '%Y-%m-'),'01')\n\t\t\t\t\t\t\t\tWHEN '-1' THEN " . (INVOICE_COLLECT_ENABLED == "yes" ? "CONCAT(DATE_FORMAT(HostFact_PeriodicElements.`NextDate`, '%Y-%m-'),IF(DAYOFMONTH(HostFact_PeriodicElements.`NextDate`) >= 15, '" . (INVOICE_COLLECT_TPM == 1 ? "01" : "15") . "', '01'))" : "HostFact_PeriodicElements.`NextDate`") . "\n\t\t\t\t\t\t\t\tELSE HostFact_PeriodicElements.`NextDate`\n\t\t\t\t\t\t\t  END) as NextDate";
                } else {
                    $select[] = "HostFact_PeriodicElements.`" . $column . "`";
                }
            } elseif(in_array($column, $TerminationArray)) {
                if($column == "TerminationID") {
                    $select[] = "HostFact_Terminations.`id` as `TerminationID`";
                } elseif($column == "TerminatedDate") {
                    $select[] = "HostFact_Terminations.`Date` as `TerminatedDate`";
                } elseif(strpos($column, "Termination.") !== false) {
                    $select[] = "HostFact_Terminations.`" . str_replace("Termination.", "", $column) . "` as `" . $column . "`";
                }
            } else {
                $select[] = "HostFact_Domains.`" . $column . "`";
            }
        }
        if(0 < count(array_intersect($SubscriptionArray, $fields))) {
            $fields[] = "PeriodicStatus";
            $select[] = "HostFact_PeriodicElements.`Status` AS `PeriodicStatus`";
        }
        if(in_array("PriceExcl", $fields) && !in_array("TaxPercentage", $fields)) {
            $fields[] = "TaxPercentage";
            $select[] = "HostFact_PeriodicElements.`TaxPercentage`";
        }
        if(in_array("PriceExcl", $fields) && !in_array("DiscountPercentage", $fields)) {
            $fields[] = "DiscountPercentage";
            $select[] = "HostFact_PeriodicElements.`DiscountPercentage`";
        }
        Database_Model::getInstance()->get("HostFact_Domains", $select);
        if($DebtorFields || $DebtorSearch || in_array("NextDate", $fields)) {
            Database_Model::getInstance()->join("HostFact_Debtors", "HostFact_Debtors.`id`=HostFact_Domains.`Debtor`");
        }
        if($RegistrarFields || $RegistrarSearch || in_array($group, ["expired", "expiretoday", "expirealmost"])) {
            Database_Model::getInstance()->join("HostFact_Registrar", "HostFact_Registrar.`id`=HostFact_Domains.`Registrar`");
        }
        if($SubscriptionFields || $SubscriptionSearch) {
            Database_Model::getInstance()->join("HostFact_PeriodicElements", "HostFact_PeriodicElements.`id`=HostFact_Domains.`PeriodicID`");
        }
        if($TerminationFields) {
            Database_Model::getInstance()->join("HostFact_Terminations", "HostFact_Terminations.`ServiceType`='domain' AND HostFact_Terminations.`ServiceID`=HostFact_Domains.`id` AND HostFact_Terminations.`Status` IN ('pending', 'processed')");
        }
        if(!empty($search_at)) {
            $or_clausule = [];
            foreach ($search_at as $searchColumn) {
                if(in_array($searchColumn, ["Debtor", "Registrar", "Tld", "HostingID"]) || strpos($searchColumn, "Handle") !== false) {
                    $or_clausule[] = ["HostFact_Domains.`" . $searchColumn . "`", $searchfor];
                } elseif($searchColumn == "Domain") {
                    $or_clausule[] = ["CONCAT(HostFact_Domains.`Domain`,'.', HostFact_Domains.`Tld`)", ["LIKE" => "%" . $searchfor . "%"]];
                } elseif(in_array($searchColumn, $DebtorArray)) {
                    $or_clausule[] = ["HostFact_Debtors.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                } elseif(in_array($searchColumn, $RegistrarArray)) {
                    $or_clausule[] = ["HostFact_Registrar.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                } elseif(in_array($searchColumn, $SubscriptionArray)) {
                    $or_clausule[] = ["HostFact_PeriodicElements.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                } else {
                    $or_clausule[] = ["HostFact_Domains.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                }
            }
            Database_Model::getInstance()->orWhere($or_clausule);
        }
        $order = $sort ? $order ? $order : "DESC" : "";
        if($sort == "Debtor") {
            Database_Model::getInstance()->orderBy("CONCAT(HostFact_Debtors.`CompanyName`, HostFact_Debtors.`SurName`)", $order);
        } elseif($sort == "Registrar") {
            Database_Model::getInstance()->orderBy("HostFact_Registrar.`Name`", $order);
        } elseif(in_array($sort, $DebtorArray)) {
            Database_Model::getInstance()->orderBy("HostFact_Debtors.`" . $sort . "`", $order);
        } elseif(in_array($sort, $RegistrarArray)) {
            Database_Model::getInstance()->orderBy("HostFact_Registrar.`" . $sort . "`", $order);
        } elseif(in_array($sort, $SubscriptionArray)) {
            Database_Model::getInstance()->orderBy("HostFact_PeriodicElements.`" . $sort . "`", $order);
        } elseif($sort) {
            Database_Model::getInstance()->orderBy("HostFact_Domains.`" . $sort . "`", $order);
        }
        if(0 <= $limit) {
            Database_Model::getInstance()->limit((max(1, $limit) - 1) * $show_results, $show_results);
        }
        if($group == "expired" || $group == "expiretoday" || $group == "expirealmost") {
            if($group == "expired") {
                Database_Model::getInstance()->where("HostFact_Domains.Status", 4)->where("HostFact_Domains.ExpirationDate", ["!=" => "0000-00-00"])->where("HostFact_Domains.ExpirationDate", ["<" => ["RAW" => "CURDATE()"]])->orWhere([["HostFact_Domains.DomainAutoRenew", "off"], ["HostFact_Domains.DomainAutoRenew", ""], ["AND" => [["HostFact_Domains.DomainAutoRenew", "on"], ["OR" => [["HostFact_Domains.IsSynced", ["!=" => "yes"]], ["HostFact_Registrar.Class", ""]]]]]]);
            } elseif($group == "expiretoday") {
                Database_Model::getInstance()->where("HostFact_Domains.Status", 4)->where("HostFact_Domains.ExpirationDate", ["!=" => "0000-00-00"])->where("HostFact_Domains.ExpirationDate", ["RAW" => "CURDATE()"])->orWhere([["HostFact_Domains.DomainAutoRenew", "off"], ["HostFact_Domains.DomainAutoRenew", ""], ["AND" => [["HostFact_Domains.DomainAutoRenew", "on"], ["OR" => [["HostFact_Domains.IsSynced", ["!=" => "yes"]], ["HostFact_Registrar.Class", ""]]]]]]);
            } elseif($group == "expirealmost") {
                Database_Model::getInstance()->where("HostFact_Domains.Status", 4)->where("HostFact_Domains.ExpirationDate", ["!=" => "0000-00-00"])->where("HostFact_Domains.ExpirationDate", [">" => ["RAW" => "CURDATE()"]])->where("DATE_SUB(`ExpirationDate`, INTERVAL :domainwarning DAY)", ["<=" => ["RAW" => "CURDATE()"]])->bindValue("domainwarning", DOMAINWARNING)->orWhere([["HostFact_Domains.DomainAutoRenew", "off"], ["HostFact_Domains.DomainAutoRenew", ""], ["AND" => [["HostFact_Domains.DomainAutoRenew", "on"], ["OR" => [["HostFact_Domains.IsSynced", ["!=" => "yes"]], ["HostFact_Registrar.Class", ""]]]]]]);
            }
        } elseif($group == "client_visible") {
            $or_where = [];
            $or_where[] = ["HostFact_Domains.Status", ["IN" => [-1, 1, 3, 4, 5, 6, 7]]];
            if($TerminationFields) {
                $or_where[] = ["AND" => [["HostFact_Domains.`Status`", 8], ["HostFact_Terminations.Date", [">" => ["RAW" => "CURDATE()"]]]]];
            }
            Database_Model::getInstance()->orWhere($or_where);
        } elseif(is_string($group) && 1 < count(explode("|", $group))) {
            $group = explode("|", $group);
            $or_clausule = [];
            foreach ($group as $status) {
                $or_clausule[] = ["HostFact_Domains.`Status`", $status];
            }
            Database_Model::getInstance()->orWhere($or_clausule);
        } elseif(is_numeric($group)) {
            Database_Model::getInstance()->where("HostFact_Domains.`Status`", $group);
        } else {
            Database_Model::getInstance()->where("HostFact_Domains.`Status`", ["!=" => 9]);
        }
        Database_Model::getInstance()->bindValue("domwarning", DOMAINWARNING == "" || !is_numeric(DOMAINWARNING) ? 0 : DOMAINWARNING);
        if(!empty($filters)) {
            foreach ($filters as $_db_field => $_db_value) {
                switch ($_db_field) {
                    case "debtor":
                        if(is_numeric($_db_value) && 0 < $_db_value) {
                            Database_Model::getInstance()->where("HostFact_Domains.Debtor", $_db_value);
                        }
                        break;
                    case "created":
                        if(isset($_db_value["from"]) && $_db_value["from"]) {
                            Database_Model::getInstance()->where("HostFact_Domains.Created", [">=" => $_db_value["from"]]);
                        }
                        if(isset($_db_value["to"]) && $_db_value["to"]) {
                            Database_Model::getInstance()->where("HostFact_Domains.Created", ["<=" => $_db_value["to"]]);
                        }
                        break;
                    case "modified":
                        if(isset($_db_value["from"]) && $_db_value["from"]) {
                            Database_Model::getInstance()->where("HostFact_Domains.Modified", [">=" => $_db_value["from"]]);
                        }
                        if(isset($_db_value["to"]) && $_db_value["to"]) {
                            Database_Model::getInstance()->where("HostFact_Domains.Modified", ["<=" => $_db_value["to"]]);
                        }
                        break;
                }
            }
        }
        $list = [];
        $list["CountRows"] = 0;
        if($domain_list = Database_Model::getInstance()->execute()) {
            $list["CountRows"] = Database_Model::getInstance()->rowCount("HostFact_Domains", "HostFact_Domains.id, DATE_SUB(HostFact_Domains.`ExpirationDate`,INTERVAL :domwarning DAY) as `WarnDate`");
            foreach ($domain_list as $result) {
                $list[$result->id] = ["id" => $result->id];
                foreach (["PriceExcl", "TaxPercentage"] as $_cast_column) {
                    if(isset($result->{$_cast_column})) {
                        $result->{$_cast_column} = (double) $result->{$_cast_column};
                    }
                }
                foreach ($fields as $column) {
                    $list[$result->id][$column] = is_string($result->{$column}) ? htmlspecialchars($result->{$column}) : $result->{$column};
                }
                if(isset($result->PriceExcl)) {
                    $list[$result->id]["PriceIncl"] = VAT_CALC_METHOD == "incl" ? round($result->PriceExcl * (1 + $result->TaxPercentage), 2) : round(round((double) $result->PriceExcl, 2) * (1 + $result->TaxPercentage), 2);
                }
            }
        }
        return $list;
    }
    public function getToken($frombackoffice = false)
    {
        if(!$this->getRegistrar() || !is_object($this->api)) {
            if($frombackoffice) {
                $this->Error[] = __("token could not be retrieved, no api implementation found");
            } else {
                echo "Token could not be retrieved.";
            }
            return false;
        }
        $info = $this->api->getToken(strtolower($this->Domain . "." . $this->Tld));
        if($info === false) {
            if($frombackoffice) {
                $this->Error[] = $this->api->Error[0];
            } else {
                echo "Token could not be retrieved.";
            }
            return false;
        }
        if($info === true) {
            Database_Model::getInstance()->update("HostFact_Domains", ["AuthKey" => ""])->where("id", $this->Identifier)->execute();
            if($frombackoffice) {
                $this->Success = array_merge($this->Success, $this->api->Success);
            } else {
                createLog("domain", $this->Identifier, "token request done from customer panel");
                echo __("token sent to the domain owner");
            }
            if(defined("API_CALL_FROM_CLIENTAREA") && API_CALL_FROM_CLIENTAREA === true) {
                createLog("domain", $this->Identifier, "token request done from customer panel");
            }
            return true;
        }
        Database_Model::getInstance()->update("HostFact_Domains", ["AuthKey" => $info])->where("id", $this->Identifier)->execute();
        if($frombackoffice) {
            $this->Success[] = sprintf(__("token retrieved, value is"), $this->Domain . "." . $this->Tld, $info);
        } else {
            createLog("domain", $this->Identifier, "token request done from customer panel");
            echo $info;
        }
        if(defined("API_CALL_FROM_CLIENTAREA") && API_CALL_FROM_CLIENTAREA === true) {
            createLog("domain", $this->Identifier, "token request done from customer panel");
        }
        return $info;
    }
    public function setToken($authkey)
    {
        if(!is_string($authkey)) {
            $this->Error[] = __("invalid token");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Domains", ["AuthKey" => $authkey])->where("id", $this->Identifier)->execute();
        if($result) {
            $this->AuthKey = $authkey;
            return true;
        }
        return false;
    }
    public function getRegistrar($use_dns = true)
    {
        require_once "class/registrar.php";
        $registrar = new registrar();
        if(!is_numeric($this->Registrar) && $this->Tld) {
            $registrar->search($this->Tld);
            $this->Registrar = $registrar->Identifier;
        } else {
            $registrar->Identifier = $this->Registrar;
            $registrar->show();
        }
        $registrar->Password = passcrypt($registrar->Password);
        $this->RegistrarName = $registrar->Name;
        if($use_dns) {
            if(!$this->DNS3 && !$this->DNS2 && !$this->DNS1) {
                $this->DNS3 = $registrar->DNS3;
            }
            if(!$this->DNS2 && !$this->DNS1) {
                $this->DNS2 = $registrar->DNS2;
            }
            if(!$this->DNS1) {
                $this->DNS1 = $registrar->DNS1;
            }
        }
        $registrarName = $registrar->Class;
        if($registrarName && @file_exists("3rdparty/domain/" . $registrarName . "/" . $registrarName . ".php")) {
            require_once "3rdparty/domain/" . $registrarName . "/" . $registrarName . ".php";
            $this->api = new $registrarName();
            $this->api->User = $registrar->User;
            $this->api->Password = $registrar->Password;
            $this->api->Testmode = $registrar->Testmode;
            $this->api->License = $registrar->License;
            $this->api->Debtor = $this->Debtor;
            $this->api->Setting1 = $registrar->Setting1;
            $this->api->Setting2 = $registrar->Setting2;
            $this->api->Setting3 = $registrar->Setting3;
            if(isset($registrar->DNSTemplate)) {
                $this->api->DNSTemplate = $registrar->DNSTemplate;
            }
            return true;
        }
        if($registrarName) {
            $this->Error[] = __("registrar module is missing some files");
            return false;
        }
        $this->NoRegistrarImplementation = true;
        return false;
    }
    public function is_free($domain, $tld, $return = false)
    {
        $result = Database_Model::getInstance()->getOne("HostFact_Domains", ["id", "Debtor"])->where("Domain", $domain)->where("Tld", $tld)->where("Status", ["NOT IN" => ["-1", "8", "9"]])->orderBy("id", "ASC")->execute();
        if($result && $result->id && $return) {
            return $result->id;
        }
        if($return) {
            return false;
        }
        if($this->Identifier == $result->id) {
            $result = false;
        }
        return !$result ? true : false;
    }
    public function createWhois()
    {
        require_once "class/handle.php";
        require_once "class/whois.php";
        $whois = new Whois();
        $handle_types = ["owner", "admin", "tech"];
        foreach ($handle_types as $handle_type) {
            $handle = new handle();
            if($handle->show($this->{$handle_type . "Handle"})) {
                if($handle->RegistrarHandle) {
                    $whois->__set($handle_type . "RegistrarHandles", [$handle->Class => $handle->RegistrarHandle]);
                }
                $whois->{$handle_type . "Sex"} = trim(htmlspecialchars_decode($handle->Sex));
                $whois->{$handle_type . "Initials"} = trim(htmlspecialchars_decode($handle->Initials));
                $whois->{$handle_type . "SurName"} = trim(htmlspecialchars_decode($handle->SurName));
                $whois->{$handle_type . "CompanyName"} = trim(htmlspecialchars_decode($handle->CompanyName));
                $whois->{$handle_type . "CompanyNumber"} = trim(htmlspecialchars_decode($handle->CompanyNumber));
                $whois->{$handle_type . "CompanyLegalForm"} = trim(htmlspecialchars_decode($handle->LegalForm));
                $whois->{$handle_type . "TaxNumber"} = trim(htmlspecialchars_decode($handle->TaxNumber));
                $whois->{$handle_type . "Address"} = trim(htmlspecialchars_decode($handle->Address));
                $whois->{$handle_type . "Address2"} = trim(htmlspecialchars_decode($handle->Address2));
                $whois->{$handle_type . "ZipCode"} = trim(htmlspecialchars_decode(strtoupper(str_replace(" ", "", $handle->ZipCode))));
                $whois->{$handle_type . "City"} = trim(htmlspecialchars_decode($handle->City));
                $whois->{$handle_type . "State"} = trim(htmlspecialchars_decode($handle->StateName));
                $whois->{$handle_type . "Country"} = trim(htmlspecialchars_decode(str_replace("EU-", "", $handle->Country)));
                $whois->{$handle_type . "PhoneNumber"} = preg_replace("/[^0-9+\\-\\(\\)]/i", "", trim(htmlspecialchars_decode($handle->PhoneNumber)));
                $whois->{$handle_type . "FaxNumber"} = preg_replace("/[^0-9+\\-\\(\\)]/i", "", trim(htmlspecialchars_decode($handle->FaxNumber)));
                $whois->{$handle_type . "EmailAddress"} = trim(htmlspecialchars_decode($handle->EmailAddress));
                $whois->{$handle_type . "InternalHandle"} = trim(htmlspecialchars_decode($handle->Handle));
                $whois->{$handle_type . "RegistrarHandle"} = trim(htmlspecialchars_decode($handle->RegistrarHandle));
                if(isset($handle->customfields_list) && 0 < count($handle->customfields_list)) {
                    $whois->{$handle_type . "custom"} = $handle->custom;
                    $whois->{$handle_type . "customvalues"} = $handle->customvalues;
                }
            }
        }
        return $whois;
    }
    public function setActive($list)
    {
        if(!is_array($list)) {
            $this->Error[] = __("invalid list of identifiers for domains");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Domains", ["Status" => 4])->where("id", ["IN" => $list])->orWhere([["RegistrationDate", ["!=" => "0000-00-00 00:00:00"]], ["ExpirationDate", ["!=" => "0000-00-00"]]])->execute();
        $result2 = Database_Model::getInstance()->update("HostFact_Domains", ["Status" => 4, "RegistrationDate" => ["RAW" => "NOW()"], "ExpirationDate" => ["RAW" => "DATE_ADD(CURDATE(), INTERVAL 1 YEAR)"]])->where("id", ["IN" => $list])->where("RegistrationDate", "0000-00-00 00:00:00")->where("ExpirationDate", "0000-00-00")->execute();
        if($result !== false && $result2 !== false) {
            foreach ($list as $domain_id) {
                createLog("domain", $domain_id, "domain set active");
            }
            $this->Success[] = __("domains are set to active");
            return true;
        } else {
            return false;
        }
    }
    public function check($standalone_check = false)
    {
        if(isset($this->api) && is_object($this->api)) {
        } else {
            if(!$this->getRegistrar()) {
                return false;
            }
            if(!isset($this->api) || !is_object($this->api)) {
                return false;
            }
        }
        if($standalone_check !== true) {
            if($this->Type == "register") {
                $this->api->caller = "register";
            } elseif($this->Type == "transfer") {
                $this->api->caller = "transfer";
            }
        }
        $result = $this->api->checkDomain(strtolower($this->Domain . "." . $this->Tld));
        if($result === true) {
            $this->Type = "register";
        } elseif(empty($this->api->Error) && empty($this->api->Warning)) {
            $this->Type = "transfer";
        } else {
            return false;
        }
    }
    public function publicCheck()
    {
        require_once "class/topleveldomain.php";
        $topleveldomain = new topleveldomain();
        $topleveldomain->showbyTLD($this->Tld);
        $idn = $topleveldomain->getAllowedIDNCharacters($this->Tld);
        if(preg_match("/^[a-z" . $idn . "0-9-]+(\\.[a-z" . $idn . "0-9-]+)*\$/i", $this->Domain) == 0) {
            $this->Type = "invalid";
            return false;
        }
        if(0 < $topleveldomain->Identifier && $topleveldomain->WhoisServer) {
            $ns = @fsockopen($topleveldomain->WhoisServer, 43, $errno, $errstr, 10);
            if(!$ns) {
                return false;
            }
            if($this->Tld == "nl") {
                fputs($ns, "is " . $this->Domain . "." . $this->Tld . "\r\n");
            } else {
                fputs($ns, $this->Domain . "." . $this->Tld . "\r\n");
            }
            stream_set_timeout($ns, 3);
            $result = "";
            while (!feof($ns)) {
                $line = fgets($ns);
                if($line === false) {
                    break;
                }
                $result .= $line;
            }
            fclose($ns);
            if(stripos($result, "Server too busy, try again later") !== false || stripos($result, "maximum number of requests per second exceeded") !== false || stripos($result, "WHOIS LIMIT EXCEEDED") !== false) {
                $this->Error[] = sprintf(__("could not connect to whois server"), $topleveldomain->WhoisServer);
                return false;
            }
            if(0 < preg_match("/" . $topleveldomain->WhoisNoMatch . "/i", $result)) {
                $this->Type = "register";
                return true;
            }
            $this->Type = "transfer";
            return true;
        }
        return false;
    }
    public function register($check = true)
    {
        $whois = $this->createWhois();
        if($this->Status == 4) {
            $this->Error[] = sprintf(__("domain is already active"), $this->Domain . "." . $this->Tld);
            return false;
        }
        if($this->Status == 6) {
            $this->Error[] = sprintf(__("registration is already in progress"), $this->Domain . "." . $this->Tld);
            return false;
        }
        Database_Model::getInstance()->update("HostFact_Domains", ["LastSyncDate" => ["RAW" => "NOW()"], "IsSynced" => "no"])->where("id", $this->Identifier)->execute();
        if(isset($this->api) && is_object($this->api)) {
        } else {
            if(!$this->getRegistrar()) {
                if(isset($this->NoRegistrarImplementation) && $this->NoRegistrarImplementation) {
                    $this->Error[] = __("registrar has no implementation");
                }
                return false;
            }
            if(!isset($this->api) || !is_object($this->api)) {
                return false;
            }
        }
        $domain = $this->Domain . "." . $this->Tld;
        if($check) {
            $this->api->caller = "register";
            $result = $this->api->checkDomain(strtolower($domain));
        }
        if(!$check || $result === true) {
            $ns["ns1"] = $this->DNS1;
            $ns["ns1ip"] = $this->DNS1IP;
            $ns["ns2"] = $this->DNS2;
            $ns["ns2ip"] = $this->DNS2IP;
            $ns["ns3"] = $this->DNS3;
            $ns["ns3ip"] = $this->DNS3IP;
            $whois = $this->createWhois();
            $this->api->ExtraFields = $this->getExtraFields($this->Identifier);
            if(is_module_active("dnsmanagement") && isset($this->DNSTemplate)) {
                global $_module_instances;
                $dnsmanagement = $_module_instances["dnsmanagement"];
                $dns_result = $dnsmanagement->before_register_domain($this->DNSTemplate, $this);
                if($dns_result === true) {
                    if(isset($dnsmanagement->createDNSZoneData)) {
                        $this->createDNSZoneData = $dnsmanagement->createDNSZoneData;
                    }
                } else {
                    if($dns_result === false) {
                        $this->Error = array_merge($this->Error, $dnsmanagement->Error);
                        return false;
                    }
                    $this->api = $dns_result;
                }
                $this->Error = array_merge($this->Error, $dnsmanagement->Error);
                $this->Success = array_merge($this->Success, $dnsmanagement->Success);
                $_module_instances["dnsmanagement"]->reset();
            }
            $result = $this->api->registerDomain(strtolower($domain), $ns, $whois);
            if($result === true) {
                if(!isset($this->api->Period) || $this->api->Period <= 0) {
                    $this->api->Period = 1;
                }
                if(isset($this->api->Pending) && $this->api->Pending === true && isset($this->api->PendingInformation) && is_array($this->api->PendingInformation)) {
                    $this->RegistrationDate = "0000-00-00 00:00:00";
                    $this->ExpirationDate = "0000-00-00";
                    $this->Status = 6;
                    $this->updatePendingInformation();
                    createLog("domain", $this->Identifier, "domain register started");
                    $this->Success[] = sprintf(__("registration domain is started"), $this->Domain . "." . $this->Tld);
                    createMessageLog("success", "registration domain is started", $this->Domain . "." . $this->Tld, "domain", $this->Identifier);
                } else {
                    $this->RegistrationDate = date("Ymd");
                    $this->ExpirationDate = date("Ymd", strtotime("+" . $this->api->Period . " year"));
                    $this->Status = 4;
                    if(is_module_active("dnsmanagement") && isset($this->DNSTemplate) && 0 < $this->DNSTemplate) {
                        global $_module_instances;
                        $dnsmanagement = $_module_instances["dnsmanagement"];
                        $dnsmanagement->domain_is_registered($this);
                        $this->Warning = array_merge($this->Warning, $dnsmanagement->Warning);
                        $this->Error = array_merge($this->Error, $dnsmanagement->Error);
                        $this->Success = array_merge($this->Success, $dnsmanagement->Success);
                    }
                    createLog("domain", $this->Identifier, "domain registered");
                    $this->Success[] = sprintf(__("registration domain is completed"), $this->Domain . "." . $this->Tld);
                    createMessageLog("success", "registration domain is completed", $this->Domain . "." . $this->Tld, "domain", $this->Identifier);
                }
                $this->Type = "register";
                $this->DomainAutoRenew = isset($this->api->DomainAutoRenew) && $this->api->DomainAutoRenew == "off" ? "off" : "on";
                Database_Model::getInstance()->update("HostFact_Domains", ["RegistrationDate" => $this->RegistrationDate, "ExpirationDate" => $this->ExpirationDate, "Status" => $this->Status, "Type" => $this->Type, "IsSynced" => "yes", "DomainAutoRenew" => $this->DomainAutoRenew])->where("id", $this->Identifier)->execute();
                $this->registrarHandles($whois);
                $domain_info = ["id" => $this->Identifier, "Debtor" => $this->Debtor, "Domain" => $this->Domain, "Tld" => $this->Tld, "Nameservers" => [$this->DNS1, $this->DNS2, $this->DNS3]];
                do_action("domain_is_registered", $domain_info);
                if(function_exists("domain_is_registered")) {
                    $domain_info = ["id" => $this->Identifier, "debtor_id" => $this->Debtor, "domain" => $this->Domain, "tld" => $this->Tld, "nameservers" => [$this->DNS1, $this->DNS2, $this->DNS3]];
                    @domain_is_registered($domain_info);
                }
                delete_stats_summary();
                return true;
            }
            $this->Error[] = sprintf(__("registration domain has failed"), $this->Domain . "." . $this->Tld);
            createMessageLog("error", "registration domain has failed", $this->Domain . "." . $this->Tld, "domain", $this->Identifier);
            $this->Error = array_merge($this->Error, $this->api->Error);
            foreach ($this->api->Error as $e) {
                createLog("domain", $this->Identifier, $e, [], false);
            }
            Database_Model::getInstance()->update("HostFact_Domains", ["Status" => 7])->where("id", $this->Identifier)->execute();
            $this->registrarHandles($whois);
            global $account;
            global $company;
            if(CRONJOB_NOTIFY_DOMAIN == "yes" && CRONJOB_NOTIFY_MAILADDRESS && (!isset($account->Identifier) || empty($account->Identifier))) {
                require_once "class/email.php";
                $email = new email();
                $email->Recipient = CRONJOB_NOTIFY_MAILADDRESS;
                $email->Sender = $company->CompanyName . " <" . $company->EmailAddress . ">";
                $email->Subject = sprintf(__("email subject domain registration error"), $this->Domain . "." . $this->Tld);
                $email->Message = file_get_contents("includes/language/" . LANGUAGE_CODE . "/mail.domain.registration.error.html");
                $email->Message = str_replace("[domain]", $this->Domain . "." . $this->Tld, $email->Message);
                $email->Message = str_replace("[registrar]", $this->RegistrarName, $email->Message);
                $email->Message = str_replace("[error]", "&bull; " . implode("<br />&bull; ", $this->api->Error), $email->Message);
                $email->Message = str_replace("[domainurl]", BACKOFFICE_URL . "domains.php?page=show&id=" . $this->Identifier, $email->Message);
                $email->AutoSubmitted = true;
                $email_sent = $email->sent();
            }
            if(is_module_active("dnsmanagement") && isset($this->DNSTemplate) && 0 < $this->DNSTemplate && isset($this->createDNSZoneData)) {
                global $_module_instances;
                $dnsmanagement = $_module_instances["dnsmanagement"];
                $dnsmanagement->domain_register_failed($this, $this->createDNSZoneData);
                $this->Warning = array_merge($this->Warning, $dnsmanagement->Warning);
                $this->Error = array_merge($this->Error, $dnsmanagement->Error);
                $this->Success = array_merge($this->Success, $dnsmanagement->Success);
            }
            return false;
        } else {
            if(!isset($this->api->Error) || empty($this->api->Error)) {
                if(DOMAINAUTOTRANSFER == "true") {
                    return $this->transfer(false);
                }
                $this->Error[] = sprintf(__("registration domain has failed, because it is already active"), $this->Domain . "." . $this->Tld);
                createMessageLog("error", "registration domain has failed, because it is already active", $this->Domain . "." . $this->Tld, "domain", $this->Identifier);
                return false;
            }
            $this->Error = array_merge($this->Error, $this->api->Error);
            return false;
        }
    }
    public function transfer($check = true)
    {
        if(isset($this->TransferAutoBlock) && $this->TransferAutoBlock) {
            $this->Warning[] = sprintf(__("transfer domain has failed, because no automatic transfer should be started"), $this->Domain . "." . $this->Tld);
            return false;
        }
        if($this->Status == 4) {
            $this->Error[] = sprintf(__("domain is already active"), $this->Domain . "." . $this->Tld);
            return false;
        }
        if($this->Status == 6) {
            $this->Error[] = sprintf(__("registration is already in progress"), $this->Domain . "." . $this->Tld);
            return false;
        }
        Database_Model::getInstance()->update("HostFact_Domains", ["LastSyncDate" => ["RAW" => "NOW()"], "IsSynced" => "no"])->where("id", $this->Identifier)->execute();
        if(isset($this->api) && is_object($this->api)) {
        } else {
            if(!$this->getRegistrar()) {
                if(isset($this->NoRegistrarImplementation) && $this->NoRegistrarImplementation) {
                    $this->Error[] = __("registrar has no implementation");
                }
                return false;
            }
            if(!isset($this->api) || !is_object($this->api)) {
                return false;
            }
        }
        $domain = $this->Domain . "." . $this->Tld;
        if($check) {
            $this->api->caller = "transfer";
            $result = $this->api->checkDomain(strtolower($domain));
        }
        if(!$check || $result === false && empty($this->api->Error)) {
            $ns["ns1"] = $this->DNS1;
            $ns["ns1ip"] = $this->DNS1IP;
            $ns["ns2"] = $this->DNS2;
            $ns["ns2ip"] = $this->DNS2IP;
            $ns["ns3"] = $this->DNS3;
            $ns["ns3ip"] = $this->DNS3IP;
            $whois = $this->createWhois();
            $this->api->ExtraFields = $this->getExtraFields($this->Identifier);
            if(is_module_active("dnsmanagement") && isset($this->DNSTemplate)) {
                global $_module_instances;
                $dnsmanagement = $_module_instances["dnsmanagement"];
                $dns_result = $dnsmanagement->before_transfer_domain($this->DNSTemplate, $this);
                if($dns_result === true) {
                    if(isset($dnsmanagement->createDNSZoneData)) {
                        $this->createDNSZoneData = $dnsmanagement->createDNSZoneData;
                    }
                } else {
                    if($dns_result === false) {
                        $this->Error = array_merge($this->Error, $dnsmanagement->Error);
                        return false;
                    }
                    $this->api = $dns_result;
                }
                $this->Error = array_merge($this->Error, $dnsmanagement->Error);
                $this->Success = array_merge($this->Success, $dnsmanagement->Success);
                $_module_instances["dnsmanagement"]->reset();
            }
            $result = $this->api->transferDomain(strtolower($domain), $ns, $whois, trim($this->AuthKey));
            if($result === true) {
                if(!isset($this->api->Period) || $this->api->Period <= 0) {
                    $this->api->Period = 1;
                }
                if(isset($this->api->Pending) && $this->api->Pending === true && isset($this->api->PendingInformation) && is_array($this->api->PendingInformation)) {
                    $this->RegistrationDate = "0000-00-00 00:00:00";
                    $this->ExpirationDate = "0000-00-00";
                    $this->Status = 6;
                    $this->updatePendingInformation();
                    createLog("domain", $this->Identifier, "domain transfer started");
                    $this->Success[] = sprintf(__("transfer domain is started"), $this->Domain . "." . $this->Tld);
                    createMessageLog("success", "transfer domain is started", $this->Domain . "." . $this->Tld, "domain", $this->Identifier);
                } else {
                    $this->RegistrationDate = date("Ymd");
                    $this->ExpirationDate = date("Ymd", strtotime("+" . $this->api->Period . " year"));
                    $this->Status = 4;
                    if(is_module_active("dnsmanagement") && isset($this->DNSTemplate) && 0 < $this->DNSTemplate) {
                        global $_module_instances;
                        $dnsmanagement = $_module_instances["dnsmanagement"];
                        $dnsmanagement->domain_is_transfered($this);
                        $this->Error = array_merge($this->Error, $dnsmanagement->Error);
                        $this->Success = array_merge($this->Success, $dnsmanagement->Success);
                        $_module_instances["dnsmanagement"]->reset();
                    }
                    createLog("domain", $this->Identifier, "domain transfered");
                    $this->Success[] = sprintf(__("transfer domain is completed"), $this->Domain . "." . $this->Tld);
                    createMessageLog("success", "transfer domain is completed", $this->Domain . "." . $this->Tld, "domain", $this->Identifier);
                }
                $this->Type = "transfer";
                $this->DomainAutoRenew = isset($this->api->DomainAutoRenew) && $this->api->DomainAutoRenew == "off" ? "off" : "on";
                Database_Model::getInstance()->update("HostFact_Domains", ["RegistrationDate" => $this->RegistrationDate, "ExpirationDate" => $this->ExpirationDate, "Status" => $this->Status, "Type" => $this->Type, "IsSynced" => "yes", "DomainAutoRenew" => $this->DomainAutoRenew])->where("id", $this->Identifier)->execute();
                $this->registrarHandles($whois);
                $domain_info = ["id" => $this->Identifier, "Debtor" => $this->Debtor, "Domain" => $this->Domain, "Tld" => $this->Tld, "Nameservers" => [$this->DNS1, $this->DNS2, $this->DNS3]];
                do_action("domain_is_transferred", $domain_info);
                if(function_exists("domain_is_transferred")) {
                    $domain_info = ["id" => $this->Identifier, "debtor_id" => $this->Debtor, "domain" => $this->Domain, "tld" => $this->Tld, "nameservers" => [$this->DNS1, $this->DNS2, $this->DNS3]];
                    @domain_is_transferred($domain_info);
                }
                delete_stats_summary();
                return true;
            }
            $this->Error[] = sprintf(__("transfer domain has failed"), $this->Domain . "." . $this->Tld);
            createMessageLog("error", "transfer domain has failed", $this->Domain . "." . $this->Tld, "domain", $this->Identifier);
            $this->Error = array_merge($this->Error, $this->api->Error);
            foreach ($this->api->Error as $e) {
                createLog("domain", $this->Identifier, $e, [], false);
            }
            Database_Model::getInstance()->update("HostFact_Domains", ["Status" => 7])->where("id", $this->Identifier)->execute();
            $this->registrarHandles($whois);
            global $account;
            global $company;
            if(CRONJOB_NOTIFY_DOMAIN == "yes" && CRONJOB_NOTIFY_MAILADDRESS && (!isset($account->Identifier) || empty($account->Identifier))) {
                require_once "class/email.php";
                $email = new email();
                $email->Recipient = CRONJOB_NOTIFY_MAILADDRESS;
                $email->Sender = $company->CompanyName . " <" . $company->EmailAddress . ">";
                $email->Subject = sprintf(__("email subject domain transfer error"), $this->Domain . "." . $this->Tld);
                $email->Message = file_get_contents("includes/language/" . LANGUAGE_CODE . "/mail.domain.transfer.error.html");
                $email->Message = str_replace("[domain]", $this->Domain . "." . $this->Tld, $email->Message);
                $email->Message = str_replace("[registrar]", $this->RegistrarName, $email->Message);
                $email->Message = str_replace("[error]", "&bull; " . implode("<br />&bull; ", $this->api->Error), $email->Message);
                $email->Message = str_replace("[domainurl]", BACKOFFICE_URL . "domains.php?page=show&id=" . $this->Identifier, $email->Message);
                $email->AutoSubmitted = true;
                $email_sent = $email->sent();
            }
            if(is_module_active("dnsmanagement") && isset($this->DNSTemplate) && 0 < $this->DNSTemplate && isset($this->createDNSZoneData)) {
                global $_module_instances;
                $dnsmanagement = $_module_instances["dnsmanagement"];
                $dnsmanagement->domain_transfer_failed($this, $this->createDNSZoneData);
                $this->Warning = array_merge($this->Warning, $dnsmanagement->Warning);
                $this->Error = array_merge($this->Error, $dnsmanagement->Error);
                $this->Success = array_merge($this->Success, $dnsmanagement->Success);
            }
            return false;
        } else {
            if(!isset($this->api->Error) || empty($this->api->Error)) {
                if(DOMAINAUTOREGISTER == "true") {
                    return $this->register(false);
                }
                $this->Error[] = sprintf(__("transfer domain has failed, because domain is free"), $this->Domain . "." . $this->Tld);
                createMessageLog("error", "transfer domain has failed, because domain is free", $this->Domain . "." . $this->Tld, "domain", $this->Identifier);
                return false;
            }
            $this->Error = array_merge($this->Error, $this->api->Error);
            return false;
        }
    }
    public function queue()
    {
        if(!$this->getRegistrar()) {
            return false;
        }
        if(!isset($this->api) || !is_object($this->api)) {
            return false;
        }
        $result = $this->api->getQueue();
        $this->Error = array_merge($this->Error, $this->api->Error);
        $this->Warning = array_merge($this->Warning, $this->api->Warning);
        $this->Success = array_merge($this->Success, $this->api->Success);
        return true;
    }
    public function registrarHandles($whois)
    {
        if(isset($this->api->registrarHandles) && is_array($this->api->registrarHandles)) {
            $handle_types = ["owner", "admin", "tech"];
            foreach ($handle_types as $handle_type) {
                if(isset($this->api->registrarHandles[$handle_type]) && isset($this->{$handle_type . "Handle"}) && 0 < $this->{$handle_type . "Handle"}) {
                    Database_Model::getInstance()->update("HostFact_Handles", ["Registrar" => $this->Registrar, "RegistrarHandle" => $this->api->registrarHandles[$handle_type], "Status" => 1])->where("id", $this->{$handle_type . "Handle"})->execute();
                }
            }
        }
    }
    public function lock($lock = true)
    {
        if(!$this->getRegistrar()) {
            if(isset($this->NoRegistrarImplementation) && $this->NoRegistrarImplementation) {
                $this->Error[] = __("registrar has no implementation");
            }
            return false;
        }
        if(!isset($this->api) || !is_object($this->api)) {
            return false;
        }
        $domain = $this->Domain . "." . $this->Tld;
        $lock = $lock ? $lock : false;
        $result = $this->api->lockDomain(strtolower($domain), $lock);
        if($result === true) {
            if($lock === true) {
                createLog("domain", $this->Identifier, "domain locked");
                $this->Success[] = sprintf(__("domain has been locked"), $domain);
            } else {
                createLog("domain", $this->Identifier, "domain unlocked");
                $this->Success[] = sprintf(__("domain has been unlocked"), $domain);
            }
            return true;
        }
        if($lock === true) {
            $this->Error[] = sprintf(__("domain locking failed"), $domain);
        } else {
            $this->Error[] = sprintf(__("domain unlocking failed"), $domain);
        }
        $this->Error = array_merge($this->Error, $this->api->Error);
        foreach ($this->api->Error as $e) {
            createLog("domain", $this->Identifier, $e, [], false);
        }
        return false;
    }
    public function changeNameserver($updatedb = true)
    {
        $domain = $this->Domain . "." . $this->Tld;
        if(is_module_active("dnsmanagement")) {
            global $_module_instances;
            $dnsmanagement = $_module_instances["dnsmanagement"];
            $manager = $dnsmanagement->get_nameservers_manager($this);
            if(isset($_POST["domain"]["DNSTemplate"])) {
                $this->DNSTemplate = intval(esc($_POST["domain"]["DNSTemplate"]));
                if(in_array($this->Status, [4, 8]) && 0 < $this->DNSTemplate && $this->DNSTemplate != $this->OldDNSTemplate && !isset($_POST["domain"]["DNSTemplateChanged"])) {
                    $this->Error[] = __("dns template changed - please agree");
                    return false;
                }
            } elseif(isset($this->DNSTemplate)) {
                $this->DNSTemplate = intval(esc($this->DNSTemplate));
            } else {
                $this->DNSTemplate = 0;
            }
        }
        $ns_changed = $this->DNS1 != $this->OldDNS1 || $this->DNS2 != $this->OldDNS2 || $this->DNS3 != $this->OldDNS3 ? true : false;
        if(isset($manager) && $manager["Type"] == "registrar" && $manager["IntegrationID"] == $this->Registrar) {
            $ns_changed = true;
        }
        if($this->Status == 4 || $this->Status == 8) {
            if($ns_changed === true && !$this->getRegistrar()) {
                if(isset($this->NoRegistrarImplementation) && $this->NoRegistrarImplementation) {
                    $this->Warning[] = __("registrar has no implementation");
                    $skip_api_action = true;
                } else {
                    return false;
                }
            }
            if(is_module_active("dnsmanagement") && 0 < $this->DNSTemplate && $this->OldDNSTemplate != $this->DNSTemplate) {
                global $_module_instances;
                $dnsmanagement = $_module_instances["dnsmanagement"];
                $dns_result = $dnsmanagement->before_update_domain_ns($this->DNSTemplate, $this);
                if($dns_result === true) {
                    if(isset($dnsmanagement->createDNSZoneData)) {
                        $this->createDNSZoneData = $dnsmanagement->createDNSZoneData;
                    }
                } else {
                    if($dns_result === false) {
                        $this->Error = array_merge($this->Error, $dnsmanagement->Error);
                        return false;
                    }
                    if($dns_result) {
                        $this->api = $dns_result;
                    }
                }
                $this->Error = array_merge($this->Error, $dnsmanagement->Error);
                $this->Success = array_merge($this->Success, $dnsmanagement->Success);
                $_module_instances["dnsmanagement"]->reset();
            }
            $dns_action = false;
            if(isset($skip_api_action) && $skip_api_action === true) {
                $dns_action = "domain_nameservers_changed";
                $updatedb = true;
                if($ns_changed === true) {
                    createLog("domain", $this->Identifier, "nameservers in software adjusted");
                }
            } elseif($ns_changed === true) {
                if(!isset($this->api) || !is_object($this->api)) {
                    $updatedb = true;
                    return false;
                }
                $ns["ns1"] = $this->DNS1;
                $ns["ns1ip"] = $ns["ns1"] ? $this->DNS1IP : "";
                $ns["ns2"] = $this->DNS2;
                $ns["ns2ip"] = $ns["ns2"] ? $this->DNS2IP : "";
                $ns["ns3"] = $this->DNS3;
                $ns["ns3ip"] = $ns["ns3"] ? $this->DNS3IP : "";
                $result = $this->api->updateNameServers(strtolower($domain), $ns);
                if($result === true) {
                    $dns_action = "domain_nameservers_changed";
                    createLog("domain", $this->Identifier, "nameservers updated");
                    $registrar_update_success = true;
                    $updatedb = true;
                } else {
                    $updatedb = false;
                    $this->Error[] = sprintf(__("updating nameservers failed"), $domain);
                    $this->Error = array_merge($this->Error, $this->api->Error);
                    foreach ($this->api->Error as $e) {
                        createLog("domain", $this->Identifier, $e, [], false);
                    }
                    $dns_action = "domain_nameserverchange_failed";
                }
                $this->Warning = array_merge($this->Warning, $this->api->Warning);
            } elseif($ns_changed === false) {
                $dns_action = "domain_nameservers_changed";
                $updatedb = true;
            }
            if($dns_action == "domain_nameservers_changed") {
                if(is_module_active("dnsmanagement") && 0 < $this->DNSTemplate && $this->OldDNSTemplate != $this->DNSTemplate) {
                    global $_module_instances;
                    $dnsmanagement = $_module_instances["dnsmanagement"];
                    $dnsmanagement->domain_nameservers_changed($this);
                    $this->Error = array_merge($this->Error, $dnsmanagement->Error);
                    $this->Success = array_merge($this->Success, $dnsmanagement->Success);
                    $_module_instances["dnsmanagement"]->reset();
                }
            } elseif($dns_action == "domain_nameserverchange_failed" && is_module_active("dnsmanagement") && isset($this->DNSTemplate) && 0 < $this->DNSTemplate && isset($this->createDNSZoneData)) {
                global $_module_instances;
                $dnsmanagement = $_module_instances["dnsmanagement"];
                $dnsmanagement->domain_nameserverchange_failed($this, $this->createDNSZoneData);
                $this->Warning = array_merge($this->Warning, $dnsmanagement->Warning);
                $this->Error = array_merge($this->Error, $dnsmanagement->Error);
                $this->Success = array_merge($this->Success, $dnsmanagement->Success);
            }
        } else {
            $this->Warning[] = sprintf(__("updating nameservers failed, because domain is not active"), $domain);
        }
        if($updatedb) {
            $this->concatNameservers();
            $result = Database_Model::getInstance()->update("HostFact_Domains", ["DNS1" => $this->DNS1, "DNS2" => $this->DNS2, "DNS3" => $this->DNS3])->where("id", $this->id)->execute();
            if($result) {
                if(is_module_active("dnsmanagement")) {
                    global $_module_instances;
                    $dnsmanagement = $_module_instances["dnsmanagement"];
                    $dnsmanagement->update_domain_dns_template($this->id, $this->DNSTemplate);
                    $_module_instances["dnsmanagement"]->reset();
                }
                if($ns_changed === true && isset($registrar_update_success)) {
                    $this->Success[] = sprintf(__("nameservers have been updated"), $domain, $this->RegistrarName);
                } elseif($ns_changed === true) {
                    $this->Success[] = sprintf(__("nameservers in software adjusted"), $domain);
                }
            }
        }
        return true;
    }
    public function concatNameservers()
    {
        $this->DNS1 = isset($this->DNS1IP) && $this->DNS1IP ? $this->DNS1 . ";" . $this->DNS1IP : $this->DNS1;
        $this->DNS2 = isset($this->DNS2IP) && $this->DNS2IP ? $this->DNS2 . ";" . $this->DNS2IP : $this->DNS2;
        $this->DNS3 = isset($this->DNS3IP) && $this->DNS3IP ? $this->DNS3 . ";" . $this->DNS3IP : $this->DNS3;
        return true;
    }
    public function determineNameservers($domain_id)
    {
        if(!is_numeric($domain_id)) {
            $this->Error[] = __("invalid identifier for domain");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_Domains", ["id", "Debtor", "HostingID", "Registrar", "DNS1", "DNS2", "DNS3"])->where("id", $domain_id)->execute();
        if(!$result || $result->id != $domain_id) {
            $this->Error[] = __("invalid identifier for domain");
            return false;
        }
        $result_registrar = Database_Model::getInstance()->getOne("HostFact_Registrar", ["id", "DNS1", "DNS2", "DNS3"])->where("id", $result->Registrar)->execute();
        if($result->DNS1 && $result->DNS1 != $result_registrar->DNS1 && $result->DNS2 != $result_registrar->DNS2 && $result->DNS3 != $result_registrar->DNS3) {
            return true;
        }
        $result_debtor = Database_Model::getInstance()->getOne("HostFact_Debtors", ["id", "DNS1", "DNS2", "DNS3"])->where("id", $result->Debtor)->execute();
        if($result_debtor->DNS1) {
            Database_Model::getInstance()->update("HostFact_Domains", ["DNS1" => $result_debtor->DNS1, "DNS2" => $result_debtor->DNS2, "DNS3" => $result_debtor->DNS3])->where("id", $domain_id)->execute();
            return true;
        }
        if(0 < $result->HostingID) {
            $result_server = Database_Model::getInstance()->getOne(["HostFact_Servers", "HostFact_Hosting"], ["HostFact_Servers.id", "HostFact_Servers.DNS1", "HostFact_Servers.DNS2", "HostFact_Servers.DNS3"])->where("HostFact_Hosting.id", $result->HostingID)->where("HostFact_Hosting.Server = HostFact_Servers.id")->execute();
            if($result_server->DNS1) {
                Database_Model::getInstance()->update("HostFact_Domains", ["DNS1" => $result_server->DNS1, "DNS2" => $result_server->DNS2, "DNS3" => $result_server->DNS3])->where("id", $domain_id)->execute();
                return true;
            }
        }
        return true;
    }
    public function extend($newExpDate = "")
    {
        if(!$this->show()) {
            return false;
        }
        $show_message = false;
        if($newExpDate == "") {
            if($this->ExpirationDate == "") {
                $this->Warning[] = __("domain expirationdate empty", [$this->Domain . "." . $this->Tld]);
                return false;
            }
            $show_message = true;
            $this->ExpirationDate = rewrite_date_site2db($this->ExpirationDate);
            $year = intval(substr($this->ExpirationDate, 0, 4));
            $year = $year + 1;
            $newExpDate = $year . substr($this->ExpirationDate, 4);
            $newExpDate = rewrite_date_db2site($newExpDate);
            $this->ExpirationDate = rewrite_date_db2site($this->ExpirationDate);
            createLog("domain", $this->Identifier, "new expirationdate adjusted", [$this->ExpirationDate, $newExpDate]);
        } else {
            createLog("domain", $this->Identifier, "new invoice-expirationdate adjusted", [$this->ExpirationDate, $newExpDate]);
        }
        $this->ExpirationDate = rewrite_date_site2db($newExpDate);
        $result = Database_Model::getInstance()->update("HostFact_Domains", ["ExpirationDate" => $this->ExpirationDate])->where("id", $this->Identifier)->execute();
        if($result && $show_message === true) {
            $this->Success[] = sprintf(__("domain expirationdate extended"), $this->Domain . "." . $this->Tld);
        }
        return true;
    }
    public function changeDebtor($new_debtor_id)
    {
        if(!$this->show()) {
            return false;
        }
        $old_debtor_id = $this->Debtor;
        require_once "class/handle.php";
        Database_Model::getInstance()->beginTransaction();
        $owner_handle_id = $this->ownerHandle;
        $admin_handle_id = $this->adminHandle;
        $tech_handle_id = $this->techHandle;
        $list_handles_update = [];
        $handles_prefix = ["owner", "admin", "tech"];
        foreach ($handles_prefix as $prefix) {
            if($prefix == "owner" || $prefix == "admin" && $admin_handle_id != $owner_handle_id || $prefix == "tech" && $tech_handle_id != $admin_handle_id) {
                $this->getHandleData($this->{$prefix . "Handle"}, $prefix);
                if(!empty($this->{$prefix . "Debtor"})) {
                    $handle = new handle();
                    $handle->Identifier = $this->{$prefix . "id"};
                    $handle->changeDebtor(0);
                    $list_handles_update[] = $this->{$prefix . "id"};
                }
            }
        }
        unset($handle);
        $handle = new handle();
        foreach ($list_handles_update as $handle_id) {
            $handle->updateGeneralToDebtor($handle_id, $new_debtor_id);
        }
        $result = Database_Model::getInstance()->update("HostFact_Domains", ["Debtor" => $new_debtor_id])->where("id", $this->Identifier)->execute();
        if($result) {
            if(0 < $this->PeriodicID) {
                require_once "class/periodic.php";
                $periodic = new periodic();
                $periodic->Identifier = $this->PeriodicID;
                if(!$periodic->changeDebtor($new_debtor_id)) {
                    Database_Model::getInstance()->rollBack();
                    return false;
                }
            }
            Database_Model::getInstance()->commit();
            require_once "class/debtor.php";
            $old_debtor = new debtor();
            $old_debtor->Identifier = $old_debtor_id;
            $old_debtor->show();
            $old_debtor_name = $old_debtor->DebtorCode . " " . ($old_debtor->CompanyName != "" ? $old_debtor->CompanyName : $old_debtor->SurName . ", " . $old_debtor->Initials);
            $debtor = new debtor();
            $debtor->Identifier = $new_debtor_id;
            $debtor->show();
            $debtor_name = $debtor->DebtorCode . " " . ($debtor->CompanyName != "" ? $debtor->CompanyName : $debtor->SurName . ", " . $debtor->Initials);
            createLog("domain", $this->Identifier, "domain debtor changed", [$old_debtor_name, $debtor_name]);
            $debtor_link = "[hyperlink_1]debtors.php?page=show&id=" . $new_debtor_id . "[hyperlink_2]" . $debtor_name . "[hyperlink_3]";
            $domain_link = "[hyperlink_1]domains.php?page=show&id=" . $this->Identifier . "[hyperlink_2]" . $this->Domain . " " . $this->Tld . "[hyperlink_3]";
            $this->Success[] = sprintf(__("service transfered to new debtor"), $domain_link, $debtor_link);
            return true;
        }
        Database_Model::getInstance()->rollBack();
        return false;
    }
    public function changeRegistrar()
    {
        $result = Database_Model::getInstance()->update("HostFact_Domains", ["Registrar" => $this->Registrar, "ownerHandle" => $this->ownerHandle, "adminHandle" => $this->adminHandle, "techHandle" => $this->techHandle, "RegistrationDate" => $this->RegistrationDate, "ExpirationDate" => $this->ExpirationDate, "Status" => "4", "DNS1" => $this->DNS1, "DNS2" => $this->DNS2, "DNS3" => $this->DNS3, "AuthKey" => $this->AuthKey])->where("id", $this->Identifier)->execute();
        if($result) {
            return true;
        }
        return false;
    }
    public function generateWHOIS($debtor_id)
    {
        require_once "class/registrar.php";
        $registrar = new registrar();
        $registrar->Identifier = $this->Registrar;
        $registrar->show();
        require_once "class/handle.php";
        $handle = new handle();
        $owner_handle = $handle->createHandleFromDebtor($debtor_id, $this->Registrar);
        if(!$owner_handle) {
            $this->Error = array_merge($this->Error, $handle->Error);
            return false;
        }
        $this->ownerHandle = $owner_handle["id"];
        if($registrar->domain_admin_customer == "0") {
            $this->adminHandle = $registrar->domain_admin_handle;
        } else {
            $this->adminHandle = $this->ownerHandle;
        }
        if($registrar->domain_tech_customer == "0") {
            $this->techHandle = $registrar->domain_tech_handle;
        } else {
            $this->techHandle = $this->ownerHandle;
        }
        unset($handle);
        unset($registrar);
    }
    public function getPendingInformation($id)
    {
        $result = Database_Model::getInstance()->getOne("HostFact_DomainsPending")->where("DomainID", $id)->execute();
        if($result && 0 < $result->DomainID) {
            $this->PendingInformation = [];
            $this->PendingInformation["DomainID"] = $result->DomainID;
            $this->PendingInformation["StatusText"] = htmlspecialchars($result->StatusText);
            $this->PendingInformation["StatusCode"] = htmlspecialchars($result->StatusCode);
            $this->PendingInformation["LastDate"] = rewrite_date_db2site($result->LastDate) . " " . __("at") . " " . rewrite_date_db2site($result->LastDate, "%H:%i");
            $this->PendingInformation["NextDate"] = rewrite_date_db2site($result->NextDate) . " " . __("at") . " " . rewrite_date_db2site($result->NextDate, "%H:%i");
        }
        return true;
    }
    public function _checkDomainPending($id)
    {
        $result = Database_Model::getInstance()->getOne("HostFact_DomainsPending", "DomainID")->where("DomainID", $id)->where("NextDate", ["<=" => ["RAW" => "NOW()"]])->execute();
        if($result && 0 < $result->DomainID) {
            return true;
        }
        return false;
    }
    public function doPending($id)
    {
        if(!$this->show($id) || $this->Status != 6) {
            Database_Model::getInstance()->delete("HostFact_DomainsPending")->where("DomainID", $id)->execute();
            return false;
        }
        if(!$this->getRegistrar() || !isset($this->api) || !is_object($this->api)) {
            Database_Model::getInstance()->update("HostFact_Domains", ["Status" => 7])->where("id", $id)->execute();
            Database_Model::getInstance()->delete("HostFact_DomainsPending")->where("DomainID", $id)->execute();
            return false;
        }
        $domain = $this->Domain . "." . $this->Tld;
        $this->api->Period = 1;
        $result = $this->api->doPending(strtolower($domain), $this->PendingInformation);
        if($result === true) {
            $this->RegistrationDate = date("Ymd");
            $this->ExpirationDate = date("Ymd", strtotime("+" . $this->api->Period . " year"));
            $this->Status = 4;
            $this->Authkey = isset($this->api->AuthKey) ? $this->api->AuthKey : $this->Authkey;
            Database_Model::getInstance()->update("HostFact_Domains", ["RegistrationDate" => $this->RegistrationDate, "ExpirationDate" => $this->ExpirationDate, "Status" => $this->Status, "Authkey" => $this->Authkey, "LastSyncDate" => ["RAW" => "NOW()"], "IsSynced" => "yes"])->where("id", $id)->execute();
            if(is_module_active("dnsmanagement") && isset($this->DNSTemplate) && 0 < $this->DNSTemplate) {
                global $_module_instances;
                $dnsmanagement = $_module_instances["dnsmanagement"];
                $dnsmanagement->domain_is_registered($this);
                $this->Error = array_merge($this->Error, $dnsmanagement->Error);
                $this->Success = array_merge($this->Success, $dnsmanagement->Success);
                $_module_instances["dnsmanagement"]->reset();
            }
            foreach ($this->api->Success as $e) {
                createLog("domain", $id, $e, [], false);
            }
        } elseif($result === false) {
            Database_Model::getInstance()->update("HostFact_Domains", ["Status" => 7])->where("id", $id)->execute();
            foreach ($this->api->Error as $e) {
                createLog("domain", $id, $e, [], false);
            }
        } elseif(isset($this->api->Pending) && $this->api->Pending === true && isset($this->api->PendingInformation) && is_array($this->api->PendingInformation)) {
            foreach ($this->api->Success as $e) {
                createLog("domain", $id, $e, [], false);
            }
            $this->updatePendingInformation();
        }
        if($result === true || $result === false) {
            Database_Model::getInstance()->delete("HostFact_DomainsPending")->where("DomainID", $id)->execute();
        } else {
            $result = Database_Model::getInstance()->update("HostFact_DomainsPending", ["LastDate" => ["RAW" => "NextDate"], "NextDate" => ["RAW" => "DATE_ADD(NOW(), INTERVAL 15 MINUTE)"]])->where("DomainID", $id)->execute();
        }
        return true;
    }
    public function updatePendingInformation()
    {
        $result = Database_Model::getInstance()->getOne("HostFact_DomainsPending")->where("DomainID", $this->Identifier)->execute();
        if(0 < $result->DomainID) {
            Database_Model::getInstance()->update("HostFact_DomainsPending", ["Registrar" => $this->Registrar, "StatusCode" => $this->api->PendingInformation["StatusCode"], "StatusText" => $this->api->PendingInformation["StatusText"], "LastDate" => ["RAW" => "NOW()"], "NextDate" => $this->api->PendingInformation["NextDate"]])->where("DomainID", $this->Identifier)->execute();
        } else {
            Database_Model::getInstance()->insert("HostFact_DomainsPending", ["DomainID" => $this->Identifier, "Registrar" => $this->Registrar, "StatusCode" => $this->api->PendingInformation["StatusCode"], "StatusText" => $this->api->PendingInformation["StatusText"], "LastDate" => ["RAW" => "NOW()"], "NextDate" => $this->api->PendingInformation["NextDate"]])->execute();
        }
        return true;
    }
    public function updateWhoisDataToRegistrar()
    {
        if(!$this->getRegistrar()) {
            if(isset($this->NoRegistrarImplementation) && $this->NoRegistrarImplementation) {
                $this->Error[] = __("registrar has no implementation");
            }
            return false;
        }
        if(!isset($this->api) || !is_object($this->api)) {
            return false;
        }
        if($this->ownerHandle <= 0) {
            $this->Error[] = sprintf(__("domain whois cannot be updated, no handle in software"), $this->Domain . "." . $this->Tld);
            return false;
        }
        $whois = $this->createWhois();
        $modified_whois = $this->api->updateDomainWhois(strtolower($this->Domain . "." . $this->Tld), $whois);
        $this->registrarHandles($whois);
        if($modified_whois) {
            if($this->ChargeCosts) {
                $result = Database_Model::getInstance()->getOne("HostFact_TopLevelDomain", ["OwnerChangeCost"])->where("Tld", $this->Tld)->execute();
                if($result->OwnerChangeCost) {
                    require_once "class/product.php";
                    $product = new product();
                    $product->Identifier = $result->OwnerChangeCost;
                    $product->show();
                    $product->ProductKeyPhrase = htmlspecialchars_decode($product->ProductKeyPhrase);
                    $invoice_lines = [];
                    $invoice_lines[] = ["ProductCode" => $product->ProductCode, "Description" => strpos($product->ProductKeyPhrase, "[domain]") !== false ? str_replace("[domain]", $this->Domain . "." . $this->Tld, $product->ProductKeyPhrase) : $product->ProductKeyPhrase . " " . $this->Domain . "." . $this->Tld];
                    require_once "class/invoice.php";
                    $invoice = new invoice();
                    $invoice->quickAdd($this->Debtor, $invoice_lines);
                    $this->Success = array_merge($this->Success, $invoice->Success);
                }
            }
            $this->Success[] = sprintf(__("domain whois updated at registrar"), $this->Domain . "." . $this->Tld);
            return true;
        }
        $this->Error[] = sprintf(__("domain whois not updates at registrar"), $this->Domain . "." . $this->Tld);
        $this->Error = array_merge($this->Error, $this->api->Error);
        return false;
    }
    public function updateContactsInDatabase()
    {
        $result = Database_Model::getInstance()->update("HostFact_Domains", ["ownerHandle" => $this->ownerHandle, "adminHandle" => $this->adminHandle, "techHandle" => $this->techHandle])->where("id", $this->Identifier)->execute();
        if($result) {
            return true;
        }
        return false;
    }
    public function serviceExecuteAction($service_id, $action, $data = false)
    {
        if(!$this->show($service_id)) {
            return false;
        }
        switch ($action) {
            case "cancelnow":
            case "cancelend":
                if(defined("U_DOMAIN_DELETE") && !U_DOMAIN_DELETE) {
                    $this->Error[] = __("invalid user rights to perform action");
                    return false;
                }
                if(!$this->getRegistrar() || !is_object($this->api)) {
                    return "manual";
                }
                break;
            case "editwhois":
            case "changenameserver":
            case "unlock":
                if(defined("U_DOMAIN_EDIT") && !U_DOMAIN_EDIT) {
                    $this->Error[] = __("invalid user rights to perform action");
                    return false;
                }
                if(!$this->getRegistrar() || !is_object($this->api)) {
                    return "manual";
                }
                break;
            case "editdnszone":
                if(defined("U_DOMAIN_EDIT") && !U_DOMAIN_EDIT) {
                    $this->Error[] = __("invalid user rights to perform action");
                    return false;
                }
                break;
            case "cancelnow":
                return $this->deleteAtRegistrar("now");
                break;
            case "cancelend":
                return $this->deleteAtRegistrar("end", true);
                break;
            case "unlock":
                return $this->lock(false);
                break;
            case "editwhois":
                define("API_CALL_FROM_CLIENTAREA", true);
                $whois_data = json_decode($data, true);
                foreach ($whois_data as $_handletype => $_handle) {
                    $whois_data[$_handletype]["State"] = isset($whois_data[$_handletype]["StateCode"]) && $whois_data[$_handletype]["StateCode"] ? $whois_data[$_handletype]["StateCode"] : $whois_data[$_handletype]["State"];
                }
                if($whois_data && is_array($whois_data) && 0 < count($whois_data)) {
                    if(isset($whois_data["Owner"])) {
                        $this->ChargeCosts = true;
                    }
                    return $this->editWhois($whois_data);
                }
                return false;
                break;
            case "editdnszone":
                $dns_zone_data = json_decode($data, true);
                if(is_module_active("dnsmanagement") && !empty($dns_zone_data)) {
                    define("API_CALL_FROM_CLIENTAREA", true);
                    global $_module_instances;
                    $dnsmanagement = $_module_instances["dnsmanagement"];
                    if($dnsmanagement->getOrSaveDNSZone($this->Identifier, $dns_zone_data["Records"]) === true) {
                        $result = true;
                    } else {
                        $result = false;
                    }
                    $this->Warning = array_merge($this->Warning, $dnsmanagement->Warning);
                    $this->Error = array_merge($this->Error, $dnsmanagement->Error);
                    $this->Success = array_merge($this->Success, $dnsmanagement->Success);
                    return $result;
                }
                return false;
                break;
            case "changenameserver":
                $ns_data = json_decode($data, true);
                if(!empty($ns_data)) {
                    define("API_CALL_FROM_CLIENTAREA", true);
                    $this->DNS1 = isset($ns_data["DNS1"]) ? $ns_data["DNS1"] : $this->DNS1;
                    $this->DNS2 = isset($ns_data["DNS2"]) ? $ns_data["DNS2"] : $this->DNS2;
                    $this->DNS3 = isset($ns_data["DNS3"]) ? $ns_data["DNS3"] : $this->DNS3;
                    $this->DNS1IP = isset($ns_data["DNS1IP"]) ? $ns_data["DNS1IP"] : $this->DNS1IP;
                    $this->DNS2IP = isset($ns_data["DNS2IP"]) ? $ns_data["DNS2IP"] : $this->DNS2IP;
                    $this->DNS3IP = isset($ns_data["DNS3IP"]) ? $ns_data["DNS3IP"] : $this->DNS3IP;
                    $this->DNSTemplate = 0;
                    $this->changeNameserver();
                    if(!empty($this->Error)) {
                        return false;
                    }
                    return true;
                }
                return false;
                break;
            default:
                $this->Error[] = __("invalid action to perform");
                return false;
        }
    }
    public function getDomainsToSync()
    {
        $domain_sync_days = defined("DOMAIN_SYNC_DAYS") && 0 < DOMAIN_SYNC_DAYS ? DOMAIN_SYNC_DAYS : 30;
        $domain = Database_Model::getInstance()->getOne(["HostFact_Domains", "HostFact_Registrar"], "HostFact_Domains.Registrar")->where("HostFact_Registrar.Class", ["<>" => ""])->where("HostFact_Domains.Status", 4)->where("HostFact_Domains.Registrar", [">" => 0])->where("HostFact_Registrar.id = HostFact_Domains.Registrar")->orWhere([["HostFact_Domains.LastSyncDate", "0000-00-00 00:00:00"], ["HostFact_Domains.LastSyncDate", ["<" => ["RAW" => "(NOW() - INTERVAL :days DAY)"]]], ["AND" => [["HostFact_Domains.ExpirationDate", ["<" => ["RAW" => "(NOW() - INTERVAL 1 DAY)"]]], ["HostFact_Domains.LastSyncDate", ["<" => ["RAW" => "(NOW() - INTERVAL 1 DAY)"]]]]]])->orderBy("HostFact_Domains.LastSyncDate", "ASC")->bindValue("days", $domain_sync_days)->execute();
        if(!$domain || empty($domain->Registrar)) {
            return [];
        }
        $domains = Database_Model::getInstance()->get("HostFact_Domains", ["id", "Domain", "Tld", "LastSyncDate", "DNS1", "DNS2", "DNS3", "ExpirationDate", "Registrar", "DomainAutoRenew"])->where("Status", 4)->where("Registrar", $domain->Registrar)->orWhere([["LastSyncDate", "0000-00-00 00:00:00"], ["LastSyncDate", ["<" => ["RAW" => "(NOW() - INTERVAL :days DAY)"]]], ["AND" => [["ExpirationDate", ["<" => ["RAW" => "(NOW() - INTERVAL 1 DAY)"]]], ["LastSyncDate", ["<" => ["RAW" => "(NOW() - INTERVAL 1 DAY)"]]]]]])->orderBy("LastSyncDate", "ASC")->bindValue("days", $domain_sync_days)->asArray()->execute();
        return $domains;
    }
    public function syncDomainsByRegistrar($list_domains, $ajax_call = false)
    {
        if(!is_array($list_domains) || empty($list_domains)) {
            $this->Error[] = __("domain sync failed");
            return false;
        }
        if(!array_key_exists(0, $list_domains)) {
            $list_domains_tmp[0] = $list_domains;
            unset($list_domains);
            $list_domains = $list_domains_tmp;
        }
        $this->Registrar = $list_domains[0]["Registrar"];
        if(!$this->getRegistrar() || !is_object($this->api)) {
            $this->moveSyncDate(0, "no", $this->Registrar);
            return false;
        }
        if(!method_exists($this->api, "getSyncData")) {
            $this->moveSyncDate(0, "no", $this->Registrar);
            $this->Error[] = __("registrar required function not found");
            return false;
        }
        $domains_registrar = [];
        $domains_hostfact = [];
        $domains_ids = [];
        foreach ($list_domains as $domain) {
            $domain["Domain"] = trim(strtolower($domain["Domain"]));
            $domain["Tld"] = strtolower($domain["Tld"]);
            $key = $domain["Domain"] . "." . $domain["Tld"];
            $domains_registrar[$key]["Domain"] = $key;
            $domains_hostfact[$key] = $domain;
            $domains_ids[] = $domain["id"];
        }
        unset($list_domains);
        $domains_registrar = $this->api->getSyncData($domains_registrar);
        if(!$domains_registrar) {
            $this->moveSyncDate($domains_ids, "no", $this->Registrar);
            $this->Error = array_merge($this->Error, $this->api->Error);
            return false;
        }
        foreach ($domains_hostfact as $domain_name => $hostfact_domain) {
            if(isset($domains_registrar[$domain_name]["Status"]) && $domains_registrar[$domain_name]["Status"] == "error") {
                $domain_cancelled = false;
                require_once "class/terminationprocedure.php";
                $termination = new Termination_Model();
                if($termination->show("domain", $hostfact_domain["id"]) && $termination->Date <= date("Y-m-d")) {
                    $domain_cancelled = true;
                }
                $periodic_id = Database_Model::getInstance()->getOne("HostFact_Domains", "PeriodicID")->where("id", $hostfact_domain["id"])->execute();
                if(empty($periodic_id->PeriodicID) && $hostfact_domain["DomainAutoRenew"] != "on" && $hostfact_domain["ExpirationDate"] <= date("Y-m-d")) {
                    $domain_cancelled = true;
                } elseif(0 < $periodic_id->PeriodicID) {
                    $periodic = Database_Model::getInstance()->getOne("HostFact_PeriodicElements", ["TerminationDate", "StartPeriod"])->where("id", $periodic_id->PeriodicID)->execute();
                    if($periodic->TerminationDate != "0000-00-00" && ($periodic->TerminationDate < date("Y-m-d") || $periodic->TerminationDate <= $periodic->StartPeriod)) {
                        $domain_cancelled = true;
                    }
                }
                Database_Model::getInstance()->update("HostFact_Domains", ["Status" => $domain_cancelled === true ? 8 : 7])->where("id", $hostfact_domain["id"])->execute();
                if(isset($domains_registrar[$domain_name]["Error_msg"])) {
                    $registrar_error = sprintf(__("registrar domain sync failed"), $this->RegistrarName) . " " . $domains_registrar[$domain_name]["Error_msg"];
                } else {
                    $registrar_error = sprintf(__("registrar domain sync failed"), $this->RegistrarName);
                }
                createLog("domain", $hostfact_domain["id"], $registrar_error, [$domain_name], false);
                $this->Error[] = $domain_name . ": " . $registrar_error;
                $this->moveSyncDate($hostfact_domain["id"], "no");
            } elseif(isset($domains_registrar[$domain_name]["Status"]) && $domains_registrar[$domain_name]["Status"] == "success" && isset($domains_registrar[$domain_name]["Information"])) {
                if(DOMAIN_SYNC_NAMESERVERS == "yes" && isset($domains_registrar[$domain_name]["Information"]["nameservers"])) {
                    $nameservers_tmp = [];
                    $nameservers = [];
                    $nameservers_tmp = explode(";", $hostfact_domain["DNS1"]);
                    $nameservers[] = strtolower($nameservers_tmp[0]);
                    $nameservers_tmp = explode(";", $hostfact_domain["DNS2"]);
                    $nameservers[] = strtolower($nameservers_tmp[0]);
                    $nameservers_tmp = explode(";", $hostfact_domain["DNS3"]);
                    $nameservers[] = strtolower($nameservers_tmp[0]);
                    $domain_nameservers = $domains_registrar[$domain_name]["Information"]["nameservers"];
                    $domain_nameservers[0] = isset($domain_nameservers[0]) ? strtolower($domain_nameservers[0]) : "";
                    $domain_nameservers[1] = isset($domain_nameservers[1]) ? strtolower($domain_nameservers[1]) : "";
                    $domain_nameservers[2] = isset($domain_nameservers[2]) ? strtolower($domain_nameservers[2]) : "";
                    if(3 < count($domain_nameservers)) {
                        foreach ($domain_nameservers as $key => $domain_nameserver) {
                            if(2 < $key) {
                                unset($domain_nameservers[$key]);
                            }
                        }
                    }
                    $ns_diff = array_diff($domain_nameservers, $nameservers);
                    if(!empty($ns_diff)) {
                        Database_Model::getInstance()->update("HostFact_Domains", ["DNS1" => $domain_nameservers[0], "DNS2" => $domain_nameservers[1], "DNS3" => $domain_nameservers[2]])->where("id", $hostfact_domain["id"])->execute();
                        $nameservers_str = trim(implode(", ", $nameservers));
                        $nameservers_str = substr($nameservers_str, -1) == "," ? substr($nameservers_str, 0, -1) : $nameservers_str;
                        $domain_nameservers_str = trim(implode(", ", $domain_nameservers));
                        $domain_nameservers_str = substr($domain_nameservers_str, -1) == "," ? substr($domain_nameservers_str, 0, -1) : $domain_nameservers_str;
                        $log_msg = sprintf(__("log.domain adjusted after registrar sync, nameservers"), $nameservers_str, $domain_nameservers_str);
                        $succes_msg = sprintf(__("domain adjusted after registrar sync, nameservers"), $domain_nameservers_str);
                        if($ajax_call === true) {
                            $refresh_link = "<a href=\"domains.php?page=show&id=" . $hostfact_domain["id"] . "\" class=\"a1 c1\">" . __("reload the page") . "</a>";
                            $succes_msg = $succes_msg . " " . $refresh_link;
                        }
                        createLog("domain", $hostfact_domain["id"], $log_msg, [$domain_name], false);
                        $this->Success[] = $domain_name . ": " . $succes_msg;
                    }
                }
                if(DOMAIN_SYNC_EXPDATE == "yes" && isset($domains_registrar[$domain_name]["Information"]["expiration_date"]) && $domains_registrar[$domain_name]["Information"]["expiration_date"] != "" && $domains_registrar[$domain_name]["Information"]["expiration_date"] != $hostfact_domain["ExpirationDate"]) {
                    Database_Model::getInstance()->update("HostFact_Domains", ["ExpirationDate" => $domains_registrar[$domain_name]["Information"]["expiration_date"]])->where("id", $hostfact_domain["id"])->execute();
                    $log_msg = sprintf(__("log.domain adjusted after registrar sync, expirationdate"), rewrite_date_db2site($hostfact_domain["ExpirationDate"]), rewrite_date_db2site($domains_registrar[$domain_name]["Information"]["expiration_date"]));
                    $succes_msg = sprintf(__("domain adjusted after registrar sync, expirationdate"), rewrite_date_db2site($hostfact_domain["ExpirationDate"]), rewrite_date_db2site($domains_registrar[$domain_name]["Information"]["expiration_date"]));
                    if($ajax_call === true) {
                        $refresh_link = "<a href=\"domains.php?page=show&id=" . $hostfact_domain["id"] . "\" class=\"a1 c1\">" . __("reload the page") . "</a>";
                        $succes_msg = $succes_msg . " " . $refresh_link;
                    }
                    createLog("domain", $hostfact_domain["id"], $log_msg, [$domain_name], false);
                    $this->Success[] = $domain_name . ": " . $succes_msg;
                }
                if(DOMAIN_SYNC_EXPDATE == "yes" && isset($domains_registrar[$domain_name]["Information"]["auto_renew"]) && $domains_registrar[$domain_name]["Information"]["auto_renew"] != "" && $domains_registrar[$domain_name]["Information"]["auto_renew"] != $hostfact_domain["DomainAutoRenew"]) {
                    Database_Model::getInstance()->update("HostFact_Domains", ["DomainAutoRenew" => $domains_registrar[$domain_name]["Information"]["auto_renew"]])->where("id", $hostfact_domain["id"])->execute();
                    $autorenew_status_text = $domains_registrar[$domain_name]["Information"]["auto_renew"] == "on" ? __("turned on") : __("turned off");
                    $log_msg = sprintf(__("log.domain adjusted after registrar sync, auto renew"), $autorenew_status_text);
                    $succes_msg = sprintf(__("domain adjusted after registrar sync, auto renew"), $autorenew_status_text);
                    if($ajax_call === true) {
                        $refresh_link = "<a href=\"domains.php?page=show&id=" . $hostfact_domain["id"] . "\" class=\"a1 c1\">" . __("reload the page") . "</a>";
                        $succes_msg = $succes_msg . " " . $refresh_link;
                    }
                    createLog("domain", $hostfact_domain["id"], $log_msg, [$domain_name], false);
                    $this->Success[] = $domain_name . ": " . $succes_msg;
                }
                Database_Model::getInstance()->update("HostFact_Domains", ["Status" => 4])->where("id", $hostfact_domain["id"])->where("Status", ["IN" => [4, 5, 6, 7]])->execute();
                $this->moveSyncDate($hostfact_domain["id"], "yes");
            }
        }
        if(!empty($this->Error)) {
            return false;
        }
        if(empty($this->Success) && $ajax_call !== true) {
            $this->Success[] = __("domain synced with registrar, no adjustments");
        }
        return true;
    }
    public function moveSyncDate($domain_id, $is_synced, $registrar_id = false)
    {
        $domain_sync_days = defined("DOMAIN_SYNC_DAYS") && 0 < DOMAIN_SYNC_DAYS ? DOMAIN_SYNC_DAYS : 30;
        if($registrar_id && is_numeric($registrar_id) && 0 < $registrar_id) {
            Database_Model::getInstance()->update("HostFact_Domains", ["LastSyncDate" => ["RAW" => "NOW()"], "IsSynced" => $is_synced])->where("Registrar", $registrar_id);
            if(!empty($domain_id)) {
                Database_Model::getInstance()->where("id", ["IN" => $domain_id]);
            }
            Database_Model::getInstance()->execute();
            return true;
        }
        if(is_numeric($domain_id) && 0 < $domain_id) {
            Database_Model::getInstance()->update("HostFact_Domains", ["LastSyncDate" => ["RAW" => "NOW()"], "IsSynced" => $is_synced])->where("id", $domain_id)->execute();
            return true;
        }
        return false;
    }
    public function checkForSync($use_sync_days = true)
    {
        if($use_sync_days === false) {
            $domain = Database_Model::getInstance()->get("HostFact_Domains", ["id", "Domain", "Tld", "LastSyncDate", "DNS1", "DNS2", "DNS3", "ExpirationDate", "Registrar", "DomainAutoRenew"])->where("id", $this->Identifier)->where("Status", ["IN" => [4, 7]])->where("Registrar", [">" => 0])->asArray()->execute();
        } else {
            $domain_sync_days = defined("DOMAIN_SYNC_DAYS") && 0 < DOMAIN_SYNC_DAYS ? DOMAIN_SYNC_DAYS : 30;
            $domain = Database_Model::getInstance()->get("HostFact_Domains", ["id", "Domain", "Tld", "LastSyncDate", "DNS1", "DNS2", "DNS3", "ExpirationDate", "Registrar", "DomainAutoRenew"])->where("id", $this->Identifier)->where("Status", ["IN" => [4, 7]])->where("Registrar", [">" => 0])->orWhere([["LastSyncDate", "0000-00-00 00:00:00"], ["LastSyncDate", ["<" => ["RAW" => "(NOW() - INTERVAL :days DAY)"]]]])->asArray()->bindValue("days", $domain_sync_days)->execute();
        }
        if($domain) {
            return $domain;
        }
        return false;
    }
    public function changeAutoRenew($set_auto_renew_to)
    {
        if($this->Status != 4 && $this->Status != 8) {
            $this->Error[] = __("only active cancelled domain can change autorenew");
            return false;
        }
        $update_array = ["DomainAutoRenew" => $set_auto_renew_to];
        if($this->Status == 8) {
            $update_array["Status"] = 4;
        }
        Database_Model::getInstance()->update("HostFact_Domains", $update_array)->where("id", $this->Identifier)->execute();
        $autorenew_status_text = $set_auto_renew_to == "off" ? __("turned off") : __("turned on");
        createLog("domain", $this->Identifier, sprintf(__("log.auto renew of domain adjusted"), $autorenew_status_text), [$this->Domain . "." . $this->Tld], false);
        if(!$this->getRegistrar() || !is_object($this->api)) {
            $this->Warning[] = sprintf(__("autorenew is adjusted in software, not with registrar"), $autorenew_status_text);
            $this->Warning[] = __("registrar has no implementation");
            return true;
        }
        $domain_name = $this->Domain . "." . $this->Tld;
        $set_auto_renew = $set_auto_renew_to == "on" ? true : false;
        $result = $this->api->setDomainAutoRenew(strtolower($domain_name), $set_auto_renew);
        if($result) {
            $this->Success[] = sprintf(__("autorenew is adjusted in software and with registrar"), $autorenew_status_text);
            return true;
        }
        $this->Warning[] = sprintf(__("autorenew is adjusted in software, not with registrar"), $autorenew_status_text);
        $this->Warning = array_merge($this->Warning, $this->api->Error);
        return true;
    }
    public function editWhois($whois_data)
    {
        $this->showHandles();
        $whois_change_result = false;
        $modifiedOwner = false;
        $list_handle_domains = [];
        $handle_types = ["Owner", "Admin", "Tech"];
        foreach ($handle_types as $_key => $_handle_type) {
            if(empty($whois_data[$_handle_type])) {
            } elseif(empty($this->Handles->{$_handle_type}->Debtor)) {
                $this->Error[] = __("domain " . strtolower($_handle_type) . " handle") . ":  " . __("cannot edit contact because it is general");
            } else {
                if(!isset($list_handle_domains[$_handle_type])) {
                    $list_handle_domains[$_handle_type] = $this->all(["Domain", "Tld"], "Domain", "ASC", "-1", "ownerHandle|adminHandle|techHandle", $this->Handles->{$_handle_type}->Identifier, false, 2);
                }
                $_tmp_handle_types = $handle_types;
                unset($_tmp_handle_types[$_key]);
                sort($_tmp_handle_types);
                if($list_handle_domains[$_handle_type] && 1 < $list_handle_domains[$_handle_type]["CountRows"] || $this->{strtolower($_handle_type) . "Handle"} == $this->{strtolower($_tmp_handle_types[0]) . "Handle"} || $this->{strtolower($_handle_type) . "Handle"} == $this->{strtolower($_tmp_handle_types[1]) . "Handle"}) {
                    $create_new_handle = true;
                } else {
                    require_once "class/handle.php";
                    $handle = new handle();
                    $handle->show($this->{strtolower($_handle_type) . "Handle"});
                    foreach ($handle as $key => $value) {
                        if(is_string($value)) {
                            $handle->{$key} = htmlspecialchars_decode($value);
                        }
                    }
                    foreach ($whois_data[$_handle_type] as $key => $value) {
                        if(in_array($key, $handle->Variables)) {
                            if($_handle_type == "Owner" && isset($this->Handles->Owner->{$key}) && $this->Handles->Owner->{$key} != $value) {
                                $modifiedOwner = true;
                            }
                            $handle->{$key} = esc($value);
                        }
                    }
                    Database_Model::getInstance()->beginTransaction();
                    if($handle->edit($this->{strtolower($_handle_type) . "Handle"})) {
                        if($handle->RegistrarHandle && !$handle->updateWhoisDataToRegistrar()) {
                            $create_new_handle = $handle->Identifier;
                            Database_Model::getInstance()->rollBack();
                        } else {
                            $whois_change_result = true;
                            Database_Model::getInstance()->commit();
                        }
                    } else {
                        $this->Error = array_merge($this->Error, $handle->Error);
                    }
                    unset($handle);
                }
                if(isset($create_new_handle) && $create_new_handle !== false) {
                    if($_handle_type == "Owner") {
                        $modifiedOwner = true;
                    }
                    require_once "class/handle.php";
                    $handle = new handle();
                    $handle->Identifier = $this->Handles->{$_handle_type}->Identifier;
                    $handle->show();
                    foreach ($handle as $key => $value) {
                        if(is_string($value)) {
                            $handle->{$key} = htmlspecialchars_decode($value);
                        }
                    }
                    foreach ($whois_data[$_handle_type] as $key => $value) {
                        if(in_array($key, $handle->Variables)) {
                            $handle->{$key} = esc($value);
                        }
                    }
                    $handle->Registrar = $this->Registrar;
                    $handle->RegistrarHandle = "";
                    $handle->Debtor = $this->Debtor;
                    $tmp_existing_handle = $handle->searchExistingHandle();
                    if(0 < $tmp_existing_handle && ($create_new_handle === true || $create_new_handle != $tmp_existing_handle)) {
                        $whois_change_result = true;
                        $this->{strtolower($_handle_type) . "Handle"} = $tmp_existing_handle;
                        unset($tmp_existing_handle);
                    } else {
                        $handle->Handle = $handle->nextInternalHandle("debtor", $this->Debtor);
                        $result = $handle->add();
                        if($result) {
                            $this->{strtolower($_handle_type) . "Handle"} = $handle->Identifier;
                            $whois_change_result = true;
                        } else {
                            $this->Error[] = __("domain " . strtolower($_handle_type) . " handle not adjusted");
                            $this->Error = array_merge($this->Error, $handle->Error);
                        }
                        unset($handle);
                    }
                }
            }
        }
        $this->updateContactsInDatabase();
        if($whois_change_result) {
            if(!$this->getRegistrar() && isset($this->NoRegistrarImplementation) && $this->NoRegistrarImplementation) {
                $this->Success[] = sprintf(__("domain whois updated in software"), $this->Domain . "." . $this->Tld);
                $this->Warning[] = __("registrar has no implementation");
                return true;
            }
            if($modifiedOwner === false) {
                $this->ChargeCosts = false;
            }
            if($this->updateWhoisDataToRegistrar()) {
                return true;
            }
            $this->Error[] = sprintf(__("domain whois updated in software"), $this->Domain . "." . $this->Tld);
            return false;
        }
        return false;
    }
}

?>