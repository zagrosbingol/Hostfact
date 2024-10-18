<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
function show_template_table($data_array, $options = [])
{
    $page = $options["page"];
    $table_type = isset($options["table_type"]) ? $options["table_type"] : "subtable";
    $session_name = isset($options["session_name"]) ? $options["session_name"] : "";
    $current_page = isset($options["current_page"]) ? $options["current_page"] : "1";
    $current_page_url = isset($options["current_page_url"]) ? $options["current_page_url"] : "";
    $redirect_page = isset($options["redirect_page"]) ? $options["redirect_page"] : "";
    $redirect_id = isset($options["redirect_id"]) ? $options["redirect_id"] : "";
    $hide_columns = isset($options["hide_columns"]) ? $options["hide_columns"] : [];
    $column_count = 3 - count($hide_columns);
    global $emailtemplates;
    $session = $_SESSION[$session_name];
    if(isset($options["add_template"]) && $options["add_template"] == "dialog") {
        global $templates_invoices;
        global $templates_pricequotes;
        global $templates_other;
        echo "\t\t\n\t\t<div id=\"add_template\" class=\"hide\" title=\"";
        echo __("add template");
        echo "\">\n\t\t\t<form id=\"TemplateForm\" name=\"form_template\" method=\"post\" action=\"?page=show";
        echo $page;
        echo "\">\n\t\t\t<input type=\"hidden\" name=\"action\" value=\"create_new\" />\n\t\t\t<strong>";
        echo __("template add - what kind of template do you want to add?");
        echo "</strong><br />\n\t\t\t<label><input type=\"radio\" name=\"new_type\" value=\"default\" checked=\"checked\"/> ";
        echo __("template add - default template");
        echo "</label><br />\n\t\t\t<label><input type=\"radio\" name=\"new_type\" value=\"clone\" /> ";
        echo __("template add - clone template");
        echo "</label><br />\n\t\t\t<br />\n\t\t\t\n\t\t\t<div id=\"add_template_clone\" class=\"hide\">\n\t\t\t\t<strong>";
        echo __("template add - which template to clone");
        echo "</strong>\n\t\t\t\t<select name=\"clone_id\" class=\"text1 size4f\">\n\t\t\t\t\t<option value=\"\">";
        echo __("please choose");
        echo "</option>\n\t\t\t\t\t";
        if(0 < $templates_invoices["CountRows"]) {
            echo "\t\t\t\t\t<optgroup label=\"";
            echo __("invoice templates");
            echo "\">\n\t\t\t\t\t\t";
            foreach ($templates_invoices as $k => $v) {
                if(is_numeric($k)) {
                    echo "\t\t\t\t\t\t<option value=\"";
                    echo $k;
                    echo "\">";
                    echo $v["Name"];
                    echo "</option>\n\t\t\t\t\t\t";
                }
            }
            echo "\t\t\t\t\t</optgroup>\n\t\t\t\t\t";
        }
        echo "\t\t\t\t\t";
        if(0 < $templates_pricequotes["CountRows"]) {
            echo "\t\t\t\t\t<optgroup label=\"";
            echo __("pricequote templates");
            echo "\">\n\t\t\t\t\t\t";
            foreach ($templates_pricequotes as $k => $v) {
                if(is_numeric($k)) {
                    echo "\t\t\t\t\t\t<option value=\"";
                    echo $k;
                    echo "\">";
                    echo $v["Name"];
                    echo "</option>\n\t\t\t\t\t\t";
                }
            }
            echo "\t\t\t\t\t</optgroup>\n\t\t\t\t\t";
        }
        echo "\t\t\t\t\t";
        if(0 < $templates_other["CountRows"]) {
            echo "\t\t\t\t\t<optgroup label=\"";
            echo __("other templates");
            echo "\">\n\t\t\t\t\t\t";
            foreach ($templates_other as $k => $v) {
                if(is_numeric($k)) {
                    echo "\t\t\t\t\t\t<option value=\"";
                    echo $k;
                    echo "\">";
                    echo $v["Name"];
                    echo "</option>\n\t\t\t\t\t\t";
                }
            }
            echo "\t\t\t\t\t</optgroup>\n\t\t\t\t\t";
        }
        echo "\t\t\t\t</select>\n\t\t\t\t<br /><br />\n\t\t\t</div>\n\t\t                \n\t\t\t<p><a id=\"add_template_btn\" class=\"button1 alt1 float_left\"><span>";
        echo __("proceed");
        echo "</span></a></p>\n\t\t\t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#add_template').dialog('close');\"><span>";
        echo __("cancel");
        echo "</span></a></p>\n\t\t\t</form>\n\t\t</div>\n\t\t";
    }
    echo "\t\n\t<form id=\"TemplateForm\" name=\"form_templates\" method=\"post\" action=\"templates.php?page=view";
    if($redirect_page) {
        echo "&amp;from_page=" . $redirect_page;
    }
    if($redirect_id) {
        echo "&amp;from_id=" . $redirect_id;
    }
    echo "\"><fieldset>\n\t\n\t<div id=\"SubTable_Templates";
    echo $page;
    echo "\">\t\t\t\n\t\t<table id=\"MainTable_Templates";
    echo $page;
    echo "\" class=\"table1\" cellpadding=\"0\" cellspacing=\"0\">\n\t\t<tr class=\"trtitle\">\n\t\t\t<th scope=\"col\" style=\"white-space:nowrap;\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','Name','Templates";
    echo $page;
    echo "','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 ";
    if($session["sort"] == "Name") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("name");
    echo "</a></td>\n\t\t\t<th scope=\"col\" style=\"white-space:nowrap;\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','Location','Templates";
    echo $page;
    echo "','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 ";
    if($session["sort"] == "Location") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("writing paper for download (PDF)");
    echo "</a></td>\n\t\t\t<th scope=\"col\" style=\"white-space:nowrap;\"><a onclick=\"ajaxSave('";
    echo $session_name;
    echo "','sort','PostLocation','Templates";
    echo $page;
    echo "','";
    echo $current_page_url;
    echo "');\" class=\"ico set2 ";
    if($session["sort"] == "PostLocation") {
        if($session["order"] == "ASC") {
            echo "arrowup";
        } else {
            echo "arrowdown";
        }
    } else {
        echo "arrowhover";
    }
    echo "\">";
    echo __("writing paper for print (PDF)");
    echo "</a></td>\n\t\t\t";
    if(!in_array("EmailTemplate", $hide_columns)) {
        echo "<th scope=\"col\" style=\"white-space:nowrap;\">";
        echo __("emailtemplate");
        echo "</td>";
    }
    echo "\t\t</tr>\n\t\t";
    $templateCounter = 0;
    foreach ($data_array as $templateID => $template) {
        if(is_numeric($templateID)) {
            $templateCounter++;
            echo "\t\t<tr ";
            if($templateCounter % 2 === 1) {
                echo "class=\"tr1\"";
            }
            echo " ";
            echo $template["Standard"] == "1" ? "style=\"font-weight:bold;\"" : "";
            echo ">\n\t\t\t<td>\n\t\t\t\t";
            if(U_LAYOUT_EDIT) {
                echo "\t\t\t\t<a href=\"?page=show";
                echo $page;
                echo "&amp;id=";
                echo $template["id"];
                echo "\" class=\"c1 a1\">";
                echo $template["Name"];
                echo "</a>\n\t\t\t\t";
            } else {
                echo "\t\t\t\t";
                echo $template["Name"];
                echo "\t\t\t\t";
            }
            echo "\t\t\t\t";
            echo $template["Standard"] == "1" ? "(" . __("default") . ")" : "";
            echo "\t\t\t</td>\n\t\t\t<td>";
            echo substr($template["Location"], strlen(DIR_PDF_FILES));
            echo "</td>\n\t\t\t<td>";
            echo substr($template["PostLocation"], strlen(DIR_PDF_FILES));
            echo "</td>\n\t \t\t";
            if(!in_array("EmailTemplate", $hide_columns)) {
                echo "<td>";
                if(0 < $template["EmailTemplate"] && isset($emailtemplates[$template["EmailTemplate"]])) {
                    echo "<a href=\"templates.php?page=showemail&amp;id=" . $template["EmailTemplate"] . "\" class=\"a1\">" . $emailtemplates[$template["EmailTemplate"]]["Name"] . "</a>";
                } else {
                    echo "-";
                }
                echo "</td>";
            }
            echo "\t\t</tr>\n\t\t";
        }
    }
    if($templateCounter === 0) {
        echo "\t\t<tr>\n\t\t\t<td colspan=\"";
        echo $column_count;
        echo "\">\n\t\t\t\t";
        echo __("no results found");
        echo "\t\t\t</td>\n\t\t</tr>\n\t\t";
    }
    echo "\t\t</table>\n\t</div>\n\t\n\t</fieldset></form>\n\t\n\t";
}

?>