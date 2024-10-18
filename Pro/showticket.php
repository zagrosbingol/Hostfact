<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require "config.php";
if(isset($_GET["employee_id"])) {
    require_once "class/employee.php";
    $employee = new employee();
    $employee->Identifier = isset($_GET["employee_id"]) ? esc($_GET["employee_id"]) : "";
    $employee->show();
    if(strpos(strtolower($employee->Signature), "<html") === false) {
        echo "\t\t<html><head><style type=\"text/css\">body {font-family: Arial,Verdana,sans-serif; font-size: 12px; color: #414042;}</style></head><body>";
        echo $employee->Signature;
        echo "</body></html>\n\t\t";
    } else {
        echo $employee->Signature;
    }
} else {
    require_once "class/ticket.php";
    $ticket = new ticketmessage();
    $ticket->Identifier = isset($_GET["id"]) ? intval(esc($_GET["id"])) : "";
    $ticket->show();
    $ticket->Message = str_replace("src=\"data:", "src=\"", $ticket->Message);
    $tmp_attachments = explode("|", $ticket->Attachments);
    foreach ($tmp_attachments as $tmp_attachment) {
        $ticket->Message = str_replace($tmp_attachment, "download.php?type=ticket&id=" . $ticket->Identifier . "&filename=" . urlencode(str_replace(DIR_TICKET_ATTACHMENTS . $ticket->TicketID . "/", "", $tmp_attachment)), $ticket->Message);
    }
    function xss_clean($data)
    {
        $data = str_replace(["&amp;", "&lt;", "&gt;"], ["&amp;amp;", "&amp;lt;", "&amp;gt;"], $data);
        $data = preg_replace("/(&#*\\w+)[\\x00-\\x20]+;/u", "\$1;", $data);
        $data = preg_replace("/(&#x*[0-9A-F]+);*/iu", "\$1;", $data);
        $data = html_entity_decode($data, ENT_COMPAT, "UTF-8");
        $data = preg_replace("#(<[^>]+?[\\x00-\\x20\"'])(?:on|xmlns)[^>]*+>#iu", "\$1>", $data);
        $data = preg_replace("#([a-z]*)[\\x00-\\x20]*=[\\x00-\\x20]*([`'\"]*)[\\x00-\\x20]*j[\\x00-\\x20]*a[\\x00-\\x20]*v[\\x00-\\x20]*a[\\x00-\\x20]*s[\\x00-\\x20]*c[\\x00-\\x20]*r[\\x00-\\x20]*i[\\x00-\\x20]*p[\\x00-\\x20]*t[\\x00-\\x20]*:#iu", "\$1=\$2nojavascript...", $data);
        $data = preg_replace("#([a-z]*)[\\x00-\\x20]*=(['\"]*)[\\x00-\\x20]*v[\\x00-\\x20]*b[\\x00-\\x20]*s[\\x00-\\x20]*c[\\x00-\\x20]*r[\\x00-\\x20]*i[\\x00-\\x20]*p[\\x00-\\x20]*t[\\x00-\\x20]*:#iu", "\$1=\$2novbscript...", $data);
        $data = preg_replace("#([a-z]*)[\\x00-\\x20]*=(['\"]*)[\\x00-\\x20]*-moz-binding[\\x00-\\x20]*:#u", "\$1=\$2nomozbinding...", $data);
        $data = preg_replace("#(<[^>]+?)style[\\x00-\\x20]*=[\\x00-\\x20]*[`'\"]*.*?expression[\\x00-\\x20]*\\([^>]*+>#i", "\$1>", $data);
        $data = preg_replace("#(<[^>]+?)style[\\x00-\\x20]*=[\\x00-\\x20]*[`'\"]*.*?behaviour[\\x00-\\x20]*\\([^>]*+>#i", "\$1>", $data);
        $data = preg_replace("#(<[^>]+?)style[\\x00-\\x20]*=[\\x00-\\x20]*[`'\"]*.*?s[\\x00-\\x20]*c[\\x00-\\x20]*r[\\x00-\\x20]*i[\\x00-\\x20]*p[\\x00-\\x20]*t[\\x00-\\x20]*:*[^>]*+>#iu", "\$1>", $data);
        $data = preg_replace("#</*\\w+:\\w[^>]*+>#i", "", $data);
        do {
            $old_data = $data;
            $data = preg_replace("#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript)|title|xml)[^>]*+>#i", "", $data);
        } while ($old_data === $data);
        return $data;
    }
    if(strpos(strtolower($ticket->Message), "<html") === false) {
        echo "\t\t<html><head><style type=\"text/css\">body {font-family: Arial,Verdana,sans-serif; font-size: 12px; color: #414042;}</style></head><body>";
        echo xss_clean($ticket->Message);
        echo "</body></html>\n\t\t";
    } else {
        echo xss_clean($ticket->Message);
    }
}

?>