<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "<!--box1-->\n<div class=\"box2 subtabs\">\n<!--box1-->\n\t\n\t<!--top-->\n\t<div class=\"top\">\n\t<!--top-->\n\t\t\n\t\t<ul class=\"list3\">\n\t\t\t<li class=\"on\"><a href=\"#subtab-domains\">";
echo __("domains for this account");
echo " (";
echo count($hosting->DomainList);
echo ")</a></li>\n\t\t</ul>\n\t\t\n\t<!--top-->\n\t</div>\n\t<!--top-->\n\t\n\t";
$domainArray = $hosting->DomainList;
$showOptions = false;
foreach ($domainArray as $sDomainName => $aDomainData) {
    if(isset($aDomainData["Parent"]) && key_exists($aDomainData["Parent"], $domainArray)) {
        $domainArray[$aDomainData["Parent"]]["Children"][$sDomainName] = $aDomainData;
        unset($domainArray[$sDomainName]);
    }
    if(!isset($aDomainData["Active"]) || !$aDomainData["Active"] === true) {
        $showOptions = true;
    }
}
echo "\t\n\t<!--content-->\n\t<div class=\"content\" id=\"subtab-domains\">\n\t<!--content-->\n\n\t\t<p class=\"float_right\" style=\"margin-bottom: 10px;\">\n            <a href=\"services.php?page=add&amp;from=hosting&amp;from_id=";
echo $hosting->Identifier;
echo "&amp;extradomain=true\" class=\"a1 c1 float_right\">\n                ";
echo __("new domain");
echo "            </a>\n        </p>\n        \n\t\t\n\t\t<form id=\"HostingDomainsForm\" name=\"form_domains\" method=\"post\" action=\"hosting.php?page=show&amp;id=";
echo $hosting->Identifier;
echo "\">\n\t\t";
echo CSRF_Model::getToken();
echo "\t\t<div id=\"SubTable_Domains\">\t\n\t\t<table id=\"MainTable_Domains\" class=\"table1\" cellpadding=\"0\" cellspacing=\"0\">\n\t\t<tr class=\"trtitle\">\n\t\t\t<th scope=\"col\">";
if($showOptions) {
    echo "<label><input name=\"DomainBatch\" class=\"BatchCheck\" type=\"checkbox\" value=\"on\" /></label>";
}
echo " ";
echo __("domain");
echo "</th>\n\t\t\t<th scope=\"col\">";
echo __("traffic");
echo "</th>\n\t\t\t<th scope=\"col\">";
echo __("discspace");
echo "</th>\n\t\t\t<th scope=\"col\">";
echo __("hosting account availability");
echo "</th>\n\t\t</tr>\n\t\t\n\t\t";
if(!empty($domainArray)) {
    $line = true;
    foreach ($domainArray as $key => $domainLine) {
        $bShowLineCheckbox = false;
        if(isset($domainLine["Active"]) && $domainLine["Active"] === true && isset($domainLine["id"]) && 0 < $domainLine["id"]) {
            $sShowOptions = __("in software and at server");
        } elseif(isset($domainLine["Active"]) && $domainLine["Active"] === true) {
            $sShowOptions = "<a href=\"services.php?page=add&amp;from=hosting&amp;from_id=" . $hosting->Identifier . "&amp;extradomain=" . $domainLine["Domain"] . "\" class=\"a1 c1\">" . __("only server, add to software") . "</a>";
        } elseif($hosting->Status < 4 || 5 < $hosting->Status) {
            $sShowOptions = __("hosting not active");
        } else {
            $sShowOptions = __("not available at server");
            $bShowLineCheckbox = true;
        }
        if(isset($domainLine["Type"]) && $domainLine["Type"] != "" && $domainLine["Type"] != "additional") {
            $domainLine["BandWidth"] = in_array($domainLine["Type"], $array_domaintype) ? __("server domaintype " . $domainLine["Type"]) : $domainLine["Type"];
            $domainLine["DiscSpace"] = in_array($domainLine["Type"], $array_domaintype) ? __("server domaintype " . $domainLine["Type"]) : $domainLine["Type"];
        }
        echo " \n\t\t\t\t<tr class=\"hover_extra_info ";
        echo $line ? "tr1" : "";
        echo "\">\n\t\t\t\t\t<td>";
        if($bShowLineCheckbox && $showOptions) {
            echo "<input name=\"domain[]\" type=\"checkbox\" class=\"DomainBatch\" value=\"";
            echo $domainLine["Domain"];
            echo "\" />";
        } elseif($showOptions) {
            echo "<span style=\"padding-left: 20px;\">&nbsp;</span>";
        }
        echo " \n\t\t\t\t\t\t";
        if(isset($domainLine["id"]) && 0 < $domainLine["id"]) {
            echo "<a href=\"domains.php?page=show&id=" . $domainLine["id"] . "\" class=\"a1\">" . $domainLine["Domain"] . "</a>";
        } else {
            echo $domainLine["Domain"];
        }
        echo "\t\t\t\t\t</td>\n\t\t\t\t\t<td>";
        echo $domainLine["BandWidth"];
        echo "</td>\n\t\t\t\t\t<td>";
        echo $domainLine["DiscSpace"];
        echo "</td>\n\t\t\t\t\t<td>";
        echo $sShowOptions;
        echo "</td>\n\t\t\t\t</tr>\n\t\t\t\t\n\t\t\t";
        $line = $line ? false : true;
        if(isset($domainLine["Children"]) && is_array($domainLine["Children"])) {
            foreach ($domainLine["Children"] as $aChild) {
                if(isset($aChild["Type"]) && $aChild["Type"] != "" && $aChild["Type"] != "additional") {
                    $aChild["BandWidth"] = in_array($aChild["Type"], $array_domaintype) ? __("server domaintype " . $aChild["Type"]) : $aChild["Type"];
                    $aChild["DiscSpace"] = in_array($aChild["Type"], $array_domaintype) ? __("server domaintype " . $aChild["Type"]) : $aChild["Type"];
                }
                echo "\t\t\t\t\t\t\n\t\t\t\t\t\t<tr class=\"hover_extra_info ";
                echo $line ? "tr1" : "";
                echo "\">\n\t\t\t\t\t\t\t<td><span class=\"ico inline subdomainhook\" style=\"margin-left:";
                if($showOptions) {
                    echo "25";
                } else {
                    echo "5";
                }
                echo "px;\">&nbsp;</span>\n\t\t\t\t\t\t\t\t";
                if(isset($aChild["id"]) && 0 < $aChild["id"]) {
                    echo "<a href=\"domains.php?page=show&id=" . $aChild["id"] . "\" class=\"a1\">" . $aChild["Domain"] . "</a>";
                } else {
                    echo $aChild["Domain"];
                }
                echo "\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t<td>";
                echo $aChild["BandWidth"];
                echo "</td>\n\t\t\t\t\t\t\t<td>";
                echo $aChild["DiscSpace"];
                echo "</td>\n\t\t\t\t\t\t\t<td>";
                if(isset($aChild["Active"]) && $aChild["Active"] === true && isset($aChild["id"]) && 0 < $aChild["id"]) {
                    echo __("in software and at server");
                } elseif(isset($aChild["Active"]) && $aChild["Active"] === true) {
                    echo "<a href=\"services.php?page=add&amp;from=hosting&amp;from_id=" . $hosting->Identifier . "&amp;extradomain=" . $aChild["Domain"] . "\" class=\"a1 c1\">" . __("only server, add to software") . "</a>";
                } elseif($hosting->Status < 4 || 5 < $hosting->Status) {
                    echo __("hosting not active");
                }
                echo "</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\n\t\t\t\t\t";
                $line = $line ? false : true;
            }
        }
    }
} else {
    echo "\t\t<tr>\n\t\t\t<td colspan=\"4\">\n\t\t\t\t";
    echo __("no results found");
    echo "\t\t\t</td>\n\t\t</tr>\n\t\t";
}
echo "\t\t";
if($showOptions) {
    echo "\t\t<tr class=\"table_options\">\n\t\t\t<td colspan=\"4\">\n\t\t\t\t<p class=\"ico inline hook\">\n\t\t\t\t\t<select name=\"action\" class=\"select1 BatchSelect\">\n\t\t\t\t\t\t<option value=\"\" selected=\"selected\">";
    echo __("with selected");
    echo "</option>\n\t\t\t\t\t\t<option value=\"addDomain\">";
    echo sprintf(__("add domain to server as"), __("server domaintype " . $serverType));
    echo "</option>\n\t\t\t\t\t</select>\n\t\t\t\t</p>\n\t\t\t</td>\n\t\t</tr>\n\t\t";
}
echo "\t\t</table>\n\t\t</div>\n\t\t\n\t\t</form>\n\t\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\t\n<!--box1-->\n</div>\n<!--box1-->";

?>