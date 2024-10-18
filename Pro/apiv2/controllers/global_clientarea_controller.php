<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class global_clientarea_controller extends api_controller
{
    public function __construct()
    {
        parent::__construct();
        HostFact_API::setObjectNames("clientarea", "clientarea");
    }
    public function settings_api_action()
    {
        HostFact_API::setObjectNames("settings", "settings");
        $settings_clientarea_whitelist = ["CLIENTAREA_URL", "BACKOFFICE_URL", "ORDERFORM_URL", "CLIENTAREA_DEFAULT_LANG", "API_KEY", "DATE_FORMAT", "CURRENCY_SIGN_LEFT", "CURRENCY_SIGN_RIGHT", "AMOUNT_DEC_PLACES", "AMOUNT_DEC_SEPERATOR", "AMOUNT_THOU_SEPERATOR", "CLIENTAREA_LOGO_URL", "CLIENTAREA_HEADER_TITLE", "CLIENTAREA_LOGOUT_URL", "CLIENTAREA_TERMS_URL", "TERMINATION_NOTICE_PERIOD", "TERMINATION_NOTICE_PERIOD_WVD", "TICKET_USE", "CLIENTAREA_USE_TICKETSYSTEM"];
        $result = Database_Model::getInstance()->get("HostFact_Settings")->where("Variable", ["IN" => $settings_clientarea_whitelist])->execute();
        if(!empty($result)) {
            foreach ($result as $key => $value) {
                $response_array[$value->Variable] = $value->Value;
            }
        }
        $result = Database_Model::getInstance()->getOne("HostFact_PaymentMethods")->where("PaymentType", "auth")->where("Availability", [">" => 0])->execute();
        if($result && 0 < $result->id) {
            $response_array["AUTHORISATION_AVAILABLE"] = true;
        } else {
            $response_array["AUTHORISATION_AVAILABLE"] = false;
        }
        global $_module_instances;
        $response_array["ACTIVE_SERVICE_MODULES"] = [];
        if(!empty($_module_instances)) {
            foreach ($_module_instances as $_instance_name => $_module_instance) {
                $response_array["ACTIVE_SERVICE_MODULES"][] = $_instance_name;
            }
        }
        HostFact_API::parseResponse($response_array, true);
    }
    public function arraylists_api_action()
    {
        HostFact_API::setObjectNames("arraylists", "arraylists");
        $response_array = [];
        $result = Database_Model::getInstance()->get("HostFact_Settings_Countries")->where("Visible", "yes")->orderBy("OrderID", "ASC")->asArray()->execute();
        if($result) {
            foreach ($result as $_row) {
                $response_array["countries"][$_row["CountryCode"]] = $_row;
            }
        }
        $result = Database_Model::getInstance()->get("HostFact_Settings_States")->orderBy("State", "ASC")->execute();
        if($result) {
            foreach ($result as $_row) {
                $response_array["states"][$_row->CountryCode][$_row->StateCode] = $_row->State;
            }
        }
        $result = Database_Model::getInstance()->get("HostFact_Settings_LegalForms")->orderBy("OrderID", "ASC")->orderBy("Title", "ASC")->execute();
        if($result) {
            foreach ($result as $_row) {
                $response_array["legalforms"][$_row->LegalForm] = $_row->Title;
            }
        }
        $result = Database_Model::getInstance()->get("HostFact_Settings_Taxrates")->where("TaxType", "line")->orderBy("Rate", "DESC")->execute();
        if($result) {
            foreach ($result as $_row) {
                $response_array["taxpercentages"]["" . (double) $_row->Rate] = 100 * (double) $_row->Rate;
                $response_array["taxpercentages_info"]["" . (double) $_row->Rate] = ["label" => htmlspecialchars($_row->Label)];
            }
        }
        $result = Database_Model::getInstance()->get("HostFact_Settings_Taxrates")->where("TaxType", "total")->orderBy("Rate", "DESC")->execute();
        if($result) {
            foreach ($result as $_row) {
                $response_array["total_taxpercentages"]["" . (double) $_row->Rate] = 100 * (double) $_row->Rate;
                $response_array["total_taxpercentages_info"]["" . (double) $_row->Rate] = ["label" => htmlspecialchars($_row->Label), "compound" => $_row->Compound];
            }
        }
        $response_array["sexes"] = ["m" => __("gender male"), "f" => __("gender female"), "d" => __("gender department"), "u" => __("gender unknown")];
        $result = Database_Model::getInstance()->get("HostFact_PaymentMethods", ["id", "Title"])->where("Availability", ["IN" => [2, 3]])->where("PaymentType", ["IN" => ["ideal", "paypal", "other"]])->asArray()->execute();
        $response_array["payment_methods"] = $result;
        global $array_customer_languages;
        $response_array["languages"] = $array_customer_languages;
        HostFact_API::parseResponse($response_array, true);
    }
}

?>