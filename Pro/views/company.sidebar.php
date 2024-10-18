<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "<script type=\"text/javascript\" src=\"js/company.js?v=";
echo JSFILE_NOCACHE;
echo "\"></script>\n<script type=\"text/javascript\" src=\"3rdparty/ckeditor/ckeditor.js?v=";
echo JSFILE_NOCACHE;
echo "\"></script>\n\n";
if(U_COMPANY_SHOW) {
    echo "    \n        <div id=\"submenu\">\n        \t<ul>\n        \t\t<li ";
    if($page == "") {
        echo "class=\"active\"";
    }
    echo "><a href=\"company.php\">";
    echo __("company data");
    echo "</a></li>\n        \t\t<li ";
    if($page == "accounts" || $page == "accountshow" || $page == "account") {
        echo "class=\"active\"";
    }
    echo "><a href=\"company.php?page=accounts\">";
    echo __("employees");
    echo "</a></li>\n        \t</ul>\n        </div>\n        ";
} else {
    echo "        <div id=\"submenu\">\n        \t<ul>\n        \t\t<li class=\"active\"><a href=\"company.php?page=accountshow&id=";
    echo $account->Identifier;
    echo "\">";
    echo __("my profile");
    echo "</a></li>\n        \t</ul>\n        </div> \n        ";
}
echo "        \n";
get_dashboard_statistics();

?>