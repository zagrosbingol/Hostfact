<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "config.php";
$filetype = isset($_GET["type"]) ? $_GET["type"] : "pdf_files";
$dbfiletype = str_replace("_files", "", $filetype);
switch ($dbfiletype) {
    case "invoice":
        $show_rights = U_INVOICE_SHOW;
        $add_rights = U_INVOICE_EDIT;
        $delete_rights = U_INVOICE_EDIT;
        break;
    case "creditinvoice":
        $show_rights = U_CREDITOR_INVOICE_SHOW;
        $add_rights = U_CREDITOR_INVOICE_EDIT;
        $delete_rights = U_CREDITOR_INVOICE_EDIT;
        break;
    case "pricequote":
        $show_rights = U_PRICEQUOTE_SHOW;
        $add_rights = U_PRICEQUOTE_EDIT;
        $delete_rights = U_PRICEQUOTE_EDIT;
        break;
    case "debtor":
        $show_rights = U_DEBTOR_SHOW;
        $add_rights = U_DEBTOR_EDIT;
        $delete_rights = U_DEBTOR_EDIT;
        break;
    case "creditor":
        $show_rights = U_CREDITOR_SHOW;
        $add_rights = U_CREDITOR_EDIT;
        $delete_rights = U_CREDITOR_EDIT;
        break;
    default:
        $show_rights = U_SETTINGS_SHOW;
        $add_rights = U_SETTINGS_ADD;
        $delete_rights = U_SETTINGS_DELETE;
        checkRight($show_rights);
        require_once "class/attachment.php";
        $attachment = new attachment();
        $attachment->FileType = $dbfiletype;
        $attachment->Reference = "";
        $attachment->FileDir = $attachment->fileDir(0, $dbfiletype, true);
        if(!is_dir($attachment->FileDir)) {
            mkdir($attachment->FileDir, 493, true);
        }
        if(!is_writable($attachment->FileDir)) {
            echo sprintf(__("cannot upload file, no folder rights"), $attachment->FileDir);
            exit;
        }
        if(isset($_GET["uploadtype"]) || isset($_POST["action"]) && $_POST["action"] == "upload" && $add_rights) {
            if(defined("IS_DEMO") && IS_DEMO) {
            } else {
                if(isset($_FILES["fileToUpload"])) {
                    $fileToUpload = $_FILES["fileToUpload"];
                    unset($_FILES["fileToUpload"]);
                    if(!empty($fileToUpload["name"])) {
                        foreach ($fileToUpload as $name => $array) {
                            foreach ($array as $key => $value) {
                                $_FILES[$key][$name] = $value;
                            }
                        }
                    }
                }
                $count = 0;
                foreach ($_FILES as $value) {
                    if(empty($value["error"])) {
                        $attachment->FilenameOriginal = $value["name"];
                        $rewriteName = false;
                        if($filetype == "invoice_files" || $filetype == "pricequote_files" || $filetype == "creditinvoice_files" || $filetype == "debtor_files" || $filetype == "creditor_files") {
                            $rewriteName = true;
                        }
                        $attachment->_setFileInfo(false, $rewriteName);
                        if(@move_uploaded_file($value["tmp_name"], $attachment->FileDir . $attachment->FilenameServer)) {
                            $_SESSION["tmp_uploaded_file"][$count]["FileDir"] = substr($attachment->FileDir, 0, -1);
                            $_SESSION["tmp_uploaded_file"][$count]["FileName"] = $attachment->FilenameOriginal;
                            $_SESSION["tmp_uploaded_file"][$count]["FilePath"] = $attachment->FileDir . $attachment->FilenameServer;
                            $_SESSION["tmp_uploaded_file"][$count]["FileType"] = $attachment->FileType;
                            $_SESSION["tmp_uploaded_file"][$count]["FileExtension"] = $attachment->FileExtension;
                            $attachment->FileSize = filesize($attachment->FileDir . $attachment->FilenameServer);
                            $size = getFileSizeUnit($attachment->FileSize);
                            $_SESSION["tmp_uploaded_file"][$count]["FileSize"] = $size["size"];
                            $_SESSION["tmp_uploaded_file"][$count]["FileSizeUnit"] = $size["unit"];
                            if($filetype == "invoice_files" || $filetype == "pricequote_files" || $filetype == "creditinvoice_files" || $filetype == "debtor_files" || $filetype == "creditor_files") {
                                $documentId = $attachment->_saveFileToDb();
                                $_SESSION["tmp_uploaded_file"][$count]["FilePath"] = $documentId;
                            }
                        }
                        $count++;
                    } elseif(isset($_GET["uploadtype"])) {
                        switch ($value["error"]) {
                            case "1":
                                echo __("upload error ini file");
                                break;
                            default:
                                echo sprintf(__("uploading file x failed"), $value["name"]);
                                exit;
                        }
                    } else {
                        header("location: filemanager.php?type=" . $filetype);
                        exit;
                    }
                }
                if(isset($_GET["uploadtype"]) && !empty($_FILES) && count($_FILES) === $count) {
                    echo "OK";
                    exit;
                }
                if(isset($_GET["uploadtype"])) {
                    exit;
                }
            }
            include "views/dialog.filemanager.php";
        } elseif(isset($_POST["action"]) && $_POST["action"] == "select" && $add_rights) {
            foreach ($_POST["name"] as $key => $value) {
                $_SESSION["tmp_uploaded_file"][$key]["FileDir"] = substr($attachment->FileDir, 0, -1);
                if(strpos($value, "TemplateOther") !== false) {
                    $filesize = "";
                    $size = ["size" => "", "unit" => ""];
                } else {
                    $filesize = filesize($value);
                    $size = getFileSizeUnit($filesize);
                }
                if(strstr($value, "/") && !strstr($value, "TemplateOther")) {
                    $_SESSION["tmp_uploaded_file"][$key]["FileName"] = substr($value, strrpos($value, "/") + 1);
                    $_SESSION["tmp_uploaded_file"][$key]["FileSize"] = $size["size"];
                    $_SESSION["tmp_uploaded_file"][$key]["FileSizeUnit"] = $size["unit"];
                    $_SESSION["tmp_uploaded_file"][$key]["FilePath"] = $value;
                } else {
                    $_SESSION["tmp_uploaded_file"][$key]["FileName"] = $value;
                    $_SESSION["tmp_uploaded_file"][$key]["FileSize"] = "";
                    $_SESSION["tmp_uploaded_file"][$key]["FileSizeUnit"] = "";
                    $_SESSION["tmp_uploaded_file"][$key]["FilePath"] = $value;
                }
            }
            include "views/dialog.filemanager.php";
        } elseif(isset($_GET["action"]) && $_GET["action"] == "delete" && $delete_rights) {
            $file = addslashes($_GET["file"]);
            $file = substr($file, 5);
            $file_tmp = str_replace("%2F", "/", urlencode($file));
            if(file_exists($file_tmp)) {
                if(is_dir($file_tmp)) {
                    $files1 = scandir($file_tmp);
                    foreach ($files1 as $subfile) {
                        if($subfile != "." && $subfile != "..") {
                            @unlink($file_tmp . "/" . $subfile);
                        }
                    }
                    if(@rmdir($file_tmp)) {
                        $error_class->Success[] = sprintf(__("directory deleted"), $file);
                    } else {
                        $error_class->Error[] = sprintf(__("directory delete failed"), $file);
                    }
                } elseif(!is_dir($file_tmp) && unlink($file_tmp)) {
                    $error_class->Success[] = sprintf(__("file deleted"), $file);
                } else {
                    $error_class->Error[] = sprintf(__("file delete failed"), $file);
                }
            } else {
                $error_class->Error[] = sprintf(__("file not exists"), $file);
            }
        } elseif(isset($_POST["action"]) && $_POST["action"] == "delete" && $delete_rights) {
            foreach ($_POST["files"] as $k => $file) {
                $file = substr($file, 5);
                if(file_exists($file)) {
                    if(is_dir($file)) {
                        $files1 = scandir($file);
                        foreach ($files1 as $subfile) {
                            if($subfile != "." && $subfile != "..") {
                                @unlink($file . "/" . $subfile);
                            }
                        }
                        if(@rmdir($file)) {
                            $error_class->Success[] = sprintf(__("directory deleted"), $file);
                        } else {
                            $error_class->Error[] = sprintf(__("directory not deleted"), $file);
                        }
                    } elseif(!is_dir($file) && unlink($file)) {
                        $error_class->Success[] = sprintf(__("file deleted"), $file);
                    } else {
                        $error_class->Error[] = sprintf(__("file not deleted"), $file);
                    }
                } else {
                    $error_class->Error[] = sprintf(__("file not exists"), $file);
                }
            }
        } else {
            if($filetype == "email_files") {
                require_once "class/template.php";
                $templatelist = new template();
                $fields = ["Name", "Location", "Title", "Author"];
                $templates = $templatelist->all($fields, "", "", "", "Type", "other");
                $attach2 = [];
                foreach ($templates as $k => $v) {
                    if(is_numeric($k)) {
                        $attach2["TemplateOther" . $k] = $v["Name"];
                    }
                }
                $tmp_count_gen = count($attach2);
                asort($attach2);
                $limit_gen = isset($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : "1";
                $chunk_attach2 = array_chunk($attach2, 10, true);
                $attach2 = $chunk_attach2[$limit_gen - 1];
                $attach2["CountRows"] = $tmp_count_gen;
                $current_page_gen = $limit_gen;
                $current_page_url_gen = "filemanager.php?type=" . $filetype;
            }
            $files = [];
            if(is_writable($attachment->FileDir)) {
                $handle = @opendir($attachment->FileDir);
                while (false !== ($file = @readdir($handle))) {
                    if($file != "." && $file != ".." && $file != "index.php" && $file != ".htaccess" && @file_exists($attachment->FileDir . $file)) {
                        $files[$attachment->FileDir . $file] = $file;
                    }
                }
                $tmp_count = count($files);
                $limit = isset($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : "1";
                $chunk_files = array_chunk($files, 10, true);
                $files = isset($chunk_files[$limit - 1]) ? $chunk_files[$limit - 1] : [];
                $files["CountRows"] = $tmp_count;
                $current_page = $limit;
                $current_page_url = "filemanager.php?type=" . $filetype;
            } else {
                $error_class->Error[] = sprintf(__("filemanager permissions check"), $attachment->FileDir);
                $ErrorMessage = parse_message();
            }
            require_once "views/dialog.filemanager.php";
        }
}

?>