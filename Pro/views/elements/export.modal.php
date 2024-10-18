<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "<h1 class=\"heading_3\">";
echo sprintf(__("export accounting modal title"), __("export accounting export_type - " . $export_type));
echo " </h1>\n\n";
if(isset($message_box) && $message_box) {
    echo $message_box;
}
echo "\n";
if(!empty($grouped_item_ids)) {
    echo "\t<strong class=\"strong\">";
    echo __("export accounting modal - data to export");
    echo "</strong><br />\n\t";
    foreach ($grouped_item_ids as $_export_type => $item_ids) {
        echo "\t\t<div class=\"export_record\">\n\t\t\t<div class=\"record_title\" style=\"display:inline-block;width: 200px;\">";
        echo count($item_ids);
        echo " ";
        echo strtolower(__("export accounting export_type - " . $_export_type));
        echo "</div>\n\t\t\t<div class=\"status_busy hide\" style=\"display:inline-block;text-align:right;width:250px\">\n\t\t\t\t";
        echo __("export accounting modal - progress");
        echo " <span data-exporttype=\"";
        echo $_export_type;
        echo "\" data-total=\"";
        echo count($item_ids);
        echo "\" data-status=\"pending\">1</span> ";
        echo __("export accounting modal - progress from");
        echo " ";
        echo count($item_ids);
        echo "&nbsp;&nbsp;&nbsp;&nbsp;<img class=\"export-loading-gif\" src=\"";
        echo __SITE_URL;
        echo "/images/loadinfo.gif\" /></div>\n\t\t</div>\n\n\t\t";
    }
    echo "\t<br/>\n\t<div id=\"export_result\"></div>\n\n\t<script type=\"text/javascript\">\n        var errors_reported = 0;\n        var items_exported = 0;\n        exportRemainingItems();\n        function exportRemainingItems()\n        {\n            // Get first pending type\n            var ExportType = \$('span[data-status=\"pending\"]').data('exporttype');\n\n            if(ExportType == undefined)\n            {\n                // We are ready\n                var html = '';\n                if(items_exported > 0)\n                {\n                    html += '<strong class=\"strong\">";
    echo __("export accounting modal - result");
    echo "</strong><br />';\n\n                    if(items_exported == 1)\n                    {\n                        html += items_exported + ' ";
    echo __("export accounting modal - result one exported");
    echo "<br />';\n                    }\n                    else\n                    {\n                        html += items_exported + ' ";
    echo __("export accounting modal - result more exported");
    echo "<br />';\n                    }\n                }\n                if(errors_reported > 0)\n                {\n                    if(html == '')\n                    {\n                        html += '<strong class=\"strong\">";
    echo __("export accounting modal - result");
    echo "</strong><br />';\n                    }\n\n                    if(items_exported == 1)\n                    {\n                        html += errors_reported + ' ";
    echo __("export accounting modal - result one error");
    echo "<br />';\n                        html += '<br /><a href=\"";
    echo url_generator("exportaccounting", false, ["module" => $package]);
    echo "\" class=\"button blue float_left\">";
    echo __("export accounting modal - result one error btn");
    echo "</a>';\n                    }\n                    else\n                    {\n                        html += errors_reported + ' ";
    echo __("export accounting modal - result more error");
    echo "<br />';\n                        html += '<br /><a href=\"";
    echo url_generator("exportaccounting", false, ["module" => $package]);
    echo "\" class=\"button blue float_left\">";
    echo __("export accounting modal - result more error btn");
    echo "</a>';\n                    }\n                }\n                else\n                {\n                    html += '<br /><a href=\"";
    echo url_generator("exportaccounting", false, ["module" => $package]);
    echo "\" class=\"button blue float_left\">";
    echo __("close");
    echo "</a>';\n                }\n\n                \$('#export_result').html(html);\n                return true;\n            }\n\n            // Show busy\n            \$('span[data-exporttype=\"' + ExportType + '\"]').parents('.export_record').find('.status_busy').removeClass('hide');\n\n            \$.post('";
    echo url_generator("exportaccounting", "exportaccounting_export", ["module" => $package], ["type" => ""]);
    echo "' + ExportType, function(data){\n\n                if(data.remaining_ids != undefined)\n                {\n                    \$('span[data-exporttype=\"' + ExportType + '\"]').html(\$('span[data-exporttype=\"' + ExportType + '\"]').data('total') - data.remaining_ids + 1); // +1 because we are busy with last one\n\n                    // Count errors and successes\n                    errors_reported += data.errors_reported;\n                    items_exported \t+= data.items_exported;\n\n                    if(data.remaining_ids == 0)\n                    {\n                        \$('span[data-exporttype=\"' + ExportType + '\"]').attr('data-status', 'processed');\n\n                        // Hide busy\n                        \$('span[data-exporttype=\"' + ExportType + '\"]').parents('.export_record').find('.status_busy').html('";
    echo __("export accounting modal - progress exported");
    echo "').addClass('c_gray');\n                    }\n\n                    // Next round\n                    exportRemainingItems();\n                }\n                else\n                {\n                    // Reload page\n                    location.href=location.href;\n                }\n            }, 'json');\n        }\n\t</script>\n\t";
    exit;
} else {
    echo "\n<form name=\"export_form\" method=\"POST\" action=\"";
    echo url_generator("exportaccounting", "exportaccounting_modal", ["module" => $package], ["type" => $export_type]);
    echo "\">\n\n\t";
    $filter = isset($_POST["Filter"]) && in_array($_POST["Filter"], ["export_needed", "manual"]) ? $_POST["Filter"] : (0 < count($statistics[$export_type]["export_needed"]) ? "export_needed" : "manual");
    echo "\t<strong class=\"strong\">";
    echo __("export accounting export - which data to export");
    echo "</strong><br />\n\t<label class=\"radio";
    if($filter != "export_needed") {
        echo " c_gray";
    }
    echo "\"><input type=\"radio\" name=\"Filter\" value=\"export_needed\"";
    if($filter != "export_needed") {
        echo " disabled=\"disabled\"";
    } else {
        echo " checked=\"checked\"";
    }
    echo "/> <span>";
    echo count($statistics[$export_type]["export_needed"]) . " " . (count($statistics[$export_type]["export_needed"]) == 1 ? __("export accounting export - export_needed one " . $export_type) : __("export accounting export - export_needed more " . $export_type));
    echo "</span></label><br />\n\t<label class=\"radio\"><input type=\"radio\" name=\"Filter\" value=\"manual\"";
    if($filter == "manual") {
        echo " checked=\"checked\"";
    }
    echo "/> <span>";
    echo __("export accounting export - manual " . $export_type);
    echo "</span></label><br />\n\t<br />\n\n\t";
    $last_filter = isset($_POST["FilterBy"]) && in_array($_POST["FilterBy"], ["code", "date"]) ? $_POST["FilterBy"] : (isset($statistics[$export_type]["filters"]["FilterBy"]) ? $statistics[$export_type]["filters"]["FilterBy"] : false);
    if(1 < count($available_filters)) {
        echo "\t\t<div id=\"filter_how_div\" class=\"hide\">\n\t\t\t<strong class=\"strong\">";
        echo __("export accounting export - how to filter");
        echo "</strong><br />\n\t\t\t";
        $checked_filter = $last_filter ? $last_filter : key($available_filters);
        if(isset($available_filters["code"])) {
            echo "<label class=\"radio\"><input type=\"radio\" name=\"FilterBy\" value=\"code\"";
            if($checked_filter == "code") {
                echo " checked=\"checked\"";
            }
            echo "/> <span>";
            echo __("export accounting export - filter code " . $export_type);
            echo "</span></label><br />";
        }
        if(isset($available_filters["date"])) {
            echo "<label class=\"radio\"><input type=\"radio\" name=\"FilterBy\" value=\"date\"";
            if($checked_filter == "date") {
                echo " checked=\"checked\"";
            }
            echo "/> <span>";
            echo __("export accounting export - filter date " . $export_type);
            echo "</span></label><br />";
        }
        echo "\t\t\t<br />\n\t\t</div>\n\t\t";
    } else {
        echo "\t\t<input type=\"radio\" name=\"FilterBy\" value=\"";
        echo key($available_filters);
        echo "\" checked=\"checked\" class=\"hide\"/>\n\t\t";
    }
    if(isset($_POST["FilterBy"])) {
        $last_filter = false;
    }
    $last_label_min = isset($statistics[$export_type]["filters"]["Min"]) ? $statistics[$export_type]["filters"]["Min"] : false;
    $last_label_max = isset($statistics[$export_type]["filters"]["Max"]) ? $statistics[$export_type]["filters"]["Max"] : false;
    if(isset($available_filters["code"])) {
        echo "\t\t<div data-filtertype=\"code\" class=\"filter_div hide\" style=\"display: inline-block;\">\n\t\t\t<strong class=\"strong\">";
        echo __("export accounting export - filter code label " . $export_type);
        echo "</strong>\n\t\t\t";
        if($last_filter == "code") {
            echo "<span class=\"c_gray float_right\" style=\"font-size: 12px;\">Vorige: ";
            echo htmlspecialchars($last_label_min);
            echo " ";
            echo __("from till");
            echo " ";
            echo htmlspecialchars($last_label_max);
            echo "</span>";
        }
        echo "\t\t\t<br />\n\t\t\t<input type=\"text\" name=\"Code[Min]\" value=\"";
        if(isset($_POST["Code"]["Min"])) {
            echo htmlspecialchars($_POST["Code"]["Min"]);
        }
        echo "\" style=\"width: 150px;\"/> ";
        echo __("from till");
        echo " <input type=\"text\" name=\"Code[Max]\" value=\"";
        if(isset($_POST["Code"]["Max"])) {
            echo htmlspecialchars($_POST["Code"]["Max"]);
        }
        echo "\" style=\"width: 150px;\"/><br />\n\t\t</div>\n\t\t";
    }
    if(isset($available_filters["date"])) {
        echo "\t\t<div data-filtertype=\"date\" class=\"filter_div hide\" style=\"display: inline-block;\">\n\t\t\t<strong class=\"strong\">";
        echo __("export accounting export - filter date label " . $export_type);
        echo "</strong>\n\t\t\t";
        if($last_filter == "date") {
            echo "<span class=\"c_gray float_right\" style=\"font-size: 12px;\">Vorige: ";
            echo rewrite_date_db2site($last_label_min);
            echo " ";
            echo __("from till");
            echo " ";
            echo rewrite_date_db2site($last_label_max);
            echo "</span>";
        }
        echo "\t\t\t<br />\n\t\t\t<input type=\"text\" name=\"Date[Min]\" value=\"";
        if(isset($_POST["Date"]["Min"])) {
            echo htmlspecialchars($_POST["Date"]["Min"]);
        }
        echo "\" style=\"width: 150px;\" class=\"datepicker\"/> ";
        echo __("from till");
        echo " <input type=\"text\" name=\"Date[Max]\" value=\"";
        if(isset($_POST["Date"]["Max"])) {
            echo htmlspecialchars($_POST["Date"]["Max"]);
        }
        echo "\" style=\"width: 150px;\" class=\"datepicker\"/>\n\t\t</div>\n\t\t";
    }
    echo "\n\n\t<div class=\"button_bar clearfix\">\n\t\t<input id=\"submit_button\" type=\"button\" value=\"";
    echo __("export accounting export - submit btn");
    echo "\" class=\"button blue float_left\" />\n\t\t<input type=\"button\" value=\"";
    echo __("cancel");
    echo "\" class=\"button grey float_right\" onclick=\"closeModal();\"/>\n\t</div>\n\n</form>\n<script type=\"text/javascript\">\n    \$(function(){\n        \$('input[name=\"FilterBy\"]').change(function(){\n            // Hide all filters\n            \$('div.filter_div').addClass('hide');\n\n            // Show selected filter\n            \$('div[data-filtertype=\"' + \$(this).val() + '\"]').removeClass('hide');\n        });\n\n        // Change between manual and export_needed\n        \$('input[name=\"Filter\"]').change(function(){\n            if(\$(this).val() == 'manual')\n            {\n                // Show filters\n                \$('#filter_how_div').removeClass('hide');\n                \$('div[data-filtertype=\"' + \$('input[name=\"FilterBy\"]:checked').val() + '\"]').removeClass('hide');\n            }\n            else\n            {\n                // Hide filters\n                \$('#filter_how_div').addClass('hide');\n\n                // Hide all filters\n                \$('div.filter_div').addClass('hide');\n            }\n        });\n\n        // Initial\n        if(\$('input[name=\"Filter\"]:checked').val() == 'manual')\n        {\n            var CurrentFilterBy = (\$('input[name=\"FilterBy\"]:checked').val() != undefined) ? \$('input[name=\"FilterBy\"]:checked').val() : \$('input[name=\"FilterBy\"]').val();\n            \$('div[data-filtertype=\"' + CurrentFilterBy + '\"]').removeClass('hide');\n            \$('#filter_how_div').removeClass('hide');\n        }\n    });\n</script>";
}

?>