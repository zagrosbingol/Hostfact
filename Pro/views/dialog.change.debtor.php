<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
global $debtor;
echo "\n<strong>";
echo __("transfer services");
echo ":</strong><br />\n\n<span class=\"inlineblock width1 lineheight2\">\n    ";
echo __("current debtor");
echo ": \n</span>\n<span id=\"current_debtor\"></span>\n<br />\n\n<label class=\"lineheight2\">\n    <span class=\"inlineblock width1\">\n        ";
echo __("to debtor");
echo "    </span>\n</label>\n<input type=\"hidden\" name=\"Debtor\" value=\"\" />\n";
createAutoComplete("debtor", "Debtor", "", ["class" => "size12"]);
echo "<br /><br />\n\n<label>\n    <input type=\"checkbox\" name=\"imsure\" value=\"yes\" /> \n    ";
echo __("tranfer services to debtor confirm");
echo "</label>";

?>