<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
$autocomplete_div_count = ["total" => 0];
if(!function_exists("hex2bin")) {
    function hex2bin($string)
    {
        return pack("H*", $string);
    }
}
if(!function_exists("encrypt_data")) {
    function encrypt_data($data)
    {
        if(!function_exists("openssl_encrypt")) {
            fatal_error("openssl_encrypt", "openssl_encrypt function is missing");
        }
        $cipher_methods = @openssl_get_cipher_methods();
        if(!is_array($cipher_methods) || !in_array("aes-256-cbc", $cipher_methods)) {
            fatal_error("aes-256-cbc", "aes-256-cbc cipher method is not supported");
        }
        if(defined("DB_CRYPT") && 10 < strlen(DB_CRYPT)) {
            $key = hex2bin(substr(DB_CRYPT . "bdb349fbeac3f173ebaf70f10b1d1eb4390ed77e655dfdb481db2feaa4af928b", 0, 64));
        } else {
            $key = hex2bin("bdb349fbeac3f173ebaf70f10b1d1eb4390ed77e655dfdb481db2feaa4af928b");
        }
        $ivsize = 16;
        $iv = openssl_random_pseudo_bytes($ivsize);
        $ciphertext = openssl_encrypt($data, "aes-256-cbc", $key, 1, $iv);
        return base64_encode($iv . $ciphertext);
    }
}
if(!function_exists("decrypt_data")) {
    function decrypt_data($data)
    {
        if(!function_exists("openssl_decrypt")) {
            fatal_error("openssl_decrypt", "openssl_decrypt function is missing");
        }
        $data = base64_decode($data);
        $cipher_methods = @openssl_get_cipher_methods();
        if(!is_array($cipher_methods) || !in_array("aes-256-cbc", $cipher_methods)) {
            fatal_error("aes-256-cbc", "aes-256-cbc cipher method is not supported");
        }
        if(defined("DB_CRYPT") && 10 < strlen(DB_CRYPT)) {
            $key = hex2bin(substr(DB_CRYPT . "bdb349fbeac3f173ebaf70f10b1d1eb4390ed77e655dfdb481db2feaa4af928b", 0, 64));
        } else {
            $key = hex2bin("bdb349fbeac3f173ebaf70f10b1d1eb4390ed77e655dfdb481db2feaa4af928b");
        }
        $ivsize = 16;
        $iv = mb_substr($data, 0, $ivsize, "8bit");
        $ciphertext = mb_substr($data, $ivsize, mb_strlen($data, "8bit"), "8bit");
        return openssl_decrypt($ciphertext, "aes-256-cbc", $key, 1, $iv);
    }
}
class Base_Controller
{
    public $Success = [];
    public $Warning = [];
    public $Error = [];
    public function __construct()
    {
    }
    protected function set($key, $value)
    {
        $this->vars[$key] = $value;
    }
    protected function view($template, $template_dir = "views")
    {
        if(isset($this->vars) && is_array($this->vars)) {
            foreach ($this->vars as $key => $value) {
                ${$key} = $value;
            }
        }
        require_once "views/header.php";
        require_once $template_dir . "/" . $template;
        require_once "views/footer.php";
        exit;
    }
    public function element($template, $template_dir = "views/elements/")
    {
        if(isset($this->vars) && is_array($this->vars)) {
            foreach ($this->vars as $key => $value) {
                ${$key} = $value;
            }
        }
        require_once $template_dir . $template;
    }
    public function router($page)
    {
        $action = isset($_POST["action"]) && $_POST["action"] ? $_POST["action"] : (isset($_GET["action"]) && $_GET["action"] ? $_GET["action"] : "");
        if(method_exists($this, $page . "_" . $action)) {
            return $this->{$page . "_" . $action}();
        }
        if(method_exists($this, $page)) {
            return $this->{$page}();
        }
        exit("page not found");
    }
    protected function merge_messages($obj)
    {
        if(isset($obj->Error) && is_array($obj->Error)) {
            $this->Error = array_merge($this->Error, $obj->Error);
        }
        if(isset($obj->Warning) && is_array($obj->Warning)) {
            $this->Warning = array_merge($this->Warning, $obj->Warning);
        }
        if(isset($obj->Success) && is_array($obj->Success)) {
            $this->Success = array_merge($this->Success, $obj->Success);
        }
    }
}
class Template_Helper
{
    private $vars = [];
    public function __construct()
    {
        $this->loadSidebar = false;
    }
    public function __set($index, $value)
    {
        $this->vars[$index] = $value;
    }
    public function __get($index)
    {
        return isset($this->vars[$index]) ? $this->vars[$index] : NULL;
    }
    public function __isset($index)
    {
        if(isset($this->vars[$index])) {
            return false === empty($this->vars[$index]);
        }
        return NULL;
    }
    public function show($name, $loadHeader = true, $fullPath = false, $from_path = "")
    {
        if($fullPath === true) {
            $path = $from_path . $name;
        } else {
            $path = $from_path . "views" . "/" . $name . ".php";
        }
        if(!file_exists($path)) {
            fatal_error("Template not found", $path);
            return false;
        }
        foreach ($this->vars as $key => $value) {
            ${$key} = $value;
        }
        if($loadHeader === true) {
            if($this->loadSidebar) {
                $sidebar_template = $this->loadSidebar . ".php";
            }
            include $from_path . "views" . "/header.php";
        }
        include $path;
        if($loadHeader === true) {
            include $from_path . "views" . "/footer.php";
        }
    }
    public function element($name, $from_path = "")
    {
        $path = $from_path . "views/elements/";
        $name = $name . ".php";
        $this->show($name, false, true, $path);
    }
    public function setSidebar($loadSidebar)
    {
        $this->loadSidebar = $loadSidebar;
    }
}
class global_flash_message
{
}
function check_email_address($emailAddresses, $param = "multiple", $formatTo = ";")
{
    $emailAddresses = htmlspecialchars_decode($emailAddresses);
    switch ($param) {
        case "single":
            return is_email($emailAddresses);
            break;
        case "convert":
        case "multiple":
            if(0 < strlen($emailAddresses)) {
                $formattedEmailAddresses = [];
                $emailAddresses = str_replace(",", ";", $emailAddresses);
                $arrayEmailAddress = [];
                $explodeEmail = explode(";", $emailAddresses);
                foreach ($explodeEmail as $emailAddress) {
                    if(0 < strlen($emailAddress) && is_email(trim($emailAddress)) === false) {
                        if($param == "convert") {
                        } else {
                            return false;
                        }
                    } elseif(0 < strlen($emailAddress) && $param == "convert") {
                        $formattedEmailAddresses[] = trim($emailAddress);
                    }
                }
                if(!empty($formattedEmailAddresses)) {
                    return str_replace("&amp;", "&", htmlspecialchars(implode($formatTo, $formattedEmailAddresses)));
                }
            }
            if($param == "convert") {
                return "";
            }
            return true;
            break;
    }
}
function getFirstMailAddress($emailAddress)
{
    $emailAddress = check_email_address($emailAddress, "convert");
    if(strpos($emailAddress, ";")) {
        $arrayEmailAddress = explode(";", $emailAddress);
        return $arrayEmailAddress[0];
    }
    return $emailAddress;
}
function stripReturnAndSubstring($str, $substring = false)
{
    $str = str_replace(["\r\n", "\n\r", "\r", "\n"], " ", $str);
    if($substring !== false) {
        $str = $substring < strlen($str) ? substr($str, 0, $substring) . "..." : substr($str, 0, $substring);
    }
    return $str;
}
function extractNumberAndSuffix($val, $return = false)
{
    preg_match("/^([\\+\\-|]?(\\s)?([0-9]+[\\.\\,\\s|]?)*([0-9]+))?(.*)/i", trim($val), $match);
    $theNumber = str_replace(" ", "", number2db($match[1]));
    $suffix = end($match);
    switch ($return) {
        case "number":
            return $theNumber;
            break;
        case "suffix":
            return $suffix;
            break;
        default:
            return [$theNumber, $suffix];
    }
}
function number2db($amount)
{
    if(strrpos($amount, ".") < strrpos($amount, ",")) {
        $amount = str_replace(",", ".", str_replace(".", "", $amount));
    } else {
        $amount = str_replace(",", "", $amount);
    }
    return trim($amount);
}
function number2site($amount)
{
    return str_replace(".", AMOUNT_DEC_SEPERATOR, $amount);
}
function generate_action_button($actionButtons)
{
    if(!empty($actionButtons)) {
        if(!is_numeric(key($actionButtons))) {
            $collectionActionButton[] = $actionButtons;
        } else {
            $collectionActionButton = $actionButtons;
        }
        $extraDialog = [];
        foreach ($collectionActionButton as $button) {
            echo "<li " . (isset($button["li-class"]) ? "class=\"" . $button["li-class"] . "\"" : "") . " ><a class=\"ico set1 " . $button["class"] . "\"" . (isset($button["style"]) ? " style=\"" . $button["style"] . "\"" : "") . (isset($button["dialog"]) ? " onclick=\"\$('#dialog_" . $button["dialog"]["dialogName"] . "').dialog('open');\"" : "") . ">" . $button["title"] . "</a></li>";
            if(isset($button["dialog"])) {
                $extraDialog[] = $button["dialog"];
            }
        }
        $dialogShow = false;
        if(!empty($extraDialog)) {
            $dialogShow = "";
            foreach ($extraDialog as $dialog) {
                $dialogShow .= "<div id=\"dialog_" . $dialog["dialogClass"] . "\" class=\"hide\" title=\"" . $dialog["title"] . "\">\n\t\t\t\t\t<form name=\"form_" . $dialog["dialogClass"] . "\" method=\"post\" action=\"" . $dialog["formAction"] . "\">\n\t\t\t\t\t\t<input type=\"hidden\" name=\"id\" value=\"" . $dialog["Identifier"] . "\"/>\n\t\t\t\t\t\t" . $dialog["description"] . "<br />\n\t\t\t\t\t\t" . $dialog["extraInput"] . "\n\t\t\t\t\t\t\n\t\t\t\t\t\t<p><a class=\"" . $dialog["buttonClass"] . " alt1 float_left\" onclick=\"if(\$('#" . $dialog["dialogClass"] . "_imsure').html() == null || \$('#" . $dialog["dialogClass"] . "_imsure:checked').val() != undefined){ \$('#loader_download').show(); \$(this).hide(); }\" id=\"" . $dialog["dialogClass"] . "_btn\"><span>" . $dialog["dialogButtonText"] . "</span></a></p>\n\t\t\t\t\t\t<p><a class=\"c1 a1 float_right\" onclick=\"\$('#dialog_" . $dialog["dialogClass"] . "').dialog('close');\"><span>" . __("cancel") . "</span></a></p>\n\t\t\t\t\t\t<span id=\"loader_download\" class=\"hide\">\n\t\t\t\t\t\t<img src=\"images/icon_circle_loader_green.gif\" class=\"ico inline\" style=\"margin-bottom:-3px;\" />&nbsp;&nbsp;\n\t\t\t\t\t\t<span class=\"loading_green\">" . __("loading") . "</span>\n\t\t\t\t\t\t</span>\n\t\t\t\t\t</form>\n\t\t\t\t</div>";
            }
        }
        return $dialogShow;
    }
}
function generate_table($table_id, $options = [], $direct_show = true)
{
    if(!empty($options)) {
        if(isset($_SESSION["backoffice_tables_config"][$table_id]) && is_array($_SESSION["backoffice_tables_config"][$table_id])) {
            if(isset($options["sort_by"]) && isset($_SESSION["backoffice_tables_config"][$table_id]["sort_by"])) {
                unset($options["sort_by"]);
                unset($options["sort_order"]);
            }
            $_SESSION["backoffice_tables_config"][$table_id] = array_merge($_SESSION["backoffice_tables_config"][$table_id], $options);
        } else {
            $_SESSION["backoffice_tables_config"][$table_id] = $options;
        }
    }
    if($direct_show === true) {
        $parameters = isset($options["parameters"]) ? $options["parameters"] : [];
        load_table($table_id, $parameters);
    }
}
function load_table($table_id, $parameters = [], $overrule_parameter = [])
{
    $options = $_SESSION["backoffice_tables_config"][$table_id];
    if(!empty($overrule_parameter)) {
        $options["parameters"] = $overrule_parameter;
    }
    if(empty($options["cols"])) {
        return false;
    }
    $data_array = [];
    if($options["data"]) {
        $current_page = isset($parameters["page_number"]) ? $parameters["page_number"] : (isset($options["page_number"]) ? $options["page_number"] : 1);
        $rows_per_page = isset($parameters["results_per_page"]) ? $parameters["results_per_page"] : (isset($options["results_per_page"]) ? $options["results_per_page"] : MAX_RESULTS_LIST);
        $sort_by = isset($parameters["sort_by"]) ? $parameters["sort_by"] : (isset($options["sort_by"]) ? $options["sort_by"] : "");
        if(isset($parameters["sort_by"]) && $parameters["sort_by"] && isset($options["sort_by"]) && $options["sort_by"] && $parameters["sort_by"] == $options["sort_by"]) {
            $sort_order = isset($options["sort_order"]) && $options["sort_order"] == "ASC" ? "DESC" : "ASC";
        } else {
            $sort_order = isset($parameters["sort_order"]) ? $parameters["sort_order"] : (isset($options["sort_order"]) ? $options["sort_order"] : "ASC");
        }
        $filter = isset($parameters["filter"]) ? $parameters["filter"] : (isset($options["filter"]) ? $options["filter"] : "");
        if($filter != "") {
            $options["parameters"]["filter"] = $filter;
        }
        $offset = is_numeric($rows_per_page) ? (max(1, $current_page) - 1) * $rows_per_page : 0;
        if(is_array($options["data"])) {
            if(count($options["data"]) == 3) {
                if(@file_exists($options["data"][0])) {
                    require_once $options["data"][0];
                    if(@class_exists($options["data"][1])) {
                        $object = new $options["data"][1]();
                        $data_array = $object->{$options}["data"][2]($offset, $rows_per_page, $sort_by, $sort_order, $options["parameters"] ?? []);
                    }
                }
            } else {
                global $_module_instances;
                $parameters = isset($options["parameters"]) ? $options["parameters"] : [];
                $data_array = $_module_instances[$options["data"][0]]->{$options}["data"][1]($offset, $rows_per_page, $sort_by, $sort_order, $parameters);
            }
        } else {
            $data_array = call_user_func_array($options["data"], [$offset, $rows_per_page, $sort_by, $sort_order, $options["parameters"]]);
        }
    }
    $_SESSION["backoffice_tables_config"][$table_id]["page_number"] = $current_page;
    $_SESSION["backoffice_tables_config"][$table_id]["results_per_page"] = $rows_per_page;
    $_SESSION["backoffice_tables_config"][$table_id]["sort_by"] = $sort_by;
    $_SESSION["backoffice_tables_config"][$table_id]["sort_order"] = $sort_order;
    $_SESSION["backoffice_tables_config"][$table_id]["filter"] = $filter;
    if((empty($data_array) || count(array_diff(array_keys($data_array), ["TotalResults", "TotalText"])) === 0) && 1 < $current_page) {
        return load_table($table_id, ["page_number" => 1, "results_per_page" => $rows_per_page]);
    }
    if(!empty($options["actions"])) {
        $form_action = isset($options["form_action"]) && $options["form_action"] ? $options["form_action"] : "modules.php";
        echo "<form id=\"" . $table_id . "Form\" name=\"form_" . $table_id . "\" method=\"post\" action=\"" . $form_action . "\"><fieldset>";
        if(isset($options["redirect_url"]) && $options["redirect_url"]) {
            echo "<input type=\"hidden\" name=\"table_redirect_url\" value=\"";
            echo $options["redirect_url"];
            echo "\" />";
        }
    }
    $col_count = isset($options["hide_cols"]) && is_array($options["hide_cols"]) ? count($options["cols"]) - count($options["hide_cols"]) : count($options["cols"]);
    $data_attributes = "";
    if(isset($options["page_total_placeholder"]) && $options["page_total_placeholder"] && isset($data_array["TotalResults"])) {
        $data_attributes = " data-total-placeholder=\"" . $options["page_total_placeholder"] . "\" data-total-results=\"" . $data_array["TotalResults"] . "\"";
    }
    if(isset($options["page_total_text_placeholder"]) && $options["page_total_text_placeholder"] && isset($data_array["TotalText"])) {
        $data_attributes .= " data-total-text-placeholder=\"" . $options["page_total_text_placeholder"] . "\" data-total-text-results=\"" . htmlspecialchars($data_array["TotalText"]) . "\"";
    }
    $ajax_callback_function = isset($options["ajax_callback_function"]) && $options["ajax_callback_function"] ? ",'" . $options["ajax_callback_function"] . "'" : "";
    $table_class = isset($options["table_class"]) ? " " . $options["table_class"] : "";
    echo "\t<div id=\"SubTable_";
    echo $table_id;
    echo "\">\t\n\t\t<table id=\"MainTable_";
    echo $table_id;
    echo "\" class=\"table1";
    echo $table_class;
    echo "\" cellpadding=\"0\" cellspacing=\"0\"";
    echo $data_attributes;
    echo ">\n\t\t\t<tr class=\"trtitle\">\n\t\t\t\t";
    foreach ($options["cols"] as $col_id => $col) {
        if(isset($options["hide_cols"]) && is_array($options["hide_cols"]) && isset($col["key"]) && in_array($col["key"], $options["hide_cols"]) || $col_id === "subtr" || $col["title"] == "") {
        } else {
            if(isset($col["colspan"]) && 1 < $col["colspan"]) {
                $col_count += $col["colspan"] - 1;
            }
            echo "<th scope=\"col\"" . (isset($col["colspan"]) && $col["colspan"] ? " colspan=\"" . $col["colspan"] . "\"" : "") . (isset($col["class"]) && $col["class"] ? " class=\"" . $col["class"] . "\"" : "") . (isset($col["width"]) && $col["width"] ? " style=\"width: " . $col["width"] . "px;\"" : "") . ">";
            if($col_id === 0 && !empty($options["actions"])) {
                echo "<label><input name=\"" . $table_id . "Batch\" class=\"BatchCheck\" type=\"checkbox\" value=\"on\" /></label> ";
            }
            if(isset($col["sortable"]) && $col["sortable"]) {
                echo "<a onclick=\"wf_table_sort_by('" . $table_id . "','" . $col["sortable"] . "','" . (isset($options["parameters"]) ? htmlspecialchars(json_encode($options["parameters"])) : "") . "'" . $ajax_callback_function . ")\" class=\"ico set2 pointer " . ($sort_by == $col["sortable"] ? $sort_order == "ASC" ? "arrowup" : "arrowdown" : "arrowhover") . "\"" . (isset($col["style"]) && $col["style"] ? " style=\"" . $col["style"] . "\"" : "") . ">";
            }
            echo $col["title"];
            if(isset($col["sort"]) && $col["sort"]) {
                echo "</a>";
            }
            echo "</th>";
        }
    }
    echo "\t\t\t</tr>\n\t\t\t";
    $rowCounter = 0;
    foreach ($data_array as $k => $row_data) {
        if(!is_numeric($k)) {
        } else {
            $rowCounter++;
            echo "\t\t\t\t<tr class=\"hover_extra_info ";
            if($rowCounter % 2 === 1) {
                echo "tr1";
            }
            echo "\">\n\t\t\t\t\t";
            foreach ($row_data as $col_id => $cell_value) {
                if(!is_numeric($col_id)) {
                } elseif(isset($options["hide_cols"]) && is_array($options["hide_cols"]) && in_array($options["cols"][$col_id]["key"], $options["hide_cols"])) {
                } else {
                    $cell_values_in_array = is_array($cell_value) && isset($options["cols"][$col_id]["colspan"]) && $options["cols"][$col_id]["colspan"] == count($cell_value) ? $cell_value : [$cell_value];
                    foreach ($cell_values_in_array as $colspan_id => $cell_value) {
                        $td_class = isset($options["cols"][$col_id]["td_class"]) ? $options["cols"][$col_id]["td_class"] : "";
                        $td_properties = "";
                        if(isset($options["cols"][$col_id]["special_type"]) && $options["cols"][$col_id]["special_type"] == "amount") {
                            if($colspan_id === 0) {
                                $td_class = $td_class ? $td_class . " currency_sign_left" : "currency_sign_left";
                                $td_properties = "style=\"width: 5px;\"";
                            } elseif($colspan_id === 1) {
                                $td_properties = "style=\"width: 60px;\" align=\"right\"";
                            } elseif($colspan_id === 2) {
                                $td_class = $td_class ? $td_class . " currency_sign_right" : "currency_sign_right";
                                $td_properties = "style=\"width: 45px;\"";
                            }
                        }
                        echo "<td" . ($td_class ? " class=\"" . $td_class . "\"" : "") . ($td_properties ? " " . $td_properties : "") . ">";
                        if($col_id === 0 && !empty($options["actions"])) {
                            echo "<input name=\"ids[]\" type=\"checkbox\" class=\"" . $table_id . "Batch\" value=\"" . $row_data["id"] . "\" /> ";
                        }
                        echo $cell_value;
                        echo "</td>";
                    }
                }
            }
            echo "\t\t\t\t</tr>\n\t\t\t\t";
            if(isset($row_data["subtr"]) && is_array($row_data["subtr"]) && !(isset($options["hide_cols"]) && is_array($options["hide_cols"]) && in_array("subtr", $options["hide_cols"]))) {
                echo "<tr class=\"tr_extra_info mark2\">";
                foreach ($row_data["subtr"] as $col_id => $cell_value) {
                    $td_colspan = isset($options["cols"]["subtr"][$col_id]["colspan"]) ? " colspan=\"" . $options["cols"]["subtr"][$col_id]["colspan"] . "\"" : "";
                    $td_class = isset($options["cols"]["subtr"][$col_id]["td_class"]) ? $options["cols"]["subtr"][$col_id]["td_class"] : "";
                    echo "<td" . ($td_class ? " class=\"" . $td_class . "\"" : "") . ($td_properties ? " " . $td_properties : "") . " " . $td_colspan . " >";
                    echo $cell_value;
                }
                echo "</tr>";
            }
        }
    }
    if($rowCounter === 0) {
        echo "\t\t\t\t<tr>\n\t\t\t\t\t<td colspan=\"";
        echo $col_count;
        echo "\">\n\t\t\t\t\t\t";
        echo __("no results found");
        echo "\t\t\t\t\t</td>\n\t\t\t\t</tr>\n\t\t\t\t";
    } elseif(!empty($options["actions"]) || $rows_per_page != "all" && isset($data_array["TotalResults"]) && min(MIN_PAGINATION, $rows_per_page) < $data_array["TotalResults"]) {
        echo "<tr class=\"table_options\"><td colspan=\"" . $col_count . "\" style=\"padding: 7px 10px 5px\">";
        if(!empty($options["actions"])) {
            echo "<p class=\"ico inline hook\"><select name=\"action\" class=\"select1 BatchSelect\">";
            echo "<option value=\"\" selected=\"selected\">" . __("with selected") . "</option>";
            $dialog_array = [];
            foreach ($options["actions"] as $label => $tmp_action) {
                if(!is_numeric($label)) {
                    echo "<optgroup label=\"" . $label . "\">";
                    foreach ($tmp_action as $tmp_action_sub) {
                        $dialog_prefix = isset($tmp_action_sub["dialog"]) ? "dialog:" : "";
                        $before_open = isset($tmp_action_sub["dialog"]["before_open"]) ? " data-before-open=\"" . $tmp_action_sub["dialog"]["before_open"] . "\"" : "";
                        echo "<option value=\"" . $dialog_prefix . $tmp_action_sub["action"] . "\"" . $before_open . ">" . $tmp_action_sub["title"] . "</option>";
                        if(isset($tmp_action_sub["dialog"])) {
                            $dialog_array[] = $tmp_action_sub;
                        }
                    }
                    echo "</optgroup>";
                } else {
                    $dialog_prefix = isset($tmp_action["dialog"]) ? "dialog:" : "";
                    $before_open = isset($tmp_action["dialog"]["before_open"]) ? " data-before-open=\"" . $tmp_action["dialog"]["before_open"] . "\"" : "";
                    echo "<option value=\"" . $dialog_prefix . $tmp_action["action"] . "\"" . $before_open . ">" . $tmp_action["title"] . "</option>";
                    if(isset($tmp_action["dialog"])) {
                        $dialog_array[] = $tmp_action;
                    }
                }
            }
            echo "</select></p><br />";
            foreach ($dialog_array as $tmp_action_dialog) {
                echo "\t\t\t\t\t\t\t<div class=\"hide\" id=\"dialog_";
                echo $tmp_action_dialog["action"];
                echo "\" title=\"";
                echo $tmp_action_dialog["dialog"]["title"];
                echo "\">\n\t\t\t\t\t\t\t\t";
                echo $tmp_action_dialog["dialog"]["content"];
                echo "\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t";
            }
        }
        if($rows_per_page != "all" && isset($data_array["TotalResults"]) && min(MIN_PAGINATION, $rows_per_page) < $data_array["TotalResults"] && min(MIN_PAGINATION, $rows_per_page) < $data_array["TotalResults"]) {
            echo "<br />\t\t\t\t\t\t<div id=\"SubTable_Paginate_";
            echo $table_id;
            echo "\">\n\t\t\t\t\t\t\n\t\t\t\t\t\t<p class=\"float_left\">";
            echo __("show");
            echo " <select class=\"select1 size2\" onchange=\"wf_table_pagination('";
            echo $table_id;
            echo "','1',this.value,'";
            echo htmlspecialchars(json_encode($options["parameters"]));
            echo "'";
            echo $ajax_callback_function;
            echo ")\">\n\t\t\t\t\t\t<option value=\"10\"";
            if($rows_per_page == 10) {
                echo " selected=\"selected\"";
            }
            echo ">10</option>\n\t\t\t\t\t\t<option value=\"25\"";
            if($rows_per_page == 25) {
                echo " selected=\"selected\"";
            }
            echo ">25</option>\n\t\t\t\t\t\t<option value=\"50\"";
            if($rows_per_page == 50) {
                echo " selected=\"selected\"";
            }
            echo ">50</option>\n\t\t\t\t\t\t<option value=\"75\"";
            if($rows_per_page == 75) {
                echo " selected=\"selected\"";
            }
            echo ">75</option>\n\t\t\t\t\t\t<option value=\"100\"";
            if($rows_per_page == 100) {
                echo " selected=\"selected\"";
            }
            echo ">100</option>\n\t\t\t\t\t\t<option value=\"999999\"";
            if($rows_per_page == 999999) {
                echo " selected=\"selected\"";
            }
            echo ">";
            echo __("all");
            echo "</option>\n\t\t\t\t\t\t</select> ";
            echo __("results per page");
            echo "</p>\n\t\t\t\t\t\t";
            $pages = max(1, ceil($data_array["TotalResults"] / $rows_per_page));
            $range_start = max(1, $current_page - 2);
            $range_end = min($pages, $current_page + 2);
            echo "\t\t\t\t\t\t\n\t\t\t\t\t\t<ul class=\"list4 float_right\">\n\t\t\t\t\t\t\t";
            if($current_page != 1) {
                echo "\t\t\t\t\t\t\t<li><a class=\"ico inline arrowleft pointer\" onclick=\"wf_table_pagination('";
                echo $table_id;
                echo "','";
                echo $current_page - 1;
                echo "','";
                echo $rows_per_page;
                echo "','";
                echo htmlspecialchars(json_encode($options["parameters"]));
                echo "'";
                echo $ajax_callback_function;
                echo ");\">";
                echo __("previous");
                echo "</a></li>\n\t\t\t\t\t\t\t";
            }
            echo "\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t";
            for ($p = 1; $p <= $pages; $p++) {
                if($range_start <= $p && $p <= $range_end) {
                    echo "<li";
                    if($p == $current_page) {
                        echo " class=\"on\"";
                    }
                    echo "><a class=\"pointer\" onclick=\"wf_table_pagination('";
                    echo $table_id;
                    echo "','";
                    echo $p;
                    echo "','";
                    echo $rows_per_page;
                    echo "','";
                    echo htmlspecialchars(json_encode($options["parameters"]));
                    echo "'";
                    echo $ajax_callback_function;
                    echo ");\">";
                    echo $p;
                    echo "</a></li>";
                } elseif($p == 1 && 1 < $range_start) {
                    echo "<li><a class=\"pointer\" onclick=\"wf_table_pagination('";
                    echo $table_id;
                    echo "','";
                    echo $p;
                    echo "','";
                    echo $rows_per_page;
                    echo "','";
                    echo htmlspecialchars(json_encode($options["parameters"]));
                    echo "'";
                    echo $ajax_callback_function;
                    echo ");\">";
                    echo $p;
                    echo "</a></li>";
                    if(2 < $range_start) {
                        echo "<li><span>...</span></li>";
                    }
                } elseif($p == $pages && $range_end < $pages) {
                    if($range_end < $pages - 1) {
                        echo "<li><span>...</span></li>";
                    }
                    echo "<li><a class=\"pointer\" onclick=\"wf_table_pagination('";
                    echo $table_id;
                    echo "','";
                    echo $p;
                    echo "','";
                    echo $rows_per_page;
                    echo "','";
                    echo htmlspecialchars(json_encode($options["parameters"]));
                    echo "'";
                    echo $ajax_callback_function;
                    echo ");\">";
                    echo $p;
                    echo "</a></li>";
                }
            }
            echo "\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t";
            if($current_page != $pages) {
                echo "\t\t\t\t\t\t\t<li><a class=\"ico inline arrowright pointer\" onclick=\"wf_table_pagination('";
                echo $table_id;
                echo "','";
                echo $current_page + 1;
                echo "','";
                echo $rows_per_page;
                echo "','";
                echo htmlspecialchars(json_encode($options["parameters"]));
                echo "'";
                echo $ajax_callback_function;
                echo ");\">";
                echo __("next");
                echo "</a></li>\n\t\t\t\t\t\t\t";
            }
            echo "\t\t\t\t\t\t</ul>\n\t\t\t\t\t\t</div><br />\n\t\t\t\t\t\t";
        }
        echo "</td></tr>";
    }
    echo "\t\t</table>\n\t\t\n\t\t";
    if(isset($options["page_total_placeholder"]) && $options["page_total_placeholder"] && isset($data_array["TotalResults"])) {
        echo "\t\t\t<script type=\"text/javascript\">\n\t\t\t\$(function(){\n\t\t\t\t\$('#";
        echo $options["page_total_placeholder"];
        echo "').html('";
        echo $data_array["TotalResults"];
        echo "').show();\n\t\t\t});\n\t\t\t</script>\n\t\t\t";
    }
    if(isset($options["page_total_text_placeholder"]) && $options["page_total_text_placeholder"] && isset($data_array["TotalText"])) {
        echo "\t\t\t<script type=\"text/javascript\">\n\t\t\t\$(function(){\n\t\t\t\t\$('#";
        echo $options["page_total_text_placeholder"];
        echo "').html('";
        echo $data_array["TotalText"];
        echo "').show();\n\t\t\t});\n\t\t\t</script>\n\t\t\t";
    }
    if(isset($options["hide_table_if_no_results"]) && $options["hide_table_if_no_results"] && isset($data_array["TotalResults"]) && (int) $data_array["TotalResults"] === 0) {
        echo "\t\t\t<script type=\"text/javascript\">\n\t\t\t\$(function(){\n\t\t\t\t\$('#show_table_";
        echo $options["hide_table_if_no_results"];
        echo "_div').remove();\n\t\t\t\t";
        if(isset($options["page_total_placeholder"]) && $options["page_total_placeholder"]) {
            echo "\t\t\t\t\t\$('#";
            echo $options["page_total_placeholder"];
            echo "').parent('a').next().remove();\n\t\t\t\t\t\$('#";
            echo $options["page_total_placeholder"];
            echo "').parent('a').remove();\n\t\t\t\t\t";
        }
        echo "\t\t\t});\n\t\t\t</script>\n\t\t\t";
    }
    echo "\t</div>\n\t";
    if(!empty($options["actions"])) {
        echo "</fieldset></form>";
    }
}
function show_sidebar($items_sidebar)
{
    if(is_array($items_sidebar)) {
        foreach ($items_sidebar as $_sidebar_group) {
            if(empty($_sidebar_group)) {
            } else {
                echo "\t\t\t<div id=\"submenu\">\n\t\t\t\t<ul>\n\t\t\t\t\t";
                foreach ($_sidebar_group as $type => $_sidebar_item) {
                    if(is_string($type) && $type == "header") {
                        echo "<li class=\"submenu_header\">" . $_sidebar_item["title"] . "</li>";
                    } elseif(isset($_sidebar_item["empty"]) && $_sidebar_item["empty"] === true) {
                        echo "<li>&nbsp;</li>";
                    } else {
                        echo "<li" . ($_sidebar_item["active"] === true ? " class=\"active\"" : "") . "><a href=\"" . htmlspecialchars($_sidebar_item["url"]) . "\">" . htmlspecialchars($_sidebar_item["title"]) . "</a></li>";
                    }
                }
                echo "\t\t\t\t</ul>\n\t\t\t</div>\n\t\t\t";
            }
        }
    }
}
function show_menu($items_menu, $level = 0)
{
    if(!empty($items_menu)) {
        for ($menu_i = 0; $menu_i < count($items_menu); $menu_i++) {
            echo $level === 0 ? "<li><div>" : "<span>";
            echo "<a href=\"" . htmlspecialchars($items_menu[$menu_i]["url"]) . "\"" . (0 < $level ? "class=\"sub-menu-sublink\"" : "") . ">" . $items_menu[$menu_i]["title"] . "</a>";
            if(0 < $level && $menu_i < count($items_menu) - 1) {
                echo ", ";
            } elseif($level === 0 && isset($items_menu[$menu_i]["children"]) && 0 < count($items_menu[$menu_i]["children"])) {
                show_menu($items_menu[$menu_i]["children"], 1);
            }
            echo $level === 0 ? "</div></li>" : "</span>";
        }
    }
}
function show_log($log_type, $reference_id, $allow_delete = false)
{
    $options = [];
    $options["cols"] = [["key" => "Date", "title" => __("date"), "width" => 180, "sortable" => "Date"], ["key" => "Action", "title" => __("action")], ["key" => "Who", "title" => __("employee"), "sortable" => "Who", "width" => 150]];
    $options["data"] = "show_log_data";
    $options["sort_by"] = "Date";
    $options["sort_order"] = "DESC";
    $options["parameters"]["type"] = $log_type;
    $options["parameters"]["reference_id"] = $reference_id;
    $options["redirect_url"] = "modules.php?module=" . $log_type . "&page=show&id=" . $reference_id;
    if($allow_delete === true) {
        $options["actions"] = [["action" => "removelogentry", "title" => __("remove logelements")]];
    }
    generate_table("loglines_" . $log_type, $options);
}
function show_log_data($offset, $results_per_page, $sort_by = "", $sort_order = "DESC", $parameters = [])
{
    $options = !empty($parameters) ? $parameters : [];
    $options["offset"] = $offset;
    $options["results_per_page"] = $results_per_page;
    $options["sort_by"] = $sort_by;
    $options["sort_order"] = $sort_order;
    $data = [];
    require_once "class/logfile.php";
    $logfile = new logfile();
    $fields = ["Date", "Who", "Name", "Action", "Values", "Translate"];
    $list_logfile = $logfile->all($fields, $options["sort_by"], $options["sort_order"], floor($options["offset"] / $options["results_per_page"]) + 1, $options["type"], $options["reference_id"], $options["results_per_page"]);
    $data = ["TotalResults" => $list_logfile["CountRows"]];
    foreach ($list_logfile as $logID => $log_item) {
        if(is_numeric($logID)) {
            $log_item["Action"] = $log_item["Action"];
            if($log_item["Translate"] == "yes") {
                $log_item["Action"] = __("log." . $log_item["Action"], $options["type"]);
                $log_item["Values"] = explode("|", $log_item["Values"]);
                if(strpos($log_item["Action"], "%s") && count(explode("%s", $log_item["Message"])) - 1 <= count($log_item["Values"])) {
                    $log_item["Action"] = call_user_func_array("sprintf", array_merge([$log_item["Action"]], $log_item["Values"]));
                }
            }
            $data[] = ["id" => $log_item["id"], rewrite_date_db2site($log_item["Date"]) . " " . __("at") . " " . rewrite_date_db2site($log_item["Date"], "%H:%i:%s"), $log_item["Action"], !in_array($log_item["Name"], ["0", "api", "clientarea", "cronjob"]) ? $log_item["Name"] : __("log line who " . $log_item["Name"])];
        }
    }
    return $data;
}
function show_subscription_tab_title($termination_site_format, $startperiod_site_format, $autorenew)
{
    $title = __("subscription");
    if(!$startperiod_site_format) {
        $title .= " (" . __("none") . ")";
    } elseif($termination_site_format) {
        if(rewrite_date_site2db($startperiod_site_format) < rewrite_date_site2db($termination_site_format)) {
            $title .= " (" . sprintf(__("subscription tab - do not invoice after x"), $termination_site_format) . ")";
        } else {
            $title .= " (" . __("subscription tab - terminated at x") . ")";
        }
    } elseif($autorenew == "once") {
        $title .= " (" . __("subscription tab - autorenew once") . ")";
    } elseif($autorenew == "no") {
        $title .= " (" . __("subscription tab - autorenew no") . ")";
    }
    return $title;
}
function show_subscription_column($subscription)
{
    global $array_periodic;
    global $array_taxpercentages;
    if(!is_array($subscription)) {
        $subscription = (array) $subscription;
    }
    $_tmp_amount = VAT_CALC_METHOD == "incl" ? round($subscription["PriceExcl"] * (1 + $subscription["TaxPercentage"]), 5) : $subscription["PriceExcl"];
    $_tmp_amount = (isset($subscription["Number"]) ? $subscription["Number"] : 1) * $_tmp_amount * round(1 - $subscription["DiscountPercentage"], 8);
    if(0 < $subscription["PeriodicID"] && $subscription["TerminationDate"] != "0000-00-00" && $subscription["StartPeriod"] < $subscription["TerminationDate"]) {
        $subscription_td = "<span class=\"inline_subscription cancelled infopopupleftsmall\">&nbsp;<span class=\"popup\">" . money($_tmp_amount) . " " . __("per") . " " . $array_periodic[$subscription["Periodic"]];
        if(!empty($array_taxpercentages)) {
            $subscription_td .= " " . (VAT_CALC_METHOD == "incl" ? __("incl vat") : __("excl vat"));
        }
        $subscription_td .= "<br />" . __("next invoice at date") . " " . rewrite_date_db2site($subscription["NextDate"]) . "<br />" . sprintf(__("do not invoice after X"), rewrite_date_db2site($subscription["TerminationDate"])) . "<b></b></span></span>";
    } elseif($subscription["TerminationDate"] && $subscription["TerminationDate"] != "0000-00-00") {
        $subscription_td = "<span class=\"inline_subscription none infopopupleftsmall\">&nbsp;<span class=\"popup\">" . __("subscription terminated at") . " " . rewrite_date_db2site($subscription["TerminationDate"]) . "<b></b></span></span>";
    } elseif(0 < $subscription["PeriodicID"] && $subscription["PeriodicStatus"] < 8 && $subscription["AutoRenew"] == "no") {
        $subscription_td = "<span class=\"inline_subscription pause infopopupleftsmall\">&nbsp;<span class=\"popup\">" . money($_tmp_amount) . " " . __("per") . " " . $array_periodic[$subscription["Periodic"]];
        if(!empty($array_taxpercentages)) {
            $subscription_td .= " " . (VAT_CALC_METHOD == "incl" ? __("incl vat") : __("excl vat"));
        }
        $subscription_td .= "<br />" . __("do not invoice automatically") . "<b></b></span></span>";
    } elseif(0 < $subscription["PeriodicID"] && $subscription["PeriodicStatus"] < 8 && $subscription["TerminationDate"] == "0000-00-00") {
        $subscription_td = "<span class=\"inline_subscription active infopopupleftsmall\">&nbsp;<span class=\"popup\">" . money($_tmp_amount) . " " . __("per") . " " . $array_periodic[$subscription["Periodic"]];
        if(!empty($array_taxpercentages)) {
            $subscription_td .= " " . (VAT_CALC_METHOD == "incl" ? __("incl vat") : __("excl vat"));
        }
        $subscription_td .= "<br />" . ($subscription["AutoRenew"] == "once" ? __("next one-time invoice at date") : __("next invoice at date")) . " " . rewrite_date_db2site($subscription["NextDate"]) . "<b></b></span></span>";
    } else {
        $subscription_td = "<span class=\"inline_subscription none infopopupleftsmall\">&nbsp;<span class=\"popup\">" . __("no subscription") . "<b></b></span></span>";
    }
    return $subscription_td;
}
function software_get_relative_path()
{
    $parse_url = parse_url(BACKOFFICE_URL);
    $path = explode("/", $parse_url["path"]);
    $pro_map_name = isset($path) && is_array($path) ? $path[count($path) - 2] : "";
    $pro_map_name = $pro_map_name . "/";
    return $pro_map_name;
}
function service_termination_function($service_type, $service_id, $intro_text = false, $service_has_expiration_date = false)
{
    require_once "class/terminationprocedure.php";
    $termination = new Termination_Model();
    if($termination->show($service_type, $service_id)) {
        return false;
    }
    require_once "class/service.php";
    $service = new service();
    if(!$service->show($service_id, $service_type)) {
        return false;
    }
    $end_date = $termination->getTerminationEndDate($service, $service_has_expiration_date);
    $termination_procedure = new TerminationProcedure_Model();
    $parameters = ["service_type" => $service_type, "results_per_page" => "all"];
    $list_termination_procedures = $termination_procedure->listProcedures($parameters);
    echo "\t<a class=\"button1 alt1\" onclick=\"\$('#service_termination').dialog('open');\" style=\"margin-left: 20px;\"><span>";
    echo __("terminate service btn");
    echo "</span></a>\n\t<div id=\"service_termination\" title=\"";
    echo __("terminate service btn");
    echo "\">\n\t\t<form name=\"terminate_service_form\" method=\"post\" action=\"services.php?page=terminations\">\n\t\t<input type=\"hidden\" name=\"action\" value=\"terminate\" />\n\t\t<select name=\"ServiceType\" class=\"hide\"><option value=\"";
    echo $service_type;
    echo "\" selected=\"selected\">";
    echo $service_type;
    echo "</option></select>\n\t\t<input type=\"hidden\" name=\"ServiceID\" value=\"";
    echo $service_id;
    echo "\" />\n\t\t\n\t\t";
    if($intro_text) {
        echo $intro_text . "<br /><br />";
    } elseif($intro_text === false) {
        echo __("default termination dialog intro text") . "<br /><br />";
    }
    echo "\t\n\t\t<strong class=\"title\">";
    echo __("terminate dialog use procedure");
    if(U_SETTINGS_SHOW) {
        echo " <a href=\"termination-procedures.php\" class=\"a1 c1 normalfont marginl\">";
        echo __("manage termination procedures");
        echo "</a>";
    }
    echo "</strong>\n\t\t<select name=\"TerminationProcedure\" class=\"text1 size4f\">\n\t\t\t<option value=\"\">";
    echo __("please choose");
    echo "</option>\n\t\t\t";
    $default_termination_procedure = false;
    $term_preference = "direct";
    foreach ($list_termination_procedures as $tmp_procedure) {
        if($tmp_procedure->Default == "yes") {
            $default_termination_procedure = $tmp_procedure->id;
            $term_preference = $tmp_procedure->TermPreference;
        }
        echo "\t\t\t\t<option value=\"";
        echo $tmp_procedure->id;
        echo "\"";
        if($tmp_procedure->Default == "yes") {
            echo " selected=\"selected\"";
        }
        echo ">";
        echo htmlspecialchars($tmp_procedure->Name);
        echo "</option>\n\t\t\t\t";
    }
    echo "\t\t</select>\n\t\t<br /><br />\n\t\t\n\t\t<strong class=\"title\">";
    echo __("terminate dialog term");
    echo "</strong>\n\t\t<label><input type=\"radio\" name=\"TerminationTerm\" value=\"direct\"";
    if("direct" == $term_preference) {
        echo " checked=\"checked\"";
    }
    echo "/> ";
    echo __("termination term preference direct");
    echo "</label><br />\n\t\t<label><input type=\"radio\" name=\"TerminationTerm\" value=\"date\"";
    if("date" == $term_preference) {
        echo " checked=\"checked\"";
    }
    echo "/> ";
    echo __("termination term preference date");
    echo "</label><br />\n\t\t<label><input type=\"radio\" name=\"TerminationTerm\" data-date=\"";
    echo rewrite_date_db2site($end_date);
    echo "\" value=\"contract\"";
    if("contract" == $term_preference) {
        echo " checked=\"checked\"";
    }
    echo "/> ";
    echo __("termination term preference contract");
    echo " (";
    echo rewrite_date_db2site($end_date);
    echo ")</label><br />\n\t\t<br />\n\t\t\n\t\t<div id=\"service_termination_date\" class=\"hide\">\n\t\t\t<strong class=\"title\">";
    echo __("termination date");
    echo "</strong>\n\t\t\t<input type=\"text\" name=\"TerminationDate\" value=\"";
    echo rewrite_date_db2site($end_date);
    echo "\" class=\"text1 size6 datepicker\" />\n\t\t\t<br /><br />\n\t\t</div>\n\n        <strong class=\"title\">";
    echo __("termination reason");
    echo "</strong>\n        <textarea name=\"TerminationReason\" class=\"size11\"></textarea>\n        <br /><br />\n\t\t\n\t\t<strong class=\"title\">";
    echo __("termination actions");
    echo " <a id=\"service_termination_change_actions\" class=\"a1 c1 marginl normalfont\">";
    echo __("changeit");
    echo "</a></strong> \n\t\t<div id=\"service_termination_actions\">\n\t\t\t";
    echo __("termination no actions will be executed");
    echo "\t\t</div>\n\t\t<br />\n\t\t\n\t\t<label><input type=\"checkbox\" name=\"imsure\" value=\"yes\"/> ";
    echo __("terminate dialog imsure");
    echo "</label><br />\n\t\t<br />\n\t\t<p><a id=\"service_termination_btn\" class=\"button2 alt1 float_left\"><span>";
    echo __("terminate dialog btn");
    echo "</span></a></p>\n\t\t<p><a class=\"a1 c1 alt1 float_right\" onclick=\"\$('#service_termination').dialog('close');\"><span>";
    echo __("cancel");
    echo "</span></a></p>\n\t\t\n\t\t</form>\n\t</div>\n\t<style type=\"text/css\">\n\t#service_termination .table1 { margin-top: 5px;}\n\t</style>\n\t<script type=\"text/javascript\">\n\t\$(function(){\n\t\t\$('#service_termination').dialog({ autoOpen: false, width: 925, modal: true, resizable: false });\n\t\t\n\t\t\$('#service_termination_btn').click(function()\n\t\t{\n\t\t\tif(\$('#service_termination input[name=\"imsure\"]:checked').val() != null)\n\t\t\t{\n\t\t\t\t// Prevent double click\n\t\t\t\tif(\$(this).data('already_clicked') == true)\n\t\t\t\t{\n\t\t\t\t\treturn true;\n\t\t\t\t}\n\t\t\t\t\$(this).data('already_clicked', true);\n\n\t\t\t\tdocument.terminate_service_form.submit();\n\t\t\t}\t\n\t\t});\n\t\t\$('#service_termination input[name=\"imsure\"]').click(function()\n\t\t{\n\t\t\tif(\$('#service_termination input[name=\"imsure\"]:checked').val() != null)\n\t\t\t{\n\t\t\t\t\$('#service_termination_btn').removeClass('button2').addClass('button1');\n\t\t\t}\n\t\t\telse\n\t\t\t{\n\t\t\t\t\$('#service_termination_btn').removeClass('button1').addClass('button2');\n\t\t\t}\n\t\t});\n\t\t\n\t\t\$('select[name=\"TerminationProcedure\"]').change(function(){\n\t\t\t\n\t\t\t// Show change-link\n\t\t\t\$('#service_termination_change_actions').show();\n\t\t\t\n\t\t\tif(\$(this).val() != '')\n\t\t\t{\n\t\t\t\t\$.post('termination-procedures.php?page=ajax', { action: 'getTerminationProcedure', procedure_id: \$(this).val()}, function(data){\n\t\t\t\t\t// Use term preference\n\t\t\t\t\t\$('input[name=\"TerminationTerm\"][value=\"' + data.term_preference + '\"]').click();\n\t\t\t\t\t\n\t\t\t\t\t// Fill actions\n\t\t\t\t\t\$('#service_termination_actions').html(data.html);\n\t\t\t\t}, 'json');\n\t\t\t}\n\t\t\telse\n\t\t\t{\n\t\t\t\t\$('#service_termination_change_actions').show();\n\t\t\t\t\$('#service_termination_actions').html('";
    echo __("termination no actions will be executed");
    echo "');\n\t\t\t}\n\t\t});\n\t\t\n\t\t// Init\n\t\t\$('select[name=\"TerminationProcedure\"]').change();\n\t\t\n\t\t\$('#service_termination_change_actions').click(function(){\n\t\t\t\$(this).hide();\t\n\t\t\t\n\t\t\t\$.post('termination-procedures.php?page=ajax', { action: 'getTerminationActionsTableEdit', procedure_id: \$('select[name=\"TerminationProcedure\"]').val(), type: 'edit'}, function(data){\n\t\t\t\t\$('#service_termination_actions').html(data);\n\t\t\t}, 'html');\n\t\t});\n\t\t\n\t\t\$('input[name=\"TerminationTerm\"]').click(function(){\n\t\t\tif(\$(this).val() == 'direct')\n\t\t\t{\n\t\t\t\t\$('#service_termination_date').hide();\n\t\t\t}\n\t\t\telse if(\$(this).val() == 'contract')\n\t\t\t{\n\t\t\t\t\$('input[name=\"TerminationDate\"]').val(\$(this).data('date'));\n\t\t\t\t\$('#service_termination_date').hide();\n\t\t\t}\n\t\t\telse\n\t\t\t{\n\t\t\t\t\$('#service_termination_date').show();\n\t\t\t}\n\t\t});\n\t\t\n\t});\n\t</script>\n\t";
}
function service_termination_batch_processing($service_type, $service_id, $post_data, &$error_messages, $service_has_expiration_date = false)
{
    require_once "class/terminationprocedure.php";
    $termination_procedure = new TerminationProcedure_Model();
    $termination = new Termination_Model();
    if($termination->show($service_type, $service_id)) {
        return "already_done";
    }
    require_once "class/service.php";
    $service = new service();
    if(!$service->show($service_id, $service_type)) {
        $error_messages = array_merge($error_messages, $service->Error);
        return false;
    }
    if(isset($post_data["ProcedureAction"]) && is_array($post_data["ProcedureAction"])) {
        $termination->ProcedureActions = $termination_procedure->parseProcedureActionPost($post_data["ProcedureAction"]);
    }
    switch ($post_data["TerminationTerm"]) {
        case "date":
            $end_date = date("Y-m-d", strtotime(rewrite_date_site2db(esc($post_data["TerminationDate"]))));
            break;
        case "contract":
            $end_date = $termination->getTerminationEndDate($service, $service_has_expiration_date);
            break;
        default:
            $end_date = date("Y-m-d");
            $termination->ServiceType = $service_type;
            $termination->ServiceID = $service_id;
            $termination->Date = $end_date;
            $termination->ProcedureID = isset($post_data["TerminationProcedure"]) && 0 < $post_data["TerminationProcedure"] && !isset($post_data["ProcedureAction"]) ? esc($post_data["TerminationProcedure"]) : 0;
            $termination->Term = esc($post_data["TerminationTerm"]);
            $termination->Reason = esc($post_data["TerminationReason"]);
            if(!$termination->add()) {
                $error_messages = array_merge($error_messages, $termination->Error);
                return false;
            }
            return true;
    }
}
function service_termination_batch_dialog($service_type, $form_name, $intro_text = false)
{
    require_once "class/terminationprocedure.php";
    $termination = new Termination_Model();
    $termination_procedure = new TerminationProcedure_Model();
    $parameters = ["service_type" => $service_type, "results_per_page" => "all"];
    $list_termination_procedures = $termination_procedure->listProcedures($parameters);
    echo "\t<div id=\"custom_dialog_terminate_";
    echo $service_type;
    echo "\" title=\"";
    echo __("terminate service batch dialog title");
    echo "\" class=\"hide\">\n\t\t<form name=\"terminate_service_form_";
    echo $service_type;
    echo "\" method=\"post\" action=\"\">\n\t\t<input type=\"hidden\" name=\"action\" value=\"terminate_batch\" />\n\t\t<select name=\"ServiceType\" class=\"hide\"><option value=\"";
    echo $service_type;
    echo "\" selected=\"selected\">";
    echo $service_type;
    echo "</option></select>\n\t\t<div class=\"hide ServiceIDs\"></div>\n\t\t\n\t\t<p class=\"terminate_batch_confirm_text\">\n\t\t";
    if($intro_text) {
        echo $intro_text . "<br /><br />";
    } elseif($intro_text === false) {
        echo __("default termination batch dialog intro text") . "<br /><br />";
    }
    echo "\t\t</p>\n\t\t\n\t\t<strong class=\"title\">";
    echo __("terminate dialog use procedure");
    if(U_SETTINGS_SHOW) {
        echo " <a href=\"termination-procedures.php\" class=\"a1 c1 normalfont marginl\">";
        echo __("manage termination procedures");
        echo "</a>";
    }
    echo "</strong>\n\t\t<select name=\"TerminationProcedure\" class=\"text1 size4f\">\n\t\t\t<option value=\"\">";
    echo __("please choose");
    echo "</option>\n\t\t\t";
    $default_termination_procedure = false;
    $term_preference = "direct";
    foreach ($list_termination_procedures as $tmp_procedure) {
        if($tmp_procedure->Default == "yes") {
            $default_termination_procedure = $tmp_procedure->id;
            $term_preference = $tmp_procedure->TermPreference;
        }
        echo "\t\t\t\t<option value=\"";
        echo $tmp_procedure->id;
        echo "\"";
        if($tmp_procedure->Default == "yes") {
            echo " selected=\"selected\"";
        }
        echo ">";
        echo htmlspecialchars($tmp_procedure->Name);
        echo "</option>\n\t\t\t\t";
    }
    echo "\t\t</select>\n\t\t<br /><br />\n\t\t\n\t\t<strong class=\"title\">";
    echo __("terminate dialog term");
    echo "</strong>\n\t\t<label><input type=\"radio\" name=\"TerminationTerm\" value=\"direct\"";
    if("direct" == $term_preference) {
        echo " checked=\"checked\"";
    }
    echo "/> ";
    echo __("termination term preference direct");
    echo "</label><br />\n\t\t<label><input type=\"radio\" name=\"TerminationTerm\" value=\"date\"";
    if("date" == $term_preference) {
        echo " checked=\"checked\"";
    }
    echo "/> ";
    echo __("termination term preference date");
    echo "</label><br />\n\t\t<label><input type=\"radio\" name=\"TerminationTerm\" value=\"contract\"";
    if("contract" == $term_preference) {
        echo " checked=\"checked\"";
    }
    echo "/> ";
    echo __("termination term preference contract");
    echo "</label><br />\n\t\t<br />\n\t\t\n\t\t<div id=\"service_termination_date\" class=\"hide\">\n\t\t\t<strong class=\"title\">";
    echo __("termination date");
    echo "</strong>\n\t\t\t<input type=\"text\" name=\"TerminationDate\" value=\"\" class=\"text1 size6 datepicker\" />\n\t\t\t<br /><br />\n\t\t</div>\n\n        <strong class=\"title\">";
    echo __("termination reason");
    echo "</strong>\n        <textarea name=\"TerminationReason\" class=\"size11\"></textarea>\n        <br /><br />\n\t\t\n\t\t<strong class=\"title\">";
    echo __("termination actions");
    echo " <a id=\"service_termination_change_actions\" class=\"a1 c1 marginl normalfont\">";
    echo __("changeit");
    echo "</a></strong> \n\t\t<div id=\"service_termination_actions\">\n\t\t\t";
    echo __("termination no actions will be executed");
    echo "\t\t</div>\n\t\t<br />\n\t\t\n\t\t<label><input type=\"checkbox\" name=\"imsure\" value=\"yes\"/> ";
    echo __("terminate dialog imsure");
    echo "</label><br />\n\t\t<br />\n\t\t<p><a id=\"service_termination_btn\" class=\"button2 alt1 float_left\"><span>";
    echo __("terminate dialog btn");
    echo "</span></a></p>\n\t\t<p><a class=\"a1 c1 alt1 float_right\" onclick=\"\$('#custom_dialog_terminate_";
    echo $service_type;
    echo "').dialog('close');\"><span>";
    echo __("cancel");
    echo "</span></a></p>\n\t\t\n\t\t</form>\n\t</div>\n\t<style type=\"text/css\">\n\t#custom_dialog_terminate_";
    echo $service_type;
    echo " .table1 { margin-top: 5px;}\n\t</style>\n\t<script type=\"text/javascript\">\n\t\$(function(){\n\t\t\n\t\t\$('#custom_dialog_terminate_";
    echo $service_type;
    echo "').dialog({ autoOpen: false, width: 860, modal: true, resizable: false, beforeClose: function( event, ui ) { \$('form[name=\"'+BatchForm+'\"]').find('.BatchSelect').val(\$('form[name=\"'+BatchForm+'\"]').find('.BatchSelect option:eq(0)').val()); BatchAction = BatchForm = ''; } });\n\t\t\t\t\n\t\t// Init\n        \$(document).on('change', '.BatchSelect', function()\n        {\n            if(\$(this).val() == \"dialog:terminate_";
    echo $service_type;
    echo "\")\n            {\n            \t// Copy some needed information\n            \t\$('#custom_dialog_terminate_";
    echo $service_type;
    echo " form').attr('action', \$(this).parents('form').attr('action'));\n            \t\$('#custom_dialog_terminate_";
    echo $service_type;
    echo " input[name=\"action\"]').val(\$(this).val());\n            \t\$('#custom_dialog_terminate_";
    echo $service_type;
    echo " .ServiceIDs').html('');\n\t\t\t\t\$('form[name=\"";
    echo $form_name;
    echo "\"] .' + \$('form[name=\"";
    echo $form_name;
    echo "\"] .BatchCheck').attr('name')).each(function(index, value){\n\t\t\t\t\t\$('#custom_dialog_terminate_";
    echo $service_type;
    echo " .ServiceIDs').append(\$(this).clone());\n\t\t\t\t});\n\t\t\t\t\$('#custom_dialog_terminate_";
    echo $service_type;
    echo " .terminate_batch_confirm_text').html(\$('#custom_dialog_terminate_";
    echo $service_type;
    echo " .terminate_batch_confirm_text').html().replace('%d',\$('#custom_dialog_terminate_";
    echo $service_type;
    echo " .ServiceIDs').find('input[type=checkbox]:checked').length));\n            \t\n            \t\$('#custom_dialog_terminate_";
    echo $service_type;
    echo "').dialog('open');\n\n                \$(document).off('click', '#custom_dialog_terminate_";
    echo $service_type;
    echo " #service_termination_btn');\n                \$(document).on('click', '#custom_dialog_terminate_";
    echo $service_type;
    echo " #service_termination_btn', function()\n\t\t\t\t{\n\t\t\t\t\tif(\$('#custom_dialog_terminate_";
    echo $service_type;
    echo " input[name=\"imsure\"]:checked').val() != null)\n\t\t\t\t\t{\n\t\t\t\t\t\t\$('#custom_dialog_terminate_";
    echo $service_type;
    echo " form').submit();\n\t\t\t\t\t}\t\n\t\t\t\t});\n                \$(document).off('click', '#custom_dialog_terminate_";
    echo $service_type;
    echo " input[name=\"imsure\"]');\n                \$(document).on('click', '#custom_dialog_terminate_";
    echo $service_type;
    echo " input[name=\"imsure\"]', function()\n\t\t\t\t{\n\t\t\t\t\tif(\$('#custom_dialog_terminate_";
    echo $service_type;
    echo " input[name=\"imsure\"]:checked').val() != null)\n\t\t\t\t\t{\n\t\t\t\t\t\t\$('#custom_dialog_terminate_";
    echo $service_type;
    echo " #service_termination_btn').removeClass('button2').addClass('button1');\n\t\t\t\t\t}\n\t\t\t\t\telse\n\t\t\t\t\t{\n\t\t\t\t\t\t\$('#custom_dialog_terminate_";
    echo $service_type;
    echo " #service_termination_btn').removeClass('button1').addClass('button2');\n\t\t\t\t\t}\n\t\t\t\t});\n\n                \$(document).off('change', 'select[name=\"TerminationProcedure\"]');\n                \$(document).on('change', 'select[name=\"TerminationProcedure\"]', function()\n\t\t\t\t{\t\n\t\t\t\t\t// Show change-link\n\t\t\t\t\t\$('#custom_dialog_terminate_";
    echo $service_type;
    echo " #service_termination_change_actions').show();\n\t\t\t\t\t\n\t\t\t\t\tif(\$(this).val() != '')\n\t\t\t\t\t{\n\t\t\t\t\t\t\$.post('termination-procedures.php?page=ajax', { action: 'getTerminationProcedure', procedure_id: \$(this).val()}, function(data){\n\t\t\t\t\t\t\t// Use term preference\n\t\t\t\t\t\t\t\$('#custom_dialog_terminate_";
    echo $service_type;
    echo " input[name=\"TerminationTerm\"][value=\"' + data.term_preference + '\"]').click();\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t// Fill actions\n\t\t\t\t\t\t\t\$('#custom_dialog_terminate_";
    echo $service_type;
    echo " #service_termination_actions').html(data.html);\n\t\t\t\t\t\t}, 'json');\n\t\t\t\t\t}\n\t\t\t\t\telse\n\t\t\t\t\t{\n\t\t\t\t\t\t\$('#custom_dialog_terminate_";
    echo $service_type;
    echo " #service_termination_change_actions').show();\n\t\t\t\t\t\t\$('#custom_dialog_terminate_";
    echo $service_type;
    echo " #service_termination_actions').html('";
    echo __("termination no actions will be executed");
    echo "');\n\t\t\t\t\t}\n\t\t\t\t});\n\n                \$(document).off('click', '#custom_dialog_terminate_";
    echo $service_type;
    echo " #service_termination_change_actions');\n                \$(document).on('click', '#custom_dialog_terminate_";
    echo $service_type;
    echo " #service_termination_change_actions', function()\n\t\t\t\t{\n\t\t\t\t\t\$(this).hide();\t\n\t\t\t\t\t\n\t\t\t\t\t\$.post('termination-procedures.php?page=ajax', { action: 'getTerminationActionsTableEdit', procedure_id: \$('#custom_dialog_terminate_";
    echo $service_type;
    echo " select[name=\"TerminationProcedure\"]').val(), type: 'edit'}, function(data){\n\t\t\t\t\t\t\$('#custom_dialog_terminate_";
    echo $service_type;
    echo " #service_termination_actions').html(data);\n\t\t\t\t\t}, 'html');\n\t\t\t\t});\n\n                \$(document).off('click', '#custom_dialog_terminate_";
    echo $service_type;
    echo " input[name=\"TerminationTerm\"]');\n                \$(document).on('click', '#custom_dialog_terminate_";
    echo $service_type;
    echo " input[name=\"TerminationTerm\"]', function()\n\t\t\t\t{\n\t\t\t\t\tif(\$(this).val() == 'direct')\n\t\t\t\t\t{\n\t\t\t\t\t\t\$('#batch_confirm #service_termination_date').hide();\n\t\t\t\t\t}\n\t\t\t\t\telse if(\$(this).val() == 'contract')\n\t\t\t\t\t{\n\t\t\t\t\t\t\$('#custom_dialog_terminate_";
    echo $service_type;
    echo " #service_termination_date').hide();\n\t\t\t\t\t}\n\t\t\t\t\telse\n\t\t\t\t\t{\n\t\t\t\t\t\t\$('#custom_dialog_terminate_";
    echo $service_type;
    echo " #service_termination_date').show();\n\t\t\t\t\t}\n\t\t\t\t});\n\t\t\t\t\n\t\t\t\t\n\t\t\t\t\$('#custom_dialog_terminate_";
    echo $service_type;
    echo " select[name=\"TerminationProcedure\"]').change();\n\t \t\t}\t\n\t\t});\n\t});\n\t</script>\n\t";
}
function service_is_terminated($service_type, $service_id, $current_page_url = false)
{
    require_once "class/terminationprocedure.php";
    $termination = new Termination_Model();
    if(!$termination->show($service_type, $service_id)) {
        return false;
    }
    require_once "class/service.php";
    $service = new service();
    if(!$service->show($service_id, $service_type)) {
        return false;
    }
    $subscription_info = $service->Subscription;
    if(0 < $termination->ProcedureID) {
        $termination_procedure = new TerminationProcedure_Model();
        $termination_procedure->id = $termination->ProcedureID;
        $termination_procedure->show();
    }
    global $account;
    if(!$account) {
        require_once "class/employee.php";
        $account = new employee();
    }
    ob_start();
    echo "\t<div class=\"setting_help_box\" style=\"background-color:#F9EBEB; border:1px solid #c02e19; line-height:18px;;\">\n\t\t\n\t\t<div>\n\t\t\t<strong>";
    echo $termination->Status == "approval" ? __("termination box title - wait for approval") : __("termination box title");
    echo "</strong><br />\n\t\t\t\n\t\t\t";
    if($termination->Status == "approval") {
        echo "\t\t\n\t\t\t\t<ul class=\"list1 float_right\">\n\t\t\t\t\t<li><a class=\"ico set1 accept acceptQuestion\" onclick=\"\$('#service_approve_termination').dialog('open');\">";
        echo __("termination approve btn");
        echo "</a></li>\n\t\t\t\n\t\t\t\t\t<li style=\"padding-right:0px;\"><a class=\"ico set1 cancel declineQuestion\" onclick=\"\$('#service_reject_termination').dialog('open');\">";
        echo __("termination reject btn");
        echo "</a></li>\n\t\t\t</ul>\n\t\t\t\t";
    } else {
        echo "\t\t\n\t\t\t\t<a class=\"button1 alt1 float_right\" onclick=\"\$('#service_undo_termination').dialog('open');\"><span>";
        echo __("reactivate service btn");
        echo "</span></a>\n\t\t\t\t";
    }
    if($termination->Created != "0000-00-00 00:00:00") {
        $who = "";
        if($termination->Who == "api") {
            $who = " " . __("termination created by api");
        } elseif($termination->Who == "clientarea") {
            $who = " " . sprintf(__("termination created by clientarea"), $termination->IP ? " (" . $termination->IP . ")" : "");
        } elseif(0 < $termination->Who && is_numeric($termination->Who)) {
            require_once "class/employee.php";
            $employee = new employee();
            if($employee->show($termination->Who)) {
                $who = " " . sprintf(__("termination created by employee"), $employee->Name);
            }
        }
        echo sprintf(__("termination box description"), rewrite_date_db2site($termination->Date), __("termination term preference " . $termination->Term), rewrite_date_db2site($termination->Created) . " " . __("at") . " " . rewrite_date_db2site($termination->Created, "%H:%i") . $who) . "<br />";
        echo "<br /><strong>" . __("termination procedure") . "</strong><br />";
        echo $termination->ProcedureID ? htmlspecialchars($termination_procedure->Name) : __("termination box procedure custom");
        if($termination->Reason) {
            echo "<br /><br /><strong>" . __("termination reason") . "</strong><br />";
            echo nl2br(htmlspecialchars($termination->Reason));
        }
    } else {
        echo sprintf(__("termination box description - no created"), rewrite_date_db2site($termination->Date));
    }
    echo "\t\t</div>\n\t\t<br />\n\t\t<strong>";
    echo __("termination actions");
    if($termination->Status != "processed") {
        echo " <a id=\"termination_change_actions\" class=\"a1 c1 marginl normalfont\">";
        echo __("changeit");
        echo "</a>";
    }
    echo "</strong>\n\t\t<div id=\"termination_actions\">\n\t\t";
    if(empty($termination->TerminationActions)) {
        echo __("termination no actions will be executed");
    } else {
        $termination_action = new Action_Model();
        $config = $termination_action->getTableConfig();
        $config["parameters"]["termination_id"] = $termination->id;
        $config["hide_cols"] = ["debtor", "service", "servicetype"];
        if($current_page_url === false) {
            global $current_page_url;
        }
        if($current_page_url) {
            $config["redirect_url"] = $current_page_url;
        }
        if($termination->Status == "processed") {
            $config["actions"] = [];
        } elseif(!$account->checkUserRights($service_type, "edit")) {
            $config["actions"] = [];
        }
        generate_table("termination_actions", $config);
    }
    echo "\t\t</div>\n\t</div>\n\t";
    if($termination->Status == "approval") {
        echo "\t\t<div id=\"service_approve_termination\" title=\"";
        echo __("service approve termination title");
        echo "\">\n\t\t\t<form name=\"approve_termination_service_form\" method=\"post\" action=\"services.php?page=terminations\">\n\t\t\t<input type=\"hidden\" name=\"action\" value=\"approve_termination\" />\n\t\t\t<input type=\"hidden\" name=\"ServiceType\" value=\"";
        echo $service_type;
        echo "\" />\n\t\t\t<input type=\"hidden\" name=\"ServiceID\" value=\"";
        echo $service_id;
        echo "\" />\n\t\t\t";
        echo __("service approve termination intro");
        echo "\t\t\t<br /><br />\n\t\t\t<p><a class=\"button1 alt1 float_left\" onclick=\"\$('form[name=approve_termination_service_form]').submit();\"><span>";
        echo __("termination approve btn");
        echo "</span></a></p>\n\t\t\t<p><a class=\"a1 c1 alt1 float_right\" onclick=\"\$('#service_approve_termination').dialog('close');\"><span>";
        echo __("cancel");
        echo "</span></a></p>\n\t\t\t\n\t\t\t</form>\n\t\t</div>\n\t\t<div id=\"service_reject_termination\" title=\"";
        echo __("service reject termination title");
        echo "\">\n\t\t\t<form name=\"reject_termination_service_form\" method=\"post\" action=\"services.php?page=terminations\">\n\t\t\t<input type=\"hidden\" name=\"action\" value=\"reject_termination\" />\n\t\t\t<input type=\"hidden\" name=\"ServiceType\" value=\"";
        echo $service_type;
        echo "\" />\n\t\t\t<input type=\"hidden\" name=\"ServiceID\" value=\"";
        echo $service_id;
        echo "\" />\n\t\t\t";
        echo __("service reject termination intro");
        echo "\t\t\t<br /><br />\n\t\t\t<p><a class=\"button1 alt1 float_left\" onclick=\"\$('form[name=reject_termination_service_form]').submit();\"><span>";
        echo __("termination reject btn");
        echo "</span></a></p>\n\t\t\t<p><a class=\"a1 c1 alt1 float_right\" onclick=\"\$('#service_reject_termination').dialog('close');\"><span>";
        echo __("cancel");
        echo "</span></a></p>\n\t\t\t\n\t\t\t</form>\n\t\t</div>\n\t\t";
    } else {
        echo "\t\t<div id=\"service_undo_termination\" title=\"";
        echo __("reactivate service btn");
        echo "\">\n\t\t\t<form name=\"undo_terminate_service_form\" method=\"post\" action=\"services.php?page=terminations\">\n\t\t\t<input type=\"hidden\" name=\"action\" value=\"reactivate\" />\n\t\t\t<input type=\"hidden\" name=\"ServiceType\" value=\"";
        echo $service_type;
        echo "\" />\n\t\t\t<input type=\"hidden\" name=\"ServiceID\" value=\"";
        echo $service_id;
        echo "\" />\n\t\t\t";
        echo __("reactivate service dialog intro") . "<br />";
        if(0 < $subscription_info->Identifier) {
            global $array_periodic;
            global $array_periodesMV;
            echo "\t\t\t\t<br />\n\t\t\t\t<strong class=\"title\">";
            echo __("period to invoice");
            echo " (";
            echo __("per") . " " . (1 < $subscription_info->Periods ? $subscription_info->Periods . " " . $array_periodesMV[$subscription_info->Periodic] : $array_periodic[$subscription_info->Periodic]);
            echo ")</strong>\n\t\t\t\t<input type=\"text\" tabindex=\"-1\" name=\"subscription[StartPeriod]\"  value=\"";
            echo $subscription_info->StartPeriod;
            echo "\" class=\"text1 size6 datepicker\" />\n\t\t\t\t&nbsp;&nbsp;&nbsp;";
            echo __("till");
            echo "&nbsp;&nbsp;&nbsp;\n\t\t\t\t<input type=\"text\" value=\"";
            echo $subscription_info->EndPeriod;
            echo "\" name=\"subscription[EndPeriod]\" class=\"text1 size6\" disabled=\"disabled\" style=\"color: #999;;\"/>\n\t\t\t\t<br />\n\t\t\t\t";
        }
        echo "<br />" . __("reactivate service dialog warning");
        echo "\t\n\t\t\t<br /><br />\n\t\t\t<label><input type=\"checkbox\" name=\"imsure\" value=\"yes\"/> ";
        echo __("reactivate service dialog imsure");
        echo "</label><br />\n\t\t\t<br />\n\t\t\t<p><a id=\"service_undo_termination_btn\" class=\"button2 alt1 float_left\"><span>";
        echo __("reactivate service btn");
        echo "</span></a></p>\n\t\t\t<p><a class=\"a1 c1 alt1 float_right\" onclick=\"\$('#service_undo_termination').dialog('close');\"><span>";
        echo __("cancel");
        echo "</span></a></p>\n\t\t\t\n\t\t\t</form>\n\t\t</div>\n\t\t";
    }
    echo "\t<style type=\"text/css\">\n\t.setting_help_box table {margin-top:0px; background-color: #fff;}\n\t</style>\n\t<script type=\"text/javascript\">\n\t\$(function(){\n\t\t\$('#termination_change_actions').click(function(){\n\t\t\t\$(this).hide();\t\n\t\t\t\n\t\t\t\$.post('services.php?page=ajax', { action: 'getActionsTableEdit', termination_id: ";
    echo $termination->id;
    echo ", type: 'edit'}, function(data){\n\t\t\t\t\$('#termination_actions').html(data);\n\t\t\t}, 'html');\n\t\t});\n\t\t\n\t\t// dialogs\n\t\t\$('#service_approve_termination').dialog({ autoOpen: false, width: 600, modal: true, resizable: false });\n\t\t\$('#service_reject_termination').dialog({ autoOpen: false, width: 600, modal: true, resizable: false });\n\t\t\$('#service_undo_termination').dialog({ autoOpen: false, width: 600, modal: true, resizable: false });\n\t\t\n\t\t\$('#service_undo_termination input[name=\"imsure\"]').click(function()\n\t\t{\n\t\t\tif(\$('#service_undo_termination input[name=\"imsure\"]:checked').val() != null)\n\t\t\t{\n\t\t\t\t\$('#service_undo_termination_btn').removeClass('button2').addClass('button1');\n\t\t\t}\n\t\t\telse\n\t\t\t{\n\t\t\t\t\$('#service_undo_termination_btn').removeClass('button1').addClass('button2');\n\t\t\t}\n\t\t});\n\t\t\n\t\t\$('#service_undo_termination_btn').click(function()\n\t\t{\n\t\t\tif(\$('#service_undo_termination input[name=\"imsure\"]:checked').val() != null)\n\t\t\t{\n\t\t\t\tdocument.undo_terminate_service_form.submit();\n\t\t\t}\t\n\t\t});\n\t\t\n\t\t";
    if(0 < $subscription_info->Identifier) {
        echo "\t\t\t\$('input[name=\"subscription[StartPeriod]\"]').change(function(){\n\t\t\t\tresult = changePeriodCalc('";
        echo $subscription_info->Periodic;
        echo "', '";
        echo $subscription_info->Periods;
        echo "', \$(this).val());\n\t\t\t\t\$('input[name=\"subscription[StartPeriod]\"]').val(result[0]);\n\t\t\t\t\$('input[name=\"subscription[EndPeriod]\"]').val(result[1]);\n\t\t\t});\n\t\t\t";
    }
    echo "\t});\n\t</script>\n\t<br />\n\t";
    $html = ob_get_clean();
    return $html;
}
function createAutoComplete($search_type, $input_field_name, $current_value = "", $options = [])
{
    global $autocomplete_div_count;
    $autocomplete_div_count["total"]++;
    $autocomplete_div_count[$search_type] = isset($autocomplete_div_count[$search_type]) ? $autocomplete_div_count[$search_type]++ : 1;
    echo "<div class=\"autocomplete_search_input\"><input type=\"text\" \n\t\t\t\t\t\tname=\"AutoCompleteSearch[]\" \n\t\t\t\t\t\tdata-type=\"" . $search_type . "\"\n\t\t\t\t\t\tdata-inputfieldname=\"" . $input_field_name . "\" \n\t\t\t\t\t\tdata-label=\"" . $current_value . "\"\n\t\t\t\t\t\tdata-filter=\"" . (isset($options["filter"]) && $options["filter"] ? $options["filter"] : "") . "\"\n\t\t\t\t\t\tplaceholder=\"" . __("autocomplete placeholder for type " . $search_type) . "\" \n\t\t\t\t\t\tautocomplete=\"off\"\n\t\t\t\t\t\tclass=\"text1" . (isset($options["class"]) ? " " . $options["class"] : "") . "\"\n\t\t\t\t\t\tvalue=\"" . $current_value . "\"\n\t\t\t\t\t\t/><span class=\"autocomplete_search_input_arrow\"></span><span class=\"autocomplete_search_input_loader\"></span></div>";
    if($autocomplete_div_count[$search_type] == 1) {
        echo "\t\t<div id=\"div_for_autocomplete_";
        echo $search_type;
        echo "\" data-type=\"";
        echo $search_type;
        echo "\" data-group_filter=\"";
        echo defined("SHOW_" . strtoupper($search_type) . "_SEARCH_GROUPS") && constant("SHOW_" . strtoupper($search_type) . "_SEARCH_GROUPS") == "yes" ? "yes" : "no";
        echo "\" data-return=\"";
        echo isset($options["return_type"]) && in_array($options["return_type"], ["id", "code"]) ? $options["return_type"] : "id";
        echo "\" class=\"autocomplete_search\">\n\t\t\t<div class=\"autocomplete_header\">\n\t\t\t\t";
        if(in_array($search_type, ["product"])) {
            echo "<div class=\"a1 c1 smallfont item_reset\">";
            echo __("autocomplete item reset " . $search_type);
            echo "</div>";
        }
        echo "\t\t\t\t\n\t\t\t\t<div class=\"toptext group_label hide\">";
        echo __("autocomplete choose " . $search_type . "group");
        echo "</div>\n\t\t\t\t<div class=\"toptext all_label hide\">";
        echo __("autocomplete choose " . $search_type);
        echo "</div>\n\t\t\t\t<div class=\"toptext goback hide\"><i></i>";
        echo __("back");
        echo "</div>\n\t\t\t\t\n\t\t\t</div>\n\t\t\t<div class=\"autocomplete_search_results\"></div>\n\t\t\t\n\t\t\t<div class=\"autocomplete_show_group_by\">\n\t\t\t\t<div class=\"autocomplete_results_limited hide\" style=\"float:right;padding-right:10px;\"></div>\n\t\t\t\t\n\t\t\t\t<label><input type=\"checkbox\" name=\"AutoCompleteShowGroup[]\" class=\"group_filter\" value=\"true\" style=\"width:20px;margin:0px;\" ";
        echo defined("SHOW_" . strtoupper($search_type) . "_SEARCH_GROUPS") && constant("SHOW_" . strtoupper($search_type) . "_SEARCH_GROUPS") == "yes" ? "checked=\"checked\"" : "";
        echo " /> ";
        echo __("autocomplete order by " . $search_type . "group");
        echo "</label>\n\n</div>\n\t\t</div>\n\t\t";
    }
}
function buildAttachmentArray($attachments)
{
    $resultAttachment = [];
    if(!empty($attachments)) {
        $attachment = new attachment();
        foreach ($attachments as $key => $AttachemtId) {
            $attachmentInfo = $attachment->getAttachmentInfo($AttachemtId);
            if($attachmentInfo !== false) {
                $resultAttachment[] = $attachmentInfo;
            }
        }
    }
    return $resultAttachment;
}
function url_generator($controller, $action = false, $reference = [], $parameters = [])
{
    $url = strtolower($controller) . ".php";
    $get_array = [];
    if($action) {
        $get_array["page"] = $action;
    }
    if(!empty($reference)) {
        foreach ($reference as $k => $v) {
            $get_array[$k] = $v;
        }
    }
    if(!empty($parameters)) {
        foreach ($parameters as $k => $v) {
            $get_array[$k] = $v;
        }
    }
    return $url . (!empty($get_array) ? "?" . http_build_query($get_array) : "");
}
function generate_message_box()
{
    $args = func_get_args();
    $message_err = [];
    $message_war = [];
    $message_suc = [];
    if(!class_exists("global_flash_message")) {
        class global_flash_message
        {
        }
    }
    $flashMessage = new global_flash_message();
    if(isset($_SESSION["flashMessage"]["Error"])) {
        $flashMessage->Error = $_SESSION["flashMessage"]["Error"];
        unset($_SESSION["flashMessage"]["Error"]);
    }
    if(isset($_SESSION["flashMessage"]["Warning"])) {
        $flashMessage->Warning = $_SESSION["flashMessage"]["Warning"];
        unset($_SESSION["flashMessage"]["Warning"]);
    }
    if(isset($_SESSION["flashMessage"]["Success"])) {
        $flashMessage->Success = $_SESSION["flashMessage"]["Success"];
        unset($_SESSION["flashMessage"]["Success"]);
    }
    $args[] = $flashMessage;
    foreach ($args as $arg) {
        if(is_object($arg) && !empty($arg->Error)) {
            $message_err = array_merge($message_err, $arg->Error);
        }
        if(is_object($arg) && !empty($arg->Warning)) {
            $message_war = array_merge($message_war, $arg->Warning);
        }
        if(is_object($arg) && !empty($arg->Success)) {
            $message_suc = array_merge($message_suc, $arg->Success);
        }
    }
    $message_html = "";
    if(!empty($message_err)) {
        $message_html .= "<div class=\"message_box error center margin_bottom_20px\">" . implode("<br />", $message_err) . "</div>";
    }
    if(!empty($message_war)) {
        $message_html .= "<div class=\"message_box warning center margin_bottom_20px\">" . implode("<br />", $message_war) . "</div>";
    }
    if(!empty($message_suc)) {
        $message_html .= "<div class=\"message_box success center margin_bottom_20px\">" . implode("<br />", $message_suc) . "</div>";
    }
    return $message_html;
}
function generate_flash_message()
{
    $args = func_get_args();
    foreach ($args as $arg) {
        if(is_object($arg) && !empty($arg->Error)) {
            $_SESSION["flashMessage"]["Error"] = isset($_SESSION["flashMessage"]["Error"]) ? array_merge($_SESSION["flashMessage"]["Error"], $arg->Error) : $arg->Error;
        }
        if(is_object($arg) && !empty($arg->Warning)) {
            $_SESSION["flashMessage"]["Warning"] = isset($_SESSION["flashMessage"]["Warning"]) ? array_merge($_SESSION["flashMessage"]["Warning"], $arg->Warning) : $arg->Warning;
        }
        if(is_object($arg) && !empty($arg->Success)) {
            $_SESSION["flashMessage"]["Success"] = isset($_SESSION["flashMessage"]["Success"]) ? array_merge($_SESSION["flashMessage"]["Success"], $arg->Success) : $arg->Success;
        }
    }
}
function internalAPI($controller, $action, $params)
{
    if(!defined("API_DIR")) {
        define("API_DIR", "apiv2");
    }
    include_once "apiv2/hostfactapi.class.php";
    HostFact_API::setRequestMethod("internal");
    HostFact_API::setResponseType("RAW");
    HostFact_API::$_request_data["internal"] = [];
    HostFact_API::$_request_data["internal"]["controller"] = $controller;
    HostFact_API::$_request_data["internal"]["action"] = $action;
    if(!empty($params)) {
        foreach ($params as $key => $value) {
            HostFact_API::$_request_data["internal"][$key] = $value;
        }
    }
    HostFact_API::createLog("internalAPI");
    try {
        HostFact_API::routeRequest();
    } catch (InternalAPIException $e) {
        if($e->getMessage() == "output") {
            return HostFact_API::$_response_array;
        }
    }
}
function formatMB($mbytes, $pow = false)
{
    $units = ["B", "KB", "MB", "GB", "TB"];
    $bytes = max(intval($mbytes) * 1024 * 1024, 0);
    if($pow === false) {
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $show_unit = true;
    } else {
        $pow = trim(substr($pow, -2));
        $pow = array_search($pow, $units);
        $show_unit = false;
    }
    $pow = max(2, $pow);
    $bytes /= pow(1024, $pow);
    $return = showNumber(round($bytes, 2));
    return $return . ($show_unit ? " " . $units[$pow] : "");
}
function logFailedLoginAttempt($type, $ip, $username = "")
{
    $json_whitelist = IP_WHITELIST && json_decode(htmlspecialchars_decode(IP_WHITELIST), true) ? json_decode(htmlspecialchars_decode(IP_WHITELIST), true) : [];
    if(!empty($json_whitelist)) {
        foreach ($json_whitelist as $_whitelist) {
            if(ip_in_range($ip, [$_whitelist["IP"]])) {
                return NULL;
            }
        }
    }
    Database_Model::getInstance()->insert("HostFact_FailedLoginAttempts", ["DateTime" => ["RAW" => "NOW()"], "IP" => $ip, "UserName" => $username, "Type" => $type])->execute();
    $result = Database_Model::getInstance()->getOne("HostFact_FailedLoginAttempts", ["COUNT(`id`) as Count"])->where("IP", $ip)->execute();
    if($result && 10 <= $result->Count) {
        unset($_SESSION["IP_BLACKLIST_CHECKED"]);
        $json_blacklist = IP_BLACKLIST && json_decode(htmlspecialchars_decode(IP_BLACKLIST), true) ? json_decode(htmlspecialchars_decode(IP_BLACKLIST), true) : [];
        foreach ($json_blacklist as $_ip_blacklist) {
            if(ip_in_range($ip, [$_ip_blacklist["IP"]])) {
                return NULL;
            }
        }
        $json_blacklist[] = ["DateTime" => date("Y-m-d H:i:s"), "IP" => $ip, "Who" => 0];
        Database_Model::getInstance()->update("HostFact_Settings", ["Value" => json_encode($json_blacklist)])->where("Variable", "IP_BLACKLIST")->execute();
    }
}
function clearFailedLoginAttempts($ip = false, $username = false, $type = "backoffice")
{
    $clear_by = [];
    if($ip) {
        $clear_by = ["IP", $ip];
    }
    if($username) {
        $clear_by = ["UserName", $username];
    }
    Database_Model::getInstance()->delete("HostFact_FailedLoginAttempts")->orWhere([$clear_by, ["DateTime", ["<=" => date("Y-m-d H:i:s", strtotime("-30 days"))]]])->where("Type", $type)->execute();
}
function ip_in_range($ip, $IPs)
{
    if(in_array($ip, $IPs)) {
        return true;
    }
    if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
        $ip = inet_pton($ip);
        foreach ($IPs as $cidrnet) {
            if(strpos($cidrnet, ":") === false) {
            } else {
                $binaryip = inet_to_bits($ip);
                list($net, $maskbits) = explode("/", $cidrnet);
                $net = inet_pton($net);
                $binarynet = inet_to_bits($net);
                $ip_net_bits = substr($binaryip, 0, $maskbits);
                $net_bits = substr($binarynet, 0, $maskbits);
                if($ip_net_bits && $net_bits && $ip_net_bits == $net_bits) {
                    return true;
                }
            }
        }
    } elseif(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        foreach ($IPs as $cidrnet) {
            list($net_addr, $maskbits) = explode("/", $cidrnet);
            if(0 < $maskbits) {
                $ip_binary_string = sprintf("%032b", ip2long($ip));
                $net_binary_string = sprintf("%032b", ip2long($net_addr));
                if(substr_compare($ip_binary_string, $net_binary_string, 0, $maskbits) === 0) {
                    return true;
                }
            }
        }
    }
    return false;
}
function inet_to_bits($inet)
{
    $unpacked = unpack("A16", $inet);
    $unpacked = str_split($unpacked[1]);
    $binaryip = "";
    foreach ($unpacked as $char) {
        $binaryip .= str_pad(decbin(ord($char)), 8, "0", STR_PAD_LEFT);
    }
    return $binaryip;
}
function normalize($string)
{
    if(is_array($string)) {
        foreach ($string as $k => $v) {
            $string[$k] = normalize($v);
        }
        return $string;
    } elseif(is_object($string)) {
        foreach ($string as $k => $v) {
            $string->{$k} = normalize($v);
        }
        return $string;
    } else {
        return htmlspecialchars($string);
    }
}
function calculateFinancialTotals($vat_calc_method, $elements = [], $disc_percentage = 0)
{
    $used_rates = [];
    $totals = ["AmountExcl" => 0, "AmountIncl" => 0];
    $amount_incl_or_excl = $vat_calc_method == "incl" ? "AmountIncl" : "AmountExcl";
    if(is_array($elements)) {
        foreach ($elements as $k => $v) {
            if(is_numeric($k)) {
                $taxKey = (string) round($v["TaxPercentage"], 6);
                if(!isset($used_rates[$taxKey])) {
                    $used_rates[$taxKey][$amount_incl_or_excl] = 0;
                }
                $used_rates[$taxKey][$amount_incl_or_excl] += $v[$amount_incl_or_excl];
            }
        }
    }
    if($vat_calc_method == "incl") {
        foreach ($used_rates as $_rate => $_amounts) {
            $_amounts["AmountIncl"] = round($_amounts["AmountIncl"] - $_amounts["AmountIncl"] * $disc_percentage, 2);
            $_amounts["AmountTax"] = round($_amounts["AmountIncl"] / (1 + $_rate) * $_rate, 2);
            $_amounts["AmountExcl"] = $_amounts["AmountIncl"] - $_amounts["AmountTax"];
            $totals["AmountExcl"] -= $_amounts["AmountTax"];
            $totals["AmountIncl"] += $_amounts["AmountIncl"];
            $used_rates[$_rate] = $_amounts;
        }
        $totals["AmountIncl"] = round($totals["AmountIncl"], 2);
        $totals["AmountExcl"] = round($totals["AmountExcl"] + $totals["AmountIncl"], 2);
    } else {
        foreach ($used_rates as $_rate => $_amounts) {
            $_amounts["AmountExcl"] = round($_amounts["AmountExcl"] - $_amounts["AmountExcl"] * $disc_percentage, 2);
            $_amounts["AmountTax"] = round($_amounts["AmountExcl"] * $_rate, 2);
            $_amounts["AmountIncl"] = $_amounts["AmountExcl"] + $_amounts["AmountTax"];
            $totals["AmountIncl"] += $_amounts["AmountTax"];
            $totals["AmountExcl"] += $_amounts["AmountExcl"];
            $used_rates[$_rate] = $_amounts;
        }
        $totals["AmountExcl"] = round($totals["AmountExcl"], 2);
        $totals["AmountIncl"] = round($totals["AmountExcl"] + $totals["AmountIncl"], 2);
    }
    return ["totals" => $totals, "used_rates" => $used_rates];
}
function getLineAmount($vatCalcMethod, $priceExcl, $periods, $number, $taxPercentage, $discountPercentage)
{
    $line_amount = [];
    $offset = 0 < $priceExcl * $periods * $number ? 0 : 0;
    if($vatCalcMethod == "incl") {
        $line_amount["incl"] = round($priceExcl * round(1 + $taxPercentage, 4) * $periods * $number + $offset, 2) - round($priceExcl * round(1 + $taxPercentage, 4) * $periods * $number * round($discountPercentage, 4) + $offset, 2);
        $line_amount["excl"] = round($line_amount["incl"] / round(1 + $taxPercentage, 4), 2);
    } else {
        $line_amount["excl"] = round($priceExcl * $periods * $number + $offset, 2) - round($priceExcl * $periods * $number * round((double) $discountPercentage, 4) + $offset, 2);
        $line_amount["incl"] = round($line_amount["excl"] * round(1 + $taxPercentage, 4), 2);
    }
    return $line_amount;
}
function HostFactErrorHandler($errno, $errstr, $errfile, $errline)
{
    if($errno == 8) {
        return NULL;
    }
    if(defined("IS_DEMO") && IS_DEMO) {
        return NULL;
    }
    if(function_exists("debug_backtrace")) {
        $backtrace = debug_backtrace();
        $html = "<pre>";
        array_shift($backtrace);
        foreach ($backtrace as $i => $l) {
            $html .= "[" . $i . "] <b>" . (isset($l["class"]) ? $l["class"] : "") . (isset($l["type"]) ? $l["type"] : "") . (isset($l["function"]) ? $l["function"] : "") . "</b>";
            if($l["file"]) {
                $html .= " in <b>" . $l["file"] . "</b>";
            }
            if($l["line"] && 0 < $l["line"]) {
                $html .= " on line <b>" . $l["line"] . "</b>";
            }
            $html .= "\n";
        }
        $html .= "</pre>";
        fatal_error("[" . $errno . "] " . $errstr . " in " . $errfile, $html);
    }
}
function checkFatalErrors()
{
    if(class_exists("hostfact_module", false) && hostfact_module::$module_error_handler === true) {
        return NULL;
    }
    if(defined("IS_DEMO") && IS_DEMO) {
        return NULL;
    }
    $error = error_get_last();
    if($error === NULL || !in_array($error["type"], [1, 4, 16, 256])) {
        return NULL;
    }
    $error["message"] = nl2br(str_replace($_SERVER["DOCUMENT_ROOT"], "", $error["message"]));
    $error["file"] = str_replace($_SERVER["DOCUMENT_ROOT"], "", $error["file"]);
    fatal_error("PHP fatal error", $error["message"] . " in " . $error["file"] . ":" . $error["line"]);
}
function countryCodeToLong($country_code)
{
    global $array_country;
    $result_db = Database_Model::getInstance()->getOne("HostFact_Settings_Countries")->where("Visible", "yes")->where("CountryCode", $country_code)->execute();
    if(empty($result_db)) {
        return $array_country[$country_code];
    }
    if(strtolower($country_code) == "nl" || strtolower($country_code) == "be") {
        return $result_db->nl_NL;
    }
    return $result_db->en_EN;
}
function countryCodeTranslatedString($country_code, $string, $namespace = "hostfact")
{
    if(defined("IS_INTERNATIONAL") && IS_INTERNATIONAL === true) {
        $country_code = "other";
    }
    if(strtolower($country_code) == "nl" || strtolower($country_code) == "be") {
        return __($string, $namespace);
    }
    global $LANG;
    include "includes/language/en_EN/en_EN.php";
    $translated_string = __($string, $namespace);
    include "includes/language/" . LANGUAGE_CODE . "/" . LANGUAGE_CODE . ".php";
    return $translated_string;
}
function phoneNumberLink($phoneNumber)
{
    $phoneNumberDigits = preg_replace("/[^+0-9]/i", "", $phoneNumber);
    if(!$phoneNumberDigits) {
        return $phoneNumber;
    }
    return "<a href=\"tel:" . $phoneNumberDigits . "\" class=\"a1\">" . $phoneNumber . "</a>";
}
function rmdirWithContent($dir)
{
    if(is_dir($dir)) {
        $objects = @scandir($dir);
        foreach ($objects as $object) {
            if($object != "." && $object != "..") {
                if(is_dir($dir . DIRECTORY_SEPARATOR . $object) && !is_link($dir . "/" . $object)) {
                    rmdirWithContent($dir . DIRECTORY_SEPARATOR . $object);
                } else {
                    @unlink($dir . DIRECTORY_SEPARATOR . $object);
                }
            }
        }
        @rmdir($dir);
    }
}

?>