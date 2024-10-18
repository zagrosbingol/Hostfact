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
$package_id = isset($_GET["id"]) ? intval(esc($_GET["id"])) : (isset($_POST["id"]) ? intval(esc($_POST["id"])) : NULL);
switch ($page) {
    case "add":
        require_once "class/template.php";
        $emailtemplatelist = new emailtemplate();
        $fields = ["Name"];
        $emailtemplates = $emailtemplatelist->all($fields);
        $templatelist = new template();
        $templatelist->Type = "other";
        $templates_other = $templatelist->all($fields, false, false, "-1", "Type", "other");
        $pagetype = "add";
        if(isset($_POST["PackageName"])) {
            if(!U_SERVICEMANAGEMENT_EDIT) {
            } else {
                require_once "class/package.php";
                $package = new package();
                foreach ($_POST as $key => $value) {
                    if(in_array($key, $package->Variables)) {
                        $package->{$key} = esc($value);
                    }
                }
                $package->EmailAuto = isset($_POST["EmailAuto"]) && 0 < $_POST["EmailTemplate"] ? "yes" : "no";
                $package->Template = $package->TemplateName ? "yes" : "no";
                if($package->add()) {
                    $page = "show";
                    $package_id = $package->Identifier;
                    if($_POST["ProductUpdate"] == "yes" && 0 < $package->Product) {
                        require_once "class/product.php";
                        $product = new product();
                        $product->updatePackageID($package->Product, $package_id);
                    }
                    header("Location: packages.php?page=show&id=" . $package_id);
                    exit;
                }
                foreach ($package->Variables as $key) {
                    $package->{$key} = htmlspecialchars($package->{$key});
                }
            }
        }
        break;
    case "edit":
        $pagetype = "edit";
        $page = "add";
        require_once "class/template.php";
        $emailtemplatelist = new emailtemplate();
        $fields = ["Name"];
        $emailtemplates = $emailtemplatelist->all($fields);
        $templatelist = new template();
        $templatelist->Type = "other";
        $templates_other = $templatelist->all($fields, false, false, "-1", "Type", "other");
        if(empty($_POST) || !U_SERVICEMANAGEMENT_EDIT) {
        } else {
            require_once "class/package.php";
            $package = new package();
            $package->show($package_id);
            $old_product = $package->Product;
            if(!isset($_POST["TemplateName"])) {
                $_POST["TemplateName"] = "";
            }
            foreach ($_POST as $key => $value) {
                if(in_array($key, $package->Variables)) {
                    $package->{$key} = esc($value);
                }
            }
            $package->EmailAuto = isset($_POST["EmailAuto"]) && 0 < $_POST["EmailTemplate"] ? "yes" : "no";
            $package->Template = $package->TemplateName ? "yes" : "no";
            if($package->edit($package_id)) {
                $page = "show";
                if($_POST["ProductUpdate"] == "yes") {
                    if($package->Product <= 0 && 0 < $old_product) {
                        require_once "class/product.php";
                        $product = new product();
                        $product->show($old_product);
                        if($product->PackageID == $package_id) {
                            $product->updatePackageID($old_product, 0);
                        }
                    } elseif(0 < $package->Product) {
                        require_once "class/product.php";
                        $product = new product();
                        $product->updatePackageID($package->Product, $package_id);
                    }
                }
                header("Location: packages.php?page=show&id=" . $package_id);
                exit;
            }
            foreach ($package->Variables as $key) {
                $package->{$key} = htmlspecialchars($package->{$key});
            }
            $error = true;
        }
        break;
    case "delete":
        $pagetype = "confirmDelete";
        if($package_id != NULL) {
            $page = "show";
        } else {
            $page = "overview";
        }
        if(empty($_POST) || !U_SERVICEMANAGEMENT_DELETE) {
        } elseif(isset($_POST["imsure"]) && $_POST["imsure"] == "yes") {
            require_once "class/package.php";
            $package = new package();
            $result = $package->delete($package_id);
            if($result) {
                $package_id = NULL;
                $page = "overview";
                if(!empty($_SESSION["ActionLog"]["Package"]["delete"])) {
                    array_shift($_SESSION["ActionLog"]["Package"]["delete"]);
                    if(!empty($_SESSION["ActionLog"]["Package"]["delete"])) {
                        header("Location: ?page=delete&id=" . current($_SESSION["ActionLog"]["Package"]["delete"]));
                        exit;
                    }
                }
            }
            $package->Success = [];
            $package->Success[] = __("one or more packages are deleted");
            flashMessage($package);
            header("Location: packages.php");
            exit;
        }
        break;
    default:
        if(isset($_SESSION["ActionLog"]["Package"]["delete"]) && is_array($_SESSION["ActionLog"]["Package"]["delete"])) {
            unset($_SESSION["ActionLog"]["Package"]["delete"]);
        }
        if(isset($_POST["action"]) && $_POST["action"] == "delete") {
            $list_packages = is_array($_POST["packages"]) ? $_POST["packages"] : [];
            if(empty($list_packages)) {
            } elseif(!U_SERVICEMANAGEMENT_DELETE) {
            } else {
                if(!isset($_SESSION["ActionLog"]["Package"])) {
                    $_SESSION["ActionLog"]["Package"] = [];
                }
                $_SESSION["ActionLog"]["Package"]["delete"] = [];
                foreach ($list_packages as $p_id) {
                    $_SESSION["ActionLog"]["Package"]["delete"][] = $p_id;
                }
                if(!empty($_SESSION["ActionLog"]["Package"]["delete"])) {
                    header("Location: ?page=delete&id=" . current($_SESSION["ActionLog"]["Package"]["delete"]));
                    exit;
                }
            }
            switch ($page) {
                case "show":
                    require_once "class/package.php";
                    $package = new package();
                    $package->show($package_id);
                    require_once "class/template.php";
                    $emailTemplate = new emailtemplate();
                    if(isset($package->EmailTemplate) && 0 < $package->EmailTemplate) {
                        $emailTemplate->Identifier = $package->EmailTemplate;
                        $emailTemplate->show();
                    }
                    $pdfTemplate = new template();
                    if(isset($package->PdfTemplate) && 0 < $package->PdfTemplate) {
                        $pdfTemplate->Identifier = $package->PdfTemplate;
                        $pdfTemplate->show();
                    }
                    if($package->Status == 9) {
                        $error_class->Warning[] = __("this package is removed from software and cannot be used for new hosting accounts");
                    }
                    require_once "class/server.php";
                    $server = new server();
                    $server->show($package->Server);
                    if($package->Template == "yes" && $package->Status != 9) {
                        $api = $server->connect();
                        if(is_object($api)) {
                            if($package->PackageType == "reseller") {
                                $api->getPackage($package->TemplateName, true);
                            } else {
                                $api->getPackage($package->TemplateName);
                            }
                            $package->updatePackageInfo($api, $package_id);
                            if(!empty($api->Error)) {
                                $package->Error = array_merge($package->Error, $api->Error);
                            }
                        }
                    }
                    require_once "class/hosting.php";
                    $hosting = new hosting();
                    $session = isset($_SESSION["package.show.accounts"]) ? $_SESSION["package.show.accounts"] : [];
                    $fields = ["Username", "Debtor", "CompanyName", "SurName", "Initials", "Domain", "Server", "Name", "Status", "PeriodicID", "PriceExcl", "Periodic", "NextDate", "StartPeriod", "TerminationDate", "AutoRenew", "TerminationID"];
                    $sort = isset($session["sort"]) ? $session["sort"] : "Username";
                    $order = isset($session["order"]) ? $session["order"] : "ASC";
                    $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) ? $session["limit"] : "1");
                    $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : min(25, MAX_RESULTS_LIST));
                    $list_hosting_accounts = $hosting->all($fields, $sort, $order, $limit, "Package", $package_id, false, $show_results);
                    $_SESSION["package.show.accounts"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "limit" => $limit];
                    $current_page = $limit;
                    require_once "class/product.php";
                    $product = new product();
                    $list_hosting_products = $product->all(["ProductCode", "ProductName", "ProductKeyPhrase", "PriceExcl", "PricePeriod"]);
                    $message = parse_message($package);
                    $wfh_page_title = __("package") . " " . $package->PackageName;
                    $current_page_url = "packages.php?page=show&id=" . $package_id;
                    $sidebar_template = "servicemanagement.sidebar.php";
                    require_once "views/package.show.php";
                    break;
                case "add":
                    checkRight(U_SERVICEMANAGEMENT_EDIT);
                    require_once "class/package.php";
                    $package = isset($package) && is_object($package) ? $package : new package();
                    if($pagetype == "edit" && (!isset($error) || $error === false)) {
                        $package->show($package_id);
                    }
                    if(isset($_GET["server_id"]) && is_numeric($_GET["server_id"])) {
                        $package->Server = intval(esc($_GET["server_id"]));
                    }
                    if($package->Template == "no") {
                        $package->Warning[] = __("use of old packages without templates, please update");
                    }
                    require_once "class/product.php";
                    $product = new product();
                    $list_hosting_products = $product->all(["ProductCode", "ProductName", "ProductType"]);
                    require_once "class/server.php";
                    $server = new server();
                    $list_servers = $server->all(["Name"]);
                    $list_server_packages = $server->getListPackages($package->Server);
                    $message = parse_message($package, $product, $server);
                    $wfh_page_title = $pagetype == "edit" ? __("edit package") : __("add package");
                    $current_page_url = "packages.php";
                    $sidebar_template = "servicemanagement.sidebar.php";
                    require_once "views/package.add.php";
                    break;
                default:
                    require_once "class/server.php";
                    $server = isset($server) && is_object($server) ? $server : new server();
                    $fields = ["Name"];
                    $list_servers = $server->all($fields);
                    require_once "class/package.php";
                    $package = isset($package) && is_object($package) ? $package : new package();
                    $session = isset($_SESSION["package.overview"]) ? $_SESSION["package.overview"] : [];
                    $sort = isset($session["sort"]) ? $session["sort"] : "PackageName";
                    $order = isset($session["order"]) ? $session["order"] : "ASC";
                    $_SESSION["package.overview"] = ["sort" => $sort, "order" => $order];
                    $fields = ["PackageName", "PackageType", "Template", "TemplateName", "Product", "ProductCode", "ProductName", "BandWidth", "uBandWidth", "DiscSpace", "uDiscSpace"];
                    $list_hosting_packages = [];
                    foreach ($list_servers as $k => $server_tmp) {
                        if(is_numeric($k)) {
                            $list_hosting_packages[$server_tmp["id"]] = [];
                            $list_hosting_packages[$server_tmp["id"]] = $package->all($fields, $sort, $order, -1, "Server", $server_tmp["id"]);
                        }
                    }
                    $message = parse_message($server, $package);
                    $wfh_page_title = __("packages");
                    $current_page_url = "packages.php";
                    $sidebar_template = "servicemanagement.sidebar.php";
                    require_once "views/package.overview.php";
            }
        }
}

?>