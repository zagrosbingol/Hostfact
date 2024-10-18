<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "config.php";
checkRight(U_SERVICEMANAGEMENT_SHOW);
$page = isset($_GET["page"]) ? $_GET["page"] : "";
$registrar_id = isset($_GET["id"]) ? intval(esc($_GET["id"])) : (isset($_POST["id"]) ? intval(esc($_POST["id"])) : NULL);
switch ($page) {
    case "add":
        $pagetype = "add";
        if(empty($_POST) || !U_SERVICEMANAGEMENT_EDIT) {
        } else {
            require_once "class/registrar.php";
            $registrar = new registrar();
            foreach ($_POST as $key => $value) {
                if(in_array($key, $registrar->Variables)) {
                    $registrar->{$key} = esc($value);
                }
            }
            if($_POST["RegistrarPassword"]) {
                $registrar->Password = esc($_POST["RegistrarPassword"]);
            } else {
                $registrar->Password = "";
            }
            $registrar->DomainEnabled = isset($_POST["DomainEnabled"]) && $_POST["DomainEnabled"] == "yes" ? "yes" : "no";
            $registrar->SSLEnabled = isset($_POST["SSLEnabled"]) && $_POST["SSLEnabled"] == "yes" ? "yes" : "no";
            if(is_module_active("dnsmanagement")) {
                global $_module_instances;
                $dnsmanagement = $_module_instances["dnsmanagement"];
                $registar = $dnsmanagement->before_integration_is_added($registrar);
            }
            if($registrar->add()) {
                $page = "show";
                $registrar_id = $registrar->Identifier;
                flashMessage($registrar);
                header("Location: registrars.php?page=show&id=" . $registrar_id);
                exit;
            }
            foreach ($registrar->Variables as $key) {
                $registrar->{$key} = htmlspecialchars($registrar->{$key});
            }
        }
        break;
    case "edit":
        $pagetype = "edit";
        $page = "add";
        if(empty($_POST) || !U_SERVICEMANAGEMENT_EDIT) {
        } else {
            $registrar_id = intval(esc($_POST["id"]));
            require_once "class/registrar.php";
            $registrar = new registrar();
            $registrar->show($registrar_id);
            foreach ($_POST as $key => $value) {
                if(in_array($key, $registrar->Variables)) {
                    $registrar->{$key} = esc($value);
                }
            }
            if($_POST["RegistrarPassword"]) {
                $registrar->Password = esc($_POST["RegistrarPassword"]);
            } else {
                $registrar->Password = "";
            }
            $registrar->DomainEnabled = isset($_POST["DomainEnabled"]) && $_POST["DomainEnabled"] == "yes" ? "yes" : "no";
            $registrar->SSLEnabled = isset($_POST["SSLEnabled"]) && $_POST["SSLEnabled"] == "yes" ? "yes" : "no";
            if(is_module_active("dnsmanagement")) {
                global $_module_instances;
                $dnsmanagement = $_module_instances["dnsmanagement"];
                $registar = $dnsmanagement->before_integration_is_added($registrar);
            }
            if($registrar->edit($registrar_id)) {
                $page = "show";
                flashMessage($registrar);
                header("Location: registrars.php?page=show&id=" . $registrar_id);
                exit;
            }
            foreach ($registrar->Variables as $key) {
                $registrar->{$key} = htmlspecialchars($registrar->{$key});
            }
            $error = true;
        }
        break;
    case "delete":
        $pagetype = "confirmDelete";
        if($registrar_id != NULL) {
            $page = "show";
        } else {
            $page = "overview";
        }
        if(empty($_POST) || !U_SERVICEMANAGEMENT_DELETE) {
        } elseif(isset($_POST["imsure"]) && $_POST["imsure"] == "yes") {
            require_once "class/registrar.php";
            $registrar = new registrar();
            $result = $registrar->delete($registrar_id);
            if($result) {
                $registrar_id = NULL;
                $page = "overview";
                if(!empty($_SESSION["ActionLog"]["Registrar"]["delete"])) {
                    array_shift($_SESSION["ActionLog"]["Registrar"]["delete"]);
                    if(!empty($_SESSION["ActionLog"]["Registrar"]["delete"])) {
                        header("Location: ?page=delete&id=" . current($_SESSION["ActionLog"]["Registrar"]["delete"]));
                        exit;
                    }
                }
            }
            unset($pagetype);
            unset($current_status);
        }
        break;
    case "import":
        if(!U_SERVICEMANAGEMENT_EDIT) {
        } else {
            if(!empty($_POST)) {
                parse_str($_POST["domain_data"], $output);
            }
            require_once "class/whois.php";
            if(isset($_POST["import_action"]) && $_POST["import_action"] == "newDomains" && isset($_POST["DialogDebtor"]) && is_array($output["DomainID"])) {
                require_once "class/domain.php";
                require_once "class/topleveldomain.php";
                require_once "class/whois.php";
                $list_domains = unserialize($_SESSION["Registrar_" . $registrar_id . "_import_response"]);
                $list_handles = [];
                $counter_imported_domains = 0;
                require_once "class/registrar.php";
                $registrar = new registrar();
                $registrar->show($registrar_id);
                require_once "3rdparty/domain/" . $registrar->Class . "/" . $registrar->Class . ".php";
                $registrar->api = new $registrar->Class();
                $registrar->api->User = $registrar->User;
                $registrar->api->Password = passcrypt($registrar->Password);
                $registrar->api->Testmode = $registrar->Testmode;
                $registrar->api->License = $registrar->License;
                $registrar->api->Setting1 = $registrar->Setting1;
                $registrar->api->Setting2 = $registrar->Setting2;
                $registrar->api->Setting3 = $registrar->Setting3;
                foreach ($output["DomainID"] as $domain_session_id => $tmp_domain) {
                    $domain = $list_domains[$domain_session_id];
                    $d = new domain();
                    $d->Domain = substr($domain["Domain"], 0, strpos($domain["Domain"], "."));
                    $d->Tld = substr($domain["Domain"], strpos($domain["Domain"], ".") + 1);
                    $d->DNS1 = isset($domain["Information"]["nameservers"][0]) ? $domain["Information"]["nameservers"][0] : "";
                    $d->DNS2 = isset($domain["Information"]["nameservers"][1]) ? $domain["Information"]["nameservers"][1] : "";
                    $d->DNS3 = isset($domain["Information"]["nameservers"][2]) ? $domain["Information"]["nameservers"][2] : "";
                    $d->Debtor = esc($_POST["DialogDebtor"]);
                    $d->AuthKey = isset($domain["Information"]["authkey"]) ? $domain["Information"]["authkey"] : "";
                    $d->RegistrationDate = isset($domain["Information"]["registration_date"]) && $domain["Information"]["registration_date"] != "" ? rewrite_date_db2site($domain["Information"]["registration_date"]) : (isset($domain["Information"]["regdate"]) && $domain["Information"]["regdate"] != "Unknown" ? $domain["Information"]["regdate"] : date("d-m-Y"));
                    $d->ExpirationDate = isset($domain["Information"]["expiration_date"]) && $domain["Information"]["expiration_date"] != "" ? rewrite_date_db2site($domain["Information"]["expiration_date"]) : (isset($domain["Information"]["expires"]) && $domain["Information"]["expires"] != "Unknown" ? $domain["Information"]["expires"] : NULL);
                    $d->Registrar = $registrar_id;
                    if(isset($_POST["Subscription"]) && $_POST["Subscription"] == "yes") {
                        require_once "class/product.php";
                        $product = new product();
                        $product->ProductCode = $output["ProductCode"][$domain_session_id];
                        $product->show();
                        $d->Product = $product->Identifier;
                    }
                    $whois = $domain["Information"]["whois"];
                    require_once "class/handle.php";
                    $handle = new handle();
                    if(is_object($whois)) {
                        $handles_ids = $handle->createHandlesFromImport($whois, $registrar, $d->Debtor);
                    } else {
                        $domain_information = $registrar->api->getDomainInformation($d->Domain . "." . $d->Tld);
                        $whois = $domain_information["Information"]["whois"];
                        $handles_ids = $handle->createHandlesFromImport($whois, $registrar, $d->Debtor);
                    }
                    if(!$handles_ids || !is_array($handles_ids)) {
                        $d->Error = array_merge($d->Error, $handle->Error);
                    }
                    $d->ownerHandle = isset($handles_ids["owner"]) ? $handles_ids["owner"] : 0;
                    $d->adminHandle = isset($handles_ids["admin"]) ? $handles_ids["admin"] : 0;
                    $d->techHandle = isset($handles_ids["tech"]) ? $handles_ids["tech"] : 0;
                    $d->Status = 4;
                    if(count($d->Error) === 0 && $d->add()) {
                        $counter_imported_domains++;
                        $d->moveSyncDate($d->Identifier, "yes");
                        $tld = new topleveldomain();
                        $domain_tld = substr($domain["Domain"], strpos($domain["Domain"], ".") + 1);
                        if($tld->is_free($domain_tld) === true) {
                            $tld->Tld = $domain_tld;
                            $tld->Registrar = $registrar_id;
                            $tld_whoisservers = $tld->getWhoisServers();
                            if(isset($tld_whoisservers[$domain_tld])) {
                                $tld->WhoisServer = $tld_whoisservers[$domain_tld]["server"];
                                $tld->WhoisNoMatch = $tld_whoisservers[$domain_tld]["nomatch"];
                            }
                            $tld->add();
                        }
                        if(isset($_POST["Subscription"]) && $_POST["Subscription"] == "yes") {
                            require_once "class/periodic.php";
                            $subscription = new periodic();
                            $subscription->ProductCode = htmlspecialchars_decode($product->ProductCode);
                            $subscription->Debtor = $d->Debtor;
                            $subscription->Description = esc($output["Description"][$domain_session_id]);
                            $subscription->PeriodicType = "domain";
                            $subscription->Reference = $d->Identifier;
                            $subscription->StartPeriod = esc($output["StartPeriod"][$domain_session_id]);
                            $subscription->EndContract = esc($output["StartPeriod"][$domain_session_id]);
                            $subscription->Periods = esc($output["Periods"][$domain_session_id]);
                            $subscription->Periodic = esc($output["Periodic"][$domain_session_id]);
                            $subscription->PriceExcl = esc($output["PriceExcl"][$domain_session_id]);
                            $subscription->TaxPercentage = $product->TaxPercentage ? btwcheck($d->Debtor, $product->TaxPercentage) : $subscription->TaxPercentage;
                            $subscription->Number = 1;
                            if(!$subscription->StartPeriod) {
                                $subscription->Error[] = sprintf(__("no valid start period for domain"), $d->Domain . "." . $d->Tld);
                            }
                            if($subscription->add()) {
                                $subscription->changeReference($subscription->Identifier, "domain", $d->Identifier);
                            } else {
                                $d->deleteFromDatabase($d->Identifier);
                                $d->Success = [];
                                $counter_imported_domains--;
                            }
                        }
                        $domain_info = ["id" => $d->Identifier, "Debtor" => $d->Debtor, "Domain" => $d->Domain, "Tld" => $d->Tld];
                        do_action("domain_is_imported", $domain_info);
                    } else {
                        $d->Error[] = sprintf(__("domain is not imported"), $d->Domain . "." . $d->Tld);
                    }
                    $d->Success = [];
                    flashMessage($d);
                    if(isset($subscription) && is_object($subscription)) {
                        $subscription->Success = [];
                        flashMessage($subscription);
                        unset($subscription);
                    }
                    if(is_array($handles_ids)) {
                        foreach ($handles_ids as $handle_id) {
                            $handle->updateGeneralToDebtor($handle_id);
                        }
                    }
                }
                if(0 < $counter_imported_domains) {
                    if(isset($_POST["Subscription"]) && $_POST["Subscription"] == "yes") {
                        $error_class->Success[] = sprintf(__("domains imported, which will be invoiced"), $counter_imported_domains);
                    } else {
                        $error_class->Success[] = sprintf(__("domains imported, which will not be invoiced"), $counter_imported_domains);
                    }
                }
                flashMessage();
                header("Location:registrars.php?page=import&id=" . $registrar_id);
                exit;
            } elseif(isset($_POST["import_action"]) && $_POST["import_action"] == "existingDomains") {
                $registrar_id = intval(esc($_GET["id"]));
                $list_domains = $_POST["existingdomains"];
                $list_session_domains = unserialize($_SESSION["Registrar_" . $registrar_id . "_import_response"]);
                require_once "class/registrar.php";
                $registrar = new registrar();
                $registrar->show($registrar_id);
                require_once "3rdparty/domain/" . $registrar->Class . "/" . $registrar->Class . ".php";
                $registrar->api = new $registrar->Class();
                $registrar->api->User = $registrar->User;
                $registrar->api->Password = passcrypt($registrar->Password);
                $registrar->api->Testmode = $registrar->Testmode;
                $registrar->api->License = $registrar->License;
                $registrar->api->Setting1 = $registrar->Setting1;
                $registrar->api->Setting2 = $registrar->Setting2;
                $registrar->api->Setting3 = $registrar->Setting3;
                require_once "class/domain.php";
                foreach ($list_domains as $domain_id) {
                    $domain_id = esc($domain_id);
                    $domain = new domain();
                    $domain->Identifier = $domain_id;
                    $domain->show();
                    $domain->Registrar = $registrar_id;
                    foreach ($list_session_domains as $session_domain) {
                        if($session_domain["Domain"] == $domain->Domain . "." . $domain->Tld) {
                            $registrar_domain = $session_domain;
                            require_once "class/handle.php";
                            $handle = new handle();
                            if((!isset($registrar_domain["Information"]["whois"]) || !is_object($registrar_domain["Information"]["whois"]) || !isset($registrar_domain["Information"]["nameservers"]) || !is_array($registrar_domain["Information"]["nameservers"]) || !$registrar_domain["Information"]["nameservers"][0] || !isset($registrar_domain["Information"]["authkey"]) || !$registrar_domain["Information"]["authkey"] || !isset($registrar_domain["Information"]["registration_date"]) && !$registrar_domain["Information"]["registration_date"] && !isset($registrar_domain["Information"]["regdate"]) && $registrar_domain["Information"]["regdate"] == "Unknown" || !isset($registrar_domain["Information"]["registration_date"]) && !$registrar_domain["Information"]["registration_date"] && !isset($registrar_domain["Information"]["expires"]) && $registrar_domain["Information"]["expires"] != "Unknown") && ($domain_information = $registrar->api->getDomainInformation($domain->Domain . "." . $domain->Tld))) {
                                $registrar_domain = $domain_information;
                            }
                            $whois = $registrar_domain["Information"]["whois"];
                            $handles_ids = $handle->createHandlesFromImport($whois, $registrar, $domain->Debtor);
                            $domain->ownerHandle = isset($handles_ids["owner"]) ? $handles_ids["owner"] : 0;
                            $domain->adminHandle = isset($handles_ids["admin"]) ? $handles_ids["admin"] : 0;
                            $domain->techHandle = isset($handles_ids["tech"]) ? $handles_ids["tech"] : 0;
                            $domain->DNS1 = isset($registrar_domain["Information"]["nameservers"][0]) ? strtolower($registrar_domain["Information"]["nameservers"][0]) : "";
                            $domain->DNS2 = isset($registrar_domain["Information"]["nameservers"][1]) ? strtolower($registrar_domain["Information"]["nameservers"][1]) : "";
                            $domain->DNS3 = isset($registrar_domain["Information"]["nameservers"][2]) ? strtolower($registrar_domain["Information"]["nameservers"][2]) : "";
                            $domain->AuthKey = isset($registrar_domain["Information"]["authkey"]) ? $registrar_domain["Information"]["authkey"] : "";
                            $domain->RegistrationDate = isset($registrar_domain["Information"]["registration_date"]) && $registrar_domain["Information"]["registration_date"] != "" ? $registrar_domain["Information"]["registration_date"] : (isset($registrar_domain["Information"]["regdate"]) && $registrar_domain["Information"]["regdate"] != "Unknown" ? rewrite_date_site2db($registrar_domain["Information"]["regdate"]) : rewrite_date_site2db($domain->RegistrationDate));
                            $domain->ExpirationDate = isset($registrar_domain["Information"]["expiration_date"]) && $registrar_domain["Information"]["expiration_date"] != "" ? $registrar_domain["Information"]["expiration_date"] : (isset($registrar_domain["Information"]["expires"]) && $registrar_domain["Information"]["expires"] != "Unknown" ? rewrite_date_site2db($registrar_domain["Information"]["expires"]) : rewrite_date_site2db($domain->ExpirationDate));
                            if($domain->changeRegistrar()) {
                                $error_class->Success[] = sprintf(__("domain transfered to registrar"), $domain->Domain . "." . $domain->Tld, $registrar->Name);
                            } else {
                                $error_class->Error[] = sprintf(__("domain is not imported"), $d->Domain . "." . $d->Tld);
                            }
                            if(is_array($handles_ids)) {
                                foreach ($handles_ids as $handle_id) {
                                    $handle->updateGeneralToDebtor($handle_id);
                                }
                            }
                            flashMessage($domain);
                            unset($domain);
                        }
                    }
                }
                unset($_SESSION["Registrar_" . $registrar_id . "_import_response"]);
            }
        }
        break;
    case "importHandles":
        if(!U_SERVICEMANAGEMENT_EDIT) {
        } elseif(isset($_POST["Debtor"]) && is_array($_POST["handles"])) {
            require_once "class/handle.php";
            $list_handles = unserialize($_SESSION["Registrar_" . $registrar_id . "_import2_response"]);
            foreach ($_POST["handles"] as $key => $handle) {
                $handle = $list_handles[$handle];
                $h = new handle();
                $h->Debtor = $_POST["Debtor"];
                $h->Handle = $handle["Handle"];
                $h->Registrar = $registrar_id;
                $h->RegistrarHandle = $handle["Handle"];
                $h->Initials = $handle["Initials"];
                $h->SurName = $handle["SurName"];
                $h->Address = $handle["Address"];
                $h->Address2 = $handle["Address2"];
                $h->ZipCode = $handle["ZipCode"];
                $h->City = $handle["City"];
                $h->State = $handle["State"];
                $h->Country = array_key_exists($handle["Country"], $array_country) ? $handle["Country"] : (array_key_exists("EU-" . $handle["Country"], $array_country) ? "EU-" . $handle["Country"] : "");
                $h->PhoneNumber = $handle["PhoneNumber"];
                $h->FaxNumber = $handle["FaxNumber"];
                $h->EmailAddress = $handle["EmailAddress"];
                $h->Sex = $handle["Sex"];
                $h->CompanyName = $handle["CompanyName"];
                $h->TaxNumber = $handle["TaxNumber"];
                $h->HandleType = $handle["HandleType"];
                $h->Status = 1;
                $h->add();
                flashMessage($h);
                unset($h);
            }
        }
        break;
    case "uploadHandles":
        if(!U_SERVICEMANAGEMENT_EDIT) {
        } elseif(!empty($_FILES)) {
            $type = esc($_POST["Type"]);
            require_once "class/handle.php";
            foreach ($_FILES as $value) {
                if(empty($value["error"])) {
                    $con = @fopen($value["tmp_name"], "r");
                    $content = @fread($con, @filesize($value["tmp_name"]));
                    fclose($con);
                    if($content) {
                        $sidn2handletype = ["Houder" => "DOHO", "AdminC" => "DOAC", "TechC" => "DOTC"];
                        $content = explode("\n", $content);
                        foreach ($content as $v) {
                            if($v) {
                                $array = explode(",", $v);
                                $handle = new handle();
                                $handle->SIDNIMPORT = true;
                                $handle->Handle = $type == "dph" ? $array[0] : $array[3];
                                $handle->HandleType = $type == "dph" ? $sidn2handletype[$array[2]] : $sidn2handletype[$array[2]];
                                if($handle->Handle != "Handle:" && 1 < strlen($handle->Handle)) {
                                    $list_handles[] = ["Handle" => $handle->Handle, "HandleType" => $handle->HandleType];
                                }
                                unset($handle);
                            }
                        }
                    }
                }
            }
            $_SESSION["Registrar_" . $registrar_id . "_import2_response"] = serialize($list_handles);
        }
        break;
    case "show":
        $pagetype = "add";
        require_once "class/registrar.php";
        $registrar = isset($registrar) && is_object($registrar) ? $registrar : new registrar();
        $registrar->show($registrar_id);
        $registrar->getVersionInfo();
        $registrar_api_list = $registrar->getAPIs();
        if($registrar->DomainEnabled == "yes") {
            require_once "class/domain.php";
            $domain = new domain();
            $session = isset($_SESSION["registrar.show.domain"]) ? $_SESSION["registrar.show.domain"] : [];
            $fields = ["Domain", "Tld", "Debtor", "CompanyName", "SurName", "Initials", "RegistrationDate", "ExpirationDate", "Type", "AuthKey", "Status", "PeriodicID", "PriceExcl", "Periodic", "NextDate", "StartPeriod", "TerminationDate", "AutoRenew", "DomainAutoRenew", "TerminationID"];
            $sort = isset($session["sort"]) ? $session["sort"] : "Domain";
            $order = isset($session["order"]) ? $session["order"] : "ASC";
            $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) && $session["limit"] ? $session["limit"] : "1");
            $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : min(25, MAX_RESULTS_LIST));
            $list_registrar_domains = $domain->all($fields, $sort, $order, $limit, "Registrar", $registrar_id, false, $show_results);
            $_SESSION["registrar.show.domain"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "limit" => $limit];
            $current_page = $limit;
        }
        require_once "class/handle.php";
        $handle = new handle();
        $session = isset($_SESSION["registrar.show.handle"]) ? $_SESSION["registrar.show.handle"] : [];
        $fields = ["Handle", "Debtor", "DebtorCode", "CompanyName", "SurName", "Initials", "HandleType", "RegistrarHandle"];
        $sort = isset($session["sort"]) ? $session["sort"] : "Handle";
        $order = isset($session["order"]) ? $session["order"] : "ASC";
        $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) && $session["limit"] ? $session["limit"] : "1");
        $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : min(25, MAX_RESULTS_LIST));
        $list_registrar_handles_all = $handle->all($fields, $sort, $order, -1, "Registrar", $registrar_id, false);
        $list_registrar_handles = $handle->all($fields, $sort, $order, $limit, "Registrar", $registrar_id, false, $show_results);
        $_SESSION["registrar.show.handle"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "limit" => $limit];
        $current_page = $limit;
        if($registrar->Status == 9) {
            $registrar->Warning[] = __("the registrar is not active");
        }
        $message = parse_message($registrar, isset($domain) ? $domain : NULL, $handle);
        $wfh_page_title = __("registrar") . " " . $registrar->Name;
        $current_page_url = "registrars.php?page=show&id=" . $registrar_id;
        $sidebar_template = "servicemanagement.sidebar.php";
        require_once "views/registrar.show.php";
        break;
    case "add":
        checkRight(U_SERVICEMANAGEMENT_EDIT);
        require_once "class/registrar.php";
        $registrar = isset($registrar) && is_object($registrar) ? $registrar : new registrar();
        $registrar_api_list = $registrar->getAPIs();
        if($pagetype == "edit" && (!isset($error) || $error === false)) {
            $registrar->show($registrar_id);
            $registrar->getVersionInfo();
        } elseif(isset($_GET["module"]) && isset($registrar_api_list[$_GET["module"]])) {
            $registrar->Name = $registrar_api_list[$_GET["module"]]["name"];
            $registrar->Class = esc($_GET["module"]);
        }
        require_once "class/handle.php";
        $handle = new handle();
        if($pagetype == "edit") {
            $fields = ["Handle", "CompanyName", "SurName", "Initials", "RegistrarHandle"];
            $list_registrar_handles = $handle->all($fields, "Handle", "ASC", -1, "Registrar", $registrar_id);
        }
        $message = parse_message($registrar, $handle);
        $wfh_page_title = $pagetype == "edit" ? __("edit registrar") : __("add registrar");
        $current_page_url = "registrars.php";
        $sidebar_template = "servicemanagement.sidebar.php";
        require_once "views/registrar.add.php";
        break;
    case "import":
        checkRight(U_SERVICEMANAGEMENT_EDIT);
        require_once "class/whois.php";
        require_once "class/registrar.php";
        $registrar = isset($registrar) && is_object($registrar) ? $registrar : new registrar();
        $registrar->show($registrar_id);
        if(!unserialize($_SESSION["Registrar_" . $registrar_id . "_import_response"])) {
            unset($_SESSION["Registrar_" . $registrar_id . "_import_response"]);
        }
        if(!isset($_SESSION["Registrar_" . $registrar_id . "_import_response"]) || isset($_GET["action"]) && $_GET["action"] == "refresh") {
            $list_domains = $registrar->importDomainList();
            if(empty($list_domains) && !empty($registrar->Error)) {
                flashMessage($registrar);
                header("Location: registrars.php?page=show&id=" . $registrar->Identifier);
                exit;
            }
            $_SESSION["Registrar_" . $registrar_id . "_import_response"] = serialize($list_domains);
        }
        $list_domains = unserialize($_SESSION["Registrar_" . $registrar_id . "_import_response"]);
        require_once "class/domain.php";
        $domain = new domain();
        $fields = ["Domain", "Tld", "Debtor", "CompanyName", "SurName", "Initials", "Name", "RegistrationDate", "ExpirationDate", "Registrar", "Status", "TerminationDate", "TerminatedDate"];
        $fulllist_hostfact_domains = $domain->all($fields);
        $temp_hostfact_domains = [];
        foreach ($fulllist_hostfact_domains as $k => $value) {
            if($k != "CountRows") {
                $temp_hostfact_domains[] = strtolower($value["Domain"] . "." . $value["Tld"]);
                $fulllist_hostfact_domains[strtolower($value["Domain"] . "." . $value["Tld"])] = $value;
                unset($fulllist_hostfact_domains[$k]);
            }
        }
        $list_hostfact_domains = $temp_hostfact_domains;
        unset($temp_hostfact_domains);
        $domain = new domain();
        $fields = ["Domain", "Tld", "Debtor", "CompanyName", "SurName", "Initials", "RegistrationDate", "ExpirationDate", "Registrar", "Status"];
        $list_registrar_domains = $domain->all($fields, "Domain", "ASC", -1, "Registrar", $registrar_id);
        $temp_registrar_domains = [];
        foreach ($list_registrar_domains as $k => $value) {
            if($k != "CountRows") {
                $temp_registrar_domains[] = strtolower($value["Domain"] . "." . $value["Tld"]);
            }
        }
        $list_registrar_domains = $temp_registrar_domains;
        unset($temp_registrar_domains);
        require_once "class/debtor.php";
        $debtor = new debtor();
        $list_debtors = $debtor->all_small();
        require_once "class/product.php";
        $product = new product();
        $fields = ["ProductCode", "ProductName", "ProductType", "PriceExcl", "Sold", "PricePeriod", "Groups"];
        $list_products = $product->all($fields, "ProductCode", "ASC", -1, "ProductType", "domain");
        $message = parse_message($registrar, $domain, $debtor, $product);
        $wfh_page_title = __("import domains from") . " " . $registrar->Name;
        $current_page_url = "registrars.php?page=import&id=" . $registrar_id;
        $sidebar_template = "servicemanagement.sidebar.php";
        require_once "views/registrar.import.domain.php";
        break;
    case "importHandles":
        checkRight(U_SERVICEMANAGEMENT_EDIT);
        require_once "class/whois.php";
        require_once "class/registrar.php";
        if(!isset($domain)) {
            $domain = "";
        }
        $registrar = isset($registrar) && is_object($registrar) ? $registrar : new registrar();
        $registrar->show($registrar_id);
        if(isset($_SESSION["Registrar_" . $registrar_id . "_import2_response"]) && !unserialize($_SESSION["Registrar_" . $registrar_id . "_import2_response"])) {
            unset($_SESSION["Registrar_" . $registrar_id . "_import2_response"]);
        }
        if(!isset($_SESSION["Registrar_" . $registrar_id . "_import2_response"]) || isset($_GET["action"]) && $_GET["action"] == "refresh") {
            $list_handles = $registrar->importHandleList();
            if(empty($list_handles) && !empty($registrar->Error)) {
                flashMessage($registrar);
                header("Location: registrars.php?page=show&id=" . $registrar->Identifier);
                exit;
            }
            $_SESSION["Registrar_" . $registrar_id . "_import2_response"] = serialize($list_handles);
        } else {
            $list_handles = unserialize($_SESSION["Registrar_" . $registrar_id . "_import2_response"]);
        }
        require_once "class/handle.php";
        $handle = new handle();
        $fields = ["Handle", "Registrar", "RegistrarHandle", "Debtor", "CompanyName", "SurName", "Initials", "Status"];
        $fulllist_hostfact_handles = $handle->all($fields);
        $temp_hostfact_handles = [];
        foreach ($fulllist_hostfact_handles as $k => $value) {
            if($k != "CountRows") {
                $temp_hostfact_handles[] = $value["RegistrarHandle"];
                $fulllist_hostfact_handles[$value["RegistrarHandle"]] = $value;
                unset($fulllist_hostfact_handles[$k]);
            }
        }
        $list_hostfact_handles = $temp_hostfact_handles;
        $handle = new handle();
        $list_registrar_handles = $handle->all($fields, "Handle", "ASC", -1, "Registrar", $registrar_id);
        $temp_registrar_handles = [];
        foreach ($list_registrar_handles as $k => $value) {
            if($k != "CountRows") {
                $temp_registrar_handles[] = $value["RegistrarHandle"];
            }
        }
        $list_registrar_handles = $temp_registrar_handles;
        require_once "class/debtor.php";
        $debtor = new debtor();
        $list_debtors = $debtor->all_small();
        $message = parse_message($registrar, $domain, $debtor);
        $wfh_page_title = __("import handles from") . " " . $registrar->Name;
        $current_page_url = "registrars.php?page=import&id=" . $registrar_id;
        $sidebar_template = "servicemanagement.sidebar.php";
        require_once "views/registrar.import.handle.php";
        break;
    default:
        require_once "class/registrar.php";
        $registrar = isset($registrar) && is_object($registrar) ? $registrar : new registrar();
        $session = isset($_SESSION["registrar.overview"]) ? $_SESSION["registrar.overview"] : [];
        $fields = ["Name", "Testmode", "User", "Status", "Class", "DomainEnabled", "SSLEnabled"];
        $sort = isset($session["sort"]) ? $session["sort"] : "Name";
        $order = isset($session["order"]) ? $session["order"] : "ASC";
        $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) ? $session["limit"] : "1");
        $searchat = isset($session["searchat"]) ? $session["searchat"] : "Name";
        $searchfor = isset($session["searchfor"]) ? $session["searchfor"] : "";
        $selectgroup = isset($session["group"]) ? $session["group"] : "";
        $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : MAX_RESULTS_LIST);
        $list_registrars = $registrar->all($fields, $sort, $order, $limit, $searchat, $searchfor, $selectgroup, $show_results);
        if(isset($list_registrars["CountRows"]) && ($list_registrars["CountRows"] < $show_results * ($limit - 1) || 1 < $limit && $list_registrars["CountRows"] == $show_results * ($limit - 1))) {
            $newPage = ceil($list_registrars["CountRows"] / $show_results);
            if($newPage <= 0) {
                $newPage = 1;
            }
            $_SESSION["registrar.overview"]["limit"] = $newPage;
            header("Location: registrars.php");
            exit;
        }
        $_SESSION["registrar.overview"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "group" => $selectgroup, "searchat" => $searchat, "searchfor" => $searchfor, "limit" => $limit];
        $current_page = $limit;
        $list_domaincount_registrars = $registrar->countNumberOfDomains();
        if(is_module_active("ssl")) {
            global $_module_instances;
            $ssl = $_module_instances["ssl"];
            $list_sslcount_registrars = $ssl->countNumberofCertificatesPerRegistrar();
        }
        $message = parse_message($registrar);
        $wfh_page_title = __("registrar overview");
        $current_page_url = "registrars.php";
        $sidebar_template = "servicemanagement.sidebar.php";
        require_once "views/registrar.overview.php";
}

?>