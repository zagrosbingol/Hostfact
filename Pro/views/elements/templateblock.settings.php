<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "<form method=\"post\" action=\"?page=show";
echo $template->Type;
if(!isset($is_add_page) || $is_add_page === false) {
    echo "&amp;id=";
    echo $template->Identifier;
}
echo "\" name=\"TemplateBlockEdit\" style=\"margin-bottom:20px;\">\n\t";
echo CSRF_Model::getToken();
echo "\t<input type=\"hidden\" name=\"action\" value=\"save_settings\" />\n\t";
if(!isset($is_add_page) || $is_add_page === false) {
    echo "\t<div class=\"heading1\">\t\n\t\t<h2>";
    echo __("templateblock settings - settings for template");
    echo "</h2>\n\t</div>\n\t";
}
echo "\n\t<div class=\"block_tabs\">\n\t\t<ul class=\"list3\">\n\t\t\t<li><a href=\"#tabs-other\">";
echo __("templateblock settings - tab general settings");
echo "</a></li>\n\t\t\t";
if($template->Type == "invoice" || $template->Type == "pricequote") {
    echo "<li><a href=\"#tabs-emailtemplate\">";
    echo __("templateblock settings - tab emailtemplate");
    echo "</a></li>";
}
echo "\t\t\t<li><a href=\"#tabs-pdftemplate\">";
echo __("templateblock settings - tab pdftemplate");
echo "</a></li>\n\t\t</ul>\n\n\t\t";
if($template->Type == "invoice" || $template->Type == "pricequote") {
    echo "\t\t<div id=\"tabs-emailtemplate\" class=\"content box3\">\t\n\t\t\n\t\t\t<!--box3-->\n\t\t\t<div class=\"box3\"><h3>";
    echo __("template");
    echo "</h3><div class=\"content\">\n\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t";
    if($template->Type == "invoice" || $template->Type == "pricequote") {
        echo "\t\t\t\t<strong class=\"title\">";
        echo __("emailtemplate");
        echo "</strong>\n\t\t\t\t<select name=\"EmailTemplate\" class=\"text1\">\n\t\t\t\t<option value=\"\">";
        echo __("please choose");
        echo "</option>\n\t\t\t\t";
        foreach ($emailtemplates as $key => $value) {
            if(is_numeric($key) && is_numeric($value["id"])) {
                echo "<option value=\"";
                echo $value["id"];
                echo "\" ";
                if($template->EmailTemplate == $value["id"]) {
                    echo "selected=\"selected\"";
                }
                echo ">";
                echo $value["Name"];
                echo "</option>\n                        ";
            }
        }
        echo "\t\t\t\t</select>\n\t\t\t\t<br /><br />\n\t\t\t\t";
    }
    echo "\n\t\t\t<!--box3-->\n\t\t\t</div></div>\n\t\t\t<!--box3-->\n\t\t\n\t\t</div>\n\t\t";
}
echo "\t\t\n\t\t<div id=\"tabs-pdftemplate\" class=\"content box3\">\n\t\t\n\t\t\t<!--box3-->\n\t\t\t<div class=\"box3\"><h3>";
echo __("writing paper");
echo "</h3><div class=\"content\">\n\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t<input type=\"hidden\" name=\"file_id_pdf_download\" />\n\t\t\t\t<input type=\"hidden\" name=\"file_id_pdf_print\" />\n\t\t\t\t<input type=\"hidden\" name=\"template_id\" value=\"";
echo $template->Identifier;
echo "\" />\n\t\t\t\t\n\t\t\t\t<strong class=\"title\">";
echo __("writing paper for download (PDF)");
echo ":</strong>\n\t\t\t\t<select class=\"text1 files_select size4f\" name=\"Location\">\n                <option value=\"\">";
echo __("please choose");
echo "</option>\n                ";
foreach ($pdf_source as $k => $pdf) {
    if(strpos($pdf, ".pdf") !== false) {
        $selected = $k == $template->Location ? "selected=\"selected\"" : "";
        echo "<option value=\"" . $k . "\" " . $selected . ">" . $pdf . "</option>";
    }
}
echo "                </select>\n\t\t\t\t<br /><br />\n\t\t\t\t\n\t\t\t\t<strong class=\"title\">";
echo __("writing paper for print (PDF)");
echo ":</strong>\n\t\t\t\t<select name=\"PostLocation\" class=\"text1 files_select size4f\">\n\t\t\t\t<option value=\"\">";
echo __("please choose");
echo "</option>\n                ";
foreach ($pdf_source as $k => $pdf) {
    if(strpos($pdf, ".pdf") !== false) {
        $selected = $k == $template->PostLocation ? "selected=\"selected\"" : "";
        echo "<option value=\"" . $k . "\" " . $selected . ">" . $pdf . "</option>";
    }
}
echo "\t\t\t\t</select>\n\t\t\t\t<br /><br />\n\t\t\t\t\n\t\t\t\t<a id=\"add_files_link\" class=\"a1 c1 ico inline add\">";
echo __("add file");
echo "</a>\n\n\t\t\t<!--box3-->\n\t\t\t</div></div>\n\t\t\t<!--box3-->\n\t\t\n\t\t</div>\n\t\t\n\t\t<div id=\"tabs-other\" class=\"content box3\">\t\n\t\t\n\t\t\t<div class=\"split2\">\n\t\t\t\t<div class=\"left\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("template");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("name template");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" name=\"Name\" value=\"";
echo $template->Name;
echo "\" class=\"text1 size1\" />\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("filename");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" name=\"FileName\" value=\"";
echo $template->FileName;
echo "\" class=\"text1 size1\" />\n\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\n\t\t\t\t</div>\n\t\t\t\t<div class=\"right\">\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t<div class=\"box3\"><h3>";
echo __("pdf properties");
echo "</h3><div class=\"content\">\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("title");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" name=\"Title\" value=\"";
echo $template->Title;
echo "\" class=\"text1 size1\" />\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("author");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" name=\"Author\" value=\"";
echo $template->Author;
echo "\" class=\"text1 size1\" />\n\t\t\t\t\t\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t</div></div>\n\t\t\t\t\t<!--box3-->\n\t\t\t\t\t\n\t\t\t\t</div>\n\t\t\t</div>\n\t\t</div>\n\t</div>\n</form>\n\n";
if(!isset($is_add_page) || $is_add_page === false) {
    echo "\t\n\t<p class=\"floatl\">\n\t\t<a class=\"a1 c1 close_without_saving\" style=\"line-height:30px;\">";
    echo __("templateblock settings - close without saving");
    echo "</a>\n\t</p>\n\t";
}
echo "\n<p class=\"floatr\">\n\t<a id=\"save_settings\" class=\"button1 alt1 pointer\">\n        <span>";
echo !isset($is_add_page) || $is_add_page === false ? __("btn edit") : __("btn add");
echo "</span>\n    </a>\n</p>\n\n<br clear=\"both\"/>\n\n<style type=\"text/css\">\n\t.block_tabs .box3 { margin-bottom: 0px !important;}\n\t.block_tabs > div.box3 { display:none; }\n</style>\n<script type=\"text/javascript\">\n\$(function(){\n\t\$( \".block_tabs\" ).tabs();\n\t\$( \".block_tabs\" ).tabs(\"option\", \"active\", ";
echo $selected_tab;
echo ");\n\t\n\t\$('#save_settings').click(function(){\n\t\t\$('form[name=\"TemplateBlockEdit\"]').submit();\n\t});\n\t\n\t\$('.close_without_saving').click(function(){\n\t\t\$('#template_block_edit').html('').hide();\t\n\t\t\$('#template_canvas_overlay').hide();\n\t});\n\t\n\t\$('#add_files_link').click(function(){\n\t\tfiletype = 'pdf_files';\n\t\t\$('#filemanager').dialog('open');\t\n\t});\n\n});\n</script>";

?>