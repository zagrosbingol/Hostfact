<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
$module = isset($_GET["module"]) ? $_GET["module"] : (isset($_POST["module"]) && is_string($_POST["module"]) ? $_POST["module"] : "");
$page = isset($_GET["page"]) ? $_GET["page"] : (isset($_POST["page"]) ? $_POST["page"] : "overview");
if($module) {
    require_once "config.php";
}
if($page == "ajax") {
    require_once "config.php";
    $namespace = "modules\\products\\" . $module;
    if(class_exists($namespace . "\\" . $module)) {
        $classname = $namespace . "\\" . $module;
    } else {
        $classname = $module;
    }
    switch ($_POST["action"]) {
        case "product_form_add":
            $module = new $classname();
            $module->product_form_add();
            exit;
            break;
        case "product_form_edit":
            $module = new $classname();
            $module->product_form_edit($_POST["product_id"]);
            exit;
            break;
        case "service_form_add":
            $module = new $classname();
            $module->service_form_add();
            exit;
            break;
        case "service_form_edit":
            $module = new $classname();
            $module->service_form_edit(intval($_POST["service_id"]));
            exit;
            break;
        case "table_reload":
            $options = [];
            if(isset($_POST["sort_by"])) {
                $options["sort_by"] = $_POST["sort_by"];
            } elseif(isset($_POST["page_number"])) {
                $options["page_number"] = $_POST["page_number"];
                $options["results_per_page"] = $_POST["results_per_page"];
            } elseif(isset($_POST["filter"])) {
                $options["filter"] = trim(esc($_POST["filter"]));
                $options["page_number"] = 1;
            }
            $parameters = isset($_POST["parameters"]) ? (array) json_decode($_POST["parameters"], true) : [];
            load_table($_POST["table_id"], $options, $parameters);
            exit;
            break;
    }
} elseif(isset($_POST["action"]) && $_POST["action"] == "removelogentry") {
    require_once "config.php";
    require_once "class/logfile.php";
    $logfile = new logfile();
    $list_log = isset($_POST["ids"]) && is_array($_POST["ids"]) ? $_POST["ids"] : [];
    foreach ($list_log as $log_id) {
        $logfile->deleteEntry($log_id);
    }
    if(empty($logfile->Error)) {
        $logfile->Success[] = sprintf(__("removed count logentries"), count($list_log));
    }
    flashMessage($logfile);
    header("Location: " . esc($_POST["table_redirect_url"]));
    exit;
} elseif($module && isset($_GET["action"]) && in_array($_GET["action"], ["start_trial", "order_module", "end_module"])) {
    include_once "3rdparty/modules/products/product_module.php";
    $product_module = new product_module();
    switch ($_GET["action"]) {
        case "start_trial":
            $product_module->module_start_trial($module);
            break;
        case "order_module":
            $product_module->module_order_module($module);
            break;
        case "end_module":
            $product_module->module_end_module($module);
            break;
        default:
            flashMessage($product_module);
            header("Location: index.php");
            exit;
    }
}
if($module && isset($_module_instances[$module]) && is_object($_module_instances[$module])) {
    $_module_instances[$module]->route_page($page);
} else {
    $load_all_module_languagefiles = true;
    require_once "config.php";
    require_once __DIR__ . "/3rdparty/modules/hostfact_module.php";
    switch ($page) {
        case "settings":
            $module_id = isset($_POST["id"]) ? intval(esc($_POST["id"])) : (isset($_GET["id"]) ? intval(esc($_GET["id"])) : false);
            if(!$module_id) {
                header("Location: modules.php");
                exit;
            }
            $module = new hostfact_module();
            $module_info = $module->getModuleByID($module_id);
            $module = new $module_info->Module();
            $module->module_settings();
            $version = [];
            if(file_exists("3rdparty/modules/" . $module_info->ModuleType . "/" . $module_info->Module . "/version.php")) {
                include "3rdparty/modules/" . $module_info->ModuleType . "/" . $module_info->Module . "/version.php";
            }
            $message = parse_message($module);
            $wfh_page_title = sprintf(__("module settings page"), __("module type " . $module_info->ModuleType) . ": " . (isset($version["name"]) && $version["name"] ? htmlspecialchars($version["name"]) : $module_info->Module));
            $current_page_url = "modules.php";
            $sidebar_template = "settings.sidebar.php";
            require_once "views/modules.settings.php";
            exit;
            break;
        case "deactivate":
            $module_id = isset($_POST["id"]) ? intval(esc($_POST["id"])) : (isset($_GET["id"]) ? intval(esc($_GET["id"])) : false);
            if(!$module_id) {
                header("Location: modules.php");
                exit;
            }
            $module = new hostfact_module();
            $module_info = $module->getModuleByID($module_id);
            $module = new $module_info->Module();
            $module->module_enable_disable($module_info->Module, "disable");
            header("Location: modules.php");
            exit;
            break;
        default:
            checkRight(U_SETTINGS_EDIT);
            $used_in_hostfact_links = [];
            $used_in_hostfact_links["registrars"] = "registrars.php";
            $used_in_hostfact_links["controlpanels"] = "servers.php";
            $used_in_hostfact_links["accounting_export"] = "export.php";
            if(isset($_GET["action"])) {
                sleep(1);
                $api_key = isset($_GET["mod"]) ? esc($_GET["mod"]) : (isset($_POST["id"]) ? esc($_POST["id"]) : "");
                $module_type = isset($_GET["type"]) ? esc($_GET["type"]) : (isset($_POST["type"]) ? esc($_POST["type"]) : "");
                $_SESSION["selected_tab"] = $module_type;
                switch ($module_type) {
                    case "registrars":
                        $path = "3rdparty/domain";
                        break;
                    case "controlpanels":
                        $path = "3rdparty/hosting";
                        break;
                    case "accounting_export":
                        $path = "3rdparty/export";
                        break;
                    case "vps":
                        $path = "3rdparty/modules/products/" . $module_type . "/integrations";
                        break;
                    case "dnsmanagement":
                        $path = "3rdparty/modules/dns/" . $module_type . "/integrations";
                        break;
                    default:
                        $path = "3rdparty/modules/" . $module_type;
                        $original_module_type = $module_type;
                        $module_type = "other";
                        $module_version_information = $_SESSION["module_version_information"];
                        if(!isset($module_version_information[$module_type][$api_key])) {
                            header("Location: modules.php");
                            exit;
                        }
                        switch ($_GET["action"]) {
                            case "install_module":
                                $module = new hostfact_module();
                                if($module->installModule($module_version_information[$module_type][$api_key]["name"], $path, $api_key, $module_version_information[$module_type][$api_key]["download_link"], $module_version_information[$module_type][$api_key]["download_hash"])) {
                                    switch ($module_type) {
                                        case "registrars":
                                        case "controlpanels":
                                        case "accounting_export":
                                        case "other":
                                            require_once "3rdparty/modules/" . $original_module_type . "/" . $original_module_type . "_module.php";
                                            require_once "3rdparty/modules/" . $original_module_type . "/" . $api_key . "/" . $api_key . ".php";
                                            $module = new $api_key();
                                            if(isset($module) && is_object($module) && method_exists($module, "module_install")) {
                                                $module->module_install();
                                            }
                                            break;
                                    }
                                }
                                flashMessage($module);
                                header("Location: modules.php");
                                exit;
                                break;
                            case "update_module":
                                $module = new hostfact_module();
                                if($module->updateModule($module_version_information[$module_type][$api_key]["name"], $path, $api_key, $module_version_information[$module_type][$api_key]["download_link"], $module_version_information[$module_type][$api_key]["download_hash"])) {
                                    if(isset($_SESSION["hostfact_module_version_has_updates"]["only_in_use"][$api_key])) {
                                        unset($_SESSION["hostfact_module_version_has_updates"]["only_in_use"][$api_key]);
                                    }
                                    if(isset($_SESSION["hostfact_module_version_has_updates"]["all"][$api_key])) {
                                        unset($_SESSION["hostfact_module_version_has_updates"]["all"][$api_key]);
                                    }
                                }
                                flashMessage($module);
                                header("Location: modules.php");
                                exit;
                                break;
                            case "remove_module":
                                switch ($module_type) {
                                    case "registrars":
                                        require_once "class/registrar.php";
                                        $registrar = new registrar();
                                        $registrar_api_list = $registrar->getAPIs();
                                        if(!isset($registrar_api_list[$api_key])) {
                                            header("Location: modules.php");
                                            exit;
                                        }
                                        $list_registrars = $registrar->all(["Class"], "Name", "ASC", "-1", "Class", $api_key);
                                        if(0 < $list_registrars["CountRows"]) {
                                            $registrar->Error[] = __("there is still an active registrar, cannot delete files");
                                            flashMessage($registrar);
                                            header("Location: modules.php");
                                            exit;
                                        }
                                        break;
                                    case "controlpanels":
                                        require_once "class/server.php";
                                        $server = new server();
                                        $server_api_list = $server->getControlPanels();
                                        if(!isset($server_api_list[$api_key])) {
                                            header("Location: modules.php");
                                            exit;
                                        }
                                        $list_servers = $server->all(["Panel"], "Name", "ASC", "-1", "Panel", $api_key);
                                        if(0 < $list_servers["CountRows"]) {
                                            $server->Error[] = __("there is still an active server, cannot delete files");
                                            flashMessage($server);
                                            header("Location: modules.php");
                                            exit;
                                        }
                                        unset($_SESSION["wf_cache_controlpanels"]);
                                        break;
                                    case "other":
                                        require_once "3rdparty/modules/" . $original_module_type . "/" . $original_module_type . "_module.php";
                                        require_once "3rdparty/modules/" . $original_module_type . "/" . $api_key . "/" . $api_key . ".php";
                                        $module = new $api_key();
                                        if(isset($module) && is_object($module) && method_exists($module, "module_uninstall") && !$module->module_uninstall()) {
                                            flashMessage($module);
                                            header("Location: modules.php");
                                            exit;
                                        }
                                        break;
                                    case "accounting_export":
                                        require_once "3rdparty/export/class.export.php";
                                        $export_class = new export();
                                        $packages_enabled = $export_class->getPackages();
                                        if($packages_enabled && in_array($api_key, $packages_enabled)) {
                                            $error_class->Error[] = __("accounting export module still active, cannot delete files");
                                            flashMessage($error_class);
                                            header("Location: modules.php");
                                            exit;
                                        }
                                        break;
                                    case "dnsmanagement":
                                        require_once "3rdparty/modules/dns/dns_module.php";
                                        require_once "3rdparty/modules/dns/" . $module_type . "/" . $module_type . ".php";
                                        $namespace = "modules\\dns\\" . $module_type;
                                        if(class_exists($namespace . "\\" . $module_type)) {
                                            $classname = $namespace . "\\" . $module_type;
                                            $module = new $classname();
                                        } else {
                                            $module = new $module_type();
                                        }
                                        if(isset($module) && is_object($module) && method_exists($module, "check_if_integration_is_used") && !$module->check_if_integration_is_used($api_key)) {
                                            flashMessage($module);
                                            header("Location: modules.php");
                                            exit;
                                        }
                                        break;
                                    default:
                                        require_once "3rdparty/modules/products/product_module.php";
                                        require_once "3rdparty/modules/products/" . $module_type . "/" . $module_type . ".php";
                                        $namespace = "modules\\products\\" . $module_type;
                                        if(class_exists($namespace . "\\" . $module_type)) {
                                            $classname = $namespace . "\\" . $module_type;
                                            $module = new $classname();
                                        } else {
                                            $module = new $module_type();
                                        }
                                        if(isset($module) && is_object($module) && method_exists($module, "check_if_integration_is_used") && !$module->check_if_integration_is_used($api_key)) {
                                            flashMessage($module);
                                            header("Location: modules.php");
                                            exit;
                                        }
                                        $module_version_information = $_SESSION["module_version_information"];
                                        $dir = $path . "/" . $api_key;
                                        $module = new hostfact_module();
                                        $module->removeModuleFromServer($module_version_information[$module_type][$api_key]["name"], $dir);
                                        flashMessage($module);
                                        header("Location: modules.php");
                                        exit;
                                }
                                break;
                        }
                }
            }
            $module = new hostfact_module();
            $list_all_modules = $module->listModules();
            if(empty($list_all_modules) && @file_exists("3rdparty/modules/collection/payt/payt.php")) {
                require_once "3rdparty/modules/collection/collection_module.php";
                require_once "3rdparty/modules/collection/payt/payt.php";
                $module = new payt();
                $module->module_install();
                header("Location: modules.php");
                exit;
            }
            $list_modules = ["active" => [], "disabled" => []];
            foreach ($list_all_modules as $key => $tmp_module) {
                $list_modules[$tmp_module->Active][$tmp_module->Module] = $tmp_module;
                $version = [];
                if(file_exists("3rdparty/modules/" . $tmp_module->ModuleType . "/" . $tmp_module->Module . "/version.php")) {
                    include "3rdparty/modules/" . $tmp_module->ModuleType . "/" . $tmp_module->Module . "/version.php";
                }
                $api_list["other"][$tmp_module->Module] = $version;
            }
            unset($list_all_modules);
            $used_in_hostfact = [];
            require_once "class/registrar.php";
            $registrar = new registrar();
            $list_registrars = $registrar->all(["Class"], "Name", "ASC", "-1");
            foreach ($list_registrars as $k => $v) {
                if(is_numeric($k)) {
                    $used_in_hostfact["registrars"][$v["Class"]][] = $v["id"];
                }
            }
            unset($list_registrars);
            $api_list["services"]["registrars"] = $registrar->getAPIs();
            require_once "class/server.php";
            $server = new server();
            $list_servers = $server->all(["Panel"], "Name", "ASC", "-1");
            foreach ($list_servers as $k => $v) {
                if(is_numeric($k)) {
                    $used_in_hostfact["controlpanels"][$v["Panel"]][] = $v["id"];
                }
            }
            unset($list_servers);
            $api_list["services"]["controlpanels"] = $server->getControlPanels();
            $product_module_integrations = [];
            $product_module_integrations = do_filter("module_get_integrations", $product_module_integrations);
            $api_list = array_merge_recursive($api_list, $product_module_integrations);
            require_once "3rdparty/export/class.export.php";
            $export_class = new export();
            $api_list["accounting_export"] = $export_class->getAvailablePackages();
            $module_version_information = hostfact_module::checkModuleVersions();
            $hostfact_updatable_modules = hostfact_module::newModuleUpdatesAvailable();
            global $_module_instances;
            $message = parse_message($module);
            $wfh_page_title = __("module overview");
            $current_page_url = "modules.php";
            $sidebar_template = "settings.sidebar.php";
            require_once "views/modules.overview.php";
            exit;
    }
}

?>