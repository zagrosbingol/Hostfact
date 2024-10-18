<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class ticket_api_controller extends api_controller
{
    public function __construct()
    {
        parent::__construct();
        HostFact_API::setObjectNames("tickets", "ticket");
        require_once "class/ticket.php";
        $this->addParameter("Identifier", "int");
        $this->addParameter("TicketID", "string");
        $this->addParameter("Debtor", "int");
        $this->addParameter("DebtorCode", "string");
        $this->addParameter("EmailAddress", "string");
        $this->addParameter("CC", "text");
        $this->addParameter("Type", "string");
        $this->addParameter("Date", "datetime");
        $this->addParameter("Subject", "string");
        $this->addParameter("Owner", "int");
        $this->addParameter("Priority", "int");
        $this->addParameter("Status", "int");
        $this->addParameter("Comment", "text");
        $this->addParameter("Number", "readonly");
        $this->addParameter("LastDate", "readonly");
        $this->addParameter("LastName", "readonly");
        $this->addParameter("TicketMessages", "array");
        $this->addSubParameter("TicketMessages", "Date", "date");
        $this->addSubParameter("TicketMessages", "Subject", "readonly");
        $this->addSubParameter("TicketMessages", "Attachments", "array_raw");
        $this->addSubParameter("TicketMessages", "Message", "text");
        $this->addSubParameter("TicketMessages", "SenderID", "int");
        $this->addSubParameter("TicketMessages", "SenderName", "string");
        $this->addSubParameter("TicketMessages", "SenderEmail", "string");
        $this->addFilter("order", "string", "DESC");
        $this->object = new ticket();
    }
    public function list_api_action()
    {
        $filters = $this->getFilterValues();
        $fields = ["TicketID", "Debtor", "CompanyName", "SurName", "Initials", "Subject", "Owner", "Priority", "Status", "Number", "LastDate", "LastName", "Name"];
        $sort = $filters["sort"] ? $filters["sort"] : "TicketID";
        $page_offset = $filters["offset"] / $filters["limit"] + 1;
        $searchat = $filters["searchat"] ? $filters["searchat"] : "TicketID|CompanyName|SurName|Subject";
        $limit = $filters["limit"];
        $field_filters = array_diff_key($filters, array_flip(["offset", "limit", "sort", "order", "searchat", "searchfor"]));
        $ticket_list = $this->object->all($fields, $sort, $filters["order"], $page_offset, $searchat, $filters["searchfor"], $field_filters, $limit);
        $array = [];
        foreach ($ticket_list as $key => $value) {
            if($key != "CountRows") {
                $array[] = ["Identifier" => $value["id"], "TicketID" => htmlspecialchars_decode($value["TicketID"]), "Number" => $value["Number"], "Debtor" => $value["Debtor"], "CompanyName" => htmlspecialchars_decode($value["CompanyName"]), "Initials" => htmlspecialchars_decode($value["Initials"]), "SurName" => htmlspecialchars_decode($value["SurName"]), "Subject" => htmlspecialchars_decode($value["Subject"]), "Owner" => $value["Owner"], "LastDate" => $this->_filter_date_db2api($value["LastDate"], false), "Priority" => $value["Priority"], "Status" => $value["Status"]];
            }
        }
        HostFact_API::setMetaData("totalresults", $ticket_list["CountRows"]);
        HostFact_API::setMetaData("currentresults", count($array));
        HostFact_API::setMetaData("offset", $filters["offset"]);
        if(!empty($this->_valid_filter_input)) {
            HostFact_API::setMetaData("filters", $this->_valid_filter_input);
        }
        HostFact_API::parseResponse($array, true);
    }
    public function show_api_action()
    {
        $ticket_id = $this->_get_ticket_id();
        return $this->_show_ticket($ticket_id);
    }
    public function add_api_action()
    {
        if(TICKET_USE != 1) {
            HostFact_API::parseError(__("cant add tickets if the ticket system is turned off"), true);
        }
        $parse_array = $this->getValidParameters();
        $ticket = $this->object;
        $ticket->Status = 0;
        $ticket->Type = "ticket";
        foreach ($parse_array as $key => $value) {
            if(in_array($key, $ticket->Variables)) {
                $ticket->{$key} = $value;
            }
        }
        $ticket->Debtor = $this->_get_debtor_id();
        if(!isset($parse_array["EmailAddress"]) && $ticket->EmailAddress == "" && 0 < $ticket->Debtor) {
            require_once "class/debtor.php";
            $debtor = new debtor();
            $debtor->Identifier = $ticket->Debtor;
            $debtor->show();
            if($debtor->EmailAddress && check_email_address($debtor->EmailAddress)) {
                $ticket->EmailAddress = $debtor->EmailAddress;
            }
        }
        HostFact_API::beginTransaction();
        if($ticket->TicketID == "") {
            $ticket->TicketID = $ticket->newTicketID();
        } elseif(!$ticket->is_free(esc($ticket->TicketID))) {
            HostFact_API::parseError(__("ticketID already exists"), true);
        }
        if($ticket->add() && $this->_add_ticket_message($ticket->Identifier)) {
            HostFact_API::commit();
            HostFact_API::parseSuccess($ticket->Success);
            $this->_show_ticket($ticket->Identifier);
        }
        HostFact_API::parseError($ticket->Error, true);
    }
    public function addmessage_api_action()
    {
        if($this->_add_ticket_message()) {
            return $this->_show_ticket($this->object->Identifier);
        }
        HostFact_API::parseError($this->object->Error, true);
    }
    public function edit_api_action()
    {
        $parse_array = $this->getValidParameters();
        $ticket = $this->object;
        $ticket->Identifier = $this->_get_ticket_id();
        if(!$ticket->show()) {
            HostFact_API::parseError($ticket->Error, true);
        }
        HostFact_API::beginTransaction();
        $old_ticketid = $ticket->TicketID;
        foreach ($ticket as $key => $value) {
            if(is_string($value)) {
                $ticket->{$key} = isset($parse_array[$key]) ? $parse_array[$key] : htmlspecialchars_decode($value);
            }
        }
        if(!$ticket->is_free($ticket->TicketID)) {
            HostFact_API::parseError(__("ticketID already exists"), true);
        }
        if($ticket->Debtor <= 0) {
            $ticket->Type = "email";
        }
        if(!isset($parse_array["EmailAddress"]) && $ticket->EmailAddress == "" && 0 < $ticket->Debtor) {
            require_once "class/debtor.php";
            $debtor = new debtor();
            $debtor->Identifier = $ticket->Debtor;
            $debtor->show();
            if($debtor->EmailAddress && check_email_address($debtor->EmailAddress)) {
                $ticket->EmailAddress = $debtor->EmailAddress;
            }
        }
        if($ticket->edit()) {
            $ticket_id = $ticket->Identifier;
            if($old_ticketid != $ticket->TicketID) {
                $ticketmessage = new ticketmessage();
                $ticketmessage->changeTicketID($old_ticketid, $ticket->TicketID);
            }
            HostFact_API::commit();
            HostFact_API::parseSuccess($ticket->Success);
            $this->_show_ticket($ticket_id);
        } else {
            HostFact_API::parseError($ticket->Error, true);
        }
    }
    public function delete_api_action()
    {
        $ticket = $this->object;
        $ticket->Identifier = $this->_get_ticket_id();
        if(!$ticket->show()) {
            HostFact_API::parseError([__("invalid identifier for ticket")], true);
        }
        if(!$ticket->delete()) {
            HostFact_API::parseError($ticket->Error, true);
        }
        HostFact_API::parseSuccess(sprintf(__("ticket is removed"), $ticket->TicketID), true);
    }
    public function changestatus_api_action()
    {
        $ticket = $this->object;
        $ticket->Identifier = $this->_get_ticket_id();
        if(!$ticket->show()) {
            HostFact_API::parseError([__("invalid identifier for ticket")], true);
        }
        $newTicketStatus = HostFact_API::getRequestParameter("Status");
        if(!in_array($newTicketStatus, [0, 1, 2, 3])) {
            HostFact_API::parseError(__("invalid Status"), true);
        }
        if(!$ticket->changeStatus($newTicketStatus)) {
            HostFact_API::parseError($ticket->Error, true);
        }
        return $this->_show_ticket($ticket->Identifier);
    }
    public function changeowner_api_action()
    {
        $ticket = $this->object;
        $ticket->Identifier = $this->_get_ticket_id();
        if(!$ticket->show()) {
            HostFact_API::parseError([__("invalid identifier for ticket")], true);
        }
        if(is_numeric(HostFact_API::getRequestParameter("Owner")) && (int) HostFact_API::getRequestParameter("Owner") === 0) {
            $newTicketOwner = 0;
        } else {
            $newTicketOwner = $this->_checkEmployee(HostFact_API::getRequestParameter("Owner"));
        }
        if(!$ticket->changeOwner($newTicketOwner)) {
            HostFact_API::parseError($ticket->Error, true);
        }
        return $this->_show_ticket($ticket->Identifier);
    }
    private function _get_ticket_id()
    {
        $ticket_id = HostFact_API::getRequestParameter("Identifier");
        if(0 < $ticket_id) {
            if(defined("API_CALL_FROM_CLIENTAREA") && API_CALL_FROM_CLIENTAREA === true && !empty(debtor_id::$ClientArea)) {
                $ticket_id = $this->object->getID("clientarea", $ticket_id, ClientArea::$debtor_id);
            } else {
                $ticket_id = $this->object->getID("identifier", $ticket_id);
            }
            return $ticket_id;
        }
        if($ticketId = HostFact_API::getRequestParameter("TicketID")) {
            $ticket_id = $this->object->getID("ticketid", $ticketId);
            if(0 < $ticket_id && defined("API_CALL_FROM_CLIENTAREA") && API_CALL_FROM_CLIENTAREA === true && !empty(debtor_id::$ClientArea)) {
                $ticket_id = $this->object->getID("clientarea", $ticket_id, ClientArea::$debtor_id);
            }
            return $ticket_id;
        }
        return false;
    }
    private function _add_ticket_message($ticket_id = 0)
    {
        $ticket = $this->object;
        $ticket->Identifier = (int) $ticket_id === 0 ? $this->_get_ticket_id() : $ticket_id;
        if($ticket->show()) {
            $ticketmessage = new ticketmessage();
            $parse_array = $this->getValidParameters();
            if(!isset($parse_array["TicketMessages"][0]["Message"]) || strlen($parse_array["TicketMessages"][0]["Message"]) === 0) {
                HostFact_API::parseError(__("no ticket message found"), true);
            }
            $employeeID = 0;
            if(isset($parse_array["TicketMessages"][0]["SenderID"])) {
                $employeeID = $this->_checkEmployee($parse_array["TicketMessages"][0]["SenderID"]);
            } elseif(isset($parse_array["TicketMessages"][0]["SenderName"]) && $parse_array["TicketMessages"][0]["SenderName"] != "") {
                $ticketmessage->SenderName = $parse_array["TicketMessages"][0]["SenderName"];
                $ticketmessage->SenderEmail = isset($parse_array["TicketMessages"][0]["SenderEmail"]) ? $parse_array["TicketMessages"][0]["SenderEmail"] : "";
                $ticket->updateLastName($parse_array["TicketMessages"][0]["SenderName"]);
            } else {
                HostFact_API::parseError(__("senderid or sendername are required"), true);
            }
            $ticketmessage->ticket_sent_message_to_cc = HostFact_API::getRequestParameter("ticket_sent_message_to_cc") == "yes" ? true : false;
            $ticketmessage->ticket_sent_notification_or_email = HostFact_API::getRequestParameter("ticket_sent_notification_or_email") == "yes" ? true : false;
            $ticketmessage->Date = isset($parse_array["TicketMessages"][0]["Date"]) && is_date(rewrite_date_site2db($parse_array["TicketMessages"][0]["Date"])) ? rewrite_date_site2db($parse_array["TicketMessages"][0]["Date"]) : $ticketmessage->Date;
            $ticketmessage->TicketID = $ticket->TicketID;
            $attachment_array = [];
            if(defined("IS_DEMO") && IS_DEMO) {
            } elseif($parse_array["TicketMessages"][0]["Attachments"]) {
                require_once "class/attachment.php";
                $attachment_class = new attachment();
                foreach ($parse_array["TicketMessages"][0]["Attachments"] as $_attachment) {
                    $attachment_class->FileType = "ticket";
                    $attachment_class->FilenameOriginal = $_attachment["Filename"];
                    $attachment_class->FileBase64 = $_attachment["Base64"];
                    $attachment_class->Reference = $ticketmessage->TicketID;
                    $result = $attachment_class->saveBase64_Ticket();
                    if($result !== false) {
                        $attachment_array[] = $result;
                    }
                }
            }
            $ticketmessage->Attachments = implode("|", $attachment_array);
            $ticketmessage->Subject = htmlspecialchars_decode($ticket->Subject);
            $ticketmessage->Message = $parse_array["TicketMessages"][0]["Message"];
            $ticketmessage->SenderID = $employeeID;
            if($ticketmessage->add()) {
                $ticketmessage->apiNotification($ticket);
                if(count($ticketmessage->Error)) {
                    HostFact_API::parseWarning($ticketmessage->Error);
                }
                if(HostFact_API::getRequestParameter("ticket_close_after_reply") == "yes") {
                    $ticket->changeStatus(3);
                } elseif(defined("API_CALL_FROM_CLIENTAREA") && API_CALL_FROM_CLIENTAREA === true) {
                    $ticket->changeStatus(0);
                } elseif((int) $ticket_id === 0) {
                    $ticket->changeStatus(1);
                }
                HostFact_API::parseSuccess($ticketmessage->Success);
                return true;
            }
        } else {
            HostFact_API::parseError($ticket->Error, true);
        }
    }
    private function _checkEmployee($employeeID)
    {
        require_once "class/employee.php";
        $employee = new employee();
        $employee->Identifier = esc($employeeID);
        if($employee->show()) {
            return $employee->Identifier;
        }
        HostFact_API::parseError($employee->Error, true);
    }
    private function _get_ticket_messages($TicketID)
    {
        $ticketmessage = new ticketmessage();
        $fields = ["TicketID", "Subject", "Message", "SenderName", "SenderEmail", "SenderID", "Date", "Attachments", "Name"];
        return $ticketmessage->all($fields, "Date", "DESC", "-1", "", "", $TicketID);
    }
    private function _show_ticket($ticket_id)
    {
        $ticket = $this->object;
        $ticket->Identifier = $ticket_id;
        if(!$ticket->show()) {
            HostFact_API::parseError($ticket->Error, true);
        }
        $result = [];
        $ticketMessages = $this->_get_ticket_messages($ticket->TicketID);
        foreach ($this->_object_parameters as $field => $field_info) {
            if($field == "TicketMessages" && 0 < $ticketMessages["CountRows"]) {
                foreach ($ticketMessages as $key => $value) {
                    if(is_numeric($key) && !empty($ticketMessages[$key])) {
                        $line_data = [];
                        $line_data["Identifier"] = $key;
                        foreach ($this->_object_parameters[$field]["children"] as $elementKey => $elementValue) {
                            if(isset($ticketMessages[$key][$elementKey])) {
                                if($elementKey == "Message") {
                                    $line_data["Base64Message"] = base64_encode(htmlspecialchars_decode($ticketMessages[$key][$elementKey]));
                                } else {
                                    $line_data[$elementKey] = is_string($ticketMessages[$key][$elementKey]) ? htmlspecialchars_decode($ticketMessages[$key][$elementKey]) : $ticketMessages[$key][$elementKey];
                                }
                            }
                        }
                        $line_data["Date"] = $this->_filter_date_db2api($line_data["Date"], false);
                        if(defined("API_CALL_FROM_CLIENTAREA") && API_CALL_FROM_CLIENTAREA === true && 0 < $line_data["SenderID"]) {
                            if(!isset($employees)) {
                                require_once "class/employee.php";
                                $employee = new employee();
                                $fields = ["Name"];
                                $employees = $employee->all($fields);
                            }
                            $line_data["EmployeeName"] = isset($employees[$line_data["SenderID"]]) ? $employees[$line_data["SenderID"]]["Name"] : "";
                        }
                        $result["TicketMessages"][] = $line_data;
                    }
                }
            } elseif($field == "DebtorCode") {
                if(0 < $ticket->Debtor) {
                    require_once "class/debtor.php";
                    $debtor = new debtor();
                    $result["DebtorCode"] = $debtor->getDebtorCode($ticket->Debtor);
                } else {
                    $result["DebtorCode"] = "";
                }
            } else {
                $result[$field] = is_string($ticket->{$field}) ? htmlspecialchars_decode($ticket->{$field}) : $ticket->{$field};
            }
        }
        $result["Date"] = $this->_filter_date_db2api($result["Date"], false);
        global $array_priority;
        global $array_ticketstatus;
        $result["Translations"] = ["Status" => isset($array_ticketstatus[$ticket->Status]) ? $array_ticketstatus[$ticket->Status] : "", "Priority" => isset($array_priority[$ticket->Priority]) ? $array_priority[$ticket->Priority] : ""];
        return HostFact_API::parseResponse($result);
    }
}

?>