<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "class/upgradegroup.php";
require_once "class/product.php";
global $array_periodic;
$upgradegroup = new UpgradeGroup_Model("hosting");
$products_in_groups = $upgradegroup->getProductsInUpgradegroups();
$products_in_groups = explode(",", $products_in_groups["Products"]);
foreach ($products_in_groups as $key => $value) {
    $products_in_groups_json[$value] = $value;
}
$product = new product();
$list_products = $product->all(["id", "ProductCode", "ProductName", "PriceExcl", "ProductType", "Groups", "PricePeriod"], "ProductCode", false, "-1", "ProductType", "hosting");
$list_product_groups = [];
foreach ($list_products as $key => $product) {
    if(is_numeric($key)) {
        $product_group_ids = "";
        if(!empty($product["Groups"])) {
            foreach ($product["Groups"] as $group) {
                $product_group_ids .= $product_group_ids == "" ? $group["id"] : "," . $group["id"];
                if(!isset($list_product_groups[$group["id"]])) {
                    $list_product_groups[$group["id"]]["GroupName"] = $group["GroupName"];
                }
                if(isset($list_product_groups[$group["id"]]["Products"])) {
                    $list_product_groups[$group["id"]]["Products"] .= "," . $product["id"];
                } else {
                    $list_product_groups[$group["id"]]["Products"] = $product["id"];
                }
            }
        }
        $list_products[$key]["GroupIDs"] = $product_group_ids;
    }
}
echo "\n<br clear=\"both\">\n                \n<div class=\"left\">\n\n    <div class=\"box3\">\n        <h3>";
echo __("hosting upgradegroups settings");
echo "</h3>\n        <div class=\"content\">\n\n            <strong class=\"title\">";
echo __("financial process");
echo "</strong>\n            <label>\n                <input type=\"radio\" name=\"HOSTING_UPGRADE_FINANCIAL_PROCESSING\" value=\"existing_period\" ";
if(HOSTING_UPGRADE_FINANCIAL_PROCESSING == "existing_period") {
    echo "checked=\"checked\"";
}
echo " />\n                ";
echo ucfirst(__("updowngrade leave period equal"));
echo "            </label><br />\n            <label>\n                <input type=\"radio\" name=\"HOSTING_UPGRADE_FINANCIAL_PROCESSING\" value=\"new_period\" ";
if(HOSTING_UPGRADE_FINANCIAL_PROCESSING == "new_period") {
    echo "checked=\"checked\"";
}
echo " />\n                ";
echo ucfirst(__("updowngrade new period"));
echo "            </label><br /><br />\n            \n            <strong class=\"title\">";
echo __("updowngrade create invoice");
echo "</strong>\n            <label>\n                <input type=\"radio\" name=\"HOSTING_UPGRADE_CREATE_INVOICE\" value=\"always\" ";
if(HOSTING_UPGRADE_CREATE_INVOICE == "always") {
    echo "checked=\"checked\"";
}
echo " />\n                ";
echo ucfirst(__("updowngrade create invoice always"));
echo "            </label><br />\n            <label>\n                <input type=\"radio\" name=\"HOSTING_UPGRADE_CREATE_INVOICE\" value=\"only_positive\" ";
if(HOSTING_UPGRADE_CREATE_INVOICE == "only_positive") {
    echo "checked=\"checked\"";
}
echo " />\n                ";
echo __("create upgrade invoice only positive");
echo "            </label><br />\n\t\t\t<label>\n                <input type=\"radio\" name=\"HOSTING_UPGRADE_CREATE_INVOICE\" value=\"never\" ";
if(HOSTING_UPGRADE_CREATE_INVOICE == "never") {
    echo "checked=\"checked\"";
}
echo " />\n                ";
echo ucfirst(__("updowngrade create invoice never"));
echo "            </label><br />\n            <br />\n            \n            <strong class=\"title\">";
echo __("prefix hosting upgrade invoice upgrade");
echo "</strong>\n            <input type=\"text\" class=\"text1 size11_percentage\" name=\"HOSTING_UPGRADE_PREFIX_UPGRADE\" value=\"";
echo HOSTING_UPGRADE_PREFIX_UPGRADE;
echo "\" />\n            <br /><br />\n            \n            <strong class=\"title\">";
echo __("prefix hosting upgrade invoice refund");
echo "</strong>\n            <input type=\"text\" class=\"text1 size11_percentage\" name=\"HOSTING_UPGRADE_PREFIX_REFUND\" value=\"";
echo HOSTING_UPGRADE_PREFIX_REFUND;
echo "\" />\n            <br />\n            \n          </div>\n    </div>  \n    <div class=\"box3\">\n\t\t<h3>";
echo __("hosting upgradegroups");
echo "</h3>\n\t\t<div class=\"content\">\n        \n            <div id=\"settings_hosting_upgradegroups\" style=\"padding: 5px 0;\">\n                <input type=\"hidden\" name=\"products_in_groups\" value='";
echo json_encode($products_in_groups_json);
echo "' />\n                <input type=\"hidden\" name=\"delete_groups\" value='' />\n                \n                ";
if(!empty($upgradegroups_list)) {
    foreach ($upgradegroups_list as $upgradegroup) {
        echo "                            <div id=\"upgradegroup_";
        echo $upgradegroup["id"];
        echo "\" data-id=\"";
        echo $upgradegroup["id"];
        echo "\">\n                            \n                                <input type=\"hidden\" name=\"upgradegroup_name[]\" value=\"";
        echo htmlspecialchars($upgradegroup["Name"]);
        echo "\" />\n                                <input type=\"hidden\" name=\"upgradegroup_id[]\" value=\"";
        echo $upgradegroup["id"];
        echo "\" />\n                                <input type=\"hidden\" name=\"upgradegroup_products[]\" value=\"";
        echo $upgradegroup["Products"];
        echo "\" />\n                                \n                                <u>";
        echo htmlspecialchars($upgradegroup["Name"]);
        echo "</u>\n                                <a class=\"a1 c1 float_right btn_edit_upgradegroup\">\n                                    ";
        echo __("edit");
        echo "                                </a><br />\n                                <div class=\"upgradegroup_products\">\n                                    ";
        $productnames = "";
        if(is_array($upgradegroup["ProductsInfo"])) {
            foreach ($upgradegroup["ProductsInfo"] as $product) {
                $productnames .= $productnames == "" ? $product["ProductName"] : ",&nbsp;&nbsp;" . $product["ProductName"];
            }
        }
        echo $productnames;
        echo "                                </div>\n                                <br />\n                            </div>\n                            ";
    }
}
if($list_products && 0 < $list_products["CountRows"]) {
    echo "<div class=\"no-groups-yet" . (!empty($upgradegroups_list) ? " hide" : "") . "\">" . __("no upgrade groups created yet") . "<br /><br /></div>";
}
echo "            </div>\n\n            \n            ";
if(!$list_products || (int) $list_products["CountRows"] === 0) {
    $products_page_link = "<a href=\"products.php\" class=\"a1 c1\">" . __("products page") . "</a>";
    echo sprintf(__("no products in software to add to upgrade groups"), $products_page_link);
} else {
    echo " \n                    <a id=\"btn_add_upgradegroup\" class=\"a1 c1 pointer\">\n                        <span>\n                            <img src=\"images/ico_add.png\" style=\"float: left; margin-right: 5px;\" /> \n                            ";
    echo ucfirst(__("add upgradegroup"));
    echo "                        </span>\n                    </a>\n                    ";
}
echo "\n\t   </div>\n    </div>\n    \n</div>\n\n<div class=\"right\">\n\t<div class=\"setting_help_box\">\n\t\t<strong>";
echo __("hosting upgradegroups explained title");
echo "</strong><br />\n\t\t";
echo __("hosting upgradegroups explained");
echo "\t</div>\n</div>\n\n\n\n<div class=\"hide\" id=\"dialog_upgradegroup\" title=\"";
echo __("hosting upgradegroup");
echo "\">\n\n    <form name=\"upgradegroup_addedit\" id=\"upgradegroup_addedit\" action=\"settings.php?page=services#tab-hosting\" method=\"post\">\n        \n        <div>\n            <strong>";
echo __("upgradegroup name");
echo ": </strong><br />\n            <input type=\"text\" name=\"upgradegroup_addedit_name\"  class=\"text1 size12\" value=\"";
echo isset($upgradegroup->Name) ? $upgradegroup->Name : "";
echo "\" /><br /><br />\n        \n            <strong>";
echo __("add product");
echo ": </strong><br />\n            \n            <select id=\"productsgroups_to_add\" name=\"productgroup_add\" class=\"text1 size1\">\n                <option value=\"\">\n                    ";
echo __("all product groups");
echo "                </option>\n                ";
if(!empty($list_product_groups)) {
    echo "                        \n                        ";
    foreach ($list_product_groups as $product_group) {
        echo "                            <option value=\"";
        echo $product_group["Products"];
        echo "\">\n                                ";
        echo $product_group["GroupName"];
        echo "                            </option>\n                            ";
    }
}
echo "            </select>                        \n            &nbsp;&nbsp;\n            <select id=\"all_products_clone_me\" class=\"text1 hide size4f\">\n            ";
if($list_products && 0 < $list_products["CountRows"]) {
    echo "                    <option value=\"\">\n                        ";
    echo __("all available hostingproducts");
    echo "                    </option>\n                    ";
    foreach ($list_products as $key => $product_to_add) {
        if(is_numeric($key)) {
            echo "                            <option value=\"";
            echo $product_to_add["id"];
            echo "\">\n                                ";
            echo $product_to_add["ProductCode"] . " - " . $product_to_add["ProductName"];
            echo "                            </option>\n                            ";
        }
    }
}
echo "            </select>\n            &nbsp;&nbsp;         \n            <a id=\"add_product_to_upgradegroup\" class=\"a1 c1\">";
echo __("add to list");
echo " (<span class=\"products_count\"></span>)</a><br /><br />\n        </div>\n        \n        <div>\n            <strong>";
echo __("products in upgradegroup");
echo ":</strong>\n            <table id=\"upgradegroup_products\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"table1 zebra_table\" style=\"width: 100%; margin: 3px 0 0 0;\">\n        \t\t<thead>\n        \t\t\t<tr>\n                        <th width=\"10\">&nbsp;</th>\n        \t\t\t\t<th width=\"70\">";
echo __("product no");
echo "</th>\n                        <th>";
echo __("productname");
echo "</th>\n                        <th colspan=\"3\" width=\"";
echo VAT_CALC_METHOD == "incl" ? "85" : "140";
echo "\">\n                            ";
echo __("price excl");
echo "                        </th>\n        \t\t\t\t<th colspan=\"3\" width=\"";
echo VAT_CALC_METHOD == "excl" ? "85" : "140";
echo "\">\n                            ";
echo __("price incl");
echo "                        </th>\n                        <th width=\"16\">&nbsp;</th>\n        \t\t\t</tr>\n        \t\t</thead>\n        \t\t<tbody>\n                ";
if($list_products && 0 < $list_products["CountRows"]) {
    foreach ($list_products as $key => $upgradegroup_product) {
        if(!is_numeric($key)) {
        } else {
            echo "                            <tr class=\"hide upgradegroup_product upgradegroup_product_";
            echo $upgradegroup_product["id"];
            echo "\">\n                                <td valign=\"top\">\n                                    <img class=\"pointer sortablehandle sort_icon\" src=\"images/ico_sort.png\" style=\"margin-top:5px;\" />\n                                </td>\n                \t\t\t\t<td valign=\"top\">\n                                    <input type=\"hidden\" name=\"product_id\" value=\"";
            echo $upgradegroup_product["id"];
            echo "\" />\n                                    ";
            echo $upgradegroup_product["ProductCode"];
            echo " \n                                </td>\n                \t\t\t\t<td valign=\"top\">\n                                    <input type=\"hidden\" name=\"product_name\" value=\"";
            echo $upgradegroup_product["ProductName"];
            echo "\" />\n                                    ";
            echo $upgradegroup_product["ProductName"];
            echo "    \n                                </td>\n                                \n                                <td valign=\"top\" style=\"width: 5px;\" class=\"currency_sign_left\">\n                                    ";
            echo currency_sign_td(CURRENCY_SIGN_LEFT);
            echo "                                </td>\n                \t\t\t\t<td valign=\"top\" style=\"width: 50px; padding-left: 0px;\" align=\"right\">\n                                    ";
            echo money($upgradegroup_product["PriceExcl"], false);
            echo "                                </td>\n                \t\t\t\t<td valign=\"top\" class=\"currency_sign_right\" style=\"width: ";
            echo VAT_CALC_METHOD == "incl" ? "20" : "75";
            echo "px;\">\n                                    ";
            if(CURRENCY_SIGN_RIGHT) {
                echo CURRENCY_SIGN_RIGHT . " ";
            }
            if(isset($upgradegroup_product["PricePeriod"]) && VAT_CALC_METHOD == "excl") {
                echo __("per") . " " . $array_periodic[$upgradegroup_product["PricePeriod"]];
            } else {
                echo "&nbsp;";
            }
            echo "                                </td>\n                \t\t\t\t\n                \t\t\t\t<td valign=\"top\" class=\"currency_sign_left\" style=\"width: 5px;\">\n                                    ";
            echo currency_sign_td(CURRENCY_SIGN_LEFT);
            echo "                                </td>\n                \t\t\t\t<td valign=\"top\" style=\"width: 50px; padding-left: 0px;\" align=\"right\">\n                                    ";
            echo money($upgradegroup_product["PriceIncl"], false);
            echo "                                </td>\n                \t\t\t\t<td valign=\"top\" class=\"currency_sign_right\" style=\"width: ";
            echo VAT_CALC_METHOD == "excl" ? "20" : "75";
            echo "px;\">\n                                    ";
            if(CURRENCY_SIGN_RIGHT) {
                echo CURRENCY_SIGN_RIGHT . " ";
            }
            if(isset($upgradegroup_product["PricePeriod"]) && VAT_CALC_METHOD == "incl") {
                echo __("per") . " " . $array_periodic[$upgradegroup_product["PricePeriod"]];
            } else {
                echo "&nbsp;";
            }
            echo "                                </td>\n                                \n                                <td>\n                                    <a class=\"del_row hide\" data-id=\"";
            echo $upgradegroup_product["id"];
            echo "\">\n                                        <img src=\"images/ico_trash.png\" class=\"remove_icon pointer\">\n                                    </a>\n                                </td>\n                \t\t\t</tr>\n                            ";
        }
    }
}
echo "                    <tr class=\"no-products-added hide\">\n    \t\t\t\t\t<td colspan=\"10\">\n                            ";
echo __("no products added");
echo "    \n                        </td>\n    \t\t\t\t</tr>\n                    \n                </tbody>\n        \t</table>\n            \n        </div>\n        <br />\n    \n    </form>\n    \n    <p style=\"clear: both;padding-top:10px;\">\n        <a id=\"save_upgradegroup\" class=\"button1 alt1\">\n            <span>";
echo __("proceed");
echo "</span>\n        </a>\n        \n        <span id=\"delete_upgradegroup_text\">\n            &nbsp;";
echo __("or");
echo "&nbsp; \n            <a id=\"delete_upgradegroup\" class=\"a1 c1\">\n                <span>";
echo strtolower(__("delete"));
echo "</span>\n            </a>\n        </span>\n    \t<a class=\"a1 c1 float_right\" onclick=\"\$('#dialog_upgradegroup').dialog('close');\">\n            <span>";
echo __("cancel");
echo " </span>\n        </a>\n    </p>\n    \n</div>\n\n<script type=\"text/javascript\">\n\n\$(function()\n{\n    var new_upgradegroup_id = 9999999;\n    var action = '';\n    var edit_group_id = 0;\n    /*\n    *  Dialog upgrade group\n    */\n    \$('#dialog_upgradegroup').dialog({\n        modal: true, \n        autoOpen: false, \n        resizable: false, \n        width: 800, \n        height: 'auto',\n        close: function(event, ui) \n        {\n            updateProductsInGroups();\n        }\n    });\n    \n    // add a upgrade group\n    \$('#btn_add_upgradegroup').click( function()\n    {\n        action = 'add';\n        // resets\n        \$('#dialog_upgradegroup input[name=\"upgradegroup_addedit_name\"]').attr('value', '');\n        \$('#dialog_upgradegroup input[name=\"product_add\"]').val('');\n        \$('#dialog_upgradegroup input[name=\"productgroup_add\"]').val('');\n        \$('table#upgradegroup_products tr.upgradegroup_product').hide();\n        \n        \$('#delete_upgradegroup_text').hide();\n        \n        \$('#dialog_upgradegroup').dialog('open');\n        \n        refreshAddableProductsList();        \n    });\n    \n    // edit a upgrade group\n    \$(document).on('click', '#settings_hosting_upgradegroups a.btn_edit_upgradegroup', function ()\n    {\n        var upgradegroup_id         = \$(this).siblings('input[name=\"upgradegroup_id[]\"]').attr('value');\n        var upgradegroup_name       = \$(this).siblings('input[name=\"upgradegroup_name[]\"]').attr('value');\n        var upgradegroup_products   = \$(this).siblings('input[name=\"upgradegroup_products[]\"]').attr('value').split(',');\n        \n        action          = 'edit';   \n        edit_group_id   = upgradegroup_id;\n        \n        // resets\n        \$('#dialog_upgradegroup input[name=\"product_add\"]').val('');\n        \$('#dialog_upgradegroup input[name=\"productgroup_add\"]').val('');\n        \$('table#upgradegroup_products tr.upgradegroup_product').hide();\n        \n        \$('#delete_upgradegroup_text').show();\n        \n        // show the correct products\n        for(var n=0; n < upgradegroup_products.length; n++)\n        {\n            \$('table#upgradegroup_products tr.upgradegroup_product_' + upgradegroup_products[n]).show();\n            // set correct order\n            \$('table#upgradegroup_products tr.upgradegroup_product_' + upgradegroup_products[n]).appendTo('#dialog_upgradegroup tbody');\n        }\n        \n        // fill fields with data\n        \$('#dialog_upgradegroup input[name=\"upgradegroup_addedit_name\"]').attr('value', upgradegroup_name);\n        \n        \$('#dialog_upgradegroup').dialog('open');\n        \n        setTableZebra();\n        refreshAddableProductsList();\n    });\n\n    /* \n    *  Table products in dialog\n    */\n    \$(\".zebra_table tbody tr\").removeClass(\"tr1\");\n    \$(\".zebra_table tbody tr:odd\").addClass(\"tr1\");\n        \n    // Sortable\n\t\$('table#upgradegroup_products tbody').sortable({\n\t\thandle: \".sortablehandle\",\n        placeholder: \"InvoiceElementContainer\",\n\t\taxis: \"y\",\n\t\tforceHelperSize: true,\n\t\tforcePlaceholderSize: true,\n\t\tcontainment: 'parent',\n\t\ttolerance: 'pointer',\n\t\tstop: function(event, ui)\n        { \n\t\t\t// Zebra-effect\n\t\t\tsetTableZebra();\n        }\n\t});\n    \n    \$('#add_product_to_upgradegroup').click(function ()\n    {\n        var product_to_add_id = \$('select#products_to_add').val();\n\n        if(product_to_add_id != undefined && product_to_add_id != '')\n        {\n            // add product id to products_in_groups\n            var products_object = JSON.parse(\$('#settings_hosting_upgradegroups input[name=\"products_in_groups\"]').val());\n            products_object[product_to_add_id] = product_to_add_id;\n            \$('#settings_hosting_upgradegroups input[name=\"products_in_groups\"]').val(JSON.stringify(products_object));\n            \n            \$('table#upgradegroup_products tr.upgradegroup_product_' + product_to_add_id).show();\n            // move to the end\n            \$('table#upgradegroup_products tr.upgradegroup_product_' + product_to_add_id).appendTo('#dialog_upgradegroup tbody');\n            \n            setTableZebra();\n            refreshAddableProductsList();            \n        }\n        // add all products\n        else\n        {\n            var products_object = JSON.parse(\$('#settings_hosting_upgradegroups input[name=\"products_in_groups\"]').val());\n            \n            \$('select#products_to_add option[value!=\"\"]').each( function()\n            {\n                products_object[\$(this).val()] = \$(this).val();\n                \$('table#upgradegroup_products tr.upgradegroup_product_' + \$(this).val()).show();\n                // move to the end\n                \$('table#upgradegroup_products tr.upgradegroup_product_' + \$(this).val()).appendTo('#dialog_upgradegroup tbody');\n            });\n            \n            // add product id to products_in_groups\n            \$('#settings_hosting_upgradegroups input[name=\"products_in_groups\"]').val(JSON.stringify(products_object));\n            \n            setTableZebra();\n            refreshAddableProductsList(); \n        }\n    });\n    \n    \$('#delete_upgradegroup').click(function()\n    {\n        if(action == 'edit')\n        {\n            // get products of this group and remove them from the products_in_groups list\n            var products_delete = \$('#upgradegroup_' + edit_group_id).children('input[name=\"upgradegroup_products[]\"]').val().split(',');\n            \n            var products_object = JSON.parse(\$('#settings_hosting_upgradegroups input[name=\"products_in_groups\"]').val());\n            \n            \$.each(products_object, function(key, val)\n            {\n                if(\$.inArray(key, products_delete) != '-1')\n                {\n                    delete products_object[key];\n                }\n            });\n            \$('#settings_hosting_upgradegroups input[name=\"products_in_groups\"]').val(JSON.stringify(products_object));\n            \n            \$('#upgradegroup_' + edit_group_id).remove();\n            \n            // update input field which posts the groups to delete\n            if(\$('input[name=\"delete_groups\"]').val() == '')\n            {\n                \$('input[name=\"delete_groups\"]').val(edit_group_id);    \n            }\n            else\n            {\n                \$('input[name=\"delete_groups\"]').val(\$('input[name=\"delete_groups\"]').val() + ',' + edit_group_id);   \n            }\n        }\n        \$('#dialog_upgradegroup').dialog('close');\n    });\n    \n    // confirm of the add/edit of a upgradegroup\n    \$('#save_upgradegroup').click( function()\n    {\n        var upgradegroup_name = \$('#dialog_upgradegroup input[name=\"upgradegroup_addedit_name\"]').val();\n        var list_products_ids   = '';\n        var list_products_names = '';\n        \$('#dialog_upgradegroup tr.upgradegroup_product:visible').each( function()\n        {\n            list_products_names += (list_products_names == '') ? \$(this).children('td').children('input[name=\"product_name\"]').val() : ',&nbsp;&nbsp;' + \$(this).children('td').children('input[name=\"product_name\"]').val();\n            list_products_ids += ',' + \$(this).children('td').children('input[name=\"product_id\"]').val(); \n        });\n        list_products_ids   = list_products_ids.substring(1);\n\n        if(upgradegroup_name == '' || list_products_ids.length == 0 || list_products_names.length == 0)\n        {\n            \$('#dialog_upgradegroup').dialog('close');\n            return false;            \n                    \n        }                        \n            \n        if(action == 'add')\n        {            \n            \$('#settings_hosting_upgradegroups').append('<div id=\"upgradegroup_' + new_upgradegroup_id + '\" class=\"new_upgradegroup\"></div>');\n            \$('#upgradegroup_' + new_upgradegroup_id).append('<input type=\"hidden\" name=\"upgradegroup_name[]\" value=\"' + htmlspecialchars(upgradegroup_name) + '\" />');\n            \$('#upgradegroup_' + new_upgradegroup_id).append('<input type=\"hidden\" name=\"upgradegroup_id[]\" value=\"' + new_upgradegroup_id + '\" />');\n            \$('#upgradegroup_' + new_upgradegroup_id).append('<input type=\"hidden\" name=\"upgradegroup_products[]\" value=\"' + list_products_ids + '\" />');\n            \n            \$('#upgradegroup_' + new_upgradegroup_id).append('<u>' + htmlspecialchars(upgradegroup_name) + '</u>');\n            \$('#upgradegroup_' + new_upgradegroup_id).append('<a class=\"a1 c1 float_right btn_edit_upgradegroup\">";
echo __("edit");
echo "</a><br />');\n            \$('#upgradegroup_' + new_upgradegroup_id).append('<div class=\"upgradegroup_products\">' + list_products_names + '</div><br />');\n            \n            new_upgradegroup_id++;\n        }\n        else if(action == 'edit')\n        {   \n            \$('#upgradegroup_' + edit_group_id).children('input[name=\"upgradegroup_name[]\"]').attr('value', htmlspecialchars(upgradegroup_name));\n            \$('#upgradegroup_' + edit_group_id).children('input[name=\"upgradegroup_products[]\"]').attr('value', list_products_ids);\n            \$('#upgradegroup_' + edit_group_id).children('u').html(htmlspecialchars(upgradegroup_name));\n            \$('#upgradegroup_' + edit_group_id).children('.upgradegroup_products').html(list_products_names);\n        }\n        \n        \$('#dialog_upgradegroup').dialog('close');\n    });\n\n    \$(document).on('click', 'table#upgradegroup_products a.del_row', function()\n    {\n        var product_to_remove_id = \$(this).attr('data-id');\n        \n        // remove product id from products_in_groups\n        var products_object = JSON.parse(\$('#settings_hosting_upgradegroups input[name=\"products_in_groups\"]').val());\n        \$.each(products_object, function(key, val)\n        {\n            if(key == product_to_remove_id)\n            {\n                delete products_object[key];\n            }\n        });\n        \$('#settings_hosting_upgradegroups input[name=\"products_in_groups\"]').val(JSON.stringify(products_object));\n        \n        \$(this).parent('td').parent('tr').hide();\n                                \n        setTableZebra();\n        refreshAddableProductsList();\n    });\n    \n    // filter by product group\n    \$(document).on('change', 'select#productsgroups_to_add', function()\n    {\n        refreshAddableProductsList();\n    });\n\n    \$(document).on('mouseenter', 'table#upgradegroup_products tr.upgradegroup_product:visible', function () {\n        \$(this).children('td').children('a.del_row').removeClass('hide');\n    });\n    \$(document).on('mouseleave', 'table#upgradegroup_products tr.upgradegroup_product:visible', function () {\n        \$(this).children('td').children('a.del_row').addClass('hide');\n\n    });\n    \n    // update counter of addable products\n    \$(document).on('change', 'select#products_to_add', function()\n    {\n        if(\$(this).val() == '')\n        {\n            \$('a#add_product_to_upgradegroup span.products_count').text(\$('select#products_to_add option').length - 1);\n        }\n        else\n        {\n            \$('a#add_product_to_upgradegroup span.products_count').text(1);\n        }\n    });\n    \n    function refreshAddableProductsList()\n    {\n        // first remove active select\n        \$('select#products_to_add').remove();\n        \n        // clone select with all products and set correct values\n        var products_select = \$('select#all_products_clone_me').clone();\n        products_select.attr('id', 'products_to_add');\n        products_select.attr('name', 'product_add');\n        products_select.removeClass('hide');\n        \n        // filter out products that are already in a upgradegroup\n        var products_object = JSON.parse(\$('#settings_hosting_upgradegroups input[name=\"products_in_groups\"]').val());\n        \$.each(products_object, function(key, val)\n        {\n            if(key != '')\n            {\n                products_select.children('option[value=\"' + key + '\"]').remove();\n            }\n        });\n        \n        //filter out products if a productgroup is set\n        if(\$('select#productsgroups_to_add').val() != '')\n        {\n            var products_from_group = \$('select#productsgroups_to_add').val().split(',');\n            products_select.children('option[value != \"\"]').each( function()\n            {\n                // if doesn't belong to the group, remove it\n                if(products_from_group.indexOf(\$(this).val()) == '-1')\n                {\n                    \$(this).remove();\n                }\n            });\n        }\n        \n        \$('select#all_products_clone_me').after(products_select);\n        \n        // add amount of products to link\n        \$('a#add_product_to_upgradegroup span.products_count').text(\$('select#products_to_add option').length - 1);\n                \n        if(\$('table#upgradegroup_products tr.upgradegroup_product:visible').length > 0)\n        {\n            \$('table#upgradegroup_products tr.no-products-added').hide();\n        }\n        else\n        {\n            \$('table#upgradegroup_products tr.no-products-added').show();\n        }\n    }       \n    \n    function updateProductsInGroups()\n    {\n        var products_in_groups = '';\n        \$('#settings_hosting_upgradegroups input[name=\"upgradegroup_products[]\"]').each( function()\n        {\n            if(\$(this).val() != '')\n            {\n                products_in_groups += (products_in_groups == '') ? \$(this).val() : ',' + \$(this).val();   \n            }\n        });\n        var products_in_groups_arr = products_in_groups.split(',');\n        var products_in_groups_obj = new Object();\n        for(var n=0; n < products_in_groups_arr.length; n++)\n        {\n            products_in_groups_obj[products_in_groups_arr[n]] = products_in_groups_arr[n];\n        }\n    \n        \$('#settings_hosting_upgradegroups input[name=\"products_in_groups\"]').val(JSON.stringify(products_in_groups_obj));\n        \n        // show/hide error text\n        if(\$('#settings_hosting_upgradegroups div').length == 1)\n        {\n            \$('#settings_hosting_upgradegroups .no-groups-yet').show();\n        }\n        else\n        {\n            \$('#settings_hosting_upgradegroups .no-groups-yet').hide();\n        }\n    }\n    \n    function setTableZebra()\n    {\n        \$(\".zebra_table tbody tr\").removeClass(\"tr1\");\n        \$(\".zebra_table tbody tr:visible:odd\").addClass(\"tr1\");\n    }\n\n});\n</script>";

?>