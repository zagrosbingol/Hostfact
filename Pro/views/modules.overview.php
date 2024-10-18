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
echo $message;
echo "\n<!--heading1-->\n<div class=\"heading1\">\n<!--heading1-->\n\n\t<h2>";
echo __("module overview");
echo "</h2>\n\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n<!--box1-->\n<div class=\"box2\" id=\"tabs\">\n<!--box1-->\n\n\t<!--top-->\n\t<div class=\"top\">\n\t<!--top-->\n\t\t";
foreach ($api_list as $module => $value) {
    if($module == "services") {
        $module_updatables[$module] = 0;
        foreach ($value as $sub_module => $sub_value) {
            $module_updatables[$module] += count(array_intersect_key($hostfact_updatable_modules, $value[$sub_module]));
        }
    } else {
        $module_updatables[$module] = is_array($api_list[$module]) ? count(array_intersect_key($hostfact_updatable_modules, $api_list[$module])) : 0;
    }
}
$installed_modules = [];
$module_tabs["services"] = __("module overview services") . (0 < $module_updatables["services"] ? " <font class=\"smallfont c3\">" . ($module_updatables["services"] == 1 ? __("module overview tab 1 new module") : sprintf(__("module overview tab x new modules"), $module_updatables["services"])) . "</font>" : "");
if(isset($module_version_information["accounting_export"]) || !empty($api_list["accounting_export"])) {
    $module_tabs["accounting_export"] = __("module overview accounting packages") . (0 < $module_updatables["accounting_export"] ? " <font class=\"smallfont c3\">" . ($module_updatables["accounting_export"] == 1 ? __("module overview tab 1 new module") : sprintf(__("module overview tab x new modules"), $module_updatables["accounting_export"])) . "</font>" : "");
}
if(!empty($module_version_information["other"])) {
    $module_tabs["other"] = __("module overview other modules") . (0 < $module_updatables["other"] ? " <font class=\"smallfont c3\">" . ($module_updatables["other"] == 1 ? __("module overview tab 1 new module") : sprintf(__("module overview tab x new modules"), $module_updatables["other"])) . "</font>" : "");
}
$module_tabs["new"] = __("module overview tab new");
$active_tab = "registrars";
if(isset($_SESSION["selected_tab"]) && $_SESSION["selected_tab"]) {
    $active_tab = $_SESSION["selected_tab"];
    unset($_SESSION["selected_tab"]);
}
echo "\t\t<ul class=\"list3\">\n\t\t\t";
$tab_count = 0;
foreach ($module_tabs as $tab_key => $tab_title) {
    echo "<li";
    if($active_tab == $tab_key) {
        echo " class=\"on\"";
    }
    echo "><a href=\"#tab-";
    echo $tab_key;
    echo "\">";
    echo $tab_title;
    echo "</a></li>";
    if($active_tab == $tab_key) {
        $selected_tab = $tab_count;
    }
    $tab_count++;
}
echo "\t\t</ul>\n\n\t<!--top-->\n\t</div>\n\t<!--top-->\n\t\n\t";
if(isset($api_list["services"])) {
    echo "\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-services\">\n\t\t<!--content-->\n\t\t";
    $loop_array = ["update", "no-update"];
    $_3rdparty_modules = [];
    $_first_title = true;
    $_showed_at_least_one = false;
    foreach ($loop_array as $update_or_not) {
        $_title_shown = false;
        foreach ($api_list["services"] as $_service_type => $_type_api_list) {
            if($update_or_not != "update") {
                $_title_shown = false;
            }
            if(!empty($_type_api_list)) {
                foreach ($_type_api_list as $api_list_key => $api_list_versioninfo) {
                    if(!isset($module_version_information[$_service_type][$api_list_key])) {
                        $_3rdparty_modules[$api_list_key] = $api_list_versioninfo;
                    } elseif($update_or_not == "update" && !isset($hostfact_updatable_modules[$api_list_key])) {
                    } else {
                        if($_title_shown === false) {
                            if($update_or_not == "update" && 0 < $module_updatables["services"]) {
                                echo "<strong>";
                                echo __("module overview available updates");
                                echo "</strong>";
                            } else {
                                if(0 < $module_updatables["services"]) {
                                    echo "<br /><br />";
                                } elseif($_first_title === false) {
                                    echo "<br />";
                                }
                                echo "<strong>";
                                echo __("module overview title " . $_service_type);
                                echo "</strong>";
                            }
                            $_first_title = false;
                            $_title_shown = true;
                        }
                        $_name = $module_version_information[$_service_type][$api_list_key]["name"];
                        $_version = $module_version_information[$_service_type][$api_list_key]["version"];
                        $_minimal_version = $module_version_information[$_service_type][$api_list_key]["minimal_version"];
                        $_logo_url = $module_version_information[$_service_type][$api_list_key]["logo"];
                        $_support_services = isset($module_version_information[$_service_type][$api_list_key]["support"]) ? $module_version_information[$_service_type][$api_list_key]["support"] : [];
                        $_changelog_url = isset($module_version_information[$_service_type][$api_list_key]["changelog_link"]) ? $module_version_information[$_service_type][$api_list_key]["changelog_link"] : false;
                        $installed_modules[$_service_type][] = $api_list_key;
                        $_showed_at_least_one = true;
                        echo "\t\t\t\t\t\t<div class=\"setting_box\">\n\t\t\t\t\t\t\t<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">\n\t\t\t\t\t\t\t<tr class=\"module_tr\">\n\t\t\t\t\t\t\t\t<td width=\"175\">\n\t\t\t\t\t\t\t\t\t";
                        if($_logo_url) {
                            echo "<img src=\"";
                            echo $_logo_url;
                            echo "\" style=\"max-width: 150px;max-height:40px;\" />";
                        }
                        echo "\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t";
                        if(isset($_type_api_list[$api_list_key]["module_link"])) {
                            echo "                                        <a href=\"";
                            echo $_type_api_list[$api_list_key]["module_link"];
                            echo "\" class=\"a1 c1\">\n                                            <strong>";
                            echo htmlspecialchars($_name);
                            echo "</strong>\n                                        </a>\n                                        ";
                        } elseif(isset($used_in_hostfact[$api_list_key]) && count($used_in_hostfact[$api_list_key]) == 1) {
                            echo "<a href=\"";
                            echo $used_in_hostfact_links[$_service_type];
                            echo "?page=show&amp;id=";
                            echo $used_in_hostfact[$api_list_key][0];
                            echo "\" class=\"a1 c1\"><strong>";
                            echo htmlspecialchars($_name);
                            echo "</strong></a>";
                        } elseif(isset($used_in_hostfact[$api_list_key]) && 1 < count($used_in_hostfact[$api_list_key])) {
                            echo "<a href=\"";
                            echo $used_in_hostfact_links[$_service_type];
                            echo "\" class=\"a1 c1\"><strong>";
                            echo htmlspecialchars($_name);
                            echo "</strong></a>";
                        } else {
                            echo "<a href=\"";
                            echo $used_in_hostfact_links[$_service_type];
                            echo "?page=add&module=";
                            echo $api_list_key;
                            echo "\" class=\"a1 c1\"><strong>";
                            echo htmlspecialchars($_name);
                            echo "</strong></a>";
                        }
                        echo "\t\t\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t\t\t";
                        $_supported_services = [];
                        if(in_array("domain", $_support_services)) {
                            $_supported_services[] = __("domains");
                        }
                        if(in_array("ssl", $_support_services)) {
                            $_supported_services[] = __("ssl certificates");
                        }
                        if(in_array("dns", $_support_services)) {
                            $_supported_services[] = __("dns management");
                        }
                        echo implode(", ", $_supported_services);
                        echo "\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t<td width=\"300\">\n\t\t\t\t\t\t\t\t\t";
                        echo __("version") . ": " . $api_list_versioninfo["version"];
                        echo "\n\t\t\t\t\t\t\t\t\t";
                        if($_changelog_url) {
                            echo "<a href=\"";
                            echo $_changelog_url;
                            echo "\" target=\"_blank\" class=\"a1 c_gray\" style=\"margin-left: 25px;\">";
                            echo __("module overview - changelog link");
                            echo "</a>";
                        }
                        echo "\n\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<td width=\"200\" align=\"right\">\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t";
                        if($update_or_not == "update") {
                            echo "\t\t\t\t\t\t\t\t\t\t<a href=\"modules.php?action=update_module&amp;type=";
                            echo $_service_type;
                            echo "&amp;mod=";
                            echo $api_list_key;
                            echo "\" class=\"button1 alt1 update_button\"><span>";
                            echo sprintf(__("module update to new version"), $_version);
                            echo "</span></a>\n\t\t\t\t\t\t\t\t\t\t";
                        } else {
                            echo "<p class=\"module_delete_hover hide\"><a onclick=\"removeModule('";
                            echo $_service_type;
                            echo "', '";
                            echo $api_list_key;
                            echo "', '";
                            echo htmlspecialchars($_name);
                            echo "')\" class=\"a1 c1 pointer\">";
                            echo __("module delete module");
                            echo "</a></p>\n\t\t\t\t\t\t\t\t\t\t";
                        }
                        echo "\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t</table>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\t";
                        unset($_type_api_list[$api_list_key]);
                    }
                }
            }
        }
    }
    if(!empty($_3rdparty_modules)) {
        echo "\t\t\t<br /><br />\n\t\t\t<strong>";
        echo __("module overview modules 3rdparty");
        echo "</strong>\n\t\t\t\n\t\t\t";
        foreach ($_3rdparty_modules as $api_list_key => $api_list_versioninfo) {
            $_showed_at_least_one = true;
            echo "\t\t\t\t<div class=\"setting_box\">\n\t\t\t\t\t<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">\n\t\t\t\t\t<tr class=\"module_tr\">\n\t\t\t\t\t\t<td width=\"175\">\n\t\t\t\t\t\t\t";
            if(isset($_3rdparty_modules[$api_list_key]["module_link"])) {
                echo "                                <a href=\"";
                echo $_3rdparty_modules[$api_list_key]["module_link"];
                echo "\" class=\"a1 c1\">\n                                    <strong>";
                echo htmlspecialchars($api_list_versioninfo["name"]);
                echo "</strong>\n                                </a>\n                                ";
            } else {
                echo "<strong>";
                echo htmlspecialchars($api_list_versioninfo["name"]);
                echo "</strong>";
            }
            echo "\t\t\t\t\t\t</td>\n\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t";
            $_supported_services = [];
            if(isset($api_list_versioninfo["domain_support"]) && $api_list_versioninfo["domain_support"]) {
                $_supported_services[] = __("domains");
            }
            if(isset($api_list_versioninfo["ssl_support"]) && $api_list_versioninfo["ssl_support"]) {
                $_supported_services[] = __("ssl certificates");
            }
            if(isset($api_list_versioninfo["dns_management_support"]) && $api_list_versioninfo["dns_management_support"]) {
                $_supported_services[] = __("dns management");
            }
            echo implode(", ", $_supported_services);
            echo "\t\t\t\t\t\t</td>\n\t\t\t\t\t\t<td width=\"300\">";
            echo __("version") . ": " . $api_list_versioninfo["version"];
            echo "</td>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<td width=\"200\" align=\"right\">\n\t\t\t\t\t\t\t&nbsp;\n\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\n\t\t\t\t\t</tr>\n\t\t\t\t\t</table>\n\t\t\t\t</div>\n\t\t\t\t";
        }
    }
    if($_showed_at_least_one === false) {
        echo "\t\t\t<div class=\"setting_help_box\">\n\t\t        ";
        echo __("module overview no service modules");
        echo "                \n\t\t    </div> \n\t\t\t";
    }
    echo "\t\t   \n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\t\t";
}
if(isset($module_version_information["accounting_export"]) || !empty($api_list["accounting_export"])) {
    echo "\t\t<div class=\"content\" id=\"tab-accounting_export\">\n \t\t";
    if(!empty($api_list["accounting_export"])) {
        $loop_array = ["update", "no-update"];
        foreach ($loop_array as $update_or_not) {
            $_title_shown = false;
            foreach ($api_list["accounting_export"] as $api_list_key => $api_list_versioninfo) {
                if(!isset($module_version_information["accounting_export"][$api_list_key])) {
                } elseif($update_or_not == "update" && !isset($hostfact_updatable_modules[$api_list_key])) {
                } else {
                    if($_title_shown === false) {
                        if($update_or_not == "update" && 0 < $module_updatables["accounting_export"]) {
                            echo "<strong>";
                            echo __("module overview available updates");
                            echo "</strong>";
                        } else {
                            if(0 < $module_updatables["accounting_export"]) {
                                echo "<br /><br />";
                            }
                            echo "<strong>";
                            echo __("module overview modules by software");
                            echo "</strong>";
                        }
                        $_title_shown = true;
                    }
                    $_name = $module_version_information["accounting_export"][$api_list_key]["name"];
                    $_version = $module_version_information["accounting_export"][$api_list_key]["version"];
                    $_minimal_version = $module_version_information["accounting_export"][$api_list_key]["minimal_version"];
                    $_logo_url = $module_version_information["accounting_export"][$api_list_key]["logo"];
                    $_support_services = isset($module_version_information["accounting_export"][$api_list_key]["support"]) ? $module_version_information["accounting_export"][$api_list_key]["support"] : [];
                    $_changelog_url = isset($module_version_information["accounting_export"][$api_list_key]["changelog_link"]) ? $module_version_information["accounting_export"][$api_list_key]["changelog_link"] : false;
                    $installed_modules["accounting_export"][] = $api_list_key;
                    echo "\t\t\t\t\t<div class=\"setting_box\">\n\t\t\t\t\t\t<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">\n\t\t\t\t\t\t<tr class=\"module_tr\">\n\t\t\t\t\t\t\t<td width=\"175\">\n\t\t\t\t\t\t\t\t";
                    if($_logo_url) {
                        echo "<img src=\"";
                        echo $_logo_url;
                        echo "\" style=\"max-width: 150px;max-height:40px;\" />";
                    }
                    echo "\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t<td>\n                                <a href=\"";
                    echo $used_in_hostfact_links["accounting_export"];
                    echo "\" class=\"a1 c1\">\n                                    <strong>";
                    echo htmlspecialchars($_name);
                    echo "</strong>\n                                </a>\n\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t<td width=\"300\">\n\t\t\t\t\t\t\t\t";
                    echo __("version") . ": " . $api_list_versioninfo["version"];
                    echo "\n\t\t\t\t\t\t\t\t";
                    if($_changelog_url) {
                        echo "<a href=\"";
                        echo $_changelog_url;
                        echo "\" target=\"_blank\" class=\"a1 c_gray\" style=\"margin-left: 25px;\">";
                        echo __("module overview - changelog link");
                        echo "</a>";
                    }
                    echo "\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<td width=\"200\" align=\"right\">\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t";
                    if($update_or_not == "update") {
                        echo "\t\t\t\t\t\t\t\t\t<a href=\"modules.php?action=update_module&amp;type=accounting_export&amp;mod=";
                        echo $api_list_key;
                        echo "\" class=\"button1 alt1 update_button\"><span>";
                        echo sprintf(__("module update to new version"), $_version);
                        echo "</span></a>\n\t\t\t\t\t\t\t\t\t";
                    } else {
                        echo "<p class=\"module_delete_hover hide\"><a onclick=\"removeModule('accounting_export', '";
                        echo $api_list_key;
                        echo "', '";
                        echo htmlspecialchars($_name);
                        echo "')\" class=\"a1 c1 pointer\">";
                        echo __("module delete module");
                        echo "</a></p>\n\t\t\t\t\t\t\t\t\t";
                    }
                    echo "\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t</table>\n\t\t\t\t\t</div>\n\t\t\t\t\t";
                    unset($api_list["accounting_export"][$api_list_key]);
                }
            }
        }
        if(!empty($api_list["accounting_export"])) {
            echo "\t\t\t\t<br /><br />\n\t\t\t\t<strong>";
            echo __("module overview modules 3rdparty");
            echo "</strong>\n\t\t\t\t\n\t\t\t\t";
            foreach ($api_list["accounting_export"] as $api_list_key => $api_list_versioninfo) {
                echo "\t\t\t\t\t<div class=\"setting_box\">\n\t\t\t\t\t\t<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">\n\t\t\t\t\t\t<tr class=\"module_tr\">\n\t\t\t\t\t\t\t<td width=\"175\">\n                                <a href=\"";
                echo $used_in_hostfact_links["accounting_export"];
                echo "\" class=\"a1 c1\">\n                                    <strong>";
                echo htmlspecialchars($api_list_versioninfo["name"]);
                echo "</strong>\n                                </a>                                \n\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t<td width=\"300\">";
                echo __("version") . ": " . $api_list_versioninfo["version"];
                echo "</td>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<td width=\"200\" align=\"right\">\n\t\t\t\t\t\t\t\t<p class=\"module_delete_hover hide\"><a onclick=\"removeModule('accounting_export', '";
                echo $api_list_key;
                echo "', '";
                echo htmlspecialchars($api_list_versioninfo["name"]);
                echo "')\" class=\"a1 c1 pointer\">";
                echo __("module delete module");
                echo "</a></p>\n\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t</table>\n\t\t\t\t\t</div>\n\t\t\t\t\t";
            }
        }
    } else {
        echo "\t\t\t<div class=\"setting_help_box\">\n\t\t        ";
        echo __("module overview no accounting export");
        echo "                \n\t\t    </div> \n\t\t\t";
    }
    echo "\t\t</div>\n\t";
}
echo "    \n\t";
if(!empty($module_version_information["other"])) {
    echo "\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-other\">\n\t\t<!--content-->\n\t\n\t\t\t";
    if(0 < $module_updatables["other"]) {
        echo "<strong>";
        echo __("module overview available updates");
        echo "</strong>";
        foreach (array_merge($list_modules["active"], $list_modules["disabled"]) as $key => $tmp_module) {
            $tab_type = "other";
            $api_list_key = $tmp_module->Module;
            $installed_modules["other"][] = $tmp_module->Module;
            $_name = $module_version_information[$tab_type][$api_list_key]["name"];
            $_version = $module_version_information[$tab_type][$api_list_key]["version"];
            $_logo_url = $module_version_information[$tab_type][$api_list_key]["logo"];
            echo "\t\t\t\t\t<div class=\"setting_box\" id=\"box_";
            echo $tmp_module->id;
            echo "\">\n\t\t\t\t\t\t<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td width=\"175\">\n\t\t\t\t\t\t\t\t";
            if($_logo_url) {
                echo "<img src=\"";
                echo $_logo_url;
                echo "\" style=\"max-width: 150px;max-height:40px;\" />";
            }
            echo "\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t<td><strong>";
            echo __("module type " . $tmp_module->ModuleType) . ": " . $_name;
            echo "</strong>\n\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t";
            if(isset($version["description"]) && $version["description"]) {
                echo $version["description"];
            }
            echo "\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<td width=\"200\" align=\"right\">\n\t\t\t\t\t\t\t\t<a href=\"modules.php?action=update_module&amp;type=";
            echo $module_version_information[$tab_type][$api_list_key]["type"];
            echo "&amp;mod=";
            echo $api_list_key;
            echo "\" class=\"button1 alt1 update_button\"><span>";
            echo sprintf(__("module update to new version"), $_version);
            echo "</span></a>\n\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t</table>\n\t\t\t\t\t</div>\n\t\t\t\t\t";
            unset($list_modules["active"][$api_list_key]);
            unset($list_modules["disabled"][$api_list_key]);
        }
        echo "<br />";
    }
    if(!empty($list_modules["active"])) {
        echo "\t\n\t\t\t\t<strong>";
        echo __("active modules");
        echo "</strong><br />\n\t\t\t\t";
        foreach ($list_modules["active"] as $key => $tmp_module) {
            $tab_type = "other";
            $api_list_key = $tmp_module->Module;
            $_name = $module_version_information[$tab_type][$api_list_key]["name"];
            $_version = $module_version_information[$tab_type][$api_list_key]["version"];
            $_minimal_version = $module_version_information[$tab_type][$api_list_key]["minimal_version"];
            $_logo_url = $module_version_information[$tab_type][$api_list_key]["logo"];
            $_support_services = isset($module_version_information[$tab_type][$api_list_key]["support"]) ? $module_version_information[$tab_type][$api_list_key]["support"] : [];
            $installed_modules[$tab_type][] = $api_list_key;
            echo "\t\t\t\t\t<div class=\"setting_box\" id=\"box_";
            echo $tmp_module->id;
            echo "\">\n\t\t\t\t\t\t<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td width=\"175\">\n\t\t\t\t\t\t\t\t";
            if($_logo_url) {
                echo "<img src=\"";
                echo $_logo_url;
                echo "\" style=\"max-width: 150px;max-height:40px;\" />";
            }
            echo "\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t<td><strong>";
            echo __("module type " . $tmp_module->ModuleType) . ": " . $_name;
            echo "</strong>\n\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t";
            if(isset($version["description"]) && $version["description"]) {
                echo $version["description"];
            }
            echo "\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<td width=\"120\" align=\"right\" style=\"height: 65px;\">\n\t\t\t\t\t\t\t\t<p style=\"margin-bottom: 6px;\"><a href=\"?page=settings&amp;id=";
            echo $tmp_module->id;
            echo "\" class=\"button1 alt1\"><span>";
            echo __("settings");
            echo "</span></a></p>\n\t\t\t\t\t\t\t\t<p><a onclick=\"deactivateModule('";
            echo $tmp_module->id;
            echo "')\" class=\"a1 c1 pointer\">";
            echo strtolower(__("modules deactivate"));
            echo "</a></p>\n\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t</table>\n\t\t\t\t\t</div>\n\t\t\t\t\t";
        }
        if(!empty($list_modules["disabled"])) {
            echo "\t\t\t\t\t<br /><br />\n\t\t\t\t\t";
        }
    }
    if(!empty($list_modules["disabled"])) {
        echo "\t\n\t\t\t\t<strong>";
        echo __("disabled modules");
        echo "</strong><br />\n\t\t\t\t";
        foreach ($list_modules["disabled"] as $key => $tmp_module) {
            $installed_modules["other"][] = $tmp_module->Module;
            $version = [];
            if(file_exists("3rdparty/modules/" . $tmp_module->ModuleType . "/" . $tmp_module->Module . "/version.php")) {
                include "3rdparty/modules/" . $tmp_module->ModuleType . "/" . $tmp_module->Module . "/version.php";
            }
            $_name = isset($version["name"]) && $version["name"] ? htmlspecialchars($version["name"]) : $tmp_module->Module;
            echo "\t\t\t\t\t<div class=\"setting_box\" id=\"box_";
            echo $tmp_module->id;
            echo "\">\n\t\t\t\t\t\t<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">\n\t\t\t\t\t\t<tr class=\"module_tr\">\n\t\t\t\t\t\t\t<td width=\"175\">\n\t\t\t\t\t\t\t\t";
            if(isset($version["logo"]) && $version["logo"] && file_exists("3rdparty/modules/" . $tmp_module->ModuleType . "/" . $tmp_module->Module . "/" . $version["logo"])) {
                echo "<img src=\"";
                echo "3rdparty/modules/" . $tmp_module->ModuleType . "/" . $tmp_module->Module . "/" . $version["logo"];
                echo "\" style=\"max-width: 150px;max-height:40px;\" />";
            }
            echo "\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t<td><strong>";
            echo __("module type " . $tmp_module->ModuleType) . ": " . $_name;
            echo "</strong>\n\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t";
            if(isset($version["description"]) && $version["description"]) {
                echo $version["description"];
            }
            echo "\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<td width=\"200\" align=\"center\" valign=\"top\" style=\"padding-top: 8px;height: 65px;\">\n\t\t\t\t\t\t\t\t<p><a href=\"?page=settings&amp;id=";
            echo $tmp_module->id;
            echo "\" class=\"button1 alt1\"><span>";
            echo __("settings");
            echo "</span></a></p>\n\t\t\t\t\t\t\t\t<p class=\"module_delete_hover hide\" style=\"margin-top: 5px;\"><a onclick=\"removeModule('";
            echo $tmp_module->ModuleType;
            echo "', '";
            echo $tmp_module->Module;
            echo "', '";
            echo $_name;
            echo "')\" class=\"a1 c1 pointer\">";
            echo __("module delete module");
            echo "</a></p>\n\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t</table>\n\t\t\t\t\t</div>\n\t\t\t\t";
        }
        echo "\t\t\t\t";
    }
    if((int) $module_updatables["other"] === 0 && empty($list_modules["active"]) && empty($list_modules["disabled"])) {
        echo "\t\t\t\t<div class=\"setting_help_box\">\n\t\t\t        ";
        echo __("module overview no other");
        echo "                \n\t\t\t    </div> \n\t\t\t\t";
    }
    echo "\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\t\t";
}
echo "\t\n\t<!--content-->\n\t<div class=\"content\" id=\"tab-new\">\n\t<!--content-->\n\t\t<br />\n\n\t\t";
if(0 < count($module_version_information) && count($module_version_information) == count(json_decode(json_encode($module_version_information), true), true)) {
    echo parse_message((object) ["Warning" => [sprintf(__("no modules found - php version check needed"), phpversion())]]);
} else {
    if(!empty($module_version_information["registrars"])) {
        echo "\n\t\t\t\t<strong style=\"margin-left: 10px;\">";
        echo __("module overview new - domain registrars");
        echo "</strong><br/>\n\n                ";
        foreach ($module_version_information["registrars"] as $registrar_key => $_registrar_info) {
            if(!in_array("domain", $_registrar_info["support"])) {
            } else {
                $already_installed = isset($installed_modules["registrars"]) && in_array($registrar_key, $installed_modules["registrars"]) ? true : false;
                echo "\t\t\t\t\t<a ";
                if(!$already_installed) {
                    echo "href=\"modules.php?action=install_module&amp;type=registrars&amp;mod=";
                    echo $registrar_key;
                    echo "\" ";
                }
                echo "class=\"module_box";
                if($already_installed) {
                    echo " already_installed";
                }
                echo "\" style=\"background: url('";
                echo $_registrar_info["logo"];
                echo "') 50% 50% no-repeat;    background-size: 100%\"><span>";
                if($already_installed) {
                    echo "<font>" . $_registrar_info["name"] . "<br />" . __("module overview new - already installed") . "</font>";
                } else {
                    echo sprintf(__("module overview new - install"), $_registrar_info["name"]);
                }
                echo "</span>\n\t\t\t\t\t</a>\n                    ";
            }
        }
    }
    if(!empty($module_version_information["registrars"])) {
        echo "\t\t\t\t<br clear=\"both\"/><br/>\n\t\t\t\t<strong style=\"margin-left: 10px;\">";
        echo __("module overview new - ssl registrars");
        echo "</strong><br/>\n\n                ";
        foreach ($module_version_information["registrars"] as $registrar_key => $_registrar_info) {
            if(!in_array("ssl", $_registrar_info["support"])) {
            } else {
                $already_installed = isset($installed_modules["registrars"]) && in_array($registrar_key, $installed_modules["registrars"]) ? true : false;
                echo "\t\t\t\t\t<a ";
                if(!$already_installed) {
                    echo "href=\"modules.php?action=install_module&amp;type=registrars&amp;mod=";
                    echo $registrar_key;
                    echo "\" ";
                }
                echo "class=\"module_box";
                if($already_installed) {
                    echo " already_installed";
                }
                echo "\" style=\"background: url('";
                echo $_registrar_info["logo"];
                echo "') 50% 50% no-repeat;    background-size: 100%\"><span>";
                if($already_installed) {
                    echo "<font>" . $_registrar_info["name"] . "<br />" . __("module overview new - already installed") . "</font>";
                } else {
                    echo sprintf(__("module overview new - install"), $_registrar_info["name"]);
                }
                echo "</span>\n\t\t\t\t\t</a>\n                    ";
            }
        }
    }
    if(!empty($module_version_information["controlpanels"])) {
        echo "\n\t\t\t\t<br clear=\"both\"/><br/>\n\t\t\t\t<strong style=\"margin-left: 10px;\">";
        echo __("module overview new - hosting panels");
        echo "</strong><br/>\n\n                ";
        foreach ($module_version_information["controlpanels"] as $api_key => $_api_info) {
            $already_installed = isset($installed_modules["controlpanels"]) && in_array($api_key, $installed_modules["controlpanels"]) ? true : false;
            echo "\t\t\t\t\t<a ";
            if(!$already_installed) {
                echo "href=\"modules.php?action=install_module&amp;type=controlpanels&amp;mod=";
                echo $api_key;
                echo "\" ";
            }
            echo "class=\"module_box";
            if($already_installed) {
                echo " already_installed";
            }
            echo "\" style=\"background: url('";
            echo $_api_info["logo"];
            echo "') 50% 50% no-repeat;    background-size: 100%\"><span>";
            if($already_installed) {
                echo "<font>" . $_api_info["name"] . "<br />" . __("module overview new - already installed") . "</font>";
            } else {
                echo sprintf(__("module overview new - install"), $_api_info["name"]);
            }
            echo "</span>\n\t\t\t\t\t</a>\n                    ";
        }
    }
    foreach ($module_version_information as $module_name => $module_version) {
        switch ($module_name) {
            case "registrars":
            case "controlpanels":
            case "other":
            case "accounting_export":
            default:
                if(!empty($_module_instances[$module_name]) && !empty($module_version_information[$module_name])) {
                    echo "<br clear=\"both\"/><br />";
                    echo "<strong style=\"margin-left: 10px;\">" . __("module overview new", $module_name) . "</strong><br />";
                    foreach ($module_version_information[$module_name] as $api_key => $_api_info) {
                        $already_installed = isset($installed_modules[$module_name]) && in_array($api_key, $installed_modules[$module_name]) ? true : false;
                        echo "\t\t\t\t\t\t\t\t<a ";
                        if(!$already_installed) {
                            echo "href=\"modules.php?action=install_module&amp;type=";
                            echo $module_name;
                            echo "&amp;mod=";
                            echo $api_key;
                            echo "\" ";
                        }
                        echo "class=\"module_box";
                        if($already_installed) {
                            echo " already_installed";
                        }
                        echo "\" style=\"background: url('";
                        echo $_api_info["logo"];
                        echo "') 50% 50% no-repeat;    background-size: 100%\"><span>";
                        if($already_installed) {
                            echo "<font>" . $_api_info["name"] . "<br />" . __("module overview new - already installed") . "</font>";
                        } else {
                            echo sprintf(__("module overview new - install"), $_api_info["name"]);
                        }
                        echo "</span>\n\t\t\t\t\t\t\t\t</a>\n                                ";
                    }
                }
        }
    }
    echo "\n            ";
    if(!empty($module_version_information["accounting_export"])) {
        echo "\t\t\t\t<br clear=\"both\"/><br/>\n\t\t\t\t<strong style=\"margin-left: 10px;\">";
        echo __("module overview new - accounting export");
        echo "</strong><br/>\n\n                ";
        foreach ($module_version_information["accounting_export"] as $api_key => $_api_info) {
            $already_installed = isset($installed_modules["accounting_export"]) && in_array($api_key, $installed_modules["accounting_export"]) ? true : false;
            echo "\t\t\t\t\t<a ";
            if(!$already_installed) {
                echo "href=\"modules.php?action=install_module&amp;type=accounting_export&amp;mod=";
                echo $api_key;
                echo "\" ";
            }
            echo "class=\"module_box";
            if($already_installed) {
                echo " already_installed";
            }
            echo "\" style=\"background: url('";
            echo $_api_info["logo"];
            echo "') 50% 50% no-repeat;    background-size: 100%\"><span>";
            if($already_installed) {
                echo "<font>" . $_api_info["name"] . "<br />" . __("module overview new - already installed") . "</font>";
            } else {
                echo sprintf(__("module overview new - install"), $_api_info["name"]);
            }
            echo "</span>\n\t\t\t\t\t</a>\n                    ";
        }
    }
    echo "\n            ";
    if(!empty($module_version_information["other"])) {
        echo "\t\t\t\t<br clear=\"both\"/><br/>\n\t\t\t\t<strong style=\"margin-left: 10px;\">";
        echo __("module overview new - other");
        echo "</strong><br/>\n                ";
        foreach ($module_version_information["other"] as $api_key => $_api_info) {
            $already_installed = isset($installed_modules["other"]) && in_array($api_key, $installed_modules["other"]) ? true : false;
            echo "\t\t\t\t\t<a ";
            if(!$already_installed) {
                echo "href=\"modules.php?action=install_module&amp;type=";
                echo $_api_info["type"];
                echo "&amp;mod=";
                echo $api_key;
                echo "\" ";
            }
            echo "class=\"module_box";
            if($already_installed) {
                echo " already_installed";
            }
            echo "\" style=\"background: url('";
            echo $_api_info["logo"];
            echo "') 50% 50% no-repeat;    background-size: 100%\"><span>";
            if($already_installed) {
                echo "<font>" . $_api_info["name"] . "<br />" . __("module overview new - already installed") . "</font>";
            } else {
                echo sprintf(__("module overview new - install"), $_api_info["name"]);
            }
            echo "</span>\n\t\t\t\t\t</a>\n                    ";
        }
    }
    echo "\n            ";
}
echo "\t\t\n\t\t<style type=\"text/css\">\n\t\t\ta.module_box {border:1px solid #999999; color:#414042; border-radius: 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px;  padding:0px; margin: 10px; background: white;width: 170px;height:80px;text-align:center; float:left;display:block;white-space:nowrap}\n\t\t\ta.module_box.clicked { cursor:default; }\n\t\t\ta.module_box span {display:none;font-weight:bold;height:40px;line-height:20px;padding:20px 0px;}\n\t\t\ta.module_box:hover,\n\t\t\ta.module_box.clicked {border: 1px solid #999999;} \n\t\t\ta.module_box:hover span,\n\t\t\ta.module_box.clicked span { display:block; background-color: white; background-color: rgba(255, 255, 255, 0.95); border-radius: 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px;}\n\t\t\t\n\t\t\ta.module_box.already_installed { border:1px solid #cccccc; }\n\t\t\ta.module_box.already_installed:hover {border:1px solid #cccccc; cursor: default;}\n\t\t\ta.module_box.already_installed span { display:block;background-color: white; background-color: rgba(255, 255, 255, 0.9); border-radius: 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px; }\n\t\t\ta.module_box.already_installed span font {display:none}\n\t\t\ta.module_box.already_installed:hover span { background-color: rgba(255, 255, 255, 1); }\n\t\t\ta.module_box.already_installed:hover span font { display:inline; }\n\t\t</style>\n\t<span id=\"loader_div\" class=\"hide loading_green\">&nbsp;<img src=\"images/icon_circle_loader_green.gif\" alt=\"\" class=\"ico inline\" style=\"margin-bottom:-3px;\" />&nbsp;<br />";
echo __("loading");
echo "</span>\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\n<!--box1-->\n</div>\n<!--box1-->\n\n<div id=\"deactivate_module\" class=\"hide\" title=\"";
echo __("modules dialog deactivate title");
echo "\">\n\t<form id=\"ProductForm\" name=\"form_deactivate\" method=\"post\" action=\"modules.php?page=deactivate\">\n\t<input type=\"hidden\" name=\"id\" value=\"\"/>\n\t";
echo __("modules dialog deactivate description");
echo "<br />\n\t<br />\n\t<label><input type=\"checkbox\" id=\"imsure\" name=\"imsure\" value=\"yes\"/> ";
echo __("modules dialog deactivate imsure");
echo "</label><br />\n\t<br />\n\t<p><a id=\"deactivate_module_btn\" class=\"button2 alt1 float_left\"><span>";
echo __("modules deactivate");
echo "</span></a></p>\n\t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#deactivate_module').dialog('close');\"><span>";
echo __("cancel");
echo "</span></a></p>\n\t</form>\n</div>\n\n<div id=\"remove_module\" class=\"hide\" title=\"";
echo __("modules dialog remove module title");
echo "\">\n\t<form id=\"ModuleForm\" name=\"form_remove_module\" method=\"post\" action=\"modules.php?action=remove_module\">\n\t<input type=\"hidden\" name=\"id\" value=\"\"/>\n\t<input type=\"hidden\" name=\"type\" value=\"\"/>\n\t";
echo __("modules dialog remove module description");
echo "<br />\n\t<br />\n\t<label><input type=\"checkbox\" id=\"imsure\" name=\"imsure\" value=\"yes\"/> ";
echo sprintf(__("modules dialog remove module imsure"), "<span class=\"module_name\"></span>");
echo "</label><br />\n\t<br />\n\t<p>\n\t\t<a id=\"remove_module_btn\" class=\"button2 alt1 float_left\"><span>";
echo __("delete");
echo "</span></a>\n\t\t<span class=\"loader_saving float_left hide\" style=\"padding: 6px 0;\">\n           <img src=\"images/icon_circle_loader_green.gif\" alt=\"\" class=\"ico inline\" style=\"margin-bottom:-3px;\" />&nbsp;&nbsp;\n           <span class=\"loading_green\">";
echo __("loading");
echo "</span>&nbsp;&nbsp;\n        </span>\n\t</p>\n\t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#remove_module').dialog('close');\"><span>";
echo __("cancel");
echo "</span></a></p>\n\t</form>\n</div>\n\n<script type=\"text/javascript\">\n\$(function(){\n\t\$('#deactivate_module').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});\n\t\$('input[name=\"imsure\"]').click(function(){\n\t\tif(\$('input[name=\"imsure\"]:checked').val() != null)\n\t\t{\n\t\t\t\$('#deactivate_module_btn').removeClass('button2').addClass('button1');\n\t\t}\n\t\telse\n\t\t{\n\t\t\t\$('#deactivate_module_btn').removeClass('button1').addClass('button2');\n\t\t}\n\t});\n\t\$('#deactivate_module_btn').click(function(){\n\t\tif(\$('input[name=\"imsure\"]:checked').val() != null)\n\t\t{\n\t\t\tdocument.form_deactivate.submit();\n\t\t}\t\n\t});\n\t\n\t\$('.module_tr').hover(function(){\n\t\t\$(this).find('p.module_delete_hover').removeClass('hide');\t\n\t}, function(){\n\t\t\$(this).find('p.module_delete_hover').addClass('hide');\n\t});\n\t\n\t\$('#remove_module').dialog({modal: true, autoOpen: false, resizable: false, width: 450, height: 'auto'});\n\t\$('input[name=\"imsure\"]').click(function(){\n\t\tif(\$('input[name=\"imsure\"]:checked').val() != null)\n\t\t{\n\t\t\t\$('#remove_module_btn').removeClass('button2').addClass('button1');\n\t\t}\n\t\telse\n\t\t{\n\t\t\t\$('#remove_module_btn').removeClass('button1').addClass('button2');\n\t\t}\n\t});\n\t\$('#remove_module_btn').click(function(){\n\t\tif(\$('input[name=\"imsure\"]:checked').val() != null)\n\t\t{\n\t\t\t\$(this).parent().find('.loader_saving').show();\n\t\t\t\$(this).hide();\n\t\t\tdocument.form_remove_module.submit();\n\t\t}\t\n\t});\n\t\n\t\$('a.module_box').click(function(event){\n\t\tevent.preventDefault();\n\t\tif(\$(this).attr('href') && !\$(this).hasClass('clicked'))\n\t\t{\n\t\t\t\$(this).addClass('clicked');\n\t\t\t\$(this).html('').append(\$('#loader_div').css('display', 'block'));\n\t\t\t\n\t\t\twindow.location = \$(this).attr('href');\n\t\t\treturn true;\n\t\t}\n\t\telse\n\t\t{\n\t\t\treturn false;\n\t\t}\n\t});\n\t\n\t\$('a.update_button').click(function(){\n\t\t\$(this).before(\$('#loader_div').show()).remove();\n\t});\n\t\n\t\n\t";
if(isset($selected_tab) && $selected_tab) {
    echo "\t\$('#tabs').tabs(\"option\", \"active\", ";
    echo $selected_tab;
    echo ");\n\t";
}
echo "});\n\nfunction deactivateModule(module_id){\n\t\$('#deactivate_module').find('input[name=\"id\"]').val(module_id);\n\t\$('#deactivate_module').dialog('open');\n}\nfunction removeModule(type, api_key, module_name){\n\t\$('#remove_module').find('input[name=\"id\"]').val(api_key);\n\t\$('#remove_module').find('input[name=\"type\"]').val(type);\n\t\$('#remove_module').find('span.module_name').html(module_name);\n\t\$('#remove_module').dialog('open');\n}\n</script>\n\n";
require_once "views/footer.php";

?>