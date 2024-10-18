<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "views/header.php";
echo "\n";
if(U_SERVICEMANAGEMENT_EDIT && 0 <= $handle->Debtor && $handle->Status < 9 && $handle->Registrar && $handle->RegistrarHandle || $handle->RegistrarHandle == "" && $registrar->Class != "" && $registrar->VersionInfo["handle_support"] === true) {
    echo "<ul class=\"list1\">\n    ";
    if(U_SERVICEMANAGEMENT_EDIT && 0 <= $handle->Debtor && $handle->Status < 9 && $handle->Registrar && $handle->RegistrarHandle) {
        echo "\t   <li><a class=\"ico set1 contactsync wait_dialog large_actionname\" href=\"?page=show&amp;id=";
        echo $handle->Identifier;
        echo "&amp;action=updatecontact\">";
        echo __("sync handles");
        echo "</a></li>\n    ";
    }
    echo "    ";
    if($handle->RegistrarHandle == "" && $registrar->Class != "" && $registrar->VersionInfo["handle_support"] === true) {
        echo "\t   <li><a class=\"ico set1 contactsync wait_dialog large_actionname\" href=\"?page=show&amp;id=";
        echo $handle->Identifier;
        echo "&amp;action=createhandleatregistrar\">";
        echo __("create handle at registrar");
        echo "</a></li>\n    ";
    }
    echo "    \n</ul>\n\n<hr />\n";
}
echo "\n";
echo $message;
echo "\n<!--heading1-->\n<div class=\"heading1\">\n<!--heading1-->\n\n\t<h2>";
echo __("handle");
echo " ";
echo $handle->Handle;
echo "</h2>\n\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n\t<!--box1-->\n\t<div class=\"box2\" id=\"tabs\">\n\t<!--box1-->\n\t\n\t\t<!--top-->\n\t\t<div class=\"top\">\n\t\t<!--top-->\n\t\t\n\t\t\t<ul class=\"list3\">\n\t\t\t\t<li class=\"on\"><a href=\"#tab-general\">";
echo __("general");
echo "</a></li>\n\t\t\t</ul>\n\t\t\n\t\t<!--top-->\n\t\t</div>\n\t\t<!--top-->\n\n\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-general\">\n\t\t<!--content-->\n\n\t\t\t<!--split2-->\n\t\t\t<div class=\"split2\">\n\t\t\t<!--split2-->\n\t\t\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t<div class=\"left\">\n\t\t\t\t<!--left-->\t\n\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("handle data");
echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("internal handle");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
echo $handle->Handle;
echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t";
if(0 < $handle->Registrar) {
    echo "\t\t\t\t\t\t\t<strong class=\"title2\">";
    echo __("registrar");
    echo "</strong>\n\t\t\t\t\t\t\t<span class=\"title2_value\"><a href=\"registrars.php?page=show&amp;id=";
    echo $handle->Registrar;
    echo "\" class=\"a1\">";
    echo $handle->Name;
    echo "</a></span>\n\t\t\t\t\t\t\t";
    if($handle->RegistrarHandle) {
        echo "\t\t\t\t\t\t\t\t<strong class=\"title2\">";
        echo __("registrarhandle");
        echo "</strong>\n\t\t\t\t\t\t\t\t<span class=\"title2_value\">";
        echo $handle->RegistrarHandle;
        echo "</span>\n\t\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t\n\t\t\t\t\t\t";
if(0 < $handle->Debtor) {
    echo "\t\t\t\t\t\t\t<strong class=\"title2\">";
    echo __("debtor");
    echo "</strong>\n\t\t\t\t\t\t\t<span class=\"title2_value\"><a href=\"debtors.php?page=show&amp;id=";
    echo $handle->Debtor;
    echo "\" class=\"a1 c1\">";
    echo $debtor->DebtorCode;
    echo " - ";
    echo $debtor->CompanyName ? $debtor->CompanyName : $debtor->Initials . " " . $debtor->SurName;
    echo "</a></span>\n\t\t\t\t\t\t";
} elseif($handle->Debtor == -1) {
    echo "\t\t\t\t\t\t\t<strong class=\"title2\">";
    echo __("debtor");
    echo "</strong>\n\t\t\t\t\t\t\t<span class=\"title2_value\">";
    echo __("new customer");
    echo "</span>\n\t\t\t\t\t\t";
} else {
    echo "\t\t\t\t\t\t\t<strong class=\"title2\">";
    echo __("debtor");
    echo "</strong>\n\t\t\t\t\t\t\t<span class=\"title2_value\">";
    echo __("not connected to a debtor");
    echo "</span>\n\t\t\t\t\t\t";
}
echo "\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("contact data");
echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("phonenumber");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
echo phoneNumberLink($handle->PhoneNumber);
echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("faxnumber");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
echo $handle->FaxNumber;
echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("emailaddress");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
echo $handle->EmailAddress;
echo "</span>\n\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t</div>\n\t\t\t\t<!--left-->\n\t\t\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t<div class=\"right\">\n\t\t\t\t<!--right-->\n\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("handle data");
echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t";
if($handle->CompanyName) {
    echo "\t\t\t\t\t\t\t<strong class=\"title2\">";
    echo __("companyname");
    echo "</strong>\n\t\t\t\t\t\t\t<span class=\"title2_value\">";
    echo $handle->CompanyName;
    echo "</span>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<strong class=\"title2\">";
    echo __("company number");
    echo "</strong>\n\t\t\t\t\t\t\t<span class=\"title2_value\">";
    echo $handle->CompanyNumber ? $handle->CompanyNumber : "-";
    echo "</span>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<strong class=\"title2\">";
    echo __("vat number");
    echo "</strong>\n\t\t\t\t\t\t\t<span class=\"title2_value\">";
    echo $handle->TaxNumber ? $handle->TaxNumber : "-";
    echo "</span>\n\t\t\t\t\t\t\t\n                            ";
    if(!empty($array_legaltype)) {
        echo "\t\t\t\t\t\t\t<strong class=\"title2\">";
        echo __("legal form");
        echo "</strong>\n\t\t\t\t\t\t\t<span class=\"title2_value\">";
        echo isset($array_legaltype[$handle->LegalForm]) ? $array_legaltype[$handle->LegalForm] : "-";
        echo "</span>\n                            ";
    }
    echo "\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("contact person");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
echo settings::getGenderTranslation($handle->Sex) . " " . $handle->Initials . " " . $handle->SurName;
echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("address");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
echo $handle->Address;
if(IS_INTERNATIONAL && $handle->Address2) {
    echo "<br />" . $handle->Address2;
}
echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("zipcode and city");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
echo $handle->ZipCode . " " . $handle->City;
echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t";
if(IS_INTERNATIONAL) {
    echo "\t\t\t\t\t\t\t<strong class=\"title2\">";
    echo __("state");
    echo "</strong>\n\t\t\t\t\t\t\t<span class=\"title2_value\">";
    echo $handle->StateName;
    echo "</span>\n\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("country");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
echo $array_country[$handle->Country];
echo "</span>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t";
if(0 < count($handle->customfields_list)) {
    echo "\t\t\t\t\t\t\n\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t<div class=\"box3\"><h3>";
    echo __("custom debtor fields h2");
    echo "</h3><div class=\"content\">\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t";
    foreach ($handle->customfields_list as $k => $custom_field) {
        echo show_custom_field($custom_field, isset($handle->custom->{$custom_field["FieldCode"]}) ? $handle->custom->{$custom_field["FieldCode"]} : "");
    }
    echo "\t\t\t\t\t\t\t\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t</div></div>\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t";
}
echo "\t\n\t\t\t\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t</div>\n\t\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t<!--split2-->\n\t\t\t</div>\n\t\t\t<!--split2-->\n\t\t\t\t\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\t\t\t\n\n\t<!--box1-->\n\t</div>\n\t<!--box1-->\n\t\n\t<br />\n\t\t\t\n\t<!--buttonbar-->\n\t<div class=\"buttonbar\">\n\t<!--buttonbar-->\n\t\n\t\t";
if(U_SERVICEMANAGEMENT_EDIT && 0 <= $handle->Debtor) {
    echo "<p class=\"pos1\"><a class=\"button1 edit_icon\" href=\"handles.php?page=edit&amp;id=";
    echo $handle->Identifier;
    echo "\"><span>";
    echo __("edit");
    echo "</span></a></p>";
}
echo "\t\t";
if(U_SERVICEMANAGEMENT_DELETE && $handle->Status < 9) {
    echo "<p class=\"pos2\"><a class=\"button1 delete_icon\" onclick=\"\$('#delete_handle').dialog('open');\"><span>";
    echo __("delete");
    echo "</span></a></p>";
}
echo "\t\t\n\t<!--buttonbar-->\n\t</div>\n\t<!--buttonbar-->\n\t\n\t";
$tabs_array = [];
if($registrar->DomainEnabled == "yes") {
    $tabs_array["domains"] = __("connected domains") . " (" . $list_handle_domains["CountRows"] . ")";
}
$tabs_array = do_filter("handle_show_custom_tab", $tabs_array, ["handle_id" => $handle->Identifier, "registrar_id" => $handle->Registrar]);
echo "\t\n\t<!--box1-->\n\t<div class=\"box2\" id=\"subtabs\">\n\t<!--box1-->\n\t\n\t\t<!--top-->\n\t\t<div class=\"top\">\n\t\t<!--top-->\n\t\t\n\t\t\t<ul class=\"list3\">\n\t\t\t\t";
foreach ($tabs_array as $tab_index => $tab_title) {
    echo "<li><a href=\"#tab-";
    echo $tab_index;
    echo "\">";
    echo $tab_title;
    echo "</a></li>";
}
echo "\t\t\t</ul>\n\t\t\n\t\t<!--top-->\n\t\t</div>\n\t\t<!--top-->\n\t\t\n\t\t";
if(isset($tabs_array["domains"])) {
    echo "\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-domains\">\n\t\t<!--content-->\n\t\t\t";
    require_once "views/elements/domain.table.php";
    $options = ["redirect_page" => "handle", "redirect_id" => $handle->Identifier, "session_name" => "handle.show.domain", "current_page" => $current_page, "current_page_url" => $current_page_url, "hide_columns" => []];
    show_domain_table($list_handle_domains, $options);
    echo "\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\t\t";
}
echo "\t\t\n\t\t";
foreach ($tabs_array as $tab_index => $tab_title) {
    if(!in_array($tab_index, ["domains"])) {
        echo "\t\t\t\t<!--content-->\n\t\t\t\t<div class=\"content\" id=\"tab-";
        echo $tab_index;
        echo "\">\n\t\t\t\t<!--content-->\n\t\t\t\t\t";
        do_action("show_custom_tab_content", $tab_index);
        echo "\t\t\t\n\t\t\t\t<!--content-->\n\t\t\t\t</div>\n\t\t\t\t<!--content-->\n\t\t\t\t";
    }
}
echo "\t<!--box1-->\n\t</div>\n\t<!--box1-->\n\n";
if(U_SERVICEMANAGEMENT_DELETE) {
    echo "<div id=\"delete_handle\" class=\"hide ";
    if(isset($pagetype) && $pagetype == "confirmDelete") {
        echo "autoopen";
    }
    echo "\" title=\"";
    echo __("deletedialog handle title");
    echo "\">\n\t<form id=\"HandleForm\" name=\"form_delete\" method=\"post\" action=\"handles.php?page=delete\">\n\t<input type=\"hidden\" name=\"id\" value=\"";
    echo $handle->Identifier;
    echo "\"/>\n\t";
    echo sprintf(__("deletedialog handle description"), $handle->Handle);
    echo "<br />            \n\t<br />\n\t<input type=\"checkbox\" name=\"imsure\" value=\"yes\" id=\"imsure\"/> <label for=\"imsure\">";
    echo __("delete this handle");
    echo "</label><br />\n\t<br />\n\t<p><a id=\"delete_handle_btn\" class=\"button2 alt1 float_left\"><span>";
    echo __("delete");
    echo "</span></a></p>\n\t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#delete_handle').dialog('close');\"><span>";
    echo __("cancel");
    echo "</span></a></p>\n\t</form>\n</div>\n";
}
echo "\n";
require_once "views/footer.php";

?>