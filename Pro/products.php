<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "config.php";
checkRight(U_PRODUCT_SHOW);
$page = isset($_GET["page"]) ? $_GET["page"] : "";
$product_id = isset($_GET["id"]) ? intval(esc($_GET["id"])) : (isset($_POST["id"]) ? intval(esc($_POST["id"])) : NULL);
$page = $page == "edit" ? "add" : $page;
switch ($page) {
    case "add":
        if(0 < $product_id) {
            $pagetype = "edit";
        } else {
            $pagetype = "add";
        }
        require_once "class/template.php";
        $emailtemplatelist = new emailtemplate();
        $fields = ["Name"];
        $emailtemplates = $emailtemplatelist->all($fields);
        $templatelist = new template();
        $templatelist->Type = "other";
        $templates_other = $templatelist->all($fields, false, false, "-1", "Type", "other");
        if(empty($_POST) || !U_PRODUCT_EDIT && $pagetype == "add" || !U_PRODUCT_EDIT && $pagetype == "edit") {
        } else {
            require_once "class/product.php";
            $product = new product();
            if(isset($product_id) && 0 < $product_id) {
                $product->Identifier = $product_id;
                $product->show();
                $pagetype = "edit";
            }
            $old_data = [];
            if(isset($product_id) && 0 < $product_id) {
                $old_data["ProductName"] = $product->ProductName;
                $old_data["ProductType"] = $product->ProductType;
            }
            if($product->HasCustomPrice != "no" && isset($_POST["HasCustomPrice"]) && $_POST["HasCustomPrice"] == "no") {
                $product->HasCustomPrice = "no";
                $product->removeCustomProductPrices();
            }
            foreach ($_POST as $key => $value) {
                if(in_array($key, $product->Variables)) {
                    $product->{$key} = esc($value);
                }
            }
            if(defined("VAT_CALC_METHOD") && VAT_CALC_METHOD == "incl" && isset($_POST["PriceIncl"])) {
                $product->PriceExcl = deformat_money(esc($_POST["PriceIncl"])) / (1 + $product->TaxPercentage);
            }
            $product->Groups = isset($_POST["Groups"]) ? esc($_POST["Groups"]) : [];
            if(isset($_POST["HasCustomPrice"]) && $_POST["HasCustomPrice"] == "period") {
                $extra_prices = is_array($_POST["CustomPrices"]["period"]["Periods"]) ? count($_POST["CustomPrices"]["period"]["Periods"]) - 1 : 0;
                if(0 < $extra_prices) {
                    $extra_prices_array = [];
                    for ($i = 0; $i < $extra_prices; $i++) {
                        $extra_prices_array[] = ["Periods" => esc($_POST["CustomPrices"]["period"]["Periods"][$i]), "Periodic" => esc($_POST["CustomPrices"]["period"]["Periodic"][$i]), "PriceExcl" => isset($_POST["CustomPrices"]["period"]["PriceExcl"][$i]) ? esc($_POST["CustomPrices"]["period"]["PriceExcl"][$i]) : false, "PriceIncl" => isset($_POST["CustomPrices"]["period"]["PriceIncl"][$i]) ? esc($_POST["CustomPrices"]["period"]["PriceIncl"][$i]) : false];
                    }
                } else {
                    $product->HasCustomPrice = "no";
                    if($product->Identifier) {
                        $product->removeCustomProductPrices();
                    }
                }
            }
            if(1 <= $product->Identifier) {
                if($product->edit($product->Identifier)) {
                    $page = "show";
                } else {
                    $page = "add";
                }
            } elseif($product->add()) {
                $product_id = $product->Identifier;
                $page = "show";
            } else {
                $page = "add";
            }
            if($product->HasCustomPrice == "period" && isset($extra_prices_array) && !empty($extra_prices_array) && !$product->updateCustomProductPrices($extra_prices_array)) {
                $product->fixCustomPriceSetting();
            }
            if(array_key_exists($product->ProductType, $additional_product_types)) {
                $module_name = $product->ProductType;
                $namespace = "modules\\products\\" . $module_name;
                $classname = class_exists($namespace . "\\" . $module_name) ? $namespace . "\\" . $module_name : $module_name;
                $module = new $classname();
                if($pagetype == "add") {
                    if(!($result = $module->product_add($product->Identifier))) {
                        $product->remove($product->Identifier);
                        $product_id = "";
                        $product->Identifier = 0;
                        $page = "add";
                    }
                } elseif(!($result = $module->product_edit($product->Identifier))) {
                    $page = "add";
                    $product->resetProductType($product->Identifier);
                }
                if(!$result) {
                    $product->Success = $product->Warning = [];
                    $product->Error = array_merge($product->Error, $module->Error);
                }
            } elseif($product->ProductType == "domain") {
                require_once "class/topleveldomain.php";
                $topleveldomain = new topleveldomain();
                if(!$topleveldomain->showbyTLD($product->ProductTld)) {
                    $topleveldomain = new topleveldomain();
                    $topleveldomain->Tld = $product->ProductTld;
                    foreach ($_POST as $key => $value) {
                        if(in_array($key, $topleveldomain->Variables)) {
                            $topleveldomain->{$key} = esc($value);
                        }
                    }
                    if($topleveldomain->add()) {
                    } else {
                        if(!isset($old_data["ProductType"])) {
                            $product->remove($product->Identifier);
                            $product_id = "";
                            $product->Identifier = 0;
                            $page = "add";
                        } else {
                            $page = "add";
                            $product->resetProductType($product->Identifier);
                        }
                        $product->Success = $product->Warning = $product->Error = [];
                        $product->Error[] = __("couldnt create topleveldomain from product");
                    }
                }
            } elseif($product->ProductType == "hosting") {
                require_once "class/package.php";
                $package = new package();
                if(isset($_POST["TemplateName"]) && substr($_POST["TemplateName"], 0, 3) == "ex:") {
                    $package->Identifier = esc(substr($_POST["TemplateName"], 3));
                    $package->show();
                    $package->updateProductReference($package->Identifier, $product->Identifier);
                    $product->updatePackageID($product->Identifier, $package->Identifier);
                } else {
                    foreach ($_POST as $key => $value) {
                        if(in_array($key, $package->Variables)) {
                            $package->{$key} = esc($value);
                        }
                    }
                    $package->Product = $product->Identifier;
                    $package->PackageName = $product->ProductName;
                    $package->EmailAuto = isset($_POST["EmailAuto"]) && 0 < $_POST["EmailTemplate"] ? "yes" : "no";
                    if($package->add()) {
                        $product->updatePackageID($product->Identifier, $package->Identifier);
                        $server = new server();
                        $server->show($package->Server);
                        if($package->Template == "yes") {
                            $api = $server->connect();
                            if(is_object($api)) {
                                if($package->PackageType == "reseller") {
                                    $api->getPackage($package->TemplateName, true);
                                } else {
                                    $api->getPackage($package->TemplateName);
                                }
                                $package->updatePackageInfo($api, $package->Identifier);
                            }
                        }
                    } else {
                        if(!isset($old_data["ProductType"])) {
                            $product->remove($product->Identifier);
                            $product_id = "";
                            $product->Identifier = 0;
                            $page = "add";
                        } else {
                            $page = "add";
                            $product->resetProductType($product->Identifier);
                        }
                        $product->Success = $product->Warning = $product->Error = [];
                        $product->Error[] = __("couldnt create package from product");
                    }
                }
            }
            if(isset($old_data["ProductType"]) && $old_data["ProductType"] == "hosting" && $product->ProductType != "hosting") {
                require_once "class/package.php";
                $package = new package();
                $package->Product = $product->Identifier;
                if($package_id = $package->search()) {
                    $package->updateProductReference($package_id, $product->Identifier);
                }
            }
            if($page == "show") {
                flashMessage($product);
                header("Location: products.php?page=show&id=" . $product->Identifier);
                exit;
            }
            foreach ($product->Variables as $key) {
                if(is_string($product->{$key})) {
                    $product->{$key} = htmlspecialchars($product->{$key});
                }
            }
        }
        break;
    case "delete":
        $pagetype = "confirmDelete";
        if($product_id != NULL) {
            $page = "show";
        } else {
            $page = "overview";
        }
        if(empty($_POST) || !U_PRODUCT_DELETE) {
        } elseif(isset($_POST["imsure"]) && $_POST["imsure"] == "yes") {
            require_once "class/product.php";
            $product = new product();
            $product->Identifier = $product_id;
            $product->show();
            $result = $product->delete($product_id);
            if($result) {
                if(array_key_exists($product->ProductType, $additional_product_types)) {
                    $module_name = $product->ProductType;
                    $namespace = "modules\\products\\" . $module_name;
                    $classname = class_exists($namespace . "\\" . $module_name) ? $namespace . "\\" . $module_name : $module_name;
                    $module = new $classname();
                    $module->product_delete($product->Identifier);
                }
                $product_id = NULL;
                $page = "overview";
                if(!empty($_SESSION["ActionLog"]["Product"]["delete"])) {
                    array_shift($_SESSION["ActionLog"]["Product"]["delete"]);
                    if(!empty($_SESSION["ActionLog"]["Product"]["delete"])) {
                        header("Location: ?page=delete&id=" . current($_SESSION["ActionLog"]["Product"]["delete"]));
                        exit;
                    }
                }
            }
            unset($pagetype);
        }
        break;
    case "show":
        $pagetype = "add";
        if(isset($_POST["action"]) && $_POST["action"] && isset($_POST["periodic"]) && is_array($_POST["periodic"])) {
            switch ($_POST["action"]) {
                case "dialog:sync":
                    $successCounter = 0;
                    $list_periodics = is_array($_POST["periodic"]) ? $_POST["periodic"] : [];
                    require_once "class/product.php";
                    $product = new product();
                    $product->Identifier = $product_id;
                    $product->show();
                    require_once "class/periodic.php";
                    foreach ($list_periodics as $k => $subscriptionID) {
                        $subscription = new periodic();
                        $subscription->Identifier = $subscriptionID;
                        $subscription->show();
                        $subscription->PriceExcl = $product->PriceExcl;
                        $use_custom_prices = $product->HasCustomPrice == "period" ? true : false;
                        $subscription->checkPricePeriod($use_custom_prices);
                        $result = $subscription->updatePrice();
                        if($result === true) {
                            $successCounter++;
                        } else {
                            $product->Error = array_merge($product->Error, $subscription->Error);
                        }
                        unset($subscription);
                    }
                    if(0 < $successCounter) {
                        $product->Success[] = sprintf(__("multiple subscriptions pricesynced"), $successCounter);
                    }
                    break;
                case "dialog:removeConnectedPeriodic":
                    if(!U_INVOICE_EDIT) {
                    } else {
                        $list_periodics = is_array($_POST["periodic"]) ? $_POST["periodic"] : [];
                        require_once "class/product.php";
                        $product = new product();
                        $product->deconnectPeriodic($list_periodics);
                    }
                    break;
            }
        } elseif(isset($_POST["action"]) && $_POST["action"]) {
            require_once "class/product.php";
            $product = new product();
            $product->Error[] = __("no items selected");
        }
        break;
    case "add_group":
        if(!U_PRODUCT_EDIT) {
        } elseif(isset($_POST["GroupName"])) {
            require_once "class/group.php";
            $group = new group();
            $group->Type = "product";
            if(isset($product_id) && 0 < $product_id) {
                $group->Identifier = $product_id;
                $group->show();
                $group->Products = isset($_POST["Groups"]) && 1 < strlen($_POST["Groups"]) ? array_unique(explode(",", substr($_POST["Groups"], 1, -1))) : [];
            } else {
                $group->Products = isset($_POST["Groups"]) && 1 < strlen($_POST["Groups"]) ? array_unique(explode(",", substr($_POST["Groups"], 1, -1))) : [];
            }
            $group->GroupName = esc($_POST["GroupName"]);
            if(0 < $group->Identifier) {
                if($group->edit()) {
                    $page = "show_group";
                    $product_id = $group->Identifier;
                } else {
                    $group->GroupName = htmlspecialchars($group->GroupName);
                }
            } else {
                $group->Type = "product";
                if($group->add()) {
                    $page = "show_group";
                    $product_id = $group->Identifier;
                } else {
                    $group->GroupName = htmlspecialchars($group->GroupName);
                }
            }
        }
        break;
    case "delete_group":
        $page = "show_group";
        if(empty($_POST) || !U_PRODUCT_DELETE) {
        } elseif(isset($_POST["imsure"]) && $_POST["imsure"] == "yes") {
            require_once "class/group.php";
            $group = new group();
            $group->Type = "product";
            $group->Identifier = $product_id;
            if($group->delete()) {
                flashMessage($group);
                header("Location: products.php?page=groups");
                exit;
            }
        }
        break;
    default:
        $pagetype = "add";
        if(isset($_SESSION["ActionLog"]["Product"]["delete"]) && is_array($_SESSION["ActionLog"]["Product"]["delete"])) {
            unset($_SESSION["ActionLog"]["Product"]["delete"]);
        }
        switch ($page) {
            case "show":
                require_once "class/product.php";
                $product = isset($product) && is_object($product) ? $product : new product();
                $product->Identifier = $product_id;
                $product->show();
                $product->recalculate();
                $product->show();
                require_once "class/group.php";
                $group = new group();
                $fields = ["GroupName"];
                $groups = $group->all($fields);
                require_once "class/periodic.php";
                $periodic = new periodic();
                $session = isset($_SESSION["product.show.subscription"]) ? $_SESSION["product.show.subscription"] : [];
                $fields = ["Debtor", "CompanyName", "SurName", "Initials", "NextDate", "PriceExcl", "Number", "NumberSuffix", "TaxPercentage", "Periods", "StartPeriod", "EndPeriod", "Description", "Periodic"];
                $sort = isset($session["sort"]) ? $session["sort"] : "NextDate";
                $order = isset($session["order"]) ? $session["order"] : "ASC";
                $searchat = "ProductCode";
                $searchfor = htmlspecialchars_decode($product->ProductCode);
                $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) ? $session["limit"] : "1");
                $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : MAX_RESULTS_LIST);
                $periodics = $periodic->all($fields, $sort, $order, $limit, $searchat, $searchfor, "active", true, $show_results);
                if(isset($periodics["CountRows"]) && ($periodics["CountRows"] < $show_results * ($limit - 1) || 1 < $limit && $periodics["CountRows"] == $show_results * ($limit - 1))) {
                    $newPage = ceil($periodics["CountRows"] / $show_results);
                    if($newPage <= 0) {
                        $newPage = 1;
                    }
                    $limit = $newPage;
                    $periodics = $periodic->all($fields, $sort, $order, $limit, $searchat, $searchfor, "active", true, $show_results);
                }
                $_SESSION["product.show.subscription"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "searchat" => $searchat, "searchfor" => $searchfor, "limit" => $limit];
                $current_page = $limit;
                if($product->ProductType == "hosting") {
                    require_once "class/package.php";
                    $package = new package();
                    $package_id = $product->PackageID;
                    if(0 < $package_id) {
                        $package->show($package_id);
                        require_once "class/server.php";
                        $server = new server();
                        $server->show($package->Server);
                    }
                } elseif($product->ProductType == "domain") {
                    require_once "class/topleveldomain.php";
                    $tld = new topleveldomain();
                    $tld->showbyTLD($product->ProductTld);
                }
                $custom_prices = [];
                if($product->HasCustomPrice == "period") {
                    $custom_prices = $product->listCustomProductPrices();
                }
                $productStats = $product->getStatistics();
                $message = parse_message($product, $group, $periodic);
                $wfh_page_title = __("product") . " " . $product->ProductName;
                $current_page_url = "products.php?page=show&id=" . $product_id;
                $sidebar_template = "product.sidebar.php";
                require_once "views/product.show.php";
                break;
            case "add":
                checkRight(U_PRODUCT_EDIT);
                require_once "class/product.php";
                $product = isset($product) && is_object($product) ? $product : new product();
                $ProductCode = $product->newProductCode();
                if(isset($product_id) && 0 < $product_id) {
                    $product->Identifier = $product_id;
                    $product->show();
                    $product->ProductCode = htmlspecialchars_decode($product->ProductCode);
                    $pagetype = "edit";
                } else {
                    $product->ProductCode = $ProductCode;
                }
                require_once "class/group.php";
                $group = isset($group) && is_object($group) ? $group : new group();
                $group->Type = "product";
                $fields = ["GroupName", "Products"];
                $groups = $group->all($fields);
                require_once "class/periodic.php";
                $periodic = new periodic();
                $fields = ["Debtor", "CompanyName", "SurName", "Initials", "NextDate", "PriceExcl", "Number", "TaxPercentage", "Periods", "StartPeriod", "EndPeriod", "Description", "Periodic"];
                $periodics = $periodic->all($fields, "ProductCode", false, -1, "ProductCode", $product->ProductCode);
                require_once "class/topleveldomain.php";
                $topleveldomain = new topleveldomain();
                $list_domain_tlds = $topleveldomain->all(["Tld", "Registrar"]);
                $fields = ["ProductCode", "ProductName", "ProductType", "PriceExcl", "Sold", "PricePeriod"];
                $products = $product->all($fields, "ProductCode", "ASC", -1);
                require_once "class/registrar.php";
                $registrar = new registrar();
                $list_registrars = $registrar->all(["Name"]);
                require_once "class/server.php";
                $server = new server();
                $list_servers = $server->all(["Name"]);
                if(isset($product_id) && 0 < $product_id && $product->ProductType == "hosting") {
                    require_once "class/package.php";
                    $package = new package();
                    $package_id = $product->PackageID;
                    if(0 < $package_id) {
                        $package->show($package_id);
                        require_once "class/server.php";
                        $server = new server();
                        $server->show($package->Server);
                    }
                } elseif(isset($product_id) && 0 < $product_id && $product->ProductType == "domain") {
                    require_once "class/topleveldomain.php";
                    $tld = new topleveldomain();
                    $tld->showbyTLD($product->ProductTld);
                } elseif(isset($_GET["type"]) && $_GET["type"] == "domain") {
                    $product->ProductType = "domain";
                    if(isset($_GET["tld"]) && $_GET["tld"]) {
                        $product->ProductTld = htmlspecialchars(esc($_GET["tld"]));
                    }
                }
                $custom_prices = [];
                if($product->HasCustomPrice == "period") {
                    $custom_prices = $product->listCustomProductPrices();
                }
                $message = parse_message($product, $group, $periodic, $registrar, $server);
                $wfh_page_title = $pagetype == "edit" ? __("edit product") : __("add product");
                $current_page_url = "products.php";
                $sidebar_template = "product.sidebar.php";
                require_once "views/product.add.php";
                break;
            case "groups":
                require_once "class/group.php";
                $group = new group();
                $group->Type = "product";
                $session = isset($_SESSION["productgroup.overview"]) ? $_SESSION["productgroup.overview"] : [];
                $fields = ["GroupName", "Products"];
                $sort = isset($session["sort"]) ? $session["sort"] : "GroupName";
                $order = isset($session["order"]) ? $session["order"] : "ASC";
                $groups = $group->all($fields, $sort, $order);
                $_SESSION["productgroup.overview"] = ["sort" => $sort, "order" => $order];
                $message = parse_message($group);
                $wfh_page_title = __("productgroup overview");
                $current_page_url = "products.php?page=groups";
                $sidebar_template = "product.sidebar.php";
                require_once "views/productgroup.overview.php";
                break;
            case "add_group":
                checkRight(U_PRODUCT_EDIT);
                require_once "class/group.php";
                $group = isset($group) && is_object($group) ? $group : new group();
                $group->Type = "product";
                if(isset($product_id) && 0 < $product_id && empty($group->Error)) {
                    $group->Identifier = $product_id;
                    if(!$group->show()) {
                        flashMessage($group);
                        header("Location: products.php?page=groups");
                        exit;
                    }
                }
                require_once "class/product.php";
                $product = new product();
                $session = isset($_SESSION["productgroup.add"]) ? $_SESSION["productgroup.add"] : [];
                $fields = ["ProductCode", "ProductName", "PriceExcl", "PricePeriod"];
                $sort = isset($session["sort"]) ? $session["sort"] : "ProductCode";
                $order = isset($session["order"]) ? $session["order"] : "ASC";
                $searchat = isset($session["searchat"]) ? $session["searchat"] : "ProductCode|ProductName";
                $searchfor = isset($session["searchfor"]) ? $session["searchfor"] : "";
                $selectgroup = isset($session["group"]) ? $session["group"] : "";
                $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) ? $session["limit"] : "1");
                $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : min(25, MAX_RESULTS_LIST));
                $products = $product->all($fields, $sort, $order, $limit, $searchat, $searchfor, $selectgroup, $show_results);
                $_SESSION["productgroup.add"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "group" => $selectgroup, "searchat" => $searchat, "searchfor" => $searchfor, "limit" => $limit];
                $current_page = $limit;
                $message = parse_message($group, $product);
                if(0 < $group->Identifier) {
                    $current_page_url = "products.php?page=add_group&amp;id=" . $group->Identifier;
                } else {
                    $current_page_url = "products.php?page=add_group";
                }
                $wfh_page_title = 0 < $product_id ? __("edit productgroup") : __("add productgroup");
                $sidebar_template = "product.sidebar.php";
                require_once "views/productgroup.add.php";
                break;
            case "show_group":
                checkRight(U_PRODUCT_SHOW);
                require_once "class/group.php";
                $group = isset($group) && is_object($group) ? $group : new group();
                $group->Type = "product";
                $group->Identifier = $product_id;
                if(!$group->show()) {
                    flashMessage($group);
                    header("Location: products.php?page=groups");
                    exit;
                }
                require_once "class/product.php";
                $product = new product();
                $session = isset($_SESSION["productgroup.show"]) ? $_SESSION["productgroup.show"] : [];
                $fields = ["ProductCode", "ProductName", "PriceExcl", "PricePeriod"];
                $sort = isset($session["sort"]) ? $session["sort"] : "ProductCode";
                $order = isset($session["order"]) ? $session["order"] : "ASC";
                $searchat = isset($session["searchat"]) ? $session["searchat"] : "ProductCode|ProductName";
                $searchfor = isset($session["searchfor"]) ? $session["searchfor"] : "";
                $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) ? $session["limit"] : "1");
                $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : min(25, MAX_RESULTS_LIST));
                $products = $product->all($fields, $sort, $order, $limit, $searchat, $searchfor, $group->Identifier, $show_results);
                if(isset($products["CountRows"]) && ($products["CountRows"] < $show_results * ($limit - 1) || 1 < $limit && $products["CountRows"] == $show_results * ($limit - 1))) {
                    $newPage = ceil($products["CountRows"] / $show_results);
                    if($newPage <= 0) {
                        $newPage = 1;
                    }
                    $limit = $newPage;
                    $products = $product->all($fields, $sort, $order, $limit, $searchat, $searchfor, $group->Identifier, $show_results);
                }
                $_SESSION["productgroup.show"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "searchat" => $searchat, "searchfor" => $searchfor, "limit" => $limit];
                $current_page = $limit;
                $message = parse_message($group, $product);
                $wfh_page_title = __("productgroup") . " " . $group->GroupName;
                $current_page_url = "products.php?page=show_group&id=" . $group->Identifier;
                $sidebar_template = "product.sidebar.php";
                require_once "views/productgroup.show.php";
                break;
            default:
                require_once "class/product.php";
                $product = isset($product) && is_object($product) ? $product : new product();
                $session = isset($_SESSION["product.overview"]) ? $_SESSION["product.overview"] : [];
                $fields = ["ProductCode", "ProductName", "ProductType", "PriceExcl", "Sold", "PricePeriod", "Groups"];
                $sort = isset($session["sort"]) ? $session["sort"] : "ProductCode";
                $order = isset($session["order"]) ? $session["order"] : "ASC";
                $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) ? $session["limit"] : "1");
                $searchat = isset($session["searchat"]) ? $session["searchat"] : "ProductCode|ProductName|ProductKeyPhrase";
                $searchfor = isset($session["searchfor"]) ? $session["searchfor"] : "";
                $selectgroup = isset($session["group"]) ? $session["group"] : "";
                $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : MAX_RESULTS_LIST);
                $products = $product->all($fields, $sort, $order, $limit, $searchat, $searchfor, $selectgroup, $show_results);
                if(isset($products["CountRows"]) && ($products["CountRows"] < $show_results * ($limit - 1) || 1 < $limit && $products["CountRows"] == $show_results * ($limit - 1))) {
                    $newPage = ceil($products["CountRows"] / $show_results);
                    if($newPage <= 0) {
                        $newPage = 1;
                    }
                    $_SESSION["product.overview"]["limit"] = $newPage;
                    header("Location: products.php");
                    exit;
                }
                $_SESSION["product.overview"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "group" => $selectgroup, "searchat" => $searchat, "searchfor" => $searchfor, "limit" => $limit];
                $current_page = $limit;
                require_once "class/group.php";
                $group = new group();
                $group->Type = "product";
                $fields = ["GroupName", "Products"];
                $groups = $group->all($fields);
                $message = parse_message($product, $group);
                $wfh_page_title = __("product overview");
                $current_page_url = "products.php";
                $sidebar_template = "product.sidebar.php";
                require_once "views/product.overview.php";
        }
}

?>