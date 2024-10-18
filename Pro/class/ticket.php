<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class ticket
{
    public $Identifier;
    public $TicketID;
    public $Debtor;
    public $EmailAddress;
    public $CC;
    public $Type;
    public $Date;
    public $Subject;
    public $Owner;
    public $Priority;
    public $Status;
    public $Comment;
    public $Number;
    public $LastDate;
    public $LastName;
    public $LockDate;
    public $LockEmployee;
    public $Free1;
    public $Free2;
    public $Free3;
    public $Free4;
    public $Free5;
    public $CountRows;
    public $Table;
    public $Error;
    public $Success;
    public $Warning;
    public $Variables = ["Identifier", "TicketID", "Debtor", "EmailAddress", "CC", "Type", "Date", "Subject", "Owner", "Priority", "Status", "Comment", "Number", "LastDate", "LastName", "LockDate", "LockEmployee"];
    public static $OAUTH2_MS_CLIENT_ID = "72dcba28-2f62-48d2-91cb-dc9cd8ac61b1";
    public static $OAUTH2_MS_DEVICECODE_URL = "https://login.microsoftonline.com/common/oauth2/v2.0/devicecode";
    public static $OAUTH2_MS_TOKEN_URL = "https://login.microsoftonline.com/common/oauth2/v2.0/token";
    public function __construct()
    {
        $this->Number = 1;
        $this->Priority = 0;
        $this->Status = 1;
        $this->Date = date("Y-m-d H:i:s");
        global $account;
        $this->Owner = isset($account->Identifier) && 0 < $account->Identifier ? $account->Identifier : 0;
        $this->Error = [];
        $this->Warning = [];
        $this->Success = [];
        $this->Type = "email";
        global $company;
        $this->SenderName = TICKET_SENDERNAME ? TICKET_SENDERNAME : htmlspecialchars_decode($company->CompanyName);
        $this->LastName = $this->SenderName;
        $this->SenderEmail = TICKET_EMAILADDRESS && check_email_address(TICKET_EMAILADDRESS, "single") ? TICKET_EMAILADDRESS : $company->EmailAddress;
        $this->LockDate = "0000-00-00 00:00:00";
        $this->LockEmployee = 0;
    }
    public function show($method = "id")
    {
        if(!is_numeric($this->Identifier) && !$this->TicketID) {
            $this->Error[] = __("invalid identifier for ticket");
            return false;
        }
        if($method == "id") {
            $result = Database_Model::getInstance()->getOne("HostFact_Tickets", ["HostFact_Tickets.*", "HostFact_Employee.`Name` as `EmployeeName`"])->join("HostFact_Employee", "HostFact_Employee.`id` = HostFact_Tickets.`Owner`")->where("HostFact_Tickets.id", $this->Identifier)->execute();
        } else {
            $result = Database_Model::getInstance()->getOne("HostFact_Tickets", ["HostFact_Tickets.*", "HostFact_Employee.`Name` as `EmployeeName`"])->join("HostFact_Employee", "HostFact_Employee.`id` = HostFact_Tickets.`Owner`")->where("HostFact_Tickets.TicketID", $this->TicketID)->execute();
        }
        if(!isset($result->id) || empty($result->id)) {
            $this->Error[] = __("invalid identifier for ticket");
            return false;
        }
        foreach ($result as $key => $value) {
            $this->{$key} = htmlspecialchars($value);
        }
        if(!$this->Identifier) {
            $this->Identifier = $result->id;
        }
        return true;
    }
    public function add()
    {
        if(!$this->validate()) {
            return false;
        }
        $this->LastDate = $this->Date;
        $result = Database_Model::getInstance()->insert("HostFact_Tickets", ["TicketID" => $this->TicketID, "Debtor" => $this->Debtor, "EmailAddress" => check_email_address($this->EmailAddress, "convert"), "CC" => check_email_address($this->CC, "convert"), "Type" => $this->Type, "Date" => $this->Date, "Subject" => $this->Subject, "Owner" => $this->Owner, "Priority" => $this->Priority, "Status" => $this->Status, "Comment" => $this->Comment, "Number" => $this->Number, "LastDate" => $this->LastDate, "LastName" => $this->LastName])->execute();
        if($result) {
            $this->Identifier = $result;
            $this->Success[] = sprintf(__("ticket is created"), $this->TicketID);
            return true;
        }
        return false;
    }
    public function edit()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for ticket");
            return false;
        }
        if(!$this->validate()) {
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Tickets", ["TicketID" => $this->TicketID, "Debtor" => $this->Debtor, "EmailAddress" => check_email_address($this->EmailAddress, "convert"), "CC" => check_email_address($this->CC, "convert"), "Type" => $this->Type, "Date" => $this->Date, "Subject" => $this->Subject, "Owner" => $this->Owner, "Priority" => $this->Priority, "Status" => $this->Status, "Comment" => $this->Comment, "Number" => $this->Number, "LastDate" => $this->LastDate, "LastName" => $this->LastName])->where("id", $this->Identifier)->execute();
        if($result) {
            $this->Success[] = sprintf(__("ticket is adjusted"), $this->TicketID);
            return true;
        }
        return false;
    }
    public function changeComment($id, $comment = "")
    {
        if(!is_numeric($id)) {
            $this->Error[] = __("invalid identifier for ticket");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Tickets", ["Comment" => $comment])->where("id", $id)->execute();
        if($result) {
            $this->Success[] = __("comment adjusted");
            return true;
        }
        return false;
    }
    public function changeStatus($newstatus)
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for ticket");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Tickets", ["Status" => $newstatus])->where("id", $this->Identifier)->execute();
        if($result) {
            delete_stats_summary();
            return true;
        }
        return false;
    }
    public function updateLastName($senderName)
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for ticket");
            return false;
        }
        if($senderName == "") {
            $this->Error[] = __("invalid sender name for ticket");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Tickets", ["LastName" => $senderName])->where("id", $this->Identifier)->execute();
        return $result ? true : false;
    }
    public function changeOwner($owner_id)
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for ticket");
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Tickets", ["Owner" => $owner_id])->where("id", $this->Identifier)->execute();
        return $result ? true : false;
    }
    public function newTicketID()
    {
        $prefix = TICKETID_PREFIX;
        $number = TICKETID_NUMBER;
        $prefix = parsePrefixVariables($prefix);
        $length = strlen($prefix . $number);
        $result = Database_Model::getInstance()->getOne("HostFact_Tickets", ["TicketID"])->where("TicketID", ["LIKE" => $prefix . "%"])->where("LENGTH(`TicketID`)", [">=" => $length])->where("(SUBSTR(`TicketID`,:PrefixLength)*1)", [">" => 0])->where("SUBSTR(`TicketID`,:PrefixLength)", ["REGEXP" => "^[0-9]+\$"])->orderBy("(SUBSTR(`TicketID`,:PrefixLength)*1)", "DESC")->bindValue("PrefixLength", strlen($prefix) + 1)->execute();
        if(isset($result->TicketID) && $result->TicketID && is_numeric(substr($result->TicketID, strlen($prefix)))) {
            $TicketID = substr($result->TicketID, strlen($prefix));
            $TicketID = $prefix . @str_repeat("0", @max(@strlen($number) - @strlen(@max($TicketID + 1, $number)), 0)) . max($TicketID + 1, $number);
        } else {
            $TicketID = $prefix . $number;
        }
        return !empty($this->Error) ? false : $TicketID;
    }
    public function is_free($TicketID)
    {
        if($TicketID) {
            $result = Database_Model::getInstance()->getOne("HostFact_Tickets", ["id"])->where("TicketID", $TicketID)->execute();
            if(!$result || $result->id == $this->Identifier) {
                return true;
            }
        }
        return false;
    }
    public function delete()
    {
        if(!is_numeric($this->Identifier) && !$this->TicketID) {
            $this->Error[] = __("invalid identifier for ticket");
            return false;
        }
        if(!$this->show()) {
            return false;
        }
        $ticketmessage = new ticketmessage();
        $fields = ["TicketID"];
        $result = $ticketmessage->all($fields, "id", "ASC", "-1", "", "", $this->TicketID);
        if(is_array($result)) {
            foreach ($result as $k => $v) {
                if(is_numeric($k)) {
                    $ticketmessage->Identifier = $k;
                    $ticketmessage->delete();
                }
            }
        }
        $result = Database_Model::getInstance()->delete("HostFact_Tickets")->where("id", $this->Identifier)->execute();
        if($result) {
            if(is_dir(DIR_TICKET_ATTACHMENTS . $this->TicketID)) {
                if(file_exists(DIR_TICKET_ATTACHMENTS . $this->TicketID . "/Thumbs.db")) {
                    @unlink(DIR_TICKET_ATTACHMENTS . $this->TicketID . "/Thumbs.db");
                }
                @rmdir(DIR_TICKET_ATTACHMENTS . $this->TicketID);
            }
            $this->Success[] = sprintf(__("ticket is removed"), $this->TicketID);
            return true;
        }
        return false;
    }
    public function validate()
    {
        if(!$this->TicketID || !$this->is_free($this->TicketID)) {
            $this->Error[] = __("ticketid already exists");
        }
        if(!strlen($this->Debtor) && !strlen($this->EmailAddress)) {
            $this->Error[] = sprintf(__("debtor or email required"));
            return false;
        }
        if(strlen($this->Debtor) && !is_numeric($this->Debtor)) {
            $this->Error[] = __("invalid debtor");
        }
        if($this->Type == "email" && !check_email_address($this->EmailAddress)) {
            $this->Error[] = __("emailaddress needed for tickets via mail");
        } elseif(strlen($this->EmailAddress) && !check_email_address($this->EmailAddress)) {
            $this->Error[] = sprintf(__("invalid email"), $this->EmailAddress);
        }
        if(isset($this->CC) && !empty($this->CC)) {
            $aValidCCEmail = [];
            $aCC = explode(";", check_email_address($this->CC, "convert"));
            if(is_array($aCC) && count($aCC)) {
                foreach ($aCC as $sCCEmail) {
                    if(!in_array($sCCEmail, $aValidCCEmail) && $sCCEmail != TICKET_EMAILADDRESS) {
                        $aValidCCEmail[] = trim($sCCEmail);
                    }
                }
                $this->CC = implode(";", $aValidCCEmail);
            }
        }
        if(!in_array($this->Type, ["email", "ticket"])) {
            $this->Error[] = __("invalid ticket type");
        }
        if(!(is_string($this->Subject) && strlen($this->Subject) <= 255 || strlen($this->Subject) === 0)) {
            $this->Error[] = __("invalid subject");
        }
        if($this->Owner == "") {
            $this->Owner = 0;
        } elseif(!is_numeric($this->Owner)) {
            $this->Error[] = __("invalid Owner");
        }
        if(!in_array($this->Priority, [0, 1, 5])) {
            $this->Priority = 0;
        }
        if(!in_array($this->Status, [0, 1, 2, 3])) {
            $this->Error[] = __("invalid Status");
        }
        return empty($this->Error) ? true : false;
    }
    public function getID($method, $value, $debtor_id = false)
    {
        switch ($method) {
            case "ticketid":
                $ticket_id = Database_Model::getInstance()->getOne("HostFact_Tickets", ["id"])->where("TicketID", $value)->execute();
                return $ticket_id !== false && 0 < $ticket_id->id ? $ticket_id->id : false;
                break;
            case "identifier":
                $ticket_id = Database_Model::getInstance()->getOne("HostFact_Tickets", ["id"])->where("id", intval($value))->execute();
                return $ticket_id !== false && 0 < $ticket_id->id ? $ticket_id->id : false;
                break;
            case "clientarea":
                $ticket_id = Database_Model::getInstance()->getOne("HostFact_Tickets", ["id"])->where("id", intval($value))->where("Debtor", $debtor_id)->execute();
                return $ticket_id !== false && 0 < $debtor_id ? $ticket_id->id : false;
                break;
        }
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
        if(TICKET_POP3_AUTH_TYPE == Settings::AUTH_TYPE_OAUTH2_MS && TICKET_PASSWORD == "") {
            $this->Error[] = __("ticket error oauth expired");
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
        $DebtorFields = 0 < count(array_intersect($DebtorArray, $fields)) ? true : false;
        $EmployeeArray = ["Name"];
        $EmployeeFields = 0 < count(array_intersect($EmployeeArray, $fields)) ? true : false;
        $search_at = [];
        $DebtorSearch = $EmployeeSearch = false;
        if($searchat && (0 < strlen($searchfor) || $searchfor)) {
            $search_at = explode("|", $searchat);
            $DebtorSearch = 0 < count(array_intersect($DebtorArray, $search_at)) ? true : false;
            $EmployeeSearch = 0 < count(array_intersect($EmployeeArray, $search_at)) ? true : false;
        }
        $select = ["HostFact_Tickets.id"];
        foreach ($fields as $column) {
            if(in_array($column, $DebtorArray)) {
                $select[] = "HostFact_Debtors.`" . $column . "`";
            } elseif(in_array($column, $EmployeeArray)) {
                $select[] = "HostFact_Employee.`" . $column . "`";
            } else {
                $select[] = "HostFact_Tickets.`" . $column . "`";
            }
        }
        Database_Model::getInstance()->get("HostFact_Tickets", $select);
        if($DebtorFields || $DebtorSearch) {
            Database_Model::getInstance()->join("HostFact_Debtors", "HostFact_Debtors.`id` = HostFact_Tickets.`Debtor`");
        }
        if($EmployeeFields || $EmployeeSearch) {
            Database_Model::getInstance()->join("HostFact_Employee", "HostFact_Employee.`id` = HostFact_Tickets.`Owner`");
        }
        if(!empty($search_at)) {
            $or_clausule = [];
            foreach ($search_at as $searchColumn) {
                if(in_array($searchColumn, ["Debtor"])) {
                    $or_clausule[] = ["HostFact_Tickets.`" . $searchColumn . "`", $searchfor];
                } elseif(in_array($searchColumn, $DebtorArray)) {
                    $or_clausule[] = ["HostFact_Debtors.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                } elseif(in_array($searchColumn, $EmployeeArray)) {
                    $or_clausule[] = ["HostFact_Employee.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                } else {
                    $or_clausule[] = ["HostFact_Tickets.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                }
            }
            Database_Model::getInstance()->orWhere($or_clausule);
        }
        $order = $sort ? $order ? $order : "DESC" : "";
        if($sort == "Debtor") {
            Database_Model::getInstance()->orderBy("CONCAT(HostFact_Debtors.`CompanyName`, HostFact_Debtors.`SurName`)", $order);
        } elseif(in_array($sort, $DebtorArray)) {
            Database_Model::getInstance()->orderBy("HostFact_Debtors.`" . $sort . "`", $order);
        } elseif(in_array($sort, $EmployeeArray)) {
            Database_Model::getInstance()->orderBy("HostFact_Employee.`" . $sort . "`", $order);
        } elseif($sort == "TicketID" || !$sort) {
            $order = $order ? $order : "DESC";
            Database_Model::getInstance()->orderBy("IF(SUBSTRING(HostFact_Tickets.`TicketID`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(HostFact_Tickets.`TicketID`,1,1))", $order)->orderBy("LENGTH(HostFact_Tickets.`TicketID`)", $order)->orderBy("HostFact_Tickets.`TicketID`", $order);
        } else {
            Database_Model::getInstance()->orderBy("HostFact_Tickets." . $sort, $order);
        }
        if(0 <= $limit) {
            Database_Model::getInstance()->limit((max(1, $limit) - 1) * $show_results, $show_results);
        }
        if(is_string($group) && 1 < count(explode("|", $group))) {
            $group = explode("|", $group);
            $or_clausule = [];
            foreach ($group as $status) {
                $or_clausule[] = ["HostFact_Tickets.`Status`", $status];
            }
            Database_Model::getInstance()->orWhere($or_clausule);
        } elseif(is_numeric($group)) {
            Database_Model::getInstance()->where("HostFact_Tickets.`Status`", $group);
        } else {
            Database_Model::getInstance()->where("HostFact_Tickets.`Status`", ["<=" => 9]);
        }
        if(!empty($filters)) {
            foreach ($filters as $_db_field => $_db_value) {
                switch ($_db_field) {
                    case "debtor":
                        if(is_numeric($_db_value) && 0 < $_db_value) {
                            Database_Model::getInstance()->where("HostFact_Tickets.Debtor", $_db_value);
                        }
                        break;
                }
            }
        }
        $list = [];
        $list["CountRows"] = $this->CountRows = 0;
        if($ticket_list = Database_Model::getInstance()->execute()) {
            $list["CountRows"] = $this->CountRows = Database_Model::getInstance()->rowCount("HostFact_Tickets", "HostFact_Tickets.id");
            foreach ($ticket_list as $result) {
                $list[$result->id] = ["id" => $result->id];
                foreach ($fields as $column) {
                    $list[$result->id][$column] = htmlspecialchars($result->{$column});
                }
            }
        }
        return $list;
    }
    public function isTicketLocked()
    {
        if(0 < $this->LockEmployee) {
            require_once "class/employee.php";
            $employee = new employee();
            $employee->show($this->LockEmployee);
            $this->LockEmployeeName = $employee->Name;
            return true;
        }
        return false;
    }
    public function lockTicket()
    {
        global $account;
        $employee_id = isset($account->Identifier) && 0 < $account->Identifier ? $account->Identifier : 0;
        Database_Model::getInstance()->update("HostFact_Tickets", ["LockDate" => ["RAW" => "NOW()"], "LockEmployee" => $employee_id])->where("id", $this->Identifier)->execute();
    }
    public function unlockTicket()
    {
        Database_Model::getInstance()->update("HostFact_Tickets", ["LockDate" => "0000-00-00 00:00:00", "LockEmployee" => "0"])->where("id", $this->Identifier)->execute();
    }
}
class ticketmessage
{
    public $Identifier;
    public $TicketID;
    public $Date;
    public $EmailAddress;
    public $CC;
    public $Subject;
    public $Attachments;
    public $Message;
    public $SenderID;
    public $SenderName;
    public $SenderEmail;
    public $Tstatus;
    public $Free1;
    public $Free2;
    public $Free3;
    public $Free4;
    public $Free5;
    public $CountRows;
    public $Table;
    public $Error;
    public $Warning;
    public $Success;
    public $Variables = ["Identifier", "Date", "TicketID", "EmailAddress", "Subject", "Attachments", "Message", "SenderID", "SenderName", "SenderEmail", "Tstatus"];
    public function __construct()
    {
        global $account;
        $this->Tstatus = 0;
        $this->Date = date("Y-m-d H:i:s");
        $this->SenderID = $account->Identifier;
        global $company;
        $this->SenderName = trim(TICKET_SENDERNAME) ? TICKET_SENDERNAME : htmlspecialchars_decode($company->CompanyName);
        $this->SenderEmail = TICKET_EMAILADDRESS && check_email_address(TICKET_EMAILADDRESS) ? TICKET_EMAILADDRESS : $company->EmailAddress;
        $this->Message = $account->Signature;
        $this->ticket_sent_message_to_cc = false;
        $this->ticket_sent_notification_or_email = false;
        $this->Error = [];
        $this->Warning = [];
        $this->Success = [];
    }
    public function show()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for ticketmessage");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_TicketMessage", ["HostFact_TicketMessage.*", "HostFact_Employee.`Name` as `EmployeeName`"])->join("HostFact_Employee", "HostFact_Employee.`id` = HostFact_TicketMessage.`SenderID`")->where("HostFact_TicketMessage.id", $this->Identifier)->execute();
        if(!isset($result->id) || empty($result->id)) {
            $this->Error[] = __("invalid identifier for ticketmessage");
            return false;
        }
        foreach ($result as $key => $value) {
            $this->{$key} = $value;
        }
        return true;
    }
    public function add()
    {
        global $account;
        if(strpos($this->Message, "src=\"data:") !== false) {
            preg_match_all("/src=\"data\\:(.*)\"/", $this->Message, $matches);
            if(isset($matches[1]) && 0 < count($matches[1])) {
                foreach ($matches[1] as $k => $match) {
                    $tmp_filename = "";
                    $tmp_mimetype = substr($match, 0, strpos($match, ";"));
                    $tmp_base64 = substr($match, strpos($match, "base64,") + 7);
                    if(!is_dir(DIR_TICKET_ATTACHMENTS . $this->TicketID)) {
                        mkdir(DIR_TICKET_ATTACHMENTS . $this->TicketID, 511);
                    }
                    switch ($tmp_mimetype) {
                        case "image/png":
                            $tmp_filename = "cid_" . $k . ".png";
                            break;
                        case "image/jpeg":
                            $tmp_filename = "cid_" . $k . ".jpeg";
                            break;
                        case "image/jpg":
                            $tmp_filename = "cid_" . $k . ".jpg";
                            break;
                        case "image/gif":
                            $tmp_filename = "cid_" . $k . ".gif";
                            break;
                        case "image/svg+xml":
                            $tmp_filename = "cid_" . $k . ".svg";
                            break;
                        default:
                            if($tmp_filename == "") {
                            } else {
                                for ($x = 1; file_exists(DIR_TICKET_ATTACHMENTS . $this->TicketID . "/" . $tmp_filename); $x++) {
                                    $tmp_filename = str_replace(["cid_" . $k . ".", "cid_" . $k . "_" . $x . "."], "cid_" . $k . "_" . ($x + 1) . ".", $tmp_filename);
                                }
                                $fp = fopen(DIR_TICKET_ATTACHMENTS . $this->TicketID . "/" . $tmp_filename, "w");
                                fwrite($fp, base64_decode($tmp_base64));
                                fclose($fp);
                                if($this->Attachments) {
                                    $this->Attachments .= "|" . DIR_TICKET_ATTACHMENTS . $this->TicketID . "/" . $tmp_filename;
                                } else {
                                    $this->Attachments .= DIR_TICKET_ATTACHMENTS . $this->TicketID . "/" . $tmp_filename;
                                }
                                $this->Message = str_replace($match, DIR_TICKET_ATTACHMENTS . $this->TicketID . "/" . $tmp_filename, $this->Message);
                            }
                    }
                }
            }
        }
        if(!$this->validate()) {
            return false;
        }
        $result = Database_Model::getInstance()->insert("HostFact_TicketMessage", ["TicketID" => $this->TicketID, "Date" => $this->Date, "EmailAddress" => $this->EmailAddress, "Subject" => $this->Subject, "Attachments" => $this->Attachments, "Message" => $this->Message, "SenderID" => $this->SenderID, "SenderName" => $this->SenderName, "SenderEmail" => $this->SenderEmail, "Tstatus" => $this->Tstatus])->execute();
        if($result) {
            $this->Identifier = $result;
            Database_Model::getInstance()->update("HostFact_Tickets", ["Number" => ["RAW" => "(SELECT COUNT(`id`) FROM `HostFact_TicketMessage` WHERE `TicketID`=:TicketID)"], "LastDate" => $this->Date, "LastName" => $this->SenderName . " - " . htmlspecialchars_decode($account->Name)])->where("TicketID", $this->TicketID)->bindValue("TicketID", $this->TicketID)->execute();
            $this->Success[] = sprintf(__("ticketmessage is added"), $this->TicketID);
            return true;
        }
        return false;
    }
    public function changeTicketID($old_ticketid, $new_ticketid)
    {
        if(!$old_ticketid || !$new_ticketid) {
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_TicketMessage", ["TicketID" => $new_ticketid])->where("TicketID", $old_ticketid)->execute();
        return $result ? true : false;
    }
    public function apiNotification($ticket)
    {
        if(!$this->ticket_sent_notification_or_email) {
            return true;
        }
        if(0 < $this->SenderID) {
            if($ticket->Type == "email") {
                $this->EmailAddress = $ticket->EmailAddress;
                if($this->ticket_sent_message_to_cc && !empty($ticket->CC)) {
                    $this->CC = $ticket->CC;
                }
                $this->sent();
            } elseif(0 < TICKET_REACTION_EMAIL_TEMPLATE && $ticket->EmailAddress && check_email_address($ticket->EmailAddress)) {
                $objects = ["ticket" => $ticket, "ticketmessage" => $this];
                $emailparameters = ["Sender" => $this->SenderName . ($this->SenderEmail ? "<" . $this->SenderEmail . ">" : ""), "Recipient" => $ticket->EmailAddress, "Sent_bcc" => false, "Debtor" => $ticket->Debtor];
                if($this->sentNotification(TICKET_REACTION_EMAIL_TEMPLATE, $objects, $emailparameters)) {
                    $this->Success[] = sprintf(__("a notification for ticket reaction is sent by mail"), $ticket->EmailAddress);
                }
            }
        } else {
            $ticket_notify_emailaddress = TICKET_NOTIFY_EMAILADDRESS;
            if(0 < $ticket->Owner && TICKET_NOTIFY_TO_EMPLOYEE == 1) {
                require_once "class/employee.php";
                $employee = new employee();
                if($employee->show($ticket->Owner) && strlen($employee->EmailAddress) && check_email_address($employee->EmailAddress)) {
                    $ticket_notify_emailaddress = $employee->EmailAddress;
                }
            }
            if(check_email_address($ticket_notify_emailaddress) && (TICKET_NOTIFY == 2 || TICKET_NOTIFY == 1) && 0 < TICKET_NOTIFY_EMAIL_TEMPLATE) {
                $objects = ["ticket" => $ticket, "ticketmessage" => $this];
                $emailparameters = ["Recipient" => $ticket_notify_emailaddress, "Sent_bcc" => false];
                if($this->sentNotification(TICKET_NOTIFY_EMAIL_TEMPLATE, $objects, $emailparameters)) {
                    $this->Success[] = sprintf(__("a notification for ticket reaction is sent by mail"), $ticket_notify_emailaddress);
                }
            }
        }
    }
    public function sentNotification($emailTemplateId, $objects, $emailparameters = [])
    {
        if(isset($emailparameters["Debtor"]) && 0 < $emailparameters["Debtor"]) {
            require_once "class/debtor.php";
            $debtor = new debtor();
            $debtor->Identifier = $emailparameters["Debtor"];
            $debtor->show();
        }
        require_once "class/template.php";
        $emailtemplate = new emailtemplate();
        $emailtemplate->Identifier = $emailTemplateId;
        $emailtemplate->show();
        require_once "class/email.php";
        $email = new email();
        foreach ($emailtemplate->Variables as $v) {
            if(is_string($emailtemplate->{$v})) {
                $email->{$v} = htmlspecialchars_decode($emailtemplate->{$v});
            } else {
                $email->{$v} = $emailtemplate->{$v};
            }
        }
        foreach ($emailparameters as $key => $value) {
            $email->{$key} = $value;
        }
        if(!(TICKET_EMAILADDRESS && check_email_address(TICKET_EMAILADDRESS))) {
            $email->Sender = $emailtemplate->Sender;
        }
        unset($invoice);
        unset($pricequote);
        unset($debtor);
        $email->add($objects);
        $email->AutoSubmitted = true;
        $result = $email->sent();
        if($result) {
            return true;
        }
        $this->Error = array_merge($this->Error, $email->Error);
        return false;
    }
    public function sent()
    {
        if(!$this->EmailAddress || !check_email_address($this->EmailAddress)) {
            $this->Error[] = __("no ticketmessage can be sent, because emailaddress is missing");
            return false;
        }
        require_once "class/email.php";
        $email = new email();
        $email->Recipient = $this->EmailAddress;
        $email->Subject = "[" . $this->TicketID . "] " . $this->Subject;
        $email->Message = $this->Message;
        $email->Sender = $this->SenderName . "<" . $this->SenderEmail . ">";
        $email->Reply = TICKET_EMAILADDRESS;
        $email->Sent_bcc = SEND_TICKET_BCC == "yes" ? true : false;
        if(isset($this->CC) && !empty($this->CC)) {
            $email->CarbonCopy = $this->CC;
        }
        if($this->Attachments) {
            $email->Attachment = $this->Attachments;
        }
        $email->skipEval = true;
        if($email->add()) {
            if($email->sent()) {
                $this->Success = array_merge($this->Success, $email->Success);
                return true;
            }
            $this->Error = array_merge($this->Error, $email->Error);
            return false;
        }
        $this->Error = array_merge($this->Error, $email->Error);
        return false;
    }
    public function delete()
    {
        if(!$this->show()) {
            return false;
        }
        $result = Database_Model::getInstance()->delete("HostFact_TicketMessage")->where("id", $this->Identifier)->execute();
        if($result) {
            if($this->Attachments) {
                $tmp_attachments = explode("|", $this->Attachments);
                foreach ($tmp_attachments as $tmp_attachment) {
                    @unlink($tmp_attachment);
                }
            }
            $this->Success[] = __("ticketmessage is removed");
            return true;
        } else {
            return false;
        }
    }
    public function validate()
    {
        if(is_array($this->Attachments)) {
            $this->Attachments = implode("|", $this->Attachments);
        }
        if(strlen($this->Message) === 0) {
            $this->Error[] = __("a message is needed for a ticketmessage");
        }
        return empty($this->Error) ? true : false;
    }
    public function all($fields, $sort = false, $order = false, $limit = "-1", $searchat = false, $searchfor = false, $group = false)
    {
        global $array_filetypes;
        $show_results = 100;
        $pdo_bind = [];
        $limit = !isset($show_results) || !is_numeric($show_results) ? -1 : $limit;
        if(!is_array($fields) || empty($fields)) {
            $this->Error[] = __("no fields given for retrieving data");
            return false;
        }
        $EmployeeArray = ["Name"];
        $EmployeeFields = 0 < count(array_intersect($EmployeeArray, $fields)) ? true : false;
        $search_at = [];
        $EmployeeSearch = false;
        if($searchat) {
            $search_at = explode("|", $searchat);
            $EmployeeSearch = 0 < count(array_intersect($EmployeeArray, $search_at)) ? true : false;
        }
        $select = ["HostFact_TicketMessage.id"];
        foreach ($fields as $column) {
            if(in_array($column, $EmployeeArray)) {
                $select[] = "HostFact_Employee.`" . $column . "`";
            } else {
                $select[] = "HostFact_TicketMessage.`" . $column . "`";
            }
        }
        Database_Model::getInstance()->get("HostFact_TicketMessage", $select);
        if($EmployeeFields || $EmployeeSearch) {
            Database_Model::getInstance()->join("HostFact_Employee", "HostFact_Employee.`id` = HostFact_TicketMessage.`SenderID`");
        }
        if(!empty($search_at)) {
            $or_clausule = [];
            foreach ($search_at as $searchColumn) {
                if(in_array($searchColumn, ["TicketID"])) {
                    $or_clausule[] = ["HostFact_TicketMessage.`" . $searchColumn . "`", $searchfor];
                } elseif(in_array($searchColumn, $EmployeeArray)) {
                    $or_clausule[] = ["HostFact_Employee.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                } else {
                    $or_clausule[] = ["HostFact_TicketMessage.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                }
            }
            Database_Model::getInstance()->orWhere($or_clausule);
        }
        $order = $sort ? $order ? $order : "DESC" : "";
        if(in_array($sort, $EmployeeArray)) {
            Database_Model::getInstance()->orderBy("HostFact_Employee.`" . $sort . "`", $order);
        } elseif($sort) {
            Database_Model::getInstance()->orderBy("HostFact_TicketMessage." . $sort, $order);
        } else {
            Database_Model::getInstance()->orderBy("HostFact_TicketMessage.Date", "ASC");
        }
        if(0 <= $limit) {
            Database_Model::getInstance()->limit((max(1, $limit) - 1) * $show_results, $show_results);
        }
        if($group) {
            Database_Model::getInstance()->where("HostFact_TicketMessage.`TicketID`", $group);
        } else {
            Database_Model::getInstance()->where("HostFact_TicketMessage.`TicketID`", "invalid");
        }
        $list = [];
        $list["CountRows"] = $this->CountRows = 0;
        if($message_list = Database_Model::getInstance()->execute()) {
            $list["CountRows"] = $this->CountRows = Database_Model::getInstance()->rowCount("HostFact_TicketMessage", "HostFact_TicketMessage.id");
            foreach ($message_list as $result) {
                $list[$result->id] = ["id" => $result->id];
                foreach ($fields as $column) {
                    $list[$result->id][$column] = htmlspecialchars($result->{$column});
                    if($column == "Attachments" && $result->{$column}) {
                        $list[$result->id][$column] = [];
                        $attachments = explode("|", $result->{$column});
                        foreach ($attachments as $v) {
                            $extensie = explode(".", $v);
                            $name = explode("/", $v);
                            $type = isset($array_filetypes[$extensie[count($extensie) - 1]]) ? $array_filetypes[$extensie[count($extensie) - 1]] : "unknown";
                            $list[$result->id][$column][] = ["name" => str_replace("[" . $result->TicketID . "]", "", $name[count($name) - 1]), "location" => $v, "type" => $type, "extension" => $extensie[count($extensie) - 1], "filesize" => @filesize($v) / 1024];
                        }
                        $attachments = "";
                    }
                }
            }
        }
        return $list;
    }
}
class mailserver
{
    public $mailserver;
    public $dir;
    public $Error;
    public $Success;
    public $Warning;
    public function __construct()
    {
        $this->dir = DIR_TICKET_ATTACHMENTS;
        $this->Error = [];
        $this->Warning = [];
        $this->Success = [];
    }
    public function checkMail()
    {
        if($this->connect()) {
            $buffer = "STAT\r\n";
            fputs($this->mailserver, $buffer);
            $buffer = fgets($this->mailserver, 512);
            $mailsum = substr($buffer, 4, 2);
            if(empty($mailsum)) {
                return false;
            }
            for ($mailid = 1; $mailid <= $mailsum; $mailid++) {
                $a = 0;
                $i = 1;
                $type = "";
                $data = "";
                $data_array = [];
                $text = "";
                $html = "";
                $message = "";
                $oboundary = "";
                $iboundary = "";
                $TicketID = "";
                $priority = "";
                $from = "";
                $reply_to = "";
                $cc = "";
                $aCC = [];
                $subject = "";
                $date = "";
                $autoreply_auto_submitted = false;
                $autoreply_has_return_path = false;
                $has_mail_attachment = false;
                $base64 = false;
                $quoted_printable = false;
                $lastone_from = false;
                $lastone_reply_to = false;
                $lastone_cc = false;
                $content_type = "";
                $mail_coding = "ISO-8859-1";
                $buffer = "RETR " . $mailid . "\r\n";
                fputs($this->mailserver, $buffer);
                while ($buffer != ".\r\n") {
                    $buffer = fgets($this->mailserver, 512);
                    $lowerbuffer = strtolower($buffer);
                    if($has_mail_attachment === false && substr($lowerbuffer, 0, 13) == "content-type:" && ($content_type == "" || $content_type == "text/plain" && $autoreply_auto_submitted === false && strtolower(substr($buffer, 14, strpos($buffer, ";") - 14)) == "text/html")) {
                        $type = strpos($buffer, ";") ? strtolower(substr($buffer, 14, strpos($buffer, ";") - 14)) : trim(strtolower(substr($buffer, 14)));
                        $content_type = $type;
                        if($type == "text/html" || $type == "text/plain") {
                            $a = 1;
                        }
                    } elseif($a === 0 && $lowerbuffer == "\r\n") {
                        $content_type = $type = "text/plain";
                        $a = 1;
                    } elseif($has_mail_attachment === false && substr($lowerbuffer, 0, 13) == "content-type:" && strtolower(substr($buffer, 14, strpos($buffer, ";") - 14)) == "text/plain" && $type == "text/plain") {
                        $text = "";
                        $content_type = $type = "text/plain";
                        $a = 1;
                    } elseif(substr($lowerbuffer, 0, 13) == "content-type:" && strpos($lowerbuffer, "message") !== false) {
                        $has_mail_attachment = true;
                    }
                    if(strpos($lowerbuffer, "charset") !== false) {
                        $mail_coding = substr($lowerbuffer, strpos($lowerbuffer, "charset") + 8);
                        if(strpos($mail_coding, "utf-8") !== false) {
                            $mail_coding = "UTF-8";
                        } else {
                            $mail_coding = "ISO-8859-1";
                        }
                    }
                    if(strpos($lowerbuffer, "content-transfer-encoding:") === 0) {
                        if(strpos($buffer, "base64") !== false) {
                            $base64 = true;
                        } else {
                            $base64 = false;
                        }
                        if(strpos($buffer, "quoted-printable") !== false) {
                            $quoted_printable = true;
                        } else {
                            $quoted_printable = false;
                        }
                    }
                    if(strpos($buffer, "filename=") !== false) {
                        $file = substr($buffer, strpos($buffer, "filename=") + 9, strpos($buffer, "\r\n") - strpos($buffer, "filename=") - 9);
                        if($file && !$text && !$html && strpos($file, ".txt") !== false) {
                            $file = false;
                        }
                        if($file) {
                            if(strpos($file, "\"") !== false && preg_match("/\"([^\"]*)\"/", $file, $matches)) {
                                $file = $matches[1];
                            }
                            $i = $i + 1;
                            $a = 5;
                            $files[$i] = str_replace("\"", "", $file);
                            $data = "";
                        }
                    } elseif($a == 4 && strpos($buffer, "name=") !== false) {
                        $file = substr($buffer, strpos($buffer, "name=") + 5, strpos($buffer, "\r\n") - strpos($buffer, "name=") - 5);
                        if($file && !$text && !$html && strpos($file, ".txt") !== false) {
                            $file = false;
                        }
                        if($file) {
                            $i = $i + 1;
                            $a = 5;
                            $files[$i] = str_replace("\"", "", $file);
                            $data = "";
                        }
                    }
                    if(substr(strtolower($buffer), 0, 11) == "content-id:") {
                        if(isset($file) && $file && strpos($buffer, base64_decode($file)) !== false) {
                            $a = 5;
                        }
                        $tmp_cid_name = substr($buffer, 11);
                        $tmp_cid_name = substr($tmp_cid_name, strpos($tmp_cid_name, "<") + 1, strpos($tmp_cid_name, ">") - 2);
                        $files_cid[$i] = $tmp_cid_name;
                    } elseif(substr(strtolower($buffer), 0, 20) == "content-disposition:" && ($text || $html) && !$file) {
                        $i = $i + 1;
                        $a = 5;
                        $files[$i] = str_replace("\"", "", "attachedmail.eml");
                        $data = "";
                    }
                    if($a == 6 && strpos($buffer, "--") !== false && $has_mail_attachment === false) {
                        if($quoted_printable == "data") {
                            $data = quoted_printable_decode($data);
                        }
                        $data_array[$i] = $data;
                        $a = 7;
                    } elseif($a == 6 && isset($oboundary[1]) && strpos($buffer, $oboundary[1]) !== false && $has_mail_attachment === true) {
                        $data_array[$i] = $data;
                        $a = 7;
                    }
                    if($a == 5 && $buffer == "\r\n") {
                        $a = 6;
                        $file = false;
                    }
                    if(!$text && !$html) {
                        if(substr($lowerbuffer, 0, 11) == "x-priority:") {
                            $priority = substr($buffer, 12, 1);
                        }
                        if(substr($lowerbuffer, 0, 3) == "cc:" && $cc == "") {
                            $cc_tmp = "";
                            $cc_tmp = trim(substr(str_replace(";", ",", $buffer), 3));
                            $aCCs = explode(",", $cc_tmp);
                            foreach ($aCCs as $sEmail) {
                                if(strpos($sEmail, "@")) {
                                    $tmpEmail = "";
                                    $tmpEmail = substr($sEmail, strpos($sEmail, "<"), strlen($sEmail));
                                    $tmpEmail = str_replace("&lt;", "", str_replace("&quot;", "", htmlspecialchars(trim($tmpEmail))));
                                    $aTmpEmail = explode("&gt;", $tmpEmail);
                                    if(isset($aTmpEmail[0]) && check_email_address($aTmpEmail[0])) {
                                        $aCC[] = $aTmpEmail[0];
                                    }
                                }
                            }
                            $lastone_cc = true;
                        } elseif($lastone_cc === true && strpos($lowerbuffer, ":") === false) {
                            $cc_tmp = "";
                            $xcc = substr($buffer, strpos($buffer, "<"), strlen($buffer));
                            $cc_tmp = str_replace("&lt;", "", str_replace("&quot;", "", htmlspecialchars(trim($xcc))));
                            $cc_tmp = explode("&gt;", $cc_tmp);
                            if(isset($cc_tmp[0]) && check_email_address($cc_tmp[0])) {
                                $aCC[] = $cc_tmp[0];
                            }
                        } elseif($lastone_cc === true) {
                            $lastone_cc = false;
                            if(!empty($aCC)) {
                                $cc = implode(",", $aCC);
                            }
                        }
                        if(substr($lowerbuffer, 0, 5) == "from:" && $from == "") {
                            $from = rtrim(htmlspecialchars(substr($buffer, 6, strlen($buffer))), "\r\n");
                            $from_tmp = str_replace("&gt;", "", str_replace("&quot;", "", $from));
                            $from_tmp = explode("&lt;", $from_tmp);
                            if(!isset($from_tmp[1]) || !check_email_address($from_tmp[1])) {
                                $lastone_from = true;
                            }
                        } elseif($lastone_from === true && strpos($lowerbuffer, ":") === false) {
                            $from_tmp = str_replace("&gt;", "", str_replace("&quot;", "", htmlspecialchars($buffer)));
                            $from_tmp = explode("&lt;", $from_tmp);
                            if(!isset($from_tmp[1]) || !check_email_address($from_tmp[1])) {
                                $lastone_from = false;
                            } else {
                                $from .= htmlspecialchars($buffer);
                            }
                            $lastone_from = false;
                        }
                        if(substr($lowerbuffer, 0, 9) == "reply-to:" && $reply_to == "") {
                            $reply_to = rtrim(htmlspecialchars(substr($buffer, 10, strlen($buffer))), "\r\n");
                            $reply_to_tmp = str_replace("&gt;", "", str_replace("&quot;", "", $reply_to));
                            $reply_to_tmp = explode("&lt;", $reply_to_tmp);
                            if(!isset($reply_to_tmp[1]) || !check_email_address($reply_to_tmp[1])) {
                                $lastone_reply_to = true;
                            }
                        } elseif($lastone_reply_to === true && strpos($lowerbuffer, ":") === false) {
                            $reply_to_tmp = str_replace("&gt;", "", str_replace("&quot;", "", htmlspecialchars($buffer)));
                            $reply_to_tmp = explode("&lt;", $reply_to_tmp);
                            if(!isset($reply_to_tmp[1]) || !check_email_address($reply_to_tmp[1])) {
                                $lastone_reply_to = false;
                            } else {
                                $reply_to .= htmlspecialchars($buffer);
                            }
                            $lastone_reply_to = false;
                        }
                        if(substr($lowerbuffer, 0, 8) == "subject:" && $subject == "") {
                            $subject = htmlspecialchars(substr($buffer, 9, strlen($buffer)));
                        }
                        if(substr($lowerbuffer, 0, 5) == "date:" && $date == "") {
                            $date = htmlspecialchars(substr($buffer, 6, strlen($buffer)));
                            if(0 < strtotime($date)) {
                                $date = date("Y-m-d H:i:s", strtotime($date));
                            } else {
                                $date = explode(" ", $date);
                                if($date[1] < 10 && strlen($date[1]) == 1) {
                                    $date[1] = "0" . $date[1];
                                }
                                $time = explode(":", $date[4]);
                                switch ($date[2]) {
                                    case "Jan":
                                        $month = "01";
                                        break;
                                    case "Feb":
                                        $month = "02";
                                        break;
                                    case "Mar":
                                        $month = "03";
                                        break;
                                    case "Apr":
                                        $month = "04";
                                        break;
                                    case "May":
                                        $month = "05";
                                        break;
                                    case "Jun":
                                        $month = "06";
                                        break;
                                    case "Jul":
                                        $month = "07";
                                        break;
                                    case "Aug":
                                        $month = "08";
                                        break;
                                    case "Sep":
                                        $month = "09";
                                        break;
                                    case "Oct":
                                        $month = "10";
                                        break;
                                    case "Nov":
                                        $month = "11";
                                        break;
                                    case "Dec":
                                        $month = "12";
                                        break;
                                    default:
                                        $date = 2000 < $date[3] ? $date[3] . $month . $date[1] . $time[0] . $time[1] . $time[2] : date("YmdHis");
                                }
                            }
                        }
                        if(substr($lowerbuffer, 0, 15) == "auto-submitted:" && $autoreply_auto_submitted === false) {
                            if(trim(substr($lowerbuffer, 16)) != "no") {
                                $autoreply_auto_submitted = true;
                            }
                        } elseif(substr($lowerbuffer, 0, 12) == "return-path:" && $autoreply_has_return_path === false && trim(substr($lowerbuffer, 12)) != "<>" && trim(substr($lowerbuffer, 12)) != "") {
                            $autoreply_has_return_path = true;
                        }
                    }
                    if($type == "multipart/mixed" && strpos($lowerbuffer, "boundary=\"") !== false) {
                        $oboundary = "";
                        preg_match("/boundary=\"(.*)\"/i", $buffer, $oboundary);
                        $content_type = "";
                    } elseif(strpos($lowerbuffer, "boundary=\"") !== false) {
                        $iboundary = "";
                        preg_match("/boundary=\"(.*)\"/i", $buffer, $iboundary);
                        $content_type = "";
                    } elseif(strpos($lowerbuffer, "boundary=") !== false) {
                        $iboundary = "";
                        preg_match("/boundary=(.*)/i", trim($buffer), $iboundary);
                        $content_type = "";
                    }
                    $_decode_printable = false;
                    if($a == 2 && $buffer == ".\r\n") {
                        $a = 3;
                        $_decode_printable = true;
                    }
                    if($a == 2 && is_array($iboundary) && strpos($buffer, $iboundary[1]) !== false) {
                        $a = 3;
                        $_decode_printable = true;
                    }
                    if($a == 2 && is_array($oboundary) && strpos($buffer, $oboundary[1]) !== false) {
                        $a = 3;
                        $_decode_printable = true;
                    }
                    if($_decode_printable && in_array($quoted_printable, ["html", "text"])) {
                        ${$quoted_printable} = quoted_printable_decode(${$quoted_printable});
                    }
                    if(isset($iboundary[1]) && $buffer == "--" . $iboundary[1] . "--\r\n") {
                        $a = 4;
                    }
                    if($a == 2 && $type == "text/html") {
                        if($base64) {
                            $html .= base64_decode($buffer);
                        } elseif($quoted_printable) {
                            $quoted_printable = "html";
                            $html .= $buffer;
                        } else {
                            $html .= $buffer;
                        }
                    } elseif($a == 2 && $type == "text/plain") {
                        if($base64) {
                            $text_buffer .= $buffer;
                            $text .= base64_decode($buffer);
                        } elseif($quoted_printable) {
                            $quoted_printable = "text";
                            $text .= $buffer;
                        } else {
                            $text .= $buffer;
                        }
                    } elseif($a == 6) {
                        if($base64) {
                            $data .= base64_decode($buffer);
                        } elseif($quoted_printable) {
                            $quoted_printable = "data";
                            $data .= $buffer;
                        } else {
                            $data .= $buffer;
                        }
                    }
                    if($a == 1 && $buffer == "\r\n") {
                        $a = 2;
                        $quoted_printable = false;
                    }
                    if($a == 1 && $buffer == "") {
                        $a = 2;
                        $quoted_printable = false;
                    }
                }
                if(!empty($reply_to)) {
                    $from = $reply_to;
                }
                if(strpos($from, "?utf-8?B?") !== false) {
                    preg_match("/=\\?utf\\-8\\?b\\?(.*)\\?=/iU", $from, $matches);
                    if(base64_decode($matches[1])) {
                        $from = str_replace($matches[0], base64_decode($matches[1]), $from);
                    }
                    preg_match("/=\\?utf\\-8\\?q\\?(.*)\\?=/iU", $from, $matches);
                    if(quoted_printable_decode($matches[1])) {
                        $from = str_replace($matches[0], quoted_printable_decode($matches[1]), $from);
                    }
                }
                if(!$text && isset($text_buffer) && $text_buffer) {
                    $text = base64_decode($text_buffer);
                }
                if(!$text && !$html) {
                } else {
                    $find = ["=\r\n", "=20", "=3D", "=09", "=2C"];
                    $replace = ["", " ", "=", "\t", ","];
                    for ($i = 128; $i < 256; $i++) {
                        $find[] = "=" . strtoupper(dechex($i));
                        if($mail_coding == "UTF-8") {
                            $replace[] = chr($i);
                        } else {
                            $replace[] = iconv("ISO-8859-1", "UTF-8", chr($i));
                        }
                    }
                    $text = htmlspecialchars(str_replace($find, $replace, $text), ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE, "UTF-8");
                    $text = preg_replace("/[[:alpha:]]+:\\/\\/[^<>[:space:]]+[[:alnum:]\\/]/i", "<a href=\"\\0\">\\0</a>", $text);
                    $html = str_replace($find, $replace, $html);
                    $from = str_replace("&gt;", "", str_replace("&quot;", "", $from));
                    $from = explode("&lt;", $from);
                    $message = $html ? $html : ($text ? nl2br($text) : "");
                    $prefix = TICKETID_PREFIX;
                    $prefix = parsePrefixVariables($prefix);
                    for ($i = 0; $i < strlen($subject); $i++) {
                        $temp = substr($subject, $i, strlen($prefix . TICKETID_NUMBER) + 2);
                        if(substr($temp, 0, strlen($prefix) + 1) == "[" . $prefix && substr($temp, -1) == "]") {
                            $TicketID = substr($temp, 1, strlen($prefix . TICKETID_NUMBER));
                        }
                    }
                    if($TicketID) {
                        $subject = str_replace("[" . $TicketID . "]", "", $subject);
                    } else {
                        $TicketID = "";
                        $subject = $subject;
                    }
                    require_once "class/template.php";
                    $emailtemplate = new emailtemplate();
                    $mail_from = "";
                    if((TICKET_NOTIFY_CUSTOMER == 2 || TICKET_NOTIFY_CUSTOMER == 1 && empty($_SESSION["UserPro"])) && 0 < TICKET_NOTIFY_CUSTOMER_EMAIL) {
                        $emailtemplate->Identifier = TICKET_NOTIFY_CUSTOMER_EMAIL;
                        $emailtemplate->show();
                        if($emailtemplate->Sender) {
                            $mail_from = $emailtemplate->Sender;
                        } else {
                            $mail_from = explode("<", $emailtemplate->Sender);
                            if(is_array($mail_from) && count($mail_from) == 2) {
                                $mail_from = str_replace(">", "", $mail_from[1]);
                            } else {
                                $mail_from = "";
                            }
                        }
                    }
                    $subject = trim($subject);
                    if(is_array($from) && isset($from[1])) {
                        $from[1] = trim($from[1]);
                    }
                    $ticket = new ticket();
                    if($TicketID && ((int) $emailtemplate->Identifier === 0 || $emailtemplate->Subject != $subject) && $from[1] != $mail_from) {
                        $ticket->TicketID = $TicketID;
                        $ticket->show("ticket");
                        if(isset($cc) && strlen($cc)) {
                            if(strlen($ticket->CC)) {
                                $ticket->CC = $ticket->CC . "," . $cc;
                            } else {
                                $ticket->CC = $cc;
                            }
                        }
                        $ticket->Status = 0;
                        $ticket->Number = $ticket->Number + 1;
                        $ticket->LastDate = $date;
                        $ticket->LastName = $from[0];
                        $ticket_add_edit_result = $ticket->edit();
                    } elseif(((int) $emailtemplate->Identifier === 0 || $emailtemplate->Subject != $subject) && ($from[1] != $mail_from || check_email_address(trim($from[0])) && trim($from[0]) != $mail_from)) {
                        $ticket->TicketID = $ticket->newTicketID();
                        $ticket->Type = "email";
                        $ticket->EmailAddress = isset($from[1]) && check_email_address($from[1]) ? $from[1] : (!is_array($from) && check_email_address($from) ? $from : trim($from[0]));
                        $result = Database_Model::getInstance()->get("HostFact_Debtors", ["id"])->where("EmailAddress", $ticket->EmailAddress)->execute();
                        if($result && count($result) == 1) {
                            $ticket->Debtor = $result[0]->id;
                        } else {
                            $ticket->Debtor = "";
                        }
                        if(isset($cc) && strlen($cc)) {
                            $ticket->CC = $cc;
                        }
                        $ticket->Date = $date;
                        $ticket->Subject = $subject;
                        $ticket->Owner = "";
                        $ticket->Priority = $priority;
                        $ticket->Status = 0;
                        $ticket->Number = 1;
                        $ticket->LastDate = $date;
                        $ticket->LastName = $from[0];
                        $ticket_add_edit_result = $ticket->add();
                    }
                    if(isset($ticket_add_edit_result) && $ticket_add_edit_result) {
                        if(((int) $emailtemplate->Identifier === 0 || $emailtemplate->Subject != $subject) && ($from[1] != $mail_from || check_email_address($from[0]) && $from[0] != $mail_from)) {
                            $attachment = "";
                            if(!empty($data_array)) {
                                if(!is_dir($this->dir . $ticket->TicketID)) {
                                    $dir = $this->dir . $ticket->TicketID . "/";
                                    if(ini_get("safe_mode") || !@mkdir($this->dir . $ticket->TicketID, 511)) {
                                        $dir = $this->dir . "[" . $ticket->TicketID . "] ";
                                    }
                                } else {
                                    $dir = $this->dir . $ticket->TicketID . "/";
                                }
                                foreach ($data_array as $k => $v) {
                                    if(is_file($dir . $files[$k])) {
                                        $files[$k] = "+_" . $files[$k];
                                    }
                                    $files[$k] = addslashes(str_replace(" ", "+", $files[$k]));
                                    $handle = fopen($dir . $files[$k], "w+");
                                    fwrite($handle, $v);
                                    fclose($handle);
                                    $attachment .= "|" . $dir . $files[$k];
                                    if(isset($files_cid[$k])) {
                                        $message = str_replace("cid:" . $files_cid[$k], $dir . $files[$k], $message);
                                    }
                                }
                                $attachment = substr($attachment, 1);
                            }
                            $ticketmessage = new ticketmessage();
                            $ticketmessage->TicketID = $ticket->TicketID;
                            $ticketmessage->Date = $date;
                            $ticketmessage->EmailAddress = "";
                            $ticketmessage->Subject = $subject;
                            $ticketmessage->Attachments = isset($attachment) ? $attachment : "";
                            $ticketmessage->Message = $message;
                            $ticketmessage->SenderID = "";
                            $ticketmessage->SenderName = $from[0];
                            $ticketmessage->SenderEmail = isset($from[1]) && trim($from[1]) ? $from[1] : $from[0];
                            $ticketmessage->SenderEmail = trim($ticketmessage->SenderEmail);
                            $ticketmessage->Tstatus = 0;
                            if($ticketmessage->add()) {
                                createMessageLog("success", "ticket received via mail", $ticket->TicketID, "ticket", $ticket->Identifier, true);
                            } else {
                                createMessageLog("error", "error while receiving ticket", $ticket->TicketID, "ticket", $ticket->Identifier, true);
                            }
                            require_once "class/company.php";
                            $company = new company();
                            $company->show();
                            $ticket_notify_emailaddress = TICKET_NOTIFY_EMAILADDRESS;
                            if(0 < $ticket->Owner && TICKET_NOTIFY_TO_EMPLOYEE == 1) {
                                require_once "class/employee.php";
                                $employee = new employee();
                                if($employee->show($ticket->Owner) && strlen($employee->EmailAddress) && check_email_address($employee->EmailAddress)) {
                                    $ticket_notify_emailaddress = $employee->EmailAddress;
                                }
                            }
                            if(check_email_address($ticket_notify_emailaddress) && (TICKET_NOTIFY == 2 || TICKET_NOTIFY == 1 && empty($_SESSION["UserPro"])) && 0 < TICKET_NOTIFY_EMAIL_TEMPLATE && $ticket_notify_emailaddress != TICKET_EMAILADDRESS) {
                                $objects = ["ticket" => $ticket, "ticketmessage" => $ticketmessage];
                                $emailparameters = ["Recipient" => $ticket_notify_emailaddress, "Debtor" => $ticket->Debtor, "Sent_bcc" => false];
                                $ticketmessage->sentNotification(TICKET_NOTIFY_EMAIL_TEMPLATE, $objects, $emailparameters);
                            }
                            if((TICKET_NOTIFY_CUSTOMER == 2 || TICKET_NOTIFY_CUSTOMER == 1 && empty($_SESSION["UserPro"])) && 0 < TICKET_NOTIFY_CUSTOMER_EMAIL && $ticketmessage->SenderEmail != TICKET_EMAILADDRESS && ($autoreply_auto_submitted === false || $autoreply_has_return_path !== false)) {
                                $objects = ["ticket" => $ticket, "ticketmessage" => $ticketmessage];
                                $emailparameters = ["Recipient" => $ticketmessage->SenderEmail, "Debtor" => $ticket->Debtor, "Sent_bcc" => false];
                                $ticketmessage->sentNotification(TICKET_NOTIFY_CUSTOMER_EMAIL, $objects, $emailparameters);
                            }
                        }
                        $buffer = "DELE " . $mailid . "\r\n";
                        fputs($this->mailserver, $buffer);
                    }
                }
            }
            $this->disconnect();
        }
    }
    public function connect($ticketServer = TICKET_POP3_SERVER, $ticketPort = TICKET_POP3_PORT, $ticketEmail = TICKET_EMAILADDRESS, $ticketPassword = TICKET_PASSWORD, $authType = TICKET_POP3_AUTH_TYPE)
    {
        if(!$ticketServer || !$ticketPort || !$ticketEmail || !$ticketPassword) {
            return false;
        }
        $context = ["ssl" => ["verify_peer" => false, "verify_peer_name" => false]];
        $context = stream_context_create($context);
        $this->mailserver = stream_socket_client($ticketServer . ":" . $ticketPort, $errno, $errstr, 10, STREAM_CLIENT_CONNECT, $context);
        if(!$this->mailserver) {
            if(isset($this->fromCronjob) && $this->fromCronjob) {
                $this->Error[] = sprintf(__("ticket error connecting with pop3 server cronjob"), "[hyperlink_1]settings.php?page=tickets[hyperlink_2]" . __("go to settings") . "[hyperlink_3]");
            } else {
                $this->Error[] = sprintf(__("ticket error connecting with pop3 server"), $errstr);
            }
            return false;
        }
        fgets($this->mailserver, 512);
        switch ($authType) {
            case Settings::AUTH_TYPE_OAUTH2_MS:
                return $this->authenticate_oauth2_ms($ticketEmail, $ticketPassword);
                break;
            default:
                return $this->authenticate_plain($ticketEmail, $ticketPassword);
        }
    }
    public function oauth2_get_refresh_token_with_device_code($client_id, $url, $device_code)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        settings::disableSSLVerificationIfNeeded($ch);
        curl_setopt($ch, CURLOPT_TIMEOUT, "10");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ["client_id" => $client_id, "grant_type" => "urn:ietf:params:oauth:grant-type:device_code", "device_code" => $device_code]);
        $result = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($result);
        if($response && !isset($response->error) && isset($response->refresh_token)) {
            return $response->refresh_token;
        }
        return "";
    }
    private function oauth2_get_bearer_token($client_id, $url, $ticketPassword)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        settings::disableSSLVerificationIfNeeded($ch);
        curl_setopt($ch, CURLOPT_TIMEOUT, "10");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ["client_id" => $client_id, "grant_type" => "refresh_token", "refresh_token" => passcrypt($ticketPassword)]);
        $result = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($result);
        if($response && !isset($response->error) && isset($response->access_token) && isset($response->refresh_token)) {
            $setting = new settings();
            $setting->Variable = "TICKET_PASSWORD";
            $setting->Value = $response->refresh_token;
            $setting->edit();
            return $response->access_token;
        }
        if($response) {
            Database_Model::getInstance()->update("HostFact_Settings", ["Value" => ""])->where("Variable", "TICKET_PASSWORD")->execute();
            $this->Error[] = __("ticket error oauth expired");
        }
        return "";
    }
    private function authenticate_oauth2_ms($ticketEmail, $ticketPassword)
    {
        $bearer = $this->oauth2_get_bearer_token(ticket::$OAUTH2_MS_CLIENT_ID, ticket::$OAUTH2_MS_TOKEN_URL, $ticketPassword);
        if(!$bearer) {
            return false;
        }
        $buffer = "AUTH XOAUTH2\r\n";
        fputs($this->mailserver, $buffer);
        fgets($this->mailserver, 512);
        $token = base64_encode("user=" . $ticketEmail . "\1auth=Bearer " . $bearer . "\1\1");
        fputs($this->mailserver, $token . "\r\n");
        $buffer = fgets($this->mailserver, 512);
        if(strpos(strtolower($buffer), "err") !== false) {
            $this->disconnect();
            $this->Error[] = __("ticket pop3 invalid token authorization");
            return false;
        }
        return true;
    }
    private function authenticate_plain($ticketEmail, $ticketPassword)
    {
        $buffer = "USER " . $ticketEmail . "\r\n";
        fputs($this->mailserver, $buffer);
        $buffer = fgets($this->mailserver, 512);
        if(strpos(strtolower($buffer), "err") !== false) {
            $this->disconnect();
            if(isset($this->fromCronjob) && $this->fromCronjob) {
                $this->Error[] = sprintf(__("ticket pop3 invalid username cronjob"), "[hyperlink_1]settings.php?page=tickets[hyperlink_2]" . __("go to settings") . "[hyperlink_3]");
            } else {
                $this->Error[] = __("ticket pop3 invalid username");
            }
            return false;
        }
        $buffer = "PASS " . passcrypt($ticketPassword) . "\r\n";
        fputs($this->mailserver, $buffer);
        $buffer = fgets($this->mailserver, 512);
        if(strpos(strtolower($buffer), "err") !== false) {
            $this->disconnect();
            if(isset($this->fromCronjob) && $this->fromCronjob) {
                $this->Error[] = sprintf(__("ticket pop3 authentication failed cronjob"), "[hyperlink_1]settings.php?page=tickets[hyperlink_2]" . __("go to settings") . "[hyperlink_3]");
            } else {
                $this->Error[] = __("ticket pop3 authentication failed");
            }
            return false;
        }
        return true;
    }
    private function disconnect()
    {
        $buffer = "QUIT\r\n";
        fputs($this->mailserver, $buffer);
        $buffer = fgets($this->mailserver, 512);
        fclose($this->mailserver);
    }
}

?>