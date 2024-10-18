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
echo __("export data");
echo "</h2> \n\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n<!--form-->\n<form id=\"ExportTemplateForm\" name=\"form_exporttemplates\" method=\"post\" action=\"export.php?page=edit\"><fieldset>\n<!--form-->\n\n\t<!--box1-->\n\t<div class=\"box2\" id=\"tabs\">\n\t<!--box1-->\n\n\t\t<!--top-->\n\t\t<div class=\"top\">\n\t\t<!--top-->\n\t\t\n\t\t\t<ul class=\"list3\">\n\t\t\t\t<li class=\"on\"><a href=\"#tab-export\">";
echo __("general");
echo "</a></li>\n\t\t\t</ul>\n\t\t\n\t\t<!--top-->\n\t\t</div>\n\t\t<!--top-->\n\n\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-export\">\n\t\t<!--content-->\n\t\t\n\t\t\t<input type=\"hidden\" name=\"id\" value=\"";
echo isset($template_id) ? $template_id : "0";
echo "\" />\n\t\t\n\t\t\t<!--split2-->\n\t\t\t<div class=\"split2\">\n\t\t\t<!--split2-->\n\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t<div class=\"left\">\n\t\t\t\t<!--left-->\n\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("name");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("name");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" name=\"template\" class=\"text1 size1\" value=\"";
echo $exportTemplate->Name;
echo "\" />\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("export data");
echo "</strong>\n\t\t\t\t\t\t";
if(0 < $exportTemplate->Identifier) {
    echo "\t\t\t\t\t\t\t";
    echo $exportdata[$exportTemplate->ExportData]["tables"][$exportTemplate->ExportData]["title"];
    echo "\t\t\t\t\t\t\t<input type=\"hidden\" name=\"ExportData\" value=\"";
    echo $exportTemplate->ExportData;
    echo "\" />\n\t\t\t\t\t\t";
} else {
    echo "\t\t\t\t\t\t\t<select class=\"text1 size10\" id=\"ExportDataNew\" name=\"ExportData\">\n\t\t\t\t\t\t\t\t<option value=\"0\">";
    echo __("please choose");
    echo "</option>\n\t\t\t\t\t\t\t\t";
    foreach ($exportdata as $key => $value) {
        if($key != "CountRows") {
            echo "\t\t\t\t\t\t\t\t<option value=\"";
            echo $key;
            echo "\">";
            echo $value["title"];
            echo "</option>\n\t\t\t\t\t\t\t\t";
        }
    }
    echo "\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t";
}
echo "\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t</div>\n\t\t\t\t<!--left-->\n\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t<div class=\"right\">\n\t\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t\t\t<div class=\"setting_help_box\">\n\t\t\t\t\t\t";
echo __("explanation edit export");
echo "\t\t\t\t\t\t\n\t\t\t\t\t</div>\n\t\t\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t</div>\n\t\t\t\t<!--right-->\n\t\t\n\t\t\t<!--split2-->\n\t\t\t</div>\n\t\t\t<!--split2-->\n\t\t\n\t\t\t<!--split2-->\n\t\t\t<div class=\"split2 ";
echo empty($exportTemplate->Identifier) ? "hide" : "";
echo "\" id=\"columns_box\">\n\t\t\t<!--split2-->\n\t\t\t\n\t\t\t\t";
if($exportTemplate->ExportData) {
    $first_table = current($exportdata[$exportTemplate->ExportData]["tables"]);
    $first_table = $first_table["table"];
}
echo "\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t<div class=\"left\">\n\t\t\t\t<!--left-->\n\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("available columns");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t<div id=\"columns_div\">\n\t\t\t\t\t\t<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\">\n\t\t\t\t\t\t\t";
foreach ($exportdata[$exportTemplate->ExportData]["tables"] as $key => $value) {
    echo "\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t<td><strong>";
    echo $value["title"];
    echo "</strong></td>\n\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t";
    foreach ($exportdata[$exportTemplate->ExportData]["tables"][$key]["fields"] as $field) {
        echo "\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t";
        if(is_int($field)) {
            echo "\t\t\t\t\t\t\t\t\t\t<strong>";
            echo $field;
            echo "</strong>\n\t\t\t\t\t\t\t\t\t";
        } else {
            echo "\t\t\t\t\t\t\t\t\t\t";
            if(is_array($exportTemplate->Elements) && in_array($value["table"] . "." . $field, $exportTemplate->Elements)) {
                echo "\t\t\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" class=\"checkbox1 addcolumn ";
                if($value["table"] != $first_table) {
                    echo "subtable";
                }
                echo "\" checked=\"checked\" id=\"";
                echo $value["table"];
                echo ".";
                echo $field;
                echo "\" />\n\t\t\t\t\t\t\t\t\t\t";
            } else {
                echo "\t\t\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" class=\"checkbox1 addcolumn ";
                if($value["table"] != $first_table) {
                    echo "subtable";
                }
                echo "\" id=\"";
                echo $value["table"];
                echo ".";
                echo $field;
                echo "\" />\n\t\t\t\t\t\t\t\t\t\t";
            }
            echo "\t\t\t\t\t\t\t\t\t\t<label for=\"";
            echo $value["table"];
            echo ".";
            echo $field;
            echo "\" data-table=\"";
            echo $exportdata[$exportTemplate->ExportData]["tables"][$value["table"]]["title"];
            echo "\">\n                                            ";
            if(isset($exportdata[$exportTemplate->ExportData]["tables"][$key]["field_translations"][$field])) {
                echo __("export." . $exportdata[$exportTemplate->ExportData]["tables"][$key]["field_translations"][$field]);
            } else {
                echo __("export." . $field);
            }
            echo "                                        </label>\n\t\t\t\t\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t</table>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\n\t\t\t\t\t\t<br clear=\"all\" />\n\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t</div>\n\t\t\t\t<!--left-->\n\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t<div class=\"right\">\n\t\t\t\t<!--right-->\n\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("selected columns");
echo "</h3><div class=\"content lineheight2\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t<div id=\"exportsortable_list\">\n\t\t\t\t\t\t\t<ul id=\"fragment-s-1\">\n\t\t\t\t\t\t\t\t";
if(!empty($exportTemplate->Elements)) {
    echo "\t\t\t\t\t\t\t\t\t";
    foreach ($exportTemplate->Elements as $key => $value) {
        echo "\t\t\t\t\t\t\t\t\t\t<li id=\"";
        echo $value;
        echo "_clone\">\n\t\t\t\t\t\t\t\t\t\t\t<input type=\"hidden\" name=\"selected_columns[]\" value=\"";
        echo $value;
        echo "\" />\n\t\t\t\t\t\t\t\t\t\t\t<a class=\"a1 c1 ico inline ico_sortable floatl\">&nbsp;</a>\n\t\t\t\t\t\t\t\t\t\t\t<strong>\n\t\t\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t\t\t\t";
        $dbname = substr($value, 0, strpos($value, "."));
        $fieldname = substr($value, strpos($value, ".") + 1);
        if(isset($exportTemplate->config[$exportTemplate->ExportData]["tables"][$dbname]["field_translations"][$fieldname])) {
            $label = __("export." . $exportTemplate->config[$exportTemplate->ExportData]["tables"][$dbname]["field_translations"][$fieldname]);
        } else {
            $label = __("export." . $fieldname);
        }
        if($dbname == $first_table) {
            echo $label;
        } else {
            echo $label . " <span class=\"normalfont\">(" . $exportdata[$exportTemplate->ExportData]["tables"][$dbname]["title"] . ")</span>";
        }
        echo "\t\t\t\t\t\t\t\t\t\t\t</strong>\n\t\t\t\t\t\t\t\t\t\t</li>\n\t\t\t\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t\t</ul>\n\t\t\t\t\t\t</div>\n\t\t\t\n\t\t\t\t\t\t<br clear=\"all\" />\n\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t</div>\n\t\t\t\t<!--right-->\n\t\t\n\t\t\t<!--split2-->\n\t\t\t</div>\n\t\t\t<!--split2-->\n\t\t\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\t\n\t<!--box1-->\n\t</div>\n\t<!--box1-->\n\t\n\t<br />\n\t\n\t<!--buttonbar-->\n\t<div class=\"buttonbar\">\n\t<!--buttonbar-->\n\t\n\t\t<p class=\"pos1\">\n            <a class=\"button1 edit_icon\" id=\"form_export_edit_btn\">\n                <span>";
echo isset($template_id) && 0 < $template_id ? __("btn edit") : __("btn add");
echo "</span>\n            </a>\n        </p>\n\t\t\n\t\t";
if(U_EXPORT_DELETE && isset($template_id) && (int) $template_id !== 0) {
    echo "<p class=\"pos2\"><a class=\"button1 delete_icon\" onclick=\"\$('#delete_exporttemplate').dialog('open');\"><span>";
    echo __("delete");
    echo "</span></a></p>";
}
echo "\t\n\t<!--buttonbar-->\n\t</div>\n\t<!--buttonbar-->\n\t\n<!--form-->\n</fieldset></form>\n<!--form-->\n\n";
if(U_EXPORT_DELETE) {
    echo "<div id=\"delete_exporttemplate\" class=\"hide\" title=\"";
    echo __("delete export template title");
    echo "\">\n\t<form id=\"ExportForm\" name=\"form_exportdelete\" method=\"post\" action=\"export.php?page=delete&amp;id=";
    echo $template_id;
    echo "\">\n\t<input type=\"hidden\" name=\"id\" value=\"";
    echo $template_id;
    echo "\"/>\n\t";
    echo sprintf(__("sure to delete export template"), $exportTemplate->Name);
    echo "<br />\n\t\n\t<br />\n\t\n\t<input type=\"checkbox\" name=\"imsure\" value=\"yes\" id=\"imsure\"/> <label for=\"imsure\">";
    echo __("delete this export template");
    echo "</label><br />\n\t<br />\n                \n\t<p><a id=\"delete_exporttemplate_btn\" class=\"button2 alt1 float_left\"><span>";
    echo __("delete");
    echo "</span></a></p>\n\t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#delete_exporttemplate').dialog('close');\"><span>";
    echo __("cancel");
    echo "</span></a></p>\n\t</form>\n</div>\n";
}
echo "\n";
require_once "views/footer.php";

?>