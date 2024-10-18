<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "views/header.php";
echo isset($message) ? $message : "";
if(!SDD_ID) {
    echo "\t<div class=\"setting_help_box\">\n\t\t<strong>";
    echo __("sepa direct debit is not activated yet - title");
    echo "</strong><br />\n\t\t";
    echo sprintf(__("sepa direct debit is not activated yet - explained"), "<a href=\"paymentmethods.php\" class=\"a1 c1\">" . __("sepa direct debit is not activated yet - link") . "</a>");
    echo " \n\t</div>\n\t";
} else {
    if(!empty($list_batches["current"])) {
        require_once "views/elements/directdebit.table.php";
        $options = ["title" => __("sdd batches current h2"), "table_type" => "current", "session_name" => "directdebit.overview"];
        show_directdebit_table($list_batches["current"], $options);
    }
    if(!empty($list_batches["processing"])) {
        require_once "views/elements/directdebit.table.php";
        $options = ["title" => __("sdd batches processing h2"), "table_type" => "processing", "session_name" => "directdebit.overview"];
        show_directdebit_table($list_batches["processing"], $options);
    }
    require_once "views/elements/directdebit.table.php";
    $options = ["title" => __("sdd batches archive h2"), "table_type" => "archive", "session_name" => "directdebit.overview", "current_page_url" => $current_page_url, "current_page" => $current_page, "total_archived_batches" => $total_archived_batches];
    show_directdebit_table($list_archived_batches, $options);
}
require_once "views/footer.php";

?>