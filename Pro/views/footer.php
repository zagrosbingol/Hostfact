<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "</div>\n\n</div>\n\n<div id=\"batch_confirm\" class=\"hide\" title=\"";
echo __("confirm action");
echo "\">\n\n\t<p id=\"batch_confirm_text\"></p>\n\t\n\t<p><a class=\"button1 alt1 float_left\" id=\"batch_confirm_submit\" onclick=\" \$(this).hide(); \$('#batch_all_loader_download').show();\"><span>";
echo __("proceed");
echo "</span></a>\n\t<span id=\"batch_all_loader_download\" class=\"hide\">\n\t\t<img src=\"images/icon_circle_loader_green.gif\" class=\"ico inline\" style=\"margin-bottom:-3px;\" />&nbsp;&nbsp;\n\t\t<span class=\"loading_green\">";
echo __("loading");
echo "</span>\n\t</span>\n\t\n\t<a class=\"a1 c1 float_right\" id=\"batch_confirm_cancel\">";
echo __("cancel");
echo "</a></p>\n</div>\n\n<div class=\"variables\"></div>\n\n<div id=\"dialog\" class=\"hide\"><center><br /><img src=\"images/loadinfo.gif\" alt=\"\"/></center></div>\n\n<div id=\"tooltip_div\" class=\"hide\"></div>\n\n<div class=\"profile_submenu hide\">\n\t\n\t<div class=\"user_note\">\n\t\t<p><strong>";
echo __("personal note");
echo "</strong></p>\n\t\t<input type=\"hidden\" name=\"action\" value=\"Notes\" />\n\t\t<textarea name=\"Notes\" style=\"width: 400px;\">";
echo $account->Notes;
echo "</textarea>\n\t\t\n\t\t<br clear=\"all\" />\n\t\t\n\t\t<a id=\"save_note\" class=\"button1 alt1 pointer floatl\"><span>";
echo __("save");
echo "</span></a>\n\t\t\n\t\t<span id=\"notes_saved_message\" class=\"loading_green floatl hide\">";
echo __("notes saved");
echo "</span>\n\t</div>\n\t\n\t<div class=\"profile_links\">\n\t\t<p>\n\t\t\t<a class=\"a1 c1\" href=\"company.php?page=accountshow&id=";
echo $account->Identifier;
echo "\">";
echo __("my profile");
echo "</a>\n\t\t</p>\n\t\t";
if(U_AGENDA_SHOW) {
    echo "\t\t\t\t<p>\n\t\t\t\t\t<a class=\"a1 c1\" href=\"agenda.php\">";
    echo __("agenda");
    echo "</a>\n\t\t\t\t</p>\n\t\t\t\t";
}
echo "\t</div>\n\t\n</div>\n\n";
if(isset($_SESSION["force_download"]) && $_SESSION["force_download"]) {
    echo "<a id=\"force_download\" href=\"download.php\" style=\"display:none;\">download</a>\n";
}
echo "\n\n<div id=\"templatelocation\" class=\"hide\" title=\"";
echo __("dialog template design title");
echo "\">\n\t<form name=\"form_templatelocation\" method=\"post\" action=\"\">\n\t\n\t<strong>";
echo __("dialog template design title");
echo "</strong><br />\n\t<input type=\"radio\" id=\"dialog_printtype_download\" name=\"printtype\" value=\"download\" checked=\"checked\"/> <label for=\"dialog_printtype_download\">";
echo __("dialog template design option1");
echo "</label><br />\n\t<input type=\"radio\" id=\"dialog_printtype_print\" name=\"printtype\" value=\"print\"/> <label for=\"dialog_printtype_print\">";
echo __("dialog template design option2");
echo "</label><br />\t\t\t\t\t\n\t<br />\n\t<p><a class=\"button1 alt1 float_left\" id=\"templatelocation_btn\"><span>";
echo __("dialog template design process");
echo "</span></a></p>\n\t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#templatelocation').dialog('close');\">";
echo __("cancel");
echo "</a></p>\n\t</form>\n</div>\n\n<div id=\"knowledgebase\">\n\t<div id=\"kb_open_btn\">\n\tHelp\n\t</div>\n\t\n\t<div id=\"kb_content\" class=\"hide\">\n\t\t<div id=\"kb_close_btn\">\n\t\t\tHelp\n\t\t</div>\n\t\t\t\t\n\t\t\t<strong class=\"kb_title\">";
echo __("integrated knowledgebase");
echo "</strong>\n\t\t\n\t\t\t<div id=\"kb_search_div\">\n\t\t\t\t<input type=\"text\" name=\"kb_search\" />\n\t\t\t\t<input type=\"hidden\" name=\"kb_page1\" value=\"";
echo htmlspecialchars(substr($_SERVER["PHP_SELF"], strrpos($_SERVER["PHP_SELF"], "/") + 1));
echo "\"/>\n\t\t\t\t<input type=\"hidden\" name=\"kb_page2\" value=\"";
echo isset($_GET["page"]) ? htmlspecialchars(esc($_GET["page"])) : "";
echo "\"/>\n\t\t\t</div>\n\t\t\t\n\t\t\t<hr />\n\t\t\t\n\t\t\t<script type=\"text/javascript\" src=\"js/jquery.tinyscrollbar.min.js\"></script>\n\t\t\t<div id=\"kb_scroll\">\n\t\t\t\t<div class=\"scrollbar disable\"><div class=\"track\"><div class=\"thumb\"><div class=\"end\"></div></div></div></div>\n\t\t\t\t<div class=\"viewport\">\n\t\t\t\t\t<div id=\"kb_results\" class=\"overview\"></div>\n\t\t\t\t</div>\t\n\t\t\t</div>\n\t\t\t<div id=\"kb_scroll2\">\n\t\t\t\t<div class=\"scrollbar disable\"><div class=\"track\"><div class=\"thumb\"><div class=\"end\"></div></div></div></div>\n\t\t\t\t<div class=\"viewport\">\n\t\t\t\t\t<div id=\"kb_article\" class=\"overview\"></div>\n\t\t\t\t</div>\t\n\t\t\t</div>\n\t\t\n\t</div>\n</div>\n</body>\n</html>";

?>