<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class search
{
    public $searchstring;
    public function __construct()
    {
        $this->Error = [];
        $this->Warning = [];
        $this->Success = [];
    }
    public function getResults($table = "", $sort = "", $order = "ASC", $show_results = MAX_RESULTS_LIST, $limit = "-1")
    {
        $result = [];
        $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : min(25, $show_results);
        switch ($table) {
            case "":
            case "HostFact_Debtors":
                if(U_DEBTOR_SHOW) {
                    require_once "class/debtor.php";
                    $this->debtor = new debtor();
                    $fields = $table == "" ? ["DebtorCode", "CompanyName", "Initials", "SurName"] : ["DebtorCode", "CompanyName", "Initials", "SurName", "EmailAddress", "Groups", "PhoneNumber", "MobileNumber", "FaxNumber", "Sex", "Address", "ZipCode", "City", "Country", "OpenAmountIncl"];
                    $searchat = $table == "" ? "DebtorCode|Initials|SurName|CompanyName|PhoneNumber|MobileNumber|EmailAddress" : "DebtorCode|Initials|SurName|CompanyName|Address|ZipCode|City|PhoneNumber|MobileNumber|EmailAddress|AccountNumber|CustomFieldValue";
                    $result["HostFact_Debtors"] = $this->debtor->all($fields, $sort, $order, $limit, $searchat, $this->searchstring, false, $show_results);
                    $session = isset($_SESSION["debtor.overview"]) ? $_SESSION["debtor.overview"] : [];
                    $_SESSION["debtor.overview"]["results"] = $show_results;
                }
                if($table != "") {
                }
                break;
            case "":
            case "HostFact_Invoice":
                if(U_INVOICE_SHOW) {
                    require_once "class/invoice.php";
                    $this->invoice = new invoice();
                    $fields = $table == "" ? ["InvoiceCode", "Debtor", "CompanyName", "SurName", "Initials"] : ["InvoiceCode", "Debtor", "CompanyName", "SurName", "Initials", "AmountExcl", "AmountIncl", "AmountPaid", "Date", "Status", "Address", "ZipCode", "City", "Country", "EmailAddress", "Reminders", "ReminderDate", "Summations", "SummationDate", "Authorisation", "PaymentMethod", "TransactionID", "InvoiceMethod", "AuthTrials", "PayDate", "Comment"];
                    $searchat = $table == "" ? "InvoiceCode|CompanyName|Initials|SurName|EmailAddress|TransactionID" : "InvoiceCode|CompanyName|Initials|SurName|Address|ZipCode|City|EmailAddress|Description|TransactionID|ReferenceNumber|CustomFieldValue";
                    if(defined("CUSTOM_SEARCH_SKIP_INVOICE_LINES") && CUSTOM_SEARCH_SKIP_INVOICE_LINES === true) {
                        $searchat = str_replace("|Description|", "|", $searchat);
                    }
                    if($table == "") {
                        $result["HostFact_Invoice"] = $this->invoice->all($fields, $sort, $order, $limit, $searchat, $this->searchstring, "0|1|2|3|4|8|9", $show_results);
                    } else {
                        $options = $this->invoice->getConfigInvoiceTable();
                        $options["page_total_placeholder"] = "page_total_placeholder_invoices_search";
                        $options["parameters"]["searchat"] = $searchat;
                        $options["parameters"]["searchfor"] = $this->searchstring;
                        $options["filter"] = "0|1|2|3|4|8|9";
                        $options["redirect_url"] = "search.php?page=show";
                        $result["HostFact_Invoice"] = $this->invoice->listInvoices(array_merge($options, $options["parameters"]));
                        $result["HostFact_Invoice"]["options"] = $options;
                    }
                }
                if($table != "") {
                }
                break;
            case "":
            case "HostFact_PriceQuote":
                if(U_PRICEQUOTE_SHOW) {
                    require_once "class/pricequote.php";
                    $this->pricequote = new pricequote();
                    $fields = $table == "" ? ["PriceQuoteCode", "Debtor", "CompanyName", "SurName", "Initials"] : ["PriceQuoteCode", "Debtor", "CompanyName", "SurName", "Initials", "AmountExcl", "AmountIncl", "Date", "Status", "Address", "ZipCode", "City", "Country", "PhoneNumber", "MobileNumber", "EmailAddress", "PriceQuoteMethod", "ReferenceNumber"];
                    $searchat = $table == "" ? "PriceQuoteCode|CompanyName|Initials|SurName|EmailAddress|ReferenceNumber" : "PriceQuoteCode|CompanyName|Initials|SurName|Address|ZipCode|City|EmailAddress|Description|ReferenceNumber|CustomFieldValue";
                    $result["HostFact_PriceQuote"] = $this->pricequote->all($fields, !$sort ? "Date` DESC, `PriceQuoteCode" : $sort, !$sort ? "DESC" : $order, $limit, $searchat, $this->searchstring, false, $show_results);
                    $session = isset($_SESSION["pricequote.overview"]) ? $_SESSION["pricequote.overview"] : [];
                    $_SESSION["pricequote.overview"]["results"] = $show_results;
                }
                if($table != "") {
                }
                break;
            case "":
            case "HostFact_Domains":
                if(U_DOMAIN_SHOW) {
                    require_once "class/domain.php";
                    $this->domain = new domain();
                    $fields = $table == "" ? ["Domain", "Tld"] : ["Domain", "Tld", "Debtor", "CompanyName", "SurName", "Initials", "RegistrationDate", "ExpirationDate", "Status", "Registrar", "Name", "Type", "PeriodicID", "PriceExcl", "Periodic", "TerminationDate", "AutoRenew", "DomainAutoRenew", "TerminationID"];
                    $searchat = $table == "" ? "Domain|Tld" : "Domain|Tld|ProductCode|Description";
                    $result["HostFact_Domains"] = $this->domain->all($fields, $sort, $order, $limit, $searchat, $this->searchstring, false, $show_results);
                    $session = isset($_SESSION["domain.overview"]) ? $_SESSION["domain.overview"] : [];
                    $_SESSION["domain.overview"]["results"] = $show_results;
                }
                if($table != "") {
                }
                break;
            case "":
            case "HostFact_Hosting":
                if(U_HOSTING_SHOW) {
                    require_once "class/hosting.php";
                    $this->hosting = new hosting();
                    $fields = $table == "" ? ["Username"] : ["Username", "Debtor", "CompanyName", "SurName", "Initials", "Domain", "Server", "Name", "Status", "Package", "PackageName", "PeriodicID", "PriceExcl", "Periodic", "TerminationDate", "NextDate", "AutoRenew", "TerminationID"];
                    $searchat = $table == "" ? "Username|Domain" : "Username|Domain|PackageName|ProductCode|Description";
                    $result["HostFact_Hosting"] = $this->hosting->all($fields, $sort, $order, $limit, $searchat, $this->searchstring, false, $show_results);
                    $session = isset($_SESSION["hosting.overview"]) ? $_SESSION["hosting.overview"] : [];
                    $_SESSION["hosting.overview"]["results"] = $show_results;
                }
                if($table != "") {
                }
                break;
            case "":
            case "HostFact_Other":
                if(U_SERVICE_SHOW) {
                    require_once "class/periodic.php";
                    $this->subscription = new periodic();
                    $this->subscription->OtherServicesOnly = true;
                    $fields = $table == "" ? ["Description", "ProductID", "ProductName", "ProductCode"] : ["Description", "ProductID", "ProductName", "Debtor", "CompanyName", "SurName", "Initials", "Periods", "Periodic", "PriceExcl", "Number", "TaxPercentage", "StartPeriod", "EndPeriod", "Status", "TerminationDate", "NextDate", "ContractPeriods", "ContractPeriodic", "EndContract", "AutoRenew"];
                    $searchat = $table == "" ? "Description" : "ProductCode|Description";
                    $result["HostFact_Other"] = $this->subscription->all($fields, $sort, $order, $limit, $searchat, $this->searchstring, false, $show_results);
                    $session = isset($_SESSION["other.overview"]) ? $_SESSION["other.overview"] : [];
                    $_SESSION["other.overview"]["results"] = $show_results;
                }
                if($table != "") {
                }
                break;
            case "":
            case "HostFact_Creditors":
                if(U_CREDITOR_SHOW) {
                    require_once "class/creditor.php";
                    $this->creditor = new creditor();
                    $fields = $table == "" ? ["CreditorCode", "CompanyName", "Initials", "SurName"] : ["CreditorCode", "CompanyName", "Initials", "SurName", "EmailAddress", "Groups", "PhoneNumber", "MobileNumber", "FaxNumber", "Sex", "Address", "ZipCode", "City", "Country"];
                    $searchat = $table == "" ? "CreditorCode|CompanyName|Initials|SurName|PhoneNumber|MobileNumber|EmailAddress" : "CreditorCode|CompanyName|Initials|SurName|PhoneNumber|MobileNumber|EmailAddress|AccountNumber";
                    $result["HostFact_Creditors"] = $this->creditor->all($fields, $sort, $order, $limit, $searchat, $this->searchstring, false, $show_results);
                    $session = isset($_SESSION["creditor.overview"]) ? $_SESSION["creditor.overview"] : [];
                    $_SESSION["creditor.overview"]["results"] = $show_results;
                }
                if($table != "") {
                }
                break;
            case "":
            case "HostFact_Products":
                if(U_PRODUCT_SHOW) {
                    require_once "class/product.php";
                    $this->product = new product();
                    $fields = $table == "" ? ["ProductCode", "ProductName"] : ["ProductCode", "ProductName", "PriceExcl", "Sold", "Groups", "PricePeriod", "ProductType"];
                    $searchat = $table == "" ? "ProductCode" : "ProductCode|ProductName|ProductDescription";
                    $result["HostFact_Products"] = $this->product->all($fields, $sort, $order, $limit, $searchat, $this->searchstring, false, $show_results);
                    $session = isset($_SESSION["product.overview"]) ? $_SESSION["product.overview"] : [];
                    $_SESSION["product.overview"]["results"] = $show_results;
                }
                if($table != "") {
                }
                break;
            case "":
            case "HostFact_NewOrder":
                if(U_ORDER_SHOW) {
                    require_once "class/neworders.php";
                    $this->order = new neworder();
                    $fields = $table == "" ? ["OrderCode", "CompanyName", "Initials", "SurName"] : ["OrderCode", "CompanyName", "Initials", "SurName", "Address", "ZipCode", "City", "Country", "EmailAddress", "PhoneNumber", "MobileNumber", "Debtor", "Customer", "Type", "Date", "Authorisation", "InvoiceMethod", "Status", "AmountExcl", "AmountIncl", "Paid", "Comment", "Employee"];
                    $searchat = $table == "" ? "OrderCode|CompanyName|Initials|SurName|EmailAddress|TransactionID" : "OrderCode|CompanyName|Initials|SurName|Address|ZipCode|City|EmailAddress|Description|TransactionID";
                    $result["HostFact_NewOrder"] = $this->order->all($fields, !$sort ? "Date` DESC, `OrderCode" : $sort, !$sort ? "DESC" : $order, $limit, $searchat, $this->searchstring, false, $show_results);
                    $session = isset($_SESSION["order.overview"]) ? $_SESSION["order.overview"] : [];
                    $_SESSION["order.overview"]["results"] = $show_results;
                }
                if($table != "") {
                }
                break;
            case "":
            case "HostFact_Tickets":
                if(U_TICKET_SHOW) {
                    require_once "class/ticket.php";
                    $this->ticket = new ticket();
                    $fields = $table == "" ? ["TicketID", "Subject"] : ["TicketID", "Debtor", "CompanyName", "SurName", "Initials", "Subject", "Owner", "Priority", "Status", "Number", "LastDate", "LastName", "Name"];
                    $searchat = $table == "" ? "TicketID" : "TicketID|Subject";
                    $result["HostFact_Tickets"] = $this->ticket->all($fields, $sort, $order, $limit, $searchat, $this->searchstring, false, $show_results);
                    $session = isset($_SESSION["ticket.overview"]) ? $_SESSION["ticket.overview"] : [];
                    $_SESSION["ticket.overview"]["results"] = $show_results;
                }
                if($table != "") {
                }
                break;
            case "":
            case "HostFact_CreditInvoice":
                if(U_CREDITOR_INVOICE_SHOW) {
                    require_once "class/creditinvoice.php";
                    $this->creditinvoice = new creditinvoice();
                    $fields = $table == "" ? ["CreditInvoiceCode", "InvoiceCode", "CompanyName", "Initials", "SurName"] : ["CreditInvoiceCode", "InvoiceCode", "CompanyName", "Initials", "SurName", "Term", "Creditor", "Date", "PayDate", "Status", "AmountExcl", "AmountIncl", "Location", "Authorisation"];
                    $searchat = $table == "" ? "CreditInvoiceCode|InvoiceCode" : "CreditInvoiceCode|InvoiceCode|CompanyName|Initials|SurName|Description";
                    $result["HostFact_CreditInvoice"] = $this->creditinvoice->all($fields, $sort, $order, $limit, $searchat, $this->searchstring, false, $show_results);
                    $session = isset($_SESSION["creditinvoice.overview"]) ? $_SESSION["creditinvoice.overview"] : [];
                    $_SESSION["creditinvoice.overview"]["results"] = $show_results;
                }
                if($table != "") {
                }
                break;
            default:
                global $additional_product_types;
                global $_module_instances;
                if($table == "") {
                    foreach ($additional_product_types as $key => $val) {
                        if(@method_exists($_module_instances[$key], "service_search")) {
                            $result[$key] = $_module_instances[$key]->service_search($this->searchstring, true);
                        }
                    }
                } elseif(isset($additional_product_types[$table]) && isset($_module_instances[$table]) && @method_exists($_module_instances[$table], "service_search")) {
                    $result[$table] = $_module_instances[$table]->service_search($this->searchstring, false);
                }
                if($table != "") {
                }
                if($table == "") {
                    $total_results = 0;
                    foreach ($result as $subkey => $sub) {
                        $total_results += $sub["CountRows"];
                    }
                    $show_max_per_category = $total_results <= 25 ? false : true;
                    foreach ($result as $subkey => $sub) {
                        $sub_count = 0;
                        if(is_array($sub)) {
                            foreach ($sub as $key => $value) {
                                if($key != "CountRows" && $key != "TotalAmountIncl" && $key != "TotalAmountExcl") {
                                    $sub_count++;
                                    if($show_max_per_category && 5 < $sub_count) {
                                    } else {
                                        if($subkey == "HostFact_Debtors") {
                                            $label = $value["CompanyName"] ? $value["DebtorCode"] . " - " . $value["CompanyName"] : $value["DebtorCode"] . " - " . $value["SurName"] . ", " . $value["Initials"];
                                            $label = htmlspecialchars_decode($label);
                                            $tmp_result[] = ["label" => $label, "url" => "debtors.php?page=show&id=" . $value["id"], "category" => __("debtors"), "category_count" => $sub["CountRows"]];
                                        }
                                        if($subkey == "HostFact_Invoice") {
                                            $label = $value["CompanyName"] ? $value["InvoiceCode"] . " - " . $value["CompanyName"] : $value["InvoiceCode"] . " - " . $value["SurName"] . ", " . $value["Initials"];
                                            $label = htmlspecialchars_decode($label);
                                            $tmp_result[] = ["label" => $label, "url" => "invoices.php?page=show&id=" . $value["id"], "category" => __("invoices"), "category_count" => $sub["CountRows"]];
                                        }
                                        if($subkey == "HostFact_PriceQuote") {
                                            $label = $value["CompanyName"] ? $value["PriceQuoteCode"] . " - " . $value["CompanyName"] : $value["PriceQuoteCode"] . " - " . $value["SurName"] . ", " . $value["Initials"];
                                            $label = htmlspecialchars_decode($label);
                                            $tmp_result[] = ["label" => $label, "url" => "pricequotes.php?page=show&id=" . $value["id"], "category" => __("pricequotes"), "category_count" => $sub["CountRows"]];
                                        }
                                        if($subkey == "HostFact_Domains") {
                                            $label = $value["Domain"] ? $value["Domain"] . "." . $value["Tld"] : $value["Tld"];
                                            $label = htmlspecialchars_decode($label);
                                            $tmp_result[] = ["label" => $label, "url" => "domains.php?page=show&id=" . $value["id"], "category" => __("domains"), "category_count" => $sub["CountRows"]];
                                        }
                                        if($subkey == "HostFact_Hosting") {
                                            $label = $value["Username"] ? $value["Username"] : "";
                                            $label = htmlspecialchars_decode($label);
                                            $tmp_result[] = ["label" => $label, "url" => "hosting.php?page=show&id=" . $value["id"], "category" => __("hosting"), "category_count" => $sub["CountRows"]];
                                        }
                                        if($subkey == "HostFact_Other") {
                                            $label = (isset($value["ProductCode"]) && $value["ProductCode"] ? $value["ProductCode"] . " - " : "") . $value["Description"];
                                            $label = htmlspecialchars_decode($label);
                                            $tmp_result[] = ["label" => $label, "url" => "services.php?page=show&id=" . $value["id"], "category" => __("menu.other.services"), "category_count" => $sub["CountRows"]];
                                        }
                                        if($subkey == "HostFact_Creditors") {
                                            $label = $value["CompanyName"] ? $value["CreditorCode"] . " - " . $value["CompanyName"] : $value["CreditorCode"] . " - " . $value["SurName"] . ", " . $value["Initials"];
                                            $label = htmlspecialchars_decode($label);
                                            $tmp_result[] = ["label" => $label, "url" => "creditors.php?page=show&id=" . $value["id"], "category" => __("creditors"), "category_count" => $sub["CountRows"]];
                                        }
                                        if($subkey == "HostFact_CreditInvoice") {
                                            $label = $value["CompanyName"] ? $value["CreditInvoiceCode"] . " - " . $value["CompanyName"] : $value["CreditInvoiceCode"] . " - " . $value["SurName"] . ", " . $value["Initials"];
                                            $label = htmlspecialchars_decode($label);
                                            $tmp_result[] = ["label" => $label, "url" => "creditors.php?page=show_invoice&id=" . $value["id"], "category" => __("creditinvoices"), "category_count" => $sub["CountRows"]];
                                        }
                                        if($subkey == "HostFact_Products") {
                                            $label = $value["ProductCode"] ? $value["ProductCode"] . " - " . $value["ProductName"] : $value["ProductName"];
                                            $label = htmlspecialchars_decode($label);
                                            $tmp_result[] = ["label" => $label, "url" => "products.php?page=show&id=" . $value["id"], "category" => __("products"), "category_count" => $sub["CountRows"]];
                                        }
                                        if($subkey == "HostFact_NewOrder") {
                                            $label = $value["CompanyName"] ? $value["OrderCode"] . " - " . $value["CompanyName"] : $value["OrderCode"] . " - " . $value["SurName"] . ", " . $value["Initials"];
                                            $label = htmlspecialchars_decode($label);
                                            $tmp_result[] = ["label" => $label, "url" => "orders.php?page=show&id=" . $value["id"], "category" => __("orders"), "category_count" => $sub["CountRows"]];
                                        }
                                        if($subkey == "HostFact_Tickets") {
                                            $label = $value["TicketID"] . " - " . $value["Subject"];
                                            $label = htmlspecialchars_decode($label);
                                            $tmp_result[] = ["label" => $label, "url" => "tickets.php?page=show&id=" . $value["id"], "category" => ucfirst(__("tickets")), "category_count" => $sub["CountRows"]];
                                        }
                                        if(isset($additional_product_types[$subkey]) && isset($_module_instances[$subkey])) {
                                            $label = $value["Label"];
                                            $label = htmlspecialchars_decode($label);
                                            $tmp_result[] = ["label" => $label, "url" => "modules.php?module=" . $subkey . "&page=show&id=" . $value["id"], "category" => ucfirst(__("module-name", $subkey)), "category_count" => $sub["CountRows"]];
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $result = $tmp_result;
                } else {
                    $result = isset($result[$table]) ? $result[$table] : [];
                }
                return $result;
        }
    }
}

?>