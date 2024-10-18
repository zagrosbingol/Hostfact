<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "views/header.php";
echo "\n";
echo $message;
echo "\n<!--heading1-->\n<div class=\"heading1\">\n<!--heading1-->\n\n\t<h2>";
echo __("handle overview");
echo "</h2> \n\n\t<p class=\"pos2\"><strong class=\"textsize1\">";
echo __("total");
echo ": <span>";
echo isset($list_domain_handles["CountRows"]) ? $list_domain_handles["CountRows"] : "0";
echo "</span></strong></p>\n\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\t\n\t";
if(U_SERVICEMANAGEMENT_ADD) {
    echo "\t<!--optionsbar-->\n\t<div class=\"optionsbar\">\n\t<!--optionsbar-->\n\t\t\n\t\t<p class=\"pos1\"><a class=\"button1 add_icon\" href=\"handles.php?page=add\"><span>";
    echo __("new handle");
    echo "</span></a></p>\n\n\t\t<p class=\"pos2\">\n\t\t\t<a class=\"sizenormal c1 a1 pointer\" onclick=\"\$('#dialog_cleanup_handles').dialog('open');\">\n\t\t\t\t";
    echo __("cleanup unused handles link");
    echo "\t\t\t</a>\n\t\t</p>\n\n\t<!--optionsbar-->\n\t</div>\n\t<!--optionsbar-->\n\t";
}
echo "\t\n\t";
require_once "views/elements/handle.table.php";
$options = ["table_type" => "maintable", "redirect_page" => "", "redirect_id" => "", "session_name" => "handle.overview", "current_page" => $current_page, "current_page_url" => $current_page_url];
show_handle_table($list_domain_handles, $options);
echo "\n";
if(U_SERVICEMANAGEMENT_EDIT) {
    echo "\t\t<div id=\"dialog_cleanup_handles\" class=\"hide\" title=\"";
    echo __("dialog clean up handles title");
    echo "\">\n\t\t\t<form name=\"cleanup_handles\" method=\"post\" action=\"handles.php\">\n                <input type=\"hidden\" name=\"action\" value=\"cleanup\" />\n\t\t\t\t";
    echo __("dialog clean up handles description");
    echo "\t\t\t\t<br /><br />\n\n                <label><input type=\"checkbox\" name=\"imsure\" value=\"yes\"/> ";
    echo __("dialog clean up contacts confirm");
    echo "</label><br />\n\t\t\t\t<br />\n\n\t\t\t\t<p>\n                    <a class=\"button2 alt1 float_left\" id=\"dialog_cleanup_handles_btn\"><span>";
    echo __("proceed");
    echo "</span></a>\n                    <span class=\"lineheight3 loader float_left hide\">\n                        <img src=\"images/icon_circle_loader_green.gif\" class=\"ico inline\" style=\"margin-bottom:-3px;\" />&nbsp;&nbsp;\n                        <span class=\"loading_green\">";
    echo __("loading");
    echo "</span>\n                    </span>\n                </p>\n\n\n\t\t\t\t<p><a class=\"a1 c1 alt1 float_right lineheight3\" onclick=\"\$('#dialog_cleanup_handles').dialog('close');\"><span>";
    echo __("cancel");
    echo "</span></a></p>\n\t\t\t</form>\n\t\t</div>\n\t\t";
}
echo "\n";
require_once "views/footer.php";

?>