<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "<!--right-->\n<div class=\"accounting_export\">\n\t<!--right-->\n\n\t";
echo $message_box;
echo "\n\n\n\t<div class=\"float_right\" style=\"margin-top: 22px;\">\n\t\t";
if(isset($export->supported["payment_invoice"]) && $export->supported["payment_invoice"] || isset($export->supported["payment_purchase"]) && $export->supported["payment_purchase"]) {
    echo "<div style=\"display:inline-block;height:34px;\">\n\t\t\t&nbsp;\n\t\t\t<a href=\"";
    echo url_generator("exportaccounting", "exportaccounting_payment", ["module" => $package]);
    echo "\" class=\"has_loading_button\">Betalingen ophalen</a>\n\t\t\t<div class=\"loading_btn hide\" style=\"margin-top: -5px;\"><img src=\"";
    echo __SITE_URL;
    echo "/images/loadinfo.gif\">";
    echo __("loading");
    echo "</div>\n\t\t\t<span class=\"c_gray margin_left_30px\">|</span>\n\t\t\t</div>\n\t\t\t";
}
echo "\t\t<div style=\"display:inline-block;height:34px;min-width:160px;text-align:right;\">\n\t\t\t&nbsp;\n\t\t\t<a href=\"";
echo url_generator("exportaccounting", false, ["module" => $package], ["oauth" => "settings"]);
echo "\" class=\"has_loading_button\">";
echo htmlspecialchars($package_information["name"]);
echo " instellingen</a>\n\t\t\t<div class=\"loading_btn hide\" style=\"margin-top: -5px;\"><img src=\"";
echo __SITE_URL;
echo "/images/loadinfo.gif\">";
echo __("loading");
echo "</div>\n\t\t</div>\n\t</div>\n\t";
if(isset($office_name) && $office_name) {
    echo "\t\t<h1 class=\"heading_3 margin_bottom_40px\">";
    echo htmlspecialchars($package_information["name"]) . ": " . htmlspecialchars($office_name);
    echo "</h1>\n\t\t";
} else {
    echo "<h1 class=\"heading_3 margin_bottom_40px\">" . htmlspecialchars($package_information["name"]) . "</h1>";
}
echo "\n\t<div>\n\t\t";
foreach ($dashboard_groups as $_group) {
    $group_counter = 0;
    foreach ($_group as $_export_type) {
        if(!isset($statistics[$_export_type])) {
        } else {
            if($group_counter === 0) {
                echo "\t\t<div class=\"export_group\">\n\t\t\t";
            }
            $group_counter++;
            $_export_info = $statistics[$_export_type];
            echo "\t\t\t<div class=\"export_block block\" data-exporttype=\"";
            echo $_export_type;
            echo "\" data-exportneeded=\"";
            echo count($_export_info["export_needed"]);
            echo "\">\n\n\t\t\t\t<div class=\"export_info_block\">\n\t\t\t\t\t";
            if(!empty($_export_info["export_needed"])) {
                echo "\t\t\t\t\t\t<div class=\"export_info_avatar float_left\">\n\t\t\t\t\t\t\t";
                echo count($_export_info["export_needed"]);
                echo "\t\t\t\t\t\t</div>\n\t\t\t\t\t\t";
            } else {
                echo "\t\t\t\t\t\t<div class=\"export_info_avatar float_left ";
                echo !isset($_export_info["has_no_items"]) || !$_export_info["has_no_items"] ? "green" : "no_items";
                echo "\"></div>\n\t\t\t\t\t\t";
            }
            echo "\t\t\t\t\t<div>\n\t\t\t\t\t\t<span class=\"strong\">";
            echo __("export accounting export_type - " . $_export_type);
            echo "</span><br />\n\t\t\t\t\t\t<span class=\"c_gray\" style=\"font-size:12px\" data-placeholder=\"exportneeded_text\">\n\t\t\t\t\t\t\t";
            if(!empty($_export_info["export_needed"])) {
                echo count($_export_info["export_needed"]) . " " . __("export accounting - x to export");
            } elseif(!isset($_export_info["has_no_items"]) || !$_export_info["has_no_items"]) {
                echo __("export accounting - nothing to export");
            }
            echo "\t\t\t\t\t\t</span>\n\t\t\t\t\t</div>\n\n\t\t\t\t\t<div class=\"clearfix\"></div>\n\n\t\t\t\t\t<div class=\"c_gray\" style=\"margin-top:10px;width: 80px;float:left;font-size:12px;\">";
            echo __("export accounting - last exported at");
            echo "</div>\n\t\t\t\t\t<div class=\"c_gray\" style=\"margin-top:10px;font-size:12px;\" data-placeholder=\"exportlastdate_text\">\n\t\t\t\t\t\t";
            if(isset($_export_info["export_lastdate"]) && $_export_info["export_lastdate"] && $_export_info["export_lastdate"] != "0000-00-00 00:00:00") {
                echo $export->showLastExportDate($_export_info["export_lastdate"]);
            } else {
                echo __("export accounting - not yet exported");
            }
            echo "</div>\n\t\t\t\t</div>\n\t\t\t\t";
            if(!isset($_export_info["has_no_items"]) || !$_export_info["has_no_items"]) {
                echo "<a class=\"goto modal_link\"  style=\"margin-top:10px\" data-href=\"";
                echo url_generator("exportaccounting", "exportaccounting_modal", ["module" => $package], ["type" => $_export_type]);
                echo "\">";
                echo __("export");
                echo "</a>";
            } elseif(in_array($_export_type, $export->importableData)) {
                echo "<a class=\"goto modal_link\" style=\"margin-top:10px\" data-href=\"";
                echo url_generator("exportaccounting", "exportaccounting_import", ["module" => $package], ["type" => $_export_type]);
                echo "\">Importeren uit ";
                echo htmlspecialchars($package_information["name"]);
                echo "</a>";
            }
            echo "\t\t\t</div>\n\t\t\t";
        }
    }
    if(0 < $group_counter) {
        echo "</div>";
    }
}
echo "\n\t\t</div>\n\n\t\t<div class=\"clearfix\"></div>\n\t\t<br /><br />\n\n\t\t";
if(!empty($manual_actions)) {
    echo "\t\t\t<div id=\"manual_action_block_div\" class=\"action_block_div\">\n\t\t\t\t<h1 class=\"heading_3 margin_bottom_20px\">";
    echo __("export accounting - manual action required");
    echo "</h1>\n\n\t\t\t\t<a id=\"manual_action_retry_all\" class=\"button blue float_left\">";
    echo __("export accounting - manual action retry all");
    echo "</a>\n\t\t\t\t<div class=\"loading_btn hide\" style=\"margin-top:7px\"><img src=\"";
    echo __SITE_URL;
    echo "/images/loadinfo.gif\" />";
    echo __("export accounting loading");
    echo "</div>\n\n\t\t\t\t<div class=\"float_right\" style=\"height:32px;line-height: 32px;\">\n\t\t\t\t\t";
    echo __("export accounting - manual action filter");
    echo "\t\t\t\t\t<select name=\"ManualActionFilter\" style=\"width: 200px;\">\n\t\t\t\t\t\t<option value=\"\" data-title=\"";
    echo __("export accounting - manual action filter all");
    echo "\">";
    echo __("export accounting - manual action filter all");
    echo "</option>\n\t\t\t\t\t\t";
    foreach ($statistics as $_export_type => $_export_info) {
        echo "<option value=\"";
        echo $_export_type;
        echo "\" data-title=\"";
        echo __("export accounting export_type - " . $_export_type);
        echo "\"></option>";
    }
    echo "\t\t\t\t\t</select>\n\t\t\t\t</div>\n\t\t\t\t<div class=\"clearfix\"></div>\n\t\t\t\t<br /><br />\n\t\t\t\t";
    foreach ($manual_actions as $_item) {
        $this->_item = $_item;
        $this->element("export.manual.block");
    }
    unset($this->_item);
    echo "\t\t\t</div>\n\t\t\t";
}
if(!empty($paid_in_hostfact_diffs)) {
    echo "\t\t\t<div class=\"clearfix\"></div>\n\t\t\t<br /><br/><br />\n\n\t\t\t<div class=\"action_block_div\">\n\t\t\t\t<div class=\"float_right\">\n\t\t\t\t\t<a href=\"";
    echo url_generator("exportaccounting", "exportaccounting_payment", ["module" => $package]);
    echo "\" class=\"has_loading_button\">Betalingen ophalen</a>\n\n\t\t\t\t\t<div class=\"loading_btn hide\" style=\"margin-top: -5px;\"><img src=\"";
    echo __SITE_URL;
    echo "/images/loadinfo.gif\">";
    echo __("loading");
    echo "</div>\n\n\t\t\t\t</div>\n\n\t\t\t\t<h1 class=\"heading_3 margin_bottom_20px\">";
    echo sprintf(__("export accounting - paid in software, not in accounting package"), htmlspecialchars($package_information["name"]), count($paid_in_hostfact_diffs));
    echo "</h1>\n\n\t\t\t\t";
    echo sprintf(__("export accounting - paid in software, not in accounting package - explained"), htmlspecialchars($package_information["name"]), htmlspecialchars($package_information["name"]));
    echo "\n\t\t\t\t<div class=\"clearfix\"></div>\n\t\t\t\t<br /><br />\n\t\t\t\t";
    foreach ($paid_in_hostfact_diffs as $_item) {
        $this->_item = $_item;
        $this->element("export.manual.block");
    }
    unset($this->_item);
    echo "\t\t\t</div>\n\t\t\t";
}
echo "\t\t<div class=\"clearfix\"></div>\n\t\t<script type=\"text/javascript\">\n            \$(function(){\n                \$('select[name=\"ManualActionFilter\"]').change(function(){\n                    if(\$(this).val() == '')\n                    {\n                        // Show all\n                        \$('#manual_action_block_div .manual_action_block').removeClass('hide');\n\n                        // All retry button text\n                        \$('#manual_action_retry_all').html('";
echo __("export accounting - manual action retry all");
echo "');\n                    }\n                    else\n                    {\n                        // Hide all except specific type\n                        \$('#manual_action_block_div .manual_action_block').addClass('hide');\n                        \$('#manual_action_block_div .manual_action_block[data-exporttype=\"' + \$(this).val() + '\"]').removeClass('hide');\n\n                        // All retry button text\n                        \$('#manual_action_retry_all').html(\$(this).find('option:selected').data('title') + ' ";
echo __("export accounting - manual action retry specific type");
echo "');\n                    }\n                });\n\n                \$('#manual_action_retry_all').click(function(){\n                    \$('html,body').animate({scrollTop: 0},'slow');\n                    openModal('";
echo url_generator("exportaccounting", "exportaccounting_modal", ["module" => $package], ["retry" => ""]);
echo "' + \$('select[name=\"ManualActionFilter\"]').val());\n                });\n                // Initials calculate of manual actions\n                recalculate_manual_actions();\n\n\n                \$(document).on('click', '.retry_link', function(){\n\n                    if(\$(this).data('already_clicked') == true)\n                    {\n                        return true;\n                    }\n\n                    \$(this).data('already_clicked', true);\n\n                    var ParentHTML = \$(this).parents('.action_block_div');\n                    var BlockElement = \$(this).parents('.manual_action_block');\n\n                    \$(BlockElement).find('.item_message').css('visibility', 'hidden');\n\n                    \$.post('";
echo url_generator("exportaccounting", "exportaccounting_export", ["module" => $package], ["type" => ""]);
echo "' + \$(BlockElement).data('exporttype') + '&retry=' + \$(BlockElement).data('referenceid'), function(data){\n\n                        var DashboardBlock = \$('.export_block[data-exporttype=\"' + \$(BlockElement).data('exporttype') + '\"]');\n\n                        if(data.Status == 'success')\n                        {\n                            if(\$(BlockElement).data('exporttype') == 'payment_invoice' || \$(BlockElement).data('exporttype') == 'payment_purchase')\n                            {\n                                \$(BlockElement).find('.loading_btn').after('<font class=\"strong\" style=\"color:#6F9A56\">";
echo __("export accounting - manual action retry payment_invoice successfull");
echo "</font>');\n                            }\n                            else\n                            {\n                                \$(BlockElement).find('.loading_btn').after('<font class=\"strong\" style=\"color:#6F9A56\">";
echo __("export accounting - manual action retry successfull");
echo "</font>');\n                            }\n\n                            \$(BlockElement).find('.loading_btn').remove();\n                            setTimeout(function(){\n                                \$(BlockElement).remove();\n\n                                // Check if this was the last manual_block\n                                if(\$(ParentHTML).find('.manual_action_block').length == 0)\n                                {\n                                    // Reload page\n                                    location.href=location.href;\n                                }\n                                else if(\$(ParentHTML).find('.manual_action_block:visible').length == 0)\n                                {\n                                    // Change filter\n                                    \$('select[name=\"ManualActionFilter\"]').val('').change();\n                                }\n\n                                // Recalculate the number of manual actions\n                                recalculate_manual_actions();\n\n                                if(\$(DashboardBlock).html() != null) {\n\n                                    // Recalculate the dashboard items in the top\n                                    \$(DashboardBlock).data('exportneeded', \$(DashboardBlock).data('exportneeded') - 1);\n                                    \$(DashboardBlock).find('div[data-placeholder=\"exportlastdate_text\"]').html(data.DateText);\n\n                                    if (\$(DashboardBlock).data('exportneeded') == 0) {\n                                        // Now complete exported\n                                        \$(DashboardBlock).find('.export_info_avatar').html('').addClass('green');\n                                        \$(DashboardBlock).find('span[data-placeholder=\"exportneeded_text\"]').html('";
echo __("export accounting - nothing to export");
echo "');\n                                    }\n                                    else {\n                                        // Not complete exported\n                                        \$(DashboardBlock).find('.export_info_avatar').html(\$(DashboardBlock).data('exportneeded')).removeClass('green');\n                                        \$(DashboardBlock).find('span[data-placeholder=\"exportneeded_text\"]').html(\$(DashboardBlock).data('exportneeded') + ' ";
echo __("export accounting - x to export");
echo "');\n                                    }\n                                }\n                            }, 1000);\n                        }\n                        else if(data.Message)\n                        {\n                            \$(BlockElement).after(data.Message);\n                            \$(BlockElement).remove();\n                            \$(DashboardBlock).find('div[data-placeholder=\"exportlastdate_text\"]').html(data.DateText);\n                        }\n                        else\n                        {\n                            // Reload page\n                            location.href=location.href;\n                        }\n\n                    }, 'json');\n                });\n\n                \$(document).on('click', '.ignore, .ignore_link', function(){\n\n                    if(\$(this).data('already_clicked') == true)\n                    {\n                        return true;\n                    }\n\n                    \$(this).data('already_clicked', true);\n\n                    var ParentHTML = \$(this).parents('.action_block_div');\n                    var BlockElement = \$(this).parents('.manual_action_block');\n                    var DashboardBlock = \$('.export_block[data-exporttype=\"' + \$(BlockElement).data('exporttype') + '\"]');\n\n                    var ReferenceType = \$(BlockElement).data('exporttype');\n                    var ReferenceID = \$(BlockElement).data('referenceid');\n\n                    \$(BlockElement).remove();\n                    // Check if this was the last manual_block\n                    if(\$(ParentHTML).find('.manual_action_block').length == 0)\n                    {\n                        \$(ParentHTML).remove();\n\n                        // Reload page\n                        location.href=location.href;\n\n                    }\n                    else if(\$(ParentHTML).find('.manual_action_block:visible').length == 0)\n                    {\n                        // Change filter\n                        \$('select[name=\"ManualActionFilter\"]').val('').change();\n                    }\n                    // Recalculate the number of manual actions\n                    recalculate_manual_actions();\n\n                    if(\$(DashboardBlock).html() != null) {\n                        // Recalculate the dashboard items in the top\n                        \$(DashboardBlock).data('exportneeded', \$(DashboardBlock).data('exportneeded') - 1);\n\n                        if (\$(DashboardBlock).data('exportneeded') == 0) {\n                            // Now complete exported\n                            \$(DashboardBlock).find('.export_info_avatar').html('').addClass('green');\n                            \$(DashboardBlock).find('span[data-placeholder=\"exportneeded_text\"]').html('";
echo __("export accounting - nothing to export");
echo "');\n                        }\n                        else {\n                            // Not complete exported\n                            \$(DashboardBlock).find('.export_info_avatar').html(\$(DashboardBlock).data('exportneeded')).removeClass('green');\n                            \$(DashboardBlock).find('span[data-placeholder=\"exportneeded_text\"]').html(\$(DashboardBlock).data('exportneeded') + ' ";
echo __("export accounting - x to export");
echo "');\n                        }\n                    }\n\n                    \$.post('";
echo url_generator("exportaccounting", "exportaccounting_export", ["module" => $package], ["type" => ""]);
echo "' + ReferenceType + '&ignore=' + ReferenceID);\n                });\n            });\n\n            function recalculate_manual_actions()\n            {\n                \$('select[name=\"ManualActionFilter\"] option').each(function(index,option_element){\n                    var MessageOfThisType = 0;\n\n                    if(\$(this).val() == '')\n                    {\n                        // All types\n                        MessageOfThisType = \$('#manual_action_block_div .manual_action_block').length;\n                    }\n                    else\n                    {\n                        // Other types\n                        MessageOfThisType = \$('#manual_action_block_div .manual_action_block[data-exporttype=\"' + \$(this).val() + '\"]').length;\n\n                        if(MessageOfThisType == undefined || MessageOfThisType == 0)\n                        {\n                            \$(this).remove();\n                            return;\n                        }\n                    }\n\n                    // Build value of filter\n                    \$(this).text(\$(this).data('title') + ' (' + MessageOfThisType + ')');\n\n                });\n            }\n\t\t</script>\n\t\t<style type=\"text/css\">\n\t\t\t.manual_action_block { width:100%; padding:10px; position:relative }\n\t\t\t.manual_action_block .item_avatar {\n\t\t\t\twidth: 40px;\n\t\t\t\theight:40px;\n\t\t\t\tmargin:4px 10px 0 0;\n\t\t\t\tdisplay:inline-block;\n\t\t\t\tcursor:default;\n\t\t\t\tborder-radius:50%;\n\t\t\t\tbackground: #666666;\n\t\t\t\tline-height:40px;\n\t\t\t\ttext-align:center;\n\t\t\t}\n\t\t\t.manual_action_block .item_avatar span {\n\t\t\t\tbackground-image: url('";
echo __SITE_URL;
echo "/images/menu_icons.png') !important;\n\t\t\t\tbackground-repeat: no-repeat !important;\n\t\t\t\tbackground-size: 14px 140px !important;\n\t\t\t\tdisplay: inline-block;\n\t\t\t\twidth: 14px;\n\t\t\t\theight: 14px;\n\t\t\t\tmargin-top:13px;\n\t\t\t\tvertical-align: top;\n\t\t\t}\n\t\t\t.manual_action_block .item_avatar span.debtor,\n\t\t\t.manual_action_block .item_avatar span.creditor {\n\t\t\t\tbackground-position: 0px -56px;\n\t\t\t}\n\n\t\t\t.manual_action_block .item_avatar span.invoice,\n\t\t\t.manual_action_block .item_avatar span.payment_invoice,\n            .manual_action_block .item_avatar span.payment_purchase,\n\t\t\t.manual_action_block .item_avatar span.creditinvoice {\n\t\t\t\tbackground-position: 0px -14px;\n\t\t\t}\n\t\t\t.manual_action_block .item_avatar span.product {\n\t\t\t\tbackground-position: 0px -70px;\n\t\t\t}\n\t\t\t.manual_action_block .item_reference {\n\t\t\t\twidth: 250px; float:left;\n\t\t\t}\n\t\t\t.manual_action_block .item_reference a {\n\t\t\t\tcolor: #444444;text-decoration:none;\n\t\t\t}\n\t\t\t.manual_action_block .item_reference a:hover {\n\t\t\t\ttext-decoration:underline;\n\t\t\t}\n\t\t\t.manual_action_block .item_message {\n\t\t\t\tmargin:0 150px 0 300px\n\t\t\t}\n\t\t\t.manual_action_block .action_link {\n\t\t\t\tfloat:right;line-height:48px;width:200px;text-align:right;padding-right:15px;\n\t\t\t}\n\t\t\t.manual_action_block .ignore {\n\t\t\t\tposition: absolute;\n\t\t\t\ttop:0px;right:0px;\n\t\t\t\tbackground: #eaeaea;\n\t\t\t\tcolor:#aaa;\n\t\t\t\tline-height:20px;\n\t\t\t\twidth:22px;height:20px;\n\t\t\t\ttext-align:center;\n\t\t\t\tcursor:pointer;\n\t\t\t}\n\t\t\tdiv.export_group { float:left; width:auto; display:inline-block;min-height:186px;}\n\t\t\tdiv.export_block { width: 248px !important; min-height: 126px; }\n\t\t\tdiv.export_group:last-child div.export_block:last-child { margin-right:0px !important;}\n\n\t\t\t.export_block .export_info_block {\n\t\t\t\tmargin: 20px 20px 14px 20px;\n\t\t\t\theight: 60px;\n\t\t\t}\n\n\t\t\t.export_block .export_info_block .export_info_avatar{\n\t\t\t\twidth: 60px;\n\t\t\t\theight:60px;\n\t\t\t\tmargin:0 20px 0 0;\n\t\t\t\tdisplay:inline-block;\n\t\t\t\tcursor:default;\n\t\t\t\tborder-radius:50%;\n\n\t\t\t\tbackground: #aaa;\n\n\t\t\t\tcolor: #FFFFFF;\n\t\t\t\tline-height:60px;\n\t\t\t\ttext-align:center;\n\t\t\t\ttext-transform: uppercase;\n\t\t\t\ttext-decoration: none;\n\t\t\t\tletter-spacing: 2px;\n\t\t\t\tfont-weight: bold;\n\t\t\t\tfont-size:20px;\n\t\t\t}\n\t\t\t.export_block .export_info_block .export_info_avatar.green,\n\t\t\t.export_block .export_info_block .export_info_avatar.no_items{\n\t\t\t\ttext-align:left;\n\t\t\t}\n\t\t\t.export_block .export_info_block .export_info_avatar.green{\n\t\t\t\tbackground: #6F9A56;\n\t\t\t}\n\t\t\t.export_block .export_info_block .export_info_avatar.green:after,\n\t\t\t.export_block .export_info_block .export_info_avatar.no_items:after\n\t\t\t{\n\t\t\t\tcontent: \" \";\n\t\t\t\tbackground-image: url('";
echo __SITE_URL;
echo "/images/ico_check_large.png');\n\t\t\t\tbackground-size: 36px 30px;\n\t\t\t\tbackground-repeat:no-repeat;\n\t\t\t\tdisplay:inline-block;\n\t\t\t\tmargin: 15px 0 0 13px;\n\t\t\t\theight:30px;\n\t\t\t\twidth:36px;\n\t\t\t}\n\n\t\t\timg.export-loading-gif{width: 16px;}\n\n\t\t\t@media screen and (max-width: 1400px)\n\t\t\t{\n\t\t\t\t.manual_action_block .item_message {\n\t\t\t\t\tclear:both;padding-top:10px;margin-left:50px;\n\t\t\t\t}\n\t\t\t}\n\t\t</style>\n\n\t\t";
if(isset($show_stop_dialog) && $show_stop_dialog === true) {
    echo "\t\t\t<script type=\"text/javascript\">\n                \$(function(){\n                    openModal('";
    echo url_generator("exportaccounting", "exportaccounting_end", ["module" => $package]);
    echo "');\n                });\n\t\t\t</script>\n\t\t\t";
}
echo "\t\t<img src=\"";
echo __SITE_URL;
echo "/images/loadinfo.gif\" class=\"hide export-loading-gif\"/>\n\t\t<!--right-->\n\t</div>\n\t<!--right-->";

?>