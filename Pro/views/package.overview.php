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
echo "\n";
if(0 < $list_servers["CountRows"]) {
    if(U_SERVICEMANAGEMENT_EDIT) {
        echo "    <!--optionsbar-->\n    <div class=\"optionsbar\">\n    <!--optionsbar-->\n    \t<p class=\"pos1\"><a class=\"button1 add_icon\" href=\"packages.php?page=add\"><span>";
        echo __("add package");
        echo "</span></a></p>\n    <!--optionsbar-->\n    </div>\n    <!--optionsbar-->\n    <br />\n    ";
    }
} else {
    echo "    <div class=\"setting_help_box\">\n        <strong>";
    echo __("it is not possible to add a package");
    echo "</strong><br />\n        ";
    echo sprintf(__("you should add a server first, package"), "<a href=\"servers.php\" class=\"a1 c1\">" . __("servers") . "</a>");
    echo "                \n    </div>            \n    ";
}
echo "\n<hr />\n\n";
foreach ($list_hosting_packages as $server_id => $package_list) {
    echo "\n\t<!--heading1-->\n\t<div class=\"heading1\" style=\"margin:0px;\">\n\t<!--heading1-->\n\t\n\t\t<h2>";
    echo __("packages on server");
    echo " <a href=\"servers.php?page=show&amp;id=";
    echo $server_id;
    echo "\" class=\"a1\">";
    echo $list_servers[$server_id]["Name"];
    echo "</a></h2>\n\t\n\t\t<p class=\"pos2\"><strong class=\"textsize1\">";
    echo __("total");
    echo ": <span>";
    echo isset($package_list["CountRows"]) ? $package_list["CountRows"] : "0";
    echo "</span></strong></p>\n\t\n\t<!--heading1-->\n\t</div><hr />\n\t<!--heading1-->\n\t\n\t<!--form-->\n\t<form action=\"packages.php?page=overview\" method=\"post\"><fieldset>\n\t<!--form-->\n\t\n\t\t<div id=\"SubTable_Server1\">\t\n\t\t\t<table id=\"MainTable_Server1\" class=\"table1\" cellpadding=\"0\" cellspacing=\"0\">\n\t\t\t<tr class=\"trtitle\">\n\t\t\t\t<th scope=\"col\" style=\"width:200px;\"><a onclick=\"save('package.overview','sort','PackageName','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 ";
    if($_SESSION["package.overview"]["sort"] == "PackageName") {
        if($_SESSION["package.overview"]["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("package name");
    echo "</a></th>\n\t\t\t\t<th scope=\"col\" style=\"width:100px;\"><a onclick=\"save('package.overview','sort','PackageType','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 ";
    if($_SESSION["package.overview"]["sort"] == "PackageType") {
        if($_SESSION["package.overview"]["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("package type");
    echo "</a></th>\n\t\t\t\t<th scope=\"col\" style=\"width:150px;\"><a onclick=\"save('package.overview','sort','TemplateName','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 ";
    if($_SESSION["package.overview"]["sort"] == "TemplateName") {
        if($_SESSION["package.overview"]["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("package templatename");
    echo "</a></th>\n\t\t\t\t<th scope=\"col\" style=\"width:75px;\"><a onclick=\"save('package.overview','sort','BandWidth','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 ";
    if($_SESSION["package.overview"]["sort"] == "BandWidth") {
        if($_SESSION["package.overview"]["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("package bandwidth");
    echo "</a></th>\n\t\t\t\t<th scope=\"col\" style=\"width:75px;\"><a onclick=\"save('package.overview','sort','DiscSpace','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 ";
    if($_SESSION["package.overview"]["sort"] == "DiscSpace") {
        if($_SESSION["package.overview"]["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("package discspace");
    echo "</a></th>\n\t\t\t\t<th scope=\"col\"><a onclick=\"save('package.overview','sort','Product','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 ";
    if($_SESSION["package.overview"]["sort"] == "Product") {
        if($_SESSION["package.overview"]["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("product");
    echo "</a></th>\n\t\t\t</tr>\n\t\t\t";
    $packageCounter = 0;
    foreach ($package_list as $packageID => $package) {
        if(is_numeric($packageID)) {
            $packageCounter++;
            echo "\t\t\t<tr class=\"hover_extra_info ";
            if($packageCounter % 2 === 0) {
                echo "tr1";
            }
            echo "\">\n\t\t\t\t<td><a href=\"packages.php?page=show&amp;id=";
            echo $packageID;
            echo "\" class=\"c1 a1\">";
            echo $package["PackageName"];
            echo "</a></td>\n\t\t\t\t<td>";
            echo $array_packagetypes[$package["PackageType"]];
            echo "</td>\n\t\t\t\t<td>";
            echo $package["Template"] == "yes" ? $package["TemplateName"] : __("no package template");
            echo "</td>\n\t\t\t\t<td>";
            if($package["uBandWidth"] == 1) {
                echo __("unlimited");
            } else {
                echo formatMB($package["BandWidth"]);
            }
            echo "</td>\n            \t<td>";
            if($package["uDiscSpace"] == 1) {
                echo __("unlimited");
            } else {
                echo formatMB($package["DiscSpace"]);
            }
            echo "</td>\n\t\t\t\t<td>";
            if($package["Product"]) {
                echo "<a href=\"products.php?page=show&amp;id=";
                echo $package["Product"];
                echo "\" class=\"a1\">";
                echo $package["ProductCode"];
                echo " - ";
                echo $package["ProductName"];
                echo "</a>";
            } else {
                echo "-";
            }
            echo "</td>\n\t\t\t</tr>\n\t\t\t";
        }
    }
    if($packageCounter === 0) {
        echo "\t\t\t<tr>\n\t\t\t\t<td colspan=\"6\">\n\t\t\t\t\t";
        echo __("no packages found");
        echo "\t\t\t\t</td>\n\t\t\t</tr>\n\t\t\t";
    }
    echo "\t\t\t</table>\n\t\t</div>\t\n\t\t\n\t<!--form-->\n\t</fieldset></form>\n\t<!--form-->\n\t<br />\n\t";
}
echo "\n\n";
require_once "views/footer.php";

?>