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
class ClientareaSettings_Controller extends Base_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    public function overview()
    {
        checkRight(U_CUSTOMERPANEL_SHOW);
        global $array_customer_languages;
        require_once "class/template.php";
        $emailtemplatelist = new emailtemplate();
        $fields = ["Name"];
        $emailtemplates = $emailtemplatelist->all($fields);
        require_once "class/clientareaprofiles.php";
        $ClientareaProfiles_Model = new ClientareaProfiles_Model();
        $clientarea_profiles = $ClientareaProfiles_Model->listProfiles();
        if(!empty($_POST) && U_CUSTOMERPANEL_EDIT) {
            $settings = new settings();
            foreach ($_POST as $key => $value) {
                $settings->Variable = esc($key);
                $settings->Value = esc($value);
                $settings->edit();
            }
            if(empty($settings->Error)) {
                $settings->Success[] = __("settings are modified");
                flashMessage($settings);
                header("Location: clientareasettings.php");
                exit;
            }
        }
        $settings = new settings();
        $settings->checkClientAreaUrl();
        $this->set("message", parse_message($settings));
        $this->set("emailtemplates", $emailtemplates);
        $this->set("clientarea_profiles", $clientarea_profiles);
        $this->set("array_customer_languages", $array_customer_languages);
        $this->set("sidebar_template", "settings.sidebar.php");
        $this->set("current_page_url", "clientareasettings.php");
        $this->set("page", "clientareasettings");
        $this->set("wfh_page_title", __("clientarea settings"));
        $this->view("settings.clientarea.php");
    }
    public function addProfile($add_or_edit = "add")
    {
        checkRight(U_CUSTOMERPANEL_EDIT);
        require_once "class/clientareaprofiles.php";
        $ClientareaProfiles_Model = new ClientareaProfiles_Model();
        $profile_id = isset($_GET["id"]) ? intval(esc($_GET["id"])) : (isset($_POST["id"]) ? intval(esc($_POST["id"])) : 0);
        $pagetype = $add_or_edit == "edit" && 0 < $profile_id ? "editprofile" : "addprofile";
        if($pagetype == "editprofile") {
            $ClientareaProfiles_Model->id = $profile_id;
            $ClientareaProfiles_Model->show();
        }
        if($pagetype == "addprofile" && empty($_POST)) {
            $ClientareaProfiles_Model->setDefaultValues();
        }
        if(!empty($_POST)) {
            if(isset($_POST["Rights"]["CLIENTAREA_DEBTOR_DATA_CHANGE"]) && ($_POST["Rights"]["CLIENTAREA_DEBTOR_DATA_CHANGE"] == "no" || !isset($_POST["Rights"]["CLIENTAREA_DEBTOR_DATA_CHANGE_NOTIFICATION"]))) {
                $_POST["Rights"]["CLIENTAREA_DEBTOR_DATA_CHANGE_NOTIFICATION"] = "none";
            }
            if(isset($_POST["Rights"]["CLIENTAREA_DEBTOR_PAYMENTDATA_CHANGE"]) && ($_POST["Rights"]["CLIENTAREA_DEBTOR_PAYMENTDATA_CHANGE"] == "no" || !isset($_POST["Rights"]["CLIENTAREA_DEBTOR_PAYMENTDATA_CHANGE_NOTIFICATION"]))) {
                $_POST["Rights"]["CLIENTAREA_DEBTOR_PAYMENTDATA_CHANGE_NOTIFICATION"] = "none";
            }
            if(isset($_POST["Rights"]["CLIENTAREA_DEBTOR_PAYMENTDATA_CHANGE"]) && ($_POST["Rights"]["CLIENTAREA_DEBTOR_PAYMENTDATA_CHANGE"] == "no" || !isset($_POST["Rights"]["CLIENTAREA_DEBTOR_PAYMENTDATA_AUTHORISATION"]))) {
                $_POST["Rights"]["CLIENTAREA_DEBTOR_PAYMENTDATA_AUTHORISATION"] = "no";
            }
            if(isset($_POST["Rights"]["CLIENTAREA_PRICEQUOTE_ACCEPT"]) && ($_POST["Rights"]["CLIENTAREA_PRICEQUOTE_ACCEPT"] == "no" || !isset($_POST["Rights"]["CLIENTAREA_PRICEQUOTE_ACCEPT_NOTIFICATION"]))) {
                $_POST["Rights"]["CLIENTAREA_PRICEQUOTE_ACCEPT_NOTIFICATION"] = "none";
            }
            if(isset($_POST["Rights"]["CLIENTAREA_PRICEQUOTE_ACCEPT"]) && ($_POST["Rights"]["CLIENTAREA_PRICEQUOTE_ACCEPT"] == "no" || !isset($_POST["Rights"]["CLIENTAREA_PRICEQUOTE_ACCEPT_TERMS"]))) {
                $_POST["Rights"]["CLIENTAREA_PRICEQUOTE_ACCEPT_TERMS"] = "no";
            }
            if(isset($_POST["Rights"]["CLIENTAREA_SERVICE_TERMINATE"]) && ($_POST["Rights"]["CLIENTAREA_SERVICE_TERMINATE"] == "no" || !isset($_POST["Rights"]["CLIENTAREA_SERVICE_TERMINATE_NOTIFICATION"]))) {
                $_POST["Rights"]["CLIENTAREA_SERVICE_TERMINATE_NOTIFICATION"] = "none";
            }
            if(isset($_POST["Rights"]["CLIENTAREA_DOMAIN_WHOIS_CHANGE"]) && ($_POST["Rights"]["CLIENTAREA_DOMAIN_WHOIS_CHANGE"] == "no" || !isset($_POST["Rights"]["CLIENTAREA_DOMAIN_WHOIS_CHANGE_NOTIFICATION"]))) {
                $_POST["Rights"]["CLIENTAREA_DOMAIN_WHOIS_CHANGE_NOTIFICATION"] = "none";
            }
            if(isset($_POST["Rights"]["CLIENTAREA_DOMAIN_NAMESERVER_CHANGE"]) && ($_POST["Rights"]["CLIENTAREA_DOMAIN_NAMESERVER_CHANGE"] == "no" || !isset($_POST["Rights"]["CLIENTAREA_DOMAIN_NAMESERVER_CHANGE_NOTIFICATION"]))) {
                $_POST["Rights"]["CLIENTAREA_DOMAIN_NAMESERVER_CHANGE_NOTIFICATION"] = "none";
            }
            if(isset($_POST["Rights"]["CLIENTAREA_DOMAIN_DNSZONE_CHANGE"]) && ($_POST["Rights"]["CLIENTAREA_DOMAIN_DNSZONE_CHANGE"] == "no" || !isset($_POST["Rights"]["CLIENTAREA_DOMAIN_DNSZONE_CHANGE_NOTIFICATION"]))) {
                $_POST["Rights"]["CLIENTAREA_DOMAIN_DNSZONE_CHANGE_NOTIFICATION"] = "none";
            }
            $ClientareaProfiles_Model->Name = trim(esc($_POST["Name"]));
            $ClientareaProfiles_Model->WelcomeTitle = trim(esc($_POST["WelcomeTitle"]));
            $ClientareaProfiles_Model->WelcomeMessage = trim(esc($_POST["WelcomeMessage"]));
            $ClientareaProfiles_Model->Rights = [];
            foreach ($_POST["Rights"] as $_key => $_value) {
                $ClientareaProfiles_Model->Rights[$_key] = esc($_value);
            }
            $ClientareaProfiles_Model->Orderforms = [];
            foreach ($_POST["Orderforms"] as $_key => $_value) {
                $ClientareaProfiles_Model->Orderforms[$_key] = esc($_value);
            }
            $ClientareaProfiles_Model->TwoFactorAuthentication = $_POST["TwoFactorAuthentication"] == "on" ? "on" : "off";
            if($pagetype == "addprofile" && $ClientareaProfiles_Model->add()) {
                flashMessage($ClientareaProfiles_Model);
                header("Location: clientareasettings.php#tab-profiles");
                exit;
            }
            if($pagetype == "editprofile" && $ClientareaProfiles_Model->edit()) {
                flashMessage($ClientareaProfiles_Model);
                header("Location: clientareasettings.php#tab-profiles");
                exit;
            }
        }
        require_once "class/orderform.php";
        $Orderform_Model = new orderform();
        $order_forms = $Orderform_Model->all();
        $order_forms = $order_forms["Available"];
        $this->set("message", parse_message($ClientareaProfiles_Model));
        $this->set("clientarea_profile", $ClientareaProfiles_Model);
        $this->set("pagetype", $pagetype);
        $this->set("order_forms", $order_forms);
        $this->set("sidebar_template", "settings.sidebar.php");
        $this->set("current_page_url", "clientareasettings.php");
        $this->set("page", "clientareasettings");
        $this->set("wfh_page_title", __("clientarea settings") . " - " . __("clientarea profile customer rights"));
        $this->view("settings.clientarea.profile.php");
    }
    public function editProfile()
    {
        $this->addProfile("edit");
    }
    public function deleteprofile()
    {
        checkRight(U_CUSTOMERPANEL_DELETE);
        require_once "class/clientareaprofiles.php";
        $ClientareaProfiles_Model = new ClientareaProfiles_Model();
        $ClientareaProfiles_Model->id = isset($_GET["id"]) ? intval(esc($_GET["id"])) : (isset($_POST["deleteprofileid"]) ? intval(esc($_POST["deleteprofileid"])) : 0);
        if(isset($_POST["imsure"]) && $_POST["imsure"] == "yes") {
            $new_profile = esc($_POST["converttoprofile"]);
            $ClientareaProfiles_Model->delete($new_profile);
        }
        flashMessage($ClientareaProfiles_Model);
        header("Location: clientareasettings.php#tab-profiles");
        exit;
    }
}
$clientarea_settings = new ClientareaSettings_Controller();
$clientarea_settings->router($page);

?>