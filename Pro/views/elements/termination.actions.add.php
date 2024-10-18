<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
$helper_has_actions = !empty($termination->TerminationActions) ? true : false;
echo "<form name=\"change_actions\" method=\"POST\" action=\"services.php?page=terminations\">\n";
echo CSRF_Model::getToken();
echo "<input type=\"hidden\" name=\"action\" value=\"editActions\" />\n<input type=\"hidden\" name=\"ServiceType\" value=\"";
echo $termination->ServiceType;
echo "\" />\n<input type=\"hidden\" name=\"ServiceID\" value=\"";
echo $termination->ServiceID;
echo "\" />\n<table id=\"termination_actions_table\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"table1\" style=\"margin-top: 5px;\">\n<thead>\n\t<tr class=\"trtitle\">\n\t\t<th style=\"width:80px;\">";
echo __("date");
echo "</th>\n\t\t<th style=\"width:140px;\">";
echo __("termination action type");
echo "</th>\n\t\t<th>";
echo __("termination action action");
echo "</th>\n\t\t<th style=\"width:80px;\">";
echo __("status");
echo "</th>\n\t\t<th style=\"width:20px;\">&nbsp;</th>\n\t</tr>\n\t";
create_termination_action_tr($array_automated_tasks, $emailtemplates, $array_action_status, NULL, false, $termination->ServiceType);
echo "</thead>\n<tbody>\n\t";
if($helper_has_actions) {
    foreach ($termination->TerminationActions as $tmp_index => $tmp_action) {
        create_termination_action_tr($array_automated_tasks, $emailtemplates, $array_action_status, $tmp_action, $tmp_index, $termination->ServiceType);
    }
}
echo "</tbody>\n</table>\n</form>\n\n<a id=\"termination_add_action\" class=\"a1 c1\" style=\"line-height: 18px;display:inline-block;\"><img style=\"float: left; margin-right: 10px;\" src=\"images/ico_add.png\"> ";
echo __("termination action add action");
echo "</a>\n\n<br /><br />\n<p>\n<a id=\"termination_actions_edit_btn\" class=\"button1 alt1\"><span>";
echo __("btn edit");
echo "</span></a>\n</p>\n<style type=\"text/css\">\n#termination_actions_table tr { position:relative; }\n#termination_actions_table tr span.termination_action_delete { display:none; height:27px; width: 20px; background: url('images/ico_close.png') no-repeat 50% 50%; cursor:pointer; }\n#termination_actions_table tr:hover span.termination_action_delete {display:inline-block; }\n</style>\n<script type=\"text/javascript\">\n\$(function(){\n\t// Switch Type\n    \$(document).off('change', 'select[name=\"TerminationAction[Type][]\"]');\n    \$(document).on('change', 'select[name=\"TerminationAction[Type][]\"]', function()\n\t{\n\t\tif(\$(this).val() == 'automatic')\n\t\t{\n\t\t\t\$(this).parents('tr').find('input[name=\"TerminationAction[Description][]\"]').hide();\n\t\t\t\$(this).parents('tr').find('select[name=\"TerminationAction[AutomatedTask][]\"]').show();\n\t\t\t\$(this).parents('tr').find('select[name=\"TerminationAction[EmailTemplate][]\"]').hide();\n\t\t}\n\t\telse if(\$(this).val() == 'mail2client' || \$(this).val() == 'mail2user')\n\t\t{\n\t\t\t\$(this).parents('tr').find('input[name=\"TerminationAction[Description][]\"]').hide();\n\t\t\t\$(this).parents('tr').find('select[name=\"TerminationAction[AutomatedTask][]\"]').hide();\n\t\t\t\$(this).parents('tr').find('select[name=\"TerminationAction[EmailTemplate][]\"]').show();\n\t\t}\n\t\telse\n\t\t{\n\t\t\t\$(this).parents('tr').find('input[name=\"TerminationAction[Description][]\"]').show();\n\t\t\t\$(this).parents('tr').find('select[name=\"TerminationAction[AutomatedTask][]\"]').hide();\n\t\t\t\$(this).parents('tr').find('select[name=\"TerminationAction[EmailTemplate][]\"]').hide();\n\t\t}\n\t});\n\t\n\t// Click on button\n\t\$('#termination_actions_edit_btn').click(function()\n\t{\n\t\tif(\$(this).hasClass('button2'))\n\t\t{\n\t\t\t// Date is required\n\t\t}\n\t\telse\n\t\t{\n\t\t\t\$('form[name=\"change_actions\"]').submit();\n\t\t}\n\t});\n\t\n\t\t\n\t// Add extra action\n\t\$('#termination_add_action').click(function()\n\t{\n\t\t\$('#termination_actions_table').show();\n\n\t\t\$('#termination_actions_table tbody').append(\$('#termination_actions_new_element').clone().removeClass('hide'));\n\n\t\t// get last tr\n\t\tvar new_tr = \$('#termination_actions_table tbody tr').last();\n\t\t\$(new_tr).attr('id', '');\n\t\t\n\t\tif((\$('#termination_actions_table tbody tr').length-1) % 2 == 0)\n\t\t{\n\t\t\t\$(new_tr).addClass('tr1');\n\t\t}\n\t\t\n\t\t// Default values\n\t\t\$(new_tr).find('select[name=\"TerminationAction[Type][]\"]').val('manual');\n\t\t\$(new_tr).find('input[name=\"TerminationAction[Description][]\"]').val('');\n\t\t\$(new_tr).find('input[name=\"TerminationAction[Status][]\"]').val('');\n\t\t\$(new_tr).find('input[name=\"TerminationAction[Date][]\"]').removeClass('hasDatepicker').attr('id', '').val('');\n\t});\n\t\n\t// If description is filled, date is required\n    \$(document).on('change keyup', 'input[name=\"TerminationAction[Description][]\"]', function(){\n\t\tcheck_if_date_is_filled_for_tr(\$(this).parents('tr'));\n\t});\n    \$(document).on('change', 'input[name=\"TerminationAction[Date][]\"]', function(){\n\t\tcheck_if_date_is_filled_for_tr(\$(this).parents('tr'));\n\t});\n    \$(document).on('change', 'select[name=\"TerminationAction[AutomatedTask][]\"]', function(){\n\t\tcheck_if_date_is_filled_for_tr(\$(this).parents('tr'));\n\t});\n\n    \$(document).off('click', 'span.termination_action_delete');\n    \$(document).on('click', 'span.termination_action_delete', function()\n\t{\n\t\t// Remove line\n\t\t\$(this).parents('tr').remove();\n\t\t\n\t\t// Recalculate\n\t\tif(\$('#termination_actions_table tbody tr').length == 0)\n\t\t{\n\t\t\t\$('#termination_actions_table').hide();\n\t\t\treturn;\n\t\t}\n\t\telse\n\t\t{\n\t\t\t\$('#termination_actions_table tbody tr').each(function(index, element){\n\t\t\t\t\$(this).removeClass('tr1');\n\n\t\t\t\tif(index % 2 == 0)\n\t\t\t\t{\n\t\t\t\t\t\$(this).addClass('tr1');\n\t\t\t\t}\n\t\t\t});\n\t\t}\n\t});\n\t\n\t";
if($helper_has_actions === false) {
    echo "\t\t\$('#termination_add_action').click();\n\t\t";
}
echo "\t\n\t// Initial...check for automatic actions\n\tif(\$('select[name=\"TerminationAction[AutomatedTask][]\"] option[value!=\"\"]').length == 0)\n\t{\n\t\t\$('select[name=\"TerminationAction[Type][]\"] option[value=\"automatic\"]').remove();\n\t}\n});\n\nfunction check_if_date_is_filled_for_tr(tr_element)\n{\n\tif(\$(tr_element).find('select[name=\"TerminationAction[Type][]\"] option:selected').val() == 'manual' && \$(tr_element).find('input[name=\"TerminationAction[Description][]\"]').val() != '' && \$(tr_element).find('input[name=\"TerminationAction[Date][]\"]').val() == '')\n\t{\n\t\t\$('#termination_actions_edit_btn').removeClass('button1').addClass('button2');\n\t}\n\telse if(\$(tr_element).find('select[name=\"TerminationAction[Type][]\"] option:selected').val() == 'automatic' && \$(tr_element).find('select[name=\"TerminationAction[AutomatedTask][]\"] option:selected').val() != '' && \$(tr_element).find('input[name=\"TerminationAction[Date][]\"]').val() == '')\n\t{\n\t\t\$('#termination_actions_edit_btn').removeClass('button1').addClass('button2');\n\t}\n\telse\n\t{\n\t\t\$('#termination_actions_edit_btn').addClass('button1').removeClass('button2');\n\t}\n\n}\n</script>\n\n";
function create_termination_action_tr($array_automated_tasks, $emailtemplates, $array_action_status, $tmp_termination_action = NULL, $tmp_index = false, $service_type = "other")
{
    $tr_id = $tmp_index === false ? " id=\"termination_actions_new_element\" class=\"hide\"" : "";
    echo "\t<tr";
    echo $tr_id;
    if($tmp_index % 2 === 0) {
        echo " class=\"tr1\"";
    }
    echo ">\n\t\t<td style=\"white-space: nowrap;\"><input type=\"hidden\" name=\"TerminationAction[ID][]\" value=\"";
    echo isset($tmp_termination_action->id) ? $tmp_termination_action->id : 0;
    echo "\" /><input type=\"text\" name=\"TerminationAction[Date][]\" value=\"";
    echo rewrite_date_db2site($tmp_termination_action->Date);
    echo "\" class=\"text1 size6 datepicker\" /></td>\n\t\t<td>\n\t\t\t<select name=\"TerminationAction[Type][]\" class=\"text1\" style=\"width: 125px;\">\n\t\t\t\t<option value=\"manual\"";
    if(!isset($tmp_termination_action->ActionType) || "manual" == $tmp_termination_action->ActionType) {
        echo " selected=\"selected\"";
    }
    echo ">";
    echo __("termination action type manual");
    echo "</option>\n\t\t\t\t<option value=\"automatic\"";
    if(isset($tmp_termination_action->ActionType) && "automatic" == $tmp_termination_action->ActionType) {
        echo " selected=\"selected\"";
    }
    echo ">";
    echo __("termination action type automatic");
    echo "</option>\n\t\t\t\t<option value=\"mail2client\"";
    if(isset($tmp_termination_action->ActionType) && "mail2client" == $tmp_termination_action->ActionType) {
        echo " selected=\"selected\"";
    }
    echo ">";
    echo __("termination action type mail2client");
    echo "</option>\n\t\t\t\t<option value=\"mail2user\"";
    if(isset($tmp_termination_action->ActionType) && "mail2user" == $tmp_termination_action->ActionType) {
        echo " selected=\"selected\"";
    }
    echo ">";
    echo __("termination action type mail2user");
    echo "</option>\n\t\t\t</select>\n\t\t</td>\n\t\t<td>\n\t\t\t<input name=\"TerminationAction[Description][]\" style=\"width:100%;box-sizing: border-box;-moz-box-sizing: border-box;\" type=\"text\" class=\"text1";
    if(isset($tmp_termination_action->ActionType) && "manual" != $tmp_termination_action->ActionType) {
        echo " hide";
    }
    echo "\" value=\"";
    echo isset($tmp_termination_action->Description) ? htmlspecialchars($tmp_termination_action->Description) : "";
    echo "\"/>\n\t\t\t<select name=\"TerminationAction[AutomatedTask][]\" style=\"\" class=\"text1 size4f";
    if(!isset($tmp_termination_action->ActionType) || "automatic" != $tmp_termination_action->ActionType) {
        echo " hide";
    }
    echo "\">\n\t\t\t\t<option value=\"\">";
    echo __("please choose");
    echo "</option>\n\t\t\t\t";
    if(is_array($array_automated_tasks)) {
        foreach ($array_automated_tasks as $task => $task_title) {
            if(strpos($task, $service_type . ":") === false) {
            } else {
                echo "\t\t\t\t\t\t<option value=\"";
                echo $task;
                echo "\"";
                if(isset($tmp_termination_action->Description) && $task == $tmp_termination_action->Description) {
                    echo " selected=\"selected\"";
                }
                echo ">";
                echo htmlspecialchars($task_title);
                echo "</option>\n\t\t\t\t\t\t";
            }
        }
    }
    echo "\t\t\t</select>\n\t\t\t<select name=\"TerminationAction[EmailTemplate][]\" style=\"\" class=\"text1 size4f";
    if(!isset($tmp_termination_action->ActionType) || !in_array($tmp_termination_action->ActionType, ["mail2client", "mail2user"])) {
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
                if(isset($tmp_termination_action->Description) && $key == $tmp_termination_action->Description) {
                    echo " selected=\"selected\"";
                }
                echo ">";
                echo $email_template["Name"];
                echo "</option>\n\t\t\t\t\t\t\t";
            }
        }
    }
    echo "\t\t\t</select>\n\t\t</td>\n\t\t<td style=\"white-space: nowrap;\">\n\t\t\t<select name=\"TerminationAction[Status][]\" class=\"text1\">\n\t\t\t\t";
    foreach ($array_action_status as $status_key => $status_title) {
        if(in_array($status_key, ["canceled", "removed"]) || $status_key == "error" && (!isset($tmp_termination_action->Status) || $tmp_termination_action->Status != "error")) {
        } else {
            echo "\t\t\t\t\t<option value=\"";
            echo $status_key;
            echo "\"";
            if(isset($tmp_termination_action->Status) && $status_key == $tmp_termination_action->Status) {
                echo " selected=\"selected\"";
            }
            echo ">";
            echo $status_title;
            echo "</option>\n\t\t\t\t\t";
        }
    }
    echo "\t\t\t</select>\n\t\t</td>\t\n\t\t<td><span class=\"termination_action_delete\">&nbsp;</span></td>\t\t\t\t\n\t</tr>\n\t";
}

?>