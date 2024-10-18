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
echo __("package") . " " . $package->PackageName;
echo "</h2>\n\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\t\n\t<!--box1-->\n\t<div class=\"box2\" id=\"tabs\">\n\t<!--box1-->\n\t\n\t\t<!--top-->\n\t\t<div class=\"top\">\n\t\t<!--top-->\n\t\t\n\t\t\t<ul class=\"list3\">\n\t\t\t\t<li class=\"on\"><a href=\"#tab-general\">";
echo __("general");
echo "</a></li>\n\t\t\t</ul>\n\t\t\n\t\t<!--top-->\n\t\t</div>\n\t\t<!--top-->\n\n\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-general\">\n\t\t<!--content-->\n\n\t\t\t<!--split2-->\n\t\t\t<div class=\"split2\">\n\t\t\t<!--split2-->\n\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t<div class=\"left\">\n\t\t\t\t<!--left-->\n\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("package data");
echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("package name");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
echo $package->PackageName;
echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("product");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
if(0 < $package->Product) {
    echo "<a href=\"products.php?page=show&amp;id=";
    echo $package->Product;
    echo "\" class=\"a1 c1\">";
    echo $list_hosting_products[$package->Product]["ProductCode"] . " - " . $list_hosting_products[$package->Product]["ProductName"];
    echo " (";
    echo money($list_hosting_products[$package->Product]["PriceExcl"]) . " " . __("per") . " " . $array_periodes[$list_hosting_products[$package->Product]["PricePeriod"]];
    echo ")</a>";
} else {
    echo "-";
}
echo "</span>\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("webhosting type");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
echo $array_packagetypes[$package->PackageType];
echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("server");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\"><a href=\"servers.php?page=show&amp;id=";
echo $package->Server;
echo "\" class=\"a1 c1\">";
echo $server->Name;
echo "</a>\n\t\t\t\t\t\t(";
echo $server->getControlPanelName($server->Panel);
echo ")\n\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("template");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
if($package->Template == "yes") {
    echo $package->TemplateName;
} else {
    echo __("use no package template");
}
echo "</span>\n\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("hosting account data briefing");
echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t\t\t\t<!--box3-->\n\n\t\t\t\t\t\t\t<strong class=\"title2\">";
echo __("emailtemplate");
echo "</strong>\n\t\t\t\t\t\t\t<span class=\"title2_value\">";
if(0 < $package->EmailTemplate && isset($emailTemplate)) {
    echo "<a href=\"templates.php?page=showemail&id=" . $package->EmailTemplate . "\" class=\"a1 c1\">" . $emailTemplate->Name . "</a>";
} else {
    echo __("none");
}
echo "</span>\n\n\t\t\t\t\t\t\t<strong class=\"title2\">";
echo __("automatic email");
echo "</strong>\n\t\t\t\t\t\t\t<span class=\"title2_value\">";
if($package->EmailAuto == "yes") {
    echo __("yes");
} else {
    echo __("no");
}
echo "</span>\n\n\t\t\t\t\t\t\t<strong class=\"title2\">";
echo __("pdf template");
echo "</strong>\n\t\t\t\t\t\t\t<span class=\"title2_value\">";
if($package->PdfTemplate && 0 < isset($pdfTemplate)) {
    echo "<a href=\"templates.php?page=showother&id=" . $package->PdfTemplate . "\" class=\"a1 c1\">" . $pdfTemplate->Name . "</a>";
} else {
    echo __("none");
}
echo "</span>\n\n\t\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\n\t\t\t\t<!--left-->\n\t\t\t\t</div>\n\t\t\t\t<!--left-->\n\t\t\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t<div class=\"right\">\n\t\t\t\t<!--right-->\n\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("properties of package");
echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("package discspace");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
if($package->uDiscSpace == 1) {
    echo __("unlimited");
} else {
    echo formatMB($package->DiscSpace);
}
echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("package bandwidth");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
if($package->uBandWidth == 1) {
    echo __("unlimited");
} else {
    echo formatMB($package->BandWidth);
}
echo "</span>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("number of domains");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
if($package->uDomains == 1) {
    echo __("unlimited");
} else {
    echo $package->Domains;
}
echo "</span>\t\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("databases");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
if($package->uMySQLDatabases == 1) {
    echo __("unlimited");
} else {
    echo $package->MySQLDatabases;
}
echo "</span>\n\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("number of emailaccounts");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
if($package->uEmailAccounts == 1) {
    echo __("unlimited");
} else {
    echo $package->EmailAccounts;
}
echo "</span>\t\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("number of subdomains");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
if($package->uSubDomains == 1) {
    echo __("unlimited");
} else {
    echo $package->SubDomains;
}
echo "</span>\t\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title2\">";
echo __("number of domainpointers");
echo "</strong>\n\t\t\t\t\t\t<span class=\"title2_value\">";
if($package->uDomainpointers == 1) {
    echo __("unlimited");
} else {
    echo $package->Domainpointers;
}
echo "</span>\t\n\t\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\n\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t</div>\n\t\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t<!--split2-->\n\t\t\t</div>\n\t\t\t<!--split2-->\n\t\t\t\t\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\t\t\t\n\n\t<!--box1-->\n\t</div>\n\t<!--box1-->\n\n\t<br />\n\t";
if($package->Status != 9) {
    echo "\t\t\n\t\t<!--buttonbar-->\n\t\t<div class=\"buttonbar\">\n\t\t<!--buttonbar-->\n\t\t\n\t\t\t";
    if(U_SERVICEMANAGEMENT_EDIT) {
        echo "<p class=\"pos1\"><a class=\"button1 edit_icon\" href=\"packages.php?page=edit&amp;id=";
        echo $package_id;
        echo "\"><span>";
        echo __("edit");
        echo "</span></a></p>";
    }
    echo "\t\t\t";
    if(U_SERVICEMANAGEMENT_DELETE) {
        echo "<p class=\"pos2\"><a class=\"button1 delete_icon\" onclick=\"\$('#delete_package').dialog('open');\"><span>";
        echo __("delete");
        echo "</span></a></p>";
    }
    echo "\t\t\t\n\t\t<!--buttonbar-->\n\t\t</div>\n\t\t<!--buttonbar-->\n\t\t<br />\n\t";
}
echo "\t\n";
if($list_hosting_accounts["CountRows"]) {
    echo "\t\n\n<!--box1-->\n<div class=\"box2\" id=\"subtabs\">\n<!--box1-->\n\t\n\t<!--top-->\n\t<div class=\"top\">\n\t<!--top-->\n\t\t\n\t\t<ul class=\"list3\">\n\t\t\t<li class=\"on\"><a href=\"#subtab-accounts\">";
    echo __("accounts with this package");
    echo " (";
    echo $list_hosting_accounts["CountRows"];
    echo ")</a></li>\n\t\t</ul>\n\t\t\n\t<!--top-->\n\t</div>\n\t<!--top-->\n\n\t<!--content-->\n\t<div class=\"content\" id=\"subtab-accounts\">\n\t<!--content-->\n\t\n\t\t";
    require_once "views/elements/hosting.table.php";
    $options = ["redirect_page" => "package", "redirect_id" => $package->Identifier, "session_name" => "package.show.accounts", "current_page" => $current_page, "current_page_url" => $current_page_url, "hide_columns" => ["Package"]];
    show_hosting_table($list_hosting_accounts, $options);
    echo "\t\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\t\n<!--box1-->\n</div>\n<!--box1-->\n\n";
}
echo "\n";
if(U_SERVICEMANAGEMENT_DELETE) {
    echo "<div id=\"delete_package\" class=\"hide ";
    if(isset($pagetype) && $pagetype == "confirmDelete") {
        echo "autoopen";
    }
    echo "\" title=\"";
    echo __("deletedialog package title");
    echo "\">\n\t<form id=\"PackageForm\" name=\"form_delete\" method=\"post\" action=\"packages.php?page=delete\">\n\t<input type=\"hidden\" name=\"id\" value=\"";
    echo $package_id;
    echo "\"/>\n\t";
    echo sprintf(__("deletedialog package description"), $package->PackageName);
    echo "<br />\n    \n\t<br />\n\t<input type=\"checkbox\" id=\"imsure\" name=\"imsure\" value=\"yes\"/> <label for=\"imsure\">";
    echo __("delete this package");
    echo "</label><br />\n\t<br />\n\t<p><a id=\"delete_package_btn\" class=\"button2 alt1 float_left\"><span>";
    echo __("delete");
    echo "</span></a></p>\n\t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#delete_package').dialog('close');\"><span>";
    echo __("cancel");
    echo "</span></a></p>\n\t\n\t</form>\n</div>\n";
}
echo "\n";
require_once "views/footer.php";

?>