<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class InternalAPIException extends Exception
{
}
class HostFact_API
{
    public static $_backup_vars;
    public static $_request_data;
    public static $_request_method = "POST";
    public static $_response_type = "JSON";
    public static $_object;
    public static $_controller_name;
    public static $_action_name;
    public static $_meta_data = [];
    public static $_object_name_plural;
    public static $_object_name_singular;
    public static $_is_transaction = false;
    public static $_response_array;
    public static $_error_counter;
    public static $_error_messages;
    public static $_api_call_log_id = false;
    public static function parseRawRequestData()
    {
        if(!empty($_GET)) {
            foreach ($_GET as $key => $value) {
                self::$_request_data["GET"][$key] = $value;
            }
        }
        if(!empty($_POST)) {
            foreach ($_POST as $key => $value) {
                self::$_request_data["POST"][$key] = $value;
            }
        }
        $_GET = $_POST = $_REQUEST = [];
    }
    public static function getRequestParameter($key)
    {
        $value = false;
        if(isset(self::$_request_data[self::$_request_method][$key])) {
            $value = self::$_request_data[self::$_request_method][$key];
        } elseif(isset(self::$_request_data[self::$_request_method][strtolower($key)])) {
            $value = self::$_request_data[self::$_request_method][strtolower($key)];
        }
        return $value;
    }
    public static function setRequestMethod($method)
    {
        if(!in_array($method, ["GET", "POST", "internal"])) {
            return false;
        }
        if($method == "internal") {
            self::$_backup_vars[] = ["request_data" => self::$_request_data, "request_method" => self::$_request_method, "response_type" => self::$_response_type, "controller_name" => self::$_controller_name, "action_name" => self::$_action_name, "meta_data" => self::$_meta_data, "object_name_plural" => self::$_object_name_plural, "object_name_singular" => self::$_object_name_singular, "is_transaction" => self::$_is_transaction, "api_call_log_id" => self::$_api_call_log_id];
            self::$_request_data = [];
            self::$_request_method = "POST";
            self::$_response_type = "JSON";
            self::$_object = new stdClass();
            self::$_controller_name = "";
            self::$_action_name = "";
            self::$_meta_data = [];
            self::$_object_name_plural = "";
            self::$_object_name_singular = "";
            self::$_is_transaction = false;
            self::$_response_array = [];
            self::$_error_counter = 0;
            self::$_error_messages = [];
            self::$_api_call_log_id = false;
        }
        self::$_request_method = $method;
        return true;
    }
    public static function getRequestMethod()
    {
        return self::$_request_method;
    }
    public static function setResponseType($type)
    {
        if(!in_array($type, ["JSON", "XML", "RAW"])) {
            return false;
        }
        self::$_response_type = $type;
        return true;
    }
    public static function getResponseType()
    {
        return self::$_response_type;
    }
    public static function routeRequest()
    {
        $controller_name = self::getRequestParameter("controller");
        $action_name = self::getRequestParameter("action");
        $controller_name = strtolower($controller_name);
        if(!$controller_name || preg_match("/[^a-z0-9]+/i", $controller_name) === false) {
            self::parseError("Invalid controller", true);
            return false;
        }
        global $additional_product_types;
        $_is_module = isset($additional_product_types[$controller_name]) ? true : false;
        if($_is_module && !@file_exists("3rdparty/modules/products/" . $controller_name . "/controllers/" . $controller_name . "_api_controller.php") || !$_is_module && !@file_exists(API_DIR . "/controllers/" . $controller_name . "_api_controller.php")) {
            self::parseError("Invalid controller", true);
            return false;
        }
        require_once API_DIR . "/controllers/api_controller.php";
        if($_is_module) {
            require_once "3rdparty/modules/products/" . $controller_name . "/controllers/" . $controller_name . "_api_controller.php";
        } else {
            require_once API_DIR . "/controllers/" . $controller_name . "_api_controller.php";
        }
        self::$_controller_name = $controller_name;
        $namespace = "modules\\products\\" . self::$_controller_name;
        if(class_exists($namespace . "\\" . self::$_controller_name . "_api_controller")) {
            $instance_name = $namespace . "\\" . self::$_controller_name . "_api_controller";
        } else {
            $instance_name = self::$_controller_name . "_api_controller";
        }
        self::$_object = new $instance_name();
        $action_name = strtolower($action_name);
        if(!$action_name || preg_match("/[^a-z0-9]+/i", $action_name) === false) {
            self::parseError("Invalid action", true);
            return false;
        }
        if(!method_exists(self::$_object, $action_name . "_api_action")) {
            self::parseError("Invalid action", true);
            return false;
        }
        self::$_action_name = $action_name;
        return self::$_object->{self::$_action_name . "_api_action"}();
    }
    public static function createLog($ip = false)
    {
        if(API_LOG_TYPE != "none") {
            $request = self::$_request_data[self::$_request_method];
            array_walk_recursive($request, function (&$item, $key) {
                if(strpos($key, "Password") !== false) {
                    $item = "***************";
                }
            });
            $request = HostFact_API::filterBase64FromLog($request);
            $call_id = Database_Model::getInstance()->insert("HostFact_API_Calls", ["DateTime" => ["RAW" => "NOW()"], "Controller" => self::getRequestParameter("controller"), "Action" => self::getRequestParameter("action"), "Input" => json_encode($request), "ResponseType" => "", "Response" => "", "IP" => $ip !== false ? $ip : $_SERVER["REMOTE_ADDR"]])->execute();
            self::$_api_call_log_id = $call_id;
        }
    }
    public static function checkLogin()
    {
        $APIkey = self::getRequestParameter("api_key");
        if($APIkey == "") {
            self::parseError("No api_key provided", true);
            return false;
        }
        self::createLog();
        $result = Database_Model::getInstance()->get("HostFact_Settings", ["Variable", "Value"])->where("Variable", ["IN" => ["API_ACTIVE", "API_KEY", "API_ACCESS"]])->execute();
        $api_settings = [];
        foreach ($result as $tmp_setting) {
            $api_settings[$tmp_setting->Variable] = $tmp_setting->Value;
        }
        if(!isset($api_settings["API_ACTIVE"]) || $api_settings["API_ACTIVE"] != "yes") {
            self::parseError("API access has been disabled", true);
            return false;
        }
        if(!self::checkIPAccess($api_settings)) {
            self::parseError("IP " . $_SERVER["REMOTE_ADDR"] . " has no access to API", true);
            return false;
        }
        if(!isset($api_settings["API_KEY"]) || $api_settings["API_KEY"] != $APIkey) {
            self::parseError("API key is invalid", true);
            return false;
        }
        if(in_array(self::getRequestParameter("action"), ["show", "list"])) {
            return true;
        }
        if(in_array(self::getRequestParameter("controller"), ["product", "group", "ticket", "attachment", "debtor", "creditor"])) {
            return true;
        }
        $start_queue = time();
        while (hasApiCollision() === true) {
            if(5 < time() - $start_queue) {
                self::parseError(sprintf(__("api too many concurrent calls"), 5), true);
                return false;
            }
            usleep(200000);
        }
        return true;
    }
    private static function checkIPAccess($api_settings)
    {
        $ip = $_SERVER["REMOTE_ADDR"];
        $IPs = explode(";", $api_settings["API_ACCESS"]);
        if(in_array($ip, explode(";", $api_settings["API_ACCESS"]))) {
            return true;
        }
        if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            foreach ($IPs as $cidrnet) {
                if(strpos($cidrnet, ":") === false) {
                } else {
                    $ip = inet_pton($_SERVER["REMOTE_ADDR"]);
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
    public static function setMetaData($key, $value)
    {
        self::$_meta_data[$key] = $value;
    }
    public static function setObjectNames($plural, $singular)
    {
        self::$_object_name_plural = $plural;
        self::$_object_name_singular = $singular;
    }
    public static function parseResponse($data = [], $isList = false)
    {
        if(self::hasErrors()) {
            self::__newResponse("error");
            self::__addResponse("errors", self::$_error_messages);
            self::__outputResponse();
        } else {
            self::__newResponse("success");
            foreach (self::$_meta_data as $key => $value) {
                self::__addResponse(strtolower($key), $value);
            }
            if(!empty($data)) {
                self::__addResponse($isList === true ? self::$_object_name_plural : self::$_object_name_singular, $data);
            }
            self::__outputResponse();
        }
    }
    public static function parseError($message, $end_script = false)
    {
        if(!is_array($message)) {
            $message = [$message];
        }
        foreach ($message as $err) {
            self::$_error_counter += 1;
            self::$_error_messages[] = $err;
        }
        if(empty($message) && !empty($_SESSION["flashMessage"]["Error"])) {
            foreach ($_SESSION["flashMessage"]["Error"] as $err) {
                self::$_error_counter += 1;
                self::$_error_messages[] = $err;
            }
        }
        if($end_script === true) {
            if(self::$_is_transaction === true) {
                self::rollBack();
            }
            self::parseResponse();
        }
    }
    public static function parseSuccess($message, $end_script = false)
    {
        if(!is_array($message)) {
            $message = [$message];
        }
        if(isset(self::$_meta_data["success"]) && is_array(self::$_meta_data["success"])) {
            $message = array_merge($message, self::$_meta_data["success"]);
        }
        self::setMetaData("success", $message);
        if($end_script === true) {
            if(self::$_is_transaction === true) {
                self::commit();
            }
            self::parseResponse();
        }
    }
    public static function parseWarning($message, $end_script = false)
    {
        if(!is_array($message)) {
            $message = [$message];
        }
        if(isset(self::$_meta_data["warning"]) && is_array(self::$_meta_data["warning"])) {
            $message = array_merge($message, self::$_meta_data["warning"]);
        }
        self::setMetaData("warning", $message);
        if($end_script === true) {
            self::parseResponse();
        }
    }
    public static function hasErrors()
    {
        return 0 < self::$_error_counter ? true : false;
    }
    public static function beginTransaction()
    {
        if(self::$_is_transaction === false) {
            Database_Model::getInstance()->beginTransaction();
            self::$_is_transaction = true;
        }
    }
    public static function commit()
    {
        if(self::$_is_transaction === true) {
            Database_Model::getInstance()->commit();
            self::$_is_transaction = false;
        }
    }
    public static function rollBack()
    {
        if(self::$_is_transaction === true) {
            Database_Model::getInstance()->rollBack();
            self::$_is_transaction = false;
        }
    }
    private static function __newResponse($status)
    {
        self::$_response_array = [];
        self::$_response_array["controller"] = self::$_controller_name ? self::$_controller_name : "invalid";
        self::$_response_array["action"] = self::$_action_name ? self::$_action_name : "invalid";
        self::$_response_array["status"] = $status;
        self::$_response_array["date"] = date("c");
    }
    private static function __addResponse($key, $data)
    {
        if(!in_array($key, ["controller", "action", "status", "date"])) {
            if(in_array($key, ["success", "warning", "errors"])) {
                if(is_array($data)) {
                    $messages = $data;
                    $data = [];
                    foreach ($messages as $value) {
                        $data[] = self::__filterResponse($value);
                    }
                } else {
                    self::$_response_array[$key] = self::__filterResponse($data);
                    return NULL;
                }
            }
            self::$_response_array[$key] = $data;
        }
    }
    private static function __filterResponse($messages)
    {
        $messages = preg_replace("/\\[hyperlink_1\\](.*)\\[hyperlink_2\\](.*)\\[hyperlink_3\\]/i", "\$2", $messages);
        return $messages;
    }
    private static function __outputResponse()
    {
        if(self::$_is_transaction === true) {
            self::rollBack();
        }
        Database_Model::getInstance()->update("HostFact_Settings", ["Value" => ""])->where("Variable", "API_IS_RUNNING")->execute();
        if(API_LOG_TYPE != "none" && self::$_api_call_log_id && 0 < self::$_api_call_log_id) {
            if(API_LOG_TYPE == "error" && !in_array(self::$_response_array["status"], ["", "error"])) {
                Database_Model::getInstance()->delete("HostFact_API_Calls")->where("id", self::$_api_call_log_id)->execute();
            } else {
                $response = HostFact_API::filterBase64FromLog(self::$_response_array);
                Database_Model::getInstance()->update("HostFact_API_Calls", ["ResponseType" => self::$_response_array["status"], "Response" => json_encode($response)])->where("id", self::$_api_call_log_id)->execute();
            }
        }
        self::getResponseType();
        switch (self::getResponseType()) {
            case "JSON":
                header("Content-Type:application/json");
                echo json_encode(self::$_response_array);
                exit;
                break;
            case "XML":
                header("Content-Type:text/xml");
                $xml = new SimpleXMLElement("<response/>");
                self::array_to_xml(self::$_response_array, $xml);
                echo $xml->asXML();
                exit;
                break;
            case "RAW":
                if(!empty(self::$_backup_vars)) {
                    $backup_vars = end(self::$_backup_vars);
                    array_pop(self::$_backup_vars);
                    self::$_request_data = $backup_vars["request_data"];
                    self::$_request_method = $backup_vars["request_method"];
                    self::$_response_type = $backup_vars["response_type"];
                    self::$_controller_name = $backup_vars["controller_name"];
                    self::$_action_name = $backup_vars["action_name"];
                    self::$_object_name_singular = $backup_vars["object_name_singular"];
                    self::$_is_transaction = $backup_vars["is_transaction"];
                    self::$_api_call_log_id = $backup_vars["api_call_log_id"];
                }
                throw new InternalAPIException("output");
                break;
        }
    }
    private static function array_to_xml($student_info, &$xml_student_info)
    {
        foreach ($student_info as $key => $value) {
            if(is_array($value)) {
                if(!is_numeric($key)) {
                    $subnode = $xml_student_info->addChild((string) $key);
                    self::array_to_xml($value, $subnode);
                } else {
                    $subnode = $xml_student_info->addChild(self::$_object_name_singular);
                    self::array_to_xml($value, $subnode);
                }
            } else {
                $xml_student_info->addChild((string) $key, htmlspecialchars($value));
            }
        }
    }
    public static function filterBase64FromLog($request)
    {
        foreach ($request as $key => $value) {
            if(is_array($value) || is_object($value)) {
                if(is_object($request)) {
                    $request->{$key} = self::filterBase64FromLog($value);
                } else {
                    $request[$key] = self::filterBase64FromLog($value);
                }
            } elseif($key === "Base64" && 25 < strlen($value)) {
                $request[$key] = substr($value, 0, 10) . "..." . substr($value, -10);
            } elseif(500 < strlen($value) && base64_encode(base64_decode($value))) {
                $request[$key] = substr($value, 0, 10) . "..." . substr($value, -10);
            }
        }
        return $request;
    }
}

?>