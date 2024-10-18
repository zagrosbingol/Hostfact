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
$discount_id = isset($_GET["id"]) ? intval(esc($_GET["id"])) : (isset($_POST["id"]) ? intval(esc($_POST["id"])) : NULL);
if($page == "edit") {
    $page = "add";
}
switch ($page) {
    case "add":
        if(!isset($_POST) || empty($_POST) || !U_PRODUCT_EDIT) {
        } else {
            require_once "class/discount.php";
            $discount = new discount();
            if(isset($_POST["Identifier"]) && 0 < $_POST["Identifier"]) {
                $discount->Identifier = esc($_POST["Identifier"]);
                $discount->show();
            }
            foreach ($_POST as $key => $value) {
                $discount->{$key} = esc($value);
            }
            if(in_array($discount->DiscountType, ["TotalPercentage", "TotalAmount", "PartialAmount"])) {
                $discount->MaxPerInvoice = "";
            }
            if(0 < $discount->Identifier) {
                $result = $discount->edit();
                $discount_id = $discount->Identifier;
            } else {
                $result = $discount->add();
                $discount_id = $discount->Identifier;
            }
            if($result) {
                flashMessage($discount);
                header("Location: discount.php?page=show&id=" . $discount->Identifier);
                exit;
            }
            foreach ($_POST as $post_key => $post_value) {
                if(in_array($post_key, $discount->Variables) && is_string($post_value)) {
                    $discount->{$post_key} = htmlspecialchars(esc($post_value));
                }
            }
        }
        break;
    case "delete":
        $pagetype = "confirmDelete";
        if($discount_id != NULL) {
            $page = "show";
        } else {
            $page = "overview";
        }
        if(empty($_POST) || !U_PRODUCT_DELETE) {
        } else {
            if(!empty($_POST) && isset($_POST["agree_delete_discount"]) && $_POST["agree_delete_discount"] == "yes" && isset($_POST["id"]) && 0 < $_POST["id"]) {
                require_once "class/discount.php";
                $discount = new discount();
                $discount->Identifier = intval(esc($_POST["id"]));
                if($discount->show() && $discount->delete()) {
                    flashMessage($discount);
                    header("Location: discount.php");
                    exit;
                }
                flashMessage($discount);
                header("Location: discount.php?page=show&id=" . $this->discount->Identifier);
                exit;
            }
            header("Location: discount.php");
            exit;
        }
        break;
    case "add":
        checkRight(U_PRODUCT_EDIT);
        require_once "class/discount.php";
        $discount = isset($discount) && is_object($discount) ? $discount : new discount();
        if(0 < $discount_id) {
            $discount->Identifier = $discount_id;
            $discount->show();
            $pagetype = "edit";
        } else {
            $pagetype = "add";
        }
        if(!is_array($discount->DebtorGroup)) {
            $discount->DebtorGroup = explode(",", $discount->DebtorGroup);
        }
        require_once "class/product.php";
        $product = new product();
        $products = $product->all(["ProductCode", "ProductName", "PriceExcl"]);
        require_once "class/group.php";
        $group = new group("product");
        $productgroups = $group->all(["GroupName"]);
        require_once "class/debtor.php";
        $debtor = new debtor();
        $debtors = $debtor->all(["DebtorCode", "CompanyName", "SurName", "Initials"]);
        $group = new group("debtor");
        $debtorgroups = $group->all(["GroupName"]);
        $message = parse_message($discount);
        $wfh_page_title = $pagetype == "add" ? __("discount create") : __("discount edit");
        $sidebar_template = "product.sidebar.php";
        require_once "views/discount.add.php";
        break;
    case "show":
        require_once "class/discount.php";
        $discount = isset($discount) && is_object($discount) ? $discount : new discount();
        $discount->Identifier = $discount_id;
        $discount->show();
        require_once "class/product.php";
        $product = new product();
        $products = $product->all(["ProductCode", "ProductName", "PriceExcl"]);
        require_once "class/group.php";
        $group = new group("product");
        $productgroups = $group->all(["GroupName"]);
        require_once "class/debtor.php";
        $debtor = new debtor();
        $debtors = $debtor->all(["DebtorCode", "CompanyName", "SurName", "Initials"]);
        $group = new group("debtor");
        $debtorgroups = $group->all(["GroupName"]);
        $message = parse_message($discount, $product, $debtor, $group);
        $wfh_page_title = __("discount") . " " . $discount->Name;
        $sidebar_template = "product.sidebar.php";
        require_once "views/discount.show.php";
        break;
    default:
        require_once "class/discount.php";
        $discount = isset($discount) && is_object($discount) ? $discount : new discount();
        $session = isset($_SESSION["discount.overview"]) ? $_SESSION["discount.overview"] : [];
        $fields = ["Name", "StartDate", "EndDate", "Counter", "Coupon"];
        $sort = isset($session["sort"]) ? $session["sort"] : "Name";
        $order = isset($session["order"]) ? $session["order"] : "ASC";
        $searchat = isset($session["searchat"]) ? $session["searchat"] : "";
        $searchfor = isset($session["searchfor"]) ? $session["searchfor"] : "";
        $selectgroup = isset($session["status"]) ? $session["status"] : "-1|1|3|4|5|6|7|8";
        $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) ? $session["limit"] : "1");
        $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : (isset($session["results"]) ? $session["results"] : MAX_RESULTS_LIST);
        $discount_list = $discount->all($fields, $sort, $order, $limit, $searchat, $searchfor, false, $show_results);
        if(isset($discount_list["CountRows"]) && ($discount_list["CountRows"] < $show_results * ($limit - 1) || 1 < $limit && $discount_list["CountRows"] == $show_results * ($limit - 1))) {
            $newPage = ceil($discount_list["CountRows"] / $show_results);
            if($newPage <= 0) {
                $newPage = 1;
            }
            $_SESSION["discount.overview"]["limit"] = $newPage;
            header("Location: discount.php");
            exit;
        }
        $_SESSION["discount.overview"] = ["sort" => $sort, "order" => $order, "results" => $show_results, "status" => $selectgroup, "searchat" => $searchat, "searchfor" => $searchfor, "limit" => $limit];
        $current_page = $limit;
        $message = parse_message($discount);
        $wfh_page_title = __("discount module");
        $current_page_url = "discount.php";
        $sidebar_template = "product.sidebar.php";
        require_once "views/discount.overview.php";
}

?>