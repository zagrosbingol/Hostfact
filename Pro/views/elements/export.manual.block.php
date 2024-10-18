<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
$_item_properties = isset($statistics[$_item->Type]["export_needed"][$_item->ReferenceID]) ? $statistics[$_item->Type]["export_needed"][$_item->ReferenceID] : (isset($statistics[$_item->Type]["export_older_items"][$_item->ReferenceID]) ? $statistics[$_item->Type]["export_older_items"][$_item->ReferenceID] : false);
$reference_name = $reference_link = false;
switch ($_item->Type) {
    case "debtor":
        $reference_name = $_item_properties->DebtorCode . " - " . ($_item_properties->CompanyName ? $_item_properties->CompanyName : $_item_properties->SurName . ", " . $_item_properties->Initials);
        $reference_link = $export->getPath("debtor") . $_item->ReferenceID;
        break;
    case "creditor":
        $reference_name = $_item_properties->CreditorCode . " - " . ($_item_properties->CompanyName ? $_item_properties->CompanyName : $_item_properties->SurName . ", " . $_item_properties->Initials);
        $reference_link = $export->getPath("creditor") . $_item->ReferenceID;
        break;
    case "product":
        $reference_name = $_item_properties->ProductCode . " - " . $_item_properties->ProductName;
        $reference_link = $export->getPath("product") . $_item->ReferenceID;
        break;
    case "invoice":
        $reference_name = __("invoice") . " " . $_item_properties->InvoiceCode;
        $reference_link = $export->getPath("invoice") . $_item->ReferenceID;
        break;
    case "creditinvoice":
        $reference_name = __("view creditinvoice") . " " . $_item_properties->CreditInvoiceCode;
        $reference_link = $export->getPath("creditinvoice") . $_item->ReferenceID;
        break;
    case "sddbatch":
        $reference_name = __("direct debit batch") . " " . $_item_properties->BatchID;
        $reference_link = $export->getPath("directdebit") . $_item_properties->BatchID;
        break;
    case "payment_invoice":
        $_item_properties = new invoice();
        $_item_properties->Identifier = $_item->ReferenceID;
        $_item_properties->show();
        $reference_name = __("invoice") . " " . $_item_properties->InvoiceCode;
        $reference_link = $export->getPath("invoice") . $_item->ReferenceID;
        break;
    case "payment_purchase":
        $_item_properties = new creditinvoice();
        $_item_properties->Identifier = $_item->ReferenceID;
        $_item_properties->show();
        $reference_name = __("view creditinvoice") . " " . ($_item_properties->InvoiceCode ? $_item_properties->InvoiceCode : $_item_properties->CreditInvoiceCode);
        $reference_link = $export->getPath("creditinvoice") . $_item->ReferenceID;
        break;
    default:
        echo "<div class=\"block manual_action_block\" data-exporttype=\"";
        echo $_item->Type;
        echo "\" data-referenceid=\"";
        echo $_item->ReferenceID;
        echo "\">\n\t<div class=\"item_avatar float_left\">\n\t\t<span class=\"";
        echo $_item->Type;
        echo "\"></span>\n\t</div>\n\n\t<div class=\"action_link\">\n\t\t<span class=\"ignore\" title=\"";
        echo __("export accounting - manual action ignore action");
        echo "\">x</span>\n\t\t<a class=\"retry_link has_loading_button\">";
        echo in_array($_item->Type, ["payment_invoice", "payment_purchase"]) ? __("export accounting - manual action retry one import") : __("export accounting - manual action retry one");
        echo "</a>\n\t\t<div class=\"loading_btn hide float_right\" style=\"margin-top:7px\"><img src=\"";
        echo __SITE_URL;
        echo "/images/loadinfo.gif\" />";
        echo __("export accounting loading");
        echo "</div>\n\n\t</div>\n\n\t<div class=\"item_reference\">\n\t\t<a href=\"";
        echo $reference_link;
        echo "\" class=\"strong ellipsis\" style=\"display:inline-block;max-width: 250px;\">";
        echo htmlspecialchars($reference_name);
        echo "</a>\n\t\t<span class=\"c_gray\" style=\"display:block;\">";
        echo rewrite_date_db2site($_item->ExportedAt) . " " . __("at") . " " . rewrite_date_db2site($_item->ExportedAt, "%H:%i");
        echo "</span>\n\t</div>\n\n\t<div class=\"item_message\">\n\t\t<span";
        if($_item->Status != "paid_diff") {
            echo " class=\"c_red\"";
        }
        echo ">\n\t\t\t";
        if(is_array($_item->Message)) {
            foreach ($_item->Message as $_msg) {
                echo "<strong class=\"strong\">" . $package_information["name"] . ":</strong> " . $_msg . "<br />";
            }
        } else {
            echo __("export accounting - manual action unknown error");
        }
        echo "\t\t</span>\n\t\t";
        if($_item->Status != "paid_diff") {
            echo "<span>";
            echo $_item->Suggestion ? "<strong class=\"strong\">" . __("export accounting - suggestion") . ":</strong> " . $_item->Suggestion : "";
            echo "</span>";
        }
        echo "\t</div>\n\n\n</div>";
}

?>