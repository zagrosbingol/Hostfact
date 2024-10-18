<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "views/header.php";
echo "\n<link href=\"js/spectrum/spectrum.css\" rel=\"stylesheet\" />\n<script src=\"js/spectrum/spectrum.js\" type=\"text/javascript\"></script>\n\n";
echo isset($message) ? $message : "";
echo "\n<!--heading1-->\n<div class=\"heading1\">\n<!--heading1-->\n\n\t<h2>";
echo __("orderforms");
echo "</h2>\n\t\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n";
if(defined("ORDERFORM_ENABLED")) {
    echo "<!--optionsbar-->\n<div class=\"optionsbar\">\n<!--optionsbar-->\n\t<p class=\"pos2\"><a href=\"orderform.php?page=old\" class=\"a1 c1\">";
    echo __("orderform settings old form");
    echo "</a></p>\n<!--optionsbar-->\n</div>\n<!--optionsbar-->\n";
}
echo "\n<!--box1-->\n<div class=\"box2\" id=\"tabs\">\n<!--box1-->\n\n\t<!--top-->\n\t<div class=\"top\">\n\t<!--top-->\n\t\n\t\t<ul class=\"list3\">\n\t\t\t<li class=\"on\"><a href=\"#tab-general\">";
echo __("orderforms");
echo "</a></li>\n\t\t\t<li><a href=\"#tab-settings\">";
echo __("general settings");
echo "</a></li>\n\t\t\t<li><a href=\"#tab-wizard\">";
echo __("whois integration in website");
echo "</a></li>\n\t\t</ul>\n\t\t\n\t<!--top-->\n\t</div>\n\t<!--top-->\n\n\t<!--content-->\n\t<div class=\"content\" id=\"tab-general\">\n\t<!--content-->\n\n\t\t";
if(U_ORDERFORM_EDIT) {
    echo "\t\t<br /><p class=\"pos1\"><a class=\"button1 add_icon\" href=\"orderform.php?page=add\"><span>";
    echo __("create orderform");
    echo "</span></a></p>\n\t\t";
}
echo "\t\t<br />\n\t\t";
$has_default_orderform = false;
if(DEFAULT_ORDERFORM) {
    echo "\t\t\t<strong>";
    echo __("default orderform");
    echo "</strong><br />\n\t\t\t";
    foreach ($orderform_list["Available"] as $tmp_orderform) {
        if(DEFAULT_ORDERFORM != $tmp_orderform["id"]) {
        } else {
            $has_default_orderform = true;
            echo "\t\t\t\t<div class=\"setting_box\" style=\"border-color: #ccc;\">\n\t\t\t\t\t<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td width=\"275\">\n\t\t\t\t\t\t\t\t<strong>";
            echo $tmp_orderform["Title"];
            echo "</strong>\n\t\t\t\t\t\t\t\t<br/>\n\t\t\t\t\t\t\t\t";
            echo array_key_exists($tmp_orderform["Type"], $additional_product_types) ? __("orderform type " . $tmp_orderform["Type"], $tmp_orderform["Type"]) : __("orderform type " . $tmp_orderform["Type"]);
            echo "\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t<td>";
            echo __("orderform url");
            echo "\t\t\t\t\t\t\t\t<br/>\n\t\t\t\t\t\t\t\t<a href=\"";
            echo ORDERFORM_URL;
            echo "?cart=";
            echo $tmp_orderform["id"];
            echo "\" class=\"a1 c1\" target=\"_blank\">";
            echo ORDERFORM_URL;
            echo "?cart=";
            echo $tmp_orderform["id"];
            echo "</a>\n\t\t\t\t\t\t\t</td>\n\n\t\t\t\t\t\t\t";
            if(U_ORDERFORM_EDIT || U_ORDERFORM_DELETE) {
                echo "\t\t\t\t\t\t\t\t<td width=\"100\" align=\"center\">\n\t\t\t\t\t\t\t\t\t";
                if(U_ORDERFORM_EDIT) {
                    echo "\t\t\t\t\t\t\t\t\t\t<p>\n\t\t\t\t\t\t\t\t\t\t<a href=\"?page=edit&amp;id=";
                    echo $tmp_orderform["id"];
                    echo "\" class=\"button1 alt1\">\n\t\t\t\t\t\t\t\t\t\t\t<span>";
                    echo __("edit");
                    echo "</span>\n\t\t\t\t\t\t\t\t\t\t</a></p>";
                }
                echo "\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t";
            }
            echo "\t\t\t\t\t\t</tr>\n\t\t\t\t\t</table>\n\t\t\t\t</div>\n\t\t\t\t";
        }
    }
    echo "<br /><br />";
}
if($has_default_orderform === false && !empty($orderform_list["Available"]) || $has_default_orderform === true && 1 < count($orderform_list["Available"])) {
    echo "\t\t\t<strong>";
    echo __("active orderforms");
    echo "</strong><br />\n\t\t\t";
    foreach ($orderform_list["Available"] as $tmp_orderform) {
        if(DEFAULT_ORDERFORM == $tmp_orderform["id"]) {
        } else {
            echo "\t\t\t\t<div class=\"setting_box\" style=\"border-color: #ccc;\">\n\t\t\t\t\t<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">\n\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td width=\"275\">\n\t\t\t\t\t\t\t<strong>";
            echo $tmp_orderform["Title"];
            echo "</strong><br />\n\t\t\t\t\t\t\t";
            echo array_key_exists($tmp_orderform["Type"], $additional_product_types) ? __("orderform type " . $tmp_orderform["Type"], $tmp_orderform["Type"]) : __("orderform type " . $tmp_orderform["Type"]);
            echo "\t\t\t\t\t\t</td>\n\t\t\t\t\t\t<td>";
            echo __("orderform url");
            echo "<br /><a href=\"";
            echo ORDERFORM_URL;
            echo "?cart=";
            echo $tmp_orderform["id"];
            echo "\" class=\"a1 c1\" target=\"_blank\">";
            echo ORDERFORM_URL;
            echo "?cart=";
            echo $tmp_orderform["id"];
            echo "</a></td>\n\n\t\t\t\t\t\t";
            if(U_ORDERFORM_EDIT || U_ORDERFORM_DELETE) {
                echo "\t\t\t\t\t\t<td width=\"100\" align=\"center\">\n\t\t\t\t\t\t\t";
                if(U_ORDERFORM_EDIT) {
                    echo "<p><a href=\"?page=edit&amp;id=";
                    echo $tmp_orderform["id"];
                    echo "\" class=\"button1 alt1\"><span>";
                    echo __("edit");
                    echo "</span></a></p>";
                }
                echo "\t\t\t\t\t\t\t";
                if(U_ORDERFORM_DELETE) {
                    echo "<p><a onclick=\"deleteOrderForm('";
                    echo $tmp_orderform["id"];
                    echo "')\" class=\"a1 c1 pointer\">";
                    echo __("delete");
                    echo "</a></p>";
                }
                echo "\t\t\t\t\t\t</td>\n\t\t\t\t\t\t";
            }
            echo "\t\t\t\t\t</tr>\n\t\t\t\t\t</table>\n\t\t\t\t</div>\n\t\t\t\t";
        }
    }
    echo "\t\t\t<br /><br />\n\t\t";
}
echo "\t\t\n\t\t\n\t\t";
if(!empty($orderform_list["Unavailable"])) {
    echo "\t\t\t<strong>";
    echo __("inactive orderforms");
    echo "</strong><br />\n\t\t\n\t\t\t";
    foreach ($orderform_list["Unavailable"] as $tmp_orderform) {
        echo "\t\t\t<div class=\"setting_box\">\n\t\t\t\t<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">\n\t\t\t\t<tr>\n\t\t\t\t\t<td width=\"275\">\n\t\t\t\t\t\t<strong>";
        echo $tmp_orderform["Title"];
        echo "</strong><br />\n\t\t\t\t\t\t";
        echo __("orderform type " . $tmp_orderform["Type"]);
        echo "\t\t\t\t\t</td>\n\t\t\t\t\t<td>";
        echo __("orderform url");
        echo "<br /><a href=\"";
        echo ORDERFORM_URL;
        echo "?cart=";
        echo $tmp_orderform["id"];
        echo "\" class=\"a1 c1\" target=\"_blank\">";
        echo ORDERFORM_URL;
        echo "?cart=";
        echo $tmp_orderform["id"];
        echo "</a></td>\n\t\t\t\t\t\n\t\t\t\t\t";
        if(U_ORDERFORM_EDIT || U_ORDERFORM_DELETE) {
            echo "\t\t\t\t\t<td width=\"100\" align=\"center\">\n\t\t\t\t\t\t";
            if(U_ORDERFORM_EDIT) {
                echo "<p><a href=\"?page=edit&amp;id=";
                echo $tmp_orderform["id"];
                echo "\" class=\"button1 alt1\"><span>";
                echo __("edit");
                echo "</span></a></p>";
            }
            echo "\t\t\t\t\t\t";
            if(U_ORDERFORM_DELETE) {
                echo "<p><a onclick=\"deleteOrderForm('";
                echo $tmp_orderform["id"];
                echo "')\" class=\"a1 c1 pointer\">";
                echo __("delete");
                echo "</a></p>";
            }
            echo "\t\t\t\t\t</td>\n\t\t\t\t\t";
        }
        echo "\t\t\t\t</tr>\n\t\t\t\t</table>\n\t\t\t</div>\n\t\t\t";
    }
    echo "\t\t";
}
echo "\t\t\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\t\n\t<!--content-->\n\t<div class=\"content\" id=\"tab-settings\">\n\t<!--content-->\n\n\t\t<!--form-->\n\t\t<form id=\"OrderForm\" name=\"form_create\" method=\"post\" action=\"orderform.php?page=settings\"><fieldset><legend>";
echo __("general settings");
echo "</legend>\n\t\t<!--form-->\n\n\t\t<!--split2-->\n\t\t<div class=\"split2\">\n\t\t<!--split2-->\n\t\t\n\t\t\t<!--left-->\n\t\t\t<div class=\"left\">\n\t\t\t<!--left-->\n\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
echo __("general settings");
echo "</h3><div class=\"content\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t\t<strong class=\"title\">";
echo __("companyname");
echo "</strong>\n\t\t\t\t\t<input type=\"text\" name=\"COMPANY_NAME\" class=\"text1 size1\" value=\"";
echo COMPANY_NAME;
echo "\" />\n\t\t\t\t\t<br /><br />\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title\">";
echo __("general email");
echo "</strong>\n\t\t\t\t\t<input type=\"text\" name=\"COMPANY_EMAIL\" class=\"text1 size1\" value=\"";
echo COMPANY_EMAIL;
echo "\" />\n\t\t\t\t\t<br /><br />\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title\">";
echo __("url terms");
echo "</strong>\n\t\t\t\t\t<input type=\"text\" name=\"COMPANY_AV_PDF\" class=\"text1 size1\" value=\"";
echo COMPANY_AV_PDF;
echo "\"/> <span id=\"orderform-terms-url-img\" class=\"loading_float\"></span>\n\t\t\t\t\t<br /><br />\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title\">";
echo __("default orderform");
echo "</strong>\n\t\t\t\t\t<select name=\"DEFAULT_ORDERFORM\" class=\"text1 size4f\">\n\t\t\t\t\t\t<option value=\"\">";
echo __("no default orderform selected");
echo "</option>\n\t\t\t\t\t\t";
if(!empty($orderform_list["Available"])) {
    echo "\t\t\t\t\t\t\t";
    foreach ($orderform_list["Available"] as $tmp_orderform) {
        echo "\t\t\t\t\t\t\t<option value=\"";
        echo $tmp_orderform["id"];
        echo "\" ";
        if($tmp_orderform["id"] == DEFAULT_ORDERFORM) {
            echo "selected=\"selected\"";
        }
        echo ">";
        echo $tmp_orderform["Title"];
        echo "</option>\n\t\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t";
}
echo "\t\t\t\t\t</select>\n\t\t\t\t\t<br /><br />\n\t\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t<!--left-->\n\t\t\t</div>\n\t\t\t<!--left-->\n\n\t\t\t<!--right-->\n\t\t\t<div class=\"right\">\n\t\t\t<!--right-->\n\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t<div class=\"box3\"><h3>";
echo __("preferences");
echo "</h3><div class=\"content\">\n\t\t\t\t<!--box3-->\n\t\t\t\t\n\t\t\t\t\t<strong class=\"title\">";
echo __("send confirmationmail");
echo "</strong>\n\t\t\t\t\t<select name=\"ORDERMAIL_SENT\" class=\"text1 size4f\">\n\t\t\t\t\t\t<option value=\"yes\" ";
if("yes" == ORDERMAIL_SENT) {
    echo "selected=\"selected\"";
}
echo ">";
echo __("do send confirmationmail");
echo "</option>\n\t\t\t\t\t\t<option value=\"no\" ";
if("no" == ORDERMAIL_SENT) {
    echo "selected=\"selected\"";
}
echo ">";
echo __("do not send confirmationmail");
echo "</option>\n\t\t\t\t\t</select>\n\t\t\t\t\t<br /><br />\n\t\t\t\t\t\n\t\t\t\t\t<div id=\"ordermail_sent_div\" ";
if("no" == ORDERMAIL_SENT) {
    echo "class=\"hide\"";
}
echo ">\n\t\t\t\t\t\t<strong class=\"title\">";
echo __("email copy confirmationmail");
echo "</strong>\n\t\t\t\t\t\t<input type=\"text\" name=\"ORDERMAIL_SENT_BCC\" class=\"text1 size1\" value=\"";
echo check_email_address(ORDERMAIL_SENT_BCC, "convert", ", ");
echo "\" />\n\t\t\t\t\t\t<br /><br />\n\t\t\t\t\t</div>\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title\">";
echo __("orderform colorcode");
echo "</strong>\n\t\t\t\t\t<input type=\"text\" name=\"ORDERFORM_CSS_COLOR\" class=\"text1 size6 colorpicker\" value=\"";
echo ORDERFORM_CSS_COLOR;
echo "\" />\n\t\t\t\t\t<br /><br />\n\t\t\t\t\t\n\t\t\t\t\t<strong class=\"title\">";
echo __("orderform domain auth required");
echo "</strong>\n\t\t\t\t\t<select name=\"DOMAIN_AUTH_KEY_REQUIRED\" class=\"text1 size4f\">\n\t\t\t\t\t\t<option value=\"yes\" ";
if("yes" == DOMAIN_AUTH_KEY_REQUIRED) {
    echo "selected=\"selected\"";
}
echo "> ";
echo __("orderform domain auth required yes");
echo "</option>\n\t\t\t\t\t\t<option value=\"no\" ";
if("no" == DOMAIN_AUTH_KEY_REQUIRED) {
    echo "selected=\"selected\"";
}
echo "> ";
echo __("orderform domain auth required no");
echo "</option>\n\t\t\t\t\t</select>\n\t\t\t\t\t<br /><br />\n\t\t\t\t\t\n\t\t\t\t<!--box3-->\n\t\t\t\t</div></div>\n\t\t\t\t<!--box3-->\n\t\n\t\t\t<!--right-->\n\t\t\t</div>\n\t\t\t<!--right-->\n\t\t\t\n\t\t<!--split2-->\n\t\t</div>\n\t\t<!--split2-->\n\t\t";
if(U_ORDERFORM_EDIT) {
    echo "\t\t<p class=\"align_right\">\n\t\t\t<a class=\"button1 alt1\" id=\"form_create_btn\">\n                <span>";
    echo __("btn edit");
    echo "</span>\n            </a>\n\t\t</p>\n\t\t";
}
echo "\t\t\n\t\t<!--form-->\n\t\t</fieldset></form>\n\t\t<!--form-->\n\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\t\n\t<!--content-->\n\t<div class=\"content tld_wizard\" id=\"tab-wizard\">\n\t<!--content-->\n\t\t<div id=\"WHOISForm\">\n\t\t\n\t\t";
if((int) $available_orderforms === 0) {
    echo "\t\t\t<div class=\"setting_help_box\">\n\t\t\t\t<strong>";
    echo __("no orderforms available");
    echo "</strong><br />\n\t\t\t\t";
    echo sprintf(__("you should add an available orderform first"), "<a href=\"orderform.php?page=add\" class=\"a1 c1\">" . __("add orderform") . "</a>");
    echo " \n\t\t\t</div>\n\t\t\t";
} else {
    $orderform_id = is_null($orderform_id) ? DEFAULT_ORDERFORM : $orderform_id;
    echo "\t\t\t<!--box3-->\n\t\t\t<div class=\"box3\"><h3>";
    echo __("for which orderform you want a whois check");
    echo "</h3><div class=\"content lineheight2\">\n\t\t\t<!--box3-->\n\t\t\t\t<strong class=\"title\">";
    echo __("orderform");
    echo "</strong>\n\t\t\t\t<select name=\"OrderForm\" class=\"text1 size4f\">\n\t\t\t\t\t";
    if(!empty($orderform_list["Available"])) {
        echo "\t\t\t\t\t\t";
        foreach ($orderform_list["Available"] as $tmp_orderform) {
            if($tmp_orderform["Type"] == "domain" || $tmp_orderform["Type"] == "hosting") {
                echo "\t\t\t\t\t\t<option value=\"";
                echo $tmp_orderform["id"];
                echo "\" ";
                if($tmp_orderform["id"] == $orderform_id) {
                    echo "selected=\"selected\"";
                }
                echo ">";
                echo $tmp_orderform["Title"];
                echo "</option>\n\t\t\t\t\t\t";
            }
            echo "\t\t\t\t\t";
        }
    }
    echo "\t\t\t\t</select>\n\t\t\t\t<br /><br />\n\t\t\t\t\n\t\t\t\t<strong>";
    echo __("whois wizard possibilities");
    echo "</strong><br />\n\t\t\t\t<label><input type=\"checkbox\" name=\"extern\" value=\"yes\" /> ";
    echo __("whois wizard external form");
    echo "</label><br />\n\t\t\t\t";
    if(!isset($orderform->OtherSettings->domain->ResultURL) || !$orderform->OtherSettings->domain->ResultURL) {
        echo "<label><input type=\"checkbox\" name=\"inlineresult\" value=\"yes\" /> ";
        echo __("whois wizard results on same page");
        echo "</label><br />";
    }
    echo "\t\t\t\n\t\t\t<!--box3-->\n\t\t\t</div></div>\n\t\t\t<!--box3-->\n\t\t\t\n\t\t\t<!--box3-->\n\t\t\t<div class=\"box3\"><h3>";
    echo __("whois domain form");
    echo "</h3><div class=\"content lineheight2\">\n\t\t\t<!--box3-->\n\t\t\t\n\t\t\t\t<strong>";
    echo __("whois wizard generated code");
    echo "</strong><br />\n\t\t\t\t";
    echo __("whois wizard generated code copy");
    echo "<br />\n\t\t\t\t<textarea id=\"whois_wizard_form_1\" class=\"text1\">&lt;script type=&quot;text/javascript&quot; src=&quot;";
    echo ORDERFORM_URL;
    echo "whois.php?";
    if($orderform_id != DEFAULT_ORDERFORM) {
        echo "cart=" . $orderform_id . "&amp;";
    }
    echo "display=form&quot;&gt;&lt;/script&gt;</textarea>\n\t\t\t\t<textarea id=\"whois_wizard_form_2\" class=\"text1 hide\">&lt;script type=&quot;text/javascript&quot; src=&quot;";
    echo ORDERFORM_URL;
    echo "whois.php?";
    if($orderform_id != DEFAULT_ORDERFORM) {
        echo "cart=" . $orderform_id . "&amp;";
    }
    echo "display=form&amp;type=inline&quot;&gt;&lt;/script&gt;</textarea>\n\t\t\t\t<textarea id=\"whois_wizard_form_3\" class=\"text1 hide\">&lt;script type=&quot;text/javascript&quot; src=&quot;";
    echo ORDERFORM_URL;
    echo "whois.php?";
    if($orderform_id != DEFAULT_ORDERFORM) {
        echo "cart=" . $orderform_id . "&amp;";
    }
    echo "display=form&amp;type=extern&quot;&gt;&lt;/script&gt;</textarea>\n\t\n\t\t\t<!--box3-->\n\t\t\t</div></div>\n\t\t\t<!--box3-->\n\t\n\t\t\t<!--box3-->\n\t\t\t<div id=\"whois_wizard_inlineresult\" ";
    if(!isset($orderform->OtherSettings->domain->ResultURL) || !$orderform->OtherSettings->domain->ResultURL) {
        echo "style=\"display:none;\"";
    }
    echo " class=\"box3\"><h3>";
    echo __("whois wizard result page");
    echo "</h3><div class=\"content lineheight2\">\n\t\t\t<!--box3-->\n\t\t\t\n\t\t\t\t<strong>";
    echo __("whois wizard generated code");
    echo "</strong><br />\n\t\t\t\t";
    echo isset($orderform->OtherSettings->domain->ResultURL) && $orderform->OtherSettings->domain->ResultURL ? sprintf(__("whois wizard generated code copy 2 custom resulturl"), $orderform->OtherSettings->domain->ResultURL) : __("whois wizard generated code copy 2");
    echo "<br />\n\t\t\t\t<textarea class=\"text1\">&lt;script type=&quot;text/javascript&quot; src=&quot;";
    echo ORDERFORM_URL;
    echo "whois.php?";
    if($orderform_id != DEFAULT_ORDERFORM) {
        echo "cart=" . $orderform_id . "&amp;";
    }
    echo "display=results&quot;&gt;&lt;/script&gt;</textarea>\n\t\t\t\t\n\t\t\t<!--box3-->\n\t\t\t</div></div>\n\t\t\t<!--box3-->\t\n\t\t\t";
}
echo "\t\t</div>\t\n\t\n\t<!--content-->\n\t</div>\n\t<!--content-->\n\n<!--box1-->\n</div>\n<!--box1-->\n\n";
if(U_ORDERFORM_DELETE) {
    echo "<div id=\"delete_orderform\" class=\"hide\" title=\"";
    echo __("deletedialog orderform title");
    echo "\">\n\t<form id=\"DeleteOrderForm\" name=\"form_delete\" method=\"post\" action=\"orderform.php?page=delete\">\n\t<input type=\"hidden\" name=\"id\" value=\"\"/>\n\t";
    echo __("deletedialog orderform description");
    echo "<br />\n\t<br />\n\t<input type=\"checkbox\" id=\"imsure\" name=\"imsure\" value=\"yes\"/> <label for=\"imsure\">";
    echo __("delete this orderform");
    echo "</label><br />\n\t<br />\n\t<p><a id=\"delete_orderform_btn\" class=\"button2 alt1 float_left\"><span>";
    echo __("delete");
    echo "</span></a></p>\n\t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#delete_orderform').dialog('close');\"><span>";
    echo __("cancel");
    echo "</span></a></p>\n\t</form>\n</div>\n";
}
echo "\n\n<script type=\"text/javascript\">\n\t\$(function()\n\t{\n\t\t// All color pickers\n\t\t\$('.colorpicker').spectrum({preferredFormat: \"hex\",  color: \"#fff\",  clickoutFiresChange: true, showButtons: false}).show();\n\t\t\$('.colorpicker').change(function()\n\t\t{\n\t\t\tif(\$(this).val())\n\t\t\t{\n\t\t\t\t\$(this).spectrum(\"set\", \$(this).val());\n\t\t\t}\n\t\t\telse\n\t\t\t{\n\t\t\t\t\$(this).next().find('.sp-preview-inner').css('background-color','#ffffff');\n\t\t\t}\n\t\t});\n\t\t// Set init color\n        \$('.colorpicker').trigger('change');\n\t});\n</script>\n\n";
require_once "views/footer.php";

?>