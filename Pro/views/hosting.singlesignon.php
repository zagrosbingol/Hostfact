<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">\n<head>\n\t<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n\t<meta http-equiv=\"X-UA-Compatible\" content=\"IE=Edge\"/>\n\t<title>";
echo isset($wfh_page_title) && $wfh_page_title != "" ? $wfh_page_title . " - " : "";
echo __("software name");
echo "</title>\n\t<link rel=\"shortcut icon\" href=\"images/favicon.ico\" />\n\t<link type=\"text/css\" href=\"css/global.css?v=";
echo JSFILE_NOCACHE;
echo "\" rel=\"stylesheet\" />\n\n\t<script type=\"text/javascript\" src=\"js/translated_vars.php?v=";
echo JSFILE_NOCACHE;
echo "&amp;csrf_token=";
echo CSRF_Model::getToken(true);
echo "\"></script>\n\t<script type=\"text/javascript\" src=\"js/jquery-3.4.0.min.js\"></script>\n\t<script type=\"text/javascript\" src=\"js/jquery-ui-1.12.1.custom/jquery-ui.js\"></script>\n\t<script type=\"text/javascript\" src=\"js/global.js?v=";
echo JSFILE_NOCACHE;
echo "\"></script>\n\n</head>\n<body>\n\n<div style=\"text-align: center; margin-top: 40px;\">\n\t<h2>\n\t\t<img src=\"images/icon_circle_loader_grey.gif\" class=\"ico inline\" style=\"margin-bottom:-1px;\" />&nbsp;&nbsp;\n\t\t";
echo __("you are being redirected to control panel");
echo "\t</h2>\n</div>\n";
if(isset($hosting) && 0 < $hosting->id && 0 < $hosting->Server && in_array($hosting->Status, [4, 5])) {
    echo "\t<form action=\"\" method=\"POST\" name=\"form\" id=\"singlesignon_formpost\">\n\n\t</form>\n\n\t<script type=\"text/javascript\">\n\t\t\$(function()\n\t\t{\n\t\t\t\$.post('XMLRequest.php', { action: 'hosting_singlesignon', hosting_id: '";
    echo intval($_GET["id"]);
    echo "' },\n\t\t\t\tfunction(data)\n\t\t\t\t{\n\t\t\t\t\t//\$('#singlesignon_formpost').append(data); return; // DEBUG: use this to show singlesignon data when var dumping from integration\n\n\t\t\t\t\tif(data == undefined)\n\t\t\t\t\t{\n\t\t\t\t\t\t// failed\n\t\t\t\t\t\twindow.location = 'hosting.php?page=show&id=";
    echo intval($_GET["id"]);
    echo "';\n\t\t\t\t\t}\n\t\t\t\t\telse if(data.form_action != undefined)\n\t\t\t\t\t{\n\t\t\t\t\t\t// Form posts\n\t\t\t\t\t\t\$.each(data.data, function(input_name,input_value){\n\t\t\t\t\t\t\t\$('form#singlesignon_formpost').append('<input type=\"hidden\" name=\"'+input_name+'\" value=\"'+input_value+'\" />');\n\t\t\t\t\t\t});\n\n\t\t\t\t\t\t\$('form#singlesignon_formpost').attr('action', data.form_action);\n\t\t\t\t\t\t\$('form#singlesignon_formpost').submit();\n\t\t\t\t\t}\n\t\t\t\t\telse if(data.url != undefined && data.url != '')\n\t\t\t\t\t{\n\t\t\t\t\t\twindow.location = data.url;\n\t\t\t\t\t}\n\t\t\t\t\telse\n\t\t\t\t\t{\n\t\t\t\t\t\t// failed\n\t\t\t\t\t\twindow.location = 'hosting.php?page=show&id=";
    echo intval($_GET["id"]);
    echo "';\n\t\t\t\t\t}\n\t\t\t\t}, \"json\"\n\t\t\t)\n\t\t\t.fail(function(data) {\n\t\t\t\t//\$('#singlesignon_formpost').append(data.responseText); return;  // DEBUG: use this to show singlesignon data when var dumping from integration\n\t\t\t\twindow.location = 'hosting.php?page=show&id=";
    echo intval($_GET["id"]);
    echo "';\n\t\t\t});\n\t\t});\n\t</script>\n\t";
}
echo "\n</body>\n</html>\n";

?>