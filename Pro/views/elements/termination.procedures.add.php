<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
$helper_has_actions = !empty($termination_procedure->ProcedureActions) ? true : false;
echo "<table id=\"termination_procedure_actions_table\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"table1\" style=\"margin-top: 5px;\">\n<thead>\n\t<tr class=\"trtitle\">\n\t\t<th style=\"width:20px\">&nbsp;</th>\n\t\t<th style=\"width:140px;\">";
echo __("termination action type");
echo "</th>\n\t\t<th>";
echo __("termination action action");
echo "</th>\n\t\t<th style=\"width:310px;\">";
echo __("termination action when");
echo "</th>\n\t\t<th style=\"width:20px;\">&nbsp;</th>\n\t</tr>\n\t";
create_termination_procedure_action_tr($array_automated_tasks, $emailtemplates);
echo "</thead>\n<tbody>\n\t";
if($helper_has_actions) {
    foreach ($termination_procedure->ProcedureActions as $tmp_index => $tmp_procedure_action) {
        create_termination_procedure_action_tr($array_automated_tasks, $emailtemplates, $tmp_procedure_action, $tmp_index);
    }
}
echo "</tbody>\n</table>\n\n\n<a id=\"termination_procedure_add_action\" class=\"a1 c1\" style=\"line-height: 18px;display:inline-block;\"><img style=\"float: left; margin-right: 10px;\" src=\"images/ico_add.png\"> ";
echo __("termination action add action");
echo "</a>\n\n<style type=\"text/css\">\n.procedure_action_days { margin-left: 5px; }\n#termination_procedure_actions_table tr { position:relative; }\n#termination_procedure_actions_table tr span.procedure_action_delete { display:none; height:27px; width: 20px; background: url('images/ico_close.png') no-repeat 50% 50%; cursor:pointer;}\n#termination_procedure_actions_table tr:hover span.procedure_action_delete {display:inline-block; }\n</style>\n<script type=\"text/javascript\">\n\$(function(){\n\t\n\tvar ProcedureActionTypeCloneAutomatic = \$('select[name=\"ProcedureAction[Type][]\"] option[value=\"automatic\"]').first().clone();\n\tvar ProcedureActionTasksClone = \$('select[name=\"ProcedureAction[AutomatedTask][]\"]').first().clone();\n\n\t// Switch procedure type\n\t\$('#termination_procedure_actions_table').parents('form').find('select[name=\"ServiceType\"]').change(function(){\n\t\t\n\t\tvar service_type = \$(this).val();\n\t\t\n\t\t\$('select[name=\"ProcedureAction[AutomatedTask][]\"]').each(function(index, select_element)\n\t\t{\n\t\t\tvar current_value = \$(this).val();\n\t\t\tvar has_visible_tasks = false;\n\t\t\t\n\t\t\t\$(select_element).find('option').remove();\n\t\t\t\$(ProcedureActionTasksClone).find('option').each(function()\n\t\t\t{\n\t\t\t\tif(\$(this).val() && \$(this).val().indexOf(service_type + ':') >= 0)\n\t\t\t\t{\n\t\t\t\t\thas_visible_tasks = true;\n\t\t\t\t\t\$(select_element).append(\$(this).clone());\n\t\t\t\t}\n\t\t\t\telse if(\$(this).val() == '')\n\t\t\t\t{\n\t\t\t\t\t\$(select_element).append(\$(this).clone());\n\t\t\t\t}\n\t\t\t});\n\t\t\t\n\t\t\t// Set value, if exists\n\t\t\t\$(select_element).val(current_value);\n\t\t\t\n\t\t\tif(has_visible_tasks === false)\n\t\t\t{\n\t\t\t\t// We don't have automated tasks, so hide\n\t\t\t\tif(\$(select_element).parents('tr').find('select[name=\"ProcedureAction[Type][]\"]').val() == 'automatic')\n\t\t\t\t{\n\t\t\t\t\t\$(select_element).parents('tr').find('input[name=\"ProcedureAction[Description][]\"]').val('');\n\t\t\t\t\t\$(select_element).parents('tr').find('select[name=\"ProcedureAction[Type][]\"]').val('manual').change();\n\t\t\t\t}\t\t\t\t\t\n\t\t\t\t\n\t\t\t\t// Hide the option for automatic\n\t\t\t\t\$(select_element).parents('tr').find('select[name=\"ProcedureAction[Type][]\"] option[value=\"automatic\"]').remove();\n\t\t\t}\n\t\t\telse if(\$(select_element).parents('tr').find('select[name=\"ProcedureAction[Type][]\"] option[value=\"automatic\"]').val() != 'automatic')\n\t\t\t{\n\t\t\t\t// Show the option for automatic\n\t\t\t\t\$(select_element).parents('tr').find('select[name=\"ProcedureAction[Type][]\"]').append(\$(ProcedureActionTypeCloneAutomatic).clone());\n\t\t\t}\t\n\t\t});\t\n\t});\n\t// On init, run (edit)\n\t\$('#termination_procedure_actions_table').parents('form').find('select[name=\"ServiceType\"]').change();\t\n\t\n\t\$('input[name=\"helper_has_actions\"]').click(function(){\n\t\tif(\$(this).prop('checked'))\n\t\t{\n\t\t\t\$('#div_termination_actions').show();\n\t\t}\n\t\telse\n\t\t{\n\t\t\t\$('#div_termination_actions').hide();\n\t\t}\t\n\t});\n\n\t// Switch Type\n    \$(document).off('change', 'select[name=\"ProcedureAction[Type][]\"]');\n    \$(document).on('change', 'select[name=\"ProcedureAction[Type][]\"]', function()\n\t{\n\t\tif(\$(this).val() == 'automatic')\n\t\t{\n\t\t\t\$(this).parents('tr').find('input[name=\"ProcedureAction[Description][]\"]').hide();\n\t\t\t\$(this).parents('tr').find('select[name=\"ProcedureAction[AutomatedTask][]\"]').show();\n\t\t\t\$(this).parents('tr').find('select[name=\"ProcedureAction[EmailTemplate][]\"]').hide();\n\t\t}\n\t\telse if(\$(this).val() == 'mail2client' || \$(this).val() == 'mail2user')\n\t\t{\n\t\t\t\$(this).parents('tr').find('input[name=\"ProcedureAction[Description][]\"]').hide();\n\t\t\t\$(this).parents('tr').find('select[name=\"ProcedureAction[AutomatedTask][]\"]').hide();\n\t\t\t\$(this).parents('tr').find('select[name=\"ProcedureAction[EmailTemplate][]\"]').show();\n\t\t}\n\t\telse\n\t\t{\n\t\t\t\$(this).parents('tr').find('input[name=\"ProcedureAction[Description][]\"]').val('').show();\n\t\t\t\$(this).parents('tr').find('select[name=\"ProcedureAction[AutomatedTask][]\"]').hide();\n\t\t\t\$(this).parents('tr').find('select[name=\"ProcedureAction[EmailTemplate][]\"]').hide();\n\t\t}\n\t});\n\t\n\t// Switch When\n    \$(document).off('change', 'select[name=\"ProcedureAction[When][]\"]');\n    \$(document).on('change', 'select[name=\"ProcedureAction[When][]\"]', function()\n\t{\n\t\tif(\$(this).val() == 'direct' || \$(this).val() == 'on')\n\t\t{\n\t\t\t\$(this).parent().find('.procedure_action_days').hide();\n\t\t}\n\t\telse\n\t\t{\n\t\t\t\$(this).parent().find('.procedure_action_days').show();\n\t\t}\t\n\t});\n\t\n\t// Add extra action\n\t\$('#termination_procedure_add_action').click(function()\n\t{\n\t\t\$('#termination_procedure_actions_table tbody').append(\$('#termination_procedure_actions_new_element').clone().removeClass('hide'));\n\t\t\n\t\t// get last tr\n\t\tvar new_tr = \$('#termination_procedure_actions_table tbody tr').last();\n\t\t\$(new_tr).attr('id', '');\n\t\t// Counter\n\t\t\$(new_tr).find('td').first().html(\$('#termination_procedure_actions_table tbody tr').length + '.');\n\t\t\n\t\tif((\$('#termination_procedure_actions_table tbody tr').length-1) % 2 == 0)\n\t\t{\n\t\t\t\$(new_tr).addClass('tr1');\n\t\t}\n\t\t\n\t\t// Default values\n\t\t\$(new_tr).find('select[name=\"ProcedureAction[Type][]\"]').val('manual');\n\t\t\$(new_tr).find('input[name=\"ProcedureAction[Description][]\"]').val('');\n\t\t\$(new_tr).find('select[name=\"ProcedureAction[When][]\"]').val('on');\n\t\t\$(new_tr).find('.procedure_action_days').hide();\n\t\t\$(new_tr).find('input[name=\"ProcedureAction[Days][]\"]').val('');\n\t});\n\n    \$(document).off('click', 'span.procedure_action_delete');\n    \$(document).on('click', 'span.procedure_action_delete', function()\n\t{\n\t\t// Remove line\n\t\t\$(this).parents('tr').remove();\n\t\t\n\t\t// Recalculate\n\t\tif(\$('#termination_procedure_actions_table tbody tr').length == 0)\n\t\t{\n\t\t\t";
if(0 < $termination_procedure->id && !empty($termination_procedure->ProcedureActions)) {
    echo "\t\t\t\t\$('#termination_procedure_add_action').click();\n\t\t\t\t";
} else {
    echo "\t\t\t\t\$('#service_termination_change_actions').show();\n\t\t\t\t\$('#service_termination_actions').html('";
    echo __("termination no actions will be executed");
    echo "');\n\t\t\t\t";
}
echo "\t\t\treturn;\n\t\t}\n\t\telse\n\t\t{\n\t\t\t\$('#termination_procedure_actions_table tbody tr').each(function(index, element){\n\t\t\t\t\$(this).removeClass('tr1');\n\t\t\t\t\$(this).find('td').first().html((index+1) + '.');\n\n\t\t\t\tif(index % 2 == 0)\n\t\t\t\t{\n\t\t\t\t\t\$(this).addClass('tr1');\n\t\t\t\t}\n\t\t\t});\n\t\t}\n\t});\n\t\n\t";
if($helper_has_actions === false) {
    echo "\t\t\$('#termination_procedure_add_action').click();\n\t\t";
}
echo "});\n</script>\n\n";
function create_termination_procedure_action_tr($array_automated_tasks, $emailtemplates, $tmp_procedure_action = NULL, $tmp_index = false)
{
    $tr_id = $tmp_index === false ? " id=\"termination_procedure_actions_new_element\" class=\"hide\"" : "";
    echo "\t<tr";
    echo $tr_id;
    if($tmp_index % 2 === 0) {
        echo " class=\"tr1\"";
    }
    echo ">\n\t\t<td>";
    echo $tmp_index + 1;
    echo ".</td>\n\t\t<td>\n\t\t\t<select name=\"ProcedureAction[Type][]\" class=\"text1\" style=\"width: 125px;\">\n\t\t\t\t<option value=\"manual\"";
    if(!isset($tmp_procedure_action->ActionType) || "manual" == $tmp_procedure_action->ActionType) {
        echo " selected=\"selected\"";
    }
    echo ">";
    echo __("termination action type manual");
    echo "</option>\n\t\t\t\t<option value=\"automatic\"";
    if(isset($tmp_procedure_action->ActionType) && "automatic" == $tmp_procedure_action->ActionType) {
        echo " selected=\"selected\"";
    }
    echo ">";
    echo __("termination action type automatic");
    echo "</option>\n\t\t\t\t<option value=\"mail2client\"";
    if(isset($tmp_procedure_action->ActionType) && "mail2client" == $tmp_procedure_action->ActionType) {
        echo " selected=\"selected\"";
    }
    echo ">";
    echo __("termination action type mail2client");
    echo "</option>\n\t\t\t\t<option value=\"mail2user\"";
    if(isset($tmp_procedure_action->ActionType) && "mail2user" == $tmp_procedure_action->ActionType) {
        echo " selected=\"selected\"";
    }
    echo ">";
    echo __("termination action type mail2user");
    echo "</option>\n\t\t\t</select>\n\t\t</td>\n\t\t<td>\n\t\t\t<input name=\"ProcedureAction[Description][]\" style=\"width:100%;box-sizing: border-box;-moz-box-sizing: border-box;\" type=\"text\" class=\"text1";
    if(isset($tmp_procedure_action->ActionType) && "manual" != $tmp_procedure_action->ActionType) {
        echo " hide";
    }
    echo "\" value=\"";
    echo isset($tmp_procedure_action->Description) ? htmlspecialchars($tmp_procedure_action->Description) : "";
    echo "\"/>\n\t\t\t<select name=\"ProcedureAction[AutomatedTask][]\" style=\"\" class=\"text1 size4f";
    if(!isset($tmp_procedure_action->ActionType) || "automatic" != $tmp_procedure_action->ActionType) {
        echo " hide";
    }
    echo "\">\n\t\t\t\t<option value=\"\">";
    echo __("please choose");
    echo "</option>\n\t\t\t\t";
    if(is_array($array_automated_tasks)) {
        foreach ($array_automated_tasks as $task => $task_title) {
            echo "\t\t\t\t\t\t<option value=\"";
            echo $task;
            echo "\"";
            if(isset($tmp_procedure_action->Description) && $task == $tmp_procedure_action->Description) {
                echo " selected=\"selected\"";
            }
            echo ">";
            echo htmlspecialchars($task_title);
            echo "</option>\n\t\t\t\t\t\t";
        }
    }
    echo "\t\t\t</select>\n\t\t\t<select name=\"ProcedureAction[EmailTemplate][]\" style=\"\" class=\"text1 size4f";
    if(!isset($tmp_procedure_action->ActionType) || !in_array($tmp_procedure_action->ActionType, ["mail2client", "mail2user"])) {
        echo " hide";
    }
    echo "\">\n\t\t\t\t<option value=\"\">";
    echo __("please choose");
    echo "</option>\n\t\t\t\t";
    if(is_array($emailtemplates)) {
        foreach ($emailtemplates as $key => $email_template) {
            if(is_numeric($key)) {
                echo "\t\t\t\t\t\t\t<option value=\"";
                echo $key;
                echo "\"";
                if(isset($tmp_procedure_action->Description) && $key == $tmp_procedure_action->Description) {
                    echo " selected=\"selected\"";
                }
                echo ">";
                echo $email_template["Name"];
                echo "</option>\n\t\t\t\t\t\t\t";
            }
        }
    }
    echo "\t\t\t</select>\n\t\t</td>\n\t\t<td style=\"white-space: nowrap;\"><select name=\"ProcedureAction[When][]\" class=\"text1\" style=\"width: 175px;\">\n\t\t\t<option value=\"direct\"";
    if(!isset($tmp_procedure_action->When) || $tmp_procedure_action->When == "direct") {
        echo " selected=\"selected\"";
    }
    echo ">";
    echo __("termination action when direct");
    echo "</option>\n\t\t\t<option value=\"before\"";
    if(isset($tmp_procedure_action->When) && $tmp_procedure_action->When == "before") {
        echo " selected=\"selected\"";
    }
    echo ">";
    echo __("termination action when before");
    echo "</option>\n\t\t\t<option value=\"on\"";
    if(isset($tmp_procedure_action->When) && $tmp_procedure_action->When == "on") {
        echo " selected=\"selected\"";
    }
    echo ">";
    echo __("termination action when on");
    echo "</option>\n\t\t\t<option value=\"after\"";
    if(isset($tmp_procedure_action->When) && $tmp_procedure_action->When == "after") {
        echo " selected=\"selected\"";
    }
    echo ">";
    echo __("termination action when after");
    echo "</option>\n\t\t</select>\n\t\t<span class=\"procedure_action_days";
    if($tmp_procedure_action->When == "direct" || $tmp_procedure_action->When == "on") {
        echo " hide";
    }
    echo "\"><input name=\"ProcedureAction[Days][]\" type=\"text\" class=\"text1 size2\" value=\"";
    echo $tmp_procedure_action->Days;
    echo "\"/> ";
    echo __("days");
    echo "</span>\n\t\t</td>\t\n\t\t<td><span class=\"procedure_action_delete\">&nbsp;</span></td>\t\t\t\t\n\t</tr>\n\t";
}

?>