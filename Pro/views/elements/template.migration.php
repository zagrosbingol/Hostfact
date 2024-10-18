<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
if(PDF_MODULE == "fpdf") {
    echo "\n\t<p class=\"pos2\"><a class=\"a1 c1\" onclick=\"\$('#dialog_migrate_editor').dialog('open');\">";
    echo __("layout editor migration link");
    echo "</a></p>\n\n\t<div id=\"dialog_migrate_editor\" title=\"";
    echo __("layout editor migration link");
    echo "\" class=\"hide\">\n\t\t<form name=\"migrate_editor\" method=\"post\" action=\"templates.php?page=";
    echo $page;
    echo "\">\n\t\t\t<input type=\"hidden\" name=\"action\" value=\"migrate_editor\"/>\n\t\t\t<p>";
    echo __("layout editor migration dialog content");
    echo "</p>\n\t\t\t<br/>\n\t\t\t<label><input type=\"checkbox\" name=\"imsure\" value=\"yes\"/> ";
    echo __("layout editor migration imsure");
    echo "</label>\n\t\t\t<br/><br/>\n\t\t</form>\n\t\t<p><a class=\"button2 alt1 float_left\" id=\"dialog_migrate_editor_submit\"><span>";
    echo __("proceed");
    echo "</span></a></p>\n\t\t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#dialog_migrate_editor').dialog('close');\">";
    echo __("cancel");
    echo "</a></p>\n\t</div>\n\t<script type=\"text/javascript\">\n        \$(function () {\n            \$('#dialog_migrate_editor').dialog({autoOpen: false, width: 700, modal: true, resizable: false});\n            \$('input[name=\"imsure\"]').click(function () {\n                if (\$('input[name=\"imsure\"]:checked').val() != null) {\n                    \$('#dialog_migrate_editor_submit').removeClass('button2').addClass('button1');\n                }\n                else {\n                    \$('#dialog_migrate_editor_submit').removeClass('button1').addClass('button2');\n                }\n            });\n            \$('#dialog_migrate_editor_submit').click(function () {\n                if (\$('input[name=\"imsure\"]:checked').val() != null) {\n                    document.migrate_editor.submit();\n                }\n            });\n        });\n\t</script>\n    ";
} elseif(PDF_MODULE == "tcpdf" && $fpdf_still_in_database === true) {
    echo "\t<div class=\"mark blue\">\n\t\t<strong>Migratie naar nieuwe WYSIWYG-editor</strong><br />\n\t\t<p>\n\n\t\t\tU bent recent overgestapt naar de nieuwe WYSIWYG-huisstijl editor. Zodra u de factuur, offerte en overige templates heeft gecontroleerd, kunt u aangeven dat u definitief bent overgestapt naar de nieuwe editor. Eventueel kunt u ook terug naar de oude editor om op een later moment over te stappen. De huidige WYSIWYG-templates worden dan wel verwijderd.</p><br />\n\t\t<a class=\"button1\" onclick=\"\$('#dialog_migrate_editor').dialog('open');\"><span>Definitief overstappen naar de nieuwe editor</span></a>\n\t\t<a class=\"button1 red float_right\" onclick=\"\$('#dialog_undo_migrate_editor').dialog('open');\"><span>Terug naar oude editor</span></a>\n\t</div>\n\t<div id=\"dialog_migrate_editor\" title=\"Definitief overstappen naar de nieuwe editor\" class=\"hide\">\n\t\t<form name=\"migrate_editor\" method=\"post\" action=\"templates.php?page=";
    echo $page;
    echo "\">\n\t\t\t<input type=\"hidden\" name=\"action\" value=\"migrate_editor_confirm\"/>\n\t\t\t<p>U staat op het punt om definitief over te stappen naar de nieuwe editor. Daarna kunt u niet meer terug naar de oude editor.</p>\n\t\t\t<br/>\n\t\t\t<label><input type=\"checkbox\" name=\"imsure\" value=\"yes\"/> Ik heb de huidige templates gecontroleerd en wil definitief overstappen.</label>\n\t\t\t<br/><br/>\n\t\t</form>\n\t\t<p><a class=\"button2 alt1 float_left\" id=\"dialog_migrate_editor_submit\"><span>";
    echo __("proceed");
    echo "</span></a></p>\n\t\t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#dialog_migrate_editor').dialog('close');\">";
    echo __("cancel");
    echo "</a></p>\n\t</div>\n\t<div id=\"dialog_undo_migrate_editor\" title=\"Terug naar oude editor\" class=\"hide\">\n\t\t<form name=\"undo_migrate_editor\" method=\"post\" action=\"templates.php?page=";
    echo $page;
    echo "\">\n\t\t\t<input type=\"hidden\" name=\"action\" value=\"migrate_editor_undo\"/>\n\t\t\t<p>U wilt tijdelijk terug naar de oude editor. Daarmee gaan de huidige WYSIWYG-templates verloren. Let op dat deze versie van HostFact de laatste versie is waarin u de oude editor kunt gebruiken.</p>\n\t\t\t<br/>\n\t\t\t<label><input type=\"checkbox\" name=\"imsure\" value=\"yes\"/> Ik wil tijdelijk terug naar de oude editor.</label>\n\t\t\t<br/><br/>\n\t\t</form>\n\t\t<p><a class=\"button2 alt1 float_left\" id=\"dialog_undo_migrate_editor_submit\"><span>";
    echo __("proceed");
    echo "</span></a></p>\n\t\t<p><a class=\"a1 c1 float_right\" onclick=\"\$('#dialog_undo_migrate_editor').dialog('close');\">";
    echo __("cancel");
    echo "</a></p>\n\t</div>\n\t<script type=\"text/javascript\">\n        \$(function () {\n            \$('#dialog_migrate_editor').dialog({autoOpen: false, width: 700, modal: true, resizable: false});\n\n            \$('#dialog_migrate_editor input[name=\"imsure\"]').click(function () {\n                if (\$('#dialog_migrate_editor input[name=\"imsure\"]:checked').val() != null) {\n                    \$('#dialog_migrate_editor_submit').removeClass('button2').addClass('button1');\n                }\n                else {\n                    \$('#dialog_migrate_editor_submit').removeClass('button1').addClass('button2');\n                }\n            });\n            \$('#dialog_migrate_editor_submit').click(function () {\n                if (\$('input[name=\"imsure\"]:checked').val() != null) {\n                    document.migrate_editor.submit();\n                }\n            });\n\n            \$('#dialog_undo_migrate_editor').dialog({autoOpen: false, width: 700, modal: true, resizable: false});\n            \$('#dialog_undo_migrate_editor input[name=\"imsure\"]').click(function () {\n                if (\$('#dialog_undo_migrate_editor input[name=\"imsure\"]:checked').val() != null) {\n                    \$('#dialog_undo_migrate_editor_submit').removeClass('button2').addClass('button1');\n                }\n                else {\n                    \$('#dialog_undo_migrate_editor_submit').removeClass('button1').addClass('button2');\n                }\n            });\n            \$('#dialog_undo_migrate_editor_submit').click(function () {\n                if (\$('input[name=\"imsure\"]:checked').val() != null) {\n                    document.undo_migrate_editor.submit();\n                }\n            });\n        });\n\t</script>\n\t";
}

?>