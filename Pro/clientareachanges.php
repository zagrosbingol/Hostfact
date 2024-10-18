<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "config.php";
$page = isset($_GET["page"]) ? $_GET["page"] : "overview";
class ClientareaChange_Controller extends Base_Controller
{
    public function __construct()
    {
        parent::__construct();
        require_once "class/clientareachange.php";
        $this->ClientareaChange_Model = new ClientareaChange_Model();
    }
    public function overview()
    {
        if(isset($_POST["action"]) && $_POST["action"]) {
            $_ids = isset($_POST["ids"]) && is_array($_POST["ids"]) ? $_POST["ids"] : [];
            $success_counter = 0;
            switch ($_POST["action"]) {
                case "dialog:approve":
                    foreach ($_ids as $_tmp_id) {
                        $clientarea_change_Model = new ClientareaChange_Model();
                        $clientarea_change_Model->id = esc($_tmp_id);
                        $clientarea_change_Model->show();
                        if(!$this->__checkReferenceRights($clientarea_change_Model->ReferenceType)) {
                            $this->Error[] = __("invalid user rights to perform action");
                        } else {
                            if($clientarea_change_Model->approve()) {
                                if($clientarea_change_Model->ReferenceType == "debtor") {
                                    $clientarea_change_Model->execute($clientarea_change_Model->id);
                                }
                                $success_counter++;
                            } else {
                                $this->merge_messages($clientarea_change_Model);
                            }
                            unset($clientarea_change_Model);
                        }
                    }
                    if(0 < $success_counter) {
                        $this->Success[] = sprintf(__("batch clientarea changes approved"), $success_counter);
                    }
                    break;
                case "dialog:reject":
                    foreach ($_ids as $_tmp_id) {
                        $clientarea_change_Model = new ClientareaChange_Model();
                        $clientarea_change_Model->id = esc($_tmp_id);
                        $clientarea_change_Model->show();
                        if(!$this->__checkReferenceRights($clientarea_change_Model->ReferenceType)) {
                            $this->Error[] = __("invalid user rights to perform action");
                        } else {
                            if($clientarea_change_Model->reject()) {
                                $success_counter++;
                            } else {
                                $this->merge_messages($clientarea_change_Model);
                            }
                            unset($clientarea_change_Model);
                        }
                    }
                    if(0 < $success_counter) {
                        $this->Success[] = sprintf(__("batch clientarea changes rejected"), $success_counter);
                    }
                    break;
                case "dialog:execute":
                    foreach ($_ids as $_tmp_id) {
                        $clientarea_change_Model = new ClientareaChange_Model();
                        $clientarea_change_Model->id = esc($_tmp_id);
                        $clientarea_change_Model->show();
                        if(!$this->__checkReferenceRights($clientarea_change_Model->ReferenceType)) {
                            $this->Error[] = __("invalid user rights to perform action");
                        } else {
                            if($clientarea_change_Model->execute($clientarea_change_Model->id)) {
                                $success_counter++;
                            } else {
                                $this->merge_messages($clientarea_change_Model);
                            }
                            unset($clientarea_change_Model);
                        }
                    }
                    if(0 < $success_counter) {
                        $this->Success[] = sprintf(__("batch clientarea changes executed"), $success_counter);
                    }
                    break;
                case "dialog:remove":
                    foreach ($_ids as $_tmp_id) {
                        $clientarea_change_Model = new ClientareaChange_Model();
                        $clientarea_change_Model->id = esc($_tmp_id);
                        $clientarea_change_Model->show();
                        if(!$this->__checkReferenceRights($clientarea_change_Model->ReferenceType)) {
                            $this->Error[] = __("invalid user rights to perform action");
                        } else {
                            if($clientarea_change_Model->delete($clientarea_change_Model->id)) {
                                $success_counter++;
                            } else {
                                $this->merge_messages($clientarea_change_Model);
                            }
                            unset($clientarea_change_Model);
                        }
                    }
                    if(0 < $success_counter) {
                        $this->Success[] = sprintf(__("batch clientarea changes removed"), $success_counter);
                    }
                    break;
                case "dialog:executedmanually":
                    foreach ($_ids as $_tmp_id) {
                        $clientarea_change_Model = new ClientareaChange_Model();
                        $clientarea_change_Model->id = esc($_tmp_id);
                        $clientarea_change_Model->show();
                        if(!$this->__checkReferenceRights($clientarea_change_Model->ReferenceType)) {
                            $this->Error[] = __("invalid user rights to perform action");
                        } else {
                            if($clientarea_change_Model->executedManually($clientarea_change_Model->id)) {
                                $success_counter++;
                            } else {
                                $this->merge_messages($clientarea_change_Model);
                            }
                            unset($clientarea_change_Model);
                        }
                    }
                    if(0 < $success_counter) {
                        $this->Success[] = sprintf(__("batch clientarea changes manually executed"), $success_counter);
                    }
                    break;
                default:
                    flashMessage($this);
                    header("Location: clientareachanges.php");
                    exit;
            }
        } else {
            $this->set("table_config_open", $this->ClientareaChange_Model->getTableConfig("open"));
            $table_config_processed = $this->ClientareaChange_Model->getTableConfig("processed");
            if(isset($_SESSION["backoffice_tables_config"]["list_clientarea_changes_processed"]["filter"])) {
                $table_config_processed["filter"] = $_SESSION["backoffice_tables_config"]["list_clientarea_changes_processed"]["filter"];
            } else {
                $table_config_processed["filter"] = "pending|error";
            }
            $this->set("table_config_processed", $table_config_processed);
            $this->set("status_array", $this->ClientareaChange_Model->StatusArray);
            $this->set("message", parse_message($this->ClientareaChange_Model));
            $this->set("sidebar_template", "clientarea.changes.sidebar.php");
            $this->set("current_page_url", "clientareachanges.php");
            $this->set("page", "overview");
            $this->set("wfh_page_title", __("clientarea changes") . " - " . __("open clientarea changes"));
            $this->view("clientarea.changes.overview.php");
        }
    }
    public function show()
    {
        global $array_legaltype;
        global $array_states;
        global $array_country;
        global $array_sex;
        $this->ClientareaChange_Model->id = isset($_GET["id"]) ? intval(esc($_GET["id"])) : (isset($_POST["id"]) ? intval(esc($_POST["id"])) : NULL);
        if(!$this->ClientareaChange_Model->show(true)) {
            flashMessage($this->ClientareaChange_Model);
            header("Location: clientareachanges.php");
            exit;
        }
        checkRight($this->__checkReferenceRights($this->ClientareaChange_Model->ReferenceType, "show"));
        if($this->ClientareaChange_Model->Approval == "pending" && $this->ClientareaChange_Model->Status == "pending") {
            $page_type = "edit";
            $this->set("form_action", "approve");
        } elseif(in_array($this->ClientareaChange_Model->Status, ["error", "pending"])) {
            $page_type = "edit";
            $this->set("form_action", "editandexecute");
        } else {
            $page_type = "show";
        }
        $action_execute = "automatic";
        strtolower($this->ClientareaChange_Model->ReferenceType);
        switch (strtolower($this->ClientareaChange_Model->ReferenceType)) {
            case "domain":
                strtolower($this->ClientareaChange_Model->Action);
                switch (strtolower($this->ClientareaChange_Model->Action)) {
                    case "editwhois":
                        if(!$this->ClientareaChange_Model->ReferenceObject->RegistrarClass) {
                            $this->set("form_action", "editandmanuallyexecute");
                            $action_execute = "manual";
                        }
                        $this->ClientareaChange_Model->Data = $this->htmlspecialchars_data($this->ClientareaChange_Model->Data);
                        $template_file = "clientarea.changes.whois." . $page_type . ".php";
                        break;
                    case "changenameserver":
                        if(!$this->ClientareaChange_Model->ReferenceObject->RegistrarClass) {
                            $this->set("form_action", "editandmanuallyexecute");
                            $action_execute = "manual";
                        }
                        $this->ClientareaChange_Model->Data = $this->htmlspecialchars_data($this->ClientareaChange_Model->Data);
                        $template_file = "clientarea.changes.nameserver." . $page_type . ".php";
                        break;
                    case "editdnszone":
                        if(is_module_active("dnsmanagement")) {
                            global $_module_instances;
                            $dnsmanagement = $_module_instances["dnsmanagement"];
                            $clientarea_function = "page_clientarea_changes_dnszone_" . $page_type;
                            $view_data = $dnsmanagement->{$clientarea_function}();
                            $this->set("record_types", $view_data["record_types"]);
                            $template_file = $view_data["template_file"];
                            $template_dir = $view_data["template_dir"];
                        }
                        break;
                }
                break;
            case "debtor":
                strtolower($this->ClientareaChange_Model->Action);
                switch (strtolower($this->ClientareaChange_Model->Action)) {
                    case "editgeneral":
                    case "editbilling":
                    case "editpayment":
                        global $array_authorisation;
                        global $array_mailingoptin;
                        global $array_invoicemethod;
                        $this->set("array_authorisation", $array_authorisation);
                        $this->set("array_mailingoptin", $array_mailingoptin);
                        $this->set("array_invoicemethod", $array_invoicemethod);
                        $this->set("array_sex", $array_sex);
                        $this->ClientareaChange_Model->Data = $this->htmlspecialchars_data($this->ClientareaChange_Model->Data);
                        $template_file = "clientarea.changes.debtor." . $page_type . ".php";
                        break;
                }
                break;
            default:
                if($this->ClientareaChange_Model->Status == "executed") {
                    $this->ClientareaChange_Model->Warning[] = sprintf(__("clientarea change executed on"), rewrite_date_db2site($this->ClientareaChange_Model->Modified, "%d-%m-%Y " . __("at") . " %H:%i"));
                } elseif($this->ClientareaChange_Model->Status == "error") {
                    strtolower($this->ClientareaChange_Model->ReferenceType);
                    switch (strtolower($this->ClientareaChange_Model->ReferenceType)) {
                        case "domain":
                            $warning_error_link = "[hyperlink_1]domains.php?page=show&amp;id=" . $this->ClientareaChange_Model->ReferenceObject->Identifier . "#tab-domain-logfile[hyperlink_2]" . __("see log for clientarea change error") . "[hyperlink_3]";
                            break;
                        default:
                            $warning_error_link = "";
                            $this->ClientareaChange_Model->Warning[] = sprintf(__("clientarea change error, see log"), rewrite_date_db2site($this->ClientareaChange_Model->Modified, "%d-%m-%Y " . __("at") . " %H:%i"), $warning_error_link);
                    }
                } elseif($this->ClientareaChange_Model->Status == "canceled" && $this->ClientareaChange_Model->Approval == "rejected") {
                    $this->ClientareaChange_Model->Warning[] = sprintf(__("clientarea change rejected on"), rewrite_date_db2site($this->ClientareaChange_Model->Modified, "%d-%m-%Y " . __("at") . " %H:%i"));
                } elseif($this->ClientareaChange_Model->Status == "pending" && in_array($this->ClientareaChange_Model->Approval, ["notused", "approved"]) && $action_execute == "automatic") {
                    $this->ClientareaChange_Model->Warning[] = __("clientarea change will soon be executed");
                }
                $this->set("ClientareaChange", $this->ClientareaChange_Model);
                $this->set("array_legaltype", $array_legaltype);
                $this->set("array_states", $array_states);
                $this->set("array_country", $array_country);
                $this->set("array_sex", $array_sex);
                $this->set("message", parse_message($this->ClientareaChange_Model));
                $this->set("sidebar_template", "clientarea.changes.sidebar.php");
                $this->set("current_page_url", "clientareachanges.php");
                $this->set("page", "show");
                $this->set("wfh_page_title", __("clientarea changes") . " - " . __("open clientarea changes"));
                if(isset($template_file) && $template_file) {
                    if(isset($template_dir) && $template_dir) {
                        $this->view($template_file, $template_dir);
                    } else {
                        $this->view($template_file);
                    }
                }
        }
    }
    public function approve()
    {
        $this->ClientareaChange_Model->id = isset($_GET["id"]) ? intval(esc($_GET["id"])) : (isset($_POST["id"]) ? intval(esc($_POST["id"])) : NULL);
        if(isset($_POST) && $this->ClientareaChange_Model->show() && $this->__checkReferenceRights($this->ClientareaChange_Model->ReferenceType) && $this->ClientareaChange_Model->approve(esc($_POST))) {
            $this->ClientareaChange_Model->execute($this->ClientareaChange_Model->id);
        }
        flashMessage($this->ClientareaChange_Model);
        header("Location: clientareachanges.php");
        exit;
    }
    public function reject()
    {
        if(isset($_GET["referenceid"]) && 0 < intval($_GET["referenceid"]) && isset($_GET["referencetype"]) && isset($_GET["debtor"]) && 0 < intval($_GET["debtor"]) && $this->__checkReferenceRights($_GET["referencetype"])) {
            $options = [];
            $options["filters"]["reference_type"] = esc($_GET["referencetype"]);
            $options["filters"]["reference_id"] = intval($_GET["referenceid"]);
            $options["filters"]["debtor"] = intval($_GET["debtor"]);
            $options["filter"]["approval"] = "pending";
            $options["filter"] = "pending";
            $changes_result = $this->ClientareaChange_Model->listChanges($options);
            if(!empty($changes_result)) {
                foreach ($changes_result as $_change) {
                    $this->ClientareaChange_Model->id = $_change->id;
                    $this->ClientareaChange_Model->Action = $_change->Action;
                    $this->ClientareaChange_Model->reject();
                }
            }
            if(isset($_POST["ClientareaChange"]) && $_POST["ClientareaChange"]) {
                flashMessage($this->ClientareaChange_Model);
                header("Location: clientareachanges.php");
                exit;
            }
            esc($_GET["referencetype"]);
            switch (esc($_GET["referencetype"])) {
                case "debtor":
                    $return_url = "debtors.php?page=show&id=" . intval($_GET["debtor"]);
                    break;
                default:
                    $return_url = "clientareachanges.php";
                    flashMessage($this->ClientareaChange_Model);
                    header("Location: " . $return_url);
                    exit;
            }
        } else {
            $this->ClientareaChange_Model->id = isset($_GET["id"]) ? intval(esc($_GET["id"])) : (isset($_POST["id"]) ? intval(esc($_POST["id"])) : NULL);
            if($this->ClientareaChange_Model->show()) {
                $this->ClientareaChange_Model->reject();
            }
            flashMessage($this->ClientareaChange_Model);
            header("Location: clientareachanges.php");
            exit;
        }
    }
    public function editAndExecute()
    {
        $this->ClientareaChange_Model->id = isset($_GET["id"]) ? intval(esc($_GET["id"])) : (isset($_POST["id"]) ? intval(esc($_POST["id"])) : NULL);
        if(isset($_POST) && $this->ClientareaChange_Model->show() && $this->__checkReferenceRights($this->ClientareaChange_Model->ReferenceType)) {
            $this->ClientareaChange_Model->Data = $_POST;
            if($this->ClientareaChange_Model->edit()) {
                $this->ClientareaChange_Model->execute($this->ClientareaChange_Model->id);
            }
        }
        flashMessage($this->ClientareaChange_Model);
        header("Location: clientareachanges.php");
        exit;
    }
    public function editAndManuallyExecute()
    {
        $this->ClientareaChange_Model->id = isset($_GET["id"]) ? intval(esc($_GET["id"])) : (isset($_POST["id"]) ? intval(esc($_POST["id"])) : NULL);
        if(isset($_POST) && $this->ClientareaChange_Model->show() && $this->__checkReferenceRights($this->ClientareaChange_Model->ReferenceType)) {
            $this->ClientareaChange_Model->Data = $_POST;
            if($this->ClientareaChange_Model->edit()) {
                $this->ClientareaChange_Model->executedManually($this->ClientareaChange_Model->id);
            }
        }
        flashMessage($this->ClientareaChange_Model);
        header("Location: clientareachanges.php");
        exit;
    }
    public function cancel()
    {
        $this->ClientareaChange_Model->id = isset($_GET["id"]) ? intval(esc($_GET["id"])) : (isset($_POST["id"]) ? intval(esc($_POST["id"])) : NULL);
        if($this->ClientareaChange_Model->show() && $this->__checkReferenceRights($this->ClientareaChange_Model->ReferenceType)) {
            $this->ClientareaChange_Model->cancel();
        }
        flashMessage($this->ClientareaChange_Model);
        header("Location: clientareachanges.php");
        exit;
    }
    public function htmlspecialchars_data($data_obj)
    {
        foreach ($data_obj as $_key => $_value) {
            if(is_string($_value)) {
                $data_obj->{$_key} = htmlspecialchars($_value);
            } elseif(is_object($_value)) {
                $data_obj->{$_key} = $this->htmlspecialchars_data($_value);
            }
        }
        return $data_obj;
    }
    private function __checkReferenceRights($reference_type = "", $rights = "edit")
    {
        if(!$reference_type) {
            return false;
        }
        switch ($reference_type) {
            case "debtor":
                if(constant("U_DEBTOR_" . strtoupper($rights))) {
                    return true;
                }
                break;
            case "domain":
                if(constant("U_DOMAIN_" . strtoupper($rights))) {
                    return true;
                }
                break;
            default:
                return false;
        }
    }
}
$clientarea_changes = new ClientareaChange_Controller();
$clientarea_changes->router($page);

?>