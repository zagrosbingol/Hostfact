<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "<!--heading1-->\n<div class=\"heading1\">\n<!--heading1-->\n\n\t<h2>";
echo sprintf(__("export oauth - step 1 connect with"), $package_information["name"]);
echo "</h2>\n\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n\n";
$show_step_2 = $export->showOAuthStep1();
if($show_step_2) {
    echo "\t<br /><br />\n\t<!--heading1-->\n\t<div class=\"heading1\">\n\t<!--heading1-->\n\t\n\t\t<h2>";
    echo __("export oauth - step 2 choose environment");
    echo "</h2>\n\t\n\t<!--heading1-->\n\t</div><hr />\n\t<!--heading1-->\n\t";
    $show_step_3 = $export->showOAuthStep2();
    if($show_step_3) {
        echo "\t\t<br /><br />\n\t\t<!--heading1-->\n\t\t<div class=\"heading1\">\n\t\t<!--heading1-->\n\t\t\n\t\t\t<h2>";
        echo __("export oauth - step 3 check settings");
        echo "</h2>\n\t\t\n\t\t<!--heading1-->\n\t\t</div><hr />\n\t\t<!--heading1-->\n\t\t";
        $export->showOAuthStep3();
    }
}
echo "<script type=\"text/javascript\">\n\$(document).ready(function() {\n\n    \n    \$('#oauth_save_officecode').click(function(){\n    \t\$(this).hide();\n\t\t\$(this).next('.loading_span').show();\n\t\t\$('form[name=oauth_officecode]').submit();\t\n    });\n    \n    \n});\n</script>";

?>