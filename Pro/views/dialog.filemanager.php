<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
if(isset($ErrorMessage)) {
    echo " \n\t<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n\t<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\" style=\"overflow: hidden !important; background-color:#FFF; \">\n\t\n\t<head>\n\t\t<title>HostFact</title>\n\t\t<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n\t\t<script type=\"text/javascript\" src=\"js/jquery-3.4.0.min.js\"></script>\n\t\t<script type=\"text/javascript\" src=\"js/jquery-ui-1.12.1.custom/jquery-ui.js\"></script>\n\t\t<link rel=\"stylesheet\" href=\"css/global.css\" type=\"text/css\" />\n\t\t<script language=\"javascript\" type=\"text/javascript\">\n\t\t\t\$(function(){ parent.\$('#filemanager iframe',top.document).height(\$('body').height()+20); });\n\t\t</script>\n\t</head>\n\t<body style=\"background: none !important; padding: 10px !important;\">\n\t\t";
    echo $ErrorMessage;
    echo "\t\t<a onclick=\"window.location.reload()\" class=\"a1 c1\">";
    echo __("check permissions");
    echo "</a>\n\t</body>\n\t</html>\t\n";
} elseif(isset($_FILES["File"]) && empty($_FILES["File"]["error"]) || isset($_POST["name"]) && is_array($_POST["name"])) {
    echo "\t<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n\t<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\" style=\"overflow: hidden !important; \">\n\t\n\t<head>\n\t\t<title>HostFact</title>\n\t\t<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n\t\t<script type=\"text/javascript\" src=\"js/jquery-3.4.0.min.js\"></script>\n\t\t<script type=\"text/javascript\" src=\"js/jquery-ui-1.12.1.custom/jquery-ui.js\"></script>\n\t\t<link rel=\"stylesheet\" href=\"css/global.css\" type=\"text/css\" />\n\t\t<script language=\"javascript\" type=\"text/javascript\">\n\t\t\t\$(function(){ parent.\$('#filemanager:visible').dialog('close'); });\n\t\t</script>\n\t</head>\n\t<body style=\"background: none !important; padding: 10px !important; width: 420px;\">\n\t\t";
    echo __("upload completed. You can close this dialog.");
    echo "\t</body>\n\t</html>\n\t";
} else {
    echo "\t<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n\t<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\" style=\"overflow: hidden !important; background-color:#FFF; \">\n\t\n\t<head>\n\t\t<title>HostFact</title>\n\t\t<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n\t\t<script type=\"text/javascript\" src=\"js/translated_vars.php?csrf_token=";
    echo CSRF_Model::getToken(true);
    echo "\"></script>\n\t\t<script type=\"text/javascript\" src=\"js/jquery-3.4.0.min.js\"></script>\n\t\t<script type=\"text/javascript\" src=\"js/jquery-ui-1.12.1.custom/jquery-ui.js\"></script>\n\t\t<script type=\"text/javascript\" src=\"js/global.js\"></script>\n\t\t<link href=\"js/jquery-ui-1.12.1.custom/jquery-ui.css\" rel=\"stylesheet\">\n\t\t<link rel=\"stylesheet\" href=\"css/global.css\" type=\"text/css\" />\n\t\t\n\t</head>\n\t<body style=\"background: none !important; padding: 10px !important;\">\n\n\t\t<div id=\"tabs_dialog\" class=\"filemanager_dialog\">\n\n\t\t\t<ul class=\"list3\">\n\t\t\t\t";
    if(!in_array($filetype, ["pdf_files", "invoice_files", "pricequote_files", "creditinvoice_files", "debtor_files", "creditor_files"])) {
        echo "\t\t\t\t\t<li>\n\t\t\t\t\t\t<a href=\"#filemanagertab-1\">";
        echo __("server files");
        echo "</a>\n\t\t\t\t\t</li>\n\t\t\t\t\t";
    }
    if(!in_array($filetype, ["pdf_files", "invoice_files", "pricequote_files", "creditinvoice_files", "debtor_files", "creditor_files", "template_image_files"])) {
        echo "\t\t\t\t\t<li>\n\t\t\t\t\t\t<a href=\"#filemanagertab-2\">";
        echo __("generated files");
        echo "</a>\n\t\t\t\t\t</li>\n\t\t\t\t\t";
    }
    echo "\t\t\t\t<li>\n\t\t\t\t\t<a href=\"#filemanagertab-3\">";
    echo __("upload");
    echo "</a>\n\t\t\t\t</li>\n\t\t\t</ul>\n\t\t\t";
    if(!in_array($filetype, ["pdf_files", "invoice_files", "pricequote_files", "creditinvoice_files", "debtor_files", "creditor_files"])) {
        echo "\t\t\t<div id=\"filemanagertab-1\">\n\t\t\t\t<form name=\"select_file\" action=\"filemanager.php?type=";
        echo $filetype;
        echo "\" method=\"post\">\n\t\t\t\t\t<input type=\"hidden\" name=\"action\" value=\"select\" />\n\t\t\t\t\t\n\t\t\t\t\t<div id=\"SubTable_Files\">\n\t\t\t\t\t\t<table id=\"MainTable_Files\" class=\"table1\" cellpadding=\"0\" cellspacing=\"0\" style=\"margin:0 0 10px 0;\">\n\t\t\t\t\t\t\t<tr class=\"trtitle\">\n\t\t\t\t\t\t\t\t<th scope=\"col\" width=\"15\"><label><input name=\"FileBatch\" class=\"BatchCheck\" type=\"checkbox\" value=\"on\" /></label></th>\n\t\t\t\t\t\t\t\t<th scope=\"col\">";
        echo __("filename");
        echo "</th>\n\t\t\t\t\t\t\t\t<th scope=\"col\" width=\"100\">";
        echo __("filesize");
        echo "</th>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t";
        $fileCounter = 0;
        echo "\t\t\t\t\t\t\t";
        if(isset($files) && is_array($files) && 0 < $files["CountRows"]) {
            echo "\t\t\t\t\t\t\t\t";
            foreach ($files as $path => $file) {
                if($path != "CountRows") {
                    $fileCounter++;
                    echo "\t\t\t\t\t\t\t\t<tr ";
                    if($fileCounter % 2 == 1) {
                        echo "class=\"tr1\"";
                    }
                    echo ">\n\t\t\t\t\t\t\t\t\t<td><input name=\"name[]\" type=\"checkbox\" value=\"";
                    echo htmlspecialchars($path);
                    echo "\" class=\"FileBatch\"/></td>\n\t\t\t\t\t\t\t\t\t<td><div class=\"ico inline file_";
                    getFileType($file);
                    echo "\">&nbsp;</div> ";
                    echo $file;
                    echo "</td>\n\t\t\t\t\t\t\t\t\t<td>";
                    echo number_format(filesize($path) / 1024, 2, AMOUNT_DEC_SEPERATOR, AMOUNT_THOU_SEPERATOR);
                    echo " KB</td>\n\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t";
                }
            }
            echo "\t\t\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\t\t";
        if($files["CountRows"] < 1) {
            echo "\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t<td colspan=\"3\">";
            echo __("no files found");
            echo "</td>\n\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t";
        } else {
            echo "\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t<td colspan=\"3\">\n\t\t\t\t\t\t\t\t\t\t";
            ajax_paginate("Files", $files["CountRows"], 10, $current_page, $current_page_url, false);
            echo "\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\t</table>\n\t\t\t\t\t</div>\n\t\t\t\t\t\n\t\t\t\t\t<p><a class=\"button2 alt1 \" id=\"select_files_btn\"><span>";
        echo __("select files");
        echo "</span></a></p>\n\t\t\t\t\t\n\t\t\t\t</form>\n\t\t\t</div>\n\t\t\t";
    }
    echo "\t\t\t\n\t\t\t";
    if(!in_array($filetype, ["pdf_files", "invoice_files", "pricequote_files", "creditinvoice_files", "debtor_files", "creditor_files", "template_image_files"])) {
        echo "\t\t\t<div id=\"filemanagertab-2\">\n\t\t\t\t<form id=\"FilemanagerForm\" name=\"form_filemanager\" action=\"filemanager.php?type=";
        echo $filetype;
        echo "\" method=\"post\">\n\t\t\t\t\t<input type=\"hidden\" name=\"action\" value=\"select\" />\n\t\t\t\t\t\n\t\t\t\t\t<div id=\"SubTable_FilesGen\">\n\t\t\t\t\t\t<table id=\"MainTable_FilesGen\" class=\"table1\" cellpadding=\"0\" cellspacing=\"0\" style=\"margin:0 0 10px 0;\">\n\t\t\t\t\t\t\t<tr class=\"trtitle\">\n\t\t\t\t\t\t\t\t<th scope=\"col\" width=\"15\"><label><input name=\"FileGenBatch\" class=\"BatchCheck\" type=\"checkbox\" value=\"on\" /></label></th>\n\t\t\t\t\t\t\t\t<th scope=\"col\">";
        echo __("generated files");
        echo "</th>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t";
        $attachCounter = 0;
        echo "\t\t\t\t\t\t\t";
        if(isset($attach2) && is_array($attach2) && 0 < $attach2["CountRows"]) {
            echo "\t\t\t\t\t\t\t\t";
            foreach ($attach2 as $key => $value) {
                if($key != "CountRows") {
                    $attachCounter++;
                    echo "\t\t\t\t\t\t\t\t<tr ";
                    if($attachCounter % 2 == 1) {
                        echo "class=\"tr1\"";
                    }
                    echo ">\n\t\t\t\t\t\t\t\t\t<td><input name=\"name[]\" type=\"checkbox\" value=\"";
                    echo htmlspecialchars($key);
                    echo "\" class=\"FileGenBatch\"/></td>\n\t\t\t\t\t\t\t\t\t<td><div class=\"ico inline file_";
                    getFileType($value);
                    echo "\">&nbsp;</div> ";
                    echo $value;
                    echo "</td>\n\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t\t";
                }
            }
            echo "\t\t\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\t\t";
        if($attach2["CountRows"] < 1) {
            echo "\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t<td colspan=\"2\">";
            echo __("no files found");
            echo "</td>\n\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t";
        } else {
            echo "\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t<td colspan=\"2\">\n\t\t\t\t\t\t\t\t\t\t";
            ajax_paginate("FilesGen", $attach2["CountRows"], 10, $current_page_gen, $current_page_url_gen, false);
            echo "\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\t</table>\n\n\t\t\t\t\t</div>\n\n                    <p><a class=\"button2 alt1 \" id=\"select_generatedfiles_btn\"><span>";
        echo __("select files");
        echo "</span></a></p>\n\n                </form>\n\t\t\t</div>\n\t\t\t";
    }
    echo "\t\t\t\n\t\t\t<div id=\"filemanagertab-3\">\n\t\t\t\t\n\t\t\t\t<form name=\"file_upload\" method=\"post\" action=\"filemanager.php?type=";
    echo $filetype;
    echo "\"";
    if(!defined("IS_DEMO") || !IS_DEMO) {
        echo " enctype=\"multipart/form-data\"";
    }
    echo ">\n\t\t\t\t<div id=\"not_uploading\">\n\t\t\t\t\t\n\t\t\t\t\t";
    if(defined("IS_DEMO") && IS_DEMO) {
        echo __("demo - not able to add attachments");
    } else {
        echo "\t\t\t\t\t\t<input type=\"hidden\" name=\"action\" value=\"upload\" />\n\t\t\t\t\t\t\n\t\t\t\t\t\t<input type=\"file\" name=\"File\" ";
        if($filetype == "pdf_files") {
            echo "accept=\"application/pdf,application/x-pdf\"";
        } elseif($filetype == "template_image_files") {
            echo "accept=\"image/*\"";
        }
        echo "/><br />\n\t\t\t\t\n\t\t\t\t\t\t<br />\n\t\t\t\t\t\t<p><a class=\"button1 alt1 float_left\" onclick=\"\$('#not_uploading').hide(); \$('#uploading').show(); \$(this).parents('form').submit();\"><span>";
        echo __("add file");
        echo "</span></a></p>\n\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\n\t\t\t\t\t<div style=\"clear:both;\"></div>\n\t\t\t\t\t\n\t\t\t\t</div>\n\t\t\t\t<div id=\"uploading\" class=\"hide\"><strong>";
    echo __("file is being uploaded to server");
    echo "</strong><br />";
    echo __("this proces can take several minutes depending on filesize");
    echo "</div>\n\t\t\t\t</form>\n\t\n\t\t\t</div>\n\t\t\t\n\t\t</div>\n\n\t<script>\n        \$(function(){\n            \$('#tabs_dialog').tabs();\n\n            \$('#tabs_dialog li').click(function(){\n                \$('#filemanager iframe', top.document).height(\$('body').height() + 20);\n            });\n        });\n        setTimeout(function(){ \$('#filemanager iframe', top.document).height(\$('body').height() + 20); }, 300);\n\t</script>\n\t</body>\n</html>\n";
}

?>