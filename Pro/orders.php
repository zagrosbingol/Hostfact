<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "config.php";
checkRight(U_ORDER_SHOW);
$page = isset($_GET["page"]) ? $_GET["page"] : "overview";
$page = $page == "edit" ? "add" : $page;
$order_id = isset($_GET["id"]) ? intval(esc($_GET["id"])) : (isset($_POST["id"]) ? intval(esc($_POST["id"])) : NULL);
switch ($page) {
    case "show":
        if(isset($order_id) && isset($_GET["action"])) {
            require_once "class/neworders.php";
            switch ($_GET["action"]) {
                case "makeinvoice":
                    if(!U_ORDER_EDIT) {
                    } else {
                        $order = new neworder();
                        $order->Identifier = $order_id;
                        if(0 < $order->Identifier) {
                            $order->makeInvoice();
                        }
                    }
                    break;
                case "markaspaid":
                    if(!U_ORDER_EDIT) {
                    } else {
                        $order = new neworder();
                        $order->Identifier = $order_id;
                        $order->show();
                        if(0 < $order->Identifier && $order->markaspaid(esc($_POST["TransactionID"])) && isset($_POST["ProcessOrder"]) && $_POST["ProcessOrder"] == "yes") {
                            flashMessage($order);
                            header("Location: orders.php?page=show&action=makeinvoice&id=" . $order->Identifier);
                            exit;
                        }
                    }
                    break;
                case "changesendmethod":
                    if(!U_ORDER_EDIT) {
                    } else {
                        $order = new neworder();
                        $order->Identifier = $order_id;
                        $order->InvoiceMethod = esc($_POST["InvoiceMethod"]);
                        $new_emailaddress = false;
                        if(isset($_POST["NewMethodEmailAddress"]) && in_array($order->InvoiceMethod, [0, 3])) {
                            $new_emailaddress = esc($_POST["NewMethodEmailAddress"]);
                        }
                        $order->changesendmethod($new_emailaddress);
                    }
                    break;
                case "cancelonlinepayment":
                    if(!U_ORDER_EDIT) {
                    } else {
                        $order = new neworder();
                        $order->Identifier = $order_id;
                        $order->show();
                        if((int) $order->Paid === 0) {
                            $order->removeTransactionID();
                        }
                    }
                    break;
                default:
                    flashMessage(isset($order) ? $order : NULL);
                    header("Location:orders.php?page=show&id=" . $order_id);
                    exit;
            }
        }
        break;
    case "add":
        if((int) $order_id === 0 && !U_ORDER_ADD || 0 < $order_id && !U_ORDER_EDIT) {
        } else {
            $pagetype = 0 < $order_id ? "edit" : "add";
            require_once "class/neworders.php";
            $order = new neworder();
            if($pagetype == "edit") {
                $order->Identifier = $order_id;
                $order->show();
                $order->format(false);
                $current_debtor = $order->Debtor;
                $current_status = $order->Status;
                $current_ordercode = $order->OrderCode;
            }
            if(!empty($_POST)) {
                if($_POST["VatShift_helper"] == "true") {
                    if(!isset($_POST["VatShift"])) {
                        $_POST["VatShift"] = "no";
                    } else {
                        $_POST["VatShift"] = "yes";
                    }
                } else {
                    $_POST["VatShift"] = "";
                }
                if($pagetype == "edit") {
                    foreach ($order as $key => $value) {
                        if(in_array($key, $order->Variables)) {
                            $order->{$key} = html_entity_decode($value);
                        }
                    }
                }
                foreach ($_POST as $post_key => $post_value) {
                    if(in_array($post_key, $order->Variables)) {
                        $order->{$post_key} = esc($post_value);
                    }
                }
                $order->Discount = (double) number2db($order->Discount);
                if(IS_INTERNATIONAL) {
                    $order->State = isset($_POST["StateCode"]) && $_POST["StateCode"] ? esc($_POST["StateCode"]) : $order->State;
                }
                if(isset($_POST["CustomerType"]) && $_POST["CustomerType"] == "debtor") {
                    $order->Customer = $order->OldDebtor;
                    $order->Type = "debtor";
                    $order->Debtor = $order->Debtor;
                } elseif(isset($_POST["CustomerType"]) && $_POST["CustomerType"] == "new") {
                    $order->Debtor = $order->OldDebtor;
                }
                $added_lines = [];
                foreach ($_POST["Number"] as $invoiceKey => $value) {
                    $NumberSuffix = extractNumberAndSuffix($value);
                    if($NumberSuffix[1] === false) {
                        $NumberSuffix[0] = $value;
                        $NumberSuffix[1] = "";
                    }
                    list($_POST["Number"][$invoiceKey], $_POST["NumberSuffix"][$invoiceKey]) = $NumberSuffix;
                }
                if(!$order->is_free(esc($_POST["OrderCode"]))) {
                    $order->Error[] = sprintf(__("ordercode not available"), esc($_POST["OrderCode"]));
                    $result = false;
                    $error_class->Error = array_merge($error_class->Error, $order->Error);
                    $order->Error = [];
                    $page = "add";
                } else {
                    $order->VatCalcMethod = $_POST["TaxRate1"] != "" && isEmptyFloat(number2db($_POST["TaxRate1"])) ? "excl" : $order->VatCalcMethod;
                    $last_filled_line = -1;
                    $line_counter = 0;
                    foreach ($_POST["Date"] as $i => $value) {
                        if($order->VatCalcMethod == "incl" && isset($_POST["PriceIncl"][$i]) && deformat_money(esc($_POST["PriceIncl"][$i])) && (esc($_POST["Taxable"]) == "true" || $_POST["TaxRate1"] != "" && 0 < $_POST["TaxRate1"])) {
                            $tax_percentage = $_POST["TaxRate1"] != "" ? esc($_POST["TaxRate1"]) : esc($_POST["TaxPercentage"][$i]);
                            $_POST["PriceExcl"][$i] = deformat_money(esc($_POST["PriceIncl"][$i])) / (1 + $tax_percentage);
                        }
                        if(isset($_POST["Item"][$i]) && 1 <= esc($_POST["Item"][$i]) && !isEmptyFloat(deformat_money(esc($_POST["Number"][$i]))) && esc($_POST["Description"][$i]) && esc($_POST["Description"][$i]) != " ") {
                            $last_filled_line = $line_counter;
                        } elseif((!isset($_POST["Item"][$i]) || esc($_POST["Item"][$i]) < 1) && esc($_POST["Number"][$i]) != "" && (esc($_POST["Description"][$i]) != "" || esc($_POST["PriceExcl"][$i]) != "")) {
                            $last_filled_line = $line_counter;
                        }
                        $line_counter++;
                    }
                    $line_counter = 0;
                    foreach ($_POST["Date"] as $i => $value) {
                        if($line_counter <= $last_filled_line || isset($_POST["Item"][$i]) && 1 <= $_POST["Item"][$i]) {
                            $orderelement = new neworderelement();
                            $orderelement->VatCalcMethod = $order->VatCalcMethod;
                            if($pagetype == "edit") {
                                if(!esc($_POST["Description"][$i])) {
                                    $_POST["Description"][$i] = " ";
                                }
                                if(isset($_POST["Item"][$i]) && 0 < $_POST["Item"][$i]) {
                                    $orderelement->Identifier = intval(esc($_POST["Item"][$i]));
                                    $orderelement->show();
                                }
                            }
                            if(!(is_numeric(number2db(esc($_POST["Number"][$i]))) && isEmptyFloat(number2db(esc($_POST["Number"][$i])))) && ($line_counter <= $last_filled_line || esc($_POST["Description"][$i]) != " " || esc($_POST["PriceExcl"][$i]) != "")) {
                                $orderelement->Type = $order->Type;
                                $orderelement->OrderCode = $order->OrderCode;
                                $orderelement->Debtor = $order->Debtor;
                                $orderelement->Date = esc($_POST["Date"][$i]);
                                $orderelement->Number = esc($_POST["Number"][$i]);
                                $orderelement->NumberSuffix = esc($_POST["NumberSuffix"][$i]);
                                $orderelement->ProductCode = isset($_POST["ProductCode"][$i]) ? esc($_POST["ProductCode"][$i]) : "";
                                $orderelement->Description = esc($_POST["Description"][$i]) == "" && $i < $_POST["NumberOfElements"] ? " " : esc($_POST["Description"][$i]);
                                $orderelement->PriceExcl = $_POST["PriceExcl"][$i];
                                $orderelement->DiscountPercentage = esc($_POST["DiscountPercentage"][$i]);
                                $orderelement->DiscountPercentageType = esc($_POST["DiscountPercentageType"][$i]);
                                $orderelement->TaxPercentage = esc($_POST["Taxable"]) == "true" || $_POST["TaxRate1"] != "" ? esc($_POST["TaxPercentage"][$i]) : "0";
                                if(isset($_POST["PeriodicType"][$i]) && esc($_POST["PeriodicType"][$i]) == "period") {
                                    $orderelement->Periods = esc($_POST["Periods"][$i]);
                                    $orderelement->Periodic = esc($_POST["Periodic"][$i]);
                                } else {
                                    $orderelement->Periods = 1;
                                    $orderelement->Periodic = "";
                                }
                                if($pagetype == "edit") {
                                    $orderelement->OldDebtor = $order->OldDebtor;
                                }
                            } else {
                                $orderelement->Number = 0;
                                $orderelement->NumberSuffix = "";
                            }
                            $orderelement->Ordering = array_search($i, array_keys($_POST["Date"]));
                            if(isset($_POST["Item"][$i]) && 0 < $_POST["Item"][$i]) {
                                $result_elements = $orderelement->edit();
                                if(isEmptyFloat(number2db($orderelement->Number))) {
                                    $_POST["Item"][$i] = 0;
                                }
                            } else {
                                $result_elements = $orderelement->add();
                                if($result_elements) {
                                    $added_lines[] = $orderelement->Identifier;
                                }
                            }
                            if(!$result_elements) {
                                $order->Error = array_merge($order->Error, $orderelement->Error);
                            }
                        }
                        $line_counter++;
                    }
                    if($pagetype == "edit") {
                        $result = $order->edit();
                        if($result && $order->Type == "new" && $order->Customer <= 0) {
                            require_once "class/newcustomer.php";
                            $customer = new newcustomer();
                            $customer->Identifier = $order->Debtor;
                            $customer->show();
                            require_once "class/handle.php";
                            $handle = new handle();
                            $matched_handles = $handle->lookupNewCustomerHandle($customer->Identifier);
                            foreach ($_POST as $k => $v) {
                                if(substr($k, 0, 8) == "Customer") {
                                    $key = substr($k, 8);
                                    if(in_array($key, $customer->Variables)) {
                                        $customer->{$key} = esc($v);
                                    }
                                }
                            }
                            if(IS_INTERNATIONAL) {
                                $customer->State = isset($_POST["CustomerStateCode"]) && $_POST["CustomerStateCode"] ? esc($_POST["CustomerStateCode"]) : $customer->State;
                                $customer->InvoiceState = isset($_POST["CustomerInvoiceStateCode"]) && $_POST["CustomerInvoiceStateCode"] ? esc($_POST["CustomerInvoiceStateCode"]) : $customer->InvoiceState;
                            }
                            if(!isset($_SESSION["custom_fields"]["newcustomer"]) || $_SESSION["custom_fields"]["newcustomer"]) {
                                $customfields_list = $_SESSION["custom_fields"]["newcustomer"];
                                $customer->customvalues = [];
                                foreach ($customfields_list as $k => $custom_field) {
                                    $customer->customvalues[$custom_field["FieldCode"]] = isset($_POST["custom"][$custom_field["FieldCode"]]) ? esc($_POST["custom"][$custom_field["FieldCode"]]) : "";
                                }
                            }
                            $customer->Password = $customer->Password ? passcrypt($customer->Password) : "";
                            if(!$customer->edit()) {
                                $order->Error = array_merge($order->Error, $customer->Error);
                            } elseif(!empty($matched_handles)) {
                                $handle->syncNewCustomerToHandle($customer->Identifier, $matched_handles);
                            }
                        }
                    } else {
                        $order->Error = __("it is not allowed to add orders");
                    }
                    if($result === true) {
                        flashMessage($order);
                        header("Location: orders.php?page=show&id=" . $order->Identifier);
                        exit;
                    }
                    foreach ($added_lines as $lineID) {
                        $orderelement = new neworderelement();
                        $orderelement->Identifier = $lineID;
                        $orderelement->show();
                        $orderelement->delete();
                    }
                    foreach ($_POST as $post_key => $post_value) {
                        if(in_array($post_key, $order->Variables) && is_string($post_value)) {
                            $order->{$post_key} = htmlspecialchars(esc($post_value));
                        }
                    }
                    $error_class->Error = array_merge($error_class->Error, $order->Error);
                    $order->Error = [];
                    $page = "add";
                }
            }
        }
        break;
    case "delete":
        $pagetype = "confirmDelete";
        $countSuccess = 0;
        if($order_id != NULL) {
            $page = "show";
        } else {
            $page = "overview";
        }
        if(empty($_POST) || !U_ORDER_DELETE) {
        } elseif(isset($_POST["imsure"]) && $_POST["imsure"] == "yes") {
            require_once "class/neworders.php";
            $order = new neworder();
            $order->Identifier = $order_id;
            $order->show();
            $result = $order->delete();
            if($result) {
                $order_id = NULL;
                $page = "overview";
                $countSuccess++;
                if(!empty($_SESSION["ActionLog"]["Order"]["delete"])) {
                    array_shift($_SESSION["ActionLog"]["Order"]["delete"]);
                    if(!empty($_SESSION["ActionLog"]["Order"]["delete"])) {
                        if(isset($_POST["forAll"]) && $_POST["forAll"] == "yes") {
                            foreach ($_SESSION["ActionLog"]["Order"]["delete"] as $order_id) {
                                $order = new neworder();
                                $order->Identifier = $order_id;
                                $order->show();
                                $result = $order->delete();
                                if($result) {
                                    $countSuccess++;
                                }
                            }
                        } else {
                            header("Location: ?page=delete&id=" . current($_SESSION["ActionLog"]["Order"]["delete"]));
                            exit;
                        }
                    }
                }
            }
            if(0 < $countSuccess) {
                $order->Success[] = sprintf(__("batch x orders removed"), $countSuccess);
            }
            flashMessage($order);
            if(isset($_SESSION["ActionLog"]["Order"]["from_page"]) && !empty($_SESSION["ActionLog"]["Order"]["from_page"])) {
                $from_page = $_SESSION["ActionLog"]["Order"]["from_page"];
                $from_id = $_SESSION["ActionLog"]["Order"]["from_id"];
                unset($_SESSION["ActionLog"]["Order"]["from_page"]);
                switch ($from_page) {
                    case "debtor":
                        $_SESSION["selected_tab"] = 2;
                        header("Location: debtors.php?page=show&id=" . intval($from_id));
                        exit;
                        break;
                }
            }
            if($result) {
                header("Location: orders.php");
                exit;
            }
            header("Location: orders.php?page=show&id=" . $order_id);
            exit;
        }
        break;
    case "view":
        require_once "class/neworders.php";
        $list_orders = isset($_POST["id"]) && is_array($_POST["id"]) ? $_POST["id"] : [];
        if(!empty($_POST["id"])) {
            switch ($_POST["action"]) {
                case "dialog:ordermakeinvoice":
                    if(!U_ORDER_EDIT) {
                    } else {
                        foreach ($list_orders as $key => $id) {
                            $order = new neworder();
                            $order->Identifier = esc($id);
                            $order->makeInvoice();
                            flashMessage($order);
                            unset($order);
                        }
                    }
                    break;
                case "delete":
                    if(!U_ORDER_DELETE) {
                    } else {
                        if(!isset($_SESSION["ActionLog"]["Order"])) {
                            $_SESSION["ActionLog"]["Order"] = [];
                        }
                        $_SESSION["ActionLog"]["Order"]["delete"] = [];
                        foreach ($list_orders as $d_id) {
                            $_SESSION["ActionLog"]["Order"]["delete"][] = $d_id;
                        }
                        if(isset($_GET["from_page"]) && !empty($_GET["from_page"])) {
                            $_SESSION["ActionLog"]["Order"]["from_page"] = esc($_GET["from_page"]);
                            $_SESSION["ActionLog"]["Order"]["from_id"] = esc($_GET["from_id"]);
                        }
                        if(!empty($_SESSION["ActionLog"]["Order"]["delete"])) {
                            header("location: ?page=delete&id=" . current($_SESSION["ActionLog"]["Order"]["delete"]));
                            exit;
                        }
                    }
                    break;
                default:
                    unset($_POST["id"]);
            }
        } elseif(isset($_POST["action"])) {
            $error_class->Warning[] = __("nothing selected");
        }
        flashMessage($order);
        if(isset($_GET["from_page"]) && !empty($_GET["from_page"])) {
            switch ($_GET["from_page"]) {
                case "debtor":
                    $_SESSION["selected_tab"] = 2;
                    header("Location: debtors.php?page=show&id=" . intval($_GET["from_id"]));
                    exit;
                    break;
            }
        }
        header("Location: orders.php");
        exit;
        break;
    case "overview":
    case "show":
        require_once "class/neworders.php";
        $order = new neworder();
        $order->Identifier = $order_id;
        if(!$order->show()) {
            flashMessage($order);
            header("Location: orders.php");
            exit;
        }
        $order->format(false);
        if($order->Type == "new" && $order->Customer <= 0) {
            require_once "class/newcustomer.php";
            $customer = new newcustomer();
            $customer->Identifier = $order->Debtor;
            $customer->show();
        } else {
            require_once "class/debtor.php";
            $debtor = new debtor();
            $debtor->Identifier = $order->Debtor;
            $debtor->show();
        }
        require_once "class/template.php";
        $template = new template();
        $template->Identifier = isset($order->Template) && 0 < $order->Template ? $order->Template : $template->getStandard("invoice");
        $template->show();
        if($order->VatShift == "yes" || $order->VatShift == "" && $order->AmountTax == money(0, false) && $debtor->Country != $company->Country && $debtor->CompanyName != "" && $debtor->TaxNumber != "" && ($debtor->Country == "NL" || in_array($debtor->Country, $_SESSION["wf_cache_array_country_EU"]))) {
            $show_vatshift_text = __("default vatshift text");
        } else {
            $show_vatshift_text = false;
        }
        $message = parse_message($order, isset($debtor) ? $debtor : NULL);
        $wfh_page_title = __("order") . " " . $order->OrderCode . ($order->Authorisation == "yes" ? __("invoice authed") : "") . (0 < $order->Paid ? " - " . __("action paid") : "");
        $current_page_url = "orders.php";
        $sidebar_template = "invoice.sidebar.php";
        require_once "views/order.show.php";
        break;
    case "add":
        checkRight(U_ORDER_EDIT);
        if(isset($order_id)) {
            require_once "class/neworders.php";
            $order = new neworder();
            $order->Identifier = $order_id;
            if(!$order->show()) {
                flashMessage($order);
                header("Location: orders.php");
                exit;
            }
            $order->Date = rewrite_date_db2site(rewrite_date_site2db($order->Date, DATE_FORMAT . " %H:%i:%s"));
            require_once "class/debtor.php";
            $debtor = new debtor();
            if(strtolower($order->Type) == "debtor") {
                $debtor->Identifier = $order->Debtor;
                $debtor->show();
            }
            $fields = ["DebtorCode", "Initials", "SurName", "CompanyName"];
            $debtors = $debtor->all($fields);
            require_once "class/newcustomer.php";
            $customer = new newcustomer();
            if($order->Type == "new" && $order->Customer <= 0) {
                $customer->Identifier = $order->Debtor;
                $customer->show();
                $debtor->TaxNumber = $customer->TaxNumber;
                $debtor->Taxable = $customer->Taxable;
                $debtor->TaxRate1 = $customer->TaxRate1;
                $debtor->TaxRate2 = $customer->TaxRate2;
            }
            require_once "class/template.php";
            $template = new template();
            $order->Template = isset($order->Template) && 0 < $order->Template ? $order->Template : $template->getStandard("invoice");
            $template->Identifier = $order->Template;
            $template->Type = "invoice";
            $template->show();
            $fields = ["Name"];
            $templates = $template->all($fields, "", "", "", "Type", "invoice");
            $pricequotetemplates = $template->all($fields, "", "", "", "Type", "pricequote");
            require_once "class/product.php";
            $product = new product();
            $fields = ["ProductCode", "ProductName"];
            $products = $product->all($fields);
            $message = parse_message($order, $debtor, $customer, $product);
            $wfh_page_title = $_GET["page"] == "add" ? __("create order") : ($_GET["page"] == "edit" ? __("edit order") : "");
            $current_page_url = "orders.php";
            $sidebar_template = "invoice.sidebar.php";
            require_once "views/order.add.php";
        } else {
            header("Location: orders.php");
            exit;
        }
        break;
    default:
        require_once "class/neworders.php";
        $orderlist = isset($orderlist) && is_object($orderlist) ? $orderlist : new neworder();
        $session = isset($_SESSION["order.overview"]) ? $_SESSION["order.overview"] : [];
        $fields = ["OrderCode", "CompanyName", "Initials", "SurName", "Address", "ZipCode", "City", "Country", "EmailAddress", "PhoneNumber", "MobileNumber", "Debtor", "Customer", "Type", "Date", "Authorisation", "InvoiceMethod", "Status", "AmountExcl", "AmountIncl", "Paid", "Comment", "Employee"];
        $sort = isset($session["sort"]) ? $session["sort"] : "Date` DESC, `OrderCode";
        $order = isset($session["order"]) ? $session["order"] : "DESC";
        $searchat = isset($session["searchat"]) ? $session["searchat"] : "";
        $searchfor = isset($session["searchfor"]) ? $session["searchfor"] : "";
        $selectgroup = isset($session["status"]) ? $session["status"] : "0|1|2";
        $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) ? $session["limit"] : "1");
        $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : MAX_RESULTS_LIST);
        $orders = $orderlist->all($fields, $sort, $order, $limit, $searchat, $searchfor, $selectgroup, $show_results);
        if(isset($orders["CountRows"]) && ($orders["CountRows"] < $show_results * ($limit - 1) || 1 < $limit && $orders["CountRows"] == $show_results * ($limit - 1))) {
            $newPage = ceil($orders["CountRows"] / $show_results);
            if($newPage <= 0) {
                $newPage = 1;
            }
            $_SESSION["order.overview"]["limit"] = $newPage;
            header("Location: orders.php");
            exit;
        }
        $_SESSION["order.overview"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "status" => $selectgroup, "searchat" => $searchat, "searchfor" => $searchfor, "limit" => $limit];
        $current_page = $limit;
        $message = parse_message($orderlist);
        $wfh_page_title = __("order overview") . " (" . $orders["CountRows"] . ")";
        $current_page_url = "orders.php";
        $sidebar_template = "invoice.sidebar.php";
        require_once "views/order.overview.php";
}

?>