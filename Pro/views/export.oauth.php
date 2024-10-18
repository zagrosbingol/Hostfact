<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "<!--right-->\n<div class=\"content accounting_export";
if(isset($export->largeOAuthSettings) && $export->largeOAuthSettings) {
    echo " accountancy_export_large";
}
echo "\">\n\t<!--right-->\n\n\t";
echo $message_box;
echo "\n\t";
if(isset($office_name) && $office_name) {
    echo "<div class=\"float_right\" style=\"margin-top: 22px;\"><a data-href=\"" . url_generator("exportaccounting", "exportaccounting_disconnect_oauth", ["module" => $package]) . "\" class=\"modal_link\">" . __("export accounting - disconnect") . "</a></div>";
    echo "<h1 class=\"heading_3 margin_bottom_40px\">" . htmlspecialchars($package_information["name"]) . " instellingen: " . htmlspecialchars($office_name) . "</h1>";
} else {
    if($export->configuredOAuth) {
        echo "<div class=\"float_right\"><a data-href=\"" . url_generator("exportaccounting", "exportaccounting_disconnect_oauth", ["module" => $package]) . "\" class=\"modal_link\">" . __("export accounting - disconnect") . "</a></div>";
    }
    echo "<h1 class=\"heading_3 margin_bottom_40px\">" . htmlspecialchars($package_information["name"]) . " instellingen</h1>";
}
$show_step_2 = $export->showOAuthStep1();
if($show_step_2) {
    $show_step_3 = $export->showOAuthStep2();
    if($show_step_3) {
        $export->showOAuthStep3();
    }
}
echo "\t<script type=\"text/javascript\">\n        \$(document).ready(function() {\n\n            \$('select[name^=\"accounts[\"], select[name^=\"taxrules[\"]').each(function(index,element){\n\n                if(\$(element).val() == '')\n                {\n                    \$(element).addClass('select_highlight');\n                }\n            });\n\n            \$('select[name^=\"accounts[\"], select[name^=\"taxrules[\"]').change(function(){\n                if(\$(this).val() == '')\n                {\n                    \$(this).addClass('select_highlight');\n                }\n                else\n                {\n                    \$(this).removeClass('select_highlight');\n                }\n            });\n\n            // Get settings\n            \$('#oauth_save_officecode').click(function(){\n                \$('form[name=oauth_officecode]').submit();\n            });\n\n            // Handle custom groups\n            var customAccounts = 0;\n            \$('div[id=\"customLedgerTable\"], div[id=\"purchaseLedgerTable\"]').hide();\n\n            \$('input[name=\"toggleCustomLedgerTable\"]').change(function() {\n                \$('#customLedgerTable').toggle();\n            });\n            \$('input[name=\"togglePurchaseLedgerTable\"]').change(function() {\n                \$('#purchaseLedgerTable').toggle();\n            });\n\n            \$('input[name^=\"group\"], select[name^=\"group\"]').each(function(i, v) {\n                if(\$(this).val() > 0)\n                {\n                    \$('input[name=\"toggleCustomLedgerTable\"]').prop('checked', true);\n                    \$('#customLedgerTable').show();\n                }\n            });\n\n            \$('input[name^=\"purchase_group\"], select[name^=\"purchase_group\"]').each(function(i, v) {\n                if(\$(this).val() > 0)\n                {\n                    \$('input[name=\"togglePurchaseLedgerTable\"]').prop('checked', true);\n                    \$('#purchaseLedgerTable').show();\n                }\n            });\n\n            // Toggle time field\n            \$('input[name=\"automation[data][]\"][value=\"payment_invoice\"]').change(function(){\n                if(\$(this).prop('checked'))\n                {\n                    \$('span[data-toggler=\"payment_invoice_time\"]').removeClass('hide');\n                }\n                else\n                {\n                    \$('span[data-toggler=\"payment_invoice_time\"]').addClass('hide');\n                }\n            });\n            // Time field checker\n            \$('input[name^=\"automation[time]\"]').change(function(){\n                var Time = \$(this).val();\n                if(!Time.match(/^([01]?[0-9]|2[0-3]):[0-5][0-9]?/))\n                {\n                    \$(this).val(\$(this).data('default-time'));\n                }\n            });\n        });\n\t</script>\n\t<style type=\"text/css\">\n\t\t@media screen and (max-width: 1560px) {\n\t\t\tselect.width_310px, input.width_310px, label.width_310px { width:230px; }\n\t\t}\n\t</style>\n\t";
if(isset($show_stop_dialog) && $show_stop_dialog === true) {
    echo "\t\t<script type=\"text/javascript\">\n            \$(function(){\n                openModal('";
    echo url_generator("exportaccounting", "exportaccounting_end", ["module" => $package]);
    echo "');\n            });\n\t\t</script>\n\t\t";
}
echo "\n\t<div class=\"clearfix\"></div>\n\t<!--right-->\n</div>\n<!--right-->";

?>