<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class group
{
    public $Identifier;
    public $Type;
    public $Status;
    public $GroupName;
    public $Products;
    public $Debtors;
    public $Creditors;
    public $CountRows;
    public $Error;
    public $Success;
    public $Warning;
    public $Variables = ["Identifier", "Type", "Status", "GroupName"];
    public function __construct($type = "product")
    {
        $this->Status = "1";
        $this->Type = $type;
        $this->Products = [];
        $this->Debtors = [];
        $this->Creditors = [];
        $this->Error = [];
        $this->Success = [];
        $this->Warning = [];
    }
    public function show()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for group");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_Group")->where("id", $this->Identifier)->execute();
        if(!isset($result->id) || empty($result->id)) {
            $this->Error[] = __("invalid identifier for group");
            return false;
        }
        foreach ($result as $key => $value) {
            $this->{$key} = htmlspecialchars($value);
        }
        switch ($this->Type) {
            case "product":
                $result = Database_Model::getInstance()->get(["HostFact_Products", "HostFact_GroupRelations"], ["HostFact_Products.id"])->where("HostFact_GroupRelations.Reference = HostFact_Products.id")->where("HostFact_GroupRelations.Type", "product")->where("HostFact_GroupRelations.Group", $this->Identifier)->where("HostFact_Products.Status", ["!=" => 9])->execute();
                foreach ($result as $product) {
                    $this->Products[] = $product->id;
                }
                break;
            case "debtor":
                $result = Database_Model::getInstance()->get(["HostFact_Debtors", "HostFact_GroupRelations"], ["HostFact_Debtors.id"])->where("HostFact_GroupRelations.Reference = HostFact_Debtors.id")->where("HostFact_GroupRelations.Type", "debtor")->where("HostFact_GroupRelations.Group", $this->Identifier)->where("HostFact_Debtors.Status", ["!=" => 9])->execute();
                foreach ($result as $debtor) {
                    $this->Debtors[] = $debtor->id;
                }
                break;
            case "creditor":
                $result = Database_Model::getInstance()->get(["HostFact_Creditors", "HostFact_GroupRelations"], ["HostFact_Creditors.id"])->where("HostFact_GroupRelations.Reference = HostFact_Creditors.id")->where("HostFact_GroupRelations.Type", "creditor")->where("HostFact_GroupRelations.Group", $this->Identifier)->where("HostFact_Creditors.Status", ["!=" => 9])->execute();
                foreach ($result as $creditor) {
                    $this->Creditors[] = $creditor->id;
                }
                break;
            default:
                return true;
        }
    }
    public function add()
    {
        if(!$this->validate()) {
            return false;
        }
        $result = Database_Model::getInstance()->insert("HostFact_Group", ["Type" => $this->Type, "Status" => $this->Status, "GroupName" => $this->GroupName])->execute();
        if($result) {
            $this->Identifier = $result;
            foreach ($this->Products as $value) {
                $result = Database_Model::getInstance()->insert("HostFact_GroupRelations", ["Group" => $this->Identifier, "Type" => $this->Type, "Reference" => $value])->execute();
                if(!$result) {
                    return false;
                }
            }
            $this->Success[] = sprintf(__($this->Type . "group is created"), $this->GroupName);
            return true;
        } else {
            return false;
        }
    }
    public function edit()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for group");
            return false;
        }
        if(!$this->validate()) {
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Group", ["Type" => $this->Type, "Status" => $this->Status, "GroupName" => $this->GroupName])->where("id", $this->Identifier)->execute();
        if($result) {
            $new_elements = [];
            foreach ($this->Products as $key => $value) {
                $new_elements[] = is_numeric($value) ? $value : $key;
            }
            switch ($this->Type) {
                case "product":
                    Database_Model::getInstance()->delete("HostFact_GroupRelations")->where("Group", $this->Identifier);
                    if(!empty($new_elements)) {
                        Database_Model::getInstance()->where("Reference", ["NOT IN" => $new_elements]);
                    }
                    Database_Model::getInstance()->where("Reference", ["NOT IN" => ["RAW" => "(SELECT `id` FROM `HostFact_Products` WHERE `Status`=9)"]])->execute();
                    break;
                case "debtor":
                case "creditor":
                    Database_Model::getInstance()->delete("HostFact_GroupRelations")->where("Group", $this->Identifier);
                    if(!empty($new_elements)) {
                        Database_Model::getInstance()->where("Reference", ["NOT IN" => $new_elements]);
                    }
                    Database_Model::getInstance()->execute();
                    break;
                default:
                    $result_gr = Database_Model::getInstance()->get("HostFact_GroupRelations", ["Reference"])->where("Group", $this->Identifier)->execute();
                    $old_elements = [];
                    foreach ($result_gr as $result_tmp) {
                        $old_elements[] = $result_tmp->Reference;
                    }
                    $insert_elements = array_diff($new_elements, $old_elements);
                    foreach ($insert_elements as $element_id) {
                        if(0 < $element_id) {
                            Database_Model::getInstance()->insert("HostFact_GroupRelations", ["Group" => $this->Identifier, "Type" => $this->Type, "Reference" => $element_id])->execute();
                        }
                    }
                    $this->Success[] = sprintf(__($this->Type . "group is adjusted"), $this->GroupName);
                    return true;
            }
        } else {
            return false;
        }
    }
    public function delete()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for group");
            return false;
        }
        if(!$this->show()) {
            return false;
        }
        $result = Database_Model::getInstance()->delete("HostFact_GroupRelations")->where("Group", $this->Identifier)->where("Type", $this->Type)->execute();
        if($result) {
            $result = Database_Model::getInstance()->delete("HostFact_Group")->where("id", $this->Identifier)->execute();
        }
        if($result) {
            $this->Success[] = sprintf(__($this->Type . "group is removed"), $this->GroupName);
            return true;
        }
        return false;
    }
    public function validate()
    {
        if(!empty($this->Creditors) && empty($this->Products)) {
            $this->Products = $this->Creditors;
        }
        if(!empty($this->Debtors) && empty($this->Products)) {
            $this->Products = $this->Debtors;
        }
        if(!is_numeric($this->Status)) {
            $this->Error[] = sprintf(__("invalid status for group"), $this->GroupName);
        }
        if(!($this->Type == "product" || $this->Type == "debtor" || $this->Type == "creditor")) {
            $this->Error[] = sprintf(__("invalid type for group"), $this->GroupName);
        }
        if(!(is_string($this->GroupName) && strlen(trim($this->GroupName)) <= 100 && 0 < strlen(trim($this->GroupName)))) {
            $this->Error[] = sprintf(__("invalid name for group"), $this->GroupName);
        } elseif(!$this->is_free($this->GroupName)) {
            $this->Error[] = sprintf(__("groupname already in use"), $this->GroupName);
        }
        if(!empty($this->Error)) {
            return false;
        }
        return true;
    }
    public function is_free($groupname)
    {
        if($groupname) {
            $result = Database_Model::getInstance()->get("HostFact_Group", ["id"])->where("GroupName", $groupname)->where("Type", $this->Type)->execute();
            if($result[0]->id == $this->Identifier) {
                return true;
            }
            return empty($result) ? true : false;
        }
        return false;
    }
    public function getID($method, $value)
    {
        switch ($method) {
            case "identifier":
                $group = Database_Model::getInstance()->getOne("HostFact_Group", ["id"])->where("id", intval($value))->execute();
                return $group !== false ? $group->id : false;
                break;
        }
    }
    public function all($fields, $sort = "GroupName", $order = "ASC")
    {
        if(!is_array($fields) || empty($fields)) {
            $this->Error[] = __("no fields given for retrieving data");
            return false;
        }
        $sql_fields = ["id"];
        foreach ($fields as $column) {
            if(in_array($column, $this->Variables)) {
                $sql_fields[] = $column;
            }
        }
        Database_Model::getInstance()->get("HostFact_Group", $sql_fields)->where("Type", $this->Type)->where("Status", ["!=" => "9"]);
        if($sort) {
            Database_Model::getInstance()->orderBy($sort, $order ? $order : "ASC");
        }
        $list = [];
        $list["CountRows"] = 0;
        if($result = Database_Model::getInstance()->execute()) {
            $list["CountRows"] = $this->CountRows = Database_Model::getInstance()->rowCount("HostFact_Group", "id");
            foreach ($result as $_group) {
                $list[$_group->id] = ["id" => $_group->id];
                foreach ($fields as $column) {
                    switch ($column) {
                        case "Products":
                            $list[$_group->id][$column] = [];
                            $result2 = Database_Model::getInstance()->get(["HostFact_Products", "HostFact_GroupRelations"], ["HostFact_Products.id", "HostFact_Products.ProductName"])->where("HostFact_GroupRelations.Reference = HostFact_Products.id")->where("HostFact_GroupRelations.Type", $this->Type)->where("HostFact_GroupRelations.Group", $_group->id)->where("HostFact_Products.Status", ["!=" => "9"])->execute();
                            if($result2 && is_array($result2)) {
                                foreach ($result2 as $_group_element) {
                                    $list[$_group->id][$column][$_group_element->id] = ["id" => $_group_element->id, "ProductName" => htmlspecialchars($_group_element->ProductName)];
                                }
                            }
                            break;
                        case "Debtors":
                            $list[$_group->id][$column] = [];
                            $result2 = Database_Model::getInstance()->get(["HostFact_Debtors", "HostFact_GroupRelations"], ["HostFact_Debtors.id"])->where("HostFact_GroupRelations.Reference = HostFact_Debtors.id")->where("HostFact_GroupRelations.Type", $this->Type)->where("HostFact_GroupRelations.Group", $_group->id)->where("HostFact_Debtors.Status", ["!=" => "9"])->execute();
                            if($result2 && is_array($result2)) {
                                foreach ($result2 as $_group_element) {
                                    $list[$_group->id][$column][$_group_element->id] = ["id" => $_group_element->id];
                                }
                            }
                            break;
                        case "Creditors":
                            $list[$_group->id][$column] = [];
                            $result2 = Database_Model::getInstance()->get(["HostFact_Creditors", "HostFact_GroupRelations"], ["HostFact_Creditors.id"])->where("HostFact_GroupRelations.Reference = HostFact_Creditors.id")->where("HostFact_GroupRelations.Type", $this->Type)->where("HostFact_GroupRelations.Group", $_group->id)->where("HostFact_Creditors.Status", ["!=" => "9"])->execute();
                            if($result2 && is_array($result2)) {
                                foreach ($result2 as $_group_element) {
                                    $list[$_group->id][$column][$_group_element->id] = ["id" => $_group_element->id];
                                }
                            }
                            break;
                        default:
                            $list[$_group->id][$column] = htmlspecialchars($_group->{$column});
                    }
                }
            }
        }
        return $list;
    }
}

?>