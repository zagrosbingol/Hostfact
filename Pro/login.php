<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
if(file_exists("wfh_reset.php")) {
    header("Location: wfh_reset.php");
    exit;
}
define("LOGINSYSTEM", true);
if(isset($_GET["action"]) && $_GET["action"] == "logout") {
    include_once "connect.php";
    $suffix = defined("DB_NAME") ? substr(md5(DB_NAME), 0, 8) : "";
    session_name("hfb" . $suffix);
    $current_session_params = session_get_cookie_params();
    $http_only = true;
    !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off" or $secure_flag = !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off" || $_SERVER["SERVER_PORT"] == 443;
    session_set_cookie_params($current_session_params["lifetime"], $current_session_params["path"], $current_session_params["domain"], $secure_flag, $http_only);
    session_start();
    $_SESSION["UserPro"] = "";
    $_SESSION["UserNamePro"] = "";
    $_SESSION["PasswordPro"] = "";
    $_SESSION["LastDate"] = "";
    session_unset();
    unset($_SESSION["UserPro"]);
    $cookieExpirationHours = defined("COOKIE_EXPIRATION_TIME") ? (int) COOKIE_EXPIRATION_TIME : 1;
    $cookieExpirationTime = time() + 3600 * $cookieExpirationHours;
    setcookie("WFU", "geen", $cookieExpirationTime);
    setcookie("WFUN", "", $cookieExpirationTime);
    setcookie("WFPWD", "", $cookieExpirationTime);
    setcookie("CookieExpiration", "", $cookieExpirationTime);
    if(ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), "", time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    }
    session_destroy();
    if(isset($_GET["update"]) && $_GET["update"] == "yes") {
        header("Location: update/index.php");
        exit;
    }
    header("Location: login.php");
    exit;
}
require_once "config.php";
require_once "class/employee.php";
$redirectAfterLoginTo = NULL;
if(isset($_GET["action"]) && $_GET["action"] == "entercode" && isset($_POST["authCode"])) {
    require_once "class/authentication.php";
    $authentication = new Authentication_Model();
    $auth_result = $authentication->authenticateUser($_SESSION["UserNamePro"], esc($_POST["authCode"]));
    if(!$auth_result) {
        createMessageLog("error", "log authentication code invalid", [$_SESSION["UserNamePro"], $_SERVER["REMOTE_ADDR"]], "login");
        $_SESSION["errormsg"] = __("authentication code invalid");
    } else {
        session_regenerate_id(true);
        $account = new employee();
        $account->show($_SESSION["UserPro"] ?? 0);
        $_SESSION["LastDate"] = $account->LastDate;
        $_SESSION["LoggedIn"] = true;
        $redirectAfterLoginTo = $_SESSION["RefererURL"] ?? NULL;
        $account = new employee();
        $account->updateLastDate($_SESSION["UserPro"], date("YmdHis"));
    }
}
if(isset($_POST["UserName"])) {
    $redirectAfterLoginTo = $_POST["refurl"] ?? NULL;
    $account = new employee();
    $account->UserName = esc($_POST["UserName"]);
    $account->Password = esc($_POST["Password"]);
    $account->search();
    if(0 < $account->Identifier) {
        $_SESSION["UserPro"] = $account->Identifier;
        $_SESSION["UserNamePro"] = $account->UserName;
        $_SESSION["PasswordPro"] = $account->Password;
        $_SESSION["language"] = $account->Language;
        if($account->TwoFactorAuthentication == "on") {
            header("Location: login.php?action=entercode");
            exit;
        }
        session_regenerate_id(true);
        $_SESSION["LastDate"] = $account->LastDate;
        $_SESSION["LoggedIn"] = true;
        if(defined("LOGIN_COOKIE") && LOGIN_COOKIE == "yes") {
            $cookieExpirationHours = defined("COOKIE_EXPIRATION_TIME") ? (int) COOKIE_EXPIRATION_TIME : 1;
            $cookieExpirationTime = time() + 3600 * $cookieExpirationHours;
            setcookie("WFU", md5($account->Identifier), $cookieExpirationTime);
            setcookie("WFUN", passcrypt(esc($_POST["UserName"])), $cookieExpirationTime);
            setcookie("WFPWD", wf_password_hash($account->Password . $_SERVER["REMOTE_ADDR"] . "-" . $cookieExpirationTime), $cookieExpirationTime);
            setcookie("CookieExpiration", $cookieExpirationTime, $cookieExpirationTime);
        }
        $account->updateLastDate($account->Identifier, date("YmdHis"));
        clearFailedLoginAttempts($_SERVER["REMOTE_ADDR"]);
    } else {
        createMessageLog("error", "log username or password invalid", [esc($_POST["UserName"]), $_SERVER["REMOTE_ADDR"]], "login");
        logFailedLoginAttempt("backoffice", $_SERVER["REMOTE_ADDR"], $account->UserName);
        $_SESSION["errormsg"] = __("username or password invalid");
    }
}
define("INDEXPAGE", true);
if(isset($_SESSION["UserPro"])) {
    $account = isset($account) ? $account : new employee();
    $account->Identifier = $_SESSION["UserPro"];
    $account->show();
}
if(isset($account) && isset($_SESSION["UserNamePro"]) && isset($_SESSION["PasswordPro"]) && $account->UserName == $_SESSION["UserNamePro"] && $account->Password == $_SESSION["PasswordPro"] && isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"] === true) {
    if($redirectAfterLoginTo && strpos($redirectAfterLoginTo, "login.php") === false) {
        unset($_SESSION["RefererURL"]);
        header("Location: " . $redirectAfterLoginTo);
        exit;
    }
    header("Location: index.php");
    exit;
}
try {
    $today = new DateTime();
} catch (Exception $e) {
    if(strpos($e->getMessage(), "date.timezone")) {
        fatal_error("Missing PHP configuration: date.timezone", "Your webserver is missing a timezone in the php.ini configuration. Please add in the php.ini the proper timezone and restart your webserver. More information can be found on <a href=\"http://php.net/manual/en/datetime.configuration.php\" target=\"_blank\">http://php.net/manual/en/datetime.configuration.php</a>");
    }
}
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html>\n<head>\n    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n    <meta http-equiv=\"X-UA-Compatible\" content=\"IE=Edge\"/>\n    <title>";
echo __("software name");
echo "</title>\n    <link rel=\"apple-touch-icon\" sizes=\"180x180\" href=\"images/favicons/apple-touch-icon.png\">\n    <link rel=\"icon\" type=\"image/png\" sizes=\"32x32\" href=\"images/favicons/favicon-32x32.png\">\n    <link rel=\"icon\" type=\"image/png\" sizes=\"16x16\" href=\"images/favicons/favicon-16x16.png\">\n    <link rel=\"manifest\" href=\"images/favicons/manifest.json\">\n    <link rel=\"mask-icon\" href=\"images/favicons/safari-pinned-tab.svg\" color=\"#184b64\">\n    <link rel=\"shortcut icon\" href=\"images/favicons/favicon.ico\">\n    <meta name=\"msapplication-config\" content=\"images/favicons/browserconfig.xml\">\n    <meta name=\"theme-color\" content=\"#ffffff\">\n    <link href=\"js/jquery-ui-1.12.1.custom/jquery-ui.css?v=";
echo JSFILE_NOCACHE;
echo "\" rel=\"stylesheet\">\n    <link type=\"text/css\" href=\"css/global.css?v=";
echo JSFILE_NOCACHE;
echo "\" rel=\"stylesheet\" />\n\t<script type=\"text/javascript\" src=\"js/jquery-3.4.0.min.js\"></script>\n\t<script type=\"text/javascript\" src=\"js/jquery-ui-1.12.1.custom/jquery-ui.js\"></script>\n    <style type=\"text/css\">\n    <!--\n    #login_container { position:absolute;left:50%;margin-left:-195px;top:200px;width:390px; }\n    #login_container.size2 { position:absolute;left:50%;margin-left:-300px;top:200px;width:600px; }\n    #login_box { background-color: white; padding: 20px; -moz-box-shadow: 0px 5px 5px #ccc; -webkit-box-shadow: 0px 5px 5px #ccc; box-shadow: 0px 5px 5px #ccc; line-height:22px; }\n    #login_box img { max-width:350px;}\n    #login_footer { margin-top:20px; text-align:center; color: #999;}\n    -->\n    </style>\n    <script language=\"javascript\" type=\"text/javascript\">\n    \$(function()\n    {\n        \$('input[name=\"UserName\"]').focus();\n\n        \$('#login_btn').click(function(){\n            \$('form[name=\"LoginForm\"]').submit();\n        });\n\n        \$('#entercode_btn').click(function(){\n            \$('form[name=\"entercode\"]').submit();\n        });\n    });\n\n    function enterSubmit(keycode)\n    {\n        if(keycode == 13)\n        {\n            \$('#login_btn').click();\n        }\n    }\n    </script>\n</head>\n\n<body>\n\n<div id=\"login_container\">\n\t<div id=\"login_box\">\n        ";
if(isset($_GET["action"]) && $_GET["action"] == "entercode") {
    echo "                <form name=\"entercode\" method=\"post\" action=\"login.php?action=entercode\">\n                ";
    CSRF_Model::getToken();
    echo "                    <img src=\"images/logo_login.png?v=";
    echo JSFILE_NOCACHE;
    echo "\" alt=\"";
    echo __("software name");
    echo "\"/>\n                    <br />\n                    \n                    ";
    if(isset($_SESSION["errormsg"])) {
        echo "                \t\t\t<div class=\"mark alt3\" style=\"\">\n                \t\t        <strong>";
        echo __("errormessage");
        echo "</strong><br /><ul><li>";
        echo $_SESSION["errormsg"];
        echo "</li></ul>\n                \t\t    </div><br />\n                \t\t\t";
        if(!isset($_GET["header"]) || $_GET["header"] != "true") {
            unset($_SESSION["errormsg"]);
        }
    }
    echo "                    \n                    <strong class=\"title2\">";
    echo __("authentication code");
    echo "</strong>\n                    <span class=\"title2_value\">\n                        <input type=\"text\" name=\"authCode\" class=\"text1 size1\" value=\"\" tabindex=\"1\" autocorrect=\"off\" autocapitalize=\"off\" autocomplete=\"off\" autofocus />\n                    </span>\n                    \n                    <br />\n                    <p class=\"align_right\">\n                \t\t<a class=\"button1 alt1\" id=\"entercode_btn\"><span>";
    echo __("send");
    echo "</span></a>\n                \t</p>\n                </form>\n                ";
} else {
    echo "                \n        \t\t<form name=\"LoginForm\" method=\"post\" action=\"login.php\">\n        \t\t";
    CSRF_Model::getToken();
    echo "\t\n        \t\t<input type=\"hidden\" name=\"refurl\" value=\"";
    echo isset($_SESSION["RefererURL"]) ? htmlspecialchars($_SESSION["RefererURL"]) : "";
    echo "\" />\n        \t\t\n        \t\t<img src=\"images/logo_login.png?v=";
    echo JSFILE_NOCACHE;
    echo "\" alt=\"";
    echo __("software name");
    echo "\"/>\n        \t\t<br />\t\n        \t\t\n        \t\t";
    if(isset($_SESSION["errormsg"])) {
        echo "            \t\t\t<div class=\"mark alt3\" style=\"\">\n            \t\t        <strong>";
        echo __("errormessage");
        echo "</strong><br /><ul><li>";
        echo $_SESSION["errormsg"];
        echo "</li></ul>\n            \t\t    </div><br />\n            \t\t\t";
        if(!isset($_GET["header"]) || $_GET["header"] != "true") {
            unset($_SESSION["errormsg"]);
        }
    }
    echo "        \t\t\t\t\n        \t\t<strong class=\"title2\">";
    echo __("username");
    echo "</strong>\n        \t\t<span class=\"title2_value\"><input type=\"text\" tabindex=\"1\" value=\"";
    if(defined("IS_DEMO") && IS_DEMO) {
        echo "demo";
    }
    echo "\" name=\"UserName\" class=\"text1 size1\" /></span>\n        \t\t\t\n        \t\t<strong class=\"title2\">";
    echo __("password");
    echo "</strong>\n        \t\t<span class=\"title2_value\"><input type=\"password\" tabindex=\"2\" value=\"";
    if(defined("IS_DEMO") && IS_DEMO) {
        echo "demo";
    }
    echo "\" name=\"Password\" class=\"text1 size1\" onkeypress=\"enterSubmit(event.keyCode);\"/></span>\n        \t\t\n        \t\t<strong class=\"title2\">";
    echo __("ip address");
    echo "</strong>\n        \t\t<span class=\"title2_value\">";
    echo $_SERVER["REMOTE_ADDR"];
    echo "</span>\n        \t\t\n        \t\t<p class=\"align_right\">\n        \t\t\t<a class=\"button1 alt1\" id=\"login_btn\"><span>";
    echo __("login");
    echo "</span></a>\n        \t\t</p>\n        \t\t</form>\n                ";
}
echo "\t</div>\n\t\n\t<div id=\"login_footer\">\n\t\t";
echo __("software is a product from");
echo "\t</div>\n</div>\n</body>\n</html>";

?>