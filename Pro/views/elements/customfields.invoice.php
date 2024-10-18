<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
function show_customfields_add($object, $debtor, $type = "invoice")
{
    if(!empty($object->customfields_list)) {
        echo "        <div class=\"customfields lineheight_input\" style=\"margin-left:40px;\">\n            <div class=\"field_row\">\n                <strong>";
        echo __("custom debtor fields h2");
        echo "</strong>\n            </div>\n\n            ";
        foreach ($object->customfields_list as $k => $custom_field) {
            if(isset($_POST["custom"][$custom_field["FieldCode"]])) {
                $custom_value = $custom_field["LabelType"] == "date" ? rewrite_date_site2db($_POST["custom"][$custom_field["FieldCode"]]) : esc($_POST["custom"][$custom_field["FieldCode"]]);
            } else {
                $custom_value = isset($object->customvalues[$custom_field["FieldCode"]]) ? $object->customvalues[$custom_field["FieldCode"]] : NULL;
            }
            echo "                <div class=\"field_row\">\n                    <span class=\"field_label\">";
            echo htmlspecialchars($custom_field["LabelTitle"]);
            echo ":</span>\n\t\t\t\t\t<span class=\"field_input\">\n\t\t\t\t\t\t";
            echo show_custom_input_field($custom_field, $custom_value);
            echo "\t\t\t\t\t</span>\n                </div>\n                ";
        }
        echo "        </div>\n        ";
    }
}

?>