<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "views/header.php";
echo "\n\n\t";
echo $message;
echo "\n\t<!--heading1-->\n\t<div class=\"heading1\">\n\t<!--heading1-->\n\t\n\t\t<h2>";
echo __("export");
echo "</h2>\n\t\n\t<!--heading1-->\n\t</div><hr />\n\t<!--heading1-->\n\t\n\t<br />\n\t\n\t<div class=\"split2\">\n\n\t\t<div class=\"left\">\n\t\t\n\t\t\t<div style=\"display:block; max-width:400px; margin:0 auto; padding:10px 15px 25px 15px; border:1px solid #CCC; border-radius:5px;\">\n\t\t\t\t<br />\n\t\t\t\t<h2 class=\"align_center\">";
echo __("export csv title");
echo "</h2>\n\t\t\t\t<br />\n\t\t\t\t\n\t\t\t\t<p style=\"color:#414042;\">\n\t\t\t\t\t";
echo __("export csv text");
echo "\t\t\t\t</p>\n\t\t\t\t\n\t\t\t\t<br /><br /><br />\n\t\t\t\t\n\t\t\t\t<p class=\"align_center\">\n\t\t\t\t\t<a class=\"button1 alt1 pointer\" href=\"export.php?page=csv\"><span>";
echo __("export csv button");
echo "</span></a>\n\t\t\t\t</p>\n\t\t\t\t\n\t\t\t</div>\n            \n            <br /><br /><br />\n            \n            ";
if(INT_SUPPORT_ACCOUNTING_MODULES === true) {
    echo "                <div style=\"display:block; max-width:400px; margin:0 auto; padding:10px 15px 25px 15px; border:1px solid #CCC; border-radius:5px;\">\n    \t\t\t\t<br />\n    \t\t\t\t<h2 class=\"align_center\">";
    echo __("export invoice pdf title");
    echo "</h2>\n    \t\t\t\t<br />\n    \t\t\t\t\n    \t\t\t\t<p style=\"color:#414042;\">\n    \t\t\t\t\t";
    echo __("export pdf invoices text");
    echo "<br />\n    \t\t\t\t</p>\n    \t\t\t\t\n    \t\t\t\t<br /><br /><br />\n    \t\t\t\t\n    \t\t\t\t<p class=\"align_center\">\n    \t\t\t\t\t<a class=\"button1 alt1 pointer\" href=\"export.php?page=invoicepdf\"><span>";
    echo __("export to pdf");
    echo "</span></a>\n    \t\t\t\t</p>\n    \t\t\t\t\n    \t\t\t</div>\n                \n                <br /><br /><br />\n                                                        \n    \t\t\t<div style=\"display:block; max-width:400px; margin:0 auto; padding:10px 15px 25px 15px; border:1px solid #CCC; border-radius:5px;\">\n    \t\t\t\t<br />\n    \t\t\t\t<h2 class=\"align_center\">";
    echo __("export purchase invoice title");
    echo "</h2>\n    \t\t\t\t<br />\n    \t\t\t\t\n    \t\t\t\t<p style=\"color:#414042;\">\n    \t\t\t\t\t";
    echo __("export purchase invoices text");
    echo "<br />\n    \t\t\t\t</p>\n    \t\t\t\t\n    \t\t\t\t<br /><br /><br />\n    \t\t\t\t\n    \t\t\t\t<p class=\"align_center\">\n    \t\t\t\t\t<a class=\"button1 alt1 pointer\" href=\"export.php?page=purchaseinvoice\"><span>";
    echo __("export purchase invoice button");
    echo "</span></a>\n    \t\t\t\t</p>\n    \t\t\t\t\n    \t\t\t</div>\n                \n                ";
}
echo "\t\t\n\t\t</div>\n\t\t\n\t\t<div class=\"right\">\n\t\t\n\t\t\t";
if(INT_SUPPORT_ACCOUNTING_MODULES === false) {
    echo "                <div style=\"display:block; max-width:400px; margin:0 auto; padding:10px 15px 25px 15px; border:1px solid #CCC; border-radius:5px;\">\n    \t\t\t\t<br />\n    \t\t\t\t<h2 class=\"align_center\">";
    echo __("export invoice pdf title");
    echo "</h2>\n    \t\t\t\t<br />\n    \t\t\t\t\n    \t\t\t\t<p style=\"color:#414042;\">\n    \t\t\t\t\t";
    echo __("export pdf invoices text");
    echo "<br />\n    \t\t\t\t</p>\n    \t\t\t\t\n    \t\t\t\t<br /><br /><br />\n    \t\t\t\t\n    \t\t\t\t<p class=\"align_center\">\n    \t\t\t\t\t<a class=\"button1 alt1 pointer\" href=\"export.php?page=invoicepdf\"><span>";
    echo __("export to pdf");
    echo "</span></a>\n    \t\t\t\t</p>\n    \t\t\t\t\n    \t\t\t</div>\n                \n                <br /><br /><br />\n                                                \n    \t\t\t<div style=\"display:block; max-width:400px; margin:0 auto; padding:10px 15px 25px 15px; border:1px solid #CCC; border-radius:5px;\">\n    \t\t\t\t<br />\n    \t\t\t\t<h2 class=\"align_center\">";
    echo __("export purchase invoice title");
    echo "</h2>\n    \t\t\t\t<br />\n    \t\t\t\t\n    \t\t\t\t<p style=\"color:#414042;\">\n    \t\t\t\t\t";
    echo __("export purchase invoices text");
    echo "<br />\n    \t\t\t\t</p>\n    \t\t\t\t\n    \t\t\t\t<br /><br /><br />\n    \t\t\t\t\n    \t\t\t\t<p class=\"align_center\">\n    \t\t\t\t\t<a class=\"button1 alt1 pointer\" href=\"export.php?page=purchaseinvoice\"><span>";
    echo __("export purchase invoice button");
    echo "</span></a>\n    \t\t\t\t</p>\n    \t\t\t\t\n    \t\t\t</div>\n                \n                ";
}
if(INT_SUPPORT_ACCOUNTING_MODULES === true) {
    if(!$package_list) {
        echo "    \t\t\n    \t\t\t<div style=\"display:block; max-width:400px; margin:0 auto; padding:10px 15px 25px 15px; border:1px solid #CCC; border-radius:5px;\">\n    \t\t\t\t<br />\n    \t\t\t\t<h2 class=\"align_center\">Boekhoudpakket</h2>\n    \t\t\t\t<p class=\"align_right\" style=\"margin-top:-18px;\">\n    \t\t\t\t\t&euro; 5,- p/m\n    \t\t\t\t</p>\n    \t\t\t\t\n    \t\t\t\t<br /><br />\n    \t\t\t\t\n    \t\t\t\t<p class=\"align_center\" style=\"color:#414042;\">\n    \t\t\t\t\tExporteer uw administratie naar uw boekhoudpakket!<br />\n    \t\t\t\t\t<a class=\"a1 c1\" href=\"https://www.hostfact.nl/boekhoudpakketten/\" target=\"_blank\">Bekijk de mogelijkheden.</a>\n    \t\t\t\t</p>\n    \t\t\t\t\n    \t\t\t\t<br />\n    \t\t\t\t\n    \t\t\t\t<p class=\"align_center\">\n    \t\t\t\t\t<strong>Klik op een logo om het 2 weken gratis uit te proberen:</strong>\n    \t\t\t\t</p>\n    \t\t\t\t<br />\n    \t\t\t\t<div style=\"display:block; width:150px; margin:0 auto;\">\n    \t\t\t\t\t";
        foreach ($available_package_list as $_package => $_package_info) {
            $_package_info = (array) $_package_info;
            if(isset($_package_info["hostfact_integration"]) && $_package_info["hostfact_integration"]) {
                $url = url_generator("exportaccounting", false, ["module" => $_package], ["action" => "start"]);
            } else {
                $url = "export.php?page=accounting_package&amp;action=start_trial&amp;module=" . $_package;
            }
            echo "                            <a href=\"";
            echo $url;
            echo "\">\n                                <img src=\"";
            echo $_package_info["logo"];
            echo "\" alt=\"\" style=\"width:100%; margin-bottom:10px;\" />\n                            </a>\n                            ";
        }
        echo "    \t\t\t\t</div>\n    \n    \t\t\t</div>\n    \t\t\t\n    \t\t\t";
    } else {
        $export_notification = do_filter("export_menu_notification", []);
        foreach ($package_list as $module) {
            $package_name = $package_logo = "";
            if(isset($available_package_list[$module])) {
                $available_package_list[$module] = (array) $available_package_list[$module];
            }
            if(isset($available_package_list[$module]["name"])) {
                $package_name = $available_package_list[$module]["name"];
                $package_logo = $available_package_list[$module]["logo"];
            } elseif(@file_exists("3rdparty/export/" . $module . "/" . $module . ".php") && @file_exists("3rdparty/export/" . $module . "/version.php")) {
                require_once "3rdparty/export/" . $module . "/version.php";
                $package_name = $version["name"];
                $package_logo = $version["logo"];
            } elseif(@file_exists("3rdparty/export/class." . $module . ".php")) {
                require_once "3rdparty/export/class." . $module . ".php";
                $module_classname = "export" . $module;
                $tmp = new $module_classname();
                $package_name = $tmp->getPackageName();
                $package_logo = "";
                unset($tmp);
            }
            if(isset($available_package_list[$module]["hostfact_integration"]) && $available_package_list[$module]["hostfact_integration"]) {
                $url = url_generator("exportaccounting", false, ["module" => $module]);
                $stop_url = url_generator("exportaccounting", false, ["module" => $module], ["action" => "stop"]);
            } else {
                $url = "export.php?page=accounting_package&amp;module=" . $module;
                $stop_url = $url . "&action=stop";
            }
            echo "    \t\t\t\n    \t\t\t\t\t<div style=\"display:block; max-width:400px; margin:0 auto 35px auto; padding:10px 15px 25px 15px; border:1px solid #CCC; border-radius:5px;\">\n\t\t\t\t\t\t\t";
            if(checkRight(U_EXPORT_EDIT, false)) {
                echo "\t\t\t\t\t\t\t\t<a href=\"";
                echo $stop_url;
                echo "\" class=\"a1 c1 float_right\">beëindigen?</a>\n\t\t\t\t\t\t\t\t<br/>";
            }
            echo "    \t\t\t\t\t\t<h2 class=\"align_center\">Boekhoudpakket</h2>\n    \t\t\t\t\t\t<br />\n    \t\t\t\t\t\t\n    \t\t\t\t\t\t<p style=\"color:#414042;\">\n    \t\t\t\t\t\t\tExporteer uw administratie naar het boekhoudpakket ";
            echo $package_name;
            echo ".\n    \t\t\t\t\t\t</p>\n    \t\t\t\t\t\t\n    \t\t\t\t\t\t<br />\n    \t\t\t\t\t\t\n    \t\t\t\t\t\t<div style=\"display:block; width:150px; margin:0 auto;\">\n                                ";
            if(!empty($package_logo)) {
                echo "\t\t\t\t\t\t\t\t\t<a href=\"";
                echo $url;
                echo "\"><img src=\"";
                echo $package_logo;
                echo "\" alt=\"\" style=\"width:100%; margin-bottom:10px;\"/></a>\n                                ";
            }
            echo "\t\t\t\t\t\t\t</div>\n    \t\t\t\t\t\t\n    \t\t\t\t\t\t<br />\n    \t\t\t\t\t\t\n    \t\t\t\t\t\t<p class=\"align_center\">\n    \t\t\t\t\t\t\t<a class=\"button1 alt1 pointer\" href=\"";
            echo $url;
            echo "\"><span>Exporteren naar ";
            echo $package_name;
            echo "</span></a>\n\n\t\t\t\t\t\t\t\t";
            if(isset($export_notification[$module]) && 0 < $export_notification[$module]) {
                echo "<br /><br />\n\t\t\t\t\t\t\t\t\t<strong class=\"strong c_red\">";
                echo sprintf(__("export accounting - x manual actions required"), $export_notification[$module]);
                echo "</strong>\n\t\t\t\t\t\t\t\t\t";
            }
            echo "    \t\t\t\t\t\t</p>\n    \t\t\n    \t\t\t\t\t</div>\n    \t\t\t\t\n    \t\t\t\t\t";
        }
    }
}
echo "\t\t</div>\n\t\n\t</div>\n\n";
require_once "views/footer.php";

?>