<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
include_once "connect.php";
$suffix = defined("DB_NAME") ? substr(md5(DB_NAME), 0, 8) : "";
$sessionPrefix = defined("SESSION_PREFIX") ? SESSION_PREFIX : "hfb";
session_name($sessionPrefix . $suffix);
$current_session_params = session_get_cookie_params();
$http_only = true;
!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off" or $secure_flag = !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off" || $_SERVER["SERVER_PORT"] == 443;
session_set_cookie_params($current_session_params["lifetime"], $current_session_params["path"], $current_session_params["domain"], $secure_flag, $http_only);
session_start();
$arg1 = isset($_POST["arg1"]) ? $_POST["arg1"] : "";
$arg2 = isset($_POST["arg2"]) ? $_POST["arg2"] : "";
$arg3 = isset($_POST["arg3"]) ? $_POST["arg3"] : "";
$allowed = [];
$allowed["account.overview"] = ["sort" => ["UserName", "Name", "Function", "LastDate", "TwoFactorAuthentication", "Language"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100", "99999"]];
$allowed["agenda.overview"] = ["period" => true, "move" => true, "status" => true, "today" => true, "search" => true];
$allowed["backup.overview"] = ["sort" => ["Date", "FileName", "Version", "Name"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"]];
$allowed["creditor.overview"] = ["sort" => ["CreditorCode", "Creditor", "EmailAddress", "AmountUnpaid"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"], "group" => true, "searchfor" => true];
$allowed["creditor.show.invoice"] = ["sort" => ["CreditInvoiceCode", "InvoiceCode", "Date", "AmountExcl", "AmountIncl", "Status", "ReferenceNumber"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"]];
$allowed["creditorgroup.overview"] = ["sort" => ["GroupName"], "order" => ["ASC", "DESC"]];
$allowed["creditorgroup.add"] = ["sort" => ["CreditorCode", "Creditor", "Address", "EmailAddress"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"]];
$allowed["creditorgroup.show"] = ["sort" => ["CreditorCode", "Creditor", "Address", "EmailAddress"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"]];
$allowed["creditinvoice.overview"] = ["sort" => ["CreditInvoiceCode", "InvoiceCode", "Date", "AmountExcl", "AmountIncl", "Status", "Creditor", "ReferenceNumber"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"], "status" => true, "searchfor" => true];
$allowed["creditinvoice.show.logfile"] = ["sort" => ["Date", "Action", "Who"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"]];
$allowed["debtor.overview"] = ["sort" => ["DebtorCode", "Debtor", "EmailAddress"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"], "group" => true, "searchfor" => true];
$allowed["debtor.show.invoice"] = ["sort" => ["InvoiceCode", "Date", "AmountExcl", "AmountIncl", "Status", "ReferenceNumber"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"]];
$allowed["debtor.show.pricequote"] = ["sort" => ["PriceQuoteCode", "Date", "AmountExcl", "AmountIncl", "Status", "ReferenceNumber"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "100"]];
$allowed["debtor.show.order"] = ["sort" => ["OrderCode", "Debtor", "AmountExcl", "AmountIncl", "Date", "Status"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "100"]];
$allowed["debtor.show.subscription"] = ["sort" => ["Description", "ProductName", "PeriodicType", "NextDate", "PriceExcl", "PriceIncl", "StartPeriod"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "100"], "status" => true];
$allowed["debtor.show.domain"] = ["sort" => ["Domain", "RegistrationDate", "ExpirationDate", "Registrar", "Status", "PeriodicID"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "100"]];
$allowed["debtor.show.hosting"] = ["sort" => ["Username", "Debtor", "Domain", "Server", "Package", "Status"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "100"]];
$allowed["debtor.show.other"] = ["sort" => ["ProductCode", "Description", "ProductName", "EndContract"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "100"]];
$allowed["debtor.show.handle"] = ["sort" => ["Handle", "Name", "CompanyName", "SurName"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "100"]];
$allowed["debtor.show.ticket"] = ["sort" => ["TicketID", "LastName", "Subject", "Owner", "LastDate", "Priority", "Status"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "100"]];
$allowed["debtor.show.ticket.closed"] = ["sort" => ["TicketID", "LastName", "Subject", "Owner", "LastDate", "Priority", "Status"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "100"]];
$allowed["debtorgroup.overview"] = ["sort" => ["GroupName"], "order" => ["ASC", "DESC"]];
$allowed["debtorgroup.add"] = ["sort" => ["DebtorCode", "Debtor", "Address", "EmailAddress"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"]];
$allowed["debtorgroup.show"] = ["sort" => ["DebtorCode", "Debtor", "Address", "EmailAddress"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"]];
$allowed["discount.overview"] = ["sort" => ["Name", "StartDate", "EndDate", "Counter"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"]];
$allowed["domain.overview"] = ["sort" => ["Domain", "Debtor", "RegistrationDate", "ExpirationDate", "Registrar", "Status", "PeriodicID"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"], "status" => true, "searchfor" => true];
$allowed["domain.show.logfile"] = ["sort" => ["Date", "Action", "Who"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"]];
$allowed["emails.overview0"] = ["sort" => ["SentDate", "Subject", "Recipient", "Status"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"], "status" => true];
$allowed["emails.overview8"] = $allowed["emails.overview0"];
$allowed["emails.overview1"] = $allowed["emails.overview8"];
$allowed["handle.overview"] = ["sort" => ["Handle", "Name", "Debtor", "CompanyName", "SurName"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "100"]];
$allowed["hosting.overview"] = ["sort" => ["Username", "Debtor", "Domain", "Server", "Package", "Status", "PriceExcl", "PeriodicID"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"], "status" => true, "searchfor" => true];
$allowed["hosting.show.logfile"] = ["sort" => ["Date", "Action", "Who"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"]];
$allowed["invoice.overview"] = ["sort" => ["InvoiceCode", "Debtor", "AmountExcl", "AmountIncl", "Date", "ReferenceNumber", "Status"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "100"], "status" => true, "searchfor" => true];
$allowed["invoice.show.logfile"] = ["sort" => ["Date", "Action", "Who"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"]];
$allowed["invoice.dashboard.waiting"] = ["sort" => ["InvoiceCode", "Debtor", "AmountExcl", "AmountIncl", "ReferenceNumber", "Date"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "100"], "status" => true, "searchfor" => true];
$allowed["invoice.dashboard.open"] = ["sort" => ["InvoiceCode", "Debtor", "AmountExcl", "AmountIncl", "Date", "ReferenceNumber"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "100"], "status" => true, "searchfor" => true];
$allowed["invoice.dashboard.waiting_c"] = ["sort" => ["InvoiceCode", "Debtor", "AmountExcl", "AmountIncl", "Date", "ReferenceNumber"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "100"], "status" => true, "searchfor" => true];
$allowed["invoice.dashboard.creditinvoice"] = ["sort" => ["CreditInvoiceCode", "InvoiceCode", "Debtor", "AmountExcl", "AmountIncl", "Date", "PayBefore"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "100"], "status" => true, "searchfor" => true];
$allowed["logfile.overview"] = ["sort" => ["Date", "Who"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"], "status" => true];
$allowed["api.show.logfile"] = ["sort" => ["DateTime", "Controller", "Action", "ResponseType", "IP"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"], "searchfor" => true, "responseType" => ["", "error", "success", "false"]];
$allowed["modification.debtor.overview"] = ["sort" => ["DebtorCode", "LastModification"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"]];
$allowed["modification.domain.overview"] = ["sort" => ["Domain", "LastModification"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"]];
$allowed["order.overview"] = ["sort" => ["OrderCode", "Debtor", "AmountExcl", "AmountIncl", "Date", "Status"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "100"], "status" => true, "searchfor" => true];
$allowed["mailing.add"] = ["sort" => ["DebtorCode", "CompanyName", "SurName", "EmailAddress"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "100"]];
$allowed["package.overview"] = ["sort" => ["PackageName", "PackageType", "TemplateName", "BandWidth", "DiscSpace"], "order" => ["ASC", "DESC"]];
$allowed["package.show.accounts"] = ["sort" => ["Username", "Debtor", "Domain", "Server", "Package", "Status", "PriceExcl"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"]];
$allowed["pricequote.overview"] = ["sort" => ["PriceQuoteCode", "Debtor", "AmountExcl", "AmountIncl", "Date", "Status", "ReferenceNumber"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "100"], "status" => true, "searchfor" => true];
$allowed["pricequote.show.logfile"] = ["sort" => ["Date", "Action", "Who"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"]];
$allowed["product.overview"] = ["sort" => ["ProductCode", "ProductName", "ProductType", "PriceExcl", "PriceIncl", "Sold"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"], "group" => true, "searchfor" => true];
$allowed["product.show.subscription"] = ["sort" => ["Description", "ProductName", "Debtor", "NextDate", "AmountExcl", "AmountIncl", "StartPeriod"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"]];
$allowed["productgroup.overview"] = ["sort" => ["GroupName"], "order" => ["ASC", "DESC"]];
$allowed["productgroup.add"] = ["sort" => ["ProductCode", "ProductName", "PriceExcl"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"]];
$allowed["productgroup.show"] = ["sort" => ["ProductCode", "ProductName", "PriceExcl"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"]];
$allowed["recipients.add"] = ["sort" => ["DebtorCode", "CompanyName", "SurName", "EmailAddress", "Mailing"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"], "searchfor" => true];
$allowed["registrar.overview"] = ["sort" => ["Name", "Testmode", "User"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"]];
$allowed["registrar.show.domain"] = ["sort" => ["Domain", "Debtor", "RegistrationDate", "ExpirationDate", "Status", "PeriodicID"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"]];
$allowed["registrar.show.handle"] = ["sort" => ["Handle", "Debtor", "RegistrarHandle", "CompanyName", "SurName"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"]];
$allowed["search.debtor"] = ["sort" => ["DebtorCode", "Debtor", "EmailAddress", "CompanyName", "SurName"], "order" => ["ASC", "DESC"], "results" => ["10", "25", "50", "100"], "searchfor" => true];
$allowed["search.domain"] = ["sort" => ["Domain", "Status"], "order" => ["ASC", "DESC"], "results" => ["10", "25", "50", "100"], "searchfor" => true];
$allowed["search.creditor"] = ["sort" => ["CreditorCode", "Creditor", "EmailAddress"], "order" => ["ASC", "DESC"], "results" => ["10"], "searchfor" => true];
$allowed["search.handle"] = ["sort" => ["Handle", "Name", "CompanyName", "SurName"], "order" => ["ASC", "DESC"], "results" => ["10", "25", "50", "100"], "searchfor" => true];
$allowed["search.hosting"] = ["sort" => ["Username", "Domain", "Status"], "order" => ["ASC", "DESC"], "results" => ["10", "25", "50", "100"], "searchfor" => true];
$allowed["search.tld"] = ["sort" => ["Tld", "Name", "WhoisServer"], "order" => ["ASC", "DESC"], "results" => ["10"], "searchfor" => true];
$allowed["server.overview"] = ["sort" => ["Name", "Panel", "Location", "IP"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100", "99999"]];
$allowed["server.show.accounts"] = ["sort" => ["Username", "Debtor", "Domain", "Status", "PriceExcl", "Package"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"]];
$allowed["server.show.packages"] = ["sort" => ["PackageName", "PackageType", "ProductCode", "TemplateName"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"]];
$allowed["server.show.debtors"] = ["sort" => ["DebtorCode", "Debtor", "EmailAddress"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"]];
$allowed["server.show.plesk"] = ["sort" => ["DebtorCode", "Debtor", "EmailAddress", "ClientID"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"]];
$allowed["service.overview"] = ["sort" => ["Description", "ProductName", "Debtor", "EndContract", "Status"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"], "status" => true, "searchfor" => true];
$allowed["service.show.invoice"] = ["sort" => ["InvoiceCode", "Debtor", "AmountExcl", "AmountIncl", "Date", "ReferenceNumber", "Status"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "100"], "status" => true, "searchfor" => true];
$allowed["subscription.overview"] = ["sort" => ["Description", "ProductName", "PeriodicType", "Debtor", "NextDate", "PriceExcl", "PriceIncl", "StartPeriod"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "100"], "status" => true];
$allowed["template.invoices.overview"] = ["sort" => ["Name", "Location", "PostLocation"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"]];
$allowed["template.pricequotes.overview"] = ["sort" => ["Name", "Location", "PostLocation"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"]];
$allowed["template.other.overview"] = ["sort" => ["Name", "Location", "PostLocation"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"]];
$allowed["template.emails.overview"] = ["sort" => ["Name", "Subject", "Sender"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"]];
$allowed["ticket.overview"] = ["sort" => ["TicketID", "LastName", "Subject", "Debtor", "Owner", "LastDate", "Priority", "Status"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "100"], "status" => true];
$allowed["statistics.overview"] = ["period" => true, "move" => true, "status" => true, "autorenew" => true, "debtorgroup" => true];
$allowed["topleveldomain.overview"] = ["sort" => ["Tld", "Name", "DomainNumber", "AskForAuthKey", "WhoisServer"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100", "99999"]];
$allowed["topleveldomain.show.domain"] = ["sort" => ["Domain", "Debtor", "RegistrationDate", "ExpirationDate", "Registrar", "Status", "PeriodicID"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "100"]];
$allowed["directdebit.show.logfile"] = ["sort" => ["Date", "Action", "Who"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"]];
$allowed["license.overview"] = ["sort" => ["License", "Debtor", "Package", "Version", "RunDate", "IPv4", "Status", "PriceExcl"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"], "status" => true, "searchfor" => true];
$allowed["license.show.logfile"] = ["sort" => ["Date", "Action", "Who"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "75", "100"]];
$allowed["debtor.show.license"] = ["sort" => ["License", "Debtor", "Package", "Version", "RunDate", "IPv4", "Status", "PriceExcl"], "order" => ["ASC", "DESC"], "results" => ["2", "10", "25", "50", "100"]];
if(in_array($arg1, array_keys($allowed)) && in_array($arg2, array_keys($allowed[$arg1])) && ($allowed[$arg1][$arg2] === true || in_array($arg3, $allowed[$arg1][$arg2]))) {
    if($arg2 == "sort" && isset($_SESSION[$arg1][$arg2]) && $_SESSION[$arg1][$arg2] == $arg3) {
        $_SESSION[$arg1]["order"] = $_SESSION[$arg1]["order"] == "DESC" ? "ASC" : "DESC";
    } elseif($arg2 == "sort") {
        $_SESSION[$arg1]["order"] = "ASC";
    }
    if($arg2 == "status") {
        unset($_SESSION[$arg1]["searchfor"]);
    } elseif($arg2 == "searchfor") {
        unset($_SESSION[$arg1]["searchat"]);
        unset($_SESSION[$arg1]["status"]);
    }
    if($arg1 == "agenda.overview" && $arg2 == "period") {
        unset($_SESSION["agenda.overview"]["StartDate"]);
    }
    echo htmlspecialchars($arg3);
    $_SESSION[$arg1][$arg2] = $arg3;
} elseif(substr($arg1, 0, 17) == "backoffice_table." && strpos($arg2, "filter") !== false) {
    echo "OK";
    $table_id = substr($arg1, 17);
    $_SESSION["backoffice_tables_config"][$table_id][$arg2] = $arg3;
} else {
    echo "BAD";
}

?>