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
if(1 <= $extern_templates["CountRows"]) {
    echo "<ul class=\"list1\">\n\n\t";
    foreach ($extern_templates as $key => $extern) {
        if(is_numeric($key)) {
            if(strpos($extern["Name"], "Mamut") !== false) {
                $filename = "mamut.png";
            } elseif(strpos($extern["Name"], "SnelStart") !== false) {
                $filename = "snelstart.png";
            }
            echo "\t\t<li>\n\t\t\t<form method=\"post\" action=\"export.php?page=download\">\n\t\t\t<input type=\"hidden\" name=\"id\" value=\"";
            echo $extern["id"];
            echo "\" />\n\t\t\t<input type=\"hidden\" name=\"dataset\" value=\"";
            echo $extern["ExportData"];
            echo "\" />\n\t\t\t<a class=\"ico set1 invoice large_actionname extern_actionbtn\" onclick=\"\" style=\"background-image:url('3rdparty/export/templates/";
            echo $filename;
            echo "');\">";
            echo str_replace(" ", "<br />", $extern["Name"]);
            echo "</a>\n\t\t\t</form>\n\t\t</li>\n\t";
        }
    }
    echo "</ul>\n";
}
echo "\n<hr />\n\n";
echo $message;
echo "\n<!--heading1-->\n<div class=\"heading1\">\n<!--heading1-->\n\n\t<h2>";
echo __("export data");
echo "</h2> \n\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n";
if(U_EXPORT_EDIT) {
    echo "<!--optionsbar-->\n<div class=\"optionsbar\">\n<!--optionsbar-->\n\n\t<p class=\"pos1\"><a class=\"button1 add_icon\" href=\"export.php?page=edit&id=0\"><span>";
    echo __("new template");
    echo "</span></a></p>\n\t\n<!--optionsbar-->\n</div>\n<!--optionsbar-->\n";
}
echo "\n<!--form-->\n<form id=\"ExportTemplateForm\" name=\"form_exporttemplates\" method=\"post\" action=\"export.php?page=download\"><fieldset>\n<!--form-->\n\n\t<!--box1-->\n\t<div class=\"box2\" id=\"tabs\">\n\t<!--box1-->\n\n\t\t<!--top-->\n\t\t<div class=\"top\">\n\t\t<!--top-->\n\t\t\n\t\t\t<ul class=\"list3\">\n\t\t\t\t<li class=\"on\"><a href=\"#tab-export\">";
echo __("general");
echo "</a></li>\n\t\t\t</ul>\n\t\t\n\t\t<!--top-->\n\t\t</div>\n\t\t<!--top-->\n\n\t\t<!--content-->\n\t\t<div class=\"content\" id=\"tab-export\">\n\t\t<!--content-->\n\t\t\t\n\t\t\t<!--split2-->\n\t\t\t<div class=\"split2\">\n\t\t\t<!--split2-->\n\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t<div class=\"left\">\n\t\t\t\t<!--left-->\n\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("data");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t \n\t\t\t\t \t\t<table width=\"100%\">\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td><strong>";
echo __("select data to export");
echo "</strong></td>\n\t\t\t\t\t\t\t</tr> \t\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t<select class=\"text1 size10\" id=\"ExportData\" name=\"ExportData\">\n\t\t\t\t\t\t\t\t\t\t<option value=\"0\">";
echo __("please choose");
echo "</option>\n\t\t\t\t\t\t\t\t\t\t";
foreach ($exportTemplates as $key => $template) {
    if($key != "CountRows") {
        echo "\t\t\t\t\t\t\t\t\t\t<option value=\"";
        echo $template["id"];
        echo "\">";
        echo $template["Name"];
        echo "</option>\n\t\t\t\t\t\t\t\t\t\t";
    }
}
echo "\t\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t<td align=\"right\">";
if(U_EXPORT_EDIT) {
    echo "<a class=\"a1 c1 floatr edit_icon hide\" id=\"export_edit\" href=\"export.php?page=edit\"><span>";
    echo __("edit template");
    echo "</span></a>";
}
echo "</td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t \t\t\t</table>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<br />\n\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t<br />\n\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3 hide\" id=\"filters\"><h3>";
echo __("filters");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t\t";
foreach ($exportTemplates as $key2 => $template) {
    if($key2 != "CountRows") {
        echo "\t\t\t\t\t\t<div class=\"exportfilter_div ";
        echo $template["ExportData"] != "HostFact_Debtors" ? "hide" : "";
        echo "\" id=\"filter_";
        echo $template["id"];
        echo "\">\n\t\t\t\t\t\t";
        if(isset($exportdata[$template["ExportData"]]["filters"])) {
            echo "\t\t\t\t\t\t\t<table>\n\t\t\t\t\t\t\t";
            foreach ($exportdata[$template["ExportData"]]["filters"] as $key3 => $filter) {
                echo "\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t<input type=\"hidden\" id=\"filterkey\" name=\"filterkey\" value=\"";
                echo $key2;
                echo "\" />\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t";
                switch ($filter["type"]) {
                    case "date":
                        echo "\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t                \t<td>\n\t\t\t\t\t\t\t\t\t\t\t<strong>";
                        echo $filter["title"];
                        echo "</strong>\n\t\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t<td class=\"paddingb\">\n\t\t\t\t\t\t\t\t\t\t\t<table>\n\t\t\t\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<td><input type=\"text\" class=\"text1 size7 datepicker\" class=\"form_input datepicker\" id=\"date_begin_";
                        echo $key2;
                        echo "_";
                        echo $key3;
                        echo "\" name=\"value1_";
                        echo $key3;
                        echo "\" disabled=\"disabled\" /></td>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<td style=\"padding: 0 15px;\" align=\"center\">";
                        echo __("up to and including");
                        echo "</td>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<td><input type=\"text\" class=\"text1 size7 datepicker\" class=\"form_input datepicker\" id=\"date_end_";
                        echo $key2;
                        echo "_";
                        echo $key3;
                        echo "\" name=\"value2_";
                        echo $key3;
                        echo "\" disabled=\"disabled\" /></td>\n\t\t\t\t\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t\t\t\t</table>\n\t\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t\t";
                        break;
                    case "id":
                        echo "\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t                \t<td>\n\t\t\t\t\t\t\t\t\t\t\t<strong>";
                        echo $filter["title"];
                        echo "</strong>\n\t\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t<td class=\"paddingb\">\n\t\t\t\t\t\t\t\t\t\t\t<table>\n\t\t\t\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size7\" id=\"id_begin_";
                        echo $template["ExportData"];
                        echo "\" name=\"value1_";
                        echo $key3;
                        echo "\"  />\n\t\t\t\t\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<td style=\"padding: 0 15px;\" align=\"center\">";
                        echo __("up to and including");
                        echo "</td>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size7\" id=\"id_end_";
                        echo $template["ExportData"];
                        echo "\" name=\"value2_";
                        echo $key3;
                        echo "\"  />\n\t\t\t\t\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t\t\t\t</table>\n\t\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t\t";
                        break;
                    case "select":
                        echo "\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t                \t<td>\n\t\t\t\t\t\t\t\t\t\t\t<strong>";
                        echo $filter["title"];
                        echo "</strong>\n\t\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t<td class=\"paddingb\">\n\t\t\t\t\t                        <select id=\"status_";
                        echo $template["ExportData"];
                        echo "\" name=\"value_";
                        echo $key3;
                        echo "\" class=\"text1 size4f\" disabled=\"disabled\">\n\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"\">Alle statussen</option>\n\t\t\t\t\t\t\t\t\t\t\t\t";
                        foreach (${$filter["values"]} as $key => $value) {
                            echo "\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"";
                            echo $key;
                            echo "\">";
                            echo $value;
                            echo "</option>\n\t\t\t\t\t\t\t\t\t\t\t\t";
                        }
                        echo "\t\t\t\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t\t";
                        break;
                    case "checkbox":
                        if(is_array($filter["function"])) {
                            $aData = $filter["function"];
                        } elseif(function_exists($filter["function"])) {
                            $aData = $filter["function"]();
                        }
                        if(!empty($aData)) {
                            echo "                            \t\t\t\t<tr>\n                                            \t<td>\n                            \t\t\t\t\t\t<strong>";
                            echo $filter["title"];
                            echo "</strong>\n                            \t\t\t\t\t</td>\n                            \t\t\t\t</tr>\n                            \t\t\t\t<tr>\n                            \t\t\t\t\t<td class=\"paddingb\">\n                            \t\t\t\t\t\t\t";
                            foreach ($aData as $key => $checkboxData) {
                                if(isset($checkboxData["key"]) && isset($checkboxData["value"])) {
                                    echo "<label><input type=\"checkbox\" name=\"" . $filter["field"] . "[]\" value=\"" . $checkboxData["value"] . "\" />" . $checkboxData["key"] . "</label><br />";
                                }
                            }
                            echo "                            \t\t\t\t\t</td>\n                            \t\t\t\t</tr>\n                    \t\t\t\t        ";
                        }
                        break;
                    case "country_invoice":
                        echo "\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t                \t<td>\n\t\t\t\t\t\t\t\t\t\t\t<strong>";
                        echo $filter["title"];
                        echo "</strong>\n\t\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t<td class=\"paddingb\">\n\t\t\t\t\t                        <select id=\"country\" name=\"value_";
                        echo $key3;
                        echo "\" class=\"text1 size4f\" disabled=\"disabled\">\n\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"all\">";
                        echo __("all countries");
                        echo "</option>\n\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"";
                        echo $company->Country;
                        echo "\">";
                        echo $array_country[$company->Country];
                        echo "</option>\n\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"eu\">";
                        echo __("eu countries");
                        echo "</option>\n\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"rest\">";
                        echo __("all countries outside europe");
                        echo "</option>\n\t\t\t\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t\t";
                        break;
                    default:
                        echo "\t\t\t\t\t\t\t";
                }
            }
            echo "\t\t\t\t\t\t\t</table>\n\t\t\t\t\t\t";
        } else {
            echo __("no export filters");
        }
        echo "\t\t\t\t\t\t</div>\n\t\t\t\t\t";
    }
}
echo "\t\t\t\t\t\n\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t<!--left-->\n\t\t\t\t</div>\n\t\t\t\t<!--left-->\n\n\t\t\t\t<!--right-->\n\t\t\t\t<div class=\"right\">\n\t\t\t\t<!--right-->\n\t\t\t\t\t\n\t\t\t\t\t<div class=\"setting_help_box\">\n\t\t\t\t\t\t";
echo __("explanation export");
echo "\t\t\t\t\t\t<br /><br />\t\t\t\t\t\t\t\n\t\t\t\t\t</div>\n\t\t\n\t\t\t\t<!--right-->\n\t\t\t\t</div>\n\t\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t<!--split2-->\n\t\t\t</div>\n\t\t\t<!--split2-->\n\n\t\t<!--content-->\n\t\t</div>\n\t\t<!--content-->\n\t\n\t<!--box1-->\n\t</div>\n\t<!--box1-->\n\t\n\t<br />\n\n\t<p class=\"align_right hide\">\n\t\t<span id=\"loader_download\" class=\"hide\">\n\t\t\t<img src=\"images/icon_circle_loader_green.gif\" alt=\"laden\" class=\"ico inline\" style=\"margin-bottom:-3px;\" />&nbsp;&nbsp;\n\t\t\t<span class=\"loading_green\">";
echo __("loading");
echo "</span>&nbsp;&nbsp;\n\t\t</span>\n\t\t<a class=\"button1 alt1 pointer\" id=\"download_export_btn\" onclick=\"\$('#loader_download').show();\"><span>";
echo __("download");
echo "</span></a>\n\t</p>\n\t\n<!--form-->\n</fieldset></form>\n<!--form-->\n\n<div id=\"export_externfilter\" class=\"hide\" title=\"";
echo __("filters");
echo "\">\n\t<form id=\"form_download_extern\" name=\"form_download_extern\" method=\"post\" action=\"export.php?page=download\">\n\t\t<input type=\"hidden\" name=\"id\" value=\"";
echo $template_id;
echo "\" />\n\t\t<input type=\"hidden\" name=\"dataset\" value=\"";
echo htmlspecialchars($dataset);
echo "\" />\n\t\t<input type=\"hidden\" name=\"template_type\" value=\"extern\" />\n\n\t\t";
if(isset($exportdata[$dataset]["filters"])) {
    echo "\t\t<table>\n\t\t";
    foreach ($exportdata[$dataset]["filters"] as $key3 => $filter) {
        echo "\t\t\t\n\t\t\t<input type=\"hidden\" id=\"filterkey\" name=\"filterkey\" value=\"";
        echo $template_id;
        echo "\" />\n\t\t\t\n\t\t\t";
        switch ($filter["type"]) {
            case "date":
                echo "\t\t\t\t<tr>\n                \t<td>\n\t\t\t\t\t\t<strong>";
                echo $filter["title"];
                echo "</strong>\n\t\t\t\t\t</td>\n\t\t\t\t</tr>\n\t\t\t\t<tr>\n\t\t\t\t\t<td class=\"paddingb\">\n\t\t\t\t\t\t<table>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td><input type=\"text\" class=\"text1 size7 datepicker\" class=\"form_input datepicker\" id=\"date_begin_";
                echo $template_id;
                echo "_";
                echo $key3;
                echo "\" name=\"value1_";
                echo $key3;
                echo "\" disabled=\"disabled\" /></td>\n\t\t\t\t\t\t\t\t<td style=\"padding: 0 15px;\" align=\"center\">";
                echo __("up to and including");
                echo "</td>\n\t\t\t\t\t\t\t\t<td><input type=\"text\" class=\"text1 size7 datepicker\" class=\"form_input datepicker\" id=\"date_end_";
                echo $template_id;
                echo "_";
                echo $key3;
                echo "\" name=\"value2_";
                echo $key3;
                echo "\" disabled=\"disabled\" /></td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t</table>\n\t\t\t\t\t</td>\n\t\t\t\t</tr>\n\t\t\t\t";
                break;
            case "id":
                echo "\t\t\t\t<tr>\n                \t<td>\n\t\t\t\t\t\t<strong>";
                echo $filter["title"];
                echo "</strong>\n\t\t\t\t\t</td>\n\t\t\t\t</tr>\n\t\t\t\t<tr>\n\t\t\t\t\t<td class=\"paddingb\">\n\t\t\t\t\t\t<table>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size7\" id=\"id_begin_";
                echo htmlspecialchars($dataset);
                echo "\" name=\"value1_";
                echo $key3;
                echo "\"  />\n\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t<td style=\"padding: 0 15px;\" align=\"center\">";
                echo __("up to and including");
                echo "</td>\n\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size7\" id=\"id_end_";
                echo htmlspecialchars($dataset);
                echo "\" name=\"value2_";
                echo $key3;
                echo "\"  />\n\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t</table>\n\t\t\t\t\t</td>\n\t\t\t\t</tr>\n\t\t\t\t";
                break;
            case "select":
                echo "\t\t\t\t<tr>\n                \t<td>\n\t\t\t\t\t\t<strong>";
                echo $filter["title"];
                echo "</strong>\n\t\t\t\t\t</td>\n\t\t\t\t</tr>\n\t\t\t\t<tr>\n\t\t\t\t\t<td class=\"paddingb\">\n                        <select id=\"status_";
                echo htmlspecialchars($dataset);
                echo "\" name=\"value_";
                echo $key3;
                echo "\" class=\"text1 size4f\" disabled=\"disabled\">\n\t\t\t\t\t\t\t<option value=\"\">Alle statussen</option>\n\t\t\t\t\t\t\t";
                foreach (${$filter["values"]} as $key => $value) {
                    echo "\t\t\t\t\t\t\t<option value=\"";
                    echo $key;
                    echo "\">";
                    echo $value;
                    echo "</option>\n\t\t\t\t\t\t\t";
                }
                echo "\t\t\t\t\t\t</select>\n\t\t\t\t\t</td>\n\t\t\t\t</tr>\n\t\t\t\t";
                break;
            case "country_invoice":
                echo "\t\t\t\t<tr>\n                \t<td>\n\t\t\t\t\t\t<strong>";
                echo $filter["title"];
                echo "</strong>\n\t\t\t\t\t</td>\n\t\t\t\t</tr>\n\t\t\t\t<tr>\n\t\t\t\t\t<td class=\"paddingb\">\n                        <select id=\"country\" name=\"value_";
                echo $key3;
                echo "\" class=\"text1 size4f\" disabled=\"disabled\">\n\t\t\t\t\t\t\t<option value=\"all\">";
                echo __("all countries");
                echo "</option>\n\t\t\t\t\t\t\t<option value=\"";
                echo $company->Country;
                echo "\">";
                echo $array_country[$company->Country];
                echo "</option>\n\t\t\t\t\t\t\t<option value=\"eu\">";
                echo __("eu countries");
                echo "</option>\n\t\t\t\t\t\t\t<option value=\"rest\">";
                echo __("all countries outside europe");
                echo "</option>\n\t\t\t\t\t\t</select>\n\t\t\t\t\t</td>\n\t\t\t\t</tr>\n\t\t\t\t";
                break;
            default:
                echo "\t\t";
        }
    }
    echo "\t\t</table>\n\t\t";
}
echo "  \t\t\n  \t\t<br />\n  \n\t\t<p><a id=\"download_extern_btn\" class=\"button1 alt1 float_left\"><span>";
echo __("download");
echo "</span></a></p>\n\t\t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#export_externfilter').dialog('close');\"><span>";
echo __("cancel");
echo "</span></a></p>\n\t</form>\n</div>\n\n";
require_once "views/footer.php";

?>