<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require "config.php";
checkRight(U_LAYOUT_SHOW);
require_once "class/template.php";
$page = isset($_GET["page"]) ? $_GET["page"] : "invoice";
$action = isset($_GET["action"]) ? $_GET["action"] : (isset($_POST["action"]) ? $_POST["action"] : "");
$template_id = isset($_GET["id"]) ? intval($_GET["id"]) : (isset($_POST["id"]) ? intval($_POST["id"]) : "");
$template_editor_version = defined("PDF_MODULE") && PDF_MODULE == "tcpdf" ? "v2" : "v1";
if($template_editor_version == "v2") {
    require_once "class/templateblock.php";
}
if(defined("IS_DEMO") && IS_DEMO) {
    if(in_array($action, ["create_new", "create_new_email", "migrate_editor", "migrate_editor_undo", "migrate_editor_confirm", "add", "delete", "transform", "clone", "edit"])) {
        $action = "";
    } elseif(in_array($action, ["save_position", "save_size", "add_block"])) {
        exit;
    }
    $error_class->Warning[] = __("demo - changing templates is not allowed");
    flashMessage();
    $error_class->Warning = [];
}
if(defined("PDF_MODULE") && PDF_MODULE == "fpdf") {
    $error_class->Warning[] = __("fpdf deprecated - dashboard warning");
}
switch ($action) {
    case "create_new":
        if(isset($_POST["new_type"]) && $_POST["new_type"] == "clone" && $_POST["clone_id"]) {
            $template = new template();
            if($template->createNewTemplateFrom(esc($_POST["clone_id"]), str_replace("show", "", $page))) {
                flashMessage($template);
                header("location: templates.php?page=" . $page . "&id=" . $template->Identifier);
                exit;
            }
            flashMessage($template);
            header("location: templates.php?page=" . str_replace("show", "", $page));
            exit;
        }
        break;
    case "create_new_email":
        if(isset($_POST["new_type"]) && $_POST["new_type"] == "clone" && $_POST["clone_id"]) {
            $template = new emailtemplate();
            $template->Identifier = intval(esc($_POST["clone_id"]));
            if($template->TemplateClone()) {
                flashMessage($template);
                header("location: templates.php?page=" . $page . "&id=" . $template->Identifier);
                exit;
            }
            flashMessage($template);
            header("location: templates.php?page=" . str_replace("show", "", $page));
            exit;
        }
        if(isset($_POST["new_type"]) && $_POST["new_type"] == "default") {
            header("location: templates.php?page=showemail");
            exit;
        }
        break;
    case "migrate_editor_undo":
        $settings = new settings();
        if(PDF_MODULE == "tcpdf") {
            Database_Model::getInstance()->truncate("HostFact_TemplateBlocks")->execute();
            $settings->Variable = "PDF_MODULE";
            $settings->Value = "fpdf";
            $settings->edit();
            $settings->Success[] = __("layout editor migration undone");
        }
        flashMessage($settings);
        header("Location: templates.php?page=" . $page);
        exit;
        break;
    case "migrate_editor_confirm":
        $settings = new settings();
        if(PDF_MODULE == "tcpdf") {
            $fpdf_still_in_database = false;
            $pdo_statement = Database_Model::getInstance()->rawQuery("SHOW TABLES");
            while ($row = $pdo_statement->fetch(PDO::FETCH_NUM)) {
                if(strtolower($row[0]) == strtolower("HostFact_TemplateElements")) {
                    $fpdf_still_in_database = true;
                    break;
                }
            }
            if($fpdf_still_in_database) {
                Database_Model::getInstance()->rawQuery("DROP TABLE `HostFact_TemplateElements`");
            }
            $settings->Success[] = __("layout editor migration confirmed");
        }
        flashMessage($settings);
        header("Location: templates.php?page=" . $page);
        exit;
        break;
    case "migrate_editor":
        $settings = new settings();
        if(PDF_MODULE == "fpdf" && isset($_POST["imsure"]) && $_POST["imsure"] == "yes") {
            require_once "class/templateblock.php";
            $template = new template();
            $template_list = $template->all(["Name", "Type"]);
            foreach ($template_list as $k => $_template) {
                if(is_numeric($k)) {
                    $new_type = "";
                    switch ($_template["Type"]) {
                        case "invoice":
                            $new_type = CREDIT_TEMPLATE == $_template["id"] ? "creditinvoice" : "invoice";
                            break;
                        case "pricequote":
                            $new_type = "pricequote";
                            break;
                        case "other":
                            if(INVOICE_REMINDER_LETTER == $_template["id"]) {
                                $new_type = "reminder";
                            } elseif(INVOICE_SUMMATION_LETTER == $_template["id"]) {
                                $new_type = "summation";
                            } else {
                                $result = Database_Model::getInstance()->getOne("HostFact_Packages", ["PackageName"])->where("Status", ["<" => 9])->where("PdfTemplate", $_template["id"])->execute();
                                if($result) {
                                    $new_type = "hostingdata";
                                }
                            }
                            break;
                        default:
                            if($new_type) {
                                $block_model = new templateblock();
                                $block_model->createDefaultBlocks($new_type, $_template["id"]);
                            }
                    }
                }
            }
            $settings->Variable = "PDF_MODULE";
            $settings->Value = "tcpdf";
            $settings->edit();
            $settings->Success[] = __("layout editor migration done, check templates");
        }
        flashMessage($settings);
        header("Location: templates.php?page=" . $page);
        exit;
        break;
    case "add":
        if($page == "showemail" && U_LAYOUT_ADD) {
            $template = new emailtemplate();
            foreach ($_POST as $key => $value) {
                if(in_array($key, $template->Variables)) {
                    $template->{$key} = esc($value);
                } elseif($key == "File") {
                    $template->Attachment = esc($value);
                }
            }
            if(!check_email_address(esc($_POST["SenderEmail"]), "single")) {
                $template->Error[] = __("invalid emailaddress sender");
            } else {
                $template->Sender = esc($_POST["SenderName"]) . " <" . esc($_POST["SenderEmail"]) . ">";
                if($template->add()) {
                    $template_id = $template->Identifier;
                    $action = "edit";
                    if(isset($_POST["TestEmail"]) && $_POST["TestEmail"] == 1) {
                        $template->test_send($_POST["TestEmailAddress"]);
                    }
                    flashMessage($template);
                    header("location: templates.php?page=email");
                    exit;
                }
                $template_id = NULL;
                $template->Sender = htmlspecialchars($template->Sender);
            }
        } elseif(($page == "invoice" || $page == "pricequote" || $page == "other") && U_LAYOUT_ADD) {
            $_POST["Standard"] = isset($_POST["Standard"]) && $_POST["Standard"] == "on" ? 1 : 0;
            $template = new template();
            $template->Type = $page;
            foreach ($_POST as $key => $value) {
                if(in_array($key, $template->Variables)) {
                    $template->{$key} = esc($value);
                }
            }
            if($template->add()) {
                $template_id = $template->Identifier;
                $action = "edit";
                flashMessage($template);
                header("location: templates.php?page=show" . $page . "&id=" . $template_id);
                exit;
            }
            $action = "add";
            $page = "show" . $page;
            for ($i = 1; $i < count($template->Variables); $i++) {
                $template->{$template->Variables[$i]} = htmlspecialchars($template->{$template->Variables[$i]});
            }
        }
        break;
    case "delete":
        if(!U_LAYOUT_DELETE) {
        } elseif($page == "showemail") {
            if(isset($_POST["imsure"]) && $_POST["imsure"] == "yes") {
                $template = new emailtemplate();
                $template->Identifier = esc($template_id);
                if($template->delete()) {
                    flashMessage($template);
                    header("location: templates.php?page=email");
                    exit;
                }
            }
        } elseif(substr($page, 0, 4) == "show" && isset($_POST["imsure"]) && $_POST["imsure"] == "yes") {
            $template = new template();
            $template->Identifier = esc($template_id);
            if($template->delete()) {
                flashMessage($template);
                header("location: templates.php?page=" . substr($page, 4));
                exit;
            }
            flashMessage($template);
            header("location: templates.php?page=" . $page . "&id=" . $template->Identifier);
            exit;
        }
        break;
    case "transform":
        if(!U_LAYOUT_EDIT) {
        } else {
            $template = new template();
            $template->Identifier = esc($template_id);
            $template->transform();
            flashMessage($template);
            header("location: templates.php?page=show" . $template->Type . "&id=" . $template_id);
            exit;
        }
        break;
    case "clone":
        if(!U_LAYOUT_EDIT) {
        } else {
            $template = new template();
            $template->Identifier = esc($template_id);
            $template->TemplateClone();
            flashMessage($template);
            header("location: templates.php?page=show" . $page . "&id=" . $template->Identifier);
            exit;
        }
        break;
    case "edit":
        if(!U_LAYOUT_EDIT) {
        } elseif($page == "showemail") {
            $template = new emailtemplate();
            $template->Identifier = esc($template_id);
            $template->show();
            $template->Attachment = [];
            foreach ($_POST as $key => $value) {
                if(in_array($key, $template->Variables)) {
                    $template->{$key} = esc($value);
                } elseif($key == "File") {
                    $template->Attachment = esc($value);
                }
            }
            if(!check_email_address(esc($_POST["SenderEmail"]), "single")) {
                $template->Error[] = __("invalid emailaddress sender");
            } else {
                $template->Sender = esc($_POST["SenderName"]) . " <" . esc($_POST["SenderEmail"]) . ">";
                if($template->edit()) {
                    $action = "edit";
                    if(isset($_POST["TestEmail"]) && $_POST["TestEmail"] == 1) {
                        $template->show();
                        $template->Subject = htmlspecialchars_decode($template->Subject);
                        $template->test_send($_POST["TestEmailAddress"]);
                    }
                    flashMessage($template);
                    header("location: templates.php?page=email");
                    exit;
                }
                $template->Sender = htmlspecialchars($template->Sender);
            }
        } elseif($page == "invoice" || $page == "pricequote" || $page == "other") {
            $_POST["Standard"] = isset($_POST["Standard"]) && $_POST["Standard"] == "on" ? 1 : 0;
            $template = new template();
            $template->Identifier = esc($template_id);
            $template->show();
            foreach ($_POST as $key => $value) {
                if(in_array($key, $template->Variables)) {
                    $template->{$key} = esc($value);
                }
            }
            if($template->edit()) {
                flashMessage($template);
                header("Location: templates.php?page=" . $page);
                exit;
            }
            $action = "edit";
            $page = "show" . $page;
            for ($i = 1; $i < count($template->Variables); $i++) {
                $template->{$template->Variables[$i]} = htmlspecialchars($template->{$template->Variables[$i]});
            }
        }
        break;
    case "save_position":
        if(!U_LAYOUT_EDIT) {
            exit;
        }
        $block_model = new templateblock();
        $block_model->savePosition($_POST["block"], $_POST["x"], $_POST["y"]);
        exit;
        break;
    case "save_size":
        if(!U_LAYOUT_EDIT) {
            exit;
        }
        $block_model = new templateblock();
        $block_model->saveSize($_POST["block"], $_POST["w"], $_POST["h"]);
        exit;
        break;
    case "add_block":
        if(!U_LAYOUT_EDIT) {
            exit;
        }
        $template = new template();
        $template->Identifier = $template_id;
        $template->show();
        $block_model = new templateblock();
        if($_POST["type"] == templateblock::BLOCK_TYPE_QR_CODE && templateblock::isQRCodeAllowed($template->Type) === false) {
            exit;
        }
        if(in_array($_POST["type"], [templateblock::BLOCK_TYPE_TEXT, templateblock::BLOCK_TYPE_TABLE, templateblock::BLOCK_TYPE_IMAGE, templateblock::BLOCK_TYPE_QR_CODE])) {
            $block_id = $block_model->createBlock($template_id, esc($_POST["type"]));
            echo $block_id;
        }
        exit;
        break;
    case "edit_block":
        if(!U_LAYOUT_EDIT) {
            exit;
        }
        $template = new template();
        $template->Identifier = $template_id;
        $template->show();
        $block_id = esc($_POST["block"]);
        $block_model = new templateblock();
        if(in_array($block_id, ["emailtemplate", "pdftemplate", "other"])) {
            $pdf_source = $template->readDir();
            $emailtemplate = new emailtemplate();
            $fields = ["Name"];
            $emailtemplates = $emailtemplate->all($fields);
            switch ($block_id) {
                case "emailtemplate":
                    $selected_tab = 1;
                    break;
                case "pdftemplate":
                    $selected_tab = $template->Type == "invoice" || $template->Type == "pricequote" ? 2 : 1;
                    break;
                default:
                    $selected_tab = 0;
                    require_once "views/elements/templateblock.settings.php";
            }
        } else {
            $block = $block_model->getBlock($block_id);
            $available_font_families = $block_model->getAvailableFonts();
            $available_font_sizes = ["4" => "4pt", "5" => "5pt", "6" => "6pt", "7" => "7pt", "8" => "8pt", "9" => "9pt", "10" => "10pt", "11" => "11pt", "12" => "12pt", "14" => "14pt", "16" => "16pt", "18" => "18pt", "20" => "20pt", "22" => "22pt", "24" => "24pt", "26" => "26pt", "28" => "28pt", "30" => "30pt", "32" => "32pt", "48" => "48pt"];
            $available_font_styles = ["" => __("templateblock - fontstyle normal"), "B" => __("templateblock - fontstyle bold"), "I" => __("templateblock - fontstyle italic"), "U" => __("templateblock - fontstyle underlined"), "BI" => __("templateblock - fontstyle bold italic"), "BU" => __("templateblock - fontstyle bold underlined"), "IU" => __("templateblock - fontstyle italic underlined"), "BIU" => __("templateblock - fontstyle bold italic underlined")];
            require_once "views/elements/templateblock.edit.php";
        }
        exit;
        break;
    case "save_block":
        checkRight(U_LAYOUT_EDIT);
        $template = new template();
        $template->Identifier = $template_id;
        $template->show();
        $block_id = esc($_POST["block_id"]);
        $block_model = new templateblock();
        $block = $block_model->getBlock($block_id);
        $block["borders"]["top"] = "no";
        $block["borders"]["right"] = "no";
        $block["borders"]["bottom"] = "no";
        $block["borders"]["left"] = "no";
        foreach ($_POST as $k => $v) {
            if(substr($k, 0, 6) != "block_") {
            } else {
                $key = explode("_", substr($k, 6));
                if(count($key) == 2) {
                    if(isset($block[$key[0]][$key[1]])) {
                        $block[$key[0]][$key[1]] = esc($v);
                    }
                } elseif(count($key) == 1 && isset($block[$key[0]])) {
                    $block[$key[0]] = esc($v);
                }
            }
        }
        if(defined("IS_DEMO") && IS_DEMO) {
        } else {
            $block_model->saveBlock($block_id, $block);
        }
        flashMessage($block_model);
        $_SESSION["scroll_to_block"] = $block_id;
        header("Location: ?page=show" . $template->Type . "&id=" . $template_id);
        exit;
        break;
    case "delete_block":
        checkRight(U_LAYOUT_SHOW);
        $template = new template();
        $template->Identifier = $template_id;
        $template->show();
        $block_model = new templateblock();
        if(defined("IS_DEMO") && IS_DEMO) {
        } else {
            $block_model->deleteBlock(esc($_GET["block_id"]));
        }
        flashMessage($block_model);
        header("Location: ?page=show" . $template->Type . "&id=" . $template_id);
        exit;
        break;
    case "save_settings":
        $template = new template();
        if(0 < $template_id) {
            $template->Identifier = $template_id;
            $template->show();
            foreach ($_POST as $key => $value) {
                if(in_array($key, $template->Variables)) {
                    $template->{$key} = esc($value);
                }
            }
            if(defined("IS_DEMO") && IS_DEMO) {
            } else {
                $template->edit();
            }
            flashMessage($template);
            header("Location: ?page=show" . $template->Type . "&id=" . $template_id);
            exit;
        } else {
            $template->Type = str_replace("show", "", $page);
            foreach ($_POST as $key => $value) {
                if(in_array($key, $template->Variables)) {
                    $template->{$key} = esc($value);
                }
            }
            if(defined("IS_DEMO") && IS_DEMO) {
            } else {
                if($template->add()) {
                    $template_id = $template->Identifier;
                    if($template->Type == "invoice" || $template->Type == "pricequote") {
                        $block_model = new templateblock();
                        $block_model->createDefaultBlocks($template->Type, $template_id);
                    }
                    flashMessage($template);
                    header("location: templates.php?page=" . $page . "&id=" . $template_id);
                    exit;
                }
                $action = "add";
                $page = "show" . $page;
                for ($i = 1; $i < count($template->Variables); $i++) {
                    $template->{$template->Variables[$i]} = htmlspecialchars($template->{$template->Variables[$i]});
                }
            }
        }
        break;
    case "default":
        $template = new template();
        $template->setDefault($template_id, str_replace("show", "", $page));
        flashMessage($template);
        header("location: templates.php?page=" . $page . "&id=" . $template_id);
        exit;
        break;
    default:
        if(defined("IS_DEMO") && IS_DEMO) {
            $action = isset($_GET["action"]) ? $_GET["action"] : "";
        }
        switch ($page) {
            case "invoice":
            case "pricequote":
            case "other":
            case "email":
                $emailtemplate = new emailtemplate();
                $session = isset($_SESSION["template.emails.overview"]) ? $_SESSION["template.emails.overview"] : [];
                $fields = ["Name", "Subject", "Sender", "Message"];
                $sort = isset($session["sort"]) ? $session["sort"] : "Name";
                $order = isset($session["order"]) ? $session["order"] : "ASC";
                $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) ? $session["limit"] : "1");
                $searchat = isset($session["searchat"]) ? $session["searchat"] : "Name";
                $searchfor = isset($session["searchfor"]) ? $session["searchfor"] : "";
                $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : min(25, MAX_RESULTS_LIST));
                $templates_email = $emailtemplate->all($fields, $sort, $order);
                $_SESSION["template.emails.overview"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "searchat" => $searchat, "searchfor" => $searchfor, "limit" => $limit];
                $message = parse_message($emailtemplate);
                $wfh_page_title = __("email templates");
                $current_page_url = "templates.php?page=email";
                $sidebar_template = "templates.sidebar.php";
                require_once "views/templates.overview.email.php";
                break;
            case "showinvoice":
            case "showpricequote":
            case "showother":
                checkRight(U_LAYOUT_EDIT);
                $template = isset($template) ? $template : new template();
                if(0 < $template_id) {
                    $template->Identifier = $template_id;
                    $template->show();
                    $action = $action == "" ? "edit" : $action;
                } else {
                    $action = $action == "" ? "add" : $action;
                    $template->Type = substr($page, 4);
                }
                $emailtemplate = new emailtemplate();
                $fields = ["Name"];
                $emailtemplates = $emailtemplate->all($fields);
                if($template_editor_version == "v2") {
                    $_has_template_canvas = true;
                    $block_model = new templateblock();
                    $template_blocks = $block_model->listBlocks($template_id);
                    $block_model->loadExampleData();
                    if($action == "edit") {
                        $Font_Warning = [];
                        $getAvailableFonts = $block_model->getAvailableFonts();
                        foreach ($template_blocks as $k => $block) {
                            if(isset($block["rows"])) {
                                foreach ($block["rows"] as $k => $row_value) {
                                    if(isset($row_value["text"]["family"]) && $row_value["text"]["family"] != "" && !array_key_exists($row_value["text"]["family"], $getAvailableFonts["custom"]) && !array_key_exists($row_value["text"]["family"], $getAvailableFonts["default"])) {
                                        $Font_Warning[$row_value["text"]["family"]] = "";
                                    }
                                }
                            } elseif($block["text"]["family"] != "" && $block["visibility"] != "none" && !array_key_exists($block["text"]["family"], $getAvailableFonts["custom"]) && !array_key_exists($block["text"]["family"], $getAvailableFonts["default"])) {
                                $Font_Warning[$block["text"]["family"]] = "";
                            }
                        }
                        array_unique($Font_Warning);
                        foreach ($Font_Warning as $font => $value) {
                            $block_model->Warning[] = sprintf(__("pdf font not found"), $font, software_get_relative_path() . templateblock::CUSTOM_FONT_PATH);
                        }
                    }
                    $message = parse_message($block_model, $template, $emailtemplates);
                    $current_page_url = "templates.php";
                    $sidebar_template = "templates.sidebar.php";
                    if($action == "edit") {
                        $available_font_families = $block_model->getAvailableFonts();
                        $wfh_page_title = sprintf(__("templateblock - edit template"), $template->Name);
                        require_once "views/templates.block.show.php";
                    } else {
                        $pdf_source = $template->readDir();
                        $wfh_page_title = __("add template");
                        require_once "views/templates.block.add.php";
                    }
                } else {
                    $pdf_source = $template->readDir();
                    $message = parse_message($template, $emailtemplate);
                    $wfh_page_title = __($template->Type . " template");
                    $current_page_url = "templates.php";
                    $sidebar_template = "templates.sidebar.php";
                    require_once "views/templates.show.php";
                }
                break;
            case "showemail":
                checkRight(U_LAYOUT_EDIT);
                $template = isset($template) ? $template : new emailtemplate();
                if(0 < $template_id) {
                    $template->Identifier = $template_id;
                    $template->show();
                    $action = $action == "" ? "edit" : $action;
                } else {
                    $action = $action == "" ? "add" : $action;
                }
                $sendername = substr($template->Sender, 0, strpos($template->Sender, "&lt;"));
                $senderemail = substr($template->Sender, strpos($template->Sender, "&lt;") + 4, -4);
                $template->SenderName = trim($sendername);
                $template->SenderEmail = trim($senderemail);
                $attachments = $template->readDir();
                $templatelist = new template();
                $fields = ["Name", "Location", "Title", "Author"];
                $templates = $templatelist->all($fields, "", "", "", "Type", "other");
                $attach2 = [];
                foreach ($templates as $k => $v) {
                    if(is_numeric($k)) {
                        $attach2["TemplateOther" . $k] = $v["Name"];
                    }
                }
                asort($attach2);
                $message = parse_message($template, $templatelist);
                $wfh_page_title = __("template");
                $current_page_url = "templates.php";
                $sidebar_template = "templates.sidebar.php";
                require_once "views/templates.show.email.php";
                break;
            case "print":
                require_once "class/template.php";
                $template = new template();
                $template->Identifier = intval(esc($_GET["id"]));
                $template->show();
                switch ($template->Type) {
                    case "invoice":
                    case "pricequote":
                        $referer_page = "showpricequote";
                        break;
                    case "other":
                        $referer_page = "showother";
                        break;
                    default:
                        $referer_page = "showinvoice";
                        if($template_editor_version == "v2") {
                            $block_model = new templateblock();
                            $block_model->loadExampleData();
                            if($template->Type == "pricequote") {
                                unset($block_model->example_data["invoice"]->InvoiceCode);
                                unset($block_model->example_data["hosting"]);
                            } elseif($template->Type == "invoice") {
                                unset($block_model->example_data["pricequote"]->PriceQuoteCode);
                                unset($block_model->example_data["hosting"]);
                            }
                            if(!$template->printLayout($block_model->example_data)) {
                                flashMessage($template);
                                header("location: templates.php?page=" . $referer_page . "&id=" . $template->id);
                                exit;
                            }
                        } elseif(!$template->printLayout()) {
                            flashMessage($template);
                            header("location: templates.php?page=" . $referer_page . "&id=" . $template->id);
                            exit;
                        }
                        exit;
                }
                break;
            case "pdftopng":
                require_once "class/template.php";
                $template = new template();
                $template->Identifier = intval(esc($_GET["id"]));
                $template->show();
                if($template->Location) {
                    $location = realpath($template->Location);
                    $file = preg_replace("/(.*)\\.pdf\$/i", "\\1.png", $location);
                    if(!file_exists($file) && $file != $location && class_exists("imagick")) {
                        try {
                            $attachment = new attachment();
                            $attachment->createPreviewForPDF($location, $file);
                        } catch (Exception $e) {
                            header("HTTP/1.0 404 Not Found");
                            exit;
                        }
                    }
                    if(file_exists($file)) {
                        header("Content-Type: image/png");
                        readfile($file);
                        exit;
                    }
                }
                header("HTTP/1.0 404 Not Found");
                exit;
                break;
            default:
                $template = new template();
                if($page == "invoice" && !$template->getStandard("invoice")) {
                    $error_class->Warning[] = __("there is no default template for invoices");
                }
                if($page == "pricequote" && !$template->getStandard("pricequote")) {
                    $error_class->Warning[] = __("there is no default template for pricequotes");
                }
                $session = isset($_SESSION["template.invoices.overview"]) ? $_SESSION["template.invoices.overview"] : [];
                $fields = ["Name", "Author", "Title", "Location", "PostLocation", "Standard", "EmailTemplate"];
                $sort = isset($session["sort"]) ? $session["sort"] : "Name";
                $order = isset($session["order"]) ? $session["order"] : "ASC";
                $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) ? $session["limit"] : "1");
                $searchat = isset($session["searchat"]) ? $session["searchat"] : "Name";
                $searchfor = isset($session["searchfor"]) ? $session["searchfor"] : "";
                $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : min(25, MAX_RESULTS_LIST));
                $templates_invoices = $template->all($fields, $sort, $order, "", "Type", "invoice");
                $_SESSION["template.invoices.overview"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "searchat" => $searchat, "searchfor" => $searchfor, "limit" => $limit];
                $current_page = $limit;
                $session = isset($_SESSION["template.pricequotes.overview"]) ? $_SESSION["template.pricequotes.overview"] : [];
                $fields = ["Name", "Author", "Title", "Location", "PostLocation", "Standard", "EmailTemplate"];
                $sort = isset($session["sort"]) ? $session["sort"] : "Name";
                $order = isset($session["order"]) ? $session["order"] : "ASC";
                $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) ? $session["limit"] : "1");
                $searchat = isset($session["searchat"]) ? $session["searchat"] : "Name";
                $searchfor = isset($session["searchfor"]) ? $session["searchfor"] : "";
                $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : min(25, MAX_RESULTS_LIST));
                $templates_pricequotes = $template->all($fields, $sort, $order, "", "Type", "pricequote");
                $_SESSION["template.pricequotes.overview"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "searchat" => $searchat, "searchfor" => $searchfor, "limit" => $limit];
                $current_page = $limit;
                $session = isset($_SESSION["template.other.overview"]) ? $_SESSION["template.other.overview"] : [];
                $fields = ["Name", "Author", "Title", "Location", "PostLocation", "Standard"];
                $sort = isset($session["sort"]) ? $session["sort"] : "Name";
                $order = isset($session["order"]) ? $session["order"] : "ASC";
                $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) ? $session["limit"] : "1");
                $searchat = isset($session["searchat"]) ? $session["searchat"] : "Name";
                $searchfor = isset($session["searchfor"]) ? $session["searchfor"] : "";
                $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : min(25, MAX_RESULTS_LIST));
                $templates_other = $template->all($fields, $sort, $order, "", "Type", "other");
                $_SESSION["template.other.overview"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "searchat" => $searchat, "searchfor" => $searchfor, "limit" => $limit];
                $current_page = $limit;
                $emailtemplate = new emailtemplate();
                $fields = ["Name"];
                $emailtemplates = $emailtemplate->all($fields);
                $fpdf_still_in_database = false;
                $pdo_statement = Database_Model::getInstance()->rawQuery("SHOW TABLES");
                while ($row = $pdo_statement->fetch(PDO::FETCH_NUM)) {
                    if(strtolower($row[0]) == strtolower("HostFact_TemplateElements")) {
                        $fpdf_still_in_database = true;
                        break;
                    }
                }
                $message = parse_message($template);
                $sidebar_template = "templates.sidebar.php";
                switch ($page) {
                    case "invoice":
                        $wfh_page_title = __("invoice templates");
                        $current_page_url = "templates.php";
                        require_once "views/templates.overview.invoice.php";
                        break;
                    case "pricequote":
                        $wfh_page_title = __("pricequote templates");
                        $current_page_url = "templates.php?page=pricequote";
                        require_once "views/templates.overview.pricequote.php";
                        break;
                    case "other":
                        $wfh_page_title = __("other templates");
                        $current_page_url = "templates.php?page=other";
                        require_once "views/templates.overview.other.php";
                        break;
                }
        }
}

?>