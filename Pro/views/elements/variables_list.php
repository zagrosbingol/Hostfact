<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "<div id=\"variable_list\">\n";
if(isset($variable_list_show_invoice) && $variable_list_show_invoice) {
    echo "\t<h3><a>";
    echo __("invoice");
    echo "</a></h3>\n\t<div style=\"max-height:200px;\">\n\t\t<a>";
    echo __("companyname");
    echo "<span>[invoice-&gt;CompanyName]</span></a>\n\t\t<a>";
    echo __("taxnumber");
    echo "<span>[invoice->TaxNumber]</span></a>\n\t\t<a>";
    echo __("initials");
    echo "<span>[invoice-&gt;Initials]</span></a> \n\t\t<a>";
    echo __("surname");
    echo "<span>[invoice-&gt;SurName]</span></a> \n\t\t<a>";
    echo __("address");
    echo " 1<span>[invoice-&gt;Address]</span></a>\n\t\t<a>";
    echo __("address");
    echo " 2<span>[invoice-&gt;Address2]</span></a>\n\t\t<a>";
    echo __("zipcode");
    echo "<span>[invoice-&gt;ZipCode]</span></a>\n\t\t<a>";
    echo __("city");
    echo "<span>[invoice-&gt;City]</span></a>\n\t\t<a>";
    echo __("state");
    echo "<span>[invoice-&gt;StateName]</span></a>\n\t\t<a>";
    echo __("country");
    echo "<span>[invoice-&gt;CountryLong]</span></a>\n\t\t<a>";
    echo __("emailaddress");
    echo "<span>[invoice-&gt;EmailAddress]</span></a>\n\t\t\n\t\t<a>";
    echo __("invoicecode");
    echo "<span>[invoice-&gt;InvoiceCode]</span></a>\n\t\t<a>";
    echo __("invoicedate");
    echo "<span>[invoice-&gt;Date]</span></a>\n\t\t<a>";
    echo __("term of payment");
    echo "<span>[invoice-&gt;Term]</span></a>\n\t\t<a>";
    echo __("pay before date");
    echo "<span>[invoice-&gt;PayBefore]</span></a>\n\t\t<a>";
    echo __("reference number");
    echo "<span>[invoice-&gt;ReferenceNumber]</span></a>\n\t\t<a>";
    echo __("description");
    echo "<span>[invoice-&gt;Description]</span></a>\n\t\t<a>";
    echo __("invoice discount percentage");
    echo "<span>[invoice-&gt;Discount]</span></a>\n\t\t\n\t\t<a>";
    echo __("templateblock totaltype pagetotalexcl");
    echo "<span>[invoice-&gt;PageTotalExcl]</span></a>\n\t\t<a>";
    echo __("templateblock totaltype pagetotalincl");
    echo "<span>[invoice-&gt;PageTotalIncl]</span></a>\n\t\t<a>";
    echo __("invoice total excl vat");
    echo "<span>[invoice-&gt;AmountExcl]</span></a>\n\t\t<a>";
    echo __("invoice total vat");
    echo "<span>[invoice-&gt;AmountTax]</span></a>\n\t\t<a>";
    echo __("invoice total incl vat");
    echo "<span>[invoice-&gt;AmountIncl]</span></a>\n\t\t<a>";
    echo __("open sum");
    echo "<span>[invoice-&gt;PartPayment]</span></a>\n\t\t<a>";
    echo __("already paid");
    echo "<span>[invoice-&gt;AmountPaid]</span></a>\n\t\t<a>";
    echo __("url for online payments");
    echo "<span>[invoice-&gt;PaymentURL]</span></a>\n        <a>";
    echo __("url for online payments");
    echo "<span>[invoice-&gt;PaymentURLRaw]</span></a>\n\t\t<a>";
    echo __("transaction id");
    echo "<span>[invoice-&gt;TransactionID]</span></a>\n\t\t<a>";
    echo __("sdd direct debit date");
    echo "<span>[invoice-&gt;DirectDebitDate]</span></a>\n\t\t<a>";
    echo __("paydate");
    echo "<span>[invoice-&gt;PayDate]</span></a>\n\t</div>\n\t";
}
if(isset($variable_list_show_invoice_elements) && $variable_list_show_invoice_elements) {
    echo "\t<h3><a>";
    echo __("invoicecolumn");
    echo "</a></h3>\n\t<div style=\"max-height:200px;\">\n\t\t<a>";
    echo __("date");
    echo "<span>[invoiceElement-&gt;Date]</span></a>\n\t\t<a>";
    echo __("number");
    echo "<span>[invoiceElement-&gt;Number]</span></a>\n\t\t<a>";
    echo __("product name");
    echo "<span>[invoiceElement-&gt;ProductName]</span></a>\n\t\t<a>";
    echo __("productcode");
    echo "<span>[invoiceElement-&gt;ProductCode]</span></a>\n\t\t<a>";
    echo __("description");
    echo "<span>[invoiceElement-&gt;Description]</span></a>\n\t\t\n\t\t<a>";
    echo __("period start");
    echo "<span>[invoiceElement-&gt;StartPeriod]</span></a>\n\t\t<a>";
    echo __("period end");
    echo "<span>[invoiceElement-&gt;EndPeriod]</span></a>\n\t\t\n\t\t<a>";
    echo __("price excl");
    echo "<span>[invoiceElement-&gt;PriceExcl]</span></a>\n\t\t<a>";
    echo __("price incl");
    echo "<span>[invoiceElement-&gt;PriceIncl]</span></a>\n\t\t<a>";
    echo __("amountexcl");
    echo "<span>[invoiceElement-&gt;AmountExcl]</span></a>\n\t\t<a>";
    echo __("amountincl");
    echo "<span>[invoiceElement-&gt;AmountIncl]</span></a>\n\t\t<a>";
    echo __("vat percentage");
    echo "<span>[invoiceElement-&gt;FullTaxPercentage]</span></a>\n\t\t\n\t\t<a>";
    echo __("invoice discount percentage");
    echo "<span>[invoiceElement-&gt;FullDiscountPercentage]</span></a>\n\t\t<a>";
    echo __("discount amount") . " " . __("excl vat");
    echo "<span>[invoiceElement-&gt;DiscountAmountExcl]</span></a>\n\t\t<a>";
    echo __("discount amount") . " " . __("incl vat");
    echo "<span>[invoiceElement-&gt;DiscountAmountIncl]</span></a>\n\t</div>\n\t";
}
if(isset($variable_list_show_pricequote) && $variable_list_show_pricequote) {
    echo "\t<h3><a>";
    echo __("pricequote");
    echo "</a></h3>\n\t<div style=\"max-height:200px;\">\n\t\t<a>";
    echo __("companyname");
    echo "<span>[invoice-&gt;CompanyName]</span></a>\n\t\t<a>";
    echo __("initials");
    echo "<span>[invoice-&gt;Initials]</span></a> \n\t\t<a>";
    echo __("surname");
    echo "<span>[invoice-&gt;SurName]</span></a> \n\t\t<a>";
    echo __("address");
    echo " 1<span>[invoice-&gt;Address]</span></a>\n\t\t<a>";
    echo __("address");
    echo " 2<span>[invoice-&gt;Address2]</span></a>\n\t\t<a>";
    echo __("zipcode");
    echo "<span>[invoice-&gt;ZipCode]</span></a>\n\t\t<a>";
    echo __("city");
    echo "<span>[invoice-&gt;City]</span></a>\n\t\t<a>";
    echo __("state");
    echo "<span>[invoice-&gt;StateName]</span></a>\n\t\t<a>";
    echo __("country");
    echo "<span>[invoice-&gt;CountryLong]</span></a>\n\t\t<a>";
    echo __("emailaddress");
    echo "<span>[invoice-&gt;EmailAddress]</span></a>\n\t\t<a>";
    echo __("pricequotecode");
    echo "<span>[invoice-&gt;PriceQuoteCode]</span></a>\n\t\t<a>";
    echo __("pricequote date");
    echo "<span>[invoice-&gt;Date]</span></a>\n\t\t<a>";
    echo __("pricequote options term");
    echo "<span>[invoice-&gt;Term]</span></a>\n\t\t<a>";
    echo __("reference number");
    echo "<span>[invoice-&gt;ReferenceNumber]</span></a>\n\t\t<a>";
    echo __("description");
    echo "<span>[invoice-&gt;Description]</span></a>\n\t\t<a>";
    echo __("invoice discount percentage");
    echo "<span>[invoice-&gt;Discount]</span></a>\n\t\t<a>";
    echo __("templateblock totaltype pagetotalexcl");
    echo "<span>[invoice-&gt;PageTotalExcl]</span></a>\n\t\t<a>";
    echo __("templateblock totaltype pagetotalincl");
    echo "<span>[invoice-&gt;PageTotalIncl]</span></a>\n\t\t<a>";
    echo __("invoice total excl vat");
    echo "<span>[invoice-&gt;AmountExcl]</span></a>\n\t\t<a>";
    echo __("invoice total vat");
    echo "<span>[invoice-&gt;AmountTax]</span></a>\n\t\t<a>";
    echo __("invoice total incl vat");
    echo "<span>[invoice-&gt;AmountIncl]</span></a>\n\t\t";
    if(PDF_MODULE == "tcpdf") {
        echo "\t\t<a>";
        echo __("url for online accept estimates");
        echo "<span>[invoice-&gt;AcceptURL]</span></a>\n\t\t<a>";
        echo __("url for online accept estimates");
        echo "<span>[invoice-&gt;AcceptURLRaw]</span></a>\n\t\t";
    }
    echo "\t</div>\n\t";
}
if(isset($variable_list_show_pricequote_elements) && $variable_list_show_pricequote_elements) {
    echo "\t<h3><a>";
    echo __("pricequotecolumn");
    echo "</a></h3>\n\t<div style=\"max-height:200px;\">\n\t\t<a>";
    echo __("date");
    echo "<span>[invoiceElement-&gt;Date]</span></a>\n\t\t<a>";
    echo __("number");
    echo "<span>[invoiceElement-&gt;Number]</span></a>\n\t\t<a>";
    echo __("product name");
    echo "<span>[invoiceElement-&gt;ProductName]</span></a>\n\t\t<a>";
    echo __("productcode");
    echo "<span>[invoiceElement-&gt;ProductCode]</span></a>\n\t\t<a>";
    echo __("description");
    echo "<span>[invoiceElement-&gt;Description]</span></a>\n\t\t\n\t\t<a>";
    echo __("period start");
    echo "<span>[invoiceElement-&gt;StartPeriod]</span></a>\n\t\t<a>";
    echo __("period end");
    echo "<span>[invoiceElement-&gt;EndPeriod]</span></a>\n\t\t\n\t\t<a>";
    echo __("price excl");
    echo "<span>[invoiceElement-&gt;PriceExcl]</span></a>\n\t\t<a>";
    echo __("price incl");
    echo "<span>[invoiceElement-&gt;PriceIncl]</span></a>\n\t\t<a>";
    echo __("amountexcl");
    echo "<span>[invoiceElement-&gt;AmountExcl]</span></a>\n\t\t<a>";
    echo __("amountincl");
    echo "<span>[invoiceElement-&gt;AmountIncl]</span></a>\n\t\t<a>";
    echo __("vat percentage");
    echo "<span>[invoiceElement-&gt;FullTaxPercentage]</span></a>\n\t\t\n\t\t<a>";
    echo __("invoice discount percentage");
    echo "<span>[invoiceElement-&gt;FullDiscountPercentage]</span></a>\n\t\t<a>";
    echo __("discount amount") . " " . __("excl vat");
    echo "<span>[invoiceElement-&gt;DiscountAmountExcl]</span></a>\n\t\t<a>";
    echo __("discount amount") . " " . __("incl vat");
    echo "<span>[invoiceElement-&gt;DiscountAmountIncl]</span></a>\n\t</div>\n\t";
}
echo "<h3><a>";
echo __("debtor information");
echo "</a></h3>\n<div style=\"max-height:200px;\">\n\t<a>";
echo __("debtorcode");
echo "<span>[debtor-&gt;DebtorCode]</span></a>\n\t\n\t<a>";
echo __("username");
echo "<span>[debtor-&gt;Username]</span></a>\n\t<a>";
echo __("password");
echo "<span>[debtor-&gt;Password]</span></a>\n\t\n\t<a>";
echo __("companyname");
echo "<span>[debtor-&gt;CompanyName]</span></a>\n\t<a>";
echo __("company number");
echo "<span>[debtor-&gt;CompanyNumber]</span></a>\n\t<a>";
echo __("taxnumber");
echo "<span>[debtor-&gt;TaxNumber]</span></a>\n\n\t<a>";
echo __("initials");
echo "<span>[debtor-&gt;Initials]</span></a>\n\t<a>";
echo __("surname");
echo "<span>[debtor-&gt;SurName]</span></a>\n\t<a>";
echo __("address");
echo " 1<span>[debtor-&gt;Address]</span></a>\n\t<a>";
echo __("address");
echo " 2<span>[debtor-&gt;Address2]</span></a>\n\t<a>";
echo __("zipcode");
echo "<span>[debtor-&gt;ZipCode]</span></a>\n\t<a>";
echo __("city");
echo "<span>[debtor-&gt;City]</span></a>\n\t<a>";
echo __("state");
echo "<span>[debtor-&gt;StateName]</span></a>\n\t<a>";
echo __("country");
echo "<span>[debtor-&gt;CountryLong]</span></a>\n\t\n\t<a>";
echo __("phonenumber");
echo "<span>[debtor-&gt;PhoneNumber]</span></a>\n\t<a>";
echo __("faxnumber");
echo "<span>[debtor-&gt;FaxNumber]</span></a>\n\t<a>";
echo __("mobilenumber");
echo "<span>[debtor-&gt;MobileNumber]</span></a>\n\t<a>";
echo __("emailaddress");
echo "<span>[debtor-&gt;EmailAddress]</span></a>\n\t<a>";
echo __("website");
echo "<span>[debtor-&gt;Website]</span></a>\n\t\n\t<a>";
echo __("account number");
echo "<span>[debtor-&gt;AccountNumber]</span></a>\n\t<a>";
echo __("bicswift");
echo "<span>[debtor-&gt;AccountBIC]</span></a>\n\t<a>";
echo __("account name");
echo "<span>[debtor-&gt;AccountName]</span></a>\n\t<a>";
echo __("bank");
echo "<span>[debtor-&gt;AccountBank]</span></a>\n\t<a>";
echo __("bank city");
echo "<span>[debtor-&gt;AccountCity]</span></a>\n\t\n\t<a>";
echo __("outstanding amount excl tax");
echo "<span>[debtor-&gt;OpenAmountExcl]</span></a>\n\t<a>";
echo __("outstanding amount incl tax");
echo "<span>[debtor-&gt;OpenAmountIncl]</span></a>\n\n    ";
if(isset($variable_list_show_email) && $variable_list_show_email) {
    echo "\t\t<a>";
    echo __("variable - debtor open invoices overview");
    echo "<span>[debtor->OpenInvoicesOverview]</span></a>\n\t\t<a>";
    echo __("variable - debtor open other invoices overview");
    echo "<span>[debtor->OtherOpenInvoicesOverview]</span></a>\n        ";
}
echo "\n\t<a>";
echo __("sdd mandate id");
echo "<span>[debtor-&gt;MandateID]</span></a>\n</div>\n";
require_once "class/debtor.php";
require_once "class/invoice.php";
require_once "class/pricequote.php";
$debtor = new debtor();
$invoice = new invoice();
$pricequote = new pricequote();
if(!empty($debtor->customfields_list) || !empty($invoice->customfields_list) || !empty($pricequote->customfields_list)) {
    echo "\t<h3><a>";
    echo __("custom client fields");
    echo "</a></h3>\n\t<div style=\"max-height:200px;\">\n        ";
    $custom_fields = [];
    foreach ($debtor->customfields_list as $k => $custom_field) {
        $custom_fields[] = $custom_field["FieldCode"];
        echo "<a>";
        echo $custom_field["LabelTitle"];
        echo "<span>[custom-&gt;";
        echo $custom_field["FieldCode"];
        echo "]</span></a>";
    }
    foreach ($invoice->customfields_list as $k => $custom_field) {
        if(!in_array($custom_field["FieldCode"], $custom_fields)) {
            $custom_fields[] = $custom_field["FieldCode"];
            echo "<a>";
            echo $custom_field["LabelTitle"];
            echo "<span>[custom-&gt;";
            echo $custom_field["FieldCode"];
            echo "]</span></a>";
        }
    }
    foreach ($pricequote->customfields_list as $k => $custom_field) {
        if(!in_array($custom_field["FieldCode"], $custom_fields)) {
            $custom_fields[] = $custom_field["FieldCode"];
            echo "<a>";
            echo $custom_field["LabelTitle"];
            echo "<span>[custom-&gt;";
            echo $custom_field["FieldCode"];
            echo "]</span></a>";
        }
    }
    echo "\t</div>\n\t";
}
if(isset($variable_list_show_subscriptions) && $variable_list_show_subscriptions) {
    echo "\t<h3><a>";
    echo __("subscriptions");
    echo "</a></h3>\n\t<div style=\"max-height:200px;\">\n\t\t\n\t\t<a>";
    echo __("number of subscriptions");
    echo "<span>[periodic-&gt;count]</span></a>\n\t\t<a>";
    echo __("next invoice at date");
    echo "<span>[periodic-&gt;NextDate]</span></a>\n\t\t\n\t\t<a>";
    echo __("subscription list start");
    echo "<span>[START:periodics]</span></a>\n\t\t<a>&nbsp;&nbsp;";
    echo __("description");
    echo "<span>[periodicElement-&gt;Description]</span></a>\n\t\t<a>&nbsp;&nbsp;";
    echo __("amountexcl");
    echo "<span>[periodicElement-&gt;AmountExcl]</span></a>\n\t\t<a>&nbsp;&nbsp;";
    echo __("amountincl");
    echo "<span>[periodicElement-&gt;AmountIncl]</span></a>\n        <a>&nbsp;&nbsp;";
    echo __("periodic element startperiod");
    echo "<span>[periodicElement-&gt;StartPeriod]</span></a>\n        <a>&nbsp;&nbsp;";
    echo __("periodic element endperiod");
    echo "<span>[periodicElement-&gt;EndPeriod]</span></a>\n\t\t<a>";
    echo __("subscription list end");
    echo "<span>[END:periodics]</span></a>\n\t\t\t\t\n\t\t<a>";
    echo __("invoice total excl vat");
    echo "<span>[periodic-&gt;totalAmountExcl]</span></a>\n\t\t<a>";
    echo __("invoice total vat");
    echo "<span>[periodic-&gt;totalAmountTax]</span></a>\n\t\t<a>";
    echo __("invoice total incl vat");
    echo "<span>[periodic-&gt;totalAmountIncl]</span></a>\n\t\t\n\t</div>\n\t";
}
if(isset($variable_list_show_services) && $variable_list_show_services) {
    $service_variables = [];
    $service_variables["domain"] = [];
    $service_variables["domain"]["header"] = __("domain");
    $service_variables["domain"]["variables"][] = ["name" => __("domain"), "var" => "[domain->Domain]"];
    $service_variables["domain"]["variables"][] = ["name" => __("topleveldomain"), "var" => "[domain->Tld]"];
    $service_variables["domain"]["variables"][] = ["name" => __("domain authcode"), "var" => "[domain->AuthKey]"];
    $service_variables["domain"]["variables"][] = ["name" => __("domain registration date"), "var" => "[domain->RegistrationDate]"];
    $service_variables["domain"]["variables"][] = ["name" => __("expires at"), "var" => "[domain->ExpirationDate]"];
    $service_variables["domain"]["variables"][] = ["name" => __("nameserver 1"), "var" => "[domain->DNS1]"];
    $service_variables["domain"]["variables"][] = ["name" => __("nameserver 2"), "var" => "[domain->DNS2]"];
    $service_variables["domain"]["variables"][] = ["name" => __("nameserver 3"), "var" => "[domain->DNS3]"];
    $service_variables["hosting"] = [];
    $service_variables["hosting"]["header"] = __("hosting");
    $service_variables["hosting"]["variables"][] = ["name" => __("username"), "var" => "[hosting->Username]"];
    $service_variables["hosting"]["variables"][] = ["name" => __("password"), "var" => "[hosting->Password]"];
    $service_variables["hosting"]["variables"][] = ["name" => __("domain"), "var" => "[hosting->Domain]"];
    $service_variables["hosting"]["variables"][] = ["name" => __("package"), "var" => "[hosting->PackageName]"];
    $service_variables["hosting"]["variables"][] = ["name" => __("discspace"), "var" => "[hosting->DiscSpace]"];
    $service_variables["hosting"]["variables"][] = ["name" => __("traffic"), "var" => "[hosting->BandWidth]"];
    if($variable_list_show_services !== "servicetype_only") {
        $service_variables["hosting"]["variables"][] = ["name" => __("server name"), "var" => "[server->Name]"];
        $service_variables["hosting"]["variables"][] = ["name" => __("server location"), "var" => "[server->Location]"];
        $service_variables["hosting"]["variables"][] = ["name" => __("ip address"), "var" => "[server->IP]"];
        $service_variables["hosting"]["variables"][] = ["name" => __("portnumber"), "var" => "[server->Port]"];
    }
    $service_variables = do_filter("service_templates_variables", $service_variables);
    foreach ($service_variables as $key => $service) {
        echo "        <h3><a>";
        echo $service["header"];
        echo "</a></h3>\n    \t<div style=\"max-height:200px;\">\n            ";
        foreach ($service["variables"] as $variable) {
            echo "                    <a>";
            echo $variable["name"];
            echo "<span>";
            echo htmlspecialchars($variable["var"]);
            echo "</span></a>\n                    ";
        }
        echo "    \t</div>\n        ";
    }
}
if(isset($variable_list_show_ticket) && $variable_list_show_ticket) {
    echo "\t<h3><a>";
    echo __("ticket system");
    echo "</a></h3>\n\t<div style=\"max-height:200px;\">\n\t\t<a>";
    echo __("ticket no");
    echo "<span>[ticket-&gt;TicketID]</span></a>\n\t\t<a>";
    echo __("subject");
    echo "<span>[ticket-&gt;Subject]</span></a>\n        <a>";
    echo __("ticket message");
    echo "<span>[ticketmessage-&gt;Message]</span></a>\n\t</div>\n\t";
}
echo "<h3><a>";
echo __("company data");
echo "</a></h3>\n<div style=\"max-height:200px;\">\n\t<a>";
echo __("companyname");
echo "<span>[company-&gt;CompanyName]</span></a>\n\t<a>";
echo __("company number");
echo "<span>[company-&gt;CompanyNumber]</span></a>\n\t<a>";
echo __("taxnumber");
echo "<span>[company-&gt;TaxNumber]</span></a>\n\t\n\t<a>";
echo __("address");
echo " 1<span>[company-&gt;Address]</span></a>\n\t<a>";
echo __("address");
echo " 2<span>[company-&gt;Address2]</span></a>\n\t<a>";
echo __("zipcode");
echo "<span>[company-&gt;ZipCode]</span></a>\n\t<a>";
echo __("city");
echo "<span>[company-&gt;City]</span></a>\n\t<a>";
echo __("state");
echo "<span>[company-&gt;StateName]</span></a>\n\t<a>";
echo __("country");
echo "<span>[company-&gt;CountryLong]</span></a>\n\t\n\t<a>";
echo __("phonenumber");
echo "<span>[company-&gt;PhoneNumber]</span></a>\n\t<a>";
echo __("faxnumber");
echo "<span>[company-&gt;FaxNumber]</span></a>\n\t<a>";
echo __("mobilenumber");
echo "<span>[company-&gt;MobileNumber]</span></a>\n\t<a>";
echo __("emailaddress");
echo "<span>[company-&gt;EmailAddress]</span></a>\n\t<a>";
echo __("website");
echo "<span>[company-&gt;Website]</span></a>\n\t\n\t<a>";
echo __("account number");
echo "<span>[company-&gt;AccountNumber]</span></a>\n\t<a>";
echo __("bicswift");
echo "<span>[company-&gt;AccountBIC]</span></a>\n\t<a>";
echo __("account name");
echo "<span>[company-&gt;AccountName]</span></a>\n\t<a>";
echo __("bank");
echo "<span>[company-&gt;AccountBank]</span></a>\n\t<a>";
echo __("bank city");
echo "<span>[company-&gt;AccountCity]</span></a>\n\t\n\t<a>";
echo __("employee") . " " . strtolower(__("name"));
echo "<span>[account-&gt;Name]</span></a>\n\t<a>";
echo __("employee") . " " . strtolower(__("emailaddress"));
echo "<span>[account-&gt;EmailAddress]</span></a>\n\t<a>";
echo __("employee") . " " . strtolower(__("phonenumber"));
echo "<span>[account-&gt;PhoneNumber]</span></a>\n\t<a>";
echo __("employee") . " " . strtolower(__("signature"));
echo "<span>[account-&gt;Signature]</span></a>\n</div>\n\n";
if(isset($variable_list_show_email) && $variable_list_show_email) {
    echo "\t<h3><a>";
    echo __("terminations");
    echo "</a></h3>\n\t<div style=\"max-height:200px;\">\n\t\t<a>";
    echo __("termination date");
    echo "<span>[termination-&gt;TerminationDate]</span></a>\n\t\t<a>";
    echo __("termination service name");
    echo "<span>[termination-&gt;ServiceName]</span></a>\n\t</div>\n\t";
}
echo "\n";
if(isset($variable_list_show_email) && $variable_list_show_email || isset($variable_list_show_pdf) && $variable_list_show_pdf) {
    echo "\t<h3><a>";
    echo __("tab_other");
    echo "</a></h3>\n\t<div style=\"max-height:200px;\">\n\t\t";
    if(isset($variable_list_show_email) && $variable_list_show_email) {
        echo "\t\t\t<a>";
        echo __("current date in email");
        echo "<span>[email-&gt;Today]</span></a>\n\t\t\t";
    }
    echo "\t\t";
    if(isset($variable_list_show_pdf) && $variable_list_show_pdf) {
        echo "\t\t\t<a>";
        echo __("current date in pdf");
        echo "<span>[pdf-&gt;Today]</span></a>\n\t\t\t<a>";
        echo __("current page number");
        echo "<span>[pdf-&gt;CurrentPageNumber]</span></a>\n\t\t\t<a>";
        echo __("total page numbers");
        echo "<span>[pdf-&gt;TotalPageNumber]</span></a>\n\t\t\t";
    }
    echo "\t</div>\n\t";
}
echo "\n<h3><a>";
echo __("conditions");
echo "</a></h3>\n<div style=\"max-height:200px;\">\n\t";
if(isset($variable_list_show_invoice_elements) && $variable_list_show_invoice_elements || isset($variable_list_show_pricequote_elements) && $variable_list_show_pricequote_elements) {
    echo "\t\t<a>";
    echo __("condition - period");
    echo "<span>[period][/period]</span></a>\n\t\t<a>";
    echo __("condition - linediscount");
    echo "<span>[linediscount][/linediscount]</span></a>\n\t";
}
echo "\t\n\t";
if(isset($variable_list_show_invoice) && $variable_list_show_invoice) {
    echo "\t\t<a>";
    echo __("condition - paid");
    echo "<span>[paid][/paid]</span></a>\n\t\t<a>";
    echo __("condition - unpaid");
    echo "<span>[unpaid][/unpaid]</span></a>\n\t\t<a>";
    echo __("condition - partly paid");
    echo "<span>[partly_paid][/partly_paid]</span></a>\n\t";
}
echo "\t\n\t";
if(isset($variable_list_show_invoice) && $variable_list_show_invoice || isset($variable_list_show_pricequote) && $variable_list_show_pricequote) {
    echo "\t\t<a>";
    echo __("condition - directdebit");
    echo "<span>[directdebit][/directdebit]</span></a>\n\t\t<a>";
    echo __("condition - transfer");
    echo "<span>[transfer][/transfer]</span></a>\n\t\t<a>";
    echo __("condition - credit");
    echo "<span>[credit][/credit]</span></a>\n\t\t<a>";
    echo __("condition - non-credit");
    echo "<span>[non-credit][/non-credit]</span></a>\n\t\t";
    if($company->Country == "NL") {
        echo "<a>";
        echo __("condition - reversecharge");
        echo "<span>[reversecharge][/reversecharge]</span></a>";
    }
    echo "\t\t<a>";
    echo __("condition - reference");
    echo "<span>[reference][/reference]</span></a>\n\t";
}
echo "\t\n\t<a>";
echo __("condition - company");
echo "<span>[is_company][/is_company]</span></a>\n    <a>";
echo __("condition - consumer");
echo "<span>[is_consumer][/is_consumer]</span></a>\n    <a>";
echo __("condition - contact");
echo "<span>[has_contact][/has_contact]</span></a>\n\t<a>";
echo __("condition - male");
echo "<span>[male][/male]</span></a>\n\t<a>";
echo __("condition - female");
echo "<span>[female][/female]</span></a>\n    <a>";
echo __("condition - department");
echo "<span>[department][/department]</span></a>\n\n\t<a>";
echo __("condition - reversecharge");
echo "<span>[reversecharge][/reversecharge]</span></a>\n\n    ";
if(isset($variable_list_show_email) && $variable_list_show_email) {
    echo "\t\t<a>";
    echo __("condition - has other outstanding invoices");
    echo "<span>[has_other_outstanding_invoices][/has_other_outstanding_invoices]</span></a>\n    ";
}
echo "</div>\n</div>\n\n<style type=\"text/css\">\n\t#variable_list { border: 1px solid #cccccc;border-bottom:0px; }\n\t#variable_list div a { display:block; padding:5px;border-bottom:1px solid #eee;line-height:20px;}\n\t#variable_list div a:last-child { border-bottom:0px;}\n\t#variable_list div a:hover {  background-color:#EEE; }\n\t\n\t#variable_list div a span { display:block; font-size:10px; color:#666; float:right;} \n\t#variable_search {}\n\t#variable_search input { background: #fff url('images/ico_find.png') no-repeat 7px center; padding:4px 25px 4px 25px; width:100%; }\n</style>\t\t\t\n\n<script type=\"text/javascript\">\n\$(function(){\n\t\n\t\$('#variable_list div a span').after('<br clear=\"both\" />')\n\t\n\t\$('#variable_list div a').click(function(){\t\t\n\t\t";
if(isset($variable_list_show_email) && $variable_list_show_email) {
    echo "\t\t\t\n\t\t\tvar text = \$.trim(\$(this).find('span').html().replace(/&gt;/g,'>'));\n\t\t\t// E-mail insert\n\t\t\tvar oEditor = CKEDITOR.instances.Message;\n\t\t\tvar html = \"<span>\"+text+\"</span>\";\n\t\t\tvar newElement = CKEDITOR.dom.element.createFromHtml( html, oEditor.document );\n\t\t\toEditor.insertElement( newElement );\n\t\n\t\t";
} else {
    echo "\t\t\twindow.prompt('";
    echo __("copy variable to clipboard");
    echo "', \$(this).find('span').html().replace(/&gt;/g,'>'));\n\t\t";
}
echo "\t\t\n\t});\n\t\n\t\$('input[name=\"SearchVariable\"]').keyup(function(){\n\t\tif(\$(this).val() == '')\n\t\t{\n\t\t\t\$('#variable_list div a').removeClass('match').show();\n\t\t\t\$('#variable_list h3').show();\n\t\t}\n\t\telse\n\t\t{\n\t\t\t\$('#variable_list div').each(function(index_div, element_div){\n\t\t\t\t\n\t\t\t\t\$(element_div).find('a.nomatch').hide();\n\t\t\t\t\n\t\t\t\t\$(element_div).find('a').each(function(index, element){\n\t\t\t\t\n\t\t\t\t\tvar tmp_var = \$(element).html().replace('<span>','').replace('</span>','').toLowerCase();\n\t\t\t\t\t\n\t\t\t\t\tif(tmp_var.indexOf(htmlspecialchars(\$('input[name=\"SearchVariable\"]').val().toLowerCase())) >= 0)\n\t\t\t\t\t{\n\t\t\t\t\t\t\$(element).addClass('match').show();\n\t\t\t\t\t}\n\t\t\t\t\telse\n\t\t\t\t\t{\n\t\t\t\t\t\t\$(element).removeClass('match').hide();\n\t\t\t\t\t}\n\t\t\t\t});\n\t\t\t\t\n\t\t\t\tif(\$(element_div).find('a.match').length == 0)\n\t\t\t\t{\n\t\t\t\t\t\$(element_div).prev().hide();\n\t\t\t\t}\n\t\t\t\telse\n\t\t\t\t{\n\t\t\t\t\t\$(element_div).prev().show();\n\t\t\t\t}\n\t\t\t});\n\t\t}\n\t});\n\t\n\t\$('input[name=\"SearchVariable\"]').change(function(){\n\t\tif(\$(this).val() == '')\n\t\t{\n\t\t\treturn false;\t\n\t\t}\n\t\tif(\$('#variable_list h3.ui-state-active').is(':hidden'))\n\t\t{\n\t\t\t\$( \"#variable_list\" ).accordion( \"option\", \"active\", \$('#variable_list h3').index(\$('#variable_list h3:visible')) );\n\t\t}\n\t});\n});\n</script>\t";

?>