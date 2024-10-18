<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
if((!defined("PDF_MODULE") || PDF_MODULE == "fpdf") && file_exists("class/pdf_fpdf.php") && is_dir("3rdparty/pdf")) {
    require_once "class/pdf_fpdf.php";
} elseif(PDF_MODULE == "tcpdf" && file_exists("class/pdf_tcpdf.php") && is_dir("3rdparty/vendor/tecnickcom/tcpdf")) {
    require_once "class/pdf_tcpdf.php";
} else {
    fatal_error("The PDF module seems to be missing", "Please contact HostFact.");
}

?>