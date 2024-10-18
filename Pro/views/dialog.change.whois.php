<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "class/domain.php";
require_once "class/registrar.php";
require_once "class/handle.php";
if(isset($_POST["page"]) && $_POST["page"] == "debtor.show.domain" && 0 < $_POST["debtor"]) {
    $add_debtor_contacts = true;
    $debtor_id = intval(esc($_POST["debtor"]));
} else {
    $add_debtor_contacts = false;
}
$registrars_array = [];
$owner_change_costs = false;
$total_change_costs = 0;
$domains_change_costs = 0;
$registrar_implementation = false;
foreach ($selected_domains as $domain_id) {
    $domain = new domain();
    $domain->show(esc($domain_id));
    if(!in_array($domain->Registrar, $registrars_array)) {
        $registrars_array[$domain->Registrar]["id"] = $domain->Registrar;
    }
    if(isset($registrars_array[$domain->Registrar]["countDomains"])) {
        $registrars_array[$domain->Registrar]["countDomains"] = $registrars_array[$domain->Registrar]["countDomains"] + 1;
    } else {
        $registrars_array[$domain->Registrar]["countDomains"] = 1;
    }
    if(isset($domain->OwnerChangeCost) && 0 < $domain->OwnerChangeCost) {
        $owner_change_costs = true;
        $total_change_costs = $total_change_costs + $domain->OwnerChangeCost;
        $domains_change_costs++;
    }
    unset($domain);
}
$handle = new handle();
$fields = ["Handle", "HandleType", "Registrar", "Name", "RegistrarHandle", "Debtor", "CompanyName", "SurName"];
$list_general_handles = $handle->all($fields, "Handle", "ASC", -1, "Debtor", "0");
if($add_debtor_contacts === true) {
    $fields = ["Handle", "HandleType", "Registrar", "Name", "RegistrarHandle", "Debtor", "CompanyName", "SurName"];
    $list_debtor_handles = $handle->all($fields, "Handle", "ASC", -1, "Debtor", $debtor_id);
}
foreach ($registrars_array as $registrar_data) {
    $registrar = new registrar();
    $registrar->show($registrar_data["id"]);
    if($registrar->Class) {
        $registrar_implementation = true;
    }
    echo "            <div class=\"change_whois_registrar margintb\">\n                <strong>";
    echo $registrar_data["countDomains"] . " " . strtolower(__("domains")) . " " . $registrar->Name;
    echo "</strong><br />\n                \n                ";
    $general_contact_options = "";
    $general_contact_options .= "<optgroup label=\"" . __("general handles") . "\">";
    $counter = 0;
    foreach ($list_general_handles as $key => $value) {
        if(is_numeric($key) && empty($value["Debtor"]) && $value["Registrar"] == $registrar_data["id"]) {
            $general_contact_options .= "<option value=\"" . $value["id"] . "\">";
            $general_contact_options .= $value["Handle"] . ($value["Name"] ? " " . $value["Name"] : "") . " - " . ($value["CompanyName"] ? $value["CompanyName"] : $value["SurName"]);
            $general_contact_options .= "</option>";
            $counter++;
        }
    }
    $general_contact_options .= "</optgroup>";
    if($counter === 0) {
        $general_contact_options = "";
    }
    $debtor_contact_options = "";
    if($add_debtor_contacts === true) {
        $debtor_contact_options .= "<optgroup label=\"" . __("handles from debtor") . "\">";
        $counter = 0;
        foreach ($list_debtor_handles as $key => $value) {
            if(is_numeric($key) && $value["Registrar"] == $registrar_data["id"]) {
                $debtor_contact_options .= "<option value=\"" . $value["id"] . "\">";
                $debtor_contact_options .= $value["Handle"] . ($value["Name"] ? " " . $value["Name"] : "") . " - " . ($value["CompanyName"] ? $value["CompanyName"] : $value["SurName"]);
                $debtor_contact_options .= "</option>";
                $counter++;
            }
        }
        $debtor_contact_options .= "</optgroup>";
    }
    if($counter === 0) {
        $debtor_contact_options = "";
    }
    echo "                \n                <!-- OWNER -->\n                <label class=\"lineheight2 inlineblock marginT2\">\n                    <span class=\"inlineblock width1\">\n                        ";
    echo __("domain owner handle");
    echo "                    </span>\n                    \n                    <select name=\"registrar[";
    echo $registrar->Identifier;
    echo "][owner]\" class=\"text1 size4f\">\n                        <option selected=\"selected\" value=\"nochange\">";
    echo __("dont change");
    echo "</option>\n                        <option value=\"useDebtor\">";
    echo ucfirst(__("use debtor data"));
    echo "</option>                        \n                        ";
    echo $debtor_contact_options;
    echo $general_contact_options;
    echo "                    </select>\n                            \n                </label>\n                <br />\n                \n                <!-- ADMIN -->\n                <label class=\"lineheight2 inlineblock marginT2\">\n                    <span class=\"inlineblock width1\">\n                        ";
    echo __("domain admin handle");
    echo "                    </span>\n                    <select name=\"registrar[";
    echo $registrar->Identifier;
    echo "][admin]\" class=\"text1 size4f\">\n                        <option selected=\"selected\" value=\"nochange\">";
    echo __("dont change");
    echo "</option>\n                        <option value=\"changeToOwner\">";
    echo __("change contact to owner contact");
    echo "</option>\n                        ";
    if(0 < $registrar->AdminHandle) {
        $handle = new handle();
        $handle->show($registrar->AdminHandle);
        echo "                                <optgroup label=\"";
        echo __("default contact registrar");
        echo "\">';\t\n                                    <option value=\"";
        echo $handle->Identifier;
        echo "\">\n                                        ";
        echo $handle->Handle . ($handle->Name ? " " . $handle->Name : "") . " - " . ($handle->CompanyName ? $handle->CompanyName : $handle->SurName);
        echo "                                    </option>\n                            \t</optgroup>\n                                ";
    }
    echo $debtor_contact_options;
    echo $general_contact_options;
    echo "                    </select>        \n                </label>\n                <br />\n                \n                <!-- TECH -->\n                <label class=\"lineheight2 inlineblock marginT2\">\n                    <span class=\"inlineblock width1\">\n                        ";
    echo __("domain tech handle");
    echo "                    </span>\n                    <select name=\"registrar[";
    echo $registrar->Identifier;
    echo "][tech]\" class=\"text1 size4f\">\n                        <option selected=\"selected\" value=\"nochange\">";
    echo __("dont change");
    echo "</option>\n                        <option value=\"changeToOwner\">";
    echo __("change contact to owner contact");
    echo "</option>\n                        ";
    if(0 < $registrar->TechHandle) {
        $handle = new handle();
        $handle->show($registrar->TechHandle);
        echo "                                <optgroup label=\"";
        echo __("default contact registrar");
        echo "\">';\t\n                                    <option value=\"";
        echo $handle->Identifier;
        echo "\">\n                                        ";
        echo $handle->Handle . ($handle->Name ? " " . $handle->Name : "") . " - " . ($handle->CompanyName ? $handle->CompanyName : $handle->SurName);
        echo "                                    </option>\n                            \t</optgroup>\n                                ";
    }
    echo $debtor_contact_options;
    echo $general_contact_options;
    echo "                    </select>        \n                </label>\n                <br />\n                \n            </div>\n        ";
    unset($registrar);
}
if($registrar_implementation === true) {
    echo "        <label>\n            <input type=\"checkbox\" name=\"update_at_registrar\" value=\"yes\" />\n            ";
    echo __("also update whoisdata at registrar");
    echo "        </label><br />        \n        ";
}
if($owner_change_costs === true) {
    if(VAT_CALC_METHOD == "incl") {
        $total_change_costs = money($total_change_costs) . " " . __("incl vat");
    } else {
        $total_change_costs = money($total_change_costs) . " " . __("excl vat");
    }
    echo "        <label>\n            <input type=\"checkbox\" name=\"Costs_Billing\" value=\"yes\" />\n            \n            ";
    echo sprintf(__("cost domain ownerchange ask 2"), $total_change_costs) . " (" . $domains_change_costs . " " . strtolower(__("domains")) . ")";
    echo "        </label><br />\n        ";
}

?>