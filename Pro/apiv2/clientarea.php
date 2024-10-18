<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
define("API_DIR", "apiv2");
if(empty($_POST)) {
    $_POST = json_decode(file_get_contents("php://input"), true);
}
define("InStAlLHosTFacT", "Ra.33sUdhWlkd22");
define("LOGINSYSTEM", true);
define("SKIP_CSRF_CHECK", true);
define("API_CALL_FROM_CLIENTAREA", true);
chdir("../");
require_once "config.php";
require_once API_DIR . "/hostfactapi.class.php";
HostFact_API::parseRawRequestData();
HostFact_API::setResponseType("JSON");
HostFact_API::setRequestMethod("POST");
$call_signature = HostFact_API::getRequestParameter("call_signature");
$call_post_data = HostFact_API::$_request_data["POST"];
unset($call_post_data["call_signature"]);
$post_data_hash = http_build_query($call_post_data);
if(decrypt_data($call_signature) != $post_data_hash) {
    HostFact_API::parseError("Invalid signature of request", true);
}
ClientArea::validateRequest();
require_once "class/company.php";
$company = new company();
$company->show();
require_once "class/employee.php";
$account = new employee();
$account->Name = $account->Function = $account->EmailAddress = $account->PhoneNumber = $account->MobileNumber = $account->Signature = "";
$multicall = HostFact_API::getRequestParameter("multicall");
if($multicall !== false) {
    ClientArea::processMulticallRequest($multicall);
} else {
    ClientArea::createLog("clientarea");
    $result = ClientArea::routeRequest();
}
class ClientArea
{
    public static $debtor_id;
    public static function validateRequest()
    {
        if(in_array(HostFact_API::getRequestParameter("controller"), ["company", "global"])) {
            return true;
        }
        $api_hash = HostFact_API::getRequestParameter("api_hash");
        $hash_data = unserialize(base64_decode($api_hash));
        if(!$hash_data || !is_array($hash_data) || empty($hash_data)) {
            HostFact_API::parseError("No access", true);
            return false;
        }
        if(isset($hash_data["debtor_password_reset"])) {
            $result = Database_Model::getInstance()->getOne("HostFact_Debtors", "SecurePassword")->where("id", $hash_data["debtor_id"])->where("ActiveLogin", "yes")->where("Password", passcrypt(decrypt_data($hash_data["debtor_password"])))->where("OneTimePasswordValidTill", [">" => ["RAW" => "NOW()"]])->where("Status", ["!=" => 9])->execute();
            if(!$result) {
                HostFact_API::parseError("No access", true);
                return false;
            }
            self::$debtor_id = $hash_data["debtor_id"];
        } elseif(isset($hash_data["debtor_password_forgot"])) {
            HostFact_API::$_request_data[HostFact_API::getRequestMethod()]["Password"] = generatePassword();
            $login_attempts_check = self::checkClientareaLoginAttempts($hash_data["remote_ip"], $hash_data["debtor_username"]);
            if($login_attempts_check !== true) {
                HostFact_API::$_controller_name = "debtor";
                HostFact_API::$_action_name = "updatelogincredentials";
                HostFact_API::setMetaData("blocked_minutes", $login_attempts_check);
                $multicall = HostFact_API::getRequestParameter("multicall");
                if($multicall) {
                    HostFact_API::setResponseType("RAW");
                    try {
                        HostFact_API::parseResponse();
                    } catch (InternalAPIException $e) {
                        if($e->getMessage() == "output") {
                            $response = [];
                            $response["updatelogincredentials"] = HostFact_API::$_response_array;
                            header("Content-Type:application/json");
                            echo json_encode($response);
                            exit;
                        }
                    }
                }
                HostFact_API::parseResponse();
                exit;
            }
            HostFact_API::$_request_data[HostFact_API::getRequestMethod()]["IPAddress"] = $hash_data["remote_ip"];
        } elseif(isset($hash_data["login_from_backoffice"])) {
            $multicall = HostFact_API::getRequestParameter("multicall");
            if($multicall) {
                HostFact_API::$_request_data[HostFact_API::getRequestMethod()]["multicall"]["checklogin"]["params"]["CustomerPanelKey"] = decrypt_data(HostFact_API::$_request_data[HostFact_API::getRequestMethod()]["multicall"]["checklogin"]["params"]["CustomerPanelKey"]);
            } else {
                HostFact_API::$_request_data[HostFact_API::getRequestMethod()]["CustomerPanelKey"] = decrypt_data(HostFact_API::$_request_data[HostFact_API::getRequestMethod()]["CustomerPanelKey"]);
            }
        } elseif(isset($hash_data["public_call"])) {
        } elseif(!isset($hash_data["debtor_id"])) {
            if(!isset($hash_data["remote_ip"]) || !$hash_data["remote_ip"] || !isset($hash_data["debtor_username"]) || !$hash_data["debtor_username"]) {
                HostFact_API::parseError("No access", true);
                return false;
            }
            $login_attempts_check = self::checkClientareaLoginAttempts($hash_data["remote_ip"], $hash_data["debtor_username"]);
            if($login_attempts_check !== true) {
                HostFact_API::$_controller_name = "debtor";
                HostFact_API::$_action_name = "checklogin";
                HostFact_API::setMetaData("blocked_minutes", $login_attempts_check);
                $multicall = HostFact_API::getRequestParameter("multicall");
                if($multicall) {
                    HostFact_API::setResponseType("RAW");
                    try {
                        HostFact_API::parseResponse();
                    } catch (InternalAPIException $e) {
                        if($e->getMessage() == "output") {
                            $response = [];
                            $response["checklogin"] = HostFact_API::$_response_array;
                            header("Content-Type:application/json");
                            echo json_encode($response);
                            exit;
                        }
                    }
                }
                HostFact_API::parseResponse();
                exit;
            }
            $result = Database_Model::getInstance()->getOne("HostFact_Debtors", ["id", "SecurePassword", "Password", "OneTimePasswordValidTill"])->where("ActiveLogin", "yes")->where("Username", $hash_data["debtor_username"])->where("Status", ["!=" => 9])->execute();
            $hash_data["debtor_password"] = decrypt_data($hash_data["debtor_password"]);
            if($result && $result->Password && passcrypt($result->Password) == $hash_data["debtor_password"] && date("Y-m-d H:i:s") < $result->OneTimePasswordValidTill) {
                Database_Model::getInstance()->update("HostFact_Debtors", ["LastDate" => ["RAW" => "NOW()"]])->where("id", $result->id)->execute();
                HostFact_API::$_controller_name = "debtor";
                HostFact_API::$_action_name = "checklogin";
                HostFact_API::setObjectNames("debtors", "debtor");
                $response = [];
                $response["passwordreset"] = true;
                $response["Identifier"] = $result->id;
                $response["Password"] = encrypt_data($result->Password);
                $multicall = HostFact_API::getRequestParameter("multicall");
                if($multicall) {
                    HostFact_API::setResponseType("RAW");
                    try {
                        HostFact_API::parseResponse($response);
                    } catch (InternalAPIException $e) {
                        if($e->getMessage() == "output") {
                            $response = [];
                            $response["checklogin"] = HostFact_API::$_response_array;
                            header("Content-Type:application/json");
                            echo json_encode($response);
                            exit;
                        }
                    }
                }
                clearFailedLoginAttempts(false, $hash_data["debtor_username"], "clientarea");
                return HostFact_API::parseResponse($response, true);
            }
            if($result && wf_password_verify($hash_data["debtor_password"], $result->SecurePassword)) {
                Database_Model::getInstance()->update("HostFact_Debtors", ["LastDate" => ["RAW" => "NOW()"]])->where("id", $result->id)->execute();
                if($result->Password) {
                    Database_Model::getInstance()->update("HostFact_Debtors", ["Password" => "", "OneTimePasswordValidTill" => ["RAW" => "NULL"]])->where("id", $result->id)->execute();
                }
                $multicall = HostFact_API::getRequestParameter("multicall");
                if($multicall) {
                    HostFact_API::$_request_data[HostFact_API::getRequestMethod()]["multicall"]["checklogin"]["params"]["Password"] = decrypt_data(HostFact_API::$_request_data[HostFact_API::getRequestMethod()]["multicall"]["checklogin"]["params"]["Password"]);
                } else {
                    HostFact_API::$_request_data[HostFact_API::getRequestMethod()]["Password"] = decrypt_data(HostFact_API::$_request_data[HostFact_API::getRequestMethod()]["Password"]);
                }
                clearFailedLoginAttempts(false, $hash_data["debtor_username"], "clientarea");
                self::$debtor_id = $result->id;
            } else {
                $remote_ip = isset($hash_data["remote_ip"]) ? $hash_data["remote_ip"] : "";
                $username = isset($hash_data["debtor_username"]) ? $hash_data["debtor_username"] : "";
                if($remote_ip || $username) {
                    logFailedLoginAttempt("clientarea", $remote_ip, $username);
                }
                HostFact_API::parseError("Invalid login", true);
                return false;
            }
        } else {
            $result = Database_Model::getInstance()->getOne("HostFact_Debtors", "SecurePassword")->where("id", $hash_data["debtor_id"])->where("ActiveLogin", "yes")->where("Username", $hash_data["debtor_username"])->where("Status", ["!=" => 9])->execute();
            if(!$result || !isset($result->SecurePassword) || sha1($result->SecurePassword) != $hash_data["debtor_password"] && passcrypt($result->Password) != $hash_data["debtor_password"]) {
                HostFact_API::parseError("No access", true);
                return false;
            }
            self::$debtor_id = $hash_data["debtor_id"];
        }
        return true;
    }
    public static function addDebtorIDToRequest($params_array, $controller, $action, $debtor_id)
    {
        if($controller == "debtor" && in_array($action, ["show", "edit"])) {
            $params_array["Identifier"] = $debtor_id;
        }
        if(in_array($action, ["list"])) {
            $params_array["debtor"] = self::$debtor_id;
        }
        return $params_array;
    }
    public static function processMulticallRequest($multicall)
    {
        $response = [];
        foreach ($multicall as $call_name => $_api_call) {
            HostFact_API::setRequestMethod("internal");
            HostFact_API::setResponseType("RAW");
            HostFact_API::$_request_data["internal"]["controller"] = $_api_call["controller"];
            HostFact_API::$_request_data["internal"]["action"] = $_api_call["action"];
            if(!empty($_api_call["params"])) {
                foreach ($_api_call["params"] as $key => $value) {
                    HostFact_API::$_request_data["internal"][$key] = $value;
                }
            }
            self::createLog("clientarea");
            try {
                self::routeRequest();
            } catch (InternalAPIException $e) {
                if($e->getMessage() == "output") {
                    $response[$call_name] = HostFact_API::$_response_array;
                }
            }
        }
        header("Content-Type:application/json");
        echo json_encode($response);
        exit;
    }
    public static function routeRequest()
    {
        $controller_name = HostFact_API::getRequestParameter("controller");
        $action_name = HostFact_API::getRequestParameter("action");
        $controller_name = strtolower($controller_name);
        $api_hash = HostFact_API::getRequestParameter("api_hash");
        $hash_data = unserialize(base64_decode($api_hash));
        if(isset($hash_data["public_call"])) {
        } else {
            HostFact_API::$_request_data[HostFact_API::getRequestMethod()] = self::addDebtorIDToRequest(HostFact_API::$_request_data[HostFact_API::getRequestMethod()], $controller_name, $action_name, self::$debtor_id);
        }
        if(!$controller_name || preg_match("/[^a-z0-9]+/i", $controller_name) === false) {
            HostFact_API::parseError("Invalid controller", true);
            return false;
        }
        global $additional_product_types;
        $_is_module = isset($additional_product_types[$controller_name]) ? true : false;
        if($_is_module && !@file_exists("3rdparty/modules/products/" . $controller_name . "/controllers/" . $controller_name . "_api_controller.php") || !$_is_module && !@file_exists(API_DIR . "/controllers/" . $controller_name . "_api_controller.php") && !@file_exists(API_DIR . "/controllers/" . $controller_name . "_clientarea_controller.php")) {
            HostFact_API::parseError("Invalid controller", true);
            return false;
        }
        require_once API_DIR . "/controllers/api_controller.php";
        HostFact_API::$_controller_name = $controller_name;
        if($_is_module) {
            require_once "3rdparty/modules/products/" . $controller_name . "/controllers/" . $controller_name . "_api_controller.php";
            $namespace = "modules\\products\\" . HostFact_API::$_controller_name;
            if(class_exists($namespace . "\\" . HostFact_API::$_controller_name . "_api_controller")) {
                $instance_name = $namespace . "\\" . HostFact_API::$_controller_name . "_api_controller";
            } else {
                $instance_name = HostFact_API::$_controller_name . "_api_controller";
            }
        } elseif(@file_exists(API_DIR . "/controllers/" . $controller_name . "_api_controller.php")) {
            require_once API_DIR . "/controllers/" . $controller_name . "_api_controller.php";
            $instance_name = HostFact_API::$_controller_name . "_api_controller";
        } elseif(@file_exists(API_DIR . "/controllers/" . $controller_name . "_clientarea_controller.php")) {
            require_once API_DIR . "/controllers/" . $controller_name . "_clientarea_controller.php";
            $instance_name = HostFact_API::$_controller_name . "_clientarea_controller";
        }
        HostFact_API::$_object = new $instance_name();
        $action_name = strtolower($action_name);
        if(!$action_name || preg_match("/[^a-z0-9]+/i", $action_name) === false) {
            HostFact_API::parseError("Invalid action", true);
            return false;
        }
        if(!method_exists(HostFact_API::$_object, $action_name . "_api_action")) {
            HostFact_API::parseError("Invalid action", true);
            return false;
        }
        HostFact_API::$_action_name = $action_name;
        return HostFact_API::$_object->{HostFact_API::$_action_name . "_api_action"}();
    }
    public static function createLog()
    {
        return NULL;
    }
    public static function checkClientareaLoginAttempts($ip, $username)
    {
        $result = Database_Model::getInstance()->get("HostFact_FailedLoginAttempts", ["id", "DateTime"])->orWhere([["IP", $ip ? $ip : ""], ["UserName", $username ? $username : ""]])->orderBy("DateTime", "DESC")->execute();
        if(!$result || count($result) < 10) {
            return true;
        }
        $minutes_ago = floor((strtotime(date("Y-m-d H:i:s")) - strtotime($result[key($result)]->DateTime)) / 60);
        $failed_logins = count($result);
        $blocked_minutes = ($failed_logins - 5) * $failed_logins / 10;
        if(0 < $failed_logins % 5 || $failed_logins % 5 === 0 && $blocked_minutes < $minutes_ago) {
            return true;
        }
        $remaining_minutes = $blocked_minutes - $minutes_ago;
        return $remaining_minutes;
    }
}

?>