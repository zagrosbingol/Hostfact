<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "config.php";
checkRight(U_SETTINGS_SHOW);
$page = isset($_GET["page"]) ? $_GET["page"] : "overview";
$action = isset($_POST["action"]) ? $_POST["action"] : (isset($_GET["action"]) ? $_GET["action"] : "");
$page = $page == "edit" ? "add" : $page;
$field_id = isset($_GET["id"]) ? intval(esc($_GET["id"])) : (isset($_POST["id"]) ? intval(esc($_POST["id"])) : NULL);
require_once "class/customfields.php";
switch ($page) {
    case "add":
        $pagetype = isset($field_id) && 0 < $field_id ? "edit" : "add";
        if(empty($_POST) || !U_SETTINGS_EDIT) {
        } else {
            $customfield = new customfields();
            if(isset($field_id) && 0 < $field_id) {
                $customfield->Identifier = $field_id;
                $customfield->show();
            }
            $customfield->FieldCode = esc($_POST["FieldCode"]);
            $customfield->LabelTitle = esc($_POST["LabelTitle"]);
            if($pagetype == "add" || isset($_POST["LabelType"])) {
                $customfield->LabelType = esc($_POST["LabelType"]);
            }
            if(in_array($customfield->LabelType, ["select", "checkbox", "radio"]) && isset($_POST["LabelOptionsKeys"]) && isset($_POST["LabelOptionsTitles"])) {
                $tmp_options = [];
                foreach ($_POST["LabelOptionsKeys"] as $k => $key) {
                    if(isset($_POST["LabelOptionsTitles"][$k]) && $_POST["LabelOptionsTitles"][$k]) {
                        $tmp_options["opt-" . esc($key)] = esc($_POST["LabelOptionsTitles"][$k]);
                    }
                }
                $customfield->LabelOptions = $tmp_options;
                if($customfield->LabelType == "checkbox") {
                    $customfield->LabelDefault = esc($_POST["LabelDefaultCheckbox"]);
                } else {
                    reset($tmp_options);
                    $customfield->LabelDefault = isset($_POST["LabelDefault"]) && $_POST["LabelDefault"] ? esc($_POST["LabelDefault"]) : substr(key($tmp_options), 4);
                }
            } else {
                $customfield->LabelOptions = "";
                $customfield->LabelDefault = "";
            }
            $customfield->ShowDebtor = isset($_POST["ShowDebtor"]) && $_POST["ShowDebtor"] == "yes" ? "yes" : "no";
            $customfield->ShowHandle = isset($_POST["ShowHandle"]) && $_POST["ShowHandle"] == "yes" ? "yes" : "no";
            $customfield->ShowOrderform = isset($_POST["ShowOrderform"]) && $_POST["ShowOrderform"] == "yes" ? "yes" : "no";
            $customfield->ShowInvoice = isset($_POST["ShowInvoice"]) && $_POST["ShowInvoice"] == "yes" ? "yes" : "no";
            $customfield->ShowPriceQuote = isset($_POST["ShowPriceQuote"]) && $_POST["ShowPriceQuote"] == "yes" ? "yes" : "no";
            $customfield->Regex = isset($_POST["validate_regex_helper"]) && $_POST["validate_regex_helper"] == "yes" && !in_array($customfield->LabelType, ["select", "checkbox", "radio"]) ? esc($_POST["Regex"]) : "";
            if($pagetype == "add" && $customfield->add()) {
                flashMessage($customfield);
                header("Location: customclientfields.php");
                exit;
            }
            if($pagetype == "edit" && $customfield->edit()) {
                flashMessage($customfield);
                header("Location: customclientfields.php");
                exit;
            }
        }
        break;
    case "delete":
        if(!U_SETTINGS_DELETE) {
        } elseif(isset($_POST["imsure"]) && $_POST["imsure"] == "yes") {
            $customfield = new customfields();
            $customfield->Identifier = $field_id;
            $customfield->show();
            $customfield->delete();
            flashMessage($customfield);
            header("Location: customclientfields.php");
            exit;
        }
        break;
    case "add":
        checkRight(U_SETTINGS_EDIT);
        if(!isset($customfield) || !is_object($customfield)) {
            $customfield = new customfields();
            if($pagetype == "edit" && $field_id) {
                $customfield->Identifier = $field_id;
                if(!$customfield->show()) {
                    flashMessage($customfield);
                    header("Location: customclientfields.php");
                    exit;
                }
            }
        }
        $message = parse_message($customfield);
        $wfh_page_title = $pagetype == "edit" ? __("edit custom client field") : __("add custom client field");
        $current_page_url = "customclientfields.php";
        $sidebar_template = "settings.sidebar.php";
        require_once "views/settings.customclientfields.add.php";
        break;
    default:
        $customfields = isset($customfields) && is_object($customfields) ? $customfields : new customfields();
        $customfields_list = $customfields->all();
        $message = parse_message($customfields);
        $wfh_page_title = __("settings") . " - " . __("custom client fields");
        $current_page_url = "customclientfields.php";
        $sidebar_template = "settings.sidebar.php";
        require_once "views/settings.customclientfields.php";
}

?>