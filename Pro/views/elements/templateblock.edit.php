<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "<link href=\"js/spectrum/spectrum.css\" rel=\"stylesheet\" />\n<script src=\"js/spectrum/spectrum.js\" type=\"text/javascript\"></script>\n\n<form method=\"post\" action=\"?page=show";
echo $template->Type;
echo "&amp;id=";
echo $template->Identifier;
echo "\" name=\"TemplateBlockEdit\" style=\"margin-bottom:20px;\">\n\t";
echo CSRF_Model::getToken();
echo "\t<input type=\"hidden\" name=\"block_id\" value=\"";
echo $block_id;
echo "\" />\n\t<input type=\"hidden\" name=\"action\" value=\"save_block\" />\n\t<div class=\"heading1\">\n\t\t<h2>";
echo __("templateblock edit - edit block");
echo "</h2>\n\t\t";
if(!in_array($block["type"], [templateblock::BLOCK_TYPE_LINES, templateblock::BLOCK_TYPE_TOTALS])) {
    echo "<a class=\"a1 c1 floatr\" href=\"?page=show";
    echo $template->Type;
    echo "&amp;id=";
    echo $template->Identifier;
    echo "&amp;action=delete_block&amp;block_id=";
    echo $block_id;
    echo "\">";
    echo __("templateblock edit - delete block");
    echo "</a>";
}
echo "\t</div>\n\n\t";
if(!in_array($block["type"], [templateblock::BLOCK_TYPE_IMAGE, templateblock::BLOCK_TYPE_QR_CODE])) {
    echo "\t\t<div style=\"position:absolute;background:#fff;top:0px;right:-342px; padding:22px 10px 10px 10px; width: 300px; border-radius:5px;\">\n\t\t\t<div class=\"heading1\">\n\t\t\t\t<h2>";
    echo __("templateblock edit - available variables");
    echo "</h2>\n\t\t\t</div>\n\n\t\t\t<div id=\"variable_search\"><input type=\"text\" name=\"SearchVariable\" class=\"text1 size1\" /></div>\n\n\t\t\t<br />\n\n\t\t\t";
    $variable_list_show_pdf = true;
    $variable_list_show_invoice = $template->Type != "pricequote" ? true : false;
    $variable_list_show_invoice_elements = $template->Type == "invoice" && $block["type"] == templateblock::BLOCK_TYPE_LINES ? true : false;
    $variable_list_show_pricequote = $template->Type == "pricequote" ? true : false;
    $variable_list_show_pricequote_elements = $template->Type == "pricequote" && $block["type"] == templateblock::BLOCK_TYPE_LINES ? true : false;
    $variable_list_show_services = $template->Type == "other" ? true : false;
    if($template->Type == "invoice" && $block["type"] == templateblock::BLOCK_TYPE_LINES) {
        $variable_list_show_services = "servicetype_only";
    }
    include_once "views/elements/variables_list.php";
    echo "\t\t</div>\n\t\t";
}
echo "\n\t<div class=\"block_tabs\">\n\t\t<ul>\n\t\t\t<li><a href=\"#tabs-value\">";
echo __("templateblock edit - tab value");
echo "</a></li>\n\t\t\t";
if(!in_array($block["type"], [templateblock::BLOCK_TYPE_IMAGE, templateblock::BLOCK_TYPE_LINES, templateblock::BLOCK_TYPE_QR_CODE])) {
    echo "<li><a href=\"#tabs-text\">";
    echo __("templateblock edit - tab text");
    echo "</a></li>";
}
echo "\t\t\t";
if(!in_array($block["type"], [templateblock::BLOCK_TYPE_LINES, templateblock::BLOCK_TYPE_QR_CODE])) {
    echo "<li><a href=\"#tabs-borders\">";
    echo __("templateblock edit - tab borders");
    echo "</a></li>";
}
echo "\t\t\t";
if(!in_array($block["type"], [templateblock::BLOCK_TYPE_LINES, templateblock::BLOCK_TYPE_TOTALS, templateblock::BLOCK_TYPE_TABLE, templateblock::BLOCK_TYPE_QR_CODE])) {
    echo "<li><a href=\"#tabs-style\">";
    echo __("templateblock edit - tab style");
    echo "</a></li>";
}
echo "\n\t\t\t";
if(in_array($block["type"], [templateblock::BLOCK_TYPE_LINES])) {
    echo "\t\t\t\t<li><a href=\"#tabs-table-head\">";
    echo __("templateblock edit - tab style thead");
    echo "</a></li>\n\t\t\t\t<li><a href=\"#tabs-table-rows\">";
    echo __("templateblock edit - tab style tbody");
    echo "</a></li>\n\t\t\t\t";
    if($template->Type == "invoice") {
        echo "<li><a href=\"#tabs-invoicelines-description\">";
        echo __("templateblock edit - tab extended description");
        echo "</a></li>";
    }
    echo "\t\t\t";
}
echo "\t\t\t<li><a href=\"#tabs-positioning\">";
echo __("templateblock edit - tab positioning");
echo "</a></li>\n\t\t\t<li><a href=\"#tabs-visibility\">";
echo __("templateblock edit - tab visibility");
echo "</a></li>\n\t\t</ul>\n\n\t\t<div id=\"tabs-value\" class=\"content box3\">\n\n\t\t\t";
switch ($block["type"]) {
    case templateblock::BLOCK_TYPE_IMAGE:
        echo "\t\t\t\t\t<h3>";
        echo __("templateblock edit - image h3");
        echo "</h3>\n\t\t\t\t\t<div class=\"content block_value_img\">\n\n\t\t\t\t\t\t\n\t\t\t\t\t\t<p>\n\t\t\t\t\t\t\t<a id=\"add_image_file\" data-filetype=\"template_image_files\" class=\"button1 alt1\">\n\t\t\t\t\t\t\t\t";
        echo __("add file");
        echo "\t\t\t\t\t\t\t</a>\n\t\t\t\t\t\t</p>\n\n\t\t\t\t\t\t<input type=\"hidden\" class=\"text1 size12\" name=\"block_value\" value=\"";
        echo htmlspecialchars($block["value"]);
        echo "\"/>\n\t\t\t\t\t\t<span id=\"image_helper_loader\" class=\"loading_float\"></span>\n\t\t\t\t\t\t<br/>\n\n\t\t\t\t\t\t<div id=\"image_helper_div\" class=\"";
        if(!$block["value"]) {
            echo "hide";
        }
        echo "\">\n\t\t\t\t\t\t\t<strong class=\"title\">";
        echo __("templateblock edit - image preview");
        echo "</strong>\n\t\t\t\t\t\t\t<img id=\"image_helper_img\" src=\"";
        echo htmlspecialchars($block["value"]);
        echo "\" alt=\"\" style=\"max-width: 750px; max-height: 400px\"/>\n\t\t\t\t\t\t</div>\n\n\t\t\t\t\t</div>\n\t\t\t\t\t";
        break;
    case templateblock::BLOCK_TYPE_QR_CODE:
        $width = $block_model->mm_to_px($block["positioning"]["w"]);
        echo "                    <h3>";
        echo __("templateblock edit - value h3");
        echo "</h3>\n\n                    <div class=\"content\">\n                        <table border=\"0\" cellpadding=\"3\" cellspacing=\"0\">\n                            <tr>\n                                <td width=\"";
        echo $width + 30;
        echo "\"><img src=\"";
        echo $block_model->generateExampleQRCode($width);
        echo "\" style=\"max-width:";
        echo $width;
        echo "px\" /></td>\n                                <td>\n                                    <p><strong>";
        echo __("templateblock edit - qrcode help title");
        echo "</strong></p>\n                                    <p>";
        echo __("templateblock edit - qrcode help description");
        echo "<p/>\n                                </td>\n                            </tr>\n                        </table>\n                    </div>\n                    ";
        break;
    case "text":
        echo "\t\t\t\t\t<h3>";
        echo __("templateblock edit - value h3");
        echo "</h3>\n\t\t\t\t\t<div class=\"content\">\n\t\t\t\t\t\t<strong class=\"title\">";
        echo __("templateblock edit - value h3");
        echo "</strong>\n\t\t\t\t\t\t<textarea class=\"text1 size5\" name=\"block_value\">";
        echo htmlspecialchars($block["value"]);
        echo "</textarea>\n\t\t\t\t\t</div>\n\t\t\t\t\t";
        break;
    case templateblock::BLOCK_TYPE_TABLE:
        echo "\t\t\t\t\t<h3>";
        echo __("templateblock edit - table h3");
        echo "</h3>\n\t\t\t\t\t<div class=\"content block_value_table\">\n\n\t\t\t\t\t\t<div style=\"width:100%; overflow:auto;white-space: nowrap\">\n\t\t\t\t\t\t\t<table id=\"templateblock_table\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"table1 zebra_table\" style=\"width:auto;float:left;\">\n\t\t\t\t\t\t\t\t<thead>\n\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t<th width=\"10\">&nbsp;</th>\n\t\t\t\t\t\t\t\t\t";
        foreach ($block["value"][0] as $col_id => $value) {
            echo "<th width=\"220\">\n\t\t\t\t\t\t\t\t\t\t";
            echo __("templateblock edit - table col");
            echo " <span class=\"col_id\">";
            echo $col_id + 1;
            echo "</span>\n\t\t\t\t\t\t\t\t\t\t<span class=\"del_col\">&nbsp;</span>\n\t\t\t\t\t\t\t\t\t\t</th>";
        }
        echo "\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t</thead>\n\t\t\t\t\t\t\t\t<tbody>\n\n\t\t\t\t\t\t\t\t";
        foreach ($block["value"] as $row_id => $record) {
            echo "\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t\t\t<img class=\"pointer sortablehandle sort_icon\" src=\"images/ico_sort.png\" />\n\t\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t\t";
            foreach ($record as $col_id => $value) {
                echo "<td valign=\"top\" width=\"220\"><input type=\"text\" name=\"block_value[";
                echo $row_id;
                echo "][";
                echo $col_id;
                echo "]\" class=\"text1 size1\" value=\"";
                echo htmlspecialchars($value);
                echo "\" /></td>";
            }
            echo "\t\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\t\t\t</tbody>\n\t\t\t\t\t\t\t</table>\n\t\t\t\t\t\t\t<a id=\"add_column\" class=\"a1 c1 ico inline add pointer\" style=\"margin: 30px 0 0 10px;\">";
        echo __("templateblock edit - table col");
        echo "</a>\n\t\t\t\t\t\t\t<div style=\"clear:both\"></div>\n\n\n\t\t\t\t\t\t\t<a id=\"add_row\" class=\"a1 c1 ico inline add pointer\" style=\"margin:0 30px 0 6px;\">";
        echo __("templateblock edit - table row");
        echo "</a><br />\n\t\t\t\t\t\t\t<br />\n\n\t\t\t\t\t\t\t<strong class=\"column_options_line\" style=\"width:";
        echo count($block["value"][0]) * 230;
        echo "px;\">";
        echo __("templateblock edit - table col options");
        echo "</strong>\n\t\t\t\t\t\t\t<table id=\"templateblock_cols\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"table1\">\n\t\t\t\t\t\t\t\t<tr class=\"tr_align\">\n\t\t\t\t\t\t\t\t\t";
        foreach ($block["value"][0] as $col_id => $value) {
            echo "<td valign=\"top\" width=\"220\">\n\t\t\t\t\t\t\t\t\t\t<font class=\"smallfont\" style=\"color: #999;\">";
            echo __("templateblock edit - table alignment");
            echo "</font><br />\n\t\t\t\t\t\t\t\t\t\t<select name=\"block_cols[";
            echo $col_id;
            echo "][text][align]\" class=\"text1\" style=\"width:210px;margin-top:4px;\">\n\t\t\t\t\t\t\t\t\t\t\t<option value=\"left\" ";
            if($block["cols"][$col_id]["style"]["format"] == "" && $block["cols"][$col_id]["text"]["align"] == "left") {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo __("templateblock edit - table alignment left");
            echo "</option>\n\t\t\t\t\t\t\t\t\t\t\t<option value=\"right\" ";
            if($block["cols"][$col_id]["style"]["format"] == "" && $block["cols"][$col_id]["text"]["align"] == "right") {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo __("templateblock edit - table alignment right");
            echo "</option>\n\t\t\t\t\t\t\t\t\t\t\t<option value=\"center\" ";
            if($block["cols"][$col_id]["style"]["format"] == "" && $block["cols"][$col_id]["text"]["align"] == "center") {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo __("templateblock edit - table alignment center");
            echo "</option>\n\t\t\t\t\t\t\t\t\t\t\t<option value=\"money\" ";
            if($block["cols"][$col_id]["style"]["format"] == "money") {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo __("templateblock edit - table alignment amount");
            echo "</option>\n\t\t\t\t\t\t\t\t\t\t</select></td>";
        }
        echo "\t\t\t\t\t\t\t\t</tr>\n\n\t\t\t\t\t\t\t\t<tr class=\"tr_width\">\n\t\t\t\t\t\t\t\t\t";
        foreach ($block["value"][0] as $col_id => $value) {
            echo "<td valign=\"top\" width=\"220\">\n\t\t\t\t\t\t\t\t\t\t<font class=\"smallfont col_options_width_label\" style=\"color: #999;\">";
            echo __("templateblock edit - table col width mm");
            echo "</font><br />\n\t\t\t\t\t\t\t\t\t\t<input type=\"text\" name=\"block_cols[";
            echo $col_id;
            echo "][positioning][w]\" class=\"text1 size6 col_options_width\" value=\"";
            if(0 < $block["cols"][$col_id]["positioning"]["w"]) {
                echo $block["cols"][$col_id]["positioning"]["w"];
            }
            echo "\" style=\"margin-top:4px;\"/></td>";
        }
        echo "\n\t\t\t\t\t\t\t\t</tr>\n\n\t\t\t\t\t\t\t</table>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\t\t\t\t\t";
        break;
    case templateblock::BLOCK_TYPE_LINES:
        echo "\t\t\t\t\t<h3>";
        echo __("templateblock edit - table h3");
        echo "</h3>\n\t\t\t\t\t<div class=\"content block_value_invoicelines\">\n\t\t\t\t\t\t<strong>";
        echo __("templateblock edit - table col-row warning");
        echo "</strong><br />\n\n\t\t\t\t\t\t<table id=\"templateblock_table\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"table1 zebra_table\" style=\"position:relative;\">\n\t\t\t\t\t\t\t<thead>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<th width=\"10\">&nbsp;</th>\n\t\t\t\t\t\t\t\t<th width=\"135\">";
        echo __("templateblock edit - table label");
        echo "</th>\n\t\t\t\t\t\t\t\t<th>";
        echo __("templateblock edit - table value");
        echo "</th>\n\t\t\t\t\t\t\t\t<th width=\"140\">";
        echo __("templateblock edit - table alignment");
        echo "</th>\n\t\t\t\t\t\t\t\t<th width=\"90\">";
        echo __("templateblock edit - table col width mm");
        echo "</th>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t</thead>\n\t\t\t\t\t\t\t<tbody>\n\t\t\t\t\t\t\t";
        foreach ($block["cols"] as $col_id => $column) {
            $colFormatting = $block["cols"][$col_id]["style"]["format"] ?? "";
            echo "\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t<td valign=\"top\"><img class=\"pointer sortablehandle sort_icon\" src=\"images/ico_sort.png\" style=\"margin-top:5px;\" /></td>\n\t\t\t\t\t\t\t\t\t<td valign=\"top\"><input type=\"text\" name=\"block_value[0][";
            echo $col_id;
            echo "]\" class=\"text1 size7\" value=\"";
            echo htmlspecialchars($block["value"][0][$col_id]);
            echo "\" /></td>\n\t\t\t\t\t\t\t\t\t<td valign=\"top\">\n\t\t\t\t\t\t\t\t\t\t<textarea name=\"block_value[1][";
            echo $col_id;
            echo "]\" class=\"text1 autogrow\" style=\"width:320px;margin-top:0px;\">";
            echo htmlspecialchars($block["value"][1][$col_id]);
            echo "</textarea>\n\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t<td valign=\"top\"><select name=\"block_cols[";
            echo $col_id;
            echo "][text][align]\" class=\"text1\" style=\"width:140px;\">\n\t\t\t\t\t\t\t\t\t\t\t<option value=\"left\" ";
            if($colFormatting == "" && $block["cols"][$col_id]["text"]["align"] == "left") {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo __("templateblock edit - table alignment left");
            echo "</option>\n\t\t\t\t\t\t\t\t\t\t\t<option value=\"right\" ";
            if($colFormatting == "" && $block["cols"][$col_id]["text"]["align"] == "right") {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo __("templateblock edit - table alignment right");
            echo "</option>\n\n\t\t\t\t\t\t\t\t\t\t\t<option value=\"center\" ";
            if($colFormatting == "" && $block["cols"][$col_id]["text"]["align"] == "center") {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo __("templateblock edit - table alignment center");
            echo "</option>\n\t\t\t\t\t\t\t\t\t\t\t<option value=\"money_left\" ";
            if($colFormatting == "money" && $block["cols"][$col_id]["text"]["align"] == "left") {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo __("templateblock edit - table alignment amount left");
            echo "</option>\n\t\t\t\t\t\t\t\t\t\t\t<option value=\"money_right\" ";
            if($colFormatting == "money" && $block["cols"][$col_id]["text"]["align"] == "right") {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo __("templateblock edit - table alignment amount right");
            echo "</option>\n\t\t\t\t\t\t\t\t\t\t</select></td>\n\t\t\t\t\t\t\t\t\t<td valign=\"top\"><input type=\"text\" name=\"block_cols[";
            echo $col_id;
            echo "][positioning][w]\" class=\"text1 size6 col_options_width\" value=\"";
            if(0 < $block["cols"][$col_id]["positioning"]["w"]) {
                echo $block["cols"][$col_id]["positioning"]["w"];
            }
            echo "\"/></td>\n\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\t\t</tbody>\n\t\t\t\t\t\t</table>\n\n\t\t\t\t\t\t<a id=\"add_column\" class=\"a1 c1 ico inline add pointer\" style=\"margin:0 30px 0 6px;\">";
        echo __("templateblock edit - table col");
        echo "</a><br />\n\t\t\t\t\t\t<br />\n\t\t\t\t\t</div>\n\t\t\t\t\t";
        break;
    case templateblock::BLOCK_TYPE_TOTALS:
        echo "\t\t\t\t\t<h3>";
        echo __("templateblock edit - totals h3");
        echo "</h3>\n\t\t\t\t\t<div class=\"content block_value_totals\">\n\n\t\t\t\t\t\t<table id=\"templateblock_table\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"table1 zebra_table\" style=\"width:auto; position:relative;\">\n\t\t\t\t\t\t\t<thead>\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t<th width=\"10\">&nbsp;</th>\n\t\t\t\t\t\t\t\t<th width=\"220\">";
        echo __("templateblock edit - table label");
        echo "</th>\n\t\t\t\t\t\t\t\t<th width=\"220\">";
        echo __("templateblock edit - table value");
        echo "</th>\n\t\t\t\t\t\t\t\t<th class=\"col_borders\">";
        echo __("templateblock edit - table borders");
        echo "</th>\n\t\t\t\t\t\t\t\t<th class=\"col_bgcolor\">";
        echo __("templateblock edit - table bgcolor");
        echo "</th>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t</thead>\n\t\t\t\t\t\t\t<tbody>\n\t\t\t\t\t\t\t";
        $used_totaltypes = [];
        $has_line_borders = false;
        $has_line_bgcolor = false;
        foreach ($block["value"] as $row_id => $record) {
            echo "\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t<td><img class=\"pointer sortablehandle sort_icon\" src=\"images/ico_sort.png\" /></td>\n\t\t\t\t\t\t\t\t\t";
            if($block["rows"][$row_id]["style"]["totaltype"]) {
                $used_totaltypes[] = $block["rows"][$row_id]["style"]["totaltype"];
                if($block["rows"][$row_id]["style"]["totaltype"] == "amounttax") {
                    echo "<td width=\"450\" valign=\"top\" colspan=\"2\">";
                } else {
                    echo "<td width=\"220\"><input type=\"text\" name=\"block_value[";
                    echo $row_id;
                    echo "][0]\" class=\"text1 size1\" value=\"";
                    echo htmlspecialchars($record[0]);
                    echo "\" /></td><td width=\"220\">";
                }
                echo "<input type=\"hidden\" name=\"block_rows[";
                echo $row_id;
                echo "][style][totaltype]\" value=\"";
                echo $block["rows"][$row_id]["style"]["totaltype"];
                echo "\"/><input type=\"hidden\" name=\"block_value[";
                echo $row_id;
                echo "][1]\" class=\"text1 size1\" value=\"\" />\n\t\t\t\t\t\t\t\t\t\t";
                echo __("templateblock totaltype " . $block["rows"][$row_id]["style"]["totaltype"]);
                if($block["rows"][$row_id]["style"]["totaltype"] == "pagetotalexcl" || $block["rows"][$row_id]["style"]["totaltype"] == "pagetotalincl") {
                    echo "<br><font color=\"gray\" style=\"display:inline-block;margin-top:-10px;\" class=\"smallfont\">(" . __("templateblock totaltype only multiple pages") . ")</font>";
                }
                echo "</td>";
            } else {
                echo "\t\t\t\t\t\t\t\t\t\t<td width=\"220\"><input type=\"text\" name=\"block_value[";
                echo $row_id;
                echo "][0]\" class=\"text1 size1\" value=\"";
                echo htmlspecialchars($record[0]);
                echo "\" /></td>\n\t\t\t\t\t\t\t\t\t\t<td width=\"220\"><input type=\"text\" name=\"block_value[";
                echo $row_id;
                echo "][1]\" class=\"text1 size1\" value=\"";
                echo htmlspecialchars($record[1]);
                echo "\" /></td>";
            }
            echo "\t\t\t\t\t\t\t\t\t<td class=\"col_borders\">\n\t\t\t\t\t\t\t\t\t\t<label class=\"infopopuptop delaypopup\"><input type=\"checkbox\" name=\"block_rows[";
            echo $row_id;
            echo "][borders][top]\" value=\"yes\" ";
            if($block["rows"][$row_id]["borders"]["top"] == "yes") {
                $has_line_borders = true;
                echo "checked=\"checked\"";
            }
            echo "/><span class=\"popup\">";
            echo __("templateblock edit - table border top");
            echo "<b></b></span></label>\n\t\t\t\t\t\t\t\t\t\t<label class=\"infopopuptop delaypopup\"><input type=\"checkbox\" name=\"block_rows[";
            echo $row_id;
            echo "][borders][right]\" value=\"yes\" ";
            if($block["rows"][$row_id]["borders"]["right"] == "yes") {
                $has_line_borders = true;
                echo "checked=\"checked\"";
            }
            echo "/><span class=\"popup\">";
            echo __("templateblock edit - table border right");
            echo "<b></b></span></label>\n\t\t\t\t\t\t\t\t\t\t<label class=\"infopopuptop delaypopup\"><input type=\"checkbox\" name=\"block_rows[";
            echo $row_id;
            echo "][borders][bottom]\" value=\"yes\" ";
            if($block["rows"][$row_id]["borders"]["bottom"] == "yes") {
                $has_line_borders = true;
                echo "checked=\"checked\"";
            }
            echo "/><span class=\"popup\">";
            echo __("templateblock edit - table border bottom");
            echo "<b></b></span></label>\n\t\t\t\t\t\t\t\t\t\t<label class=\"infopopuptop delaypopup\"><input type=\"checkbox\" name=\"block_rows[";
            echo $row_id;
            echo "][borders][left]\" value=\"yes\" ";
            if($block["rows"][$row_id]["borders"]["left"] == "yes") {
                $has_line_borders = true;
                echo "checked=\"checked\"";
            }
            echo "/><span class=\"popup\">";
            echo __("templateblock edit - table border left");
            echo "<b></b></span></label>\n\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t<td class=\"col_bgcolor\"><input type=\"text\" name=\"block_rows[";
            echo $row_id;
            echo "][style][bgcolor]\" class=\"text1 size6 colorpicker\" value=\"";
            if($block["rows"][$row_id]["style"]["bgcolor"]) {
                $has_line_bgcolor = true;
                echo $block["rows"][$row_id]["style"]["bgcolor"];
            }
            echo "\" /></td>\n\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\t\t</tbody>\n\t\t\t\t\t\t</table>\n\t\t\t\t\t\t<br/>\n\n\t\t\t\t\t\t<strong>";
        echo __("templateblock edit - table add a row");
        echo "</strong><br />\n\t\t\t\t\t\t<div class=\"add_default_row_div\">\n\t\t\t\t\t\t\t<a id=\"drd_amountexcl\" onclick=\"addDefaultTotalRow('amountexcl');\" class=\"a1 c1";
        if(in_array("amountexcl", $used_totaltypes)) {
            echo " hide";
        }
        echo "\">";
        echo __("templateblock totaltype amountexcl");
        echo "</a>\n\t\t\t\t\t\t\t<a id=\"drd_amounttax\" onclick=\"addDefaultTotalRow('amounttax');\" class=\"a1 c1";
        if(in_array("amounttax", $used_totaltypes)) {
            echo " hide";
        }
        echo "\">";
        echo __("templateblock totaltype amounttax");
        echo "</a>\n\t\t\t\t\t\t\t<a id=\"drd_amountincl\" onclick=\"addDefaultTotalRow('amountincl');\" class=\"a1 c1";
        if(in_array("amountincl", $used_totaltypes)) {
            echo " hide";
        }
        echo "\">";
        echo __("templateblock totaltype amountincl");
        echo "</a>\n\t\t\t\t\t\t\t<a id=\"drd_pagetotalexcl\" onclick=\"addDefaultTotalRow('pagetotalexcl');\" class=\"a1 c1";
        if(in_array("pagetotalexcl", $used_totaltypes)) {
            echo " hide";
        }
        echo "\">";
        echo __("templateblock totaltype pagetotalexcl");
        echo "</a>\n\t\t\t\t\t\t\t<a id=\"drd_pagetotalincl\" onclick=\"addDefaultTotalRow('pagetotalincl');\" class=\"a1 c1";
        if(in_array("pagetotalincl", $used_totaltypes)) {
            echo " hide";
        }
        echo "\">";
        echo __("templateblock totaltype pagetotalincl");
        echo "</a>\n\t\t\t\t\t\t</div>\n\n\t\t\t\t\t\t<span class=\"add_default_row_div ";
        if(count($used_totaltypes) == 5) {
            echo "hide";
        }
        echo "\">of </span><a id=\"add_row\" class=\"a1 c1\">";
        echo __("templateblock edit - table create own row");
        echo "</a>\n\t\t\t\t\t\t<br />\n\t\t\t\t\t\t<br />\n\n\t\t\t\t\t\t<div style=\"float:left;width:270px;\">\n\n\t\t\t\t\t\t\t<strong>";
        echo __("templateblock edit - table col label alignment");
        echo "</strong><br />\n\t\t\t\t\t\t\t<select name=\"block_cols[0][text][align]\" class=\"text1\" style=\"width:210px;\">\n\t\t\t\t\t\t\t\t<option value=\"left\" ";
        if($block["cols"][0]["style"]["format"] == "" && $block["cols"][0]["text"]["align"] == "left") {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo __("templateblock edit - table alignment left");
        echo "</option>\n\t\t\t\t\t\t\t\t<option value=\"right\" ";
        if($block["cols"][0]["style"]["format"] == "" && $block["cols"][0]["text"]["align"] == "right") {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo __("templateblock edit - table alignment right");
        echo "</option>\n\t\t\t\t\t\t\t\t<option value=\"center\" ";
        if($block["cols"][0]["style"]["format"] == "" && $block["cols"][0]["text"]["align"] == "center") {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo __("templateblock edit - table alignment center");
        echo "</option>\n\t\t\t\t\t\t\t\t<option value=\"money\" ";
        if($block["cols"][0]["style"]["format"] == "money") {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo __("templateblock edit - table alignment amount");
        echo "</option>\n\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t<br /><br />\n\n\t\t\t\t\t\t\t<strong>";
        echo __("templateblock edit - table col label width mm");
        echo "</strong><br />\n\t\t\t\t\t\t\t<input type=\"text\" name=\"block_cols[0][positioning][w]\" class=\"text1 size6\" value=\"";
        if(0 < $block["cols"][0]["positioning"]["w"]) {
            echo $block["cols"][0]["positioning"]["w"];
        }
        echo "\" />\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\t<div style=\"float:left;\">\n\n\t\t\t\t\t\t\t<strong>";
        echo __("templateblock edit - table extra options");
        echo "</strong><br />\n\t\t\t\t\t\t\t<label><input type=\"checkbox\" name=\"has_line_borders\" value=\"yes\" ";
        if($has_line_borders) {
            echo "checked=\"checked\"";
        }
        echo "/> ";
        echo __("templateblock edit - table extra option borders between rows");
        echo "</label><br />\n\t\t\t\t\t\t\t<label><input type=\"checkbox\" name=\"has_line_bgcolor\" value=\"yes\" ";
        if($has_line_bgcolor) {
            echo "checked=\"checked\"";
        }
        echo "/> ";
        echo __("templateblock edit - table extra option bgcolor rows");
        echo "</label><br />\n\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\t<br clear=\"both\" /><br />\n\t\t\t\t\t</div>\n\t\t\t\t\t";
        break;
    default:
        echo "\n\t\t</div>\n\t\t<div id=\"tabs-positioning\" class=\"content box3\">\n\t\t\t<h3>";
        echo __("templateblock edit - positioning h3");
        echo "</h3>\n\n\t\t\t<div class=\"content\">\n\n\t\t\t\t<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\">\n\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td colspan=\"4\"><strong>";
        echo __("templateblock edit - position");
        echo "</strong></td>\n\t\t\t\t\t</tr>\n\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td width=\"60\">";
        echo __("templateblock edit - position x-axis");
        echo ":</td>\n\t\t\t\t\t\t<td width=\"150\"><input type=\"text\" class=\"text1 size6\" name=\"block_positioning_x\" value=\"";
        echo $block["positioning"]["x"];
        echo "\"/> ";
        echo __("templateblock edit - mm");
        echo "</td>\n\t\t\t\t\t\t<td width=\"60\">";
        echo __("templateblock edit - position y-axis");
        echo ":</td>\n\t\t\t\t\t\t<td><input type=\"text\" class=\"text1 size6\" name=\"block_positioning_y\" value=\"";
        echo $block["positioning"]["y"];
        echo "\"/> ";
        echo __("templateblock edit - mm");
        echo "</td>\n\t\t\t\t\t</tr>\n\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td colspan=\"4\"><br /><strong>";
        echo __("templateblock edit - size");
        echo "</strong></td>\n\t\t\t\t\t</tr>\n\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td>";
        echo __("templateblock edit - size width");
        echo ":</td>\n\t\t\t\t\t\t<td><input type=\"text\" class=\"text1 size6\" name=\"block_positioning_w\" value=\"";
        echo $block["positioning"]["w"];
        echo "\"/> ";
        echo __("templateblock edit - mm");
        echo "</td>\n\n                        ";
        if($block["type"] == templateblock::BLOCK_TYPE_QR_CODE) {
            echo "                            <td colspan=\"2\">";
            echo __("templateblock edit - size width qr minimum", ["min" => templateblock::QR_CODE_MIN_SIZE]);
            echo "</td>\n                        ";
        } else {
            echo "                            <td>";
            echo __("templateblock edit - size height");
            echo ":</td>\n                            <td><input class=\"text1 size6\" name=\"block_positioning_h\" value=\"";
            echo $block["positioning"]["h"];
            echo "\"/> ";
            echo __("templateblock edit - mm");
            echo "</td>\n                        ";
        }
        echo "\t\t\t\t\t</tr>\n\t\t\t\t</table>\n\t\t\t</div>\n\t\t</div>\n\t\t";
        if(!in_array($block["type"], [templateblock::BLOCK_TYPE_IMAGE, templateblock::BLOCK_TYPE_LINES])) {
            echo "\t\t\t<div id=\"tabs-text\" class=\"content box3\">\n\t\t\t\t<h3>";
            echo __("templateblock edit - text h3");
            echo "</h3>\n\n\t\t\t\t<div class=\"content\">\n\t\t\t\t\t<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\">\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td width=\"320\"><strong>";
            echo __("templateblock edit - text family");
            echo "</strong></td>\n\t\t\t\t\t\t\t<td><strong>";
            echo __("templateblock edit - text size");
            echo "</strong></td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td><select class=\"text1 size1\" name=\"block_text_family\">\n\t\t\t\t\t\t\t\t\t<option value=\"";
            echo $k;
            echo "\" ";
            if($block["text"]["family"] == "") {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo __("please choose");
            echo "</option>\n\t\t\t\t\t\t\t\t\t";
            $fonts_displayed = [];
            foreach ($available_font_families as $optgroup => $tmp_fonts) {
                if(!empty($tmp_fonts)) {
                    echo "<optgroup label=\"";
                    echo __("templateblock edit - text family group " . $optgroup);
                    echo "\">";
                    foreach ($tmp_fonts as $k => $v) {
                        if($block["text"]["family"] != $k && in_array(preg_replace("/li.ttf|b.ttf|i.ttf|l.ttf|z.ttf/", ".ttf", $v["title"]), $fonts_displayed)) {
                        } else {
                            $fonts_displayed[] = $v["title"];
                            echo "<option value=\"";
                            echo $k;
                            echo "\" ";
                            if($block["text"]["family"] == $k) {
                                echo "selected=\"selected\"";
                            }
                            echo ">";
                            echo $v["filename"];
                            echo "</option>";
                        }
                    }
                    echo "</optgroup>";
                }
            }
            echo "\t\t\t\t\t\t\t\t</select></td>\n\t\t\t\t\t\t\t<td><select class=\"text1 size6\" name=\"block_text_size\">\n\t\t\t\t\t\t\t\t\t";
            foreach ($available_font_sizes as $k => $v) {
                echo "<option value=\"";
                echo $k;
                echo "\" ";
                if($block["text"]["size"] == $k) {
                    echo "selected=\"selected\"";
                }
                echo ">";
                echo $v;
                echo "</option>";
            }
            echo "\t\t\t\t\t\t\t\t</select></td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td colspan=\"2\">&nbsp;</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td><strong>";
            echo __("templateblock edit - text style");
            echo "</strong></td>\n\t\t\t\t\t\t\t<td><strong>";
            echo __("templateblock edit - text alignment");
            echo "</strong></td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td><select class=\"text1 size1\" name=\"block_text_style\">\n\t\t\t\t\t\t\t\t\t";
            foreach ($available_font_styles as $k => $v) {
                echo "<option value=\"";
                echo $k;
                echo "\" ";
                if($block["text"]["style"] == $k) {
                    echo "selected=\"selected\"";
                }
                echo ">";
                echo $v;
                echo "</option>";
            }
            echo "\t\t\t\t\t\t\t\t</select></td>\n\t\t\t\t\t\t\t<td><select class=\"text1 size1\" name=\"block_text_align\">\n\t\t\t\t\t\t\t\t\t<option value=\"left\" ";
            if($block["text"]["align"] == "left") {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo __("templateblock edit - table alignment left");
            echo "</option>\n\t\t\t\t\t\t\t\t\t<option value=\"right\" ";
            if($block["text"]["align"] == "right") {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo __("templateblock edit - table alignment right");
            echo "</option>\n\t\t\t\t\t\t\t\t\t<option value=\"center\" ";
            if($block["text"]["align"] == "center") {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo __("templateblock edit - table alignment center");
            echo "</option>\n\n\t\t\t\t\t\t\t\t</select></td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td colspan=\"2\">&nbsp;</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td><strong>";
            echo __("templateblock edit - text color");
            echo "</strong></td>\n\t\t\t\t\t\t\t<td><strong>";
            echo __("templateblock edit - text lineheight");
            echo "</strong></td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td><input type=\"text\" name=\"block_text_color\" class=\"text1 size6 colorpicker\" value=\"";
            echo $block["text"]["color"];
            echo "\" /></td>\n\t\t\t\t\t\t\t<td><select class=\"text1 size1\" name=\"block_text_lineheight\">\n\t\t\t\t\t\t\t\t\t<option value=\"1\" ";
            if($block["text"]["lineheight"] == "1") {
                echo "selected=\"selected\"";
            }
            echo ">1</option>\n\t\t\t\t\t\t\t\t\t<option value=\"1.1\" ";
            if($block["text"]["lineheight"] == "1.1") {
                echo "selected=\"selected\"";
            }
            echo ">1.1</option>\n\t\t\t\t\t\t\t\t\t<option value=\"1.25\" ";
            if($block["text"]["lineheight"] == "1.25") {
                echo "selected=\"selected\"";
            }
            echo ">1.25</option>\n\t\t\t\t\t\t\t\t\t<option value=\"1.5\" ";
            if($block["text"]["lineheight"] == "1.5") {
                echo "selected=\"selected\"";
            }
            echo ">1.5</option>\n\t\t\t\t\t\t\t\t\t<option value=\"1.75\" ";
            if($block["text"]["lineheight"] == "1.75") {
                echo "selected=\"selected\"";
            }
            echo ">1.75</option>\n\t\t\t\t\t\t\t\t\t<option value=\"2\" ";
            if($block["text"]["lineheight"] == "2") {
                echo "selected=\"selected\"";
            }
            echo ">2</option>\n\t\t\t\t\t\t\t\t</select></td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t</table>\n\t\t\t\t</div>\n\t\t\t</div>\n\t\t";
        }
        echo "\t\t";
        if(!in_array($block["type"], [templateblock::BLOCK_TYPE_LINES])) {
            echo "\t\t\t<div id=\"tabs-borders\" class=\"content box3\">\n\t\t\t\t<h3>";
            echo __("templateblock edit - borders h3");
            echo "</h3>\n\n\t\t\t\t<div class=\"content\">\n\t\t\t\t\t<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\">\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td colspan=\"2\"><strong>";
            echo __("templateblock edit - borders h3");
            echo "</strong><br />\n\t\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t\t<label><input type=\"checkbox\" name=\"block_borders_top\" value=\"yes\" ";
            if($block["borders"]["top"] == "yes") {
                echo "checked=\"checked\"";
            }
            echo "/> ";
            echo __("templateblock edit - border top");
            echo "</label>\n\t\t\t\t\t\t\t\t<label><input type=\"checkbox\" name=\"block_borders_right\" value=\"yes\" ";
            if($block["borders"]["right"] == "yes") {
                echo "checked=\"checked\"";
            }
            echo "/> ";
            echo __("templateblock edit - border right");
            echo "</label>\n\t\t\t\t\t\t\t\t<label><input type=\"checkbox\" name=\"block_borders_bottom\" value=\"yes\" ";
            if($block["borders"]["bottom"] == "yes") {
                echo "checked=\"checked\"";
            }
            echo "/> ";
            echo __("templateblock edit - border bottom");
            echo "</label>\n\t\t\t\t\t\t\t\t<label><input type=\"checkbox\" name=\"block_borders_left\" value=\"yes\" ";
            if($block["borders"]["left"] == "yes") {
                echo "checked=\"checked\"";
            }
            echo "/> ";
            echo __("templateblock edit - border left");
            echo "</label>\n\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t</tr>\n\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td colspan=\"2\">&nbsp;</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td><strong>";
            echo __("templateblock edit - border style");
            echo "</strong></td>\n\t\t\t\t\t\t\t<td><strong>";
            echo __("templateblock edit - border color");
            echo "</strong></td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td><select class=\"text1 size1\" name=\"block_borders_type\">\n\t\t\t\t\t\t\t\t\t<option value=\"solid\" ";
            if($block["borders"]["type"] == "solid") {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo __("templateblock edit - border style solid");
            echo "</option>\n\t\t\t\t\t\t\t\t\t<option value=\"dotted\" ";
            if($block["borders"]["type"] == "dotted") {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo __("templateblock edit - border style dotted");
            echo "</option>\n\t\t\t\t\t\t\t\t</select> <input type=\"text\" name=\"block_borders_thickness\" class=\"text1 size3\" value=\"";
            echo $block["borders"]["thickness"];
            echo "\" /> px</td>\n\t\t\t\t\t\t\t<td><input type=\"text\" name=\"block_borders_color\" class=\"text1 size6 colorpicker\"  value=\"";
            echo $block["borders"]["color"];
            echo "\" /></td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t</table>\n\t\t\t\t</div>\n\t\t\t</div>\n\t\t";
        }
        echo "\t\t";
        if(!in_array($block["type"], [templateblock::BLOCK_TYPE_LINES, templateblock::BLOCK_TYPE_TOTALS, templateblock::BLOCK_TYPE_TABLE])) {
            echo "\t\t\t<div id=\"tabs-style\" class=\"content box3\">\n\t\t\t\t<h3>";
            echo __("templateblock edit - style h3");
            echo "</h3>\n\n\t\t\t\t<div class=\"content\">\n\t\t\t\t\t<strong class=\"title\">";
            echo __("templateblock edit - style bgcolor");
            echo "</strong>\n\t\t\t\t\t<input type=\"text\" class=\"text1 size6 colorpicker\" name=\"block_style_bgcolor\" value=\"";
            echo $block["style"]["bgcolor"];
            echo "\" />\n\t\t\t\t\t<br /><br />\n\n\n\t\t\t\t</div>\n\t\t\t</div>\n\t\t";
        }
        echo "\t\t";
        if(in_array($block["type"], [templateblock::BLOCK_TYPE_LINES])) {
            echo "\t\t\t<div id=\"tabs-table-head\" class=\"content box3\">\n\n\t\t\t\t<div class=\"split2\">\n\t\t\t\t\t<div class=\"left\">\n\n\t\t\t\t\t\t<div class=\"box3\">\n\t\t\t\t\t\t\t<h3>";
            echo __("templateblock edit - text h3");
            echo "</h3>\n\t\t\t\t\t\t\t<div class=\"content\">\n\n\t\t\t\t\t\t\t\t<strong class=\"title\">";
            echo __("templateblock edit - text family");
            echo "</strong>\n\t\t\t\t\t\t\t\t<select class=\"text1 size1\" name=\"block_rows[0][text][family]\">\n\t\t\t\t\t\t\t\t\t<option value=\"\" ";
            if($block["rows"][0]["text"]["family"] == "") {
                echo "selected=\"selected\"";
            }
            echo ">-standaard van document-</option>\n\t\t\t\t\t\t\t\t\t";
            $fonts_displayed = [];
            foreach ($available_font_families as $optgroup => $tmp_fonts) {
                if(!empty($tmp_fonts)) {
                    echo "<optgroup label=\"";
                    echo __("templateblock edit - text family group " . $optgroup);
                    echo "\">";
                    foreach ($tmp_fonts as $k => $v) {
                        if($block["rows"][0]["text"]["family"] != $k && in_array(preg_replace("/li.ttf|b.ttf|i.ttf|l.ttf|z.ttf/", ".ttf", $v["title"]), $fonts_displayed)) {
                        } else {
                            $fonts_displayed[] = $v["title"];
                            echo "<option value=\"";
                            echo $k;
                            echo "\" ";
                            if($block["rows"][0]["text"]["family"] == $k) {
                                echo "selected=\"selected\"";
                            }
                            echo ">";
                            echo $v["filename"];
                            echo "</option>";
                        }
                    }
                    echo "</optgroup>";
                }
            }
            echo "\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t<br /><br />\n\n\t\t\t\t\t\t\t\t<strong class=\"title\">";
            echo __("templateblock edit - text size");
            echo "</strong>\n\t\t\t\t\t\t\t\t<select class=\"text1 size6\" name=\"block_rows[0][text][size]\">\n\t\t\t\t\t\t\t\t\t";
            foreach ($available_font_sizes as $k => $v) {
                echo "<option value=\"";
                echo $k;
                echo "\" ";
                if($block["rows"][0]["text"]["size"] == $k) {
                    echo "selected=\"selected\"";
                }
                echo ">";
                echo $v;
                echo "</option>";
            }
            echo "\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t<br /><br />\n\n\t\t\t\t\t\t\t\t<strong class=\"title\">";
            echo __("templateblock edit - text style");
            echo "</strong>\n\t\t\t\t\t\t\t\t<select class=\"text1 size1\" name=\"block_rows[0][text][style]\">\n\t\t\t\t\t\t\t\t\t";
            foreach ($available_font_styles as $k => $v) {
                echo "<option value=\"";
                echo $k;
                echo "\" ";
                if($block["rows"][0]["text"]["style"] == $k) {
                    echo "selected=\"selected\"";
                }
                echo ">";
                echo $v;
                echo "</option>";
            }
            echo "\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t<br /><br />\n\n\t\t\t\t\t\t\t\t<strong class=\"title\">";
            echo __("templateblock edit - text color");
            echo "</strong>\n\t\t\t\t\t\t\t\t<input type=\"text\" name=\"block_rows[0][text][color]\" class=\"text1 size6 colorpicker\" value=\"";
            echo $block["rows"][0]["text"]["color"];
            echo "\" />\n\t\t\t\t\t\t\t\t<br /><br />\n\n\t\t\t\t\t\t\t\t<strong class=\"title\">";
            echo __("templateblock edit - text lineheight");
            echo "</strong>\n\t\t\t\t\t\t\t\t<select class=\"text1 size1\" name=\"block_rows[0][text][lineheight]\">\n\t\t\t\t\t\t\t\t\t<option value=\"1\" ";
            if($block["rows"][0]["text"]["lineheight"] == "1") {
                echo "selected=\"selected\"";
            }
            echo ">1</option>\n\t\t\t\t\t\t\t\t\t<option value=\"1.25\" ";
            if($block["rows"][0]["text"]["lineheight"] == "1.25") {
                echo "selected=\"selected\"";
            }
            echo ">1.25</option>\n\t\t\t\t\t\t\t\t\t<option value=\"1.5\" ";
            if($block["rows"][0]["text"]["lineheight"] == "1.5") {
                echo "selected=\"selected\"";
            }
            echo ">1.5</option>\n\t\t\t\t\t\t\t\t\t<option value=\"1.75\" ";
            if($block["rows"][0]["text"]["lineheight"] == "1.75") {
                echo "selected=\"selected\"";
            }
            echo ">1.75</option>\n\t\t\t\t\t\t\t\t\t<option value=\"2\" ";
            if($block["rows"][0]["text"]["lineheight"] == "2") {
                echo "selected=\"selected\"";
            }
            echo ">2</option>\n\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\t\t\t\t\t<div class=\"right\">\n\n\t\t\t\t\t\t<div class=\"box3\">\n\t\t\t\t\t\t\t<h3>";
            echo __("templateblock edit - borders h3");
            echo "</h3>\n\t\t\t\t\t\t\t<div class=\"content\">\n\n\t\t\t\t\t\t\t\t<label><input type=\"checkbox\" name=\"block_rows[0][borders][top]\" value=\"yes\" ";
            if($block["rows"][0]["borders"]["top"] == "yes") {
                echo "checked=\"checked\"";
            }
            echo "/> ";
            echo __("templateblock edit - border top");
            echo "</label>\n\t\t\t\t\t\t\t\t<label><input type=\"checkbox\" name=\"block_rows[0][borders][right]\" value=\"yes\" ";
            if($block["rows"][0]["borders"]["right"] == "yes") {
                echo "checked=\"checked\"";
            }
            echo "/> ";
            echo __("templateblock edit - border right");
            echo "</label>\n\t\t\t\t\t\t\t\t<label><input type=\"checkbox\" name=\"block_rows[0][borders][bottom]\" value=\"yes\" ";
            if($block["rows"][0]["borders"]["bottom"] == "yes") {
                echo "checked=\"checked\"";
            }
            echo "/> ";
            echo __("templateblock edit - border bottom");
            echo "</label>\n\t\t\t\t\t\t\t\t<label><input type=\"checkbox\" name=\"block_rows[0][borders][left]\" value=\"yes\" ";
            if($block["rows"][0]["borders"]["left"] == "yes") {
                echo "checked=\"checked\"";
            }
            echo "/> ";
            echo __("templateblock edit - border left");
            echo "</label>\n\t\t\t\t\t\t\t\t<br /><br />\n\n\t\t\t\t\t\t\t\t<strong class=\"title\">";
            echo __("templateblock edit - border style");
            echo "</strong>\n\t\t\t\t\t\t\t\t<select class=\"text1 size1\" name=\"block_rows[0][borders][type]\">\n\t\t\t\t\t\t\t\t\t<option value=\"solid\" ";
            if($block["rows"][0]["borders"]["type"] == "solid") {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo __("templateblock edit - border style solid");
            echo "</option>\n\t\t\t\t\t\t\t\t\t<option value=\"dotted\" ";
            if($block["rows"][0]["borders"]["type"] == "dotted") {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo __("templateblock edit - border style dotted");
            echo "</option>\n\t\t\t\t\t\t\t\t</select> <input type=\"text\" name=\"block_rows[0][borders][thickness]\" class=\"text1 size3\" value=\"";
            echo $block["rows"][0]["borders"]["thickness"];
            echo "\" /> px\n\t\t\t\t\t\t\t\t<br /><br />\n\n\t\t\t\t\t\t\t\t<strong class=\"title\">";
            echo __("templateblock edit - border color");
            echo "</strong>\n\t\t\t\t\t\t\t\t<input type=\"text\" name=\"block_rows[0][borders][color]\" class=\"text1 size6 colorpicker\"  value=\"";
            echo $block["rows"][0]["borders"]["color"];
            echo "\" />\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t</div>\n\n\t\t\t\t\t\t<br />\n\n\t\t\t\t\t\t<div class=\"box3\">\n\t\t\t\t\t\t\t<h3>";
            echo __("templateblock edit - style h3");
            echo "</h3>\n\t\t\t\t\t\t\t<div class=\"content\">\n\t\t\t\t\t\t\t\t<strong class=\"title\">";
            echo __("templateblock edit - style bgcolor");
            echo "</strong>\n\t\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size6 colorpicker\" name=\"block_rows[0][style][bgcolor]\" value=\"";
            echo $block["rows"][0]["style"]["bgcolor"];
            echo "\" />\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\t\t\t\t</div>\n\n\t\t\t</div>\n\t\t\t<div id=\"tabs-table-rows\" class=\"content box3\">\n\n\t\t\t\t<div class=\"split2\">\n\t\t\t\t\t<div class=\"left\">\n\n\t\t\t\t\t\t<div class=\"box3\">\n\t\t\t\t\t\t\t<h3>";
            echo __("templateblock edit - text h3");
            echo "</h3>\n\t\t\t\t\t\t\t<div class=\"content\">\n\n\t\t\t\t\t\t\t\t<strong class=\"title\">";
            echo __("templateblock edit - text family");
            echo "</strong>\n\t\t\t\t\t\t\t\t<select class=\"text1 size1\" name=\"block_rows[1][text][family]\">\n\t\t\t\t\t\t\t\t\t<option value=\"\" ";
            if($block["rows"][1]["text"]["family"] == "") {
                echo "selected=\"selected\"";
            }
            echo ">-standaard van document-</option>\n\t\t\t\t\t\t\t\t\t";
            $fonts_displayed = [];
            foreach ($available_font_families as $optgroup => $tmp_fonts) {
                if(!empty($tmp_fonts)) {
                    echo "<optgroup label=\"";
                    echo __("templateblock edit - text family group " . $optgroup);
                    echo "\">";
                    foreach ($tmp_fonts as $k => $v) {
                        if($block["rows"][1]["text"]["family"] != $k && in_array(preg_replace("/li.ttf|b.ttf|i.ttf|l.ttf|z.ttf/", ".ttf", $v["title"]), $fonts_displayed)) {
                        } else {
                            $fonts_displayed[] = $v["title"];
                            echo "<option value=\"";
                            echo $k;
                            echo "\" ";
                            if($block["rows"][1]["text"]["family"] == $k) {
                                echo "selected=\"selected\"";
                            }
                            echo ">";
                            echo $v["filename"];
                            echo "</option>";
                        }
                    }
                    echo "</optgroup>";
                }
            }
            echo "\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t<br /><br />\n\n\t\t\t\t\t\t\t\t<strong class=\"title\">";
            echo __("templateblock edit - text size");
            echo "</strong>\n\t\t\t\t\t\t\t\t<select class=\"text1 size6\" name=\"block_rows[1][text][size]\">\n\t\t\t\t\t\t\t\t\t";
            foreach ($available_font_sizes as $k => $v) {
                echo "<option value=\"";
                echo $k;
                echo "\" ";
                if($block["rows"][1]["text"]["size"] == $k) {
                    echo "selected=\"selected\"";
                }
                echo ">";
                echo $v;
                echo "</option>";
            }
            echo "\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t<br /><br />\n\n\t\t\t\t\t\t\t\t<strong class=\"title\">";
            echo __("templateblock edit - text style");
            echo "</strong>\n\t\t\t\t\t\t\t\t<select class=\"text1 size1\" name=\"block_rows[1][text][style]\">\n\t\t\t\t\t\t\t\t\t";
            foreach ($available_font_styles as $k => $v) {
                echo "<option value=\"";
                echo $k;
                echo "\" ";
                if($block["rows"][1]["text"]["style"] == $k) {
                    echo "selected=\"selected\"";
                }
                echo ">";
                echo $v;
                echo "</option>";
            }
            echo "\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t<br /><br />\n\n\t\t\t\t\t\t\t\t<strong class=\"title\">";
            echo __("templateblock edit - text color");
            echo "</strong>\n\t\t\t\t\t\t\t\t<input type=\"text\" name=\"block_rows[1][text][color]\" class=\"text1 size6 colorpicker\" value=\"";
            echo $block["rows"][1]["text"]["color"];
            echo "\" />\n\t\t\t\t\t\t\t\t<br /><br />\n\n\t\t\t\t\t\t\t\t<strong class=\"title\">";
            echo __("templateblock edit - text lineheight");
            echo "</strong>\n\t\t\t\t\t\t\t\t<select class=\"text1 size1\" name=\"block_rows[1][text][lineheight]\">\n\t\t\t\t\t\t\t\t\t<option value=\"1\" ";
            if($block["rows"][1]["text"]["lineheight"] == "1") {
                echo "selected=\"selected\"";
            }
            echo ">1</option>\n\t\t\t\t\t\t\t\t\t<option value=\"1.25\" ";
            if($block["rows"][1]["text"]["lineheight"] == "1.25") {
                echo "selected=\"selected\"";
            }
            echo ">1.25</option>\n\t\t\t\t\t\t\t\t\t<option value=\"1.5\" ";
            if($block["rows"][1]["text"]["lineheight"] == "1.5") {
                echo "selected=\"selected\"";
            }
            echo ">1.5</option>\n\t\t\t\t\t\t\t\t\t<option value=\"1.75\" ";
            if($block["rows"][1]["text"]["lineheight"] == "1.75") {
                echo "selected=\"selected\"";
            }
            echo ">1.75</option>\n\t\t\t\t\t\t\t\t\t<option value=\"2\" ";
            if($block["rows"][1]["text"]["lineheight"] == "2") {
                echo "selected=\"selected\"";
            }
            echo ">2</option>\n\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\t\t\t\t\t<div class=\"right\">\n\n\t\t\t\t\t\t<div class=\"box3\">\n\t\t\t\t\t\t\t<h3>";
            echo __("templateblock edit - borders h3");
            echo "</h3>\n\t\t\t\t\t\t\t<div class=\"content\">\n\n\t\t\t\t\t\t\t\t<label><input type=\"checkbox\" name=\"block_rows[1][borders][top]\" value=\"yes\" ";
            if(isset($block["rows"][1]["borders"]["top"]) && $block["rows"][1]["borders"]["top"] == "yes") {
                echo "checked=\"checked\"";
            }
            echo "/> ";
            echo __("templateblock edit - border top");
            echo "</label>\n\t\t\t\t\t\t\t\t<label><input type=\"checkbox\" name=\"block_rows[1][borders][right]\" value=\"yes\" ";
            if(isset($block["rows"][1]["borders"]["right"]) && $block["rows"][1]["borders"]["right"] == "yes") {
                echo "checked=\"checked\"";
            }
            echo "/> ";
            echo __("templateblock edit - border right");
            echo "</label>\n\t\t\t\t\t\t\t\t<label><input type=\"checkbox\" name=\"block_rows[1][borders][bottom]\" value=\"yes\" ";
            if(isset($block["rows"][1]["borders"]["bottom"]) && $block["rows"][1]["borders"]["bottom"] == "yes") {
                echo "checked=\"checked\"";
            }
            echo "/> ";
            echo __("templateblock edit - border bottom");
            echo "</label>\n\t\t\t\t\t\t\t\t<label><input type=\"checkbox\" name=\"block_rows[1][borders][left]\" value=\"yes\" ";
            if(isset($block["rows"][1]["borders"]["left"]) && $block["rows"][1]["borders"]["left"] == "yes") {
                echo "checked=\"checked\"";
            }
            echo "/> ";
            echo __("templateblock edit - border left");
            echo "</label>\n\t\t\t\t\t\t\t\t<br /><br />\n\n\t\t\t\t\t\t\t\t<strong class=\"title\">";
            echo __("templateblock edit - border style");
            echo "</strong>\n\t\t\t\t\t\t\t\t<select class=\"text1 size1\" name=\"block_rows[1][borders][type]\">\n\t\t\t\t\t\t\t\t\t<option value=\"solid\" ";
            if($block["rows"][1]["borders"]["type"] == "solid") {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo __("templateblock edit - border style solid");
            echo "</option>\n\t\t\t\t\t\t\t\t\t<option value=\"dotted\" ";
            if($block["rows"][1]["borders"]["type"] == "dotted") {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo __("templateblock edit - border style dotted");
            echo "</option>\n\t\t\t\t\t\t\t\t</select> <input type=\"text\" name=\"block_rows[1][borders][thickness]\" class=\"text1 size3\" value=\"";
            echo $block["rows"][1]["borders"]["thickness"];
            echo "\" /> px\n\t\t\t\t\t\t\t\t<br /><br />\n\n\t\t\t\t\t\t\t\t<strong class=\"title\">";
            echo __("templateblock edit - border color");
            echo "</strong>\n\t\t\t\t\t\t\t\t<input type=\"text\" name=\"block_rows[1][borders][color]\" class=\"text1 size6 colorpicker\"  value=\"";
            echo $block["rows"][1]["borders"]["color"];
            echo "\" />\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t</div>\n\n\t\t\t\t\t\t<br />\n\n\t\t\t\t\t\t<div class=\"box3\">\n\t\t\t\t\t\t\t<h3>";
            echo __("templateblock edit - style h3");
            echo "</h3>\n\t\t\t\t\t\t\t<div class=\"content\">\n\t\t\t\t\t\t\t\t<strong class=\"title\">";
            echo __("templateblock edit - style bgcolor");
            echo "</strong>\n\t\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size6 colorpicker\" name=\"block_rows[1][style][bgcolor]\" value=\"";
            echo $block["rows"][1]["style"]["bgcolor"];
            echo "\" /><br /><br />\n\t\t\t\t\t\t\t\t<label><input type=\"checkbox\" name=\"zebra_effect\" value=\"yes\" ";
            if($block["rows"][1]["style"]["bgcolor_even"] != "") {
                echo "checked=\"checked\"";
            }
            echo "/> ";
            echo __("templateblock edit - want a zebra effect");
            echo "</label>\n\n\t\t\t\t\t\t\t\t<div id=\"zebra_effect_color\" class=\"";
            if($block["rows"][1]["style"]["bgcolor_even"] == "") {
                echo "hide";
            }
            echo "\">\n\t\t\t\t\t\t\t\t\t<br />\n\n\t\t\t\t\t\t\t\t\t<strong class=\"title\">";
            echo __("templateblock edit - zebra effect bgcolor");
            echo "</strong>\n\t\t\t\t\t\t\t\t\t<input type=\"text\" class=\"text1 size6 colorpicker\" name=\"block_rows[1][style][bgcolor_even]\" value=\"";
            echo $block["rows"][1]["style"]["bgcolor_even"];
            echo "\" />\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\t\t\t\t</div>\n\n\t\t\t</div>\n\t\t\t";
            if($template->Type == "invoice") {
                echo "\t\t\t\t<div id=\"tabs-invoicelines-description\" class=\"content box3\">\n\t\t\t\t\t<div class=\"box3\">\n\t\t\t\t\t\t<h3>";
                echo __("templateblock edit - invoicelines description h3");
                echo "</h3>\n\t\t\t\t\t\t<div class=\"content\">\n\n\t\t\t\t\t\t\t";
                echo __("templateblock edit - invoicelines description explained");
                echo "<br />\n\t\t\t\t\t\t\t<br />\n\t\t\t\t\t\t\t<div style=\"max-height: 300px; overflow-y: auto;padding-top:3px;\">\n\t\t\t\t\t\t\t\t";
                $_types = array_merge(["domain" => $array_producttypes["domain"], "hosting" => $array_producttypes["hosting"]], $additional_product_types);
                foreach ($_types as $_type => $_title) {
                    echo "\t\t\t\t\t\t\t\t\t<strong class=\"title2\" style=\"line-height:26px;\">";
                    echo $_title;
                    echo "</strong>\n\t\t\t\t\t\t\t\t\t<textarea name=\"block_rows[1][additional_description][";
                    echo $_type;
                    echo "]\" class=\"text1 autogrow\" style=\"width: 320px; margin-top: 0px;\">";
                    if(isset($block["rows"][1])) {
                        echo "\n" . htmlspecialchars($block["rows"][1]["additional_description"][$_type]);
                    }
                    echo "</textarea>\n\t\t\t\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t\t\t\t\t";
                }
                echo "\t\t\t\t\t\t\t</div>\n\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\t\t\t\t</div>\n\t\t\t";
            }
            echo "\n\n\t\t";
        }
        echo "\t\t<div id=\"tabs-visibility\" class=\"content box3\">\n\t\t\t<h3>";
        echo __("templateblock edit - visibility h3");
        echo "</h3>\n\n\t\t\t<div class=\"content\">\n\t\t\t\t<strong class=\"title\">";
        echo __("templateblock edit - visible on");
        echo "</strong>\n\t\t\t\t<select name=\"block_visibility\" class=\"text1 size1\">\n\t\t\t\t\t<option value=\"all\" ";
        if($block["visibility"] == "all") {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo __("templateblock edit - visible all pages");
        echo "</option>\n\t\t\t\t\t";
        if(!in_array($block["type"], [templateblock::BLOCK_TYPE_LINES, templateblock::BLOCK_TYPE_TOTALS])) {
            echo "\t\t\t\t\t\t<option value=\"first\" ";
            if($block["visibility"] == "first") {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo __("templateblock edit - visible only first page");
            echo "</option>\n\t\t\t\t\t";
        }
        echo "\t\t\t\t\t";
        if(!in_array($block["type"], [templateblock::BLOCK_TYPE_LINES])) {
            echo "\t\t\t\t\t\t<option value=\"last\" ";
            if($block["visibility"] == "last") {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo __("templateblock edit - visible only last page");
            echo "</option>\n\t\t\t\t\t";
        }
        echo "\t\t\t\t\t<option value=\"none\" ";
        if($block["visibility"] == "none") {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo __("templateblock edit - not visible");
        echo "</option>\n\t\t\t\t</select>\n\t\t\t\t<br /><br />\n\t\t\t</div>\n\t\t</div>\n\t</div>\n</form>\n\n<p class=\"floatl\">\n\t";
        if(isset($_POST["force"]) && $_POST["force"] == "yes") {
            echo "\t\t<a href=\"?page=show";
            echo $template->Type;
            echo "&amp;id=";
            echo $template->Identifier;
            echo "&amp;action=delete_block&amp;block_id=";
            echo $block_id;
            echo "\" class=\"a1 c1\">";
            echo __("templateblock settings - close without saving");
            echo "</a>\n\t";
        } else {
            echo "\t\t<a class=\"a1 c1 close_without_saving\" style=\"line-height:30px;\">";
            echo __("templateblock settings - close without saving");
            echo "</a>\n\t";
        }
        echo "</p>\n<p class=\"floatr\">\n\t<a id=\"save_block\" class=\"button1 alt1 pointer\">\n\t\t<span>";
        echo __("btn edit");
        echo "</span>\n\t</a>\n</p>\n\n<br clear=\"both\"/>\n\n<style type=\"text/css\">\n\t.block_tabs .box3 { margin-bottom: 0px !important; }\n\t.block_tabs > div.box3 { display:none; }\n\n\t#templateblock_table { position:relative; }\n\t#templateblock_table th:first-child { padding:0px;}\n\t#templateblock_table td:first-child { padding: 7px 2px 5px 8px;  }\n\n\t#templateblock_table th { white-space: normal;}\n\t#templateblock_table th span.del_col {margin:0px 10px 0px 0px; cursor:pointer; float:right;width:16px; height:16px; background:url(images/ico_trash.png) no-repeat center center; visibility:hidden; }\n\t#templateblock_table td span.del_row {position:absolute;right:2px;margin:3px 0 0 2px; cursor:pointer; width:16px; height:16px; background:url(images/ico_trash.png) no-repeat center center; }\n\t#templateblock_table td input {margin-right:10px;}\n\n\t.column_options_line { display:block;border-bottom:1px solid #CCC;padding:5px 10px 5px 30px; }\n\n\t#templateblock_cols { width:auto;border:0px;margin:0 0 0 20px; }\n\n\t.block_value_invoicelines span.del_row { right:2px !important; }\n\t.block_value_totals td { line-height:24px; }\n\tlabel.infopopuptop { margin: 0px; }\n\tlabel.infopopuptop span.popup{ min-width:0px;white-space: nowrap}\n\n\t.block_value_totals .col_borders, .block_value_totals .col_bgcolor { display:none; }\n\n\t.block_value_totals .add_default_row_div a { margin-right:7px; }\n\t.block_value_totals .add_default_row_div a:after{ content:\", \"; text-decoration:none; color: #414042 }\n\t.block_value_totals .add_default_row_div a:last-child:after{ content:\"\"; }\n\n\t.zebra_table tr, .zebra_table td { background-color: white; }\n\t.colorpicker { display:inline-block !important}\n</style>\n<script type=\"text/javascript\">\n\t\$(function(){\n\t\t\$( \".block_tabs\" ).tabs();\n\n\t\t\$(\".zebra_table tbody tr\").removeClass(\"tr1\");\n\t\t\$(\".zebra_table tbody tr:odd\").addClass(\"tr1\");\n\n\t\t\$('#save_block').click(function(){\n\t\t\t\$('form[name=\"TemplateBlockEdit\"]').submit();\n\t\t});\n\n\t\t\$('.close_without_saving').click(function(){\n\t\t\t\$('.colorpicker').spectrum('destroy');\n\t\t\t\$('#template_block_edit').html('').hide();\n\t\t\t\$('#template_canvas_overlay').hide();\n\t\t});\n\n\t\t/**\n\t\t * Image previewer\n\t\t */\n\t\t\$('.block_value_img input[name=\"block_value\"]').change(function()\n\t\t{\n\t\t\t\$('#image_helper_loader').html('');\n\t\t\t\$('#image_helper_div').hide();\n\n\t\t\tvar ImageURL = \$(this).val();\n\t\t\tif(ImageURL.indexOf('http') == -1)\n\t\t\t{\n\t\t\t\t// Relative path?\n\t\t\t\t\$('#image_helper_div').show();\n\t\t\t\t\$('#image_helper_img').attr('src',ImageURL);\n\t\t\t}\n\t\t\telse if(ImageURL)\n\t\t\t{\n\t\t\t\t\$('#image_helper_loader').html('<img src=\"images/icon_circle_loader_grey.gif\" style=\"margin:6px 0 6px 6px;\" />');\n\n\t\t\t\t// Check URL\n\t\t\t\t\$.post(\"XMLRequest.php\", { action: 'check_url', url: ImageURL },function(data){\n\t\t\t\t\tif(data.content_type != null){\n\t\t\t\t\t\tif(data.content_type.indexOf('image') !== -1){\n\t\t\t\t\t\t\t\$('#image_helper_loader').html('<img src=\"images/ico_check.png\" style=\"margin:6px 0 6px 6px;\" />');\n\t\t\t\t\t\t\t\$('#image_helper_div').show();\n\t\t\t\t\t\t\t\$('#image_helper_img').attr('src',ImageURL);\n\t\t\t\t\t\t}else{\n\t\t\t\t\t\t\t\$('#image_helper_div').hide();\n\t\t\t\t\t\t\t\$('#image_helper_loader').html('<span class=\"loading_red\">' + __LANG_URL_IS_NOT_AN_IMAGE + '</span>');\n\t\t\t\t\t\t}\n\t\t\t\t\t}else{\n\t\t\t\t\t\t\$('#image_helper_loader').html('<span class=\"loading_red\">' + __LANG_URL_IS_NOT_AN_IMAGE + '</span>');\n\t\t\t\t\t}\n\t\t\t\t},'json');\n\t\t\t}\n\t\t});\n\n\t\t/**\n\t\t * Table properties\n\t\t */\n\t\tif(\$('.block_value_table').html())\n\t\t{\n\t\t\t\$(\"#add_column\").click(function(){\n\t\t\t\tvar ColCount = \$('#templateblock_table thead tr th').length - 1;\n\n\t\t\t\t\$('#templateblock_table thead tr').append('<th width=\"220\">'+__LANG_TEMPLATEBLOCK_COLUMN+' <span class=\"col_id\">' + (ColCount+1) + '</span><span class=\"del_col\">&nbsp;</span></td>');\n\n\t\t\t\t\$('#templateblock_table tbody tr').each(function(index, element){\n\t\t\t\t\t// Get right RowCount, may be sorted\n\t\t\t\t\tvar CurrentRow = \$(element).find('input[name^=\"block_value[\"][name\$=\"][0]\"]').attr('name').replace('block_value[','').replace('][0]','');\n\t\t\t\t\t\$(element).append('<td valign=\"top\" width=\"220\"><input type=\"text\" name=\"block_value[' + CurrentRow + '][' + ColCount + ']\" class=\"text1 size1\" value=\"\" />');\n\t\t\t\t});\n\n\t\t\t\t// Add col options\n\t\t\t\t\$('#templateblock_cols tr:first-child').append('<td valign=\"top\" width=\"220\"><font class=\"smallfont\" style=\"color: #999;\">'+__LANG_TEMPLATEBLOCK_ALIGNMENT+'</font><br /><select name=\"block_cols[' + ColCount + '][text][align]\" class=\"text1\" style=\"width:210px;margin-top:4px;\"><option value=\"left\">'+__LANG_TEMPLATEBLOCK_ALIGNMENT_LEFT+'</option><option value=\"right\">'+__LANG_TEMPLATEBLOCK_ALIGNMENT_RIGHT+'</option><option value=\"center\">'+__LANG_TEMPLATEBLOCK_ALIGNMENT_CENTER+'</option><option value=\"money\">'+__LANG_TEMPLATEBLOCK_ALIGNMENT_AMOUNT+'</option></select></td>');\n\n\t\t\t\t\$('#templateblock_cols tr:last-child').append('<td valign=\"top\" width=\"220\"><font class=\"smallfont col_options_width_label\" style=\"color: #999;\">'+__LANG_TEMPLATEBLOCK_COLUMN_WIDTH+'</font><br /><input type=\"text\" name=\"block_cols[' + ColCount + '][positioning][w]\" class=\"text1 size6 col_options_width\" value=\"\" style=\"margin-top:4px;\"/></td>');\n\n\t\t\t\t// Set line width\n\t\t\t\t\$('.column_options_line').css('width', (ColCount+1) * 230);\n\n\t\t\t\tcheckColWidthFields();\n\t\t\t});\n\n\t\t\t\$(\"#add_row\").click(function(){\n\t\t\t\tvar RowCount = \$('#templateblock_table tbody tr').length;\n\n\t\t\t\t// First check if this RowCount is indeed free to use. If undefined, it's fine, otherwise we mix up rows/columns\n\t\t\t\tvar RowCountChecker = 0;\n\t\t\t\twhile(\$('input[name^=\"block_value[' + RowCount + ']\"]').val() != undefined && RowCountChecker < 25)\n\t\t\t\t{\n\t\t\t\t\tRowCount++;\n\t\t\t\t\tRowCountChecker++;\n\t\t\t\t}\n\n\t\t\t\t\$('#templateblock_table tbody').append('<tr>');\n\n\t\t\t\t\$('#templateblock_table thead tr th').each(function(CurrentCol, element){\n\t\t\t\t\tif(CurrentCol == 0)\n\t\t\t\t\t{\n\t\t\t\t\t\t\$('#templateblock_table tbody tr:last-child').append('<td><img class=\"pointer sortablehandle sort_icon\" src=\"images/ico_sort.png\" /></td>');\n\t\t\t\t\t}\n\t\t\t\t\telse\n\t\t\t\t\t{\n\t\t\t\t\t\t\$('#templateblock_table tbody tr:last-child').append('<td valign=\"top\" width=\"220\"><input type=\"text\" name=\"block_value[' + RowCount + '][' + (CurrentCol-1) + ']\" class=\"text1 size1\" value=\"\" />');\n\t\t\t\t\t}\n\t\t\t\t});\n\n\t\t\t\t// Zebra-effect\n\t\t\t\t\$(\".zebra_table tbody tr\").removeClass(\"tr1\");\n\t\t\t\t\$(\".zebra_table tbody tr:odd\").addClass(\"tr1\");\n\t\t\t});\n\n\t\t\t// Hover effects\n            \$(document).on('mouseenter', '#templateblock_table th, #templateblock_table td', function() {\n                \$('#templateblock_table thead tr th:nth-child(' + (\$(this).parent('tr').find('td, th').index(\$(this)) + 1) + ')').find('.del_col').css('visibility', 'visible');\n            });\n            \$(document).on('mouseleave', '#templateblock_table th, #templateblock_table td', function() {\n                \$('#templateblock_table thead .del_col').css('visibility','hidden');\n\t\t\t});\n\n\t\t\t// Delete rows/cols\n            \$(document).on('click', '.del_col', function(){\n\t\t\t\tvar CurrentCol = \$(this).parents('tr').find('th').index(\$(this).parent('th'));\n\n\t\t\t\t\$('#templateblock_table thead tr th:nth-child('+(CurrentCol+1)+')').remove();\n\t\t\t\t\$('#templateblock_table tbody tr td:nth-child('+(CurrentCol+1)+')').remove();\n\n\t\t\t\t\$('#templateblock_cols tr td:nth-child('+(CurrentCol)+')').remove();\n\n\t\t\t\t// Set line width\n\t\t\t\t\$('.column_options_line').css('width', (\$('#templateblock_table thead tr th').length-1) * 230);\n\n\t\t\t\t// Recalculate column count\n\t\t\t\t\$('#templateblock_table thead tr th').each(function(CurrentCol, element){\n\t\t\t\t\tif(CurrentCol > 0)\n\t\t\t\t\t{\n\t\t\t\t\t\t\$(element).find('.col_id').html(CurrentCol);\n\t\t\t\t\t}\n\t\t\t\t});\n\n\t\t\t\t// Always make 1 column\n\t\t\t\tif(\$('#templateblock_table thead tr th').length <= 1)\n\t\t\t\t{\n\t\t\t\t\t\$(\"#add_column\").click();\n\t\t\t\t}\n\t\t\t\telse\n\t\t\t\t{\n\t\t\t\t\tcheckColWidthFields();\n\t\t\t\t}\n\t\t\t});\n\n            \$(document).on('click', '.del_row', function(){\n\t\t\t\t\$(this).parents('tr').remove();\n\n\t\t\t\t// Always make 1 row\n\t\t\t\tif(\$('#templateblock_table tbody tr').length < 1)\n\t\t\t\t{\n\t\t\t\t\t\$(\"#add_row\").click();\n\t\t\t\t}\n\n\t\t\t\t// Zebra-effect\n\t\t\t\t\$(\".zebra_table tbody tr\").removeClass(\"tr1\");\n\t\t\t\t\$(\".zebra_table tbody tr:odd\").addClass(\"tr1\");\n\t\t\t});\n\t\t}\n\n\n\t\t/**\n\t\t * For all table-types\n\t\t */\n\t\tif(\$('.block_value_table').html() || \$('.block_value_invoicelines').html() || \$('.block_value_totals').html())\n\t\t{\n\t\t\t// Sortable\n\t\t\t\$('#templateblock_table tbody').sortable({\n\t\t\t\thandle: \".sortablehandle\",\n\t\t\t\tstop: function(event, ui){\n\t\t\t\t\t// Zebra-effect\n\t\t\t\t\t\$(\".zebra_table tbody tr\").removeClass(\"tr1\");\n\t\t\t\t\t\$(\".zebra_table tbody tr:odd\").addClass(\"tr1\");\n\t\t\t\t}\n\t\t\t});\n\n            \$(document).on('mouseenter', '#templateblock_table tbody tr', function () {\n                \$(this).find('td:visible:last').append('<span class=\"del_row\">&nbsp;</span>');\n            });\n            \$(document).on('mouseleave', '#templateblock_table tbody tr', function () {\n                \$(this).find('.del_row').remove();\n            });\n\n\t\t\t// Keep track of widths of cols, one must be hidden\n\t\t\tcheckColWidthFields();\n            \$(document).on('change', 'input.col_options_width', function(){\n\t\t\t\tcheckColWidthFields();\n\t\t\t});\n\t\t}\n\n\t\t/**\n\t\t * Invoicelines\n\t\t */\n\t\tif(\$('.block_value_invoicelines').html())\n\t\t{\n\t\t\t\$(\"#add_column\").click(function(){\n\n\t\t\t\tvar ColCount = \$('#templateblock_table tbody tr').length;\n\n\t\t\t\t// First check if this ColCount is indeed free to use. If undefined, it's fine, otherwise we mix up rows/columns\n\t\t\t\tvar ColCountChecker = 0;\n\t\t\t\twhile(\$('input[name=\"block_value[0][' + ColCount + ']\"]').val() != undefined && ColCountChecker < 25)\n\t\t\t\t{\n\t\t\t\t\tColCount++;\n\t\t\t\t\tColCountChecker++;\n\t\t\t\t}\n\n\t\t\t\t\$('#templateblock_table tbody').append('<tr>');\n\n\t\t\t\t\$('#templateblock_table tbody tr:last-child').append('<td valign=\"top\"><img class=\"pointer sortablehandle sort_icon\" src=\"images/ico_sort.png\" style=\"margin-top:5px;\" /></td>');\n\t\t\t\t\$('#templateblock_table tbody tr:last-child').append('<td valign=\"top\"><input type=\"text\" name=\"block_value[0][' + ColCount + ']\" class=\"text1 size7\" value=\"\" /></td>');\n\t\t\t\t\$('#templateblock_table tbody tr:last-child').append('<td valign=\"top\"><textarea name=\"block_value[1][' + ColCount + ']\" class=\"text1 autogrow\" style=\"width:320px;margin-top:0px;\"></textarea></td>');\n\t\t\t\t\$('#templateblock_table tbody tr:last-child').append('<td valign=\"top\"><select name=\"block_cols[' + ColCount + '][text][align]\" class=\"text1\" style=\"width:140px;\"><option value=\"left\">'+__LANG_TEMPLATEBLOCK_ALIGNMENT_LEFT+'</option><option value=\"right\">'+__LANG_TEMPLATEBLOCK_ALIGNMENT_RIGHT+'</option><option value=\"center\">'+__LANG_TEMPLATEBLOCK_ALIGNMENT_CENTER+'</option><option value=\"money_left\">'+__LANG_TEMPLATEBLOCK_ALIGNMENT_AMOUNT_LEFT+'</option><option value=\"money_right\">'+__LANG_TEMPLATEBLOCK_ALIGNMENT_AMOUNT_RIGHT+'</option></select></td>');\n\t\t\t\t\$('#templateblock_table tbody tr:last-child').append('<td valign=\"top\"><input type=\"text\" name=\"block_cols[' + ColCount + '][positioning][w]\" class=\"text1 size6 col_options_width\"/></td>');\n\n\t\t\t\t// Zebra-effect\n\t\t\t\t\$(\".zebra_table tbody tr\").removeClass(\"tr1\");\n\t\t\t\t\$(\".zebra_table tbody tr:odd\").addClass(\"tr1\");\n\n\t\t\t\t\$('textarea[name=\"block_value[1][' + ColCount + ']\"]').autoGrow();\n\t\t\t\tcheckColWidthFields();\n\n\t\t\t});\n\n            \$(document).on('click', '.del_row', function(){\n\t\t\t\t\$(this).parents('tr').remove();\n\n\t\t\t\tcheckColWidthFields();\n\n\t\t\t\t// Always make 1 row/col\n\t\t\t\tif(\$('#templateblock_table tbody tr').length < 1)\n\t\t\t\t{\n\t\t\t\t\t\$(\"#add_column\").click();\n\t\t\t\t}\n\n\t\t\t\t// Zebra-effect\n\t\t\t\t\$(\".zebra_table tbody tr\").removeClass(\"tr1\");\n\t\t\t\t\$(\".zebra_table tbody tr:odd\").addClass(\"tr1\");\n\t\t\t});\n\n\t\t\t\$('input[name=\"zebra_effect\"]').click(function(){\n\t\t\t\tif(\$(this).prop('checked'))\n\t\t\t\t{\n\t\t\t\t\t\$('#zebra_effect_color').show();\n\t\t\t\t}\n\t\t\t\telse\n\t\t\t\t{\n\t\t\t\t\t\$('#zebra_effect_color').hide().find('input').val('');\n\t\t\t\t}\n\t\t\t});\n\n\t\t\t// Initiate autogrow\n\t\t\t\$('#templateblock_table textarea').autoGrow();\n\t\t\t\$('textarea[name^=\"block_rows[1][additional_description]\"]').autoGrow();\n\n\t\t}\n\n\t\t/**\n\t\t * Totals\n\t\t */\n\t\tif(\$('.block_value_totals').html())\n\t\t{\n\t\t\t\$(\"#add_row\").click(function(){\n\t\t\t\tvar RowCount = \$('#templateblock_table tbody tr').length;\n\n\t\t\t\t// First check if this RowCount is indeed free to use. If undefined, it's fine, otherwise we mix up rows/columns\n\t\t\t\tvar RowCountChecker = 0;\n\t\t\t\twhile(\$('input[name^=\"block_value[' + RowCount + ']\"]').val() != undefined && RowCountChecker < 25)\n\t\t\t\t{\n\t\t\t\t\tRowCount++;\n\t\t\t\t\tRowCountChecker++;\n\t\t\t\t}\n\n\t\t\t\t\$('#templateblock_table tbody').append('<tr>');\n\n\t\t\t\t\$('#templateblock_table tbody tr:last-child').append('<td><img class=\"pointer sortablehandle sort_icon\" src=\"images/ico_sort.png\" /></td>');\n\t\t\t\t\$('#templateblock_table tbody tr:last-child').append('<td width=\"220\"><input type=\"text\" name=\"block_value['+RowCount+'][0]\" class=\"text1 size1\" value=\"\" /></td>');\n\t\t\t\t\$('#templateblock_table tbody tr:last-child').append('<td width=\"220\"><input type=\"text\" name=\"block_value['+RowCount+'][1]\" class=\"text1 size1\" value=\"\" /></td>');\n\t\t\t\t\$('#templateblock_table tbody tr:last-child').append('<td class=\"col_borders\"><label class=\"infopopuptop delaypopup\"><input type=\"checkbox\" name=\"block_rows['+RowCount+'][borders][top]\" value=\"yes\" /><span class=\"popup\">'+__LANG_TEMPLATEBLOCK_BORDER_TOP+'<b></b></span></label><label class=\"infopopuptop delaypopup\"><input type=\"checkbox\" name=\"block_rows['+RowCount+'][borders][right]\" value=\"yes\" /><span class=\"popup\">'+__LANG_TEMPLATEBLOCK_BORDER_RIGHT+'<b></b></span></label><label class=\"infopopuptop delaypopup\"><input type=\"checkbox\" name=\"block_rows['+RowCount+'][borders][bottom]\" value=\"yes\" /><span class=\"popup\">'+__LANG_TEMPLATEBLOCK_BORDER_BOTTOM+'<b></b></span></label><label class=\"infopopuptop delaypopup\"><input type=\"checkbox\" name=\"block_rows['+RowCount+'][borders][left]\" value=\"yes\" /><span class=\"popup\">'+__LANG_TEMPLATEBLOCK_BORDER_LEFT+'<b></b></span></label></td>');\n\t\t\t\tif(\$('input[name=\"has_line_borders\"]').prop('checked')){\n\t\t\t\t\t\$('#templateblock_table tbody tr:last-child td:last-child').show();\n\t\t\t\t}\n\t\t\t\t\$('#templateblock_table tbody tr:last-child').append('<td class=\"col_bgcolor\"><input type=\"text\" name=\"block_rows['+RowCount+'][style][bgcolor]\" class=\"text1 size6 colorpicker\" value=\"\" /></td>');\n\t\t\t\tif(\$('input[name=\"has_line_bgcolor\"]').prop('checked')){\n\t\t\t\t\t\$('#templateblock_table tbody tr:last-child td:last-child').show();\n\t\t\t\t\t\$('#templateblock_table tbody tr:last-child td:last-child').find('.colorpicker').spectrum({preferredFormat: \"hex\",  color: \"#fff\",  clickoutFiresChange: true, showButtons: false});\n\t\t\t\t}\n\n\t\t\t\t// Zebra-effect\n\t\t\t\t\$(\".zebra_table tbody tr\").removeClass(\"tr1\");\n\t\t\t\t\$(\".zebra_table tbody tr:odd\").addClass(\"tr1\");\n\t\t\t});\n\n            \$(document).on('click', '.del_row', function(){\n\n\t\t\t\t\$('.add_default_row_div').find('#drd_'+\$(this).parents('tr').find('input[name\$=\"[style][totaltype]\"]').val()).removeClass('hide');\n\n\t\t\t\t\$(this).parents('tr').remove();\n\n\t\t\t\t// Always make 1 row\n\t\t\t\tif(\$('#templateblock_table tbody tr').length < 1)\n\t\t\t\t{\n\t\t\t\t\t\$(\"#add_row\").click();\n\t\t\t\t}\n\n\t\t\t\t// Zebra-effect\n\t\t\t\t\$(\".zebra_table tbody tr\").removeClass(\"tr1\");\n\t\t\t\t\$(\".zebra_table tbody tr:odd\").addClass(\"tr1\");\n\t\t\t});\n\n\t\t\t\$('.add_default_row_div a').click(function(){\n\t\t\t\t\$(this).hide();\n\n\t\t\t\tif(\$('.add_default_row_div a:visible').length == 0)\n\t\t\t\t{\n\t\t\t\t\t\$('span.add_default_row_div').hide();\n\t\t\t\t}\n\t\t\t});\n\n\t\t\t\$('input[name=\"has_line_borders\"]').click(function(){\n\t\t\t\tif(\$(this).prop('checked')){\n\t\t\t\t\t\$('.block_value_totals .col_borders').show();\n\t\t\t\t}else{\n\t\t\t\t\t\$('.block_value_totals .col_borders').hide();\n\t\t\t\t\t\$('.block_value_totals .col_borders').find('input').prop('checked',false);\n\t\t\t\t}\n\t\t\t});\n\n\t\t\t\$('input[name=\"has_line_bgcolor\"]').click(function(){\n\t\t\t\tif(\$(this).prop('checked')){\n\t\t\t\t\t\$('.block_value_totals .col_bgcolor').show();\n\t\t\t\t}else{\n\t\t\t\t\t\$('.block_value_totals .col_bgcolor').hide();\n\t\t\t\t\t\$('.block_value_totals .col_bgcolor').find('input').val('');\n\t\t\t\t}\n\t\t\t});\n\n\t\t\t// Initial checks\n\t\t\tif(\$('input[name=\"has_line_borders\"]').prop('checked'))\n\t\t\t{\n\t\t\t\t\$('.block_value_totals .col_borders').css('display', 'table-cell');\n\t\t\t}\n\t\t\tif(\$('input[name=\"has_line_bgcolor\"]').prop('checked'))\n\t\t\t{\n\t\t\t\t\$('.block_value_totals .col_bgcolor').css('display', 'table-cell');\n\t\t\t}\n\n\t\t}\n\n\t\t// All color pickers\n\t\t\$('.colorpicker').spectrum({preferredFormat: \"hex\",  color: \"#fff\",  clickoutFiresChange: true, showButtons: false});\n\t\t\$('.colorpicker').change(function(){\n\t\t\tif(\$(this).val())\n\t\t\t{\n\t\t\t\t\$(this).spectrum(\"set\", \$(this).val());\n\t\t\t}\n\t\t\telse\n\t\t\t{\n\t\t\t\t\$(this).next().find('.sp-preview-inner').css('background-color','#ffffff');\n\t\t\t}\n\t\t});\n        // Set init color\n        \$('.colorpicker').trigger('change');\n\n\t\t\$('#add_image_file').click(function(){\n\t\t\tfiletype = 'template_image_files';\n\t\t\t\$('#filemanager').dialog('open');\n\t\t});\n\n\t});\n\n\tfunction checkColWidthFields()\n\t{\n\t\tvar EmptyWidths = 0;\n\n\t\t\$('input.col_options_width').each(function(){\n\t\t\tif(\$(this).val() == '' || \$(this).val() == '0')\n\t\t\t{\n\t\t\t\t\$(this).val('').hide();\n\t\t\t\t\$(this).parent().find('.col_options_width_label').hide();\n\t\t\t\tEmptyWidths++;\n\t\t\t}\n\t\t});\n\n\t\tif(EmptyWidths > 1)\n\t\t{\n\t\t\t\$('input.col_options_width').show();\n\t\t\t\$('.col_options_width_label').show();\n\t\t}\n\t}\n\n\tfunction addDefaultTotalRow(totaltype)\n\t{\n\t\t// First check if this RowCount is indeed free to use. If undefined, it's fine, otherwise we mix up rows/columns\n\t\tvar RowCountChecker = 0;\n\t\tvar RowCount = \$('#templateblock_table tbody tr').length;\n\t\twhile(\$('input[name^=\"block_rows[' + RowCount + ']\"]').val() != undefined && RowCountChecker < 25)\n\t\t{\n\t\t\tRowCount++;\n\t\t\tRowCountChecker++;\n\t\t}\n\n\t\t\$('#templateblock_table tbody').append('<tr>');\n\t\t\$('#templateblock_table tbody tr:last-child').append('<td><img class=\"pointer sortablehandle sort_icon\" src=\"images/ico_sort.png\" /></td>');\n\t\tswitch(totaltype)\n\t\t{\n\t\t\tcase 'amountexcl': var language_string = __LANG_TEMPLATEBLOCK_TOTALTYPE_AMOUNTEXCL; break;\n\t\t\tcase 'amounttax': var language_string = __LANG_TEMPLATEBLOCK_TOTALTYPE_AMOUNTTAX; break;\n\t\t\tcase 'amountincl': var language_string = __LANG_TEMPLATEBLOCK_TOTALTYPE_AMOUNTINCL; break;\n\t\t\tcase 'pagetotalexcl': var language_string = __LANG_TEMPLATEBLOCK_TOTALTYPE_PAGETOTALEXCL; break;\n\t\t\tcase 'pagetotalincl': var language_string = __LANG_TEMPLATEBLOCK_TOTALTYPE_PAGETOTALINCL; break;\n\t\t}\n\n\t\tif(totaltype == 'amounttax')\n\t\t{\n\t\t\t\$('#templateblock_table tbody tr:last-child').append('<td width=\"450\" valign=\"top\" colspan=\"2\"><input type=\"hidden\" name=\"block_rows['+RowCount+'][style][totaltype]\" value=\"'+totaltype+'\"/><input type=\"hidden\" name=\"block_value['+RowCount+'][0]\" class=\"text1 size1\" value=\"\" /><input type=\"hidden\" name=\"block_value['+RowCount+'][1]\" class=\"text1 size1\" value=\"\" />'+language_string+'</td>');\n\t\t}\n\t\telse\n\t\t{\n\n\t\t\tif(totaltype == 'pagetotalexcl' || totaltype == 'pagetotalincl')\n\t\t\t{\n\t\t\t\tlanguage_string += '<br><font color=\"gray\" style=\"display:inline-block;margin-top:-10px;\" class=\"smallfont\">('+__LANG_TEMPLATEBLOCK_TOTALTYPE_ONLY_MULTIPLE_PAGES+')</font>';\n\t\t\t}\n\n\t\t\t\$('#templateblock_table tbody tr:last-child').append('<td width=\"220\"><input type=\"text\" name=\"block_value['+RowCount+'][0]\" class=\"text1 size1\" value=\"\" /></td>');\n\t\t\t\$('#templateblock_table tbody tr:last-child').append('<td width=\"220\"><input type=\"hidden\" name=\"block_rows['+RowCount+'][style][totaltype]\" value=\"'+totaltype+'\"/><input type=\"hidden\" name=\"block_value['+RowCount+'][1]\" class=\"text1 size1\" value=\"\" />'+language_string+'</td>');\n\t\t}\n\n\t\t\$('#templateblock_table tbody tr:last-child').append('<td class=\"col_borders\"><label class=\"infopopuptop delaypopup\"><input type=\"checkbox\" name=\"block_rows['+RowCount+'][borders][top]\" value=\"yes\" /><span class=\"popup\">'+__LANG_TEMPLATEBLOCK_BORDER_TOP+'<b></b></span></label><label class=\"infopopuptop delaypopup\"><input type=\"checkbox\" name=\"block_rows['+RowCount+'][borders][right]\" value=\"yes\" /><span class=\"popup\">'+__LANG_TEMPLATEBLOCK_BORDER_RIGHT+'<b></b></span></label><label class=\"infopopuptop delaypopup\"><input type=\"checkbox\" name=\"block_rows['+RowCount+'][borders][bottom]\" value=\"yes\" /><span class=\"popup\">'+__LANG_TEMPLATEBLOCK_BORDER_BOTTOM+'<b></b></span></label><label class=\"infopopuptop delaypopup\"><input type=\"checkbox\" name=\"block_rows['+RowCount+'][borders][left]\" value=\"yes\" /><span class=\"popup\">'+__LANG_TEMPLATEBLOCK_BORDER_LEFT+'<b></b></span></label></td>');\n\t\tif(\$('input[name=\"has_line_borders\"]').prop('checked')){\n\t\t\t\$('#templateblock_table tbody tr:last-child td:last-child').show();\n\t\t}\n\n\t\t\$('#templateblock_table tbody tr:last-child').append('<td class=\"col_bgcolor\"><input type=\"text\" name=\"block_rows['+RowCount+'][style][bgcolor]\" class=\"text1 size6 colorpicker\" value=\"\" /></td>');\n\t\tif(\$('input[name=\"has_line_bgcolor\"]').prop('checked')){\n\t\t\t\$('#templateblock_table tbody tr:last-child td:last-child').show();\n\t\t\t\$('#templateblock_table tbody tr:last-child td:last-child').find('.colorpicker').spectrum({preferredFormat: \"hex\",  color: \"#fff\",  clickoutFiresChange: true, showButtons: false});\n\t\t}\n\n\t}\n</script>";
}

?>