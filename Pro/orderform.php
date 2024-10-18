<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "config.php";
checkRight(U_ORDERFORM_SHOW);
$page = isset($_GET["page"]) ? $_GET["page"] : "orderform";
$orderform_id = isset($_GET["id"]) ? intval(esc($_GET["id"])) : (isset($_POST["id"]) ? intval(esc($_POST["id"])) : NULL);
require_once "class/orderform.php";
$orderform = new orderform();
$orderform_list = $orderform->all();
if(defined("ORDERFORM_ENABLED") && ($page == "old" || $page == "new")) {
    $_SESSION["ShowOrderForm"] = $page;
} elseif(defined("ORDERFORM_ENABLED") && ORDERFORM_ENABLED == "yes" && !isset($_SESSION["ShowOrderForm"])) {
    $_SESSION["ShowOrderForm"] = empty($orderform_list["Available"]) && empty($orderform_list["Unavailable"]) ? "old" : "new";
}
if(isset($_SESSION["ShowOrderForm"]) && $_SESSION["ShowOrderForm"] == "old") {
    if(isset($_GET["action"]) && $_GET["action"] == "edit" && U_ORDERFORM_EDIT) {
        $settings = new settings();
        foreach ($_POST as $key => $value) {
            $settings->Variable = $key;
            $settings->Value = esc($value);
            $settings->edit();
        }
        if(empty($settings->Error)) {
            $settings->Success[] = __("settings orderform saved");
        }
        flashMessage($settings);
        header("Location: ?");
        exit;
    } else {
        require_once "class/group.php";
        $group = new group();
        $fields = ["GroupName"];
        $groups = $group->all($fields);
        $message = parse_message($group);
        $sidebar_template = "settings.sidebar.php";
        require_once "views/customerpanel.orderform.php";
        exit;
    }
} else {
    switch ($page) {
        case "settings":
            if(!empty($_POST)) {
                $settings = new settings();
                foreach ($_POST as $key => $value) {
                    $settings->Variable = esc($key);
                    $settings->Value = esc($value);
                    $settings->edit();
                }
            }
            if(empty($settings->Error)) {
                $settings->Success[] = __("settings are modified");
            }
            flashMessage($settings);
            header("Location: orderform.php");
            exit;
            break;
        case "delete":
            if(!empty($_POST) && U_ORDERFORM_DELETE && 0 < $orderform_id && isset($_POST["imsure"]) && $_POST["imsure"] == "yes") {
                $orderform->Identifier = $orderform_id;
                $orderform->show();
                $orderform->delete();
                flashMessage($orderform);
            }
            header("Location: orderform.php");
            exit;
            break;
        default:
            if(isset($_GET["action"]) && $_GET["action"] == "add") {
                $page = "add";
            } elseif(isset($_GET["action"]) && $_GET["action"] == "edit") {
                $page = "edit";
            }
            if(!empty($_POST) && isset($_POST["Title"])) {
                if(0 < $orderform_id) {
                    $orderform->Identifier = $orderform_id;
                    $orderform->show();
                }
                foreach ($_POST as $key => $value) {
                    if(in_array($key, $orderform->Variables)) {
                        $orderform->{$key} = esc($value);
                    }
                }
                if($orderform->ProductGroups["domain"] == $orderform->ProductGroups["options"] || $orderform->ProductGroups["hosting"] == $orderform->ProductGroups["options"]) {
                    $orderform->ProductGroups["options"] = "";
                }
                $orderform->ShowPrices = isset($_POST["ShowPrices"]) && $_POST["ShowPrices"] == "yes" ? "yes" : "no";
                $orderform->ShowDiscountCoupon = isset($_POST["ShowDiscountCoupon"]) && $_POST["ShowDiscountCoupon"] == "yes" ? "yes" : "no";
                $orderform->OtherSettings = [];
                $orderform->PeriodChoiceOptions = [];
                if(isset($_POST["domain"])) {
                    $orderform->OtherSettings["domain"] = $_POST["domain"];
                    if(isset($_POST["domain"]["Popular"]) && is_array($_POST["domain"]["Popular"])) {
                        $orderform->OtherSettings["domain"]["PopularList"] = implode("|", $_POST["domain"]["Popular"]);
                    } else {
                        $orderform->OtherSettings["domain"]["PopularList"] = "";
                    }
                }
                if(isset($_POST["hosting"])) {
                    $orderform->OtherSettings["hosting"] = $_POST["hosting"];
                }
                if(isset($_POST["custom"])) {
                    $orderform->OtherSettings["custom"] = $_POST["custom"];
                }
                if(!in_array($orderform->Type, ["other", "domain", "hosting", "custom"]) && isset($_POST[$orderform->Type]) && is_array($_POST[$orderform->Type])) {
                    $orderform->OtherSettings = do_filter("orderform_add_save", $orderform->OtherSettings, $_POST);
                }
                if(isset($_POST["PeriodChoiceOptions_Periods"]) && is_array($_POST["PeriodChoiceOptions_Periods"])) {
                    foreach ($_POST["PeriodChoiceOptions_Periods"] as $k => $v) {
                        $orderform->PeriodChoiceOptions[] = ["Periods" => $v, "Periodic" => $_POST["PeriodChoiceOptions_Periodic"][$k]];
                    }
                }
                if(0 < $orderform_id) {
                    $result = $orderform->edit();
                    $page = "edit";
                } else {
                    $result = $orderform->add();
                    $page = "add";
                }
                if($result) {
                    if($page == "add" && empty($orderform_list["Available"]) && empty($orderform_list["Unavailable"])) {
                        $settings = new settings();
                        $settings->Variable = "DEFAULT_ORDERFORM";
                        $settings->Value = $orderform->Identifier;
                        $settings->edit();
                    }
                    flashMessage($orderform);
                    header("Location: orderform.php");
                    exit;
                }
                $orderform->ProductGroups = json_decode(json_encode($orderform->ProductGroups));
                $orderform->PeriodChoiceOptions = json_decode(json_encode($orderform->PeriodChoiceOptions));
                $orderform->OtherSettings = json_decode(json_encode($orderform->OtherSettings));
            }
            switch ($page) {
                case "add":
                case "edit":
                    if(!isset($orderform) || !is_object($orderform)) {
                        $orderform = new orderform();
                    }
                    $array_languages_orderform = $orderform->getLanguages();
                    require_once "class/product.php";
                    $product = new product();
                    if($orderform_id && empty($orderform->Error)) {
                        $orderform->Identifier = $orderform_id;
                        $orderform->show();
                        $compare_matrix = $packages_descriptions = [];
                        if(isset($orderform->ProductGroups->hosting) && 0 < $orderform->ProductGroups->hosting) {
                            $fields = ["ProductCode", "ProductName", "PriceExcl", "PricePeriod"];
                            $product_list = $product->all($fields, "ProductCode", false, "-1", false, false, $orderform->ProductGroups->hosting);
                            if(0 < $product_list["CountRows"]) {
                                foreach ($product_list as $k => $v) {
                                    if(is_numeric($k)) {
                                        $compare_matrix[$v["id"]]["ProductID"] = $v["id"];
                                        $compare_matrix[$v["id"]]["ProductCode"] = $v["ProductCode"];
                                        $compare_matrix[$v["id"]]["ProductName"] = $v["ProductName"];
                                        $compare_matrix[$v["id"]]["PriceExcl"] = $v["PriceExcl"];
                                        $compare_matrix[$v["id"]]["PricePeriod"] = $v["PricePeriod"];
                                    }
                                }
                            }
                            $packages_descriptions = $compare_matrix;
                        }
                    } else {
                        $compare_matrix = $packages_descriptions = [];
                    }
                    $domain_group_id = isset($_POST["group_id"]) && $_POST["group_id"] ? esc($_POST["group_id"]) : (isset($orderform->ProductGroups->domain) ? $orderform->ProductGroups->domain : 0);
                    if(0 < $domain_group_id) {
                        $products = $product->all(["ProductTld", "PriceExcl", "PricePeriod"], "ProductTld", "ASC", "-1", "ProductType", "domain", $domain_group_id);
                        $tld_products = [];
                        foreach ($products as $k => $v) {
                            if(is_numeric($k)) {
                                $tld_products[$v["ProductTld"]] = $v;
                            }
                        }
                    }
                    require_once "class/group.php";
                    $group = new group();
                    $fields = ["GroupName"];
                    $groups = $group->all($fields);
                    $wfh_page_title = 0 < $orderform->Identifier ? sprintf(__("edit orderform"), $orderform->Title) : __("add orderform");
                    $message = parse_message($group, $orderform);
                    $sidebar_template = "settings.sidebar.php";
                    require_once "views/customerpanel.orderforms.add.php";
                    break;
                default:
                    if(0 < $orderform_id) {
                        $orderform->Identifier = $orderform_id;
                        $orderform->show();
                    } elseif(0 < DEFAULT_ORDERFORM) {
                        $orderform->Identifier = DEFAULT_ORDERFORM;
                        $orderform->show();
                    }
                    $available_orderforms = 0;
                    foreach ($orderform_list["Available"] as $tmp_orderform) {
                        if($tmp_orderform["Type"] == "domain" || $tmp_orderform["Type"] == "hosting") {
                            $available_orderforms++;
                        }
                    }
                    $message = parse_message($orderform);
                    $wfh_page_title = __("settings") . " - " . __("orderforms");
                    $sidebar_template = "settings.sidebar.php";
                    require_once "views/customerpanel.orderforms.php";
            }
    }
}

?>