<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class service
{
    public $Debtor;
    public $CompanyName;
    public $Initials;
    public $SurName;
    public $Address;
    public $Address2;
    public $ZipCode;
    public $City;
    public $State;
    public $StateName;
    public $Country;
    public $Taxable;
    public $PeriodicType;
    public $Subscription;
    public $DebtorObject;
    public $RedirectURL;
    public $Error = [];
    public $Warning = [];
    public $Success = [];
    public function __construct()
    {
        $this->Taxable = "auto";
        require_once "class/periodic.php";
        $this->Subscription = new periodic();
        $this->Subscription->FromServicePage = true;
        global $additional_product_types;
        $this->additional_product_types = $additional_product_types;
    }
    public function show($id, $type)
    {
        switch ($type) {
            case "domain":
                require_once "class/domain.php";
                $this->domain = new domain();
                if(!$this->domain->show($id, false)) {
                    return false;
                }
                $this->Debtor = $this->domain->Debtor;
                $this->PeriodicType = "domain";
                if(0 < $this->domain->PeriodicID) {
                    $this->Subscription->show($this->domain->PeriodicID);
                }
                break;
            case "hosting":
                require_once "class/hosting.php";
                $this->hosting = new hosting();
                if(!$this->hosting->show($id, false)) {
                    return false;
                }
                $this->Debtor = $this->hosting->Debtor;
                $this->PeriodicType = "hosting";
                if(0 < $this->hosting->PeriodicID) {
                    $this->Subscription->show($this->hosting->PeriodicID);
                }
                break;
            default:
                if(isset($this->additional_product_types) && is_array($this->additional_product_types) && array_key_exists($type, $this->additional_product_types)) {
                    global $_module_instances;
                    $module = $_module_instances[$type];
                    $subscription_id = $this->Subscription->lookupSubscription($type, $id);
                    if(0 < $subscription_id) {
                        $this->Subscription->show($subscription_id);
                        $debtor_id = $this->Subscription->Debtor;
                    } else {
                        $debtor_id = $module->get_debtor_id($id);
                    }
                    if(!$debtor_id) {
                        return false;
                    }
                    $this->Debtor = $debtor_id;
                    $this->PeriodicType = $type;
                    if(method_exists($module, "service_show")) {
                        $this->{$type} = $module->service_show($id);
                    }
                } else {
                    if(!$this->Subscription->show($id)) {
                        return false;
                    }
                    $this->Debtor = $this->Subscription->Debtor;
                    $this->PeriodicType = $this->Subscription->PeriodicType ? $this->Subscription->PeriodicType : "other";
                }
                $this->show_debtor($this->Debtor);
                return true;
        }
    }
    public function show_debtor($debtor_id)
    {
        require_once "class/debtor.php";
        $debtor = new debtor();
        $debtor->Identifier = $debtor_id;
        if(!$debtor->show() || $debtor->Status == 9 || $debtor->Anonymous == "yes") {
            return false;
        }
        $this->CompanyName = $debtor->CompanyName;
        $this->Initials = $debtor->Initials;
        $this->SurName = $debtor->SurName;
        $this->Address = $debtor->Address;
        $this->Address2 = $debtor->Address2;
        $this->ZipCode = $debtor->ZipCode;
        $this->City = $debtor->City;
        $this->State = $debtor->State;
        $this->StateName = $debtor->StateName;
        $this->Country = $debtor->Country;
        $this->DebtorObject = $debtor;
        if(isset($this->domain) && empty($this->domain->Identifier) && ($debtor->DNS1 || $debtor->DNS2 || $debtor->DNS3)) {
            $this->domain->DNS1 = $debtor->DNS1;
            $this->domain->DNS2 = $debtor->DNS2;
            $this->domain->DNS3 = $debtor->DNS3;
            $this->NameserverFromDebtor = true;
        }
        $this->Taxable = $debtor->Taxable;
        $this->TaxRate1 = $debtor->TaxRate1;
        return true;
    }
    public function add($data)
    {
        $this->Debtor = $data["Debtor"];
        $this->PeriodicType = $data["PeriodicType"];
        require_once "class/debtor.php";
        $debtor = new debtor();
        $debtor->Identifier = $this->Debtor;
        if(!$debtor->show()) {
            $this->Error[] = "Er is geen debiteur gekozen";
            return false;
        }
        $this->DebtorObject = $debtor;
        if(isset($debtor->TaxRate1) && !is_null($debtor->TaxRate1)) {
            $data["subscription"]["TaxPercentage"] = $debtor->TaxRate1;
        }
        $result = false;
        switch ($this->PeriodicType) {
            case "domain":
                $data["domain"]["CreateAnother"] = isset($data["CreateAnother"]) ? $data["CreateAnother"] : "";
                $result = $this->add_domain($data["domain"]);
                break;
            case "hosting":
                $data["hosting"]["CreateAnother"] = isset($data["CreateAnother"]) ? $data["CreateAnother"] : "";
                $result = $this->add_hosting($data["hosting"]);
                break;
            default:
                if(isset($this->additional_product_types) && is_array($this->additional_product_types) && array_key_exists($this->PeriodicType, $this->additional_product_types)) {
                    $post_data_module = [];
                    foreach ($data["module"][$this->PeriodicType] as $key => $value) {
                        $post_data_module[$key] = $value;
                    }
                    $post_data_module["Debtor"] = $this->Debtor;
                    global $_module_instances;
                    $module = $_module_instances[$this->PeriodicType];
                    $result = $module->service_add($post_data_module);
                    $this->Warning = array_merge($this->Warning, $module->Warning);
                    if($result) {
                        $this->Subscription->Reference = $module->Identifier;
                        $this->Subscription->PeriodicType = $this->PeriodicType;
                        if(isset($module->RedirectURL) && $module->RedirectURL) {
                            $this->RedirectURL = $module->RedirectURL;
                            if($data["CreateAnother"] == "yes") {
                                $_SESSION["redirect_after_create"] = "services.php?page=add&type=" . $this->PeriodicType . "&debtor=" . $module->Debtor;
                            }
                        } else {
                            $this->RedirectURL = isset($data["CreateAnother"]) && $data["CreateAnother"] == "yes" ? "services.php?page=add&debtor=" . $this->Debtor . "&type=" . $this->PeriodicType : "modules.php?module=" . $this->PeriodicType . "&page=show&id=" . $module->Identifier;
                        }
                    }
                } else {
                    $result = true;
                    if(isset($data["CreateAnother"]) && $data["CreateAnother"] == "yes") {
                        $this->RedirectURL = "services.php?page=add&debtor=" . $this->Debtor . "&type=other";
                    }
                }
                if($result === true && isset($data["subscription_invoice"]) && $data["subscription_invoice"] == "yes") {
                    if($data["SubscriptionType"] == "existing") {
                        if($this->Subscription->show($data["subscription"]["Existing"])) {
                            $result = true;
                        } else {
                            $result = false;
                        }
                    } elseif($data["SubscriptionType"] == "current") {
                        if($this->Subscription->show($data["subscription"]["Identifier"])) {
                            foreach ($data["subscription"] as $key => $value) {
                                if(in_array($key, $this->Subscription->Variables)) {
                                    $this->Subscription->{$key} = $value;
                                }
                            }
                            $this->Subscription->Debtor = $this->Debtor;
                            if(VAT_CALC_METHOD == "incl" && isset($data["subscription"]["PriceIncl"]) && deformat_money(esc($data["subscription"]["PriceIncl"])) && (esc($data["Taxable"]) == "true" || $data["TaxRate1"] != "" && 0 < $data["TaxRate1"])) {
                                $tax_percentage = $data["TaxRate1"] != "" ? esc($data["TaxRate1"]) : esc($data["subscription"]["TaxPercentage"]);
                                $this->Subscription->PriceExcl = deformat_money(esc($data["subscription"]["PriceIncl"])) / (1 + $tax_percentage);
                            }
                            require_once "class/product.php";
                            $product = new product();
                            $product->show($data["subscription"]["Product"]);
                            $this->Subscription->ProductCode = $product->ProductCode ? htmlspecialchars_decode($product->ProductCode) : "";
                            if(isset($data["ContractPeriod"]) && $data["ContractPeriod"] == "billing") {
                                $this->Subscription->ContractPeriods = $this->Subscription->Periods;
                                $this->Subscription->ContractPeriodic = $this->Subscription->Periodic;
                            }
                            if($this->Subscription->edit()) {
                                $result = true;
                            } else {
                                $result = false;
                            }
                        } else {
                            $result = false;
                        }
                    } else {
                        foreach ($data["subscription"] as $key => $value) {
                            if(in_array($key, $this->Subscription->Variables)) {
                                $this->Subscription->{$key} = $value;
                            }
                        }
                        $this->Subscription->Debtor = $this->Debtor;
                        if(VAT_CALC_METHOD == "incl" && isset($data["subscription"]["PriceIncl"]) && deformat_money(esc($data["subscription"]["PriceIncl"])) && (esc($data["Taxable"]) == "true" || $data["TaxRate1"] != "" && 0 < $data["TaxRate1"])) {
                            $tax_percentage = $data["TaxRate1"] != "" ? esc($data["TaxRate1"]) : esc($data["subscription"]["TaxPercentage"]);
                            $this->Subscription->PriceExcl = deformat_money(esc($data["subscription"]["PriceIncl"])) / (1 + $tax_percentage);
                        }
                        require_once "class/product.php";
                        $product = new product();
                        $product->show($data["subscription"]["Product"]);
                        $this->Subscription->ProductCode = $product->ProductCode ? htmlspecialchars_decode($product->ProductCode) : "";
                        if(isset($data["ContractPeriod"]) && $data["ContractPeriod"] == "billing") {
                            $this->Subscription->ContractPeriods = $this->Subscription->Periods;
                            $this->Subscription->ContractPeriodic = $this->Subscription->Periodic;
                            $this->Subscription->StartContract = $this->Subscription->StartPeriod;
                            $this->Subscription->EndContract = $this->Subscription->EndPeriod;
                        }
                        if($this->Subscription->add()) {
                            $result = true;
                        } else {
                            $result = false;
                        }
                    }
                    if($this->Subscription->Identifier && $result === true) {
                        switch ($this->PeriodicType) {
                            case "domain":
                                $this->Subscription->changeReference($this->Subscription->Identifier, "domain", $this->domain->Identifier);
                                break;
                            case "hosting":
                                $this->Subscription->changeReference($this->Subscription->Identifier, "hosting", $this->hosting->Identifier);
                                break;
                            default:
                                if(isset($this->additional_product_types) && is_array($this->additional_product_types) && array_key_exists($this->PeriodicType, $this->additional_product_types) && isset($module->Identifier) && 0 < $module->Identifier) {
                                    $this->Subscription->changeReference($this->Subscription->Identifier, $this->PeriodicType, $module->Identifier);
                                } else {
                                    $this->Subscription->changeReference($this->Subscription->Identifier, "other", 0);
                                }
                        }
                    }
                }
                if($result === false) {
                    switch ($this->PeriodicType) {
                        case "domain":
                            if(0 < $this->domain->Identifier) {
                                $this->domain->deleteFromDatabase($this->domain->Identifier);
                            }
                            if(isset($this->created_handle) && 0 < $this->created_handle) {
                                require_once "class/handle.php";
                                $handle = new handle();
                                $handle->deleteFromDatabase($this->created_handle);
                                $this->domain->ownerHandle = 0;
                            }
                            if($data["domain"]["adminc"] != "custom") {
                                $this->domain->adminHandle = 0;
                            }
                            if($data["domain"]["techc"] != "custom") {
                                $this->domain->techHandle = 0;
                            }
                            $this->fix_domain();
                            $this->Error = array_merge($this->Error, $this->domain->Error);
                            break;
                        case "hosting":
                            if(0 < $this->hosting->Identifier) {
                                $this->hosting->deleteFromDatabase($this->hosting->Identifier);
                            }
                            $this->fix_hosting();
                            $this->Error = array_merge($this->Error, $this->hosting->Error);
                            break;
                        default:
                            if(isset($this->additional_product_types) && is_array($this->additional_product_types) && array_key_exists($this->PeriodicType, $this->additional_product_types)) {
                                if(isset($module->Identifier) && 0 < $module->Identifier) {
                                    $module->service_undo_add($module->Identifier);
                                }
                                $this->Error = array_merge($this->Error, $module->Error);
                            }
                            if(isset($data["subscription_invoice"]) && $data["subscription_invoice"] == "yes") {
                                if($data["SubscriptionType"] != "existing" && 0 < $this->Subscription->Identifier) {
                                    $this->Subscription->deleteFromDatabase($this->Subscription->Identifier);
                                }
                                foreach ($data["subscription"] as $key => $value) {
                                    $this->Subscription->{$key} = htmlspecialchars($value);
                                }
                                $this->Subscription->DiscountPercentage = (double) $this->Subscription->DiscountPercentage / 100;
                                $this->Error = array_merge($this->Error, $this->Subscription->Error);
                            }
                            $this->CompanyName = $debtor->CompanyName;
                            $this->Initials = $debtor->Initials;
                            $this->SurName = $debtor->SurName;
                            $this->Address = $debtor->Address;
                            $this->Address2 = $debtor->Address2;
                            $this->ZipCode = $debtor->ZipCode;
                            $this->City = $debtor->City;
                            $this->State = $debtor->State;
                            $this->StateName = $debtor->StateName;
                            $this->Country = $debtor->Country;
                            return false;
                    }
                } else {
                    $call_hook_reference_id = 0;
                    switch ($this->PeriodicType) {
                        case "domain":
                            $call_hook_reference_id = $this->domain->Identifier;
                            break;
                        case "hosting":
                            $call_hook_reference_id = $this->hosting->Identifier;
                            break;
                        default:
                            if(isset($this->additional_product_types) && is_array($this->additional_product_types) && array_key_exists($this->PeriodicType, $this->additional_product_types) && isset($module->Identifier) && 0 < $module->Identifier) {
                                $call_hook_reference_id = $module->Identifier;
                            } else {
                                $call_hook_reference_id = $this->Subscription->Identifier;
                            }
                            $service_info = ["Type" => $this->PeriodicType, "id" => $call_hook_reference_id, "Debtor" => $this->Debtor];
                            do_action("service_is_created", $service_info);
                            $this->Success[] = __("service added");
                            return true;
                    }
                }
        }
    }
    public function edit($data)
    {
        switch ($data["PeriodicType"]) {
            case "domain":
                $result = $this->show($data["domain_id"], "domain");
                break;
            case "hosting":
                $result = $this->show($data["hosting_id"], "hosting");
                break;
            default:
                if(isset($this->additional_product_types) && is_array($this->additional_product_types) && array_key_exists($data["PeriodicType"], $this->additional_product_types)) {
                    if(isset($data["module"][$data["PeriodicType"]]["id"]) && 0 < $data["module"][$data["PeriodicType"]]["id"]) {
                        $result = $this->show($data["module"][$data["PeriodicType"]]["id"], $data["PeriodicType"]);
                    } else {
                        $result = false;
                    }
                } else {
                    $result = $this->show($data["subscription"]["Identifier"], $data["PeriodicType"]);
                }
                if(!$result) {
                    return false;
                }
                if($this->Debtor != $data["Debtor"]) {
                    $this->Debtor = $data["Debtor"];
                    if(0 < $this->Subscription->Identifier) {
                        $this->Subscription->changeReference($this->Subscription->Identifier, $this->Subscription->PeriodicType, $this->Subscription->Reference, $this->Debtor);
                    }
                }
                $this->PeriodicType = $data["PeriodicType"];
                $result = false;
                switch ($this->PeriodicType) {
                    case "domain":
                        $this->domain->Identifier = $data["domain_id"];
                        $result = $this->edit_domain($data["domain"]);
                        $this->Error = array_merge($this->Error, $this->domain->Error);
                        break;
                    case "hosting":
                        $this->hosting->Identifier = $data["hosting_id"];
                        $result = $this->edit_hosting($data["hosting"]);
                        $this->Error = array_merge($this->Error, $this->hosting->Error);
                        break;
                    default:
                        if(isset($this->additional_product_types) && is_array($this->additional_product_types) && array_key_exists($this->PeriodicType, $this->additional_product_types)) {
                            $service_id = intval($data["module"][$data["PeriodicType"]]["id"]);
                            $post_data_module = [];
                            foreach ($data["module"][$this->PeriodicType] as $key => $value) {
                                $post_data_module[$key] = $value;
                            }
                            $post_data_module["Debtor"] = $this->Debtor;
                            global $_module_instances;
                            $module = $_module_instances[$this->PeriodicType];
                            $result = $module->service_edit($service_id, $post_data_module);
                            $this->Warning = array_merge($this->Warning, $module->Warning);
                            if($result) {
                                $this->Subscription->Reference = $module->Identifier;
                                $this->Subscription->PeriodicType = $this->PeriodicType;
                                if(isset($module->RedirectURL) && $module->RedirectURL) {
                                    $this->RedirectURL = $module->RedirectURL;
                                } else {
                                    $this->RedirectURL = "modules.php?module=" . $this->PeriodicType . "&page=show&id=" . $module->Identifier;
                                }
                            } elseif(!empty($module->Error)) {
                                $this->Error = array_merge($this->Error, $module->Error);
                            }
                        } else {
                            $result = true;
                        }
                        if($result === true && 0 < $this->Subscription->Identifier && (!isset($data["subscription_invoice"]) || $data["subscription_invoice"] != "yes" || $data["SubscriptionType"] != "current")) {
                            if(isset($data["subscription"]["keep_current"]) && $data["subscription"]["keep_current"] == "keep") {
                                $this->Subscription->changeReference($this->Subscription->Identifier, "other", 0);
                            } else {
                                $this->Subscription->delete($this->Subscription->Identifier);
                            }
                        }
                        if($result === true && isset($data["subscription_invoice"]) && $data["subscription_invoice"] == "yes") {
                            $debtor = new debtor();
                            $debtor->Identifier = $this->Debtor;
                            $debtor->show();
                            if(isset($debtor->TaxRate1) && !is_null($debtor->TaxRate1)) {
                                $data["subscription"]["TaxPercentage"] = $debtor->TaxRate1;
                            }
                            if($data["SubscriptionType"] == "existing") {
                                if($this->Subscription->show($data["subscription"]["Existing"])) {
                                    $result = true;
                                } else {
                                    $result = false;
                                }
                            } elseif($data["SubscriptionType"] == "current") {
                                if($this->Subscription->show($data["subscription"]["Identifier"])) {
                                    foreach ($data["subscription"] as $key => $value) {
                                        if(in_array($key, $this->Subscription->Variables)) {
                                            $this->Subscription->{$key} = $value;
                                        }
                                    }
                                    $this->Subscription->Debtor = $this->Debtor;
                                    if(VAT_CALC_METHOD == "incl" && isset($data["subscription"]["PriceIncl"]) && deformat_money(esc($data["subscription"]["PriceIncl"])) && (esc($data["Taxable"]) == "true" || $data["TaxRate1"] != "" && 0 < $data["TaxRate1"])) {
                                        $tax_percentage = $data["TaxRate1"] != "" ? esc($data["TaxRate1"]) : esc($data["subscription"]["TaxPercentage"]);
                                        $this->Subscription->PriceExcl = deformat_money(esc($data["subscription"]["PriceIncl"])) / (1 + $tax_percentage);
                                    }
                                    require_once "class/product.php";
                                    $product = new product();
                                    $product->show($data["subscription"]["Product"]);
                                    $this->Subscription->ProductCode = $product->ProductCode ? htmlspecialchars_decode($product->ProductCode) : "";
                                    if(isset($data["ContractPeriod"]) && $data["ContractPeriod"] == "billing") {
                                        $this->Subscription->ContractPeriods = $this->Subscription->Periods;
                                        $this->Subscription->ContractPeriodic = $this->Subscription->Periodic;
                                    }
                                    if($this->Subscription->edit()) {
                                        $result = true;
                                    } else {
                                        $result = false;
                                    }
                                } else {
                                    $result = false;
                                }
                            } else {
                                foreach ($data["subscription"] as $key => $value) {
                                    if(in_array($key, $this->Subscription->Variables)) {
                                        $this->Subscription->{$key} = $value;
                                    }
                                }
                                $this->Subscription->Debtor = $this->Debtor;
                                if(VAT_CALC_METHOD == "incl" && isset($data["subscription"]["PriceIncl"]) && deformat_money(esc($data["subscription"]["PriceIncl"])) && (esc($data["Taxable"]) == "true" || $data["TaxRate1"] != "" && 0 < $data["TaxRate1"])) {
                                    $tax_percentage = $data["TaxRate1"] != "" ? esc($data["TaxRate1"]) : esc($data["subscription"]["TaxPercentage"]);
                                    $this->Subscription->PriceExcl = deformat_money(esc($data["subscription"]["PriceIncl"])) / (1 + $tax_percentage);
                                }
                                require_once "class/product.php";
                                $product = new product();
                                $product->show($data["subscription"]["Product"]);
                                $this->Subscription->ProductCode = $product->ProductCode ? htmlspecialchars_decode($product->ProductCode) : "";
                                if(isset($data["ContractPeriod"]) && $data["ContractPeriod"] == "billing") {
                                    $this->Subscription->ContractPeriods = $this->Subscription->Periods;
                                    $this->Subscription->ContractPeriodic = $this->Subscription->Periodic;
                                    $this->Subscription->StartContract = $this->Subscription->StartPeriod;
                                    $this->Subscription->EndContract = $this->Subscription->EndPeriod;
                                }
                                if($this->Subscription->add()) {
                                    $result = true;
                                } else {
                                    $result = false;
                                }
                            }
                            if($this->Subscription->Identifier && $result === true) {
                                switch ($this->PeriodicType) {
                                    case "domain":
                                        $this->Subscription->changeReference($this->Subscription->Identifier, "domain", $this->domain->Identifier);
                                        break;
                                    case "hosting":
                                        $this->Subscription->changeReference($this->Subscription->Identifier, "hosting", $this->hosting->Identifier);
                                        break;
                                    default:
                                        if(isset($this->additional_product_types) && is_array($this->additional_product_types) && array_key_exists($this->PeriodicType, $this->additional_product_types) && isset($module->Identifier) && 0 < $module->Identifier) {
                                            $this->Subscription->changeReference($this->Subscription->Identifier, $this->PeriodicType, $module->Identifier);
                                        } else {
                                            $this->Subscription->changeReference($this->Subscription->Identifier, "other", 0);
                                        }
                                }
                            }
                        }
                        if($result === false) {
                            switch ($this->PeriodicType) {
                                case "domain":
                                    if(!empty($this->created_handles)) {
                                        foreach ($this->created_handles as $handle_id) {
                                            require_once "class/handle.php";
                                            $handle = new handle();
                                            $handle->deleteFromDatabase($handle_id);
                                        }
                                    }
                                    $this->fix_domain();
                                    break;
                                case "hosting":
                                    $this->fix_hosting();
                                    break;
                                default:
                                    if(isset($this->additional_product_types) && is_array($this->additional_product_types) && array_key_exists($this->PeriodicType, $this->additional_product_types)) {
                                        if(isset($module->Identifier) && 0 < $module->Identifier) {
                                            $module->service_undo_edit($module->Identifier);
                                        }
                                    }
                                    if(isset($data["subscription_invoice"]) && $data["subscription_invoice"] == "yes") {
                                        if($data["SubscriptionType"] == "new" && 0 < $this->Subscription->Identifier) {
                                            $this->Subscription->deleteFromDatabase($this->Subscription->Identifier);
                                        }
                                        foreach ($data["subscription"] as $key => $value) {
                                            $this->Subscription->{$key} = htmlspecialchars($value);
                                        }
                                        $this->Subscription->DiscountPercentage = (double) $this->Subscription->DiscountPercentage / 100;
                                        $this->Error = array_merge($this->Error, $this->Subscription->Error);
                                    }
                                    return false;
                            }
                        } else {
                            $call_hook_reference_id = 0;
                            switch ($this->PeriodicType) {
                                case "domain":
                                    $call_hook_reference_id = $this->domain->Identifier;
                                    break;
                                case "hosting":
                                    $call_hook_reference_id = $this->hosting->Identifier;
                                    break;
                                default:
                                    if(isset($this->additional_product_types) && is_array($this->additional_product_types) && array_key_exists($this->PeriodicType, $this->additional_product_types) && isset($module->Identifier) && 0 < $module->Identifier) {
                                        $call_hook_reference_id = $module->Identifier;
                                    } else {
                                        $call_hook_reference_id = $this->Subscription->Identifier;
                                    }
                                    $service_info = ["Type" => $this->PeriodicType, "id" => $call_hook_reference_id, "Debtor" => $this->Debtor];
                                    do_action("service_is_edited", $service_info);
                                    $this->Success = [];
                                    $this->Success[] = __("service adjusted");
                                    return true;
                            }
                        }
                }
        }
    }
    public function add_domain($data)
    {
        require_once "class/domain.php";
        $this->domain = new domain();
        foreach ($data as $key => $value) {
            if(in_array($key, $this->domain->Variables)) {
                $this->domain->{$key} = $value;
            }
        }
        $this->domain->Debtor = $this->Debtor;
        if(isset($data["DirectServerCreation"]) && $data["DirectServerCreation"]) {
            $this->domain->DirectServerCreation = true;
        } else {
            $this->domain->DirectServerCreation = false;
        }
        if($data["ownerc"] == "debtor") {
            require_once "class/handle.php";
            $handle = new handle();
            $owner_handle = $handle->createHandleFromDebtor($this->Debtor, $this->domain->Registrar);
            if(!$owner_handle) {
                $this->domain->Error = array_merge($this->domain->Error, $handle->Error);
                return false;
            }
            $this->domain->ownerHandle = $owner_handle["id"];
            if($owner_handle["created"] === true) {
                $this->created_handle = $owner_handle["id"];
            }
        } elseif($data["ownerc"] == "custom") {
        }
        if($data["adminc"] == "owner") {
            $this->domain->adminHandle = $this->domain->ownerHandle;
        } elseif($data["adminc"] == "handle") {
            $this->domain->adminHandle = $data["adminc_id"];
        } elseif($data["adminc"] == "custom") {
        }
        if($data["techc"] == "owner") {
            $this->domain->techHandle = $this->domain->ownerHandle;
        } elseif($data["techc"] == "handle") {
            $this->domain->techHandle = $data["techc_id"];
        } elseif($data["techc"] == "custom") {
        }
        if($this->domain->add()) {
            $extra_fields = [];
            foreach ($data as $key => $value) {
                if(substr($key, 0, 6) == "Extra_") {
                    $extra_fields[substr($key, 6)] = $value;
                }
            }
            if(!empty($extra_fields)) {
                $this->domain->editExtraFields($this->domain->Identifier, $extra_fields);
            }
            $this->Subscription->Reference = $this->domain->Identifier;
            $this->Subscription->PeriodicType = "domain";
            if(isset($data["DirectCreation"]) && $data["DirectCreation"]) {
                if($data["CreateAnother"] == "yes") {
                    $_SESSION["redirect_after_register"] = "services.php?page=add&debtor=" . $this->Debtor . "&type=domain";
                }
                $this->RedirectURL = "domains.php?page=show&id=" . $this->domain->Identifier . "&action=startregister";
            } elseif(isset($data["CreateAnother"]) && $data["CreateAnother"] == "yes") {
                $this->RedirectURL = "services.php?page=add&debtor=" . $this->Debtor . "&type=domain";
            } else {
                $this->RedirectURL = "domains.php?page=show&id=" . $this->domain->Identifier;
            }
            $this->Warning = array_merge($this->Warning, $this->domain->Warning);
            return true;
        } else {
            return false;
        }
    }
    public function edit_domain($data)
    {
        $old_handles = ["owner" => $this->domain->ownerHandle, "admin" => $this->domain->adminHandle, "tech" => $this->domain->techHandle];
        foreach ($data as $key => $value) {
            if(in_array($key, $this->domain->Variables)) {
                $this->domain->{$key} = $value;
            }
        }
        $this->domain->Debtor = $this->Debtor;
        if(isset($data["DirectServerCreation"]) && $data["DirectServerCreation"]) {
            $this->domain->DirectServerCreation = true;
        } else {
            $this->domain->DirectServerCreation = false;
        }
        if(in_array($this->domain->Status, [4, 8]) && 0 < $this->domain->DNSTemplate && $this->domain->DNSTemplate != $this->domain->OldDNSTemplate && !isset($data["DNSTemplateChanged"])) {
            $this->domain->Error[] = __("dns template changed - please agree");
            return false;
        }
        if($data["ownerc"] == "debtor") {
            require_once "class/handle.php";
            $handle = new handle();
            $owner_handle = $handle->createHandleFromDebtor($this->Debtor, $this->domain->Registrar);
            if(!$owner_handle) {
                $this->domain->Error = array_merge($this->domain->Error, $handle->Error);
                return false;
            }
            $this->domain->ownerHandle = $owner_handle["id"];
            if($owner_handle["created"] === true) {
                $this->created_handle = $owner_handle["id"];
            }
        } elseif($data["ownerc"] == "custom") {
        }
        if($data["adminc"] == "owner") {
            $this->domain->adminHandle = $this->domain->ownerHandle;
        } elseif($data["adminc"] == "handle") {
            $this->domain->adminHandle = $data["adminc_id"];
        } elseif($data["adminc"] == "custom") {
        }
        if($data["techc"] == "owner") {
            $this->domain->techHandle = $this->domain->ownerHandle;
        } elseif($data["techc"] == "handle") {
            $this->domain->techHandle = $data["techc_id"];
        } elseif($data["techc"] == "custom") {
        }
        if($this->domain->edit($this->domain->Identifier)) {
            $this->Success[] = __("domain adjusted, but subscription has errors");
            $extra_fields = [];
            foreach ($data as $key => $value) {
                if(substr($key, 0, 6) == "Extra_") {
                    $extra_fields[substr($key, 6)] = $value;
                }
            }
            if(!empty($extra_fields)) {
                $this->domain->editExtraFields($this->domain->Identifier, $extra_fields);
            }
            $this->Subscription->Reference = $this->domain->Identifier;
            $this->Subscription->PeriodicType = "domain";
            if(isset($data["DirectCreation"]) && $data["DirectCreation"]) {
                $this->RedirectURL = "domains.php?page=show&id=" . $this->domain->Identifier . "&action=startregister";
            } else {
                $this->RedirectURL = "domains.php?page=show&id=" . $this->domain->Identifier;
            }
            if($this->domain->Status == 4 && ($old_handles["owner"] != $this->domain->ownerHandle || $old_handles["admin"] != $this->domain->adminHandle || $old_handles["tech"] != $this->domain->techHandle)) {
                $this->Warning[] = __("handles of active domain are changed, please update at registrar manually");
            }
            $this->Warning = array_merge($this->Warning, $this->domain->Warning);
            return true;
        } else {
            return false;
        }
    }
    public function fix_domain()
    {
        foreach ($this->domain->Variables as $key) {
            $this->domain->{$key} = htmlspecialchars($this->domain->{$key});
        }
        $this->domain->RegistrationDate = rewrite_date_db2site($this->domain->RegistrationDate);
        $this->domain->ExpirationDate = rewrite_date_db2site($this->domain->ExpirationDate);
    }
    public function changeDebtor($new_debtor_id)
    {
        if(!$this->show($this->Identifier, "other")) {
            return false;
        }
        require_once "class/periodic.php";
        $periodic = new periodic();
        $periodic->Identifier = $this->Identifier;
        if($periodic->changeDebtor($new_debtor_id)) {
            require_once "class/debtor.php";
            $debtor = new debtor();
            $debtor->Identifier = $new_debtor_id;
            $debtor->show();
            $debtor_name = $debtor->DebtorCode . " " . ($debtor->CompanyName != "" ? $debtor->CompanyName : $debtor->SurName . ", " . $debtor->Initials);
            $debtor_link = "[hyperlink_1]debtors.php?page=show&id=" . $new_debtor_id . "[hyperlink_2]" . $debtor_name . "[hyperlink_3]";
            $service_link = "[hyperlink_1]services.php?page=show&id=" . $this->Identifier . "[hyperlink_2]" . stripReturnAndSubstring($this->Subscription->Description, 120) . "[hyperlink_3]";
            $this->Success[] = sprintf(__("service transfered to new debtor"), $service_link, $debtor_link);
            return true;
        }
        return false;
    }
    public function add_hosting($data)
    {
        require_once "class/hosting.php";
        $this->hosting = new hosting();
        foreach ($data as $key => $value) {
            if(in_array($key, $this->hosting->Variables)) {
                $this->hosting->{$key} = $value;
            }
        }
        $this->hosting->Debtor = $this->Debtor;
        $this->hosting->Product = 0;
        if($this->hosting->add()) {
            $this->Subscription->Reference = $this->hosting->Identifier;
            $this->Subscription->PeriodicType = "hosting";
            if(isset($data["AddDomain"]) && $data["AddDomain"] == "yes" && isset($data["DirectCreation"]) && $data["DirectCreation"] == "yes") {
                $_SESSION["redirect_after_create"] = "services.php?page=add&from=hosting&from_id=" . $this->hosting->Identifier;
                $this->RedirectURL = "hosting.php?page=show&id=" . $this->hosting->Identifier . "&action=startcreate";
            } elseif(isset($data["AddDomain"]) && $data["AddDomain"] == "yes") {
                require_once "class/topleveldomain.php";
                $topleveldomain = new topleveldomain();
                $tmp_domain = explode(".", $data["Domain"], 2);
                $Tld = isset($tmp_domain[1]) ? $tmp_domain[1] : "";
                if($topleveldomain->showbyTLD($Tld, true) === false) {
                    $this->Error[] = sprintf(__("top level domain doesnt exist in software"), $Tld);
                    $this->RedirectURL = "hosting.php?page=show&id=" . $this->hosting->Identifier;
                } else {
                    $this->RedirectURL = "services.php?page=add&from=hosting&from_id=" . $this->hosting->Identifier;
                }
            } elseif(isset($data["DirectCreation"]) && $data["DirectCreation"] == "yes") {
                if($data["CreateAnother"] == "yes") {
                    $_SESSION["redirect_after_create"] = "services.php?page=add&debtor=" . $this->hosting->Debtor . "&type=hosting";
                }
                $this->RedirectURL = "hosting.php?page=show&id=" . $this->hosting->Identifier . "&action=startcreate";
            } elseif(isset($data["CreateAnother"]) && $data["CreateAnother"] == "yes") {
                $this->RedirectURL = "services.php?page=add&debtor=" . $this->Debtor . "&type=hosting";
            } else {
                $this->RedirectURL = "hosting.php?page=show&id=" . $this->hosting->Identifier;
            }
            return true;
        }
        return false;
    }
    public function edit_hosting($data)
    {
        foreach ($data as $key => $value) {
            if(in_array($key, $this->hosting->Variables)) {
                $this->hosting->{$key} = $value;
            }
        }
        $this->hosting->Debtor = $this->Debtor;
        $this->hosting->Product = 0;
        if($this->hosting->edit($this->hosting->Identifier)) {
            if(($data["Status"] == 4 || $data["Status"] == 5) && $data["changePackageOnServer"] == "yes" && $data["Server"] == $data["oldServer"] && $data["Package"] != $data["oldPackage"] && 0 < $data["Package"]) {
                require_once "class/package.php";
                $package = new package();
                $package->show($data["Package"]);
                if(!$this->hosting->changePackageOnServer($this->hosting->Username, $package->TemplateName)) {
                    $this->Error[] = sprintf(__("package change on server failed"), $this->hosting->Username);
                }
            }
            $this->Success[] = __("hosting adjusted, but subscription has errors");
            $this->Subscription->Reference = $this->hosting->Identifier;
            $this->Subscription->PeriodicType = "hosting";
            if(isset($data["AddDomain"]) && $data["AddDomain"] == "yes" && isset($data["DirectCreation"]) && $data["DirectCreation"] == "yes") {
                $_SESSION["redirect_after_create"] = "services.php?page=add&from=hosting&from_id=" . $this->hosting->Identifier;
                $this->RedirectURL = "hosting.php?page=show&id=" . $this->hosting->Identifier . "&action=startcreate";
            } elseif(isset($data["AddDomain"]) && $data["AddDomain"] == "yes") {
                require_once "class/topleveldomain.php";
                $topleveldomain = new topleveldomain();
                $tmp_domain = explode(".", $data["Domain"], 2);
                $Tld = isset($tmp_domain[1]) ? $tmp_domain[1] : "";
                if($topleveldomain->showbyTLD($Tld, true) === false) {
                    $this->Error[] = sprintf(__("top level domain doesnt exist in software"), $Tld);
                    $this->RedirectURL = "hosting.php?page=show&id=" . $this->hosting->Identifier;
                } else {
                    $this->RedirectURL = "services.php?page=add&from=hosting&from_id=" . $this->hosting->Identifier;
                }
            } elseif(isset($data["DirectCreation"]) && $data["DirectCreation"] == "yes") {
                $this->RedirectURL = "hosting.php?page=show&id=" . $this->hosting->Identifier . "&action=startcreate";
            } else {
                $this->RedirectURL = "hosting.php?page=show&id=" . $this->hosting->Identifier;
            }
            return true;
        }
        return false;
    }
    public function fix_hosting()
    {
        foreach ($this->hosting->Variables as $key) {
            $this->hosting->{$key} = htmlspecialchars($this->hosting->{$key});
        }
        $this->hosting->Password = passcrypt($this->hosting->Password);
    }
}

?>