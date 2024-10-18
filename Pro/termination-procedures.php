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
class TerminationProcedures_Controller extends Base_Controller
{
    public function __construct()
    {
        parent::__construct();
        require_once "class/terminationprocedure.php";
    }
    public function overview()
    {
        checkRight(U_SETTINGS_SHOW);
        if(isset($_POST["action"]) && $_POST["action"] && U_SETTINGS_EDIT) {
            $_ids = isset($_POST["ids"]) && is_array($_POST["ids"]) ? $_POST["ids"] : [];
            switch ($_POST["action"]) {
                case "dialog:delete":
                    foreach ($_ids as $_tmp_id) {
                        $termination_procedure = new TerminationProcedure_Model();
                        $termination_procedure->id = esc($_tmp_id);
                        $termination_procedure->show();
                        $termination_procedure->delete();
                        $this->merge_messages($termination_procedure);
                        unset($termination_procedure);
                    }
                    break;
                default:
                    flashMessage($this);
                    header("Location: ?");
                    exit;
            }
        } else {
            $termination_procedure = new TerminationProcedure_Model();
            $termination_procedure->checkDefaultProcedures();
            $this->set("table_config", $termination_procedure->getTableConfig());
            $this->set("message", parse_message($termination_procedure));
            $this->set("sidebar_template", "settings.sidebar.php");
            $this->set("current_page_url", "termination-procedures.php");
            $this->set("page", "overview");
            $this->set("wfh_page_title", __("settings") . " - " . __("termination procedures overview"));
            $this->view("termination.procedures.overview.php");
        }
    }
    public function add($add_or_edit = "add")
    {
        checkRight(U_SETTINGS_EDIT);
        $termination_procedure = new TerminationProcedure_Model();
        if($add_or_edit == "edit") {
            $termination_procedure->id = isset($_POST["id"]) ? intval(esc($_POST["id"])) : intval(esc($_GET["id"]));
            if(!$termination_procedure->show()) {
                flashMessage($termination_procedure);
                header("Location: ?");
                exit;
            }
        }
        if(isset($_POST["Name"])) {
            foreach ($_POST as $key => $value) {
                if(in_array($key, $termination_procedure->Variables)) {
                    $termination_procedure->{$key} = esc($value);
                }
            }
            $termination_procedure->Default = isset($_POST["Default"]) && $_POST["Default"] == "yes" ? "yes" : "no";
            $termination_procedure->ProcedureActions = [];
            if(isset($_POST["helper_has_actions"]) && $_POST["helper_has_actions"] == "yes") {
                $termination_procedure->ProcedureActions = $termination_procedure->parseProcedureActionPost($_POST["ProcedureAction"]);
            }
            Database_Model::getInstance()->beginTransaction();
            if($add_or_edit == "edit" && $termination_procedure->edit() || $add_or_edit == "add" && $termination_procedure->add()) {
                Database_Model::getInstance()->commit();
                flashMessage($termination_procedure);
                header("Location: ?");
                exit;
            }
            Database_Model::getInstance()->rollBack();
        }
        if($add_or_edit == "edit") {
            $this->set("page_form_title", __("termination procedures edit h2"));
            $this->set("procedure_id", $termination_procedure->id);
        } else {
            $this->set("page_form_title", __("termination procedures add h2"));
        }
        $this->set("add_or_edit", $add_or_edit);
        global $array_producttypes;
        $this->set("array_producttypes", $array_producttypes);
        $this->set("array_automated_tasks", $termination_procedure->getProcedureAutomatedTasks());
        require_once "class/template.php";
        $emailtemplate = new emailtemplate();
        $emailtemplates = $emailtemplate->all(["Name"]);
        $this->set("emailtemplates", $emailtemplates);
        $this->set("termination_procedure", $termination_procedure);
        $this->set("message", parse_message($termination_procedure));
        $this->set("sidebar_template", "settings.sidebar.php");
        $this->set("current_page_url", "termination-procedures.php");
        $this->set("page", $add_or_edit);
        $this->set("wfh_page_title", $this->vars["page_form_title"]);
        $this->view("termination.procedures.add.php");
    }
    public function edit()
    {
        return $this->add("edit");
    }
    public function settings()
    {
        checkRight(U_SETTINGS_SHOW);
        if(isset($_POST["TERMINATION_NOTICE_PERIOD"]) && 0 < (int) $_POST["TERMINATION_NOTICE_PERIOD"]) {
            $settings = new settings();
            $settings->Variable = "TERMINATION_NOTICE_PERIOD";
            $settings->Value = (int) esc($_POST["TERMINATION_NOTICE_PERIOD"]);
            $settings->edit();
            $settings->Variable = "TERMINATION_NOTICE_PERIOD_WVD";
            $settings->Value = isset($_POST["TERMINATION_NOTICE_PERIOD_WVD"]) && $_POST["TERMINATION_NOTICE_PERIOD_WVD"] == "yes" ? "yes" : "no";
            $settings->edit();
            $this->Success[] = __("settings are modified");
        } else {
            $this->Error[] = __("termination notice period should be greater than zero");
        }
        flashMessage($this);
        header("Location: termination-procedures.php");
        exit;
    }
    public function ajax_getTerminationProcedure()
    {
        $termination_procedure = new TerminationProcedure_Model();
        $termination_procedure->id = isset($_POST["procedure_id"]) ? intval(esc($_POST["procedure_id"])) : 0;
        if(0 < $termination_procedure->id) {
            $termination_procedure->show();
        }
        if(empty($termination_procedure->ProcedureActions)) {
            $html = __("termination no actions will be executed");
        } else {
            $config = $termination_procedure->getTableConfigActions();
            $config["parameters"]["procedure_id"] = intval(esc($_POST["procedure_id"]));
            ob_start();
            generate_table("termination_actions_dialog", $config);
            $html = ob_get_clean();
        }
        echo json_encode(["term_preference" => $termination_procedure->TermPreference, "html" => $html]);
        exit;
    }
    public function ajax_getTerminationActionsTableEdit()
    {
        $termination_procedure = new TerminationProcedure_Model();
        $termination_procedure->id = isset($_POST["procedure_id"]) ? intval(esc($_POST["procedure_id"])) : 0;
        if(0 < $termination_procedure->id) {
            $termination_procedure->show();
        }
        $this->set("array_automated_tasks", $termination_procedure->getProcedureAutomatedTasks());
        require_once "class/template.php";
        $emailtemplate = new emailtemplate();
        $emailtemplates = $emailtemplate->all(["Name"]);
        $this->set("emailtemplates", $emailtemplates);
        $this->set("termination_procedure", $termination_procedure);
        $this->element("termination.procedures.add.php");
        exit;
    }
}
$termination_procedure = new TerminationProcedures_Controller();
$termination_procedure->router($page);

?>