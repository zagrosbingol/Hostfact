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
switch ($selectgroup) {
    case "0":
        echo __("not sent emails");
        break;
    case "1":
        echo __("sent emails") . " (" . sprintf(__("last x days"), email::RETENTION_DAYS_EMAIL_ARCHIVE) . ")";
        break;
    case "8":
        echo __("emails with errors") . " (" . sprintf(__("last x days"), email::RETENTION_DAYS_EMAIL_ARCHIVE) . ")";
        break;
    default:
        echo "</h2> \n\t\n\t<p class=\"pos2\"><strong class=\"textsize1\">";
        echo __("total");
        echo ": <span>";
        echo isset($email_list["CountRows"]) ? $email_list["CountRows"] : "0";
        echo "</span></strong></p>\n\n\t\n<!--heading1-->\n</div><hr />\n<!--heading1-->\n\n";
        if((int) $selectgroup === 0) {
            echo "<!--optionsbar-->\n<div class=\"optionsbar\">\n<!--optionsbar-->\n\t\n\t<p class=\"pos1\"><a class=\"button1 alt1\" href=\"emails.php?page=batch\"><span>";
            echo __("send next batch");
            echo "</span></a></p>\n\t\n<!--optionsbar-->\n</div>\n<!--optionsbar-->\n";
        }
        echo "\n";
        require_once "views/elements/email.table.php";
        $options = ["redirect_page" => "emails", "session_name" => "emails.overview" . $selectgroup, "current_page" => $current_page, "current_page_url" => $current_page_url, "selectgroup" => $selectgroup];
        show_email_table($email_list, $options);
        echo "\n";
        require_once "views/footer.php";
}

?>