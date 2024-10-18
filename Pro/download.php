<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
define("DOWNLOAD_PHP_ACTIVE", "YES");
require_once "config.php";
require_once "class/attachment.php";
$attachment = new attachment();
if(isset($_SESSION["force_download"]) && $_SESSION["force_download"]) {
    $_SESSION["delete_download"] = $_SESSION["force_download"];
    $filename = "temp/" . esc($_SESSION["force_download"]);
    if(strpos($filename, ".txt") !== false) {
        $content_type = "application/force-download";
    } elseif(strpos($filename, ".xls") !== false) {
        $content_type = "application/vnd.ms-excel; charset=utf-8";
    } elseif(strpos($filename, ".csv") !== false) {
        $content_type = "text/x-csv";
    } elseif(strpos($filename, ".xml") !== false) {
        $content_type = "text/xml";
    } elseif(strpos($filename, ".zip") !== false) {
        $content_type = "application/zip";
    } else {
        $content_type = "application/pdf";
    }
    header("Cache-Control: public, must-revalidate");
    header("Pragma: hack");
    header("Content-Type: " . $content_type);
    header("Content-Length: " . @filesize($filename));
    header("Content-Disposition: attachment; filename=\"" . esc($_SESSION["force_download"]) . "\"");
    header("Content-Transfer-Encoding: binary");
    $fp = @fopen($filename, "rb");
    $buffer = @fread($fp, @filesize($filename));
    @fclose($fp);
    echo $buffer;
    exit;
}
if(isset($_GET["filename"]) && $_GET["filename"] || isset($_GET["id"]) && $_GET["id"]) {
    $filename = "";
    if(isset($_GET["filename"]) && (strpos(esc($_GET["filename"]), "..") !== false || strpos(esc($_GET["filename"]), "./") !== false || strpos(trim($_GET["filename"]), "/") !== false)) {
        header("HTTP/1.1 404 Not Found");
        exit;
    }
    if(isset($_GET["type"]) && $_GET["type"] == "ticket") {
        $file_name = esc($_GET["filename"]);
        $result = Database_Model::getInstance()->getOne("HostFact_TicketMessage", "TicketID")->where("id", esc(intval($_GET["id"])))->execute();
        if($result) {
            $filename = $attachment->fileDir(0, $_GET["type"], true) . $result->TicketID . "/" . $file_name;
        }
    } elseif(isset($_GET["type"]) && $_GET["type"] == "backup" && U_SETTINGS_SHOW) {
        $file_name = esc($_GET["filename"]);
        $filename = BACKUP_DIR . $file_name;
    } elseif(isset($_GET["type"]) && ($_GET["type"] == "invoice" || $_GET["type"] == "creditinvoice" || $_GET["type"] == "pricequote" || $_GET["type"] == "pricequote_accepted" || $_GET["type"] == "debtor" || $_GET["type"] == "creditor")) {
        $result = Database_Model::getInstance()->getOne("HostFact_Documents")->where("id", esc($_GET["id"]))->execute();
        $filename = $attachment->fileDir($result->Reference, $result->Type) . $result->FilenameServer;
        $file_name = $result->Filename;
    }
    if(file_exists($filename)) {
        $f = fopen($filename, "r");
        $buffer = fread($f, filesize($filename));
        fclose($f);
        $filename = basename($filename);
        header("Content-type: application/octet-stream");
        header("Content-Disposition: download; filename=\"" . $file_name . "\"");
        header("Content-Transfer-Encoding: binary");
        echo $buffer;
    } else {
        fatal_error("Download has expired", "The file is no longer available, please try again.");
        exit;
    }
} else {
    fatal_error("Download has expired", "The file is no longer available, please try again.");
    exit;
}

?>