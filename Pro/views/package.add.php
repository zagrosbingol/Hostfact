<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "views/header.php";
echo "<script type=\"text/javascript\" src=\"js/package.js?v=";
echo JSFILE_NOCACHE;
echo "\"></script>\n";
$page_form_title = $pagetype == "edit" ? __("edit package") : __("add package");
echo "\n";
echo $message;
echo "\n<!--form-->\n<form id=\"PackageForm\" name=\"form_create\" method=\"post\" action=\"packages.php?page=";
echo $pagetype;
echo "\"><fieldset><legend>";
echo $page_form_title;
echo "</legend>\n\t\n";
if(0 < $package_id) {
    echo "\t<input type=\"hidden\" name=\"id\" value=\"";
    echo $package_id;
    echo "\" />\n";
}
echo "<input type=\"hidden\" name=\"ProductUpdate\" value=\"no\" />\n<!--form-->\n\n<!--heading1-->\n<div class=\"heading1\">\n<!--heading1-->\n\n\t<h2>";
echo $page_form_title;
echo "</h2>\n\t\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n\t<!--box1-->\n\t<div class=\"box2\" id=\"tabs\">\n\t<!--box1-->\n\t\n\t\t<!--top-->\n\t\t<div class=\"top\">\n\t\t<!--top-->\n\t\t\n\t\t\t<ul class=\"list3\">\n\t\t\t\t<li class=\"on\"><a href=\"#tab-general\">";
echo __("general");
echo "</a></li>\n\t\t\t</ul>\n\t\t\n\t\t<!--top-->\n\t\t</div>\n\t\t<!--top-->\n\n\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-general\">\n\t\t<!--content-->\n\t\t\t\n\t\t\t<!--split2-->\n\t\t\t<div class=\"split2\">\n\t\t\t<!--split2-->\n\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t<div class=\"left\">\n\t\t\t\t<!--left-->\n\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("package data");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("package name");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" class=\"text1 size1\" name=\"PackageName\" value=\"";
echo $package->PackageName;
echo "\" maxlength=\"100\" ";
$ti = tabindex($ti);
echo " />\t\t\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("product");
echo "</strong>\n\t\t\t\t\t\t<input type=\"hidden\" name=\"Product\" value=\"";
echo $package->Product;
echo "\" />\n\t\t\t\t\t\t";
$selected_name = 0 < $package->Product ? $list_hosting_products[$package->Product]["ProductCode"] . " " . $list_hosting_products[$package->Product]["ProductName"] : "";
createAutoComplete("product", "Product", $selected_name, ["class" => "size12", "filter" => "hosting|other"]);
echo "\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("webhosting type");
echo "</strong>\n\t\t\t\t\t\t<select name=\"PackageType\" class=\"text1 size4f\" ";
$ti = tabindex($ti);
echo ">\n\t\t\t\t\t\t\t";
foreach ($array_packagetypes as $key => $value) {
    echo "\t\t                        <option value=\"";
    echo $key;
    echo "\" ";
    if($package->PackageType == $key) {
        echo "selected=\"selected\"";
    }
    echo ">";
    echo $value;
    echo "</option>\n\t\t                    ";
}
echo "\t\t\t\t\t\t</select>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("server");
echo "</strong>\n\t\t\t\t\t\t<select name=\"Server\" class=\"text1 size4f\" ";
$ti = tabindex($ti);
echo ">\n\t\t\t\t\t\t\t<option value=\"\">";
echo __("make your choice");
echo "</option>\n\t\t\t\t\t\t\t";
foreach ($list_servers as $key => $value) {
    if(is_numeric($key)) {
        echo "\t\t\t\t\t\t\t<option value=\"";
        echo $key;
        echo "\" ";
        if($package->Server == $key) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo $value["Name"];
        echo "</option>\n\t\t\t\t\t\t\t";
    }
}
echo "\t\t\t\t\t\t</select>\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("template");
echo "</strong>\n\t\t\t\t\t\t<span id=\"hosting_div_template\">";
echo __("no server selected yet");
echo "</span>\n\t\t\t\t\t\t<input type=\"hidden\" name=\"TemplateNameHidden\" value=\"";
echo $package->TemplateName;
echo "\" ";
$ti = tabindex($ti);
echo " />\n\t\t\t\t\t\t<select name=\"TemplateName\" class=\"text1 size4f hide\">\n\n\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t</div>\n\t\t\t\t<!--left-->\n\t\t\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t<div class=\"right\">\n\t\t\t\t<!--right-->\n\t\t\t\t\t\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("properties of package");
echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<div id=\"properties_hosting_package_none\"><i>";
echo __("first select a template");
echo "</i></div>\t\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<div id=\"properties_hosting_package_custom\" class=\"hide\">\n\t\t\t\t\t\t\t\t<strong class=\"title2\">";
echo __("package discspace");
echo "</strong>\n\t\t\t\t\t\t\t\t<span class=\"title2_value\"><input type=\"text\" class=\"text1 size2\" name=\"DiscSpace\" value=\"";
echo $package->DiscSpace;
echo "\" /> MB</span>\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<strong class=\"title2\">";
echo __("package bandwidth");
echo "</strong>\n\t\t\t\t\t\t\t\t<span class=\"title2_value\"><input type=\"text\" class=\"text1 size2\" name=\"BandWidth\" value=\"";
echo $package->BandWidth;
echo "\" /> MB</span>\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<strong class=\"title2\">";
echo __("number of domains");
echo "</strong>\n\t\t\t\t\t\t\t\t<span class=\"title2_value\"><input type=\"text\" class=\"text1 size2\" name=\"Domains\" value=\"";
echo $package->Domains;
echo "\" /></span>\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<strong class=\"title2\">";
echo __("databases");
echo "</strong>\n\t\t\t\t\t\t\t\t<span class=\"title2_value\"><input type=\"text\" class=\"text1 size2\" name=\"MySQLDatabases\" value=\"";
echo $package->MySQLDatabases;
echo "\" /></span>\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<strong class=\"title2\">";
echo __("number of emailaccounts");
echo "</strong>\n\t\t\t\t\t\t\t\t<span class=\"title2_value\"><input type=\"text\" class=\"text1 size2\" name=\"EmailAccounts\" value=\"";
echo $package->EmailAccounts;
echo "\" /></span>\n\n\t\t\t\t\t\t\t</div>\t\n\t\t\t\t\t\n\t\t\t\t\t\t\t<div id=\"properties_hosting_package\" class=\"hide\">\n\t\t\t\t\t\t\t\t<strong class=\"title2\">";
echo __("package discspace");
echo "</strong>\n\t\t\t\t\t\t\t\t<span id=\"hosting_discspace\" class=\"title2_value\">-</span>\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<strong class=\"title2\">";
echo __("package bandwidth");
echo "</strong>\n\t\t\t\t\t\t\t\t<span id=\"hosting_traffic\" class=\"title2_value\">-</span>\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<strong class=\"title2\">";
echo __("number of domains");
echo "</strong>\n\t\t\t\t\t\t\t\t<span id=\"hosting_domains\" class=\"title2_value\">-</span>\t\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<strong class=\"title2\">";
echo __("databases");
echo "</strong>\n\t\t\t\t\t\t\t\t<span id=\"hosting_databases\" class=\"title2_value\">-</span>\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<strong class=\"title2\">";
echo __("number of emailaccounts");
echo "</strong>\n\t\t\t\t\t\t\t\t<span id=\"hosting_emailaccounts\" class=\"title2_value\">-</span>\n\t\t\t\t\t\t\t</div>\n\n\t\t\t\t\t\t</div>\n\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div>\n\t\t\t\t\t<!--box3-->\n\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("hosting account data briefing");
echo "</h3><div class=\"content\">\n\t\t\t\t\t\t\t<!--box3-->\n\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("hosting account data briefing mailtemplate");
echo "</strong>\n\t\t\t\t\t\t\t<select name=\"EmailTemplate\" class=\"text1 size4f\">\n\t\t\t\t\t\t\t\t<option value=\"\">";
echo __("make your choice");
echo "</option>\n\t\t\t\t\t\t\t\t";
foreach ($emailtemplates as $k => $v) {
    if(is_numeric($k)) {
        echo "<option value=\"";
        echo $k;
        echo "\" ";
        if($k == $package->EmailTemplate) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo $v["Name"];
        echo "</option>";
    }
}
echo "\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t<br /><br />\n\n\t\t\t\t\t\t\t<div id=\"hosting_pdf_email_div\" class=\"";
if($package->EmailTemplate <= 0) {
    echo "hide";
}
echo "\">\n\t\t\t\t\t\t\t\t<input type=\"checkbox\" id=\"pdf_email_sent_yes\" name=\"EmailAuto\" value=\"yes\" class=\"text1\" ";
if($package->EmailAuto == "yes") {
    echo "checked=\"checked\"";
}
echo "/> <label for=\"pdf_email_sent_yes\">";
echo __("send hosting account briefing mail automatically");
echo "</label><br /><br />\n\t\t\t\t\t\t\t</div>\n\n\t\t\t\t\t\t\t<strong class=\"title\">";
echo __("hosting account data briefing pdf");
echo "</strong>\n\t\t\t\t\t\t\t<select name=\"PdfTemplate\" class=\"text1 size4f\">\n\t\t\t\t\t\t\t\t<option value=\"\">";
echo __("make your choice");
echo "</option>\n\t\t\t\t\t\t\t\t";
foreach ($templates_other as $k => $v) {
    if(is_numeric($k)) {
        echo "<option value=\"";
        echo $k;
        echo "\" ";
        if($k == $package->PdfTemplate) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo $v["Name"];
        echo "</option>";
    }
}
echo "\t\t\t\t\t\t\t</select>\n\n\t\t\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\n\t\t\t\t<!--right-->\n\t\t\t\t</div>\n\t\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t<!--split2-->\n\t\t\t</div>\n\t\t\t<!--split2-->\n\t\t\t\t\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\t\t\t\n\t<!--box1-->\n\t</div>\n\t<!--box1-->\n\n\t<br />\n\t\t\t\t\t\n\t<p class=\"align_right\">\n        <a class=\"button1 alt1\" id=\"package_create_btn\">\n            <span>";
echo $pagetype == "edit" ? __("btn edit") : __("btn add");
echo "</span>\n        </a>\n    </p>\n\t\n<!--form-->\n</fieldset></form>\n<!--form-->\n\n<div id=\"package_product_connect_dialog\" class=\"hide\" title=\"";
echo __("package product connect dialog title");
echo "\">\n\n\t";
echo sprintf(__("package product connect dialog description"), "<span class=\"productname\"></span>", "<span class=\"packagename\">" . $package->PackageName . "</span>", "<span class=\"productname\"></span>");
echo "<br />\n\t<br />\n\t<input type=\"radio\" name=\"ProductUpdateDialog\" id=\"ProductUpdateDialogYes\" value=\"yes\"/> <label for=\"ProductUpdateDialogYes\">";
echo sprintf(__("package product connect dialog update product"), "<span class=\"packagename\">" . $package->PackageName . "</span>");
echo "</label><br />\n\t<input type=\"radio\" name=\"ProductUpdateDialog\" id=\"ProductUpdateDialogNo\" value=\"no\"/> <label for=\"ProductUpdateDialogNo\">";
echo __("package product connect dialog do not update product");
echo "</label><br />\n\t<br />\n\t<p><a id=\"package_product_connect_dialog_btn\" class=\"button2 alt1 float_left\"><span>";
echo __("proceed");
echo "</span></a></p>\n\n</div>\n\n";
require_once "views/footer.php";

?>